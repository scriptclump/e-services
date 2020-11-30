<?php
namespace App\Modules\InventoryAuditCycleCount\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Log;
use UserActivity;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Orders\Models\Inventory;   // update inv_update_logs table

use Utility;

class InventoryAuditCycleCountModel extends Model {
  protected $table        = "inventory_audit";

    public function filterOptions() {
        $filter_array                       = Array();
        $warehouses_table                   = DB::table('legalentity_warehouses');
        $roleOb                             = new Role();
        
        if (Session('roleId') != '1') {
            $warehouses_table               = $warehouses_table->where('legal_entity_id', Session::get('legal_entity_id'));
        }

        $filter_array['dc_name']            = $warehouses_table->where('lp_wh_name', '!=', NULL)->where('lp_wh_name', '!=', '')->where('dc_type', '=', '118001')->orderBy('lp_wh_name', 'asc')->pluck('lp_wh_name', 'le_wh_id')->all();
        return $filter_array;
    }

    public function getUsers()
    {
        try {
                $sql = DB::table("users as UU")
                        ->join("user_roles as UR", "UR.user_id", "=", "UU.user_id")
                        ->where("UU.is_active","=", 1)
                        ->where("legal_entity_id","=", 2)
                        ->where("UR.role_id", "=",56)   // 56 is picker role Id
                        ->get(array("email_id", DB::raw("concat(firstname,' ', lastname) as username")))->all();
                return json_decode(json_encode($sql), true);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function insertInventoryAudit($productsData, $warewhouseid, $URL)
    {

       try {
        $rolesObj               = new Role();
        $errorArray             = array();
        $mainArr                = array();
        $updateCounter          = 0;
        $allproductsForUSer     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        $allproductIds          = $allproductsForUSer['products'];
        $getallWarehouseIds     = $this->filterOptions();
        $i=1;
        $timestamp              = date('Y-m-d H:i:s');
        $current_timeStamp      = strtotime($timestamp);
        $insertcount    = 0;

        $email_lists = $this->getUsers();
        $All_mails = array_column($email_lists, "email_id");
        
        $insertedarray = array();
        $roleRepoObj = new RoleRepo();
        $whdetails =$roleRepoObj->getLEWHDetailsById($warewhouseid);
        $statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
        $prefix   = "IACC";

        $audit_code = Utility::getReferenceCode($prefix,$statecode);
        $inventory_audit_bulkupload_arr = array("file_path" => $URL, "excel_upload_logs_pk" => $current_timeStamp, "type" => "Cycle Count", "created_by" => \Session::get('userId'), "audit_code" => $audit_code);
        $inventory_audit_bulkupload_id = DB::table("inventory_audit_bulkupload")->insertGetId($inventory_audit_bulkupload_arr);
        
        $approval_flow_func                 = new CommonApprovalFlowFunctionModel();

        $ticket_creation = $approval_flow_func->notifyUserForFirstApproval("Inventory Bulk Audit", $inventory_audit_bulkupload_id, \Session::get('userId'));

        // $approval_flow_func->storeWorkFlowHistory('Inventory Bulk Audit', $inventory_audit_bulkupload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId')); 

        $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('Inventory Bulk Audit', 'drafted', \Session::get('userId'), $inventory_audit_bulkupload_id);


        
        // $curr_status_ID = $res_approval_flow_func['currentStatusId'];
        // $nextlevelStatusId = $res_approval_flow_func['data'][0]['nextStatusId'];
        $update_bulk_status = DB::table("inventory_audit_bulkupload")->where("bulk_audit_id", $inventory_audit_bulkupload_id)->update(array("approval_status" => "57129"));

       foreach ($productsData as $value) {
        
           $countWareAndProductId = $this->checkWareHouseAndProductId($warewhouseid, $value['product_id']);
             if($countWareAndProductId < 1)
             {
                  $errorArray['wrongCombination'][] = 'Line #' . ($i+2)." Product Does not existed for this warehouse <br>";
                  $i++;
                  continue;
             }

            if(!isset($allproductIds[$value['product_id']]))
            {
                $errorArray['productIderrors'][] = 'Line #' . ($i+2)." Invalid Product Ids <br>";
                $i++;
                continue;
            }

            if($value['users'] == "" || empty($value['users']))
           {
                $errorArray['userserrors'][] = 'Line #' . ($i+2)." Empty Users <br>";
                $i++;
                continue;
           }

           if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $value['users'])) {

                $errorArray['userserrors'][] = 'Line #' . ($i+2)." Invalid Email Id format <br>";
                $i++;
                continue;
            }

            if(!in_array($value['users'], $All_mails))
            {
                $errorArray['userserrors'][] = 'Line #' . ($i+2)." This email is not in our system <br>";
                $i++;
                continue;   
            }

            
           // $updatevals = $this->updateProductsForReplanishment($value['replanishment_level'], $allReplanishmenttCode[$value['replanishment_uom']], $value['product_id'], $warewhouseid);

            $getconfig_Data = $this->getWarehouseDetails($value['product_id'], $warewhouseid);
            if(empty($getconfig_Data))
            {
              $getconfig_details =  "";
            }else{
              $getconfig_details =  $getconfig_Data;
            }
            

            $getEAN = $this->productPackConfigDetails($value['product_id']);
            $getEAN = isset($getEAN[0]['pack_sku_code'])?$getEAN[0]['pack_sku_code']:"";

            if(!empty($getconfig_details))
            {
              foreach ($getconfig_details as $key => $configvalue) {

                $check_prod_auditexists = $this->checkProductAuditExists($value['product_id'], $warewhouseid, $configvalue['wh_loc_id'], array(0, 1));
              if($check_prod_auditexists == 0){

                  $insertedarray = array(
                    "product_id" => $value['product_id'],
                    "product_title" => $value['product_title'],
                    "wh_id" => $warewhouseid,
                    "location_id" => $configvalue['wh_loc_id'],
                    "location_code" => $configvalue['wh_location'],
                    "type" => "Cycle Count",
                    "status" => 0,
                    "assigned_by" => Session::get('userId'),
                    // "approval_status" => $nextlevelStatusId,
                    "mrp" => $this->getproductPrice($value['product_id']),
                    "auditor" => $this->getUserId($value['users']),
                    "EAN" => $getEAN,
                    "unique_audit_id" => $inventory_audit_bulkupload_id
                  );
                  $insert = $this->insertStocktake($insertedarray);

              }else{
                    $errorArray['wrongCombination'][] = "This Combination is already repeted for Product Id :".$value['product_id']." <br>";
                    continue;  
              }

                // $insertedarray[] = array(
                //     "product_id" => $value['product_id'],
                //     "product_title" => $value['product_title'],
                //     "wh_id" => $warewhouseid,
                //     "location_id" => $configvalue['wh_loc_id'],
                //     "location_code" => $configvalue['wh_location'],
                //     "type" => "Cycle Count",
                //     "status" => 0,
                //     "mrp" => $this->getproductPrice($value['product_id']),
                //     "auditor" => $this->getUserId($value['users']),
                //     "EAN" => $getEAN
                //   );

                 // $j++;
                 $insertcount++;
               } 
            }else{

                $errorArray['productIderrors'][] = 'Line #' . ($i+2)." This Product doesn't mapped <br>";
                $i++;
                continue;   
            }
           
           // if($updatevals == 1)
           // {
           //      $updateCounter++;
           // }

           // if($updatevals == 0)
           // {
           //  $errorArray['samerecords'][] = 'Line #' . ($i+2)." Duplicate data <br>";
           // }

            $i++;
       }

                      
       

       $mainArr['success']                  = $errorArray;
       // $mainArr['dpulicate_count']          = (isset($errorArray['samerecords'])?count($errorArray['samerecords']):0);
       $mainArr['updated_count']            = $insertcount;
       $wrong_combination                   = (isset($errorArray['wrongCombination'])?count(array_unique($errorArray['wrongCombination'])):0);
       $product_errors                      = (isset($errorArray['productIderrors'])?count(array_unique($errorArray['productIderrors'])):0);
       $commetErrorsCount                   = (isset($errorArray['userserrors'])?count(array_unique($errorArray['userserrors'])):0);
       $mainArr['error_count']              = $wrong_combination + $product_errors + $commetErrorsCount;

       $log_array                           = $mainArr;
       $mainArr['reference'] = $current_timeStamp;
       UserActivity::excelUploadFileLogs("INVENTORY AUDIT", $current_timeStamp, $URL, $log_array);
       
       return $mainArr;
    
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
          }
    
    }

