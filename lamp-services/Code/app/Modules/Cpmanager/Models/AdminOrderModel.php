<?php

namespace App\Modules\Cpmanager\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use views;
use view;
use Config;

class AdminOrderModel extends Model {

/*
  * Function Name: updateOrderStatus()
  * Description: Used to get update order status based on order_id
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 5 Jan 2017
  * Modified Date & Reason:
  */
    public function updateOrderStatus($orderId, $statusId,$adminid) {
        try{
         
            DB::table('gds_orders')
            ->whereRaw('FIND_IN_SET(gds_order_id,"'.$orderId.'")')
            ->update(array('order_status_id' => $statusId, 'updated_at'=>date('Y-m-d H:i:s'), 
                'updated_by'=>$adminid));

        }
        catch(Exception $e) {
            
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }
 /*
  * Function Name: updateStockTransfer()
  * Description: Used to get update order details in gds_order_track
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 5 Jan 2017
  * Modified Date & Reason:
  */
 public function updateStockTransfer($order_ids, $delivered_by, $de_name, $stock_delivered_mobile, $stock_driver_name, $stock_vehicle_number, $stock_driver_mobile, $docket_code, $adminid, $vehicleId) {
        try {
            $delivered_date = date('Y-m-d H:i:s');
            DB::table('gds_order_track')
                    ->whereRaw('FIND_IN_SET(gds_order_id,"' . $order_ids . '")')
                    ->update(array('st_del_ex_id' => $delivered_by,
                        'st_del_date' => $delivered_date,
                        'st_vehicle_no' => $stock_vehicle_number,
                        'st_driver_name' => $stock_driver_name,
                        'st_driver_mobile' => $stock_driver_mobile,
                        'st_docket_no' => $docket_code,
                        'vehicle_id' => $vehicleId));
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    /*
  * Function Name: getOrdersBasedOnDockets()
  * Description: Used to get docket number based on user_id and its hub
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 18 Jan 2017
  * Modified Date & Reason:
  */
    public function getOrdersBasedOnDockets($hub,$start_date,$end_date,$flag) {
        try{      
        $result = DB::table('gds_orders as go')
                ->select(DB::raw("got.st_docket_no,go.le_wh_id,getLeWhName(go.le_wh_id) as warehouse_name,
                    getLeWhName(go.hub_id) as hub_name,go.hub_id,COUNT(got.gds_order_id) as order_count,
                    SUM(got.bags_cnt) as bag_cnt,SUM(got.cfc_cnt) as cfc_cnt,SUM(got.crates_cnt) as crates_cnt"))
                ->Join('gds_order_track as got','go.gds_order_id','=','got.gds_order_id')
               ->whereRaw('FIND_IN_SET(go.hub_id,"'.$hub.'")')
               ->GROUPBY('got.st_docket_no');
        if($flag==1)
         {
           $result->whereIn("go.order_status_id",[17022,17023])
                  ->where("go.order_transit_status","=",17027);

         }else{ 

        $result->where("go.order_status_id",'=','17024');
      //        ->whereBetween(db::raw("DATE(got.st_del_date)"),array($start_date,$end_date));
         }

        return $result->get()->all(); 
          
        }
        catch(Exception $e) {
               return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
     }
    }
   /*
      * Function Name: checkLpToken()
      * Description: checkCustomerToken function is used to check if the lp_token passed when the customer is logged in is valid.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2017
      * Version: v1.0
      * Created Date: 7 Feb 2016
      * Modified Date & Reason:
    */
    public function checkLpToken($customer_token){

     // db::enableQueryLog();
      $query = DB::table("users as u")
      ->select(DB::raw("count(u.lp_token) as count")) 
      ->where("u.lp_token","=",$customer_token)   
      ->get()->all();
     // print_r(db::getquerylog());exit;
      return $query[0]->count;   
      
      
    }
   /*
      * Function Name: getLpUserId()
      * Description: getLpUserId function is used to get user_id based on token
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2017
      * Version: v1.0
      * Created Date: 7 Feb 2016
      * Modified Date & Reason:
    */


    public function getLpUserId($customer_token){
      $query = DB::table("users as u")
      ->select("user_id","firstname","lastname","legal_entity_id")
      ->where("u.lp_token","=",$customer_token)   
      ->get()->all();
      
      return $query;   
      
      
    }

    /*
  * Function Name: saveGeoDatas()
  * Description: Used to save tracking details
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 5 Jan 2017
  * Modified Date & Reason:
  */
    public function saveGeoDatas($data) {
        try{
         

          $geo= DB::table('geo_track')->insertGetId([
                                          'geo_type' => $data->geo_type,
                                          'user_id'=>$data->user_id,
                                          'latitude'=>$data->latitude,
                                          'longitude'=>$data->longitude,
                                           'route_id'=>$data->route_id,
                                           'heading'=>$data->heading,
                                           'accuracy'=>$data->accuracy,
                                           'speed'=>$data->speed,
                                           'reading'=>(isset($data->reading))?$data->reading:0,
                                          'created_at'=> date("Y-m-d H:i:s")
                                        ]);
          return $geo;

        }
        catch(Exception $e) {
               return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
     }
    }

        /*
  * Function Name: getGeoDatas()
  * Description: Used to save tracking details
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 5 Jan 2017
  * Modified Date & Reason:
  */
    public function getGeoDatas($data) {
    try{
         

          $data= DB::table("geo_track")
                  ->where("user_id","=",$data->user_id)   
                  //->where("user_id","=",$data->user_id)
                  ->where(db::raw(" DATE(created_at)"),$data->start_date) 
                  ->get()->all();

          return $data;

        }
        catch(Exception $e) {
            
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }
/*
  * Function Name: getReturnProductWithReason()
  * Description: Used to get orders with reasons
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 28 March 2017
  * Modified Date & Reason:
  */
    public function getReturnProductWithReason($data) {
    try{
          $data= DB::table("gds_return_grid as grg")
                 ->select(db::raw("grg.gds_order_id,grg.return_order_code,gr.product_id
                  ,getProductName(gr.product_id) as product_name,
                  gr.return_reason_id,getMastLookupValue(gr.return_reason_id) as reason
                  ,gr.qty,gds_prod.mrp,gr.approved_quantity,gr.quarantine_qty,IFNULL(gr.dit_qty,0)as dit_qty,
                  IFNULL(gr.dnd_qty,0)as dnd_qty,IFNULL(gr.excess_qty,0)as excess_qty"))
                 ->join('gds_returns as gr','grg.return_grid_id','=','gr.return_grid_id')
                // ->join('products as prod','prod.product_id','=','gr.product_id')
                  ->leftJoin('gds_order_products as gds_prod', function($join)
                {
                $join->on('gds_prod.product_id','=','gr.product_id');
                $join->on('gds_prod.gds_order_id','=','grg.gds_order_id');
                 })
                  ->where("grg.gds_order_id","=",$data->order_id)   
                  ->get()->all();
       $data=json_decode(json_encode($data),true);
        
        $i=0;
        foreach ($data as $key => $value) {
          

          $bin_code= DB::Table('warehouse_config as wc')
            ->join('bin_inventory as binv','wc.wh_loc_id','=','binv.bin_id')
            ->join('bin_type_dimensions as bin_dimension','wc.bin_type_dim_id','=','bin_dimension.bin_type_dim_id')
            ->select(db::raw("group_concat(wc.wh_location) as bin"))
            ->where('wc.pref_prod_id', $value['product_id'])
            ->where('bin_dimension.bin_type', 109003)
            //->where('binv.qty','>', 0)
            ->first();
            $bin_code=json_decode(json_encode($bin_code),true);
          $data[$i]['bin_code']=$bin_code['bin'];
     
        $i++;
        }
          return $data;

        }
        catch(Exception $e) {
            
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }
    /*
  * Function Name: getCancelProductWithReason
  * Description: Used to get orders with cancel reasons
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 28 March 2017
  * Modified Date & Reason:
  */
    public function getCancelProductWithReason($data) {
    try{
       
          $data= DB::table("gds_cancel_grid as gcg")
                 ->select(db::raw("gcg.gds_order_id,gcg.cancel_code,goc.product_id
                  ,getProductName(goc.product_id) as product_name,
                  IFNULL(goc.cancel_reason_id,0) AS cancel_reason_id ,IFNULL(getMastLookupValue(goc.cancel_reason_id),'') as reason
                  ,qty"))
                 ->join('gds_order_cancel as goc','gcg.cancel_grid_id','=','goc.cancel_grid_id')
                  ->where("gcg.gds_order_id","=",$data->order_id)   
                  ->get()->all();

          return $data;

        }
        catch(Exception $e) {
            
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }
        /*
  * Function Name: updateProgressFlag()
  * Description: Used to get update progress flag
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 5 Jan 2017
  * Modified Date & Reason:
  */
    public function updateProgressFlag($order_id,$updated_by) {
        try{
            
           $result= DB::table('gds_order_track')
                    ->where('gds_order_id','=',$order_id)
                    ->update(array('in_progress' => 1));


            return $result;
        }
        catch(Exception $e) {
               return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
     }
    }



     /*
  * Function Name: searchAllProducts()
  * Description: Used to get products based on keyword
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2018
  * Version: v1.0
  * Created Date: 16 June 2018
  * Modified Date & Reason:
  */
    public function searchAllProducts($keyword) {
        try{

           $result=  DB::table('products as prod')
         ->select('prod.product_id','prod.product_title as name')
        ->where('prod.product_title','LIKE','%'.$keyword.'%')      
        ->where('prod.cp_enabled','=',1)
        ->where('prod.is_sellable','=',1)
        ->get()->all();

            return $result;
        }
        catch(Exception $e) {
               return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
     }
    }

  /*
  * Function Name: checkProgressFlag()
  * Description: Used to get products based on keyword
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2018
  * Version: v1.0
  * Created Date: 16 June 2018
  * Modified Date & Reason:
  */
    public function checkProgressFlag($orderid) {
        try{

           $result=  DB::table('gds_order_track')   
                     ->where('gds_order_id','=',$orderid)
                     ->where('in_progress',1)
                     ->count();

            return $result;
        }
        catch(Exception $e) {
               return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
     }
    }
}
