const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;
const con = dbconnection.Conn;
let crypto = require('crypto');
let _ = require('underscore');
const sequelize = require('../../../config/sequelize');
var mongoose = require('mongoose');
//console.log(mongoose.model('reviews'))
const user = mongoose.model('User');
const userTemp = mongoose.model('userTemp');
const moment = require('moment')



//used to validate customertoken 
module.exports.validateCusomerToken = function (customer_token) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(customer_token);
               let data = { 'token_status': 0 };
               let query = "select user_id from users where password_token = " + string
               sequelize.query(query).then(rows => {
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




/*
Purpose : checkPermissionByFeatureCode() Used to check user permission based on userid and featurecode
Author :Deepak Tiwari
Request : featureCode, userId
Resposne : Returns Mobile features
*/
function checkPermissionByFeatureCode(feature_code, user_id) {
     //return new Promise((resolve, reject) => {
     try {
          // userId is for superadmin
          if (user_id == 1) {
               return true;
          } else {
               var query = "select features.name from role_access join features on role_access.feature_id=features.feature_id join user_roles on role_access.role_id=user_roles.role_id where user_roles.user_id =" + user_id + " and features.feature_code ='" + feature_code + "' and features.is_active=1";
               db.query(query, {}, function (err, res) {
                    if (err) {
                         return false;
                    } else {
                         if (res.length > 0) {
                              return true;
                         } else {
                              return false;
                         }
                    }
               });
          }
     } catch (err) {
          console.log(err)
     }
     // })
     //      // userId is for superadmin
     //      if (userId == 1) {
     //           return true;
     //      } else {
     //           let data = "select count(features.name)  from role_access JOIN features ON role_access.feature_id = features.feature_id  JOIN user_roles ON role_access.role_id = user_roles.role_id where user_roles.user_id =" + userId + "&& features.feature_code ='" + featureCode + "'&& features.is_active = 1";
     //           let count;
     //           db.query(data, {}, function (err, name) {
     //                if (err) {
     //                     console.log(err);
     //                } else if (Object.keys(name).length > 0) {
     //                     count = name;
     //                }
     //           })
     //           return (count > 0) ? true : false;
     //      }
     // } catch (err) {
     //      console.log(err)
     // }


}

function ConfirmOtpcheckPermissionByFeatureCode(feature_code, user_id) {
     return new Promise((resolve, reject) => {
          try {
               // userId is for superadmin
               if (user_id == 1) {
                    resolve(True);
               } else {
                    var query = "select features.name from role_access join features on role_access.feature_id=features.feature_id join user_roles on role_access.role_id=user_roles.role_id where user_roles.user_id =" + user_id + " and features.feature_code ='" + feature_code + "' and features.is_active=1";
                    db.query(query, {}, function (err, res) {
                         if (err) {
                              return false;
                         } else {
                              if (res.length > 0) {
                                   resolve(true);
                              } else {
                                   resolve(false);
                              }
                         }
                    });
               }
          } catch (err) {
               console.log(err)
          }
     })
     //      // userId is for superadmin
     //      if (userId == 1) {
     //           return true;
     //      } else {
     //           let data = "select count(features.name)  from role_access JOIN features ON role_access.feature_id = features.feature_id  JOIN user_roles ON role_access.role_id = user_roles.role_id where user_roles.user_id =" + userId + "&& features.feature_code ='" + featureCode + "'&& features.is_active = 1";
     //           let count;
     //           db.query(data, {}, function (err, name) {
     //                if (err) {
     //                     console.log(err);
     //                } else if (Object.keys(name).length > 0) {
     //                     count = name;
     //                }
     //           })
     //           return (count > 0) ? true : false;
     //      }
     // } catch (err) {
     //      console.log(err)
     // }


}

/*
Purpose :used To check pincode is valid or not
author : Deepak Tiwari
Request : Require pincode
Resposne : return the count of pincode match from database.
*/
module.exports.checkPincode = function (pincode) {
     let response = [];
     return new Promise((resolve, reject) => {
          let data = "SELECT COUNT(DISTINCT pincode) AS COUNT FROM cities_pincodes WHERE pincode =" + pincode;
          db.query(data, {}, function (err, row) {
               if (err) {
                    reject(err)
               } else if (Object.keys(row).length > 0) {
                    response.push(row)
                    resolve(response);
               }
          })

     })
}

/*
Purpose : Used to return mobile features based on userid and feature_id
Author :Deepak Tiwari
Request : userid, feature_id
Resposne : Returns Mobile features
*/
function getMobileFeatures(userid, feature_id) {
     let response = [];
     return new Promise((resolve, reject) => {
          let data = "select distinct features.feature_code,features.name,features.is_menu,features.parent_id,features.feature_id  from user_roles left join users ON users.user_id = user_roles.user_id left join role_access ON role_access.role_id = user_roles.role_id left Join features ON features.feature_id = role_access.feature_id where users.is_active = 1 && user_roles.user_id =" + userid + "&& features.parent_id =" + feature_id + " && features.is_active  = 1  ORDER BY features.sort_order ASC"
          db.query(data, {}, function (err, result) {
               if (err) {
                    reject(err)
               } else if (Object.keys(result).length > 0) {
                    response.push(result)
               }
               resolve(response);
          })
     })
}

/*
Purpose : Used to return features based on userid
Author :Deepak Tiwari
Request : userid, flag
Resposne : Returns features
*/
function getFeatures(userid, flag) {
     try {
          let feature_id;
          return new Promise((resolve, reject) => {
               if (flag == 1) {
                    let lpFeatureQuery = "call getFeatureByID(" + userid + ",'" + 'LP0001' + "')";
                    db.query(lpFeatureQuery, {}, function (err, lpFeatures) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else {
                              resolve(lpFeatures[0]);
                         }
                    })

               } else {
                    let lpFeatureQuery = "call getFeatureByID(" + userid + ",'" + 'M00001' + "')";
                    db.query(lpFeatureQuery, {}, function (err, mobileFeatures) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else {
                              //console.log("mobile_features", mobileFeatures);
                              resolve(mobileFeatures);
                         }
                    })
               }
          })
     } catch (err) {
          console.log(err);
     }

}


/*
Purpose : generateOtp function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require user ccustomer_token, phone
Resposne : generate otp.
*/
function generateOtp(userId, phone, buyer_type_id, otpflag) {
     var Curl = require('node-libcurl').Curl;
     return new Promise((resolve, reject) => {
          var curl = new Curl();
          let random_number = Math.floor(100000 + Math.random() * 900000);
          let mobile_number = phone;
          let app_unique_key = "qoVggl61OKE";
          let message = "<#> Your OTP for Ebutor is " + random_number + "\n - " + app_unique_key;
          if (mobile_number.length >= 10 && message != "") {
               let Host = process.env.SMS_HOST;
               let receipientno = mobile_number;
               let senderID = process.env.SMS_SENDERID;
               curl.setOpt(Curl.option.URL, process.env.SMS_URL);
               curl.setOpt('FOLLOWLOCATION', true);
               curl.setOpt(Curl.option.POST, 1);
               curl.setOpt(Curl.option.POSTFIELDS, "user=" + Host + "&senderID=" + senderID + "&receipientno=" + receipientno + "&msgtxt=" + message);
               curl.on('end', function (statusCode, body, headers) {
                    // console.log(headers)
                    this.close();
               });

               curl.on('error', function (err, curlErrorCode) {
                    console.error(err.message);
                    console.error('---');
                    console.error(curlErrorCode);
                    this.close();
               });

               let buffer = curl.perform();
               if (buffer == null) {
               } else {
                    let res = {};
                    let current_datetime = new Date();
                    let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                    if (otpflag == 0) {
                         //checking weather that user details are already exist or not based on user
                         let body = {
                              mobile: mobile_number,
                              otp: random_number,
                              legal_entity_type_id: buyer_type_id,
                              createdOn: formatted_date
                         }
                         userTemp.create(body).then(UserTempRecord => {
                              if (UserTempRecord) {
                                   console.log("record inserted--->")
                              }
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                         let query = "insert into  user_temp(mobile_no , otp , legal_entity_type_id , created_at) values (" + mobile_number + ',' + random_number + ',' + buyer_type_id + ",'" + formatted_date + "')";
                         db.query(query, {}, function (err, rows) {
                              if (err) {
                                   console.log(err)
                              } else {
                                   res = { message: "Please Confirm  OTP", status: 1 }
                                   resolve(res);
                              }
                         })
                    } else if (otpflag == 1) {

                         //checking weather that user details are already exist or not based on user
                         user.findOne({ user_id: userId }, function (err, response) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                                   res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                              } else if (response != null) {
                                   //based on token send we were validating  token  
                                   user.updateOne(
                                        {
                                             $and: [
                                                  { user_id: userId }, { is_active: 1 }
                                             ]
                                        },
                                        {
                                             $set: {
                                                  otp: random_number,
                                                  updatedOn: formatted_date
                                             }
                                        }
                                   ).then((otpUpdated => {
                                        if (otpUpdated) {
                                             console.log("otpUpdated updated succcessfully ---> 373");
                                        }
                                   })).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              } else {
                                   let query = "select mobile_no , password_token , lp_token ,user_id , otp ,lp_otp , is_disabled, is_active , legal_entity_id , created_by , created_at from users where user_id =" + userId;
                                   db.query(query, {}, function (err, query_response) {
                                        if (err) {
                                             console.log(err);
                                        } else if (Object.keys(query_response).length > 0) {
                                             let body = {
                                                  mobile: query_response[0].mobile_no,
                                                  user_id: query_response[0].user_id,
                                                  password_token: query_response[0].password_token,
                                                  lp_token: query_response[0].lp_token,
                                                  otp: query_response[0].otp,
                                                  lp_otp: query_response[0].lp_otp,
                                                  is_active: query_response[0].is_active,
                                                  is_disabled: query_response[0].is_disabled,
                                                  legal_entity_id: query_response[0].legal_entity_id,
                                                  //  createdOn: query_response[0].created_at,
                                                  createdBy: query_response[0].created_by
                                             }
                                             user.create(body).then(inserted => {
                                                  //after inserting the records in validating customer_token or password_token
                                                  if (inserted) {
                                                       user.updateOne(
                                                            {
                                                                 $and: [
                                                                      { user_id: userId }, { is_active: 1 }
                                                                 ]
                                                            },
                                                            {
                                                                 $set: {
                                                                      otp: random_number,
                                                                      updatedOn: formatted_date
                                                                 }
                                                            }
                                                       ).then((otpUpdated => {
                                                            if (otpUpdated) {
                                                                 console.log("otpUpdated updated succcessfully---->416");
                                                            }
                                                       })).catch(err => {
                                                            console.log(err);
                                                            reject(err);
                                                       })
                                                  }

                                             })

                                        }
                                   })

                              }
                         })
                         let query = "update users set otp='" + random_number + "',updated_at ='" + formatted_date + "'where user_id =" + userId + "&& is_active = 1";
                         db.query(query, {}, function (err, rows) {
                              if (err) {
                                   console.log(err)
                              } else {
                                   res = { message: "Please Confirm  OTP", status: 1 }
                                   resolve(res);
                              }
                         })
                    } else {
                         //checking weather that user details are already exist or not based on user
                         userTemp.findOne({ mobile: mobile_number }, function (err, response) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                                   res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                              } else if (response != null) {
                                   //based on token send we were validating  token  
                                   userTemp.updateOne(
                                        {
                                             mobile: mobile_number
                                        },
                                        {
                                             $set: {
                                                  otp: random_number,
                                                  updatedOn: formatted_date
                                             }
                                        }
                                   ).then((otpUpdated => {
                                        if (otpUpdated) {
                                             console.log("Usertemp updated succcessfully");
                                        }
                                   })).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              } else {
                                   let query = "select mobile_no ,  otp ,legal_entity_type_id , status  from user_temp where mobile_no =" + mobile_number;
                                   db.query(query, {}, function (err, query_response) {
                                        if (err) {
                                             console.log(err);
                                        } else if (Object.keys(query_response).length > 0) {
                                             let body = {
                                                  mobile: query_response[0].mobile_no,
                                                  otp: query_response[0].otp,
                                                  legal_entity_type_id: query_response[0].legal_entity_type_id,
                                                  status: query_response[0].status,
                                                  createdOn: formatted_date
                                             }
                                             userTemp.create(body).then(inserted => {
                                                  //after inserting the records in validating customer_token or password_token
                                                  if (inserted) {
                                                       userTemp.updateOne(
                                                            {
                                                                 mobile: mobile_number
                                                            },
                                                            {
                                                                 $set: {
                                                                      otp: random_number,
                                                                      updatedOn: formatted_date
                                                                 }
                                                            }
                                                       ).then((otpUpdated => {
                                                            if (otpUpdated) {
                                                                 console.log("UserTemp updated succcessfully ---- > 495");
                                                            }
                                                       })).catch(err => {
                                                            console.log(err);
                                                            reject(err);
                                                       })
                                                  }

                                             })

                                        }
                                   })

                              }
                         })
                         let query = "update user_temp set otp='" + random_number + "',updated_at ='" + formatted_date + "'where mobile_no ='" + mobile_number + "'";
                         db.query(query, {}, function (err, rows) {
                              if (err) {
                                   console.log(err)
                              } else {
                                   res = { message: "Please Confirm  OTP", status: 1 }
                                   resolve(res);
                              }
                         })
                    }

               }

          }
     })
}



/*
Purpose : checkPermissionByFeatureCode() Used to check user permission based on userid and featurecode
Author :Deepak Tiwari
Request : featureCode, userId
Resposne : Returns Mobile features
*/
module.exports.checkPermissionByFeatureCode_controller = function (featureCode, userId) {
     // try {
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

/*
Purpose : Used to store first screen details
Author :Deepak Tiwari
Request : telephone, buyer_type, sales_token
Resposne : Returns Registration Status 
*/
module.exports.registration = function (telephone, buyer_type, sales_token) {
     try {
          let desc;
          let buyer_type_id;
          let user_chkdet;
          let ff_check;
          let customer_chk;
          let lp_feature = [];
          let mobile_feature = [];
          let result = {};
          let customer_id;
          let is_active;
          let otpflag;
          let srm_check;
          return new Promise((resolve, reject) => {
               let data = "select * from master_lookup as ml where ml.value = 78002";
               db.query(data, {}, function (err, master) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(master).length > 0) {
                         desc = master[0].description;
                         let query = "select * from users where mobile_no =" + telephone + " && is_active = 1"
                         db.query(query, {}, async function (err, userchk) {
                              if (err) {
                                   reject(err);
                              } else if (Object.keys(userchk).length > 0) {
                                   // console.log("user", userchk)
                                   if (buyer_type == '') {
                                        buyer_type_id = 3001;
                                   } else {
                                        buyer_type_id = buyer_type;
                                   }
                                   user_chkdet = userchk[0];
                                   ff_check = await ConfirmOtpcheckPermissionByFeatureCode('EFF001', user_chkdet.user_id);
                                   srm_check = await ConfirmOtpcheckPermissionByFeatureCode('SRM001', user_chkdet.user_id);
                                   customer_chk = await ConfirmOtpcheckPermissionByFeatureCode('MCU001', user_chkdet.user_id);
                                   //used to access LPmobile feature
                                   getFeatures(user_chkdet.user_id, 1).then(lp_features => {
                                        if (lp_features.length > 0) {
                                             lp_feature.push(lp_features)
                                        }
                                        //used to access All mobile feature
                                        getFeatures(user_chkdet.user_id, 2).then(feature => {
                                             if (feature.length > 0) {
                                                  mobile_feature.push(feature)
                                             }
                                             console.log("", ff_check, srm_check, lp_feature.length, mobile_feature.length, customer_chk)
                                             if (ff_check == 1 || srm_check == 1 || lp_feature.length != 0 || (mobile_feature.length != 0 && customer_chk == 0)) {
                                                  console.log("if condition")
                                                  if (sales_token) {
                                                       result = { message: "Already registered as a field-force user.", status: 0 }
                                                       resolve(result);

                                                  } else {
                                                       console.log("hello")
                                                       otpflag = 1;
                                                       let otp = generateOtp(user_chkdet.user_id, telephone, buyer_type_id, otpflag);
                                                       resolve(otp);
                                                  }
                                             } else {
                                                  let users_num_rows;
                                                  let users_temp_num_rows;
                                                  let query = "select user.user_id,leg.legal_entity_id,leg.is_approved,user.is_active from users as user LEFT JOIN legal_entities as leg ON leg.legal_entity_id = user.legal_entity_id where user.mobile_no ='" + telephone + "'&& user.is_active = 1 &&  leg.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc + "&& ml.is_active = 1)";
                                                  db.query(query, {}, function (err, result_users) {
                                                       if (err) {
                                                            reject(err);
                                                       } else {
                                                            users_num_rows = result_users.length;
                                                            if (result_users != '') {
                                                                 customer_id = result_users[0].user_id;
                                                            } else {
                                                                 customer_id = '';
                                                            }

                                                            if (users_num_rows > 0) {
                                                                 is_active = result_users[0].is_active;
                                                            } else {
                                                                 is_active = 0;
                                                            }

                                                            let query_1 = "select * from user_temp where mobile_no =" + telephone;
                                                            db.query(query_1, {}, function (err, result_user_temp) {
                                                                 if (err) {
                                                                      reject(err)
                                                                 } else {
                                                                      users_temp_num_rows = result_user_temp.length;
                                                                      // console.log("user_row", users_num_rows, users_temp_num_rows)
                                                                      console.log("user_row", users_num_rows, users_temp_num_rows)
                                                                      if (users_num_rows == 0 && users_temp_num_rows == 0) {
                                                                           console.log("----619---------");
                                                                           otpflag = 0;
                                                                           let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                                           resolve(otp);
                                                                      } else if (users_num_rows > 0 && is_active == 1 && sales_token == '') {
                                                                           console.log("----624---------");
                                                                           otpflag = 1;
                                                                           let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                                           resolve(otp);
                                                                      } else if ((users_num_rows > 0 && sales_token != "") || ((users_num_rows > 0) && (is_active == 0))) {
                                                                           console.log("----629---------");
                                                                           let result_1 = {};
                                                                           if (is_active == 1) {
                                                                                result_1 = { message: "Already registered with us! Please login.", status: 0 };
                                                                           } else {
                                                                                result1 = { message: "We are sorry your shop is not being serviced at the moment.", status: 0 };
                                                                           }
                                                                           resolve(result_1);

                                                                      } else {
                                                                           console.log("685")
                                                                           otpflag = 2;
                                                                           let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                                           resolve(otp);
                                                                      }
                                                                 }
                                                            })



                                                       }
                                                  })

                                             }
                                        }).catch(err => {
                                             console.log(err)
                                        })
                                   }).catch(err => {
                                        console.log(err)
                                   })

                              } else {
                                   console.log("=====> 7000")
                                   ff_check = 0;
                                   srm_check = 0;
                                   customer_chk = 0;
                                   lp_feature = [];
                                   mobile_feature = [];
                                   if (ff_check == 1 || srm_check == 1 || lp_feature.length != 0 || (mobile_feature.length != 0 && customer_chk == 0)) {
                                        console.log("if condition")
                                        if (sales_token) {
                                             result = { message: "Already registered as a field-force user.", status: 0 }
                                             resolve(result);

                                        } else {
                                             otpflag = 1;
                                             let otp = generateOtp(user_chkdet.user_id, telephone, buyer_type, otpflag);
                                             resolve(otp);
                                        }
                                   } else {
                                        console.log("======>719")
                                        let users_num_rows;
                                        let users_temp_num_rows;
                                        let query = "select user.user_id,leg.legal_entity_id,leg.is_approved,user.is_active from users as user LEFT JOIN legal_entities as leg ON leg.legal_entity_id = user.legal_entity_id where user.mobile_no ='" + telephone + "' && user.is_active = 1 &&  leg.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc + "&& ml.is_active = 1)";
                                        db.query(query, {}, function (err, result_users) {
                                             if (err) {
                                                  reject(err);
                                             } else {
                                                  users_num_rows = result_users.length;
                                                  if (result_users != '') {
                                                       customer_id = result_users[0].user_id;
                                                  } else {
                                                       customer_id = '';
                                                  }

                                                  if (users_num_rows > 0) {
                                                       is_active = result_users[0].is_active;
                                                  } else {
                                                       is_active = 0;
                                                  }

                                                  let query_1 = "select * from user_temp where mobile_no =" + telephone;
                                                  db.query(query_1, {}, function (err, result_user_temp) {
                                                       if (err) {
                                                            reject(err)
                                                       } else {
                                                            users_temp_num_rows = result_user_temp.length;
                                                            // console.log("user_row", users_num_rows, users_temp_num_rows)
                                                            console.log("user_row", users_num_rows, users_temp_num_rows)
                                                            if (users_num_rows == 0 && users_temp_num_rows == 0) {
                                                                 console.log("====>752")
                                                                 otpflag = 0;
                                                                 let otp = generateOtp(customer_id, telephone, buyer_type, otpflag);
                                                                 resolve(otp);
                                                            } else if (users_num_rows > 0 && is_active == 1 && sales_token == '') {
                                                                 otpflag = 1;
                                                                 let otp = generateOtp(customer_id, telephone, buyer_type, otpflag);
                                                                 resolve(otp);
                                                            } else if ((users_num_rows > 0 && sales_token != "") || ((users_num_rows > 0) && (is_active == 0))) {
                                                                 let result_1 = {};
                                                                 if (is_active == 1) {
                                                                      result_1 = { message: "Already registered with us! Please login.", status: 0 };
                                                                 } else {
                                                                      result1 = { message: "We are sorry your shop is not being serviced at the moment.", status: 0 };
                                                                 }
                                                                 resolve(result_1);

                                                            } else {
                                                                 otpflag = 2;
                                                                 let otp = generateOtp(customer_id, telephone, buyer_type, otpflag);
                                                                 resolve(otp);
                                                            }
                                                       }
                                                  })



                                             }
                                        })

                                   }
                              }
                         })

                    }

               })
          })
     } catch (err) {
          console.log(err)
     }


}



module.exports.logApiRequests = function (data) {
     return new Promise((resolve, reject) => {
          var MongoClient = require('mongodb').MongoClient;
          var host = 'mongodb://' + process.env['MONGO_USER'] + ":" + process.env['MONGO_PASSWORD'] + "@" + process.env['MONGO_HOST'] + ":" + process.env['MONGO_PORT'] + "/" + process.env['MONGO_DATABASE'];
          MongoClient.connect(host, { useNewUrlParser: true, useUnifiedTopology: true }, function (err, db) {
               if (err) console.log("error", err);
               var dbo = db.db("ebutor");
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let apiUrl;
               let parameters;
               if (typeof data.apiUrl != 'undefined') {
                    apiUrl = data.apiUrl;
               }
               if (typeof data.parameter != 'undefined') {
                    parameters = data.parameter;
               }
               let body = {
                    apiUrl: apiUrl,
                    parameters: parameters,
                    created_at: formatted_date
               }
               dbo.collection('container_api_logs').insertOne(body, function (err, res) {
                    if (err) throw err;
                    db.close();
               });
          });
     });
}
/*
Purpose : Used to ckeck otp availabity
Author :Deepak Tiwari
Request : otp, telephone
Resposne : Returns userdetails 
*/
function checkOtpUser(otp, telephone) {
     try {
          return new Promise((resolve, reject) => {
               //checking weather that user details are already exist or not based on user
               // user.findOne({ $and: [{ mobile: telephone }, { otp: otp }] }, function (err, response) {
               //      if (err) {
               //           console.log(err);
               //           reject(err);
               //      } else if (response != null) {
               //           resolve(response);
               //      } else {
               //           resolve('');
               //      }
               // })
               let query = "select * from users us where us.otp =" + otp + " and us.mobile_no=" + telephone;
               db.query(query, {}, function (err, response) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(response).length > 0) {
                         result = response
                         resolve(result);
                    } else {
                         resolve('');
                    }
               })
          })
     } catch (err) {
          console.log(err);
     }


}

/*
Purpose : To check OTP avalibilty from user_temp table
Author :Deepak Tiwari
Request : otp, telephone
Resposne : Returns userdetails 
*/
function checkOtpUsertemp(otp, telephone) {
     try {
          console.log("-----866------")
          let result;
          return new Promise((resolve, reject) => {
               userTemp.findOne({ $and: [{ mobile: telephone }, { otp: otp }] }, function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (response != null) {
                         resolve(response);
                    } else {
                         resolve('');
                    }
               })
               // let query = "select * from user_temp ustmp where ustmp.otp =" + otp + " and ustmp.mobile_no=" + telephone;
               // db.query(query, {}, function (err, response) {

               //      if (err) {
               //           reject(err)
               //      } else if (Object.keys(response).length > 0) {
               //           result = response
               //           resolve(result);
               //      } else {
               //           resolve('');
               //      }
               // })

          })
     } catch (err) {
          console.log(err);
     }
}



/*
Purpose : Check wether mobile number is confirmed or not
Author :Deepak Tiwari
Request : phonenumber
Resposne : Returns status of mobile number
*/
function getStatus(phonenumber) {
     try {
          let desc;
          let user_chkdet;
          let ff_check;
          let customer_chk;
          let lp_feature;
          let mobile_feature;
          let srm_check;
          let result = [];
          return new Promise((resolve, reject) => {
               let data = "select description from master_lookup as ml where ml.value = 78002"
               db.query(data, {}, function (err, master) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(master).length > 0) {
                         desc = master[0].description;
                         let query = "select * from users where mobile_no =" + phonenumber + " && is_active = 1"
                         db.query(query, {}, async function (err, userchk) {
                              if (err) {
                                   reject(err);
                              } else if (Object.keys(userchk).length > 0) {
                                   user_chkdet = userchk[0];
                                   ff_check = await ConfirmOtpcheckPermissionByFeatureCode('EFF001', user_chkdet.user_id);//Enabled Field Force
                                   srm_check = await ConfirmOtpcheckPermissionByFeatureCode('SRM001', user_chkdet.user_id);//SRM Enabled
                                   customer_chk = await ConfirmOtpcheckPermissionByFeatureCode('MCU001', user_chkdet.user_id);//Retailers who is registered in ebutor	
                                   getFeatures(user_chkdet.user_id, 1).then(lp_features => {
                                        if (lp_features.length > 0) {
                                             lp_feature = lp_features
                                        }
                                   })

                                   getFeatures(user_chkdet.user_id, 2).then(feature => {
                                        if (feature.length > 0) {
                                             mobile_feature = feature
                                        }
                                   })
                              } else {
                                   ff_check = 0;
                                   srm_check = 0;
                                   customer_chk = 0;
                                   lp_feature = [];
                                   mobile_feature = [];
                              }
                              if (ff_check == 1 || srm_check == 1 || lp_feature != 0 || mobile_feature != 0 && customer_chk == 0) {
                                   console.log("----883----")
                                   let query_1 = "select users.legal_entity_id from users where mobile_no =" + phonenumber + "&& is_active = 1";
                                   db.query(query_1, {}, function (err, response) {
                                        if (err) {
                                             reject(err)
                                        } else if (Object.keys(response).length > 0) {
                                             result.push(response);
                                             resolve(result[0]);
                                        } else {
                                             resolve('');
                                        }
                                   })

                              } else {
                                   console.log("------897-----");
                                   let query = "select user.legal_entity_id from users as user LEFT JOIN legal_entities as leg ON leg.legal_entity_id = user.legal_entity_id where user.mobile_no =" + phonenumber + " && leg.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc + "&& ml.is_active = 1)";
                                   db.query(query, {}, function (err, result_users) {
                                        if (err) {
                                             reject(err);
                                        } else if (Object.keys(result_users).length > 0) {
                                             result.push(result_users);
                                             resolve(result[0]);
                                        } else {
                                             resolve('');
                                        }
                                   })

                              }
                         })
                    } else {
                         resolve('');
                    }
               })

          })

     } catch (err) {
          console.log(err)
          let data = {};
          data = { status: "failed", message: "unable to get status" }
          resolve(data);
     }
}


/*
* Function Name: InsertDeviceDetails
* Description: the function is used to  insert and update device details  
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 25th Jan 2017
* Modified Date & Reason: 
*/
function InsertDeviceDetails(user_id, device_id, ip_address, platform_id, reg_id) {
     try {
          return new Promise((resolve, reject) => {
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
               let data = "insert into device_details (user_id, device_id, app_id, ip_address, registration_id, platform_id, created_at, updated_at) values (" + user_id + ',' + "'" + device_id + "'" + ',' + 0 + ',' + "'" + ip_address + "'" + ",'" + reg_id + "'," + platform_id + ',' + "'" + formatted_date + "'" + ',' + "'" + formatted_date + "') ON DUPLICATE KEY UPDATE  user_id = '" + user_id + "', ip_address = '" + ip_address + "', registration_id = '" + reg_id + "', platform_id = '" + platform_id + "', updated_at = '" + formatted_date + "'";
               db.query(data, {}, function (err, row) {
                    if (err) {
                         reject(err)
                    }
               })
          })
     } catch (err) {
          console.log(err)
     }
}



/*
Purpose : getMyRoles function used get  role_id
author : Deepak Tiwari
Request : Require userId
Resposne : Returns user  role_id
*/
function getMyRoles(userId) {
     let json;
     try {
          let currentUserId;
          if (!userId) {
               currentUserId = '';//i have to add session code to get userid
          } else {
               currentUserId = userId;
          }
          return new Promise((resolve, reject) => {
               if (currentUserId != null) {
                    let data = "select role_id FROM user_roles WHERE user_id =" + currentUserId + " ORDER BY  user_roles.user_roles_id ";
                    db.query(data, {}, function (err, rows) {
                         if (err) {
                              return reject(err);
                         }
                         if (Object.keys(rows).length > 0) {
                              string = JSON.stringify(rows)
                              json = JSON.parse(string)
                              return resolve(json);
                         }
                    });
               } else {
                    console.log("Role Not Found For Specific User")
                    resolve();
               }
          })


     } catch (err) {
          console.log(err)
     }

}

/*
Purpose : getSupportRole function used get user role 
author : Deepak Tiwari
Request : Require userId
Resposne : Returns user support role
*/
function getSupportRole(userId) {
     let isSupportRole = [];
     let currentRoles = [];
     try {
          return new Promise((resolve, reject) => {
               getMyRoles(userId).then((role) => {
                    if (role != null) {
                         for (i = 0; i <= role.length; i++) {
                              if (i != role.length) {
                                   currentRoles.push(role[i].role_id);
                              } else {
                                   break;
                              }
                         }
                         let data = "select role_id from roles r where r.role_id in (" + currentRoles + ") and r.is_support_role =" + 1;
                         db.query(data, {}, function (err, rows) {
                              if (err) {
                                   return reject(err);
                              }
                              else if (Object.keys(rows).length > 0) {
                                   string = JSON.stringify(rows)
                                   json = JSON.parse(string)
                                   return resolve(json);

                              } else {
                                   resolve('');
                              }
                         })
                    } else {
                         resolve('');
                    }

               }).catch((err) => {
                    console.log(err);
                    reject(err);
               })

          })

     } catch (err) {
          console.log(err)
     }

}

/*
Purpose : getTeamList function used get team list
author : Deepak Tiwari
Request : Require userId
Resposne : Returns team list
*/
function getTeamList(userId) {
     try {
          let response = 0;
          let userList = [];
          return new Promise((resolve, reject) => {
               getSupportRole(userId).then((supportRole) => {
                    if (supportRole) {
                         supportRole.forEach(function (roleId) {
                              if (roleId.role_id > 0) {
                                   let data = "select user_id from user_roles where role_id = " + roleId.role_id + " group by  user_id";
                                   db.query(data, {}, function (err, rows) {
                                        if (err) {
                                             return reject(err);
                                        }
                                        else if (Object.keys(rows).length > 0) {
                                             string = JSON.stringify(rows)
                                             json = JSON.parse(string)
                                             json.forEach(function (userId) {
                                                  if (userId.user_id > 0) {
                                                       let data = "select user_id from users where reporting_manager_id =" + userId.user_id;
                                                       db.query(data, {}, function (err, rows) {
                                                            if (err) {
                                                                 reject(err);
                                                            }
                                                            else if (Object.keys(rows).length > 0) {
                                                                 userList.push(rows[0]);
                                                                 response = userList[0].user_id;
                                                                 resolve(response);
                                                            }
                                                       });
                                                  }
                                             });

                                        } else {
                                             resolve('');
                                        }

                                   });
                              }
                         });
                    } else {
                         if (userId > 0) {
                              let data = "select user_id from users where reporting_manager_id = " + userId;
                              db.query(data, {}, function (err, rows) {
                                   if (err) {
                                        reject(err);
                                   }
                                   else if (Object.keys(rows).length > 0) {
                                        response = rows[0]
                                        resolve(response.user_id);
                                   } else {
                                        resolve(response);
                                   }
                              });
                         }
                    }
               }).catch((err) => {
                    console.log(err);
                    reject(err);
               })
          })

     } catch (err) {
          console.log(err)
     }


}

