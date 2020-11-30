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
use App\Modules\Inventory\Models\InvWriteoff;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use PDF;
use Notifications;
use Mail;
use App\Lib\Queue;
use File;
use DB;
use Illuminate\Support\Facades\Input;
use App\Modules\Inventory\Models\ReadInvWriteoffLogs;
use App\Modules\Inventory\Models\InventorySnapshot;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Central\Repositories\ProductRepo;
use App\Modules\Notifications\Models\NotificationsModel;

class InvWriteoffController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                $this->_inventorySnp = new InventorySnapshot();
                $this->_inventory = new Inventory();
                $this->_readlogs = new ReadInvWriteoffLogs();
                $this->_invWriteoff = new InvWriteoff();
                $this->_approvalFlowMethod     = new CommonApprovalFlowFunctionModel();
                $this->queue = new Queue();
                return $next($request);
            });

           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function writeOffInv(Request $request){
        $wh_list = $this->_inventorySnp->getWhList();
        return View::make('Inventory::invWriteoff',['wh_list'=>$wh_list]);
    }
    public function writeoffDownload(Request $request){
        try{
            $from_date1 = $request->input('from_date');
            $from_date = date('Y-m-d', strtotime($from_date1 .' -1 day'));
            $to_date = $request->input('to_date');
//            log::info("inventory write off from date...".$from_date.'...todate...'.$to_date);
            $wh_id = $request->input('wh_id');
            $token = $request->input('_token');
            $inventory_data = $this->_inventorySnp->invWriteoffData($from_date,$to_date,$wh_id);
            $pre_inv_dnd = array();
            $pre_inv_dit = array();
            $curdayinv_dnd = 0;
            $curdayinv_dit = 0;
            $inv_data_array = array();

            if($inventory_data)
            {
                foreach ($inventory_data as $invKey => $invValue)
                {
                    $pre_date = date('Y-m-d',strtotime($invValue['created_at']));
                    if(strtotime($pre_date) == strtotime($to_date)){
                        $curdayinv_dnd = $invValue['dnd_qty'];
                        $curdayinv_dit = $invValue['dit_qty'];
                        $cur_pre_dnd= ($curdayinv_dnd-$pre_inv_dnd[$invValue['product_id']] >= 0)?$curdayinv_dnd-$pre_inv_dnd[$invValue['product_id']]:0;
                        $cur_pre_dit= ($curdayinv_dit-$pre_inv_dit[$invValue['product_id']] >= 0)?$curdayinv_dit-$pre_inv_dit[$invValue['product_id']]:0;
                        $invData = $this->getInvDitDnd($invValue['product_id'],$wh_id);
                        if(empty($invData))
                        {
                            continue;
                        }
                      /*  if($cur_pre_dit >= 0 || $cur_pre_dnd >= 0)
                        {
                            
                            if(!empty($invData)){
                                 $invDndQty =  $invData[0]['dnd_qty'];
                                $invDitQty =  $invData[0]['dit_qty'];
                              
                                $cur_pre_dnd= ($invDndQty-$cur_pre_dnd >= 0)?$cur_pre_dnd-$cur_pre_dnd:0;
                                $cur_pre_dit= ($invDitQty-$cur_pre_dit >= 0)?$cur_pre_dit-$cur_pre_dit:0;
                            }                           
                        }*/
                        $inv_data_array[$invValue['product_id']] = array("product_id"=>$invValue['product_id'],"product_title"=>$invValue['product_title'],"mrp"=>$invValue['mrp'],"sku"=>$invValue['sku'],"sp"=>$invValue['esp'],"lp"=>$invValue['elp'],"cur_dnd_qty"=>$cur_pre_dnd,"cur_dit_qty"=>$cur_pre_dit);
                        
                    }
                    else  if(strtotime($pre_date) == strtotime($from_date)){
                        $pre_inv_dnd[$invValue['product_id']] = $invValue['dnd_qty'];
                        $pre_inv_dit[$invValue['product_id']] = $invValue['dit_qty'];
                    }

                }
            }
            $headers = array("Product Id","Product Title","MRP","SKU","SP","LP","DIT QTY","DND QTY");
            $mytime = Carbon::now();
            Excel::create('Inventory Writeoff Report'.$mytime->toDateTimeString(), function($excel) use($headers,$inv_data_array,$wh_id,$from_date1,$to_date) 
            {
               $excel->sheet("Writeoff Report", function($sheet) use($headers, $inv_data_array,$wh_id,$from_date1,$to_date)
                {
                    $sheet->loadView('Inventory::writeoffDownload', array('headers' => $headers,'data' => $inv_data_array, 'warehouseId' => $wh_id,'from_date'=>$from_date1,"to_date"=>$to_date)); 

             });
            })->export('xlsx');
        }catch (\ErrorException $ex) {
            print_r($ex->getMessage());
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }
    public function getInvDitDnd($pid,$wh_id)
    {
        try{
            $queryRs =  DB::table('inventory')
                        ->where('product_id',$pid)
                        ->where('le_wh_id',$wh_id)
                        ->select('dit_qty','dnd_qty')
                        ->get()->all();
            $queryRs = json_decode(json_encode($queryRs), true);
            return $queryRs;
        }catch(\ErrorException $ex)
        {
            print_r($ex->getMessage());
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function uploadWriteoff(Request $request)
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

            $excelheaders = array("1" =>"Product Id", "2" => "Product Title", "3" => "MRP", "4" => "SKU", "5" => "SP", "6" => "LP", "7" => "DIT QTY", "8" => "DND QTY");
           
            $explodewarehousedata = explode(":", $warehouseData);
            
            $headingdata = $data1['header_data'];//headers from excel

            $warehouseID = $explodewarehousedata[1];
           
            $productsData = $data1['productsdata'];
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Inventory Write-Off', 'drafted', \Session::get('userId'));
           // log::info("res approval flow function..".$res_approval_flow_func['status']);
            if($res_approval_flow_func['status'] == 0)
            {
                print_r(json_encode(array("no_permission" => "No Permission")));
                die;
            }
            $file_data = Input::file('upload_excel_sheet');
            $url = $productObj->uploadToS3($file_data,'inventory',1);
            //log::info("s3 upload.".$url);
            $upload_data['file_name'] = $file_data->getClientOriginalName();
            $upload_data['file_extension'] = $file_data->getClientOriginalExtension();
            $excelheaders = array_values($excelheaders);
            $headingdata  = array_values($headingdata);  //resetting excel columns
              
            if ($excelheaders != $headingdata) {
                $result = 0;
            } else {
               // Log::info("Calling model method");
                $result = $this->_invWriteoff->updating_inv_writeoff_Values($productsData, $warehouseID, $url);
                $result['linkdownload'] = 'inventorywriteoff/excellogs/'.$result['reference'];
                // $result['linkdownload'] = $file_path;
            }
            Notifications::addNotification(['note_code' => 'INVT001', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Inventory writeoff updation Completed, <a href="/' .$result['linkdownload'] . '" target="_blank">View Details</a>']);
            return $result;
                // $result['linkdownload'] = $file_path;
        }   

    }
    public function readExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->takeColumns(2)->first();
           
          
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'false');
            $headres = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->takeColumns(8)->first();
            
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->takeColumns(8)->get();
            $data['warehouseid'] = $cat_data;
            $data['header_data'] = $headres;
            $data['productsdata'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
    public function readWriteoffExcelLogs($refId){
        try {
            $result = $this->_readlogs->readExcelLogs($refId);
            
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /* Bulk Upload approval workflow */
    public function approvalWorkFlowBulkUpdate($bulk_upload_id)
    {
        try{
            $approval_flow_func= new CommonApprovalFlowFunctionModel();
             $bulkDetails = $this->_invWriteoff->getBulkWriteoffUploadDetails($bulk_upload_id);
            $allprod_ids = array_column($bulkDetails, 'product_id');
            $warehouse = array_unique(array_column($bulkDetails, 'le_wh_id'));
            
            $all_current_inventory = $this->_inventory->getAllCurrentInventory($allprod_ids, $warehouse);

            if(empty($bulkDetails))
            {
                Redirect::to('/')->send();
                    die();
            }
            $approvalStatus = $bulkDetails[0]['approval_status'];
           
            $workflowData = $approval_flow_func->getApprovalFlowDetails('Inventory Write-Off', $approvalStatus, \Session::get('userId'));
        
            $approvalData = isset($workflowData['data'])?$workflowData['data']:"";
            
            $currentStatusId  = isset($workflowData['currentStatusId'])?$workflowData['currentStatusId']:"";
            return view('Inventory::writeoffapproval')->with(["bulkdetails" => $bulkDetails,
                                                                "bulk_upload_id" => $bulk_upload_id,
                                                                "approvalStatus"=>$approvalData, 
                                                                "curr_status_id" => $currentStatusId]);

        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }      
    }
    public function bulkApprovalSubmit(Request $request)
    {

        $notificationsObj = new NotificationsModel();
        $return_value = 0;
        $nextStatus = $request->input('next_status');
        $approval_Comment = $request->input('approval_comment');
        $curr_StatusId = $request->input("current_status_id");  
        $trackId = $request->input("bulk_upload_id");  
        $data_array = array();
        $bulkDetails = $this->_invWriteoff->getBulkWriteoffUploadDetails($trackId);
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
               // Log::info(" i am in final level approval. ".$is_final);
                $param =array();
                $userIds = $notificationsObj->getUsersByCode("INVWRIOFF");
                $AllEmails = $this->_inventory->userEmailsByIds($userIds);
                $getFile =  $this->_inventory->getWriteoffUploadedFile($trackId);
                $getFile = $getFile[0]['filepath'];
                $param['email'] = $AllEmails;
                $param['user'] = $request->session()->get('userId');
                $param['file'] = $getFile;
                $encodedInput = base64_encode(json_encode($param));

                $args = array("ConsoleClass" => 'InventoryFinalApprovalMail', 'arguments' => array('emails'=>$encodedInput));
                $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                 Log::info(" calling model.");
                $tableUpdateID = 1;
                // update the inventory table
                $updateInventory = $this->_invWriteoff->updateInventoryTableforWriteoff($trackId);
                if(!empty($updateInventory))
                {
                    $nextStatusID = 57089;
                    $data_array = $updateInventory;
                }             
           }

            // update the tracking table with ID
            $update_tracking_table = $this->_invWriteoff->updateTrackingTableWithStatusforWriteoff($tableUpdateID, $trackId);

           
            // call approval history function
            $this->_approvalFlowMethod->storeWorkFlowHistory('Inventory Write-Off', $trackId, $curr_StatusId, $nextStatusID, $approval_Comment, \Session::get('userId')); 
            if(!empty($data_array))
                return $data_array;
        }
       
        return $return_value;
    
    }
}
