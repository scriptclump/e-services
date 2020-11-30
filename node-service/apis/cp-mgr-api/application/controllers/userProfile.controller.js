
const usermodel = require('../non_sequelize_model/userProfile.model')
var validator = require("email-validator");
//var upload = require('../../../cp-mgr-api/config/s3config');
const aws = require('aws-sdk');
const BUCKET_NAME = 'ebutormedia-test';
const IAM_USER_KEY = 'AKIAJTLG7MDDMDYFY3NQ';
const IAM_USER_SECRET = '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';




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
               res.status(200).json({ status: "failed", message: 'Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001' })
          });

     } catch (err) {
          res.status(500).json({ status: "failed", message: 'Internal server error Error_Code : 5000' })

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
                    res.status(200).json({ status: "failed", message: 'Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001' })
               })

          } else if (Data.flag == 2) {
               if (Data.country != null) {
                    usermodel.getStates(Data.country).then((result) => {
                         if (result != null) {
                              res.status(200).json({ status: "success", message: 'Available States.', data: result })
                         } else {
                              res.status(200).json({ status: "failed", message: 'State Not Found.' })
                         }
                    }).catch((err) => {
                         console.log(err)
                         res.status(200).json({ status: "failed", message: 'Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001' })
                    })

               } else {
                    res.status(200).json({ status: "failed", message: 'Required parameters missing.' })
               }

          } else {
               console.log("Please provide flag details.")
          }

     } catch (err) {
          console.log(err)
          res.status(500).json({ success: false, message: 'Internal server error Error_Code : 5000' })
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
                                                  res.status(200).json({ status: 'success', message: "Available beat", data: beat })
                                             } else {
                                                  res.status(200).json({ status: 'failed', message: "Unable to process your request.Plz contact support on - 04066006442." })
                                             }
                                        }).catch((error) => {
                                             console.log("err", error)
                                             res.status(200).json({ success: false, message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001" })
                                        })
                                   } else {
                                        res.status(200).json({ success: false, message: "Could not get any response. Plz contact support on - 04066006442." })
                                   }
                              }).catch((err) => {
                                   console.log(err)
                              })
                         } else {
                              res.status(200).json({ success: false, message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001" })
                         }
                    } else {
                         res.status(200).json({ success: false, message: "Your Session Has Expired. Please Login Again." })
                    }
               }).catch((err) => {
                    console.log(err)

               })
          } else {
               res.status(200).json({ success: false, message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001" })
          }
     } catch (err) {
          console.log(err)
          res.status(200).json({ success: false, message: "Internal server error Error_Code : 5000" })
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
                                   res.status(200).json({ status: 'success', message: 'User Details', data: response })
                              } else {
                                   res.status(200).json({ status: 'failed', message: 'No data Found', })
                              }

                         }).catch((err) => {
                              console.log(err)
                         })
                    } else {
                         let Data = [];
                         res.status(200).json({ status: 'Session', message: "Your Session Has Expired. Please Login Again.", data: Data })
                    }
               }).catch((err) => {
                    console.log(err)
               })
          } else {
               let Data = [];
               res.status(200).json({ status: 'Session', message: "Your Session Has Expired. Please Login Again.", data: Data })
          }
     } catch (err) {
          console.log(err)
          let Data = [];
          res.status(200).json({ status: 'Failed', message: "Internal server error", data: Data })
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
                    res.status(200).json({ status: 'failed', messsage: 'Please enter pincode.' })
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
                              res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001.", date: [] })
                         })

                    } else {
                         res.status(200).json({ status: 'failed', message: "Something went wrong.Plz contact support on - 04066006442.", date: [] })
                    }
               }).catch((err) => {
                    console.log(err);
                    res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001.", date: [] })
               })
          } else {
               res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001", date: [] })
               die;
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ status: 'failed', message: "Internal server error Error_Code : 5000.", date: [] })

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
          if (req.body.data != null) {
               DATA = JSON.parse(req.body.data);
          } else {
               res.status(200).json({ status: "Failed", message: "Required Parameters not Passed" })
          }
          if (DATA.customer_token != null) {
               usermodel.checkCustomerToken(DATA.customer_token).then((checkCustomerToken) => {
                    if (checkCustomerToken > 0) {
                         usermodel.customerLegalid(DATA.customer_token).then((le_id) => {
                              if (le_id == 2) {
                                   res.status(200).json({ status: "failed", message: "Invalid token" })
                              }
                         }).catch(err => {
                              console.log(err)
                         })
                    } else {
                         res.status(200).json({ status: 'session', message: "Your Session Has Expired. Please Login Again." })
                    }
               }).catch(err => {
                    console.log(err)
               })
          } else {
               res.status(200).json({ status: 'session', message: "Your Session Has Expired. Please Login Again." })
          }

          let firstname = {};
          let lastname = {};
          let filepath = [];
          if (typeof req.files.img != 'undefined' || typeof DATA.firstname != 'undefined' || typeof DATA.lastname != 'undefined') {
               if (typeof req.files.img != 'undefined') {
                    var singleupload = upload.single('img');
                    singleupload(req, res, function (err, data) {
                         if (err) {
                              console.log(err)
                              // res.status(200).json({ success: false, message: "Unable to upload " })
                         } else {
                              filepath.push(req.files.img[0].location);
                         }
                    });
               }
               if (typeof DATA.firstname != 'undefined' && DATA.firstname != null || typeof DATA.lastname != 'undefined' && DATA.lastname != null) {
                    var pattern = new RegExp('^[a-zA-Z]+$/g');
                    if (DATA.firstname != null && DATA.firstname.length < 32 && DATA.firstname.length > 1) {
                         firstname = DATA.firstname;
                    } else {
                         usermodel.getCustomerData(DATA.customer_token).then((data) => {
                              if (data != null) {
                                   // res.status(200).json({ status: 'failed', message: "Firstname is invalid ", data: data })
                              } else {

                              }
                         }).catch((err) => {
                              console.log(err);
                         })
                    }
                    if (DATA.lastname != null && DATA.lastname.length < 32 && DATA.lastname.length > 1) {
                         lastname = DATA.lastname;
                    } else {
                         usermodel.getCustomerData(DATA.customer_token).then((data) => {
                              if (data != null) {
                                   //res.status(200).json({ status: 'failed', message: "lastname is invalid ", data: data })
                              } else {

                              }
                         }).catch((err) => {
                              console.log(err);
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
               usermodel.updateProfile(DATA.customer_token, firstname, filepath, lastname).then((CustomerData1) => {
                    if (CustomerData1 != null) {
                         // res.status(200).json({ status: 'success', message: 'Successfully Updated Profile', data: CustomerData1 })
                    } else {
                    }

               }).catch((err) => {
                    console.log(err)
               })

          };

          //email update and sending a notification mail 
          if (typeof DATA.email != 'undefined') {
               if (DATA.email && validator.validate(DATA.email)) {
                    let email = DATA.email;

                    if (email != null) {
                         //model to update email in db
                         let count = usermodel.updateEmail(DATA.customer_token, email)
                         if (count == 1) {
                              let email_id = { 'email': email }
                              //let template = usermodel.Test_email(DATA.firstname)
                              //usermodel.sendMail('successful updation', email, template);
                              // res.status(200).json({ status: 'success', message: 'Email Successfully Updated', data: email_id })
                         } else {
                              //  res.status(200).json({ status: 'failed', message: 'Failed to update Database' })
                         }
                    }
               } else {
                    let data1 = [];
                    usermodel.getCustomerData(DATA.customer_token).then((customer_details) => {
                         if (customer_details != null) {
                              data1 = customer_details[0]
                              //  res.status(200).json({ status: 'failed', message: "Please enter valid email", data: customer_details })
                         } else {

                         }
                    }).catch((err) => {
                         console.log(err);
                    })

               }
          }

          // //Sending otp for updating new mobile number 
          if (typeof DATA.telephone != 'undefined' && typeof DATA.otp_sent == 'undefined') {
               if (DATA.telephone != null && DATA.telephone.length >= 10) {
                    usermodel.allTelephone(DATA.telephone, DATA.customer_token).then((allTelephone) => {
                         if (allTelephone) {
                              let telephone = usermodel.getTelephone(DATA.customer_token);
                              if (telephone == DATA.telephone) {
                              } else {
                                   if (allTelephone == 0) {
                                        usermodel.generateOtp(DATA.customer_token, DATA.telephone).then((response) => {
                                             if (response != null) {
                                                  let result_telephone = [];
                                                  let telephone = DATA.telephone;
                                                  let otp = response;
                                                  result_telephone.push(telephone);
                                                  result_telephone.push(otp);
                                                  //res.status(200).json({ status: "success", message: 'Telephone details', data: result_telephone })
                                             }
                                        }).catch((err) => {
                                             console.log(err);
                                        })
                                   } else {

                                   }
                              }
                         }
                    }).catch((err) => {
                         console.log(err)
                    })


               } else {

               }

          }
          //validating mobile number
          if (typeof DATA.telephone != 'undefined' && typeof DATA.otp_sent != 'undefined') {
               usermodel.getOtp(DATA.customer_token).then((otp) => {
                    if (otp != null) {
                         if (DATA.otp_sent != null && DATA.otp_sent.length == 6 && DATA.otp_sent == otp.otp) {
                              console.log('hello')
                              usermodel.updateTelephone(DATA.customer_token, DATA.telephone);
                              let updatedTel = [];
                              let telephone = DATA.telephone;
                              updatedTel.push(telephone);
                              res.status(200).json({ status: 'success ', message: "Telephone number successfully updated", data: updatedTel })
                         } else {
                              //res.status(200).json({ status: "Failed", message: "Please enter valid otp" })
                         }

                    } else {
                    }
               }).catch(err => {
                    console.log(err);
               })
          }
          if (typeof DATA.business_type != 'undefined' && DATA.business_type || typeof DATA.company != 'undefined' || DATA.buyer_type) {
               usermodel.updateBussinessType(DATA.business_type, DATA.company, DATA.buyer_type, DATA.customer_token);
          }

          if (typeof DATA.postcode != 'undefined' && DATA.postcode && DATA.postcode.length == 6) {
               usermodel.serviceablePincode(DATA.postcode).then((checkPincode) => {
                    if (checkPincode > 0) {
                    } else {
                         res.status(200).json({ status: 'failed', message: "Please enter valid postcode" })
                    }
               }).catch((err) => {
                    console.log(err)
               })
          } else {
          }
          if (typeof DATA.city != 'undefined' && DATA.city) {
               city = DATA.city;
          } else {
          }

          if (typeof DATA.state != 'undefined' && DATA.state) {
               state = DATA.state;
          } else {
          }

          let pref_value1 = DATA.pref_value1 != null ? DATA.pref_value1 : '';
          let delivery_time = DATA.delivery_time != null ? DATA.delivery_time : '';
          let beat_id = DATA.beat_id != null ? DATA.beat_id : 0;
          if (DATA.internet_availability || DATA.manufacturers || DATA.No_of_shutters || DATA.area_id || DATA.volume_class || DATA.business_start_time || DATA.business_end_time || DATA.postcode || DATA.area || DATA.smartphone && DATA.city || DATA.state || beat_id) {
               let facilities = typeof DATA.facilities ? DATA.facilities : 0;
               let is_icecream = typeof DATA.is_icecream ? DATA.is_icecream : 0;
               let sms_notification = typeof DATA.sms_notification ? DATA.sms_notification : 0;
               let is_milk = typeof DATA.is_milk ? DATA.is_milk : 0;
               let is_fridge = typeof DATA.is_fridge ? DATA.is_fridge : 0;
               let is_vegetables = typeof DATA.is_vegetables ? DATA.is_vegetables : 0;
               let is_visicooler = typeof DATA.is_visicooler ? DATA.is_visicooler : 0;
               let dist_not_serv = typeof DATA.dist_not_serv ? DATA.dist_not_serv : '';
               let is_deepfreezer = typeof DATA.is_deepfreezer ? DATA.is_deepfreezer : 0;
               let is_swipe = typeof DATA.is_swipe ? DATA.is_swipe : 0;
               usermodel.getMasterLookupValues(106).then((master_data) => {
                    if (master_data != null) {
                         let master_manf = [];
                         for (let i = 0; i < master_data.length; i++) {
                              master_manf[i] = master_data[i].value;
                         }
                         master_manf = master_manf.join();
                         usermodel.updateCustomerTable(DATA.internet_availability, master_manf, DATA.No_of_shutters, DATA.area, DATA.volume_class, delivery_time, pref_value1, DATA.business_start_time, DATA.business_end_time, DATA.postcode, city, DATA.smartphone, DATA.customer_token, DATA.state, beat_id, is_icecream, sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe);
                    } else {

                    }
               }).catch((err) => {
                    console.log(err)
               })

          }

          let user_id, legal_entity_id, latitude, longitude;
          if (typeof DATA.user_id != 'undefined' && DATA.user_id && typeof DATA.legal_entity_id != 'undefined' && DATA.legal_entity_id && typeof DATA.latitude != 'undefined' && DATA.latitude && typeof DATA.longitude != 'undefined' && DATA.longitude) {
               user_id = DATA.user_id;
               legal_entity_id = DATA.legal_entity_id;
               latitude = DATA.latitude;
               longitude = DATA.longitude;
               usermodel.updateGeo(user_id, legal_entity_id, latitude, longitude).then((geo) => {
                    if (geo != null && geo >= 1) {
                         //res.status(200).json({ status: "success", message: "Geo location updated successfully", data: geo })
                    }
                    else {
                    }
               }).catch((err) => {
                    console.log("err ========>", err.message)
               })
          }
          else {
          }

          if (DATA.address_1 != null) {
          } else {
          }
          let address_2 = DATA.address_2 ? DATA.address_2 : '';
          let locality = DATA.locality ? DATA.locality : '';
          let landmark = DATA.landmark ? DATA.landmark : '';
          let gstin = DATA.gstin ? DATA.gstin : '';
          let arn_number = DATA.arn_number != null ? DATA.arn_number : '';

          if (typeof DATA.gstin != 'undefined' && DATA.gstin) {
               let gstin_no = usermodel.getGstinNo(DATA.gstin);
               let user_gstin_no = usermodel.getUserGstinNo(DATA.customer_token);
               if (gstin_no == 0 || DATA.gstin == user_gstin_no) {
                    gstin = DATA.gstin
               } else {
                    res.status(200).json({ status: "failed", message: "Gstin Already Exist" })
               }

          }
          if (typeof DATA.arn_number != 'undefined' && DATA.arn_number) {
               let arn_no = usermodel.getArnNo(DATA.arn_number);
               let user_arn_no = usermodel.getUserArnNo(DATA.customer_token);
               if (arn_no == 0 || user_arn_no == DATA.arn_number) {
                    arn_number = DATA.arn_number;
               } else {
                    res.status(200).json({ status: "failed", message: "Arn number Already Exist" })
               }
          }

          //contact details
          if (typeof DATA.contact_no1 != 'undefined' && DATA.contact_no1 && typeof DATA.contact_name1 != 'undefined' && DATA.contact_name1 || typeof DATA.contact_no2 != 'undefined' && DATA.contact_no2 && typeof DATA.contact_name2 != 'undefined' && DATA.contact_name2 && typeof DATA.user_id2 != 'undefined' && DATA.user_id2) {
               let contact_no1;
               let contact_name1;
               let user_id1;
               let contact_no2;
               let contact_name2;
               let user_id2;
               if (typeof DATA.contact_no2 == 'undefined') {
                    contact_no2 = '';
                    contact_name2 = '';
                    user_id2 = '';
               }
               if (typeof DATA.contact_no1 == 'undefined') {
                    contact_no1 = '';
                    contact_name1 = '';
                    user_id1 = '';
               }

               if (typeof DATA.user_id1 != 'undefined' && DATA.user_id1 && typeof DATA.contact_name1 != 'undefined' && DATA.contact_name1) {
                    let checkTelephone = usermodel.allTelephone(DATA.contact_no1, DATA.customer_token);
                    let mobile_no = usermodel.getMobile(DATA.user_id1);
                    if (checkTelephone == 0 || (DATA.contact_no1 == mobile_no)) {
                         contact_no1 = DATA.contact_no1;
                         contact_name1 = DATA.contact_name1;
                         user_id1;
                         if (DATA.user_id1) {
                              user_id1 = DATA.user_id1;
                         }
                    } else {
                    }
               }
               if (typeof DATA.user_id2 != 'undefined' && DATA.user_id2 && typeof DATA.contact_no2 != 'undefined' && typeof DATA.contact_name2 != 'undefined') {
                    let checkTelephone = usermodel.allTelephone(DATA.contact_no2, DATA.customer_token);
                    let mobile_no = usermodel.getMobile(DATA.user_id2);
                    if (checkTelephone == 0 || DATA.contact_no2 == mobile_no) {
                         let contact_no2 = DATA.contact_no2;
                         let contact_name2 = DATA.$contact_name2;
                         if (DATA.user_id2) {
                         }

                         if (DATA.user_id2 != null) {
                              $customerData = usermodel.updateCustomerContact(user_id2, contact_no2, contact_name2);
                         }
                    } else {
                         res.status(200).json({ status: "failed", message: 'Please enter valid user details' })
                    }


               }

               if (typeof DATA.contact_no2 != 'undefined' && typeof DATA.user_id2 != 'undefined' && DATA.user_id2) {

                    let checkTelephone = usermodel.allTelephone(DATA.contact_no2, DATA.customer_token);

                    if (checkTelephone == 0) {
                         DATA.user_id2 = usermodel.AddContact(DATA.contact_no2, DATA.contact_name2, DATA.customer_token);

                    } else if (checkTelephone != 0) {
                         res.status(200).json({ status: 'failed', message: "Mobile number already exist" })
                    }

               }


               if (typeof DATA.contact_no1 != 'undefined' && typeof DATA.user_id1 != 'undefined' && DATA.user_id1 == null) {
                    let checkTelephone = usermodel.allTelephone(DATA.contact_no1, DATA.customer_token);
                    if (checkTelephone == 0) {

                         DATA.user_id1 = usermodel.AddContact(DATA.contact_no1, DATA.contact_name1, DATA.customer_token);

                    } else {
                         res.status(200).json({ status: 'failed', message: "Mobile number already exist" })
                    }
               }
          }

          //Email duplicate validation checking
          if (DATA.email && validator.validate(DATA.email)) {
               let email = DATA.email;
               //Already exists mail id needs to skip for update
               let email_verification = usermodel.eMailcheck(DATA.customer_token);
               if (email_verification == DATA.email) {
                    CustomerData = usermodel.updateAddressData(DATA.customer_token, DATA.address_1, address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number);
                    usermodel.getCustomerData(DATA.customer_token).then((user_data) => {
                         if (CustomerData != null) {
                              res.status(200).json({ status: 'success', message: 'Updated successfully', data: user_data })
                         } else {
                              //res.status(200).json({ status: 'failed', message: 'Not updated' })

                         }
                    }).catch((err) => {
                         console.log(err)
                    })
               }
               //Email Exists check     
               let emailchk = usermodel.getEmail(email);
               if (emailchk && emailchk == null) {
                    email = DATA.email;
               } else {
               }

          } else {
               CustomerData = usermodel.updateAddressData(DATA.customer_token, DATA.address_1, DATA.address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number);
          }

          if (typeof DATA.email != 'undefined' && DATA.email != null && validator.validate(DATA.email)) {
               let email_up = DATA.email;
               if (email_up != null) {
                    usermodel.updateEmail(DATA.customer_token, email_up);
                    CustomerData = usermodel.updateAddressData(DATA.customer_token, DATA.address_1, DATA.address_2, locality, landmark, city, DATA.postcode, state, gstin, arn_number);
               }

          }


          // //Sending otp for updating new mobile number 
          if (typeof DATA.telephone != 'undefined' && typeof DATA.otp_sent == 'undefined') {
               if (DATA.telephone != null && DATA.telephone.length >= 10) {
                    usermodel.allTelephone(DATA.telephone, DATA.customer_token).then((allTelephone) => {
                         if (allTelephone) {
                              let telephone = usermodel.getTelephone(DATA.customer_token);
                              if (telephone == DATA.telephone) {
                              } else {
                                   if (allTelephone == 0) {
                                        usermodel.generateOtp(DATA.customer_token, DATA.telephone).then((response) => {
                                             if (response != null) {
                                                  let result_telephone = [];
                                                  let telephone = DATA.telephone;
                                                  let otp = response;
                                                  result_telephone.push(telephone);
                                                  result_telephone.push(otp);
                                                  //res.status(200).json({ status: "success", message: 'Telephone details', data: result_telephone })
                                             }
                                        }).catch((err) => {
                                             console.log(err);
                                        })
                                   } else {

                                   }
                              }
                         }
                    }).catch((err) => {
                         console.log(err)
                    })


               } else {

               }

          }
          //validating mobile number
          if (typeof DATA.telephone != 'undefined' && typeof DATA.otp_sent != 'undefined') {
               usermodel.getOtp(DATA.customer_token).then((otp) => {
                    if (otp != null) {
                         if (DATA.otp_sent != null && DATA.otp_sent.length == 6 && DATA.otp_sent == otp.otp) {
                              usermodel.updateTelephone(DATA.customer_token, DATA.telephone);
                              let updatedTel = [];
                              let telephone = DATA.telephone;
                              updatedTel.push(telephone);
                              res.status(200).json({ status: 'success ', message: "Telephone number successfully updated", data: updatedTel })
                         } else {
                              res.status(200).json({ status: "Failed", message: "Please enter valid otp" })
                         }

                    } else {
                    }
               }).catch(err => {
                    console.log(err);
               })
          }

          if (DATA.customer_token != null && typeof DATA.otp_sent == 'undefined') {
               if (typeof DATA.contact_no1 != 'undefined' && DATA.contact_no1 != null && typeof DATA.contact_name1 != 'undefined' && DATA.contact_name1 != null && typeof DATA.user_id1 != 'undefined' && DATA.user_id1 != null || typeof DATA.contact_no2 != 'undefined' && DATA.contact_no2 != null && typeof DATA.contact_name2 != 'undefined' && DATA.contact_name2 != null && typeof DATA.user_id2 != 'undefined' && DATA.user_id2 != null) {
                    if (typeof DATA.contact_no1 != 'undefined' && typeof DATA.contact_name1 != 'undefined' && typeof DATA.contact_name2 != 'undefined' && typeof DATA.contact_no2 != 'undefined') {
                         usermodel.allTelephone_merge(DATA.contact_no1, DATA.contact_no2, DATA.customer_token).then((checkTelephone) => {
                              if (checkTelephone == 0) {
                                   customerData = usermodel.updateCustomerContact(DATA.user_id1, DATA.contact_no1, DATA.contact_name1, DATA.contact_name2);
                                   res.status(200).json({ status: 'success', message: 'Contact Updated', data: customerData })
                              } else {
                                   res.status(200).json({ status: 'failed', message: 'Mobile number already exists' })
                              }
                         }).catch((err) => {
                              console.log(err);
                         });

                    }
               } else {
                    //res.status(200).json({ status: 'failed', message: 'Please enter the valid value' })
               }
               usermodel.getCustomerData(DATA.customer_token).then((response) => {
                    if (response != null) {
                         res.status(200).json({ status: 'success', message: 'User Details', data: response })
                    } else {
                         res.status(200).json({ status: 'Failed', message: 'No data Found', })
                    }

               }).catch((err) => {
                    console.log(err)
               })
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ status: 'failed', message: 'Unable to process your request ! Please try later' })
     }
}





function uploadToS3(file) {
     return new Promise((resolve, reject) => {
          let s3bucket = new aws.S3({
               accessKeyId: IAM_USER_KEY,
               secretAccessKey: IAM_USER_SECRET,
               Bucket: BUCKET_NAME
          });
          var params = {
               Bucket: BUCKET_NAME,
               Key: file[0].originalname,
               Body: file[0].buffer
          };
          s3bucket.upload(params, function (err, data) {
               if (err) {
                    console.log('error in callback');
                    console.log(err);
                    reject(err);
               }
               console.log('success');
               console.log(data);
               resolve(data.Location)
          });
     })

}

//File upload to S3 loaction 
/*
 *   For: prescription and report upload
 *   Author: Deepak Tiwari
 *   Request params parameters: filepath
 *   Returns:file location where we had uploaded a file in S3
 */
module.exports.fileUpload = function (req, res) {
     try {
          console.log("req", req.files.img);
          if (typeof req.files.img[0].buffer == 'undefined') {
               var singleupload = upload.single('img');
               singleupload(req, res, function (err, data) {
                    if (err) {
                         console.log(err)
                         res.status(200).json({ success: false, message: "Unable to upload " })
                    } else {
                         res.status(200).json({ success: true, message: "Uploaded Successfully ", data: req.files.img[0].location })
                    }
               });

          } else {
               uploadToS3(req.files.img).then(response => {
                    res.status(200).json({ status: 'success', message: "Uploaded Successfully ", data: response })
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ status: 'failed', message: "Something went wrong " })
               })
               console.log("url", req.files.img[0].buffer);
               console.log("origin", req.files.img[0].originalname)
          }
     } catch (err) {
          console.log(err)
          res.status(500).json({ success: false, message: "Unable To Process Your Request , Please Try Later" })
     }
}

