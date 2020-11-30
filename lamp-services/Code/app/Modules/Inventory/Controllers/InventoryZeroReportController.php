<?php

/*
 * Filename: InventoryController.php
 * Description: This file is used for manage product inventory
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 4 July 2016
 * Modified date: 4 July 2016
 */

/*
 * InventoryController is used to manage product inventory
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\Inventory\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
// use Illuminate\Support\Facades\Redirect;
use App\Modules\Inventory\Models\ZeroInventory;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryDc;
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

class InventoryZeroReportController extends BaseController {

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
                parent::Title('Out of Stock ');
                $this->_inventory = new ZeroInventory();
                $this->_inventorymain = new Inventory();
                $this->_inventoryDc = new InventoryDc();
                $this->_roleRepo = new RoleRepo();
                $this->produc_grid_field_db_match = array(
                    // 'product_image' => 'product_image',
                    'product_title' => 'product_title',
                    'product_id' => 'product_id',
                    'sku' => 'sku',
                    'kvi' => 'kvi',
                    'upc' => 'upc',
                    'mrp' => 'mrp',
                    'soh' => 'soh',
                    'atp' => 'atp',
                    'map' => 'map',
                    'order_qty' => 'order_qty',
                    'available_inventory' => 'available_inventory'
                );
                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'Reports' => url('#'), 'Out of Stock Report' => url('#'));
            parent::Breadcrumbs($breadCrumbs);
            $options = json_decode(json_encode($this->_inventory->filterOptions()), true);
            $role_access = $this->_roleRepo->checkPermissionByFeatureCode('INV010');
            $importaccess = $this->_roleRepo->checkPermissionByFeatureCode('INV1003');
            $InventoryReasonCodes = $this->_inventorymain->getInventoryReasonCodes();
            return view('Inventory::indexZeroReport')->with(['filter_options' => $options, 'role_access' => $role_access, 'import_access' => $importaccess,'inventory_reason_Codes' => $InventoryReasonCodes]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    

    public function getExport(Request $request, $store = null) {

        $getproductInfo = array();
        $mytime = Carbon::now();

        $decoded_input = json_decode($request->input('filterData'), true);
        $productid = 0;
        $dcName = '';
        $filters = '';
        if (!empty($decoded_input['dc_name'])) {
            $dcName = $decoded_input['dc_name'];
        }
        if (!empty($decoded_input)) {
            $filters = $decoded_input;
        }
        $getallinventory = $this->_inventory->getProductsDetails($decoded_input);
        $getallinventory = $getallinventory;
        
        Excel::create('Inventory-Zero-SOH-Report-' . $mytime->toDateTimeString(), function($excel) use($getallinventory) {
        
            $excel->setTitle('Inventory');
            $excel->setDescription('Product Information');
            // $time = date("Y-m-d H:i:s");
            $excel->sheet("Zero-SOH-Report", function($sheet) use($getallinventory) {
                        // $sheet->fromArray($getproductInfo);
                        $sheet->loadView('Inventory::productinfoZero' ,array("productinfo" => $getallinventory));
                    }); 
        })->export('xls');        
    }
       


    public function getProductsForProductPage(Request $request)
    {
        $page           = $request->input('page');   //Page number
        $pageSize       = $request->input('pageSize'); //Page size for ajax call
        $filterBy       = array();
        $orderby_array  = "";
        
        if ($request->input('$orderby')) {             //checking for sorting
            $order              = explode(' ', $request->input('$orderby'));
            $order_query_field  = $order[0]; //on which field sorting need to be done
            $order_query_type   = $order[1]; //sort type asc or desc
            $order_by_type      = 'desc';

            if ($order_query_type == 'asc') {
                $order_by_type      = 'asc';
            }
            
            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by           = $this->grid_field_db_match[$order_query_field];
            }

            $orderby_array          = $order_by . " " . $order_by_type;
        }

        $filterBy = $request->input('$filter');

        $filter_by = "";
        if (isset($filterBy) && !empty($filterBy)) {
                $filter_explode = explode(' and ', $filterBy);

                foreach ($filter_explode as $filter_each) {
                    $filter_each_explode = explode(' ', $filter_each);
                    $length = count($filter_each_explode);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter_each_explode[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter_each_explode[$length - 2];
                        $filter_query_value = $filter_each_explode[$length - 1];
                    } else {
                        $filter_query_field = $filter_each_explode[0];
                        $filter_query_operator = $filter_each_explode[1];
                        $filter_query_value = $filter_each_explode[2];
                    }

                    $filter_query_field_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
                        //Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

                        if ($filter_query_field_substr == 'startsw') {
                            $filter_query_field_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = $filter_query_field_value_array[1] . '%';

                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $starts_with_value = $this->produc_grid_field_db_match[$key] . ' like ' . $filter_value;
                                    $filter_by[] = $starts_with_value;
                                } else {
                                    $starts_with_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'endswit') {
                            $filter_query_field_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = '%' . $filter_query_field_value_array[1];

                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $ends_with_value = $this->produc_grid_field_db_match[$key] . ' like ' . $filter_value;
                                    $filter_by[] = $ends_with_value;
                                } else {
                                    $ends_with_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'tolower') {
                            $filter_query_value_array = explode("'", $filter_query_value);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = $filter_query_value_array[1];
                            if ($filter_query_operator == 'eq') {
                                $like = ' = ';
                            } else {
                                $like = ' != ';
                            }
                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $to_lower_value = $this->produc_grid_field_db_match[$key] . $like . $filter_value;
                                    $filter_by[] = $to_lower_value;
                                } else {
                                    $to_lower_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'indexof') {
                            $filter_query_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_query_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = ' like ';
                            } else {
                                $like = ' not like ';
                            }
                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $indexof_value = $this->produc_grid_field_db_match[$key] . $like . $filter_value;
                                    $filter_by[] = $indexof_value;
                                } else {
                                    $indexof_value = "";
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :
                                $filter_operator = ' = ';
                                break;

                            case 'ne':
                                $filter_operator = ' != ';
                                break;

                            case 'gt' :
                                $filter_operator = ' > ';
                                break;

                            case 'lt' :
                                $filter_operator = ' < ';
                                break;

                            case 'ge' :
                                $filter_operator = ' >= ';
                                break;

                            case 'le' :
                                $filter_operator = ' <= ';
                                break;
                        }

                        if (isset($this->produc_grid_field_db_match[$filter_query_field])) {
                            //getting appropriate table field based on grid field
                            $filter_field = $this->produc_grid_field_db_match[$filter_query_field];
                        }

                        $filter_by[] = $filter_field . $filter_operator . $filter_query_value;
                    }
                }
            }

        $Products = $this->_inventory->getProductsDetails($filter_by, $page, $pageSize, $orderby_array);

        echo json_encode(array("results" => $Products));
        
    }

    public function getAllInventory(Request $request) {
        // print_r($request->all());die;

        try {
            $productid = $request->input('productid');
            
            $decoded_input = json_decode($request->input('filterData'), true);
            $dcName = '';
            $filters = '';
            if (!empty($decoded_input['dc_name'])) {
                $dcName = $decoded_input['dc_name'];
            }
            if (!empty($decoded_input)) {
                $filters = $decoded_input;
            }
            $getallinventory = json_decode(json_encode($this->_inventory->getAllProductsByWareHouse($decoded_input)), true);
           // echo "<pre>"; print_r($getallinventory);die;
            
            echo json_encode(array('results' => $getallinventory));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            echo json_encode(array('results' => ''));
        }
    }


}
