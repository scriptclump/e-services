<?php

namespace App\Modules\DiMiCiReport\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\DiMiCiReport\Models\InventorySnapshot;
use Log;
class DimiciReport extends Model {
    
    public function generateReport($start, $end, $dc) {
//        Log::info("Come to generateReport");

        if(empty($end)){
            $end = date('Y-m-d');
        }
        if(empty($start)){
            $start = date('Y-m-d', strtotime($end. " -15 days"));
        }
        
        $start = $start_str = date('Y-m-d', strtotime($start));
        $end = $end_str = date('Y-m-d', strtotime($end));
        
        $diff = date_diff(date_create($start),date_create($end));
        $daysDiff =  $diff->format("%a") + 1;
        
        $dateRange = $this->dateFunct($start, $end);
        $daysCount = $daysDiff-count($dateRange);
        
        $start = date('Y-m-d 00:00:00', strtotime($start));
        $end = date('Y-m-d 23:59:59', strtotime($end));
        
        $productIdsByGroup = $selectedProductByGroup = $final_result = array();
        
        //Distinct product_group_id 's
        $productGroupIds = $this->productGroups($dc);
        
        $this->_inventorySnapshot = new InventorySnapshot();
        $noStockDays = $this->_inventorySnapshot->noStockDays($productGroupIds, $dc, $start, $end);
        
        //Array, product_group_id as key and product_id 's as element
        foreach($productGroupIds as $eachGroupId){
            $productIdsByGroup[$eachGroupId] = $this->proIdByProGroup($eachGroupId);
        }
        
        //Last po product_id w.r.t product_group_id
        foreach($productIdsByGroup as $groupId => $productIds){
            if(count($productIds) > 1){
                $selectedProductByGroup[$groupId] = $this->lastPoProduct($groupId, $dc);
            } else {
                $selectedProductByGroup[$groupId] = $productIds;
            }
        }
        
        //Product details
        foreach($selectedProductByGroup as $groupId => $productId){
            if(empty($productId))
                continue;
            $final_result[$groupId] = $this->detailsByProductId($productId[0], $dc);
        }
        
        //Other columns calculations
        $total_tlc = $total_earnings_sum = 0;
        $temp_esp = $temp_elp = $temp_ptr = "";
        foreach($final_result as $key => $value){
            if($final_result[$key]["CP Enabled"] == 1){
                $final_result[$key]["CP Enabled"] = "Yes";
            } else {
                $final_result[$key]["CP Enabled"] = "No";
            }
            
            $temp_esp = $final_result[$key]["ESP"];
            unset($final_result[$key]["ESP"]);
            
            $temp_elp = $final_result[$key]["ELP"];
            unset($final_result[$key]["ELP"]);
            
            $temp_ptr = $final_result[$key]["PTR"];
            unset($final_result[$key]["PTR"]);
            
            // $final_result[$key]["CFC Qty"] = $this->cfcQtyByProductId($final_result[$key]["Product Id"], $dc);
            $final_result[$key]["CFC Qty"] = $this->cfcQtyByProductIdNew($final_result[$key]["Product Id"]);

            
            $final_result[$key]["ESP"] = $temp_esp;
            
            $final_result[$key]["ELP"] = $temp_elp;
            
            $final_result[$key]["PTR"] = $temp_ptr;
            
            if(isset($value["ESP"]) && isset($value["ELP"]) && $value["ELP"] > 0){
                $final_result[$key]["Ebutor Margin %"] = (float) number_format((($value["ESP"] - $value["ELP"]) / $value["ELP"]) * 100, 2, '.', '');
            } else {
                $final_result[$key]["Ebutor Margin %"] = 0.00;
            }
            
            $final_result[$key]["TLC"] = $temp_tlc = $this->tlcCountByProductGroup($productIdsByGroup[$key], $start, $end, $dc);
            
            $total_tlc += $temp_tlc;
            
            $final_result[$key]["Net Sold Qty"] = $this->netSoldQty($productIdsByGroup[$key], $start, $end, $dc);
            
            if($final_result[$key]["Net Sold Qty"] !== 0){
//                $final_result[$key]["Avg Day Sales (Eaches)"] = (float) number_format($final_result[$key]["Net Sold Qty"] / $daysCount, 5, '.', '');
//                $avg_day_sales_eaches = $final_result[$key]["Net Sold Qty"] / $daysCount;
                if(isset($noStockDays[$key]) && $noStockDays[$key] !== 0){
                    $avg_day_sales_eaches = $final_result[$key]["Net Sold Qty"] / $noStockDays[$key];
                } else {
                    $avg_day_sales_eaches = 0.0000;
                }
            } else {
//                $final_result[$key]["Avg Day Sales (Eaches)"] = 0.0000;
                $avg_day_sales_eaches = 0.0000;
            }
            
//            if(isset($final_result[$key]["Avg Day Sales (Eaches)"]) && isset($final_result[$key]["CFC Qty"]) && $final_result[$key]["CFC Qty"] > 0){
//                $final_result[$key]["Avg Day Sales (CFC)"] = (float) number_format($final_result[$key]["Avg Day Sales (Eaches)"] / $final_result[$key]["CFC Qty"], 5, '.', '');
            if(isset($avg_day_sales_eaches) && isset($final_result[$key]["CFC Qty"]) && $final_result[$key]["CFC Qty"] > 0){
                $avg_day_sales_cfc = $avg_day_sales_eaches / $final_result[$key]["CFC Qty"];
            } else{
//                $final_result[$key]["Avg Day Sales (CFC)"] = 0.0000;
                $avg_day_sales_cfc = 0.0000;
            }
            
            if(isset($avg_day_sales_eaches)){
                $final_result[$key]["Avg Day Sales (Eaches)"] = (float) number_format($avg_day_sales_eaches, 5, '.', '');
            }
            
            if(isset($avg_day_sales_cfc)){
                $final_result[$key]["Avg Day Sales (CFC)"] = (float) number_format($avg_day_sales_cfc, 5, '.', '');
            }
            
            $avgEsp = $this->avgEsp($productIdsByGroup[$key], $start, $end);
            if($avgEsp > 0){
                $final_result[$key]["Avg ESP"] = (float) number_format($avgEsp, 5, '.', '');
            } else {
                $final_result[$key]["Avg ESP"] = (float) $value["ESP"];
            }
            
            $avgElp = $this->avgElp($productIdsByGroup[$key], $start, $end, $dc);
            if($avgElp > 0){
                $final_result[$key]["Avg ELP"] = (float) number_format($avgElp, 5, '.', '');
            } else {
                $final_result[$key]["Avg ELP"] = (float) $value["ELP"];
            }
            
            $prod_id = $final_result[$key]['Product Id'];
            $query = json_decode(json_encode(DB::select(DB::raw("Select getProductTotalEarnings($prod_id, '$start_str', '$end_str') as total_earnings"))), true);
            $final_result[$key]["Total Earnings"] = $temp_total_earnings = $query[0]["total_earnings"];
            
//            $final_result[$key]["Total Earnings"] = $temp_total_earnings = $final_result[$key]["Net Sold Qty"] * ($final_result[$key]["Avg ESP"] - $final_result[$key]["Avg ELP"]);
            
            $total_earnings_sum += $temp_total_earnings;
        }
        
        foreach($final_result as $eachKey => $eachValue){
            if($total_tlc > 0) {
                $final_result[$eachKey]["DI %"] = (float) number_format(($final_result[$eachKey]["TLC"] / $total_tlc) * 100, 2, '.', '');
            } else {
                $final_result[$eachKey]["DI %"] = 0.00;
            }
            
            if(isset($final_result[$eachKey]["Avg ELP"]) && $final_result[$eachKey]["Avg ELP"] > 0) {
                $final_result[$eachKey]["MI %"] = (float) number_format((($final_result[$eachKey]["Avg ESP"] - $final_result[$eachKey]["Avg ELP"]) / $final_result[$eachKey]["Avg ELP"]) * 100, 2, '.', '');
            } else {
                $final_result[$eachKey]["MI %"] = 0.00;
            }
            
            if($total_earnings_sum > 0){
                $final_result[$eachKey]["CI %"] = (float) number_format(($final_result[$eachKey]["Total Earnings"] / $total_earnings_sum) * 100, 2, '.', '');
            } else {
                $final_result[$eachKey]["CI %"] = 0.00;
            }
            
            $final_result[$eachKey]["DI MI CI Avg %"] = (float) number_format(($final_result[$eachKey]["DI %"] + $final_result[$eachKey]["MI %"] + $final_result[$eachKey]["CI %"]) / 3, 2, '.', '');
            
            if($final_result[$eachKey]["DI MI CI Avg %"] <= 1){
                $final_result[$eachKey]["KVI Ranking"] = 5;
            } elseif($final_result[$eachKey]["DI MI CI Avg %"] > 1 && $final_result[$eachKey]["DI MI CI Avg %"] <= 2){
                $final_result[$eachKey]["KVI Ranking"] = 4;
            } elseif($final_result[$eachKey]["DI MI CI Avg %"] > 2 && $final_result[$eachKey]["DI MI CI Avg %"] <= 3){
                $final_result[$eachKey]["KVI Ranking"] = 3;
            } elseif($final_result[$eachKey]["DI MI CI Avg %"] > 3 && $final_result[$eachKey]["DI MI CI Avg %"] <= 4){
                $final_result[$eachKey]["KVI Ranking"] = 2;
            } elseif($final_result[$eachKey]["DI MI CI Avg %"] > 4){
                $final_result[$eachKey]["KVI Ranking"] = 1;
            }
            
            $final_result[$eachKey]["available_inventory"] += $this->pendingReturns($final_result[$eachKey]["Product Id"], $dc, $start, $end) + $this->openPOQty($final_result[$eachKey]["Product Id"], $dc, $start, $end);

            if($final_result[$eachKey]["CFC Qty"] > 0){
                $final_result[$eachKey]["Available CFC"] = $final_result[$eachKey]["available_inventory"] / $final_result[$eachKey]["CFC Qty"];
            } else {
                $final_result[$eachKey]["Available CFC"] = 0;
            }
            unset($final_result[$eachKey]["available_inventory"]);
            
            if($final_result[$eachKey]["DI %"] >= 1){
                $final_result[$eachKey]["DI Stock Days"] = 7;
            } elseif($final_result[$eachKey]["DI %"] >= 0.5){
                $final_result[$eachKey]["DI Stock Days"] = 5;
            } elseif($final_result[$eachKey]["DI %"] < 0.5){
                $final_result[$eachKey]["DI Stock Days"] = 3;
            }
            
            if($final_result[$eachKey]["MI %"] >= 10){
                $final_result[$eachKey]["MI Stock Days"] = 7;
            } elseif($final_result[$eachKey]["MI %"] >= 5){
                $final_result[$eachKey]["MI Stock Days"] = 5;
            } elseif($final_result[$eachKey]["MI %"] < 5){
                $final_result[$eachKey]["MI Stock Days"] = 3;
            }
            
            if($final_result[$eachKey]["CI %"] >= 1){
                $final_result[$eachKey]["CI Stock Days"] = 7;
            } elseif($final_result[$eachKey]["CI %"] >= 0.5){
                $final_result[$eachKey]["CI Stock Days"] = 5;
            } elseif($final_result[$eachKey]["CI %"] < 0.5){
                $final_result[$eachKey]["CI Stock Days"] = 3;
            }
            
            $final_result[$eachKey]["Avg DI MI CI Stock Days"] = (float) number_format(($final_result[$eachKey]["DI Stock Days"] + $final_result[$eachKey]["MI Stock Days"] + $final_result[$eachKey]["CI Stock Days"]) / 3, 2, '.', '');
            
            $final_result[$eachKey]["Stock Norm"] = (float) number_format(($final_result[$eachKey]["Avg DI MI CI Stock Days"] * $final_result[$eachKey]["Avg Day Sales (CFC)"]), 2, '.', '');
            
//            $final_result[$eachKey]["Suggested BQ"] = number_format(($final_result[$eachKey]["Avg DI MI CI Stock Days"] * $final_result[$eachKey]["Avg Day Sales (CFC)"]) - $final_result[$eachKey]["Available CFC"], 2, '.', '');
            
//            if($final_result[$eachKey]["Suggested BQ"] >= 0){
//                $final_result[$eachKey]["Min BQ"] = ceil($final_result[$eachKey]["Suggested BQ"]);
//            } else {
//                $final_result[$eachKey]["Min BQ"] = 0;
//            }
            
//            if($final_result[$eachKey]["Min BQ"] > 0) {
//                $final_result[$eachKey]["CFC To Buy"] = $final_result[$eachKey]["Min BQ"];
//            } else{
//                $final_result[$eachKey]["CFC To Buy"] = 0;
//            }
            
            $final_result[$eachKey]["Suggested BQ"] = (float) number_format($final_result[$eachKey]["Stock Norm"] - $final_result[$eachKey]["Available CFC"], 2, '.', '');
            
            if($final_result[$eachKey]["Suggested BQ"] > 0) {
                $final_result[$eachKey]["CFC To Buy"] = ceil($final_result[$eachKey]["Suggested BQ"]);
            } else{
                $final_result[$eachKey]["CFC To Buy"] = 0;
            }
            
//            $final_result[$eachKey]["Avg CFC ELP"] = number_format(($final_result[$eachKey]["Avg ELP"] * $final_result[$eachKey]["CFC Qty"]), 2, '.', '');
            
            $final_result[$eachKey]["CFC ELP"] = (float) number_format(($final_result[$eachKey]["CFC Qty"] * $final_result[$eachKey]["ELP"]), 2, '.', '');
            
            if($final_result[$eachKey]["Ebutor Margin %"] <= 1){
                $final_result[$eachKey]["Target CFC ELP"] = (float) number_format(($final_result[$eachKey]["CFC ELP"] / 1.05), 2, '.', '');
            } elseif($final_result[$eachKey]["Ebutor Margin %"] > 1 && $final_result[$eachKey]["Ebutor Margin %"] <= 2) {
                $final_result[$eachKey]["Target CFC ELP"] = (float) number_format(($final_result[$eachKey]["CFC ELP"] / 1.04), 2, '.', '');
            } elseif($final_result[$eachKey]["Ebutor Margin %"]  > 2 && $final_result[$eachKey]["Ebutor Margin %"] <= 3){
                $final_result[$eachKey]["Target CFC ELP"] = (float) number_format(($final_result[$eachKey]["CFC ELP"] / 1.03), 2, '.', '');
            } elseif($final_result[$eachKey]["Ebutor Margin %"] > 3 && $final_result[$eachKey]["Ebutor Margin %"] <= 4){
                $final_result[$eachKey]["Target CFC ELP"] = (float) number_format(($final_result[$eachKey]["CFC ELP"] / 1.02), 2, '.', '');
            } elseif($final_result[$eachKey]["Ebutor Margin %"] > 4 && $final_result[$eachKey]["Ebutor Margin %"] < 5){
                $final_result[$eachKey]["Target CFC ELP"] = (float) number_format(($final_result[$eachKey]["CFC ELP"] / 1.01), 2, '.', '');
            } elseif($final_result[$eachKey]["Ebutor Margin %"] >= 5){
                $final_result[$eachKey]["Target CFC ELP"] = $final_result[$eachKey]["CFC ELP"];
            }
            
            $final_result[$eachKey]["Total Amount"] = (float) number_format(($final_result[$eachKey]["CFC To Buy"] * $final_result[$eachKey]["Target CFC ELP"]), 2, '.', '');
            
            $final_result[$eachKey]["PM"] = $this->srmName($selectedProductByGroup[$eachKey], "name");
            $supplier_info = $this->getLastPoSupplier($final_result[$eachKey]["Product Id"], $dc);
            $final_result[$eachKey]["Supplier Code"] = (isset($supplier_info['le_code']))?$supplier_info['le_code']:'';
            $final_result[$eachKey]["Supplier Name"] = (isset($supplier_info['business_legal_name']))?$supplier_info['business_legal_name']:'';
            
            $final_result[$eachKey]["Max BQ"] = (float) number_format(($final_result[$eachKey]["CFC To Buy"] + ($final_result[$eachKey]["Avg Day Sales (CFC)"] * 1)), 2, '.', '');
            
            $final_result[$eachKey]["Max CFC Allowed"] = ceil($final_result[$eachKey]["Max BQ"]);
            
            if($final_result[$eachKey]["Ebutor Margin %"] == 0){
                $final_result[$eachKey]["Max CFC ELP Allowed"] = (float) number_format(($final_result[$eachKey]["CFC ELP"] / 1.03), 2, '.', '');
            } elseif($final_result[$eachKey]["Ebutor Margin %"] > 0 && $final_result[$eachKey]["Ebutor Margin %"] <= 1){
                $final_result[$eachKey]["Max CFC ELP Allowed"] = (float) number_format(($final_result[$eachKey]["CFC ELP"] / 1.02), 2, '.', '');
            } elseif($final_result[$eachKey]["Ebutor Margin %"] > 1 && $final_result[$eachKey]["Ebutor Margin %"] <= 2){
                $final_result[$eachKey]["Max CFC ELP Allowed"] = (float) number_format(($final_result[$eachKey]["CFC ELP"] / 1.01), 2, '.', '');
            } elseif($final_result[$eachKey]["Ebutor Margin %"] > 2){
                $final_result[$eachKey]["Max CFC ELP Allowed"] = $final_result[$eachKey]["CFC ELP"];
            } else {
                $final_result[$eachKey]["Max CFC ELP Allowed"] = "";
            }
            
            $final_result[$eachKey]["Freebie Product Title"] = $final_result[$eachKey]["freebie"];
            unset($final_result[$eachKey]["freebie"]);
            
            if(isset($final_result[$eachKey]["MRP"])){
                $final_result[$eachKey]["MRP"] = (float) $final_result[$eachKey]["MRP"];
            }
            
            if(isset($final_result[$eachKey]["ESP"])){
                $final_result[$eachKey]["ESP"] = (float) $final_result[$eachKey]["ESP"];
            }
            
            if(isset($final_result[$eachKey]["ELP"])){
                $final_result[$eachKey]["ELP"] = (float) $final_result[$eachKey]["ELP"];
            }
            
            if(isset($final_result[$eachKey]["PTR"])){
                $final_result[$eachKey]["PTR"] = (float) $final_result[$eachKey]["PTR"];
            }

            $final_result[$eachKey]["Star"] = $this->getStarInfo($final_result[$eachKey]["Product Id"], $dc);
        }
       // Log::info("return final result DiMiCiReport");
        return $final_result;
    }
    
