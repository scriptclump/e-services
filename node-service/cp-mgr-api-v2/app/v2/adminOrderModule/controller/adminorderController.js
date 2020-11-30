var adminOrderModel = require('../model/adminOrderModel');
var strtotime = require('strtotime');
const moment = require('moment');

/*
purpose : Used to save geo location
request : Order detailes
resposne  :Placed order details
Author : Deepak tiwari
*/
module.exports.saveGeoData = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.token != 'undefined' && data.token != '') {
                    //validating customer token
                    adminOrderModel.checkCustomerToken(data.token).then(checkToken => {
                         if (checkToken > 0) {
                              if (typeof data.geo_type != 'undefined' && data.geo_type != '') {
                                   if (typeof data.latitude != 'undefined' && data.latitude != '') {
                                        if (typeof data.longitude != 'undefined' && data.longitude != '') {
                                             if (typeof data.user_id != 'undefined' && data.user_id != '') {
                                                  data.accuracy = (typeof data.accuracy != 'undefined' && data.accuracy != '') ? data.accuracy : 0;
                                                  data.heading = (typeof data.heading != 'undefined' && data.heading != '') ? data.heading : 0;
                                                  data.route_id = (typeof data.route_id != 'undefined' && data.route_id != '') ? data.route_id : 0;
                                                  data.speed = (typeof data.speed != 'undefined' && data.speed != '') ? data.speed : 0;
                                                  let user_id = data.user_id;
                                                  adminOrderModel.getGeoDetails(user_id).then(geoDetails => {
                                                       console.log("=======>28 geoDetails", geoDetails);
                                                       let lastInserted = typeof geoDetails.created_at != 'undefined' ? geoDetails.created_at : "";
                                                       let lastLatitude = typeof geoDetails.latitude != 'undefined' ? geoDetails.latitude : "";
                                                       let lastLongitude = typeof geoDetails.longitude != 'undefined' ? geoDetails.longitude : "";
                                                       let lastInsertTime = typeof lastInserted != 'undefined' ? strtotime(lastInserted) : strtotime('-5 minutes');
                                                       let curTime = strtotime(moment().format("YYYY-MM-DDTHH:mm:ss"));
                                                       let differenceInTime = curTime - lastInsertTime;
                                                       console.log("=====>36", sec = Math.floor(differenceInTime / 60000), differenceInTime, lastInsertTime, curTime, differenceInTime, Math.floor((differenceInTime / 1000) % 60));
                                                       //checking for multiple calls in less than one minute ,
                                                       if (differenceInTime > 50 && (lastLatitude != Math.round(data.latitude, 5) || lastLongitude != round(data.longitude, 5))) {
                                                            adminOrderModel.saveGeoData(data).then(result => {
                                                                 if (result != '') {
                                                                      res.status(200).json({ 'status': 'success', 'message': 'Inserted geo data' });
                                                                 } else {
                                                                      res.status(200).json({ 'status': 'failed', 'message': 'Unsuccessful operation.Please try later' })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
                                                            })
                                                       } else {
                                                            res.status(200).json({ 'status': 'failed', 'message': "Multiple calls in less than 1 min with same lat/long values" })
                                                       }

                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             } else {
                                                  res.status(200).json({ 'status': 'failed', 'message': 'Already exist user' })
                                             }
                                        } else {
                                             res.status(200).json({ 'status': 'failed', 'message': 'Please provide your current location' });
                                        }
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': 'Please provide your current location' });
                                   }
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'Please provide geo type' });
                              }
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': 'You have already logged into ebutor system' });
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': "Please provide token" });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Please pass required parameter' });
          }
     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': 'Internal server error' });
     }
}



