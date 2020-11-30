<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductRelations extends Model
{
    public $timestamps = true;
    protected $fillable =['product_id','parent_id','created_by'];
    protected $table = "product_relations";

}
