
const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;
const pool = dbconnection.pool;
const mongoose = require('mongoose');
const user = mongoose.model('User');
const Sequelize = require('sequelize');
const sequelize = require('../../../config/sequelize');
var moment = require('moment');
var Mutex = require('async-mutex').Mutex;



/**
 * purpose : Used to get warehouse id based product_id , wh_id
 * request : productid , warehouse_id
 * return : return  le_wh_id,
 * author : Deepak Tiwari
 */
module.exports.getwareHouseId = function (product_id, wh_id, segmentId) {
     try {
          return new Promise((resolve, reject) => {
               let query = "SELECT GetCPInventoryStatus(" + product_id + ",'" + wh_id + "'," + segmentId + ',' + 4 + ") as le_wh_id "
               db.query(query, {}, function (err, rows) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         console.log("warehouse details", rows)
                         resolve(rows)
                    } else {
                         resolve(0);
                    }
               })
          })

     } catch (err) {
          console.log(err)
     }


}


/**
 * purpose : Used to perform some select query which we were passing from comtroller
 * request : query , product_id , le_wh_id
 * return : return  le_wh_id,
 * author : Deepak Tiwari
 */
module.exports.Query = function (query) {
     try {
          return new Promise((resolve, reject) => {
               console.log("query ===>33", query)
               db.query(query, {}, function (err, rows) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         resolve(rows);
                    } else {
                         resolve(null)
                    }
               })
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
 * purpose :Used to get customer id based on customer_token
 * request : customer_token or sales_token
 * return : return  customer_id,
 * author : Deepak Tiwari
 */
module.exports.getcustomerId = function (token) {
     try {
          return new Promise((resolve, reject) => {
               let customerId;
               let query = "select user_id from users where password_token ='" + token + "'";
               db.query(query, {}, function (err, rows) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         customerId = rows[0].user_id;
                         resolve(customerId);
                    } else {
                         customerId = 0;
                         resolve(customerId);
                    }
               })
          })

     } catch (err) {
          console.log(err)
          return ({ 'status': 'failed', 'message': 'Internal server error' })
     }
}

/**
 * purpose :Used to get customer id based on customer_token
 * request : customer_token or sales_token
 * return : return  customer_id,
 * author : Deepak Tiwari
 */
function getcustomerId_model(token) {
     try {
          return new Promise((resolve, reject) => {
               let customerId;
               let query = "select user_id from users where password_token ='" + token + "'";
               db.query(query, {}, function (err, rows) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         customerId = rows[0].user_id;
                         resolve(customerId);
                    } else {
                         customerId = 0;
                         resolve(customerId);
                    }
               })
          })

     } catch (err) {
          console.log(err)
          return ({ 'status': 'failed', 'message': 'Internal server error' })
     }
}


/**
 * purpose :Used to get customer type based on legal_entity_id
 * request : legal_entity_id
 * return : return  customer type,
 * author : Deepak Tiwari
 */
module.exports.getUserCustomerType = function (legal_entity_id) {
     try {
          return new Promise((resolve, reject) => {
               if (legal_entity_id != '') {
                    let query = "select legal_entity_type_id from legal_entities from legal_entity_id =" + legal_entity_id
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(rows).length > 0) {
                              resolve(rows[0].legal_entity_type_id)
                         } else {
                              resolve(0)
                         }
                    })
               }
          })

     } catch (err) {
          console.log(err)
     }
}


/**
 * purpose :Used to get order count for perticular cus_le_id
 * request : legal_entity_id
 * return : return  order count,
 * author : Deepak Tiwari
 */
module.exports.getSuccessOrderCount = function (legal_entity_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select count(gds_order_id) as count from gds_orders where cust_le_id=" + legal_entity_id + " && order_status_id IN(" + 17007 + ',' + 17008 + ',' + 17023 + ")";
               db.query(query, {}, function (err, count) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(count).length > 0) {
                         resolve(count[0].count)

                    }
               })
          })
     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}

/**
 * purpose :Used to get order count for perticular cus_le_id
 * request : legal_entity_id
 * return : return  order count,
 * author : Deepak Tiwari
 */
module.exports.getParentCustId = function (legal_entity_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select user_id as cust_user_id from users where legal_entity_id =" + legal_entity_id + " && is_parent = 1"
               db.query(query, {}, function (err, rows) {

                    console.log("query", query)
                    if (err) {
                         console.log(err);
                         reject(err)
                    } else if (Object.keys(rows).length > 0) {
                         resolve(rows)
                    } else {
                         resolve(null)
                    }
               })
          })
     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}

/**
 * purpose :Used to get order count for perticular cus_le_id
 * request : legal_entity_id
 * return : return  order count,
 * author : Deepak Tiwari
 */
module.exports.getUserEcash = function (userId) {
     try {
          return new Promise((resolve, reject) => {
               if (userId > 0 && userId != '') {
                    let query = "select * from user_ecash_creditlimit as uec  where uec.user_id  =" + userId + " limit 1";
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(rows).length > 0) {
                              resolve(rows[0])
                         } else {
                              resolve([])
                         }
                    })
               }
          })
     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}

/**
 * purpose :Used to get minimum order value based customer type
 * request : customerType, leWhId, isSelfOrder
 * return : return  minimum order value,
 * author : Deepak Tiwari
 */
module.exports.getCustomertypeMinorderValue = function (customerType, leWhId, isSelfOrder) {
     try {
          return new Promise((resolve, reject) => {
               let minimumOrderValue;
               let movOrderCount;
               if (customerType != '') {
                    let query = "select minimum_order_value, self_order_mov, mov_ordercount from ecash_creditlimit where customer_type='" + customerType + "' && dc_id =" + leWhId;
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              console.log(err);
                              reject(err)
                         } else if (Object.keys(rows).length > 0) {
                              if (!isSelfOrder) {
                                   // For Self Orders
                                   minimumOrderValue = (typeof rows.self_order_mov != 'undefined' && rows.self_order_mov != '') ? rows.self_order_mov : 0;
                              } else {
                                   // For Field Force Placed Orders
                                   minimumOrderValue = (typeof rows.minimum_order_value != 'undefined' && rows.minimum_order_value != '') ? rows.minimum_order_value : 0;
                              }
                              movOrderCount = (typeof rows.mov_ordercount && rows.mov_ordercount != '') ? rows.mov_ordercount : 0;
                              resolve({ "min_order_value": minimumOrderValue, "mov_ordercount": movOrderCount })
                         } else {
                              resolve({ "min_order_value": '', "mov_ordercount": '' })
                         }
                    })
               }
          })

     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}


/**
 * purpose : This function is used to check ScheduledDeliveryDate is public holiday or not
 * request : scheduled_delivery_date
 * return : return  reason,
 * author : Deepak Tiwari
 */
module.exports.ScheduledDeliveryDate = function (scheduled_delivery_date) {
     try {
          return new Promise((resolve, reject) => {
               let reason = '';
               let query = "select reason from holiday_calender where date =" + scheduled_delivery_date;
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(result).length > 0) {
                         resolve(result)
                    } else {
                         resolve(reason)
                    }
               })
          })
     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}


/**
 * purpose : This function is used to fetch cart details based on cart_id 
 * request : cart_id
 * return : return  Cart detailes,
 * author : Deepak Tiwari
 */
module.exports.Cartdetails = function (data) {
     try {
          console.log("data", data)
          return new Promise((resolve, reject) => {
               let query = "select c.cart_id,c.user_id,c.product_id,c.rate,p.mrp, c.quantity, c.dit_quantity, c.total_price as prodtotal,p.sku, c.le_wh_id, c.le_wh_id_list, c.is_slab, c.hub_id as hub, c.esu_quantity,c.parent_id, c.star, c.product_slab_id, c.prmt_det_id, c.esu, c.freebee_qty, c.freebee_mpq, c.discount_type, c.discount, c.discount_on from cart as c  left join users as us ON us.user_id = c.user_id left join products as p ON p.product_id = c.product_id  where c.cart_id =" + data.cartId + " && c.status = 1  group by c.product_id"
               db.query(query, {}, function (err, response) {
                    console.log(query)
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(response).length > 0) {
                         resolve(response)
                    } else {
                         console.log("cart is empty")
                         let result = '';
                         resolve(result)
                    }
               })
          })

     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}


