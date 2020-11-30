const usermodel = require('../../accountModule/model/accountModel')
const validator = require("email-validator");
const upload = require('../../../config/s3Config');
const cpMessage = require('../../../config/cpMessage')




/*
Purpose : Used To get all icon details
author : Deepak Tiwari
Request : Require icon code 
Resposne : Return all icon details 
*/
module.exports.iconData = function (req, res) {
     try {
          usermodel.getIconData(170001, 170003, 170004).then((resposne) => {
               if (resposne != null) {
                    res.status(200).json({ status: "success", message: 'Available icon', data: resposne })
               } else {
                    res.status(200).json({ status: "failed", message: 'Icon Not Found' })
               }
          }).catch((err) => {
               console.log(err);
               res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
          });

     } catch (err) {
          console.log(err);
          res.status(500).json({ status: "failed", message: cpMessage.serverError })

     }

}

/*
Purpose : Used To get all icon details
author : Deepak Tiwari
Request : Require icon code
Resposne : Return all icon details
*/

module.exports.getStateCountries = function (req, res) {
     try {
          let Data = JSON.parse(req.body.data);
          if (Data.flag != null && Data.flag == 1) {
               usermodel.getCountries().then((resposne) => {
                    if (resposne != null) {
                         res.status(200).json({ status: "success", message: 'Available Country', data: resposne })
                    } else {
                         res.status(200).json({ status: "failed", message: 'Country Not Found' })
                    }
               }).catch((err) => {
                    console.log(err)
                    res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
               })

          } else if (Data.flag == 2) {
               if (Data.country != null) {
                    usermodel.getStates(Data.country).then((result) => {
                         if (result != null) {
                              res.status(200).json({ status: "success", message: 'Available States', data: result })
                         } else {
                              res.status(200).json({ status: "failed", message: 'State Not Found' })
                         }
                    }).catch((err) => {
                         console.log(err)
                         res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                    })

               } else {
                    res.status(200).json({ status: "failed", message: 'Please enter country' })
               }

          } else {

          }

     } catch (err) {
          console.log(err)
          res.status(500).json({ status: 'failed', message: cpMessage.serverError })
     }



}


/*
Purpose : getFfBeat used to get  beat based on ff_id
author : Deepak Tiwari
Request :  requires sales_token,ff_id
Resposne : Return all FFbeat  details
*/
module.exports.getFFBeat = function (req, res) {
     try {
          let Data = JSON.parse(req.body.data);
          if (Data.sales_token != null) {
               //checking sales token is correct or not
               usermodel.checkCustomerToken(Data.sales_token).then((resposne) => {
                    if (resposne > 0) {
                         if (Data.ff_id) {
                              let hub_list = Data.hub != null ? Data.hub : null;
                              usermodel.getTeamByUser(Data.ff_id).then((result) => {
                                   if (result != null) {
                                        usermodel.getFfBeat(result, hub_list).then((beat) => {
                                             if (beat != null) {
                                                  res.status(200).json({ status: 'success', message: "Available FF beat", data: beat })
                                             } else {
                                                  res.status(200).json({ status: 'failed', message: "No FF beat found" })
                                             }
                                        }).catch((error) => {
                                             console.log("err", error.message)
                                             res.status(200).json({ status: 'failed', message: cpMessage.internalCatch })
                                        })
                                   } else {
                                        res.status(200).json({ status: 'failed', message: "Not Found Any result" })
                                   }
                              }).catch((err) => {
                                   console.log(err.message)
                              })
                         } else {
                              res.status(200).json({ status: 'failed', message: "Please enter ff details" })
                         }
                    } else {
                         res.status(200).json({ status: 'failed', message: "You have already logged into the Ebutor System" })
                    }
               }).catch((err) => {
                    console.log(err)

               })
          } else {
               res.status(200).json({ status: 'failed', message: cpMessage.tokenNotPassed })
          }
     } catch (err) {
          console.log(err)
          res.status(200).json({ status: 'failed', message: cpMessage.serverError })
     }
}



/*
Purpose : getUserProfile used to get user details based on consumer_token
author : Deepak Tiwari
Request :  require consumer_token   
Resposne : Return user basic  details
*/
module.exports.getUserProfile = function (req, res) {
     try {
          let Data = JSON.parse(req.body.data);
          if (Data.customer_token != null) {
               usermodel.checkCustomerToken(Data.customer_token).then((checkCustomerToken) => {
                    if (checkCustomerToken > 0) {
                         let customer_token = Data.customer_token;
                         usermodel.getCustomerData(customer_token).then((response) => {
                              if (response != null) {
                                   res.status(200).json({ status: 'success', message: 'Updated Successfully', data: response })
                              } else {
                                   res.status(200).json({ status: 'failed', message: 'No data Found', })
                              }

                         }).catch((err) => {
                              console.log(err)
                              res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                         })
                    } else {
                         res.status(200).json({ status: 'session', message: cpMessage.invalidToken })
                    }
               }).catch((err) => {
                    console.log(err)
                    res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
               })
          } else {
               res.status(200).json({ status: 'failed', message: cpMessage.tokenNotPassed })
          }
     } catch (err) {
          console.log(err)
          res.status(200).json({ status: 'failed', message: cpMessage.serverError })
     }

}


/*
Purpose : getPincodeData function is used to get the areas,its city and state
author : Deepak Tiwari
Request :  Nothing 
Resposne : Return Pincode details from database
*/
module.exports.getPincodeData = function (req, res) {
     try {
          if (req.body.data != null) {
               let Data = JSON.parse(req.body.data);
               if (Data.pincode != null) {
               } else {
                    res.status(200).json({ status: 'failed', messsage: 'Please provide pass pincode' })
                    // die;
               }
               usermodel.getPincodeAreas(Data.pincode).then((response) => {
                    if (response != null) {
                         usermodel.getPincodeData(Data.pincode).then((code) => {
                              if (code != null) {
                                   res.status(200).json({ status: 'success', message: "getPincodeData", data: response, state_id: code.state_id, state_name: code.state_name })
                              } else {
                                   res.status(200).json({ status: 'success', message: "No Data", date: [], state_id: '', state_name: '' })
                              }

                         }).catch((err) => {
                              console.log(err);
                              res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                         })

                    } else {
                         res.status(200).json({ status: 'failed', message: "Unable to process your request Please try later.", date: [] })
                    }
               }).catch((err) => {
                    console.log(err);
                    res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
               })
          } else {
               res.status(200).json({ status: 'failed', message: cpMessage.invalidRequestBody, date: [] })
               die;
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ status: 'failed', message: cpMessage.serverError, date: [] })

     }


}



