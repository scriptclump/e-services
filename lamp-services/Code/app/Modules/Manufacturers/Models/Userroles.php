<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class Userroles extends Model
{
        public $timestamps = false;
        protected $fillable = ['role_id','user_id'];
	protected $table = "user_roles";
	protected $primaryKey = 'user_roles_id';
}
