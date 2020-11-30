const db=require('../../dbConnection');
module.exports={
	getFilterData: async function(req,res){
        return new Promise((resolve,reject)=>{
        	try{
        		console.log(req);
        		var response=[];
        		if(req.hasOwnProperty('permissionLevelId') && req.permissionLevelId>0){
        			if(req.hasOwnProperty('user_id')){
        				var currentUserId=req.user_id
        			}else{
        				res.send('Please Send UserID');
        			}
        			module.exports.getPermissionLevelData(req.permissionLevelId).then(data1=>{
        				switch(data1)
        				{
        					case 'sbu' :
        					var sbuparams={'user_id':currentUserId,'permission_level':req.permissionLevelId};
        					module.exports.getWarehouseData(sbuparams).then(data2=>{
        						var warehousekeyvalue={'sbu':data2};
        						response.push(warehousekeyvalue);
        						return resolve(response);
        					});
        					break;
        				}
        			});
        		}else{
        			return reject("No Results found..")
        		}
        	}catch(err){
	            console.error(err);
	            return reject("No Results found..")
        	}
        	});
    },
	getWarehouseData:  function(req,res){
    	return new Promise(async(resolve,reject)=>{
    		try{
    			var resultset={};
    			var objectIds;
    			if(req.user_id>0 && req.permission_level>0)
    			{
    				var globalFeature=await module.exports.checkPermissionByFeatureCode('GLB0001',req.user_id);
    				var inActiveDCAccess=await module.exports.checkPermissionByFeatureCode('GLBWH0001',req.user_id);
    				if(req.hasOwnProperty('active') && req.active==0)
    				{
    					inActiveDCAccess=1;
    				}
    				var qry2="select group_concat(object_id) as object_id from user_permssion where user_id="+req.user_id+" and permission_level_id="+req.permission_level;
    				db.query(qry2,{},function(err,rows){
    					if(err){
    						return reject(err);
    					}

    					if(rows.length>0){
    						objectIds=rows[0].object_id.split(",");
    						if(!globalFeature)
    						{
    							var qry3="select GROUP_CONCAT(le_wh_id) as le_wh_id,dc_type from legalentity_warehouses where dc_type > 0";
	    						if(inActiveDCAccess==0)
	    						{
	    							qry3 +=' and status=1';
	    						}
    							if(objectIds.length==1 || objectIds.includes('0'))
    							{
                                    console.log(objectIds);
                                    console.log(objectIds.includes('0')+'=================');
                                    if(objectIds.includes('0')){
    								    qry3 +=" and dc_type in (118001,118002)";
                                    }else{
                                        qry3 += " and bu_id in ("+objectIds+")";
                                    }
    							}else{
    								qry3 +=" and bu_id in ("+objectIds+")";
    							}
    							qry3 += " group by dc_type";
    						}else if(globalFeature){
    							var qry3="select GROUP_CONCAT(le_wh_id) as le_wh_id,dc_type from legalentity_warehouses where dc_type > 0";
	    						if(inActiveDCAccess==0)
	    						{
	    							qry3 +=' and status=1';
	    						}
    							qry3 += " group by dc_type";
    						}
                            console.log(qry3);
    						db.query(qry3,{},async function(err1,rows1){
	    					if(err1){
	    						return reject(err1);
	    					}
	    					if(Object.keys(rows1).length >0){
								for (var i = 0; i < rows1.length; i++) {
									resultset[rows1[i].dc_type]= rows1[i].le_wh_id;
								}
								await resolve(resultset);
							}
							else{
								return reject("No Results found..")
							}
	    				});

    					}
    				});
    			}else{
    				return reject("No Results found..")
    			} 	
    		}catch(err){
	            console.error(err);
	            return reject("No Results found..");
        	}		
    	});
    },
    /*Funtion :getPermissionLevelData
	Input :permission level id
	output :get permission name using permssion level from permission_level table
    */
    getPermissionLevelData: function(req,res){
    	return new Promise((resolve,reject)=>{
    		try{
				var permissionName='';
				if(req>0)
				{
					var qry="select name from permission_level where permission_level_id="+req;
					db.query(qry,{},function(err,rows){
						if(err){
							return  reject(err);
						}
						if(rows.length >0){

							permissionName=rows[0].name;
							return resolve(permissionName);
						}
						else{
							return resolve(permissionName);
						}
					});
				}
    		}catch(err){
	            console.error(err);
	            return reject("No Results found..")
        	}
    	});
    },
    getDisplayNameByLeWhID: function(req,res){
    	return new Promise((resolve,reject)=>{
    		var displaynamequery="select le_wh_id,display_name,latitude,longitude,legal_entity_id,state,city, (6371 * ACOS (COS ( RADIANS("+req.latitude+") ) * COS( RADIANS( latitude ) ) * COS( RADIANS( longitude ) - RADIANS("+req.longitude+") ) + SIN ( RADIANS("+req.latitude+") ) * SIN( RADIANS( latitude ) ) ) ) AS distance from legalentity_warehouses where dc_type=118001 and status=1 HAVING distance < 20";
            // le_wh_id in ("+req.accessdclist+") and
            db.query(displaynamequery,{},function(err,rows){
    			if(err){
    				return reject(err);
    			}
    			if(rows.length>0){
    				return resolve(rows);
    			}else{
    				var emptyarray=[];
    				emptyarray.push({});
    				return resolve(emptyarray);
    			}
    		});
    	});
    },
    getBeatsByffId: function(userid,legalentityid,limit,offset){
    	return new Promise(async (resolve,reject)=>{
    		try{
	    			let flag=0;
		    		var allbeataccess=await module.exports.checkPermissionByFeatureCode('ALLBEAT1',userid);
		    		if(allbeataccess)
		    		{
		    			flag=1;
		    		}
		    		let getbeats = "CALL getBeatDetails("+userid+","+legalentityid+",NULL,"+flag+","+limit+","+offset+")";
                    db.query(getbeats,{},function(err,res){
						if(err){
							console.log(err);
							resolve([]);
						}else{
							if(res.length>0){
								resolve(res[0]);
							}else{
								resolve([]);
							}
						}
					});
			}catch(err){
	            console.error(err);
	            return reject("Please Try Again after sometime");
        	}
    		
    	});
    },
    getBeatsByffIdByserach: function(userid,legalentityid,beatkeyword){
    	return new Promise(async (resolve,reject)=>{
    		try{
	    			let flag=0;
		    		var allbeataccess=await module.exports.checkPermissionByFeatureCode('ALLBEAT1',userid);
		    		if(allbeataccess)
		    		{
		    			flag=1;
		    		}
		    		let getbeats = "CALL getBeatDetailsSearch("+userid+","+legalentityid+",NULL,"+flag+",'"+beatkeyword+"')";
					db.query(getbeats,{},function(err,res){
						if(err){
							console.log(err);
							resolve([]);
						}else{
							if(res.length>0){
								resolve(res[0]);
							}else{
								resolve([]);
							}
						}
					});
			}catch(err){
	            console.error(err);
	            return reject("Please Try Again after sometime");
        	}
    		
    	});
    },
    checkPermissionByFeatureCode:async function(feature_code,user_id){
    	return new Promise((resolve,reject)=>{
			if(user_id==1){
				resolve(true);
			}else{
				var query="select features.name from role_access join features on role_access.feature_id=features.feature_id join user_roles on role_access.role_id=user_roles.role_id where user_roles.user_id ="+user_id+" and features.feature_code ='"+feature_code+"' and features.is_active=1";
				db.query(query,{},function(err,res){
					if(err){
						resolve(false);
					}else{
						if(res.length>0){
							resolve(true);
						}else{
							resolve(false);
						}
					}
				});
			}
		});
	},
    getAllCustomers: async function(user_id,beatid,isbilled,offset,limit,search,flag,hub,spoke,sort){
        return new Promise((resolve,reject)=>{
            if(flag=='' && sort=='')
            {
                let sort=146006;
            }
                let retailersquery="select leg.business_legal_name AS company,leg.latitude,leg.longitude,leg.address1 AS address_1,leg.address2,leg.legal_entity_id,CONCAT(users1.firstname,' ',users1.lastname) as firstname,getRetailerCheck_in( leg.legal_entity_id) as check_in,users1.mobile_no AS telephone,users1.user_id AS customer_id,users1.password_token as customer_token,leg.legal_entity_type_id as buyer_type,cust.pincode as pincode";
            if(flag!=1)
            {
                retailersquery +=" ,IFNULL((select ROUND(uec.cashback,2) from users us join user_ecash_creditlimit uec ON uec.user_id=us.user_id where us.legal_entity_id=leg.legal_entity_id and us.is_parent=1 limit 1),0) as remain_bal";
            }
            retailersquery +=" from legal_entities AS leg join users as users1 on leg.legal_entity_id=users1.legal_entity_id";
            if(flag==1)
            {
                if(beatid!=='')
                {
                    if(beatid.indexOf(-1)!==-1)
                    {
                        retailersquery+=" join retailer_flat as cust on leg.legal_entity_id=cust.legal_entity_id";                        
                    }else{
                         retailersquery+=" join retailer_flat as cust on leg.legal_entity_id=cust.legal_entity_id and cust.beat_id in ("+$beat_id+")";
                    }
                }

                if(hub!=='' && spoke!=='')
                {
                    retailersquery+=" join pjp_pincode_area as pa on pa.pjp_pincode_area_id=cust.beat_id join spokes as sp on pa.spoke_id=sp.spoke_id"
                }

            }else{   
                   retailersquery+=" join retailer_flat as cust on leg.legal_entity_id=cust.legal_entity_id"; 
            
                if(beatid!=-1){
                    retailersquery+="  and cust.beat_id="+beatid;    
                }
            }
            retailersquery+=" where users1.is_active=1 and users1.is_parent=1 and (leg.legal_entity_type_id like '%30%' or leg.legal_entity_type_id like '%1014%' or leg.legal_entity_type_id like '%1016%')";
            if(isbilled==1)
            {
                var date = new Date().toISOString().split('T')[0];
                var retailersresult=retailersquery+" and leg.legal_entity_id not in (select go.cust_le_id from gds_orders as go where DATE(created_at) ="+date+" and go.created_by="+user_id+")";

            }else{
                var retailersresult=retailersquery;
            }
            if(search!=''){
                retailersresult+=" and leg.business_legal_name like '%"+search+"%' or users1.mobile_no like '%"+search+"%'";
            }
            if(hub!=='' && spoke!=='')
            {
                retailersresult+=" and FIND_IN_SET(sp.le_wh_id,'"+$hub+"')";
                retailersresult+=" and FIND_IN_SET(sp.spoke_id,'"+$spoke+"')";
            }
            if(sort!=''){
                if(sort==146001){
                    retailersresult+=" order by avg_bill_val asc";
                }else if(sort==146006){
                    retailersresult+=" order by avg_bill_val desc";
                }else if(sort==146002){
                    retailersresult+=" order by leg.business_legal_name asc";
                }else if(sort==146005){
                    retailersresult+=" order by leg.business_legal_name desc";
                }else if(sort==146003){
                    retailersresult+=" order by check_in asc";
                }else if(sort==146010){
                    retailersresult+=" order by remain_bal asc";
                }else if(sort==146009){
                    retailersresult+=" order by remain_bal desc";
                }else{
                    retailersresult+=" order by check_in desc";
                }
            }

            if(offset!=''){
                retailersresult+=" limit "+offset+","+limit; 
            }
            console.log(retailersresult);
            db.query(retailersresult,{},function(err,rows){
                if(err){
                    reject('error');
                }
                if(Object.keys(rows).length >0){
                    return resolve(rows);
                }
                else{
                    return resolve([]);
                }
            });
        });
    },
    insertIntoCallLogs:function(req,res){
       return new Promise((resolve,reject)=>{
       /* date=new Date();
        var day = date.getDate(); 
        var month = date.getMonth()+1; 
        var year = date.getFullYear();
        var presentday = year + "-"+ month +"-"+day;*/
        let current_datetime = new Date();
        let presentday = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
               
        var insertcalllogsdata="insert into ff_call_logs(ff_id,user_id,legal_entity_id,check_in,check_in_lat,check_in_long) values ('"+req.ff_id+"','"+req.customer_id+"','"+req.customerLegalEntityId+"','"+presentday+"','"+req.checkin_lat+"','"+req.checkin_long+"')";
       console.log(insertcalllogsdata);
        db.query(insertcalllogsdata,{},function(err,rows){
                if(err){
                    reject('error');
                }
                if(Object.keys(rows).length >0){
                     resolve(rows.insertId);
                }
                else{
                     resolve([]);
                }
            });
       })
    },
    checkffmShedules:function(req,res){
        return new Promise((resolve,reject)=>{
            var ffmschedulemapdetails="select count(fps_id) as fps_id from ffm_pjp_schedules where ff_id="+req.ff_id+" and date='"+req.assigned_date+"' and pincode='"+req.pincode+"' limit 1";
            console.log(ffmschedulemapdetails);
            db.query(ffmschedulemapdetails,{},function(err,rows){
                if(err){
                    reject('error');
                }
                if(Object.keys(rows).length >0){
                    var reslength=rows[0].fps_id;
                    if(reslength>0){
                        return resolve({"status":"success","message":"Data Found","data":reslength});
                    }else{
                        return resolve({"status":"failed","message":"No schedules found","data":[]});
                    }
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });   
        })
    },
    checkOutFFMS: function(req,res){
        return new Promise((resolve,reject)=>{
            let current_datetime = new Date();
            let presentday = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();
        
            var checkoutdatainsert="update ff_call_logs SET check_out='"+presentday+"',check_out_lat='"+req.checkout_lat+"',check_out_long='"+req.checkout_long+"',ffm_comments='"+req.feedback+"' where user_id='"+req.customer_id+"' and ff_id='"+req.ff_id+"' and log_id='"+req.ff_log_id+"'";
            //console.log(checkoutdatainsert);
            db.query(checkoutdatainsert,{},function(err,rows){
                if(err){
                    reject('error');
                }
                if(Object.keys(rows).length >0){
                    return resolve({"status":"success","message":"Data Found","data":"Data Saved Successfully"});
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":{}});
                }
            });
        })
    },

    getUserIdCustLeId: function(req,res){
        return new Promise((resolve,reject)=>{
          var getretailerdetailsbyleid="select user_id,legal_entity_id from users where legal_entity_id="+req.customer_le_id+" and mobile_no="+req.mobile;
            db.query(getretailerdetailsbyleid,{},function(err,rows){
                if(err){
                    reject('error');
                }
                if(Object.keys(rows).length >0){
                    return resolve(rows);
                }
                else{
                    return resolve([]);
                }
            });  
        })
    },
    getDCRetailers: function(req,res){
        return new Promise((resolve,reject)=>{
            var dcretailer="select * from users where legal_entity_id="+req.legal_entity_id+" and is_parent=1 and is_active=1";
console.log(dcretailer+"dcretailerdcretailerdcretailerdcretailer");
            db.query(dcretailer,{},function(err,rows){
                if(err){
                    reject('error');
                }
                if(Object.keys(rows).length >0){
                    return resolve(rows);
                }
                else{
                    return resolve([]);
                }
            });
        })
    },
    getCitiesList: function(req,res){
        return new Promise((resolve,reject)=>{
            //var getstatename="select name from zone where zone_id="+req.zone+" limit 1";
            // db.query(getstatename,{},function(err,rows){
            //     if(err){
            //         reject('error');
            //     }
                //if(Object.keys(rows).length >0){
                    //var getcitieslistbyzonename="select city_id,city from cities_pincodes where state like '%"+req.zone+"%' group by city,state";
                    var getcitieslistbyzonename="SELECT city_name,(6371 * ACOS (COS ( RADIANS("+req.latitude+") )* COS( RADIANS( latitude ) ) * COS( RADIANS( longitude ) - RADIANS("+req.longitude+") ) + SIN ( RADIANS("+req.latitude+") ) * SIN( RADIANS( latitude ) ) ) ) AS distance,latitude,longitude FROM `state_city_codes`  HAVING distance < 50 ORDER BY distance"
                    console.log(getcitieslistbyzonename);
                    db.query(getcitieslistbyzonename,{},function(err,cities){
                        if(err){
                            reject('error');
                        }
                        if(Object.keys(cities).length >0){
                            return resolve(cities);
                        }
                        else{
                            return resolve([]);
                        }
                    });          
                // }
                // else{
                //     return reject([]);
                // }
            //});
        })
    },

    checkFFDCSchedule: function(req,res){
        return new Promise((resolve,reject)=>{
            let current_datetime = new Date();
            let presentday = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
            var ffmschedulemapdetails="select count(fps_id) as fps_id from ffm_pjp_schedules where ff_id="+req.ff_id+" and date='"+presentday+"'";
            if(req.flag=="dc")
             {
                ffmschedulemapdetails +=" and le_wh_id="+req.le_wh_id;
             }else if(req.flag=='city'){
                ffmschedulemapdetails +=" and city='"+req.city+"'";
             }   
              ffmschedulemapdetails +=" limit 1";
            console.log(ffmschedulemapdetails);
            db.query(ffmschedulemapdetails,{},function(err,rows){
                console.log(rows+"rows=================");
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    var reslength=rows[0].fps_id;
                    if(reslength>0){
                        return resolve({"status":"success","message":"Data Found","data":reslength});
                    }else{
                        return resolve({"status":"failed","message":"No schedules found","data":[]});
                    }
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });
        })
    },

    trackCity: function(req,res){
        return new Promise((resolve,reject)=>{
            var ffmschedulemapdetails="insert into ffm_city_track(ff_call_logs_id,ff_id,city) values ("+req.ffcalllogid+","+req.ff_id+",'"+req.city+"')";
            console.log(ffmschedulemapdetails);
            db.query(ffmschedulemapdetails,{},function(err,rows){
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    return resolve(true);                    
                }
                else{
                    return resolve(false);
                }
            });
        })
    },

    getcitycoordinaytesbyName: function(req,res){
        return new Promise((resolve,reject)=>{
            var getcityname="select * from state_city_codes where city_name='"+req.city+"'";
            console.log(getcityname);
            db.query(getcityname,{},function(err,rows){
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    return resolve({"status":"success","message":"Data Found","data":rows});
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });
        })  
    },
    getScheduledPlansForFF: function(req,res){
        return new Promise((resolve,reject)=>{
            var scheduledplansquery="select group_concat(le_wh_id) as le_wh_id from ffm_pjp_schedules where ff_id= "+req.user_id+"  and date >= CURDATE()";
            //console.log(scheduledplansquery);
            db.query(scheduledplansquery,{},function(err,rows){
                if(err){
                    return reject(err);
                }
                if(rows.length>0 && rows[0].le_wh_id!=''){
                    var scheduledplansquerylewhids="select le_wh_id,display_name,latitude,longitude,legal_entity_id,state,city from legalentity_warehouses where dc_type=118001 and status=1 and le_wh_id in ("+rows[0].le_wh_id+")";
                        
                        db.query(scheduledplansquerylewhids,{},function(err,rows1){
                            if(err){
                                return reject(err);
                            }
                            if(rows1.length>0)
                            {
                                return resolve(rows1);    
                            }else{
                                var emptyarray=[];
                                emptyarray.push({});
                                return resolve(emptyarray);
                            }
                        });
                            
                }else{
                    var emptyarray=[];
                    emptyarray.push({});
                    return resolve(emptyarray);
                }
            });
        });
    },

    getScheduledCitiesList: function(req,res){
        return new Promise((resolve,reject)=>{
                    var getscheduledcitieslistbyzonename="SELECT GROUP_CONCAT(city) as city FROM `ffm_pjp_schedules` where ff_id ="+req.user_id+"  and date >= CURDATE()";
                    db.query(getscheduledcitieslistbyzonename,{},function(err,cities){
                        if(err){
                            reject('error');
                        }
                        if(Object.keys(cities).length >0){
                            if(cities[0].city!=null){
                                cities= cities[0].city.split(',');
                            }else{
                                cities='';
                            }
                            var getcitieslistbyzonename="SELECT city_name,latitude,longitude,0 as distance FROM `state_city_codes`  where city_name in (?)"
                                db.query(getcitieslistbyzonename,[cities],function(err,scheduledcities){
                                    if(err){
                                        reject('error');
                                    }
                                    if(Object.keys(scheduledcities).length >0){
                                        return resolve(scheduledcities);
                                    }
                                    else{
                                        return resolve([]);
                                    }
                                }); 
                        }
                        else{
                            return resolve([]);
                        }
                    });          
        })
    },
}