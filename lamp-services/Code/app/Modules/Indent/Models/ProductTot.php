<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductTot extends Model
{
    protected $table          = "product_tot";
    protected $primaryKey     = "prod_price_id";
    
    public function getProductTotById($productId) {
		$fieldArr = array('tot.mpq','tot.product_id');
		$query = DB::table('product_tot as tot')->select($fieldArr);
        $query->where('tot.product_id', '=', $productId);
        return $query->first();
    }

    
    public function getSuppliersByIndent($indentId, $whId){
    	/*select distinct(pt.supplier_id), le.business_legal_name from product_tot pt
inner join indent_products ip on ip.product_id=pt.product_id
inner join legal_entities le on le.legal_entity_id = pt.supplier_id
where ip.indent_id = 282 and pt.le_wh_id=4497;*/
		$fields = array( 
    //DB::raw('distinct(pt.supplier_id) as suppliers'), 
    'le.business_legal_name as supplier_name','le.legal_entity_id as suppliers',
    'le.address1','le.address2', 'le.city','le.pincode','le.pan_number','le.tin_number', 'countries.name as country_name', 
    'zone.name as state_name');
		//$query = DB::table("product_tot as pt")->select($fields)
    $query = collect(DB::table("legal_entities as le")->select($fields)
				//->join("indent_products as ip", "ip.product_id", "=", "pt.product_id")
				//->join("legal_entities as le", "le.legal_entity_id", "=", "pt.supplier_id")
				->join("suppliers AS SS ","le.legal_entity_id", "=", "SS.legal_entity_id")
				->leftJoin('countries', 'countries.country_id', '=', 'le.country')
				->leftJoin('zone', 'zone.zone_id', '=', 'le.state_id')
				//->where("ip.indent_id",$indentId)
				//->where("pt.le_wh_id",$whId)
        //->where("SS.is_active","=", 1)
        ->where("le.is_approved", "=", 1)
        //->where("pt.subscribe", "=", 1)
				->get()->all()
        )->keyBy('suppliers');
				//->lists("le.legal_entity_id","le.legal_entity_id");
		return json_decode(json_encode($query,1),1);
    }

    

    public function getSelectedSupplierAddress($supplierID)
    {
    	try {
    		$fields = array('le.address1','le.address2', 'le.city','le.pincode', 'countries.name as country_name', 'zone.name as state_name');
    		$sql = DB::table("legal_entities as le")
    					->leftJoin('countries', 'countries.country_id', '=', 'le.country')
						->leftJoin('zone', 'zone.zone_id', '=', 'le.state_id')
						->where("le.legal_entity_id", "=", $supplierID)
						->get($fields)->all();
			$res = json_decode(json_encode($sql), true);
			return $res[0];
    		
    	} catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
}
