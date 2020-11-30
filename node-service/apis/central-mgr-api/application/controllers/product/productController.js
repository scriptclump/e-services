/*
Filename : productController.js
Author : eButor
CreateData : 19-Oct-2016
Desc : Product related API calls 
*/
var dateFormat = require('dateformat');
var apiCall = system.getModel('product');
var taxController = {
	/*
	Req : @req (Json)- product_id, state_id 
	Res : @res (Json)- Json response
	Desc : Get Tax details with Tax Type and Percentage
	*/

    getUnbilledSKUList:function(req, res){
    	/*
			This function calls taxModel and decides applied taxes on product
    	*/
    	console.log('Log started @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
    	console.log(req.body);
    	if(Object.keys(req.body).length === 0){
    		response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON Format"
            };
            res.json(response);
            return;
    	}

		if(!req.body.start_date || !req.body.end_date){
            res.json(sleekConfig.badRequestWithWrongInput);
            return;
        }
        else{
    		try{
        		console.log('Start Date: '+req.body.start_date+"\n");
        		console.log('End Date: '+req.body.end_date+"\n");
        		var startDate = dateFormat(req.body.start_date, "yyyy-mm-dd");
        		var endDate = dateFormat(req.body.end_date, "yyyy-mm-dd");
    		}catch(err){
    			console.error('Error in date conversion: '+err+"\n");
            	console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
            	response = {
                    "Status":400,
                    "Message":"Bad Request",
                    "ResponseBody":"Invalid Date"
                };
                res.json(response);
            	return;
    		}
    		a = /\s/g;
    		var ff  = req.body.ff.replace(a, "");
    		var area_code = req.body.area_code.replace(a, "");

    		if(ff.length === 0){
    			ff = 0;
    		}
    		if(area_code.length === 0){
    			area_code = 0;
    		}
    		console.log('startDate: '+startDate+' |endDate: '+endDate+' |ff: '+ff+' |area_code: '+area_code);
			// Call the model to get the final response 
            apiCall.getUnbilledSKUs(startDate, endDate, ff, area_code, function(apiResponse){
                // make the response 
                //console.log('Response Received ..: '+apiResponse+"\n");
                var json = JSON.parse(apiResponse);
                var result = new Array(); 
                var error = '';
                //Check if Json contains data
                if(json.length !==0){
                	console.log(json.length);
                	//Json Loop
                	if(json[0].hasOwnProperty("serverError") && json[0]['serverError'].length!==0){
                		console.log('Server Error Cought ..: '+json[0]['serverError']+"\n");
                		response = {
	                        "Status":500,
	                        "Message":"Error",
	                        "ResponseBody":json[0]['serverError']
	                    };

	                    console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
                		
                		//res.json(sleekConfig.badRequestWithWrongInput);
                		return res.json(response);
                	}
                	else if(json[0].hasOwnProperty("Exception") && json[0]['Exception'].length!==0){
                		console.log('Exception Cought ..: '+json[0]['Exception']+"\n");
                		response = {
	                        "Status":204,
	                        "Message":"Success",
	                        "ResponseBody":json[0]['Exception']
	                    };

	                    console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
                		
                		//res.json(sleekConfig.badRequestWithWrongInput);
                		return res.json(response);
                	}
                	else{
		                	//result.push(json);
		                }

	                response = {
                        "Status":200,
                        "Message":"Success",
                        "ResponseBody":json
                    };
	                
                    console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
                    res.json(response);
                    return;
                }
	            else{
	            	console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
	            	response = {
                        "Status":204,
                        "Message":"Success",
                        "ResponseBody":"No Result Found"
                    };
                    res.json(response);
	            	//res.json(sleekConfig.SuccessNoData);
	            	return;
	            }
            });
		}
	},

};


module.exports = taxController;