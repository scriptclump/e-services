/**
 * User.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
var dateFormat = require('moment-timezone');
const db = require('../../dbConnection');

module.exports = {
    checkCustomerToken: function (token, callback) {
        var sql = "SELECT count(u.user_id) as count FROM users as u where u.password_token = ? OR u.lp_token = ? ";
        db.query(sql, [token, token], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    getUserInfoByToken: function (token, callback) {
        var sql = "SELECT user_id,GetUserName(u.user_id,2) as user_name,email_id,mobile_no,legal_entity_id,otp,emp_code,is_active FROM users as u where u.password_token = ? OR u.lp_token = ? OR u.chat_token = ? ";
        db.query(sql, [token, token, token], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    }
};

