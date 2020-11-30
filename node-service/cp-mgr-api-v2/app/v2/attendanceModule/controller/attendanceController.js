const attendanceModel = require('../model/attendanceModel');
let cpMessage = require('../../../config/cpMessage');


/*
purpose : Used to get hub user details
request : Order detailes
resposne  :Placed order details
Author : Deepak tiwari
*/
module.exports.getHubUsers = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               // Added Legal Entity Id as we deal with mulitple stockicts
               if (typeof data.legal_entity_id == 'undefined' || typeof data.legal_entity_id != 'undefined' && data.legal_entity_id == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Please provide legal Entity Id' })
               }
               //validating dc Id
               if (typeof data.dcid == 'undefined' || typeof data.dcid != 'undefined' && data.dcid == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Please provide Dc Id' })
               }

               //valiadting token
               if (typeof data.token != 'undefined' && data.token != '') {
                    if (typeof data.user_id != 'undefined' && data.user_id != '') {
                         if (typeof data.date != 'undefined' && data.date != '') {
                              attendanceModel.checkCustomerToken(data.token).then(valToken => {
                                   if (valToken > 0) {
                                        let currentminRoles = [];
                                        let currentRoles;
                                        let subRoles;
                                        let userLegalId;
                                        let reportingLegalId;
                                        let leId;
                                        let ignoreUserId = '';
                                        let team;
                                        attendanceModel.getMyRoles(data.user_id).then(Roles => {
                                             console.log("currentRoles", Roles);
                                             currentRoles = Roles.role_id
                                             currentminRoles = Math.min(currentRoles)
                                             console.log("currentRoles", currentminRoles, currentRoles);
                                             if (currentRoles != '') {
                                                  attendanceModel.getSubroles(currentminRoles, data.user_id).then(result => {
                                                       console.log("result==>44", result);
                                                       subRoles = result;
                                                       attendanceModel.getMyLegalentityId(data.user_id).then(userleId => {
                                                            console.log("userlegalid= >>>43", userleId);
                                                            userLegalId = userleId.user_id;
                                                            attendanceModel.getMyLegalentityIdofReporting(userLegalId).then(reportinglegalid => {
                                                                 console.log('reportingleID', reportinglegalid);
                                                                 reportingLegalId = reportinglegalid;
                                                                 attendanceModel.getlegalidbasedondcid(data.dcid).then(leid => {
                                                                      if (typeof leid != 'undefined' && leid != '') {
                                                                           console.log("leid", leid);
                                                                           leId = leid.legal_entity_id;
                                                                      } else {
                                                                           leId = '';
                                                                      }
                                                                      attendanceModel.getSuppliersByUser(data.user_id, data.legal_entity_id, data.dcid, subRoles, reportingLegalId, ignoreUserId).then(value => {
                                                                           console.log("value==>54", value);
                                                                           team = value;
                                                                           attendanceModel.getUserData(team, date.date).then(result => {
                                                                                console.log("result 58====>", result);
                                                                                if (result != '') {
                                                                                     res.status(200).json({ 'status': 'success', 'message': "Hub user details", 'data': result })
                                                                                } else {
                                                                                     res.status(200).json({ 'status': 'failed', 'message': "no data found" })
                                                                                }
                                                                           }).catch(err => {
                                                                                console.log(err);
                                                                                res.status(200).json({ 'status': 'failed', 'message': "Something went wrong" })
                                                                           })
                                                                      }).catch(err => {
                                                                           console.log(err);
                                                                           res.status(200).json({ 'status': 'failed', 'message': "Something went wrong" })
                                                                      })
                                                                 }).catch(err => {
                                                                      console.log(err);
                                                                      res.status(200).json({ 'status': 'failed', 'message': "Something went wrong" })
                                                                 })
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 res.status(200).json({ 'status': 'failed', 'message': "Something went wrong" })
                                                            })
                                                       }).catch(err => {
                                                            console.log(err);
                                                            res.status(200).json({ 'status': 'failed', 'message': "Something went wrong" })
                                                       })
                                                  }).catch(err => {
                                                       console.log(err);
                                                       res.status(200).json({ 'status': 'failed', 'message': "Something went wrong" })
                                                  })
                                             }
                                        }).catch(err => {
                                             console.log(err);
                                             res.status(200).json({ 'status': 'failed', 'message': "Something went wrong" })
                                        })
                                   } else {
                                        res.status(200).json({ 'status': 'session', 'message': "You have already logged into the Ebutor System" })
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': "Something went wrong" })
                              })
                         } else {
                              res.status(200).json({ 'status': 'failed', 'message': "Please provide date" })
                         }
                    } else {
                         res.status(200).json({ 'status': 'failed', 'message': "Please provide userId" })
                    }
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': "Please provide access token" })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Required parameter not passed' });
          }
     } catch (err) {
          console.log(err.message);
          res.status(200).json({ 'status': 'failed', 'message': "Internal server error" })
     }
}

