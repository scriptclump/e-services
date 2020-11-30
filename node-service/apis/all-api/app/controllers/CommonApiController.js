/**
 * comomoapicontroller
 *
 * @description :: logic for self signup all cutomers
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
const CommonApi = require('../models/CommonApi');

module.exports = {
	Stockistdashboard:function(req,res){
	var data = req.body.data;
		if(!data){
			var response = {"status":400,"Message":"Bad Request","Response Body":"Invalid JSON Format"}
			return res.json(response);
		}else{
			CommonApi.getStockistDashboardData(data,function(response){

				//console.log("ressssp",response);
				if(response == "No data found"){
				var response1 = {"status":"success","Message":"No data found","data":"No data"}
				res.send(response1);	
				}else{
				var b = response.replace(/"/g, '\'');
				var response1 = {"status":"success","Message":"data found","data":b}
				res.send(response1);
				}

			});
		}
	},

	Stockistdetails:function(req,res){
		var data = req.body.data;
		if(!data){
			var response = {"status":400,"Message":"Bad Request","Response Body":"Invalid JSON Format"}
			return res.json(response);
		}else{
			CommonApi.getStockistDetailsdata(data,function(response){
				res.send(response);
			});
		}
	},

	hrmsdashboard:function(req,res){
		var data = req.body.data;
		if(!data){
			var response = {"status":400,"Message":"Bad Request","Response Body":"Invalid JSON Format"}
			return res.json(response);
		}else{
			CommonApi.getHrmsDashboarddata(data,function(response){
				var b = response.replace(/"/g, '\'');
				if(response === "No data found"){
				var response1 = {"status":"success","Message":"No data found","data":b}
				res.send(response1);	
				}else{
				var response1 = {"status":"success","Message":"data found","data":b}
				res.send(response1);
				}
			});
		}
	},

}; 