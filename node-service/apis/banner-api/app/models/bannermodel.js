const db=require('../../dbConnection');
module.exports={
	/**
	 * [checkBrandID To check brand exists]
	 * @param  {[array]} req [brand id]
	 * @param  {[int]} res [if brand exists then it will grater than 0 else 0]
	 */
	checkBrandID:function(req,res){
		return new Promise((resolve,reject)=>{
			var data="select count(*) as count from brands where brand_id="+req;
			console.log(data);
			db.query(data,{},function(err,res){
				if(err){
					//console.log(err);
					reject(err);
				}
				resolve(res[0].count);
			});
		});			

	},
	/**
	 * [checkCustomerToken To check whether token is authorized or  not]
	 * @param  {[string]} req [token]
	 * @param  {[integer]} res [user count with the given token]
	 */
	checkCustomerToken:function(req,res){
		return new Promise((resolve,reject)=>{
			var data="select count(u.user_id) as count from users u where u.password_token="+"'"+req+"'"+" or u.lp_token="+"'"+req+"'"+" or u.chat_token="+"'"+req+"'";
			//console.log(data);
			db.query(data,{},function(err,res){
				if(err){
					reject(err);
				}else{
					resolve(res[0].count);
				}
			});
		});
	},
	/**
	 * [getBeatsByUserId To get beat details]
	 * @param  {[array]} req [user id, legal entity id ]
	 * @param  {[Object]} res [Beat details]
	 */
	getBeatsByUserId:function(req,res){
		return new Promise((resolve,reject)=>{
			var beats=[];
			//console.log('req',req);
			if(req!=''){
				var data="select user_id,legal_entity_id from users where password_token="+"'"+req+"'"+" or lp_token="+"'"+req+"'"+" limit 1";
				db.query(data,{},function(err,res){
					if(err){
						reject(err);
					}else{
						if(res.length>0){
							//console.log('result',res);
							var user_id=res[0].user_id;
							var legal_entity_id=res[0].legal_entity_id;
							if(user_id>0){
								var flag=0;
								var hubDetails="select le_wh_id as hub_id from pjp_pincode_area where rm_id="+user_id+" limit 1";
								db.query(hubDetails,{},function(err,res){
									if(err){
										reject('');
									}else{
										if(res.length==0){
											flag = 2;
											hubData="select hub_id from retailer_flat where legal_entity_id="+legal_entity_id+" limit 1";
											db.query(hubData,{},function(err,res){
												if(err){
													reject('');
												}else{
													//console.log('one',hubData);
													hubDetails=res;
												}
											});
										}
										else{
											//console.log('two');
											hubDetails=res;
										}
										setTimeout(() => {
											//console.log('hub details',hubDetails);
											var hub_id=null;
											if(hubDetails.length>0){
												if(hubDetails.hasOwnProperty('hub_id')){
													var hub_id=hubDetails[0].hub_id
												}
											}
											var allBeatsaccess=module.exports.checkPermissionByFeatureCode('ALLBEAT1',user_id);
											if(allBeatsaccess){
												flag=1;
											}
											var query="CALL getBeatDetails("+user_id+","+legal_entity_id+","+hub_id+","+flag+",1000,0)";
											console.log('procedure',query);
											db.query(query,{},function(err,res){
												if(err){
													reject('');
												}else{
													//console.log('resjson',res.length);
													res=JSON.parse(JSON.stringify(res[0]));
													var finalresult=[];
													for(var i=0;i< res.length;i++){
														if(res[i]['Beat ID']!=null){
															finalresult.push(res[i]['Beat ID']);
														}
													}
													
													//console.log('final result......',finalresult);
													//console.log(JSON.stringify(finalresult));
													resolve(finalresult);
												}
											});
										},600);					
									}
								});
							}
						}else{
							reject(''); 
						}
					}
				});
			}else{
				reject('');
			}
		});
	},
	/**
	 * [checkPermissionByFeatureCode To check whether user has access to that feature]
	 * @param  {[string]} feature_code [feature code]
	 * @param  {[int]} user_id      [user id]
	 * @return {[boolean]}              [true if user has access/ false if he doesn't have]
	 */
	checkPermissionByFeatureCode:function(feature_code,user_id){
		if(user_id==1){
			return true;
		}else{
			var query="select features.name from role_access join features on role_access.feature_id=features.feature_id join user_roles on role_access.role_id=user_roles.role_id where user_roles.user_id ="+user_id+" and features.feature_code ='"+feature_code+"' and features.is_active=1";
			db.query(query,{},function(err,res){
				if(err){
					return false;
				}else{
					if(res.length>0){
						return true;
					}else{
						return false;
					}
				}
			});
		}

	},
	/**
	 * [getUserHubId To get user warehouse information]
	 * @param  {[string]} customertoken [customer token]
	 * @return {[array]}               [user warehouse information]
	 */
	getUserHubId:function(customertoken){
		//console.log('getUserHubId');
		return new Promise((resolve,reject)=>{
			var hubId=[];
			if(customertoken!=''){
				var legalEntityData="select legal_entity_id,user_id from users u where u.password_token="+"'"+customertoken+"'"+" or u.lp_token="+"'"+customertoken+"'"+" limit 1";
				db.query(legalEntityData,{},function(err,res){
					if(err){
						//console.log('err',err);
					}else{
						if(res.length != 0){
							//console.log('res',res);
							var legalEntityId=res[0].hasOwnProperty('legal_entity_id')?res[0]['legal_entity_id']:0;
							var userId=res[0].hasOwnProperty('user_id')?res[0]['user_id']:0;
							if(legalEntityId==2){
								var permission="select object_id from user_permssion where user_id="+userId+" and permission_level_id=6 group by object_id";
								//console.log('permission',permission);

								db.query(permission,{},function(err,res2){
									if(err){
										consoel.log('error',err);
									}else{
										//console.log('permission',res2);

										if(res2.length!=0){
											var object_id_list='';
											for(let i=0;i<res2.length;i++){
												if(object_id_list!='')
												object_id_list+=',';
												object_id_list+=res2[i]['object_id'];
											}
											//console.log('object_id_list',object_id_list);
											var gethub="select GROUP_CONCAT(le_wh_id) as le_wh_id from legalentity_warehouses where dc_type>0 and status=1 and dc_type='118002' and bu_id in("+object_id_list+")";
											db.query(gethub,{},function(err,res3){
												if(err){
													console.log('error',err);
												}else{
													//console.log('gethubresult',res3);
													if(res3.length!=0){
														resolve(res3[0]['le_wh_id']);
													}else{
														reject('');
													}
												}
											});
										}
									}
								});
							}
							else if(legalEntityId>0){
								var hub_id="select spokes.le_wh_id from pjp_pincode_area LEFT JOIN customers ON customers.beat_id = pjp_pincode_area.pjp_pincode_area_id LEFT JOIN spokes   ON spokes.spoke_id = pjp_pincode_area.spoke_id  WHERE customers.le_id="+legalEntityId+" limit 1";
								db.query(hub_id,{},function(err,res4){
									if(err){
										console.log('error',err);
									}else{
										//console.log('gethubresult',res3);
										if(res4.length!=0){
											resolve(res4[0]['le_wh_id']);
										}else{
											reject('');
										}
									}
								});
							}

						}
					}
				});


			}
		})
		
	},
	/**
	 * [getBlockedData To get blocked brands, manf list]
	 * @param  {[string]} customertoken [customertoken]
	 * @return {[array]}               [To get blocked brands, manf list ]
	 */
	getBlockedData:function(customertoken){
		return new Promise((resolve,reject)=>{
			//console.log('customertoken',customertoken);
			var dcsdata;
			var hubsdata;
			var spokesdata;
			var beatsdata;
			module.exports.getBeatsByUserId(customertoken).then((beats)=>{
				beatsdata=beats;
				return module.exports.getSpokesByBeats(beats);
			}).then((spokes) =>{
				spokesdata=spokes;
				return module.exports.getSpokesByHubs(spokes);
			}).then((hubs)=>{
				hubsdata=hubs;
				return module.exports.getSpokesByDc(hubs);
			}).then((dcs)=>{
				dcsdata=dcs;
				module.exports.getBlockedList(dcsdata,hubsdata,spokesdata,beatsdata).then(data=>{
					
					console.log('&&&&',data);
					resolve(data);
				});
			}).catch(err=>{
				reject('');
			});
		})
		
	},
	getSpokesByBeats:function(beats){
		return new Promise((resolve,reject)=>{
			var spokes=[];
			//console.log('beats',beats.length);
			if(beats!='' && beats!= null && beats.length>0){
				//console.log('in if');
				var spokesData="select group_concat(distinct(spoke_id)) as spokes from pjp_pincode_area where pjp_pincode_area_id in ("+beats+") group by spoke_id";
				//console.log('spokesData',spokesData);
				db.query(spokesData,{},function(err,res){
					if(err){
						reject('');
					}else{
						if(res.length >0){
							//console.log('res',res);
							for(var i=0;i< res.length;i++){
								spokes.push(res[i]['spokes']);
							}
							//console.log(JSON.stringify(spokes));
							//console.log('data',spokes);
							resolve(spokes);
						}else{
							resolve(spokes);
						}
					}
				});
			}else{
				//console.log('in else');
				resolve(spokes);
			}
		});
		
	},
	getSpokesByHubs:function(spokes){
		return new Promise((resolve,reject)=>{
			var hubs=[];
			if(spokes!='' && spokes!= null && spokes.length>0){
				var hubsData="select group_concat(distinct(le_wh_id)) as hubs from spokes where spoke_id in ("+spokes+") group by spoke_id";
				//console.log('hubsData',hubsData);
				db.query(hubsData,{},function(err,res){
					if(err){
						reject('');
					}else{
						if(res.length >0){
							//console.log('res',res);
							for(var i=0;i< res.length;i++){
								hubs.push(res[i]['hubs']);
							}
							//console.log(JSON.stringify(hubs));
							resolve(hubs);
						}else{
							resolve(hubs);
						}
					}
				});
			}else{
				resolve(hubs);
			}
		});		
	},
	getSpokesByDc:function(hubs){
		return new Promise((resolve,reject)=>{
			var dcs=[];
			if(hubs!='' && hubs!= null && hubs.length>0){
				var dcsData="select group_concat(distinct(dc_id)) as dc from dc_hub_mapping where hub_id in ("+hubs+") group by dc_id";
				//console.log('dcsData',dcsData);
				db.query(dcsData,{},function(err,res){
					if(err){
						reject('');
					}else{
						if(res.length >0){
							//console.log('res',res);
							for(var i=0;i< res.length;i++){
								dcs.push(res[i]['dc']);
							}
							//console.log(JSON.stringify(dcs));
							resolve(dcs);
						}else{
							resolve(dcs);
						}
					}
				}); 
			}else{
				resolve(dcs);
			}
		})
	},
	getBlockedList: function(dcs,hubs,spokes,beats){
		return new Promise((resolve,reject)=>{

			var result=[];
			let temp=[];
			temp[0]={brands:[],manf:[]};

			if(dcs.length>0){ 
			    module.exports.processData('DC', dcs, temp).then((data)=>{
			    	//resolve(data);
			    	if(data.length>0){
			    		temp = module.exports.blocklistMerge(temp,data);
			    	}
			    });
			    if(hubs.length>0){
					module.exports.processData('HUB',hubs,temp).then((data)=>{
				    	if(data.length>0){
				    		temp = module.exports.blocklistMerge(temp,data);
				    	}
			    		
				    	if(spokes.length>0){
							module.exports.processData('SPOKE',spokes,temp).then((data)=>{
						    	if(data.length>0){
			    					temp = module.exports.blocklistMerge(temp,data);
						    	}
						    	if(beats.length>0){
									module.exports.processData('BEAT',beats,temp).then((data)=>{
								    	if(data.length>0){
			    							temp = module.exports.blocklistMerge(temp,data);
								    	}
										resolve(temp[0]);
									});
								}else{
									resolve(temp[0]);
								}
							});	
						}else if(beats.length>0){
							module.exports.processData('BEAT',beats,temp).then((data)=>{
						    	if(data.length>0){
			    					temp = module.exports.blocklistMerge(temp,data);
						    	}
								resolve(temp[0]);
							});
						}else{
							resolve(temp[0]);
						}
					});				
				}else if(spokes.length>0){
					module.exports.processData('SPOKE',spokes,temp).then((data)=>{
				    	if(data.length>0){
			    			temp = module.exports.blocklistMerge(temp,data);
				    	}
					    	if(beats.length>0){
								module.exports.processData('BEAT',beats,temp).then((data)=>{
							    	if(data.length>0){
			    						temp = module.exports.blocklistMerge(temp,data);
							    	}
									resolve(temp[0]);
								});
							}else{
									resolve(temp[0]);
							}
						});	
				}else if(beats.length>0){
						module.exports.processData('BEAT',beats,temp).then((data)=>{
					    	if(data.length>0){
	    						temp = module.exports.blocklistMerge(temp,data);
					    	}
							resolve(temp[0]);
						});
				}else{
					resolve(temp[0]);
				}
			}else if(hubs.length>0){
				module.exports.processData('HUB',hubs,temp).then((data)=>{
			    	if(data.length>0){
						temp = module.exports.blocklistMerge(temp,data);
			    	}
			    	if(spokes.length>0){
						module.exports.processData('SPOKE',spokes,temp).then((data)=>{
					    	if(data.length>0){
	    						temp = module.exports.blocklistMerge(temp,data);
					    	}
					    	if(beats.length>0){
								module.exports.processData('BEAT',beats,temp).then((data)=>{
							    	if(data.length>0){
			    						temp = module.exports.blocklistMerge(temp,data);
							    	}
									resolve(temp[0]);
								});
							}else{
								resolve(temp[0]);
							}
						});	
					}else if(beats.length>0){
						module.exports.processData('BEAT',beats,temp).then((data)=>{
					    	if(data.length>0){
	    						temp = module.exports.blocklistMerge(temp,data);
					    	}
							resolve(temp[0]);
						});
					}else{
						resolve(temp[0]);
					}
				});				
			}else if(spokes.length>0){
				module.exports.processData('SPOKE',spokes,temp).then((data)=>{
			    	if(data.length>0){
						temp = module.exports.blocklistMerge(temp,data);
			    	}
			    	if(beats.length>0){
						module.exports.processData('BEAT',beats,temp).then((data)=>{
					    	if(data.length>0){
	    						temp = module.exports.blocklistMerge(temp,data);
					    	}
							resolve(temp[0]);
						});
					}else{
						resolve(temp[0]);
					}
				});			    
			}else if(beats.length>0){
				module.exports.processData('BEAT',beats,temp).then((data)=>{
			    	if(data.length>0){
						temp = module.exports.blocklistMerge(temp,data);
			    	}
			    	resolve(temp[0]);
				});			    
			}else{
				resolve(temp[0]);
			}
		});
	},
	processData:function(scopeType,scopeIds,resultData){
		return new Promise((resolve,reject)=>{
			var response="select ref_type,ref_id from hub_product_mapping where scope_type= '"+scopeType+"' and scope_id in ("+scopeIds+")";
			console.log(response);
			db.query(response,{},function(err,res){
				if(err){
					//console.log('err',err);
					reject('');
				}else{
					var resultData=[];
					let brandItem=[];
					let manfitem=[];

					if(res.length>0){
						for(var i=0;i<res.length;i++){
							var type=res[i].hasOwnProperty('ref_type')?res[i]['ref_type']:'';
							var data=res[i].hasOwnProperty('ref_id')?res[i]['ref_id']:'';
							if(type=='brands'){
								brandItem.push(data);
							}if(type=='manufacturers'){
								manfitem.push(data);
							}
						}
						resultData.push({brands: brandItem,manf: manfitem});
						console.log('pradeepa',resultData);
						resolve(resultData);
					}else{
						//resultData.push({brands: brandItem,manf: manfitem});
						resolve(resultData);						
					}
				}
			});
		});
	},
	shopByBrand: function(le_wh_id,segmentId,offset_limit,offset,blockedList,customerType){
		//console.log('in shop by brand',blockedList);
		return new Promise((resolve,reject)=>{
			let brands =0;
			if(blockedList.brands.length > 0){
				brands=blockedList.brands;
				brands =  brands.join();
				console.log(typeof(brands),brands);
			}
			var shopByBrand="call getCPBrands_ByCust('"+le_wh_id+"',"+segmentId+","+offset_limit+","+offset+",'"+brands+"',"+customerType+")";
			var dataResult = [];
			db.query(shopByBrand,{},function(err,res){
				if(err){
					reject('');
				}else{
					if(res.length>0){
						for(let i=0;i<res[0].length;i++){
							let temp=[];
							temp.push(res[0][i]['image']);
							var object={
								'id':res[0][i]['id'],							
								'name':res[0][i]['name'],
								'image':temp,
								'is_sponsered':res[0][i]['is_sponsered'],
								'config_id':res[0][i]['config_id']
							}
							dataResult.push(object);
						}
						resolve(dataResult);

					}else{
						resolve('0');
					}
				}
			});
		});
	},

	shopByManufacturer:function(le_wh_id,segmentId,offset_limit,offset,blockedList,customerType){
		return new Promise((resolve,reject)=>{
			let manf=0;
			//console.log('in shop by manufacturer',blockedList);
			if(blockedList.manf.length > 0){
				manf=blockedList.manf;
				manf =  manf.join();
				console.log(typeof(manf),manf);
			}
			var shopByManf="call getCPManufactuers_ByCust('"+le_wh_id+"',"+segmentId+","+offset_limit+","+offset+",'"+manf+"',"+customerType+")";
			//console.log(shopByManf);
			var dataResult = [];
			db.query(shopByManf,{},function(err,res){
				if(err){
					console.log('err manf',err);
					reject('');
				}else{
					//console.log(res);
					if(res.length>0){
						for(let i=0;i<res[0].length;i++){
							let temp=[];
							temp.push(res[0][i]['image']);
							var object={
								'id':res[0][i]['id'],							
								'name':res[0][i]['name'],
								'image':temp
							}
							dataResult.push(object);
						}
						resolve(dataResult);

					}else{
						resolve('0');
					}
				}
			});
		});

	},
	ShopbyCategory:function(le_wh_id,segmentId,offset_limit,offset,customerType){
		return new Promise((resolve,reject)=>{
		var Shopbycategory="call getCPCategories_ByCust('"+le_wh_id+"',"+segmentId+","+offset_limit+","+offset+","+customerType+")";
		var dataResult = [];
		db.query(Shopbycategory,{},function(err,res){
			if(err){
				reject('');
			}else{
				if(res.length>0){
					for(let i=0;i<res[0].length;i++){
						if(res[0][i]['image']== null){
							res[0][i]['image']="http://s328.photobucket.com/user/mailebutor/media/Haldirams/BHUJIA%20SEV%201KG_zpsui8fyjds.jpg.html";
						}
						let temp=[];
						temp.push(res[0][i]['image']);
						var object={
							'id':res[0][i]['id'],							
							'name':res[0][i]['name'],
							'image':temp
						}
						dataResult.push(object);
					}
					resolve(dataResult);

				}else{
					resolve('0');
				}
			}
		});
		})
		

	},
	getProducts:function(product_id,le_wh_id,customerType){
		return new Promise((resolve,reject)=>{
			var result=[];

			if(customerType==3016){
				var query="CALL getCpProductsDIT("+product_id+","+le_wh_id+")";
				//console.log('error',query);
				db.query(query,{},function(err,res){
					if(err){
						result.push({'data':''});
						resolve(result);

					}else{
						if(res.length>0){
							var temp=[];
							for(var i=0;i<res[0].length;i++){
								//console.log('error',res[i]);
								temp.push(res[0][i]);
							}
							let data={'data':temp};
							result.push(data);
							resolve(result[0]);
						}else{
							resolve('')
						}

					}
				});
			}else{
				var query="CALL getCpProducts("+product_id+","+le_wh_id+")";
				//console.log('error',query);
				db.query(query,{},function(err,res){
					if(err){
						//console.log('error',query);
						result.push({'data':''});
						resolve('');

					}else{
						if(res.length>0){
							var temp=[];
							for(var i=0;i<res[0].length;i++){
								//console.log('error',res[i]);
								temp.push(res[0][i]);
							}
							let data={'data':temp};
							result.push(data);
							resolve(result[0]);
						}else{
							resolve('')
						}
					}
				});
			}
		},err=>{
			reject(err);
		})
			
	},
	addMappingDetails:function(inputData){
		var data={
			"object_id":"2",
			"sponsor_type":"16602",
			"object_type":"16606",
			"impression_cost":"10",
			"click_cost":"20",
			"from_date":"2018-09-09",
			"to_date":"2018-10-10",
			"priority":"6",
			"status": null,
			"approval_status": null,
			"mapping_id": null
		}
		var data=[2,16602,16606,10,20,"2018-09-09","2018-10-09",6,null,null,null];
		let query="insert into banner_config (object_id,sponsor_type,object_type,impression_cost,click_cost,from_date,to_date,priority,status,approval_status,mapping_id) VALUES ?";
		db.query(query,data,function(err,res){
			if(err){
				console.log(err);
			}else{
				//console.log('result',res);
			}
		});
	},
	blocklistMerge: function(temp,data){
		let result=[];
		console.log('branddddd',temp);
		console.log('manffff',data);
		let tempBrands = temp[0].brands;
		let databrands = data[0].brands;
		let brandresult = [...new Set([...tempBrands, ...databrands])];
		let tempManfs = temp[0].manf;
		let dataManfs = data[0].manf;
		let manfresult = [...new Set([...tempManfs, ...dataManfs])];
		result.push({brands:brandresult,manf:manfresult});
		return result;
	}


}