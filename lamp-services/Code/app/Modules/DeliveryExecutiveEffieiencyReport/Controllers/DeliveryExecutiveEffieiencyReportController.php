<?php

/*
 * Filename: DeliveryExecutiveEffieiencyReportController.php
 * Description: This file is used to generate report for Delivery Executive Efficiency 
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 2nd Jan 2017
 * Modified date: 2nd Jan 2017
 */

/*
 * DeliveryExecutiveEffieiencyReportController is used to generate report for Delivery Executive Efficiency 
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\DeliveryExecutiveEffieiencyReport\Controllers;
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
use App\Modules\DeliveryExecutiveEffieiencyReport\Models\DeliveryEfficiencyModel;


class DeliveryExecutiveEffieiencyReportController extends BaseController {

     
    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }


                $this->_roleRepo = new RoleRepo();
                $access = $this->_roleRepo->checkPermissionByFeatureCode('DEFR001');
                if (!$access) {
                    Redirect::to('/')->send();
                    die();
                }

                return $next($request);
            }); 
            $this->_delivery_effeciency_report = new DeliveryEfficiencyModel;
            parent::Title('Delivery Executive Efficiency Report');

            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }



    public function indexAction() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'Report' => url('#'), 'Delivery Executive Efficiency Report' => url('/delexeeffcreport/index'));
            parent::Breadcrumbs($breadCrumbs);
           $deliveried_users = $this->_delivery_effeciency_report->getAllDeliveriedUsers();
           $getAllHubs = $this->_delivery_effeciency_report->getAllHubs();
            return view('DeliveryExecutiveEffieiencyReport::index')->with(["delivery_persons" => $deliveried_users, 'allhubs' => $getAllHubs]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function gridData(Request $request) {
        try {
            $page           = $request->input('page');   //Page number
            $pageSize       = $request->input('pageSize'); //Page size for ajax call

            $filters = $request->input("filters");
            $filtered_data = json_decode($filters, true);
            $data = $this->_delivery_effeciency_report->getGridData($filtered_data, $page, $pageSize);
            echo json_encode(array('results' => $data['result'], 'TotalRecordsCount' => $data['count']));
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
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
                                        "Date",
                                        "Delivery Executive Name",
                                        "Assigned Time",
                                        "Submit Time",
                                        "Hub Name",
                                        "Order No",
                                        "Invoice Qty",
                                        "Invoice Val",
                                        "Invoice SKU",
                                        "delivered Qty",
                                        "Return Qty",
                                        "Return Reason",
                                        "Duration",
                                        "Order Status",
                                        "Area",
                                        "Beats",
                                        // "Estimated Distance"

                                        );

                $result = $this->_delivery_effeciency_report->getExport($filtered_data);
                $fileName = 'Report' . $mytime->toDateTimeString();
        } catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
                Log::error($ex->getTraceAsString());
        }
     
    }

   

}
