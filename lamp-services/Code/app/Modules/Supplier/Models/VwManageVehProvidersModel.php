<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class VwManageVehProvidersModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_name','contact','sup_rm','Documents','created_by','created_at','approvedby','Approvedon','is_active'];
	protected $table = "vw_managevehproviders";



}
