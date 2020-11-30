<?php

namespace App\models\User;
use Illuminate\Database\Eloquent\Model;
use DB;
class User extends Model {

	
    public $timestamps = false;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
        
        protected $primaryKey  = 'user_id';

}
