/**
 * CrateController
 *
 * @description :: Server-side logic for managing Crate related oprations
 * @help    ();    :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
//var _ = require('lodash');
var dateFormat = require('dateformat');
const Crate = require('../models/Crate');

module.exports = {
	index: function(req, res){
		console.log(sails.config.baseurl.url);
		return res.send('Crate APIs...');
	},

	getCrateDetails: function(req, res)
	{
		console.log("Function: getCrateDetails \n",req.body.data);
		if(!req.body.data){
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON Format"
            };
            return res.json(response);
		}else{
			var data = req.body.data;
			//data = JSON.parse(data);
			var type = data.type;
			var code = data.code;
		}
		console.log('data'+data);
		console.log('type'+type);
		console.log('code'+code);	

		if(type === 'c'){
			Crate.checkCrateStatus(code, function (result){
				if(result == null || result.length==0){
					var response = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';
					res.send(response);
					return true;
				} else if(result[0].status === 136001){
					var response = '{"Status":204, "Message":"success", "ResponseBody":{"status":"empty"}}';
					res.send(response);
					return true;
				} else{
					Crate.getOrderInvoiceDetails(code, type, function (result)
					{
						if(result == null || result.length==0){
							var response = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';
							return res.send(response);
						}

						var final = [];
						final.push(result[0]);
						var order_id = result[0].gds_order_id;

						console.log("Calling getOrderInvoiceDetails("+order_id+")");
						Crate.getContainerDetails(order_id, final, function(response,final,order_id){

							if(response == null){
								var resp = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';
								return res.send(resp);
							}
							var key = final.length;
							final[key-1]['containers'] = response;
							//final=final[key-1];

							Crate.getCancelledProducts(order_id, final, function(response, final){
								console.log("Calling getCancelledProducts: "+order_id);
								if(response == null){
									var resp = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';
									return res.send(resp);
								}
								
								var key = final.length;
								final[key-1]['cancelled_items'] = response;
								final=final[key-1];

								var finalJson = '{"Status":200, "Message":"success", "ResponseBody":'+JSON.stringify(final)+'}';
								return res.send(finalJson);
							});							
						});
					});
				}
			});
		} else if(type === 'o'){
			console.log('Reached O');
			Crate.getOrderInvoiceDetails(code, type, function (result)
			{
				if(result == null || result.length==0){
					var response = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';
					return res.send(response);
				}

				var final = [];
				final.push(result[0]);
				var order_id = result[0].gds_order_id;

				console.log("Calling getOrderInvoiceDetails("+order_id+")");
				Crate.getContainerDetails(order_id, final, function(response,final){

					if(response == null){
						var resp = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';
						return res.send(resp);
					}
					var key = final.length;
					final[key-1]['containers'] = response;
					//final=final[key-1];

					Crate.getCancelledProducts(order_id, final, function(response, final){
						console.log("Calling getCancelledProducts: "+order_id);
						if(response == null){
							var resp = '{"Status":201, "Message":"success", "ResponseBody":"No data found!"}';
							return res.send(resp);
						}
						var key = final.length;

						final[key-1]['cancelled_items'] = response;
						final=final[key-1];

						var finalJson = '{"Status":200, "Message":"success", "ResponseBody":'+JSON.stringify(final)+'}';
						return res.send(finalJson);
					});	
				});
			});
		}
		
	},
	saveCrateVerification: function(req, res){
		console.log("Function: saveCrateVerification \n");
		if(!req.body.data){
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON"
            };
            return res.json(response);
		}else{
			var data = JSON.parse(req.body.data);
			var orderId = data.gds_order_id;
                        var current_status = (data.current_status)?data.current_status:'';
			var userId = data.Userid;
			var containers = data.containers;
		}
		console.log("parameter received \nOrder:"+orderId+" User:"+userId);

		var selected = [];
		if(containers.length>0){
			for(var key in containers){
				if(containers[key].is_verified == 1)
					selected.push(containers[key].container_barcode);
			}
		}

		if(selected.length>0 && (containers.length === selected.length)){
			Crate.saveContainerVerification(orderId, userId, selected, function(result){
				console.log("Affected rows: "+result.affectedRows+"\nChanged Rows: "+result.changedRows+"\n");
				if(result.affectedRows>0){
                                    Crate.getTokenByUserId(userId, function(tokenResult){
                                        if(tokenResult){
                                            if(current_status!=17021){
                                                Crate.verifyPickedQtyApi(orderId, tokenResult, function(apiResult){
                                                    if(apiResult.Message == "FullPicked"){
                                                        Crate.generateInvoiceApi(orderId, tokenResult, function(apiResults){
                                                            var message;
                                                            if(apiResults.Status == 200){
                                                                message = "Success";
                                                            } else {
                                                                message = "Failed";
                                                            }
                                                            response = {
                                                                "Status": apiResults.Status,
                                                                "Message": message,
                                                                "ResponseBody": apiResults.Message
                                                            }
                                                            return res.json(response);
                                                        });
                                                    } else {
                                                        response = {
                                                            "Status": 200,
                                                            "Message": "Success",
                                                            "ResponseBody": "The order was partially picked, so auto invoice is not generated"
                                                        };
                                                        return res.json(response);
                                                    }
                                                });
                                            } else {
                                                response = {
                                                    "Status": 200,
                                                    "Message": "Success",
                                                    "ResponseBody": "Invoice already Created"
                                                };
                                                return res.json(response);
                                            }
                                        } else {
                                            response = {
                                                "Status": 200,
                                                "Message": "Success",
                                                "ResponseBody": "Could not get token for the given user id"
                                            };
                                            return res.json(response);
                                        }
                                    });
//					response = {
//		                "Status":200,
//		                "Message":"Success",
//		                "ResponseBody":"Update Success"
//		            };
//					return res.json(response);
				} else{
					response = {
		                "Status":400,
		                "Message":"Failed",
		                "ResponseBody":"No Match found"
		            };
					return res.json(response);
				}
			});
		} else{
			response = {
                "Status":200,
                "Message":"Success",
                "ResponseBody":"All containers are not verified"
            };
			return res.json(response);
		}		
	},
	getPickedCrateList: function(req, res){
		console.log('Log Started @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
		console.log("Function: getPickedCrateList \n");
		if(!req.body.data){
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON"
            };
            return res.json(response);
		}else{
			var data = req.body.data;
			var pickedDate = data.date;
		}
		console.log("parameter received \nPickedDate:"+pickedDate);

		if(pickedDate){
    		try{
        		pickedDate = dateFormat(pickedDate, "yyyy-mm-dd");
        		console.log('pickedDate: '+pickedDate+"\n");
    		}catch(err){
    			console.error('Error in date conversion: '+err+"\n");
            	console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
            	response = {
                    "Status":400,
                    "Message":"Bad Request",
                    "ResponseBody":"Invalid Date"
                };
            	return res.json(response);;
    		}
    	}
    	else{
    		var pickedDate = dateFormat("yyyy-mm-dd");
    	}

		if(pickedDate){
			Crate.getPickedCrateDetails(pickedDate, function(result){
				if(result.lenght ===0){
					response = {
		                "Status":200,
		                "Message":"Success",
		                "ResponseBody":"No Crate Found"
		            };
					return res.json(response);
				} else{
					response = {
		                "Status":200,
		                "Message":"Success",
		                "ResponseBody":result
		            };
					return res.json(response);
				}				
			});
		} else{
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Missing Date"
            };
			return res.json(response);
		}		
	},

	getProductReason: function(req, res){
		console.log('Log Started @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
		console.log("Function: getProductReason \n");
		console.log(req.body.data);
		if(!req.body.data){
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON"
            };
            return res.json(response);
		}else{
			var data = req.body.data;

			if(!data.orderId || !data.productid || !data.containerBarcode){
				response = {
	                "Status":400,
	                "Message":"Bad Request",
	                "ResponseBody":"Missing values"
	            };
	            return res.json(response);
			}

			var odrId = data.orderId;
			var prodId = data.productid;
			var container = data.containerBarcode;
		}
		console.log("Calling model `getSavedReasons`:\nOrderId:"+odrId+"\nProductId:"+prodId+"\nContainer:"+container);

		try{
			Crate.getSavedReasons(odrId, prodId, container, function(result){
				console.log(result);
				if(result.length !==0){
					response = {
		                "Status":200,
		                "Message":"Success",
		                "ResponseBody":result
		            };
					return res.json(response);
				} else{
					response = {
		                "Status":400,
		                "Message":"Failed",
		                "ResponseBody":"No Match found"
		            };
					return res.json(response);
				}
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			return(err);
		}
		//return res.json(data);
	},

	saveWrongPickReason: function(req, res){
		console.log('Log Started @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
		console.log("Function: saveWrongPickReason \n");
		console.log(req.body.data);
		if(!req.body.data){
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON"
            };
            return res.json(response);
		}else{
			var data = req.body.data;

			if(!data.orderId || !data.userid || !data.productid || !data.containerBarcode || !data.reasonCode || !data.qty){
				response = {
	                "Status":400,
	                "Message":"Bad Request",
	                "ResponseBody":"Missing values"
	            };
	            return res.json(response);
			}

			var odrId = data.orderId;
			var usrId = data.userid;
			var prodId = data.productid;
			var container = data.containerBarcode;
			var reasonCode = data.reasonCode;
			var qty = data.qty;
		}
		console.log("Calling model to update `saveReason`:\nOrderId:"+odrId+"\nProductId:"+prodId+"\nContainer:"+container+"\nReason:"+reasonCode+"\nQty:"+qty);

		try{
			Crate.saveReason(odrId, prodId, container, reasonCode, qty, usrId, function(result){
				console.log("Affected rows: "+result.affectedRows+"\nChanged Rows: "+result.changedRows+"\n");
				if(result.affectedRows>0){
					response = {
		                "Status":200,
		                "Message":"Success",
		                "ResponseBody":"Update Success"
		            };
					return res.json(response);
				} else{
					response = {
		                "Status":400,
		                "Message":"Failed",
		                "ResponseBody":"No Match found"
		            };
					return res.json(response);
				}
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			return(err);
		}
		//return res.json(data);
	},

	getReasonList: function(req, res){
		console.log('Log Started @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
		console.log("Function: getReasonList \n");
		console.log(req.query);
		if(!req.query.query){
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid Query"
            };
            return res.json(response);
		}else{
			//var data = req.body.data;
		}
		

		try{
			Crate.getReasons(req.query.query, function(result){
				if(result.lenght !==0){
					console.log(result);
					response = {
		                "Status":200,
		                "Message":"Success",
		                "ResponseBody":result
		            };
					return res.json(response);
				} else{
					response = {
		                "Status":400,
		                "Message":"Failed",
		                "ResponseBody":"No Match found"
		            };
					return res.json(response);
				}
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			return(err);
		}
	},

	getWrongPickedProducts: function(req, res){
		console.log('Log Started @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
		console.log("Function: getWrongPickedProducts \n");
		// console.log(req.body.data);
		if(!req.body.data){
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON"
            };
            return res.json(response);
		}else{
			var data = JSON.parse(req.body.data);

			if(!data.order_id || !data.crates){
				response = {
	                "Status":400,
	                "Message":"Bad Request",
	                "ResponseBody":"Missing values"
	            };
	            return res.json(response);
			}

			var odrId = data.order_id;
			var container = data.crates;
		}
		// console.log(odrId);
		// console.log(container);

		console.log("Calling model `getSavedReasonsByCrate`:\nOrderId:"+odrId+"\nContainer:"+container);

		try{
			Crate.getSavedReasonsByCrate(odrId, container, function(result){
				//console.log(result);
				if(result.length !==0){
					response = {
		                "Status":200,
		                "Message":"Success",
		                "ResponseBody":result
		            };
					return res.json(response);
				} else{
					response = {
		                "Status":200,
		                "Message":"Success",
		                "ResponseBody":[]
		            };
					return res.json(response);
				}
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			return(err);
		}
		//return res.json(data);
	}
};

