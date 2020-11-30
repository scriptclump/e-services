<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductFreeBie extends Model
{
    public $timestamps = true;
    protected $table = "freebee_conf";
    protected $primaryKey = 'free_conf_id';
}
