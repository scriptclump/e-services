<?php

namespace App\Modules\LeaveManagement\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
use Redirect;
use UserActivity;
use Illuminate\Support\Facades\Input;
use App\Modules\LeaveManagement\Models\LeaveManagement;

class LeaveManagementController extends BaseController {

    public function __construct() {
        try {
            parent::Title('Crate Management');
            $this->_leaveManagement = new LeaveManagement();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function leaveMasterInfo(Request $request) {
        try {
            $leaveinfo_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Leave Management", "leavemasterinfo", $request->input("data"), "leavemasterinfo api was requested", "");
            if($leaveinfo_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_leaveManagement->authenticatToken($leaveinfo_params["lp_token"]);
            if(!empty($token_check) && isset($token_check['emp_id']) && $token_check['emp_id']!=''){
                $res_leave_info = $this->_leaveManagement->leaveMasterInformation($token_check);
                UserActivity::apiUpdateActivityLog($last_id, $res_leave_info);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_leave_info) ? "Leave Management Information" : "No data", "data" => !empty($res_leave_info) ? $res_leave_info : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function leaveRequest(Request $request) {
        try {
            $leave_request_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Leave Management", "leaverequest", $request->input("data"), "leaverequest api was requested", "");
            if($leave_request_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_leaveManagement->authenticatToken($leave_request_params["lp_token"]);
            if(!empty($token_check && isset($token_check['emp_id']) && $token_check['emp_id']!='')){
                if(!isset($leave_request_params['leave_type']) || $leave_request_params['leave_type'] == ""){
                    UserActivity::apiUpdateActivityLog($last_id, "leave_type is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "leave_type is missing", "data" => array()));
                } elseif(!isset($leave_request_params['from_date']) || $leave_request_params['from_date'] == ""){
                    UserActivity::apiUpdateActivityLog($last_id, "from_date is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "from_date is missing", "data" => array()));
                } elseif(!isset($leave_request_params['to_date']) || $leave_request_params['to_date'] == ""){
                    UserActivity::apiUpdateActivityLog($last_id, "to_date is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "to_date is missing", "data" => array()));
                } elseif(!isset($leave_request_params['no_of_days']) || $leave_request_params['no_of_days'] == ""){
                    UserActivity::apiUpdateActivityLog($last_id, "no_of_days is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "no_of_days is missing", "data" => array()));
                } elseif(!isset($leave_request_params['reason']) || $leave_request_params['reason'] == ""){
                    UserActivity::apiUpdateActivityLog($last_id, "reason is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "reason is missing", "data" => array()));
                } elseif(!isset($leave_request_params['emergency_number']) || $leave_request_params['emergency_number'] == ""){
                    UserActivity::apiUpdateActivityLog($last_id, "emergency_number is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "emergency_number is missing", "data" => array()));
                }

                $res_leave_request = $this->_leaveManagement->leaveRequestProcess($leave_request_params, $token_check);
                UserActivity::apiUpdateActivityLog($last_id, $res_leave_request);
                if($res_leave_request == 'Success'){
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_leave_request) ? "Leave Request Response" : "No data", "data" => !empty($res_leave_request) ? $res_leave_request : array()));
                }
                else if($res_leave_request == 'Failed')
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => $res_leave_request, "data" => array()));
                else if($res_leave_request == 'Optional Holiday Can Be Applied Only For One Day')
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => $res_leave_request, "data" => array()));
                else if($res_leave_request == 'No Optional Holiday On Applied Date')
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => $res_leave_request, "data" => array()));
                else if($res_leave_request == 'lfh error')
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "You are not allowed to apply Login from home on this date!", "data" => array()));

            }
            else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function daysCalculation(Request $request) {
        try {
            $days_calculation_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Leave Management", "dayscalculation", $request->input("data"), "dayscalculation api was requested", "");
            if($days_calculation_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_leaveManagement->authenticatToken($days_calculation_params["lp_token"]);
            if(!empty($token_check) && isset($token_check['emp_id']) && $token_check['emp_id']!=''){

                if(!isset($days_calculation_params['from_date']) || empty($days_calculation_params['from_date'])){
                    UserActivity::apiUpdateActivityLog($last_id, "from_date is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "from_date is missing", "data" => array()));
                } elseif(!isset($days_calculation_params['to_date']) || $days_calculation_params['to_date'] == ""){
                    UserActivity::apiUpdateActivityLog($last_id, "to_date is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "to_date is missing", "data" => array()));
                }

                $res_days_calculation = $this->_leaveManagement->noOfDaysCount($days_calculation_params,$token_check);

                UserActivity::apiUpdateActivityLog($last_id, $res_days_calculation);
                if(is_numeric($res_days_calculation)){
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_days_calculation) ? "Days Calculation Response" : "No data", "data" => !empty($res_days_calculation) ? $res_days_calculation : array()));
                } else{
                    return json_encode(array("status_code" => 402, "status" => "Failed", "message" => $res_days_calculation, "data" => array()));
                }

                
                
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function leaveHistory(Request $request) {
        try {
            $leave_history_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Leave Management", "leavehistory", $request->input("data"), "leavehistory api was requested", "");
            if($leave_history_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_leaveManagement->authenticatToken($leave_history_params["lp_token"]);
            if(!empty($token_check) && isset($token_check['emp_id']) && $token_check['emp_id']!=''){
                $res_leave_history = $this->_leaveManagement->leaveHistoryByEmpId($token_check);
                UserActivity::apiUpdateActivityLog($last_id, $res_leave_history);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_leave_history) ? "Leave Request Response" : "No data", "data" => !empty($res_leave_history) ? $res_leave_history : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getPendingApprovals(Request $request) {
        try {
            $leaveinfo_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Leave Management", "getPendingApprovals", $request->input("data"), "getPendingApprovals api requested", "");
            if($leaveinfo_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_leaveManagement->authenticatToken($leaveinfo_params["lp_token"]);
            if(!empty($token_check) && isset($token_check['emp_id']) && $token_check['emp_id']!=''){

                if(!isset($leaveinfo_params['leave_type']) || empty($leaveinfo_params['leave_type'])){
                    UserActivity::apiUpdateActivityLog($last_id, "leave_type is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "leave_type is missing", "data" => array()));
                }

                $res_leave_info = $this->_leaveManagement->pendingApprovalList($token_check['user_id'],$leaveinfo_params['leave_type']);

                UserActivity::apiUpdateActivityLog($last_id, $res_leave_info);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_leave_info) ? "Pending leave approvals" : "No data", "data" => !empty($res_leave_info) ? $res_leave_info : array()));

            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateLeaveStatus(Request $request) {
        try {

            $leaveinfo_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Leave Management", "updateLeaveStatus", $request->input("data"), "updateLeaveStatus api requested", "");
            if($leaveinfo_params["lp_token"] == ""){
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            
            $token_check = $this->_leaveManagement->authenticatToken($leaveinfo_params["lp_token"]);

            if(!empty($token_check) && isset($token_check['emp_id']) && $token_check['emp_id']!=''){

                if(!isset($leaveinfo_params['leave_id']) || empty($leaveinfo_params['leave_id'])){
                    UserActivity::apiUpdateActivityLog($last_id, "leave_id is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "leave_id is missing", "data" => array()));
                } elseif(!isset($leaveinfo_params['status']) || $leaveinfo_params['status'] == ""){
                    UserActivity::apiUpdateActivityLog($last_id, "status is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "status is missing", "data" => array()));
                }

                $res_leave_info = $this->_leaveManagement->updateStatus($token_check['user_id'],$leaveinfo_params);

                UserActivity::apiUpdateActivityLog($last_id, $res_leave_info);
                if($res_leave_info=='Approved' || $res_leave_info=='Rejected' || $res_leave_info == 'Withdrawn')
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => "Leave Status Update", "data" => $res_leave_info));
                else
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => $res_leave_info, "data" => array()));

            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function pendingApprovalNotifications() {
        try {
            $this->_leaveManagement->sendPlNotifications();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}