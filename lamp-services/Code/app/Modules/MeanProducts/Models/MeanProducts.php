<?php

namespace App\Modules\MeanProducts\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Notifications;
use UserActivity;
use Utility;
use App\Modules\Inventory\Models\Inventory;

class MeanProducts extends Model {

    public function getAllWareHouses() {
        $warehouses_table = DB::table('legalentity_warehouses');
        if (Session('roleId') != '1') {
            $warehouses_table = $warehouses_table->where('legal_entity_id', Session::get('legal_entity_id'));
        }

        $warehouses = $warehouses_table->where('lp_wh_name', '!=', NULL)->where('lp_wh_name', '!=', '')->where('dc_type', '=', '118001')->orderBy('lp_wh_name', 'asc')->pluck('lp_wh_name', 'le_wh_id')->all();
        return $warehouses;
    }

    public function gridDetails($filters) {
        $Inventory = new Inventory();
        $onlyProductIDs = array();
        $product_details = array();
        $dcid = $filters['dc_name'];
        $fromDate = date('Y-m-d 00:00:00', strtotime($filters['from_date']));
        $toDate = date('Y-m-d 23:59:59', strtotime($filters['todate']));
        $dateDiff = date_diff(date_create($fromDate), date_create($toDate));
        $dateDifference = $dateDiff->format("%R%a")+1;
        $numberDays = $filters['num_days'];
        $getallproductids = DB::table("vw_inventory_report")
                ->where("le_wh_id", $dcid)
                ->get(array("product_id", "manufacturer_name", "brand_name", "category_name", "soh", "product_title", "available_inventory", "cfc_qty", "sku", "mrp", "cp_enabled", "product_group_id"))->all();
        $getallproductids = json_decode(json_encode($getallproductids), true);

        $finalArr = array();

        foreach ($getallproductids as $key => $value) {
            $onlyProductIDs[] = $value['product_id'];
            $product_details[$value['product_id']] = array($value['manufacturer_name'], $value['brand_name'], $value['category_name'], $value['soh'], $value['product_title'], $value['available_inventory'], $value['cfc_qty'], $value['sku'], $value["mrp"], (($value["cp_enabled"]==1)?"Y":"N"), $value['product_group_id']);
        }

        $sql = "gds_order_products";

        $requiredinfo = DB::table("gds_order_products")
                ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_order_products.gds_order_id")
                ->whereIn("product_id", $onlyProductIDs)
                ->whereBetween('gds_order_products.created_at', array($fromDate, $toDate))
                ->where("gds_orders.le_wh_id", $dcid)
                ->groupBy('product_id');

        $final_result = array();
        $requiredinfo = $requiredinfo->select(DB::raw('sum(qty) as prod_qty, product_id'))
                ->get()->all();
        $final_result['results'] = json_decode(json_encode($requiredinfo), true);
        $i = 0;
        foreach ($final_result['results'] as $key => $value) {
            $date = ''; $podate = ''; $poCode = ''; $poQty = '';
            $poMRP = ''; $lastSupplier = ''; $lastSupType = ''; $lastCFCMrp = '';

            $poDetails = $this->getPODetails($value['product_id'], $dcid, $fromDate, $toDate);
            
            $supplier_id = "";
            if(!empty($poDetails)){
                if(isset($poDetails[0]) && !empty($poDetails[0])){
                    $date = (isset($poDetails[0]['po_date'])?$poDetails[0]['po_date']:"");
                    $podate = date("Y-m-d", strtotime($date));
                    if($podate == '1970-01-01')
                    {
                        $podate = "";
                    }
                    $poCode = $poDetails[0]['po_code'];
                    $poQty = $poDetails[0]['qty']." ".$poDetails[0]['packType'];
                    $poMRP = $poDetails[0]['mrp'];

                    //Last CFC MRP
                    //lastTotEaches = 12
                    //lastTotMRP = 720
                    //lastTotCFC = 
                    /*$lastTotEaches = $poDetails[0]['no_of_eaches']*$poDetails[0]['qty'];
                    $lastTotMRP = ($poDetails[0]['mrp']!=0)?$lastTotEaches*$poDetails[0]['mrp']:0;
                    $lastTotCFC = ($product_details[$value['product_id']][6]!=0)?$lastTotEaches/$product_details[$value['product_id']][6]:0;
                    $lastCFCMrp = ($lastTotCFC!=0)?$lastTotMRP/$lastTotCFC:0;*/
                    //Get Supplier info

                    if(isset($poDetails[0]['legal_entity_id']) && !empty($poDetails[0]['legal_entity_id'])){
                        $supplier = $this->getSupplierDetails($poDetails[0]['legal_entity_id']);
                        if(!empty($supplier)){
                            $supplier_id = $poDetails[0]['legal_entity_id'];
                            $lastSupplier = $supplier[0]['business_legal_name'];
                            $lastSupType = $supplier[0]['supplierType'];
                        }
                    }
                }
            }

            $poPriceHistory = $this->getPriceHistory($value['product_id'], $dcid, $fromDate, $toDate);

            $minCFCElp = ''; $lastCFCElp = "";
            if(!empty($poPriceHistory)){
                $mrpArr = array_column($poPriceHistory, 'elp');
                $minElp = min($mrpArr);
                       
                //CFC min ELP 
                $minCFCElp = $product_details[$value['product_id']][6]*$minElp;
            }
            if(!empty($supplier_id)){
                $lastPoPriceHistory = $this->getPriceHistory($value['product_id'], $dcid, $fromDate, $toDate, $supplier_id);
                if(!empty($lastPoPriceHistory)){
                    if(isset($lastPoPriceHistory[0]) && !empty($lastPoPriceHistory[0])){
                        $lastCFCElp = $product_details[$value['product_id']][6]*$lastPoPriceHistory[0]['elp'];
                    }
                }

            }
            $final = array();
            $final['Product ID'] = $value['product_id'];
            $final['Manufacturer'] = $product_details[$value['product_id']][0];
            $final['Product Title'] = $product_details[$value['product_id']][4];
            $final['SKU'] = $product_details[$value['product_id']][7];
            $final['CFC Qty'] = $product_details[$value['product_id']][6];
            $final['Product Group Id'] = $product_details[$value['product_id']][10];
            $grpId = $this->getProductGroupId($value['product_id']);
            $latestmrp = 0;
            
            if(!empty($grpId[0]['product_group_id'])){ 
                $latestmrp = $this->getLatestMRP($grpId[0]['product_group_id'], $dcid);
                if(!empty($latestmrp)){
                    $latestmrp = round($latestmrp[0]['mrp'], 4);
                }
                else{
                    $latestmrp = 0;
                }
            }
            elseif(!empty($product_details[$value['product_id']][8])){
                $latestmrp = round($product_details[$value['product_id']][8], 4);
            }
            
            $final['Latest MRP'] = $latestmrp;
            $final['CP Enabled'] = $product_details[$value['product_id']][9];
            $final['PO Code'] = $poCode;
            $final['PO Date'] = $podate;
            $final['PO Qty'] = $poQty;
            $final['AVG Day Sales'] = ($dateDifference>0)?(round(($value['prod_qty'] / $dateDifference), 4)):"";
            $cfcToBuy = 0; $availableCFC = 0;

            $openToBuy = "";
            if($dateDifference>0){
                $openToBuy = (($value['prod_qty'] / $dateDifference) * ($numberDays)) - ($product_details[$value['product_id']][5]);
            }
            
            if ($product_details[$value['product_id']][6] != 0) {
                $cfcToBuy = ($openToBuy/ ($product_details[$value['product_id']][6]));
                $availableCFC = ($product_details[$value['product_id']][5])/($product_details[$value['product_id']][6]);
            }

            $final['Available CFC'] = round($availableCFC, 4);
            if ($product_details[$value['product_id']][6] != 0) {
                $retrnPendingReturn = round($this->pendingReturns($value['product_id'], $dcid)/$product_details[$value['product_id']][6], 4);
            } else {
                $retrnPendingReturn = 0;
            }
            $final['Return Pending Qty'] = $retrnPendingReturn;
            $final['Open to buy CFC'] = round(($cfcToBuy - $retrnPendingReturn), 4);
            $final['CFC To Buy'] = '';
            $final['Min CFC Rate'] = round($minCFCElp, 4);
            $final['Last Bought CFC Rate'] = round($lastCFCElp, 4);
            $final['TotalAmount'] = '';
            $final['Supplier'] = $lastSupplier;
            $final['WD-SWD'] = $lastSupType;
            $finalArr['results'][] = $final;
        }
        return $finalArr;
    }

