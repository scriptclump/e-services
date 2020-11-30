
const dbconnection = require('../../dbConnection');
const db = dbconnection.DB;
const con = dbconnection.Conn;
var nodemailer = require("nodemailer");
const config = require("../../config/config")
var Mailgen = require('mailgen');
const mongoose = require('mongoose');
const user = mongoose.model('User');
const userTemp = mongoose.model('userTemp');


/*
Purpose : getIconData function is  used to get all icon details
author : Deepak Tiwari
Request : Require icon code 
Resposne : Return all icon details 
*/
exports.getIconData = function (code1, code2, code3) {
     let result = [];
     return new Promise((resolve, reject) => {
          let data = "SELECT id,label,url,icon_code FROM  icons_list WHERE  icon_type  IN(" + code1 + ")";
          db.query(data, {}, function (err, rows) {
               if (err) {
                    return reject(err);
               }
               else if (Object.keys(rows).length > 0) {
                    let data = "SELECT id,label,url,icon_code FROM  icons_list WHERE  icon_type  IN(" + code2 + ")";
                    db.query(data, {}, function (err, row) {
                         if (err) {
                              return reject(err);
                         }
                         else if (Object.keys(row).length > 0) {
                              console.log(JSON.stringify(row))
                              result = { code: rows, code2: row }
                              let data = "SELECT id,label,url,icon_code FROM  icons_list WHERE  icon_type  IN(" + code3 + ")";
                              db.query(data, {}, function (err, res) {
                                   if (err) {
                                        return reject(err);
                                   }
                                   else if (Object.keys(res).length > 0) {
                                        let data = "SELECT id,label,url,icon_code FROM  icons_list WHERE  icon_type  IN(" + 170005 + ")";
                                        db.query(data, {}, function (err, code) {
                                             if (err) {
                                                  return reject(err);
                                             }
                                             else if (Object.keys(code).length > 0) {
                                                  result = [{
                                                       "display_title": "Select the fridge type",
                                                       "key": "fridge",
                                                       "items": JSON.parse(JSON.stringify(rows))
                                                  }, {
                                                       "display_title": "Select what you sell below",
                                                       "key": "alsoselling",
                                                       "items": JSON.parse(JSON.stringify(row))
                                                  }, {
                                                       "display_title": "Do you have a Swipe Machine?",
                                                       "key": "other",
                                                       "items": JSON.parse(JSON.stringify(code))
                                                  }
                                                  ]
                                             }
                                             else {
                                                  return reject("No mapping found..")
                                             }
                                             resolve(result);
                                        });
                                   }
                                   else {
                                        return reject("No mapping found..")
                                   }
                              });
                         }
                         else {
                              return reject("No mapping found..")
                         }

                    });
               }
               else {
                    return reject("No mapping found..")
               }


          });

     });

}



/*
Purpose : getCountries function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Not require  
Resposne : Return all country details 
*/
exports.getCountries = function (req, res) {
     return new Promise((resolve, reject) => {
          let data = "select country_id , name from  countries";
          db.query(data, {}, function (err, rows) {
               if (err) {
                    return reject(err);
               }
               if (Object.keys(rows).length > 0) {
                    return resolve(rows);
               }
               else {
                    return reject("No mapping found..")
               }

          });
          // db.release()
     });

}


/*
Purpose : getStates function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require country id 
Resposne : Return all state details wiht that country 
*/
exports.getStates = function (country_id) {
     return new Promise((resolve, reject) => {
          let data = "select c.zone_id AS state_id , c.name AS state_name FROM zone AS c  WHERE c.country_id =" + country_id + "&&  c.zone_id != " + 4035 + "&& STATUS =" + 1 + " ORDER BY  c.zone_id ASC";
          db.query(data, {}, function (err, rows) {
               if (err) {
                    return reject(err);
               }
               if (Object.keys(rows).length > 0) {
                    return resolve(rows);
               }
               else {
                    return reject("No mapping found..")
               }
               // db.release()
          });

     });

}


/*
Purpose : checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Give access to the user to the application
*/
exports.checkCustomerToken = function (customer_token) {
     // return new Promise((resolve, reject) => {
     //     let string = JSON.stringify(customer_token);
     //   let count = 0;
     // user.countDocuments({ password_token: customer_token }, function (err, response) {
     //    if (err) {
     //       console.log(err);
     //     reject(err);
     //} else if (response > 0) {
     //   resolve(response)
     ///   } else {
     //     resolve(count)
     //     }
     // })
     //  });

     return new Promise((resolve, reject) => {
          let string = JSON.stringify(customer_token)
          let data = "select count(user_id) as counts FROM users WHERE password_token =" + string;
          db.query(data, {}, function (err, rows) {
               if (err) {
                    return reject(err);
               }
               if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].counts);
               }
               else {
                    return reject("No mapping found..")
               }
               // db.release()
          });
     });
}


