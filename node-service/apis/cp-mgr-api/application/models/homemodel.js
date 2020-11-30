const role = require('../controllers/rolerepo');
const dbconnection = require('../../dbConnection');
const db = dbconnection.DB;
module.exports = {
	/**
	 * [getBeatsByffId To assigned beats for ff]
	 * @param  {[int]} user_id         [user id]
	 * @param  {[int]} legal_entity_id [legal entity id]
	 * @param  {[int]} limit           [limit]
	 * @param  {[int]} offset          [offset]
	 */
	getBeatsByffId: function (user_id, legal_entity_id, limit, offset) {
		return new Promise((resolve, reject) => {
			role.checkPermissionByFeatureCode('ALLBEAT1', user_id).then(data => {
				let flag = 0;
				if (data === true) {
					flag = 1;
				}
				console.log("beataccess",data, flag);
				let beatquery = "CALL getBeatDetails("+user_id+","+legal_entity_id+",NULL,"+flag+","+limit+","+offset+")";
				db.query(beatquery,{},function(err,res){
					if(err){
						console.log(err);
						resolve([]);
					}else{
						//console.log(res);
						if(res.length>0){
							resolve(res[0]);
						}else{
							resolve([]);
						}
					}
				});
			})
		});
	},
	/**
	 * [getCategoryList To get Category list]
	 * @param  {[int]} le_wh_id     [warehouse id]
	 * @param  {[int]} segment_type [segment type]
	 * @param  {[int]} limit        [limit]
	 * @param  {[int]} offset       [offset]
	 * @return {[array]}              [category list]
	 */
	getCategoryList: function (le_wh_id, segment_type, customer_type, limit, offset) {
		return new Promise((resolve, reject) => {
			//let query = "CALL getProductsByCategory(?,?)";
			let query = "CALL getProductsByCategoryByLimit(?,?,?,?,?)";
			db.query(query, [le_wh_id, segment_type, customer_type, limit, offset], (err, res) => {
				if (err) {
					console.log(err);
					resolve([]);
				}else{
					if(res[0].length > 0){
						let categoryset = res[0];
						resolve(categoryset);
					}else{
						resolve([]);
					}
				}
			},err=>{
				resolve([]);
			})
		})
	},
	/**
	 * [getProductsByCategoryWise Get products list by category wise]
	 * @param  {[int]} le_wh_id      [le wh id]
	 * @param  {[int]} segment_type  [segment type]
	 * @param  {[int]} customer_type [customer type]
	 * @param  {[int]} limit         [limit]
	 * @param  {[int]} offset        [offset]
	 * @return {[array]}               [Products list]
	 */
	getProductsByCategoryWise: function (le_wh_id, segment_type, customer_type, limit, offset) {
		return new Promise((resolve, reject) => {
			module.exports.getCategoryList(le_wh_id, segment_type, customer_type, limit, offset).then(list => {
				/*if(list.length >0){
					Promise.all(list.map(item=> module.exports.getEachCategoryProducts(item,le_wh_id,customer_type))).then(productArray=>{
						//console.log(productArray);
						resolve(productArray);
					},err=>{
						resolve([]);
					});
				}else{
					resolve([]);
				}*/
				let products_list='';
				if(list.length>0){
					for(let index=0;index<list.length;index++){
						console.log('products list',products_list);
						if(products_list=='' && list[index].products!=''){
							products_list = list[index].products;
						}else if(list[index].products!=''){
							products_list = products_list +"," +list[index].products;
						}
						if(index == list.length-1){
							resolve(products_list);
						}
					}
				}else{
					resolve('');
				}
				
			},err=>{
				resolve('');
			});
		});		
	},
	/**
	 * [getEachCategoryProducts Get product packs of products list under a single category]
	 * @param  {[string]} item          [comma seperated products list]
	 * @param  {[int]} le_wh_id      [warehouse id]
	 * @param  {[int]} customer_type [customer type]
	 * @return {[array]}               [Array with products and pack information]
	 */
	getEachCategoryProducts: function (item, le_wh_id, customer_type) {
		return new Promise((resolve, reject) => {
			if (item.products && item.products != null) {
				let product_ids = "CALL getCpProductsByPack(?,?,?)";
				db.query(product_ids,[item.products,le_wh_id,customer_type],(err,res)=>{
					if(err){
						console.log(err);
						resolve(item);
					}else{
						if(res[0].length > 0){
							let products = res[0];
							Promise.all(products.map(product => module.exports.getProductPacks(product,le_wh_id,customer_type))).then(result=>{
								item.product_details = result;
								resolve(item);
							})
						}else{
							console.log('VVV');
							resolve(item);
						}
						//item.product_det = res[0];
						//resolve(item);
					}
				});
			}else{
				console.log('ttt');
				resolve(item);
			}
		})
	},
	/**
	 * [getProductPacks description]
	 * @param  {[int]} product       [product id]
	 * @param  {[int]} le_wh_id      [warehouse id]
	 * @param  {[int]} customer_type [customer type]
	 * @return {[array]}               [Pack information]
	 */
	getProductPacks: function (product, le_wh_id, customer_type) {
		return new Promise((resolve, reject) => {
			let query = "CALL getProductSlabsByCust(?,?,0,?)";
			db.query(query,[product.product_id,le_wh_id,customer_type],(err,res)=>{
				if(err){
					console.log(err);
					//resolve([]);
					reject();
				}else{
					if(res[0].length>0){
						delete product.pack_data;
						product.pack_details = res[0];
						resolve(product);
					}else{
						delete product.pack_data;
						product.pack_details = [];
						resolve(product);
					}
				}
			});
		})
	},
	/**
	 * [getBeatsByffIdByserach To get beats lists by search]
	 * @param  {[int]} user_id         [ff id]
	 * @param  {[int]} legal_entity_id [legal entity id]
	 * @param  {[string]} keyword         [entered text]
	 * @return {[array]}                 [beats list]
	 */
	getBeatsByffIdByserach: function (user_id, legal_entity_id, keyword) {
		return new Promise((resolve, reject) => {
			role.checkPermissionByFeatureCode('ALLBEAT1', user_id).then(data => {
				let flag = 0;
				if (data === true) {
					flag = 1;
				}
				console.log("beataccess",data, flag);
				let beatquery = "CALL getBeatDetailsSearch("+user_id+","+legal_entity_id+",NULL,"+flag+",'"+keyword+"')";
				db.query(beatquery,{},function(err,res){
					if(err){
						console.log(err);
						resolve([]);
					}else{
						//console.log(res);
						if(res.length>0){
							resolve(res[0]);
						}else{
							resolve([]);
						}
					}
				});
			})
		});
	}

}
