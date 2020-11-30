/**
 * KpiTemplate.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
const db = require('../../dbConnection');
var MongoClient = require('mongodb').MongoClient;

const config = require('../../config/config.json');
module.exports = {
  	TemplateData:function(report_type, callback){
      var host = 'mongodb://'+config['mongo_host']+":"+config['mongo_port']+"/"+config['mongo_database'];
      MongoClient.connect(host, function (err, db) {
        var KpiTemplate = db.collection('kpitemplate');
    		KpiTemplate.find({"report_type":report_type}).exec(function(err, result){
    			if(err)
    			{
    				console.log(err);
    				return err;
    			}
    			callback(result);
    		});
      });
  	}
};

