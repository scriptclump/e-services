var assignOrderModel = require('../model/assignOrderModel');


/*
purpose : Used to get pending collection date
request : Order detailes
resposne  :Placed order details
Author : Deepak tiwari
*/
module.exports.getPendingCollectionDate = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let userId = typeof data.user_id != 'undefined' ? parseInt(data.user_id) : 0;
               let adminToken = data.adminToken;
               let allowTrip;
               let collectedOn;
               let submitedBy;
               let submitDo = 1;
               let remittedHi;
               if (!userId) {
                    res.status(200).json({ 'status': 404, 'message': 'Invalid user id' })
               }
               //validating admintoken
               assignOrderModel.checkCustomerToken(adminToken).then(validation => {
                    if (!validation) {
                         res.status(200).json({ 'status': 404, 'message': 'Authentication failed. Verify credentials' })
                    }
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' });
               })

               //Allow to start trip with pending colletions
               assignOrderModel.getMasterLookup(78017).then(masterDesc => {
                    if (masterDesc != '') {
                         allowTrip = typeof masterDesc.description != 'undefined' ? masterDesc.description : 0;
                    }
                    assignOrderModel.getPendingCollectionDate(userId).then(collectedon => {
                         collectedOn = collectedon;
                         //submitted by Delivery Officer collections
                         assignOrderModel.getMasterLookup(78019).then(masterValue => {
                              submitedBy = typeof masterValue.description != 'undefined' ? masterValue.description : 0;
                              if (submitedBy == 1) {//===
                                   assignOrderModel.getPendingCollectionHI(userId, [57055]).then(result => {
                                        submitDo = result;
                                        //submitted by Hub incharge collections
                                        assignOrderModel.getMasterLookup(78020).then(masterDesc => {
                                             if (masterDesc != '') {
                                                  let remittedToHi = typeof masterDesc.description != 'undefined' ? masterDesc.description : 0;
                                                  remittedHi = 1;
                                                  if (remittedToHi == 1) {//==
                                                       assignOrderModel.getPendingCollectionHI(userId, [57051]).then(response => {
                                                            remittedHi = response;
                                                            res.status(200).json({ 'status': 'success', 'collected_on': collectedOn, 'allow_trip': allowTrip, 'hubpending': submitDo, 'remittedhi': remittedHi })
                                                       }).catch(err => {
                                                            console.log(err);
                                                            res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' });
                                                       })
                                                  }
                                             } else {
                                                  //else condition
                                                  res.status(200).json({ 'status': 'success', 'collected_on': collectedOn, 'allow_trip': allowTrip, 'hubpending': submitDo, 'remittedhi': remittedHi })
                                             }

                                        }).catch(err => {
                                             console.log(err);
                                             res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
                                        });
                                        //  res.status(200).json({ 'status': 'success', 'collected_on': collectedOn, 'allow_trip': allowTrip, 'hubpending': submitDo, 'remittedhi': remittedHi })
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' });
                                   })
                              } else {
                                   res.status(200).json({ 'status': 'success', 'collected_on': collectedOn, 'allow_trip': allowTrip, 'hubpending': submitDo, 'remittedhi': remittedHi })
                              }
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' });
                         })

                    }).catch(err => {
                         console.log(err);
                    });
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': 'Something went wrong' })
               });
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Required feild not passed' });
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': 'Something went wrong' })
     }

}