/*
  * Function Name: UpdateGeo()
  * Description: Used to updatedgeo
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 5 Jan 2017
  * Modified Date & Reason:
  */
module.exports.UpdateGeo = function (req, res) {
     try {
          let user_id, legal_entity_id, latitude, longitude;
          let data = JSON.parse(req.body.data)
          if (typeof data.user_id != 'undefined' && data.user_id && typeof data.legal_entity_id != 'undefined' && data.legal_entity_id && typeof data.latitude != 'undefined' && data.latitude && typeof data.longitude != 'undefined' && data.longitude) {
               user_id = data.user_id;
               legal_entity_id = data.legal_entity_id;
               latitude = data.latitude;
               longitude = data.longitude;
          }
          else {
               res.status(200).json({ status: "failed", message: "Please send userid" })
          }
          usermodel.updateGeo(user_id, legal_entity_id, latitude, longitude).then((geo) => {
               if (geo != null && geo >= 1) {
                    res.status(200).json({ status: "success", message: "Geo location updated successfully", data: geo })
               }
               else {
                    res.status(200).json({ status: "failed", message: "No data" })
               }
          }).catch((err) => {
               console.log("err ========>", err)
          })

     } catch (err) {
          console.log(err)
          res.status(200).json({ status: "failed", message: "Internal server error" })
     }

}


