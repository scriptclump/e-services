<?php

namespace App\models\countries;
use Illuminate\Database\Eloquent\Model;
//use DB;
use Session;
class Countries extends Model {

    public $timestamps = false;
    protected $table = 'countries';
    protected $fillable = array('country_id', 'iso_code_3', 'name');

    //put your code here
    
}