    public function cfcQtyByProductId($productId, $leWhId) {
        $selectedProductCfcQty =  DB::table("po_products as pp")
                            ->join("po", "po.po_id", "=", "pp.po_id")
                            ->where("pp.product_id", $productId)
                            ->where("po.le_wh_id", $leWhId)
                            ->orderBy("pp.created_at", "DESC")
                            ->take(1)
                            ->pluck("pp.no_of_eaches")->all();
        if(!empty($selectedProductCfcQty)){
            $res = $selectedProductCfcQty[0];
        } else {
            $res = 0;
        }
        
        return json_decode(json_encode($res), true);
    }
    public function getLastPoSupplier($productId, $leWhId) {
        $poSup =  DB::table("po_products as pp")
                            ->join("po", "po.po_id", "=", "pp.po_id")
                            ->join("legal_entities", "po.legal_entity_id", "=", "legal_entities.legal_entity_id")
                            ->select(['legal_entities.le_code','legal_entities.business_legal_name'])
                            ->where("pp.product_id", $productId)
                            ->where("po.le_wh_id", $leWhId)
                            ->orderBy("pp.created_at", "DESC")->first();
        return json_decode(json_encode($poSup), true);
    }
    public function getSupplierByCode($productId,$le_wh_id,$le_code) {
        $sup =  DB::table("legal_entities")
                ->join("product_tot", "product_tot.supplier_id", "=", "legal_entities.legal_entity_id")
                            ->select(['legal_entities.legal_entity_id','legal_entities.le_code','legal_entities.business_legal_name'])
                            ->where("product_tot.product_id", $productId)
                            ->where("product_tot.le_wh_id", $le_wh_id)
                            ->where("legal_entities.le_code", $le_code)
                ->first();
        return json_decode(json_encode($sup), true);
    }
    
