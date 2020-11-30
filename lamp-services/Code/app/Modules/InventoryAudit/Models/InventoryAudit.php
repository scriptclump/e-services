<?php
namespace App\Modules\InventoryAudit\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Notifications;
use UserActivity;
use Utility;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;


class InventoryAudit extends Model {

    public function getProductList($whId, $auditType, $limit) {
        $field_list = "inv.le_wh_id, inv.product_id, inv.product_title, inv.sku, inv.mrp, PPC.pack_sku_code as ean";
        if($auditType == "Cycle Count"){
            $field_list .= ", conf.wh_loc_id as bin_id, conf.wh_location as bin_location";
        }
        $prod_query = DB::table("vw_inventory_report as inv")->join("product_pack_config as PPC", "PPC.product_id", "=", "inv.product_id")
                    ->select([DB::raw($field_list)]);
        if($auditType == "Cycle Count"){
            $prod_query = $prod_query->join("warehouse_config as conf", function($join){
                                $join->on("conf.le_wh_id", "=", "inv.le_wh_id")
                                     ->on("conf.pref_prod_id", "=", "inv.product_id");
                          });
        
            $binLocations = $this->assignedBinLocations();
            if(!empty($binLocations)){
                $prod_query = $prod_query->whereNotIn("conf.wh_loc_id", $binLocations);
            }
        }
        $prod_query = $prod_query->where("PPC.pack_code_type", "=", "79002")->where("PPC.level", "=", "16001");
        $prod_query = $prod_query->where("inv.le_wh_id", "=", $whId)
                    ->take($limit)->get()->all();
        return $prod_query;
    }
    