/*
Purpose : updateProfile function is used to update the customer profile usong the customer_token passed.
author : Deepak Tiwari
Request :  Nothing 
Resposne : Return response body wiht user updated profile 
*/
module.exports.updateProfile = function (req, res) {
     try {
          let DATA;
          let city;
          let state;
          let CustomerData = {};
          let firstname = {};
          let lastname = {};
          let filepath = [];
          let fssai_doc_path = [];
          let gst_doc_path = [];
          //request body validation
          if (req.body.data != null) {
               DATA = JSON.parse(req.body.data);
               //validating customerToken
               if (DATA.customer_token != null) {
                    usermodel.checkCustomerToken(DATA.customer_token).then((checkCustomerToken) => {
                         console.log("checkcustomerztoken", checkCustomerToken)
                         if (checkCustomerToken > 0) {
                              usermodel.customerLegalid(DATA.customer_token).then((le_id) => {
                                   if (le_id == 2) {
                                        res.status(200).json({ status: "failed", message: "Sales token Passed" })
                                   } else {
                                        console.log("else condition")
                                        usermodel.getLegalEntityTypeId(DATA.customer_token).then(legalEntityTypeId => {//this condition we have added to restrict dc/FC should not able to update retailers data
                                             console.log("====>legalentitytype", legalEntityTypeId);
                                             if (legalEntityTypeId == 0) {
                                                  res.status(200).json({ 'status': 'failed', 'message': cpMessage.UpdateProfileRestrictionForDcFc })
                                             } else {

                                                  let address_2 = (typeof DATA.address_2 != 'undefined' && DATA.address_2) ? DATA.address_2 : '';
                                                  let address_1 = (typeof DATA.address_1 != 'undefined' && DATA.address_1) ? DATA.address_1 : '';
                                                  let locality = (typeof DATA.locality != 'undefined' && DATA.locality) ? DATA.locality : '';
                                                  let landmark = (typeof DATA.landmark != 'undefined' && DATA.landmark) ? DATA.landmark : '';
                                                  let gstin = (typeof DATA.gstin != 'undefined' && DATA.gstin) ? DATA.gstin : '';
                                                  let arn_number = (typeof DATA.arn_number != 'undefined' && DATA.arn_number) ? DATA.arn_number : '';
                                                  let pref_value1 = typeof DATA.pref_value1 != 'undefined' ? DATA.pref_value1 : '';
                                                  let delivery_time = typeof DATA.delivery_time != 'undefined' ? DATA.delivery_time : '';
                                                  let beat_id = (typeof DATA.beat_id != 'undefined' && DATA.beat_id) ? DATA.beat_id : 0;
                                                  let fssai_number = (typeof DATA.fssai != 'undefined' && DATA.fssai) ? DATA.fssai : '';
                                                  let user_id, legal_entity_id, latitude, longitude;

                                                  //profile upload
                                                  if (typeof req.files.img != 'undefined' || typeof DATA.firstname != 'undefined' || typeof DATA.lastname != 'undefined') {
                                                       if (typeof req.files.img != 'undefined') {
                                                            var singleupload = upload.single('img');
                                                            singleupload(req, res, function (err, data) {
                                                                 if (err) {
                                                                      console.log(err)
                                                                 } else {
                                                                      //console.log("req.bhjsdb", req.files.img)
                                                                      filepath.push(req.files.img[0].location);
                                                                 }
                                                            });
                                                       }
                                                       if (typeof DATA.firstname != 'undefined' && DATA.firstname != '' || typeof DATA.lastname != 'undefined' && DATA.lastname != '') {
                                                            var pattern = new RegExp('^[a-zA-Z]+$/g');
                                                            if (DATA.firstname != '' && DATA.firstname.length < 32 && DATA.firstname.length > 0) {
                                                                 firstname = DATA.firstname;
                                                            } else {
                                                                 usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                      if (data != '') {
                                                                           res.status(200).json({ status: 'failed', message: cpMessage.InavlidFirst, data: data })
                                                                      }
                                                                 }).catch((err) => {
                                                                      console.log(err);
                                                                      res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                 })
                                                            }
                                                            if (typeof DATA.lastname != 'undefined' && DATA.lastname.length < 32 && DATA.lastname.length > 0) {
                                                                 lastname = DATA.lastname;
                                                            } else {
                                                                 usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                      if (data != '') {
                                                                           res.status(200).json({ status: 'failed', message: cpMessage.InvalidLastname, data: data })
                                                                      }
                                                                 }).catch((err) => {
                                                                      console.log(err);
                                                                      res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                 });
                                                            }

                                                       } else {
                                                            usermodel.getFirstname(DATA.customer_token).then((firstname_val => {
                                                                 if (firstname_val) {
                                                                      firstname = firstname_val[0]
                                                                      lastname = firstname_val[1]
                                                                 } else if (firstname_val == null) {
                                                                      firstname = '';
                                                                 }
                                                            })).catch((err) => {
                                                                 console.log(err)
                                                            })

                                                            usermodel.getLastname(DATA.customer_token).then((lastname_val) => {
                                                                 if (lastname_val) {
                                                                      lastname = lastname_val[0].lastname
                                                                 } else if (lastname_val == null) {
                                                                      lastname = '';
                                                                 }
                                                            }).catch((err) => {
                                                                 console.log(err)
                                                            })
                                                       }

                                                       //updating first name , lastname and user profile picture
                                                       usermodel.updateProfile(DATA.customer_token, firstname, filepath, lastname).then((CustomerData1) => {
                                                            console.log("=====>335 Updated successfully")
                                                       }).catch((err) => {
                                                            console.log(err)
                                                            res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                       })

                                                  }


                                                  if (typeof req.files.fssai_doc != 'undefined' || typeof req.files.gst_doc != 'undefined') {
                                                       let userId = (typeof DATA.user_id != 'undefined' && DATA.user_id != '') ? DATA.user_id : 0;
                                                       //fssai_doc 
                                                       if (typeof req.files.fssai_doc != 'undefined') {
                                                            var singleupload = upload.single('fssai_doc');
                                                            singleupload(req, res, async function (err, data) {
                                                                 if (err) {
                                                                      console.log(err)
                                                                 } else {
                                                                      fssai_doc_path.push(await req.files.fssai_doc[0].location);
                                                                      console.log("req", fssai_doc_path)
                                                                      //gst_doc
                                                                      if (typeof req.files.gst_doc != 'undefined') {
                                                                           var singleupload = upload.single('gst_doc');
                                                                           singleupload(req, res, function (err, data) {
                                                                                if (err) {
                                                                                     console.log(err)
                                                                                } else {
                                                                                     gst_doc_path.push(req.files.gst_doc[0].location);
                                                                                     if (gst_doc_path.length > 0) {
                                                                                          usermodel.updateGstDocPath(gst_doc_path, DATA.legal_entity_id, userId).then(update_doc => {
                                                                                               console.log("gst_doc and fssai_doc updated successfully");
                                                                                          }).catch(err => {
                                                                                               console.log(err);
                                                                                               res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                          })
                                                                                     }
                                                                                }
                                                                           });
                                                                      }
                                                                      if (fssai_doc_path.length > 0) {
                                                                           usermodel.updateFssaiDocPath(fssai_doc_path, DATA.legal_entity_id, userId).then(update_doc => {
                                                                                console.log("gst_doc and fssai_doc updated successfully");
                                                                           }).catch(err => {
                                                                                console.log(err);
                                                                                res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                           })
                                                                      }

                                                                 }

                                                            });
                                                       } else {
                                                            //gst_doc
                                                            if (typeof req.files.gst_doc != 'undefined') {
                                                                 var singleupload = upload.single('gst_doc');
                                                                 singleupload(req, res, function (err, data) {
                                                                      if (err) {
                                                                           console.log(err)
                                                                      } else {
                                                                           gst_doc_path.push(req.files.gst_doc[0].location);
                                                                           if (gst_doc_path.length > 0) {
                                                                                usermodel.updateGstDocPath(gst_doc_path, DATA.legal_entity_id, userId).then(update_doc => {
                                                                                     console.log("gst_doc and fssai_doc updated successfully");
                                                                                }).catch(err => {
                                                                                     console.log(err);
                                                                                     res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                })
                                                                           }
                                                                      }
                                                                 });
                                                            }
                                                       }
                                                  }

                                                  //updating business-type , company details company_name(business_legal_name)
                                                  if (typeof DATA.business_type != 'undefined' && DATA.business_type || typeof DATA.company != 'undefined' || DATA.buyer_type) {
                                                       usermodel.updateBussinessType(DATA.business_type, DATA.company, DATA.buyer_type, DATA.customer_token, DATA.ffid);
                                                  }

                                                  // city details
                                                  if (typeof DATA.city != 'undefined' && DATA.city) {
                                                       city = DATA.city;
                                                  }

                                                  //state Details
                                                  if (typeof DATA.state != 'undefined' && DATA.state) {
                                                       state = DATA.state;
                                                  }

                                                  //fssai number
                                                  if (typeof DATA.fssai != 'undefined' && DATA.fssai) {
                                                       usermodel.getFssaiNo(DATA.fssai).then(fssai_no => {// validating entered fssai number 
                                                            usermodel.getUserFssaiNo(DATA.customer_token).then(user_fssai_no => {// Used to get user existing fssai number from database
                                                                 if (fssai_no == 0 || DATA.fssai == user_fssai_no) {
                                                                      fssai_number = DATA.fssai;
                                                                 } else {
                                                                      res.status(200).json({ status: "failed", message: "Fssai Number Already Exist" })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                                            })

                                                       }).catch(err => {
                                                            console.log(err);
                                                            res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                                       })
                                                  }

                                                  //Updating customer table
                                                  if (DATA.internet_availability || DATA.manufacturers || DATA.No_of_shutters || DATA.area_id || DATA.volume_class || DATA.business_start_time || DATA.business_end_time || DATA.postcode || DATA.area || DATA.smartphone && DATA.city || DATA.state || beat_id) {
                                                       let facilities = typeof DATA.facilities != 'undefined' ? DATA.facilities : 0;
                                                       let is_icecream = typeof DATA.is_icecream != 'undefined' ? DATA.is_icecream : 0;
                                                       let sms_notification = typeof DATA.sms_notification != 'undefined' ? DATA.sms_notification : 0;
                                                       let is_milk = typeof DATA.is_milk != 'undefined' ? DATA.is_milk : 0;
                                                       let is_fridge = typeof DATA.is_fridge != 'undefined' ? DATA.is_fridge : 0;
                                                       let is_vegetables = typeof DATA.is_vegetables != 'undefined' ? DATA.is_vegetables : 0;
                                                       let is_visicooler = typeof DATA.is_visicooler != 'undefined' ? DATA.is_visicooler : 0;
                                                       let dist_not_serv = typeof DATA.dist_not_serv != 'undefined' ? DATA.dist_not_serv : '';
                                                       let is_deepfreezer = typeof DATA.is_deepfreezer != 'undefined' ? DATA.is_deepfreezer : 0;
                                                       let is_swipe = typeof DATA.is_swipe != 'undefined' ? DATA.is_swipe : 0;
                                                       let No_of_shutters = typeof DATA.No_of_shutters != 'undefined' ? DATA.No_of_shutters : 0;
                                                       usermodel.getMasterLookupValues(106).then((master_data) => {// returning brands detail
                                                            if (master_data != null) {
                                                                 let master_manf = [];
                                                                 for (let i = 0; i < master_data.length; i++) {
                                                                      master_manf[i] = master_data[i].value;
                                                                 }
                                                                 master_manf = master_manf.join();
                                                                 //updating customer details
                                                                 usermodel.updateCustomerTable(DATA.internet_availability, master_manf, No_of_shutters, DATA.area, DATA.volume_class, delivery_time, pref_value1, DATA.business_start_time, DATA.business_end_time, DATA.postcode, city, DATA.smartphone, DATA.customer_token, DATA.state, beat_id, is_icecream, sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe, fssai_number);
                                                            } else {
                                                                 master_manf = "";
                                                                 usermodel.updateCustomerTable(DATA.internet_availability, master_manf, No_of_shutters, DATA.area, DATA.volume_class, delivery_time, pref_value1, DATA.business_start_time, DATA.business_end_time, DATA.postcode, city, DATA.smartphone, DATA.customer_token, DATA.state, beat_id, is_icecream, sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe, fssai_number);


                                                            }
                                                       }).catch((err) => {
                                                            console.log(err)
                                                            res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                       })

                                                  }

                                                  //update geolocation
                                                  if (typeof DATA.user_id != 'undefined' && DATA.user_id && typeof DATA.legal_entity_id != 'undefined' && DATA.legal_entity_id && typeof DATA.latitude != 'undefined' && DATA.latitude && typeof DATA.longitude != 'undefined' && DATA.longitude) {
                                                       user_id = DATA.user_id;
                                                       legal_entity_id = DATA.legal_entity_id;
                                                       latitude = DATA.latitude;
                                                       longitude = DATA.longitude;
                                                       //updating latitude , longitude in legalentity table(Updating legal_entities)
                                                       usermodel.updateGeo(user_id, legal_entity_id, latitude, longitude).then((geo) => {
                                                            if (geo != null && geo >= 1) {
                                                                 //res.status(200).json({ status: "success", message: "Geo location updated successfully", data: geo })
                                                            }
                                                       }).catch((err) => {
                                                            console.log("err ========>", err)
                                                            res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                       })
                                                  }

                                                  //gstin
                                                  if (typeof DATA.gstin != 'undefined' && DATA.gstin) {
                                                       usermodel.getGstinNo(DATA.gstin).then(gstin_no => {// validating entered gst number 
                                                            usermodel.getUserGstinNo(DATA.customer_token).then(user_gstin_no => {// Used to get user existing gstin number from database
                                                                 if (gstin_no == 0 || DATA.gstin == user_gstin_no) {
                                                                      gstin = DATA.gstin
                                                                 } else {
                                                                      res.status(200).json({ status: "failed", message: "Gstin Already Exist" })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                                            })

                                                       }).catch(err => {
                                                            console.log(err);
                                                            res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                                       })
                                                  }

                                                  //arn number
                                                  if (typeof DATA.arn_number != 'undefined' && DATA.arn_number) {
                                                       usermodel.getArnNo(DATA.arn_number).then(arn_no => {
                                                            usermodel.getUserArnNo(DATA.customer_token).then(user_arn_no => {
                                                                 if (arn_no == 0 || user_arn_no == DATA.arn_number) {
                                                                      arn_number = DATA.arn_number;
                                                                 } else {
                                                                      res.status(200).json({ status: "failed", message: "Arn number Already Exist" })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                                            })
                                                       }).catch(err => {
                                                            console.log(err);
                                                            res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                                       })
                                                  }

                                                  //validating entered pincode weather entered pincode is  in servicable area or not
                                                  if (typeof DATA.postcode != 'undefined') {
                                                       if (DATA.postcode != '' && DATA.postcode.length == 6) {
                                                            usermodel.serviceablePincode(DATA.postcode).then((checkPincode) => {//checking weather entered pincode is serviceable or not
                                                                 if (checkPincode <= 0) {
                                                                      res.status(200).json({ status: 'failed', message: "Entered pincode area not comes under ebutor services" })
                                                                 } else {
                                                                      //email & mobile number
                                                                      if ((typeof DATA.email != 'undefined' && DATA.email != '') || (typeof DATA.telephone != 'undefined' && DATA.telephone != '')) {
                                                                           // //Sending otp for updating new mobile number 
                                                                           if (typeof DATA.telephone != 'undefined' && DATA.telephone != "") {
                                                                                if (DATA.telephone != null && DATA.telephone.length >= 10) {//validating telephone number
                                                                                     usermodel.allTelephone(DATA.telephone, DATA.customer_token).then((allTelephone) => {//checking weather entered number is exist or not 
                                                                                          console.log("AllTelephone", allTelephone[0]);
                                                                                          usermodel.getTelephone(DATA.customer_token).then(telephone => {//fetching user telephone number based on customer_token
                                                                                               if (telephone[0].mobile_no == DATA.telephone) {
                                                                                                    usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                                                         if (data != '') {
                                                                                                              res.status(200).json({ status: 'failed', message: "Entered number already exist", data: data })
                                                                                                         } else {
                                                                                                              console.log("empty")
                                                                                                         }
                                                                                                    }).catch((err) => {
                                                                                                         console.log(err);
                                                                                                         res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                                    })
                                                                                               } else if (telephone[0].mobile_no != DATA.telephone) {
                                                                                                    if (allTelephone[0] == 0) {// entered phone number not exist
                                                                                                         usermodel.updateTelephone(DATA.customer_token, DATA.telephone, DATA.ffid).then(response => {
                                                                                                              usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                                                                   if (data != '') {
                                                                                                                        res.status(200).json({ status: 'success', message: "Updated Successfully", data: data });
                                                                                                                   }
                                                                                                              }).catch((err) => {
                                                                                                                   console.log(err);
                                                                                                                   // res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                                              })
                                                                                                         }).catch((err) => {
                                                                                                              console.log(err);
                                                                                                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                                                                                         })
                                                                                                    } else if (allTelephone[0] != 0) {
                                                                                                         usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                                                              if (data != '') {
                                                                                                                   res.status(200).json({ 'status': 'failed', 'message': "Entered number already exist", data: data })
                                                                                                              } else {
                                                                                                                   res.status(200).json({ 'status': 'failed', 'message': 'Entered number already exist' })
                                                                                                              }
                                                                                                         }).catch((err) => {
                                                                                                              console.log(err);
                                                                                                              res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                                         })
                                                                                                    }
                                                                                               }
                                                                                          })
                                                                                     }).catch((err) => {
                                                                                          console.log(err);
                                                                                          res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                     })
                                                                                } else {
                                                                                     res.status(200).json({ 'status': 'failed', 'message': 'Please enter valid contact number' })
                                                                                }
                                                                           }

                                                                           //Email duplicate validation checking(check required)
                                                                           if (typeof DATA.email != 'undefined' && DATA.email != '') {
                                                                                if (DATA.email && validator.validate(DATA.email)) {
                                                                                     let email = DATA.email;
                                                                                     //Already exists mail id needs to skip for update
                                                                                     usermodel.eMailCheck(DATA.customer_token).then(email_verification => {
                                                                                          console.log("email_verification", email_verification)
                                                                                          if (email_verification == DATA.email) {
                                                                                               usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                                               usermodel.getCustomerData(DATA.customer_token).then((user_data) => {
                                                                                                    if (user_data != null) {
                                                                                                         res.status(200).json({ status: 'failed', message: 'Email already exist', data: user_data })
                                                                                                    }
                                                                                               }).catch((err) => {
                                                                                                    console.log(err)
                                                                                                    res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                               })
                                                                                          } else if (email_verification != DATA.email) {
                                                                                               //Email Exists check     
                                                                                               usermodel.getEmail(email).then(emailchk => {
                                                                                                    console.log("email", emailchk)
                                                                                                    if (typeof emailchk != 'undefined' && emailchk == 0) {
                                                                                                         email = DATA.email;
                                                                                                         //updating user email id
                                                                                                         if (email != null) {
                                                                                                              usermodel.updateEmail(DATA.customer_token, email, DATA.ffid).then(email_update => {
                                                                                                                   usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                                                                   usermodel.getCustomerData(DATA.customer_token).then((response) => {
                                                                                                                        if (response != null) {
                                                                                                                             res.status(200).json({ status: 'success', message: 'Updated Successfully', data: response })
                                                                                                                        } else {
                                                                                                                             res.status(200).json({ status: 'failed', message: 'No data Found', })
                                                                                                                        }

                                                                                                                   }).catch((err) => {
                                                                                                                        console.log(err);
                                                                                                                        res.status(200).json({ status: 'failed', message: cpMessage.internalCatch });
                                                                                                                   })
                                                                                                              }).catch((err) => {
                                                                                                                   console.log(err);
                                                                                                                   res.status(200).json({ status: 'failed', message: cpMessage.internalCatch });
                                                                                                              })

                                                                                                         }
                                                                                                    } else {
                                                                                                         res.status(200).json({ status: 'failed', message: 'Email already exist' })
                                                                                                    }
                                                                                               })


                                                                                          }
                                                                                     }).catch((err) => {
                                                                                          console.log(err);
                                                                                          res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                     })
                                                                                } else if (DATA.email == '') {
                                                                                     usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                                     usermodel.getCustomerData(DATA.customer_token).then((response) => {
                                                                                          if (response != null) {
                                                                                               res.status(200).json({ status: 'success', message: 'Please enter valid email', data: response })
                                                                                          } else {
                                                                                               res.status(200).json({ status: 'Failed', message: 'No data Found', })
                                                                                          }

                                                                                     }).catch((err) => {
                                                                                          console.log(err)
                                                                                          res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                     })
                                                                                }
                                                                           } else if (typeof DATA.email == 'undefined' || DATA.email == '') {
                                                                                usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                                usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                                     if (data != '') {
                                                                                          res.status(200).json({ status: 'success', message: "Updated Successfully", data: data });
                                                                                     }
                                                                                }).catch((err) => {
                                                                                     console.log(err);

                                                                                     res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                })
                                                                           }
                                                                      } else {
                                                                           usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                           usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                                if (data != '') {
                                                                                     res.status(200).json({ status: 'success', message: "Updated Successfully", data: data });
                                                                                }
                                                                           }).catch((err) => {
                                                                                console.log(err);
                                                                                res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                           })
                                                                      }
                                                                 }
                                                            }).catch((err) => {
                                                                 console.log(err)
                                                                 res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                            })
                                                       } else if (DATA.postcode == '' && DATA.postcode.length != 6) {
                                                            res.status(200).json({ status: 'failed', message: "Please enter valid postcode" })
                                                       }

                                                  } else {
                                                       //email & mobile number
                                                       if (typeof DATA.email != 'undefined' && DATA.email != '' || typeof DATA.telephone != 'undefined' && DATA.telephone != '') {
                                                            // //Sending otp for updating new mobile number 
                                                            if (typeof DATA.telephone != 'undefined' && DATA.telephone != "") {
                                                                 if (DATA.telephone != null && DATA.telephone.length >= 10) {//validating telephone number
                                                                      usermodel.allTelephone(DATA.telephone, DATA.customer_token).then((allTelephone) => {//checking weather entered number is exist or not 
                                                                           console.log("AllTelephone", allTelephone[0]);
                                                                           usermodel.getTelephone(DATA.customer_token).then(telephone => {//fetching user telephone number based on customer_token
                                                                                if (telephone[0].mobile_no == DATA.telephone) {
                                                                                     usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                                          if (data != '') {
                                                                                               res.status(200).json({ status: 'failed', message: "Entered number already exist", data: data })
                                                                                          } else {
                                                                                               console.log("empty")
                                                                                          }
                                                                                     }).catch((err) => {
                                                                                          console.log(err);
                                                                                          res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                     })
                                                                                } else if (telephone[0].mobile_no != DATA.telephone) {
                                                                                     if (allTelephone[0] == 0) {// entered phone number not exist
                                                                                          usermodel.updateTelephone(DATA.customer_token, DATA.telephone, DATA.ffid).then(response => {
                                                                                               usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                                                    if (data != '') {
                                                                                                         res.status(200).json({ status: 'success', message: "Updated Successfully", data: data });
                                                                                                    }
                                                                                               }).catch((err) => {
                                                                                                    console.log(err);
                                                                                                    // res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                               })
                                                                                          }).catch((err) => {
                                                                                               console.log(err);
                                                                                               res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                                                                          })
                                                                                     } else if (allTelephone[0] != 0) {
                                                                                          usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                                               if (data != '') {
                                                                                                    res.status(200).json({ 'status': 'failed', 'message': "Entered number already exist", data: data })
                                                                                               } else {
                                                                                                    res.status(200).json({ 'status': 'failed', 'message': 'Entered number already exist' })
                                                                                               }
                                                                                          }).catch((err) => {
                                                                                               console.log(err);
                                                                                               res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                          })
                                                                                     }
                                                                                }
                                                                           })
                                                                      }).catch((err) => {
                                                                           console.log(err);
                                                                           res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                      })
                                                                 } else {
                                                                      res.status(200).json({ 'status': 'failed', 'message': 'Please enter valid contact number' })
                                                                 }
                                                            }

                                                            //Email duplicate validation checking(check required)
                                                            if (typeof DATA.email != 'undefined' && DATA.email != '') {
                                                                 if (DATA.email && validator.validate(DATA.email)) {
                                                                      let email = DATA.email;
                                                                      //Already exists mail id needs to skip for update
                                                                      usermodel.eMailCheck(DATA.customer_token).then(email_verification => {
                                                                           console.log("email_verification", email_verification)
                                                                           if (email_verification == DATA.email) {
                                                                                usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                                usermodel.getCustomerData(DATA.customer_token).then((user_data) => {
                                                                                     if (user_data != null) {
                                                                                          res.status(200).json({ status: 'failed', message: 'Email already exist', data: user_data })
                                                                                     }
                                                                                }).catch((err) => {
                                                                                     console.log(err)
                                                                                     res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                                })
                                                                           } else if (email_verification != DATA.email) {
                                                                                //Email Exists check     
                                                                                usermodel.getEmail(email).then(emailchk => {
                                                                                     console.log("email", emailchk)
                                                                                     if (typeof emailchk != 'undefined' && emailchk == 0) {
                                                                                          email = DATA.email;
                                                                                          //updating user email id
                                                                                          if (email != null) {
                                                                                               usermodel.updateEmail(DATA.customer_token, email, DATA.ffid).then(email_update => {
                                                                                                    usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                                                    usermodel.getCustomerData(DATA.customer_token).then((response) => {
                                                                                                         if (response != null) {
                                                                                                              res.status(200).json({ status: 'success', message: 'Updated Successfully', data: response })
                                                                                                         } else {
                                                                                                              res.status(200).json({ status: 'failed', message: 'No data Found', })
                                                                                                         }

                                                                                                    }).catch((err) => {
                                                                                                         console.log(err);
                                                                                                         res.status(200).json({ status: 'failed', message: cpMessage.internalCatch });
                                                                                                    })
                                                                                               }).catch((err) => {
                                                                                                    console.log(err);
                                                                                                    res.status(200).json({ status: 'failed', message: cpMessage.internalCatch });
                                                                                               })

                                                                                          }
                                                                                     } else {
                                                                                          res.status(200).json({ status: 'failed', message: 'Email already exist' })
                                                                                     }
                                                                                })


                                                                           }
                                                                      }).catch((err) => {
                                                                           console.log(err);
                                                                           res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                      })
                                                                 } else if (DATA.email == '') {
                                                                      usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                      usermodel.getCustomerData(DATA.customer_token).then((response) => {
                                                                           if (response != null) {
                                                                                res.status(200).json({ status: 'success', message: 'Please enter valid email', data: response })
                                                                           } else {
                                                                                res.status(200).json({ status: 'Failed', message: 'No data Found', })
                                                                           }

                                                                      }).catch((err) => {
                                                                           console.log(err)
                                                                           res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                      })
                                                                 }
                                                            } else if (typeof DATA.email == 'undefined' || DATA.email == '') {
                                                                 usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                                 usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                      if (data != '') {
                                                                           res.status(200).json({ status: 'success', message: "Updated Successfully", data: data });
                                                                      }
                                                                 }).catch((err) => {
                                                                      console.log(err);

                                                                      res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                                 })
                                                            }
                                                       } else {
                                                            usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number, DATA.ffid);
                                                            usermodel.getCustomerData(DATA.customer_token).then((data) => {
                                                                 if (data != '') {
                                                                      res.status(200).json({ status: 'success', message: "Updated Successfully", data: data });
                                                                 }
                                                            }).catch((err) => {
                                                                 console.log(err);
                                                                 res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                                                            })
                                                       }
                                                  }


                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                        })
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ status: "failed", message: cpMessage.internalCatch });
                              })
                         } else {
                              res.status(200).json({ status: 'session', message: cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err)
                         res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ status: 'failed', message: cpMessage.tokenNotPassed })
               }
          } else {
               res.status(200).json({ status: "failed", message: cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ status: 'failed', message: cpMessage.serverError })
     }
}
/*
Purpose :  getShippingAddress function is used to handle the request of getting all the shipping address of the customer based on the customer_token passed.
author : Deepak Tiwari
Request :  Nothing 
Resposne : Return response body wiht user updated profile 
*/
module.exports.getShippingAddress = function (req, res) {
     try {
          let Data;
          let customer_token;
          let legal_entity_id;
          let customer_id;
          let address;
          let ecash;
          if (typeof req.body.data != 'undefined') {
               Data = JSON.parse(req.body.data)
          } else {
               res.status(200).json({ status: 'failed', message: 'Required parameter not passed' })
          }
          //checking customer is set or not
          if (typeof Data.customer_token != 'undefined' && Data.customer_token != '') {
               usermodel.checkCustomerToken(Data.customer_token).then(checkCustomerToken => {
                    if (checkCustomerToken > 0) {
                         customer_token = Data.customer_token;
                         if (customer_token != '') {
                              let legal_entity_id = (typeof Data.legal_entity_id != 'undefined' && Data.legal_entity_id != '') ? Data.legal_entity_id : '';
                              usermodel.getShippingAddress(customer_token, legal_entity_id).then(shippingAddress => {
                                   if (shippingAddress != '') {
                                        address = shippingAddress
                                   }
                                   usermodel.getUserIdByCustomerToken(customer_token).then(customer_id => {
                                        if (customer_id != '') {
                                             usermodel.getExistingEcash(customer_id).then(ecash_Amount => {
                                                  //flag = 0  when customer token is valid
                                                  let result = { 'address': address, 'flag': 0, 'ecash_amount': ecash_Amount.toString() }
                                                  res.status(200).json({ 'status': 'success', 'message': "getShippingAddress", 'data': result })
                                             }).catch(err => {
                                                  console.log(err)
                                             })
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })
                              }).catch(err => {
                                   console.log(err)
                              })

                         }
                    } else {
                         res.status(200).json({ status: 'session', message: 'You have already logged into the Ebutor System' })
                    }
               }).catch(err => {
                    console.log(err)
               })

          } else {
               res.status(200).json({ status: 'session', message: 'You have already logged into the Ebutor System' })

          }
     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }


}

/*
Purpose :  saveAddress function is used to add address for the customer.
author : Deepak Tiwari
Request :  Address detailes
Resposne : Return updated address details
*/
module.exports.saveAddress = function (req, res) {
     try {
          let data;
          let customer_token;
          let flag;
          let Details_decode = [];
          let response = {};
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data)
          } else {
               res.status(200).json({ status: 'failed', message: 'Required parameter not passed' })
          }

          if (typeof data.customer_token != 'undefined' && data.customer_token != '') {
               usermodel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                    if (checkCustomerToken > 0) {
                         customer_token = data.customer_token;
                         //checking weather flag is set or not 
                         if (typeof data.flag != 'undefined' && data.flag != '' && (data.flag == 1 || data.flag == 2)) {
                              flag = data.flag;
                         } else {
                              res.status(200).json({ status: 'failed', message: 'Invalid flag value' })
                         }

                         if (flag == 1) {//flag one is for save address in legal_entity_warehouses table
                              if (Array.isArray(data.Details)) {
                                   Details_decode.push(data.Details);
                              } else {
                                   Details_decode.push(data.Details)
                              }


                              if (data.Details == '' || data.Details.length == 0) {
                                   res.status(200).json({ status: 'failed', message: 'Address detail not provided' })
                              }

                              for (let i = 0; i < Details_decode.length; i++) {
                                   let FirstName = Details_decode[i].FirstName;
                                   let LastName = Details_decode[i].LastName;
                                   let Address = Details_decode[i].Address;
                                   let Address1 = Details_decode[i].Address1;
                                   let locality = Details_decode[0].locality;
                                   let landmark = Details_decode[0].landmark;
                                   let City = Details_decode[i].City;
                                   let pin = Details_decode[i].pin;
                                   let state = Details_decode[i].state;
                                   let country = Details_decode[i].country;
                                   let addressType = Details_decode[i].addressType;
                                   let telephone = typeof Details_decode[i].telephone ? Details_decode[i].telephone : '';
                                   let email = typeof Details_decode[i].email ? Details_decode[i].email : '';

                                   console.log("======>888")
                                   //validating address 
                                   usermodel.check_duplicate_address(Details_decode[i], customer_token).then(checkDuplicateAddress => {
                                        if (checkDuplicateAddress >= 1) {
                                             res.status(200).json({ status: 'failed', message: 'Address already exist' });
                                        }
                                   })

                                   if (FirstName == '') {
                                        response = { status: 'failed', message: 'Please provide firstname' }
                                   }

                                   if (Address == '') {
                                        response = { status: 'failed', message: 'Please provide address details' }
                                        console.log(response)
                                   }

                                   if (City == '') {
                                        response = { status: 'failed', message: 'Please enter city datails' }
                                        console.log(response)

                                   }

                                   if (telephone == '' && telephone.length !== 10) {
                                        res.status(200).json({ status: 'failed', message: 'You have entered invalid telephone' })
                                   }

                                   if (pin == '' || pin.length !== 6) {
                                        res.status(200).json({ status: 'failed', message: 'You have enteres invalid pincode' })
                                   }

                                   if (state == '') {
                                        response = { status: 'failed', message: 'Please enter state detailes' }
                                        console.log(response)
                                   }

                                   if (country == '') {
                                        response = { status: 'failed', message: 'Please enter country details' }
                                        console.log(response)
                                   }

                                   if (addressType == '') {
                                        response = { status: 'failed', message: 'Please enter adrresstype' }
                                        console.log(response)

                                   }

                                   usermodel.addAddress(Details_decode, customer_token).then(addAddress => {
                                        if (addAddress != '') {
                                             res.status(200).json({ status: 'success', message: "Address added successfully", data: addAddress })
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })
                              }

                         } else if (flag == 2) {//flag 2 is for edit address in legal_entity_warehouses table
                              if (Array.isArray(data.Details)) {
                                   Details_decode.push(data.Details);
                              } else {
                                   Details_decode.push(data.Details)
                              }


                              if (data.Details == '' || data.Details.length == 0) {
                                   res.status(200).json({ status: 'failed', message: 'Address detail not provided' })
                              }

                              for (let i = 0; i < Details_decode.length; i++) {
                                   let le_wh_id = Details_decode[0].address_id;
                                   let FirstName = Details_decode[0].FirstName;
                                   let LastName = Details_decode[0].LastName;
                                   let Address = Details_decode[0].Address;
                                   let Address1 = Details_decode[0].Address1;
                                   let locality = Details_decode[0].locality;
                                   let landmark = Details_decode[0].landmark;
                                   let City = Details_decode[0].City;
                                   let pin = Details_decode[0].pin;
                                   let state = Details_decode[0].state;
                                   let country = Details_decode[0].country;
                                   let addressType = Details_decode[0].addressType;
                                   let telephone = typeof Details_decode[0].telephone ? Details_decode[0].telephone : '';
                                   let email = typeof Details_decode[0].email ? Details_decode[0].email : '';

                                   //validations
                                   usermodel.check_duplicate_address(Details_decode[i], customer_token).then(checkDuplicateAddress => {
                                        if (checkDuplicateAddress >= 1) {
                                             res.status(200).json({ status: 'failed', message: 'Address already exist' });
                                        } else {

                                             if (FirstName == '') {
                                                  response = { status: 'failed', message: 'Please provide firstname' }
                                             }

                                             if (Address == '') {
                                                  response = { status: 'failed', message: 'Please provide address details' }
                                                  console.log(response)
                                             }

                                             if (City == '') {
                                                  response = { status: 'failed', message: 'Please enter city datails' }
                                                  console.log(response)

                                             }

                                             if (telephone == '' && telephone.length !== 10) {
                                                  res.status(200).json({ status: 'failed', message: 'You have entered invalid telephone' })
                                             }

                                             if (pin == '' || pin.length !== 6) {
                                                  res.status(200).json({ status: 'failed', message: 'You have enteres invalid pincode' })
                                             }


                                             if (state == '') {
                                                  response = { status: 'failed', message: 'Please enter state detailes' }
                                                  console.log(response)
                                             }

                                             if (country == '') {
                                                  response = { status: 'failed', message: 'Please enter country details' }
                                                  console.log(response)
                                             }

                                             if (addressType == '') {
                                                  response = { status: 'failed', message: 'Please enter adrresstype' }
                                                  console.log(response)

                                             }
                                             usermodel.editAddress(Details_decode, customer_token).then(editAddress => {
                                                  if (editAddress != '') {
                                                       res.status(200).json({ status: 'success', message: "Address edited successfully", data: editAddress })
                                                  }
                                             }).catch(err => {
                                                  console.log(err)
                                             })
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })


                              }

                         }
                    } else {
                         res.status(200).json({ status: 'session', message: 'You have already logged into the Ebutor System' })
                    }
               })
          } else {
               res.status(200).json({ status: 'session', message: 'You have already logged into the Ebutor System' })
          }


     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': 'failed', 'message': "Internal server error" });
     }

}

