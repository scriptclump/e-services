<?php
namespace App\Modules\OutFlowCycleReport\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Notifications;
use UserActivity;
use Utility;
use Log;

class OutFlowCycleModel extends Model {
    public function getGridData($filtered_data, $page, $pageSize)
    {
        try {
                $result = array();
                $resulted_array     =   array(
                                            "GO.order_code as SO_num",
                                            "GO.gds_order_id as order_id",
                                            "LE.business_legal_name as retailer_name",
                                            "GO.created_at as SO_Date",
                                            DB::raw("GetUserName(GO.created_by, 2) as created_By"),
                                            DB::raw("getOrderBeatName(GO.cust_le_id) as beat_name"),
                                            DB::raw("getOrderAreaName(GO.cust_le_id) as area_name"),
                                            DB::raw("getLeWhName(GO.hub_id) as hub_name"),
                                            DB::raw("getMastLookupValue(GOP.order_status) as order_status"),
                                            "GOP.sku as product_code",
                                            "GOP.pname as product_description",
                                            "GOP.mrp",
                                            DB::raw("sum(GOP.qty) as SO_Qty"),
                                            // "GO.total as SO_val",
                                            "GOP.cost as SO_val",
                                            "GOT.picked_date",
                                            DB::raw("sum(GSP.qty) as Picked_qty"),
                                            DB::raw("GetUserName(GOT.picker_id, 2) as picked_by")
                                        );
                $sql = DB::table("gds_orders as GO")
                            ->join("gds_order_track as GOT", "GO.gds_order_id", "=", "GOT.gds_order_id")
                            ->join("gds_order_products as GOP", "GO.gds_order_id", "=", "GOP.gds_order_id")
                            ->join("gds_ship_grid as GSG", "GO.gds_order_id", "=", "GSG.gds_order_id")
                            // ->leftJoin("gds_ship_products as GSP", "GSG.gds_ship_grid_id", "=", "GSP.gds_ship_grid_id")

                            ->leftJoin("gds_ship_products as GSP",function($join){
                            $join->on("GSG.gds_ship_grid_id","=", "GSP.gds_ship_grid_id")
                            ->on("GOP.product_id", "=", "GSP.product_id");
                            })
                            ->leftJoin("legal_entities as LE", "GO.cust_le_id", "=", "LE.legal_entity_id")
                  
                            ->groupBy("GO.gds_order_id")
                            ->groupBy("sku");

                if($filtered_data['date'])
                {
                    $sql = $sql
                            ->where("GO.created_at", ">=", date("Y-m-d", strtotime($filtered_data['date']))." 00:00:00")
                            ->where("GO.created_at", "<=", date("Y-m-d", strtotime($filtered_data['to_date']))." 23:59:59");
                }

                if($filtered_data['sales_officers'])
                {
                    $sql = $sql->whereIn("GO.created_by", $filtered_data['sales_officers']);
                }

                if($filtered_data['delivery_executives'])
                {
                    $sql = $sql->whereIn("GOT.delivered_by", $filtered_data['delivery_executives']);
                }

                if($filtered_data['retailers'])
                {
                    $sql = $sql->whereIn("GO.cust_le_id", $filtered_data['retailers']);
                }

                if($filtered_data['beat'])
                {
                    $sql = $sql->whereIn("GO.beat", $filtered_data['beat']);   
                }

                if($filtered_data['area'])
                {
                    // $sql = $sql->whereIn("GO.cust_le_id", $filtered_data['area']);
                    $sql = $sql->leftjoin("customers as cust", "cust.le_id", "=", "GO.cust_le_id")
                    ->leftjoin("cities_pincodes as CP", "CP.city_id", "=", "cust.area_id")
                    ->whereIn("city_id", $filtered_data['area']);
                    
                }

                $countarr = $sql->get(array("GO.gds_order_id"))->all();
                $count = count($countarr);

                $sql = $sql->skip($page * $pageSize)->take($pageSize);
                $sql = $sql->get($resulted_array)->all();
                // dd(DB::getQueryLog());
                $data  = json_decode(json_encode($sql), true);

                foreach ($data as $key => $value) {
                    $data[$key]['picked_Date'] = $this->getPickedDate($value['order_id']);
                }

                $result['result'] = $data;
                $result['count'] = $count;
                return $result;
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
        
    }

    public function getExport($filtered_data)
    {

            try {
                    $result = array();
                    $resulted_array     =   array(
                                            "GO.order_code as SO_num",
                                            "GO.gds_order_id as order_id",
                                            "LE.business_legal_name as retailer_name",
                                            "GO.created_at as SO_Date",
                                            DB::raw("GetUserName(GO.created_by, 2) as created_By"),
                                            DB::raw("getOrderBeatName(GO.cust_le_id) as beat_name"),
                                            DB::raw("getOrderAreaName(GO.cust_le_id) as area_name"),
                                            DB::raw("getLeWhName(GO.hub_id) as hub_name"),
                                            DB::raw("getMastLookupValue(GOP.order_status) as order_status"),
                                            "GOP.sku as product_code",
                                            "GOP.pname as product_description",
                                            "GOP.mrp",
                                            DB::raw("sum(GOP.qty) as SO_Qty"),
                                            // "GO.total as SO_val",
                                            "GOP.cost as SO_val",
                                            "GOT.picked_date",
                                            DB::raw("sum(GSP.qty) as Picked_qty"),
                                            DB::raw("GetUserName(GOT.picker_id, 2) as picked_by")
                                        );
                $sql = DB::table("gds_orders as GO")
                            ->join("gds_order_track as GOT", "GO.gds_order_id", "=", "GOT.gds_order_id")
                            ->join("gds_order_products as GOP", "GO.gds_order_id", "=", "GOP.gds_order_id")
                            ->join("gds_ship_grid as GSG", "GO.gds_order_id", "=", "GSG.gds_order_id")
                            // ->leftJoin("gds_ship_products as GSP", "GSG.gds_ship_grid_id", "=", "GSP.gds_ship_grid_id")

                            ->leftJoin("gds_ship_products as GSP",function($join){
                            $join->on("GSG.gds_ship_grid_id","=", "GSP.gds_ship_grid_id")
                            ->on("GOP.product_id", "=", "GSP.product_id");
                            })
                            ->leftJoin("legal_entities as LE", "GO.cust_le_id", "=", "LE.legal_entity_id")
                  
                            ->groupBy("GO.gds_order_id")
                            ->groupBy("sku");

                                // ->get($resulted_array);
                    if($filtered_data['date'])
                    {
                        $sql = $sql
                                ->where("GO.created_at", ">=", date("Y-m-d", strtotime($filtered_data['date']))." 00:00:00")
                                ->where("GO.created_at", "<=", date("Y-m-d", strtotime($filtered_data['to_date']))." 23:59:59");
                    }

                    if($filtered_data['sales_officers'])
                    {
                        $sql = $sql->whereIn("GO.created_by", $filtered_data['sales_officers']);
                    }

                    if($filtered_data['delivery_executives'])
                    {
                        $sql = $sql->whereIn("GOT.delivered_by", $filtered_data['delivery_executives']);
                    }

                    if($filtered_data['retailers'])
                    {
                        $sql = $sql->whereIn("GO.cust_le_id", $filtered_data['retailers']);
                    }

                    if($filtered_data['beat'])
                    {
                        $sql = $sql->whereIn("GO.beat", $filtered_data['beat']);   
                    }

                    if($filtered_data['area'])
                    {
                        // $sql = $sql->whereIn("GO.cust_le_id", $filtered_data['area']);
                        $sql = $sql->leftjoin("customers as cust", "cust.le_id", "=", "GO.cust_le_id")
                        ->leftjoin("cities_pincodes as CP", "CP.city_id", "=", "cust.area_id")
                        ->whereIn("city_id", $filtered_data['area']);
                        
                    }

                    $sql = $sql->get($resulted_array)->all();

                    $data  = json_decode(json_encode($sql), true);

                    foreach ($data as $key => $value) {
                        $data[$key]['picked_Date'] = $this->getPickedDate($value['order_id']);
                    }

                    return $data;
                    
            } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            }
            
        
    }

