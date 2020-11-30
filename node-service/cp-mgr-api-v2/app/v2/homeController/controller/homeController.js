const homeModel = require('../../homeController/model/homeModel');
const cpMessage = require('../../../config/cpMessage');
const moment = require('moment');
const Joi = require('joi');//cleint side validation
const upload = require('../../../config/s3Config');
const encryption = require('../../../config/encryption.js');

/**
 * Purpose : Used to verify mobile version
 */
module.exports.getversion = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let result = {};
               let data = JSON.parse(req.body.data);
               let userId = (typeof data.user_id != 'undefined' && data.user_id != '') ? data.user_id : 0;
               let deviceId = (typeof data.device_id != 'undefined' && data.device_id != '') ? data.device_id : '';
               let ipAddress = (typeof data.ip_address != 'undefined' && data.ip_address != '') ? data.ip_address : 0;
               let regId = (typeof data.reg_id != 'undefined' && data.req_id != '') ? data.reg_id : 0;
               let platformId = (typeof data.platform_id != 'undefined' && data.platform_id != '') ? data.platform_id : 0;
               let dbVersion = (typeof data.db_version != 'undefined' && data.db_version != '') ? data.db_version : 0;
               if (deviceId != '' && userId != 0) {
                    homeModel.InsertDeviceDetails(userId, deviceId, ipAddress, platformId, regId);//used to insert device details
               }
               //used to get mobile version and mobile details based on vesion number and type of nobile
               homeModel.versioncheck(data.number, data.type).then(response => {
                    console.log("--->>21", response);
                    let number = '';
                    let name = '';
                    let type = '';
                    response.forEach((element) => {
                         number = element.number;
                         type = element.type;
                    })
                    console.log(number, type)
                    if (number != '' && type != '') {
                         result = { 'status': "update", 'versionUpdateStatus': '1', 'version_number': number, 'db_version': dbVersion, 'currentdate': moment().format("DD-MM-YYYY") }
                         res.json(result);
                    } else {
                         result = { 'status': 'Not required', 'versionUpdateStatus': '0', 'version': "No update required", 'db_version': dbVersion, 'currentdate': moment().format("DD-MM-YYYY") }
                         res.json(result);
                    }
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
               })
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
          }

     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

//Helpfull in sorting of data based pn select field
module.exports.getSortingDataFilter = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               // let data = JSON.parse(req.body.data);
               let decryptedData = encryption.decrypt(req.body.data);
               let data = JSON.parse(decryptedData);
               let response = {};
               let encryptResult;
               let flag = typeof data.flag != 'undefined' ? data.flag : 0;
               if (flag == 1) {
                    if (typeof data.sales_token != 'undefined' && data.sales_token != '') {
                         homeModel.checkCustomerToken(data.sales_token).then(checkToken => {
                              if (checkToken > 0) {
                                   // 146 is master_lookup  value for sorting
                                   homeModel.getMasterLookupValues(146).then(sortingData => {
                                        if (sortingData != '') {
                                             // res.status(200).json({ 'status': 'success', 'message': 'Sorted data', 'data': sortingData });
                                             response = { 'status': 'success', 'message': 'Sorted data', 'data': sortingData };
                                             encryptResult = encryption.encrypt(JSON.stringify(response));
                                             res.send(encryptResult);
                                        } else {
                                             //res.status(200).json({ 'status': 'failed', 'message': 'Please apply filter' });
                                             response = { 'status': 'failed', 'message': 'Please apply filter' };
                                             encryptResult = encryption.encrypt(JSON.stringify(response));
                                             res.send(encryptResult);
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        //res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                        response = { 'status': 'failed', 'message': cpMessage.internalCatch };
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
                              // res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                              response = { 'status': 'failed', 'message': cpMessage.internalCatch };
                              encryptResult = encryption.encrypt(JSON.stringify(response));
                              res.send(encryptResult);
                         })
                    } else {
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed });
                         response = { 'status': 'failed', 'message': cpMessage.tokenNotPassed };
                         encryptResult = encryption.encrypt(JSON.stringify(response));
                         res.send(encryptResult);
                    }
               } else {
                    homeModel.getSortingDataFilter().then(sortingData => {
                         if (sortingData != '') {
                              //res.status(200).json({ 'status': 'success', 'message': 'Sorted data', 'data': sortingData });
                              response = { 'status': 'success', 'message': 'Sorted data', 'data': sortingData };
                              encryptResult = encryption.encrypt(JSON.stringify(response));
                              res.send(encryptResult);
                         } else {
                              //  res.status(200).json({ 'status': 'failed', 'message': 'Please apply some filter' });
                              response = { 'status': 'failed', 'message': 'Please apply some filter' };
                              encryptResult = encryption.encrypt(JSON.stringify(response));
                              res.send(encryptResult);
                         }
                    }).catch(err => {
                         console.log(err);
                         //res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                         response = { 'status': 'failed', 'message': cpMessage.internalCatch };
                         encryptResult = encryption.encrypt(JSON.stringify(response));
                         res.send(encryptResult);
                    })
               }

          } else {
               //res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
               response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody };
               encryptResult = encryption.encrypt(JSON.stringify(response));
               res.send(encryptResult);
          }

     } catch (err) {
          console.log(err);
          //res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
          response = { 'status': 'failed', 'message': cpMessage.serverError };
          encryptResult = encryption.encrypt(JSON.stringify(response));
          res.send(encryptResult);
     }


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
          console.log("inside")
          var singleupload = upload.single('img');
          singleupload(req, res, function (err, data) {
               if (err) {
                    console.log(err)
                    res.status(200).json({ success: false, message: "Unable to upload " })
               } else {
                    console.log("res.loc", req.files.img[0].location)
                    res.status(200).json({ success: true, message: "Uploaded Successfully ", data: req.files.img[0].location })
               }
          });
     } catch (err) {
          console.log(err)
          res.status(500).json({ success: false, message: "Unable To Process Your Request , Please Try Later" })
     }
}

