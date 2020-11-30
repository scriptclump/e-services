/**
 * UpiEbutorController
 *
 * @description :: Server-side logic for managing Upiebutors
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
var MongoClient = require('mongodb').MongoClient;
const config = require('../../config/config.json');
const upi = require('../../config/upi');
const fs= require('fs');
module.exports = {

	index: function (req, res) {
		console.log(sails.config.baseurl.url);
		return res.send('Ebutor UPI App Working On this Link !');
	}, 

	UpiApp: function(req, res){
		var test = [1,2,3,4,5];
		var response = req.body;
		var data = JSON.parse(response.data);
		console.log(data);
		// for (var i = 0; i < response.break.length; i++){
		// 	return res.json(500, { error: 'break point is '+response.break.length });
		// };
		return res.json(200, {'request': data});
	},
	CollectionRequest: function(req, res){
		var crypto = require("crypto");
		var constants = require("constants");
		var _id = module.exports.makeid();
		/**
		 * [publickey RSA 2048 public key for check sum]
		 * @type {String}
		 */
		if (config['url'] == 'https://api.ebutor.com') {
			var upi_data = upi.upi_prod;
			var publickey = fs.readFileSync('../../config/cert/public_key.pem');
			var url = upi_data.url+'/WebPaymentS2S/Merchant/MerchantToken';
		}else{
			var upi_data = upi.upi_dev;
			var publickey = fs.readFileSync('../../config/cert/PING_ENCRYPTKEY2.pem');
			var url = 'https://upiuat.axisbank.co.in/WebPaymentS2S/Merchant/MerchantToken';
		}
		
		var timestamp = Date.now();
		var values = req.body;
		var merchId = upi_data.MerchId;
		var merchChanId = upi_data.MerchChanId;
		var unqTxnId = _id+timestamp;
		var unqCustId = '919773187515';
		var amount = values.amount;
		var txnDtl = 'GRT';
		var currency = 'INR';
		var orderId = values.orderId;
		var customerVpa = values.customerVpa;
		var expiry = '60';
		var sId = '123';
		var print = merchId+merchChanId+unqTxnId+unqCustId+amount+txnDtl+currency+orderId+customerVpa+expiry+sId;

		/**
		 * [checksum description]
		 * @type {[type]}
		 */
		var bufferToEncrypt = new Buffer(print);
		var encrypted = crypto.publicEncrypt(
		    {"key" : publickey, padding : constants.RSA_PKCS1_PADDING},
		    bufferToEncrypt);
		var msg = encrypted.toString("hex");
		
		console.log('encrypted', msg, '\n');
		var body =  
				{ 	
					merchId : merchId,
					merchChanId : merchChanId,
					unqTxnId : unqTxnId,
					unqCustId : unqCustId,
					amount : amount,
					txnDtl : txnDtl,
					currency : currency,
					orderId : orderId,
					customerVpa : customerVpa,
					expiry : expiry,
					sId : sId,
					checkSum : msg 
			 	};
		var saveRequest = JSON.stringify(body);
		saveRequest = JSON.parse(saveRequest);
		saveRequest.ResponseUpi = {};
		saveRequest.status = 'pending';
		delete saveRequest.checkSum;
		var host = 'mongodb://'+config['mongo_host']+":"+config['mongo_port']+"/"+config['mongo_database'];
  		MongoClient.connect(host, function (err, db) {
            var Upi_request = db.collection('upi_request');
            if (err) throw new Error(err);
			Upi_request.create(saveRequest)
					.exec(function(err, result){ 
					   console.log({'id':result.id});
					});
		});

		module.exports.GetToken(url , body, function(data) {
			console.log(data);
			if (data == 'undefined') {
				var errMsg = {
						"code": "404",
						"result": "ERROR",
						"data": "No response from AxisUpi !"
					};
				return res.send();
			}else{
				if (data.code == 000 && data.result) {
					var request = require("request");
					if (config['url'] == 'https://api.ebutor.com') {
						var options = { method: 'POST',
						  	url: 'https://pingupi.axisbank.co.in/WebPaymentS2S/Merchant/requestCollect/'+data.data,
							headers: 
							{ 
								'cache-control': 'no-cache'
							} 
						};
					}else{
						var options = { method: 'POST',
						  	url: 'https://upiuat.axisbank.co.in/WebPaymentS2S/Merchant/requestCollect/'+data.data,
							headers: 
							{ 
								'cache-control': 'no-cache'
							} 
						};
					}
					
					console.log(options);

					request(options, function (error, response, upiResponse) {
						if (error) throw new Error(error);

						var rData = JSON.parse(JSON.stringify(upiResponse));
						var rmerchTranId = JSON.parse(rData);
						var storeResponse = rData;
						rmerchTranId = JSON.parse(JSON.stringify(rmerchTranId.data));
						rmerchTranId = rmerchTranId.merchTranId;
						var host = 'mongodb://'+config['mongo_host']+":"+config['mongo_port']+"/"+config['mongo_database'];
			      		MongoClient.connect(host, function (err, db) {
			                var Upi_request = db.collection('upi_request');
			                if (err) throw new Error(err);
							Upi_request.update({ unqTxnId : rmerchTranId},{ ResponseUpi : JSON.parse(storeResponse)})
							.exec(function(err, result){ 
							   console.log(result);
							});
							return res.send(upiResponse);
						});
					});
				}else{
					return res.send(data);
				}
			}
		});		
	},
	checksum: function(str) {
		/**
		 * [checksum description]
		 * @type {[type]}
		 */
		if (config['url'] == 'https://api.ebutor.com') {
			var publickey = fs.readFileSync('../../config/cert/public_key.pem');
		}else{
			var publickey = fs.readFileSync('../../config/cert/PING_ENCRYPTKEY2.pem');
		}
		var bufferToEncrypt = new Buffer(str);
		var encrypted = sails.crypto.publicEncrypt(
		    {"key" : publickey, padding : sails.constants.RSA_PKCS1_PADDING},
		    bufferToEncrypt);
		var msg = encrypted.toString("hex");
		return msg;
	},

	GetToken: function(url, data, callback){

		var request = require("request");
		var options = { 
			method: 'POST',
			url: url,
			headers: 
				{ 
					'content-type': 'application/json' 
				},
			body: data,
			json: true 
		};
		request.post(options
		    , function(error, response, body) {
		        if (error) {
		            console.log(error);
		        }
		        else {
		            callback(body);
		        }
		    });
	},
	/**
	 * [CollectionStatus description]
	 * @param {[json]} req  formData: { unqTxnId: 'SA95UZdplklsfssdjkklsjfstrurig3yua707k124dyQh31486560006221' }
	 * @param {[type]} res [description]
	 */
	CollectionStatus: function (req,res) {
		if (config['url'] == 'https://api.ebutor.com') {
			var upi_data = upi.upi_prod;
			var url = upi_data.url+'/WebPaymentS2S/Merchant/status';
		}
		else{
			var upi_data = upi.upi_dev;
			var url = 'https://upiuat.axisbank.co.in/WebPaymentS2S/Merchant/status';
		}
		console.log(upi_data);
		var reqData = req.body;
		var merchId = upi_data.MerchId;
		var merchChanId = upi_data.MerchChanId;
		var unqTxnId = reqData.unqTxnId;
		var mkChecksum = merchId+merchChanId+unqTxnId;
		var checksum = module.exports.checksum(mkChecksum);
		var postData = {
			"merchId":merchId,
			"merchChanId":merchChanId,
			"unqTxnId":unqTxnId,
			"checkSum": checksum
		}
		var request = require("request");


		var options = { 
			method: 'POST',
		  	url: url,
			headers: 
			{ 
				'cache-control': 'no-cache',
				'content-type': 'application/json'
			},
			body: postData,
			json: true
		};
		console.log(options);

		request(options, function (error, response, upiCollectionStatus) {
		  if (error) throw new Error(error);

		  	console.log(upiCollectionStatus);
		  	var host = 'mongodb://'+config['mongo_host']+":"+config['mongo_port']+"/"+config['mongo_database'];
      		MongoClient.connect(host, function (err, db) {
                var Upi_request = db.collection('upi_request');
                if (err) throw new Error(err);

				Upi_request.update({ unqTxnId : upiCollectionStatus.data.merchTranId},{ status : upiCollectionStatus.result})
						.exec(function(err, result){ 
						   console.log(result);
						   // if (result != []) {
						   // 		//GdsOrderPayment.updateGdsPayment(result[0].orderId, result[0].unqTxnId);
						   // }
						});
				return res.send(upiCollectionStatus);
			});		  
		});
	},
	/**
	 * [CollectionRefund description]
	 * @param {[type]} req { unqTxnId: 'SA95UZdplklsfssdjkklsjfstrurig3yua707k124dyQh31486560006221',
     txnRefundAmount: '2.00',
     refundReason: 'Not interested' }
	 * @param {[type]} res [description]
	 */
	CollectionRefund: function(req,res){
		if (config['url'] == 'https://api.ebutor.com') {
			var upi_data = sails.config.upi.upi_prod;
			var url = upi_data.url+'/WebPaymentS2S/Merchant/refund';
		}else{
			var upi_data = sails.config.upi.upi_dev;
			var url = 'https://upiuat.axisbank.co.in/WebPaymentS2S/Merchant/refund';
		}
		var reqData = req.body;
		var timestamp = Date.now();
		var merchId = upi_data.MerchId;
		var merchChanId = upi_data.MerchChanId;
		var unqTxnId = reqData.unqTxnId;
		var mobNo = "919773187515";
		var txnRefundAmount = reqData.txnRefundAmount;
		var txnRefundId = Date.now();
		var refundReason = reqData.refundReason;
		var sId = "123";
		var mkChecksum = merchId+merchChanId+txnRefundId+mobNo+txnRefundAmount+unqTxnId+refundReason+sId;
		console.log(mkChecksum);
		var checksum = module.exports.checksum(mkChecksum);
		var postData = {
			"merchId":merchId,
			"merchChanId":merchChanId,
			"unqTxnId":unqTxnId,
			"mobNo":mobNo,
			"txnRefundAmount":txnRefundAmount,
			"txnRefundId":txnRefundId,
			"refundReason":refundReason,
			"sId":sId,
			"checkSum":checksum
		}
		var request = require("request");

		var options = { 
			method: 'POST',
		  	url: url,
			headers: 
			{ 
				'cache-control': 'no-cache',
				'content-type': 'application/json'
			},
			body: postData,
			json: true
		};
		console.log(options);

		request(options, function (error, response, upiCollectionStatus) {
		  if (error) throw new Error(error);

		  console.log(upiCollectionStatus);
		  return res.send(upiCollectionStatus);
		});
	},

	makeid:function()
	{
	    var text = "";
	    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	    for( var i=0; i < 5; i++ )
	        text += possible.charAt(Math.floor(Math.random() * possible.length));

	    return text;
	},
	listOfCollection:function(req,res){

		Upi_request.findOne({orderId : req.body.orderId, sort: 'createdAt DESC'}).exec(function (err, colletionRequests) {
				 if (err) return err; 
				return res.send(colletionRequests);
		    });
	},
	listOfCollectionByDate:function(req,res){

		var moment = require('moment');
		console.log(req.body);
		var start,end,page;
		//console.log({start: new Date(req.body.startDate), end : new Date(req.body.endDate)});
		if(req.body.startDate){

			start = new Date(req.body.startDate);
			start.setHours(0,0,0,0);
		
		}else{
		
			start = moment().startOf('day').toISOString();
		
		}
		
		if(req.body.endDate){

			end = new Date(req.body.endDate);
			end.setHours(23,59,59,999);
		
		}else{

			end = moment().endOf('day').add(1, 'days').toISOString();
		}

		if(req.body.page){
		
			pageNo = Number(req.body.page)
		
		}else{

			pageNo = 1;
		}

		var limit = 100;
		var skip = ( pageNo - 1 ) * limit;
		var host = 'mongodb://'+config['mongo_host']+":"+config['mongo_port']+"/"+config['mongo_database'];
  		MongoClient.connect(host, function (err, db) {
            var Upi_request = db.collection('upi_request');
            if (err) throw new Error(err);

			Upi_request.find({ 
					createdAt : { 
						">=" : new Date(start), "<" : new Date(end) 
					},
					sort:'createdAt DESC',
					limit: limit, 
					skip: skip

				})
			.exec(function (err, Orders) {
		            if (err){
		                return res.send(err);
		            }else{
						return res.json(200,Orders);
		            }
		        });
		});
	}
};
