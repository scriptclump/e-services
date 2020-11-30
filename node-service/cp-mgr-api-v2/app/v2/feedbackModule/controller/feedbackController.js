let feedbackModel = require("../model/feedbackModel");
let cpMessage = require('../../../config/cpMessage');

/*
     Purpose : getFeedbackReasons function is used to fetch the feedback group from master_lookup table,
     author : Muzzamil,
     Req.body : sales_token, feedback_groupid,
     Response : name and value from master_lookup table,

*/

module.exports.getFeedbackReasons = (req, res) => {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               if (typeof data.sales_token != 'undefined' && data.sales_token != '') {
                    feedbackModel.checkSalesToken(data.sales_token).then(checkSalesToken => {
                         let feedback_groupid = data.feedback_groupid;
                         if (checkSalesToken > 0) {
                              if (feedback_groupid != '' && typeof feedback_groupid != 'undefined') {
                                   feedbackModel.getFeedbackReasons(feedback_groupid).then(response => {
                                        res.status(200).json({ 'status': 'success', 'message': 'getFeedbackReasons', 'data': response });
                                   }).catch(err => {
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch, 'data': [] });
                                   })
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] });
                              }
                         } else {
                              res.status(200).json({ "status": 'session', 'message': cpMessage.invalidToken, 'data': [] })
                         }
                    }).catch(err => {
                         res.status(200).json({ "status": 'failed', 'message': cpMessage.invalidToken, 'data': [] })
                    })
               } else {
                    res.status(200).json({ "status": 'failed', 'message': cpMessage.tokenNotPassed, 'data': [] })
               }
          } else {
               res.status(200).json({ "status": 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] })
          }
     } catch (err) {
          res.staus(500).json({ 'status': 'failed', 'message': cpMessage.serverError, 'data': [] })
     }
}

/*
     Purpose : OrderReviewRating function is used to keep track of users Reveiw and rating after placing an order.,
     author : Deepak tiwari,
     Req.body : Flag, user_id ,Review, Rating.
     Response : Will store user review and rating in table and return status.,

*/
module.exports.appReviewRating = (req, res) => {
     try {
          if (typeof req.body.data != 'undefined' && req.body.data != '') {
               let data = JSON.parse(req.body.data);
               if (typeof data.flag != 'undefined' && data.flag != '') {
                    let userId = (typeof data.user_id != 'undefined' && data.user_id != '') ? data.user_id : 0;
                    let Review = (typeof data.review != 'undefined' && data.review != '') ? JSON.stringify(data.review) : null;
                    let Rating = (typeof data.rating != 'undefined' && data.rating != '') ? data.rating : 0;
                    if (data.flag == 1) {
                         //In this case we need to check weather user have rated or not.
                         feedbackModel.getReviewRating(userId).then(ratingDetail => {
                              console.log("----64-----", ratingDetail)
                              if (ratingDetail > 0) {
                                   res.status(200).json({ "status": 'success', 'message': 'User already rated!', 'data': { 'rating': 1 } });
                              } else {
                                   res.status(200).json({ "status": 'failed', 'message': 'Not yet rated.', 'data': { 'rating': 0 } });
                              }
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ "status": 'failed', 'message': cpMessage.internalCatch });
                         })

                    } else if (data.flag == 2) {
                         //In this case we have to store user review and rating in tables.
                         feedbackModel.storeReviewRating(userId, Review, Rating).then(ratingDetail => {
                              if (ratingDetail) {
                                   res.status(200).json({ "status": 'success', 'message': 'Thank you for providing your valuable feedback.' });
                              } else {
                                   res.status(200).json({ "status": 'failed', 'message': cpMessage.UnsuccessfulOperation });
                              }
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ "status": 'failed', 'message': cpMessage.internalCatch });
                         })
                    } else {
                         res.status(200).json({ "status": 'failed', 'message': cpMessage.invalidRequestBody });
                    }
               } else {
                    res.status(200).json({ "status": 'failed', 'message': cpMessage.invalidRequestBody });
               }
          } else {
               res.status(200).json({ "status": 'failed', 'message': cpMessage.invalidRequestBody })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}




