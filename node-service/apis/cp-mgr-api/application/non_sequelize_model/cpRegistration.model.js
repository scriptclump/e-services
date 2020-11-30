const dbconnection = require('../../dbConnection');
const db = dbconnection.DB;
const con = dbconnection.Conn;
const config = require("../../config/config")
var crypto = require('crypto');
const nodemailer = require("nodemailer");
const mongoose = require('mongoose');
//console.log(mongoose.model('reviews'))
const user = mongoose.model('User');
const userTemp = mongoose.model('userTemp');

/*
Purpose : checkPermissionByFeatureCode() Used to check user permission based on userid and featurecode
Author :Deepak Tiwari
Request : featureCode, userId
Resposne : Returns Mobile features
*/
function checkPermissionByFeatureCode(featureCode, userId) {
     try {
          // userId is for superadmin
          if (userId == 1) {
               return true;
          } else {
               let data = "select count(features.name) as count  from role_access JOIN features ON role_access.feature_id = features.feature_id  JOIN user_roles ON role_access.role_id = user_roles.role_id where user_roles.user_id =" + userId + " && features.feature_code ='" + featureCode + "' && features.is_active = 1";
               let count;
               db.query(data, {}, function (err, name) {
                    if (err) {
                         reject(err)
                    } else {
                         count = name[0].count;
                         resolve(count > 0) ? true : false;
                    }
               })
          }
     } catch (err) {
          console.log(err)
     }


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
                              resolve([lpFeatures[0]]);
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
                              resolve([mobileFeatures[0]]);
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
          let random_number = Math.floor(100000 + Math.random() * 999999);
          let mobile_number = phone;
          let app_unique_key = "qoVggl61OKE";
          let message = "<#> Your OTP for Ebutor is  " + random_number + " \n - " + app_unique_key;
          if (mobile_number.length >= 10 && message != "") {
               let user = 'vinil@esealinc.com:eseal@123';
               let receipientno = mobile_number;
               let senderID = 'EBUTOR';
               curl.setOpt(Curl.option.URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
               curl.setOpt('FOLLOWLOCATION', true);
               curl.setOpt(Curl.option.POST, 1);
               curl.setOpt(Curl.option.POSTFIELDS, "user=" + user + "&senderID=" + senderID + "&receipientno=" + receipientno + "&msgtxt=" + message);
               curl.on('end', function (statusCode, body, headers) {
                    console.log(headers, body)
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
                         let query = "insert into  user_temp(mobile_no , otp , legal_entity_type_id , created_at) values (" + mobile_number + ',' + random_number + ',' + buyer_type_id + ',' + formatted_date + ")";
                         db.query(query, {}, function (err, rows) {
                              if (err) {
                                   console.log(err)
                              } else {
                                   res = { message: "Please Confirm  OTP", status: 1 }
                                   resolve(res);
                              }
                         })
                    } else if (otpflag == 1) {
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
                         let query = "update user_temp set otp='" + random_number + "',updated_at ='" + formatted_date + "'where mobile_no ='" + phone + "'";
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
     try {
          return new Promise((resolve, reject) => {
               // userId is for superadmin
               if (userId == 1) {
                    return true;
               } else {
                    let data = "select count(features.name) as count  from role_access JOIN features ON role_access.feature_id = features.feature_id  JOIN user_roles ON role_access.role_id = user_roles.role_id where user_roles.user_id =" + userId + " && features.feature_code ='" + featureCode + "' && features.is_active = 1";
                    let count;
                    db.query(data, {}, function (err, name) {
                         if (err) {
                              reject(err)
                         } else {
                              count = name[0].count;
                              resolve(count > 0) ? true : false;
                         }
                    })
               }
          })

     } catch (err) {
          console.log(err)
     }


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
                         db.query(query, {}, function (err, userchk) {
                              if (err) {
                                   reject(err);
                              } else if (Object.keys(userchk).length > 0) {
                                   if (buyer_type == '') {
                                        buyer_type_id = 3001;
                                   } else {
                                        buyer_type_id = buyer_type;
                                   }
                                   user_chkdet = userchk[0];
                                   ff_check = checkPermissionByFeatureCode('EFF001', user_chkdet.user_id);
                                   srm_check = checkPermissionByFeatureCode('SRM001', user_chkdet.user_id);
                                   customer_chk = checkPermissionByFeatureCode('MCU001', user_chkdet.user_id);
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
                                             if (ff_check == 1 || srm_check == 1 || lp_feature.length != 0 || mobile_feature.length != 0 && customer_chk == 0) {
                                                  console.log("if condition")
                                                  if (sales_token) {
                                                       result = { message: "Already Registered FieldForce", status: 0 }
                                                       resolve(result);

                                                  } else {
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
                                                                      let users_temp_num_rows = result_user_temp.length;

                                                                 }
                                                            })


                                                            if (users_num_rows == 0 && users_temp_num_rows == 0) {
                                                                 otpflag = 0;
                                                                 let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                                 resolve(otp);
                                                            } else if (users_num_rows > 0 && is_active == 1 && sales_token != '') {
                                                                 otpflag = 1;
                                                                 let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                                 resolve(otp);
                                                            } else if (users_num_rows > 0 && sales_token != "" || users_num_rows > 0 && is_active == 0) {
                                                                 let result_1 = {};
                                                                 if (is_active == 1) {
                                                                      result_1 = { message: "You are already registered with us.Please login.", status: 0 };
                                                                 } else {
                                                                      result1 = { message: "We are sorry your shop is not being serviced at the moment.", status: 0 };
                                                                 }
                                                                 resolve(result_1);

                                                            } else {
                                                                 otpflag = 2;
                                                                 let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                                 resolve(otp);
                                                            }

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
                                   ff_check = 0;
                                   srm_check = 0;
                                   customer_chk = 0;
                                   lp_feature = [];
                                   mobile_feature = [];
                                   if (ff_check == 1 || srm_check == 1 || lp_feature.length != 0 || mobile_feature.length != 0 && customer_chk == 0) {
                                        console.log("if condition")
                                        if (sales_token) {
                                             result = { message: "Already Registered FieldForce", status: 0 }
                                             resolve(result);

                                        } else {
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
                                                            let users_temp_num_rows = result_user_temp.length;

                                                       }
                                                  })


                                                  if (users_num_rows == 0 && users_temp_num_rows == 0) {
                                                       otpflag = 0;
                                                       let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                       resolve(otp);
                                                  } else if (users_num_rows > 0 && is_active == 1 && sales_token != '') {
                                                       otpflag = 1;
                                                       let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                       resolve(otp);
                                                  } else if (users_num_rows > 0 && sales_token != "" || users_num_rows > 0 && is_active == 0) {
                                                       let result_1 = {};
                                                       if (is_active == 1) {
                                                            result_1 = { message: "You are already registered with us.Please login.", status: 0 };
                                                       } else {
                                                            result1 = { message: "We are sorry your shop is not being serviced at the moment.", status: 0 };
                                                       }
                                                       resolve(result_1);

                                                  } else {
                                                       otpflag = 2;
                                                       let otp = generateOtp(customer_id, telephone, buyer_type_id, otpflag);
                                                       resolve(otp);
                                                  }

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
          var host = 'mongodb://' + config['MONGO_USER'] + ":" + config['MONGO_PASSWORD'] + "@" + config['MONGO_HOST'] + ":" + config['MONGO_PORT'] + "/" + config['MONGO_DATABASE'];
          MongoClient.connect(host,
               function (err, db) {
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
                    db.collection('container_api_logs').insertOne(body, function (err, res) {
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
          let result;
          return new Promise((resolve, reject) => {
               let query = "select * from users us where us.otp =" + otp + " && us.mobile_no=" + telephone;
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
          let result;
          return new Promise((resolve, reject) => {
               let query = "select * from user_temp ustmp where ustmp.otp =" + otp + " && ustmp.mobile_no=" + telephone;
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
                         db.query(query, {}, function (err, userchk) {
                              if (err) {
                                   reject(err);
                              } else if (Object.keys(userchk).length > 0) {
                                   user_chkdet = userchk[0];
                                   ff_check = checkPermissionByFeatureCode('EFF001', user_chkdet.user_id);//Enabled Field Force
                                   srm_check = checkPermissionByFeatureCode('SRM001', user_chkdet.user_id);//SRM Enabled
                                   customer_chk = checkPermissionByFeatureCode('MCU001', user_chkdet.user_id);//Retailers who is registered in ebutor	
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
                                   console.log("627")
                                   ff_check = 0;
                                   srm_check = 0;
                                   customer_chk = 0;
                                   lp_feature = [];
                                   mobile_feature = [];
                              }
                              if (ff_check == 1 || srm_check == 1 || lp_feature != 0 || mobile_feature != 0 && customer_chk == 0) {
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
                                   console.log("646");
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
               let data = "insert into device_details (user_id, device_id, app_id, ip_address, registration_id, platform_id, created_at, updated_at) values (" + user_id + ',' + "'" + device_id + "'" + ',' + 0 + ',' + "'" + ip_address + "','" + reg_id + "'," + platform_id + ',' + "'" + formatted_date + "'" + ',' + "'" + formatted_date + "') ON DUPLICATE KEY UPDATE  user_id = '" + user_id + "', ip_address = '" + ip_address + "', registration_id = '" + reg_id + "', platform_id = '" + platform_id + "', updated_at = '" + formatted_date + "'";
               db.query(data, {}, function (err, row) {
                    if (err) {
                         reject(err)
                    } else {
                         console.log("Device id inserted Successfully")
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
                         let data = "select role_id from roles r where r.role_id in (" + currentRoles + ") && r.is_support_role =" + 1;
                         db.query(data, {}, function (err, rows) {
                              if (err) {
                                   return reject(err);
                              }
                              else if (Object.keys(rows).length > 0) {
                                   string = JSON.stringify(rows)
                                   json = JSON.parse(string)
                                   return resolve(json);

                              }
                         })
                    } else {
                    }

               }).catch((err) => {
                    console.log(err)
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
                    if (supportRole != null) {
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
                                                                 response = userList;
                                                                 resolve(response);
                                                            }
                                                       });
                                                  }
                                             });

                                        } else {
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
                                        resolve(response);
                                   }
                              });
                         }
                    }
               }).catch((err) => {
                    console.log(err)
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
function getTeamByUser(userId) {
     let response = []
     try {
          return new Promise((resolve, reject) => {
               response = userId;
               if (userId > 0) {
                    getTeamList(userId).then((childUserList) => {
                         if (childUserList != null) {
                              response = childUserList
                         }
                    }).catch((err) => {
                         reject(err)
                    })
               }
               resolve(response);
          })
     } catch (err) {
          reject(err)
     }
}

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
                         }
                    })
               }


          })

     } catch (err) {
          console.log(err)
     }
}

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

function getLeFromBeat(beat, latitude, longitude) {
     return new Promise((resolve, reject) => {
          try {
               let query = "SELECT l.legal_entity_id FROM beat_master bm INNER JOIN legalentity_warehouses l ON bm.hub_id = l.le_wh_id WHERE bm.beat_id=" + beat;
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(response).length > 0) {
                         let result = "";
                         if (response.length > 1) {
                              response.forEach(element => {
                                   result = element.legal_entity_id + ',' + result;
                              })
                              result = result.slice(0, result.length - 1);

                              let warehouseDetails = "CALL getWarehouseByCatDist(" + latitude + "," + longitude + ",'" + result + "')";
                              db.query(warehouseDetails, {}, function (err, Warehouseresult) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                   } else if (Object.keys(Warehouseresult).length > 0) {
                                        let warehouseainfo = Warehouseresult[0];
                                        resolve(warehouseainfo[0].legal_entity_id);
                                   } else {
                                        resolve(0);
                                   }
                              })

                         } else {
                              resolve(response[0].legal_entity_id);
                         }


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
//
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
                         }
                    })

               }
          })

     } catch (err) {
          console.log(err)
     }
}

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

function getProductsByUser(userId, permissionLevelId, legalEntityId) {
     try {
          let response;
          return new Promise((resolve, reject) => {
               if (userId > 0 && permissionLevelId > 0) {
                    getUserPermission(userId, 8).then(allCategoryPermission => {
                         if (allCategoryPermission) {
                              let data = "select products.product_title , products.product_id from products where products.legal_entity_id =" ///+ legalEntityId;
                              db.query(data, {}, function (err, res) {

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
                         }
                    })
               }

          })
     } catch (err) {
          console.log(err)
     }
}

function getWarehouseData(currentUserId, permissionLevelId, active = 1) {
     try {
          let response = {};
          let globalFeature;
          let inActiveDCAccess;
          let query = [];
          return new Promise((resolve, reject) => {
               if (currentUserId > 0 && permissionLevelId > 0) {
                    globalFeature = checkPermissionByFeatureCode('GLB0001', currentUserId);
                    inActiveDCAccess = checkPermissionByFeatureCode('GLBWH0001', currentUserId);

                    if (active == 0) {
                         inActiveDCAccess = 1;
                    }
                    let data = "select object_id from user_permssion where user_id =" + currentUserId + "&& permission_level_id =" + permissionLevelId + " group by object_id";
                    db.query(data, {}, function (err, result) {
                         if (err) {
                              reject(err);
                         } else {
                              let Data = "select * from legalentity_warehouses where dc_type  > 0";
                              db.query(Data, {}, function (err, res) {
                                   if (err) {
                                        reject(err);
                                   } else if (Object.keys(res).length > 0) {
                                        query.push(res)
                                        if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
                                             console.log("=======1149", inActiveDCAccess);
                                             let query_1 = "select * from legalentity_warehouses where dc_type  > 0 && status = 1";
                                             db.query(query_1, {}, function (err, rows) {
                                                  if (err) {
                                                       reject(err)
                                                  } else if (Object.keys(rows).length > 0) {
                                                       query.push(rows) //query returns only active records
                                                  }
                                             })
                                        }

                                        if (globalFeature) {
                                             if (result.length == 1 || 0 in result) {
                                                  if (typeof result[0] != 'undefined' && (result[0] == 0 || 0 in result)) {
                                                       let query_1 = "select * from legalentity_warehouses where dc_type IN (118001, 118002)";
                                                       db.query(query_1, {}, function (err, rows) {
                                                            if (err) {
                                                                 reject(err)
                                                            } else if (Object.keys(rows).length > 0) {
                                                                 query.push(rows)
                                                            }
                                                       })
                                                  } else {
                                                       let query_1 = "select * from legalentity_warehouses where bu_id IN" + result + "&& dc_type  > 0";
                                                       db.query(query_1, {}, function (err, rows) {
                                                            if (err) {
                                                                 reject(err)
                                                            } else if (Object.keys(rows).length > 0) {
                                                                 query.push(rows)
                                                            }

                                                       })
                                                  }
                                             } else {
                                                  let query_1 = "select * from legalentity_warehouses where bu_id IN" + result + "&& dc_type  > 0";
                                                  db.query(query_1, {}, function (err, rows) {
                                                       if (err) {
                                                            reject(err)
                                                       } else if (Object.keys(rows).length > 0) {
                                                            query.push(rows)
                                                       }

                                                  })
                                             }

                                             let update_query = "SET SESSION group_concat_max_len = 100000";
                                             db.query(update_query, {}, function (err, row) {
                                                  if (err) {
                                                       reject(err)
                                                  } else {
                                                       console.log("session updated")
                                                  }
                                             })

                                             let data_1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
                                             db.query(data_1, {}, function (err, rows) {
                                                  if (err) {
                                                       reject(err)
                                                  } else if (Object.keys(rows).length > 0) {
                                                       query.push(rows);
                                                       if (query.length > 0) {
                                                            query[0].forEach((element) => {
                                                                 response = { dc_type: element.le_wh_id }
                                                            })
                                                            resolve(response);
                                                       }
                                                  }
                                             })
                                        } else if (!globalFeature) {
                                             let data_2 = "select * from legalentity_warehouses where dc_type > 0";
                                             db.query(data_2, {}, function (err, row) {
                                                  if (err) {
                                                       reject(err)
                                                  } else if (Object.keys(row).length > 0) {
                                                       query.push(row)
                                                  }
                                             })
                                             if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
                                                  let data2 = "select * from legalentity_warehouses where dc_type > 0 && status = 1";
                                                  db.query(data2, {}, function (err, row) {
                                                       if (err) {
                                                            reject(err)
                                                       } else if (Object.keys(row).length > 0) {
                                                            query.push(row)
                                                       }
                                                  })
                                             }

                                             let update_query = "SET SESSION group_concat_max_len = 100000";
                                             db.query(update_query, {}, function (err, row) {
                                                  if (err) {
                                                       reject(err)
                                                  } else {
                                                       console.log("session updated")
                                                  }
                                             })

                                             let data1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
                                             db.query(data1, {}, function (err, rows) {
                                                  if (err) {
                                                       reject(err)
                                                  } else if (Object.keys(rows).length > 0) {
                                                       query.push(rows);
                                                       if (query.length > 0) {
                                                            query[0].forEach((element) => {
                                                                 response = { dc_type: element.le_wh_id }
                                                            })
                                                            resolve(response);
                                                       }
                                                  }
                                             })
                                        }
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
                         }
                    })
               }

          })
     } catch (err) {
          console.log(err)
     }

}

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
                              console.log("1472", getPermissionLevelName[0].name);
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
                                                       response = { getPermissionLevelName: result }
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
                                             response = { getPermissionLevelName: result }
                                             resolve(response.getPermissionLevelName)
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

function getEcashInfo(user_id) {
     try {
          let ecash_user_data;
          return new Promise((resolve, reject) => {
               if (user_id != null) {
                    let query = "select creditlimit,(cashback-applied_cashback) as ecash,payment_due from user_ecash_creditlimit where user_id =" + user_id + " limit 1"
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(rows).length > 0) {
                              ecash_user_data = rows;
                              resolve(ecash_user_data[0]);
                         } else {
                              resolve('');
                         }
                    })


               }
          })

     } catch (err) {
          console.log(err)
     }
}

function getWarehouseIdByMobileNo(mobile_no) {
     return new Promise((resolve, reject) => {
          let data = "select d.dc_id from retailer_flat r JOIN pjp_pincode_area p ON r.beat_id = p.pjp_pincode_area_id JOIN dc_hub_mapping d ON d.hub_id = p.le_wh_id where r.mobile_no =" + mobile_no;
          db.query(data, {}, function (err, warehouse_id) {
               if (err) {
                    reject(err);
               } else if (Object.keys(warehouse_id).length > 0) {
                    resolve(warehouse_id[0].dc_id);
               } else {
                    resolve('');
               }
          })
     })
}

function getHub(beat_id) {
     return new Promise((resolve, reject) => {
          if (beat_id) {
               let data = " select distinct lew.le_wh_id as hub  from pjp_pincode_area as ppa  join legalentity_warehouses as lew ON ppa.le_wh_id = lew.le_wh_id  where ppa.pjp_pincode_area_id =" + beat_id + "&& lew.dc_type =118002 ";
               db.query(data, {}, function (err, hub) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(hub).length > 0) {
                         if (hub[0].hub) {
                              resolve(hub[0].hub);
                         } else {
                              resolve('');
                         }
                    }
               })
          }
     })
}

