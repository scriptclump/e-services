const db= require('../../dbConnection');
const redis= require('../../redisConnection');

//console.log(redis);
module.exports={

	getSuggestionInCart: async function(req,res){
		return new Promise(async (resolve,reject)=>{
			var productsArray=[];
			let tempArray =[];
			var promotions=[];

			if(req){
				req=JSON.parse(req);
				
				//console.log(req);
				if(req.hasOwnProperty('cart_value')){
					productsArray=req['products'].map((proddata,index)=>{
						for(key in proddata){
							return +(key);
						}
					});
					var date = module.exports.getCurrentDate();
					var promotions=[];
					var lewhid=req.le_wh_id.split(',');
					lewhid = lewhid.join('|');
					var query="select wh_id,cbk_label,brand_id,range_from,product_value,cbk_value,product_group_id,excl_prod_group_id,excl_brand_id,excl_category_id,manufacturer_id,excl_manf_id from promotion_cashback_details where FIND_IN_SET("+req.customer_type+",customer_type)  AND CONCAT(',', wh_id, ',') REGEXP ',("+lewhid+"),' and ? between start_date and end_date and cbk_status=1 and is_self in ("+req.self_order+",2) AND ((? BETWEEN range_from AND range_to) OR ?<range_from)order by range_from asc";
					db.query(query,[date,req.cart_value,req.cart_value],async function(err,rows){
						if(err){
							console.log(err);
						}
						if(rows && rows.length>0){
							var index=0;
							//rows.forEach(async data=>{
								await Promise.all(rows.map(async (data, key) => {
								//console.log('aaaaaaaaaaaaaa',rows.length);
								await module.exports.CalculateEachPromotion(data,productsArray,req).then(async (res)=>{
									//console.log('bbbbbbbbbbbbbb',res);
									if( res && !(Object.keys(res).length === 0 && res.constructor === Object)){
										console.log(res);
										if(res.length>0){
											promotions[promotions.length]=Object.assign({}, ...res);//res;
										}
									}
									if(rows.length-1==index){
										var Result={status:200,message:"success",data:promotions};
										resolve(Result);
									}else{
										index++;
									}
								});

							}));
						}else{
							var Result={status:200,message:"success",data:[]};
							resolve(Result);
						}
					});					
				}
			}
		});
	},
	getCurrentDate:function(){
		var date = new Date(),
        month = '' + (date.getMonth() + 1),
        day = '' + date.getDate(),
        year = date.getFullYear();

	    if (month.length < 2) month = '0' + month;
	    if (day.length < 2) day = '0' + day;

	    return [year, month, day].join('-');
	},
	CalculateEachPromotion:async function(data,productsArray,input){
		return new Promise(async (resolve,reject)=>{
			//input['products']=JSON.parse(JSON.stringify(input['products']));
			
			var lewhid=input['le_wh_id'].split(',');
			Promise.all(lewhid.map(async (value, key) =>module.exports.loopingthroughPromotionsWarehouse(value, data,productsArray,input))).then(promisedata => {
                promisedata=promisedata.filter(value => Object.keys(value).length !== 0);
                resolve(promisedata);
            }); 

		});
		
	},

	loopingthroughPromotionsWarehouse: function (value, data,productsArray,input) {
        return new Promise(async (resolve, reject) => {
        	var promotions={};
        		var le_wh_id=value;//lewhid[key];
			if((data['wh_id'].split(',')).includes(le_wh_id)){
			var getProducts = "SELECT pf.`product_id` FROM products_inventory_flat pf JOIN product_cpenabled_dcfcwise pc ON pf.`product_id`=pc.`product_id`	JOIN inventory i ON pc.`le_wh_id`=i.`le_wh_id` AND i.soh-(i.`order_qty`+i.`reserved_qty`)>0 	AND i.`le_wh_id` = pc.`le_wh_id`AND i.`product_id` = pf.`product_id` AND pc.`cp_enabled` = 1 AND pc.`is_sellable` = 1 JOIN `wh_category_map` wcm ON  wcm.category_id=pf.category_id  AND wcm.le_wh_id=(SELECT hub_id FROM dc_hub_mapping WHERE dc_id IN ('"+le_wh_id+"')) WHERE pc.le_wh_id IN (?)";
			if(data['brand_id']!='0' && data['brand_id']!=null && data['brand_id']!='null'){
				 getProducts +=" AND pf.`brand_id` IN ("+data['brand_id']+")";
			}
			if(data['excl_brand_id']!='0' && data['excl_brand_id']!=null && data['excl_brand_id']!='null'){
				 getProducts +=" AND pf.`brand_id` NOT IN ("+data['excl_brand_id']+")";
			}
			if(data['manufacturer_id']!='0' && data['manufacturer_id']!=null && data['manufacturer_id']!='null'){
				 getProducts +=" AND pf.`manufacturer_id` IN ("+data['manufacturer_id']+")";
			}
			if(data['excl_manf_id']!='0' && data['excl_manf_id']!=null && data['excl_manf_id']!='null'){
				 getProducts +=" AND pf.`manufacturer_id` NOT IN ("+data['excl_manf_id']+")";
			}
			if(data['excl_category_id']!='0' && data['excl_category_id']!=null && data['excl_category_id']!='null'){
				 getProducts +=" AND pf.`category_id` NOT IN ("+data['excl_category_id']+")";
			}
			if(data['product_group_id']!='0' && data['product_group_id']!=null && data['product_group_id']!='null'){
				 getProducts +=" AND pf.`product_group_id` IN ("+data['product_group_id']+")";
			}
			if(data['excl_prod_group_id']!='0' && data['excl_prod_group_id']!=null && data['excl_prod_group_id']!='null'){
				 getProducts +=" AND pf.`product_group_id` NOT IN ("+data['excl_prod_group_id']+")";
			}
			db.query(getProducts,[le_wh_id],async function(err,rows){
				if(err){
					console.log(err);
					resolve(promotions);
				}else{
					if(rows){
						let sumofProductVal=0;
						let offerProductIds =rows.map(data=>data['product_id']);
						let ValidProductIds=offerProductIds.filter(id=>{
							return productsArray.indexOf(id)!=-1;
						});
						if(ValidProductIds.length > 0){
							if(data['product_value']){
								input['products'].forEach(data=>{
									let pid=Object.keys(data)[0];
									productid=+(pid);									
									if(ValidProductIds.indexOf(productid)!=-1){
										sumofProductVal+= +(data[pid]);
									}
								});
								if(data['product_value']>sumofProductVal){
									if(offerProductIds.length > 0){
										let temp=data['range_from']-sumofProductVal;
										promotions['promotion']="Add below product(s) worth of Rs."+Math.round(temp*100)/100 +"/- to get "+data['cbk_value']+"% discount on total bill";
										promotions['label']=data['cbk_label'];
										offerproducts=offerProductIds.join();
										console.log(offerProductIds.join());
										var productInfo="select product_id,product_title,thumbnail_image from products where product_id in ("+offerproducts+")";
										
										db.query(productInfo,{},function(err,productrows){
											if(err){
												console.log(err);
											}else{
												promotions['products']=productrows;
											}
											resolve(promotions);
										});
									}else{
										resolve(promotions);
									}

								}else{
									let temp=data['range_from']-sumofProductVal;//input.cart_value;
									if(temp>0){
										promotions['promotion']="Add product(s) worth of Rs."+Math.round(temp*100)/100+"/- to get "+data['cbk_value']+"% discount on total bill";
										promotions['label']=data['cbk_label'];
										if(offerProductIds.join().length > 0){
											offerproducts=offerProductIds.join();
											//promotions['promotion']="Add below product(s) worth of Rs."+Math.round(temp*100)/100 +"/- to get "+data['cbk_value']+"% discount on total bill";
											promotions['label']=data['cbk_label'];
											var productInfo="select product_id,product_title,thumbnail_image from products where product_id in ("+offerproducts+")";
											
											db.query(productInfo,{},function(err,productrows){
												if(err){
													console.log(err);
												}else{
													console.log(data,'datadata');
													promotions['products']=productrows;
													resolve(promotions);
												}
												
											});
										}else{
											resolve(promotions);
										}
									}else{
										resolve(promotions);
									}
								}
							}
						}else{
							let temp=data['range_from']-sumofProductVal;
							
							if(offerProductIds.join().length > 0){
								offerproducts=offerProductIds.join();
								console.log(offerProductIds.join());
								promotions['promotion']="Add below product(s) worth of Rs."+Math.round(temp*100)/100 +"/- to get "+data['cbk_value']+"% discount on total bill";
								promotions['label']=data['cbk_label'];
								var productInfo="select product_id,product_title,thumbnail_image from products where product_id in ("+offerproducts+")";
								
								db.query(productInfo,{},function(err,productrows){
									if(err){
										console.log(err);
									}else{
										promotions['products']=productrows;
									}
									resolve(promotions);
								});
							}else{
								resolve(promotions);
							}
						}
					}else{
						resolve(promotions);
					}
				}
			});
		}else{
			resolve(promotions);
		};
		});
		
	},
	getPromotionsFromRedis: function(date){
		return new Promise((resolve,reject)=>{
			// redis.get('promotions',(err,res)=>{
			// 	console.log('err',err);
			// 	if(res){
			// 		let res2= JSON.parse(res);
			// 		resolve(res2);
			// 	}else{
					var query="select cbk_label, brand_id, range_from, range_to, product_value, cbk_value, customer_type, wh_id, is_self  from promotion_cashback_details where cbk_status=1 and ? between start_date and end_date and cbk_status=1 order by range_from asc";
					//console.log(err);
					db.query(query,[date],function(err,rows){
						if(err){		
							console.log(err);
							resolve([]);
						}else{
							let res1 = JSON.stringify(rows);
							//redis.set('promotions',res1,(error,result)=>{

								resolve(rows);
							//});
						}
					});
				//}
			//});
		})
		
	},
	getQualifiedPromotion: function(item,le_wh_id,customer_type,self_order,cart_val){
		return new Promise((resolve,reject)=>{
			console.log('qualify',typeof le_wh_id,le_wh_id,typeof item.wh_id,item.wh_id,item.wh_id.indexOf(le_wh_id),item.wh_id.includes(item.le_wh_id));
			if(item.wh_id.indexOf(le_wh_id)!=-1 && item.customer_type.indexOf(customer_type)!=-1 && (item.is_self==self_order || item.is_self==2)){
				console.log('^^^^^^',item);
				if((item.range_from<=cart_val && item.range_to>= cart_val)|| cart_val<=item.range_from ){
					console.log('&&&&&&&&&&&&&',item);
					resolve(item);
				}else{
					resolve(null);
				}
			}else{
				resolve(null);
			}
		})
		
	}	 
}