'user strict';

const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;
const Sequelize = require('sequelize');
var userModel = require('../../schema/users');
const cartModel = require('../../schema/cart')
var sequelize = require('../../../config/sequelize');//sequlize connection file
var cache = require('../../../config/redis');//rediscache connection file 
var moment = require('moment');//used to return date in required format
const user = userModel(sequelize, Sequelize);
const cart = cartModel(sequelize, Sequelize);
var nodemailer = require("nodemailer");
const mongoose = require('mongoose');
const userMongo = mongoose.model('User');






/*
purpose :Used to insert user cart details.  
request :productId, leWhId, segmentId, totalQty, customerId
resposne  : will add new cartDetails
Author : Deepak tiwari
*/
function inventoryRequest(productId, leWhId, segmentId, totalQty, customerId) {
     try {
          console.log("hello", productId)
          if (productId > 0) {
               let data = {
                    'le_wh_id': leWhId,
                    'product_id': productId,
                    'segment_id': segmentId,
                    'total_qty': totalQty,
                    'customer_id': customerId
               }
               let query = "insert into inventory_request( product_id  , le_wh_id , segment_id , total_qty , customer_id) values('" + productId + "','" + leWhId + "','" + segmentId + "','" + totalQty + "','" + customerId + "')";
               console.log("query", query)
               sequelize.query(query).then(result => {
                    if (result.length > 0) {
                         console.log("Inventory_request inserted Successfully")
                    }
               }).catch(err => {
                    console.log(err);
               })
          }
     } catch (err) {
          console.log(err);
          console.log(err.message);
     }
}
/*
purpose : addcart function is used to check quanity of product and return avail qty & status.  
request : product_id, quantity, wh_id, segmentId, cust_data
resposne  : will add new cart 
Author : Deepak tiwari
*/
module.exports.addCart = function (product_id, quantity, wh_id, segmentId, cust_data) {
     try {
          return new Promise((resolve, reject) => {
               let customerId = cust_data.customer_id;
               let customerType = cust_data.customer_type;
               let data = {};
               let availableQuantity;
               let query = " SELECT GetCPInventoryStatus( " + product_id + ',' + wh_id + ',' + segmentId + ',' + " 4  ) as le_wh_id "
               sequelize.query(query).then(result => {
                    if (result.length > 0) {
                         let res = JSON.parse(JSON.stringify(result[[0]]));
                         let le_wh_id = res[0].le_wh_id;
                         if ((le_wh_id == 0) || le_wh_id == '') {
                              data = { 'status': 0, 'product_id': product_id, 'available_quantity': 0 };
                         } else {
                              if (customerType != 3016) {
                                   let query = "select (dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty from inventory where product_id =" + product_id + " && le_wh_id =" + le_wh_id;
                                   sequelize.query(query).then(availableInventory => {
                                        if (availableInventory.length > 0) {
                                             let availableQty = JSON.parse(JSON.stringify(availableInventory[[0]]))
                                             availableQuantity = availableQty[0].availQty;
                                             if (quantity > availableInventory) {
                                                  data = Object.assign(data, {
                                                       'status': 0,
                                                       'product_id': product_id,
                                                       'available_quantity': availableQuantity
                                                  })
                                                  inventoryRequest(product_id, wh_id, segmentId, quantity, customerId);
                                             } else {
                                                  data = Object.assign(data, {
                                                       'status': 1,
                                                       'product_id': product_id,
                                                       'available_quantity': availableQuantity
                                                  })
                                                  resolve(data);
                                             }

                                        } else {
                                             resolve('');
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })

                              } else {
                                   console.log("======>96", le_wh_id)
                                   let query_1 = "select inv_display_mode from inventory  where product_id =" + product_id + " && le_wh_id =" + le_wh_id;
                                   sequelize.query(query_1).then(checkInventory => {
                                        if (checkInventory.length > 0) {
                                             let Inventory = JSON.parse(JSON.stringify(checkInventory[[0]]))
                                             let displaymode = Inventory[0].inv_display_mode;
                                             console.log("=====>68", displaymode);
                                             let query_2 = " select (" + displaymode + " - (order_qty+reserved_qty)) as availQty from inventory where product_id =" + product_id + " && le_wh_id =" + le_wh_id;
                                             sequelize.query(query_2).then(availableInventory => {
                                                  if (availableInventory.length > 0) {
                                                       let availableQty = JSON.parse(JSON.stringify(availableInventory[[0]]))
                                                       availableQuantity = availableQty[0].availQty;
                                                       console.log(availableQuantity, quantity)
                                                       if (quantity > availableInventory) {
                                                            console.log("if condition")
                                                            data = Object.assign(data, {
                                                                 'status': 0,
                                                                 'product_id': product_id,
                                                                 'available_quantity': availableQuantity
                                                            })
                                                            inventoryRequest(product_id, wh_id, segmentId, quantity, customerId);
                                                       } else {
                                                            data = Object.assign(data, {
                                                                 'status': 1,
                                                                 'product_id': product_id,
                                                                 'available_quantity': availableQuantity
                                                            })
                                                            resolve(data);
                                                       }
                                                  } else {
                                                       resolve('');
                                                  }
                                             }).catch(err => {
                                                  console.log("======>err", err)
                                             })
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })
                              }
                         }
                    } else {
                         resolve('');
                    }
               }).catch(err => {
                    console.log(err);
                    return reject(err);
               })
          })
     } catch (err) {
          console.log(err);
     }
}


/*
purpose :function is used to log the cart details into mongodb
request : cart details
resposne  :Will log cart details into mongodb
Author : Deepak tiwari
*/
module.exports.logApiRequests = function (data) {
     console.log("log", data.apiurl);
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
               if (typeof data.apiurl != 'undefined') {
                    apiUrl = data.apiurl;
               }
               if (typeof data.parameter != 'undefined') {
                    parameters = data.parameters;
               }
               let body = {
                    apiUrl: data.apiurl,
                    parameters: data.parameters,
                    created_at: moment().format("YYYY-MM-DDTHH:mm:ss")
               }
               dbo.collection('container_api_logs').insertOne(body, function (err, res) {
                    if (err) throw err;
                    db.close();
               });
          });
     });
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
          });
     } catch (err) {
          console.log(err)
     }
}

/**
 * purpose : Used to validate relation between dc and hub
 * request : salesUserId, customerLegalEntityId
 * return : return  data if valid,
 * author : Deepak Tiwari
 */
