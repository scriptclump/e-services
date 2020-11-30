<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
        public $timestamps = false;
        protected $fillable = ['business_unit_id','user_name','password','firstname','lastname','email_id','mobile_no','landline_no','landline_ext','profile_picture','is_active','created_by','created_on','modified_by','modified_on','legal_entity_id','otp','password_token','is_email_verified'];
	protected $table = "users";
	protected $primaryKey = 'user_id';
}
