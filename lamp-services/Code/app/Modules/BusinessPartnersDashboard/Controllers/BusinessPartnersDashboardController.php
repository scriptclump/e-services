<?php
namespace App\Modules\BusinessPartnersDashboard\Controllers;
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
use App\Modules\Assets\Controllers\commonIgridController;
use App\Modules\Roles\Models\Role;
use App\Modules\Inventory\Models\Inventory;


class BusinessPartnersDashboardController extends BaseController {

    public function __construct() {

        $this->reports = new ReportsRepo();
        $this->roleAccess = new RoleRepo();
        $this->dashboard = new DashboardController();
        $this->objCommonGrid = new commonIgridController();
        $this->_inventory = new Inventory();
       //added middleware to get userid within constructor(4/11/2019)
         $this->middleware(function ($request, $next) {
             $this->userId = Session::get('userId');
             // All the code related to the session will come here
             return $next($request);
         });  
        // By Default its Zero!
        // Hub Id for CNC
        parent::Title('Ebutor - Business Partners Dashboard');
    }

    /**
    * The GET method for the Index Dashboard Grid
    */
    public function index() {
            // Code to Check weather the User has CNC Page Access or not
            $dashboard_data=Input::all();
             $checkBFILAccess = $this->roleAccess->checkPermissionByFeatureCode('BPD001');//getting dashboard feature access
             $ExportAccess = $this->roleAccess->checkPermissionByFeatureCode('EXE001');//getting excel export feature access
             if(!$checkBFILAccess)
             return Redirect::to('/');
             parent::Title('Ebutor - Business Partners Dashboard');
            // By default it will load for the Current Date
            $fromDate = date('Y-m-d');
            $toDate = $this->dashboard->tomorrow->format('Y-m-d');
            $getaccessdcfcAccess=$this->getBuidsByUserId($this->userId);
            $getaccessdcfcAccess=explode(',',  $getaccessdcfcAccess);
            $getaccessdcfcAccess=min( $getaccessdcfcAccess);// returning minimum of buid 
            $bu_id=   $getaccessdcfcAccess;
            $result = $this->getPartnersDashboard_web($this->userId,$fromDate,$toDate,$bu_id);
            return view('BusinessPartnersDashboard::index')
              ->with(["last_updated"=>date('Y-m-d h:i a'), "BusinessPartnerDatails"=>$result  ,"buid"=>$bu_id , "ExportAccess" => $ExportAccess , "BUID"=> $bu_id ]);
    }


