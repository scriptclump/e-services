

const trackingModel = require('../../TrackingController/model/trackingModel');
const cpMessage = require('../../../config/cpMessage');
/*
* Description:Check the pincodes in serviceable areas.
*/
module.exports.CheckPincode = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let result;
               trackingModel.getWarehouseid(data.pincode, true).then(CheckPincode => {
                    if (CheckPincode == '') {
                         result = "false";
                         res.status(200).json({ 'status': 'failed', 'message': "Non serviceable region", 'data': result })
                    } else {
                         let response = { 'serviceable': "true", 'le_wh_id': CheckPincode.le_wh_id, 'legal_entity_id': CheckPincode.legal_entity_id };
                         res.status(200).json({ 'status': 'success', 'message': 'Serviceable', 'data': response })
                    }
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
               })

          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody })
          }

     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}

