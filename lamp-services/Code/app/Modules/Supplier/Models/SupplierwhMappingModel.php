<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierwhMappingModel extends Model
{
    public $timestamps = true;
    protected $fillable = ['supplier_id','le_wh_id','product_id','atp','delivery_time','created_by','created_at','updated_by','updated_at'];
	protected $table = "supplier_le_wh_mapping";
	protected $primaryKey = 'sup_le_wh_id';
}