    //used for date filter to get toand from date range
    public function getDateRange($inputDate='')
    {
      
        $this->tomorrow = new \DateTime('tomorrow');
        $this->yesterday = new \DateTime('yesterday');
        $fromDate = date('Y-m-d');
      //  $toDate = $this->tomorrow->format('Y-m-d');
        $toDate = date('Y-m-d');
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
                    $toDate = date('Y-m-d');
                 //   $toDate = $this->tomorrow->format('Y-m-d');
                    break;
                case 'yesterday':
                    $toDate = $fromDate = $this->yesterday->format('Y-m-d');
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
            // if($toDate == date("Y-m-d"))
            //     $toDate = $this->tomorrow->format("Y-m-d");
            // Log::info("fromDate ".$fromDate);
            // Log::info("toDate ".$toDate);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return ["fromDate" => $fromDate, "toDate" => $toDate];
        }
        return ["fromDate" => $fromDate, "toDate" => $toDate];
    }

    /**
    * The POST method for the Index Dashboard Grid
    */
    public function getIndexData(){
        try {
            $data = \Input::all();            
            $datesArr = $this->getDateRange($data);
            $fromDate = $datesArr["fromDate"];
            $toDate = $datesArr["toDate"];
            if(isset($data['buid']) && !empty($data['buid'])) { 
                $buid=$data['buid'];
            }else{
                $getaccessdcfcAccess=$this->getBuidsByUserId($this->userId);
                $getaccessdcfcAccess=explode(',',  $getaccessdcfcAccess);
                $getaccessdcfcAccess=min( $getaccessdcfcAccess);
                $buid =   $getaccessdcfcAccess;
            }
            $result["dashboard"] = $this->getPartnersDashboard_web($this->userId,$fromDate,$toDate,$buid);
            if(!empty($data))
            {
                return ['last_updated' => date('Y-m-d h:i a'),'BusinessPartnerDatails' => $result , 'BUID'=>$buid];
            }          
        
        } catch (Exception $e) {
            $result=array();
            return ['last_updated' => date('Y-m-d h:i a'),'BusinessPartnerDatails' => $result];
            //return "Sorry! Something went wrong. Please contact the Admin";
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
        }
    }
    
    /**
    * The Central Function for the Dashboard Data.
    * It acts like a Model for the Dashboard Procedure..,
    */
    public function getPartnersDashboard_web($userId = 0, $fromDate, $toDate,$buid){
         $response = DB::select(DB::raw("CALL getBusinessDashboardByBU_web('".$fromDate."','".$toDate."',".$buid.")"));
         $result = json_decode(json_encode($response),true);
         $data = json_decode($result[0]['Stockist_Dashboard'],true);
        return json_decode(json_encode($data));
    }

    //Used to show grip data
    public function getPartnersDashBoardGridData(Request $request,$userId = 0,$flag=1)
    {
        try{
            $data = Input::all();
            $datesArr = $this->getDateRange($data);
            $fromDate = $datesArr["fromDate"];
            $toDate = $datesArr["toDate"];
            //business unit 
            if(isset($data['buid']) && !empty($data['buid'])) { 
                $buid=$data['buid'];
            } else {
                $getaccessdcfcAccess=$this->getBuidsByUserId($this->userId);
                $getaccessdcfcAccess=explode(',',  $getaccessdcfcAccess);
                $getaccessdcfcAccess=min( $getaccessdcfcAccess);
                $buid =   $getaccessdcfcAccess;
            }

            $makeFinalSql = array();            
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }

            // make sql for FC name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Stockist_Name", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for  state name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("State", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
             // make sql for city name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("City", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for Total invoice amount
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Total_Invoiced", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for Total_Return
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Total_Returned", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            //Date filter 
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Date", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            //Dc name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Parent", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $orderBy = "";
            $orderBy = $request->input('%24orderby');
            if($orderBy==''){
                $orderBy = $request->input('$orderby');
            }

            // Arrange data for pagination
            $page="";
            $pageSize="";
            if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
                $page = $request->input('page');
                $pageSize = $request->input('pageSize');
            }

            if($orderBy!=''){
                $orderBy = ' ORDER BY ' . $orderBy;
            }

            $sqlWhrCls = '';
            $countLoop = 0;
            foreach ($makeFinalSql as $value) {
                if( $countLoop==0 ){
                    $sqlWhrCls .= ' AND ' . $value;
                }elseif(count($makeFinalSql)==$countLoop ){
                    $sqlWhrCls .= $value;
                }else{
                    $sqlWhrCls .= ' AND ' .$value;
                }
                $countLoop++;
            }

            $response = DB::select(DB::raw("CALL getPaymentLedgerData(2 , '".$fromDate."','".$toDate."',".$buid.")"));
            $result = json_decode(json_encode($response),true);
            return array("data" => $result);
            
        }catch(Exception $e) {
            $data=array();
            return array("data" => $data);
      }
    }
    
    // Used to get Dc/Fc Details based on userId
    public function getBuUnit(){
        $data =   $this->_inventory->getDcFCTreeData();
        $parentWiseArr = array();
            foreach (  $data  as $value) {
                $parentWiseArr[count($parentWiseArr)]="<option value='".$value->le_wh_id."' class='bu3' >".$value->display_name."</option>";
                 $parentWiseArr = array_unique($parentWiseArr);
             }
            return   $parentWiseArr;
    }

    //return business unit id based on user_id
    public function getBuidsByUserId($userid){
        try{
            $data =   $this->_inventory->getDcFCTreeData();
                foreach (  $data  as $value) {
                    $whIds = $value->le_wh_id;
                 }
             return  $whIds;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }   

    //used to get excel export data based on entered date filter
    public function getPartnersReport(){
        try{
            $data=Input::all();
            $fromdate=isset($data['fromdate'])?date('Y-m-d',strtotime($data['fromdate'])):date('Y-m-d');
            $todate=isset($data['todate'])?date('Y-m-d',strtotime($data['todate'])):date('Y-m-d');
            $file_name = 'Business_Partners_' .date('Y-m-d-H-i-s').'.csv';
            $BU_ID = isset($data['exportbuId'])?$data['exportbuId']:0;
            $query = "CALL  getPaymentLedgerData(2 ,'".$fromdate."','".$todate."',".$BU_ID.")"; 
           // echo '<pre>'; print_r($query); exit;
            $this->exportToCsv($query, $file_name);die;
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    //used to export filtered data into excel
    public function exportToCsv($query, $filename) {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', -1);
        $host = env('READ_DB_HOST');
        $port = env('DB_PORT');
        $dbname = env('DB_DATABASE');
        $uname = env('DB_USERNAME');
        $pwd = env('DB_PASSWORD');
        $filePath = public_path().'/uploads/reports/'.$filename;
        //echo $filePath;die;
        $sqlIssolation = 'SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;';
        $sqlCommit = 'COMMIT';
        $exportCommand = "mysql -h ".$host." -u ".$uname." -p'".$pwd."' ".$dbname." -e \"".$sqlIssolation.$query.';'.$sqlCommit.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$filePath;
       // echo '<pre>'; print_r($exportCommand); exit;
        system($exportCommand);
        
        header("Content-Type: application/force-download");
        header("Content-Disposition:  attachment; filename=\"" . $filename . "\";" );
        header("Content-Transfer-Encoding:  binary");
        header("Accept-Ranges: bytes");
        header('Content-Length: ' . filesize($filePath));
        
        $readFile = file($filePath);
        foreach($readFile as $val){
            echo $val;
        }
        exit;
    }
}
