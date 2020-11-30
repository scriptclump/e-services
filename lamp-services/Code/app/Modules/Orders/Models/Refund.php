<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Refund extends Model
{
    protected $table = "gds_refund_grid";

    /**
     * getAllRefunds() - get all refund
     * @param  integer  $orderId
     * @param  integer $rowCount
     * @param  integer $offset
     * @param  integer $perpage
     * @param  array   $filter
     * @return Array
     */
    
	public function getAllRefunds($orderId, $rowCount=0, $offset=0, $perpage=10, $filter=array()) {
		
		try{
			$fieldArr = array('grid.*','orders.order_date');
		
			$query = DB::table('gds_refund_grid as grid')->select($fieldArr);
	                $query->join('gds_orders as orders', 'grid.gds_order_id', '=', 'orders.gds_order_id');
			$query->where('grid.gds_order_id', $orderId);
			#echo $query->toSql();die;
			if($rowCount) {
				return $query->count();
			}
			else {
				return $query->get()->all();
			}
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}		
	}

	public function getRefundById($refundId) {
		try{
			$fieldArr = array('grid.*', 'rproduct.*');
		
			$query = DB::table('gds_refund_grid as grid')->select($fieldArr);
            $query->join('gds_refund_products as rproduct', 'grid.refund_grid_id', '=', 'rproduct.refund_grid_id');
	        //$query->join('gds_orders as orders', 'grid.gds_order_id', '=', 'orders.gds_order_id');
			$query->where('grid.refund_grid_id', $refundId);
			#echo $query->toSql();die;
			return $query->get()->all();
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
}
