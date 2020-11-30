'user strict';

const Sequelize = require('sequelize');
var TransactionModel = require('../schema/transactions');
var sequelize = require('../../config/sequelize');

const Transaction = TransactionModel(sequelize, Sequelize)

module.exports = {
    getStatusById: function (transactionId) {
        //   console.log('sd'+Transaction);
        //  Transaction.findAll().then(transactions => res.json(transactions))
        Transaction.findAll().then(function (data) {
            console.log(data);
        });
    }
}
