<?php
namespace App\models\EmailTemplate;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class EmailTemplate extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'email_templates';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
        
        protected $primaryKey  = 'email_id';

}
