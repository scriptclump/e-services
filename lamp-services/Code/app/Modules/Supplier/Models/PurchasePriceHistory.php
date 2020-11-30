<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;


class PurchasePriceHistory extends Model
{
    public $timestamps = false;
    protected $fillable = ['pur_price_id','product_id','supplier_id','le_wh_id','elp','effective_date','created_at','created_by'];
	protected $table = "purchase_price_history";
	protected $primaryKey = 'pur_price_id';
            
}