    public function getPODetails($productId, $wh_id, $fromDate, $toDate)
    {
        $sql = DB::table("po_products")
                ->select("po_code", "po_products.po_id", "po.po_date", "po_products.qty", "po_products.no_of_eaches", "mrp", DB::raw("getMastLookupValue(po_products.uom) as packType"), 'legal_entity_id')
                ->join("po", "po.po_id", "=","po_products.po_id")
                ->where("po_products.product_id", "=", $productId)
                ->where("po.le_wh_id", "=", $wh_id)
                ->whereBetween("po_products.created_at", array($fromDate, $toDate)) 
                ->orderBy(DB::raw("DATE_FORMAT(po.po_date, '%Y-%m-%d %H:%i:%s')"), "desc")
                //->orderBy("po_products.po_product_id", "desc")
                ->get()->all();
                //->limit(1)
                //->get(array("po_code", "po.po_date", "po_products.qty", DB::raw("getMastLookupValue(po_products.uom) as packType")));
        $returnval = json_decode(json_encode($sql), true);
        return (!empty($returnval)?$returnval:"");
    }

    public function getSupplierDetails($le_id){
        $sql = DB::table("legal_entities")
            ->select("legal_entities.business_legal_name", DB::raw("getMastLookupValue(suppliers.supplier_type) as supplierType"))
            ->join("suppliers", "suppliers.legal_entity_id", "=", "legal_entities.legal_entity_id")
            ->where("legal_entities.legal_entity_id", "=", $le_id)
            ->limit(1)
            ->get()->all();

        $res = json_decode(json_encode($sql), true);

        return $res;
    }