    public function getStarInfo($prod_id, $wh_id) {
        $sql = DB::table("vw_inventory_report")->where("product_id", $prod_id)->where("le_wh_id", $wh_id)->get(array('star'))->all();
        $data = json_decode(json_encode($sql), true);
        return isset($data[0]['star']) ? $data[0]['star'] : "";
    }
    
    public function productGroups($dcId) {
        $productGroups = DB::table("vw_inventory_report")
                         ->distinct()
                         ->where("le_wh_id", $dcId)
                         ->orderBy("product_group_id", "ASC")
                         ->pluck("product_group_id")->all();
        return json_decode(json_encode($productGroups), true);
    }
    
    public function proIdByProGroup($groupId) {
        $productIdByGroupId = DB::table("products")
                              ->where("product_group_id", $groupId)
                              ->where("is_sellable", 1)
                              ->where("kvi", 69001)
                              ->pluck("product_id")->all();
        return json_decode(json_encode($productIdByGroupId), true);
    }
    
    public function lastPoProduct($groupId, $dcId) {
        $selectedProduct =  DB::table("po_products as pp")
                            ->join("po", "po.po_id", "=", "pp.po_id")
                            ->join("products as p", "pp.product_id", "=", "p.product_id")
                            ->where("p.product_group_id", $groupId)
                            ->where("po.le_wh_id", $dcId)
                            ->orderBy("pp.created_at", "DESC")
                            ->take(1)
                            ->pluck("pp.product_id")->all();
        return json_decode(json_encode($selectedProduct), true);
    }
    
