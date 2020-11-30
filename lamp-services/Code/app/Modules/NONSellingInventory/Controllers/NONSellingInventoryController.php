<?php

/*
 * Filename: InventoryController.php
 * Description: This file is used for manage product inventory
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 19th Oct 2016
 * Modified date: 19th Oct 2016
 */

/*
 * InventoryController is used to manage product inventory
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\NONSellingInventory\Controllers;

// ini_set('max_execution_time', 0);
// ini_set('memory_limit', -1);

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
// use Illuminate\Support\Facades\Redirect;
use App\Modules\NONSellingInventory\Models\NONSellingInventory;
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

class NONSellingInventoryController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
               
                // $access = $this->_roleRepo->checkPermissionByFeatureCode('INV1001');
                // if (!$access) {
                //     Redirect::to('/')->send();
                //     die();
                // }
                $this->_roleRepo = new RoleRepo();
                parent::Title('Non Billed SKU Report');
                $this->_nonsellingInventory = new NONSellingInventory();
                $this->_roleRepo = new RoleRepo();
                return $next($request);
            });
          
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
                $breadCrumbs = array('Home' => url('/'), 'Report' => url('#'), 'Non Billed SKU Report' => url('nonsellinginventory/index'));
                parent::Breadcrumbs($breadCrumbs);
                $fieldforceUsers = $this->_nonsellingInventory->getFieldForceUsers();
                $getPlaces = $this->_nonsellingInventory->getAllPlaces();
                return view('NONSellingInventory::index')->with(['fieldforceusers' => $fieldforceUsers, 'places' => $getPlaces]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getNonSellingResults(Request $request)
    {
        try {
        $decodeddata = json_decode($request->input('filters'), true);
        $filtersData = $this->_nonsellingInventory->getFilteredResults($decodeddata);
        echo json_encode(array('results' => $filtersData['res'], 'TotalRecordsCount' => $filtersData['count']));
    } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        
    }


    public function getNonSellingResultsExport(Request $request)
    {
        try {
                $mytime = Carbon::now();
                $decodeddata = json_decode($request->input('filters'), true);
                $excelheaders  = array("Product  ID", "Product Title", "SKU", "MRP", "ESP");
                $filtersData = $this->_nonsellingInventory->getFilteredResults($decodeddata);
                $filtersData = $filtersData['res'];
                Excel::create('NonSellable-Inventory' . $mytime->toDateTimeString(), function($excel) use($filtersData, $excelheaders) {
                    //get all warehouses
                    $excel->setTitle('NonSellable-Inventory');
                    $excel->setDescription('Product Information');
                    $excel->sheet("NonSellable Inventory", function($sheet) use($filtersData, $excelheaders) {
                                // $sheet->fromArray($getproductInfo);
                                $sheet->loadView('NONSellingInventory::exportExcel' ,array('headers' => $excelheaders, 'inventory' => $filtersData));
                            }); 
                })->export('xls');
        
    } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        
    }

    

}
