<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : ProductInventory.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 20-May-2016
  Desc : Model for product inventory table
 */

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    protected $table = 'product_inventory';
    protected $primaryKey = "prod_inv_id";
}