
const usermodel = require('../non_sequelize_model/cpRegistration.model');
var validator = require("email-validator");
var upload = require('../../../cp-mgr-api/config/s3config');
var config = require('../../../cp-mgr-api/config/config.json');
const aws = require('aws-sdk');
const BUCKET_NAME = 'ebutormedia';
const IAM_USER_KEY = 'AKIAJTLG7MDDMDYFY3NQ';
const IAM_USER_SECRET = '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';


//file upload 
function uploadToS3(file) {
     return new Promise((resolve, reject) => {
          let s3bucket = new aws.S3({
               accessKeyId: config.S3AccessKeyId,
               secretAccessKey: config.S3AecretAccessKey,
               Bucket: config.S3BucketName
          });
          var params = {
               Bucket: config.S3BucketName,
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
               // console.log(data);
               resolve(data.Location);
          });
     })

}

//fucntion is used to generate random string
//purpose
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
                    usermodel.registration(telephone, buyer_type_id, sales_token).then((result) => {
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
                              res.status(200).json({ status: "failed", message: "Entered wrong otp" })
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
                    let appFlag;
                    if (typeof Data.customer_token != 'undefined') {
                         customer_token = Data.customer_token;
                         custflag = 2;

                    } else {
                         customer_token = '';
                         custflag = '';

                    }
                    if (typeof Data.app_flag != 'undefined' && Data.app_flag != '') {
                         appFlag = DATA.app_flag;
                    } else {
                         appFlag = 0;
                    }
                    usermodel.resendOtp(telephone, customer_token, custflag, appFlag).then((result) => {
                         if (result) {
                              if (result.status == 1) {
                                   res.status(200).json({ status: "success", message: "Otp sent successfully", "data": result })
                              } else {
                                   res.status(200).json({ status: "failed", message: result.message })
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
                    let state_name;
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
                    let gst_doc_file = [];
                    let fssai_doc_file = [];

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

                    console.log("gst_doc");
                    //upload gst_doc to aws s3
                    if (typeof req.files.gst_doc != 'undefined') {
                         if (typeof req.files.gst_doc[0].buffer == 'undefined') {
                              var singleupload = upload.single('gst_doc');
                              singleupload(req, res, function (err, data) {
                                   if (err) {
                                        console.log(err);
                                   } else {
                                        gst_doc_file.push(req.files.gst_doc[0].location);
                                   }
                              });

                         } else {
                              uploadToS3(req.files.gst_doc).then(response => {
                                   gst_doc_file.push(response);
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ status: 'failed', message: "Something went wrong " })
                              })
                              //console.log("url", req.files.img[0].buffer);
                              //console.log("origin", req.files.img[0].originalname)
                         }
                    } else {
                         gst_doc_file = [];
                    }

                    console.log("fssai_doc")
                    //upload fssai_doc to aws s3
                    if (typeof req.files.fssai_doc != 'undefined') {
                         if (typeof req.files.fssai_doc[0].buffer == 'undefined') {
                              var singleupload = upload.single('fssai_doc');
                              singleupload(req, res, function (err, data) {
                                   if (err) {
                                        console.log(err);
                                   } else {
                                        fssai_doc_file.push(req.files.fssai_doc[0].location);
                                   }
                              });

                         } else {
                              uploadToS3(req.files.fssai_doc).then(response => {
                                   fssai_doc_file.push(response);
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ status: 'failed', message: "Something went wrong " })
                              })

                         }

                    } else {
                         fssai_doc_file = [];
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
                         if (Data.firstname == '') {
                              data = { message: "Please enter firstname between 4 to 32 characters", status: "Failed" }
                              console.log(data);
                              firstname = '';
                         } else {
                              firstname = Data.firstname;
                         }
                    } else {
                         firstname = '';
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
                         city = '';
                         data = { message: "Please enter city", status: "Failed" }
                         console.log(data);

                    }

                    /// pincode = Data.pincode;
                    if (typeof Data.business_legal_name != 'undefined') {
                         if (Data.business_legal_name == '') {
                              data = { message: "Please enter business_legal_name between 4 to 32 characters", status: "Failed" }
                              console.log(data);
                              business_legal_name = '';
                         } else {

                              business_legal_name = Data.business_legal_name;
                         }

                    } else {
                         business_legal_name = '';
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
                         usermodel.checkUser(Data.contact_no1).then((result_no1) => {
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
                         usermodel.checkUser(Data.contact_no2).then((result) => {
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

                    //state_name
                    if (typeof Data.state_name != 'undefined' && Data.state_name != "") {
                         state_name = Data.state_name;
                    } else {
                         state_name = '';
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
                         usermodel.getMasterLookupValues(106).then((master_data) => {
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
                    let fssai = (typeof Data.fssai != 'undefined' && Data.fssai != '') ? Data.fssai : '';
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
                              usermodel.checkDeviceId(device_id).then((checkDeviceId) => {
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
                    // usermodel.logApiRequests(request);
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
                         usermodel.checkCustomerToken(Data.sales_token).then((checkSalesToken) => {
                              if (checkSalesToken > 0) {
                                   sales_token = Data.sales_token;
                                   //Pincode 
                                   if (typeof Data.pincode != 'undefined') {
                                        if (Data.pincode == '' || Data.pincode.length < 4 || Data.pincode.length > 32) {
                                             data = { message: "Please enter pincode 6 digit number", status: "Failed" }
                                             console.log(data)
                                        } else {
                                             usermodel.checkPincode(Data.pincode).then((chk_pincode) => {
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
                                                                      usermodel.getEmail(Data.email_id).then((emailchk) => {
                                                                           if (typeof emailchk != 'undefined' && emailchk == 0) {
                                                                                email_id = Data.email_id;
                                                                                usermodel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                                                     bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                                                     sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe, fssai, gst_doc_file, fssai_doc_file, state_name).then((final_result) => {
                                                                                          finalArray = final_result;
                                                                                          if (final_result.status == 1) {
                                                                                               if (typeof final_result.customer_id != 'undefined') {
                                                                                                    usermodel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
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
                                                                                                         res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                                                                    })
                                                                                               } else {
                                                                                                    final_result = Object.assign(final_result, {
                                                                                                         'sales_target': 0
                                                                                                    })
                                                                                                    res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                               }
                                                                                          } else {
                                                                                               res.status(200).json({ status: 'failed', message: final_result.message })
                                                                                          }

                                                                                     }).catch(err => {
                                                                                          console.log(err);
                                                                                          res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                                                     })

                                                                           } else if (emailchk > 0) {
                                                                                let data_8 = { status: "failed", message: "Email already exist" }
                                                                                res.json(data_8)
                                                                           }
                                                                      }).catch((err) => {
                                                                           console.log(err);
                                                                           res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                                      })
                                                                 }
                                                            } else {
                                                                 email_id = "";
                                                                 usermodel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                                      bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                                      sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe, fssai, gst_doc_file, fssai_doc_file, state_name).then((final_result) => {
                                                                           finalArray = final_result;
                                                                           if (final_result.status == 1) {
                                                                                if (typeof final_result.customer_id != 'undefined') {
                                                                                     usermodel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
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
                                                                                          res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                                                     })
                                                                                } else {
                                                                                     final_result = Object.assign(final_result, {
                                                                                          'sales_target': 0
                                                                                     })
                                                                                     res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                }

                                                                           } else {
                                                                                res.status(200).json({ status: 'failed', message: final_result.message })
                                                                           }
                                                                      }).catch(err => {
                                                                           console.log(err);
                                                                           res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                                      })
                                                            }
                                                       }
                                                  }

                                             }).catch((err) => {
                                                  console.log(err);
                                                  res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                             })
                                        }
                                   } else {
                                        data = { message: "Please enter pincode", status: "Failed" }
                                        res.status(200).json(data)
                                   }

                              } else {
                                   res.status(200).json({ status: "session", message: 'Your Session Has Expired. Please Login Again.' })
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
                                   usermodel.checkPincode(Data.pincode).then((chk_pincode) => {
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
                                                            usermodel.getEmail(Data.email_id).then((emailchk) => {
                                                                 if (typeof emailchk != 'undefined' && emailchk == 0) {
                                                                      email_id = Data.email_id;
                                                                      usermodel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                                           bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                                           sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe, fssai, gst_doc_file, fssai_doc_file, state_name).then((final_result) => {
                                                                                finalArray = final_result;
                                                                                if (final_result.status == 1) {
                                                                                     if (typeof final_result.customer_id != 'undefined') {
                                                                                          usermodel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
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
                                                                                               res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                                                          })
                                                                                     } else {
                                                                                          final_result = Object.assign(final_result, {
                                                                                               'sales_target': 0
                                                                                          })
                                                                                          res.status(200).json({ status: 'success', message: "Registered successfully", data: final_result })
                                                                                     }

                                                                                } else {
                                                                                     res.status(200).json({ status: 'failed', message: final_result.message })
                                                                                }

                                                                           }).catch(err => {
                                                                                console.log(err);
                                                                                res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                                           })

                                                                 } else if (emailchk > 0) {
                                                                      let data_8 = { status: "failed", message: "Email already exist" }
                                                                      res.json(data_8)
                                                                 }
                                                            }).catch((err) => {
                                                                 console.log(err);
                                                                 res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                            })
                                                       }
                                                  } else {
                                                       //when email id not passed
                                                       email_id = "";
                                                       usermodel.address(business_legal_name, segment_id, tin_number, address1, address2, locality, landmark, city, pincode, firstname, email_id, mobile_no, doc_file_path, profile_picture, latitude, longitude, download_token, ip_address, device_id, pref_value, pref_value1,
                                                            bstart_time, bend_time, state_id, noof_shutters, volume_class, license_type, sales_token, contact_no1, contact_no2, contact_name1, contact_name2, area, master_manf, smartphone, network, lastname, beat_id, customer_type, gstin, arn_number, is_icecream,
                                                            sms_notification, is_milk, is_fridge, is_vegetables, is_visicooler, dist_not_serv, facilities, is_deepfreezer, is_swipe, fssai, gst_doc_file, fssai_doc_file, state_name).then((final_result) => {
                                                                 finalArray = final_result;
                                                                 if (final_result.status == 1) {
                                                                      if (typeof final_result.customer_id != 'undefined') {
                                                                           usermodel.checkPermissionByFeatureCode_controller('SALESTARGET001', final_result.customer_id).then((salesTargetFeature) => {
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
                                                                      res.status(200).json({ status: 'failed', message: final_result.message })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
                                                            })
                                                  }
                                             }
                                        }

                                   }).catch((err) => {
                                        console.log(err);
                                        res.status(200).json({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442." })
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
          console.log(err);
          res.status(200).json({ status: 'failed', message: "Internal server error" })
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
          usermodel.validateToken(data.customer_token).then(customer_token => {
               customerToken = customer_token;
               if (customerToken.token_status == 0) { //==0
                    res.status(200).json({ 'status': 'Failed', 'message': 'Your Session Has Expired. Please Login Again.' })
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
                         // console.log("retailer", retailerInfo)
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

/*
Purpose : Used to validate enter mobile number for registration
author : Deepak Tiwari
Request : Telephone number
Resposne : Returns already exsist message match found in users table.
*/
module.exports.MobileNoValidation = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.telephone != 'undefined' && data.telephone != '') {
                    usermodel.checkUser(data.telephone).then((result) => {
                         if (result > 0) {
                              res.status(200).json({ status: "failed", message: "Entered mobile number already exists" })
                         } else {
                              res.status(200).json({ status: "success", message: "Mobile number not exists" })
                         }
                    }).catch(err => {
                         console.log(err)
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': "Please enter mobile number" })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': "Required parameter not send" })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': "Internal server error" })
     }
}

//used to insert missing record from retailerFlat table 
module.exports.insertFlatTabele = function (req, res) {
     try {
          let data = JSON.parse(req.body.data);
          usermodel.InsertFlatTable(data.telephone).then((response) => {
               if (response) {
                    res.send(response);
               }
          }).catch((err) => {
               console.log(err);
               res.status(200).json({ 'status': 'failed', message: 'Something went wrong ' })
          })

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', message: 'Internal server error ' })
     }
}



