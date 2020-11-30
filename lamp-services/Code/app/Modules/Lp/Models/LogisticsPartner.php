<?php

namespace App\Modules\Lp\Models;

use Illuminate\Database\Eloquent\Model;

class LogisticsPartner extends Model
{
    public $timestamps = false;
        protected $fillable = [
        'lp_name', 'lp_legal_name','description','address_1','address_2', 'city', 'state', 
            'country', 'pincode', 'phone','email','website','api_password', 'services', 'shipping_method',
            'serviceability', 'api_username', 'api_apikey',
    ];
}
