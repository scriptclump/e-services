const report = require('../models/reportmodel');
const db = require('../../dbConnection');
module.exports = {
	/**
	 * [getReportDetails To get master report data]
	 * @param  {[array]} req [Table name, columns list to retrieve, filter text to add in where condition]
	 * @param  {[Object]} res [Data]
	 */
	getReportDetails: function (req, res) {
		console.log('get basic');
		console.log(req.body);
		if(!req.body){
			response={
				"status":'400',
				"message":'Bad Request',
				"ResponseBody":"Invalid JSON"
			}
			return res.json(response);
		}else{
			var data=req.body;
			console.log(data);
			console.log(data.table);
		}
		//console.log("calling details("+type+","+code+")");
		report.getData(data,function(err,result){
			if(result == null || result.length == 0 || err){
				var response = '{"Status":500, "message":"success", "ResponseBody":"No data found!"}';
				return res.send(response);
			}
			var finalres='{"Status":200,"message":"success","ResponseBody":'+JSON.stringify(result)+'}'
			//console.log(finalres);
			return res.send(finalres);
		})

	}
}
