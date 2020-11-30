<?php
namespace App\Modules\CrateManagement\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Notifications;
use UserActivity;
use Utility;
use Log;
use App\Modules\Roles\Models\Role;

class CrateManagement extends Model {

    public function hubsList() {
        try {
            $hubs_query = DB::table("legalentity_warehouses")
                          ->where("dc_type", 118002)->where("state", 4033)->where("status", 1)
                          ->pluck("lp_wh_name", "le_wh_id")->all();
            return $hubs_query;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function authenticatToken($lpToken) {
        try {
            return json_decode(json_encode(DB::table("users")->where("lp_token", $lpToken)->orWhere("password_token", $lpToken)->pluck("user_id")->all()), true);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function cratesByHub($hubId) {
        try {
            $order_ids_query = DB::table("gds_orders")
                               ->where("hub_id", $hubId)
                               ->pluck("gds_order_id")->all();
            $crates_query = DB::table("picker_container_mapping")
                            ->whereIn("order_id", $order_ids_query)->where("container_barcode", "!=", NULL)
                            ->distinct()
                            ->get(["order_id", "container_barcode"])->all();
            return $crates_query;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function changeCrateStatus($cratesInfo, $warehouseID) {
        try {
            $result_set = array();
            foreach($cratesInfo as $each_info){
                $crtString = substr($each_info["crate_code"], 0, 3);
                if ($crtString == "CRT") {
                    $update_crate = DB::table('container_master')
                            ->where("crate_code", "=", $each_info["crate_code"])
                            ->where("le_wh_id", "=", $warehouseID);
                    $cratResult = $update_crate->get()->all();
                    if(count($cratResult)){
                        $allValues = json_decode(json_encode($cratResult[0]), true);
                    }else{
                        return $result_set;
                    }
                    $oldValues = array("status" => $allValues["status"], "transaction_status" => $allValues["transaction_status"]);
                    $uniqueDetails = array("crate_code" => $each_info["crate_code"], "le_wh_id" => $warehouseID);
                    $update_array = array();
                    $update_check = "";

                    if (!empty($each_info["status"])) {
                        $update_array["status"] = $each_info["status"];
                    }
                    if (!empty($each_info["transaction_status"])) {
                        $update_array["transaction_status"] = $each_info["transaction_status"];
                    }
                    $result_set[] = $update_check = $update_crate->update($update_array);

                    if ($update_check) {
                        if ($each_info["status"]) {
                            $statusName = json_decode(json_encode(DB::select("select getMastLookupValue(" . $each_info["status"] . ") as status")[0]), true);
                        } else {
                            $statusName["status"] = "";
                        }
                        if ($each_info["transaction_status"]) {
                            $transactionStatusName = json_decode(json_encode(DB::select("select getMastLookupValue(" . $each_info["transaction_status"] . ") as transaction")[0]), true);
                        } else {
                            $transactionStatusName["transaction"] = "";
                        }
                        $insertArray = array("crate_id" => $allValues["crate_id"],
                            "crate_code" => $allValues["crate_code"],
                            "le_wh_id" => $warehouseID,
                            "status" => !empty($each_info["status"]) ? $each_info["status"] : NULL,
                            "transaction_status" => !empty($each_info["transaction_status"]) ? $each_info["transaction_status"] : NULL,
                            "container_type" => $allValues["container_type"],
                            "comment" => "status: " . $statusName["status"] . " and transaction status: " . $transactionStatusName["transaction"],
                            "created_by" => \Session::get('userId'),
                            "created_at" => date('Y-m-d H:i:s'));
                        $insert_result[] = DB::table("crate_history")->insertGetId($insertArray);
                    }

                    $new_values = array("status" => $each_info["status"], "transaction_status" => $each_info["transaction_status"]);
                    $mongodb = UserActivity::userActivityLog("Crate Management", $new_values, "Crate status or transaction_status was updated in container_master table", $oldValues, $uniqueDetails);
                }
            }
            return $result_set;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function changeCrateLoadingStatus($cratesInfo, $warehouseID, $status) {
        try {
            $result_set = array();
            foreach($cratesInfo as $each_info){
                if(!empty($each_info["crate_code"]) && !empty($each_info["order_id"]) && !empty($status)){
                    $qryUpdate = DB::table("picker_container_mapping")
                                 ->where("container_barcode", "=", $each_info["crate_code"])
                                 ->where("order_id", "=", $each_info["order_id"])
                                 ->where("le_wh_id", "=", $warehouseID);

                    $allValues = json_decode(json_encode($qryUpdate->get()->all()[0]), true);
                    $oldValues = array("loading_status" => $allValues["loading_status"]);
                    $uniqueDetails = array("container_barcode" => $each_info["crate_code"], "le_wh_id" => $warehouseID, "order_id" => $each_info["order_id"]);
                    $update_check = "";

                    $result_set[] = $update_check = $qryUpdate->update(array("loading_status" => $status));

                    if($update_check){
                        $loadingStatusName = json_decode(json_encode(DB::select("select getMastLookupValue(".$status.") as loading_status")[0]), true);
                        $insertArray = array("crate_code" => $allValues["container_barcode"],
                                             "le_wh_id" => $warehouseID,
                                             "order_id" => $allValues["order_id"],
                                             "picker_id" => $allValues["picked_by"],
                                             "container_type" => $allValues["container_type"],
                                             "comment" => "loading status: ".$loadingStatusName["loading_status"],
                                             "created_by" => \Session::get('userId'),
                                             "created_at" => date('Y-m-d H:i:s'));
                        $insert_result[] = DB::table("crate_history")->insertGetId($insertArray);
                    }

                    $new_values = array("loading_status" => $status);
                    $mongodb = UserActivity::userActivityLog("Crate Management", $new_values, "Loading status was updated in picker_container_mapping table", $oldValues, $uniqueDetails);
                } else{
                    return array();
                }
            }
            return $result_set;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getDocumentDetails($docNumber, $warehouseID) {
        try {
            if(!empty($docNumber) && !empty($warehouseID)){
                $sql = DB::table('vw_stock_transit_report')
                       ->where("st_docket_no", "=", $docNumber)
                       ->where("le_wh_id", "=", $warehouseID)
                       ->select('hub_id', 'st_vehicle_no', 'order_id', 'order_code', 'cfc_cnt', 'crates_cnt', 'bags_cnt', 'crates_id')
                       ->get()->all();

                $result = json_decode(json_encode($sql), true);

                if(!empty($result)) {
                    return $result;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getHostURL(){
        try {
            $query = DB::table("mp_configuration")
            ->select(DB::raw("key_value")) 
            ->where("key_name","=","URL")   
            ->get()->all();

            return $query[0]->key_value;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function getOrderCrate($orderList){
        try {
            $query = DB::table("picker_container_mapping")
            ->select(DB::raw("DISTINCT(container_barcode),le_wh_id"))
            ->whereIn("order_id",[$orderList])
            ->get()->all();

            return json_decode(json_encode($query), true);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getLpToken($userId) {
        try {
            $tokenQuery = json_decode(json_encode(DB::table("users")->where("user_id", $userId)->get(["lp_token", "password_token"])->all()), true)[0];
            if($tokenQuery["lp_token"] != ""){
                $token = $tokenQuery["lp_token"];
            } else if($tokenQuery["password_token"] != ""){
                $token = $tokenQuery["password_token"];
            }
            return $token;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function exchangeCrateDetails($leWhId, $oldCrateCode, $newCrateCode, $orderCode, $exchangeType) {
        try {
            $orderId = $this->getOrderId($orderCode);
            $pickerContainerQuery = DB::table("picker_container_mapping")->where("le_wh_id", $leWhId)
                                    ->where("container_barcode", $oldCrateCode)->where("order_id", $orderId)
                                    ->update(array("container_barcode" => $newCrateCode));
            if($pickerContainerQuery){
                $containreOldDetailsQuery = DB::table("container_master")->where("crate_code", $oldCrateCode)
                                            ->where("le_wh_id", $leWhId);

                $containreOldDetailsRes = json_decode(json_encode($containreOldDetailsQuery->get()->all()), true)[0];
                $old_status = $containreOldDetailsRes["status"];
                $old_transaction_status = $containreOldDetailsRes["transaction_status"];
                $old_values = array("status" => $old_status, "transaction_status" => $old_transaction_status);

                $containreNewQuery = DB::table("container_master")->where("crate_code", $newCrateCode)
                                     ->where("le_wh_id", $leWhId)->update(array("status" => 136002, "transaction_status" => $old_transaction_status));
                
                if($containreNewQuery){
                    $new_values_new_crate = array("status" => 136002, "transaction_status" => $old_transaction_status);
                    $old_values_new_crate = array("status" => 136001, "transaction_status" => 137001);
                    $mongodb = UserActivity::userActivityLog("Crate Management", $new_values_new_crate, "Crate status or transaction_status was updated in container_master table", $old_values_new_crate, $newCrateCode);
                }
                
                if($exchangeType == "wrong"){
                    $containreOldQuery = $containreOldDetailsQuery->update(array("status" => 136001, "transaction_status" => 137001));
                    
                    if($containreOldQuery){
                        $new_values = array("status" => 136001, "transaction_status" => 137001);
                        $mongodb = UserActivity::userActivityLog("Crate Management", $new_values, "Crate status or transaction_status was updated in container_master table", $old_values, $oldCrateCode);
                    }
                } elseif($exchangeType == "missing") {
                    $containreOldQuery = $containreOldDetailsQuery->update(array("status" => 136006, "transaction_status" => $old_transaction_status));
                    
                    if($containreOldQuery){
                        $new_values = array("status" => 136006, "transaction_status" => $old_transaction_status);
                        $mongodb = UserActivity::userActivityLog("Crate Management", $new_values, "Crate status or transaction_status was updated in container_master table", $old_values, $oldCrateCode);
                    }
                }
            }
            return $pickerContainerQuery;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function checkCrateStatus($crateCode) {
        try {
            $crateQuery = json_decode(json_encode(DB::table("container_master")->where("crate_code", $crateCode)->get(["status", "transaction_status"])->all()), true)[0];
            return $crateQuery;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function orderCrateMapCheck($orderCode, $crateCode) {
        try {
            $order_id = $this->getOrderId($orderCode);

            $order_status_id = json_decode(json_encode(DB::table("gds_orders")->where("gds_order_id", $order_id)->pluck("order_status_id")->all()), true)[0];
            $order_status_array = array(17005, 17021); // READY TO DISPATCH, INVOICED
            if(in_array($order_status_id, $order_status_array)){
                $check_query = DB::table("picker_container_mapping")->where("order_id", $order_id)->where("container_barcode", $crateCode)->pluck("p_id")->all();
                if($check_query){
                    $result = "";
                } else {
                    $result = "There is no mapping between the given order code and crate, please enter valid details";
                }
            } else {
                $result = "The entered order should be in READY TO DISPATCH or INVOICED status, please enter valid details";
            }

            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getOrderId($orderCode) {
        try {
            return json_decode(json_encode(DB::table("gds_orders")->where("order_code", $orderCode)->pluck("gds_order_id")->all()), true)[0];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function missingCrateDetails() {
        try {
            $missing_list = DB::select(DB::raw("SELECT crate_code, le_wh_id, getMastLookupValue(status) as status, getMastLookupValue(transaction_status) as transaction_status,"
                    . " (SELECT order_id FROM picker_container_mapping WHERE container_barcode = crate_code ORDER BY created_at DESC LIMIT 1) AS order_id,"
                    . " (SELECT order_code FROM gds_orders WHERE gds_order_id = order_id) as order_code,"
                    . " getMastLookupValue((SELECT order_status_id FROM gds_orders WHERE gds_order_id = order_id)) AS order_status"
                    . " FROM `container_master` WHERE `transaction_status` IN (137004, 137005)")); // Quarantine @DC, Quarantine @HUB
            $missing_list_encode = json_decode(json_encode($missing_list), true);
            return $missing_list_encode;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function rheCrateDetails($userId) {
        try {
            $roleModel = new Role();
            $filterData = json_decode($roleModel->getFilterData(6, $userId), true);
            $sbu = json_decode($filterData["sbu"], true);
            
            $rahQuery = DB::table("gds_orders as go")
                        ->join("gds_returns as gr", function($join){
                            $join->on("go.gds_order_id", "=", "gr.gds_order_id");
                        })
                        ->join("picker_container_mapping as pcm", function($join){
                            $join->on("go.gds_order_id", "=", "pcm.order_id");
                        })
                        ->join("container_master as cm", function($join){
                            $join->on("pcm.container_barcode", "=", "cm.crate_code");
                        })
                        ->whereIn("go.order_status_id", [17022, 17023])
                        ->where("gr.return_status_id", 57067)
                        ->whereNull("go.order_transit_status")
                        ->where("cm.status", 136002)
                        ->where("cm.transaction_status", 137006);
            if(isset($sbu["118001"])) {
                $rahQuery->whereIn("go.le_wh_id", [$sbu["118001"]]);
            }
            if(isset($sbu["118002"])) {
                $rahQuery->whereIn("go.hub_id", [$sbu["118002"]]);
            }
            $rahRes = $rahQuery->groupBy("pcm.container_barcode")->orderBy("go.order_date", "DESC")
                      ->get([DB::raw("go.gds_order_id as order_id"), "go.order_code", DB::raw("pcm.container_barcode as crate_code"), "cm.status", "cm.transaction_status"])->all();
            
            $deliveredQry = DB::table("picker_container_mapping as pcm")
                            ->join("gds_orders as go", function($join){
                                $join->on("go.gds_order_id", "=", "pcm.order_id");
                            })
                            ->join("container_master as cm", function($join){
                                $join->on("pcm.container_barcode", "=", "cm.crate_code");
                            })
                            ->join("gds_order_track as got", function($join){
                                $join->on("got.gds_order_id", "=", "pcm.order_id");
                            })
                            ->where("go.order_status_id", 17007)
                            ->where("cm.status", 136001)
                            ->where("cm.transaction_status", 137006)
                            ->whereNull("got.rt_docket_no");
            if(isset($sbu["118001"])) {
                $deliveredQry->whereIn("go.le_wh_id", [$sbu["118001"]]);
            }
            if(isset($sbu["118002"])) {
                $deliveredQry->whereIn("go.hub_id", [$sbu["118002"]]);
            }
            $deliveredRes = $deliveredQry->groupBy("pcm.container_barcode")
                            ->get([DB::raw("go.gds_order_id as order_id"), "go.order_code", DB::raw("pcm.container_barcode as crate_code"), "cm.status", "cm.transaction_status"])->all();
            
            $excessQry = DB::table("picker_container_mapping as pcm")
                         ->join("container_master as cm", function($join){
                             $join->on("pcm.container_barcode", "=", "cm.crate_code");
                         })
                         ->join("gds_orders as go", function($join){
                             $join->on("go.gds_order_id", "=", "pcm.order_id");
                         })
                         ->where("cm.transaction_status", 137008);
            if(isset($sbu["118002"])) {
                $excessQry->whereIn("pcm.claimed_at", [$sbu["118002"]]);
            }         
            $excessRes = $excessQry->get(["pcm.order_id", "go.order_code", DB::raw("pcm.container_barcode as crate_code"), "cm.status", "cm.transaction_status", DB::raw("pcm.claimed_at as claimed_hub_id")])->all();
            
            return array("rah" => $rahRes, "delivered" => $deliveredRes, "excess" => $excessRes);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getCratesList($type, $orderby = "") {
        try {
            $sql = DB::table("container_master");
            
            if($type !== "all"){
                $sql = $sql->where("transaction_status", $type);
            }

            if (!empty($orderby)) {
                $orderClause = explode(" ", $orderby);
                $sql = $sql->orderby($orderClause[0], $orderClause[1]);  //order by query
            }
            $sql_res["result"] = $sql->select(DB::raw("crate_id,le_wh_id,crate_code, getMastLookupValue(status) as status, getMastLookupValue(transaction_status) as transaction_status, "
                                    . "(select order_id from picker_container_mapping where container_barcode = crate_code order by created_at desc limit 1) as last_order_id,"
                                    . "getOrderCode((select order_id from picker_container_mapping where container_barcode = crate_code order by created_at desc limit 1)) as last_order_code, "
                                    . "getMastLookupValue((select order_status_id from gds_orders where gds_order_id = last_order_id)) as last_order_status, "
                                    . "(select GetUserName(picker_id, 2) from gds_order_track where gds_order_id = last_order_id) as picker_name, "
                                    . "(select GetUserName(delivered_by, 2) from gds_order_track where gds_order_id = last_order_id) as de_name, "
                                    . "getLeWhName (le_wh_id) AS warehouse_name,"
                                    . " (SELECT getLeWhName(hub_id) FROM gds_orders WHERE gds_order_id = last_order_id) AS hub_name"
                    ))
                    ->get()->all();
            return json_decode(json_encode($sql_res), true);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getCurlResponse($crate_code) {
        try {
            $crate_params = json_encode(array("data" => array("type" => "c", "code" => $crate_code)));
            
            $curlCall = curl_init();
            curl_setopt($curlCall, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlCall, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($curlCall, CURLOPT_URL, env("EBUTOR_NODE_URL")."/crate/getCrateDetails");
            curl_setopt($curlCall, CURLOPT_POST, strlen($crate_params));
            curl_setopt($curlCall, CURLOPT_POSTFIELDS, $crate_params);
            $output = curl_exec($curlCall);
            $info = curl_getinfo($curlCall);
            $error = curl_error($curlCall);
//            echo "<br /><br />Output: <pre>"; print_r(json_decode($output)); echo "<br />----<br />Info: "; var_dump($info); echo "<br />----<br />Error: "; var_dump($error);
            curl_close($curlCall);

            return $output;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function updateCrateExcess($crateCode, $orderId, $leWhName, $hubName, $claimHubId) {
        $warehouseID = json_decode(json_encode(DB::table("legalentity_warehouses")->where("lp_wh_name", $leWhName)->where("dc_type", 118001)->pluck("le_wh_id")->all()), true)[0];
        $hubID = json_decode(json_encode(DB::table("legalentity_warehouses")->where("lp_wh_name", $hubName)->where("dc_type", 118002)->pluck("le_wh_id")->all()), true)[0];
        
        if($hubID != $claimHubId){
            $pcmQryUpdate = DB::table("picker_container_mapping")
                            ->where("container_barcode", "=", $crateCode)
                            ->where("order_id", "=", $orderId)
                            ->where("le_wh_id", "=", $warehouseID)
                            ->update(array("claimed_at" => $claimHubId));
            if($pcmQryUpdate){
                $cmQryDetails = DB::table("container_master")
                                ->where("crate_code", "=", $crateCode)
                                ->where("le_wh_id", "=", $warehouseID);
                $allValues = json_decode(json_encode($cmQryDetails->get()->all()), true)[0];
                
                $cmQryRes = $cmQryDetails->update(array("transaction_status" => 137008));
                if($cmQryRes){
                    $new_values = array("status" => $allValues["status"], "transaction_status" => 137008);
                    $old_values = array("status" => $allValues["status"], "transaction_status" => $allValues["transaction_status"]);
                    $mongodb = UserActivity::userActivityLog("Crate Management", $new_values, "Crate status or transaction_status was updated in container_master table", $old_values, $crateCode);
                    
                    return "Crate was claimed successfully!";
                } else {
                    return "Something went wrong while claiming the crate!";
                }
            } else {
                return "Something went wrong while claiming the crate!";
            }
        } else {
            return "The crate belong to the same Hub cannot be claimed as excess crate.";
        }
    }
    
    public function hubToDcCrateDetails($docketNo) {
        try {
            $fields = array(DB::raw("got.rt_docket_no as docket_no"),
                            DB::raw("go.gds_order_id as order_id"),
                            DB::raw("go.order_code as order_code"),
                            "go.order_status_id",
                            "cm.crate_code",
                            "cm.status",
                            "cm.transaction_status");
            $docketQuery = DB::table("gds_orders as go")
                        ->join("gds_order_track as got", function($join){
                            $join->on("go.gds_order_id", "=", "got.gds_order_id");
                        })
                        ->join("picker_container_mapping as pcm", function($join){
                            $join->on("go.gds_order_id", "=", "pcm.order_id");
                        })
                        ->join("container_master as cm", function($join){
                            $join->on("pcm.container_barcode", "=", "cm.crate_code");
                        })
                        ->where("got.rt_docket_no", $docketNo)
                        ->where("cm.transaction_status", 137003)
                        ->groupBy("pcm.container_barcode")
                        ->get($fields)->all();
            return $docketQuery;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function hubToDcDocketDetails($userId) {
        try {
            $roleModel = new Role();
            $filterData = json_decode($roleModel->getFilterData(6, $userId), true);
            $sbu = json_decode($filterData["sbu"], true);
            
//            $docketQuery = DB::table("gds_orders as go")
//                        ->join("gds_order_track as got", function($join){
//                            $join->on("go.gds_order_id", "=", "got.gds_order_id");
//                        })
//                        ->join("picker_container_mapping as pcm", function($join){
//                            $join->on("go.gds_order_id", "=", "pcm.order_id");
//                        })
//                        ->join("container_master as cm", function($join){
//                            $join->on("pcm.container_barcode", "=", "cm.crate_code");
//                        })
//                        ->whereIn("go.order_status_id", [17007, 17022, 17023])
//                        ->where("cm.transaction_status", 137003)
//                        ->whereNotNull("got.rt_docket_no");
//            if(isset($sbu["118001"])) {
//                $docketQuery->whereIn("go.le_wh_id", [$sbu["118001"]]);
//            }
//            $fields = array("got.rt_docket_no",
//                            DB::raw("go.gds_order_id as order_id"),
//                            "go.order_code",
//                            "go.order_status_id",
//                            "cm.crate_code",
//                            "cm.status",
//                            "cm.transaction_status",
//                            "go.le_wh_id",
//                            "go.hub_id",
//                            DB::raw("getLeWhName(go.le_wh_id) as dc_name"),
//                            DB::raw("getLeWhName(go.hub_id) as hub_name"));
//            $rdocketRes = json_decode(json_encode($docketQuery->groupBy("cm.crate_code")->get($fields)->all()), true);
            
            $rawQuery = "SELECT got.rt_docket_no, got.order_track_id,go.gds_order_id AS order_id, go.order_code,go.order_status_id,cm.crate_code,
cm.status, cm.transaction_status,go.le_wh_id, go.hub_id, getLeWhName(go.le_wh_id) AS dc_name, getLeWhName(go.hub_id) AS hub_name FROM 
 (SELECT goo.gds_order_id AS gds_order_id, pcm.container_barcode
    FROM gds_orders goo, picker_container_mapping pcm,gds_order_track gtt
    WHERE pcm.order_id = goo.gds_order_id
    AND gtt.gds_order_id = goo.gds_order_id";
            if(isset($sbu["118001"])) {
                $rawQuery = $rawQuery." AND goo.le_wh_id IN (".$sbu['118001'].")";
            }
            $rawQuery = $rawQuery." AND goo.order_status_id IN (17007, 17022, 17023)
    AND gtt.rt_received_at IS NULL AND gtt.rt_docket_no IS NOT NULL
    GROUP BY pcm.container_barcode
   ORDER BY pcm.p_id DESC) sub_go JOIN gds_orders go ON go.gds_order_id = sub_go.gds_order_id
   INNER JOIN gds_order_track AS got ON go.gds_order_id = got.gds_order_id
   INNER JOIN container_master AS cm ON sub_go.container_barcode = cm.crate_code
   WHERE cm.transaction_status = 137003 AND got.rt_received_at IS NULL AND got.rt_docket_no IS NOT NULL
   GROUP BY cm.crate_code
   ORDER BY got.order_track_id DESC;";
            
            $docketQuery = DB::select(DB::raw($rawQuery));
            
            $rdocketRes = json_decode(json_encode($docketQuery), true);
            
            $result = $final_result = array();
            foreach($rdocketRes as $eachRes){
                $result[$eachRes["rt_docket_no"]]["docket_no"] = $eachRes["rt_docket_no"];
                $result[$eachRes["rt_docket_no"]]["order_id"][] = $eachRes["order_id"];
                $result[$eachRes["rt_docket_no"]]["crate_code"][] = $eachRes["crate_code"];
                $result[$eachRes["rt_docket_no"]]["dc_name"] = $eachRes["dc_name"];
                $result[$eachRes["rt_docket_no"]]["hub_name"] = $eachRes["hub_name"];
            }
            foreach($result as $each){
                $final_result[] = array("docket_no" => $each["docket_no"],
                                    "dc_name" => $each["dc_name"],
                                    "hub_name" => $each["hub_name"],
                                    "order_count" => count(array_unique($each["order_id"])),
                                    "crate_count" => count($each["crate_code"]));
            }
            
            return $final_result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getStatusCount() {
        $countQuery = DB::table("container_master as cm")
                      ->select(DB::raw("COUNT(*) AS all_crates, SUM(IF(cm.transaction_status = 137001, 1, 0)) AS available, SUM(IF(cm.transaction_status = 137009, 1, 0)) AS rtd,"
                        . " SUM(IF(cm.transaction_status = 137002, 1, 0)) AS sit_dc_hub, SUM(IF(cm.transaction_status = 137006, 1, 0)) AS crate_in_hub,"
                        . " SUM(IF(cm.transaction_status = 137003, 1, 0)) AS sit_hub_dc, SUM(IF(cm.transaction_status = 137007, 1, 0)) AS crate_in_dc"))
                      ->get()->all();
        return $countQuery;
    }
    public function wareHouseList(){
        $query = DB::table('legalentity_warehouses')->select(['display_name as name','le_wh_id'])
                     ->where('dc_type','=',118001)
                     ->where('legal_entity_id','!=',2)
                     ->get()->all();
        return $query;
    }
    public function containerMasterCodeGen($le_wh_id,$crate,$s_no){
        //$codeCheck = DB::table('container_master')->select('crate_code')->where('crate_code','=',"'".$crate."'")->get()->all();
        $codeCheck ="select `crate_code` from `container_master` where `crate_code` ='".$crate."' limit 1";
        $codeCheck =DB::selectFromWriteConnection(DB::raw($codeCheck));
        $codeCheck =isset($codeCheck[0])?$codeCheck[0]->crate_code:'';
        if($codeCheck!=''){
            return 0;
        }
        $query = DB::selectFromWriteConnection(DB::raw("CALL insertContainerMaster(".$le_wh_id.",'".$crate."',".$s_no.")"));
        return $query;
    }
    public function crateEditDetails($id){
        $query ='SELECT crate_id,le_wh_id,crate_code, getMastLookupValue(STATUS) AS STATUS, getMastLookupValue(transaction_status) AS transaction_status, (SELECT order_id FROM picker_container_mapping WHERE container_barcode = crate_code ORDER BY created_at DESC LIMIT 1) AS last_order_id,getOrderCode((SELECT order_id FROM picker_container_mapping WHERE container_barcode = crate_code ORDER BY created_at DESC LIMIT 1)) AS last_order_code, getMastLookupValue((SELECT order_status_id FROM gds_orders WHERE gds_order_id = last_order_id)) AS last_order_status, (SELECT GetUserName(picker_id, 2) FROM gds_order_track WHERE gds_order_id = last_order_id) AS picker_name, (SELECT GetUserName(delivered_by, 2) FROM gds_order_track WHERE gds_order_id = last_order_id) AS de_name, getLeWhName (le_wh_id) AS warehouse_name, (SELECT getLeWhName(hub_id) FROM gds_orders WHERE gds_order_id = last_order_id) AS hub_name FROM `container_master` WHERE `crate_id` = '.$id.'';
        $query = DB::select(DB::raw($query));
        return  $query;
    }
    public function updateCrateEditDetails($id,$le_wh_id){
        $query = DB::table('container_master')
                    ->where('crate_id','=',$id)
                    ->update(['le_wh_id'=>$le_wh_id]);
        return $query;
    }
    public function getCrateCodes($le_wh_id){
        $query = DB::table('container_master')
                    ->select('crate_id','crate_code','le_wh_id')
                    ->where(['le_wh_id'=>$le_wh_id])->get()->all();
        $query = json_decode(json_encode($query),1);
        if(!empty($query)){
            return $query;
        }else{
            return [];
        }
    }
}