/*
 *   For: getShippingAddress function is used to check if the customer_token passed when the customer is logged in is valid.
 *   Author: Deepak Tiwari
 *   Request params parameters: customer_token, legal_entity_id
 *   Returns: Return shipping address and date of delivery
 */
exports.getShippingAddress = function (address_id, legal_entity_id) {
     try {
          return new Promise((resolve, reject) => {
               if (address_id != '') {
                    let query = "select lew.legal_entity_id, lew.contact_name as Firstname, lew.address1 as Address, lew.address2 as Address1 ,lew.phone_no as telephone, lew.city as City, lew.pincode as pin, z.name as state, coun.name as country, lew.email  from legalentity_warehouses as lew left join countries as coun ON coun.country_id  = lew.country  left join zone as z ON z.zone_id = lew.state where lew.le_wh_id =" + address_id + "  group by lew.le_wh_id";
                    db.query(query, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(result).length > 0) {
                              resolve(result)
                         } else {
                              resolve('')
                         }
                    })
               } else {

                    let query = "select le.legal_entity_id, le.business_legal_name, user.firstname as Firstname, le.address1 as Address, le.address2 as Address1, user.mobile_no as telephone, le.locality, le.landmark, le.city as City, le.pincode as pin, z.name as state from legal_entities as le left join users as user ON user.legal_entity_id = le.legal_entity_id left join countries as coun ON coun.country_id  = le.country  left join zone as z ON z.zone_id = le.state_id  where le.legal_entity_id =" + legal_entity_id;
                    db.query(query, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(result).length > 0) {
                              resolve(result)
                         } else {
                              resolve('')
                         }
                    })
               }
          })
     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }


}


/*
 *   For: function used to get AreaId details based on legal_entity_id.
 *   Author: Deepak Tiwari
 *   Request params parameters: legal_entity_id
 *   Returns: Return Area id
 */
module.exports.GetAreaID = function (legal_entity_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select area_id from customers where le_id = " + legal_entity_id;
               db.query(query, {}, function (err, rows) {
                    console.log("area_id", query)
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         resolve(rows[0].area_id);
                    } else {
                         resolve('');
                    }
               })
          })

     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }

}


/*
Purpose : getCustomerData function is used to get  the customer data of the customer_token passed 
author : Deepak Tiwari
Request : Require customer_token
Resposne : Returns user basic info .
*/
module.exports.getCustomerData = function (customer_token) {
     try {
          return new Promise((resolve, reject) => {
               let string = JSON.stringify(customer_token)
               let data = "select legal_entity_id , mobile_no  FROM users WHERE password_token =" + string;
               db.query(data, {}, function (err, rows) {
                    if (err) {
                         reject(err);
                    }
                    else if (Object.keys(rows).length > 0) {
                         let response = rows[0];
                         let legal_entity_id = response.legal_entity_id;
                         let mobile_no = response.mobile_no;
                         let master_query = "select * from master_lookup where value = 78002";
                         db.query(master_query, {}, function (err, rows_1) {
                              if (err) {
                                   return reject(err);
                              }
                              else if (Object.keys(rows_1).length > 0) {
                                   let desc = rows_1[0].description;
                                   let is_primary;
                                   //let details = "CALL get_user_details( " + mobile_no + ',' + desc + ")";
                                   let details = "SELECT u.firstname AS firstname, u.lastname,u.email_id as email , u.profile_picture AS documents, le.business_legal_name AS company, le.legal_entity_id AS address_id, le.address1 AS address_1, le.address2 AS address_2, IFNULL(le.locality, '') AS locality, IFNULL(le.landmark, '') AS landmark, le.city, coun.name, le.pincode AS postcode, u.mobile_no AS telephone, u.email_id AS email, le.business_type_id AS business_type, c.No_of_shutters, cp.city_id AS area_id, IFNULL(cp.officename, '') AS AREA, z.name AS state, c.volume_class, up.preference_value AS delivery_time, up.preference_value1 AS pref_value1, c.master_manf AS manufacturers, c.smartphone, c.dist_not_serv, c.is_icecream, c.facilities, up.sms_subscription AS sms_notification, c.is_visicooler, c.is_milk, c.is_deepfreezer,c.is_swipe , c.is_fridge, c.is_vegetables, up.business_start_time, up.business_end_time, c.network AS internet_availability, le.legal_entity_type_id AS buyer_type, c.beat_id, u.is_parent, le.gstin, le.arn_number, IFNULL(p.pdp, '') AS pdp, IFNULL(p.pdp_slot, '') AS pdp_slot, CASE  WHEN le.legal_entity_type_id = 3013  THEN 1 ELSE 0 END AS is_premium   FROM users AS u LEFT JOIN legal_entities AS le ON le.legal_entity_id = u.legal_entity_id LEFT JOIN customers AS c ON c.le_id = le.legal_entity_id  LEFT JOIN  cities_pincodes AS cp  ON cp.city_id = c.area_id  LEFT JOIN countries AS coun ON coun.country_id = le.country  LEFT JOIN  zone AS z ON z.zone_id = le.state_id  LEFT JOIN user_preferences AS up ON up.user_id = u.user_id LEFT JOIN  pjp_pincode_area AS p ON  p.pjp_pincode_area_id = c.beat_id WHERE u.mobile_no = " + mobile_no + " AND  z.country_id = 99 AND le.legal_entity_type_id IN(SELECT VALUE AS QUERY FROM master_lookup AS ml WHERE ml.mas_cat_id =" + desc + " && ml.is_active = 1) ";
                                   db.query(details, {}, function (err, row) {
                                        console.log("details", details)
                                        if (err) {
                                             return reject(err);
                                        }
                                        else if (Object.keys(row).length > 0) {
                                             console.log("row", row)
                                             let string1 = JSON.stringify(row[0])
                                             let json1 = JSON.parse(string1)
                                             let result = row[0]
                                             let DATA = "select count(lew.le_wh_id) as count from legalentity_warehouses as lew LEFT JOIN users as u ON u.mobile_no = lew.phone_no && u.legal_entity_id = lew.legal_entity_id  where u.password_token =" + string;
                                             db.query(DATA, {}, function (err, row_4) {
                                                  if (err) {
                                                       console.log(err);
                                                  } else if (Object.keys(row_4).length > 0) {
                                                       let data = [];
                                                       is_primary = row_4[0].count;
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
                                                            db.query(data1, {}, function (err, row_2) {
                                                                 if (err) {
                                                                      console.log(err);
                                                                 } else if (Object.keys(row_2).length > 0) {
                                                                      let string = JSON.stringify(row_2);
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
                                                                           result = result.concat(contact);
                                                                      } else {
                                                                           contact['contact_no1'] = '';
                                                                           contact['contact_name1'] = '';
                                                                           contact['contact_no2'] = '';
                                                                           contact['contact_name2'] = '';
                                                                           contact['user_id1'] = '';
                                                                           contact['user_id2'] = '';
                                                                           result = result.concat(contact);

                                                                      }
                                                                      let parentData = [];
                                                                      let parent_data = "select count(lew.phone_no) as count from users as u LEFT JOIN  legalentity_warehouses as lew ON   lew.phone_no = u.mobile_no where u.password_token =" + string;
                                                                      db.query(parent_data, {}, function (err, row_3) {
                                                                           if (err) {
                                                                                console.log(err)
                                                                           } else if (Object.keys(row_3).length > 0) {
                                                                                if (row_3[0].count > 0) {
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
                    } else {
                         resolve([]);
                    }
               });

          });

     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }

}


/**
 * purpose : function used to validate the Discounts before placing the Order 
 * request : cart_id
 * return : return  Cart detailes,
 * author : Deepak Tiwari
 */
module.exports.isDiscountApplicable = function (discount, discountOn, discountType, discountOnValues = null) {
     try {
          return new Promise((resolve, reject) => {
               let isDiscount = 0;
               let response;
               if (discountOnValues == null) {
                    let curDate = new Date();
                    let query = "select discount from customer_discounts where discount_on = " + discountOn + "  && discount_type = " + discountType + "  &&" + curDate + "between  discount_start_date and discount_end_date limit 1"
                    db.query(query, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              reject(err)
                         } else if (Object.keys(result).length > 0) {
                              response = typeof result[0] != 'undefined ' ? result[0] : result
                              if (typeof response.discount != 'undefined' && response.discount != discount) {
                                   resolve(false);
                              }

                              if (!typeof response.discount != 'undefined' && discount != null) {
                                   resolve(false);
                              }
                              resolve(true);
                         }
                    })
               } else {
                    let curDate = new Date();
                    let query = "select discount from customer_discounts where discount_on = " + discountOn + "  && discount_on_values = " + discountOnValues + " && discount_type = " + discountType + "  &&" + curDate + "between  discount_start_date and discount_end_date limit 1"
                    db.query(query, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              reject(err)
                         } else if (Object.keys(result).length > 0) {
                              response = typeof result[0] != 'undefined ' ? result[0] : result;
                              if (typeof response.discount != 'undefined' && response.discount != discount) {
                                   resolve(false);
                              }

                              if (!typeof response.discount != 'undefined' && discount != null) {
                                   resolve(false);
                              }
                              resolve(true)
                         }
                    })
               }
          })

     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }

}


/**
 * purpose : function used to get product packs
 * request : cart_id , product_id
 * return : return  Cart detailes,
 * author : Deepak Tiwari
 */
module.exports.getProdPacks = function (cart_id, product_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select cp.esu_quantity,cp.esu,cp.star,cp.pack_level,cp.pack_price,cp.pack_qty,cp.pack_cashback from cart_product_packs as cp where cp.cart_id =" + cart_id + " && cp.product_id =" + product_id;
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(result).length > 0) {
                         resolve(result)
                    } else {
                         resolve('')
                    }
               })
          })
     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}

/**
 * purpose : function used to Check whether the product is freebies or not
 * request :  product_id
 * return : return  item count,
 * author : Deepak Tiwari
 */
module.exports.isFreebie = function (product_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select count(*)  as count from freebee_conf where free_prd_id =" + product_id;
               db.query(query, {}, function (err, count) {
                    console.log("query idFreebie", query, count)
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (count[0].count > 0) {
                         resolve(count[0].count)
                    } else {
                         resolve(0)
                    }
               })
          })

     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}

/**
 * purpose : function used to do orders activity logs
 * request :  data
 * return :Insets data into orders_logs_api table,
 * author : Deepak Tiwari
 */
module.exports.OrderApiRequests = function (data) {
     try {
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
               dbo.collection('order_cp_logs').insertOne(body, function (err, res) {
                    if (err) throw err;
                    db.close();
               });
          });
     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }

}


/**
 * purpose : functionused to get server host URL from DB
 * request :  
 * return : return  server host url,
 * author : Deepak Tiwari
 */
module.exports.getHostURL = function () {
     try {
          return new Promise((resolve, reject) => {
               let query = "select key_value from mp_configuration where key_name = 'URL'";
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(result).length > 0) {
                         resolve(result[0].key_value);
                    } else {
                         resolve('');
                    }
               })
          })

     } catch (err) {
          console.log(err);
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}


