<?php

/*
 * Filename: DailyInventoryReportController.php
 * Description: This file is used for Last Day inventory Report 
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 13th April 2017
 * Modified date: 17th May 2017
 */

/*
 * DailyInventoryReportController is used to manage product inventory
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\InventoryAuditCycleCount\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use PDF;
use Notifications;
use UserActivity;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\Roles\Models\Role;
use App\Modules\InventoryAuditCycleCount\Models\InventoryAuditCycleCountModel;
use App\Modules\Inventory\Models\InventoryDc;
use App\Modules\Inventory\Models\Inventory;
use App\Central\Repositories\ProductRepo;
use App\Modules\InventoryAuditCycleCount\Models\ReadLogs;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;


class InventoryAuditCycleCountController extends BaseController {

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
                $this->_inventoryCC = new InventoryAuditCycleCountModel();
                $this->_inventoryDc = new InventoryDc();
                $this->_inventory = new Inventory();
                $this->_readlogs = new ReadLogs();
                $this->_approvalFlowMethod= new CommonApprovalFlowFunctionModel();
                parent::Title('Inventory Audit - Ebutor');
                return $next($request);
            });

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'Inventory' => url('/inventory/index'), 'Inventory Audit' => url('inventoryauditcc/index'));
            parent::Breadcrumbs($breadCrumbs);
            $options = json_decode(json_encode($this->_inventoryCC->filterOptions()), true);
            return view('InventoryAuditCycleCount::index')->with(['filter_options' => $options]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

public function donwloadTemplateCC(Request $request)
   {
        try {
                 $mytime = Carbon::now();
                $warehouseId = $request->input('warehousenamess_cc');
                $warehousename = $this->_inventoryDc->getWareHouseName($warehouseId);
                $allproductsdata = $this->_inventoryCC->getAllProducts($warehouseId);
                $allproductsdata = json_decode(json_encode($allproductsdata), true);
                $excelheaders = array("Category Name","Manufacturer Name", "Brand Name", "MRP", "SOH","Product ID", "Product Title", "Product Group ID", "SKU", "Users");
                $users = $this->_inventoryCC->getUsers();
                Excel::create('Inventory-CycleCount' . $mytime->toDateTimeString(), function($excel) use($warehousename, $allproductsdata, $excelheaders, $warehouseId, $users) {
                    //get all warehouses
                    $excel->setTitle('Inventory');
                    $excel->setDescription('Product Information');
                    $excel->sheet($warehousename, function($sheet) use($allproductsdata,  $excelheaders, $warehouseId) 
                    {
                        $sheet->loadView('InventoryAuditCycleCount::downloadExcel' ,array('headers' => $excelheaders, 'products_info' => $allproductsdata, 'warehouseId' => $warehouseId));
                    }); 

                    $excel->sheet("Users", function($sheet) use($users) 
                    {
                        $sheet->loadView('InventoryAuditCycleCount::users' ,array('users' => $users));
                    }); 
                })->export('xls');
        } catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
        }
   }

   public function downloadTemplateST(Request $request)
   {
        try {
                 $mytime = Carbon::now();
                $warehouseId = $request->input('warehousenamess_st');
                $warehousename = $this->_inventoryDc->getWareHouseName($warehouseId);
                
                $allproductsdata = $this->_inventoryCC->getAllProductsForST($warehouseId);
                $allproductsdata = json_decode(json_encode($allproductsdata), true);

                $excelheaders = array("Category Name","Manufacturer Name", "Brand Name", "MRP","SOH", "Product ID", "Product Title", "Product Group ID", "SKU", "Bin","Users");
                $users = $this->_inventoryCC->getUsers();
                Excel::create('Inventory-Stock-Take' . $mytime->toDateTimeString(), function($excel) use($warehousename, $allproductsdata, $excelheaders, $warehouseId, $users) {
                    //get all warehouses
                    $excel->setTitle('Inventory');
                    $excel->setDescription('Product Information');
                    $excel->sheet($warehousename, function($sheet) use($allproductsdata,  $excelheaders, $warehouseId) 
                    {
                        // $sheet->fromArray($getproductInfo);
                        $sheet->loadView('InventoryAuditCycleCount::downloadExcelStockTake' ,array('headers' => $excelheaders, 'products_info' => $allproductsdata, 'warehouseId' => $warehouseId));
                    }); 

                    $excel->sheet("Users", function($sheet) use($users) 
                    {
                        // $sheet->fromArray($getproductInfo);
                        $sheet->loadView('InventoryAuditCycleCount::users' ,array('users' => $users));
                    }); 
                })->export('xls');
        } catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
        }
   }


    public function excelUpload(Request $request)
    {
        try {
                $productObj = new ProductRepo();

                if (Input::hasFile('upload_excel_sheet')) {
                        $path = Input::file('upload_excel_sheet')->getRealPath();
                        $data = $this->readExcel($path);
                        $data1 = json_decode(json_encode($data), true);
                        $file_path = "";
                        $warehouseData = '';
                        if(isset($data1['warehouseid'])){
                                $warehouseData = reset($data1['warehouseid']);
                        }

                        $excelheaders = array("Category Name","Manufacturer Name", "Brand Name","MRP", "SOH","Product ID", "Product Title", "Product Group ID", "SKU", "Users");
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
                            
                            $result = $this->_inventoryCC->insertInventoryAudit($productsData, $warehouseID, $url);
                            
                            $result['linkdownload'] = 'inventoryauditcc/excellogsaudit/'.$result['reference'];
                        }
                        // Notifications::addNotification(['note_code' => 'INVT001', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Inventory updation Completed, <a href="/' .$result['linkdownload'] . '" target="_blank">View Details</a>']);
                        print_r(json_encode($result));
                    }
           
            } catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
            }
        
        
    }

    public function excelUploadStockTake(Request $request)
    {
        try {
                $productObj = new ProductRepo();

                if (Input::hasFile('upload_excel_sheet')) {
                    $path = Input::file('upload_excel_sheet')->getRealPath();
                    $data = $this->readExcel($path);
                    $data1 = json_decode(json_encode($data), true);
                    $file_path = "";
                    $warehouseData = '';
                    if(isset($data1['warehouseid'])){
                            $warehouseData = reset($data1['warehouseid']);
                    }

                    
                    $excelheaders = array("Category Name","Manufacturer Name", "Brand Name", "MRP","SOH", "Product ID", "Product Title", "Product Group ID", "SKU", "Bin","Users");

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
                        // echo "<pre>";print_r($productsData);die;
                        $result = $this->_inventoryCC->insertInventoryAuditStockTake($productsData, $warehouseID, $url);
                        $result['linkdownload'] = 'inventoryauditcc/excellogsaudit/'.$result['reference'];
                        
                    }
                    // Notifications::addNotification(['note_code' => 'INVT001', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Inventory updation Completed, <a href="/' .$result['linkdownload'] . '" target="_blank">View Details</a>']);
                    print_r(json_encode($result));
                }
           
            } catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
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

    public function readExcelAudit($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['header_data'] = $cat_data;
            $data['data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function readExcelLogsAudit($refId)
    {
        try {
            $result = $this->_readlogs->readExcelLogsAudit($refId);
            
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function viewApprovalTicket($audit_id, $status="")
    {
        try {
            
            $bulkDetails = $this->_inventoryCC->getStatusByAuditID($audit_id);
            $approvalStatus = $bulkDetails[0]['approval_status'];
            $all_status_counts = $this->_inventoryCC->getAllStatusCounts($audit_id);
            $audit_data = $this->_inventoryCC->getBulkDataByAuditID('view', $audit_id, $status);
            $error_Counter = $this->_inventoryCC->getAllItemsApprovedCount($audit_id);
            
            $workflowData = $this->_approvalFlowMethod->getApprovalFlowDetails('Inventory Bulk Audit', $approvalStatus, \Session::get('userId'));
                        
            $approvalData = isset($workflowData['data'])?$workflowData['data']:"";
            $currentStatusId  = isset($workflowData['currentStatusId'])?$workflowData['currentStatusId']:"";
            
            return view('InventoryAuditCycleCount::viewapprovalticket')
                            ->with(['audit_data' => $audit_data,
                                    'audit_id' => $audit_id,
                                    'all_status_counts' => $all_status_counts, 
                                    'status' => $status,
                                    'approvalStatus'=>$approvalData, 
                                    'curr_status_id' => $currentStatusId,
                                    'error_counter' => $error_Counter
                                   ]);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function auditApproval($audit_id)
    {
        try {
            $mytime = Carbon::now();
            $audit_data = $this->_inventoryCC->getBulkDataByAuditID('download', $audit_id);
            $excelheaders = array(
                                    "Warehouse Id",
                                    "Product Id", 
                                    "Product Name",
                                    "SKU", 
                                    "MRP",
                                    "ELP",
                                    "Opening Balance",
                                    "SOH", 
                                    "Pending Return Qty", 
                                    "Purchase Returns", 
                                    "Picked qty", 
                                    "Quarantine Qty", 
                                    "Location",
                                    // "New Location",
                                    "Bin Qty",
                                    "Updated By",
                                    "Good Qty",
                                    "Damaged Qty",
                                    "Damaged ELP",
                                    "Expired Qty",
                                    "Expired ELP",
                                    "Short Qty",
                                    "Short ELP",
                                    "Excess Qty",
                                    "Excess ELP",
                                    "Current Bin Qty",
                                    "Deviation Value",
                                    "Approved Good Qty",
                                    "Approved Damaged Qty",
                                    "Approved Expired Qty"
                                    // ,"Approved Missing Qty",
                                    // "Approved Excess Qty"
                                    );

            $warehousename = "Inventory-audting";
        Excel::create('Inventory-Audit-Template' . $mytime->toDateTimeString(), function($excel) use($audit_data, $excelheaders, $warehousename, $audit_id) {
            //get all warehouses
            $excel->setTitle('Inventory');
            $excel->setDescription('Product Information');
            $excel->sheet($warehousename, function($sheet) use($audit_data,$excelheaders, $audit_id) 
            {
                // $sheet->fromArray($getproductInfo);
                $sheet->loadView('InventoryAuditCycleCount::downloadexcelauditdata' ,array('headers' => $excelheaders, 'products_info' => $audit_data, "audit_id" => $audit_id));
            }); 


        })->export('xls');
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }



    public function closedTicketData($audit_id)
    {
        try {
            $mytime = Carbon::now();
            $audit_data = $this->_inventoryCC->getBulkDataByAuditIDClosedTkts('downloaddata', $audit_id);
            $excelheaders = array(
                                    "Warehouse Id",
                                    "Product Id", 
                                    "Product Name",
                                    "SKU", 
                                    "MRP",
                                    "ELP",
                                    "Opening Balance",
                                    "SOH", 
                                    "Pending Return Qty", 
                                    "Purchase Returns", 
                                    "Picked qty", 
                                    "Quarantine Qty", 
                                    "Location",
                                    "New Location",
                                    "Bin Qty",
                                    "Updated By",
                                    "Good Qty",
                                    "Damaged Qty",
                                    "Damaged ELP",
                                    "Expired Qty",
                                    "Expired ELP",
                                    "Short Qty",
                                    "Short ELP",
                                    "Excess Qty",
                                    "Excess ELP",
                                    "Current Bin Qty",
                                    "Approved Good Qty",
                                    "Approved Damaged Qty",
                                    "Approved Expired Qty"
                                    // ,"Approved Missing Qty",
                                    // "Approved Excess Qty"
                                    );

            $warehousename = "Inventory-audting";
        Excel::create('Inventory-Audit-Closed' . $mytime->toDateTimeString(), function($excel) use($audit_data, $excelheaders, $warehousename, $audit_id) {
            //get all warehouses
            $excel->setTitle('Inventory');
            $excel->setDescription('Product Information');
            $excel->sheet($warehousename, function($sheet) use($audit_data,$excelheaders, $audit_id) 
            {
                // $sheet->fromArray($getproductInfo);
                $sheet->loadView('InventoryAuditCycleCount::downloadexcelauditdata_closedtkts' ,array('headers' => $excelheaders, 'products_info' => $audit_data, "audit_id" => $audit_id));
            }); 


        })->export('xls');
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }




    public function uploadAppovalWorkFlowSheet(Request $request)
    {
        try {
                $audit_id = $request->input('audit_id');

                $productObj = new ProductRepo();

                if (Input::hasFile('upload_excel_sheet')) {
                        $path = Input::file('upload_excel_sheet')->getRealPath();
                        $data = $this->readExcel($path);
                        $data1 = json_decode(json_encode($data), true);
                        
                        $file_path = "";
                        $warehouseData = '';
                        if(isset($data1['warehouseid'])){
                                $auditInfo = reset($data1['warehouseid']);
                        }
                        
                        $explode_Data = explode(":", $auditInfo);
                        $excel_audit_id = $explode_Data[1];
                        
                        $excelheaders = array(
                                            "Warehouse Id",
                                            "Product Id", 
                                            "Product Name",
                                            "SKU", 
                                            "MRP",
                                            "ELP",
                                            "Opening Balance",
                                            "SOH", 
                                            "Pending Return Qty",  
                                            "Purchase Returns", 
                                            "Picked qty", 
                                            "Quarantine Qty", 
                                            "Location",
                                            // "New Location",
                                            "Bin Qty",
                                            "Updated By",
                                            "Good Qty",
                                            "Damaged Qty",
                                            "Damaged ELP",
                                            "Expired Qty",
                                            "Expired ELP",
                                            "Short Qty",
                                            "Short ELP",
                                            "Excess Qty",
                                            "Excess ELP",
                                            "Current Bin Qty",
                                            "Deviation Value",
                                            "Approved Good Qty",
                                            "Approved Damaged Qty",
                                            "Approved Expired Qty"
                                            // ,"Approved Missing Qty",
                                            // "Approved Excess Qty"
                                        );
                        
                        
                        $headingdata = $data1['header_data'];//headers from excel
                        $headingdata = array_values($headingdata);
                        
                        $auditData = $data1['productsdata'];
                        
                        if($audit_id != $excel_audit_id)
                        {
                            $result = 2;
                            return $result;
                        }
                        if ($excelheaders != $headingdata) {
                            $result = 0;
                        } else {
                            $file_data = Input::file('upload_excel_sheet');
                            $url = $productObj->uploadToS3($file_data,'inventory',1);
                            
                            $upload_data['file_name'] = $file_data->getClientOriginalName();
                            $upload_data['file_extension'] = $file_data->getClientOriginalExtension();
                            
                            $result = $this->_inventoryCC->updateInventoryAuditData($audit_id, $auditData, $url);
                            // $result = "<table><tr> <td>data1</td> <td>data2</td> <td>data3</td> </tr></table>";
                            // $result['linkdownload'] = 'inventoryauditcc/excellogsaudit/'.$result['reference'];
                        }
                        // Notifications::addNotification(['note_code' => 'INVT001', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Inventory updation Completed, <a href="/' .$result['linkdownload'] . '" target="_blank">View Details</a>']);
                        // print_r(json_encode($result));
                    }
           
            return $result;
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function submitApprovalStatus(Request $request)
    {
        try {
            
        $return_value = 0;
        $nextStatus = $request->input('next_status');
        $approval_Comment = $request->input('approval_comment');
        $curr_StatusId = $request->input("current_status_id");  
        $trackId = $request->input("bulk_upload_id");  

        $bulkDetails = $this->_inventoryCC->getStatusByAuditID($trackId);
        
        if($bulkDetails[0]['approval_status'] == 1)
        {
            $return_value = 1;
        }else
        {
            $explode_data = explode(",", $nextStatus);
            
            $nextStatusID = $explode_data[0];
            $is_final = $explode_data[1];

            $tableUpdateID = $nextStatusID;
            
            if($is_final == 1)
            {
                $tableUpdateID = 1;
                // update the inventory table
                $updateInventory = $this->_inventoryCC->updateBinInventory($trackId, "approved");
               
            }

            // // update the tracking table with ID
            $update_bulk_table = $this->_inventoryCC->updateBulkAuditTablewithStatus($tableUpdateID, $trackId);

            // call approval history function
            $this->_approvalFlowMethod->storeWorkFlowHistory('Inventory Bulk Audit', $trackId, $curr_StatusId, $nextStatusID, $approval_Comment, \Session::get('userId')); 
        }


        return $return_value;
    
    
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getAllClosedTickets(Request $request)
    {
        try {
            $raw_Data = $request->input('filters');
            $raw_Data = json_decode($raw_Data[0], true);
            $from_Date = $raw_Data['from_date'];
            $to_Date = $raw_Data['to_date'];
            $getclosed_tickets = $this->_inventoryCC->getAllClosedTicketsBetweenDates($from_Date, $to_Date);
            return $getclosed_tickets;
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function downloadTicket($tktId)
    {
        try {
            
                $audit_data = $this->_inventoryCC->getBulkDataByAuditID('download', $tktId);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }


    public function allOpenTickets(Request $request)
    {
        try {
                $page           = $request->input('page');   //Page number
                $pageSize       = $request->input('pageSize'); //Page size for ajax call
                $opentktsData = $this->_inventoryCC->allOpenTkts($page, $pageSize);
                $opentktsData = json_decode(json_encode($opentktsData), true);
                
                foreach ($opentktsData['result'] as $key => $value) {
                    
                    $opentktsData['result'][$key]['link'] = "<a class = 'linkclass' href='/inventoryauditcc/opentkt/".$value['bulk_audit_id']."' target = '_blank'><u>".$value['audit_code']."</u></a>";
                    // $opentktsData[$key]['Audit_Code'] = $value['bulk_audit_id'];
                }
                
                echo json_encode(array('results' => $opentktsData['result'], 'TotalRecordsCount' => $opentktsData['count']));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function openTicketInfo($tktid)
    {
        try {
            $getTketInfo = $this->_inventoryCC->getTicketInfo($tktid);
            if($getTketInfo == "completed")
            {
                Redirect::to('/inventoryauditcc/index')->send();
            }
            return view('InventoryAuditCycleCount::openTktInfo')->with(['data' => $getTketInfo]);
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

}

?>