/*
Purpose : getFfBeat used to get based on beat based on ff_id.
author : Deepak Tiwari
Request : Require ff_id,hub
Resposne : Returns All best under ff 
*/
exports.getFfBeat = function (ff_id, hub) {
     try {
          return new Promise((resolve, reject) => {
               let hub_array = [];
               let beat_array = [];
               let data = "select pjp_name ,pjp_pincode_area_id,pdp,pdp_slot,rm_id   from  pjp_pincode_area join legalentity_warehouses as lew   ON pjp_pincode_area.le_wh_id = lew.le_wh_id where lew.status = 1 ";
               db.query(data, {}, function (err, rows) {
                    if (err) {
                         console.log(err)
                    }
                    else if (Object.keys(rows).length > 0) {
                         if (hub == null) {
                              rows.forEach((element) => {
                                   if (element.rm_id == ff_id || element.pjp_pincode_area_id == 0) {
                                        beat_array.push(element);
                                   }
                              })
                              return resolve(beat_array)
                         } else {
                              hub_array = hub
                              let data = "select * from spokes join pjp_pincode_area where pjp_pincode_area.spoke_id = spokes.spoke_id and  spokes.le_wh_id IN (" + hub + ")";
                              db.query(data, {}, function (err, rows) {
                                   if (err) {
                                        //return reject(err);
                                   } else if (Object.keys(rows).length > 0) {
                                        string = JSON.stringify(rows)
                                        beat_array = JSON.parse(string)
                                        return resolve(beat_array);
                                   }

                              })

                         }
                    }
                    // db.release()
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
                         //db.release()
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
                              //db.release()
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
                                                                 return reject(err);
                                                            }
                                                            else if (Object.keys(rows).length > 0) {
                                                                 userList.push(rows[0]);
                                                                 response = userList;
                                                                 return resolve(response);
                                                            }
                                                            // db.release()
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
                                        return reject(err);
                                   }
                                   else if (Object.keys(rows).length > 0) {
                                        response = rows[0]
                                        return resolve(response);
                                   }
                                   //db.release()
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
exports.getTeamByUser = function (userId) {
     let response = []
     try {
          return new Promise((resolve, reject) => {
               response = userId;
               if (userId > 0) {
                    getTeamList(userId).then((childUserList) => {
                         if (childUserList != null) {
                              response = childUserList
                              return resolve(response);
                         } else {
                              return resolve(response);
                         }

                    }).catch((err) => {
                         reject(err)
                    })
               } else {
                    return resolve(response);
               }
               return resolve(response);
          })
     } catch (err) {
          reject(err)
     }
}


/*
Purpose : getCustomerData function is used to get  the customer data of the customer_token passed 
author : Deepak Tiwari
Request : Require customer_token
Resposne : Returns user basic info .
*/
exports.getCustomerData = function (customer_token) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(customer_token)
               let result = []
               let data = "select legal_entity_id , mobile_no  FROM users WHERE password_token =" + string;
               db.query(data, {}, function (err, rows) {
                    if (err) {
                         return reject(err);
                    }
                    else if (Object.keys(rows).length > 0) {
                         let response = rows[0];
                         let legal_entity_id = response.legal_entity_id;
                         let mobile_no = response.mobile_no;
                         let master_query = "select * from master_lookup where value = 78002";
                         db.query(master_query, {}, function (err, rows) {
                              if (err) {
                                   return reject(err);
                              }
                              else if (Object.keys(rows).length > 0) {
                                   let desc = rows[0].description;
                                   let is_primary;
                                   //let details = "CALL get_user_details( " + mobile_no + ',' + desc + ")";
                                   let details = "SELECT u.firstname AS firstname, u.lastname,u.email_id as email , u.profile_picture AS documents, le.business_legal_name AS company, le.legal_entity_id AS address_id, le.address1 AS address_1, le.address2 AS address_2, IFNULL(le.locality, '') AS locality, IFNULL(le.landmark, '') AS landmark, le.city, coun.name, le.pincode AS postcode, u.mobile_no AS telephone, u.email_id AS email, le.business_type_id AS business_type, c.No_of_shutters, cp.city_id AS area_id, IFNULL(cp.officename, '') AS AREA, z.name AS state, c.volume_class, up.preference_value AS delivery_time, up.preference_value1 AS pref_value1, c.master_manf AS manufacturers, c.smartphone, c.dist_not_serv, c.is_icecream, c.facilities, up.sms_subscription AS sms_notification, c.is_visicooler, c.is_milk, c.is_deepfreezer,c.is_swipe , c.is_fridge, c.is_vegetables, up.business_start_time, up.business_end_time, c.network AS internet_availability, le.legal_entity_type_id AS buyer_type, c.beat_id, u.is_parent, le.gstin, le.arn_number, IFNULL(p.pdp, '') AS pdp, IFNULL(p.pdp_slot, '') AS pdp_slot, CASE  WHEN le.legal_entity_type_id = 3013  THEN 1 ELSE 0 END AS is_premium   FROM users AS u LEFT JOIN legal_entities AS le ON le.legal_entity_id = u.legal_entity_id LEFT JOIN customers AS c ON c.le_id = le.legal_entity_id  LEFT JOIN  cities_pincodes AS cp  ON cp.city_id = c.area_id  LEFT JOIN countries AS coun ON coun.country_id = le.country  LEFT JOIN  zone AS z ON z.zone_id = le.state_id  LEFT JOIN user_preferences AS up ON up.user_id = u.user_id LEFT JOIN  pjp_pincode_area AS p ON  p.pjp_pincode_area_id = c.beat_id WHERE u.mobile_no = " + mobile_no + " AND  z.country_id = 99 AND le.legal_entity_type_id IN(SELECT VALUE AS QUERY FROM master_lookup AS ml WHERE ml.mas_cat_id =" + desc + " && ml.is_active = 1) ";
                                   db.query(details, {}, function (err, rows) {
                                        if (err) {
                                             return reject(err);
                                        }
                                        else if (Object.keys(rows).length > 0) {
                                             let string1 = JSON.stringify(rows[0])
                                             let json1 = JSON.parse(string1)
                                             result = rows[0]
                                             let DATA = "select count(lew.le_wh_id) as count from legalentity_warehouses as lew LEFT JOIN users as u ON u.mobile_no = lew.phone_no && u.legal_entity_id = lew.legal_entity_id  where u.password_token =" + string;
                                             db.query(DATA, {}, function (err, rows) {
                                                  if (err) {
                                                       console.log(err);
                                                  } else if (Object.keys(rows).length > 0) {
                                                       let data = [];
                                                       is_primary = rows[0].count;
                                                       if (result != null) {
                                                            var fridges = { 'is_deepfreezer': result.is_deepfreezer, 'is_fridge': result.is_fridge, 'is_visicooler': result.is_visicooler };
                                                            var alsoselling = { 'is_icecream': result.is_icecream, 'is_milk': result.is_milk, 'is_vegetables': result.is_vegetables };
                                                            var notification = { 'sms_notification': result.sms_notification };
                                                            data.push(fridges);
                                                            data.push(alsoselling);
                                                            data.push(notification);
                                                            result.retailer_details = data;
                                                       }
                                                       if (is_primary > 0 || result.is_parent == 1) {
                                                            let data1 = "select u.mobile_no, u.firstname, u.user_id from users as u where u.legal_entity_id  =" + legal_entity_id + " && u.mobile_no !=" + mobile_no + " && u.is_active = 1 && u.is_disabled = 0 limit 2 ";
                                                            db.query(data1, {}, function (err, rows) {
                                                                 if (err) {
                                                                      console.log(err);
                                                                 } else if (Object.keys(rows).length > 0) {
                                                                      let string = JSON.stringify(rows);
                                                                      let user = JSON.parse(string);
                                                                      if (user != null) {
                                                                           let i = 1;
                                                                           let contact = [];
                                                                           user.forEach(function (users) {
                                                                                let contact_name = users.firstname;
                                                                                let contact_no = users.mobile_no;
                                                                                let user_id = users.user_id;
                                                                                contact.push(contact_name.i);
                                                                                contact.push(contact_no.i);
                                                                                contact.push(user_id.i);
                                                                                i++;
                                                                           })
                                                                           result = result.push(contact);
                                                                      } else {
                                                                           contact['contact_no1'] = '';
                                                                           contact['contact_name1'] = '';
                                                                           contact['contact_no2'] = '';
                                                                           contact['contact_name2'] = '';
                                                                           contact['user_id1'] = '';
                                                                           contact['user_id2'] = '';
                                                                           result = result.push(contact);
                                                                      }
                                                                      let parentData = [];
                                                                      let parent_data = "select count(lew.phone_no) as count from users as u LEFT JOIN  legalentity_warehouses as lew ON   lew.phone_no = u.mobile_no where u.password_token =" + string;
                                                                      db.query(parent_data, {}, function (err, rows) {
                                                                           if (err) {
                                                                                console.log(err)
                                                                           } else if (Object.keys(rows).length > 0) {
                                                                                if (rows[0].count > 0) {
                                                                                     parentData['is_parent'] = 1;
                                                                                } else {
                                                                                     parentData['is_parent'] = result.is_parent

                                                                                }
                                                                           }
                                                                      })
                                                                      result = result.concat(parentData);
                                                                 }
                                                            })


                                                       }
                                                  }
                                                  if (result != null) {
                                                       let email = result.email;
                                                       let pos = email.indexOf('@nomail');
                                                       if (pos == -1) {
                                                            return resolve(result);
                                                       } else {
                                                            result.email = '';
                                                            return resolve(result);
                                                       }

                                                  } else {
                                                       return;
                                                  }

                                             })


                                        }
                                   })
                              }

                         })

                    }
                    else {
                         return reject("No mapping found..")
                    }
               });

          });

     } catch (err) {
          console.log(err)
     }

}


/*
Purpose : getPincodeAreas function is used to get all the areas based on pincode
author : Deepak Tiwari
Request : Require pincode
Resposne : Returns area code .
*/
exports.getPincodeAreas = function (pincode) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(pincode)
               let data = "select cp.officename from cities_pincodes as cp where cp.pincode=" + string;
               db.query(data, {}, function (err, rows) {
                    if (err) {
                         return reject(err);
                    }
                    if (Object.keys(rows).length > 0) {
                         return resolve(rows);
                    }
                    else {
                         return reject("No mapping found..")
                    }
                    db.release()
               });
          });
     } catch (err) {

     }
}


/*
Purpose : getPincodeData used to get areas and pincode based on pincode.
author : Deepak Tiwari
Request : Require pincode
Resposne : Returns areas and pincode .
*/
exports.getPincodeData = function (pincode) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(pincode)
               let DATA = [];
               let data = "select distinct state from cities_pincodes as cp where cp.pincode=" + string;
               db.query(data, {}, function (err, rows) {
                    if (err) {
                         return reject(err);
                    }
                    if (Object.keys(rows).length > 0) {
                         let string = JSON.stringify(rows[0].state)
                         let data = "select zone_id , name from zone where name  like" + string;
                         db.query(data, {}, function (err, row) {
                              if (err) {
                                   console.log(err);
                                   return reject(err);
                              } else if (Object.keys(row).length > 0) {
                                   let state_id = row[0].zone_id;
                                   let state_name = rows[0].state;
                                   DATA.push(state_id);
                                   DATA.push(state_name);
                                   return resolve(DATA)
                              }

                         })
                         return resolve(rows);
                    }
                    else {

                    }
                    // db.release();
               });

          });

     } catch (err) {
          console.log(err);

     }

}