/**
 * purpose : functiona used to update  po_so_status 
 * request :  po_id, order_code, status
 * return : return  updated po_so_status,
 * author : Deepak Tiwari
 */
module.exports.updateStockistOrderStatus = function (po_id, order_code, status = 0) {
     try {
          return new Promise((resolve, reject) => {
               let query = "update po set po_so_status =" + status + " , po_so_order_code = '" + order_code + "' where po_id =" + po_id
               db.query(query, {}, function (err, status) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(status);
                    }
               })
          })

     } catch (err) {
          console.log(err);
          let error = { 'status': 'failed', 'message': 'Internal server error' }
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

/**
 * purpose : function used to update  po_so_status 
 * request :  po_id, order_code, status
 * return : return  updated po_so_status,
 * author : Deepak Tiwari
 */
module.exports.FFLogsUpdate_new = function (data) {
     try {
          return new Promise((resolve, reject) => {
               let customer_id;
               let legal_entity_id;
               let ffIds;
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               console.log("data ===>851", data)
               let query = "select user_id ,legal_entity_id from users where password_token = '" + data.customer_token + "'";
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(result).length > 0) {
                         customer_id = result[0].user_id;
                         legal_entity_id = result[0].legal_entity_id
                    } else {
                         customer_id = 0;
                         legal_entity_id = 0;
                    }
                    //get ff_id based on sales_token
                    getcustomerId_model(data.sales_token).then(ff_id => {
                         if (ff_id != '') {
                              ffIds = ff_id;
                         } else {
                              ffIds = '';
                         }

                         //update ff_call_logs based on ff_id , user_id
                         let StartDate = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + 00 + ':' + 00 + ':' + 00;
                         let EndDate = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + 23 + ':' + 59 + ':' + 59;
                         let update_query = "update ff_call_logs set check_out_lat =" + data.latitude + ", check_out_long =" + data.longitude + ",activity = 107001 , check_out = '" + formatted_date + "' where ff_id =" + ffIds + " && user_id =" + customer_id + " && check_in IN ('" + StartDate + "','" + EndDate + "')";
                         db.query(update_query, {}, function (err, updated) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                              } else {
                                   console.log("Updated Successfully 916")
                              }
                         })
                    }).catch(err => {
                         console.log(err);
                    })

               })
          })

     } catch (err) {
          console.log(err);
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }
}


/**
 * purpose : FFlogsUpdate function is used to update FF logs
 * request :  sales_token , customer_token
 * return : return  updated po_so_status,
 * author : Deepak Tiwari
 */
module.exports.FFLogsUpdate = function (sales_token, customer_token) {
     try {
          return new Promise((resolve, reject) => {
               let customer_id;
               let legal_entity_id;
               let ffIds;
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               console.log("data ===>851", data)
               let query = "select user_id ,legal_entity_id from users where password_token = '" + customer_token + "'";
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(result).length > 0) {
                         customer_id = result[0].user_id;
                         legal_entity_id = result[0].legal_entity_id
                    } else {
                         customer_id = 0;
                         legal_entity_id = 0;
                    }
                    //get ff_id based on sales_token
                    getcustomerId_model(sales_token).then(ff_id => {
                         if (ff_id != '') {
                              ffIds = ff_id;
                         } else {
                              ffIds = '';
                         }

                         //inserting data into ff_call_logs
                         let insert_query = "insert into ff_logs (ff_id , user_id , activity , legal_entity_id , created_at)  value ('" + ffIds + "','" + customer_id + "','" + 107001 + "','" + legal_entity_id + "','" + formatted_date + "')";
                         db.query(insert_query, {}, function (err, inserted) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                              } else {
                                   console.log("Inserted Successfully 976")
                              }
                         })
                    }).catch(err => {
                         console.log(err);
                    })

               })
          })


     } catch (err) {
          console.log(err);
          let error = { 'status': 'failed', 'message': 'Internal server error' }
          return error;
     }

}

//used to check stocklist for fc -1014 , dc-1016
module.exports.checkStockist = function (legal_entity_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select count(*) as count from legal_entities where legal_entity_type_id IN( 1014 , 1016 ) && business_type_id = 47001 && legal_entity_id =" + legal_entity_id;
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(result).length > 0) {
                         resolve(result[0].count)
                    }
               })
          })
     } catch (err) {
          console.log(err);
          let error = { 'status': 'failed', 'message': 'Internal server error' };
          return error;
     }

}

