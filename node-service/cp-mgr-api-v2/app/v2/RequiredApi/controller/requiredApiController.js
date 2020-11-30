const model = require('../model/requiredApiModel');//model
const cpmessage = require('../../../config/cpMessage');//response messages
const Joi = require('joi');
var upload = require('../../../config/s3Config');
var encryption = require('../../../config/encryption');

/**
 * Purpose : Used to update address at the time of placing an order
 * Required :Address details , latitude , longitude
 * Response :  Will update address in respective tables
 * Authore : Deepak Tiwari
 */

module.exports.updateAddress = function (req, res) {
     try {
          let data = JSON.parse(req.body.data)
          if (typeof data != 'undefined') {
               /* joi (client side validation) */
               let requestbody = {
                    customerToken: data.customer_token,
                    address1: data.address1,
                    latitude: data.latitude,
                    longitude: data.longitude,
                    legalEntityId: data.legal_entity_id,
                    legalEntityWh: data.le_wh_id,
                    pincode: data.pin,
                    state: data.state,
                    city: data.city,
               }

               let validationRule = Joi.object().keys({
                    customerToken: Joi.string().required(),
                    address1: Joi.string().required(),
                    latitude: Joi.string().required(),
                    longitude: Joi.string().required(),
                    legalEntityId: Joi.number().allow(null, '').required(),
                    legalEntityWh: Joi.number().allow(null, '').required(),
                    pincode: Joi.number().required(),
                    state: Joi.string().required(),
                    city: Joi.string().required(),
               });
               Joi.validate(requestbody, validationRule, function (err, valid) {
                    if (err) {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpmessage.internalCatch });
                    } else {
                         model.validateToken(data.customer_token).then(validated => {
                              console.log("customer_Token", validated);
                              let customerToken = validated;
                              //checking weather token is active or not 
                              if (customerToken.token_status == 0) {//0
                                   res.status(200).json({ 'status': "session", 'message': cpmessage.invalidToken });
                              } else {
                                   //validating entered pincode weather entered pincode is  in servicable area or not
                                   if (data.pin != '' && data.pin.length == 6) {
                                        model.serviceablePincode(data.pin).then((checkPincode) => {
                                             if (checkPincode <= 0) {
                                                  res.status(200).json({ status: 'failed', message: "Entered pincode area not comes under ebutor services" })
                                             } else {
                                                  //fetching state_id , country_id based on statename
                                                  model.getstate(data.state).then(stateDetails => {
                                                       console.log("=====>62", stateDetails);
                                                       /*updating shippingAddress */
                                                       model.updateAddress(data.customer_token, data.legal_entity_id, data.le_wh_id, data.address1, data.latitude, data.longitude, data.pin, data.city, stateDetails.zone_id, stateDetails.country_id, stateDetails.name).then(updated => {
                                                            model.getUserIdByCustomerToken(data.customer_token).then(customer_id => {
                                                                 if (customer_id != '') {
                                                                      model.getExistingEcash(customer_id).then(ecash_Amount => {
                                                                           //flag = 0  when customer token is valid
                                                                           let result = { 'address': updated, 'flag': 0, 'ecash_amount': ecash_Amount.toString() }
                                                                           res.status(200).json({ 'status': 'success', 'message': "updated successfully", 'data': result })
                                                                      }).catch(err => {
                                                                           console.log(err)
                                                                      })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err)
                                                            })

                                                       }).catch(err => {
                                                            console.log(err);
                                                            res.status(200).json({ 'status': 'failed', 'message': cpmessage.internalCatch });
                                                       })
                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             }
                                        }).catch((err) => {
                                             console.log(err);
                                             res.status(200).json({ status: "failed", message: "Something went wrong" })
                                        })
                                   } else {
                                        res.status(200).json({ status: 'failed', message: "Please enter valid postcode" })
                                   }

                              }
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ "status": "failed", "message": cpmessage.internalCatch });
                         })
                    }
               })
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpmessage.invalidRequestBody });
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ "status": "failed", "message": cpmessage.serverError });
     }
}

/**
 * Purpose : Used to save feedback while doing checkout.
 * Required :feedback_picture , comments , ff_id , retailer_id
 * Response :  feedback get updated in ff_commments table.
 * Authore : Deepak Tiwari
 */

