//const search = require('../models/searchmodel');
const models = require('../tables/index');
const Op = require('sequelize').Op;
const role = require('./role');
const dbconnection = require('../../dbConnection');
const db = dbconnection.DB;
const banner = require('../../../banner-api/app/models/bannermodel');
module.exports = {
	/**
	 * [getSearchAjax To search for the products]
	 * @param  {[array]} req [keyword, customer_type, le_wh_id, segment_id]
	 * @param  {[array]} res [Products list]
	 */
	getSearchAjax: function (req, res) {
		if (req.body.data) {
			let data = req.body.data;
			data = JSON.parse(data);
			let hub_id = 0;
			let customer_token = data.customer_token;
			let keyword = data.hasOwnProperty('keyword') ? data.keyword : '';
			if (keyword != '') {
				let customer_type = data.hasOwnProperty('customer_type') ? data.customer_type : '';
				if (customer_type != '') {
					let le_wh_id = data.hasOwnProperty('le_wh_id') ? data.le_wh_id : '';
					if (le_wh_id != '') {
						console.log('***', le_wh_id);
						let segment_id = data.hasOwnProperty('segment_id') ? data.segment_id : '';
						if (segment_id != '') {
							/*if(data.hasOwnProperty('hub_id')){
								let hub_id= data.hub_id;
								console.log(hub_id);
							}else{*/
							models.users.findAll(
								{
									where: {
										[Op.or]: [
											{ password_token: customer_token },
											{ lp_token: customer_token }
										]
									},
									attributes: ['legal_entity_id', 'user_id']
								})
								.then(result => {
									if (result.length > 0) {
										let userData = result[0];
										userData = JSON.parse(JSON.stringify(userData));
										let legalEntityId = userData.hasOwnProperty('legal_entity_id') ? userData.legal_entity_id : 0;
										let userId = userData.hasOwnProperty('user_id') ? userData.user_id : 0;
										console.log(userId);
										//module.exports.getUserHubDetails(legalEntityId,userId).then(hub_id=>{
										let flag = data.hasOwnProperty('flag') ? data['flag'] : '';
										if (flag == 1) {
											module.exports.getSupplierSearch(keyword, le_wh_id).then(res2 => {
												res.send(res2);
											})
										} else {
											banner.getBlockedData(customer_token).then(blockedItem => {
												//console.log('searchblocklist', blockedItem);
												let blockedList = {};
												let brands = 0, manf = 0;
												if (blockedItem.brands.length > 0) {
													brands = blockedItem.brands.join();
												}
												if (blockedItem.manf.length > 0) {
													manf = blockedItem.manf.join();
												}
												//res.send({some:blockedList});
												let searchresult = [];
												let catPromise = module.exports.getSearchAjaxCategory(keyword, le_wh_id, customer_type);
												let brandPromise = module.exports.getSearchAjaxBrand(keyword, le_wh_id, brands, customer_type);
												let manfPromise = module.exports.getSearchAjaxManufacturer(keyword, le_wh_id, manf, customer_type);
												let productPromise = module.exports.getSearchAjaxProduct(keyword, le_wh_id, segment_id, brands, manf, customer_type);
												Promise.all([catPromise, brandPromise, manfPromise, productPromise]).then(promresult => {
													searchresult = [...searchresult, ...promresult[0]];
													searchresult = [...searchresult, ...promresult[1]];
													searchresult = [...searchresult, ...promresult[2]];
													searchresult = [...searchresult, ...promresult[3]];
													let finalres = { data: searchresult };
													res.send({ status: "success", message: "getSearchAjax", data: finalres });
												});
												/*module.exports.getSearchAjaxCategory(keyword,le_wh_id,customer_type).then(categories=>{
													searchresult=[...searchresult,...categories];
													module.exports.getSearchAjaxBrand(keyword,le_wh_id,brands,customer_type).then(brands=>{
														searchresult=[...searchresult,...brands];
														module.exports.getSearchAjaxManufacturer(keyword,le_wh_id,manf,customer_type).then(manfs=>{
															searchresult=[...searchresult,...manfs];
															module.exports.getSearchAjaxProduct(keyword,le_wh_id,segment_id,brands,manf,customer_type).then(products=>{
																searchresult=[...searchresult,...products];
																let finalres ={data:searchresult};
																res.send({status:"success",message:"getSearchAjax",data:finalres})
															});
														});
													});

												});*/

											});
										}

										//})

									}
								}, err => {
									console.log('err', err);
								})
							/*models.users.findAll({where:{user_id:3},attributes:['firstname','lastname']}).then(data=>{
								console.log('data');
							});*/
							//}
						} else {
							res.send({ 'status': 'failed', 'message': 'Customer type not found', 'data': [] });
						}
					} else {
						res.send({ 'status': 'failed', 'message': 'Customer type not found', 'data': [] });
					}

				} else {
					res.send({ 'status': 'failed', 'message': 'Customer type not found', 'data': [] });
				}

			} else {
				res.send({ 'status': 'failed', 'message': 'Please send keyword', 'data': [] });
			}

		}
	},
	/**
	 * [getUserHubDetails To get user(ff/retailer) warehouse information]
	 * @param  {[int]} legalEntityId [legalEntityId]
	 * @param  {[int]} userId        [userId]
	 * @return {[string]}               [hub id(s)]
	 */
	getUserHubDetails: function (legalEntityId, userId) {
		return new Promise((resolve, reject) => {
			if (legalEntityId == 2) {
				role.getWarehouseData(userId, 6).then(data => {
					resolve(data);
				});
			} else if (legalEntityId > 0) {
				let query = ' select getRetailerHub(?) as hub_id';
				db.query(query, [legalEntityId], function (err, res) {
					//console.log('spokeeeeeeeeee',res.length,res);
					if (err) {
						console.log(err);
						resolve(0);
					}
					else if (res.length > 0) {
						resolve(res[0].hub_id);
					} else {
						resolve(0);
					}
				});
			}
		})
	},
	/**
	 * [getSupplierSearch To get products under a supplier]
	 * @param  {[string]} keyword  [Entered input]
	 * @param  {[int]} le_wh_id [warehouse id]
	 * @return {[array]}          [List of products]
	 */
	getSupplierSearch: function (keyword, le_wh_id) {
		return new Promise((resolve, reject) => {
			let le = le_wh_id.split(',');
			let query = "select distinct prod.product_id,prod.product_title as name from products prod join inventory i on prod.product_id= i.product_id where prod.product_title like '%" + keyword + "%' and i.le_wh_id=" + le_wh_id;
			db.query(query, {}, function (err, res) {
				if (err) {
					console.log('suppliererr', err);
					resolve([]);
				} else {
					//console.log('supplierres', res);
					resolve(res);
				}
			})
		})
	},
	/**
	 * [getSearchAjaxProduct To get products whose title matched with entered text]
	 * @param  {[string]} keyword       [Keyword]
	 * @param  {[int]} le_wh_id      [warehouse id]
	 * @param  {[int]} segment_id    [segment type]
	 * @param  {[int]} brands        [blocked brands list]
	 * @param  {[int]} manf          [blocked manufacturers list]
	 * @param  {[int]} customer_type [customer type]
	 * @return {[array]}               [products list]
	 */
	getSearchAjaxProduct: function (keyword, le_wh_id, segment_id, brands, manf, customer_type) {
		return new Promise((resolve, reject) => {
			//	console.log(keyword, le_wh_id, manf, brands, customer_type, segment_id);

			let procquery = "call getProductsFromSearch(?,?,?,?,?,?)";
			console.log(typeof (customer_type));
			db.query(procquery, [keyword, le_wh_id, manf, brands, customer_type, segment_id], (err, res) => {
				if (err) {
					console.log('err', err);
					resolve([]);
				} else {
					if (res[0].length > 0) {
						res = JSON.parse(JSON.stringify(res[0]));
						resolve(res);
					} else {
						resolve([]);
					}
				}
			})
		})
	},
	/**
	 * [getSearchAjaxBrand To get products from brands whose title matched with entered text]
	 * @param  {[string]} keyword       [Keyword]
	 * @param  {[int]} le_wh_id      [warehouse id]
	 * @param  {[int]} brand_id      [blocked brand id's]
	 * @param  {[int]} customer_type [customer type]
	 * @return {[array]}               [products list]
	 */
	getSearchAjaxBrand: function (keyword, le_wh_id, brand_id, customer_type) {
		return new Promise((resolve, reject) => {

			let procquery = "call getBrandFromSearch(?,?,?,?)";
			console.log(typeof (customer_type));
			db.query(procquery, [keyword, le_wh_id, brand_id, customer_type], (err, res) => {
				if (err) {
					console.log('err', err);
					resolve([]);
				} else {
					if (res[0].length > 0) {
						res = JSON.parse(JSON.stringify(res[0]));
						resolve(res);
					} else {
						resolve([]);
					}
				}
			})
		})
	},
	/**
	 * [getSearchAjaxCategory To get products from Categories whose title matched with entered text]
	 * @param  {[string]} keyword       [Keyword]
	 * @param  {[int]} le_wh_id      [warehouse id]
	 * @param  {[int]} customer_type [customer type]
	 * @return {[array]}               [products list]
	 */
	getSearchAjaxCategory: function (keyword, le_wh_id, customer_type) {
		return new Promise((resolve, reject) => {
			let procquery = "call getCategoriesFromSearch(?,?,?)";
			console.log(typeof (customer_type));
			db.query(procquery, [keyword, le_wh_id, customer_type], (err, res) => {
				if (err) {
					console.log('err', err);
					resolve([]);
				} else {
					//console.log(res);
					if (res[0].length > 0) {
						res = JSON.parse(JSON.stringify(res[0]));
						resolve(res);
					} else {
						resolve([]);
					}
				}
			})
		})
	},
	/**
	 * [getSearchAjaxManufacturer To get products from Manufacturers whose title matched with entered text]
	 * @param  {[string]} keyword       [Keyword]
	 * @param  {[int]} le_wh_id      [warehouse id]
	 * @param  {[int]} customer_type [customer type]
	 * @return {[array]}               [products list]
	 */
	getSearchAjaxManufacturer: function (keyword, le_wh_id, manf, customer_type) {
		return new Promise((resolve, reject) => {
			let procquery = "call getmanfFromSearch(?,?,?,?)";
			console.log(typeof (customer_type));
			db.query(procquery, [keyword, le_wh_id, manf, customer_type], (err, res) => {
				if (err) {
					console.log('err', err);
					resolve([]);
				} else {
					if (res[0].length > 0) {
						res = JSON.parse(JSON.stringify(res[0]));
						resolve(res);
					} else {
						resolve([]);
					}
				}
			})
		})
	}
};