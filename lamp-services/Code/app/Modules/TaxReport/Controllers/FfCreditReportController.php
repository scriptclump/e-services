<?php
/*
 */

namespace App\Modules\TaxReport\Controllers;
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);
use App\Http\Controllers\BaseController;
use View;
use Log;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use App\Modules\TaxReport\Models\FfCreditReport;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use PDF;
use App\Modules\Roles\Models\Role;

class FfCreditReportController extends BaseController {

    public function __construct() {
       
        $this->_ffcreditreport = new FfCreditReport();
        $this->_roleRepo = new RoleRepo();      
        $this->roleObj = new Role(); 

        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                $access = $this->_roleRepo->checkPermissionByFeatureCode('FFCR001');
                parent::Title('FC Credit Report');
         
                if (!$access) {
                    Redirect::to('/')->send();
                    die();
                }
                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

/*

 */

     public function getFFCreditReport(){
        try {
            $breadCrumbs = array('Home' => url('/'),'FC Credit Report' => '#', 'Dashboard' => '#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('taxReportLabels.index_page_title'));
            
            $Json=json_decode($this->roleObj->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);      
            $warehouse=$this->roleObj->GetWareHouses($filters);
            $warehouse = json_decode(json_encode($warehouse), True);
         return View('TaxReport::ffcredit_report',['dcs' => json_decode(json_encode($warehouse))]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
     }
      public function downloadFFCreditReport(){
         try{
            $flag ='';
            $filterData = Input::get();
            $filename='';
            if(!empty($filterData['warehouse'])){

                if(in_array(0, $filterData['warehouse'])){
                    $warehouse='NULL';
                    //$warehouse=json_decode($this->roleObj->getFilterData(6), 1);
                    //$warehouse = json_decode($Json['sbu'], 1); 
                }else{
                $warehouse=implode(',',$filterData['warehouse']);
                $warehouse=trim($warehouse,',');
                $warehouse="'".$warehouse."'";
               }
            
            }else{
              $warehouse='NULL';  
            }
            $fdate = (isset($filterData['fromdate']) && !empty($filterData['fromdate'])) ? $filterData['fromdate'] : date('Y-m').'-01';
            $fdate = str_replace('/', '-', $fdate);
            $fromDate=  date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['todate']) && !empty($filterData['todate'])) ? $filterData['todate'] : date('Y-m').'-01';
            $date = str_replace('/', '-', $tdate);
            $TDate=  date('Y-m-d', strtotime($date)); 
            $flag=$filterData['select_flags'];
            $report_name_explode=explode('_',$flag);
            $flag=$report_name_explode[1];
            $filename=$report_name_explode[0];
            $details = json_decode(json_encode( $this->_ffcreditreport->getCreditReportsData_Ffs($fromDate,$TDate,$warehouse,$flag)), true);
            Excel::create($filename.'- '. date('Y-m-d'),function($excel) use($details,$filename) {
                $excel->sheet($filename, function($sheet) use($details,$filename) {          
                $sheet->fromArray($details);
                });      
            })->export('csv');

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('taxReportLabels.errorInputData')));
        }
    }

    public function getBacthPrcsReport(){
        try {
            $breadCrumbs = array('Home' => url('/'),'Batch Process Report' => '#', 'Dashboard' => '#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('taxReportLabels.batch_process_heading'));
            
            $access = $this->_roleRepo->checkPermissionByFeatureCode('BTCH0001');
            if (!$access) {
                Redirect::to('/')->send();
                die();
            }
            
            $Json=json_decode($this->roleObj->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);      
            $warehouse=$this->roleObj->GetWareHouses($filters);
            $warehouse = json_decode(json_encode($warehouse), True);
            return View('TaxReport::batchProcessReport',['dcs' => json_decode(json_encode($warehouse))]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }


    public function downloadBatchReport(){
        try{
            $flag ='';
            $filterData = Input::get();
            $filename='';
            if(!empty($filterData['warehouse'])){

                if(in_array(0, $filterData['warehouse'])){
                    $warehouse='NULL';
                    //$warehouse=json_decode($this->roleObj->getFilterData(6), 1);
                    //$warehouse = json_decode($Json['sbu'], 1); 
                }else{
                $warehouse=implode(',',$filterData['warehouse']);
                $warehouse=trim($warehouse,',');
                $warehouse="'".$warehouse."'";
               }
            
            }else{
              $warehouse='NULL';  
            }
            $fdate = (isset($filterData['fromdate']) && !empty($filterData['fromdate'])) ? $filterData['fromdate'] : date('Y-m-d');
            $fdate = str_replace('/', '-', $fdate);
            $fromDate=  date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['todate']) && !empty($filterData['todate'])) ? $filterData['todate'] : date('Y-m-d');
            $date = str_replace('/', '-', $tdate);
            $TDate=  date('Y-m-d', strtotime($date));
            $flag=0;
            $filename="Batch Process Report";
            $details = json_decode(json_encode( $this->_ffcreditreport->getBatchProcessReportsData($fromDate,$TDate,$warehouse,$flag)), true);
            Excel::create($filename.'- '. date('Y-m-d'),function($excel) use($details,$filename) {
                $excel->sheet($filename, function($sheet) use($details,$filename) {          
                $sheet->fromArray($details);
                });      
            })->export('csv');

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('taxReportLabels.errorInputData')));
        }
    }

}
