/**
 * Routeadmin.js
 */
const db = require('../../dbConnection');


module.exports = {

	//connection : 'someMysqlServer',
  	// attributes: {

  	// },

  	getOrderCoordinates:function(data,cb){

  			//console.log(data);
  			var order_list = data['order_ids']; 
  			//var query = 'select  from gds_orders where gds_orders.gds_order_id in (?)';
  			var query = "select gds_orders.cust_le_id,gds_orders.gds_order_id,legal_entities.longitude,legal_entities.latitude from gds_orders LEFT JOIN legal_entities on legal_entities.legal_entity_id = gds_orders.cust_le_id where gds_orders.gds_order_id in (" +order_list+")";
  			db.query(query,{}, function(err, result){
				if(err){ 
					console.log(err);
					return err;
				}
  			cb(result);
  		});
  	},

  	getOrderCoordinatesPromise:function(data){

  		var Promise = require('bluebird');
  		var order_list = data['order_ids']; 
  		var query = 'select * from gds_orders where gds_order_id in (?)';	
  		var queryAsync = Promise.promisify(db.query);
  		var ret;
  		queryAsync(query, [order_list])
  		.then(function(result){
  			return result;
  		})
  		.catch(function(err){

  			console.log(err);
  		});
  	}

};

