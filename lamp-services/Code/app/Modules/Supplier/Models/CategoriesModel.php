<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['category_id','cat_name','description','parent_id','is_active','charges','charge_type','is_product_class','created_by','created_at','updated_by','updated_at'];
    protected $table = "categories";
    protected $primaryKey = 'category_id';
}
