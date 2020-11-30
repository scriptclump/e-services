const home = require('../models/homemodel');
const dbconnection = require('../../dbConnection');
const joi = require('joi');
const db = dbconnection.DB;

module.exports = {
	/**
	 * [getBeatsByff To get beats list]
	 * @param  {[array]} req [sales token, keyword, offset, limit]
	 * @param  {[Object]} res [Beats list]
	 */
	getBeatsByff: function (req, res) {
		console.log('hi');
		if (req.body.data) {
			let data = JSON.parse(req.body.data);
			let sales_token = data.hasOwnProperty('sales_token') ? data.sales_token : '';
			let keyword = data.hasOwnProperty('keyword') ? data.keyword : '';
			let limit = data.hasOwnProperty('limit') ? data.limit : '';
			let offset = data.hasOwnProperty('offset') ? data.offset : '';

			if (sales_token != '') {
				if (keyword == '') {
					if (limit !== '') {
						if (offset !== '') {
							let query = "select user_id,legal_entity_id from users where password_token = '" + sales_token + "'";
							db.query(query, {}, function (err, userdet) {
								if (err) {
									res.send({ 'status': 'session', 'message': 'Your Session Has Expired. Please Login Again.', 'data': [] });
								} else {
									if (userdet.length > 0) {
										home.getBeatsByffId(userdet[0].user_id, userdet[0].legal_entity_id, limit, offset).then(result => {
											//let beats = {beats:result};
											res.send({'status':'success','message':'Data found','data':{beats:result}});

										},err=>{
											res.send({'status':'failed','message':'No data found','data':[]});
										})
									} else {
										res.send({ 'status': 'session', 'message': 'Your Session Has Expired. Please Login Again.', 'data': [] });
									}
								}
							});
						} else {
							res.send({ 'status': 'failed', 'message': 'Required parameters missing.Plz contact support on - 04066006442 Error_Code : 7002.', 'data': [] });
						}
					} else {
						res.send({ 'status': 'failed', 'message': 'Required parameters missing.Plz contact support on - 04066006442 Error_Code : 7002.', 'data': [] });
					}
				} else {
					let query = "select user_id,legal_entity_id from users where password_token = '" + sales_token + "'";
					db.query(query, {}, function (err, userdet) {
						if (err) {
							res.send({ 'status': 'session', 'message': 'Your Session Has Expired. Please Login Again.', 'data': [] });
						} else {
							if (userdet.length > 0) {
								home.getBeatsByffIdByserach(userdet[0].user_id, userdet[0].legal_entity_id, keyword).then(result => {
									//let beats = {beats:result};
									res.send({'status':'success','message':'Data found','data':{beats:result}});

								},err=>{
									res.send({'status':'failed','message':'No data found','data':[]});
								})
							} else {
								res.send({ 'status': 'session', 'message': 'Your Session Has Expired. Please Login Again.', 'data': [] });
							}
						}
					});
				}

			} else {
				res.send({ 'status': 'failed', 'message': 'Please provide authorized token Error_Code : 3002.', 'data': [] });
			}
		} else {
			res.send({ "status": "failed", "message": "Required parameters missing.Plz contact support on - 04066006442 Error_Code : 7002.", "data": [] });
		}
	},
	/**
	 * [getCategoriesList To get products list by category wise]
	 * @param  {[array]} req [warehouse id, segment id, customer type, limit, offset]
	 * @param  {[Object]} res [Products list]
	 */
	getCategoriesList: function (req, res) {
		if (req.body.data) {
			let data = JSON.parse(req.body.data);
			console.log(data);
			const schema = joi.object().keys({
				le_wh_id:[joi.string(), joi.number()],
				segment_type:joi.number().integer().required(),
				customer_type:joi.number().integer().required(),
				limit:joi.number().integer().required(),
				offset:joi.number().integer().required()
			});
			joi.validate(data,schema,function(err,result){
				if(err){
					console.log('err',err);
					res.send({'status':'failed','message':'Please send correct input','data':[]});
				}else{
					console.log('res',result);
					home.getProductsByCategoryWise(result.le_wh_id,result.segment_type,result.customer_type,result.limit,result.offset).then(categorylist=>{
						
						if(categorylist!=''){
							console.log(categorylist);
							//res.send(categorylist);
							let productcount= categorylist.split(',');
							console.log(productcount.length);
							let products_list = {product_id: categorylist,count:productcount.length};
							res.send({'status':'success','message':'Data found','data':products_list});
						}else{
							let products_list = {product_id: '',count:0};
							res.send({'status':'success','message':'Data found','data':products_list});
						}
					},err=>{
						res.send({'status':'success','message':'No data found','data':[]});
					});
				}
			})
			//console.log(joi.validate(data,schema));
		} else {
			res.send({ "status": "failed", "message": "Required parameters missing.Plz contact support on - 04066006442 Error_Code : 7002.", "data": [] });
		}
	}
}
