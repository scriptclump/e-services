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

class RoutingAdminController extends BaseController{

	protected $dcList;
	protected $hublist;
	protected $routeDataModel;
	protected $routingUrl,$routingPort;

	public function __construct() {

		$this->routingUrl = env('ROUTING_ADMIN_URL');
		$this->routingPort = env('ROUTING_ADMIN_PORT');
    }

    public function admin(){

    	$this->routeDataModel = new RouteDataModel();
    	$dc = $this->routeDataModel->getAllDC();
        $hub = $this->routeDataModel->getAllHUB();
        //$vehicles = $this->routeDataModel-> 
        $return = array();
        $return['dc'] = $dc;
        $return['hub'] = $hub;
        $vehicles = array();
        foreach ($hub as $key => $value) {
        	
        	foreach ($hub[$key] as $k => $value) {
        		$vehicles[$k] = $this->routeDataModel->getvehicleDataEachHub($k,80);
        	}
        }
        $return['vehicles'] = $vehicles;
        //return Response::json(array('Status' => true, 'Message' => $return));

    }

    public function index(){
        $this->routeDataModel = new RouteDataModel();
        $dc = $this->routeDataModel->getAllDC();
        $hub = $this->routeDataModel->getAllHUB();
        //$vehicles = $this->routeDataModel-> 
        $return = array();
        $return['dc'] = $dc;
        $return['hub'] = $hub;
        $vehicles = array();
        foreach ($hub as $key => $value) {
            
            foreach ($hub[$key] as $k => $value) {
                $vehicles[$k] = $this->routeDataModel->getvehicleDataEachHub($k,80);
            }
        }

        $return['vehicles'] = $vehicles;
        $status['0'] =  'na';
		$status['1'] =  'distributed';
		$status['2'] =  'assigned';
		$status['3'] =  'enroute';
		$status['4'] =  'complited';
		$status['5'] =  'idle';
    	return View::make('RoutingAdmin::index')
    						->with('data',$return)
    						->with('status',$status);
    }

    public function getOrdersByHub(){

    	$data = Input::get();
    	if(!isset($data['hub_id'])){

    		return Response::json(array('status' => false,'message' => 'Hub id missing'));
    	}
    	$hub_id = $data['hub_id'];
    	$this->routeDataModel = new RouteDataModel();
    	$orders = $this->routeDataModel->getOrderListByHubId($hub_id);
    	
    	if(!$orders){
    		return Response::json(array('status' => false,'message' => 'Orders having zero coordinates'));
    	}

    	if(count($orders) <= 0){
    		return Response::json(array('status' => false,'message' => 'No orders found'));
    	}else{
    		return Response::json(array('status' => true,'message' => $orders));
    	}
    	

    }


    /**
     * [generateRoutes description]
     * @return [type] [description]
     */
    public function generateRoutes(){
    	
    	$data = Input::get();
    	if(!isset($data['hub_id'])){

    		return Response::json(array('status' => false,'message' => 'hub_id missing'));
    	}

    	if(!isset($data['order_data'])){

    		return Response::json(array('status' => false,'message' => 'order_data Missing'));

    	}else{

    		$order_data = $data['order_data'];
    	}

    	if(!isset($data['vehicles_details'])){

	   		return Response::json(array('status' => false,'message' => 'vehicles_details not provided'));

    	}else{

    		$vehicles_details = $data['vehicles_details'];
    	}


    	$this->routeDataModel = new RouteDataModel();
    	$hub_lat = $this->routeDataModel->getHUBCoordinates($data['hub_id']);
    		
    	//calculate on average now
    	$estimation_variables = $this->calculateVariableData($order_data,$vehicles_details,1);


    	if(count($hub_lat) <= 0){

    		return Response::json(array('status' => false,'message' => 'HUB coordinates Missing'));	
    	}

    	/**
    	 * [$esu_time description]
    	 *  We will be adding this to avoid future count data logic on esu
    	*/
    	$esu_time = isset($data['esu_time'])?$data['esu_time']:3;
    	foreach ($vehicles_details as $key => $value) {
    		
    		$vehicles_details[$key]['esu_time'] = $esu_time;
    	}
    	
    	$request = array();
    	$request['hubcoordinates'] = $hub_lat;
    	$request['orderscoordinates'] = $order_data;
    	$request['vehicles_details'] = $vehicles_details;
    	$request['order_count'] = isset($data['order_count'])?$data['order_count']:30;

    	/*****
    		Check the route distribution exist beforehand
    	******/
		$postfields = json_encode($request);
		$postfields_hash = md5($postfields);

		$ret_check = $this->routeDataModel->checkRouteExists($postfields_hash,$data['hub_id']);

		$ret_check = false;
		if(!$ret_check){

			$resData = [];
			$splitloadOnLocation = $this->routeDataModel->curlRequest($this->routingUrl.'splitloadOnLocationOnOrderCountBaseBeat',$this->routingPort,$postfields);
			if($splitloadOnLocation['status']){
				
				$route_id = $this->routeDataModel->insertRoutehash($postfields_hash,$data['hub_id']);
				$splitloadOnLocation = json_decode($splitloadOnLocation['data'],true);
				if($splitloadOnLocation){
					//$resData['hub_coordinates'] = $hub_lat;
					$this->routeDataModel->storeRoutesToDB($splitloadOnLocation,$route_id,$data['hub_id']);
					$splitloadOnLocation_data = $this->routeDataModel->getAllPreStoredRouteData($route_id);
					$updateAllCratesToDb = $this->routeDataModel->updateAllCratesToDb($route_id);
					//$data = $this->routeDataModel->setRouteIdAgainstOrderId($route_id);
					$resData['hub_coordinates'] = $hub_lat;
					$unassigned_coordinates = $splitloadOnLocation_data['unassigned_coordinates'];
					unset($splitloadOnLocation_data['unassigned_coordinates']);
					$resData['assigned_coordinates'] = $splitloadOnLocation_data;
					$resData['unassigned_coordinates'] = $unassigned_coordinates;
					
					//adding estimates
					if($estimation_variables){
						$resData['estimates'] = $estimation_variables;
					}else{
						$resData['estimates'] = array();
					}
					return  Response::json(array('status' => true,'message' => $resData));
				}else{

					return Response::json(array('status' => false,'message' => 'Load on location filed at google end check again'));
				}
			}else{

				return Response::json(array('status' => false,'message' => 'Load distribution could not take place try again later'));
			}

		}else{

			$route_id = $ret_check;
			$splitloadOnLocation_data = $this->routeDataModel->getAllPreStoredRouteData($route_id);
			$resData['hub_coordinates'] = $hub_lat;
			$unassigned_coordinates = $splitloadOnLocation_data['unassigned_coordinates'];
			unset($splitloadOnLocation_data['unassigned_coordinates']);
			$resData['assigned_coordinates'] = $splitloadOnLocation_data;
			$resData['unassigned_coordinates'] = $unassigned_coordinates;

			//adding estimates
			if($estimation_variables){
				$resData['estimates'] = $estimation_variables;
			}else{
				$resData['estimates'] = array();
			}

			return  Response::json(array('status' => true,'message' => $resData));
		}

		
    }

    
    /**
     * [calculateVariableData  : to caclculate the estimates of the 
     * 	values]
     * @param  [type] $order_data       [description]
     * @param  [type] $vehicles_details [description]
     * @param  [type] available values 
     *
     * 							0 : Same type vehicle
     *                          1 : Average             
     */
    public function calculateVariableData($order_data,$vehicles_details,$type,$options =  array()){

    	//var_dump($order_data);
    	//var_dump($vehicles_details);

    	switch($type){

    		case 1:{

    			//Sum of the vehicle max capacity
    			$total_load_capacity = 0;

    			//Sum of the total load in the consignment
    			$total_consignment_load = 0;

    			if(is_array($vehicles_details)){

    				foreach ($vehicles_details as $key => $value) {
    				
    					$total_load_capacity += $value['vehicle_max_load'];

    				}

    				if($total_load_capacity == 0){
    					return false;
    				}else{

    					$average_load_capacity = $total_load_capacity/count($vehicles_details);
    				}

    			}else{
    				return false;
    			}

    			if(is_array($order_data)){

    				foreach($order_data as $order){

    					$total_load_capacity += $order['weight'];	
    				}
    			}

    			$estimates = array();
    			$estimates['min_required_vehicles'] = floor($total_load_capacity/$average_load_capacity);
    		 	$estimates['max_required_vehicles'] = ceil($total_load_capacity/$average_load_capacity);
    			$estimates['required_vehicle'] = round($total_load_capacity/$average_load_capacity);

    		}break;
    	}

    	return $estimates;
    }

