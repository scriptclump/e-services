const cart = require('../models/cartmodel');
const banner = require('../models/bannermodel');
module.exports = {
	/*addCart:function(req,res){
		cart.addCartDetails(req.body.data).then(result=>{

		});
	}*/
	saveFeedbackReasons: function (req, res) {
		if (req.body.data) {
			var data = req.body.data;
			console.log(data);
			data = JSON.parse(data);
			var flag = data.hasOwnProperty('flag') ? data['flag'] : '';
			var comments = data.hasOwnProperty('comments') ? data['comments'] : '';
			var feedback_groupid = data.hasOwnProperty('feedback_groupid') ? data['feedback_groupid'] : '';
			var feedback_id = data.hasOwnProperty('feedback_id') ? data['feedback_id'] : '';
			var legal_entity_id = data.hasOwnProperty('legal_entity_id') ? data['legal_entity_id'] : '';
			var sales_token = data.hasOwnProperty('sales_token') ? data['sales_token'] : '';
			var latitude = data.hasOwnProperty('latitude') ? data['latitude'] : '';
			var longitude = data.hasOwnProperty('longitude') ? data['longitude'] : '';
			var user_id = data.hasOwnProperty('user_id') ? data['user_id'] : '';
			var activity = data.hasOwnProperty('activity') ? data['activity'] : '';
			var cart_data = data.hasOwnProperty('cart_data') ? data['cart_data'] : '';
			var le_wh_id = data.hasOwnProperty('le_wh_id') ? data['le_wh_id'] : '';

			var feedback_pic = '';
			var feebackaudio = '';
			if (flag == 1) {
				banner.checkCustomerToken(sales_token).then(data => {
					if (data > 0) {
						ff_id = cart.getUserIdFromToken(sales_token).then(userdet => {
							console.log(userdet);
							if (userdet.length > 0) {
								if (le_wh_id != '') {
									var input = [
										{
											'ff_id': userdet[0]['user_id'],
											'legal_entity_id': legal_entity_id,
											'feedback_groupid': feedback_groupid,
											'feedback_id': feedback_id,
											'comments': comments,
											'feedback_pic': feedback_pic,
											'feedback_audio': feebackaudio,
											'sales_token': sales_token,
											'activity': activity,
											'latitude': latitude,
											'longitude': longitude,
											'user_id': user_id,
											'cart_data': cart_data,
											'le_wh_id': le_wh_id
										}
									];
									if (feedback_groupid) {
										cart.saveFeedbackReasons(input).then(result1 => {
											console.log(result1);
										});
									}
									cart.getLatLongDetails(input).then(latlong => {
										console.log('latlng', latlong);
										if (latlong.longitude != '' && latlong.latitude != '') {
											input[0]['latitude'] = latlong.latitude;
											input[0]['longitude'] = latlong.longitude;
											cart.insertFFComments(input).then(result2 => {
												//var ffcomment = 
												if (result2) {
													res.send(result2);
												} else {
													res.send({ status: 'success', message: 'Not Saved', data: [] })
												}
											});
										} else {
											res.send({ status: 'failed', message: 'Please send latitude & longitude', data: [] });
										}
									});
								} else {
									res.send({ status: 'failed', message: 'Please send warehouse id', data: [] });
								}
							} else {
								res.send({ status: 'failed', message: 'Your Session Has Expired. Please Login Again.', data: [] });
							}
						});
					} else {
						res.send({ status: 'session', message: 'Your Session Has Expired. Please Login Again.', data: [] });
					}
				});

			} else {
				if (sales_token != '') {
					if (feedback_groupid != '') {
						if (feedback_id != '') {
							if (legal_entity_id != '') {
								if (le_wh_id != '') {
									banner.checkCustomerToken(sales_token).then(data => {
										if (data > 0) {
											ff_id = cart.getUserIdFromToken(sales_token).then(userdet => {
												var input = [
													{
														'ff_id': userdet[0]['user_id'],
														'legal_entity_id': legal_entity_id,
														'feedback_groupid': feedback_groupid,
														'feedback_id': feedback_id,
														'comments': comments,
														'feedback_pic': feedback_pic,
														'feedback_audio': feebackaudio,
														'sales_token': sales_token,
														'activity': activity,
														'latitude': latitude,
														'longitude': longitude,
														'user_id': user_id,
														'le_wh_id': le_wh_id
													}
												];
												if (feedback_groupid) {
													cart.saveFeedbackReasons(input).then(result1 => {
														console.log(result1);
														if (result1.data.length > 0) {
															res.send(result1);
														} else {
															res.send({ status: 'success', message: 'Not saved', 'data': [] });
														}
													});
												}
											});

										} else {
											res.send({ status: 'session', message: 'Your Session Has Expired. Please Login Again.', data: [] });
										}
									});
								} else {
									res.send({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7005", data: [] });
								}

							} else {
								res.send({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7007", data: [] });
							}
						} else {
							res.send({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7009", data: [] });
						}
					} else {
						res.send({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7010", data: [] });
					}
				} else {
					res.send({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7008", data: [] });
				}
			}

		}
	},
	checkin: function (req, res) {
		if (req.body.data) {
			var data = req.body.data;
			data = JSON.parse(data);
			var customer_token = data.hasOwnProperty('customer_token') ? data['customer_token'] : '';
			var salesUserId = data.hasOwnProperty('sales_user_id') ? data['sales_user_id'] : '';
			var companyLegalEntityId = data.hasOwnProperty('sales_legal_entity_id') ? data['sales_legal_entity_id'] : '';
			var customerLegalEntityId = data.hasOwnProperty('legal_entity_id') ? data['legal_entity_id'] : '';

			if (customer_token != '') {
				if (customerLegalEntityId != '') {
					if (companyLegalEntityId != '') {
						banner.checkCustomerToken(customer_token).then(data => {
							if (data > 0) {
								cart.validParentChildRelation({ cust_le_id: customerLegalEntityId, ff_le_id: companyLegalEntityId, user_id: salesUserId }).then(data2 => {
									console.log(data2['status']);
									if (data2['status'] != 'success') {
										res.send(data2);
									} else {
										cart.getUserIdFromLeid(customerLegalEntityId).then(userdet => {
											if (userdet.length > 0) {
												cart.checkValidRelation({ 'ff_id': salesUserId, 'cust_le_id': customerLegalEntityId, 'user_id': userdet[0]['user_id'] }).then(data3 => {
													res.send(data3);
												});
											} else {
												res.send({ status: 'failed', message: 'Your Session Has Expired. Please Login Again.', data: [] });
											}
										})

									}
								})
							} else {
								res.send({ status: 'session', message: 'Your Session Has Expired. Please Login Again.', data: [] });
							}
						});
					} else {
						res.send({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7007", data: [] });
					}
				} else {
					res.send({ status: 'failed', message: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7007", data: [] });
				}
			} else {
				res.send({ status: 'failed', message: 'Please provide authorized token Error_Code : 3002', data: [] });

			}

		}

	},
	saveCartData: async function (req, res) {
		if (req.body.data) {
			var data = req.body.data;
			data = JSON.parse(data);
			let warehouseOne = data.cart_data[0].warehouseId;
			let warehouseTotal = 0;
			let productList = [];
			let minimumOrderValue;
			let secondWarehouseTotal = 0;
			let secondWhIdProductList = [];
			let secondWhIdMinimumOrderValue;
			let categoryName = "";
			const promises = data.cart_data.map(async (p) => {
				//			for (let p in data.cart_data) {
				if (p.warehouseId == warehouseOne) {
					warehouseTotal = warehouseTotal + parseFloat(p.totalPrice);
					productList.push(p.productId);
					minimumOrderValue = p.minimun_order_value;
					categoryName = await cart.getCategoryName(p.cat_id)
				} else if (p.warehouseId != warehouseOne) {
					secondWarehouseTotal = secondWarehouseTotal + parseFloat(p.totalPrice);
					secondWhIdProductList.push(p.productId);
					secondWhIdMinimumOrderValue = p.minimun_order_value;
					categoryName = await cart.getCategoryName(p.cat_id);
				} else {
					categoryName = await cart.getCategoryName(p.cat_id);
				}
			});
			const Resolvepromise = await Promise.all(promises);
			if (warehouseTotal > minimumOrderValue && secondWarehouseTotal > secondWhIdMinimumOrderValue) {
				cart.addnewItemsToCart(data).then(result => {
					res.status(200).json({ 'status': 'success', 'message': "Minimum Ordervalue should be " + minimumOrderValue + " Rupees", 'data': { 'product': productList }, 'OrderAmount': warehouseTotal, 'minimumBillValue': minimumOrderValue, "categoryName": categoryName, "flag": 0 });
					//res.send(result);
				});
			} else if (warehouseTotal < minimumOrderValue) {
				cart.addnewItemsToCart(data).then(result => {
					res.status(200).json({ 'status': 'success', 'message': "Minimum Ordervalue should be " + minimumOrderValue + " Rupees", 'data': { 'product': productList }, 'OrderAmount': warehouseTotal, 'minimumBillValue': minimumOrderValue, "categoryName": categoryName, "flag": 1 });
				})
			} else if (secondWarehouseTotal < secondWhIdMinimumOrderValue) {
				cart.addnewItemsToCart(data).then(result => {
					res.status(200).json({ 'status': 'success', 'message': "Minimum Ordervalue should be " + secondWhIdMinimumOrderValue + " Rupees", 'data': { 'product': secondWhIdProductList }, 'OrderAmount': secondWarehouseTotal, 'minimumBillValue': secondWhIdMinimumOrderValue, "categoryName": categoryName, "flag": 1 });
				})
			} else {
				cart.addnewItemsToCart(data).then(result => {
					res.status(200).json({ 'status': 'success', 'message': "Minimum Ordervalue should be " + minimumOrderValue + " Rupees", 'data': { 'product': productList }, 'OrderAmount': warehouseTotal, 'minimumBillValue': minimumOrderValue, "categoryName": categoryName, "flag": 0 });
				});
			}


			// cart.addnewItemsToCart(data).then(result => {
			// 	res.send(result);
			// });
		} else {
			res.send({ status: 'failed', message: 'Please send valid input', data: [] });
		}
	},
	getCartData: function (req, res) {
		if (req.body.data) {
			var data = req.body.data;
			data = JSON.parse(data);
			cart.getOfflineCartDataOfCust(data, function (result) {
				let items = result.filter(ele => {
					if (ele != null && ele != '') {
						return ele;
					}
				});
				let finalres = { 'cart_items': items };
				res.send({ status: 'success', message: '', data: finalres });
			});
		} else {
			res.send({ status: 'failed', message: 'Please send valid input', data: [] });
		}
	},
	deleteCartData: function (req, res) {
		if (req.body.data) {
			var data = req.body.data;
			data = JSON.parse(data);
			cart.deleteCartData(data).then(result => {
				res.send({ status: 'success', message: '', data: 'deleted successfully' });
			});
		} else {
			res.send({ status: 'failed', message: 'Please send valid input', data: [] });
		}
	},

	/**
	 * Get the recommended product for retailer
	 * @param {*} req 
	 * @param {*} res
	 * @returns Array of success & failure message with status code
	 */
	getRecommendedProducts: function(req,res){
		if(req.body != ""){
			// Validation
			if(!req.body.cust_le_id != "" && req.body.limit != "" && req.body.repeat != ""){
				res.send({ status: 'failed', message: 'Please send valid customer ID, limit & repeat', data: [] });
			} else{
				cart.fetchRecommendedProducts(req.body).then(result=>{
					res.send({ status: 'success', message: '', data: result });
				}).catch(function(err){
					res.send({ status: 'failed', message: 'Something went wrong', data: [err] });
				});
			}			
		} else {
			res.send({ status: 'failed', message: 'Please send valid input', data: [] });
		}
	}
}