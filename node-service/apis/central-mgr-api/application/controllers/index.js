/*
 * 
 * @package Node.js
 * @version 1.0
 * @author Rohit <rohit.singh@ebutor.com>
 * @Date 20-07-2016
 */

//index function
var unirest = require('unirest');

var indexController = {
    index:function(req, res){
        res.json(sleekConfig.badRequest);
    },

    callDelTest:function(req, res){
    	console.log("Requesting for Delhivery quey");
    	unirest.get("http://localhost:3001/calldeltest?queid="+req.query.queid)
                .headers({'Content-Type': 'application/json'})
                .end(function (response) {
                	console.log("Received the response successfully.");
                    res.json(response);
                });
    }
};


module.exports = indexController;