<?php
namespace App\Modules\RoutingAdmin\Models;

use App\Modules\LegalEntities\Models\Legalentity;
use Illuminate\Database\Eloquent\Model;
use App\Central\Repositories\RoleRepo;
use App\Modules\Roles\Models\Role;
use Config;
use DB;
use Log;
use  \Exception;


class RouteDataModel extends Model{

	public function __construct(){
		 $this->_roleModel = new Role();

	}
	public function getAllDC(){

		$dc = array();
		$wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
        $wh_list = (json_decode($wh_list['sbu'],true));
        $dc_list = isset($wh_list['118001']) ? $wh_list['118001'] : 'NULL';
		$query = "Select le_wh_id,lp_wh_name from legalentity_warehouses where dc_type=118001 and status = 1 and le_wh_id In(".$dc_list.") order by lp_wh_name asc";
		//echo $query;exit;
		$data = DB::select($query);
		if(count($data) > 0){
			$data = json_decode(json_encode($data),true);
			 foreach ($data as $key => $value) {
			 	$dc[$value['le_wh_id']] = $value['lp_wh_name'];
			 }
			return $dc;
		}else{
			return $dc;
		}
	}

	public function getAllHUB(){

			$hub = array();
			$wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
	        $wh_list = (json_decode($wh_list['sbu'],true));
			$hub_list = isset($wh_list['118002']) ? $wh_list['118002'] : 'NULL';
			//$query = "SELECT l.lp_wh_name,l.`le_wh_id`,d.`dc_id`,d.`hub_id` ,l.longitude, l.latitude, l.address1 FROM dc_hub_mapping d INNER JOIN legalentity_warehouses l ON d.hub_id = l .le_wh_id";
			$query = "Select 
			business_units.parent_bu_id as dc_bu_id,
			legalentity_warehouses.le_wh_id,
			legalentity_warehouses.lp_wh_name 
			from 
			legalentity_warehouses 
			left join business_units on business_units.bu_id = legalentity_warehouses.bu_id
			where 
			legalentity_warehouses.dc_type=118002 and legalentity_warehouses.status = 1 or legalentity_warehouses.le_wh_id In(".$hub_list.") order by legalentity_warehouses.sort_order";
			$data = DB::select($query);
			//print_r($data);exit;
			if(count($data) > 0){

				$data = json_decode(json_encode($data),true);
				$dc = array();
				$bu_ids = array();

				foreach ($data as $key => $value) {
					
					if(!isset($dc[$value['dc_bu_id']])) {
						$dc[$value['dc_bu_id']] = array();
						array_push($bu_ids,$value['dc_bu_id']);
					}
					$hub[$value['le_wh_id']] = $value['lp_wh_name'];
					$dc[$value['dc_bu_id']][$value['le_wh_id']] = $hub[$value['le_wh_id']];
				}
				//print_r($dc);exit;

				$dc_temp = array();
				foreach ($dc as $key => $value) {
					if($key != ""){
						$dc_id = $this->getWarehouseFromBuId($key);
						if(!$dc_id){
							//throw new Exception("DC id for this BU_id not found");
							$dc_id['le_wh_id'] = 0; // 0 are no dc found
						}
						$dc_temp[$dc_id['le_wh_id']] = $value;
					}
				}
				// return $dc_temp;
				return $dc_temp;
			}else {
				return $hub;
			}

			/*$data = DB::select($query);
			if(count($data) > 0){

				return $data;

			}else{
				return false;
			} */
	}

	/**
	 * [getWarehouseFromBuIds description]
	 * @param  [type] $bu_id [description]
	 * @return [type]        [description]
	 * optimise later check this out
	 */
	public function getWarehouseFromBuId($bu_id){

		$query = 'select * from legalentity_warehouses where bu_id = '.$bu_id;
		$data = DB::select($query);
		if(count($data) > 0){
			$data = json_decode(json_encode($data),true);
			return $data[0];
		}else{
			return false;
		}


	}

	public function getVehiclesAvailableHub($hublist){


	}

	/**
	 * [getvehicleDataEachHub description]
	 * @param  [type] $hub_id [description]
	 * @return [type]         [description]
	 */

	// old query select * from vehicle where is_active = 1 and hub_id = $hub_id
	public function getvehicleDataEachHub($hub_id,$load_listed=0){

		$vehicle_dump = array();
		$key = "vehicles_details";
		$query = "select * from vehicle JOIN vehicle_attendance AS va ON va.vehicle_id = vehicle.vehicle_id where is_active = 1 and hub_id IN ($hub_id) and va.attn_date='".date('Y-m-d')."' and va.is_present=1";
		$data = DB::select($query);
		if(count($data) > 0){
			
			$data = json_decode(json_encode($data),true);
			$i = 0;
			foreach ($data as $key => $value) {
				
				$length = $value['length'] / 1000;
				$breadth = $value['breadth'] / 1000 ;
				$height = $value['height'] / 1000;

				$volume =  $length * $breadth * $height;
				$volume = number_format($volume,4, '.', '');

				$vehicle_dump[$i]['vehicle_number'] = $value['reg_no'];
				$vehicle_dump[$i]['vehicle_id'] = $value['vehicle_id'];

				if($load_listed != 0){

					$vehicle_dump[$i]['vehicle_max_load'] = ($volume * $load_listed)/100;
				}else{
					$vehicle_dump[$i]['vehicle_max_load'] = $volume;
				}
				
				$i++;				

			}
		}

		return $vehicle_dump;
	}