    public function insertInventoryAuditStockTake($productsData, $warewhouseid, $URL)
    {
      try {
        $rolesObj               = new Role();
        $errorArray             = array();
        $mainArr                = array();
        $updateCounter          = 0;
        $allproductsForUSer     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        $allproductIds          = $allproductsForUSer['products'];
        $getallWarehouseIds     = $this->filterOptions();
        $i=1;
        $timestamp              = date('Y-m-d H:i:s');
        $current_timeStamp      = strtotime($timestamp);
        $insertcount    = 0;

        $email_lists = $this->getUsers();
        $All_mails = array_column($email_lists, "email_id");

        $roleRepoObj = new RoleRepo();
        $whdetails =$roleRepoObj->getLEWHDetailsById($warewhouseid);
        $statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
        $prefix  = "IAST";

        $audit_code = Utility::getReferenceCode($prefix,$statecode);
        $inventory_audit_bulkupload_arr = array("file_path" => $URL, "excel_upload_logs_pk" => $current_timeStamp,  "type" => "Stock Take", "created_by" => \Session::get('userId'), "audit_code" => $audit_code);
        $inventory_audit_bulkupload_id = DB::table("inventory_audit_bulkupload")->insertGetId($inventory_audit_bulkupload_arr);
        
        $insertedarray = array();


        $approval_flow_func                 = new CommonApprovalFlowFunctionModel();

        $ticket_creation = $approval_flow_func->notifyUserForFirstApproval("Inventory Bulk Audit", $inventory_audit_bulkupload_id, \Session::get('userId'));

        // $approval_flow_func->storeWorkFlowHistory('Inventory Bulk Audit', $inventory_audit_bulkupload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId')); 

        $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('Inventory Bulk Audit', 'drafted', \Session::get('userId'), $inventory_audit_bulkupload_id);



        // $curr_status_ID = $res_approval_flow_func['currentStatusId'];
        // $nextlevelStatusId = $res_approval_flow_func['data'][0]['nextStatusId'];
        $update_bulk_status = DB::table("inventory_audit_bulkupload")->where("bulk_audit_id", $inventory_audit_bulkupload_id)->update(array("approval_status" => "57129"));



       foreach ($productsData as $value) {
        $rackcode = "";
           $countWareAndProductId = $this->checkWareHouseAndProductId($warewhouseid, $value['product_id']);
             if($countWareAndProductId < 1)
             {
                  $errorArray['wrongCombination'][] = 'Line #' . ($i+2)." Product Does not existed for this warehouse <br>";
                  $i++;
                  continue;
             }

            if(!isset($allproductIds[$value['product_id']]))
            {
                $errorArray['productIderrors'][] = 'Line #' . ($i+2)." Invalid Product Ids <br>";
                $i++;
                continue;
            }


            
          if(($value['bin'] == "" || empty($value['bin'])))
           {
                $errorArray['userserrors'][] = 'Line #' . ($i+2)." Empty Bin<br>";
                $i++;
                continue;
           }
           
           $countbinsbyProductId = "";
          if(!empty($value['bin']) ) //when both rack and bin were came OR only bincode came
          {
            
            $countbinsbyProductId = $this->checkProductConfig($value['product_id'], $warewhouseid, $value['bin']); 
            $bincode = $value['bin'];
          }
          /* Aisle inserting code */
          // if(empty($value['bin']) && !empty($value['aisle'])) //when bin is empty and Rack is not empty
          // {
          //   $get_racks_All = $this->getallRacksByProduct($value['product_id'], $warewhouseid);
            
          //   $get_racks = array_column($get_racks_All, "rackcode");
          //   $get_racks = array_unique($get_racks);  //all available racks getting here
            
          //   if(!in_array($value['aisle'], $get_racks)){
          //     $errorArray['userserrors'][] = 'Line #' . ($i+2)." Invalid Rack for this product <br>";
          //     $i++;
          //     continue;
          //   }
              
          //   $rackcode = $value['aisle'];

          // } 

         if(!empty($value['bin']) && $countbinsbyProductId  < 1)
         {
            $errorArray['userserrors'][] = 'Line #' . ($i+2)." Invalid bin for this product <br>";
            $i++;
            continue;
         }
         
        if($value['users'] == "" || empty($value['users']))
         {
              $errorArray['userserrors'][] = 'Line #' . ($i+2)." Empty Users <br>";
              $i++;
              continue;
         }

       if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $value['users'])) {

            $errorArray['userserrors'][] = 'Line #' . ($i+2)." Invalid Email Id format <br>";
            $i++;
            continue;
        }

        if(!in_array($value['users'], $All_mails))
        {
            $errorArray['userserrors'][] = 'Line #' . ($i+2)." This email is not in our system <br>";
            $i++;
            continue;   
        }

            
        $get_loc_data = $this->getWarehouseDetails($value['product_id'], $warewhouseid, $value['bin']);
        
        $get_loc_id = $get_loc_data[0]['wh_loc_id'];

        $getEAN = $this->productPackConfigDetails($value['product_id']);
        $getEAN = isset($getEAN[0]['pack_sku_code'])?$getEAN[0]['pack_sku_code']:"";

        if(!empty($value['bin']))
        {
          $check_prod_auditexists = $this->checkProductAuditExists($value['product_id'], $warewhouseid, $get_loc_id , array(0, 1));
              if($check_prod_auditexists == 0){
                  $insertedarray = array(
                  "product_id" => $value['product_id'],
                  "product_title" => $value['product_title'],
                  "wh_id" => $warewhouseid,
                  "location_id" => $get_loc_id,
                  "location_code" => $value['bin'],
                  "type" => "Stock Take",
                  "status" => 0,
                  "assigned_by" => Session::get('userId'),
                  "mrp" => $this->getproductPrice($value['product_id']),
                  "auditor" => $this->getUserId($value['users']),
                  "EAN" => $getEAN,
                  "unique_audit_id" => $inventory_audit_bulkupload_id);

                  $insert = $this->insertStocktake($insertedarray);

              }else{
                    $errorArray['productId_error'][] = "This Combination is already repeted for Product Id :".$value['product_id']." <br>";
                    $i++;
                    continue;  
              }
               $i++;
              $insertcount++;
        }
        /*Aisle code*/
/*        else if(!empty($value['aisle'])){

              $getAllBinsByRack = $this->getallbinbasedonRack($value['product_id'], $rackcode, $warewhouseid);
              foreach ($getAllBinsByRack as $bin_key => $bin_value) 
              {
                $check_prod_auditexists = $this->checkProductAuditExists($value['product_id'], $warewhouseid, $bin_value['bin_id'], array(0, 1));

                  if($check_prod_auditexists == 0){
                          $insertedarray = array(
                          "product_id" => $value['product_id'],
                          "product_title" => $value['product_title'],
                          "wh_id" => $warewhouseid,
                          "location_id" => $bin_value['bin_id'],
                          "location_code" => $bin_value['bin_code'],
                          "type" => "Stock Take",
                          "status" => 0,
                          "mrp" => $this->getproductPrice($value['product_id']),
                          "auditor" => $this->getUserId($value['users']),
                          "EAN" => $getEAN,
                          "unique_audit_id" => $inventory_audit_bulkupload_id
                        );
                      $insert = $this->insertStocktake($insertedarray);
                  }else{
                        $errorArray['productId_error'][] = "This Combination is already repeted for Product Id :".$value['product_id']." <br>";
                        continue;  
                  }
               
                  $insertcount++; 
              }
            }*/
            
           

            // $i++;
       }

