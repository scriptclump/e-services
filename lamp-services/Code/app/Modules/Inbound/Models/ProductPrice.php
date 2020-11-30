<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : ProductPrice.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 24-May-2016
  Desc : Model for product price table
 */

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = "product_tot";
    protected $primaryKey = "prod_price_id";
}