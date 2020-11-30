const banner = require('../models/bannermodel');
const config = require('../../config/config.json');
const redisClient = require('../../redisConnection');
module.exports = {
	index: function (req, res) {
		res.send('ok');
	},
	getCategories: function (req, res) {
		var status = 0;
		var result = [];
		var beat_id = 0;

		console.log('body', req.body.data);
		console.log('config', config['DB_DATABASE']);
		var cache_string = config['DB_DATABASE'];
		//console.log('req',req);
		if (Object.keys(req.body.data).length > 0) {
			var params = req.body.data;
			params = JSON.parse(params);
			if (params.hasOwnProperty('sort_id')) {
				var sort_id = params.sort_id;
			} else {
				var sort_id = '';
			}
			if (params.hasOwnProperty('le_wh_id') && params['le_wh_id']) {
				var le_wh_id = params['le_wh_id'];
				var customer_type = params.hasOwnProperty('customer_type') ? params['customer_type'] : '';
				console.log(params['segment_id']);

				if (params.hasOwnProperty('segment_id') && params['segment_id']) {

					console.log('in if');
					var segment_id = params['segment_id'];
					if (params.hasOwnProperty('brand_id') && params['brand_id']) {
						banner.checkBrandID(params['brand_id']).then((data) => {
							if (data < 1) {
								res.send({ 'status': 'failed', 'message': 'wrong brand id' });
							}
							else {
								//res.send({"count":data});
								var brand_id = params['brand_id'];
								if (params.hasOwnProperty('offset') && params['offset']) {
									var offset = params['offset'];
									if (params.hasOwnProperty('offset_limit') && params['offset_limit']) {
										var offset_limit = params['offset_limit'];
										if (params.hasOwnProperty('customer_token')) {
											banner.checkCustomerToken(params['customer_token']).then((data) => {
												if (data > 0) {
													var customer_token = params['customer_token'];
													console.log('success');
													banner.getBeatsByUserId(customer_token).then((data) => {
														//console.log(data);
														if (!data) {
															res.send({ 'status': 'failed', 'message': 'User does not have any beats assigned Error_Code : 8001' });
														}
														var beat_id = data;
														res.send(data);

													});
												} else {
													var customer_token = '';
													console.log('hello');
												}
											});
										} else {
											var customer_token = '';

										}

									} else {
										res.send({ 'status': 'failed', 'message': 'offset limit is required' });
									}
								} else {
									res.send({ 'status': 'failed', 'message': 'offset is required' });
								}
							}
							if (brand_id) {
								if (sort_id == '') {
									sort_id = -1;
								}
								var keystring = false;
								if (keystring) {

								} else {

								}
							}
						}).catch(err => {
							console.log(err);
						});

					}
				} else {
					res.send('1');
				}
			} else {
				res.send('2');
			}



		} else {
			console.log('empty');
			//res.send()
		}

	},
	featureProducts: function (req, res) {

		var brandData;
		var manfData;
		var catData;
		if (Object.keys(req.body.data).length > 0) {
			var params = req.body.data;
			params = JSON.parse(params);
			var flag = '';
			if (params.hasOwnProperty('flag')) {
				if (params.flag != '') {
					flag = params['flag'];
					console.log('flag', flag);
				}
			}
			var customer_token = '';
			var hub_id = 0;
			if (params.hasOwnProperty('customer_token')) {
				banner.checkCustomerToken(params['customer_token']).then((data) => {

					if (data > 0) {
						customer_token = params['customer_token'];
						//console.log('between',customer_token);
						/*if(params.hasOwnProperty('hub_id')){
							hub_id=params['hub_id'];
						}else{
							banner.getUserHubId(params['customer_token']).then((data2)=>{
								hub_id=data2;
							},err1=>{
								console.log('err in getUserHubId',err1);
							});
						}*/
					} else {
						customer_token = '';
						hub_id = 0;
					}
				}, err2 => {
					res.send({ 'status': 'failed', 'message': 'invalid token', data: [] });
				});
			} else {
				customer_token = '';
				hub_id = 0;
			}
			// setTimeout(()=>{
			//console.log('controller customertoken',customer_token);
			banner.getBlockedData(customer_token).then((data3) => {
				//console.log('data3',data3);
				var blockedData = { brands: [0], manf: [0] };
				if ((params.hasOwnProperty('segment_id') && params['segment_id'] != '') && (params.hasOwnProperty('le_wh_id') && params['le_wh_id'] != '')) {
					//console.log('helloo');
					var customer_type = params.hasOwnProperty('customer_type') ? params['customer_type'] : '';
					let brandpromise = banner.shopByBrand(params['le_wh_id'], params['segment_id'], params['offset_limit'], params['offset'], blockedData, customer_type);
					let manfpromise = banner.shopByManufacturer(params['le_wh_id'], params['segment_id'], params['offset_limit'], params['offset'], blockedData, customer_type);
					let categorypromise = banner.ShopbyCategory(params['le_wh_id'], params['segment_id'], params['offset_limit'], params['offset'], customer_type);

					Promise.all([brandpromise, manfpromise, categorypromise]).then(dataResult => {
						brandData = dataResult[0];
						manfData = dataResult[1];
						catData = dataResult[2];

						if (params.hasOwnProperty('flag') && (params['flag'] == 1 || params['flag'] == 2 || params['flag'] == 3)) {
							var message = '';
							var result;
							if (params['flag'] == 1) {
								message = "ShopbyBrand";
								result = brandData;
							} else if (params['flag'] == 2) {
								message = "ShopbyManufacturer";
								result = manfData;
							} else if (params['flag'] == 3) {
								message = "ShopbyCategory";
								result = catData;
							}
							var resultData = [];
							//console.log('message',message);
							//console.log('result',result.length);
							if (result.length > 0) {
								//console.log('hellooo');
								var some = { 'status': 'success', 'message': message, 'data': result };
								resultData.push(some);
								//console.log('result',resultData);

								res.send(resultData[0]);
							} else {
								var error = [];
								var some = { 'status': 'failed', 'message': 'No Data Found!!', 'data': [] };
								res.send(err.push(some));
							}

						} else {
							if (brandData.length == 0) {
								brandData = [];
							} else if (manfData.length == 0) {
								manfData = [];
							} else if (catData.length == 0) {
								catData = [];
							}
							var finalArray = [];

							var flagone = { 'flag': 1, 'display_title': 'Shop By Brands', 'key': 'brand_id', 'items': brandData };
							var flagtwo = { 'flag': 2, 'display_title': 'Shop By Manufacturer', 'key': 'manufacturer_id', 'items': manfData };
							var flagthree = { 'flag': 3, 'display_title': 'Shop By Category', 'key': 'category_id', 'items': catData };
							finalArray.push(flagone);
							finalArray.push(flagtwo);
							finalArray.push(flagthree);
							finalResult = [];
							finalResult.push({ 'status': 'success', 'message': 'getFeaturedProducts', 'data': finalArray })
							res.send(finalResult[0]);

						}
					}, err => {
						res.send({ 'status': 'success', 'message': 'getFeaturedProducts', 'data': [] })
						console.log('err in category', err);
					});
					/*banner.shopByBrand(params['le_wh_id'],params['segment_id'],params['offset_limit'],params['offset'],blockedData,customer_type).then((data4)=>{
					    //console.log('all_top_brands4',data4);
					    brandData=data4;
					    banner.shopByManufacturer(params['le_wh_id'],params['segment_id'],params['offset_limit'],params['offset'],blockedData,customer_type).then((data5)=>{
						    //console.log  ('all_top_brands5',data5);
						    manfData=data5;
						    banner.ShopbyCategory(params['le_wh_id'],params['segment_id'],params['offset_limit'],params['offset'],customer_type).then((data6)=>{
							    //console.log('all_top_brands6',data6);
							    catData=data6;
						   },err5=>{
									console.log('err in category',err5);
						   });
					   },err4=>{
							 console.log('err in manf',err4);
					   });
				   },err3=>{
					    console.log('err in shopByBrand',err3);
				   });*/


				} else {
					var error = [];
					some = { 'status': 'failed', 'message': 'legalWarehouseId or segmentId is not set', 'data': [] };

					res.send(err.push(some));
				}

			}, err => {
				res.send({ 'status': 'failed', 'message': 'invalid token', data: [] });
			});
			//},500)

		}
	},
	getOfflineProducts: function (req, res) {
		console.log('reqbody', req.body.data);
		if (Object.keys(req.body.data).length > 0) {
			var parameters = req.body.data;
			parameters = JSON.parse(parameters);
			var le_wh_id;
			var product_ids;
			if (parameters.hasOwnProperty('le_wh_id') && parameters['le_wh_id'] != '') {
				le_wh_id = parameters['le_wh_id'];
				le_wh_id = "'" + le_wh_id + "'";
			} else {
				le_wh_id = 1;
			}

			if (parameters.hasOwnProperty('product_ids')) {
				product_ids = parameters['product_ids'];
				product_ids = "'" + product_ids + "'";
			} else {
				let result = [];
				var data = {
					'status': 'failed',
					'message': 'product id required',
					'data': []
				};
				result.push(data);
				res.send(result);
			}
			var customertype = parameters.hasOwnProperty('customer_type') ? parameters['customer_type'] : '';
			console.log('product_id', product_ids);
			banner.getProducts(product_ids, le_wh_id, customertype).then((dataResult) => {
				res.send({ 'status': 'success', 'message': 'getOfflineProducts', 'data': dataResult });
			});
		}
	},
	addBanner: function (req, res) {
		console.log('request body', req.body.data);
		if (Object.keys(req.body.data).length > 0) {

			var inputData = JSON.parse(req.body.data);
			banner.addMappingDetails(inputData);/*.then((data)=>{

			});*/
		}
	},	
	fileUploadToS3 : function (req, res) {
		const singleUpload = upload.single('img');
        singleUpload(req,res,function(err,data){
        	console.log(req.files);
			if(err)
			return res.status(422).send({errors: [{title: 'Image Upload Error', detail: err.message}] });
			//return res.json({'imageUrl': req.file.location}); 
			console.log(req.files);
			res.send({status:'success',message:'s3upload',data:{url:req.files.img[0].location}});
		});
	},
	getRedis: function(req,res){
		console.log('a');		
		redisClient.set('testkey','abc',(err,res)=>{
			if(err)
			console.log('err',err);
			else{
				console.log('res',res);
				redisClient.get('testkey',(err1,res1)=>{
					if(err1)
					console.log('err1',err1);
					else{
						console.log('res1',res1);
						redisClient.keys('*',(err2,res2)=>{
							if(err2)
								console.log('err2',err2);
								console.log('res2',res2);
							
						})
					}

				})
			}
		});
	}

}