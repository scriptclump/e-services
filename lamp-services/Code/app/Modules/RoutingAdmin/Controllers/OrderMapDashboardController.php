<?php
/**
 * @Class OrderMapDashboardController
 * @file OrderMapDashboardController.php
 * @author Ebutor Distribution
 */

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

class OrderMapDashboardController extends BaseController{

	protected $routeDataModel;
	protected $orderMapDashboard;


	function __construct()
	{
		$this->routeDataModel = new RouteDataModel();
		$this->orderMapDashboard = new OrderMapDashboard();
	}

	/**
	 * Order Map View for map view of order across dc
	 * @param null
	 * @return void
	 */
	public function OrderMapView()
	{
		$data = $this->OrderMapDashboard();
		$filterElements = $this->filterElements();
		return View::make('RoutingAdmin::getAllOrders')
    						->with('data',$data)
    						->with('filter_elements', $filterElements);
	}

	/**
	 * Order Map Dashboard for api call
	 * @param date
	 * @return json
	 */
	public function OrderMapDashboard()
	{
		$data = Input::get();
		$order_date = [];
		if(isset($data['from_date'])) {
			if ($data['from_date'] != '') {
				$order_date['from_date'] = $data['from_date'];
			}else{
				$order_date['from_date'] = date('Y-m-d');
			}
		}else{
			$order_date['from_date'] = date('Y-m-d');
		}

		if(isset($data['to_date'])) {
			if ($data['to_date'] != '') {
				$order_date['to_date'] = $data['to_date'];
			}else{
				$order_date['to_date'] = date('Y-m-d');
			}
		}else{
			$order_date['to_date'] = date('Y-m-d');
		}
				
		if (strtotime($order_date['from_date']) > strtotime($order_date['to_date'])) {
			$order_date['from_date'] = $data['to_date'];
		}
		/**
		 * Getting order list by date
		 * @var array
		 */
		$order_list = [];
		$order_list_model = $this->orderMapDashboard->getAllOrdersByDate($order_date['from_date'], $order_date['to_date']);
		$orders_with_zero = [];
		$orders_with_coords = [];
		if ($order_list_model) {
			foreach ($order_list_model as $key => $order) {
				if ($order['latitude'] == 0 || $order['longitude'] == 0) {
					$orders_with_zero[] = $order;
				}else{
					$orders_with_coords[] = $order; 
				}
			}
		}else{
			var_dump($order_date);
			return json_encode(array('status' => false ,'message' => 'No order found for date: '.$order_date['from_date'].'-'.$order_date['to_date']));
		}		
		$order_list['orders_with_coords'] = $orders_with_coords;
		$order_list['orders_without_coords'] = $orders_with_zero;
		$order_list['hub_details'] = $this->orderMapDashboard->getHubList();
		if ($order_list) {
			$return_data = array('status' => true ,'OrderList' => $order_list);
			$return_data = json_encode($return_data);
			return $return_data;
		}else{
			return json_encode(array('status' => false ,'message' => 'No order found'));
		}		
	}

	/**
	 * Function filter element for dropdown menu data for filter
	 * @return void
	 */
	public function filterElements()
	{
		$allHub = $this->orderMapDashboard->getAllHUB();
		$allDc = $this->orderMapDashboard->getAllDC();
		/**
		 * $allFieldForce -- getting fildforce data by sending role id 53
		 * @var Array
		 */
		$allBeats = $this->orderMapDashboard->getAllBeats();
		$allFieldForce = $this->orderMapDashboard->getFieldForceList(53);
		$orderStatusList = $this->orderMapDashboard->getListOfOrderStatus();
		$return_array = [];
		$return_array['HUB'] = 	$allHub;
		$return_array['DC'] = $allDc;
		$return_array['FieldForce'] = $allFieldForce;
		$return_array['order_status'] = $orderStatusList;
		$return_array['beat'] = $allBeats;
		return json_encode($return_array);
	}
}