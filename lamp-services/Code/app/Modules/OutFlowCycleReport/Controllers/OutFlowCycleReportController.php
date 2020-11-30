<?php

/*
 * Filename: OutFlowCycleReportController.php
 * Description: This file is used to generate report for OutFlowCycle 
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 4th Jan 2017
 * Modified date: 4th Jan 2017
 */

/*
 * OutFlowCycleReportController is used to generate report for OutFlowCycle
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\OutFlowCycleReport\Controllers;
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
use App\Modules\OutFlowCycleReport\Models\OutFlowCycleModel;


class OutFlowCycleReportController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                
                $this->_roleRepo = new RoleRepo();
                $this->_outflow_cycle_report = new OutFlowCycleModel;
                parent::Title('Out Flow Cycle Report');

                $access = $this->_roleRepo->checkPermissionByFeatureCode('OFCR001');
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
                $alloptions = array();
                $breadCrumbs = array('Home' => url('/'), 'Report' => url('#'), 'Out Flow Cycle Report' => url('/outflowcyclereport/index'));
                parent::Breadcrumbs($breadCrumbs);
               $alloptions['allretailers'] = $this->_outflow_cycle_report->getAllRetailersforSO();
               $alloptions['allsalesofficers'] = $this->_outflow_cycle_report->getAllSalesOfficers();
               $alloptions['alldeliveryexecutives'] = $this->_outflow_cycle_report->getAllDeliveryExecutivesforSO();
               $alloptions['allbeats'] = $this->_outflow_cycle_report->getAllBeatsforSO();
               $alloptions['allareas']  = $this->_outflow_cycle_report->getAllAreasforSO();
               return view('OutFlowCycleReport::index')->with(["options" => $alloptions]);
            
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
            $data = $this->_outflow_cycle_report->getGridData($filtered_data, $page, $pageSize);
            $unique_entry_id = 1;
            foreach ($data['result'] as $key => $value) {
                $data['result'][$key]['unique'] = $unique_entry_id;
                $unique_entry_id++;
            }
            echo json_encode(array('results' => $data['result'], 'TotalRecordsCount' => $data['count']));
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function exportData(Request $request)
    {
        try {
                $filters = $request->input("filters");
                $filtered_data = json_decode($filters, true);
                $mytime = Carbon::now();
                $excelHeaders  = array(
                                        "SO No",
                                        "SO Date",
                                        "SO Created By",
                                        "Retailer Name",
                                        "Area",
                                        "Beat",
                                        "Hub Name",
                                        "Order Status",
                                        "Product Code",
                                        "Product Description",
                                        "MRP",
                                        "SO Qty",
                                        "SO Val",
                                        "Picked Date",
                                        "Picked Qty",
                                        "Picked by"
                                        );

                $result = $this->_outflow_cycle_report->getExport($filtered_data);
                $fileName = 'OutFlow-Cycle-' . $mytime->toDateTimeString();
                Excel::create($fileName, function($excel) use($result, $excelHeaders) {


            $excel->sheet("OutFlowCycleReport", function($sheet) use($excelHeaders, $result) {
                $sheet->loadView('OutFlowCycleReport::export', array('headers' => $excelHeaders, 'data' => $result));
            });
            })->export('xls');
            
        
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
     
    }

   

}
