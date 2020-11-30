const sequelize = require('../../../config/sequelize');//sequelize connection file
const cpmessage = require('../../../config/cpMessage');//response message
const moment = require('moment');//moment is used for date formatting
const Sequelize = require('sequelize');
const zoneModel = require('../../schema/zone');//zone schema reference
var userModel = require('../../schema/users');
const zoneTable = zoneModel(sequelize, Sequelize);//zone table reference
const user = userModel(sequelize, Sequelize);
const mongoose = require('mongoose');
const userMongo = mongoose.model('User');
const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;



/**
 * purpose:  getstate function is used to get state_id , country_id , based on state name 
 */
module.exports.getstate = function (stateName) {
     return new Promise((resolve, reject) => {
          try {
               zoneTable.findOne({
                    where: { name: stateName },
                    attributes: ['zone_id', 'country_id', 'name']
               }).then(result => {
                    let response = JSON.parse(JSON.stringify(result));
                    resolve(response);
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
Purpose :serviceablePincode function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require user pincode.
*/
module.exports.serviceablePincode = function (pincode) {
     return new Promise((resolve, reject) => {
          let data = " select count(le_wh_id) as count from wh_serviceables as whs where whs.pincode =" + pincode;
          sequelize.query(data).then(response => {
               let result = JSON.parse(JSON.stringify(response[0]))
               resolve(result[0].count);
          }).catch(err => {
               console.log(err);
               reject(err);
          })
     })
}

/**
 * purpose : Used to validate appid & customer token detailes
 * request : customer_token or sales_token
 * return : return  token_status =  1 or 0 ,
 * author : Deepak Tiwari
 */
module.exports.validateToken = async function (customer_token) {
     try {
          return new Promise((resolve, reject) => {

               // userMongo.countDocuments({ password_token: customer_token }, function (err, response) {
               //      //console.log("response", response)
               //      if (err) {
               //           console.log(err);
               //           reject(err);
               //           res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
               //      } else if (response > 0) {
               //           data = { 'token_status': 1 }
               //           resolve(data)
               //      } else {
               //           resolve(data);

               //      }
               // })

               let string = JSON.stringify(customer_token);
               let data = { 'token_status': 0 };
               let query = "select user_id from users where password_token = " + string
               sequelize.query(query).then(rows => {
                    console.log("rows=======>592", rows)
                    if (rows[0].length > 0) {
                         data = { 'token_status': 1 }
                         resolve(data)
                    } else {
                         resolve(data)
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          });
     } catch (err) {
          console.log(err)
     }
}

/**
 * Used to get userid Based on customer token
 */
exports.getUserIdByCustomerToken = function (customer_token) {
     try {
          return new Promise((resolve, reject) => {
               if (customer_token != null) {
                    let string = JSON.stringify(customer_token)
                    let query = "select user_id from users where password_token =" + string;
                    sequelize.query(query).then(response => {
                         let result = JSON.parse(JSON.stringify(response[0]))
                         if (result.length > 0) {
                              resolve(result[0].user_id);
                         } else {
                              resolve(null)
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

/**
 * Purpose : Used to get userExisting ecash value
 */
exports.getExistingEcash = function (user_id) {
     try {
          return new Promise((resolve, reject) => {
               if (user_id != '') {
                    let query = "select cashback-applied_cashback as ecash from user_ecash_creditlimit where user_id =" + user_id;
                    sequelize.query(query).then(response => {
                         let usercash_data = JSON.parse(JSON.stringify(response[0]))
                         let ecash_amount = (typeof usercash_data.ecash != 'undefined' && usercash_data.ecash != '') ? usercash_data.ecash : 0;
                         resolve(ecash_amount)
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    resolve(null);
               }
          })
     } catch (err) {
          console.log(err)
     }
}

/**
 * Purpose : Used to update shippingAddress at the time of creating new order
 */
module.exports.updateAddress = function (customerToken, legalEntityId, legalWhId, address1, latitude, longitude, pincode, city, state_id, country, stateName) {
     return new Promise((resolve, reject) => {
          try {
               if (legalEntityId != '') {
                    let query = "update legal_entities set address1='" + address1 + "', longitude =" + longitude + ",latitude =" + latitude + " ,pincode =" + pincode + ",city ='" + city + "',country ='" + country + "',state_id =" + state_id + " where legal_entity_id= " + legalEntityId;
                    sequelize.query(query).then(updated => {
                         let data = "update retailer_flat set address ='" + address1 + "', address1 ='" + address1 + "' ,longitude =" + longitude + " ,latitude =" + latitude + ",pincode =" + pincode + ",city ='" + city + "',country ='" + country + "',state_id =" + state_id + ",state='" + stateName + "'  where legal_entity_id =" + legalEntityId;
                         sequelize.query(data).then(retailer_update => {
                              let query = "select u.firstname as Firstname , le.address1 as Address , le.address2 as Address1 , le.landmark , le.locality , u.mobile_no  as telephone  , le.city as City,le.pincode as pin,z.name as state,coun.name as country,u.email_id,le.legal_entity_id as legal_entity_id,p.pdp,p.pdp_slot  , le.latitude as Latitude , le.longitude as Longitude from legal_entities as le left join users as u ON u.legal_entity_id = le.legal_entity_id left join countries as coun ON coun.country_id = le.country left join zone as z ON z.zone_id = le.state_id left join retailer_flat as r  ON r.legal_entity_id = le.legal_entity_id left join pjp_pincode_area as p ON p.pjp_pincode_area_id = r.beat_id where u.password_token = '" + customerToken + "'";
                              sequelize.query(query).then(rows => {
                                   let response = JSON.parse(JSON.stringify(rows[0]))
                                   if (response != '' && response != null) {
                                        let day = moment();
                                        let date;
                                        if (response[0].pdp == 'Mon') {
                                             date = day.day(8).format('YYYY-MM-DD');//here i am making devilery date as next monday
                                        } else if (response[0].pdp == "Tue") {
                                             date = day.day(9).format('YYYY-MM-DD');
                                        } else if (response[0].pdp == 'Wed') {
                                             date = day.day(10).format('YYYY-MM-DD');
                                        } else if (response[0].pdp == 'Thu') {
                                             date = day.day(11).format('YYYY-MM-DD');
                                        } else if (response[0].pdp == 'Fri') {
                                             date = day.day(12).format('YYYY-MM-DD');
                                        } else if (response[0].pdp == 'Sat') {
                                             date = day.day(13).format('YYYY-MM-DD');
                                        } else if (response[0].pdp == 'Sun') {
                                             date = day.day(14).format('YYYY-MM-DD');
                                        }

                                        response[0].date = date

                                        if (response[0].pdp == '') {
                                             response[0].date = '';
                                        }
                                        resolve(response)
                                   } else {
                                        resolve('')
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
                    console.log("Le_Wh update condition")
                    let query = "update legalentity_warehouses set address1='" + address1 + "', longitude =" + longitude + ",latitude =" + latitude + " ,pincode =" + pincode + ",city ='" + city + "',country ='" + country + "'  where le_wh_id= " + legalWhId;
                    sequelize.query(query).then(legalWarehouseUpdate => {
                         let query = "select lew.contact_name as Firstname, lew.address1 as Address, lew.address2 as Address1, lew.phone_no as telephone, le.landmark, le.locality, lew.city as City, lew.pincode as pin, z.name as state, coun.name as country, lew.email as email_id, lew.le_wh_id as address_id, p.pdp, p.pdp_slot  , le.latitude as Latitude , le.longitude as Longitude from legalentity_warehouses as lew  left join legal_entities as le ON le.legal_entity_id = lew.legal_entity_id left join users as u ON u.legal_entity_id = lew.legal_entity_id left join countries as coun ON   coun.country_id = lew.country  left join zone as z ON z.zone_id = lew.state left join retailer_flat as r ON r.legal_entity_id  =  le.legal_entity_id left join pjp_pincode_area as p ON p.pjp_pincode_area_id = r.beat_id where u.password_token ='" + customerToken + "'"
                         sequelize.query(query).then(rows => {
                              let response = JSON.parse(JSON.stringify(rows[0]))
                              if (response != '' && response != null) {
                                   let day = moment();
                                   let date;
                                   if (response[0].pdp == 'Mon') {
                                        date = day.day(8).format('YYYY-MM-DD');
                                   } else if (response[0].pdp == "Tue") {
                                        date = day.day(9).format('YYYY-MM-DD');
                                   } else if (response[0].pdp == 'Wed') {
                                        date = day.day(10).format('YYYY-MM-DD');
                                   } else if (response[0].pdp == 'Thu') {
                                        date = day.day(11).format('YYYY-MM-DD');
                                   } else if (response[0].pdp == 'Fri') {
                                        date = day.day(12).format('YYYY-MM-DD');
                                   } else if (response[0].pdp == 'Sat') {
                                        date = day.day(13).format('YYYY-MM-DD');
                                   } else if (response[0].pdp == 'Sun') {
                                        date = day.day(14).format('YYYY-MM-DD');
                                   }
                                   response[0].date = date
                                   if (response[0].pdp == '') {
                                        response[0].date = '';
                                   }
                                   resolve(response)
                              } else {
                                   resolve('')
                              }
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
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

/**
 * Purpose : Used to validate customer token
 */
module.exports.checkCustomerToken = function (req, res) {
     return new Promise((resolve, reject) => {
          let count = 0;
          userMongo.countDocuments({ password_token: req }, function (err, response) {
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
     // return new Promise((resolve, reject) => {
     //      var data = "select count(u.user_id) as count from users u where u.password_token=" + "'" + req + "'" + " or u.lp_token=" + "'" + req + "'" + " or u.chat_token=" + "'" + req + "'";
     //      db.query(data, {}, function (err, res) {
     //           if (err) {
     //                reject(err);
     //           } else {
     //                resolve(res[0].count);
     //           }
     //      });
     // });
}
/**
 * Purpose : Used to fetch userId from customer token
 */
module.exports.getUserIdFromToken = function (req, res) {
     return new Promise((resolve, reject) => {
          if (req != '') {
               var data = "select u.user_id,u.firstname,u.lastname,u.legal_entity_id from users u where u.password_token=" + "'" + req + "'" + " or u.lp_token=" + "'" + req + "'" + " or u.chat_token=" + "'" + req + "'";
               db.query(data, {}, function (err, res) {
                    if (err) {
                         reject(err);
                    } else {
                         resolve(res);
                    }
               });
          }

     });
}

/**
 * Purpose : Used to save feedback 
 */
module.exports.saveFeedbackReasons = function (req, res) {
     return new Promise((resolve, reject) => {
          if (req.length > 0) {
               req = req[0];
               var data = "insert into customer_feedback(legal_entity_id, feedback_type, feedback_group_type, comments, picture, audio, created_by, created_at) values(" + req.legal_entity_id + "," + req.feedback_id + "," + req.feedback_groupid + ",'" + req.comments + "','" + req.feedback_pic + "','" + req.feedback_audio + "'," + req.ff_id + ",'" + moment().format("YYYY-MM-DD HH:mm:ss") + "')";
               db.query(data, function (err, res) {
                    if (err) {
                         console.log(err)
                         let Result = { status: 'failed', message: "Please try again", data: [] };
                         resolve(Result);
                    } else {
                         let Result = { status: 'success', message: "Saved Successfully", data: res };
                         resolve(Result);
                    }
               });
          } else {
               console.log("====>293")
               let Result = { status: 'failed', message: "Please try again", data: [] };
               resolve(Result);
          }
     });
}

/**
 * Purpose : Used to save ff comments
 */
module.exports.insertFFComments = function (req, res) {
     return new Promise((resolve, reject) => {
          if (req.length > 0) {
               req = req[0];
               date = new Date();
               var day = date.getDate();
               var month = date.getMonth() + 1;
               var year = date.getFullYear();
               var presentday = year + "-" + month + "-" + day;


               if (req['activity'] == 107000) {
                    let log = "insert into ff_call_logs (ff_id,user_id,legal_entity_id,activity,check_in,check_in_lat,check_in_long,created_at) values(" + req.ff_id + "," + req.user_id + "," + req.legal_entity_id + "," + req.activity + ",'" + moment().format("YYYY-MM-DD HH:mm:ss") + "'," + req.latitude + "," + req.longitude + ",'" + moment().format("YYYY-MM-DD HH:mm:ss") + "')";
                    db.query(log, function (err, res) {
                         if (err) {
                              console.log(err);
                              let Result = { status: 'failed', message: "Please try again", data: [] };
                              resolve(Result);
                         } else {
                              let Result = { status: 'success', message: "Saved Successfully", data: [] };
                              resolve(Result);
                         }
                    });
               } else {
                    let log = "select log_id from ff_call_logs where legal_entity_id=" + req.legal_entity_id + " and ff_id=" + req.ff_id + " order by log_id desc limit 1 ";
                    db.query(log, function (err, res) {
                         if (err) {
                              console.log(err);
                              let Result = { status: 'failed', message: "Please try again", data: [] };
                              resolve(Result);
                         } else {
                              if (res.length > 0) {
                                   let updatedData = "update ff_call_logs set activity= " + req.activity + ",check_out ='" + moment().format("YYYY-MM-DD HH:mm:ss") + "' ,check_out_lat = " + req.latitude + " ,check_out_long =" + req.longitude + " where ff_id = " + req.ff_id + " and user_id =" + req.user_id + " and legal_entity_id =" + req.legal_entity_id + " and log_id =" + res[0].log_id;
                                   db.query(updatedData, function (err, res) {
                                        if (err) {
                                             console.log(err);
                                             let Result = { status: 'failed', message: "Please try again", data: [] };
                                             resolve(Result);
                                        } else {
                                             let Result = { status: 'success', message: "Saved Successfully", data: [] };
                                             if (req.activity == 107001) {
                                                  let removeCart = "Delete from offline_cart_details where cust_id=" + req.user_id;
                                                  db.query(removeCart, {}, function (err, res) {
                                                       if (err) {
                                                            console.log('err4', err);
                                                            let Result = { status: 'failed', message: "Please try again", data: [] };
                                                            resolve(Result);
                                                       } else {
                                                            resolve(Result);
                                                       }
                                                  });
                                             } else {
                                                  console.log('###', req.legal_entity_id, req.cart_data);
                                                  if (req.cart_data) {
                                                       console.log('%%');
                                                       let removeCart = "Delete from offline_cart_details where cust_id=" + req.user_id;
                                                       db.query(removeCart, {}, function (err, res) {
                                                            if (err) {
                                                                 console.log('err4', err);
                                                                 let Result = { status: 'failed', message: "Please try again", data: [] };
                                                                 resolve(Result);
                                                            } else {
                                                                 for (let p in req.cart_data) {
                                                                      let cart_products = req.cart_data[p];
                                                                      let cart_data = "insert into offline_cart_details (product_id,parent_id,product_image,product_title,product_star,color_code,esu,quantity,status,unit_price,total_price,margin,blocked_qty,prmt_det_id,is_slab,slab_esu,product_slab_id,pack_level,pack_type,freebie_product_id,freebee_qty,freebee_mpq,discount_type,discount,cashback_amount,le_wh_id,cust_id,is_child,packs) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                                                                      db.query(cart_data, [cart_products['productId'],
                                                                      cart_products['parentId'],
                                                                      cart_products['productImage'], cart_products['productTitle'], cart_products['packStar'], cart_products['star'], cart_products['esu'], cart_products['quantity'], cart_products['status'], cart_products['unitPrice'], cart_products['totalPrice'], cart_products['margin'], cart_products['blockedQty'], cart_products['prmtDetId'], cart_products['isSlab'], cart_products['slabEsu'], cart_products['productSlabId'], cart_products['packLevel'], cart_products['packType'], cart_products['freebieProductId'], cart_products['freeqty'], cart_products['freebieMpq'], null, cart_products['discount'], cart_products['cashbackAmount'],
                                                                      req.le_wh_id, req.user_id,
                                                                      cart_products['isChild'], JSON.stringify(cart_products['packs'])], function (err, res) {
                                                                           if (err) {
                                                                                let Result = { status: 'failed', message: "Please try again", data: [] };
                                                                                resolve(Result);
                                                                                console.log(err);
                                                                           } else {
                                                                                let Result = { status: 'success', message: "Inserted successfully", data: res };
                                                                                resolve(Result);
                                                                           }
                                                                      })
                                                                 }
                                                            }
                                                       });
                                                  }
                                                  resolve(Result);
                                             }

                                        }
                                   });
                              } else {
                                   let Result = { status: 'failed', message: "Please try again", data: [] };
                                   resolve(Result);
                              }
                         }
                    });
               }
          } else {
               let Result = { status: 'failed', message: "Please try again", data: [] };
               resolve(Result);
          }
     });

}

/**
 * Purpose : Used to fetch lat long based legal_entity_id.
 */
module.exports.getLatLongDetails = function (req, res) {
     return new Promise((resolve, reject) => {
          let data = "select latitude,longitude from retailer_flat where legal_entity_id=?";
          if (req.length > 0) {
               req = req[0];
               if (req.latitude == '' && req.longitude == '') {
                    db.query(data, [req.legal_entity_id], function (err, res) {
                         if (err) {
                              let Result = { latitude: '', longitude: '' };
                              resolve(Result);
                         } else {
                              if (res.length > 0) {
                                   resolve({ latitude: res[0].latitude, longitude: res[0].longitude });
                              } else {
                                   resolve({ latitude: '', longitude: '' });
                              }
                         }
                    })
               } else {
                    resolve({ latitude: req.latitude, longitude: req.longitude });
               }
          }
     });
}

/**
 * Purpose : Used to save brand feedback. 
 */
module.exports.brandFeedbackModel = function (ff_id, retailer_le_id, status, buying_price, selling_price, weekly_sales_value, feedback_pic) {
     return new Promise((resolve, reject) => {
          try {
               let query = "insert into brand_feedback(ff_id , retailer_le_id, status,buying_price , selling_price , weekly_sales_value, feedback_picture , created_by) values(" + ff_id + ',' + retailer_le_id + ',' + status + ',' + buying_price + ',' + selling_price + ',' + weekly_sales_value + ",'" + feedback_pic + "'," + ff_id + ")";
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(response);
                    }
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}


/*
Purpose : Used to return features based on userid
Author :Deepak Tiwari
Request : userid, flag
Resposne : Returns features
*/
module.exports.getFeatures = (userid, ff_user_id) => {
     return new Promise((resolve, reject) => {
          try {
               let mpFeatureQuery;
               if (ff_user_id == 0) {
                    //Before ff  checking to retailer
                    mpFeatureQuery = "call getFeatureByID(" + userid + ",'" + 'M00001' + "')";
               } else {
                    // After ff checkin to retailer
                    mpFeatureQuery = "call getFeatureByID('" + userid + "," + ff_user_id + "','" + 'M00001' + "')";
               }

               db.query(mpFeatureQuery, {}, function (err, mobileFeatures) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(mobileFeatures[0]);
                    }
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}

/**
 * Purpose : Used to BeatId ,Warehouse Details based on Pincode , latitude ,Longitude. 
 */
module.exports.getBeatInfo = function (pincode, latitude , longitude) {
     return new Promise((resolve, reject) => {
            var data = "CALL getBeatByLoc("+ latitude +','+ longitude +',' + pincode +")";
               db.query(data, {}, function (err, res) {
                    if (err) {
                         reject(err);
                    } else {
                         resolve(res[0]);
                    }
               });
         
     });
}