//used to caculate user undelivered item
module.exports.custUnDeliveredOrderValue = function (cust_le_id) {
     try {
          return new Promise((resolve, reject) => {
               let data;
               if (cust_le_id > 0 && cust_le_id != '') {
                    let query = "select sum(go.total) as order_value,(select sum(cancel_value) from gds_cancel_grid where gds_cancel_grid.gds_order_id = go.gds_order_id) as cancel_value from`gds_orders` as `go` JOIN`gds_orders_payment` AS`gop` ON`gop`.`gds_order_id` = `go`.`gds_order_id` where`go`.`cust_le_id` = " + cust_le_id + " and go.order_status_id IN(17001, 17020, 17005, 17021, 17024, 17025, 17026, 17014) and  `gop`.`payment_method_id` = 22018 limit 1";
                    db.query(query, {}, function (err, result) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(result).length > 0) {
                              if (typeof result[0] != 'undefined') {
                                   console.log("undelivered value", result)
                                   data = result[0].order_value - result[0].cancel_value
                                   resolve(data)
                              } else {
                                   data = 0;
                                   resolve(data)
                              }

                         } else {
                              data = 0;
                              resolve(data)
                         }
                    })
               }
          })
     } catch (err) {
          console.log(err);
          let error = { 'status': 'failed', 'message': 'Internal server error' };
          return error;
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
                                                       db.query(data, {}, async function (err, rows) {
                                                            if (err) {
                                                                 return reject(err);
                                                            }
                                                            else if (Object.keys(rows).length > 0) {
                                                                 response = await userList.concat(rows);
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
     try {
          return new Promise((resolve, reject) => {
               let response = [];
               response.push(userId);
               if (userId > 0) {
                    getTeamList(userId).then((childUserList) => {
                         console.log("childUsrList", childUserList.length);
                         if (childUserList != null) {
                              for (let i = 0; i < childUserList.length; i++) {
                                   response = response.concat(childUserList[i].user_id);
                              }

                         }
                         console.log("response", response);
                         return resolve(response);
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
Purpose : getCustomerOrder function is used to view orders of customer 
author : Deepak Tiwari
Request : Require customerId, sales_rep_id, legal_entity_id, offset, offset_limit, status_id
Resposne : Return all available customer orders
*/
module.exports.getCustomerOrder = function (customerId, sales_rep_id, legal_entity_id, offset = 0, offset_limit, status_id) {
     console.log("sample request ", customerId)
     try {
          return new Promise((resolve, reject) => {
               let finalQuery;
               let legalEntityId;
               let userId;
               let query = "SELECT g.order_code,g.gds_order_id AS order_number,g.order_date as date,g.total AS total_amount , getMastLookupValue(g.order_status_id) AS order_status  FROM gds_orders AS g";
               if ((typeof sales_rep_id != 'undefined' && sales_rep_id != '') && (customerId == '')) {
                    finalQuery = query.concat(" where g.created_by IN (" + sales_rep_id + ")")
               } else {
                    if (legal_entity_id != '') {
                         finalQuery = query.concat(" where g.cust_le_id = " + legal_entity_id)
                         console.log("query", finalQuery)
                    } else {
                         let query_1 = "SELECT legal_entity_id from users where user_id=" + customerId;
                         db.query(query_1, {}, function (err, row) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                              } else if (Object.keys(row).length > 0) {
                                   legalEntityId = row[0].legal_entity_id;
                                   let query_2 = "SELECT user_id from users  where legal_entity_id=" + legalEntityId;
                                   db.query(query_2, {}, function (err, response) {
                                        if (err) {
                                             console.log(err);
                                             reject(err);
                                        } else if (Object.keys(response).length > 0) {
                                             userId = JSON.parse(JSON.stringify(response))
                                             console.log("userid ", userId);
                                             let str = userId.join();
                                             console.log("str ===>1287", str);
                                             finalQuery = query.concat(" left join gds_customer as gc  ON g.gds_cust_id = gc.gds_cust_id where FIND_IN_SET(gc.mp_user_id," + str + ")")
                                             console.log("query", finalQuery)
                                        }
                                   })
                              }
                         })

                    }
               }

               //order details based on order status
               if (typeof status_id != 'undefined ' && status_id != '') {
                    if (status_id == 17001) {
                         finalQuery = finalQuery.concat(" && g.order_status_id IN(17001)");
                    } else if (status_id == 17007) {
                         finalQuery = finalQuery.concat(" && g.order_status_id IN(17007 , 17023)");
                    } else if (status_id == 17022) {
                         finalQuery = finalQuery.concat(" && g.order_status_id IN(17022)");
                    } else if (status_id == 17009) {
                         finalQuery = finalQuery.concat(" && g.order_status_id IN(17009 , 17015 , 17017)");
                    }
               }

               let tempCount = finalQuery;
               let total;
               db.query(tempCount, {}, function (err, rows) {
                    if (err) {
                         console.log(err);
                         reject(err);

                    } else {
                         total = rows.length;
                         if (offset_limit != '') {
                              finalQuery = finalQuery.concat(" order by g.created_at DESC limit " + offset + ',' + offset_limit)
                              db.query(finalQuery, {}, function (err, result) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);
                                   } else if (Object.keys(result).length > 0) {
                                        let response = { 'Result': result, 'totalOrderCount': total }
                                        resolve(response);
                                   } else {
                                        resolve('')
                                   }
                              })

                         } else {
                              finalquery = finalQuery.concat(" order by g.created_at DESC")
                              db.query(finalQuery, {}, function (err, result) {
                                   if (err) {
                                        console.log(err);
                                        reject(err);

                                   } else if (Object.keys(result).length > 0) {
                                        let response = { 'Result': result, 'totalOrderCount': total }
                                        resolve(response);
                                   } else {
                                        resolve('')
                                   }
                              })
                         }
                    }
               })


          })

     } catch (err) {
          console.log(err);
          let error = { 'status': 'failed', 'message': 'Internal server error' };
          return error;
     }




}

//used to get order status based on orderId
module.exports.GetOrderstatus = function (order_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "SELECT product_id,order_status FROM gds_order_products  where gds_order_id = " + order_id;
               db.query(query, {}, function (err, result) {
                    if (err) {
                         console.log(err);
                         reject(err);

                    } else if (Object.keys(result).length > 0) {
                         console.log("response", result);
                         let response = JSON.parse(JSON.stringify(result))
                         resolve(response);
                    }
               })
          })

     } catch (err) {
          console.log(err)
     }
}


/*
Purpose : functionality used to get order code against Orderid 
author : Deepak Tiwari
Request : Require order_id
Resposne : Return all available customer orders
*/
module.exports.getOrderCode = function (order_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select order_code from gds_orders where gds_order_id =" + order_id;
               db.query(query, {}, function (err, rows) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (Object.keys(rows).length > 0) {
                         resolve(rows[0]);
                    }
               })
          })

     } catch (err) {
          console.log(err);
     }

}

