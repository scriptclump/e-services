<?php

namespace App\Modules\LeaveManagement\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Notifications;
use UserActivity;
use Utility;
use Log;
use Session;
use App\Modules\Roles\Models\Role;
use App\Central\Repositories\ProductRepo;
use \App\Central\Repositories\RoleRepo;

class LeaveManagement extends Model {

    public function __construct(){    
        $this->objPushNotification = new ProductRepo();
        $this->roleAccess = new RoleRepo();
    }

    public function authenticatToken($lpToken) {
        try {
            $ids = DB::table("employee as emp")->join("users as u", function ($join) {
                        $join->on("emp.emp_id", "=", "u.emp_id");
                    })->where("u.is_active", 1)->where("emp.is_active", 1)
                    ->where(function($query) use ($lpToken){
                        $query->where('u.lp_token',$lpToken)
                              ->orWhere('u.password_token',$lpToken);
                    })
                    ->get(["emp.emp_id", "emp.emp_group_id", "u.user_id"])->all();
                    $data=json_decode(json_encode($ids), true);
                    if(count($ids)>0){
                        return json_decode(json_encode($ids), true)[0];
                    }else{
                        return array();
                    }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function leaveMasterInformation($userEmpId) {
        try {
            $types = DB::table("master_lookup")
                    ->whereIn("mas_cat_id", [148, 149])
                    ->get(["mas_cat_id", "master_lookup_name", "value"])->all();

            foreach ($types as $each_value) {
                if ($each_value->mas_cat_id == 148) {
                    if($each_value->value != 148003)
                        $leave_info["leave_type"][$each_value->value] = $each_value->master_lookup_name;
                } else if ($each_value->mas_cat_id == 149) {
                    $leave_info["leave_reason_type"][$each_value->value] = $each_value->master_lookup_name;
                }
            }

            $leave_info["holiday_list"] = DB::table("holiday_list")->whereIn('holiday_type',[1])->where('emp_group_id',$userEmpId['emp_group_id'])->whereRaw('YEAR(holiday_date)=YEAR(CURDATE())')->pluck("holiday_name", "holiday_date")->all();

            $leave_info["current_leave_count"] = DB::table("leave_master")->where("emp_id", $userEmpId["emp_id"])
                            ->select(DB::raw("getMastLookupValue(leave_type) as leave_type"), "no_of_leaves")->get()->all();
            $leave_info["optional_holiday_list"] = DB::table("holiday_list")->select("holiday_name", "holiday_date","holiday_list_id")->whereIn('holiday_type',[0])->where('emp_group_id',$userEmpId['emp_group_id'])->whereRaw('YEAR(holiday_date)=YEAR(CURDATE())')->get()->all();

            return $leave_info;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function leaveRequestProcess($leaveRequestParams, $userEmpId) {
        try {
            if($leaveRequestParams["leave_type"] == 148005){
                if($leaveRequestParams["from_date"] != $leaveRequestParams["to_date"]){
                    return "Optional Holiday Can Be Applied Only For One Day";
                }
                else{
                    $date = $this->checkIsOptional($leaveRequestParams["from_date"]);
                    if(empty($date)){
                        return "No Optional Holiday On Applied Date";   
                    }
                }
            }
            if($leaveRequestParams["leave_type"] == 148007){
                $data = DB::table('employee')->where('emp_id',$userEmpId['emp_id'])->first();
                $empdata = DB::table('emp_attendance')
                ->where('date',$leaveRequestParams['from_date'])
                ->where('emp_id',$data->emp_code)
                ->count();
                if($empdata >0){
                    $hours= isset($leaveRequestParams["hours"]) ? $leaveRequestParams["hours"] : 0;
                }else{
                    return 'lfh error';
                }
            }else{
                $hours=0;
            }
            $insertArray = array("emp_id" => $userEmpId["emp_id"],
                "leave_type" => $leaveRequestParams["leave_type"],
                "from_date" => $leaveRequestParams["from_date"],
                "to_date" => $leaveRequestParams["to_date"],
                "no_of_days" => $leaveRequestParams["no_of_days"],
                "reason" => $leaveRequestParams["reason"],
                "contact_number" => $leaveRequestParams["emergency_number"],
                "status" => 57163,
                "created_by" => $userEmpId["emp_id"],
                "created_at" => date('Y-m-d H:i:s'),
                "module_name"  => isset($leaveRequestParams["module_name"]) ? $leaveRequestParams["module_name"] : "",
                "project_name"  => isset($leaveRequestParams["project_name"]) ? $leaveRequestParams["project_name"] : "",
                "hours"  => $hours
            );
            $insert_result = DB::table("leave_history")->insertGetId($insertArray);

            if($insert_result != ''){
                $deductLeave =  DB::table('leave_master')
                    ->where('emp_id',$userEmpId["emp_id"])
                    ->where('leave_type',$leaveRequestParams["leave_type"])
                    ->decrement('no_of_leaves',$leaveRequestParams["no_of_days"]);

                $userId = DB::table('employee')
                    ->where('emp_id',$userEmpId["emp_id"])
                    ->get(array('reporting_manager_id', "firstname", "lastname"))->all();

                $userId = json_decode(json_encode($userId), true);
                
                if(!empty($userId)){
                    $empName = $userId[0]['firstname']." ".$userId[0]['lastname'];
                    $uId = $userId[0]['reporting_manager_id'];

                    $RegId = $this->getRegIds($uId);
                    $leave_name = DB::select("select getMastLookupValue('".$leaveRequestParams["leave_type"]."') as name");
                    $leave_name = json_decode(json_encode($leave_name), true);
                     //return $RegId;
                    $tokenDetails = json_decode((json_encode($RegId)), true);

                    $message = $empName.' has applied '.$leave_name[0]['name'].' for'.$leaveRequestParams["no_of_days"].' day(s).';
                    $pushNotification = $this->objPushNotification->pushNotifications($message, $tokenDetails, "LeaveManagement");
                }
                return "Success";
            } else
                 return "failed";
            
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function checkIsOptional($from_date){
        $date = DB::table('holiday_list')
                ->where('holiday_date',$from_date)
                ->where('holiday_type',0)
                ->get()->all();
        return $date; 
    }

    public function noOfDaysCount($daysCalculationParams, $empData) {
        try {
            //DB::enablequerylog();
            $weekEnds = DB::table('emp_groups as eg')
                    ->where('eg.emp_group_id',$empData['emp_group_id'])
                    ->select('eg.weekend_one','eg.weekend_two')
                    ->get()->all();
            $weekEnds = json_decode(json_encode($weekEnds), true);

            $holidays = DB::table('holiday_list')
                    ->where('emp_group_id',$empData['emp_group_id'])
                    ->whereBetween('holiday_date', array($daysCalculationParams['from_date'],$daysCalculationParams['to_date']))
                    ->where('holiday_type',1)
                    ->pluck('holiday_date')->all();

            $start = date('Y-m-d', strtotime($daysCalculationParams['from_date']));
            $end = date('Y-m-d', strtotime($daysCalculationParams['to_date']));

            $dates = array();
            if(!in_array(date('l', strtotime($start)), $weekEnds[0]) && !in_array($start, $holidays))
                $dates[] = $start;
            while($start < $end){
                $start = date('Y-m-d', strtotime("+1 day", strtotime($start)));
                if(!in_array(date('l', strtotime($start)), $weekEnds[0]) && !in_array($start, $holidays))
                    $dates[] = $start;
            }

            $appliedLeaves = DB::select(DB::raw("SELECT db_date FROM emp_calendar WHERE EXISTS (SELECT 1 FROM leave_history lh 
WHERE db_date BETWEEN lh.`from_date` AND lh.`to_date` AND lh.emp_id = ".$empData['emp_id']." and lh.status in (57164,57163)) AND 
db_date BETWEEN '".date('Y-m-d', strtotime($daysCalculationParams['from_date']))."' AND '".date('Y-m-d', strtotime($daysCalculationParams['to_date']))."'"));

            $appliedLeaves = json_decode(json_encode($appliedLeaves), true);
            $applied = array();
            foreach($appliedLeaves as $leave)
                $applied[] = $leave['db_date'];
            $check = array_intersect($applied,$dates);
            if(!empty($check) || count($check)>0){
                return "You have existing applied leaves for selected dates!";
            } else{
                return count($dates);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function leaveHistoryByEmpId($userEmpId) {
        try {
            $leave_history = DB::table("leave_history")
                    ->where("emp_id", $userEmpId["emp_id"])
                    ->select("emp_id", "leave_history_id", DB::raw("getMastLookupValue(leave_type) as leave_type"), "from_date", "to_date", "no_of_days", "status")
                    ->orderBy("leave_history_id","desc")
                    ->get()->all();
            return $leave_history;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function pendingApprovalList($mngrId,$type) {
        try {
            //DB::enablequerylog();
            if($type==1)
                $status = array(57163);
            else
                $status = array(57164);
            $user_id = Session::get("userId");
            $Hraccess = $this->roleAccess->checkPermissionByFeatureCode('HRL001');
            $pendingLeaves = DB::table("employee AS e")
                            ->join('leave_history AS lh', 'lh.emp_id', '=', 'e.emp_id')
                            ->whereIn("lh.status", $status)
                            ->select('e.emp_id',
                                    'e.emp_code',
                                    DB::raw("CONCAT(e.firstname,' ',e.lastname) as emp_name"),
                                    'e.firstname','e.lastname',
                                    'lh.leave_history_id',
                                    DB::raw("getMastLookupValue(lh.leave_type) as leave_type"),
                                    DB::raw(" (CASE WHEN leave_type = 148005 THEN getOptionalLeaveName(DATE_FORMAT(from_date, '%Y-%m-%d') ) ELSE   getMastLookupValue (reason) END)AS reason"),
                                    'lh.no_of_days',
                                    'lh.from_date',
                                    'lh.to_date',
                                    DB::raw("getMastLookupValue(lh.status) as status"))
                            ->orderBy("lh.leave_history_id","desc");
            if(!$Hraccess)
                $pendingLeaves = $pendingLeaves->where("e.reporting_manager_id", $mngrId)->get()->all();
            elseif($Hraccess && $type ==1)
                $pendingLeaves = $pendingLeaves->get()->all();
            elseif($Hraccess && $type !=1)
                $pendingLeaves = $pendingLeaves->where("lh.updated_by",$user_id)->get()->all();
            // $sql = DB::getQueryLog();
            return $pendingLeaves;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function updateStatus($userId, $data) {
        try {
            //DB::enablequerylog();
            $pendingLeaves = DB::table("leave_history AS lh")
                    ->whereIn("lh.leave_history_id", $data['leave_id'])
                    ->where("lh.status",57163)
                    ->select("lh.leave_history_id","lh.emp_id","lh.no_of_days","lh.leave_type")
                    ->get()->all();
            $pendingLeaves = json_decode(json_encode($pendingLeaves), true);
            if(empty($pendingLeaves))
                return "Failed";

            foreach($pendingLeaves as $pending){
                $updateLeaves = DB::table("leave_history AS lh")
                    ->where("lh.leave_history_id", $pending['leave_history_id'])
                    ->update(['lh.status'=>$data['status'],'lh.updated_by'=>$userId]);

                if($data['status'] == 57165 || $data['status'] == 57166){
                    $increaseLeave =  DB::table('leave_master')
                        ->where('emp_id',$pending["emp_id"])
                        ->where('leave_type',$pending["leave_type"])
                        ->increment('no_of_leaves',$pending["no_of_days"]);
                    if($data['status'] == 57166){
                        $result = 'Withdrawn';
                    }    
                }

                if($data['status'] == 57164 || $data['status'] == 57165){
                    $RegId = $this->getRegIds($pending["emp_id"]);

                    $tokenDetails = json_decode((json_encode($RegId)), true);

                    if($data['status'] == 57164)
                        $result = 'Approved';
                    else
                        $result = 'Rejected';
                    $message = 'Your Leave is '.$result.' by Reporting Manager!';

                    $pushNotification = $this->objPushNotification->pushNotifications($message, $tokenDetails, "LeaveManagement");
                }
            }
            // $sql = DB::getQueryLog();
            // print_r(end($sql)); exit;
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function getRegIds($userIds){

        $sqlUser = "select registration_id, platform_id FROM device_details WHERE user_id IN (".$userIds.")";
        $allData = DB::select(DB::raw($sqlUser));
        return $allData;
    }
    
    public function sendPlNotifications() {
        $userIds = DB::table("users AS u")
                ->join("leave_history AS lh", "lh.emp_id", "=", "u.emp_id")
                ->where("lh.status", 57163)
                ->groupBy("u.reporting_manager_id")
                ->pluck("u.reporting_manager_id")->all();
        $userIds = json_decode(json_encode($userIds), true);

        if (!empty($userIds)) {
            foreach ($userIds as $eachId) {
                $RegId = $this->getRegIds($eachId);

                $tokenDetails = json_decode((json_encode($RegId)), true);

                $message = 'Leave(s) pending for your approval.';

                $this->objPushNotification->pushNotifications($message, $tokenDetails, "LeaveManagement");
            }
        }
    }


    public function webleaveRequestProcess($leaveRequestParams, $userEmpId) {

        try {
            if($leaveRequestParams["leave_type"] == 148005){
                $leaveRequestParams["reason"] = $leaveRequestParams["optional_leave"];
            }else{
                $leaveRequestParams["reason"] = $leaveRequestParams["normal_leave"];
            }
            if($leaveRequestParams["leave_type"] == 148007){
                //echo 'hiii';exit;
                $data = DB::table('employee')->where('emp_id',$userEmpId['emp_id'])->first();
                $empdata = DB::table('emp_attendance')
                ->where('date',$leaveRequestParams['from_date'])
                ->where('emp_id',$data->emp_code)
                ->count();
                if($empdata >0){
                    $hours= isset($leaveRequestParams["hours"]) ? $leaveRequestParams["hours"] : 0;
                }else{
                    return 'lfh error';
                }
            }else{
                $hours = 0;
            }
            $insertArray = array("emp_id" => $userEmpId["emp_id"],
                "leave_type" => $leaveRequestParams["leave_type"],
                "from_date" => $leaveRequestParams["from_date"],
                "to_date" => $leaveRequestParams["to_date"],
                "no_of_days" => $leaveRequestParams["no_of_days"],
                "reason" => $leaveRequestParams["reason"],
                "contact_number" => $leaveRequestParams["emergency_number"],
                "status" => 57163,
                "created_by" => $userEmpId["emp_id"],
                "created_at" => date('Y-m-d H:i:s'),
                "module_name"  => isset($leaveRequestParams["module_name"]) ? $leaveRequestParams["module_name"] : "",
                "project_name"  => isset($leaveRequestParams["project_name"]) ? $leaveRequestParams["project_name"] : "",
                "hours"  => $hours
            );
            $insert_result = DB::table("leave_history")->insertGetId($insertArray);

            if($insert_result != ''){
                if(isset($leaveRequestParams['hr_id'])){
                    DB::table('leave_history')
                        ->where('from_date',$leaveRequestParams["from_date"])
                        ->where('to_date',$leaveRequestParams["to_date"])
                        ->update(['created_by'=>$leaveRequestParams['hr_id']]);
                }
                $deductLeave =  DB::table('leave_master')
                    ->where('emp_id',$userEmpId["emp_id"])
                    ->where('leave_type',$leaveRequestParams["leave_type"])
                    ->decrement('no_of_leaves',$leaveRequestParams["no_of_days"]);

                $userId = DB::table('employee')
                    ->where('emp_id',$userEmpId["emp_id"])
                    ->get(array('reporting_manager_id', "firstname", "lastname"))->all();

                $userId = json_decode(json_encode($userId), true);
                
                if(!empty($userId)){
                    $empName = $userId[0]['firstname']." ".$userId[0]['lastname'];
                    $uId = $userId[0]['reporting_manager_id'];

                    $RegId = $this->getRegIds($uId);
                    $leave_name = DB::select("select getMastLookupValue('".$leaveRequestParams["leave_type"]."') as name");
                    $leave_name = json_decode(json_encode($leave_name), true);
                    //return $RegId;
                    $tokenDetails = json_decode((json_encode($RegId)), true);

                    $message = $empName.' has applied '.$leave_name[0]['name'].' for'.$leaveRequestParams["no_of_days"].' day(s).';

                    //$pushNotification = $this->objPushNotification->pushNotifications($message, $tokenDetails, "LeaveManagement");
                }
                return "Success";
            } else
                return "failed";
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

}
