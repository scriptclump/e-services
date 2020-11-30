<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class ProductAttributes extends Model
{
    public $timestamps = false;
    protected $primaryKey = "prod_att_id";
    protected $table = "product_attributes";

    

}

