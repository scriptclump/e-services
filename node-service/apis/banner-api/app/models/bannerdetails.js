const db=require('../../dbConnection');
module.exports={
	getBannerDetails:function(req,res){
		return new Promise((resolve,reject)=>{
			var data="select *,DATE_FORMAT(from_date, '%Y-%m-%d') AS f_date,  DATE_FORMAT(to_date, '%Y-%m-%d') AS t_date  from banner where banner_id="+req;
			var date = new Date().toLocaleString();
			//console.log(data)
			db.query(data,{},function(err,rows){
				if(err){
					return  reject(err);
				}
				if(Object.keys(rows).length >0){
				 	return resolve(rows[0]);
				}
				else{
					return reject("No mapping found..")
				}
			});
		});			

	},
	getSponsorDetails:function(req,res){
		return new Promise((resolve,reject)=>{
			var data="select *,DATE_FORMAT(from_date, '%Y-%m-%d') AS f_date,  DATE_FORMAT(to_date, '%Y-%m-%d') AS t_date  from sponsors where sponsor_id="+req;
			var date = new Date().toLocaleString()
			db.query(data,{},function(err,rows){
				if(err){
					return  reject(err);
				}
				if(Object.keys(rows).length >0){
				 	return resolve(rows[0]);
				}
				else{
					return reject("No mapping found..")
				}
			});
		});			

	},
	insertMappingDetails:function(req,res){
		return new Promise((resolve,reject)=>{
			var data="INSERT INTO sponsor_history_details (config_mapping_id,config_object_id,config_object_type,user_id,le_wh_id,hub_id,beat_id,action_type,cost,converted_to)  VALUES ?";
			db.query(data,[req],function(err,rows){
				if(err){
					return  reject(err);
				}
				if(Object.keys(rows).length >0){
				 	return resolve(rows[0]);
				}
				else{
					return reject("No mapping found..")
				}
			});
		});
	},
	getSponsoredDetails:function(req,res){
		return new Promise((resolve,reject)=>{
			var date = new Date().toISOString().split('T')[0];
			date = "'%"+date+"%'";
			var data="SELECT *,IF(b.`action_type`=16802,1,0) AS click_conversion  FROM sponsor_history_details b WHERE b.`config_mapping_id`="+req.config_mapping_id+" and user_id="+req.user_id+" and created_at like "+date+"and action_type="+req.action_type+" and config_object_type="+req.type;
			db.query(data,{},function(err,rows){
				if(err){
					return  reject(err);
				}
				resolve(rows[0]);
			});
		});
	},
	updateSponsoredDetails:function(req,res){
		return new Promise((resolve,reject)=>{
			var data="UPDATE sponsor_history_details s SET s.click_conversion = ? where s.sponsor_history_id = ? ";
			//console.log(data);
			//console.log(req.ic,req.sponsor_history_id)
			db.query(data,[req.ic,req.sponsor_history_id],function(err,rows){
				if(err){
					return  reject(err);
				}
				resolve(rows[0]);
			});
		});
	}
}