    public function detailsByProductId($productId, $dcId) {
        $details = DB::table("products")
                   ->where("product_id", $productId)
                   ->get([DB::raw("product_group_id as 'Product Group Id'"), DB::raw("sku as SKU"), DB::raw("GetUserName(manufacturer_id,3) as 'Manufacturer Name'"),
                       DB::raw("product_title as 'Product Title'"), DB::raw("product_id as 'Product Id'"), DB::raw("getMastLookupValue(kvi) as KVI"), DB::raw("mrp as MRP"), DB::raw("cp_enabled as 'CP Enabled'"),
                       DB::raw("esu as ESU"), /* DB::raw("IFNULL(getProductCfcQty(product_id),0) as 'CFC Qty'"), */ DB::raw("getProductEspByCustType(product_id, 3014)as ESP"),
                       DB::raw("getProductElp(product_id) as ELP"), DB::raw("IFNULL(getPtrValue(product_id),0) as PTR"),
                       DB::raw("(SELECT available_inventory FROM vw_inventory_report WHERE product_id = ".$productId." and le_wh_id = ".$dcId.") as 'available_inventory'"), 
                       DB::raw("getFreeBeeDesc(product_id) as freebie")])->all();
        return json_decode(json_encode($details), true)[0];
    }
    
    public function tlcCountByProductGroup($productIds, $fromDate, $toDate, $dcId) {
        $tlcCount = DB::table("gds_order_products")
                    ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_order_products.gds_order_id")
                    ->where("gds_orders.le_wh_id", $dcId)
                    ->whereIn("gds_order_products.product_id", $productIds)
                    ->whereBetween("gds_order_products.created_at", [$fromDate, $toDate])
//                    ->whereNotIn(DB::raw("DAYOFWEEK(gds_order_products.created_at)"), [1])
                    ->count();
        return json_decode(json_encode($tlcCount), true);
    }
    
