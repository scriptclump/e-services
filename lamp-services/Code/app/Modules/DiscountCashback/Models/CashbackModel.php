<?php
namespace App\Modules\DiscountCashback\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class CashbackModel extends Model {

    
    
    public function __construct(){
       
    }

    public function getAppliedCash($cbk_id,$product_id,$le_wh_id){

    	$query = "select *,getRolesNameById(benificiary_type) as role_given from promotion_cashback_details where cbk_id = $cbk_id and product_id = $product_id and wh_id = $le_wh_id and cbk_status = 1 ";
    	$data = DB::select($query);
    	if(count($data) > 0){

    		$data = json_decode(json_encode($data),true);
    		return $data[0];
    	}else{

    		return false;
    	}

    }

    public function getCashBackOnOrderId($order_id)
    {
        $query = "SELECT *, getRolesNameById(benificiary_type) as role_given 
                    FROM gds_order_cashback_data
                    WHERE gds_order_cashback_data.gds_order_id = $order_id";
        $data = DB::select($query);
        if(count($data) > 0){

            $data = json_decode(json_encode($data),true);
            return $data;
        }else{

            return false;
        }
    }

    public function getPackDetailsOnOrder($gds_order_id,$gds_product_id,$product_star){

    	$query = "select * from gds_order_product_pack where gds_order_id = $gds_order_id and product_id = $gds_product_id and star = $product_star";

    	$data = DB::select($query);
        if(count($data) > 0){

            $data = json_decode(json_encode($data),true);
            return $data[0];
        }else{

            return false;
        }
    }

    public function getBillValueByStar($gds_order_id,$product_star){
        
        $query = "SELECT 
                    IFNULL(ROUND((IFNULL(SUM(gds_invoice_items.`qty` * gds_order_products.unit_price),0) - IFNULL(SUM(gds_returns.`qty` * gds_order_products.unit_price),0)),2),0) AS applied_bill
                                FROM gds_order_products,gds_order_product_pack, gds_orders JOIN gds_invoice_items
                                LEFT JOIN gds_returns ON gds_returns.`gds_order_id` = gds_invoice_items.`gds_order_id` 
                                AND gds_returns.`product_id` = gds_invoice_items.`product_id`
                                WHERE gds_order_products.gds_order_id = gds_orders.gds_order_id
                                AND gds_order_products.gds_order_id = gds_invoice_items.gds_order_id
                                AND gds_invoice_items.`gds_order_id` = gds_orders.gds_order_id
                                AND gds_invoice_items.`product_id` = gds_order_products.`product_id`
                                AND gds_order_product_pack.`gds_order_id` = gds_orders.gds_order_id 
                                AND gds_order_product_pack.`product_id` = gds_order_products.`product_id`
                                AND gds_orders.gds_order_id IN ($gds_order_id) AND gds_order_product_pack.star = $product_star";
        $query_new = " SELECT 
            applied_bill
            -- gds_order_product_pack.star
            FROM (
            SELECT 
                gds_order_products.gds_order_id,
                gds_order_products.product_id, IFNULL(ROUND((IFNULL(gds_invoice_items.`qty`,0) - IFNULL(gds_returns.`qty`,0)),2),0) * gds_order_products.unit_price AS applied_bill
            FROM 
            gds_order_products
            LEFT JOIN gds_invoice_items ON 
                    gds_invoice_items.gds_order_id = gds_order_products.gds_order_id AND 
                    gds_invoice_items.product_id   = gds_order_products.product_id
            LEFT JOIN gds_returns ON 
                    gds_returns.gds_order_id = gds_order_products.gds_order_id AND 
                    gds_returns.product_id   = gds_order_products.product_id
            WHERE 
            gds_order_products.gds_order_id = $gds_order_id) AS products
            LEFT JOIN 
                    (
                        SELECT *
                        FROM (
                            SELECT *
                            FROM gds_order_product_pack
                            WHERE gds_order_id = $gds_order_id
                            ORDER BY product_id,pack_qty DESC
                        )   pp GROUP BY product_id
                    ) AS product_pack
            ON 
            product_pack.product_id   = products.product_id AND
            product_pack.gds_order_id = products.gds_order_id
            WHERE star = $product_star
            GROUP BY product_pack.product_id";

        $data = DB::select($query_new);
        if(count($data) > 0){

            $data = json_decode(json_encode($data),true);
            $return_data = [];
            $applied_bill = 0;
            foreach ($data as $key => $value) {
              $applied_bill +=  $value['applied_bill'];
            }
            $return_data['applied_bill'] = $applied_bill;
            
            return $return_data;
        }else{

            return false;
        }
    }

    public function getReturnQtyByOrderIdAndProductId($gds_order_id,$gds_product_id){

        $query = "SELECT sum(qty) AS qty
                    FROM gds_returns
                    WHERE gds_order_id = $gds_order_id AND product_id = $gds_product_id";

        $data = DB::select($query);
        if(count($data) > 0){

            $data = json_decode(json_encode($data),true);
            return $data[0];
        }else{

            return array('qty' => 0);
        }
    }

    
}