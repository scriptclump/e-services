const beatDashboardModel = require('../model/beatDashboardModel');
const cpMessage = require('../../../config/cpMessage');
const encryption = require('../../../config/encryption');

/*
purpose : Used to get pending collection date
request : Order detailes
response  :Placed order details
Author : Deepak tiwari
*/
module.exports.getAllData = function (req, res) {
     try {
          let status = "success";
          let message = 'No data found.';
          let response = [];
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               //let data = JSON.parse(req.body.data);
               let decryptedData = encryption.decrypt(req.body.data);
               let data = JSON.parse(decryptedData);
               let encryptedResponse = {};
               let encryptResult;
               if (typeof data.customer_token != 'undefined' && data.customer_token != '') {
                    let token = data.customer_token;
                    beatDashboardModel.validateToken(token).then(validated => {
                         if (validated.token_status == 1) {
                              let dcType = typeof data.dc_type != 'undefined' ? data.dc_type : 0;
                              let beatId = typeof data.beat_id != 'undefined' ? data.beat_id : 0;
                              let requestId = (typeof data.request_id != 'undefined' && data.request_id != '') ? data.request_id : 0;
                              if (dcType > 0) {
                                   if (dcType == 118001) {// dc master_lookup value
                                        response = [];
                                        let returnAll = typeof data.return_all ? data.return_all : 0;
                                        beatDashboardModel.getalldc(returnAll, token).then(result => {
                                             if (typeof result == 'object') {
                                                  status = typeof result.status != 'undefined' ? result.status : 0;
                                                  if (status == 1) {
                                                       response = typeof result.data != 'undefined' ? result.data : 0;
                                                  } else {
                                                       message = typeof result.message != 'undefined' ? result.message : [];
                                                  }
                                             } else {
                                                  status = typeof result.status != 'undefined' ? result.status : 0;
                                                  if (status == 1) {
                                                       response = typeof result.data != 'undefined' ? result.data : 0;
                                                  } else {
                                                       message = typeof result.message != 'undefined' ? result.message : [];
                                                  }
                                             }
                                             // console.log("response  ==>48", response);
                                             if (response.length > 0) {
                                                  // res.status(200).json({ 'status': 'success', 'message': 'Successful.', 'data': response[0] });
                                                  encryptedResponse = { 'status': 'success', 'message': 'Successful.', 'data': response[0] };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             } else {
                                                  // res.status(200).json({ 'status': 'success', 'message': 'No data found', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'No data found', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             // res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                             encryptedResponse = { 'status': 'failed', 'message': cpMessage.internalCatch };
                                             encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                             res.send(encryptResult);
                                        })

                                   } else if (dcType == 118002) {//hub masterlookup value
                                        response = [];
                                        let returnAll = typeof data.return_all ? data.return_all : 0;
                                        beatDashboardModel.getHubsById(requestId, returnAll, token).then(async (result) => {
                                             // console.log("result ===> 57", result);
                                             if (typeof result == 'object') {
                                                  status = typeof result.status != 'undefined' ? result.status : 0;
                                                  if (status == 1) {
                                                       response = typeof result.data != 'undefined' ? result.data : 0;
                                                  } else {
                                                       message = typeof result.message != 'undefined' ? result.message : [];
                                                  }
                                             } else {
                                                  status = typeof result.status != 'undefined' ? result.status : 0;
                                                  if (status == 1) {
                                                       response = typeof result.data != 'undefined' ? result.data : 0;
                                                  } else {
                                                       message = typeof result.message != 'undefined' ? result.message : [];
                                                  }
                                             }

                                             let whBeatMap = await beatDashboardModel.getMappedBeats(beatId);
                                             for (let i = 0; i < response.length; i++) {
                                                  if (whBeatMap.includes(response[i].id)) {
                                                       response[i].flag = 1;
                                                  } else {
                                                       response[i].flag = 0;
                                                  }
                                             }
                                             if (response.length > 0) {
                                                  // res.status(200).json({ 'status': 'success', 'message': 'Successful.', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'Successful.', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             } else {
                                                  //res.status(200).json({ 'status': 'success', 'message': 'No data found', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'No data found', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             //res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' });
                                             encryptedResponse = { 'status': 'failed', 'message': 'Something went wrong' };
                                             encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                             res.send(encryptResult);
                                        })

                                   } else if (dcType == 118003) {
                                        let returnBeat = typeof data.returnBeats != 'undefined' ? data.returnBeats : 0;
                                        beatDashboardModel.getSpokesById(requestId, returnBeat).then(result => {
                                             response = result;
                                             if (response.length > 0) {
                                                  //res.status(200).json({ 'status': 'success', 'message': 'Successful.', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'Successful.', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             } else {
                                                  //res.status(200).json({ 'status': 'success', 'message': 'No data found', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'No data found', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             //res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                             encryptedResponse = { 'status': 'failed', 'message': cpMessage.internalCatch };
                                             encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                             res.send(encryptResult);
                                        })

                                   } else if (dcType == 118004) {
                                        beatDashboardModel.getBeatsById(requestId).then(result => {
                                             response = result;
                                             if (response.length > 0) {
                                                  // res.status(200).json({ 'status': 'success', 'message': 'Successful.', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'Successful.', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             } else {
                                                  // res.status(200).json({ 'status': 'success', 'message': 'No data found', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'No data found', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             // res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                             encryptedResponse = { 'status': 'failed', 'message': cpMessage.internalCatch };
                                             encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                             res.send(encryptResult);
                                        })

                                   } else if (dcType == 118005) {
                                        beatDashboardModel.getAreasById(requestId).then(result => {
                                             response = result;
                                             if (response.length > 0) {
                                                  // res.status(200).json({ 'status': 'success', 'message': 'Successful.', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'Successful.', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             } else {
                                                  // res.status(200).json({ 'status': 'success', 'message': 'No data found', 'data': response });
                                                  encryptedResponse = { 'status': 'success', 'message': 'No data found', 'data': response };
                                                  encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                                  res.send(encryptResult);
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             //  res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                             encryptedResponse = { 'status': 'failed', 'message': cpMessage.internalCatch };
                                             encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                             res.send(encryptResult);
                                        })

                                   } else {
                                        //  res.status(200).json({ 'status': 'failed', 'message': 'Please provide correct dcType' });
                                        encryptedResponse = { 'status': 'failed', 'message': 'Please provide correct dcType' };
                                        encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                        res.send(encryptResult);
                                   }
                              } else {
                                   //res.status(200).json({ 'status': 'failed', 'message': 'Please provide correct dcType' });
                                   encryptedResponse = { 'status': 'failed', 'message': 'Please provide correct dcType' };
                                   encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                                   res.send(encryptResult);
                              }
                         } else {
                              //  res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken });
                              encryptedResponse = { 'status': 'session', 'message': cpMessage.invalidToken };
                              encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                              res.send(encryptResult);
                         }
                    }).catch(err => {
                         console.log(err);
                         //res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                         encryptedResponse = { 'status': 'failed', 'message': cpMessage.internalCatch };
                         encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                         res.send(encryptResult);
                    })
               } else {
                    // res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed });
                    encryptedResponse = { 'status': 'failed', 'message': cpMessage.tokenNotPassed };
                    encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
                    res.send(encryptResult);
               }
          } else {
               //res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
               encryptedResponse = { 'status': 'failed', 'message': cpMessage.invalidRequestBody };
               encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
               res.send(encryptResult);
          }
     } catch (err) {
          console.log(err);
          //res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
          encryptedResponse = { 'status': 'failed', 'message': cpMessage.serverError };
          encryptResult = encryption.encrypt(JSON.stringify(encryptedResponse));
          res.send(encryptResult);
     }
}

/*
purpose : Used to store spoke details
request : Spoke detailes
response : Returns updated details
Author : Deepak tiwari
*/
module.exports.storeSpoke = function (req, res) {
     try {
          let spokeId = 0;
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               if (typeof data.customer_token != '' && data.customer_token != '') {
                    beatDashboardModel.validateToken(data.customer_token).then(validated => {
                         if (validated.token_status == 1) {
                              beatDashboardModel.saveSpokeData(data).then(response => {
                                   if (response != '' && response.status != 'failed') {
                                        res.status(200).json({ 'status': 'success', 'message': response.message, 'data': response });
                                   } else {
                                        res.status(200).json(response);
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                              })
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken });
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message ': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/*
purpose : Used to store beat details
request : Beat detailes
response : Returns updated details
Author : Deepak tiwari
*/
module.exports.storeBeat = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               if (typeof data.customer_token != '' && data.customer_token != '') {
                    beatDashboardModel.validateToken(data.customer_token).then(validated => {
                         if (validated.token_status == 1) {
                              beatDashboardModel.saveBeatData(data).then(response => {
                                   if (response != '' && response.status != 'failed') {
                                        res.status(200).json({ 'status': 'success', 'message': 'Successfully saved' });
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.UnsuccessfulOperation });
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                              })
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken });
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message ': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}