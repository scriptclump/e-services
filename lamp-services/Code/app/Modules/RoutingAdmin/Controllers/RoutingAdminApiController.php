<?php 
namespace App\Modules\RoutingAdmin\Controllers;

use App\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;


/*
	Model Classes
 */
use App\Modules\RoutingAdmin\Models\RouteDataModel;
use App\Modules\RoutingAdmin\Models\OrderMapDashboard;

class RoutingAdminApiController extends BaseController{

	protected $routeDataModel;
	protected $orderMapDashboard;


	function __construct()
	{
		$this->routeDataModel = new RouteDataModel();
		$this->orderMapDashboard = new OrderMapDashboard();
	}

	function getOrdersInfoOncrate(){

		$data = Input::get();
		if(isset($data['crate_code'])){

			$data = $this->routeDataModel->getOrdersOnCratesCount($data['crate_code']);

			if(count($data) == 0){
				return Response::json(array('status'=>false,'message'=>'data not found'));
			}else{
				return Response::json(array('status'=>true,'message'=>$data));
			}		

		}else{

			return Response::json(array('status'=>false,'message'=>'data not found'));
		}
	}

	function getAllVehiclesByHubId(){
		$data = Input::get();
		if (isset($data['hub_id'])) {
			$hub_id = $data['hub_id'];
		}else{
			return Response::json(array('status'=>false,'message'=>'Hub id is missing'));
		}
		
		$vehicle_list = $this->routeDataModel->getvehicleDataEachHub($hub_id,80);
		if(count($vehicle_list) > 0){
			return Response::json(array('status'=>true,'message'=> $vehicle_list));
		}else{
			return Response::json(array('status'=>false,'message'=>'No vechiels avialable for '.$hub_id));
		}
	}

	/**
	 * @return [type] [description]
	 */
	function getPositionCrate(){

		$data = Input::get();
		if(!isset($data['crate_code'])){

			return Response::json(array('status'=>false,'message'=>'Crate code not found in routing'));
		}

		$crate_code = $data['crate_code'];
		$crate_data = $this->routeDataModel->getOrdersOnCratesCount($crate_code);

		if(count($crate_data) == 0){
			return Response::json(array('status'=>false,'message'=>'Crate not assigined to any route'));
		}else{

			
			if(count($crate_data > 0)){

				$maincrate = array();
				$innerOrderSequence = 0;
				foreach ($crate_data as $key => $value) {
					
					if($value['crate_code'] == $crate_code){

						$maincrate = $crate_data[$key];
						$innerOrderSequence = $key;
						break;
					}

				}

				$route_admin_id = $maincrate['route_id'];
				if(is_null($route_admin_id)){
					return Response::json(array('status'=>false,'message'=>'Crate not assigined to any route probably in unassigned route'));
				}
				$route_store = $this->routeDataModel->getPreStoredSortedRouteLogData($route_admin_id);
		        if(!$route_store){

		        	$data = $this->routeDataModel->getRouteDataOnRouteAdminLogId($route_admin_id);
		        	if(!$data){
		        		return Response::json(array('status' => false,'message' => 'required JsonObjects  are Missing for this route')); 
		        	}else{

		        		if(!is_null($data['route_data'])){

		        			$insertId = $data['id'];
			            	$jsonrequest = json_decode($data['route_data'],true);
				            $jsonrequest['hub_coordinates'] = $this->routeDataModel->getHUBCoordinates($data['hub_id']);

				           	$jsonrequest = json_encode($jsonrequest);

				            $shortestPath = $this->routeDataModel->curlRequest($this->routingUrl.'arrangeShortestPath',$this->routingPort,$jsonrequest);
				            if($shortestPath['status']){

				            	$this->routeDataModel->setRouteStoreData($insertId,$shortestPath,$data['hub_id']);
				            }

		        		}else{

		        			return Response::json(array('status' => false,'message' => 'required JsonObjects  are Missing for this route'));
		        		}
		        	}
		    	}else{

		    		$shortestPath = json_decode($route_store['data'],true);
		        	$temp['status'] = $shortestPath['status'];
		        	$temp['data'] = json_encode($shortestPath['data']);
		        	$shortestPath = null;
		        	$shortestPath = $temp;
		    	}

		    	$json_array = json_decode($shortestPath['data'],true);

		    	if(count($json_array['coordinates_data']) > 0){

		    		$order_data = $json_array['coordinates_data'];
		    		$crate_data = [];
		    		$crate_order = [];
		    		foreach ($order_data as $key => $value) {

		    			if(count($value['coordinates']['crates_info']['crates']) > 0){

		    				foreach ($value['coordinates']['crates_info']['crates'] as $crate) {
		    					array_push($crate_data,$crate);
		    					$crate_order[$crate] =$value['coordinates']['order_code'];
		    				}
		    			}
		    			
		    			
		    		}

		    		$cycle = 7;
		    		$appart_start = 1;
		    		$fill_cycle = 0;
		    		$crate_holder = array();
		    		foreach ($crate_data as $value) {
		    			
		    			if($fill_cycle  >= $cycle){
		    				$appart_start += 1;
		    				$fill_cycle = 0;
		    			}

		    			$fill_cycle +=1; 
		    			$crate_holder[$value] = $appart_start.'0'.$fill_cycle; 

		    		}

		    		return Response::json(array('status' => true,'message' => array('order_code' => $crate_order[$crate_code],'position' => $crate_holder[$crate_code] ,'crate_code' => $crate_code)));		    		

		    	}else{

		    		return Response::json(array('status' => false,'message' => 'Data not found in the sysytem routes'));
		    	}


			}else{
				return Response::json(array('status'=>false,'message'=>'Crate not assigined to any route'));
			}
			

		}



	}

}