    public function getPriceHistory($productId, $dcid, $fromDate, $toDate, $supplier_id=''){
        $sql = DB::table("purchase_price_history")
            ->select("product_id", "supplier_id", "le_wh_id", "elp", "effective_date")
            ->where("product_id", "=", $productId)
            ->where("le_wh_id", "=", $dcid)
            ->where("elp", ">", 0)
            ->whereBetween("effective_date", array($fromDate, $toDate));

            if(!empty($supplier_id)){
                $sql = $sql->where("supplier_id", "=", $supplier_id);
            }
            $sql = $sql->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s')"), "desc")
            ->get()->all();
        $res = json_decode(json_encode($sql), true);

        return $res;
    }

    public function getProductGroupId($productId){
        $sql = DB::table('products')
            ->select('product_group_id')
            ->where('product_id', "=", $productId)
            ->limit(1)
            ->get()->all();
        $res = json_decode(json_encode($sql), true);

        return $res; 
    }

    public function getLatestMRP($grpID, $dcid){
        $sql = DB::table('products')
            ->select("products.product_id", "products.mrp", "po.po_id", "po.po_date")
            ->join("po_products", "products.product_id", "=", "po_products.product_id")
            ->join("po", "po.po_id", "=", "po_products.po_id")
            ->where("products.product_group_id", "=", $grpID)
            ->where("po.le_wh_id", "=", $dcid)
            ->orderBy(DB::raw("DATE_FORMAT(po.po_date, '%Y-%m-%d %H:%i:%s')"), "desc")
            ->limit(1)
            ->get()->all();

        $res = json_decode(json_encode($sql), true);

        return $res;
    }
    
