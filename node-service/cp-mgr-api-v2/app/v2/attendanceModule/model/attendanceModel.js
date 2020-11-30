const Sequelize = require('sequelize');
const sequelize = require('../../../config/sequelize');
const moment = require('moment');
const mongoose = require('mongoose');
const user = mongoose.model('User');
const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;
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
                    res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
               } else if (response > 0) {
                    resolve(response)
               } else {
                    resolve(count)
               }
          })
     });
}


/*
Purpose : getMyRoles function used get  role_id
author : Deepak Tiwari
Request : Require userId
Resposne : Returns user  role_id
*/
module.exports.getMyRoles = function (userId) {
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
                    sequelize.query(data).then(rows => {
                         if (rows.length > 0) {
                              let response = JSON.parse(JSON.stringify(rows[0]));
                              let result = response.filter((value) => {
                                   return value.role_id;
                              });

                              return resolve(result[0]);
                         } else {
                              return resolve(0);
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    console.log("Role Not Found For Specific User")
               }
          })
     } catch (err) {
          console.log(err)
     }

}

function getMyRole(userId) {
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
                    sequelize.query(data).then(rows => {
                         if (rows.length > 0) {
                              let response = JSON.parse(JSON.stringify(rows[0]));
                              let result = response.filter((value) => {
                                   return value.role_id;
                              });

                              return resolve(result[0]);
                         } else {
                              return resolve(0);
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    console.log("Role Not Found For Specific User")
               }
          })
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
function checkPermissionByFeatureCode(featureCode, userId) {
     return new Promise((resolve, reject) => {
          try {
               // userId is for superadmin
               if (userId == 1) {
                    return resolve(true);
               } else {
                    let data = "select count(features.name)  from role_access JOIN features ON role_access.feature_id = features.feature_id  JOIN user_roles ON role_access.role_id = user_roles.role_id where user_roles.user_id =" + userId + "&& features.feature_code ='" + featureCode + "'&& features.is_active = 1";
                    sequelize.query(data).then(name => {
                         let count = JSON.parse(JSON.stringify(name[0]));
                         if (count > 0) {
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
Purpose : getSubroles() Used to get user sub role based  user permission 
Author :Deepak Tiwari
Request : (superroleid, userId,
Resposne : Returns users subrole
*/
module.exports.getSubroles = function (superroleid, userId, res = []) {
     return new Promise((resolve, reject) => {
          try {
               let response;
               let globalAccess;
               let getSubRoles;
               let subRoleId;
               if (res == '') {
                    response = [];
                    response = response.concat(superroleid);
               } else {
                    response = res;

               }
               //fetching user global permission
               checkPermissionByFeatureCode("GLB0001", userId).then(result => {
                    console.log("result====>", result);
                    globalAccess = result;
                    if (globalAccess) {
                         let query = "select GROUP_CONCAT(role_id) AS role_id  from roles where is_active = 1 ";
                         sequelize.query(query).then(rows => {
                              let response = JSON.parse(JSON.stringify(rows[0]))
                              getSubRoles = response;
                              if (getSubRoles[0].role_id != null) {
                                   subRoleId = getSubRoles[0].role_id.split(',');
                                   response = response.concat(subRoleId);

                              }
                              return resolve(response);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         let query = "select GROUP_CONCAT(role_id) AS role_id from roles where parent_role_id IN(" + superroleid + ")";
                         sequelize.query(query).then(rows => {
                              console.log("row====>123", rows);
                              let response = JSON.parse(JSON.stringify(rows[0]))
                              getSubRoles = response;
                              if (getSubRoles[0].role_id != null) {
                                   subRoleId = getSubRoles[0].role_id.split(',');
                                   response = response.concat(subRoleId);
                                   this.getSubroles(subRoleId, userId, response).then(row => {
                                        console.log("========>145", row);
                                        return resolve(row[0]);
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })

                              } else {
                                   return resolve(res);
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

/*
Purpose : function Used to get user legal entity based  on userid 
Author :Deepak Tiwari
Request :  userId
Resposne : Returns users legalEntity.
*/
module.exports.getMyLegalentityId = function (userId = null) {
     return new Promise((resolve, reject) => {
          try {
               let legalEntityIdUser = '';
               let currentUserId;
               if (!userId) {
                    currentUserId = '';
               } else {
                    currentUserId = userId;
               }
               if (currentUserId > 0) {
                    let query = " select user_id from users where user_id =" + currentUserId;
                    sequelize.query(query).then(user_id => {
                         console.log("===> 197", user_id);
                         let userID = JSON.parse(JSON.stringify(user_id[0]))
                         if (userID != '') {
                              legalEntityIdUser = userID;
                         } else {
                              legalEntityIdUser = '';
                         }

                         return resolve(legalEntityIdUser[0]);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    return resolve('');
               }
          } catch (err) {
               console.log(err.message);
          }
     })

}

/*
Purpose : function() Used to get  legal entity based  on reportingId  
Author :Deepak Tiwari
Request :  reportingId
Resposne : Returns users legalEntity.
*/
module.exports.getMyLegalentityIdofReporting = function (reportingId, res = []) {
     return new Promise((resolve, reject) => {
          try {
               let response = [];
               if (res == '') {
                    response = response.concat(reportingId);
               } else {
                    response = res;
               }
               let query = " select GROUP_CONCAT(user_id) AS user_id from users where reporting_manager_id IN(" + reportingId + ")  && is_active = 1 ";
               sequelize.query(query).then(getSubReporting => {
                    let reporting = JSON.parse(JSON.stringify(getSubReporting[0]));
                    if (reporting[0].user_id != null) {
                         let getSubReportingId = reporting[0].user_id.split(',');
                         response = resposne.concat(getSubReportingId);
                         return resolve(getMyLegalentityIdofReporting(getSubReportingId, response));
                    } else {
                         return resolve(res);
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


/*
Purpose : checkPermissionByFeatureCode() Used to check user permission based on userid and featurecode
Author :Deepak Tiwari
Request : featureCode, userId
Resposne : Returns Mobile features
*/
function checkPermissionByFeatureCode_1(featureCode, userId) {
     try {
          // userId is for superadmin
          if (userId == 1) {
               return true;
          } else {
               let data = "select count(features.name)  from role_access JOIN features ON role_access.feature_id = features.feature_id  JOIN user_roles ON role_access.role_id = user_roles.role_id where user_roles.user_id =" + userId + "&& features.feature_code ='" + featureCode + "'&& features.is_active = 1";
               let count;
               sequelize.query(data).then(name => {
                    let count = JSON.parse(JSON.stringify(name[0]));
                    if (count > 0) {
                         return true;
                    } else {
                         return false;
                    }
               }).catch(err => {
                    console.log(err);
                    return '';
               })
          }
     } catch (err) {
          console.log(err)
     }


}

/*
Purpose : function() Used to get  legal entity based  on DC ID
Author :Deepak Tiwari
Request :  Dcid
Resposne : Returns users legalEntity.
*/
module.exports.getlegalidbasedondcid = function (dcid) {
     try {
          return new Promise((resolve, reject) => {
               let query = " select legal_entity_id  from legalentity_warehouses where le_wh_id =" + dcid;
               sequelize.query(query).then(response => {
                    if (response.length > 0) {
                         let legalId = JSON.parse(JSON.stringify(response[0]));
                         resolve(legalId[0])
                    } else {
                         resolve('')
                    }

               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          })
     } catch (err) {
          console.log(err);
          reject(err);
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
               getMyRole(userId).then((role) => {
                    console.log('roles', Object.keys(role).length);
                    if (role != null) {
                         for (i = 0; i <= Object.keys(role).length; i++) {
                              if (i != Object.keys(role).length) {
                                   console.log("roles", role.role_id)
                                   currentRoles.push(role.role_id);
                              } else {
                                   break;
                              }
                         }
                         //fetching role_id from roles table based on currentRoles
                         let data = "select role_id from roles r where r.role_id in (" + currentRoles + ") && r.is_support_role =" + 1;
                         sequelize.query(data).then(rows => {
                              console.log("=====>391", rows[0].length);
                              if (rows[0].length > 0) {
                                   let json = JSON.parse(JSON.stringify(rows[0]));
                                   return resolve(json);
                              } else {
                                   console.log("hello")
                                   resolve('');
                              }

                         }).catch(err => {
                              console.log(err);
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

//someproblem is there which i need to resolve later
module.exports.getSuppliersByUser = function (userId, legalEntityId, dcid = "", roleId = [], reportinglegalId = [], ignoreusers = 0) {
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
                    } else {
                         console.log("===>.432");
                         isSupportRole = res;
                    }
               }).catch(err => {
                    console.log(err.message);
               })
               //checking user permission based on userid
               checkPermissionByFeatureCode("GLB0001", userId).then(responsed => {
                    console.log("responsed", responsed, userId, dcid);
                    if (responsed) {
                         globalAccess = responsed;
                    } else {
                         globalAccess = '';
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })

               if (userId > 0) {
                    checkPermissionByFeatureCode('FFUSERS001', userId).then(result => {
                         console.log("=====>443", result);
                         if (result) {
                              let roleid = roleId;
                              let reportinglegalid = reportinglegalId;
                              if (dcid != "") {
                                   let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id";
                                   sequelize.query(query).then(row => {
                                        if (row != '') {
                                             response.push(JSON.parse(JSON.stringify(row[0])));
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }
                              if (dcid != "" && ignoreusers == 1) {
                                   let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where legalentity_warehouses.le_wh_id =" + dcid;
                                   sequelize.query(query).then(row => {
                                        if (row != '') {
                                             response.push(JSON.parse(JSON.stringify(row[0])));
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }
                              console.log("globalaccess", globalAccess)

                              if (reportinglegalid.length > 0) {
                                   if (!globalAccess) {
                                        let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where users.reporting_manager_id  IN (" + reportinglegalid + ")";
                                        sequelize.query(query).then(row => {
                                             if (row != '') {
                                                  response.push(JSON.parse(JSON.stringify(row[0])));
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                   }
                              } else if (ignoreusers == '') {
                                   let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where users.reporting_manager_id =" + userId;
                                   sequelize.query(query).then(row => {
                                        if (row != '') {
                                             response.push(JSON.parse(JSON.stringify(row[0])));
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }
                              if (roleid != null && ignoreusers == 1) {
                                   let query = "select * from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where user_roles.role_id IN (" + roleid + ")  && user_permssion.permission_level_id = 6  && group By user_roles.user_id";
                                   sequelize.query(query).then(row => {
                                        if (row != '') {
                                             response.push(JSON.parse(JSON.stringify(row[0])));
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }

                              let query = "select users.user_id from users join user_roles ON users.user_id = user_roles.user_id  join user_permssion ON user_permssion.user_id = users.user_id join legalentity_warehouses ON legalentity_warehouses.bu_id = user_permssion.object_id where is_active = 1 group by users.user_id";
                              sequelize.query(query).then(row => {
                                   if (row != '') {
                                        response.push(JSON.parse(JSON.stringify(row[0])));
                                   }
                              }).catch(err => {
                                   console.log(err);
                              })
                              resolve(response);
                         } else {
                              if (!(userId in fetched_reporting_ids)) {
                                   if (dcid != '') {
                                        let query = "select  * from users join legalentity_warehouses ON legalentity_warehouses.legal_entity_id =  users.legal_entity_id";
                                        sequelize.query(query).then(row => {
                                             if (row != '') {
                                                  response.push(JSON.parse(JSON.stringify(row[0])));
                                             }
                                             if (response.length > 0) {
                                                  let array = JSON.parse(JSON.stringify(response[[0]]))
                                                  for (let i = 0; i < array.length > 0; i++) {

                                                       suppliers.push(array[i]);
                                                       this.getSuppliersByUser(array[i].user_id, legalEntityId, dcid, roleId, reportinglegalId, ignoreusers).then(resulted_data => {
                                                            if (resulted_data != null) {
                                                                 final_array.push(resulted_data);
                                                                 resolve(final_array[0]);
                                                            }
                                                       }).catch(err => {
                                                            console.log(err.message)
                                                       })
                                                  }

                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })


                                        console.log("resposen", response)
                                   } else {
                                        let query = " select  *  from users join legalentity_warehouses ON legalentity_warehouses.legal_entity_id =  users.legal_entity_id where users.reporting_manager_id =" + userId;
                                        sequelize.query(query).then(row => {
                                             if (row != '') {
                                                  response.push(JSON.parse(JSON.stringify(row[0])));
                                             }
                                             if (response.length > 0) {
                                                  let array = JSON.parse(JSON.stringify(response[[0]]))
                                                  for (let i = 0; i < array.length > 0; i++) {
                                                       suppliers.push(array[i]);
                                                       this.getSuppliersByUser(array[i].user_id, legalEntityId, dcid, roleId, reportinglegalId, ignoreusers).then(resulted_data => {
                                                            if (resulted_data != null) {
                                                                 final_array.push(resulted_data);
                                                            }
                                                       }).catch(err => {
                                                            console.log(err.message)
                                                       })
                                                  }
                                                  resolve(final_array[0]);
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                   }
                                   if (dcid != null && ignoreusers == 1) {
                                        let query = " select  * from users join legalentity_warehouses ON legalentity_warehouses.legal_entity_id =  users.legal_entity_id where legalentity_warehouses.le_wh_id =" + dcid;
                                        sequelize.query(query).then(row => {
                                             if (row != '') {
                                                  response.push(JSON.parse(JSON.stringify(row[0])));
                                             }

                                             if (response.length > 0) {
                                                  let array = JSON.parse(JSON.stringify(response[[0]]))
                                                  for (let i = 0; i < array.length > 0; i++) {
                                                       suppliers.push(array[i]);
                                                       this.getSuppliersByUser(array[i].user_id, legalEntityId, dcid, roleId, reportinglegalId, ignoreusers).then(resulted_data => {
                                                            if (resulted_data != null) {
                                                                 final_array.push(resulted_data);
                                                            }
                                                       }).catch(err => {
                                                            console.log(err.message)
                                                       })
                                                  }
                                                  resolve(final_array[0]);
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                   } else {
                                        let query = "select  users.user_id  from users join legalentity_warehouses ON legalentity_warehouses.legal_entity_id =  users.legal_entity_id";
                                        sequelize.query(query).then(row => {
                                             if (row != '') {
                                                  response.push(JSON.parse(JSON.stringify(row[0])));
                                             }

                                             if (response.length > 0) {
                                                  let array = JSON.parse(JSON.stringify(response[[0]]))
                                                  for (let i = 0; i < array.length > 0; i++) {
                                                       suppliers.push(array[i]);
                                                       this.getSuppliersByUser(array[i].user_id, legalEntityId, dcid, roleId, reportinglegalId, ignoreusers).then(resulted_data => {
                                                            if (resulted_data != null) {
                                                                 final_array.push(resulted_data);
                                                            }
                                                       }).catch(err => {
                                                            console.log(err.message)
                                                       })
                                                  }
                                                  resolve(final_array[0]);
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                        fetched_reporting_ids.push(userId)

                                   }

                              }

                         }

                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })


               }

          })
     } catch (err) {
          reject(err)
     }
}


module.exports.getUserData = function (user_ids, date) {
     return new Promise((resolve, reject) => {
          try {
               let userIds;
               let sendDate = JSON.stringify(date);
               if (typeof user_ids == 'object') {
                    userIds = user_ids.join();
               }

               let query = " SELECT u.user_id , GetUserName(u.user_id, 2) AS username  ,getRolesNamesbyUserId(u.user_id) AS roles, IFNULL(is_present, 1) AS is_present FROM users AS u LEFT JOIN attendance AS a  ON u.user_id = a.user_id  AND a.attn_date BETWEEN " + sendDate + " AND " + sendDate + "  WHERE FIND_IN_SET(u.user_id, " + userIds + ") and u.is_active = 1"
               sequelize.query(query).then(result => {
                    console.log("query =>450", result);
                    resolve(result);
               }).catch(err => {
                    console.log(err);
               })
          } catch (err) {
               console.log(err);
               let error = { 'status': 'failed', 'message': err.message, 'data': [] }
               resolve(error);
          }
     })

}

function checkAttendance(user_id, date) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select count(user_id) as user_count from attendance where user_id =" + user_id + " &&  attn_date =" + date;
               sequelize.query(query).then(result => {
                    console.log("result", result);
                    let response = JSON.parse(JSON.stringify(result[0]));
                    resolve(response[0].user_count);
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

function checkVehicleAttendance(vehicle_id, attn_date) {
     return new Promise((resolve, reject) => {
          try {
               let query = " SELECT COUNT(vehicle_id) AS count FROM vehicle_attendance WHERE vehicle_id =" + vehicle_id + " AND attn_date = " + attn_date;
               sequelize.query(query).then(count => {
                    let vehicle_Count = JSON.parse(JSON.stringify(count[0]));
                    console.log("vehicle_Count", vehicle_Count);
                    if (vehicle_Count > 0) {
                         resolve(false)
                    } else {
                         resolve(true);
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

function checkTemporaryVehicleData(data) {
     return new Promise((resolve, reject) => {
          try {
               let replace = data.vehicle_reg_no.replace(' ', '');
               let upperCase = replace.toUpperCase();
               console.log("uppercase , date", upperCase, moment().format("YYYY-MM-DD"));
               let query = "SELECT COUNT(vehicle_id) AS count FROM vehicle WHERE  hub_id=" + data.hub_id + "  && UPPER(REPLACE( reg_no, ' ', '')) = '" + upperCase + "'  AND DATE(created_at) =" + moment().format("YYYY-MM-DD");
               sequelize.query(query).then(result => {
                    let response = JSON.parse(JSON.stringify(result[0]));
                    let count = typeof response[0].count != 'undefined' ? response[0].count : -1;
                    if (parseInt(count) > 0) {
                         resolve(false);
                    } else {
                         resolve(true);
                    }
               }).catch(err => {
                    console.log(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

function checkContractVehicleData(data) {
     return new Promise((resolve, reject) => {
          try {
               let replace = data.vehicle_reg_no.replace(' ', '');
               let upperCase = replace.toUpperCase();
               let query = " select count(vehicle_id) as count from vehicle where   UPPER(REPLACE( reg_no, ' ', '')) = '" + upperCase + "' &&  vehicle_type = 156001";
               sequelize.query(query).then(result => {
                    let response = JSON.parse(JSON.stringify(result[0]));
                    let count = typeof response[0].count != 'undefined' ? response[0].count : -1;
                    if (parseInt(count) > 0) {
                         resolve(false);
                    } else {
                         resolve(true);
                    }
               }).catch(err => {
                    console.log(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

function getMasterLookupValue(value) {
     // To get Temp Vehicle Legal Id and Provider Id
     return new Promise((resolve, reject) => {
          let query = "SELECT value, description FROM master_lookup WHERE value IN(" + value + ")";
          sequelize.query(query).then(result => {
               let response = JSON.parse(JSON.stringify(result[0]));
               if (response[0].value == value) {
                    resolve(response[0].description);
               } else {
                    resolve('');
               }
          }).catch(err => {
               console.log(err);
          })
     })
}

module.exports.saveAttendances = function (data) {
     return new Promise((resolve, reject) => {
          try {
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let attendanceDataArray = [];
               if (data.attendance_data != '') {
                    attendanceDataArray.push(data.attendance_data);
                    attendanceDataArray.forEach((value) => {
                         checkAttendance(value.user_id, data.date).then(response => {
                              if (response != 0) {
                                   let query = " insert into attendance (user_id , source , is_present , attn_date , created_by , created_at ,updated_at ) values (" + value.user_id + ",'" + 145001 + "'," + value.is_present + ",'" + data.date + "'," + data.user_id + ",'" + formatted_date + "','" + formatted_date + "' )"
                                   sequelize.query(query).then(inserted => {
                                        console.log("inserted successfully")
                                        resolve(true);
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              } else {
                                   let query = "update attendance set is_present = " + value.is_present + " , updated_at = '" + formatted_date + "'  where user_id =" + value.user_id + " &&  attn_date =" + data.date
                                   sequelize.query(query).then(updated => {
                                        console.log("updated")
                                        resolve(true);
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              }
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    })
               }

          }
          catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

module.exports.getVehicleIdsByUserIdModel = function (user_id, vehicle_type = 156001) {
     let current_datetime = new Date();
     let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
     return new Promise((resolve, reject) => {
          try {
               let query = "select  le.business_legal_name AS vehicleName, vehicle.reg_no AS vehicleno, vehicle.vehicle_id AS vehicle_id, IFNULL(ve.is_present, 0) AS is_present , IFNULL(ve.reporting_time, ' ')  AS reporting_time  FROM legal_entities AS le LEFT JOIN vehicle ON le.legal_entity_id = vehicle.legal_entity_id LEFT JOIN legalentity_warehouses AS lw ON lw.le_wh_id = vehicle.hub_id  LEFT JOIN users ON users.legal_entity_id = lw.legal_entity_id LEFT JOIN ( SELECT va.is_present, va.reporting_time, va.vehicle_id FROM vehicle_attendance AS va  WHERE va.attn_date = " + formatted_date + ") AS ve ON ve.vehicle_id = vehicle.vehicle_id  WHERE users.user_id = " + user_id + " AND vehicle.is_active = 1 AND vehicle.vehicle_type = " + vehicle_type + " AND vehicle.vehicle_id NOT IN(SELECT repl.`replace_with` FROM vehicle AS repl  JOIN vehicle_attendance AS rva ON repl.`vehicle_id` = rva.`vehicle_id`  WHERE repl.replace_with = vehicle.vehicle_id AND rva.attn_date = " + formatted_date + ")"
               sequelize.query(query).then(response => {
                    resolve(response[0])
               }).catch(err => {
                    console.log(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}

module.exports.saveVehicleAttendances = function (data) {
     return new Promise((resolve, reject) => {
          try {
               if (typeof data == 'undefined' || data == '') {
                    resolve(false);
               }

               let userId = data.user_id;
               let date = data.date;
               let attendanceDataArray = [];
               if (typeof data.attendance_data != 'undefined') {
                    attendanceDataArray.push(data.attendance_data);
                    attendanceDataArray.forEach((record) => {
                         let vehicle_id = record.vehicle_id;
                         let vehicle_reg_no = record.vehicle_reg_no;
                         let is_present = record.is_present;
                         let reporting_time = record.reporting_time;
                         let manualAttendance = 145001;// master lookup id for manual attendance
                         //checking vahicle attendance 
                         checkVehicleAttendance(vehicle_id, date).then(count => {
                              if (count) {//new vehicle not existing  in ebutor system
                                   //inserted query 
                                   let query = "insert into vehicle_attendance  (attn_date , vehicle_id , vehicle_reg_no , is_present , reporting_time , source , created_by) values ('" + date + "'," + vehicle_id + ",'" + vehicle_reg_no + "'," + is_present + ",'" + reporting_time + "','" + manualAttendance + "'," + userId + ")";
                                   sequelize.query(query).then(insert => {
                                        console.log("inserted Sucesss");
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })

                              } else {//existing vehicle
                                   // Update Laravel Query      
                                   let query = "update vehicle_attendance set is_present =" + is_present + " , reporting_time = '" + reporting_time + "' , updated_at ='" + moment().format("YYYY-MM-DDTHH:mm:ss") + "' where vehicle_id =" + vehicle_id + "  && attn_date =" + date;
                                   sequelize.query(query).then(updated => {
                                        console.log("Updated successfully");
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                              }
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    })
                    return resolve(true);
               } else {
                    let error = { "status": 'failed', 'messaeg ': 'Please pass attendance details' }
                    resolve(error);
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

module.exports.saveTemporaryVehicleData = function (data) {
     return new Promise((resolve, reject) => {
          try {
               let tempVehicleLegalId = 78012;
               let tempVehicleProvider = 78013;
               let IS_ACTIVE = 1;
               //checking temporary vehicle data
               checkTemporaryVehicleData(data).then(tempVehicleData => {
                    if (!tempVehicleData) {
                         resolve({ 'status': 'failed', 'message': 'Data is already Inserted' })
                    }
               }).catch(err => {
                    console.log(err);
               })
               //checking contract vehicle data
               checkContractVehicleData(data).then(contractVehicleData => {
                    if (!contractVehicleData) {
                         resolve({ 'status': 'failed', 'message': 'Vehicle already registered as contract base' })
                    }
               }).catch(err => {
                    console.log(err);
               })

               getMasterLookupValue(tempVehicleLegalId).then(vehicle_le_id => {
                    let vehicleLegalId = vehicle_le_id;
                    getMasterLookupValue(tempVehicleProvider).then(vehicle_provider => {
                         let vehicleProvider = vehicle_provider;
                         let date = moment().format("YYYY-MM-DDTHH:mm:ss");
                         let query = " insert into vehicle ( legal_entity_id , hub_id , reg_no , veh_provider , replace_With , vehicle_type ,is_active , created_by , approved_at , created_at , vehicle_model ,driver_le_id )  values (" + vehicleLegalId + "," + data.hub_id + ",'" + data.vehicle_reg_no + "','" + vehicleProvider + "','" + data.replace_with + "','" + data.vehicle_type + "'," + IS_ACTIVE + "," + data.user_id + ",'" + date + "','" + date + "' ," + 0 + ',' + 0 + ")";
                         sequelize.query(query).then(inserted => {
                              let vehicleId = inserted[0].insertId;
                              let attendanceArray = { 'vehicle_id': vehicleId, 'vehicle_reg_no': data.vehicle_reg_no, 'is_present': 1, 'reporting_time': moment().format('HH:mm:ss') };
                              console.log("attemdanceArray", attendanceArray);
                              let insertArray = { 'user_id': data.user_id, 'date': moment().format('YYYY-MM-DD'), 'attendance_data': attendanceArray };
                              this.saveVehicleAttendances(insertArray).then(response => {
                                   if (response) {
                                        resolve({ 'status': "success", 'message': "Temporary Vehicle has been Inserted" })
                                   }
                              }).catch(err => {
                                   console.log(err);
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