    public function getAllSalesOfficers()
    {
        try {

                $rolerepo = new RoleRepo();
                $mainArr = array();
                $getuserIdfor_delivary = DB::table("roles")->where("name", "=", "Field Force Associate")->get(array("role_id"))->all();
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
        
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function getAllDeliveryExecutivesforSO()
    {
        try {
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
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }


    public function getAllRetailersforSO()
    {
        try {
            // $sql =  DB::table("gds_orders as GO")
            //             ->join("legal_entities as LE", "GO.cust_le_id", "=", "LE.legal_entity_id")
            //             ->where("legal_entity_type_id", "=", 3001)
            //             ->groupBy("GO.cust_le_id")
            //             ->lists("business_legal_name as retailer_name", "LE.legal_entity_id as retailer_id");
            $sql = DB::table("legal_entities")
                        ->where("legal_entity_type_id", 'like', '3%')
                        ->pluck("business_legal_name as retailer_name", "legal_entity_id as retailer_id")->all();
            return $sql;
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }


    public function getAllBeatsforSO()
    {
        try {
                $sql =  DB::table("gds_orders as GO")
                        ->join("pjp_pincode_area as PPA","GO.beat", "=", "PPA.pjp_pincode_area_id")
                        ->groupBy("GO.beat")
                        ->pluck("pjp_name as beat_name", "GO.beat as beat_id")->all();
                return $sql;
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    // public function getAllAreasforSO()
    // {
    //     try {
    //         $sql = DB::table("gds_orders as GO ")
    //                     ->leftjoin("customers as cust", "cust.le_id", "=", "GO.cust_le_id")
    //                     ->leftjoin("cities_pincodes as CP", "CP.city_id", "=", "cust.area_id")
    //                     ->where("city_id", "!=", "")
    //                     ->groupBy("GO.cust_le_id")
    //                     ->lists(DB::raw("concat(officename,'-', pincode) as officename"), "cust_le_id");
    //         return $sql;
    //     } catch (\ErrorException $ex) {
    //             Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
    //     }
    // }

    public function getAllAreasforSO()
    {
        try {
                $sql = DB::table("gds_orders as GO ")
                        ->leftjoin("customers as cust", "cust.le_id", "=", "GO.cust_le_id")
                        ->leftjoin("cities_pincodes as CP", "CP.city_id", "=", "cust.area_id")
                        ->where("city_id", "!=", "")
                        ->groupBy("GO.cust_le_id")
                        ->pluck(DB::raw("distinct concat(officename,'-', pincode) as officename"), "city_id")->all();
                return $sql;
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function getPickedDate($orderId)
    {
        //getting picked date for a particular order based on the order Id and based on the Status(Ready To dispatch status is )
        try {
            
                $sql = DB::table("gds_orders_comments")
                        ->where("entity_id", "=", $orderId)
                        ->where("order_status_id", "=", 17005)
                        ->orderBy("comment_date", "desc")
                        ->limit(1)
                        ->get(array("comment_date"))->all();
                $data = json_decode(json_encode($sql), true);
                
                return isset($data[0]['comment_date'])?$data[0]['comment_date']:"";
        }  catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
}

