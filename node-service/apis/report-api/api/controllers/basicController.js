const basic = require('../models/basic');
module.exports = {
	getBasicDetails:function(req,res){
		console.log('get basic',req.body);
		if(!req.body.data){
			response={
				"status":'400',
				"message":'Bad Request',
				"ResponseBody":"Invalid JSON"
			}
			res.send(JSON.parse(response));
		}else{
			var data=req.body.data;
		}
		//console.log("calling details("+type+","+code+")");
		basic.getData(data,function(err,result){
			if(result == null || result.length == 0 || err){
				var response = '{"Status":500, "message":"success", "ResponseBody":"No data found!"}';
				res.send(response);
			}else{
				var finalres='{"Status":200,"message":"success","ResponseBody":'+JSON.stringify(result)+'}'
				//console.log(finalres);
				res.send(finalres);
			}
			
		})

	}
}