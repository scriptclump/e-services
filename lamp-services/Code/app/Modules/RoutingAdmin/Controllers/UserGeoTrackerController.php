<?php
/**
 * @Class FeildForceTrackerController
 * @file FeildForceTrackerController.php
 * @author Ebutor Distribution
 * prasenjit@ebutor
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
use Excel;
use Mail;


/*
	Model Classes
 */
use App\Modules\RoutingAdmin\Models\RouteDataModel;
use App\Modules\RoutingAdmin\Models\UserGeoTrackerModel;
use App\Modules\RoutingAdmin\Models\TrackHistoryMongo;
use App\Modules\RoutingAdmin\Models\GeoTrackHistory;

class UserGeoTrackerController extends BaseController{


	//protected $routeDataModel;
	protected $UserGeoTrackerModel;
    protected $RouteDataModel;


	function __construct(){

		$this->UserGeoTrackerModel = new UserGeoTrackerModel();
        $this->RouteDataModel = new RouteDataModel();
	}

	public function generateTrackEx(){

        $usertype = '57';
		$ff_list = $this->UserGeoTrackerModel->getAllFeildForce($usertype);
		if(!$ff_list){
			$ff_list = array();
		}elseif (count($ff_list) == 0) {
			$ff_list = array();
		}

        $hubs = $this->RouteDataModel->getAllHUB();

        $hub_list = array();
        $de_list = array();
        
        /**
         * [$key description]
         * @var [type]
         */
        foreach ($hubs as $key=>$value){
            
            foreach ($value as $hub_id => $value) {

                $hub_list[$hub_id] = $value; 
                $hub_de = $this->RouteDataModel->getDeliveryExecutiveFromHubId($hub_id);
                $de_list[$hub_id] = array();
                if(count($hub_de) > 0){

                    $de_list_actual = array();
                    foreach ($hub_de as $value) {
                        
                        $de_list[$hub_id][$value['user_id']] = $value['name'];
                        $de_list_actual[] = $value['user_id'];
                    }

                    $de_list[$hub_id][implode(',',$de_list_actual)] = 'all'; 

                }
            } 

        }

        //$hub_de = $this->$RouteDataModel->getDeliveryExecutiveFromHubId($hub_id);

        return View::make("RoutingAdmin::viewTrackExecutive")
        			->with('hub_list',json_encode($hub_list))
                    ->with('de_list',json_encode($de_list));
    }

    /**
     * [getLastKnownLocation description]
     * @return [type] [description]
     */
    public function getLastKnownLocation(){

    	$data = input::get();

    	if(!isset($data['user_list'])){
    		return Response::json(array('status' => false,'message' => 'no specific user not sent'));
    	}

    	if($data['user_list'] == 'all'){
 
    		$user_id = null;
 
    	}else{
 
    		$user_id = $data['user_list'];
 
    	}

    	//$usertype = '52,53'; //feild force
        $usertype = '57'; //delivery guy
    	$data = $this->UserGeoTrackerModel->getLastKnownLocation($user_id,$usertype);

    	if(!$data){
    		return Response::json(array('status' => false,'message' => 'user data not available'));
    	}else{

    		return Response::json(array('status' => true,'message' => $data));

    	}



    }

    /**
     * [getGeoTrackHistory description]
     * @return [type] [description]
     */
    public function getGeoTrackHistory(){

    	$data = input::get();
    	if(!isset($data['user_id'])){
    		return Response::json(array('status' => false,'message' => 'no specific user sent'));
    	}

    	if(!isset($data['date'])){
    		return Response::json(array('status' => false,'message' => 'no date sent for the particular user'));
    	}

    	if(isset($data['date'])){

    		if((bool)strtotime($data['date'])){
    			$data['date'] = date('Y-m-d',strtotime($data['date']));
    		}else{
    			return Response::json(array('status' => false,'message' => 'date format is wrong'));
    		}
    	}

        if ($data['date'] != date('Y-m-d')) {

            /* Getting Data From Mongo DB */
            $TrackHistoryMongo = new TrackHistoryMongo();
            $returnData = $TrackHistoryMongo->getTrackHistoryMongo($data['user_id'], $data['date']);

            if (!$returnData) {
                $this->storeTrackHistory($data['user_id'], $data['date']);
                $level_returnData = $TrackHistoryMongo->getTrackHistoryMongo($data['user_id'], $data['date']);
                if (!$level_returnData) {
                    return Response::json(array('status' => false,'message' => 'No data found'));
                }else{
                    $hostoryCoordinateData = $level_returnData['coordinate_data'];
                    $hisotryOrderData = $level_returnData['order_data'];
                    return Response::json(array('status' => true,'message' => $hostoryCoordinateData,'order_data' => $hisotryOrderData));
                }               
                
            }else{

                $hostoryCoordinateData = $returnData['coordinate_data'];
                $hisotryOrderData = $returnData['order_data'];

                return Response::json(array('status' => true,'message' => $hostoryCoordinateData,'order_data' => $hisotryOrderData));
            }        

        }else{
            $coordinateData = $this->UserGeoTrackerModel->historyTimeRoute($data['user_id'],$data['date']);
            if(!$coordinateData){

                return Response::json(array('status' => false,'message' => 'data not found'));      

            }else{
               
                $order_data = $this->UserGeoTrackerModel->setUpdateDeOrderlist($data['user_id'],$data['date']);
                return Response::json(array('status' => true,'message' => $coordinateData,'order_data' => $order_data));
            }
        }
    	
    }

