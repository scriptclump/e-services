const registrationModel = require('../../registrationsModule/model/registrationApiModel');
const validator = require("email-validator");
const upload = require('../../../config/s3Config');
const cpMessage = require('../../../config/cpMessage');
const encryption = require('../../../config/encryption.js');

//Function is used to generate random string
function randStrGen(len) {
     let result = "";
     let chars = "abcdefghijklmnopqrstuvwxyz0123456789";
     let charArray = chars.split();
     let i;
     for (i = 0; i < len; i++) {
          let randItem = Math.floor(Math.random() * chars.length);
          result = "" + charArray[randItem];
     }
     return result;
}

/*
Purpose : Used to get register new customer
author : Deepak Tiwari
Request : Require user details and flag
Resposne : Return message  that user registered successfully
*/

module.exports.registration = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data.length > 0) {
               let Data = JSON.parse(req.body.data);
               let telephone;
               let sales_token;
               let buyer_type_id;
               let customer_token;
               let data = {};
               let finalArray;
               //Registration 
               if (Data.flag == 1) {
                    if (typeof Data.telephone != 'undefined' && Data.telephone) {
                         if (Data.telephone.length >= 10) {
                              telephone = Data.telephone;
                         } else {
                              res.status(200).json({ status: "failed", message: "Please send valid mobile number" });
                         }
                    } else {
                         res.status(200).json({ status: "failed", message: "Pass mobile number" })
                    }

                    if (typeof Data.sales_token != 'undefined' && Data.sales_token) {
                         sales_token = Data.sales_token;
                    } else {
                         sales_token = '';
                    }

                    if (typeof Data.business_type_id != 'undefined' && Data.business_type_id) {
                         buyer_type_id = Data.business_type_id;
                    } else {
                         buyer_type_id = '';

                    }
                    registrationModel.registration(telephone, buyer_type_id, sales_token).then((result) => {
                         if (result.status == 1) {
                              res.status(200).json({ status: "success", message: "You have successfully registered ", data: result })
                         } else {
                              res.status(200).json({ status: "failed", message: result.message })
                         }
                    }).catch(err => {
                         console.log(err)
                    })

               }
               //OPT confirmation
               if (Data.flag == 2) {
                    console.log("75")
                    let device_id = (typeof Data.device_id != 'undefined' && Data.device_id != '') ? Data.device_id : '';
                    let ip_address = (typeof Data.ip_address != 'undefined' && Data.ip_address != '') ? Data.ip_address : 0;
                    let reg_id = (typeof Data.reg_id != 'undefined' && Data.reg_id != '') ? Data.reg_id : 0;
                    let platform_id = (typeof Data.platform_id != 'undefined' && Data.platform_id != '') ? Data.platform_id : 0;
                    let module_id = (typeof Data.module_id != 'undefined' && Data.module_id != '') ? Data.module_id : 0;
                    usermodel.confirmOtp(Data.telephone, Data.otp, device_id, ip_address, reg_id, platform_id, module_id).then((confirmation) => {
                         if (confirmation.length > 0) {
                              res.status(200).json(confirmation)
                         } else if (confirmation.length == 0) {
                              res.status(200).json({ status: "Failed", message: "Entered wrong otp" })
                         } else {
                              res.status(200).json(confirmation)
                         }
                    })
               }

               //Resend OTP
               if (Data.flag == 3) {
                    if (typeof Data.telephone != 'undefined') {
                         if (Data.telephone != null && Data.telephone.length >= 10) {
                              telephone = Data.telephone;
                         } else {
                              res.status(200).json({ status: 'failed', message: "Please send valid mobile number" })
                         }
                    } else {
                         res.status(200).json({ status: 'failed', message: "Pass mobile number" })
                    }

                    let custflag;
                    if (typeof Data.customer_token != 'undefined') {
                         customer_token = Data.customer_token;
                         custflag = 2;

                    } else {
                         customer_token = '';
                         custflag = '';

                    }
                    registrationModel.resendOtp(telephone, customer_token, custflag).then((result) => {
                         if (result) {
                              if (result.status == 1) {
                                   res.status(500).json({ status: "success", message: "Otp sent successfully" })
                              } else {
                                   res.status(500).json({ status: "failed", message: result.message })
                              }

                         }
                    }).catch((err) => {
                         console.log(err);
                         res.status(500).json({ status: "failed", message: "Internal Server Error" })
                    })

               }
               //Inserting Address detail into  the database
               if (Data.flag == 4) {
                    //Validations 
                    if (Data.pincode == '' && Data.city == '' && Data.firstname == '' && Data.business_legal_name == '' && Data.address1 == '' && Data.segment_id == '' && Data.address2 == '') {
                         data = { message: "Please enter mandatory Fields", status: "Failed" }
                         console.log(data)
                    }
                    let mobile_no;
                    let firstname;
                    let email_id;
                    let city;
                    let pincode;
                    let business_legal_name;
                    let address1;
                    let address2;
                    let locality;
                    let landmark;
                    let contact_no1;
                    let contact_no2
                    let contact_name1;
                    let contact_name2;
                    let tin_number;
                    let customer_type;
                    let volume_class;
                    let noof_shutters;
                    let license_type;
                    let latitude;
                    let longitude;
                    let pref_value;
                    let pref_value1;
                    let bstart_time;
                    let state_id;
                    let area;
                    let smartphone;
                    let master_manf = [];
                    let network;
                    let lastname;
                    let device_id;
                    let ip_address;
                    let download_token;
                    let doc_file_path = [];
                    let profile_picture = [];
                    //Upload document to aws s3
                    if (typeof req.files.doc_url != 'undefined') {
                         var singleupload = upload.single('doc_url');
                         singleupload(req, res, function (err, data) {
                              if (err) {
                                   console.log(err);
                              } else {
                                   doc_file_path.push(req.files.doc_url[0].location);
                              }
                         });
                    } else {
                         doc_file_path = [];
                    }

                    //upload profile picture to aws s3
                    if (typeof req.files.profile_picture != 'undefined') {
                         var singleupload = upload.single('profile_picture');
                         singleupload(req, res, function (err, data) {
                              if (err) {
                                   console.log(err);
                              } else {
                                   profile_picture.push(req.files.profile_picture[0].location);
                              }
                         });
                    } else {
                         profile_picture = [];
                    }


                    //telephone
                    if (typeof Data.telephone != 'undefined') {
                         if (Data.telephone && Data.telephone.length >= 10) {
                              mobile_no = Data.telephone;
                         } else {
                              data = { message: "Please send valid mobile number", status: "Failed" }
                              console.log(data);
                         }

                    } else {
                         data = { message: "Enter telephone", status: "Failed" }
                         console.log(data);
                    }

                    // First name
                    if (typeof Data.firstname != 'undefined') {
                         if (Data.firstname == '' || Data.firstname.length < 4 || Data.firstname.length > 32) {
                              data = { message: "Please enter firstname between 4 to 32 characters", status: "Failed" }
                              console.log(data);
                         } else {
                              firstname = Data.firstname;
                         }
                    } else {

                    }

                    //City name       
                    if (typeof Data.city != 'undefined') {
                         if (Data.city == '' || Data.city.length < 4 || Data.city.length > 32) {
                              data = { message: "Please enter city between 4 to 32 characters", status: "Failed" }
                              console.log(data);
                         } else {
                              city = Data.city;
                         }

                    } else {
                         data = { message: "Please enter city", status: "Failed" }
                         console.log(data);

                    }

                    /// pincode = Data.pincode;
                    if (typeof Data.business_legal_name != 'undefined') {
                         if (Data.business_legal_name == '' || Data.business_legal_name.length < 4 || Data.business_legal_name.length > 32) {
                              data = { message: "Please enter business_legal_name between 4 to 32 characters", status: "Failed" }
                              console.log(data);
                         } else {

                              business_legal_name = Data.business_legal_name;
                         }

                    } else {
                         data = { message: "Please enter shopname", status: "Failed" }
                         console.log(data);
                    }

                    if (typeof Data.address1 != 'undefined') {
                         if (Data.address == '') {
                              data = { message: "Please enter address", status: "Failed" }
                              console.log(data);
                         } else {
                              address1 = Data.address1;
                         }

                    } else {
                         data = { message: "Please enter address1", status: "Failed" }
                         console.log(data);
                    }

                    if (typeof Data.address2 != 'undefined') {
                         address2 = Data.address2;
                    } else {
                         address2 = '';

                    }

                    if (typeof Data.locality != 'undefined') {
                         locality = Data.locality;
                    } else {
                         locality = '';
                    }

                    if (typeof Data.landmark != 'undefined') {
                         landmark = Data.landmark;
                    } else {
                         landmark = '';
                    }

                    //contactno_1
                    if (typeof Data.contact_no1 != 'undefined' && Data.contact_no1 != "") {
                         registrationModel.checkUser(Data.contact_no1).then((result_no1) => {
                              if (result_no1 >= 1) {
                                   data = { message: "ContactNumber " + Data.contact_no1 + "already exists", status: "Failed" }
                                   console.log(data);
                              } else {
                                   contact_no1 = Data.contact_no1;
                              }
                         }).catch((err) => {
                              console.log(err)
                         })
                    } else {
                         contact_no1 = '';
                    }

                    //contactno_2
                    if (typeof Data.contact_no2 != 'undefined' && Data.contact_no2 != "") {
                         registrationModel.checkUser(Data.contact_no2).then((result) => {
                              if (result > 0) {
                                   data = { message: "ContactNumber " + Data.contact_no2 + "already exists", status: "Failed" }
                                   console.log(data);
                              } else {
                                   contact_no2 = Data.contact_no2;
                              }
                         }).catch(err => {
                              console.log(err)
                         })

                    } else {
                         contact_no2 = '';
                    }

                    //contact_name1 
                    if (typeof Data.contact_name1 != 'undefined' && Data.contact_name1 != "") {
                         contact_name1 = Data.contact_name1;
                    } else {

                         contact_name1 = '';

                    }

                    //contact_name2 
                    if (typeof Data.contact_name2 != 'undefined' && Data.contact_name2 != "") {
                         contact_name2 = Data.contact_name2;
                    } else {
                         contact_name2 = '';
                    }

                    //Segment id
                    if (typeof Data.segment_id != 'undefined') {
                         if (Data.segment_id == '') {
                              data = { message: "Please choose segment id", status: "Failed" }
                              console.log(data);
                         } else {
                              segment_id = Data.segment_id;
                         }
                    } else {
                         data = { message: "Please choose businesstype", status: "Failed" }
                         console.log(data);

                    }

                    //customertype
                    if (typeof Data.customer_type != 'undfined') {
                         if (Data.customer_type == '') {
                              data = { message: "Please choose customer_type", status: "Failed" }
                              console.log(data);
                         } else {
                              customer_type = Data.customer_type;
                         }
                    } else {
                         data = { message: "Please enter customer type", status: "Failed" }
                         console.log(data);
                    }

                    //Tin Number
                    if (typeof Data.tin_number != 'undefined') {
                         tin_number = Data.tin_number;
                    } else {
                         tin_number = '';

                    }

                    //volume_class
                    if (typeof Data.volume_class != 'undefined') {
                         volume_class = Data.volume_class;
                    } else {

                         volume_class = '';

                    }

                    //noof_shutters
                    if (typeof Data.noof_shutters != 'undefined') {
                         noof_shutters = Data.noof_shutters;
                    } else {
                         noof_shutters = '';
                    }

                    //noof_shutters
                    if (typeof Data.license_type != 'undefined') {
                         license_type = Data.license_type;

                    } else {
                         license_type = '';

                    }
                    //Latitude
                    if (typeof Data.latitude != 'undefined') {
                         latitude = Data.latitude;

                    } else {
                         data = { message: "Please enter latitude", status: "Failed" }
                         console.log(data);
                    }

                    //longitude
                    if (typeof Data.longitude != 'undefined') {
                         longitude = Data.longitude;
                    } else {
                         data = { message: "Please enter longitude", status: "Failed" }
                         console.log(data);
                    }

                    //pref_value
                    if (typeof Data.pref_value != 'undefined') {
                         pref_value = Data.pref_value;
                    } else {
                         pref_value = '';
                    }

                    //pref_value1
                    if (typeof Data.pref_value1 != 'undefined') {
                         pref_value1 = Data.pref_value1
                    } else {
                         pref_value1 = '';
                    }

                    //bstart
                    if (typeof Data.bstart_time != 'undefined') {
                         bstart_time = Data.bstart_time;
                    } else {
                         bstart_time = '07:00:00';
                    }

                    //bend_time
                    if (typeof Data.bend_time != 'undefined') {
                         bend_time = Data.bend_time;
                    } else {
                         bend_time = '21:00:00';
                    }

                    //state_id
                    if (typeof Data.state_id != 'undefined' && Data.state_id != "") {
                         state_id = Data.state_id;
                    } else {
                         state_id = '';
                    }

                    ///area
                    if (typeof Data.area != 'undefined' && Data.area != "") {
                         area = Data.area;
                    } else {
                         area = '';
                    }


                    //master_manf
                    if (typeof Data.master_manf != 'undefined' && Data.master_manf != "") {
                         master_manf.push(Data.master_manf)
                    } else {
                         registrationModel.getMasterLookupValues(106).then((master_data) => {
                              if (master_data != null) {
                                   let master_manf1 = [];
                                   for (let i = 0; i < master_data.length; i++) {
                                        master_manf1[i] = master_data[i].value;
                                   }
                                   master_manf.push(master_manf1);
                              } else {

                              }
                         }).catch((err) => {
                              console.log(err)
                         })
                    }


                    //smartphone
                    if (typeof Data.smartphone != 'undefined' && Data.smartphone != "") {
                         smartphone = Data.smartphone;
                    } else {
                         smartphone = 0;
                    }

                    //network
                    if (typeof Data.network != 'undefined' && Data.network != "") {
                         network = Data.network;
                    } else {
                         network = 0;
                    }

                    //lastname
                    if (typeof Data.lastname != 'undefined' && Data.lastname != "") {
                         lastname = Data.lastname;
                    } else {
                         lastname = '';
                    }

                    let beat_id = (typeof Data.beat_id != 'undefined' && Data.beat_id != "") ? Data.beat_id : 0;
                    let gstin = (typeof Data.gstin != 'undefined' && Data.gstin != "") ? Data.gstin : '';
                    let arn_number = (typeof Data.arn_number != 'undefined' && Data.arn_number != "") ? Data.arn_number : '';


                    //Device Id
                    if (typeof Data.device_id != 'undefined') {
                         device_id = Data.device_id;
                         if (device_id.length != 0) {
                              device_id = Data.device_id;
                         }
                         else {
                              data = { message: 'Please send valid device Id', status: "Failed" }
                              console.log(data);
                         }
                    }
                    else {
                         data = { message: 'Please send device Id', status: "Failed" }
                         console.log(data);
                    }

                    //Valiadtion ip address
                    if (typeof Data.ip_address != 'undefined' && Data.ip_address != '') {
                         if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(Data.ip_address) === false) {
                              data = { message: 'Ip Invalid', status: "Failed" }
                              console.log(data);
                         } else {
                              ip_address = Data.ip_address;
                              registrationModel.checkDeviceId(device_id).then((checkDeviceId) => {
                                   if (checkDeviceId.length == 0) {
                                        download_token = randStrGen(10);
                                   } else {
                                        download_token = checkDeviceId[0].appId;
                                   }

                              })
                         }
                    } else {
                         data = { message: 'IP address not set', status: "Failed" }
                         console.log(data);
                    }

                    let request = { parameter: Data, apiUrl: 'registration' };
                    registrationModel.logApiRequests(request);
                    //Model 
                    let facilities = Data.facilities ? Data.facilities : 0;
                    let is_icecream = Data.is_icecream ? Data.is_icecream : 0;
                    let sms_notification = Data.sms_notification ? Data.sms_notification : 0;
                    let is_milk = Data.is_milk ? Data.is_milk : 0;
                    let is_fridge = Data.is_fridge ? Data.is_fridge : 0;
                    let is_vegetables = Data.is_vegetables ? Data.is_vegetables : 0;
                    let is_visicooler = Data.is_visicooler ? Data.is_visicooler : 0;
                    let dist_not_serv = Data.dist_not_serv ? Data.dist_not_serv : '';
                    let is_deepfreezer = Data.is_deepfreezer ? Data.is_deepfreezer : 0;
                    let is_swipe = Data.is_swipe ? Data.is_swipe : 0;

                    //validating customer token
                    if (typeof Data.sales_token != 'undefined' && Data.sales_token != "") {
                         registrationModel.checkCustomerToken(Data.sales_token).then((checkSalesToken) => {
                              if (checkSalesToken > 0) {
                                   sales_token = Data.sales_token;
                                   //Pincode 
                                   if (typeof Data.pincode != 'undefined') {
                                        if (Data.pincode == '' || Data.pincode.length < 4 || Data.pincode.length > 32) {
                                             data = { message: "Please enter pincode 6 digit number", status: "Failed" }
                                             console.log(data)
                                        } else {
                                             registrationModel.checkPincode(Data.pincode).then((chk_pincode) => {
                                                  if (chk_pincode) {
                                                       if (typeof chk_pincode[0].COUNT != 'undefined' && chk_pincode[0].COUNT == 0) {
                                                            data = { message: "Please pass valid pincode", status: "Failed" }
                                                            console.log(data);
                                                       } else {
                                                            pincode = Data.pincode;
                                                            //EmailId
                                                            if (typeof Data.email_id != 'undefined' && Data.email_id != "") {
                                                                 if (Data.email_id == '' || Data.email_id.length > 96 || validator.validate(Data.email_id) == false) {
                                                                      data = { message: "Please enter email in proper format", status: "Failed" }
                                                                      console.log(data);
                                                                 } else {
                                                                      registrationModel.getEmail(Data.email_id).then((emailchk) => {
                                                                           if (typeof emailchk != 'undefined' && emailchk == 0) {
                                                                                email_id = Data.email_id;
                                                                                registrationModel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                                                     bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                                                     sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe).then((final_result) => {
                                                                                          finalArray = final_result;
                                                                                          if (final_result.status == 1) {
                                                                                               if (typeof final_result.customer_id != 'undefined') {
                                                                                                    registrationModel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
                                                                                                         if (salesTargetFeature == 1) {
                                                                                                              final_result = Object.assign(final_result, {
                                                                                                                   'sales_target': 1
                                                                                                              })
                                                                                                              res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                                         } else {
                                                                                                              final_result = Object.assign(final_result, {
                                                                                                                   'sales_target': 0
                                                                                                              })
                                                                                                              res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                                         }
                                                                                                    }).catch(err => {
                                                                                                         console.log(err);
                                                                                                    })
                                                                                               } else {
                                                                                                    final_result = Object.assign(final_result, {
                                                                                                         'sales_target': 0
                                                                                                    })
                                                                                                    res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                               }
                                                                                          } else {
                                                                                               res.status(200).json({ status: 'Failed', message: "Unable to register right now Please try later!", data: finalArray })
                                                                                          }

                                                                                     }).catch(err => {
                                                                                          console.log(err)
                                                                                     })

                                                                           } else if (emailchk > 0) {
                                                                                let data_8 = { status: "failed", message: "Email already exist" }
                                                                                res.json(data_8)
                                                                           }
                                                                      }).catch((err) => {
                                                                           console.log(err)
                                                                      })
                                                                 }
                                                            } else {
                                                                 email_id = "";
                                                                 registrationModel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                                      bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                                      sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer).then((final_result) => {
                                                                           finalArray = final_result;
                                                                           if (final_result.status == 1) {
                                                                                if (typeof final_result.customer_id != 'undefined') {
                                                                                     registrationModel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
                                                                                          if (salesTargetFeature == 1) {
                                                                                               final_result = Object.assign(final_result, {
                                                                                                    'sales_target': 1
                                                                                               })
                                                                                               res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                          } else {
                                                                                               final_result = Object.assign(final_result, {
                                                                                                    'sales_target': 0
                                                                                               })
                                                                                               res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                          }
                                                                                     }).catch(err => {
                                                                                          console.log(err);
                                                                                     })
                                                                                } else {
                                                                                     final_result = Object.assign(final_result, {
                                                                                          'sales_target': 0
                                                                                     })
                                                                                     res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                }

                                                                           } else {
                                                                                res.status(200).json({ status: 'Failed', message: "Unable to register right now Please try later!", data: finalArray })
                                                                           }
                                                                      }).catch(err => {
                                                                           console.log(err);
                                                                      })
                                                            }
                                                       }
                                                  }

                                             }).catch((err) => {
                                                  console.log(err)
                                             })
                                        }
                                   } else {
                                        data = { message: "Please enter pincode", status: "Failed" }
                                        res.status(200).json(data)
                                   }

                              } else {
                                   res.status(200).json({ status: "session", message: 'You have already logged into the Ebutor System' })
                              }
                         })
                    } else {
                         //self registration
                         sales_token = "";
                         //Pincode 
                         if (typeof Data.pincode != 'undefined') {
                              if (Data.pincode == '' || Data.pincode.length < 4 || Data.pincode.length > 32) {
                                   data = { message: "Please enter pincode 6 digit number", status: "Failed" }
                                   console.log(data)
                              } else {
                                   registrationModel.checkPincode(Data.pincode).then((chk_pincode) => {
                                        if (chk_pincode) {
                                             if (typeof chk_pincode[0].COUNT != 'undefined' && chk_pincode[0].COUNT == 0) {
                                                  data = { message: "Please pass valid pincode", status: "Failed" }
                                                  console.log(data);
                                             } else {
                                                  pincode = Data.pincode;
                                                  //EmailId
                                                  if (typeof Data.email_id != 'undefined' && Data.email_id != "") {
                                                       if (Data.email_id == '' || Data.email_id.length > 96 || validator.validate(Data.email_id) == false) {
                                                            data = { message: "Please enter email in proper format", status: "Failed" }
                                                            console.log(data);
                                                       } else {
                                                            registrationModel.getEmail(Data.email_id).then((emailchk) => {
                                                                 if (typeof emailchk != 'undefined' && emailchk == 0) {
                                                                      email_id = Data.email_id;
                                                                      registrationModel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                                           bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                                           sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe).then((final_result) => {
                                                                                finalArray = final_result;
                                                                                if (final_result.status == 1) {
                                                                                     if (typeof final_result.customer_id != 'undefined') {
                                                                                          registrationModel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
                                                                                               if (salesTargetFeature == 1) {
                                                                                                    final_result = Object.assign(final_result, {
                                                                                                         'sales_target': 1
                                                                                                    })
                                                                                                    res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                               } else {
                                                                                                    final_result = Object.assign(final_result, {
                                                                                                         'sales_target': 0
                                                                                                    })
                                                                                                    res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                               }
                                                                                          }).catch(err => {
                                                                                               console.log(err);
                                                                                          })
                                                                                     } else {
                                                                                          final_result = Object.assign(final_result, {
                                                                                               'sales_target': 0
                                                                                          })
                                                                                          res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                     }

                                                                                } else {
                                                                                     res.status(200).json({ status: 'Failed', message: "Unable to register right now Please try later!", data: finalArray })
                                                                                }

                                                                           }).catch(err => {
                                                                                console.log(err)
                                                                           })

                                                                 } else if (emailchk > 0) {
                                                                      let data_8 = { status: "failed", message: "Email already exist" }
                                                                      res.json(data_8)
                                                                 }
                                                            }).catch((err) => {
                                                                 console.log(err)
                                                            })
                                                       }
                                                  } else {
                                                       //when email id not passed
                                                       email_id = "";
                                                       registrationModel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                            bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                            sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer).then((final_result) => {
                                                                 finalArray = final_result;
                                                                 if (final_result.status == 1) {
                                                                      if (typeof final_result.customer_id != 'undefined') {
                                                                           registrationModel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
                                                                                if (salesTargetFeature == 1) {
                                                                                     final_result = Object.assign(final_result, {
                                                                                          'sales_target': 1
                                                                                     })
                                                                                     res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                } else {
                                                                                     final_result = Object.assign(final_result, {
                                                                                          'sales_target': 0
                                                                                     })
                                                                                     res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                }
                                                                           }).catch(err => {
                                                                                console.log(err);
                                                                           })
                                                                      } else {
                                                                           final_result = Object.assign(final_result, {
                                                                                'sales_target': 0
                                                                           })
                                                                           res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                      }

                                                                 } else {
                                                                      res.status(200).json({ status: 'Failed', message: "Unable to register right now Please try later!", data: finalArray })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                            })
                                                  }
                                             }
                                        }

                                   }).catch((err) => {
                                        console.log(err)
                                   })
                              }
                         } else {
                              data = { message: "Please enter pincode", status: "Failed" }
                              res.status(200).json(data)
                         }
                    }
               }
          } else {
               res.status(200).json({ status: "Failed", message: "Please provide required details" })
          }

     } catch (err) {
          console.log(err)
     }



}

