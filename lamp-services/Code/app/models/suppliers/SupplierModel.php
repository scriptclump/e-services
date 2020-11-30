<?php

namespace App\models\suppliers;

use Illuminate\Database\Eloquent\Model;

class SupplierModel extends Model
{
    //
     protected $table = 'users';
     protected $fillable = array('user_name', 'firstname', 'lastname','email_id');
     public $timestamps = false;
}


