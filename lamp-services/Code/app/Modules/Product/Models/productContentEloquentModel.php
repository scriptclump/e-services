<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class ProductContentEloquentModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "prod_content_id";
    protected $table = "product_content";

}