/*
Purpose :After Registration Process,with the inputs of device details ,Ipadress & customer Id we generate AppID
author : Deepak Tiwari
Request : Require ipaddress , customer Id , device details
Resposne : Return generated app Id for perticular customer.
*/
module.exports.generate_Appid = function (req, res) {
     let data = JSON.parse(req.body.data);
     let device_id;
     let download_token;
     let error = {};
     if (typeof data.ip_address != 'undefined' && data.ip_address != '') {
          if (typeof data.customerId != 'undefined' && data.customerId != '') {
               if (typeof data.device_id != 'undefined') {
                    if (data.device_id.length != 0) {
                         device_id = data.device_id
                    }
                    else {
                         res.status(200).json({ 'status': 'failed', 'message': "Please send valid device details" })
                    }

               }
               else {
                    res.status(200).json({ 'status': 'failed', 'message': "Please send valid device details" })
               }

               if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(Data.ip_address) === false) {
                    res.status(200).json({ 'status': 'failed', 'message': "Please enter valid ip address" })
               } else {
                    registrationModel.checkDeviceId(data.device_id).then((checkDeviceId) => {
                         if (checkDeviceId.length == 0) {
                              download_token = randStrGen(10);
                              createDownloadtoken(det, download_token).then(download_id => {
                                   console.log("-======>718 created successfully")
                              }).catch(err => {
                                   console.log(err)
                              })


                              if (download_token != '') {
                                   let Resposne = { "Status": "Success", "Message": "AppId", "Data": download_token }
                                   console.log(Resposne)
                              }
                              else {
                                   error = { message: "Failed to insert in database", status: "Failed" }
                                   console.log(error);

                              }
                         } else {
                              let appId = { appId: checkDeviceId[0].appId };
                              res.status(200).json({ 'Status': "Success", "Message": "AppId", "Data": appId })
                         }

                    })
               }
          }
          else {
               res.status(200).json({ 'status': 'failed', 'message': "customerId not set" })
          }
     }
     else {
          res.status(200).json({ 'status': 'failed', 'message': "IP address not set" })
     }
}

