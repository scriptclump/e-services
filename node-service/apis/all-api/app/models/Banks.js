/**
 * SalesOrder.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

var dateFormat = require('moment-timezone');
const db = require('../../dbConnection');

module.exports = {  
  getBankDetailByIFSC: function(ifsc, callback)
	{
		var sql = "select bank_id, bank_name,ifsc,micr,branch, address,contact_phone,"
		+"city,district,state from bank_info where ifsc = ?";
		
		db.query(sql, [ifsc], function(err, result){
		
			if(err){ 
				console.log(err);
				return err;
			}
			callback(result);
		});
	},
	
	verifyToken: function(token, callback) {
		
		var sql = "select user_id from users where password_token = ? or lp_token= ? limit 1";
		
		db.query(sql, [token,token], function(err, result){
			
			if(err){ 
				console.log(err);
				return err;
			}
			
			callback(result);
		});
		
	},
	verifyApiKey: function(apiKey, secretKey, apiName, callback) {
		
		var sql = "SELECT COUNT(api_session.api_id) as total FROM api_session" 
					+" JOIN api_role_mfg ON api_role_mfg.api_role_mfgid = api_session.role_id"
					+" JOIN api_role_mfgassign ON api_role_mfgassign.api_role_mfgasid = api_role_mfg.api_role_mfgid"
					+" JOIN api_features ON api_features.api_fid = api_role_mfgassign.api_fid"
					+" WHERE api_key = ? "
					+" AND secret_key = ? " 
					+" AND api_features.feature_name = ?"
					+" AND api_status = 1";
		
		db.query(sql, [apiKey, secretKey, apiName], function(err, result){
			
			if(err){ 
				console.log(err);
				sails.log(err);
				return err;
			}
			callback(result);
		});
		
	}
};

