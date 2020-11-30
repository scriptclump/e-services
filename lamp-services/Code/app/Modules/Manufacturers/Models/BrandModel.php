<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
class BrandModel extends Model
{

    public $timestamps = true;
    protected $fillable = ['brand_id', 'legal_entity_id', 'brand_name', 'description', 'is_active', 'is_global', 'is_authorized', 'logo_url'];
    protected $table = "brands";
    protected $primaryKey = 'brand_id';

    public function getPurchaseManager() {
        $pm_data = DB::table('roles')
                ->select(DB::raw("concat(users.firstname, ' ', users.lastname) as username"), 'users.user_id as id')
                ->Join('user_roles', 'roles.role_id', '=', 'user_roles.role_id')
                ->Join('users', 'user_roles.user_id', '=', 'users.user_id')
                ->where('roles.name', 'Key Account Manager')
                ->where(array('users.is_active' => 1, 'roles.is_deleted' => 0))
                ->orderBy('users.user_id', 'ASC')
                ->get()->all();
        return $pm_data;
    }	    
    
}