/*
Purpose : functionality used to get order history 
author : Deepak Tiwari
Request : Require oorder_id, data, order_status, orderStatusId
Resposne : Return order details
*/
module.exports.orderhistory = function (order_id, data, order_status, orderStatusId) {
     try {
          return new Promise((resolve, reject) => {
               var MongoClient = require('mongodb').MongoClient;
               var host = 'mongodb://' + process.env['MONGO_USER'] + ":" + process.env['MONGO_PASSWORD'] + "@" + process.env['MONGO_HOST'] + ":" + process.env['MONGO_PORT'] + "/" + process.env['MONGO_DATABASE'];
               MongoClient.connect(host, { useNewUrlParser: true, useUnifiedTopology: true }, function (err, db) {
                    if (err) throw err;
                    console.log("connection")
                    var dbo = db.db("ebutor");
                    let current_datetime = new Date();
                    let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
                    let parameters;
                    if (orderStatusId == '17001') {
                         Object.keys(data).filter(value => {
                              parameters = {
                                   'order_id': parseInt(order_id),
                                   'product_id': parseInt(data[value].product_id),
                                   'order_status_id': parseInt(order_status_id),
                                   'quanity': parseInt(data[value].quantity),
                                   'total': parseInt(data[value].prodtotal),
                                   'order_status': order_status,
                                   'date_added': formatted_date
                              }
                         })
                         dbo.collection('orderhistory').insertOne(parameters, function (err, res) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                                   db.close()
                              } else {
                                   resolve(res)
                              }
                         });

                    } else if (orderStatusId == '17009') {
                         Object.keys(data).filter(value => {
                              parameters = {
                                   'order_id': parseInt(order_id),
                                   'product_id': parseInt(data[value].product_id),
                                   'order_status_id': parseInt(order_status_id),
                                   'quanity': parseInt(data[value].quantity),
                                   'cancel_reason_id': parseInt(data[value].cancel_reason_id),
                                   'order_status': order_status,
                                   'comments': parseInt(data[value].comments),
                                   'date_added': formatted_date
                              }
                         })
                         dbo.collection('orderhistory').insertOne(parameters, function (err, res) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                                   db.close()
                              } else {
                                   resolve(res)
                              }
                         });


                    } else if (orderStatusId == '17010') {
                         Object.keys(data).filter(value => {
                              parameters = {
                                   'order_id': parseInt(order_id),
                                   'product_id': parseInt(data[value].product_id),
                                   'order_status_id': parseInt(order_status_id),
                                   'quanity': parseInt(data[value].quantity),
                                   'total': parseInt(data[value].prodtotal),
                                   'order_status': order_status,
                                   'return_reason_id': parseInt(data[value].returnreasonid),
                                   'comments': parseint(data[value].comments),
                                   'date_added': formatted_date
                              }
                         })
                         dbo.collection('orderhistory').insertOne(parameters, function (err, res) {
                              if (err) {
                                   console.log(err);
                                   reject(err);
                                   db.close()
                              } else {
                                   resolve(res)
                              }
                         });
                    }
               });
          });
     } catch (err) {
          console.log(err);
     }
}


/*
Purpose :functionality used to view product details based on  order id & product id 
author : Deepak Tiwari
Request : Require order_id, product_id
Resposne : Return all available customer orders
*/
module.exports.getProdetails = function (order_id, product_id) {
     try {
          console.log("product_id ", product_id);
          console.log("order_id", order_id);
          return new Promise((resolve, reject) => {
               if (typeof product_id != 'undefined' && product_id != '') {
                    let query = "select gds.product_id,gds.gds_order_id,gds.qty,gds.sku,gds.pname from gds_order_products as gds where gds.gds_order_id =" + order_id + " && gds.product_id =" + product_id;
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(rows).length > 0) {
                              resolve(rows);
                         }
                    })
               } else {
                    let query = "select gds.product_id,gds.gds_order_id,gds.qty,gds.sku,gds.pname from gds_order_products as gds where gds.gds_order_id =" + order_id;
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              console.log(err);
                              reject(err);
                         } else if (Object.keys(rows).length > 0) {
                              resolve(rows);
                         }
                    })
               }
          })
     } catch (err) {
          console.log(err);
     }

}


/*
Purpose :functionality used to validate order_id and product_id 
author : Deepak Tiwari
Request : Require order_id, product_id
Resposne : Return all available customer orders
*/
module.exports.valOrderProd = function (order_id, product_id) {
     try {
          return new Promise((resolve, reject) => {
               if (typeof product_id != 'undefined' && product_id != '') {
                    let query = "select count(go.gds_order_id) as count  from gds_orders as go left join  gds_order_products as gop ON gop.gds_order_id = go.gds_order_id where go.gds_order_id =" + order_id + " && gop.product_id =" + product_id;
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              console.log(err);
                         } else if (Object.keys(rows).length > 0) {
                              resolve(rows[0].count);
                         }
                    })
               } else {
                    let query = "select count(go.gds_order_id) as count  from gds_orders as go left join  gds_order_products as gop ON gop.gds_order_id = go.gds_order_id where go.gds_order_id =" + order_id;
                    db.query(query, {}, function (err, rows) {
                         if (err) {
                              console.log(err);
                         } else if (Object.keys(rows).length > 0) {
                              resolve(rows[0].count);
                         }
                    })
               }
          })
     } catch (err) {
          console.log(err)
     }
}