    /**
     * [generateLoadSheet description]
     * @return [type] [description]
     */
    public function generateShortestPath(){

    	$data = Input::get();
        $data = json_encode($data);
		$this->routeDataModel = new RouteDataModel();
		$shortestPath = $this->routeDataModel->curlRequest($this->routingUrl.'arrangeShortestPath',$this->routingPort,$data);
		
		if($shortestPath['status']){		
			$shortestPath = json_decode($shortestPath['data'],true);
			
			if($shortestPath){
					return  Response::json(array('status' => true,'message' => $shortestPath));
			}else{

					return Response::json(array('status' => false,'message' => 'Load on location filed at google end check again'));
			}
		}else{

			return Response::json(array('status' => false,'message' => 'Load distribution could not take place try again later'));
		}
		

    }

    public function generateLoadSheet(){
        $data = input::get();
        if(!isset($data['vehicle_number'])){

            return Response::json(array('status' => false,'message' => 'vehicle_number missing'));
        }
        if(!isset($data['hub_id'])){

            return Response::json(array('status' => false,'message' => 'Hub_coordinates are missing'));
        }
        $this->routeDataModel = new RouteDataModel();

        //get prestored routes for the vehicles
        $route_store = $this->routeDataModel->getRouteStoreData($data['vehicle_number'],$data['hub_id']);

        if(!$route_store){

        	$jsonrequest = $this->routeDataModel->getAllRequiredJson($data['vehicle_number'],$data['hub_id']);

	        if(count($jsonrequest) <= 0){

	            return Response::json(array('status' => false,'message' => 'required JsonObjects  are Missing')); 
	        }else{
	        	
	            //return Redirect::away('/routingadmin/loadSheet');
	            
	            $insertId = $jsonrequest['id'];
            	$jsonrequest = json_decode($jsonrequest['route_data'],true);
	            $jsonrequest['hub_coordinates'] = $this->routeDataModel->getHUBCoordinates($data['hub_id']);

	           	$jsonrequest = json_encode($jsonrequest);

	            $shortestPath = $this->routeDataModel->curlRequest($this->routingUrl.'arrangeShortestPath',$this->routingPort,$jsonrequest);
	            if($shortestPath['status']){

	            	$this->routeDataModel->setRouteStoreData($insertId,$shortestPath);
	            }
	        }

        }else{

        	$shortestPath = json_decode($route_store,true);
        	$temp['status'] = $shortestPath['status'];
        	$temp['data'] = json_encode($shortestPath['data']);
        	$shortestPath = null;
        	$shortestPath = $temp;
        }

        if($shortestPath['status']){
			

			$shortestPath = json_decode($shortestPath['data'],true);
			if($shortestPath){

				//store this to the table
				//return  Response::json(array('status' => true,'message' => $shortestPath));
				return View::make("RoutingAdmin::loadSheet")->with('json',$shortestPath);
			}else{

				return Response::json(array('status' => false,'message' => 'Load on location filed at google end check again'));
			}
		}else{

			return Response::json(array('status' => false,'message' => 'Load distribution could not take place try again later'));
		}

        
    }