function getHub_New(beat_id) {
     // return new Promise((resolve, reject) => {
     //      let result = "";
     //      if (legal_Entity_id) {
     //           let data = "SELECT le_wh_id FROM legalentity_warehouses AS lew  JOIN legal_entities AS le ON lew.legal_entity_id = le.parent_le_id  AND le.legal_entity_id =" + legal_Entity_id;
     //           db.query(data, {}, function (err, hub) {
     //                if (err) {
     //                     reject(err)
     //                } else if (Object.keys(hub).length > 0) {
     //                     hub.forEach(element => {
     //                          result = element.le_wh_id + ',' + result;
     //                     })
     //                     result = result.slice(0, result.length - 1);
     //                     resolve(result);
     //                     // console.log(hub);
     //                     // if (hub[0].le_wh_id) {
     //                     //      resolve(hub.le_wh_id);
     //                     // } else {
     //                     //      resolve('');
     //                     // }
     //                }
     //           })
     //      }
     // })


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


function logApiRequests1(data) {
     return new Promise((resolve, reject) => {
          var MongoClient = require('mongodb').MongoClient;
          var host = 'mongodb://' + config['MONGO_USER'] + ":" + config['MONGO_PASSWORD'] + "@" + config['MONGO_HOST'] + ":" + config['MONGO_PORT'] + "/" + config['MONGO_DATABASE'];
          MongoClient.connect(host, function (err, db) {
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
               db.collection('container_api_logs').insertOne(body, function (err, res) {
                    if (err) throw err;
                    db.close();
               });
          });
     });

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
          let customer_token;
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
                              db.query(data_query, {}, function (err, userchk) {
                                   if (err) {
                                        reject(err);
                                   } else {
                                        if (userchk) {
                                             console.log("User details found")
                                             user_chkdet = userchk[0];
                                             ff_check = checkPermissionByFeatureCode('EFF001', user_chkdet.user_id);//Enabled field force
                                             srm_check = checkPermissionByFeatureCode('SRM001', user_chkdet.user_id);//Supplier relationship Management
                                             customer_chk = checkPermissionByFeatureCode('MCU001', user_chkdet.user_id);//retailers who is registered with ebutor
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
                                                            } else if (Object.keys(mfc_data).length > 0) {
                                                                 if (typeof mfc_data[0].mfc_id != 'undefined' && mfc_data[0].mfc_id != '') {
                                                                      mfc = mfc_data[0].mfc_id;
                                                                 } else {
                                                                      mfc = 0;
                                                                 }
                                                                 if (device_id != null) {
                                                                      InsertDeviceDetails(user_chkdet.user_id, device_id, ip_address, platform_id, reg_id);
                                                                 }
                                                                 let query_2 = "select creditlimit from user_ecash_creditlimit where le_id =" + user_chkdet.legal_entity_id;
                                                                 db.query(query_2, {}, function (err, creditlimit) {
                                                                      if (err) {
                                                                           reject(err)
                                                                      } else {
                                                                           if (creditlimit.length > 0) {
                                                                                creditlimit = creditlimit[0].creditlimit;
                                                                           } else {
                                                                                creditlimit = 0;
                                                                           }

                                                                           if (ff_check == 1 || srm_check == 1 || lp_feature != null || (mobile_feature != null && customer_chk == 0)) {
                                                                                console.log("if condition")
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
                                                                                customer_token = crypto.createHash('md5').update(phonenumber).digest("hex");
                                                                                dashboard = checkPermissionByFeatureCode('FFD001', user_chkdet.user_id);//feild force DashBoard
                                                                                new_dashboard = checkPermissionByFeatureCode('MFD001', user_chkdet.user_id);//	Here it displays the beats assigned to ff on that day and the outlets assigned to that beats.	

                                                                                if (dashboard == 1 || new_dashboard == 1) {
                                                                                     is_dashboard = 1;
                                                                                     getTeamByUser(user_chkdet.user_id).then(team => {
                                                                                          let key = user_chkdet.user_id.hasOwnProperty(team)
                                                                                          if (key != false) {
                                                                                               delete team.key
                                                                                               if (team.length >= 1) {
                                                                                                    has_child = 1;
                                                                                               } else {
                                                                                                    has_child = 0;
                                                                                               }
                                                                                          }
                                                                                     }).catch(err => {
                                                                                          console.log(err);
                                                                                          reject({ status: "failed", message: "Something went wrong" })
                                                                                     })
                                                                                } else {
                                                                                     is_dashboard = 0;
                                                                                     has_child = 0;
                                                                                }

                                                                                if (module_id == 1) {
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
                                                                                     //     if (DataFilter.length > 0) {
                                                                                     decode_data = DataFilter;
                                                                                     sbu_lits = typeof decode_data.sbu != 'undefined' ? decode_data.sbu : [];
                                                                                     decode_sbulist = sbu_lits;
                                                                                     hub = (typeof decode_sbulist[118002] != 'undefined' && decode_data[118002]) ? decode_data[118002] : '';
                                                                                     le_wh_id = (typeof decode_sbulist[118001] != 'undefined' && decode_data[118001]) ? decode_sbulist[118001] : '';
                                                                                     //  }
                                                                                     //response body 

                                                                                     let ff_ecash_details;
                                                                                     getEcashInfo(user_chkdet.user_id).then(result => {
                                                                                          ff_ecash_details = result;

                                                                                          let promo = [];
                                                                                          let format_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate()
                                                                                          let query = "select COUNT(*) as count FROM promotion_cashback_details pc  JOIN legalentity_warehouses lw  ON lw.le_wh_id =   pc.wh_id AND lw.legal_entity_id = " + user_chkdet.legal_entity_id + " WHERE cbk_status = 1 AND is_self in (0, 2) AND '" + format_date + "' BETWEEN start_date AND end_date"; /* */
                                                                                          db.query(query, {}, function (err, res) {
                                                                                               if (err) {
                                                                                                    reject(err)
                                                                                               } else if (Object.keys(res).length > 0) {
                                                                                                    promo.push(res);
                                                                                               }
                                                                                               mobile_feature_array
                                                                                               if (mobile_feature.length > 0) {
                                                                                                    mobile_feature_array = JSON.parse(JSON.stringify(mobile_feature[[0]]))
                                                                                               }
                                                                                               let promo_array = JSON.parse(JSON.stringify(promo[[0]]))

                                                                                               response_data = {
                                                                                                    customer_group_id: '', customer_token: customer_token, customer_id: user_chkdet.user_id, legal_entity_id: user_chkdet.legal_entity_id, parent_le_id: user_chkdet.parent_le_id,
                                                                                                    firstname: user_chkdet.firstname, lastname: user_chkdet.lastname, image: profile_picture, segment_id: '', pincode: '', le_wh_id: le_wh_id, hub: hub,
                                                                                                    is_active: user_chkdet.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: is_dashboard, has_child: has_child, lp_feature: lp_feature, mobile_feature: mobile_feature_array,
                                                                                                    beat_id: '', latitude: '', longitude: '', ff_ecash_details: ff_ecash_details, mfc: mfc, ff_full_name: user_chkdet.firstname + '_' + user_chkdet.lastname,
                                                                                                    ff_profile_pic: user_chkdet.profile_picture, credit_limit: creditlimit, aadhar_id: user_chkdet.aadhar_id, promotion_count: promo_array[0].count
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
                                                                                console.log("else condition")
                                                                                let business_type_id;
                                                                                let pincode;
                                                                                let legal_entity_type_id;
                                                                                // let le_wh_id;
                                                                                let parent_le_id;
                                                                                is_ff = 0;
                                                                                is_srm = 0;
                                                                                let data_7 = "select us.* , le.is_approved  from users as us  LEFT JOIN  legal_entities as le ON le.legal_entity_id =  us.legal_entity_id where us.mobile_no =" + phonenumber + " && le.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc + " && ml.is_active = 1 )";
                                                                                db.query(data_7, {}, function (err, user_leg) {
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
                                                                                               let customer_token = crypto.createHash('md5').update(phonenumber).digest("hex");
                                                                                               let que = "update users as us set password_token ='" + customer_token + "' , updated_at ='" + formatted_date + "' where us.user_id =" + customer_id;
                                                                                               db.query(que, {}, (err, updated) => {
                                                                                                    if (err) {
                                                                                                         console.log(err)
                                                                                                         reject(err)
                                                                                                    } else {
                                                                                                         console.log("updated Successfully")
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

                                                                                               getWarehouseIdByMobileNo(phonenumber).then((le_wh_id_1) => {
                                                                                                    if (le_wh_id != null) {
                                                                                                         le_wh_id = '';
                                                                                                    } else {
                                                                                                         le_wh_id = le_wh_id_1;
                                                                                                    }

                                                                                               }).catch(err => {
                                                                                                    console.log(err);
                                                                                               })
                                                                                          }

                                                                                          let queu = "select beat_id from customers where le_id =" + legal_entity_id;
                                                                                          db.query(queu, {}, function (err, beat) {
                                                                                               if (err) {
                                                                                                    reject(err)
                                                                                               } else if (Object.keys(beat).length > 0) {
                                                                                                    beat_id = beat
                                                                                                    let hub = '';
                                                                                                    let ecash_details;
                                                                                                    if (beat_id != null && beat_id.length > 0) {
                                                                                                         getHub(beat_id[0].beat_id).then((hub_value) => {
                                                                                                              if (hub_value != null) {
                                                                                                                   hub = hub_value
                                                                                                              } else {
                                                                                                                   hub = '';
                                                                                                              }
                                                                                                              //used to getEcashinfo
                                                                                                              getEcashInfo(user_det.user_id).then(result => {
                                                                                                                   ecash_details = result
                                                                                                                   let promo;
                                                                                                                   let format_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
                                                                                                                   if (le_wh_id != '') {
                                                                                                                        let query_3 = "select  count(*) as count from promotion_cashback_details where wh_id =" + le_wh_id + "&& cbk_status=1 &&  is_self in (1,2) && '" + format_date + "'between start_date and end_date && customer_type like '%" + legal_entity_type_id + "%'";//le_wh_id
                                                                                                                        db.query(query_3, {}, function (err, promotion_data) {
                                                                                                                             console.log(query_3)
                                                                                                                             if (err) {
                                                                                                                                  reject(err)
                                                                                                                             } else if (Object.keys(promotion_data).length > 0) {
                                                                                                                                  promo = promotion_data;
                                                                                                                                  let mobile_feature_array_1 = JSON.parse(JSON.stringify(mobile_feature[[0]]))
                                                                                                                                  function_response = { customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_det.user_id, legal_entity_id: user_det.legal_entity_id, parent_le_id: parent_le_id, firstname: user_det.firstname, lastname: user_det.lastname, image: profile_picture, segment_id: business_type_id, pincode: pincode, le_wh_id: le_wh_id, hub: hub, is_active: user_det.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: 0, lp_feature: [], mobile_feature: mobile_feature_array_1, beat_id: beat_id[0].beat_id, latitude: segment_det[0].latitude, longitude: segment_det[0].longitude, ecash_details: ecash_details, ff_full_name: user_det.firstname + ' ' + user_det.lastname, ff_profile_pic: user_det.profile_picture, mfc: mfc, credit_limit: creditlimit, aadhar_id: user_det.aadhar_id, promotion_count: promo[0].count }
                                                                                                                                  let res1 = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 1, data: function_response };
                                                                                                                                  resolve(res1);
                                                                                                                             }
                                                                                                                        })
                                                                                                                   } else {
                                                                                                                        let mobile_feature_array_1 = JSON.parse(JSON.stringify(mobile_feature[[0]]));
                                                                                                                        function_response = {
                                                                                                                             customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_det.user_id, legal_entity_id: user_det.legal_entity_id,
                                                                                                                             parent_le_id: parent_le_id, firstname: user_det.firstname, lastname: user_det.lastname, image: profile_picture, segment_id: business_type_id, pincode: pincode,
                                                                                                                             le_wh_id: le_wh_id, hub: hub, is_active: user_det.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: 0, lp_feature: [], mobile_feature: mobile_feature_array_1,
                                                                                                                             beat_id: beat_id[0].beat_id, latitude: segment_det[0].latitude, longitude: segment_det[0].longitude, ecash_details: ecash_details, ff_full_name: user_det.firstname + ' ' + user_det.lastname,
                                                                                                                             ff_profile_pic: user_det.profile_picture, mfc: mfc, credit_limit: creditlimit, aadhar_id: user_det.aadhar_id, promotion_count: 0
                                                                                                                        }
                                                                                                                        let res1 = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 1, data: function_response };
                                                                                                                        resolve(res1);
                                                                                                                   }

                                                                                                              }).catch(err => {
                                                                                                                   console.log(err)
                                                                                                              })

                                                                                                         }).catch(err => {
                                                                                                              console.log(err)
                                                                                                         })
                                                                                                    }
                                                                                               }
                                                                                          })
                                                                                     }
                                                                                })
                                                                           }
                                                                      }
                                                                 })
                                                            } else {
                                                                 // console.log("mfc is empty", (ff_check == 1 || srm_check == 1 || lp_feature.length > 0) || (mobile_feature.length > 0 && customer_chk == 0))
                                                                 mfc = 0;
                                                                 if (device_id != null) {
                                                                      InsertDeviceDetails(user_chkdet.user_id, device_id, ip_address, platform_id, reg_id);
                                                                 }
                                                                 let query_2 = "select creditlimit from user_ecash_creditlimit where le_id =" + user_chkdet.legal_entity_id;
                                                                 db.query(query_2, {}, function (err, creditlimit) {
                                                                      if (err) {
                                                                           reject(err)
                                                                      } else {
                                                                           if (creditlimit.length > 0) {
                                                                                creditlimit = creditlimit[0].creditlimit;
                                                                           } else {
                                                                                creditlimit = 0;
                                                                           }
                                                                           //checking user features
                                                                           if (ff_check == 1 || srm_check == 1 || lp_feature != null || (lp_feature != null && customer_chk == 0)) {
                                                                                console.log("2047 if condition")
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
                                                                                customer_token = crypto.createHash('md5').update(phonenumber).digest("hex");
                                                                                dashboard = checkPermissionByFeatureCode('FFD001', user_chkdet.user_id);//feild force DashBoard
                                                                                new_dashboard = checkPermissionByFeatureCode('MFD001', user_chkdet.user_id);//	Here it displays the beats assigned to ff on that day and the outlets assigned to that beats.	
                                                                                console.log("new_dashBoard", dashboard, new_dashboard);
                                                                                if (dashboard == 1 || new_dashboard == 1) {
                                                                                     is_dashboard = 1;
                                                                                     getTeamByUser(user_chkdet.user_id).then(team => {
                                                                                          let key = user_chkdet.user_id.hasOwnProperty(team)
                                                                                          if (key != false) {
                                                                                               delete team.key
                                                                                               if (team.length >= 1) {
                                                                                                    has_child = 1;
                                                                                               } else {
                                                                                                    has_child = 0;
                                                                                               }
                                                                                          }
                                                                                     }).catch(err => {
                                                                                          console.log(err);
                                                                                          reject({ status: "failed", message: "Something went wrong" })
                                                                                     })
                                                                                } else {
                                                                                     is_dashboard = 0;
                                                                                     has_child = 0;
                                                                                }

                                                                                if (module_id == 1) {
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

                                                                                     //  if (DataFilter.length > 0) {
                                                                                     decode_data = DataFilter;
                                                                                     sbu_lits = typeof decode_data.sbu != 'undefined' ? decode_data.sbu : [];
                                                                                     decode_sbulist = sbu_lits;
                                                                                     hub = (typeof decode_sbulist[118002] != 'undefined' && decode_data[118002]) ? decode_data[118002] : '';
                                                                                     le_wh_id = (typeof decode_sbulist[118001] != 'undefined' && decode_data[118001]) ? decode_sbulist[118001] : '';
                                                                                     //  }
                                                                                     //response body 

                                                                                     let ff_ecash_details;
                                                                                     getEcashInfo(user_chkdet.user_id).then(result => {
                                                                                          ff_ecash_details = result;
                                                                                          let promo = [];
                                                                                          let format_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate()
                                                                                          let query = "select COUNT(*) as count FROM promotion_cashback_details pc  JOIN legalentity_warehouses lw  ON lw.le_wh_id =   pc.wh_id AND lw.legal_entity_id = " + user_chkdet.legal_entity_id + " WHERE cbk_status = 1 AND is_self in (0, 2) AND '" + format_date + "' BETWEEN start_date AND end_date"; /* */
                                                                                          db.query(query, {}, function (err, res) {
                                                                                               if (err) {
                                                                                                    reject(err)
                                                                                               } else if (Object.keys(res).length > 0) {
                                                                                                    promo.push(res);
                                                                                               }
                                                                                               mobile_feature_array
                                                                                               if (mobile_feature.length > 0) {
                                                                                                    mobile_feature_array = JSON.parse(JSON.stringify(mobile_feature[[0]]))
                                                                                               }
                                                                                               let promo_array = JSON.parse(JSON.stringify(promo[[0]]))
                                                                                               response_data = {
                                                                                                    customer_group_id: '', customer_token: customer_token, customer_id: user_chkdet.user_id, legal_entity_id: user_chkdet.legal_entity_id, parent_le_id: user_chkdet.parent_le_id,
                                                                                                    firstname: user_chkdet.firstname, lastname: user_chkdet.lastname, image: profile_picture, segment_id: '', pincode: '', le_wh_id: le_wh_id, hub: hub,
                                                                                                    is_active: user_chkdet.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: is_dashboard, has_child: has_child, lp_feature: lp_feature, mobile_feature: mobile_feature_array,
                                                                                                    beat_id: '', latitude: '', longitude: '', ff_ecash_details: ff_ecash_details, mfc: mfc, ff_full_name: user_chkdet.firstname + '_' + user_chkdet.lastname,
                                                                                                    ff_profile_pic: user_chkdet.profile_picture, credit_limit: creditlimit, aadhar_id: user_chkdet.aadhar_id, promotion_count: promo_array[0].count
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
                                                                                console.log("else condition")
                                                                                let business_type_id;
                                                                                let pincode;
                                                                                let legal_entity_type_id;
                                                                                // let le_wh_id;
                                                                                let parent_le_id;
                                                                                is_ff = 0;
                                                                                is_srm = 0;
                                                                                let data_7 = "select us.* , le.is_approved  from users as us  LEFT JOIN  legal_entities as le ON le.legal_entity_id =  us.legal_entity_id where us.mobile_no =" + phonenumber + " && le.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc + " && ml.is_active = 1 )";
                                                                                db.query(data_7, {}, function (err, user_leg) {
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
                                                                                               let customer_token = crypto.createHash('md5').update(phonenumber).digest("hex");
                                                                                               let que = "update users as us set password_token ='" + customer_token + "' , updated_at ='" + formatted_date + "' where us.user_id =" + customer_id;
                                                                                               db.query(que, {}, (err, updated) => {
                                                                                                    if (err) {
                                                                                                         console.log(err)
                                                                                                         reject(err)
                                                                                                    } else {
                                                                                                         console.log("updated Successfully")
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

                                                                                               getWarehouseIdByMobileNo(phonenumber).then((le_wh_id_1) => {
                                                                                                    if (le_wh_id != null) {
                                                                                                         le_wh_id = '';
                                                                                                    } else {
                                                                                                         le_wh_id = le_wh_id_1;
                                                                                                    }

                                                                                               }).catch(err => {
                                                                                                    console.log(err);
                                                                                               })
                                                                                          }

                                                                                          let queu = "select beat_id from customers where le_id =" + legal_entity_id;
                                                                                          db.query(queu, {}, function (err, beat) {
                                                                                               if (err) {
                                                                                                    reject(err)
                                                                                               } else if (Object.keys(beat).length > 0) {
                                                                                                    beat_id = beat
                                                                                                    let hub = '';
                                                                                                    let ecash_details;
                                                                                                    if (beat_id != null && beat_id.length > 0) {
                                                                                                         getHub(beat_id[0].beat_id).then((hub_value) => {
                                                                                                              if (hub_value != null) {
                                                                                                                   hub = hub_value
                                                                                                              } else {
                                                                                                                   hub = '';
                                                                                                              }
                                                                                                              //used to getEcashinfo
                                                                                                              getEcashInfo(user_det.user_id).then(result => {
                                                                                                                   ecash_details = result
                                                                                                                   let promo;
                                                                                                                   let format_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
                                                                                                                   if (le_wh_id != '') {
                                                                                                                        let query_3 = "select  count(*) as count from promotion_cashback_details where wh_id =" + le_wh_id + "&& cbk_status=1 &&  is_self in (1,2) && '" + format_date + "'between start_date and end_date && customer_type like '%" + legal_entity_type_id + "%'";//le_wh_id
                                                                                                                        db.query(query_3, {}, function (err, promotion_data) {
                                                                                                                             if (err) {
                                                                                                                                  reject(err)
                                                                                                                             } else if (Object.keys(promotion_data).length > 0) {
                                                                                                                                  promo = promotion_data;
                                                                                                                                  let mobile_feature_array_1 = JSON.parse(JSON.stringify(mobile_feature[[0]]))
                                                                                                                                  function_response = { customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_det.user_id, legal_entity_id: user_det.legal_entity_id, parent_le_id: parent_le_id, firstname: user_det.firstname, lastname: user_det.lastname, image: profile_picture, segment_id: business_type_id, pincode: pincode, le_wh_id: le_wh_id, hub: hub, is_active: user_det.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: 0, lp_feature: [], mobile_feature: mobile_feature_array_1, beat_id: beat_id[0].beat_id, latitude: segment_det[0].latitude, longitude: segment_det[0].longitude, ecash_details: ecash_details, ff_full_name: user_det.firstname + ' ' + user_det.lastname, ff_profile_pic: user_det.profile_picture, mfc: mfc, credit_limit: creditlimit, aadhar_id: user_det.aadhar_id, promotion_count: promo[0].count }
                                                                                                                                  let res1 = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 1, data: function_response };
                                                                                                                                  resolve(res1);
                                                                                                                             }
                                                                                                                        })
                                                                                                                   } else {
                                                                                                                        let mobile_feature_array_1 = JSON.parse(JSON.stringify(mobile_feature[[0]]));
                                                                                                                        function_response = {
                                                                                                                             customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_det.user_id, legal_entity_id: user_det.legal_entity_id,
                                                                                                                             parent_le_id: parent_le_id, firstname: user_det.firstname, lastname: user_det.lastname, image: profile_picture, segment_id: business_type_id, pincode: pincode,
                                                                                                                             le_wh_id: le_wh_id, hub: hub, is_active: user_det.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: 0, lp_feature: [], mobile_feature: mobile_feature_array_1,
                                                                                                                             beat_id: beat_id[0].beat_id, latitude: segment_det[0].latitude, longitude: segment_det[0].longitude, ecash_details: ecash_details, ff_full_name: user_det.firstname + ' ' + user_det.lastname,
                                                                                                                             ff_profile_pic: user_det.profile_picture, mfc: mfc, credit_limit: creditlimit, aadhar_id: user_det.aadhar_id, promotion_count: 0
                                                                                                                        }
                                                                                                                        let res1 = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 1, data: function_response };
                                                                                                                        resolve(res1);
                                                                                                                   }

                                                                                                              }).catch(err => {
                                                                                                                   console.log(err)
                                                                                                              })

                                                                                                         }).catch(err => {
                                                                                                              console.log(err)
                                                                                                         })
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
                                                  }).catch(err => {
                                                       console.log(err);
                                                       reject({ status: "failed", message: "Something went wrong" })
                                                  })
                                             }).catch(err => {
                                                  console.log(err);
                                                  reject({ status: "failed", message: "Something went wrong" })
                                             })
                                        } else {
                                             ff_check = 0;
                                             srm_check = 0;
                                             customer_chk = 0;
                                             lp_feature = [];
                                             mobile_feature = [];
                                             mfc = 0;
                                             creditlimit = 0;
                                             if (ff_check == 1 || srm_check == 1 || lp_feature != null || (mobile_feature != null && customer_chk == 0)) {
                                                  console.log("if condition")
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
                                                  customer_token = crypto.createHash('md5').update(phonenumber).digest("hex");
                                                  dashboard = checkPermissionByFeatureCode('FFD001', user_chkdet.user_id);//feild force DashBoard
                                                  new_dashboard = checkPermissionByFeatureCode('MFD001', user_chkdet.user_id);//	Here it displays the beats assigned to ff on that day and the outlets assigned to that beats.	

                                                  if (dashboard == 1 || new_dashboard == 1) {
                                                       is_dashboard = 1;
                                                       getTeamByUser(user_chkdet.user_id).then(team => {
                                                            let key = user_chkdet.user_id.hasOwnProperty(team)
                                                            if (key != false) {
                                                                 delete team.key
                                                                 if (team.length >= 1) {
                                                                      has_child = 1;
                                                                 } else {
                                                                      has_child = 0;
                                                                 }
                                                            }
                                                       }).catch(err => {
                                                            console.log(err);
                                                            reject({ status: "failed", message: "Something went wrong" })
                                                       })
                                                  } else {
                                                       is_dashboard = 0;
                                                       has_child = 0;
                                                  }

                                                  if (module_id == 1) {
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
                                                       //if (DataFilter.length > 0) {
                                                       decode_data = DataFilter;
                                                       sbu_lits = typeof decode_data.sbu != 'undefined' ? decode_data.sbu : [];
                                                       decode_sbulist = sbu_lits;
                                                       hub = (typeof decode_sbulist[118002] != 'undefined' && decode_data[118002]) ? decode_data[118002] : '';
                                                       le_wh_id = (typeof decode_sbulist[118001] != 'undefined' && decode_data[118001]) ? decode_sbulist[118001] : '';
                                                       //  }
                                                       //response body 

                                                       let ff_ecash_details;
                                                       getEcashInfo(user_chkdet.user_id).then(result => {
                                                            ff_ecash_details = result;

                                                            let promo = [];
                                                            let format_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate()
                                                            let query = "select COUNT(*) as count FROM promotion_cashback_details pc  JOIN legalentity_warehouses lw  ON lw.le_wh_id =   pc.wh_id AND lw.legal_entity_id = " + user_chkdet.legal_entity_id + " WHERE cbk_status = 1 AND is_self in (0, 2) AND '" + format_date + "' BETWEEN start_date AND end_date"; /* */
                                                            db.query(query, {}, function (err, res) {
                                                                 if (err) {
                                                                      reject(err)
                                                                 } else if (Object.keys(res).length > 0) {
                                                                      promo.push(res);
                                                                 }
                                                                 mobile_feature_array
                                                                 if (mobile_feature.length > 0) {
                                                                      mobile_feature_array = JSON.parse(JSON.stringify(mobile_feature[[0]]))
                                                                 }
                                                                 let promo_array = JSON.parse(JSON.stringify(promo[[0]]))

                                                                 response_data = {
                                                                      customer_group_id: '', customer_token: customer_token, customer_id: user_chkdet.user_id, legal_entity_id: user_chkdet.legal_entity_id, parent_le_id: user_chkdet.parent_le_id,
                                                                      firstname: user_chkdet.firstname, lastname: user_chkdet.lastname, image: profile_picture, segment_id: '', pincode: '', le_wh_id: le_wh_id, hub: hub,
                                                                      is_active: user_chkdet.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: is_dashboard, has_child: has_child, lp_feature: lp_feature, mobile_feature: mobile_feature_array,
                                                                      beat_id: '', latitude: '', longitude: '', ff_ecash_details: ff_ecash_details, mfc: mfc, ff_full_name: user_chkdet.firstname + '_' + user_chkdet.lastname,
                                                                      ff_profile_pic: user_chkdet.profile_picture, credit_limit: creditlimit, aadhar_id: user_chkdet.aadhar_id, promotion_count: promo_array[0].count
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
                                                  console.log("else condition")
                                                  let business_type_id;
                                                  let pincode;
                                                  let legal_entity_type_id;
                                                  // let le_wh_id;
                                                  let parent_le_id;
                                                  is_ff = 0;
                                                  is_srm = 0;
                                                  let data_7 = "select us.* , le.is_approved  from users as us  LEFT JOIN  legal_entities as le ON le.legal_entity_id =  us.legal_entity_id where us.mobile_no =" + phonenumber + " && le.legal_entity_type_id IN (select value from master_lookup as ml where ml.mas_cat_id =" + desc + " && ml.is_active = 1 )";
                                                  db.query(data_7, {}, function (err, user_leg) {
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
                                                                 let customer_token = crypto.createHash('md5').update(phonenumber).digest("hex");
                                                                 let que = "update users as us set password_token ='" + customer_token + "' , updated_at ='" + formatted_date + "' where us.user_id =" + customer_id;
                                                                 db.query(que, {}, (err, updated) => {
                                                                      if (err) {
                                                                           console.log(err)
                                                                           reject(err)
                                                                      } else {
                                                                           console.log("updated Successfully")
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

                                                                 getWarehouseIdByMobileNo(phonenumber).then((le_wh_id_1) => {
                                                                      if (le_wh_id != null) {
                                                                           le_wh_id = '';
                                                                      } else {
                                                                           le_wh_id = le_wh_id_1;
                                                                      }

                                                                 }).catch(err => {
                                                                      console.log(err);
                                                                 })
                                                            }

                                                            let queu = "select beat_id from customers where le_id =" + legal_entity_id;
                                                            db.query(queu, {}, function (err, beat) {
                                                                 if (err) {
                                                                      reject(err)
                                                                 } else if (Object.keys(beat).length > 0) {
                                                                      beat_id = beat
                                                                      let hub = '';
                                                                      let ecash_details;
                                                                      if (beat_id != null && beat_id.length > 0) {
                                                                           getHub(beat_id[0].beat_id).then((hub_value) => {
                                                                                if (hub_value != null) {
                                                                                     hub = hub_value
                                                                                } else {
                                                                                     hub = '';
                                                                                }
                                                                                //used to getEcashinfo
                                                                                getEcashInfo(user_det.user_id).then(result => {
                                                                                     ecash_details = result
                                                                                     let promo;
                                                                                     let format_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
                                                                                     if (le_wh_id != '') {
                                                                                          let query_3 = "select  count(*) as count from promotion_cashback_details where wh_id =" + le_wh_id + "&& cbk_status=1 &&  is_self in (1,2) && '" + format_date + "'between start_date and end_date && customer_type like '%" + legal_entity_type_id + "%'";//le_wh_id
                                                                                          db.query(query_3, {}, function (err, promotion_data) {
                                                                                               console.log(query_3)
                                                                                               if (err) {
                                                                                                    reject(err)
                                                                                               } else if (Object.keys(promotion_data).length > 0) {
                                                                                                    promo = promotion_data;
                                                                                                    let mobile_feature_array_1 = JSON.parse(JSON.stringify(mobile_feature[[0]]))
                                                                                                    function_response = { customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_det.user_id, legal_entity_id: user_det.legal_entity_id, parent_le_id: parent_le_id, firstname: user_det.firstname, lastname: user_det.lastname, image: profile_picture, segment_id: business_type_id, pincode: pincode, le_wh_id: le_wh_id, hub: hub, is_active: user_det.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: 0, lp_feature: [], mobile_feature: mobile_feature_array_1, beat_id: beat_id[0].beat_id, latitude: segment_det[0].latitude, longitude: segment_det[0].longitude, ecash_details: ecash_details, ff_full_name: user_det.firstname + ' ' + user_det.lastname, ff_profile_pic: user_det.profile_picture, mfc: mfc, credit_limit: creditlimit, aadhar_id: user_det.aadhar_id, promotion_count: promo[0].count }
                                                                                                    let res1 = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 1, data: function_response };
                                                                                                    resolve(res1);
                                                                                               }
                                                                                          })
                                                                                     } else {
                                                                                          let mobile_feature_array_1 = JSON.parse(JSON.stringify(mobile_feature[[0]]));
                                                                                          function_response = {
                                                                                               customer_group_id: legal_entity_type_id, customer_token: customer_token, customer_id: user_det.user_id, legal_entity_id: user_det.legal_entity_id,
                                                                                               parent_le_id: parent_le_id, firstname: user_det.firstname, lastname: user_det.lastname, image: profile_picture, segment_id: business_type_id, pincode: pincode,
                                                                                               le_wh_id: le_wh_id, hub: hub, is_active: user_det.is_active, is_ff: is_ff, is_srm: is_srm, is_dashboard: 0, lp_feature: [], mobile_feature: mobile_feature_array_1,
                                                                                               beat_id: beat_id[0].beat_id, latitude: segment_det[0].latitude, longitude: segment_det[0].longitude, ecash_details: ecash_details, ff_full_name: user_det.firstname + ' ' + user_det.lastname,
                                                                                               ff_profile_pic: user_det.profile_picture, mfc: mfc, credit_limit: creditlimit, aadhar_id: user_det.aadhar_id, promotion_count: 0
                                                                                          }
                                                                                          let res1 = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 1, data: function_response };
                                                                                          resolve(res1);
                                                                                     }

                                                                                }).catch(err => {
                                                                                     console.log(err)
                                                                                })

                                                                           }).catch(err => {
                                                                                console.log(err)
                                                                           })
                                                                      }
                                                                 }
                                                            })
                                                       }
                                                  })
                                             }
                                        }
                                   }
                              })
                         }
                    })
               } else {
                    console.log("New User generated otp")
                    let user_temp_det = [];
                    let user_temp_query;
                    let query_4 = "select * from user_temp  as ustemp where ustemp.mobile_no =" + phonenumber + "&& ustemp.otp =" + otp
                    db.query(query_4, {}, function (err, use_temp) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(use_temp).length > 0) {
                              user_temp_det.push(use_temp);
                         }
                         if (use_temp != null) {
                              user_temp_det.push(use_temp[0]);
                              user_temp_query = use_temp.length;
                              console.log("user_temp_length ", user_temp_query);
                              // ------Before completing Registartion-------
                              if (user_temp_query == 1) {
                                   console.log("user_temp_length ", user_temp_query);
                                   let data_4 = "update user_temp as ustmp set status = 1  , updated_at = '" + formatted_date + "' where ustmp.mobile_no ='" + phonenumber + "'";
                                   db.query(data_4, {}, function (err, update_status) {
                                        if (err) {
                                             reject(err)
                                        } else if (update_status) {
                                             let res = { message: "Thank you for confirming your Mobile Number", status: 1, approved: 0 };
                                             resolve(res);
                                        }
                                   })
                              }
                         } else {
                              resolve({ 'status': 'failed', 'message': "Please Send Valid OTP" })
                         }

                    })
               }
          })
     } catch (err) {
          console.log(err)
          let error = { status: 'failed', message: "Unable to process Your request .Please try later" };
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
                         checkOtpUser(otp, telephone).then((user) => {
                              if (user) {
                                   console.log("user already exist")
                                   users.push(user[0]);
                                   if (users.length == 1 && users != '' || userTemp.length == 1 && userTemp != '') {
                                        getStatus(telephone).then((appstatus) => {
                                             if (typeof appstatus != 'undefined' && appstatus != null) {
                                                  appstatus_legal = appstatus[0].legal_entity_id;
                                             } else {
                                                  appstatus_legal = '';
                                             }
                                             //used to confirm otp
                                             otpConfirm(telephone, otp, appstatus_legal, device_id, ip_address, reg_id, platform_id, module_id).then((result) => {
                                                  let request = { parameter: result, apiUrl: 'Login' };
                                                  logApiRequests1(request);
                                                  if (result.status == 1) {
                                                       if (typeof result.data.customer_id != 'undefined') {
                                                            salesTargetFeature = checkPermissionByFeatureCode('SALESTARGET001', result.data.customer_id)
                                                            if (salesTargetFeature == 1) {
                                                                 result.data.sales_target = 1;
                                                            } else {
                                                                 result.data.sales_target = 0;
                                                            }

                                                            saleTargetFeature = checkPermissionByFeatureCode('MBMSU001', result.data.customer_id)
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
                                                  }
                                                  else {
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
                                        resolve({ status: "failed", message: "Please enter valid OTP " })
                                   }
                              } else {
                                   console.log("New User")
                                   checkOtpUsertemp(otp, telephone).then((usertemp) => {
                                        console.log("usertemp", usertemp)
                                        userTemp.push(usertemp);
                                        if (users.length == 1 && users != '' || userTemp.length == 1 && userTemp != '') {
                                             //fetching users legalEntityId  based on entered mobile number
                                             getStatus(telephone).then((appstatus) => {
                                                  if (typeof appstatus != 'undefined' && appstatus != '') {
                                                       appstatus_legal = appstatus[0].legal_entity_id;
                                                  } else {
                                                       appstatus_legal = '';
                                                  }
                                                  console.log("2416")
                                                  otpConfirm(telephone, otp, appstatus_legal, device_id, ip_address, reg_id, platform_id, module_id).then((result) => {

                                                       let request = { parameter: result, apiUrl: 'Login' };
                                                       logApiRequests1(request);
                                                       if (result.status == 1) {
                                                            //newly added
                                                            let sample = { data: { sales_target: 0, must_sku_list: 0 } };
                                                            let FinalResposnse = Object.assign(result, sample)
                                                            let check = { status: 'success', message: 'confirm', data: FinalResposnse }
                                                            resolve(check)
                                                       } else {
                                                            resolve({ 'status': 'failed', 'message': result.message })
                                                       }
                                                  }).catch((err) => {
                                                       console.log(err)
                                                  })
                                             }).catch((err) => {
                                                  console.log(err)
                                             })

                                        } else {
                                             resolve({ 'status': 'failed', 'message': "Please Send Valid OTP" })
                                        }
                                   }).catch((err) => {
                                        console.log(err.message)
                                   })
                              }
                         }).catch((err) => {
                              console.log(err)
                              resolve({ status: "failed", message: "Something went wrong" })
                         })
                    } else {
                         let res_1 = { status: "failed", message: "Please enter Otp " };
                         resolve(res_1)
                    }
               }
               else {
                    let res_2 = { status: "failed", message: "Please enter mobile number" };
                    resolve(res_2)
               }
          })
     } catch (err) {
          console.log(err)
          let data = { status: "failed", message: "Unable to Process your request-2198" }
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
          var Curl = require('node-libcurl').Curl;
          return new Promise((resolve, reject) => {
               var curl = new Curl();
               let random_number = Math.floor(100000 + Math.random() * 999999);
               let string = JSON.stringify(customer_token);
               let mobile_number = telephone;
               let app_unique_key = "qoVggl61OKE";
               // let app_unique_key = env("CP_APP_UNIQUE_KEY");
               let message = "<#> Your OTP for Ebutor is  " + random_number + " \n - " + app_unique_key;
               if (mobile_number.length >= 10 && message != "") {
                    let user = 'vinil@esealinc.com:eseal@123';
                    let receipientno = mobile_number;
                    let senderID = 'EBUTOR';
                    curl.setOpt(Curl.option.URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
                    curl.setOpt('FOLLOWLOCATION', true);
                    curl.setOpt(Curl.option.POST, 1);
                    curl.setOpt(Curl.option.POSTFIELDS, "user=" + user + "&senderID=" + senderID + "&receipientno=" + receipientno + "&msgtxt=" + message);
                    curl.on('end', function (statusCode, body, headers) {
                         console.log(headers, body)
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
                         reject({ status: 0, message: "Not Valid" })
                    } else {
                         let res = {};
                         let buyer_type_id = 3001; // for kirana,s
                         let current_datetime = new Date();
                         let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                         if (otpflag == 0) {
                              let query = "insert into  user_temp(mobile_no , otp , legal_entity_type_id , created_at) values (" + "'" + mobile_number + "'" + ',' + "'" + random_number + "'" + ',' + buyer_type_id + ',' + "'" + formatted_date + "')";
                              db.query(query, {}, function (err, rows) {
                                   if (err) {
                                        console.log(err)
                                   } else {
                                        res = { message: "Please Confirm  OTP", status: 1 }
                                        resolve(res);
                                   }
                              })
                         } else if (otpflag == 1) {
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

               } else {
                    res = { message: "Invalid Information", status: 0 }
                    resolve(res);
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
                                   resolve(count);
                              } else {
                                   resolve(count);
                              }
                         })
                    }

               })

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

     // return new Promise((resolve, reject) => {
     //      let response = [];
     //      let string = JSON.stringify(customer_token)
     //      let data = "select count(user_id) as counts FROM users WHERE password_token =" + string;
     //      db.query(data, {}, function (err, rows) {
     //           if (err) {
     //                reject(err);
     //           }
     //           if (Object.keys(rows).length > 0) {
     //                response.push(rows[0]);
     //                resolve(response[0].counts);
     //           }
     //           else {
     //                reject("No mapping found..")
     //           }
     //           // db.release()
     //      });


     // });
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
*sample input
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
                    if (err) { console.log(err) }
                    let data = "select le_code from retailer_flat where legal_entity_id =" + string
                    con.query(data, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              con.rollback(function () {
                                   console.log(err);
                              });
                         } else {
                              let legalEntityDetails = result[0];
                              let Query = "select aadhar_id , mobile_no from users where legal_entity_id=" + string;
                              con.query(Query, {}, function (err, aadhar) {
                                   if (err) {
                                        console.log(err);
                                        con.rollback(function () {
                                             console.log(err);
                                        });
                                   } else {
                                        aadhar_id = aadhar[0];
                                        let Query1 = 'Call getLegalEntitiesDataById(0,0,' + legalEntityId + ')';
                                        con.query(Query1, {}, function (err, row) {
                                             if (err) {
                                                  console.log(err);
                                                  con.rollback(function () {
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
                                                                           if (beatId > 0) {
                                                                                let data_1 = "select pjp_pincode_area.spoke_id,pjp_pincode_area.le_wh_id from pjp_pincode_area LEFT JOIN spokes ON spokes.spoke_id = pjp_pincode_area.spoke_id where pjp_pincode_area_id =" + beatid;
                                                                                con.query(data_1, {}, function (err, pincode) {
                                                                                     if (err) {
                                                                                          con.rollback(function () {
                                                                                               console.log(err);
                                                                                          });
                                                                                     } else if (Object.keys(pincode).length > 0) {
                                                                                          let hubDetail = JSON.parse(JSON.stringify(pincode[0]));
                                                                                          hubId = hubDetail.hasOwnProperty('le_wh_id') ? hubDetails[0].le_wh_id : 0;
                                                                                          spokeId = hubDetail.hasOwnProperty('spoke_id') ? hubDetails[0].spoke_id : 0;
                                                                                          if (hubId > 0 && spokeId > 0) {
                                                                                               // string[0].hub_id = hubId;
                                                                                               // string[0].spoke_id = spokeId;
                                                                                               let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legalEntityId;
                                                                                               con.query(customer, {}, function (err, customers) {
                                                                                                    if (err) {
                                                                                                         con.rollback(function () {
                                                                                                              console.log(err);
                                                                                                         });
                                                                                                    } else if (Object.keys(customers).length > 0) {

                                                                                                    }
                                                                                               })
                                                                                          }

                                                                                     }
                                                                                })

                                                                                //updating retailer flat
                                                                                delete string[0].sms_notification;
                                                                                let current_datetime = string[0].last_order_date_old;
                                                                                let curretDate = string[0].created_at;
                                                                                let currDate = string[0].updated_at;
                                                                                formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
                                                                                formatted = curretDate.getFullYear() + "-" + (curretDate.getMonth() + 1) + "-" + curretDate.getDate() + " " + curretDate.getHours() + ":" + curretDate.getMinutes() + ":" + curretDate.getSeconds();
                                                                                formattedDate = currDate.getFullYear() + "-" + (currDate.getMonth() + 1) + "-" + currDate.getDate() + " " + currDate.getHours() + ":" + currDate.getMinutes() + ":" + currDate.getSeconds();
                                                                                let retailer = " UPDATE retailer_flat SET parent_le_id ='" + parent_le + "' , business_legal_name = '" + string[0].business_legal_name + "',legal_entity_type_id='" + string[0].legal_entity_type_id + "',business_type_id ='" + string[0].business_type_id + "' ,name ='" + string[0].name + "', mobile_no = '" + string[0].mobile_no + " ', volume_class_id = '" + string[0].volume_class_id + "', volume_class = '" + string[0].volume_class + "', No_of_shutters = '" + string[0].No_of_shutters + "', suppliers = '" + string[0].suppliers + "', business_start_time = '" + string[0].business_start_time + "', business_end_time = '" + string[0].business_end_time + "', address = '" + string[0].address + "', address1 = '" + string[0].address1 + "', address2 = '" + string[0].address2 + "', area_id = '" + string[0].area_id + "',AREA = '" + string[0].area + "',hub_id = '" + string[0].hub_id + "', beat_id = '" + string[0].beat_id + "', spoke_id = '" + string[0].spoke_id + "', beat = '" + string[0].beat + "', city = '" + string[0].city + "', state_id = '" + string[0].state_id + "', state = '" + string[0].state + "', country = '" + string[0].country + "', locality = '" + string[0].locality + "', landmark = '" + string[0].lankmark + "', pincode = '" + string[0].pincode + "' , smartphone = '" + string[0].smartphone + "', network = '" + string[0].network + "', master_manf = '" + string[0].master_manf + "', orders_old = '" + string[0].orders_old + "', last_order_date_old = '" + formatted_date + "', beat_rm_name = '" + string[0].beat_rm_name + "',created_by = '" + string[0].created_by + "', created_at = '" + formatted + "', created_time = '" + string[0].created_time + "', updated_by = '" + string[0].updated_by + "', updated_at = '" + formattedDate + "', updated_time = '" + string[0].updated_time + "', latitude = '" + string[0].latitude + "', longitude = '" + string[0].longitude + "',is_icecream = ' " + string[0].is_icecream + "', is_milk = '" + string[0].is_milk + "', is_deepfreezer = '" + string[0].is_deepfreezer + "', is_fridge = '" + string[0].is_fridge + "', is_vegetables = '" + string[0].is_vegetables + "', is_visicooler = '" + string[0].is_visicooler + "',dist_not_serv = '" + string[0].dist_not_serv + "', facilities = " + string[0].facilities + " WHERE legal_entity_id =" + legalEntityId;
                                                                                con.query(retailer, {}, function (err, retailers) {
                                                                                     if (err) {
                                                                                          con.rollback(function () {
                                                                                               console.log(err);
                                                                                          });
                                                                                     } else if (Object.keys(retailers).length > 0) {
                                                                                          console.log("retailer flat updated1")
                                                                                     }
                                                                                })

                                                                           }
                                                                      }

                                                                 } else {
                                                                      if (typeof string[0].hub_id != 'undefined') {
                                                                           beatId = typeof string[0].beat_id != 'undefined' ? string[0].beat_id : 0;
                                                                           //  if (beatId > 0) {
                                                                           let Querys = "select pjp_pincode_area.spoke_id ,pjp_pincode_area.le_wh_id from pjp_pincode_area  LEFT JOIN  spokes ON spokes.spoke_id = pjp_pincode_area.spoke_id where pjp_pincode_area_id=" + beatId;
                                                                           con.query(Querys, {}, function (err, hubDetails) {
                                                                                if (err) {
                                                                                     con.rollback(function () {
                                                                                          console.log(err);
                                                                                     });
                                                                                } else if (Object.keys(hubDetails[0]).length > 0) {
                                                                                     let hubDetail = JSON.parse(JSON.stringify(hubDetails[0]));
                                                                                     hubId = hubDetail.hasOwnProperty('le_wh_id') ? hubDetails[0].le_wh_id : 0;
                                                                                     spokeId = hubDetail.hasOwnProperty('spoke_id') ? hubDetails[0].spoke_id : 0;
                                                                                     if (hubId > 0 && spokeId > 0) {
                                                                                          string[0].hub_id = hubId;
                                                                                          string[0].spoke_id = spokeId;
                                                                                          let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legalEntityId;
                                                                                          con.query(customer, {}, function (err, customers) {
                                                                                               if (err) {
                                                                                                    con.rollback(function () {
                                                                                                         console.log(err);
                                                                                                    });
                                                                                               } else if (Object.keys(customers).length > 0) {
                                                                                                    console.log("customer updated successfully")
                                                                                               }
                                                                                          })
                                                                                     }
                                                                                }
                                                                                delete string[0].sms_notification;
                                                                                let current_datetime = new Date(string[0].last_order_date_old);
                                                                                let curretDate = new Date(string[0].created_at);
                                                                                let currDate = new Date(string[0].updated_at);
                                                                                formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
                                                                                formatted = curretDate.getFullYear() + "-" + (curretDate.getMonth() + 1) + "-" + curretDate.getDate() + " " + curretDate.getHours() + ":" + curretDate.getMinutes() + ":" + curretDate.getSeconds();
                                                                                formattedDate = currDate.getFullYear() + "-" + (currDate.getMonth() + 1) + "-" + currDate.getDate() + " " + currDate.getHours() + ":" + currDate.getMinutes() + ":" + currDate.getSeconds();
                                                                                let retailer = "insert into retailer_flat (parent_le_id ,legal_entity_id , le_code , business_legal_name  , legal_entity_type_id , business_type_id ,name , mobile_no , volume_class_id , volume_class , No_of_shutters , suppliers, business_start_time , business_end_time , address , address1 , address2 , area_id , AREA  , hub_id , beat_id , spoke_id , beat , city , state_id , state , country , locality , landmark , pincode , smartphone , network , master_manf , orders_old , last_order_date_old , beat_rm_name , created_by , created_at , created_time , updated_by , updated_At , updated_time , latitude , longitude , is_icecream , is_milk , is_deepfreezer , is_fridge , is_vegetables , is_visicooler , dist_not_serv , facilities  , legal_entity_type , business_type , fssai , is_swipe) values (" + parent_le + "," + string[0].legal_entity_id + ",'" +
                                                                                     string[0].le_code + "','" + string[0].business_legal_name + "'," + string[0].legal_entity_type_id + "," + string[0].business_type_id + ",'" + string[0].name + "'," + string[0].mobile_no + "," + string[0].volume_class_id + ", '" + string[0].volume_class + "'," + string[0].No_of_shutters + ", '" + string[0].suppliers + "','" + string[0].business_start_time + "','" + string[0].business_end_time + "','" + string[0].address + "','" + string[0].address1 + "','" + string[0].address2 + "'," + string[0].area_id + ",'" + string[0].area + "'," + string[0].hub_id + "," + string[0].beat_id + "," + string[0].spoke_id + ",'" + string[0].beat + "','" + string[0].city + "'," + string[0].state_id + ",'" + string[0].state + "','" + string[0].country + "','" + string[0].locality + "','" + string[0].landmark + "'," + string[0].pincode + ",'" + string[0].smartphone + "','" + string[0].network + "','" + string[0].master_manf + "'," + string[0].orders_old + ",'" + formatted_date + "','" + string[0].beat_rm_name + "','" + string[0].created_by + "','" + formatted + "','" + string[0].created_time + "','" + string[0].updated_by + "','" + formattedDate + "','" + string[0].updated_time + "','" + string[0].latitude + "','" + string[0].longitude + "'," + string[0].is_icecream + "," + string[0].is_milk + "," + string[0].is_deepfreezer + "," + string[0].is_fridge + "," + string[0].is_vegetables + "," + string[0].is_visicooler + ",'" + string[0].dist_not_serv + "'," + string[0].facilities + ",'" + string[0].legal_entity_type + "','" + string[0].business_type + "','" + string[0].fssai + "'," + string[0].is_swipe + ")";
                                                                                con.query(retailer, {}, function (err, retailers) {
                                                                                     if (err) {
                                                                                          con.rollback(function () {
                                                                                               console.log(err);
                                                                                          });
                                                                                     } else if (Object.keys(retailers).length > 0) {
                                                                                          console.log("updated retailer flat")
                                                                                     }
                                                                                })
                                                                                // }
                                                                                //  }
                                                                           })

                                                                           // }
                                                                      }
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                            })
                                                       }
                                                  }
                                                  con.commit(function (err) {
                                                       if (err) {
                                                            con.rollback(function () {
                                                                 console.log(err);
                                                            });
                                                       } else {
                                                            console.log("Transaction close")
                                                       }

                                                  });

                                             }

                                        })
                                   }
                              })

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
                         console.log("====>2866", result[0])
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
//used to get warehouse based on beat
function getWhFromBeat(beat) {
     return new Promise((resolve, reject) => {
          try {
               let query_1 = "select d.dc_id FROM pjp_pincode_area p INNER JOIN dc_hub_mapping d ON p.`le_wh_id` = d.hub_id WHERE p.pjp_pincode_area_id=" + beat;
               db.query(query_1, {}, function (err, result) {
                    if (err) {
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         resolve(result[0].dc_id)
                    } else {
                         resolve(0)
                    }
               })

          } catch (err) {
               console.log(err)
          }
     })

}

//used to get warehouse based on beat
function getWhFromBeat_New(beat_id) {
     // return new Promise((resolve, reject) => {
     //      try {
     //           let query_1 = "SELECT le_wh_id FROM legalentity_warehouses AS lew  JOIN legal_entities AS le ON lew.legal_entity_id = le.parent_le_id AND lew.dc_type = 118001 AND le.legal_entity_id =" + legal_Entity_id;
     //           db.query(query_1, {}, function (err, result) {
     //                if (err) {
     //                     reject(err)
     //                } else if (Object.keys(result).length > 0) {
     //                     resolve(result[0].le_wh_id)
     //                } else {
     //                     resolve(0)
     //                }
     //           })

     //      } catch (err) {
     //           console.log(err)
     //      }
     // })

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


function rollBackLegalentityInsertedRecord(last_insert_legal_id) {
     try {
          let query = "delete from legal_entities where legal_entity_id =" + last_insert_legal_id;
          db.query(query, {}, function (err, response) {
               if (err) {
                    console.log(err);
               } else {
                    console.log('Legal_entity record rolled back successfully');
               }
          })
     } catch (err) {
          console.log(err);
     }

}




function rollBackUserInsertedRecord(last_insert_user_id) {
     try {
          let query = "delete from users where user_id =" + last_insert_user_id;
          db.query(query, {}, function (err, response) {
               if (err) {
                    console.log(err);
               } else {
                    console.log('User record rolled back successfully');
               }
          })
     } catch (err) {
          console.log(err);
     }

}

function deleteFromMongoUserTemp(telephone) {
     try {
          userTemp.deleteOne({ mobile: telephone }, function (err, deleted) {
               if (err) {
                    console.log(err);
               }
               console.log("deleted successfully");
          })
     } catch (err) {
          console.log(err);
     }
}

function insertInMongoUserTable(mobile_no, user_id, password_token, otp, is_active, is_disabled, legal_entity_id) {
     console.log("3474")
     try {
          let body = {
               mobile: mobile_no,
               user_id: user_id,
               password_token: password_token,
               lp_token: '',
               otp: otp,
               lp_otp: '',
               is_active: is_active,
               is_disabled: is_disabled,
               legal_entity_id: legal_entity_id,
               //  createdOn: query_response[0].created_at,
          }
          user.create(body).then(inserted => {
               console.log("inserted successfully");
          })
     } catch (err) {
          console.log(err);
     }
}


function getStateId(stateName) {
     return new Promise((resolve, reject) => {
          try {
               let query = "SELECT zone_id FROM zone WHERE NAME LIKE '%" + stateName + "%'";
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err);
                    } else if (Object.keys(response).length > 0) {
                         resolve(response[0].zone_id);
                    } else {
                         resolve('');
                    }
               })
          } catch (err) {
               console.log(err);
          }
     })
}
/*
Purpose : Add address function is used to add the address to regirstration process third step. fillable variable is difined to store the data fields into the database
author : Deepak Tiwari
Request : Requirebusiness_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email, mobile_no, filepath1, filepath2, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1, bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1,contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat, customer_type,.
Resposne : Update Address.
*/
module.exports.address = function (business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email, mobile_no, filepath1, filepath2, latitude = 0, longitude = 0, download_token, ip_address, device_id, pref_value, pref_value1, bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat, customer_type, gstin, arn_number, is_icecream, sms_notification, is_milk = 0, is_fridge = 0, is_vegetables = 0, is_visicooler = 0, dist_not_serv = '', facilities = 0, is_deepfreezer = 0, is_swipe, fssai, gst_doc, fssai_doc, stateName = '', aadhar_id = '', credit_limit = 0, mfc = '', pan_number = '', website_url = '', logo = '') {
     try {
          console.log("inside address function")
          return new Promise((resolve, reject) => {
               let otp;
               let desc;
               let ff_uid = 0;
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
               let last_insert_userpref_id;
               let last_insert_role_id;
               let users = [];
               let legal = [];
               let role_id;
               let mobile_feature;
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
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         //used fetch otp from user temp
                         otp = (typeof rows[0].otp != 'undefined' && rows[0].otp != null) ? rows[0].otp : 0;
                         if ((sales_token != '' && customer_type != 3028)) {
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
                                                                 reject(err);
                                                            } else if (Object.keys(legal_entitycompany_type).length > 0) {
                                                                 getRefCode('CU', state_id).then(result => {//fetching reference code for entered state
                                                                      if (result.length > 0) {
                                                                           le_code = result;
                                                                           if (ff_uid != '') {
                                                                                getParentLeIdFromFFId(ff_uid).then(parent_id => {//fetching ff legal_entity_id based on  ff_id 
                                                                                     if (parent_id) {
                                                                                          parent_le_id = parent_id;
                                                                                     } else {
                                                                                          parent_le_id = 0;
                                                                                     }
                                                                                     //inserting user details into legal_entities table
                                                                                     let businessName = JSON.stringify(business_legal_name);
                                                                                     let Address_1 = JSON.stringify(address1);
                                                                                     let Address_2 = JSON.stringify(address2);
                                                                                     let data_5 = "insert into  legal_entities ( business_legal_name ,address1 , legal_entity_type_id , address2 , locality , landmark ,tin_number ,country , state_id , is_approved, city, pincode, business_type_id, latitude, longitude, le_code, parent_id, created_by, gstin, arn_number, created_at, parent_le_id ,pan_number , website_url  , logo , fssai) VALUE (" + businessName + "," + Address_1 + ",'" + customer_type + "'," + Address_2 + ",'" + locality + "','" + landmark + "','" + tin_number + "'," + 99 + ",'" + state_id + "','" + is_approved + "','" + city + "','" + pincode + "','" + segment_id + "','" + latitude + "','" + longitude + "','" + le_code + "','" + legal_entitycompany_type[0].description + "'," + ff_uid + ",'" + gstin + "','" + arn_number + "','" + formatted_date + "','" + parent_le_id + "','" + pan_number + "','" + website_url + "' ,'" + logo + "','" + fssai + "')";
                                                                                     db.query(data_5, {}, function (err, inserted) {
                                                                                          if (err) {
                                                                                               console.log(err.message)
                                                                                               reject(err);
                                                                                          } else if (typeof inserted != 'undefined') {
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
                                                                                                         console.log("error", err.message);
                                                                                                         rollBackLegalentityInsertedRecord(last_insert_legal_id);
                                                                                                         console.log("last_insert_id", last_insert_legal_id)
                                                                                                         resolve({ 'status': "failed", 'message': "Entered email already exist" });
                                                                                                    } else if (insert) {
                                                                                                         last_insert_user_id = insert.insertId;
                                                                                                         insertInMongoUserTable(mobile_no, last_insert_user_id, customer_token, otp, 1, 0, last_insert_legal_id);
                                                                                                         if (ff_uid == null) {
                                                                                                              ff_uid = last_insert_user_id;
                                                                                                         }
                                                                                                         //we have to add
                                                                                                         let query_7 = "select cp.city_id from cities_pincodes as cp  where cp.pincode ='" + pincode + "' && cp.officename  ='" + area + "' && cp.city ='" + city + "'";
                                                                                                         db.query(query_7, {}, function (err, res) {
                                                                                                              if (err) {
                                                                                                                   console.log(err)
                                                                                                                   reject(err);
                                                                                                              } else if (res) {
                                                                                                                   area_chk.push(res);
                                                                                                                   ///used to get warehouse
                                                                                                                   if (ff_uid && ff_uid != '') {//querying in legalentity_warehouse table 
                                                                                                                        // getWarehouseFromLeId(parent_le_id).then(le_wh => {
                                                                                                                        //      if (le_wh == null) {
                                                                                                                        //           le_wh_id = '';
                                                                                                                        //      } else {
                                                                                                                        //           le_wh_id = le_wh;
                                                                                                                        //      }
                                                                                                                        // }).catch(err => {
                                                                                                                        //      console.log(err);
                                                                                                                        //      reject(err);
                                                                                                                        // })
                                                                                                                        getWhFromBeat_New(beat).then(le_wh => {// checking dc_hub_mapping then returning dc_id
                                                                                                                             if (le_wh == null) {
                                                                                                                                  le_wh_id = '';
                                                                                                                             } else {
                                                                                                                                  le_wh_id = le_wh;
                                                                                                                             }
                                                                                                                        }).catch(err => {
                                                                                                                             console.log(err);
                                                                                                                             reject(err);
                                                                                                                        })
                                                                                                                   } else if (beat && beat != '') {
                                                                                                                        getWhFromBeat_New(beat).then(le_wh => {// checking dc_hub_mapping then returning dc_id
                                                                                                                             if (le_wh == null) {
                                                                                                                                  le_wh_id = '';
                                                                                                                             } else {
                                                                                                                                  le_wh_id = le_wh;
                                                                                                                             }
                                                                                                                        }).catch(err => {
                                                                                                                             console.log(err);
                                                                                                                             reject(err);
                                                                                                                        })
                                                                                                                   } else {
                                                                                                                        le_wh_id = '';
                                                                                                                   }


                                                                                                                   //used to get hub
                                                                                                                   getHub_New(beat).then(beat_id => {
                                                                                                                        if (beat_id == null) {
                                                                                                                             hub = '';
                                                                                                                        } else {
                                                                                                                             hub = beat_id;
                                                                                                                        }

                                                                                                                   }).catch((err) => {
                                                                                                                        console.log(err);
                                                                                                                        reject(err);
                                                                                                                   })

                                                                                                                   console.log("is_swipe", is_Swipe)
                                                                                                                   if (area_chk[0].length > 0) {
                                                                                                                        let area1 = JSON.parse(JSON.stringify(area_chk[[0]]));
                                                                                                                        //Insert data into customers
                                                                                                                        let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer,is_swipe ) values (" + last_insert_legal_id + ',' +
                                                                                                                             volume_class + ',' + noof_shutters + ',' + area1[0].city_id + ",'" + master_manf + "'," + smartphone + ",'" + network + "','" + ff_uid + "','" + beat + "','" + formatted_date + "'," + is_icecream + ',' +
                                                                                                                             is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";
                                                                                                                        db.query(insert_user, {}, function (err, area_id) {
                                                                                                                             if (err) {
                                                                                                                                  console.log("3623", err.message);
                                                                                                                                  console.log("last_insert_user_id ", last_insert_user_id);
                                                                                                                                  console.log("last_insert_legal_entity_id ", last_insert_legal_id);
                                                                                                                                  rollBackUserInsertedRecord(last_insert_user_id);
                                                                                                                                  rollBackLegalentityInsertedRecord(last_insert_legal_id);
                                                                                                                                  reject(err.message);
                                                                                                                             } else if (typeof area_id != 'undefined') {
                                                                                                                                  area_chk_id = area_id.insertId;
                                                                                                                             }
                                                                                                                        })
                                                                                                                   } else {
                                                                                                                        let state_query = "select name from zone where zone_id = " + state_id;
                                                                                                                        db.query(state_query, {}, function (err, res) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err)
                                                                                                                                  reject(err);
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
                                                                                                                                            reject(err);
                                                                                                                                       } else {
                                                                                                                                            last_insert_city_id = inserted.insertId
                                                                                                                                       }
                                                                                                                                       //Insert data into customers
                                                                                                                                       let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer, is_swipe) values (" + last_insert_legal_id + ',' +
                                                                                                                                            volume_class + ',' + noof_shutters + ',' + last_insert_city_id + ",'" + master_manf + "'," + smartphone + ',' + network + ',' + ff_uid + ',' + beat + ",'" + formatted_date + "'," + is_icecream + ',' +
                                                                                                                                            is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";
                                                                                                                                       db.query(insert_user, {}, function (err, area_id) {
                                                                                                                                            if (err) {
                                                                                                                                                 console.log("3623", err.message);
                                                                                                                                                 console.log("last_insert_user_id ", last_insert_user_id);
                                                                                                                                                 console.log("last_insert_legal_entity_id ", last_insert_legal_id);
                                                                                                                                                 rollBackUserInsertedRecord(last_insert_user_id);
                                                                                                                                                 rollBackLegalentityInsertedRecord(last_insert_legal_id);
                                                                                                                                                 reject(err.message);
                                                                                                                                            } else if (typeof area_id != 'undefined') {
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
                                                                                                                                       reject(err);
                                                                                                                                  } else if (Object.keys(user).length > 0) {
                                                                                                                                       users.push(JSON.parse(JSON.stringify(user[[0]])))
                                                                                                                                       let entity_query = "select legal_entity_id,legal_entity_type_id from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                                                       db.query(entity_query, {}, function (err, respon) {
                                                                                                                                            if (err) {
                                                                                                                                                 console.log(err)
                                                                                                                                                 reject(err);
                                                                                                                                            } else if (Object.keys(respon).length > 0) {
                                                                                                                                                 legal.push(JSON.parse(JSON.stringify(respon[[0]])));
                                                                                                                                                 let query_10 = "select master_lookup_name from master_lookup where value = '" + license_type + "'";
                                                                                                                                                 db.query(query_10, {}, function (err, license) {
                                                                                                                                                      if (err) {
                                                                                                                                                           console.log(err)
                                                                                                                                                           reject(err);
                                                                                                                                                      } else if (Object.keys(license).length > 0) {
                                                                                                                                                           if (filepath1.length > 0 && typeof filepath1[0] != 'undefined') {
                                                                                                                                                                //Insert data into lelgal entity doc table
                                                                                                                                                                let insert_1 = "insert into legal_entity_docs (legal_entity_id , doc_url , doc_type,created_at) values (" + last_insert_legal_id + ",'" + filepath1 + "','" + license[0].master_lookup_name + "','" + formatted_date + "')";
                                                                                                                                                                db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                                     if (err) {
                                                                                                                                                                          console.log(err)
                                                                                                                                                                          reject(err);
                                                                                                                                                                     } else {
                                                                                                                                                                          last_insert_doc_id = insert.insertId;
                                                                                                                                                                     }
                                                                                                                                                                })
                                                                                                                                                           }
                                                                                                                                                      }

                                                                                                                                                 })

                                                                                                                                                 //setting user preferences
                                                                                                                                                 if (users.length != 0) {
                                                                                                                                                      if (pref_value != null || bstart_time != null || bend_time != null) {
                                                                                                                                                           //Insert data into user_prefences 
                                                                                                                                                           let insert_2 = "insert into user_preferences (user_id ,preference_name ,preference_value , preference_value1 ,business_start_time , business_end_time , sms_subscription  ,create_at) values (" + users[0].user_id + ',' + "'expected delivery' ,'" + pref_value + "','" + pref_value1 + "','" + bstart_time + "','" + bend_time + "'," + sms_notification + ",'" + formatted_date + "')";
                                                                                                                                                           db.query(insert_2, {}, function (err, inserted) {
                                                                                                                                                                if (err) {
                                                                                                                                                                     console.log(err);
                                                                                                                                                                     reject(err);
                                                                                                                                                                } else if (inserted) {
                                                                                                                                                                     last_insert_userpref_id = inserted.insertId;

                                                                                                                                                                }
                                                                                                                                                           })
                                                                                                                                                      }

                                                                                                                                                 }

                                                                                                                                                 if (sales_token != null && users.length != 0) {
                                                                                                                                                      let insert_call_logs = "insert into ff_call_logs (ff_id , user_id , legal_entity_id , activity , check_in , check_in_lat , check_in_long ,created_at) values (" + ff_uid + ',' + users[0].user_id + ',' + legal[0].legal_entity_id + ',' + 107000 + ",'" + formatted_date + "'," + latitude + ',' + longitude + ",'" + formatted_date + "')";
                                                                                                                                                      db.query(insert_call_logs, {}, function (err, inserted) {
                                                                                                                                                           if (err) {
                                                                                                                                                                console.log(err);
                                                                                                                                                                reject(err);
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
                                                                                                                                                                          console.log(err);
                                                                                                                                                                          reject(err);
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
                                                                                                                                                                                                   console.log(err);
                                                                                                                                                                                                   reject(err);
                                                                                                                                                                                              } else {
                                                                                                                                                                                                   console.log("usercredit limit inserted successfully")
                                                                                                                                                                                              }
                                                                                                                                                                                         })
                                                                                                                                                                                    }


                                                                                                                                                                                    if (mfc != '') {
                                                                                                                                                                                         let insert1 = "insert into mfc_customer_mapping (mfc_id  , cust_le_id , credit_limit , is_active) value (" + mfc + ',' + legal[0].legal_entity_id + ',' + credit_limit + "," + 1 + ")";
                                                                                                                                                                                         db.query(insert1, {}, function (err, inserted) {
                                                                                                                                                                                              if (err) {
                                                                                                                                                                                                   console.log(err);
                                                                                                                                                                                                   reject(err);
                                                                                                                                                                                              } else {
                                                                                                                                                                                                   console.log("mfc inserted")
                                                                                                                                                                                              }
                                                                                                                                                                                         })
                                                                                                                                                                                    }


                                                                                                                                                                                    if (last_insert_legal_id != '' && last_insert_user_id != '') {
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
                                                                                                                                                                                         console.log("gst_doc ,fssai_doc", gst_doc, fssai_doc);
                                                                                                                                                                                         if (fssai_doc.length > 0 && typeof fssai_doc[0] != 'undefined') {
                                                                                                                                                                                              //Insert data into lelgal entity doc table
                                                                                                                                                                                              let doc_name = "Food License Document";
                                                                                                                                                                                              let doc_type = "FSSAI";
                                                                                                                                                                                              let insert_1 = "insert into legal_entity_docs (doc_url , doc_type , doc_name , legal_entity_id  , created_at , created_by) values ('" + fssai_doc + "','" + doc_type + "','" + doc_name + "','" + last_insert_legal_id + "','" + formatted_date + "','" + ff_uid + "')";
                                                                                                                                                                                              db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                                                                   if (err) {
                                                                                                                                                                                                        console.log(err)
                                                                                                                                                                                                        reject(err);
                                                                                                                                                                                                   } else {
                                                                                                                                                                                                        console.log(" fssai _doc path inserted successfully")
                                                                                                                                                                                                   }
                                                                                                                                                                                              })
                                                                                                                                                                                         }
                                                                                                                                                                                         //gst document upload
                                                                                                                                                                                         if (gst_doc.length > 0 && typeof gst_doc[0] != 'undefined') {
                                                                                                                                                                                              //Insert data into lelgal entity doc table
                                                                                                                                                                                              let doc_name = "GST License Document";
                                                                                                                                                                                              let doc_type = "GSTIN";
                                                                                                                                                                                              let insert_1 = "insert into legal_entity_docs (doc_url , doc_type , doc_name , legal_entity_id  , created_at , created_by) values ('" + gst_doc + "','" + doc_type + "','" + doc_name + "','" + last_insert_legal_id + "','" + formatted_date + "','" + ff_uid + "')";
                                                                                                                                                                                              db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                                                                   if (err) {
                                                                                                                                                                                                        console.log(err)
                                                                                                                                                                                                        reject(err);
                                                                                                                                                                                                   } else {
                                                                                                                                                                                                        console.log("gst_doc path inserted successfully")
                                                                                                                                                                                                   }
                                                                                                                                                                                              })
                                                                                                                                                                                         }

                                                                                                                                                                                         let delete_user = "delete from user_temp where mobile_no ='" + mobile_no + "'";
                                                                                                                                                                                         db.query(delete_user, {}, function (err, deleted) {
                                                                                                                                                                                              if (err) {
                                                                                                                                                                                                   console.log(err)
                                                                                                                                                                                                   reject(err);
                                                                                                                                                                                              } else {
                                                                                                                                                                                                   console.log("User_temop delete ")
                                                                                                                                                                                              }
                                                                                                                                                                                         })
                                                                                                                                                                                         deleteFromMongoUserTemp(mobile_no);
                                                                                                                                                                                         let Querys = "SELECT le_wh_id FROM legalentity_warehouses AS lew  JOIN legal_entities AS le ON lew.legal_entity_id = le.parent_le_id AND lew.dc_type = 118002 AND le.legal_entity_id =" + legal[0].legal_entity_id;
                                                                                                                                                                                         db.query(Querys, {}, function (err, hubDetails) {
                                                                                                                                                                                              if (err) {
                                                                                                                                                                                                   console.log(err);
                                                                                                                                                                                                   reject(err);
                                                                                                                                                                                              } else if (Object.keys(hubDetails).length > 0) {
                                                                                                                                                                                                   let hubId = typeof hubDetails[0].le_wh_id != "undefined" ? hubDetails[0].le_wh_id : 0;
                                                                                                                                                                                                   // let GetSpokesQuery = "SELECT spoke_id FROM beat_master WHERE hub_id =" + hubId;
                                                                                                                                                                                                   let GetSpokesQuery = "SELECT spoke_id FROM beat_master WHERE beat_id =" + beat;
                                                                                                                                                                                                   console.log("----3960---");
                                                                                                                                                                                                   db.query(GetSpokesQuery, {}, function (err, SpokeDetails) {
                                                                                                                                                                                                        if (err) {
                                                                                                                                                                                                             console.log(err);
                                                                                                                                                                                                             reject(err);
                                                                                                                                                                                                        } else if (Object.keys(SpokeDetails).length > 0) {
                                                                                                                                                                                                             let spokeId = typeof SpokeDetails[0].spoke_id != "undefined" ? SpokeDetails[0].spoke_id : 0;
                                                                                                                                                                                                             if (hubId > 0 && spokeId > 0) {
                                                                                                                                                                                                                  let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legal[0].legal_entity_id;
                                                                                                                                                                                                                  db.query(customer, {}, function (err, customers) {
                                                                                                                                                                                                                       if (err) {
                                                                                                                                                                                                                            console.log(err);
                                                                                                                                                                                                                            reject(err);
                                                                                                                                                                                                                       } else if (Object.keys(customers).length > 0) {
                                                                                                                                                                                                                            console.log("customer updated successfully");
                                                                                                                                                                                                                            let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                                                                            db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                                                                                 if (err) {
                                                                                                                                                                                                                                      console.log(err);
                                                                                                                                                                                                                                      reject(err);
                                                                                                                                                                                                                                 } else if (retailer_insert) {
                                                                                                                                                                                                                                      console.log("retailer flat inserted")
                                                                                                                                                                                                                                      resolve(res);
                                                                                                                                                                                                                                 } else {
                                                                                                                                                                                                                                      resolve('');
                                                                                                                                                                                                                                 }
                                                                                                                                                                                                                            })
                                                                                                                                                                                                                       }
                                                                                                                                                                                                                  })
                                                                                                                                                                                                             }
                                                                                                                                                                                                        }

                                                                                                                                                                                                   })
                                                                                                                                                                                              }

                                                                                                                                                                                         })


                                                                                                                                                                                         //  if (beatId > 0) {
                                                                                                                                                                                         // let Querys = "select pjp_pincode_area.spoke_id ,pjp_pincode_area.le_wh_id from pjp_pincode_area  LEFT JOIN  spokes ON spokes.spoke_id = pjp_pincode_area.spoke_id where pjp_pincode_area_id=" + beat;
                                                                                                                                                                                         // db.query(Querys, {}, function (err, hubDetails) {
                                                                                                                                                                                         //      if (err) {
                                                                                                                                                                                         //           console.log(err);
                                                                                                                                                                                         //           reject(err);
                                                                                                                                                                                         //      } else if (Object.keys(hubDetails[0]).length > 0) {
                                                                                                                                                                                         //           let hubDetail = JSON.parse(JSON.stringify(hubDetails[0]));
                                                                                                                                                                                         //           hubId = hubDetail.hasOwnProperty('le_wh_id') ? hubDetails[0].le_wh_id : 0;
                                                                                                                                                                                         //           spokeId = hubDetail.hasOwnProperty('spoke_id') ? hubDetails[0].spoke_id : 0;
                                                                                                                                                                                         //           if (hubId > 0 && spokeId > 0) {
                                                                                                                                                                                         //                // string[0].hub_id = hubId;
                                                                                                                                                                                         //                // string[0].spoke_id = spokeId;
                                                                                                                                                                                         //                let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legal[0].legal_entity_id;
                                                                                                                                                                                         //                db.query(customer, {}, function (err, customers) {
                                                                                                                                                                                         //                     if (err) {
                                                                                                                                                                                         //                          console.log(err);
                                                                                                                                                                                         //                          reject(err);
                                                                                                                                                                                         //                     } else if (Object.keys(customers).length > 0) {
                                                                                                                                                                                         //                          console.log("customer updated successfully")
                                                                                                                                                                                         //                     }
                                                                                                                                                                                         //                })
                                                                                                                                                                                         //           }
                                                                                                                                                                                         //      }

                                                                                                                                                                                         // })


                                                                                                                                                                                         // let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                                         // db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                                         //      if (err) {
                                                                                                                                                                                         //           console.log(err);
                                                                                                                                                                                         //           reject(err);
                                                                                                                                                                                         //      } else if (retailer_insert) {
                                                                                                                                                                                         //           console.log("retailer flat inserted")
                                                                                                                                                                                         //           resolve(res);
                                                                                                                                                                                         //      } else {
                                                                                                                                                                                         //           resolve('');
                                                                                                                                                                                         //      }
                                                                                                                                                                                         // })

                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("-----3915------------------")
                                                                                                                                                                                         let failed_status = { status: "failed", message: "Unable to process your request.Plz contact support on - 04066006442." }
                                                                                                                                                                                         resolve(failed_status);
                                                                                                                                                                                    }

                                                                                                                                                                               }
                                                                                                                                                                          }).catch(err => {
                                                                                                                                                                               console.log(err);
                                                                                                                                                                               reject(err);
                                                                                                                                                                          })
                                                                                                                                                                     }
                                                                                                                                                                })
                                                                                                                                                           }
                                                                                                                                                      }
                                                                                                                                                 }).catch(err => {
                                                                                                                                                      console.log(err);
                                                                                                                                                      reject(err);
                                                                                                                                                 })
                                                                                                                                            } else {
                                                                                                                                                 console.log("hello====>3480")
                                                                                                                                                 resolve({ 'status': "failed", 'message': "Unable to process your request.Plz contact support on - 04066006442." });
                                                                                                                                            }
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       resolve({ 'status': "failed", 'message': "User not yet resgistered.Plz contact support on - 04066006442." });
                                                                                                                                  }
                                                                                                                             })
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
                                                                                     reject(err);
                                                                                })
                                                                           } else {
                                                                                console.log("feild force details are empty");
                                                                                resolve({ 'status': "failed", 'message': "Unable to process your request.Plz contact support on - 04066006442." });
                                                                           }
                                                                      } else {
                                                                           resolve({ 'status': "failed", 'message': "Please select correct state" });
                                                                      }
                                                                 }).catch(err => {
                                                                      console.log(err);
                                                                      reject(err);
                                                                 })
                                                            } else {
                                                                 resolve({ 'status': "failed" });
                                                            }
                                                       })
                                                  }
                                             })
                                        } else {
                                             resolve({ 'status': "failed", 'message': "Please select correct state", "message": "Unable to process your request.Plz contact support on - 04066006442." });
                                        }
                                   })
                              }).catch((err) => {
                                   console.log(err);
                                   reject(err);
                              })
                         } else if ((sales_token == '' && customer_type != 3028)) {
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
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                   } else if (Object.keys(stateName).length > 0) {
                                        let pincode_query = "select count(*)  as count from cities_pincodes where pincode=" + pincode + " && state = '" + stateName[0].name + "'";
                                        db.query(pincode_query, {}, function (err, pincodeStateChk) {
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
                                                            reject(err);
                                                       } else if (Object.keys(legal_entitycompany_type).length > 0) {
                                                            getRefCode('CU', state_id).then(result => {
                                                                 if (result.length > 0) {
                                                                      le_code = result;
                                                                      // if (beat != '') {
                                                                      getLeFromBeat(beat, latitude, longitude).then(parent_id => {//fetching legal entity id fron pjp_pincode_area based on beat_id
                                                                           if (parent_id) {
                                                                                parent_le_id = parent_id;
                                                                           } else {
                                                                                parent_le_id = 0;
                                                                           }
                                                                           let businessNameSelf = JSON.stringify(business_legal_name);
                                                                           let Address_1_Self = JSON.stringify(address1);
                                                                           let Address_2_Self = JSON.stringify(address2)
                                                                           //inserting user details into legal_entities table
                                                                           let data_5 = "insert into  legal_entities ( business_legal_name ,address1 , legal_entity_type_id , address2 , locality , landmark ,tin_number ,country , state_id , is_approved, city, pincode, business_type_id, latitude, longitude, le_code, parent_id, created_by, gstin, arn_number, created_at, parent_le_id ,pan_number , website_url  , logo,fssai) VALUE (" + businessNameSelf + "," + Address_1_Self + ",'" + customer_type + "'," + Address_2_Self + ",'" + locality + "','" + landmark + "','" + tin_number + "'," + 99 + ",'" + state_id + "','" + is_approved + "','" + city + "','" + pincode + "','" + segment_id + "','" + latitude + "','" + longitude + "','" + le_code + "','" + legal_entitycompany_type[0].description + "'," + ff_uid + ",'" + gstin + "','" + arn_number + "','" + formatted_date + "','" + parent_le_id + "','" + pan_number + "','" + website_url + "' ,'" + logo + "','" + fssai + "')";
                                                                           db.query(data_5, {}, function (err, inserted) {
                                                                                if (err) {
                                                                                     console.log(err.message)
                                                                                     reject(err);
                                                                                } else if (typeof inserted != 'undefined') {
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
                                                                                               console.log("error", err.message);
                                                                                               console.log("last_insert_id", last_insert_legal_id);
                                                                                               rollBackLegalentityInsertedRecord(last_insert_legal_id);

                                                                                               resolve({ 'status': "failed", 'message': "Entered email already exist" });
                                                                                          } else if (insert) {
                                                                                               last_insert_user_id = insert.insertId;
                                                                                               insertInMongoUserTable(mobile_no, last_insert_user_id, customer_token, otp, 1, 0, last_insert_legal_id);
                                                                                               if (ff_uid == null) {
                                                                                                    ff_uid = last_insert_user_id;
                                                                                               }

                                                                                               if (sales_token == null) {
                                                                                                    //self registration
                                                                                                    let data1 = "update users as us set created_by ='" + ff_uid + "', updated_at ='" + formatted_date + "', us.user_id =" + last_insert_user_id + "  where us.user_id =" + last_insert_user_id;
                                                                                                    db.query(data1, {}, function (err, update) {
                                                                                                         if (err) {
                                                                                                              console.log(err)
                                                                                                              reject(err);
                                                                                                         } else {
                                                                                                              console.log("1.updated")
                                                                                                         }
                                                                                                    })
                                                                                                    let data_6 = "update legal_entities as le  set created_by = '" + ff_uid + "',updated_at = '" + formatted_date + "' where le.legal_entity_id = " + last_insert_legal_id;
                                                                                                    db.query(data_6, {}, function (err, update) {
                                                                                                         if (err) {
                                                                                                              console.log(err)
                                                                                                              reject(err);
                                                                                                         } else {
                                                                                                              console.log("2.updated")
                                                                                                         }
                                                                                                    })
                                                                                               }


                                                                                               //we have to add
                                                                                               let query_7 = "select cp.city_id from cities_pincodes as cp  where cp.pincode ='" + pincode + "' && cp.officename  ='" + area + "' && cp.city ='" + city + "'";
                                                                                               db.query(query_7, {}, function (err, res) {
                                                                                                    if (err) {
                                                                                                         console.log(err)
                                                                                                         reject(err);
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
                                                                                                                   reject(err);
                                                                                                              })
                                                                                                         } else if (beat && beat != '') {
                                                                                                              getWhFromBeat_New(beat).then(le_wh => {
                                                                                                                   if (le_wh == null) {
                                                                                                                        le_wh_id = '';
                                                                                                                   } else {
                                                                                                                        le_wh_id = le_wh;
                                                                                                                   }
                                                                                                              }).catch(err => {
                                                                                                                   console.log(err);
                                                                                                                   reject(err);
                                                                                                              })
                                                                                                         } else {
                                                                                                              le_wh_id = '';
                                                                                                         }


                                                                                                         //used to get hub
                                                                                                         getHub_New(beat).then(beat_id => {
                                                                                                              if (beat_id == null) {
                                                                                                                   hub = '';
                                                                                                              } else {
                                                                                                                   hub = beat_id;
                                                                                                              }

                                                                                                         }).catch((err) => {
                                                                                                              console.log(err);
                                                                                                              reject(err);
                                                                                                         })


                                                                                                         if (area_chk[0].length > 0) {
                                                                                                              let area1 = JSON.parse(JSON.stringify(area_chk[[0]]))
                                                                                                              //Insert data into customers
                                                                                                              let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer,is_swipe ) values (" + last_insert_legal_id + ',' +
                                                                                                                   volume_class + ',' + noof_shutters + ',' + area1[0].city_id + ",'" + master_manf + "'," + smartphone + ",'" + network + "','" + ff_uid + "','" + beat + "','" + formatted_date + "'," + is_icecream + ',' +
                                                                                                                   is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";

                                                                                                              db.query(insert_user, {}, function (err, area_id) {
                                                                                                                   if (err) {
                                                                                                                        console.log("3623", err.message);
                                                                                                                        console.log("last_insert_user_id ", last_insert_user_id);
                                                                                                                        console.log("last_insert_legal_entity_id ", last_insert_legal_id);
                                                                                                                        rollBackUserInsertedRecord(last_insert_user_id);
                                                                                                                        rollBackLegalentityInsertedRecord(last_insert_legal_id);
                                                                                                                        reject(err.message);
                                                                                                                   } else if (typeof area_id != 'undefined') {
                                                                                                                        area_chk_id = area_id.insertId;
                                                                                                                   }
                                                                                                              })
                                                                                                         } else {
                                                                                                              let state_query = "select name from zone where zone_id = " + state_id;
                                                                                                              db.query(state_query, {}, function (err, res) {
                                                                                                                   if (err) {
                                                                                                                        console.log(err);
                                                                                                                        reject(err);
                                                                                                                   } else if (Object.keys(res).length > 0) {
                                                                                                                        state_name = res;
                                                                                                                   } else {
                                                                                                                        let ros = { 'status': 'failed', 'message': 'Unable to process your request->3837' }
                                                                                                                   }

                                                                                                                   if (state_name.length != 0) {
                                                                                                                        let insert_city = "insert into cities_pincodes (country_id ,pincode , city , state , officename) values(" + 99 + ',' + pincode + ",'" + city + "','" + state_name[0].name + "','" + area + "')";
                                                                                                                        db.query(insert_city, {}, function (err, inserted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err);
                                                                                                                                  reject(err);
                                                                                                                             } else if (inserted) {
                                                                                                                                  last_insert_city_id = inserted.insertId
                                                                                                                             }
                                                                                                                             //Insert data into customers
                                                                                                                             let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer, is_swipe) values (" + last_insert_legal_id + ',' +
                                                                                                                                  volume_class + ',' + noof_shutters + ',' + last_insert_city_id + ",'" + master_manf + "'," + smartphone + ',' + network + ',' + ff_uid + ',' + beat + ",'" + formatted_date + "'," + is_icecream + ',' +
                                                                                                                                  is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";
                                                                                                                             db.query(insert_user, {}, function (err, area_id) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log("3623", err.message);
                                                                                                                                       console.log("last_insert_user_id ", last_insert_user_id);
                                                                                                                                       console.log("last_insert_legal_entity_id ", last_insert_legal_id);
                                                                                                                                       rollBackUserInsertedRecord(last_insert_user_id);
                                                                                                                                       rollBackLegalentityInsertedRecord(last_insert_legal_id);
                                                                                                                                       reject(err.message);
                                                                                                                                  } else if (typeof area_id != 'undefined') {
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
                                                                                                                   reject(err);
                                                                                                              } else if (Object.keys(master).length > 0) {
                                                                                                                   desc = master[0].description
                                                                                                                   let query_9 = "select u.user_id,u.mobile_no,u.is_active,u.otp,le.legal_entity_id,u.profile_picture from users as u left join legal_entities as le ON le.legal_entity_id = u.legal_entity_id where u.mobile_no =" + mobile_no + "&&  u.is_active = 1 && le.legal_entity_type_id IN( select value from master_lookup as ml where ml.mas_cat_id =" + desc + "&&  ml.is_active =1) ";
                                                                                                                   db.query(query_9, {}, function (err, user) {
                                                                                                                        if (err) {
                                                                                                                             console.log(err);
                                                                                                                             reject(err);
                                                                                                                        } else if (Object.keys(user).length > 0) {
                                                                                                                             users.push(JSON.parse(JSON.stringify(user[[0]])))
                                                                                                                             let entity_query = "select legal_entity_id,legal_entity_type_id from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                                             db.query(entity_query, {}, function (err, respon) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err);
                                                                                                                                       reject(err);
                                                                                                                                  } else if (Object.keys(respon).length > 0) {
                                                                                                                                       legal.push(JSON.parse(JSON.stringify(respon[[0]])));
                                                                                                                                       let query_10 = "select master_lookup_name from master_lookup where value = '" + license_type + "'";
                                                                                                                                       db.query(query_10, {}, function (err, license) {
                                                                                                                                            if (err) {
                                                                                                                                                 console.log(err);
                                                                                                                                                 reject(err);
                                                                                                                                            } else if (Object.keys(license).length > 0) {
                                                                                                                                                 if (filepath1.length > 0 && typeof filepath1[0] != 'undefined') {
                                                                                                                                                      //Insert data into lelgal entity doc table
                                                                                                                                                      let insert_1 = "insert into legal_entity_docs (legal_entity_id , doc_url , doc_type,created_at) values (" + last_insert_legal_id + ",'" + filepath1 + "','" + license[0].master_lookup_name + "','" + formatted_date + "')";
                                                                                                                                                      db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                           if (err) {
                                                                                                                                                                console.log(err);
                                                                                                                                                                reject(err);
                                                                                                                                                           } else {
                                                                                                                                                                last_insert_doc_id = insert.insertId;
                                                                                                                                                           }
                                                                                                                                                      })
                                                                                                                                                 }
                                                                                                                                            }
                                                                                                                                       })

                                                                                                                                       //setting user preferences
                                                                                                                                       if (users.length != 0) {
                                                                                                                                            if (pref_value != null || bstart_time != null || bend_time != null) {
                                                                                                                                                 //Insert data into user_prefences 
                                                                                                                                                 let insert_2 = "insert into user_preferences (user_id ,preference_name ,preference_value , preference_value1 ,business_start_time , business_end_time , sms_subscription  ,create_at) values (" + users[0].user_id + ',' + "'expected delivery' ,'" + pref_value + "','" + pref_value1 + "','" + bstart_time + "','" + bend_time + "'," + sms_notification + ",'" + formatted_date + "')";
                                                                                                                                                 db.query(insert_2, {}, function (err, inserted) {
                                                                                                                                                      if (err) {
                                                                                                                                                           console.log(err);
                                                                                                                                                           reject(err);
                                                                                                                                                      } else if (inserted) {
                                                                                                                                                           last_insert_userpref_id = inserted.insertId;

                                                                                                                                                      }
                                                                                                                                                 })
                                                                                                                                            }

                                                                                                                                       }

                                                                                                                                       if (sales_token != null && users.length != 0) {
                                                                                                                                            let insert_call_logs = "insert into ff_call_logs (ff_id , user_id , legal_entity_id , activity , check_in , check_in_lat , check_in_long ,created_at) values (" + ff_uid + ',' + users[0].user_id + ',' + legal[0].legal_entity_id + ',' + 107000 + ",'" + formatted_date + "'," + latitude + ',' + longitude + ",'" + formatted_date + "')";
                                                                                                                                            db.query(insert_call_logs, {}, function (err, inserted) {
                                                                                                                                                 if (err) {
                                                                                                                                                      console.log(err);
                                                                                                                                                      reject(err);
                                                                                                                                                 } else if (inserted) {
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
                                                                                                                                                                console.log(err);
                                                                                                                                                                reject(err);
                                                                                                                                                           } else if (last_insert) {
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
                                                                                                                                                                                         console.log(err);
                                                                                                                                                                                         reject(err);
                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("usercredit limit inserted successfully")
                                                                                                                                                                                    }
                                                                                                                                                                               })
                                                                                                                                                                          }


                                                                                                                                                                          if (mfc != '') {
                                                                                                                                                                               let insert1 = "insert into mfc_customer_mapping (mfc_id  , cust_le_id , credit_limit , is_active) value (" + mfc + ',' + legal[0].legal_entity_id + ',' + credit_limit + "," + 1 + ")";
                                                                                                                                                                               db.query(insert1, {}, function (err, inserted) {
                                                                                                                                                                                    if (err) {
                                                                                                                                                                                         console.log(err);
                                                                                                                                                                                         reject(err);
                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("mfc inserted")
                                                                                                                                                                                    }
                                                                                                                                                                               })
                                                                                                                                                                          }


                                                                                                                                                                          if (last_insert_legal_id != '' && last_insert_user_id != '') {
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

                                                                                                                                                                               console.log("gst_doc ,fssai_doc", gst_doc, fssai_doc);
                                                                                                                                                                               if (fssai_doc.length > 0 && typeof fssai_doc[0] != 'undefined') {
                                                                                                                                                                                    //Insert data into lelgal entity doc table
                                                                                                                                                                                    let doc_name = "Food License Document";
                                                                                                                                                                                    let doc_type = "FSSAI";
                                                                                                                                                                                    let insert_1 = "insert into legal_entity_docs (doc_url , doc_type , doc_name , legal_entity_id  , created_at , created_by) values ('" + fssai_doc + "','" + doc_type + "','" + doc_name + "','" + last_insert_legal_id + "','" + formatted_date + "','" + ff_uid + "')";
                                                                                                                                                                                    db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                                                         if (err) {
                                                                                                                                                                                              console.log(err);
                                                                                                                                                                                              reject(err);
                                                                                                                                                                                         } else {
                                                                                                                                                                                              console.log(" fssai _doc path inserted successfully")
                                                                                                                                                                                         }
                                                                                                                                                                                    })
                                                                                                                                                                               }
                                                                                                                                                                               //gst document upload
                                                                                                                                                                               if (gst_doc.length > 0 && typeof gst_doc[0] != 'undefined') {
                                                                                                                                                                                    //Insert data into lelgal entity doc table
                                                                                                                                                                                    let doc_name = "GST License Document";
                                                                                                                                                                                    let doc_type = "GSTIN";
                                                                                                                                                                                    let insert_1 = "insert into legal_entity_docs (doc_url , doc_type , doc_name , legal_entity_id  , created_at , created_by) values ('" + gst_doc + "','" + doc_type + "','" + doc_name + "','" + last_insert_legal_id + "','" + formatted_date + "','" + ff_uid + "')";
                                                                                                                                                                                    db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                                                         if (err) {
                                                                                                                                                                                              console.log(err);
                                                                                                                                                                                              reject(err);
                                                                                                                                                                                         } else {
                                                                                                                                                                                              console.log("gst_doc path inserted successfully")
                                                                                                                                                                                         }
                                                                                                                                                                                    })
                                                                                                                                                                               }

                                                                                                                                                                               let delete_user = "delete from user_temp where mobile_no ='" + mobile_no + "'";
                                                                                                                                                                               db.query(delete_user, {}, function (err, deleted) {
                                                                                                                                                                                    if (err) {
                                                                                                                                                                                         console.log(err);
                                                                                                                                                                                         reject(err);
                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("User_temop delete ")
                                                                                                                                                                                    }
                                                                                                                                                                               })
                                                                                                                                                                               deleteFromMongoUserTemp(mobile_no);
                                                                                                                                                                               let Querys = "SELECT le_wh_id FROM legalentity_warehouses AS lew  JOIN legal_entities AS le ON lew.legal_entity_id = le.parent_le_id AND lew.dc_type = 118002 AND le.legal_entity_id =" + legal[0].legal_entity_id;
                                                                                                                                                                               db.query(Querys, {}, function (err, hubDetails) {
                                                                                                                                                                                    if (err) {
                                                                                                                                                                                         console.log(err);
                                                                                                                                                                                         reject(err);
                                                                                                                                                                                    } else if (Object.keys(hubDetails).length > 0) {
                                                                                                                                                                                         let hubId = typeof hubDetails[0].le_wh_id != "undefined" ? hubDetails[0].le_wh_id : 0;
                                                                                                                                                                                         console.log("----4499");
                                                                                                                                                                                         let GetSpokesQuery = "SELECT spoke_id FROM beat_master WHERE beat_id =" + beat;
                                                                                                                                                                                         db.query(GetSpokesQuery, {}, function (err, SpokeDetails) {
                                                                                                                                                                                              if (err) {
                                                                                                                                                                                                   console.log(err);
                                                                                                                                                                                                   reject(err);
                                                                                                                                                                                              } else if (Object.keys(SpokeDetails).length > 0) {
                                                                                                                                                                                                   let spokeId = typeof SpokeDetails[0].spoke_id != "undefined" ? SpokeDetails[0].spoke_id : 0;
                                                                                                                                                                                                   if (hubId > 0 && spokeId > 0) {
                                                                                                                                                                                                        let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legal[0].legal_entity_id;
                                                                                                                                                                                                        db.query(customer, {}, function (err, customers) {
                                                                                                                                                                                                             if (err) {
                                                                                                                                                                                                                  console.log(err);
                                                                                                                                                                                                                  reject(err);
                                                                                                                                                                                                             } else if (Object.keys(customers).length > 0) {
                                                                                                                                                                                                                  console.log("customer updated successfully");
                                                                                                                                                                                                                  let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                                                                  db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                                                                       if (err) {
                                                                                                                                                                                                                            console.log(err);
                                                                                                                                                                                                                            reject(err);
                                                                                                                                                                                                                       } else if (retailer_insert) {
                                                                                                                                                                                                                            console.log("retailer_flat inserted")
                                                                                                                                                                                                                            resolve(res);
                                                                                                                                                                                                                       }
                                                                                                                                                                                                                  })
                                                                                                                                                                                                             }else {
                                                                                                                                                                                                                  let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                                                                  db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                                                                       if (err) {
                                                                                                                                                                                                                            console.log(err);
                                                                                                                                                                                                                            reject(err);
                                                                                                                                                                                                                       } else if (retailer_insert) {
                                                                                                                                                                                                                            console.log("retailer_flat inserted")
                                                                                                                                                                                                                            resolve(res);
                                                                                                                                                                                                                       }
                                                                                                                                                                                                                  })
                                                                                                                                                                                                             }
                                                                                                                                                                                                        })
                                                                                                                                                                                                   } else {
                                                                                                                                                                                                        let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                                                        db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                                                             if (err) {
                                                                                                                                                                                                                  console.log(err);
                                                                                                                                                                                                                  reject(err);
                                                                                                                                                                                                             } else if (retailer_insert) {
                                                                                                                                                                                                                  console.log("retailer_flat inserted")
                                                                                                                                                                                                                  resolve(res);
                                                                                                                                                                                                             }
                                                                                                                                                                                                        })  
                                                                                                                                                                                                   }
                                                                                                                                                                                              }else {
                                                                                                                                                                                                   let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                                                   db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                                                        if (err) {
                                                                                                                                                                                                             console.log(err);
                                                                                                                                                                                                             reject(err);
                                                                                                                                                                                                        } else if (retailer_insert) {
                                                                                                                                                                                                             console.log("retailer_flat inserted")
                                                                                                                                                                                                             resolve(res);
                                                                                                                                                                                                        }
                                                                                                                                                                                                   }) 
                                                                                                                                                                                              }

                                                                                                                                                                                         })
                                                                                                                                                                                    } else {
                                                                                                                                                                                         console.log("hello");
                                                                                                                                                                                         let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                                         db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                                              if (err) {
                                                                                                                                                                                                   console.log(err);
                                                                                                                                                                                                   reject(err);
                                                                                                                                                                                              } else if (retailer_insert) {
                                                                                                                                                                                                   console.log("retailer_flat inserted")
                                                                                                                                                                                                   resolve(res);
                                                                                                                                                                                              }
                                                                                                                                                                                         })
                                                                                                                                                                                    }

                                                                                                                                                                               })
                                                                                                                                                                               //  if (beatId > 0) {
                                                                                                                                                                               // let Querys = "select pjp_pincode_area.spoke_id ,pjp_pincode_area.le_wh_id from pjp_pincode_area  LEFT JOIN  spokes ON spokes.spoke_id = pjp_pincode_area.spoke_id where pjp_pincode_area_id=" + beat;
                                                                                                                                                                               // db.query(Querys, {}, function (err, hubDetails) {
                                                                                                                                                                               //      if (err) {
                                                                                                                                                                               //           console.log(err);
                                                                                                                                                                               //           reject(err);
                                                                                                                                                                               //      } else if (Object.keys(hubDetails[0]).length > 0) {
                                                                                                                                                                               //           let hubDetail = JSON.parse(JSON.stringify(hubDetails[0]));
                                                                                                                                                                               //           hubId = hubDetail.hasOwnProperty('le_wh_id') ? hubDetails[0].le_wh_id : 0;
                                                                                                                                                                               //           spokeId = hubDetail.hasOwnProperty('spoke_id') ? hubDetails[0].spoke_id : 0;
                                                                                                                                                                               //           if (hubId > 0 && spokeId > 0) {
                                                                                                                                                                               //                // string[0].hub_id = hubId;
                                                                                                                                                                               //                // string[0].spoke_id = spokeId;
                                                                                                                                                                               //                let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legal[0].legal_entity_id;
                                                                                                                                                                               //                db.query(customer, {}, function (err, customers) {
                                                                                                                                                                               //                     if (err) {
                                                                                                                                                                               //                          console.log(err);
                                                                                                                                                                               //                          reject(err);
                                                                                                                                                                               //                     } else if (Object.keys(customers).length > 0) {
                                                                                                                                                                               //                          console.log("customer updated successfully")
                                                                                                                                                                               //                     }
                                                                                                                                                                               //                })
                                                                                                                                                                               //           }
                                                                                                                                                                               //      }

                                                                                                                                                                               // })


                                                                                                                                                                               // let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                               // db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                               //      if (err) {
                                                                                                                                                                               //           console.log(err);
                                                                                                                                                                               //           reject(err);
                                                                                                                                                                               //      } else if (retailer_insert) {
                                                                                                                                                                               //           console.log("retailer_flat inserted")
                                                                                                                                                                               //           resolve(res);
                                                                                                                                                                               //      }
                                                                                                                                                                               // })

                                                                                                                                                                          } else {

                                                                                                                                                                               let failed_status = { status: "failed", message: "Unable to process your request.Plz contact support on - 04066006442." }
                                                                                                                                                                               resolve(failed_status);
                                                                                                                                                                          }

                                                                                                                                                                     }
                                                                                                                                                                }).catch(err => {
                                                                                                                                                                     console.log(err);
                                                                                                                                                                     reject(err);
                                                                                                                                                                })
                                                                                                                                                           }
                                                                                                                                                      })
                                                                                                                                                 }
                                                                                                                                            }
                                                                                                                                       }).catch(err => {
                                                                                                                                            console.log(err);
                                                                                                                                            reject(err);
                                                                                                                                       })
                                                                                                                                  } else {
                                                                                                                                       console.log("-----4424--------")
                                                                                                                                       resolve({ 'status': "failed", 'message': "Something went wrong. Plz contact support on - 04066006442." });
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

                                                                                }
                                                                           })
                                                                      }).catch(err => {
                                                                           console.log(err)
                                                                           reject(err);
                                                                      })
                                                                 } else {
                                                                      resolve({ 'status': "failed", 'message': "Please select correct state" });
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 reject(err);
                                                            })
                                                       } else {
                                                            console.log("------4462---------")
                                                            resolve({ 'status': "failed", 'message': "Something went wrong. Plz contact support on - 04066006442." });
                                                       }
                                                  })
                                             }
                                        })
                                   } else {
                                        resolve({ 'status': "failed", 'message': "Please select correct state" });
                                   }
                              })
                         } else {
                              console.log("Consumer Registration")
                              //Consumer Registration
                              ff_uid = 0;
                              ff_le_id = null;
                              //serviceable code
                              is_approved = 1;
                              status = 1;
                              let pincode_query = "select count(*)  as count from cities_pincodes where pincode=" + pincode + " && state = '" + stateName + "'";
                              console.log("----4531---", pincode_query);
                              db.query(pincode_query, {}, function (err, pincodeStateChk) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                   } else if (pincodeStateChk[0].count <= 0) {
                                        console.log("heloo")
                                        is_approved = 0;
                                        status = 0;
                                        resolve({ 'status': "failed", 'message': "Please select correct state" });
                                   } else {
                                        let query_2 = "select description from master_lookup  where value = 78001";
                                        db.query(query_2, {}, async function (err, legal_entitycompany_type) {

                                             if (err) {
                                                  console.log(err)
                                                  reject(err);
                                             } else if (Object.keys(legal_entitycompany_type).length > 0) {
                                                  let stateId = await getStateId(stateName);
                                                  console.log("-----4570----", stateId);
                                                  let result = await getRefCode('CU', stateId);
                                                  if (result.length > 0) {
                                                       le_code = result;
                                                       let parent_id = await getLeFromBeat(beat, latitude, longitude);
                                                       if (parent_id) {
                                                            parent_le_id = parent_id;
                                                       } else {
                                                            parent_le_id = 0;
                                                       }
                                                       let businessNameSelf = JSON.stringify(business_legal_name);
                                                       let Address_1_Self = JSON.stringify(address1);
                                                       let Address_2_Self = JSON.stringify(address2)
                                                       //inserting user details into legal_entities table
                                                       let data_5 = "insert into  legal_entities ( business_legal_name ,address1 , legal_entity_type_id , address2 , locality , landmark ,tin_number ,country , state_id , is_approved, city, pincode, business_type_id, latitude, longitude, le_code, parent_id, created_by, gstin, arn_number, created_at, parent_le_id ,pan_number , website_url  , logo,fssai) VALUE (" + businessNameSelf + "," + Address_1_Self + ",'" + customer_type + "'," + Address_2_Self + ",'" + locality + "','" + landmark + "','" + tin_number + "'," + 99 + ",'" + stateId + "','" + is_approved + "','" + city + "','" + pincode + "','" + segment_id + "','" + latitude + "','" + longitude + "','" + le_code + "','" + legal_entitycompany_type[0].description + "'," + ff_uid + ",'" + gstin + "','" + arn_number + "','" + formatted_date + "','" + parent_le_id + "','" + pan_number + "','" + website_url + "' ,'" + logo + "','" + fssai + "')";
                                                       db.query(data_5, {}, function (err, inserted) {
                                                            if (err) {
                                                                 console.log(err.message)
                                                                 reject(err);
                                                            } else if (typeof inserted != 'undefined') {
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
                                                                           console.log("error", err.message);
                                                                           console.log("last_insert_id", last_insert_legal_id);
                                                                           rollBackLegalentityInsertedRecord(last_insert_legal_id);

                                                                           resolve({ 'status': "failed", 'message': "Entered email already exist" });
                                                                      } else if (insert) {
                                                                           last_insert_user_id = insert.insertId;
                                                                           insertInMongoUserTable(mobile_no, last_insert_user_id, customer_token, otp, 1, 0, last_insert_legal_id);
                                                                           if (ff_uid == null) {
                                                                                ff_uid = last_insert_user_id;
                                                                           }

                                                                           if (sales_token == null) {
                                                                                //self registration
                                                                                let data1 = "update users as us set created_by ='" + ff_uid + "', updated_at ='" + formatted_date + "', us.user_id =" + last_insert_user_id + "  where us.user_id =" + last_insert_user_id;
                                                                                db.query(data1, {}, function (err, update) {
                                                                                     if (err) {
                                                                                          console.log(err)
                                                                                          reject(err);
                                                                                     } else {
                                                                                          console.log("1.updated")
                                                                                     }
                                                                                })
                                                                                let data_6 = "update legal_entities as le  set created_by = '" + ff_uid + "',updated_at = '" + formatted_date + "' where le.legal_entity_id = " + last_insert_legal_id;
                                                                                db.query(data_6, {}, function (err, update) {
                                                                                     if (err) {
                                                                                          console.log(err)
                                                                                          reject(err);
                                                                                     } else {
                                                                                          console.log("2.updated")
                                                                                     }
                                                                                })
                                                                           }


                                                                           //we have to add
                                                                           let query_7 = "select cp.city_id from cities_pincodes as cp  where cp.pincode ='" + pincode + "' && cp.officename  ='" + area + "' && cp.city ='" + city + "'";
                                                                           db.query(query_7, {}, async function (err, res) {
                                                                                if (err) {
                                                                                     console.log(err)
                                                                                     reject(err);
                                                                                } else {
                                                                                     area_chk.push(res);
                                                                                     ///used to get warehouse
                                                                                     if (ff_uid && ff_uid != '') {
                                                                                          let le_wh = await getWarehouseFromLeId(parent_le_id);
                                                                                          if (le_wh == null) {
                                                                                               le_wh_id = '';
                                                                                          } else {
                                                                                               le_wh_id = le_wh;
                                                                                          }
                                                                                     } else if (beat && beat != '') {
                                                                                          let le_wh = await getWhFromBeat_New(beat);
                                                                                          if (le_wh == null) {
                                                                                               le_wh_id = '';
                                                                                          } else {
                                                                                               le_wh_id = le_wh;
                                                                                          }
                                                                                     } else {
                                                                                          le_wh_id = '';
                                                                                     }


                                                                                     //used to get hub
                                                                                     let beat_id = await getHub_New(beat);
                                                                                     if (beat_id == null) {
                                                                                          hub = '';
                                                                                     } else {
                                                                                          hub = beat_id;
                                                                                     }

                                                                                     if (area_chk[0].length > 0) {
                                                                                          let area1 = JSON.parse(JSON.stringify(area_chk[[0]]))
                                                                                          //Insert data into customers
                                                                                          let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer,is_swipe ) values (" + last_insert_legal_id + ',' +
                                                                                               volume_class + ',' + noof_shutters + ',' + area1[0].city_id + ",'" + master_manf + "'," + smartphone + ",'" + network + "','" + ff_uid + "','" + beat + "','" + formatted_date + "'," + is_icecream + ',' +
                                                                                               is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";

                                                                                          db.query(insert_user, {}, function (err, area_id) {
                                                                                               if (err) {
                                                                                                    console.log("3623", err.message);
                                                                                                    console.log("last_insert_user_id ", last_insert_user_id);
                                                                                                    console.log("last_insert_legal_entity_id ", last_insert_legal_id);
                                                                                                    rollBackUserInsertedRecord(last_insert_user_id);
                                                                                                    rollBackLegalentityInsertedRecord(last_insert_legal_id);
                                                                                                    reject(err.message);
                                                                                               } else if (typeof area_id != 'undefined') {
                                                                                                    area_chk_id = area_id.insertId;
                                                                                               }
                                                                                          })
                                                                                     } else {
                                                                                          // let state_query = "select name from zone where zone_id = " + state_id;
                                                                                          // db.query(state_query, {}, function (err, res) {
                                                                                          //      if (err) {
                                                                                          //           console.log(err);
                                                                                          //           reject(err);
                                                                                          //      } else if (Object.keys(res).length > 0) {
                                                                                          //           state_name = res;
                                                                                          //      } else {
                                                                                          //           let ros = { 'status': 'failed', 'message': 'Unable to process your request->3837' }
                                                                                          //      }

                                                                                          //  if (state_name.length != 0) {
                                                                                          let insert_city = "insert into cities_pincodes (country_id ,pincode , city , state , officename) values(" + 99 + ',' + pincode + ",'" + city + "','" + stateName + "','" + area + "')";
                                                                                          db.query(insert_city, {}, function (err, inserted) {
                                                                                               if (err) {
                                                                                                    console.log(err);
                                                                                                    reject(err);
                                                                                               } else if (inserted) {
                                                                                                    last_insert_city_id = inserted.insertId
                                                                                               }
                                                                                               //Insert data into customers
                                                                                               let insert_user = "insert into customers (le_id , volume_class , No_of_shutters , area_id , master_manf , smartphone , network ,created_by ,beat_id , created_at , is_icecream , is_milk , is_fridge , is_vegetables ,is_visicooler, dist_not_serv , facilities ,is_deepfreezer, is_swipe) values (" + last_insert_legal_id + ',' +
                                                                                                    volume_class + ',' + noof_shutters + ',' + last_insert_city_id + ",'" + master_manf + "'," + smartphone + ',' + network + ',' + ff_uid + ',' + beat + ",'" + formatted_date + "'," + is_icecream + ',' +
                                                                                                    is_milk + ',' + is_fridge + ',' + is_vegetables + ',' + is_visicooler + ",'" + dist_not_serv + "'," + facilities + ',' + is_deepfreezer + ',' + is_Swipe + ")";
                                                                                               db.query(insert_user, {}, function (err, area_id) {
                                                                                                    if (err) {
                                                                                                         console.log("3623", err.message);
                                                                                                         console.log("last_insert_user_id ", last_insert_user_id);
                                                                                                         console.log("last_insert_legal_entity_id ", last_insert_legal_id);
                                                                                                         rollBackUserInsertedRecord(last_insert_user_id);
                                                                                                         rollBackLegalentityInsertedRecord(last_insert_legal_id);
                                                                                                         reject(err.message);
                                                                                                    } else if (typeof area_id != 'undefined') {
                                                                                                         area_chk_id = area_id.insertId;
                                                                                                    }
                                                                                               })
                                                                                          })
                                                                                          // }
                                                                                          // })

                                                                                     }

                                                                                     ///master lookup
                                                                                     let data2 = "select * from master_lookup as ml where ml.value = 78002";
                                                                                     db.query(data2, {}, async function (err, master) {
                                                                                          if (err) {
                                                                                               console.log(err)
                                                                                               reject(err);
                                                                                          } else if (Object.keys(master).length > 0) {
                                                                                               desc = master[0].description
                                                                                               let query_9 = "select u.user_id,u.mobile_no,u.is_active,u.otp,le.legal_entity_id,u.profile_picture from users as u left join legal_entities as le ON le.legal_entity_id = u.legal_entity_id where u.mobile_no =" + mobile_no + "&&  u.is_active = 1 && le.legal_entity_type_id IN( select value from master_lookup as ml where ml.mas_cat_id =" + desc + "&&  ml.is_active =1) ";
                                                                                               db.query(query_9, {}, async function (err, user) {
                                                                                                    if (err) {
                                                                                                         console.log(err);
                                                                                                         reject(err);
                                                                                                    } else if (Object.keys(user).length > 0) {
                                                                                                         users.push(JSON.parse(JSON.stringify(user[[0]])))
                                                                                                         let entity_query = "select legal_entity_id,legal_entity_type_id from legal_entities where legal_entity_id =" + last_insert_legal_id;
                                                                                                         db.query(entity_query, {}, async function (err, respon) {
                                                                                                              if (err) {
                                                                                                                   console.log(err);
                                                                                                                   reject(err);
                                                                                                              } else if (Object.keys(respon).length > 0) {
                                                                                                                   legal.push(JSON.parse(JSON.stringify(respon[[0]])));
                                                                                                                   let query_10 = "select master_lookup_name from master_lookup where value = '" + license_type + "'";
                                                                                                                   db.query(query_10, {}, async function (err, license) {
                                                                                                                        if (err) {
                                                                                                                             console.log(err);
                                                                                                                             reject(err);
                                                                                                                        } else if (Object.keys(license).length > 0) {
                                                                                                                             if (filepath1.length > 0 && typeof filepath1[0] != 'undefined') {
                                                                                                                                  //Insert data into lelgal entity doc table
                                                                                                                                  let insert_1 = "insert into legal_entity_docs (legal_entity_id , doc_url , doc_type,created_at) values (" + last_insert_legal_id + ",'" + filepath1 + "','" + license[0].master_lookup_name + "','" + formatted_date + "')";
                                                                                                                                  db.query(insert_1, {}, function (err, insert) {
                                                                                                                                       if (err) {
                                                                                                                                            console.log(err);
                                                                                                                                            reject(err);
                                                                                                                                       } else {
                                                                                                                                            last_insert_doc_id = insert.insertId;
                                                                                                                                       }
                                                                                                                                  })
                                                                                                                             }
                                                                                                                        }
                                                                                                                   })

                                                                                                                   //setting user preferences
                                                                                                                   if (users.length != 0) {
                                                                                                                        if (pref_value != null || bstart_time != null || bend_time != null) {
                                                                                                                             //Insert data into user_prefences 
                                                                                                                             let insert_2 = "insert into user_preferences (user_id ,preference_name ,preference_value , preference_value1 ,business_start_time , business_end_time , sms_subscription  ,create_at) values (" + users[0].user_id + ',' + "'expected delivery' ,'" + pref_value + "','" + pref_value1 + "','" + bstart_time + "','" + bend_time + "'," + sms_notification + ",'" + formatted_date + "')";
                                                                                                                             db.query(insert_2, {}, function (err, inserted) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err);
                                                                                                                                       reject(err);
                                                                                                                                  } else if (inserted) {
                                                                                                                                       last_insert_userpref_id = inserted.insertId;

                                                                                                                                  }
                                                                                                                             })
                                                                                                                        }

                                                                                                                   }

                                                                                                                   if (sales_token != null && users.length != 0) {
                                                                                                                        let insert_call_logs = "insert into ff_call_logs (ff_id , user_id , legal_entity_id , activity , check_in , check_in_lat , check_in_long ,created_at) values (" + ff_uid + ',' + users[0].user_id + ',' + legal[0].legal_entity_id + ',' + 107000 + ",'" + formatted_date + "'," + latitude + ',' + longitude + ",'" + formatted_date + "')";
                                                                                                                        db.query(insert_call_logs, {}, function (err, inserted) {
                                                                                                                             if (err) {
                                                                                                                                  console.log(err);
                                                                                                                                  reject(err);
                                                                                                                             } else if (inserted) {
                                                                                                                                  last_insert_fflog_id = inserted.insertId;
                                                                                                                             }

                                                                                                                        })
                                                                                                                   }

                                                                                                                   //used to get roleid
                                                                                                                   let res = await getRoleId();
                                                                                                                   if (res) {
                                                                                                                        role_id = res;
                                                                                                                        if (role_id != null) {
                                                                                                                             let user_query = "insert into user_roles (role_id  ,user_id , created_by ,  created_at) values (" + role_id + ',' + users[0].user_id + ',' + ff_uid + ",'" + formatted_date + "')";
                                                                                                                             db.query(user_query, {}, async function (err, last_insert) {
                                                                                                                                  if (err) {
                                                                                                                                       console.log(err);
                                                                                                                                       reject(err);
                                                                                                                                  } else if (last_insert) {
                                                                                                                                       last_insert_role_id = last_insert.insertId;
                                                                                                                                       //user to get user features
                                                                                                                                       let mobile = await getFeatures(users[0].user_id, 2);
                                                                                                                                       if (mobile == null) {
                                                                                                                                            mobile_feature = [];
                                                                                                                                       } else {
                                                                                                                                            mobile_feature = mobile;
                                                                                                                                            if (users.length != 0) {
                                                                                                                                                 let data_insert = "insert into user_ecash_creditlimit (user_id , creditlimit) value (" + users[0].user_id + ',' + credit_limit + ")";
                                                                                                                                                 db.query(data_insert, {}, function (err, inserted) {
                                                                                                                                                      if (err) {
                                                                                                                                                           console.log(err);
                                                                                                                                                           reject(err);
                                                                                                                                                      } else {
                                                                                                                                                           console.log("usercredit limit inserted successfully")
                                                                                                                                                      }
                                                                                                                                                 })
                                                                                                                                            }


                                                                                                                                            if (mfc != '') {
                                                                                                                                                 let insert1 = "insert into mfc_customer_mapping (mfc_id  , cust_le_id , credit_limit , is_active) value (" + mfc + ',' + legal[0].legal_entity_id + ',' + credit_limit + "," + 1 + ")";
                                                                                                                                                 db.query(insert1, {}, function (err, inserted) {
                                                                                                                                                      if (err) {
                                                                                                                                                           console.log(err);
                                                                                                                                                           reject(err);
                                                                                                                                                      } else {
                                                                                                                                                           console.log("mfc inserted")
                                                                                                                                                      }
                                                                                                                                                 })
                                                                                                                                            }


                                                                                                                                            if (last_insert_legal_id != '' && last_insert_user_id != '') {
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

                                                                                                                                                 console.log("gst_doc ,fssai_doc", gst_doc, fssai_doc);
                                                                                                                                                 if (fssai_doc.length > 0 && typeof fssai_doc[0] != 'undefined') {
                                                                                                                                                      //Insert data into lelgal entity doc table
                                                                                                                                                      let doc_name = "Food License Document";
                                                                                                                                                      let doc_type = "FSSAI";
                                                                                                                                                      let insert_1 = "insert into legal_entity_docs (doc_url , doc_type , doc_name , legal_entity_id  , created_at , created_by) values ('" + fssai_doc + "','" + doc_type + "','" + doc_name + "','" + last_insert_legal_id + "','" + formatted_date + "','" + ff_uid + "')";
                                                                                                                                                      db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                           if (err) {
                                                                                                                                                                console.log(err);
                                                                                                                                                                reject(err);
                                                                                                                                                           } else {
                                                                                                                                                                console.log(" fssai _doc path inserted successfully")
                                                                                                                                                           }
                                                                                                                                                      })
                                                                                                                                                 }
                                                                                                                                                 //gst document upload
                                                                                                                                                 if (gst_doc.length > 0 && typeof gst_doc[0] != 'undefined') {
                                                                                                                                                      //Insert data into lelgal entity doc table
                                                                                                                                                      let doc_name = "GST License Document";
                                                                                                                                                      let doc_type = "GSTIN";
                                                                                                                                                      let insert_1 = "insert into legal_entity_docs (doc_url , doc_type , doc_name , legal_entity_id  , created_at , created_by) values ('" + gst_doc + "','" + doc_type + "','" + doc_name + "','" + last_insert_legal_id + "','" + formatted_date + "','" + ff_uid + "')";
                                                                                                                                                      db.query(insert_1, {}, function (err, insert) {
                                                                                                                                                           if (err) {
                                                                                                                                                                console.log(err);
                                                                                                                                                                reject(err);
                                                                                                                                                           } else {
                                                                                                                                                                console.log("gst_doc path inserted successfully")
                                                                                                                                                           }
                                                                                                                                                      })
                                                                                                                                                 }

                                                                                                                                                 let delete_user = "delete from user_temp where mobile_no ='" + mobile_no + "'";
                                                                                                                                                 db.query(delete_user, {}, function (err, deleted) {
                                                                                                                                                      if (err) {
                                                                                                                                                           console.log(err);
                                                                                                                                                           reject(err);
                                                                                                                                                      } else {
                                                                                                                                                           console.log("User_temop delete ")
                                                                                                                                                      }
                                                                                                                                                 })
                                                                                                                                                 deleteFromMongoUserTemp(mobile_no);
                                                                                                                                                 //  if (beatId > 0) {
                                                                                                                                                 let Querys = "select pjp_pincode_area.spoke_id ,pjp_pincode_area.le_wh_id from pjp_pincode_area  LEFT JOIN  spokes ON spokes.spoke_id = pjp_pincode_area.spoke_id where pjp_pincode_area_id=" + beat;
                                                                                                                                                 db.query(Querys, {}, function (err, hubDetails) {
                                                                                                                                                      if (err) {
                                                                                                                                                           console.log(err);
                                                                                                                                                           reject(err);
                                                                                                                                                      } else if (Object.keys(hubDetails[0]).length > 0) {
                                                                                                                                                           let hubDetail = JSON.parse(JSON.stringify(hubDetails[0]));
                                                                                                                                                           hubId = hubDetail.hasOwnProperty('le_wh_id') ? hubDetails[0].le_wh_id : 0;
                                                                                                                                                           spokeId = hubDetail.hasOwnProperty('spoke_id') ? hubDetails[0].spoke_id : 0;
                                                                                                                                                           if (hubId > 0 && spokeId > 0) {
                                                                                                                                                                // string[0].hub_id = hubId;
                                                                                                                                                                // string[0].spoke_id = spokeId;
                                                                                                                                                                let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legal[0].legal_entity_id;
                                                                                                                                                                db.query(customer, {}, function (err, customers) {
                                                                                                                                                                     if (err) {
                                                                                                                                                                          console.log(err);
                                                                                                                                                                          reject(err);
                                                                                                                                                                     } else if (Object.keys(customers).length > 0) {
                                                                                                                                                                          console.log("customer updated successfully");
                                                                                                                                                                          let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                                          db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                                               if (err) {
                                                                                                                                                                                    console.log(err);
                                                                                                                                                                                    reject(err);
                                                                                                                                                                               } else if (retailer_insert) {
                                                                                                                                                                                    console.log("retailer_flat inserted")
                                                                                                                                                                                    resolve(res);
                                                                                                                                                                               }
                                                                                                                                                                          })
                                                                                                                                                                     }
                                                                                                                                                                })
                                                                                                                                                           }
                                                                                                                                                      }

                                                                                                                                                 })


                                                                                                                                                 // let Retailer_Update_Query = "CALL retailer_flat_insert(" + mobile_no + ")";
                                                                                                                                                 // db.query(Retailer_Update_Query, {}, function (err, retailer_insert) {
                                                                                                                                                 //      if (err) {
                                                                                                                                                 //           console.log(err);
                                                                                                                                                 //           reject(err);
                                                                                                                                                 //      } else if (retailer_insert) {
                                                                                                                                                 //           console.log("retailer_flat inserted")
                                                                                                                                                 //           resolve(res);
                                                                                                                                                 //      }
                                                                                                                                                 // })

                                                                                                                                            } else {

                                                                                                                                                 let failed_status = { status: "failed", message: "Unable to process your request.Plz contact support on - 04066006442." }
                                                                                                                                                 resolve(failed_status);
                                                                                                                                            }

                                                                                                                                       }
                                                                                                                                  }
                                                                                                                             })
                                                                                                                        }
                                                                                                                   }
                                                                                                              } else {
                                                                                                                   console.log("-----4424--------")
                                                                                                                   resolve({ 'status': "failed", 'message': "Something went wrong. Plz contact support on - 04066006442." });
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

                                                            }
                                                       })

                                                  } else {
                                                       resolve({ 'status': "failed", 'message': "Please select correct state" });
                                                  }

                                             } else {
                                                  console.log("------4462---------")
                                                  resolve({ 'status': "failed", 'message': "Something went wrong. Plz contact support on - 04066006442." });
                                             }
                                        })
                                   }
                              })


                         }
                    } else {
                         console.log("Please genarate Otp");
                         resolve({ 'status': "failed", 'message': "Something went wrong. Plz contact support on - 04066006442." });
                    }
               })

          })
     } catch (err) {
          console.log(err);
          resolve({ 'status': "failed", 'message': "Internal server error" });
     }
}



