var sequelize = require('../../../config/sequelize');//sequlize connection file
var moment = require('moment');//used to return date in required format
const Sequelize = require('sequelize');//sequelize reference
const _ = require('underscore');//used to get specific feild from an array
const userModel = require('../../schema/users');//users model reference
const productsModel = require('../../schema/products')//productModel reference
const productMediaModel = require('../../schema/product_media');//product_media reference
const productContentModel = require('../../schema/product_content');//model reference
const productsTable = productsModel(sequelize, Sequelize);//productstable reference
const productMediaTable = productMediaModel(sequelize, Sequelize);//productMedia Table reference
const userTable = userModel(sequelize, Sequelize);//table reference
const productContentTable = productContentModel(sequelize, Sequelize);//productContent table reference
const cache = require('../../../config/redis');//used to get access to cache
const mongoose = require('mongoose');
const user = mongoose.model('User');
const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;

//Used to check weather sent brandId is valid or not , 
module.exports.checkBrandId = function (brand_id) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select count(brand_id) as count from brands  where brand_id =" + brand_id;
               sequelize.query(query).then(response => {
                    console.log("response====>", response)
                    let result = JSON.parse(JSON.stringify(response[0]));
                    resolve(result[0].count)
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
/*
Purpose : checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Give access to the user to the application
*/
exports.checkCustomerToken = function (customer_token) {
     return new Promise((resolve, reject) => {
          let string = JSON.stringify(customer_token);
          let count = 0;
          user.countDocuments({ password_token: customer_token }, function (err, response) {
               if (err) {
                    console.log(err);
                    reject(err);
               } else if (response > 0) {
                    resolve(response)
               } else {
                    resolve(count)
               }
          })
     });
}

//this function is to checkuserpermission based on featureCode , userId
function checkPermissionByFeatureCode(featureCode, userId = null) {
     return new Promise((resolve, reject) => {
          try {
               if (userId == 1) {//userId ==1 is for superAmin
                    resolve(true);
               } else {
                    let query = "select count(features.name) from role_access as role join features ON role.feature_id =  features.feature_id join user_roles ON role.role_id = user_roles.role_id where user_roles.user_id =" + userId + " && features.feature_code = '" + featureCode + "' &&  features.is_active = 1";
                    sequelize.query(query).then(result => {
                         console.log("====>55", result);
                         let count = JSON.parse(JSON.stringify(result[0]));
                         if (count.count > 0) {
                              resolve(true);
                         } else {
                              resolve(false);
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
//used to get beatid based customer token
module.exports.getBeatByUserId = function (customerToken) {
     return new Promise((resolve, reject) => {
          try {
               let beat = [];
               let flag;
               let hubDetails;
               let hubID;
               if (customerToken != '') {
                    let query = " select user_id , legal_entity_id  from users where password_token ='" + customerToken + "' ||  lp_token ='" + customerToken + "'";
                    sequelize.query(query).then(user => {
                         let userDetails = JSON.parse(JSON.stringify(user[0]));
                         console.log("userDetails", userDetails);
                         if (userDetails[0] != '') {
                              let userId = typeof userDetails[0].user_id ? userDetails[0].user_id : 0;
                              let legalEntityId = typeof userDetails[0].legal_entity_id ? userDetails[0].legal_entity_id : 0;
                              if (userId > 0) {
                                   flag = 0;//ff users only
                                   let pjpQuery = " select le_wh_id as hub_id from pjp_pincode_area where rm_id = " + userId;
                                   sequelize.query(pjpQuery).then(hub => {
                                        console.log("hubdetails", hub);
                                        hubDetails = JSON.parse(JSON.stringify(hub[0]));
                                        if (hubDetails == '') {// condition when user is retailer only
                                             flag = 2;
                                             let data = "select hub_id from retailer_flat  where legal_entity_id =" + legalEntityId;
                                             sequelize.query(data).then(hubId => {
                                                  console.log("hubId ==>70", hubId);
                                                  hubDetails = JSON.parse(JSON.stringify(hubId[0]))
                                                  hubID = typeof hubDetails[0].hub_id != 'undefined' ? hubDetails[0].hub_id : "";
                                                  // This Feature is to check, wheather the user has the access to all the Beats. Thats it
                                                  checkPermissionByFeatureCode("ALLBEAT1").then(permission => {
                                                       console.log("====>104", permission);
                                                       if (permission) {
                                                            flag = 1;//flag =1 mean ff with all beat access
                                                       }
                                                       let beatDetailsQuery = " call getBeatDetails(" + userId + ',' + legalEntityId + ',' + hubID + ',' + flag + ',' + 1000 + ',' + 0 + ")";
                                                       sequelize.query(beatDetailsQuery).then(beat_list => {
                                                            console.log("=====>110", beat_list);
                                                            let beatList = JSON.parse(JSON.stringify(beat_list[0]));
                                                            console.log("===>112", beatList);
                                                            let beatArray = _.pluck(beat_list, 'Beat ID');//used to fetch only beatId from an array
                                                            console.log("======>114 beatArray", beatArray);
                                                            resolve(beatArray.join());
                                                       }).catch(err => {
                                                            console.log(err);
                                                            reject(err)
                                                       })

                                                  }).catch(err => {
                                                       console.log(err);
                                                       reject(err);
                                                  })

                                             }).catch(err => {
                                                  console.log(err);
                                                  reject(err);
                                             })
                                        } else {
                                             hubID = typeof hubDetails.hub_id != 'undefined' ? hubDetails.hub_id : "";
                                             console.log("hubid", hubDetails[0])
                                             // This Feature is to check, wheather the user has the access to all the Beats. Thats it
                                             checkPermissionByFeatureCode("ALLBEAT1").then(permission => {
                                                  console.log("====>104", permission);
                                                  if (permission) {
                                                       flag = 1;//flag =1 mean ff with all beat access
                                                  }
                                                  let beatDetailsQuery = " call getBeatDetails(" + userId + ',' + legalEntityId + ',' + hubID + ',' + flag + ")";
                                                  sequelize.query(beatDetailsQuery).then(beat_list => {
                                                       console.log("=====>110", beat_list);
                                                       let beatList = JSON.parse(JSON.stringify(beat_list[0]));
                                                       let beatArray = _.pluck(beatList, 'Beat ID');//used to fetch only beatId from an array
                                                       console.log("======>114 beatArray", beatArray);
                                                       resolve(beatArray.join());
                                                  }).catch(err => {
                                                       console.log(err);
                                                       reject(err)
                                                  })

                                             }).catch(err => {
                                                  console.log(err);
                                                  reject(err);
                                             })

                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              }
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    resolve('');
               }
          } catch (err) {
               console.log(err);
          }
     })

}
//used to get spokeId based on  beatId 
function getSpokesByBeats(beat) {
     return new Promise((resolve, reject) => {
          try {
               let spokes = [];
               if (beat != '') {
                    if (typeof beat != 'object') {
                         beat = beat.split(',')
                    }
                    if (beat != '') {
                         let query = "select group_concat(distinct(spoke_id)) as spokes from pjp_pincode_area where pjp_pincode_area_id IN(" + beat + ") group by spoke_id"
                         sequelize.query(query).then(spoke => {
                              let spokeDetails = JSON.parse(JSON.stringify(spoke[0]));
                              console.log("spokeDetails", spokeDetails);
                              if (spokeDetails != '') {
                                   spoke = spokeDetails[0].hasOwnProperty('spokes') ? spokeDetails[0].spokes : [];
                                   resolve(spoke);
                              }
                         }).catch(err => {
                              console.log(err);
                         })
                    }
               } else {
                    resolve('');
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}
//used to get hubId based on spokeId
function getSpokesByHubs(spokes) {
     return new Promise((resolve, reject) => {
          try {
               let hubs = [];
               if (spokes != '') {
                    if (typeof spokes != 'object') {
                         spokes = spokes.split(',')
                    }
                    if (spokes != '') {
                         let query = "select group_concat(distinct(le_wh_id)) as hubs from spokes  where spoke_id IN(" + spokes + " )  group by le_wh_id"
                         sequelize.query(query).then(hub => {
                              let hubDetails = JSON.parse(JSON.stringify(hub[0]));
                              console.log("spokeDetails", hubDetails);
                              if (hubDetails != '') {
                                   hubs = hubDetails[0].hasOwnProperty('hubs') ? hubDetails[0].hubs : [];
                                   resolve(hubs);
                              }
                         }).catch(err => {
                              console.log(err);
                         })
                    }
               } else {
                    resolve('');
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
//used to get dc based on hubid
function getDcByHub(hubs) {
     return new Promise((resolve, reject) => {
          try {
               let dcs = [];
               if (hubs != '') {
                    if (typeof hubs != 'object') {
                         hubs = hubs.split(',');
                    }

                    if (hubs != '') {
                         let query = " select group_concat(distinct(dc_id)) as dc from dc_hub_mapping where hub_id IN (" + hubs + ")  group by dc_id";
                         sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(dcData => {
                              console.log("===>245", dcData);
                              dcs = dcData[0].hasOwnProperty('dc') ? dcData[0].dc : [];
                              console.log("====>247", dcs);
                              resolve(dcs);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }
               } else {
                    resolve('');
               }
          } catch (err) {
               console.lof(err);
          }
     })
}

//used to get brand and manufacture 
function processData(scopeType, scopeIds, resultData) {//working fine
     return new Promise((resolve, reject) => {
          try {
               if (typeof scopeIds != 'object') {
                    scopeIds = scopeIds.split(',');
               }
               // used to fetch reference type and reference id
               let query = " select ref_type , ref_id  from hub_product_mapping where scope_type = '" + scopeType + "' &&  scope_id IN (" + scopeIds + " ) "
               sequelize.query(query).then(response => {
                    let result = JSON.parse(JSON.stringify(response[0]));
                    if (result != '') {
                         console.log("===>272", result);
                         result.forEach((details) => {
                              let type = details.hasOwnProperty('ref_type') ? details.ref_type : '';
                              let data = details.hasOwnProperty('ref_id') ? details.ref_id : 0;
                              let tempBrands;
                              let tempManf;
                              console.log("====>275 resultdata", resultData, type, data);
                              if (type == 'brands') {
                                   tempBrands = resultData.brands;
                                   console.log("====>282", tempBrands);
                                   tempBrands = data;
                                   console.log('======>284', tempBrands);
                                   resultData.brands.push(tempBrands);
                                   console.log("======>286", resultData);
                                   //resolve(resultData);
                              }
                              //type as manufacturers
                              if (type == 'manufacturers') {
                                   tempManf = resultData.manf;
                                   tempManf = data;
                                   resultData.manf.push(tempManf);
                              }

                         })
                         console.log("=====>297", resultData);
                         resolve(resultData);
                    } else {
                         console.log("=====>300", resultData);
                         resolve(resultData);
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to get 
function getBlockedList(dcs, hubs, spokes, beats) {//working fine
     console.log("===>309", dcs, hubs, spokes, beats)
     return new Promise((resolve, reject) => {
          try {
               let result = [];
               let temp = { 'manf': [], 'brands': [] };

               if (dcs != '' || hubs != '' || spokes != '' || beats != '') {
                    //dcs
                    processData('DC', dcs, temp).then(value_1 => {
                         temp = Object.assign(temp, value_1);
                         console.log("temp ====>314", temp);
                         //hubs
                         processData('HUB', hubs, temp).then(value_2 => {
                              temp = Object.assign(temp, value_2);
                              console.log("temp ====>324", temp);
                              //spokes
                              processData('SPOKE', spokes, temp).then(value_3 => {
                                   temp = Object.assign(temp, value_3);
                                   console.log("temp ====>334", temp);
                                   //beats
                                   processData('BEAT', beats, temp).then(value_4 => {
                                        temp = Object.assign(temp, value_4);
                                        console.log("temp ====>344", temp);
                                        //final result 
                                        result = temp;
                                        resolve(result);
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    console.log("temp ====>357", temp);
                    //final result 
                    result = temp;
                    resolve(result);
               }

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to legalEntityId based on userId
function getLegalEntityId(userId) {
     try {
          let legalEntityId = 0;
          return new Promise((resolve, reject) => {
               if (userId > 0) {
                    let data = "select legal_entity_id from users where user_id =" + userId;
                    sequelize.query(data).then(legalEntityDt => {
                         let legalEntityData = JSON.parse(JSON.stringify(legalEntityDt[0]))
                         if (Object.keys(legalEntityData[0]).length > 0) {
                              legalEntityId = typeof legalEntityData[0] != 'undefined' ? legalEntityData[0].legal_entity_id : 0;
                              resolve(legalEntityId);
                         } else {
                              resolve(0);
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }

          })

     } catch (err) {
          console.log(err)
     }

}

//used to get user required data based on customer
function getDataFromToken(flag, token, field) {
     return new Promise((resolve, reject) => {
          try {
               //for flag value 1
               if (flag == 1) {
                    userTable.findAll({
                         where: { password_token: token },
                         attributes: field
                    }).then(result => {
                         let row = JSON.parse(JSON.stringify(result));
                         console.log("======>572", row);
                         if (row != '') {
                              resolve(row);
                         } else {
                              userTable.findAll({
                                   where: { lp_token: token },
                                   attributes: field
                              }).then(response => {
                                   let row_1 = JSON.parse(JSON.stringify(response));
                                   resolve(row_1);
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }
               //for flag value 2
               if (flag == 2) {
                    userTable.findAll({
                         where: { password_token: token },
                         attributes: field
                    }).then(result => {
                         let row = JSON.parse(JSON.stringify(result));
                         console.log("=====>597", row)
                         if (Object.keys(row).length) {
                              resolve(row);
                         } else {
                              userTable.findAll({
                                   where: { lp_token: token },
                                   attributes: field
                              }).then(response => {
                                   let row_1 = JSON.parse(JSON.stringify(response));
                                   resolve(row_1);
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }
          } catch (err) {
               console.log(err);
          }
     })
}

// purpose :Used to get customer type based on legal_entity_id
function getUserCustomerType(legal_entity_id) {
     try {
          return new Promise((resolve, reject) => {
               if (legal_entity_id != '') {
                    let query = "select legal_entity_type_id from legal_entities from legal_entity_id =" + legal_entity_id
                    sequelize.query(query).then(rows => {
                         if (rows.length > 0) {
                              return resolve(rows[0].legal_entity_type_id)
                         } else {
                              return resolve(0)
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               }
          })

     } catch (err) {
          console.log(err)
     }
}

//used to get ProductSlabs based on productId , customerType , leWhId , userId
function ProductSlabs(temp, productId, customerType, LeWhId, userId) {
     return new Promise((resolve, reject) => {
          try {
               let data;
               let entryExists = 1;//flag used to check  weather keystring already exist in cache.
               let slabDetails = {};
               let se = [];
               let stock;
               let appKeyData = process.env.DATABASE_NAME;
               console.log("======>840 keystring", appKeyData, productId, customerType, LeWhId);
               //keystring for fetching value from cache
               let keyString = appKeyData + '_product_slab_' + productId + '_customer_type_' + customerType + '_le_wh_id_' + LeWhId;
               console.log('keyString', keyString);
               //used to get data from redis cache
               cache.get(keyString, async function (error, rows) {
                    let response = JSON.parse(rows);
                    if (error) {
                         console.log(error);
                         reject(error);
                    } else if (typeof response != 'undefined' && response != null) {
                         slabDetails = response;
                         if (typeof slabDetails[temp] != 'undefined') {
                              data = slabDetails[temp];///slabDetails.temp
                         } else {
                              console.log("=====>838")
                              entryExists = 0;
                         }
                    } else {
                         entryExists = 0;
                    }

                    console.log("entryExist", entryExists);
                    //validation weather value is exist for entered keySTring or not
                    if (!entryExists) {//!
                         console.log("===if condition=====");
                         let query = "CALL getProductSlabsByCust(" + productId + ',' + LeWhId + ',' + userId + ',' + customerType + ")";
                         sequelize.query(query).then(result => {
                              if (result.length > 0) {
                                   data = JSON.parse(JSON.stringify(result[0]));
                                   console.log("====>874 getProductSlabsByCust", data);
                                   slabDetails = { [temp]: data };
                                   console.log("====>858", slabDetails)
                                   if (data != '') {
                                        cache.set(keyString, JSON.stringify(slabDetails), function (error, added) {
                                             if (error) {
                                                  console.log(error.message);
                                             } else {
                                                  console.log("added=====>865", added);
                                             }
                                        })
                                        resolve(data);
                                   }
                              } else {
                                   reject({ 'status': "failed", 'messgae': "No productSlab found for this product" })
                              }
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         if (customerType == 3016) {//customer type 3016 means clearance so in that case we were calling DIT procedure(Which mean we were showing damage product inventory)
                              //Used to get damange productInventory (we were showing lesser price of product)
                              let DitInventoryQuery = "select getDITCPInventoryByPId(" + productId + ",'" + LeWhId + "') as stock";
                              sequelize.query(DitInventoryQuery).then(stocks => {
                                   console.log("====>897 stocks", stocks);
                                   stock = JSON.parse(JSON.stringify(stocks[0]));
                                   console.log("===>stock", stock);
                                   let stockValue = typeof stock[0].stock != 'undefined' ? stock[0].stock : 0;
                                   let sample = [];
                                   sample.push(data);
                                   for (let i = 0; i < sample.length; i++) {
                                        sample[i].stock = stockValue
                                   }
                                   resolve(sample);
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         } else {
                              let DitInventoryQuery = "select GetCPInventoryByProductId(" + productId + ",'" + LeWhId + "') as stock";
                              sequelize.query(DitInventoryQuery).then(stocks => {
                                   stock = JSON.parse(JSON.stringify(stocks[0]));
                                   let stockValue = typeof stock[0].stock != 'undefined' ? stock[0].stock : 0;
                                   let sample = [];
                                   sample.push(data);
                                   for (let i = 0; i < sample.length; i++) {
                                        sample[i].stock = stockValue
                                   }
                                   resolve(sample);
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         }

                    }
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}

//used to get data releated to  brand or manufacturers
module.exports.getBlockedData = function (customerToken) {
     return new Promise((resolve, reject) => {
          try {
               //fetching beatid
               this.getBeatByUserId(customerToken).then(beats => {
                    console.log("======>107", beats);
                    getSpokesByBeats(beats).then(spokes => {
                         console.log("=====>109", spokes);
                         getSpokesByHubs(spokes).then(hubs => {
                              console.log("=====>111", hubs);
                              getDcByHub(hubs).then(dc => {
                                   console.log("====>113", dc);
                                   getBlockedList(dc, hubs, spokes, beats).then(blockList => {
                                        console.log("=====>115 blockList", blockList);
                                        resolve(blockList);
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//Returns only productIds based on search criteria.
module.exports.getProductIdsList = function (object_id, limit, offset, flag, blockedList, customerType, le_wh_id) {
     return new Promise((resolve, reject) => {
          try {
               console.log("blockListdata", blockedList);
               let brands = typeof blockedList.brands != 'undefined' ? blockedList.brands : 0;
               let manf = typeof blockedList.manf != 'undefined' ? blockedList.manf : 0;
               if (customerType != 3015) {// 3015 is for cash-n-carry
                    customerType = 3014//is for all type
               }
               //brands
               console.log("brands , manf", brands, manf)
               console.log(typeof brands)
               if (typeof brands == 'object') {
                    brands = brands.join();//if brand id is array then we were adding comma sperator.
               }
               //manf
               if (typeof manf == 'object') {
                    manf = manf.join();//if manf id is array then we were adding comma sperator.
               }

               console.log("=====>437", brands, manf)
               le_wh_id = le_wh_id.split("'");
               console.log("====> 428 le_Wh_id", le_wh_id);
               let query = "call getCpProductsIdsList_ByCust(" + object_id + ',' + limit + ',' + offset + ',' + flag + ',' + 0 + ',' + 0 + ',' + customerType + ',' + le_wh_id + ")";
               sequelize.query(query).then(response => {
                    let result = JSON.parse(JSON.stringify(response[0]));
                    console.log("<==432==> productId", result);
                    resolve(result);
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to validate category id 
module.exports.checkCategoryId = function (categoryId) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select count(cat.mp_category_id) as count from mp_categories  as cat  where cat.mp_category_id =" + categoryId + " && cat.mp_id = 1"
               sequelize.query(query).then(response => {
                    let result = JSON.parse(JSON.stringify(response[0]));
                    resolve(result[0].count)
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//getCategories function is used to get all the categories along with their parent_category_id and the segment_id
module.exports.getCategories = function (segment_id, le_wh_id) {
     return new Promise((resolve, reject) => {
          try {
               let query = "call  getCPCategories(" + le_wh_id + ',' + segment_id + ',' + 0 + ',' + 0 + ")";
               sequelize.query(query).then(result => {
                    console.log("=====>471 categories", result);
                    let res = JSON.parse(JSON.stringify(result[0]));
                    if (res != '') {
                         resolve(res);
                    } else {
                         resolve('');
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to check weather enter productid is valid or not 
module.exports.checkProductId = function (productId) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select count(p.product_id) as count from products as p where p.product_id =" + productId;
               sequelize.query(query).then(response => {
                    let result = JSON.parse(JSON.stringify(response[0]));
                    resolve(result[0].count)
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to getProductDetails based on productId , customer_token    /////////////(Not yet completed )
module.exports.getProductDetails = function (product_id, offset, offset_limit, sort_id, customer_token = '', api = '', prodData, pincode, segment_id) {
     return new Promise((resolve, reject) => {
          try {
               let productData = {};
               let inventory = 0;
               let productName;
               productData = { 'product_id': parentId };
               let query = "select prod.product_title as product_name, pc.description,prod.primary_image,prod.esu,prod.mrp from products as prod left join product_content as pc ON pc.product_id = prod.product_id where prod.product_id =" + product_id;
               sequelize.query(query).then(prodName => {
                    productName = JSON.parse(JSON.stringify(prodName[0]));
                    console.log("productname=====> 529", productName)
                    productData = Object.assign(productData, {
                         'product_name ': productName[0].product_name,
                         'description': productName[0].description,
                         'mrp': productName[0].mrp,
                         'rating': Review(productId),
                         'review': getReviews(productId),
                         'esu ': productName[0].esu,
                         'related_product': [],
                         'image': productName[0].primary_image,
                         'primary_image': productName[0].primary_image,
                         'images': getMedia(productId)
                    })
                    //child product
                    console.log("====>543", productData);
                    resolve(productData)
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

/*
 * purpose : funtion used to get userId based on customer_token
*/
module.exports.getUserId = function (customer_token) {
     try {
          return new Promise((resolve, reject) => {
               getDataFromToken(2, customer_token, ['user_id', 'firstname', 'lastname', 'legal_entity_id']).then(result => {
                    resolve(result);
               })
          })
     } catch (err) {
          console.log(err);
          return ({ 'status': "failed", 'message': 'Internal Server Error' })
     }
}

//used to update user review and rating based on user_id
module.exports.addReviewRating = function (user_id, reviewData, firstname, lastname) {
     return new Promise((resolve, reject) => {
          try {
               var MongoClient = require('mongodb').MongoClient;
               var host = 'mongodb://' + process.env['MONGO_USER'] + ":" + process.env['MONGO_PASSWORD'] + "@" + process.env['MONGO_HOST'] + ":" + process.env['MONGO_PORT'] + "/" + process.env['MONGO_DATABASE'];
               MongoClient.connect(host, { useNewUrlParser: true, useUnifiedTopology: true }, function (err, db) {
                    if (err) throw err;
                    var dbo = db.db("ebutor");
                    dbo.collection('reviews').findOne({
                         $and: [
                              { entity_id: parseInt(reviewData.reviews.entity_id) },
                              { rating: parseInt(reviewData.reviews.rating) },
                              { user_id: parseInt(user_id) }
                         ]
                    }, { user_id: 1 }).then(response => {
                         if (response == null) {
                              let insertArray = {
                                   'user_id': parseInt(user_id),
                                   'author': firstname + ' ' + lastname,
                                   'review_type': reviewData.reviews.review_type,
                                   'entity_id': parseInt(reviewData.reviews.entity_id),
                                   'segment_id': parseInt(reviewData.reviews.segment_id),
                                   'comment': reviewData.reviews.comment,
                                   'rating': parseInt(reviewData.reviews.rating),
                                   'status': reviewData.reviews.status,
                                   'data_added': moment().format("YYYY-MM-DD HH:mm:ss"),
                                   'updated_at': moment().format("YYYY-MM-DDTHH:mm:ss"),
                                   'created_at': moment().format("YYYY-MM-DDTHH:mm:ss")
                              }
                              dbo.collection('reviews').insertOne(insertArray, function (err, res) {
                                   if (err) {
                                        console.log(err);
                                        db.close();
                                        reject(err);
                                   } else {
                                        reviewData.status = 1;
                                        resolve(reviewData);
                                   }
                              });
                         } else {
                              reviewData.status = 0;
                              resolve(reviewData);
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               });

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//Used to get multiple product images based on productId
module.exports.getMedia = function (productid) {
     return new Promise((resolve, reject) => {
          try {
               productMediaTable.findAll({// querying image based on product_id
                    where: { product_id: productid, media_type: '85003' },
                    attributes: [['url', 'image']]
               }).then(result => {
                    let image_1 = JSON.parse(JSON.stringify(result));
                    productsTable.findAll({
                         where: { product_id: productid },
                         attributes: [['primary_image', 'image']],
                         limit: undefined
                    }).then(response => {
                         let image_2 = JSON.parse(JSON.stringify(response));
                         let final_image = [image_1, image_2];
                         resolve(image_1);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
          }
     })
}

//used to get product description from product content table based on product id .
module.exports.getDescription = function (productId) {
     return new Promise((resolve, reject) => {
          try {
               productContentTable.findOne({
                    where: { product_id: productId },
                    attributes: ['description']
               }).then(result => {
                    let response = JSON.parse(JSON.stringify(result));
                    if (response != '') {
                         resolve(response.description);
                    } else {
                         resolve('');
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to get product pricing based on productId
/* Get products slab including Cache */
module.exports.getPricing = function (productId, le_wh_id, userId, cusType) {
     return new Promise((resolve, reject) => {
          try {
               let temp;
               let LeWhId;
               let customerType;
               console.log("le_Wh_id", le_wh_id);
               LeWhId = temp = le_wh_id.replace(/'/g, '');
               console.log("temp====>1035", temp, LeWhId);
               temp = temp.replace(',', '_');//replacing comma with underscore

               if (userId == 0) {
                    temp = 0;
               }
               getLegalEntityId(userId).then(legalEntityId => {
                    console.log("===>804 legalENtity", legalEntityId);
                    if (cusType == 'NULL') {
                         getUserCustomerType(legalEntityId).then(customerTypes => {
                              customerType = customerTypes;
                              ProductSlabs(temp, productId, customerType, LeWhId, userId).then(prodSlabs => {
                                   resolve(prodSlabs);
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         customerType = cusType;
                         ProductSlabs(temp, productId, customerType, LeWhId, userId).then(prodSlabs => {
                              resolve(prodSlabs);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }
               })

          } catch (err) {
               console.log(err);
               reject(err);

          }
     })
}

//returns the product Data along with variants and parent child relationship
module.exports.getOfflineProducts = function (productId, leWhId, customerType = '') {
     return new Promise((resolve, reject) => {
          try {
               if (customerType == 3016) {
                    //customertype 3016 mean clearance type
                    let query = "CALL  getCpProductsDIT(" + productId + ',' + customerType + ',' + leWhId + ")";
                    sequelize.query(query).then(result => {
                         resolve(result)
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    let query = "CALL  getCpProducts(" + productId + ',' + customerType + ',' + leWhId + ")";
                    sequelize.query(query).then(result => {
                         resolve(result)
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}


//getReview function is used to getProduct review and rating based on productId
module.exports.getReviews = function (productId) {
     return new Promise((resolve, reject) => {
          try {
               let status = 1;
               var MongoClient = require('mongodb').MongoClient;
               var host = 'mongodb://' + process.env['MONGO_USER'] + ":" + process.env['MONGO_PASSWORD'] + "@" + process.env['MONGO_HOST'] + ":" + process.env['MONGO_PORT'] + "/" + process.env['MONGO_DATABASE'];
               MongoClient.connect(host, { useNewUrlParser: true, useUnifiedTopology: true }, function (err, db) {
                    if (err) throw err;
                    var dbo = db.db("ebutor");
                    dbo.collection('reviews').find({
                         $and: [
                              { entity_id: parseInt(productId) },
                              { status: parseInt(status) }
                         ]
                    }, { 'user_id': 1, 'rating': 1, 'author': 1 }).toArray(function (err, result) {
                         if (err) {
                              reject(err);
                         } else {
                              resolve(result);
                         }

                         db.close();
                    });
               });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//getProductSpecification function is used to get product specification based on  product_id
module.exports.getProductSpecifications = function (productId) {
     return new Promise((resolve, reject) => {
          try {
               let query = " select pa.attribute_id,pa.value, a.name from product_attributes as pa  left join attributes as a  ON a.attribute_id = pa.attribute_id where pa.product_id = " + productId + " &&  pa.value !=''"
               sequelize.query(query).then(response => {
                    resolve(response[0]);
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

/*
 * [getProducts description] :getProducts function is used to get all the products for the category_id passed within the mentioned limits.
 */
module.exports.getProducts = function (categoryId, offset, offsetLimit, sortId) {
     return new Promise((resolve, reject) => {
          try {
               let sortColumn;
               let sortBy;
               let query;
               let result = {};
               query = "select Distinct(cp.product_id) from vw_cp_products as cp join products_slab_rates as psr ON cp.product_id = psr.product_id  where cp.product_class_id ='" + categoryId + "' and cp.is_default = 1  and cp.variant_value1 != '' ";
               //peice of code used to defined what type of sorting we have select.
               if (sortId == 650001) {//margin sorting(High to low)
                    sortColumn = "margin";
                    sortBy = "desc";
               } else if (sortId == 650002) {//price sorting(high to low)
                    sortColumn = "price";
                    sortBy = "desc";
               } else if (sortId == 650003) {//price sorting(Low to High)
                    sortColumn = "price";
                    sortBy = "asc";
               } else if (sortId == 650004) {//margin sorting (Low to High)
                    sortColumn = "margin";
                    sortBy = "asc";
               } else if (sortId == 650005) {//product_title sorting(Z to A)
                    sortColumn = "product_title";
                    sortBy = "desc";
               } else if (sortId == 650006) {//product_title sorting(A to Z)
                    sortColumn = "product_title";
                    sortBy = "asc"
               }

               console.log("sortCOlumn , sortBy", sortColumn, sortBy);
               //valiadting sorting field
               if (typeof sortColumn != 'undefined') {
                    query = query.concat(" order by" + sortColumn + " " + sortBy + " limit " + offset + ',' + offsetLimit + "");
                    sequelize.query(query).then(response => {
                         let productData = response[0];
                         result = { 'data': productData };
                         resolve(result);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    query = query.concat(" limit " + offset + ',' + offsetLimit + "");
                    sequelize.query(query).then(response => {
                         let productData = response[0];
                         result = { 'data': productData };
                         resolve(result);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

// returns the product Data along with variants and parent child relationship for key performance indicators like high  margin, fast moving, high ptr etc.
module.exports.getProductsByKPI = function (flag, offset, offsetLimit, segmentId, leWhId, sortId, blockedList, customerType = 0) {
     return new Promise((resolve, reject) => {
          try {
               let brand = typeof blockedList.brand != 'undefined' ? blockedList.brand : 0;
               let manufacture = typeof blockedList.manf != 'undefined' ? blockedList.manf : 0;
               //brand 
               if (typeof brand == 'object') {
                    brand = brand.join();//checking if brand is object then we were sperating brand with commas
               }

               if (typeof manufacture == 'object') {
                    manufacture = manufacture.join();//checking if manufacture is object then we were sperating with commas
               }
               //brand
               if (brand == "") {
                    brand = 0;
               }
               //manufactures
               if (manufacture == "") {
                    manufacture = 0;
               }
               let total = '@total';
               let result = {};
               let query = " call getProductsByKPI_ByCust(" + flag + ',' + offsetLimit + ',' + offset + ',' + segmentId + ',' + leWhId + ',' + sortId + ',' + brand + ',' + manufacture + ',' + customerType + ',' + total + ")";
               sequelize.query(query).then(response => {
                    result = { data: response };
                    countQuery = "select  @total";
                    sequelize.query(countQuery).then(totalRecord => {
                         let totalProduct = totalRecord[0];
                         result = Object.assign(result, { 'TotalProducts': totalProduct[0]['@total'] });
                         resolve(result);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//Used to get top rated products from mongo db 
module.exports.getTopRatedProduct = function (offset, offsetLimit, segmentId) {
     return new Promise((resolve, reject) => {
          try {
               var MongoClient = require('mongodb').MongoClient;
               var host = 'mongodb://' + process.env['MONGO_USER'] + ":" + process.env['MONGO_PASSWORD'] + "@" + process.env['MONGO_HOST'] + ":" + process.env['MONGO_PORT'] + "/" + process.env['MONGO_DATABASE'];
               MongoClient.connect(host, { useNewUrlParser: true, useUnifiedTopology: true }, function (err, db) {
                    if (err) throw err;
                    var dbo = db.db("ebutor");
                    if (segmentId != '') {
                         dbo.collection('reviews').find({
                              segment_id: parseInt(segmentId)
                         }).limit(parseInt(offsetLimit)).skip(parseInt(offset)).sort({ rating: -1 }).toArray(function (err, response) {
                              if (err) {
                                   console.log(err);
                              } else {
                                   resolve(response);
                              }
                         });

                    } else {
                         dbo.collection('reviews').find({
                         }).limit(parseInt(offsetLimit)).skip(parseInt(offset)).sort({ rating: -1 }).toArray(function (err, response) {
                              if (err) {
                                   console.log(err);
                              } else {
                                   resolve(response);
                              }
                         });
                    }

               });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}


//used to get sku details
module.exports.getCpMustskuList = function (flag, offset, offsetLimit, segmentId, leWhId, sortId, blockedList, customerType = 0) {
     return new Promise((resolve, reject) => {
          try {
               let brand = typeof blockedList.brand != 'undefined' ? blockedList.brand : 0;
               let manufacture = typeof blockedList.manf != 'undefined' ? blockedList.manf : 0;
               //brand 
               if (typeof brand == 'object') {
                    brand = brand.join();//checking if brand is object then we were sperating brand with commas
               }

               if (typeof manufacture == 'object') {
                    manufacture = manufacture.join();//checking if manufacture is object then we were sperating with commas
               }
               //brand
               if (brand == "") {
                    brand = 0;
               }
               //manufactures
               if (manufacture == "") {
                    manufacture = 0;
               }
               let total = '@total';
               let result = {};
               let query = " call getProductsByMust_Sku(" + flag + ',' + offsetLimit + ',' + offset + ',' + leWhId + ',' + sortId + ',' + customerType + ',' + total + ")";
               sequelize.query(query).then(response => {
                    result = { data: response };
                    countQuery = "select  @total";
                    sequelize.query(countQuery).then(totalRecord => {
                         let totalProduct = totalRecord[0];
                         result = Object.assign(result, { 'TotalProducts': totalProduct[0]['@total'] });
                         resolve(result);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}


function getStateIdsBasedOnLewhid(le_Wh_id) {
     return new Promise((resolve, reject) => {
          try {
               let leWhId = le_Wh_id.replace(/'/g, '');
               let result = "";
               let query = "SELECT state  FROM `legalentity_warehouses` WHERE le_wh_id IN (" + leWhId + ")";
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(response).length > 0) {
                         response.forEach(element => {
                              result = element.state + ',' + result;
                         })
                         result = "'" + result.slice(0, result.length - 1) + "'";

                         resolve(result)
                    } else {
                         resolve('');
                    }

               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}

//Used to get product profitable point 
module.exports.getProductPoint = async function (product_id, le_Wh_id) {
     return new Promise(async (resolve, reject) => {
          try {
               let state_id = await getStateIdsBasedOnLewhid(le_Wh_id);
               let query = "SELECT points FROM state_product_points WHERE product_id IN (" + product_id + " ) AND state_id IN (" + state_id + ")";
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(response).length > 0) {
                         resolve(response)
                    } else {
                         resolve('');
                    }

               })



          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to check weather warehouse is assigned with category or not.
function checkCategoryDetails(le_Wh_id) {
     return new Promise(async (resolve, reject) => {
          try {
               //based on warehouse details fetching hub details
               let query = "SELECT hub_id FROM dc_hub_mapping WHERE dc_id IN(" + le_Wh_id + ")";
               db.query(query, {}, async function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(response).length > 0) {
                         let sample = [];
                         const promises = response.map(async (resp) => {
                              sample.push(resp.hub_id);
                         })
                         const Resolvepromise = await Promise.all(promises);
                         if (sample.lenght > 1) {
                              //In case when multiple warehouse mapped to one beat
                              let categoryQuery = "SELECT category_id FROM wh_category_map WHERE le_wh_id IN (" + sample + ")";
                              db.query(categoryQuery, {}, async function (err, categoryResponse) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                   } else if (Object.keys(categoryResponse).length > 1) {
                                        resolve(true);
                                   } else {
                                        resolve(false);
                                   }
                              })

                         } else {
                              //single warehouse mapped to one category
                              let categoryQuery = "SELECT category_id FROM wh_category_map WHERE le_wh_id IN (" + sample + ")";
                              db.query(categoryQuery, {}, async function (err, categoryResponse) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                   } else if (Object.keys(categoryResponse).length > 0) {
                                        resolve(true);
                                   } else {
                                        resolve(false);
                                   }
                              })

                         }
                    } else {
                         resolve(false);
                    }

               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}

/**
 * purpose :  getWareHouseBasedOnCategories used to warehouse details based on category id
 * author  :  Deepak tiwari
 * Request :  category id  
 * Response :  returns warehouse details
 */
module.exports.getWareHouseBasedOnCategories = function (categoriesId, le_wh_id, beat_id, FFId) {
     return new Promise((resolve, reject) => {
          try {
               //checking category mapping status
               checkCategoryDetails(le_wh_id).then(result => {
                    //checking weather warehouse is mapped with category or not
                    if (result) {
                         let query;
                         if (beat_id != '') {
                              //Incase of self login and after checkin
                              query = "SELECT wc.`le_wh_id` FROM wh_category_map AS wc JOIN beat_master AS bm  ON wc.`le_wh_id` = bm.`hub_id`  WHERE wc.category_id =" + categoriesId + " AND bm.beat_id =" + beat_id;
                         } else {
                              //Incase of ff login
                              // query = "SELECT wc.`le_wh_id` FROM wh_category_map AS wc JOIN beat_master AS bm  ON wc.`le_wh_id` = bm.`hub_id`  WHERE wc.category_id =" + categoriesId;

                              query = "SELECT wc.`le_wh_id` FROM wh_category_map AS wc JOIN legalentity_warehouses ON legalentity_warehouses.`le_wh_id`=wc.le_wh_id  JOIN users ON users.`legal_entity_id`=legalentity_warehouses.`legal_entity_id` WHERE wc.category_id =" + categoriesId + " AND users.`user_id`= " + FFId;
                         }

                         db.query(query, {}, function (err, response) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                              } else if (Object.keys(response).length > 0) {
                                   //If warehouse mapped with some category then only resulting warehouse Details 
                                   let WarehouseInfo = "SELECT dc_id FROM dc_hub_mapping WHERE hub_id= " + response[0].le_wh_id;

                                   db.query(WarehouseInfo, {}, function (err, le_wh_id) {
                                        if (err) {
                                             console.log(err);
                                             reject(err);
                                        } else if (Object.keys(le_wh_id).length > 0) {
                                             resolve(le_wh_id);
                                        } else {
                                             resolve(false);
                                        }

                                   })
                              } else {
                                   //waehouse not mapped with category
                                   resolve(false);
                              }

                         })
                    } else {
                         //waehouse not mapped with category
                         resolve(false);
                    }
               }).catch(err => {
                    console.log(err);
                    resolve(err);
               })

          } catch (err) {
               console.log(err);
               reject(err)
          }
     })
}



/**
 * purpose :  getOrderLimitBasedONProduct function is used to get minimum order value based on customer type and warehouse.
 * author  :  Deepak tiwari
 * Request :  le_wh_id , customer_type
 * Response : minimum order value
 */
module.exports.getOrderLimitBasedONProduct = function (le_Wh_id, customerType, isSelfOrder) {
     return new Promise((resolve, reject) => {
          try {
               let orderMinValue;
               let mov_ordercount;
               if (typeof customerType != 'undefined' && customerType != '') {
                    let orderLimit = "select minimum_order_value , self_order_mov,mov_ordercount from ecash_creditlimit where customer_type =" + customerType + " And dc_id =" + le_Wh_id;
                    db.query(orderLimit, {}, async function (err, orderLimitValue) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(orderLimitValue).length > 0) {
                              if (isSelfOrder == 0) {
                                   // For Self Orders
                                   orderMinValue = (typeof orderLimitValue[0].self_order_mov !== 'undefined' && orderLimitValue[0].self_order_mov !== '') ? orderLimitValue[0].self_order_mov : 0;
                              } else {
                                   // For Field Force Placed Orders
                                   orderMinValue = (typeof orderLimitValue[0].minimum_order_value !== 'undefined' && orderLimitValue[0].minimum_order_value !== '') ? orderLimitValue[0].minimum_order_value : 0;
                              }
                              mov_ordercount = (typeof orderLimitValue[0].mov_ordercount !== 'undefined' && orderLimitValue[0].mov_ordercount !== '') ? orderLimitValue[0].mov_ordercount : 0;
                              let response = { "minOrderValue": orderMinValue, "movOrdercount": mov_ordercount };
                              resolve(response);
                         } else {
                              let response = { "minOrderValue": 0, "movOrdercount": 0 };
                              resolve(response);
                         }
                    })
               } else {
                    resolve('');
               }
          } catch (err) {
               console.log(err);
               let response = { "minOrderValue": 0, "movOrdercount": 0 };
               reject(response);
          }
     })

}


module.exports.getCategoryName = function (cat_id) {
     return new Promise((resolve, reject) => {
          if (cat_id != '') {
               var data = `SELECT cat_name FROM categories  WHERE category_id = ${cat_id}`;
               db.query(data, {}, function (err, res) {
                    if (err) {
                         reject(err);
                    } else {
                         resolve(res[0].cat_name);
                    }
               });
          }
     });
}

module.exports.getCategoryList =  function(warehouse_id){
     return new Promise((resolve , reject )=>{
          try {
            //let query =  "CALL getCategoryJson(" + warehouse_id + ")";
            let query =  "SELECT categories.`category_id`,categories.`cat_name`,categories.`image_url`,parent_id FROM wh_category_map JOIN categories ON wh_category_map.`category_id`=categories.`category_id` WHERE  wh_category_map.`le_wh_id`=" + warehouse_id;
            db.query(query,{},async function(err , response){
                 if(err){
                      console.log(err);
                      reject(err);
                 } else {
                        for(let i=0;i<response.length;i++){
                         let query_1 = "select categories.`category_id`,categories.`cat_name`,categories.`image_url`,parent_id from categories where parent_id ="+ response[i].parent_id;
                         db.query(query_1,{},async function(err , result){
                              if(err){
                                   console.log(err);
                                   reject(err);
                              } else {
                                   response[i].subcat =  result;
                                   resolve(response);
                              }
                          })
                    }
                 }
             })
          } catch(err){
               console.log(err);
               reject(err);
          }
     })
}