/*
Purpose : checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Give access to the user to the application
*/
async function getTeamByUser(userId) {
     let response;
     let result = [];
     try {
          return new Promise(async (resolve, reject) => {
               response = userId;
               if (userId > 0) {
                    let childUserList = await getTeamList(userId)
                    // result = result.push(childUserList, response);
                    if (childUserList != 0) {
                         response = Object.assign({ response, childUserList })
                         resolve(Object.values(response));
                    } else {
                         result.push(response);
                         resolve(result);
                    }
               } else {
                    result.push(response);
                    resolve(result);
               }
               //resolve(response);
          })
     } catch (err) {
          reject(err)
     }
}
//used to get permissionLevel based on permission level id.
function getPermissionLevelData(permissionLevelId) {
     try {
          let permissionName = '';
          return new Promise((resolve, reject) => {
               if (permissionLevelId > 0) {
                    let data = "select name from permission_level where permission_level_id=" + permissionLevelId;
                    db.query(data, {}, function (err, response) {
                         if (err) {
                              reject(err);
                         } else if (Object.keys(response).length > 0) {
                              resolve(response);
                              permissionName = response.name
                         } else {
                              resolve(permissionName);
                         }
                    })
               }


          })

     } catch (err) {
          console.log(err);
     }
}
//used to get legalEntity Id based on userId
function getLegalEntityId(userId) {
     try {
          let legalEntityId = 0;
          return new Promise((resolve, reject) => {
               if (userId > 0) {
                    let data = "select legal_entity_id from users where user_id =" + userId;
                    db.query(data, {}, function (err, legalEntityData) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(legalEntityData[0]).length > 0) {
                              legalEntityId = typeof legalEntityData[0] != 'undefined' ? legalEntityData[0].legal_entity_id : 0;
                              resolve(legalEntityId);

                         }
                    })
               }

          })

     } catch (err) {
          console.log(err)
     }

}
//used to get LegalENtity id based on beatid
function getLeFromBeat(beat) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select l.`legal_entity_id` FROM pjp_pincode_area p inner JOIN legalentity_warehouses l ON p.`le_wh_id` = l.le_wh_id WHERE p.pjp_pincode_area_id=" + beat;
               db.query(query, {}, function (err, response) {
                    if (err) {
                         cosnsole.log(err);
                         reject(err);
                    } else if (Object.keys(response).length > 0) {
                         resolve(response[0].legal_entity_id);
                    } else {
                         resolve(0);
                    }
               })
          } catch (err) {
               console.log(err);
               reject(err);

          }
     })
}
function getUserPermission(userId, permissionLevelId) {
     try {
          let response = [];
          let allCategoryPermission = 0;
          let categoryPermissionArray = [];
          let categoryPermissionList;
          return new Promise((resolve, reject) => {
               if (userId > 0 && permissionLevelId > 0) {
                    let data = "select group_concat(object_id) as object_id  from user_permssion where user_id =" + userId + " && permission_level_id =" + permissionLevelId;
                    db.query(data, {}, function (err, response) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(response).length > 0) {
                              categoryPermissionList = response[0].hasOwnProperty('object_id') ? response[0].object_id : '';
                              if (categoryPermissionList != '') {
                                   categoryPermissionArray = categoryPermissionList;
                                   for (let i = 0; i < categoryPermissionArray.length; i++) {
                                        if (categoryPermissionArray[i] = 0) {
                                             allCategoryPermission = 1;
                                        }
                                   }
                                   console.log("allCategoryPermission", allCategoryPermission)
                                   resolve(allCategoryPermission);
                              }

                         }
                    })
               }

          })

     } catch (err) {
          console.log(err)
     }
}
//used to get brand details based on userId
function getBrandByUser(userId, legalEntityId) {
     try {
          console.log("userid", userId)
          let response = [];
          return new Promise((resolve, reject) => {
               if (userId > 0) {
                    let data = "select brands.brand_name ,brands.brand_id from brands group by brands.brand_id"
                    db.query(data, {}, function (err, res) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(res).length > 0) {
                              response.push(res)
                              resolve(response);
                         } else {
                              resolve(response);
                         }
                    })

               }
          })

     } catch (err) {
          console.log(err)
     }
}
//used to get categories based on userId and permissionlevel
function getCategoryByUser(userId, permissionLevelId) {
     try {
          let response = [];
          return new Promise((resolve, reject) => {
               if (userId > 0 && permissionLevelId > 0) {
                    getUserPermission(userId, permissionLevelId).then(allCategoryPermission => {
                         if (allCategoryPermission) {
                              let data = "select categories.category_id from categories";
                              db.query(data, {}, function (err, res) {
                                   if (err) {
                                        reject(err)
                                   } else if (Object.keys(res).length > 0) {
                                        response.push(res);
                                        resolve(response);
                                   } else {
                                        resolve(response);
                                   }
                              })
                         } else {
                              let query = "select categories.category_id from categories join user_permssion ON user_permssion.object_id = categories.category_id where user_permssion.user_id =" + userId + "&& user_permssion.permission_level_id =" + permissionLevelId + " group By categories.category_id";
                              db.query(query, {}, function (err, responsed) {
                                   if (err) {
                                        reject(err);
                                   } else if (Object.keys(responsed).length > 0) {
                                        response.push(responsed);
                                        resolve(response);

                                   } else {
                                        resolve(response);
                                   }
                              })
                         }
                    })
               }

          })

     } catch (err) {
          console.log(err)
     }
}
//used to get products based on userId , legalEntityId
function getProductsByUser(userId, permissionLevelId, legalEntityId) {
     try {
          let response;
          return new Promise((resolve, reject) => {
               if (userId > 0 && permissionLevelId > 0) {
                    getUserPermission(userId, 8).then(allCategoryPermission => {
                         if (allCategoryPermission) {
                              let data = "select products.product_title , products.product_id from products where products.legal_entity_id =" ///+ legalEntityId;
                              db.query(data, {}, function (err, res) {
                                   // console.log("allCategoryPermission", data)
                                   if (err) {
                                        reject(err)
                                   } else if (Object.keys(res).length > 0) {
                                        response.push(res)
                                        resolve(response);
                                   }
                              })
                         } else {
                              let query = "select products.product_title ,products.product_id from products join user_permssion ON user_permssion.object_id = products.category_id where user_permssion.user_id= " + userId + "&& user_permssion.permission_level_id = 8 && products.legal_entity_id =" + legalEntityId
                              db.query(query, {}, function (err, result) {
                                   if (err) {
                                        reject(err)
                                   } else if (Object.keys(result).length > 0) {
                                        response = JSON.parse(JSON.stringify(result))
                                   }
                                   resolve(response);
                              })
                         }
                    })

               }

          })

     } catch (err) {
          console.log(err)
     }
}
//Used to get Manufactures details based on userId
function getManufacturerByUser(userId, permissionLevelId, legalEntityId) {
     try {
          let response = [];
          return new Promise((resolve, reject) => {
               if (userId > 0 && permissionLevelId > 0) {
                    let data = "select business_legal_name , legal_entity_id from legal_entities where legal_entity_type_id = 1006 group by legal_entity_id ORDER BY business_legal_name ASC";
                    db.query(data, {}, function (err, result) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(result).length > 0) {
                              response.push(result)
                              resolve(response);
                         } else {
                              resolve(response)
                         }
                    })
               }

          })
     } catch (err) {
          console.log(err)
     }
}
//WareHouse details
// function getWarehouseData(currentUserId, permissionLevelId, active = 1) {
//      try {
//           let globalFeature;
//           let inActiveDCAccess;
//           let objectIds;
//           let resultset = [];
//           let sample = [];
//           return new Promise(async (resolve, reject) => {
//                if (currentUserId > 0 && permissionLevelId > 0) {
//                     globalFeature = await ConfirmOtpcheckPermissionByFeatureCode('GLB0001', currentUserId);
//                     inActiveDCAccess = await ConfirmOtpcheckPermissionByFeatureCode('GLBWH0001', currentUserId);

//                     if (active == 0) {
//                          inActiveDCAccess = 1;
//                     }
//                     var qry2 = "select group_concat(object_id) as object_id from user_permssion where user_id=" + currentUserId + " and permission_level_id=" + permissionLevelId;
//                     db.query(qry2, {}, function (err, rows) {
//                          if (err) {
//                               return reject(err);
//                          }
//                          if (rows.length > 0) {
//                               objectIds = rows[0].object_id.split(",");
//                               if (!globalFeature) {
//                                    var qry3 = "select GROUP_CONCAT(le_wh_id) as le_wh_id,dc_type from legalentity_warehouses where dc_type > 0 and is_disabled = 0";
//                                    if (inActiveDCAccess == 0) {
//                                         qry3 += ' and status=1';
//                                    }
//                                    if (objectIds.length == 1 || objectIds.includes(0)) {
//                                         console.log(objectIds);
//                                         console.log(objectIds.includes('0') + '=================');
//                                         if (objectIds.includes('0')) {
//                                              qry3 += " and dc_type in (118001,118002)";
//                                         } else {
//                                              qry3 += " and bu_id in (" + objectIds + ")";
//                                         }
//                                    } else {
//                                         qry3 += " and bu_id in (" + objectIds + ")";
//                                    }
//                                    qry3 += " group by dc_type";
//                               } else if (globalFeature) {
//                                    var qry3 = "select GROUP_CONCAT(le_wh_id) as le_wh_id,dc_type from legalentity_warehouses where dc_type > 0  and is_disabled = 0";
//                                    if (inActiveDCAccess == 0) {
//                                         qry3 += ' and status=1';
//                                    }
//                                    qry3 += " group by dc_type";
//                               }
//                               console.log(qry3);
//                               db.query(qry3, {}, async function (err1, rows1) {
//                                    if (err1) {
//                                         return reject(err1);
//                                    }
//                                    if (Object.keys(rows1).length > 0) {
//                                         for (var i = 0; i < rows1.length; i++) {
//                                              sample.push(rows1[i].dc_type = [rows1[i].le_wh_id]);
//                                         }
//                                         await resolve(sample);
//                                    }
//                                    else {
//                                         return reject("No Results found..")
//                                    }
//                               });

//                          }
//                          // let Data = "select * from legalentity_warehouses where (dc_type  > 0 and is_disabled = 0)";
//                          // if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
//                          //      Data = Data.concat(" and status = 1");
//                          // }
//                          // console.log("globalacccess", globalFeature)
//                          // if (!globalFeature) {
//                          //      if (result.length == 1 || 0 in result) {
//                          //           if (typeof result[0] != 'undefined' && (result[0] == 0 || 0 in result)) {
//                          //                Data = Data.concat("and dc_type IN (118001, 118002)");
//                          //           } else {
//                          //                Data = Data.concat("and bu_id IN(" + result + ")");
//                          //           }
//                          //      } else {
//                          //           Data = Data.concat("and bu_id IN(" + result + ")");
//                          //      }

//                          //      let update_query = "SET SESSION group_concat_max_len = 100000";
//                          //      db.query(update_query, {}, function (err, row) {
//                          //           if (err) {
//                          //                reject(err)
//                          //           } else {
//                          //                console.log("session updated")
//                          //           }
//                          //      })

//                          //      console.log("------1416------", Data);
//                          //      let data_1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
//                          //      db.query(data_1, {}, function (err, rows) {
//                          //           if (err) {
//                          //                reject(err)
//                          //           } else if (Object.keys(rows).length > 0) {
//                          //                // console.log("-----1470------", rows);
//                          //                query.push(rows);
//                          //                if (query.length > 0) {
//                          //                     query[0].forEach((element) => {
//                          //                          response = { [element.dc_type]: element.le_wh_id }
//                          //                     })
//                          //                     resolve(response);
//                          //                }
//                          //           }
//                          //      })
//                          // } else if (globalFeature) {
//                          //      let data_2 = "select * from legalentity_warehouses where dc_type > 0";
//                          //      db.query(data_2, {}, function (err, row) {
//                          //           if (err) {
//                          //                reject(err)
//                          //           } else if (Object.keys(row).length > 0) {
//                          //                query.push(row)
//                          //           }
//                          //      })
//                          //      if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
//                          //           let data2 = "select * from legalentity_warehouses where dc_type > 0 && status = 1";
//                          //           db.query(data2, {}, function (err, row) {
//                          //                if (err) {
//                          //                     reject(err)
//                          //                } else if (Object.keys(row).length > 0) {
//                          //                     query.push(row)
//                          //                }
//                          //           })
//                          //      }

//                          //      let update_query = "SET SESSION group_concat_max_len = 100000";
//                          //      db.query(update_query, {}, function (err, row) {
//                          //           if (err) {
//                          //                reject(err)
//                          //           } else {
//                          //                console.log("session updated")
//                          //           }
//                          //      })

//                          //      console.log("hello")
//                          //      let data1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
//                          //      db.query(data1, {}, function (err, rows) {
//                          //           if (err) {
//                          //                reject(err)
//                          //           } else if (Object.keys(rows).length > 0) {
//                          //                query.push(rows);
//                          //                if (query.length > 0) {
//                          //                     query[0].forEach((element) => {
//                          //                          response = { dc_type: element.le_wh_id }
//                          //                     })
//                          //                     resolve(response);
//                          //                }
//                          //           }
//                          //      })
//                          // }




//                          // db.query(Data, {}, function (err, res) {
//                          //      if (err) {
//                          //           reject(err);
//                          //      } else if (Object.keys(res).length > 0) {
//                          //           query.push(res);
//                          //           console.log("----1405 -------", query.length);
//                          //           if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
//                          //                let query_1 = "select * from legalentity_warehouses where dc_type  > 0 and is_disabled = 0 and status = 1";
//                          //                db.query(query_1, {}, function (err, rows) {
//                          //                     if (err) {
//                          //                          reject(err)
//                          //                     } else if (Object.keys(rows).length > 0) {
//                          //                          query.push(rows) //query returns only active records
//                          //                     }
//                          //                })
//                          //           }
//                          //           console.log("globalacccess", globalFeature)
//                          //           if (!globalFeature) {
//                          //                if (result.length == 1 || 0 in result) {
//                          //                     if (typeof result[0] != 'undefined' && (result[0] == 0 || 0 in result)) {
//                          //                          let query_1 = "select * from legalentity_warehouses where dc_type IN (118001, 118002) and  dc_type  > 0 and is_disabled = 0";
//                          //                          db.query(query_1, {}, function (err, rows) {
//                          //                               if (err) {
//                          //                                    reject(err)
//                          //                               } else if (Object.keys(rows).length > 0) {
//                          //                                    console.log("------1426------", rows.length)
//                          //                                    query.push(rows);
//                          //                                    console.log("----1425------", query.length);
//                          //                               }
//                          //                          })


//                          //                          console.log("------1456--------", query.length);
//                          //                          let update_query = "SET SESSION group_concat_max_len = 100000";
//                          //                          db.query(update_query, {}, function (err, row) {
//                          //                               if (err) {
//                          //                                    reject(err)
//                          //                               } else {
//                          //                                    console.log("session updated")
//                          //                               }
//                          //                          })

//                          //                          console.log("------1465------", query.length);
//                          //                          let data_1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
//                          //                          db.query(data_1, {}, function (err, rows) {
//                          //                               if (err) {
//                          //                                    reject(err)
//                          //                               } else if (Object.keys(rows).length > 0) {
//                          //                                    // console.log("-----1470------", rows);
//                          //                                    query.push(rows);
//                          //                                    if (query.length > 0) {
//                          //                                         query[0].forEach((element) => {
//                          //                                              response = { [element.dc_type]: element.le_wh_id }
//                          //                                         })
//                          //                                         resolve(response);
//                          //                                    }
//                          //                               }
//                          //                          })
//                          //                     } else {
//                          //                          let query_1 = "select * from legalentity_warehouses where bu_id IN(" + result + ") && dc_type  > 0 ";
//                          //                          db.query(query_1, {}, function (err, rows) {
//                          //                               if (err) {
//                          //                                    reject(err)
//                          //                               } else if (Object.keys(rows).length > 0) {
//                          //                                    query.push(rows);
//                          //                                    console.log("----1425------", query.length);

//                          //                                    console.log("response=====>", query)
//                          //                               }

//                          //                          })
//                          //                     }

//                          //                } else {
//                          //                     let query_1 = "select * from legalentity_warehouses where bu_id IN(" + result + ") && dc_type  > 0";
//                          //                     db.query(query_1, {}, function (err, rows) {
//                          //                          if (err) {
//                          //                               reject(err)
//                          //                          } else if (Object.keys(rows).length > 0) {
//                          //                               query.push(rows)
//                          //                          }

//                          //                     })
//                          //                }

//                          //           } else if (globalFeature) {
//                          //                let data_2 = "select * from legalentity_warehouses where dc_type > 0";
//                          //                db.query(data_2, {}, function (err, row) {
//                          //                     if (err) {
//                          //                          reject(err)
//                          //                     } else if (Object.keys(row).length > 0) {
//                          //                          query.push(row)
//                          //                     }
//                          //                })
//                          //                if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
//                          //                     let data2 = "select * from legalentity_warehouses where dc_type > 0 && status = 1";
//                          //                     db.query(data2, {}, function (err, row) {
//                          //                          if (err) {
//                          //                               reject(err)
//                          //                          } else if (Object.keys(row).length > 0) {
//                          //                               query.push(row)
//                          //                          }
//                          //                     })
//                          //                }

//                          //                let update_query = "SET SESSION group_concat_max_len = 100000";
//                          //                db.query(update_query, {}, function (err, row) {
//                          //                     if (err) {
//                          //                          reject(err)
//                          //                     } else {
//                          //                          console.log("session updated")
//                          //                     }
//                          //                })

//                          //                console.log("hello")
//                          //                let data1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
//                          //                db.query(data1, {}, function (err, rows) {
//                          //                     if (err) {
//                          //                          reject(err)
//                          //                     } else if (Object.keys(rows).length > 0) {
//                          //                          query.push(rows);
//                          //                          if (query.length > 0) {
//                          //                               query[0].forEach((element) => {
//                          //                                    response = { dc_type: element.le_wh_id }
//                          //                               })
//                          //                               resolve(response);
//                          //                          }
//                          //                     }
//                          //                })
//                          //           }
//                          // }

//                          // else {
//                          //      resolve('');
//                          // }
//                     })
//                }

//           })

//      } catch (err) {
//           console.log(err)
//      }
// }




//used to fetch warehouseDetails based on currentUserID , permissionLevelId
async function getWarehouseData(currentUserId, permissionLevelId, active = 1) {//changes required
     return new Promise(async (resolve, reject) => {
          try {
               let response = [];
               let globalFeatures;
               let globalFeature;
               let inActiveDCAccess;
               let inActiveDCAcc;
               let query = [];
               if (currentUserId > 0 && permissionLevelId > 0) {
                    //checking for global access features
                    globalFeatures = await ConfirmOtpcheckPermissionByFeatureCode('GLB0001', currentUserId)
                    console.log("globalacess ====>49", globalFeatures);
                    if (globalFeatures) {
                         globalFeature = globalFeatures;
                    } else {
                         globalFeature = 0;
                    }
                    //checking for inactiveAccess features
                    inActiveDCAcc = await ConfirmOtpcheckPermissionByFeatureCode('GLBWH0001', currentUserId)
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
                                   //  let Data = "select GROUP_CONCAT(le_wh_id ORDER BY le_wh_id DESC) AS le_wh_id , dc_type from legalentity_warehouses  where dc_type  > 0  AND is_disabled = 0";
                                   let Data = "SELECT SUBSTRING_INDEX(GROUP_CONCAT(le_wh_id ORDER BY le_wh_id DESC),',',1) AS le_wh_id  , dc_type FROM legalentity_warehouses  JOIN legal_entities ON legalentity_warehouses.`legal_entity_id`=legal_entities.`legal_entity_id` WHERE dc_type  > 0 AND is_disabled = 0 AND legal_entities.`legal_entity_type_id` IN (1014)";
                                   if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
                                        Data = Data.concat(" AND status = 1  group by dc_type");
                                   } else {
                                        Data = Data.concat(" group by dc_type");
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
                                                  resolve([{ '118001': 10774 }, { '118002': 10775 }]);
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

               } else {
                    resolve('');
               }
          } catch (err) {
               console.log(err)
               reject(err);
          }
     })
}
//Supplier details based on userId
function getSuppliersByUser(userId, legalEntityId, dcid = "", roleId = [], reportinglegalId = [], ignoreusers = 0) {
     try {
          let response = [];
          let isSupportRole;
          let ffusers;
          let globalAccess;
          let final_array = [];
          let suppliers = [];
          let fetched_reporting_ids = [];
          return new Promise((resolve, reject) => {
               getSupportRole(userId).then((res) => {
                    if (res) {
                         isSupportRole = res;
                    }
               }).catch(err => {
                    console.log(err.message);
               })
               let responsed = checkPermissionByFeatureCode("GLB0001", userId)
               if (responsed) {
                    globalAccess = responsed;
               }
               if (userId > 0) {
                    let result = checkPermissionByFeatureCode('FFUSERS001', userId)
                    if (result) {
                         let roleid = roleId;
                         let reportinglegalid = reportinglegalId;
                         if (dcid != "") {
                              let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id";
                              db.query(query, {}, function (err, row) {
                                   if (err) {
                                        reject(err)
                                   } else if (Object.keys(row).length > 0) {
                                        response.push(row)
                                   }
                              })
                         }
                         if (dcid != "" && ignoreusers == 1) {
                              let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where legalentity_warehouses.le_wh_id =" + dcid;
                              db.query(query, {}, function (err, row) {
                                   if (err) {
                                        reject(err)
                                   } else if (Object.keys(row).length > 0) {
                                        response.push(row)
                                   }
                              })
                         }

                         if (reportinglegalid.length > 0) {
                              if (!globalAccess) {
                                   let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where users.reporting_manager_id  IN (" + reportinglegalid + ")";
                                   db.query(query, {}, function (err, row) {
                                        // console.log("response", query)
                                        if (err) {
                                             reject(err)
                                        } else if (Object.keys(row).length > 0) {
                                             response.push(row)

                                        }
                                   })
                              }
                         } else if (ignoreusers == '') {
                              let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where users.reporting_manager_id =" + userId;
                              db.query(query, {}, function (err, row) {
                                   if (err) {
                                        reject(err)
                                   } else if (Object.keys(row).length > 0) {
                                        response.push(row)
                                   }
                              })
                         }
                         if (roleid != null && ignoreusers == 1) {
                              let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where user_roles.role_id IN (" + roleid + ")  && user_permssion.permission_level_id = 6  && group By user_roles.user_id";
                              db.query(query, {}, function (err, row) {
                                   if (err) {
                                        reject(err)
                                   } else if (Object.keys(row).length > 0) {
                                        response.push(row)
                                   }
                              })
                         }

                         let query = "select users.user_id from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where is_active = 1 group by users.user_id";
                         db.query(query, {}, function (err, row) {
                              if (err) {
                                   reject(err)
                              } else if (Object.keys(row).length > 0) {
                                   response.push(row)
                              }
                         })
                         resolve(response);
                    } else {
                         if (!(userId in fetched_reporting_ids)) {
                              if (dcid != '') {
                                   let query = "select  * from users join legalentity_warehouses ON legalentity_warehouses.legal_entity_id =  users.legal_entity_id";
                                   db.query(query, {}, function (err, rows) {

                                        if (err) {
                                             reject(err)
                                        } else if (Object.keys(rows).length > 0) {
                                             response.push(rows)

                                        }
                                   })
                              } else {
                                   let query = " select  *  from users join legalentity_warehouses ON legalentity_warehouses.legal_entity_id =  users.legal_entity_id where users.reporting_manager_id =" + userId;
                                   db.query(query, {}, function (err, rows) {
                                        if (err) {
                                             reject(err)
                                        } else if (Object.keys(rows).length > 0) {
                                             response.push(rows)
                                        }
                                   })
                              }
                              if (dcid != null && ignoreusers == 1) {
                                   let query = " select  * from users join legalentity_warehouses ON legalentity_warehouses.legal_entity_id =  users.legal_entity_id where legalentity_warehouses.le_wh_id =" + dcid;
                                   db.query(query, {}, function (err, rows) {
                                        if (err) {
                                             reject(err)
                                        } else if (Object.keys(rows).length > 0) {
                                             response.push(rows)
                                        }
                                   })
                              } else {
                                   let query = "select  users.user_id  from users join legalentity_warehouses ON legalentity_warehouses.legal_entity_id =  users.legal_entity_id";
                                   db.query(query, {}, function (err, rows) {
                                        if (err) {
                                             reject(err)
                                        } else if (Object.keys(rows).length > 0) {
                                             response.push(rows)
                                        }
                                   })
                                   fetched_reporting_ids.push(userId)

                              }

                         }

                    }
                    if (response.length > 0) {
                         let array = JSON.parse(JSON.stringify(response[[0]]))
                         for (let i = 0; i < array.length > 0; i++) {
                              suppliers.push(array[i]);
                              getSuppliersByUser(array[i].user_id, legalEntityId, dcid, roleId, reportinglegalId, ignoreusers).then(resulted_data => {
                                   if (resulted_data != null) {
                                        final_array.push(resulted_data);
                                   }
                              }).catch(err => {
                                   console.log(err.message)
                              })
                         }
                         resolve(final_array[0]);
                    }

               }

          })
     } catch (err) {
          reject(err)
     }
}
//Permissions based on userId
function getPermissionsByUser(userId, permissionLevelId) {
     try {
          let response = [];
          return new Promise((resolve, reject) => {
               if (userId > 0 && permissionLevelId > 0) {
                    let query = "select object_id from user_permssion where user_id =" + userId + " && permission_level_id =" + permissionLevelId + " group by object_id"
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(rows).length > 0) {
                              response.push(rows)
                              resolve(response);
                         } else {
                              resolve(response);
                         }
                    })
               }

          })
     } catch (err) {
          console.log(err)
     }

}
//Filtering userDetails based on userID , and permissionLevelID-6 (bu)
function getFilterData(permissionLevelId, userId) {
     try {
          return new Promise((resolve, reject) => {
               let response = {};
               let currentUserId;
               if (permissionLevelId > 0) {
                    if (userId) {
                         currentUserId = userId;
                    } else {
                         currentUserId = 0;
                    }
                    getPermissionLevelData(permissionLevelId).then((getPermissionLevelName) => {
                         if (getPermissionLevelName.length != 0) {
                              switch (getPermissionLevelName[0].name) {
                                   case 'brand':
                                        getLegalEntityId(currentUserId).then((legalEntityId) => {
                                             if (legalEntityId) {
                                                  getBrandByUser(currentUserId, legalEntityId).then((result) => {
                                                       let res = JSON.parse(JSON.stringify(result[[0]]))
                                                       response = { getPermissionLevelName: res }
                                                       resolve(response.getPermissionLevelName)
                                                  }).catch(err => {
                                                       console.log(err)
                                                  })

                                             }
                                        }).catch(err => {
                                             console.log(err)
                                        })
                                        break;
                                   case 'category':
                                        getCategoryByUser(currentUserId, permissionLevelId).then(result => {
                                             response = { getPermissionLevelName: result }
                                             resolve(response.getPermissionLevelName)
                                        }).catch(err => {
                                             console.log(err)
                                        })
                                        break;
                                   case 'manufacturer':
                                        getLegalEntityId(currentUserId).then((legalEntityId) => {
                                             if (legalEntityId) {
                                                  getManufacturerByUser(currentUserId, permissionLevelId, legalEntityId).then(result => {
                                                       let arr = JSON.parse(JSON.stringify(result[[0]]))
                                                       response = { getPermissionLevelName: arr }
                                                       resolve(response.getPermissionLevelName)
                                                  })
                                             }
                                        }).catch(err => {
                                             console.log(err)
                                        })
                                        break;
                                   case 'supplier':
                                        getLegalEntityId(currentUserId).then((legalEntityId) => {
                                             if (legalEntityId) {
                                                  getSuppliersByUser(currentUserId, legalEntityId).then((temp) => {
                                                       if (temp) {
                                                            response = { getPermissionLevelName: temp }
                                                            resolve(response.getPermissionLevelName);
                                                       }
                                                  })
                                             }
                                        }).catch(err => {
                                             console.log(err)
                                        })
                                        break;
                                   case 'products':
                                        getLegalEntityId(currentUserId).then((legalEntityId) => {
                                             if (legalEntityId) {
                                                  getProductsByUser(currentUserId, permissionLevelId, legalEntityId).then((result) => {
                                                       response = { [getPermissionLevelName[0].name]: result }
                                                       resolve(response.getPermissionLevelName)
                                                  }).catch(err => {
                                                       console.log(err)
                                                  })
                                             }
                                        }).catch(err => {
                                             console.log(err)
                                        })
                                        break;
                                   case 'sbu':
                                        getWarehouseData(currentUserId, permissionLevelId).then(result => {
                                             response = { [getPermissionLevelName[0].name]: result }
                                             resolve(response);
                                             // resolve(response.getPermissionLevelName)
                                        }).catch(err => {
                                             console.log(err)
                                        })

                                        break;
                                   case 'customer':
                                        let customers = [];
                                        getTeamByUser(currentUserId).then(users => {
                                             customers.push(users);
                                             response = { getPermissionLevelName: customers }
                                             resolve(response.getPermissionLevelName)
                                        }).catch(err => {
                                             console.log(err)
                                        })
                                        break;
                                   default:
                                        getPermissionsByUser(currentUserId, permissionLevelId).then(result => {
                                             if (result) {
                                                  response = { getPermissionLevelName: result }
                                                  resolve(response.getPermissionLevelName)
                                             }
                                        }).catch(err => {
                                             console.log(err)
                                        })
                                        break;
                              }

                         } else {
                              resolve(0);
                         }

                    }).catch(err => {
                         console.log(err)
                    })


               }
          })
     } catch (err) {
          console.log(err)
     }
}
//used to get EcashInfo of user based on usedId
function getEcashInfo(user_id) {
     try {
          let ecash_user_data = [];
          return new Promise((resolve, reject) => {
               if (user_id != null) {
                    let query = "select creditlimit,(cashback-applied_cashback) as ecash,payment_due from user_ecash_creditlimit where user_id =" + user_id + " limit 1"
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(rows).length > 0) {
                              ecash_user_data.push(rows[0]);
                              resolve(ecash_user_data[0]);
                         } else {
                              resolve(ecash_user_data);
                         }
                    })


               }
          })

     } catch (err) {
          console.log(err)
     }
}
//used to get WareHouseID of user from retails_flat table based on mobile_no
function getWarehouseIdByMobileNo(mobile_no) {
     console.log("-----2159------")
     return new Promise((resolve, reject) => {
          let data = "select d.dc_id from retailer_flat r JOIN pjp_pincode_area p ON r.beat_id = p.pjp_pincode_area_id JOIN dc_hub_mapping d ON d.hub_id = p.le_wh_id where r.mobile_no =" + mobile_no;
          db.query(data, {}, function (err, warehouse_id) {
               console.log("-----2162-----------", data);
               if (err) {
                    reject(err);
               } else if (Object.keys(warehouse_id).length > 0) {
                    console.log("=====>1604", warehouse_id)
                    resolve(warehouse_id[0].dc_id);
               } else {
                    resolve('');
               }
          })
     })
}
//hub details based on beatId
// function getHub(beat_id) {
//      return new Promise((resolve, reject) => {
//           let result = "";
//           if (beat_id) {
//                let data = "SELECT hub_id FROM beat_master WHERE beat_id  =" + beat_id;
//                db.query(data, {}, function (err, hub) {
//                     if (err) {
//                          reject(err)
//                     } else if (Object.keys(hub).length > 0) {
//                          hub.forEach(element => {
//                               result = element.hub_id + ',' + result;
//                          })
//                          result = result.slice(0, result.length - 1);
//                          resolve(result);
//                     } else {
//                          resolve('');
//                     }
//                })
//           } else {
//                resolve('');
//           }
//      })
// }

function getHub(beat_id) {
     return new Promise((resolve, reject) => {
          let result = "";
          if (beat_id) {
               let data = "SELECT hub_id FROM beat_master WHERE beat_id  =" + beat_id;
               db.query(data, {}, function (err, hub) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(hub).length > 0) {
                         hub.forEach(element => {
                              result = element.hub_id + ',' + result;
                         })
                         result = result.slice(0, result.length - 1);
                         resolve(result);
                    } else {
                         resolve('');
                    }
               })
          } else {
               resolve('');
          }
     })
}

//used to get lewh_id 
function getLewhDetails(beat_id) {
     return new Promise((resolve, reject) => {
          let result = "";
          if (beat_id) {
               let data = "SELECT dc_id FROM beat_master WHERE beat_id  =" + beat_id;
               db.query(data, {}, function (err, le_wh_id) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(le_wh_id).length > 0) {
                         le_wh_id.forEach(element => {
                              result = element.dc_id + ',' + result;
                         })
                         result = result.slice(0, result.length - 1);
                         resolve(result);
                    } else {
                         resolve('');
                    }
               })
          } else {
               resolve('');
          }
     })
}