    public function insertPickerAssignment($inputArray) {
        if($inputArray["type"] == "Cycle Count"){
            foreach($inputArray["product_info"] as $eachInfo){
                $productTitle = json_decode(json_encode(DB::table("products")->where("product_id", $eachInfo["product_id"])
                                ->pluck("product_title")[0]), true);
                $sku = json_decode(json_encode(DB::table("products")->where("product_id", $eachInfo["product_id"])
                                ->pluck("sku")->all()[0]), true);
                $insertArray = array("wh_id" => $inputArray["wh_id"],
                                     "product_id" => $eachInfo["product_id"],
                                     "product_title" => $productTitle,
                                     "mrp" => $eachInfo["mrp"],
                                     "location_id" => $eachInfo["location_id"],
                                     "location_code" => $eachInfo["location_code"],
                                     "auditor" => $inputArray["picker_id"],
                                     "type" => $inputArray["type"],
                                     "assigned_by" => $inputArray["user_id"],
                                     "assigned_date" => date('Y-m-d H:i:s'),
                                     "status" => 0,
                                     "EAN" => $eachInfo['ean'],
                                     "sku" =>$sku,
                                    "is_flag" =>0);
                $result[] = DB::table("inventory_audit")->insertGetId($insertArray);
            }
            return $result;
        } elseif($inputArray["type"] =="Stock Take"){
            foreach($inputArray["product_info"] as $eachInfo){
                $assignedBinLocations = $this->assignedBinLocations();
                $bin_location_details = json_decode(json_encode(DB::table("warehouse_config")->where("le_wh_id", $inputArray["wh_id"])
                                        ->where("pref_prod_id", $eachInfo["product_id"])->whereNotIn("wh_loc_id", $assignedBinLocations)
                                        ->get(["wh_loc_id", "wh_location"])->all()), true);
                $productTitle = json_decode(json_encode(DB::table("products")->where("product_id", $eachInfo["product_id"])
                                ->pluck("product_title")->all()[0]), true);
                $sku = json_decode(json_encode(DB::table("products")->where("product_id", $eachInfo["product_id"])
                                ->pluck("sku")->all()[0]), true);
                if($bin_location_details){
                    foreach($bin_location_details as $each_detail){
                        $insertArray = array("wh_id" => $inputArray["wh_id"],
                                         "product_id" => $eachInfo["product_id"],
                                         "product_title" => $productTitle,
                                         "mrp" => $eachInfo["mrp"],
                                         "location_id" => $each_detail["wh_loc_id"],
                                         "location_code" => $each_detail["wh_location"],
                                         "auditor" => $inputArray["picker_id"],
                                         "type" => $inputArray["type"],
                                         "assigned_by" => $inputArray["user_id"],
                                         "assigned_date" => date('Y-m-d H:i:s'),
                                         "status" => 0,
                                          "EAN" => $eachInfo['ean'],
                                            "sku" =>$sku);
                        $result[] = DB::table("inventory_audit")->insertGetId($insertArray);
                    }
                } else {
                    return "bin";
                }
            }
            return $result;
        } elseif($inputArray["type"] =="category"){

          foreach($inputArray["product_info"] as $eachInfo){
                
                //print_r(count($inputArray["product_info"]));
               // $assignedBinLocations = $this->assignedBinLocations();
            $getcategories =  "SELECT prdts.`category_id`,prdts.`product_id`,prdts.`product_title`,prdts.`sku`,prdts.`mrp` 
                FROM products AS prdts JOIN categories AS cat ON cat.`category_id` = prdts.`category_id`  
                LEFT JOIN inventory i ON i.product_id = prdts.product_id WHERE cat.`category_id` IN (".$eachInfo['category_id'].")
                AND NOT EXISTS (SELECT * FROM inventory_audit ia WHERE ia.`product_id` = prdts.`product_id`
                AND ia.`created_at` BETWEEN CURDATE() AND NOW() AND ia.is_flag IN(0,1)) AND (i.soh > 0 OR dit_qty > 0 OR dnd_qty > 0) AND i.le_wh_id = ".$inputArray['wh_id']; 
                
             
            $prod_query = DB::select(DB::raw($getcategories));
           
            $cat = json_decode(json_encode($prod_query), True);

                if($cat){
                
                    foreach($cat as $each_detail){
                        
                        $insertArray = array("wh_id" => $inputArray["wh_id"],
                                        "product_id" => $each_detail["product_id"],
                                        "product_title" => $each_detail["product_title"],
                                        "mrp" => $each_detail["mrp"],
                                        // "category_id" => $eachInfo["category_id"],
                                        //"location_id" => $each_detail["wh_loc_id"],
                                        //"location_code" => $each_detail["wh_location"],
                                        "auditor" => $inputArray["picker_id"],
                                        "type" => "Cycle Count",
                                        "assigned_by" => $inputArray["user_id"],
                                        "assigned_date" => date('Y-m-d H:i:s'), 
                                        "status" => 0,
                                        //"EAN" => $each_detail['ean'],
                                       "sku" =>$each_detail['sku']);
                        $result[] = DB::table("inventory_audit")->insertGetId($insertArray);
                    }
                    
                } else {
                    return "bin";
                }

            }
            return $result;
        }
    }
    
    public function authenticatToken($lpToken) {
        return json_decode(json_encode(DB::table("users")->where("lp_token", $lpToken)->orWhere("password_token", $lpToken)->pluck("user_id")->all()), true);
    }
    
