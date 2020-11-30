var unirest = require('unirest');
var orderInfoModel = {
	getdata:function(order_id, callback){
		try{

				// console.log("select PCM.order_id as OrderId, GO.order_code as OrderCode,GO.order_date as OrderDate, container_barcode as ContainerBarcode, container_num as ContainerNo, GetUserName(PCM.picked_by, 2) AS pickedBy, product_barcode as ProductBarcode, PCM.productid AS ProductId,PP.sku,ML.master_lookup_name AS PackType,PPC.no_of_eaches AS EachesCount,PCM.qty from picker_container_mapping  as PCM JOIN gds_orders AS GO ON PCM.order_id = GO.gds_order_id LEFT JOIN product_pack_config AS PPC ON PCM.product_barcode = PPC.pack_sku_code AND PCM.productid = PPC.product_id JOIN products AS PP ON PCM.productid = PP.product_id LEFT JOIN master_lookup AS ML ON PPC.level = ML.value where PCM.order_id = "+order_id+" ");
  				con.query("select PCM.order_id as OrderId, GO.order_code as OrderCode,GO.order_date as OrderDate, container_barcode as ContainerBarcode, RIGHT(container_barcode,3) as ContainerNo, GetUserName(PCM.picked_by, 2) AS pickedBy, product_barcode as ProductBarcode, PCM.productid AS ProductId,PP.sku,ML.master_lookup_name AS PackType,PPC.no_of_eaches AS EachesCount,PCM.qty from picker_container_mapping  as PCM JOIN gds_orders AS GO ON PCM.order_id = GO.gds_order_id LEFT JOIN product_pack_config AS PPC ON PCM.product_barcode = PPC.pack_sku_code AND PCM.productid = PPC.product_id JOIN products AS PP ON PCM.productid = PP.product_id LEFT JOIN master_lookup AS ML ON PPC.level = ML.value where PCM.order_id = '"+order_id+"' ", function(error,rows)
		            {
		                if(error)
		                {
		                    console.log(error);
		                }
		                
		                // var myRespnse = JSON.stringify(rows);
		                // var requeststatus = rows[0]['is_cancelled'];
		       //          for (var i in rows) {
					    //     console.log('Container barcodes: ', rows[i].ContainerBarcode);
					    // }
		                // console.log(rows);
		                callback(JSON.stringify(rows));
		        });

    

		}catch(err){
            console.error(err);
            return err;
        }
	},
};

module.exports = orderInfoModel;