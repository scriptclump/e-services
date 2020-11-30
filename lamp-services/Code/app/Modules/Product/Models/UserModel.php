<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class UserModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "user_id";
    protected $table = "users";

}