    public function getPickerAssignment($getDetails) {
        $query = json_decode(json_encode(DB::table("inventory_audit")->where("type", $getDetails["type"])
                ->where("auditor", $getDetails["picker_id"])->where("status", 0)
                ->get(["inv_audit_id", "product_id", "product_title", "mrp", "wh_id", "location_id", "location_code", "auditor", "type", "wh_id", "EAN"])->all()), true);
        $res = $semi_arr = array();
        if($getDetails["type"] == "Stock Take"){
            foreach($query as $each){
                $res[] = array("inv_audit_id" => $each["inv_audit_id"],
                                            "product_id" => $each["product_id"],
                                            "product_title" => $each["product_title"],
                                            "mrp" => $each["mrp"],
                                            "wh_id" => $each["wh_id"],
                                            "location_id" => $each["location_id"],
                                            "location_code" => $each["location_code"],
                                            "ean" => $each['EAN']);
            }
            return $res;
        } else if($getDetails["type"] == "Cycle Count"){
            foreach($query as $each){
                $semi_arr[$each["product_id"]]["product_info"] = array("product_id" => $each["product_id"],
                                            "product_title" => $each["product_title"],
                                            "mrp" => $each["mrp"],
                                            "wh_id" => $each["wh_id"],
                                            "ean" => $each['EAN']);
                $semi_arr[$each["product_id"]]["location_arr"]["location_info"][] = array(
                                                "inv_audit_id" => $each["inv_audit_id"],
                                                "location_id" => $each["location_id"],
                                                "location_code" => $each["location_code"]
                                            );
            }
            foreach($semi_arr as $each_semi){
                $res[] = array_merge($each_semi["product_info"], $each_semi["location_arr"]);
            }
            return $res;
        }
    }
    
    public function assignedBinLocations() {
        return json_decode(json_encode(DB::table("inventory_audit")->where("status", 0)->orWhere("status", 1)->pluck("location_id")->all()), true);
    }
    
    public function updateAuditDetails($updateParams) {

        // $apiData["data"] = json_encode(array("user_id" => $updateParams["auditor"],
        //                    "audit_id" => $updateParams["inv_audit_id"],
        //                    "product_id" => $updateParams["product_id"],
        //                    "warehouse_id" => $updateParams["wh_id"],
        //                    "request_source" => $updateParams["type"],
        //                    "dit_qty" => $dit_qty,
        //                    "missing_qty" => $missing_qty,
        //                    "lp_token" => $updateParams["lp_token"],
        //                    "comments" => ""));
        // $ticket_res = json_decode($this->curlCallFunction($apiData));
        
        // if($ticket_res){
        //     $update_array = array("good_qty" => $updateParams["good_qty"],
        //                     "damage_qty" => $updateParams["damage_qty"],
        //                     "expire_qty" => $updateParams["expire_qty"],
        //                     "status" => 1,
        //                     "audit_date" => date('Y-m-d H:i:s'),
        //                     "updated_at" => date('Y-m-d H:i:s'),
        //                     "updated_by" => $updateParams["auditor"]);        
        //     return DB::table("inventory_audit")->where("inv_audit_id", $updateParams["inv_audit_id"])->where("status", 0)
        //               ->where("auditor", $updateParams["auditor"])->update($update_array);
        // } else {
        //     return 0;
        // }
            $ret_val = 0;
            $curr_bin_qty = $this->getOldBinQty($updateParams["product_id"], $updateParams["wh_id"], $updateParams['bin_id']);
            $missing_qty = $curr_bin_qty - ($updateParams["good_qty"]+$updateParams["damage_qty"]+$updateParams["expire_qty"]);
            
            $dit_qty = $updateParams["damage_qty"] + $updateParams["expire_qty"];
            // $missing_qty = $curr_bin_qty - $dit_qty;
            if($missing_qty < 0)
            {
                $missing_qty = 0;
            }
            $quarantine_qty = $dit_qty + $missing_qty;
            $product_id = $updateParams['product_id'];
            $warehouseID = $updateParams['wh_id'];

            $update_quarantine = DB::table('inventory')
                                ->where("product_id", "=", $product_id)
                                ->where("le_wh_id", "=", $warehouseID);

            $quarantine_Data = $update_quarantine->get(array("quarantine_qty", "soh"))->all();
            $quarantine_Data = json_decode(json_encode($quarantine_Data), true);
            
            $old_quarantine_qty = $quarantine_Data[0]['quarantine_qty'];
            $resulted_quarantine_Qty = $old_quarantine_qty + $quarantine_qty;


            $oldvaluesArray = array("old_quarantine_qty" => $old_quarantine_qty);
            $newvalues = array("new_quarantine_qty" => $resulted_quarantine_Qty);
            $update_quarantine= $update_quarantine->increment('quarantine_qty',$quarantine_qty);
            $uniquevalues = array("product_id" => $product_id, "warehouse_id" => $warehouseID);

            UserActivity::userActivityLog("Inventory Audit", $newvalues, "Adding Quarantine Qty for inventory table for the product_id: ".$product_id." and warehouse_id : ".$warehouseID." ", $oldvaluesArray, $uniquevalues);  //logs
            if(strlen($updateParams["damage_qty"]) == 0)
            {
                $updateParams["damage_qty"] = 0;
            }

            if(strlen($updateParams["expire_qty"]) == 0)
            {
                $updateParams["expire_qty"] = 0;
            }

            $update_array = array("good_qty" => $updateParams["good_qty"],

                            "damage_qty" => $updateParams["damage_qty"],
                            "expire_qty" => $updateParams["expire_qty"],
                            "status" => 1,
                            "old_soh" => $quarantine_Data[0]['soh'],
                            "old_bin_qty" => $curr_bin_qty,
                            "audit_date" => date('Y-m-d H:i:s'),
                            "updated_at" => date('Y-m-d H:i:s'),
                            "updated_by" => $updateParams["auditor"]);   
            if(!empty($updateParams['new_location_code']))
            {
                $temparr = array("new_location_code" => $updateParams['new_location_code']);
                $update_array = array_merge($update_array, $temparr);
            }

            if(!empty($updateParams['mfg_date']))
            {
                $temparr1 = array("mfg_date" => $updateParams['mfg_date']);
                $update_array = array_merge($update_array, $temparr1);
            }
            $sql = DB::table("inventory_audit")->where("inv_audit_id", $updateParams["inv_audit_id"])->where("status", 0)
                      ->where("auditor", $updateParams["auditor"])->update($update_array);

            if($sql)
            {
              $ret_val = 1;
            }
            return $ret_val;
        
    
    }
    
