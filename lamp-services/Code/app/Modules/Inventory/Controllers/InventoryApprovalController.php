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
use App\Modules\Tax\Models\Product;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Lib\Queue;
use DB;

class InventoryApprovalController extends BaseController {

    public $queue;

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }

                $this->_roleRepo = new RoleRepo();
                parent::Title('Inventory Workflow');
                $this->_inventory = new Inventory();
                
                $this->_productClass = new Product();
                $this->_roleRepo = new RoleRepo();
                $this->_approvalFlowMethod= new CommonApprovalFlowFunctionModel();
                $this->queue = new Queue();
                return $next($request);
            });
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function approvalWorkFlowInventoryUpdate($trackingId)
    {
        $inventoryDetails = $this->_inventory->getTrackingDetails($trackingId);
        
        $productId = $inventoryDetails['product_id'];

        $inventoryData = $this->_inventory->getInventoryDetailsBasedOnProductId($productId, $inventoryDetails['le_wh_id']);

        // $oldSoh = $inventoryData[0]['soh'];
        // $newSoh = $oldSoh+$inventoryDetails['stock_diff'];
        $new_inventory_details = array();
        $new_inventory_details['old_dit'] =$inventoryData[0]['dit_qty'];
        $new_inventory_details['new_dit'] =$inventoryData[0]['dit_qty']+$inventoryDetails['dit_diff'] ;
        $new_inventory_details['old_dnd'] =$inventoryData[0]['dnd_qty'] ;
        $new_inventory_details['new_dnd'] =$inventoryData[0]['dnd_qty']+$inventoryDetails['dnd_diff'] ;
        

        $productInfo = $this->_productClass->getProductDetailsofTaxClassCode($productId);


        $inventoryDetails = $this->_inventory->getTrackingDetails($trackingId);

        $approvalStatus = $inventoryDetails['approval_status'];

        $workflowData = $this->_approvalFlowMethod->getApprovalFlowDetails('Inventory', $approvalStatus, \Session::get('userId'));
        $approvalData = isset($workflowData['data'])?$workflowData['data']:"";
        
        $currentStatusId  = isset($workflowData['currentStatusId'])?$workflowData['currentStatusId']:"";
        return view('Inventory::inventoryApproval')->with(
            ['productDetails' => $productInfo[0], 
            "InventoryDetails" => $inventoryDetails, 
            "new_vals" => $new_inventory_details,
            "approvalStatus"=>$approvalData, 
            "curr_status_id" => $currentStatusId, 
            "trackingId" => $trackingId]);

    }
    /* Bulk Upload approval workflow */
    public function approvalWorkFlowBulkUpdate($bulk_upload_id)
    {

        $bulkDetails = $this->_inventory->getBulkUploadDetails($bulk_upload_id);
        $allprod_ids = array_column($bulkDetails, 'product_id');
        $warehouse = array_unique(array_column($bulkDetails, 'le_wh_id'));
        
        $all_current_inventory = $this->_inventory->getAllCurrentInventory($allprod_ids, $warehouse);
        $main_Arr = array();
        foreach ($all_current_inventory as $key => $value) {
            $main_Arr[$value['product_id']] = array("curr_dit_qty"=>$value['dit_qty'], "curr_dnd_qty" => $value['dnd_qty']);
        }
        
        foreach ($bulkDetails as $key => $value) {
            $currditqty=isset($main_Arr[$value['product_id']]['curr_dit_qty'])?$main_Arr[$value['product_id']]['curr_dit_qty']:0;
            $bulkDetails[$key]['curr_dit_qty'] = $currditqty;//$main_Arr[$value['product_id']]['curr_dit_qty'];
            $bulkDetails[$key]['resulted_dit'] = ($currditqty+$value['dit_diff']);
            $currdndqty=isset($main_Arr[$value['product_id']]['curr_dnd_qty'])?$main_Arr[$value['product_id']]['curr_dnd_qty']:0;
            $bulkDetails[$key]['curr_dnd_qty'] = $currdndqty;//$main_Arr[$value['product_id']]['curr_dnd_qty'];
            $bulkDetails[$key]['resulted_dnd'] = ($currdndqty+$value['dnd_diff']);
        }
        
        if(empty($bulkDetails))
        {
            Redirect::to('/')->send();
                die();
        }
        
        $approvalStatus = $bulkDetails[0]['approval_status'];
        // $gettracking_info = $
       //  foreach ($bulkDetails as $value) {
       //  $productId = $value['product_id'];
       //  $inventoryData = $this->_inventory->getInventoryDetailsBasedOnProductId($productId, $value['le_wh_id']);
       //  $oldSoh = $inventoryData[0]['soh'];
       //  $newSoh = $oldSoh+$value['stock_diff'];

       // $oldDit = $inventoryData[0]['dit_qty'];
       //  $newDit = $oldDit+$value['dit_diff'];
        
       //  $oldDnd = $inventoryData[0]['dnd_qty'];
       //  $newDnd = $oldDnd+$value['dnd_diff'];

       //  //Update Tracking Table values
       //  $inventoryMod = new Inventory();
       //  // $inventoryMod->updateBulkInventoryTracking($bulk_upload_id, $oldSoh, $newSoh, $oldDit, $newDit, $oldDnd, $newDnd, $productId, $value['le_wh_id']);

       //  }
        $workflowData = $this->_approvalFlowMethod->getApprovalFlowDetails('Inventory Bulk Upload', $approvalStatus, \Session::get('userId'));
    
        $approvalData = isset($workflowData['data'])?$workflowData['data']:"";
        
        $currentStatusId  = isset($workflowData['currentStatusId'])?$workflowData['currentStatusId']:"";
        
        return view('Inventory::bulkuploadapproval')->with(["bulkdetails" => $bulkDetails,
                                                            "bulk_upload_id" => $bulk_upload_id,
                                                            "approvalStatus"=>$approvalData, 
                                                            "curr_status_id" => $currentStatusId]);

     
    }

    public function approvalSubmit(Request $request)
    {
        $notificationsObj = new NotificationsModel();
        $return_value = 0;
        $nextStatus = $request->input('next_status');
        $approval_Comment = $request->input('approval_comment');
        $curr_StatusId = $request->input("current_status_id");  
        $trackId = $request->input("tracking_id");  
        
        $inventoryDetails = $this->_inventory->getTrackingDetails($trackId);
        
        if($inventoryDetails['approval_status'] == 1)
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
                $param =array();


                // $inventoryDetails = $this->_inventory->getTrackingDetails($trackingId);
        
                $productId = $inventoryDetails['product_id'];
                $productInfo = $this->_productClass->getProductDetailsofTaxClassCode($productId);

                $inventoryData = $this->_inventory->getInventoryDetailsBasedOnProductId($productId, $inventoryDetails['le_wh_id']);

        
                $new_inventory_details = array();
                $new_inventory_details['old_dit']       =$inventoryData[0]['dit_qty'];
                $new_inventory_details['new_dit']       =$inventoryData[0]['dit_qty']+$inventoryDetails['dit_diff'] ;
                $new_inventory_details['old_dnd']       =$inventoryData[0]['dnd_qty'] ;
                $new_inventory_details['new_dnd']       =$inventoryData[0]['dnd_qty']+$inventoryDetails['dnd_diff'] ;
                $new_inventory_details['prod_info']     = $productInfo;




                $userIds = $notificationsObj->getUsersByCode("INVAPPR");
                $AllEmails = $this->_inventory->userEmailsByIds($userIds);
                $inventory_Details = $this->_inventory->getTrackingDetails($trackId);
                $param['email'] = $AllEmails;
                $param['user'] = $request->session()->get('userId');
                $param['bulkupload'] = $inventoryDetails;
                $param['newvals'] = $new_inventory_details;

                


                $encodedInput = base64_encode(json_encode($param));

                $args = array("ConsoleClass" => 'InventoryFinalApprovalMail', 'arguments' => array('emails'=>$encodedInput));
                $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);

                $tableUpdateID = 1;
                // update the inventory table
                $updateInventory = $this->_inventory->updateInventoryTable($trackId);

            }else if($is_final == 0){
                //rejected case
                $updateInventory = $this->_inventory->revertInventoryTable($trackId);
            }


            // update the tracking table with ID
            $update_tracking_table = $this->_inventory->updateTrackingTableWithStatus($tableUpdateID, $trackId);


            // call approval history function
            $this->_approvalFlowMethod->storeWorkFlowHistory('Inventory', $trackId, $curr_StatusId, $nextStatusID, $approval_Comment, \Session::get('userId')); 
        }

        
        return $return_value;
    }

    public function bulkApprovalSubmit(Request $request)
    {
    DB::beginTransaction();
    try{
        $notificationsObj = new NotificationsModel();
        $return_value = 0;
        $nextStatus = $request->input('next_status');
        $approval_Comment = $request->input('approval_comment');
        $curr_StatusId = $request->input("current_status_id");  
        $trackId = $request->input("bulk_upload_id");  
        $data_array = array();
        $bulkDetails = $this->_inventory->getBulkUploadDetails($trackId);
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
                $param =array();
                $userIds = $notificationsObj->getUsersByCode("INVAPPR");
                $AllEmails = $this->_inventory->userEmailsByIds($userIds);
                $getFile =  $this->_inventory->getUploadedFile($trackId);
                $getFile = $getFile[0]['filepath'];
                $param['email'] = $AllEmails;
                $param['user'] = $request->session()->get('userId');
                $param['file'] = $getFile;
                $encodedInput = base64_encode(json_encode($param));

                $args = array("ConsoleClass" => 'InventoryFinalApprovalMail', 'arguments' => array('emails'=>$encodedInput));
                $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);

                $tableUpdateID = 1;
                // update the inventory table
                $updateInventory = $this->_inventory->updateInventoryTableforBulk($trackId);
               if(!empty($updateInventory))
                {
//                    Log::info("SOH transter failed.....");
                    $nextStatusID = 57089;
                    $tableUpdateID = 57089;
                    $data_array = $updateInventory;
                    $updateInventory = $this->_inventory->revertInventoryTableforBulk($trackId);
                    //return $data_array;
                }
            }else if($nextStatusID == 57089){
                //rejected case
                $updateInventory = $this->_inventory->revertInventoryTableforBulk($trackId);
            }

            // update the tracking table with ID
            $update_tracking_table = $this->_inventory->updateTrackingTableWithStatusforBulk($tableUpdateID, $trackId);

          //  Log::info("going to work flow----");
            //            Log::info( $trackId.'__bulkupload id__'.$curr_StatusId.'________current status______'.$nextStatusID.'_____next sid____'.$approval_Comment.'____user token_______'. \Session::get('userId'));
            // call approval history function
            $this->_approvalFlowMethod->storeWorkFlowHistory('Inventory Bulk Upload', $trackId, $curr_StatusId, $nextStatusID, $approval_Comment, \Session::get('userId'));
            DB::commit(); 
            if(!empty($data_array))
                return $data_array;
        }
    }catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            $return_value=2;    
    }
        
        return $return_value;
    
    }

     /* inventory adjustment */
    public function adjApprovalWorkFlowBulkUpdate($bulk_upload_id)
    {

        $bulkDetails = $this->_inventory->getInvAdjustmentBulkTrackingDetails($bulk_upload_id);
        $allprod_ids = array_column($bulkDetails, 'product_id');
        $warehouse = array_unique(array_column($bulkDetails, 'le_wh_id'));
        $all_current_inventory = $this->_inventory->getAllCurrentInventory($allprod_ids, $warehouse);
        $main_Arr = array();
        foreach ($all_current_inventory as $key => $value) {
            $main_Arr[$value['product_id']] = array("curr_dit_qty"=>$value['dit_qty'], "curr_dnd_qty" => $value['dnd_qty'],"soh"=>$value["soh"]);
        }

        foreach ($bulkDetails as $key => $value) {
            $bulkDetails[$key]['curr_dit_qty'] = $main_Arr[$value['product_id']]['curr_dit_qty'];
            $bulkDetails[$key]['resulted_dit'] = ($main_Arr[$value['product_id']]['curr_dit_qty']+$value['dit_diff']);
            $bulkDetails[$key]['curr_dnd_qty'] = $main_Arr[$value['product_id']]['curr_dnd_qty'];
            $bulkDetails[$key]['resulted_dnd'] = ($main_Arr[$value['product_id']]['curr_dnd_qty']+$value['dnd_diff']);
        }
        if(empty($bulkDetails))
        {
            Redirect::to('/')->send();
                die();
        }
               $approvalStatus = $bulkDetails[0]['approval_status'];
             
        $workflowData = $this->_approvalFlowMethod->getApprovalFlowDetails('Inventory Adjustment', $approvalStatus, \Session::get('userId'));
        $approvalData = isset($workflowData['data'])?$workflowData['data']:"";
        
        $currentStatusId  = isset($workflowData['currentStatusId'])?$workflowData['currentStatusId']:"";
        
        return view('Inventory::inventoryAdjustmentApproval')->with(["bulkdetails" => $bulkDetails,
                                                            "bulk_upload_id" => $bulk_upload_id,
                                                            "approvalStatus"=>$approvalData, 
                                                            "curr_status_id" => $currentStatusId]);

     
    }
    /*inv adjustment approval titck */

     public function invAdjApprovalSubmit(Request $request)
    {
    DB::beginTransaction();
    try{
        $notificationsObj = new NotificationsModel();
        $return_value = 0;
        $nextStatus = $request->input('next_status');
        $approval_Comment = $request->input('approval_comment');
        $curr_StatusId = $request->input("current_status_id");  
        $trackId = $request->input("bulk_upload_id");  
        $data_array = array();
        $bulkDetails = $this->_inventory->getInvAdjustmentBulkTrackingDetails($trackId);
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
               
                /*$param =array();
                $userIds = $notificationsObj->getUsersByCode("INVAPPR");
                $AllEmails = $this->_inventory->userEmailsByIds($userIds);
                $getFile =  $this->_inventory->getUploadedFile($trackId);
                $getFile = $getFile[0]['filepath'];
                $param['email'] = $AllEmails;
                $param['user'] = $request->session()->get('userId');
                $param['file'] = $getFile;
                $encodedInput = base64_encode(json_encode($param));

                $args = array("ConsoleClass" => 'InventoryFinalApprovalMail', 'arguments' => array('emails'=>$encodedInput));
                $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
*/
                $tableUpdateID = 1;
                // update the inventory table
                $updateInventory = $this->_inventory->invAdjUpdateInventoryTableforBulk($trackId);
               if(!empty($updateInventory))
                {
          //          Log::info("inv adjustment transter failed.....");
                    $nextStatusID = 57194;
                    $tableUpdateID = 57194;
                    $data_array = $updateInventory;
                   
                    //return $data_array;
                }
            }

            // update the tracking table with ID
            $update_tracking_table = $this->_inventory->updateTrackingTableWithStatusforBulk($tableUpdateID, $trackId);

           // Log::info("going to work flow----".$tableUpdateID);
             //           Log::info( $trackId.'__bulkupload id__'.$curr_StatusId.'________current status______'.$nextStatusID.'_____next sid____'.$approval_Comment.'____user token_______'. \Session::get('userId'));
            // call approval history function
            DB::commit();
            $this->_approvalFlowMethod->storeWorkFlowHistory('Inventory Adjustment', $trackId, $curr_StatusId, $nextStatusID, $approval_Comment, \Session::get('userId')); 
            if(!empty($data_array))
                return $data_array;
        }        
        return $return_value;
    }catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            $return_value=2;
            return $return_value;
        }
    }
}