    public function netSoldQty($productIds, $fromDate, $toDate, $dcId) {
        $invoice_qty_query = DB::table("gds_invoice_grid")
                            ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_invoice_grid.gds_order_id")
                            ->join("gds_order_invoice", "gds_order_invoice.gds_invoice_grid_id", "=", "gds_invoice_grid.gds_invoice_grid_id")
                            ->join("gds_invoice_items", "gds_invoice_items.gds_order_invoice_id", "=", "gds_order_invoice.gds_order_invoice_id")
                            ->where("gds_orders.le_wh_id", $dcId)
                            ->whereIn("gds_invoice_items.product_id", $productIds)
                            ->whereBetween("gds_orders.order_date", [$fromDate, $toDate])
//                            ->whereNotIn(DB::raw("DAYOFWEEK(gds_invoice_grid.created_at)"), [1])
                            ->select(DB::raw("IFNULL(SUM(gds_invoice_items.qty), 0) AS invoice_qty"))
                            ->get()->all();
        $invoice_qty = json_decode(json_encode($invoice_qty_query), true)[0];
        $return_qty_query = DB::table("gds_returns")
                            ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_returns.gds_order_id")
                            ->where("gds_orders.le_wh_id", $dcId)
                            ->whereIn("gds_returns.product_id", $productIds)
                            ->whereBetween("gds_orders.order_date", [$fromDate, $toDate])
//                            ->whereNotIn(DB::raw("DAYOFWEEK(gds_returns.created_at)"), [1])
                            ->select(DB::raw("IFNULL(SUM(qty), 0) AS return_qty"))
                            ->get()->all();
        $return_qty = json_decode(json_encode($return_qty_query), true)[0];
        if(isset($invoice_qty["invoice_qty"]) && isset($return_qty["return_qty"])){
            $net_sold_qty =  $invoice_qty["invoice_qty"] - $return_qty["return_qty"];
        } else {
            $net_sold_qty =  0;
        }
        return $net_sold_qty;
    }

