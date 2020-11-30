<?php

namespace App\Modules\Orders\Controllers;

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
use PDF;
use App\Modules\Roles\Models\Role;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Central\Repositories\RoleRepo;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\ConsignmentModel;
use App\Modules\Indent\Models\LegalEntity;
use Response;

class ConsignmentController extends BaseController {

    protected $_orderModel;
    protected $_leModel;
    protected $_roleModel;
    protected $_consignmentModel;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });
        $this->_orderModel = new OrderModel();
        $this->_leModel = new LegalEntity();
        $this->_roleModel = new Role();
        $this->_consignmentModel = new ConsignmentModel();
    }

    public function printSalesReports() {
        try {
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $report_excel = $this->getExcelData($filters);
            $bulkPrintData = array();
            if (is_array($report_excel) && count($report_excel) > 0) {
                foreach ($report_excel as $key => $hub) {
                    $leInfo = array();
                    if (is_array($hub) && count($hub) > 0) {
                        $orderId = $hub[0]['order_id'];
                        $orderDetails = $this->_orderModel->getOrderDetailById($orderId);
                        $leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                        $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id, $orderDetails->le_wh_id);
                        $fromAddress = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                        $toAddress = $this->_leModel->getWarehouseById($orderDetails->hub_id);
                        $bulkPrintData[] = view('Orders::printTripReports')->with(array(
                                    'Reportinfo' => $hub,
                                    'hubName' => $key,
                                    'leInfo' => $leInfo,
                                    'legalEntity' => $legalEntity,
                                    'fromAddress' => $fromAddress,
                                    'toAddress' => $toAddress
                                ))->render();
                    }
                }
                return view('Orders::bulkprintTripReports')->with('bulkPrintData', $bulkPrintData);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function excelSalesReports($vehicleId) {
        try {
            $reportDate = Date("Y_m_d_H_i_s");
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $filters['vehicle_id'] = $vehicleId;
            $report_excel = $this->getExcelData($filters, 'excel');
            if (is_array($report_excel) && count($report_excel) > 0) {
                Excel::create('Tripsheet_DC_HUB_' . $reportDate, function($excel) use($report_excel) {
                    foreach ($report_excel as $key => $hub) {
                        $excel->sheet($key, function($sheet) use($hub) {
                            $sheet->loadView('Orders::tripReports')->with('Reportinfo', $hub);
                        });
                    }
                })->export('xls');
            } else {
                Excel::create('Tripsheet_DC_HUB_' . $reportDate, function($excel) use($report_excel) {
                    $excel->sheet('Trip Sheet', function($sheet) use($report_excel) {
                        $sheet->loadView('Orders::tripReports')->with('Reportinfo', $report_excel);
                    });
                })->export('xls');
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getExcelData($filters, $type = '') {
        $query = DB::table("vw_stock_transit_report");
        if (isset($filters['118001'])) {
            $Dcs_Assigned = implode(',', explode(',', $filters['118001']));
            $query->whereRaw("le_wh_id IN ($Dcs_Assigned)");
        }
        if (isset($filters['118002'])) {
            $Hubs_Assigned = implode(',', explode(',', $filters['118002']));
            $query->whereRaw("hub_id IN ($Hubs_Assigned)");
        }
        if (isset($filters['vehicle_id'])) {
            $query->where('vehicle_id',$filters['vehicle_id']);
        }
        $dataArr = $query->get()->all();
        
        $loadSheetDataArr = $hubWiseOrders = $containerCounts = array();
        
        if (is_array($dataArr) && count($dataArr) > 0) {
            foreach ($dataArr as $data) {
                $lp_wh_name = str_replace(' ', '_', $data->lp_wh_name);
                if ($type != 'excel') {
                    $lp_wh_name = $lp_wh_name . '_' . $data->st_docket_no;
                }
                if (strlen($lp_wh_name) > 30) {
                    if ($type == 'excel') {
                        $lp_wh_name = substr($lp_wh_name, 0, 30);
                    } else {
                        $lp_wh_name = 'Docket#' . $data->st_docket_no;
                    }
                }
                $loadSheetDataArr[$lp_wh_name][] = $data;
                
                $hubWiseOrders[$lp_wh_name][] = $data->order_id;
            }
            
            foreach($hubWiseOrders as $hubName => $orderIds){
                $containerCounts[$hubName] = DB::table("gds_order_track")->whereIn("gds_order_id", array_unique($orderIds))
                ->select(DB::raw("sum(cfc_cnt) as cfc_cnt, sum(bags_cnt) as bags_cnt, sum(crates_cnt) as crates_cnt"))->get()->all()[0];
                
                array_push($loadSheetDataArr[$hubName], $containerCounts[$hubName]);
            }
        }
        return json_decode(json_encode($loadSheetDataArr), 1);
    }

    public function excelSalesReportsHubtoDc($vehicleId) {
        try {
            $reportDate = Date("Y_m_d_H_i_s");
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $filters['vehicle_id'] = $vehicleId;
            $report_excel = $this->getExcelDataHubtoDc($filters, 'excel');
            if (is_array($report_excel) && count($report_excel) > 0) {
                Excel::create('Tripsheet_HUB_DC_' . $reportDate, function($excel) use($report_excel) {
                    foreach ($report_excel as $key => $hub) {
                        $excel->sheet($key, function($sheet) use($hub) {
                            $sheet->loadView('Orders::excel_reports_hub_dc')->with('HubtoDc', $hub);
                        });
                    }
                })->export('xls');
            } else {
                Excel::create('Tripsheet_HUB_DC_' . $reportDate, function($excel) use($report_excel) {
                    $excel->sheet('Trip Sheet', function($sheet) use($report_excel) {
                        $sheet->loadView('Orders::excel_reports_hub_dc')->with('HubtoDc', $report_excel);
                    });
                })->export('xls');
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function printSalesReportsHubtoDc() {
        try {
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $report_excel = $this->getExcelDataHubtoDc($filters);
            $bulkPrintData = array();
            if (is_array($report_excel) && count($report_excel) > 0) {
                foreach ($report_excel as $key => $hub) {
                    $leInfo = array();
                    if (is_array($hub) && count($hub) > 0) {
                        $orderId = $hub[0]['order_id'];
                        $orderDetails = $this->_orderModel->getOrderDetailById($orderId);
                        $leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                        $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id, $orderDetails->le_wh_id);
                        $fromAddress = $this->_leModel->getWarehouseById($orderDetails->hub_id);
                        $toAddress = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                        $bulkPrintData[] = view('Orders::printTripReportsHubToDC')->with(array(
                                    'HubtoDc' => $hub,
                                    'hubName' => $key,
                                    'leInfo' => $leInfo,
                                    'legalEntity' => $legalEntity,
                                    'fromAddress' => $fromAddress,
                                    'toAddress' => $toAddress
                                ))->render();
                    }
                }
                return view('Orders::bulkprintTripReports')->with('bulkPrintData', $bulkPrintData);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getExcelDataHubtoDc($filters, $type = '') {
        $query = DB::table("vw_stock_transit_report_hub_to_dc");
        if (isset($filters['118001'])) {
            $Dcs_Assigned = implode(',', explode(',', $filters['118001']));
            $query->whereRaw("le_wh_id IN ($Dcs_Assigned)");
        }
        if (isset($filters['118002'])) {
            $Hubs_Assigned = implode(',', explode(',', $filters['118002']));
            $query->whereRaw("hub_id IN ($Hubs_Assigned)");
        }
        if (isset($filters['vehicle_id'])) {
            $query->where('vehicle_id',$filters['vehicle_id']);
        }

        $dataArr = $query->get()->all();
        
        $loadSheetDataArr = $hubWiseOrders = $containerCounts = array();
        
        if (is_array($dataArr) && count($dataArr) > 0) {
            foreach ($dataArr as $data) {
                $lp_wh_name = str_replace(' ', '_', $data->hub_name);
                if ($type != 'excel') {
                    $lp_wh_name = $lp_wh_name . '_' . $data->rt_docket_no;
                }
                if (strlen($lp_wh_name) > 30) {
                    if ($type == 'excel') {
                        $lp_wh_name = substr($lp_wh_name, 0, 30);
                    } else {
                        $lp_wh_name = 'Docket#' . $data->rt_docket_no;
                    }
                }
                $loadSheetDataArr[$lp_wh_name][] = $data;
                
                $hubWiseOrders[$lp_wh_name][] = $data->order_id;
            }
            
            foreach($hubWiseOrders as $hubName => $orderIds){
                $containerCounts[$hubName] = DB::table("gds_order_track")->whereIn("gds_order_id", array_unique($orderIds))
                ->select(DB::raw("sum(cfc_cnt) as cfc_cnt, sum(bags_cnt) as bags_cnt, sum(crates_cnt) as crates_cnt"))->get()->all()[0];
                
                array_push($loadSheetDataArr[$hubName], $containerCounts[$hubName]);
            }
        }
        return json_decode(json_encode($loadSheetDataArr), 1);
    }

    public function tripPdfAction() {
        try {
            $reportDate = Date("Y_m_d_H_i_s");
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $report_excel = $this->getExcelData($filters);
            $bulkData = array();
            if (is_array($report_excel) && count($report_excel) > 0) {
                foreach ($report_excel as $key => $hub) {
                    $leInfo = array();
                    if (is_array($hub) && count($hub) > 0) {
                        $orderId = $hub[0]['order_id'];
                        $orderDetails = $this->_orderModel->getOrderDetailById($orderId);
                        $leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                        $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id, $orderDetails->le_wh_id);
                        $fromAddress = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                        $toAddress = $this->_leModel->getWarehouseById($orderDetails->hub_id);
                        $bulkData[] = view('Orders::tripPdf')->with(array(
                                    'Reportinfo' => $hub,
                                    'hubName' => $key,
                                    'leInfo' => $leInfo,
                                    'legalEntity' => $legalEntity,
                                    'fromAddress' => $fromAddress,
                                    'toAddress' => $toAddress
                                ))->render();
                    }
                }
                $pdf = PDF::loadView('Orders::bulkprintTripReports', array('bulkPrintData' => $bulkData));
                return $pdf->download('Tripsheet_DC_HUB_' . $reportDate . '.pdf');
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function tripHubPdfAction() {
        try {
            $reportDate = Date("Y_m_d_H_i_s");
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $report_excel = $this->getExcelDataHubtoDc($filters);
            $bulkData = array();
            if (is_array($report_excel) && count($report_excel) > 0) {
                foreach ($report_excel as $key => $hub) {
                    $leInfo = array();
                    if (is_array($hub) && count($hub) > 0) {
                        $orderId = $hub[0]['order_id'];
                        $orderDetails = $this->_orderModel->getOrderDetailById($orderId);
                        $leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                        $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id, $orderDetails->le_wh_id);
                        $fromAddress = $this->_leModel->getWarehouseById($orderDetails->hub_id);
                        $toAddress = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                        $bulkData[] = view('Orders::tripHubPdf')->with(array(
                                    'HubtoDc' => $hub,
                                    'hubName' => $key,
                                    'leInfo' => $leInfo,
                                    'legalEntity' => $legalEntity,
                                    'fromAddress' => $fromAddress,
                                    'toAddress' => $toAddress
                                ))->render();
                    }
                }
                $pdf = PDF::loadView('Orders::bulkprintTripReports', array('bulkPrintData' => $bulkData));
                return $pdf->download('Tripsheet_HUB_DC_' . $reportDate . '.pdf');
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getDockets() {
        try {

            $Request = Input::all();
            $flag = (isset($Request['transfer_type']) && $Request['transfer_type'] == 'dc') ? 1 : '' ;

            $Json = json_decode($this->_roleModel->getFilterData(6),1);
            $filters = json_decode($Json['sbu'], 1);

            if(isset($filters['118002']) && !empty($filters['118002'])) {

               $Dockets = $this->_consignmentModel->getOrdersByDockets($filters['118002'],$flag);     

               return Response::json(array('status' => 200,'data'=>$Dockets));
                
            } else {
                return Response::json(array('status' => 400,'data'=>'Dockets not available'));

            }



        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function getdocketDetails() {
        try {

            $post = Input::all();
            $Request = array('transfer_type'=>$post['transfer_type'],'docket_no'=>$post['docket_no']);

            if($Request['transfer_type']!='' && $Request['docket_no']!='') {

                $Result = $this->_consignmentModel->getdocketDetails($Request);
                return Response::json(array('status'=>200,'data'=>$Result, 'totalOrders'=>count($Result)));

            }            

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return Response::json(array('status'=>400,'data'=>[], 'totalOrders'=>0));
        }
    }    
}