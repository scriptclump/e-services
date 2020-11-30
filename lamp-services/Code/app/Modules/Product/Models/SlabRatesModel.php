<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class SlabRatesModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "product_slab_id";
    protected $table = "products_slab_rates";

}

