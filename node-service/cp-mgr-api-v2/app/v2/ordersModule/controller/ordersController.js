


var ordersModel = require('../../ordersModule/model/ordersModel');
let cpMessage = require('../../../config/cpMessage');
var rp = require('request-promise');
var encrptionInstance = require('../../../config/encryption');



/*
purpose : Used to create new order placed by retailes or ff.
request : Order detailes
resposne  :Placed order details
Author : Deepak tiwari
*/
function placeOrder(data, res) {
     let customerId;
     let customerType;
     let salesRepId;
     let userEcash;
     let customerUserId;
     let customerParentId;
     let succesOrdersCount;
     let currentEcash;
     let userCreditLimit;
     let undeliveredValue;
     let is_self;
     let isStockist;
     let creditLimit;
     let totalPrice;
     let orderStatus;
     let minimumOrderValue;
     let deliveryDate;
     let scheduledDeliveryDate;
     let cart;
     let segmentId;//segment_id
     let legal_entity_id;
     let addressId; //address_id
     let longitude;
     let latitude;
     let mfc_id;
     let address;
     let landmark;
     let locality;
     let orderLevelCashback;
     let areaId;
     let Data;
     let randomNumber;
     let gdsPostInfo = {};
     let totalAmount;
     let logsArray = { 'data': data, 'legal_entity_id': data.legal_entity_id, 'order_code ': data.orderId };
     is_self = (typeof data.sales_token != 'undefined' && data.sales_token != '') ? 0 : 1;
     if (typeof data.sales_token != 'undefined' && data.sales_token != '') {
          //Fetching ff customer id
          ordersModel.getcustomerId(data.sales_token).then(sales_rep_id => {
               if (sales_rep_id != '') {
                    salesRepId = sales_rep_id;
               } else {
                    salesRepId = '';
               }
               console.log("======>53", salesRepId)
          }).catch(err => {
               console.log(err)
          })
     } else {
          salesRepId = 0;
     }

     //fetching retailer customer id 
     ordersModel.getcustomerId(data.customer_token).then(customer_id => {
          if (customer_id != '') {
               customerId = customer_id;
          } else {
               customerId = '';
          }
          console.log("=====>68", customerId)
     }).catch(err => {
          console.log(err)
     })

     //Customer_type
     if (typeof data.customer_type != 'undefined' && data.customer_type != '') {
          customerType = data.customer_type;
     } else {
          ordersModel.getUserCustomerType(data.legal_entity_id).then(customer_type => {
               if (customer_type != '') {
                    customerType = customer_type;
               } else {
                    customerType = '';
               }
          }).catch(err => {
               console.log(err)
          })
     }

     //pending
     ordersModel.getSuccessOrderCount(data.legal_entity_id).then(order_success_count => {
          if (order_success_count != '') {
               succesOrdersCount = order_success_count;
          } else {
               succesOrdersCount = '';
          }
          console.log("SUccess", succesOrdersCount)
     }).catch(err => {
          console.log(err)
     })


     //fetching customer parent ID
     ordersModel.getParentCustId(data.legal_entity_id).then(cus_parent_id => {
          if (cus_parent_id != '') {
               customerParentId = cus_parent_id;
          } else {
               customerParentId = '';
          }
          customerUserId = (typeof customerParentId.cust_user_id != 'undefined') ? customerParentId.cust_user_id : customerId;
          //users ecash details
          ordersModel.getUserEcash(customerUserId).then(user_ecash => {
               if (user_ecash != '') {
                    userEcash = user_ecash;
               } else {
                    userEcash = '';
               }
               //calculating CurrentEcash available for that customer (Based on cashback amount)
               currentEcash = (typeof userEcash.cashback != 'undefined') ? userEcash.cashback : 0;
               userCreditLimit = (typeof userEcash.creditlimit != 'undefined') ? userEcash.creditlimit : 0;
               ordersModel.custUnDeliveredOrderValue(data.legal_entity_id).then(undeliveredvalue => {//calculating customer undelivered orderValue
                    if (typeof undeliveredvalue != 'undefined') {
                         undeliveredValue = undeliveredvalue;
                    } else {
                         undeliveredValue = '';
                    }
               }).catch(err => {
                    console.log(err)
               })
               //checking  stockist
               ordersModel.checkStockist(data.legal_entity_id).then(stockist => {
                    if (stockist != '') {
                         isStockist = stockist;
                    } else {
                         isStockist = '';
                    }
                    currentEcash += (isStockist > 0) ? userCreditLimit : 0;

               }).catch(err => {
                    console.log(err)
               })

               //validating user credit limit with totalprice
               if (typeof data.type != 'undefined' && data.type == 'mfc') {
                    creditLimit = typeof data.credit_limit != 'undefined' ? (data.credit_limit) : 0;
                    totalPrice = typeof data.total_price != 'undefined' ? (data.total_price) : 0;
                    //comparing usercreditlimit with total order price
                    if (totalPrice > creditLimit) {
                         let difference = creditLimit - totalPrice;
                         res.status(200).json({ 'status': 'failed', 'message': 'We are unable to process your order. You have exceeded the credit limit', 'data': difference })
                    }
                    orderStatus = 'NewFC';
               } else {
                    orderStatus = 'New';
               }

               //verifing weather customer have cleared  payments.
               if (currentEcash < 0) { //&& data.payment_mode = 'loc'
                    // res.status(200).json({ 'status': 'failed', 'message': 'Please clear your previous order payments to continue' })
               }

               //If any undelivered items available
               if (undeliveredValue > 0) {
                    res.status(200).json({ 'status': 'failed', 'message': 'You already have some undelivered orders in your cart' })
               }


               if ((data.total + undeliveredValue) > userCreditLimit && data.paymentmode == 'loc') {
                    res.status(200).json({ 'status': 'failed', 'message': "Order value should not be more than " + userCreditLimit + " with LOC" })
               }

          }).catch(err => {
               console.log(err)
          })

     }).catch(err => {
          console.log(err)
     })

     // To check the Minimum Order Value based on- Customer Type, Warehouse Id , Order Placed By (Self Order or FF Order)
     ordersModel.getCustomertypeMinorderValue(customerType, data.le_wh_id, salesRepId).then(mov_details => {
          if (mov_details != '') {
               minimumOrderValue = mov_details;
          } else {
               minimumOrderValue = '';
          }

          //Only for ebutor premium club order
          if (customerType == 3013 && data.total < minimumOrderValue) {
               res.status(200).json({ 'status': 'failed', 'message': "Minimum order value should be " + minimumOrderValue + " Rupees" })
               // for all type of orders
          } else if (succesOrdersCount >= minimumOrderValue.mov_ordercount && data.total < minimumOrderValue) {
               res.status(200).json({ 'status': 'failed', 'message': "Minimum order value should be " + minimumOrderValue + " Rupees" })
          }
     }).catch(err => {
          console.log(err)
     })

     // validating selected Scheduled Delivery Date is not holiday list 
     if (typeof data.scheduled_delivery_date != 'undefined' && data.scheduled_delivery_date != '') {
          let deldate = data.scheduled_delivery_date;
          deliveryDate = deldate.substring(0, 10);
     } else {
          deliveryDate = '';
     }

     ordersModel.ScheduledDeliveryDate(deliveryDate).then(scheduled_delivery_date => {
          if (scheduled_delivery_date != '') {
               scheduledDeliveryDate = scheduled_delivery_date;
          } else {
               scheduledDeliveryDate = '';
          }

     }).catch(err => {
          console.log(err)
     })

     /*---------------------------------------------fetching cart details---------------------------------------------------------- */
     ordersModel.Cartdetails(data).then(cartDetails => {
          if (cartDetails != '') {
               cart = cartDetails;
          } else {
               cart = '';
          }

          //cart logs
          logsArray = { 'data': data, 'legal_entity_id': data.legal_entity_id, 'order_code ': data.orderId, 'cart_data': cart }

     }).catch(err => {
          console.log(err)
     })


     segmentId = (typeof data.segment_id != 'undefined' && data.segment_id != '') ? data.segment_id : 0;
     legal_entity_id = (typeof data.legal_entity_id != 'undefined' && data.legal_entity_id != '') ? data.legal_entity_id : '';
     addressId = (typeof data.address_id != 'undefined' && data.address_id != '') ? data.address_id : '';
     latitude = (typeof data.latitude != 'undefined' && data.latitude != '') ? data.latitude : 0;
     longitude = (typeof data.longitude != 'undefined' && data.longitude != '') ? data.longitude : 0;
     mfc_id = (typeof data.mfc != 'undefined' && data.mfc != '') ? data.mfc : 0;
     //fetching shippingAddress
     ordersModel.getShippingAddress(addressId, legal_entity_id).then(customerAddress => {
          if (customerAddress != '') {
               address = customerAddress[0];
          } else {
               address = '';
          }
          //landmark & locality
          landmark = (typeof address.landmark != 'undefined' && address.landmark != '') ? address.landmark : '';
          locality = (typeof address.locality != 'undefined' && address.locality != '') ? address.locality : '';
          orderLevelCashback = (typeof data.order_level_cashback != 'undefined' && data.order_level_cashback != '') ? data.order_level_cashback : '';
          //area_id
          ordersModel.GetAreaID(address.legal_entity_id).then(area_id => {
               if (area_id != '') {
                    areaId = area_id;
               } else {
                    areaId = area_id;
               }

               //validating address details
               if (address.pin != '') {
                    if (address.Firstname != '') {
                         if (address.Address != '') {
                              if (address.telephone != '') {
                                   if (address.state != '') {
                                        if (address.country != '') {
                                             ordersModel.getCustomerData(data.customer_token).then(customerData => {
                                                  if (customerData != '') {
                                                       Data = JSON.parse(JSON.stringify(customerData));
                                                       randomNumber = Math.floor(Math.random() * 10000000000);
                                                  } else {
                                                       Data = ''
                                                       res.status(200).json({ 'status': 'fialed', 'message': "Inactive token" })
                                                  }

                                                  //gdsportINfo
                                                  gdsPostInfo = Object.assign(gdsPostInfo, {
                                                       'customer_info': {
                                                            'suffix': '',
                                                            'first_name': Data.firstname,
                                                            'middle_name': '',
                                                            'last_name': Data.lastname,
                                                            'email_address': Data.email,
                                                            'channel_user_id': Data.customerId,
                                                            'cust_le_id': address.legal_entity_id,
                                                            'mobile_no': Data.telephone, 'dob': '',
                                                            'channel_id': process.env.channelId,
                                                            'gender': '',
                                                            'register_date': ''
                                                       },
                                                       'address_info': {
                                                            0: {
                                                                 'address_type': 'shipping',
                                                                 'first_name': address.Firstname,
                                                                 'middle_name': '',
                                                                 'last_name': '',
                                                                 'email': address.email,
                                                                 'address1': address.Address,
                                                                 'address2': address.Address1,
                                                                 'landmark': landmark,
                                                                 'locality': locality,
                                                                 'city': address.City,
                                                                 'state': address.state,
                                                                 'phone': address.telephone,
                                                                 'pincode': address.pin,
                                                                 'country': address.country,
                                                                 'company': Data.company,
                                                                 'area_id': area_id,
                                                                 'mobile_no': ''
                                                            },
                                                            1: {
                                                                 'address_type': 'billing',
                                                                 'first_name': address.Firstname,
                                                                 'middle_name': '',
                                                                 'last_name': '',
                                                                 'email': address.email,
                                                                 'address1': address.Address,
                                                                 'address2': address.Address1,
                                                                 'landmark': landmark,
                                                                 'locality': locality,
                                                                 'city': address.City,
                                                                 'state': address.state,
                                                                 'phone': address.telephone,
                                                                 'pincode': address.pin,
                                                                 'country': address.country,
                                                                 'company': Data.company,
                                                                 'area_id': area_id,
                                                                 'mobile_no': ''

                                                            }

                                                       }
                                                  });

                                                  console.log("=======> 363")
                                                  //Discount
                                                  let dbDiscountAmount = 0;
                                                  let totalDiscountAmount = 0;
                                                  let discount = "";
                                                  let discountType = "";
                                                  let orderDiscount = 0.00;
                                                  let orderDiscountAmount = 0.00;
                                                  let orderDiscountOnValues = '';
                                                  let orderDiscountOn = '';
                                                  let orderDiscountType = '';
                                                  salesToken = typeof data.sales_token != 'undefined' ? data.sales_token : '';
                                                  if (salesToken == '') {
                                                       //peice of code used to check discount 
                                                       orderDiscount = typeof data.discount != 'undefined' ? parseFloat(data.discount) : 0.00;
                                                       orderDiscountType = typeof data.discount_type != 'undefined' ? data.discount_type : '';
                                                       orderDiscountOn = typeof data.discount_on != 'undefined' ? data.discount_on : '';
                                                       orderDiscountOnValues = typeof data.discount_on_values != 'undefined' ? parseFloat(data.discount_on_values) : 0.00;
                                                       orderDiscountAmount = typeof data.discountAmount != 'undefined' ? parseFloat(data.discountAmount) : 0;
                                                       if (orderDiscount > 0 && orderDiscountType != '' && orderDiscountOn != '' && orderDiscountOnValues > 0) {
                                                            //checking weather discount is applicable or not
                                                            ordersModel.isDiscountApplicable(orderDiscount, orderDiscountOn, orderDiscountType, orderDiscountOnValues).then(discountStatus => {
                                                                 if (discountStatus) {
                                                                      totalAmount = typeof data.total != 'undefined' ? parseFloat(data.total) : 0.00;
                                                                      //if there is order level discount in the table
                                                                      if (orderDiscountType == "percentage" && totalAmount > 0) {
                                                                           dbDiscountAmount = orderDiscount * parseFloat(totalAmount) / 100;
                                                                      } else if (orderDiscountType == "value" && totalAmount > 0 && parseFloat(totalAmount) > orderDiscount) {
                                                                           dbDiscountAmount = orderDiscount;
                                                                      }
                                                                      dbDiscountAmount = (dbDiscountAmount < 0) ? 0 : dbDiscountAmount;
                                                                 } else {
                                                                      res.status(200).json({ 'status': '-3', 'message': 'Discount promotion has been Expired. Please update the Cart.' })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err)
                                                            })
                                                       }

                                                       totalDiscountAmount = dbDiscountAmount;

                                                  }

                                                  let cartDetails = cart;
                                                  console.log("cardetails", cartDetails);
                                                  console.log("cart", cart);
                                                  let placedProducts = [];
                                                  if (cartDetails != '') {
                                                       cartDetails.forEach((cartdata) => {
                                                            let esu_quantity = (typeof cartdata.esu_quantity != 'undefined' && cartdata.esu_quantity != '') ? cartdata.esu_quantity : '';
                                                            let star = (typeof cartdata.star != 'undefined' && cartdata.star != '') ? cartdata.star : 0;
                                                            let product_id = typeof cartdata.product_id != 'undefined' ? cartdata.product_id : '';
                                                            let wh_id = typeof cartdata.le_wh_id_list != 'undefined' ? cartdata.le_wh_id_list : '';
                                                            let quantity = typeof cartdata.quantity != 'undefined' ? cartdata.quantity : 0;
                                                            //checkng is there is any free item available with that product
                                                            let isFreebie;
                                                            let wareHouseId;
                                                            let availQuantity;
                                                            let displayMode;
                                                            let query;
                                                            //fetching warehouse id

                                                            ordersModel.getwareHouseId(product_id, wh_id, segmentId).then(wareHouseid => {
                                                                 if (wareHouseid != '') {
                                                                      wareHouseId = wareHouseid;
                                                                 } else {
                                                                      wareHouseId = '';
                                                                 }

                                                                 let le_wh_id = typeof wareHouseId[0].le_wh_id != 'undefined' ? wareHouseId[0].le_wh_id : 0;
                                                                 if (customerType == 3016) {
                                                                      //fetching available quantity from inventory based on product_id
                                                                      query = "select (dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty from inventory where product_id =" + product_id + " and le_wh_id = " + le_wh_id;
                                                                      ordersModel.Query(query).then(queryData => {
                                                                           if (queryData != '') {
                                                                                availQuantity = typeof queryData[0].availQty != 'undefined' ? queryData[0].availQty : 0;
                                                                                if (quantity > (availQuantity + quantity)) {
                                                                                     res.status(200).json({ 'status': '0', 'message': cpMessage.lowinventory })
                                                                                }
                                                                           }
                                                                      }).catch(err => {
                                                                           console.log(err)
                                                                      })
                                                                 } else {
                                                                      //get inventory displace mode weather it is soh , available stoke
                                                                      let checkinventory = "select inv_display_mode from inventory  where product_id = " + product_id + " && le_wh_id = " + le_wh_id;
                                                                      ordersModel.Query(checkinventory).then(checkInventory => {
                                                                           if (checkInventory != '') {
                                                                                displayMode = typeof checkInventory[0].inv_display_mode != 'undefined' ? checkInventory[0].inv_display_mode : 'soh';
                                                                                query = "select ( " + displayMode + "-(order_qty+reserved_qty)) as availQty from inventory where product_id = " + product_id + " and le_wh_id = " + le_wh_id;
                                                                                ordersModel.Query(query).then(queryData => {
                                                                                     if (queryData != '') {
                                                                                          availQuantity = typeof queryData[0].availQty != 'undefined' ? queryData[0].availQty : 0;
                                                                                          //checking low inventory condition
                                                                                          if (quantity > (availQuantity + quantity)) {
                                                                                               res.status(200).json({ 'status': '0', 'message': cpMessage.lowinventory })
                                                                                          }
                                                                                     }
                                                                                }).catch(err => {
                                                                                     console.log(err)
                                                                                })

                                                                           }
                                                                      }).catch(err => {
                                                                           console.log(err)
                                                                      })
                                                                 }

                                                            }).catch(err => {
                                                                 console.log(err)
                                                            })

                                                            // Discount only for self orders and not for freebies
                                                            let isDiscountApplicable = false;
                                                            let discountError = [];
                                                            let discountOn;
                                                            let request = {};
                                                            let discountAmount;

                                                            //get freebie
                                                            ordersModel.isFreebie(product_id).then(freeBie => {
                                                                 isFreebie = freeBie;
                                                                 console.log("salesToken , isFreebie", salesToken, isFreebie);
                                                                 if (salesToken == '' && !isFreebie) {//salesToken == '' && !isFreebie
                                                                      discount = (typeof cartdata.discount != 'undefined' && cartdata.discount != '') ? cartdata.discount : 0.00;
                                                                      discountType = (typeof cartdata.discount_type != 'undefined' && cartdata.discount_type != '') ? cartdata.discount_type : '';
                                                                      discountOn = (typeof cartdata.discount_on != 'undefined' && cartdata.discount_on != '') ? cartdata.discount_on : '';
                                                                      discountAmount = (typeof cartdata.discount_amount != 'undefined ' && cartdata.discount_amount != '') ? cartdata.discount_amount : 0.00;
                                                                      if (discount != 0.00 && discountType != '' && discountOn != '') {
                                                                           let isDiscount = 0;
                                                                           //used to validate discount
                                                                           totalPrice = typeof cartdata.prodtotal != 'undefined' ? cartdata.prodtotal : 0;
                                                                           star = typeof CharacterData.star != 'undefined' ? cartdata.star : null;
                                                                           ordersModel.isDiscountApplicable(discount, discountOn, discountType, star).then(discounts => {
                                                                                console.log("discount status", discounts)
                                                                                if (discounts || data.ignore_discount_check && data.ignore_discount_check == 1) {
                                                                                     //if there is order level discount in the table
                                                                                     if (discountType == "percentage") {
                                                                                          discountAmount = discount * parseFloat(totalPrice) / 100;
                                                                                     } else if (orderDiscountType == "value" && parseFloat(totalPrice) > discount) {
                                                                                          discountAmount = discount;
                                                                                          discountAmount = (discountAmount < 0) ? 0 : discountAmount;
                                                                                          totalDiscountAmount += discountAmount;
                                                                                          console.log("totalDiscount == >516", totalDiscountAmount)
                                                                                     }
                                                                                } else {
                                                                                     res.status(200).json({ 'status': '-3', 'message': 'Discount promotion has been Expired. Please update the Cart.' })
                                                                                }
                                                                           }).catch(err => {
                                                                                console.log(err)
                                                                           })

                                                                      }
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err)
                                                            })


                                                            if (orderDiscountAmount != 0 && totalDiscountAmount != 0 && orderDiscountOn == "order" && (totalDiscountAmount.toPrecision(12) != orderDiscountAmount.toPrecision(12))) {
                                                                 // ERROR: Invalid Order Value Discount
                                                                 res.status(200).json({ 'status': '-3', 'message': 'Discount promotion has been Expired. Please update the Cart.' })
                                                            }

                                                            //order information
                                                            let current_datetime = new Date();
                                                            let formatted_date = current_datetime.getDate() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getFullYear();
                                                            let odpCounter = 0;
                                                            let packs;
                                                            ordersModel.getProdPacks(cartdata.cart_id, cartdata.product_id).then(productPacks => {
                                                                 if (productPacks != '') {
                                                                      packs = JSON.parse(JSON.stringify(productPacks[0]));
                                                                 } else {
                                                                      packs = '';
                                                                 }
                                                                 //gdsorder post information
                                                                 gdsPostInfo = Object.assign(gdsPostInfo, {
                                                                      'product_info': [
                                                                           odpCounter = {
                                                                                'sku': cartdata.sku,
                                                                                'le_wh_id': cartdata.le_wh_id,
                                                                                'hub_id': cartdata.hub,
                                                                                'channelId': process.env.channelId,
                                                                                'order_id': '',
                                                                                'channelitemid': cartdata.product_id,
                                                                                'scoitemid': cartdata.product_id,
                                                                                'parent_id': cartdata.parent_id,
                                                                                'quantity': (customerType == 3016 && typeof cartdata.dit_quantity != 'undefined') ? cartData.dit_quantity : cartdata.quantity,
                                                                                'esu_quantity': esu_quantity,
                                                                                'price': cartdata.mrp,
                                                                                'sellprice': cartdata.rate,
                                                                                'discounttype': '',
                                                                                'discountprice': '',
                                                                                'tax': '',
                                                                                'subtotal': cartdata.prodtotal,
                                                                                'channelcancelitem': cartdata.rate,
                                                                                'total': cartdata.prodtotal,
                                                                                'company': Data.company,
                                                                                'servicename': '',
                                                                                'servicecost': '',
                                                                                'dispatchdate': '',
                                                                                'mintimetodispatch': '',
                                                                                'maxtimetodispatch': '',
                                                                                'timeunits': '',
                                                                                'star': cartdata.star,
                                                                                'discount': (salesToken == '' && !isFreebie) ? discount : 0,
                                                                                'discount_type': (salesToken == '' && !isFreebie) ? discountType : '',
                                                                                'discount_on': (salesToken == '' && !isFreebie) ? discountOn : '',
                                                                                'discount_amount': (salesToken == '' && !isFreebie) ? discountAmount : 0.00,
                                                                                'product_slab_id': cartdata.product_slab_id,
                                                                                'prmt_det_id': cartdata.prmt_det_id,
                                                                                'freebee_qty': cartdata.freebee_qty,
                                                                                'freebee_mpq': cartdata.freebee_mpq,
                                                                                'packs': packs
                                                                           }
                                                                      ]
                                                                 });
                                                                 odpCounter++;

                                                                 console.log("product info 564", gdsPostInfo)
                                                                 gdsPostInfo = Object.assign(gdsPostInfo, {
                                                                      'order_info': {
                                                                           'channelid': process.env.channelId,
                                                                           'channelorderid': randomNumber,
                                                                           'orderstatus': orderStatus,
                                                                           'orderdate': formatted_date,
                                                                           'paymentmethod': data.paymentmode,
                                                                           'shippingcost': '',
                                                                           'subtotal': data.total,
                                                                           'tax': ' ',
                                                                           'totalamount': data.total,
                                                                           'discounttype': (salesToken == '' && totalDiscountAmount != 0) ? orderDiscountType : '',
                                                                           'discount': (salesToken == '' && totalDiscountAmount != 0) ? orderDiscount : 0.00,
                                                                           'discountamount': (salesToken == '' && totalDiscountAmount != 0) ? totalDiscountAmount : 0,
                                                                           'grandtotal': parseFloat(data.final_amount),
                                                                           'currencycode': 'INR',
                                                                           'channelorderstatus': 'New',
                                                                           'updateddate': formatted_date,
                                                                           'gdsorderid': '',
                                                                           'channelcustid': Data.customerId,
                                                                           'is_self': is_self,
                                                                           'customer_type': customerType,
                                                                           'createddate': formatted_date,
                                                                           'order_level_cashback': orderLevelCashback,
                                                                           'mfc_id': mfc_id,
                                                                           'discount_on_tax_less': typeof data.discount_on_tax_less != 'undefined' ? data.discount_on_tax_less : 0,
                                                                           'instant_wallet_cashback': typeof data.instant_wallet_cashback != 'undefined' ? data.instant_wallet_cashback : 0,
                                                                           "trade_dis_cashback_applied": typeof data.trade_dis_cashback_applied != 'undefined' ? data.trade_dis_cashback_applied : 0,
                                                                           "trade_discount_ids": typeof data.trade_discount_ids != 'undefined' ? data.trade_discount_ids : "",
                                                                           'auto_invoice': typeof data.auto_invoice != 'undefined' ? data.auto_invoice : 0
                                                                      }
                                                                 })

                                                                 //payment mode 
                                                                 let wCollectTxnId = '';
                                                                 let merchTranId = '';
                                                                 let payment_modes = ['cod', 'CnC', 'loc', 'MFC']; //cod- cash on delivery , loc- line of credit
                                                                 if (payment_modes.indexOf(data.paymentmode) != -1) {//true
                                                                      wCollectTxnId = null;
                                                                      merchTranId = null;
                                                                 } else if (data.paymentmode == 'upi') {
                                                                      wCollectTxnId = data.wCollectTxnId;
                                                                      merchTranId = data.merchTranId;
                                                                 } else {
                                                                      console.log({ 'status': 'failed', 'message': 'Please check your payment detailes' })
                                                                 }
                                                                 console.log("paymentmode", payment_modes.indexOf(data.paymentmode), data.paymentmode)
                                                                 //payment information

                                                                 gdsPostInfo = Object.assign(gdsPostInfo, {
                                                                      'payment_info': {
                                                                           0: {
                                                                                'order_id': data.orderId,
                                                                                'channelid': process.env.channelId,
                                                                                'paymentmethod': data.paymentmode,
                                                                                'paymentstatus': 'Pending',
                                                                                'paymentcurrency': 'INR',
                                                                                'amount': data.final_amount,
                                                                                'buyeremail': address.email,
                                                                                'buyername': address.Firstname,
                                                                                'buyerphone': address.telephone,
                                                                                'transactionId': wCollectTxnId,
                                                                                'merchTranId': merchTranId,
                                                                                'paymentDate': current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
                                                                           }
                                                                      }
                                                                 })

                                                                 salesToken = (typeof data.sales_token != 'undefined' && data.sales_token != '') ? data.sales_token : '';
                                                                 let platform_id = (typeof data.platform_id != 'undefined' && data.platform_id != '') ? data.platform_id : '';
                                                                 let pref_value = (typeof data.pref_value != 'undefined' && data.pref_value != '') ? data.pref_value : '';
                                                                 let pref_value1 = (typeof data.pref_value1 != 'undefined' && data.pref_value1 != ' ') ? data.pref_value1 : '';
                                                                 let created_by = (typeof data.sales_token != 'undefined' && data.sales_token != '') ? salesRepId : customerId;

                                                                 //address information
                                                                 gdsPostInfo = Object.assign(gdsPostInfo, {
                                                                      'additional_info': {
                                                                           'cart_id': data.cartId,
                                                                           'customer_token': data.customer_token,
                                                                           'sales_token': salesToken,
                                                                           'platform_id': platform_id,
                                                                           'company': Data.company,
                                                                           'preferred_delivery_slot1': pref_value,
                                                                           'preferred_delivery_slot2': pref_value1,
                                                                           'scheduled_delivery_date': data.scheduled_delivery_date,
                                                                           'sms_content': "Thank you, your order has been placed successfully. Your order number is " + data.orderId + " and your order will be shipped within 3 days.",
                                                                           'customer_id': customerId,
                                                                           'sales_rep_id': salesRepId,
                                                                           'latitude': latitude,
                                                                           'longitude': longitude,
                                                                           'activity': 107001,
                                                                           'created_by': created_by

                                                                      }

                                                                 })

                                                                 let orderDataReq = JSON.stringify(gdsPostInfo);
                                                                 console.log(orderDataReq)
                                                                 let url;
                                                                 let config = {};
                                                                 let message;
                                                                 let httpStatusCode;
                                                                 //order logs
                                                                 logsArray = { 'data': data, 'legal_entity_id': data.legal_entity_id, 'order_code ': data.orderId, 'order_req': orderDataReq };
                                                                 request = { 'parameter': logsArray, 'apiUrl': 'orderlogs' }
                                                                 ordersModel.OrderApiRequests(request);
                                                                 //calling dmapi for placing an order
                                                                 ordersModel.getHostURL().then(HostUrl => {
                                                                      if (HostUrl != '') {
                                                                           url = 'http://' + HostUrl + '/dmapi/v2/placeorder';
                                                                           config = { 'api_key': process.env.GDSAPIKey, 'secret_key': process.env.GDSAPISECRETKey, 'orderdata': orderDataReq }
                                                                           var options = {
                                                                                method: 'POST',
                                                                                uri: url,
                                                                                body: { 'api_key': process.env.GDSAPIKey, 'secret_key': process.env.GDSAPISECRETKey, 'orderdata': orderDataReq },
                                                                                json: false // Automatically stringifies the body to JSON
                                                                           };


                                                                           rp(options)
                                                                                .then(function (parsedBody) {
                                                                                     // POST succeeded...
                                                                                     console.log('parsePost', parsedBody)
                                                                                     if (parsedBody.Status == 1) {
                                                                                          if (customerType == 3015) {
                                                                                               message = 'Thank you for your Cash-n-Carry order. Please visit Ebutor CnC counter to pick up your items.';
                                                                                               res.status(200).json({ 'status': 'success', 'message': message, 'data': { 'orderId': data.orderId }, 'orderAmount': Math.ceil(data.final_amount) })
                                                                                          } else {
                                                                                               if (cartdata != "" && typeof data.po_id != 'undefined') {
                                                                                                    // here we are updating po so status flag in table since order is placed succesfully entered to queue!
                                                                                                    ordersModel.updateStockistOrderStatus(data.po_id, data.orderId, 1).then(update => {
                                                                                                         if (update != '') {
                                                                                                              console.log("updated successfully")
                                                                                                         }
                                                                                                    }).catch(err => {
                                                                                                         console.log(err);
                                                                                                    })
                                                                                               }
                                                                                               message = 'Your Order has been placed successfully and will receive confirmation to your register mobile number';
                                                                                               res.status(200).json({ 'status': 'success', 'message': message, 'data': { 'orderId': data.orderId }, 'orderAmount': Math.ceil(data.final_amount) })
                                                                                          }
                                                                                          if (typeof data.sales_token != 'undefined' && data.sales_token != '') {
                                                                                               ordersModel.UpdateCheckoutFfComments(data.sales_token, customerId, 107001, latitude, longitude).then(updated => {
                                                                                                    if (updated != '') {
                                                                                                         console.log("updated successfully 2")
                                                                                                    }
                                                                                               }).catch(err => {
                                                                                                    console.log(err)
                                                                                               })
                                                                                          } else {
                                                                                               let delete_query = "delete from offline_cart_details where cust_id =" + customerId;
                                                                                               ordersModel.Query(delete_query).then(deleted => {
                                                                                                    if (deleted != '') {
                                                                                                         console.log("deleted successfully")
                                                                                                    }
                                                                                               })
                                                                                          }

                                                                                          if (typeof data.sales_token != 'undefined' && data.sales_token != '') {
                                                                                               if ((typeof data.latitude != 'undefined' && data.latitude != '') && (typeof data.longitude != 'undefined' && data.longitude != '')) {
                                                                                                    ordersModel.FFLogsUpdate_new(data).then(log_update => {
                                                                                                         if (log_update != '') {
                                                                                                              console.log("updated successfully 3")
                                                                                                         }
                                                                                                    }).catch(err => {
                                                                                                         console.log(err)
                                                                                                    })
                                                                                               } else {
                                                                                                    ordersModel.FFLogsUpdate(data.sales_token, data.customer_token).then(log_update => {
                                                                                                         if (log_update != '') {
                                                                                                              console.log("updated successfully 3")
                                                                                                         }
                                                                                                    }).catch(err => {
                                                                                                         console.log(err)
                                                                                                    })
                                                                                               }
                                                                                          }


                                                                                     } else if (currentEcash > 0 || undeliveredValue < 0) {
                                                                                          res.status(200).json({ 'status': 'success', 'message': message, 'data': { 'orderId': data.orderId }, 'orderAmount': data.final_amount })
                                                                                     }

                                                                                })
                                                                                .catch(function (err) {
                                                                                     // POST failed...
                                                                                     console.log(err.message)
                                                                                });

                                                                      }
                                                                 }).catch(err => {
                                                                      console.log(err)
                                                                 })
                                                            }).catch(err => {
                                                                 console.log(err)
                                                            })
                                                       })
                                                  } else {
                                                       res.status(200).json({ 'status': '0', 'message': 'Cart Refreshed' })
                                                  }

                                             }).catch(err => {
                                                  console.log(err)
                                             });


                                        } else {
                                             console.log({ 'status': "failed", 'message': "Shipping Country is empty" })
                                        }

                                   } else {
                                        console.log({ 'status': "failed", 'message': "Shipping State is empty" })
                                   }
                              } else {
                                   console.log({ 'status': "failed", 'message': "Shipping telephone is empty" })
                              }
                         } else {
                              console.log({ 'status': "failed", 'message': "Shipping Address is empty" })
                         }
                    } else {
                         console.log({ 'status': "failed", 'message': "Shipping Firstname is empty" })
                    }
               } else {
                    console.log({ 'status': 'failed', 'message': 'Please enter pincode ' })
               }
          }).catch(err => {
               console.log(err)
          })
     }).catch(err => {//shipping address cache
          console.log(err)
     })
}
//create new order
module.exports.createOrder = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);

               //validating token 
               let salesToken;
               let customerToken;
               if (typeof data.sales_token != 'undefined' && data.sales_token != '' && typeof data.customer_token != 'undefined' && data.customer_token != '') {
                    if ((data.sales_token != data.customer_token)) {
                         ordersModel.validateToken(data.sales_token).then(sales_token => {
                              salesToken = sales_token;
                              //if condition  typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1
                              if ((typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1)) {
                                   console.log("place order api")
                                   placeOrder(data, res);//used to place order
                              } else {
                                   res.status(200).json({ 'status': 'session', 'message ': 'You have already logged into the Ebutor system' })
                              }

                         }).catch(err => {
                              console.log(err)
                         })
                    } else {
                         res.status(200).json({ 'status': "failed", 'message': "Field Force is not authorised to place Order by himself, so please select retailer" })
                    }
               } else {
                    console.log("elese condition", salesToken)
                    ordersModel.validateToken(data.customer_token).then(customers_token => {
                         console.log("customer_Token", customers_token);
                         customerToken = customers_token;
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              placeOrder(data, res);//used to place order
                         } else {
                              res.status(200).json({ 'status': 'session', 'message ': 'You have already logged into the Ebutor system' })
                         }
                    }).catch(err => {
                         console.log(err)
                    })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Please pass required parameter ' })
          }
     } catch (err) {
          console.log(err)
          res.status(200).json({ 'status': 'failed', 'message': 'Internal server error' })
     }
}

