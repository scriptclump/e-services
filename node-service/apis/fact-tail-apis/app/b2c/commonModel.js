'use strict';

const Sequelize = require('sequelize');
const database = require('../config/mysqldb');
const db = database.DB;


module.exports.getUserName = async (userId) => {
    try{
        return new Promise((resolve,reject) => {
            let query = `SELECT firstname,lastname FROM users WHERE user_id = ${userId};`
            // console.log(query);
            db.query(query, (err,data) => {
                if(err){
                    reject("User not found");
                } 
                // console.log("username", data);
                let fullName = data[0].firstname + data[0].lastname;
                resolve(fullName);
            })
        })
    } catch(err){

    }
};