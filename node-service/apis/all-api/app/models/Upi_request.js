/**
 * Upi_request.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
var MongoClient = require('mongodb').MongoClient;

const config = require('../../config/config.json');

module.exports = {

  	ListCollections:function(orderId){

  	},
  	colletionRequestsByOrderId: function(orderId, cb) {
  		var host = 'mongodb://'+config['mongo_host']+":"+config['mongo_port']+"/"+config['mongo_database'];
      MongoClient.connect(host, function (err, db) {
          var Upi_request = db.collection('upi_request');
  		    Upi_request.findOne({orderId : orderId}).exec(function (err, colletionRequests) {
  				if (err) return cb(err);
  				if (!colletionRequests) return cb(new Error('User not found.'));
  				return cb(null, colletionRequests); 
  		    });
	    });
	  }

};