/*
Purpose :getAllCustomers() used To get all the customers
author : Deepak Tiwari
Request : ff_id, beat_id, is_billed, offset, offset_limit, search, flag, hub, spoke, sort
Resposne : Returns all customer details.
*/
module.exports.getAllCustomers = function (req, res) {
     try {
          let user_data;
          let beat_id;
          let is_billed;
          let offset;
          let offset_limit;
          let search;
          let flag;
          let hub;
          let spoke;
          let sort;
          let Response = {};
          let app_flag = 0;
          if (typeof req.body.data != 'undefined') {
               // let data = JSON.parse(req.body.data)
               let decryptedData = encryption.decrypt(req.body.data);
               let data = JSON.parse(decryptedData);
               let response = {};
               let encryptResult;
               if (typeof data.customer_token != 'undefined' && data.customer_token != '') {
                    registrationModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                         if (checkCustomerToken > 0) {
                              beat_id = (typeof data.beat_id != 'undefined' && data.beat_id != '') ? data.beat_id : '';
                              is_billed = (typeof data.is_billed != 'undefined' && data.is_billed != '') ? data.is_billed : '';
                              offset = (typeof data.offset != 'undefined' && data.offset != '') ? data.offset : '';
                              offset_limit = (typeof data.offset_limit != 'undefined' && data.offset_limit != '') ? data.offset_limit : '';
                              search = (typeof data.search != 'undefined' && data.search != '') ? data.search : '';
                              flag = (typeof data.flag != 'undefined' && data.flag != '') ? data.flag : '';
                              hub = (typeof data.hub != 'undefined' && data.hub != '') ? data.hub : '';
                              spoke = (typeof data.spoke != 'undefined' && data.spoke != '') ? data.spoke : '';
                              sort = (typeof data.sort && data.sort != '') ? data.sort : '';
                              registrationModel.getUserId(data.customer_token, app_flag).then(user_data => {
                                   registrationModel.getAllCustomers(user_data[0].user_id, beat_id, is_billed, offset, offset_limit, search, flag, hub, spoke, sort).then(customers => {
                                        if (customers != '') {
                                             //  res.status(200).json({ 'status': "success", "message": "All customer data", "data": customers })
                                             response = { 'status': "success", "message": "All customer data", "data": customers };
                                             encryptResult = encryption.encrypt(JSON.stringify(response));
                                             res.send(encryptResult);
                                        }
                                        else {
                                             //res.status(200).json({ 'status': "success", "message": "No Data", "data": [] })
                                             response = { 'status': "success", "message": "No Data", "data": [] };
                                             encryptResult = encryption.encrypt(JSON.stringify(response));
                                             res.send(encryptResult);
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        //res.status(200).json({ 'status': 'session', 'message': cpMessage.internalCatch })
                                        response = { 'status': 'session', 'message': cpMessage.internalCatch };
                                        encryptResult = encryption.encrypt(JSON.stringify(response));
                                        res.send(encryptResult);
                                   })

                              }).catch(err => {
                                   console.log(err);
                                   //res.status(200).json({ 'status': 'session', 'message': cpMessage.internalCatch })
                                   response = { 'status': 'session', 'message': cpMessage.internalCatch };
                                   encryptResult = encryption.encrypt(JSON.stringify(response));
                                   res.send(encryptResult);
                              })
                         } else {
                              //res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken });
                              response = { 'status': 'session', 'message': cpMessage.invalidToken };
                              encryptResult = encryption.encrypt(JSON.stringify(response));
                              res.send(encryptResult);
                         }
                    }).catch(err => {
                         console.log(err);
                         //res.status(200).json({ 'status': 'session', 'message': cpMessage.internalCatch })
                         response = { 'status': 'session', 'message': cpMessage.internalCatch };
                         encryptResult = encryption.encrypt(JSON.stringify(response));
                         res.send(encryptResult);
                    })
               } else {
                    //res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed });
                    response = { 'status': 'failed', 'message': cpMessage.tokenNotPassed };
                    encryptResult = encryption.encrypt(JSON.stringify(response));
                    res.send(encryptResult);

               }

          } else {
               //res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
               response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody };
               encryptResult = encryption.encrypt(JSON.stringify(response));
               res.send(encryptResult);
          }
     } catch (err) {
          //res.status(500).json({ "status": "failed", "message": cpMessage.serverError });
          console.log(err);
          response = { "status": "failed", "message": cpMessage.serverError };
          encryptResult = encryption.encrypt(JSON.stringify(response));
          res.send(encryptResult);
     }

}

