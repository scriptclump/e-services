<?php

namespace App\Modules\InventoryStatusReports\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Log;
use Illuminate\Support\Facades\Cache;

Class InventoryStatusReports extends Model {

    public function getInventoryStatusReports() {
/*        $reports = DB::table('vw_inventory_report')->select('le_wh_id', 'product_id', 'product_title', 'sku','product_class_name', 'brand_name', 'manufacturer_name', 'elp', 'esp', 'esu','mrp', 'available_inventory', DB::raw('getLeWhName(le_wh_id) as whname'),
		DB::raw('getProductName(getParentPrdId(product_id)) as parent_id'), DB::raw('getvarientValue(product_id,1)  AS variant_value1'),'is_sellable','cp_enabled')
			->where('soh', '>', '0')
			->where('kvi', '<>', 'Q9')
			->where(function($p){
			$p->where(function($q) {
			$q->where('esp','<=','0')
			->orWhere('esu','<=','0')
			->orWhere('elp','<=','0');
			})
			->orwhere(function($q){
			$q->where('is_sellable','=','0')
			->orwhere('cp_enabled','=','0');
			});				
			} )

			->get();
			//->toSql();

			//print_r($reports); die;
		
        return $reports;
*/	

        $reports = DB::table('vw_NonSellableSohProducts')->select('le_wh_id', 'product_id', 'product_title', 'sku','product_class_name', 'brand_name', 'manufacturer_name', 'elp', 'esp', 'esu','mrp', 'available_inventory', DB::raw('getLeWhName(le_wh_id) as whname'),
		DB::raw('getProductName(getParentPrdId(product_id)) as parent_id'), DB::raw('getvarientValue(product_id,1)  AS variant_value1'),'is_sellable','cp_enabled')->get()->all();
		
		if(count($reports)>0)	
        return $reports;
	    else
		return array();	
    }


}
