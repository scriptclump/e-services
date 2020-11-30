<?php

namespace App\Modules\AngularLogistics\Controllers;

use App\Http\Controllers\BaseController;

use Log;
use View;
use Session;
use Request;
use Redirect;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Modules\AngularLogistics\Models\AngularLogisticsModel;
use App\Modules\RoutingAdmin\Models\RouteDataModel;
use App\Central\Repositories\RoleRepo;


class AngularLogisticsController extends BaseController{

    public $_LogisticsModel;
    public $_RouteModel;
    public $_RoleAccess;
    public $_NodeApiUrl;

    function __construct()
    {
        $this->_LogisticsModel = new AngularLogisticsModel();
        $this->_RouteModel = new RouteDataModel();
        $this->_RoleAccess = new RoleRepo();
        $this->_NodeApiUrl = env('EBUTOR_NODE_URL');

        /* 
         * The Delivery Officer Role ID is 65...
         */
        //define("DO_ROLE_ID",65);
        /* 
         * The Delivery Executive Role ID is 57...
         */
        //define("DE_ROLE_ID",57);
        define("DE_ROLE_ID",'DELR002'); 
        /* 
         * The Picker Officer Role ID is 56...
         */
        //define("PICKER_ROLE",56);
        define("PICKER_ROLE",'PICKR002');

    }

    public function index()
    {
        $result['DC'] = $this->_RouteModel->getAllDC();
        $result['Hub'] = $this->_RouteModel->getAllHUB();
        //$result['DE'] = $this->_LogisticsModel->getAllOperationalOfficers([DE_ROLE_ID,DO_ROLE_ID]);
        //$result['Pickers'] = $this->_LogisticsModel->getAllOperationalOfficers(PICKER_ROLE);
        $result['DE'] = $this->_LogisticsModel->getAllOperationalOfficers(DE_ROLE_ID);
        $result['Pickers'] = $this->_LogisticsModel->getAllOperationalOfficers(PICKER_ROLE);
        
        /**
            Features to Access the Dashboard Graphs
        */
        $dcSummary = $this->_RoleAccess->checkPermissionByFeatureCode('DCSUM01');
        $hubSummary = $this->_RoleAccess->checkPermissionByFeatureCode('HUBSUM01');
        $pickingSummary = $this->_RoleAccess->checkPermissionByFeatureCode('PCKSUM01');
        $checkingSummary = $this->_RoleAccess->checkPermissionByFeatureCode('CHKSUM01');
        $deliverySummary = $this->_RoleAccess->checkPermissionByFeatureCode('DELSUM01');
        $logisticsSummary = $this->_RoleAccess->checkPermissionByFeatureCode('LOGSUM01');

        return View::make('AngularLogistics::index')
            ->with("data",$result)
            ->with("dcSummary",$dcSummary)
            ->with("hubSummary",$hubSummary)
            ->with("pickingSummary",$pickingSummary)
            ->with("checkingSummary",$checkingSummary)
            ->with("deliverySummary",$deliverySummary)
            ->with("logisticsSummary",$logisticsSummary)
            ->with("nopeApiUrl",$this->_NodeApiUrl);
    }

    /**
     * [getDcInfo description]
     * @return [type] [description]
     */
    public function getDcInfo()
    {
        $data = Input::all();
        $dc_id = isset($data['dc_id'])?$data['dc_id']:NULL;
        
        $result = $this->_LogisticsModel->getWareHouseInfoById($dc_id,'dc');
        return json_decode(json_encode($result),true);
    }

    /**
     * [getHubInfo description]
     * @return [type] [description]
     */
    public function getHubInfo()
    {
        $data = Input::all();
        $hub_id = isset($data['hub_id'])?$data['hub_id']:NULL;
        
        $result = $this->_LogisticsModel->getWareHouseInfoById($hub_id,'hub');
        return json_decode(json_encode($result),true);
    }

    public function reports()
    {
        $result = $this->apiData();

        return View::make('AngularLogistics::reports')
                    ->with('result',$result);
    }

    /**
     * Method to Call Logistics Procedure Code
     * @param  [int] $dc_id, [int] $hub_id, [date] $fromDate, [date] $toDate 
     * @return  JSON Array void
     */
    public function apiData()
    {
        $data = Input::all();
        /*
        * Dc and Hub Id`s are Optional...
        */
        $dcId = isset($data['dcId'])?$data['dcId']:NULL;
        $hubId = isset($data['hubId'])?$data['hubId']:NULL;
        $fromDate = isset($data['fromDate'])?$data['fromDate']:date("Y-m-d");
        $toDate = isset($data['toDate'])?$data['toDate']:date("Y-m-d");

        $result = $this->_LogisticsModel->getLogisticsData($fromDate,$toDate,$dcId,$hubId);
        if ($result) {
            return json_encode(array('status' => true, 'message' => $result));
        }else{
            return json_encode(array('status' => false, 'message' => 'No data found !!'));
        }
    }

