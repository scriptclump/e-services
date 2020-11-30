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

class FFTargetController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                     Redirect::to('/login')->send();
            }
            $this->reports = new ReportsRepo();
            $this->roleAccess = new RoleRepo();

            $this->tomorrow = new \DateTime('tomorrow');
            $this->yesterday = new \DateTime('yesterday');
            return $next($request);
        });
    }


    public function indexAction($whid="") {
		try {


            $dashboardAccess = $this->roleAccess->checkPermissionByFeatureCode('DAS001');

            $user_id = Session::get('user_id');
            $dcs = $this->roleAccess->getAllDcs($user_id);
            if($whid==""){
                $wh_id=isset($dcs[0]->le_wh_id)?$dcs[0]->le_wh_id:"";    
            }else {
                $wh_id=$whid;
            }
            if(count($dcs)==0 && $wh_id=="" || !$dashboardAccess){     
                return View('Dashboard::index')->with([]); 
            }   
            
            $data['wh_id']=$wh_id;
             
            $result = $this->getDashboardData($data);
            $result = array('dashboard'=>$result);

            $last_updated = isset($result['last_updated'])?$result['last_updated']:date('Y-m-d h:i a');
            unset($result['last_updated']);
            /*if(!empty($data)){
                return json_encode(['order_details' => $result,'last_updated' => $last_updated]);
            }else{*/
                // Here we check Permissions to View the Dashboard
                $dcselect=$this->roleAccess->checkPermissionByFeatureCode('DNCDC001');
                $tabAccess['sales'] = $this->roleAccess->checkPermissionByFeatureCode('SALTAB01');
                $whLegal = $this->reports->getlegalidbasedondcid($wh_id);
                $legalEntityId = isset($whLegal->legal_entity_id)?$whLegal->legal_entity_id:$this->legal_entity_id;
                $this->legal_entity_id = $legalEntityId;
                //Log::info('this legalentityid'.$this->legal_entity_id);
                return 
                    View('Dashboard::ffTarget')->with([
                        'order_details' => $result,
                        'last_updated' => $last_updated,
                        'tab_access' => $tabAccess,
                        'legalEntityId' => $legalEntityId,
                        'dcs'           => $dcs,
                        'whid'          => $wh_id,
                        'dcdashboard'    =>$dcselect
                    ]);



		} catch (\Exception $e) {
		    Log::info($e->getMessage());
		    Log::info($e->getTraceAsString());	
		}       

	}


    public function getIndexData() {
        try {
             $data = Input::all();
            $data['wh_id']=isset($data['wh_id'])?$data['wh_id']:"";                  
            if(!empty($data))
            {
                $result = $this->getDashboardData($data);
                $result = array('dashboard'=>$result);
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

    public function getDashboardData($data =0) {

        $dcid = isset($data['wh_id'])?$data['wh_id']:"";
        $legalid = Session::get('legal_entity_id');
        if(count($data)>1){
        $datesArr = $this->getDateRange($data);
        }
        $fromDate = (isset($datesArr['fromDate']) && $datesArr['fromDate']!='') ? $datesArr['fromDate'] : date('Y-m-01');
         $toDate = (isset($datesArr['fromDate']) && $datesArr['fromDate']!='') ? $datesArr['fromDate'] : date('Y-m-01');
         $time=strtotime($toDate);
         $month=date("m",$time);
         $year=date("Y",$time);
         $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
         $toDate =$year.'-'.$month.'-'.$days;
        
        $flag = isset($data['flag'])?$data['flag']:0;

        $user_id = isset($data['user_id'])?$data['user_id']:0;

        $result = $this->reports->getSalesTargetData($fromDate, $toDate, $flag, $dcid,$user_id);        
        return $result;
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
        if(isset($inputDate['fromDate']) && !empty($inputDate['fromDate'])){
         $fromDate=explode('/',$inputDate['fromDate']);   
         $fromDate = $fromDate[0].'/'.'01'.'/'.$fromDate[1];
         $fromDate= strtotime($fromDate);
         $fromDate = date("Y-m-d",$fromDate);
             if($inputDate['filter_date']=='custom'){
            
             $data=explode('/',$inputDate['fromDate']);   
             $inputDate['fromDate'] = '01'.'/'.$data[0].'/'.$data[1];
            }

        }else{ 
        $fromDate = date('Y-m-d');
        }

        $toDate = $this->tomorrow->format('Y-m-d');

            if(isset($inputDate['filter_date']) && $inputDate['filter_date']=='custom'){
            
             $data=explode('/',$inputDate['fromDate']);   

             $toDate = $inputDate['fromDate'];
             $time=strtotime($toDate);
             $month=date("m",$time);
             $year=date("Y",$time);
             $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

             $inputDate['toDate'] = $days.'/'.$data[0].'/'.$data[1];
            }
        
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

    public function getSalesTarget()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        if(isset($datesArr['fromDate'])){
         $fromDate = $datesArr['fromDate'];
         $time=strtotime($fromDate);
         $month=date("m",$time);
         $year=date("Y",$time);
         $fromDate =$year.'-'.$month.'-'.'01';
        }else{
        $fromDate = date('Y-m-01');
        }

        if(isset($datesArr['toDate'])){
         $toDate = $datesArr['fromDate'];
         $time=strtotime($toDate);
         $month=date("m",$time);
         $year=date("Y",$time);
         $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
         $toDate =$year.'-'.$month.'-'.$days;
        }else{
        $toDate = date('Y-m-d');    
        }
        $wh_id = isset($data["wh_id"])?$data["wh_id"]:"";

        //TODO Cache
        /*$ffInfo = Cache::tags(CACHE_TAG)->get('dnc_sales_target_'.'0_'.$fromDate.'_'.$toDate.'_'.$wh_id,false);
        if(empty($ffInfo)){

            // Updating this Line
            // Reason: to Remove the CnC Hub Data in the Current Dashboard 
            // as per the requirement of [@satish]
            // $ffInfo = DB::select('CALL getFFReport(0, "'.$fromDate.'", "'.$toDate.'")');
            // $ffInfo = DB::select('CALL getDnCFFReport(0, "'.$fromDate.'", "'.$toDate.'")');
            $salesTarget = DB::selectFromWriteConnection(DB::raw('CALL getDynamicFFTGTDashboard_grid("'.$wh_id.'", "'.$fromDate.'", "'.$toDate.'",1)'));
            Cache::tags(CACHE_TAG)->put('dnc_sales_target_'.'0_'.$fromDate.'_'.$toDate.'_'.$wh_id, $salesTarget, 5);
        
        }*/
        //echo 'CALL getDynamicFFTGTDashboard_grid("'.$wh_id.'", "'.$fromDate.'", "'.$toDate.'",1,0)';exit;
        $salesTarget=array();
        $salesTarget['data'] = DB::selectFromWriteConnection(DB::raw('CALL getDynamicFFTGTDashboard_grid("'.$wh_id.'", "'.$fromDate.'", "'.$toDate.'",1,0)'));
        //print_r($salesTarget);exit;
        if(isset($salesTarget['data'][0])){
        foreach ($salesTarget['data'][0] as $key => $value) {
            $salesTarget['headers'][]=$key;
        }//echo '<pre/>';print_r($salesTarget); exit;
        }else{
            $salesTarget['headers'][]='';
        }
        return json_encode($salesTarget);
    }

}   