/*
purpose : Used to get customer orders
request : Order detailes
resposne  :Placed order details
Author : Deepak tiwari
*/
module.exports.getOrders = async function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let salesToken;
               let customerToken;
               let salesRepId;
               let customerId;
               let orderDetails;
               if (typeof data.sales_token != 'undefined' && data.sales_token != '' && typeof data.customer_token != 'undefined' && data.customer_token != '') {
                    console.log("if condition")
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         console.log("sales_token", sales_token);
                         salesToken = sales_token;
                         if ((typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1)) {
                              let legalEntityId = (typeof data.legal_entity_id != 'undefined' && data.legal_entity_id != '') ? data.legal_entity_id : '';
                              let offset = (typeof data.offset != 'undefined' && data.offset != '') ? data.offset : 0;
                              let offsetLimit = (typeof data.offset_limit != 'undefined' && data.offset_limit != '') ? data.offset_limit : '';
                              let statusId = (typeof data.status_id != 'undefined' && data.status_id != '') ? data.status_id : '';
                              if (typeof data.sales_token != 'undefined' && data.sales_token != '' && typeof data.customer_token != 'undefined' && data.customer_token != '') {
                                   ordersModel.getcustomerId(data.sales_token).then(async sales_rep_id => {
                                        if (sales_rep_id != '') {
                                             salesRepId = sales_rep_id;
                                        } else {
                                             salesRepId = '';
                                        }
                                        ordersModel.getcustomerId(data.customer_token).then(customer_id => {
                                             if (customer_id != '') {
                                                  customerId = customer_id;
                                             } else {
                                                  customerId = '';
                                             }
                                             //validation 
                                             if (data.sales_token != data.customer_token) {
                                                  ordersModel.getCustomerOrder(customerId, salesRepId, legalEntityId, offset, offsetLimit, statusId).then(order_details => {
                                                       if (order_details != '') {
                                                            orderDetails = order_details;
                                                       } else {
                                                            orderDetails = '';
                                                       }
                                                       if (orderDetails == '') {
                                                            res.status(200).json({ 'status': 'failed', 'message': 'No orders found' })
                                                       } else {
                                                            res.status(200).json({ 'status': 'success', 'message': 'Available orders', 'data': orderDetails.Result, 'count': orderDetails.totalOrderCount })
                                                       }
                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             } else {
                                                  console.log("inside this case")
                                                  //used to get customer role 
                                                  ordersModel.getTeamByUser(salesRepId).then(sales_id => {

                                                       if (sales_id != '') {
                                                            salesRepId = sales_id;
                                                       } else {
                                                            salesRepId = '';
                                                       }
                                                       ordersModel.getCustomerOrder('', salesRepId, legalEntityId, offset, offsetLimit, statusId).then(order_details => {
                                                            if (order_details != '') {
                                                                 orderDetails = order_details;
                                                            } else {
                                                                 orderDetails = '';
                                                            }
                                                            //resposne
                                                            if (orderDetails == '') {
                                                                 res.status(200).json({ 'status': 'failed', 'message': 'No orders found' })
                                                            } else {
                                                                 res.status(200).json({ 'status': 'success', 'message': 'Available orders', 'data': orderDetails.Result, 'count': orderDetails.totalOrderCount })
                                                            }
                                                       }).catch(err => {
                                                            console.log(err);
                                                       })
                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              } else {
                                   ordersModel.getcustomerId(data.customer_token).then(customer_id => {
                                        if (customer_id != '') {
                                             customerId = customer_id;
                                        } else {
                                             customerId = '';
                                        }
                                        ordersModel.getCustomerOrder(customerId, '', legalEntityId, offset, offsetLimit, statusId).then(order_details => {
                                             if (order_details != '') {
                                                  orderDetails = order_details;
                                             } else {
                                                  orderDetails = '';
                                             }
                                             //resposne
                                             if (orderDetails == '') {
                                                  res.status(200).json({ 'status': 'failed', 'message': 'No orders found' })
                                             } else {
                                                  res.status(200).json({ 'status': 'success', 'message': 'Available orders', 'data': orderDetails.Result, 'count': orderDetails.totalOrderCount })
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': "You have already logged into the Ebutor System" })
                         }
                    }).catch(err => {
                         console.log(err)
                    })
               } else {
                    console.log("elese condition", salesToken);
                    ordersModel.validateToken(data.customer_token).then(customers_token => {
                         console.log("customer_Token", customers_token);
                         customerToken = customers_token;
                         if ((typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1)) {
                              let legalEntityId = (typeof data.legal_entity_id != 'undefined' && data.legal_entity_id != '') ? data.legal_entity_id : '';
                              let offset = (typeof data.offset != 'undefined' && data.offset != '') ? data.offset : '';
                              let offsetLimit = (typeof data.offset_limit != 'undefined' && data.offset_limit != '') ? data.offset_limit : '';
                              let statusId = (typeof data.status_id != 'undefined' && data.status_id != '') ? data.status_id : '';
                              if (typeof data.sales_token != 'undefined' && data.sales_token != '' && typeof data.customer_token != 'undefined' && data.customer_token != '') {
                                   ordersModel.getcustomerId(data.salesToken).then(async sales_rep_id => {
                                        if (sales_rep_id != '') {
                                             salesRepId = sales_rep_id;
                                        } else {
                                             salesRepId = '';
                                        }
                                        ordersModel.getcustomerId(data.customer_token).then(customer_id => {
                                             if (customer_id != '') {
                                                  customerId = customer_id;
                                             } else {
                                                  customerId = '';
                                             }
                                             //validation (ff login)
                                             if (data.sales_token != data.customer_token) {
                                                  ordersModel.getCustomerOrder(customerId, salesRepId, legalEntityId, offset, offsetLimit, statusId).then(order_details => {
                                                       if (order_details != '') {
                                                            orderDetails = order_details;
                                                       } else {
                                                            orderDetails = '';
                                                       }
                                                       if (orderDetails == '') {
                                                            res.status(200).json({ 'status': 'failed', 'message': 'No orders found' })
                                                       } else {
                                                            res.status(200).json({ 'status': 'success', 'message': 'Available orders', 'data': orderDetails.Result, 'count': orderDetails.totalOrderCount })
                                                       }
                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             } else {
                                                  //used to get customer role (self login)
                                                  ordersModel.getTeamByUser(salesRepId).then(sales_id => {
                                                       if (sales_id != '') {
                                                            salesRepId = sales_id;
                                                       } else {
                                                            salesRepId = '';
                                                       }
                                                       ordersModel.getCustomerOrder('', salesRepId, legalEntityId, offset.offsetLimit, statusId).then(order_details => {
                                                            if (order_details != '') {
                                                                 orderDetails = order_details;
                                                            } else {
                                                                 orderDetails = '';
                                                            }
                                                            //resposne
                                                            if (orderDetails == '') {
                                                                 res.status(200).json({ 'status': 'failed', 'message': 'No orders found' })
                                                            } else {
                                                                 res.status(200).json({ 'status': 'success', 'message': 'Available orders', 'data': orderDetails.Result, 'count': orderDetails.totalOrderCount })
                                                            }
                                                       }).catch(err => {
                                                            console.log(err);
                                                       })
                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              } else {
                                   ordersModel.getcustomerId(data.customer_token).then(customer_id => {
                                        if (customer_id != '') {
                                             customerId = customer_id;
                                        } else {
                                             customerId = '';
                                        }
                                        ordersModel.getCustomerOrder(customerId, '', legalEntityId, offset.offsetLimit, statusId).then(order_details => {
                                             if (order_details != '') {
                                                  orderDetails = order_details;
                                             } else {
                                                  orderDetails = '';
                                             }
                                             //resposne
                                             if (orderDetails == '') {
                                                  res.status(200).json({ 'status': 'failed', 'message': 'No orders found' })
                                             } else {
                                                  res.status(200).json({ 'status': 'success', 'message': 'Available orders', 'data': orderDetails.Result, 'count': orderDetails.totalOrderCount })
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': "You have already logged into the Ebutor System" })
                         }
                    }).catch(err => {
                         console.log(err)
                    })

               }

               console.log("sales_token , customer_token", salesToken, customerToken);

          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Please pass required parameters' })
          }
     } catch (err) {
          console.log("error ===>917", err)
          res.json({ 'status': 'failed', 'message': 'Internal server error' });
     }
}

/*
purpose : This is dmapi which is used to cancel the orders through command line
request : Order detailes
resposne  : Require order details
Author : Deepak tiwari
*/
function cancelorderdmapi(array, res) {
     try {
          let gdsProduct;
          let Counter = 0;
          let salesToken = (typeof array.sales_token != 'undefined' && array.sales_token != '') ? array.sales_token : '';
          let data = { 'channel_order_id': array.orderID, 'order_id': array.orderID, 'channelId': process.env.channelId, 'sales_token': salesToken, 'customer_token': array.customer_token };
          if (typeof array.orderID != 'undefined' && typeof array.product_id != 'undefined') {
               ordersModel.getProdetails(array.orderID, array.product_id).then(resposne => {
                    if (resposne != '') {
                         gdsProduct = resposne;
                    } else {
                         gdsProduct = [];
                    }
                    let i = 0;
                    gdsProduct.forEach((gdsproduct) => {
                         //gdsorder post information
                         data = Object.assign(data, {
                              'product_info': [
                                   Counter = {
                                        'sku': gdsproduct.sku,
                                        'channelitemid': gdsproduct.product_id,
                                        'product_id': gdsproduct.product_id,
                                        'quantity': gdsproduct.qty,
                                        'cancel_reason_id': array.reason_id,
                                        'comments': array.comments
                                   }
                              ]
                         });
                         Counter++;
                    })


                    //email notification
                    ordersModel.getHostURL().then(HostUrl => {
                         if (HostUrl != '') {
                              let config = { 'api_key': process.env.CR_GDSAPIKey, 'secret_key': process.env.CR_GDSAPISECRETKey, 'orderdata': orderDataReq };
                              let url = 'http://' + HostUrl + '/dmapi/cancelOrder';
                              var options = {
                                   method: 'POST',
                                   uri: url,
                                   body: { 'api_key': process.env.CR_GDSAPIKey, 'secret_key': process.env.CR_GDSAPISECRETKey, 'orderdata': JSON.stringify(data) },
                                   json: true // Automatically stringifies the body to JSON
                              };

                              //calling dmapi using request node package
                              rp(options)
                                   .then(function (parsedBody) {
                                        if (parsedBody.Status == 1) {
                                             let orderStatus = "CANCELLED BY CUSTOMER";
                                             let result = {
                                                  'requestId': array.orderID,
                                                  'status': orderStatus
                                             }
                                             res.status(200).json({ 'status': "success", 'message': "Your order has been successfully cancelled", 'data': result })
                                             //code to fetch order code 
                                             let orderId;
                                             let orderStatusId = '17009';
                                             let orderHistory;
                                             ordersModel.getOrderCode(array.orderID).then(order_id => {
                                                  if (order_id != '') {
                                                       orderId = order_id;
                                                  }
                                                  //orderHistory 
                                                  ordersModel.orderhistory(array.orderID, data.product_info, orderStatus, orderStatusId).then(orderhistory => {
                                                       if (orderhistory != '') {
                                                            orderHistory = orderhistory;
                                                       }
                                                  }).catch(err => {
                                                       console.log(err)
                                                  })
                                             }).catch(err => {
                                                  console.log(err);
                                             })
                                        } else if (parsedBody.Status == 500) {
                                             res.status(200).json({ 'status': 'failed', 'message': 'Order is already cancelled' })
                                        } else if (parsedBody.Status == 0) {
                                             res.status(200).json({ 'status': 'failed', 'message': parsedBody.Message })
                                        } else if (parsedBody.Status == 404) {
                                             res.status(200).json({ 'status': 'failed', 'message': parsedBody.Message })
                                        } else {
                                             res.status(200).json({ 'status': 'failed', 'message': "Order Cancellation is Unsuccessful" })
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
               //fetching product details like (product_id and all)
               ordersModel.getProdetails(array.orderID, '').then(response => {
                    if (response != '') {
                         gdsProduct = response;
                    } else {
                         gdsProduct = [];
                    }

                    gdsProduct.forEach((gdsproduct) => {
                         //gdsorder post information
                         data = Object.assign(data, {
                              'product_info': [
                                   Counter = {
                                        'sku': gdsproduct.sku,
                                        'channelitemid': gdsproduct.product_id,
                                        'product_id': gdsproduct.product_id,
                                        'quantity': gdsproduct.qty,
                                        'cancel_reason_id': array.reason_id,
                                        'comments': array.comments
                                   }
                              ]
                         });
                         Counter++;
                    })

                    //email notification
                    ordersModel.getHostURL().then(HostUrl => {
                         if (HostUrl != '') {
                              let config = { 'api_key': process.env.CR_GDSAPIKey, 'secret_key': process.env.CR_GDSAPISECRETKey, 'orderdata': JSON.stringify(data) };
                              let url = 'http://' + HostUrl + '/dmapi/cancelOrder';
                              console.log('url', url);
                              var options = {
                                   method: 'POST',
                                   uri: url,
                                   body: { 'api_key': process.env.CR_GDSAPIKey, 'secret_key': process.env.CR_GDSAPISECRETKey, 'orderdata': JSON.stringify(data) },
                                   json: true // Automatically stringifies the body to JSON
                              };
                              rp(options)
                                   .then(function (err, parsedBody) {
                                        if (err) {
                                             console.log("error===>4", err);
                                             res.status(err.Status).json({ 'status': "failed", 'message': err.Message })
                                        } else {
                                             console.log("response from dmapi", parsedBody, err);
                                             if (parsedBody.Status == 1) {
                                                  let orderStatus = "CANCELLED BY CUSTOMER";
                                                  let result = {
                                                       'requestId': array.orderID,
                                                       'status': orderStatus
                                                  }
                                                  res.status(200).json({ 'status': "success", 'message': "Your order has been successfully cancelled", 'data': result })
                                                  //code to fetch order code 
                                                  let orderId;
                                                  let orderStatusId = '17009';
                                                  let orderHistory;
                                                  ordersModel.getOrderCode(array.orderID).then(order_id => {
                                                       if (order_id != '') {
                                                            orderId = order_id;
                                                       }
                                                       ordersModel.orderhistory(array.orderID, data.product_info, orderStatus, orderStatusId).then(orderhistory => {
                                                            if (orderhistory != '') {
                                                                 orderHistory = orderhistory;
                                                            }
                                                       }).catch(err => {
                                                            console.log(err)
                                                       })
                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             } else if (parsedBody.Status == 500) {
                                                  res.status(200).json({ 'status': 'failed', 'message': 'Order is already cancelled' })
                                             } else if (parsedBody.Status == 0) {
                                                  res.status(200).json({ 'status': 'failed', 'message': parsedBody.Message })
                                             } else if (parsedBody.Status == 404) {
                                                  res.status(200).json({ 'status': 'failed', 'message': parsedBody.Message })
                                             } else {
                                                  res.status(200).json({ 'status': 'failed', 'message': "Order Cancellation is Unsuccessful" })
                                             }
                                        }
                                   }).catch(err => {
                                        console.log("error=>", err.message)
                                   })
                         }
                    }).catch(err => {
                         console.log("error===>1", err)
                    })
               }).catch(err => {
                    console.log("error===>2", err)
               })
          }
     } catch (err) {
          console.log("error====>3", err.message);
     }
}
/*
purpose : Used to cancel user order
request : Order detailes
resposne  : order status
Author : Deepak tiwari
*/
module.exports.cancelOrder = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let salesToken;
               let customerToken;
               if ((typeof data.customer_token != 'undefined' && data.customer_token != '') && (typeof data.sales_token != 'undefined' && data.sales_token != '')) {
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         salesToken = sales_token;
                         console.log("sales_token", salesToken);
                         if (typeof salesToken.token_status != 'undefined' && salesToken.token_status == 0) {//
                              if ((typeof data.reason_id != 'undefined' && data.reason_id != '') && (typeof data.comments != 'undefined' && typeof data.comments != '') && (typeof data.orderID != 'undefined' && data.orderID != '')) {
                                   cancelorderdmapi(data, res);
                              } else {
                                   res.status(200).json({ 'status': "failed", 'message': "reason_id or comments or orderID is not passed" })
                              }
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }

                    }).catch(err => {
                         console.log(err);
                    });
               } else {
                    ordersModel.validateToken(data.customer_token).then(customer_token => {
                         console.log("customer_token", customer_token);
                         customerToken = customer_token;
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              if ((typeof data.reason_id != 'undefined' && data.reason_id != '') && (typeof data.comments != 'undefined' && typeof data.comments != '') && (typeof data.orderID != 'undefined' && data.orderID != '')) {
                                   cancelorderdmapi(data, res);
                              } else {
                                   res.status(200).json({ 'status': "failed", 'message': "reason_id or comments or orderID is not passed" })
                              }
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }
                    }).catch(err => {
                         console.log(err);
                    });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': "Required parameter not passed" })
          }

     } catch (err) {
          console.log("====>your are in catch===>", err.message);
          res.json({ 'status': 'failed', 'messsage': "Internal server error" })
     }
}


/*
purpose : Used to get order details
request : Order id , status , token 
resposne  : order status
Author : Deepak tiwari
*/
module.exports.Orderdetails = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let details = {};
               let salesToken;
               let customerToken;
               if ((typeof data.customer_token != 'undefined' && data.customer_token != '') && (typeof data.sales_token != 'undefined' && data.sales_token != '')) {
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         salesToken = sales_token;
                         console.log("sales_token", salesToken);
                         if (typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1) {
                              ordersModel.valOrderProd(data.orderID, '').then(valOrder => {
                                   if (valOrder > 0) {
                                        details = { 'orderid': data.orderID, 'channelid': process.env.channelId };
                                        //email notification
                                        ordersModel.getHostURL().then(HostUrl => {
                                             if (HostUrl != '') {
                                                  let url = 'http://' + HostUrl + '/dmapi/getOrderDetails';
                                                  console.log('url', url);
                                                  var options = {
                                                       method: 'POST',
                                                       uri: url,
                                                       body: { 'api_key': process.env.CR_GDSAPIKey, 'secret_key': process.env.CR_GDSAPISECRETKey, 'orderdata': JSON.stringify(details) },
                                                       json: true // Automatically stringifies the body to JSON
                                                  };
                                                  rp(options)
                                                       .then(function (parsedBody) {
                                                            if (parsedBody.Status == 1) {
                                                                 let response = {
                                                                      'order_id': parsedBody.Message.gds_order_id,
                                                                      'order_code': parsedBody.Message.order_code,
                                                                      'date_added': parsedBody.Message.order_date,
                                                                      'tax_total': parsedBody.Message.tax_total,
                                                                      'total': parsedBody.Message.grand_total,
                                                                      'sub_total': parsedBody.Message.sub_total,
                                                                      'coupon': '',
                                                                      'discount_amount': parsedBody.Message.discount_amount,
                                                                      'status': parsedBody.Message.order_status,
                                                                      'shipping_firstname': parsedBody.Message.shipping.fname,
                                                                      'shipping_lastname': parsedBody.Message.shipping.lname,
                                                                      'shipping_email': parsedBody.Message.email,
                                                                      'shipping_telephone': parsedBody.Message.shipping.telephone,
                                                                      'shipping_address': parsedBody.Message.shipping.addr1,
                                                                      'shipping_address2': parsedBody.Message.shipping.addr2,
                                                                      'shipping_city': parsedBody.Message.shipping.city,
                                                                      'shipping_pin': parsedBody.Message.shipping.postcode,
                                                                      'shipping_state': parsedBody.Message.shipping.state_name,
                                                                      'shipping_country': parsedBody.Message.shipping.country_name
                                                                 };

                                                                 if (typeof parsedBody.Message.shipping_track_details != 'undefined') {
                                                                      parsedBody.Mesaage.shipping_track_details.forEach((Element) => {
                                                                           response = Object.assign(response, {
                                                                                'shipping_track_details': Element
                                                                           })
                                                                      })
                                                                 }

                                                                 let products = JSON.parse(JSON.stringify(parsedBody.Message.products));
                                                                 if (typeof products != 'undefined') {
                                                                      products.forEach((Element) => {
                                                                           let order_id = parsedBody.Message.gds_order_id;
                                                                           let product_id = Element.product_id;
                                                                           response = Object.assign(response, {
                                                                                'product': {
                                                                                     'product_id': Element.product_id,
                                                                                     'name': Element.product_name,
                                                                                     'variant_id': Element.product_id,
                                                                                     'image': Element.product_image,
                                                                                     'pack_size': '',
                                                                                     'dealer_price': Element.total,
                                                                                     'unit_price': Element.unit_price,
                                                                                     'margin': '',
                                                                                     'order_status': Element.product_order_status,
                                                                                     'qty': Element.order_qty,
                                                                                     'tax': Element.tax,
                                                                                     'status': Element.status

                                                                                }
                                                                           })
                                                                      })
                                                                      response = Object.assign(response, {
                                                                           'order_track': JSON.parse(JSON.stringify(parsedBody.Message.order_track)),
                                                                           'delivery_slot': parsedBody.Message.delivery_slot
                                                                      })
                                                                 }
                                                                 res.status(200).json({ 'status': "success", 'message': "orderDetails", 'data': response })
                                                            } else if (parsedBody.Status == 0) {
                                                                 res.status(200).json({ 'status': "failed", 'message': parsedBody.Message })
                                                            }
                                                       }).catch(err => {
                                                            console.log("error=>2", err.message)
                                                       })
                                             }
                                        }).catch(err => {
                                             console.log("error===>1", err)
                                        })

                                   } else {
                                        res.status(200).json({ 'status': "failed", 'message': "Entered wrong orderId" })
                                   }
                              })
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }
                    }).catch(err => {
                         console.log(err);
                    });
               } else {
                    ordersModel.validateToken(data.customer_token).then(customer_token => {
                         customerToken = customer_token;
                         console.log("customer_token", customerToken);
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              ordersModel.valOrderProd(data.orderID, '').then(valOrder => {
                                   if (valOrder > 0) {
                                        details = { 'orderid': data.orderID, 'channelid': process.env.channelId };
                                        //email notification
                                        ordersModel.getHostURL().then(HostUrl => {
                                             if (HostUrl != '') {
                                                  let url = 'http://' + HostUrl + '/dmapi/getOrderDetails';
                                                  console.log('url', url);
                                                  var options = {
                                                       method: 'POST',
                                                       uri: url,
                                                       body: { 'api_key': process.env.CR_GDSAPIKey, 'secret_key': process.env.CR_GDSAPISECRETKey, 'orderdata': JSON.stringify(details) },
                                                       json: true // Automatically stringifies the body to JSON
                                                  };
                                                  rp(options)
                                                       .then(function (parsedBody) {
                                                            if (parsedBody.Status == 1) {
                                                                 let response = {
                                                                      'order_id': parsedBody.Message.gds_order_id,
                                                                      'order_code': parsedBody.Message.order_code,
                                                                      'date_added': parsedBody.Message.order_date,
                                                                      'tax_total': parsedBody.Message.tax_total,
                                                                      'total': parsedBody.Message.grand_total,
                                                                      'sub_total': parsedBody.Message.sub_total,
                                                                      'coupon': '',
                                                                      'discount_amount': parsedBody.Message.discount_amount,
                                                                      'status': parsedBody.Message.order_status,
                                                                      'shipping_firstname': parsedBody.Message.shipping.fname,
                                                                      'shipping_lastname': parsedBody.Message.shipping.lname,
                                                                      'shipping_email': parsedBody.Message.email,
                                                                      'shipping_telephone': parsedBody.Message.shipping.telephone,
                                                                      'shipping_address': parsedBody.Message.shipping.addr1,
                                                                      'shipping_address2': parsedBody.Message.shipping.addr2,
                                                                      'shipping_city': parsedBody.Message.shipping.city,
                                                                      'shipping_pin': parsedBody.Message.shipping.postcode,
                                                                      'shipping_state': parsedBody.Message.shipping.state_name,
                                                                      'shipping_country': parsedBody.Message.shipping.country_name
                                                                 };

                                                                 if (typeof parsedBody.Message.shipping_track_details != 'undefined') {
                                                                      parsedBody.Mesaage.shipping_track_details.forEach((Element) => {
                                                                           response = Object.assign(response, {
                                                                                'shipping_track_details': Element
                                                                           })
                                                                      })
                                                                 }

                                                                 let products = JSON.parse(JSON.stringify(parsedBody.Message.products));
                                                                 if (typeof products != 'undefined') {
                                                                      products.forEach((Element) => {
                                                                           let order_id = parsedBody.Message.gds_order_id;
                                                                           let product_id = Element.product_id;
                                                                           response = Object.assign(response, {
                                                                                'product': {
                                                                                     'product_id': Element.product_id,
                                                                                     'name': Element.product_name,
                                                                                     'variant_id': Element.product_id,
                                                                                     'image': Element.product_image,
                                                                                     'pack_size': '',
                                                                                     'dealer_price': Element.total,
                                                                                     'unit_price': Element.unit_price,
                                                                                     'margin': '',
                                                                                     'order_status': Element.product_order_status,
                                                                                     'qty': Element.order_qty,
                                                                                     'tax': Element.tax,
                                                                                     'status': Element.status

                                                                                }
                                                                           })
                                                                      })
                                                                      response = Object.assign(response, {
                                                                           'order_track': JSON.parse(JSON.stringify(parsedBody.Message.order_track)),
                                                                           'delivery_slot': parsedBody.Message.delivery_slot
                                                                      })
                                                                 }
                                                                 res.status(200).json({ 'status': "success", 'message': "orderDetails", 'data': response })
                                                            } else if (parsedBody.Status == 0) {
                                                                 res.status(200).json({ 'status': "failed", 'message': parsedBody.Message })
                                                            }
                                                       }).catch(err => {
                                                            console.log("error=>2", err.message)
                                                       })
                                             }
                                        }).catch(err => {
                                             console.log("error===>1", err)
                                        })

                                   } else {
                                        res.status(200).json({ 'status': "failed", 'message': "Entered wrong orderId" })
                                   }
                              })
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }

                    }).catch(err => {
                         console.log(err);
                    });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Required parameter not passed' })
          }
     } catch (err) {
          console.log(err);
          let error = { 'status': 'failed', 'message': err.message }
          return (error);
     }
}


//return order api
module.exports.returnOrder = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.sales_token != 'undefined' && data.sales_token != '' && typeof data.customer_token != 'undefined' && data.customer_token != '') {
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         salesToken = sales_token;
                         //if condition  typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1
                         if ((typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1)) {
                              console.log("====>1572 Return Order Api")
                              if ((typeof data.reason_id != 'undefined' && data.reason_id != '') && (typeof data.comments != 'undefined' && data.comments != '') && (typeof data.orderID != 'undefined' && data.orderID)) {
                                   returnOrderDmapi(data, res);
                              } else {
                                   res.status(200).json({ 'status': "failed", 'message': cpMessage.invalidRequestBody })
                              }
                         } else {
                              res.status(200).json({ 'status': 'session', 'message ': 'You have already logged into the Ebutor system' })
                         }

                    }).catch(err => {
                         console.log(err)
                    })
               } else {
                    ordersModel.validateToken(data.customer_token).then(customers_token => {
                         console.log("customer_Token", customers_token);
                         customerToken = customers_token;
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              console.log("return order api ===> 1590")
                              if ((typeof data.reason_id != 'undefined' && data.reason_id != '') && (typeof data.comments != 'undefined' && data.comments != '') && (typeof data.orderID != 'undefined' && data.orderID)) {
                                   returnOrderDmapi(data, res);
                              } else {
                                   res.status(200).json({ 'status': "failed", 'message': cpMessage.invalidRequestBody })
                              }
                         } else {
                              res.status(200).json({ 'status': 'session', 'message ': 'You have already logged into the Ebutor system' })
                         }
                    }).catch(err => {
                         console.log(err)
                    })
               }

          } else {
               res.status(200).json({ 'status': "failed", "message": cpMessage.invalidRequestBody })
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': "failed", 'message': cpMessage.serverError })
     }
}

/*
purpose : Used to get return reasons in dropdown while returning order
request :Nothing
resposne  : Return returnReasons
Author : Deepak tiwari
*/
module.exports.returnReasons = function (req, res) {
     try {
          ordersModel.returnReasons().then(response => {
               if (response != '') {
                    res.status(200).json({ 'status': "success", 'message': "Return Reason", 'data': response })
               } else {
                    res.status(200).json({ 'status': "failed", 'message': "No return reason found" })
               }
          }).catch(err => {
               console.log(err);
               res.status(200).json({ 'status': "failed", 'message': "Something went wrong" })
          })
     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': "failed", 'message': cpMessage.serverError })
     }

}

/*
purpose : Used to get cancel reasons in dropdown while cancelling order
request :Nothing
resposne  : Return cancelReasons
Author : Deepak tiwari
*/
module.exports.cancelReasons = function (req, res) {
     try {
          // let name = encrptionInstance.encrypt(req.body.data);
          //console.log("======>1792 Encrypted", name);
          ordersModel.returnCancelReasons().then(response => {
               if (response != '') {
                    //   let decryptedData = encrptionInstance.decrypt(name);
                    //console.log("decrypted ====> 1796 Decryption", JSON.parse(decryptedData));
                    res.status(200).json({ 'status': "success", 'message': "Cancel Reason", 'data': response })
               } else {
                    res.status(200).json({ 'status': "failed", 'message': "No cancel reason found" })
               }
          }).catch(err => {
               console.log(err);
               res.status(200).json({ 'status': "failed", 'message': "Something went wrong" })
          })
     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': "failed", 'message': cpMessage.serverError })
     }
}

/*
purpose : Used to generate OrderRef code 
request : Le_wh_id  
resposne  : Return generatedOrderRef code
Author : Deepak tiwari
*/
module.exports.genarateOrderRef = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.le_wh_id != 'undefined' && data.le_wh_id != '') {
                    ordersModel.generateOrderRef(data.le_wh_id).then(orderCode => {
                         console.log("state", orderCode);
                         if (orderCode == '') {
                              res.status(200).json({ 'status': "success", 'message': "Details not found" })
                         } else {
                              res.status(200).json({ 'status': "success", 'message': "Order code", 'data': orderCode })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': "Warehouse id cannot be empty" })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': "Internal server error" });
     }
}

/*
purpose : Used to generate otp
request : customer , sales token
resposne  : Return generted otp
Author : Deepak tiwari
*/
module.exports.generateOtpOrder = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let salesToken;
               let customerToken;
               if ((typeof data.customer_token != 'undefined' && data.customer_token != '') && (typeof data.sales_token != 'undefined' && data.sales_token != '')) {
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         salesToken = sales_token;
                         console.log("sales_token", salesToken);
                         if (typeof salesToken.token_status != 'undefined' && salesToken.token_status == 0) {//
                              if (typeof data.telephone != 'undefined' && data.telephone != '') {
                                   ordersModel.generateOtp(data.customer_token, data.telephone).then(otp => {
                                        console.log("===>1863", otp)
                                        if (otp) {
                                             res.status(200).json({ 'status': 'success', 'message': "Please confirm otp", 'data': otp })
                                        } else {
                                             res.status(200).json({ 'status': 'failed', 'message': "Unable to get otp" })
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
                                   })

                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'Telephone is empty' })
                              }
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }

                    }).catch(err => {
                         console.log(err);
                    });
               } else {
                    ordersModel.validateToken(data.customer_token).then(customer_token => {
                         console.log("customer_token", customer_token);
                         customerToken = customer_token;
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              if (typeof data.telephone != 'undefined' && data.telephone != '') {
                                   ordersModel.generateOtp(data.customer_token, data.telephone).then(otp => {
                                        console.log("===>1891", otp)
                                        if (otp) {
                                             res.status(200).json({ 'status': 'success', 'message': "Please confirm otp", 'data': otp })
                                        } else {
                                             res.status(200).json({ 'status': 'failed', 'message': "Unable to get otp" })
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
                                   })

                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'Telephone is empty' })
                              }
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }
                    }).catch(err => {
                         console.log(err);
                    });
               }

          } else {
               res.status(200).json({ 'status': 'failed', 'message': "Required parameter not passed" })
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ "status": "failed", 'message': "Internal server error" })
     }
}

/*
purpose : Used to confirm otp
request : customer , sales token , otp
resposne  : Return otp validaion
Author : Deepak tiwari
*/
module.exports.orderOtpConfirmation = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let salesToken;
               let customerToken;
               if ((typeof data.customer_token != 'undefined' && data.customer_token != '') && (typeof data.sales_token != 'undefined' && data.sales_token != '')) {
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         salesToken = sales_token;
                         console.log("sales_token", salesToken);
                         if (typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1) {//
                              if (typeof data.telephone != 'undefined' && data.telephone != '') {
                                   if (typeof data.otp != 'undefined' && data.otp != '') {
                                        ordersModel.getOtp(data.customer_token).then(otp => {
                                             if (data.otp != otp.otp && otp.status == 'success') {
                                                  res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidOtp })
                                             } else if (data.otp == otp.otp && otp.status == 'success') {
                                                  res.status(200).json({ 'status': 'success', 'message': "Valid otp" })
                                             } else {
                                                  res.status(200).json({ 'status': 'failed', 'message': otp.message })
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
                                        })
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': 'Otp is empty' })
                                   }
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'Telephone is empty' })
                              }
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }

                    }).catch(err => {
                         console.log(err);
                    });
               } else {
                    ordersModel.validateToken(data.customer_token).then(customer_token => {
                         console.log("customer_token", customer_token);
                         customerToken = customer_token;
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              if (typeof data.telephone != 'undefined' && data.telephone != '') {
                                   if (typeof data.otp != 'undefined' && data.otp != '') {
                                        ordersModel.getOtp(data.customer_token).then(otp => {
                                             if (data.otp != otp.otp && otp.status == 'success') {
                                                  res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidOtp })
                                             } else if (data.otp == otp.otp && otp.status == 'success') {
                                                  res.status(200).json({ 'status': 'success', 'message': "Valid otp" })
                                             } else {
                                                  res.status(200).json({ 'status': 'failed', 'message': otp.message })
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
                                        })
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': 'Otp is empty' })
                                   }
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'Telephone is empty' })
                              }
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }
                    }).catch(err => {
                         console.log(err);
                    });
               }

          } else {
               res.status(200).json({ 'status': 'failed', 'message': "Required parameter not passed" })
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ "status": "failed", 'message': "Internal server error" })
     }
}