/*
Purpose Used to get otp 
author : Deepak Tiwari
Request : telephone , customer_token  , 
Resposne : Returns all customer details.
*/
module.exports.getOtp = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               //let data = JSON.parse(req.body.data);
               let decryptedData = encryption.decrypt(req.body.data);
               // console.log("decrypted ====> 1796 Decryption", JSON.parse(decryptedData));
               let data = JSON.parse(decryptedData);
               let result_otp;
               let response = {};
               registrationModel.checkMobileNumber(data.telephone).then(otp => {
                    if (otp != '') {
                         if (typeof data.telephone != 'undefined' && data.telephone != '') {
                              if (typeof data.customer_token != 'undefined' && data.customer_token != '') {
                                   registrationModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                                        if (checkCustomerToken > 0) {
                                             registrationModel.getSalesOtp(data.customer_token).then(result => {
                                                  // console.log("result", result)
                                                  if (result != '') {
                                                       result_otp = result;
                                                       response = { 'status': "success", 'message': "autofillotp", 'data': [{ "otp_number": result_otp }] };
                                                       encryptResult = encryption.encrypt(JSON.stringify(response));
                                                       res.send(encryptResult);
                                                       //res.status(200).json({ 'status': "success", 'message': "autofillotp", 'data': [{ "otp_number": result_otp }] })
                                                  } else {
                                                       response = { 'status': "failed", 'message': "Please enter valid mobile number" };
                                                       encryptResult = encryption.encrypt(JSON.stringify(response));
                                                       res.send(encryptResult);
                                                       // res.status(200).json({ 'status': "failed", 'message': "Please enter valid mobile number" })
                                                  }
                                             }).catch(err => {
                                                  console.log(err);
                                                  response = { 'status': "failed", 'message': "Something went wrong" };
                                                  encryptResult = encryption.encrypt(JSON.stringify(response));
                                                  res.send(encryptResult);
                                             })
                                        } else {
                                             response = { 'status': "failed", 'message': "Please enter valid sales token" };
                                             encryptResult = encryption.encrypt(JSON.stringify(response));
                                             res.send(encryptResult);
                                             //res.status(200).json({ 'status': "failed", 'message': "Please enter valid sales token" })
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        response = { 'status': "failed", 'message': "Something went wrong" };
                                        encryptResult = encryption.encrypt(JSON.stringify(response));
                                        res.send(encryptResult);
                                   })
                              } else {
                                   if (otp == '') {
                                        response = { 'status': "failed", 'message': "Please provide valid mobile number" };
                                        encryptResult = encryption.encrypt(JSON.stringify(response));
                                        res.send(encryptResult);
                                        //  res.status(200).json({ 'status': "failed", 'message': "Please provide valid mobile number" })
                                   } else if (otp != '') {
                                        response = { 'status': "success", 'message': "Autofillotp", "data": [{ "otp_number": otp }] };
                                        encryptResult = encryption.encrypt(JSON.stringify(response));
                                        res.send(encryptResult);
                                        // res.status(200).json({ 'status': "success", 'message': "Autofillotp", "data": [{ "otp_number": otp }] })
                                   }

                              }
                         } else {
                              response = { 'status': "failed", 'message': "Please provide mobile number" };
                              encryptResult = encryption.encrypt(JSON.stringify(response));
                              res.send(encryptResult);
                              // res.status(200).json({ 'status': "failed", 'message': "Please provide mobile number" })
                         }

                    } else {
                         response = { 'status': "failed", 'message': "Please provide valid mobile number" };
                         encryptResult = encryption.encrypt(JSON.stringify(response));
                         res.send(encryptResult);
                         //res.status(200).json({ 'status': "failed", 'message': "Please provide valid mobile number" })
                    }
               }).catch(err => {
                    console.log(err);
                    response = { 'status': "failed", 'message': "Something went wrong" };
                    encryptResult = encryption.encrypt(JSON.stringify(response));
                    res.send(encryptResult);
               })
          } else {
               console.log("else ")
               response = { 'status': "failed", 'message': "Please pass required parameters" };
               encryptResult = encryption.encrypt(JSON.stringify(response));
               res.send(encryptResult);
               //res.status(200).json({ 'status': "failed", 'message': "Please pass required parameters" })
          }

     } catch (err) {
          console.log("ërror ", err)
          console.log("catch")
          response = { "status": "failed", "message": "Internal server error" };
          encryptResult = encryption.encrypt(JSON.stringify(response));
          res.send(encryptResult);
          // res.status(500).json(response)
     }
}

