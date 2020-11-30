<?php
namespace App\Modules\PickerEfficiencyReport\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Notifications;
use UserActivity;
use Utility;
use Log;
// use App\Modules\Roles\Models\Role;

class PickerEfficiencyModel extends Model {
    public function getGridData($filtered_data, $page, $pageSize)
    {
        try {
                DB::enableQueryLog();
                $role = new Role();
                date_default_timezone_set('UTC');
                
                $data = json_decode($role->getFilterData(6), true);
                $hubids = json_decode($data['sbu'], true);

                //getting all hubs
                $hubs_array = array();
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
                
                
                $result         = array();
                $mainData1      = array();
                $del_temp_arr   = "";
                $resulted_array = array(
                                   "go.gds_order_id as OrderId",
                                   "go.order_date",
                                   "got.delivery_date",
                                   // "gsg.created_at as picked_date",
                                   DB::raw("GetUserName(got.picker_id, 2) as picked_By"),
                                   "go.order_code as order_num",
                                    // DB::raw("sum(gop.qty) as order_qty"),
                                   DB::raw("(select sum(qty)  from gds_order_products where gds_order_id = go.gds_order_id ) as order_qty "),
                                    // "got.scheduled_piceker_date",
                                    "go.total as order_val",
                                    "gsg.created_at as picked_date",
                                    DB::raw("getLeWhName(go.hub_id) as hub_name"),
                                    DB::raw("count(gop.gds_order_prod_id) as skus_order"),
                                    // DB::raw("sum(gsp.qty) as picked_qty"),
                                    DB::raw("(select sum(qty) from gds_ship_products where gds_ship_grid_id  = gsg.gds_ship_grid_id) AS picked_qty"),
                                    DB::raw("getOrderAreaName(go.cust_le_id) as area_name"),
                                    DB::raw('((select sum(qty) from gds_ship_products where gds_ship_grid_id  = gsg.gds_ship_grid_id)*100)/ (SELECT SUM(qty) FROM gds_order_products WHERE gds_order_id = go.gds_order_id) AS order_fill_rate'),
                                    DB::raw('NULL as complition_time'),
                                    "got.picking_start_time"
                                    // concat((sum("GSP.qty")*100)/sum("GOP.qty"), " %") as order_fill_rate

                                );
                $sql        = DB::table("gds_orders as go")
                                ->leftJoin("gds_ship_grid as gsg", "go.gds_order_id", "=", "gsg.gds_order_id")
                                ->leftJoin("gds_order_track as got", "go.gds_order_id", "=", "got.gds_order_id")
                                ->leftJoin("gds_order_products as gop", "go.gds_order_id", "=", "gop.gds_order_id")
                                // ->leftJoin("gds_orders_comments as GOC", "go.gds_order_id", "=", "GOC.entity_id")
                                // ->leftJoin("gds_ship_products as gsp", "gsp.gds_ship_grid_id", "=", "gsg.gds_ship_grid_id")

                                ->join("gds_ship_products as gsp",function($join){
                                            $join->on("gsp.gds_ship_grid_id", "=", "gsg.gds_ship_grid_id")
                                            ->on("gsp.product_id", "=", "gop.product_id");
                                })
                                // ->join("gds_orders_comments as GOC", "go.gds_order_id", "=", "GOC.entity_id")
                                ->whereIn("go.hub_id", $explodedata_hubids)
                                ->orderBy("gsg.created_at", "desc")
                                // ->where("go.order_status_id", "=", 17020)
                                ->groupBy("go.gds_order_id");

                                // dd($sql->toSql());
                  
                
                if($filtered_data['date'])
                {
                    $sql = $sql->where("gsg.created_at", ">=", date("Y-m-d", strtotime($filtered_data['date']))." 00:00:00");
                }


                if($filtered_data['to_date'])
                {
                    $sql = $sql->where("gsg.created_at", "<=", date("Y-m-d", strtotime($filtered_data['to_date']))." 23:59:59");
                }
                
                if($filtered_data['del_users'])
                {
                    $sql = $sql->whereIn("got.picker_id", $filtered_data['del_users']);
                }

                if($filtered_data['hubs'])
                {
                    $sql = $sql->whereIn("go.hub_id", $filtered_data['hubs']);
                }

                $countarr       = $sql->get(array("go.gds_order_id"))->all();
                $count          = count($countarr);

                $sql            = $sql->skip($page * $pageSize)->take($pageSize);
                $sql            = $sql->get($resulted_array)->all();

                
                // dd(DB::getQueryLog());
                $data           = json_decode(json_encode($sql), true);
                
                $cancel_Info    = $this->getCancelledData();
                // echo "<pre>";print_r($cancel_Info);die;

                foreach ($data as $key => $value) {
                    $assigned_date= $this->getPickedDate($value['OrderId'], 17020);  //assigned time
                    // $picked_date = $this->getAssignDate($value['OrderId'], 17005);  //call for picked date
                    $picked_date = $value['picked_date'];
                   
                    // if($picked_date == "")
                    // {
                    //     $value['picked_date'] = "";
                    // }else
                    // {
                    //     $value['picked_date'] = date("m-d-Y", strtotime($picked_date));
                    // }
                    $value['Picking_Start_Time'] = date('H:i:s', strtotime($value['picking_start_time']));
                    $value['picked_date'] = date('d-m-Y', strtotime($value['picked_date']));
                    $value['scheduled_piceker_date'] = date("d-m-Y H:i:s", strtotime($assigned_date));
                    // $value['duration'] = date("H:i:s",strtotime($picked_date)-strtotime($assigned_date));
                    $value['complition_Time'] = date("H:i:s",strtotime($picked_date));
                    

                    $value['duration'] = date("H:i:s", strtotime($value['complition_Time'])-strtotime(date("H:i:s",strtotime(isset($value['picking_start_time'])?$value['picking_start_time']:0 ))));


                    // $value['duration'] = date("H:i:s",($value['complition_Time'] - date("H:i:s",strtotime($value['picking_start_time']))));
                    $value['order_fill_rate'] = round($value['order_fill_rate'], 2);

                    
                    if(array_key_exists($value['OrderId'], $cancel_Info))
                    {
                        $mainData1[] = array_merge($value, $cancel_Info[$value['OrderId']]);
                    }
                    else
                    {
                        // $value['delivered_qty'] = $value['inv_qty'];
                        $del_temp_arr   = array("cancelled_qty" => "", "comment" => "");
                        $mainData1[]    = array_merge($value, $del_temp_arr);
                    }

                }
                $result['result']   = $mainData1;
                $result['count']    = $count;
                return $result;
            
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
                
                $result         = array();
                $mainData1      = array();
                $del_temp_arr   = "";
                $resulted_array = array(
                                   "go.gds_order_id as OrderId",
                                   "go.order_date",
                                   "got.delivery_date",
                                   // "gsg.created_at as picked_date",
                                   DB::raw("GetUserName(got.picker_id, 2) as picked_By"),
                                   "go.order_code as order_num",
                                    // DB::raw("sum(gop.qty) as order_qty"),
                                   DB::raw("(select sum(qty)  from gds_order_products where gds_order_id = go.gds_order_id ) as order_qty "),
                                    // "got.scheduled_piceker_date",
                                    "go.total as order_val",
                                    "gsg.created_at as picked_date",
                                    DB::raw("getLeWhName(go.hub_id) as hub_name"),
                                    DB::raw("count(gop.gds_order_prod_id) as skus_order"),
                                    // DB::raw("sum(gsp.qty) as picked_qty"),
                                    DB::raw("(select sum(qty) from gds_ship_products where gds_ship_grid_id  = gsg.gds_ship_grid_id) AS picked_qty"),
                                    DB::raw("getOrderAreaName(go.cust_le_id) as area_name"),
                                    DB::raw('((select sum(qty) from gds_ship_products where gds_ship_grid_id  = gsg.gds_ship_grid_id)*100)/ (SELECT SUM(qty) FROM gds_order_products WHERE gds_order_id = go.gds_order_id) AS order_fill_rate'),
                                    DB::raw('NULL as complition_time'),
                                    "got.picking_start_time"
                                    // concat((sum("GSP.qty")*100)/sum("GOP.qty"), " %") as order_fill_rate

                                );
                $sql        = DB::table("gds_orders as go")
                                ->leftJoin("gds_ship_grid as gsg", "go.gds_order_id", "=", "gsg.gds_order_id")
                                ->leftJoin("gds_order_track as got", "go.gds_order_id", "=", "got.gds_order_id")
                                ->leftJoin("gds_order_products as gop", "go.gds_order_id", "=", "gop.gds_order_id")
                                // ->leftJoin("gds_orders_comments as GOC", "go.gds_order_id", "=", "GOC.entity_id")
                                // ->leftJoin("gds_ship_products as gsp", "gsp.gds_ship_grid_id", "=", "gsg.gds_ship_grid_id")

                                ->join("gds_ship_products as gsp",function($join){
                                            $join->on("gsp.gds_ship_grid_id", "=", "gsg.gds_ship_grid_id")
                                            ->on("gsp.product_id", "=", "gop.product_id");
                                })
                                // ->join("gds_orders_comments as GOC", "go.gds_order_id", "=", "GOC.entity_id")
                                ->whereIn("go.hub_id", $explodedata_hubids)
                                ->orderBy("gsg.created_at", "desc")
                                // ->where("go.order_status_id", "=", 17020)
                                ->groupBy("go.gds_order_id");

                if($filtered_data['date'])
                {
                    $sql = $sql->where("gsg.created_at", ">=", date("Y-m-d", strtotime($filtered_data['date']))." 00:00:00");
                }


                if($filtered_data['to_date'])
                {
                    $sql = $sql->where("gsg.created_at", "<=", date("Y-m-d", strtotime($filtered_data['to_date']))." 23:59:59");
                }
                
                if($filtered_data['del_users'])
                {
                    $sql = $sql->whereIn("got.picker_id", $filtered_data['del_users']);
                }

                if($filtered_data['hubs'])
                {
                    $sql = $sql->whereIn("go.hub_id", $filtered_data['hubs']);
                }

                $sql = $sql->get($resulted_array)->all();


                $data           = json_decode(json_encode($sql), true);
                
                $cancel_Info    = $this->getCancelledData();

                foreach ($data as $key => $value) {
                    $assigned_date= $this->getPickedDate($value['OrderId'], 17020);  //assigned time
                    // $picked_date = $this->getAssignDate($value['OrderId'], 17005);  //call for picked date
                    $picked_date = $value['picked_date'];
                   
                    // if($picked_date == "")
                    // {
                    //     $value['picked_date'] = "";
                    // }else
                    // {
                    //     $value['picked_date'] = date("m-d-Y", strtotime($picked_date));
                    // }
                    $value['Picking_Start_Time'] = date('H:i:s', strtotime($value['picking_start_time']));
                    $value['picked_date'] = date('d-m-Y', strtotime($value['picked_date']));
                    $value['scheduled_piceker_date'] = date("d-m-Y H:i:s", strtotime($assigned_date));
                    // $value['duration'] = date("H:i:s",strtotime($picked_date)-strtotime($assigned_date));
                    $value['complition_Time'] = date("H:i:s",strtotime($picked_date));
                    

                    $value['duration'] = date("H:i:s", strtotime($value['complition_Time'])-strtotime(date("H:i:s",strtotime(isset($value['picking_start_time'])?$value['picking_start_time']:0 ))));


                    // $value['duration'] = date("H:i:s",($value['complition_Time'] - date("H:i:s",strtotime($value['picking_start_time']))));
                    $value['order_fill_rate'] = round($value['order_fill_rate'], 2);

                    
                    if(array_key_exists($value['OrderId'], $cancel_Info))
                    {
                        $mainData1[] = array_merge($value, $cancel_Info[$value['OrderId']]);
                    }
                    else
                    {
                        // $value['delivered_qty'] = $value['inv_qty'];
                        $del_temp_arr   = array("cancelled_qty" => "", "comment" => "");
                        $mainData1[]    = array_merge($value, $del_temp_arr);
                    }

                }
                return $mainData1;
            
        } catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
                Log::error($ex->getTraceAsString());
        }
        
    
}

    public function getCancelledData()
    {
        try {

                $final_arr      = "";
                $resulted_arr   = array("GO.gds_order_id",
                                    DB::raw("sum(GOC.qty) as cancelled_qty"), 
                                    "GCG.cancel_status_id",
                                    "GOC.cancel_reason_id",
                                    DB::raw("IF(GOC.cancel_reason_id IS NULL or GOC.cancel_reason_id = '', 'Auto Cancelled', ML.master_lookup_name) as cancel_reason")
                                    // DB::raw("(select comment  from gds_orders_comments as GC 
                                    // where GC.entity_id = GO.gds_order_id
                                    // order by GC.comment_date desc 
                                    // limit 1 ) as comment")
                                    // DB::raw("(select concat(getMastLookupValue(GC.order_status_id), ' : ', GC.comment)  from gds_orders_comments as GC 
                                    // where GC.entity_id = GO.gds_order_id
                                    // order by GC.comment_date desc 
                                    // limit 1 ) as comment")
                                );
                $sql            = DB::table("gds_orders as GO")
                                    ->join("gds_cancel_grid as GCG", "GO.gds_order_id", "=", "GCG.gds_order_id")
                                    ->join("gds_order_cancel as GOC", "GCG.cancel_grid_id", "=", "GOC.cancel_grid_id")
                                    ->leftJoin("master_lookup as ML", "GOC.cancel_reason_id", "=", "ML.value")
                                    ->orderBy("ML.master_lookup_name", "desc")
                                    // ->limit(1)
                                    ->select($resulted_arr)->groupBy("GO.gds_order_id")->get()->all();
                        
                        $mainData = json_decode(json_encode($sql), true);
                        foreach ($mainData as $key => $value) {
                            $explodeData = explode(",", $value['cancel_reason']);
                            if(sizeof($explodeData) >=1 )
                            {
                                $unique_status_data = array_unique($explodeData);
                            }
                            $implode_data = implode(",", $unique_status_data);
                            $final_arr[$value['gds_order_id']] = array("cancelled_qty" => $value['cancelled_qty'], "comment" => $implode_data);
                            // $final_arr[$value['gds_order_id']] = array("cancelled_qty" => $value['cancelled_qty'], "comment" => $value['cancel_reason']);
                            // if($value['cancel_status_id'] != 17015)
                            // {
                            //     $final_arr[$value['gds_order_id']] = array("cancelled_qty" => $value['cancelled_qty'], "comment" => $value['comment']);
                            // }else
                            // {
                            //     $final_arr[$value['gds_order_id']] = array("cancelled_qty" => $value['cancelled_qty'], "comment" => "CANCELLED BY EBUTOR");
                            // }
                                
                        }
                return $final_arr;
    
            
        } catch (Exception $e) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function getAllPickersOld()
    {
        try {
                $delivery_arr   = array();
                $sql            = DB::table("gds_order_track")
                                    ->where("picker_id", "!=", NULL)
                                    ->where("picker_id", "!=", 0)
                                    ->groupBy("picker_id")
                                    ->get(array(DB::raw("GetUserName(picker_id, 2) as picker_name"), "picker_id"))->all();
                $returnData     = json_decode(json_encode($sql), true);
                foreach ($returnData as $key => $value) {
                        if(!array_key_exists($value['picker_id'], $delivery_arr))
                        {
                            $delivery_arr[$value['picker_id']] = $value['picker_name'];
                        }

                }
                return $delivery_arr;
         } catch (Exception $e) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }


        /*Get All Delivery Persons*/
    public function getAllPickers()
    {
        try {
                $rolerepo               = new RoleRepo();
                $mainArr                = array();
                $getuserIdfor_delivary  = DB::table("roles")->where("name", "=", "Picker")->get(array("role_id"))->all();
                $data                   = json_decode(json_encode($getuserIdfor_delivary), true);
                $role_id                = $data[0]['role_id'];

                $getuserIds             = $rolerepo->getRoleById($role_id);
                $getuserIds             = json_decode(json_encode($getuserIds), true);
                
                $alluserIDS             = $getuserIds['user_id'];

                $resulted_array         = array(
                                            DB::raw("concat(firstname,' ',lastname) as name"),
                                            "user_id" 
                                            );
                $userdetails            = DB::table("users")->whereIn("user_id", $alluserIDS)->get($resulted_array)->all();
                $userdetails            = json_decode(json_encode($userdetails), true);
                
                foreach ($userdetails as $value) {
                    $mainArr[$value['user_id']] = $value['name'];
                }
                return $mainArr;
            
        } catch (Exception $e) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }

    }

    public function getAssignDate($order_id, $status_Id)
    {
        try {
            $sql = DB::table("gds_orders_comments")
                        ->where("entity_id", "=", $order_id)
                        ->where("order_status_id", "=", $status_Id)
                        ->orderBy("comment_date", "desc")
                        ->limit(1)
                        ->get(array("comment_date"))->all();

                        // echo "<pre>";print_r($sql);die;

            $data = json_decode(json_encode($sql), true);

            $date = isset($data[0]['comment_date'])?$data[0]['comment_date']:"";
            return $date;

        } catch (Exception $e) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function getPickedDate($order_id, $status_Id)
    {
        // echo $order_id ." ".$status_Id."<br>";
        try {
            $sql = DB::table("gds_orders_comments")
                        ->where("entity_id", "=", $order_id)
                        ->where("order_status_id", "=", $status_Id)
                        ->orderBy("comment_date", "desc")
                        ->limit(1)
                        ->get(array("comment_date"))->all();
                        // dd($sql->toSql());

                        // echo "<pre>";print_r($sql);die;

            $data = json_decode(json_encode($sql), true);

            $date = (!empty($data[0]['comment_date'])?$data[0]['comment_date']:"");
            return $date;

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
            //         ->lists("lp_wh_name", "le_wh_id");
            $hubids = json_decode($data['sbu'], true);
            $hubs_array = array();
                if(isset($hubids['118002']))
                {
                    $explodedata = explode(",", $hubids['118002']);
                        foreach ($explodedata as $key => $value) {
                            $sql  = DB::table("legalentity_warehouses")->where("le_wh_id", "=", $value)->get(array("lp_wh_name"))->all();
                            $warehousename = json_decode(json_encode($sql), true);
                            $mainarray[$value] = $warehousename[0]['lp_wh_name'];
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

    public function crateUtilization($start, $end){
        //calculate picked item weight according to pack types
        
        $sql = DB::select(DB::raw("select le_wh_id, order_id, created_at, container_barcode, productid, qty from picker_container_mapping where created_at>= :start and created_at <= :end and container_barcode is not null"), array("start"=>$start, "end"=>$end));

        $sql = json_decode(json_encode($sql), true);

        //print_r($sql);

        $packArr = array();
        foreach($sql as $each){
            $pickedQty = $each['qty'];
            $sqlPackConfig = DB::select(DB::raw("select product_id, level, no_of_eaches, lbh_uom, length, breadth, height
                    from product_pack_config
                    where product_id = :prod and is_cratable=1 order by no_of_eaches desc"),array("prod"=>$each['productid']));
            $sqlPackConfig = json_decode(json_encode($sqlPackConfig), true);
            
            foreach($sqlPackConfig as $pack){
              if($pack['no_of_eaches']>0 && $pickedQty>0 && $pack['no_of_eaches']<=$pickedQty) {
                $packArr[$each['order_id']][$each['container_barcode']][] = array(
                              "packLevel"=>$pack['level'],
                              "packCount"=>floor($pickedQty / $pack['no_of_eaches']),
                              "packLBH"=> $pack['length']*$pack['breadth']*$pack['height'],
                              "Order_date" => $each['created_at']
                            );
                $pickedQty = ($pickedQty % $pack['no_of_eaches']);
              }
            }

            
        }

        $final = array();
        foreach($packArr as $odr=>$packOdr){
            foreach($packOdr as $crt=>$packProd){
                // echo "<pre>";print_r($packProd);die;
                $temp = array();
                $temp['Crate Code'] = $crt;
                $temp['Order ID'] = $odr;
                $temp['Order Date'] = $packProd[0]['Order_date'];
                $temp['Crate LBH'] = 56.5*36.5*23.5;
                
                $totLBH = 0;
                foreach($packProd as $prodArr){
                    $totLBH += $prodArr['packLBH']*$prodArr['packCount'];
                }
                $temp['Total Product LBH'] = $totLBH;
                $temp['Utilization %'] = round(($totLBH*100)/$temp['Crate LBH'], 2)."%";
                $final[] = $temp;
            }
        }
        return $final;

    }

    public function getPickerConsolidatedReport($from_date, $to_date)
    {

        try {
            
            $from_date = date("Y-m-d ", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));
            // echo "hiii".$from_date." ".$to_date;die;
                // $fieldsArr = array(
                //             DB::raw("GetUserName(PCM.picked_by, 2) as verifier_name"),
                //             DB::raw("count(DISTINCT(PCM.order_id)) as total_verified_orders"),
                //             DB::raw("count(PCM.productid) as total_verified_skus"),
                //             DB::raw("SUM(CASE PCM.wrong_picked_reason WHEN 138002 THEN wrong_picked_qty ELSE 0 END) AS short_qty"),
                //             DB::raw("SUM(CASE PCM.wrong_picked_reason WHEN 138001 THEN wrong_picked_qty ELSE 0 END) AS excess_qty"),
                //             DB::raw("COUNT(DISTINCT CASE PCM.wrong_picked_reason WHEN 138002 THEN GO.gds_order_id END) AS short_orders"),
                //             DB::raw("COUNT(DISTINCT CASE PCM.wrong_picked_reason WHEN 138001 THEN GO.gds_order_id  END) AS excess_Orders"),
                //             DB::raw("SUM(CASE PCM.wrong_picked_reason WHEN 138004 THEN wrong_picked_qty ELSE 0 END) AS wrongt_qty"),
                            
                //             // DB::raw("(select sum(wrong_picked_qty) from picker_container_mapping where wrong_picked_reason = 138002 and verified_by = PCM.verified_by) as short_qty"),
                //             // DB::raw("(select sum(wrong_picked_qty) from picker_container_mapping where wrong_picked_reason = 138001 and verified_by = PCM.verified_by) as excess_qty")

                //             );
                // $sql = DB::table("picker_container_mapping as PCM")
                //             ->join("gds_orders as GO", "GO.gds_order_id", "=", "PCM.order_id")
                //             // ->where("PCM.verified_by", "!=", "") 
                //             ->whereBetween("GO.order_date", array($from_date, $to_date." 23:59:59"))
                //             ->groupBy("picked_by")
                //             ->get($fieldsArr);


                $sql = DB::selectFromWriteConnection(DB::raw("CALL getPickerListByDt('".$from_date."', '".$to_date."')"));
                $data = json_decode(json_encode($sql), true);

                return $data;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    
    }
}

