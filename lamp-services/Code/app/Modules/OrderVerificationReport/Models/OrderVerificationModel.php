<?php

namespace App\Modules\OrderVerificationReport\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Notifications;
use UserActivity;
use Utility;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Tax\Models\Product;
use Log;

class OrderVerificationModel extends Model {

    public function getOrderVerificationData($filters ="", $page = "", $pageSize = "", $orderby = "") {
        try {
            
            $fields = array("GO.order_code",
                "PCM.container_barcode as crate_num",
                "PCM.product_barcode as sku",
                // DB::raw("getLeWhName(PCM.le_wh_id) as dcname"),
                DB::raw("ledc.lp_wh_name AS dcname"),
                "GO.order_date",
                // DB::raw("GetUserName(PCM.picked_by, 2) as pickername"),
                DB::raw("CONCAT(picus.firstname,' ',IFNULL(picus.lastname,'')) AS pickername"),
                "PCM.created_at as picking_time",
                // DB::raw("GetUserName(PCM.verified_by, 2) as verifier_name"),
                DB::raw("CONCAT(vrfus.firstname,' ',IFNULL(vrfus.lastname,'')) AS verifier_name"),
                "PCM.wrong_picked_qty as wrong_qty",
                "ML.master_lookup_name as reason",
                DB::raw("IF(PCM.is_verified = 1, 'No', 'Yes') as verification_status"),
                DB::raw("IF(PCM.is_verified = 1, '', PCM.verified_at) as verification_time"),
                // DB::raw("getProductName(productid) as product_title"),
                DB::raw("p.product_title AS product_title"),
                // DB::raw("getLeWhName(GO.hub_id) as hub_name")
                DB::raw("lehub.lp_wh_name AS hub_name"),
                //get filepath
                DB::raw("ovf.file_path AS file_path")
            );
            $sql = DB::table("gds_orders as GO")
                    ->join("picker_container_mapping as PCM", "GO.gds_order_id", "=", "PCM.order_id")
                    ->leftjoin('order_verification_files as ovf', function($join)
                    {
                        $join->on('ovf.order_id','=','PCM.order_id');
                        $join->on('ovf.container_name','=','PCM.container_barcode');
                    })
                    ->leftJoin("master_lookup as ML", "PCM.wrong_picked_reason", "=", "ML.value")
                    ->join("products AS p", "p.product_id", "=", "PCM.productid")
                    ->leftJoin("users AS picus", "picus.user_id", "=", "PCM.picked_by")
                    ->leftJoin("users AS vrfus", "vrfus.user_id", "=", "PCM.verified_by")
                    ->join("legalentity_warehouses AS lehub", "lehub.le_wh_id", "=", "GO.hub_id")
                    ->join("legalentity_warehouses AS ledc", "ledc.le_wh_id", "=", "PCM.le_wh_id")
                    // ->join("master_lookup AS ML", "PCM.wrong_picked_reason", "=", "ML.value")
                    // ->whereBetween("GO.order_date", array($start_date, $end_date))
                    ->orderBy("GO.order_date", "desc")
                    ->where("PCM.container_barcode", "!=", "");
            
            if(!empty($filters)){
                if(!empty($filters['from_date']) && !empty($filters['to_date']))
                {
                    $sql = $sql->whereBetween("PCM.verified_at", array($filters['from_date'], $filters['to_date']." 23:59:59"));
                }

                if(!empty($filters['crate_number']))
                {
                    $sql = $sql->whereIn("PCM.container_barcode", $filters['crate_number']);
                }

                if(!empty($filters['order_code']))
                {
                    $sql = $sql->where("GO.order_code", $filters['order_code']);
                }

                if(!empty($filters['prod_sku']))
                {
                    $sql = $sql->whereIn("PCM.product_barcode", $filters['prod_sku']);
                }

                if(!empty($filters['dc_names']))
                {
                    $sql = $sql->whereIn("PCM.le_wh_id", $filters['dc_names']);
                }

                if(!empty($filters['hubs']))
                {
                    $sql = $sql->whereIn("GO.hub_id", $filters['hubs']);
                }

                if(!empty($filters['picker_names']))
                {
                    $sql = $sql->whereIn("PCM.picked_by", $filters['picker_names']);
                }

                if(!empty($filters['verified_by']))
                {
                    $sql = $sql->whereIn("PCM.verified_by", $filters['verified_by']);
                }

                if(strlen($filters['verification_status']) != 0)
                {
                    $sql = $sql->where("PCM.is_verified", $filters['verification_status']);
                }

                if(!empty($filters['product_title']))
                {
                    $sql = $sql->whereIn("PCM.productid", $filters['product_title']);
                }
            } else{
                $sql = $sql->whereBetween("PCM.verified_at", array(date('Y-m-d 00:00:00', strtotime("-1 day")), date('Y-m-d 23:59:59', strtotime("now"))));
            }

            if (!empty($orderby)) {
                $orderClause = explode(" ", $orderby);
                $sql = $sql->orderby($orderClause[0], $orderClause[1]);  //order by query
            }

            if ($page != '' && $pageSize != '') {
                $sql_res["count"] = $sql->count();
                $sql = $sql->skip($page * $pageSize)->take($pageSize);
            }
            $sql_res["result"] = $sql->get($fields)->all();
            return json_decode(json_encode($sql_res), true);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function orderCodes($searchterm)
    {
        try {
                $sql = DB::table("gds_orders")
                        ->where('order_code', 'like', '%'.$searchterm.'%')
                        ->pluck("order_code")->all();
                return $sql;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function getAllCrateCodes()
    {
        try {
                $sql = DB::table("picker_container_mapping")->pluck("container_barcode")->all();
                return $sql;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function filterOptions() {
        $filter_array                       = Array();
        $warehouses_table                   = DB::table('legalentity_warehouses');
        $products_table                     = DB::table('products');
        $product_titles                     = DB::table("products");
        
        $roleOb                             = new Role();
        $rbac_manfacturer_name              = json_decode($roleOb->getFilterData(11, Session::get('userId')), true);
        $filter_array['manfacturer_name']   = $rbac_manfacturer_name['manufacturer'];
        
        if (Session('roleId') != '1') {
            $warehouses_table               = $warehouses_table->where('legal_entity_id', Session::get('legal_entity_id'));
            $products_table                 = $products_table->where('legal_entity_id', Session::get('legal_entity_id'));
            $product_titles                 = $product_titles->where('legal_entity_id', Session::get('legal_entity_id'));
            
        }

        $filter_array['dc_name']            = $warehouses_table->where('lp_wh_name', '!=', NULL)->where('lp_wh_name', '!=', '')->where('dc_type', '=', '118001')->orderBy('lp_wh_name', 'asc')->pluck('lp_wh_name', 'le_wh_id')->all();
        $filter_array['product_titles']     = $product_titles->pluck('product_title', 'product_id')->all();
        $filter_array['sku']                = DB::table("vw_inventory_report")->distinct('sku')->where('le_wh_id', '!=', NULL)->where('le_wh_id', '!=', '')->orderBy('sku', 'asc')->pluck('sku')->all();
        $filter_array['all_crates']         = DB::table("picker_container_mapping")->where("container_barcode", "!=", "")->where("container_barcode", "!=", NULL)->groupBy("container_barcode")->pluck("container_barcode")->all();
        $filter_array['hub_names']          = DB::table("gds_orders")->where("hub_id", "!=", 0)->where("hub_id", "!=", "")->pluck(DB::raw("getLeWhName(hub_id) as hub_name"), "hub_id")->all();
        $filter_array['all_pickers']        = $this->getUsers();
        $filter_array['verified_users']     = DB::table("picker_container_mapping")->groupBy("verified_by")->pluck( DB::raw("GetUserName(verified_by, 2) as verifier_name"), "verified_by")->all();
        $filter_array['verification_status'] = DB::table("picker_container_mapping")->pluck(DB::raw("IF(is_verified = 1, 'No', 'Yes') as verification_status"), 'is_verified')->all();

        return $filter_array;
    }

    public function getUsers()
    {
        try {
                $sql = DB::table("users as UU")
                        ->join("user_roles as UR", "UR.user_id", "=", "UU.user_id")
                        ->where("legal_entity_id","=", 2)
                        ->where("UR.role_id", "=",56)   // 56 is picker role Id
                        ->pluck(DB::raw("concat(firstname,' ', lastname) as username"), "UU.user_id")->all();
                return $sql;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function getOrderVerificationSummary($from_date, $to_date)
    {
        try {

                $fieldsArr = array(
                            DB::raw("GetUserName(PCM.verified_by, 2) as verifier_name"),
                            DB::raw("count(DISTINCT(PCM.order_id)) as total_verified_orders"),
                            DB::raw("count(PCM.productid) as total_verified_skus"),
                            DB::raw("count(DISTINCT(PCM.container_barcode)) as total_verified_crate"),
                            DB::raw("SUM(CASE PCM.wrong_picked_reason WHEN 138002 THEN wrong_picked_qty ELSE 0 END) AS short_qty"),
                            DB::raw("SUM(CASE PCM.wrong_picked_reason WHEN 138001 THEN wrong_picked_qty ELSE 0 END) AS excess_qty"),
                            DB::raw("COUNT(DISTINCT CASE PCM.wrong_picked_reason WHEN 138002 THEN GO.gds_order_id END) AS short_orders"),
                            DB::raw("COUNT(DISTINCT CASE PCM.wrong_picked_reason WHEN 138001 THEN GO.gds_order_id  END) AS excess_Orders")
                            
                            // DB::raw("(select sum(wrong_picked_qty) from picker_container_mapping where wrong_picked_reason = 138002 and verified_by = PCM.verified_by) as short_qty"),
                            // DB::raw("(select sum(wrong_picked_qty) from picker_container_mapping where wrong_picked_reason = 138001 and verified_by = PCM.verified_by) as excess_qty")

                            );
                $sql = DB::table("picker_container_mapping as PCM")
                            ->join("gds_orders as GO", "GO.gds_order_id", "=", "PCM.order_id")
                            ->where("PCM.verified_by", "!=", "")
                            ->whereBetween("PCM.verified_at", array($from_date, $to_date." 23:59:59"))
                            ->groupBy("verified_by")
                            ->get($fieldsArr)->all();
                $data = json_decode(json_encode($sql), true);
                return $data;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

}
