<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

Class ProductGroup extends Model {

    public function childProdutList($parent_id) {
        $child_data = DB::table('product_relations as pr')
                        ->join('vw_products_list as vw', 'vw.product_id', '=', 'pr.product_id')
                        ->where('pr.parent_id', $parent_id)
                        ->select('vw.product_id', 'vw.primary_image', 'vw.product_title', 'vw.mrp', 'vw.variant_value1 as Varient1', 'vw.variant_value2 as Varient2', 'vw.variant_value3 as Varient3', 'vw.cp_enabled', 'vw.is_sellable')->get()->all();
        $parent_data = array();
        $parent_data = DB::table('vw_products_list as vw')
                        ->where('vw.product_id', $parent_id)
                        ->select('vw.product_id', 'vw.primary_image', 'vw.product_title', 'vw.mrp', 'vw.variant_value1 as Varient1', 'vw.variant_value2 as Varient2', 'vw.variant_value3 as Varient3', 'vw.cp_enabled', 'vw.is_parent', 'vw.is_sellable')->get()->all();
        $product_viw_data = array_merge($parent_data, $child_data);
       return $product_viw_data;
    }
    public function pricing($product_id,$userId) {
         $whId = Session::get('warehouseId'); 
        $pricing = DB::select('call getProductSlabs(?,?,?)', array($product_id, $whId, $userId));
        return $pricing;
    }
    public function getSupplier ($product_id) {
        $supplier = DB::table('product_tot')->where('product_id', $product_id)->select('supplier_id')->first();
        return $supplier;
    }
    public function getRepoProducts ($parent_id) {
        $child_data = DB::table('product_relations as pr')
                    ->join('vw_products_list as vw', 'vw.product_id', '=', 'pr.product_id')
                    ->where('pr.parent_id', $parent_id)
                    ->select('vw.product_id', 'vw.primary_image', 'vw.product_title', 'vw.mrp', 'vw.variant_value1 as Varient1', 
                            'vw.variant_value2 as Varient2', 'vw.variant_value3 as Varient3', 'vw.cp_enabled', 'vw.is_sellable')
                    ->get()->all();            
            $parent_data = array();
            $parent_data = DB::table('vw_products_list as vw')
                            ->where('vw.product_id', $parent_id)
                            ->select('vw.product_id', 'vw.primary_image', 'vw.product_title', 'vw.mrp', 
                                    'vw.variant_value1 as Varient1', 'vw.variant_value2 as Varient2', 
                                    'vw.variant_value3 as Varient3', 'vw.cp_enabled', 'vw.is_sellable')->get()->all();
            $product_viw_data = array_merge($parent_data, $child_data);
            
        return $product_viw_data;
    }

}