module.exports.saveFeedbackReasons = function (req, res) {
     if (req.body.data) {
          var data = req.body.data;
          //console.log(data);
          data = JSON.parse(data);
          var flag = data.hasOwnProperty('flag') ? data['flag'] : '';
          var comments = data.hasOwnProperty('comments') ? data['comments'] : '';
          var feedback_groupid = data.hasOwnProperty('feedback_groupid') ? data['feedback_groupid'] : '';
          var feedback_id = data.hasOwnProperty('feedback_id') ? data['feedback_id'] : '';
          var legal_entity_id = data.hasOwnProperty('legal_entity_id') ? data['legal_entity_id'] : '';
          var sales_token = data.hasOwnProperty('sales_token') ? data['sales_token'] : '';
          var latitude = data.hasOwnProperty('latitude') ? data['latitude'] : '';
          var longitude = data.hasOwnProperty('longitude') ? data['longitude'] : '';
          var user_id = data.hasOwnProperty('user_id') ? data['user_id'] : '';
          var activity = data.hasOwnProperty('activity') ? data['activity'] : '';
          var cart_data = data.hasOwnProperty('cart_data') ? data['cart_data'] : '';
          var le_wh_id = data.hasOwnProperty('le_wh_id') ? data['le_wh_id'] : '';
          var feedback_pic = [];
          var feebackaudio = [];
          //fileupload
          if (typeof req.files.feedback_pic != 'undefined') {
               var singleupload = upload.single('feedback_pic');
               singleupload(req, res, function (err, data) {
                    if (err) {
                         console.log(err)
                    } else {
                         for (let i = 0; i < req.files.feedback_pic.length; i = i + 1) {
                              // console.log("req.bhjsdb", req.files.feedback_pic[i].location)
                              feedback_pic.push(req.files.feedback_pic[i].location);
                         }

                    }
               });
          }

          if (typeof req.files.feedback_audio != 'undefined') {
               var singleupload = upload.single('feedback_audio');
               singleupload(req, res, function (err, data) {
                    if (err) {
                         console.log(err)
                    } else {
                         for (let i = 0; i < req.files.feedback_audio.length; i = i + 1) {
                              // console.log("req.bhjsdb", req.files.feedback_pic[i].location)
                              feebackaudio.push(req.files.feedback_audio[i].location);
                         }

                    }
               });
          }

          if (flag == 1) {
               model.checkCustomerToken(sales_token).then(data => {
                    if (data > 0) {
                         ff_id = model.getUserIdFromToken(sales_token).then(userdet => {
                              if (userdet.length > 0) {
                                   if (le_wh_id != '') {
                                        var input = [
                                             {
                                                  'ff_id': userdet[0]['user_id'],
                                                  'legal_entity_id': legal_entity_id,
                                                  'feedback_groupid': feedback_groupid,
                                                  'feedback_id': feedback_id,
                                                  'comments': comments,
                                                  'feedback_pic': feedback_pic,
                                                  'feedback_audio': feebackaudio,
                                                  'sales_token': sales_token,
                                                  'activity': activity,
                                                  'latitude': latitude,
                                                  'longitude': longitude,
                                                  'user_id': user_id,
                                                  'cart_data': cart_data,
                                                  'le_wh_id': le_wh_id
                                             }
                                        ];
                                        if (feedback_groupid) {
                                             model.saveFeedbackReasons(input).then(result1 => {
                                                  console.log("savefeedbackResponse");
                                             });
                                        }
                                        model.getLatLongDetails(input).then(latlong => {
                                             if (latlong.longitude != '' && latlong.latitude != '') {
                                                  input[0]['latitude'] = latlong.latitude;
                                                  input[0]['longitude'] = latlong.longitude;
                                                  model.insertFFComments(input).then(result2 => {
                                                       //var ffcomment = 
                                                       if (result2) {
                                                            console.log(cpmessage.FeedbackSubmitted);
                                                            res.send({ status: 'success', message: cpmessage.FeedbackSubmitted, data: result2 });
                                                       } else {
                                                            res.send({ status: 'success', message: 'Not Saved', data: [] })
                                                       }
                                                  });
                                             } else {
                                                  res.send({ status: 'failed', message: cpmessage.invalidRequestBody, data: [] });
                                             }
                                        });
                                   } else {
                                        res.send({ status: 'failed', message: cpmessage.invalidRequestBody, data: [] });
                                   }
                              } else {
                                   res.send({ status: 'failed', message: cpmessage.invalidToken, data: [] });
                              }
                         });
                    } else {
                         res.send({ status: 'session', message: cpmessage.invalidToken, data: [] });
                    }
               });

          } else {
               if (sales_token != '') {
                    if (feedback_groupid != '') {
                         if (feedback_id != '') {
                              if (legal_entity_id != '') {
                                   if (le_wh_id != '') {
                                        model.checkCustomerToken(sales_token).then(data => {
                                             if (data > 0) {
                                                  ff_id = model.getUserIdFromToken(sales_token).then(userdet => {
                                                       var input = [
                                                            {
                                                                 'ff_id': userdet[0]['user_id'],
                                                                 'legal_entity_id': legal_entity_id,
                                                                 'feedback_groupid': feedback_groupid,
                                                                 'feedback_id': feedback_id,
                                                                 'comments': comments,
                                                                 'feedback_pic': feedback_pic,
                                                                 'feedback_audio': feebackaudio,
                                                                 'sales_token': sales_token,
                                                                 'activity': activity,
                                                                 'latitude': latitude,
                                                                 'longitude': longitude,
                                                                 'user_id': user_id,
                                                                 'le_wh_id': le_wh_id
                                                            }
                                                       ];
                                                       if (feedback_groupid) {
                                                            model.saveFeedbackReasons(input).then(result1 => {
                                                                 if (result1.data.length > 0) {
                                                                      res.send({ status: 'success', message: cpmessage.FeedbackSubmitted, data: result1 });
                                                                 } else {
                                                                      res.send({ status: 'success', message: 'Not saved', 'data': [] });
                                                                 }
                                                            });
                                                       }
                                                  });

                                             } else {
                                                  res.send({ status: 'failed', message: cpmessage.invalidToken, data: [] });
                                             }
                                        });
                                   } else {
                                        res.send({ status: 'failed', message: cpmessage.invalidRequestBody, data: [] });
                                   }

                              } else {
                                   res.send({ status: 'failed', message: cpmessage.invalidRequestBody, data: [] });
                              }
                         } else {
                              res.send({ status: 'failed', message: cpmessage.invalidRequestBody, data: [] });
                         }
                    } else {
                         res.send({ status: 'failed', message: cpmessage.invalidRequestBody, data: [] });
                    }
               } else {
                    res.send({ status: 'failed', message: cpmessage.invalidRequestBody, data: [] });
               }
          }
     }
}