//LogApi is log userRequest into mongoDb 
function logApiRequests1(data) {
     return new Promise((resolve, reject) => {
          var MongoClient = require('mongodb').MongoClient;
          var host = 'mongodb://' + process.env['MONGO_USER'] + ":" + process.env['MONGO_PASSWORD'] + "@" + process.env['MONGO_HOST'] + ":" + process.env['MONGO_PORT'] + "/" + process.env['MONGO_DATABASE'];
          MongoClient.connect(host, { useNewUrlParser: true, useUnifiedTopology: true }, function (err, db) {
               if (err) throw err;
               var dbo = db.db("ebutor");
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let apiUrl;
               let parameters;
               if (typeof data.apiUrl != 'undefined') {
                    apiUrl = data.apiUrl;
               }
               if (typeof data.parameter != 'undefined') {
                    parameters = data.parameter;
               }
               let body = {
                    apiUrl: apiUrl,
                    parameters: parameters,
                    created_at: formatted_date
               }
               dbo.collection('container_api_logs').insertOne(body, function (err, res) {
                    if (err) throw err;
                    db.close();
               });
          });
     });

}

//used to get lewh_id 
function getWhFromBeat(beat_id) {
     return new Promise((resolve, reject) => {
          let result = "";
          if (beat_id) {
               let data = "SELECT dc_id FROM beat_master WHERE beat_id  =" + beat_id;
               db.query(data, {}, function (err, le_wh_id) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(le_wh_id).length > 0) {
                         le_wh_id.forEach(element => {
                              result = element.dc_id + ',' + result;
                         })
                         result = result.slice(0, result.length - 1);
                         resolve(result);
                    } else {
                         resolve('');
                    }
               })
          } else {
               resolve('');
          }
     })
}

function subOrdinateList(userId){
     return new Promise((resolve , reject)=>{
          try{
               let subOrdinateCount = 0;
               let query =  "select fn_Emp_Subordinates_one_Heirarchy(" + userId  +") as reporting_manager";
               db.query(query,{}, function(err , response){
                    console.log("----2312----" , response[0].reporting_manager)
                         if(err){
                         console.log(err);
                         reject(err);
                         } else if (Object.keys(response).length > 0  && response[0].reporting_manager != null) {
                              subOrdinateCount =  response.length;
                              let array =  response[0].reporting_manager.split(',');
                              resolve(array.length);
                         } else {
                              resolve(subOrdinateCount);
                         }
               })
          }catch(err){
               console.log(err);
               reject(err);
          }
     })
}


/*
Purpose :  Used to confirm otp
Author :Deepak Tiwari
Request : phonenumber, otp, legal_entity_id, device_id, ip_address, reg_id, platform_id, module_id
Resposne : Returns Otp confirmation
*/
function otpConfirm(phonenumber, otp, legal_entity_id, device_id, ip_address, reg_id, platform_id, module_id) {
     try {
          let desc;
          let response_data = {};
          let function_response = {};
          let user_chkdet;
          let srm_check;
          let ff_check;
          let customer_chk;
          let lp_feature;
          let mobile_feature;
          let mfc;
          let creditlimit;
          let profile_picture;
          let result = [];
          let category = {};
          let dashboard;
          let new_dashboard;
          let Update_custoken;
          let is_dashboard;
          let has_child;
          let segment_det = [];
          let le_wh_id;
          let is_ff;
          let is_srm;
          let current_datetime = new Date();
          let beat_id;
          let decode_data;
          let sbu_lits;
          let decode_sbulist;
          let hub;
          let mobile_feature_array;
          let business_type_id;
          let pincode;
          let legal_entity_type_id;
          let parent_le_id;
          let subOrdinate;
          let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
          return new Promise((resolve, reject) => {
         
               if (legal_entity_id != '') {
                    console.log("Exsisting user generated ")
                    let query = "select description from master_lookup as ml where ml.value = 78002"
                    db.query(query, {}, function (err, master) {
                         if (err) {
                              console.log(err)
                         } else if (Object.keys(master).length > 0) {
                              desc = master[0].description;
                              // The reason to Join Legal Entity Id, is to get their parent legal entity id
                              let data_query = "select u.* , le.parent_le_id  from users as u left join legal_entities as le  ON le.legal_entity_id =  u.legal_entity_id where u.mobile_no =" + phonenumber + " && u.is_active = 1";
                              db.query(data_query, {}, async function (err, userchk) {
                                   if (err) {
                                        reject(err);
                                   } else if (Object.keys(userchk).length > 0) {
                                        if (userchk) {
                                             console.log("User details found")
                                             user_chkdet = userchk[0];
                                             subOrdinate =  await subOrdinateList(user_chkdet.user_id);
                                             ff_check = await ConfirmOtpcheckPermissionByFeatureCode('EFF001', user_chkdet.user_id);//Enabled field force
                                             srm_check = await ConfirmOtpcheckPermissionByFeatureCode('SRM001', user_chkdet.user_id);//Supplier relationship Management
                                             customer_chk = await ConfirmOtpcheckPermissionByFeatureCode('MCU001', user_chkdet.user_id);//retailers who is registered with ebutor
                                             let subordinate_count = ( subOrdinate != 0) ? 1 : 0;
                                             getFeatures(user_chkdet.user_id, 1).then(lp_features => {
                                                  if (lp_features.length > 0) {
                                                       lp_feature = lp_features;
                                                  }
                                                  getFeatures(user_chkdet.user_id, 2).then(feature => {
                                                       if (feature.length > 0) {
                                                            mobile_feature = feature;
                                                       }

                                                       let query_1 = "select mfc_id from mfc_customer_mapping where cust_le_id =" + user_chkdet.legal_entity_id + "&& is_active = 1";
                                                       db.query(query_1, {}, function (err, mfc_data) {
                                                            if (err) {
                                                                 reject(err)
                                                            } else {
                                                                 if ((Object.keys(mfc_data).length > 0) && (typeof mfc_data[0].mfc_id != 'undefined' && mfc_data[0].mfc_id != '')) {
                                                                      mfc = mfc_data[0].mfc_id;
                                                                 } else {
                                                                      mfc = 0;
                                                                 }
                                                                 if (device_id != null) {
                                                                      InsertDeviceDetails(user_chkdet.user_id, device_id, ip_address, platform_id, reg_id);
                                                                 }
                                                                 let query_2 = "select creditlimit from user_ecash_creditlimit where le_id =" + user_chkdet.legal_entity_id;
                                                                 db.query(query_2, {}, async function (err, creditlimit) {
                                                                      if (err) {
                                                                           reject(err)
                                                                      } else {
                                                                           if (creditlimit.length > 0) {
                                                                                creditlimit = creditlimit[0].creditlimit;
                                                                           } else {
                                                                                creditlimit = 0;
                                                                           }


                                                                           if (ff_check == 1 || srm_check == 1 || lp_feature != null || (mobile_feature != null && customer_chk == 0)) {
                                                                                console.log("-------2322-------")
                                                                                if (ff_check != null) {
                                                                                     is_ff = 1;
                                                                                } else {
                                                                                     is_ff = 0;
                                                                                }
                                                                                if (srm_check != null) {
                                                                                     is_srm = 1;
                                                                                } else {
                                                                                     is_srm = 0;
                                                                                }
                                                                                let random_number = JSON.stringify(Math.floor(10000000 + Math.random() * 90000000));
                                                                                let customer_token = crypto.createHash('md5').update(random_number).digest("hex");
                                                                                dashboard = await ConfirmOtpcheckPermissionByFeatureCode('FFD001', user_chkdet.user_id);//feild force DashBoard
                                                                                new_dashboard = await ConfirmOtpcheckPermissionByFeatureCode('MFD001', user_chkdet.user_id);//	Here it displays the beats assigned to ff on that day and the outlets assigned to that beats.	
                                                                                if (dashboard == 1 || new_dashboard == 1) {
                                                                                     is_dashboard = 1;
                                                                                     let teamsReturn = await getTeamByUser(user_chkdet.user_id);
                                                                                     //removing user_id from array
                                                                                     teamsReturn.splice(teamsReturn.indexOf(user_chkdet.user_id), 1);
                                                                                     if (teamsReturn.length >= 1) {
                                                                                          has_child = 1;
                                                                                     } else {
                                                                                          has_child = 0;
                                                                                     }
                                                                                } else {
                                                                                     is_dashboard = 0;
                                                                                     has_child = 0;
                                                                                }

                                                                                let data_3 = "select business_type_id,legal_entity_type_id,pincode,latitude,longitude,parent_le_id from legal_entities as le where le.legal_entity_id =" + legal_entity_id
                                                                                db.query(data_3, {}, function (err, segment) {
                                                                                     if (err) {
                                                                                          reject(err);
                                                                                     } else if (Object.keys(segment).length > 0) {
                                                                                          segment_det.push(segment[0])
                                                                                          business_type_id = segment_det[0].business_type_id;
                                                                                          pincode = segment_det[0].pincode;
                                                                                          legal_entity_type_id = segment_det[0].legal_entity_type_id;
                                                                                          parent_le_id = segment_det[0].parent_le_id;
                                                                                     }
                                                                                })

                                                                                if (module_id == 1) {

                                                                                     //checking weather that user details are already exist or not based on user
                                                                                     user.findOne({ user_id: user_chkdet.user_id }, function (err, response) {
                                                                                          if (err) {
                                                                                               console.log(err);
                                                                                               reject(err);
                                                                                               res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                                                                                          } else if (response != null) {
                                                                                               //based on token send we were validating  token  
                                                                                               user.updateOne(
                                                                                                    { user_id: user_chkdet.user_id },
                                                                                                    {
                                                                                                         $set: {
                                                                                                              lp_token: customer_token,
                                                                                                              updatedOn: moment().format("YYYY-MM-DD HH:mm:ss")
                                                                                                         }
                                                                                                    }
                                                                                               ).then((lpUpdate => {
                                                                                                    if (lpUpdate) {
                                                                                                         console.log("lptoken updated succcessfully");
                                                                                                    }
                                                                                               })).catch(err => {
                                                                                                    console.log(err);
                                                                                                    reject(err);
                                                                                               })
                                                                                          } else {
                                                                                               let query = "select mobile_no , password_token , lp_token ,user_id , otp ,lp_otp , is_disabled, is_active , legal_entity_id , created_by , created_at from users where user_id =" + user_chkdet.user_id;
                                                                                               db.query(query, {}, function (err, query_response) {
                                                                                                    if (err) {
                                                                                                         console.log(err);
                                                                                                    } else if (Object.keys(query_response).length > 0) {
                                                                                                         let body = {
                                                                                                              mobile: query_response[0].mobile_no,
                                                                                                              user_id: query_response[0].user_id,
                                                                                                              password_token: query_response[0].password_token,
                                                                                                              lp_token: query_response[0].lp_token,
                                                                                                              otp: query_response[0].otp,
                                                                                                              lp_otp: query_response[0].lp_otp,
                                                                                                              is_active: query_response[0].is_active,
                                                                                                              is_disabled: query_response[0].is_disabled,
                                                                                                              legal_entity_id: query_response[0].legal_entity_id,
                                                                                                              //  createdOn: query_response[0].created_at,
                                                                                                              createdBy: query_response[0].created_by
                                                                                                         }
                                                                                                         user.create(body).then(inserted => {
                                                                                                              //after inserting the records in validating customer_token or password_token
                                                                                                              if (inserted) {
                                                                                                                   user.updateOne(
                                                                                                                        { user_id: user_chkdet.user_id },
                                                                                                                        {
                                                                                                                             $set: {
                                                                                                                                  lp_token: customer_token,
                                                                                                                                  updatedOn: moment().format("YYYY-MM-DD HH:mm:ss")
                                                                                                                             }
                                                                                                                        }
                                                                                                                   ).then((lpUpdate => {
                                                                                                                        if (lpUpdate) {
                                                                                                                             console.log("lptoken updated succcessfully");
                                                                                                                        }
                                                                                                                   })).catch(err => {
                                                                                                                        console.log(err);
                                                                                                                        reject(err);
                                                                                                                   })
                                                                                                              }

                                                                                                         })

                                                                                                    }
                                                                                               })

                                                                                          }
                                                                                     })

                                                                                     let Query = "update users set lp_token ='" + customer_token + "', updated_at ='" + formatted_date + "'where user_id =" + user_chkdet.user_id;
                                                                                     db.query(Query, {}, function (err, response) {
                                                                                          if (err) {
                                                                                               reject(err)
                                                                                          } else {
                                                                                               console.log('1.updated Successfully module')

                                                                                          }
                                                                                     })
                                                                                } else if (module_id == 2) {
                                                                                     let Query = "update users set chat_token ='" + customer_token + "', updated_at ='" + formatted_date + "'where user_id =" + user_chkdet.user_id;
                                                                                     db.query(Query, {}, function (err, response) {
                                                                                          if (err) {
                                                                                               reject(err)
                                                                                          } else {
                                                                                               console.log('2.updated Successfully module')

                                                                                          }
                                                                                     })
                                                                                } else {
                                                                                     //checking weather that user details are already exist or not based on user
                                                                                     user.findOne({ user_id: user_chkdet.user_id }, function (err, response) {
                                                                                          if (err) {
                                                                                               console.log(err);
                                                                                               reject(err);
                                                                                               res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                                                                                          } else if (response != null) {
                                                                                               //based on token send we were validating  token  
                                                                                               user.updateOne(
                                                                                                    { user_id: user_chkdet.user_id },
                                                                                                    {
                                                                                                         $set: {
                                                                                                              password_token: customer_token,
                                                                                                              updatedOn: moment().format("YYYY-MM-DD HH:mm:ss")
                                                                                                         }
                                                                                                    }
                                                                                               ).then((passUpdate => {
                                                                                                    if (passUpdate) {
                                                                                                         console.log("---2465----");
                                                                                                    }
                                                                                               })).catch(err => {
                                                                                                    console.log(err);
                                                                                                    reject(err);
                                                                                               })
                                                                                          } else {
                                                                                               let query = "select mobile_no , password_token , lp_token ,user_id , otp ,lp_otp , is_disabled, is_active , legal_entity_id , created_by , created_at from users where user_id =" + user_chkdet.user_id;
                                                                                               db.query(query, {}, function (err, query_response) {
                                                                                                    if (err) {
                                                                                                         console.log(err);
                                                                                                    } else if (Object.keys(query_response).length > 0) {
                                                                                                         let body = {
                                                                                                              mobile: query_response[0].mobile_no,
                                                                                                              user_id: query_response[0].user_id,
                                                                                                              password_token: query_response[0].password_token,
                                                                                                              lp_token: query_response[0].lp_token,
                                                                                                              otp: query_response[0].otp,
                                                                                                              lp_otp: query_response[0].lp_otp,
                                                                                                              is_active: query_response[0].is_active,
                                                                                                              is_disabled: query_response[0].is_disabled,
                                                                                                              legal_entity_id: query_response[0].legal_entity_id,
                                                                                                              // createdOn: query_response[0].created_at,
                                                                                                              createdBy: query_response[0].created_by
                                                                                                         }
                                                                                                         user.create(body).then(inserted => {
                                                                                                              //after inserting the records in validating customer_token or password_token
                                                                                                              if (inserted) {
                                                                                                                   user.updateOne(
                                                                                                                        { user_id: user_chkdet.user_id },
                                                                                                                        {
                                                                                                                             $set: {
                                                                                                                                  password_token: customer_token,
                                                                                                                                  updatedOn: moment().format("YYYY-MM-DD HH:mm:ss")
                                                                                                                             }
                                                                                                                        }
                                                                                                                   ).then((passUpdate => {
                                                                                                                        if (passUpdate) {
                                                                                                                             console.log("------2503--------");
                                                                                                                        }
                                                                                                                   })).catch(err => {
                                                                                                                        console.log(err);
                                                                                                                        reject(err);
                                                                                                                   })
                                                                                                              }

                                                                                                         })

                                                                                                    }
                                                                                               })

                                                                                          }
                                                                                     })
                                                                                     let Query = "update users set password_token ='" + customer_token + "', updated_at ='" + formatted_date + "'where user_id =" + user_chkdet.user_id;
                                                                                     db.query(Query, {}, function (err, response) {
                                                                                          if (err) {
                                                                                               reject(err)
                                                                                          } else {
                                                                                               console.log('3.updated Successfully module')

                                                                                          }
                                                                                     })
                                                                                }

                                                                                if (user_chkdet.profile_picture == null) {
                                                                                     profile_picture = "";
                                                                                } else {
                                                                                     profile_picture = user_chkdet.profile_picture;
                                                                                }

                                                                                getFilterData(6, user_chkdet.user_id).then(DataFilter => {
                                                                                     decode_data = DataFilter;
                                                                                     sbu_lits = typeof decode_data.sbu != 'undefined' ? decode_data.sbu : [];
                                                                                     decode_sbulist = sbu_lits;
                                                                                     //response body 
                                                                                     let ff_ecash_details;
                                                                                     getEcashInfo(user_chkdet.user_id).then(result => {
                                                                                          ff_ecash_details = result;
                                                                                          let promo = [];
                                                                                          let format_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate()
                                                                                          let query = "select COUNT(*) as count FROM promotion_cashback_details pc  JOIN legalentity_warehouses lw  ON lw.le_wh_id =   pc.wh_id AND lw.legal_entity_id = " + user_chkdet.legal_entity_id + " WHERE cbk_status = 1 AND is_self in (0, 2) AND '" + format_date + "' BETWEEN start_date AND end_date"; /* */
                                                                                          db.query(query, {}, async function (err, res) {
                                                                                               if (err) {
                                                                                                    reject(err)
                                                                                               } else if (Object.keys(res).length > 0) {
                                                                                                    promo.push(res[0]);
                                                                                               }
                                                                                               mobile_feature_array
                                                                                               if (mobile_feature.length > 0) {
                                                                                                    mobile_feature_array = JSON.parse(JSON.stringify(mobile_feature[[0]]))
                                                                                               }
                                                                                               let promo_array = JSON.parse(JSON.stringify(promo[0]))
                                                                                               hub = (typeof decode_sbulist[1] != 'undefined' && decode_sbulist[1] != '') ? Object.values(decode_sbulist[1]) : 0;
                                                                                               le_wh_id = (typeof decode_sbulist[0] != 'undefined' && decode_sbulist[0] != '') ? Object.values(decode_sbulist[0]) : 0;
                                                                                               console.log("-----2614-------", hub, le_wh_id);
                                                                                               // category = await getCategoryByHubId(hub, le_wh_id);
                                                                                               response_data = {
                                                                                                    customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_chkdet.user_id, legal_entity_id: user_chkdet.legal_entity_id, parent_le_id: user_chkdet.parent_le_id,
                                                                                                    firstname: user_chkdet.firstname, lastname: user_chkdet.lastname, image: profile_picture, segment_id: business_type_id, pincode: '', le_wh_id: le_wh_id[0], hub: hub[0],
                                                                                                    is_active: user_chkdet.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: is_dashboard, has_child: has_child, lp_feature: lp_feature, mobile_feature: mobile_feature_array,
                                                                                                    beat_id: '', latitude: '', longitude: '', ff_ecash_details: ff_ecash_details, mfc: mfc, ff_full_name: user_chkdet.firstname + '_' + user_chkdet.lastname,
                                                                                                    ff_profile_pic: user_chkdet.profile_picture, credit_limit: creditlimit, aadhar_id: user_chkdet.aadhar_id, promotion_count: promo_array.count,subOrdinate:subordinate_count
                                                                                               };
                                                                                               let row = {};
                                                                                               row = { message: 'Thank you for confirming your Mobile Number', status: 1, approved: 1, data: response_data }
                                                                                               resolve(row)
                                                                                          })
                                                                                     }).catch(err => {
                                                                                          console.log(err);
                                                                                          reject({ status: "failed", message: "Something went wrong" })
                                                                                     })
                                                                                }).catch(err => {
                                                                                     console.log(err);
                                                                                     reject({ status: "failed", message: "Something went wrong" })
                                                                                })

                                                                           } else {
                                                                                console.log("-------2580-----")

                                                                                is_ff = 0;
                                                                                is_srm = 0;
                                                                                let random_number = JSON.stringify(Math.floor(10000000 + Math.random() * 90000000));
                                                                                let customer_token = crypto.createHash('md5').update(random_number).digest("hex");
                                                                                let data_7 = "select us.* , le.is_approved  from users as us  LEFT JOIN  legal_entities as le ON le.legal_entity_id =  us.legal_entity_id where us.mobile_no =" + phonenumber + " && le.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc + " && ml.is_active = 1 )";
                                                                                db.query(data_7, {}, async function (err, user_leg) {
                                                                                     if (err) {
                                                                                          reject(err)
                                                                                     } else if (Object.keys(user_leg).length > 0) {
                                                                                          let user_det = [];
                                                                                          user_det = user_leg[0];
                                                                                          let user_query = user_leg.length;

                                                                                          // ------After completing Registartion-------
                                                                                          if (user_query == 1 && user_det.is_active == 1) {
                                                                                               console.log("2179")
                                                                                               let customer_id = user_det.user_id;
                                                                                               user.findOne({ user_id: customer_id }, function (err, response) {
                                                                                                    if (err) {
                                                                                                         console.log(err);
                                                                                                         reject(err);
                                                                                                         res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                                                                                                    } else if (response != null) {
                                                                                                         //based on token send we were validating  token  
                                                                                                         user.updateOne(
                                                                                                              { user_id: customer_id },
                                                                                                              {
                                                                                                                   $set: {
                                                                                                                        password_token: customer_token,
                                                                                                                        updatedOn: moment().format("YYYY-MM-DD HH:mm:ss")
                                                                                                                   }
                                                                                                              }
                                                                                                         ).then((passUpdate => {
                                                                                                              if (passUpdate) {
                                                                                                                   console.log("----2619-----");
                                                                                                              }
                                                                                                         })).catch(err => {
                                                                                                              console.log(err);
                                                                                                              reject(err);
                                                                                                         })
                                                                                                    } else {
                                                                                                         let query = "select mobile_no , password_token , lp_token ,user_id , otp ,lp_otp , is_disabled, is_active , legal_entity_id , created_by , created_at from users where user_id =" + customer_id;
                                                                                                         db.query(query, {}, function (err, query_response) {
                                                                                                              if (err) {
                                                                                                                   console.log(err);
                                                                                                              } else if (Object.keys(query_response).length > 0) {
                                                                                                                   let body = {
                                                                                                                        mobile: query_response[0].mobile_no,
                                                                                                                        user_id: query_response[0].user_id,
                                                                                                                        password_token: query_response[0].password_token,
                                                                                                                        lp_token: query_response[0].lp_token,
                                                                                                                        otp: query_response[0].otp,
                                                                                                                        lp_otp: query_response[0].lp_otp,
                                                                                                                        is_active: query_response[0].is_active,
                                                                                                                        is_disabled: query_response[0].is_disabled,
                                                                                                                        legal_entity_id: query_response[0].legal_entity_id,
                                                                                                                        // createdOn: query_response[0].created_at,
                                                                                                                        createdBy: query_response[0].created_by
                                                                                                                   }
                                                                                                                   user.create(body).then(inserted => {
                                                                                                                        //after inserting the records in validating customer_token or password_token
                                                                                                                        if (inserted) {
                                                                                                                             user.updateOne(
                                                                                                                                  { user_id: customer_id },
                                                                                                                                  {
                                                                                                                                       $set: {
                                                                                                                                            password_token: customer_token,
                                                                                                                                            updatedOn: moment().format("YYYY-MM-DD HH:mm:ss")
                                                                                                                                       }
                                                                                                                                  }
                                                                                                                             ).then((passUpdate => {
                                                                                                                                  if (passUpdate) {
                                                                                                                                       console.log("-----2657--------");
                                                                                                                                  }
                                                                                                                             })).catch(err => {
                                                                                                                                  console.log(err);
                                                                                                                                  reject(err);
                                                                                                                             })
                                                                                                                        }

                                                                                                                   })

                                                                                                              }
                                                                                                         })

                                                                                                    }
                                                                                               })

                                                                                               let que = "update users as us set password_token ='" + customer_token + "' , updated_at ='" + formatted_date + "' where us.user_id =" + customer_id;
                                                                                               db.query(que, {}, (err, updated) => {
                                                                                                    if (err) {
                                                                                                         console.log(err)
                                                                                                         reject(err)
                                                                                                    } else {
                                                                                                         console.log("----2679----")
                                                                                                    }
                                                                                               })

                                                                                               let data_3 = "select business_type_id,legal_entity_type_id,pincode,latitude,longitude,parent_le_id from legal_entities as le where le.legal_entity_id =" + legal_entity_id
                                                                                               db.query(data_3, {}, function (err, segment) {
                                                                                                    if (err) {
                                                                                                         reject(err);
                                                                                                    } else if (Object.keys(segment).length > 0) {
                                                                                                         segment_det.push(segment[0])
                                                                                                         business_type_id = segment_det[0].business_type_id;
                                                                                                         pincode = segment_det[0].pincode;
                                                                                                         legal_entity_type_id = segment_det[0].legal_entity_type_id;
                                                                                                         parent_le_id = segment_det[0].parent_le_id;
                                                                                                    }
                                                                                               })

                                                                                               // Adding Parent Le Id 
                                                                                               if (user_det.profile_picture == null) {
                                                                                                    profile_picture = "";
                                                                                               } else {
                                                                                                    profile_picture = user_det.profile_picture;
                                                                                               }

                                                                                               // let le_wh = await getWarehouseIdByMobileNo(phonenumber);
                                                                                               // if (le_wh == '') {
                                                                                               //      le_wh_id = '';
                                                                                               // } else {
                                                                                               //      le_wh_id = le_wh;
                                                                                               // }



                                                                                          }

                                                                                          let queu = "select beat_id from customers where le_id =" + legal_entity_id;
                                                                                          db.query(queu, {}, async function (err, beat) {
                                                                                               if (err) {
                                                                                                    reject(err)
                                                                                               } else if (Object.keys(beat).length > 0) {
                                                                                                    beat_id = beat
                                                                                                    let hub = '';
                                                                                                    let ecash_details;
                                                                                                    if (beat_id != '' && beat_id.length > 0) {
                                                                                                         getHub(beat_id[0].beat_id).then(async (hub_value) => {
                                                                                                              if (hub_value != '') {
                                                                                                                   hub = hub_value
                                                                                                              } else {
                                                                                                                   hub = '';
                                                                                                              }
                                                                                                              let le_wh = await getWhFromBeat(beat_id[0].beat_id);
                                                                                                              if (le_wh == '') {
                                                                                                                   le_wh_id = '';
                                                                                                              } else {
                                                                                                                   le_wh_id = le_wh;
                                                                                                              }
                                                                                                              //used to getEcashinfo
                                                                                                              getEcashInfo(user_det.user_id).then(result => {
                                                                                                                   ecash_details = result
                                                                                                                   let promo;
                                                                                                                   let format_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
                                                                                                                   if (le_wh_id != '' && typeof le_wh_id != 'undefined') {
                                                                                                                        let query_3 = "select  count(*) as count from promotion_cashback_details where wh_id In(" + le_wh_id + ")&& cbk_status=1 &&  is_self in (1,2) && '" + format_date + "'between start_date and end_date && customer_type like '%" + legal_entity_type_id + "%'";//le_wh_id
                                                                                                                        db.query(query_3, {}, function (err, promotion_data) {
                                                                                                                             if (err) {
                                                                                                                                  reject(err)
                                                                                                                             } else if (Object.keys(promotion_data).length > 0) {
                                                                                                                                  promo = promotion_data;
                                                                                                                                  let mobile_feature_array_1 = JSON.parse(JSON.stringify(mobile_feature[[0]]))
                                                                                                                                  function_response = { customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_det.user_id, legal_entity_id: user_det.legal_entity_id, parent_le_id: parent_le_id, firstname: user_det.firstname, lastname: user_det.lastname, image: profile_picture, segment_id: business_type_id, pincode: pincode, le_wh_id: le_wh_id, hub: hub, is_active: user_det.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: 0, lp_feature: [], mobile_feature: mobile_feature_array_1, beat_id: beat_id[0].beat_id, latitude: segment_det[0].latitude, longitude: segment_det[0].longitude, ecash_details: ecash_details, ff_full_name: user_det.firstname + ' ' + user_det.lastname, ff_profile_pic: user_det.profile_picture, mfc: mfc, credit_limit: creditlimit, aadhar_id: user_det.aadhar_id, promotion_count: promo[0].count,subOrdinate:subordinate_count }
                                                                                                                                  let res1 = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 1, data: function_response };
                                                                                                                                  resolve(res1);
                                                                                                                             }
                                                                                                                        })
                                                                                                                   } else {
                                                                                                                        let mobile_feature_array_1 = JSON.parse(JSON.stringify(mobile_feature[[0]]));
                                                                                                                        function_response = {
                                                                                                                             customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_det.user_id, legal_entity_id: user_det.legal_entity_id,
                                                                                                                             parent_le_id: parent_le_id, firstname: user_det.firstname, lastname: user_det.lastname, image: profile_picture, segment_id: business_type_id, pincode: pincode,
                                                                                                                             le_wh_id: le_wh_id, hub: hub, hub_categories: '', is_active: user_det.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: 0, lp_feature: [], mobile_feature: mobile_feature_array_1,
                                                                                                                             beat_id: beat_id[0].beat_id, latitude: segment_det[0].latitude, longitude: segment_det[0].longitude, ecash_details: ecash_details, ff_full_name: user_det.firstname + ' ' + user_det.lastname,
                                                                                                                             ff_profile_pic: user_det.profile_picture, mfc: mfc, credit_limit: creditlimit, aadhar_id: user_det.aadhar_id, promotion_count: 0,subOrdinate:subordinate_count
                                                                                                                        }
                                                                                                                        let res1 = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 1, data: function_response };
                                                                                                                        resolve(res1);
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
                                                                                               } else {
                                                                                                    console.log("User does not have beat assign")
                                                                                                    resolve({ message: 'Something went wrong. Plz contact support on - 04066006442' });
                                                                                               }
                                                                                          })
                                                                                     }
                                                                                })
                                                                           }
                                                                      }
                                                                 })
                                                            }
                                                       })
                                                  }).catch(err => {
                                                       console.log(err);
                                                       reject({ status: "failed", message: "Something went wrong" })
                                                  })
                                             }).catch(err => {
                                                  console.log(err);
                                                  reject({ status: "failed", message: "Something went wrong" })
                                             })
                                        }
                                   } else {
                                        let res = { message: "Your account has been deactivated. Plz contact support on - 04066006442 Error_Code : 8003", status: 0, approved: 0 };
                                        resolve(res);
                                   }
                              })
                         }
                    })
               } else {
                    console.log("New User generated otp")
                    let user_temp_query;
                    userTemp.countDocuments({
                         $and: [
                              { mobile: phonenumber }, { otp: otp }
                         ]
                    }, function (err, response) {
                         if (err) {
                              console.log(err);
                              reject(err);
                              res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                         } else if (response != null) {
                              // ------Before completing Registartion-------
                              user_temp_query = response;
                              if (user_temp_query == 1) {
                                   userTemp.findOne({ mobile: phonenumber }, function (err, response) {
                                        if (err) {
                                             console.log(err);
                                             reject(err);
                                             res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                                        } else if (response != null) {
                                             //based on token send we were validating  token  
                                             userTemp.updateOne(
                                                  {
                                                       mobile: phonenumber
                                                  },
                                                  {
                                                       $set: {
                                                            status: 1,
                                                            updatedOn: formatted_date
                                                       }
                                                  }
                                             ).then((otpUpdated => {
                                                  if (otpUpdated) {
                                                       console.log("---userTemp--2854--");
                                                       let res = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 0 };
                                                       resolve(res);
                                                  }
                                             })).catch(err => {
                                                  console.log(err);
                                                  reject(err);
                                             })
                                        } else {
                                             let query = "select mobile_no ,  otp ,legal_entity_type_id , status  from user_temp where mobile_no =" + phonenumber;
                                             db.query(query, {}, function (err, query_response) {
                                                  if (err) {
                                                       console.log(err);
                                                  } else if (Object.keys(query_response).length > 0) {
                                                       let body = {
                                                            mobile: query_response[0].mobile_no,
                                                            otp: query_response[0].otp,
                                                            legal_entity_type_id: query_response[0].legal_entity_type_id,
                                                            status: query_response[0].status,
                                                            createdOn: formatted_date
                                                       }
                                                       userTemp.create(body).then(inserted => {
                                                            //after inserting the records in validating customer_token or password_token
                                                            if (inserted) {
                                                                 userTemp.updateOne(
                                                                      {
                                                                           mobile: phonenumber
                                                                      },
                                                                      {
                                                                           $set: {
                                                                                status: 1,
                                                                                updatedOn: formatted_date
                                                                           }
                                                                      }
                                                                 ).then((otpUpdated => {
                                                                      if (otpUpdated) {
                                                                           let res = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 0 };
                                                                           resolve(res);
                                                                      }
                                                                 })).catch(err => {
                                                                      console.log(err);
                                                                      reject(err);
                                                                 })
                                                            }

                                                       })

                                                  }
                                             })

                                        }
                                   })
                                   let data_4 = "update user_temp as ustmp set status = 1  , updated_at = '" + formatted_date + "' where ustmp.mobile_no ='" + phonenumber + "'";
                                   db.query(data_4, {}, function (err, update_status) {
                                        if (err) {
                                             reject(err)
                                        } else if (update_status) {
                                             let res = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 0 };
                                             resolve(res);
                                        }
                                   })
                              } else {
                                   resolve({ 'status': 'failed', 'message': "Please enter Valid OTP" })
                              }
                         } else {
                              resolve({ 'status': 'failed', 'message': "Please enter Valid OTP" })
                         }
                    })
                    // let query_4 = "select * from user_temp  as ustemp where ustemp.mobile_no =" + phonenumber + " and  ustemp.otp =" + otp
                    // db.query(query_4, {}, function (err, use_temp) {
                    //      if (err) {
                    //           reject(err)
                    //      } else if (Object.keys(use_temp).length > 0) {
                    //           user_temp_det.push(use_temp);
                    //      }
                    //      if (use_temp != null) {
                    //           user_temp_det.push(use_temp[0]);
                    //           user_temp_query = use_temp.length;
                    //           // ------Before completing Registartion-------
                    //           if (user_temp_query == 1) {
                    //                userTemp.findOne({ mobile: phonenumber }, function (err, response) {
                    //                     if (err) {
                    //                          console.log(err);
                    //                          reject(err);
                    //                          res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                    //                     } else if (response != null) {
                    //                          //based on token send we were validating  token  
                    //                          userTemp.updateOne(
                    //                               {
                    //                                    mobile: phonenumber
                    //                               },
                    //                               {
                    //                                    $set: {
                    //                                         status: 1,
                    //                                         updatedOn: formatted_date
                    //                                    }
                    //                               }
                    //                          ).then((otpUpdated => {
                    //                               if (otpUpdated) {
                    //                                    console.log("---userTemp--");
                    //                               }
                    //                          })).catch(err => {
                    //                               console.log(err);
                    //                               reject(err);
                    //                          })
                    //                     } else {
                    //                          let query = "select mobile_no ,  otp ,legal_entity_type_id , status  from user_temp where mobile_no =" + phonenumber;
                    //                          db.query(query, {}, function (err, query_response) {
                    //                               if (err) {
                    //                                    console.log(err);
                    //                               } else if (Object.keys(query_response).length > 0) {
                    //                                    let body = {
                    //                                         mobile: query_response[0].mobile_no,
                    //                                         otp: query_response[0].otp,
                    //                                         legal_entity_type_id: query_response[0].legal_entity_type_id,
                    //                                         status: query_response[0].status,
                    //                                         createdOn: formatted_date
                    //                                    }
                    //                                    userTemp.create(body).then(inserted => {
                    //                                         //after inserting the records in validating customer_token or password_token
                    //                                         if (inserted) {
                    //                                              userTemp.updateOne(
                    //                                                   {
                    //                                                        mobile: phonenumber
                    //                                                   },
                    //                                                   {
                    //                                                        $set: {
                    //                                                             status: 1,
                    //                                                             updatedOn: formatted_date
                    //                                                        }
                    //                                                   }
                    //                                              ).then((otpUpdated => {
                    //                                                   if (otpUpdated) {
                    //                                                        console.log("UserTemp updated succcessfully ---- > 2863");
                    //                                                   }
                    //                                              })).catch(err => {
                    //                                                   console.log(err);
                    //                                                   reject(err);
                    //                                              })
                    //                                         }

                    //                                    })

                    //                               }
                    //                          })

                    //                     }
                    //                })
                    //                let data_4 = "update user_temp as ustmp set status = 1  , updated_at = '" + formatted_date + "' where ustmp.mobile_no ='" + phonenumber + "'";
                    //                db.query(data_4, {}, function (err, update_status) {
                    //                     if (err) {
                    //                          reject(err)
                    //                     } else if (update_status) {
                    //                          let res = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 0 };
                    //                          resolve(res);
                    //                     }
                    //                })
                    //           } else {
                    //                resolve('');
                    //           }
                    //      } else {
                    //           resolve({ 'status': 'failed', 'message': "Please Send Valid OTP" })
                    //      }

                    // })
               }
          })
     } catch (err) {
          console.log(err)
          let error = { status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 5000" };
          return error;
     }

}

/*
Purpose :  After Registration Process,while login OTP confirmation functionality used to confirm OTP
Author :Deepak Tiwari
Request : telephone, otp, device_id, ip_address, reg_id, platform_id, module_id
Resposne : Returns Otp confirmation
*/
module.exports.confirmOtp = function (telephone, otp, device_id, ip_address, reg_id, platform_id, module_id) {
     try {
          let users = [];
          let userTemp = [];
          let salesTargetFeature;
          let saleTargetFeature;
          let appstatus_legal;
          return new Promise((resolve, reject) => {
               if (typeof telephone != 'undefined' && telephone != '') {
                    if (typeof otp != 'undefined' && otp != '') {
                         console.log("----3090-------")
                         checkOtpUser(otp, telephone).then((user) => {
                              if (user) {
                                   users.push(user);
                                   if (Object.keys(users).length == 1 && user != '') {
                                        getStatus(telephone).then((appstatus) => {
                                             if (typeof appstatus != 'undefined' && appstatus != '') {
                                                  appstatus_legal = appstatus[0].legal_entity_id;
                                             } else {
                                                  appstatus_legal = '';
                                             }

                                             //used to confirm otp
                                             otpConfirm(telephone, otp, appstatus_legal, device_id, ip_address, reg_id, platform_id, module_id).then(async (result) => {
                                                  let request = { parameter: result, apiUrl: 'Login' };
                                                  logApiRequests1(request);
                                                  if (result.status == 1) {
                                                       if (typeof result.data.customer_id != 'undefined') {
                                                            salesTargetFeature = await ConfirmOtpcheckPermissionByFeatureCode('SALESTARGET001', result.data.customer_id)
                                                            if (salesTargetFeature == 1) {
                                                                 result.data.sales_target = 1;
                                                            } else {
                                                                 result.data.sales_target = 0;
                                                            }

                                                            saleTargetFeature = await ConfirmOtpcheckPermissionByFeatureCode('MBMSU001', result.data.customer_id)
                                                            if (saleTargetFeature == 1) {
                                                                 result.data.must_sku_list = 1;
                                                            } else {
                                                                 result.data.must_sku_list = 0;
                                                            }

                                                       } else {
                                                            result.data.sales_target = 0;
                                                            result.data.must_sku_list = 0;
                                                       }
                                                       let check = { status: 'success', message: 'confirm', data: result }
                                                       resolve(check);

                                                  } else if (result.status == 0) {
                                                       resolve({ status: "failed", message: "Your account has been deactivated. Plz contact support on - 04066006442" })
                                                  } else {
                                                       resolve({ status: "failed", message: result.message })
                                                  }
                                             }).catch((err) => {
                                                  console.log(err)
                                                  resolve({ status: "failed", message: "Something went wrong" })
                                             })
                                        }).catch(err => {
                                             console.log(err);
                                             resolve({ status: "failed", message: "Something went wrong" })
                                        })
                                   } else {
                                        resolve({ status: "failed", message: "Please enter valid OTP ", "data": [] })
                                   }
                              } else {
                                   checkOtpUsertemp(otp, telephone).then((usertemp) => {
                                        userTemp.push(usertemp);
                                        if (Object.keys(userTemp).length == 1 && userTemp != '') {
                                             //fetching users legalEntityId  based on entered mobile number
                                             getStatus(telephone).then((appstatus) => {
                                                  if (typeof appstatus != 'undefined' && appstatus != '') {
                                                       appstatus_legal = appstatus[0].legal_entity_id;
                                                  } else {
                                                       appstatus_legal = '';
                                                  }
                                                  otpConfirm(telephone, otp, appstatus_legal, device_id, ip_address, reg_id, platform_id, module_id).then((result) => {

                                                       let request = { parameter: result, apiUrl: 'Login' };
                                                       logApiRequests1(request);
                                                       if (result.status == 1) {
                                                            //newly added
                                                            let check = { status: 'success', message: 'confirm', data: result }
                                                            resolve(check)
                                                       } else if (result.status == 0) {
                                                            resolve({ status: "failed", message: "Your account has been deactivated. Plz contact support on - 04066006442" })
                                                       } else {
                                                            resolve({ status: "failed", message: result.message })
                                                       }
                                                  }).catch((err) => {
                                                       console.log(err)
                                                  })
                                             }).catch((err) => {
                                                  console.log(err)
                                             })

                                        } else {
                                             resolve({ 'status': 'failed', 'message': "Please enter valid OTP.", "data": [] })
                                        }
                                   }).catch((err) => {
                                        console.log(err.message)
                                   })
                              }
                         }).catch((err) => {
                              console.log(err)
                              resolve({ status: "failed", message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001" })
                         })
                    } else {
                         let res_1 = { status: "failed", message: "Please enter valid Otp ", "data": [] };
                         resolve(res_1)
                    }
               }
               else {
                    let res_2 = { status: "failed", message: "Please enter mobile number", "data": [] };
                    resolve(res_2)
               }
          })
     } catch (err) {
          console.log(err)
          let data = { status: "failed", message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 5000" }
          return data;
     }
}

/*
Purpose :  Used to resend Otp 
Author :Deepak Tiwari
Request : telephone, otp, customer_token, custflag, user_id
Resposne : Returns Otp confirmation
*/

function resendGeneratedOtp(telephone, otpflag, customer_token, custflag, userId, app_flag) {
     try {
          console.log("otpflag", otpflag, userId);
          var Curl = require('node-libcurl').Curl;
          return new Promise((resolve, reject) => {
               var curl = new Curl();
               let random_number = Math.floor(100000 + Math.random() * 900000);
               let string = JSON.stringify(customer_token);
               let mobile_number = telephone;
               let buyer_type_id = 3001; // for kirana,s
               let app_unique_key = "qoVggl61OKE";
               let message = "<#> Your OTP for Ebutor is " + random_number + "\n - " + app_unique_key;
               if (mobile_number.length >= 10 && message != "") {
                    let Host = process.env.SMS_HOST;
                    let receipientno = mobile_number;
                    let senderID = process.env.SMS_SENDERID;
                    curl.setOpt(Curl.option.URL, process.env.SMS_URL);
                    curl.setOpt('FOLLOWLOCATION', true);
                    curl.setOpt(Curl.option.POST, 1);
                    curl.setOpt(Curl.option.POSTFIELDS, "user=" + Host + "&senderID=" + senderID + "&receipientno=" + receipientno + "&msgtxt=" + message);
                    curl.on('end', function (statusCode, body, headers) {
                         this.close();
                    });

                    curl.on('error', function (err, curlErrorCode) {
                         console.error(err.message);
                         console.error('---');
                         console.error(curlErrorCode);
                         this.close();
                    });

                    let buffer = curl.perform();

                    if (buffer == null) {
                    } else {
                         let res = {};
                         let current_datetime = new Date();
                         let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                         if (otpflag == 0) {
                              //checking weather that user details are already exist or not based on user
                              let body = {
                                   mobile: mobile_number,
                                   otp: random_number,
                                   legal_entity_type_id: buyer_type_id,
                                   createdOn: formatted_date
                              }
                              userTemp.create(body).then(UserTempRecord => {
                                   if (UserTempRecord) {
                                        console.log("record inserted--->")
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                              let query = "insert into  user_temp(mobile_no , otp , legal_entity_type_id , created_at) values (" + mobile_number + ',' + random_number + ',' + buyer_type_id + ",'" + formatted_date + "')";
                              db.query(query, {}, function (err, rows) {
                                   if (err) {
                                        console.log(err)
                                   } else {
                                        res = { message: "Please Confirm  OTP", status: 1 }
                                        resolve(res);
                                   }
                              })
                         } else if (otpflag == 1) {
                              //checking weather that user details are already exist or not based on user
                              user.findOne({
                                   $and: [
                                        { user_id: userId }, { is_active: 1 }
                                   ]
                              }, function (err, response) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                        res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                                   } else if (response != null) {
                                        //based on token send we were validating  token  
                                        user.updateOne(
                                             {
                                                  $and: [
                                                       { user_id: userId }, { is_active: 1 }
                                                  ]
                                             },
                                             {
                                                  $set: {
                                                       otp: random_number,
                                                       updatedOn: formatted_date
                                                  }
                                             }
                                        ).then((otpUpdated => {
                                             if (otpUpdated) {
                                                  console.log("otpUpdated updated succcessfully ---> 345");
                                             }
                                        })).catch(err => {
                                             console.log(err);
                                             reject(err);
                                        })
                                   } else {
                                        let query = "select mobile_no , password_token , lp_token ,user_id , otp ,lp_otp , is_disabled, is_active , legal_entity_id , created_by , created_at from users where user_id =" + userId;
                                        db.query(query, {}, function (err, query_response) {
                                             if (err) {
                                                  console.log(err);
                                             } else if (Object.keys(query_response).length > 0) {
                                                  let body = {
                                                       mobile: query_response[0].mobile_no,
                                                       user_id: query_response[0].user_id,
                                                       password_token: query_response[0].password_token,
                                                       lp_token: query_response[0].lp_token,
                                                       otp: query_response[0].otp,
                                                       lp_otp: query_response[0].lp_otp,
                                                       is_active: query_response[0].is_active,
                                                       is_disabled: query_response[0].is_disabled,
                                                       legal_entity_id: query_response[0].legal_entity_id,
                                                       //  createdOn: query_response[0].created_at,
                                                       createdBy: query_response[0].created_by
                                                  }
                                                  user.create(body).then(inserted => {
                                                       //after inserting the records in validating customer_token or password_token
                                                       if (inserted) {
                                                            user.updateOne(
                                                                 {
                                                                      $and: [
                                                                           { user_id: userId }, { is_active: 1 }
                                                                      ]
                                                                 },
                                                                 {
                                                                      $set: {
                                                                           otp: random_number,
                                                                           updatedOn: formatted_date
                                                                      }
                                                                 }
                                                            ).then((otpUpdated => {
                                                                 if (otpUpdated) {
                                                                      console.log("otpUpdated updated succcessfully---->416");
                                                                 }
                                                            })).catch(err => {
                                                                 console.log(err);
                                                                 reject(err);
                                                            })
                                                       }

                                                  })

                                             }
                                        })

                                   }
                              })
                              let query = "update users set otp='" + random_number + "',updated_at ='" + formatted_date + "'where user_id =" + userId + "&& is_active = 1";
                              db.query(query, {}, function (err, rows) {
                                   if (err) {
                                        console.log(err)
                                   } else {
                                        res = { message: "Please Confirm  OTP", status: 1 }
                                        resolve(res);
                                   }
                              })
                         } else {
                              //checking weather that user details are already exist or not based on user
                              userTemp.findOne({ mobile: mobile_number }, function (err, response) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                        res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                                   } else if (response != null) {
                                        //based on token send we were validating  token  
                                        userTemp.updateOne(
                                             {
                                                  mobile: mobile_number
                                             },
                                             {
                                                  $set: {
                                                       otp: random_number,
                                                       updatedOn: formatted_date
                                                  }
                                             }
                                        ).then((otpUpdated => {
                                             if (otpUpdated) {
                                                  console.log("Usertemp updated succcessfully");
                                             }
                                        })).catch(err => {
                                             console.log(err);
                                             reject(err);
                                        })
                                   } else {
                                        let query = "select mobile_no ,  otp ,legal_entity_type_id , status  from user_temp where mobile_no =" + mobile_number;
                                        db.query(query, {}, function (err, query_response) {
                                             if (err) {
                                                  console.log(err);
                                             } else if (Object.keys(query_response).length > 0) {
                                                  let body = {
                                                       mobile: query_response[0].mobile_no,
                                                       otp: query_response[0].otp,
                                                       legal_entity_type_id: query_response[0].legal_entity_type_id,
                                                       status: query_response[0].status,
                                                       createdOn: formatted_date
                                                  }
                                                  userTemp.create(body).then(inserted => {
                                                       //after inserting the records in validating customer_token or password_token
                                                       if (inserted) {
                                                            userTemp.updateOne(
                                                                 {
                                                                      mobile: mobile_number
                                                                 },
                                                                 {
                                                                      $set: {
                                                                           otp: random_number,
                                                                           updatedOn: formatted_date
                                                                      }
                                                                 }
                                                            ).then((otpUpdated => {
                                                                 if (otpUpdated) {
                                                                      console.log("UserTemp updated succcessfully ---- > 495");
                                                                 }
                                                            })).catch(err => {
                                                                 console.log(err);
                                                                 reject(err);
                                                            })
                                                       }

                                                  })

                                             }
                                        })

                                   }
                              })
                              let query = "update user_temp set otp='" + random_number + "',updated_at ='" + formatted_date + "'where mobile_no ='" + mobile_number + "'";
                              db.query(query, {}, function (err, rows) {
                                   if (err) {
                                        console.log(err)
                                   } else {
                                        res = { message: "Please Confirm  OTP", status: 1 }
                                        resolve(res);
                                   }
                              })
                         }

                    }


               }
          })
     } catch (err) {
          console.log(err)
     }
}

/*
Purpose : Used to get Email with count
author : Deepak Tiwari
Request : Require user email
Resposne : return email count .
*/
module.exports.getEmail = function (email) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(email)
               let data = "select count(email_id) as count from users where email_id =" + string;
               db.query(data, {}, function (err, mail) {
                    if (err) {
                         reject(err)
                    } else {

                         resolve(mail[0].count);
                    }
               })
          })
     } catch (err) {
          console.log(err)
     }

}