/**
 * purpose : Used to validate appid & customer token detailes
 * request : customer_token or sales_token
 * return : return  token_status =  1 or 0 ,
 * author : Deepak Tiwari
 */
module.exports.validateToken = async function (token) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(token);
               let data = { 'token_status': 0 };
               let query = "select user_id from users where password_token = " + string
               db.query(query, {}, async function (err, rows) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(rows).length > 0) {
                         data = { 'token_status': 1 }
                         await resolve(data)
                    } else {
                         await resolve(data)
                    }
               })
          })
     } catch (err) {
          console.log(err)
     }
}


module.exports.getUserId = function (customer_token) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select user_id,firstname, lastname, legal_entity_id from users as u where u.password_token = '" + customer_token + "' || u.lp_token ='" + customer_token + "'";
               db.query(query, {}, function (err, rows) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         resolve(rows);
                    } else {
                         resolve('')
                    }
               })
          })

     } catch (err) {
          console.log(err);
          return ({ 'status': "failed", 'message': 'Internal Server Error' })
     }
}


//getRetailerInfo
module.exports.getRetailerInfo = function (custId) {
     try {
          console.log("customer", custId)
          return new Promise((resolve, reject) => {
               let query = "select l.business_legal_name, l.address1, l.address2, l.locality, l.city, l.pincode, zone.name as state_name, u.mobile_no from legal_entities as l left join zone ON zone.zone_id =  l.state_id left join users as u  ON  u.legal_entity_id = l.legal_entity_id where l.legal_entity_id =" + custId;
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(result).length > 0) {
                         resolve(result[0])
                    } else {
                         resolve('');
                    }
               })
          })

     } catch (err) {
          console.log(err);
          return ({ 'status': 'failed', 'messaeg': 'Internal Server Error' });

     }
}

