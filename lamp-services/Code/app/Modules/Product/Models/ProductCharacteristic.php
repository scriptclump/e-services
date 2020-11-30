<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class ProductCharacteristic extends Model
{
   
    protected $primaryKey = "charateristic_id";
    protected $table = "product_characteristics";
    public $timestamps = true;
    

}

