<?php

namespace App\Modules\DiMiCiReport\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Log;

class InventorySnapshot extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'inventory_snapshot';
    protected $primaryKey = '_id';
    
    public function noStockDays($groupIds, $leWhId, $startDate, $endDate) {
       try {
            $groupWise = $groupDateSoh = $final = array();
            
            $query = $this
                     ->where("le_wh_id", (int)$leWhId)
                     ->whereIn("product_group_id", array_map("intval", $groupIds))
                     ->whereBetween("created_at", [$startDate, $endDate])
                     ->get(["le_wh_id", "product_id", "product_group_id", "soh", "created_at"])->all();
            $resultAll = json_decode(json_encode($query), true);
            
            foreach($resultAll as $each){
                $date = explode(" ", $each["created_at"]);
                $groupWise[$each["product_group_id"]][$each["product_id"]][$date[0]] = $each["soh"];
            }
            foreach($groupWise as $group_id => $products){
                foreach($products as $product_id => $values){
                    foreach($values as $date => $soh){
                        if($soh > 0){
                            $groupDateSoh[$group_id][$date] = array_sum(array_column($products, $date));
                        }
                    }
                }
            }
            foreach($groupDateSoh as $groupid => $eachGroup){
                if(array_sum($eachGroup) > 0){
                    $final[$groupid] = count($eachGroup);
                } else {
                    $final[$groupid] = 0;
                }
            }
            return $final;
       } catch (\ErrorException $ex) {
           Log::error($ex->getMessage());
           Log::error($ex->getTraceAsString());
       }
    }
    
}