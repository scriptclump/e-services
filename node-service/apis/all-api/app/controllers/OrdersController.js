/**
 * OrdersController
 *
 * @description :: Server-side logic for managing Orders
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
const User = require('../models/User');

module.exports = {
	index: function (req, res) {
		return res.send('For orders under queue !!');
	},

	failed: function (req, res) {

		var moment = require('moment');
		var start = moment().startOf('day').toISOString();
		var end = moment().endOf('day').add(1, 'days').toISOString();
		
		dmapiRequests
		.find()
		.where(
			{ 
				"requestDateTime" : { 
					">=" : new Date(start), "<" : new Date(end) 
				},
				//"resquestData.api_key" : "cp_qc"
			}
		).exec(function (err, Orders) {
	            if (err)
	                return res.send(err)
	            return res.send(Orders)
	        });
	},

	FindUsers: function (req, res) {
		User.find().exec(
			function(err, Users) {
				console.log(err);
				return res.send(Users);
		});
	},
	
	NativeCall: function (req, res) {
		User.native(function(err, collection) {
		  if (err) return res.serverError(err);

		  collection.find({}, {
		    name: true
		  }).toArray(function (err, results) {
		    if (err) return res.serverError(err);
		    return res.ok(results);
		  });
		});
	},
	SocketMsg: function(req,res){
		if (req.method == 'POST') {
			var msg = req.body;
	        sails.sockets.blast('dashboard-channel', { data: msg});
	        res.setHeader('Content-Type', 'application/json');
			return res.json(200,{ data: msg, message: 'success', status: 200 });
		}else{
			return res.json(405,{ method: 'GET' , message: 'Method not allowed.', status: 0 });
		}		
	}
}; 