module.exports.getFFBeatByPincode = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let Data = JSON.parse(req.body.data)
               let hub_list;
               let team;
               let pincode;
               let ffLegalEntity;
               let pincodeLegalEntity;
               if (typeof Data.sales_token != 'undefined' && Data.sales_token != '') {
                    usermodel.checkCustomerToken(Data.sales_token).then(checkSalesToken => {
                         if (checkSalesToken > 0) {
                              if (typeof Data.ff_id != 'undefined' && Data.ff_id != '') {
                                   usermodel.checkPermissionByFeatureCode("GLB0001", Data.ff_id).then(globalAccess => {
                                        if (!globalAccess) {
                                             hub_list = (typeof Data.hub != 'undefined' && Data.hub != '') ? Data.hub : '';
                                             usermodel.getTeamByUser(Data.ff_id).then(teams => {
                                                  // console.log('tems =====>839', teams)
                                                  team = teams
                                                  pincode = Data.pincode;
                                                  usermodel.getffLegalentity(Data.ff_id).then(ffLegalEntity => {
                                                       usermodel.getPincodeLegalentity(pincode).then(pincodeLegalEntity => {
                                                            if (ffLegalEntity != 0 && pincodeLegalEntity != 0) {
                                                                 if (ffLegalEntity == pincodeLegalEntity) {
                                                                      usermodel.getFfBeatByPincodewise(team, hub_list, pincode).then(data => {
                                                                           if (data != '') {
                                                                                res.status(200).json({ status: "success", message: "getFfBeat", data: data[0] })
                                                                           }
                                                                           else {
                                                                                res.status(200).json({ status: "failed", message: "Something went wrong.Plz contact support on - 04066006442." })
                                                                           }
                                                                      })
                                                                 } else {
                                                                      res.status(200).json({ status: "failed", message: 'Incorrect location mapping with retailer. Plz contact support on - 04066006442.' })
                                                                 }
                                                            } else if (ffLegalEntity == 0 || pincodeLegalEntity == 0) {
                                                                 if (ffLegalEntity == 0) {
                                                                      res.status(200).json({ status: "failed", message: 'Something went wrong.Plz contact support on - 04066006442.' })
                                                                 }
                                                                 else if (pincodeLegalEntity == 0) {
                                                                      res.status(200).json({ status: "failed", message: 'Incorrect location mapping with retailer. Plz contact support on - 04066006442.' })

                                                                 } else {
                                                                      res.status(200).json({ status: "failed", message: 'Incorrect location mapping with retailer. Plz contact support on - 04066006442.' })

                                                                 }
                                                            }
                                                       })
                                                  })
                                             }).catch(err => {
                                                  console.log(err)
                                             })
                                        } else {
                                             usermodel.getBeatsForGlobalAccess(Data.pincode).then(beatsForGlobalAccess => {
                                                  if (beatsForGlobalAccess != null) {
                                                       res.status(200).json({ status: "success", message: 'getFfBeat', 'data': beatsForGlobalAccess })
                                                  } else {
                                                       res.status(200).json({ status: "success", message: 'Not beat available ', })
                                                  }
                                             }).catch(err => {
                                                  console.log(err)
                                             })
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })
                              }
                         } else {
                              res.status(200).json({ 'status': "session", "message ": 'Your Session Has Expired. Please Login Again.' })
                         }
                    }).catch(err => {
                         console.log(err)
                         res.status(200).json({ 'status': "failed", "message ": "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001." })
                    })
               } else {
                    res.status(200).json({ 'status': "failed", "message ": 'Please provide authorized token Error_Code : 3002.' })
               }
          } else {
               res.status(200).json({ 'status': "failed", "message ": "Required parameters missing.Plz contact support on - 04066006442 Error_Code : 7002." })
          }
     } catch (err) {
          console.log(err)
          res.status(200).json({ status: "failed", message: "Internal server error Error_Code : 5000." })
     }
}