/*
Purpose : customerLegalid used to get legal entity id based on customer_token.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Returns legal_entity_id .
*/
exports.customerLegalid = function (customer_token) {
     return new Promise((resolve, reject) => {
          let string = JSON.stringify(customer_token)
          let data = "SELECT legal_entity_id FROM  users WHERE  password_token =" + string;
          db.query(data, {}, function (err, rows) {
               if (err) {
                    return reject(err);
               }
               else if (Object.keys(rows).length > 0) {
                    return resolve(rows[0].legal_entity_id);
               }
               else {
                    return reject("No mapping found..")
               }
          });
     });

}


/*
Purpose : getfirstname used to get user firstname based on customer_token.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Returns user firstname .
*/
exports.getFirstname = function (customer_token) {
     return new Promise((resolve, reject) => {
          let string = JSON.stringify(customer_token)
          let data = "SELECT firstname  , lastname FROM  users  WHERE  password_token =" + string;
          db.query(data, {}, function (err, rows) {
               if (err) {
                    return reject(err);
               }
               else if (Object.keys(rows).length > 0) {
                    let basic = [];
                    let firstname = rows[0].firstname;
                    let lastname = rows[0].lastname;
                    basic.push(firstname);
                    basic.push(lastname);
                    return resolve(basic);
               }
               else {
                    return reject("No mapping found..")
               }
          });
     });
}


/*
Purpose : getlastname used to get user lastname based on customer_token.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Returns user lastname .
*/
exports.getLastname = function (customer_token) {
     return new Promise((resolve, reject) => {
          let string = JSON.stringify(customer_token)
          let data = "SELECT lastname FROM  users  WHERE  password_token =" + string;
          db.query(data, {}, function (err, rows) {
               if (err) {
                    return reject(err);
               }
               else if (Object.keys(rows).length > 0) {
                    return resolve(rows);
               }
               else {
                    return reject("No mapping found..")
               }
          });
     });
}

/*
Purpose : updateCustomerTable used to get user lastname based on customer_token.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Returns user lastname .
*/
module.exports.updateCustomerTable = function (internet_availability, manufacturers, No_of_shutters, area, volume_class, delivery_time, pref_value1, business_start_time, business_end_time, postcode, city, smartphone, customer_token, state, beat, is_icecream = 0, sms_notification = 0, is_milk = 0, is_fridge = 0, is_vegetables = 0, is_visicooler = 0, dist_not_serv = '', facilities = 0, is_deepfreezer = 0, is_swipe = 0) {
     try {
          let legal_entity_id;
          let string = JSON.stringify(customer_token);
          let data = "select user_id , legal_entity_id  from users where password_token =" + string;
          db.query(data, {}, function (err, Id) {
               if (err) {

               } else if (Object.keys(Id).length > 0) {
                    let user_id = Id[0].user_id;
                    legal_entity_id = Id[0].legal_entity_id;
                    let current_datetime = new Date();
                    let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                    let Query = "update user_preferences set preference_value ='" + delivery_time + "', preference_value1='" + pref_value1 + "', business_start_time='" + business_start_time + "', business_end_time='" + business_end_time + "', sms_subscription =" + sms_notification + ", updated_at ='" + formatted_date + "'where user_id =" + user_id;
                    db.query(Query, {}, function (err, updated) {
                         if (err) {
                              console.log(err);
                         } else {
                         }
                    })
                    let Query_1 = "select cp.city_id from cities_pincodes as cp where cp.pincode=" + postcode + " && cp.officename LIKE '%" + area + "%'";
                    db.query(Query_1, {}, function (err, area_chk) {
                         if (err) {
                              console.log(err);
                         } else if (Object.keys(area_chk).length > 0) {
                              let current_datetime = new Date();
                              let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                              let update_query = "update customers set network ='" + internet_availability + "' ,No_of_shutters ='" + No_of_shutters + "',volume_class ='" + volume_class + "',master_manf ='" + manufacturers + "',smartphone ='" + smartphone + "',beat_id ='" + beat + "',area_id='" + area_chk[0].city_id + "',is_icecream ='" + is_icecream +
                                   "',is_milk='" + is_milk + "',is_fridge='" + is_fridge + "',is_vegetables ='" + is_vegetables + "',is_visicooler='" + is_visicooler + "',dist_not_serv ='" + dist_not_serv + "',facilities ='" + facilities + "',is_deepfreezer ='" + is_deepfreezer + "',updated_at ='" + formatted_date + "' , is_swipe ='" + is_swipe + "'where  le_id= " + legal_entity_id;
                              db.query(update_query, {}, function (err, updated) {
                                   if (err) {
                                        console.log(err);
                                   } else {
                                   }
                              })

                         } else {
                              let state_query = "select name from zone where zone_id=" + state;
                              let last_insert_city_id;
                              db.query(state_query, {}, function (err, state_name) {
                                   if (err) {
                                        console.log(err);
                                   } else if (Object.keys(state_name).length > 0) {
                                        let statename = state_name[0].name;
                                        let string_city = JSON.stringify(city);
                                        let string_state = JSON.stringify(statename);
                                        let string_area = JSON.stringify(area)
                                        let city_query = "insert into  cities_pincodes(country_id , pincode ,city ,state , officename) values (" + '99' + ',' + postcode + ',' + string_city + ',' + string_state + ',' + string_area + ")";
                                        db.query(city_query, {}, function (err, inserted) {

                                             if (err) {
                                                  console.log(err);
                                             } else {
                                                  last_insert_city_id = inserted.insertId;
                                             }
                                        })
                                   }
                              })
                              let current_datetime = new Date();
                              let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
                              let update_query = "update customers set network =" + internet_availability + " ,No_of_shutters =" + No_of_shutters + ",volume_class ='" + volume_class + "',master_manf ='" + manufacturers + "',smartphone ='" + smartphone + "',beat_id ='" + beat + "',area_id='" + last_insert_city_id + "',is_icecream =" + is_icecream +
                                   ",is_milk=" + is_milk + ",is_fridge=" + is_fridge + ",is_vegetables =" + is_vegetables + ",is_visicooler=" + is_visicooler + ",dist_not_serv ='" + dist_not_serv + "',facilities =" + facilities + ",is_deepfreezer =" + is_deepfreezer + "updated_at ='" + formatted_date + "' , is_swipe ='" + is_swipe + "'where  le_id= " + legal_entity_id;
                              db.query(update_query, {}, function (err, updated) {
                                   if (err) {
                                        console.log(err);
                                   } else {
                                   }
                              })

                         }
                    })
               }

          })
          let retailer = updateFlatTable(legal_entity_id);
     } catch (err) {
          console.log(err);

     }



}