/*
 *   For: send Email
 *   Author: Deepak Tiwari
 *   Request params parameters: subject, email, body
 *   Returns:Send email to user mail
 */
exports.sendMail = function (subject, email, body) {
     return new Promise((resolve, reject) => {
          let transporter = nodemailer.createTransport({
               host: 'smtp.office365.com',
               port: 587,
               secureConnection: true, // upgrade later with STARTTLS
               auth: {
                    user: config.EmailUsername,
                    pass: config.EmailPassword
               }
          });

          var Mail_list = email;
          let HelperOptions = {
               from: '"no-reply" <tracker@ebutor.com>',//support@ebutor.com
               to: Mail_list,
               subject: JSON.stringify(subject),
               html: body
          };
          // transporter.use('compile', htmlToText(HelperOptions));
          transporter.sendMail(HelperOptions, (error, info) => {
               if (error) {
                    console.log("error =====>", error);
                    console.log("successful ===>", info);
                    reject(error)
               }
               else {
                    console.log("successful ===>", info);
                    resolve(info)
               }
          });
     })

};


function getUsersByCode_mysql(messageCode) {
     try {
          let userList = [];
          let notificationUsers;
          return new Promise((resolve, reject) => {
               let query = "select notification_recipients.notificaiton_recipient_roles, notification_recipients.notificaiton_recipient_users, notification_recipients.notificaiton_recipient_legal_entities from notification_template left join notification_recipients ON notification_recipients.notification_template_id = notification_template.notification_template_id where notification_template.notification_code='" + messageCode + "'";
               db.query(query, {}, function (err, rows) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         if (rows != '') {

                              let roles = rows[0].notificaiton_recipient_roles.split(',');
                              let users = rows[0].notificaiton_recipient_users.split(',');
                              let legalEntities = rows[0].notificaiton_recipient_legal_entities.split(',');

                              if (users != '') {
                                   let activeUserQuery = "select users.user_id from users where users.is_active = 1  &&  users.user_id IN (" + users + ")";
                                   db.query(activeUserQuery, {}, function (err, activeUser) {
                                        if (err) {
                                             console.log(err);
                                             reject(err);
                                        } else if (Object.keys(activeUser).length > 0) {
                                             userList = activeUser;
                                        }

                                        if (roles != '') {
                                             let rolesListQuery = " select user_roles.user_id from user_roles join users ON users.user_id =  user_roles.user_id  where users.is_active  = 1  && user_roles.role_id IN (" + roles + ")";
                                             db.query(rolesListQuery, {}, function (err, rolesList) {
                                                  if (err) {
                                                       console.log(err);
                                                       reject(err);
                                                  } else if (Object.keys(rolesList).length > 0) {

                                                       if (userList != '') {
                                                            userList = userList.concat(rolesList);
                                                       } else {
                                                            userList.push(rolesList);
                                                       }
                                                  }//legal_entity id
                                                  if (legalEntities != '') {
                                                       let legalEntityQuery = "select user_id from users where legal_entity_id = " + legalEntities + " &&  is_active = 1";
                                                       db.query(legalEntityQuery, {}, function (err, legalEntityList) {
                                                            if (err) {
                                                                 console.log(err);
                                                                 reject(err);
                                                            } else if (Object.keys(legalEntityList).length > 0) {
                                                                 if (userList != '') {
                                                                      userList = userList.concat(legalEntityList);
                                                                 } else {
                                                                      userList.push(legalEntityList);
                                                                 }
                                                            }
                                                       })

                                                  }
                                                  resolve(JSON.parse(JSON.stringify(userList)))
                                             })
                                        }

                                   })
                              }




                         } else {
                         }
                    }
               })

          })
     } catch (err) {
          console.log(err)
     }
}