/*
Purpose : EditAddress function is used to add address for the customer.
author : Deepak Tiwari
Request :  Address detailes
Resposne : Return updated address details
*/
module.exports.editAddress = function (req, res) {
     try {
          let data;
          let customer_token;
          let flag;
          let Details_decode = [];
          let response = {};
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data)
          } else {
               res.status(200).json({ status: 'failed', message: 'Required parameter not passed' })
          }

          if (typeof data.customer_token != 'undefined' && data.customer_token != '') {
               usermodel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                    if (checkCustomerToken > 0) {
                         customer_token = data.customer_token;
                         if (customer_token) {
                              if (Array.isArray(data.Details)) {
                                   Details_decode.push(data.Details);
                              } else {
                                   Details_decode.push(data.Details)
                              }

                              if (data.Details == '' || data.Details.length == 0) {
                                   res.status(200).json({ status: 'failed', message: 'Address detail not provided' })
                              }

                              for (let i = 0; i < Details_decode.length; i++) {
                                   let le_wh_id = Details_decode[0].address_id;
                                   let FirstName = Details_decode[0].FirstName;
                                   let LastName = Details_decode[0].LastName;
                                   let Address = Details_decode[0].Address;
                                   let Address1 = Details_decode[0].Address1;
                                   let locality = Details_decode[0].locality;
                                   let landmark = Details_decode[0].landmark;
                                   let City = Details_decode[0].City;
                                   let pin = Details_decode[0].pin;
                                   let state = Details_decode[0].state;
                                   let country = Details_decode[0].country;
                                   let addressType = Details_decode[0].addressType;
                                   let telephone = typeof Details_decode[0].telephone ? Details_decode[0].telephone : '';
                                   let email = typeof Details_decode[0].email ? Details_decode[0].email : '';

                                   //validations
                                   usermodel.check_duplicate_address(Details_decode[i], customer_token).then(checkDuplicateAddress => {
                                        if (checkDuplicateAddress >= 1) {
                                             res.status(200).json({ status: 'failed', message: 'Address already exist' });
                                        } else {
                                             if (FirstName == '') {
                                                  response = { status: 'failed', message: 'Please provide firstname' }
                                             }

                                             if (Address == '') {
                                                  response = { status: 'failed', message: 'Please provide address details' }
                                                  console.log(response)
                                             }

                                             if (City == '') {
                                                  response = { status: 'failed', message: 'Please enter city datails' }
                                                  console.log(response)

                                             }

                                             if (telephone == '' && telephone.length !== 10) {
                                                  res.status(200).json({ status: 'failed', message: 'You have entered invalid telephone' })
                                             }

                                             if (pin == '' || pin.length !== 6) {
                                                  res.status(200).json({ status: 'failed', message: 'You have enteres invalid pincode' })
                                             }


                                             if (state == '') {
                                                  response = { status: 'failed', message: 'Please enter state detailes' }
                                                  console.log(response)
                                             }

                                             if (country == '') {
                                                  response = { status: 'failed', message: 'Please enter country details' }
                                                  console.log(response)
                                             }

                                             if (addressType == '') {
                                                  response = { status: 'failed', message: 'Please enter adrresstype' }
                                                  console.log(response)

                                             }
                                             usermodel.editAddress(Details_decode, customer_token).then(editAddress => {
                                                  if (editAddress != '') {
                                                       res.status(200).json({ status: 'success', message: "Address edited successfully", data: editAddress })
                                                  }
                                             }).catch(err => {
                                                  console.log(err)
                                             })

                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }
                         }
                    } else {
                         res.status(200).json({ status: 'session', message: 'You have already logged into the Ebutor System' })
                    }
               }).catch(err => {
                    console.log(err)
               })
          } else {
               res.status(200).json({ status: 'session', message: 'You have already logged into the Ebutor System' })
          }
     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': 'failed', 'message': "Internal server error" });
     }


}


