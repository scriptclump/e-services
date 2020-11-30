<?php
namespace App\Modules\HrmsEmployees\Models;
use App\Central\Repositories\RoleRepo;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use URL;
use \App\Modules\Roles\Models\Role;

class AttendanceModel extends Model
{
    protected $table = 'emp_attendance';
    protected $primaryKey = 'id';
    public function __construct()
    {
        $this->roleRepo = new RoleRepo();
    }

}
