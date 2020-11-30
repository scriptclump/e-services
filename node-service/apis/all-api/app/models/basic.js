const db = require('../../dbConnection');

module.exports={
	getData:function(data,callback){
		console.log('model');
		var sql="select ";
		console.log(data);
		if(data.displayData != ''){
		 	sql+= data.displayData;
		}else{
		 	sql+="*"
		}
		sql+=" from " ;
		console.log('table name',data.table);
		console.log(data);
		/*if(data.groupby!='' && data.groupby!=undefined && data.table=='vw_dynamic_order_details'){
			sql="select gds_order_id,order_code,product_id,product_name,brand_id,brand_name,category_id,category_name,manufacturer_id,manufacturer_name,sum(unit_price),sum(elp),sum(order_qty),sum(invoice_qty),sum(return_qty),sum(cancel_qty),sum(booked_tbv),sum(delivered_tbv),sum(booked_tgm),sum(delivered_tgm) from ";
		}*/

		if(data.table!=''){
			sql+=data.table;
		}
		if(data.filtertext!=''){
			sql+=" "+data.filtertext;
		}
		if(data.fromdate!=''&&data.todate!=''){
			if(data.filtertext!=""){
				sql+=" and order_date between '"+data.fromdate+'\' and \''+data.todate+"\'";
			}else{
				sql+=" where order_date between '"+data.fromdate+'\' and \''+data.todate+"\'";
			}
		}
		if(data.groupby!='' && data.groupby!=undefined){
			sql+=" Group By "+data.groupby;
		}
		sql+=" limit 200";
		console.log(sql);
		db.query(sql,{},function(err,res){		
			callback(err,res);
		})
	}
}