/*
purpose : Used to saveAttendance
request : UserId , date , token , attendance_Date
resposne  :Return updated attendance details
Author : Deepak tiwari
*/
module.exports.saveAttendance = function (req, res) {
     try {
          if (req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               if (typeof data.token != 'undefined' && data.token != '') {
                    if (typeof data.user_id != 'undefined' && data.user_id != '') {
                         if (typeof data.date != 'undefined' && data.date != '') {
                              if (typeof data.attendance_data != 'undefined' && data.attendance_data != '') {
                                   attendanceModel.checkCustomerToken(data.token).then(validateToken => {
                                        if (validateToken > 0) {
                                             attendanceModel.saveAttendances(data).then(result => {
                                                  console.log("result===>135", result);
                                                  if (result != '') {
                                                       res.status(200).json({ 'status': 'success', 'message': "Attendance saved successfully" });
                                                  } else {
                                                       res.status(200).json({ 'status': 'failed', 'message': "Unable to save attendance please try later" });
                                                  }
                                             }).catch(err => {
                                                  console.log(err);
                                             })
                                        } else {
                                             res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken })
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
                                   })
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': 'Attendance date not passed' })
                              }
                         } else {
                              res.status(200).json({ 'status': 'failed', 'message': 'Please provide date' });
                         }
                    } else {
                         res.status(200).json({ 'status': 'failed', 'message': 'Please provide user Id' });
                    }
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed });
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
          }
     } catch (err) {
          res.status(200).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/*
purpose : Used to get vehicle id based on user_id
request : UserId , date , token , attendance_Date
resposne  :Return vehicle id 
Author : Deepak tiwari
*/
module.exports.getVehicleIdsByUserId = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               if (typeof data.user_id == 'undefined' && data.user_id == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Invalid user Id' })
               }
               //validating admin token
               if (typeof data.admin_token != 'undefined' && data.admin_token != '') {
                    attendanceModel.checkCustomerToken(data.admin_token).then(validateToken => {
                         if (validateToken > 0) {
                              //fetching vehicle Id based on user_id
                              attendanceModel.getVehicleIdsByUserIdModel(data.user_id, data.vehicle_type).then(response => {
                                   if (response != '') {
                                        let result = { 'hub_vehicles': response }
                                        res.status(200).json({ "status": 'success', 'message': 'Available vehicle', 'data': result })
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': 'No vehicle available' })
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                              })
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}

/*
purpose : Used to save vehicle attendance
request : UserId , date , token , attendance_Date
resposne  :Return vehicle id 
Author : Deepak tiwari
*/
module.exports.saveVehicleAttendance = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               //userId validation
               if (typeof data.user_id == 'undefined' && data.user_id == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Invalid user Id' })
               }
               //date validation
               if (typeof data.date == 'undefined' && data.date == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Invalid Attendance Date' })
               }
               //attendance_data validation
               if (typeof data.attendance_data != 'undefined' && data.attendance_data == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Invalid Attendance Date' })
               }
               //validating admin token
               if (typeof data.token != 'undefined' && data.token != '') {
                    attendanceModel.checkCustomerToken(data.token).then(validateToken => {
                         if (validateToken > 0) {
                              //fetching vehicle Id based on user_id
                              attendanceModel.saveVehicleAttendances(data).then(response => {
                                   if (response) {
                                        let result = { 'hub_vehicles': response }
                                        res.status(200).json({ "status": 'success', 'message': 'Vehicle attendance updated sucessfully', 'data': result });
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': 'Unable to update vehicle attendance' })
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
                              })
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }

     } catch (err) {
          res.status(200).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}


/*
purpose : Used to save temporary vehicle 
request : UserId , date , token , vehicle_type , vehicle_req_no
resposne  :Return update details
Author : Deepak tiwari
*/
module.exports.saveTemporaryVehicle = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               //userId validation
               if (typeof data.user_id == 'undefined' && data.user_id == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Invalid user Id' })
               }
               //vehicle_type
               if (typeof data.vehicle_type == 'undefined' && data.vehicle_type == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Vehicle type is required field' })
               }
               //"Vehicle Reg No 
               if (typeof data.vehicle_reg_no != 'undefined' && data.vehicle_reg_no == '') {
                    res.status(200).json({ 'status': 'failed', 'message': 'Vehicle Reg No is required field' })
               }
               //replace_with
               if (typeof data.replace_with != 'undefined' && data.replace_with == '') {
                    data.replace_with = null;
               }
               //validating admin token
               if (typeof data.token != 'undefined' && data.token != '') {
                    attendanceModel.checkCustomerToken(data.token).then(validateToken => {
                         if (validateToken > 0) {
                              //fetching vehicle Id based on user_id
                              attendanceModel.saveTemporaryVehicleData(data).then(response => {
                                   if (response != '') {
                                        res.status(200).json({ "status": response.status, 'message': response.message })
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': 'Unable to update vehicle details' })
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                              })
                         } else {
                              res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken })
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }

     } catch (err) {
          res.status(200).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}