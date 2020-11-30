<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use Lang;

class OrderProduct extends Model
{
    public function getProductPackConfig($productIds) {
    
        try{
            $fields = array('ppc.product_id', 'ppc.level', 'ppc.no_of_eaches');
            $query = DB::table('product_pack_config as ppc')->select($fields);
            $query->whereIn('ppc.product_id', $productIds);
            $query->where('ppc.level', '16004');
            $query->where('ppc.is_sellable', 1);
            $query->orderBy('ppc.effective_date', 'desc');
            $packInfo = $query->first();
            return $packInfo;

            /*$packConfig = array();

            if(count($packInfo)) {
                foreach ($packInfo as $pack) {
                    $packConfig[$pack->product_id] = $pack->no_of_eaches;
                }
            }
            
            return $packConfig;*/
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getGdsOrderProductByOrderId($orderId) {
        $query = DB::table('gds_order_products as gdsprd')->select(array('gdsprd.product_id', 'gdsprd.total', 'gdsprd.qty', 'gdsprd.gds_order_prod_id'));
        
        $query->where('gdsprd.gds_order_id', $orderId);
        return $query->get()->all();

    }
}
