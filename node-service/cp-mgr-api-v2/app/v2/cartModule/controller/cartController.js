let cartModel = require('../../cartModule/model/cartModel');
let cpMessage = require('../../../config/cpMessage');

/*
 * purpose : the function is used to add cart
 * request : customer_id , customertype , user_id , product_id
 * return : Return inventory details,
 * author : Deepak Tiwari
 */
module.exports.addCart = function (req, res) {
     try {
          let customerToken;
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               let request = { 'parameters': data, apiurl: 'addCart' };
               cartModel.logApiRequests(request);
               if (typeof data.customer_token == 'undefined' && data.customer_token == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Token not sent' })
               } else {
                    //validating customer_token
                    cartModel.validateToken(data.customer_token).then(customers_token => {
                         console.log("customer_Token", customers_token);
                         customerToken = customers_token;
                         //checking weather token is active or not 
                         if (customerToken.token_status == 0) {//0
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken });
                         } else {
                              let custId = typeof data.user_id != 'undefined' ? data.user_id : 0;
                              let isSlab = (typeof data.is_slab && data.is_slab != '') ? data.is_slab : 0;
                              let customerId = (typeof data.customer_id != 'undefined' && data.customer_id != '') ? data.customer_id : custId;
                              let customerType = (typeof data.customer_type != 'undefined' && data.customer_type != '') ? data.customer_type : 0;
                              if (customerType == 0) {
                                   res.status(200).json({ 'status': 'session', 'message': 'Customer type should not be empty' });
                              }
                              let customerData = { 'customer_id': customerId, 'customer_type': customerType }
                              cartModel.addCart(data.product_id, data.quantity, data.le_wh_id, data.segment_id, customerData).then(inventory => {
                                   if (inventory.status == -1) {
                                        res.status(200).json({ 'status': 'failed', 'message': 'Offer is not valid for this quantity', 'data': inventory })
                                   } else {
                                        res.status(200).json({ 'status': 'Succes', 'message': 'Addcart', 'data': inventory })
                                   }
                              }).catch(err => {
                                   console.log(err);
                              })
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               }

          } else {
               res.status(200).json({ "status": 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          res.staus(200).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}

/*
 * purpose : the function is used to InventoryCheck
 * request : customer_id , customertype , user_id , product_id
 * return : Return inventory details,
 * author : Deepak Tiwari
 */
module.exports.CheckCartInventory = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               let salesUserId;
               let request = { 'parameter': data, apiurl: 'CheckCartInventory' };
               console.log("requets", request);
               cartModel.logApiRequests(request);
               if (typeof data.customer_token != 'undefined' && data.customer_token == '') {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed })
               } else {
                    //validating customer_token
                    cartModel.validateToken(data.customer_token).then(value => {
                         //checking weather token is active or not 
                         if (value.token_status == 0) {//0
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken });
                         } else {
                              // If the order is placed by PO on web, then we don`t validate the Data,
                              // But if the order is from Mobile, then we were validating  DC and Hub.
                              if (typeof data.is_web != 'undefined') {//checking weather request is send from web or mobile
                                   salesUserId = typeof data.sales_rep_id != 'undefined' ? data.sales_rep_id : '';
                                   // Here we were checking  DC and Hub Mapping Relation
                                   cartModel.checkValidRelation(salesUserId, data.customer_legal_entity_id).then(result => {//validating ff and retailer mapping
                                        console.log("checkValidRelation", result);
                                        if (result != '') {
                                             //if order is placed by retailer(self login)
                                             if (result.status == 'success') {
                                                  if (salesUserId == "") {
                                                       if (data.le_wh_id != result.data[0].dc_id || data.hub != result.data[0].hub) {
                                                            res.status(200).json({ 'status': 'success', 'message': 'Please select valid Dc or Hub' })
                                                       }
                                                  }
                                             } else {
                                                  //validation failed
                                                  res.status(200).json(result);
                                             }
                                        } else {
                                             res.status(200).json({ 'status': 'failed', 'messaage': cpMessage.UnsuccessfulOperation })
                                        }
                                   })
                                   //checking dchub mapping
                                   cartModel.checkDCHubMapping(data.le_wh_id, data.hub).then(dchubMapping => {
                                        console.log("dcHUbMapping", dchubMapping);
                                        if (dchubMapping.length == 0) {
                                             res.status(200).json({ 'status': 'failed', 'message': 'Improper Dc,Hub mapping!' })
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }
                              //
                              let custId = typeof value.user_id != 'undefined' ? value.user_id : 0;
                              let deleteCartArr = { 'customer_token': data.customer_token, 'isClearCart': 'true' };
                              let productArraySize = data.products.length;
                              let customerId = custId;
                              let salesToken = typeof data.sales_token != 'undefined' ? data.sales_token : '';
                              let customerType = typeof data.customer_type != 'undefined' ? data.customer_type : 'Null';
                              //clearing privious cart details
                              cartModel.deletecart(deleteCartArr).then(deletedCart => {
                                   if (deletedCart != '') {
                                        console.log("Deleted Successfully");
                                   }
                              }).catch(err => {
                                   console.log(err);
                              })

                              console.log("===>customer_token", customerType);
                              //based on customer_type changing hub array
                              if (customerType == '3015') {
                                   data.hub = process.env.CNC_HUB_ID;
                              } else if (customerType == '3016') {
                                   data.hub = process.env.CLEARANCE_HUB_ID;
                              }

                              let mfcType = typeof data.type != 'undefined' ? data.type : '';
                              console.log("mfctype", mfcType);
                              if (mfcType == 'mfc') {
                                   let orderTotal = 0;
                                   let credit_limit;
                                   data.products.forEach((product) => {
                                        orderTotal += product.total_price;
                                        credit_limit = product.credit_limit;
                                   })
                                   console.log("orderTotal ,creditlimit", orderTotal, credit_limit);
                                   //checking credit limit
                                   if (orderTotal > credit_limit) {
                                        let different = orderTotal - credit_limit;
                                        res.status(200).json({ 'status': 'failed', 'message': "Order value exceeded credit limit by " + different, 'data': different });
                                   }
                              }
                              console.log("data.products, data.le_wh_id, data.hub, data.segment_id, data.customer_token, customerId, salesToken, customerType, mfcType", data.products, data.le_wh_id, data.hub, data.segment_id, data.customer_token, customerId, salesToken, customerType, mfcType)
                              cartModel.CheckCartInventory(data.products, data.le_wh_id, data.hub, data.segment_id, data.customer_token, customerId, salesToken, customerType, mfcType).then(inventory => {
                                   if (inventory != '') {
                                        res.status(200).json({ 'status': 'success', 'message': 'CheckCartInventory', 'data': inventory })
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': 'No inventory found' })
                                   }
                              }).catch(err => {
                                   console.log(err);
                              })
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Required parameter not passed' })
          }
     } catch (err) {
          res.status(200).json({ 'status': "failed", 'message': err.message });
     }
}

/*
 * purpose : the function is used to delete cart
 * request : cartid , productid
 * return : deleted cart data,
 * author : Deepak Tiwari
 */
module.exports.deleteCart = function (req, res) {
     try {
          if (req.body.data != '' && typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.customer_token != 'undefined' && data.customer_token == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Token not sent' })
               }

               if (typeof data.isClearCart != 'undefined' && data.isClearCart == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'isClearCart flag not sent' })
               }

               cartModel.validateToken(data.customer_token).then(customer_token => {
                    console.log(customer_token)
                    let customerToken = customer_token;
                    if (customerToken.token_status == 0) {
                         res.status(200).json({ 'status': 'session', 'message': 'You have already logged into the Ebutor System' })
                    } else if (customerToken.token_status == 1) {
                         cartModel.deletecart(data).then(deleted => {
                              if (deleted != '') {
                                   res.status(200).json({ 'status': 'success', 'message': 'Delete cart', 'data': 'Your product was successfully deleted' })
                              } else {
                                   res.status(200).json({ 'status': 'success', 'message': 'Delete cart', 'data': 'Unable to refesh your cart' })
                              }
                         }).catch(err => {
                              console.log(err);
                         })
                    }
               }).catch(err => {
                    console.log(err);
               })
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Please send required feild' })
          }
     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': "Unable to process your request " })
     }
}

/*
 * purpose : the function is used to edit cartdetails
 * request : cartid , productid
 * return : edited cart data,
 * author : Deepak Tiwari
 */
module.exports.editCart = function (req, res) {
     try {
          if (req.body.data != '' && typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.customer_token != 'undefined' && data.customer_token == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Token not sent' })
               }
               //token validation
               cartModel.validateToken(data.customer_token).then(customer_token => {
                    console.log(customer_token)
                    let customerToken = customer_token;
                    if (customerToken.token_status == 0) {
                         res.status(200).json({ 'status': 'session', 'message': 'You have already logged into the Ebutor System' })
                    } else if (customerToken.token_status == 1) {
                         cartModel.editCart(data.product_id, data.customer_token, data.quantity, data.le_wh_id, data.segment_id).then(edited => {
                              if (edited != '') {
                                   let result = { 'data': edited };
                                   res.status(200).json({ 'status': 'failed', 'message': 'Successfully Edited', 'data': result })
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'Unable to process your edit request .Please try later', 'data': result })
                              }
                         })
                    }
               }).catch(err => {
                    console.log(err);
               })
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Please send required feild' });
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': 'Something went wrong' });
     }
}

