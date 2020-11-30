/**
 * BanksController
 *
 * @description :: Server-side logic for managing Banks
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
const Banks = require('../models/Banks');

module.exports = {
	index: function (req, res) {
		return res.send('its working');
	},
	getBankDetailByIFSC: function(req, res){
		var data = req.body;
				
		var apiKey = data.api_key;
		var secretKey = data.secret_key;
		var token = data.token;
		var ifsc = data.ifsc;

		if(apiKey == '' || ifsc == ''){
			return res.ok({Status: 404 , Message: "Missing Token or IFSC."});
		}

		if(data.api_key && data.secret_key){
			Banks.verifyApiKey(apiKey, secretKey, 'getBankDetailByIFSC', function(result){
				if(result[0].total == '0') {
					return res.ok({Status: 404 , Message:"Failed API Authentication."});
				}
			});
		}

		Banks.verifyToken(token, function(result) {
			if(result.length == '0') {
				return res.ok({Status: 404, Message:"Verify Your User Token."});
			}		
			
			if(result.length > '0') {
				Banks.getBankDetailByIFSC(ifsc, function(result){
			
					if(result.length == '0') {
						return res.send({Status: 200 , Message:"No Record Found."});
					}
					if(result.length > 0) {
						return res.send({Status:200, Message:"Success", Data:result});
					}	

				});
			}
			else {
			 	return res.send({Status:404, Message:"Something went wrong"});
			}
		});
	}
	
};

