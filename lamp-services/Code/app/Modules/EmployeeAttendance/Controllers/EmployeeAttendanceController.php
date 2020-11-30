<?php
namespace App\Modules\EmployeeAttendance\Controllers;
date_default_timezone_set("Asia/Kolkata");

use App\Http\Controllers\BaseController;
use View;
use Log;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Central\Repositories\RoleRepo;
use App\Modules\EmployeeAttendance\Models\Attendance;
use UserActivity;


class EmployeeAttendanceController extends BaseController {
    
    public function __construct() {
        $this->_roleRepo = new RoleRepo();
        $this->_Attendance = new Attendance();
        //$this->_InventorySnapshot = new InventorySnapshot();
    }

    public function indexAction() {
        try{
            $allEmp = $this->_Attendance->getAllEmployees(14);
            echo "Total Employee:".count($allEmp)."\n";
            $allData = $this->_Attendance->getAttendance($allEmp);
            echo "Data Found:".count($allData)."\n";

            $insertAttendance = $this->_Attendance->putEmpAttendance($allData);
            
            if($insertAttendance)
                return 'Success';
            else
                return $insertAttendance;

        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function copyEmployeeData(){
        try{
            $allEmp = $this->_Attendance->getEmployeeDetails(14);
            
            //print_r($allEmp); exit;

            $empArr = array();
            foreach($allEmp as $emp){
                $temp = array();
                $temp['legal_entity_id']= 2;
                $temp['business_unit_id']=$emp['BusinessUnitId'];
                $temp['firstname']=$emp['First_Name'];
                $temp['lastname']=$emp['Last_Name'];
                $temp['office_email']=$emp['Email_Address'];
                $temp['role_id']=$emp['Authorization_Role_Id'];
                $temp['reporting_manager_id']=$emp['Report_Manager_Id'];
                $temp['designation']=$emp['Designation_Id'];
                $temp['department']=$emp['DepartmentId'];
                $temp['mobile_no']=$emp['Phone_Number'];
                $temp['alternative_mno']=$emp['Home_Phone_Number'];
                $temp['employment_type']=$emp['EmploymentTypeID'];
                if(!empty($emp['Date_Of_Birth'])){
                    $emp['Date_Of_Birth'] = date('Y-m-d', strtotime($emp['Date_Of_Birth']));
                }
                $temp['dob']=$emp['Date_Of_Birth'];
                $temp['father_name']=$emp['Fathers_Name'];
                $temp['mother_name']=$emp['MotherName'];
                $temp['gender']=$emp['Gender'];
                $temp['marital_status']=$emp['Martial_Status'];
                $temp['blood_group']=$emp['Blood_Group'];
                $temp['nationality']=$emp['Nationality'];
                $temp['aadhar_number']=$emp['Adhar_Card_Number'];
                $temp['aadhar_name']=$emp['NameAsPerAdhar'];
                $temp['pan_card_number']=$emp['Pancard_Number'];
                $temp['pan_card_name']=$emp['NameAsPerPan'];
                $temp['driving_licence_number']=$emp['Driving_Licence_Number'];
                if(!empty($emp['Driving_Licence_Expiry_Date'])){
                    $emp['Driving_Licence_Expiry_Date'] = date('Y-m-d', strtotime($emp['Driving_Licence_Expiry_Date']));
                }
                $temp['dl_expiry_date']=$emp['Driving_Licence_Expiry_Date'];
                $temp['uan_number']=$emp['UANNumber'];
                $temp['passport_number']=$emp['Passport_Number'];
                if(!empty($emp['Passport_Valid_To'])){
                    $emp['Passport_Valid_To'] = date('Y-m-d', strtotime($emp['Passport_Valid_To']));
                }
                $temp['passport_valid_to']=$emp['Passport_Valid_To'];
                $temp['emp_code']=$emp['Emp_Id'];
                if(!empty($emp['Date_Of_Joining'])){
                    $emp['Date_Of_Joining'] = date('Y-m-d', strtotime($emp['Date_Of_Joining']));
                }
                $temp['doj']=$emp['Date_Of_Joining'];
                $temp['status']=57148;
                $temp['ep_emp_id']=$emp['Id'];

                $empArr[] = $temp;
            }
            echo "Employee Count: ".count($empArr)."\n";
            $insertEmp = $this->_Attendance->insertEmployees($empArr);

            // $checkUsers = $this->_Attendance->userChecknUpdate();

            // //Update Role/Designation/Department to epmloyee table
            // $updateEmp = $this->_Attendance->empRoleUpdate();
            
            

            /*echo count($allEmp)."\n";

            $empArr = array();
            foreach($allEmp as $emp){
                $temp = array();
                $temp['emp_id'] = $emp['Id'];
                $temp['emp_code'] = $emp['Emp_Id'];

                $empArr[] = $temp;
            }*/

            // $leaveQuota = $this->_Attendance->updateLeaveQuota($empArr);
            
            // print_r($leaveQuota);
            
            // $leaveHistory = $this->_Attendance->updateLeaveHistory($empArr);

            // print_r($leaveHistory);
            
            // $attendance = $this->_Attendance->dumpAttendance($empArr);
            
            // print_r($attendance);
            //return $insertEmp;

        } catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function attendanceHistory(Request $request) {
        try {
            $attendancehistory_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Employee Attendance", "attendancehistory", $request->input("data"), "attendancehistory api was requested", "");
            if ($attendancehistory_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if ($attendancehistory_params["from_date"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "From date is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "From date is missing", "data" => array()));
            }
            if ($attendancehistory_params["to_date"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "To date is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "To date is missing", "data" => array()));
            }

            $token_check = $this->_Attendance->authenticatToken($attendancehistory_params["lp_token"]);
            if (!empty($token_check) && isset($token_check['emp_id']) && $token_check['emp_id'] != '') {
                if ($attendancehistory_params["sub_ord_code"] != "") {
                    $emp_code = $attendancehistory_params["sub_ord_code"];
                } else {
                    $emp_code = $token_check["emp_code"];
                }

                $res_attendance_history = $this->_Attendance->attendanceByDateEmpCode($attendancehistory_params, $emp_code);
                UserActivity::apiUpdateActivityLog($last_id, $res_attendance_history);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_attendance_history) ? "Attendance History" : "No data", "data" => !empty($res_attendance_history) ? $res_attendance_history : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getMySubordinates(Request $request) {
        try {
            $getmysubordinates_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Employee Attendance", "getmysubordinates", $request->input("data"), "getmysubordinates api was requested", "");
            if ($getmysubordinates_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }

            $token_check = $this->_Attendance->authenticatToken($getmysubordinates_params["lp_token"]);
            if (!empty($token_check) && isset($token_check['emp_id']) && $token_check['emp_id'] != '') {
                Session::set('userId', $token_check["user_id"]);
                $is_hr_manager = $this->_roleRepo->checkPermissionByFeatureCode('ALLEMP');
                $res_my_subordinates = $this->_Attendance->getSubordinatesByUserId($token_check["user_id"], $is_hr_manager);
                UserActivity::apiUpdateActivityLog($last_id, $res_my_subordinates);
                return json_encode(array("status_code" => 200, "status" => "Success", "message" => !empty($res_my_subordinates) ? "My Subordinates" : "No data", "data" => !empty($res_my_subordinates) ? $res_my_subordinates : array()));
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "Token mismatch");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token mismatch", "data" => array()));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getDeviceAttendance() {
        try{
            echo "<pre>";
            $allEmp = $this->_Attendance->getAllEmployees();
            echo "Total Employee:".count($allEmp)."\n";
            $allData = $this->_Attendance->getDeviceLogs($allEmp);
            echo "Data Found:".count($allData)."\n";

            $insertAttendance = $this->_Attendance->putDeviceAttendance($allData);
            
            if($insertAttendance)
                return 'Success';
            else
                return $insertAttendance;

        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function getDeviceData() {
        try{
            echo "<pre>";
            $allDevice = $this->_Attendance->getDevices();
            echo "Total Devices:".count($allDevice)."\n";
            //print_r($allDevice);
            $allData = $this->_Attendance->putDevices($allDevice);
            //echo "Data Found:".count($allData)."\n";

            if($allData)
                return 'Success';
            else
                return $allData;

        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
}