    public function generateViewMap(){
        $data = input::get();
        if(!isset($data['vehicle_number'])){

            return Response::json(array('status' => false,'message' => 'vehicle_number missing'));
        }
        if(!isset($data['hub_id'])){

            return Response::json(array('status' => false,'message' => 'Hub_coordinates are missing'));
        }
        $this->routeDataModel = new RouteDataModel();
        $tempHub = $this->routeDataModel->getHUBCoordinates($data['hub_id']);
        //get prestored routes for the vehicles
        $route_store = $this->routeDataModel->getRouteStoreData($data['vehicle_number'],$data['hub_id']);

        if(!$route_store){

        	$jsonrequest = $this->routeDataModel->getAllRequiredJson($data['vehicle_number'],$data['hub_id']);

	        if(count($jsonrequest) <= 0){

	            return Response::json(array('status' => false,'message' => 'required JsonObjects  are Missing')); 
	        }else{
	        	
	            //return Redirect::away('/routingadmin/loadSheet');
	            
	            $insertId = $jsonrequest['id'];
            	$jsonrequest = json_decode($jsonrequest['route_data'],true);
	            
	            $jsonrequest['hub_coordinates'] = $tempHub;
	           	$jsonrequest = json_encode($jsonrequest);

	            $shortestPath = $this->routeDataModel->curlRequest($this->routingUrl.'arrangeShortestPath',$this->routingPort,$jsonrequest);
	            if($shortestPath['status']){

	            	$this->routeDataModel->setRouteStoreData($insertId,$shortestPath);
	            }
	        }

        }else{

        	$shortestPath = json_decode($route_store,true);
        	$temp['status'] = $shortestPath['status'];
        	$temp['data'] = json_encode($shortestPath['data']);
        	$shortestPath = null;
        	$shortestPath = $temp;
        }

        if($shortestPath['status']){
			

			$shortestPath = json_decode($shortestPath['data'],true);
			if($shortestPath){
				$shortestPath['hub_coordinates'] = $tempHub;
				$shortestPath = json_encode($shortestPath, true);
				//var_dump($shortestPath);
				//store this to the table
				//return  Response::json(array('status' => true,'message' => $shortestPath));
				return View::make("RoutingAdmin::viewMap")->with('json',$shortestPath);
			}else{

				return Response::json(array('status' => false,'message' => 'Load on location filed at google end check again'));
			}
		}else{

			return Response::json(array('status' => false,'message' => 'Load distribution could not take place try again later'));
		}

        
    }


    public function generateViewMapAll(){

    	$data = input::get();
        if(!isset($data['route_admin_id'])){

            return Response::json(array('status' => false,'message' => 'route_admin_id missing'));
        }
        //start validating all data
        $data['route_admin_id'] = filter_var($data['route_admin_id'],FILTER_SANITIZE_NUMBER_INT);

      	if($data['route_admin_id'] === '' || $data['route_admin_id'] == 0){
        	return Response::json(array('status' => false,'message' => 'send a valid all route Id'));
        }

        

        $this->routeDataModel = new RouteDataModel();
        $jsonrequest = $this->routeDataModel->getAllRequiredJsonAllRoutes($data['route_admin_id']);

        $route_data_array = array();
        $route_data_array = $jsonrequest;
        $route_data_array['hub_coordinates'] = $this->routeDataModel->getHUBCoordinatesFromRouteAdminId($data['route_admin_id']);
        if(!$jsonrequest){
        	return Response::json(array('status' => false,'message' => 'Pre existing maps not found !!'));	
        }else{
        	
        	//echo json_encode($route_data_array);
        	return View::make("RoutingAdmin::viewMapAll")->with('json',json_encode($route_data_array));
        }

    }

    public function generateViewMapsex(){
    	$data = input::get();
        if(!isset($data['vehicle_number'])){

            return Response::json(array('status' => false,'message' => 'vehicle_number missing'));
        }
        if(!isset($data['hub_id'])){

            return Response::json(array('status' => false,'message' => 'Hub_coordinates are missing'));
        }
        $this->routeDataModel = new RouteDataModel();
        $jsonrequest = $this->routeDataModel->getAllRequiredJson($data['vehicle_number'],$data['hub_id']);

        if(count($jsonrequest) <= 0){

            return Response::json(array('status' => false,'message' => 'required JsonObjects  are Missing')); 
        }else{
        	
            //return Redirect::away('/routingadmin/loadSheet');
            $insertId = $jsonrequest['id'];
            $jsonrequest = json_decode($jsonrequest['route_data'],true);
            $jsonrequest['hub_coordinates'] = $this->routeDataModel->getHUBCoordinates($data['hub_id']);
            $tempPush = $jsonrequest['hub_coordinates'];
           	$jsonrequest = json_encode($jsonrequest);
            $shortestPath = $this->routeDataModel->curlRequest($this->routingUrl.'arrangeShortestPath',$this->routingPort,$jsonrequest);
		
			if($shortestPath['status']){		
				$shortestPath = json_decode($shortestPath['data'],true);
				
				$shortestPath['hub_coordinates'] = $tempPush;
				if($shortestPath){
					//return  Response::json(array('status' => true,'message' => $shortestPath));
					//return View::make("RoutingAdmin::loadSheet")->with('json',$shortestPath);
					$shortestPath = json_encode($shortestPath, true);
					return View::make("RoutingAdmin::viewMap")->with('json',$shortestPath);
				}else{

					return Response::json(array('status' => false,'message' => 'Load on location filed at google end check again'));
				}
			}else{

				return Response::json(array('status' => false,'message' => 'Load distribution could not take place try again later'));
			}
        }
    }

