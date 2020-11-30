/**
 * Upi_request.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

module.exports = {

  attributes: {
  	ListCollections:function(orderId){

  	},
  	colletionRequestsByOrderId: function(orderId, cb) {

	    Upi_request.findOne({orderId : orderId}).exec(function (err, colletionRequests) {
				if (err) return cb(err);
				if (!colletionRequests) return cb(new Error('User not found.'));
				return cb(null, colletionRequests); 
		    });
	}

  }
};

