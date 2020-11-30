<?php

namespace App\models\GdsOrders;

use Illuminate\Database\Eloquent\Model;
class GDSOrders extends Model {

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
        //use UserTrait, RemindableTrait;
        protected $primaryKey = 'gds_order_id';
        public $timestamps = false;
        protected $table = 'gds_orders';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
       /* protected $fillable = array('mp_id','mp_order_id','legal_entity_id','order_status_id',
            'order_date','ship_total','sub_total', 'tax_total', 'total', 'gds_cust_id',
            'firstname','lastname','email','phone_no');*/

	
}