    public function WorkingCapitalData(){

        $data=Input::all();

        $dcID=isset($data['working_dc'])?$data['working_dc']:NULL;

        $result = $this->_LogisticsModel->getWorkingCapitalData($dcID);

        return $result;


    }

    public function WorkingCapitalReport(){
        $salesAccessSummary = $this->_RoleAccess->checkPermissionByFeatureCode('SAL001');
        $DcLeadAccessSummary = $this->_RoleAccess->checkPermissionByFeatureCode('DCLEAD001');

        $checkAccess = $this->_RoleAccess->checkPermissionByFeatureCode('WC001');
            /*if(!$checkAccess){
                return Redirect::to('/');
            }
            else{*/

                $data=Input::all();
                $result['DC'] = $this->_RouteModel->getAllDC();
                $result['Hub'] = $this->_RouteModel->getAllHUB();
                $result['DO'] = $this->_LogisticsModel->getAllOperationalOfficers(DE_ROLE_ID);
                $result['Pickers'] = $this->_LogisticsModel->getAllOperationalOfficers(PICKER_ROLE);

                 return View::make('AngularLogistics::WorkingCapital')
                    ->with("data", $result)
                    ->with("checkAccess", $checkAccess)
                    ->with("salesAccessSummary", $salesAccessSummary)
                    ->with("DcLeadAccessSummary", $DcLeadAccessSummary);

           /* }*/
    }

    public function getdamageReport(){
        $data=Input::all();
        $dc_id = $data['dc_id'];
        $hub_id = $data['hub_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        if($dc_id='null'){
            $dc_id ='NULL';
        }
        if($hub_id='null' || $hub_id == 'undefined'){
            $hub_id ='NULL';
        }
        if($start_date == ''){
            $start_date = date('Y-m-d');
        }
        if($end_date == ''){
            $end_date = date('Y-m-d');
        }
        $getData = $this->_LogisticsModel->getDamageReportData($dc_id,$hub_id,$start_date,$end_date);
        
        return $getData;

    }

    

    public function getDncLeader(){
        $data=Input::all();
        $dc_id = $data['dc_id'];
        if($dc_id =='null'){
            $dc_id ='NULL';
        }
        $getData = $this->_LogisticsModel->getDnCLeaderdata($dc_id);
        
        return $getData;

    }

    public function hubops(){
        $result['DC'] = $this->_RouteModel->getAllDC();
        $result['Hub'] = $this->_RouteModel->getAllHUB();
        /*$result['DO'] = $this->_LogisticsModel->getAllOperationalOfficers(DO_ROLE_ID);*/
        //$result['DE'] = $this->_LogisticsModel->getAllOperationalOfficers([DE_ROLE_ID,DO_ROLE_ID]);
        $result['DE'] = $this->_LogisticsModel->getAllOperationalOfficers(DE_ROLE_ID);
        $result['Pickers'] = $this->_LogisticsModel->getAllOperationalOfficers(PICKER_ROLE);
        $result['Vehicle'] = $this->_LogisticsModel->getAllVehiclesData();
        
        /**
            Features to Access the Dashboard Graphs
        */

        $hubOpsSummary = $this->_RoleAccess->checkPermissionByFeatureCode('HUBOP01');

        return View::make('AngularLogistics::hubops')
            ->with("data",$result)
            ->with("hubOpsSummary",$hubOpsSummary)
            ->with("nopeApiUrl",$this->_NodeApiUrl);
    }

    public function getSalesData(){
        $data=Input::all();
        $dc_id = $data['dc_id'];
        if($dc_id=='null'){
            $dc_id ='NULL';
        }
        $getData = $this->_LogisticsModel->getSalesLeaderdata($dc_id);
        
        return $getData;

    }

    public function getDeliveryLeader(){
        $data=Input::all();
        $hub_id = $data['hub_id'];
        if($hub_id =='null'){
            $hub_id ='NULL';
        }
        $getData = $this->_LogisticsModel->getDeliveryLeaderdata($hub_id);
        
        return $getData;

    }

    public function getVehicleReport(){
        $data=Input::all();
        $dc_id = $data['dc_id'];
        $hub_id = $data['hub_id'];
        $vehicle_id = $data['vehicle_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        if($dc_id='null'){
            $dc_id ='NULL';
        }
        if($hub_id='null' || $hub_id == 'undefined'){
            $hub_id ='NULL';
        }
        if($vehicle_id='null' || $vehicle_id == 'undefined'){
            $vehicle_id ='NULL';
        }
        if($start_date == ''){
            $start_date = date('Y-m-d');
        }
        if($end_date == ''){
            $end_date = date('Y-m-d');
        }
        $getData = $this->_LogisticsModel->getVehicleReportData($dc_id,$hub_id,$vehicle_id,$start_date,$end_date);
        
        return $getData;

    }

    public function getPurchaseLeader(){
        $data=Input::all();
        $dc_id = $data['dc_id'];
        if($dc_id =='null'){
            $dc_id ='NULL';
        }
        $getData = $this->_LogisticsModel->getPurchaseLeaderdata($dc_id);
        
        return $getData;

    }

}
