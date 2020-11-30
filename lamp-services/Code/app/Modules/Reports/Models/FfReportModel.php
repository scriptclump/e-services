<?php

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;

class FfReportModel extends Model
{
    public $timestamps = true;
    protected $fillable = ["ff_rp_id", "hub_name", "beat", "user_id", "name", "first_order", "order_cnt", "first_call", "calls_cnt", "tbv", "uob", "abv", "tlc", "ulc", "alc", "contrib", "margin", "delivered_margin", "cancel_ord_cnt", "cancel_ord_val", "return_ord_cnt", "return_ord_val", "order_date", "role", "commission", "created_at", "updated_at", "ecash"];
	protected $table = "ff_report";
	protected $primaryKey = 'ff_rp_id';
}