/*
 * purpose : the function is used to view cartdetails
 * request : cartid , productid
 * return : cart details,
 * author : Deepak Tiwari
 */
module.exports.veiwCart = function (req, res) {
     try {
          if (req.body.data != '' && typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.customer_token != 'undefined' && data.customer_token == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Token not sent' })
               }
               //token validation
               cartModel.validateToken(data.customer_token).then(customer_token => {
                    console.log(customer_token)
                    let customerToken = customer_token;
                    if (customerToken.token_status == 0) {
                         res.status(200).json({ 'status': 'session', 'message': 'You have already logged into the Ebutor System' })
                    } else if (customerToken.token_status == 1) {
                         //fetching customerId
                         cartModel.getcustomerId(data.customer_token).then(customerId => {
                              if (customerId != '') {
                                   //fetching cart details
                                   cartModel.getViewcartData(customerId).then(view => {
                                        if (view.length > 0) {
                                             let result = {};
                                             let k = 0;
                                             for (let i = 0; i < view.length; i++) {
                                                  result[k] = {
                                                       'cartId': view[i].cartId,
                                                       'product_id': view[i].product_id,
                                                       'name': view[i].Name,
                                                       'total_price': view[i].total_price,
                                                       'deta_added': view[i].created_at
                                                  }
                                                  //fetching different variant od perticuladr product
                                                  cartModel.variant(view[i].product_id, customerId).then(variants => {
                                                       if (variants.length > 0) {
                                                            console.log("variants======>303", variants);
                                                            let l = 0;
                                                            for (let j = 0; j < variants.length; j++) {
                                                                 result = Object.assign(result, {
                                                                      [result[k] + 'variants' + [l]]: variants[j]
                                                                 });
                                                                 //fetching packdetails
                                                                 cartModel.getPackdata(view[i].product_id, data.le_wh_id, customerId).then(packsData => {
                                                                      console.log("packsdata", packsData.length);
                                                                      if (packsData.length > 0 && typeof packsData[0] != 'undefined') {
                                                                           for (let c = 0; c < packsData.length; c++) {
                                                                                result = Object.assign(result, {
                                                                                     [result[k] + 'variants' + [l] + 'packs' + [c]]: {
                                                                                          'pack_size': packsData[i].pack_size,
                                                                                          'margin': packsData[c].margin,
                                                                                          'unit_price': packsData[i].unit_price,
                                                                                          'dealer_price': packsData[i].dealer_price,
                                                                                          'qty': ''
                                                                                     }
                                                                                })
                                                                           }
                                                                           l++;
                                                                      } else {
                                                                           console.log({ 'status': 'failed', 'message': 'No pack details available' })
                                                                      }
                                                                 }).catch(err => {
                                                                      console.log(err);
                                                                      res.status(500).json({ 'status': 'failed', 'message': 'Something went wrong' });
                                                                 })
                                                            }
                                                            k++;
                                                       } else {
                                                            res.status(200).json({ 'status': 'failed', 'message': 'No variants available for this product' })
                                                       }

                                                  }).catch(err => {
                                                       console.log(err);
                                                       res.status(500).json({ 'status': 'failed', 'message': 'Something went wrong' });
                                                  })
                                             }
                                             console.log("result", result)
                                             if (result.length > 0) {
                                                  res.status(200).json({ 'status': 'success', 'message': 'Available cart details', 'data': result });
                                             } else {
                                                  res.status(200).json({ 'status': 'success', 'message': 'Your cart is empty' });
                                             }

                                        } else {
                                             res.status(200).json({ 'status': 'failed', 'message': 'No cart details found' })
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(500).json({ 'status': 'failed', 'message': 'Something went wrong' });
                                   })

                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'User not exist in ebutor system' })
                              }
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' });
                         })
                    }
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' });
               })
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Please send required feild' });
          }

     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': 'Something went wrong' });
     }
}

