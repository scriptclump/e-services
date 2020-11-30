<?php

namespace App\models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use DB;
use Session;

class Varients extends Model {

    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'mp_attr_options';
    protected $fillable = array('mp_id', 'mp_key', 'mp_option_id', 'featureid', 'mp_option_name', 'description');
    protected $dates = ['deleted_at'];

    
}
