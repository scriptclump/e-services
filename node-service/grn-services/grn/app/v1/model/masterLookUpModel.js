const Sequelize = require('sequelize');
var sequelize = require('../../config/sequelize');
var express = require('express');
var router = express.Router();

var auth = require('../middleware/auth');
var database = require('../../config/mysqldb');
let db = database.DB;
let con = db;


module.exports.getAllOrderStatus = async (catName, isActive = (1)) => {
    try {
        return new Promise((resolve, reject) => {
            let query = `SELECT master_lookup.master_lookup_name AS NAME,master_lookup.value FROM master_lookup
            JOIN master_lookup_categories ON master_lookup_categories.mas_cat_id = master_lookup.mas_cat_id
            WHERE master_lookup_categories.mas_cat_name = '${catName}' and master_lookup.is_active = ${isActive};`

            sequelize.query(query, { type: Sequelize.QueryTypes.SELECT }).then(response => {
                // console.log(response.length);
                // console.log(response);
                let orderStatusArr = [];
                let final = [];
                if (response.length > 0) {
                    response.forEach(data => {
                        let result = {};
                        result[data.value] = data.NAME;
                        orderStatusArr.push(result);
                    })
                }
                resolve(orderStatusArr);
            })
        })
    } catch (err) {
        console.log(err);
        reject(err);
    }
}