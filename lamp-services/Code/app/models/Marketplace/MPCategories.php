<?php

namespace App\models\Marketplace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use DB;
use Session;
class MPCategories extends Model {
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'mp_categories';
    protected $fillable = array('mp_id','mp_key', 'mp_category_id','charge_type','mp_commission','category_name', 'parent_category_id', 'is_leaf_category','is_approved');
    //put your code here
    protected $dates = ['deleted_at'];
}
