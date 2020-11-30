/**
 * PurchaseReturnController
 *
 * @description :: Server-side logic for managing PurchaseReturn related oprations
 * @help    ();    :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
//var _ = require('lodash');



var dateFormat = require('dateformat');
const PurchaseReturn = require('../models/PurchaseReturn');

module.exports = {
    index: function (req, res) {
        console.log(sails.config.baseurl.url);
        return res.send('Return APIs...');
    },
    getPRList: function (req, res)
    {
        //return res.ok('I am here ');
        console.log("Function: getPRList \n");
        if (!req.body.data) {
            response = {"Status": 400,"Message": "Bad Request","ResponseBody": "Invalid JSON Format"};
            return res.json(response);
        } else {
            var data = req.body.data;
            var type = data.type;
            var picker_id = data.picker_id;
            var status = (data.status)?data.status:'';
            if (data.user_token) {
                var user_token = data.user_token;
            } else {
                response = {"Status": 400, "Message": "Bad Request", "ResponseBody": "Please send user token"};
                return res.json(response);
            }
            var fdate = (data.fdate)?data.fdate:'';
            var tdate = (data.tdate)?data.tdate:'';
            var offset = (data.offset)?data.offset:0;
            var perpage = (data.perpage)?data.perpage:10;
        }
        PurchaseReturn.checkCustomerToken(user_token, function (tokenresult) {
            if (tokenresult[0].count == 0) {
                var response = '{"Status":201, "Message":"success", "ResponseBody":"Invalid user token."}';
                res.send(response);
                return true;
            } else {
                PurchaseReturn.getOpenPRList(status,type, picker_id,fdate,tdate,offset,perpage, function (result) {
                    if (result == null || result.length == 0) {
                        var response = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';
                    } else {
                        var response = '{"Status":201, "Message":"success", "ResponseBody":' + JSON.stringify(result) + '}';
                    }
                    res.send(response);
                    return true;
                });
            }            
        });
    },
    getPRDetails: function (req, res)
    {
        //return res.ok('I am here ');
        console.log("Function: getPRDetails \n");
        if (!req.body.data) {
            response = {"Status": 400,"Message": "Bad Request","ResponseBody": "Invalid JSON Format"};
            return res.json(response);
        } else {
            var data = req.body.data;
            var pr_id = data.pr_id;
            if (data.user_token) {
                var user_token = data.user_token;
            } else {
                response = {"Status": 400, "Message": "Bad Request", "ResponseBody": "Please send user token"};
                return res.json(response);
            }
        }
        PurchaseReturn.checkCustomerToken(user_token, function (tokenresult) {
            if (tokenresult[0].count == 0) {
                var response = '{"Status":201, "Message":"success", "ResponseBody":"Invalid user token."}';
                res.send(response);
                return true;
            } else {
                PurchaseReturn.getPRDetails(pr_id, function (result) {
                    if (result == null || result.length == 0) {
                        var response = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';                        
                    } else {
                        var response = '{"Status":201, "Message":"success", "ResponseBody":' + JSON.stringify(result) + '}';                        
                    }
                    res.send(response);
                    return true;
                });
            }            
        });
    },
    assignPickerToPR: function (req, res)
    {
        //return res.ok('I am here ');
        console.log("Function: assignPickerToPR \n");
        if (!req.body.data) {
            response = {"Status": 400,"Message": "Bad Request","ResponseBody": "Invalid JSON Format"};
            return res.json(response);
        } else {
            var data = req.body.data;
            var pr_id = data.pr_id;
            var picker_id = data.picker_id;
            if (data.user_token) {
                var user_token = data.user_token;
            } else {
                response = {"Status": 400, "Message": "Bad Request", "ResponseBody": "Please send user token"};
                return res.json(response);
            }
        }
        PurchaseReturn.checkCustomerToken(user_token, function (tokenresult) {
            if (tokenresult[0].count == 0) {
                var response = '{"Status":201, "Message":"success", "ResponseBody":"Invalid user token."}';
                res.send(response);
                return true;
            } else {
                PurchaseReturn.assignPickerToPR(pr_id, picker_id , function (result) {
                    var response = '{"Status":201, "Message":"success", "ResponseBody":Assigned successfully}';
                    res.send(response);
                    return true;
                });
            }            
        });
    }
};

