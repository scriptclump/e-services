<?php

namespace App\Modules\InventoryAudit\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\InventoryAudit\Models\InventoryAudit;

use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Roles\Models\Role;
use App\Modules\Inventory\Models\Inventory;
use DB;
use UserActivity;
class InventoryAuditController extends BaseController {

    public function __construct() {
        try {
            parent::Title('Inventory Audit - Ebutor');
            $this->_inventoryAudit = new InventoryAudit();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getProductList(Request $request) {
        try {
            $prodlist_params = json_decode($request->input("prodlist_params"), true);
            if($prodlist_params["lp_token"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($prodlist_params["wh_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            if($prodlist_params["type"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Type is missing", "data" => array()));
            }
            if($prodlist_params["limit"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Limit is missing", "data" => array()));
            }
            
            $token_check = $this->_inventoryAudit->authenticatToken($prodlist_params["lp_token"]);
            if(!empty($token_check)){
                $res_prodlist = $this->_inventoryAudit->getProductList($prodlist_params["wh_id"], $prodlist_params["type"], $prodlist_params["limit"]);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_prodlist) ? "Products list" : "No data", "data" => !empty($res_prodlist) ? $res_prodlist : array()));
            } else {
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function savePickerAssignment(Request $request) {
        try {
            $picker_assign_params = json_decode($request->input("saveassign_params"), true);
            if($picker_assign_params["lp_token"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($picker_assign_params["wh_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            if($picker_assign_params["type"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Type is missing", "data" => array()));
            }
            if($picker_assign_params["user_id"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "User id is missing", "data" => array()));
            }
            if($picker_assign_params["picker_id"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Picker id is missing", "data" => array()));
            }
            if(empty($picker_assign_params["product_info"])){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Product info is missing", "data" => array()));
            }
            
            $token_check = $this->_inventoryAudit->authenticatToken($picker_assign_params["lp_token"]);
            if(!empty($token_check)){
                $res_assign = $this->_inventoryAudit->insertPickerAssignment($picker_assign_params);
                if($res_assign == "bin"){
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Didn't found the bin configuration for the selected product & warehouse", "data" => array()));
                } else {
                    return json_encode(array("status_code" => 200, "status" => !empty($res_assign) ? "Success" : "Failed", "message" => !empty($res_assign) ? "Picker assigned for the selected products!" : "Something went wrong!", "data" => !empty($res_assign) ? $res_assign : array()));
                }
            } else {
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getPickerAssignment(Request $request) {
        try {
            $get_picker_assign = json_decode($request->input("get_assign_params"), true);
            if($get_picker_assign["lp_token"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($get_picker_assign["picker_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Picker id is missing", "data" => array()));
            }
            if($get_picker_assign["type"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Type is missing", "data" => array()));
            }
            
            $token_check = $this->_inventoryAudit->authenticatToken($get_picker_assign["lp_token"]);
            if(!empty($token_check)){
                $get_assign = $this->_inventoryAudit->getPickerAssignment($get_picker_assign);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($get_assign) ? "Products list" : "No data", "data" => !empty($get_assign) ? $get_assign : array()));
            } else {
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function updateAudit(Request $request) {
        try {
            $update_params = json_decode($request->input("audit_update_params"), true);
            if($update_params["lp_token"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($update_params["wh_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            if($update_params["type"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Type is missing", "data" => array()));
            }
            if($update_params["auditor"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Auditor id is missing", "data" => array()));
            }
            if($update_params["product_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Product id is missing", "data" => array()));
            }
            
            $token_check = $this->_inventoryAudit->authenticatToken($update_params["lp_token"]);
            if(!empty($token_check)){
                $update_res = 0;
                if(count($update_params['bin_info'])>0){
                    foreach($update_params['bin_info'] as $eachBin){
                        $temp = array();
                        $temp['wh_id'] = $update_params['wh_id'];
                        $temp['auditor'] = $update_params['auditor'];
                        $temp['type'] = $update_params['type'];
                        $temp['product_id'] = $update_params['product_id'];
                        $temp = array_merge($temp, $eachBin);
                        $update_res += $this->_inventoryAudit->updateAuditDetails($temp);
                    }
                    return json_encode(array("status_code" => 200, "status" => !empty($update_res) ? "Success" : "Failed", "message" => !empty($update_res) ? "Update successfull!" : "Something went wrong!", "data" => !empty($update_res) ? $update_res : array()));
                }
                
            } else {
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            echo $ex;
        }
    }

    public function getAvailableLocations(Request $request)
    {
        try {
            $locationsArr = array();
            $update_params = json_decode($request->input("params"), true);
            $update_params["lp_token"] = isset($update_params["lp_token"])?$update_params["lp_token"]:"";
            $update_params["wh_id"] = isset($update_params["wh_id"])?$update_params["wh_id"]:"";
            $update_params["product_id"] = isset($update_params["product_id"])?$update_params["product_id"]:"";
            if($update_params["lp_token"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($update_params["wh_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            if($update_params["product_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Product id is missing", "data" => array()));
            }
            
            $token_check = $this->_inventoryAudit->authenticatToken($update_params["lp_token"]);
            if(!empty($token_check)){
            $available_locs  = $this->_inventoryAudit->getAvaliableLocations($update_params);
            foreach ($available_locs as $key => $value) {
                $locationsArr[] = $value['wh_location'];
            }
            $locationsmainArr['locations'] = $locationsArr;
            
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => $locationsmainArr));
            } else {
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getAvailableSOH(Request $request){
        try{
        $soh_details = json_decode($request->input("get_soh_data"), true);
        // check the warehouse id 
        $soh_details['lp_token'] = isset($soh_details['lp_token']) ? $soh_details['lp_token'] : "";
        $soh_details['wh_id'] = isset($soh_details['wh_id']) ? $soh_details['wh_id'] : "";
        $soh_details['product_id'] =isset($soh_details['product_id']) ? $soh_details['product_id'] : "";        

        if($soh_details['lp_token'] == ""){
            return array("status_code" => 400, "status" => "Failed", "message" => "lp token is missing", "data" => array());
        }
        if($soh_details['wh_id'] == ""){
            return array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array());
        }
        if($soh_details['product_id'] == ""){
            return array("status_code" => 400, "status" => "Failed", "message" => "Product id is missing", "data" => array());
        }

        // check the lp token in database
        $token_check = $this->_inventoryAudit->authenticatToken($soh_details['lp_token']);
            //echo "<pre/>";print_r($token_check);exit;
            if(!empty($token_check)){

         $getdata  = $this->_inventoryAudit->getsohdetails($soh_details);
         return json_encode(array("status_code" => 200, "status" => "Success", "message" => $getdata));

        }else {
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }


        }catch(\ErrorException $ex){
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }

     public function getpickerassigmentwithSOH(Request $request) {
        try {
            $get_picker_assign = json_decode($request->input("get_assign_params"), true);
            if($get_picker_assign["lp_token"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($get_picker_assign["picker_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Picker id is missing", "data" => array()));
            }
            if($get_picker_assign["type"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Type is missing", "data" => array()));
            }
            
            $token_check = $this->_inventoryAudit->authenticatToken($get_picker_assign["lp_token"]);
            if(!empty($token_check)){
                $get_assign = $this->_inventoryAudit->getPickerAssignmentWithSOHData($get_picker_assign);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($get_assign) ? "Products list" : "No data", "data" => !empty($get_assign) ? $get_assign : array()));
            } else {
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    // this api is used for update the soh and dit and quarantine with workflow

    public function saveallSOHandDITwithMobileApi(Request $request){
        try{
            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            $get_api_data = json_decode($request->input("data"), true);
            if($get_api_data["lp_token"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($get_api_data["wh_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            if($get_api_data["picker_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Picker Id missing", "data" => array()));
            }

            $user_id = $this->_inventoryAudit->authenticatToken($get_api_data["lp_token"]);
            //echo "<pre/>";print_r($user_id);exit;
            if(empty($user_id)){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "user id missing", "data" => array()));
            }

            if(!empty($user_id)){
                $get_api_data["user_id"] = $user_id[0];
                $data = $this->updating_SOH_ATP_Values($get_api_data['prod_info'],$get_api_data);
                
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => $data));
                // first written code
           // $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Inventory Bulk Upload',57085,$user_id[0]);

           /*if($res_approval_flow_func['status'] == 0){
            $msg=  "You Don't Have Permission To insert details";
            return json_encode(array("status_code" => 400, "status" => "Failed", "message" =>$msg, "data" => array()));
            }*/

            /*$approvalData = isset($res_approval_flow_func['data'])?$res_approval_flow_func['data']:"";
            foreach($approvalData as $data){
                $NextStatusId  = isset($data['nextStatusId'])?$data['nextStatusId']:"";
            }
            $currentStatusId  = isset($res_approval_flow_func['currentStatusId'])?$res_approval_flow_func['currentStatusId']:"";*/

            //save the details in bulk upload table
            /*$savedetails = array(
                            'activity_date'         =>date('y-m-d'),
                            'remarks'               =>'inventory checking by picker without upload sheet',
                            'approved_by'           => $user_id[0], 
                            'approval_status'       => $currentStatusId,                          
                            'created_by'            => $user_id[0],
            );*/
        //$bulk_upload_id = $this->_inventoryAudit->saveIntoinventorybulktable($savedetails);
           // $uploaddetails = array();
            /*foreach($get_api_data['prod_info'] as $data){
                $uploaddetails  = array(
                    'product_id'    =>$data['product_id'],
                    'le_wh_id'      =>$get_api_data["wh_id"],
                    'activity_date' =>date('y-m-d'),
                    'activity_type' =>117001,
                    'new_soh'       =>$data['product_id'],
                    'new_dit_qty'   =>$data['missingqty'],
                    'new_dnd_qty'   =>$data['damagedqty'],
                    'excess'        =>$data['excessqty'],
                    'bulk_upload_id' => $bulk_upload_id,
                    'approved_by'     => $user_id[0],
                    'approval_status'   =>$currentStatusId,
                );
        $stockdetails = $this->_inventoryAudit->saveIntotrackingtable($uploaddetails);
        $approvalDataResp =  $approval_flow_func->storeWorkFlowHistory("Inventory Bulk Upload",$bulk_upload_id, $currentStatusId, $NextStatusId, "Inventory uploaded from mobile", $user_id[0]);


        return json_encode(array("status_code" => 200, "status" => "Success", "message" => $approvalDataResp));
            }*/
        }else {
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        }catch(\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());

        }

    }


    // this function same like as excel upload

    public function updating_SOH_ATP_Values($productsData, $warewhouseid, $URL="")
    {
        //echo "<pre/>";print_r($productsData);exit;
        $approval_flow_func     = new CommonApprovalFlowFunctionModel();
        $rolesObj               = new Role();
        $errorArray             = array();
        $mainArr                = array();
        $updateCounter          = 0;
        $inventory              = new Inventory();
       // $allproductsForUSer     = json_decode($rolesObj->getFilterData(9, $warewhouseid['user_id']), true);

        //$allproductIds          = $allproductsForUSer['products'];
       // $getallWarehouseIds     = $this->filterOptions();
        $i=1;
        $timestamp              = date('Y-m-d H:i:s');
        $current_timeStamp      = strtotime($timestamp);
        $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Inventory Bulk Upload', 'drafted', $warewhouseid['user_id']);
        //echo "<pre/>";print_r($res_approval_flow_func);exit;
        $curr_status_ID         = $res_approval_flow_func['currentStatusId'];
        $nextlevelStatusId      = $res_approval_flow_func['data'][0]['nextStatusId'];
        $insert_array = array("filepath"                => $URL,
                               "approval_status"         => $nextlevelStatusId,
                               "created_by"              => $warewhouseid['user_id'],
                               "remarks"                 => "cycle count from mobile"
                            );
        $bulk_upload_id = DB::table("inventory_bulk_upload")->insertGetId($insert_array);
        $negvalinvupload = array();
        $cnt_inv = 3;
        $neg_cnt =0;
//$negvalinvupload["negative_values"] = 'Negative Values';
        //echo $warewhouseid['wh_id'];exit;
        foreach ($productsData as $excelValue) {
              //  if($excelValue['reason']!="") {
                $product_ID         = $excelValue['product_id'];
$getinventory_values = $this->_inventoryAudit->getInventoryDetailsBasedOnProductIdForSoh($product_ID, $warewhouseid['wh_id']);
//echo "<pre/>";print_r($getinventory_values['soh']);exit;
                $inventory_table_soh = $getinventory_values['soh'];
                $inventory_table_dit_qty = $getinventory_values['dit_qty'];
                $inventory_table_dnd_qty = $getinventory_values['dnd_qty'];
                $inventory_table_quarantine = $getinventory_values['quarantine_qty'];  

                $exce_quarantine_qty = $excelValue['missingqty']+$excelValue['expiredqty'];
                $excel_dit = $excelValue['damagedqty'];
                $excel_dnd = $excelValue['missingqty'];
                $excel_excess = $excelValue['excessqty'];

                
                $resulted_SOH       = $inventory_table_soh - $exce_quarantine_qty +$excel_excess;
                $resulted_dit_qty   = $inventory_table_dit_qty + $excel_dit;

                $resulted_dnd_qty   = $inventory_table_dnd_qty + $excel_dnd - $excel_excess;

                //echo $resulted_SOH;exit;
                if($resulted_dnd_qty <0 || $resulted_dit_qty <0 || $resulted_SOH <0 ){
                    Log::info("count__".$cnt_inv);
                    $resulted_quaratine_qty = $getinventory_values['quarantine_qty'];
                    $errorArray['wrongCombination'][] = " Product Does not existed for this warehouse <br>";
                    $product_ID = DB::table("products")->where("product_id",$product_ID)->pluck('product_title');
                    $negvalinvupload["negative_values"][] =" Inventory negative values. Product Name #".$product_ID[0]." SOH # ".$resulted_SOH." DIT QTY # ".$resulted_dit_qty." DND QTY # ".$resulted_dnd_qty." Quarantine QTY # ".$resulted_quaratine_qty;
                    
                }
                $neg_cnt++;
           // }
            $cnt_inv++;
           
        }
        //echo "asda";exit;
        //print_r($productsData);
       //echo "<pre/>";print_r($negvalinvupload);exit;
       //die();
        if(!empty($negvalinvupload))
        {
           // echo "gingg";
            $mainArr['success'] = $negvalinvupload;
           // $mainArr['error_count'] = (isset($negvalinvupload['negative_values'])?count($negvalinvupload['negative_values']):0);
           // $mainArr['reference'] = $current_timeStamp;
            //$mainArr['updated_count']            = $neg_cnt;
            //$mainArr['duplicate_count']            = 0;
            //Log::info($mainArr);
            $log_array = $mainArr;

            return $mainArr;
            //UserActivity::excelUploadFileLogs("INVENTORY", $current_timeStamp, $URL, $log_array);
            //return $mainArr;
        }
        //echo "<pre/>";print_r($productsData);exit;
        foreach ($productsData as $value) {
        //echo "sfsdfs";exit;
           $countWareAndProductId = $inventory->checkWareHouseAndProductId($warewhouseid['wh_id'], $value['product_id']);
           //echo "dfs";exit;
     //echo "<pre/>";print_r($countWareAndProductId);exit;
//echo "<pre/>";print_r($value);
//echo "<pre/>";print_r($countWareAndProductId);exit;
           /*if($countWareAndProductId < 1)
           {
                $errorArray['wrongCombination'][] = 'Line #' . ($i+2)." Product Does not existed for this warehouse <br>";
                $i++;
                continue;
           }*/

          // echo "i ma here";

          // echo "<pre/>";print_r($value);exit;
           /* if(!isset($allproductIds[$value['product_id']]))
            {
                $errorArray['productIderrors'][] = 'Line #' . ($i+2)." Invalid Product Ids <br>";
                $i++;
                continue;
            }*/
            //checyking all inputs weather it is empty or not
           /* if(empty($value['excessqty']) && empty($value['damagedqty']) && empty($value['missingqty']))
            {
                $errorArray['commenterrors'][] = 'Line #' . ($i+2)." Blank inputs  <br>";
                $i++;
                continue;
            }
            if($value['excessqty'] == 0 && $value['damagedqty'] == 0 && $value['missingqty'] == 0)
            {
                $errorArray['commenterrors'][] = 'Line #' . ($i+2)." Blank inputs  <br>";
                $i++;
                continue;
            }*/
           /* if($value['comments'] == "" || empty($value['comments']))
           {
                $errorArray['commenterrors'][] = 'Line #' . ($i+2)." Empty commments <br>";
                $i++;
                continue;
           }
            if($value['reason'] == "" || empty($value['reason']))
           {
                $errorArray['reasonerrors'][] = 'Line #' . ($i+2)." Empty Reasons <br>";
                $i++;
                continue;
           }*/

           /*$check_valid_reason_or_not = $inventory->getReasonCodeBasedOnReasonType($value['reason']);
           if($check_valid_reason_or_not[0] == 0)
           {
                $errorArray['reason_mismatch_errors'][] = 'Line #' . ($i+2)." Invalid Reasons <br>";
                $i++;
                continue;
           }*/
           
           /*if((!is_int($value['prodsoh']) && ($value['prodsoh']!='')) || ($value['prodsoh'] < 0 && ($value['prodsoh']!='') ))
           {
                $errorArray['soherrors'][] = 'Line #' . ($i+2)." Invalid data in SOH <br>";
                $i++;
                continue;
           }*/
           /*Log::info("i m in exce____".$value['soherrors']);
            if(($value['excessqty'] < 0) || (!is_int($value['excessqty']) && ($value['excessqty']!='')) || ($value['excessqty'] < 0 && $value['excessqty'] ==''))
           {
            Log::info("i m in error_____".$value['excessqty']);
                $errorArray['atperrors'][] = 'Line #' . ($i+2)." Invalid data in Excess <br>";
                $i++;
                continue;
           }
           if((!is_int($value['damagedqty']) && ($value['damagedqty']!='')) || ($value['damagedqty'] < 0 && ($value['damagedqty']!='') ))
           {
                $errorArray['diterrors'][] = 'Line #' . ($i+2)." Invalid data in Damaged QTY <br>";
                $i++;
                continue;
           }

           if((!is_int($value['missingqty']) && ($value['missingqty']!='')) || ($value['missingqty'] < 0) && ($value['missingqty']!='') )
           {
                $errorArray['dnderrors'][] = 'Line #' . ($i+2)." Invalid data in Missing <br>";
                $i++;
                continue;
           }*/
//echo $warewhouseid['wh_id'];exit;
           $getcurrent_soh = $inventory->getSOH($value['product_id'], $warewhouseid['wh_id']);

 
           $total_orderd_qty = $inventory->getOrderdQty($value['product_id'], $warewhouseid['wh_id']); // getting here total orderd qty against product and warehouseId

           $checkcount_tracking_table =$inventory->getOpenProductsInTracking_WorkFlow($value['product_id'], $warewhouseid['wh_id']);
           $getOldValues = $inventory->getOldSOHAndATPValues($value['product_id'], $warewhouseid['wh_id']);
           //echo "<pre/>";print_r($getOldValues);exit;
           $curr_dit = $value['damagedqty'];
           $curr_dnd = $value['missingqty'];

           if(strlen($value['damagedqty']) == 0)
                $curr_dit = $getOldValues['dit_qty'];

            if(strlen($value['missingqty']) == 0)
                    $curr_dnd = $getOldValues['dnd_qty'];
           

           if($curr_dit < 0 || $curr_dnd < 0)
           {
                $errorArray['dnderrors'][] = " Negative(-ve) values are not allowed for DIT or Missing !! <br>";
                $i++;
                continue;
           }

           /*$resulted_soh =0;
           if(!empty($curr_dit) && !empty($curr_dnd))
            $resulted_soh = ($getcurrent_soh[0]['soh'] - $total_orderd_qty) - ($curr_dit + $curr_dnd);

           if($resulted_soh < 0)
           {
                $errorArray['dnderrors'][] = 'Line #' . ($i+2)." sum of dit qty and missing qty should be less than soh!! <br>";
                $i++;
                continue;
           }*/

           if($checkcount_tracking_table != 0)
           {
                $errorArray['dnderrors'][] = " Error !! Approval request for same product is pending. Please close pending requests first to continue. <br>";
                $i++;
                continue;
           }


           $updatevals = $this->_inventoryAudit->updateProductsinventory($value['excessqty'], $value['prodsoh'], $warewhouseid['wh_id'], $value['product_id'], "", "", $value['damagedqty'], $value['missingqty'], 117001, $bulk_upload_id,$warewhouseid['user_id']);
            if($updatevals == "failed")
            {
                $errorArray['dnderrors'][] = " Quarantine Quantity always be less than or equals to soh!! <br>"; //server side validations
                $i++;
                continue;
            }           
           
           if($updatevals == 1)
           {
            //update flag in inventory audit table
            $updateflag = $this->_inventoryAudit->updateflag($value['product_id'], $warewhouseid['wh_id']);

                $updateCounter++;
           }

           if($updatevals == 0)
           {
            $errorArray['samerecords'][] = " Duplicate data <br>";
           }

            $i++;
       }

       if($updateCounter > 0)
      //  echo "i ma going";
       $approval_flow_func->storeWorkFlowHistory('Inventory Bulk Upload', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user from mobile', $warewhouseid['user_id']); //creating tickets
       $mainArr['success']                  = $errorArray;
       $mainArr['dpulicate_count']          = (isset($errorArray['samerecords'])?count($errorArray['samerecords']):0);
       $mainArr['updated_count']            = $updateCounter;
       $wrong_combination                   = (isset($errorArray['wrongCombination'])?count($errorArray['wrongCombination']):0);
       $product_errors                      = (isset($errorArray['productIderrors'])?count($errorArray['productIderrors']):0);
       $soh_error_count                     = (isset($errorArray['soherrors'])?count($errorArray['soherrors']):0);
       $atp_error_count                     = (isset($errorArray['atperrors'])?count($errorArray['atperrors']):0);
       $commetErrorsCount                   = (isset($errorArray['commenterrors'])?count($errorArray['commenterrors']):0);
       $reasonErrorsCount                   = (isset($errorArray['reasonerrors'])?count($errorArray['reasonerrors']):0);
       $reason_mismatch_Count               = (isset($errorArray['reason_mismatch_errors'])?count($errorArray['reason_mismatch_errors']):0);
       $ditErrorsCount                      = (isset($errorArray['diterrors'])?count($errorArray['diterrors']):0);
       $dndErrorsCount                      = (isset($errorArray['dnderrors'])?count($errorArray['dnderrors']):0);
       $mainArr['error_count']              = $wrong_combination + $product_errors + $soh_error_count + $atp_error_count + $commetErrorsCount + $ditErrorsCount + $dndErrorsCount+$reasonErrorsCount + $reason_mismatch_Count ;

       $log_array                           = $mainArr;
       $mainArr['reference'] = $current_timeStamp;
       UserActivity::excelUploadFileLogs("INVENTORY", $current_timeStamp, $URL, $log_array);
       
       return $mainArr;
    }

        public function getProductListForCycle(Request $request) {
        try {
            $prodlist_params = json_decode($request->input("prodlist_params"), true);
            if($prodlist_params["lp_token"] == ""){
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($prodlist_params["wh_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            if($prodlist_params["type"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Type is missing", "data" => array()));
            }
            if($prodlist_params["limit"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Limit is missing", "data" => array()));
            }
            
            $token_check = $this->_inventoryAudit->authenticatToken($prodlist_params["lp_token"]);
            if(!empty($token_check)){
                $res_prodlist = $this->_inventoryAudit->getProductListCyclecount($prodlist_params["wh_id"], $prodlist_params["type"], $prodlist_params["limit"]);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_prodlist) ? "Products list" : "No data", "data" => !empty($res_prodlist) ? $res_prodlist : array()));
            } else {
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function getCategoryListForCycle(Request $request) {
        try {
            $res_prodlist = json_decode($request->input("res_prodlist"), true);
            $token_check = $this->_inventoryAudit->authenticatToken($res_prodlist["lp_token"]);

             if($res_prodlist["le_wh_id"] == ""){
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
                $res_prodlist = $this->_inventoryAudit->getCategoryListCycle($res_prodlist["le_wh_id"]);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_prodlist) ? "Categories list" : "No data", "data" => !empty($res_prodlist) ? $res_prodlist : array()));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


}
