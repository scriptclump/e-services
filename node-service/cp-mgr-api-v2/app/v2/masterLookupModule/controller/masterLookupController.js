let masterLookupModel = require('../model/masterLookupModel');
let cpMessage = require('../../../config/cpMessage');

/* 
    Purpose : getCashbackHistory function is used to fetch the cashback history details from ecash_transaction_history table,
    author : Muzzamil,
    Req.body : customer_token, user_id,
    Response : getOrderCode(order_id) as order_code,order_id,cash_back_amount,getMastLookupValue(transaction_type) as cashback_type,transaction_date 
   sa 
*/

module.exports.getCashbackHistory = (req, res) => {
    try {
        if (typeof req.body.data != "undefined" && req.body.data != '') {
            let data = JSON.parse(req.body.data);
            if (typeof data.customer_token != "undefined" && data.customer_token != '') {
                masterLookupModel.checkCustomerToken(data.customer_token).then(customerToken => {
                    if (customerToken > 0) {
                        if (typeof data.user_id != 'undefined' && data.user_id != '') {
                            masterLookupModel.getCashbackHistory(data.user_id).then(response => {
                                res.status(200).json({ 'status': 'success', 'message': 'getCashbackHistory', 'data': response });
                            }).catch(err => {
                                res.status(200).json({ 'status': 'success', 'message': cpMessage.internalCatch, 'data': [] });
                            })
                        } else {
                            res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidCustomer, 'data': [] });
                        }
                    } else {
                        res.status(200).json({ 'status': 'session', 'message': cpMessage.invalidToken, 'data': [] })
                    }
                }).catch(err => {
                    res.status(200).json({ 'status': 'success', 'message': cpMessage.internalCatch, 'data': [] });
                })

            } else {
                res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed, 'data': [] });
            }
        } else {
            res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] });
        }

    } catch (err) {
        res.staus(500).json({ 'status': 'failed', 'message': cpMessage.serverError, 'data': [] });
    }
}

/* 
    Purpose : getFFbyHub function 
    author : Muzzamil,
    Req.body : user_id, hub_id
    Response : user_id, firstname, lastname, email_id, mobile_no. 
    
*/

module.exports.getFFByHub = (req, res) => {
    try {
        if (typeof req.body.data != "undefined" && req.body.data != '') {
            let data = JSON.parse(req.body.data);
            if (typeof data.user_id != 'undefined' && data.user_id != '' && typeof data.hub_id != 'undefined' && data.hub_id != '') {
                masterLookupModel.getRmData(data.user_id, data.hub_id).then(response => {
                    res.status(200).json({ 'status': 'success', 'message': 'getFFByHub', 'data': response });
                }).catch(err => {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch, 'data': [] });
                })
            } else {
                res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] })
            }

        } else {
            res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody, 'data': [] })
        }
    } catch (err) {
        res.staus(500).json({ 'status': 'failed', 'message': cpMessage.serverError, 'data': [] });
    }
}




/*
    Purpose : Used to get customer feedback for masterlookup value
    author : Deepak,
    Req.body : {}
    Response : 

*/

module.exports.getCustomerfeedback = (req, res) => {
    try {
        masterLookupModel.getMasterLookupValues(115).then(response => {
            res.status(200).json({ 'status': 'success', 'message': 'Customerfeedback', 'data': response });
        }).catch(err => {
            console.log(err);
            res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch })
        })
    } catch (err) {
        console.log(err);
        res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })

    }
}