/*
Purpose :  DisableContactuser function is disable the customer 
author : Deepak Tiwari
Request :  Require user contact info
Resposne : Will disable user contact 
*/
module.exports.DisableContactuser = function (req, res) {
     try {
          let data = JSON.parse(req.body.data);
          if (typeof data.customer_token != 'undefined' && data.customer_token != '') {
               if (typeof data.telephone != 'undefined' && data.telephone != '') {
                    usermodel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                         if (checkCustomerToken > 0) {
                              usermodel.DisableContactuser(data.customer_token, data.telephone).then(DisableContactuser => {
                                   let response = { 'telephone': data.telephone };
                                   if (DisableContactuser > 0) {
                                        res.status(200).json({ 'status': 'success', 'message': "Successfully disabled the contact", 'data': response })
                                   }
                                   else {
                                        res.status(200).json({ 'status': 'failed', 'message': "Unable to disable the contact", 'data': response })
                                   }
                              })
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': 'You have already logged into the Ebutor System' })
                         }
                    }).catch(err => {
                         console.log(err)
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': 'Please enter your telephone number' })
               }
          } else {
               res.status(200).json({ 'status': 'session', 'message': 'You have already logged into the Ebutor System' })
          }

     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}

/*
Purpose :  DisableContactuser function is disable the customer contact number
author : Deepak Tiwari
Request :  Require user contact info
Resposne : Will disable user contact 
*/
module.exports.timeslotData = function (req, res) {
     try {
          let data = JSON.parse(req.body.data);
          usermodel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
               if (checkCustomerToken > 0) {
                    usermodel.getTimeslotData().then(slot => {
                         if (slot) {
                              res.status(500).json({ 'status': 'success', 'message': 'success', 'data': slot })
                         } else {
                              res.status(500).json({ 'status': 'failed', 'message': 'No timeslot found' })
                         }
                    })
               } else {
                    res.status(200).json({ 'status': 'session', 'message': 'You have already logged into the Ebutor System' })
               }
          })
     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}


