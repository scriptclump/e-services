<?php

namespace App\Modules\Lp\Models;

use Illuminate\Database\Eloquent\Model;

class lpWarehouses extends Model
{
    public $timestamps = false;
    protected $fillable = ['lp_id','lp_wh_code','lp_wh_name','address1','state','city','pincode','longitude','latitude','landmark','phone_no','email','country'];
}
