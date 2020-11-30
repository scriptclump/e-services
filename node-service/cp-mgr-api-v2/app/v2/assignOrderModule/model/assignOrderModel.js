'user strict';

const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;
const Sequelize = require('sequelize');
const sequelize = require('../../../config/sequelize');
const masterLookUpModel = require('../../schema/master_lookup');
const masterLookUp = masterLookUpModel(sequelize, Sequelize);
const mongoose = require('mongoose');
const user = mongoose.model('User');


/*
Purpose : checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Give access to the user to the application
*/
exports.checkCustomerToken = function (customer_token) {
     return new Promise((resolve, reject) => {
          let string = JSON.stringify(customer_token);
          let count = 0;
          user.countDocuments({ password_token: customer_token }, function (err, response) {
               if (err) {
                    console.log(err);
                    reject(err);
                    res.status(200).json({ success: false, message: "Unable to process your request please try later! " })
               } else if (response > 0) {
                    resolve(response)
               } else {
                    resolve(count)
               }
          })
     });
}


/*
Purpose : Function is used to get description from master_lookup table based on value
author : Deepak Tiwari
Request : Require value
Resposne : Returns description.
*/
module.exports.getMasterLookup = function (Mastervalue) {
     return new Promise((resolve, reject) => {
          try {
               let response = [];
               masterLookUp.findAll({ where: { value: Mastervalue }, attributes: ['master_lookup_id', 'description', 'value'] }).then(row => {
                    response.push(JSON.parse(JSON.stringify(row)));
                    if (response.length > 0) {
                         resolve(response[0])
                    } else {
                         resolve([]);
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

/*
Purpose : Function is used to get pending collection date
author : Deepak Tiwari
Request : Require value
Resposne : Returns description.
*/
module.exports.getPendingCollectionDate = function (userId) {
     return new Promise((resolve, reject) => {
          try {
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
               let query = "select MIN(DATE(collected_on)) as collected_on from collection_history left join remittance_mapping as mapping ON mapping.collection_id = collection_history.collection_id where collected_by =" + userId + " && mapping.remittance_id IS NULL";
               sequelize.query(query).then(res => {
                    let resultArr = JSON.parse(JSON.stringify(res[0]));
                    if (resultArr == '' || resultArr[0].collected_on == '') {
                         return resolve(formatted_date);
                    } else {
                         return resolve(resultArr[0].collected_on);
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
          }
     })
}

/*
Purpose : Function is used to get pending collection by hub Incharge
author : Deepak Tiwari
Request : Require userId, apprStatus
Resposne : Returns description.
*/
module.exports.getPendingCollectionHI = function (userId, apprStatus) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select COUNT(mapping.collection_id) as count from collection_history left join remittance_mapping as mapping ON mapping.collection_id = collection_history.collection_id left join  collection_remittance_history as rmhistory ON rmhistory.remittance_id = mapping.remittance_id left join collections ON collections.collection_id = collection_history.collection_id left join gds_orders ON gds_orders.gds_order_id = collections.gds_order_id left join gds_orders_payment ON  gds_orders_payment.gds_order_id =  gds_orders.gds_order_id where collected_by =" + userId + "  && rmhistory.approval_status IN (" + apprStatus + ")  && gds_orders_payment.payment_status_id = 32003  && gds_orders.order_status_id IN ( 17007 ,17023 ) ";
               sequelize.query(query).then(rows => {
                    let resultArr = JSON.parse(JSON.stringify(rows[0]));
                    if (rows.length > 0) {
                         if (typeof resultArr[0].count != 'undefined' && resultArr[0].count > 0) {
                              return resolve(0);
                         } else {
                              return resolve(1);
                         }
                    } else {
                         return resolve(0);
                    }
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          }
          catch (err) {
               console.log(err);
               reject(err);
          }
     })

}

