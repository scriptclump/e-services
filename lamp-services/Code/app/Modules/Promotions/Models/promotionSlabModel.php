<?php
/*
FileName : promotionSlabModel.php
Author   :eButor
Description : All the function for download /view the slab data.
CreatedDate : 29/may/2017
*/
//defining namespace
namespace App\Modules\Promotions\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class promotionSlabModel extends Model
{
    
    public function slabReportDetails($makeFinalSql, $orderBy, $page, $pageSize, $sqlForOrderDate){

    	$sqlWhrCls = '';
		$countLoop = 0;

		foreach ($makeFinalSql as $value) {
			if( $countLoop==0 ){
				$sqlWhrCls .= ' WHERE ' . $value;
			}elseif( count($makeFinalSql)==$countLoop ){
				$sqlWhrCls .= $value;
			}else{
				$sqlWhrCls .= ' AND ' .$value;
			}
			$countLoop++;
		}

 		if($orderBy ==''){
      		$orderBy = '';
        }else{
   			$orderBy = ' ORDER BY '.$orderBy;

        }

        $dateCls = ' WHERE DATE(gord.`order_date`) = DATE(NOW())';
		if($sqlForOrderDate!=''){
 			 $dateCls = ' WHERE ' . $sqlForOrderDate;
		}

		//get data from db based on above conditions
  		$sqlQuery ="select *, (OrderQty/NoOfEaches) AS 'CFCSold' FROM (
					SELECT gord.`gds_order_id`, gord.`order_code`, 
					gord.`hub_id`, (SELECT legalentity_warehouses.`lp_wh_name` FROM  `legalentity_warehouses` WHERE legalentity_warehouses.`le_wh_id` = gord.`hub_id`) AS 'HUBName',
					CONCAT(usr.firstname, ' ', usr.lastname) AS 'SOName', usr.`mobile_no` AS 'SONumber',
					gord.`cust_le_id`, 
					prmt.end_range AS 'ESU_qty', prmt.price AS 'Slabrates', 
					(SELECT master_lookup_name FROM master_lookup WHERE master_lookup.value=gprd.`order_status`) AS 'OrderStatus',
					(SELECT no_of_eaches FROM product_pack_config AS pk WHERE pk.`level`=16004 AND pk.is_sellable=1 AND pk.product_id=gprd.product_id ORDER BY effective_date DESC LIMIT 1) AS 'NoOfEaches',
					(SELECT pin.`officename` FROM customers AS cust INNER JOIN cities_pincodes AS pin ON pin.`city_id`=cust.`area_id` WHERE cust.`le_id`=gord.`cust_le_id`) AS 'AreaName',
					gord.`shop_name`, DATE_FORMAT(gord.`order_date`,'%d-%m-%Y') AS 'OrderDate', gord.`order_date`, 
					TRIM(gprd.`pname`) AS 'ProdutName', TRIM(gprd.sku) AS 'ProductSKU', gprd.mrp, gprd.qty AS 'OrderQty', gprd.total, gprd.order_status,
					gord.`beat`,
					(SELECT pjp_name FROM pjp_pincode_area AS bt WHERE bt.`pjp_pincode_area_id`=gord.`beat`) AS 'BeatName'
					FROM gds_order_products AS gprd
					INNER JOIN gds_orders AS gord ON gord.`gds_order_id`=gprd.`gds_order_id`
					INNER JOIN products_slab_rates AS prmt ON prmt.product_slab_id=gprd.product_slab_id
					INNER JOIN users AS usr ON usr.`user_id`=gord.`created_by`
					".$dateCls."
					)  AS innertbl ".$sqlWhrCls . $orderBy ;
					
          $allRecallData = DB::select(DB::raw($sqlQuery));
         $TotalRecordsCount = count($allRecallData);
	    return json_encode(array('results'=>$allRecallData));
    }
    /**
     * [getDataAsPerQueryForSlabReport Get list of slab applied orders]
     * @param  [date] $FinalDate [date]
     * @return [array]            [orderws list]
     */
    public function getDataAsPerQueryForSlabReport($FinalDate){
    			//get data from db based on $FinalDate
    	$slabQuery = "
					select *, (OrderQty/NoOfEaches) AS 'CFCSold' FROM (
					SELECT gord.`gds_order_id`, gord.`order_code`, 
					gord.`hub_id`, (SELECT legalentity_warehouses.`lp_wh_name` FROM  `legalentity_warehouses` WHERE legalentity_warehouses.`le_wh_id` = gord.`hub_id`) AS 'HUBName',
					CONCAT(usr.firstname, ' ', usr.lastname) AS 'SOName', usr.`mobile_no` AS 'SONumber',
					gord.`cust_le_id`, 
					CASE prmt.prmt_det_status WHEN 0 THEN 'InActive' WHEN 1 THEN 'Active' ELSE 'InActive' END AS 'promotionStatus',prmt.prmt_det_id AS 'promotionid',
					 DATE_FORMAT(prmt.start_date,'%Y-%m-%d') AS 'start_date',
					 DATE_FORMAT(prmt.end_date,'%Y-%m-%d') AS 'end_date',
					prmt.end_range AS 'ESU_qty', prmt.price AS 'Slabrates', 
					(SELECT master_lookup_name FROM master_lookup WHERE master_lookup.value=gprd.`order_status`) AS 'OrderStatus',
					(SELECT no_of_eaches FROM product_pack_config AS pk WHERE pk.`level`=16004 AND pk.is_sellable=1 AND pk.product_id=gprd.product_id ORDER BY effective_date DESC LIMIT 1) AS 'NoOfEaches',
					(SELECT pin.`officename` FROM customers AS cust INNER JOIN cities_pincodes AS pin ON pin.`city_id`=cust.`area_id` WHERE cust.`le_id`=gord.`cust_le_id`) AS 'AreaName',
					gord.`shop_name`, DATE_FORMAT(gord.`order_date`,'%d-%m-%Y') AS 'OrderDate', gord.`order_date`, 
					TRIM(gprd.`pname`) AS 'ProdutName', TRIM(gprd.sku) AS 'ProductSKU', gprd.mrp, gprd.qty AS 'OrderQty', gprd.total, gprd.order_status,
					gord.`beat`,
					(SELECT pjp_name FROM pjp_pincode_area AS bt WHERE bt.`pjp_pincode_area_id`=gord.`beat`) AS 'BeatName',
					(SELECT le_code FROM legal_entities AS le WHERE le.`legal_entity_id`= gord.`cust_le_id`) AS 'RetailerCode'
					FROM gds_order_products AS gprd
					INNER JOIN gds_orders AS gord ON gord.`gds_order_id`=gprd.`gds_order_id`
					INNER JOIN products_slab_rates AS prmt ON prmt.product_slab_id=gprd.product_slab_id
					INNER JOIN users AS usr ON usr.`user_id`=gord.`created_by`
					WHERE DATE(	gord.`order_date` ) $FinalDate ) as innertbl";
 					$slabData = DB::select(DB::raw($slabQuery));
					return json_encode($slabData);
    }
}