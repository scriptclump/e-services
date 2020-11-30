<?php

/*
 * Filename: OrdersController.php
 * Description: This file is used for manage sales orders
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 23 June 2016
 * Modified date: 23 June 2016
 */

/*
 * OrdersController is used to manage orders
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\Orders\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Log;
use DB;
use Auth;
use Response;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Input;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\Shipment;
use App\Modules\Orders\Models\Refund;
use App\Modules\Orders\Models\ReturnModel;

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;
use App\Modules\Roles\Models\Role;

class ReportController extends BaseController {

	protected $_orderModel;
	protected $_masterLookup;
	protected $_commentTypeArr;
	protected $_invoiceModel;
	protected $_shipmentModel;
	protected $_roleRepo;
	protected $_sms;
	protected $_refund;
	protected $_leModel;
	protected $_filterStatus;
	protected $_returnModel;
    protected $_roleModel;

    public function __construct() {
		$this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });
		$this->_orderModel = new OrderModel();
		$this->_masterLookup = new MasterLookup();
		$this->_invoiceModel = new Invoice();
		$this->_shipmentModel = new Shipment();
		$this->_roleRepo = new RoleRepo();
		$this->_sms = new dmapiOrders();
		$this->_refund = new Refund();
		$this->_leModel = new LegalEntity();
		$this->_returnModel = new ReturnModel();
        $this->_roleModel=new Role();

	       
    }
	
	/*
	 * downloadOrders() method is used to Download All Orders
	 * @param NULL
	 * @return Excell
	 */
    public function downloadOrders(Request $request) {
        try {
            //ini_set('memory_limit', '256M');
            $filterData = $request->input();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-0';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $file_name = 'orders_invoice_report_' .date('Y-m-d-H-i-s').'.csv';
            $dc_id  = $filterData['loc_dc_id'];
            $query = "CALL getOrderInvoiceReport(0,0,'$fdate','$tdate','$dc_id')";
            // echo $query;die;        
            $this->exportToCsv($query, $file_name);die;            
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    
    public function DownloadOrderDetails(Request $request) {
        try {
            $filterData = $request->input();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $file_name = 'orders_detail_report_'.date('d-m-Y-H-i-s').'.csv';
            $dc_id  = $filterData['loc_dc_id'];
            $query = "CALL getOrderDetailReport(0,0,'$fdate','$tdate','$dc_id')";
            $this->exportToCsv($query, $file_name);die;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }


    public function DownloadConsolidateOrders(Request $request) {
        try {
            $userId = session::get('userId');
            $filterData = $request->input();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $dc_id  = $filterData['loc_dc_id'];
            $report_id  = $filterData['report_id'];
            $dcNames = implode(',',$dc_id);
            if ($dcNames==0){
                $dcNames = 'NULL';
            }else{
                $dcNames =  "'".$dcNames."'";
            }

            if($report_id == 6){
                $file_name = 'orders_consolidated_report'.date('Y-m-d').'.csv';

                Utility::query_db_results_to_file('getOrderConsolidateReport1',$fdate,$tdate, $dcNames,$userId, 50000, 0, $file_name);exit();
            }

            if($report_id == 1){
                $file_name = 'orders_consolidated_report'.date('Y-m-d').'.csv';
                $query = "CALL getOrderConsolidateReport(0,0,'$fdate','$tdate',$dcNames,$userId)";
            }elseif($report_id == 2){
//                log::info("request handle by laravel 5.2");
                $file_name = 'invoice_consolidated_report'.date('Y-m-d').'.csv';
                $query = "CALL getInvoicedConsolidateReport('$fdate','$tdate',$dcNames)";
            }elseif($report_id == 3){
                $file_name = 'cancelled_consolidated_report'.date('Y-m-d').'.csv';
                $query = "CALL getCanceledConsolidateReport('$fdate','$tdate',$dcNames)";
            }
            elseif($report_id == 4){
                $file_name = 'returned_consolidated_report'.date('Y-m-d').'.csv';
                $query = "CALL getReturnedConsolidateReport('$fdate','$tdate',$dcNames)";
            }else{
                $file_name = 'delivered_consolidated_report'.date('Y-m-d').'.csv';
                $query = "CALL getDeliveredConsolidateReport('$fdate','$tdate',$dcNames)";
            }
            $this->exportToCsvThroughReporting($query, $file_name);die;

           /* $query = DB::selectFromWriteConnection(DB::raw("CALL getOrderConsolidateReport(0,0,'$fdate','$tdate',$dcNames)"));
            $query = json_decode(json_encode($query), true);
            foreach($query as $key=>$val){
                foreach($val as $key1=>$val1){
                    if($key1=='ESP' || $key1=='MRP' || $key1=='Base Price' || $key1=='Tax Value' || $key1=='Tax%' || $key1=='Order ESU Qty' || 
                        $key1=='Order Total(SKUs)' || $key1=='Invoice Total(SKU)' || $key1=='Cancelled Total' || $key1=='Returns Total' || $key1=='Line Status'){
                        $query[$key][$key1]=(float)($val1);
                    }
           }
           }
            $result = $this->makeExcelFile($file_name,$query,$file_name);die();*/
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    
    public function generateDsrAction(Request $request) {
        try {
            $idsArr = $request->all('ids');
            $orderIds = $idsArr['ids'];
            //$orderIds = implode(',', $filterData);
            $file_name = 'dsr_report_'.date('d-m-Y-H-i-s').'.csv';
            $query = "CALL getDsrReports('$orderIds')";
            $this->exportToCsv($query, $file_name);die;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }

    public function DownloadReturnReport(Request $request){
       
        $filterData = $request->input();
        $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
        $fdate = date('Y-m-d', strtotime($fdate));
        $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
        $tdate = date('Y-m-d', strtotime($tdate));
        $file_name = 'sales_return_report'.date('Y-m-d').'.csv';
        $dc_id  = $filterData['loc_dc_id'];
        $dcNames = implode(',',$dc_id);
            if ($dcNames==0){
                $dcNames = 'NULL';
            }else{
                $dcNames =  "'".$dcNames."'";
            }
        $query = "CALL getSalesReturnReports('$fdate','$tdate',$dcNames)";
        $this->exportToCsv($query,$file_name);die;

        /*$query = DB::selectFromWriteConnection(DB::raw("CALL getSalesReturnReports('$fdate','$tdate',$dcNames)"));


            $query = json_decode(json_encode($query), true);
            foreach($query as $key=>$val){
                foreach($val as $key1=>$val1){
                    if($key1=='Order Value' || $key1=='mrp' || $key1=='Invoice Value' || $key1=='Return Value' || $key1=='Missing Value'){
                        $query[$key][$key1]=(float)($val1);
                    }
           }
           }

            $result = $this->makeExcelFile($file_name,$query,$file_name);die();*/
    }

    public function exportToCsv($query, $filename) {
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
    public function exportToCsvThroughReporting($query, $filename) {
        $host = env('READ_DB_HOST');
        $port = env('DB_PORT');
        $dbname = env('REPORT_DATABASE');
        $uname = env('REPORT_USERNAME');
        $pwd = env('REPORT_PASSWORD');
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
    
    public function salesVouchersReportAction(Request $request) {
        try{

                $filterData = $request->input();
                $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
                $fdate = date('Y-m-d', strtotime($fdate));
                $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
                $tdate = date('Y-m-d', strtotime($tdate));
                $file_name = 'sales_vouchers_report_'.$fdate.'_to_'.$tdate.'.csv';
                $dc_id  = $filterData['loc_dc_id'];
                $fields = array('orders.order_code',
                                'orders.order_date',
                                'orders.total as order_value',
                                'grid.created_at as invoice_date',
                                'le.business_legal_name',
                                'le.le_code',
                                'tax.tax',
                                DB::raw('SUM(products.tax_amount) as taxSum'),
                                DB::raw('SUM(products.row_total) as saleTotal'),
                                'grid.invoice_code',
                                'grid.grand_total');

                $query = DB::table('gds_orders as orders')->select($fields);
                $query->join('gds_invoice_grid as grid','grid.gds_order_id','=','orders.gds_order_id');
                $query->join('gds_order_invoice as invoice','invoice.gds_invoice_grid_id','=','grid.gds_invoice_grid_id');
                $query->join('gds_invoice_items as products','invoice.gds_order_invoice_id','=','products.gds_order_invoice_id');
                $query->leftjoin('legal_entities as le','le.legal_entity_id','=','orders.cust_le_id');

                $query->join('gds_order_products as gdsprod', function($join)
                {
                    $join->on('gdsprod.product_id','=','products.product_id');
                    $join->on('gdsprod.gds_order_id','=','orders.gds_order_id');
                });
                
                $query->leftJoin('gds_orders_tax as tax','tax.gds_order_prod_id','=','gdsprod.gds_order_prod_id');
                
                $query->groupBy('tax.tax');
                $query->groupBy('orders.gds_order_id');
                $query->where('grid.created_at', '>=', $fdate.' 00:00:00');
                $query->where('grid.created_at', '<=', $tdate.' 23:59:59');
                $query->where('orders.le_wh_id','=',$dc_id);
                $resArr = $query->get()->all();           
                
                #echo '<pre>';print_r($resArr);
                
                if(count($resArr)) {
                    foreach($resArr as $res) {

                      $roundoff = Utility::getRoundOff($res->grand_total, 'roundoff'); 

                      if(!isset($orderDataXlsArr[$res->order_code])){
                        $orderDataXlsArr[$res->order_code] = array(
                                'retailer_name'=>$res->business_legal_name.' - '.$res->le_code, 
                                'invoice_code'=>$res->invoice_code, 
                                'invoice_date'=>$res->invoice_date, 
                                'order_code'=>$res->order_code, 
                                'order_date'=>$res->order_date, 
                                'order_value'=>$res->order_value, 
                                'invoice_value'=>round($res->grand_total));
                      }
                      
                                                
                        $tax = (float)$res->tax;
                        if($tax == '5') {
                            $orderDataXlsArr[$res->order_code]['sales_5'] = $res->saleTotal;
                            $orderDataXlsArr[$res->order_code]['OutputVat5'] = $res->taxSum;
                            if(!isset($orderDataXlsArr[$res->order_code]['sales_14_5'])){
                                $orderDataXlsArr[$res->order_code]['sales_14_5'] = '';
                            }
                            if(!isset($orderDataXlsArr[$res->order_code]['OutputVat14_5'])){
                                 $orderDataXlsArr[$res->order_code]['OutputVat14_5'] = '';
                            }
                        }
                        else if($tax == '14.5') {
                            if(!isset($orderDataXlsArr[$res->order_code]['sales_5'])){
                                $orderDataXlsArr[$res->order_code]['sales_5'] = '';
                            }
                            
                            if(!isset( $orderDataXlsArr[$res->order_code]['OutputVat5'])){
                                $orderDataXlsArr[$res->order_code]['OutputVat5'] = '';   
                            }
                            $orderDataXlsArr[$res->order_code]['sales_14_5'] = $res->saleTotal;
                            $orderDataXlsArr[$res->order_code]['OutputVat14_5'] = $res->taxSum; 
                        }
                        else {

                            $orderDataXlsArr[$res->order_code]['sales_5'] = '';
                            $orderDataXlsArr[$res->order_code]['OutputVat5'] = '';
                            $orderDataXlsArr[$res->order_code]['sales_14_5'] = '';
                            $orderDataXlsArr[$res->order_code]['OutputVat14_5'] = '';
                        }
                        $orderDataXlsArr[$res->order_code]['Roundoff'] = $roundoff;
                    }
                   
                    #echo '<pre>';print_r($orderDataXlsArr);die;

                    $csvHeaders = array(
                            'Retailer Name', 
                            'Invoice No', 
                            'Invoice Date', 
                            'Order No',
                            'Order Date', 
                            'Order Value', 
                            'Invoice Value',
                            'Sales @ 5% Basic Value',
                            'Output Vat 5%',
                            'Sales @ 14.5% Basic Value',
                            'Output Vat 14.5%',
                            'Roundoff');

                    $this->downloadCsv($csvHeaders, $orderDataXlsArr, $file_name);

                    die;
                }

        }
        catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function salesSummaryReportAction(Request $request) {
        try{
            DB::enablequerylog();
            $filterData = $request->input();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $file_name = 'sales_summary_report_'.$fdate.'_to_'.$tdate.'.csv';

            //echo $fdate." || ".$tdate."<br>";
            $dc_id  = $filterData['loc_dc_id'];
            $query = DB::selectFromWriteConnection(DB::raw("CALL getSalesSummaryReport('$fdate', '$tdate','$dc_id')"));
            
            /*echo "<pre>";
            $query = DB::getQueryLog();
            print_r(end($query));*/

            $query = json_decode(json_encode($query), true);

            /*if(!empty($query)){
                $header = array_keys($query[0]);
            }

            $final = array_merge($header,$query);*/

            //echo "<pre>"; print_r($query); exit;
            
            $result = $this->makeExcelFile($file_name,$query);

            return $result;
        }
        catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return $e->getMessage();
        }
    }

    public function locReportAction(Request $request){
         try{
            DB::enablequerylog();
            $filterData = $request->input();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $dc_id  = $filterData['loc_dc_id'];
            $file_name = 'loc_report_'.$fdate.'_to_'.$tdate.'.csv';
            $query = "SELECT * FROM vw_loc_order_report WHERE Dc_ID IN($dc_id) AND `Order Date` BETWEEN '$fdate' AND '$tdate'";
            $query = DB::select(DB::raw($query));
            $query = json_decode(json_encode($query), true);          
            $result = $this->makeExcelFile($file_name,$query);
            return $result;
        }
        catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return $e->getMessage();
        }
    }

    public function downloadCsv($csvHeaders, $csvData, $filename) {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename='.$filename.'');
         
        // do not cache the file
        header('Pragma: no-cache');
        header('Expires: 0');
         
        // create a file pointer connected to the output stream
        $file = fopen('php://output', 'w');
         
        // send the column headers
        fputcsv($file, $csvHeaders);
         
        // output each row of the data
        foreach ($csvData as $row)
        {
            fputcsv($file, $row);
        }
         
        exit();
    }

    public function makeExcelFile($fileName, $results,$sheet='DiMiCiReport',$ext='xls') {
        try {
            //echo "Total Rows: ".count($results)."\n"; //exit;
            //Log::info("Total Rows: ".count($results));
            Excel::create($fileName, function($excel) use($results,$sheet) {
                $excel->sheet($sheet, function($sheet) use($results) {
                    $sheet->fromArray($results);
                });
            })->export('xls');
            return public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".".$ext;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex;
        }
    }
    public function orderSummaryAction(Request $request){
         try{
            $filterData = $request->input();
            $isActive= (int)isset($filterData['is_active']);
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $dc_id  = $filterData['loc_dc_id'];
            $file_name = 'Order_Summary_'.$fdate.'_to_'.$tdate;
            $query = DB::selectFromWriteConnection(DB::raw("CALL getOpenOrdersByPidByDC(".$dc_id.",'".$fdate."','".$tdate."',$isActive)")); 
            $query = json_decode(json_encode($query), true); 
            $result = $this->makeExcelFile($file_name,$query,'Order Summary');
            return $result;
        }
        catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return $e->getMessage();
        }
    }
    public function dcFCSalesReportDownload(Request $request){
        $filterData = $request->input();
        $fdate = (isset($filterData['dcfc_fdate']) && !empty($filterData['dcfc_fdate'])) ? $filterData['dcfc_fdate'] : date('Y-m').'-01';
        $fdate = date('Y-m-d', strtotime($fdate));
        $tdate = (isset($filterData['dcfc_tdate']) && !empty($filterData['dcfc_tdate'])) ? $filterData['dcfc_tdate'] : date('Y-m-d');
        $tdate = date('Y-m-d', strtotime($tdate));
        $report_excel = $this->_returnModel->excelReports($fdate,$tdate);
        Excel::create('Reports_'.date('Y-m-d H:i:s'), function($excel) use($report_excel) {                        
            $excel->sheet('reportsData', function($sheet) use($report_excel) {          
            $sheet->fromArray($report_excel);
            });
        })->export('xls'); 
    }
        public function retailerSalesDownloadOrder(Request $request){
        $filterData = $request->input();
        $fdate = (isset($filterData['retailer_fdate']) && !empty($filterData['retailer_fdate'])) ? $filterData['retailer_fdate'] : date('Y-m').'-01';
        $fdate = date('Y-m-d', strtotime($fdate));
        $tdate = (isset($filterData['dcfc_tdate']) && !empty($filterData['dcfc_tdate'])) ? $filterData['dcfc_tdate'] : date('Y-m-d');
        $tdate = date('Y-m-d', strtotime($tdate));
        $report_excel = $this->_returnModel->retailerExcelReports($fdate,$tdate);
        Excel::create('Reports_'.date('Y-m-d H:i:s'), function($excel) use($report_excel) {                        
            $excel->sheet('reportsData', function($sheet) use($report_excel) {          
            $sheet->fromArray($report_excel);
            });
        })->export('xls'); 
    }
        public function apobSalesDownload(Request $request){
        $filterData = $request->input();
        $fdate = (isset($filterData['dcfc_fdate']) && !empty($filterData['dcfc_fdate'])) ? $filterData['dcfc_fdate'] : date('Y-m').'-01';
        $fdate = date('Y-m-d', strtotime($fdate));
        $tdate = (isset($filterData['dcfc_tdate']) && !empty($filterData['dcfc_tdate'])) ? $filterData['dcfc_tdate'] : date('Y-m-d');
        $tdate = date('Y-m-d', strtotime($tdate));
        $report_excel = $this->_returnModel->apobExcelReports($fdate,$tdate);
        Excel::create('Reports_'.date('Y-m-d H:i:s'), function($excel) use($report_excel) {                        
            $excel->sheet('reportsData', function($sheet) use($report_excel) {          
            $sheet->fromArray($report_excel);
            });
        })->export('xls'); 
    }

    public function ofdOrdersReportDownload(Request $request){
        $filterData = $request->input();
        $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
        $fdate = date('Y-m-d', strtotime($fdate));
        $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
        $tdate = date('Y-m-d', strtotime($tdate));
        $file_name = 'ofd_orders_report'.date('Y-m-d').'.csv';
        $dc_id  = $filterData['loc_dc_id'];
        $dcNames = implode(',',$dc_id);
            if ($dcNames==0){
                $user=Session::get('userId');
                $dcList = $this->_roleModel->getWarehouseData($user, 6);
                $dcList = json_decode($dcList,true);
                $dcNames = isset($dcList['118001']) ? $dcList['118001'] : 0;
                $dcNames =  "'".$dcNames."'";
            }else{
                $dcNames =  "'".$dcNames."'";
            }
        $query = "CALL getOFDOrdersListByDC('$fdate','$tdate',$dcNames)";
        $this->exportToCsv($query,$file_name);
    }
}
