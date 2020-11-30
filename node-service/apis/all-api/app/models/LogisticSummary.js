/**
 * LogisticSummary.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
const db = require('../../dbConnection');

module.exports = {
  	ReportByType:function(dc_id, hub_id, from_date, to_date, report_type, callback){
  		if(report_type == "picking_summary")
  		{
  			db.query("call getKPIPickingSummaryReport(?,?,?,?)",[dc_id,hub_id,from_date,to_date], function(err, results) {
		      	if (err) {
		      		console.log(err);
	                return err;
	            } 
	            callback(results[0]);
			});
  		}else if(report_type == "inventory_dc_report")
  		{
  			db.query("call getKPIInventoryDCReport(?,?,?)",[dc_id,hub_id,from_date,to_date], function(err, results) {
		      	if (err) {
		      		console.log(err);
	                return err;
	            } 
	            callback(results[0]);
			});
  		}else
  		{
  			callback("report type mismatch");
  		}
  	}
};

