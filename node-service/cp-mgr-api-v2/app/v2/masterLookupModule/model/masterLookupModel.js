var sequelize = require('../../../config/sequelize');//sequlize connection file
const Sequelize = require('sequelize');
const mongoose = require('mongoose');
const user = mongoose.model('User');
const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;

module.exports.getCashbackHistory = (user_id) => {
    return new Promise((resolve, reject) => {
        try {
            // let query = "select getOrderCode(order_id) as order_code, order_id, cash_back_amount, getMastLookupValue(transaction_type) as cashback_type, DATE_FORMAT(transaction_date, '%Y-%m-%d %H:%i:%s') as transaction_date from ecash_transaction_history where user_id ="+ user_id +" Order By transaction_date DESC";
            let query = "select getOrderCode(order_id) as order_code, order_id, cash_back_amount, getMastLookupValue(transaction_type) as cashback_type, DATE_FORMAT(CONVERT_TZ(`transaction_date`,'+00:00',@@global.time_zone), '%Y-%m-%d %H:%i:%s') as transaction_date from ecash_transaction_history where user_id =" + user_id + " Order By transaction_date DESC";
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                let result = JSON.parse(JSON.stringify(response))
                resolve(result);

            }).catch(err => {
                reject(err);
            })

        } catch (err) {
            reject(err);
        }
    })

}

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
            } else if (response > 0) {
                resolve(response)
            } else {
                resolve(count);
            }
        })


        // let data = "select count(user_id) as counts FROM users WHERE password_token =" + string;
        // db.query(data, {}, function (err, rows) {
        //      if (err) {
        //           return reject(err);
        //      }
        //      if (Object.keys(rows).length > 0) {
        //           return resolve(rows[0].counts);
        //      }
        //      else {
        //           return reject("No mapping found..")
        //      }
        //      // db.release()
        // });
    });
}


module.exports.getRmData = function (user_id, hub_id) {
    return new Promise((resolve, reject) => {
        try {
            let query = `select u.user_id,u.firstname,u.lastname,u.email_id,u.mobile_no from 
            users u JOIN legalentity_warehouses l ON u.legal_entity_id = l.legal_entity_id
            JOIN user_roles ur ON u.user_id = ur.user_id
            JOIN roles r ON r.role_id = ur.role_id
            WHERE u.reporting_manager_id = `+ user_id + `
            AND l.le_wh_id=` + hub_id + `
            AND r.short_code IN ("SSLO","SSLA")
            AND u.is_active =1 group by u.user_id`;
            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(rows => {
                let result = JSON.parse(JSON.stringify(rows));
                resolve(result);
            }).catch(err => {
                reject(err);
            })
        } catch (err) {
            reject(err);
        }
    })
}


/*
Purpose :getMasterLookupValues function used to get masterlookup  From masterlookup table  .
author : Deepak Tiwari
Request : Require mas_cat_id.
Resposne : return masterlook up value .
*/
module.exports.getMasterLookupValues = function (mas_cat_id) {
    try {
        return new Promise((resolve, reject) => {
            let value = "select ml.master_lookup_name as name ,ml.value,ml.description from master_lookup as ml where ml.mas_cat_id =" + mas_cat_id + "&& ml.is_active = 1 ORDER BY ml.sort_order ASC";
            db.query(value, {}, function (err, master_lookup) {
                if (err) {
                    return resolve(err);
                } else if (Object.keys(master_lookup).length > 0) {
                    return resolve(master_lookup);
                }

            })

        })

    } catch (err) {
        console.log(err);
    }

}
