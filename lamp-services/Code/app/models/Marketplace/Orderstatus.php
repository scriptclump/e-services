<?php

namespace App\models\Marketplace;
use Illuminate\Database\Eloquent\Model;
//use DB;
use Session;
class Orderstatus extends Model {

    public $timestamps = false;
    protected $table = 'mp_order_status';
    protected $fillable = array('id','mp_id','mp_status','status_type','order_status_description');
    //put your code here
    
}
