'user strict';

const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;
const Sequelize = require('sequelize');
const sequelize = require('../../../config/sequelize');
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
     // return new Promise((resolve, reject) => {
     //      let string = JSON.stringify(customer_token);
     //      let count = 0;
     //      let data = "select count(user_id) as counts FROM users WHERE password_token =" + string;
     //      db.query(data, {}, function (err, rows) {
     //           if (err) {
     //                return reject(err);
     //           }
     //           if (Object.keys(rows).length > 0) {
     //                return resolve(rows[0].counts);
     //           }
     //           else {
     //                return reject("No mapping found..")
     //           }
     //           // db.release()
     //      })
     // });
}




/*
Purpose : Used to save tracking details 
author : Deepak Tiwari
Request : Require latitude , longitude , user_id , token , speed 
Resposne : Will update geo details in geo_track table in db.
*/
module.exports.saveGeoData = function (data) {
     return new Promise((resolve, reject) => {
          try {
               let reading = typeof data.reading != 'undefined' ? data.reading : 0;
               let current_datetime = new Date();
               let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
               let query = " insert into geo_track (geo_type , user_id , latitude , longitude , route_id , heading , accuracy , speed , reading , created_at ) values (" + data.geo_type + ',' + data.user_id + ',' + data.latitude + ',' + data.longitude + ',' + data.route_id + ',' + data.heading + ',' + data.accuracy + ',' + data.speed + ',' + reading + ",'" + formatted_date + "')";
               sequelize.query(query).then(insert => {
                    let geo = insert[0].insertedId;
                    return resolve(geo);
               }).catch(err => {
                    console.log(err);
                    reject(err.message);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}


module.exports.getGeoDetails = function (user_id) {
     return new Promise((resolve, reject) => {
          try {
               let query = "select * from geo_track where user_id  =" + user_id + " order by created_at DESC";
               sequelize.query(query).then(geo => {
                    let geoDetails = JSON.parse(JSON.stringify(geo[0]));
                    console.log("geoDetails", geoDetails);
                    if (geoDetails != '') {
                         resolve(geoDetails[0]);
                    } else {
                         resolve('');
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