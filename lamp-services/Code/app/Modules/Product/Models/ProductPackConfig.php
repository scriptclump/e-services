<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class productPackConfig extends Model
{
    public $timestamps = false;
    protected $table = "product_pack_config";
    protected $primaryKey = 'pack_id';
}
