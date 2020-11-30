<?php
namespace App\Modules\CNCDashboard\Controllers;

use DB;
use Log;
use View;
use Cache;
use Input;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\ReportsRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\Dashboard\Controllers\DashboardController;

class CNCDashboardController extends BaseController {

    public function __construct() {
        $this->middleware(function($request,$next){
            parent::__construct();
            if (!Session::has('userId')) {
                return Redirect::to('/');
            }
            return $next($request);
        });
        $this->reports = new ReportsRepo();
        $this->roleAccess = new RoleRepo();
        $this->dashboard = new DashboardController();

        // $this->userId = Session::get('userId');
        // By Default its Zero!
        $this->userId = 0;

        // Hub Id for CNC
        define('CNC_HUBID', 10694);
    }

    /**
    * The GET method for the Index Dashboard Grid
    */
    public function index() {
        try {
            
            // Code to Check weather the User has CNC Page Access or not
            $checkCNCAccess = $this->roleAccess->checkPermissionByFeatureCode('CNC002');
            if(!$checkCNCAccess)
                return Redirect::to('/');

            // By default it will load for the Current Date
            $fromDate = date('Y-m-d');
            $toDate = $this->dashboard->tomorrow->format('Y-m-d');
            $result = $this->getCnCDashboardData($this->userId,$fromDate,$toDate);

            return view('CNCDashboard::index')
                ->with(["last_updated"=>date('Y-m-d h:i a'), "cncData"=>$result]);

        } catch (Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }

    /**
    * The POST method for the Index Dashboard Grid
    */
    public function getIndexData(){
        try {
            
            $data = \Input::all();            
            $datesArr = $this->dashboard->getDateRange($data);
            $fromDate = $datesArr["fromDate"];
            $toDate = $datesArr["toDate"];
            
            $result["dashboard"] = $this->getCnCDashboardData($this->userId,$fromDate,$toDate);
            
            if(!empty($data))
            {
                return ['last_updated' => date('Y-m-d h:i a'),'cncData' => $result];
            }          
        
        } catch (Exception $e) {
            return "Sorry! Something went wrong. Please contact the Admin";
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
        }
    }
    
    /**
    * The Central Function for the Dashboard Data.
    * It acts like a Model for the Dashboard Procedure..,
    */
    public function getCnCDashboardData($userId = 0, $fromDate, $toDate){
        
        // Code to Check weather the User has TGM Access or not
        $checkTGMAccess = $this->roleAccess->checkPermissionByFeatureCode('USRTGM01');
        $flag = ($checkTGMAccess)?4:1;

        $data = Cache::tags("cncDashboard")->get('cnc_dashboard_report_'.$userId.'_'.$flag.'_'.$fromDate.'_'.$toDate,false);
        if(empty($data))
        {
            $query = "CALL getCnCDashboard_web(?,?,?,?)";
            $response = DB::select($query,[$userId,$flag,$fromDate,$toDate]);
            $result = json_decode(json_encode($response),true);
            $data = json_decode($result[0]['Dashboard'],true);

            Cache::tags("cncDashboard")->put('cnc_dashboard_report_'.$userId.'_'.$flag.'_'.$fromDate.'_'.$toDate,$data,CACHE_TIME);
            Cache::tags("cncDashboard")->put('cnc_dashboard_report_'.$userId.'_'.$flag.'_'.$fromDate.'_'.$toDate.'last_updated',date('Y-m-d h:i:s a'),CACHE_TIME);
        }

        return json_decode(json_encode($data));
    }
    
    public function getTodayFFUsersList()
    {
        $data = Input::all();
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        
        $data = Cache::tags("cncDashboard")->get('cnc_ffreport'.'0_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getcncFFReport(?,?,?)";
            $data = DB::select($query,[0,$fromDate,$toDate]);
            Cache::tags("cncDashboard")->put('cnc_ffreport'.'0_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);
        
        }
        foreach ($data as $record) {
            $record->NAME = isset($record->NAME)?"  ".$record->NAME:'';
            $record->order_cnt = isset($record->order_cnt)?floatval($record->order_cnt):0;
            $record->calls_cnt = isset($record->calls_cnt)?floatval($record->calls_cnt):0;
            $record->commission = isset($record->commission)?floatval($record->commission):0;
            $record->Contribution = isset($record->Contribution)?floatval($record->Contribution):0;
            $record->success_rate = isset($record->success_rate)?floatval($record->success_rate):0;
            $record->margin = isset($record->margin)?floatval($record->margin):0;
            $record->tbv = isset($record->tbv)?floatval($record->tbv):0;
            $record->UOB = isset($record->UOB)?floatval($record->UOB):0;
            $record->ABV = isset($record->ABV)?floatval($record->ABV):0;
            $record->TLC = isset($record->TLC)?floatval($record->TLC):0;
            $record->ULC = isset($record->ULC)?floatval($record->ULC):0;
            $record->ALC = isset($record->ALC)?floatval($record->ALC):0;
        }
        return json_encode($data);
    }

    public function getNewCustomersDashboard()
    {
        $data = Input::all();
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];

        $data = Cache::tags("cncDashboard")->get('cnc_newCustomersDashboard_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getCnCLegalEntitiesExportData(?,?)";
            $data = DB::select($query,[$fromDate,$toDate]);
            Cache::tags("cncDashboard")->put('cnc_newCustomersDashboard_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }

        return array("data" => $data);
    }

    public function getDeliveryDashboard()
    {
        $data = Input::all();
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];

        $data = Cache::tags("cncDashboard")->get('cnc_deliveryDashboard_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getDeliverDashboard_web(?,?,?,?,?,?,?)";
            $data = DB::select($query,[0,ONLY_CNC_HUB,$fromDate,$toDate,NULL,NULL,NULL]);
            Cache::tags("cncDashboard")->put('cnc_deliveryDashboard_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }

        $result = $this->dashboard->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getPickersDashboard()
    {
        $data = Input::all();
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];

        $data = Cache::tags("cncDashboard")->get('cnc_pickerDashboard_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getPickerDashboard_web(?,?,?,?,?,?,?)";
            $data = DB::select($query,[NULL,NULL,$fromDate,$toDate,NULL,NULL,CNC_HUBID]);
            Cache::tags("cncDashboard")->put('cnc_pickerDashboard_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }
        
        $result = $this->dashboard->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getVerificationDashboard()
    {
        $data = Input::all();
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        
        $data = Cache::tags("cncDashboard")->get('cnc_verifierDashboard_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getVerifierDashboard_web(?,?,?,?,?,?,?)";
            $data = DB::select($query,[0,ONLY_CNC_HUB,$fromDate,$toDate,NULL,NULL,NULL]);
            Cache::tags("cncDashboard")->put('cnc_verifierDashboard_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);
        
        }

        $result = $this->dashboard->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getShrinkageDashboard()
    {
        $data = Input::all();
        // Closing this as Shringkage default date is changed
        // // The Default "today" is one week for this PROC.
        if(isset($data['filter_date']) and
            ($data['filter_date'] == "today" or
                $data['filter_date'] == '')){
            // $fromDate = date('Y-m-d', strtotime('-7 days'));
            // The default must be 'mtd'.
            $data['filter_date'] = "mtd";
            $datesArr = $this->dashboard->getDateRange($data);
        }
        else
            $datesArr = $this->dashboard->getDateRange($data);

        $fromDate = $datesArr['fromDate'];
        $toDate = $datesArr['toDate'];
        
        $data = Cache::tags("cncDashboard")->get('cnc_shrinkageDashboard_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getcncShrinkageDashboard_web(?,?,?)";
            $data = DB::select($query,[NULL,$fromDate,$toDate]);
            Cache::tags("cncDashboard")->put('cnc_shrinkageDashboard_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }

        $result = $this->dashboard->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getCollectionsDashboard()
    {
        $data = Input::all();
        // Closing this as Collections default date is changed
        // // The Default "today" is one week for this PROC.
        if(isset($data['filter_date']) and
            ($data['filter_date'] == "today" or
                $data['filter_date'] == '')){
            // $fromDate = date('Y-m-d', strtotime('-7 days'));
            // The default must be 'mtd'.
            $data['filter_date'] = "mtd";
            $datesArr = $this->dashboard->getDateRange($data);
        }
        else
            $datesArr = $this->dashboard->getDateRange($data);

        $fromDate = $datesArr['fromDate'];
        $toDate = $datesArr['toDate'];

        $data = Cache::tags("cncDashboard")->get('cnc_collectionDashboard_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getCollectionDashboard_web(?,?,?,?)";
            $data = DB::select($query,[NULL,NULL,$fromDate,$toDate,ONLY_CNC_HUB]);
            Cache::tags("cncDashboard")->put('cnc_collectionDashboard_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }

        $result = $this->dashboard->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getInventoryDashboard()
    {
        $data = Cache::tags("cncDashboard")->get('cnc_inventoryDashboard_web',false);
        if(empty($data)){

            $query = "CALL getInventoryDashboard_web()";
            $data = DB::select($query);
            Cache::tags("cncDashboard")->put('cnc_inventoryDashboard_web', $data, CACHE_TIME);

        }

        $result = $this->dashboard->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getSelfOrdersPlaced(){

        $data = Input::all();
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];

        $data = Cache::tags("cncDashboard")->get('cnc_selfOrders_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getCnCSelfOrders(?,?)";
            $data = DB::select($query,[$fromDate,$toDate]);
            Cache::tags("cncDashboard")->put('cnc_selfOrders_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }

        $result = $this->dashboard->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }
    
    public function getVehiclesDashboard(){

        $data = Input::all();
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];

        $data = Cache::tags("cncDashboard")->get('cnc_getVehicleDashboard_web_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getCNCVehicleDashboard_web(?,?)";
            $data = DB::select($query,[$fromDate,$toDate]);
            Cache::tags("cncDashboard")->put('cnc_getVehicleDashboard_web_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);
        }

        $result = $this->dashboard->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getLogisticsDashboard(){

        $data = Input::all();
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];

        $data = Cache::tags("cncDashboard")->get('cnc_getLogisticsDashboard_web_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            $query = "CALL getLogisticsDashboard_web(?,?,?,?,?)";
            $data = DB::select($query,[NULL,NULL,$fromDate,$toDate,ONLY_CNC_HUB]);
            Cache::tags("cncDashboard")->put('cnc_getLogisticsDashboard_web_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }

        // Empty Check
        if(empty($data))
            return array("data" => [],"headers" => []);
        
        $result = $this->dashboard->transposeLogisticsDashboard($data);
        return array("data" => $result["data"],"headers" => $result["headers"]);
    }

}