    /**
    */
    public function getOrdersHubSocket(){
        return View::make("RoutingAdmin::getAllOrders");
        
    }
    public function getAllOrdersByDcHub($dc,$hub){
        echo $dc;
        echo $hub;

    }



    public function clearRoutes(){

    	$data = input::get();
    	$this->routeDataModel = new RouteDataModel();
    	if(!isset($data['hub_id'])){

            return Response::json(array('status' => false,'message' => 'Hub Id missing check !!!'));
        }

        $clearData = $this->routeDataModel->clearRouteData($data['hub_id']);

        if(!$clearData){
        	return Response::json(array('status' => false,'message' => 'Trips are on else the hub is not present'));
        }else{
        	return Response::json(array('status' => true,'message' => 'Routes cleared'));	
        }
    }

    public function assignDeliveryExcecutive(){

    	$data = input::get();
    	$this->routeDataModel = new RouteDataModel();
    	$message = $this->routeDataModel->assignDeliveryExcecutive(json_encode($data['data']),$data['de_id'],$data['de_name'],$data['hub_id'],$data['vehicle_number']);
    	//$message = array('status' => false, 'message' => 'The data set not found in db(testing)');
    	return Response::json($message);
    }

    /**
     * [getDeliveryExecutiveList sent to the front screen]
     * @return [type] [json ]
     */
    public function getDeliveryExecutiveList(){

    	$data = input::get();
    	if(!isset($data['hub_id'])){
    		return Response::json(array('status' => false,'message' => 'Hub id not sent'));
    	}else{

    		$this->routeDataModel = new RouteDataModel();
    		$de_list = $this->routeDataModel->getDeliveryExecutiveFromHubId($data['hub_id']);
    		if(count($de_list) > 0){

    			$delist_array = array();
    			foreach ($de_list as $value) {
    				
    				$delist_array[$value['user_id']] = $value['name'];

    			}

    			return Response::json(array('status' => true,'message' => $delist_array));
    		}else{
    			return Response::json(array('status' => false,'message' => 'De Executive not present for the hub'));
    		}
    	}
    }


    /**
     * [getHistoricalRoutes description]
     * @return [type] [description]
     */
    public function getHistoricalRoutes(){

    	$data = input::get();
    	if(!isset($data['hub_id'])){
    		return Response::json(array('status' => false,'message' => 'Hub id not sent'));
    	}

    	if(!isset($data['from_date'])){
    		
    		$data['from_date'] = date('Y-m-d',strtotime("+1 month", $time));
    		$data['from_date'] = $data['from_date'].' 00:00:00';
    	}else{

    		$data['from_date'] = $data['from_date'].' 00:00:00';
    	}

    	if(!isset($data['to_date'])){
    		
    		$data['to_date'] = date('Y-m-d');
    		$data['to_date'] = $data['to_date'].' 23:59:59';

    	}else{
    		$data['to_date'] = $data['to_date'].' 23:59:59';
    	}

    	if(!$data['offset_count']){
    		$data['offset_count'] = 10;
    	}

    	if(!$data['page_no']){
    		$data['page_no'] = 1;
    	}

    	if($data['from_date'] > $data['to_date']){
    		return Response::json(array('status' => false,'message' => 'To date is more than from date'));
    	}

		$this->routeDataModel = new RouteDataModel();
    	$routeData = $this->routeDataModel->getGeneratedRoutesOnDateRange($data['hub_id'],$data['from_date'],$data['to_date'],$data['offset_count'],$data['page_no']);

    	return Response::json($routeData);

    }

    /**
     * [getRoutesInfoFromRouteId description]
     * @return [type] [description]
     */
    public function getRoutesInfoFromRouteId(){

    	$data = input::get();
    	if(!isset($data['hub_id'])){
    		return Response::json(array('status' => false,'message' => 'Hub id not sent'));
    	}

    	if(!isset($data['route_id'])){
    		return Response::json(array('status' => false,'message' => 'Route id not sent'));
    	}
    	$this->routeDataModel = new RouteDataModel();
    	$data = $this->routeDataModel->getRouteDataToPopulateExisting($data['route_id'],$data['hub_id']);
    	return Response::json($data);

    }

    /**
     * [generateNewRoutes description]
     * @return [type] [description]
     */
    public function generateNewRoutes(){
        $this->routeDataModel = new RouteDataModel();
        $dc = $this->routeDataModel->getAllDC();
        $hub = $this->routeDataModel->getAllHUB();
        //$vehicles = $this->routeDataModel-> 
        $return = array();
        $return['dc'] = $dc;
        $return['hub'] = $hub;
        $vehicles = array();
        //print_r($hub);exit;
        foreach ($hub as $key => $val) {
            foreach ($val as $k => $value) {
                $vehicles[$k] = $this->routeDataModel->getvehicleDataEachHub($k,80);
            }
        }
        $return['vehicles'] = $vehicles;
        $status['0'] =  'na';
        $status['1'] =  'distributed';
        $status['2'] =  'assigned';
        $status['3'] =  'enroute';
        $status['4'] =  'complited';
        $status['5'] =  'idle';
        return View::make('RoutingAdmin::routeAdmin')
                            ->with('data',$return)
                            ->with('status',$status);

    }
    
