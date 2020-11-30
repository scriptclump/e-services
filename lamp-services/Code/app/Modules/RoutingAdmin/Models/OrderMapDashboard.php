<?php 
namespace App\Modules\RoutingAdmin\Models;

use App\Modules\LegalEntities\Models\Legalentity;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Config;
use DB;
use Log;
use  \Exception;


class OrderMapDashboard extends Model{

	public function __construct(){
		$this->_roleModel = new Role();

	}

	/**
	 * get all orders based on date
	 * @param  date
	 * @return array
	 */
	public function getAllOrdersByDate( $from_date = '', $to_date = '')
	{
		$wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
        $wh_list = (json_decode($wh_list['sbu'],true));
        $dc_acess_list = isset($wh_list['118001']) ? $wh_list['118001'] : 'NULL';
		$query = "SELECT 
					go.gds_order_id, 
					go.cust_le_id, 
					go.order_status_id, 
					getMastLookupValue(go.order_status_id) as order_status,
					go.total as order_total,
					go.hub_id,
					getLeWhName(go.hub_id) as hub_name,
					go.le_wh_id,
					go.beat as beat_id,
					getBeatName(go.beat) as beat,
					go.order_code, 
					le.latitude, 
					le.longitude, 
					go.order_date,
					go.firstname as first_name,
					go.lastname as last_name,
					go.shop_name as company,
					le.address1 as addr1,
					le.address2 as addr2,
					le.city,
					le.pincode as postcode,
					le.locality,
					le.landmark,
					go.created_by as ff_id,
					GetUserName(go.created_by,2) as cteated_by,
					if(go.is_self=0,'No','Yes') as is_self
					FROM `gds_orders` go
					LEFT JOIN legal_entities le ON le.legal_entity_id = go.cust_le_id
					LEFT JOIN legalentity_warehouses lw ON lw.le_wh_id = go.hub_id
					WHERE (go.order_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59') and go.le_wh_id in ($dc_acess_list) order by 
					lw.sort_order,
					beat";
		$data = DB::select($query);
		if (count($data) > 0) {	
			$data = json_decode(json_encode($data),true);

			$orderIds = array_column($data, 'gds_order_id');

			$query2 = "SELECT 
					go.gds_order_id, 
					go.cust_le_id, 
					go.order_status_id, 
					getMastLookupValue(go.order_status_id) as order_status,
					go.total as order_total,
					go.hub_id,
					getLeWhName(go.hub_id) as hub_name,
					go.le_wh_id,
					go.beat as beat_id,
					getBeatName(go.beat) as beat,
					go.order_code, 
					le.latitude, 
					le.longitude, 
					go.order_date,
					go.firstname as first_name,
					go.lastname as last_name,
					go.shop_name as company,
					le.address1 as addr1,
					le.address2 as addr2,
					le.city,
					le.pincode as postcode,
					le.locality,
					le.landmark,
					go.created_by as ff_id,
					GetUserName(go.created_by,2) as cteated_by,
					if(go.is_self=0,'No','Yes') as is_self,
					gc.comment as reason
					FROM `gds_orders` go
					LEFT JOIN legal_entities le ON le.legal_entity_id = go.cust_le_id
					LEFT JOIN legalentity_warehouses lw ON lw.le_wh_id = go.hub_id
					LEFT JOIN gds_orders_comments gc ON 
						(gc.entity_id = go.gds_order_id and gc.order_status_id=17014)
					WHERE (go.order_status_id=17014 and go.gds_order_id NOT IN (".implode(',', $orderIds).")) and go.le_wh_id in ($dc_acess_list)
					group by 
						go.gds_order_id
					order by 
						lw.sort_order,
						beat";

			$data2 = DB::select($query2);

			if (count($data2) > 0) {
				$data2 = json_decode(json_encode($data2),true);
				$data = array_merge($data,$data2);
			}
			return $data;

		}else{
			return false;
		}
	}

	/**
	 * FieldForce list by role id 53 
	 * @param  null
	 * @return void
	 */
	public function getFieldForceList($role_id)
	{
		// 	$query = "SELECT distinct(users.`user_id`), 
	     //    GetUserName(users.`user_id`, 2) AS `name`, roles.`name` AS `role` 
	     //    FROM `user_permssion` 
	     //    LEFT JOIN users ON users.`user_id` = user_permssion.`user_id` 
	     //    LEFT JOIN user_roles ON user_roles.`user_id` = users.`user_id` 
	     //    LEFT JOIN roles ON roles.`role_id` = user_roles.`role_id` 
	     //    WHERE user_roles.`role_id` = $role_id AND users.is_active = 1
		//    ";
	
		$query = "SELECT users.`user_id`AS 'ID',CONCAT(IFNULL(users.`firstname`,' '),IFNULL(users.`lastname`,' '))AS NAME,
		pjp_pincode_area.`pjp_name`AS 'AREA_NAME' , pjp_pincode_area.pjp_pincode_area_id AS 'beat_id'
		FROM `pjp_pincode_area` JOIN users   LEFT JOIN user_roles ON user_roles.`user_id` = users.`user_id`  WHERE pjp_pincode_area.`rm_id`=users.`user_id` AND user_roles.`role_id` = $role_id 
		";

	        $db_data = DB::select($query);
	      
	        if(!empty($db_data)){
	        	// $tempData = []; 
	        	// foreach ($db_data as $key => $fieldforce) {
	        	// 	$tempData[$fieldforce->user_id]= $fieldforce->name;
	        	// }

	            // $db_data = json_decode(json_encode($tempData),true);
	             return $db_data;

	        }else{

	            return array();
	        }
	}

	/**
	 * Get all beats
	 * @return void
	 */
	public function getAllBeats()
	{
			$query = "select pjp_pincode_area_id,pjp_name,le_wh_id FROM pjp_pincode_area ORDER BY le_wh_id ";
			$db_data = DB::select($query);
			if($db_data != null) {
				return $db_data;
				// if (!empty($db_data)) {
				// 	$temp_beat = [];
				// 	foreach ($db_data as $key => $beat_data) {
				// 		$temp_beat[$beat_data->beat_id] = [
				// 			'beat_name' => $beat_data->beat_name,
				// 			'le_wh_id' => $beat_data->le_wh_id
				// 		];
				// 	}
				// 	// $db_data = json_decode(json_encode($temp_beat),true);
				// 	return $db_data;
				// }else{
				// 	return [];
				// }
			} else {
				return [];
			}
		
	}

	/**
	 * Get list of order status
	 * @return void
	 */
	public function getListOfOrderStatus()
	{
		$query = "SELECT 
					ml.value AS order_status, 
					ml.master_lookup_name AS order_status_name
					FROM master_lookup ml
					WHERE ml.mas_cat_id = 17 AND ml.value IS NOT NULL";
		$db_data = DB::select($query);
		if (!empty($db_data)) {
			$temp_order_status = [];
			foreach ($db_data as $key => $status_list) {
				$temp_order_status[$status_list->order_status] = $status_list->order_status_name;
			}
			$retrun_data = json_decode(json_encode($temp_order_status),true);
            return $retrun_data;
		}else{
			return [];
		}
	}

	/**
	 * Get List of active HUBs with Lat-long
	 * @return  void [<description>]
	 */
	public function getHubList(){
		$wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
        $wh_list = (json_decode($wh_list['sbu'],true));
        $hub_acess_list = isset($wh_list['118002']) ? $wh_list['118002'] : 'NULL';
		$query = "SELECT 
					lw.le_wh_id, bu.bu_name, lw.longitude, lw.latitude, lw.address1, lw.address2 
					FROM legalentity_warehouses lw
					JOIN business_units bu ON (lw.bu_id=bu.bu_id)
					WHERE bu.is_active=1 AND lw.status=1 AND lw.dc_type=118002 AND lw.le_wh_id in ($hub_acess_list) AND lw.longitude>0";
		$db_data = DB::select($query);
		if (!empty($db_data)) {
			$retrun_data = json_decode(json_encode($db_data),true);
            return $retrun_data;
		}else{
			return [];
		}

	}
	
	public function getAllHUB(){

		$hub = array();
		$wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
	   $wh_list = (json_decode($wh_list['sbu'],true));
		$hub_list = isset($wh_list['118002']) ? $wh_list['118002'] : 'NULL';
		$query = "SELECT l.lp_wh_name,l.`le_wh_id`,d.`dc_id`,d.`hub_id` ,l.longitude, l.latitude, l.address1 FROM dc_hub_mapping d INNER JOIN legalentity_warehouses l ON d.hub_id = l .le_wh_id";
		
		$data = DB::select($query);
		if(count($data) > 0){
			
			return $data;

		}else{
			return false;
		} 
}


public function getAllDC(){

	$dc = array();
	$wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
   $wh_list = (json_decode($wh_list['sbu'],true));
   $dc_list = isset($wh_list['118001']) ? $wh_list['118001'] : 'NULL';
	$query = "Select le_wh_id,lp_wh_name from legalentity_warehouses where dc_type=118001 and status = 1 and le_wh_id In(".$dc_list.")";
	$data = DB::select($query);
	if(count($data) > 0){

		$data = json_decode(json_encode($data),true);
		foreach ($data as $key => $value) {
			
			$dc[$value['le_wh_id']] = $value['lp_wh_name'];
		}
		return $data;
	}else{
		return $data;
	}
}


}