/*
Purpose :getParentIdFromLegalEntity function used to get ParentId From LegalEntity table  .
author : Deepak Tiwari
Request : Require legalEntityId.
Resposne : return ParentId .
*/
function getParentIdFromLegalEntity(legal_entity_id) {
     let data = " select parent_le_id from legal_entities where legal_entity_id =" + legal_entity_id;
     db.query(data, {}, function (err, rows) {
          if (err) {
               console.log(err);
          } else if (Object.keys(rows).length > 0) {
               if (typeof rows[0].parent_le_id != 'undefined' && rows[0].parent_le_id != null) {
                    return rows[0].parent_le_id;

               } else {
                    return '';
               }

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
          if (legalEntityId > 0) {
               let string = JSON.stringify(legalEntityId);
               //  pool.getConnection(function (err, con) {
               con.beginTransaction(function (err) {
                    if (err) { console.log(err) }
                    let data = "select le_code from retailer_flat where legal_entity_id =" + string;
                    db.query(data, {}, function (err, result) {
                         if (err) {
                              con.rollback(function () {
                                   console.log(err);
                              });
                         } else if (Object.keys(result).length > 0) {
                              let legalEntityDetails = result[0];
                              let Query = "select aadhar_id , mobile_no from users where legal_entity_id=" + string;
                              db.query(Query, {}, function (err, aadhar) {
                                   if (err) {
                                        con.rollback(function () {
                                             console.log(err);
                                        });
                                   } else if (Object.keys(aadhar).length > 0) {
                                        aadhar_id = aadhar[0];
                                        let Query1 = 'Call getLegalEntitiesDataById(0,0,' + legalEntityId + ')';

                                        db.query(Query1, {}, function (err, row) {
                                             if (err) {
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
                                                            let parent_le_id = getParentIdFromLegalEntity(string[0].legal_entity_id);
                                                            response.parent_le_id = parent_le_id;
                                                       }
                                                  }
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
                                                                 db.query(data_1, {}, function (err, pincode) {
                                                                      if (err) {
                                                                           con.rollback(function () {
                                                                                console.log(err);
                                                                           });
                                                                      } else if (Object.keys(pincode).length > 0) {
                                                                           hubId = pincode.hasOwnProperty('le_wh_id') ? hubDetails.le_wh_id : 0;
                                                                           spokeId = pincode.hasOwnProperty('spoke_id') ? hubDetails.spoke_id : 0;
                                                                           if (hubId > 0 && spokeId > 0) {
                                                                                string[0].hub_id = hubId;
                                                                                string[0].spoke_id = spokeId;
                                                                                updateDetails.hub_id = hubId;
                                                                                updateDetails.spoke_id = spokeId;
                                                                                let customer = "update customers set updateDetails where le_id =" + legalEntityId;
                                                                                db.query(customer, {}, function (err, customers) {
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

                                                            }
                                                       }
                                                       delete string[0].sms_notification;
                                                       let current_datetime = string[0].last_order_date_old;
                                                       let curretDate = string[0].created_at;
                                                       let currDate = string[0].updated_at;

                                                       let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();

                                                       let formatted = curretDate.getFullYear() + "-" + (curretDate.getMonth() + 1) + "-" + curretDate.getDate() + " " + curretDate.getHours() + ":" + curretDate.getMinutes() + ":" + curretDate.getSeconds();

                                                       let formattedDate = currDate.getFullYear() + "-" + (currDate.getMonth() + 1) + "-" + currDate.getDate() + " " + currDate.getHours() + ":" + currDate.getMinutes() + ":" + currDate.getSeconds();


                                                       let retailer = " UPDATE retailer_flat SET parent_le_id ='" + string[0].parent_le_id + "' , business_legal_name = '" + string[0].business_legal_name + "',legal_entity_type_id='" + string[0].legal_entity_type_id + "',business_type_id ='" + string[0].business_type_id + "' ,name ='" + string[0].name + "', mobile_no = '" + string[0].mobile_no + " ', volume_class_id = '" + string[0].volume_class_id + "', volume_class = '" + string[0].volume_class + "', No_of_shutters = '" + string[0].No_of_shutters + "', suppliers = '" + string[0].suppliers + "', business_start_time = '" + string[0].business_start_time + "', business_end_time = '" + string[0].business_end_time + "', address = '" + string[0].address + "', address1 = '" + string[0].address1 + "', address2 = '" + string[0].address2 + "', area_id = '" + string[0].area_id + "',AREA = '" + string[0].area + "',hub_id = '" + string[0].hub_id + "', beat_id = '" + string[0].beat_id + "', spoke_id = '" + string[0].spoke_id + "', beat = '" + string[0].beat + "', city = '" + string[0].city + "', state_id = '" + string[0].state_id + "', state = '" + string[0].state + "', country = '" + string[0].country + "', locality = '" + string[0].locality + "', landmark = '" + string[0].lankmark + "', pincode = '" + string[0].pincode + "' , smartphone = '" + string[0].smartphone + "', network = '" + string[0].network + "', master_manf = '" + string[0].master_manf + "', orders_old = '" + string[0].orders_old + "', last_order_date_old = '" + formatted_date + "', beat_rm_name = '" + string[0].beat_rm_name + "',created_by = '" + string[0].created_by + "', created_at = '" + formatted + "', created_time = '" + string[0].created_time + "', updated_by = '" + string[0].updated_by + "', updated_at = '" + formattedDate + "', updated_time = '" + string[0].updated_time + "', latitude = '" + string[0].latitude + "', longitude = '" + string[0].longitude + "',is_icecream = ' " + string[0].is_icecream + "', is_milk = '" + string[0].is_milk + "', is_deepfreezer = '" + string[0].is_deepfreezer + "', is_fridge = '" + string[0].is_fridge + "', is_vegetables = '" + string[0].is_vegetables + "', is_visicooler = '" + string[0].is_visicooler + "',dist_not_serv = '" + string[0].dist_not_serv + "', facilities = " + string[0].facilities + " WHERE legal_entity_id =" + legalEntityId;
                                                       db.query(retailer, {}, function (err, retailers) {
                                                            if (err) {
                                                                 con.rollback(function () {
                                                                      console.log(err);
                                                                 });
                                                            } else if (Object.keys(retailers).length > 0) {

                                                            }
                                                       })
                                                  } else {
                                                       if (typeof string[0].hub_id != 'undefined' && string[0].hub_id == 0) {
                                                            beatId = typeof string[0].beat_id != 'undefined' ? string[0].beat_id : 0;
                                                            if (beatId > 0) {
                                                                 let Querys = "select pjp_pincode_area.spoke_id ,pjp_pincode_area.le_wh_id from pjp_pincode_area  LEFT JOIN  spokes ON spokes.spoke_id = pjp_pincode_area.spoke_id where pjp_pincode_area_id=" + beatId;
                                                                 db.query(Querys, {}, function (err, hubDetails) {
                                                                      if (err) {
                                                                           con.rollback(function () {
                                                                                console.log(err);
                                                                           });
                                                                      } else if (Object.keys(hubDetails[0]).length > 0) {
                                                                           hubId = pincode.hasOwnProperty('le_wh_id') ? hubDetails[0].le_wh_id : 0;
                                                                           spokeId = pincode.hasOwnProperty('spoke_id') ? hubDetails[0].spoke_id : 0;
                                                                           if (hubId > 0 && spokeId > 0) {
                                                                                string[0].hub_id = hubId;
                                                                                string[0].spoke_id = spokeId;
                                                                                updateDetails.hub_id = hubId;
                                                                                updateDetails.spoke_id = spokeId;
                                                                                let customer = "update customers set hub_id ='" + hubId + "', spoke_id = '" + spokeId + "' where le_id =" + legalEntityId;
                                                                                db.query(customer, {}, function (err, customers) {
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

                                                            }
                                                       }
                                                       delete string[0].sms_notification;
                                                       let retailer = " UPDATE retailer_flat SET parent_le_id ='" + string[0].parent_le_id + "' , business_legal_name = '" + string[0].business_legal_name + "',legal_entity_type_id='" + string[0].legal_entity_type_id + "',business_type_id ='" + string[0].business_type_id + "' ,name ='" + string[0].name + "', mobile_no = '" + string[0].mobile_no + " ', volume_class_id = '" + string[0].volume_class_id + "', volume_class = '" + string[0].volume_class + "', No_of_shutters = '" + string[0].No_of_shutters + "', suppliers = '" + string[0].suppliers + "', business_start_time = '" + string[0].business_start_time + "', business_end_time = '" + string[0].business_end_time + "', address = '" + string[0].address + "', address1 = '" + string[0].address1 + "', address2 = '" + string[0].address2 + "', area_id = '" + string[0].area_id + "',AREA = '" + string[0].area + "',hub_id = '" + string[0].hub_id + "', beat_id = '" + string[0].beat_id + "', spoke_id = '" + string[0].spoke_id + "', beat = '" + string[0].beat + "', city = '" + string[0].city + "', state_id = '" + string[0].state_id + "', state = '" + string[0].state + "', country = '" + string[0].country + "', locality = '" + string[0].locality + "', landmark = '" + string[0].lankmark + "', pincode = '" + string[0].pincode + "' , smartphone = '" + string[0].smartphone + "', network = '" + string[0].network + "', master_manf = '" + string[0].master_manf + "', orders_old = '" + string[0].orders_old + "', last_order_date_old = '" + formatted_date + "', beat_rm_name = '" + string[0].beat_rm_name + "',created_by = '" + string[0].created_by + "', created_at = '" + formatted + "', created_time = '" + string[0].created_time + "', updated_by = '" + string[0].updated_by + "', updated_at = '" + formattedDate + "', updated_time = '" + string[0].updated_time + "', latitude = '" + string[0].latitude + "', longitude = '" + string[0].longitude + "',is_icecream = ' " + string[0].is_icecream + "', is_milk = '" + string[0].is_milk + "', is_deepfreezer = '" + string[0].is_deepfreezer + "', is_fridge = '" + string[0].is_fridge + "', is_vegetables = '" + string[0].is_vegetables + "', is_visicooler = '" + string[0].is_visicooler + "',dist_not_serv = '" + string[0].dist_not_serv + "', facilities = " + string[0].facilities + " WHERE legal_entity_id =" + legalEntityId;
                                                       db.query(retailer, {}, function (err, retailers) {
                                                            if (err) {
                                                                 con.rollback(function () {
                                                                      console.log(err);
                                                                 });
                                                            } else if (Object.keys(retailers).length > 0) {

                                                            }
                                                       })
                                                  }
                                                  con.commit(function (err) {
                                                       if (err) {
                                                            con.rollback(function () {
                                                                 console.log(err);
                                                            });
                                                       } else {
                                                            con.release();
                                                       }

                                                  });

                                             }

                                        })
                                   }
                              })

                         }
                    })
               })
               // })
          }
     } catch (err) {

          console.log(err);
     }
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
                         return resolve(err);
                         return reject(err);
                    } else if (Object.keys(master_lookup).length > 0) {
                         return resolve(master_lookup);
                    }

               })

          })

     } catch (err) {
          console.log(err);
     }

}


/*
Purpose : updateProfile function is used to check if the customer_token passed when the customer is logged in is valid..
author : Deepak Tiwari
Request : Require customer_token , firstname , filepath2 , lastname_get
Resposne : Update multiple table  .
*/
exports.updateProfile = function (customer_token, f_name, filepath2, l_name) {
     return new Promise((resolve, reject) => {
          let string = JSON.stringify(customer_token)
          let data = "SELECT user_id , legal_entity_id  FROM  users  WHERE  password_token =" + string;
          db.query(data, {}, function (err, rows) {
               if (err) {
                    return reject(err);
               }
               else if (Object.keys(rows).length > 0) {
                    let user_id = rows[0].user_id;
                    let legal_entity_id = rows[0].legal_entity_id;
                    let current_datetime = new Date();
                    let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                    let data;
                    if (filepath2.length != 0 && typeof f_name.length != 'undefined' && typeof l_name.length != 'undefined') {
                         data = "update users  set firstname = '" + f_name + "',lastname ='" + l_name + "',profile_picture ='" + filepath2 + "' ,updated_at ='" + formatted_date + "' where password_token =" + string;
                    } else if (filepath2.length == 0 && typeof f_name.length != 'undefined' && typeof l_name.length != 'undefined') {
                         data = "update users  set firstname = '" + f_name + "',lastname ='" + l_name + "' ,updated_at ='" + formatted_date + "' where password_token =" + string
                    } else if (filepath2.length == 0 && typeof f_name.length == 'undefined' && typeof l_name.length != 'undefined') {
                         data = "update users  set lastname ='" + l_name + "',updated_at ='" + formatted_date + "' where password_token =" + string;
                    } else if (filepath2.length == 0 && typeof f_name.length != 'undefined' && typeof l_name.length == 'undefined') {
                         data = "update users  set firstname = '" + f_name + "' ,updated_at ='" + formatted_date + "' where password_token =" + string;
                    } else if (filepath2.length == 0 && typeof f_name.length == 'undefined' && typeof l_name.length == 'undefined') {
                         data = "update users  set updated_at ='" + formatted_date + "' where password_token =" + string;
                    } else if (filepath2.length != 0 && typeof f_name.length == 'undefined' && typeof l_name.length == 'undefined') {
                         data = "update users  set profile_picture ='" + filepath2 + "' ,updated_at ='" + formatted_date + "' where password_token =" + string;
                    }
                    db.query(data, {}, function (err, rows) {
                         if (err) {
                              return reject(err);
                         }
                         else if (Object.keys(rows).length > 0) {
                              let BasicData = { 'firstname': f_name, 'lastname': l_name, 'documents': filepath2 };


                              return resolve(BasicData);
                         }
                         else {
                              return reject("No mapping found..")
                         }
                    });
                    updateFlatTable(legal_entity_id);

               }
               else {
                    return reject("No mapping found..")
               }
          });
     });

}



/*
Purpose : allTelephone function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require customer_token , firstname , filepath2 , lastname_get
Resposne : Update multiple table  .
*/
module.exports.allTelephone = function (telephone, customer_token) {
     try {
          let result = [];
          return new Promise((resolve, reject) => {
               if (telephone != null) {
                    let query = "select count(mobile_no) as count from users as u where u.mobile_no =" + telephone;
                    db.query(query, {}, function (err, response) {
                         if (err) {
                              console.log(err);

                         } else if (Object.keys(response).length > 0) {
                              result.push(response[0].count);
                              return resolve(result)
                         }

                    });


               }
               // console.log(result)
               // resolve(result)

          })

     } catch (err) {
          console.log(err)
     }
}

/*
Purpose : allTelephone function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require customer_token , firstname , filepath2 , lastname_get
Resposne : Update multiple table  .
*/
module.exports.allTelephone_merge = function (telephone1, telephone2, customer_token) {
     try {
          return new Promise((resolve, reject) => {
               let result;
               if (telephone1 != null && telephone2 != null) {
                    let query = "select count(mobile_no) as count from users as u where u.mobile_no IN(" + telephone1 + ',' + telephone2 + ")";
                    db.query(query, {}, function (err, response) {
                         if (err) {
                              console.log(err);

                         } else if (Object.keys(response).length > 0) {
                              result = response[0].count;
                              resolve(result);
                         }
                    });

               }

          })

     } catch (err) {
          console.log(err)
     }
}



/*
Purpose : getMobile function is used to get user mobile number based on user_id.
author : Deepak Tiwari
Request : Require user_id
Resposne : return user mobile number.
*/
module.exports.getMobile = function (user_id) {
     let data = "select mobile_no from users where user_id =" + user_id;
     db.query(data, {}, function (err, response) {
          if (err) {
               console.log(err);
          } else if (Object.keys(response).length > 0) {
               let mobile_no = response[0].mobile_no;
               return mobile_no;
          }
     })
}


/*
Purpose : updateTelephone function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require customer_token, telephone
Resposne : update user mobile number.
*/
module.exports.updateTelephone = function (customer_token, telephone) {
     try {
          let string = JSON.stringify(customer_token);
          let data = " select user_id , legal_entity_id from users where password_token =" + string;
          db.query(data, {}, function (err, user_data) {
               if (err) {
                    console.log(err);
               } else if (Object.keys(user_data).length > 0) {
                    let user_id = user_data[0].user_id;
                    let legal_entity_id = user_data[0].legal_entity_id;

                    if (legal_entity_id != 2) {
                         let current_datetime = new Date();
                         let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                         let query = "update users set mobile_no ='" + telephone + "', updated_at= '" + formatted_date + "'where password_token =" + string;
                         db.query(query, {}, function (err, updated) {
                              if (err) {
                                   console.log(err);
                              } else {
                              }
                              //db.release();
                         })
                         let Email_query = "select email_id from users where password_token =" + string;
                         db.query(Email_query, {}, function (err, response) {
                              if (err) {
                                   console.log(err);
                              } else {
                                   let existingEmail = response[0].email_id;
                                   let pos = existingEmail.indexOf('@nomail');
                                   if (pos != -1) {
                                        let update = " update users set email_id ='" + telephone + ".@nomail.com' , update_at ='" + formatted_date + "' where password_token =" + string;
                                        db.query(update, {}, function (err, updated) {
                                             if (err) {
                                                  console.log(err)
                                             } else {
                                             }

                                        })

                                   }

                              }

                         })

                         updateFlatTable(legal_entity_id);
                    }





               }
          })

     } catch (err) {
          console.log(err)

     }



}


/*
Purpose :  updateCustomerContact() function is used to update user contact details.
author : Deepak Tiwari
Request : Require user_id , contact_no1 , contact_name1
Resposne : Update user contact details.
*/
module.exports.updateCustomerContact = function (user_id1, contact_no1, contact_name2, contact_name1) {
     if (user_id1 != null) {
          let current_datetime = new Date();
          let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
          let data = " update users set mobile_no =" + contact_no1 + " , updated_at ='" + formatted_date + "',firstname ='" + contact_name1 + "', lastname ='" + contact_name2 + "',email_id ='" + contact_no1 + ".@nomail.com" + "'where user_id=" + user_id1;
          db.query(data, {}, function (err, updated) {
               if (err) {
                    console.log(err);
               } else {
               }
          })
     } else {
          let email_id = contact_no1 + ".@nomail.com";
          let result = { 'contact_no1': contact_no1, 'contact_name1': contact_name1, 'email_id': email_id, 'user_id1': user_id1 }
          return result;

     }


}


/*
Purpose :  AddContact() function is used to add user contact details.
author : Deepak Tiwari
Request : Require  contact_no1 , contact_name1 , customer_token 
Resposne : Add user contact details.
*/
module.exports.AddContact = function (contact_no1, contact_name1, customer_token) {
     try {
          let string = JSON.stringify(customer_token)
          let data = "select legal_entity_id , user_id from users where password_token =" + string;
          db.query(data, {}, function (err, legal_entity_id) {
               if (err) {
                    console.log(err);
               } else if (Object.keys(legal_entity_id).length > 0) {
                    let query = "insert into users(mobile_no ,firstname ,email_id , legal_entity_id ,is_active ,updated_at)  values (" + contact_no1 + ',' + contact_name1 + ',' + contact_no1 + ".@nomail.com" + ',' + legal_entity_id[0].legal_entity_id + ',' + 1 + ',' + new Data('Y-m-d H:i:s');
                    db.query(query, {}, function (err, updated) {
                         if (err) {
                              console.log(err);
                         } else {
                              let user_id = updated.insertId;
                              return user_id;
                         }
                    })
               }

          })
     } catch (err) {
          console.log(err);

     }
}


/*
Purpose :  eMailcheck() function is used to get  user email_id.
author : Deepak Tiwari
Request : Require  contact_no1 , contact_name1 , customer_token 
Resposne : Add user contact details.
*/
module.exports.eMailcheck = function (customer_token) {
     let string = JSON.stringify(customer_token)
     let data = "select  u.email_id  from users as u where u.password_token=" + string;
     db.query(data, {}, function (err, result) {
          if (err) {

          } else if (Object.keys(result).length > 0) {
               return result[0].email_id;
          }
     })
}

/*
Purpose :  checkPincodeLegalentity function is used to check enter pincode is valid or not .
author : Deepak Tiwari
Request : Require  pincode .
Resposne : Add valid pincode .
*/
function checkPincodeLegalentity(pincode) {
     try {
          return new Promise((resolve, reject) => {
               let data = "select legal_entity_id from wh_serviceables where pincode=" + pincode;
               db.query(data, {}, function (err, getLegalEntity) {
                    if (err) {
                         console.log(err)
                    } else if (Object.keys(getLegalEntity).length > 0) {
                         return resolve(getLegalEntity[0].legal_entity_id);
                    }
                    else {
                         return false;
                    }
               })



          })
     } catch (err) {
          console.log(err);
     }
}

/*
Purpose :  updateBussinessType function is used to update bussiness type  .
author : Deepak Tiwari
Request : Require  business_type, business_legal_name, buyer_type, customer_token .
Resposne : Update bussiness type .
*/
module.exports.updateBussinessType = function (business_type, business_legal_name, buyer_type, customer_token) {
     try {
          let string = JSON.stringify(customer_token);
          let data = " select legal_entity_id , user_id from users where password_token =" + string;
          db.query(data, {}, function (err, legal_entity_id) {
               if (err) {
                    console.log(err)
               } else if (Object.keys(legal_entity_id).length > 0) {
                    if (legal_entity_id[0].legal_entity_id != 2) {
                         let current_datetime = new Date();
                         let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                         let query = "update legal_entities set business_legal_name='" + business_legal_name + "',business_type_id=" + business_type + ",legal_entity_type_id=" + buyer_type + ",updated_by=" + legal_entity_id[0].user_id + ",updated_at='" + formatted_date + "'where legal_entity_id=" + legal_entity_id[0].legal_entity_id;
                         db.query(query, {}, function (err, updated) {
                              if (err) {
                                   console.log(err)
                              } else {
                              }
                         })
                    }

               }
          })
     } catch (err) {
          console.log(err)
     }

}

/*
Purpose : updateAddressData function is used to check if the customer_token passed when the customer is logged in is valid..
author : Deepak Tiwari
Request : Require customer_token, address_1, address_2, locality, landmark, city, postcode, state, gstin, arn_number
Resposne : update user address details in database.
*/
module.exports.updateAddressData = function (customer_token, address_1, address_2, locality, landmark, city, postcode, state, gstin, arn_number) {
     try {
          let pincode = postcode;
          let string = JSON.stringify(customer_token);
          let data = "select user_id , legal_entity_id from users where password_token=" + string;
          let le_wh_ids;
          let result = {};
          db.query(data, {}, function (err, user_data) {
               if (err) {
                    console.log(err);

               } else if (Object.keys(user_data).length > 0) {
                    let parent_le_id;
                    checkPincodeLegalentity(postcode).then((parent_id) => {
                         if (err) {
                              console.log(err)
                         } else {
                              parent_le_id = parent_id;
                              if (parent_le_id != null) {
                                   if (user_data[0].legal_entity_id != 2) {
                                        let current_datetime = new Date();
                                        let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                                        let query = "update legal_entities as le  set le.legal_entity_id='" + user_data[0].legal_entity_id + "',le.address1 ='" + address_1 + "',le.address2='" + address_2 + "',le.locality ='" + locality + "',le.landmark = '" + landmark + "',le.city ='" + city +
                                             "',le.pincode ='" + postcode + "',le.state_id ='" + state + "',le.gstin ='" + gstin + "',le.arn_number ='" + arn_number + "',le.updated_at ='" + formatted_date + "',parent_le_id ='" + parent_le_id + "'where le.legal_entity_id =" + user_data[0].legal_entity_id;
                                        db.query(query, {}, function (err, updated) {
                                             if (err) {
                                                  console.log(err);
                                             } else {
                                                  let Query = "select group_concat(distinct ws.le_wh_id separator ',') as le_wh_id from wh_serviceables as ws JOIN legalentity_warehouses as lew ON ws.pincode = lew.pincode where ws.pincode =" + pincode;
                                                  db.query(Query, {}, function (err, le_wh_id) {
                                                       if (err) {
                                                            console.log(err)
                                                       } else if (Object.keys(le_wh_id).length > 0) {
                                                            le_wh_ids = le_wh_id[0].le_wh_id;

                                                       } else {
                                                            le_wh_ids = ' ';
                                                       }
                                                       updateFlatTable(user_data[0].legal_entity_id);
                                                       let AddressData = {
                                                            'address_1': address_1, 'address_2': address_2,
                                                            'locality': locality, 'landmark': landmark, 'city': city, 'postcode': postcode,
                                                            'customer_token': customer_token, 'le_wh_id': le_wh_ids
                                                       };
                                                       result = AddressData;
                                                  })
                                             }
                                        })
                                        return result;
                                   } else {
                                        return '';
                                   }

                              }
                         }
                    });
               }
          })

     } catch (err) {
          console.log(err);
     }
}

/*
Purpose : To all getGstinNo 
author : Deepak Tiwari
Request : Require gstin
Resposne : return gstin number .
*/
module.exports.getGstinNo = function (gstin) {
     try {
          let counts = 1;
          let string = JSON.stringify(gstin)
          let data = "select count(gstin) as count from legal_entities as le where le.gstin =" + string;
          db.query(data, {}, function (err, result) {
               if (err) {
                    console.log(err);
               } else {
                    counts = result[0].count;
                    return counts;
               }
          })

     } catch (err) {
          console.log(err);

     }
}

/*
Purpose : getUserGstinNo perticular user gstin number  based customer_token
author : Deepak Tiwari
Request : Require customer_token
Resposne : return user gstin number.
*/
module.exports.getUserGstinNo = function (customer_token) {
     let gstin;
     let string = JSON.stringify(customer_token);
     let data = "select le.gstin from legal_entities as le join users as u ON u.legal_entity_id = le.legal_entity_id  where u.password_token=" + string;
     db.query(data, {}, function (err, gstin_data) {
          if (err) {
               console.log(err);
          } else if (Object.keys(gstin_data).length > 0) {
               gstin = gstin_data[0].gstin;
          } else {
               gstin = '';
          }
          return gstin;
     })
}

/*
Purpose : Used to get Email with count
author : Deepak Tiwari
Request : Require user email
Resposne : return email count .
*/
module.exports.getEmail = function (email) {
     try {
          let string = JSON.stringify(email)
          let data = "select count(email_id) as count from users where email_id =" + string;
          db.query(data, {}, function (err, mail) {
               if (err) {
                    console.log(err)
               } else {
                    return mail[0].count;
               }
          })
     } catch (err) {
          console.log(err)
     }

}

/*
Purpose : User to get Arn No with count
author : Deepak Tiwari
Request : Require arn_number
Resposne : return arn_number  count .
*/
module.exports.getArnNo = function (arn_number) {
     let result = 0;
     let data = "select count(arn_number) as count from legal_entities as le where le.arn_number=" + arn_number;
     db.query(data, {}, function (err, response) {
          if (err) {
               console.log(err)

          } else if (Object.keys(response).length > 0) {
               result = response[0].count;
          }
     })
     return result;
}


/*
Purpose : getUserArnNo function used to get arnno for perticular user.
author : Deepak Tiwari
Request : Require customer_token.
Resposne : return arn_number  count .
*/
module.exports.getUserArnNo = function (customer_token) {
     let arn_data;
     let string = JSON.stringify(customer_token);
     let data = "select le.arn_number from legal_entities as le JOIN users as u ON u.legal_entity_id = le.legal_entity_id where u.password_token =" + string;
     db.query(data, {}, function (err, arn_no) {
          if (err) {
               console.log(err)

          } else if (Object.keys(arn_no).length > 0) {
               arn_data = arn_no[0].arn_number;

          } else {
               arn_data = '';
          }
          return arn_data;
     })
}

/*
Purpose : updateEmail function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require user customer_token, email
Resposne : Update user email.
*/
module.exports.updateEmail = function (customer_token, email) {
     try {
          let string = JSON.stringify(customer_token)
          let data = "select user_id ,legal_entity_id from users where password_token=" + string;
          db.query(data, {}, function (err, user_data) {
               if (err) {
                    console.log(err)
               } else if (Object.keys(user_data).length > 0) {
                    let user_id = user_data[0].user_id;
                    let legal_entity_id = user_data[0].legal_entity_id;
                    if (legal_entity_id != 2) {
                         let pos = email.indexOf('@nomail');
                         if (pos == -1) {
                              let current_datetime = new Date();
                              let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                              let query = "update users set email_id='" + email + "',updated_at ='" + formatted_date + "'where password_token=" + string;
                              db.query(query, {}, function (err, updated) {
                                   if (err) {
                                        console.log(err)
                                   } else {

                                   }
                              })
                         } else {
                              let Query_1 = "select email_id , mobile_no from users where password_token =" + string;
                              db.query(Query_1, {}, function (err, data) {
                                   if (err) {
                                        console.log(err)
                                   } else if (Object.keys(data).length > 0) {
                                        if (data[0].email_id == null) {
                                             let mobile_no = data[0].mobile_no;
                                             let current_datetime = new Date();
                                             let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                                             let sample = "update users set email_id ='" + mobile_no + ".@nomail.com" + "',updated_at ='" + formatted_date + "'where password_token =" + string;
                                             db.query(sample, {}, function (err, updated) {
                                                  if (err) {
                                                       console.log(err)
                                                  } else {

                                                  }
                                             })
                                        }
                                   }
                              })
                         }

                    }

               }
          })
          return 1;
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
module.exports.generateOtp = function (customer_token, phone) {
     var Curl = require('node-libcurl').Curl;
     return new Promise((resolve, reject) => {
          var curl = new Curl();
          let random_number = Math.floor(100000 + Math.random() * 999999);
          let string = JSON.stringify(customer_token);
          let mobile_number = phone;
          let message = "Your OTP for Ebutor is  " + random_number;
          if (mobile_number.length >= 10 && message != null) {
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
                    let current_datetime = new Date();
                    let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                    let query = "update users set otp='" + random_number + "',updated_at ='" + formatted_date + "'where password_token =" + string;
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              console.log(err)
                         } else {
                              console.log('Otp')
                         }
                    })
                    resolve(random_number);

               }

          }
          // curl.close();
     })
}


/*
Purpose : getOtp function is used to check if the customer_token passed when the customer is logged in is valid..
author : Deepak Tiwari
Request : Require user ccustomer_token
Resposne : send otp.
*/
module.exports.getOtp = function (customer_token) {
     return new Promise((resolve, reject) => {
          let string = JSON.stringify(customer_token);
          let data = "select u.otp from  users as u where u.password_token =" + string;
          db.query(data, {}, function (err, rows) {
               if (err) {
                    reject(err);
               } else if (Object.keys(rows).length > 0) {
                    resolve(rows[0])

               }
          })
     })


}

/*
Purpose : getTelephone function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require user ccustomer_token
*/
module.exports.getTelephone = function (customer_token) {

     let data = " select u.mobile_no from users as u where u.password_token ='" + customer_token + "'";
     db.query(data, {}, function (err, response) {
          if (err) {
               console.log(err)
          } else if (Object.keys(response).length > 0) {
               return response;
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
          db.query(data, {}, function (err, response) {
               if (err) {
                    return reject(err)
               } else if (Object.keys(response).length > 0) {
                    return resolve(response[0].count);
               }
          })
     })
}



module.exports.Test_email = function (username) {

     var mailGenerator = new Mailgen({

          theme: 'EmailUpdate',

          product: {

               name: 'Ebutor',

               logo: '',//'http://localhost:1203/cp-mgr-api/Ebutor_logo.png',

               img: '',

               link: 'https://portal.ebutor.com/assets/admin/layout/img/logo.png'

          }

     });

     var email = {

          body: {

               greeting: 'Hi',

               username: 'Deepak',

               notifImg: 'https://portal.ebutor.com/assets/admin/layout/img/logo.png',//'' http://localhost:1203/cp-mgr-api/Ebutor_logo.png',

               notifName: 'Account Confirmation',

               intro: ["Thank you for updating email in Ebutor.."],

               outro: "Have Questions? Just reply to this email or call +1313671-1935.",

               signature: 'Yours truly'

          }

     };

     var emailBody = mailGenerator.generate(email);

     return emailBody;

};



/*
 *   For: send Email
 *   Author: Deepak Tiwari
 *   Request params parameters: subject, email, body
 *   Returns:Send email to user mail
 */
exports.sendMail = function (subject, email, body) {

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
          to: 'tdeepak240@gmail.com',
          subject: subject,
          html: body
     };
     // transporter.use('compile', htmlToText(HelperOptions));
     transporter.sendMail(HelperOptions, (error, info) => {
          if (error) {
               return console.log(error);
          }
          else {
               console.log(info);
          }
     });
};



/*
     * Class Name: updateGeoLocation
     * Description: Function used to updateGeoLocation
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 2nd dec 2016
     * Modified Date & Reason: 
   */

module.exports.updateGeo = function (user_id, legal_entity_id, latitude, longitude) {
     try {
          return new Promise((resolve, reject) => {
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let query = "update legal_entities set latitude ='" + latitude + "' ,longitude ='" + longitude + "', updated_by='" + user_id + "' ,updated_at ='" + formatted_date + "'where legal_entity_id =" + 0;
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(response.affectedRows);
                    }

               })

          })
     } catch (err) {
          console.log(err);
     }
}


