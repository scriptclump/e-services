<?php

namespace App\models\BusinessUnit;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

Class BusinessUnit extends Model {

    public $timestamps = false;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'business_units';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
     
    protected $fillable = array('business_unit_id','name','manufacturer_id','is_active','description');

}