/**
 * Purpose : Used to save Brands feedback.
 * Required :feedback_picture , buying_price ,selling_price , ff_id , retailer_id
 * Response :  feedback get updated in branc_feedback table.
 * Authore : Deepak Tiwari
 */

module.exports.saveBrandFeedback = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               if (typeof data.ff_id != 'undefined' && data.ff_id != '') {
                    if (typeof data.retailer_le_id != 'undefined' && data.retailer_le_id != '') {
                         let buying_price = (typeof data.buying_price != 'undefined' && data.buying_price != '') ? data.buying_price : 0;
                         let selling_price = (typeof data.selling_price != 'undefined' && data.selling_price != '') ? data.selling_price : 0;
                         let weekly_sales_value = (typeof data.weeksal_val != 'undefined' && data.weeksal_val != '') ? data.weeksal_val : 0;
                         let ff_id = data.ff_id;
                         let retailer_le_id = data.retailer_le_id;
                         var feedback_pic = [];
                         let status = 0;
                         //fileupload
                         if (typeof req.files.feedback_pic != 'undefined') {
                              var singleupload = upload.single('feedback_pic');
                              singleupload(req, res, function (err, data) {
                                   if (err) {
                                        console.log(err)
                                   } else {
                                        for (let i = 0; i < req.files.feedback_pic.length; i = i + 1) {
                                             //console.log("req.bhjsdb", req.files.feedback_pic[i].location)
                                             feedback_pic.push(req.files.feedback_pic[i].location);
                                        }
                                        model.brandFeedbackModel(ff_id, retailer_le_id, status, buying_price, selling_price, weekly_sales_value, feedback_pic).then(inserted => {
                                             console.log("--------318---------")
                                             if (inserted) {
                                                  res.status(200).json({ 'status': 'success', 'message': cpmessage.FeedbackSubmitted });
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             res.status(200).json({ 'status': 'failed', 'message': cpmessage.internalCatch });
                                        })
                                   }
                              });
                         } else {
                              model.brandFeedbackModel(ff_id, retailer_le_id, status, buying_price, selling_price, weekly_sales_value, '').then(inserted => {
                                   console.log('-------330-----------')
                                   if (inserted) {
                                        res.status(200).json({ 'status': 'success', 'message': cpmessage.FeedbackSubmitted });
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpmessage.internalCatch });
                              })
                         }
                    } else {
                         console.log("-----missing retailer_details---------");
                         res.status(200).json({ 'status': 'failed', 'message': cpmessage.invalidRequestBody });
                    }
               } else {
                    console.log("--------ff details are missing-----------");
                    res.status(200).json({ 'status': 'failed', 'message': cpmessage.invalidRequestBody });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpmessage.invalidRequestBody });
          }


     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpmessage.serverError });
     }
}


