<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;


class BrandModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['brand_id','legal_entity_id','brand_name','description','is_active','is_global','is_authorized','is_trademark','trademark_url','logo_url'];
	protected $table = "brands";
	protected $primaryKey = 'brand_id';
    
    /*
     * getBrandsBySupplierId($supplier_id) is used to get Brands of a supplier
     * @param $supplier_id Integer
     * @return boolean or Array or String
     */
    public function getBrandsBySupplierId($legal_entity_id){



		$fieldArr = array('brand_name');
        $query = $this->select($fieldArr);
        $query->whereIn('legal_entity_id', $legal_entity_id);
        $query->groupby('brand_name');
		$brands = $query->get()->all();
        return $brands;
    }
	
}