/*
Purpose :Used To checkUser with that phonenumber
author : Deepak Tiwari
Request : Require contact
Resposne : return the count of user_id match from database .
*/
module.exports.checkUser = function (contact) {
     try {
          let desc;
          let count;
          return new Promise((resolve, reject) => {
               let query = "select * from master_lookup as ml where ml.value  = 78002";
               db.query(query, {}, function (err, master) {
                    if (err) {
                         reject(err);
                    } else if (Object.keys(master).length > 0) {
                         desc = master[0].description;
                         let data = "select COUNT(us.user_id) as count from users as us left join legal_entities as le ON le.legal_entity_id = us.legal_entity_id where us.mobile_no =" + contact + "&& le.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id  = 3 && ml.is_active = 1)";
                         db.query(data, {}, function (err, row) {
                              if (err) {
                                   reject(err)
                              } else if (Object.keys(row).length > 0) {
                                   count = row[0].count;
                              }
                         })
                    }

               })
               resolve(count)
          })


     } catch (err) {
          console.log(err)
          let data = { status: "failed", message: "Unable to Process your request--2348" }
          return data;
     }

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



/*
Purpose :getMasterLookupValues function used to get masterlookup  From masterlookup table  .
author : Deepak Tiwari
Request : Require mas_cat_id.
Resposne : return masterlook up value .
*/
module.exports.getMasterLookupValues = function (mas_cat_id) {
     try {
          return new Promise((resolve, reject) => {
               let value = "select ml.master_lookup_name as name ,ml.value,ml.description from master_lookup as ml where ml.mas_cat_id =" + mas_cat_id + "&& ml.is_active = 1 ORDER BY ml.sort_order ASC";
               db.query(value, {}, function (err, master_lookup) {
                    if (err) {
                         reject(err);
                    } else if (Object.keys(master_lookup).length > 0) {
                         resolve(master_lookup);
                    }

               })

          })

     } catch (err) {
          console.log(err);
     }

}


/*
Purpose : Generate APPID-checkDeviceId  .
author : Deepak Tiwari
Request : Require decice id.
Resposne : return appid  , device id  , device details .
*/
module.exports.checkDeviceId = function (device_id) {
     let response = [];
     return new Promise((resolve, reject) => {
          let data = " select ded.device_id as device_id,ded.app_id as appId from device_details as ded where ded.device_id ='" + device_id + "'";
          db.query(data, {}, function (err, row) {
               if (err) {
                    reject(err)
               } else if (Object.keys(row).length > 0) {
                    response.push(row)
               }
          })
          resolve(response);
     })
}



/*
Purpose : To get check wether pincode is in servicable location or not  .
author : Deepak Tiwari
Request : Require pincode.
Resposne : return count of wh_id .
*/
function serviceablePincode(pincode) {
     return new Promise((resolve, reject) => {
          let data = "select count(le_wh_id) as count from wh_serviceables as whs where whs.pincode =" + pincode;
          db.query(data, {}, function (err, rows) {
               if (err) {
                    reject(err)
               } else if (Object.keys(rows).length > 0) {
                    resolve(rows)
               }
          })
     })

}


function getRefCode(prefix, stateId = '4033') {
     try {
          let response = '';
          return new Promise((resolve, reject) => {
               if (prefix != '') {
                    let data = "CALL reference_no('" + stateId + "', '" + prefix + "' )";
                    db.query(data, {}, function (err, refNoArr) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(refNoArr[0]).length > 0) {
                              let arr = JSON.parse(JSON.stringify(refNoArr[[0]]))
                              response = typeof arr[0].ref_no != 'undefined' ? arr[0].ref_no : '';
                              resolve(response);
                         } else {
                              resolve('');
                         }
                    })
               }

          })

     } catch (err) {
          console.log(err)
          return '';
     }
}


function getFFLeId(user_id) {
     try {
          return new Promise((resolve, reject) => {
               let data = "select legal_entity_id from users where user_id =" + user_id
               db.query(data, {}, function (err, leId) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(leId).length > 0) {
                         if (typeof leId[0].legal_entity_id != 'undefined' && leId[0].legal_entity_id != null) {
                         } else {
                              resolve('');
                         }
                         resolve(leId[0].legal_entity_id);
                    } else {
                         resolve('');
                    }

               })

          })
     } catch (err) {
          console.log(err)
     }

}

function checkUser(contact) {
     try {
          let desc;
          return new Promise((resolve, reject) => {
               let query = "select * from master_lookup as ml where ml.value  = 78002";
               db.query(query, {}, function (err, master) {
                    if (err) {
                         reject(err);
                    } else if (Object.keys(master).length > 0) {
                         desc = master[0].description;
                         let data = "select COUNT(us.user_id) as count from users as us left join legal_entities as le ON le.legal_entity_id = us.legal_entity_id where us.mobile_no =" + contact + "&& le.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id  = " + desc + "&& ml.is_active = 1)";
                         db.query(data, {}, function (err, row) {
                              if (err) {
                                   reject(err)
                              } else if (Object.keys(row).length > 0) {
                                   resolve(row[0].count);
                              } else {
                                   resolve(0);
                              }
                         })
                    }

               })

          })


     } catch (err) {
          console.log(err)
          let data = { status: "failed", message: "Unable to Process your request--2540" }
          return data;
     }

}


/*
* Function name: getWarehouseid
* Description: Used to get warehouse_id
*/
function getWarehouseid(pincode, getLegalEntityID = false) {
     let response = {};
     return new Promise((resolve, reject) => {
          let query = "select ws.le_wh_id as le_wh_id , ws.legal_entity_id from wh_serviceables as ws JOIN legalentity_warehouses as lew ON ws.le_wh_id= lew.le_wh_id where ws.pincode =" + pincode + "&&lew.dc_type=118001 && lew.status=1";
          db.query(query, {}, function (err, le_wh) {
               if (err) {
                    reject(err)
               } else if (Object.keys(le_wh).length > 0) {
                    if (le_wh[0] != null) {
                         // If the API wants legal Entity Id, then its set to 1(true)
                         if (getLegalEntityID) {
                              response = { le_wh_id: le_wh[0].le_wh_id, legal_entity_id: le_wh[0].legal_entity_id }
                              resolve(response);
                         } else {
                              // If its only warehouse Id
                              resolve(le_wh[0].le_wh_id);
                         }
                    } else {
                         return '';
                    }
               }
          })
     })

}


//used to fetch parent_le_id from legal_entity table based on legal_Entity_id
function getParentIdFromLegalEntity(legal_entity_id) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select parent_le_id from legal_entities where legal_entity_id =" + legal_entity_id;
               db.query(query, {}, function (err, parent) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(parent[0].parent_le_id);

                    }

               })

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

/*
Purpose : updateFlatTable function used to update retailer table .
author : Deepak Tiwari
Request : Require legalEntityId.
Resposne : update retailer table .
*/
function updateFlatTable(legalEntityId, parent_le_id = null) {
     try {
          let aadhar_id = [];
          let beatId = 0;
          let hubId = 0;
          let spokeId = 0;
          let formatted_date;
          let formattedDate;
          let formatted;
          let string;
          if (legalEntityId > 0) {
               let string = JSON.stringify(legalEntityId);
               // pool.getConnection(function (err, con) {
               con.beginTransaction(function (err) {
                    if (err) {
                         console.log(err)
                         con.rollback(function (err) {
                              console.log(err);
                         });
                    }
                    let data = "select le_code from retailer_flat where legal_entity_id =" + string
                    db.query(data, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                         } else {
                              let legalEntityDetails = result[0];
                              let Query = "select aadhar_id , mobile_no from users where legal_entity_id=" + string;
                              db.query(Query, {}, function (err, aadhar) {
                                   if (err) {
                                        console.log(err);
                                   } else {
                                        aadhar_id = aadhar[0];
                                        let Query1 = 'Call getLegalEntitiesDataById(0,0,' + legalEntityId + ')';
                                        db.query(Query1, {}, function (err, row) {
                                             if (err) {
                                                  console.log(err);
                                                  con.rollback(function (err) {
                                                       console.log(err);
                                                  });
                                             } else if (Object.keys(row).length > 0) {
                                                  let response = [];
                                                  if (parent_le_id != null) {
                                                       let parent_le_id = parent_le_id;
                                                       response.push(parent_le_id);
                                                  }
                                                  if (row != null) {
                                                       response.push(row);
                                                       response = typeof response[0] != 'undefined' ? response[0] : response;
                                                       if (parent_le_id == null) {
                                                            let string = response[0];
                                                            //used to get parentLegal entityID
                                                            getParentIdFromLegalEntity(string[0].legal_entity_id).then(parent_le => {
                                                                 response.parent_le_id = parent_le;
                                                                 let aadharid = aadhar_id.aadhar_id;
                                                                 let mobileno = aadhar_id.mobile_no;
                                                                 response.push(aadharid);
                                                                 response.push(mobileno);
                                                                 let string = response[0];
                                                                 if (legalEntityDetails != null) {
                                                                      let legalEntityId = string[0].legal_entity_id;
                                                                      if (typeof string[0].legal_entity_id != 'undefined') {
                                                                           delete string[0].legal_entity_id;
                                                                      }
                                                                      if (typeof string[0].le_code != 'undefined') {
                                                                           delete string[0].le_code;
                                                                      }
                                                                      if (typeof string[0].hub_id != 'undefined' && string[0].hub_id == 0) {
                                                                           beatId = typeof string[0].beat_id != 'undefined' ? string[0].beat_id : 0;
                                                                           //   if (beatId > 0) {
                                                                           let data_1 = "select pjp_pincode_area.spoke_id,pjp_pincode_area.le_wh_id from pjp_pincode_area LEFT JOIN spokes ON spokes.spoke_id = pjp_pincode_area.spoke_id where pjp_pincode_area_id =" + beatid;
                                                                           db.query(data_1, {}, function (err, pincode) {
                                                                                if (err) {
                                                                                     console.log(err);
                                                                                } else if (Object.keys(pincode).length > 0) {
                                                                                     let hubDetail = JSON.parse(JSON.stringify(pincode[0]));
                                                                                     hubId = hubDetail.hasOwnProperty('le_wh_id') ? hubDetails[0].le_wh_id : 0;
                                                                                     spokeId = hubDetail.hasOwnProperty('spoke_id') ? hubDetails[0].spoke_id : 0;
                                                                                     if (hubId > 0 && spokeId > 0) {
                                                                                          string[0].hub_id = hubId;
                                                                                          string[0].spoke_id = spokeId;
                                                                                          if (beatId > 0) {
                                                                                               let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legalEntityId;
                                                                                               db.query(customer, {}, function (err, customers) {
                                                                                                    if (err) {
                                                                                                         console.log(err)
                                                                                                         con.rollback(function (err) {
                                                                                                              console.log(err);
                                                                                                         });
                                                                                                    } else if (Object.keys(customers).length > 0) {
                                                                                                         console.log("=====>2726 customer table updated successfully")
                                                                                                    }
                                                                                               })
                                                                                          }
                                                                                     }

                                                                                }
                                                                                // })

                                                                                //     }
                                                                                //updating retailer flat
                                                                                delete string[0].sms_notification;
                                                                                let current_datetime = string[0].last_order_date_old;
                                                                                let curretDate = string[0].created_at;
                                                                                let currDate = string[0].updated_at;
                                                                                formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
                                                                                formatted = curretDate.getFullYear() + "-" + (curretDate.getMonth() + 1) + "-" + curretDate.getDate() + " " + curretDate.getHours() + ":" + curretDate.getMinutes() + ":" + curretDate.getSeconds();
                                                                                formattedDate = currDate.getFullYear() + "-" + (currDate.getMonth() + 1) + "-" + currDate.getDate() + " " + currDate.getHours() + ":" + currDate.getMinutes() + ":" + currDate.getSeconds();
                                                                                let retailer = " UPDATE retailer_flat SET parent_le_id ='" + parent_le + "' , business_legal_name = '" + string[0].business_legal_name + "',legal_entity_type_id='" + string[0].legal_entity_type_id + "',business_type_id ='" + string[0].business_type_id + "' ,name ='" + string[0].name + "', mobile_no = '" + string[0].mobile_no + " ', volume_class_id = '" + string[0].volume_class_id + "', volume_class = '" + string[0].volume_class + "', No_of_shutters = '" + string[0].No_of_shutters + "', suppliers = '" + string[0].suppliers + "', business_start_time = '" + string[0].business_start_time + "', business_end_time = '" + string[0].business_end_time + "', address = '" + string[0].address + "', address1 = '" + string[0].address1 + "', address2 = '" + string[0].address2 + "', area_id = '" + string[0].area_id + "',AREA = '" + string[0].area + "',hub_id = '" + string[0].hub_id + "', beat_id = '" + string[0].beat_id + "', spoke_id = '" + string[0].spoke_id + "', beat = '" + string[0].beat + "', city = '" + string[0].city + "', state_id = '" + string[0].state_id + "', state = '" + string[0].state + "', country = '" + string[0].country + "', locality = '" + string[0].locality + "', landmark = '" + string[0].lankmark + "', pincode = '" + string[0].pincode + "' , smartphone = '" + string[0].smartphone + "', network = '" + string[0].network + "', master_manf = '" + string[0].master_manf + "', orders_old = '" + string[0].orders_old + "', last_order_date_old = '" + formatted_date + "', beat_rm_name = '" + string[0].beat_rm_name + "',created_by = '" + string[0].created_by + "', created_at = '" + formatted + "', created_time = '" + string[0].created_time + "', updated_by = '" + string[0].updated_by + "', updated_at = '" + formattedDate + "', updated_time = '" + string[0].updated_time + "', latitude = '" + string[0].latitude + "', longitude = '" + string[0].longitude + "',is_icecream = ' " + string[0].is_icecream + "', is_milk = '" + string[0].is_milk + "', is_deepfreezer = '" + string[0].is_deepfreezer + "', is_fridge = '" + string[0].is_fridge + "', is_vegetables = '" + string[0].is_vegetables + "', is_visicooler = '" + string[0].is_visicooler + "',dist_not_serv = '" + string[0].dist_not_serv + "', facilities = " + string[0].facilities + " , is_swipe = " + string[0].is_swipe + " WHERE legal_entity_id =" + legalEntityId;
                                                                                db.query(retailer, {}, function (err, retailers) {
                                                                                     if (err) {
                                                                                          console.log(err);
                                                                                          con.rollback(function (err) {
                                                                                               console.log(err);
                                                                                          });
                                                                                     } else if (Object.keys(retailers).length > 0) {
                                                                                          console.log("retailer flat updated1")
                                                                                     }
                                                                                })
                                                                                //commit code
                                                                                con.commit(function (err) {
                                                                                     if (err) {
                                                                                          con.rollback(function () {
                                                                                               console.log(err);
                                                                                          });
                                                                                     } else {
                                                                                          console.log("Transaction close")
                                                                                     }

                                                                                });

                                                                           })
                                                                      }

                                                                 } else {
                                                                      if (typeof string[0].hub_id != 'undefined' && string[0].hub_id == 0) {
                                                                           beatId = typeof string[0].beat_id != 'undefined' ? string[0].beat_id : 0;
                                                                           //   if (beatId) {
                                                                           let Querys = "select pjp_pincode_area.spoke_id ,pjp_pincode_area.le_wh_id from pjp_pincode_area  LEFT JOIN  spokes ON spokes.spoke_id = pjp_pincode_area.spoke_id where pjp_pincode_area_id=" + beatId;
                                                                           db.query(Querys, {}, function (err, hubDetails) {
                                                                                if (err) {
                                                                                     console.log(err);
                                                                                } else if (Object.keys(hubDetails[0]).length > 0) {
                                                                                     // console.log("hubDetails", hubDetails)
                                                                                     let hubDetail = JSON.parse(JSON.stringify(hubDetails[0]));
                                                                                     hubId = hubDetail.hasOwnProperty('le_wh_id') ? hubDetails[0].le_wh_id : 0;
                                                                                     spokeId = hubDetail.hasOwnProperty('spoke_id') ? hubDetails[0].spoke_id : 0;
                                                                                     if (hubId > 0 && spokeId > 0) {
                                                                                          string[0].hub_id = hubId;
                                                                                          string[0].spoke_id = spokeId;
                                                                                          if (beatId > 0) {
                                                                                               let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legalEntityId;
                                                                                               db.query(customer, {}, function (err, customers) {
                                                                                                    if (err) {
                                                                                                         console.log(err);
                                                                                                         con.rollback(function (err) {
                                                                                                              console.log(err);
                                                                                                         });
                                                                                                    } else if (Object.keys(customers).length > 0) {
                                                                                                         console.log("customer updated successfully")
                                                                                                    }
                                                                                               })
                                                                                          }
                                                                                     }
                                                                                }
                                                                                // })
                                                                                //  }
                                                                                delete string[0].sms_notification;
                                                                                let current_datetime = new Date(string[0].last_order_date_old);
                                                                                let curretDate = new Date(string[0].created_at);
                                                                                let currDate = new Date(string[0].updated_at);
                                                                                formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
                                                                                formatted = curretDate.getFullYear() + "-" + (curretDate.getMonth() + 1) + "-" + curretDate.getDate() + " " + curretDate.getHours() + ":" + curretDate.getMinutes() + ":" + curretDate.getSeconds();
                                                                                formattedDate = currDate.getFullYear() + "-" + (currDate.getMonth() + 1) + "-" + currDate.getDate() + " " + currDate.getHours() + ":" + currDate.getMinutes() + ":" + currDate.getSeconds();
                                                                                let retailer = "insert into retailer_flat (parent_le_id ,legal_entity_id , le_code , business_legal_name  , legal_entity_type_id , business_type_id ,name , mobile_no , volume_class_id , volume_class , No_of_shutters , suppliers, business_start_time , business_end_time , address , address1 , address2 , area_id , AREA  , hub_id , beat_id , spoke_id , beat , city , state_id , state , country , locality , landmark , pincode , smartphone , network , master_manf , orders_old , last_order_date_old , beat_rm_name , created_by , created_at , created_time , updated_by , updated_At , updated_time , latitude , longitude , is_icecream , is_milk , is_deepfreezer , is_fridge , is_vegetables , is_visicooler , dist_not_serv , facilities  , legal_entity_type , business_type , is_swipe) values (" + parent_le + "," + string[0].legal_entity_id + ",'" +
                                                                                     string[0].le_code + "','" + string[0].business_legal_name + "'," + string[0].legal_entity_type_id + "," + string[0].business_type_id + ",'" + string[0].name + "'," + string[0].mobile_no + "," + string[0].volume_class_id + ", '" + string[0].volume_class + "'," + string[0].No_of_shutters + ", '" + string[0].suppliers + "','" + string[0].business_start_time + "','" + string[0].business_end_time + "','" + string[0].address + "','" + string[0].address1 + "','" + string[0].address2 + "'," + string[0].area_id + ",'" + string[0].area + "'," + string[0].hub_id + "," + string[0].beat_id + "," + string[0].spoke_id + ",'" + string[0].beat + "','" + string[0].city + "'," + string[0].state_id + ",'" + string[0].state + "','" + string[0].country + "','" + string[0].locality + "','" + string[0].landmark + "'," + string[0].pincode + ",'" + string[0].smartphone + "','" + string[0].network + "','" + string[0].master_manf + "'," + string[0].orders_old + ",'" + formatted_date + "','" + string[0].beat_rm_name + "','" + string[0].created_by + "','" + formatted + "','" + string[0].created_time + "','" + string[0].updated_by + "','" + formattedDate + "','" + string[0].updated_time + "','" + string[0].latitude + "','" + string[0].longitude + "'," + string[0].is_icecream + "," + string[0].is_milk + "," + string[0].is_deepfreezer + "," + string[0].is_fridge + "," + string[0].is_vegetables + "," + string[0].is_visicooler + ",'" + string[0].dist_not_serv + "'," + string[0].facilities + ",'" + string[0].legal_entity_type + "','" + string[0].business_type + "'," + string[0].is_swipe + ")";
                                                                                db.query(retailer, {}, function (err, retailers) {
                                                                                     if (err) {
                                                                                          console.log(err);
                                                                                          con.rollback(function (err) {
                                                                                               console.log(err);
                                                                                          });
                                                                                     } else if (Object.keys(retailers).length > 0) {
                                                                                          console.log("updated retailer flat")
                                                                                     }
                                                                                })
                                                                                // }
                                                                                //commit code
                                                                                con.commit(function (err) {
                                                                                     if (err) {
                                                                                          con.rollback(function () {
                                                                                               console.log(err);
                                                                                          });
                                                                                     } else {
                                                                                          console.log("Transaction close")
                                                                                     }

                                                                                });
                                                                           })

                                                                           //   }
                                                                      }
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 con.rollback(function (err) {
                                                                      console.log(err);
                                                                 });
                                                            })
                                                       }
                                                  }


                                             }

                                        })
                                   }
                              })
                              // con.commit(function (err) {
                              //      if (err) {
                              //           con.rollback(function () {
                              //                console.log(err);
                              //           });
                              //      } else {
                              //           console.log("Transaction close")
                              //      }

                              // });

                         }
                    })


               })



               //  })
          }
     } catch (err) {
          console.log(err);
     }
}
/*
Purpose : Used to get role id for customers
author : Deepak Tiwari
Request : not require 
Resposne : Return role id.
*/
function getRoleId() {
     return new Promise((resolve, reject) => {
          let query = "select DISTINCT ler.role_id from legal_entity_roles as ler  where ler.le_type_id like '%30%'";
          db.query(query, {}, function (err, result) {
               if (err) {
                    reject(err)
               } else if (Object.keys(result).length > 0) {
                    resolve(result[0].role_id)
               } else {
                    resolve('');
               }
          })
     })
}

