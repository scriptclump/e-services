<?php

namespace App\Modules\DiMiCiReport\Models;

use Session;
use DB;
use Log;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\Modules\DiMiCiReport\Models\DimiciGrid;

class DimiciGridMongo extends Eloquent {

    protected $connection = 'mongo';
    protected $table = "inv_snapshot";
    
    public function gridData($start, $end, $dc) {
        try{
            $dimiciGrid = new DimiciGrid();
            if(empty($end)){
                $end = date('Y-m-d');
            }
            if(empty($start)){
                $start = date('Y-m-d', strtotime($end. " -15 days"));
            }

            $start = date('Y-m-d', strtotime($start));
            $end = date('Y-m-d', strtotime($end));
            
            echo $start." : ".$end."\n";
            $diff=date_diff(date_create($start),date_create($end));
            $daysDiff =  $diff->format("%a"); 
            //echo $daysDiff."\n";
            $dateRange = $dimiciGrid->dateFunct($start, $end);
            //print_r($dateRange); 
            $daysCount = $daysDiff-count($dateRange);
            //echo $daysCount."\n";

            echo "Days Difference: ".$daysCount."\n";

            $start = date('Y-m-d 00:00:00', strtotime($start));
            $end = date('Y-m-d 23:59:59', strtotime($end));

            echo $start." : ".$end."\n";
            //exit;
            
            $final_result = $newData = array();
            
            $sql = $this->select("product_group_id", "sku", "manufacturer_name", "product_title", "product_id", "kvi", "mrp", "cp_enabled", "esu", "cfc_qty", "esp", "elp", "ptrvalue", "frebee_desc", "updated_at")
                ->where('dcname', 'like', '%'.$dc.'%')
                ->whereBetween("updated_at", [$start, $end]);
            //dd($sql->toSql());
            
            $allData = json_decode(json_encode($sql->orderBy("product_group_id", "ASC")->get()->all()), true);
            
            //echo count($allData)."\n";
            //print_r($allData);
            foreach($allData as $key=>$eachData){
                $uDate = explode(' ',$eachData['updated_at']);
                if(in_array($uDate['0'], $dateRange)){
                    unset($allData[$key]); continue;
                }                    
                $newData[$eachData["product_group_id"]]["product_ids"][] = $eachData["product_id"];
                $newData[$eachData["product_group_id"]]["esps"][] = isset($eachData["esp"]) ? $eachData["esp"] : 0;
                $newData[$eachData["product_group_id"]]["elps"][] = isset($eachData["elp"]) ? $eachData["elp"] : 0;
            }
            
            $total_tlc = 0;
            foreach($newData as $product_group_id => $eachData){
                $newData[$product_group_id]["TLC"] = $temp_tlc = json_decode(json_encode(DB::table("gds_order_products")->whereIn("product_id", array_unique($eachData["product_ids"]))->count()), true);
                $dimiciGrid = new DimiciGrid();
                $nsq_avc = $dimiciGrid->eachProDetails(array_unique($eachData["product_ids"]));
                $newData[$product_group_id]["Net Sold Qty"] = $nsq_avc["net_sold_qty"];
                $newData[$product_group_id]["Available CFC"] = $nsq_avc["available_cfc"];
                $newData[$product_group_id]["PM"] = $nsq_avc["pm"];
                $newData[$product_group_id]["Avg ESP"] = array_sum($eachData["esps"]) / count($eachData["esps"]);
                $newData[$product_group_id]["Avg ELP"] = array_sum($eachData["elps"]) / count($eachData["elps"]);
                $total_tlc += $temp_tlc;
            }
            
            $sql = $sql->groupBy("product_group_id");

//            $final_result['count'] = count($sql->get()->all());
//            $sql = $sql->skip((int) $page * (int) $pageSize)->take((int) $pageSize);
            
            $final_result['result'] = json_decode(json_encode($sql->get()->all()), true);

            echo "Check 1\n";
            $total_earnings_sum = 0;
            foreach($final_result['result'] as $key => $value){
                $uDate = explode(' ',$value['updated_at']);
                if(in_array($uDate[0], $dateRange)){
                    unset($final_result['result'][$key]); continue;
                }
                $final_result['result'][$key]["Product Group Id"] = $final_result['result'][$key]["product_group_id"];
                unset($final_result['result'][$key]["product_group_id"]);
                $final_result['result'][$key]["SKU"] = $final_result['result'][$key]["sku"];
                unset($final_result['result'][$key]["sku"]);
                $final_result['result'][$key]["Manufacturer"] = $final_result['result'][$key]["manufacturer_name"];
                unset($final_result['result'][$key]["manufacturer_name"]);
                $final_result['result'][$key]["Product Title"] = $final_result['result'][$key]["product_title"];
                unset($final_result['result'][$key]["product_title"]);
                $final_result['result'][$key]["Product Id"] = $final_result['result'][$key]["product_id"];
                unset($final_result['result'][$key]["product_id"]);
                $final_result['result'][$key]["KVI"] = $final_result['result'][$key]["kvi"];
                unset($final_result['result'][$key]["kvi"]);
                $final_result['result'][$key]["MRP"] = $final_result['result'][$key]["mrp"];
                unset($final_result['result'][$key]["mrp"]);
                $final_result['result'][$key]["CP Enabled"] = $final_result['result'][$key]["cp_enabled"];
                unset($final_result['result'][$key]["cp_enabled"]);
                $final_result['result'][$key]["ESU"] = $final_result['result'][$key]["esu"];
                unset($final_result['result'][$key]["esu"]);
                $final_result['result'][$key]["CFC QTY"] = $final_result['result'][$key]["cfc_qty"];
                unset($final_result['result'][$key]["cfc_qty"]);
                $final_result['result'][$key]["ESP"] = $final_result['result'][$key]["esp"];
                unset($final_result['result'][$key]["esp"]);
                $final_result['result'][$key]["ELP"] = $final_result['result'][$key]["elp"];
                unset($final_result['result'][$key]["elp"]);
                $final_result['result'][$key]["PTR"] = $final_result['result'][$key]["ptrvalue"];
                unset($final_result['result'][$key]["ptrvalue"]);

                //echo $final_result['result'][$key]["ESP"]." - ".$final_result['result'][$key]["ELP"]."\n";
                if(isset($final_result['result'][$key]["ESP"]) && isset($final_result['result'][$key]["ELP"]) && $final_result['result'][$key]["ELP"]>0){
                    $final_result['result'][$key]["Ebutor Margin %"] = number_format((($final_result['result'][$key]["ESP"] - $final_result['result'][$key]["ELP"]) / $final_result['result'][$key]["ELP"]) * 100, 2)." %";
                }  else{
                    $final_result['result'][$key]["Ebutor Margin %"] = '';
                }

                $final_result['result'][$key]["TLC"] = $newData[$value["product_group_id"]]["TLC"];
                $final_result['result'][$key]["Net Sold Qty"] = $newData[$value["product_group_id"]]["Net Sold Qty"];
                $final_result['result'][$key]["Available CFC"] = $newData[$value["product_group_id"]]["Available CFC"];
                $final_result['result'][$key]["PM"] = $newData[$value["product_group_id"]]["PM"];
                if($newData[$value["product_group_id"]]["Net Sold Qty"] !== 0){
                    $final_result['result'][$key]["Avg Day Sales (Eaches)"] = ($newData[$value["product_group_id"]]["Net Sold Qty"])/$daysCount;
                } else {
                    $final_result['result'][$key]["Avg Day Sales (Eaches)"] = 0;
                }

                if(isset($final_result['result'][$key]["Avg Day Sales (Eaches)"]) && isset($final_result['result'][$key]["CFC QTY"]) && $final_result['result'][$key]["CFC QTY"]>0){
                    $final_result['result'][$key]["Avg Day Sales (CFC)"] = $final_result['result'][$key]["Avg Day Sales (Eaches)"] / $final_result['result'][$key]["CFC QTY"];
                } else{
                    $final_result['result'][$key]["Avg Day Sales (CFC)"] = 0;
                }
                $final_result['result'][$key]["Avg ESP"] = $newData[$value["product_group_id"]]["Avg ESP"];
                $final_result['result'][$key]["Avg ELP"] = $newData[$value["product_group_id"]]["Avg ELP"];
                $final_result['result'][$key]["Total Earnings"] = $temp_total_earnings = $final_result['result'][$key]["Net Sold Qty"] * ($final_result['result'][$key]["Avg ESP"] - $final_result['result'][$key]["Avg ELP"]);
                if($total_tlc>0)
                    $final_result['result'][$key]["DI"] = $newData[$value["product_group_id"]]["TLC"] / $total_tlc;
                else
                    $final_result['result'][$key]["DI"] = 0;
                if(isset($final_result['result'][$key]["Avg ELP"]) && $final_result['result'][$key]["Avg ELP"]>0)
                    $final_result['result'][$key]["MI"] = ($final_result['result'][$key]["Avg ESP"] - $final_result['result'][$key]["Avg ELP"])/$final_result['result'][$key]["Avg ELP"];
                else
                    $final_result['result'][$key]["MI"] = 0;
                $total_earnings_sum += $temp_total_earnings;
            }
            echo "Check 2\n";
            
            foreach($final_result['result'] as $key_element => $value_element){
                //echo "Check 2-1\n";
                $final_result['result'][$key_element]["CI"] = $final_result['result'][$key_element]["Total Earnings"] / $total_earnings_sum;
                //echo "Check 2-2\n";
                $final_result['result'][$key_element]["DI MI CI Avg"] = ($final_result['result'][$key_element]["DI"] + $final_result['result'][$key_element]["MI"] + $final_result['result'][$key_element]["CI"]) / 3;
                //echo "Check 2-3\n";
                if($final_result['result'][$key_element]["DI MI CI Avg"] <= 1){
                    $final_result['result'][$key_element]["KVI Ranking"] = 5;
                } elseif($final_result['result'][$key_element]["DI MI CI Avg"] <= 2){
                    $final_result['result'][$key_element]["KVI Ranking"] = 4;
                } elseif($final_result['result'][$key_element]["DI MI CI Avg"] <= 3){
                    $final_result['result'][$key_element]["KVI Ranking"] = 3;
                } elseif($final_result['result'][$key_element]["DI MI CI Avg"] <= 4){
                    $final_result['result'][$key_element]["KVI Ranking"] = 2;
                } elseif($final_result['result'][$key_element]["DI MI CI Avg"] > 4){
                    $final_result['result'][$key_element]["KVI Ranking"] = 1;
                }
                //echo "Check 2-4\n";
                if($final_result['result'][$key_element]["DI"] >= 1){
                    $final_result['result'][$key_element]["DI Stock Days"] = 15;
                } elseif($final_result['result'][$key_element]["DI"] >= 0.5){
                    $final_result['result'][$key_element]["DI Stock Days"] = 10;
                } elseif($final_result['result'][$key_element]["DI"] < 0.5){
                    $final_result['result'][$key_element]["DI Stock Days"] = 5;
                }
                //echo "Check 2-5\n";
                if($final_result['result'][$key_element]["MI"] >= 10){
                    $final_result['result'][$key_element]["MI Stock Days"] = 15;
                } elseif($final_result['result'][$key_element]["MI"] >= 5){
                    $final_result['result'][$key_element]["MI Stock Days"] = 10;
                } elseif($final_result['result'][$key_element]["MI"] < 5){
                    $final_result['result'][$key_element]["MI Stock Days"] = 5;
                }
                //echo "Check 2-6\n";
                if($final_result['result'][$key_element]["CI"] >= 1){
                    $final_result['result'][$key_element]["CI Stock Days"] = 15;
                } elseif($final_result['result'][$key_element]["CI"] >= 0.5){
                    $final_result['result'][$key_element]["CI Stock Days"] = 10;
                } elseif($final_result['result'][$key_element]["CI"] < 0.5){
                    $final_result['result'][$key_element]["CI Stock Days"] = 5;
                }
                //echo "Check 2-7\n";
                $final_result['result'][$key_element]["Avg DI MI CI Stock Days"] = ($final_result['result'][$key_element]["DI Stock Days"] + $final_result['result'][$key_element]["MI Stock Days"] + $final_result['result'][$key_element]["CI Stock Days"]) / 3;
                //echo "Check 2-8\n";
                $final_result['result'][$key_element]["Suggested BQ"] = ($final_result['result'][$key_element]["Avg DI MI CI Stock Days"] * $final_result['result'][$key_element]["Avg Day Sales (CFC)"]) - $final_result['result'][$key_element]["Available CFC"];
                //echo "Check 2-9\n";
                $final_result['result'][$key_element]["Min BQ"] = ceil($final_result['result'][$key_element]["Suggested BQ"]);
                //echo "Check 2-10\n";
                
                if($final_result['result'][$key_element]["Min BQ"] > 0) {
                    $final_result['result'][$key_element]["CFC To Buy"] = $final_result['result'][$key_element]["Min BQ"];
                } else{
                    $final_result['result'][$key_element]["CFC To Buy"] = 0;
                }
                //echo "Check 2-11\n";
                $final_result['result'][$key_element]["Avg CFC ELP"] = ($final_result['result'][$key_element]["Avg ELP"] * $final_result['result'][$key_element]["CFC QTY"]);
                $final_result['result'][$key_element]["CFC ELP"] = ($final_result['result'][$key_element]["CFC QTY"] * $final_result['result'][$key_element]["ELP"]);
                //echo "Check 2-12\n";
                if($final_result['result'][$key_element]["Net Sold Qty"] <= 1){
                    $final_result['result'][$key_element]["Target CFC ELP"] = $final_result['result'][$key_element]["CFC ELP"] / 1.05;
                } elseif($final_result['result'][$key_element]["Net Sold Qty"] > 1 && $final_result['result'][$key_element]["Net Sold Qty"] <= 2) {
                    $final_result['result'][$key_element]["Target CFC ELP"] = $final_result['result'][$key_element]["CFC ELP"] / 1.04;
                } elseif($final_result['result'][$key_element]["Net Sold Qty"]  > 2 && $final_result['result'][$key_element]["Net Sold Qty"] <= 3){
                    $final_result['result'][$key_element]["Target CFC ELP"] = $final_result['result'][$key_element]["CFC ELP"] / 1.03;
                } elseif($final_result['result'][$key_element]["Net Sold Qty"] > 3 && $final_result['result'][$key_element]["Net Sold Qty"] <= 4){
                    $final_result['result'][$key_element]["Target CFC ELP"] = $final_result['result'][$key_element]["CFC ELP"] / 1.02;
                } elseif($final_result['result'][$key_element]["Net Sold Qty"] > 4 && $final_result['result'][$key_element]["Net Sold Qty"] < 5){
                    $final_result['result'][$key_element]["Target CFC ELP"] = $final_result['result'][$key_element]["CFC ELP"] / 1.01;
                } elseif($final_result['result'][$key_element]["Net Sold Qty"] >= 5){
                    $final_result['result'][$key_element]["Target CFC ELP"] = $final_result['result'][$key_element]["CFC ELP"];
                }
                //echo "Check 2-13\n";
                //echo $final_result['result'][$key_element]["CFC To Buy"]." - ".$final_result['result'][$key_element]["Target CFC ELP"]."<br>";
                $final_result['result'][$key_element]["Total Amount"] = ($final_result['result'][$key_element]["CFC To Buy"] * $final_result['result'][$key_element]["Target CFC ELP"]);
                //echo "Check 2-14\n";
                $final_result['result'][$key_element]["Max BQ"] = $final_result['result'][$key_element]["CFC To Buy"] + ($final_result['result'][$key_element]["Avg Day Sales (CFC)"] * 2);
                //echo "Check 2-15\n";
                $final_result['result'][$key_element]["Max CFC Allowed"] = ceil($final_result['result'][$key_element]["Max BQ"]);
                //echo "Check 2-16\n";
                
                if($final_result['result'][$key_element]["Ebutor Margin %"] == 0){
                    $final_result['result'][$key_element]["Max CFC ELP Allowed"] = $final_result['result'][$key_element]["CFC ELP"] / 1.03;
                } elseif($final_result['result'][$key_element]["Ebutor Margin %"] <= 1){
                    $final_result['result'][$key_element]["Max CFC ELP Allowed"] = $final_result['result'][$key_element]["CFC ELP"] / 1.02;
                } elseif($final_result['result'][$key_element]["Ebutor Margin %"] <= 2){
                    $final_result['result'][$key_element]["Max CFC ELP Allowed"] = $final_result['result'][$key_element]["CFC ELP"] / 1.01;
                } elseif($final_result['result'][$key_element]["Ebutor Margin %"] > 2){
                    $final_result['result'][$key_element]["Max CFC ELP Allowed"] = $final_result['result'][$key_element]["CFC ELP"];
                }                
                $final_result['result'][$key_element]["Freebie Product Title"] = $final_result['result'][$key_element]["frebee_desc"];
                unset($final_result['result'][$key_element]["frebee_desc"]);
                //$final_result['result'][$key_element]["Updated At"] = $final_result['result'][$key_element]["updated_at"];
                unset($final_result['result'][$key_element]["updated_at"]);
                //echo "Check 2-17\n";
            }
            echo "Check 3\n";
            return $final_result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
}