    public function pendingReturns($productId, $wh_id) {
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
    public function dmsMailSetup() {
        $triggerQuery = json_decode(json_encode(DB::table("dms_report_trigger")->get()->all()), true);
        foreach($triggerQuery as $eachRes){
            $parameters["dc_name"] = $eachRes["le_wh_id"];
            $parameters["from_date"] = date('Y-m-d 00:00:00', strtotime("-".$eachRes["period_days"]."days"));
            $parameters["todate"] = date('Y-m-d 23:59:59', strtotime("-1 days"));
            $parameters["num_days"] = $eachRes["inventory_days"];
            $girdValues = $this->gridDetails($parameters);
            if($girdValues){
                return $this->dmsMailProd($girdValues["results"]);
            }
        }
    }

    public function dmsMailProd($girdData) {
        $emailProArr = $srmWiseData = array();
        $emailQuery = DB::table("product_tot as tot")
                    ->join("legal_entities as le", function($lejoin){
                        $lejoin->on("le.legal_entity_id", "=", "tot.supplier_id")
                        ->where("le.legal_entity_type_id", "=", 1002);
                    })
                    ->join("users as u", "u.user_id", "=", "le.rel_manager")
                    ->select("tot.product_id", "tot.supplier_id", "le.rel_manager", "u.email_id")
                    ->get()->all();
        $emailData = json_decode(json_encode($emailQuery), true);
        foreach($emailData as $eachEmailData){
            $emailProArr[$eachEmailData["email_id"]][] = $eachEmailData["product_id"];
        }
        $emailProArrUnique = array_map(function ($eachArray){ return array_unique($eachArray); }, $emailProArr);
        foreach($girdData as $value){
            foreach($emailProArrUnique as $email => $srmProducts){
                if(in_array($value['Product ID'], $srmProducts)){
                    $srmWiseData[$email][] = $value;
                }
            }
        }
        return $srmWiseData;
    }
    
    public function dmsSetupTable() {
        return json_decode(json_encode(DB::table("dms_report_trigger")->get()->all()), true);
    }
    
    public function dmsEmailSetupInsert($insertData) {
        $isExistQuery = $this->dmsSetupTable();
        if(count($isExistQuery) === 0) {
            $dmsInsertQuery = DB::table("dms_report_trigger")
                              ->insertGetId(array("period_days" => $insertData["period_days"], "inventory_days" => $insertData["inventory_days"],
                            "le_wh_id" => $insertData["dc_id"], "created_by" => Session::get('userId')));
            if($dmsInsertQuery) {
                $insertData["call_message"] = "success";
                $oldvalues = "";
                $uniqueId = $dmsInsertQuery;
                $newvalues = $insertData;
                UserActivity::userActivityLog("DMSMailSetup", $newvalues, "DMS report email settings insert", $oldvalues, $uniqueId);
            } else {
                $insertData["call_message"] = "error";
            }
        } else {
            $dmsUpdatQuery = DB::table("dms_report_trigger")
                             ->update(array("period_days" => $insertData["period_days"], "inventory_days" => $insertData["inventory_days"],
                            "le_wh_id" => $insertData["dc_id"], "updated_by" => Session::get('userId')));
            if($dmsUpdatQuery) {
                $insertData["call_message"] = "success";
                $oldvalues = array("le_wh_id" => $isExistQuery[0]["le_wh_id"], "period_days" => $isExistQuery[0]["period_days"], "inventory_days" => $isExistQuery[0]["inventory_days"]);
                $uniqueId = $isExistQuery[0]["dms_trgger_id"];
                $newvalues = $insertData;
                UserActivity::userActivityLog("DMSMailSetup", $newvalues, "DMS report email settings update", $oldvalues, $uniqueId);
            } else {
                $insertData["call_message"] = "error";
            }
        }
        return $insertData;
    }
}