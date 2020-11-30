/**
 * MobileApiLogsController
 *
 * @description :: Server-side logic for managing Mobile API Logs related oprations
 * @help    ();    :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */

var dateFormat = require('dateformat');
const MobileApiLogs = require('../models/MobileApiLogs');

module.exports = {
    index: function (req, res) {
        console.log(sails.config.baseurl.url);
        return res.send('Mobile API Logs...');
    },
    insertMobileApiLog: function (req, res) {
        console.log("Log Started at " + dateFormat("yyyy-mm-dd, h:MM:ss TT") + "\n\nFunction (Controller): insertMobileApiLog");
        if (!req.body.data) {
            response = {
                Status: 400,
                Message: "Bad Request",
                ResponseBody: "Invalid Query"
            };
            return res.json(response);
        } else {
            var data = req.body.data;
            var orderId = data.gds_order_id;
            var moduleName = data.module;
            var details = data.details;
            console.log("\nReceived parameters are: \nModule Name: " + moduleName + "\nOrder: " + orderId + "\nDetails: " + details);
            MobileApiLogs.insertApiLogs(moduleName, orderId, details, function (result) {
                if (result.insertedId) {
                    response = {
                        Status: 200,
                        Message: "Success",
                        ResponseBody: "The log details inserted successfully."
                    };
                    return res.json(response);
                } else {
                    response = {
                        Status: 400,
                        Message: "Failed",
                        ResponseBody: "Something went wrong."
                    };
                    return res.json(response);
                }
            });            
        }
    }
};