    public function dateFunct($fromDate, $toDate) {
       $date_from = strtotime($fromDate);
       $date_to = strtotime($toDate);
       $dateArray = array();
       for ($i = $date_from; $i <= $date_to; $i += 86400) {
           $weekDay = date("w", $i);
           if($weekDay == 0){
               $dateArray[] = date("Y-m-d", $i);
           }
       }
       return $dateArray;
    }
    
    public function avgEsp($productIds, $startDate, $endDate) {
        $firstQuery = $avgEsp = array();
        
        foreach($productIds as $eachProductId){
            $firstQuery[$eachProductId] = DB::table("product_prices_history")
                                          ->where("customer_type", 3001)
                                          ->where("product_id", $eachProductId)
                                          ->whereBetween("effective_date", [$startDate, $endDate])
                                          ->orderBy("effective_date", "DESC")
                                          ->pluck("price")->all();
//            if(!empty($firstQuery[$eachProductId])){
//                $secondQuery = DB::table("product_prices")
//                               ->where("customer_type", 3001)
//                               ->where("product_id", $eachProductId)
//                               ->where("effective_date", "<", $startDate)
//                               ->orderBy("effective_date", "DESC")
//                               ->take(1)
//                               ->pluck("price")->all();
//                if(isset($secondQuery[0]) && !empty($secondQuery[0]))
//                $firstQuery[$eachProductId][] = $secondQuery[0];            
//            }
        }
        
        foreach($firstQuery as $product_id => $eachEsps){
            if(count($eachEsps) > 0){
                $avgEsp[$product_id] = array_sum($eachEsps) / count($eachEsps);
//            } else {
//                $avgEsp[$product_id] = 0;
            }
        }
        
        if(count($avgEsp) > 0){
            $final_avg_esp = array_sum($avgEsp) / count($avgEsp);
        } else {
            $final_avg_esp = 0;
        }
        
        return $final_avg_esp;
    }
    