module.exports.getffmaps = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let Data = JSON.parse(req.body.data)
               usermodel.getFfDynamicData(Data).then(data => {
                    if (data != '') {
                         res.status(200).json(data)
                    } else {
                         res.status(200).json({ 'status': 'failed', 'message': "Unable to process your request.Plz contact support on - 04066006442 Error_Code : 7003" })
                    }
               }).catch(err => {
                    console.log(err)
               })

          } else {
               res.status(200).json({ 'status': 'failed', 'message': "Required parameters missing.Plz contact support on - 04066006442 Error_Code : 7002." })
          }
     } catch (err) {
          console.log(err)
          res.status(200).json({ status: "failed", message: "Internal server error Error_Code : 5000." })
     }

}


module.exports.getFFPincodeList = function (req, res) {
     try {
          // console.log(req.body.data)
          let data = JSON.parse(req.body.data)
          usermodel.getFFPincode(data.user_id).then(pincodeList => {
               if (pincodeList != '') {
                    res.status(200).json({ 'status': 'success', 'message': "Available pincode ", 'data': pincodeList })
               } else {
                    res.status(500).json({ 'status': 'failed', 'message': "Unable to process your request.Plz contact support on - 04066006442 Error_Code : 7003" })
               }
          })

     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': 'failed', 'message': "Internal server error Error_Code : 5000." })
     }



}


module.exports.getffPincodeList = function (req, res) {
     try {
          let data = JSON.parse(req.body.data);
          usermodel.getUserIdByCustomerToken(data.ff_token).then(user_id => {
               // console.log("user_id", user_id)
               usermodel.getFFPin(user_id).then(response => {
                    res.status(200).json({ 'status': 'success', 'message': 'success', 'data': response })
               }).catch(err => {
                    console.log(err)
               })
          }).catch(err => {
               console.log(err)
          })

     } catch (err) {
          console.log(err)
     }
}