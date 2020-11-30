<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class totModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['prod_price_id','product_id','currency_id','mrp','msp','base_price','dlp','rlp','is_markup','cbp','vat','cst','gst','credit_days','delivery_terms','is_return_accepted','inventory_mode'];
	protected $table = "product_tot";
	protected $primaryKey = 'prod_price_id';



}
