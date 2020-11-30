<?php
namespace App\Modules\EmployeeAttendance\Controllers;
use App\Http\Controllers\BaseController;
use View;
use Log;
use Redirect;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Central\Repositories\RoleRepo;
use App\Modules\EmployeeAttendance\Models\Attendance;
use App\Modules\EmployeeAttendance\Models\GridAttendance;
use UserActivity;

class AttendanceGridController extends BaseController {
     public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });    

            $this->_roleRepo = new RoleRepo();
            $this->_Attendance = new Attendance();
            $this->_GridAttendance = new GridAttendance();
            $this->grid_field_db_match = array(
                    'db_date' => 'ca.db_date',
                    'in_time' => 'ea.in_time',
                    'out_time' => 'ea.out_time',
                    'total_hours' => 'ea.total_hrs',
                    'productive_hours' => 'ea.productive_hrs'
                );
            //$this->_InventorySnapshot = new InventorySnapshot();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function myattendanceIndex(){
        $userId = Session::get('userId');  
        $allEmps = "";
        $allEmps = $this->_Attendance->getSubordinatesByUserId($userId);
        $allEmps = json_decode(json_encode($allEmps), true);
        $emp_code = DB::table('employee')->join('users','employee.emp_id','=','users.emp_id')->where('user_id',$userId)->select('employee.emp_code')->first();
        $emp_code = isset($emp_code)?$emp_code->emp_code:0;
        $pendingHours = DB::select("CALL get_employeeDynamicDashboard($emp_code)");
        if(count($pendingHours)>0){
            $dashboard = $pendingHours[0]->Emp_Dashboard;
            $dashboard = json_decode($dashboard,1);
            $total_deviation='00:00:00';
            $morehoursdata=1;
            $upcoming_holiday='';
            $today_birthday=[];
            $upcoming_birthday=[];
            foreach ($dashboard as $index => $keyval) {
                $empdata = $keyval[0];
                if($empdata['key'] == 'TOTAL DEVIATION'){
                    if($empdata['val']){
                        $morehours = explode('-', $empdata['val']);
                        $morehoursdata=isset($morehours[0])?$morehours[0]:'';
                        $total_deviation=$empdata['val'];
                    }
                }
                if($empdata['key'] == 'UPCOMING HOLIDAY'){
                    if($empdata['val']){
                        $upcoming_holiday=$empdata['val'];
                    }
                }
                if($empdata['key'] == 'TODAY BIRTHDAY'){
                    if($empdata['val']){
                        $multiplebday = explode(',', $empdata['val']);
                        $today_birthday=$multiplebday;
                    }
                }    
                if($empdata['key'] == 'UPCOMING BIRTHDAY'){
                    if($empdata['val']){
                        $multiplebday = explode(',', $empdata['val']);
                        $upcoming_birthday=$multiplebday;
                    }
                }
            }
        }
        $today_birthday = array_filter($today_birthday);
        $upcoming_birthday = array_filter($upcoming_birthday);

        return view('EmployeeAttendance::attendanceReports',['allEmps'=>$allEmps,'total_deviation'=>$total_deviation,'morehoursdata'=>$morehoursdata,'upcoming_holiday'=>$upcoming_holiday,'today_birthday'=>$today_birthday,'upcoming_birthday'=>$upcoming_birthday]);
    }
    public function getAttendanceGrideData(Request $request){
        try{
            $data=array();
            $userId = Session::get('userId');
            $emp_code = $this->getEmpIdByUserid($userId);
            $pageData = $request->all();
            $from_date = isset($request->from_date)?date('Y-m-d', strtotime($request->from_date)):date('Y-m-d', strtotime("-10 days"));
            $to_date = isset($request->to_date)?date('Y-m-d', strtotime($request->to_date)):date('Y-m-d', strtotime("-1 days"));
            $page = $request->page;
            $pageSize = $request->pageSize;
            $orderby_array = array();
            if($page == 0)
            {
                $page = $page;
            }else
            {
                $page = $page*$pageSize;
            }
            if ($request->input('$orderby')){
            $order = explode(' ', $request->input('$orderby'));
            $order_query_field = $order[0];
            $order_query_type = $order[1]; //type
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->grid_field_db_match[$order_query_field])) {
                $order_by = $this->grid_field_db_match[$order_query_field];
                $orderby_array = $order_by . " " . $order_by_type;
            }

            }
            $count = "0";
            if($emp_code!='')
            {
                $dataArray = array("from_date"=>$from_date,"to_date"=>$to_date);
                $count_data = $this->_Attendance->AttendanceByDateEmpCode($dataArray,$emp_code);
                $data = $this->_GridAttendance->GridAttendanceByDateEmpCode($dataArray,$emp_code,$page,$pageSize,$orderby_array);
                $count = count($count_data);
            }
            $data = json_encode(array('Records' => $data,'TotalRecordsCount' =>$count));
            return $data;
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    public function getEmpIdByUserid($id)
    {
        try{
            $rsQuery = DB::table('users')
                    ->where('user_id',$id)
                    ->where('is_active',1)
                    ->pluck('emp_code');
            return $rsQuery[0];
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
        
    }
    public function allEmpAttendanceReports()
    {
        $userId = Session::get('userId');  
        $allEmps = "";
        $parentLevelEmps = "";
        if($is_hr_manager == 1)
        {
            $allEmps = $this->_Attendance->getSubordinatesByUserId($userId);
            $allEmps = json_decode(json_encode($allEmps), true);
        }  
        return view('EmployeeAttendance::allEmployeeAttendance',["allEmps" => $allEmps,"parentLevelEmps" => $parentLevelEmps]);
    }
     public function getAllAttendanceGrideData(Request $request)
    {
        $data=array();
        $pageData = $request->all();
        $emp_code = $request->emp_code;
        $from_date = isset($request->from_date)?date('Y-m-d', strtotime($request->from_date)):date('Y-m-d', strtotime("-10 days"));
        $to_date = isset($request->to_date)?date('Y-m-d', strtotime($request->to_date)):date('Y-m-d', strtotime("-1 days"));
        $page = $request->page;
        $pageSize = $request->pageSize;
        $orderby_array = array();
        if($page == 0)
        {
            $page = $page;
        }else
        {
            $page = $page*$pageSize;
        }      
        if ($request->input('$orderby')){
            $order = explode(' ', $request->input('$orderby'));
            $order_query_field = $order[0];
            $order_query_type = $order[1]; //type
            $order_by_type = 'desc';
        if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
        if (isset($this->grid_field_db_match[$order_query_field])){
                $order_by = $this->grid_field_db_match[$order_query_field];
                $orderby_array = $order_by . " " . $order_by_type;
            }

        }
         $count="0";
        if($emp_code!='')
        {
            $dataArray = array("from_date"=>$from_date,"to_date"=>$to_date);
            $count_data = $this->_Attendance->AttendanceByDateEmpCode($dataArray,$emp_code);
            $data = $this->_GridAttendance->GridAttendanceByDateEmpCode($dataArray,$emp_code,$page,$pageSize,$orderby_array);
            $count = count($count_data);
        }
        $data = json_encode(array('Records' => $data,'TotalRecordsCount' =>$count));
        return $data;
    }
}