/*
Purpose Used to update retailer data 
author : Deepak Tiwari
Request : sales_token , user_id  ,segment_id 
Resposne : Returns updated retailer data.
*/
module.exports.updateRetailerData = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let user_data = [];
               let user_id;
               let segment_id;
               let volume_class;
               let noof_shutter;
               let master_manf;
               let smartphone;
               let buyer_type;
               let network;
               if (typeof data.sales_token != 'undefined' && data.sales_token) {
                    registrationModel.checkCustomerToken(data.sales_token).then(checkSalesToken => {
                         if (checkSalesToken > 0) {
                              registrationModel.getUserId(data.sales_token).then(user_data => {
                                   user_data.push(user_data)
                                   if (typeof data.user_id != 'undefined' && data.user_id != '') {
                                        user_id = data.user_id;
                                   } else {
                                        let data = { 'status': "failed", 'message': "Please provide user id" }
                                        console.log(data)
                                   }

                                   if (typeof data.segment_id != 'undefined' && data.segment_id != '') {
                                        segment_id = data.segment_id;
                                   } else {
                                        let data = { 'status': "failed", 'message': "Please provide segment id" }
                                        console.log(data)
                                   }

                                   if (typeof data.volume_class != 'undefined' && data.volume_class != '') {
                                        volume_class = data.volume_class;
                                   } else {
                                        let data = { 'status': "failed", 'message': "Please provide volume class" }
                                        console.log(data)
                                   }

                                   if (typeof data.noof_shutter != 'undefined' && data.noof_shutter != '') {
                                        noof_shutter = data.noof_shutter;
                                   } else {
                                        let data = { 'status': "failed", 'message': "Please provide noof_shutter" }
                                        console.log(data)
                                   }


                                   if (typeof data.master_manf != 'undefined' && data.master_manf != '') {
                                        master_manf = data.master_manf;
                                   } else {
                                        let data = { 'status': "failed", 'message': "Please provide  master_manf" }
                                        console.log(data)
                                   }


                                   if (typeof data.smartphone != 'undefined' && data.smartphone != '') {
                                        smartphone = data.smartphone;
                                   } else {
                                        let data = { 'status': "failed", 'message': "Please provide  smartphone" }
                                        console.log(data)
                                   }


                                   if (typeof data.network != 'undefined' && data.network != '') {
                                        network = data.network;
                                   } else {
                                        let data = { 'status': "failed", 'message': "Please provide  network" }
                                        console.log(data)
                                   }

                                   if (typeof data.buyer_type != 'undefined' && data.buyer_type != '') {
                                        buyer_type = data.buyer_type
                                   } else {
                                        let data = { 'status': "failed", 'message': "Please provide buyer_type" }
                                        console.log(data)
                                   }

                                   registrationModel.updateRetailerData(user_id, segment_id, volume_class, noof_shutter, master_manf, smartphone, network, buyer_type, user_data[0].user_id).then(update_data => {
                                        if (update_data != '') {
                                             res.status(200).json({ 'status': "success", "message": "Updated Successfully" })
                                        }
                                        else {
                                             res.status(200).json({ 'status': "failed", "message": "Unable to Update" })
                                        }

                                   }).catch(err => {
                                        console.log(err)
                                        res.status(200).json({ 'status': "failed", "message": cpMessage.internalCatch })
                                   })
                              }).catch(err => {
                                   console.log(err)
                              })

                         } else {
                              res.status(200).json({ "status": "session", "message": cpMessage.invalidToken })
                         }
                    })
               } else {
                    let data = { 'status': "failed", 'message': cpMessage.tokenNotPassed }
                    console.log(data)
               }

          } else {
               res.status(200).json({ 'status': "failed", "message": cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': "failed", "message": cpMessage.serverError })
     }

}


/*
Purpose Used To insert into ff_call_logs table
author : Deepak Tiwari
Request : sales_token , user_id  ,activity , latitude 
Resposne : Returns updated retailer data.
*/
module.exports.InsertFfComments = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data)
               let sales_token;
               let result = [];
               let user_id;
               let activity;
               let latitude;
               let longitude;

               if (typeof data.user_id != 'undefined' && data.user_id != '') {
                    user_id = data.user_id;
               }
               else {
                    let error = { "status": "failed", "message": "Please provide user_id" }
                    console.log(error)
               }

               if (typeof data.activity != 'undefined' && data.activity != '') {
                    activity = data.activity;
               }
               else {
                    let error = { "status": "failed", "message": "Please provide activity field" }
                    console.log(error)
               }

               if (typeof data.latitude != 'undefined' && data.latitude != '') {
                    latitude = data.latitude;
               }
               else {
                    latitude = '';
               }


               if (typeof data.longitude != 'undefined' && data.longitude != '') {
                    longitude = data.longitude;
               }
               else {
                    longitude = ''
               }

               if (typeof data.sales_token != 'undefined' && data.sales_token != '') {
                    registrationModel.checkCustomerToken(data.sales_token).then(checkCustomerToken => {
                         if (checkCustomerToken > 0) {
                              sales_token = data.sales_token;
                              if (typeof data.flag != 'undefined' && data.flag == 2) {
                                   registrationModel.UpdateCheckoutFfComments(sales_token, user_id, activity, latitude, longitude).then(response => {
                                        result.push(response)
                                        if (result != '') {
                                             res.status(200).json({ 'status': "success", 'message': "Added Successfully" })
                                        } else {
                                             res.status(200).json({ 'status': "failed", 'message': "Unable to process your request" })
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })
                              } else if (typeof data.flag != 'undefined' && data.flag == 1) {
                                   registrationModel.InsertNewFfComments_controller(sales_token, user_id, activity, latitude, longitude).then(response => {
                                        result.push(response)
                                        if (result != '') {
                                             res.status(200).json({ 'status': "success", 'message': "Added Successfully" })
                                        } else {
                                             res.status(200).json({ 'status': "failed", 'message': "Unable to process your request" })
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })
                              } else {
                                   registrationModel.InsertFfComments(sales_token, user_id, activity, latitude, longitude).then(response => {
                                        result.push(response)
                                        if (result != '') {
                                             res.status(200).json({ 'status': "success", 'message': "Added Successfully" })
                                        } else {
                                             res.status(200).json({ 'status': "failed", 'message': "Unable to process your request" })
                                        }
                                   }).catch(err => {
                                        console.log(err)
                                   })

                              }
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': 'You have already logged into the Ebutor System' })
                         }
                    }).catch(err => {
                         console.log(err)
                    })
               } else {
                    res.status(200).json({ 'status': 'session', 'message': ' sales token not provided' })
               }

          } else {
               res.status(200).json({ 'status': "failed", 'message': "Required parameter not pass" })
          }
     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': "failed", 'message': "Internal server error" })
     }

}



/*
Purpose Used To generate retailer token
author : Deepak Tiwari
Request : phonenumber , ff_token , latitude , longitude
Resposne : Returns generated token
*/
module.exports.generateRetailerToken = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               // let data = JSON.parse(req.body.data);
               let decryptedData = encryption.decrypt(req.body.data);
               let data = JSON.parse(decryptedData);
               let response = {};
               let encryptResult;
               let telephone;
               let latitude;
               let longitude;
               //telephone
               if (typeof data.telephone != 'undefined') {
                    if (data.telephone.length >= 10) {
                         telephone = data.telephone;
                    } else {
                         //res.status(200).json({ 'status': "failed", ' message': cpMessage.invalidTelephoneNumber });
                         response = { 'status': "failed", ' message': cpMessage.invalidTelephoneNumber };
                         encryptResult = encryption.encrypt(JSON.stringify(response));
                         res.send(encryptResult);
                    }
               } else {
                    //res.status(200).json({ 'status': 'failed', 'message': cpMessage.Emptytelephone });
                    response = { 'status': 'failed', 'message': cpMessage.Emptytelephone };
                    encryptResult = encryption.encrypt(JSON.stringify(response));
                    res.send(encryptResult);
               }
               if (typeof data.sales_token != 'undefined' && data.sales_token != '') {
                    registrationModel.validateCusomerToken(data.sales_token).then(checkCustomerToken => {
                         if (checkCustomerToken.token_status == 1) {
                              //latitude
                              if (typeof data.latitude != 'undefined' && data.latitude != '') {
                                   latitude = data.latitude;
                              } else {
                                   latitude = '';
                              }
                              //longitude
                              if (typeof data.longitude != 'undefined' && data.longitude != '') {
                                   longitude = data.longitude;
                              } else {
                                   longitude = '';
                              }

                              //generate retailer token
                              registrationModel.generateRetailerToken(telephone, data.sales_token, latitude, longitude).then(response => {
                                   if (response != '') {
                                        // res.status(200).json({ 'status': 'success', 'message': "Retailer token", 'data': response });
                                        response = { 'status': 'success', 'message': "Retailer token", 'data': response };
                                        encryptResult = encryption.encrypt(JSON.stringify(response));
                                        res.send(encryptResult);
                                   } else {
                                        // res.status(200).json({ 'status': "failed", 'message': cpMessage.GenarateRetailerToken });
                                        response = { 'status': "failed", 'message': cpMessage.GenarateRetailerToken };
                                        encryptResult = encryption.encrypt(JSON.stringify(response));
                                        res.send(encryptResult);
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   // res.status(200).json({ 'status': "failed", 'message': cpMessage.internalCatch });
                                   response = { 'status': "failed", 'message': cpMessage.internalCatch };
                                   encryptResult = encryption.encrypt(JSON.stringify(response));
                                   res.send(encryptResult);
                              })

                         } else {
                              //res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidToken });
                              response = { 'status': 'failed', 'message': cpMessage.invalidToken };
                              encryptResult = encryption.encrypt(JSON.stringify(response));
                              res.send(encryptResult);
                         }
                    }).catch(err => {
                         console.log(err);
                         response = { 'status': "failed", 'message': cpMessage.internalCatch };
                         encryptResult = encryption.encrypt(JSON.stringify(response));
                         res.send(encryptResult);
                    })
               } else {
                    // res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed });
                    response = { 'status': 'failed', 'message': cpMessage.tokenNotPassed };
                    encryptResult = encryption.encrypt(JSON.stringify(response));
                    res.send(encryptResult);
               }
          } else {
               //res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
               response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody };
               encryptResult = encryption.encrypt(JSON.stringify(response));
               res.send(encryptResult);
          }
     } catch (err) {
          console.log(err);
          // res.status(500).json({ 'status': "failed", "message": cpMessage.serverError });
          response = { 'status': "failed", "message": cpMessage.serverError };
          encryptResult = encryption.encrypt(JSON.stringify(response));
          res.send(encryptResult);
     }
}