/*
Purpose : Used to get cartCount 
author : Deepak Tiwari
Request : Cart Details
Resposne : Return cart Count
*/
module.exports.cartCount = function (req, res) {
     try {
          if (req.body.data != '' && typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.customer_token != 'undefined' && data.customer_token == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Token not sent' })
               }
               //token validation
               cartModel.validateToken(data.customer_token).then(customer_token => {
                    console.log(customer_token)
                    let customerToken = customer_token;
                    if (customerToken.token_status == 0) {
                         res.status(200).json({ 'status': 'session', 'message': 'You have already logged into the Ebutor System' })
                    } else if (customerToken.token_status == 1) {
                         //fetching customerId
                         cartModel.getcustomerId(data.customer_token).then(customerId => {
                              if (customerId != '') {
                                   cartModel.cartcount(customerId).then(count => {
                                        console.log("count ====> 401", count);
                                        res.status(200).json({ 'status': 'success', 'message': "cartcount", 'data': count })
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': "failed", 'message': 'Something went wrong' })
                                   })
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'User not exist in ebutor system' })
                              }
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': "failed", 'message': 'Something went wrong' })
                         })
                    }
               }).catch(err => {
                    console.log(err);
               })
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Please send required feild' });
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': 'Internal server error' })
     }
}


