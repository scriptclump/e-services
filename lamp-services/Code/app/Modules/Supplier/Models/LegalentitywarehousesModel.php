<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class LegalentitywarehousesModel extends Model
{
 public $timestamps = false;
    protected $fillable = ['lp_id','lp_name','legal_entity_id','lp_wh_id','tin_number','lp_wh_name','contact_name','phone_no','email','country','address1','address2','state','pincode','city','status','longitude','latitude','landmark','created_by','created_at','updated_by','updated_at'];
	protected $table = "legalentity_warehouses";
	protected $primaryKey = 'le_wh_id';   
}