/*
Purpose Used To update customer address
author : Deepak Tiwari
Request : 
Resposne : Returns updated  data.
*/
module.exports.address = function (req, res) {
     try {
          let Data = JSON.parse(req.body.data)
          if (Data.pincode == '' && Data.city == '' && Data.firstname == '' && Data.business_legal_name == '' && Data.address1 == '' && Data.segment_id == '' && Data.address2 == '') {
               data = { message: "Please enter mandatory Fields", status: "Failed" }
               console.log(data)
          }
          let mobile_no;
          let firstname;
          let email_id;
          let city;
          let pincode;
          let business_legal_name;
          let address1;
          let address2;
          let locality;
          let landmark;
          let contact_no1;
          let contact_no2
          let contact_name1;
          let contact_name2;
          let tin_number;
          let customer_type;
          let volume_class;
          let noof_shutters;
          let license_type;
          let latitude;
          let longitude;
          let pref_value;
          let pref_value1;
          let bstart_time;
          let state_id;
          let area;
          let smartphone;
          let master_manf = [];
          let network;
          let lastname;
          let device_id;
          let ip_address;
          let download_token;
          let doc_file_path = [];
          let profile_picture = [];
          //Upload document to aws s3
          if (typeof req.files.doc_url != 'undefined') {
               var singleupload = upload.single('doc_url');
               singleupload(req, res, function (err, data) {
                    if (err) {
                         console.log(err);
                    } else {
                         doc_file_path.push(req.files.doc_url[0].location);
                    }
               });
          } else {
               doc_file_path = [];
          }

          //upload profile picture to aws s3
          if (typeof req.files.profile_picture != 'undefined') {
               var singleupload = upload.single('profile_picture');
               singleupload(req, res, function (err, data) {
                    if (err) {
                         console.log(err);
                    } else {
                         profile_picture.push(req.files.profile_picture[0].location);
                    }
               });
          } else {
               profile_picture = [];
          }


          //telephone
          if (typeof Data.telephone != 'undefined') {
               if (Data.telephone && Data.telephone.length >= 10) {
                    mobile_no = Data.telephone;
               } else {
                    data = { message: "Please send valid mobile number", status: "Failed" }
                    console.log(data);
               }

          } else {
               data = { message: "Enter telephone", status: "Failed" }
               console.log(data);
          }

          // First name
          if (typeof Data.firstname != 'undefined') {
               if (Data.firstname == '' || Data.firstname.length < 4 || Data.firstname.length > 32) {
                    data = { message: "Please enter firstname between 4 to 32 characters", status: "Failed" }
                    console.log(data);
               } else {
                    firstname = Data.firstname;
               }
          } else {

          }

          //City name       
          if (typeof Data.city != 'undefined') {
               if (Data.city == '' || Data.city.length < 4 || Data.city.length > 32) {
                    data = { message: "Please enter city between 4 to 32 characters", status: "Failed" }
                    console.log(data);
               } else {
                    city = Data.city;
               }

          } else {
               data = { message: "Please enter city", status: "Failed" }
               console.log(data);

          }

          /// pincode = Data.pincode;
          if (typeof Data.business_legal_name != 'undefined') {
               if (Data.business_legal_name == '' || Data.business_legal_name.length < 4 || Data.business_legal_name.length > 32) {
                    data = { message: "Please enter business_legal_name between 4 to 32 characters", status: "Failed" }
                    console.log(data);
               } else {

                    business_legal_name = Data.business_legal_name;
               }

          } else {
               data = { message: "Please enter shopname", status: "Failed" }
               console.log(data);
          }

          if (typeof Data.address1 != 'undefined') {
               if (Data.address == '') {
                    data = { message: "Please enter address", status: "Failed" }
                    console.log(data);
               } else {
                    address1 = Data.address1;
               }

          } else {
               data = { message: "Please enter address1", status: "Failed" }
               console.log(data);
          }

          if (typeof Data.address2 != 'undefined') {
               address2 = Data.address2;
          } else {
               address2 = '';

          }

          if (typeof Data.locality != 'undefined') {
               locality = Data.locality;
          } else {
               locality = '';
          }

          if (typeof Data.landmark != 'undefined') {
               landmark = Data.landmark;
          } else {
               landmark = '';
          }

          //contactno_1
          if (typeof Data.contact_no1 != 'undefined' && Data.contact_no1 != "") {

               registrationModel.checkUser(Data.contact_no1).then((result_no1) => {
                    if (result_no1 >= 1) {
                         data = { message: "ContactNumber " + Data.contact_no1 + "already exists", status: "Failed" }
                         console.log(data);
                    } else {
                         contact_no1 = Data.contact_no1;
                    }
               }).catch((err) => {
                    console.log(err)
               })
          } else {
               contact_no1 = '';
          }

          //contactno_2
          if (typeof Data.contact_no2 != 'undefined' && Data.contact_no2 != "") {
               registrationModel.checkUser(Data.contact_no2).then((result) => {
                    if (result > 0) {
                         data = { message: "ContactNumber " + Data.contact_no2 + "already exists", status: "Failed" }
                         console.log(data);
                    } else {
                         contact_no2 = Data.contact_no2;
                    }
               }).catch(err => {
                    console.log(err)
               })

          } else {
               contact_no2 = '';
          }

          //contact_name1 
          if (typeof Data.contact_name1 != 'undefined' && Data.contact_name1 != "") {
               contact_name1 = Data.contact_name1;
          } else {

               contact_name1 = '';

          }

          //contact_name2 
          if (typeof Data.contact_name2 != 'undefined' && Data.contact_name2 != "") {
               contact_name2 = Data.contact_name2;
          } else {
               contact_name2 = '';
          }

          //Segment id
          if (typeof Data.segment_id != 'undefined') {
               if (Data.segment_id == '') {
                    data = { message: "Please choose segment id", status: "Failed" }
                    console.log(data);
               } else {
                    segment_id = Data.segment_id;
               }
          } else {
               data = { message: "Please choose businesstype", status: "Failed" }
               console.log(data);

          }

          //customertype
          if (typeof Data.customer_type != 'undfined') {
               if (Data.customer_type == '') {
                    data = { message: "Please choose customer_type", status: "Failed" }
                    console.log(data);
               } else {
                    customer_type = Data.customer_type;
               }
          } else {
               data = { message: "Please enter customer type", status: "Failed" }
               console.log(data);
          }

          //Tin Number
          if (typeof Data.tin_number != 'undefined') {
               tin_number = Data.tin_number;
          } else {
               tin_number = '';

          }

          //volume_class
          if (typeof Data.volume_class != 'undefined') {
               volume_class = Data.volume_class;
          } else {

               volume_class = '';

          }

          //noof_shutters
          if (typeof Data.noof_shutters != 'undefined') {
               noof_shutters = Data.noof_shutters;
          } else {
               noof_shutters = '';
          }

          //noof_shutters
          if (typeof Data.license_type != 'undefined') {
               license_type = Data.license_type;

          } else {
               license_type = '';

          }
          //Latitude
          if (typeof Data.latitude != 'undefined') {
               latitude = Data.latitude;

          } else {
               data = { message: "Please enter latitude", status: "Failed" }
               console.log(data);
          }

          //longitude
          if (typeof Data.longitude != 'undefined') {
               longitude = Data.longitude;
          } else {
               data = { message: "Please enter longitude", status: "Failed" }
               console.log(data);
          }

          //pref_value
          if (typeof Data.pref_value != 'undefined') {
               pref_value = Data.pref_value;
          } else {
               pref_value = '';
          }

          //pref_value1
          if (typeof Data.pref_value1 != 'undefined') {
               pref_value1 = Data.pref_value1
          } else {
               pref_value1 = '';
          }

          //bstart
          if (typeof Data.bstart_time != 'undefined') {
               bstart_time = Data.bstart_time;
          } else {
               bstart_time = '07:00:00';
          }

          //bend_time
          if (typeof Data.bend_time != 'undefined') {
               bend_time = Data.bend_time;
          } else {
               bend_time = '21:00:00';
          }

          //state_id
          if (typeof Data.state_id != 'undefined' && Data.state_id != "") {
               state_id = Data.state_id;
          } else {
               state_id = '';
          }

          ///area
          if (typeof Data.area != 'undefined' && Data.area != "") {
               area = Data.area;
          } else {
               area = '';
          }


          //master_manf
          if (typeof Data.master_manf != 'undefined' && Data.master_manf != "") {
               master_manf.push(Data.master_manf)
          } else {
               registrationModel.getMasterLookupValues(106).then((master_data) => {
                    if (master_data != null) {
                         let master_manf1 = [];
                         for (let i = 0; i < master_data.length; i++) {
                              master_manf1[i] = master_data[i].value;
                         }
                         master_manf.push(master_manf1);
                    } else {

                    }
               }).catch((err) => {
                    console.log(err)
               })
          }


          //smartphone
          if (typeof Data.smartphone != 'undefined' && Data.smartphone != "") {
               smartphone = Data.smartphone;
          } else {
               smartphone = '';
          }

          //network
          if (typeof Data.network != 'undefined' && Data.network != "") {
               network = Data.network;
          } else {
               network = '';
          }

          //lastname
          if (typeof Data.lastname != 'undefined' && Data.lastname != "") {
               lastname = Data.lastname;
          } else {
               lastname = '';
          }

          let beat_id = (typeof Data.beat_id != 'undefined' && Data.beat_id != "") ? Data.beat_id : 0;
          let gstin = (typeof Data.gstin != 'undefined' && Data.gstin != "") ? Data.gstin : '';
          let arn_number = (typeof Data.arn_number != 'undefined' && Data.arn_number != "") ? Data.arn_number : '';


          //Device Id
          if (typeof Data.device_id != 'undefined') {
               device_id = Data.device_id;
               if (device_id.length != 0) {
                    device_id = Data.device_id;
               }
               else {
                    data = { message: 'Please send valid device Id', status: "Failed" }
                    console.log(data);
               }
          }
          else {
               data = { message: 'Please send device Id', status: "Failed" }
               console.log(data);
          }

          //Valiadtion ip address
          if (typeof Data.ip_address != 'undefined' && Data.ip_address != '') {
               if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(Data.ip_address) === false) {
                    data = { message: 'Ip Invalid', status: "Failed" }
                    console.log(data);
               } else {
                    ip_address = Data.ip_address;
                    registrationModel.checkDeviceId(device_id).then((checkDeviceId) => {
                         if (checkDeviceId.length == 0) {
                              download_token = randStrGen(10);
                         } else {
                              download_token = checkDeviceId[0].appId;
                         }

                    })
               }
          }
          else {
               data = { message: 'IP address not set', status: "Failed" }
               console.log(data);
          }

          let request = { parameter: Data, apiUrl: 'registration' };
          registrationModel.logApiRequests(request);
          //Model 
          let facilities = Data.facilities ? Data.facilities : 0;
          let is_icecream = Data.is_icecream ? Data.is_icecream : 0;
          let sms_notification = Data.sms_notification ? Data.sms_notification : 0;
          let is_milk = Data.is_milk ? Data.is_milk : 0;
          let is_fridge = Data.is_fridge ? Data.is_fridge : 0;
          let is_vegetables = Data.is_vegetables ? Data.is_vegetables : 0;
          let is_visicooler = Data.is_visicooler ? Data.is_visicooler : 0;
          let dist_not_serv = Data.dist_not_serv ? Data.dist_not_serv : '';
          let is_deepfreezer = Data.is_deepfreezer ? Data.is_deepfreezer : 0;
          let is_swipe = Data.is_swipe ? Data.is_swipe : 0;

          //checking customertoken
          if (typeof Data.sales_token != 'undefined' && Data.sales_token != "") {
               registrationModel.checkCustomerToken(Data.sales_token).then((checkSalesToken) => {
                    console.log("checkSalesToken", checkSalesToken)
                    if (checkSalesToken > 0) {
                         sales_token = Data.sales_token;
                         //Pincode 
                         if (typeof Data.pincode != 'undefined') {
                              if (Data.pincode == '' || Data.pincode.length < 4 || Data.pincode.length > 32) {
                                   data = { message: "Please enter pincode 6 digit number", status: "Failed" }
                                   console.log(data)
                              } else {
                                   registrationModel.checkPincode(Data.pincode).then((chk_pincode) => {
                                        if (chk_pincode) {
                                             if (typeof chk_pincode[0].COUNT != 'undefined' && chk_pincode[0].COUNT == 0) {
                                                  data = { message: "Please pass valid pincode", status: "Failed" }
                                                  console.log(data);
                                             } else {
                                                  pincode = Data.pincode;
                                                  //EmailId
                                                  if (typeof Data.email_id != 'undefined' && Data.email_id != "") {
                                                       if (Data.email_id == '' || Data.email_id.length > 96 || validator.validate(Data.email_id) == false) {
                                                            data = { message: "Please enter email in proper format", status: "Failed" }
                                                            console.log(data);
                                                       } else {
                                                            registrationModel.getEmail(Data.email_id).then((emailchk) => {
                                                                 if (typeof emailchk != 'undefined' && emailchk == 0) {
                                                                      email_id = Data.email_id;
                                                                      console.log("doc", doc_file_path)
                                                                      registrationModel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                                           bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                                           sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe).then((final_result) => {
                                                                                if (final_result.status == 1) {
                                                                                     if (typeof final_result.customer_id != 'undefined') {
                                                                                          registrationModel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
                                                                                               if (salesTargetFeature == 1) {
                                                                                                    final_result.sales_target = 1;
                                                                                               } else {
                                                                                                    final_result.sales_target = 0;
                                                                                               }
                                                                                          })
                                                                                     } else {
                                                                                          final_result.sales_target = 0;
                                                                                     }
                                                                                     res.status(200).json({ status: 'success', message: "Addesss updated successfully", data: final_result })
                                                                                } else {
                                                                                     res.status(200).json({ status: 'Failed', message: "Unable to register right now Please try later!", data: final_result })
                                                                                }

                                                                           }).catch(err => {
                                                                                console.log(err)
                                                                           })

                                                                 } else if (emailchk > 0) {
                                                                      let data_8 = { status: "failed", message: "Email already exist" }
                                                                      res.json(data_8)
                                                                 }
                                                            }).catch((err) => {
                                                                 console.log(err)
                                                            })
                                                       }
                                                  } else {
                                                       email_id = "";
                                                       registrationModel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                            bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                            sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer).then((final_result) => {
                                                                 if (final_result.status == 1) {
                                                                      if (typeof final_result.customer_id != 'undefined') {
                                                                           registrationModel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
                                                                                if (salesTargetFeature == 1) {
                                                                                     final_result.sales_target = 1;
                                                                                } else {
                                                                                     final_result.sales_target = 0;
                                                                                }
                                                                           })
                                                                      } else {
                                                                           final_result.sales_target = 0;
                                                                      }
                                                                      res.status(200).json({ status: 'success', message: "Registeres successfully", data: final_result })
                                                                 } else {
                                                                      res.status(200).json({ status: 'Failed', message: "Unable to register right now Please try later!", data: final_result })
                                                                 }
                                                            })
                                                  }
                                             }
                                        }

                                   }).catch((err) => {
                                        console.log(err)
                                   })
                              }
                         } else {
                              data = { message: "Please enter pincode", status: "Failed" }
                              res.status(200).json(data)
                         }

                    } else {
                         res.status(200).json({ status: "session", message: 'You have already logged into the Ebutor System' })
                    }
               })

          } else {
               sales_token = "";
               res.status(200).json({ status: "falied", message: "Sales Token is required" })

          }


     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': "failed", "message": "Unable to process your request! Please try later" })
     }
}


