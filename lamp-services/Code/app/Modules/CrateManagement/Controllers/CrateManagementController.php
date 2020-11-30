<?php

namespace App\Modules\CrateManagement\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
use Redirect;
use UserActivity;
use Illuminate\Support\Facades\Input;
use App\Modules\CrateManagement\Models\CrateManagement;

class CrateManagementController extends BaseController {

    public function __construct() {
        try {
            parent::Title('Crate Management');
            $this->_crateManagement = new CrateManagement();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getHubsList(Request $request) {
        try {
            $hublist_params = json_decode($request->input("hubslist_params"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "gethubslist", $request->input("hubslist_params"), "gethubslist api was requested", "");
            if($hublist_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($hublist_params["lp_token"]);
            if(!empty($token_check)){
                $res_hublist = $this->_crateManagement->hubsList();
                UserActivity::apiUpdateActivityLog($last_id, $res_hublist);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_hublist) ? "Hubs list" : "No data", "data" => !empty($res_hublist) ? $res_hublist : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getOrderCratesByHub(Request $request) {
        try {
            $crateslist_params = json_decode($request->input("crateslist_params"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "getordercratesbyhub", $request->input("crateslist_params"), "getordercratesbyhub api was requested", "");
            if($crateslist_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($crateslist_params["hub_id"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Hub id is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Hub id is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($crateslist_params["lp_token"]);
            if(!empty($token_check)){
                $res_crateslist = $this->_crateManagement->cratesByHub($crateslist_params["hub_id"]);
                UserActivity::apiUpdateActivityLog($last_id, $res_crateslist);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_crateslist) ? "Crates list" : "No data", "data" => !empty($res_crateslist) ? $res_crateslist : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function setCrateStatus(Request $request) {
        try {
            $crates_status_list_params = json_decode($request->input("cratestatuslist_params"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "setcratestatus", $request->input("cratestatuslist_params"), "setcratestatus api was requested", "");
            if($crates_status_list_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($crates_status_list_params["crate_info"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Crates information are missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Crates information are missing", "data" => array()));
            }
            if($crates_status_list_params["le_wh_id"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Warehouse id is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($crates_status_list_params["lp_token"]);
            if(!empty($token_check)){
                $res_crates_update = $this->_crateManagement->changeCrateStatus($crates_status_list_params["crate_info"], $crates_status_list_params["le_wh_id"]);
                UserActivity::apiUpdateActivityLog($last_id, $res_crates_update);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_crates_update) ? "Crates status were updated successfully!" : "Something went wrong while updating the status of crates", "data" => !empty($res_crates_update) ? $res_crates_update : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateCrateLoadingStatus(Request $request) {
        try {
            $crates_status_list_params = json_decode($request->input("cratelist"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "updatecrateloadingstatus", $request->input("cratelist"), "updatecrateloadingstatus api was requested", "");
            if($crates_status_list_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($crates_status_list_params["crate_info"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Crates information are missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Crates information are missing", "data" => array()));
            }
            if($crates_status_list_params["le_wh_id"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Warehouse id is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            if($crates_status_list_params["loading_status"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Status is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Status is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($crates_status_list_params["lp_token"]);
            if(!empty($token_check)){
                $res_crates_update = $this->_crateManagement->changeCrateLoadingStatus($crates_status_list_params["crate_info"], $crates_status_list_params["le_wh_id"], $crates_status_list_params["loading_status"]);
                if(!empty($res_crates_update)){
                    UserActivity::apiUpdateActivityLog($last_id, $res_crates_update);
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_crates_update) ? "Crates status were updated successfully!" : "Something went wrong while updating the status of crates", "data" => !empty($res_crates_update) ? $res_crates_update : array()));
                } else{
                    UserActivity::apiUpdateActivityLog($last_id, "Missing values");
                    return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Missing values", "data" => array()));
                }
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function getTripSheetDetails(Request $request) {
        try {
            $crates_status_list_params = json_decode($request->input("cratelist"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "gettripsheetdetails", $request->input("cratelist"), "gettripsheetdetails api was requested", "");
            if($crates_status_list_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($crates_status_list_params["docket_no"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Docket number missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Docket number missing", "data" => array()));
            }
            if($crates_status_list_params["le_wh_id"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Warehouse id is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($crates_status_list_params["lp_token"]);
            if(!empty($token_check)){
                $res_get_details = $this->_crateManagement->getDocumentDetails($crates_status_list_params["docket_no"], $crates_status_list_params["le_wh_id"]);

                // print_r($res_get_details);
                $formatResult = array(); $cfc_count = $bags_count = $crates_count = 0;
                if(!empty($res_get_details)){
                    foreach($res_get_details as $docData){
                        $formatResult['hub_id'] = $docData['hub_id'];
                        $formatResult['vehicle_no'] = $docData['st_vehicle_no'];
                        $cfc_count += $docData['cfc_cnt'];
                        $bags_count += $docData['bags_cnt'];
                        $crates_count += $docData['crates_cnt'];
                        $formatResult['crates'] = array();
                        if(isset($docData['crates_id']) && !empty($docData['crates_id'])){
                            $crates = explode(",", $docData['crates_id']);
                            foreach($crates as $crate){
                                $temp = array();
                                $temp['crate_no'] = $crate;
                                $temp['order_code'] = $docData['order_code'];
                                $temp['order_id'] = $docData['order_id'];
                                $formatResult['crates'][] = $temp;
                                unset($temp);
                            }
                        }
                    }
                    $formatResult['cfc_count'] = $cfc_count;
                    $formatResult['bags_count'] = $bags_count;
                    $formatResult['crates_count'] = $crates_count;
                }
                if(!empty($formatResult)){
                    UserActivity::apiUpdateActivityLog($last_id, $formatResult);
                    return json_encode(
                        array(
                            "status_code" => 200, 
                            "status" => "Success", 
                            "message" => "Success", 
                            "data" => $formatResult)
                        );
                } else{
                    UserActivity::apiUpdateActivityLog($last_id, "No Match Found");
                    return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "No Match Found", "data" => array()));
                }

            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    
    public function exchangeCrate(Request $request) {
        try {
            $crate_exchange_list_params = json_decode($request->input("crate_exchange_list"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "exchangecrate", $request->input("crate_exchange_list"), "exchangecrate api was requested", "");
            if($crate_exchange_list_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($crate_exchange_list_params["le_wh_id"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Warehouse id is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse id is missing", "data" => array()));
            }
            if($crate_exchange_list_params["old_crate_code"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Old crate code is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Old crate code is missing", "data" => array()));
            } else {
                $status_check_old = $this->_crateManagement->checkCrateStatus($crate_exchange_list_params["old_crate_code"]);
                $check_array = array(136002, 136003); // Filled, Allocated
                if(!in_array((int)$status_check_old["status"], $check_array)){
                    UserActivity::apiUpdateActivityLog($last_id, "Old crate is not in filled or allocated status, please select filled or allocated crate");
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => "Old crate is not in filled or allocated status, please select filled or allocated crate", "data" => array()));
                }
            }
            if($crate_exchange_list_params["new_crate_code"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "New crate code is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "New crate code is missing", "data" => array()));
            } else {
                $status_check_new = $this->_crateManagement->checkCrateStatus($crate_exchange_list_params["new_crate_code"]);
                if((int)$status_check_new["status"] !== 136001 && (int)$status_check_new["transaction_status"] !== 137001 ){ // Empty & Available
                    UserActivity::apiUpdateActivityLog($last_id, "New crate is not empty, please select empty crate");
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => "New crate is not empty, please select empty crate", "data" => array()));
                }
            }
            if($crate_exchange_list_params["order_code"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Order code is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Order code is missing", "data" => array()));
            } else {
                $order_crate_check = $this->_crateManagement->orderCrateMapCheck($crate_exchange_list_params["order_code"], $crate_exchange_list_params["old_crate_code"]);
                if($order_crate_check != ""){
                    UserActivity::apiUpdateActivityLog($last_id, $order_crate_check);
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => $order_crate_check, "data" => array()));
                }
            }
            if($crate_exchange_list_params["exchange_type"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Exchange Type is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Exchange Type is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($crate_exchange_list_params["lp_token"]);
            if(!empty($token_check)){
                $res_crates_exchange = $this->_crateManagement->exchangeCrateDetails($crate_exchange_list_params["le_wh_id"], $crate_exchange_list_params["old_crate_code"], $crate_exchange_list_params["new_crate_code"], $crate_exchange_list_params["order_code"], $crate_exchange_list_params["exchange_type"]);
                UserActivity::apiUpdateActivityLog($last_id, $res_crates_exchange);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_crates_exchange) ? "Crates details were exchanged successfully!" : "Something went wrong while exchanging the crate details", "data" => !empty($res_crates_exchange) ? $res_crates_exchange : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    
    public function missingCratesList(Request $request) {
        try {
            $missing_crate_list_params = json_decode($request->input("missing_crate_list"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "missingcrateslist", $request->input("missing_crate_list"), "missingcrateslist api was requested", "");
            if($missing_crate_list_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($missing_crate_list_params["lp_token"]);
            if(!empty($token_check)){
                $res_missing_crates = $this->_crateManagement->missingCrateDetails();
                UserActivity::apiUpdateActivityLog($last_id, $res_missing_crates);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_missing_crates) ? "Missing crate details fetched successfully!" : "There are no missing crates to fetch!", "data" => !empty($res_missing_crates) ? $res_missing_crates : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    
    public function rahDeliveryExcessList(Request $request) {
        try {
            $rah_delivery_excess_params = json_decode($request->input("rah_delivery_excess_params"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "rahdeliveryexcesslist", $request->input("rah_delivery_excess_params"), "rahdeliveryexcesslist api was requested", "");
            if($rah_delivery_excess_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($rah_delivery_excess_params["lp_token"]);
            if(!empty($token_check)){
                $res_return_crates = $this->_crateManagement->rheCrateDetails($token_check[0]);
                UserActivity::apiUpdateActivityLog($last_id, $res_return_crates);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_return_crates) ? "Crate information by order details fetched successfully!" : "There are no details to fetch!", "data" => !empty($res_return_crates) ? $res_return_crates : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    
    public function setCrateExcess(Request $request) {
        try {
            $set_crate_excess_params = json_decode($request->input("set_crate_excess"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "setcrateexcess", $request->input("set_crate_excess"), "setcrateexcess api was requested", "");
            if($set_crate_excess_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($set_crate_excess_params["crate_code"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Crate code is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Crate code is missing", "data" => array()));
            } else {
                $status_check_old = $this->_crateManagement->checkCrateStatus($set_crate_excess_params["crate_code"]);
                $check_array = array(136002); // Filled
                if(!in_array((int)$status_check_old["status"], $check_array)){
                    UserActivity::apiUpdateActivityLog($last_id, "Crate is not in filled , please select filled crate to claim as excess");
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => "Crate is not in filled , please select filled crate to claim as excess", "data" => array()));
                }
            }
            if($set_crate_excess_params["order_id"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Order id is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Order id is missing", "data" => array()));
            }
            if($set_crate_excess_params["claim_hub_id"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Hub id is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Hub id is missing", "data" => array()));
            }
            if($set_crate_excess_params["le_wh_name"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Warehouse name is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Warehouse name is missing", "data" => array()));
            }
            if($set_crate_excess_params["hub_name"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Hub name is missing");
                return json_encode(array("status_code" => 400, "status" => "Failed", "message" => "Hub name is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($set_crate_excess_params["lp_token"]);
            if(!empty($token_check)){
                $res_excess_crates = $this->_crateManagement->updateCrateExcess($set_crate_excess_params["crate_code"], $set_crate_excess_params["order_id"], $set_crate_excess_params["le_wh_name"], $set_crate_excess_params["hub_name"], $set_crate_excess_params["claim_hub_id"]);
                UserActivity::apiUpdateActivityLog($last_id, $res_excess_crates);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => $res_excess_crates, "data" => array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    
    public function hubToDcCrateList(Request $request) {
        try {
            $hub_to_dc_cratelist_params = json_decode($request->input("hub_to_dc_cratelist_params"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "hubtodccratelist", $request->input("hub_to_dc_cratelist_params"), "hubtodccratelist api was requested", "");
            if($hub_to_dc_cratelist_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if($hub_to_dc_cratelist_params["docket_no"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Docket No is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Docket No is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($hub_to_dc_cratelist_params["lp_token"]);
            if(!empty($token_check)){
                $hub_to_dc_crates = $this->_crateManagement->hubToDcCrateDetails($hub_to_dc_cratelist_params["docket_no"]);
                UserActivity::apiUpdateActivityLog($last_id, $hub_to_dc_crates);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($hub_to_dc_crates) ? "Crate information by order details fetched successfully!" : "There are no details to fetch!", "data" => !empty($hub_to_dc_crates) ? $hub_to_dc_crates : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    
    public function hubToDcDocketList(Request $request) {
        try {
            $hub_to_dc_docketlist_params = json_decode($request->input("hub_to_dc_docketlist_params"), true);
            $last_id = UserActivity::apiActivityLog("Crate Management", "hubtodcdocketlist", $request->input("hub_to_dc_docketlist_params"), "hubtodcdocketlist api was requested", "");
            if($hub_to_dc_docketlist_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_crateManagement->authenticatToken($hub_to_dc_docketlist_params["lp_token"]);
            if(!empty($token_check)){
                $hub_to_dc_docktes = $this->_crateManagement->hubToDcDocketDetails($token_check[0]);
                UserActivity::apiUpdateActivityLog($last_id, $hub_to_dc_docktes);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($hub_to_dc_docktes) ? "Crate information by order details fetched successfully!" : "There are no details to fetch!", "data" => !empty($hub_to_dc_docktes) ? $hub_to_dc_docktes : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    
}
