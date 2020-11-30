const db =  require('../../dbConnection');
module.exports={
	getData:function(data,callback){
		console.log('model',data);
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
		if(data.dcs == true){
			sql +=" and legal_entity_type_id in (1014,1016)";
		}else{
			sql +=" and legal_entity_type_id not in (1014,1016)";
		}
		if(data.groupby!='' && data.groupby!=undefined){
			sql+=" Group By "+data.groupby;
		}
		if(data.hasOwnProperty('limit')){
			sql+=" limit "+data['limit'];
		}
		//sql+=" limit 200";
		console.log('**',sql);
		db.query(sql,function(err,res){		
			callback(err,res);
		});		
	}
}