//auth key
module.exports.checkAuthentication = function (auth_token) {
     try {
          if (auth_token == 'E446F5E53AD8835EAA4FA63511E22') {
               return true;
          } else {
               return false;
          }
     } catch (err) {
          console.log(err)
     }

}
//update
module.exports.getAllDcByuserId = function (req, res) {
     try {
          // check for the header authentication
          // console.log('=====>1109 request body', req.headers)
          let auth_token = req.headers.auth
          let data = JSON.parse(req.body.data)
          let userId = data.user_id
          if (userId == '') {
               res.status(200).json({ 'status': "failed", "message ": "Please enter user details" })
          }

          console.log("userid", userId)
          // check the feature  code and get dcs
          registrationModel.getAllDcs(userId).then(response => {
               // console.log("getallDC", response)
               if (response != null) {
                    res.status(200).json({ 'status': "success", "message ": "Data found", "data": response })
               } else {
                    res.status(200).json({ 'status': "failed", "message ": "Data not found" })
               }
          }).catch(err => {
               console.log(err)
               res.status(200).json({ 'status': "failed", "message ": "Unable to process your request.Please try later" })
          })
     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': "failed", "message ": "Internal server error" })
     }

}

module.exports.getAllStockists = function (req, res) {
     try {
          let data = JSON.parse(req.body.data)
          let userId = data.userId
          if (userId == '') {
               res.status(200).json({ 'status': "failed", "message ": "Please enter user details" })
          }
          // check the feature  code and get stockists
          registrationModel.checkPermissionByFeatureCode_controller('STDRP001', data.userId).then(checkFeatureStocklist => {
               console.log("checkFeatureStocklist", checkFeatureStocklist)
               if (checkFeatureStocklist == 1) {
                    registrationModel.getAllstockists(userId).then(response => {
                         res.status(200).json({ 'status': "success", "message ": "Data found", "code": 200, "data": response })
                    }).catch(err => {
                         console.log(err)
                         res.status(200).json({ 'status': "failed", "message ": "Unable to process your request.Please try later" })
                    })
               } else {
                    res.status(200).json({ 'status': "success", "message ": '', "code": 400 })
               }
          }).catch(err => {
               console.log(err)
               res.status(200).json({ 'status': "failed", "message ": "Unable to process your request.Please try later" })
          })
     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': "failed", "message ": "Internal server error" })
     }
}

//used to get product details of both brand and manufactures based on used_id and access_level of that  particular user
module.exports.getBrandsManufacturerProductGroupByUser = function (req, res) {
     try {
          let data = JSON.parse(req.body.data)
          let userId = data.userId
          if (userId == '') {
               res.status(200).json({ 'status': "failed", "message ": "Please enter user details" })
          }
          // check the feature  code and get stockists
          registrationModel.checkPermissionByFeatureCode_controller('STDRP001', userId).then(checkFeatureStocklist => {
               console.log("1934", checkFeatureStocklist)
               if (checkFeatureStocklist == 1) {
                    registrationModel.getBrandsManufacturerProductGroupByUser(userId).then(response => {
                         if (response != '') {
                              res.status(200).json({ 'status': "success", "message ": "Data found", "code": 200, "data": response });
                         } else {
                              res.status(200).json({ 'status': 'failed', 'message': "Data not found" });
                         }
                    }).catch(err => {
                         console.log(err)
                         res.status(200).json({ 'status': "failed", "message ": "Unable to process your request.Please try later" })
                    })
               } else {
                    //user not allowed to access this feature
                    res.status(200).json({ 'status': "success", "message": cpMessage.NotAllowedToAccessFeature, "code": 400 })
               }
          }).catch(err => {
               console.log(err)
               res.status(200).json({ 'status': "failed", "message ": "Unable to process your request.Please try later" })
          })
     } catch (err) {
          console.log(err)
          res.status(500).json({ 'status': "failed", "message ": "Internal server error" })
     }
}

