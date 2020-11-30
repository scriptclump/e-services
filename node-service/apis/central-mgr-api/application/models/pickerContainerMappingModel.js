var unirest = require('unirest');
var pickerContainerMappingModel = {
	getdata:function(bar_code, callback){
		try{

  				con.query("SELECT PCM.container_barcode AS ContainerBarcode, IF(container_barcode LIKE '%-%' ,SUBSTRING_INDEX(PCM.container_barcode,'-', -1), RIGHT(PCM.container_barcode, 5)) as ContainerNo,GO.order_code AS order_num,GetUserName(PCM.picked_by, 2) AS pickedBy,PCM.product_barcode AS ProductBarcode,PCM.productid AS ProductId, PP.sku,ML.master_lookup_name AS PackType,PPC.no_of_eaches AS EachesCount,PCM.qty, PP.product_title, getBeatName(GO.beat) as beat_name, GOT.st_docket_no, LW.lp_wh_name AS HUB,GOP.mrp, GOP.qty FROM picker_container_mapping AS PCM JOIN gds_orders AS GO ON PCM.order_id = GO.gds_order_id LEFT JOIN product_pack_config AS PPC ON PCM.product_barcode = PPC.pack_sku_code AND PCM.productid = PPC.product_id JOIN products AS PP ON PCM.productid = PP.product_id LEFT JOIN master_lookup AS ML ON PPC.level = ML.value join gds_order_track as GOT on GO.gds_order_id = GOT.gds_order_id LEFT JOIN legalentity_warehouses AS LW ON GO.hub_id = LW.le_wh_id join gds_order_products as GOP on GOP.gds_order_id = GO.gds_order_id and GOP.product_id = PCM.productid WHERE PCM.container_barcode='"+bar_code+"' AND order_id = (SELECT order_id FROM picker_container_mapping WHERE container_barcode = '"+bar_code+"' GROUP BY order_id ORDER BY created_at DESC LIMIT 1)", function(error,rows)
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

module.exports = pickerContainerMappingModel;