//used to get legalentityId used on customer_token
function getLegalEntityID(customer_token) {
     return new Promise((resolve, reject) => {
          try {
               let response = [];
               let query_1 = "select user_id , legal_entity_id from users where password_token =" + customer_token;
               db.query(query_1, {}, function (err, result) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         // console.log("====>2866", result[0])
                         response.push(result)
                         resolve(result)
                    } else {
                         resolve('')
                    }
               })

          } catch (err) {
               console.log(err)
          }
     })

}
// //used to get warehouse based on beat
// function getWhFromBeat(beat) {
//      return new Promise((resolve, reject) => {
//           try {
//                let query_1 = "select d.dc_id FROM pjp_pincode_area p INNER JOIN dc_hub_mapping d ON p.`le_wh_id` = d.hub_id WHERE p.pjp_pincode_area_id=" + beat;
//                db.query(query_1, {}, function (err, result) {
//                     if (err) {
//                          reject(err)
//                     } else if (Object.keys(result).length > 0) {
//                          resolve(result[0].dc_id)
//                     } else {
//                          resolve(0)
//                     }
//                })

//           } catch (err) {
//                console.log(err)
//           }
//      })

// }
//used to getWarehouse based on legal entity id.
function getWarehouseFromLeId(id) {
     return new Promise((resolve, reject) => {
          try {
               let query_1 = "select le_wh_id from legalentity_warehouses where dc_type  =  118001 && legal_entity_id =" + id;
               db.query(query_1, {}, function (err, result) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         resolve(result[0].le_wh_id)
                    } else {
                         resolve('')
                    }
               })

          } catch (err) {
               console.log(err)
          }
     })
}

//used to get parent legalID based on ffId
function getParentLeIdFromFFId(id) {
     return new Promise((resolve, reject) => {
          try {
               let query_1 = "select legal_entity_id from users where user_id =" + id;
               db.query(query_1, {}, function (err, result) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         console.log("result===>2930", result[0].legal_entity_id)
                         resolve(result[0].legal_entity_id)
                    } else {
                         resolve('')
                    }
               })

          } catch (err) {
               console.log(err)
          }
     })
}

