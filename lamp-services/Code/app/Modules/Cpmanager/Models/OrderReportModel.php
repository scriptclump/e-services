<?php

namespace App\Modules\Cpmanager\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use App\Modules\Cpmanager\Models\CategoryModel;
use DB;
use views;
use view;
use Config;

class OrderReportModel extends Model {

    public function getOrdersByStatus($status, $fdate, $tdate, $offset, $perpage,$columns,$userId) {
        try {


           /* $categoryModel= new CategoryModel();     
            $userData = $categoryModel->getUserId($columns['user_token']);

            if(is_array($userData) && isset($userData[0])) {
                $userId = $userData[0]->user_id;
            }  */  

            $roleModel = new Role();

            $Json = json_decode($roleModel->getFilterData(6,$userId),1);
            $Json = json_decode($Json['sbu'],1);


            $selectArr = [
                'gds_orders.gds_order_id',
                'gds_orders.order_code',
                DB::raw('getLeWhName(gds_orders.hub_id) as hub_name'),
                DB::raw('getOrderBeatName(gds_orders.cust_le_id) as beat'),
                'gds_orders.total as order_val',
                DB::raw('getMastLookupValue(gds_orders.pref_slab1) as del_slot'),
                'gds_orders.shop_name',
                'le.address1 as retailer_address',
                'gds_orders.order_date',
                'track.cfc_cnt',
                'track.bags_cnt',
                'track.crates_cnt',
                'invgrid.gds_invoice_grid_id as invoice_order_no',
                'rgrid.return_grid_id as return_id',
                DB::raw('GetUserName(track.delivered_by,2) as de_name'),
                DB::raw('GetUserName(track.picker_id,2) as picker_name'),
                'gds_orders.le_wh_id as le_wh_id',
                'invgrid.grand_total as invoice_amt',
                DB::raw('getMastLookupValue(gds_orders.order_status_id) as order_status'),
                DB::raw("((SUM(invitem.qty)*100)/SUM(gdsprd.qty)) as fill_rate"),
                DB::raw("(select COUNT(gds_order_prod_id) from gds_order_products where gds_order_products.gds_order_id = gds_orders.gds_order_id) as line_no"),
                DB::raw("(SELECT GROUP_CONCAT(SUBSTRING_INDEX(pcm.container_barcode, '-',-1))
                        FROM picker_container_mapping AS pcm
                        LEFT JOIN container_master AS cm ON pcm.container_barcode = cm.crate_code
                        WHERE pcm.order_id =gds_orders.gds_order_id) as crate_no"),
            ];
            $query = DB::table('gds_orders');
            $query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->leftJoin('gds_order_products as gdsprd', 'gdsprd.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->leftJoin('gds_invoice_grid as invgrid', 'invgrid.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->leftJoin('gds_invoice_items as invitem', 'invitem.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'gds_orders.cust_le_id');
            $query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->select($selectArr);
            if(isset($columns['filter_status']) && $columns['filter_status'] == 'rah'){
                $query->join('gds_returns as gdsr','gds_orders.gds_order_id','=','gdsr.gds_order_id');
                $query->whereIn('gds_orders.order_status_id', array('17022','17023'));
                $query->where('gdsr.return_status_id', '57067');
                $query->whereNull('gds_orders.order_transit_status');
            }else if(isset($columns['filter_status']) && $columns['filter_status'] == 'stocktransitdc'){
                $query->whereIn('gds_orders.order_status_id', array('17022','17023'));	
                $query->where('gds_orders.order_transit_status', '17027');
            }else if(isset($columns['filter_status']) && $columns['filter_status'] == 'stockindc'){
                $query->whereIn('gds_orders.order_status_id', array('17022','17023'));	
                $query->where('gds_orders.order_transit_status', '17028');
            }else{
                if(isset($status) && is_array($status) && count($status)>0){
                    if(in_array('17021', $status)){
                       array_push($status, '17026');
                    }
                    $query->whereIn("gds_orders.order_status_id", $status);
                }
            }
            if(isset($columns['flag']) && $columns['flag'] == 1){ //flag 1 - filter on delivery date
                if($fdate!="" && $tdate!="") {
                    $query->whereBetween('track.delivery_date', [$fdate, $tdate]);
                }
            }else{                
                if($fdate!="" && $tdate!="") {
                    $query->whereBetween('gds_orders.order_date', [$fdate, $tdate]);
                }
            }
            
            if(isset($columns['docket_no']) && $columns['docket_no'] != ''){
                $query->where("track.st_docket_no", $columns['docket_no']);
            }
            if(isset($columns['beat_id']) && is_array($columns['beat_id']) && count($columns['beat_id']) > 0){
                $query->whereIn("gds_orders.beat", $columns['beat_id']);
            }            
            if(isset($columns['hub_id']) && is_array($columns['hub_id']) && count($columns['hub_id']) > 0){
                $query->whereIn("gds_orders.hub_id", $columns['hub_id']);
            }

            if(isset($Json['118001'])) {
                $Dcs_Assigned = $Json['118001'];
                $query->whereRaw("gds_orders.le_wh_id IN ($Dcs_Assigned)");
            }
            if(isset($Json['118002'])) {
                $Hubs_Assigned = $Json['118002'];
                $query->whereRaw("gds_orders.hub_id IN ($Hubs_Assigned)");
            }
            $query->groupBy('gds_orders.gds_order_id');
            $query->orderBy('gds_orders.order_date', 'DESC');
            $query->skip($offset * $perpage)->take($perpage);
            $result = $query->get()->all();
            return $result;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }
    public function getPRAOrders($status, $fdate, $tdate, $offset, $perpage,$columns,$userId) {
        try {
          /*  $categoryModel= new CategoryModel();     
            $userData = $categoryModel->getUserId($columns['user_token']);
            if(is_array($userData) && isset($userData[0])) {
                $userId = $userData[0]->user_id;
            }*/
            $roleModel = new Role();
            $Json = json_decode($roleModel->getFilterData(6,$userId),1);
            $Json = json_decode($Json['sbu'],1);
            $selectArr = [
                'gds_orders.gds_order_id',
                'gds_orders.order_code',
                DB::raw('getLeWhName(gds_orders.hub_id) as hub_name'),
                DB::raw('getOrderBeatName(gds_orders.cust_le_id) as beat'),
                'gds_orders.total as order_val',
                DB::raw('getMastLookupValue(gds_orders.pref_slab1) as del_slot'),
                'gds_orders.shop_name',
                'le.address1 as retailer_address',
                'gds_orders.order_date',
                'track.cfc_cnt',
                'track.bags_cnt',
                'track.crates_cnt',
                'invgrid.gds_invoice_grid_id as invoice_order_no',
                'rgrid.return_grid_id as return_id',
                DB::raw('GetUserName(track.delivered_by,2) as de_name'),
                'gds_orders.le_wh_id as le_wh_id',
                'invgrid.grand_total as invoice_amt',
                DB::raw('getMastLookupValue(gds_orders.order_status_id) as order_status'),
                DB::raw("((SUM(invitem.qty)*100)/SUM(gdsprd.qty)) as fill_rate"),
                DB::raw("(select COUNT(gds_order_prod_id) from gds_order_products where gds_order_products.gds_order_id = gds_orders.gds_order_id) as line_no"),
                DB::raw("(SELECT GROUP_CONCAT(SUBSTRING_INDEX(pcm.container_barcode, '-',-1))
                        FROM picker_container_mapping AS pcm
                        LEFT JOIN container_master AS cm ON pcm.container_barcode = cm.crate_code
                        WHERE pcm.order_id =gds_orders.gds_order_id) as crate_no"),
            ];
            $query = DB::table('gds_orders');
            $query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->leftJoin('gds_order_products as gdsprd', 'gdsprd.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->leftJoin('gds_invoice_grid as invgrid', 'invgrid.gds_order_id', '=', 'gds_orders.gds_order_id');
            //$query->leftJoin('gds_invoice_items as invitem', 'invitem.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->join('gds_invoice_items as invitem', function ($join) {
                $join->on('invitem.gds_order_id', '=', 'gds_orders.gds_order_id')
                     ->on("invitem.product_id", '=', 'gdsprd.product_id');
            });
            $query->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'gds_orders.cust_le_id');            
            $query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'gds_orders.gds_order_id');
            //$query->join('gds_returns as gdsr','gdsr.gds_order_id','=','gds_orders.gds_order_id');
            $query->join('gds_returns as gdsr', function ($join) {
                $join->on('gdsr.gds_order_id', '=', 'gds_orders.gds_order_id')
                     ->on("gdsr.product_id", '=', 'gdsprd.product_id');
            });
            $query->select($selectArr);
            $query->whereIn('gds_orders.order_status_id', $status);
            $query->where('gdsr.return_status_id', array('67002'));
            if($fdate!="" && $tdate!="") {
                    $query->whereBetween('track.delivery_date', [$fdate, $tdate]);
            }            
            if(isset($columns['beat_id']) && is_array($columns['beat_id']) && count($columns['beat_id']) > 0){
                $query->whereIn("gds_orders.beat", $columns['beat_id']);
            }            
            if(isset($columns['hub_id']) && is_array($columns['hub_id']) && count($columns['hub_id']) > 0){
                $query->whereIn("gds_orders.hub_id", $columns['hub_id']);
            }
            if(isset($Json['118001'])) {
                $Dcs_Assigned = $Json['118001'];
                $query->whereRaw("gds_orders.le_wh_id IN ($Dcs_Assigned)");
            }
            if(isset($Json['118002'])) {
                $Hubs_Assigned = $Json['118002'];
                $query->whereRaw("gds_orders.hub_id IN ($Hubs_Assigned)");
            }
            $query->groupBy('gds_orders.gds_order_id');
            $query->orderBy('track.delivery_date', 'DESC');
            $query->skip($offset * $perpage)->take($perpage);
            $result = $query->get()->all();
            return $result;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function getOrderInfoByOrderId($orderId) {
        try {
            $orderId = (int) $orderId;
            $fieldArr = array(
                'orders.legal_entity_id',
                'orders.le_wh_id',
                DB::raw('getLeWhName(orders.hub_id) as hub_name'),
                'orders.gds_order_id',
                'orders.gds_cust_id',
                'orders.order_code',                
                DB::raw('GetUserName(orders.created_by,2) as created_by'),
                'orders.firstname',
                'orders.lastname',
                'orders.email',
                'orders.shop_name',
                'orders.phone_no',
                'orders.order_date',
                'orders.total as order_value',
                DB::raw('getMastLookupValue(orders.order_status_id) as order_status'),
                'orders.ship_total',
                'orders.tax_total',
                'orders.sub_total',
                'orders.discount as discount_total',
                'orders.total as grand_total',
                'orders.discount_type',
                'orders.order_expiry_date',
                DB::raw('getMastLookupValue(orders.pref_slab1) as del_slot'),
                DB::raw('CONCAT(le.address1, " ",le.address2) AS billing_address')
            );
            $query = DB::table('gds_orders as orders')->select($fieldArr);
            $query->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'orders.cust_le_id');
            $query->where('orders.gds_order_id', $orderId);
            $orders = $query->first();
            return $orders;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function getOrderProdcutInfoByOrderId($orderId) {
        try {
            $fieldArr = array(
                'product.product_id',
                'product.pname',
                'product.sku',
                'product.qty as orderQty',
                'product.mrp',
                'product.price',
                'product.tax',
                'product.tax_class',
                'product.total',
                DB::raw('(product.price / product.qty) as unitPrice'),
                DB::raw('(
                        CASE
                          WHEN ISNULL(
                            `product`.`parent_id`
                          ) 
                          THEN `product`.`product_id`
                          ELSE `product`.`parent_id`
                        END
                            ) AS `parent_id`'),
                'shipitem.qty as shipQty',
                'invitem.qty as invQty',
                'canitem.qty as canQty',
                'retitem.qty as retQty',                
            );
            $query = DB::table('gds_order_products as product')->select($fieldArr);
            $query->join('gds_orders as orders', 'orders.gds_order_id', '=', 'product.gds_order_id');
            $query->leftJoin('gds_ship_grid as shipgrid', 'shipgrid.gds_order_id', '=', 'orders.gds_order_id');
            $query->leftJoin('gds_ship_products as shipitem', function ($join) {
                $join->on('shipitem.gds_ship_prd_id', '=', 'shipgrid.gds_ship_grid_id')->on("shipitem.product_id", '=', 'product.product_id');
            });
            $query->leftJoin('gds_invoice_items as invitem', function ($join) {
                $join->on('invitem.gds_order_id', '=', 'orders.gds_order_id')->on("product.product_id", '=', 'invitem.product_id');
            });
            $query->leftJoin('gds_cancel_grid as cangrid', 'cangrid.gds_order_id', '=', 'orders.gds_order_id');
            $query->leftJoin('gds_order_cancel as canitem', function ($join) {
                $join->on('canitem.cancel_grid_id', '=', 'cangrid.cancel_grid_id')->on("canitem.product_id", '=', 'product.product_id');
            });
            $query->leftJoin('gds_returns as retitem', function ($join) {
                $join->on('retitem.gds_order_id', '=', 'orders.gds_order_id')->on("product.product_id", '=', 'retitem.product_id');
            });
            
            if (is_array($orderId) && count($orderId) > 0) {
                $query->whereIn('product.gds_order_id', $orderId);
            } else {
                $query->where('product.gds_order_id', $orderId);
            }
            $query->orderBy('parent_id', 'asc');
            $products = $query->get()->all();
            return $products;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function getInvoiceByOrderId($order_id) {
        try {
            $selectArr = [
                'gig.gds_invoice_grid_id',
                'gig.invoice_code as InvoiceId',
                DB::raw('GetUserName(gig.created_by,2) as InvoicedBy'),
                'gig.created_at as InvoiceDate',
            ];
            $query = DB::table('gds_invoice_grid as gig');
            $query->select($selectArr);
            $query->where('gig.gds_order_id', $order_id);
            $result = $query->get()->all();
            return $result;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function getInvoiceQtyByOrderId($order_id) {
        try {
            $selectArr = [
                'gig.gds_invoice_grid_id',
                DB::raw('SUM(gds_invoice_items.qty) as totalInvoiceQty'),
            ];
            $query = DB::table('gds_invoice_grid as gig');
            $query->join('gds_invoice_items', 'gds_invoice_items.gds_order_id', '=', 'gig.gds_order_id');
            $query->select($selectArr);
            $query->where('gig.gds_order_id', $order_id);
            $result = $query->first();
            return $result;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function getOrderQtyByOrderId($order_id) {
        try {
            $selectArr = [
                DB::raw('SUM(gds_order_products.qty) as totalOrderQty'),
                DB::raw('COUNT(gds_order_products.gds_order_prod_id) as orderLineCount'),
            ];
            $query = DB::table('gds_order_products');
            $query->select($selectArr);
            $query->where('gds_order_products.gds_order_id', $order_id);
            $result = $query->first();
            return $result;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function getCancelByOrderId($order_id) {
        try {
            $selectArr = [
                'gcg.cancel_grid_id',
                'gcg.cancel_code as CancelId',
                DB::raw('GetUserName(gcg.created_by,2) as CancelledBy'),
                'gcg.created_at as CancelDate',
            ];
            $query = DB::table('gds_cancel_grid as gcg');
            $query->select($selectArr);
            $query->where('gcg.gds_order_id', $order_id);
            $result = $query->get()->all();
            return $result;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function getReturnsByOrderId($order_id) {
        try {
            $selectArr = [
                'grg.return_grid_id',
                'grg.return_order_code as ReturnId',
                DB::raw('GetUserName(grg.created_by,2) as ReturnedBy'),
                'grg.created_at as ReturnDate',
            ];
            $query = DB::table('gds_return_grid as grg');
            $query->select($selectArr);
            $query->where('grg.gds_order_id', $order_id);
            $result = $query->get()->all();
            return $result;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function getOrderCommentById($entityId = 0, $commentType = '') {
        try {
            $fieldArr = array('comment.*',
                DB::raw('GetUserName(comment.commentby,2) AS user_name'),
                DB::raw('getMastLookupValue(comment.order_status_id) AS status')
            );
            $query = DB::table('gds_orders_comments as comment')->select($fieldArr);
            if ($entityId) {
                $query->where('entity_id', $entityId);
            }
            if ($commentType != '') {
                $commentType = $this->getCommentTypeByName($commentType);
                $query->where('comment_type', $commentType);
            }
            $query->groupBy('comment.comment_id');
            $query->orderBy('comment.comment_id', 'DESC');
            $commentArr = $query->get()->all();
            return $commentArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getOrderStatus($catName = 'Order Status') {
        try {
            $fieldArr = array('master_lookup_name as name', 'value');
            $query = DB::table('master_lookup')->select($fieldArr);
            $query->join('master_lookup_categories', 'master_lookup_categories.mas_cat_id', '=', 'master_lookup.mas_cat_id');
            $query->where('master_lookup.is_active', 1);
            $query->where('master_lookup_categories.mas_cat_name', $catName);
            $query->whereNotNull('value');
            $allOrderStatusArr = $query->pluck('name', 'value')->all();
            return $allOrderStatusArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

}
