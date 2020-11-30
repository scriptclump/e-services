<?php

namespace App\models\Marketplace;
use Illuminate\Database\Eloquent\Model;
use Session;
class MPStatusMapping extends Model {

    public $timestamps = false;
    protected $table = 'mp_status_mapping';
    protected $fillable = array(
    	'mp_status_id',
    	'mp_id',
    	'status_type',
    	'mp_status',
    	'order_status_description',
    	'ebutor_status_id',
    	'is_active',
    	'created_by',
    	'created_at',
    	'updated_by',
    	'updated_at'
    	);
    //put your code here
    
}