    /**
     * [trackHistoryKML description]
     * @return [type] [description]
     */
    public function trackHistoryKML($user_id,$date,$file_format){

        $date = date('Y-m-d',strtotime($date));
        $coordinateData = $this->UserGeoTrackerModel->historyTimeRoute($user_id,$date);
        if(!$coordinateData){
            $coordinateData = array();
        }

        $text = '';
        foreach ($coordinateData as $coordinates) {
            
            $text .= $coordinates['latitude'].','.$coordinates['longitude'].',0'.''."\n";
        }

        $data = View::make("RoutingAdmin::kmldata")
                    ->with('coordinatesData',$text);
        $response = Response::make($data, 200);
        $response->header('Content-Type', 'text/xml');
        return $response;
    }

    public function storeTrackHistoryCron($user_id = '', $date = '' )
    {   $data = [];
        $data['user_id'] = $user_id;
        $data['date'] = $date;
        
        if(!isset($data['user_id'])){
            return Response::json(array('status' => false,'message' => 'no specific user sent'));
        }

        if(!isset($data['date'])){
            return Response::json(array('status' => false,'message' => 'no date sent for the particular user'));
        }

        if(isset($data['date'])){

            if((bool)strtotime($data['date'])){
                $data['date'] = date('Y-m-d',strtotime($data['date']));
            }else{
                return Response::json(array('status' => false,'message' => 'date format is wrong'));
            }
        }
        $coordinateData = $this->UserGeoTrackerModel->historyTimeRoute($data['user_id'],$data['date']);
        $order_data = $this->UserGeoTrackerModel->setUpdateDeOrderlist($data['user_id'],$data['date']);
        $totalDistance = 0;
        if (!$coordinateData) {
            return Response::json(array('status' => false,'message' => 'No history found'));
        }else{
            foreach ($coordinateData as $key => $coordinates) {
                if ($key > 0) {
                    $totalDistance += $this->haversineGreatCircleDistance($coordinateData[$key - 1]["latitude"],$coordinateData[$key - 1]["longitude"],$coordinateData[$key]["latitude"],$coordinateData[$key]["longitude"]);
                }
            }
        }
        
          
        $tempDistance = $totalDistance / 1000;
        $tempArr = [];
        $tempArr['distance'] = round( $tempDistance * 100)/100; /*In KM*/
        $tempArr['order_data'] = $order_data;
        $tempArr['coordinate_data'] = $coordinateData;
        $tempArr['de_id'] = $data['user_id'];
        $tempArr['hub_id'] = $order_data[0]['hub_id'];
        $tempArr['hub_name'] = $order_data[0]['hub_name'];
        $tempArr['date'] = $data['date'];
        $tempArr['de_name'] = $coordinateData[0]['name'];
        $TrackHistoryMongo = new TrackHistoryMongo();
        $TrackHistoryMongo->saveTrackHistoryMongo($tempArr);
        unset($tempArr['order_data']);
        unset($tempArr['coordinate_data']);
        $GeoTrackHistory = new GeoTrackHistory();
        $GeoTrackHistory->insertGeoTrackHistory($tempArr);
        return Response::json(array('status' => 200,'message' => 'Data inserted to MongoDB & MySql'));
    }

