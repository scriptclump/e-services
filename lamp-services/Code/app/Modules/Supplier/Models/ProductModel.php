<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductModel extends Model
{
    public $timestamps = true;
    protected $fillable = ['product_id','legal_entity_id','supplier_id','product_name','product_group_id','primary_image','product_type_id','category_id','business_unit_id','is_gds_enabled','brand_id','weight_uom','weight','lbh_uom','length','breadth','height','product_uom','upc','upc_type','tax_class_id','is_active','date_added','created_by','date_modified','modified_by','sku','seller_sku','is_deleted','is_traceable','moq','is_heavy_weight','no_of_units','is_parent'];
	protected $table = "products";
	protected $primaryKey = 'product_id';


	public function brands()
    {
        return $this->hasOne('App\Modules\Supplier\Models\BrandModel', 'brand_id', 'brand_id');
    }

	public function tot()
    {
        return $this->hasOne('App\Modules\Supplier\Models\TotModel', 'product_id', 'product_id');
    }

	
}