	/**
	 * [getOrderListByHubId description]
	 * dummy now will add up the set up
	 * @return [type] [description]
	 */
	public function getOrderListByHubId($hub_id){

		$query = '	select gds_orders.gds_order_id,
					getBeatName(gds_orders.beat) as beat,
					gds_orders.order_code,
					latitude as `lat`,
					longitude as `long`,
					legal_entities.business_legal_name,
					legal_entities.address1,
					legal_entities.address2,
					legal_entities.city,
					legal_entities.pincode,
					sum(gds_order_products.no_of_units) as esu_count,
					gds_invoice_grid.grand_total as invoice_amount

					from 
					gds_orders
					left join legal_entities on gds_orders.cust_le_id = legal_entities.legal_entity_id
					left join gds_order_products on gds_order_products.gds_order_id = gds_orders.gds_order_id
					left join gds_invoice_grid on gds_invoice_grid.gds_order_id = gds_orders.gds_order_id
					where gds_orders.hub_id = '.$hub_id.' 
					 
					
					and ( 
						gds_orders.order_status_id = 17014 OR 
						gds_orders.order_status_id = 17025 
					) group by gds_order_id order by gds_order_id';
					//group by gds_order_id order by gds_order_id limit 100';
					//gds_orders.order_status_id = 17021 OR gds_orders.order_status_id = 17024 
		$data = DB::select($query);
		if(count($data) > 0){

			$data = json_decode(json_encode($data),true);
			$order_data = array();

			$escape_array = array(":", "-", "/", "*","'","\n\r","\n","\r");

			foreach ($data as $key => $value) {
				
				// if($value['lat'] == 0.000000 || $value['long'] == 0.000000){
				// 	return false;
				// }else{
				
				$address = $value['address1'].' '.$value['address2'];
				$temp['gds_order_id'] = $value['gds_order_id'];
				$temp['order_code'] = $value['order_code'];
				$temp['invoice_amount'] = $value['invoice_amount'];
				$temp['lat'] = $value['lat'];
				$temp['long'] = $value['long'];
				$temp['beat'] = $value['beat'];
				$temp['shop_name'] = str_replace($escape_array,'', $value['business_legal_name']);
				$temp['esu_count'] = $value['esu_count'];
				$temp['address_info']['address'] = str_replace($escape_array,"",$address);
				$temp['address_info']['city'] = $value['city'];
				$temp['address_info']['pin'] = $value['pincode'];
				$temp['crates_info'] = $this->getCratesCountOnOrderId($value['gds_order_id']);
				$temp['other_info'] = $this->bagAndCfcCountByOrderId($value['gds_order_id']);

				if($temp['crates_info']['crates_count'] == 0){
					$count_crates = 1;
				}else{
					$count_crates = $temp['crates_info']['crates_count'];
				}

					$volume_total = $count_crates * (50 + 10 ); //in ebutor one crate has 50 liters capacity
					//to nulify the system we will take in 10 lits more for each crate that will accomadte bags
					$volume_in_meters = number_format(($volume_total/1000), 4, '.', ''); 
					$temp['weight'] = $volume_in_meters;
					$order_data[$key] = $temp;
				//}

				}

				return $order_data;

			}else{

				return array();
			}

		}

	/**
	 * [getCratesCountOnOrderId description]
	 * @param  [type] $order_id [description]
	 * @return [type]           [description]
	 */
	private function getCratesCountOnOrderId($order_id){

		$query = "select 
		order_id,
		container_barcode 
		from 
		picker_container_mapping 
		where order_id = $order_id
		group by order_id,container_barcode
		";
		$data = DB::select($query);
		if(count($data) > 0 ){

			$data = json_decode(json_encode($data),true);
			$return['crates'] = array();
			$return['crates_count'] = 0;
			foreach ($data as $key => $value) {
				
				array_push($return['crates'],$value['container_barcode']);
				$return['crates_count'] += 1;
			}
		}else{
			$return['crates'] = array();
			$return['crates_count'] = 0;
		}

		return $return;

	}

	/**
	 * [getHUBCoordinates description]
	 * @param  [type] $hub_id [description]
	 * @return [type]         [description]
	 * use caching after this
	 */
	public function getHUBCoordinates($hub_id){

		$query = 'select latitude as `lat`,longitude as `long` from legalentity_warehouses where le_wh_id = '.$hub_id;
		//$query = 'select latitude as `long`,longitude as `lat` from legalentity_warehouses where le_wh_id = '.$hub_id;
		$data = DB::select($query);
		if(count($data) > 0){
			$data = json_decode(json_encode($data),true);
			return $data[0];
		}else{
			return array();
		}
	}