/*
Purpose Used To confirm the entered by user
author : Deepak Tiwari
Request : device_id , ip_address , reg_id , platform_id , module_id , mobile_no
Resposne : Returns updated message.
*/
module.exports.confirmOtp = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data.length > 0) {
               /** Code for decryption **/
               let decryptedData = encryption.decrypt(req.body.data);
               // console.log("decrypted ====> 1796 Decryption", JSON.parse(decryptedData));
               let Data = JSON.parse(decryptedData);
               //let Data = JSON.parse(req.body.data);
               let device_id = (typeof Data.device_id != 'undefined' && Data.device_id != '') ? Data.device_id : '';
               let ip_address = (typeof Data.ip_address != 'undefined' && Data.ip_address != '') ? Data.ip_address : 0;
               let reg_id = (typeof Data.reg_id != 'undefined' && Data.reg_id != '') ? Data.reg_id : 0;
               let platform_id = (typeof Data.platform_id != 'undefined' && Data.platform_id != '') ? Data.platform_id : 0;
               let module_id = (typeof Data.module_id != 'undefined' && Data.module_id != '') ? Data.module_id : 0;
               registrationModel.confirmOtp(Data.telephone, Data.otp, device_id, ip_address, reg_id, platform_id, module_id).then((confirmation) => {
                    if (confirmation.length > 0) {
                         // res.status(200).json(confirmation)
                         result = encryption.encrypt(confirmation);
                         // console.log("======>1792 Encrypted", result);
                         res.send(result);

                    } else if (confirmation.length == 0) {
                         // res.status(200).json({ status: "failed", message: "Entered wrong otp" })
                         let response = { status: "failed", message: "Entered wrong otp" };
                         result = encryption.encrypt(JSON.stringify(response));
                         //  console.log("======>1792 Encrypted", result);
                         res.send(result);

                    } else {
                         // res.status(200).json(confirmation)
                         result = encryption.encrypt(JSON.stringify(confirmation));
                         // console.log("======>1792 Encrypted", result);
                         res.send(result);

                    }
               })

          } else {
               response = { status: "failed", message: "Please provide required details" };
               result = encryption.encrypt(JSON.stringify(response));
               //  console.log("======>1792 Encrypted", result);
               res.send(result);
               // res.status(200).json()
          }
     } catch (err) {
          console.log(err);
          response = { 'status': 'failed', 'message': 'Internal server error' };
          result = encryption.encrypt(JSON.stringify(response));
          //  console.log("======>1792 Encrypted", result);
          res.send(result);
          // res.status(200).json()
     }

}



/*
Purpose Used To send generated otp to the end user
author : Deepak Tiwari
Request : mobile_no , sales_token , business_type_id
Resposne : Returns generated otp.
*/
module.exports.sendOtp = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data.length > 0) {
               let response = {};
               let encryptResult;
               // let decryptedData = encryption.decrypt(req.body.data);
               // // console.log("decrypted ====> 1796 Decryption", JSON.parse(decryptedData));
               // let Data = JSON.parse(decryptedData);
               let Data = JSON.parse(req.body.data);
               if (typeof Data.telephone != 'undefined' && Data.telephone) {
                    if (Data.telephone.length >= 10) {
                         telephone = Data.telephone;
                    } else {
                         response = { status: "failed", message: "Please send valid mobile number" };
                         encryptResult = encryption.encrypt(JSON.stringify(response));
                         // //  console.log("======>1792 Encrypted", result);
                         // res.send(encryptResult);
                         res.status(200).json({ status: "failed", message: "Please send valid mobile number" });
                    }
               } else {
                    response = { status: "failed", message: "Please enter mobile number" };
                    // encryptResult = encryption.encrypt(JSON.stringify(response));
                    // //  console.log("======>1792 Encrypted", result);
                    // res.send(encryptResult);
                    res.status(200).json({ status: "failed", message: "Pass mobile number" })
               }

               if (typeof Data.sales_token != 'undefined' && Data.sales_token) {
                    sales_token = Data.sales_token;
               } else {
                    sales_token = '';
               }

               if (typeof Data.business_type_id != 'undefined' && Data.business_type_id) {
                    buyer_type_id = Data.business_type_id;
               } else {
                    buyer_type_id = '';

               }

               //console.log("telephone", Data.telephone, telephone)
               registrationModel.registration(telephone, buyer_type_id, sales_token).then((result) => {
                    if (result.status == 1) {
                         response = { status: "success", message: "You have successfully registered ", data: result };
                         // encryptResult = encryption.encrypt(JSON.stringify(response));
                         // //  console.log("======>1792 Encrypted", result);
                         // res.send(encryptResult);
                         res.status(200).json(response)
                    } else {
                         response = { status: "failed", message: result.message };
                         // encryptResult = encryption.encrypt(JSON.stringify(response));
                         // //  console.log("======>1792 Encrypted", result);
                         // res.send(encryptResult);
                         res.status(200).json({ status: "failed", message: result.message })

                    }
               }).catch(err => {
                    console.log(err)
               })

          } else {
               response = { status: "failed", message: "Please provide required details" };
               // encryptResult = encryption.encrypt(JSON.stringify(response));
               // //  console.log("======>1792 Encrypted", result);
               // res.send(encryptResult);
               res.status(200).json(response)
          }
     } catch (err) {
          console.log(err);
          response = { 'status': 'failed', 'message': 'Internal server error' };
          // encryptResult = encryption.encrypt(JSON.stringify(response));
          // //  console.log("======>1792 Encrypted", result);
          // res.send(encryptResult);
          res.status(200).json(response);
     }

}


/*
Purpose Used To resend the  generated otp to the end user
author : Deepak Tiwari
Request : mobile_no , token , business_type_id
Resposne : Returns generated otp.
*/
module.exports.resendOtp = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data.length > 0) {
               //let Data = JSON.parse(req.body.data);
               let response = {};
               let encryptResult;
               let decryptedData = encryption.decrypt(req.body.data);
               //console.log("decrypted ====> 1796 Decryption", JSON.parse(decryptedData));
               let Data = JSON.parse(decryptedData);
               if (typeof Data.telephone != 'undefined' && Data.telephone) {
                    if (Data.telephone.length >= 10) {
                         telephone = Data.telephone;
                    } else {
                         response = { status: "failed", message: "Please send valid mobile number" };
                         encryptResult = encryption.encrypt(JSON.stringify(response));
                         //  console.log("======>1792 Encrypted", result);
                         res.send(encryptResult);
                         // res.status(200).json({ status: "failed", message: "Please send valid mobile number" });
                    }
               } else {
                    response = { status: "failed", message: "Please enter mobile number" };
                    encryptResult = encryption.encrypt(JSON.stringify(response));
                    //  console.log("======>1792 Encrypted", result);
                    res.send(encryptResult);
                    // res.status(200).json({ status: "failed", message: "Pass mobile number" })
               }

               let custflag;
               if (typeof Data.customer_token != 'undefined') {
                    customer_token = Data.customer_token;
                    custflag = 2;

               } else {
                    customer_token = '';
                    custflag = '';

               }
               registrationModel.resendOtp(telephone, customer_token, custflag).then((result) => {
                    if (result) {
                         if (result.status == 1) {
                              //res.status(500).json()
                              response = { status: "success", message: "Otp sent successfully", data: result };
                              encryptResult = encryption.encrypt(JSON.stringify(response));
                              //  console.log("======>1792 Encrypted", result);
                              res.send(encryptResult);
                         } else {
                              response = { status: "failed", message: result.message };
                              encryptResult = encryption.encrypt(JSON.stringify(response));
                              //  console.log("======>1792 Encrypted", result);
                              res.send(encryptResult);
                              // res.status(500).json()
                         }

                    }
               }).catch((err) => {
                    console.log(err);
                    response = { status: "failed", message: "Something went wrong" };
                    encryptResult = encryption.encrypt(JSON.stringify(response));
                    //  console.log("======>1792 Encrypted", result);
                    res.send(encryptResult);
                    // res.status(500).json()
               })

          } else {
               response = { status: "failed", message: "Required parameters not passed" };
               encryptResult = encryption.encrypt(JSON.stringify(response));
               //  console.log("======>1792 Encrypted", result);
               res.send(encryptResult);
               //res.status(200).json()
          }
     } catch (err) {
          console.log(err);
          response = { 'status': 'failed', 'message': 'Internal server error' };
          encryptResult = encryption.encrypt(JSON.stringify(response));
          //  console.log("======>1792 Encrypted", result);
          res.send(encryptResult);
          // res.status(200).json()
     }

}




module.exports.Loadtest = function (req, res) {
     try {
          registrationModel.loadtest().then(response => {
               if (response != null) {
                    res.status(200).json({ 'status': 'success', 'message': 'UserDetails', 'data': response });
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': 'No data found' });
               }
          }).catch(err => {
               console.log(err);
               res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' });
          })

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': 'Internal Server Error' })
     }
}





