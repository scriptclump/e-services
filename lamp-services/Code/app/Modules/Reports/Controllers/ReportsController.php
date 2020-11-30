<?php

namespace App\Modules\Reports\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Excel;
use Illuminate\Support\Facades\Config;
use App\Modules\Reports\Models\Reports;
use Carbon\Carbon;
use App\Central\Repositories\RoleRepo;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Orders\Controllers\OrdersGridController;


class ReportsController extends BaseController {

    public function __construct() {
        try {
            $this->_orders = new OrdersGridController();
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return \Redirect::to('/');
                }
                return $next($request);
            }); 
            parent::Title('Ebutor-FF Report');
            $breadCrumbs = array('Dashboard' => url('/'), 'FF Report' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $this->grid_field_db_match_grouprepo = array(
                'Name' => 'Name',                
            );
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {	
            $userId = Session::get('userId');
            $bu_id = $this->getaccessbuids($userId);
			$rolerepo = new RoleRepo();
			$approveAccess = $rolerepo->checkActionAccess($userId, 'FF0001');
			if(!$approveAccess)
			{
			return redirect()->to('/');
			}
            return View::make('Reports::index')->with(['bu_id'=>$bu_id]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getaccessbuids($userId){
                $getaccessbuids=$this->_orders->getBuidsByUserId($userId);
                $getaccessbuids=explode(',', $getaccessbuids);
                $getaccessbuids=min($getaccessbuids);
                if($getaccessbuids==0 && is_numeric($getaccessbuids)){
                        $buid=DB::table('business_units')
                              ->select('bu_id')
                              ->where('parent_bu_id',$getaccessbuids)
                              ->first();
                        $bu_id=isset($buid->bu_id) ? $buid->bu_id: '';
                }else{
                    $bu_id=$getaccessbuids;
                }
        return $bu_id;        
    }

    public function getledgerreport(){
        try{
            parent::Title('Payment Ledger Report');
            $breadCrumbs = array('Home' => url('/'), 'Finance Reports' => '', 'Payment Ledger Report' => url('getledgerreport'));
            parent::Breadcrumbs($breadCrumbs);
            $rolerepo = new RoleRepo();
            $checkAccess = $rolerepo->checkPermissionByFeatureCode('PAYMENTLDR001');
            if(!$checkAccess)
            {
                return redirect()->to('/');
            }
            $this->_inventory = new Inventory();
            //$bu_data=$this->_inventory->businessTreeData();
            $userId = Session::get('userId');
            $bu_data = $rolerepo->getAllDcs($userId);
            return View::make('Reports::ledger_report')->with('bu',$bu_data)->with('hasAccess',$checkAccess);
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getledgerpaymentreport(){
        try{
            $avgStockModel = new reports();
            $data=Input::all();
            $fromdate=isset($data['fromdate'])?date('Y-m-d',strtotime($data['fromdate'])):date('Y-m-d');
            $todate=isset($data['todate'])?date('Y-m-d',strtotime($data['todate'])):date('Y-m-d');
            $bc = isset($data['business_unit_id']) ? $data['business_unit_id'] : '';
            //print_r($bc);
            $file_name = 'Payments_Ledger_Report_' .date('Y-m-d-H-i-s').'.csv';
            $query = "CALL  getPaymentLedgerData(2,'".$fromdate."','".$todate."','".$bc."')"; 
            //echo '<pre>'; print_r($query); exit;
            $this->exportToCsv($query, $file_name);die;

        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getReports(Request $request) {
        try {
            $userId = Session::get('userId');
            $bu_id = $this->getaccessbuids($userId);
            $reports = new Reports();
            $result = $reports->getReports($bu_id,$request);                      
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function excelSalesReports(Request $request) {
        try {
            $userId = Session::get('userId');
            $bu_id = $this->getaccessbuids($userId);
            $reportDate = Carbon::now(); 			
            $excel_reports = new Reports();
            $report_excel = $excel_reports->excelReports($bu_id,$request);

            Excel::create('Reports_'.$reportDate, function($excel) use($report_excel) {                        
                /*$excel->sheet('reportsData', function($sheet) use($report_excel) {                        
                        $sheet->loadView('Reports::excel_reports')->with('Reportinfo', $report_excel);
                    });*/
                    $excel->sheet('reportsData', function($sheet) use($report_excel) {          
                $sheet->fromArray($report_excel);
                });
        })->export('xlsx');        
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getFFNames(Request $request) {
        try {	
            $term = $request->get('term');
            $buid = $request->get('buid');
            $reportObj = new Reports();
            $namesList = $reportObj->getAllNames($term,$buid);
            return $namesList;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function index(){
            parent::Title('FF Attendance - Ebutor');
            $breadCrumbs = array('Reports' => url('/'), 'FF Attendance' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $roleRepo = new RoleRepo();
            $hasAccess = $roleRepo->checkPermissionByFeatureCode('FFAAM001');
        return View::make('Reports::ffReport')->with('hasAccess',$hasAccess);
    }
    public function getAttendance(Request $request){
        $start_date = date('Y-m-d', strtotime($request->get('ffat_date_from')));
        $end_date = date('Y-m-d', strtotime($request->get('ffat_date_to')));
        $reports = new Reports();
        $procedure = $reports->getFFMonthlyAttReport($start_date,$end_date);
        $procedure2 = $reports->getFFMonthlyAttReportUser($start_date,$end_date);
        $mytime = Carbon::now();
        $headers =[];
        if(count($procedure2)){
            $headers = json_decode(json_encode($procedure2[0]),true);
        }
        Excel::create('FF Attendance Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers,$procedure,$procedure2,$start_date,$end_date) 
            {
                $excel->sheet("FF Attendance Sheet ", function($sheet) use($headers,$procedure,$procedure2,$start_date,$end_date)
                {
                    $sheet->loadView('Reports::monthlyAttReport')
                                        ->with('procedure', $procedure)
                                        ->with('headers', $headers)
                                        ->with('procedure2', $procedure2)
                                        ->with('start_date', $start_date)
                                        ->with('end_date', $end_date);  
                            });
            })->export('xlsx'); 
        //return View::make('Reports::monthlyAttReport')->with(['procedure'=>$procedure,"headers"=>$headers,"procedure2"=>$procedure2,"start_date"=>$start_date,"end_date"=>$end_date]);
    }

   public function indexAvgStock(){
            parent::Title('Average Stock Value Report');
            $breadCrumbs = array('Home' => url('/'), 'General Reports' => '', 'Average Stock Value  Report' => url('avgStock'));
            parent::Breadcrumbs($breadCrumbs);
            $roleRepo = new RoleRepo();
            $hasAccess = $roleRepo->checkPermissionByFeatureCode('DCFCAVS01');
            $this->_inventory = new Inventory();
            $bu_data=$this->_inventory->businessTreeData();
            return View::make('Reports::avgStockDetailsReport')->with('bu',$bu_data)
                                                               ->with('hasAccess',$hasAccess);
    }
    
    /*Function for getting the Avg stock report*/
    public function getAvgStock()
    {
        try
        {
            $bu_id=Input::get('bu_id');
            $roleRepo = new RoleRepo();
            $dashboardAccess = $roleRepo->checkPermissionByFeatureCode('DCFCAVS01');
            $avgStockModel = new reports();
            if($bu_id=='' || $dashboardAccess == '')
            {
               return redirect()->to('/');
            }
            else
            {
            $Dataset=$avgStockModel->getAvgStockData($bu_id);
            $DataSet = json_decode(json_encode($Dataset),true);
            Excel::create('avg_stock_details'.date('Y-m-d_H_i_s'), function($excel) use ($DataSet) {
                $excel->setTitle('avg_stock_details');
                $excel->sheet('Avg Stock Details', function($sheet) use ($DataSet) {
                    $sheet->fromArray($DataSet);
                });

            })->download('csv');

            }        


        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function odersTabGetBuUnit(){
        return $this->_Inventory->businessTreeData();
    }
    

    public function getSalesReport(){
        try{
            parent::Title('Sales Consolidate Report');
            $breadCrumbs = array('Home' => url('/'), 'General Reports' => '', 'Sales Consolidate  Summary Report' => url('avgStock'));
            parent::Breadcrumbs($breadCrumbs);
            $rolerepo = new RoleRepo();
            $checkAccess = $rolerepo->checkPermissionByFeatureCode('SALESREPORT001');
            if(!$checkAccess)
            {
                return redirect()->to('/');
            }
            return View::make('Reports::salesConsolidateReport');
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    } 


    public function getSalesConsolidateReport(){
        try{
            $avgStockModel = new reports();
            $data=Input::all();
            $fromdate=isset($data['fromdate'])?date('Y-m-d',strtotime($data['fromdate'])):date('Y-m-d');
            $todate=isset($data['todate'])?date('Y-m-d',strtotime($data['todate'])):date('Y-m-d');

            $file_name = 'Sales_Consolidate_Summary_Report_' .date('Y-m-d-H-i-s').'.csv';
            $query = "CALL  getSalesConsolidateReport('".$fromdate."','".$todate."')"; 
            $this->exportToCsv($query, $file_name);die;

        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
    *The function is used to get Profitability Points Report
    *@method POST
    *@param $fdate, $tdate & $le_wh_ids
    *@return Download a CSV report
    **/
    public function getProfitablityPointsReport(Request $request){
               
       try{
            $filterData = $request->input();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $type_id  = $filterData['type_id'];
            $bu_id  = $filterData['loc_dc_id'];
            $buNames = implode(',',$bu_id);
            $current_user_id = Session::get('userId');

            //access list of user
            $bu_access_list = DB::table('user_permssion')
                ->where(['user_id' => $current_user_id, 'permission_level_id' => 6])
                ->groupBy('object_id')
                ->pluck('object_id')->all();

            // if user has global access else specific user access level the bu are defined
            if (in_array(0, $bu_access_list)) {
                if ($buNames==0){
                    $buNames = 'NULL';
                    if($type_id == 1 || $type_id == 2)
                    {
                        $buNames = 1;
                    }
                }
            } else {
                // If user selected all, getting the list of bu are assigned to him for DC/FC/FF
                if ($buNames==0){
                    if($type_id == 1){
                        $query = "SELECT bu_id FROM legalentity_warehouses WHERE legal_entity_id IN(SELECT legal_entity_id FROM legal_entities WHERE legal_entity_type_id IN(1016)) and status=1  and dc_type IN(118001)";
                        $bu_ids = implode(",", $bu_access_list);
                        $query .=" and bu_id IN(".$bu_ids.")";
                        $results = DB::select(DB::raw($query));
                        $buNames = array();

                        foreach($results as $value) {
                            $buNames[] = $value->bu_id;
                        }
                        $buNames = implode(",", $buNames);
                    }
                    if($type_id == 2)
                    {
                        $query = "SELECT bu_id FROM legalentity_warehouses WHERE legal_entity_id IN(SELECT legal_entity_id FROM legal_entities WHERE legal_entity_type_id IN(1014)) and status=1  and dc_type IN(118001)";
                        $bu_ids = implode(",", $bu_access_list);
                        $query .=" and bu_id IN(".$bu_ids.")";
                        $results = DB::select(DB::raw($query));
                        $buNames = array();

                        foreach($results as $value) {
                            $buNames[] = $value->bu_id;
                        }
                        $buNames = implode(",", $buNames);
                    }
                    if($type_id == 3)
                    {
                        $results = DB::select(DB::raw("CALL getFfByUserAccess('".$current_user_id."')"));
                        $buNames = array();

                        foreach($results as $value) {
                            $buNames[] = $value->user_id;
                        }
                       $buNames = implode(",", $buNames);
                    }
                }                            
            }

            $file_name = 'Profitability_Points_Report_'.date('Y-m-d-H-i-s').'.csv';

            if($type_id == 1)
            {
                $query = "CALL getProfitabilityPointsReport('".$fdate."','".$tdate."', ".$buNames.", 1)";
            } else if($type_id == 2){
                $query = "CALL getProfitabilityPointsReport('".$fdate."','".$tdate."', ".$buNames.", 2)";                
            } else {
                $query = "CALL getProfitabilityPointsReport('".$fdate."','".$tdate."', ".$buNames.", 3)";
            }

            $db_host = env('DB_HOST');
            $this->exportToCsv($query, $file_name, $db_host);exit();

        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function exportToCsv($query, $filename, $host=null) {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', -1);
        $host = !empty($host) ? $host : env('READ_DB_HOST');
        $port = env('DB_PORT');
        $dbname = env('DB_DATABASE');
        $uname = env('DB_USERNAME');
        $pwd = env('DB_PASSWORD');
        $filePath = public_path().'/uploads/reports/'.$filename;
        //echo $filePath;die;
        $sqlIssolation = 'SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;';
        $sqlCommit = 'COMMIT';
        $exportCommand = "mysql -h ".$host." -u ".$uname." -p'".$pwd."' ".$dbname." -e \"".$sqlIssolation.$query.';'.$sqlCommit.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$filePath;
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
/*Invoice Report*/
    public function getInvoiceReport(){
        try{
            $data=Input::all();
            $fromdate=isset($data['fsinvoicedate'])?date('Y-m-d',strtotime($data['fsinvoicedate'])):date('Y-m-d');
            $todate=isset($data['toinvoicedate'])?date('Y-m-d',strtotime($data['toinvoicedate'])):date('Y-m-d');

            $file_name = 'Invocie_Consolidate_Report_' .date('Y-m-d-H-i-s').'.csv';
            $query = "CALL  getInvoiceConsSummReport('".$fromdate."','".$todate."')"; 
            $this->exportToCsv($query, $file_name);die;

        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
        //@returns TBV report view
     public function tbvReports() {
         try{
            parent::Title('Ebutor - Sales Report');
            $breadCrumbs = array('Home' => url('/'), 'Reports' => '','Sales Report' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $rolerepo = new RoleRepo();
            $checkAccess = $rolerepo->checkPermissionByFeatureCode('TBV001');
            if(!$checkAccess){
                return redirect()->to('/');
            }
            return View::make('Reports::total_bill_values')->with('hasAccess',$checkAccess);
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    //@Returns TBV export
      public function getTbvReport(){
        try{
            $tbvReports = new reports();
            $data=Input::all();
         
            $fromdate=isset($data['fromdate'])?date('Y-m-d',strtotime($data['fromdate'])):date('Y-m-d');
            $todate=isset($data['todate'])?date('Y-m-d',strtotime($data['todate'])):date('Y-m-d');
            $tbv = isset($data['tbv_id']) ? $data['tbv_id'] : '';
            $sale_type = isset($data['sale_id']) ? $data['sale_id'] : '';
            $bill_type = isset($data['bill_id']) ? $data['bill_id'] : '';
            $file_name = 'Sales_Report' .date('Y-m-d-H-i-s').'.csv';
            $query = "CALL  getTBVByType('".$fromdate."','".$todate."','".$sale_type."','".$tbv."','".$bill_type."')";
            //print_r($query);exit;

            $this->exportToCsv($query, $file_name);die;
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