/*
Purpose : Add address function is used to add the address to regirstration process third step. fillable variable is difined to store the data fields into the database
author : Deepak Tiwari
Request : Requirebusiness_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email, mobile_no, filepath1, filepath2, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1, bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1,contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat, customer_type,.
Resposne : Update Address.
*/
module.exports.address = function (business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email, mobile_no, filepath1, filepath2, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1, bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat, customer_type, gstin, arn_number, is_icecream, sms_notification, is_milk = 0, is_fridge = 0, is_vegetables = 0, is_visicooler = 0, dist_not_serv = '', facilities = 0, is_deepfreezer = 0, is_swipe, aadhar_id = '', credit_limit = 0, mfc = '', pan_number = '', website_url = '', logo = '') {
     try {

          return new Promise((resolve, reject) => {
               // pool.getConnection(function (err, con) {
               //      if (err) {
               //           console.log("======>connection error 2744", err)
               //      }
               con.beginTransaction(function (err) {
                    if (err) {
                         console.log("=========>transaction error", err)
                    }
                    let query = [];
                    let otp;
                    let res = {};
                    let desc;
                    let ff_uid = 0;
                    let ff_le_id;
                    let is_approved;
                    let status;
                    let le_code;
                    let parent_le_id = 0;
                    let last_insert_legal_id;
                    let email_id;
                    let customer_token;
                    let last_insert_user_id;
                    let area_chk_id;
                    let state_name = [];
                    let last_insert_city_id;
                    let last_insert_doc_id;
                    let last_insert_userpref_id;
                    let last_insert_fflog_id;
                    let last_insert_role_id;
                    let users = [];
                    let legal = [];
                    let role_id;
                    let mobile_feature;
                    let last_insert_user1_id = '';
                    let last_insert_user2_id = '';
                    let area_chk = [];
                    let le_wh_id;
                    let hub;
                    let current_datetime = new Date();
                    let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                    let is_Swipe;
                    if (typeof is_swipe != 'undefined') {
                         is_Swipe = is_swipe;
                    } else {
                         is_Swipe = 0;
                    }

                    let data = "select mobile_no,status,otp,legal_entity_type_id from user_temp where mobile_no =" + mobile_no
                    db.query(data, {}, function (err, rows) {
                         if (err) {
                              console.log(err)
                              con.rollback(function (err) {
                                   reject(err);
                              });
                         } else if (Object.keys(rows).length > 0) {
                              //used fetch otp from user temp
                              otp = (typeof rows[0].otp != 'undefined' && rows[0].otp != null) ? rows[0].otp : 0;
                              if (sales_token != '') {
                                   console.log("ff registerting user")
                                   //ff registering the retailer
                                   let string = JSON.stringify(sales_token)
                                   //fetching ff le_entity_id
                                   getLegalEntityID(string).then((res) => {
                                        if (res.length > 0) {
                                             ff_uid = res[0].user_id;
                                             ff_le_id = typeof res[0].legal_entity_id != 'undefined' ? res[0].legal_entity_id : null;
                                        } else {
                                             ff_uid = '';
                                             ff_le_id = null;
                                        }
                                        //serviceable code
                                        is_approved = 1;
                                        status = 1;
                                        //validating weather selected state is serviceable state or not 
                                        let statename = "select name from zone where zone_id =" + state_id;
                                        db.query(statename, {}, function (err, stateName) {
                                             console.log("=====>2931", statename);
                                             if (err) {
                                                  console.log(err);
                                                  reject(err);
                                             } else if (Object.keys(stateName).length > 0) {
                                                  let pincode_query = "select count(*)  as count from cities_pincodes where pincode=" + pincode + " && state = '" + stateName[0].name + "'";
                                                  db.query(pincode_query, {}, function (err, pincodeStateChk) {
                                                       console.log("===>2937", pincodeStateChk[0].count)
                                                       if (err) {
                                                            console.log(err);
                                                            reject(err);
                                                       } else if (pincodeStateChk[0].count <= 0) {
                                                            is_approved = 0;
                                                            status = 0;
                                                            resolve({ 'status': "failed", 'message': "Please select correct state" });
                                                       } else {
                                                            let query_2 = "select description from master_lookup  where value = 78001";
                                                            db.query(query_2, {}, function (err, legal_entitycompany_type) {

                                                                 if (err) {
                                                                      console.log(err)
                                                                      con.rollback(function (err) {
                                                                           reject(err);
                                                                      });
                                                                 } else if (Object.keys(legal_entitycompany_type).length > 0) {
                                                                      getRefCode('CU', state_id).then(result => {
                                                                           if (result.length > 0) {
                                                                                le_code = result;
                                                                                if (ff_uid != '') {
                                                                                     getParentLeIdFromFFId(ff_uid).then(parent_id => {
                                                                                          console.log("heelo ==>2690")
                                                                                          if (parent_id) {
                                                                                               parent_le_id = parent_id;
                                                                                          } else {
                                                                                               parent_le_id = 0;
                                                                                          }
                                                                                          console.log("parent_legal_id", parent_le_id);
                                                                                          //inserting user details into legal_entities table
                                                                                          let data_5 = "insert into  legal_entities ( business_legal_name ,address1 , legal_entity_type_id , address2 , locality , landmark ,tin_number ,country , state_id , is_approved, city, pincode, business_type_id, latitude, longitude, le_code, parent_id, created_by, gstin, arn_number, created_at, parent_le_id ,pan_number , website_url  , logo) VALUE ('" + business_legal_name + "','" + address1 + "','" + customer_type + "','" + address2 + "','" + locality + "','" + landmark + "','" + tin_number + "'," + 99 + ",'" + state_id + "','" + is_approved + "','" + city + "','" + pincode + "','" + segment_id + "','" + latitude + "','" + longitude + "','" + le_code + "','" + legal_entitycompany_type[0].description + "'," + ff_uid + ",'" + gstin + "','" + arn_number + "','" + formatted_date + "','" + parent_le_id + "','" + pan_number + "','" + website_url + "' ,'" + logo + "')";
                                                                                          db.query(data_5, {}, function (err, inserted) {
                                                                                               if (err) {
                                                                                                    console.log(err)
                                                                                                    con.rollback(function (err) {
                                                                                                         reject(err)
                                                                                                    })
                                                                                               } else {
                                                                                                    last_insert_legal_id = inserted.insertId;
                                                                                                    if (email != '') {
                                                                                                         email_id = email;
                                                                                                    } else {
                                                                                                         email_id = mobile_no + '@nomail.com';
                                                                                                    }

                                                                                                    customer_token = crypto.createHash('md5').update(mobile_no).digest("hex");
                                                                                                    //Insert data into user table
                                                                                                    let query_5 = "insert into users (firstname , lastname , email_id , mobile_no , profile_picture , otp , password_token , legal_entity_id , is_active , is_parent , aadhar_id , created_by  ,  created_at ,password ) values ('" + firstname + "','" + lastname + "','" + email_id + "','" + mobile_no + "','" + filepath2 + "','" + otp + "','" + customer_token + "','" + last_insert_legal_id + "','" + status + "'," + 1 + ",'" + aadhar_id + "'," + ff_uid + ",'" + formatted_date + "','" + "')";
                                                                                                    db.query(query_5, {}, function (err, insert) {
                                                                                                         if (err) {
                                                                                                              console.log("error", err);
                                                                                                              resolve({ 'status': "failed", 'message': "We cannot add or update parent details of users" });
                                                                                                              con.rollback(function (err) {
                                                                                                                   reject(err)
                                                                                                              })
                                                                                                         } else {
                                                                                                              last_insert_user_id = insert.insertId;
                                                                                                              if (ff_uid == null) {
                                                                                                                   ff_uid = last_insert_user_id;
                                                                                                              }

                                                                                                              if (sales_token == null) {
                                                                                                                   //self registration
                                                                                                                   let data1 = "update users as us set created_by ='" + ff_uid + "', updated_at ='" + formatted_date + "', us.user_id =" + last_insert_user_id + "  where us.user_id =" + last_insert_user_id;
                                                                                                                   db.query(data1, {}, function (err, update) {
                                                                                                                        if (err) {
                                                                                                                             console.log(err)
                                                                                                                             con.rollback(function (err) {
                                                                                                                                  reject(err)
                                                                                                                             })
                                                                                                                        } else {
                                                                                                                             console.log("1.updated")
                                                                                                                        }
                                                                                                                   })
                                                                                                                   let data_6 = "update legal_entities as le  set created_by = '" + ff_uid + "',updated_at = '" + formatted_date + "' where le.legal_entity_id = " + last_insert_legal_id;
                                                                                                                   db.query(data_6, {}, function (err, update) {
                                                                                                                        if (err) {
                                                                                                                             console.log(err)
                                                                                                                             con.rollback(function () {
                                                                                                                                  reject(err)
                                                                                                                             })
                                                                                                                        } else {
                                                                                                                             console.log("2.updated")
                                                                                                                        }
                                                                                                                   })
                                                                                                              }

                                                                                                              if (typeof contact_no1 != 'undefined' && contact_no1 != "") {
                                                                                                                   //check weather that perticular user is exist or not 
                                                                                                                   checkUser(contact_no1).then((chk_user1) => {
                                                                                                                        if (chk_user1 >= 1) {
                                                                                                                             let delete_1 = "delete from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                                             db.query(delete_1, {}, function (err, deteted) {
                                                                                                                                  console.log(delete_1)
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log("delete in legal_entity table");
                                                                                                                                  }
                                                                                                                             })

                                                                                                                             let delete_2 = "delete from users where user_id =" + last_insert_user_id;
                                                                                                                             db.query(delete_2, {}, function (err, deteted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log("delete in users table");
                                                                                                                                  }
                                                                                                                             })


                                                                                                                             let delete_3 = "delete from legalentity_warehouses where legal_entity_id =" + last_insert_legal_id;
                                                                                                                             db.query(delete_3, {}, function (err, deteted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log(" 2. delete in legalentity_warehouses");
                                                                                                                                  }
                                                                                                                             })

                                                                                                                             let result = { message: "ContactNumber already exists  " + contact_no1, status: 'failed ' };
                                                                                                                             resolve(result);

                                                                                                                        } else if (contact_name1 != '') {
                                                                                                                             //Insert data into user table
                                                                                                                             let insert_query = "insert into users (firstname  , email_id , mobile_no , profile_picture , otp , legal_entity_id , is_active , created_by ,created_at,password ,lastname) values ('" + contact_name1 + "','" + contact_no1 + '@nomail.com' + "','" + contact_no1 + "','" + filepath2 + "'," + otp + ',' + last_insert_legal_id + ',' + status + ',' + ff_uid + ",'" + formatted_date + "','" + "','" + "')";
                                                                                                                             db.query(insert_query, {}, function (err, inserted) {
                                                                                                                                  //console.log("insert_query", insert_query)
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       last_insert_user1_id = inserted.insertId;
                                                                                                                                  }
                                                                                                                             })
                                                                                                                        }
                                                                                                                   }).catch(err => {
                                                                                                                        console.log(err)
                                                                                                                   })

                                                                                                              }

                                                                                                              if (typeof contact_no2 != 'undefined' && contact_no2 != "") {
                                                                                                                   checkUser(contact_no2).then((chk_user2) => {
                                                                                                                        if (chk_user2 >= 1) {
                                                                                                                             let delete_1 = "delete from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                                             db.query(delete_1, {}, function (err, deteted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log("delete in legal_entity table");
                                                                                                                                  }
                                                                                                                             })

                                                                                                                             let delete_2 = "delete from users where user_id =" + last_insert_user_id;
                                                                                                                             db.query(delete_2, {}, function (err, deteted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log("delete in users table");
                                                                                                                                  }
                                                                                                                             })


                                                                                                                             let delete_3 = "delete from legalentity_warehouses where legal_entity_id =" + last_insert_legal_id;
                                                                                                                             db.query(delete_3, {}, function (err, deteted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log(" 2. delete in legalentity_warehouses");
                                                                                                                                  }
                                                                                                                             })

                                                                                                                             if (last_insert_user1_id != null) {
                                                                                                                                  let delete_4 = "delete from users where user_id =" + last_insert_user1_id;
                                                                                                                                  db.query(delete_4, {}, function (err, deteted) {
                                                                                                                                       if (err) {
                                                                                                                                            console.log(err)
                                                                                                                                            con.rollback(function (err) {
                                                                                                                                                 reject(err)
                                                                                                                                            })
                                                                                                                                       } else {
                                                                                                                                            console.log("delete in users table");
                                                                                                                                       }
                                                                                                                                  })
                                                                                                                             }

                                                                                                                             let result = { message: "ContactNumber already exists" + contact_no2, status: 'failed ' };
                                                                                                                             resolve(result);
                                                                                                                        } else if (contact_name2 != "") {
                                                                                                                             //Insert data into user table
                                                                                                                             let insert = "insert into users (firstname  , email_id , mobile_no , profile_picture , otp , legal_entity_id , is_active , created_by ,created_at,password ,lastname) values ('" + contact_name2 + "','" + contact_no2 + '@nomail.com' + "','" + contact_no2 + "','" + filepath2 + "'," + otp + ',' + last_insert_legal_id + ',' + status + ',' + ff_uid + ",'" + formatted_date + "','" + "','" + "')";
                                                                                                                             db.query(insert, {}, function (err, inserted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       last_insert_user2_id = inserted.insertId;
                                                                                                                                  }
                                                                                                                             })
                                                                                                                        }
                                                                                                                   }).catch(err => {
                                                                                                                        console.log(err)
                                                                                                                   })
                                                                                                              }
                                                                                                         }
                                                                                                    })

                                                                                                    //we have to add
                                                                                                    let query_7 = "select cp.city_id from cities_pincodes as cp  where cp.pincode ='" + pincode + "' && cp.officename  ='" + area + "' && cp.city ='" + city + "'";
                                                                                                    db.query(query_7, {}, function (err, res) {
                                                                                                         if (err) {
                                                                                                              console.log(err)
                                                                                                              con.rollback(function (err) {
                                                                                                                   reject(err)
                                                                                                              })
                                                                                                         } else {
                                                                                                              area_chk.push(res);
                                                                                                              ///used to get warehouse
                                                                                                              if (ff_uid && ff_uid != '') {
                                                                                                                   getWarehouseFromLeId(parent_le_id).then(le_wh => {
                                                                                                                        if (le_wh == null) {
                                                                                                                             le_wh_id = '';
                                                                                                                        } else {
                                                                                                                             le_wh_id = le_wh;
                                                                                                                        }
                                                                                                                   }).catch(err => {
                                                                                                                        console.log(err);
                                                                                                                   })
                                                                                                              } else if (beat && beat != '') {
                                                                                                                   getWhFromBeat(beat).then(le_wh => {
                                                                                                                        if (le_wh == null) {
                                                                                                                             le_wh_id = '';
                                                                                                                        } else {
                                                                                                                             le_wh_id = le_wh;
                                                                                                                        }
                                                                                                                   }).catch(err => {
                                                                                                                        console.log(err);
                                                                                                                   })
                                                                                                              } else {
                                                                                                                   le_wh_id = '';
                                                                                                              }
                                                                                                              // getWarehouseid(pincode).then(le_wh => {
                                                                                                              //      if (le_wh == null) {
                                                                                                              //           le_wh_id = '';
                                                                                                              //      } else {
                                                                                                              //           le_wh_id = le_wh;

                                                                                                              //      }
                                                                                                              // }).catch((err) => {
                                                                                                              //      console.log(err)
                                                                                                              // })

                                                                                                              //used to get hub
                                                                                                              getHub(beat).then(beat_id => {
                                                                                                                   if (beat_id == null) {
                                                                                                                        hub = '';
                                                                                                                   } else {
                                                                                                                        hub = beat_id;
                                                                                                                   }

                                                                                                              }).catch((err) => {
                                                                                                                   console.log(err)
                                                                                                              })

                                                                                                              console.log("is_swipe===>3324", is_Swipe, is_swipe)
                                                                                                              if (area_chk[0].length > 0) {
                                                                                                                   let area1 = JSON.parse(JSON.stringify(area_chk[[0]]));
                                                                                                                   //Insert data into customers
                                                                                                                   let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer,is_swipe ) values (" + last_insert_legal_id + ',' +
                                                                                                                        volume_class + ',' + noof_shutters + ',' + area1[0].city_id + ",'" + master_manf + "'," + smartphone + ",'" + network + "','" + ff_uid + "','" + beat + "','" + formatted_date + "'," + is_icecream + ',' +
                                                                                                                        is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";

                                                                                                                   db.query(insert_user, {}, function (err, area_id) {
                                                                                                                        if (err) {
                                                                                                                             console.log(err)
                                                                                                                             con.rollback(function (err) {
                                                                                                                                  reject(err)
                                                                                                                             })
                                                                                                                        } else {

                                                                                                                             area_chk_id = area_id.insertId;
                                                                                                                        }
                                                                                                                   })
                                                                                                              } else {
                                                                                                                   let state_query = "select name from zone where zone_id = " + state_id;
                                                                                                                   db.query(state_query, {}, function (err, res) {
                                                                                                                        if (err) {
                                                                                                                             console.log(err)
                                                                                                                             con.rollback(function (err) {
                                                                                                                                  reject(err)
                                                                                                                             })
                                                                                                                        } else if (Object.keys(res).length > 0) {
                                                                                                                             state_name = res;
                                                                                                                        } else {
                                                                                                                             let ros = { 'status': 'failed', 'message': 'Unable to process your request -2' }
                                                                                                                        }

                                                                                                                        if (state_name.length != 0) {
                                                                                                                             let insert_city = "insert into cities_pincodes (country_id ,pincode , city , state , officename) values(" + 99 + ',' + pincode + ",'" + city + "','" + state_name[0].name + "','" + area + "')";
                                                                                                                             db.query(insert_city, {}, function (err, inserted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       last_insert_city_id = inserted.insertId
                                                                                                                                  }
                                                                                                                                  console.log("is_swipe===>3368", is_Swipe, is_swipe)
                                                                                                                                  //Insert data into customers
                                                                                                                                  let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer, is_swipe ) values (" + last_insert_legal_id + ',' +
                                                                                                                                       volume_class + ',' + noof_shutters + ',' + last_insert_city_id + ",'" + master_manf + "'," + smartphone + ',' + network + ',' + ff_uid + ',' + beat + ",'" + formatted_date + "'," + is_icecream + ',' +
                                                                                                                                       is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";
                                                                                                                                  db.query(insert_user, {}, function (err, area_id) {
                                                                                                                                       if (err) {
                                                                                                                                            console.log(err)
                                                                                                                                            con.rollback(function (err) {
                                                                                                                                                 reject(err)
                                                                                                                                            })
                                                                                                                                       } else {
                                                                                                                                            area_chk_id = area_id.insertId;

                                                                                                                                       }
                                                                                                                                  })
                                                                                                                             })
                                                                                                                        }
                                                                                                                   })



                                                                                                              }

                                                                                                              ///master lookup
                                                                                                              let data2 = "select * from master_lookup as ml where ml.value = 78002";
                                                                                                              db.query(data2, {}, function (err, master) {
                                                                                                                   if (err) {
                                                                                                                        console.log(err)
                                                                                                                        con.rollback(function (err) {
                                                                                                                             reject(err)
                                                                                                                        })
                                                                                                                   } else if (Object.keys(master).length > 0) {
                                                                                                                        desc = master[0].description
                                                                                                                        let query_9 = "select u.user_id,u.mobile_no,u.is_active,u.otp,le.legal_entity_id,u.profile_picture from users as u left join legal_entities as le ON le.legal_entity_id = u.legal_entity_id where u.mobile_no =" + mobile_no + "&&  u.is_active = 1 && le.legal_entity_type_id IN( select value from master_lookup as ml where ml.mas_cat_id =" + desc + "&&  ml.is_active =1) ";
                                                                                                                        db.query(query_9, {}, function (err, user) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else if (Object.keys(user).length > 0) {
                                                                                                                                  users.push(JSON.parse(JSON.stringify(user[[0]])))
                                                                                                                                  let entity_query = "select legal_entity_id,legal_entity_type_id from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                                                  db.query(entity_query, {}, function (err, respon) {
                                                                                                                                       if (err) {
                                                                                                                                            console.log(err)
                                                                                                                                            con.rollback(function (err) {
                                                                                                                                                 reject(err)
                                                                                                                                            })
                                                                                                                                       } else if (Object.keys(respon).length > 0) {
                                                                                                                                            legal.push(JSON.parse(JSON.stringify(respon[[0]])));
                                                                                                                                            let query_10 = "select master_lookup_name from master_lookup where value = '" + license_type + "'";
                                                                                                                                            db.query(query_10, {}, function (err, license) {
                                                                                                                                                 if (err) {
                                                                                                                                                      console.log(err)
                                                                                                                                                      con.rollback(function (err) {
                                                                                                                                                           reject(err)
                                                                                                                                                      })
                                                                                                                                                 } else if (Object.keys(license).length > 0) {
                                                                                                                                                      if (filepath1 != null) {
                                                                                                                                                           //Insert data into lelgal entity doc table
                                                                                                                                                           let insert_1 = "insert into legal_entity_docs (legal_entity_id , doc_url , doc_type,created_at) values (" + last_insert_legal_id + ",'" + filepath1 + "','" + license[0].master_lookup_name + "','" + formatted_date + "')";
                                                                                                                                                           db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                                if (err) {
                                                                                                                                                                     console.log(err)
                                                                                                                                                                     con.rollback(function (err) {
                                                                                                                                                                          reject(err)
                                                                                                                                                                     })
                                                                                                                                                                } else {
                                                                                                                                                                     last_insert_doc_id = insert.insertId;
                                                                                                                                                                }
                                                                                                                                                           })
                                                                                                                                                      }
                                                                                                                                                 } else {

                                                                                                                                                 }
                                                                                                                                            })

                                                                                                                                            //setting user preferences
                                                                                                                                            if (users.length != 0) {
                                                                                                                                                 if (pref_value != null || bstart_time != null || bend_time != null) {
                                                                                                                                                      //Insert data into user_prefences 
                                                                                                                                                      let insert_2 = "insert into user_preferences (user_id ,preference_name ,preference_value , preference_value1 ,business_start_time , business_end_time , sms_subscription  ,create_at) values (" + users[0].user_id + ',' + "'expected delivery' ,'" + pref_value + "','" + pref_value1 + "','" + bstart_time + "','" + bend_time + "'," + sms_notification + ",'" + formatted_date + "')";
                                                                                                                                                      db.query(insert_2, {}, function (err, inserted) {
                                                                                                                                                           if (err) {
                                                                                                                                                                console.log(err)
                                                                                                                                                                con.rollback(function (err) {
                                                                                                                                                                     reject(err)
                                                                                                                                                                })
                                                                                                                                                           } else {
                                                                                                                                                                last_insert_userpref_id = inserted.insertId;

                                                                                                                                                           }
                                                                                                                                                      })
                                                                                                                                                 }

                                                                                                                                            }

                                                                                                                                            if (sales_token != null && users.length != 0) {
                                                                                                                                                 let insert_call_logs = "insert into ff_call_logs (ff_id , user_id , legal_entity_id , activity , check_in , check_in_lat , check_in_long ,created_at) values (" + ff_uid + ',' + users[0].user_id + ',' + legal[0].legal_entity_id + ',' + 107000 + ",'" + formatted_date + "'," + latitude + ',' + longitude + ",'" + formatted_date + "')";
                                                                                                                                                 db.query(insert_call_logs, {}, function (err, inserted) {
                                                                                                                                                      if (err) {
                                                                                                                                                           console.log(err)
                                                                                                                                                           con.rollback(function (err) {
                                                                                                                                                                reject(err)
                                                                                                                                                           })
                                                                                                                                                      } else {
                                                                                                                                                           last_insert_fflog_id = inserted.insertId;
                                                                                                                                                      }

                                                                                                                                                 })
                                                                                                                                            }

                                                                                                                                            //used to get roleid
                                                                                                                                            getRoleId().then(res => {
                                                                                                                                                 if (res) {
                                                                                                                                                      role_id = res;
                                                                                                                                                      if (role_id != null) {
                                                                                                                                                           let user_query = "insert into user_roles (role_id  ,user_id , created_by ,  created_at) values (" + role_id + ',' + users[0].user_id + ',' + ff_uid + ",'" + formatted_date + "')";
                                                                                                                                                           db.query(user_query, {}, function (err, last_insert) {
                                                                                                                                                                if (err) {
                                                                                                                                                                     console.log(err)
                                                                                                                                                                     con.rollback(function (err) {
                                                                                                                                                                          reject(err)
                                                                                                                                                                     })
                                                                                                                                                                } else {
                                                                                                                                                                     last_insert_role_id = last_insert.insertId;
                                                                                                                                                                     //user to get user features
                                                                                                                                                                     getFeatures(users[0].user_id, 2).then(mobile => {
                                                                                                                                                                          if (mobile == null) {
                                                                                                                                                                               mobile_feature = [];
                                                                                                                                                                          } else {
                                                                                                                                                                               mobile_feature = mobile;
                                                                                                                                                                               if (users.length != 0) {
                                                                                                                                                                                    let data_insert = "insert into user_ecash_creditlimit (user_id , creditlimit) value (" + users[0].user_id + ',' + credit_limit + ")";
                                                                                                                                                                                    db.query(data_insert, {}, function (err, inserted) {
                                                                                                                                                                                         if (err) {
                                                                                                                                                                                              console.log(err)
                                                                                                                                                                                              con.rollback(function (err) {
                                                                                                                                                                                                   reject(err)
                                                                                                                                                                                              })
                                                                                                                                                                                         } else {
                                                                                                                                                                                              console.log("usercredit limit inserted successfully")
                                                                                                                                                                                         }
                                                                                                                                                                                    })
                                                                                                                                                                               }


                                                                                                                                                                               if (mfc != '') {
                                                                                                                                                                                    let insert1 = "insert into mfc_customer_mapping (mfc_id  , cust_le_id , credit_limit , is_active) value (" + mfc + ',' + legal[0].legal_entity_id + ',' + credit_limit + "," + 1 + ")";
                                                                                                                                                                                    db.query(insert1, {}, function (err, inserted) {
                                                                                                                                                                                         if (err) {
                                                                                                                                                                                              console.log(err)
                                                                                                                                                                                              con.rollback(function (err) {
                                                                                                                                                                                                   reject(err)
                                                                                                                                                                                              })
                                                                                                                                                                                         } else {
                                                                                                                                                                                              console.log("mfc inserted")
                                                                                                                                                                                         }
                                                                                                                                                                                    })
                                                                                                                                                                               }


                                                                                                                                                                               if (last_insert_legal_id != null && last_insert_user_id != null && area_chk_id != null && last_insert_role_id != null && last_insert_userpref_id != null) {
                                                                                                                                                                                    console.log("users if")
                                                                                                                                                                                    if (status == 1) {
                                                                                                                                                                                         res = { message: "Registered Successfully" }
                                                                                                                                                                                    } else {
                                                                                                                                                                                         res = { message: "We are sorry your shop is not being serviced at the moment." }
                                                                                                                                                                                    }
                                                                                                                                                                                    console.log("======>3530", le_wh_id)
                                                                                                                                                                                    res = {
                                                                                                                                                                                         business_legal_name: business_legal_name, firstname: firstname, lastname: lastname, legal_entity_id: legal[0].legal_entity_id,
                                                                                                                                                                                         customer_group_id: legal[0].legal_entity_type_id, customer_token: customer_token, customer_id: users[0].user_id, image: users[0].profile_picture,
                                                                                                                                                                                         segment_id: segment_id, pincode: pincode, is_ff: 0, is_srm: 0, is_dashboard: 0, le_wh_id: le_wh_id, hub: hub,
                                                                                                                                                                                         is_active: users[0].is_active, status: 1, has_child: 0, lp_feature: [], mobile_feature: mobile_feature, beat_id: beat, latitude: latitude, longitude: longitude,
                                                                                                                                                                                         parent_le_id: parent_le_id
                                                                                                                                                                                    }

                                                                                                                                                                                    let delete_user = "delete from user_temp where mobile_no ='" + mobile_no + "'";
                                                                                                                                                                                    db.query(delete_user, {}, function (err, deleted) {
                                                                                                                                                                                         if (err) {
                                                                                                                                                                                              console.log(err)
                                                                                                                                                                                              con.rollback(function (err) {
                                                                                                                                                                                                   reject(err)
                                                                                                                                                                                              })
                                                                                                                                                                                         } else {
                                                                                                                                                                                              console.log("User_temop delete ")
                                                                                                                                                                                         }
                                                                                                                                                                                    })
                                                                                                                                                                                    updateFlatTable(legal[0].legal_entity_id);
                                                                                                                                                                                    resolve(res);
                                                                                                                                                                                    con.commit(function (err) {
                                                                                                                                                                                         if (err) {
                                                                                                                                                                                              console.log(err)
                                                                                                                                                                                              con.rollback(function (err) {
                                                                                                                                                                                                   reject(err)
                                                                                                                                                                                              })
                                                                                                                                                                                         } else {
                                                                                                                                                                                         }

                                                                                                                                                                                    });

                                                                                                                                                                               } else {
                                                                                                                                                                                    console.log(err)
                                                                                                                                                                                    con.rollback(function (err) {
                                                                                                                                                                                         reject(err)
                                                                                                                                                                                    })
                                                                                                                                                                                    let failed_status = { status: "failed", message: "Please try again" }
                                                                                                                                                                                    resolve(failed_status);
                                                                                                                                                                               }

                                                                                                                                                                          }
                                                                                                                                                                     }).catch(err => {
                                                                                                                                                                          console.log(err)
                                                                                                                                                                     })
                                                                                                                                                                }
                                                                                                                                                           })
                                                                                                                                                      }
                                                                                                                                                 }
                                                                                                                                            }).catch(err => {
                                                                                                                                                 console.log(err)
                                                                                                                                            })
                                                                                                                                       } else {
                                                                                                                                            console.log("hello====>3480")
                                                                                                                                            resolve({ 'status': "failed", 'message': "Unable to process your request-3481" });
                                                                                                                                       }
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  resolve({ 'status': "failed", 'message': "User not yet resgistered" });
                                                                                                                             }
                                                                                                                        })
                                                                                                                   }
                                                                                                              })
                                                                                                         }
                                                                                                    })
                                                                                               }
                                                                                          })
                                                                                     }).catch(err => {
                                                                                          console.log(err)
                                                                                          con.rollback(function () {
                                                                                               reject(err);
                                                                                          });
                                                                                     })
                                                                                } else {
                                                                                     resolve({ 'status': "failed", 'message': "feild force user id id is empty" });
                                                                                }
                                                                           } else {
                                                                                resolve({ 'status': "failed", 'message': "Please select correct state" });
                                                                           }
                                                                      }).catch(err => {
                                                                           console.log(err)
                                                                           con.rollback(function (err) {
                                                                                reject(err);
                                                                           });
                                                                      })
                                                                 } else {
                                                                      resolve({ 'status': "failed" });
                                                                 }
                                                            })
                                                       }
                                                  })
                                             } else {
                                                  resolve({ 'status': "failed", 'message': "Please select correct state" });
                                             }
                                        })
                                   }).catch((err) => {
                                        console.log(err)
                                   })
                              } else {
                                   console.log("self registration")
                                   //self registration
                                   ff_uid = 0;
                                   ff_le_id = null;
                                   //serviceable code
                                   is_approved = 1;
                                   status = 1;
                                   //validating weather selected state is serviceable state or not 
                                   let statename = "select name from zone where zone_id =" + state_id;
                                   db.query(statename, {}, function (err, stateName) {
                                        // console.log("=====>2931", statename);
                                        if (err) {
                                             console.log(err);
                                             reject(err);
                                        } else if (Object.keys(stateName).length > 0) {
                                             let pincode_query = "select count(*)  as count from cities_pincodes where pincode=" + pincode + " && state = '" + stateName[0].name + "'";
                                             db.query(pincode_query, {}, function (err, pincodeStateChk) {
                                                  console.log("===>2937", pincodeStateChk[0].count)
                                                  if (err) {
                                                       console.log(err);
                                                       reject(err);
                                                  } else if (pincodeStateChk[0].count <= 0) {
                                                       is_approved = 0;
                                                       status = 0;
                                                       resolve({ 'status': "failed", 'message': "Please select correct state" });
                                                  } else {
                                                       let query_2 = "select description from master_lookup  where value = 78001";
                                                       db.query(query_2, {}, function (err, legal_entitycompany_type) {

                                                            if (err) {
                                                                 console.log(err)
                                                                 con.rollback(function (err) {
                                                                      reject(err);
                                                                 });
                                                            } else if (Object.keys(legal_entitycompany_type).length > 0) {
                                                                 getRefCode('CU', state_id).then(result => {
                                                                      if (result.length > 0) {
                                                                           le_code = result;

                                                                           if (beat != '') {
                                                                                getLeFromBeat(beat).then(parent_id => {
                                                                                     if (parent_id) {
                                                                                          parent_le_id = parent_id;
                                                                                     } else {
                                                                                          parent_le_id = 0;
                                                                                     }

                                                                                     //inserting user details into legal_entities table
                                                                                     let data_5 = "insert into  legal_entities ( business_legal_name ,address1 , legal_entity_type_id , address2 , locality , landmark ,tin_number ,country , state_id , is_approved, city, pincode, business_type_id, latitude, longitude, le_code, parent_id, created_by, gstin, arn_number, created_at, parent_le_id ,pan_number , website_url  , logo) VALUE ('" + business_legal_name + "','" + address1 + "','" + customer_type + "','" + address2 + "','" + locality + "','" + landmark + "','" + tin_number + "'," + 99 + ",'" + state_id + "','" + is_approved + "','" + city + "','" + pincode + "','" + segment_id + "','" + latitude + "','" + longitude + "','" + le_code + "','" + legal_entitycompany_type[0].description + "'," + ff_uid + ",'" + gstin + "','" + arn_number + "','" + formatted_date + "','" + parent_le_id + "','" + pan_number + "','" + website_url + "' ,'" + logo + "')";
                                                                                     db.query(data_5, {}, function (err, inserted) {
                                                                                          if (err) {
                                                                                               console.log(err)
                                                                                               con.rollback(function (err) {
                                                                                                    reject(err)
                                                                                               })
                                                                                          } else {
                                                                                               last_insert_legal_id = inserted.insertId;
                                                                                               if (email != '') {
                                                                                                    email_id = email;
                                                                                               } else {
                                                                                                    email_id = mobile_no + '@nomail.com';
                                                                                               }

                                                                                               customer_token = crypto.createHash('md5').update(mobile_no).digest("hex");
                                                                                               //Insert data into user table
                                                                                               let query_5 = "insert into users (firstname , lastname , email_id , mobile_no , profile_picture , otp , password_token , legal_entity_id , is_active , is_parent , aadhar_id , created_by  ,  created_at ,password ) values ('" + firstname + "','" + lastname + "','" + email_id + "','" + mobile_no + "','" + filepath2 + "','" + otp + "','" + customer_token + "','" + last_insert_legal_id + "','" + status + "'," + 1 + ",'" + aadhar_id + "'," + ff_uid + ",'" + formatted_date + "','" + "')";
                                                                                               db.query(query_5, {}, function (err, insert) {
                                                                                                    if (err) {
                                                                                                         console.log("error", err);
                                                                                                         resolve({ 'status': "failed", 'message': "We cannot add or update parent details of users" });
                                                                                                         con.rollback(function (err) {
                                                                                                              reject(err)
                                                                                                         })
                                                                                                    } else {
                                                                                                         last_insert_user_id = insert.insertId;
                                                                                                         if (ff_uid == null) {
                                                                                                              ff_uid = last_insert_user_id;
                                                                                                         }

                                                                                                         if (sales_token == null) {
                                                                                                              //self registration
                                                                                                              let data1 = "update users as us set created_by ='" + ff_uid + "', updated_at ='" + formatted_date + "', us.user_id =" + last_insert_user_id + "  where us.user_id =" + last_insert_user_id;
                                                                                                              db.query(data1, {}, function (err, update) {
                                                                                                                   if (err) {
                                                                                                                        console.log(err)
                                                                                                                        con.rollback(function (err) {
                                                                                                                             reject(err)
                                                                                                                        })
                                                                                                                   } else {
                                                                                                                        console.log("1.updated")
                                                                                                                   }
                                                                                                              })
                                                                                                              let data_6 = "update legal_entities as le  set created_by = '" + ff_uid + "',updated_at = '" + formatted_date + "' where le.legal_entity_id = " + last_insert_legal_id;
                                                                                                              db.query(data_6, {}, function (err, update) {
                                                                                                                   if (err) {
                                                                                                                        console.log(err)
                                                                                                                        con.rollback(function () {
                                                                                                                             reject(err)
                                                                                                                        })
                                                                                                                   } else {
                                                                                                                        console.log("2.updated")
                                                                                                                   }
                                                                                                              })
                                                                                                         }

                                                                                                         if (typeof contact_no1 != 'undefined' && contact_no1 != "") {
                                                                                                              //check weather that perticular user is exist or not 
                                                                                                              checkUser(contact_no1).then((chk_user1) => {
                                                                                                                   if (chk_user1 >= 1) {
                                                                                                                        let delete_1 = "delete from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                                        db.query(delete_1, {}, function (err, deteted) {
                                                                                                                             console.log(delete_1)
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  console.log("delete in legal_entity table");
                                                                                                                             }
                                                                                                                        })

                                                                                                                        let delete_2 = "delete from users where user_id =" + last_insert_user_id;
                                                                                                                        db.query(delete_2, {}, function (err, deteted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  console.log("delete in users table");
                                                                                                                             }
                                                                                                                        })


                                                                                                                        let delete_3 = "delete from legalentity_warehouses where legal_entity_id =" + last_insert_legal_id;
                                                                                                                        db.query(delete_3, {}, function (err, deteted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  console.log(" 2. delete in legalentity_warehouses");
                                                                                                                             }
                                                                                                                        })

                                                                                                                        let result = { message: "ContactNumber already exists  " + contact_no1, status: 'failed ' };
                                                                                                                        resolve(result);

                                                                                                                   } else if (contact_name1 != '') {
                                                                                                                        //Insert data into user table
                                                                                                                        let insert_query = "insert into users (firstname  , email_id , mobile_no , profile_picture , otp , legal_entity_id , is_active , created_by ,created_at,password ,lastname) values ('" + contact_name1 + "','" + contact_no1 + '@nomail.com' + "','" + contact_no1 + "','" + filepath2 + "'," + otp + ',' + last_insert_legal_id + ',' + status + ',' + ff_uid + ",'" + formatted_date + "','" + "','" + "')";
                                                                                                                        db.query(insert_query, {}, function (err, inserted) {
                                                                                                                             // console.log("insert_query", insert_query)
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  last_insert_user1_id = inserted.insertId;
                                                                                                                             }
                                                                                                                        })
                                                                                                                   }
                                                                                                              }).catch(err => {
                                                                                                                   console.log(err)
                                                                                                              })

                                                                                                         }

                                                                                                         if (typeof contact_no2 != 'undefined' && contact_no2 != "") {
                                                                                                              checkUser(contact_no2).then((chk_user2) => {
                                                                                                                   if (chk_user2 >= 1) {
                                                                                                                        let delete_1 = "delete from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                                        db.query(delete_1, {}, function (err, deteted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  console.log("delete in legal_entity table");
                                                                                                                             }
                                                                                                                        })

                                                                                                                        let delete_2 = "delete from users where user_id =" + last_insert_user_id;
                                                                                                                        db.query(delete_2, {}, function (err, deteted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  console.log("delete in users table");
                                                                                                                             }
                                                                                                                        })


                                                                                                                        let delete_3 = "delete from legalentity_warehouses where legal_entity_id =" + last_insert_legal_id;
                                                                                                                        db.query(delete_3, {}, function (err, deteted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  console.log(" 2. delete in legalentity_warehouses");
                                                                                                                             }
                                                                                                                        })

                                                                                                                        if (last_insert_user1_id != null) {
                                                                                                                             let delete_4 = "delete from users where user_id =" + last_insert_user1_id;
                                                                                                                             db.query(delete_4, {}, function (err, deteted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log("delete in users table");
                                                                                                                                  }
                                                                                                                             })
                                                                                                                        }

                                                                                                                        let result = { message: "ContactNumber already exists" + contact_no2, status: 'failed ' };
                                                                                                                        resolve(result);
                                                                                                                   } else if (contact_name2 != "") {
                                                                                                                        //Insert data into user table
                                                                                                                        let insert = "insert into users (firstname  , email_id , mobile_no , profile_picture , otp , legal_entity_id , is_active , created_by ,created_at,password ,lastname) values ('" + contact_name2 + "','" + contact_no2 + '@nomail.com' + "','" + contact_no2 + "','" + filepath2 + "'," + otp + ',' + last_insert_legal_id + ',' + status + ',' + ff_uid + ",'" + formatted_date + "','" + "','" + "')";
                                                                                                                        db.query(insert, {}, function (err, inserted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  last_insert_user2_id = inserted.insertId;
                                                                                                                             }
                                                                                                                        })
                                                                                                                   }
                                                                                                              }).catch(err => {
                                                                                                                   console.log(err)
                                                                                                              })
                                                                                                         }
                                                                                                    }
                                                                                               })

                                                                                               //we have to add
                                                                                               let query_7 = "select cp.city_id from cities_pincodes as cp  where cp.pincode ='" + pincode + "' && cp.officename  ='" + area + "' && cp.city ='" + city + "'";
                                                                                               db.query(query_7, {}, function (err, res) {
                                                                                                    if (err) {
                                                                                                         console.log(err)
                                                                                                         con.rollback(function (err) {
                                                                                                              reject(err)
                                                                                                         })
                                                                                                    } else {
                                                                                                         area_chk.push(res);
                                                                                                         ///used to get warehouse
                                                                                                         if (ff_uid && ff_uid != '') {
                                                                                                              getWarehouseFromLeId(parent_le_id).then(le_wh => {
                                                                                                                   if (le_wh == null) {
                                                                                                                        le_wh_id = '';
                                                                                                                   } else {
                                                                                                                        le_wh_id = le_wh;
                                                                                                                   }
                                                                                                              }).catch(err => {
                                                                                                                   console.log(err);
                                                                                                              })
                                                                                                         } else if (beat && beat != '') {
                                                                                                              getWhFromBeat(beat).then(le_wh => {
                                                                                                                   if (le_wh == null) {
                                                                                                                        le_wh_id = '';
                                                                                                                   } else {
                                                                                                                        le_wh_id = le_wh;
                                                                                                                   }
                                                                                                              }).catch(err => {
                                                                                                                   console.log(err);
                                                                                                              })
                                                                                                         } else {
                                                                                                              le_wh_id = '';
                                                                                                         }
                                                                                                         // getWarehouseid(pincode).then(le_wh => {
                                                                                                         //      if (le_wh == null) {
                                                                                                         //           le_wh_id = '';
                                                                                                         //      } else {
                                                                                                         //           le_wh_id = le_wh;

                                                                                                         //      }
                                                                                                         // }).catch((err) => {
                                                                                                         //      console.log(err)
                                                                                                         // })

                                                                                                         //used to get hub
                                                                                                         getHub(beat).then(beat_id => {
                                                                                                              if (beat_id == null) {
                                                                                                                   hub = '';
                                                                                                              } else {
                                                                                                                   hub = beat_id;
                                                                                                              }

                                                                                                         }).catch((err) => {
                                                                                                              console.log(err)
                                                                                                         })

                                                                                                         console.log("is_swipe ===> 3943", is_Swipe, is_swipe)
                                                                                                         if (area_chk[0].length > 0) {
                                                                                                              let area1 = JSON.parse(JSON.stringify(area_chk[[0]]))
                                                                                                              //Insert data into customers
                                                                                                              let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer,is_swipe ) values (" + last_insert_legal_id + ',' +
                                                                                                                   volume_class + ',' + noof_shutters + ',' + area1[0].city_id + ",'" + master_manf + "'," + smartphone + ",'" + network + "','" + ff_uid + "','" + beat + "','" + formatted_date + "'," + is_icecream + ',' +
                                                                                                                   is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";

                                                                                                              db.query(insert_user, {}, function (err, area_id) {
                                                                                                                   if (err) {
                                                                                                                        console.log(err)
                                                                                                                        con.rollback(function (err) {
                                                                                                                             reject(err)
                                                                                                                        })
                                                                                                                   } else {

                                                                                                                        area_chk_id = area_id.insertId;
                                                                                                                   }
                                                                                                              })
                                                                                                         } else {
                                                                                                              let state_query = "select name from zone where zone_id = " + state_id;
                                                                                                              db.query(state_query, {}, function (err, res) {
                                                                                                                   if (err) {
                                                                                                                        console.log(err)
                                                                                                                        con.rollback(function (err) {
                                                                                                                             reject(err)
                                                                                                                        })
                                                                                                                   } else if (Object.keys(res).length > 0) {
                                                                                                                        state_name = res;
                                                                                                                   } else {
                                                                                                                        let ros = { 'status': 'failed', 'message': 'Unable to process your request->3837' }
                                                                                                                   }

                                                                                                                   if (state_name.length != 0) {
                                                                                                                        let insert_city = "insert into cities_pincodes (country_id ,pincode , city , state , officename) values(" + 99 + ',' + pincode + ",'" + city + "','" + state_name[0].name + "','" + area + "')";
                                                                                                                        db.query(insert_city, {}, function (err, inserted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  con.rollback(function (err) {
                                                                                                                                       reject(err)
                                                                                                                                  })
                                                                                                                             } else {
                                                                                                                                  last_insert_city_id = inserted.insertId
                                                                                                                             }
                                                                                                                             console.log("=====>3987", is_Swipe, is_swipe)
                                                                                                                             //Insert data into customers
                                                                                                                             let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer, is_swipe ) values (" + last_insert_legal_id + ',' +
                                                                                                                                  volume_class + ',' + noof_shutters + ',' + last_insert_city_id + ",'" + master_manf + "'," + smartphone + ',' + network + ',' + ff_uid + ',' + beat + ",'" + formatted_date + "'," + is_icecream + ',' +
                                                                                                                                  is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";
                                                                                                                             db.query(insert_user, {}, function (err, area_id) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       area_chk_id = area_id.insertId;

                                                                                                                                  }
                                                                                                                             })
                                                                                                                        })
                                                                                                                   }
                                                                                                              })



                                                                                                         }

                                                                                                         ///master lookup
                                                                                                         let data2 = "select * from master_lookup as ml where ml.value = 78002";
                                                                                                         db.query(data2, {}, function (err, master) {
                                                                                                              if (err) {
                                                                                                                   console.log(err)
                                                                                                                   con.rollback(function (err) {
                                                                                                                        reject(err)
                                                                                                                   })
                                                                                                              } else if (Object.keys(master).length > 0) {
                                                                                                                   desc = master[0].description
                                                                                                                   let query_9 = "select u.user_id,u.mobile_no,u.is_active,u.otp,le.legal_entity_id,u.profile_picture from users as u left join legal_entities as le ON le.legal_entity_id = u.legal_entity_id where u.mobile_no =" + mobile_no + "&&  u.is_active = 1 && le.legal_entity_type_id IN( select value from master_lookup as ml where ml.mas_cat_id =" + desc + "&&  ml.is_active =1) ";
                                                                                                                   db.query(query_9, {}, function (err, user) {
                                                                                                                        if (err) {
                                                                                                                             console.log(err)
                                                                                                                             con.rollback(function (err) {
                                                                                                                                  reject(err)
                                                                                                                             })
                                                                                                                        } else if (Object.keys(user).length > 0) {
                                                                                                                             users.push(JSON.parse(JSON.stringify(user[[0]])))
                                                                                                                             let entity_query = "select legal_entity_id,legal_entity_type_id from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                                             db.query(entity_query, {}, function (err, respon) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err)
                                                                                                                                       con.rollback(function (err) {
                                                                                                                                            reject(err)
                                                                                                                                       })
                                                                                                                                  } else if (Object.keys(respon).length > 0) {
                                                                                                                                       legal.push(JSON.parse(JSON.stringify(respon[[0]])));
                                                                                                                                       let query_10 = "select master_lookup_name from master_lookup where value = '" + license_type + "'";
                                                                                                                                       db.query(query_10, {}, function (err, license) {
                                                                                                                                            if (err) {
                                                                                                                                                 console.log(err)
                                                                                                                                                 con.rollback(function (err) {
                                                                                                                                                      reject(err)
                                                                                                                                                 })
                                                                                                                                            } else if (Object.keys(license).length > 0) {
                                                                                                                                                 if (filepath1 != null) {
                                                                                                                                                      //Insert data into lelgal entity doc table
                                                                                                                                                      let insert_1 = "insert into legal_entity_docs (legal_entity_id , doc_url , doc_type,created_at) values (" + last_insert_legal_id + ",'" + filepath1 + "','" + license[0].master_lookup_name + "','" + formatted_date + "')";
                                                                                                                                                      db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                           if (err) {
                                                                                                                                                                console.log(err)
                                                                                                                                                                con.rollback(function (err) {
                                                                                                                                                                     reject(err)
                                                                                                                                                                })
                                                                                                                                                           } else {
                                                                                                                                                                last_insert_doc_id = insert.insertId;
                                                                                                                                                           }
                                                                                                                                                      })
                                                                                                                                                 }
                                                                                                                                            } else {

                                                                                                                                            }
                                                                                                                                       })

                                                                                                                                       //setting user preferences
                                                                                                                                       if (users.length != 0) {
                                                                                                                                            if (pref_value != null || bstart_time != null || bend_time != null) {
                                                                                                                                                 //Insert data into user_prefences 
                                                                                                                                                 let insert_2 = "insert into user_preferences (user_id ,preference_name ,preference_value , preference_value1 ,business_start_time , business_end_time , sms_subscription  ,create_at) values (" + users[0].user_id + ',' + "'expected delivery' ,'" + pref_value + "','" + pref_value1 + "','" + bstart_time + "','" + bend_time + "'," + sms_notification + ",'" + formatted_date + "')";
                                                                                                                                                 db.query(insert_2, {}, function (err, inserted) {
                                                                                                                                                      if (err) {
                                                                                                                                                           console.log(err)
                                                                                                                                                           con.rollback(function (err) {
                                                                                                                                                                reject(err)
                                                                                                                                                           })
                                                                                                                                                      } else {
                                                                                                                                                           last_insert_userpref_id = inserted.insertId;

                                                                                                                                                      }
                                                                                                                                                 })
                                                                                                                                            }

                                                                                                                                       }

                                                                                                                                       if (sales_token != null && users.length != 0) {
                                                                                                                                            let insert_call_logs = "insert into ff_call_logs (ff_id , user_id , legal_entity_id , activity , check_in , check_in_lat , check_in_long ,created_at) values (" + ff_uid + ',' + users[0].user_id + ',' + legal[0].legal_entity_id + ',' + 107000 + ",'" + formatted_date + "'," + latitude + ',' + longitude + ",'" + formatted_date + "')";
                                                                                                                                            db.query(insert_call_logs, {}, function (err, inserted) {
                                                                                                                                                 if (err) {
                                                                                                                                                      console.log(err)
                                                                                                                                                      con.rollback(function (err) {
                                                                                                                                                           reject(err)
                                                                                                                                                      })
                                                                                                                                                 } else {
                                                                                                                                                      last_insert_fflog_id = inserted.insertId;
                                                                                                                                                 }

                                                                                                                                            })
                                                                                                                                       }

                                                                                                                                       //used to get roleid
                                                                                                                                       getRoleId().then(res => {
                                                                                                                                            if (res) {
                                                                                                                                                 role_id = res;
                                                                                                                                                 if (role_id != null) {
                                                                                                                                                      let user_query = "insert into user_roles (role_id  ,user_id , created_by ,  created_at) values (" + role_id + ',' + users[0].user_id + ',' + ff_uid + ",'" + formatted_date + "')";
                                                                                                                                                      db.query(user_query, {}, function (err, last_insert) {
                                                                                                                                                           if (err) {
                                                                                                                                                                console.log(err)
                                                                                                                                                                con.rollback(function (err) {
                                                                                                                                                                     reject(err)
                                                                                                                                                                })
                                                                                                                                                           } else {
                                                                                                                                                                last_insert_role_id = last_insert.insertId;
                                                                                                                                                                //user to get user features
                                                                                                                                                                getFeatures(users[0].user_id, 2).then(mobile => {
                                                                                                                                                                     if (mobile == null) {
                                                                                                                                                                          mobile_feature = [];
                                                                                                                                                                     } else {
                                                                                                                                                                          mobile_feature = mobile;
                                                                                                                                                                          if (users.length != 0) {
                                                                                                                                                                               let data_insert = "insert into user_ecash_creditlimit (user_id , creditlimit) value (" + users[0].user_id + ',' + credit_limit + ")";
                                                                                                                                                                               db.query(data_insert, {}, function (err, inserted) {
                                                                                                                                                                                    if (err) {
                                                                                                                                                                                         console.log(err)
                                                                                                                                                                                         con.rollback(function (err) {
                                                                                                                                                                                              reject(err)
                                                                                                                                                                                         })
                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("usercredit limit inserted successfully")
                                                                                                                                                                                    }
                                                                                                                                                                               })
                                                                                                                                                                          }


                                                                                                                                                                          if (mfc != '') {
                                                                                                                                                                               let insert1 = "insert into mfc_customer_mapping (mfc_id  , cust_le_id , credit_limit , is_active) value (" + mfc + ',' + legal[0].legal_entity_id + ',' + credit_limit + "," + 1 + ")";
                                                                                                                                                                               db.query(insert1, {}, function (err, inserted) {
                                                                                                                                                                                    if (err) {
                                                                                                                                                                                         console.log(err)
                                                                                                                                                                                         con.rollback(function (err) {
                                                                                                                                                                                              reject(err)
                                                                                                                                                                                         })
                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("mfc inserted")
                                                                                                                                                                                    }
                                                                                                                                                                               })
                                                                                                                                                                          }


                                                                                                                                                                          if (last_insert_legal_id != null && last_insert_user_id != null && area_chk_id != null && last_insert_role_id != null && last_insert_userpref_id != null) {
                                                                                                                                                                               console.log("users if")
                                                                                                                                                                               if (status == 1) {
                                                                                                                                                                                    res = { message: "Registered Successfully" }
                                                                                                                                                                               } else {
                                                                                                                                                                                    res = { message: "We are sorry your shop is not being serviced at the moment." }
                                                                                                                                                                               }

                                                                                                                                                                               res = {
                                                                                                                                                                                    business_legal_name: business_legal_name, firstname: firstname, lastname: lastname, legal_entity_id: legal[0].legal_entity_id,
                                                                                                                                                                                    customer_group_id: legal[0].legal_entity_type_id, customer_token: customer_token, customer_id: users[0].user_id, image: users[0].profile_picture,
                                                                                                                                                                                    segment_id: segment_id, pincode: pincode, is_ff: 0, is_srm: 0, is_dashboard: 0, le_wh_id: le_wh_id, hub: hub,
                                                                                                                                                                                    is_active: users[0].is_active, status: 1, has_child: 0, lp_feature: [], mobile_feature: mobile_feature, beat_id: beat, latitude: latitude, longitude: longitude,
                                                                                                                                                                                    parent_le_id: parent_le_id
                                                                                                                                                                               }

                                                                                                                                                                               let delete_user = "delete from user_temp where mobile_no ='" + mobile_no + "'";
                                                                                                                                                                               db.query(delete_user, {}, function (err, deleted) {
                                                                                                                                                                                    if (err) {
                                                                                                                                                                                         console.log(err)
                                                                                                                                                                                         con.rollback(function (err) {
                                                                                                                                                                                              reject(err)
                                                                                                                                                                                         })
                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("User_temop delete ")
                                                                                                                                                                                    }
                                                                                                                                                                               })
                                                                                                                                                                               updateFlatTable(legal[0].legal_entity_id);

                                                                                                                                                                               resolve(res);
                                                                                                                                                                               con.commit(function (err) {
                                                                                                                                                                                    if (err) {
                                                                                                                                                                                         console.log(err)
                                                                                                                                                                                         con.rollback(function (err) {
                                                                                                                                                                                              reject(err)
                                                                                                                                                                                         })
                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("updated ===> 4051")
                                                                                                                                                                                    }

                                                                                                                                                                               });

                                                                                                                                                                          } else {
                                                                                                                                                                               console.log(err)
                                                                                                                                                                               con.rollback(function (err) {
                                                                                                                                                                                    reject(err)
                                                                                                                                                                               })
                                                                                                                                                                               let failed_status = { status: "failed", message: "Please try again" }
                                                                                                                                                                               resolve(failed_status);
                                                                                                                                                                          }

                                                                                                                                                                     }
                                                                                                                                                                }).catch(err => {
                                                                                                                                                                     console.log(err)
                                                                                                                                                                })
                                                                                                                                                           }
                                                                                                                                                      })
                                                                                                                                                 }
                                                                                                                                            }
                                                                                                                                       }).catch(err => {
                                                                                                                                            console.log(err)
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log("hello")
                                                                                                                                       resolve({ 'status': "failed", 'message': "Unable to process your request-4078" });
                                                                                                                                  }
                                                                                                                             })
                                                                                                                        } else {
                                                                                                                             resolve({ 'status': "failed", 'message': "User not yet resgistered" });
                                                                                                                        }
                                                                                                                   })
                                                                                                              }
                                                                                                         })
                                                                                                    }
                                                                                               })
                                                                                          }
                                                                                     })
                                                                                }).catch(err => {
                                                                                     console.log(err)
                                                                                     con.rollback(function () {
                                                                                          reject(err);
                                                                                     });
                                                                                })
                                                                           } else {
                                                                                resolve({ 'status': "failed", 'message': "Empty beat details" });
                                                                           }
                                                                      } else {
                                                                           resolve({ 'status': "failed", 'message': "Please select correct state" });
                                                                      }
                                                                 }).catch(err => {
                                                                      console.log(err)
                                                                      con.rollback(function (err) {
                                                                           reject(err);
                                                                      });
                                                                 })
                                                            } else {
                                                                 resolve({ 'status': "failed" });
                                                            }
                                                       })
                                                  }
                                             })
                                        } else {
                                             resolve({ 'status': "failed", 'message': "Please select correct state" });
                                        }
                                   })
                              }
                         } else {
                              resolve({ 'status': "failed", 'message': "Please generate otp first ." });
                         }
                    })
               })
               // })
          })
     } catch (err) {
          console.log(err)
     }
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
               let data = { 'token_status': 0 };
               user.countDocuments({ password_token: customer_token }, function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (response > 0) {
                         data = { 'token_status': 1 }
                         resolve(data);
                    } else {
                         resolve(data);

                    }
               })


               //      let string = JSON.stringify(customer_token);
               //      let data = { 'token_status': 0 };
               //      let query = "select user_id from users where password_token = " + string
               //      sequelize.query(query).then(rows => {
               //           console.log("rows=======>592", rows)
               //           if (rows[0].length > 0) {
               //                data = { 'token_status': 1 }
               //                resolve(data)
               //           } else {
               //                resolve(data)
               //           }
               //      }).catch(err => {
               //           console.log(err);
               //           reject(err);
               //      })
          });
     } catch (err) {
          console.log(err)
     }
}

