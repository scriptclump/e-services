/**
 * Crate.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

var _ = require('lodash');
var dateFormat = require('moment-timezone');
const db = require('../../dbConnection');
const config = require('../../config/config.json');

module.exports = {
	checkCrateStatus: function(crateCode, callback){
		console.log("checkCrateStatus:- received parameters are: "+crateCode);

		var sql = "SELECT status FROM container_master where crate_code = ?";
		db.query(sql, [crateCode], function(err, result){
			if(err){ 
				console.log(err);
				sails.log(err);
				return err;
			}
			callback(result);
		});
	},

	getOrderInvoiceDetails: function(code, type, callback)
	{
		console.log("getOrderInvoiceDetails:- received parameters are: "+code+" || "+type);

		var sql = "select go.gds_order_id, go.order_code, DATE_FORMAT(go.order_date,'%Y-%m-%d %H:%i:%s') as order_date, `getLeWhName`(go.le_wh_id) as `wh_name`,  `getLeWhName`(go.hub_id) as `hub_name`, "
		+"getMastLookupValue(go.order_status_id) as `order_status`, getUserName(go.created_by,2) as `so_name`, go.total, go.shop_name, getOrderBeatName(go.cust_le_id) as `beat`,getOrderSpokeName(go.cust_le_id) as `spoke`, "
		+"gi.invoice_code, gi.grand_total,  DATE_FORMAT(gi.created_at,'%Y-%m-%d %H:%i:%s') as created_at, getUserName(gi.created_by,2) as `created_by`, "
		+"getUserName(pc.picked_by,2) as `picked_by`, pc.picked_by as `picker_id`, DATE_FORMAT(pc.created_at,'%Y-%m-%d %H:%i:%s') as `picked_at`,gi.gds_invoice_grid_id,go.hub_id from gds_orders go "
		+"left join gds_invoice_grid gi on (gi.gds_order_id = go.gds_order_id) "
		+"left join picker_container_mapping pc on (pc.order_id=go.gds_order_id) where ";
		if(type=='c')
			sql+= "pc.container_barcode = ? ";
		else 
			sql+= "go.order_code= ? ";
		sql+= "order by pc.created_at desc limit 1";
		//console.log("Query: \n"+sql);
		db.query(sql, [code], function(err, result){
			if(err){ 
				console.log(err);
				sails.log(err);
				return err;
			}
			callback(result);
		});
	},

	getContainerDetails: function(orderId,final,callback)
	{
		console.log("getContainerDetails:- received parameters are: "+orderId);

		//console.log(final[0]);

		var sql = "select pc.container_barcode, ov.file_path, getMastLookupValue(pc.container_type) as `container_type`, pc.is_verified, "
		+"getCrateWeight(pc.container_barcode,pc.order_id) as weight, p.product_title, p.product_id, p.mrp, p.thumbnail_image as productUrl, sum(pc.qty) as picked_qty, pc.weight as prod_weight, cm.status "
		+"from picker_container_mapping pc "
		+"inner join products p on (p.product_id = pc.productid) "
		+"left join container_master cm on (cm.crate_code=pc.container_barcode) "
		+"left join order_verification_files ov on (ov.order_id = pc.order_id AND ov.container_name = pc.container_barcode) "
		+"where pc.order_id = ? group by pc.container_barcode, pc.productid";
		db.query(sql, [orderId], function(err, result){
			if(err){ 
				console.log(err);
				sails.log(err);
				return err;
			}
			var container = [];
			var crate = bag = cfc = 0;
			if(result != null){
				try{
					_.forEach(result, function(elements){
						var crateMatch = _.find(container, function(o) { return o.container_barcode == elements.container_barcode; });
						if(crateMatch === undefined){
							if(elements.container_type == 'Crate')
								crate++;
							else if(elements.container_type == 'Bag')
								bag++;
							else if(elements.container_type == 'CFC')
								cfc++;
							var tempProd = {"product_title":elements.product_title, "product_id":elements.product_id, "mrp":elements.mrp, "productUrl":elements.productUrl, "picked_qty":elements.picked_qty, "weight":elements.prod_weight};
							var tempCrate = {"container_barcode":elements.container_barcode, "container_type":elements.container_type, 
							"status":elements.status, "weight":elements.weight, "is_verified":elements.is_verified,"image_path":elements.file_path, "products":[tempProd]};

							container.push(tempCrate);
						} else{
							var indexCrate = _.findIndex(container, function(crateIndex){
									return crateIndex.container_barcode == elements.container_barcode;
								});
							var tempProd = {"product_title":elements.product_title, "product_id":elements.product_id, "mrp":elements.mrp, "productUrl":elements.productUrl, "picked_qty":elements.picked_qty, "weight":elements.prod_weight};
							container[indexCrate].products.push(tempProd);
						}
						//console.log(elements.container_barcode);
					});
					final[0].Crate = crate;
					final[0].Bag = bag;	
					final[0].CFC = cfc;	
					//console.log(final);
				}catch(err){
					console.error('Internal Error: '+err+"\n");
					callback(err, final);
				}
			}
			callback(container,final,orderId);
		});
	},
	getCancelledProducts: function(order_id, final, callback){
		console.log("Under getCancelledProducts: "+order_id);
		try{
			var sql = "SELECT p.product_id, p.sku, p.product_title, p.thumbnail_image as productUrl, goc.qty as canceled_qty, p.mrp "
			+"FROM gds_cancel_grid gcg "
			+"join gds_order_cancel goc on (gcg.cancel_grid_id = goc.cancel_grid_id) "
			+"join products p on (p.product_id = goc.product_id) "
			+"where gcg.gds_order_id=?";

			db.query(sql, [order_id], function(err, result){
				if(err){ 
					console.log(err);
					sails.log(err);
					callback(err, final);
				}
				//console.log(result);
				var cancelled = [];
				_.forEach(result, function(elements){
					var tempProd = {"product_title":elements.product_title, "sku":elements.sku, "product_id":elements.product_id, "mrp":elements.mrp, "productUrl":elements.productUrl, "cancelled_qty":elements.canceled_qty};
					cancelled.push(tempProd);
				});
				callback(cancelled, final);
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			callback(err, final);
		}
	},
	saveContainerVerification: function(odrId, usrId, containers, callback){
		var now = dateFormat().tz("Asia/Kolkata").format("YYYY-MM-DD HH:mm:ss");
		console.log("Timestamp: "+now);
		console.log("saveContainerVerification:- received parameters are: "+odrId+" || "+usrId+" || "+containers);
		try{
			var qry = "UPDATE `picker_container_mapping` "
					+"SET `is_verified` = '1', `verified_by` = ?, `verified_at` = ? "
					+"WHERE `order_id`= ? and `container_barcode` in (?)";
			//console.log("Query: \n"+qry);


			db.query(qry, [usrId,now,odrId,containers], function(err, result){
				if(err){ 
					console.log("Error: "+err+"\n");
					return err;
				}
				//console.log("Query success");
                                
                                var verify_query = "insert into gds_orders_comments (comment_type, entity_id, order_status_id, comment, commentby) "
                                + "values(?, ?, ?, ?, ?)";
                                console.log("gds_orders_comments table:- received parameters are: "+odrId+" || "+usrId+" || "+containers);
                                db.query(verify_query, ['136', odrId, '136007', containers + " was / were verified", usrId], function(err, gocResult){
                                    if(err){ 
                                        console.log("Error: "+err+"\n");
                                        return err;
                                    }
                                });
                                
				callback(result);
			});
			//console.log(query.sql);
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			callback(err);
		}
	},
	getPickedCrateDetails: function(pickingDate, callback){
		console.log("getPickedCrateDetails:- received parameters are: "+pickingDate);
		try{
			console.log(pickingDate+' 00:00:00'+' -- '+pickingDate+' 23:59:59 \n');
			var qry = "select distinct(container_barcode), container_type, getUserName(picked_by,2) as `picker_name`, "
			+"picked_by as picker_id, is_verified, getUserName(verified_by,2) as `verified_by` "
			+"from picker_container_mapping where created_at>=? "
			+"and created_at<=? group by container_barcode order by container_barcode";
			console.log("Query: \n"+qry);
			db.query(qry, [pickingDate+' 00:00:00',pickingDate+' 23:59:59'], function(err, result){
				if(err){ 
					console.log("Error: "+err+"\n");
					return err;
				}
				callback(result);
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			callback(err);
		}
	},
	saveReason: function(odrId, prodId, container, reason, qty, usrId, callback){
		console.log('Log Started @ '+dateFormat().format("YYYY-MM-DD HH:mm:ss")+"\n");
		console.log("saveReason:- received parameters are: "+odrId+" || "+prodId+" || "+container+" || "+reason+" || "+qty);
		try{
			var qry = "UPDATE `picker_container_mapping` "
					+"SET `wrong_picked_reason` = ? , `wrong_picked_qty` = ? "
					+"WHERE `order_id`= ? and `productid`=? and `container_barcode` =?";
			console.log("Query: \n"+qry);
			db.query(qry, [reason,qty,odrId,prodId,container], function(err, result){
				if(err){ 
					console.log("Error: "+err+"\n");
					return err;
				}
				/*var qry1 = "UPDATE `picker_container_mapping` "
					+"SET `is_verified` = '1', `verified_by` = ?, `verification_time` = ? "
					+"WHERE `order_id`= ? and `container_barcode` = ?";
				Crate.query(qry1, [usrId,dateFormat().format("YYYY-MM-DD HH:mm:ss"),odrId,container], function(err, result){
					if(err){ 
						console.log("Error: "+err+"\n");
						return err;
					}
				});*/
				callback(result);
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			callback(err);
		}
	},
	getReasons: function(query, callback){
		console.log("model getPickedCrateDetails");
		try{
			var qry = "select master_lookup_name as name,value from master_lookup where mas_cat_id = ?";
			console.log("Query: \n"+qry);
			db.query(qry, [138], function(err, result){
				if(err){ 
					console.log("Error: "+err+"\n");
					return err;
				}
				//console.log(result);
				callback(result);
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			callback(err);
		}
	},
	getSavedReasons: function(odrId, prodId, container, callback){
		console.log("model getSavedReasons, parameters:"+odrId+" || "+prodId+" || "+container);
		try{
			var qry = "select getMastLookupValue(wrong_picked_reason) as reason,wrong_picked_reason as reason_code, "
			+"wrong_picked_qty as qty from picker_container_mapping "
			+"where order_id=? and productid=? and container_barcode=?";
			console.log("Query: \n"+qry);
			db.query(qry, [odrId,prodId,container], function(err, result){
				if(err){ 
					console.log("Error: "+err+"\n");
					return err;
				}
				//console.log(result);
				callback(result);
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			callback(err);
		}
	},
	getSavedReasonsByCrate: function(odrId, container, callback){
		console.log("model getSavedReasonsByCrate, parameters:"+odrId+" || "+container);
		try{
			var qry = "select p.product_id, p.product_title, getMastLookupValue(pcm.wrong_picked_reason) as reason, "
			+"pcm.wrong_picked_qty as qty, pcm.container_barcode as crate "
			+"from picker_container_mapping pcm "
			+"join products p on (pcm.productid=p.product_id) "
			+"where order_id=? and container_barcode in (?) and pcm.wrong_picked_reason is not null";
			//console.log("Query: \n"+qry);
			db.query(qry, [odrId,container], function(err, result){
				if(err){ 
					console.log("Error: "+err+"\n");
					return err;
				}
				//console.log(result);
				callback(result);
			});
		}catch(err){
			console.error('Internal Error: '+err+"\n");
			callback(err);
		}
	},
    verifyPickedQtyApi: function(orderId, lpToken, callback){
        console.log("In verifyPickedQtyApi model, parameters:" + orderId + " || " + lpToken);
        var data = {"data": JSON.stringify({"token": "" + lpToken + "", "order_id": "" + orderId + ""})};
	console.log(data);
        var request = require("request");
        var options = {
            method: 'POST',
            url: config['verification_url'],
            headers: {
                'content-type': 'application/json'
            },
            body: data,
            json: true
        };
        request.post(options, function(error, response, body) {
            if (error) {
                console.error(error);
            }
            else {
                console.log(body);
                callback(body);
            }
        });
    },
    generateInvoiceApi: function(orderId, lpToken, callback){
        console.log("In generateInvoiceApi model, parameters:" + orderId + " || " + lpToken);
        var data = {"data": JSON.stringify({"token": "" + lpToken + "", "order_id": "" + orderId + ""})};
        var request = require("request");
        var options = {
            method: 'POST',
            url: config['generateinv_url'],
            headers: {
                'content-type': 'application/json'
            },
            body: data,
            json: true
        };
        request.post(options, function(error, response, body) {
            if (error) {
                console.error(error);
            }
            else {
                console.log(body);
                callback(body);
            }
        });
    },
    getTokenByUserId: function (userId, callback) {
        console.log("In getToenByUserId model, parameters:" + userId);
        try {
            var qry = "select lp_token, password_token from users where user_id = ?";
            console.log("Query: \n" + qry);
            db.query(qry, [userId], function (err, result) {
                if (err) {
                    console.log("Error: " + err + "\n");
                    return err;
                }
                console.log(result);
                if(result[0].lp_token != null){
                    callback(result[0].lp_token);
                } else {
                    callback(result[0].password_token);
                }
            });
        } catch (err) {
            console.error('Internal Error: ' + err + "\n");
            callback(err);
        }
    }
};