    public function avgElp($productIds, $startDate, $endDate, $dcId) {
        $firstQuery = $avgElp = array();
        
        foreach($productIds as $eachProductId){
            $firstQuery[$eachProductId] = DB::table("purchase_price_history")
                                          ->where("elp", ">", 0)
                                          ->where("le_wh_id", $dcId)
                                          ->where("product_id", $eachProductId)
                                          ->whereBetween("effective_date", [$startDate, $endDate])
                                          ->orderBy("effective_date", "DESC")
                                          ->pluck("elp")->all();
//            $firstQuery[$eachProductId] = DB::table("product_tot")
//                                          ->where("dlp", ">", 0)
//                                          ->where("le_wh_id", $dcId)
//                                          ->where("product_id", $eachProductId)
//                                          ->whereBetween("effective_date", [$startDate, $endDate])
//                                          ->orderBy("effective_date", "DESC")
//                                          ->pluck("dlp")->all();
//            if(!empty($firstQuery[$eachProductId])){
//                $secondQuery = DB::table("product_tot")
//                               ->where("dlp", ">", 0)
//                               ->where("le_wh_id", $dcId)
//                               ->where("product_id", $eachProductId)
//                               ->where("effective_date", "<", $startDate)
//                               ->orderBy("effective_date", "DESC")
//                               ->take(1)
//                               ->pluck("dlp")->all();
//                if(isset($secondQuery[0]) && !empty($secondQuery[0]))
//                $firstQuery[$eachProductId][] = $secondQuery[0];            
//            }
        }
        
        foreach($firstQuery as $product_id => $eachElps){
            if(count($eachElps) > 0){
                $avgElp[$product_id] = array_sum($eachElps) / count($eachElps);
//            } else {
//                $avgElp[$product_id] = 0;
            }
        }
        
        if(count($avgElp) > 0){
            $final_avg_elp = array_sum($avgElp) / count($avgElp);
        } else {
            $final_avg_elp = 0;
        }
        
        return $final_avg_elp;
    }
    
