<?php
namespace App\Modules\Dashboard\Controllers;

use DB;
use Log;
use View;
use Cache;
use Input;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\ReportsRepo;
use App\Central\Repositories\RoleRepo;
use Session;
use App\Modules\Roles\Models\Role;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use Redirect;

class DashboardController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->reports = new ReportsRepo();
        $this->roleAccess = new RoleRepo();
        $this->purchaseOrder    = new PurchaseOrder();
        // Date Objects of Tommorow and Date
        $this->tomorrow = new \DateTime('tomorrow');
        $this->yesterday = new \DateTime('yesterday');

        // Logged In User Legal Entity Id
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                     Redirect::to('/login')->send();
            }
            $this->legal_entity_id = \Session::get('legal_entity_id');
            // Cache Tag Name + le id
            define("CACHE_TAG","dncDashboard_"+$this->legal_entity_id);
        return $next($request);
        });

        // The Default Cache Time to set for 15 Minutes
        define("CACHE_TIME", 15);
        // [Except Cnc Hub] Flag to show all except CnC Hub
        define("EXCEPT_CNC_HUB", 1);
        // [Any Hub] Flag to show all Hubs or Selected Hub
        define("ANY_HUB", 2);
        // [Only Cnc Hub] Flag to show only CnC
        define("ONLY_CNC_HUB", 3);
    }

    public function indexAction($whid="") {
     try {
        
            $dashboardAccess = $this->roleAccess->checkPermissionByFeatureCode('DAS001');
            $myProfile = $this->roleAccess->checkPermissionByFeatureCode('MYP001');
            $myAttendence = $this->roleAccess->checkPermissionByFeatureCode('EA001');
            $changePassword = $this->roleAccess->checkPermissionByFeatureCode('CHPSS01');
            $globalAccess = $this->roleAccess->checkPermissionByFeatureCode('GLB0001');            
            Session::set("profileAccess",$myProfile);
            Session::set("attendenceAccess",$myAttendence);
            Session::set("changepassword",$changePassword);
            $user_id = Session::get('userId');
            $isSupplierCheck = $this->purchaseOrder->checkUserIsSupplier($user_id);
            if (count($isSupplierCheck)>0) {
                return View('Dashboard::index')->with([]);
            }
            $roleObj = new Role();
            $manufObj = json_decode($roleObj->getFilterData(11,$user_id), 1);
            $getaccessbuids=$this->roleAccess->getBuidsByUserId($user_id);
            $getaccessbuids=explode(',', $getaccessbuids);
            $getaccessbuids=min($getaccessbuids);
            if($getaccessbuids=='0' || $globalAccess){

                if(!Session::has('dashboardRedirect')){
                    Session::put('dashboardRedirect',1);
                    return Redirect::to('/stockist');
                }
                $buid=DB::table('business_units')
                      ->select('bu_id')
                      ->where('parent_bu_id',$getaccessbuids)
                      ->first();
                $bu_id=isset($buid->bu_id)?$buid->bu_id:1;
            }else{
                if(empty($getaccessbuids) || $getaccessbuids==''){
                    return View('Dashboard::index')->with([]); 
                }
                $bu_id=$getaccessbuids;
            }
            if($whid=='' &&  is_numeric($bu_id)){ 
                $data['bu_id']=$bu_id;
                $result = $this->getDashboardData($data);
            }elseif(is_numeric($whid)){
                $data['bu_id']=$whid;
                $bu_id=$whid;
                $result = $this->getDashboardData($data);    
            }else{
                $result=[];
                $bu_id='';
            } 
            //$result = $this->getDashboardData($data);
            
            $last_updated = isset($result['last_updated'])?$result['last_updated']:date('Y-m-d h:i a');
            unset($result['last_updated']);
                // Here we check Permissions to View the Dashboard
                $dcselect=$this->roleAccess->checkPermissionByFeatureCode('DNCDC001');
                $tabAccess['sales'] = $this->roleAccess->checkPermissionByFeatureCode('SALTAB01');
                $tabAccess['newCustomers'] = $this->roleAccess->checkPermissionByFeatureCode('NEWCUS01');
                $tabAccess['selfOrders'] = $this->roleAccess->checkPermissionByFeatureCode('SELORD01');
                $tabAccess['delivery'] = $this->roleAccess->checkPermissionByFeatureCode('DELTM01');
                $tabAccess['picking'] = $this->roleAccess->checkPermissionByFeatureCode('PICTM01');
                $tabAccess['verification'] = $this->roleAccess->checkPermissionByFeatureCode('VERTM01');
                $tabAccess['shrinkage'] = $this->roleAccess->checkPermissionByFeatureCode('SHRDAS01');
                $tabAccess['collections'] = $this->roleAccess->checkPermissionByFeatureCode('COLLDA01');
                $tabAccess['vehicles'] = $this->roleAccess->checkPermissionByFeatureCode('VEHDA01');
                $tabAccess['logistics'] = $this->roleAccess->checkPermissionByFeatureCode('LOGDA01');
                $tabAccess['inventory'] = $this->roleAccess->checkPermissionByFeatureCode('INVDAS');
                                return 
                    View('Dashboard::hello')->with([
                        'order_details' => $result,
                        'last_updated' => $last_updated,
                        'tab_access' => $tabAccess,
                        'dcdashboard'    =>$dcselect,
                        "manufacturer"=>$manufObj,
                        "buid"=>$bu_id
                    ]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            $manufObj['manufacturer ']=[];
            $dcselect=$this->roleAccess->checkPermissionByFeatureCode('DNCDC001');
            $last_updated = date('Y-m-d h:i a');
            return View('Dashboard::hello')->with([
                        'order_details' => [],
                        'last_updated' => $last_updated,
                        'dcdashboard'    =>$dcselect,
                        "manufacturer"=>$manufObj,
                        "buid"=>''
            ]);

            //return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }

    /**
    * Central Function for Date Ranges for all the Dashboard Methods
    * @param $inputDate [@json]
    *   -> filterData
    *   -> fromDate - dd/mm/yyyy Format
    *   -> toDate - dd/mm/yyyy Format
    * @return [fromDate='YYYY-MM-DD', toDate='YYYY-MM-DD']
    */
    public function getDateRange($inputDate='')
    {
        // $lastCall = debug_backtrace();
        // Log::info("lastCall");
        // Log::info($lastCall[1]['function']);
        // Log::info($inputDate);

        $fromDate = date('Y-m-d');
        $toDate = $this->tomorrow->format('Y-m-d');
        
        try {
            $switchOp = isset($inputDate['filter_date'])?strtolower($inputDate['filter_date']):"";
            switch($switchOp)
            {
                case 'wtd':
                    $currentWeekSunday = strtotime("last sunday");
                    $sunday = date('w', $currentWeekSunday)==date('w') ? $currentWeekSunday + 7*86400 : $currentWeekSunday;
                    $lastSunday = date("Y-m-d",$sunday);
                    $fromDate = $lastSunday;
                    break;
                case 'mtd':
                    $fromDate = date('Y-m-01');
                    break;
                case 'ytd': case 'quarter':
                    $fromDate = date('Y-01-01');
                    break;
                case 'today':
                    $fromDate = date('Y-m-d');
                    $toDate = $this->tomorrow->format('Y-m-d');
                    break;
                case 'yesterday':
                    $toDate = $fromDate = $this->yesterday->format('Y-m-d');
                    break;
                case 'last_month':
                    $fromDate = date("Y-m-1", strtotime("last month"));
                    $toDate = date("Y-m-t", strtotime("last month"));
                    break;
                case 'custom': default:
                    // Converting the Date format from "dd/mm/yyyy" -to- "yyyy-mm-dd";
                    if(isset($inputDate['fromDate']) and !empty($inputDate['fromDate'])){
                        $fromDateSubArr = explode('/', $inputDate['fromDate']);
                        $newFromDate = $fromDateSubArr[2]."-".$fromDateSubArr[1]."-".$fromDateSubArr[0];
                    }else
                        $newFromDate = date('Y-m-d');
                    
                    if(isset($inputDate['toDate']) and !empty($inputDate['toDate'])){
                        $toDateSubArr = explode('/', $inputDate['toDate']);
                        $newToDate = $toDateSubArr[2]."-".$toDateSubArr[1]."-".$toDateSubArr[0];
                    }else
                        $newToDate = $this->tomorrow->format("Y-m-d");

                    $fromDate = $newFromDate;
                    $toDate = $newToDate;
                    break;
            }
            // If the toDate is todate, then changing it to tomorrow
            if($toDate == date("Y-m-d"))
                $toDate = $this->tomorrow->format("Y-m-d");
            // Log::info("fromDate ".$fromDate);
            // Log::info("toDate ".$toDate);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return ["fromDate" => $fromDate, "toDate" => $toDate];
        }
        return ["fromDate" => $fromDate, "toDate" => $toDate];
    }

    public function getTodayFFUsersList()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';
        
        $ffInfo = Cache::tags(CACHE_TAG)->get('dnc_ffreport_'.'0_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
        if(empty($ffInfo)){

            // Updating this Line
            // Reason: to Remove the CnC Hub Data in the Current Dashboard 
            // as per the requirement of [@satish]
            // $ffInfo = DB::select('CALL getFFReport(0, "'.$fromDate.'", "'.$toDate.'")');
            // $ffInfo = DB::select('CALL getDnCFFReport(0, "'.$fromDate.'", "'.$toDate.'")');
            $ffInfo = DB::select(DB::raw('CALL getDynamicDnCFFReport(0, "'.$fromDate.'", "'.$toDate.'",'.$legal_entity_id.','.$wh_id.')'));
            Cache::tags(CACHE_TAG)->put('dnc_ffreport_'.'0_'.$fromDate.'_'.$toDate.'_'.$wh_id, $ffInfo, 5);
        
        }
        }else{
            $ffInfo=[];
        }
        $result = $this->igGridFormattedTableData($ffInfo);

        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getNewCustomersDashboard()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';

        $data = Cache::tags(CACHE_TAG)->get('newCustomersDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
        if(empty($data)){

            // Updating Proc to Hide CnC Hub Data in Main Dashboard [@satish]
            // $query = "CALL getLegalEntitiesExportData(?,?)";
            // $query = "CALL getDnCLegalEntitiesExportData(?,?)";
            $query = "CALL getDynamicDnCLegalEntitiesExport('".$fromDate."','".$toDate."',".$legal_entity_id.",".$wh_id.")";
            $data = DB::select(DB::raw($query));
            Cache::tags(CACHE_TAG)->put('newCustomersDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id, $data, CACHE_TIME);

        }
        }else{
            $data=[];
        }

        return array("data" => $data);
    }

    public function getDeliveryDashboard()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';

        $data = Cache::tags(CACHE_TAG)->get('deliveryDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
            if(empty($data)){

                // $query = 'CALL getDeliverDashboard_web(?,?,?,?,?,?,?)';
                $query = 'CALL getDynamicDeliverDashboard_web(0,'.EXCEPT_CNC_HUB.',"'.$fromDate.'","'.$toDate.'",'.$wh_id.',NULL,NULL,'.$legal_entity_id.')';
                $data = DB::select(DB::raw($query));
                Cache::tags(CACHE_TAG)->put('deliveryDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id, $data, CACHE_TIME);

            }
        }else{
            $data=[];
        }

        $result = $this->igGridFormattedTableData($data);

        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getPickersDashboard()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';


        $data = Cache::tags(CACHE_TAG)->get('pickerDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
            if(empty($data)){

                // $query = "CALL getPickerDashboard_web(?,?,?,?,?,?,?)";
                $query = "CALL getDynamicPickerDashboard_web(NULL,".EXCEPT_CNC_HUB.",'".$fromDate."','".$toDate."',".$wh_id.",NULL,NULL,".$legal_entity_id.")";
                $data = DB::select(DB::raw($query));
                Cache::tags(CACHE_TAG)->put('pickerDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id, $data, CACHE_TIME);

            }
        }else{
            $data=[];
        }
        
        $result = $this->igGridFormattedTableData($data);

        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getVerificationDashboard()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';

        $data = Cache::tags(CACHE_TAG)->get('verifierDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
            if(empty($data)){

                // $query = "CALL getVerifierDashboard_web(?,?,?,?,?,?,?)";
                $query = "CALL getDynamicVerifierDashboard_web(0,".EXCEPT_CNC_HUB.",'".$fromDate."','".$toDate."',".$wh_id.",NULL,NULL,".$legal_entity_id.")";
                $data = DB::select(DB::raw($query));
                Cache::tags(CACHE_TAG)->put('verifierDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id, $data, CACHE_TIME);
            
            }
        }else{
            $data=[];
        }

        $result = $this->igGridFormattedTableData($data);
        
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
            $datesArr = $this->getDateRange($data);
        }
        else
            $datesArr = $this->getDateRange($data);
        
        $fromDate = $datesArr['fromDate'];
        $toDate = $datesArr['toDate'];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';

        
        $data = Cache::tags(CACHE_TAG)->get('shrinkageDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
            if(empty($data)){
                // Updating Proc due to removal of CnC Hub data in Main Dashboard
                // $data = DB::select('CALL getShrinkageDashboard_web(NULL, "'.$fromDate.'", "'.$toDate.'")');
                // $query = 'CALL getdncShrinkageDashboard_web(?,?,?)';
                $query = 'CALL getDynamicShrinkageDashboard_web('.$wh_id.',"'.$fromDate.'","'.$toDate.'",'.$legal_entity_id.')';
                $data = DB::select(DB::raw($query));
                Cache::tags(CACHE_TAG)->put('shrinkageDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id, $data, CACHE_TIME);
            }
          }else{
            $data=[];
         }  

        $result = $this->igGridFormattedTableData($data);
        
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
            $datesArr = $this->getDateRange($data);
        }
        else
            $datesArr = $this->getDateRange($data);
        
        $fromDate = $datesArr['fromDate'];
        $toDate = $datesArr['toDate'];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';


        $data = Cache::tags(CACHE_TAG)->get('collectionDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
            if(empty($data)){

                // $query = "CALL getCollectionDashboard_web(?,?,?,?,?)";
                $query = "CALL getDynamicCollectionDashboard_web(".$wh_id.",NULL,'".$fromDate."','".$toDate."',".EXCEPT_CNC_HUB.",".$legal_entity_id.")";
                $data = DB::select(DB::raw($query));
                Cache::tags(CACHE_TAG)->put('collectionDashboard_'.$fromDate.'_'.$toDate.'_'.$wh_id, $data, CACHE_TIME);

            }
        }else{
            $data=[];
         }

        $result = $this->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getInventoryDashboard()
    {   
        /*$data = Input::all();
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);*/
        $data = Input::all();
        if(isset($data['filter_date']) and
            ($data['filter_date'] == "today" or
                $data['filter_date'] == '')){
            $data['filter_date'] = "mtd";
            $datesArr = $this->getDateRange($data);
        }
        else
            $datesArr = $this->getDateRange($data);
        
        $fromDate = $datesArr['fromDate'];
        $toDate = $datesArr['toDate'];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';
        $data = Cache::tags(CACHE_TAG)->get('inventoryDashboard_web_'.$wh_id,false);      
            if(empty($data)){
                // $data = DB::select('CALL getInventoryDashboard_web()');
                $data = DB::select(DB::raw('CALL getDynamicInventoryDashboard_web('.$wh_id.','.$legal_entity_id.')'));
                Cache::tags(CACHE_TAG)->put('inventoryDashboard_web_'.$wh_id, $data, CACHE_TIME);
            }
         }else{
            $data=[];
         }

        $result = $this->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getSelfOrdersPlaced()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
      $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';


        $data = Cache::tags(CACHE_TAG)->get('selfOrders_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
            if(empty($data)){
                // Updating this Proc, as to remove CnC Hub Data in Main Dashboard [@satish]
                // $query = "CALL getSelfOrders(?,?)";
                // $query = "CALL getDnCSelfOrders(?,?)";
                $query = "CALL getDynamicDnCSelfOrders('".$fromDate."','".$toDate."',".$legal_entity_id.",".$wh_id.")";
                Log::info('selforders query=='.$query);
                $data = DB::select(DB::raw($query));
                Cache::tags(CACHE_TAG)->put('selfOrders_'.$fromDate.'_'.$toDate.'_'.$wh_id, $data, CACHE_TIME);
            }
         }else{
            $data=[];
        }

        $result = $this->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }
    
    public function getVehiclesDashboard()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';

        $data = Cache::tags(CACHE_TAG)->get('getVehicleDashboard_web_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
            if(empty($data)){
                // Updated this Proc as to remove CnC Data in the main Dashboard
                // $data = DB::select('CALL getVehicleDashboard_web("'.$fromDate.'", "'.$toDate.'")');
                // $query = 'CALL getDNCVehicleDashboard_web(?,?)';
                $query = 'CALL getDynamicVehicleDashboard_web("'.$fromDate.'","'.$toDate.'",'.$legal_entity_id.','.$wh_id.')';
                $data = DB::select(DB::raw($query));
                Cache::tags(CACHE_TAG)->put('getVehicleDashboard_web_'.$fromDate.'_'.$toDate.'_'.$wh_id, $data, CACHE_TIME);
            }
        }else{
            $data=[];
        }

        $result = $this->igGridFormattedTableData($data);
        
        return array("data" => $result["data"], "headers" => $result["headers"]);
    }

    public function getLogisticsDashboard()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $buid = isset($data["buid"])?$data["buid"]:'';
        $whleid=$this->reports->getdcidbasedonbuid($buid);
        if(count($whleid)>0){
        $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
        $wh_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';

        // $data = DB::SELECT('CALL getLogisticsDashboard_web(NULL,NULL,"'.$fromDate.'", "'.$toDate.'")');
        // $query = "CALL getLogisticsDashboard_web(?,?,?,?,?)";
        $query = "CALL getDynamicLogisticsDashboard_web('".$wh_id."',NULL,'".$datesArr['fromDate']."','".$datesArr['toDate']."',".EXCEPT_CNC_HUB.",".$legal_entity_id.")";
        $data = DB::select(DB::raw($query));
        }else{
            $data=[];
        }        
        // Empty Check
        if(empty($data))
            return array("data" => [],"headers" => []);

        $result = $this->transposeLogisticsDashboard($data);
        return array("data" => $result["data"],"headers" => $result["headers"]);
    }

    public function transposeLogisticsDashboard($data)
    {
        $data = json_decode(json_encode($data),true);
        $headers = array_keys(json_decode(json_encode($data[0]),true));

        $finalArr = [];
        $index = 0;
        $igGridHeaders = ["Key Performance Indicator"];
        /**
        * The below Algorithm is a Transpose of the Data, what we Get... 
        * Written by [@arjun] arjun.kesava@gmail.com
        */
        foreach ($headers as $head)
        // 24 times (approx)
        {
            if($head == "DC")   continue;
            
            // A Temporary Array
            $tempArr = new \stdClass();
            $colIndex = 0; 

            // Adding the First Column in the Record
            $tempArr->{"Warehouse_00"} = $head;

            /**
            * This runs for every Warehouse
            */
            foreach ($data as $key => $value)
            //4 times
            {
                // As this needs to run 1 * 4 times... 
                if($head == "Hub"){
                    array_push($igGridHeaders,$data[$index]["Hub"]);
                    $index++;
                    continue;
                }
                // Setting Column Name for Each WareHouse
                $col = "Warehouse_0".($colIndex+1);

                $tempArr->{$col} = $value[$head];
                $colIndex++;
            }
            if($head == "Hub")  continue;

            // Pushing the Temporary Array in to the Final Array
            array_push($finalArr,$tempArr);
            unset($tempArr);
        }

        return ["data" => $finalArr, "headers" => $igGridHeaders];
    }
    
    public function getDashboardData($data =0)
    {
        $buid = isset($data['bu_id'])?$data['bu_id']:1;
        $brandid=isset($data['brandid'])?$data['brandid']:'NULL';
        $manufid=isset($data['manufid'])?$data['manufid']:'NULL';
        $productgrpid=isset($data['productgrpid'])?$data['productgrpid']:'NULL';
        $categoryid=isset($data['categoryid'])?$data['categoryid']:'NULL';
        $datesArr = $this->getDateRange($data);

        // Code to Check weather the User has TGM Access or not
        $checkTGMAccess =$this->roleAccess->checkPermissionByFeatureCode('USRTGM01');
        $flag = ($checkTGMAccess)?4:1;
        $result = $this->reports->getMyDashboardData(0, $datesArr["fromDate"], $datesArr["toDate"], $flag,$buid,$brandid,$manufid,$productgrpid,$categoryid);        
        return $result;
    }
    
    public function getIndexData() {
        try {
            $data = Input::all();
            $data['bu_id']=isset($data['buid'])?$data['buid']:1;
            $data['brandid']=(isset($data['brandid']) && !empty($data['brandid']))?$data['brandid']:'NULL';
            $data['manufid']=(isset($data['manufid']) && !empty($data['manufid']))?$data['manufid']:'NULL';
            $data['productgrpid']=(isset($data['productgrpid']) && !empty($data['productgrpid']))?$data['productgrpid']:'NULL';
            $data['categoryid']=(isset($data['categoryid']) && !empty($data['categoryid']))?$data['categoryid']:'NULL';
            if(!empty($data))
            {
                $result = $this->getDashboardData($data);
                $last_updated = isset($result['last_updated'])?$result['last_updated']:date('Y-m-d h:i a');
                unset($result['last_updated']);
                
                return json_encode(['order_details' => $result,'last_updated' => $last_updated]);
            }
            // If the Data is invalid, then it will be routed to GET method!
            return $this->indexAction();           
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something went wrong. Please contact the Admin or check the logs";
        }
    }

    /**
    * Method to format the Keys, of the Response arrays
    */
    public function igGridFormattedTableData($data)
    {
        // Code to format the Headers [Column Names]
        $headers = isset($data[0])?array_keys(json_decode(json_encode($data[0]),true)):"";
        $index=0;
        if(!empty($headers))
        foreach ($headers as $head) {
            $headers[$index++] = preg_replace('/\s+/', '_', $head);
        }

        /* Code to format the Headers of Data in each Column */
        $newArrObj = array();
        foreach ($data as $record) {
            $keys = array_keys(json_decode(json_encode($record),true));
            $newSubArr = array();
            foreach ($keys as $headName) {

                if(substr($headName, 0,2) == "1_"){
                    $record->$headName = floatval($record->$headName);
                }

                // Inserting elements in to new Array
                $newSubArr[preg_replace('/\s+/', '_', $headName)] = $record->$headName;
            }
            // Pushing them to the Final Array
            array_push($newArrObj, $newSubArr);
        }

        $formattedResponse["data"] = isset($newArrObj)?$newArrObj:[];
        $formattedResponse["headers"] = isset($headers)?$headers:"";

        return $formattedResponse;
    }

    public function getBrandsByManufacturerId(){
        try{
            $data=Input::all();
            return $this->roleAccess->getBrands($data);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something went wrong. Please contact the Admin or check the logs";
        }
    }

     public function getProductGroupByBrandId(){
        try{
            $data=Input::all();
            return $this->roleAccess->getProductGroupByBrand($data);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something went wrong. Please contact the Admin or check the logs";
        }
    }

}