module.exports.getFfBeatByPincodewise = function (ff_id, hub, pincode) {
     try {
          return new Promise((resolve, reject) => {
               if (hub == '') {
                    hub = 'NULL';
               }
               let query = "CALL getMobileBeatDetailsByPin(" + ff_id + ',' + pincode + ',' + hub + ")"
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
          let error = { 'status': 'failed', 'message': "Internal server error" }
          return error;
     }
}


/*
Purpose : checkPermissionByFeatureCode() Used to check user permission based on userid and featurecode
Author :Deepak Tiwari
Request : featureCode, userId
Resposne : Returns Mobile features
*/
module.exports.checkPermissionByFeatureCode = function (featureCode, userId) {
     try {
          return new Promise((resolve, reject) => {
               // userId is for superadmin
               if (userId == 1) {
                    return true;
               } else {
                    let data = "select count(features.name)  from role_access JOIN features ON role_access.feature_id = features.feature_id  JOIN user_roles ON role_access.role_id = user_roles.role_id where user_roles.user_id =" + userId + "&& features.feature_code ='" + featureCode + "'&& features.is_active = 1";
                    let count;
                    db.query(data, {}, function (err, name) {
                         if (err) {
                              reject(err)
                         } else if (Object.keys(name).length > 0) {
                              count = name;
                         }
                    })
                    resolve(count > 0) ? true : false;
               }
          })

     } catch (err) {
          console.log(err)
     }
}



exports.getffLegalentity = function (ff_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select legal_entity_id from users where user_id=" + ff_id
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         resolve(result[0].legal_entity_id)
                    } else {
                         resolve(0)
                    }
               })

          })
     } catch (err) {
          let error = { 'status': 'failed', 'message': "Internal server error" }
          return error;
     }
}


