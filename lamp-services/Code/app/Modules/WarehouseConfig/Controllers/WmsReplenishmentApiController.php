<?php

namespace App\Modules\WarehouseConfig\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Log;
use DB;
use Input;
use Redirect;
use App\Modules\WarehouseConfig\Models\WmsReplenishmentApiModel;
use App\Central\Repositories\RoleRepo;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Cpmanager\Models\AdminOrderModel;
use Illuminate\Http\Request;
Class WmsReplenishmentApiController extends BaseController
{
  	public function __construct() 
    {   
        $this->categoryModel = new CategoryModel(); 
         $this->_token = new AdminOrderModel();                
        $this->_wmsReplenishmentApiModel = new WmsReplenishmentApiModel();
    } 
    public function reservedReplanishment()
    {
        try {
            $WarehouseConfig = new WmsReplenishmentApiModel();//Pick-Face'109003' || Reserved '109004'
            $reserveToPickface = $WarehouseConfig->reservedReplanishment(109003, 4497);
            $storageToReserve = $WarehouseConfig->reservedReplanishment(109004, 4497);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getReplenishmentList(Request $request) {
        $get_list = json_decode($request->input("get_replenish_assign"), true);
        if($get_list["lp_token"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
        }
        if($get_list["wh_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
        }

        $token_check = $this->_wmsReplenishmentApiModel->authenticatToken($get_list["lp_token"]);
        if(!empty($token_check)){
            if(isset($get_list['replenishment_flow']) && $get_list['replenishment_flow']==2)
                $replenish_list = $this->_wmsReplenishmentApiModel->replenishList($get_list["wh_id"], 2);
            else
                $replenish_list = $this->_wmsReplenishmentApiModel->replenishList($get_list["wh_id"], 1);
            return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($replenish_list) ? "Replenishment list" : "No data", "data" => !empty($replenish_list) ? $replenish_list : array()));
        } else {
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
        }
    }

    public function getAssignedReplenishmentList(Request $request) {
        $get_list = json_decode($request->input("get_replenish_assign"), true);
        if($get_list["lp_token"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
        }
        if($get_list["wh_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
        }

        $token_check = $this->_wmsReplenishmentApiModel->authenticatToken($get_list["lp_token"]);
        if(!empty($token_check)){
            if(isset($get_list['replenishment_flow']) && $get_list['replenishment_flow']==2)
                $replenish_list = $this->_wmsReplenishmentApiModel->getAllAssignList($get_list["wh_id"], 2);
            else
                $replenish_list = $this->_wmsReplenishmentApiModel->getAllAssignList($get_list["wh_id"], 1);
            return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($replenish_list) ? "Replenishment list" : "No data", "data" => !empty($replenish_list) ? $replenish_list : array()));
        } else {
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
        }
    }
    
    public function saveReplenishAssign(Request $request) {
        $save_assign = json_decode($request->input("save_replenish_assign"), true);
        if($save_assign["lp_token"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
        }
        if(empty($save_assign["rack_info"])){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Rack information is missing", "data" => array()));
        }
        if($save_assign["picker_id"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Picker id is missing", "data" => array()));
        }
        if($save_assign["wh_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
        }
        
        $token_check = $this->_wmsReplenishmentApiModel->authenticatToken($save_assign["lp_token"]);
        if(!empty($token_check)){
            if(isset($save_assign['replenishment_flow']) && $save_assign['replenishment_flow']==2)
                $assign_res = $this->_wmsReplenishmentApiModel->saveAssign($save_assign, 2);
            else
                $assign_res = $this->_wmsReplenishmentApiModel->saveAssign($save_assign, 1);
            
            return json_encode(array("status_code" => $assign_res != 0 ? 200 : 400, "status" => $assign_res != 0 ? "Success" : "Failed", "message" => $assign_res != 0 ? "Picker assigned for the replenishment!" : "Something went wrong!", "data" => !empty($assign_res) ? $assign_res : array()));
        } else {
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
        }
    }
    
    public function getAssignedList(Request $request) {
        $assign_list = json_decode($request->input("assign_list"), true);
        if($assign_list["lp_token"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
        }
        if($assign_list["picker_id"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Picker id is missing", "data" => array()));
        }
        if($assign_list["wh_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
        }
        
        $token_check = $this->_wmsReplenishmentApiModel->authenticatToken($assign_list["lp_token"]);
        if(!empty($token_check)){
            if(isset($assign_list['replenishment_flow']) && $assign_list['replenishment_flow']==2)
                $assign_list_res = $this->_wmsReplenishmentApiModel->getAssignList($assign_list, 2);
            else
                $assign_list_res = $this->_wmsReplenishmentApiModel->getAssignList($assign_list, 1);
            return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($assign_list_res) ? "Assigned replenishment list" : "No data", "data" => !empty($assign_list_res) ? $assign_list_res : array()));
        } else {
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
        }
    }
    
    public function saveReplenishQty(Request $request) {
        $save_replenish_qty = json_decode($request->input("save_replenish_qty"), true);
        if($save_replenish_qty["lp_token"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
        }
        if($save_replenish_qty["picker_id"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Picker id is missing", "data" => array()));
        }
        if($save_replenish_qty["wh_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
        }
        if($save_replenish_qty["bin_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Bin id is missing", "data" => array()));
        }
        if($save_replenish_qty["product_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Product id is missing", "data" => array()));
        }
        if($save_replenish_qty["placed_qty"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Placed quantity is missing", "data" => array()));
        }
        if($save_replenish_qty["rack"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Rack is missing", "data" => array()));
        }
        if($save_replenish_qty["replenishment_type"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Replenishment type is missing", "data" => array()));
        }
        if($save_replenish_qty["bin_code"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Bin code is missing", "data" => array()));
        }
        if($save_replenish_qty["replenishment_product_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Replenishment product id is missing", "data" => array()));
        }
        if($save_replenish_qty["source"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Source code is missing", "data" => array()));
        }
        if($save_replenish_qty["source_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Source id is missing", "data" => array()));
        }
        
        $token_check = $this->_wmsReplenishmentApiModel->authenticatToken($save_replenish_qty["lp_token"]);
        if(!empty($token_check)){
            $save_replenish_res = $this->_wmsReplenishmentApiModel->saveReplenishmentQty($save_replenish_qty);
            return json_encode(array("status_code" => $save_replenish_res != 0 ? 200 : 400, "status" => $save_replenish_res != 0 ? "Success" : "Failed", "message" => $save_replenish_res != 0 ? "Replenishment quantity was updated successfully!" : "Something went wrong!", "data" => !empty($save_replenish_res) ? $save_replenish_res : array()));
        } else {
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
        }
    }

    public function getUserCompletedList(Request $request) {
        $assign_list = json_decode($request->input("assign_list"), true);
        if($assign_list["lp_token"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
        }
        if($assign_list["picker_id"] == ""){
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Picker id is missing", "data" => array()));
        }
        if($assign_list["wh_id"] == ""){
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
        }
        
        $token_check = $this->_wmsReplenishmentApiModel->authenticatToken($assign_list["lp_token"]);
        if(!empty($token_check)){
            if(isset($assign_list['replenishment_flow']) && $assign_list['replenishment_flow']==2)
                $completed_list_res = $this->_wmsReplenishmentApiModel->getCompletedList($assign_list, 2);
            else
                $completed_list_res = $this->_wmsReplenishmentApiModel->getCompletedList($assign_list, 1);
            return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($completed_list_res) ? "Assigned replenishment list" : "No data", "data" => !empty($completed_list_res) ? $completed_list_res : array()));
        } else {
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
        }
    }
    
}

