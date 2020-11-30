<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class VwBrandwiseDetailsModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['brand_name','status','IS Trademarked','Authorised','Products','legal_entity_id','WithImages','WithoutImages','withInventory','WithoutInventory','approved','pending'];
	protected $table = "vw_brandwisedetails";

}
