/**
 * ApprovalController
 *
 * @description :: Server-side logic for managing Approvals
 * @help    ();    :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
var _ = require('lodash');
module.exports = {
	index: function(req, res){
		console.log(sails.config.baseurl.url);
		return res.send('Approval APIs...');
	},

	cycleCount: function(req, res)
	{
		console.log("Function: getProducts \n");
		//console.log('Log Started @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
		if(!req.body.data){
			response = {
                "Status":400,
                "Message":"Bad Request",
                "ResponseBody":"Invalid JSON Format"
            };
            return res.json(response);
		}else{
			var data = req.body.data;
			var str = data.toString();
		}
		console.log("Calling getProductsByGroup("+str+")");
		Filters.getProductsByGroup(str, function (result)
		{
			//mas_cat_id = 5 for all channels
			//console.log("controller getProducts function");
			if(result == null){
				var response = '{"Status":400, "Message":"failed", "ResponseBody":"No data found!"}';
				return res.send(response);
			}
			console.log("Received response!!");
			var response = '{"Status":200, "Message":"success", "ResponseBody":'+JSON.stringify(result)+'}';
			var final = JSON.parse(response);
			var prodList = final.ResponseBody;

			var categories = [];

			if(prodList!=null){
				try{
					_.forEach(prodList, function(elements){
						var catMatch = _.find(categories, function(o) { return o.categoryId == elements.category_id; });
						if(catMatch === undefined){
							//Create Category element
							var tempCat = {"categoryName":elements.category_name, "categoryId":elements.category_id, "brands":[]};

							//Create Product element
							var tempProduct = {"productName":elements.product_title, "productId":elements.product_id};
							//tempCat.brands.push(tempProduct);

							//Create Brand element
							var tempBrand = {"brandName":elements.brand_name, "brandId":elements.brand_id, "products":[tempProduct]};
							tempCat.brands.push(tempBrand);
							
							//Push category to main array			
							categories.push(tempCat);
						} else{
							//console.log("Product: "+elements.product_id+" || category: "+elements.category_id+" || brand: "+elements.brand_id);
							//Search for Brand 
							var brandMatch = _.find(categories, function(cat) { 
								if(elements.category_id==cat.categoryId){
									return _.find(cat.brands, function(brand){
										return brand.brandId == elements.brand_id;
									});
								} else
									return;
							});

							//If brand is not present Push it to main array
							if(brandMatch == undefined){
								var indexCat = _.findIndex(categories, function(catIndex){
									return catIndex.categoryId == elements.category_id;
								});
								var tempBrand = {"brandName":elements.brand_name, "brandId":elements.brand_id, "products":[]};
								categories[indexCat].brands.push(tempBrand);
							}

							//Search for Product
							var productMatch = _.find(categories, function(cat) { 
								return _.find(cat.brands, function(brand){
									return _.find(brand.products, function(product){
										return product.productId == elements.product_id;
									});
								});
							});	

							if(productMatch == undefined){
								var indexCat = _.findIndex(categories, function(catIndex){
									return catIndex.categoryId == elements.category_id;
								});
								var indexbrand = _.findIndex(categories[indexCat].brands, function(brandIndex){
									return brandIndex.brandId == elements.brand_id;
								});
								//Create product element and push into main array
								//console.log(elements.product_id+" || category: "+indexCat+" || brand: "+indexbrand);
								var tempProduct = {"productName":elements.product_title, "productId":elements.product_id};
								categories[indexCat].brands[indexbrand].products.push(tempProduct);
							}			
						}
					});
				} catch(err){
					console.error('Internal Error: '+err+"\n");
	            	//console.log('Log ended @ '+dateFormat("yyyy-mm-dd, h:MM:ss TT")+"\n");
	            	response = {
	                    "Status":500,
	                    "Message":"Internal Server Error",
	                    "ResponseBody":"Error: "+err
	                };
	            	return res.send(response);;
				}
			}
			return res.send(categories);
		});
	},
};