/**
 * Purpose : Used to get user assigned features.
 * Required :user_id
 * Response : Returns all assigned feature for that user
 * Authore : Deepak Tiwari
 */

module.exports.getFeature = (req, res) => {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               if (typeof data.user_id != 'undefined' && data.user_id != '') {
                    let ff_user_id = (typeof data.ff_user_id != 'undefined' && data.ff_user_id != '') ? data.ff_user_id : 0;
                    model.getFeatures(data.user_id, ff_user_id).then(features => {
                         if (features) {
                              res.status(200).json({ 'status': 'success', 'message': "Available Features", 'data': features });
                         } else {
                              res.status(200).json({ 'status': 'failed' });
                         }

                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpmessage.internalCatch });
                    })
               } else {
                    console.log("--------user details are missing-----------");
                    res.status(200).json({ 'status': 'failed', 'message': cpmessage.invalidRequestBody });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpmessage.invalidRequestBody });
          }

     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpmessage.serverError });
     }
}

/**
 * Purpose : Used to encrypt or decrypt the input parameter.
 * Required :encrypted or decrypted parameter , flag
 * Response : Returns encrypted or decrypted response based on flag
 * Authore : Deepak Tiwari
 */
module.exports.encryptOrDecrypt = (req, res) => {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               if (data.flag == 1) {
                    console.log("-----Decryption----");
                    //decryption
                    let decryptionData = encryption.decrypt(data.body);
                    res.send(decryptionData);
               } else {
                    console.log("-----Encryption----");
                    //Encryption
                    let encryptedData = encryption.encrypt(req.body.data);
                    res.send(encryptedData);
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpmessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpmessage.serverError });
     }
}

/**
 * Purpose : Used to Beat Id ,Warehouse details based on pincode ,latitude ,longitude.
 * Required :Pincode ,Latitude ,Longitude.
 * Response : Returns BeatId ,Warehouse name , Warehouse Id.
 * Authore : Deepak Tiwari.
 */
module.exports.getBeatDetails =  (req,res)=>{
     try {
      if (typeof req.body.data != 'undefined' && req.body.data != '') {
           let data = JSON.parse(req.body.data);
          let pincode =  (typeof data.pincode!='undefined' && data.pincode !='')?data.pincode:0;
          let latitude = (typeof data.latitude!='undefined' && data.latitude !='')?data.latitude:0;
          let longitude = (typeof data.longitude!='undefined' && data.longitude !='')?data.longitude:0;
          model.getBeatInfo(pincode,latitude,longitude).then((response)=>{
               if(response.length > 0) {
                 res.status(200).json({'status':'success','data':response});
               } else {
                res.status(200).json({ 'status': 'failed', 'message': cpmessage.InactivePincode });
               }
          }).catch(err=>{
               console.log(err);
               res.status(200).json({ 'status': 'failed', 'message': cpmessage.internalCatch });
          })
      } else {
           res.status(200).json({ 'status': 'failed', 'message': cpmessage.invalidRequestBody });
      }  
     } catch(err){
      console.log(err);
      res.json({ 'status': 'failed', 'message': 'Internal server error' }); 
     }
 }
 