    /**
     * [viewRouteHistory description]
     * @return [type] [description]
     */
    public function viewRouteHistory(){
        
        $this->routeDataModel = new RouteDataModel();
        $dc = $this->routeDataModel->getAllDC();
        $hub = $this->routeDataModel->getAllHUB();
        //$vehicles = $this->routeDataModel-> 
        $return = array();
        $return['dc'] = $dc;
        $return['hub'] = $hub;
        $vehicles = array();
        foreach ($hub as $key => $value) {
            
            foreach ($hub[$key] as $k => $value) {
                $vehicles[$k] = $this->routeDataModel->getvehicleDataEachHub($k,80);
            }
        }

        $return['vehicles'] = $vehicles;
        $status['0'] =  'na';
        $status['1'] =  'distributed';
        $status['2'] =  'assigned';
        $status['3'] =  'enroute';
        $status['4'] =  'complited';
        $status['5'] =  'idle';
        return View::make('RoutingAdmin::routeHistory')
                            ->with('data',$return)
                            ->with('status',$status);;
                            
    }

    /**
     * [generateLoadSheetOnRouteIds This basically is the 
     * 	route admin log id]
     * @return [type] [description]
     */
    public function generateLoadSheetOnRouteId(){

    	$data = input::get();
    	if(!isset($data['route_id'])){
    		return Response::json(array('status' => false,'message' => 'Route id not sent'));
    	}

    	$this->routeDataModel = new RouteDataModel();

        //get prestored routes for the vehicles
        $route_store = $this->routeDataModel->getPreStoredSortedRouteLogData($data['route_id']);
        if(!$route_store){

        	$data = $this->routeDataModel->getRouteDataOnRouteAdminLogId($data['route_id']);
        	if(!$data){
        		return Response::json(array('status' => false,'message' => 'required JsonObjects  are Missing for this route')); 
        	}else{

        		if(!is_null($data['route_data'])){

        			$insertId = $data['id'];
	            	$jsonrequest = json_decode($data['route_data'],true);
		            $jsonrequest['hub_coordinates'] = $this->routeDataModel->getHUBCoordinates($data['hub_id']);

		           	$jsonrequest = json_encode($jsonrequest);

		            $shortestPath = $this->routeDataModel->curlRequest($this->routingUrl.'arrangeShortestPath',$this->routingPort,$jsonrequest);
                    $de_name = $data['delivery_executive_name'];
		            if($shortestPath['status']){

		            	$this->routeDataModel->setRouteStoreData($insertId,$shortestPath,$data['hub_id'],$data['delivery_executive_name']);
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
            $de_name = $route_store['delivery_executive_name'];

        }

        if($shortestPath['status']){
			

			$shortestPath = json_decode($shortestPath['data'],true);
            $shortestPath['de_name'] = $de_name;
			if($shortestPath){

				//store this to the table
				//return  Response::json(array('status' => true,'message' => $shortestPath));     
               
                $unassigned_data = $this->routeDataModel->getdataFromRoutingUnassignedLog($data['route_id']);
                if(!$unassigned_data){
                    return View::make("RoutingAdmin::loadSheet")
                    ->with('json',$shortestPath);
                }
                else{
                    
                    $shortestPath['unassigned']['coordinates_data'] = $unassigned_data;
                    return View::make("RoutingAdmin::loadSheet")->with('json',$shortestPath);
                }
				
			}else{

				return Response::json(array('status' => false,'message' => 'Load on location filed at google end check again'));
			}
		}else{

			return Response::json(array('status' => false,'message' => 'Load distribution could not take place try again later'));
		}

    }

    /**
     * [generateViewMapOnRouteId This basically is the 
     * 	route admin log id]
     * @return [type] [description]
     */
    public function generateViewMapOnRouteId(){

    	$data = input::get();
    	if(!isset($data['route_id'])){
    		return Response::json(array('status' => false,'message' => 'Route id not sent'));
    	}

    	$this->routeDataModel = new RouteDataModel();

        //get prestored routes for the vehicles
        $route_store = $this->routeDataModel->getPreStoredSortedRouteLogData($data['route_id']);
        if(!$route_store){

        	$data = $this->routeDataModel->getRouteDataOnRouteAdminLogId($data['route_id']);
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
        	$shortestPath['data']['hub_coordinates'] = $this->routeDataModel->getHUBCoordinates($route_store['hub_id']);
        	$temp['status'] = $shortestPath['status'];
        	$temp['data'] = json_encode($shortestPath['data']);
        	$shortestPath = null;
        	$shortestPath = $temp;

        }

        if($shortestPath['status']){
				//$shortestPath = json_decode($shortestPath['data'],true);
			if($shortestPath){
                $mapData = json_encode($shortestPath['data'], true);
                
				return View::make("RoutingAdmin::viewMap")->with('json',$mapData);
			}else{

				return Response::json(array('status' => false,'message' => 'Load on location filed at google end check again'));
			}
		}else{

			return Response::json(array('status' => false,'message' => 'Load distribution could not take place try again later'));
		}


    }

    /**
     * [changeOrdersFormRoutes description]
     * @return [type] [description]
     */
    public function changeOrdersFormRoutes(){

    	$data = input::get();

    	if(!isset($data['assignTo']) || !isset($data['assignFrom']) || !isset($data['assignFromOrderId'])){

    		return Response::json(array('status' => false,'message' => "Either one of the parameter is missing 'assignTo' 'assignFrom' 'assignFromOrderId'"));	
    	}

    	$assignTo = (int)$data['assignTo'];
    	$assignFrom = (int)$data['assignFrom'];
    	$assignFromOrderId = (int)$data['assignFromOrderId'];
        $route_admin_id = (int)$data['route_admin_id'];

    	$this->routeDataModel = new RouteDataModel();
    	$dataToBeAssigned = $this->routeDataModel->getRouteDataOnRouteAdminLogId($assignTo);
    	if(!$dataToBeAssigned){
    		return Response::json(array('status' => false,'message' => "The Route to be moved dosent exists"));
    	}
    	$dataToBeRemoved = $this->routeDataModel->getRouteDataOnRouteAdminLogId($assignFrom);
    	if(!$dataToBeRemoved){
    		return Response::json(array('status' => false,'message' => "The Route to be copied from dosent exists"));
    	}

    	$orderDataTobeEdited = json_decode($dataToBeRemoved['route_data'],true);

    	$checkFlag = false;

    	foreach ($orderDataTobeEdited['coordinates_data'] as $key => $value) {
    		
    		if($orderDataTobeEdited['coordinates_data'][$key]['order_id'] == $assignFromOrderId){

    			$checkFlag = true;
    			$orderCutData = $orderDataTobeEdited['coordinates_data'][$key];
    			//var_dump($orderCutData);
    		 	unset($orderDataTobeEdited['coordinates_data'][$key]);
    		 	$newOrderSet = array_values($orderDataTobeEdited['coordinates_data']);
    		 	$orderDataTobeEdited['coordinates_data'] = $newOrderSet;
    			break;
    		}
    	}

    	if($checkFlag == false){
    		return Response::json(array('status' => false,'message' => "ordergiven dosent exist in the from route"));
    	}else{
    		
    		$dataToBeAssigned_orderData = json_decode($dataToBeAssigned['route_data'],true);
    		array_push($dataToBeAssigned_orderData['coordinates_data'],$orderCutData);

    		$this->routeDataModel->setRouteDataOnRouteAdminLogIdOnRouteUpdate($assignTo,$dataToBeAssigned_orderData);
    		$this->routeDataModel->setRouteDataOnRouteAdminLogIdOnRouteUpdate($assignFrom,$orderDataTobeEdited);
    		//thiswill update the routing_admin_crates_logs
    		$this->routeDataModel->changeCrateInfoOnCrateLog($orderCutData,$assignTo);
      		
      		$jsonrequest = $this->routeDataModel->getAllRequiredJsonAllRoutes($route_admin_id);
            	//$data = json_encode($jsonrequest);
    		return Response::json(array('status' => true,'message' => "order moved refresh routes", 'data' => $jsonrequest));

    	}
    }

 	/**
 	 * [setDeliveryExecutiveAndVehicle will accept the de or vehicle and set it accordingly]
 	 * return json
 	 */
    public function setDeliveryExecutiveAndVehicle(){

    	$data = input::get();

    	if(!isset($data['route_id'])){
    		return Response::json(array('status' => false,'message' => "Route id not found"));
    	}
        $route_id = $data['route_id'];
    	$return_data['de_set'] = false;
    	$de_flag = 0;
    	$ve_flag = 0;
    	$return_data['vehicle_set'] = false;
    	$this->routeDataModel = new RouteDataModel();

    	if(isset($data['de_id']) && isset($data['de_name'])){
    		
    		$de_flag = 1;
    		$de_data = $this->routeDataModel->assignDeliveryExcecutive($data['de_id'],$data['de_name'],$route_id);

    		if($de_data['status'] !== false){
    			//set the $return_data['de_set'] to true
    			$return_data['de_set'] = true;
    			$message['de_set']['status'] = true;
    			$message['de_set']['message'] = $de_data['message'];  	
    		}else{

    			$message['de_set']['status'] = false;
    			$message['de_set']['message'] = $de_data['message'];
    			if(isset($de_data['NotInHub'])){
    				$message['de_set']['NotInHub'] = $de_data['NotInHub'];
    			}
    		}    		
    		
    	}


    		$ve_flag = 1;
    		$ve_data = $this->routeDataModel->setVehicleToRoute($route_id,$data['vehicle_id'],$data['vehicle_number']);
    		if($ve_data['status'] !== false){
    			//set the return $return_data['vehicle_set'] to true
    			$return_data['vehicle_set'] = true;
    			$message['vehicle_set']['status'] = true;
    			$message['vehicle_set']['message'] = $ve_data['message'];
    		}else{
    			$message['vehicle_set']['status'] = false;
    			$message['vehicle_set']['message']=$ve_data['message'];
    		}
            
    	if($ve_flag == 1 && $de_flag == 1){
    		
    		if($return_data['de_set'] === false && $return_data['vehicle_set'] === false){
    			return Response::json(array('status' => false,'message' => $message));
    		}else if($return_data['de_set'] === true && $return_data['vehicle_set'] === true){
    			return Response::json(array('status' => true,'message' => $message));
    		}else{
    			return Response::json(array('status' => false,'message' => $message));
    		}
    		
    	}else if($ve_flag == 1 && $de_flag == 0){

    		$status = $message['vehicle_set']['status'];
    		return Response::json(array('status' => $status,'message' => $message));

    	}else if($ve_flag == 0 && $de_flag == 1){

    		$status = $message['de_set']['status'];
    		return Response::json(array('status' => $status,'message' => $message));

    	}else{
    		return Response::json(array('status' => false,'message' => 'Something went wrong try again'));
    	}

    	// if($return_data['de_set'] === false && $return_data['vehicle_set'] === false){

    	// 	return Response::json(array('status' => false,'message' => $message));

    	// }else if($return_data['de_set'] === true && $return_data['vehicle_set'] === true){

    	// 	return Response::json(array('status' => true,'message' => $message));
    	// }else{

    	// 	if($return_data['de_set'] === true && $return_data['vehicle_set'] === false){

    	// 		return Response::json(array('status' => false,'message' => $message));	
    	// 	}

    	// 	if($return_data['de_set'] === false && $return_data['vehicle_set'] === true){

    	// 		return Response::json(array('status' => false,'message' => $message));	
    	// 	}
    	// }

    }

    /**
     * [updateRouteDistanceTime description]
     * @return [type] [description]
     */
    public function updateRouteDistanceTime(){

    	$data = input::get();

    	if(!isset($data['route_id'])){
    		return Response::json(array('status' => false,'message' => "Route id not found"));
    	}
    	$this->routeDataModel = new RouteDataModel();
    	$route_id = $data['route_id'];
    	$data = $this->routeDataModel->getRouteDataOnRouteAdminLogId($route_id);
    	if(!$data){
    		return Response::json(array('status' => false,'message' => 'required JsonObjects  are Missing for this route')); 
    	}else{

    		if(!is_null($data['route_data'])){

    			$insertId = $data['id'];
    			$hub_coordinates = $this->routeDataModel->getHUBCoordinates($data['hub_id']);
    			//$route_data = $data['route_data'];
    			$sortedData = $this->routeDataModel->getPreStoredSortedRouteLogData($insertId);
    			if(!$sortedData){

    				$jsonrequest = json_decode($data['route_data'],true);
	            	$jsonrequest['hub_coordinates'] = $hub_coordinates;

	           		$jsonrequest = json_encode($jsonrequest);

	            	$shortestPath = $this->routeDataModel->curlRequest($this->routingUrl.'arrangeShortestPath',$this->routingPort,$jsonrequest);
		            if($shortestPath['status'] == 200){

		            	$this->routeDataModel->setRouteStoreData($insertId,$shortestPath,$data['hub_id']);
		            	$sortedData = $shortestPath['data'];
		            	$sortedData = json_decode($sortedData,true);

		            }else{

		            	return Response::json(array('status' => false,'message' => 'Google Response Failed for some reason!!'));	
		            }		            

        		}else{

        			$sortedData = json_decode($sortedData['data'],true);
        			$sortedData = $sortedData['data'];
        		}

        		$estimated_time = 0;
        		$estimated_distance = 0;

        		$keyCount =  count($sortedData['coordinates_data']);

        		$esu_time_slot = $sortedData['vehicleInfo']['esu_time'];

        		foreach ($sortedData['coordinates_data'] as $key => $value) {
        			
        			$esu_time = $value['coordinates']['esu_count'] * $esu_time_slot;//secs

        			if($key == 0){

        				$destination_lat = $value['coordinates']['lat'];
        				$destination_long = $value['coordinates']['long'];
        				$starting_lat = $hub_coordinates['lat'];
        				$starting_long = $hub_coordinates['long'];

        			}else if($key == $keyCount-1){

        				$destination_lat = $hub_coordinates['lat'];
        				$destination_long = $hub_coordinates['long'];
        				$starting_lat = $value['coordinates']['lat'];
        				$starting_long = $value['coordinates']['long'];

        			}else{

        				$destination_lat = $value['coordinates']['lat'];
        				$destination_long = $value['coordinates']['long'];
        				$starting_lat = $sortedData['coordinates_data'][$key-1]['coordinates']['lat'];
        				$starting_long = $sortedData['coordinates_data'][$key-1]['coordinates']['long'];

        			}
        			
        			$data = $this->routeDataModel->GetDrivingDistanceGoogle($starting_lat, $starting_long, $destination_lat, $destination_long);

        			$estimated_time += $esu_time+$data['time'];//secs
        			$estimated_distance += $data['distance']; //mts
        		}

        		if($estimated_time != 0 || $estimated_distance != 0){

        			$this->routeDataModel->updateRouteDistanceTimeOnRouteId($route_id,$estimated_time,$estimated_distance);
        		}

        		return Response::json(array('status' => true,'message' => array('distance' => $estimated_distance,'time' => $estimated_time)));

    		}else{

    			return Response::json(array('status' => false,'message' => 'route data missing in database')); 
    		}

    	}
    }

    /**
     * [downloadTripSheet download the sheet for the data]
     * @return [type] [description]
     */
    public function downloadTripSheet(){

    	$data = input::get();

    	if(!isset($data['route_admin_id'])){
    		return Response::json(array('status' => false,'message' => "Admin Route id not found"));
    	}

    	$route_admin_id = $data['route_admin_id'];
    	$this->routeDataModel = new RouteDataModel();
    	$jsonrequest = $this->routeDataModel->getAllRequiredJsonAllRoutesWithUnassigned($data['route_admin_id']);
    	//echo '<pre>';
    	//print_r($jsonrequest);exit;
    	if(count($jsonrequest) > 0){

    		$data_temp = [];
    		$temp = array();
			$temp[] = "Order Code";
			$temp[] = "Beat";
			$temp[] = "Crates";
			$temp[] = "Crates Count";
			$temp[] = "Vehicle Number";
			array_push($data_temp,$temp);

    		foreach ($jsonrequest['assigned'] as $value) {
    			
				foreach ($value['coordinates_data'] as $data) {
    				$temp = array();
    				$temp[] = $data['coordinates']['order_code'];
    				$temp[] = $data['coordinates']['beat'];
    				$temp[] = implode(',',$data['coordinates']['crates_info']['crates']);
    				$temp[] = $data['coordinates']['crates_info']['crates_count'];
    				$temp[] = $value['vehicleInfo']['vehicle_number'];
    				array_push($data_temp,$temp);
				}
  				
    		}

    		foreach ($jsonrequest['unassigned'] as $value) {
    			
				foreach ($value['coordinates_data'] as $data) {
    				$temp = array();
    				$temp[] = $data['coordinates']['order_code'];
    				$temp[] = $data['coordinates']['beat'];
    				$temp[] = implode(',',$data['coordinates']['crates_info']['crates']);
    				$temp[] = $data['coordinates']['crates_info']['crates_count'];
    				$temp[] = 'unassigned';
    				array_push($data_temp,$temp);
				}
  				
    		}

     		\Excel::create('TripSheet_'.$route_admin_id, function($excel) use($data_temp,$route_admin_id) {                    
            		$excel->sheet('TripSheet_'.$route_admin_id, function($sheet) use($data_temp) {                        
               	    	 $sheet->loadView('RoutingAdmin::excelTripSheet')->with('tripdata', $data_temp);
                	});
        	})->export('xls');
         	
    		//return View::make("RoutingAdmin::excelTripSheet")->with('tripdata',$data_temp);
    	}else{

    		return Response::json(array('status' => false,'message' => 'route data missing in database'));
    	}



    }


    public function moveToUnAssigned(){

    	$data = input::get();
    	if(!isset($data['route_id'])){
    		return Response::json(array('status' => false,'message' => 'route id parameter missing'));
    	}

    	if(!isset($data['order_id_list'])){

    		return Response::json(array('status' => false,'message' => 'order_id_list parameter missing'));
    	}else{

    		$order_data = explode(',',$data['order_id_list']);

    		if(!count($order_data) > 0 || $order_data[0] == ""){
    			return Response::json(array('status' => false,'message' => 'order_id_list should be a comma separated list'));
    		}
    	}

    	if(count($order_data) > 0){
    		$this->routeDataModel = new RouteDataModel();
    		$route_data = $this->routeDataModel->getRouteDataOnRouteAdminLogId($data['route_id']);
    		if(!$route_data){
    			return Response::json(array('status' => false,'message' => 'Route Id given do not exists'));
    		}else{


    			if($route_data['status'] == 1 || $route_data['status'] == 2){

    				$route_data_json = $route_data['route_data'];
	    			$route_data_json = json_decode($route_data_json,true);
	    			$order_ids_to_check = $order_data;
	    			$unassigned_data = [];
	    			$order_exist_check = 0;
	    			foreach ($route_data_json['coordinates_data'] as $key => $value) {

	    				if(in_array($value['order_id'], $order_ids_to_check)){
	    					array_push($unassigned_data,$route_data_json['coordinates_data'][$key]);
	    					unset($route_data_json['coordinates_data'][$key]);
	    					$order_exist_check++;
	    				}
	    			}

	    			if($order_exist_check !== count($order_ids_to_check)){
	    				return Response::json(array('status' => false,'message' => 'One or more order do not belong to the set given to move'));
	    			}

	    			$route_data_json['coordinates_data'] = array_values($route_data_json['coordinates_data']);

	    			$flag = $this->routeDataModel->setRouteDataOnRouteAdminLogIdOnRouteUpdate($data['route_id'],$route_data_json);

	    			//The main route_id for the give admin_log_id
	    			$flag = $this->routeDataModel->setRouteDataOnRouteAdminLogIdOnRouteUpdateUnassigned($route_data['route_id'],$unassigned_data);
	    			
	    			if($flag){
	    				return Response::json(array('status' => true,'message' => 'orders moved to unassigned'));
	    			}else{
	    				return Response::json(array('status' => false,'message' => 'Internal error in order movement'));
	    			}

    			}else{

    				return array('status' => false, 'message' => 'current status is either completed/ on-trip action not allowed');
	  			}
    		}
    	}else{

    		return Response::json(array('status' => false,'message' => 'Order Do not exist in the route'));	
    	}


    }

    public function moveUnassignedToRoute(){

    	$data = input::get();
    	
    	if(!isset($data['unassigned_order'])){ //order_id

    		return Response::json(array('status' => false,'message' => 'Order Code Do not Exist'));
    	}

    	if(!isset($data['to_route'])){
    		return Response::json(array('status' => false,'message' => 'Move To route not found'));
    	}

    	if(!isset($data['unassign_route_id'])){
    		return Response::json(array('status' => false,'message' => 'unassign_route_id parameter missing'));
    	}

    	$this->routeDataModel = new RouteDataModel();
    	$ret_data = $this->routeDataModel->mapUnassignedToRoute($data['unassign_route_id'],$data['unassigned_order'],$data['to_route']);
    	if($ret_data){

    		return Response::json(array('status' => true,'message' => 'Order moved to route'));

    	}else{
			return Response::json(array('status' => false,'message' => 'Unassigned order to route failed'));    		
    	}

    }

    //////////////////////////////////////////////////////API///////////////////////////////////////////////
     /**
     * [getRoutesOnHUB description]
     * @return [type] [description]
     */
    public function getRoutesOnHUBapi(){

    	//gethubid form Request
    	//get all data and vehicle list
    	
    	$data = input::get();
    	if(!isset($data['hub_id'])){
    		return Response::json(array('status' => false,'message' => 'Hub id not sent'));
    	}

    	if(!isset($data['date'])){
    		return Response::json(array('status' => false,'message' => 'Date not sent'));
    	}else{

    		$date = $data['date'];
    		$this->routeDataModel = new RouteDataModel();
    		$allHubData = $this->routeDataModel->getLatestRoutesInHUB($data['hub_id'],$date);
    		return Response::json($allHubData);
    	}
    	
    }


    public function getSortedDataApi(){

    	$data = input::get();

    	if(!isset($data['route_id'])){
    		return Response::json(array('status' => false,'message' => "Route id not found"));
    	}
    	$this->routeDataModel = new RouteDataModel();
    	$route_id = $data['route_id'];
    	$route_store = $this->routeDataModel->getPreStoredSortedRouteLogData($route_id);
        if(!$route_store){

        	$data = $this->routeDataModel->getRouteDataOnRouteAdminLogId($route_id);
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

    	if($shortestPath['status']){
			

			$shortestPath = json_decode($shortestPath['data'],true);
			if($shortestPath){

				$extraData = $this->routeDataModel->getDEAndDistanceRouteAdminId($route_id);
				$shortestPath['extraData'] = $extraData;
				return  Response::json(array('status' => true,'message' => $shortestPath));
				//return View::make("RoutingAdmin::loadSheet")->with('json',$shortestPath);
			}else{

				return Response::json(array('status' => false,'message' => 'Load on location filed at google end check again'));
			}
		}else{

			return Response::json(array('status' => false,'message' => 'Load distribution could not take place try again later'));
		}
    }

    //////////////////////////////////////////////////////API///////////////////////////////////////////////


}


?>
