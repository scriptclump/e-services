<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Products extends Model {
            /*
     * getproductsBySupplier() method is used to get get Product Information by Supplier Id
     * @param Null
     * @return
     */
    public function getproductsBySupplier($supplier_id, $warehouse_id) {
        try {   
            $fieldArr = array('products.product_id','products.product_title as product_name','products.sku');
            $query = DB::table('product_tot')->select($fieldArr);
            $query->join('products','products.product_id','=','product_tot.product_id');
            $query->leftJoin('product_content','product_content.product_id','=','product_tot.product_id');
            $query->where('product_tot.supplier_id', $supplier_id);
            $query->where('product_tot.le_wh_id', $warehouse_id);
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
            /*
     * getProductsById() method is used to get get Product Information By Id
     * @param Null
     * @return
     */
    public function getProductsById($productId,$le_wh_id,$sup_id) {
        try {

            $fieldArr = array('products.product_id','products.product_title as product_name','products.sku','products.upc','products.mrp','tot.base_price'
                ,'tot.cbp','tot.mpq','inventory.soh','inventory.mbq','currency.symbol_left as symbol', 'products.seller_sku');

            $query = DB::table('products')->select($fieldArr);
            $query->leftJoin('brands','brands.brand_id','=','products.brand_id');
            $query->leftJoin('product_tot as tot', function($join)
                    {
                        $join->on('products.product_id','=','tot.product_id');
                        //$join->on('tot.supplier_id','=','po.legal_entity_id');
                        //$join->on('tot.le_wh_id','=','po.le_wh_id');
                    });
            $query->leftJoin('inventory', function($join)
             {
                $join->on('products.product_id','=','inventory.product_id');
                $join->on('tot.le_wh_id','=','inventory.le_wh_id');
             });
             
            $query->leftJoin('product_content','product_content.product_id','=','products.product_id');
            $query->leftJoin('currency','tot.currency_id','=','currency.currency_id');
            $query->where('products.product_id', $productId);
            $query->where('tot.le_wh_id', $le_wh_id);
            $query->where('tot.supplier_id', $sup_id);
            
            return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getProductsBySku($sku, $supplierId, $leWhId) {
        try {

            $fieldArr = array('products.product_id', 'products.sku','products.upc','products.mrp','tot.base_price', 'products.seller_sku');

            $query = DB::table('products')->select($fieldArr);
            $query->leftJoin('brands','brands.brand_id','=','products.brand_id');
            $query->leftJoin('product_tot as tot','products.product_id','=','tot.product_id');
            $query->where('products.sku', $sku);
            $query->where('tot.le_wh_id', $leWhId);
            $query->where('tot.supplier_id', $supplierId);
            return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

}
