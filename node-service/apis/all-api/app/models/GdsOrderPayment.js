/**
 * GdsOrderPayment.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
const db = require('../../dbConnection');

module.exports = {

  //connection : 'someMysqlServer',
  // tableName: 'gds_orders_payment',
  // attributes: {
  // 	id:{

  // 		type : 'integer',
  // 		columnName : 'orders_payment_id',
  // 		primaryKey : true,
  // 		autoIncrement : true
  // 	},
  // 	gds_order_id: 'integer'

  // },

  
  updateGdsPayment:function(order_id,transaction_id){

  			console.log(order_id);
  			console.log(transaction_id);
  			// GdsOrderPayment.update({'gds_order_id':order_id},{'transaction_id': transaction_id})
  			// .exec(function(err, result){

  			// 		console.log(err);
  			// 		console.log(result);
  			// });
  			order_id = 1;
  			transaction_id = '\'dasdasdasd\'';
  			var mysqlString = 'update gds_orders_payment set transaction_id = '
  								+transaction_id+' where gds_order_id = '+order_id;
  			console.log(mysqlString);
  			db.query(mysqlString,{},function(err,result){
  				console.log(err);
  				console.log(result);
  			});

  }

};