	/**
	 * [curlRequest description]
	 * @param  [type] $url        [description]
	 * @param  [type] $port       [description]
	 * @param  [type] $postfields [description]
	 * @return [type]             [description]
	 */
	public function curlRequest($url,$port,$postfields){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_PORT,$port);        
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postfields);
		$buffer = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if(empty ($buffer)){ 
			return array('status'=>$status,'data'=>'');
		}else{ 
			return array('status'=>$status,'data'=>$buffer);
		} 
	}

	/**
	 * [storeRoutesToDB description]
	 * @param  [type] $routes   [description]
	 * @param  [type] $route_id [description]
	 * @param  [type] $hub_id   [description]
	 * @return [type]           [description]
	 */
	public function storeRoutesToDB($routes,$route_id,$hub_id){

		if(is_array($routes)){

			if(count($routes) > 0){
				$routes=$routes['data'];

				if(isset($routes['extra_vehicles'])){
					unset($routes['extra_vehicles']);
				}

				$unassigned_coordinates = array();
				if(isset($routes['unassigned_coordinates'])){
					$unassigned_coordinates = $routes['unassigned_coordinates'];
					unset($routes['unassigned_coordinates']);
				}
				foreach ($routes as $key => $route) {
					$vehicleInfo = $route['vehicleInfo']['vehicle_number'];
					$orderCount = $route['vehicleInfo']['orderCount'];
					$crate_count = 0;
					foreach ($route['coordinates_data'] as $value) {
							
							$crate_count += $value['coordinates']['crates_info']['crates_count'];
					}
					
					$data[] = array(

						'hub_id' => $hub_id,
						'route_id' => $route_id,
						'vehicle_code' => '',
						'route_data' => json_encode($route),
						'no_of_orders' => $orderCount,
						'no_of_crates' => $crate_count,
						'vehicle_number_generated' => $vehicleInfo,
    					'status' => 1, //routes generated
    				);
    	

				}

				if(count($unassigned_coordinates) == 0){

					$data[] = array(

						'hub_id' => $hub_id,
						'route_id' => $route_id,
						'vehicle_code' => '',
						'route_data' => json_encode(array()),
						'no_of_orders' => 0,
						'no_of_crates' => 0,
						'vehicle_number_generated' => 'unassigned',
    					'status' => 1, //routes generated
    				);

				}else{

					$orderCount = 0;
					$crate_count = 0;
					foreach ($unassigned_coordinates['coordinates_data'] as $value) {
							
							$orderCount += 1;
							$crate_count += $value['coordinates']['crates_info']['crates_count'];
					}
					$data[] = array(

						'hub_id' => $hub_id,
						'route_id' => $route_id,
						'vehicle_code' => '',
						'route_data' => json_encode($unassigned_coordinates),
						'no_of_orders' => $orderCount,
						'no_of_crates' => $crate_count,
						'vehicle_number_generated' => 'unassigned',
    					'status' => 1, //routes generated
    				);
				}

				//var_dump($data);
				DB::table('routing_admin_log')->insert($data);
			}
		}

		return true;
	}

	/**
	 * [setRouteIdAgainstOrderId description]
	 */
	public function setRouteIdAgainstOrderId($route_admin_id){

		$data = $this->getAllRequiredJsonAllRoutesWithUnassigned($route_admin_id);
		if(!$data){
			return false;
		}else{

			foreach ($data['assigned'] as $key => $value) {

				$order_list = array_column($value['coordinates_data'], 'order_id'); 
				
				if(count($order_list) > 0){
					$order_list = implode(',',$order_list);
					$this->updateRouteIdToOrders($order_list,$key);
				}
			}

			if(isset($data['unassigned'])){

				foreach ($data['unassigned'] as $key => $value){
					$unassigned_orders = array_column($value['coordinates_data'],'order_id');
					var_dump($unassigned_orders);
					if(count($unassigned_orders) > 0){
						$order_list = implode(',',$order_list);
						$this->updateRouteIdToOrders($order_list,$key);
					}else{

						// dont know why am i wirting this not at all interested
					}
					
				}
			}
		}

	}

	/**
	 * [updateRouteIdToOrders description]
	 * @return [type] [description]
	 */
	public function updateRouteIdToOrders($order_list,$route_admin_log_id){

		$query = "update gds_order_track set route_admin_log_id=$route_admin_log_id where gds_order_id in($order_list)";
		DB::statement($query);
		return true;
	}


	public function mapUnassignedToRoute($unassign_route_admin_log_id,$order_id,$maptoRoute_id){

		try{

			$query = "INSERT INTO routing_unassigned_log (unassign_route_admin_id,mapped_route_id,order_id) VALUES($unassign_route_admin_log_id, $maptoRoute_id, $order_id) ON DUPLICATE KEY UPDATE    
			unassign_route_admin_id=$unassign_route_admin_log_id,mapped_route_id=$maptoRoute_id";
			DB::statement(DB::raw($query));
			return true;
		}catch(\Exception $e){

			echo $e->getMessage();
			return false;
		}
		
		

	}


    /**
     * [checkRouteExists description]
     * @param  [type] $hashMd5 [description]
     * @return [type]          [description]
     */
    public function checkRouteExists($hashMd5,$hub_id){

    	$query = "select * from routeadmins where veh_key_d = '$hashMd5' and hub_id=$hub_id";
    	$data = DB::select($query);
    	if(count($data) > 0){

    		$data = json_decode(json_encode($data),true);
    		return $data[0]['id'];

    	}else{
    		return false;
    	}

    }

    /**
     * [insertRoutehash description]
     * @param  [type] $hashMd5 [description]
     * @param  [type] $hub_id  [description]
     * @return [insert Id]          [description]
     */
    public function insertRoutehash($hashMd5,$hub_id){

    	$data['veh_key_d'] = $hashMd5;
    	$data['hub_id'] = $hub_id;
    	$data['status'] = 1;
    	return DB::table('routeadmins')->insertGetId($data);
    }

    public function getAllPreStoredRouteData($route_id){

    	$query = 'select * from routing_admin_log where route_id = '.$route_id;
    	$data = DB::select($query);
    	$return_array = [];
    	
    	if(count($data) > 0){
    		$data = json_decode(json_encode($data),true);

    		foreach ($data as $value) {
    			
    			if($value['vehicle_number_generated'] === 'unassigned'){
    			 	$return_array['unassigned_coordinates'] = $value;
    			}else{

    				$return_array[] = $value;	
    			}
    			
    		}

    		// if(!isset($return_array['unassigned_coordinates'])){
    		// 	$return_array['unassigned_coordinates'] = array();
    		// }

    		foreach ($return_array as $key => $value) {
    			
    			$return_array[$key]['route_data'] = json_decode($return_array[$key]['route_data'],true);
    		}

    		array_walk_recursive($return_array, function (&$item, $key) {
   				 $item = null === $item ? '' : $item;
			});
    	}

    	return $return_array;

    }
    public function getAllRequiredJson($vehicle_number,$hub_id){

    	$query = "	select
    				routing_admin_log.id,
    				routing_admin_log.route_data
    				from 
    				routing_admin_log where vehicle_code = '$vehicle_number' 
    				and hub_id = $hub_id
    				and status <> 0
    				order by created_at
    				limit 1
    				";
    	$data = DB::select($query);

    	$return_array = [];
    	
    	if(count($data) > 0){
    		$data = json_decode(json_encode($data),true);
    		return $data[0];
    	}

    	return $return_array;

    }

    /**
     * [setRouteStoreData description]
     * @param [type] $route_admin_log_id [description]
     * @param [type] $data               [description]
     */
    public function setRouteStoreData($route_admin_log_id,$data,$hub_id,$de_name){
    	$data['data'] = json_decode($data['data'],true);
    	$insertData['route_admin_log_id'] = $route_admin_log_id;
    	$insertData['data'] = json_encode($data);
    	$insertData['hub_id'] = $hub_id;
    	$insertData['delivery_executive_name'] = $de_name;
    	$insertData['created_on'] = date('Y-m-d H:i:s');
    	return DB::table('routes_log')->insertGetId($insertData);


    }

    /**
     * [getRouteStoreData description]
     * @param  [type] $vehicle_number [description]
     * @param  [type] $hub_id         [description]
     * @return [type]                 [description]
     */
    public function getRouteStoreData($vehicle_number,$hub_id){

    	$query = " select 
    				routing_admin_log.id
    				from 
    				routing_admin_log where vehicle_code = '$vehicle_number' 
    				and hub_id = $hub_id
    				and status=1
    				order by created_at
    				limit 1
    			";
    	$data = DB::select($query);

    	if(count($data) > 0){
    		$data = json_decode(json_encode($data),true);
    		$route_admin_log_id = $data[0]['id'];

    		$query = "select data from routes_log where route_admin_log_id = $route_admin_log_id limit 1";
    		$data = DB::select($query);
    		if(count($data) > 0){

    			$data = json_decode(json_encode($data),true);
    			return $data[0]['data'];

    		}else{
    			return false;
    		}

    	}else{

    		return false;
    	}
    }

    /**
     * [getAllRequiredJsonAllRoutes description]
     * @param  [type] $hub_id [description]
     * @return [type]         [description]
     */
    public function getAllRequiredJsonAllRoutes($route_admin_id){

    	$query = " 	select 
    				*
    				from 
    				routing_admin_log 
    				where 
    				route_id = $route_admin_id
    			";
    	$data = DB::select($query);
    	if(count($data) > 0){

    		$data = json_decode(json_encode($data),true);
    		$return_array = array();
    		$route_number = 0;
    		foreach ($data as $value) {
 				
 				if($value['vehicle_number_generated'] !== 'unassigned'){
 					$route_number += 1;
    			 	$temp = array();
    			 	
    			 	$temp = json_decode($value['route_data'],true);
    			 	$temp['route_number'] = $route_number;
    			 	$return_array[$value['id']] = $temp;	
    			}
    		}

    		return $return_array;

    	}else{
    		return false;
    	}

    }

     /**
     * [getAllRequiredJsonAllRoutesWithUnassigned with unassigned data]
     * @param  [type] $hub_id [description]
     * @return [type]         [description]
     */
    public function getAllRequiredJsonAllRoutesWithUnassigned($route_admin_id){

    	$query = " 	select 
    				*
    				from 
    				routing_admin_log 
    				where 
    				route_id = $route_admin_id
    			";
    	$data = DB::select($query);
    	if(count($data) > 0){

    		$data = json_decode(json_encode($data),true);
    		$return_array_assigned = array();
    		$return_array_unassigned = array();
    		$route_number = 0;
    		foreach ($data as $value) {
 				
 					if($value['vehicle_number_generated'] !== 'unassigned'){
	    			 	$temp = array();
	    			 	$temp = json_decode($value['route_data'],true);
	    			 	$temp['vehicle_code'] = $value['vehicle_code'];
	    			 	$return_array_assigned[$value['id']] = $temp;	
	    			}else{

	    				$temp = array();
	    			 	$temp = json_decode($value['route_data'],true);
	    			 	$temp['vehicle_code'] = 'unassigned';
	    			 	$return_array_unassigned[$value['id']] = $temp;
	    			}
    			
    		}
       		return array('assigned' => $return_array_assigned,'unassigned' => $return_array_unassigned);
    	}else{
    		return false;
    	}

    }

    /**
     * [clearRouteData description]
     * @param  [type] $hub_id [description]
     * @return [type]         [description]
     */
    public function clearRouteData($hub_id){

    	$query = " select * from routing_admin_log where status=0 and hub_id = $hub_id";
    	$data = DB::select($query);
    	if(count($data)){

    		$data = json_decode(json_encode($data),true);

    		$route_id = $data[0]['route_id'];
    		var_dump($route_id);
    		$newQuery = "delete from routing_admin_log where route_id = $route_id";
    		DB::statement($newQuery);
    		$newQuery = "delete from routeadmins where id = $route_id";
    		DB::statement($newQuery);
    		return true;
    	}else{

    		return false;
    	}
    }

   	/**
   	 * [assignDeliveryExcecutive assign orders to a particular delivery executive]
   	 * @param  [type] $data  [description]
   	 * @param  [type] $de_id [description]
   	 * @return [type]        [description]
   	 */
    public function assignDeliveryExcecutive($de_id,$de_name,$route_id){

    	$query = "select id,route_data,status from routing_admin_log where id = $route_id limit 1";
    	$db_data = DB::select($query);
    	if(count($db_data) > 0){
    		
    		$db_data = json_decode(json_encode($db_data),true);
    		$db_data = $db_data[0];
    		if($db_data['status'] == 1 || $db_data['status'] == 2){

    			$data = json_decode($db_data['route_data'],true);
		    	$ids = array_column($data['coordinates_data'], 'order_id');

		    	//Check for those ordes thats are in unassigned data 
		    	//pick up order ids and put it back to the system
		    	//@prasenjit Chowdhury
		    	$unassignedDataToroute = $this->getdataFromRoutingUnassignedLog($route_id);
		    	if($unassignedDataToroute !== false){
		    		
		    		if(count($unassignedDataToroute) > 0){
		    			$un_ids = array_column($unassignedDataToroute, 'order_id');
		    		}
		    		foreach ($un_ids as $value) {
		    			
		    			array_push($ids, $value);
		    		}

		    	}


		    	if(count($ids) == 0){

		    		return array('status' => false,'message' => 'Route Do not contain any orders');

    			}

    			//check if the status in stock in hub all of it
    			//17025 Stock in hub
    			//17014 Hold
    			//17026 Out for delivery
    			$flag = $this->checkForStatusOnOrderId($db_data['route_data'],'17025,17014,17026');
    			if($flag['status']){
    				$this->updateDeliveryExecutiveToRoute($route_id,$de_id,$de_name);
	    			$this->updateDeliveryExecutiveToOrdersTrack($db_data['route_data'],$de_id);
	    			$this->updateOrdersToOutFordelivery($db_data['route_data']);

	    			return array('status' => true, 'message' => $de_name);
    			}else{

    				if($flag['message'] != ''){
    					return array('status' => false, 'message' => 'Some orders are still not in hub','NotInHub' => $flag['message']);
    				}else{
    					return array('status' => false, 'message' => $flag['message']);
    				}
    				
    			}
    			
    		}else if($db_data['status'] >= 3){

    			return array('status' => false, 'message' => 'Already on trip cannot change DE');
    		}
    	}else{
    		return array('status' => false, 'message' => 'Route Id not found in the db to be updated');
    	}

    }

    /**
     * [checkForStatusOnOrderId description]
     * @param  [type] $route_data [description]
     * @param  [type] $status     [description]
     * @return [true : for all status to a point
     *          false : for any one missing)
     */
    public function checkForStatusOnOrderId($route_data,$status_list){

    	$data = json_decode($route_data,true);
    	$ids = array_column($data['coordinates_data'], 'order_id');

    	if(count($ids) == 0){

    		return array('status' => false,'message' => 'Route Do not contain any orders');

    	}else{
    		$order_list = implode(',',$ids);
	    	$query = "select gds_order_id,order_code from gds_orders where order_status_id not in($status_list) and gds_order_id in($order_list)";
	    	
	    	$q_data = DB::select($query);
	    	if(count($q_data) > 0){

	    		$q_data = json_decode(json_encode($q_data),true);
	    		$message = [];
	    		foreach ($q_data as $value) {
	    			
	    			$temp['order_id'] = $value['gds_order_id'];
	    			$temp['order_code'] = $value['order_code']; 
	    			$message[] = $temp;

	    		}

	    		return array('status' => false,'message' => $message);
	    	}else{
	    		return array('status' => true,'message' => '');
	    	}
    	}
    	

    }

    /**
     * [updateDeliveryExecutiveToRoute We will just update the de_id and name ]
     * @return [type] [description]
     */
    public function updateDeliveryExecutiveToRoute($id,$de_id,$de_name){

    	try{

    		$query = "Update routing_admin_log set delivery_executive=$de_id,delivery_executive_name = '$de_name',status=2 where id = $id";
    		DB::statement($query);
    		return array('status' => true, 'message' => '');
    	}catch(\Exception $e){
    		return array('status' => false, 'message' => $e->getMessage());
    	}  	

    }

    /**
     * [updateDeliveryExecutiveToOrdersTrack description]
     * @param  [type] $data  [description]
     * @param  [type] $de_id [description]
     * @return [type]        [description]
     */
    public function updateDeliveryExecutiveToOrdersTrack($data,$de_id){

    	$data = json_decode($data,true);
    	$ids = array_column($data['coordinates_data'], 'order_id');
    	if(count($ids) > 0){

    		$order_list = implode(',',$ids);
	    	$delivery_date = date('Y-m-d H:i:s');
	    	$query = "Update gds_order_track set delivered_by = $de_id,delivery_date = '$delivery_date' where gds_order_id in($order_list)";
	    	DB::statement($query);
	    	return true;

    	}else{
    		return false;
    	}
    	

    }

    public function setVehicleToRoute($route_id,$vehicle_id,$vehicle_code){

    	$query = "select id,route_data,status from routing_admin_log where id = $route_id limit 1";
    	$db_data = DB::select($query);
    	if(count($db_data) > 0){
    		
    		$db_data = json_decode(json_encode($db_data),true);
    		$db_data = $db_data[0];
    		if($db_data['status'] == 1 || $db_data['status'] == 2){
    			$query = "delete from routes_log where route_admin_log_id=$route_id";
    			DB::statement($query);
    			$route_data = json_decode($db_data['route_data'],true);
    			$route_data['vehicleInfo']['vehicle_number'] = $vehicle_code;
    			$route_data['vehicleInfo']['vehicle_id'] = $vehicle_id;
    			$route_data = json_encode($route_data);
    			$query = "update routing_admin_log set route_data='$route_data',vehicle_code='$vehicle_code',vehicle_id=$vehicle_id,status=2 where id=$route_id";
    			DB::statement($query);    			
    			return array('status' => true, 'message' => $vehicle_code);

    		}else if($db_data['status'] >= 3){

    			return array('status' => false, 'message' => 'Already on trip cannot change vehicle');
    		}
    	}else{
    		return array('status' => false, 'message' => 'Route Id not found in the db to be updated');
    	}


    }

    /**
     * [getDeliveryExecutiveFromHubId description]
     * @param  [type] $hub_id [description]
     * @return [type]         [description]
     */
    public function getDeliveryExecutiveFromHubId($hub_id){

    	//$legal_entities = new Legalentity();
    	//$delivery_executive = $legal_entities->getActiveUsersRouting($hub_id,57);
      $roleRepo = new RoleRepo();
      $delivery_executive = $roleRepo->getUsersByFeatureAndLeWareHouseId($hub_id,'DELR002');
    	return $delivery_executive;
    }

    public function updateOrdersToOutFordelivery($data){

    	$data = json_decode($data,true);
    	$ids = array_column($data['coordinates_data'], 'order_id');
    	if(count($ids) > 0){
    		$order_list = implode(',',$ids);
	    	$query = "Update gds_orders set order_status_id = 17026 where gds_order_id in ($order_list)";
	    	DB::statement($query);
	    	return true;
    	}else{
    		return false;
    	}
    	
    }

    public function getAllRoutesInHUB($hub_id,$date){

		$query = "	select 
					routeadmins.id as route_admin_id,
					group_concat(routing_admin_log.id) as routes_id
					from 
					routeadmins 
					left join routing_admin_log on routing_admin_log.route_id = routeadmins.id
					where routeadmins.hub_id = $hub_id and routing_admin_log.vehicle_number_generated <> 'unassigned'
					and date(routing_admin_log.created_at) = '$date' group by route_admin_id
				";


    	$db_data = DB::select($query);
    	if(count($db_data) > 0){

    		$db_data = json_decode(json_encode($db_data),true);

    		$results = [];
    		foreach ($db_data as $value) {
    			
    			if(!is_null($value['route_admin_id'])){

    				array_push($results,$value);
    			}

    		}

    		if(count($results) > 0){
    			return array('status' => true,'message' => $results);
    		}else{
    			return array('status' => false,'message' => 'no routes to show');
    		}

    	}else{

    		return array('status' => false,'message' => 'pre-existing routes not available' );
    	}
    }
    public function getLatestRoutesInHUB($hub_id,$date){

		$query = "	select 
					routeadmins.id as route_admin_id, 
					group_concat(routing_admin_log.id) as routes_id,
					routing_admin_log.created_at as Date
					from 
					routeadmins 
					left join routing_admin_log on routing_admin_log.route_id = routeadmins.id
					where routeadmins.hub_id = $hub_id and routing_admin_log.vehicle_number_generated <> 'unassigned'
					and date(routing_admin_log.created_at) = '$date' group by route_admin_id  DESC LIMIT 0,1
				";


    	$db_data = DB::select($query);
    	if(count($db_data) > 0){

    		$db_data = json_decode(json_encode($db_data),true);

    		$results = [];
    		foreach ($db_data as $value) {
    			
    			if(!is_null($value['route_admin_id'])){

    				array_push($results,$value);
    			}

    		}

    		if(count($results) > 0){
    			return array('status' => true,'message' => $results);
    		}else{
    			return array('status' => false,'message' => 'no routes to show');
    		}

    	}else{

    		return array('status' => false,'message' => 'pre-existing routes not available' );
    	}
    }

    public function getGeneratedRoutesOnDateRange($hub_id,$from_date,$to_date,$offset_count = 10,$page = 1){

    	$page_no = $page-1;
    	$offset = $offset_count*$page_no;
    	$query = "Select id,createdAt from routeadmins where createdAt between '$from_date' and '$to_date' and hub_id = $hub_id LIMIT $offset,$offset_count";
    	
    	$db_data = DB::select($query);
    	$db_data = json_decode(json_encode($db_data),true);
    	
    	if(count($db_data) > 0){

    		$response_array = [];
    		foreach($db_data as $value) {
    			
    			$returned_data = $this->getRouteDataOnRouteId($value['id'],$hub_id);
    			$returned_data = json_decode(json_encode($returned_data),true);
    			if(count($returned_data) > 0){

    				$return_array = $returned_data;
    				$return_array['route_id'] = $value['id'];
    				$return_array['created_at'] = $value['createdAt'];
		    		$return_array['route_generated'] = true;

    			}else{

    				$return_array['route_id'] = $value['id'];
    				$return_array['order_count'] = 0;
		    		$return_array['crate_count'] = 0;
		    		$return_array['vehicle_count'] = 0;
		    		$return_array['unassigned_count'] = 0;
		    		$return_array['created_at'] = $value['createdAt'];
		    		$return_array['route_generated'] = false;
    			}

    			$response_array[] = $return_array;

    		}
    		return array('status' => true,'message' => $response_array);

    	}else{
    		return array('status' => false,'message' => 'pre-existing routes not available' );
    	}


    }

    public function getRouteDataOnRouteId($route_id,$hub_id){

    	$query = "select * from routing_admin_log where hub_id = $hub_id and route_id = $route_id";
    	$data = DB::select($query);
    	$data = json_decode(json_encode($data),true);
    	if(count($data) > 0){

    		$return_array = array();
    		$return_array['order_count'] = 0;
    		$return_array['crate_count'] = 0;
    		$return_array['vehicle_count'] = 0;
    		$return_array['unassigned_count'] = 0; 
    		foreach ($data as $value) {
    			
    			if($value['vehicle_number_generated'] !== 'unassigned'){
    					$return_array['order_count'] += $value['no_of_orders'];
			    		$return_array['crate_count'] += $value['no_of_crates'];;
			    		$return_array['vehicle_count'] += 1;
    			}else{
    				$return_array['unassigned_count'] += $value['no_of_orders'];
    			}
    			
    		}

    		return $return_array;


    	}else{
    		return array();
    	}

    }

    /**
     * [getRouteDataToPopulateExisting description]
     * @param  [type] $route_id [description]
     * @param  [type] $hub_id   [description]
     * @return [type]           [description]
     */
    public function getRouteDataToPopulateExisting($route_id,$hub_id){


    	$query = "select * from routing_admin_log where hub_id = $hub_id and route_id = $route_id and vehicle_number_generated <> 'unassigned'";
    	$data = DB::select($query);
    	$data = json_decode(json_encode($data),true);
    	if(count($data) > 0){

    		foreach ($data as $key => $value) {

    			//added by prasenjit 12th june to accomadoate unassigned data
    			$unassignedData = $this->getdataFromRoutingUnassignedLog($value['id']);    			
    			$data[$key]['route_data'] = json_decode($value['route_data'],true);
    			if($unassignedData !== false){
    				
    				foreach ($unassignedData as $value) {
    					array_push($data[$key]['route_data']['coordinates_data'],$value);
    					
    				}
    			}
    			
    		}

    		$un_query = "select * from routing_admin_log where hub_id = $hub_id and route_id = $route_id and vehicle_number_generated = 'unassigned'";
    		$un_data = DB::select($un_query);
    		$un_data = json_decode(json_encode($un_data),true);
    		$unassign_route_admin_id = $un_data[0]['id'];
    		$new_unassignedData = $this->getUnassignedRoutingLogWithUnassignedRouteId($unassign_route_admin_id);
    		$data[$key+1] = $new_unassignedData;
    		array_walk_recursive($data, function (&$item, $key) {
   				 $item = null === $item ? '' : $item;
			});
    		return array('status'=>true,'message'=>$data);

    	}else{

    		return array('status'=>false,'message'=>'data does not exist for this route');
    	}

    }


    /**
     * [getRouteDataOnRouteAdminLogId description]
     * @param  [type] $routing_admin_log_id [description]
     * @return [type]                       [description]
     */
    public function getRouteDataOnRouteAdminLogId($route_admin_log_id){

    	$query = "select * from routing_admin_log where id = $route_admin_log_id limit 1";
    	$data = DB::select($query);
    	$data = json_decode(json_encode($data),true);
    	if(count($data) > 0){

	   		return $data[0];

    	}else{

    		return false;
    	}

    }

    public function getPreStoredSortedRouteLogData($route_admin_log_id){

    	$query = "select data, hub_id, delivery_executive_name from routes_log where route_admin_log_id = $route_admin_log_id limit 1";
    	$data = DB::select($query);
    	$data = json_decode(json_encode($data),true);
    	if(count($data) > 0){
	   		return $data[0];
    	}else{
    		return false;
    	}

    }

    /**
     * [setRouteDataOnRouteAdminLogIdOnRouteUpdate description]
     * @param [type] $route_admin_log_id [description]
     * @param [type] $data               [description]
     */
    public function setRouteDataOnRouteAdminLogIdOnRouteUpdate($route_admin_log_id,$data){


		$orderCount = 0;
		$crates_count = 0;
		$consignmentWeight = 0;

    	foreach ($data['coordinates_data'] as $key => $value) {
    		$orderCount += 1;
    		$crates_count += $value['coordinates']['crates_info']['crates_count'];
    		$consignmentWeight += $value['coordinates']['weight'];
    	}

    	$data['vehicleInfo']['orderCount'] = $orderCount;
    	$data['vehicleInfo']['consignmentWeight'] = $consignmentWeight;
		$data = json_encode($data);
		//$data = str_replace("'", "\'", $data);
    	$query = "update routing_admin_log set route_data='$data',no_of_orders=$orderCount,no_of_crates=$crates_count where id = $route_admin_log_id";
    	DB::statement($query);
    	$query = "delete from routes_log where route_admin_log_id=$route_admin_log_id";
    	DB::statement($query);
    	return true;
	}

	/**
     * [setRouteDataOnRouteAdminLogIdOnRouteUpdate description]
     * @param [type] $route_id [description]
     * @param [type] $data               [description]
     */
    public function setRouteDataOnRouteAdminLogIdOnRouteUpdateUnassigned($route_id,$data){

    	$query = "select * from routing_admin_log where route_id=$route_id and vehicle_number_generated='unassigned'";
    	$db_data = DB::select($query);
    	//{"coordinates_data":[]}
    	//
    	$db_data = json_decode(json_encode($db_data[0]),true);
		$orderCount = 0;
		$crates_count = 0;

		$previous_orders = json_decode($db_data['route_data'],true);
		$old_data_count = count($previous_orders['coordinates_data']);

		foreach ($data as $value) {
			
			$previous_orders['coordinates_data'][$old_data_count] = $value;
			$old_data_count++;
		}

		$crates = array();
    	foreach ($previous_orders['coordinates_data'] as $key => $value) {
    		$orderCount += 1;
    		$crates_count += $value['coordinates']['crates_info']['crates_count'];
    		foreach ($value['coordinates']['crates_info']['crates'] as $crate) {
    			array_push($crates,$crate);
    		}
    	}

		$new_orders = json_encode($previous_orders);
		$route_admin_id = $db_data['id'];
    	$query = "update routing_admin_log set route_data='$new_orders',no_of_orders=$orderCount,no_of_crates=$crates_count where id = $route_admin_id";    	
    	DB::statement($query);

    	//thiswill update the routing_admin_crates_logs
    	if(count($crates) > 0){

    		$crates = implode("','",$crates);
    		$query = "update routing_admin_crates_log set route_admin_log_id=null where crate_code in ('$crates')";
    		DB::statement($query);
    	}
    	
    	return true;
	}

	/**
	 * [GetDrivingDistanceGoogle description]
	 * @param [type] $lat1  [description]
	 * @param [type] $lat2  [description]
	 * @param [type] $long1 [description]
	 * @param [type] $long2 [description]
	 */
	public function GetDrivingDistanceGoogle($lat1,$long1,$lat2,$long2)
	{
	    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    $response_a = json_decode($response, true);
	    $dist = $response_a['rows'][0]['elements'][0]['distance']['value']; //mts
	    $time = $response_a['rows'][0]['elements'][0]['duration']['value']; //secs
	    return array('distance' => $dist, 'time' => $time);
	}

	/**
	 * [updateRouteDistanceTimeOnRouteId description]
	 * @param  [type] $route_admin_log_id [description]
	 * @param  [type] $time               [description]
	 * @param  [type] $distance           [description]
	 * @return [type]                     [description]
	 */
	public function updateRouteDistanceTimeOnRouteId($route_admin_log_id,$time,$distance){

		$query = "update routing_admin_log set estimated_distance=$distance,estimated_time=$time where id = $route_admin_log_id";
		DB::statement($query);
    	return true;

	}

	/**
	 * [updateAllCratesToDb description]
	 * @param  [type] $route_id [description]
	 * @return [type]           [description]
	 */
	public function updateAllCratesToDb($route_id){

		$query = "select id,route_data from routing_admin_log where route_id=$route_id and vehicle_number_generated <> 'unassigned'";
		$data = DB::select($query);
    	$data = json_decode(json_encode($data),true);
    	
    	if(count($data)>0){

    		foreach ($data as $value) {
    			
    			$route_admin_log_id = $value['id'];
    			$route_data = json_decode($value['route_data'],true);

    			foreach ($route_data['coordinates_data'] as $value) {
    				//var_dump($value);exit;
    				$order_id = $value['coordinates']['gds_order_id'];
    				$order_code = $value['coordinates']['order_code'];
    				$crates = $value['coordinates']['crates_info']['crates'];
    				
	   				foreach ($crates as $value) {
    					$query = "insert INTO routing_admin_crates_log (crate_code, order_id, route_admin_log_id,order_code) VALUES ('$value',$order_id,$route_admin_log_id,'$order_code')
							ON DUPLICATE KEY UPDATE order_id=$order_id,route_admin_log_id=$route_admin_log_id,order_code='$order_code'";
						DB::statement(DB::raw($query));
    				}
    			}

    		}

    	}else{
    		return false;
    	}
	}
	
	/**
	 * [getOrdersOnCratesCount description]
	 * @param  [type] $crate_code [description]
	 * @return [type]             [description]
	 */
	public function getOrdersOnCratesCount($crate_code){

		$query = "	
			select 
			routing_admin_crates_log.order_id,
			routing_admin_crates_log.order_code,
			routing_admin_crates_log.crate_code,
			routing_admin_log.vehicle_number_generated as route_number,
			routing_admin_log.route_id as route_admin_id,
			routing_admin_log.id as route_id,
			if(routing_admin_log.vehicle_code = '', routing_admin_log.vehicle_number_generated,routing_admin_log.vehicle_code) as vehicle_code
			from 
			routing_admin_crates_log
			left join routing_admin_log on routing_admin_log.id=routing_admin_crates_log.route_admin_log_id 
			where order_id =(select order_id from routing_admin_crates_log where crate_code = '$crate_code')";

		$data = DB::select($query);
    	$data = json_decode(json_encode($data),true);
    	return $data;

	}

	/**
	 * [changeCrateInfoOnCrateLog description]
	 * @param  [type] $data     [description]
	 * @param  [type] $route_id [description]
	 * @return [type]           [description]
	 */
	public function changeCrateInfoOnCrateLog($data,$route_id){

		if(count($data['coordinates']['crates_info']['crates']) > 0){

			$crates = $data['coordinates']['crates_info']['crates'];
			$crates = "'".implode("','",$crates)."'";
			$query = "update routing_admin_crates_log set route_admin_log_id=$route_id where crate_code in ($crates)";
			$data = DB::statement($query);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * [getDeliveryExecutiveOnRoutingAdminLogId description]
	 * @param  [type] $routing_admin_log_id [description]
	 * @return [type]                       [description]
	 */
	public function getDEAndDistanceRouteAdminId($routing_admin_log_id){

		$query = "select IFNULL(delivery_executive,0) as de_id,
						 IFNULL(delivery_executive_name,'') as de_name,
						 estimated_distance,
						 estimated_time 
						 from routing_admin_log where id = $routing_admin_log_id";
		$data = DB::select($query);
    	$data = json_decode(json_encode($data),true);
    	return $data[0];
	}

	public function getdataFromRoutingUnassignedLog($route_admin_log_id){

		$query = "select unassign_route_admin_id,order_id from routing_unassigned_log where mapped_route_id=$route_admin_log_id";
		$data = DB::select($query);
    	$data = json_decode(json_encode($data),true);
    	if(count($data) == 0){
    		return false;
    	}else{

    		$unassigned_log_id = $data[0]['unassign_route_admin_id'];
    		//echo $unassigned_log_id;exit;
    		$unassignedData = $this->getRouteDataOnRouteAdminLogId($unassigned_log_id);

    		if(!$unassignedData){
    			return false;
    		}else{

    			$unassignedData = json_decode($unassignedData['route_data'],true);

    			if(!$unassignedData['coordinates_data']){
    				return false;
    			}else{

    				//var_dump($unassignedData);
    				$unassignedDataOrderIds = array_column($unassignedData['coordinates_data'],'order_id');    				
    				$unassignedRouteData = $unassignedData['coordinates_data'];
    				$array_combined = array_combine($unassignedDataOrderIds,$unassignedRouteData);

    				$addunassigned = array();
    				foreach ($data as $value){
		    			
    					if (array_key_exists($value['order_id'], $array_combined)) {
    							array_push($addunassigned,$array_combined[$value['order_id']]);
						}
						
						//var_dump($value);
		    		}

		    		if(count($addunassigned) > 0){
		    			return $addunassigned;
		    		}else{
		    			return false;
		    		}
    				
    			}
    			
    		}

    		
    	}

	}

	/**
	 * [getUnassignedRoutingLogWithUnassignedRouteId Takes the unassiged data and remove the data from the query]
	 * @param  [type] $unassign_route_admin_id [description]
	 * @return [type]                          [description]
	 */
	public function getUnassignedRoutingLogWithUnassignedRouteId($unassign_route_admin_id){

		$unassignedData = $this->getRouteDataOnRouteAdminLogId($unassign_route_admin_id);
		$unassignedData['route_data'] = json_decode($unassignedData['route_data'],true);

		if(count($unassignedData['route_data']['coordinates_data']) > 0){

			$query = "select order_id from routing_unassigned_log where unassign_route_admin_id = $unassign_route_admin_id";
			$data = DB::select($query);
	    	$data = json_decode(json_encode($data),true);
	    	if(count($data) == 0){
	    		return $unassignedData;
	    	}else{

	    		$removeAddOrderId = array();
				$unassignedRouteData = $unassignedData['route_data']['coordinates_data'];
				
				foreach ($data as $value){
	    			
	    			array_push($removeAddOrderId,$value['order_id']);
					
	    		}

	    		foreach ($unassignedRouteData as $key => $value) {
	    			
	    			if(in_array($value['coordinates']['gds_order_id'],$removeAddOrderId)){

	    				unset($unassignedRouteData[$key]);
	    			}
	    		}

	    		$temparray = array_values($unassignedRouteData);
	    		$unassignedData['route_data']['coordinates_data'] = $temparray;
	    		return $unassignedData;
	    	}


		}else{

			return $unassignedData;
		}

	}

	/**
	**/
	public function getHUBCoordinatesFromRouteAdminId($route_admin_id){

		$query = "select hub_id from routeadmins where id = $route_admin_id";
		$data = DB::select($query);
	    $data = json_decode(json_encode($data),true);
	    if(count($data) == 0){
	    	return array();
		}else{

			$hub_id = $data[0]['hub_id'];
			$hub_coordinates = $this->getHUBCoordinates($hub_id);
			return $hub_coordinates;
		}
	}

	public function bagAndCfcCountByOrderId($order_id)
	{
		$query = "SELECT bags_cnt AS bag_count, cfc_cnt AS cfc_count
					FROM gds_order_track got
					WHERE got.gds_order_id = $order_id";
		$data = DB::select($query);
	    $data = json_decode(json_encode($data),true);
	    if(count($data) == 0){
	    	return array(
	    			'bag_count' => 0,
	    			'cfc_count' => 0
	    		);
		}else{

			return $data;
		}
	}

}

?>