exports.getPincodeLegalentity = function (pincode) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select legal_entity_id from wh_serviceables where pincode=" + pincode
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(result).length > 0) {
                         resolve(result[0].legal_entity_id)
                    } else {
                         resolve(0)
                    }
               })
          })
     } catch (err) {
          let error = { 'status': 'failed', 'message': "Internal server error" }
          return error;
     }
}

exports.getBeatsForGlobalAccess = function (pincode) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select pjp.pjp_name,pjp.pjp_pincode_area_id FROM wh_serviceables ws, dc_hub_mapping lw, pjp_pincode_area pjp, spokes s, legalentity_warehouses lew WHERE ws.le_wh_id = lw.dc_id AND pjp.le_wh_id = lw.hub_id AND  s.spoke_id = pjp.spoke_id AND lew.le_wh_id = pjp.le_wh_id AND ws.pincode = " + pincode
               db.query(query, {}, function (err, beats) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(beats).length > 0) {
                         resolve(beats)
                    }
               })
          })
     } catch (err) {
          let error = { 'status': 'failed', 'message': "Internal server error" }
          return error;
     }
}


exports.getFfDynamicData = function (data) {
     try {
          return new Promise((resolve, reject) => {
               if (typeof data.data_source != 'undefined' && data.data_source_name && data.data_input) {
                    let val = data.data_input;
                    let query;
                    let keys = [];
                    let values
                    if (typeof val.value_array != 'undefined' && typeof val.key_array != 'undefined') {
                         keys = val.key_array;
                         let values = val.value_array;
                         if (data.data_source == 1) {
                              query = "select * from " + data.data_source_name;
                              keys.push("from_date", "to_date", "user_id", "activity_type", "flag");
                              for (let i = 0; i < keys.length > 0; i++) {
                                   if (i == 0) {
                                        query = query.concat(" where ");
                                        if (typeof values[i] == "string") {
                                             query = query.concat(keys[i] + "='" + values[i] + "'");
                                        }
                                        else if (typeof values[i] == "integer" || typeof values[i] == "double" || typeof values[i] == "number") {
                                             query = query.concat(keys[i] + "=" + values[i]);
                                        }
                                   } else {
                                        if (typeof values[i] == "string") {
                                             query = query.concat(" and " + keys[i] + "= '" + values[i] + "'");
                                        }
                                        else if (typeof values[i] == "integer" || typeof values[i] == "double" || typeof values[i] == "number") {
                                             query = query.concat(" and " + keys[i] + "= " + values[i]);

                                        }

                                   }
                              }
                              db.query(query, {}, function (err, Data) {
                                   if (err) {
                                        console.log(err);
                                        reject(err)
                                   } else if (Object.keys(Data).length > 0) {
                                        let response = { 'status': 'success', 'message ': 'success', 'data': Data }
                                        resolve(response)
                                   }
                              })
                         } else if (data.data_source == 2) {
                              let procedureParams = "";
                              let len = values.length;
                              for (let element = 0; element < values.length; element++) {
                                   if (typeof values[element] == 'string') {
                                        procedureParams = procedureParams + "" + values[element] + ""
                                   } else if (typeof values[element] == 'number' || typeof values[element] == 'double' || typeof values[element] == 'integer') {
                                        procedureParams = procedureParams.concat(values[element])
                                   }


                                   if (values[element] >= 0 && element < len - 1) {
                                        procedureParams = procedureParams + ","
                                   }

                              }



                              let call = "call " + data.data_source_name + "(" + procedureParams + ")";
                              db.query(call, {}, function (err, Data) {
                                   if (err) {
                                        console.log(err);
                                        reject(err)
                                   } else if (Object.keys(Data).length > 0) {
                                        let response = { 'status': 'success', 'message ': 'success', 'data': Data[0] }
                                        resolve(response)
                                   }
                              })
                         }
                    } else {
                         let err = { 'status': 'failed', 'message': 'input is required' }
                         resolve(err);
                    }
               } else {
                    let error = { 'status': 'failed', 'message': 'input is required' }
                    resolve(error)
               }
          })
     } catch (err) {
          console.log(err)
     }


}



