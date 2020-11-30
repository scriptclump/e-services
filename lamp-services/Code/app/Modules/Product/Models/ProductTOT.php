<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductTOT extends Model
{
    public $timestamps = true;
    protected $table = "product_tot";
    protected $primaryKey = 'prod_price_id';
}
