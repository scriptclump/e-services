<?php
namespace App\Modules\HrmsEmployees\Controllers;
use View;
use Session;
use Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BaseController;
use URL;
use Log;
use Response;
use Illuminate\Http\Request;
use Redirect;
use \App\Modules\HrmsEmployees\Models\AttendanceModel;
use \App\Central\Repositories\CustomerRepo;
use \App\Central\Repositories\RoleRepo;
use App\Central\Repositories\ProductRepo;
use \App\Central\Repositories\GlobalRepo;
use Illuminate\Support\Facades\DB;
Class AttendanceController extends BaseController {
    public $roleAccess;
    protected $user_grid_fields;
    
    public function __construct(RoleRepo $roleAccess, CustomerRepo $custRepoObj, Request $request) {
        $this->middleware(function ($request, $next) {
            parent::__construct();
            if (!Session::has('userId')) {
                return Redirect::to('/');
            }
            $global = new GlobalRepo();
            $global->logRequest($request);
            $this->roleAccess = $roleAccess;
            $this->custRepoObj = $custRepoObj;
            $this->attendanceModel = new AttendanceModel();
            return $next($request);
        });
    }
    public function myattendanceIndex()
    {
        return view('HrmsEmployees::attendanceReports');
    }
     public function allEmpAttendanceReports()
    {
        $hrPermission = $this->roleAccess->checkPermissionByFeatureCode('HRMSAT001');  
        $managerPermission = $this->roleAccess->checkPermissionByFeatureCode('HRMSMA001');
        $allEmps = "";
        $parentLevelEmps = "";
        if($hrPermission == 1)
        {
            $allEmps = DB::table('users')
                        ->join('employee','users.emp_id','=','employee.emp_id')
                        ->where('users.is_active',1)
                        ->select('users.emp_id','users.firstname','users.lastname')
                        ->get()->all();
            $allEmps = json_decode(json_encode($allEmps), true);
        }  
        return view('HrmsEmployees::allEmployeeAttendance',["allEmps" => $allEmps,"parentLevelEmps" => $parentLevelEmps]);
    }
    public function getAttendanceGrideData(Request $request)
    {
        $data=NULL;
        $userId = Session::get('userId');
        $emp_id = $this->getEmpIdByUserid($userId);
        $pageData = $request->all();
        $from_date = isset($request->from_date)?$request->from_date:"";
        $to_date = isset($request->to_date)?$request->to_date:"";
        $page = $request->page;
        $pageSize = $request->pageSize;
        if($emp_id!='')
        {
            $query = DB::table("emp_attendance")
                    ->where('emp_id',$emp_id);
            if($from_date!= "" && $to_date!= "")
            {
                $query->whereBetween('date', array($from_date, $to_date));
                $count = count($query->get()->all());
            }
            $query = $query
                    ->skip($page * $pageSize)
                    ->take($pageSize);
            if($from_date == "" && $to_date == "")
            {
                $count = count($query->get()->all());
            }
            $data =$query->get()->all();
        }
        $data = json_encode(array('Records' => $data,'TotalRecordsCount' =>$count));
        return $data;
    }
    public function getEmpIdByUserid($id)
    {
        $rsQuery = DB::table('users')
                    ->where('user_id',$id)
                    ->where('is_active',1)
                    ->pluck('emp_id')->all();
        return $rsQuery[0];
    }
    public function getAllAttendanceGrideData(Request $request)
    {
        $data=NULL;
        $pageData = $request->all();
        $emp_id = $request->emp_id;
        $from_date = isset($request->from_date)?$request->from_date:"";
        $to_date = isset($request->to_date)?$request->to_date:"";
        $page = $request->page;
        $pageSize = $request->pageSize;
        if($emp_id!='')
        {
            $query = DB::table("emp_attendance")
                    ->where('emp_id',$emp_id);
            if($from_date!= "" && $to_date!= "")
            {
                $query->whereBetween('date', array($from_date, $to_date));
            }
            $count = count($query->get()->all());
            $data = $query
                    ->skip($page * $pageSize)
                    ->take($pageSize)->get()->all();
        }
        $data = json_encode(array('Records' => $data,'TotalRecordsCount' =>$count));
        return $data;
    }

}