//used to get userId
module.exports.getUserId = function (customer_token, app_flag = 0) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(customer_token)
               if (app_flag == 0) {
                    let query = "select user_id , firstname , lastname , legal_entity_id from users as u where u.password_token =" + string;
                    db.query(query, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              reject(err)
                         } else if (Object.keys(result).length > 0) {
                              resolve(result)
                         }
                    })
               } else {
                    let query = "select user_id , firstname , lastname , legal_entity_id from users as u where u.password_token =" + string + " or lp_token =" + string;
                    db.query(query, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              reject(err)
                         } else if (Object.keys(result).length > 0) {
                              resolve(result)
                         }
                    })
               }


          })
     } catch (err) {
          console.log(err)
     }

}

/*
Purpose : used to get warehouseid based on provided hub_id
author : Deepak Tiwari
Request : hub_id.
Resposne : Returns warehouseid.
*/
function getWarehouseidByHub(hub_id) {
     return new Promise((resolve, reject) => {
          let query = "select lew.le_wh_id from legalentity_warehouses as lew left join dc_hub_mapping as dhm ON dhm.dc_id = lew.le_wh_id where dhm.hub_id = " + hub_id + "and lew.dc_type = 118001";
          db.query(query, {}, function (err, row) {
               if (err) {
                    console.log(err)
                    reject(err)
               } else if (Object.keys(row).length > 0) {
                    let le_wh_ids = [];
                    if (Array.isArray(row) && row.length > 0) {
                         le_wh.forEach((lewh) => {
                              le_wh_ids.push(lewh.le_wh_id + ',')
                         })
                         le_wh_ids = le_wh_ids.split(',')
                    }
                    console.log("===>3377", le_wh_ids)
                    resolve(le_wh_ids)
               }
          })

     })
}

/*
Purpose :checkMobileNumber() used to check weather enter mobile number is correct or not 
author : Deepak Tiwari
Request : mobile number
Resposne : Returns otp.
*/
module.exports.checkMobileNumber = function (mobile_no) {
     return new Promise((resolve, reject) => {
          user.findOne({ mobile: mobile_no }, { otp: 1 }).then(otp_number => {
               if (otp_number != null) {
                    resolve(otp_number.otp);
               } else {
                    userTemp.findOne({ mobile: mobile_no }, { otp: 1 }).then(user_temp_otp => {
                         if (user_temp_otp != null) {
                              resolve(user_temp_otp.otp);
                         } else {
                              resolve('');
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
          // let query = "select otp as otp_number from users where mobile_no = " + mobile_no;
          // db.query(query, {}, function (err, salesotp) {
          //      if (err) {
          //           reject(err)
          //      } else if (Object.keys(salesotp).length > 0) {
          //           resolve(salesotp[0])
          //      } else {
          //           let temp = "select otp as otp_number from user_temp where mobile_no =" + mobile_no;
          //           db.query(temp, {}, function (err, row) {
          //                if (err) {
          //                     reject(err)
          //                } else if (Object.keys(row).length > 0) {
          //                     if (row.length > 0) {
          //                          //console.log("hdbs" , row)
          //                          resolve(row[0])
          //                     } else {
          //                          resolve('')
          //                     }
          //                }
          //           })
          //      }
          // })
     })
}



function getMaterDescription(value) {
     try {
          if (value != '') {
               return new Promise((resolve, reject) => {
                    let query = "select description from master_lookup  where value = " + value + " and is_active = 1";
                    db.query(query, {}, function (err, master_desc) {
                         if (err) {
                              console.log(err)
                              reject(err)
                         } else if (Object.keys(master_desc[0]).length > 0) {
                              resolve(master_desc[0])
                         } else {
                              resolve('')
                         }
                    })
               })
          }
     } catch (err) {
          console.log(err)
     }
}

//used to get warehouse details based on bu_id
function getLeWhByBu(bu_id) {
     return new Promise((resolve, reject) => {
          let result;
          db.query("call getBuHierarchy_proc(?,@le_wh_ids);SELECT @le_wh_ids as wh_list;", [bu_id], function (err, rows) {
               let json = JSON.parse(JSON.stringify(rows[1]))
               // console.log("query", json[0].wh_list)
               if (err) {
                    console.log(err)
                    reject(err)
               } else if (json[0].wh_list != null) {
                    //  console.log("rows")
                    result = json[0].wh_list.split(',')
                    resolve(result)

               } else {
                    resolve(0);
               }
          })

     })
}


//reveiw require
function getNextBusinessChild(catId, businessArr, level) {
     try {
          return new Promise((resolve, reject) => {
               let sample = JSON.parse(JSON.stringify(businessArr[0]))
               let collectChild = [];
               let temp = [];
               let tempArray = [];
               let child = [];
               if (businessArr.length > 0) {
                    for (let i = 0; i < sample.length; i++) {
                         if (sample[i].parent_bu_id == catId) {
                              delete temp;
                              temp = [];
                              getLeWhByBu(sample[i].bu_id).then(data => {
                                   if (data == 0) {
                                        if (!sample[i].bu_id == tempArray) {
                                             tempArray = sample[i].bu_id
                                             temp = "<option value='" + sample[i].bu_id + "'class='bu" + level + "'>" + sample[i].bu_name + "</option>";
                                        }
                                   }
                                   // delete businessArr.key
                                   child = getNextBusinessChild(sample[[i]].bu_id, businessArr, level + 1);
                                   if (typeof child != 'undefined') {
                                        if (child.length > 0) {
                                             child.forEach(function (value) {
                                                  value.forEach(function (val) {
                                                       temp[temp.length] = val
                                                  })
                                             })
                                        }
                                   }

                                   collectChild = temp
                                   console.log("collect", collectChild)
                                   resolve(collectChild)
                              }).catch(err => {
                                   console.log(err)
                              })
                         }
                    }

               } else {
                    resolve(collectChild)
               }

          })

     } catch (err) {
          console.log(err)
     }
}


Array.prototype.unique = function () {
     return this.filter(function (value, index, self) {
          return self.lastIndexOf(value) === index;
     });
}

function inArray(needle, haystack) {
     var length = haystack.length;
     for (var i = 0; i < length; i++) {
          if (haystack[i] == needle) return true;
     }
     return false;
}

//used to get all business unit details for particular user
function allBusinessUnits(userId) {
     let access_value = [];
     let data = [];
     let bu_id_exist = [];
     return new Promise((resolve, reject) => {
          let query = " SELECT GROUP_CONCAT(object_id) as object_id FROM user_permssion WHERE user_id = " + userId + " and permission_level_id=6";
          db.query(query, {}, function (err, access) {
               if (err) {
                    reject(err)
               } else if (Object.keys(access).length > 0) {
                    if (access == '') {
                         resolve(access_value)
                    }
                    access_value = access[0].object_id.split(',')
                    console.log("access_value", access_value)
                    let data = [];
                    if (inArray(0, access_value)) {
                         let query_1 = "select bu_name , bu_id , parent_bu_id , description , is_active , cost_center  from business_units"
                         db.query(query_1, {}, function (err, rows) {
                              if (err) {
                                   console.log(err)
                                   reject(err)
                              } else if (Object.keys(rows).length > 0) {
                                   data.push(rows);
                                   bu_id_exist.push(_.pluck(data[[0]], 'bu_id'))
                                   // console.log('data===>', data)
                                   resolve(data)

                              }
                         })
                    } else {
                         console.log("ypur in else condition")
                         let query_1 = "select bu_name , bu_id , parent_bu_id , description , is_active , cost_center from business_units where bu_id IN(" + access_value + ")"
                         db.query(query_1, {}, function (err, businesData) {
                              //console.log("response", businesData)
                              if (err) {
                                   console.log(err)
                                   reject(err)
                              } else if (Object.keys(businesData).length > 0) {
                                   bu_id_exist.push(_.pluck(businesData, 'bu_id'))
                                   data = businesData;
                                   let json = JSON.parse(JSON.stringify(data[0]))
                                   resolve(data)
                              }
                         })
                    }
               }
          })
     })
}


//used to get warehouse details based on bu ids 
function businessTreeData(userId) {
     try {
          return new Promise((resolve, reject) => {
               allBusinessUnits(userId).then(allBusinessUnits => {//used to get business unit details fromm user_permission table based on user_id
                    let parentWiseArr = {};
                    let tempArray = [];
                    let resolvedfinalArray = [];
                    let data;
                    let sample = JSON.parse(JSON.stringify(allBusinessUnits[0]))
                    //  console.log("sample value ", sample);
                    for (let i = 0; i < sample.length; i++) {
                         if (sample[i].parent_bu_id == 0) {
                              console.log("if condition")
                              getLeWhByBu(sample[i].bu_id).then(data => {//get all warehouses details for entered bu_id
                                   if (data.length != 0) {
                                        if (!(tempArray == sample[[i]].bu_id)) {
                                             tempArray = sample[[i]].bu_id;
                                             parentWiseArr[sample[[i]].bu_id] = [[sample[[i]].bu_id] = sample[[i]].bu_name]
                                             console.log("pa1", parentWiseArr)
                                        }
                                   }
                                   delete allBusinessUnits.businesData;
                                   getNextBusinessChild(sample[i].bu_id, allBusinessUnits, 2).then(child => {
                                        parentWiseArr = parentWiseArr.concat(child)
                                   }).catch(err => {
                                        console.log(err)
                                   })
                                   //  resolve(parentWiseArr.unique());
                              }).catch(err => {
                                   console.log(err)
                              })
                         } else {
                              console.log("else condition")
                              getLeWhByBu(sample[i].bu_id).then(data => {
                                   if (data.length != 0) {
                                        if (!(sample[i].bu_id.hasOwnProperty(tempArray))) {//checking weather tempArray is exist in array or not if not then only inserting in tempArray to restrict dublicated value
                                             tempArray = sample[i].bu_id;
                                             parentWiseArr = Object.assign(parentWiseArr, { [sample[i].bu_id]: sample[i].bu_name })
                                             //   parentWiseArr[sample[i].bu_id] = [[sample[i].bu_id] = sample[i].bu_name]
                                             //  console.log("pa", parentWiseArr)
                                        }
                                   }
                                   console.log("OutSide the if condition")
                                   delete allBusinessUnits.businesData;
                                   getNextBusinessChild(sample[[i]].bu_id, allBusinessUnits, 2).then(child => {
                                        parentWiseArr = Object.assign(parentWiseArr, child);
                                        // parentWiseArr = parentWiseArr.concat(child)
                                   }).catch(err => {
                                        console.log(err)
                                   })

                                   resolvedfinalArray.push(parentWiseArr);
                                   // console.log('resolvedFinalArray', resolvedfinalArray);
                                   // resolve(resolvedfinalArray)
                                   //  resolve(parentWiseArr.unique());
                              }).catch(err => {
                                   console.log(err)
                              })
                         }
                         resolve(resolvedfinalArray);
                    }
               }).catch(err => {
                    console.log(err)
               })

          })
     } catch (err) {
          console.log(err)
     }

}

//update
module.exports.getAllstockists = function (userId) {
     try {
          return new Promise((resolve, reject) => {
               businessTreeData(userId).then(query => {
                    console.log("query", query)
                    resolve(query)
               }).catch(err => {
                    console.log(err)
                    reject(err)
               })
          })
     } catch (err) {
          console.log(err)
     }
}

//used to get parent legal_entity_id based upon selected feildforce id
function getParentLeIdFromFFId(id) {
     return new Promise((resolve, reject) => {
          let query = "select legal_entity_id from users where user_id =?"
          db.query(query, [id], function (err, row) {
               if (err) {
                    console.log(err)
                    reject(err)
               } else if (Object.keys(row).length > 0) {
                    resolve(row[0].legal_entity_id)
               } else {
                    resolve('')
               }
          })
     })

}


function getParentIdFromLegalEntity(legal_entity_id) {
     return new Promise((resolve, reject) => {
          let query = "select parent_le_id from legal_entities where legal_entity_id =?"
          db.query(query, [legal_entity_id], function (err, row) {
               if (err) {
                    console.log(err)
                    reject(err)
               } else if (Object.keys(row).length > 0) {
                    resolve(row[0].parent_le_id)
               } else {
                    resolve('')
               }
          })
     })

}

function getWarehouseIdByMobileNo(mobile_no) {
     try {
          return new Promise((resolve, reject) => {
               let query = " select d.dc_id FROM retailer_flat r JOIN pjp_pincode_area p ON r.beat_id = p.pjp_pincode_area_id JOIN dc_hub_mapping d ON d.hub_id = p.le_wh_id WHERE r.mobile_no = " + mobile_no;
               db.query(query, {}, function (err, warehouse_id) {
                    if (err) {
                         console.log(err);
                         reject(err)
                    } else if (Object.keys(warehouse_id).length > 0) {
                         resolve(warehouse_id[0].dc_id)
                    } else {
                         resolve('')
                    }
               })
          })
     } catch (err) {
          console.log(err)
     }
}

//used to get product group
function getProductGroups() {
     try {
          return new Promise((resolve, reject) => {
               let query = "select pg.product_grp_ref_id as product_grp_id , pg.product_grp_name , b.brand_id from product_groups as pg JOIN products as p ON pg.product_grp_ref_id = p.product_group_id JOIN brands as b ON b.brand_id = p.brand_id  group By product_grp_id order By pg.product_grp_name ASC "
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         resolve(result)
                    }
               })

          })
     } catch (err) {
          console.log(err)
     }
}

//used to get product details of both brands and manufacture based on used_id and access level of that user
module.exports.getBrandsManufacturerProductGroupByUser = function (userid) {
     try {
          let finalarray = [];
          let brandObj = [];
          let manfObj = [];
          return new Promise((resolve, reject) => {
               getProductGroups().then(product_grp => {
                    let query = "select brands.brand_name , brands.brand_id , brands.mfg_id from brands group by  brands.brand_id"
                    db.query(query, {}, function (err, brand) {
                         if (err) {
                              console.log(err)
                              reject(err)
                         } else if (Object.keys(brand).length > 0) {
                              brandObj.push(brand)
                              let query_1 = "select business_legal_name , legal_entity_id from legal_entities where legal_entity_type_id = 1006 group by legal_entity_id"
                              db.query(query_1, {}, function (err, response) {
                                   if (err) {
                                        console.log(err)
                                        reject(err)
                                   } else if (Object.keys(response).length > 0) {
                                        manfObj.push(response)
                                        let result = { 'brands': brandObj, 'manufacturer': manfObj, 'product_group': product_grp }
                                        resolve(result)
                                   }
                              })
                         }
                    })

               }).catch(err => {
                    console.log(err)
               })
          })

     } catch (err) {
          let data = { 'status': "failed", "message": "Error", "full message": "Internal server error" }
          return data
     }
}


//used to get legal_entity based on entered beat 
function getLeFromBeat(beat) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select l.legal_entity_id FROM pjp_pincode_area p inner JOIN legalentity_warehouses l ON p.le_wh_id = l.le_wh_id WHERE p.pjp_pincode_area_id=" + beat;
               db.query(query, {}, function (err, le_id) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(le_id).length > 0) {
                         resolve(le_id[0].legal_entity_id)
                    } else {
                         resolve('')
                    }
               })
          })
     } catch (err) {
          console.log(err)
     }

}

// //Used to get warehouse id based on selected beat.
// function getWhFromBeat(beat) {
//      try {
//           return new Promise((resolve, reject) => {
//                let query = "select d.dc_id FROM pjp_pincode_area p INNER JOIN dc_hub_mapping d ON p.le_wh_id = d.hub_id WHERE p.pjp_pincode_area_id=" + beat;
//                db.query(query, {}, function (err, le_id) {
//                     if (err) {
//                          console.log(err);
//                          reject(err)

//                     } else if (Object.keys(le_id).length > 0) {
//                          console.log("===>3769", le_id[0])
//                          resolve(le_id[0].dc_id)
//                     } else {
//                          resolve(0)
//                     }
//                })
//           })

//      } catch (err) {
//           console.log(err)
//      }
// }


function getUserIdByCustomerToken(customer_token) {
     try {
          return new Promise((resolve, reject) => {
               if (customer_token != null) {
                    let query = "select user_id from users where password_token ='" + customer_token + "'"
                    db.query(query, {}, function (err, data) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(data).length > 0) {
                              resolve(data[0].user_id)
                         } else {
                              resolve(null);
                         }
                    })
               }

          })

     } catch (err) {
          console.log(err)
     }

}



/*
Purpose :We insert required details with appid when device details are not available  
author : Deepak Tiwari
Request : ip_address, device_id, user_id, download_token
Resposne : Returns download token.
*/
module.exports.createDownloadtoken = function (ip_address, device_id, user_id, download_token) {
     try {
          return new Promise((resolve, reject) => {
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let query = "insert into device_details (user_id , device_id , app_id , ip_address ,created_at , last_used_date) values (" + user_id + ',' + device_id + ',' + download_token + ',' + ip_address + ',' + formatted_date + ',' + formatted_date + ")"
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err)
                    } else {
                         console.log("====> 3828 , inserted successfully")
                    }
               })
          })

     } catch (err) {
          console.log(err)
     }
}


/*
Purpose :InsertNewFfComments() used  To insert into ff_call_logs table
author : Deepak Tiwari
Request : sales_token, user_id, activity, latitude, longitude
Resposne : Insert ff comments.
*/
exports.InsertNewFfComments_controller = function (sales_token, user_id, activity, latitude, longitude) {
     try {
          console.log("sales_token", sales_token)
          let ff_id;
          let log_chk;
          let current_datetime = new Date();
          let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
          return new Promise((resolve, reject) => {
               if (sales_token != '') {
                    let query = "select user_id from users where password_token  = '" + sales_token + "'"
                    db.query(query, {}, function (err, ff_ids) {
                         if (err) {
                              console.log(err)
                              reject(err)
                         } else if (Object.keys(ff_ids).length > 0) {
                              ff_id = ff_ids[0].user_id

                         } else {
                              ff_id = ''
                         }
                         let query_1 = "select legal_entity_id from users where user_id = " + user_id;
                         db.query(query_1, {}, function (err, legal_entity_id) {
                              if (err) {
                                   console.log(err)
                                   reject(err)
                              } else if (Object.keys(legal_entity_id).length > 0) {
                                   if (activity == 107000) {
                                        let query_2 = "select log_id from ff_call_logs as fcl where legal_entity_id  =" + legal_entity_id[0].legal_entity_id + " && ff_id =" + ff_id + " && DATE(created_at) = '" + formatted_date + "'";
                                        db.query(query_2, {}, function (err, log_Chk) {
                                             if (err) {
                                                  console.log(err);
                                                  reject(err)
                                             } else if (Object.keys(log_Chk).length > 0) {
                                                  log_chk = log_Chk
                                             }
                                        })

                                        let insert_query = "insert into ff_call_logs (ff_id , user_id , legal_entity_id , activity , check_in , check_in_lat , check_in_long , created_at) values('" + ff_id + "'" + ',' + "'" + user_id + "'" + ',' + "'" + legal_entity_id[0].legal_entity_id + "'" + ',' + "'" + activity + "'" + ',' + "'" + formatted_date + "'" + ',' + "'" + latitude + "'" + ',' + "'" + longitude + "'" + ',' + "'" + formatted_date + "')"

                                        db.query(insert_query, {}, function (err, inserted) {
                                             if (err) {
                                                  console.log(err);
                                                  reject(err)
                                             } else {
                                                  console.log("======>4015 inserted successfully")
                                                  resolve(true);
                                             }
                                        })

                                   } else {

                                        let query_4 = "select log_id  from ff_call_logs as fcl where legal_entity_id = " + legal_entity_id[0].legal_entity_id + " && ff_id =" + ff_id + " order by log_id DESC limit 1";
                                        db.query(query_4, {}, function (err, log_chk1) {
                                             if (err) {
                                                  console.log(err)
                                                  reject(err)
                                             } else if (Object.keys(log_chk1).length > 0) {
                                                  let update_query = "update ff_call_logs set activity =" + activity + ',' + "check_out = '" + formatted_date + "' , check_out_lat =" + latitude + " , check_out_long =" + longitude + "where ff_id =" + ff_id + " && user_id =" + user_id + " && legal_entity_id =" + legal_entity_id[0].legal_entity_id + "&& log_id =" + log_chk1[0].log_id + " && DATE(created_at) ='" + formatted_date + "'";
                                                  db.query(update_query, {}, function (err, updated) {
                                                       if (err) {
                                                            console.log(err);
                                                            reject(err)
                                                       } else {
                                                            console.log("======>4003 Updated successfully")
                                                            resolve(true)
                                                       }
                                                  })
                                             }
                                        })
                                   }
                              }
                         })

                    })

               }
          })



     } catch (err) {
          console.log(err)
          let error = { "status": "failed", "message": "Internal server error" }
          return error;
     }


}


/*
Purpose :getAllCustomers() used To get all the customers
author : Deepak Tiwari
Request : (ff_id, beat_id, is_billed, offset, offset_limit, search, flag, hub, spoke, sort
Resposne : Returns all customer details.
*/
module.exports.getAllCustomers = function (ff_id, beat_id, is_billed, offset, offset_limit, search, flag, hub, spoke, sort) {
     try {
          let result;
          let query;
          return new Promise((resolve, reject) => {
               if (flag != '' && sort == "") {
                    sort = 146006;
               }

               if (flag == 1) {
                    query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup  FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id  =  users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id WHERE users1.is_active  = 1 && users1.is_parent =1 && (legal_entity_type_id LIKE '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%')";
               } else {
                    query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup, getRetailerOrdersCount(leg.legal_entity_id) AS no_of_orders, getRetailerReturnsCount(leg.legal_entity_id) AS return_orders, getRetailerTotalBusiness(leg.legal_entity_id) AS total_business, getRetailerAvgBillValue(leg.legal_entity_id) AS avg_bill_val, 0 AS 'rank' ,IFNULL((SELECT ROUND(uec.cashback,2) FROM users us JOIN user_ecash_creditlimit uec ON uec.user_id=us.user_id WHERE us.legal_entity_id=leg.legal_entity_id AND us.is_parent=1 LIMIT 1),0) AS remain_bal    FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id = users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id WHERE users1.is_active = 1 && users1.is_parent =1 && legal_entity_type_id LIKE '%30%'";
               }

               if (flag == 1) {
                    if (beat_id != '') {
                         if (beat_id.indexOf(-1) !== -1) {
                              query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup  FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id  =  users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id WHERE users1.is_active  = 1 && users1.is_parent =1 && (legal_entity_type_id LIKE '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%')";

                         } else {
                              query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup  FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id  =  users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id WHERE cust.beat_id IN (" + beat_id + ") && users1.is_active  = 1 && users1.is_parent =1 && (legal_entity_type_id LIKE '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%')";

                         }
                    }

                    if (hub != '' && spoke != '') {
                         query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup   FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id  =  users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id join pjp_pincode_area as pa ON pa.pjp_pincode_area_id = cust.beat_id join spokes as sp ON pa.spoke_id = sp.spoke_id  WHERE users1.is_active  = 1 && users1.is_parent =1 && (legal_entity_type_id LIKE '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%') && FIND_IN_SET(sp.le_wh_id ," + hub + ") && FIND_IN_SET(sp.spoke_id ," + spoke + ") ";

                    } else {
                         if (beat_id != -1) {
                              query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup  FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id  =  users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id WHERE cust.beat_id IN (" + beat_id + ") &&users1.is_active  = 1 && users1.is_parent =1 && (legal_entity_type_id LIKE '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%')";
                         } else {
                              query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup  FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id  =  users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id WHERE users1.is_active  = 1 && users1.is_parent =1 && (legal_entity_type_id LIKE '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%')";
                         }
                    }
               } else {
                    if (beat_id != -1) {
                         query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup, getRetailerOrdersCount(leg.legal_entity_id) AS no_of_orders, getRetailerReturnsCount(leg.legal_entity_id) AS return_orders, getRetailerTotalBusiness(leg.legal_entity_id) AS total_business, getRetailerAvgBillValue(leg.legal_entity_id) AS avg_bill_val, 0 AS 'rank' ,IFNULL((SELECT ROUND(uec.cashback,2) FROM users us JOIN user_ecash_creditlimit uec ON uec.user_id=us.user_id WHERE us.legal_entity_id=leg.legal_entity_id AND us.is_parent=1 LIMIT 1),0) AS remain_bal    FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id = users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id WHERE cust.beat_id IN (" + beat_id + ") && users1.is_active = 1 && users1.is_parent =1 && (legal_entity_type_id LIKE '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%')";
                    } else {
                         query = "SELECT leg.business_legal_name AS company , leg.latitude , leg.longitude ,leg.address1 AS address_1 ,leg.address2,leg.legal_entity_id , CONCAT(users1.firstname,' ',users1.lastname) AS firstname, cust.beat_id  ,getBeatName(cust.beat_id) AS beatname , getRetailerCheck_in( leg.legal_entity_id) AS check_in , users1.mobile_no AS telephone , users1.user_id AS customer_id, users1.password_token AS customer_token, No_of_shutters, volume_class, business_type_id, master_manf,leg.legal_entity_type_id AS buyer_type , CASE WHEN volume_class IS NULL OR volume_class = '' OR No_of_shutters IS NULL OR No_of_shutters = '' OR master_manf IS NULL OR master_manf = '' THEN 1  ELSE 0  END AS popup, getRetailerOrdersCount(leg.legal_entity_id) AS no_of_orders, getRetailerReturnsCount(leg.legal_entity_id) AS return_orders, getRetailerTotalBusiness(leg.legal_entity_id) AS total_business, getRetailerAvgBillValue(leg.legal_entity_id) AS avg_bill_val, 0 AS 'rank' ,IFNULL((SELECT ROUND(uec.cashback,2) FROM users us JOIN user_ecash_creditlimit uec ON uec.user_id=us.user_id WHERE us.legal_entity_id=leg.legal_entity_id AND us.is_parent=1 LIMIT 1),0) AS remain_bal    FROM legal_entities AS leg JOIN users AS users1 ON leg.legal_entity_id = users1.legal_entity_id  JOIN customers AS cust ON leg.legal_entity_id = cust.le_id WHERE users1.is_active = 1 && users1.is_parent =1 && (legal_entity_type_id LIKE '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%')";
                    }
               }

               if (is_billed == 1) {
                    let date = new Date();
                    result = query.concat(" && leg.legal_entity_id NOT IN (select go.cust_le_id from gds_orders as go where DATE(created_at) ='" + date + "'  && go.created_by = " + ff_id + ")");
               } else {
                    result = query;
               }

               if (search != '') {
                    result = query.concat("&& (leg.business_legal_name like  '%" + search + "%' or users1.mobile_no like '%" + search + "%')")
               }

               if (sort) {
                    if (sort == 146001) {
                         result = result.concat(" order by avg_bill_val  ASC");
                    } else if (sort == 146006) {
                         result = result.concat(" order by avg_bill_val  DESC");
                    } else if (sort == 146002) {
                         result = result.concat(" order by leg.business_legal_name ASC");
                    } else if (sort == 146005) {
                         result = result.concat(" order by leg.business_legal_name DESC");
                    } else if (sort == 146003) {
                         result = result.concat(" order by check_in ASC")
                    } else if (sort == 146010) {
                         result = result.concat(" order by remain_bal ASC")
                    } else if (sort == 146009) {
                         result = result.concat(" order by remain_bal DESC")
                    } else {
                         result = result.concat(" order by check_in DESC")
                    }
               }


               if (offset_limit != 0) {
                    result = result.concat(" limit " + offset + ',' + offset_limit + "")
                    db.query(result, {}, function (err, response) {
                         // console.log(result)
                         if (err) {
                              console.log(err);
                              reject(err)
                         } else if (Object.keys(response).length > 0) {
                              resolve(response)
                         } else {
                              resolve(response);
                         }
                    })
               } else {
                    db.query(result, {}, function (err, rows) {
                         if (err) {
                              console.log(err);
                              reject(err)
                         } else if (Object.keys(rows).length > 0) {
                              resolve(rows)
                         } else {
                              resolve(rows);
                         }
                    })
               }
          })
     } catch (err) {
          console.log(err)
          let data = { 'status': "failed", "message": "Internal server error" }
          return data;
     }

}


/*
Purpose :InsertNewFfComments() used  To insert into ff_call_logs table
author : Deepak Tiwari
Request : sales_token, user_id, activity, latitude, longitude
Resposne : Insert ff comments.
*/
function InsertNewFfComments(sales_token, user_id, activity, latitude, longitude) {
     try {
          let ff_id;
          let log_chk;
          let current_datetime = new Date();
          let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
          return new Promise((resolve, reject) => {
               if (sales_token != '') {
                    let query = "select user_id from users where password_token  = '" + sales_token + "'"
                    db.query(query, {}, function (err, ff_ids) {
                         if (err) {
                              console.log(err)
                              reject(err)
                         } else if (Object.keys(ff_ids).length > 0) {
                              ff_id = ff_ids[0].user_id

                         } else {
                              ff_id = ''
                         }
                         let query_1 = "select legal_entity_id from users where user_id = " + user_id;
                         db.query(query_1, {}, function (err, legal_entity_id) {
                              if (err) {
                                   console.log(err)
                                   reject(err)
                              } else if (Object.keys(legal_entity_id).length > 0) {
                                   if (activity == 107000) {
                                        let query_2 = "select log_id from ff_call_logs as fcl where legal_entity_id  =" + legal_entity_id[0].legal_entity_id + " && ff_id ='" + ff_id + "' && DATE(created_at) = '" + formatted_date + "'";
                                        db.query(query_2, {}, function (err, log_Chk) {
                                             if (err) {
                                                  console.log(err);
                                                  reject(err)
                                             } else if (Object.keys(log_Chk).length > 0) {
                                                  log_chk = log_Chk
                                             }
                                        })

                                        let insert_query = "insert into ff_call_logs (ff_id , user_id , legal_entity_id , activity , check_in , check_in_lat , check_in_long , created_at) values('" + ff_id + "'" + ',' + "'" + user_id + "'" + ',' + "'" + legal_entity_id[0].legal_entity_id + "'" + ',' + "'" + activity + "'" + ',' + "'" + formatted_date + "'" + ',' + "'" + latitude + "'" + ',' + "'" + longitude + "'" + ',' + "'" + formatted_date + "')"
                                        db.query(insert_query, {}, function (err, inserted) {
                                             if (err) {
                                                  console.log(err);
                                                  reject(err)
                                             } else {
                                                  console.log("======>4015 inserted successfully")
                                             }
                                        })
                                   } else {

                                        let query_4 = "select log_id  form ff_call_logs as fcl where legal_entity_id = " + legal_entity_id[0].legal_entity_id + " && ff_id =" + ff_id + " order by log_id DESC limit 1";
                                        db.query(query_4, {}, function (err, log_chk1) {
                                             if (err) {
                                                  console.log(err)
                                                  reject(err)
                                             } else if (Object.keys(log_chk1).length > 0) {
                                                  let update_query = "update ff_call_logs set activity =" + activity + ',' + "check_out = " + formatted_date + " , check_out_lat =" + latitude + " , check_out_long =" + longitude + "where ff_id =" + ff_id + " && user_id =" + user_id + "legal_entity_id =" + legal_entity_id[0].legal_entity_id + "&& log_id =" + log_chk1[0].log_id + " && DATE(created_at) =" + current_datetime;
                                                  db.query(update_query, {}, function (err, update) {
                                                       if (err) {
                                                            console.log(err)
                                                            reject(err)
                                                       } else {
                                                            console.log("update successfully")
                                                       }
                                                  })
                                             }
                                        })
                                   }

                              }

                         })

                    })

               }
               resolve(true);
          })



     } catch (err) {
          console.log(err)
          let error = { "status": "failed", "message": "Internal server error" }
          return error;
     }


}