    public function curlCallFunction($data) {
        $curlCall = curl_init();
        curl_setopt($curlCall, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlCall, CURLOPT_URL, $_SERVER['HTTP_HOST']."/inventory/approvalworkflowcreation");
        curl_setopt($curlCall, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curlCall, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($curlCall);
        $info = curl_getinfo($curlCall);
        $error = curl_error($curlCall);
//        echo "Output: <pre>"; print_r(json_decode($output)); echo "<br />----<br />Info: "; var_dump($info); echo "<br />----<br />Error: "; var_dump($error);
        curl_close($curlCall);
        return $output;
    }

    public function getOldBinQty($prod_id, $warehouseID, $bin_id)
    {
      $sql = DB::table("bin_inventory")
                  ->where("bin_id", $bin_id)
                  ->where("product_id", $prod_id)
                  ->where("wh_id", $warehouseID)
                  ->get(array("qty"))->all();

      $data  = json_decode(json_encode($sql), true);
      // echo "stopppp";print_r($data);die;
      return $data[0]['qty'];
    }

    public function getAvaliableLocations($params)
    {
        try {
                $product_id = $params['product_id'];
                $wh_id = $params['wh_id'];

                $sql = DB::table("warehouse_config")->where("le_wh_id", $wh_id)->whereIn("pref_prod_id", [$product_id,""])->get(array("wh_location"))->all();
                $data = json_decode(json_encode($sql), true);
                return $data;
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function getsohdetails($params){

        //echo "<pre/>";print_r($params);exit;
        $sql = DB::table("inventory")
        ->select("soh","dit_qty","dnd_qty")
        ->where("le_wh_id", $params['wh_id'])->where("product_id", $params['product_id'])->get()->all();
                $data = json_decode(json_encode($sql), true);
                return $data;
    }


        public function getPickerAssignmentWithSOHData($getDetails) {
            $statusArr = array("1", "57089", "57075");
        $query = json_decode(json_encode(DB::table("inventory_audit as ia")
            //->join("inventory_tracking","inventory_tracking.product_id","=","ia.product_id")
            ->where("ia.type", $getDetails["type"])
                ->where("ia.auditor", $getDetails["picker_id"])->where("status", 0)->where("is_flag",0)
                ->whereDate('created_at', '=', date('Y-m-d'))
                //->whereNotIn("inventory_tracking.approval_status",[1,57089,57075])
                ->get(["ia.inv_audit_id", "ia.product_id", "ia.product_title", "ia.mrp", "ia.wh_id", "ia.location_id", "ia.location_code", "ia.auditor", "ia.type", "ia.wh_id" ,DB::raw("IFNULL(ia.EAN,0) as EAN"),DB::raw("IFNULL(ia.sku,0) as sku"),DB::raw('getInventoryByProduct(ia.wh_id,ia.product_id) as soh')])->all()), true);

        $res = $semi_arr = array();
        if($getDetails["type"] == "Stock Take"){
            foreach($query as $each){
                $res[] = array("inv_audit_id" => $each["inv_audit_id"],
                                            "product_id" => $each["product_id"],
                                            "product_title" => $each["product_title"],
                                            "mrp" => $each["mrp"],
                                            "wh_id" => $each["wh_id"],
                                            "location_id" => $each["location_id"],
                                            "location_code" => $each["location_code"],
                                            "sku" => $each["sku"]);
            }
            return $res;
        } else if($getDetails["type"] == "Cycle Count"){
            foreach($query as $each){
                $semi_arr[$each["product_id"]]["product_info"] = array("product_id" => $each["product_id"],
                                            "product_title" => $each["product_title"],
                                            "mrp" => $each["mrp"],
                                            "wh_id" => $each["wh_id"],
                                            "ean" => $each['EAN'],
                                             "soh" => $each['soh'],
                                            "sku" => $each["sku"]
                                     );
            }
            foreach($semi_arr as $each_semi){
                $res[] = $each_semi["product_info"];
            }
            return $res;
        }
    }

    //save into inventory bulk table

    public function saveIntoinventorybulktable($details){
        $save=DB::table('inventory_bulk_upload')->insert($details);
            $lastid = DB::getPdo()->lastInsertId($save);
            return $lastid;
    }

    public function saveIntotrackingtable($uploadarray){
        //echo "<pre/>";print_r($uploadarray);
         $saveintodetails = DB::table('inventory_tracking')->insert($uploadarray);
        return $saveintodetails;
    }




        public function updateProductsinventory($excess_qty, $sohval, $wareId, $prodId, $sku, $comments, $dit_qty, $dnd_qty, $reason="", $bulk_upload_id="",$userid)
    {
        $inventory                          = new Inventory;
        $approval_flow_func                 = new CommonApprovalFlowFunctionModel();
        
        $returnval                          = 1;
        $timestamp                          = date('Y-m-d H:i:s');
        $oldvalues                          = $inventory->getOldSOHAndATPValues($prodId, $wareId);
        
        if($bulk_upload_id != "")
        {
            //when bulk upload came then only
            if(strlen($sohval) == 0)
                $sohval = $oldvalues['soh'];

            // if(strlen($atpval) == 0)
            //     $atpval = $oldvalues['atp'];

            if(strlen($dit_qty) == 0)
                // $dit_qty = $oldvalues['dit_qty'];
                $dit_qty = 0;

            if(strlen($dnd_qty) == 0){
                // $dnd_qty = $oldvalues['dnd_qty'];
                $dnd_qty = 0;
            }
        }

        $stock_difference                   = $sohval - $oldvalues['soh'];
        $dit_diff                           = $dit_qty - $oldvalues['dit_qty'];
        $dnd_diff                           = $dnd_qty - $oldvalues['dnd_qty'];
        $user_ID                            = $userid;
        if($bulk_upload_id == "" || $bulk_upload_id == 0 || $bulk_upload_id == NULL)
        {
            $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('Inventory', 'drafted', $userid);
        }
        else
        {
            $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('Inventory Bulk Upload', 'drafted', $userid);            
        }
        //echo "<pre/>";print_r($res_approval_flow_func);exit;
        
        if($res_approval_flow_func['status'] == 1)
        {
            $curr_status_ID = $res_approval_flow_func['currentStatusId'];
            $nextlevelStatusId = $res_approval_flow_func['data'][0]['nextStatusId'];
            $quarantine_QTY = ($dit_qty+$dnd_qty);
            $getcuurent_quarantine_qty  = $this->getInventoryDetailsBasedOnProductIdForSoh($prodId, $wareId);
            $curr_quarantine_qty = $getcuurent_quarantine_qty['quarantine_qty'] + $quarantine_QTY;
            $update_inventory = DB::table("inventory")
                ->where("product_id", "=", $prodId)
                ->where("le_wh_id", "=", $wareId)
                ->update(["quarantine_qty" => $curr_quarantine_qty]);

            $insert_array = array("product_id"      => $prodId,
                               "le_wh_id"           => $wareId,
                               "activity_type"      => $reason,
                               "approval_status"    => $nextlevelStatusId,
                               // "stock_diff"         => $stock_difference,
                               // "old_soh"            => $oldvalues['soh'],
                               // "new_soh"            => $sohval,
                               // "old_atp"            => $oldvalues['atp'],
                               // "new_atp"            => $atpval,
                                "old_dit_qty"        => $oldvalues['dit_qty'],
                               "new_dit_qty"        => $dit_qty,
                               "dit_diff"           => $dit_qty,

                               "dnd_diff"           => $dnd_qty,
                               "excess"             => $excess_qty,
                                "old_dnd_qty"        => $oldvalues['dnd_qty'],
                                "new_dnd_qty"        => $dnd_qty,

                               "created_by"         => $user_ID,
                               "approved_by"        => $user_ID,
                               "remarks"            => $comments
                               ,"bulk_upload_id"    => $bulk_upload_id,
                               "quarantine_qty"     => $quarantine_QTY
                               );
            $inv_track_id = DB::table("inventory_tracking")->insertGetId($insert_array);
            if(!$inv_track_id)
            {
                return "failed"; // here dit_diff and dnd_diff data type is un-signed if negitive value came query won't execute then we are returning failed
            }
            if($bulk_upload_id != "")
            {
                // $approval_flow_func->storeWorkFlowHistory('Inventory Bulk Upload', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId'));
            }
            else
            {
                $approval_flow_func->storeWorkFlowHistory('Inventory', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user from  mobile', $userid);                
            }
            

            //Check if current step is final..
            if($res_approval_flow_func['data'][0]['isFinalStep'] == 1)
            {
                $tableUpdateID = 1;
                // update the inventory table
                $updateInventory = $inventory->updateInventoryTable($inv_track_id);
                $update_tracking_table = $this->updateTrackingTableWithStatusinventory($tableUpdateID, $inv_track_id,$warewhouseid['user_id']);
            }
        }
        elseif($res_approval_flow_func['status'] == 0)
        {
            $returnval = 0;
        }
        
        return $returnval;
    }

      public function updateTrackingTableWithStatusinventory($tableUpdateID, $tracingID,$userid)
    {
        $getoldStatus = $this->getOldStatusFromTracking($tracingID);
        $uniquevalues = array("product_id" => $getoldStatus['product_id']);
        $oldvaluesArray = array("old_status"=> $getoldStatus['approval_status']);

        $sql = DB::table("inventory_tracking")
                ->where("inv_track_id", "=", $tracingID)
                ->update(['approved_by'=> $userid, "approval_status" => $tableUpdateID]);


        $DBentries = array("new_status"=>$tableUpdateID);    
            
        UserActivity::userActivityLog("Inventory", $DBentries, "Changing the workflow status from".$getoldStatus['approval_status']." to".$tableUpdateID , $oldvaluesArray, $uniquevalues);

                

    }

     public function getInventoryDetailsBasedOnProductIdForSoh($productId, $leWhId)
    {
        $sql            = DB::table("inventory")
                        ->select("soh", "dit_qty", "dnd_qty", "quarantine_qty", "order_qty")
                        ->where("product_id", "=", $productId)
                        ->where("le_wh_id", "=", $leWhId)
                        ->first();
        //$query ="select soh,dit_qty,dnd_qty,quarantine_qty, order_qty from inventory where product_id =".$productId." and le_wh_id=".$leWhId;
        //$sql =  DB::selectFromWriteConnection($query);
        $data =json_decode(json_encode($sql), true);
        return $data;
    }

    public function getProductListCyclecount($whId, $auditType, $limit) {

        /*$field_list = "inv.le_wh_id1, inv.product_id, inv.product_title, inv.sku, inv.mrp, PPC.pack_sku_code as ean";
        if($auditType == "Cycle Count"){
            $field_list .= ", conf.wh_loc_id as bin_id, conf.wh_location as bin_location";
        }
        $prod_query = DB::table("vw_inventory_report as inv")->join("product_pack_config as PPC", "PPC.product_id", "=", "inv.product_id")
                    ->select([DB::raw($field_list)]);
                   // echo "<pre/>";print_r($prod_query);exit;
        if($auditType == "Cycle Count"){
            $prod_query = $prod_query->join("warehouse_config as conf", function($join){
                                $join->on("conf.le_wh_id", "=", "inv.le_wh_id")
                                     ->on("conf.pref_prod_id", "=", "inv.product_id");
                          });
        
            $binLocations = $this->assignedBinLocations();
            if(!empty($binLocations)){
                $prod_query = $prod_query->whereNotIn("conf.wh_loc_id", $binLocations);
            }
        }
       // $prod_query = $prod_query->where("PPC.pack_code_type", "=", "79002")->where("PPC.level", "=", "16001");
        $prod_query = $prod_query->where("PPC.level", "=", "16001");
        $prod_query = $prod_query->where("inv.le_wh_id", "=", $whId)
                    ->take($limit)->get();*/
        $query ="select inv.le_wh_id, inv.product_id, inv.product_title, inv.sku, inv.mrp, PPC.pack_sku_code AS ean FROM vw_inventory_report AS inv 
            INNER JOIN `product_pack_config` AS `PPC` 
            ON `PPC`.`product_id` = `inv`.`product_id`
            WHERE inv.`le_wh_id` = '".$whId."' AND `PPC`.`level` = 16001 
            AND NOT EXISTS (SELECT * FROM inventory_audit ia WHERE ia.`product_id` = inv.`product_id`
            AND ia.`created_at` BETWEEN CURDATE() AND NOW() AND ia.is_flag IN(0,1)) LIMIT $limit";
        $prod_query = DB::selectFromWriteConnection(DB::raw($query));

        return $prod_query;
    }

    public function updateflag($productid,$warehouse){

        $sql = DB::table("inventory_audit")
                ->where("product_id", "=", $productid)
                ->where("wh_id", "=", $warehouse)
                ->update(['is_flag'=> 1]);

        return $sql;

    }

    public function getCategoryListCycle($le_wh_id){

       /*  $sql = DB::table("categories")
                ->select("cat_name","category_id")
                 ->where("is_active", "=", 1)
                 ->where("legal_entity_id", "=", $le_id) 
                 ->get();*/

           /* $dc_id = DB::table("legalentity_warehouses")->where("legal_entity_id",$le_id)->pluck('le_wh_id');
                if($category_id == 0){
                    $categories='NULL';
               
                } else{

                    $categories="'".$categories."'";
                } */

        $sql = DB::select(DB::raw("CALL getCategoryInvProducts(NULL,'$le_wh_id',1)"));
        return $sql;

    }
 
}

