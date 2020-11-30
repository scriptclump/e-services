<?php

/*
 * Filename: InventoryController.php
 * Description: This file is used for manage product inventory
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 25th October 2016
 * Modified date: 25th October 2016
 */

/*
 * InventoryController is used to manage product inventory
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\MeanProducts\Controllers;

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
use PDF;
use Notifications;
use UserActivity;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\Roles\Models\Role;
use App\Modules\MeanProducts\Models\MeanProducts;
use Mail;
use File;

class MeanProductsController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }

                $this->_roleRepo = new RoleRepo();
                parent::Title('Daily Mean Sales Report');
                $this->_roleRepo = new RoleRepo();
                $this->_meanproducts = new MeanProducts();
                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'Report' => url('#'), 'DMS Report' => url('meanproducts/index'));
            parent::Breadcrumbs($breadCrumbs);
            $warehouses = $this->_meanproducts->getAllWareHouses();
            $emailSetupBtnAccess = $this->_roleRepo->checkPermissionByFeatureCode('DMS002');
            $emailSetupVals = $this->_meanproducts->dmsSetupTable();
            if(!empty($emailSetupVals)){
                $setupVals = $emailSetupVals[0];
            } else {
                $setupVals = "";
            }
            return view('MeanProducts::index')->with(['warehouses' => $warehouses, "emailSetupVals" => $setupVals, "emailSetupBtnAccess" => $emailSetupBtnAccess]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function gridData(Request $request) {
        try {
            $filters = $request->input('filtersData');
            $decodedFilters = json_decode($filters, true);
            $data = $this->_meanproducts->gridDetails($decodedFilters);
            /*echo "Under gridData <br>";
            echo "<pre>"; print_r($data); die;*/
            echo json_encode(array('results' => (!empty($data['results']) ? $data['results'] : array())));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function exportExcel(Request $request) {
        try {
            $mytime = Carbon::now();
            $fileName = 'Dialy-Mean-Sales-Report-' . $mytime->toDateTimeString();
            $filters = $request->input('filtersData');
            $decodedFilters = json_decode($filters, true);
            $data = $this->_meanproducts->gridDetails($decodedFilters);
            $results = $data['results'];
            //$headers = array("Product ID", "Manufacturer Name", "Product Title", "SKU", "CFC Qty", "Latest MRP", "CP Enabled(Y/N)", "PO Code", "PO Date", "PO Qty", "AVG Day Sales", "Available CFC", "Open to Buy(CFC)", "CFC to Buy", "Min CFC Rate", "Last Bought CFC Rate", "Total Amount", "Supplier Name", "WD/SWD");
            $i = 0;
            Excel::create($fileName, function($excel) use($results, $i) {
                $excel->sheet("DMSReport", function($sheet) use($results, $i) {
                    $sheet->fromArray($results);
                    $totRows = count($results);
                    for ($i = 2; $i <= $totRows + 1; $i++) {
                        $sheet->cell("S".$i, function($cell) use($i) {
                            $cell->setValue('=P'.$i.'*Q'.$i);
                        });
                    }
                });
            })->export('xls');
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