       $mainArr['success']                  = $errorArray;
       // $mainArr['dpulicate_count']          = (isset($errorArray['samerecords'])?count($errorArray['samerecords']):0);
       $mainArr['updated_count']            = $insertcount;
       $wrong_combination                   = (isset($errorArray['wrongCombination'])?count($errorArray['wrongCombination']):0);
       $product_errors                      = (isset($errorArray['productIderrors'])?count($errorArray['productIderrors']):0);
       $commetErrorsCount                   = (isset($errorArray['userserrors'])?count($errorArray['userserrors']):0);
       $productId_error                     = (isset($errorArray['productId_error'])?count(array_unique($errorArray['productId_error'])) : 0);
       $mainArr['error_count']              = $wrong_combination + $product_errors + $commetErrorsCount+$productId_error;

       $log_array                           = $mainArr;
       $mainArr['reference'] = $current_timeStamp;
       UserActivity::excelUploadFileLogs("INVENTORY AUDIT", $current_timeStamp, $URL, $log_array);
       
       return $mainArr;
    
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
          }
    
    
    }

  public function checkWareHouseAndProductId($warehouseId, $productId)
  {
      try {

            $sql        = DB::table("inventory")->where("product_id", "=", $productId)->where("le_wh_id", "=", $warehouseId);
            $count      = $sql->count();
            return $count;
          
      } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
        }
  }

  public function getWarehouseDetails($prod_id, $warehouseId, $binlocation="")
  {
    try {
        $sql = DB::table("warehouse_config")
              ->where("pref_prod_id", "=", $prod_id)
              ->where("le_wh_id", "=", $warehouseId);
        if($binlocation != "")
        {
          $sql = $sql->where("wh_location", $binlocation);
        }
              $sql = $sql->get(array("wh_loc_id", "wh_location"))->all();
        return json_decode(json_encode($sql), true);
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
    }
  }

  public function getallRacksByProduct($prod_id, $warehouseId)
  {
    try {
        $sql = DB::table("warehouse_config")
            ->where("pref_prod_id", "=", $prod_id)
            ->where("le_wh_id", "=", $warehouseId)
            ->get(array(DB::raw("SUBSTRING_INDEX(wh_location,'-',1) as rackcode")))->all();

      return json_decode(json_encode($sql), true);
    } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
    }
  }

  public function productPackConfigDetails($prod_id)
  {
    try {
          $sql = DB::table("product_pack_config")
                ->where("product_id", "=", $prod_id)
                ->where("pack_code_type", "=", 79002)
                ->where("level", "=", 16001)
                ->get(array("pack_sku_code"))->all();
          return json_decode(json_encode($sql), true);

    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
      }
  }

  public function insertInventoryAuditData($prod_id, $title, $warehouseId, $wh_loc_id, $wh_location, $ean)
  {
    try {
           $this->product_id = $prod_id;
           $this->product_title = $title;
           $this->wh_id = $warehouseId;
           $this->location_id = $wh_loc_id;
           $this->location_code = $wh_location;
           $this->EAN = $ean;
           $this->save();
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
      }
  }

  public function getproductPrice($prod_id)
  {
    try {

      $sql = DB::table("products")->where("product_id", "=", $prod_id)->get(array("mrp"))->all();
      $data = json_decode(json_encode($sql), true);
      return isset($data[0]['mrp'])?$data[0]['mrp']:"";
      
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
      }
  }

  public function getUserId($email)
  {
    try {
        $sql = DB::table("users")->where("email_id", "=", $email)->get(array("user_id"))->all();
      $data = json_decode(json_encode($sql), true);
      return isset($data[0]['user_id'])?$data[0]['user_id']:"";
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
      }
  }

  public function getAllProducts($warehouseId)
  {
    try {
          $Product_ids          = array();
          $rolesObj             = new Role();
          $productIDs           = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);
          $allPending_products  = DB::table("inventory_audit")
                                    ->distinct("product_id")->whereIn("status", [0,1])->pluck("product_id")->all();
          // echo "all pending products <pre>";print_r($allPending_products);die;

          $prod_ids             = array_keys($productIDs['products']);
          $Product_ids          = array_values(array_diff($prod_ids, $allPending_products));
          // echo "<pre>";print_r($Product_ids);die;

          
          $sql                  = DB::table("warehouse_config as WC")
                                
                                    ->join("vw_inventory_report as VIR",function($join){
                                            $join->on("VIR.product_id",  "=", "WC.pref_prod_id")
                                            ->on("VIR.le_wh_id",  "=", "WC.le_wh_id");
                                    })

                                     ->leftJoin("inventory_audit as IA",function($join){
                                            $join->on("IA.product_id",  "=", "WC.pref_prod_id")
                                            ->on("IA.wh_id", "=", "WC.le_wh_id");
                                    })
                                    ->where('WC.le_wh_id', '=', $warehouseId)
                                    ->where(function($query){
                                            return $query
                                            ->whereNull('IA.status')
                                            ->orWhereNotIn('IA.status', [0,1]);
                                  })->groupBy("WC.pref_prod_id");
                              
          $sql                  = $sql->whereIn('WC.pref_prod_id', $Product_ids);
          
          $result               = $sql->get(array('VIR.product_id', 'VIR.product_title', 'VIR.sku', 'VIR.category_name', 'VIR.brand_name', 'VIR.manufacturer_name', 'VIR.mrp', 'VIR.soh', 'VIR.product_group_id'))->all();



          $result               = json_decode(json_encode($result), true);
          
          return $result;
    
      
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
      }
  }


    public function getAllProductsForST($warehouseId)
  {
    try {
        $rolesObj       = new Role();
        $productIDs     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);
        
        $prod_ids       = array_keys($productIDs['products']);
        
        $all_bins_arr   = array();
        $alllocations   = DB::table("warehouse_config")
                          ->join("vw_inventory_report",function($join){
                                $join->on("vw_inventory_report.le_wh_id", "=", "warehouse_config.le_wh_id")
                                ->on("vw_inventory_report.product_id", "=", "warehouse_config.pref_prod_id");
                        })
                          // ->join("vw_inventory_report", "vw_inventory_report.product_id", "=", "warehouse_config.pref_prod_id")
                          ->whereIn("pref_prod_id", $prod_ids)
                          ->where("warehouse_config.le_wh_id", "=", $warehouseId)
                          ->get(array("wh_loc_id", "wh_location", "pref_prod_id", "product_id", "product_title", "sku", "category_name", "brand_name", "manufacturer_name", "mrp", "soh", "product_group_id"))->all();
        $allproduct_locations = json_decode(json_encode($alllocations), true);

        $getAllPendingLocs = DB::table("inventory_audit")->where("wh_id", $warehouseId)->whereIn("status", [0, 1])->pluck("location_code")->all();
        
        foreach ($allproduct_locations as $key => $value) {
          if(!in_array($value['wh_location'],$getAllPendingLocs))
          {
          // $getprod_Data = DB::table("vw_inventory_report")->where("product_id", $value['pref_prod_id'])->get(array('product_id', 'product_title', 'sku', 'category_name', 'brand_name', 'manufacturer_name', 'mrp', 'soh'));
            // $getProductsInfo = json_decode(json_encode($getprod_Data), true);
            $all_bins_arr[$value['wh_location']] = array("wh_location" => $value['wh_location'], "location_id" => $value['wh_loc_id'], "product_id" => $value['pref_prod_id'], "product_title" => $value['product_title'], "sku" => $value['sku'], "category_name" => $value['category_name'], "brand_name" => $value['brand_name'], "manufacturer_name" => $value['manufacturer_name'], "mrp" => $value['mrp'], "soh" => $value['soh'], "product_group_id" => $value['product_group_id']); //
          }
        }
        return $all_bins_arr;
    
      
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
      }
  }


  public function checkProductConfig($prod_id, $warehouse_id, $bin)
  {
    try {
          $sql = DB::table("warehouse_config")
                  ->where("pref_prod_id", $prod_id)
                  ->where("le_wh_id", $warehouse_id)
                  ->where("wh_location", $bin)
                  ->count();
          return $sql;
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
      }
  }

  public function getallbinbasedonRack($prod_id, $rackcode, $warehouseId)
  {
    try {
          $onlybins = array();
          $sql = DB::table("warehouse_config")
                  ->where("pref_prod_id", $prod_id)
                  ->where("le_Wh_id", $warehouseId)
                  ->get(array("wh_location", "wh_loc_id"))->all();
          $data = json_decode(json_encode($sql), true);

          foreach ($data as $key => $value) {
            // $onlybins = array();
            $explode_data = explode("-", $value['wh_location']);
              if($explode_data[0] == $rackcode)
              {
                $onlybins[$key]['bin_code'] = $value['wh_location'];
                $onlybins[$key]['bin_id']  = $value['wh_loc_id'];
               }
          }
          return $onlybins;
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
      }
  }

  public function insertStocktake($insertedarray){
    try {
        $result = $this::insert($insertedarray);
        return  $result;
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
    }
  }

  public function checkProductAuditExists($prod_id, $warewhouseid, $bin_id, $statusArr)
  {
    try {
          $sql = $this
                ->where("product_id", $prod_id)
                ->where("wh_id", $warewhouseid)
                ->where("location_id", $bin_id)
                ->whereIn("status", $statusArr)
                ->count();

          return $sql;
    } catch (\ErrorException $ex) {
              Log::error($ex->getMessage());
    }
  }

  public function getBulkDataByAuditID($purpose, $audit_id, $status="")
  {
    try {
          $audit_data = DB::table("inventory_audit")->where("unique_audit_id", $audit_id)->get(array("audit_date"));
          $audit_date = json_decode(json_encode($audit_data), true);
          

          $current_date = date("Y-m-d");
          $end_date = $current_date." 23:59:59";
          $audit_date = isset($audit_date[0]['audit_date'])?$audit_date[0]['audit_date']:$current_date;
          $fields = array(
                          "IA.wh_id",
                          "IA.product_id",
                          "II.product_title", 
                          "II.soh", 
                          "IA.old_soh as opening_balance",
                          "IA.location_code",
                          DB::raw("IF(BI.qty IS NULL or BI.qty = '', 0, BI.qty) as bin_qty"), 
                          "II.quarantine_qty",
                          DB::raw("IF(IA.good_qty IS NULL or IA.good_qty = '', 0, IA.good_qty) as good_qty"),
                          DB::raw("IF(IA.damage_qty IS NULL or IA.damage_qty = '', 0, IA.damage_qty) as damage_qty"),
                          DB::raw("IF(IA.expire_qty IS NULL or IA.expire_qty = '', 0, IA.expire_qty) as expire_qty"),
                          DB::raw("IF(IA.old_bin_qty IS NULL or IA.old_bin_qty = '', 0, IA.old_bin_qty) as old_bin_qty"),
                          "BI.bin_id",
                          DB::raw("getProductSKU(IA.product_id) as sku"),
                          "IA.appr_good_qty",
                          "IA.appr_damage_qty",
                          "IA.appr_expire_qty",
                          "II.mrp",
                          "II.elp",
                          DB::raw("GetUserName(IA.auditor, 2) as updated_by"),
                          "IA.new_location_code",
                          "IA.status",
                          "BI.qty as bin_qty"
                        );

          

          $sql = DB::table("inventory_audit as IA")
                      ->join("vw_inventory_report as II",function($join){
                                $join->on("IA.wh_id", "=", "II.le_wh_id")
                                ->on("IA.product_id", "=", "II.product_id");
                        })->leftJoin("bin_inventory as BI",function($join){
                                $join->on("IA.wh_id", "=", "BI.wh_id")
                                ->on("IA.product_id", "=", "BI.product_id")
                                ->on("IA.location_id", "=", "BI.bin_id");
                        });
          // echo $status;die;
          if($status == "completed")
          {
            $sql = $sql->whereNotNull('good_qty');
          }else if($status == "pending"){
            $sql = $sql->WhereNull('good_qty');
          } 

          if($purpose == "download")
          {

          $update_inventory_audit_status = DB::table("inventory_audit")
                                          ->where("unique_audit_id", $audit_id)
                                          ->update(array("status" => 1));
            // $sql = $sql->whereNotNull('good_qty');
          }
          $sql = $sql->where("IA.unique_audit_id", $audit_id)->groupBy("IA.location_id");
          $sql = $sql->get($fields)->all();

          $data  = json_decode(json_encode($sql), true);
          foreach ($data as $key => $value) {

            $curr_bin_qty = $this->getOldBinQty($value['product_id'], $value['wh_id'], $value['bin_id']);
            // if($curr_bin_qty == "")
            // {
            //   continue;
            // }
            $excess_qty = 0;
            $missing_qty = $curr_bin_qty - ($value['good_qty'] + $value['damage_qty'] + $value['expire_qty']);
            
            if($missing_qty < 0)
            {
              $excess_qty = ltrim($missing_qty, "-");
              $missing_qty = 0;
            }

            $approved_excess_qty = 0;
            $approved_missing_qty = $curr_bin_qty - ($value['appr_good_qty'] + $value['appr_damage_qty'] + $value['appr_expire_qty']);
            
            if($approved_missing_qty < 0)
            {
            
              $approved_excess_qty = ltrim($approved_missing_qty, "-");
              $approved_missing_qty = 0;
            }
            
            $ret_sql = "select product_id, sum(qty) as qty, GO.le_wh_id FROM gds_orders AS GO INNER JOIN gds_returns AS GR ON GO.gds_order_id = GR.gds_order_id AND GO.le_wh_id = ".$value['wh_id']." AND GR.product_id = ".$value['product_id']." WHERE GR.created_at BETWEEN '".$audit_date."' AND '".$end_date."' ";
            
            $returnsql = DB::select( DB::raw($ret_sql) );
            $returndata = json_decode(json_encode($returnsql), true);
            $sales_return_data = $this->pendingReturns($value['product_id'], $value['wh_id']);
            // $data[$key]['sales_return_qty'] = isset($returndata[0]['qty'])?$returndata[0]['qty']:0;
            $data[$key]['sales_return_qty'] = $sales_return_data;


            $grn_q = "select product_id, good_qty  from stock_inward where created_at between '".$audit_date."' AND '".$end_date."' ";
            $GRN_sql = DB::select(DB::raw($grn_q));
            $grn_data = json_decode(json_encode($GRN_sql), true);
            $data[$key]['grn_qty'] = isset($grn_data[0]['good_qty'])?$grn_data[0]['good_qty']:0;

            $purchase_return_sql = "select PRP.qty From inward as I left join purchase_returns as PR on PR.inward_id = I.inward_id left join purchase_return_products as PRP on PR.pr_id = PRP.pr_id where I.le_wh_id = ".$value['wh_id']." and PRP.product_id = ".$value['product_id']." and PRP.created_at BETWEEN '".$audit_date."' AND '".$end_date."' ";
            $Purchase_sql = DB::select(DB::raw($purchase_return_sql));
            $purchase_Data = json_decode(json_encode($Purchase_sql), true);
            $data[$key]['purchase_return_qty'] = isset($purchase_Data[0]['qty'])?$purchase_Data[0]['qty']:0;

            $picked_query = "select sum(qty) as qty from gds_orders as GO join gds_ship_grid as GSG on GO.gds_order_id = GSG.gds_order_id join gds_ship_products as GSP on GSG.gds_ship_grid_id = GSP.gds_ship_grid_id where GO.le_wh_id = ".$value['wh_id']." and GSP.product_id =".$value['product_id']." and GSP.created_at BETWEEN '".$audit_date."' AND '".$end_date."' ";
            $picked_sql = DB::select(DB::raw($picked_query));
            $ppicked_Data = json_decode(json_encode($picked_sql), true);
            $data[$key]['picked_qty'] = isset($ppicked_Data[0]['qty'])?$ppicked_Data[0]['qty']:0;
            $data[$key]['missing_qty'] = $missing_qty;
            $data[$key]['excess_qty'] = $excess_qty;
            if($value['appr_good_qty'] == "" && $value['good_qty'] != "")
            {
              $data[$key]['deviation_value'] = $value['bin_qty'] - ($value['good_qty'] + $value['damage_qty']+$value['expire_qty']);
            } else if ($value['appr_good_qty'] == "" && $value['good_qty'] == ""){
              $data[$key]['deviation_value'] = 0;
            } else if($value['appr_good_qty'] != "" && $value['good_qty'] != ""){
              $data[$key]['deviation_value'] = $value['bin_qty'] - ($value['appr_good_qty'] + $value['appr_damage_qty']+$value['appr_expire_qty']);              
            }else{
             $data[$key]['deviation_value'] = 0; 
            }
             
            if(strlen($value['appr_good_qty']) != 0 && strlen($value['appr_damage_qty']) != 0 && strlen($value['appr_expire_qty']) != 0)
            {
              $data[$key]['appr_missing_qty'] = $approved_missing_qty;
              $data[$key]['appr_excess_qty'] = $approved_excess_qty;
            }else{
              $data[$key]['appr_missing_qty'] = "";
              $data[$key]['appr_excess_qty'] = "";
            }

            if($value['good_qty'] == 0 && $value['damage_qty'] == 0 && $value['expire_qty'] == 0)
            {
              $data[$key]['missing_qty'] = 0;
            }

            // if($value['appr_good_qty'] == 0 && $value['appr_damage_qty'] == 0 && $value['appr_expire_qty'] == 0)
            // {
            //   $data[$key]['appr_missing_qty'] = 0;
            // }



          }
          

          return $data;
          
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }


  public function getBulkDataByAuditIDClosedTkts($purpose, $audit_id, $status="")
  {
    try {
          $audit_data = DB::table("inventory_audit")->where("unique_audit_id", $audit_id)->get(array("audit_date"))->all();
          $audit_date = json_decode(json_encode($audit_data), true);
          

          $current_date = date("Y-m-d");
          $end_date = $current_date." 23:59:59";
          $audit_date = isset($audit_date[0]['audit_date'])?$audit_date[0]['audit_date']:$current_date;
          $fields = array(
                          "IA.wh_id",
                          "IA.product_id",
                          "II.product_title", 
                          "II.soh", 
                          "IA.old_soh as opening_balance",
                          "IA.location_code",
                          DB::raw("IF(BI.qty IS NULL or BI.qty = '', 0, BI.qty) as bin_qty"), 
                          "II.quarantine_qty",
                          DB::raw("IF(IA.good_qty IS NULL or IA.good_qty = '', 0, IA.good_qty) as good_qty"),
                          DB::raw("IF(IA.damage_qty IS NULL or IA.damage_qty = '', 0, IA.damage_qty) as damage_qty"),
                          DB::raw("IF(IA.expire_qty IS NULL or IA.expire_qty = '', 0, IA.expire_qty) as expire_qty"),
                          DB::raw("IF(IA.old_bin_qty IS NULL or IA.old_bin_qty = '', 0, IA.old_bin_qty) as old_bin_qty"),
                          "BI.bin_id",
                          DB::raw("getProductSKU(IA.product_id) as sku"),
                          "IA.appr_good_qty",
                          "IA.appr_damage_qty",
                          "IA.appr_expire_qty",
                          "II.mrp",
                          "II.elp",
                          DB::raw("GetUserName(IA.updated_by, 2) as updated_by"),
                          "IA.new_location_code",
                          "IA.appr_good_qty",
                          "IA.appr_damage_qty",
                          "IA.appr_expire_qty"
                        );

          

          $sql = DB::table("inventory_audit as IA")
                      ->join("vw_inventory_report as II",function($join){
                                $join->on("IA.wh_id", "=", "II.le_wh_id")
                                ->on("IA.product_id", "=", "II.product_id");
                        })->leftJoin("bin_inventory as BI",function($join){
                                $join->on("IA.wh_id", "=", "BI.wh_id")
                                ->on("IA.product_id", "=", "BI.product_id")
                                ->on("IA.location_id", "=", "BI.bin_id");
                        });
          if($status == "completed")
          {
            $sql = $sql->whereNotNull('good_qty');
          }else if($status == "pending"){
            $sql = $sql->WhereNull('good_qty');
          } 

          if($purpose == "download")
          {

          $update_inventory_audit_status = DB::table("inventory_audit")
                                          ->where("unique_audit_id", $audit_id)
                                          ->update(array("status" => 1));
            
          }
          $sql = $sql->where("IA.unique_audit_id", $audit_id)->groupBy("IA.location_id");
          $sql = $sql->get($fields)->all();

          $data  = json_decode(json_encode($sql), true);
          foreach ($data as $key => $value) {

            $curr_bin_qty = $this->getOldBinQty($value['product_id'], $value['wh_id'], $value['bin_id']);
            // if($curr_bin_qty == "")
            // {
            //   continue;
            // }
            $excess_qty = 0;
            $missing_qty = $curr_bin_qty - ($value['good_qty'] + $value['damage_qty'] + $value['expire_qty']);
            
            if($missing_qty < 0)
            {
              $excess_qty = ltrim($missing_qty, "-");
              $missing_qty = 0;
            }

            $approved_excess_qty = 0;
            $approved_missing_qty = $curr_bin_qty - ($value['appr_good_qty'] + $value['appr_damage_qty'] + $value['appr_expire_qty']);
            
            if($approved_missing_qty < 0)
            {
            
              $approved_excess_qty = ltrim($approved_missing_qty, "-");
              $approved_missing_qty = 0;
            }
            
            $ret_sql = "select product_id, sum(qty) as qty, GO.le_wh_id FROM gds_orders AS GO INNER JOIN gds_returns AS GR ON GO.gds_order_id = GR.gds_order_id AND GO.le_wh_id = ".$value['wh_id']." AND GR.product_id = ".$value['product_id']." WHERE GR.created_at BETWEEN '".$audit_date."' AND '".$end_date."' ";
            
            $returnsql = DB::select( DB::raw($ret_sql) );
            $returndata = json_decode(json_encode($returnsql), true);
            $sales_return_data = $this->pendingReturns($value['product_id'], $value['wh_id']);
            // $data[$key]['sales_return_qty'] = isset($returndata[0]['qty'])?$returndata[0]['qty']:0;
            $data[$key]['sales_return_qty'] = $sales_return_data;


            $grn_q = "select product_id, good_qty  from stock_inward where created_at between '".$audit_date."' AND '".$end_date."' ";
            $GRN_sql = DB::select(DB::raw($grn_q));
            $grn_data = json_decode(json_encode($GRN_sql), true);
            $data[$key]['grn_qty'] = isset($grn_data[0]['good_qty'])?$grn_data[0]['good_qty']:0;

            $purchase_return_sql = "select PRP.qty From inward as I left join purchase_returns as PR on PR.inward_id = I.inward_id left join purchase_return_products as PRP on PR.pr_id = PRP.pr_id where I.le_wh_id = ".$value['wh_id']." and PRP.product_id = ".$value['product_id']." and PRP.created_at BETWEEN '".$audit_date."' AND '".$end_date."' ";
            $Purchase_sql = DB::select(DB::raw($purchase_return_sql));
            $purchase_Data = json_decode(json_encode($Purchase_sql), true);
            $data[$key]['purchase_return_qty'] = isset($purchase_Data[0]['qty'])?$purchase_Data[0]['qty']:0;

            $picked_query = "select sum(qty) as qty from gds_orders as GO join gds_ship_grid as GSG on GO.gds_order_id = GSG.gds_order_id join gds_ship_products as GSP on GSG.gds_ship_grid_id = GSP.gds_ship_grid_id where GO.le_wh_id = ".$value['wh_id']." and GSP.product_id =".$value['product_id']." and GSP.created_at BETWEEN '".$audit_date."' AND '".$end_date."' ";
            $picked_sql = DB::select(DB::raw($picked_query));
            $ppicked_Data = json_decode(json_encode($picked_sql), true);
            $data[$key]['picked_qty'] = isset($ppicked_Data[0]['qty'])?$ppicked_Data[0]['qty']:0;
            $data[$key]['missing_qty'] = $missing_qty;
            $data[$key]['excess_qty'] = $excess_qty;
            if(strlen($value['appr_good_qty']) != 0 && strlen($value['appr_damage_qty']) != 0 && strlen($value['appr_expire_qty']) != 0)
            {
              $data[$key]['appr_missing_qty'] = $approved_missing_qty;
              $data[$key]['appr_excess_qty'] = $approved_excess_qty;
            }else{
              $data[$key]['appr_missing_qty'] = "";
              $data[$key]['appr_excess_qty'] = "";
            }

            if($value['good_qty'] == 0 && $value['damage_qty'] == 0 && $value['expire_qty'] == 0)
            {
              $data[$key]['missing_qty'] = 0;
            }

            // if($value['appr_good_qty'] == 0 && $value['appr_damage_qty'] == 0 && $value['appr_expire_qty'] == 0)
            // {
            //   $data[$key]['appr_missing_qty'] = 0;
            // }



          }
          

          return $data;
          
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function getOldBinQty($prod_id, $warehouseID, $bin_id)
  {
    try {
          $sql = DB::table("bin_inventory")->where("bin_id", $bin_id)->where("product_id", $prod_id)->where("wh_id", $warehouseID)->get(array("qty"))->all();
          $data  = json_decode(json_encode($sql), true);
         return isset($data[0]['qty'])?$data[0]['qty']:"";
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }

  }

  public function getSkuByProductId($pid)
  {
    try {
          $sql = DB::table("products")->where("product_id", $pid)->get(array('sku'))->all();
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }


  public function updateInventoryAuditData($audit_id, $auditData, $url)
  {
    try {
          $htmlData = "<table class='table table-border table-hover table-advance ' border='1'><thead><tr> <th>Warehouse Id</th> <th>Product ID</th> <th>Product Name</th> <th>SKU</th> <th>MRP</th> <th>ELP</th> <th>Opening Balance</th> <th>SOH</th> <th>Pending Return Qty</th> <th>Purchase Returns</th> <th>Picked Qty</th> <th>Quarantine Qty</th> <th>Location</th> <th>Bin Qty</th> <th>Assigned By</th>  <th>Good Qty</th> <th>Damaged Qty</th> <th>Damage ELP</th> <th>Expired Qty</th> <th>Expired ELP</th> <th>Short Qty</th> <th>Short ELP</th>  <th>Excess Qty</th> <th>Excess ELP</th> <th>Current Bin Qty</th> <th>Deviation Value</th> <th>Approved Good Qty</th> <th>Approved Damaged Qty</th> <th>Approved Expired Qty</th> <th>Approved Short Qty</th> <th>Approved Excess Qty</th>  </tr><thead><tbody>";
          $insertion_counter = 0;
          $error_counter = 1;
          $excel_counter = 0;
          $total_size = sizeof($auditData);
          foreach ($auditData as $key => $value) {
            $deviationVal = $value['bin_qty'] - ($value['approved_good_qty'] + $value['approved_damaged_qty']+$value['approved_expired_qty']);
            $htmlData .= "<tr><td>".$value['warehouse_id']."</td> <td>".$value['product_id']."</td> <td>".$value['product_name']."</td><td>".$value['sku']."</td><td>".$value['mrp']."</td><td>".$value['elp']."</td> <td>". $value['opening_balance'] ."</td> <td>".$value['soh']."</td> <td>".$value['pending_return_qty']."</td> <td>".$value['purchase_returns']."</td> <td>".$value['picked_qty']."</td> <td>".$value['quarantine_qty']."</td> <td>".$value['location']."</td> <td>".$value['bin_qty']."</td> <td>".$value['updated_by']."</td> <td>".$value['good_qty']."</td> <td>".$value['damaged_qty']."</td> <td>".$value['damaged_qty']*$value['elp']."</td> <td>".$value['expired_qty']."</td> <td>".$value['expired_qty'] * $value['elp']."</td> <td>".$value['short_qty']."</td> <td>".$value['short_qty']*$value['elp']."</td> <td>".$value['excess_qty']."</td> <td>".$value['excess_qty']*$value['elp']."</td> <td>".$value['current_bin_qty']."</td> <td>".$deviationVal."</td> <td>".$value['approved_good_qty']."</td> <td>".$value['approved_damaged_qty']."</td> <td>".$value['approved_expired_qty']."</td>";
            $arr = array();

            if(!isset($value['approved_good_qty']) && !isset($value['appr_damage_qty']) && !isset($value['approved_expired_qty']))
            {
              
              $excel_counter++;
            }
            
            if((strlen($value['approved_good_qty']) == 0))
            {
            
              $error_counter++;
              continue;
            }

            if($value['approved_good_qty'] < 0 || !is_numeric($value['approved_good_qty']))
            {
            
              $error_counter++;
              continue;
            }

            if(strlen($value['approved_good_qty']) != 0)
            {
            
              $arr = array("appr_good_qty" => $value['approved_good_qty']);
            }
            

            if($value['approved_damaged_qty'] < 0 || !is_numeric($value['approved_damaged_qty']))
            {
            
              $error_counter++;
              // continue;
            }
            
            if(strlen($value['approved_damaged_qty']) != 0)
            {
            
              $arr = array_merge($arr, array("appr_damage_qty" => $value['approved_damaged_qty']));
            }



            if($value['approved_expired_qty'] < 0 || !is_numeric($value['approved_expired_qty']))
            {
            
              $error_counter++;
              // continue;
            }

            if(strlen($value['approved_expired_qty']) != 0)
            {
            
              $arr = array_merge($arr, array("appr_expire_qty" => $value['approved_expired_qty']));
            }
            
           $excessQty = 0;
          $missingQty = $value['current_bin_qty'] - ($value['approved_good_qty'] + $value['approved_damaged_qty'] + $value['approved_expired_qty']);
          if($missingQty < 0)
          {
            $excessQty = ltrim($missingQty, "-");
            $missingQty = 0;
          }
            
          $htmlData .= "<td>".$missingQty."</td> <td>".$excessQty."</td></tr>";
            $update_audit_data = $this->updateAuditData($value['location'], $value['warehouse_id'], $value['product_id'], $arr);
            if($update_audit_data)
                {
                  $insertion_counter++;
                }

          }
          $htmlData .= "</tbody></table>";
          $error_Counter = $this->getAllItemsApprovedCount($audit_id);
          
          $final_data  = array("htmldata" => $htmlData, "total_insertions" => $insertion_counter, "total_fails" => $total_size -$insertion_counter, "excel_error_counter" => $excel_counter, "error_counter" => $error_Counter);
          
          return $final_data;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function updateAuditData($location, $warehouse_id, $product_id, $dataArr)
  {
    try {
          $sql = DB::table("inventory_audit")
                    ->where("location_code", $location)
                    ->where("wh_id", $warehouse_id)
                    ->where("product_id", $product_id)
                    ->update($dataArr);
          return $sql;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function getAllStatusCounts($audit_id)
  {
    try {
          $mainarr['all'] = DB::table('inventory_audit')->where('unique_audit_id', $audit_id)->count();
          $mainarr['completed'] = DB::table('inventory_audit')->where('unique_audit_id', $audit_id)->whereNotNull('good_qty')->count();
          $mainarr['pending'] = DB::table('inventory_audit')->where('unique_audit_id', $audit_id)->WhereNull('good_qty')->count();
          return $mainarr;

    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }


  public function getStatusByAuditID($audit_id)
  {
    try {
          $sql = DB::table("inventory_audit_bulkupload")->where("bulk_audit_id", $audit_id)->get(array("approval_status"))->all();
          $data  = json_decode(json_encode($sql), true);
          return $data;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function updateBulkAuditTablewithStatus($tableUpdateID, $trackId)
  {
    try {
          if($tableUpdateID == 57129)
          {

            $updatequery = DB::table("inventory_audit")->where("unique_audit_id", $trackId)->update(array("status" => 2, "appr_good_qty" => DB::raw("NULL"), "appr_damage_qty" => DB::raw("NULL"), "appr_expire_qty" => DB::raw("NULL")));
          }
          $sql = DB::table("inventory_audit_bulkupload")
                        ->where("bulk_audit_id", $trackId)
                        ->update(array("approval_status" => $tableUpdateID));
          return $sql;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function updateBinInventory($trackId, $apprval_status)
  {
    try {
          $fields = array("IA.good_qty", "IA.damage_qty", "IA.expire_qty", "IA.appr_good_qty", "IA.appr_damage_qty", "IA.appr_expire_qty", "IA.product_id", "IA.wh_id", "IA.location_id", "BI.qty as curr_bin_qty");

          $all_bulk_Details = DB::table("inventory_audit as IA")
                                ->join("bin_inventory as BI",function($join){
                                $join->on("BI.product_id", "=", "IA.product_id")
                                ->on("BI.wh_id", "=", "IA.wh_id")
                                ->on("BI.bin_id", "=", "IA.location_id");
                                  })->where("IA.unique_audit_id", $trackId)->get($fields)->all();

          $bulk_data = json_decode(json_encode($all_bulk_Details), true);

          foreach ($bulk_data as $bulkkey => $bulkvalue) {
            $appr_missing_qty = "";
            $appr_excess_qty = 0;

            $missing_qty = "";
            $excess_qty = 0;
            
            $dit_qty = $bulkvalue['damage_qty'] + $bulkvalue['expire_qty'];
            $missing_qty = $bulkvalue['curr_bin_qty'] -( $bulkvalue['good_qty'] + $dit_qty );

            $appr_dit_qty = $bulkvalue['appr_damage_qty'] + $bulkvalue['appr_expire_qty'];
            $appr_missing_qty = $bulkvalue['curr_bin_qty'] -( $bulkvalue['appr_good_qty'] + $appr_dit_qty );

            if($appr_missing_qty < 0)
            {
              $appr_excess_qty = ltrim($appr_missing_qty, "-");
              $appr_missing_qty = 0;
            }

            if($missing_qty < 0)
            {
              $excess_qty = ltrim($missing_qty, "-");
              $missing_qty = 0;
            }
            if($apprval_status == "approved")
            {

              $deduct_quantities_bin_inventory = $this->deductBinInventory($bulkvalue['product_id'], $bulkvalue['wh_id'], $bulkvalue['location_id'], $appr_dit_qty, $appr_missing_qty, $appr_excess_qty, $trackId);

              $deduct_inventory_SOH = $this->deductSOHQty($trackId, $bulkvalue['product_id'], $bulkvalue['wh_id'], $appr_dit_qty, $appr_missing_qty, $appr_excess_qty, $dit_qty, $missing_qty);
            }
            
            // $deduct_inventory_quaratine = $this->deductQuarantineQty($bulkvalue['product_id'], $bulkvalue['wh_id'], $dit_qty, $missing_qty, $excess_qty);

          }
          $update_audit_status = $this->updateInventoryAuditStatus($trackId);
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function updateInventoryAuditStatus($ticket_id)
  {
    try {
          $oldvaluesArray = array();
          $uniquevalues = array("unique_audit_id" => $ticket_id);
          $new_values = array();

          UserActivity::userActivityLog("Inventory Audit", $new_values, "Here Audting ticket was final approved(In Inventory Audit table, the status is changed to 2)" , $oldvaluesArray, $uniquevalues);

            $sql = DB::table("inventory_audit")->where("unique_audit_id", $ticket_id)->update(array("status" => 2));

          
            return $sql;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function deductBinInventory($product_id, $wh_id, $location_id, $dit_qty, $missing_qty, $excessQty, $workflowID)
  {
    try {
          $queryappend = "";
          
          $newvalues = array();  //logs
          $resulted_bin_qty = 0; //logs
          $total_quantity = $dit_qty+$missing_qty;

           $query = DB::table('bin_inventory')
                                ->where("product_id", "=", $product_id)
                                ->where("wh_id", "=", $wh_id)
                                ->where("bin_id", "=", $location_id);
           $oldBinDetails  =   $query->get(array("qty"))->all();

           $oldBinData = json_decode(json_encode($oldBinDetails), true);
           $oldBinQty = $oldBinData[0]['qty'];
           $oldvaluesArray = array("old_bin_qty" => $oldBinQty);

          if($excessQty > 0)
          {
            $queryappend = ", qty = qty+".$excessQty;
            $resulted_bin_qty = $oldBinQty+$excessQty;
            $query->increment("qty", $excessQty);
            $newvalues = array("newqty(excessqty_existed)" => $resulted_bin_qty."(if excess qty is existed)");  //logs
          }

          $newvalues = array("newqty" => ($resulted_bin_qty - $total_quantity));  //logs
          $uniquevalues = array("product_id" => $product_id, "warehouse_id" => $wh_id, "location_id" => $location_id, "bulk_upload_id" => $workflowID);
          $query = $query->decrement('qty', $total_quantity);


          UserActivity::userActivityLog("Inventory Audit", $newvalues, "Deducting Bin quantity(from bin_inventory table) in Approval Workflow(Approval workflowId/bulkuploadId : ".$workflowID." )", $oldvaluesArray, $uniquevalues);  //logs
          return $query;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function deductQuarantineQty($product_id, $wh_id, $dit_qty, $missing_qty, $excessQty)
  {
    try {
          // deducting dit and missing qty from quarantine qty
          $deducting_qty = $dit_qty+$missing_qty;

         $query_quarantine = DB::table('inventory')
                    ->where("product_id", "=", $product_id)
                    ->where("le_wh_id", "=", $wh_id)->get(array("quarantine_qty"))->all();

          $quarantine_qty  = json_decode(json_encode($query_quarantine), true);
          $old_quarantine_qty = $quarantine_qty[0]['quarantine_qty'];
          $resulted_quarantine_qty = $old_quarantine_qty - $deducting_qty;

          $oldvaluesArray = array("old_quarantine_qty" => $old_quarantine_qty);
          $newvalues = array("new_quarantine_qty" => $resulted_quarantine_qty);
          $uniquevalues = array("product_id" => $product_id, "warehouse_id" => $wh_id);

          UserActivity::userActivityLog("Inventory Audit", $newvalues, "Deducted Quarantine Qty from inventory table for the product: ".$product_id." and warehouse ID ", $oldvaluesArray, $uniquevalues);  //logs

           $query = DB::table('inventory')
                                ->where("product_id", "=", $product_id)
                                ->where("le_wh_id", "=", $wh_id)
                                ->decrement('quarantine_qty', $deducting_qty);

          return $query;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }


  public function deductSOHQty($ticlet_id, $product_id, $wh_id, $dit_qty, $missing_qty, $excessQty, $deducted_dit, $deducted_dnd)
  {
    try {

          $newvalues = array();
          $obj = new Inventory();

          
          $quarantine_qty = ($dit_qty + $missing_qty);
          $update_quarantine = DB::table('inventory')
                                ->where("product_id", "=", $product_id)
                                ->where("le_wh_id", "=", $wh_id);

          $old_soh_details = $update_quarantine->get(array("soh", "dit_qty", "dnd_qty", "quarantine_qty", "order_qty"))->all();
          $old_soh_details = json_decode(json_encode($old_soh_details), true);

          $old_soh = $old_soh_details[0]['soh'];
          $old_dit_qty = $old_soh_details[0]['dit_qty'];
          $old_dnd_qty = $old_soh_details[0]['dnd_qty'];
          $old_quarantine_qty = $old_soh_details[0]['quarantine_qty'];
          $old_Order_Qty      = $old_soh_details[0]['order_qty'];
          
          $deductQuarantineQTY = $deducted_dit + $deducted_dnd;
          $resulted_inv_quarantine_qty = $old_quarantine_qty - ($deducted_dit + $deducted_dnd);

          $oldvaluesArray = array("old_soh" => $old_soh, "old_dit_qty" => $old_dit_qty, "old_quarantine_qty" => $old_quarantine_qty);
          $resulted_dit_qty = $old_dit_qty+$dit_qty;

          $res_soh = $old_soh;
          if($excessQty > 0)                     
          {
            $update_quarantine->increment('soh', $excessQty);
            $res_soh =  $res_soh + $excessQty;
            $newvalues = array("new_soh_excess_qty" => $res_soh." (when excess Qty exists)") ;
          }

          $res_soh = $res_soh - $quarantine_qty;
        $newvalues = array("new_soh" => $res_soh, "new_dit_qty" => $old_dit_qty+$dit_qty, "new_quarantine_qty" => $resulted_inv_quarantine_qty);

          $update_quarantine_inv = DB::table('inventory')
                                ->where("product_id", "=", $product_id)
                                ->where("le_wh_id", "=", $wh_id);

          $update_quarantine_inv->increment("dit_qty", $dit_qty); //incrementing the dit qty
          // $update_quarantine_inv->increment("dnd_qty", $missing_qty); // as per new requirement no need to add dnd/missing qty
          $update_quarantine_inv->decrement('soh',$quarantine_qty); //decrementing the SOH value
          $update_quarantine_inv->decrement('quarantine_qty', $deductQuarantineQTY); //Dedcuting the quarantine qty

          // $invLogs[] = array(
          //                               'le_wh_id'=>$wh_id,
          //                               'product_id'=>$product_id,
          //                               'soh'=>$res_soh,
          //                               'order_qty'=>0,
          //                               'ref'=>'Inventory Audit',
          //                               'ref_type'=>'',
          //                               'quarantine_qty'=>$resulted_dit_qty,
          //                               'dit_qty'=>$dit_qty,
          //                               'dnd_qty'=>0,
          //                               'comments'=>''
          //                       );



          $invLogs[]      = array('le_wh_id'=>$wh_id,
                                        'old_soh'=>$old_soh,
                                        'old_order_qty'=>$old_soh_details[0]['order_qty'],
                                        'old_quarantine_qty'=>$old_quarantine_qty,
                                        'old_dnd_qty'=>$old_dnd_qty,
                                        'old_dit_qty'=>$old_dit_qty,
                                        'product_id'=>$product_id,
                                        'soh' => ($excessQty - $quarantine_qty),
                                        'quarantine_qty'=>"-".$deductQuarantineQTY,
                                        'dit_qty'=>$dit_qty,
                                        'dnd_qty'=>$missing_qty,
                                        'order_qty'=>0,
                                        'ref_type'=>8,
                                        'comments'=>''
                                        );
          $obj->addInQueueWithBulk($invLogs);
          
          $uniquevalues = array("product_id" => $product_id, "warehouse_id" => $wh_id, "ticket_id" => $ticlet_id);
          UserActivity::userActivityLog("Inventory Audit", $newvalues, "Deducting SOH Qty from inventory table for product_id : ".$product_id." and warehouse_id : ".$wh_id." ", $oldvaluesArray, $uniquevalues);  //logs
          return $update_quarantine_inv;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }


  public function pendingReturns($productId, $wh_id) 
  {
    $re_query = DB::table("gds_returns")
                ->select([DB::raw("SUM(qty) as re_qty")])
                ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_returns.gds_order_id")
                ->where("approval_status", "!=", 1)
                ->where("approval_status", "!=", 0)
                ->where("approval_status", "!=", NULL)
                ->where("gds_orders.le_wh_id", "=", $wh_id)
                ->where("product_id", $productId)
                ->get()->all();

    if(isset($re_query[0])){
        $re_query_en = json_decode(json_encode($re_query[0]), true);
    }
    else{
        $re_query_en = json_decode(json_encode(reset($re_query)), true);
    }

    if($re_query_en["re_qty"]){
        $final_res = $re_query_en["re_qty"];
    } else {
        $final_res = 0;
    }
    return $final_res;
  }


  public function getAllItemsApprovedCount($ticket_id)
  {
    try {
      
          $fields = array("appr_good_qty", "appr_damage_qty", "appr_expire_qty", "good_qty", "damage_qty", "expire_qty");
          $sql = DB::table("inventory_audit")->where("unique_audit_id", $ticket_id)->get($fields)->all();

          $data = json_decode(json_encode($sql), true);
          
          $update_counter = 0; $checkUserEntry = 0;
          
          $checkTicketCount = count($data);
          foreach ($data as $key => $value) {
              
            $appr_gud_qty = strlen($value['appr_good_qty']);
            
            $appr_dam_qty = strlen($value['appr_damage_qty']);
            $appr_exp_qty = strlen($value['appr_expire_qty']);

            $good_qty_len = strlen($value['good_qty']);
            $damage_qty_len = strlen($value['damage_qty']);
            $expire_qty_len = strlen($value['expire_qty']);

            // echo 'appr gud'.$appr_gud_qty." appr dam ".$appr_dam_qty." appr Exp ".$appr_exp_qty;die;
            
            if($good_qty_len != 0){
              if($appr_gud_qty == 0 || $appr_dam_qty == 0 || $appr_exp_qty == 0)
                $update_counter++;
            }else{
              $checkUserEntry++;
            }
            
            
          }
          // echo "chk user entry".$checkTicketCount;
          if( $checkTicketCount == $checkUserEntry)
            return 1;
          elseif($update_counter>0){
            return 1;
          }
          else
            return 0;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function getAllClosedTicketsBetweenDates($from_Date, $to_Date)
  {
    try {
            $to_Date = $to_Date." 23:59:59";
            $sql = DB::table("inventory_audit_bulkupload")
                  ->whereBetween("created_at", array($from_Date, $to_Date))
                  ->where("approval_status", 1)
                  ->orderBy("created_at", "desc")
                  ->pluck("bulk_audit_id")->all();
            $data = json_decode(json_encode($sql), true);
            return json_encode($data);
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

  public function allOpenTkts($page, $pageSize)
  {
    try {
        DB::enableQueryLog();
        $data = array();
        $sql  = DB::table("inventory_audit_bulkupload")->where("approval_status", "!=", 1)->where("approval_status", "!=", 0);
        $data['count'] = $sql->count();
        
        $sql = $sql->skip($page * $pageSize)->take($pageSize);
        
        $res = json_decode(json_encode($sql->get(array(DB::raw("GetUserName(created_by, 2) AS username"), "audit_code", "created_at", "bulk_audit_id"))->all(), true));
        
        $data['result'] = $res;

        return $data;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }


  public function getTicketInfo($tktid)
  {
    try {
          $getTktstatus = DB::table("inventory_audit_bulkupload")->where("bulk_audit_id", $tktid)->pluck("approval_status")->all();
          $tktID = $getTktstatus[0];
          if($tktID == 1)
          {
            return "completed";
          }

          $sql = DB::table("inventory_audit")
                ->join("inventory_audit_bulkupload", "inventory_audit_bulkupload.bulk_audit_id", "=", "inventory_audit.unique_audit_id")
                ->where("unique_audit_id", $tktid)
                ->get(array(DB::raw("GetUserName(assigned_by, 2) AS username"), "product_title", "product_id", "location_code", "inventory_audit.created_at", "audit_code"))->all();
          $sql = json_decode(json_encode($sql), true);
          return $sql;
    } catch (\ErrorException $ex) {
      Log::error($ex->getMessage());
    }
  }

}