/*
Purpose : serviceablePincode() used To get check wether pincode is in servicable location or not
author : Deepak Tiwari
Request : phonenumber, ff_token, latitude, longitude
Resposne : Return generated token.
*/
module.exports.generateRetailerToken = async function (phonenumber, ff_token, latitude, longitude) {
     try {
          let response = {};
          let user_det;
          let user_query;
          let customer_id;
          let ecash_details = null;
          let ff_id;
          let ff_ecash_details = null;
          let le_wh_id;
          let customer_token;
          let promotion_count;
          let category = {};
          let current_datetime = new Date();
          let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
          return new Promise((resolve, reject) => {
               getMaterDescription(78002).then(desc => {
                    let query = "select us.*,le.is_approved,le.business_type_id,le.legal_entity_type_id,le.pincode,le.latitude,le.longitude,le.parent_le_id  from users as us left join legal_entities as le ON le.legal_entity_id = us.legal_entity_id where us.mobile_no = " + phonenumber + " && us.is_active =  1  && le.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc.description + " && ml.is_active = 1)"
                    db.query(query, {}, async function (err, user_leg) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(user_leg).length > 0) {
                              user_det = JSON.parse(JSON.stringify(user_leg[0]))
                              user_query = user_leg.length;
                              // ------After completing Registartion-------
                              if (user_query == 1 && user_det.is_active == 1) {
                                   customer_id = user_det.user_id;
                                   let random_number = JSON.stringify(Math.floor(10000000 + Math.random() * 90000000));
                                   customer_token = crypto.createHash('md5').update(random_number).digest("hex");
                                   user.findOne({ user_id: customer_id }, function (err, mongoResponse) {
                                        if (err) {
                                             console.log(err);
                                             reject(err);
                                        } else if (mongoResponse != null) {
                                             user.updateOne({ user_id: customer_id },
                                                  {
                                                       $set: {
                                                            password_token: customer_token,
                                                            updatedOn: formatted_date
                                                       }
                                                  }, function (err, tokenupdate) {
                                                       if (err) {
                                                            console.log(err);
                                                            reject(err);
                                                       } else {
                                                            console.log("------6258-----");
                                                       }
                                                  })
                                        } else {
                                             let query = "select mobile_no , password_token , lp_token ,user_id , otp ,lp_otp , is_disabled, is_active , legal_entity_id , created_by , created_at from users where user_id =" + customer_id;
                                             db.query(query, {}, function (err, query_response) {
                                                  if (err) {
                                                       console.log(err);
                                                  } else if (Object.keys(query_response).length > 0) {
                                                       let body = {
                                                            mobile: query_response[0].mobile_no,
                                                            user_id: query_response[0].user_id,
                                                            password_token: query_response[0].password_token,
                                                            lp_token: query_response[0].lp_token,
                                                            otp: query_response[0].otp,
                                                            lp_otp: query_response[0].lp_otp,
                                                            is_active: query_response[0].is_active,
                                                            is_disabled: query_response[0].is_disabled,
                                                            legal_entity_id: query_response[0].legal_entity_id,
                                                            //createdOn: query_response[0].created_at,
                                                            createdBy: query_response[0].created_by
                                                       }
                                                       user.create(body).then(inserted => {
                                                            //after inserting the records 
                                                            user.updateOne({ user_id: customer_id },
                                                                 {
                                                                      $set: {
                                                                           password_token: customer_token,
                                                                           updatedOn: formatted_date
                                                                      }
                                                                 }, function (err, tokenUpdate) {
                                                                      if (err) {
                                                                           console.log(err);
                                                                           reject(err);
                                                                      } else {
                                                                           console.log("------6293-----");
                                                                      }
                                                                 })

                                                       })

                                                  } else {
                                                       reject('');
                                                  }
                                             })
                                        }
                                   })
                                   let update_query = "update users as us set password_token ='" + customer_token + "', updated_at = '" + formatted_date + "' where us.user_id =" + customer_id;
                                   db.query(update_query, {}, async function (err, Update_custoken) {
                                        if (err) {
                                             console.log(err)
                                             reject(err)
                                        } else {
                                             console.log("updated successfully")
                                        }
                                   })

                                   InsertNewFfComments(ff_token, customer_id, 107000, latitude, longitude).then(status => {
                                        if (status) {
                                             console.log("====>4097 inserted Successfully")
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })

                                   getWarehouseIdByMobileNo(phonenumber).then(async le_wh_ids => {
                                        if (le_wh_id == '') {
                                             let response = { "status": "failed", "message": "Please provide valid data" }
                                             resolve(response);
                                        } else {
                                             le_wh_id = le_wh_ids;
                                        }
                                        let ecash_detail = await getEcashInfo(user_det.user_id);
                                        if (ecash_detail) {
                                             ecash_details = ecash_detail[0];
                                        }
                                        let ff_ids = await getUserIdByCustomerToken(ff_token);
                                        if (ff_ids) {
                                             ff_id = ff_ids
                                             let ff_ecash_detail = await getEcashInfo(ff_id)
                                             if (ff_ecash_detail != '') {
                                                  ff_ecash_details = ff_ecash_detail
                                             }
                                        }
                                        let query_6 = "select beat_id from customers where le_id =" + user_det.legal_entity_id;
                                        db.query(query_6, {}, async function (err, beat_id) {
                                             if (err) {
                                                  console.log(err);
                                                  reject(err)
                                             } else if (Object.keys(beat_id).length > 0) {
                                                  beat_id.push(beat_id)
                                                  let LEWHID = await getLewhDetails(beat_id[0].beat_id);
                                                  console.log("-----6520----", LEWHID)
                                                  getHub(beat_id[0].beat_id).then(hub => {
                                                       if (hub == '') {
                                                            hub = '';
                                                       } else {
                                                            hub = hub

                                                       }
                                                       if (le_wh_id != '') {
                                                            let query_7 = "select count(*) as count from promotion_cashback_details where  FIND_IN_SET (" + le_wh_id + " ,wh_id) and cbk_status=1 and is_self = 0 and CURDATE() between start_date and end_date and customer_type like '%" + user_det.legal_entity_type_id + "%'";
                                                            db.query(query_7, {}, async function (err, promotioms) {
                                                                 if (err) {
                                                                      console.log(err);
                                                                      reject(err)
                                                                 } else if (Object.keys(promotioms).length > 0) {
                                                                      promotion_count = promotioms[0].count
                                                                 } else {
                                                                      promotion_count = 0
                                                                 }
                                                                 // category = await getCategoryByHubId(hub, le_wh_id);
                                                                 getFeatures(user_det.user_id, 2).then(mobile_features => {
                                                                      response = {
                                                                           customer_group_id: user_det.legal_entity_type_id, customer_token: customer_token, legal_entity_id: user_det.legal_entity_id, parent_le_id: user_det.parent_le_id,
                                                                           customer_id: user_det.user_id, firstname: user_det.firstname, lastname: user_det.lastname, image: user_det.profile_picture,
                                                                           segment_id: user_det.business_type_id, pincode: user_det.pincode, is_active: user_det.is_active, le_wh_id: LEWHID, hub: hub, is_ff: 0, lp_feature: [],
                                                                           mobile_feature: mobile_features, beat_id: beat_id[0].beat_id, latitude: user_det.latitude, longitude: user_det.longitude, ecash_details: ecash_details, ff_ecash_details: ff_ecash_details, promotion_count: promotion_count
                                                                      }

                                                                      let res = { "message": "Token generated", "status": 1, "approved": 1, "data": response }
                                                                      resolve(res)
                                                                 }).catch(err => {
                                                                      console.log(err)
                                                                 })
                                                            })



                                                       }
                                                  })
                                             }
                                        })
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })


                              }
                         } else {
                              resolve('');
                         }
                    })
               }).catch(err => {
                    console.log(err)
               })

          })
     } catch (err) {
          console.log(err)
          let error = { "status": "failed", "message": "Internal server error" }
          return error;
     }

}


/*
Purpose : checkfiledfource() used To checkUser with that phonenumber
author : Deepak Tiwari
Request : phonenumber, telephone
Resposne : Return otp confirmation.
*/
function checkfeildforce(telephone) {
     try {
          let desc;
          let buyer_type_id;
          let user_chkdet;
          let ff_check;
          let customer_chk;
          let lp_feature = [];
          let mobile_feature = [];
          let result_users = [];
          let result_users2 = [];
          let result = {};
          let customer_id;
          let is_active;
          let otpflag;
          let srm_check;
          return new Promise((resolve, reject) => {
               let data = "select * from master_lookup as ml where ml.value = 78002";
               db.query(data, {}, function (err, master) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(master).length > 0) {
                         desc = master[0].description;
                         let query = "select * from users where mobile_no =" + telephone + " && is_active = 1"
                         db.query(query, {}, function (err, userchk) {
                              if (err) {
                                   reject(err);
                              } else if (Object.keys(userchk).length > 0) {
                                   user_chkdet = userchk[0];
                                   ff_check = checkPermissionByFeatureCode('EFF001', user_chkdet.user_id);
                                   srm_check = checkPermissionByFeatureCode('SRM001', user_chkdet.user_id);
                                   customer_chk = checkPermissionByFeatureCode('MCU001', user_chkdet.user_id);

                                   getFeatures(user_chkdet.user_id, 1).then(lp_features => {
                                        if (lp_features.length > 0) {
                                             lp_feature.push(lp_features)
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })

                                   getFeatures(user_chkdet.user_id, 2).then(feature => {
                                        if (feature.length > 0) {
                                             mobile_feature.push(feature)
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })
                              } else {
                                   ff_check = 0;
                                   srm_check = 0;
                                   customer_chk = 0;
                                   lp_feature = [];
                                   mobile_feature = [];

                              }
                              if (ff_check == 1 || srm_check == 1 || lp_feature.length != 0 || mobile_feature.length != 0 && customer_chk == 0) {
                                   let query_8 = "select us.otp as otp_number from users as us where us.mobile_no =" + telephone;
                                   db.query(query_8, {}, function (err, result_user) {
                                        if (err) {
                                             console.log(err)
                                             reject(err)
                                        } else if (Object.keys(result_user).length > 0) {
                                             result_users.push(result_user)
                                        }

                                   })

                              } else {
                                   let query = "select user.otp as otp_number from users as user LEFT JOIN legal_entities as leg ON leg.legal_entity_id = user.legal_entity_id where user.mobile_no ='" + telephone + "' &&  leg.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc + "&& ml.is_active = 1)";
                                   db.query(query, {}, function (err, result_user) {
                                        if (err) {
                                             reject(err);
                                        } else if (Object.keys(result_user).length > 0) {
                                             result_users.push(result_user)
                                             let query_1 = "select us.otp as otp_number from user_temp as us where us.mobile_no =" + telephone;
                                             db.query(query_1, {}, function (err, result) {
                                                  if (err) {
                                                       console.log(err)
                                                       reject(err)
                                                  } else if (Object.keys(result).length > 0) {
                                                       result_users2.push(result)
                                                  }
                                             })
                                        }
                                   })
                              }

                              if (result_users2 != '') {
                                   result_users2;
                              } else {
                                   resolve(result_users);
                              }

                         })

                    }

               })
          })
     } catch (err) {
          let res = { 'status': "failed", 'message': 'Internal server error' }
          return res
     }



}



function checkSalesToken(sales_token) {
     try {
          let ff_check;
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(sales_token);
               let query = "select user_id  from users where password_token = " + string
               db.query(query, {}, function (err, user_id) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(user_id).length > 0) {
                         ff_check = checkPermissionByFeatureCode('EFF001', user_id[0].user_id);
                         if (ff_check != 1) {
                              ff_check = checkPermissionByFeatureCode('MFF001', user_id[0].user_id);
                         }
                         resolve(ff_check)
                    } else {
                         ff_check = 0;
                         resolve(ff_check)
                    }
               })
          })

     } catch (err) {
          console.log(err)
     }




}

module.exports.getSalesOtp = function (sales_token) {
     try {
          return new Promise((resolve, reject) => {
               //checking weather that user details are already exist or not based on user
               user.findOne({ $or: [{ password_token: sales_token }, { lp_token: sales_token }] }, { otp: 1 }, function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                         res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                    } else if (response != null) {
                         //if response is not equal null 
                         // console.log("response", response)
                         resolve(response.otp);
                    } else {
                         let query = "select mobile_no , password_token , lp_token ,user_id , otp ,lp_otp , is_disabled, is_active , legal_entity_id , created_by , created_at from users where password_token ='" + sales_token + "'";
                         db.query(query, {}, function (err, query_response) {
                              if (err) {
                                   console.log(err);
                              } else if (Object.keys(query_response).length > 0) {
                                   let body = {
                                        mobile: query_response[0].mobile_no,
                                        user_id: query_response[0].user_id,
                                        password_token: query_response[0].password_token,
                                        lp_token: query_response[0].lp_token,
                                        otp: query_response[0].otp,
                                        lp_otp: query_response[0].lp_otp,
                                        is_active: query_response[0].is_active,
                                        is_disabled: query_response[0].is_disabled,
                                        legal_entity_id: query_response[0].legal_entity_id,
                                        // createdOn: query_response[0].created_at,
                                        createdBy: query_response[0].created_by
                                   }
                                   user.create(body).then(inserted => {
                                        //after inserting the records 
                                        user.findOne({ $or: [{ password_token: sales_token }, { lp_token: sales_token }] }, { otp: 1 }, function (err, response) {
                                             if (err) {
                                                  console.log(err);
                                                  reject(err);
                                                  res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
                                             } else if (response != null) {
                                                  //if response is not equal null 
                                                  // console.log("response", response)
                                                  resolve(response.otp);
                                             } else {
                                                  resolve('');
                                             }
                                        })

                                   })

                              } else {
                                   reject('');
                              }
                         })

                    }
               })

          })

     } catch (err) {
          console.log(err)
     }
}


function getNameFromMaster(Value) {
     try {
          let result = [];
          let getValues = [];
          return new Promise((resolve, reject) => {
               let query = "select master_lookup_name from master_lookup where value = '" + Value + "'"
               db.query(query, {}, function (err, getValue) {
                    if (err) {
                         console.log(err);
                         reject(err)
                    } else if (Object.keys(getValue).length > 0) {
                         getValues.push(JSON.parse(JSON.stringify(getValue[0])))
                         console.log("======>4431 getvalue", getValues)
                         getValues.forEach((value) => {
                              resolve(value)
                         })
                    } else {
                         resolve('')
                    }
               })
          })
     } catch (err) {
          console.log(err)
     }
}


/*
Purpose :UpdateRetailerData() used To update retailer data
author : Deepak Tiwari
Request : user_id, segment_id, volume_class, noof_shutters, master_manf, smartphone, network, buyer_type, ff_id
Resposne : Update retailer data
*/
module.exports.updateRetailerData = function (user_id, segment_id, volume_class, noof_shutters, master_manf, smartphone, network, buyer_type, ff_id) {
     try {
          return new Promise((resolve, reject) => {
               let noofSuppliers;
               let volumeClassName;
               let retailer_network;
               let retailer_smartphone;
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let query = "select leg.legal_entity_id  from users as user join legal_entities as leg ON leg.legal_entity_id = user.legal_entity_id where user_id =" + user_id
               db.query(query, {}, function (err, user_id) {
                    if (err) {
                         console.log(err);
                         reject(err)
                    } else if (Object.keys(user_id).length > 0) {
                         //based on buyer_type we were updatin business_type_id in legalEntity table
                         if (buyer_type != '') {
                              let update_query = "update legal_entities set  business_type_id = " + segment_id + ", legal_entity_type_id =" + buyer_type + ", updated_by =" + ff_id + ", updated_at ='" + formatted_date + "'  where legal_entity_id =" + user_id[0].legal_entity_id;
                              db.query(update_query, {}, function (err, Update_segment) {
                                   if (err) {
                                        console.log(err)
                                        reject(err)
                                   } else {
                                        console.log("====>4476 Updated Successfully")
                                   }
                              })
                         } else {
                              let update_query = "update legal_entities set  business_type_id = " + segment_id + ", updated_by =" + ff_id + ", updated_at ='" + formatted_date + "' where  legal_entity_id =" + user_id[0].legal_entity_id;
                              db.query(update_query, {}, function (err, Update_segment) {
                                   if (err) {
                                        console.log(err)
                                        reject(err)
                                   } else {
                                        console.log("====>4485 Updated Successfully")
                                   }
                              })
                         }

                         let data = " select * from customers where le_id =" + user_id[0].legal_entity_id;
                         db.query(data, {}, function (err, cust_chk) {
                              if (err) {
                                   console.log(err)
                                   reject(err)
                              } else if (Object.keys(cust_chk).length > 0) {
                                   let update_query_1 = "update customers set  volume_class =" + volume_class + ", No_of_shutters =" + noof_shutters + " , master_manf ='" + master_manf + "' , dist_not_serv = '" + master_manf + "' , smartphone =" + smartphone + " , network =" + network + " , updated_by =" + ff_id + " ,updated_at = '" + formatted_date + "' where le_id =" + user_id[0].legal_entity_id;
                                   db.query(update_query_1, {}, function (err, result) {
                                        if (err) {
                                             console.log(err)
                                             reject(err)
                                        } else {
                                             console.log("======>4502 Updated Successfully")
                                        }
                                   })


                                   retailer_smartphone = smartphone == 1 ? 'YES' : 'NO';
                                   retailer_network = network == 1 ? 'YES' : 'NO';
                                   volumeClassName = volume_class.split(',');
                                   let volumeClassSample = [];
                                   let noofSuppliersSample = [];
                                   getNameFromMaster(volumeClassName).then(volume_class1 => {
                                        volumeClassSample.push(volume_class1.master_lookup_name);
                                        console.log("  volumeClassSample", volumeClassSample)
                                        volumeClassName = volumeClassSample.join(); //used to join  commas or any sperator on array
                                        console.log(' volumeClassName', volumeClassName)
                                        noofSuppliers = master_manf.split(',');
                                        getNameFromMaster(noofSuppliers).then(no_suppliers => {
                                             noofSuppliersSample.push(no_suppliers.master_lookup_name);
                                             console.log(" noofSuppliers ", noofSuppliersSample)
                                             noofSuppliers = noofSuppliersSample.join();
                                             console.log("noofSuppliers", noofSuppliers)
                                             let update_query_2 = " update retailer_flat  set volume_class ='" + volumeClassName + "' ,volume_class_id = " + volume_class + " , No_of_shutters =" + noof_shutters + " , master_manf ='" + master_manf + "' ,dist_not_serv ='" + master_manf + "' ,suppliers = '" + noofSuppliers + "' , smartphone ='" + retailer_smartphone + "' , network ='" + retailer_network + "' ,updated_by =" + ff_id + " ,updated_at ='" + formatted_date + "'  where legal_entity_id =" + user_id[0].legal_entity_id;
                                             db.query(update_query_2, {}, function (err, retailer_flat) {
                                                  if (err) {
                                                       console.log(err)
                                                       reject(err);
                                                  } else {
                                                       console.log("======>4523 updated successfully")
                                                       resolve("updated successfully")
                                                  }
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
                                   let insert_query = "insert into customers ( le_id , volume_class  , No_of_shutters , master_manf , smartphone , network , updated_by , updated_at , area_id)  values(" + user_id[0].legal_entity_id + ',' + volume_class + ",'" + noof_shutters + "','" + master_manf + "'," + smartphone + ',' + network + ',' + ff_id + ",'" + formatted_date + "'," + 0 + ")";
                                   //  let update_query_1 = "update customers set  le_id =" + user_id[0].legal_entity_id + ", volume_class =" + volume_class + ", No_of_shutters =" + noof_shutters + " , master_manf ='" + master_manf + "' , smartphone =" + smartphone + " , network =" + network + " , updated_by =" + ff_id + " ,updated_at = '" + formatted_date + "'"
                                   db.query(insert_query, {}, function (err, result) {
                                        if (err) {
                                             console.log(err)
                                             reject(err)
                                        } else {
                                             console.log("======>4502 Updated Successfully")
                                        }
                                   })

                                   retailer_smartphone = smartphone == 1 ? 'YES' : 'NO';
                                   retailer_network = network == 1 ? 'YES' : 'NO';
                                   volumeClassName = volume_class.split(',');
                                   let volumeClassSample = [];
                                   let noofSuppliersSample = [];
                                   getNameFromMaster(volumeClassName).then(volume_class1 => {
                                        volumeClassSample.push(volume_class1.master_lookup_name);
                                        console.log("  volumeClassSample", volumeClassSample)
                                        volumeClassName = volumeClassSample.join(); //used to join  commas or any sperator on array
                                        console.log(' volumeClassName', volumeClassName)
                                        noofSuppliers = master_manf.split(',');
                                        getNameFromMaster(noofSuppliers).then(no_suppliers => {
                                             noofSuppliersSample.push(no_suppliers.master_lookup_name);
                                             console.log(" noofSuppliers ", noofSuppliersSample)
                                             noofSuppliers = noofSuppliersSample.join();
                                             console.log("noofSuppliers", noofSuppliers)
                                             let update_query_2 = " update retailer_flat  set volume_class ='" + volumeClassName + "' ,volume_class_id = " + volume_class + " , No_of_shutters =" + noof_shutters + " , master_manf ='" + master_manf + "' ,dist_not_serv ='" + master_manf + "' ,suppliers = '" + noofSuppliers + "' , smartphone ='" + retailer_smartphone + "' , network ='" + retailer_network + "' ,updated_by =" + ff_id + " ,updated_at ='" + formatted_date + "'  where legal_entity_id =" + user_id[0].legal_entity_id;
                                             db.query(update_query_2, {}, function (err, retailer_flat) {
                                                  if (err) {
                                                       console.log(err)
                                                       reject(err);
                                                  } else {
                                                       console.log("======>4523 updated successfully")
                                                       resolve("updated successfully")
                                                  }
                                             })
                                        }).catch(err => {
                                             console.log(err);
                                             reject(err);
                                        })
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })

                              }
                         })

                    } else {
                         resolve('')
                    }
               })
          })
     } catch (err) {
          console.log(err)
          let error = { "status": "failed", "message": "Internal server error" }
          return error;
     }


}


/*
Purpose :InsertFfComments() used To insert into ff_call_logs table
author : Deepak Tiwari
Request : sales_token, user_id, activity, latitude, longitude
Resposne : return inserted data
*/
exports.InsertFfComments = function (sales_token, user_id, activity, latitude, longitude) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(sales_token)
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let query = "select user_id from users where password_token =" + string
               db.query(query, {}, function (err, ff_id) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(ff_id).length > 0) {
                         let data = "select legal_entity_id from users where user_id =" + user_id
                         db.query(data, {}, function (err, legal_entity_id) {
                              if (err) {
                                   console.log(err)
                                   reject(err)
                              } else if (Object.keys(legal_entity_id).length > 0) {
                                   let insert_query = "insert into ff_call_logs (ff_id , user_id , legal_entity_id , activity , latitude , longitude , created_at , check_in , check_in_lat , check_in_long)  value ('" + ff_id[0].user_id + "','" + user_id + "','" + legal_entity_id[0].legal_entity_id + "','" + activity + "','" + latitude + "','" + longitude + "','" + formatted_date + "','" + formatted_date + "','" + latitude + "','" + longitude + "')"
                                   db.query(insert_query, {}, function (err, result) {
                                        if (err) {
                                             console.log(err)
                                             reject(err)
                                        } else {
                                             console.log("=======>4632", result)
                                             resolve(result.affectedRows)
                                        }
                                   })

                              }
                         })
                    }
               })
          })
     } catch (err) {
          console.log(err)
          let error = { 'status': "failed", 'message': 'Internal server error' }
          return error;
     }


}


/*
Purpose :UpdateCheckoutFfComments() used To update checkout ff comment 
author : Deepak Tiwari
Request : sales_token, user_id, activity, latitude, longitude
Resposne : return updated data
*/
module.exports.UpdateCheckoutFfComments = function (sales_token, user_id, activity, latitude, longitude) {
     try {
          return new Promise((resolve, reject) => {
               let results;
               let string = JSON.stringify(sales_token)
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let query = "select user_id from users where password_token =" + string
               db.query(query, {}, function (err, ff_id) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(ff_id).length > 0) {
                         let update_query = "update ff_call_logs set check_out_lat =" + latitude + " , check_out_long =" + longitude + " , activity = " + activity + " , check_out ='" + formatted_date + "' where ff_id =" + ff_id[0].user_id + " && user_id =" + user_id + " && activity = 107000";
                         db.query(update_query, {}, function (err, result) {
                              if (err) {
                                   console.log(err)
                                   reject(err)
                              } else {
                                   results = result
                                   console.log('=======>4684 updated successfully')
                                   resolve(results)
                              }
                         })

                         let delete_query = "delete from offline_cart_details where cust_id =" + user_id;
                         db.query(delete_query, {}, function (err, deleted) {
                              if (err) {
                                   console.log(err)
                              } else {
                                   console.log("=====>4693 deleted successfully")
                              }
                         })
                    }
               })


          })
     } catch (err) {
          console.log(err)
          let error = { 'status': "failed", 'message': 'Internal server error' }
          return error;
     }

}




/*
Purpose :getFeatureByUserId() Used to  features based on userid
author : Deepak Tiwari
Request : user_id
Resposne : return features
*/
function getFeatureByUserId(userid) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select features.feature_code , features.name  from user_roles left join users ON users.user_id  =  user_roles.user_id  left join role_access ON role_access.role_id  = user_roles.role_id left join features ON features.feature_id  =  role_access.feature_id  where users.is_active = 1 && user_roles.user_id  =" + userid + " && features.is_mobile_enabled = 1 && features.is_active =1 order by features.sort_order ASC";

               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         console.log("=====>4732 result", result)
                         resolve(result)

                    }
               })
          })

     } catch (err) {
          console.log(err)
     }
}


/*
Purpose : getPjp() Used to check weather pjp are assigned to ff person
author : Deepak Tiwari
Request : ff_id
Resposne : return pjp
*/
function getPjp(ff_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select rm_id from pjp_pincode_area as ppa where ppa.rm_id =" + ff_id

               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         console.log("=====>4732 result", result)
                         resolve(result)

                    }
               })
          })

     } catch (err) {
          console.log(err)
     }
}


/*
Purpose :getPjpBasedOnPincode() Used to get pjps based on pincode
author : Deepak Tiwari
Request : pincode, le_wh_id
Resposne : return beat 
*/
function getPjpBasedOnPincode(pincode, le_wh_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select ppa.pjp_pincode_area_id  from pincode_area as pa join pjp_pincode_area as ppa  ON pa.pjp_pincode_area_id = ppa.pjp_pincode_area_id where pa.pincode =" + pincode
               db.query(query, {}, function (err, beat) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(beat).length > 0) {
                         console.log("=====>4732 result", beat)
                         if (le_wh_id != '') {
                              let query_1 = "select ppa.pjp_pincode_area_id  from pincode_area as pa join pjp_pincode_area as ppa  ON pa.pjp_pincode_area_id = ppa.pjp_pincode_area_id where pa.pincode =" + pincode + " && FIND_IN_SET(ppa.le_wh_id," + le_wh_id + ")"
                              db.query(query_1, {}, function (err, beats) {
                                   if (err) {
                                        console.log(err)
                                        reject(err)
                                   } else if (Object.keys(beats).length > 0) {
                                        resolve(beats)
                                   }
                              })

                         } else {
                              resolve(beat)
                         }
                    }
               })
          })

     } catch (err) {
          console.log(err)
          let error = { "status": "failed", "message ": "Internal server error" }
          return error;
     }


}

module.exports.getAllDcs = function (user_id) {
     try {
          let filters;
          let dcAccessList;
          return new Promise((resolve, reject) => {
               getFilterData(6, user_id).then(DataFilter => {
                    if (DataFilter != '') {
                         filters = DataFilter.dc_type;
                         console.log("filters", filters)
                         dcAccessList = (typeof filters != 'undefined' && filters != '') ? filters : '';
                         let query = "select * FROM legalentity_warehouses AS lw INNER JOIN zone AS z ON lw.state = z.zone_id WHERE lw.dc_type=118001 AND lw.status=1 AND lw.le_wh_id IN (" + dcAccessList + ")";
                         db.query(query, {}, function (err, allDcs) {
                              console.log(query)
                              if (err) {
                                   console.log(err);
                                   reject(err)
                              } else if (Object.keys(allDcs).length > 0) {
                                   resolve(allDcs)
                              } else {
                                   resolve()
                              }
                         })
                    }
               }).catch(err => {
                    console.log(err)
               })
          })

     } catch (err) {
          console.log(err)
     }

}

module.exports.resendOtp = function (telephone, customer_token, custflag, app_flag = 0) {
     return new Promise((resolve, reject) => {
          try {
               let userChk;
               let ffCheck;
               let otpFlag;
               let result;
               let description;
               let userNumRow;
               let userTempNumRow;
               let userId;
               let isActive;
               let query = "select * from users where mobile_no =" + telephone + " and is_active = 1";
               db.query(query, {}, async function (err, response) {
                    console.log("---7171----", response.length)
                    if (err) {
                         console.log(err);
                    } else {

                         if (response.length > 0) {
                              userChk = JSON.parse(JSON.stringify(response[0]));
                              // Checking weather ff is enabled or not
                              ffCheck = await ConfirmOtpcheckPermissionByFeatureCode('EFF001', userChk.user_id);
                              if (ffCheck != 1) {
                                   //checking weather user is ff or self user generated otp
                                   ffCheck = await ConfirmOtpcheckPermissionByFeatureCode('MFF001', userChk.user_id);
                              }
                         } else {
                              ffCheck = -1;
                         }

                         if (ffCheck == 1) {
                              otpFlag = 1;
                              resendGeneratedOtp(telephone, otpFlag, customer_token, custflag, userChk.user_id, app_flag).then(response => {
                                   result = response;
                                   resolve(result);
                              }).catch(err => {
                                   console.log(err);
                              })
                         } else {
                              let masterQuery = "select * from master_lookup as ml where ml.value = 78002";
                              db.query(masterQuery, {}, function (err, desc) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                   } else {
                                        description = desc[0].description;
                                        let query_1 = "select user.user_id,leg.legal_entity_id,leg.is_approved,user.is_active from users as user left join legal_entities as leg ON leg.legal_entity_id  = user.legal_entity_id where user.mobile_no =" + telephone + " && user.is_active = 1 && leg.legal_entity_type_id  IN( select value from master_lookup as ml where ml.mas_cat_id = " + description + " && ml.is_active = 1)";
                                        db.query(query_1, {}, function (err, userDetails) {
                                             if (err) {
                                                  console.log(err);
                                                  reject(err);
                                             } else {
                                                  userNumRow = userDetails.length;
                                                  let userTempQuery = "select * from user_temp where mobile_no =" + telephone;
                                                  db.query(userTempQuery, {}, function (err, userTempResult) {
                                                       if (err) {
                                                            console.log(err);
                                                            reject(err);
                                                       } else {
                                                            userTempNumRow = userTempResult.length;
                                                       }

                                                       //checking user details
                                                       if (userNumRow > 0) {
                                                            userId = userDetails[0].user_id;
                                                            isActive = userDetails[0].is_active;
                                                       } else {
                                                            userId = '';
                                                            isActive = 0;
                                                       }

                                                       console.log(userNumRow, userTempNumRow)
                                                       //If userDetails is null
                                                       if (userNumRow == 0 && customer_token == '' && userTempNumRow == 0) {
                                                            otpFlag = 0;
                                                            resendGeneratedOtp(telephone, otpFlag, customer_token, custflag, userId, app_flag).then(response => {
                                                                 result = response;
                                                                 resolve(result);
                                                            }).catch(err => {
                                                                 console.log(err);
                                                            })
                                                       } else if ((userNumRow > 0 && isActive == 1) || (customer_token != '' && custflag == 2) || userNumRow > 0) {

                                                            otpFlag = 1;
                                                            resendGeneratedOtp(telephone, otpFlag, customer_token, custflag, userId, app_flag).then(response => {
                                                                 result = response;
                                                                 resolve(result);
                                                            }).catch(err => {
                                                                 console.log(err);
                                                            })

                                                       } else {
                                                            otpFlag = 2;
                                                            resendGeneratedOtp(telephone, otpFlag, customer_token, custflag, userId, app_flag).then(response => {
                                                                 result = response;
                                                                 console.log("result", result);
                                                                 resolve(result);
                                                            }).catch(err => {
                                                                 console.log(err);
                                                            })
                                                       }
                                                  })

                                             }
                                        })
                                   }
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



module.exports.loadtest = function () {
     return new Promise((resolve, reject) => {
          try {
               user.find({}).then(userdetails => {
                    if (userdetails != null) {
                         resolve(userdetails);
                    } else {
                         reject('');
                    }
               })

          } catch (err) {
               console.log(err);
               reject(err);
          }

     })

}

