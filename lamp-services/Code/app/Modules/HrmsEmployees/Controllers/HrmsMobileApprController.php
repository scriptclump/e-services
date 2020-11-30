<?php

/*
 * Filename: HrmsMobileApprController.php
 * Description: This file is used for hrms approval from mobile
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 11 November 2017
 * Modified date: 11 November 2017
 */

namespace App\Modules\HrmsEmployees\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Log;
use App\Modules\HrmsEmployees\Models\HrmsApiModel;
use App\Modules\HrmsEmployees\Models\ExitProcessModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use UserActivity;

class HrmsMobileApprController extends BaseController {

    public function __construct() {
        try {
            $this->_hrmsApiModel = new HrmsApiModel();
            $this->_exitModel = new ExitProcessModel();
            $this->_approvalFlowMethod = new CommonApprovalFlowFunctionModel();
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getHrmsTkts(Request $request) {
       try {
            $gettkts_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("HRMS Onboard Workflow Mobile", "getHrmsTkts", $request->input("data"), "getHrmsTkts api was requested", "");
            if ($gettkts_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }

            $token_check = $this->_hrmsApiModel->authenticatToken($gettkts_params["lp_token"]);
            if ($token_check !== "Token not found") {
                $role_ids = $this->_hrmsApiModel->getRolesByUserId($token_check);
                if ($role_ids !== "Role does not exist") {
                    $tickets_list = $this->_hrmsApiModel->getTicketsByRoleId($role_ids,$gettkts_params["flag"],$token_check);
                    if (!empty($tickets_list)) {
                        UserActivity::apiUpdateActivityLog($last_id, $tickets_list);
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => "All tickets for the given token", "data" => $tickets_list));
                    } else {
                        UserActivity::apiUpdateActivityLog($last_id, $tickets_list);
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => "There are no tickets for the given token", "data" => $tickets_list));
                    }
                } else {
                    UserActivity::apiUpdateActivityLog($last_id, "Role does not exit for the given token");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Role does not exit for the given token", "data" => array()));
                }
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "You have already logged into the Ebutor System");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong from API side", "data" => array("error" => $ex->getMessage())));
        }
    }

    public function awfhrmsdetails(Request $request) {
        try {
            $awfhrmsdetails_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("HRMS Onboard Workflow Mobile", "awfhrmsdetails", $request->input("data"), "awfhrmsdetails api was requested", "");
            if ($awfhrmsdetails_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if ($awfhrmsdetails_params["emp_id"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Emp id is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Emp id is missing", "data" => array()));
            }

            $token_check = $this->_hrmsApiModel->authenticatToken($awfhrmsdetails_params["lp_token"]);
            if ($token_check !== "Token not found") {
                $final_data = array();
                $emp_details = $this->_hrmsApiModel->getEmployeeDetails($awfhrmsdetails_params["emp_id"]);
                if (!empty($emp_details)) {
                    $final_data["emp_details"] = $emp_details[0];
                    $final_data["approval_details"] = $this->_approvalFlowMethod->getApprovalFlowDetails("HRMS Onboard Flow", $final_data["emp_details"]["status"], $token_check);

                    UserActivity::apiUpdateActivityLog($last_id, $final_data);
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => "Employee and approval details for the given emp id", "data" => $final_data));
                } else {
                    UserActivity::apiUpdateActivityLog($last_id, "Could not find the details for the given emp id");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Could not find the details for the given emp id", "data" => array()));
                }
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "You have already logged into the Ebutor System");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong from API side", "data" => array("error" => $ex->getMessage())));
        }
    }

    public function approveHrmsTkt(Request $request) {
        try {
            $approvehrmstkt_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("HRMS Onboard Workflow Mobile", "approvehrmstkt", $request->input("data"), "approvehrmstkt api was requested", "");
            if ($approvehrmstkt_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if ($approvehrmstkt_params["hidden_emp_id"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Employee id is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Employee id is missing", "data" => array()));
            }
            if ($approvehrmstkt_params["currentStatusId"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Current status id is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Current status id is missing", "data" => array()));
            }
            if ($approvehrmstkt_params["next_status_id"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Next status id is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Next status id is missing", "data" => array()));
            } else {
                $explode_nsi = explode(",", $approvehrmstkt_params["next_status_id"]);
                if ($explode_nsi[0] == 57149 && $approvehrmstkt_params["join_date"] == "") {
                    UserActivity::apiUpdateActivityLog($last_id, "Joining date is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Joining date is missing", "data" => array()));
                } elseif ($explode_nsi[0] == 57153 && $approvehrmstkt_params["employee_email_id"] == "") {
                    UserActivity::apiUpdateActivityLog($last_id, "Office email id is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Office email id is missing", "data" => array()));
                } elseif ($explode_nsi[0] == 57156 && $approvehrmstkt_params["employee_exit_date"] == "") {
                    UserActivity::apiUpdateActivityLog($last_id, "Exit date is missing");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Exit date is missing", "data" => array()));
                } elseif ($explode_nsi[0] != 57149 && $approvehrmstkt_params["join_date"] != "") {
                    UserActivity::apiUpdateActivityLog($last_id, "Joining date is not applicable at this stage");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Joining date is not applicable at this stage", "data" => array()));
                }
            }
            if ($approvehrmstkt_params["comments"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Comments are missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Comments are missing", "data" => array()));
            }
            if ($approvehrmstkt_params["nextstatusname"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Next Status Name is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Next Status Name is missing", "data" => array()));
            }

            $token_check = $this->_hrmsApiModel->authenticatToken($approvehrmstkt_params["lp_token"]);
            if ($token_check !== "Token not found") {
                $nsi_exploded = explode(",", $approvehrmstkt_params["next_status_id"]);
                $next_status_id = $nsi_exploded[0];
                $is_final = $nsi_exploded[1];
                if ($is_final != 1) {
                    $is_final = $next_status_id;
                }

                if ($next_status_id == "57152") {
                    $emp_code = $this->_exitModel->generateTheEmpCode($approvehrmstkt_params['hidden_emp_id']);
                    if ($emp_code != 1) {
                        UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while generating the employee code");
                        return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while generating the employee code", "data" => array()));
                    }
                } elseif ($next_status_id == "57153") {
                    $update_email = $this->_exitModel->changeTheUserStatusInEmployeeTable($approvehrmstkt_params['hidden_emp_id'], $approvehrmstkt_params);
                    if ($update_email != 1) {
                        UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while updating the office email id");
                        return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while updating the office email id", "data" => array()));
                    }
                } elseif ($next_status_id == "57155") {
                    if ($approvehrmstkt_params["currentStatusId"] == "57152") {
                        if (isset($approvehrmstkt_params["employee_email_id"]) && $approvehrmstkt_params["employee_email_id"] !== "") {
                            $response = $this->_exitModel->changeTheUserStatusInEmployeeTable($approvehrmstkt_params['hidden_emp_id'], $approvehrmstkt_params);
                            if ($response != 1) {
                                UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while updating the office email id in employee table");
                                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while updating the office email id in employee table", "data" => array()));
                            }
                        } else {
                            UserActivity::apiUpdateActivityLog($last_id, "Office email id is missing");
                            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Office email id is missing", "data" => array()));
                        }
                    }
                    
                    if ($approvehrmstkt_params["currentStatusId"] == "57156" || $approvehrmstkt_params["currentStatusId"] == "57159") {
                        $dolResponse = $this->_exitModel->updateDOL($approvehrmstkt_params['hidden_emp_id']);
                        if ($dolResponse != 1) {
                            UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while updating the dateof leaving in employee table");
                            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while updating the dateof leaving in employee table", "data" => array()));
                        }
                    }

                    $save_user = $this->_exitModel->saveIntoUsertable($approvehrmstkt_params['hidden_emp_id'], $approvehrmstkt_params);
                    if ($save_user != 1) {
                        UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while creating a user");
                        return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while creating a user", "data" => array()));
                    }
                } else if ($next_status_id == "57156") {
                    $exit_date = $this->_exitModel->updateTheExitDate($approvehrmstkt_params['hidden_emp_id'], $approvehrmstkt_params);
                    if ($exit_date != 1) {
                        UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while updating the exit date");
                        return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while updating the exit date", "data" => array()));
                    }
                }

                \Session::put('userId', $token_check);
                $update_status = $this->_exitModel->updateMainTableWithStatus($approvehrmstkt_params, $is_final);
                if ($update_status) {
                    $set_session = $this->_hrmsApiModel->setSessionValues($token_check);
                    if ($set_session) {
                        $emp_details = $this->_hrmsApiModel->getEmployeeDetails($approvehrmstkt_params["hidden_emp_id"]);
                        if(isset($emp_details[0])){
                            $emp_name = $emp_details[0]["firstname"];
                            if($emp_details[0]["middlename"] != "" || $emp_details[0]["middlename"] != NULL){
                                $emp_name .= " " . $emp_details[0]["middlename"];
                            }
                            $emp_name .= " " . $emp_details[0]["lastname"];
                        } else {
                            $emp_name = "";
                        }
                        $title = "HRMS ".$approvehrmstkt_params['nextstatusname']." - ". $emp_name;
                        $approval_result = $this->_approvalFlowMethod->storeWorkFlowHistory("HRMS Onboard Flow", $approvehrmstkt_params['hidden_emp_id'], $approvehrmstkt_params['currentStatusId'], $next_status_id, $approvehrmstkt_params['comments'], $token_check, $title);
                        if ($approval_result) {
                            UserActivity::apiUpdateActivityLog($last_id, "Submitted successfully");
                            return json_encode(array("status_code" => 200, "status" => "Success", "message" => "Submitted successfully", "data" => array("approval" => "success")));
                        } else {
                            UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while approving the status");
                            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while approving the status", "data" => array()));
                        }
                    } else {
                        UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while setting the session values");
                        return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while setting the session values", "data" => array()));
                    }
                } else {
                    UserActivity::apiUpdateActivityLog($last_id, "Something went wrong while updating the status of the employee");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong while updating the status of the employee", "data" => array()));
                }
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "You have already logged into the Ebutor System");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong from API side", "data" => array("error" => $ex->getMessage())));
        }
    }

}