/*
Purpose : Used to sendMailtoFF once new retailer registers in ebutor(Based on notification_code we were fetching user roles  then for those specific role only we were sending email)
author : Deepak Tiwari
Request : Customer_token , customer_id , Email_id
Resposne : Return message  that user registered successfully
*/
module.exports.sendEmailtoFF = function (req, res) {
     try {
          let data = JSON.parse(req.body.data);
          let customerToken;
          let customerId;
          let toEmail;
          let subject;
          let body;
          let retailerInformation;
          if (typeof data.customer_token == 'undefined' && data.customer_token == '') {
               res.status(200).json({ 'status': 'failed', 'message': "Token not sent" })
          }
          //validation customer token
          cartModel.validateToken(data.customer_token).then(customer_token => {
               customerToken = customer_token;
               if (customerToken.token_status == 0) { //==0
                    res.status(200).json({ 'status': 'Failed', 'message': 'You have already logged into the ebutor system' })
               } else {
                    customerId = data.customer_id
                    //fetching userid
                    usermodel.getUserId(data.customer_token).then(userData => {
                         if (userData != '') {
                              let userId = typeof userData[0].user_id != 'undefined' ? userData[0].user_id : '';
                              if (userId == '' || customerId == '') {
                                   res.status(200).json({ 'status': 'failed', 'message': 'User or customer details can not be empty' });
                              }
                         }
                    }).catch(err => {
                         console.log(err);
                    })

                    //used to get retailer information
                    usermodel.getRetailerInfo(customerId).then(retailerInfo => {
                         console.log("retailer", retailerInfo)
                         if (retailerInfo != '') {
                              retailerInformation = retailerInfo;
                              let instance = config.MAIL_ENV;
                              toEmail = [];
                              subject = instance + ' New Retailer without Beat';
                              body = "<html> <h4>Hello ,<h4></br><p>New customer <strong> " + retailerInformation.business_legal_name + ' ' + '(' + retailerInformation.mobile_no + ') ' + 'Address: ' + retailerInformation.address1 + ', ' + retailerInformation.address2 + ', ' + retailerInformation.locality + ', ' + retailerInformation.city + ', ' + retailerInformation.state_name + ' - ' + retailerInformation.pincode + "</strong> onboarded please do the needful & Map the customer to the Respective Beat.</p></html>"

                         }
                    }).catch(err => {
                         console.log(err);
                    });

                    let noficationCodeForNewRetailerRegister = "BEAT001";
                    usermodel.getUsersByCode('BEAT001').then(userIdData => {
                         if (userIdData != '') {
                              usermodel.getUserEmailByIds(userIdData).then(userEmailArr => {
                                   if (userEmailArr != '' && userEmailArr.length > 0) {
                                        userEmailArr.forEach((element => {
                                             toEmail.push(element);
                                        }));
                                        usermodel.sendMail(subject, toEmail, body).then(info => {
                                             if (typeof info != 'undefined' && info != '') {
                                                  res.status(200).json({ 'status': 'success', 'message': 'Email send successfully' })
                                             } else {
                                                  res.status(200).json({ 'status': 'failed', 'message': 'Unable to send email' })
                                             }
                                        })
                                   }
                              }).catch(err => {
                                   console.log(err)
                              })

                         }
                    }).catch(err => {
                         console.log(err);
                    })
               }
          }).catch(err => {
               console.log(err)
          })


     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': 'Internal server error' })
     }
}



