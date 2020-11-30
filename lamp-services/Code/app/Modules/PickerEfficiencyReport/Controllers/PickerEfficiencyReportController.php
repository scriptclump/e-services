<?php

/*
 * Filename: PickerEfficiencyReportController.php
 * Description: This file is used to generate report for Picker Efficiency 
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 3rd Jan 2017
 * Modified date: 3rd Jan 2017
 */

/*
 * PickerEfficiencyReportController is used to generate report for Picker Efficiency 
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\PickerEfficiencyReport\Controllers;
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);
use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\Roles\Models\Role;
use App\Modules\PickerEfficiencyReport\Models\PickerEfficiencyModel;
use Mail;


class PickerEfficiencyReportController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                
                $this->_roleRepo = new RoleRepo();
                $this->_role = new Role();
                $this->_picker_effeciency_report = new PickerEfficiencyModel;
                parent::Title('Picker Efficiency Report'); 

                $access = $this->_roleRepo->checkPermissionByFeatureCode('PFR001');
                if (!$access) {
                    Redirect::to('/')->send();
                    die();
                }
            return $next($request);
            });
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'Report' => url('#'), 'Picker Efficiency Report' => url('/pickereffcreport/index'));
            parent::Breadcrumbs($breadCrumbs);
           $picked_users = $this->_picker_effeciency_report->getAllPickers();
           $getAllHubs = $this->_picker_effeciency_report->getAllHubs();
           // echo "<pre>";print_r($getAllHubs);die;
           // $data = json_decode($this->_role->getFilterData(6), true);
           // print_r(json_decode($data['sbu'], true));
           // echo "<pre>";print_r($getAllHubs);die;
            return view('PickerEfficiencyReport::index')->with(['users' => $picked_users, 'allhubs' => $getAllHubs]);
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function gridData(Request $request) {
        try {
            $page           = $request->input('page');   //Page number
            $pageSize       = $request->input('pageSize'); //Page size for ajax call

            $filters = $request->input("filters");
            $filtered_data = json_decode($filters, true);
            // $data = $this->_picker_effeciency_report->getGridData($filtered_data, $page, $pageSize);
             $data = $this->_picker_effeciency_report->getGridData($filtered_data, $page, $pageSize);
            echo json_encode(array('results' => isset($data['result'])?$data['result']:[], 'TotalRecordsCount' => isset($data['count'])?$data['count']:[]));
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function exportData(Request $request)
    {
        try {
                $filters = $request->input("filters");
                $filtered_data = json_decode($filters, true);
                // $data = $this->_delexecperfreport->getExport($filtered_data);

                $mytime = Carbon::now();
                $excelHeaders  = array(
                                        "Picked Date",
                                        "Picked By",
                                        "Assgined Date & Time",
                                        "Hub Name",
                                        "Order No",
                                        "Order Qty",
                                        "Order Val",
                                        "SKUs Order",
                                        "Picked Qty",
                                        "Cancelled Qty",
                                        "Cancellation Reason",
                                        "Start Time",
                                        "Complition Time",
                                        "Duration",
                                        "Area",
                                        "Order Fill Rate"
                                        );

                $result = $this->_picker_effeciency_report->getExport($filtered_data);
                $fileName = 'Picker-efficency-' . $mytime->toDateTimeString();
                Excel::create($fileName, function($excel) use($result, $excelHeaders) {


            $excel->sheet("Report", function($sheet) use($excelHeaders, $result) {
                $sheet->loadView('PickerEfficiencyReport::export', array('headers' => $excelHeaders, 'data' => $result));
            });
            })->export('xls');
            
        
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
     
    } 

    public function pickerSummaryReport(Request $request)
    {
        try {

            $from_date = $request->input('transac_date_from1');
            $to_date = $request->input('transac_date_to1');
            // echo "date";print_r($from_date);die;
            $getSummaryReportData = $this->_picker_effeciency_report->getPickerConsolidatedReport($from_date, $to_date);
            $mytime = Carbon::now();
            $excelheaders = array("Verified By", "From Date", "To Date", "Total Orders", "Excess Picked Orders", "Short Picked Orders", "Total Line Items", "Excess Picked Line Items", "Short Picked Line Items", "Wrong Picked Line Items");
            Excel::create('Picker-summary-Report' . $mytime->toDateTimeString(), function($excel) use($excelheaders, $getSummaryReportData, $from_date, $to_date) {

                $excel->sheet("PickerSummaryReport", function($sheet) use($excelheaders, $getSummaryReportData, $from_date, $to_date) {
                    // $sheet->fromArray($getproductInfo);
                    $sheet->loadView('PickerEfficiencyReport::summaryReport', array('headers' => $excelheaders, 'data' => $getSummaryReportData, "from_date" => date("Y-m-d", strtotime($from_date)), "to_date" => date("Y-m-d", strtotime($to_date))));
                });
            })->export('xls');


        
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    } 

}
