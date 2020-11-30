<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class BrandModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "brand_id";
    protected $table = "brands";

}

