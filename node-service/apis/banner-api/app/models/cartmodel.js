const db = require('../../dbConnection');
//const upload = require('../../config/s3config');

module.exports = {
	/*addCartDetails: function(req,res){
		return new Promise((resolve,reject)=>{

		});
	},*/
	/*uploadFileToS3: function(req,res){
		const singleUpload = upload.single('feedback_pic');
		singleUpload(req,res,function(err,data){
			if(err)
				return false;
			return req.file.location
		});
	},*/
	getUserIdFromToken: function (req, res) {
		return new Promise((resolve, reject) => {
			if (req != '') {
				var data = "select u.user_id,u.firstname,u.lastname,u.legal_entity_id from users u where u.password_token=" + "'" + req + "'" + " or u.lp_token=" + "'" + req + "'" + " or u.chat_token=" + "'" + req + "'";
				console.log(data);
				db.query(data, {}, function (err, res) {
					if (err) {
						reject(err);
					} else {
						resolve(res);
					}
				});
			}

		});
	},
	saveFeedbackReasons: function (req, res) {
		return new Promise((resolve, reject) => {
			var data = "insert into customer_feedback(legal_entity_id, feedback_type, feedback_group_type, comments, picture, audio, created_by, created_at) values(?,?,?,?,?,?,?,?)";
			console.log(req);
			if (req.length > 0) {
				req = req[0];
				db.query(data, [req.legal_entity_id, req.feedback_id, req.feedback_groupid,
				req.comments, req.feedback_pic, req.feedback_audio, req.ff_id, new Date()], function (err, res) {
					if (err) {
						let Result = { status: 'failed', message: "Please try again", data: [] };
						resolve(Result);
					} else {
						let Result = { status: 'success', message: "Saved Successfully", data: res };
						resolve(Result);
					}
				});
			} else {
				let Result = { status: 'failed', message: "Please try again", data: [] };
				resolve(Result);
			}
		});
	},
	insertFFComments: function (req, res) {
		return new Promise((resolve, reject) => {
			if (req.length > 0) {
				req = req[0];
				date = new Date();
				var day = date.getDate();
				var month = date.getMonth() + 1;
				var year = date.getFullYear();
				var presentday = year + "-" + month + "-" + day;


				if (req['activity'] == 107000) {
					let log = "insert into ff_call_logs (ff_id,user_id,legal_entity_id,activity,check_in,check_in_lat,check_in_long,created_at) values(?,?,?,?,?,?,?,?)";
					db.query(log, [req.ff_id, req.user_id, req.legal_entity_id, req.activity, new Date(), req.latitude, req.longitude, new Date()], function (err, res) {
						if (err) {
							console.log(err);
							let Result = { status: 'failed', message: "Please try again", data: [] };
							resolve(Result);
						} else {
							let Result = { status: 'success', message: "Saved Successfully", data: [] };
							resolve(Result);
						}
					});
				} else {
					let log = "select log_id from ff_call_logs where legal_entity_id=? and ff_id=?   order by log_id desc limit 1 ";
					db.query(log, [req.legal_entity_id, req.ff_id], function (err, res) {
						if (err) {
							console.log(err);
							let Result = { status: 'failed', message: "Please try again", data: [] };
							resolve(Result);
						} else {
							if (res.length > 0) {
								let updatedData = "update ff_call_logs set activity=?, check_out=?, check_out_lat=?, check_out_long=? where ff_id=? and user_id=? and legal_entity_id=? and log_id=?";
								db.query(updatedData, [req.activity, new Date(), req.latitude, req.longitude, req.ff_id, req.user_id, req.legal_entity_id, res[0].log_id], function (err, res) {
									if (err) {
										console.log(err);
										let Result = { status: 'failed', message: "Please try again", data: [] };
										resolve(Result);
									} else {
										let Result = { status: 'success', message: "Saved Successfully", data: [] };
										if (req.activity == 107001) {
											let removeCart = "Delete from offline_cart_details where cust_id=" + req.user_id;
											db.query(removeCart, {}, function (err, res) {
												if (err) {
													console.log('err4', err);
													let Result = { status: 'failed', message: "Please try again", data: [] };
													resolve(Result);
												} else {
													resolve(Result);
												}
											});
										} else {
											console.log('###', req.legal_entity_id, req.cart_data);
											if (req.cart_data) {
												console.log('%%');
												let removeCart = "Delete from offline_cart_details where cust_id=" + req.user_id;
												db.query(removeCart, {}, function (err, res) {
													if (err) {
														console.log('err4', err);
														let Result = { status: 'failed', message: "Please try again", data: [] };
														resolve(Result);
													} else {
														for (let p in req.cart_data) {
															let cart_products = req.cart_data[p];
															let cart_data = "insert into offline_cart_details (product_id,parent_id,product_image,product_title,product_star,color_code,esu,quantity,status,unit_price,total_price,margin,blocked_qty,prmt_det_id,is_slab,slab_esu,product_slab_id,pack_level,pack_type,freebie_product_id,freebee_qty,freebee_mpq,discount_type,discount,cashback_amount,le_wh_id,cust_id,is_child,packs) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
															db.query(cart_data, [cart_products['productId'],
															cart_products['parentId'],
															cart_products['productImage'], cart_products['productTitle'], cart_products['packStar'], cart_products['star'], cart_products['esu'], cart_products['quantity'], cart_products['status'], cart_products['unitPrice'], cart_products['totalPrice'], cart_products['margin'], cart_products['blockedQty'], cart_products['prmtDetId'], cart_products['isSlab'], cart_products['slabEsu'], cart_products['productSlabId'], cart_products['packLevel'], cart_products['packType'], cart_products['freebieProductId'], cart_products['freeqty'], cart_products['freebieMpq'], null, cart_products['discount'], cart_products['cashbackAmount'],
															req.le_wh_id, req.user_id,
															cart_products['isChild'], JSON.stringify(cart_products['packs'])], function (err, res) {
																if (err) {
																	let Result = { status: 'failed', message: "Please try again", data: [] };
																	resolve(Result);
																	console.log(err);
																} else {
																	let Result = { status: 'success', message: "Inserted successfully", data: res };
																	resolve(Result);
																}
															})
														}
													}
												});
											}

										}
										resolve(Result);
									}
								});
							} else {
								let Result = { status: 'failed', message: "Please try again", data: [] };
								resolve(Result);
							}
						}
					});
				}
			} else {
				let Result = { status: 'failed', message: "Please try again", data: [] };
				resolve(Result);
			}
		});

	},
	validParentChildRelation: function (req, res) {
		//console.log(req);
		return new Promise((resolve, reject) => {
			var checkValid = "select count(*) as count from legal_entities where legal_entity_id=? and parent_le_id=?";
			db.query(checkValid, [req.cust_le_id, req.ff_le_id], function (err, res) {
				if (err) {
					let Result = { status: 'failed', message: "You are not allowed to Check In this Retailer", data: { display: 1 } };
					resolve(Result);
				} else {
					//console.log(res[0].count);
					if (req.cust_le_id == req.ff_le_id) {
						let Result = { status: 'success', message: "Valid Check In", data: [] };
						resolve(Result);
					}
					if (res[0].count > 0) {
						let Result = { status: 'success', message: "Valid Check In", data: [] };
						resolve(Result);
					} else {
						let data = "select count(*) as count from user_roles ur join roles r on ur.role_id=r.role_id where r.short_code='FFMC' and user_id=" + req.user_id;
						db.query(data, {}, function (err2, res2) {
							if (res2[0].count > 0) {
								let Result = { status: 'success', message: "Valid Check In", data: [] };
								resolve(Result);
							} else {
								let Result = { status: 'failed', message: "You are not allowed to Check In this Retailer", data: { display: 1 } };
								resolve(Result);
							}
						});
					}
				}
			})
		});
	},
	checkValidRelation: function (req, res) {
		return new Promise((resolve, reject) => {
			if (req.ff_id != '') {
				var query = 'CALL getFFRetailerCheckIn(?,?)';
				db.query(query, [req.ff_id, req.cust_le_id], function (err, res) {
					if (err) {
						console.log(err);
						let Result = { status: 'failed', message: "Please try again", data: [] };
						resolve(Result);
					} else {

						var aggregate = res.length > 0 ? (res[0].length > 0 ? (res[0][0].hasOwnProperty('AGGREGATE') ? res[0][0]['AGGREGATE'] : 0) : 0) : 0;
						console.log(aggregate);
						if (aggregate > 0) {
							//let Result={status:'success1',message:"Valid Check In",data:[]};
							console.log('***', req);
							module.exports.getOfflineCartDataOfCust({ cust_le_id: req.cust_le_id, user_id: req.user_id }, function (resdata) {
								console.log(resdata);
								let items = resdata.filter(ele => {
									if (ele != null && ele != '') {
										return ele;
									}
								});
								var cart_data = { cart_items: items };
								let Result = { status: 'success', message: "Valid Check In", data: cart_data };
								resolve(Result);
							})/*.then(response=>{
								var cart_data={cart_items:response};
								let Result ={status:'success',message:"Valid Check In",data:cart_data};
								resolve(Result);
							});*/
						} else {
							var validcheck = 'select  dhm.dc_id,dhm.hub_id FROM retailer_flat AS rf  LEFT JOIN wh_serviceables AS wh ON wh.pincode = rf.pincode AND wh.legal_entity_id = rf.parent_le_id JOIN legalentity_warehouses AS lw ON lw.legal_entity_id = rf.parent_le_id AND rf.hub_id = lw.le_wh_id LEFT JOIN dc_hub_mapping AS dhm ON dhm.hub_id = rf.hub_id WHERE rf.legal_entity_id = ?';
							db.query(validcheck, [req.cust_le_id], function (err, res) {
								if (err) {
									let Result = { status: 'failed', message: "Please try again", data: [] };
									resolve(Result);
								} else {
									if (res.length > 0) {
										module.exports.getOfflineCartDataOfCust({ cust_le_id: req.cust_le_id, user_id: req.user_id }, function (resdata) {
											let items = resdata.filter(ele => {
												if (ele != null && ele != '') {
													return ele;
												}
											});
											var cart_data = { cart_items: items };
											let Result = { status: 'success', message: "Valid Check In", data: cart_data };
											resolve(Result);
										})
										/*let Result={status:'success2',message:"Valid Check In",data:[]};
										resolve(Result);*/
									} else {
										let Result = { status: 'failed', message: "Improper Dc and Hub Configuration for the retailer or field force Error_Code : 8005.", data: { display: 0 } };
										resolve(Result);
									}
								}
							})

						}

					}
				})

			} else {

			}
		})
	},
	getOfflineCartDataOfCust: function (req, callback) {
		//return new Promise((resolve,reject)=>{
		var getcartData = "select cart_id,blocked_qty as 'blockedQty', cashback_amount as 'cashbackAmount', cust_id as 'customerId' ,discount,esu,freebee_mpq as 'freebieMpq',freebie_product_id as 'freebieProductId',freebee_qty as 'freeqty',is_child as 'isChild',is_slab as 'isSlab', margin,pack_level as 'packLevel',product_star as 'packStar',pack_type as 'packType', parent_id as 'parentId',prmt_det_id as 'prmtDetId',product_id as 'productId',product_image as 'productImage',product_slab_id as 'productSlabId',product_title as 'productTitle', quantity,slab_esu as 'slabEsu',color_code as 'star',status,total_price as 'totalPrice', unit_price as 'unitPrice',created_at as updatedDate, le_wh_id as warehouseId,packs,cust_id,product_point,category_id as categoryId,minimum_order_value as minimumOrderValue from offline_cart_details where cust_id=?";
		db.query(getcartData, [req.user_id], function (err, res) {
			if (err) {
				console.log('444', err);
				let Result = { status: 'failed', message: "Please try again", data: [] };
				callback(Result);
			} else {
				if (res.length > 0) {
					//let value=0;

					//res.forEach(function(element,value){
					for (let value = 0, p = Promise.resolve(); value < res.length; value++) {
						console.log('%%%%%', value);
						p = p.then(_ => new Promise(resolve => {
							res[value]['packs'] = JSON.parse(res[value]['packs']);
							var element = res[value];
							console.log(value, element['cart_id']);
							var inventorycheck = "select soh-(order_qty+reserved_qty) as available_qty from inventory where product_id=? and le_wh_id=?";
							db.query(inventorycheck, [element['productId'], element['warehouseId']], function (err, response) {
								if (err) {
									console.log('err3', err);
									res[value]['status'] = 0;
								} else {
									if (element['parentId'] != element['productId']) {
										let freequery = "SELECT COUNT(*) AS count FROM freebee_conf WHERE free_prd_id = ? AND main_prd_id = ? AND CURDATE() BETWEEN start_date AND end_date ";
										db.query(freequery, [element['productId'], element['parentId']], function (err, freeres) {
											if (err) {
												resolve('');
											} else {
												console.log('8888888888', freeres);
												if (freeres[0].count > 0) {
													if (response.length > 0) {
														//console.log(element['productId']);
														if (response[0]['available_qty'] <= 0) {
															console.log('qty', response[0]['available_qty'], element['productId'], value);
															module.exports.checkCartInventory({ 'res': res, 'response': response, 'value': value }).then(data5 => {

																res[value] = data5;

																//console.log('5555',value,res);
																if (value == res.length - 1) {
																	//console.log('99999999991',value,res);
																	callback(res);
																} else {
																	resolve();
																}
															});
														} else {
															//console.log('aaaa');
															if (value == res.length - 1) {
																//console.log('99999999992',value,res);
																callback(res);
															} else {
																resolve();
															}
														}
													} else {
														res[value]['status'] = 0;
														if (value == res.length - 1) {
															//console.log('99999999993',value,res);	
															callback(res);
														} else {
															resolve();
														}
													}
												} else {
													for (let k = 0; k < res.length; k++) {
														if (res[k]['productId'] == element['parentId']) {
															res[k]['freebieProductId'] = 0;
															res[k]['freeqty'] = 0;
															res[k]['freebieMpq'] = 0;
														}
													}
													res[value] = '';
													if (value == res.length - 1) {
														//console.log('99999999992',value,res);
														callback(res);
													} else {
														resolve();
													}
												}
											}

										});
									} else {
										if (response.length > 0) {
											//console.log(element['productId']);
											if (response[0]['available_qty'] <= 0) {
												console.log('qty', response[0]['available_qty'], element['productId'], value);
												module.exports.checkCartInventory({ 'res': res, 'response': response, 'value': value }).then(data5 => {

													res[value] = data5;

													//console.log('5555',value,res);
													if (value == res.length - 1) {
														//console.log('99999999991',value,res);
														callback(res);
													} else {
														resolve();
													}
												});
											} else {
												//console.log('aaaa');
												if (value == res.length - 1) {
													//console.log('99999999992',value,res);
													callback(res);
												} else {
													resolve();
												}
											}
										} else {
											res[value]['status'] = 0;
											if (value == res.length - 1) {
												//console.log('99999999993',value,res);	
												callback(res);
											} else {
												resolve();
											}
										}
									}

								}

							});
						}))
					}
					//console.log(value);
					//})

				} else {
					callback([]);
				}

			}
		})
		//});
	},
	checkCartInventory: function (req, res) {
		return new Promise((resolve, reject) => {
			var { value } = req;
			var { res } = req;
			var { response } = req;
			var product_group = "select  p.`product_id`,p.product_title,p.thumbnail_image,p.star,ps.unit_price,ps.margin,i.soh-(i.`reserved_qty`+i.`order_qty`) AS qty FROM products p JOIN inventory i ON p.`product_id` = i.`product_id` AND i.`le_wh_id` = ? AND i.soh-(i.`reserved_qty`+i.`order_qty`)>= ? AND  i.`product_id` NOT IN (?) JOIN product_slab_flat ps ON ps.product_id=p.product_id AND ps.wh_id=?  WHERE p.product_group_id IN (SELECT pp.product_group_id FROM products pp WHERE pp.product_id = ?) and p.kvi not in (69010) group by ps.product_id";
			db.query(product_group, [res[value]['warehouseId'], 0, res[value]['productId'], res[value]['warehouseId'], res[value]['productId']], function (err, resp) {
				if (err) {
					console.log('111', err);
					res[value]['status'] = 0;
					resolve('');
				} else {
					if (resp.length > 0) {
						var flag = 1;
						var levelflag = 1;
						for (index in resp) {
							console.log('hi2');
							if (flag == 1) {
								//console.log('------productId-----',resp[index]['product_id'] );
								var ProductFromSameGroup = "select pc.level,pc.no_of_eaches,ps.is_slab,ps.esu AS slab_esu,ps.prmt_det_id,ps.product_slab_id,pc.star,pc.product_id from product_pack_config pc join product_slab_flat ps on ps.product_id=pc.product_id AND pc.`level`=ps.`pack_level` and ps.wh_id=? where pc.product_id=? order by pc.no_of_eaches asc";
								db.query(ProductFromSameGroup, [res[value]['warehouseId'], resp[index]['product_id']], function (err, response2) {
									if (err) {
										console.log('222', err);
										res[value]['status'] = 0;
										if (index == resp.length - 1) {
											resolve('');
										}
									} else {
										if (response2.length > 0) {
											//console.log('@@@@@@@@',response2.length,response2,'^^^^^^');
											level = 0;
											var qty = res[value]['quantity'];
											var packs = [];
											let j = 0;
											for (i = response2.length - 1; i >= 0; i--) {
												let no_of_eaches = response2[i]['no_of_eaches'] * response2[i]['slab_esu'];
												//console.log('i',i,response2[i]);
												//if(no_of_eaches<qty){
												let no_of_units = parseInt(qty / no_of_eaches);
												qty = qty % no_of_eaches;
												packs[j++] = {
													'pack_qty': no_of_units * no_of_eaches,
													'pack_size': no_of_eaches,
													'pack_level': response2[i]['level'],
													'qty': no_of_units,
													'star': response2[i]['star'],
													'esu': response2[i]['slab_esu'],
													'pack_cashback': '',
													'product_id': response2[i]['product_id'],
													'customer_id': res[value]['cust_id']
												};
												//}
											}
											resp[index]['packs'] = packs;
											//console.log('packs list',packs);
											response2.forEach(function (ele) {
												module.exports.updateProductInCart({
													"response2": ele,
													"res": res[value],
													"replaceData": resp[index],
												}).then(dat4 => {
													//console.log('lllllllllll',level, res[value]['cart_id']);
													if (level == response2.length - 1 && index == resp.length - 1) {
														res[value] = dat4;
														console.log('&&&&&&&&&&&', level, '$', response2.length, '$', index, '$', resp.length, res[value]['cart_id']);
														res[value]['packs'] = packs;
														resolve(res[value]);
													} else {
														res[value] = dat4;
														level = level + 1;
													}
												});
											});
										} else {
											res[value]['status'] = 0;
											if (index == resp.length - 1) {
												resolve('');
											}
										}
									}
								})

							}
						}

					} else {
						res[value]['status'] = 0;
						resolve('');
					}
				}
			});
		})

	},
	updateProductInCart: function (req, res) {
		return new Promise((resolve, reject) => {

			var response2 = req.response2;
			var replaceData = req.replaceData;
			var res = req.res;
			//console.log(response2);
			if (response2['no_of_eaches'] <= res['quantity']) {
				//console.log(replaceData);

				var updateCart = "UPDATE offline_cart_details SET product_id=?,parent_id=?,product_image=?,product_title=?,esu=?,pack_level=?,unit_price=?,total_price=?,margin=?,product_star=?,prmt_det_id=?,is_slab=?,slab_esu=?,product_slab_id=?,packs=?,STATUS=2 WHERE cart_id=?";
				db.query(updateCart, [replaceData['product_id'], replaceData['product_id'], replaceData['thumbnail_image'], replaceData['product_title'], response2['slab_esu'], response2['level'], replaceData['unit_price'], replaceData['unit_price'] * res['quantity'], replaceData['margin'], replaceData['star'], response2['prmt_det_id'], response2['is_slab'], response2['slab_esu'], response2['product_slab_id'], JSON.stringify(res['packs']), res['cart_id']], function (err, resp3) {
					if (err) {
						console.log(err);
						res['status'] = 0;
						console.log('xxx');
						resolve(res);
					} else {
						//console.log('77');
						res['productId'] = replaceData['product_id'];
						res['productImage'] = replaceData['thumbnail_image'];
						res['productTitle'] = replaceData['product_title'];
						res['parentId'] = replaceData['product_id'];
						res['packLevel'] = response2['level'];
						res['unitPrice'] = replaceData['unit_price'];
						res['totalPrice'] = replaceData['unit_price'] * res['quantity'];
						res['margin'] = replaceData['margin'];
						res['packStar'] = replaceData['star'];
						res['isSlab'] = response2['is_slab'];
						res['productSlabId'] = response2['product_slab_id'];
						res['slabEsu'] = response2['slab_esu'];
						res['prmtDetId'] = response2['prmt_det_id'];
						res['status'] = 2;
						console.log('yyy');
						resolve(res);
					}
				});

			} else {
				console.log('zzz');
				resolve(res);
			}
		})
	},
	getLatLongDetails: function (req, res) {
		return new Promise((resolve, reject) => {
			let data = "select latitude,longitude from retailer_flat where legal_entity_id=?";
			if (req.length > 0) {
				req = req[0];
				if (req.latitude == '' && req.longitude == '') {
					db.query(data, [req.legal_entity_id], function (err, res) {
						if (err) {
							let Result = { latitude: '', longitude: '' };
							resolve(Result);
						} else {
							if (res.length > 0) {
								resolve({ latitude: res[0].latitude, longitude: res[0].longitude });
							} else {
								resolve({ latitude: '', longitude: '' });
							}
						}
					})
				} else {
					resolve({ latitude: req.latitude, longitude: req.longitude });
				}
			}
		});
	},
	getUserIdFromLeid: function (req, res) {
		return new Promise((resolve, reject) => {
			if (req != '') {
				var data = "select u.user_id,u.firstname,u.lastname,u.legal_entity_id from users u where u.legal_entity_id=" + "'" + req + "'";
				console.log(data);
				db.query(data, {}, function (err, res) {
					if (err) {
						reject(err);
					} else {
						resolve(res);
					}
				});
			}

		});
	},

	addnewItemsToCart: function (req, res) {
		return new Promise((resolve, reject) => {
			let cartItem = req.cart_data;
			if (cartItem.length > 0) {
				let removeCart = "Delete from offline_cart_details where cust_id=" + req.user_id + " and (parent_id=" + cartItem[0].productId + " or product_id=" + cartItem[0].productId + ")";
				db.query(removeCart, {}, function (err, res) {
					if (err) {
						console.log('err4', err);
						let Result = { status: 'failed', message: "Please try again", data: [] };
						resolve(Result);
					} else {
						for (let p in req.cart_data) {
							let cart_products = req.cart_data[p];
							if (cart_products['product_point'] == "null") {
								cart_products['product_point'] = 0.00;
							}
							let cart_data = "insert into offline_cart_details (product_id,parent_id,category_id ,product_image,product_title,product_star,color_code,esu,quantity,status,unit_price,total_price,margin,blocked_qty,prmt_det_id,is_slab,slab_esu,product_slab_id,pack_level,pack_type,freebie_product_id,freebee_qty,freebee_mpq,discount_type,discount,cashback_amount,le_wh_id,cust_id,is_child,product_point,packs,minimum_order_value) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
							db.query(cart_data, [cart_products['productId'],
							cart_products['parentId'],
							cart_products['cat_id'],
							cart_products['productImage'], cart_products['productTitle'], cart_products['packStar'], cart_products['star'], cart_products['esu'], cart_products['quantity'], cart_products['status'], cart_products['unitPrice'], cart_products['totalPrice'], cart_products['margin'], cart_products['blockedQty'], cart_products['prmtDetId'], cart_products['isSlab'], cart_products['slabEsu'], cart_products['productSlabId'], cart_products['packLevel'], cart_products['packType'], cart_products['freebieProductId'], cart_products['freeqty'], cart_products['freebieMpq'], null, cart_products['discount'], cart_products['cashbackAmount'],
							cart_products['warehouseId'], req.user_id,
							cart_products['isChild'], cart_products['product_point'], JSON.stringify(cart_products['packs']), cart_products['minimun_order_value']], function (err, res) {
								if (err) {
									let Result = { status: 'failed', message: "Please try again", data: [] };
									resolve(Result);
									console.log(err);
								} else {
									let Result = { status: 'success', message: "Inserted successfully", data: res, "flag": 0 };
									resolve(Result);
								}
							});
						}
					}
				});
			} else {
				let Result = { status: 'failed', message: "Please try again", data: [] };
				resolve(Result);
				console.log(err);
			}
		});
	},
	// addnewItemsToCart: function (req, res) {
	// 	return new Promise((resolve, reject) => {
	// 		let cartItem = req.cart_data;
	// 		if (cartItem.length > 0) {
	// 			let removeCart = "Delete from offline_cart_details where cust_id=" + req.user_id + " and (parent_id=" + cartItem[0].productId + " or product_id=" + cartItem[0].productId + ")";
	// 			db.query(removeCart, {}, function (err, res) {
	// 				if (err) {
	// 					console.log('err4', err);
	// 					let Result = { status: 'failed', message: "Please try again", data: [] };
	// 					resolve(Result);
	// 				} else {
	// 					for (let p in req.cart_data) {
	// 						let cart_products = req.cart_data[p];
	// 						let cart_data = "insert into offline_cart_details (product_id,parent_id,product_image,product_title,product_star,color_code,esu,quantity,status,unit_price,total_price,margin,blocked_qty,prmt_det_id,is_slab,slab_esu,product_slab_id,pack_level,pack_type,freebie_product_id,freebee_qty,freebee_mpq,discount_type,discount,cashback_amount,le_wh_id,cust_id,is_child,product_point,packs) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	// 						db.query(cart_data, [cart_products['productId'],
	// 						cart_products['parentId'],
	// 						cart_products['productImage'], cart_products['productTitle'], cart_products['packStar'], cart_products['star'], cart_products['esu'], cart_products['quantity'], cart_products['status'], cart_products['unitPrice'], cart_products['totalPrice'], cart_products['margin'], cart_products['blockedQty'], cart_products['prmtDetId'], cart_products['isSlab'], cart_products['slabEsu'], cart_products['productSlabId'], cart_products['packLevel'], cart_products['packType'], cart_products['freebieProductId'], cart_products['freeqty'], cart_products['freebieMpq'], null, cart_products['discount'], cart_products['cashbackAmount'],
	// 						req.le_wh_id, req.user_id,
	// 						cart_products['isChild'], cart_products['product_point'], JSON.stringify(cart_products['packs'])], function (err, res) {
	// 							if (err) {
	// 								let Result = { status: 'failed', message: "Please try again", data: [] };
	// 								resolve(Result);
	// 								console.log(err);
	// 							} else {
	// 								let Result = { status: 'success', message: "Inserted successfully", data: res };
	// 								resolve(Result);
	// 							}
	// 						});
	// 					}
	// 				}
	// 			});
	// 		} else {
	// 			let Result = { status: 'failed', message: "Please try again", data: [] };
	// 			resolve(Result);
	// 			console.log(err);
	// 		}
	// 	});
	// },
	deleteCartData: function (req, res) {
		return new Promise((resolve, reject) => {
			if (req != '') {
				var data = `delete from offline_cart_details where cust_id=${req.cust_id} and product_id in (${req.products})`;
				db.query(data, {}, function (err, res) {
					if (err) {
						reject(err);
					} else {
						resolve(res);
					}
				});
			}
		});
	},
	/**
	 * Fetch the recommended proucts for the FC/DC users
	 * @param {*} req 
	 * @param {*} res 
	 */
	fetchRecommendedProducts: function(req,res){
		return new Promise((resolve,reject)=>{
			if(req!=''){
				// var data =`delete from offline_cart_details where cust_id=${req.cust_id} and product_id in (${req.products})`;
				var sql = 'CALL getRecommendedProducts1('+req.cust_le_id+','+req.limit+','+req.repeat+')';
				db.query(sql,{},function(err,res){
					if(err){
						reject(err);
					}else{
						resolve(res[0]);
					}
				});
			}
		});
	},
	getCategoryName: function (cat_id) {
		return new Promise((resolve, reject) => {
			if (cat_id != '') {
				var data = `SELECT cat_name FROM categories  WHERE category_id = ${cat_id}`;
				db.query(data, {}, function (err, res) {
					if (err) {
						reject(err);
					} else {
						resolve(res[0].cat_name);
					}
				});
			}
		});
	},
}