const db=require('../../dbConnection');
module.exports={
	getUserDataByToken:function(req,res){
		return new Promise((resolve,reject)=>{
			var sql="SELECT user_id FROM users u WHERE u.password_token='"+req+"'";
			console.log(sql);
			db.query(sql,{},function(err,rows){
				if(err){
					console.log(err);
					reject(err);
				}
				if(rows!=undefined &&Object.keys(rows).length >0){
				 	resolve(rows[0]);
				}
				else
				 reject("Invalid Token")
			});
		});			

	},
}