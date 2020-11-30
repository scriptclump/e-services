
const Sequelize = require('sequelize');
const sequelize = require('../../../config/sequelize');
const userPermission = require('../../schema/user_permssion');
const legalEntityWareHouses = require('../../schema/legalentity_warehouses');
const mongoose = require('mongoose');
const user = mongoose.model('User');
const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;


/*
Purpose : checkPermissionByFeatureCode() Used to check user permission based on userid and featurecode
Author :Deepak Tiwari
Request : featureCode, userId
Resposne : Returns Mobile features
*/
function checkPermissionByFeatureCode(featureCode, userId) {
     return new Promise((resolve, reject) => {
          try {
               // userId is for superadmin
               if (userId == 1) {
                    return resolve(true);
               } else {
                    let data = "select count(features.name) as count from role_access JOIN features ON role_access.feature_id = features.feature_id  JOIN user_roles ON role_access.role_id = user_roles.role_id where user_roles.user_id =" + userId + " && features.feature_code ='" + featureCode + "' && features.is_active = 1";
                    sequelize.query(data).then(name => {
                         let count = JSON.parse(JSON.stringify(name[0]));
                         console.log("count", count[0].count);
                         if (count[0].count > 0) {
                              return resolve(true);
                         } else {
                              return resolve(false);
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               }
          } catch (err) {
               console.log(err)
               reject(err);
          }
     })

}
//used to fetch warehouseDetails based on currentUserID , permissionLevelId
function getWarehouseData(currentUserId, permissionLevelId, active = 1) {//changes required
     return new Promise((resolve, reject) => {
          try {
               let response = [];
               let globalFeature;
               let inActiveDCAccess;
               let query = [];
               if (currentUserId > 0 && permissionLevelId > 0) {
                    //checking for global access features
                    globalFeature = checkPermissionByFeatureCode('GLB0001', currentUserId).then(globalFeatures => {
                         if (globalFeatures) {
                              globalFeature = globalFeatures;
                         } else {
                              globalFeature = 0;
                         }
                         //checking for inactiveAccess features
                         inActiveDCAccess = checkPermissionByFeatureCode('GLBWH0001', currentUserId).then(inActiveDCAcc => {
                              if (inActiveDCAcc) {
                                   inActiveDCAccess = inActiveDCAcc;
                              } else {
                                   inActiveDCAccess = 0;
                              }

                              if (active == 0) {
                                   inActiveDCAccess = 1;
                              }
                              let data = "select object_id from user_permssion where user_id =" + currentUserId + " && permission_level_id =" + permissionLevelId + " group by object_id";
                              sequelize.query(data).then(resultData => {
                                   let result = JSON.parse(JSON.stringify(resultData[0]))
                                   if (result != '') {//!=
                                        let Data = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses  where dc_type  > 0";
                                        if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
                                             Data = Data.concat(" && status = 1");//query returns only active records
                                        }

                                        if (!globalFeature) {
                                             let testResult = [];
                                             result.forEach((element) => {
                                                  testResult.push(element.object_id);
                                             })
                                             if (testResult.length == 1 || testResult.indexOf(0) != -1) {
                                                  if (typeof testResult != 'undefined' && (testResult == 0 || testResult.indexOf(0) != -1)) {
                                                       Data = Data.concat(" && dc_type IN (118001, 118002) group by dc_type");
                                                  } else {
                                                       Data = Data.concat(" && bu_id IN (" + testResult + ") group by dc_type");
                                                  }
                                             } else {
                                                  console.log("result", result[0].object_id)
                                                  Data = Data.concat(" && bu_id IN (" + testResult + ") group by dc_type");
                                             }

                                             let update_query = "SET SESSION group_concat_max_len = 100000";
                                             sequelize.query(update_query).then(rows => {
                                                  console.log("updated successfully");
                                             }).catch(err => {
                                                  console.log(err);
                                                  reject(err);
                                             })
                                             //  Data = Data.concat("select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type");
                                             // let data_1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
                                             sequelize.query(Data).then(rows => {
                                                  if (rows.length > 0) {
                                                       if (rows[0].length > 0) {
                                                            rows[0].forEach((element) => {
                                                                 response.push({ [element.dc_type]: element.le_wh_id });
                                                            })
                                                            resolve(response);
                                                       }
                                                  } else {
                                                       resolve(response);
                                                  }
                                             }).catch(err => {
                                                  console.log(err);
                                             })
                                        } else if (globalFeature) {
                                             let Data = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses  where dc_type  > 0";
                                             if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
                                                  Data = Data.concat(" && status = 1  group by dc_type");
                                             }

                                             let update_query = "SET SESSION group_concat_max_len = 100000";
                                             sequelize.query(update_query).then(rows => {
                                                  console.log("updated successfully=>138");
                                             }).catch(err => {
                                                  console.log(err);
                                                  reject(err);
                                             })
                                             // Data = Data.concat("select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type");
                                             // let data_1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
                                             sequelize.query(Data).then(rows => {
                                                  if (rows.length > 0) {
                                                       if (rows[0].length > 0) {
                                                            rows[0].forEach((element) => {
                                                                 response.push({ [element.dc_type]: element.le_wh_id });
                                                            })
                                                            resolve(response);
                                                       }
                                                  } else {
                                                       resolve(response);
                                                  }
                                             }).catch(err => {
                                                  console.log(err);
                                             })
                                        }
                                   } else {
                                        resolve('');
                                   }
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
                    resolve('');
               }
          } catch (err) {
               console.log(err)
               reject(err);
          }
     })
}

//used to userId based on customer token
function getMyUserId(token) {//having doudt
     return new Promise((resolve, reject) => {
          try {
               let userId = 0;
               if (typeof token != 'undefined') {
                    let customerToken = typeof token != 'undefined' ? token : '';
                    if (customerToken != '') {
                         let query = " select user_id from users where lp_token ='" + customerToken + "' or password_token ='" + customerToken + "'";
                         sequelize.query(query).then(userInfo => {
                              let userInformation = JSON.parse(JSON.stringify(userInfo[0]))
                              if (userInformation != '') {
                                   userId = userInformation[0].user_id;
                                   resolve(userId);
                              } else {
                                   userId = 0;
                                   resolve(userId);
                              }
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }
               } else {
                    resolve(userId);
               }
          } catch (err) {
               console.log(err);
               return resolve(0);
          }
     })
}
//user to get Accesslest based legalEntity id
function getMyAccessList(entityId, token = 0) {
     return new Promise((resolve, reject) => {
          try {
               let dcId = 0;
               getMyUserId(token).then(userId => {
                    if (userId > 0) {
                         getWarehouseData(userId, 6).then(warehouseDetails => {
                              // console.log("warehouseDetails 1232", typeof warehouseInfo[1] != 'undefined' && warehouseInfo[1] != '');
                              if (warehouseDetails != '') {
                                   let warehouseInfo = JSON.parse(JSON.stringify(warehouseDetails));
                                   // console.log("warehouseDetails 1232", warehouseInfo);
                                   if (entityId == 118001) {
                                        dcId = (typeof warehouseInfo[0] != 'undefined' && warehouseInfo[0] != '') ? Object.values(warehouseInfo[0]) : 0;
                                   } else {
                                        dcId = (typeof warehouseInfo[1] != 'undefined' && warehouseInfo[1] != '') ? Object.values(warehouseInfo[1]) : 0;
                                        //dcId = Object.values(warehouseInfo[1]);
                                   }
                                   resolve(dcId);
                              } else {
                                   resolve(dcId);
                              }
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
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
//used to get all Dc details
module.exports.getalldc = function (returnAll, token) {
     return new Promise((resolve, reject) => {
          try {
               let result = [];
               let message = '';
               let status = 0;
               let dcAccessList;
               getMyAccessList(118001, token).then(dcAccess => {
                    // console.log("dcAccessList", dcAccess);
                    dcAccessList = dcAccess;
                    //checking access permission
                    if (dcAccessList == 0) {
                         getMyAccessList(118002, token).then(hubAccessList => {
                              if (hubAccessList != 0) {
                                   // console.log("hubAccesslist", hubAccessList);
                                   //let hubAccess = hubAccessList.split(',');
                                   console.log("hubAccess");
                                   let query = "select dc_id from dc_hub_mapping where hub_id  =" + hubAccessList;
                                   sequelize.query(query).then(rows => {
                                        console.log("result===>22");
                                        dcAccessList = rows;
                                        if (dcAccessList != '' && typeof dcAccessList == 'object') {
                                             dcAccessList = dcAccessList.join();
                                        }
                                        //  console.log("dcAccessList===>31", dcAccessList);
                                        // validating dcAccesslist
                                        if (dcAccessList != 0 && returnAll == 0) {
                                             let query_1 = "select le_wh_id as id  , display_name as name  from legalentity_warehouses where dc_type = 118001  &&  status = 1  &&  le_wh_id IN (" + dcAccessList + ")";
                                             sequelize.query(query_1).then(response => {
                                                  // console.log("response ==>36", response);
                                                  if (response) {
                                                       result = response
                                                       status = 1;
                                                  } else {
                                                       result = [];
                                                       status = 0;
                                                  }
                                                  resolve({ "status": status, 'message': 'Successfull', 'data': result });
                                             }).catch(err => {
                                                  console.log(err);
                                             })
                                        } else {
                                             if (returnAll) {
                                                  let query_2 = "select le_wh_id as id  , display_name  as name from legalentity_warehouses  where dc_type = 118001  &&  status = 1 ";
                                                  sequelize.query(query_2).then(response => {
                                                       // console.log("response===>507", response);
                                                       if (response) {
                                                            result = response
                                                            status = 1;
                                                       } else {
                                                            result = [];
                                                            status = 0;
                                                       }
                                                       resolve({ "status": status, 'message': 'Successfull', 'data': result });
                                                  })
                                             } else {
                                                  resolve({ "status": 0, 'message': 'Please assign dc to the user' });
                                             }
                                        }

                                   }).catch(err => {
                                        console.log(err);
                                        let error = { 'status': 'failed', 'message': 'Internal server error' }
                                        reject(error);
                                   })
                              }
                         }).catch(err => {
                              console.log(err);
                              let error = { 'status': 'failed', 'message': 'Internal server error' }
                              reject(error);
                         })
                    }

                    console.log("outside")
                    if (dcAccessList != 0 && returnAll == 0) {
                         let query_1 = "select le_wh_id as id  , display_name  as name from legalentity_warehouses  where dc_type = 118001  &&  status = 1  &&  le_wh_id IN (" + dcAccessList + ")";
                         sequelize.query(query_1).then(response => {
                              // console.log("response ==>36", response[0]);
                              if (response) {
                                   result = response
                                   status = 1;
                              } else {
                                   result = [];
                                   status = 0;
                              }
                              resolve({ "status": status, 'message': 'Successfull', 'data': result });
                         }).catch(err => {
                              console.log(err);
                         })
                    } else {
                         if (returnAll) {
                              let query_2 = "select le_wh_id as id  , display_name as name from legalentity_warehouses  where dc_type = 118001  &&  status = 1 ";
                              sequelize.query(query_2).then(response => {
                                   // console.log("response===>507", response);
                                   if (response) {
                                        result = response
                                        status = 1;
                                   } else {
                                        result = [];
                                        status = 0;
                                   }
                                   resolve({ "status": status, 'message': 'Successfull', 'data': result });
                              })
                         } else {
                              resolve({ "status": 0, 'message': 'Please assign dc to the user' });
                         }
                    }
               }).catch(err => {
                    console.log(err);
                    let error = { 'status': 'failed', 'message': 'Internal server error' }
                    reject(error);
               })
          } catch (err) {
               console.log(err);
               let error = { 'status': 'failed', 'message': 'Internal server error' }
               reject(error);
          }
     })
}
//used to get all hub details based on dcId , returnAll , token
module.exports.getHubsById = function (dcId, returnAll, token) {
     return new Promise((resolve, reject) => {
          try {
               let result = [];
               let message = '';
               let status = 0;
               getMyAccessList(118002, token).then(hubAccessList_1 => {
                    //console.log("hubaccesslist ===> 376", hubAccessList_1, dcId);
                    if (hubAccessList_1 != 0 && returnAll == 0) {
                         if (dcId != '') {
                              let dcID = dcId.split(',');
                              //let hubAccessId = hubAccessList_1[0].split(',')
                              status = 1;
                              let query = "select legalentity_warehouses.le_wh_id as id ,legalentity_warehouses.lp_wh_name as name from dc_hub_mapping left join legalentity_warehouses  ON legalentity_warehouses.le_wh_id =  dc_hub_mapping.hub_id where  legalentity_warehouses.dc_type = 118002 &&  legalentity_warehouses.status = 1  && dc_id IN(" + dcID + ")  &&  legalentity_warehouses.le_wh_id IN(" + hubAccessList_1 + ")  group by legalentity_warehouses.le_wh_id";
                              sequelize.query(query).then(result => {
                                   //  console.log("result==>325,", result);
                                   let response = JSON.parse(JSON.stringify(result[0]))
                                   resolve({ 'status': status, 'message': message, 'data': response });
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         } else {
                              status = 1;
                              // let hubAccessIds = hubAccessList_1[0].split(',')
                              // console.log("hubAccessIds", hubAccessList_1[0])
                              let query = "select legalentity_warehouses.le_wh_id as id ,legalentity_warehouses.lp_wh_name as name  from legalentity_warehouses  where legalentity_warehouses.dc_type = 118002 &&  legalentity_warehouses.status = 1  && le_wh_id IN(" + hubAccessList_1 + ")";
                              sequelize.query(query).then(result => {
                                   //  console.log("result==>325,", result);
                                   let response = JSON.parse(JSON.stringify(result[0]))
                                   resolve({ 'status': status, 'message': message, 'data': response });
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         }
                    } else {
                         status = 1;
                         if (returnAll) {
                              if (dcId != '') {
                                   let dcID = dcId.split(',');
                                   status = 1;
                                   let query = "select legalentity_warehouses.le_wh_id as id ,legalentity_warehouses.lp_wh_name as name from dc_hub_mapping left join legalentity_warehouses  ON legalentity_warehouses.le_wh_id =  dc_hub_mapping.hub_id where  legalentity_warehouses.dc_type = 118002 &&  legalentity_warehouses.status = 1  && dc_id IN(" + dcID + ")  group by legalentity_warehouses.le_wh_id";
                                   sequelize.query(query).then(result => {
                                        //console.log("result==>325,", result);
                                        let response = JSON.parse(JSON.stringify(result[0]))
                                        resolve({ 'status': status, 'message': message, 'data': response });
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              } else {
                                   let query = " select legalentity_warehouses.le_wh_id as id ,legalentity_warehouses.lp_wh_name as name  from legalentity_warehouses  where legalentity_warehouses.dc_type = 118002 &&  legalentity_warehouses.status = 1";
                                   sequelize.query(query).then(result => {
                                        //console.log("result==>325,", result);
                                        let response = JSON.parse(JSON.stringify(result[0]))
                                        resolve({ 'status': status, 'message': message, 'data': response });
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              }

                         } else {
                              resolve({ 'status': status, 'message': "Please assign HUB to the user" });
                              message = "Please assign HUB to the user";
                         }
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               return resolve([]);
          }
     })

}
//used to get all spokes based on hubId and returnBeats
module.exports.getSpokesById = function (hubId, returnBeats) {
     return new Promise((resolve, reject) => {
          try {
               let result = [];
               let hubID;
               if (hubId.length > 0) {
                    hubID = hubId.split(',');
               } else {
                    hubID = hubId
               }
               if (hubId != null) {
                    if (returnBeats == 1) {
                         let query = "select pjp_pincode_area_id as id ,pjp_name as name from pjp_pincode_area  left join spokes ON spokes.spoke_id =  pjp_pincode_area.spoke_id  where spokes.le_wh_id IN(" + hubID + ")";
                         sequelize.query(query).then(response => {
                              //  console.log("response===>482", response);
                              if (response) {
                                   result = JSON.parse(JSON.stringify(response[0]))
                                   // console.log('result===>485', result);
                              } else {
                                   result = [];
                              }
                              resolve(result);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         let query = "select spoke_id as id , spoke_name as name , pincode  from spokes where le_wh_id IN(" + hubID + ")";
                         sequelize.query(query).then(response => {
                              //  console.log("response===>497", response);
                              if (response) {
                                   result = JSON.parse(JSON.stringify(response[0]))
                                   //  console.log('result===>500', result);
                              } else {
                                   result = [];
                              }
                              resolve(result);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }
               } else {
                    resolve(result);
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
//used to get Beat details based on hubId 
module.exports.getBeatsById = function (hubId) {
     return new Promise((resolve, reject) => {
          try {
               let result = [];
               let hubID;
               if (hubId.length > 0) {
                    hubID = hubId.split(',');
               } else {
                    hubID = hubId
               }
               if (hubId != null) {
                    let query = "select pjp_pincode_area_id as id ,pjp_name as name  from  pjp_pincode_area  where spoke_id IN (" + hubID + ")  group by pjp_pincode_area.pjp_pincode_area_id"
                    sequelize.query(query).then(response => {
                         if (response) {
                              result = JSON.parse(JSON.stringify(response[0]))
                              //console.log('result===>532', result);
                         } else {
                              result = [];
                         }
                         resolve(result);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    console.log("else condition")
                    resolve(result);
               }
          } catch (err) {
               console.log(err);
          }
     })
}
//used to get Area details based on spokeId
module.exports.getAreasById = function (spokeId) {
     return new Promise((resolve, reject) => {
          try {
               let result = [];
               if (spokeId != null) {
                    let query = " select pincode_area.area_id as id , cities_pincodes.officename as name from pincode_area left join cities_pincodes ON cities_pincodes.city_id =  pincode_area.area_id where pjp_pincode_area_id IN(" + spokeId + ")  group by pincode_area.area_id";
                    sequelize.query(query).then(response => {
                         //  console.log("response===>529", response);
                         if (response) {
                              result = JSON.parse(JSON.stringify(response[0]))
                              // console.log('result===>532', result);
                         } else {
                              result = [];
                         }
                         resolve(result);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    resolve(result);
                    // resolve({ 'status': 'failed', 'message': "Spoke id is required feild" })
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
/**
 * purpose : Used to validate appid & customer token detailes
 * request : customer_token or sales_token
 * return : return  token_status =  1 or 0 ,
 * author : Deepak Tiwari
 */
exports.validateToken = function (customer_token) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(customer_token);
               let count = 0;
               let data = { 'token_status': 0 };
               user.countDocuments({ password_token: customer_token }, function (err, response) {
                    //console.log("response", response)
                    if (err) {
                         console.log(err);
                         reject(err);
                         res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                    } else if (response > 0) {
                         data = { 'token_status': 1 }
                         resolve(data)
                    } else {
                         resolve(data);
                    }
               })


               // let string = JSON.stringify(customer_token);
               // let data = { 'token_status': 0 };
               // let query = "select user_id from users where password_token = " + string
               // sequelize.query(query).then(rows => {
               //      console.log("rows=======>592", rows)
               //      if (rows[0].length > 0) {
               //           data = { 'token_status': 1 }
               //           resolve(data)
               //      } else {
               //           resolve(data)
               //      }
               // }).catch(err => {
               //      console.log(err);
               //      reject(err);
               // })
          });
     } catch (err) {
          console.log(err)
     }
}

/**
* purpose : Used to save spoke data
* request :spoke details => name , le_wh_id , customer_token , spoke_id , user_id , pincode ,
* return : return successful response,
* author : Deepak Tiwari
*/
exports.saveSpokeData = function (data) {
     return new Promise((resolve, reject) => {
          try {
               let request = data;
               let spokeName = typeof request.name != 'undefined' ? request.name : 0;
               let spokeHubId = typeof request.le_wh_id != 'undefined' ? request.le_wh_id : '';
               let spokePincode = typeof request.pincode != 'undefined' ? request.pincode : '';
               let spokeId = typeof request.id != 'undefined' ? request.id : 0;
               let message = {};
               if (spokeName != '' && spokeHubId > 0) {
                    if (spokeId > 0) {
                         console.log("if condition")
                         let query = "update spokes set spoke_name ='" + spokeName + "' , le_wh_id =" + spokeHubId + " , pincode =" + spokePincode + " where spoke_id =" + spokeId;
                         sequelize.query(query).then(updated => {
                              request = Object.assign(request, { message: "Updated successfully" });
                              resolve(request);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         let query = "select spoke_id from spokes where le_wh_id =" + spokeHubId + " && spoke_name ='" + spokeName + "'";
                         sequelize.query(query).then(check => {
                              let spokeDetails = JSON.parse(JSON.stringify(check[0]))
                              if (spokeDetails == '') {
                                   let query = "insert into spokes ( spoke_name , le_wh_id , pincode) values ('" + spokeName + "' , " + spokeHubId + " ," + spokePincode + ")"
                                   sequelize.query(query).then(inserted => {
                                        request = Object.assign(request, { 'id': inserted[0].insertId, "message": 'Inserted successfully' });
                                        resolve(request);
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              } else {
                                   let query = "update spokes set spoke_name ='" + spokeName + "' , le_wh_id =" + spokeHubId + " , pincode ='" + spokePincode + "' where spoke_id =" + spokeDetails[0].spoke_id;
                                   sequelize.query(query).then(updated => {
                                        request = Object.assign(request, { 'id': spokeDetails.spoke_id, "message": 'Inserted successfully' });
                                        resolve(request);
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
               } else {
                    resolve({ 'message': "Spoke details can't be empty.", "status": 'failed' })
               }
          } catch (err) {
               console.log(err);
               resolve({ 'message': "Internal server error.", 'status': 'failed' })
          }
     })

}
/**
 * purpose : Used to save beat data
 * request : Beat details =>  name , le_wh_id , customer_token , Beat_id , user_id  , days,
 * return : return  successful response,
 * author : Deepak Tiwari
 */
exports.saveBeatData = function (request) {
     return new Promise((resolve, reject) => {
          try {
               let message = 'Unable to save data';
               let pjpName = typeof request.name != 'undefined' ? request.name : '';
               let pjpDay = typeof request.days ? request.days : '';
               let pjpRmId = typeof request.rm_id ? request.rm_id : '';
               let pjpWhId = typeof request.le_wh_id ? request.le_wh_id : 0;
               let pjpSpokeId = typeof request.spoke_id ? request.spoke_id : 0;
               let pjpUserId = typeof request.user_id ? request.user_id : '';
               let pjpId = typeof request.id ? request.id : 0;
               if (pjpName != '' && pjpRmId > 0 && pjpWhId > 0 && pjpDay != '') {
                    if (pjpId > 0) {
                         let query = "update pjp_pincode_area set pjp_name =''" + pjpName + "', days ='" + pjpDay + "' , rm_id =" + pjpRmId + " , le_wh_id = " + pjpWhId + " , spoke_id =" + pjpSpokeId + " , updated_by = " + pjpUserId + " where pjp_pincode_area_id=" + pjpId;
                         sequelize.query(query).then(updated => {
                              resolve({ 'message': 'Updated Successfully', "status": "success" })
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         let query = "insert into pjp_pincode_area (pjp_name , days , rm_id , le_wh_id ,spoke_id , created_by) values ('" + pjpName + "','" + pjpDay + "'," + pjpRmId + "," + pjpWhId + "," + pjpSpokeId + "," + pjpUserId + ")";
                         sequelize.query(query).then(inserted => {
                              resolve({ 'message': 'Inserted Successfully' })
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }
               } else {
                    resolve({ 'message': 'Inserted Successfully', 'status': "failed" })
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}


exports.getMappedBeats = function (Beat_id) {
     return new Promise((resolve, reject) => {
          try {
               let query = "SELECT le_wh_id as id FROM wh_beat_map WHERE beat_id =" + Beat_id;
               let whDetail = [];
               sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(WH_Id => {
                    for (let i = 0; i < WH_Id.length; i++) {
                         whDetail.push(parseInt(WH_Id[i].id));
                    }
                    resolve(whDetail);
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