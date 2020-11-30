<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class VwManfBrandsModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['brand_name','status','IS Trademarked','Authorized','Products','manufacturer_id','WithImages','WithoutImages','withInventory','WithoutInventory','approved','pending'];
	protected $table = "vw_brand_details";



}
