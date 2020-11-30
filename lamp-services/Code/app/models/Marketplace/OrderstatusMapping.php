<?php

namespace App\models\Marketplace;
use Illuminate\Database\Eloquent\Model;
//use DB;
use Session;
class OrderstatusMapping extends Model {

    public $timestamps = false;
    protected $table = 'mp_status_mapping';
    protected $fillable = array('mp_status_id','mp_id','status_type','mp_status','ebutor_status_id','active_status');
    //put your code here
    
}
