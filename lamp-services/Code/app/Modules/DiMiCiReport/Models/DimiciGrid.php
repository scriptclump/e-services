<?php

namespace App\Modules\DiMiCiReport\Models;

use Session;
use DB;
use Log;
use Illuminate\Database\Eloquent\Model;

class DimiciGrid extends Model {
    
    public function eachProDetails($productIds) {
        $order_qty = DB::table("gds_order_products")
                    ->whereIn("product_id", $productIds)
                    ->select(DB::raw("SUM(getReturnPrdQty(gds_order_id, product_id)) AS return_qty"), "qty")
                    ->get()->all();
        $netSoldQty = json_decode(json_encode($order_qty), true)[0];
        if(isset($netSoldQty["qty"]) && isset($netSoldQty["return_qty"])){
            $final["net_sold_qty"] =  $netSoldQty["qty"] - $netSoldQty["return_qty"];
        } else {
            $final["net_sold_qty"] =  0;
        }
        
        foreach($productIds as $proId){
            $query = json_decode(json_encode(DB::select("select IFNULL(getProductCfcQty(".$proId."), 0) AS available_cfc")), true);
            $quantity[] = $query[0]["available_cfc"];
            
            $srmQuery = DB::table("product_tot as tot")
                    ->join("legal_entities as le", function($lejoin){
                        $lejoin->on("le.legal_entity_id", "=", "tot.supplier_id")
                        ->where("le.legal_entity_type_id", "=", 1002);
                    })
                    ->join("users as u", "u.user_id", "=", "le.rel_manager")
                    ->where("tot.product_id", $proId)
                    ->pluck(DB::raw("distinct(CONCAT(u.firstname, ' ', u.lastname)) as user_name"))->all();
            $final["pm"] = json_decode(json_encode($srmQuery), true);
        }
        $final["available_cfc"] = array_sum($quantity);
        return $final;
    }
    
    public function noOfDays() {
        $numberofdays = 15;
        $today = strtotime(date('Y-m-d'));
        for($i=0; $i<$numberofdays; $i++){
            $substractDay = 86400;
            $previousDay = date('w', ($today-$substractDay));
            if($previousDay == 0 || $previousDay == 6) {
                $i--;
            }
            $today = $today-$substractDay;
        }
        $dateRange["from"] = date("Y-m-d", $today);
        $dateRange["to"] = date("Y-m-d", strtotime("-1 day"));
        return $dateRange;
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

    public function getUserEmail($userId){
        $srmQuery = json_decode(json_encode(DB::table("users")->where('user_id','=',$userId)->pluck('email_id')->all()), true);
        return ($srmQuery[0]);
    }

    public function getAllWareHouses() {
       $warehouses_table = DB::table('legalentity_warehouses')->select('lp_wh_name as name', 'le_wh_id');
       if (Session('roleId') != '1') {
           $warehouses_table = $warehouses_table->where('legal_entity_id', Session::get('legal_entity_id'));
       }

       $warehouses = $warehouses_table->where('lp_wh_name', '!=', NULL)->where('lp_wh_name', '!=', '')->where('dc_type', '=', '118001')->where('status', 1)->orderBy('lp_wh_name', 'asc')->get()->all();
       return $warehouses;
    }

    public function getEmailByName($userName){
        $srmQuery = json_decode(json_encode(DB::table("users")->where(DB::raw("CONCAT(firstname, ' ', lastname)"), '=', $userName)->pluck('email_id')->all()), true);
        return ($srmQuery[0]);
    }
    
}