<?php
/*
 */

namespace App\Modules\TaxReport\Controllers;
use App\Http\Controllers\BaseController;
use View;
use Log;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use App\Modules\TaxReport\Models\PaymentReceivedReport;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use PDF;
use App\Modules\Roles\Models\Role;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;
use App\Modules\InvDataMismatchReports\Controllers\ReportController;
use App\Modules\InvDataMismatchReports\Models\DataReportsModel;
use DB;
use Mail;
class PaymentReceivedReportController extends BaseController {

    public function __construct() {
       
        $this->_paymentreport = new PaymentReceivedReport();
        $this->_roleRepo = new RoleRepo();      
        $this->roleObj = new Role();   
        $this->objCommonGrid = new commonIgridController();


        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                $access = $this->_roleRepo->checkPermissionByFeatureCode('FFPAYR001');
                parent::Title('FC Payment Received Report');
          
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

     public function getPaymentReport(){
        try {
            $breadCrumbs = array('Home'=>'#','Financial Reports' => url('/'),'FC Payment Received Report' => '#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title(trans('taxReportLabels.index_page_title')." - ".trans('taxReportLabels.index_page_title'));
            $user_id = Session::get('userId');
            $warehouse=$this->_roleRepo->getAllDcs($user_id);
            //$filters = json_decode($Json['sbu'], 1);      
            //$warehouse=$this->roleObj->GetWareHouses($filters);
            //$warehouse = json_decode(json_encode($warehouse), True);
         return View('TaxReport::payment_received_report',['dcs' => $warehouse]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
     }
      public function downloadPaymentReceivedReport(){
         try{
            $flag ='';
            $filterData = Input::get();
            $filename='';
            if(!empty($filterData['warehouse'])){

                if(in_array(0, $filterData['warehouse'])){
                    $roleObj = new Role();
                    $user_id = Session::get('userId');
                    $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
                     $filters = json_decode($Json['sbu'], 1);            
                     $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
                     $warehouse=$dc_acess_list;
                }else{
                $warehouse=implode(',',$filterData['warehouse']);
                $warehouse=trim($warehouse,',');
                $warehouse=$warehouse;
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
            //$flag=$filterData['select_flags'];
            //$report_name_explode=explode('_',$flag);
            //$flag=$report_name_explode[1];
           // $filename=$report_name_explode[0];
            $details = json_decode(json_encode( $this->_paymentreport->getPaymentReportsData_Ffs($fromDate,$TDate,$warehouse)), true);
            Excel::create("PaymentReceivedDetailsReport".'- '. date('Y-m-d'),function($excel) use($details,$filename) {
                $excel->sheet("PaymentReceivedDetailsReport", function($sheet) use($details,$filename) {          
                $sheet->fromArray($details);
                });      
            })->export('csv');

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('taxReportLabels.errorInputData')));
        }
    }
    public function getPaymentDetails(Request $request){ 
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }

        if(count($request->input('$fillter')) > 0) {
            $fillter = $request->input('$fillter');
            $finalSearchField = $this->paymentDateconvertDateToQuery('andwarehouse', $fillter);
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pay_code", $filter, false);
        
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ledger_account", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("txn_reff_code", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Created_By", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Created_At", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("transaction_date", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("payment_type", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pay_amount", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Mode_Type", $filter, false);
        // if($fieldQuery!=''){
        //     $makeFinalSql[] = $fieldQuery;
        // }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("warehouse_name", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        if(!empty($finalSearchField)){
        $makeFinalSql[]=$finalSearchField;
         }
        $orderBy = $request->input('%24orderby');
        if($orderBy==''){
            $orderBy = $request->input('$orderby');
        }

        $page="";
        $pageSize="";
        if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
            $page = $request->input('page');
            $pageSize = $request->input('pageSize');
        }
      
        $result=$this->_paymentreport->stockistPaymentHistory($makeFinalSql, $orderBy, $page, $pageSize);

   return $result;

    }
   public function paymentDateconvertDateToQuery($fldName, $strDate){
       
        $breakByFld = explode($fldName,$strDate);
        if(isset($breakByFld[1])){
        $warehouse = $breakByFld[1];
        $explodewarehouse = explode('eq',$warehouse);
        if($explodewarehouse!='undefined'){
          $breakByFld[1]=str_replace('eq', '=',$breakByFld[1]);
          $warehouseaccess=explode('=', $breakByFld[1]);
          if($warehouseaccess[1]==0){
            $Json=json_decode($this->roleObj->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);      
            $filters=$filters['118001'];
            $breakByFld[1]=str_replace('0', '('.$filters.')', $breakByFld[1]);
            $breakByFld[1]=str_replace('=', 'in', $breakByFld[1]);
          }
          $finalString= $breakByFld[0].' and '.$breakByFld[1];
           
          $finalString=str_replace('Created_At', "date(Created_At)", $finalString);
        }else{
            $finalString='';
        }
    }else{
        $finalString='';
    }
        return $finalString;
    }
   
}