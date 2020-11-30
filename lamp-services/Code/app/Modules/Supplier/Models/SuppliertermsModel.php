<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class SuppliertermsModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['legal_entity_id','le_type_id','vendor_reg_charges','sku_reg_charges','dc_link_charges','b2b_channel_support_as',
        'ecp_visibility_ass','po_days','delivery_tat','delivery_tat_uom','invoice_days','delivery_frequency','credit_period','payment_days',
        'negotiation','rtv','rtv_timeline','rtv_scope','rtv_location','start_date','end_date','created_by','created_at','updated_by','updated_at'];
	protected $table = "aggrement_terms";
	protected $primaryKey = 'terms_id';

	
}