module.exports.getUsersByCode = function (messageCode) {
     try {
          let response = {};
          return new Promise((resolve, reject) => {
               let status = false;
               if (messageCode != '') {
                    getUsersByCode_mysql(messageCode).then(response => {
                         if (response != '') {
                              let res = [];
                              Object.keys(response).filter(value => {
                                   res.push(response[value].user_id);
                              })
                              resolve(res);
                         }
                    }).catch(err => {
                         console.log(err);
                    })

               } else {
                    resolve({ 'status': status });
               }

          })

     } catch (err) {
          console.log(err);
     }
}

module.exports.getUserEmailByIds = function (Ids) {
     try {
          return new Promise((resolve, reject) => {
               let email;
               if (Ids.length > 0) {
                    let query = "select email_id from users where user_id IN (" + Ids + ") && is_active = 1";
                    db.query(query, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(result).length > 0) {
                              let res = [];
                              Object.keys(result).filter(value => {
                                   res.push(result[value].email_id);
                              })
                              resolve(res);
                         } else {
                              email = [];
                              resolve(email)
                         }
                    })
               }
          })
     } catch (err) {
          console.log(err);
          let error = { 'status': 200, 'message': 'Internal server error' }
          return error;

     }
}



/*
* Function name: resendotp
* Description: Used to resend otp
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 June 2016
* Modified Date & Reason:
*/

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
               let query = "select * from users where mobile_no =" + telephone;
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err);
                    } else {
                         if (response.length > 0) {
                              let userChk = JSON.parse(JSON.stringify(response[0]));
                              // Checking weather ff is enabled or not
                              ffCheck = checkPermissionByFeatureCode('EFF001', userChk.user_id);
                              if (ffCheck != 1) {
                                   //checking weather user is ff or self user generated otp
                                   ffCheck = checkPermissionByFeatureCode('MFF001', userChk.user_id);
                              }
                         } else {
                              ffCheck = -1;
                         }

                         if (ffCheck == 1) {
                              otpFlag = 1;
                              resendGeneratedOtp(telephone, otpFlag, customer_token, custflag, userChk[0].user_id, app_flag).then(response => {
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
                                        let query_1 = "select user.user_id,leg.legal_entity_id,leg.is_approved,user.is_active from users as user left join legal_entities as leg ON leg.legal_entity_id  = user.legal_entity_id where user.mobile_no =" + telephone + " &&  leg.legal_entity_type_id  IN( select value from master_lookup as ml where ml.mas_cat_id = " + description + " && ml.is_active = 1)";
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
                                                       } else if ((userNumRow > 0 && isActive == 1) || (customer_token != '' && custflag == 2)) {

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


module.exports.InsertFlatTable = function (telephone) {
     return new Promise((resolve, reject) => {
          try {
               let query = "CALL retailer_flat_insert(" + telephone + ")";
               db.query(query, {}, function (err, response) {
                    console.log("query", query);
                    if (err) {
                         resolve(err);
                    } else {
                         resolve({ status: "success", message: 'inserted successfully' })
                    }
               })

          } catch (err) {
               console.log(err);
          }
     })

}