/*
purpose : Used to get filtered order status based on master _lookup value 
request :nothing
resposne  : Return order status
Author : Deepak tiwari
*/
module.exports.getFilterOrderStatus = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let salesToken;
               let customerToken;
               if ((typeof data.customer_token != 'undefined' && data.customer_token != '') && (typeof data.sales_token != 'undefined' && data.sales_token != '')) {
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         salesToken = sales_token;
                         console.log("sales_token", salesToken);
                         if (typeof salesToken.token_status != 'undefined' && salesToken.token_status == 0) {//
                              ordersModel.getFilterOrderStatus().then(result => {
                                   if (result != '') {
                                        res.status(200).json({ "status": "success", 'message': "getFilterOrderStatus", 'data': result })
                                   } else {
                                        res.status(200).json({ "status": "success", 'message': "No data" })
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                              })
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }

                    }).catch(err => {
                         console.log(err);
                    });
               } else {
                    ordersModel.validateToken(data.customer_token).then(customer_token => {
                         console.log("customer_token", customer_token);
                         customerToken = customer_token;
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              ordersModel.getFilterOrderStatus().then(result => {
                                   if (result != '') {
                                        res.status(200).json({ "status": "success", 'message': "getFilterOrderStatus", 'data': result })
                                   } else {
                                        res.status(200).json({ "status": "success", 'message': "No data" })
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                              })
                         } else {
                              res.status(200).json({ 'status': "session", 'message': "You have already logged into the Ebutor System" })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    });
               }

          } else {
               res.status(200).json({ 'status': 'failed', 'message': "Required parameter not passed" })
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ "status": "failed", 'message': "Internal server error" })
     }
}