//used to validate entered mobile
module.exports.confirmMobileNumber = function (req, res) {
     try {
          //validating mobile number
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.telephone != 'undefined' && typeof data.otp_sent != 'undefined') {
                    usermodel.getOtp(data.telephone).then((otp) => {
                         if (otp != null) {
                              if (data.otp_sent != null && data.otp_sent.length == 6 && data.otp_sent == otp.otp) {
                                   res.status(200).json({ status: 'success', message: "Number verified successfully " })
                                   usermodel.deleteFromUserTemp(data.telephone);
                              } else {
                                   res.status(200).json({ status: "failed", message: "Incorrect otp" })
                              }
                         } else {
                              res.status(200).json({ status: "failed", message: "Please genearte otp agian!" })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': "failed", 'message': "Please enter otp" })
               }
          } else {
               res.status(200).json({ 'status': "failed", 'message': cpMessage.invalidRequestBody })
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': "failed", 'message': cpMessage.serverError })
     }
}

// used to generate otp at the time mobile number update 
module.exports.generateOtpForVerification = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.telephone != 'undefined' && data.telephone != '') {
                    if (data.telephone.length >= 10) {
                         usermodel.allTelephone(data.telephone, null).then(allTelephone => {
                              if (allTelephone[0] == 0) {// entered phone number not exist
                                   usermodel.generateOtpForMobileValidate(data.telephone).then(result => {
                                        res.status(200).json(result)
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                                   })
                              } else {

                                   res.status(200).json({ 'status': 'failed', 'message': "Entered number already exist" })

                              }
                         })
                    } else {
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidTelephoneNumber })
                    }
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': "Please enter mobile number" })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}


