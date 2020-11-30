<?php

namespace App\Modules\Retailer\Models;

use Illuminate\Database\Eloquent\Model;

class countries extends Model
{
    public $timestamps = false;
    protected $fillable = ['country_id','name','iso_code_2','iso_code_3','address_format','postcode_required','status'];
    protected $table = "countries";
}
