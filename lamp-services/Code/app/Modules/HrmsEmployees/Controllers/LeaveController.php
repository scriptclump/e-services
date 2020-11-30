<?php

namespace App\Modules\HrmsEmployees\Controllers;

use URL;
use Log;
use View;
use File;
use Excel;
use Session;
use Response;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use \App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\ProductRepo;
use \App\Central\Repositories\GlobalRepo;
use \App\Central\Repositories\CustomerRepo;
use App\Modules\LeaveManagement\Models\LeaveManagement;
use App\Modules\Assets\Controllers\commonIgridController;
use \App\Modules\HrmsEmployees\Models\EmpLeaveManageModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;



Class LeaveController extends BaseController {

    public $roleAccess;
    protected $user_grid_fields;

    public function __construct(RoleRepo $roleAccess, CustomerRepo $custRepoObj, Request $request) {
        $this->middleware(function ($request, $next) use ($roleAccess,$custRepoObj) {
            parent::__construct();
            if (!Session::has('userId')) {
                return Redirect::to('/');
            }
            $global = new GlobalRepo();
            $global->logRequest($request);
            $this->roleAccess = $roleAccess;
            $this->custRepoObj = $custRepoObj;
            $this->_approvalFlowMethod = new CommonApprovalFlowFunctionModel();
            $this->_leaveManage = new EmpLeaveManageModel();
            $this->_leave_manage = new LeaveManagement();
            $this->objCommonGrid = new commonIgridController();
            return $next($request);
        });
    }


    public function applyleaveManage(Request $request){
        $Hraccess = $this->roleAccess->checkPermissionByFeatureCode('HRL001');

        if($Hraccess){
            $employees = DB::table('employee')->select('emp_code',DB::raw('concat(firstname,
                    (CASE WHEN middlename IS NOT NULL THEN concat(" ",middlename) ELSE ""END)," ",lastname)as firstname'))->get()->all();
            $employee = json_decode(json_encode($employees),1);
            $leavetypes['leave_type'] = [];
            $leavetypes['leave_holiday_type'] = [];
            $leavetypes['leave_reason_type'] = [];
            $leavetypes['current_leave_count'] = [];
            parent::Title('HR Leave Manage');
            return view('HrmsEmployees::empLeaveManage')
                ->with(array('employee' => $employee,'leavetypes' => $leavetypes,'Hraccess' => $Hraccess,'employid' => 0,'emp_group_id' => 1));
        }else{
            $getemployeegroupid  = $this->_leaveManage->getEmployeegroupId(Session::get('userId'));
            if ($getemployeegroupid == 0) {
                    Redirect::to('/')->send();
                }
            $leavetypes = $this->_leaveManage->getLeaveTypes($getemployeegroupid['emp_id'],$getemployeegroupid['emp_group_id']);
            parent::Title('Leave Manage');
            parent::Breadcrumbs(array('Home' => '/', 'Time Management' => '#', 'Leave Manage' => '#'));
            return view('HrmsEmployees::empLeaveManage')
                   ->with(array('leavetypes' => $leavetypes, 'Hraccess' => $Hraccess, 'employid' => $getemployeegroupid['emp_id'],'emp_group_id' => $getemployeegroupid['emp_group_id']));
        }
    }

    public function employeeData($employeecode){
        $data = DB::table('employee')->select('emp_id','emp_group_id')->where('emp_code',$employeecode)->first();
        $data = (json_decode(json_encode($data),1));
        $leavetypes = $this->_leaveManage->getLeaveTypes($data['emp_id'],$data['emp_group_id']);
        $leavetypes['emp_id'] = $data['emp_id'];
        $leavetypes['emp_group_id'] = $data['emp_group_id'];
        return $leavetypes;
    }

    public function empApplyLeave(Request $request){

        $data = $request->input();
        if($data['leave_type'] == 148005){
            $data['from_date'] = $this->_leaveManage->getFromdate($data);
            $data['to_date'] = $data['from_date'];
        }
        $data['from_date'] = date('Y-m-d', strtotime($data['from_date']));
        $data['to_date'] = date('Y-m-d', strtotime($data['to_date']));
        $Hraccess = $this->roleAccess->checkPermissionByFeatureCode('HRL001');
        if(!$Hraccess){
            $getemployeegroupid  = $this->_leaveManage->getEmployeegroupId(Session::get('userId'));
            $empdata = array();
            $empdata['emp_id'] = $getemployeegroupid['emp_id'];
            $empdata['emp_group_id'] = $getemployeegroupid['emp_group_id'];
        }else{
            $empdata['emp_id'] = $data['employ_id'];
            $empdata['emp_group_id'] = $data['emp_group_id'];
            $data['hr_id'] = Session::get('userId');
            $data['hr_id'] = DB::table('users')->select('emp_id')->where('user_id',$data['hr_id'])->first();
            $data['hr_id']= $data['hr_id']->emp_id;
        }
        $countdays = $this->_leave_manage->noOfDaysCount($data,$empdata);
        if($countdays=="You have existing applied leaves for selected dates!"){
            return "you have already applied leaves for the following dates";
        }

        $leavecount = $this->_leaveManage->getLeaveTypes($empdata['emp_id'],$empdata['emp_group_id']);
            Log::info('remaining leaves'.$countdays);
            Log::info('applied no'.$leavecount['current_leave_count'][0]->no_of_leaves);
            $causual = 0;
            $optional = 0;
            $sick = 0;
            $leavecount = json_decode(json_encode($leavecount),1);
            foreach ($leavecount['current_leave_count'] as $key => $value) {
                if($value['leave_type'] == 'Optional Holiday'){
                    $optional = $value['no_of_leaves'];
                }if($value['leave_type'] == 'Casual Leave'){
                    $causual = $value['no_of_leaves'];
                }if($value['leave_type'] == 'Sick Leave'){
                    $sick = $value['no_of_leaves'];
                }
            }
            if($data['leave_type'] == 148002 && $countdays > $causual){
                return "causual error";
            }else if($data['leave_type'] == 148001 && $countdays > $sick){
                return "sick error";
            }else if($data['leave_type'] == 148005 && $countdays > $optional){
                return "optional error";
            }
            

        $data['no_of_days'] = $countdays; 
        $request = $this->_leave_manage->webleaveRequestProcess($data,$empdata);
        return $request;
    }

        public function getAllTheappliedLeaves($empid,Request $request){
            $makeFinalSql = array();            
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }
            // make sql for from_date
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("from_date", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for to_date
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("to_date", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for contact_number
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("contact_number", $filter,false);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for #Days
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("no_of_days", $filter,false);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $orderBy = "";
            $orderBy = $request->input('%24orderby');
            if($orderBy==''){
                $orderBy = $request->input('$orderby');
            }
            // Arrange data for pagination
            $page="";
            $pageSize="";
            if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
                $page = $request->input('page');
                $pageSize = $request->input('pageSize');
            }
            $leavedata =  $this->_leaveManage->getapplyLeaveHistory($empid,$makeFinalSql, $orderBy, $page, $pageSize);
            return $leavedata; 
 
        }

        public function withdrawleave($leaveid){

        $data1 = array();
        $withdrawdata = $this->_leaveManage->getwithdrawdata($leaveid);
        $data1['leave_id'] = array($withdrawdata[0]->leave_history_id);
        $data1['status'] = 57166;

        $request = $this->_leave_manage->updateStatus(Session::get("userId"),$data1);

        return $request;
        }

        public function approvependingleaves(){
        $pendingleave = $this->roleAccess->checkPermissionByFeatureCode('EMPPEN001');
            if (!$pendingleave) {
                Redirect::to('/employee/dashboard')->send();
            }
             parent::Title('Employee Pending Leave(s)');
        return view('HrmsEmployees::emppendingleaves');
        }

        public function getEmployeeAppliedLeaveList($managerid){
            $request = $this->_leave_manage->pendingApprovalList($managerid,1);
            $dataArr = array();
            if(count($request)) {                            
                foreach($request as $leaves) {
                    $from_date = date('Y-m-d', strtotime($leaves->from_date));
                    $to_date = date('Y-m-d', strtotime($leaves->to_date));
                    $i =0;$day = [];
                    while ($from_date<=$to_date){
                        $day[$i] = date('D', strtotime($from_date));
                        $i++;
                        $from_date = date('Y-m-d',strtotime("+1 day", strtotime($from_date)));
                    }
                    $day = implode(',',$day);
                    $checkbox = '<input type="checkbox" name="chk[]" value="'.$leaves->leave_history_id.'">';
                    
                    $dataArr[] = array(
                            'chk'=>$checkbox,
                            'from_date'=>date("m/d/Y", strtotime($leaves->from_date)),
                            'to_date'=>date("m/d/Y", strtotime($leaves->to_date)),
                            'emp_code'=>$leaves->emp_code,
                            'employee_name'=>$leaves->emp_name ,
                            'leave_type'=>$leaves->leave_type,
                            'reason'=>$leaves->reason,
                            'no_of_days'=>$leaves->no_of_days,
                            'day' =>$day,
                    );
                }
                }

        return Response::json(array('results'=>$dataArr));
        }

        public function approveorreject($status,Request $request){
            $data = $request->input();
            $data['leave_id'] = $data['leave_id'];
            $data['status'] = $status;
            $request = $this->_leave_manage->updateStatus(Session::get("userId"),$data);
            $result = $request;
            return $result;
        }

        public function gethistory($managerid){

            $request = $this->_leave_manage->pendingApprovalList($managerid,2);

            $dataArr = array();
            if(count($request)) {                            
                foreach($request as $leaves) {
                    
                    $dataArr[] = array(
                            'from_date'=>date("m/d/Y", strtotime($leaves->from_date)),
                            'to_date'=>date("m/d/Y", strtotime($leaves->to_date)),
                            'emp_code'=>$leaves->emp_code,
                            'employee_name'=>$leaves->emp_name ,
                            'leave_type'=>$leaves->leave_type,
                            'reason'=>$leaves->reason,
                            'status'=>$leaves->status,
                            'no_of_days'=>$leaves->no_of_days,
                    );
                }
                }

        return Response::json(array('results'=>$dataArr));
        }

}