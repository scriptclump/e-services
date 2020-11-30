<?php
namespace App\Modules\DeliveryExecutiveEffieiencyReport\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use App\Modules\Inventory\Models\InventorySnapshot;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Notifications;
use UserActivity;
use Utility;
use Log;

class DeliveryEfficiencyModel extends Model {

    //grid data
    public function getGridData($filtered_data, $page, $pageSize)
    {
        try {
            $role = new Role();
            date_default_timezone_set('UTC');
            
            $data = json_decode($role->getFilterData(6), true);
            $hubids = json_decode($data['sbu'], true);

            //getting all hubs
            $hubs_array = array();
			$explodedata_hubids = [0];
            if(isset($hubids['118002']))
            {
                $explodedata_hubids = explode(",", $hubids['118002']);  //118002 is hub type  , 118001 is dc type 
            }
            else if(isset($hubids['118001']) && !isset($hubids['118002']))
            {
                $explode_warehouses = explode(",", $hubids['118001']);
                
                $sql_hubs = DB::table("legalentity_warehouses as lw")
                                ->join("business_units as bu", "bu.bu_id", "=", "lw.bu_id")
                                ->join("business_units as bu2", "bu2.parent_bu_id", "=", "bu.bu_id")
                                ->join("legalentity_warehouses as lw2", "lw2.bu_id", "=", "bu2.bu_id")
                                ->where("lw2.dc_type", "!=", "118001")
                                ->whereIn("lw.le_wh_id", $explode_warehouses)
                                ->groupBy("lw2.le_wh_id")
                                ->pluck("lw2.lp_wh_name", "lw2.le_wh_id")->all();

                $explodedata_hubids = array_keys($sql_hubs);
                
            }
        // $deliveried_statuses = array("17023", "17007", "17010", "17014"); //status for delivered, partial delivered, returned and Hold statues
        // 
            $deliveried_statuses = array("17023", "17007", "17010", "17014"); //status for delivered, partial delivered
            $result = array();
            $mainarr = array();
            $mainData1 = array();
            $del_temp_arr = "";
            $return_array = "";
            $resultedArr = array(
                             "gds_orders.gds_order_id as OrderId",
                             "gds_order_track.delivery_date as del_date",
                             "gds_orders.order_date",
                             DB::raw("getLeWhName(gds_orders.hub_id) as hub_name"),
                             "gds_order_products.product_id",
                             "legalentity_warehouses.longitude as warehouse_lang",
                             "legalentity_warehouses.latitude as warehouse_lattitude",
                             "legal_entities.longitude as legal_longitude",
                             "legal_entities.latitude as legal_latitude",
                              DB::raw("GetUserName(gds_order_track.delivered_by, 2) as deliverdBy"),
                              "gds_orders.order_code as order_num",
                              "gds_invoice_items.qty as inv_qty",
                              "gds_invoice_items.row_total_incl_tax as inv_val",
                              "gds_order_products.sku as inv_SKU",
                              "master_lookup.master_lookup_name as order_status",
                              DB::raw("getOrderAreaName(gds_orders.cust_le_id) as area_name"),
                              DB::raw("getBeatName(gds_orders.beat) as beat_name"),
                              "gds_orders.order_status_id",
                              "gds_order_track.delivery_start_time",
                              "gds_order_track.delivery_end_time",

                              );
        $sql = DB::table("gds_orders")
                ->join("gds_order_track", "gds_orders.gds_order_id", "=", "gds_order_track.gds_order_id")
                ->join("gds_invoice_grid", "gds_orders.gds_order_id", "=", "gds_invoice_grid.gds_order_id")
                ->join("gds_order_invoice", "gds_order_invoice.gds_invoice_grid_id", "=", "gds_invoice_grid.gds_invoice_grid_id")
                //->join("gds_invoice_items", "gds_invoice_items.gds_order_invoice_id", "=", "gds_order_invoice.gds_order_invoice_id")
                ->join("gds_order_products", "gds_orders.gds_order_id", "=", "gds_order_products.gds_order_id")
                ->join("gds_invoice_items",function($join){
                        $join->on("gds_invoice_items.gds_order_invoice_id","=", "gds_order_invoice.gds_order_invoice_id")
                        ->on("gds_orders.gds_order_id", "=", "gds_invoice_items.gds_order_id")
                        ->on("gds_order_products.product_id", "=", "gds_invoice_items.product_id");
                })
                
                ->join("legalentity_warehouses", "gds_orders.le_wh_id", "=", "legalentity_warehouses.le_wh_id")
                ->join("legal_entities", "gds_orders.cust_le_id", "=", "legal_entities.legal_entity_id")
                ->join("master_lookup", "gds_orders.order_status_id", "=", "master_lookup.value")
                ->whereIn("gds_orders.order_status_id", $deliveried_statuses)//17007 is deliveried status//17007 is deliveried status
                ->where("gds_orders.cust_le_id", "!=", NULL)
                ->whereIn("gds_orders.hub_id", $explodedata_hubids)
                ->groupBy("gds_orders.gds_order_id")->groupBy("gds_order_products.product_id");
                
        // if($filtered_data['date'])
        // {
        //     $sql = $sql
        //             ->where("gds_order_track.delivery_date", ">=", date("Y-m-d", strtotime($filtered_data['date']))." 00:00:00")
        //             ->where("gds_order_track.delivery_date", "<=", date("Y-m-d", strtotime($filtered_data['date']))." 23:59:59");
        // }

        if($filtered_data['date'])
        {
            $sql = $sql
                    ->where("gds_order_track.delivery_date", ">=", date("Y-m-d", strtotime($filtered_data['date']))." 00:00:00");
        }


        if($filtered_data['to_date'])
        {
            $sql = $sql
                    ->where("gds_order_track.delivery_date", "<=", date("Y-m-d", strtotime($filtered_data['to_date']))." 23:59:59");
        }


        if($filtered_data['del_users'])
        {
            $sql = $sql->whereIn("gds_order_track.delivered_by", $filtered_data['del_users']);
        }

        if($filtered_data['hubs'])
        {
            $sql = $sql->whereIn("gds_orders.hub_id", $filtered_data['hubs']);
        }

        $countarr = $sql->get(array("gds_orders.gds_order_id"))->all();
        $count = count($countarr);

        $sql = $sql->skip($page * $pageSize)->take($pageSize);
        $sql = $sql->get($resultedArr)->all();

        $data = json_decode(json_encode($sql), true);
        $return_Info = $this->orderReturnInfo(); //getting all order return data
        
        foreach ($data as $key => $value) {
            // $value['estimated_distance'] =  round($this->getDistanceinKm($value['warehouse_lattitude'], $value['warehouse_lang'], $value['legal_latitude'], $value['legal_longitude'], "K"), 2) . " Kilometers";  // For calculating Esimated distance
            $submit_time = $this->getSubmitTime($value['OrderId']);
            $delivery_time = $this->getAssignedTime($value['OrderId']);
            $de_start_time =  $value['delivery_start_time'];
            $de_end_time   =  $value['delivery_end_time'];
            if(strlen($delivery_time) != 0)
              $value['delivery_time'] = date("m-d-Y H:i:s", strtotime($delivery_time));
            else
              $value['delivery_time'] = "";
            // $value['submit_time'] = date("m-d-Y H:i:s", strtotime($submit_time));
            $value['submit_time'] = $value['delivery_end_time'];

            // $value['duration'] = date("H:i:s", strtotime($submit_time)-strtotime($delivery_time) );
            $value['duration'] = date("H:i:s", strtotime($de_end_time)-strtotime($de_start_time) );
            $value['delivery_date'] = date("m-d-Y", strtotime($value['del_date']));
            
            if(array_key_exists($value['OrderId'], $return_Info))
            {

              
                $return_qty = isset($return_Info[$value['OrderId']][$value['product_id']]['return_qty'])?$return_Info[$value['OrderId']][$value['product_id']]['return_qty']:0;
                $value['delivered_qty'] = ($value['inv_qty'] - $return_qty);
            

                if(!empty($return_qty))
                {
                  $mainData1[] = array_merge($value, $return_Info[$value['OrderId']][$value['product_id']]);
                }
                else
                {
                   $mainData1[] = array_merge($value, array("qty" => 0, "return_qty" => 0, "reason" => "")); 
                }
            
                
            }
            else
            {
                $return_qty = isset($return_Info[$value['OrderId']][$value['product_id']]['return_qty'])?$return_Info[$value['OrderId']][$value['product_id']]['return_qty']:0;
                $value['delivered_qty'] = ($value['inv_qty'] - $return_qty);
             
                $del_temp_arr = array("qty" => 0, "return_qty" => 0, "reason" => "");
                $mainData1[] = array_merge($value, $del_temp_arr);
            }


            
        }
        
        $result['result'] = $mainData1;
        $result['count'] = $count;
        return $result;
    } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    //getting order return info
    public function orderReturnInfo()
    {
        $return_arr = array();
        $resulted_Arr = array(
                            "gds_orders.gds_order_id", 
                            "gds_returns.product_id",
                            "gds_returns.qty",
                            DB::raw("sum(gds_returns.approved_quantity) as return_qty"),
                            "master_lookup_name as reason",

                        );
        $sql = DB::table("gds_orders")
                   ->leftJoin("gds_order_products", "gds_orders.gds_order_id", "=", "gds_order_products.gds_order_id")
                   // ->join("gds_returns", "gds_orders.gds_order_id", "=", "gds_returns.gds_order_id")

                   ->leftJoin("gds_returns",function($join){
                        $join->on("gds_orders.gds_order_id","=", "gds_returns.gds_order_id")
                        ->on("gds_order_products.product_id", "=", "gds_returns.product_id");
                })
                   ->join("master_lookup", "gds_returns.return_reason_id", "=", "master_lookup.value")
                   ->groupBy("gds_orders.gds_order_id")
                   ->groupBy("gds_returns.product_id")
                   ->get($resulted_Arr)->all();

       $data  = json_decode(json_encode($sql), true);
       foreach ($data as $key => $value) {
           $return_arr[$value['gds_order_id']][$value['product_id']] = array("qty" => $value['qty'], "return_qty" => $value['return_qty'], "reason" => $value['reason']);
       }
       return $return_arr;
    }
//calculating distance from latittude and longitude
public function getDistance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } 
        else if ($unit == "N") {
            return ($miles * 0.8684);
        }
        else {
            return $miles;
        }
    }

