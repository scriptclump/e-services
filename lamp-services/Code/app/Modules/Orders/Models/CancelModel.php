<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\Invoice;
use App\models\Dmapi\dmapiOrders;
use App\Modules\Orders\Models\PaymentModel;
use App\Modules\Tax\Models\TaxClass;
use Log;

class CancelModel extends Model
{
    
    public $timestamps = false;

    public function getCancelGridByOrderId($orderId) {
    	try {
			$query = DB::table('gds_cancel_grid as grid')->select('grid.*');
			$query->where('grid.gds_order_id', $orderId);
			return $query->get()->all();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    public function deleteCancelProduct($gridId) {
    	try {
    		DB::table('gds_order_cancel')->whereIn('cancel_grid_id', $gridId)->delete();	
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    public function deleteCancelGrid($gridId) {
        try {
            DB::table('gds_cancel_grid')->where('cancel_grid_id', $gridId)->delete();    
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getCancelGridByGridId($gridId) {
        try {
            $query = DB::table('gds_cancel_grid as grid')->select('grid.*');
            $query->where('grid.cancel_grid_id', $gridId);
            return $query->first();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
    public function getCancelProductByGridId($gridId) {
        try {
            $query = DB::table('gds_order_cancel as cancel')->select('cancel.*');
            $query->where('cancel.cancel_grid_id', $gridId);
            return $query->get()->all();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function convertObjectToArray($data) {
        return json_decode(json_encode($data),true);
    }

    /*
    *  getCancelledProductqty() method is used to get cancelled products Qty of order*
     * @param $orderId,$product_id Numeric
     * @return Object
     */
    public function getCancelledQtyWithReason($orderId, $product_id) {
        try {
            $fields = array(DB::raw('SUM(canitem.qty) as cancelledQty'), 'canitem.cancel_reason_id');
            $query = DB::table('gds_cancel_grid as grid')->select($fields);
            $query->join('gds_order_cancel as canitem','canitem.cancel_grid_id','=','grid.cancel_grid_id');
            $query->where('grid.gds_order_id', $orderId);
            $query->where('canitem.product_id', $product_id);
            return $query->first();
            
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function updateCancelGrid($gridId, $fields) {
        try{
            DB::table('gds_cancel_grid')->where('cancel_grid_id', $gridId)->update($fields);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function rollBackOrders($gdsorderId, $flag){

     $query = DB::selectFromWriteConnection(DB::raw("CALL revertOrderStatus_OFD($gdsorderId,$flag)"));

     return $query;
     
    }


}
