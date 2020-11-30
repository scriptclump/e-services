/*
Filename : taxController.js
Author : eButor
CreateData : 20-July-2016
Desc : Tax related API calls 
*/
var dateFormat = require('dateformat');
var apiCall = system.getModel('tax');
var taxController = {
	/*
	Req : @req (Json)- product_id, state_id 
	Res : @res (Json)- Json response
	Desc : Get Tax details with Tax Type and Percentage
	*/

    getTaxDetails:function(req, res){
    	/*
			This function calls taxModel and decides applied taxes on product
    	*/
    	console.log("\n========================\n");
    	console.log('Log started @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
    	if(Object.keys(req.body).length === 0){
    		response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON Format"
            };

            console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n"+JSON.stringify(response));
            
            return res.json(response);
    	}

    	console.log(JSON.stringify(req.body));
    	console.log("========Request Received=====\n");
    	console.log('Product ID: '	+req.body.product_id+"\n");
    	console.log('Buyer State: '	+req.body.buyer_state_id+"\n");
    	console.log('Seller State: '	+req.body.seller_state_id+"\n");
    	console.log("=========Request Received====\n");

		if(!req.body.product_id || !req.body.buyer_state_id || !req.body.seller_state_id){
            res.json(sleekConfig.badRequestWithWrongInput);
            return;
        }
        else{
        	if(req.body.date){
        		try{
            		console.log('paramDate: '+req.body.date+"\n");
            		var paramDate = dateFormat(req.body.date, "yyyy-mm-dd");
        		}catch(err){
        			console.error('Error in date conversion: '+err+"\n");
	            	
	            	response = {
	                    "Status":400,
	                    "Message":"Bad Request",
	                    "ResponseBody":"Invalid Date"
	                };
	                console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n"+JSON.stringify(response));
	            	//res.json(sleekConfig.badRequestWithWrongInput);
	            	return res.json(response);;
        		}
        	}
        	else{
        		var paramDate = dateFormat("yyyy-mm-dd");
        	}
			// Call the model to get the final response 
            apiCall.getTaxes(req.body.product_id, req.body.buyer_state_id, req.body.seller_state_id, paramDate, function(apiResponse){
                // make the response 
                console.log('Response Received ..: '+apiResponse+"\n");
                var json = JSON.parse(apiResponse);
                var result = new Array(); 
                var error = '';
                //Check if Json contains data
                if(json.length !==0){
                	//Json Loop
                	if(json[0].hasOwnProperty("Error") && json[0]['Error'].length!==0){
                		console.log('Error Cought ..: '+json[0]['Error']+"\n");
                		response = {
	                        "Status":400,
	                        "Message":"Bad Request",
	                        "ResponseBody":json[0]['Error']
	                    };

	                    console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n"+JSON.stringify(response));
                		
                		//res.json(sleekConfig.badRequestWithWrongInput);
                		return res.json(response);
                	}
                	else if(json[0].hasOwnProperty("serverError") && json[0]['serverError'].length!==0){
                		console.log('Server Error Cought ..: '+json[0]['serverError']+"\n");
                		response = {
	                        "Status":500,
	                        "Message":"Error",
	                        "ResponseBody":json[0]['serverError']
	                    };

	                    console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n"+JSON.stringify(response));
                		
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

	                    console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n"+JSON.stringify(response));
                		
                		//res.json(sleekConfig.badRequestWithWrongInput);
                		return res.json(response);
                	}
                	else{
        				var approvalFlag = 0; var sellableFlag = 0; var checkPackTypeFlag = 0;
                		for(key in json){
		                	//Check Parameters	                	
		                	if( json.hasOwnProperty(key) ){
		                		if(json[key]['approval'] !== 1){
		                			approvalFlag = 1;
		                			break;
		                		}
		                		if(json[key]['sellable'] === 0){
		                			sellableFlag = 1;
		                			if(json[key]['pack_type'] === 'Freebie'){
			                			checkPackTypeFlag = 1;
			                			console.log('Freebie Item Found\n');
			                			break;
			                		}
		                			console.log('Non Sellable Item found\n');
		                			//break;
		                		}		                		
		                		var tax_class_id 	= json[key]['tax_class_id'];
			                	var tax_type 		= json[key]['tax_class_type'];
			                	var tax_code 		= json[key]['tax_class_code'];
			                	var tax_percent 	= json[key]['tax_percentage'];
			                	var tax_start		= null;
			                	var HSN_Code		= json[key]['hsn_code'];
			                	var CGST			= json[key]['CGST'];
			                	var SGST			= json[key]['SGST'];
			                	var IGST			= json[key]['IGST'];
			                	var UTGST			= json[key]['UTGST'];

			                	if(json[key]['date_start'] !== null && json[key]['date_start'] !== '0000-00-00'){
			                		tax_start	= dateFormat(json[key]['date_start'], "yyyy-mm-dd");
			                	}		                	
			                	var tax_end		= null;
			                	if(json[key]['date_end'] !== null && json[key]['date_end'] !== '0000-00-00'){
			                		tax_end	= dateFormat(json[key]['date_end'], "yyyy-mm-dd");
			                	}
			                	console.log("========TAX CLASS=====\n");

			                	console.log('tax_class_id: '	+tax_class_id+"\n");
			                	console.log('tax_class_type: '	+tax_type+"\n");
			                	console.log('tax_class_code: '	+tax_code+"\n");
			                	console.log('tax_percentage: '	+tax_percent+"\n");
			                	console.log('date_start: '		+tax_start+"\n");
			                	console.log('date_end: '		+tax_end+"\n");
			                	console.log('status: '			+json[key]['status']+"\n");
			                	console.log('HSN_Code: '		+HSN_Code+"\n");
			                	console.log('CGST: '			+CGST+"\n");
			                	console.log('SGST: '			+SGST+"\n");
			                	console.log('IGST: '			+IGST+"\n");
			                	console.log('UTGST: '			+UTGST+"\n");

			                	console.log("=========TAX CLASS====\n");

			                	//Prepare response
			                	tax_value = {
			                		'Tax Class ID' 		: tax_class_id,
			                		'Tax Type' 			: tax_type,
			                		'Tax Code' 			: tax_code,
			                		'Tax Percentage' 	: tax_percent,
			                		"HSN_Code"			: HSN_Code,
			                		"CGST"				: CGST,
			                		"SGST"				: SGST,
			                		"IGST"				: IGST,
			                		"UTGST"				: UTGST
			                	};
			                	
		                		//If both dates are present
		                		if(tax_start!==null && tax_start!=='0000-00-00' && paramDate>=tax_start){
		                			result.push(tax_value);
		                		}
		                	}
		                }
		                //console.log("Approval: "+approvalFlag);
		                if(approvalFlag === 1){
		                	response = {
		                        "Status":204,
		                        "Message":"Success",
		                        "ResponseBody":"One or many Tax classes for product are Not Approved "
		                    };
		                }
		                else if(sellableFlag === 1 && checkPackTypeFlag === 1){
		                	response = {
		                        "Status":204,
		                        "Message":"Success",
		                        "ResponseBody":"Product is Non-Sellable and Freebie"
		                    };
		                }
		                else{
		                	response = {
		                        "Status":200,
		                        "Message":"Success",
		                        "ResponseBody":result
		                    };
		                }
		                
	                    console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n"+JSON.stringify(response));
	                    res.json(response);
	                    return;
                	}
	            }
	            else{
	            	
	            	response = {
                        "Status":204,
                        "Message":"Success",
                        "ResponseBody":"Tax Mapping for Product Not Found"
                    };

                    console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n"+JSON.stringify(response));
                    res.json(response);
	            	//res.json(sleekConfig.SuccessNoData);
	            	return;
	            }
            });
		}
	},

};


module.exports = taxController;