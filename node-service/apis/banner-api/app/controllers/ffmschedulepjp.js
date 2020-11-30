const cart = require('../models/cartmodel');
const ffmschedule = require('../models/ffmschedulepjpmodel');
const joi = require('joi');
var NodeGeocoder = require('node-geocoder');
module.exports = {
	/*
	Function :dcfclistbyuser
	Input : sales token of ff/ff manager
	Output : list of all dcs which are access specific
	Author : Ebutor
	*/
	dcfclistbyuser: function(req,res){
		try{
			if(Object.keys(req.body.data).length>0)
			{
				var params=req.body.data;
				var lptoken;
				params=JSON.parse(params);
				if(params.hasOwnProperty('sales_token'))
				{
					if(params.hasOwnProperty('latitude') && params.hasOwnProperty('longitude'))
					{
						//check if users exits with the sales or customer token
						cart.getUserIdFromToken(params['sales_token']).then((data)=>{
							console.log(data);
							if(data.length>0){
								var userid=data[0].user_id;
								var reqdata={"user_id":userid,'permissionLevelId':6};
								ffmschedule.getFilterData(reqdata).then((dclist)=>{
									if(dclist.length>0)
									{
										var reqdata2={'accessdclist':dclist[0].sbu['118001'],'latitude':params.latitude,'longitude':params.longitude}
										ffmschedule.getDisplayNameByLeWhID(reqdata2).then((dipsplayname)=>{
											ffmschedule.getScheduledPlansForFF(reqdata).then((sheduledplans)=>{
												Array.prototype.push.apply(dipsplayname,sheduledplans);
												//dipsplayname.filter((v,i,a)=>a.find(t=>(t.le_wh_id === v.le_wh_id))===v.le_wh_id);
												dipsplayname =  dipsplayname.map(e => e['le_wh_id'])
								                  // store the indexes of the unique objects
								                  .map((e, i, final) => final.indexOf(e) === i && i)
								                  // eliminate the false indexes & return unique objects
								                 .filter((e) => dipsplayname[e]).map(e => dipsplayname[e]);
								                  dipsplayname = dipsplayname.filter(value => Object.keys(value).length !== 0);
												res.send({'status':'success','message':"Data Found","data":dipsplayname});				
											}).catch(err=>{
												console.log(err);
												res.send({'status':'failed','message':'Please Try Again',"data":[]});
											});
										}).catch(err=>{
											console.log(err);
											res.send({'status':'failed','message':'Please Try Again',"data":[]});
										});
									}
								});
							}else{
								res.send({'status':'failed','message':'No User Found','data':[]});		
							}
						});
					}else{
						res.send({'status':'failed','message':'GeoLocation Missing','data':[]});
					}	
				}else{
					res.send({'status':'failed','message':'Please send token','data':[]});
				}
			}
		}
		catch(err){
	            console.error(err);
	            res.send({'status':'failed','message':'Please Try Again after sometime','data':[]});
    	}	
	},
	/*
	Funtion: getBeatsByUser
	Input Params: sales token (ff user password token or lp token)
	Result: List of all Beats particular FF/FFM has, which is returned by procedure
	Author: Ebutor Dec 11th,2019(Written)
	*/
	getBeatsByUser: function(req,res){
		try{
			if(Object.keys(req.body.data).length>0)
			{
				let params=req.body.data;
				params=JSON.parse(params);
				let keyword = params.hasOwnProperty('keyword')?params.keyword:'';
				let limit = params.hasOwnProperty('limit')?params.limit:'';
				let offset = params.hasOwnProperty('offset')?params.offset:'';
				if(params.hasOwnProperty('sales_token'))
				{
					if(keyword=='')
					{
						if(limit!=='')
						{
							if(offset!=='')
							{
								cart.getUserIdFromToken(params['sales_token']).then((data)=>{
									if(data.length>0){
										ffmschedule.getBeatsByffId(data[0].user_id,data[0].legal_entity_id,limit,offset).then((beatslist)=>{
											res.send({'success':'success','message':'Data found','data':{beats:beatslist}});				
										}).catch(err=>{
											res.send({'status':'failed','message':err,'data':[]});		
										});
									}else{
										res.send({'status':'failed','message':'No User Found','data':[]});		
									}
								}).catch( err=>{
									res.send({'status':'failed','message':'Please Try Again','data':[]});
								});
							}else{
								res.send({'status':'failed','message':'Please send offset','data':[]});
							}
						}else{
							res.send({'status':'failed','message':'Please send limit','data':[]});
						}
					}else{
						cart.getUserIdFromToken(params['sales_token']).then((data)=>{
							if(data.length>0){
								ffmschedule.getBeatsByffIdByserach(data[0].user_id,data[0].legal_entity_id,keyword).then(result=>{
									res.send({'status':'success','message':'Data found','data':{beats:result}});

								}).catch(err=>{
									res.send({'status':'failed','message':'No data found','data':[]});
								});
							}else{
								res.send({'status':'failed','message':'You are already logged into system!!','data':[]});
							}
								
						});
					}
				}else{
					res.send({'status':'failed','message':'Please send token','data':[]});
				}
			}
		}
		catch(err){
	            console.error(err);
	            res.send({'status':'failed','message':'Please Try Again after sometime','data':[]});
    	}		

	},
	/*
	Function :getRetailersByBeatId
	Input 	 :sales_token,ffid,beat_id,limit,offset
	output 	 : List of all Retailers under particular beat
	*/
	getRetailersByBeatId : function(req,res){
		try{
			if(Object.keys(req.body.data).length>0)
			{
				let params=req.body.data;
				params=JSON.parse(params);
				let sales_token=params.hasOwnProperty('sales_token')?params.sales_token:"";
				let ffid=params.hasOwnProperty('ff_id')?params.ff_id:"";
				let beat_id=params.hasOwnProperty('beat_id')?params.beat_id:"";
				let limit=params.hasOwnProperty('limit')?params.limit:"";
				let offset=params.hasOwnProperty('offset')?params.offset:"";
				let sort=params.hasOwnProperty('sort')?params.sort:"";
				let search=params.hasOwnProperty('search')?params.search:"";
				let is_billed=params.hasOwnProperty('is_billed')?params.is_billed:"";
				let flag=params.hasOwnProperty('flag')?params.flag:"";
				let hub=params.hasOwnProperty('is_billed')?params.hub:"";
				let spoke=params.hasOwnProperty('spoke')?params.spoke:"";
				if(sales_token!=='')
				{
					if(ffid!=='')
					{
						if(beat_id!=='')
						{
							if(limit!=='')
							{
								if(offset!=='')
								{
									cart.getUserIdFromToken(sales_token).then((data)=>{
										if(data.length>0)
										{
											ffmschedule.getAllCustomers(data[0].user_id,beat_id,is_billed,offset,limit,search,flag,hub,spoke,sort).then(retailersinfo=>{
												console.log(retailersinfo);
												res.send({'status':'success','message':'Data Found','data':{retailers:retailersinfo}});
											}).catch( err=>{
												res.send({'status':'failed','message':'Please Try Again','data':[]});
											});
										}else{
											res.send({'status':'failed','message':'No User Found','data':[]});
										}

									}).catch(err=>{
										res.send({'status':'failed','message':'Please Try Again','data':[]});	
									});			
								}else{
									res.send({'status':'failed','message':'Please Send Offset','data':[]});
								}
							}else{
								res.send({'status':'failed','message':'Please Send limit','data':[]});	
							}

						}else{
							res.send({'status':'failed','message':'Please Send Beat','data':[]});			
						}
					}else{
						res.send({'status':'failed','message':'FF ID Missing','data':[]});
					}
				}else{
					res.send({'status':'failed','message':'Sales Token Missing','data':[]});
				}
			}
		}
		catch(err){
	            console.error(err);
	            res.send({'status':'failed','message':'Please Try Again after sometime','data':[]});
    	}
	},
	checkIn: function(req,res){
		try{
			if(Object.keys(req.body.data).length>0)
			{
				let params=req.body.data;
				params=JSON.parse(params);
				//console.log(params);
				let sales_token=params.hasOwnProperty('sales_token')?params.sales_token:"";
				//let customer_token=params.hasOwnProperty('customer_token')?params.customer_token:"";
				let customer_le_id=params.hasOwnProperty('customer_le_id')?params.customer_le_id:"";
				let mobile=params.hasOwnProperty('mobile')?params.mobile:"";
				let assigned_date=params.hasOwnProperty('assigned_date')?params.assigned_date:"";
				let le_wh_id=params.hasOwnProperty('le_wh_id')?params.le_wh_id:"";
				let pincode=params.hasOwnProperty('pincode')?params.pincode:"";
				let checkin_lat=params.hasOwnProperty('checkin_lat')?params.checkin_lat:"";
				let checkin_long=params.hasOwnProperty('checkin_long')?params.checkin_long:"";
				let flag=params.hasOwnProperty('flag')?params.flag:"";
				let legal_entity_id=params.hasOwnProperty('legal_entity_id')?params.legal_entity_id:"";
				let city=params.hasOwnProperty('city')?params.city:"";

				if(sales_token!=='')
				{
					if(flag==='Retailer')
					{
						if(customer_le_id!=='')
						{
							if(assigned_date!=='')
							{
								/*if(le_wh_id!=='')
								{*/
									if(pincode!=='')
									{
										if(checkin_lat!=='' && checkin_long!=='')
										{
											cart.getUserIdFromToken(sales_token).then((data)=>{
												if(data.length>0)
												{
													let custledetails={"customer_le_id":customer_le_id,"mobile":mobile};
													ffmschedule.getUserIdCustLeId(custledetails).then((customerdetails)=>{
														console.log(customerdetails[0].user_id+"customer_le_idcustomer_le_idcustomer_le_idcustomer_le_id");
														if(customerdetails.length>0){
															/*var ffminputparams={"ff_id":data[0].user_id,"assigned_date":assigned_date,"pincode":pincode,"le_wh_id":le_wh_id};
															ffmschedule.checkffmShedules(ffminputparams).then((checkffmscheduledata)=>{
																if(checkffmscheduledata['status']!='success' || checkffmscheduledata['data']==0 || checkffmscheduledata['data']==''){
																	res.send(checkffmscheduledata);
																}else{*/
																	cart.validParentChildRelation({cust_le_id:customerdetails[0].legal_entity_id,ff_le_id:data[0].legal_entity_id,user_id:data[0].user_id}).then(data2=>{
																		console.log(data2['status']);
																		if(data2['status']!='success'){
																			res.send(data2);
																		}else{
																			cart.getUserIdFromLeid(customerdetails[0].legal_entity_id).then(userdet=>{
																				if(userdet.length>0){
																					/*cart.checkValidRelation({'ff_id':salesUserId,'cust_le_id':customerLegalEntityId,'user_id':userdet[0]['user_id']}).then(data3=>{
																						res.send(data3);
																					});	*/
																					var ffmcalllogs={"ff_id":data[0].user_id,"customer_id":customerdetails[0].user_id,"customerLegalEntityId":customerdetails[0].legal_entity_id,"checkin_lat":checkin_lat,"checkin_long":checkin_long};	
																					ffmschedule.insertIntoCallLogs(ffmcalllogs).then(calllogsdata=>{
																							let customerffiddata={"ffid":data[0].user_id,"customer_id":customerdetails[0].user_id,"ff_log_id":calllogsdata}
																							res.send({status:'success',message:'Data Found',data:customerffiddata});
																					})
																				}else{
																					res.send({status:'failed',message:'You have already logged into the Ebutor System',data:[]});												
																				}
																			})
																			
																		}
																	}).catch( err=>{
																		res.send({'status':'failed','message':'Please Try Again','data':[]});
																	});
																/*}
															}).catch( err=>{
																res.send({'status':'failed','message':'Please Try Again','data':[]});
															});*/
														}else{
															res.send({status:'failed',message:'You have already logged into the Ebutor System',data:[]});
														}
													}).catch( err=>{
														res.send({'status':'failed','message':'Please Try Again','data':[]});
													});										
												}else{
														res.send({'status':'session','message':'You have already logged into the Ebutor System','data':[]});		
												}

											}).catch(err=>{
												res.send({'status':'failed','message':'Please Try Again','data':[]});	
											});
										}else{
											res.send({'status':'failed','message':'Latitude or Longitude is Missing','data':[]});
										}
									}else{
										res.send({'status':'failed','message':'Pincode Missing','data':[]});
									}
								/*}else{
									res.send({'status':'failed','message':'Warehouse Missing','data':[]});
								}*/
							}else{
								res.send({'status':'failed','message':'Please Send Selected Date','data':[]});
							}
						}else{
							res.send({'status':'failed','message':'Customer Token is Missing','data':[]});
						}
					}else if(flag==="citycheckin"){
						if(checkin_lat!=='' && checkin_long!=='' && city!=='')
							{
								cart.getUserIdFromToken(sales_token).then((data)=>{
									if(data.length>0)
									{
										//var ffmdcschedulecheck={"ff_id":data[0].user_id,"city":city,"flag":"city"};
										/*ffmschedule.checkFFDCSchedule(ffmdcschedulecheck).then((ffdcschedule)=>{
											if(ffdcschedule['status']!='success' || ffdcschedule['data']==0 || ffdcschedule['data']==''){
												res.send(ffdcschedule);
											}else{*/	
												var ffmcalllogs={"ff_id":data[0].user_id,"customer_id":0,"customerLegalEntityId":0,"checkin_lat":checkin_lat,"checkin_long":checkin_long};	
												ffmschedule.insertIntoCallLogs(ffmcalllogs).then((calllogsdata)=>{
														ffmschedule.trackCity({"city":city,"ff_id":data[0].user_id,"ffcalllogid":calllogsdata}).then((citylogs)=>{
															let customerffiddata={"ffid":data[0].user_id,"customer_id":0,"ff_log_id":calllogsdata};
															res.send({status:'success',message:'Data Found',data:customerffiddata});
														}).catch(err=>{
															let customerffiddata={"ffid":data[0].user_id,"customer_id":0,"ff_log_id":calllogsdata};
															res.send({status:'success',message:'Data Found',data:customerffiddata});
														});
												});	
											//}
										/*}).catch(err=>{
											res.send({'status':'failed','message':'Please Try Again','data':[]});	
										});*/									
									}else{
											res.send({'status':'success','message':'You have already logged into the Ebutor System','data':[]});		
									}

								}).catch(err=>{
									res.send({'status':'failed','message':'Please Try Again','data':[]});	
								});
							}else{
								res.send({'status':'failed','message':'Latitude or Longitude or city is Missing','data':[]});
							}
					}else{
						if(legal_entity_id!=='')
						{
							if(checkin_lat!=='' && checkin_long!=='')
							{
								cart.getUserIdFromToken(sales_token).then((data)=>{
									if(data.length>0)
									{
										/*var ffmdcschedulecheck={"ff_id":data[0].user_id,"le_wh_id":le_wh_id,"flag":"dc"};
										ffmschedule.checkFFDCSchedule(ffmdcschedulecheck).then((ffdcschedule)=>{
										if(ffdcschedule['status']!='success' || ffdcschedule['data']==0 || ffdcschedule['data']==''){
											res.send(ffdcschedule);
										}else{*/	
											var custledetails={"legal_entity_id":legal_entity_id};
											ffmschedule.getDCRetailers(custledetails).then((dcretailer)=>{
												console.log(dcretailer+'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz');
												var ffmcalllogs={"ff_id":data[0].user_id,"customer_id":dcretailer[0].user_id,"customerLegalEntityId":dcretailer[0].legal_entity_id,"checkin_lat":checkin_lat,"checkin_long":checkin_long};	
												ffmschedule.insertIntoCallLogs(ffmcalllogs).then((calllogsdata)=>{
													console.log(dcretailer[0].user_id+"calllogsdatacalllogsdatacalllogsdata")
														let customerffiddata={"ffid":data[0].user_id,"customer_id":dcretailer[0].user_id,"ff_log_id":calllogsdata}
														res.send({status:'success',message:'Data Found',data:customerffiddata});
												});
											}).catch(err=>{
												console.log(err);
												res.send({'status':'failed','message':'Please Try Again','data':[]});		
											});
										//}
										/*}).catch(err=>{
												console.log(err);
												res.send({'status':'failed','message':'Please Try Again','data':[]});		
										});*/										
									}else{
											res.send({'status':'session','message':'You have already logged into the Ebutor System','data':[]});		
									}

								}).catch(err=>{
									res.send({'status':'failed','message':'Please Try Again','data':[]});	
								});
							}else{
								res.send({'status':'failed','message':'Latitude or Longitude is Missing','data':[]});
							}
						}else{
							res.send({'status':'failed','message':'Warehouse Data Missing','data':[]});
						}
					}
				}else{
					res.send({'status':'failed','message':'Sales Token Missing','data':[]});
				}
			}
		}
		catch(err){
	            console.error(err);
	            res.send({'status':'failed','message':'Please Try Again after sometime','data':[]});
    	}		
	},
	checkOut: function(req,res){
		try{
			if(Object.keys(req.body.data).length>0)
			{
				let params=req.body.data;
				params=JSON.parse(params);
				const schema = joi.object().keys({
					ff_id:[joi.string(), joi.number()],
					customer_id:joi.number().integer().required(),
					ff_log_id:joi.number().integer().required(),
					checkout_lat:joi.number().required(),
					checkout_long:joi.number().required(),
					feedback:joi.string().default(null)
				});
				joi.validate(params,schema,function(err,result){
					if(err){
						console.log('err',err);
						res.send({'status':'failed','message':'Please send correct input','data':[]});
					}else{
						console.log('res',result);
						ffmschedule.checkOutFFMS(result).then((checkoutdata)=>{
							res.send(checkoutdata);
						}).catch( err=>{
							res.send({'status':'failed','message':'Please Try Again','data':[]});
						});			
					}
				});
			}
		}
		catch(err){
	            console.error(err);
	            res.send({'status':'failed','message':'Please Try Again after sometime','data':[]});
    	}
	},
	citiesList: function(req,res){
		try{
			if(Object.keys(req.body.data).length>0)
			{
				let params=req.body.data;
				params=JSON.parse(params);
				console.log(params);
				let schema = joi.object().keys({
					sales_token:joi.string().alphanum(),
					latitude:joi.number().required(),
					longitude:joi.number().required()
				});
				joi.validate(params,schema,function(err,result){
					if(err){
						console.log('err',err);
						res.send({'status':'failed','message':'Please send correct input','data':[]});
					}else{
						console.log('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
						cart.getUserIdFromToken(params.sales_token).then((data)=>{
							if(data.length>0){
								ffmschedule.getCitiesList({'latitude':params.latitude,"longitude":params.longitude}).then((citynames)=>{
									ffmschedule.getScheduledCitiesList({'latitude':params.latitude,"longitude":params.longitude,'user_id':data[0].user_id}).then((scheduledcitynames)=>{
										Array.prototype.push.apply(citynames,scheduledcitynames);
										citynames =  citynames.map(e => e['city_name'])
						                  .map((e, i, final) => final.indexOf(e) === i && i)
						                 .filter((e) => citynames[e]).map(e => citynames[e]); 
						                citynames = citynames.filter(value => Object.keys(value).length !== 0);
										res.send({'status':'success','message':'Data Found','data':citynames});
									}).catch( err=>{
										res.send({'status':'failed','message':'Please Try Again','data':err});
									});
								}).catch( err=>{
									console.log(err+'eeeeeeeeeeeeeeeeeeeeeeeeeeee');
									res.send({'status':'failed','message':'Please Try Again','data':err});
								});
							}else{
								res.send({'status':'failed','message':'You have Logged Out of Ebutor','data':[]});	
							}
						}).catch( err=>{
							console.log(err+' pppppppppppppppppppppppppp');
							res.send({'status':'failed','message':'Please Try Again','data':[]});
						});			
					}
				});
			}
		}
		catch(err){
	            console.error(err);
	            res.send({'status':'failed','message':'Please Try Again after sometime','data':[]});
		}
	},
	getLatitudeLongitudeByCityName: function(req,res){
		try{
			if(Object.keys(req.body.data).length>0)
			{
				let params=req.body.data;
				params=JSON.parse(params);
				console.log(params);
				let schema = joi.object().keys({
					city:joi.string().alphanum(),
					sales_token:joi.string().alphanum()
				});
				joi.validate(params,schema,function(err,result){
					if(err){
						console.log('err',err);
						res.send({'status':'failed','message':'Please send correct input','data':[]});
					}else{
						cart.getUserIdFromToken(params.sales_token).then((data)=>{
							if(data.length>0){
								ffmschedule.getcitycoordinaytesbyName({'city':params.city}).then((citydata)=>{
									res.send(citydata);
								}).catch( err=>{
									res.send({'status':'failed','message':'Please Try Again','data':[]});
								});	
								/*var options = {
									  provider: 'google',
									 
									  // Optional depending on the providers
									  httpAdapter: 'https', // Default
									  apiKey: 'AIzaSyDxnWRzkxLyWEVsaNA1DkCQOw_4nbwc8to', // for Mapquest, OpenCage, Google Premier
									  formatter: null         // 'gpx', 'string', ...
									};
									 
									var geocoder = NodeGeocoder(options);
									 
									// Using callback
									// geocoder.geocode('29 champs elysée paris', function(err, res) {
									//   console.log(res);
									// });
									 
									// Or using Promise
									geocoder.geocode(params.city)
									  .then(function(rescity) {
									    console.log(rescity);
									    //var lat_long={"latitude":res[0].latitude,"longitude":res[0].longitude}
									    res.send({'status':'success','message':'Data Found','data':rescity});
									  })
									  .catch(function(err) {
									    console.log(err);
									  });*/
							}else{
								res.send({'status':'failed','message':'You have Logged Out of Ebutor','data':[]});	
							}
						}).catch( err=>{
							console.log(err);
							res.send({'status':'failed','message':'Please Try Again','data':[]});
						});			
					}
				});
			}
		}
		catch(err){
	            console.error(err);
	            res.send({'status':'failed','message':'Please Try Again after sometime','data':[]});
		}	
	}
}