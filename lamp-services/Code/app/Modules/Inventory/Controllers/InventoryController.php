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
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Central\Repositories\ProductRepo;
use App\Modules\Inventory\Models\ReadLogs;
use Mail;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Inventory\Controllers\commonIgridController;
use App\Modules\Inventory\Models\InventorySnapshot;
use App\Modules\Inventory\Models\InventorySummary;
use App\Lib\Queue;
use File;
use DB;
class InventoryController extends BaseController {

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
               
                return $next($request);
            });
                $this->_inventorySnp = new InventorySnapshot();
                $this->_inventorySummary = new InventorySummary();
                $this->_roleRepo = new RoleRepo();
                parent::Title('Ebutor - Manage Inventory');
                $this->_inventory = new Inventory();
                $this->_inventoryDc = new InventoryDc();
                $this->_roleRepo = new RoleRepo();
                $this->_readlogs = new ReadLogs();
                $this->objCommonGrid = new commonIgridController();
                $this->queue = new Queue();
                $this->roleObj = new Role();
                $this->grid_field_db_match = array(
                    'product_image' => 'product_image',
                    'product_title' => 'product_title',
                    'sku' => 'sku',
                    'kvi' => 'kvi',
                    'upc' => 'upc',
                    'mrp' => 'mrp',
                    'soh' => 'soh',
                    'atp' => 'atp',
                    'order_qty' => 'order_qty',
                    'available_inventory' => 'available_inventory'
                );
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'Inventory' => url('/inventory/index'), 'Dashboard' => url('#'));
            parent::Breadcrumbs($breadCrumbs);
            $options = json_decode(json_encode($this->_inventory->filterOptions()), true);
            $role_access = $this->_roleRepo->checkPermissionByFeatureCode('INV010');
            $importaccess = $this->_roleRepo->checkPermissionByFeatureCode('INV1003');
            $InventoryReasonCodes = $this->_inventory->getInventoryReasonCodes();
           return view('Inventory::index')->with(['filter_options' => $options, 'role_access' => $role_access, 'import_access' => $importaccess, 'inventory_reason_Codes' => $InventoryReasonCodes]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getAllInventory(Request $request) {
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
            $getallinventory = json_decode(json_encode($this->_inventoryDc->getAllInventory($dcName)), true);

            $i = 0;
            foreach ($getallinventory as $eachData) {
                
                $ptrValue = 0.00;
                $cpValue = 0.00;
                $mrpValue = 0.00;
                $mapValue = 0.00;
                $EspValue = 0.00;
                $product_data = $this->_inventory->getAllProductsByWareHouse($eachData['le_wh_id'], $filters, $productid);
                if (!empty($filters) || ($productid!=0)) {
                    $decoded_data = json_decode(json_encode($product_data), true);
                    foreach ($decoded_data['results'] as $each_data) {
                        $tempPtr = 0;
                        $tempPtr = $each_data['ptrvalue'] * $each_data['soh'];
                        $ptrValue += $tempPtr;
                        $tempCp = 0;
                        $tempCp = $each_data['cp'] * $each_data['soh'];
                        $cpValue += $tempCp;
                        $tempMrp = 0;
                        $tempMrp = $each_data['mrp'] * $each_data['soh'];
                        $mrpValue += $tempMrp;
                        $tempMap = 0;
                        $tempMap = $each_data['map'] * $each_data['soh'];
                        $mapValue += $tempMap;

                        $tempEsp = 0;
                        $tempEsp = $each_data['esp'] * $each_data['soh'];
                        $EspValue += $tempEsp;
                    }
                    $getallinventory[$i]['ptrvalue'] = $ptrValue;
                    $getallinventory[$i]['cpvalue'] = $cpValue;
                    $getallinventory[$i]['mrpvalue'] = $mrpValue;
                    $getallinventory[$i]['mapvalue'] = $mapValue;
                    $getallinventory[$i]['espvalue'] = $EspValue;
                }
                //$getallinventory[$i++]['inventory'] = $this->_inventory->getAllProductsByWareHouse($eachData['le_wh_id'], $filters, $productid);
                //Changes made by @rohit - Jan10th-2017
                $getallinventory[$i++]['inventory'] = $product_data;
            }
            echo json_encode(array('results' => $getallinventory));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            echo json_encode(array('results' => ''));
        }
    }

    public function getExport(Request $request, $store = null,$param=array()) {
        $getproductInfo = array();
        $mytime = Carbon::now();
        if(count($param)>0){
            $decoded_input=isset($param['filterData'])?$param['filterData']:array();
        }else{
            $decoded_input = json_decode($request->input('filterData'), true);
        }
        $productid = 0;
        $dcName = '';
        $filters = '';
        if (!empty($decoded_input['dc_name'])) {
            $dcName = $decoded_input['dc_name'];
            if(!is_array($dcName)){
                $dcName=explode(",",$dcName);
            }
        }
        if (!empty($decoded_input)) {
            $filters = $decoded_input;
        }

        $i = 0;
        $fileName = 'Inventory-' . $mytime->toDateTimeString();
        if($store != null)
        {
            $fileName = str_replace(' ', '_', (str_replace(':', '_', $fileName)));
        }


        $excelExport = Excel::create($fileName, function($excel) use($getproductInfo, $decoded_input, $dcName, $filters, $productid) {
            //get all warehouses
            $excel->setTitle('Inventory');
            $excel->setDescription('Product Information');

            // $inventoryELP    = $this->_roleRepo->checkPermissionByFeatureCode('INVELP');
            // $inventoryDLP    = $this->_roleRepo->checkPermissionByFeatureCode('INVDLP');
            // $inventoryFLP    = $this->_roleRepo->checkPermissionByFeatureCode('INVFLP');

            //$allWarehouses = json_decode($this->_inventoryDc->getAllInventory(), true);
            //Changes made by @rohit -Jan10th-2017

            // $arrSize = sizeof($decoded_input['dc_name']);

            //if data is empty then default grid will be downloaded
            if (empty($decoded_input)) {
                Log::info('inventory export if');
                $allWarehouses = json_decode(json_encode($this->_inventoryDc->getDataForMail()), true);

                for ($i = 0; $i < sizeof($allWarehouses); $i++) {
                    $warehousename = $allWarehouses[$i]['dcname'];
                    $warehouseID = $allWarehouses[$i]['le_wh_id'];
                    Log::info($warehousename);
                    reset($getproductInfo);
                    $getData = json_decode(json_encode($this->_inventory->getExportInventoryData($warehouseID,Session::get('userId'))), true);
                    //print_r($getData);
                    $getproductInfo = isset($getData['results'])?$getData['results']:array();
                    //Log::info($getproductInfo);
                    if($warehousename == "" || $warehousename == NULL)
                    {
                        $warehousename = "NULL";
                    }
                    if(count($getproductInfo)>0){
                        /*$getproductInfo = array_map(
                            function (array $elem) use ($inventoryELP,$inventoryDLP,$inventoryFLP) {
                                if(isset($elem['MRP'])){
                                    $elem['MRP'] = (float)$elem['MRP'];
                                }
                                if(isset($elem['ESP'])){
                                    $elem['ESP'] = (float)$elem['ESP'];
                                }
                                if(isset($elem['ELP'])){    
                                    $elem['ELP'] = (float)$elem['ELP'];
                                }
                                if(isset($elem['PTR'])){
                                    $elem['PTR'] = (float)$elem['PTR'];
                                }
                                if(isset($elem['Available CFC'])){
                                    $elem['Available CFC'] = (float)$elem['Available CFC'];
                                }
                                if(isset($elem['FLP'])){
                                    $elem['FLP'] = (float)$elem['FLP'];
                                }
                                if(isset($elem['DLP'])){
                                    $elem['DLP'] = (float)$elem['DLP'];
                                }
                                if($inventoryELP == 0)
                                    unset($elem['ELP']);
                                
                                if($inventoryDLP == 0)
                                    unset($elem['DLP']);
                                
                                if($inventoryFLP == 0)
                                    unset($elem['FLP']);
                                
                                return $elem;             
                            },
                            $getproductInfo
                        );*/
                        $excel->sheet(substr($warehousename,0,30), function($sheet) use($getproductInfo) {
                            $sheet->fromArray($getproductInfo);
                            //$headers = isset($getproductInfo[0])?array_keys($getproductInfo[0]):[];
                            //$sheet->loadView('Inventory::productinfo')->with('productinfo', $getproductInfo)->with('headers',$headers);
                        });
                    }
                }
            } else {
                Log::info('inventory export else');
                $getallinventory = json_decode(json_encode($this->_inventoryDc->getAllInventory($dcName)), true);
                $i = 0;
                foreach ($getallinventory as $eachData) {
                    $ptrValue = 0.00;
                    $cpValue = 0.00;
                    $mrpValue = 0.00;
                    $product_data = $this->_inventory->getAllProductsByWareHouseForExcel($eachData['le_wh_id'], $filters, $productid);

                    if (!empty($filters)) {
                        $decoded_data = json_decode(json_encode($product_data), true);
                        foreach ($decoded_data['results'] as $each_data) {
                            $tempPtr = 0;
                            $tempPtr = $each_data['ptrvalue'] * $each_data['soh'];
                            $ptrValue += $tempPtr;
                            $tempCp = 0;
                            $tempCp = $each_data['cp'] * $each_data['soh'];
                            $cpValue += $tempCp;
                            $tempMrp = 0;
                            $tempMrp = $each_data['mrp'] * $each_data['soh'];
                            $mrpValue += $tempMrp;
                        }
                        $getallinventory[$i]['ptrvalue'] = $ptrValue;
                        $getallinventory[$i]['cpvalue'] = $cpValue;
                        $getallinventory[$i]['mrpvalue'] = $mrpValue;
                    }
                    //$getallinventory[$i++]['inventory'] = $this->_inventory->getAllProductsByWareHouseForExcel($eachData['le_wh_id'], $filters, $productid);
                    //Changes made by @rohit - Jan10th-2017

                    $getallinventory[$i++]['inventory'] = $product_data;

                   // $getproductInfo = json_decode(json_encode($getallinventory[$i]['inventory']['results']), true);
                   // $excel->sheet(substr($warehousename,0,30), function($sheet) use($getproductInfo) {
                     //    $sheet->fromArray($getproductInfo);
                        //$sheet->loadView('Inventory::productinfo')->with('productinfo', $getproductInfo);
                    //});
                }

                for ($i = 0; $i < sizeof($getallinventory); $i++) {
                    $warehousename = $getallinventory[$i]['dcname'];
                    $warehouseID = $getallinventory[$i]['le_wh_id'];
//                    Log::info($warehouseID);
                    reset($getproductInfo);
                    $getproductInfo = json_decode(json_encode($getallinventory[$i]['inventory']['results']), true);
  //                  Log::info($getproductInfo);
                    $headers = isset($getproductInfo[0])?array_keys($getproductInfo[0]):[];
                    $excel->sheet(substr($warehousename,0,30), function($sheet) use($getproductInfo) {
                         $sheet->fromArray($getproductInfo);
                        //$sheet->loadView('Inventory::productinfo')->with('productinfo', $getproductInfo);
                    });
                }
            }
        }); 
        if($store != null)
        {
            $excelExport->store('xlsx', storage_path('Inventory/Exports'));
            return $fileName.'.xlsx';
        }else{            
            $excelExport->export('xlsx');
        }        
    }

   public function updateInventory(Request $request)
   {
    try {

            $excess_qty = $request->input("excess_qty");
            $soh_value = $request->input("soh_update");
            $comments  = $request->input("inventory_comments");
            $reason = $request->input("reason");
            $warehouse_ID = $request->input("warehouse_id");
            $product_id = $request->input("prod");

            // $getOldValues = $this->_inventory->getOldSOHAndATPValues($product_id, $warehouse_ID);
            

            $getcurrentSOH = $this->_inventory->getSOH($product_id, $warehouse_ID);
            $total_ordrerd_qty = $this->_inventory->getOrderdQty($product_id, $warehouse_ID);
            $count_openproducts_in_workflow = $this->_inventory->getOpenProductsInTracking_WorkFlow($product_id, $warehouse_ID);
            
            $getcurrentSOH = $getcurrentSOH[0]['soh'];

            $dit_Qty = $request->input("dit_qty");
            $dnd_Qty = $request->input("dnd_qty");

            if($dit_Qty == 0 && $dnd_Qty == 0 && $excess_qty == 0)
            {
                return "allzero";
            }
            // $dit_diff = $dit_Qty - $getOldValues['dit_qty'];
            // $dnd_diff = $dnd_Qty - $getOldValues['dnd_qty'] ;
            
            if($dit_Qty < 0 || $dnd_Qty < 0)
            {
                return "negitivevalues";
            }

            $resulted_soh = ($getcurrentSOH -$total_ordrerd_qty) - ($dit_Qty + $dnd_Qty);
            
            if($resulted_soh < 0)
            {
                return "failed";
            }

            if($count_openproducts_in_workflow != 0)
            {
                return "opentickets"; 
            }

            $sku = $this->_inventory->getSkuByProductId($product_id);
            $result = $this->_inventory->updateProducts($excess_qty, $soh_value, $warehouse_ID, $product_id, $sku, $comments, $dit_Qty, $dnd_Qty, $reason);
            
            return $result;
        
    } catch (Exception $e) {
        Log::error($ex->getMessage());
        Log::error($ex->getTraceAsString());
    }
   
   }

   public function exportTemplate(Request $request)
   {
        $mytime = Carbon::now();
        $warehouseId = $request->input('warehousenamess');
        $warehousename = $this->_inventoryDc->getWareHouseName($warehouseId);
        $allproductsdata = $this->_inventory->getAllProductsByOnlyWareHouseIdForExcel($warehouseId);
        $allproductsdata = json_decode(json_encode($allproductsdata), true);
        $excelheaders = array("Product ID", "Product Title", "SKU", "SOH", "Excess", "DIT QTY", "Missing", "Comments", "Reason");
        $getInventory_reason_codes = $this->_inventory->getInventoryReasonCodes();
        Excel::create('Inventory-template-' . $mytime->toDateTimeString(), function($excel) use($warehousename, $allproductsdata, $excelheaders, $warehouseId, $getInventory_reason_codes) {
            //get all warehouses
            $excel->setTitle('Inventory');
            $excel->setDescription('Product Information');
            $excel->sheet(substr($warehousename,0,30), function($sheet) use($allproductsdata,  $excelheaders, $warehouseId) 
            {
                // $sheet->fromArray($getproductInfo);
                $sheet->loadView('Inventory::downloadExcel' ,array('headers' => $excelheaders, 'products_info' => $allproductsdata, 'warehouseId' => $warehouseId));
            }); 

            $excel->sheet("Reason codes", function($sheet) use($getInventory_reason_codes) 
            {
                // $sheet->fromArray($getproductInfo);
                $sheet->loadView('Inventory::reasonCodes' ,array('reasoncodes' => $getInventory_reason_codes));
            }); 
        })->export('xls');



   }

   public function replanishmentDownloadTemplate(Request $request)
   {
        try {
                 $mytime = Carbon::now();
                
                $warehouseId = $request->input('warehousenamess-replanishment');
                $warehousename = $this->_inventoryDc->getWareHouseName($warehouseId);
                $allproductsdata = $this->_inventory->getAllProductsByOnlyWareHouseIdForExcelReplanishment($warehouseId);
                $allproductsdata = json_decode(json_encode($allproductsdata), true);
                $excelheaders = array("Product ID", "Product Title", "SKU", "Replenishment Level", "Replenishment UOM");
                $getInventory_reason_codes = $this->_inventory->getReplanishmentCodes();
                
                Excel::create('Replenishment-template-' . $mytime->toDateTimeString(), function($excel) use($warehousename, $allproductsdata, $excelheaders, $warehouseId, $getInventory_reason_codes) {
                    //get all warehouses
                    $excel->setTitle('Inventory');
                    $excel->setDescription('Product Information');
                    $excel->sheet(substr($warehousename,0,30), function($sheet) use($allproductsdata,  $excelheaders, $warehouseId) 
                    {
                        // $sheet->fromArray($getproductInfo);
                        $sheet->loadView('Inventory::downloadExcelReplanishment' ,array('headers' => $excelheaders, 'products_info' => $allproductsdata, 'warehouseId' => $warehouseId));
                    }); 

                    $excel->sheet("Replenishment UOMs", function($sheet) use($getInventory_reason_codes) 
                    {
                        $sheet->loadView('Inventory::ReplanishmentCodes' ,array('reasoncodes' => $getInventory_reason_codes));
                        // $sheet->fromArray($getproductInfo);
                        
                    }); 
                })->export('xls');
        } catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
        }
   }

   public function excelImport(Request $request)
   {
    DB::beginTransaction();
    try{
        $productObj = new ProductRepo();
        $approval_flow_func= new CommonApprovalFlowFunctionModel();
        if (Input::hasFile('upload_excel_sheet')) {
            $path = Input::file('upload_excel_sheet')->getRealPath();
            $data = $this->readExcel($path);
            $data1 = json_decode(json_encode($data), true);
            $file_path = "";
            $warehouseData = '';
            if(isset($data1['warehouseid'])){
                    $warehouseData = reset($data1['warehouseid']);
            }

            
            $excelheaders = array("1" =>"Product ID", "2" => "Product Title", "3" => "SKU", "4" => "SOH", "5" => "Excess", "6" => "DIT QTY", "7" => "Missing", "8" => "Comments", "9" => "Reason");
            
            $explodewarehousedata = explode(":", $warehouseData);
            
            $headingdata = $data1['header_data'];//headers from excel

            $warehouseID = $explodewarehousedata[1];
            
            $productsData = $data1['productsdata'];
            
            
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Inventory Bulk Upload', 'drafted', \Session::get('userId'));
            
            /*if($res_approval_flow_func['status'] == 0)
            {
                print_r(json_encode(array("no_permission" => "No Permission")));
                die;
            }*/
            $file_data = Input::file('upload_excel_sheet');
            $url =$productObj->uploadToS3($file_data,'inventory',1);

            $upload_data['file_name'] = $file_data->getClientOriginalName();
            $upload_data['file_extension'] = $file_data->getClientOriginalExtension();
            $excelheaders = array_values($excelheaders);
            $headingdata  = array_values($headingdata);  //resetting excel columns
            if ($excelheaders != $headingdata) {
                $result = 0;
            } else {
                $result = $this->_inventory->updating_SOH_ATP_Values($productsData, $warehouseID, $url);

                $result['linkdownload'] = 'inventory/excellogs/'.$result['reference'];
                // $result['linkdownload'] = $file_path;
            }
            Notifications::addNotification(['note_code' => 'INVT001', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Inventory updation Completed, <a href="/' .$result['linkdownload'] . '" target="_blank">View Details</a>']);
            DB::commit();
            print_r(json_encode($result));
        }
    } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            print_r(json_encode(array("rollBack" => "rollBack")));
        }
    
    }

   public function excelUploadReplanishment(Request $request)
   {
    $productObj = new ProductRepo();
    $approval_flow_func= new CommonApprovalFlowFunctionModel();
    if (Input::hasFile('upload_excel_sheet')) {
            $path = Input::file('upload_excel_sheet')->getRealPath();
            $data = $this->readExcel($path);
            $data1 = json_decode(json_encode($data), true);
            $file_path = "";
            $warehouseData = '';
            if(isset($data1['warehouseid'])){
                    $warehouseData = reset($data1['warehouseid']);
            }

            
            $excelheaders = array("0" =>"Product ID", "1" => "Product Title", "2" => "SKU", "3" => "Replenishment Level", "4" => "Replenishment UOM");
            // $excelheaders = array("0" =>"Product ID", "1" => "Product Title", "2" => "SKU", "3" => "Replanishment Level", "4" => "Replanishment UOM");
            //$warehouseData = $data1['warehouseid'][0];
            $explodewarehousedata = explode(":", $warehouseData);
            
            $headingdata = $data1['header_data'];//headers from excel
            $headingdata = array_values($headingdata);
            
            $warehouseID = $explodewarehousedata[1];
            
            $productsData = $data1['productsdata'];

            if ($excelheaders != $headingdata) {
                $result = 0;
            } else {
                $file_data = Input::file('upload_excel_sheet');
                $url = $productObj->uploadToS3($file_data,'inventory',1);

                $upload_data['file_name'] = $file_data->getClientOriginalName();
                $upload_data['file_extension'] = $file_data->getClientOriginalExtension();
                $result = $this->_inventory->updatingReplanishmentQty($productsData, $warehouseID, $url);
                $result['linkdownload'] = 'inventory/excellogsreplanishment/'.$result['reference'];
                // $result['linkdownload'] = $file_path;
            }
            Notifications::addNotification(['note_code' => 'INVT001', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Inventory updation Completed, <a href="/' .$result['linkdownload'] . '" target="_blank">View Details</a>']);
            print_r(json_encode($result));
        }
    
    }

    public function readExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'false');
            $headres = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['warehouseid'] = $cat_data;
            $data['header_data'] = $headres;
            $data['productsdata'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }


    public function getProductsForProductPage(Request $request)
    {

        $inventoryEditAccess = $this->_roleRepo->checkPermissionByFeatureCode('INV1002');
        $productid = $request->input('productid');
        $Products = $this->_inventory->getProductsDetails($productid);
        
        foreach ($Products['results'] as $key => $value) {
            if($inventoryEditAccess == 1)
            {
              
              $Products['results'][$key]['actions'] = '<a data-type="edit" data-ditqty="'.$value['dit_qty'].'" data-dndqty = "'.$value['dnd_qty'].'" data-dcname="'. $value['dcname'] .'" data-skuid="'. $value['sku'] .'" data-producttitle="'. $value['product_title'] .'" data-warehouseid="' . $value['le_wh_id'] . '" data-prodid="' . $value['product_id'] . '" data-soh="'. $value['soh'] .'" data-atp = "'. $value['atp'] .'" data-toggle="modal" data-target="#edit-products"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            }
            else
            {
               $Products['results'][$key]['actions'] = ""; 
            }
        }

        echo json_encode($Products);
        
    }

    public function readExcelLogs($refId)
    {
        try {
            $result = $this->_readlogs->readExcelLogs($refId);
            
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function readExcelLogsReplanishement($refId)
    {
        try {
            $result = $this->_readlogs->readExcelLogsReplanishment($refId);
            
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function mailInventoryApproved($email, $file="", $bulkuploadvals="", $new_vals=""){
        try{
            $message_aftermail_Sent = "";
            $time = Carbon::now();
            $htmlData = "";
            $emailBody = "Hello User, <br/><br/>";
            if(!empty($bulkuploadvals) && !empty($new_vals))
            {
                $attribute_type = isset($bulkuploadvals['master_lookup_name'])?$bulkuploadvals['master_lookup_name']:"N/A";
                $htmlData .= "<table border='1' width='100%' cellspacing='0' cellpadding='4' bordercolor=''><thead><tr> <th>Product Title</th>  <th>SKU</th> <th>Field</th> <th> Old Value</th> <th>Difference</th> <th>New Value</th> <th>Excess</th> <th>Reason</th> <th>Comment</th> </tr></thead>";
                $htmlData .= "<tbody> <tr> <td rowspan='4'>".$new_vals['prod_info'][0]['product_title']."</td> <td rowspan='4'>".$new_vals['prod_info'][0]['sku']."</td> <td>DIT</td> <td>".$new_vals['old_dit']."</td> <td>".$bulkuploadvals['dit_diff']."</td> <td>".$new_vals['new_dit']."</td> <td rowspan='4'>".$bulkuploadvals['excess']."</td> <td rowspan='4'>".$attribute_type."</td> <td rowspan='4'>".$bulkuploadvals['remarks']."</td>     </tr>";
                $htmlData .= "<tr>   <td>Missing</td> <td>".$new_vals['old_dnd']."</td> <td>".$bulkuploadvals['dnd_diff']."</td> <td>".$new_vals['new_dnd']."</td> </tr> </tbody></table>";

                $emailBody .= "Inventory bulk upload approved successfully<br/><br/>";
                $emailBody .= $htmlData."<br/><br/>";
            }
            
            

                if(!empty($file))
                {
                    $emailBody .= "Inventory bulk upload approved successfully, please <a href='".$file."'>click here</a> to download report<br/><br/>";
                }
            // $emailBody .= "*Note: This is an auto generated email !!";
                
            if (Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($email, $time) {
                        $message->to($email);
                        $message->subject('Inventory Approval Confirmation');
                            if(!empty($file))
                            {
                                $message->attach($file);
                            }
                    })) {
                // File::delete($filePath);
                $message_aftermail_Sent .=  "Mail sent to all users !! \n";
            } else {
                $message_aftermail_Sent .= "Error in sending mail  !!\n";
            }

            return $message_aftermail_Sent;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }




    public function indexActionInventory() {
        try {          
            $breadCrumbs = array('Home' => url('/'), 'Inventory' => url('/inventory/index'), 'Dashboard' => url('#'));
            parent::Breadcrumbs($breadCrumbs);
            $options = json_decode(json_encode($this->_inventory->filterOptions()), true);
            
            $role_access = $this->_roleRepo->checkPermissionByFeatureCode('INV010');
            $importaccess = $this->_roleRepo->checkPermissionByFeatureCode('INV1003');
            $invAdj_access = $this->_roleRepo->checkPermissionByFeatureCode('INADJ001');
            $stockistab = $this->_roleRepo->checkPermissionByFeatureCode('STKINV001');

            $InventoryReasonCodes = $this->_inventory->getInventoryReasonCodes();
            $productdetails = json_decode(json_encode($this->_inventorySnp->productFilterOption()), true); 
            $sohAcess =  $this->_roleRepo->checkPermissionByFeatureCode('SOHR001');   
            $summaryProdDetails = json_decode(json_encode($this->_inventorySummary->productTitleSku()), true); 

            $Json=json_decode($this->roleObj->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);      
            
            $warehouse=$this->roleObj->GetWareHouses($filters);
            $warehouse = json_decode(json_encode($warehouse), True);
            return view('Inventory::indexnew')->with(['filter_options'=>$options, 'role_access' => $role_access, 'import_access' => $importaccess,'inventory_reason_Codes'=> $InventoryReasonCodes,'productdetail'=>$productdetails,'soh_access'=>$sohAcess,'summaryProdDetails' => $summaryProdDetails,"invAdj_access"=>$invAdj_access,'stockistab'=>json_decode(json_encode($stockistab)),'dcs'=>json_decode(json_encode($warehouse))]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

     public function getAllInventoryGrid(Request $request) {
       // try {
            $productid = $request->input('productid');
            $decoded_input = json_decode($request->input('filterData'), true);
            $offset=$request->input('$skip');
            $perpage=$request->input('$top');
            $offset=($offset!='')?$offset:0;
            $perpage=($perpage!='')?$perpage:10;
            $dcName[] = $request->input('dcname');
           // $dcName = [4497];
            $filters = '';
            if (!empty($decoded_input['dc_name'])) {
                $dcName = $decoded_input['dc_name'];
            }
            if (!empty($decoded_input)) {
                $filters = $decoded_input;
            }
            
            $dcName = implode(',', $dcName);          
            $makeFinalSql = array();
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }

            // make sql for product_title
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("product_title", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for sku
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("sku", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for mrp
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("mrp", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for product id
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("product_id", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for product_group_id
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("product_group_id", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for kvi
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("kvi", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for inv_display_mode
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("inv_display_mode", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for soh
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("soh", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            

            // make sql for soh
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("re_pending_qty", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            
            // make sql for soh
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("atp", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for soh
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("dit_qty", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for soh
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("dnd_qty", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }


            // make sql for soh
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("replanishment_level", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for soh
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("replanishment_uom", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for map
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("map", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for order_qty
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("order_qty", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }


            // make sql for available_inventory
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("available_inventory", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for reserved_qty
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("reserved_qty", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for reserved_qty
            /*$fieldQuery = $this->objCommonGrid->makeIGridToSQL("di", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }*/
            
            // make sql for reserved_qty
            /*$fieldQuery = $this->objCommonGrid->makeIGridToSQL("mi", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }*/

            // make sql for reserved_qty
            /*$fieldQuery = $this->objCommonGrid->makeIGridToSQL("ci", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }*/
            

             // make sql for ISD
            /*$fieldQuery = $this->objCommonGrid->makeIGridToSQL("isd", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }*/
            

             // make sql for isd7
            /*$fieldQuery = $this->objCommonGrid->makeIGridToSQL("isd7", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }*/
            

             // make sql for isd30
            /*$fieldQuery = $this->objCommonGrid->makeIGridToSQL("isd30", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }*/
            

            // make sql for star
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("star", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            

            // arran data for filtering
             $orderBy = "";
            $orderBy = $request->input('%24orderby');
            if($orderBy==''){
                $orderBy = $request->input('$orderby');
            }

            // Arrange data for pagination
            $page="";
            $pageSize="";
            if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
                $page = $request->input('page');
                $pageSize = $request->input('pageSize');
            }

            $getallinventory = json_decode(json_encode($this->_inventoryDc->getAllInventoryByName($dcName)), true);
           
            foreach ($getallinventory as $eachData) {
                $product_count = $this->_inventory->getAllProductsByWareHouseBySelection($eachData['le_wh_id'], $filters, $productid,$makeFinalSql,0,0,$orderBy);
                //print_r($eachData);exit;
                $product_data = $this->_inventory->getAllProductsByWareHouseBySelection($eachData['le_wh_id'], $filters, $productid,$makeFinalSql,$offset,$perpage,$orderBy);  
            }
            $product_data['resultCount']=(isset($product_count))?$product_count:0;
            return $product_data;
        /*} catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            echo json_encode(array('results' => ''));
        }*/
    }


    // this is for download inventory data based on new route

     public function getExportInventoryData(Request $request, $store = null) {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', -1);
        $getproductInfo = array();
        $mytime = Carbon::now();
        //$inventoryELP    = $this->_roleRepo->checkPermissionByFeatureCode('INVELP');
        //$inventoryDLP    = $this->_roleRepo->checkPermissionByFeatureCode('INVDLP');
        //$inventoryFLP    = $this->_roleRepo->checkPermissionByFeatureCode('INVFLP');
        $decoded_input = json_decode($request->input('filterData'), true);
        $productid = 0;

        $dcName = '';
        $filters = '';
        if (!empty($decoded_input['dc_name'])) {
            $dcName = $decoded_input['dc_name'];
            // $key =  array_search("all",$dcName);
            // if(!is_array($dcName)){
            //     $dcName=explode(",",$dcName);
            //     $key =  array_search("all",$dcName);
            // }
        }
        if (!empty($decoded_input)) {
            $filters = $decoded_input;
        }
        $fileName = 'Inventory-' . $mytime->toDateTimeString();
        if($store != null)
        {
            $fileName = str_replace(' ', '_', (str_replace(':', '_', $fileName)));
        }


        $param = array();
        $param['userName'] = $request->session()->get('userName');
        $param['userId'] = $request->session()->get('userId');

        $inputs = json_decode($request->input('filterDetails'), true);

        $param['getproductInfo'] = $getproductInfo;
        $param['decoded_input'] = $decoded_input;
        $param['filters'] = $filters;
        $param['productid'] = $productid;
        $param['filterData']=$decoded_input;
        $request=new Request();

        if($dcName == 'all'){
            $dcData=$this->_inventory->getAllWarehouseDataByAccess();
            //$dcData=json_decode($dcData,true);
            $data=array_column(json_decode(json_encode($dcData),1),"bu_id");
            // $Json = json_decode($this->roleObj->getFilterData(6), 1);
            //$filters = json_decode($Json['sbu'], 1);
            //$dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            //print_r($dc_acess_list);die;

           /* $dataInventory = array();
            foreach ($data as $key => $value) {
                $result = $this->_inventory->getWhByData($value);
                foreach ($result as $key => $value) {
                    if($value!='')
                    $dataInventory[]=$value;
                }
            }
    
            $getallinventory = $this->_inventory->getDcData($dataInventory);*/
            $param['getallinventory'] = $dcData;
            $decoded_input['dc_name']=$data;
            $param['decoded_input'] = $decoded_input;
            $encoded = base64_encode(json_encode($param));
            $args = array("ConsoleClass" => 'InventoryReport', 'arguments' => array('data'=>$encoded));
            $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);            
            return "You will get an email with Report attached !!";
        }else{
            $dcName = $this->_inventory->getWarehouseByBu($dcName);
             /*   foreach ($dcName  as $k=>$value) {
                    $product_data = json_decode(json_encode($this->_inventory->getExportInventoryData($value->le_wh_id)),true);
                    $product_data = isset($product_data['results'])?$product_data['results']:[];
                    $headers = isset($product_data[0])?array_keys($product_data[0]):[];
                    $getWareHouseTypeId = $this->_inventory->getWhareHouseTypeId($value->le_wh_id);
                    $wareHouseTypeId = $getWareHouseTypeId->legal_entity_type_id;
                return view('Inventory::productinfo')->with('productinfo', $product_data)
                        ->with('headers', $headers)->with('inventoryELP',$inventoryELP)->with('inventoryDLP',$inventoryDLP)->with('inventoryFLP',$inventoryFLP)->with('inventoryBLP',$inventoryBLP)->with('wareHouseTypeId',$wareHouseTypeId);
                }
                */

            $fileName = 'Inventory-' . $mytime->toDateTimeString();
            $excelExport = Excel::create($fileName, function($excel) use($filters,$dcName) {
                //get all warehouses,
                $excel->setTitle('Inventory');
                $excel->setDescription('Product Information');
                $inventorydata=array();

             
                foreach ($dcName  as $value) {
                    log::info($value->le_wh_id);
                    $product_data = json_decode(json_encode($this->_inventory->getExportInventoryData($value->le_wh_id,Session::get('userId'))),true);
                    $product_data = isset($product_data['results'])?$product_data['results']:[];
                    $getWareHouseTypeId = $this->_inventory->getWhareHouseTypeId($value->le_wh_id);
                    $wareHouseTypeId = $getWareHouseTypeId->legal_entity_type_id;
                    $warehousename = $value->dcname;
                    $warehousename = preg_replace('/[^A-Za-z0-9\- ]/', '',  $warehousename);
                    if(count($product_data)>0){
                        $excel->sheet(substr($warehousename,0,30), function($sheet) use($product_data) {
                            $sheet->fromArray($product_data);
                        });
                    }
                }
            });
            $excelExport->export('xlsx');
        }
            
    }
    public function createExcelBkground($userName,$userId,$getproductInfo, $decoded_input, $getallinventory, $filters, $productid){
        try {
            $fileName = "InvReport_".time();
            $filepath = $this->makeExcelFile($fileName,$getproductInfo, $decoded_input, $getallinventory, $filters, $productid,$userId);
            return $this->mailExcelReport($filepath, $userId, $userName);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function makeExcelFile($fileName,$getproductInfo, $decoded_input, $getallinventory, $filters, $productid,$userId) {
        try {

            Excel::create($fileName, function($excel) use($getproductInfo, $decoded_input, $getallinventory, $filters, $productid,$userId){
                $allWarehouses = $getallinventory;
            //if data is empty then default grid will be downloaded
            if (empty($decoded_input)) {
                for ($i = 0; $i < sizeof($allWarehouses); $i++) {
                    $warehousename = $allWarehouses[$i]['dcname'];
                    $warehouseID = $allWarehouses[$i]['le_wh_id'];
                    reset($getproductInfo);
                    $getproductInfo = json_decode(json_encode($this->_inventory->getAllProductsByOnlyWareHouseIdForExcel($warehouseID)), true);
                    if($warehousename == "" || $warehousename == NULL)
                    {
                        $warehousename = "NULL";
                    }
                    if(count($getproductInfo)>0){

                       // $headers = isset($getproductInfo[0])?array_keys($getproductInfo[0]):[];
                       /* $excel->sheet(substr($warehousename,0,30), function($sheet) use($getproductInfo,$headers) {
                            // $sheet->fromArray($getproductInfo);
                            $sheet->loadView('Inventory::productinfo')->with('productinfo', $getproductInfo)->with('headers', $headers);
                        });*/
                        
                        // $product_data = array_map(
                        //             function (array $elem) use ($inventoryELP,$inventoryDLP,$inventoryFLP) {
                        //                 if(isset($elem['MRP'])){
                        //                     $elem['MRP'] = (float)$elem['MRP'];
                        //                 }
                        //                 if(isset($elem['ESP'])){
                        //                     $elem['ESP'] = (float)$elem['ESP'];
                        //                 }
                        //                 if(isset($elem['ELP'])){    
                        //                     $elem['ELP'] = (float)$elem['ELP'];
                        //                 }
                        //                 if(isset($elem['PTR'])){
                        //                     $elem['PTR'] = (float)$elem['PTR'];
                        //                 }
                        //                 if(isset($elem['Available CFC'])){
                        //                     $elem['Available CFC'] = (float)$elem['Available CFC'];
                        //                 }
                        //                 if(isset($elem['FLP'])){
                        //                     $elem['FLP'] = (float)$elem['FLP'];
                        //                 }
                        //                 if(isset($elem['DLP'])){
                        //                     $elem['DLP'] = (float)$elem['DLP'];
                        //                 }
                        //                 if($inventoryELP == 0)
                        //                     unset($elem['ELP']);
                                        
                        //                 if($inventoryDLP == 0)
                        //                     unset($elem['DLP']);
                                        
                        //                 if($inventoryFLP == 0)
                        //                     unset($elem['FLP']);
                                        
                        //                 return $elem;              
                        //             },
                        //             $getproductInfo
                        //         );
                        $excel->sheet(substr($warehousename,0,30), function($sheet) use($product_data) {
                            $sheet->fromArray($product_data);
                        });
                    }
                }
            } else {
                foreach ($getallinventory as $eachData) {
                    $product_data = json_decode(json_encode($this->_inventory->getExportInventoryData($eachData['le_wh_id'],$userId)),1);
                    $product_data = isset($product_data['results'])?$product_data['results']:[];
                    $getWareHouseTypeId = $this->_inventory->getWhareHouseTypeId($eachData['le_wh_id']);
                    $wareHouseTypeId = $getWareHouseTypeId->legal_entity_type_id;
                    $warehousename = $eachData['dcname'];
                    if(count($product_data)>0){
                        //$headers = isset($product_data[0])?array_keys($product_data[0]):[];
                        /*
                        $excel->sheet(substr($warehousename,0,30), function($sheet) use($product_data,$headers,$inventoryELP,$inventoryDLP,$inventoryFLP,$wareHouseTypeId) {
                            // $sheet->fromArray($getproductInfo);                           
                            $sheet->loadView('Inventory::productinfo')->with('productinfo', $product_data)->with('headers', $headers)->with('inventoryELP',$inventoryELP)->with('inventoryDLP',$inventoryDLP)->with('inventoryFLP',$inventoryFLP)->with('wareHouseTypeId',$wareHouseTypeId);
                        });
                        */
                      /*  $product_data = array_map(
                                    function (array $elem) use ($inventoryELP,$inventoryDLP,$inventoryFLP) {
                                       if(isset($elem['MRP'])){
                                            $elem['MRP'] = (float)$elem['MRP'];
                                        }
                                        if(isset($elem['ESP'])){
                                            $elem['ESP'] = (float)$elem['ESP'];
                                        }
                                        if(isset($elem['ELP'])){    
                                            $elem['ELP'] = (float)$elem['ELP'];
                                        }
                                        if(isset($elem['PTR'])){
                                            $elem['PTR'] = (float)$elem['PTR'];
                                        }
                                        if(isset($elem['Available CFC'])){
                                            $elem['Available CFC'] = (float)$elem['Available CFC'];
                                        }
                                        if(isset($elem['FLP'])){
                                            $elem['FLP'] = (float)$elem['FLP'];
                                        }
                                        if(isset($elem['DLP'])){
                                            $elem['DLP'] = (float)$elem['DLP'];
                                        }
                                        if($inventoryELP == 0)
                                            unset($elem['ELP']);
                                        
                                        if($inventoryDLP == 0)
                                            unset($elem['DLP']);
                                        
                                        if($inventoryFLP == 0)
                                            unset($elem['FLP']);
                                        
                                        return $elem;            
                                    },
                                    $product_data
                                );*/
                        $excel->sheet(substr($warehousename,0,30), function($sheet) use($product_data) {
                            $sheet->fromArray($product_data);
                        });
                    }
                }
            }
            })->store('xlsx', public_path('download'));
            return public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xlsx";
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function mailExcelReport($filePath, $userId, $userName){
        try{
            $email = $this->_inventory->getUserEmail($userId);
            $time = Carbon::now();
            $emailBody = "Hello " . ucwords(str_replace(".", " ", explode("@", $email)[0])) . ", <br/><br/>";
            $emailBody .= "Please find attached Inventory Report.<br/><br/>";
            $emailBody .= "*Note: This is an auto generated email !!";
            if (Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($email, $filePath, $time) {
                        $message->to($email);
                        $message->subject('Inventory Report '.date('d-m-Y',strtotime($time->toDateTimeString())));
                        $message->attach($filePath);
                    })) {
                File::delete($filePath);
                echo "Mail sent to - ".$email." !! Temp file deleted !!\n";
            } else {
                echo "Error in sending mail to ".$email." !!\n";
            }
            return $email;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function exportInvAdjTemplate(Request $request)
   {
        $mytime = Carbon::now();
        $warehouseId = $request->input('warehousenamess');
        $warehousename = $this->_inventoryDc->getWareHouseName($warehouseId);
        $allproductsdata = $this->_inventory->getAllProductsByOnlyWareHouseIdForExcel($warehouseId);
        $allproductsdata = json_decode(json_encode($allproductsdata), true);
        $excelheaders = array("Product ID", "Product Title", "SKU", "SOH", "Excess", "DIT QTY", "Missing", "Comments", "Reason");
        $getInventory_reason_codes = $this->_inventory->getInventoryAdjReasonCodes();
        Excel::create('Inv-adjustment-template-' . $mytime->toDateTimeString(), function($excel) use($warehousename, $allproductsdata, $excelheaders, $warehouseId, $getInventory_reason_codes) {
            //get all warehouses
            $excel->setTitle('Inventory A');
            $excel->setDescription('Product Information');
            $excel->sheet(substr($warehousename,0,30), function($sheet) use($allproductsdata,  $excelheaders, $warehouseId) 
            {
                // $sheet->fromArray($getproductInfo);
                $sheet->loadView('Inventory::downloadExcel' ,array('headers' => $excelheaders, 'products_info' => $allproductsdata, 'warehouseId' => $warehouseId));
            }); 

            $excel->sheet("Reason codes", function($sheet) use($getInventory_reason_codes) 
            {
                // $sheet->fromArray($getproductInfo);
                $sheet->loadView('Inventory::reasonCodes' ,array('reasoncodes' => $getInventory_reason_codes));
            }); 
        })->export('xls');

   }
   public function InvAdjExcelImport(Request $request)
   {
    DB::beginTransaction();
    try{
        $productObj = new ProductRepo();
        $approval_flow_func= new CommonApprovalFlowFunctionModel();
        if (Input::hasFile('upload_excel_sheet')) {
            $path = Input::file('upload_excel_sheet')->getRealPath();
            $data = $this->readExcel($path);
            $data1 = json_decode(json_encode($data), true);
            $file_path = "";
            $warehouseData = '';
            if(isset($data1['warehouseid'])){
                    $warehouseData = reset($data1['warehouseid']);
            }
            $excelheaders = array("1" =>"Product ID", "2" => "Product Title", "3" => "SKU", "4" => "SOH", "5" => "Excess", "6" => "DIT QTY", "7" => "Missing", "8" => "Comments", "9" => "Reason");
            
            $explodewarehousedata = explode(":", $warehouseData);
            
            $headingdata = $data1['header_data'];//headers from excel

            $warehouseID = $explodewarehousedata[1];
            
            $productsData = $data1['productsdata'];
            
            
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Inventory Adjustment', 'drafted', \Session::get('userId'));
            
            if($res_approval_flow_func['status'] == 0)
            {
                print_r(json_encode(array("no_permission" => "No Permission")));
                die;
            }
            $file_data = Input::file('upload_excel_sheet');
            $url = $productObj->uploadToS3($file_data,'inventory',1);

            $upload_data['file_name'] = $file_data->getClientOriginalName();
            $upload_data['file_extension'] = $file_data->getClientOriginalExtension();
            $excelheaders = array_values($excelheaders);
            $headingdata  = array_values($headingdata);  //resetting excel columns
            if ($excelheaders != $headingdata) {
                $result = 0;
            } else {
                $result = $this->_inventory->updatingInvAdjValues($productsData, $warehouseID, $url);
                $result['linkdownload'] = 'inventoryadjustment/excellogs/'.$result['reference'];
                // $result['linkdownload'] = $file_path;
            }
            Notifications::addNotification(['note_code' => 'INVT001', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Inventory Adjustment updation Completed, <a href="/' .$result['linkdownload'] . '" target="_blank">View Details</a>']);
            DB::commit();
            print_r(json_encode($result));
        }
    } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            print_r(json_encode(array("rollBack" => "rollBack")));
        }
    }

    public function outOfStockReport(){

        $oosAccess = $this->_roleRepo->checkPermissionByFeatureCode('INVOOS1');
        if(!$oosAccess)
            Redirect::to('/')->send();

        $productsInfo = $this->_inventory->getProductsInfo();
        
        return view('Inventory::outofstockreport')->with(['productsData' => $productsInfo]);
    }

    public function outOfStockReportChartData(Request $request){

        $parameters = Input::all();

        if(isset($parameters['productId']) and !empty($parameters['productId'])){
            // Searching for 'ALL' products ID
            $parameters['productId'] = 
                (is_array($parameters['productId']))?
                $parameters['productId']:explode(",",$parameters['productId']);
            
            if(array_search('0', $parameters['productId']) or $parameters['productId'][0] == "NULL")
                $parameters['productId'] = NULL;
            else
                $parameters['productId'] = implode(",", $parameters['productId']);
        }else
            return ["status" => 0, "message" => "Please select atleast 1 Product."];

        if(isset($parameters['flag']) and !empty($parameters['flag'])){
            if($parameters['flag'] != "oos_report_date_range" or $parameters['flag'] == "NULL"){
                $dashboardController = new DashboardController();
                $dates = $dashboardController->getDateRange(["filter_date" => $parameters['flag']]);
                $parameters['startDate'] = $dates['fromDate'];
                $parameters['endDate'] = $dates['toDate'];
            }
        }
        else
            return ["status" => 0, "message" => "Please select valid Dates."];

        $periodType = 1;
        if(isset($parameters['flag']))
        switch ($parameters['flag']) {
            case 'today': case 'yesterday':
                $periodType = 1;
                break;
            
            case 'wtd':
                $periodType = 2;
                break;

            case 'ytd':
                $periodType = 3;
                break;

            case 'quarter':
                $periodType = 4;
                break;
            
            case 'oos_report_date_range':
                $diffSecs = strtotime($parameters['endDate']) - strtotime($parameters['startDate']);
                $diffDays = $diffSecs / 86400;
                if($diffDays < 7)
                    $periodType = 2;
                else if($diffDays <= 31)
                    $periodType = 1;
                else if($diffDays > 31)
                    $periodType = 3;
                else
                    $periodType = 1;
                break;

            default:
                $periodType = 1;
                break;
        }

        $modalResponse = $this->_inventory->getKPIOOSReportModal($parameters,$periodType);

        if(($parameters['flag'] != -1) and (empty($modalResponse) or $modalResponse == []))
            return ["status" => 0, "message" => "No Data in Database. Please select valid Dates"];

        if($modalResponse == -1)
            return ["status" => 0, "message" => "Invalid Inputs, Please try again!"];

        $gridData = [];
        if(isset($modalResponse['grid']))
            $gridData = $modalResponse['grid'];

        // Preparation of Data for Charts
        $chartData = [];
        if(isset($modalResponse['chart']))
        foreach ($modalResponse['chart'] as $key => $record) {
            $toolDate = [];
            foreach ($record as $subKey => $value) {
                if($subKey!= 'Date' and $subKey!= 'Product_id' and $subKey!= 'Customer Name' and $subKey!= 'Shop Name' and $subKey!= 'Product Name' and $subKey!= 'Sale Loss' and $subKey!= 'Out of Stock Qty'){
                    $toolDate[][$subKey] = $value;
                }
            }
            $chartData[$record->OOS_Date][] = [
                "Label" => "OOS",
                "sale_loss" => $record->{'Sale Loss'},
                "OOS" => $record->{'Out of Stock Qty'},
                "name" => $record->{'Product Name'},
                "values" => $toolDate,
            ];
        }
        
        return ["status" => 1, "chartResult" => $chartData, "gridResult" => $gridData];
    }

    public function stockistLedgerReports(){
         try{
            $filterData = Input::get();

            $warehouse=$filterData['warehouse'];
            
            $fdate = (isset($filterData['fromdate']) && !empty($filterData['fromdate'])) ? $filterData['fromdate'] : date('Y-m').'-01';
            $fdate = str_replace('/', '-', $fdate);
            $fromDate=  date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['todate']) && !empty($filterData['todate'])) ? $filterData['todate'] : date('Y-m').'-01';
            $date = str_replace('/', '-', $tdate);
            $TDate=  date('Y-m-d', strtotime($date));
            $details = json_decode(json_encode($this->_inventory->inventoryStockistReports($warehouse,$fromDate,$TDate)), true);
            Excel::create('Stockist Inventory Reports - '. date('Y-m-d'),function($excel) use($details) {
                $excel->sheet('Stockist Inventory', function($sheet) use($details) {          
                $sheet->fromArray($details);
                });      
            })->export('csv');

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => "Error in getting Data"));
        }
    }
    public function getBuUnit(){
        return $this->_inventory->businessTreeData();
    }

    public function indexBatchAction(){
        try {          
            $breadCrumbs = array('Home' => url('/'), 'Inventory' => url('/inventory/index'), 'Inventory Batch' => url('#'));
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('Inventory Batch'));
            parent::Breadcrumbs($breadCrumbs);
            $checkBPermissions=$this->_roleRepo->checkPermissionByFeatureCode('INVBTCH001');
            if ($checkBPermissions==0)
            {
                return Redirect::to('/');
            }
            return view('Inventory::batchindex')->with([]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }       
    }

    public function inventoryBatchHistory(Request $request){
        try{
            $makeFinalSql = array();
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }

            if(count($request->input('$fillter')) > 0) {
                $filter .= ' and'.$request->input('$fillter');
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("mainbatchidsfilter", $filter, false);
            
            if($fieldQuery!=''){
                $explodebatch=explode('=', $fieldQuery);
                $batchid=isset($explodebatch[1])?trim($explodebatch[1]):'';
                if(isset($explodebatch[1]) && $batchid!=''){
                    $fieldQuery =str_replace('mainbatchidsfilter', 'main_batch_id', $fieldQuery);        
                    $makeFinalSql[] = $fieldQuery;
                }
            }



            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("product_id", $filter, false);
            
            if($fieldQuery!=''){
                $explodebatchprdid=explode('=', $fieldQuery);
                $prdid=isset($explodebatchprdid[1])?trim($explodebatchprdid[1]):'';
                if(isset($explodebatchprdid[1]) && $prdid!=''){
                    //$fieldQuery =str_replace('product_id', 'gob.product_id', $fieldQuery);    
                    $makeFinalSql[] = $fieldQuery;
                }
            }

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("SKU", $filter, false);
            $fieldQuery =str_replace('SKU', 'SKU', $fieldQuery);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("display_name", $filter, false);
            $fieldQuery =str_replace('display_name', 'getLeWhName(le_wh_id)', $fieldQuery);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("business_legal_name", $filter, false);
           // $fieldQuery =str_replace('business_legal_name', 'le.business_legal_name', $fieldQuery);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("main_batch_id", $filter, false);
            if($fieldQuery!=''){
                //$fieldQuery =str_replace('main_batch_id', 'gob.main_batch_id', $fieldQuery);
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ord_qty", $filter, false);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("legal_entity_type", $filter, false);
            //$fieldQuery =str_replace('legal_entity_type', 'rf.legal_entity_type', $fieldQuery);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("inv_qty", $filter, false);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("esp", $filter, false);
            if($fieldQuery!=''){
                //$fieldQuery =str_replace('esp', 'gob.esp', $fieldQuery);
                $makeFinalSql[] = $fieldQuery;
            }

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("elp", $filter, false);
            if($fieldQuery!=''){
                //$fieldQuery =str_replace('elp', 'gob.elp', $fieldQuery);
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("mfg_date", $filter, true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("exp_date", $filter, true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("fromdate", $filter);
            if($fieldQuery!=''){
                $explodefromdate=explode('=', $fieldQuery);
                $frmdate=isset($explodefromdate[1])?trim($explodefromdate[1]):'';
                if(isset($explodefromdate[1]) && $frmdate!=''){    
                    $makeFinalSql[] = $fieldQuery;
                }
            }

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("todate", $filter);
            if($fieldQuery!=''){
                $explodetodate=explode('=', $fieldQuery);
                $todate=isset($explodetodate[1])?trim($explodetodate[1]):'';
                if(isset($explodetodate[1]) && $todate!=''){    
                    $makeFinalSql[] = $fieldQuery;
                }
            }
           

            $orderBy = $request->input('%24orderby');
            if($orderBy==''){
                $orderBy = $request->input('$orderby');
            }


            $page="";
            $pageSize="";
            if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
                $page = $request->input('page');
                $pageSize = $request->input('pageSize');
            }
                
                $content = $this->_inventory->getBatchHistoryList($makeFinalSql, $orderBy, $page, $pageSize);
                
                return $content;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }   
    }

    public function getBatchIdsBySKU(){
        try{
            $data=Input::all();
            $batchids=$this->_inventory->getBatchIdsBySKU($data['product_id']);
            $resreturn='<option value="">Select Batch ID</option>';
            if(count($batchids)>0){
                for($l=0;$l<count($batchids);$l++) {
                       $resreturn.='<option value="'.$batchids[$l]->main_batch_id. '"> '.$batchids[$l]->main_batch_id.'</option>';
                  }
            }
            return $resreturn;   
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getBatchSkus()
    {
        try{
            $data = \Input::all();
            $skus = $this->_inventory->getBatchSkus($data);
            return $skus;die;
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function getBatchReport(){
        try{
            $data = \Input::all();
            $batchskus = $this->_inventory->getBatchReport($data);
            $batchskus = json_decode(json_encode($batchskus), true);
            Excel::create('Inventory Batch Report - '. date('Y-m-d'),function($excel) use($batchskus) {
                $excel->sheet('Inventory Batch Report', function($sheet) use($batchskus) {          
                $sheet->fromArray($batchskus);
                });      
            })->export('csv');       
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
               
    }
}