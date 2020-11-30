const sequelize = require('../../../config/sequelize');
const cpMessage = require('../../../config/cpMessage');
var moment = require('moment');
const mongoose = require('mongoose');
const user = mongoose.model('User');
const dbconnection = require('../../../config/mysql');
const db = dbconnection.DB;

/*
* Purpose: the function is used to  insert and update device details 
* author : Deepak Tiwari 
*/
module.exports.InsertDeviceDetails = function (user_id, device_id, ip_address, platform_id, reg_id) {
     try {
          return new Promise((resolve, reject) => {
               let date = moment().format("YYYY-MM-DDTHH:mm:ss");
               let data = "insert into device_details (user_id, device_id, app_id, ip_address, registration_id, platform_id, created_at, updated_at) values (" + user_id + ',' + "'" + device_id + "'" + ',' + 0 + ',' + "'" + ip_address + "','" + reg_id + "','" + platform_id + "','" + date + "','" + date + "') ON DUPLICATE KEY UPDATE  user_id = '" + user_id + "', ip_address = '" + ip_address + "', registration_id = '" + reg_id + "', platform_id = '" + platform_id + "', updated_at = '" + date + "'";
               sequelize.query(data).then(inserted => {
                    console.log("Device id inserted Successfully")
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          })
     } catch (err) {
          console.log(err)
     }
}


/*
* Purpose: the function is used to version 
* author : Deepak Tiwari
*/
module.exports.versioncheck = function (number, type) {
     return new Promise((resolve, reject) => {
          try {
               let query = "SELECT version_number AS number,app_type AS TYPE FROM app_version_info  WHERE (app_type ='" + type + "' && version_number >" + number + ")";
               sequelize.query(query).then(response => {
                    //console.log(query)
                    let result = response[0];
                    resolve(result);
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
Purpose : checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
author : Deepak Tiwari
Request : Require customer_token
Resposne : Give access to the user to the application
*/
exports.checkCustomerToken = function (customer_token) {
     return new Promise((resolve, reject) => {
          // let string = JSON.stringify(customer_token);
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

/*
Purpose :getMasterLookupValues function used to get masterlookup  From masterlookup table  .
author : Deepak Tiwari
*/
module.exports.getMasterLookupValues = function (mas_cat_id) {
     try {
          return new Promise((resolve, reject) => {
               let value = "select ml.master_lookup_name as name ,ml.value,ml.description , ml.image as icon  from master_lookup as ml where ml.mas_cat_id =" + mas_cat_id + "&& ml.is_active = 1 ORDER BY ml.sort_order ASC";
               sequelize.query(value).then(response => {
                    let values = response[0];
                    resolve(values);
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          })

     } catch (err) {
          console.log(err);
     }

}

module.exports.getSortingDataFilter = function () {
     return new Promise((resolve, reject) => {
          try {
               let value = "select ml.master_lookup_id as id,ml.master_lookup_name as name,ml.value as sort_id from master_lookup as ml left join master_lookup_categories as mlc ON mlc.mas_cat_id = ml.mas_cat_id where ml.mas_cat_id = 65 && mlc.is_active = 1 ";
               sequelize.query(value).then(response => {
                    let values = response[0];
                    resolve(values);
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

module.exports.getUnBilledSkus = function (data) {
     return new Promise((resolve, reject) => {
          try {
               if (typeof data.start_date != 'undefined' && data.start_date != '') {
                    let start_date = data.start_date;
                    let end_date = data.end_date;
               } else {
                    let start_date = date('Y-m-d');
                    let end_date = date('Y-m-d');
               }

               let id = (typeof data.id != 'undefined' && data.id != '') ? data.id : 0;
               let beat_id = (typeof data.beat_id != 'undefined' && data.beat_id != '') ? data.beat_id : 0;
               let flag = (typeof data.flag != 'undefined' && data.flag != '') ? data.flag : 0;
               let sort_id = (typeof data.sort_id != 'undefined' && data.sort_id != '') ? data.sort_id : 0;
               let cust_type = (typeof data.customer_type != 'undefined' && data.customer_type != '') ? data.customer_type : 0;

               let value = "call getSKUSByffid_ByCust(" + id + "," + data.ff_id + "," + data.offset + "," + data.offset_limit + "," + sort_id + "," + data.is_billed + ",'" + data.start_date + "','" + data.end_date + "'," + flag + "," + beat_id + "," + cust_type + ")";
               sequelize.query(value).then(response => {
                    let final_result = {

                    };
                    if (typeof response[0].product_id != 'undefined' && response[0].product_id != '') {
                         final_result = { 'product_id': response[0].product_id, 'count': response[0].product_id.length };
                    } else {
                         final_result = { 'product_id': '', 'count': '' };
                    }
                    resolve(final_result);
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

/*Check Sales Token 
 The same code is already in FeedbackModel
 Later need to take this into Helper module.
 author : Muzzamil
*/
module.exports.checkSalesToken = function (token) {
     return new Promise((resolve, reject) => {
          try {
               let salesToken = JSON.stringify(token);
               let query = "select verifyToken(" + salesToken + ") as count"
               sequelize.query(query).then(rows => {
                    let result = JSON.parse(JSON.stringify(rows[0]));
                    resolve(result[0].count);
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