<?php

namespace App\models\Marketplace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use DB;
use Session;
class MPCharges extends Model {
    use SoftDeletes;
    
    public $timestamps = false;
    protected $table = 'mp_charges';
    protected $fillable = array('mp_charges_id','mp_key','mp_id','service_type_id', 'charges_from_date', 'charges_to_date','updated_date','created_date','ed_fee','charges','charge_type','currency_id','is_recurring','recurring_interval','recurring_period');

    protected $dates = ['deleted_at'];
}
