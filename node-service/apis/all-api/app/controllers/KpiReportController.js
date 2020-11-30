/**
 * KpiReportController
 *
 * @description :: Server-side logic for managing KpiReport
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
var dateFormat = require('dateformat');
var KpiTemplate = require("../models/KpiTemplate");
var KpiReport = require("../models/KpiReport");

module.exports = {
    /**
   * `KpiReportController.index()`
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
      var report_type = (data.report_type)?data.report_type:'';
      if(report_type != '')
      {
        KpiTemplate.TemplateData(report_type,function(result){
          if(result[0]['db_params'][0] == '' || result[0]['db_call'] =='' ) {
              response = {"Status": 201, "Message": "Invalid Data Request", "ResponseBody": "Unknown mongodb db call name and db parameters."};
              return res.json(response);
            }
            var db_params = result[0]['db_params'][0];
            var db_call_name = result[0]['db_call'];
            delete data['report_type'];
            var request_data = data;
            
            //var response_params_keys = Object.keys(data);
            //var response_params_value = Object.values(data);
            var response_params_keys = Object.getOwnPropertyNames(data);
            var response_params_value = [];
            for (var key in data){
                 response_params_value.push(data[key]);
            }

            //var db_params_keys = Object.keys(db_params);
            //var db_params_value = Object.values(db_params);
            var db_params_keys = Object.getOwnPropertyNames(db_params);
            var db_params_value = [];
            for (var key in db_params){
                 db_params_value.push(db_params[key]);
            }
            
            var request_data_validation ="";
            /*check request parameters and db parameters are equal or not.*/
            if (db_params_keys.length == response_params_keys.length  && db_params_keys.every(function(u, i) {
                    return u === response_params_keys[i];
            })) 
            {
              /*check request parameters are not null.*/
              for (const request_data_key in request_data) {
                for (const db_params_key in db_params) {
                  if(request_data_key == db_params_key && db_params[db_params_key] == true && request_data[request_data_key] == "" )
                  {
                    request_data_validation = db_params_key;
                    break; 
                  }
                }
              }
              if(request_data_validation!=""){
                 response = {"Status": 201, "Message": "Invalid Data Request", "ResponseBody": "Unknown report type "+request_data_validation+" value."};
                        return res.json(response);
              }else
              {
                KpiReport.ReportByType(db_call_name,response_params_value,function (result) {
                  if (result == null || result.length == 0) {
                      var response = '{"Status":200, "Message":"success", "ResponseBody":"No data found!"}';
                  } else {
                      data = result[0];
                      var flag = 0;
                      for (var key in data){
                        if(data[key]!= 0){
                          flag =1;
                        }
                      }
                      if(flag === 0)
                      {
                        var response = '{"Status":200, "Message":"success", "ResponseBody":"No data found!"}';
                      }else
                      {
                        var response = '{"Status":200, "Message":"success", "ResponseBody":' + JSON.stringify(result) + '}';
                      }                     
                  }
                  res.send(response);
                  return true;
                });
              }
            } 
            else
            {
              response = {"Status": 201, "Message": "Invalid Data Request", "ResponseBody": "Please maintain parameters like  "+db_params_keys+" and report_type."};
              return res.json(response);
            }
          });
      }else
      {
        response = {"Status": 201, "Message": "Invalid Data Request", "ResponseBody": "Unknown report type."};
        return res.json(response);
      }
    }
  }
};

