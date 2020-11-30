<?php

namespace App\Modules\Tax\Models;
date_default_timezone_set("Asia/Kolkata");

/*
  Filename : Counter.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 05-July-2016
  Desc : Model for tax classes mongo table, to store tax class names
 */

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Session;
use App\Modules\Tax\Models\MasterLookUp;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use UserActivity;

class ClassTaxMap extends Model {

    protected $table = 'tax_class_product_map';
    protected $primaryKey = 'map_id';

    public function taxClasses() {
        return $this->hasOne('App\Modules\Tax\Models\TaxClass', 'tax_class_id', 'tax_class_id');
    }

    public function displayTaxMap($product_id) {
        $final_result = array();
        $result = $this->join('tax_classes', 'tax_classes.tax_class_id', '=', 'tax_class_product_map.tax_class_id')
                        ->where('tax_classes.status', '=', 'Active')
                        ->where("tax_class_product_map.status", "=", 1)
                        ->where('tax_class_product_map.product_id', $product_id)->get()->all();
        $result = json_decode(json_encode($result), true);

        $masterlookup = new MasterLookUp();
        $alltaxes = $masterlookup->getTaxTypes();
        $taxTypes = array();
        foreach ($alltaxes as $value) {
            $taxTypes[] = trim($value['master_lookup_name']);
        }
        ksort($taxTypes);
        foreach ($result as $res) {
            if (in_array($res['tax_class_type'], $taxTypes)) {
                $new_res["text"] = $res['tax_class_code'];
                $new_res["value"] = $res['tax_class_code'];
                $final_result[$res['state_id']][] = $new_res;
            }
        }
        return $final_result;
    }

    public function displayAllTaxMap($product_id, $taxType) {
        $final_result = array();
        $result = json_decode(json_encode($this->join('tax_classes', 'tax_classes.tax_class_id', '=', 'tax_class_product_map.tax_class_id')
                                ->where('tax_classes.status', '=', 'Active')
                                ->where('tax_classes.tax_class_type', '=', $taxType)
                                ->where('tax_class_product_map.product_id', $product_id)->get()->all()), true);
        foreach ($result as $res) {
            $final_result[$res['state_id']][] = $res['tax_class_code'];
        }
        return $final_result;
    }

    public function taxMapProductWise($product_id) {
        $final_result = array();
        $result = json_decode(json_encode($this->join('tax_classes', 'tax_classes.tax_class_id', '=', 'tax_class_product_map.tax_class_id')
                                ->where('tax_classes.status', '=', 'Active')
                                ->where("tax_class_product_map.status", "=", 1)
                                ->where('tax_class_product_map.product_id', $product_id)->get()->all()), true);
        /* foreach ($result as $res) {
          $final_result[$res['state_id']][] = $res['tax_class_code'];
          } */
        $final_result[] = $result;
        return $result;
    }

    public function checkTaxClassMap($productId, $taxClassId) {
        return $this->join('tax_classes', 'tax_classes.tax_class_id', '=', 'tax_class_product_map.tax_class_id')
                        ->where('tax_class_product_map.product_id', $productId)->where('tax_classes.tax_class_id', $taxClassId)->pluck('map_id')->all();
    }

