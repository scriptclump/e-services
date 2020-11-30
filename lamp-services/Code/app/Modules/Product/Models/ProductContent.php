<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductContent extends Model
{
    public $timestamps = true;
    protected $table = "product_content";
    protected $fillable = ['title', 'description'];
    protected $primaryKey = 'prod_content_id';
    
}