    public function storeTrackHistory()
    {
        $data = input::get();
        
        if(!isset($data['user_id'])){
            return Response::json(array('status' => false,'message' => 'no specific user sent'));
        }

        if(!isset($data['date'])){
            return Response::json(array('status' => false,'message' => 'no date sent for the particular user'));
        }

        if(isset($data['date'])){

            if((bool)strtotime($data['date'])){
                $data['date'] = date('Y-m-d',strtotime($data['date']));
            }else{
                return Response::json(array('status' => false,'message' => 'date format is wrong'));
            }
        }
        $coordinateData = $this->UserGeoTrackerModel->historyTimeRoute($data['user_id'],$data['date']);
        $order_data = $this->UserGeoTrackerModel->setUpdateDeOrderlist($data['user_id'],$data['date']);
        $totalDistance = 0;
        if (!$coordinateData) {
            return Response::json(array('status' => false,'message' => 'No history found'));
        }else{
            foreach ($coordinateData as $key => $coordinates) {
                if ($key > 0) {
                    $totalDistance += $this->haversineGreatCircleDistance($coordinateData[$key - 1]["latitude"],$coordinateData[$key - 1]["longitude"],$coordinateData[$key]["latitude"],$coordinateData[$key]["longitude"]);
                }
            }
        }  
          
        $tempDistance = $totalDistance / 1000;
        $tempArr = [];
        $tempArr['distance'] = round( $tempDistance * 100)/100; /*In KM*/
        $tempArr['order_data'] = $order_data;
        $tempArr['coordinate_data'] = $coordinateData;
        $tempArr['de_id'] = $data['user_id'];
        $tempArr['hub_id'] = $order_data[0]['hub_id'];
        $tempArr['hub_name'] = $order_data[0]['hub_name'];
        $tempArr['date'] = $data['date'];
        $tempArr['de_name'] = $coordinateData[0]['name'];
        $TrackHistoryMongo = new TrackHistoryMongo();
        $TrackHistoryMongo->saveTrackHistoryMongo($tempArr);

        return Response::json(array('status' => 200,'message' => 'Data inserted to mongo'));
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000){
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function getTrackHistoryByHub()
    {
        $data = input::get();
        if(!isset($data['hub_id'])){
            return Response::json(array('status' => false,'message' => 'no specific hub sent'));
        }

        if(!isset($data['from_date'])){
            return Response::json(array('status' => false,'message' => 'no from date sent for the particular user'));
        }
        if(!isset($data['to_date'])){
            return Response::json(array('status' => false,'message' => 'no to date sent for the particular user'));
        }

        if(isset($data['from_date'])){

            if((bool)strtotime($data['from_date'])){
                $data['from_date'] = date('Y-m-d',strtotime($data['from_date']));
            }else{
                return Response::json(array('status' => false,'message' => 'from date format is wrong'));
            }
        }

        if(isset($data['to_date'])){

            if((bool)strtotime($data['to_date'])){
                $data['to_date'] = date('Y-m-d',strtotime($data['to_date']));
            }else{
                return Response::json(array('status' => false,'message' => 'from date format is wrong'));
            }
        }
        $TrackHistoryMongo = new TrackHistoryMongo();
        $historyDataByHub = $TrackHistoryMongo->getTrackHistoryByHubMongo($data['hub_id'],$data['from_date'],$data['to_date'] );
        $tempOrderStatus = [];
        $statusCoount = []; 
        $tempStatusCount = [];
        $totalCollectedAmount = [];
        $totalInvoicedAmount = []; 
        $totalReturnedAmount = [];            
        
        if (!$historyDataByHub) {
            return Response::json(array('status' => false,'message' => 'No data found in mongo!'));
        }else{
            foreach ($historyDataByHub as $key => $historyDataByHubs) {
                if (!isset($totalInvoicedAmount[$key])) {
                    $totalInvoicedAmount[$key] = [];
                    $totalInvoicedAmount[$key]['invoiced_amount'] = 0;
                }
                if (!isset($totalCollectedAmount[$key])) {
                    $totalCollectedAmount[$key] = [];
                    $totalCollectedAmount[$key]['collected_amount'] = 0;
                }
                if (!isset($totalReturnedAmount[$key])) {
                    $totalReturnedAmount[$key] = [];
                    $totalReturnedAmount[$key]['returned_total'] = 0;
                }               
                
                foreach ($historyDataByHubs['order_data'] as $orderKey => $orderValue) {
                    $tempOrderStatus[$key][$orderValue['order_status']][] = $orderKey;
                    if (isset($orderValue['invoice_amount'])) {
                        $totalInvoicedAmount[$key]['invoiced_amount'] += (float)$orderValue['invoice_amount'];
                    }else{
                        $totalCollectedAmount[$key]['collected_amount'] += 0;
                    }
                    if (isset($orderValue['collected_amount'])) {
                        $totalCollectedAmount[$key]['collected_amount'] += (float)$orderValue['collected_amount'];
                    }else{
                        $totalCollectedAmount[$key]['collected_amount'] += 0;
                    }
                    if (isset($orderValue['return_total'])) {
                        $totalReturnedAmount[$key]['returned_total'] += (float)$orderValue['return_total'];
                    }else{
                        $totalReturnedAmount[$key]['returned_total'] += 0;
                    }
                }
                $statusCoount = $tempOrderStatus[$key];
                foreach ($statusCoount as $statusCoountKey => $statusCoountValue) {
                    $statusCoountKey = str_replace(' ', '_', $statusCoountKey);
                    $tempStatusCount[$key][$statusCoountKey] = count($statusCoountValue);
                }
                $statusCoount = $tempStatusCount[$key];
                $statusCoount['order_attempted'] = count($historyDataByHubs['order_data']);
                $historyDataByHub[$key]['order_status'] =  $statusCoount;
                $historyDataByHub[$key]['invoiced_amount'] = $totalInvoicedAmount[$key]['invoiced_amount'];
                $historyDataByHub[$key]['collected_amount'] = $totalCollectedAmount[$key]['collected_amount'];
                $historyDataByHub[$key]['returned_total'] = $totalReturnedAmount[$key]['returned_total'];
                $historyDataByHub[$key]['attempted_date'] = date('Y-m-d',$historyDataByHubs['date']->sec);
                unset($historyDataByHub[$key]['date']);
            }
            return Response::json(array('status' => true,'message' => $historyDataByHub));
        }
    }

    public function getTrackHistoryByDE()
    {
        $data = input::get();
        if(!isset($data['de_id'])){
            return Response::json(array('status' => false,'message' => 'no specific DE sent'));
        }

        if(!isset($data['from_date'])){
            return Response::json(array('status' => false,'message' => 'no from date sent for the particular user'));
        }
        if(!isset($data['to_date'])){
            return Response::json(array('status' => false,'message' => 'no to date sent for the particular user'));
        }

        if(isset($data['from_date'])){

            if((bool)strtotime($data['from_date'])){
                $data['from_date'] = date('Y-m-d',strtotime($data['from_date']));
            }else{
                return Response::json(array('status' => false,'message' => 'from date format is wrong'));
            }
        }

        if(isset($data['to_date'])){

            if((bool)strtotime($data['to_date'])){
                $data['to_date'] = date('Y-m-d',strtotime($data['to_date']));
            }else{
                return Response::json(array('status' => false,'message' => 'from date format is wrong'));
            }
        }
        $TrackHistoryMongo = new TrackHistoryMongo();
        $historyDataByDE = $TrackHistoryMongo->getTrackHistoryByDEMongo($data['de_id'],$data['from_date'],$data['to_date'] );
        $tempOrderStatus = [];
        $statusCoount = []; 
        $tempStatusCount = []; 
        $totalCollectedAmount = [];
        $totalInvoicedAmount = []; 
        $totalReturnedAmount = [];

        if (!$historyDataByDE) {
            return Response::json(array('status' => false,'message' => 'No data found in mongo!'));
        }else{
            foreach ($historyDataByDE as $key => $historyDataByDES) {
                if (!isset($totalInvoicedAmount[$key])) {
                    $totalInvoicedAmount[$key] = [];
                    $totalInvoicedAmount[$key]['invoiced_amount'] = 0;
                }
                if (!isset($totalCollectedAmount[$key])) {
                    $totalCollectedAmount[$key] = [];
                    $totalCollectedAmount[$key]['collected_amount'] = 0;
                }
                if (!isset($totalReturnedAmount[$key])) {
                    $totalReturnedAmount[$key] = [];
                    $totalReturnedAmount[$key]['returned_total'] = 0;
                }

                foreach ($historyDataByDES['order_data'] as $orderKey => $orderValue) {
                    $tempOrderStatus[$key][$orderValue['order_status']][] = $orderKey;
                    if (isset($orderValue['invoice_amount'])) {
                        $totalInvoicedAmount[$key]['invoiced_amount'] += (float)$orderValue['invoice_amount'];
                    }else{
                        $totalCollectedAmount[$key]['collected_amount'] += 0;
                    }
                    if (isset($orderValue['collected_amount'])) {
                        $totalCollectedAmount[$key]['collected_amount'] += (float)$orderValue['collected_amount'];
                    }else{
                        $totalCollectedAmount[$key]['collected_amount'] += 0;
                    }
                    if (isset($orderValue['return_total'])) {
                        $totalReturnedAmount[$key]['returned_total'] += (float)$orderValue['return_total'];
                    }else{
                        $totalReturnedAmount[$key]['returned_total'] += 0;
                    }
                }

                $statusCoount = $tempOrderStatus[$key];
                foreach ($statusCoount as $statusCoountKey => $statusCoountValue) {
                    $statusCoountKey = str_replace(' ', '_', $statusCoountKey);
                    $tempStatusCount[$key][$statusCoountKey] = count($statusCoountValue);
                }
                $statusCoount = $tempStatusCount[$key];
                $statusCoount['order_attempted'] = count($historyDataByDES['order_data']);
                $historyDataByDE[$key]['order_status'] =  $statusCoount;
                $historyDataByDE[$key]['invoiced_amount'] = $totalInvoicedAmount[$key]['invoiced_amount'];
                $historyDataByDE[$key]['collected_amount'] = $totalCollectedAmount[$key]['collected_amount'];
                $historyDataByDE[$key]['returned_total'] = $totalReturnedAmount[$key]['returned_total'];
                $historyDataByDE[$key]['attempted_date'] = date('Y-m-d',$historyDataByDES['date']->sec);
                unset($historyDataByDE[$key]['date']);
            }
            return Response::json(array('status' => true,'message' => $historyDataByDE));
        }
    }


    public function exportTrackDataToExcel()
    {
        $data = input::get();

        if(isset($data['hub_id'])){
            if (!isset($data['from_date']) || !isset($data['to_date'])) {
                return Response::json(array('status' => false,'message' => 'from date format is wrong'));
            }
          
            $track_data = $this->getTrackHistoryByHub($data['hub_id'],$data['from_date'],$data['to_date']);
            $excel_data = $track_data->getData(); 
        }

        if (isset($data['de_id'])) {
            if (!isset($data['from_date']) || !isset($data['to_date'])) {
                return Response::json(array('status' => false,'message' => 'from date format is wrong'));
            }
          
            $track_data = $this->getTrackHistoryByDE($data['de_id'],$data['from_date'],$data['to_date']);
           $excel_data = $track_data->getData(); 
        }

        if ($excel_data->status) {
            $final_excel_data = [];
            foreach ($excel_data->message as $key => $messages) {

                $final_excel_data[$key]['Hub']             = $messages->hub_name;
                $final_excel_data[$key]['Do']              = $messages->de_name;
                $final_excel_data[$key]['vehicle_no']      = $messages->order_data[0]->vehicle_no; 
                $final_excel_data[$key]['attempted_date']  = $messages->attempted_date; 
                $final_excel_data[$key]['distance']        = $messages->distance;
                $final_excel_data[$key]['order_assigned']  = $messages->order_status->order_attempted;
                $final_excel_data[$key]['invoice_value']   = $messages->invoiced_amount;
                $final_excel_data[$key]['delivered']       = isset($messages->order_status->DELIVERED) ? $messages->order_status->DELIVERED : 0;
                $final_excel_data[$key]['partial_return']  = isset($messages->order_status->PARTIALLY_DELIVERED) ? $messages->order_status->PARTIALLY_DELIVERED : 0;
                $final_excel_data[$key]['full_return']     = isset($messages->order_status->RETURNED) ? $messages->order_status->RETURNED : 0;
                $final_excel_data[$key]['hold']            = isset($messages->order_status->HOLD) ? $messages->order_status->HOLD : 0;
                $final_excel_data[$key]['collected_value'] = $messages->collected_amount;
                $final_excel_data[$key]['returned_value']  = $messages->returned_total;
                
            }
            if($final_excel_data != null or $final_excel_data != "")
            {    
               $filename = "Excel_".date("d-m-Y");
                \Excel::create('TripSheet_'.$filename, function($excel) use($final_excel_data) {                    
                        $excel->sheet('Tracking Data', function($sheet) use($final_excel_data) {                        
                             $sheet->loadView('RoutingAdmin::trackHistoryExcel')->with('tripdata', $final_excel_data);
                        });
                })->export('xls');
            }
        }else{
            return Response::json(array('status' => false,'message' => 'No data Found'));
        }
    }
    public function nonQueue(){
        $title = 'My mail';
        $content = 'welecome to laravel mail';
        $data['title'] = $title;
        $data['content'] = $content;
        Mail::send('RoutingAdmin::mail', ['title' => $title, 'content' => $content], function ($message)
        {
            $message->subject('Test Mail');
            //$message->from('tracker@ebutor.com', 'Ebutor Distribution Pvt Ltd');

            $message->to('vicky@vickytripathy.in');

        });

        return response()->json(['message' => 'Request completed']);
    }
}