//used to get order return reasons from master_look_up based on cat_id = 59
module.exports.returnReasons = function () {
     return new Promise((resolve, reject) => {
          try {
               let query = "select ml.master_lookup_id as id,ml.master_lookup_name as name,ml.value from master_lookup as ml  where ml.is_active = 1 && ml.mas_cat_id = 59 Order by ml.sort_order ASC"
               sequelize.query(query).then(rows => {
                    let json = JSON.parse(JSON.stringify(rows[0]))
                    if (rows.length > 0) {
                         resolve(json);
                    } else {
                         resolve(json)
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

//used to get order cancel reasons from master_look_up based on mas_cat_id = 60
module.exports.returnCancelReasons = function () {
     return new Promise((resolve, reject) => {
          try {
               let query = "select ml.master_lookup_id as id,ml.master_lookup_name as name,ml.value from master_lookup as ml left join master_lookup_categories as mlc ON mlc.mas_cat_id =  ml.mas_cat_id where mlc.is_active = 1  && ml.mas_cat_id  = 60"
               sequelize.query(query).then(rows => {
                    let json = JSON.parse(JSON.stringify(rows[0]))
                    if (rows.length > 0) {
                         resolve(json);
                    } else {
                         resolve(json)
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
//used to get reference_code for serial  number table based on prefix and stateCode
function getReferenceCode(prefix, stateCode) {
     return new Promise((resolve, reject) => {
          try {
               let referenceNumber
               const mutex = new Mutex();//npm library used to resolve concurrent access resource problem
               mutex
                    .acquire()// function is used acquire lock on resource(it works on obect and return release reference  )
                    .then(function (release) {
                         if (prefix != "" && stateCode != "") {
                              let query = "SELECT CONCAT(state_code,prefix,DATE_FORMAT(CURDATE(), '%y'),LPAD(MONTH(CURDATE()), 2, '0'),LPAD(serial_numbers.`reference_id`,serial_numbers.`length`,0)) AS ref_no FROM serial_numbers WHERE serial_numbers.`state_code` = '" + stateCode + "' AND serial_numbers.`prefix` = '" + prefix + "' LIMIT 1 ";
                              sequelize.query(query).then(result => {
                                   let response = JSON.parse(JSON.stringify(result[0]))
                                   let updateQuery = "update serial_numbers set reference_id = reference_id + 1 where  state_code ='" + stateCode + "' && prefix = '" + prefix + "'";
                                   sequelize.query(updateQuery).then(updated => {
                                        referenceNumber = typeof response[0].ref_no != 'undefined' ? response[0].ref_no : '';
                                        resolve(referenceNumber);
                                   }).catch(err => {
                                        console.log(err);
                                        release();
                                        reject(err);
                                   })
                              }).catch(err => {
                                   console.log(err);
                                   release();
                                   reject(err);
                              })
                         } else {
                              referenceNumber = "";
                              resolve(referenceNumber);
                         }
                    });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
//used ot get refCode for order we were using in from 'SO'
function getRefCode(prefix, stateId = '4033') {
     return new Promise((resolve, reject) => {
          try {
               let response = '';
               if (prefix != '') {
                    let query = "select code from zone where zone_id = " + stateId;
                    sequelize.query(query).then(code => {
                         let stateCode = JSON.parse(JSON.stringify(code[0]))
                         let finalStateCode = typeof stateCode[0].code ? stateCode[0].code : "TS";
                         response = getReferenceCode(prefix, finalStateCode);
                         resolve(response)
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

/*
Purpose :functional used to get order Ref
author : Deepak Tiwari
Request : Require le_wh_id
Resposne : Return all available orders ref
*/
module.exports.generateOrderRef = function (le_wh_id) {
     return new Promise((resolve, reject) => {
          try {
               let query = " select state  from legalentity_warehouses  where le_wh_id =" + le_wh_id;
               sequelize.query(query).then(state => {
                    console.log("state ===> 1640", state);
                    let stateDetails = JSON.parse(JSON.stringify(state[0]));
                    console.log("stateDetails", stateDetails);
                    if (stateDetails.length < 0) {
                         resolve(false);
                    } else {
                         let stateId = stateDetails[0].state;
                         let type = 'SO';//
                         getRefCode(type, stateId).then(orderCode => {
                              resolve(orderCode);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }
               }).catch(err => {
                    console.log(err);
               })
          } catch (err) {
               console.log(err);
          }
     })

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
                    return resolve(random_number);

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
          sequelize.query(data).then(rows => {
               let result = JSON.parse(JSON.stringify(rows[0]))
               if (result.length > 0) {
                    resolve({ 'status': 'success', 'otp': result[0].otp })
               } else {
                    resolve({ 'status': 'failed', 'message': "Please generate otp" })
               }
          }).catch(err => {
               console.log(err);
          })
     })
}
//used to get all order status from db
module.exports.getFilterOrderStatus = function () {
     return new Promise((resolve, reject) => {
          try {
               let query = " select value,master_lookup_name from master_lookup where is_active =1  && value in(17001, 17022, 17009, 17007)  order by sort_order ASC"
               sequelize.query(query).then(rows => {
                    let json = JSON.parse(JSON.stringify(rows[0]))
                    if (json != '') {
                         resolve(json);
                    } else {
                         resolve(json)
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
//used to get tax percentage based on product_id 
function getTaxPercentageOnProduct(productId) {
     return new Promise((resolve, reject) => {
          try {
               let data = "select sum(tax) as tax_percentage, tax_class, tax.SGST, tax.CGST, tax.IGST, tax.UTGST from gds_orders_tax as tax where tax.gds_order_prod_id = " + productId;
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    resolve(rows[0])
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
//used to get porduct actualEsp
function getProductByOrderIdProductIdFromActualEsp(orderId, productId) {
     return new Promise((resolve, reject) => {
          try {
               let data = "select product.gds_order_prod_id, product.actual_esp*product.qty as total, product.actual_esp*product.qty as cost, product.cost/product.qty as actual_cost, product.qty, gds_orders.firstname, gds_orders.lastname, gds_orders.currency_id,gds_orders.shop_name,gds_orders.discount_before_tax from gds_order_products as product join gds_orders ON gds_orders.gds_order_id = product.gds_order_id where product.product_id =" + productId + " And product.gds_order_id =" + orderId;
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    resolve(rows[0])
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
//used to get product landing price
function getProductByOrderIdProductIdForLp(orderId, productId) {
     return new Promise(async (resolve, reject) => {
          try {
               let product;
               let data = "SELECT count(*) as count  from gds_invoice_items WHERE product_id =" + productId + " And gds_order_id =" + orderId + " And qty > 0 ";
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(async (rows) => {
                    if (rows[0].count > 0) {
                         let query = "SELECT product.product_id AS gds_order_prod_id, product.row_total_incl_tax AS total, product.row_total AS cost,go_product.cost/go_product.qty AS actual_cost, product.qty, gds_orders.firstname,gds_orders.lastname, gds_orders.currency_id, gds_orders.shop_name,gds_orders.discount_before_tax  from gds_invoice_items as product left join  gds_order_products as go_product ON go_product.product_id =  product.product_id  And go_product.gds_order_id = product.gds_order_id join gds_orders ON gds_orders.gds_order_id = product.gds_order_id where product.product_id =" + productId + " And product.gds_order_id =" + orderId;
                         sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(proDetails => {
                              product = proDetails[0];
                              resolve(product);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         product = await getProductByOrderIdProductIdFromActualEsp(orderId, productId);
                         resolve(product);
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
//used to get product details based on product_id and order_id 
function getProductByOrderIdProductId(orderId, productId) {
     return new Promise((resolve, reject) => {
          try {
               let data = " SELECT product.*, gds_orders.firstname, gds_orders.lastname, gds_orders.currency_id, gds_orders.shop_name FROM gds_order_products AS product JOIN gds_orders ON gds_orders.gds_order_id = product.gds_order_id WHERE product.product_id =" + productId + " And product.gds_order_id =" + orderId;
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    resolve(rows[0])
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
//used to get tax percentage from gds_orders_tax based on order_id and product_id.
function getTaxPercentageBasedOnOrderId(orderId, productId) {
     return new Promise((resolve, reject) => {
          try {
               let data = "select sum(tax) as tax_percentage,tax_class,tax.SGST,tax.CGST,tax.IGST,tax.UTGST from gds_orders_tax as tax where tax.gds_order_id =" + orderId + " and tax.product_id = " + productId;
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    resolve(rows[0])
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
//used to get unit price for each product
function getSingleUnitPrice(total, taxPercentage, qty) {
     return new Promise((resolve, reject) => {
          try {
               var singleUnitPrice = (total / (100 + parseFloat(taxPercentage)) * 100) / qty;
               resolve(singleUnitPrice)
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
//used to get unit price for each returned product with tax.
module.exports.getUnitPriceWithTax = (orderId, productId) => {
     return new Promise(async (resolve, reject) => {
          try {
               let taxPer;
               let taxClass;
               let SGST;
               let CGST;
               let IGST;
               let UTGST;
               let taxDetails = await getTaxPercentageBasedOnOrderId(orderId, productId);
               let product = await getProductByOrderIdProductIdForLp(orderId, productId);
               if (taxDetails == '' || taxDetails.tax_percentage == '') {
                    let taxPerObject = await getTaxPercentageOnProduct(prodcut.gds_order_prod_id);
                    taxPer = taxPerObject.tax_percentage;
                    taxClass = taxPerObject.tax_class;
                    SGST = taxPerObject.SGST;
                    CGST = taxperObject.CGST;
                    IGST = taxperObject.IGST;
                    UTGST = taxPerObject.UTGST;
               } else {
                    taxPer = taxDetails.tax_percentage;
                    taxClass = taxDetails.tax_class;
                    SGST = taxDetails.SGST;
                    CGST = taxDetails.CGST;
                    IGST = taxDetails.IGST;
                    UTGST = taxDetails.UTGST;
               }
               var singleUnitPrice = await getSingleUnitPrice(product.total, taxPer, product.qty);
               let singleUnitPriceWithtax = ((taxPer / 100) * singleUnitPrice) + singleUnitPrice;
               let singleUnitPriceBeforeTax = singleUnitPrice;
               if (product.discount_before_tax == 1) {
                    singleUnitPriceBeforeTax = product.actual_cost;
               }
               let result = { 'singleUnitPrice': singleUnitPrice, 'singleUnitPriceWithtax': singleUnitPriceWithtax, 'singleUnitPriceBeforeTax': singleUnitPriceBeforeTax, 'tax_percentage': taxPer, 'tax_class': taxClass, 'SGST': SGST, 'CGST': CGST, 'IGST': IGST, 'UTGST': UTGST };
               resolve(result);
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}
//used to get warehouse details based on le_wh_id.
function getLeWhDetailsById(leWhId) {
     return new Promise((resolve, reject) => {
          try {
               let data = "SELECT zone.code AS state_code FROM  legalentity_warehouses AS warehouses JOIN legal_entities AS legal ON warehouses.legal_entity_id = legal.legal_entity_id LEFT JOIN zone ON zone.zone_id = warehouses.state WHERE warehouses.le_wh_id =" + leWhId;
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    resolve(rows[0])
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
//used to get stateCode based on orderId
module.exports.getStateCode = (orderId) => {
     return new Promise((resolve, reject) => {
          try {
               let data = "select le_wh_id as whareHouseID from gds_orders where gds_order_id =" + orderId;
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(async (rows) => {
                    let whId = typeof rows[0].whareHouseID != 'undefined' ? rows[0].whareHouseID : '';
                    let whDetails = await getLeWhDetailsById(whId);
                    let stateCode = typeof whDetails.state_code != 'undefined' ? whDetails.state_code : "TS";
                    resolve(stateCode);
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })

          } catch (err) {
               cosnole.log(err);
               reject(err);

          }
     })
}
//used to save return grid details
module.exports.saveReturnGrid = (data, userId) => {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let query = "insert into sales_return_request(gds_order_id , return_order_code,total_return_value,total_return_items,total_return_item_qty,approval_status,return_status_id,created_at,updated_at,created_by) values (?,?,?,?,?,?,?,?,?,?)";
               db.query(query, [data.gds_order_id, data.reference_no, data.total_return_value, data.total_return_items, data.total_return_item_qty, data.approval_status, data.return_status_id, date, date, userId], function (err, res) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(res.insertId);
                    }
               });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
//used to save return details
module.exports.saveReturn = (data, userId = NULL, pricedata = []) => {
     return new Promise(async (resolve, reject) => {
          try {
               let price;
               if (pricedata.length == 0) {
                    price = await this.getUnitPriceWithTax(data.gds_order_id, data.product_id);
               } else {
                    price = pricedata;
               }
               let tax_amount = ((price.singleUnitPrice * price.tax_percentage) / 100) * data.qty;
               let refcode = data.reference_no;
               /** GST tax calculation */

               /**Tax percentage */
               let sgstPer = typeof price.SGST !== 'undefined' ? price.SGST : 0;
               let cgstPer = typeof price.CGST !== 'undefined' ? price.CGST : 0;
               let igstPer = typeof price.IGST !== 'undefined' ? price.IGST : 0;
               let utgstPer = typeof price.UTGST !== 'undefined' ? price.UTGST : 0;
               /**Tax amount */
               let SGSTVal = (tax_amount * sgstPer) / 100;
               let CGSTVal = (tax_amount * cgstPer) / 100;
               let IGSTVal = (tax_amount * igstPer) / 100;
               let UTGSTVal = (tax_amount * utgstPer) / 100;
               /*---Amount calculation---*/
               let tax_amt = price.singleUnitPriceWithtax - price.singleUnitPrice;
               let sub_total = price.singleUnitPrice * data.qty;
               let total = price.singleUnitPriceWithtax * data.qty;
               let tax_class_id = price.tax_class;
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               /**unitprice -> with tax , sub_total-- without tax , total-- with tax */
               let query = "insert into return_request_products(product_id , gds_order_id, reference_no,return_reason_id, return_status_id,return_by,return_qty,unit_price,tax_per,tax_amt,tax_total,sub_total,total,SGST,CGST,IGST,UTGST, approved_quantity,return_request_id ,approval_status,approved_by_user,created_by, created_at,updated_at,tax_class_id) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
               db.query(query, [data.product_id, data.gds_order_id, refcode, data.return_reason_id, data.return_status_id, userId, data.qty, price.singleUnitPriceWithtax, price.tax_percentage, tax_amt, tax_amount, sub_total, total, SGSTVal, CGSTVal, IGSTVal, UTGSTVal, 0, data.return_request_id, data.approval_status, data.approved_by_user, userId, date, date, tax_class_id], function (err, res) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(res.insertId);
                    }
               });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
//used to update gst in sales_return_grid
module.exports.updateGstOnReturnGrid = (orderId) => {
     return new Promise((resolve, reject) => {
          try {
               let data = "select SUM(SGST) AS totSGST, SUM(CGST) AS totCGST ,SUM(IGST) AS totIGST,SUM(UTGST) AS totUTGST from return_request_products where gds_order_id=" + orderId;
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    if (rows.length > 0) {
                         let response = rows[0];
                         let updateQuery = "update sales_return_request set cgst_total =" + response.totCGST + " , sgst_total =" + response.totSGST + " , igst_total =" + response.totIGST + " , utgst_total = " + response.totUTGST + " where gds_order_id =" + orderId;
                         sequelize.query(updateQuery).then(updated => {
                              resolve(updated);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         resolve();
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
//used to get OrderDetail based on orderid
module.exports.getOrderInfoByOrderId = (orderId) => {
     return new Promise((resolve, reject) => {
          try {
               let data = "SELECT * FROM gds_orders WHERE gds_order_id =" + orderId;
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    resolve(rows)
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
//used to get reference_no
module.exports.getrefCode = (prefix, stateCode = 'TS') => {
     return new Promise((resolve, reject) => {
          try {
               let response = '';
               if (prefix != '') {
                    response = getReferenceCode(prefix, stateCode);
                    resolve(response)
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}
//used to track order return status
module.exports.getReturnDetailsBasedOnOrderId = (orderId, productId) => {
     return new Promise((resolve, reject) => {
          try {
               let data = "select returns.product_id,sum(return_qty) as returned from sales_return_request as grid  join return_request_products as returns on(grid.sales_return_request_id = returns.return_request_id  and grid.gds_order_id = returns.gds_order_id) where grid.gds_order_id =" + orderId + " and returns.product_id = " + productId + "  AND grid.return_status_id NOT IN(57230,57231) group by returns.product_id";
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    if (rows.length > 0) {
                         resolve(rows)
                    } else {
                         let row = [{ 'returned': 0 }]
                         resolve(row)
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
//used to get invoice details based on orderId
function getAllInvoiceGridByOrderId(orderId) {
     return new Promise((resolve, reject) => {
          try {
               let data = "SELECT grid.`gds_invoice_grid_id`, item.`product_id`,product.sku, product.product_title, sum(item.qty) as qty from  gds_invoice_grid AS grid INNER JOIN gds_order_invoice AS invoice ON(grid.gds_invoice_grid_id = invoice.gds_invoice_grid_id)INNER JOIN gds_invoice_items AS item ON(invoice.`gds_order_invoice_id` = item.`gds_order_invoice_id`) LEFT JOIN products AS product ON(item.product_id = product.product_id) WHERE grid.`gds_order_id` =" + orderId + " group by(item.product_id)";
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    resolve(rows)
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
//Used to get return details based on orderId
function getReturnedByOrderId(orderId) {
     return new Promise((resolve, reject) => {
          try {
               let data = "select returns.product_id,sum(return_qty) as returned from sales_return_request as grid  join return_request_products  as returns on(grid.sales_return_request_id = returns.return_request_id  and grid.gds_order_id = returns.gds_order_id) where grid.gds_order_id =" + orderId + "  group by returns.product_id";
               sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                    resolve(rows)
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
//used to update orderstatus in gds_order_product
function updateReturnStatusProductRows(productId, orderId, statusId) {
     return new Promise((resolve, reject) => {
          try {
               let data = "update gds_order_products set order_status =" + statusId + " where gds_order_id =" + orderId + " and product_id=" + productId;
               sequelize.query(data).then(rows => {
                    resolve(rows);
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
//used to update order status in gds_orders table
function updateOrderStatusById(orderId, statusId, userId) {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let data = "update gds_orders set order_status_id =" + statusId + " , updated_at ='" + date + "' ,updated_by =" + userId + " where gds_order_id =" + orderId;
               sequelize.query(data).then(rows => {
                    resolve(rows);
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
//used to update orderstatus after return
module.exports.updateReturnOrderStatusonOrderId = (orderId, refcode, userId) => {
     return new Promise(async (resolve, reject) => {
          try {
               let invoicedItems = await getAllInvoiceGridByOrderId(orderId);
               let returnedItems = await getReturnedByOrderId(orderId);
               let productArray = {};
               //returned 
               if (returnedItems.length > 0) {
                    returnedItems.forEach((value) => {
                         productArray[value.product_id] = { 'returned': parseInt(value.returned), 'invoiced': 0 };
                    })
               }
               //invoiced
               invoicedItems.forEach((invoiced) => {
                    let returnQuantity = 0;
                    if (typeof productArray[invoiced.product_id] != 'undefined') {
                         if (typeof productArray[invoiced.product_id].returned != 'undefined') {
                              returnQuantity = productArray[invoiced.product_id].returned;
                         } else {
                              returnQuantity = 0;
                         }
                         productArray[invoiced.product_id] = { 'returned': returnQuantity, 'invoiced': parseInt(invoiced.qty) };
                    } else {
                         productArray[invoiced.product_id] = { 'returned': returnQuantity, 'invoiced': parseInt(invoiced.qty) };
                    }
               })
               let completeDeliveryCount = 0;
               let PartialReturnCount = 0;
               let FullReturnCount = 0;
               let productKeys = Object.keys(productArray);
               const promises = productKeys.map(async (key) => {
                    /**Full return */
                    if (productArray[key].invoiced == productArray[key].returned) {
                         console.log("----2302----")
                         let query = "select product_id,order_status from gds_order_products where gds_order_id =" + orderId + " and product_id =" + productArray[key].product_id;
                         sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                              rows.forEach((checkstatus) => {
                                   let orderStatus = checkstatus.order_status;
                                   let productId = checkstatus.product_id;
                                   if (orderStatus == 17009 || orderStatus == 17015) {
                                        updateReturnStatusProductRows(key, orderId, orderStatus);
                                   } else {
                                        FullReturnCount += 1;
                                        updateReturnStatusProductRows(key, orderId, '17022');
                                   }
                              })
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                         /**Complete delived */
                    } else if (productArray[key].invoiced > 0 && productArray[key].returned == 0) {
                         completeDeliveryCount += 1;
                         updateReturnStatusProductRows(key, orderId, '17007');
                    } else {
                         console.log("----2325-----");
                         PartialReturnCount += 1;
                         updateReturnStatusProductRows(key, orderId, '17023');
                    }

               })
               const Resolvepromise = await Promise.all(promises)
               /* Updating orderstatus in gds_orders */
               if (PartialReturnCount > 0 && FullReturnCount > 0) {
                    updateOrderStatusById(orderId, '17023', userId);
                    resolve('17023');
               } else if (FullReturnCount > 0 && completeDeliveryCount > 0) {
                    updateOrderStatusById(orderId, '17023', userId);
                    resolve('17023');
               } else if (PartialReturnCount > 0 && completeDeliveryCount > 0) {
                    updateOrderStatusById(orderId, '17023', userId);
                    resolve('17023');
               } else if (PartialReturnCount > 0) {
                    updateOrderStatusById(orderId, '17023', userId);
                    resolve('17023');
               } else {
                    updateOrderStatusById(orderId, '17022', userId);
                    resolve('17022');
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}
//used to cancel return request
module.exports.canCelReturnRequest = (userId, orderId) => {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let data = "update return_request_products set return_status_id =57230 , approval_status =57230  ,updated_by =" + userId + " , updated_at ='" + date + "' where gds_order_id =" + orderId + " and  approval_status NOT IN(57227)";
               sequelize.query(data).then(rows => {
                    let data = "update sales_return_request set return_status_id = 57230  , approval_status =57230 , updated_by =" + userId + " , updated_at ='" + date + "' where gds_order_id =" + orderId + " and  approval_status NOT IN(57227)";
                    sequelize.query(data).then(updated => {
                         resolve(updated);
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

//used to enable the flag  for cancel return request
module.exports.updatetOrderReturnRejectFlag = (orderId) => {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let data = "update gds_orders set is_return_cancelable =1 , is_returnable =2  where gds_order_id =" + orderId;
               sequelize.query(data).then(rows => {
                    resolve(rows);
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

//used to update id_return_Cancelable flag after return cancel
module.exports.updateReturnCancelableFlag = (orderId) => {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let data = "update gds_orders set is_return_cancelable =0 , is_returnable =1  where gds_order_id =" + orderId;
               sequelize.query(data).then(rows => {
                    resolve(rows);
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

//used to validate delivery token
module.exports.checkDeliveryToken = (deliveryToken) => {
     return new Promise((resolve, reject) => {
          try {
               let count = 0;
               if (typeof deliveryToken != 'undefined' && deliveryToken != '') {
                    let data = "select verifyToken('" + deliveryToken + "') as count";
                    sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                         count = typeof rows[0].count != 'undefined' ? rows[0].count : '';
                         resolve(count);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    resolve(count);
               }
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//Used to get Delivery Executive based on delivery token
module.exports.getDeliveryExecutive = (deliveryToken) => {
     return new Promise((resolve, reject) => {
          try {
               let userId = 0;
               if (typeof deliveryToken != 'undefined' && deliveryToken != '') {
                    let data = "select user_id from users where lp_token = '" + deliveryToken + "'";
                    sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                         userId = typeof rows[0].user_id != 'undefined' ? rows[0].user_id : 0;
                         resolve(userId);
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    resolve(userId);
               }
          } catch (err) {
               console.log(err);
               resolve(userId);
          }
     })

}

//Used to get return request 
module.exports.getReturnRequest = (UserId) => {
     return new Promise((resolve, reject) => {
          try {
               if (typeof UserId != 'undefined' && UserId != '') {
                    let data = "SELECT firstname AS customerName, shop_name AS shopName,le.longitude , le.latitude  , le.address1 , le.city,le.city,le.state_id ,sr.sales_return_request_id,sr.sales_return_request_id,sr.gds_order_id,sr.return_order_code,sr.total_return_value,sr.total_return_items,sr.total_return_item_qty,sr.return_status_id,sr.approval_status,sr.delivery_executive_id  , sr.created_by , sr.created_at ,sr.updated_by , sr.updated_at FROM sales_return_request  AS sr JOIN gds_orders  ON sr.`gds_order_id` = gds_orders.gds_order_id JOIN legal_entities  AS le ON le.legal_entity_id =gds_orders.cust_le_id  WHERE  sr.delivery_executive_id =" + UserId + " AND sr.approval_status IN (57226)";
                    sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                         if (rows.length > 0) {
                              resolve(rows);
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
               console.log(err);
               resolve(userId);
          }
     })

}


//Used to get return request product details based orderId and DE id
module.exports.returnRequestProducts = (UserId, orderId) => {
     return new Promise((resolve, reject) => {
          try {
               if ((typeof UserId != 'undefined' && UserId != '') && (typeof orderId != 'undefined' && orderId != '')) {
                    let data = "SELECT rp.product_id, p.product_title ,p.mrp , rp.return_id,rp.gds_order_id,rp.reference_no,rp.return_reason_id,rp.return_qty,rp.return_request_id,rp.unit_price,rp.tax_per,rp.tax_amt,rp.sub_total,rp.tax_total,rp.total,rp.approval_status,rp.tax_class_id FROM return_request_products AS rp JOIN products AS p ON rp.product_id = p.product_id  WHERE  return_request_id  IN (SELECT sales_return_request_id FROM sales_return_request AS sr WHERE sr.gds_order_id =" + orderId + " AND sr.approval_status IN (57226) AND sr.delivery_executive_id =" + UserId + ")";
                    sequelize.query(data, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                         if (rows.length > 0) {
                              resolve(rows);
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
               console.log(err);
               resolve(userId);
          }
     })

}

//used to update save return grid after pickup
module.exports.updateReturnGrid = (data, userId) => {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let query = "update sales_return_request set approval_status =" + data.approval_status + " , total_picked_qty =" + data.total_picked_item_qty + " , updated_by =" + userId + " , updated_at ='" + date + "'  where delivery_executive_id =" + userId + " AND gds_order_id =" + data.gds_order_id + " AND sales_return_request_id =" + data.return_request_id;
               db.query(query, function (err, res) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(res);
                    }
               });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to update return request product after pickup
module.exports.updateSaveReturn = (data, userId) => {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let query = "update return_request_products set approval_status =" + data.approval_status + " , picked_qty =" + data.picked_qty + " , updated_by =" + userId + " , updated_at ='" + date + "'  where return_request_id  = " + data.return_request_id + " AND gds_order_id =" + data.gds_order_id + " AND product_id =" + data.product_id;
               db.query(query, function (err, res) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(res);
                    }
               });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

//used to get sales_return_request_id based on orderId
module.exports.getReturnRequestId = (orderId) => {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let query = "SELECT sales_return_request_id AS return_id FROM sales_return_request  where gds_order_id =" + orderId;
               db.query(query, function (err, res) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(res[0].return_id);
                    }
               });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}


//used to update id_return_Cancelable flag after pickUp
module.exports.updateCancelableFlag = (orderId) => {
     return new Promise((resolve, reject) => {
          try {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let data = "update gds_orders set is_return_cancelable =0  where gds_order_id =" + orderId;
               sequelize.query(data).then(rows => {
                    resolve(rows);
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