    public function insertTaxClassMap($productId, $taxClassId, $effectiveDate, $hsnCode='',$parentId) {
        try{
            // $userId = json_decode(json_encode(DB::table("users")->where("email_id", "taxassociate@noemail.com")->pluck("user_id")->all()), true)[0];
            
            $userId = \Session::get('userId');
            $this->product_id = $productId;
            $this->tax_class_id = $taxClassId;
            $this->hsn_code = $hsnCode;
            $this->date_start = $effectiveDate;
            $this->created_by = $userId;
            $this->parent_id = $parentId;
            $this->date_start = date('Y-m-d');
            $this->save();
            $count="select count(*) as count from appr_workflow_history where awf_for_id=".$parentId." and awf_for_type='TAX'";
            $count=DB::select(DB::raw($count));
            $count=json_decode(json_encode($count[0]),true);
              
            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('TAX', 'drafted', $userId);
        
            if(isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])){
                $current_status_id = $res_approval_flow_func["currentStatusId"];
                $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                $this->updateStatusAWF($this->map_id, $next_status_id.",0");
                if($count['count']==0){
                    $approval_flow_func->storeWorkFlowHistory('TAX', $parentId, $current_status_id, $next_status_id, 'System approval at the time of insertion', $userId);
                }
            }
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        
    }

    public function checkHSNCodeForProduct($prod_id)
    {
        $sql = $this->where("product_id", $prod_id)->where("hsn_code", "!=", "")->pluck("hsn_code")->all();
        return $sql;
    }


    public function updateStatusAWF($taxMapId, $newStatus, $optional = ""){
        try{
            $checkval = "false";
            $update_result = $this->where('map_id', $taxMapId)->first();
            $product_id = $update_result->product_id;

            $tax_Class_details = DB::table("tax_class_product_map as TCPM")
                        ->join("tax_classes as TC", "TC.tax_class_id", "=", "TCPM.tax_class_id")
                        ->join("master_lookup as ML", "ML.master_lookup_name", "=", "TC.tax_class_type");
                        

            $fullTAx_details = $tax_Class_details->select('TCPM.date_start','TC.tax_class_type','ML.parent_lookup_id','TC.state_id')->where("TCPM.map_id", $taxMapId)->first();
            $tax_effective_date = $fullTAx_details->date_start;
            $parent_lookup_id = $fullTAx_details->parent_lookup_id;
            $tax_type = $fullTAx_details->tax_class_type;
            $state_id = $fullTAx_details->state_id;

            $newStatus_explode = explode(",", $newStatus);
            $newStatusFinal = $newStatus_explode[1] == 0 ? $newStatus_explode[0] : $newStatus_explode[1];
            
        //     select * from tax_class_product_map  as TCPM
        // join tax_classes as TC on TC.tax_class_id = TCPM.tax_class_id
        //     join master_lookup as ML on ML.master_lookup_name = TC.tax_class_type
        //     where TCPM.product_id = 277 and TC.state_id = 4033 and TC.tax_class_type = 'GST' and TC.date_start = '2017-06-27' and TCPM.`status` = 1
        //     and ML.parent_lookup_id  = 10001;
            
            if($parent_lookup_id != 10003){
                $sql_check = DB::table("tax_class_product_map as TCPM")
                        ->join("tax_classes as TC", "TC.tax_class_id", "=", "TCPM.tax_class_id")
                        ->join("master_lookup as ML", "ML.master_lookup_name", "=", "TC.tax_class_type")
                        ->where("TCPM.product_id", "=", $product_id)
                        ->where("TC.state_id", "=", $state_id)
                        ->where("TC.tax_class_type", "=", $tax_type)
                        ->where("TCPM.date_start", "=", $tax_effective_date)
                        ->where("TCPM.status", "=", 1)
                        ->where("ML.parent_lookup_id", "=", $parent_lookup_id)
                        ->count();
                  

                if($sql_check > 0 && $optional == 'approval'){
                    $checkval = "true";
                }else{
                        $user_activity_action = "Tax mapping id ".$taxMapId." status was updated to ".$newStatusFinal." from ".$update_result->status;
                        $user_activity_oldvalue = $update_result->status;
                        
                        $update_result->status = $newStatusFinal;
                        $update_result->approved_by = \Session::get('userId');
                        $update_result->approved_at = date('Y-m-d H:i:s');
                        $update_result->updated_by = \Session::get('userId');
                        $update_result->updated_at = date('Y-m-d H:i:s');
                        $update_result->save();
                        
                        UserActivity::userActivityLog('Tax Mapping', array("new_status" => $newStatusFinal), $user_activity_action, array("old_status" => $user_activity_oldvalue), array("mapping_id" => $taxMapId));
                }    
            }else{
                    $user_activity_action = "Tax mapping id ".$taxMapId." status was updated to ".$newStatusFinal." from ".$update_result->status;
                        $user_activity_oldvalue = $update_result->status;
                        
                        $update_result->status = $newStatusFinal;
                        $update_result->approved_by = \Session::get('userId');
                        $update_result->approved_at = date('Y-m-d H:i:s');
                        $update_result->updated_by = \Session::get('userId');
                        $update_result->updated_at = date('Y-m-d H:i:s');
                        $update_result->save();
                        
                        UserActivity::userActivityLog('Tax Mapping', array("new_status" => $newStatusFinal), $user_activity_action, array("old_status" => $user_activity_oldvalue), array("mapping_id" => $taxMapId));
            }
            
            if($checkval == "true")
            {
                return 'same-effective-date-exists';
            }else{
               return 'fine'; 
            }
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }



    public function checkStatus($taxMapId, $newStatus, $optional = ""){
        $checkval = "false"; 
        $update_result = $this->where('map_id', $taxMapId)->first();
        $product_id = $update_result->product_id;

        $tax_Class_details = DB::table("tax_class_product_map as TCPM")
                    ->join("tax_classes as TC", "TC.tax_class_id", "=", "TCPM.tax_class_id")
                    ->join("master_lookup as ML", "ML.master_lookup_name", "=", "TC.tax_class_type");
                    

        $fullTAx_details = $tax_Class_details->select('TCPM.date_start','TC.tax_class_type','ML.parent_lookup_id','TC.state_id')
        ->where("TCPM.map_id", $taxMapId)->first();
        $tax_effective_date = $fullTAx_details->date_start;
        $parent_lookup_id = $fullTAx_details->parent_lookup_id;
        $tax_type = $fullTAx_details->tax_class_type;
        $state_id = $fullTAx_details->state_id;

        $newStatus_explode = explode(",", $newStatus);
        $newStatusFinal = $newStatus_explode[1] == 0 ? $newStatus_explode[0] : $newStatus_explode[1];
        
    //     select * from tax_class_product_map  as TCPM
    // join tax_classes as TC on TC.tax_class_id = TCPM.tax_class_id
    //     join master_lookup as ML on ML.master_lookup_name = TC.tax_class_type
    //     where TCPM.product_id = 277 and TC.state_id = 4033 and TC.tax_class_type = 'GST' and TC.date_start = '2017-06-27' and TCPM.`status` = 1
    //     and ML.parent_lookup_id  = 10001;
        if($parent_lookup_id != 10003){
            $sql_check = DB::table("tax_class_product_map as TCPM")
                    ->join("tax_classes as TC", "TC.tax_class_id", "=", "TCPM.tax_class_id")
                    ->join("master_lookup as ML", "ML.master_lookup_name", "=", "TC.tax_class_type")
                    ->where("TCPM.product_id", "=", $product_id)
                    ->where("TC.state_id", "=", $state_id)
                    ->where("TC.tax_class_type", "=", $tax_type)
                    ->where("TCPM.date_start", "=", $tax_effective_date)
                    ->where("TCPM.status", "=", 1)
                    ->where("ML.parent_lookup_id", "=", $parent_lookup_id)
                    ->count();
                    
            if($sql_check > 0 && $optional == 'approval'){
                $checkval = "true";
            }else{
                    $user_activity_action = "Tax mapping id ".$taxMapId." status was updated to ".$newStatusFinal." from ".$update_result->status;
                    $user_activity_oldvalue = $update_result->status;
                    
                    $update_result->status = $newStatusFinal;
                    $update_result->approved_by = \Session::get('userId');
                    $update_result->approved_at = date('Y-m-d H:i:s');
                    $update_result->updated_by = \Session::get('userId');
                    $update_result->updated_at = date('Y-m-d H:i:s');
                    //$update_result->save();
                    
                    
            }    
        }else{
                $user_activity_action = "Tax mapping id ".$taxMapId." status was updated to ".$newStatusFinal." from ".$update_result->status;
                    $user_activity_oldvalue = $update_result->status;
                    
                    $update_result->status = $newStatusFinal;
                    $update_result->approved_by = \Session::get('userId');
                    $update_result->approved_at = date('Y-m-d H:i:s');
                    $update_result->updated_by = \Session::get('userId');
                    $update_result->updated_at = date('Y-m-d H:i:s');
                   // $update_result->save();
                    
                   
        }
        
        if($checkval == "true")
        {
            return 'same-effective-date-exists';
        }else{
           return 'fine'; 
        }
    }

    public function updateTaxClassMap($mapId, $taxClassId) {
        $update_result = $this->where('map_id', $mapId)->first();
        $update_result->tax_class_id = $taxClassId;
        $update_result->save();
    }

    public function deleteTaxClassMap($taxMapId,$col_name) {
        $selected_row = $this->where($col_name,$taxMapId);
        $selected_row->delete();
    }

    public function getAlltaxClassRulesbasedonProductID($productID, $stateID) {
        $query = $this->join('tax_classes', 'tax_classes.tax_class_id', '=', 'tax_class_product_map.tax_class_id')
                ->where('tax_class_product_map.product_id', '=', $productID)
                ->where('tax_classes.state_id', '=', $stateID)
                ->where('tax_classes.status', '=', 'Active')
                ->orderBy('tax_classes.tax_class_type', 'desc')
                ->get(array('tax_percentage', 'tax_class_product_map.status', 'map_id', 'tax_class_code', 'tax_classes.tax_class_type', 'tax_classes.date_start'))->all();
            return $query;
    }

    public function mappingStatusUpate($mapId) {
        $return_value = 0;
        $query = $this->where('map_id', $mapId)->update(['status' => 1]);

        if ($query) {
            $return_value = 1;
        }
        return $return_value;
    }

    public function checkDuplicate($stateId, $product_ID, $TaxType) {
        $count = $this->join('tax_classes', 'tax_classes.tax_class_id', '=', 'tax_class_product_map.tax_class_id')
                ->where('tax_class_product_map.product_id', '=', $product_ID)
                ->where('tax_classes.state_id', '=', $stateId)
                ->where('tax_classes.tax_class_code', '=', $TaxType)
                ->count();
        return $count;
    }

    public function getAvailableTaxesByProductId($productId, $stateid, $taxtype) {
        $avaliabletaxes = DB::table('tax_classes')
                ->where('state_id', '=', $stateid)
                ->where('tax_class_type', '=', $taxtype)
                ->where('status', '=', 'Active')
                ->pluck('tax_class_code', 'tax_classes.tax_class_id')->all();

        $data = $this->join('tax_classes', 'tax_classes.tax_class_id', '=', 'tax_class_product_map.tax_class_id')
                ->where('tax_class_product_map.product_id', '=', $productId)
                ->where('tax_classes.state_id', '=', $stateid)
                ->where('tax_classes.tax_class_type', '=', $taxtype)
                ->where('tax_classes.status', '=', 'Active')
                ->pluck('tax_class_code', 'tax_classes.tax_class_id')->all();

        $result = array_diff($avaliabletaxes, $data);
        return $result;
    }

    public function getAllTaxesForStateByProductId($productId, $stateId) {
        $getTaxclassCodes = DB::table('tax_classes')->where('state_id', '=', $stateId)->where('status', '=', 'Active')->pluck('tax_class_code', 'tax_class_id')->all();
        $getProductMappings = $this->join('tax_classes', 'tax_classes.tax_class_id', '=', 'tax_class_product_map.tax_class_id')
                ->where('tax_class_product_map.product_id', '=', $productId)
                ->where('tax_classes.state_id', '=', $stateId)
                ->orderBy('tax_classes.tax_class_type', 'desc')
                ->pluck('tax_class_code', 'tax_class_product_map.tax_class_id')->all();
        $data = array_diff($getTaxclassCodes, $getProductMappings);
        return $data;
    }

    public function taxCountBasedonTaxClassId($taxclassId) {
        $query = $this->where('tax_class_id', '=', $taxclassId)->count();
        return $query;
    }

    public function getAllExcelDataWithFilters($cats = "", $brands = "", $taxtypes = "", $legal_entity_id) {

        $info = array();
        $query2 = DB::table("zone")
                ->join("tax_classes", "tax_classes.state_id", "=", "zone.zone_id");

        if (!empty($taxtypes) && $taxtypes[0] != "Tax Type") {
            $query2 = $query2->where('tax_classes.tax_class_type', '=', $taxtypes);
        }

        $query2 = $query2->get(array("tax_class_id", "tax_class_code", "zone.code"))->all();

        foreach ($query2 as $value) {
            $val = json_decode(json_encode($value), true);
            $taxId = $val['tax_class_id'];
            unset($val['tax_class_id']);
            $info[$taxId] = $val;
        }

        /*SELECT categories.cat_name, brands.brand_name, products.product_title, products.sku,
(select tax_classes.tax_class_id from tax_classes 
where tax_classes.tax_class_id=cpm.tax_class_id limit 0,1) 
as 'tax_class_id'
FROM products
inner join categories on categories.category_id = products.category_id
inner join brands on brands.brand_id = products.brand_id
left join tax_class_product_map on products.product_id = tax_class_product_map.product_id*/

        $subSql = "(select tax_classes.tax_class_id, tax_class_product_map.product_id, tax_class_product_map.hsn_code from tax_classes 
                Inner join tax_class_product_map on tax_class_product_map.tax_class_id = tax_classes.tax_class_id
                where tax_classes.tax_class_id=tax_class_product_map.tax_class_id 
                AND tax_classes.status = 'Active' and  tax_class_product_map.status = 1 ";
        if (!empty($taxtypes) && $taxtypes[0] != "Tax Type") {
            $subSql .= "AND tax_classes.tax_class_type = '$taxtypes'";
        }
        $subSql .= ")";

        $sql = "SELECT categories.cat_name, brands.brand_name, products.product_title, products.product_id, products.sku, custom.tax_class_id, custom.hsn_code
                FROM products inner join categories on categories.category_id = products.category_id 
                inner join brands on brands.brand_id = products.brand_id 
                left join $subSql custom
                on custom.product_id = products.product_id ";

        $flag = 0;
        if (!empty($cats)) {
            if($flag == 1){
                $sql .= "AND products.category_id IN ($cats) ";
            }
            else{
                $sql .= "WHERE products.category_id IN ($cats) ";
            }
            $flag = 1;
        }

        if (!empty($brands)) {

            if($flag == 1){
                $sql .= "AND products.brand_id IN ($brands) ";
            }
            else{
                $sql .= "WHERE products.brand_id IN ($brands) ";
            }
            $flag = 1;
        }

        if ($legal_entity_id != 0) {
            if($flag == 1){
                $sql .= "AND products.legal_entity_id = $legal_entity_id ";
            }
            else{
                $sql .= "WHERE products.legal_entity_id = $legal_entity_id ";
            }
            $flag = 1;
        }

        $sql .= "ORDER BY brands.brand_name, products.product_title, categories.cat_name ";

        //echo $sql; die;
        $results = DB::select( DB::raw($sql) );

        
        /*$query = DB::table("products")
            ->join("categories", "categories.category_id", "=", "products.category_id")
            ->join("brands", "brands.brand_id", "=", "products.brand_id")
            ->join("tax_class_product_map", "products.product_id", "=", "tax_class_product_map.product_id")
            ->join("tax_classes", "tax_classes.tax_class_id", "=", "tax_class_product_map.tax_class_id")
            ->where("tax_classes.status", "=", "Active");

        if (!empty($taxtypes) && $taxtypes[0] != "Tax Type") {
            $query = $query->where('tax_classes.tax_class_type', '=', $taxtypes);
        }

        if (!empty($cats)) {
            $query = $query->whereIn('products.category_id', explode(',', $cats));
        }

        if (!empty($brands)) {
            $query = $query->whereIn('products.brand_id', explode(',', $brands));
        }

        if ($legal_entity_id != 0) {
            $query = $query->where('products.legal_entity_id', '=', $legal_entity_id);
        }
        $query = $query
                ->get(array('categories.cat_name', 'brands.brand_name', 'products.product_title', 'products.sku', 'tax_class_product_map.tax_class_id'));*/


        $taxinfo = json_decode(json_encode($results), true);
        
        //echo "<pre>"; print_r($taxinfo); die;
        $i = 0;
        
        foreach ($taxinfo as $value1) {
            // unset($taxinfo[$i]['tax_class_id']);

            if (array_key_exists($value1['tax_class_id'], $info)) {
                if($info[$value1['tax_class_id']]['code'] == "*")
                {

                    $info[$value1['tax_class_id']]['code'] = "*(ALL)";
                }
                $taxinfo[$i]['tax_class_code'] = $info[$value1['tax_class_id']]['tax_class_code'];
                $taxinfo[$i]['code'] = $info[$value1['tax_class_id']]['code'];

            } else {
                // unset($taxinfo[$i]);
                $taxinfo[$i]['tax_class_code'] = "";
                $taxinfo[$i]['code'] = "";
            }

            $i++;
        }
        return $taxinfo;
    }

    public function getApproveAllTaxes($productId, $stateId) {
        $timestamp = date('Y-m-d H:i:s');
        $approved_by = Session::get('userId');
        $query = DB::table('tax_classes')->where("state_id", "=", $stateId)->where("status", "=", "Active")->get(array('tax_class_id'))->all();

        $query1 = json_decode(json_encode($query), true);
        print_r($query1);
        for ($i = 0; $i < sizeof($query1); $i++) {
            $tax_classID = $query1[$i]['tax_class_id'];
            $updatetaxMapping = $this
                    ->where('tax_class_id', "=", $tax_classID)
                    ->where("product_id", "=", $productId)
                    ->where("status", "=", 0)
                    ->update(['status' => '1', 'approved_by' => $approved_by, 'approved_at' => $timestamp]);
        }
        $returnval = 1;
        return $returnval;
    }

    public function checkMappingExists($productId, $stateID) {
        $query = $this->
                join("tax_classes", "tax_classes.tax_class_id", "=", "tax_class_product_map.tax_class_id")
                ->join("master_lookup", "master_lookup.master_lookup_name", "=", "tax_classes.tax_class_type")
                ->where("product_id", "=", $productId)
                ->where("state_id", "=", $stateID)
                ->count();
        return $query;
    }

    public function getAllStatusesByProductId($productID, $stateID) {
        return $this->join("tax_classes", "tax_classes.tax_class_id", "=", "tax_class_product_map.tax_class_id")
                ->where("product_id", "=", $productID)
                ->where("state_id", "=", $stateID)
                ->where("tax_classes.status", "=", "Active")
                ->pluck('tax_class_product_map.status')->all();
    }

    public function getProductTaxDetails($mappingId)
    {
        $ptDetails["mappingDetails"] = json_decode(json_encode($this->where("map_id", $mappingId)->get(["product_id", "status", "hsn_code"])->all()), true);
        if(empty($ptDetails["mappingDetails"]))
        {
            //if no mapping details were found
            return "";
        }
        $productClass = new Product();
        $ptDetails["productDetails"] = json_decode(json_encode($productClass->getProductDetailsofTaxClassCode($ptDetails["mappingDetails"][0]['product_id'])), true);
        $ptDetails["taxDetails"] = json_decode(json_encode($this->setConnection('mysql-write')->join("tax_classes", "tax_classes.tax_class_id", "=", "tax_class_product_map.tax_class_id")
                                    ->where("map_id", $mappingId)->get(["tax_class_product_map.map_id", "tax_class_product_map.product_id", "tax_class_product_map.parent_id","tax_classes.tax_class_type", "tax_classes.tax_class_code", "tax_classes.tax_percentage", "tax_classes.state_id", "tax_class_product_map.date_start"])->all()), true);
        $zone_modle = new Zone();
        $state_name = json_decode(json_encode($zone_modle->getStateName($ptDetails["taxDetails"][0]["state_id"])), true)[0];
        $ptDetails["taxDetails"][0]["state_name"] = $state_name;
        
        $hsndata = json_decode(json_encode(DB::table("HSN_Master")->where("ITC_HSCodes", $ptDetails["mappingDetails"][0]["hsn_code"])->get()->all()), true);
        
        if(!empty($ptDetails["mappingDetails"][0]["hsn_code"]) && empty($hsndata))
        {
            $ptDetails["hsn_data"] = array(0 => ["ITC_HSCodes" => $ptDetails["mappingDetails"][0]["hsn_code"], "HSC_Desc" => "", "tax_percent" => ""]);
        } else {
            $ptDetails["hsn_data"] =  $hsndata;
        }
        return $ptDetails;
    }
    
    public function getActionByStatus($statusCode) {
        $approval_flow_func= new CommonApprovalFlowFunctionModel();
        return $approval_flow_func->getApprovalFlowDetails('TAX', $statusCode, Session::get('userId'));
    }
    
    public function statusCodeByRoleIdAEF() {
        return DB::table("appr_workflow_status_details")
                ->where("awf_id", 37)->where("applied_role_id", Session::get('roleId')-1)
                ->pluck("awf_status_to_go_id")->all();
    }
    
    public function allHsnCodes($type, $productId) {
        $hsnCodes = DB::table('HSN_Master')->where("is_active", "=", 1);
        
        if($productId != 'NA'){
            $hsnByProduct = DB::table('tax_class_product_map')->where('product_id', $productId)->where('hsn_code', '!=', '')->orderBy('date_start','desc')->pluck('hsn_code')->all();
            $finalHsnCode = array_unique($hsnByProduct);

            if(count($finalHsnCode) > 0){
                $hsnCodes = $hsnCodes->where('ITC_HSCodes', $finalHsnCode[0])->get(['ITC_HSCodes', 'HSC_Desc', 'tax_percent'])->all();

                if($hsnCodes){
                    $final['hsn_codes'] = $hsnCodes;
                } else {
                    $checkhsncode="select * from HSN_Master WHERE ITC_HSCodes=".$finalHsnCode[0];
                    $checkhsncode=DB::SELECT($checkhsncode);
                    $checkhsncode=isset($checkhsncode[0]->ITC_HSCodes)?$checkhsncode[0]->ITC_HSCodes:'';
                    $final['hsn_codes'] = array( 0 =>['ITC_HSCodes' => $checkhsncode, 'HSC_Desc' => '', 'tax_percent' => '']);
                }

                $final['exist'] = 'yes';
            } else if(count($finalHsnCode) === 0){
                $final['exist'] = 'no';
            }
        } else {
            if(isset($type) && $type != 'All'){
                $final['hsn_codes'] = $hsnCodes->where('ITC_HSCodes', $type)->get(['ITC_HSCodes', 'HSC_Desc', 'tax_percent'])->all();
            } else if(isset($type) && $type == 'All') {
                $final['hsn_codes'] = $hsnCodes->get(['ITC_HSCodes', 'HSC_Desc', 'tax_percent'])->all();
            }
        }

        return $final;
    }
     public function getProductIdBasedOnMappingId($mappingId)
    {
        
        $getProductID = $this->where("map_id", "=", $mappingId)->get(array("product_id"))->all();
        $productId = $getProductID;
        $productId = $productId[0]['product_id'];
        return $productId;
    }

    public function getProductInfo($productId)
    {
        $productClass = new Product();
        $productDetails = $productClass->getProductDetailsofTaxClassCode($productId);
        return $productDetails;
    }

    public function getTaxDetails($mappingId)
    {
        $sql = $this->join("tax_classes", "tax_classes.tax_class_id", "=", "tax_class_product_map.tax_class_id")->where("map_id", "=", $mappingId)->get()->all();
        return $sql;
    }


    public function getHsnInfo($term)
    {
        $hsnCodes = DB::table('HSN_Master')->where("is_active", "=", 1)->where('ITC_HSCodes', 'like', '%'.$term.'%')->pluck("ITC_HSCodes")->all();
        // echo "<pre>";print_r($hsnCodes);
        return $hsnCodes;
    }

    public function gethsnCodeByProdId($prod_id)
    {
        $hsn_code = "";
        $sql = DB::table("tax_class_product_map")->where("product_id", $prod_id)->get(array('hsn_code'))->all();

        $data = json_decode(json_encode($sql), true);
        foreach ($data as $hsn) {
            if($hsn != "")
            {
                $hsn_code = $hsn;
            }
        }
        return $hsn_code;
    }
    
    public function checkTaxClassByDate($productId, $effectiveDate, $stateId, $taxclassId) {
        $taxcls = DB::table('tax_classes')->where('tax_class_id',$taxclassId)->get()->all();
        return $this->join("tax_classes", "tax_classes.tax_class_id", "=", "tax_class_product_map.tax_class_id")
                ->where("tax_class_product_map.product_id", $productId)->where("tax_class_product_map.date_start", $effectiveDate)
                ->where("tax_classes.state_id", $stateId)
                ->where("tax_classes.tax_class_type",$taxcls[0]->tax_class_type)->pluck('map_id')->all();
    }
    
    public function checkPendingForApprovals($productId) {
        return $this->where('product_id', $productId)->whereNotIn('status', [1,57047])->pluck('map_id')->all();
    }
    public function getMapIdForParentID($id){
        $result="select map_id from tax_class_product_map WHERE parent_id=".$id;
        $result=DB::connection('mysql-write')->select(DB::raw($result));
        $result=json_decode(json_encode($result),true);
        return $result;

    }

}