public function getDistanceinKm($lat1, $lng1, $lat2, $lng2, $miles = true)
{
    try {

            $pi80 = M_PI / 180;
            $lat1 *= $pi80;
            $lng1 *= $pi80;
            $lat2 *= $pi80;
            $lng2 *= $pi80;
         
            $r = 6372.797; // mean radius of Earth in km
            $dlat = $lat2 - $lat1;
            $dlng = $lng2 - $lng1;
            $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $km = $r * $c;
         
            return ($miles ? ($km * 0.621371192) : $km);
        
    } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

}

    public function getExport($filtered_data)
    {
        try {
                $role = new Role();
                date_default_timezone_set('UTC');
                
                $data = json_decode($role->getFilterData(6), true);
        $hubids = json_decode($data['sbu'], true);

        //getting all hubs
        $hubs_array = array();
        $explodedata_hubids = [0];
        if(isset($hubids['118002']))
        {
          $explodedata_hubids = explode(",", $hubids['118002']);  //118002 is hub type  , 118001 is dc type 
        }
        else if(isset($hubids['118001']) && !isset($hubids['118002']))
        {
          $explode_warehouses = explode(",", $hubids['118001']);
          
          $sql_hubs = DB::table("legalentity_warehouses as lw")
                  ->join("business_units as bu", "bu.bu_id", "=", "lw.bu_id")
                  ->join("business_units as bu2", "bu2.parent_bu_id", "=", "bu.bu_id")
                  ->join("legalentity_warehouses as lw2", "lw2.bu_id", "=", "bu2.bu_id")
                  ->where("lw2.dc_type", "!=", "118001")
                  ->whereIn("lw.le_wh_id", $explode_warehouses)
                  ->groupBy("lw2.le_wh_id")
                  ->pluck("lw2.lp_wh_name", "lw2.le_wh_id")->all();

          $explodedata_hubids = array_keys($sql_hubs);
          
        }

                $result = array();
                $deliveried_statuses = array("17023", "17007", "17010", "17014"); //status for delivered, partial delivered and returned
                $mainarr = array();
                $mainData1 = array();
                $del_temp_arr = "";
                $resultedArr = array(
                             "gds_orders.gds_order_id as OrderId",
                             "gds_order_track.delivery_date as del_date",
                             "gds_orders.order_date",
                             DB::raw("getLeWhName(gds_orders.hub_id) as hub_name"),
                             "gds_order_products.product_id",
                             "legalentity_warehouses.longitude as warehouse_lang",
                             "legalentity_warehouses.latitude as warehouse_lattitude",
                             "legal_entities.longitude as legal_longitude",
                             "legal_entities.latitude as legal_latitude",
                              DB::raw("GetUserName(gds_order_track.delivered_by, 2) as deliverdBy"),
                              "gds_orders.order_code as order_num",
                              "gds_invoice_items.qty as inv_qty",
                              "gds_invoice_items.row_total_incl_tax as inv_val",
                              "gds_order_products.sku as inv_SKU",
                              "master_lookup.master_lookup_name as order_status",
                              DB::raw("getOrderAreaName(gds_orders.cust_le_id) as area_name"),
                              DB::raw("getBeatName(gds_orders.beat) as beat_name"),
                              "gds_orders.order_status_id",
                              "delivery_start_time",
                              "delivery_end_time"


                              );
                // $sql = DB::table("gds_orders")
                //         ->join("gds_order_track", "gds_orders.gds_order_id", "=", "gds_order_track.gds_order_id")
                //         ->join("gds_invoice_grid", "gds_orders.gds_order_id", "=", "gds_invoice_grid.gds_order_id")
                //         ->join("gds_order_invoice", "gds_order_invoice.gds_invoice_grid_id", "=", "gds_invoice_grid.gds_invoice_grid_id")
                //         //->join("gds_invoice_items", "gds_invoice_items.gds_order_invoice_id", "=", "gds_order_invoice.gds_order_invoice_id")
                //         ->join("gds_order_products", "gds_orders.gds_order_id", "=", "gds_order_products.gds_order_id")
                //         ->join("gds_invoice_items",function($join){
                //                 $join->on("gds_invoice_items.gds_order_invoice_id","=", "gds_order_invoice.gds_order_invoice_id")
                //                 ->on("gds_orders.gds_order_id", "=", "gds_invoice_items.gds_order_id")
                //                 ->on("gds_order_products.product_id", "=", "gds_invoice_items.product_id");
                //         })
                        
                //         ->join("legalentity_warehouses", "gds_orders.le_wh_id", "=", "legalentity_warehouses.le_wh_id")
                //         ->join("legal_entities", "gds_orders.cust_le_id", "=", "legal_entities.legal_entity_id")
                //         ->join("master_lookup", "gds_orders.order_status_id", "=", "master_lookup.value")
                //         ->whereIn("gds_orders.order_status_id", $deliveried_statuses)//17007 is deliveried status//17007 is deliveried status
                //         ->where("gds_orders.cust_le_id", "!=", NULL)
                //         ->whereIn("gds_orders.hub_id", $explodedata_hubids)
                //         ->groupBy("gds_orders.gds_order_id")->groupBy("gds_order_products.product_id");
                
        
        // if($filtered_data['date'])
        // {
        //     $sql = $sql
        //             ->where("gds_order_track.delivery_date", ">=", date("Y-m-d", strtotime($filtered_data['date']))." 00:00:00")
        //             ->where("gds_order_track.delivery_date", "<=", date("Y-m-d", strtotime($filtered_data['date']))." 23:59:59");
        // }
          if($filtered_data['date'])
          {           
              $date_st = date("Y-m-d", strtotime($filtered_data['date']))." 00:00:00";
          }
          if($filtered_data['to_date'])
          {
             $date_ed = date("Y-m-d", strtotime($filtered_data['to_date']))." 00:00:00";
          }
        
          if($filtered_data['del_users'])
          {
            $deliver_impl = implode(",", $filtered_data['del_users']);
            $deliver_impl = "'".$deliver_impl."'";
          }
          elseif(is_null($filtered_data['del_users']))
          {
             $deliver_impl = 'NULL';
          }
          if($filtered_data['hubs'])
          {
            $explodedata_impl = implode(",", $filtered_data['hubs']); //'7,8,9,'
            $explodedata_impl = "'".$explodedata_impl."'";
          }
          elseif(is_null($filtered_data['hubs']))
          {
            $explodedata_impl='NULL';

          }
        $query = "CALL getDeliveryEfficiencyReport($explodedata_impl,$deliver_impl,'".$date_st."','".$date_ed."')";
        $file_name = 'DeliveryExecutiveEffieiencyReport'.date('d-m-Y-H-i-s').'.csv';
        $filePath = public_path().'/uploads/reports/'.$file_name;
        $invobj = new InventorySnapshot(); 
        $dataReport = $invobj->exportToExcel($query,$file_name,$filePath);
        $data = json_decode(json_encode($dataReport), true);
        $return_Info = $this->orderReturnInfo(); //getting all order return data
        
        foreach ($data as $key => $value) {
            // $value['estimated_distance'] =  round($this->getDistanceinKm($value['warehouse_lattitude'], $value['warehouse_lang'], $value['legal_latitude'], $value['legal_longitude'], "K"), 2) . " Kilometers";  // For calculating Esimated distance
            $submit_time = $this->getSubmitTime($value['OrderId']);
            $delivery_time = $this->getAssignedTime($value['OrderId']);
            $de_start_time =  $value['delivery_start_time'];
            $de_end_time   =  $value['delivery_end_time'];
            if(strlen($delivery_time) != 0)
              $value['delivery_time'] = date("m-d-Y H:i:s", strtotime($delivery_time));
            else
              $value['delivery_time'] = "";
            // $value['submit_time'] = date("m-d-Y H:i:s", strtotime($submit_time));
            $value['submit_time'] = $value['delivery_end_time'];

            // $value['duration'] = date("H:i:s", strtotime($submit_time)-strtotime($delivery_time) );
            $value['duration'] = date("H:i:s", strtotime($de_end_time)-strtotime($de_start_time) );
            $value['delivery_date'] = date("m-d-Y", strtotime($value['del_date']));
            
            if(array_key_exists($value['OrderId'], $return_Info))
            {


                $return_qty = isset($return_Info[$value['OrderId']][$value['product_id']]['return_qty'])?$return_Info[$value['OrderId']][$value['product_id']]['return_qty']:0;
                $value['delivered_qty'] = ($value['inv_qty'] - $return_qty);

                if(!empty($return_qty))
                {
                  $mainData1[] = array_merge($value, $return_Info[$value['OrderId']][$value['product_id']]);
                }
                else
                {
                   $mainData1[] = array_merge($value, array("qty" => 0, "return_qty" => 0, "reason" => "")); 
                }
            
                
            }
            else
            {
                $return_qty = isset($return_Info[$value['OrderId']][$value['product_id']]['return_qty'])?$return_Info[$value['OrderId']][$value['product_id']]['return_qty']:0;
                $value['delivered_qty'] = ($value['inv_qty'] - $return_qty);

                $del_temp_arr = array("qty" => 0, "return_qty" => 0, "reason" => "");
                $mainData1[] = array_merge($value, $del_temp_arr);
            }

        }

        // echo "<prE>";print_r($mainData1);die;
        return $mainData1;
    } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    
    }


    /*Get All Delivery Persons*/
    public function getAllDeliveriedUsers()
    {
        $rolerepo = new RoleRepo();
        $mainArr = array();
        $getuserIdfor_delivary = DB::table("roles")->where("name", "=", "Delivery Executive")->get(array("role_id"))->all();
        $data = json_decode(json_encode($getuserIdfor_delivary), true);
        $role_id = $data[0]['role_id'];

        $getuserIds = $rolerepo->getRoleById($role_id);
        $getuserIds = json_decode(json_encode($getuserIds), true);
        
        $alluserIDS  = $getuserIds['user_id'];

        $resulted_array = array(
                            DB::raw("concat(firstname,' ',lastname) as name"),
                            "user_id" 
                            );
        $userdetails = DB::table("users")->whereIn("user_id", $alluserIDS)->get($resulted_array)->all();
        $userdetails = json_decode(json_encode($userdetails), true);
        
        foreach ($userdetails as $value) {
            $mainArr[$value['user_id']] = $value['name'];
        }
        return $mainArr;
    }

    public function getAssignedTime($orderId)
    {
        try {

                $sql = DB::table("gds_orders_comments")
                    ->where("entity_id", "=", $orderId)
                    ->where("comment_type", "=", 53)
                    ->where("order_status_id", "=", 17026)
                    ->orderBy("comment_date", "desc")
                    ->limit(1)
                    ->get(array("comment_date"))->all(); // shipment status is 53

                $data = json_decode(json_encode($sql), true);

                return isset($data[0]['comment_date'])?$data[0]['comment_date']:"";
            
        } catch (Exception $e) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
        
    }
    public function getSubmitTime($orderId)
    {
        try {
                $sql = DB::table("gds_orders_comments")
                    ->where("entity_id", "=", $orderId)
                    ->where("comment_type", "=", 17)
                    ->whereIn("order_status_id", array('17023','17007', '17014', '17022'))
                    ->orderBy("comment_date", "desc")
                    ->limit(1)
                    ->get(array("comment_date"))->all(); // shipment status is 53

                $data = json_decode(json_encode($sql), true);

                return isset($data[0]['comment_date'])?$data[0]['comment_date']:"";
            
        } catch (Exception $e) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }

        
    
    }

    public function getAllHubs()
    {
        try {
            $role = new Role();
            $mainarray = array();
            $data = json_decode($role->getFilterData(6), true);

            // $sql  = DB::table("legalentity_warehouses")
            //         ->where("dc_type", "=", 118002)
            //         ->pluck("lp_wh_name", "le_wh_id")->all();
            $hubids = json_decode($data['sbu'], true);
            $hubs_array = array();
                if(isset($hubids['118002']))
                {
                    $explodedata = explode(",", $hubids['118002']);
                        foreach ($explodedata as $key => $value) {
                            $sql  = DB::table("legalentity_warehouses")->where("le_wh_id", "=", $value)->get(array("lp_wh_name"))->all();
                            $warehousename = json_decode(json_encode($sql), true);
                            if(isset($warehousename[0]['lp_wh_name'])) {
                            $mainarray[$value] = $warehousename[0]['lp_wh_name'];
                            }
                        }
                }
                else if(isset($hubids['118001']) && !isset($hubids['118002']))
                {
                    $explode_warehouses = explode(",", $hubids['118001']);
                    
                    $sql_hubs = DB::table("legalentity_warehouses as lw")
                                    ->join("business_units as bu", "bu.bu_id", "=", "lw.bu_id")
                                    ->join("business_units as bu2", "bu2.parent_bu_id", "=", "bu.bu_id")
                                    ->join("legalentity_warehouses as lw2", "lw2.bu_id", "=", "bu2.bu_id")
                                    ->where("lw2.dc_type", "!=", "118001")
                                    ->whereIn("lw.le_wh_id", $explode_warehouses)
                                    ->groupBy("lw2.le_wh_id")
                                    ->pluck("lw2.lp_wh_name", "lw2.le_wh_id")->all();

                    $mainarray = $sql_hubs;
                    
                }

            
            return $mainarray;



            
            
        } catch (Exception $e) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

}

