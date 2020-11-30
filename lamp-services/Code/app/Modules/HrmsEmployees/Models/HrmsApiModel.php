<?php

namespace App\Modules\HrmsEmployees\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class HrmsApiModel extends Model {

    public function authenticatToken($lpToken) {
        try {
            $user_id = json_decode(json_encode(DB::table("users")->where("lp_token", $lpToken)->orWhere("password_token", $lpToken)->pluck("user_id")->all()), true);
            if (!empty($user_id)) {
                return $user_id[0];
            } else {
                return "Token not found";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getRolesByUserId($userId) {
        try {
            $role_ids = DB::table("user_roles")->where("user_id", $userId)->pluck("role_id")->all();
            if (!empty($role_ids)) {
                return $role_ids;
            } else {
                return "Role does not exist";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getTicketsByRoleId($roleId,$flag,$userId) {
        try {
           $role_ids = implode("|", $roleId);
            $isFinalQuery="";
            if ($flag==0) {
                $isFinalQuery = " AND appr_workflow_history.`is_final` = 0";
            }elseif ($flag==1) {
                $isFinalQuery = " AND appr_workflow_history.`is_final` = 1";
            }
            $raw_query="SELECT * FROM appr_workflow_history WHERE appr_workflow_history.`awf_history_id` IN
                (
                    SELECT (MAX(awf_history_id))
                         FROM appr_workflow_history AS apprinner
                    WHERE(
                            CONCAT(',', apprinner.`next_lbl_role`, ',') REGEXP ',(" . $role_ids . "),'
                            OR (apprinner.`created_by_manager` = $userId
                                AND apprinner.`next_lbl_role` = '75')
                            OR (apprinner.`ticket_created_by` = $userId
                            AND apprinner.`next_lbl_role` = '76')
                    )
                    GROUP BY apprinner.`awf_for_id`,apprinner.`awf_for_type_id` )
                    ".$isFinalQuery."
                    AND appr_workflow_history.`awf_for_type_id` = 56030 ORDER BY created_at DESC";
            $all_tickets = DB::select(DB::raw($raw_query));
            return $all_tickets;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function setSessionValues($userId) {
        $user_details = DB::table("users as u")
                ->join("user_roles as ur", function($join) {
                    $join->on("ur.user_id", "=", "u.user_id");
                })
                ->join("roles as r", function($join) {
                    $join->on("r.role_id", "=", "ur.role_id");
                })
                ->where("u.user_id", $userId)
                ->get(["u.user_id", "ur.role_id", "r.name", DB::raw("CONCAT(u.firstname, ' ', u.lastname) as user_name")])->all();
        $encoded_details = json_decode(json_encode($user_details), true);
        if ($encoded_details) {
            \Session::put('roleId', $encoded_details[0]["role_id"]);
            \Session::put('userName', $encoded_details[0]["user_name"]);
            return true;
        } else {
            return false;
        }
    }

    public function getEmployeeDetails($emp_id) {
        $empData = DB::table("employee")->where('emp_id', $emp_id)
                ->select("emp_id", "profile_picture", DB::raw("getMastLookupValue(employment_type) as employment_type"), "aadhar_number", "prefix", "firstname", "middlename", "lastname", "emp_code", "office_email", "email_id", "mobile_no", "alternative_mno", "landline_ext", "pan_card_number", "uan_number", DB::raw("getRolesNameById(role_id) as role_id"), DB::raw("GetUserName(reporting_manager_id, 2) as reporting_manager_id"), DB::raw("getMastLookupValue(department) as department"), DB::raw("getMastLookupValue(designation) as designation"), DB::raw("getEmpGroupName(emp_group_id) as emp_grop_id"), DB::raw("getBusinessUnitName(business_unit_id) as business_unit_id"), "doj", "dob", "gender", "marital_status", "nationality", "blood_group", "grade", "status", "is_active")
                ->get()->all();

        return json_decode(json_encode($empData), true);
    }

}
