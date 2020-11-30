const factailModel = require('../model/factailModel');
const categoryModel = require('../../categoryModule/model/categoryModel');
const cpMessage = require('../../../config/cpMessage');
var cache = require('../../../config/redis');//rediscache connection file 
const _ = require('underscore');//used to get specific feild from an array

/*
Purpose : homescreenDetails function is used to handle the request of getting the catg/brands/manfs related data.
author : Aaradhya
Request : Require le_wh_id
Response : Returns productDetails,
*/
module.exports.homescreenDetails = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let le_wh_id;
               let offset = 0;let offset_limit = 5;
               if (typeof data.le_wh_id != 'undefined' && data.le_wh_id != '') {
                    le_wh_id = data.le_wh_id;
                    let segment_id = 48001;
                    let brand = 0;let manufacture = 0;
                    let state;
                    state = "select state from legalentity_warehouses where le_wh_id =" + le_wh_id ;
                    factailModel.getState(le_wh_id).then((response) => {
                         state = response;
                         factailModel.getDetails(le_wh_id,segment_id,offset_limit,offset,data.customer_type,manufacture,brand,state).then((result) => {
                              res.status(200).json({'data': result });
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': 'failed', 'message': 'Error in getdetails' })
                         })
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': 'failed', 'message': 'State is not defined' })
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyLeWhId })
               }
          } else {
               res.status(200).json({ 'status': "failed", 'message': cpMessage.invalidRequestBody });
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': "failed", 'message': cpMessage.serverError });
     }

}