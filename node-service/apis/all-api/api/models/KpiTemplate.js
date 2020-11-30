/**
 * KpiTemplate.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

module.exports = {
	connection: 'someMongodbServer',
	//tableName: 'ApiLogs',
  	attributes: {

  	},
  	TemplateData:function(report_type, callback){
  		KpiTemplate.find({"report_type":report_type}).exec(function(err, result){
  			if(err)
  			{
  				console.log(err);
  				return err;
  			}
  			callback(result);
  		});
  	}
};

