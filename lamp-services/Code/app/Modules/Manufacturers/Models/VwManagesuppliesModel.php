<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class VwManageSuppliesModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_name','contact','sup_rm','Brands','Products','warehouses','Documents','created_by','created_at','approvedby','Approvedon','is_active'];
	protected $table = "vw_managesupplies";



}
