<?php

/*
 * Filename: OrderVerificationController.php
 * Description: This file is used for Order Verification
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 16th May 2017
 * Modified date: 16th May 2017
 */

/*
 * OrderVerificationController is used for Order Verification
 * @author      Ebutor <info@ebutor.com>
 * @copyright   ebutor@2016
 * @package     Orders
 * @version:    v1.0
 */

namespace App\Modules\OrderVerificationReport\Controllers;
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
use App\Modules\OrderVerificationReport\Models\OrderVerificationModel;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\Roles\Models\Role;

class OrderVerificationController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                return $next($request);
            });
            // $access = $this->_roleRepo->checkPermissionByFeatureCode('INV1001');
            // if (!$access) {
            //     Redirect::to('/')->send();
            //     die();
            // }
            parent::Title('Order Verification Report - Ebutor');
            $this->grid_field_db_match = array(
                'crate_num' => 'PCM.container_barcode',
                'sku' => 'PCM.container_barcode',
                'order_code' => 'GO.order_code',
                'verification_time' => 'PCM.updated_at',
                'picking_time' => 'PCM.created_at',
                'order_date' => 'GO.order_date',
                'reason' => 'ML.master_lookup_name',
                'wrong_qty' => 'PCM.wrong_picked_qty'
               
            );
            $this->_orderVerificationModel = new OrderVerificationModel();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            parent::Title('Order Verification Report - Ebutor');
            $breadCrumbs = array('Home' => url('/'), 'Reports' => url('#'), 'Order Verifiacation Report' => url('/orderverificationreport/index'));
            parent::Breadcrumbs($breadCrumbs);
            $allfilters = $this->_orderVerificationModel->filterOptions();
            // $data = $this->_orderVerificationModel->getOrderVerificationData();
            return view('OrderVerificationReport::index')->with(["all_filters" => $allfilters]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function viewVerificationData(Request $request) {
        parent::Title('Order Verification Report');
        $filters = json_decode($request->input("filters"), true);
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call

        // $start_date = $filters['from_date'];
        // $end_date = $filters['to_date'];

        $orderby_array = "";
        if ($request->input('$orderby')) {
            // print_r($request->input('$orderby'));exit;           //checking for sorting
            $order = explode(' ', $request->input('$orderby'));

            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
            }
            $orderby_array = $order_by . " " . $order_by_type;
        }
        // print_r($orderby_array);exit;           //checking for sorting


        $filtered_data = $this->_orderVerificationModel->getOrderVerificationData($filters, $page, $pageSize, $orderby_array);
        echo json_encode(array('results' => !empty($filtered_data["result"])?$filtered_data["result"]:[], 'TotalRecordsCount' => !empty($filtered_data["count"])?$filtered_data["count"]:[]));
    }

    public function getOrderVerificationData(Request $request) {
        try {
            parent::Title('Order Verification Report');
            $mytime = Carbon::now();
            $filters = json_decode($request->input("filters"), true);
            // $inputs = json_decode($request->input("filters"), true);
            // $start_date = $inputs['from_date'];
            // $end_date = $inputs['to_date'];
            $filtered_data_all = $this->_orderVerificationModel->getOrderVerificationData($filters, '', '', '');
            $filtered_data = $filtered_data_all["result"];

            $excelheaders = array(
                "DC Name",
                "Crate No",
                "Order Code",
                "Order Date",
                "Hub Name",
                "Picker Name",
                "Picking Time",
                "Verifier Name",
                "Verification Time",
                "Verification Status",
                "Product SKU",
                "Product Title",
                "Verifier Reasons",
                "Quantity"
            );
            Excel::create('OrderVerification-Report' . $mytime->toDateTimeString(), function($excel) use($excelheaders, $filtered_data) {

                $excel->sheet("OrderVerificationReport", function($sheet) use($excelheaders, $filtered_data) {
                    // $sheet->fromArray($getproductInfo);
                    $sheet->loadView('OrderVerificationReport::downloadOrderVerificationExcel', array('headers' => $excelheaders, 'data' => $filtered_data));
                });
            })->export('xls');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function orderCodes(Request $request)
    {
        try {
                $search_term = $request->input('term');
                $getCratesnumbers = $this->_orderVerificationModel->orderCodes($search_term);
                $getCratesnumbers = json_encode($getCratesnumbers);
                return $getCratesnumbers;
                
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function summaryReport(Request $request)
    {
        try {
            $from_date = $request->input('transac_date_from1');
            $to_date = $request->input('transac_date_to1');
            // echo "date";print_r($from_date);die;
            $getSummaryReportData = $this->_orderVerificationModel->getOrderVerificationSummary($from_date, $to_date);
            $mytime = Carbon::now();
            $excelheaders = array("Verified By",
             "From Date", "To Date", "No of Orders Verified", "No of Crates", "No of Lines Verified", "Excess Reported Orders","Excess Reported Lines","Short Reported Orders","Short Reported Lines");
            Excel::create('OrderVerification-summary-Report' . $mytime->toDateTimeString(), function($excel) use($excelheaders, $getSummaryReportData, $from_date, $to_date) {

                $excel->sheet("OrderVerificationReport", function($sheet) use($excelheaders, $getSummaryReportData, $from_date, $to_date) {
                    // $sheet->fromArray($getproductInfo);
                    $sheet->loadView('OrderVerificationReport::summaryReport', array('headers' => $excelheaders, 'data' => $getSummaryReportData, "from_date" => $from_date, "to_date" => $to_date));
                });
            })->export('xls');


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
