/**
 * comonapi.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
const db = require('../../dbConnection');

module.exports = {
    savepolicydetails:function(data,file,callback){
    var data = JSON.parse(data);
    console.log(data);
    var policy_name = data.policy_name;
    var effective_date = data.effective_date;
    var expiry_date = data.expire_date;

    //var policy_name = data.policy_name;
      if(!data){
        var response = {"Status":400,"Message":"Bad Request","Response Body":"Invalid JSON Format"}
        return res.json(response);
      }else{
        var sql ="Insert Into hr_policies (policy_name,effective_date,expire_date,file) VALUES ('"+policy_name+"','"+effective_date+"','"+expiry_date+"','"+file+"')";
        db.query(sql,{},function (err, result) {
                    if (err) {
                        sails.log(err);
                        return err;
                    }else{
                    //console.log(result.insertId);
                    callback(result);
            } 
        });
      }

    },

    viewallpolicies:function(callback){
        var sql ="select * from  hr_policies";
        db.query(sql,{},function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }else{
            //console.log(result.insertId);
            callback(result);
        } 
        });
    }
  
};