module.exports.checkValidRelation = function (salesUserId, customerLegalEntityId) {
     try {
          return new Promise((resolve, reject) => {
               if (salesUserId != '') {
                    let query = " CALL getFFRetailerCheckIn( " + salesUserId + ',' + customerLegalEntityId + ")";
                    sequelize.query(query).then(validFfRelation => {
                         if (validFfRelation != '' && typeof validFfRelation[0].AGGREGATE && validFfRelation[0].AGGREGATE > 0) {
                              resolve({ 'status': 'success', 'data': [] });
                         } else {
                              resolve({ 'status': 'failed' })
                         }
                    }).catch(err => {
                         reject(err);
                    })
               } else {
                    //For Self Orders 
                    let result;
                    let query = "SELECT dhm.dc_id,dhm.hub_id FROM retailer_flat AS rf LEFT JOIN wh_serviceables AS wh ON wh.pincode = rf.pincode AND wh.legal_entity_id = rf.parent_le_id JOIN legalentity_warehouses AS lw ON lw.legal_entity_id = rf.parent_le_id AND rf.hub_id = lw.le_wh_id LEFT JOIN dc_hub_mapping AS dhm ON dhm.hub_id = rf.hub_id WHERE rf.legal_entity_id = " + customerLegalEntityId;
                    sequelize.query(query).then(validSFRelation => {
                         if (validSFRelation != '') {
                              result = { 'status': 'success', 'data': validSFRelation };
                              resolve(result)
                         } else {
                              let data = { 'display': 0 }
                              result = { "status": "failed", "message": "Improper Dc and Hub Configuration for the retailer or field force", "data": data }
                              resolve(result);
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

/**
 * purpose : Used to validate relation between dc and hub
 * request : le_wh_id, hub_id
 * return : return  data if valid,
 * author : Deepak Tiwari
 */
module.exports.checkDCHubMapping = function (le_wh_id, hub_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select dc_hub_map_id, is_active from dc_hub_mapping  from dc_id =" + le_wh_id + " && hub_id = " + hub_id;
               sequelize.query(query).then(response => {
                    if (response != '') {
                         resolve(response);
                    } else {
                         resolve('');
                    }
               })
          })
     } catch (err) {
          console.log(err);
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
               user.findOne({ where: { password_token: token }, attributes: ['user_id'] }).then(rows => {
                    if (rows != '' && rows != null) {
                         customerId = JSON.parse(JSON.stringify(rows)).user_id;
                         console.log("=========>customerId", customerId);
                         return resolve(customerId);
                    } else {
                         customerId = 0;
                         return resolve(customerId);
                    }
               }).catch(err => {
                    console.log(err);
                    return reject(err);
               })
          })
     } catch (err) {
          console.log(err)
          let error = { 'status': 'failed', 'message': 'Internal server error' };
          return (error);
     }
}

//used to legalEntityId based on userId
function getLegalEntityId(userId) {
     try {
          let legalEntityId = 0;
          return new Promise((resolve, reject) => {
               console.log("====>322", userId);
               if (userId != '') {
                    let data = "select legal_entity_id from users where user_id =" + userId;
                    sequelize.query(data).then(legalEntityData => {
                         if (legalEntityData[0].length > 0) {
                              legalEntityId = typeof legalEntityData[0] != 'undefined' ? legalEntityData[0].legal_entity_id : 0;
                              console.log("======>legalEntityId", legalEntityId)
                              resolve(legalEntityId);
                         } else {
                              resolve(0);
                         }
                    }).catch(err => {
                         console.log(err);
                         reject(err);
                    })
               } else {
                    resolve(0);
               }

          })

     } catch (err) {
          console.log(err)
     }

}

/**
 * purpose :Used to get customer type based on legal_entity_id
 * request : legal_entity_id
 * return : return  customer type,
 * author : Deepak Tiwari
 */
function getUserCustomerType(legal_entity_id) {
     try {
          return new Promise((resolve, reject) => {
               if (legal_entity_id != '') {
                    let query = "select legal_entity_type_id from legal_entities from legal_entity_id =" + legal_entity_id
                    sequelize.query(query).then(rows => {
                         console.log("query", query);
                         if (rows.length > 0) {
                              console.log("====>362", rows)
                              resolve(rows[0].legal_entity_type_id)
                         } else {
                              resolve(0)
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               } else {
                    resolve(0)
               }
          })

     } catch (err) {
          console.log(err)
     }
}

/**
 * purpose : Used to delete cart
 * request : le_wh_id, hub_id
 * return : return  data if valid,
 * author : Deepak Tiwari
 */
module.exports.deletecart = function (data) {
     try {
          console.log("data ===>", data)
          return new Promise((resolve, reject) => {
               if (data.isClearCart == 'true') {
                    console.log("=========>if condition")
                    this.getcustomerId(data.customer_token).then(userId => {
                         console.log("user_id", userId)
                         let query = "delete from cart where user_id =" + userId + " &&  status =  1";
                         sequelize.query(query).then(result => {
                              console.log("result ==>339", result);
                              if (result != '') {
                                   let query = "delete from cart_product_packs where user_id =" + userId + " && status = 1";
                                   sequelize.query(query).then(response => {
                                        if (response != '') {
                                             return resolve(response);
                                        } else {
                                             return resolve({ 'status': 'failed', 'message': 'Unable to refresh your cart .Please try later' });
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        return reject(err);
                                   })
                              } else {
                                   return resolve({ 'status': 'failed', 'message': 'Unable to refresh your cart .Please try later' });
                              }
                         }).catch(err => {
                              console.log(err);
                              return reject(err);
                         })
                    }).catch(err => {
                         console.log(err)
                         return reject(err);
                    })
               } else {
                    console.log("=========>else condition")
                    let query = "delete from cart where cart_id =" + data.cartId;
                    sequelize.query(query).then(result => {
                         console.log("result ==>361", result);
                         if (result != '') {
                              let query = "delete from cart_product_packs where cart_id =" + data.cartId;
                              sequelize.query(query).then(response => {
                                   console.log("result ==>361", result);
                                   if (response != '') {
                                        return resolve(response);
                                   } else {
                                        return resolve({ 'status': 'failed', 'message': 'Unable to refresh your cart .Please try later' });
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   return reject(err);
                              })
                         } else {
                              return resolve({ 'status': 'failed', 'message': 'Unable to refresh your cart .Please try later' });
                         }
                    }).catch(err => {
                         console.log(err);
                         return reject(err);
                    })

               }
          })
     } catch (err) {
          let error = { 'status': 'failed', 'message': err.message }
          console.log(err);
          return error;

     }
}

/**
 * purpose : function used to Check whether the product is freebies or not
 * request :  product_id
 * return : return  item count,
 * author : Deepak Tiwari
 */
function isFreebie(product_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select count(*)  as count from freebee_conf where free_prd_id =" + product_id;
               db.query(query, {}, function (err, count) {
                    console.log("query idFreebie", query)
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else if (count > 0) {
                         resolve(count)
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

/*
 * purpose : function is used  to get the product ids for which free prods not available
 * request : cartdata, wh_id, hub, segmentId, token, customerId, sales_token = '', cust_type
 * return : return  product Id whose isFreeBie is true
 * author : Deepak Tiwari
 */
function checkFreeProds(cartdata, wh_id, segmentId, token, customerId, customer_type = '') {
     return new Promise((resolve, reject) => {
          console.log("customer_type", customer_type);
          try {
               let productFreeNotAvail = [];
               cartdata.forEach((cd) => {
                    let productId = cd.product_id;
                    let parentId = cd.parent_id;
                    let quantity = cd.total_qty;
                    let totalPrice = cd.total_price;
                    let availableInventory;
                    let availableQuantity;
                    let checkInventory;
                    let error = {};
                    let query = "select GetCPInventoryStatus(" + productId + "," + wh_id + "," + segmentId + " , 4) as le_wh_id";
                    sequelize.query(query).then(wareHouseId => {
                         if (wareHouseId.length > 0) {
                              let warehouse = JSON.parse(JSON.stringify(wareHouseId[[0]]))
                              let le_wh_id = warehouse[0].le_wh_id;
                              if (le_wh_id == 0 || le_wh_id == '') {
                                   productFreeNotAvail.push(parentId);
                              } else {
                                   if (customer_type == 3016) {//clearance 
                                        let query_1 = " select (dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty from inventory where product_id =" + productId + "  && le_wh_id =" + le_wh_id;
                                        sequelize.query(query_1).then(availableIn => {
                                             if (availableIn.length > 0) {
                                                  availableInventory = JSON.parse(JSON.stringify(availableIn[0]));;
                                             } else {
                                                  error = { 'status': 'failed', 'message': 'Inventory not available' }
                                                  return resolve(error);
                                             }
                                             console.log("quantity , availableInventory", quantity, availableInventory)
                                             availableQuantity = availableInventory[0].availQty;
                                             if ((quantity) > availableQuantity) {//comparing weather available quantity is greater then  total_oty or not
                                                  productFreeNotAvail = parentId;
                                             } else if ((totalPrice == '' || totalPrice == 0) && productId == parentId) {
                                                  productFreeNotAvail = productId;
                                             }
                                             return resolve(productFreeNotAvail);
                                        }).catch(err => {
                                             console.log(err);
                                             return reject(err);
                                        })

                                   } else {
                                        //for other customer_type firts we were select display_mode for other 
                                        let query_3 = "select inv_display_mode from inventory  where product_id =" + productId + "  && le_wh_id =" + le_wh_id;
                                        sequelize.query(query_3).then(checkInv => {
                                             console.log("checkin ===>564", checkInv)
                                             let checkInven = JSON.parse(JSON.stringify(checkInv[[0]]));
                                             if (checkInv.length > 0) {
                                                  checkInventory = checkInven;
                                             } else {
                                                  //inventory not found for this product_id
                                                  error = { 'status': 'failed', 'message': 'Inventory not available' }
                                                  return reject(error);
                                             }

                                             let displayMode = checkInventory[0].inv_display_mode;
                                             let query_4 = " select ( " + displayMode + "-(order_qty+reserved_qty)) as availQty from inventory where product_id=" + productId + " && le_wh_id = " + le_wh_id;
                                             sequelize.query(query_4).then(availableIn => {
                                                  if (availableIn.length > 0) {
                                                       availableInventory = JSON.parse(JSON.stringify(availableIn[0]));
                                                  } else {
                                                       error = { 'status': 'failed', 'message': 'Inventory not available' }
                                                       return reject(error);
                                                  }
                                                  console.log("quantity , availableInventory", quantity, availableInventory[0].availQty, totalPrice, productId, parentId)
                                                  availableQuantity = availableInventory[0].availQty;
                                                  if ((quantity) > availableQuantity) {//case when required quantity is more then available quantity
                                                       productFreeNotAvail.push(parentId);
                                                       console.log("562", productFreeNotAvail)
                                                  } else if ((totalPrice == '' || totalPrice == 0) && productId == parentId) {//case when total
                                                       console.log("566", productFreeNotAvail)
                                                       productFreeNotAvail.push(productId);
                                                  }
                                                  console.log("productFreeNotAvail", productFreeNotAvail);
                                                  return resolve(productFreeNotAvail);
                                             }).catch(err => {
                                                  console.log(err);
                                                  return reject(err);
                                             })

                                        }).catch(err => {
                                             console.log(err);
                                             return reject(err);
                                        })
                                   }
                              }
                         } else {
                              return resolve('');
                         }

                    }).catch(err => {
                         console.log(err);
                         return reject(err);
                    })
               })

          } catch (err) {
               console.log(err);
               return reject(err);
          }
     })
}

//used to get masterLookup value based on description
function getMasterValue(desc) {
     console.log("desc ====>598", desc);
     return new Promise((resolve, reject) => {
          try {
               let query = " select value from master_lookup where description ='" + desc + "'";
               sequelize.query(query).then(description => {
                    let desc = JSON.parse(JSON.stringify(description[0]))
                    console.log("604", desc[0].value, typeof description[0])
                    if (typeof description[0] == 'object') {
                         return resolve(desc[0].value);
                    } else {
                         return resolve(0);
                    }
               }).catch(err => {
                    console.log(err);
                    return reject(err);
               })
          } catch (err) {
               console.log(err);
               let response = { 'status': "failed", 'message': "Internal server error" };
               return resolve(response);
          }
     })

}

//used to get packPrice  
function getPackPrice(qty, packSizeArr) {
     let packSize = [];
     return new Promise((resolve, reject) => {
          try {
               if (typeof packSizeArr[qty] != 'undefined') {
                    return resolve(packSizeArr[qty]);
               } else {
                    packSize = packSizeArr.reverse();
                    for (let i = 0; i < Object.keys(packSize).length > 0; i++) {
                         if (qty > Object.keys(packSize[i])) {
                              resolve(packSize[i]);
                         }
                         break;
                    }
               }
          } catch (err) {
               console.log(err.message);
               return reject(err);
          }
     })

}

//used to DC based on hub_id
function getDCByHub(hub_id) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select dc_hub_map_id , dc_id , is_active from dc_hub_mapping  where hub_id =" + hub_id;
               sequelize.query(query).then(result => {
                    console.log("result=====>662", result);
                    let response = JSON.parse(JSON.stringify(result[0]));
                    if (result[0].length > 0) {
                         resolve(response[0]);
                    } else {
                         reject('')
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

//used to get unique value from array
const unique = (value, index, self) => {
     return self.indexOf(value) === index
}

//used to get userDetails
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

//used to checkcart Inventory based on customerType
function cartInventory(cartdata, wh_id, hubId, segmentId, token, customerId, sales_token = '', customerType) {
     return new Promise((resolve, reject) => {
          try {
               let sizeOfCart = cartdata.length;
               let productFreeItems;
               let removeProduct = [];
               let inventoryArray = [];
               let le_wh_id;
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
               //checking for free product available 
               checkFreeProds(cartdata, wh_id, segmentId, token, customerId, customerType).then(freeProductAvailable => {
                    if (freeProductAvailable != '') {
                         productFreeItems = freeProductAvailable;
                    } else {
                         productFreeItems = '';
                    }
                    for (let i = 0; i < sizeOfCart; i++) {
                         let esuQuantity = (typeof cartdata[i].esu_quantity != 'undefined' && cartdata[i].esu_quantity != '') ? cartdata[i].esu_quantity : '';
                         let margin = (typeof cartdata[i].applied_margin != 'undefined' && cartdata[i].applied_margin != '') ? cartdata[i].applied_margin : '';
                         let totalPrice = parseFloat((typeof cartdata[i].total_price != 'undefined' && cartdata[i].total_price != '') ? cartdata[i].total_price : 0.00);
                         let totalQty = (typeof cartdata[i].total_qty != 'undefined' && cartdata[i].total_qty != '') ? cartdata[i].total_qty : '';
                         let rate = (typeof cartdata[i].unit_price != 'undefined' && cartdata[i].unit_price != '') ? cartdata[i].unit_price : '';
                         let productId = cartdata[i].product_id;
                         let parentId = cartdata[i].parent_id;
                         let quantity = cartdata[i].total_qty;
                         let is_slab = (typeof cartdata[i].is_slab != 'undefined' && cartdata[i].is_slab != '') ? cartdata[i].is_slab : 0;
                         let star = (typeof cartdata[i].star != 'undefined' && cartdata[i].star != '') ? cartdata[i].star : '';
                         let productSlabId = (typeof cartdata[i].product_slab_id != 'undefined' && cartdata[i].product_slab_id != '') ? cartdata[i].product_slab_id : 0;
                         let prmt_det_id = (typeof cartdata[i].prmt_det_id != 'undefined' && cartdata[i].prmt_det_id != '') ? cartdata[i].prmt_det_id : 0;
                         let freebee_mpq = (typeof cartdata[i].freebee_mpq != 'undefined' && cartdata[i].freebee_mpq != '') ? cartdata[i].freebee_mpq : 0;
                         let freebee_qty = (typeof cartdata[i].freebee_qty != 'undefined' && cartdata[i].freebee_qty != '') ? cartdata[i].freebee_qty : 0.0;
                         let esu = (typeof cartdata[i].esu != 'undefined' && cartdata[i].esu != '') ? cartdata[i].esu : 0;
                         let packs = (typeof cartdata[i].packs != 'undefined' && cartdata[i].packs != '') ? cartdata[i].packs : [];
                         let credit_limit = (typeof cartdata[i].credit_limit != 'undefined' && cartdata[i].credit_limit != '') ? cartdata[i].credit_limit : 0;
                         let data = {};
                         let packSizePrice;
                         let availableQuantity;
                         let displaymode;
                         //checking available inventory for selected product
                         let query = "select  GetCPInventoryStatus(" + productId + "," + wh_id + ", " + segmentId + ",4) as le_wh_id";
                         sequelize.query(query).then(wareHouseId => {
                              console.log("====822=====", wareHouseId)
                              if (wareHouseId != '') {
                                   let warehouse = JSON.parse(JSON.stringify(wareHouseId[[0]]))
                                   le_wh_id = warehouse[0].le_wh_id;
                              } else {
                                   le_wh_id = '';
                              }
                              //fetching master value 
                              if (star != '') {
                                   getMasterValue(star).then(Star => {
                                        if (Star != '') {
                                             star = Star;
                                        } else {
                                             star = '';
                                        }
                                        console.log("star", star)
                                   }).catch(err => {
                                        console.log(err);
                                        return reject(err);
                                   })
                              }
                              console.log("star", star, hubId, packs);
                              let hub = (hubId != '' && typeof hubId != 'undefined') ? hubId : 0;

                              //packs validation
                              if (packs == '') {
                                   data = { 'status': -5, 'cartId': '', 'product_id': productId, 'le_wh_id': wh_id, 'hub_id ': hub, 'available_quantity': 0 };
                                   removeProduct = parentId;

                              }
                              let wrongUnitPrice = false;
                              let isFreeBie = 0;
                              //checking free item available for particular productid in  freebee_conf table
                              isFreebie(productId).then(freeBie => {
                                   console.log("freeBie", freeBie)
                                   if (freeBie != '') {
                                        isFreeBie = freeBie;
                                   } else {
                                        isFreeBie = 0;
                                   }
                                   /*-------------------------------Discount validation------------------------------------*/
                                   //Discount only for self orders(available only when retailer Itself placing order)
                                   let isDiscountValid = true;
                                   let isDiscountApplicable = false;
                                   let discount;
                                   let discountType;
                                   let discountOn;
                                   let discountOnValue;
                                   let discountAmount;
                                   let isDiscount;
                                   let checkUnitPrice;
                                   let unitpriceData;
                                   let tempDetailes = [];
                                   if (sales_token == '' && !isFreeBie) {//only for self order with no freeBie available
                                        discount = (typeof cartdata[i].discount != 'undefined' && cartdata[i].discount != '') ? cartdata[i].discount : 0;
                                        discountType = (typeof cartdata[i].discount_type != 'undefined' && cartdata[i].discount_type != '') ? cartdata[i].discount_type : '';
                                        discountOn = (typeof cartdata[i].discount_on != 'undefined' && cartdata[i].discount_on != '') ? cartdata[i].discount_on : '';
                                        discountOnValue = (typeof cartdata[i].discount_on_values != 'undefined' && cartdata[i].discount_on_values != '') ? cartdata[i].discount_on_values : '';
                                        discountAmount = 0;
                                        isDiscount = 0;
                                        if (discount == 0 && discountType == '' && discountOn == '' && discountOnValue == '') {
                                             isDiscountApplicable = false;
                                        } else {
                                             //checking weather discount is valid or not 
                                             let query = "select discount  from customer_discount where discount_on =" + discountOn + " && discount_type =" + discountType + " &&  discount_on_values like %" + discountOnValue + "%  && discount_start_date =" + formatted_date + " && discount_end_date =" + formatted_date;
                                             sequelize.query(query).then(isdiscount => {
                                                  console.log("discount", isdiscount);
                                                  if (typeof isdiscount[0].discount != 'undefined' && isdiscount[0].discount != discount) {//in this case user already got some discount on product so we were removing freebie for his inventory
                                                       isDiscountValid = false;
                                                       data = { 'status': -4, 'cartId': '', 'product_id': productId, 'le_wh_id': wh_id, 'hub_id': hub, 'available_quantity': 0 };
                                                       removeProduct = productId;
                                                  }
                                                  console.log("880 isdiscountValid", isDiscountValid);
                                                  if (isDiscountValid) {
                                                       isDiscountApplicable = true;
                                                       if (discountType == 'percentage') {
                                                            discountAmount = discount * parseFloat(totalPrice) / 100;
                                                       } else if (discountType == 'value' && parseFloat(totalPrice) > discount) {
                                                            discountAmount = discount;
                                                            discountAmount = (discountAmount < 0) ? 0 : discountAmount;
                                                       }
                                                  } else {
                                                       //discount is not available
                                                       isDiscountApplicable = false;
                                                  }
                                             }).catch(err => {
                                                  console.log(err);
                                             })

                                        }
                                   }
                                   console.log("899 isdiscountValid", isDiscountApplicable);
                                   //le_wh_id is not null
                                   console.log("le_wh_id", le_wh_id);
                                   if ((le_wh_id == 0) || le_wh_id == '' || (wh_id == 0) || wh_id == '') {
                                        data = Object.assign(data, { 'status': 0, 'cartId': '', 'product_id': productId, 'le_wh_id': le_wh_id, 'hub_id': hub, 'available_quantity': 0 });
                                        inventoryRequest(productId, le_wh_id, segmentId, quantity, customerId);
                                   } else {
                                        if (customerType == 3016) {
                                             console.log("cutomertpe is 3016")
                                             let query_1 = "select (dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty from `inventory` where `product_id` = " + product_id + " && le_wh_id =" + le_wh_id;
                                             segmentId.query(query_1).then(queryResponse => {
                                                  if (queryResponse.length > 0) {
                                                       availableQuantity = JSON.parse(JSON.stringify(queryResponse[0]));
                                                  } else {
                                                       availableQuantity = 0;
                                                  }
                                             }).catch(err => {
                                                  console.log(err);
                                             })
                                        } else {
                                             console.log("cutomertype other then 3016")
                                             let query_2 = "select inv_display_mode from inventory  where product_id =" + productId + " && le_wh_id =" + le_wh_id;
                                             sequelize.query(query_2).then(CheckInventory => {
                                                  let checkInven = JSON.parse(JSON.stringify(CheckInventory[[0]]));
                                                  if (CheckInventory.length > 0) {
                                                       displaymode = checkInven[0].inv_display_mode;
                                                  }
                                                  let query_3 = "select ( " + displaymode + " - (order_qty+reserved_qty)) as availQty from `inventory` where `product_id` =" + productId + " && `le_wh_id` =" + le_wh_id;
                                                  sequelize.query(query_3).then(rows => {
                                                       if (rows.length > 0) {
                                                            availableQuantity = JSON.parse(JSON.stringify(rows[0]));
                                                       } else {
                                                            availableQuantity = '';
                                                       }
                                                       if ((quantity) > availableQuantity[0].availQty && (typeof productFreeItems != 'undefined' || (productFreeItems in productId) || (productFreeItems in parentId))) {
                                                            data = Object.assign(data, { 'status': 0, 'cartId': '', 'product_id': productId, 'le_wh_id': wh_id, 'hub_id': hub, 'available_quantity': availableQuantity[0].availQty });
                                                            inventoryRequest(productId, wh_id, segmentId, quantity, customerId);
                                                       } else {
                                                            console.log("else =======>806")
                                                            let checkUnitPrice = [];
                                                            if (productId == parentId || parentId == 0) {
                                                                 let appKeyData = process.env.DATABASE_NAME;
                                                                 console.log("wh_id", wh_id);
                                                                 let temp = wh_id.replace(/'/g, '');
                                                                 console.log("temp====>1035", temp)
                                                                 if (temp.length > 1) {
                                                                      temp = temp.replace(',', '_');
                                                                 }
                                                                 if (customerId == 0) {
                                                                      temp = 0;
                                                                 }
                                                                 let keyString = appKeyData + '_product_slab_' + productId + '_customer_type_' + customerType + '_le_wh_id_' + le_wh_id;
                                                                 //used to get data from redis cache
                                                                 cache.get(keyString, async function (error, rows) {
                                                                      let response = JSON.parse(rows);
                                                                      if (error) {
                                                                           console.log(error);
                                                                      } else if (typeof response != 'undefined' && response != null) {
                                                                           unitpriceData = await (response != '' ? response : []);
                                                                      }
                                                                      if (typeof unitpriceData[temp] != 'undefined' && unitpriceData[temp] != null) {
                                                                           checkUnitPrice = unitpriceData[temp]//unitpriceData.temp;
                                                                           if (typeof availableQuantity != 'undefined' && checkUnitPrice.lenght > 1) {
                                                                                checkUnitPrice.forEach((slabData) => {
                                                                                     if (typeof slabData.stock != 'undefined') {
                                                                                          slabData.stock = availableQuantity[0].availQty;
                                                                                     }
                                                                                     tempDetailes = slabData;
                                                                                })
                                                                           }
                                                                           if (tempDetailes != '') {
                                                                                checkUnitPrice = tempDetailes;
                                                                           }
                                                                           unitpriceData.temp = checkUnitPrice;
                                                                           unitpriceData = { [temp]: checkUnitPrice };
                                                                           cache.set(keyString, JSON.stringify(unitpriceData),
                                                                                function (error, added) {
                                                                                     if (error) {
                                                                                          console.log(error.message);
                                                                                     } else {
                                                                                          console.log("addedx=====>865", added);
                                                                                     }
                                                                                })

                                                                      } else {
                                                                           console.log("====else condition for case")
                                                                           let query_5 = "CALL getProductSlabsByCust( " + productId + ",'" + + wh_id + "'," + customerId + ',' + customerType + ")";
                                                                           sequelize.query(query_5).then(result => {
                                                                                unitpriceData = { [temp]: JSON.parse(JSON.stringify(result)) };
                                                                                cache.set(keyString, JSON.stringify(unitpriceData), function (error, done) {
                                                                                     if (error) {
                                                                                          console.log(error);
                                                                                     } else {
                                                                                          console.log("done=======>876", done);
                                                                                     }
                                                                                })
                                                                           }).catch(err => {
                                                                                console.log(err);
                                                                                return reject(err);
                                                                           })

                                                                      }

                                                                      if (typeof checkUnitPrice != 'undefined' && checkUnitPrice.length > 0) {
                                                                           // console.log("checkunit ===> 1095", JSON.parse(checkUnitPrice))
                                                                           let packSizeArr = [];
                                                                           checkUnitPrice.forEach((price) => {
                                                                                //review required
                                                                                packSizeArr.push({ [price.pack_size]: price.unit_price });
                                                                                console.log('packsize', packSizeArr)
                                                                                // packSizeArr = Object.assign(packSizeArr, { [price.pack_size]: price.unit_price });
                                                                           })

                                                                           if (typeof packSizeArr != 'undefined') {
                                                                                //fetch packPrice
                                                                                getPackPrice(quantity, packSizeArr).then(packPrice => {
                                                                                     if (packPrice != '') {
                                                                                          packSizePrice = packPrice;
                                                                                     } else {
                                                                                          packSizePrice = 0;
                                                                                     }
                                                                                     if ((!isFreeBie && checkUnitPrice.length == 0) || (typeof packSizePrice != 'undefined' && packSizePrice != rate)) {//!
                                                                                          data = Object.assign(data, {
                                                                                               'status': -1,
                                                                                               'cartId': 1,
                                                                                               'product_id': productId,
                                                                                               'le_wh_id': wh_id,
                                                                                               'hub_id': hub,
                                                                                               'available_quantity': availableQuantity[0].availQty,
                                                                                               'old_price': rate,
                                                                                               'new_price': packSizePrice
                                                                                          })
                                                                                          removeProduct = productId;
                                                                                     } else {
                                                                                          //check cart count based user login
                                                                                          let status = 1;
                                                                                          console.log("removeProduct", removeProduct)
                                                                                          if (removeProduct.hasOwnProperty(parentId)) {
                                                                                               if (wrongUnitPrice) {
                                                                                                    status = -2;//unit price mismatch
                                                                                               } else if (!isDiscountValid) {
                                                                                                    status = -4;//discount on available
                                                                                               } else if (pack == '') {
                                                                                                    status = -5;//pack is missing
                                                                                               } else {
                                                                                                    status = -1;//cart details not found
                                                                                                    cartId = '';
                                                                                               }

                                                                                          } else {
                                                                                               console.log("star =======>", star)
                                                                                               let lewhid;
                                                                                               let cartTableData;
                                                                                               let cartTableCount;
                                                                                               let cartArr = {};
                                                                                               let cartInsertId;
                                                                                               let dit_qty = 0;
                                                                                               let quantity_cart = 0;
                                                                                               let checkCartTable = "select count(cart_id) as cc,cart_id from `cart` where `product_id` =" + productId + " && `user_id` = " + customerId + " &&  `status` = 1";
                                                                                               sequelize.query(checkCartTable).then(cartTableResult => {
                                                                                                    getDCByHub(hub).then(whData => {
                                                                                                         console.log("whData", whData)
                                                                                                         lewhid = typeof whData.dc_id != 'undefined' ? whData.dc_id : 0
                                                                                                         cartTableData = JSON.parse(JSON.stringify(cartTableResult[0]));
                                                                                                         cartTableCount = cartTableData[0].cc;

                                                                                                         //Cart insertion
                                                                                                         if (cartTableCount == 0) {
                                                                                                              cartArr = {
                                                                                                                   'product_id': productId,
                                                                                                                   'user_id': customerId,
                                                                                                                   'session_id': token,
                                                                                                                   'esu_quantity': esuQuantity,
                                                                                                                   'esu': esu,
                                                                                                                   'parent_id': parentId,
                                                                                                                   'total_price': totalPrice,
                                                                                                                   'rate': rate,
                                                                                                                   'margin': margin,
                                                                                                                   'le_wh_id_list': toString(wh_id),
                                                                                                                   "le_wh_id": lewhid,
                                                                                                                   'hub_id': hub,
                                                                                                                   'created_at': formatted_date,
                                                                                                                   'star': star,
                                                                                                                   'is_slab': is_slab,
                                                                                                                   'prmt_det_id': prmt_det_id,
                                                                                                                   'product_slab_id': productSlabId,
                                                                                                                   'freebee_mpq': freebee_mpq,
                                                                                                                   'freebee_qty': freebee_qty,
                                                                                                                   'discount': ((sales_token == '') && isDiscountApplicable && !isFreebie) ? discount : 0,
                                                                                                                   'discount_type': ((sales_token == '') && isDiscountApplicable && !isFreebie) ? discountType : null,
                                                                                                                   'discount_on': ((sales_token == '') && isDiscountApplicable && !isFreebie) ? discountOn : '',
                                                                                                                   'discount_amount': ((sales_token == '') && isDiscountApplicable && !isFreebie) ? discountAmount : 0
                                                                                                              };
                                                                                                              if (customerType == 3016) {
                                                                                                                   cartArr = Object.assign(cartArr, {
                                                                                                                        'dit_quantity': totalQty
                                                                                                                   })
                                                                                                                   dit_qty = totalQty;
                                                                                                              } else {
                                                                                                                   cartArr = Object.assign(cartArr, {
                                                                                                                        'quantity': totalQty
                                                                                                                   })
                                                                                                                   quantity_cart = totalQty;
                                                                                                              }
                                                                                                              console.log("le_whID", lewhid)
                                                                                                              let Discount = ((sales_token == '') && isDiscountApplicable && !isFreebie) ? discount : 0;
                                                                                                              let DiscountType = ((sales_token == '') && isDiscountApplicable && !isFreebie) ? discountType : null;
                                                                                                              let DiscountOn = ((sales_token == '') && isDiscountApplicable && !isFreebie) ? discountOn : null;
                                                                                                              let DiscountAmount = ((sales_token == '') && isDiscountApplicable && !isFreebie) ? discountAmount : 0;
                                                                                                              let insert_query = "insert into cart (product_id , user_id , session_id , esu_quantity , esu , parent_id , total_price , rate , margin , le_wh_id_list, le_wh_id ,hub_id , created_at  , star , is_slab , prmt_det_id , product_slab_id , freebee_mpq , freebee_qty , discount ,discount_type ,discount_on ,discount_amount, dit_quantity , quantity) values (" + productId + ',' + customerId + ",'" + token + "'," + esuQuantity + ',' + esu + ',' + parentId + ',' + totalPrice + ',' + rate + ',' + margin + ",'" + wh_id + "'," + lewhid + ',' + hub + ",'" + formatted_date + "'," + star + ',' + is_slab + ',' + prmt_det_id + ',' + productSlabId + ',' + freebee_mpq + ',' + freebee_qty + ',' + Discount + "," + DiscountType + "," + DiscountOn + "," + DiscountAmount + ',' + dit_qty + ',' + quantity_cart + ")";
                                                                                                              sequelize.query(insert_query).then(cartDataInsered => {
                                                                                                                   cartInsertId = cartDataInsered[0];
                                                                                                                   if (packs != '') {
                                                                                                                        packs.forEach((value) => {
                                                                                                                             let packCashBack = (typeof value.pack_cashback != 'undefined' && value.pack_cashback != '') ? value.pack_cashback : 0;
                                                                                                                             let packQty = typeof value.pack_qty != 'undefined' && value.pack_qty != '' ? value.pack_qty : 0;
                                                                                                                             let query = "insert into cart_product_packs  (product_id , user_id , cart_id , session_id , esu , esu_quantity , star , pack_level , created_at , pack_price , pack_qty,pack_cashback)  values(" + productId + ',' + customerId + ',' + cartInsertId + ",'" + token + "'," + value.esu + ',' +
                                                                                                                                  value.qty + ',' + value.star + ',' + value.pack_level + ",'" + formatted_date + "'," + rate * packQty + ',' + packQty + ',' + packCashBack + ")";
                                                                                                                             sequelize.query(query).then(inserted => {
                                                                                                                                  console.log("inserted successfully");
                                                                                                                                  data = Object.assign(data, {
                                                                                                                                       'status': status,
                                                                                                                                       'cartId': cartInsertId,
                                                                                                                                       'product_id': productId,
                                                                                                                                       'le_wh_id': wh_id,
                                                                                                                                       'hub_id': hub,
                                                                                                                                       'available_quantity': availableQuantity[0].availQty
                                                                                                                                  })
                                                                                                                                  inventoryArray[i] = data;
                                                                                                                                  return resolve(inventoryArray);
                                                                                                                             }).catch(err => {
                                                                                                                                  console.log(err);
                                                                                                                                  reject(err);
                                                                                                                             })
                                                                                                                        })
                                                                                                                   }
                                                                                                              }).catch(err => {
                                                                                                                   console.log(err);
                                                                                                                   reject(err);
                                                                                                              })
                                                                                                         } else {
                                                                                                              console.log("in else part due to Cart not deleted while checkcart for product id: " + productId + ' request from the user Id ' + customerId + '_customer_type_' + customerType)
                                                                                                              cartInsertId = '';
                                                                                                              data = Object.assign(data, {
                                                                                                                   'status': status,
                                                                                                                   'cartId': cartInsertId,
                                                                                                                   'product_id': productId,
                                                                                                                   'le_wh_id': wh_id,
                                                                                                                   'hub_id': hub,
                                                                                                                   'available_quantity': availableQuantity[0].availQty
                                                                                                              })
                                                                                                              inventoryArray[i] = data;
                                                                                                              return resolve(inventoryArray);
                                                                                                         }

                                                                                                    }).catch(err => {
                                                                                                         console.log(err);
                                                                                                         reject(err);
                                                                                                    })
                                                                                               }).catch(err => {
                                                                                                    console.log(err);
                                                                                                    reject(err);
                                                                                               })
                                                                                               console.log("cartInsertId", cartInsertId);
                                                                                               //cartId = cartInsertId;
                                                                                          }
                                                                                          data = Object.assign(data, {
                                                                                               'status': status,
                                                                                               'cartId': cartId,
                                                                                               'product_id': productId,
                                                                                               'le_wh_id': wh_id,
                                                                                               'hub_id': hub,
                                                                                               'available_quantity': availableQuantity[0].availQty
                                                                                          })
                                                                                     }

                                                                                     inventoryArray[i] = data;
                                                                                     return resolve(inventoryArray);
                                                                                }).catch(err => {
                                                                                     console.log(err);
                                                                                     reject(err);
                                                                                })
                                                                           }
                                                                      }
                                                                 })
                                                            }
                                                       }
                                                  }).catch(err => {
                                                       console.log(err);
                                                       return reject(err);
                                                  })
                                             }).catch(err => {
                                                  console.log(err);
                                                  reject(err);
                                             })

                                        }
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   return reject(err);
                              })
                              console.log("ísfreebie ===>>>>>", isFreeBie);

                         }).catch(err => {
                              console.log(err);
                              return reject(err);
                         })

                    }
               }).catch(err => {
                    console.log(err);
                    return reject(err);
               })

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })

}

/*
 * purpose : function is used to check inventory
 * request : cartdata, wh_id, hub, segmentId, token, customerId, sales_token = '', cust_type
 * return : return  data if available in inventory,
 * author : Deepak Tiwari
 */
module.exports.CheckCartInventory = function (cartdata, wh_id, hubId, segmentId, token, customerId, sales_token = '', custType, mfcType) {
     try {
          console.log("======>756", cartdata, wh_id, hubId, segmentId, token, customerId, sales_token = '', custType)
          return new Promise((resolve, reject) => {
               let customerType;
               //fetching legalEntity based on customerId
               getLegalEntityId(customerId).then(legalentity_id => {
                    let legalEntityId = legalentity_id;
                    console.log("customer_type", custType, legalEntityId);
                    if (custType == 'NULL') {
                         getUserCustomerType(legalEntityId).then(customertype => {//based on legalEntityid fetching customertype
                              customerType = customertype;
                              cartInventory(cartdata, wh_id, hubId, segmentId, token, customerId, sales_token = '', customerType).then(response => {//used to checkcart Inventory based on customerType
                                   resolve(response);
                              }).catch(err => {
                                   console.log(err);
                                   reject(err);
                              })
                         }).catch(err => {
                              console.log(err);
                              return reject(err);
                         })
                    } else {
                         customerType = custType;
                         cartInventory(cartdata, wh_id, hubId, segmentId, token, customerId, sales_token = '', customerType).then(response => {//used to checkcart Inventory based on customerType
                              resolve(response);
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    }
               }).catch(err => {
                    console.log(err);
                    return reject(err);
               })
          })
     } catch (err) {
          console.log(err);
          reject(err);
     }
}

/*
 * purpose :  funtion used to get pack Data
 * request : customerId , product_id
 * return : will return  cart details,
 * author : Deepak Tiwari
 */
module.exports.getPackdata = function (productId, token, le_wh_id) {
     return new Promise((resolve, reject) => {
          try {
               this.getcustomerId(token).then(user_id => {
                    let temp = JSON.stringify(le_wh_id).trim();
                    temp = temp.replace(',', '_');
                    let appKeyData = process.env.DATABASE_NAME;
                    let getPackData = [];
                    let slabDetails;
                    let error = { 'status': 'failed', 'message': 'Something went wrong' }
                    let keyString = appKeyData + '_product_slab_' + productId + '_le_wh_id_' + le_wh_id;
                    console.log('keyString', keyString);
                    //used to get data from redis cache
                    cache.get(keyString, async function (err, response) {
                         if (err) {
                              console.log(err);
                              return reject(error);
                         } else if (typeof response != 'undefined' && response != null) {
                              slabDetails = await (response != '' ? JSON.parse(JSON.stringify(response)) : []);
                              if (typeof slabDetails != 'undefined') {
                                   getPackData.push(slabDetails[temp])
                                   return resolve(getPackData);
                              } else {
                                   let query = "CALL getProductSlabs(" + productId + ',' + le_wh_id + ',' + user_id + ")";
                                   sequelize.query(query).then(result => {
                                        if (result.length > 0) {
                                             slabDetails[temp] = result;
                                             cache.set(keyString, slabDetails, function (err, done) {
                                                  if (err) {
                                                       console.log(err);
                                                       reject(err);
                                                  } else {
                                                       console.log("done===>1425", done);
                                                  }
                                             })
                                        } else {
                                             Console.log("no data found in product slab");
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        return reject(error);

                                   })
                              }
                              console.log("getpackData==>1437", getPackData);
                              return resolve(getPackData);
                         } else {
                              let query = "CALL getProductSlabs(" + productId + ',' + le_wh_id + ',' + user_id + ")";
                              sequelize.query(query).then(result => {
                                   console.log("result", result)
                                   if (result.length > 0) {
                                        console.log("result===>1418", result, temp);
                                        slabDetails[temp] = result;
                                        console.log("slabdetails ===>1420", slabDetails)
                                        cache.set(keyString, 'abdc', function (err, done) {
                                             if (err) {
                                                  console.log(err);
                                                  reject(err);
                                             } else {
                                                  console.log("done===>1425", done);
                                             }
                                        })
                                   } else {
                                        console.log("no data found in product slab");
                                   }
                                   console.log("getpackData==>1455", getPackData);
                                   return resolve(getPackData);
                              }).catch(err => {
                                   console.log(err);
                                   return reject(err);
                              })
                         }
                    });
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

/*
 * purpose : function is used edit cart
 * request : product_id, customer_token, quantity, le_wh, segment_id
 * return : will return edited cart details,
 * author : Deepak Tiwari
 */
module.exports.editCart = function (productId, customer_token, quantity, le_wh, segment_id) {
     try {
          return new Promise((resolve, reject) => {
               let query = "select parent_id from vw_cp_products  where product_id =" + productId;
               sequelize.query(query).then(parent_id => {
                    console.log("parent_id", parent_id);
                    let parentId = parent_id[0].parent_id;
                    let productData = {};
                    let productName;
                    let childProducts;
                    let variantSize;
                    let variantSize_1;
                    let variantSize_2;
                    let inventory;
                    let isDefault;
                    let orderedQuantity;
                    productData = { 'product_id': parentId };
                    let query_1 = "select prod.product_title as product_name,prod.esu,pc.description,prod.primary_image,prod.mrp from products as products as prod left join product_content as pc ON pc.product_id = prod.product_id where prod.product_id =" + parentId;
                    sequelize.query(query_1).then(product_name => {
                         console.log("product_name", product_name);
                         if (product_name.length > 0) {
                              productName = product_name[0];
                              console.log("productname=====> 1161", productName)
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

                              //used to get childproduct
                              getChildProduct(parentId).then(child_product => {
                                   console.log("child", child_product);
                                   if (child_product != '') {
                                        childProducts = child_product;
                                   } else {
                                        childProducts = '';
                                   }
                                   let variantname = childProducts.filter(unique);
                                   let variantName = variantname.variant_value1;
                                   if (variantName[0] != '') {
                                        variantSize = variantName.length;
                                   } else {
                                        variantSize = 0;
                                   }

                                   let variantName_1 = variantname.variant_value2;
                                   if (variantName_1[0] != '') {
                                        variantSize_1 = variantName_1.length;
                                   } else {
                                        variantSize_1 = 0;
                                   }

                                   let variantName_2 = variantname.variant_value3;
                                   if (variantName_2[0] != '') {
                                        variantSize_2 = variantName_2.length;
                                   } else {
                                        variantSize_2 = 0;
                                   }

                                   if (variantSize > 0) {
                                        for (let i = 0; i < variantSize; i++) {
                                             childProducts.forEach((childproduct) => {
                                                  if (childproduct.product_id == productId) {
                                                       orderedQuantity = quantity;
                                                       isDefault = 1;
                                                       getInventory(childproduct.product_id, le_wh, segment_id).then(result => {
                                                            if (result != '') {
                                                                 inventory = result;
                                                            } else {
                                                                 inventory = '';
                                                            }
                                                       }).catch(err => {
                                                            console.log(err);
                                                       })
                                                  } else {
                                                       orderedQuantity = 0;
                                                       isDefault = 0;
                                                       getInventory(childproduct.product_id, le_wh, segment_id).then(result => {
                                                            if (result != '') {
                                                                 inventory = result;
                                                            } else {
                                                                 inventory = '';
                                                            }
                                                       }).catch(err => {
                                                            console.log(err);
                                                       })
                                                  }

                                                  if (variantName[i] == childproducts.variant_value1) {
                                                       productData = Object.assign(productData, {
                                                            ['variants' + [i] + 'variant_name']: childproduct.variant_value1,
                                                            ['variants' + [i] + 'product_id']: childproduct.product_id,
                                                            ['variants' + [i] + 'product_name']: childproduct.product_title
                                                       })
                                                       if (childproduct.product_id == productId) {
                                                            orderedQuantity = quantity;
                                                            getInventory(childproduct.product_id, le_wh, segment_id).then(result => {
                                                                 if (result != '') {
                                                                      inventory = result;
                                                                 } else {
                                                                      inventory = '';
                                                                 }
                                                                 productData = Object.assign(productData, {
                                                                      ['variants' + [i] + 'quantity']: inventory,
                                                                      ['variants' + [i] + 'ordered_quantity']: orderedQuantity,
                                                                      ['variants' + [i] + 'is_default']: isDefault
                                                                 })
                                                            }).catch(err => {
                                                                 console.log(err);
                                                            })
                                                            isDefault = 1;
                                                       } else {
                                                            isDefault = 0;
                                                            orderedQuantity = 0
                                                            getInventory(childproduct.product_id, le_wh, segment_id).then(result => {
                                                                 if (result != '') {
                                                                      inventory = result;
                                                                 } else {
                                                                      inventory = '';
                                                                 }
                                                                 productData = Object.assign(productData, {
                                                                      ['variants' + [i] + 'quantity']: inventory,
                                                                      ['variants' + [i] + 'ordered_quantity']: orderedQuantity,
                                                                      ['variants' + [i] + 'is_default']: isDefault
                                                                 })
                                                            }).catch(err => {
                                                                 console.log(err);
                                                            })


                                                       }
                                                       let productSpecification = getProductSpecifications(childproduct.product_id);
                                                       let reviews = getReviews(childproduct.product_id);
                                                       let reating = Review(productId);
                                                       productData = Object.assign(productData, {
                                                            ['variants' + [i] + 'description']: getDescription(childproduct.product_id),
                                                            ['variants' + [i] + 'mrp']: childproduct.mrp,
                                                            ['variants' + [i] + 'image']: childproduct.primary_image,
                                                            ['variants' + [i] + 'images']: getMedia(childproduct.product_id),
                                                            ['variants' + [i] + 'specification']: productSpecification,
                                                            ['variants' + [i] + 'reviews']: reviews,
                                                            ['variants' + [i] + 'esu']: childproduct.esu,
                                                            ['variants' + [i] + 'variant_name']: childproduct.variant_value1,
                                                       })
                                                       let k = 0;
                                                       if (childproduct.variant_value2 != '') {
                                                            productData = Object.assign(productData, {
                                                                 ['variants' + [i] + 'has_inner_varients']: 0,
                                                                 ['variants' + [i] + 'pakcs']: this.getPackData(childproduct.product_id, customer_token, le_wh)
                                                            })
                                                       } else {
                                                            for (let j = 0; j < variantSize_1; j++) {
                                                                 productData = Object.assign(productData, {
                                                                      ['variants' + [i] + 'has_inner_varients']: 1
                                                                 })
                                                                 if (variantName_1[j] == childproduct.variant_value2) {
                                                                      productData = Object.assign(productData, {

                                                                      })
                                                                 }
                                                            }
                                                       }
                                                  }

                                             })
                                        }

                                   }
                              }).catch(err => {
                                   console.log(err);
                              })
                         } else {

                         }
                    })


               })
          })

          return productData;
     } catch (err) {
          console.log(err.message)
     }
}

/*
 * purpose : function is used view cart details
 * request : customerId
 * return : will return  cart details,
 * author : Deepak Tiwari
 */
module.exports.getViewcartData = function (customerId) {
     return new Promise((resolve, reject) => {
          try {
               let cartDetails;
               let query = "select oc.cart_id as cartId,p.product_title as Name,p.product_id,p.product_id as variant_id,oc.total_price,oc.created_at  from cart oc left join products p on p.product_id = oc.product_id  where user_id = " + customerId + " order by p.is_parent = 1 and p.is_active = 1 desc"
               sequelize.query(query).then(result => {
                    if (result.length > 0) {
                         cartDetails = JSON.parse(JSON.stringify(result[0]));
                         return resolve(cartDetails);
                    } else {
                         cartDetails = '';
                         return resolve(cartDetails);
                    }
               }).catch(err => {
                    console.log(err);
                    return reject({ 'status': 'failed', 'message': 'Something went wrong' })
               })
          } catch (err) {
               console.log(err);
               return reject({ 'status': 'failed', 'message': 'Internal server error' })
          }
     })
}


/*
 * purpose :  funtion used to Get cart ,Product & variant details 
 * request : customerId , product_id
 * return : will return  cart details,
 * author : Deepak Tiwari
 */
module.exports.variant = function (productId, customerId) {
     return new Promise((resolve, reject) => {
          try {
               let query = 'select p.product_title as product_name,pc.description,p.product_id as product_variant_id,p.product_id as variant_id,cp.variant_value1 as name,cp.primary_image as Image,p.sku,p.mrp,p.is_parent as is_default,oc.quantity as Total_quantity,oc.total_price as Total_Price,oc.rate as applied_mrp,oc.margin as applied_margin  from cart oc left join products p on p.product_id = oc.product_id left join vw_cp_products cp on cp.product_id = oc.product_id left join product_content pc on pc.product_id = oc.product_id where oc.product_id = ' + productId + ' and oc.user_id = ' + customerId + ' group by p.product_id order by oc.cart_id ASC';
               sequelize.query(query).then(result => {
                    if (result.length > 0) {
                         cartDetails = JSON.parse(JSON.stringify(result[0]));
                         return resolve(cartDetails);
                    } else {
                         cartDetails = '';
                         return resolve(cartDetails);
                    }
               })
          } catch (err) {
               console.log(err);
               return reject({ 'status': 'failed', 'message': 'Internal server error' })
          }
     })
}


/*
 * purpose :  funtion used to get count of product after placing order. 
 * request : customerId 
 * return : will return  count of product,
 * author : Deepak Tiwari
 */
module.exports.cartcount = function (customerId) {
     return new Promise((resolve, reject) => {
          try {
               let count = 0;
               let query = "select count(cart_id) as cc from cart where user_id='" + customerId + "' and status='0'";
               sequelize.query(query).then(count => {
                    console.log("count", count);
                    if (count.length > 0) {
                         let productCount = JSON.parse(JSON.stringify(count[0]))
                         let cartCount = productCount[0].cc;
                         console.log("cartcount==.>", cartCount)
                         if (cartCount > 0) {
                              count = 0;
                              return resolve(count);
                         } else {
                              let query = "select count(cart_id) as cc from cart where user_id = '" + customerId + "'";
                              sequelize.query(query).then(total => {
                                   if (total.length > 0) {
                                        console.log("totalcount", total)
                                        let totalCount = JSON.parse(JSON.stringify(total[0]));
                                        count = totalCount[0].cc
                                        console.log("count", count)
                                        return resolve(count);
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   return reject({ 'status': 'failed', 'message': 'Something went wrong' })
                              })
                         }
                    } else {
                         resolve('');
                    }
               }).catch(err => {
                    console.log(err);
                    return reject({ 'status': 'failed', 'message': 'Something went wrong' })
               })
          } catch (err) {
               console.log(err);
               return reject({ 'status': 'failed', 'message': 'Internal server error' })
          }
     })
}

/*
 * purpose : funtion used to get userId based on customer_token
 * request : customerId 
 * return : will return  count of product,
 * author : Deepak Tiwari
 */
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
               host: process.env.EMAIL_HOST,
               port: 587,
               secureConnection: true, // upgrade later with STARTTLS
               auth: {
                    user: process.env.EmailUserName,
                    pass: process.env.EmailPassword
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

/*
 *   For: Used to get all those user those whose comes under perticular messageCode 
 *   Author: Deepak Tiwari
 *   Request MessageCode
 *   Returns:Will return userDetails
 */
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

///used to get users email id based on  ids
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

//hub details based on beatId
module.exports.getHub = function (beat_id) {
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

/*
Purpose : used to get warehouseid based on provided hub_id
author : Deepak Tiwari
Request : hub_id.
Resposne : Returns warehouseid.
*/
module.exports.getWarehouseidByHub = function (hub_id) {
     return new Promise((resolve, reject) => {
          let query = "select lew.le_wh_id from legalentity_warehouses as lew left join dc_hub_mapping as dhm ON dhm.dc_id = lew.le_wh_id where dhm.hub_id = " + hub_id + "  and lew.dc_type = 118001";
          db.query(query, {}, function (err, row) {
               if (err) {
                    console.log(err)
                    reject(err)
               } else if (Object.keys(row).length > 0) {
                    let le_wh_ids = [];
                    let leWhId = [];
                    if (Array.isArray(row) && row.length > 0) {
                         console.log("le_wh", row)
                         for (let i = 0; i < row.length; i++) {
                              if (row.length == 1) {
                                   le_wh_ids.push(row[i].le_wh_id + ',')//adding comma separator
                                   leWhId = le_wh_ids[0].split(',')//spliting based on commas
                                   le_wh_ids = parseInt(le_wh_ids);// at last converting result into integer
                              } else {
                                   le_wh_ids.push(row[i].le_wh_id)
                              }
                         }
                    }
                    resolve(le_wh_ids)
               }
          })

     })
}

/*
Purpose : used to get warehouseid based on provided hub_id
author : Deepak Tiwari
Request : hub_id.
Resposne : Returns warehouseid.
*/
module.exports.updateBeat = function (userId, custId, beatId, le_wh_id, hubId) {
     return new Promise((resolve, reject) => {
          try {
               let query = " select legal_entity_id  from legalentity_warehouses where le_wh_id =" + le_wh_id;
               sequelize.query(query).then(le_id => {
                    console.log("le_id==>1829", le_id)
                    let leId = JSON.parse(JSON.stringify(le_id[0]))
                    if (leId != '') {
                         //fetching beat_name  based on beatId from pjp_pincode_area
                         let query_1 = "select * from pjp_pincode_area where pjp_pincode_area_id =" + beatId;
                         sequelize.query(query_1).then(beat_name => {
                              let beatName = JSON.parse(JSON.stringify(beat_name[0]))
                              console.log("beatName==>1836", beatName)
                              if (beatName != '') {
                                   //updating customer table
                                   let updateCustomer = " update customers set beat_id =" + beatId + " , hub_id =" + hubId + " , spoke_id =" + beatName[0].spoke_id + " , updated_by =" + userId + " , updated_at ='" + moment().format("YYYY-MM-DDTHH:mm:ss") + "'  where le_id =" + custId;
                                   sequelize.query(updateCustomer).then(cusUpdated => {
                                        console.log("customer updated successfully")
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                                   //updating retailer_flat
                                   let updateRetailer = " update retailer_flat  set beat_id =" + beatId + " , beat ='" + beatName[0].pjp_name + "' ,  hub_id =" + hubId + " , spoke_id = " + beatName[0].spoke_id + " , parent_le_id =" + leId[0].legal_entity_id + " , updated_by =" + userId + " , updated_at = '" + moment().format("YYYY-MM-DDTHH:mm:ss") + "' where legal_entity_id =" + custId;
                                   sequelize.query(updateRetailer).then(retailerUpdated => {
                                        console.log("retailers updated successfully")
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                                   //updating legal_entity_table 
                                   let legalEntityUpdate = " update legal_entities set parent_le_id = " + leId[0].legal_entity_id + "  where legal_entity_id = " + custId;
                                   sequelize.query(legalEntityUpdate).then(legalEntityUpdated => {
                                        console.log("legalEntity updated successfully")
                                   }).catch(err => {
                                        console.log(err);
                                        reject(err);
                                   })
                                   resolve();
                              } else {
                                   resolve('');
                              }
                         }).catch(err => {
                              console.log(err);
                              reject(err);
                         })
                    } else {
                         resolve(0);
                    }

               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
          }
     })

}
