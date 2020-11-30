<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductPolicies extends Model
{
    public $timestamps = true;
    protected $table = "product_policies";
    protected $primaryKey = 'product_policy_id';
}
