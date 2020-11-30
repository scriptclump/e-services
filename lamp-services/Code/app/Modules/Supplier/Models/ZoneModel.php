<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['zone_id','country_id','name','code','status','created_by','created_at','updated_by','updated_at'];
    protected $table = "zone";
}
