<?php

namespace App\models\Marketplace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use DB;
use Session;
class Features extends Model {
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'mp_attributes';
    protected $fillable = array('mp_key','mp_id','mp_category_id', 'feature_id', 'feature_name', 'feature_type','isrequiredfeature','isfilterfeature');

    protected $dates = ['deleted_at'];
    
}