module.exports.UnBilledSkus = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               //let data = JSON.parse(req.body.data);
               let decryptedData = encryption.decrypt(req.body.data);
               let data = JSON.parse(decryptedData);
               let Response = {};
               let encryptResult;
               if (typeof data.sales_token != 'undefined' && data.sales_token != '') {
                    if (typeof data.ff_id != 'undefined' && data.ff_id != '') {
                         if (typeof data.offset != 'undefined' && data.offset != '') {
                              if (typeof data.offset_limit != 'undefined' && data.offset_limit != '') {
                                   if (typeof data.is_billed != 'undefined' && data.is_billed != '') {
                                        if (typeof data.start_date != 'undefined' && data.start_date != '') {
                                             homeModel.checkSalesToken(data.sales_token).then(checkSalesToken => {
                                                  if (checkSalesToken > 0) {
                                                       homeModel.getUnBilledSkus(data).then(response => {
                                                            if (response.product_id) {
                                                                 // res.status(200).json({ 'status': 'success', 'message': 'getUnbilledSKUS', 'data': response });
                                                                 Response = { 'status': 'success', 'message': 'getUnbilledSKUS', 'data': response };
                                                                 encryptResult = encryption.encrypt(JSON.stringify(Response));
                                                                 res.send(encryptResult);
                                                            } else {
                                                                 // res.status(200).json({ 'status': 'failed', 'message': 'No products SKUs Found', 'data': [] });
                                                                 Response = { 'status': 'failed', 'message': 'No products SKUs Found', 'data': [] };
                                                                 encryptResult = encryption.encrypt(JSON.stringify(Response));
                                                                 res.send(encryptResult);
                                                            }
                                                       }).catch(err => {
                                                            console.log(err);
                                                            // res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch, 'data': [] });
                                                            Response = { 'status': 'failed', 'message': cpMessage.internalCatch, 'data': [] };
                                                            encryptResult = encryption.encrypt(JSON.stringify(Response));
                                                            res.send(encryptResult);
                                                       })
                                                  } else {
                                                       // res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken, 'data': [] });
                                                       Response = { 'status': 'session', 'message': cpMessage.invalidToken, 'data': [] };
                                                       encryptResult = encryption.encrypt(JSON.stringify(Response));
                                                       res.send(encryptResult);
                                                  }
                                             }).catch(err => {
                                                  console.log(err);
                                                  Response = { 'status': 'failed', 'message': cpMessage.internalCatch, 'data': [] };
                                                  encryptResult = encryption.encrypt(JSON.stringify(Response));
                                                  res.send(encryptResult);
                                             })
                                        } else {
                                             // res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] });
                                             Response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] };
                                             encryptResult = encryption.encrypt(JSON.stringify(Response));
                                             res.send(encryptResult);
                                        }
                                   } else {
                                        // res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] });
                                        Response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] };
                                        encryptResult = encryption.encrypt(JSON.stringify(Response));
                                        res.send(encryptResult);
                                   }
                              } else {
                                   // res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] });
                                   Response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] };
                                   encryptResult = encryption.encrypt(JSON.stringify(Response));
                                   res.send(encryptResult);
                              }
                         } else {
                              //  res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] });
                              Response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] };
                              encryptResult = encryption.encrypt(JSON.stringify(Response));
                              res.send(encryptResult);
                         }
                    } else {
                         // res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] });
                         Response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] };
                         encryptResult = encryption.encrypt(JSON.stringify(Response));
                         res.send(encryptResult);
                    }
               } else {
                    //res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
                    Response = { 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] };
                    encryptResult = encryption.encrypt(JSON.stringify(Response));
                    res.send(encryptResult);
               }
          }
     } catch (err) {
          //res.staus(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
          console.log(err);
          let error = { 'status': 'failed', 'message': cpMessage.serverError };
          encryptResult = encryption.encrypt(JSON.stringify(error));
          res.send(encryptResult);
     }

}


