/**
 * LogisticSummaryController
 *
 * @description :: Server-side logic for managing Logisticsummaries
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
const LogisticSummary = require('../models/LogisticSummary');

module.exports = {
	


  /**
   * `LogisticSummaryController.index()`
   */
  index: function (req, res) {
    return res.json({
      todo: 'index() is not implemented yet!'
    });
  },
  Reports: function(req, res){
  		if (!req.body.data) {
            response = {"Status": 400,"Message": "Bad Request","ResponseBody": "Invalid JSON Format"};
            return res.json(response);
        } 
        else
        {
        	var data = req.body.data;
          var from_date = (data.from_date)?data.from_date:'';
          var to_date = (data.to_date)?data.to_date:'';
          var dc_id = (data.dc_id)?data.dc_id:'';
          var hub_id = (data.hub_id)?data.hub_id:null;
          var report_type = (data.report_type)?data.report_type:'';
          if(from_date!='' && to_date!='' && dc_id!='' && report_type!=""){
          	LogisticSummary.ReportByType(dc_id,hub_id,from_date,to_date,report_type,function (result) {
                  if (result == null || result.length == 0) {
                      var response = '{"Status":200, "Message":"success", "ResponseBody":"No data found!"}';
                  } else {
                      var response = '{"Status":200, "Message":"success", "ResponseBody":' + JSON.stringify(result) + '}';
                  }
                  res.send(response);
                  return true;
              });
        	}else{
        		response = {"Status": 201, "Message": "Invalid Data Request", "ResponseBody": "Please pass report type, from date, to date and hub name"};
           		return res.json(response);
        	}
        }
  	}
};

