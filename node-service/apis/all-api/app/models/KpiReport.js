/**
 * KpiReport.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
const db = require('../../dbConnection');

module.exports = {
  	ReportByType:function(db_call,request_params, callback){
      var request_params_size = request_params.length;
      var db_pass_params ="";
      for(var i=0; i<request_params_size;i++)
      {
        db_pass_params+= "?,";
      }
      var index = db_pass_params.lastIndexOf(",");
      db_pass_params = db_pass_params.substring(0, index) + db_pass_params.substring(index + 1);
			db.query("call "+db_call+"("+db_pass_params+")",request_params, function(err, results) {
	      	if (err) {
	      		 console.log(err);
              return err;
            } 
            callback(results[0]);
			});
  	}
};

