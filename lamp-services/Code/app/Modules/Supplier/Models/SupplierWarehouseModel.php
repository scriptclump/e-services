<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class SupplierWarehouseModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['legal_entity_id','erp_code','sp_wh_name','contact_name','phone_no','email','country','address1','address2','state','pincode','city','longitude','longitude','latitude'];
	protected $table = "supplier_warehouses";
	protected $primaryKey = 'sp_wh_id';

	
}