exports.getFFPincode = function (id) {
     try {
          return new Promise((resolve, reject) => {
               var today = new Date();
               var day = today.getDay();
               var daylist = ["Sun", "Mon", "Tue", "Wed ", "Thu", "Fri", "Sat"];
               console.log("Today is : " + daylist[day] + ".");
               let query = "select default_pincode as pincode,pjp_pincode_area_id,pjp_name from pjp_pincode_area where default_pincode is not null and days like '%" + daylist[day] + "%' and  rm_id=" + id;
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(response).length > 0) {
                         resolve(response)
                    } else {
                         resolve(response);
                    }
               })
          })
     } catch (err) {
          console.log(err)
     }
}


module.exports.getFFPin = function (id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select wh.pincode FROM  wh_serviceables wh INNER JOIN legalentity_warehouses le ON le.le_wh_id = wh.le_wh_id INNER JOIN users u ON u.legal_entity_id = le.legal_entity_id WHERE u.user_id = " + id;
               db.query(query, {}, function (err, response) {
                    if (err) {
                         console.log(err)
                         reject(err)
                    } else if (Object.keys(response).length > 0) {
                         resolve(response)
                    } else {
                         resolve(response);
                    }
               })
          })
     } catch (err) {
          console.log(err)
     }
}



module.exports.getUserIdByCustomerToken = function (customer_token) {
     try {
          console.log("customer", customer_token)
          return new Promise((resolve, reject) => {
               if (customer_token != null) {
                    let string = JSON.stringify(customer_token)
                    let query = "select user_id from users where password_token =" + string;
                    db.query(query, {}, function (err, data) {
                         console.log("query", query)
                         if (err) {
                              console.log(err)
                              reject(err)
                         } else if (Object.keys(data).length > 0) {
                              resolve(data[0].user_id)
                         } else {
                              resolve(null)
                         }
                    })
               }
          })

     } catch (err) {
          console.log(err)
     }
}