module.exports.emailValidation = function (req, res) {
     try {
          console.log("hello")
          //validating mobile number
          if (typeof req.body.data != 'undefined') {
               let DATA = JSON.parse(req.body.data);
               //Email duplicate validation checking(check required)
               if (typeof DATA.email != 'undefined' && DATA.email != '') {
                    if (DATA.email && validator.validate(DATA.email)) {
                         let email = DATA.email;
                         //Already exists mail id needs to skip for update
                         usermodel.eMailCheck(DATA.customer_token).then(email_verification => {
                              console.log("email_verification", email_verification)
                              if (email_verification == DATA.email) {
                                   // usermodel.updateAddressData(DATA.customer_token, address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number, fssai_number);
                                   res.status(200).json({ status: 'failed', message: 'Email already exist' })
                              } else {
                                   //Email Exists check     
                                   usermodel.getEmail(email).then(emailchk => {
                                        console.log("email", emailchk)
                                        if (emailchk == 1) {
                                             res.status(200).json({ status: 'failed', message: 'Email already exist' })
                                        } else {
                                             res.status(200).json({ status: 'success', message: '' })
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              }
                         }).catch((err) => {
                              console.log(err);
                              res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
                         })
                    } else {
                         res.status(200).json({ status: 'failed', message: 'Please enter valid email.' })
                    }
               }
          } else {
               res.status(200).json({ 'status': "failed", 'message': cpMessage.invalidRequestBody })
          }


     } catch (err) {
          console.log(err);
     }
}


module.exports.stateValidator = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               usermodel.stateValidator(data.state, data.postcode).then(response => {
                    res.status(200).json(response)
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
               })
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}


/*
Purpose :  Simpleapi to get userdetails for chat application
author : Deepak Tiwari
Request :  Require user contact info.
Resposne : return userdetails.
*/
module.exports.chatBotApi = function (req, res) {
     try {
          let data = JSON.parse(req.body.data);
          usermodel.getChatDetails(data.telephone).then(response => {
               if (response) {
                    res.status(200).json({ 'status': 'success', 'message': "UserDetails", "data": response })
               } else {
                    res.status(200).json({ 'status': 'success', 'message': "UserDetails", "data": [] })
               }
          }).catch(err => {
               console.log(err);
               res.status(500).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
          })

     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}

module.exports.getCustomerDetails = function (req, res) {
     try {
          let data = JSON.parse(req.body.data);
          usermodel.getCustomerData(data.customer_token).then((data) => {
               if (data != '') {
                    res.status(200).json({ status: 'success', message: 'Customer Details', data: data })
               }
          }).catch((err) => {
               console.log(err);
               res.status(200).json({ status: "failed", message: cpMessage.internalCatch })
          })

     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}