module.exports.updateBeat = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if ((typeof data.customer_token != 'undefined' && data.customer_token == '') || (typeof data.customer_token == 'undefined')) {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed })
               } else {
                    //validating customer_token
                    cartModel.validateToken(data.customer_token).then(validated => {
                         let customerToken = validated;
                         if (customerToken.token_status == 0) {
                              res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken })
                         } else {
                              let customerId = data.customer_id;
                              let beatId = data.beat_id;
                              if (beatId == '' || customerId == '' || beatId <= 0) {
                                   res.status(200).json({ 'status': 'failed', 'message': 'Beat id or Customer id can not be empty' })
                              } else {
                                   //fetching userId based on customer token
                                   cartModel.getUserId(data.customer_token).then(userId => {
                                        console.log("user_id", userId);
                                        let userID = typeof userId[0].user_id != 'undefined' ? userId[0].user_id : 0;
                                        //fetching hubId based on beatId
                                        cartModel.getHub(beatId).then(hubId => {
                                             console.log("hubid===>544", hubId);
                                             if (hubId == '' || hubId <= 0) {
                                                  res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyHubId });
                                             } else {
                                                  //fetching warehouseId based on hubid
                                                  cartModel.getWarehouseidByHub(hubId).then(le_wh_id => {
                                                       console.log("le_wh_id=====>551", le_wh_id);
                                                       if (le_wh_id == '') {
                                                            res.status(200).json({ 'status': 'failed', 'message': 'Warehouse details can not be empty' })
                                                       } else {
                                                            //updating customers and retailer_flat table
                                                            cartModel.updateBeat(userID, customerId, beatId, le_wh_id, hubId).then(updated => {
                                                                 let response = { 'hub_id': hubId, 'le_wh_id': le_wh_id };
                                                                 res.status(200).json({ 'status': 'success', 'message': 'Beat updated successfully', 'data': response })
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                            })
                                                       }
                                                  }).catch(err => {
                                                       console.log(err);
                                                       res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                  })
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                        })
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                   })
                              }
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                    })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