/*
purpose : Used to store sales  return workflow.
Request : currentStatus , nextStatus, modules name , user_id.
Resposne : WIll update workFlow in `appr_workflow_history` table.
Author : Deepak tiwari.
*/
async function storeWorkFLow(lastInsertId, currentStatus, nextStatus, approvalComment, userId) {
     console.log("------1943----")
     return new Promise((resolve, reject) => {
          try {
               //getting Hosturl from DB.
               ordersModel.getHostURL().then(HostUrl => {
                    if (HostUrl != '') {
                         let url = 'http://' + HostUrl + '/cpmanager/storeWorkFlowApis/' + "Sales Return Requests" + "/" + lastInsertId + "/" + currentStatus + "/" + nextStatus + "/" + approvalComment + "/" + userId;
                         var options = {
                              method: 'GET',
                              uri: url,
                              json: true // Automatically stringifies the body to JSON
                         };


                         rp(options)
                              .then(function (parsedBody) {
                                   // POST succeeded...
                                   if (parsedBody.Status == 1) {
                                        resolve(true);
                                   } else {
                                        resolve(false);
                                   }
                              })
                              .catch(function (err) {
                                   // POST failed...
                                   console.log(err.message)
                                   reject(err);
                              });
                    } else {
                         resolve(false);
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
purpose : Used to validate order return workflow.
request : Product details with return quantity, customer_token .
resposne  : Updates order status in respective tables.
Author : Deepak tiwari
*/
async function returnOrderValidation(data, res) {
     try {
          /* putting constraints order can be returned after delivery only*/
          let deliveryStatus = [17007, 17022, 17023, 17008];
          let orderInfo = await ordersModel.getOrderInfoByOrderId(data.order_id);
          if (deliveryStatus.includes(orderInfo[0].order_status_id)) {
               /* validating weather specific product is returnable or not */

               /** Return Request status*/
               let status_id = 57225;
               if (typeof data.returns != 'undefined') {
                    let returnData = {};
                    let sampleRet = [];
                    let returnValue = 0;
                    let totalReturnValue = 0;
                    let totalReturnItemQty = 0;
                    let key = 0;
                    let returnQuantity = data.returns;
                    let orderData = {};
                    let status;
                    let message;
                    for (let i = 0; i < returnQuantity.length; i++) {
                         if (returnQuantity[i].return_qty > 0) {
                              /**Fetching unit price for each product including tax */
                              let price = await ordersModel.getUnitPriceWithTax(data.order_id, returnQuantity[i].product_id);
                              returnValue = returnValue + (price.singleUnitPriceWithtax * returnQuantity[i].return_qty);
                              sampleRet[i] = await Object.assign({}, { 'product_id': returnQuantity[i].product_id, 'qty': returnQuantity[i].return_qty, 'good_qty': returnQuantity[i].return_qty, 'return_reason_id': returnQuantity[i].return_reason, 'return_status_id': status_id, 'approval_status': status_id, 'approved_by_user': data.user_id, 'gds_order_id': data.order_id, 'le_wh_id': orderInfo[0].le_wh_id, 'tax_details': price, 'return_request_id': '', 'reference_no': '' });
                              key++;
                              totalReturnValue = totalReturnValue + 1;
                              totalReturnItemQty = totalReturnItemQty + parseInt(returnQuantity[i].return_qty);
                         }
                    }

                    if (sampleRet.length > 0) {
                         let stateCode = await ordersModel.getStateCode(data.order_id);
                         let refCode = await ordersModel.getrefCode('SR', stateCode);
                         orderData = { 'gds_order_id': data.order_id, 'total_return_value': returnValue, 'return_status_id': status_id, 'approval_status': status_id, 'total_return_items': totalReturnValue, 'total_return_item_qty': totalReturnItemQty, 'reference_no': refCode };
                         /*updating records in sales_order_grid*/
                         let returnOrderGridId = await ordersModel.saveReturnGrid(orderData, data.user_id);
                         orderData = Object.assign(orderData, { 'return_request_id': returnOrderGridId });
                         for (let i = 0; i < sampleRet.length; i++) {
                              if (returnOrderGridId) {
                                   sampleRet[i].return_request_id = returnOrderGridId;
                                   sampleRet[i], reference_no = refCode;
                                   orderData = Object.assign(orderData, { 'return_request_id': returnOrderGridId, 'product_id': sampleRet[i].product_id, 'qty': sampleRet[i].qty, 'good_qty': sampleRet[i].good_qty, 'approved_by_user': sampleRet[i].approved_by_user, 'return_reason_id': sampleRet[i].return_reason_id });
                                   /**Updating record in sales_Returns */
                                   let orderReturn = await ordersModel.saveReturn(orderData, data.user_id);
                                   if (orderReturn) {
                                        status = 'success';
                                        message = cpMessage.ReturnRequestProcess;
                                   } else {
                                        status = 'failed';
                                        message = cpMessage.ErrorWhileSalesReturn;
                                   }
                              } else {
                                   status = 'failed';
                                   message = cpMessage.ErrorWhileSalesReturn;
                              }

                         }
                         /**  Update return grid gst value  */
                         await ordersModel.updateGstOnReturnGrid(orderData['gds_order_id']);
                         /**Updating order Reject flag  Reject  */
                         await ordersModel.updatetOrderReturnRejectFlag(orderData['gds_order_id']);
                         /**Used to store workFlow  */
                         let lastInsertId = returnOrderGridId; let currentStatus = 57225; let nextStatus = 57225; let approvalComment = "Return request initiated successfully"; let userId = data.user_id;
                         await storeWorkFLow(lastInsertId, currentStatus, nextStatus, approvalComment, userId);
                         //ordersModel.updateReturnOrderStatusonOrderId(orderData.gds_order_id, refCode, data.user_id);
                         res.status(200).json({ 'status': status, 'message': message, 'data': { 'cancel_return_flag': 1, 'ReturnRefCode': refCode } });
                    } else {
                         console.log("----2031---")
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.ErrorWhileSalesReturn });
                    }
               } else {
                    resolve('');
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidReturnOrderRequest })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }

}
/*
purpose : Used to return ordered items.
request : Product details with return quantity, customer_token .
resposne  : Updates order status in respective tables.
Author : Deepak tiwari
*/
module.exports.returnOrderApi = (req, res) => {
     try {
          /*  Request body validation   */
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let salesToken;
               let customerToken;
               /* Token validation */
               if ((typeof data.customer_token != 'undefined' && data.customer_token != '') && (typeof data.sales_token != 'undefined' && data.sales_token != '')) {
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         salesToken = sales_token;
                         if (typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1) {
                              /*Validating weather order is returnable or not */
                              returnOrderValidation(data, res);
                         } else {
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    });
               } else {
                    ordersModel.validateToken(data.customer_token).then(customer_token => {
                         customerToken = customer_token;
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              /* Validating weather order is returnable or not */
                              returnOrderValidation(data, res);
                         } else {
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}
/*
purpose : Used to cancel return request.
request : Product details with return quantity, customer_token .
resposne  : Updates order status in respective tables.
Author : Deepak tiwari
*/

module.exports.cancelReturnRequest = async (req, res) => {
     try {
          /*  Request body validation   */
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let salesToken;
               let customerToken;
               let userId = (typeof data.user_id != 'undefined' && data.user_id != '') ? data.user_id : 0;
               let orderId = (typeof data.order_id != 'undefined' && data.order_id != '') ? data.order_id : 0;
               let returnRequestId = await ordersModel.getReturnRequestId(orderId);
               /* Token validation */
               if ((typeof data.customer_token != 'undefined' && data.customer_token != '') && (typeof data.sales_token != 'undefined' && data.sales_token != '')) {
                    ordersModel.validateToken(data.sales_token).then(sales_token => {
                         salesToken = sales_token;
                         if (typeof salesToken.token_status != 'undefined' && salesToken.token_status == 1) {
                              ordersModel.canCelReturnRequest(userId, orderId).then(async (response) => {
                                   await ordersModel.updateReturnCancelableFlag(orderId);
                                   let lastInsertId = returnRequestId; let currentStatus = 57225; let nextStatus = 57231; let approvalComment = "Return request cancelled"; let userId = userId;
                                   await storeWorkFLow(lastInsertId, currentStatus, nextStatus, approvalComment, userId);
                                   res.status(200).json({ "status": "success", 'message': cpMessage.CancelReturnRequest })
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                              })
                         } else {
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    });
               } else {
                    ordersModel.validateToken(data.customer_token).then(customer_token => {
                         customerToken = customer_token;
                         if (typeof customerToken.token_status != 'undefined' && customerToken.token_status == 1) {
                              ordersModel.canCelReturnRequest(userId, orderId).then(async (response) => {
                                   await ordersModel.updateReturnCancelableFlag(orderId);
                                   let lastInsertId = returnRequestId; let currentStatus = 57225; let nextStatus = 57231; let approvalComment = "Return request cancelled"; let userId = data.user_id;
                                   await storeWorkFLow(lastInsertId, currentStatus, nextStatus, approvalComment, userId);
                                   res.status(200).json({ "status": "success", 'message': cpMessage.CancelReturnRequest })
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                              })
                         } else {
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/*
purpose : Used to get all return request based on  delivery_token.
request : Delivery token .
resposne  : Return all the return request raise based assigned  delivery executive.
Author : Deepak tiwari
*/

module.exports.getReturnRequest = async (req, res) => {
     try {
          /*  Request body validation   */
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               /* Token validation */
               if ((typeof data.delivery_token != 'undefined' && data.delivery_token != '')) {
                    ordersModel.checkDeliveryToken(data.delivery_token).then(async (valid) => {
                         if (valid) {
                              //Get delivery executive details 
                              let DeliveryExecDetails = await ordersModel.getDeliveryExecutive(data.delivery_token);
                              if (DeliveryExecDetails) {
                                   ordersModel.getReturnRequest(DeliveryExecDetails).then(result => {
                                        if (result) {
                                             res.status(200).json({ "status": "success", 'message': "Available Return Requests", 'data': result })
                                        } else {
                                             res.status(200).json({ "status": "success", 'message': "No Return Request Found", 'data': [] })
                                        }

                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                                   })
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
                              }

                         } else {
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/*
purpose : Used to get all return request details based on order details.
request : DeliveryToken , OrderId .
resposne  : Return request details based on order details.
Author : Deepak tiwari
*/
module.exports.returnRequestDetails = async (req, res) => {
     try {
          /*  Request body validation   */
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               /* Token validation */
               if ((typeof data.delivery_token != 'undefined' && data.delivery_token != '')) {
                    ordersModel.checkDeliveryToken(data.delivery_token).then(async (valid) => {
                         if (valid) {
                              //Get delivery executive details 
                              let DeliveryExecDetails = await ordersModel.getDeliveryExecutive(data.delivery_token);
                              if (DeliveryExecDetails) {
                                   ordersModel.getReturnRequest(DeliveryExecDetails).then(result => {
                                        if (result) {
                                             res.status(200).json({ "status": "success", 'message': "Available Return Requests", 'data': result })
                                        } else {
                                             res.status(200).json({ "status": "success", 'message': "No Return Request Found", 'data': [] })
                                        }

                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                                   })
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
                              }

                         } else {
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/*
purpose : Used to get all return request details based on order details.
request : DeliveryToken , OrderId .
resposne  : Return request details based on order details.
Author : Deepak tiwari
*/
module.exports.returnRequestProductDetails = async (req, res) => {
     try {
          /*  Request body validation   */
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let orderId = typeof data.order_id != 'undefined' ? data.order_id : '';
               /* Token validation */
               if ((typeof data.delivery_token != 'undefined' && data.delivery_token != '')) {
                    ordersModel.checkDeliveryToken(data.delivery_token).then(async (valid) => {
                         if (valid) {
                              //Get delivery executive details 
                              let DeliveryExecDetails = await ordersModel.getDeliveryExecutive(data.delivery_token);
                              if (DeliveryExecDetails) {
                                   ordersModel.returnRequestProducts(DeliveryExecDetails, orderId).then(result => {
                                        if (result) {
                                             res.status(200).json({ "status": "success", 'message': "Available Product Info", 'data': result });
                                        } else {
                                             res.status(200).json({ "status": "failed", 'message': "No Product Found", 'data': [] });
                                        }

                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                                   })
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
                              }

                         } else {
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}


/*
purpose : Used to store picked quantity details received by DE.
request : Product details with picked quantity, customer_token .
resposne  : Updates order status in respective tables.
Author : Deepak tiwari
*/
module.exports.storePickedQuantityDetails = async (req, res) => {
     try {
          /*  Request body validation   */
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               /* Token validation */
               if ((typeof data.delivery_token != 'undefined' && data.delivery_token != '')) {
                    ordersModel.checkDeliveryToken(data.delivery_token).then(async (valid) => {
                         if (valid) {
                              if (typeof data.returns != 'undefined' && data.returns != '') {
                                   let sampleRet = [];
                                   let totalReturnValue = 0;
                                   let totalReturnItemQty = 0;
                                   let returnQuantity = data.returns;
                                   let orderData = {};
                                   let returnRequestId;
                                   let approval_status = 57229;
                                   let status;
                                   let message;
                                   for (let i = 0; i < returnQuantity.length; i++) {
                                        if (returnQuantity[i].picked_qty > 0) {
                                             sampleRet[i] = await Object.assign({}, { 'product_id': returnQuantity[i].product_id, 'picked_qty': returnQuantity[i].picked_qty, 'approval_status': data.status, 'picked_by_user': data.user_id, 'gds_order_id': data.order_id, 'return_request_id': returnQuantity[i].return_request_id });
                                             totalReturnValue = totalReturnValue + 1;
                                             totalReturnItemQty = totalReturnItemQty + parseInt(returnQuantity[i].picked_qty);
                                             returnRequestId = returnQuantity[i].return_request_id
                                        }
                                   }
                                   if (sampleRet.length > 0) {
                                        orderData = { 'gds_order_id': data.order_id, 'approval_status': data.status, 'total_picked_item_qty': totalReturnItemQty, 'return_request_id': returnRequestId };
                                        /*updating records in sales_order_grid*/
                                        let returnOrderGridId = await ordersModel.updateReturnGrid(orderData, data.user_id);
                                        for (let i = 0; i < sampleRet.length; i++) {
                                             if (returnOrderGridId) {
                                                  orderData = Object.assign(orderData, { 'product_id': sampleRet[i].product_id, 'picked_qty': sampleRet[i].picked_qty, 'approved_by_user': sampleRet[i].picked_by_user });
                                                  /**Updating record in sales_Returns */
                                                  let orderReturn = await ordersModel.updateSaveReturn(orderData, data.user_id);
                                                  if (orderReturn) {
                                                       status = 'success';
                                                       message = "Successfully Picked";
                                                  } else {
                                                       status = 'failed';
                                                       message = "Unsuccessful Operation";
                                                  }
                                             } else {
                                                  status = 'failed';
                                                  message = "Unsuccessful Operation";
                                             }

                                        }
                                        //Used to store workFlow
                                        let lastInsertId = returnRequestId; let currentStatus = 57226; let nextStatus = data.status; let approvalComment = "Stock Picked Successfully"; let userId = data.user_id;
                                        await storeWorkFLow(lastInsertId, currentStatus, nextStatus, approvalComment, userId);
                                        await ordersModel.updateCancelableFlag(data.order_id);
                                        res.status(200).json({ 'status': status, 'message': message });
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': "Unsuccessful Operation" });
                                   }
                              } else {
                                   resolve('');
                              }
                         } else {
                              res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": "failed", 'message': cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': "session", 'message': cpMessage.invalidToken })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}