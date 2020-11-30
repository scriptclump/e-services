'use strict';

var Transaction = require('../model/paymentModel.js');

/**
 * Connnect to the payment gateway for the payment
 */
exports.createTransaction = function (req, res) {
    var new_transaction = new Transaction(req.body); 
    Transaction.createTransaction(new_transaction, function (err, transaction) {
        if (err){
           return res.send(err);
        }            
        res.json(transaction);
    });
};


/**
 * Get the status of the transaction from database
 * @param int transactionId Transaction referance address
 * @returns MixedArray All the fields of the array
 */
exports.getStatus = function (req, res) {
    Transaction.getStatusById(req.params.transactionId, function (err, transaction) {
        if (err){
            return res.send(err);
        }
        console.log('Result:', transaction);
        res.send(transaction);
    });
};

// sendDataToPaymentGateway = function (req, res) {
//     // Connect to the payment gateway
// };