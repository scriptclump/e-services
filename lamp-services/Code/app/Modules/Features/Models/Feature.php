<?php

namespace App\Modules\Features\Models;


use Illuminate\Database\Eloquent\Model;

class Feature extends Model {

	
	public $timestamps = false ;
	protected $primaryKey = 'feature_id';
	protected $fillable = ['master_lookup_id','name','feature_code','description','created_by','created_on','modified_by','modified_on'];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'features';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

}