    public function srmName($productId, $type) {
//        $srmQuery = DB::table("product_tot as tot")
//                    ->join("legal_entities as le", function($lejoin){
//                        $lejoin->on("le.legal_entity_id", "=", "tot.supplier_id")
//                        ->where("le.legal_entity_type_id", "=", 1002);
//                    })
//                    ->join("users as u", "u.user_id", "=", "le.rel_manager")
//                    ->where("tot.product_id", $productId);
        $srmQuery = DB::table("inward AS inw")
                    ->join("inward_products AS inw_p", "inw.inward_id", "=", "inw_p.inward_id")
                    ->join("legal_entities as le", function($lejoin){
                        $lejoin->on("le.legal_entity_id", "=", "inw.legal_entity_id")
                        ->where("le.legal_entity_type_id", "=", 1002);
                    })
                    ->join("users as u", "u.user_id", "=", "le.rel_manager")
                    ->where("u.is_active","=", 1)
                    ->where("inw_p.product_id", $productId)
                    ->orderBy("inw.inward_id", "DESC")
                    ->take(1);
        if($type == "name"){
            $srmQuery = $srmQuery->pluck(DB::raw("CONCAT(u.firstname, ' ', u.lastname) as user_name"))->all();
        } elseif($type == "email"){
            $srmQuery = $srmQuery->pluck("email_id")->all();
        }
        $pm = json_decode(json_encode($srmQuery), true);
        if(!empty($pm[0])){
            return $pm[0];
        } else {
            return "";
        }
    }
    
    public function productIdBySku($sku) {
        $productIdQuery = DB::table("products")->where("sku", $sku)->pluck("product_id")->all();
        $productId = json_decode(json_encode($productIdQuery), true);
        return $productId[0];
    }
    
    public function manufaturerByProduct($prodid) {
        $manuIdQuery = DB::table("products")->where("product_id", $prodid)->pluck("manufacturer_id")->all();
        $manuId = json_decode(json_encode($manuIdQuery), true);
        return $manuId[0];
    }

    public function manufaturerName($manuid) {
        $manuQuery = DB::table("legal_entities")
                    ->where("legal_entity_id", $manuid)
                    ->pluck("business_legal_name")->all();
        $manuName = json_decode(json_encode($manuQuery), true);
        return $manuName[0];
    }
    
    public function getSuppliers($prodid, $wh_id) {
        $manuIdQuery = DB::table("product_tot AS pt")
                    ->where("pt.product_id",$prodid)
                    ->where("pt.le_wh_id", $wh_id)
                    ->pluck("pt.supplier_id")->all();
        $manuId = json_decode(json_encode($manuIdQuery), true);
        return $manuId;
    }
    
    public function pendingReturns($productId, $wh_id, $startDate, $endDate) {
        $re_query = DB::table("gds_returns")
                    ->select([DB::raw("SUM(qty) as re_qty")])
                    ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_returns.gds_order_id")
                    ->where("approval_status", "!=", 1)
                    ->where("approval_status", "!=", 0)
                    ->where("approval_status", "!=", NULL)
                    ->where("gds_orders.le_wh_id", "=", $wh_id)
                    ->where("product_id", $productId)
                    ->whereBetween("gds_returns.created_at", [$startDate, $endDate])
                    ->get()->all();

        if(isset($re_query[0])){
            $re_query_en = json_decode(json_encode($re_query[0]), true);
        } else{
            $re_query_en = json_decode(json_encode(reset($re_query)), true);
        }

        if($re_query_en["re_qty"]){
            $final_res = $re_query_en["re_qty"];
        } else {
            $final_res = 0;
        }
        return $final_res;
    }
	

    public function openPOQty($productId, $wh_id, $startDate, $endDate){
        $sql = DB::table("po_products as pp")
               ->join("po", "po.po_id", "=", "pp.po_id")
               ->where("po.po_status", "=", 87001)
               ->where("pp.product_id", "=", $productId)
               ->where("po.le_wh_id", "=", $wh_id)
               ->whereBetween("pp.created_at", [$startDate, $endDate])
               ->pluck(DB::raw("pp.no_of_eaches * pp.qty as qty"))->all();
        
        $result = json_decode(json_encode($sql), true);
        
        if(!empty($result)){
            return $result[0];
        } else {
            return 0;
        }
    }

    public function cfcQtyByProductIdNew($product_id) {
        $selectedProductCfcQty = DB::select(DB::raw("SELECT getProductCfcQty($product_id)  AS CFC"));
        $res = 0;
        if(!empty($selectedProductCfcQty)){
            $res = $selectedProductCfcQty[0]->CFC;
        }
        return $res;
    }
    
}