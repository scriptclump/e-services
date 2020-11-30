<?php

namespace App\Modules\HrmsEmployees\Models;

use App\Central\Repositories\RoleRepo;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Session;
use URL;
use App\Modules\Roles\Models\Role;
use App\Modules\HrmsEmployees\Models\EmpContactModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Central\Repositories\ProductRepo;

class EmployeeModel extends Model {

    protected $table = 'employee';
    protected $primaryKey = 'emp_id';
    protected $roleRepo = 'user_id';
    public $timestamps = false;
    public $_businessUnitDefault = 0;
    public $_categoriesDefault = 0;
    public $_roleList = [];

    public function __construct() {
        $this->roleRepo = new RoleRepo();
        
    }

    public function saveUsers($data,$aadhar_pic,$pan_pic) {
        try {
            $empContactModelObj = new EmpContactModel();
             $legalEntityId = \Session::get('legal_entity_id');
            $id = 0;
            $status = false;
            $message = 'Unable to save data please contact admin';
            if (!empty($data)) {
                $status = true;
                $message = 'Data saved sucessfully';
                $this->prefix = isset($data['prefix']) ? $data['prefix'] : "Mr.";
                $this->lastname = isset($data['lastname']) ? $data['lastname'] : '';
                $this->firstname = isset($data['firstname']) ? $data['firstname'] : '';
                $this->mobile_no = isset($data['mobile_no']) ? $data['mobile_no'] : '';
                $this->email_id = isset($data['email_id']) ? $data['email_id'] : '';

                $this->father_name = isset($data['father_name']) ? $data['father_name'] : '';
                $this->mother_name = isset($data['mother_name']) ? $data['mother_name'] : '';
                $this->gender = isset($data['gender']) ? $data['gender'] : '';
                $this->nationality = isset($data['nationality']) ? $data['nationality'] : '';
                $this->blood_group = isset($data['blood_group']) ? $data['blood_group'] : '';
                $this->dob = isset($data['dob']) ? $data['dob'] : '';
                $this->marital_status = isset($data['marital_status']) ? $data['marital_status'] : '';
                $this->employment_type = isset($data['employment_type']) ? $data['employment_type'] : '';
                $this->emp_group_id = isset($data['emp_group_id']) ? $data['emp_group_id'] : '';

                $this->password = $data['password'];
                $this->legal_entity_id = $legalEntityId;
                $this->aadhar_number = isset($data['aadhar_number']) ? $data['aadhar_number'] : '';
                $this->aadhar_image = isset($aadhar_pic) ? $aadhar_pic : '';
                $this->emp_code = isset($data['emp_code']) ? $data['emp_code'] : '';
                $this->role_id = isset($data['role_id']) ? $data['role_id'] : 0;
                $this->designation = isset($data['designation']) ? $data['designation'] : 0;
                $this->department = isset($data['department']) ? $data['department'] : 0;
                $this->reporting_manager_id = isset($data['reporting_manager_id']) ? $data['reporting_manager_id'] : 0;
                $this->business_unit_id = isset($data['business_unit_id']) ? $data['business_unit_id'] : 0;

                $this->middlename = isset($data['middlename']) ? $data['middlename'] : '';
                $this->alternative_mno = isset($data['alternative_mno']) ? $data['alternative_mno'] : '';
                $this->landline_ext = isset($data['landline_ext']) ? $data['landline_ext'] : '';
                $this->pan_card_number = isset($data['pan_card_number']) ? $data['pan_card_number'] : '';
                $this->pan_card_image = isset($pan_pic) ? $pan_pic : '';
                $this->uan_number = isset($data['uan_number']) ? $data['uan_number'] : '';
                $this->save();
                $empId = $this->emp_id;
                $cur_address = isset($data['cur_add']) ? $data['cur_add'] : "";
                $cur_address2 = isset($data['cur_add2']) ? $data['cur_add2'] : "";
                $cur_city = isset($data['cur_city']) ? $data['cur_city'] : "";
                $cut_state = isset($data['cut_state']) ? $data['cut_state'] : "";
                $cur_country = isset($data['cur_country']) ? $data['cur_country'] : "";
                $cur_pincode = isset($data['cur_pincode']) ? $data['cur_pincode'] : "";

                $per_city = isset($data['per_city']) ? $data['per_city'] : "";
                $per_add = isset($data['per_add']) ? $data['per_add'] : "";
                $per_add2 = isset($data['per_add2']) ? $data['per_add2'] : "";
                $per_state = isset($data['per_state']) ? $data['per_state'] : "";
                $per_country = isset($data['per_country']) ? $data['per_country'] : "";
                $per_pincode = isset($data['per_pincode']) ? $data['per_pincode'] : "";

                $emergency_name = isset($data['emergency_name']) ? $data['emergency_name'] : "";
                $emergency_relation = isset($data['emergency_relation']) ? $data['emergency_relation'] : "";
                $emergency_contact_one = isset($data['emergency_contact_one']) ? $data['emergency_contact_one'] : "";
                $emergency_contact_two = isset($data['emergency_contact_two']) ? $data['emergency_contact_two'] : "";

                $emp_contact_info = array("cu_address"=>$cur_address,"cu_address2"=>$cur_address2,"cu_city"=>$cur_city,"cu_state"=>$cut_state,"cu_country"=>$cur_country,"cu_zip_code"=>$cur_pincode ,"pe_address"=>$per_add,"pe_address2"=>$per_add, "pe_city"=>$per_city, "pe_country"=>$per_country,"pe_state"=>$per_state, "pe_zip_code" =>$per_pincode,'employee_id'=>$empId,'emergency_name'=>$emergency_name,'emergency_relation'=>$emergency_relation,'emergency_contact_one'=>$emergency_contact_one,'emergency_contact_two'=>$emergency_contact_two);
                $empContactModelObj->insert($emp_contact_info);
                $approvalFlowMethod = new CommonApprovalFlowFunctionModel();
                $status = $approvalFlowMethod->notifyUserForFirstApproval('HRMS Onboard Flow', $empId, Session::get('userId'));
                Log::info($status);
                Log::info($legalEntityId);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $empId;
    }

    public function updateSaveUser($data, $emp_id) {
        try {
            $getEmpExistRole = DB::table('employee')
                                ->where('emp_id',$emp_id)
                                ->pluck('role_id')->all();
            $existedRole = $getEmpExistRole[0];
            unset($data['edit_aadhar_image']);
           $this->where('emp_id', $emp_id)->update($data);
            $rs= $this->updateUserTable($data, $emp_id,$existedRole);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        //return $userId;
    }

    public function sendEmail($legal_entity_id, $userId, $data) {
        try {
            if ($userId) {
                $userId = $this->roleRepo->encodeData($userId);
                $legal_entity_id = $this->roleRepo->encodeData($legal_entity_id);
                $data['from'] = 'ebutor.buyer@gmail.com';
                $url = URL::asset('signup/' . $legal_entity_id . '/' . $userId);
                $link = $url;
                $body = array('template'=>'emails.register', 'attachment'=>'', 'link' => $link, 'username' => $data['firstname'] . ' ' . $data['lastname']);
                $subject='Registration with FBE';
                $email = array($data['email']);
                Utility::sendEmail($email, $subject, $body);
                // \Mail::send('emails.register', ['link' => $link, 'username' => $data['firstname'] . ' ' . $data['lastname']], function($message) use ($data) {
                //     $message->from($data['from'], 'Ebutor')->to($data['email'])->subject('Registration with FBE');
                // });
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function savePassword($data) {
        try {
            $status = false;
            $message = 'Unable to save password. Please contact admin';
            if (!empty($data['set_password'])) {
                $password = $data['set_password'];
                $confirm_password = $data['confirm_password'];
                $st = strcmp($password, $confirm_password);
                if ($st == 0) {
                    $this->where('user_id', $data['user_id'])->update(['password' => md5($password)]);
                    $this->roleRepo->updateDates('users', $data['user_id'], 'user_id', 0, 1, \Session::get('userId'));
                    $status = true;
                    $message = "Password saved successfully.";
                } else {
                    $message = "Password mismatch. Please retry";
                }
            } else {
                $message = "Incorrect password type";
            }
            return json_encode([
                'status' => $status,
                'message' => $message]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getUserId($email) {
        try {
            if ($email != '') {
                $result = 0;
                $response = DB::table('users')->where('email_id', $email)->first(['user_id']);
                if ($response) {
                    $result = $response;
                }
                $result = $result->user_id;
                return $result;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getManagersList($data) {
        try {
            $roles = [];
            $legalEntityId = \Session::get('legal_entity_id');
            $currentRoleId = \Session::get('roleId');
            $roleIds = isset($data['role_id']) ? $data['role_id'] : 0;
            $roleLists = [];
            if (!empty($data)) {
                if (is_array($roleIds)) {
                    foreach ($roleIds as $roleId) {
                        $roleLists = $this->roleRepo->getParentRolesbyRoleId($roleId, $legalEntityId);
                    }
                }
                $userId = isset($data['user_id']) ? $data['user_id'] : 0;
                if (!empty($roleIds) && $legalEntityId > 0) {
                    if ($userId > 0) {
                        $roles = DB::table('user_roles')
                                ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                                ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                                ->where(['users.legal_entity_id' => $legalEntityId])
                                ->where('users.user_id', '!=', $userId)
                                ->whereIn('user_roles.role_id', $roleLists)
                                ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                                ->groupBy('user_roles.user_id')
                                ->get()->all();
                    } else {
                        $roles = DB::table('user_roles')
                                ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                                ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                                ->where(['users.legal_entity_id' => $legalEntityId])
                                ->whereIn('user_roles.role_id', $roleLists)
                                ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                                ->groupBy('user_roles.user_id')
                                ->get()->all();
                    }
                } elseif (!empty($roleId) && $currentRoleId == 1 && $legalEntityId == 0) {
                    if ($userId > 0) {
                        $roles = DB::table('user_roles')
                                ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                                ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
//                            ->where(['users.legal_entity_id' => $legalEntityId])
                                ->where('users.user_id', '!=', $userId)
                                ->whereIn('user_roles.role_id', $roleLists)
                                ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                                ->groupBy('user_roles.user_id')
                                ->get()->all();
                    } else {
                        $roles = DB::table('user_roles')
                                ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                                ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
//                            ->where(['users.legal_entity_id' => $legalEntityId])
                                ->whereIn('user_roles.role_id', $roleLists)
                                ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                                ->groupBy('user_roles.user_id')
                                ->get()->all();
                    }
                }
            }
            $currentUserId = \Session::get('userId');
            $currentUser = DB::table('users')
                    ->where(['users.user_id' => $currentUserId, 'users.legal_entity_id' => $legalEntityId])
                    ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                    ->get()->all();
//            Log::info($roles);
  //          Log::info($currentUser);
            $roles = array_merge($roles, $currentUser);
    //        Log::info($roles);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($roles);
    }

    public function getBusinessUnits() {
        try {
            return DB::table('business_units')
                            ->select('bu_id', 'bu_name')
                            ->get()->all();
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function saveUser($data, $userId = 0) {
        if ($userId > 0) {
            DB::table('users')->where('user_id', $userId)->update($data);
        } else {
            if ((empty($data['getuserid']))) {
                unset($data['getuserid']);
                $data['created_at'] = date('Y-m-d H:i:s');
                $userId = DB::table('users')->insertGetId($data);
            } else {
                unset($data['getuserid']);
                DB::table('users')->where('user_id', $userId)->update($data);
            }
        }
        $this->roleRepo->updateDates('users', $userId, 'user_id', 0, 1, \Session::get('userId'));
        return $userId;
    }

    public function getUsers($userId) {
        $result = DB::table('users')
                ->select('user_id', 'firstname', 'lastname', 'email_id', 'mobile_no', 'emp_id')
                ->where('user_id', '=', $userId)
                ->first();
        return $result;
    }

    public function getEmployeeDetails($emp_id) {
        $empData = $this->where('emp_id', $emp_id)
                ->get()->all();
        $empData = json_decode(json_encode($empData), true);
        return $empData;
    }

    public function updateEmpApproval($emp_id, $status) {
        return $this->where('emp_id', $emp_id)
                        ->update(['status' => $status]);
    }

    // get all report managers

    public function getAllReportingManagers($userId) {
        try {
            $response = [];
            if ($userId > 0) {
                if (!in_array($userId, $this->_reporting_managers_list)) {
                    $response = DB::table('users')
                            ->where(['user_id' => $userId])
                            ->pluck('reporting_manager_id')->all();
                    $this->_reporting_managers_list[] = $userId;
                }
//                Log::info(DB::getQueryLog());
                if (!empty($response)) {
                    foreach ($response as $supplierUserId) {
                        $this->_reporting_mangers[] = $supplierUserId;
                        $this->getAllReportingManagers($supplierUserId);
                    }
                }
            }
            return $this->_reporting_mangers;
            //echo "<pre>";print_R($this->_suppliers);die;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $this->_suppliers;
    }

    //get data from master lookup for reason

    public function getdataforreasondropdown() {
        $groupName = DB::table("master_lookup")
                ->where('mas_cat_id', '=', "151")
                ->orderby("sort_order")
                ->get()->all();
        return $groupName;
    }

    public function master_lookup($id) {
        $sqlRs = DB::table('master_lookup')
                ->where('mas_cat_id', $id)
                ->select('master_lookup_name', 'value')
                ->get()->all();
        $sqlRs = json_decode(json_encode($sqlRs), true);
        return $sqlRs;
    }

    public function getUserNameById($id) {
        $srmName = DB::table('users')
                        ->select(DB::raw('CONCAT(firstname," ",lastname) as name'))
                        ->where("user_id", $id)->get()->all();
        if (isset($srmName[0])) {
            $name = $srmName[0]->name;
        } else {
            $name = "";
        }
        return $name;
    }

    public function savePanPic($data, $id, $empId) {
        try {
            if (!empty($data['pan_file'])) {
                $file = $data['pan_file'];
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'jpeg' || $extension == 'jpg' || $extension == 'png' || $extension == 'JPG' || $extension =='pdf' || $extension =='doc' ||$extension =='docx') {
                    $productObj = new ProductRepo();
                    $url = $productObj->uploadToS3($file, 'emp_images', 1);
                    if ($url != '') {
                        $update_emp = DB::table('employee')->where('emp_id', $empId);
                        $current_pic = $update_emp->pluck('pan_card_image')->all()[0];
                        if($current_pic != NULL){
                            $cur_pic_arr = explode('/', $current_pic);
                            if($cur_pic_arr[2] == 's3.ap-south-1.amazonaws.com'){
                                $picdelete = $productObj->deleteFromS3($current_pic);
                            }
                        }
                        $update_emp = $update_emp->update(['pan_card_image' => $url]);
                    }
                }else{
                    $url = '';
                }
                return $url;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function saveAadharPic($data, $id, $empId) {
        try {
            if (!empty($data['aadhar_file'])) {
                $file = $data['aadhar_file'];
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'jpeg' || $extension == 'jpg' || $extension == 'png' || $extension == 'JPG' || $extension =='pdf' || $extension =='doc' ||$extension =='docx') {
                    $productObj = new ProductRepo();
                    $url = $productObj->uploadToS3($file, 'emp_images', 1);
                    if ($url != '') {
                        $update_emp = DB::table('employee')->where('emp_id', $empId);
                        $current_pic = $update_emp->pluck('aadhar_image')[0];
                        if($current_pic != NULL){
                            $cur_pic_arr = explode('/', $current_pic);
                            if($cur_pic_arr[2] == 's3.ap-south-1.amazonaws.com'){
                                $picdelete = $productObj->deleteFromS3($current_pic);
                            }
                        }
                        $update_emp = $update_emp->update(['aadhar_image' => $url]);
                    }
                }else{
                    $url = '';
                }
                return $url;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getEmployeeRoleNames($roleids) {
        $sqlRs = DB::table('roles')
                ->where('role_id', $roleids)
                ->pluck(DB::raw('group_concat(name) as names'))->all();
        if (!empty($sqlRs)) {
            return $sqlRs[0];
        }
        return null;
    }

    public function getUserNameByUserId($id) {
        $sqlRs = DB::select('select getRepManagernameHierarchy(' . $id . ') as name');
        $sqlRs = json_decode(json_encode($sqlRs), true);
        return $sqlRs[0]['name'];
    }

    public function employeeGroup() {
        $groupRs = DB::table('emp_groups')
                ->get()->all();
        $groupRs = json_decode(json_encode($groupRs), true);
        return $groupRs;
    }
    public function getCountries() {
        $countries_data = DB::table('countries')
                ->select('name as country_name', 'country_id as id')
                ->get()->all();
        return $countries_data;
    }
    public function getStates() {
        $states_data = DB::table('zone')
                ->select('name as state_name', 'zone_id as id')
                ->where('country_id', 99)
                ->orderByRaw("FIELD(name,'Telangana') DESC")->get()->all();
        return $states_data;
    }
    public function getMasterLookupNameById($id) {

        $sqlRs = DB::table('master_lookup')->where('value','=', $id)->get()->all();
        if(!empty($sqlRs))
        {
            return $sqlRs[0]->master_lookup_name;
        }
        return null;
    }
    public function getRole()
    {
        $legal_entity_id = Session::get('legal_entity_id');
        $result = DB::table('roles')
                    ->select('roles.name', 'roles.role_id')
                    ->where(['roles.is_active' => 1, 'roles.legal_entity_id' => $legal_entity_id, 'roles.is_deleted' => 0])
                    ->where('role_id','!=',1)
                    ->groupBy('roles.role_id')
                    ->orderBy('roles.role_id', 'ASC')
                    ->get()->all();
        return $result;
    }

    public function updateUserTable($data, $emp_id, $existedRole)
    {
        try{
            $checkUserActive = DB::table('users')
                            ->where('emp_id',$emp_id)
                            ->pluck('user_id')->all();
            $userData= array();
            if(!empty($checkUserActive))
            {
                $userData['firstname'] =  $data['firstname'];
                $userData['lastname'] =  $data['lastname'];
                $userData['business_unit_id'] = $data['business_unit_id'];
                $userData['department'] = $data['department'];
                $userData['designation'] = $data['designation'];
                $userData['mobile_no'] = $data['mobile_no'];
                $userData['reporting_manager_id'] = $data['reporting_manager_id'];
                $userData['updated_by'] = \Session::get('userId');
                $userData['updated_at'] = date('Y-m-d h:i:s');
                $getUserId = DB::table('users')
                            ->where('emp_id',$emp_id)
                            ->update($userData);
                $this->updateUserRoles($checkUserActive[0],$data['role_id'],$emp_id,$existedRole);
            }
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function updateUserRoles($userId,$role_id,$emp_id,$existedRole)
    {
        try{
            $checkRole = DB::table('user_roles')
                        ->where('role_id',$role_id)
                        ->where('user_id',$userId)
                        ->select("role_id")
                        ->get()->all();
            if(empty($checkRole))
            {
                if(!empty($existedRole))
                {
                    $updateData = array("role_id"=>$role_id,
                            "updated_by"=>\Session::get('userId'));
                    DB::table('user_roles')
                    ->where('role_id',$existedRole)
                    ->where('user_id',$userId)
                    ->update($updateData);
                }              
            }
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    // get the employee assets
    public function getEmployeeAssets($empid){

        $assetsQuery = "select product_title,company_asset_code,serial_number,date_format(asset_allocated_date,'%d-%b-%Y')as asset_allocated_date,getManfName(p.manufacturer_id) as manufacturername FROM assets AS a
                JOIN products AS p ON p.product_id=a.product_id
                JOIN users AS us ON us.user_id = a.`allocated_to_id`
                WHERE us.`emp_id`=$empid";

        $data = DB::select(DB::raw($assetsQuery));
        return $data;
    }
    
    public function UpdateCertificationDetails($id,$data)
    {
        $result = DB::table('emp_certification')
                   ->where('employee_certification_id',$id)
                   ->update($data);
        return $result;
    }


    public function getIfsclist($term){

        $getifsclist = "select * from bank_info where  ifsc like '%$term%' limit 20";
        $allData = DB::select(DB::raw($getifsclist));

        $ifsc_arr = array();
        foreach($allData  as $getnames) {
            $ifscs = array("label" => $getnames->ifsc,"ifsc" => $getnames->ifsc,"branch" => $getnames->branch,"bank_name" => $getnames->bank_name,"micr_code" =>$getnames->micr);
            array_push($ifsc_arr, $ifscs); 
        }
        return $ifsc_arr;
    }

    public function checkPasswordInTable($password,$emp_id){

        $id = DB::table('users')->where('password','=',$password)->where('emp_id',$emp_id)->pluck('user_id')->all();
        return $id;

    }

    public function checkPasswordInUsersTable($password,$emp_id){
/* emp_id it is the emp_id its user_id in users table*/
        $id = DB::table('users')->where('password','=',$password)->where('user_id',$emp_id)->pluck('user_id')->all();
        return $id;

    }
    public function changePasswordInUsersAndEmployeeTable($empId,$password){

        $updatepassword = DB::table('users')->where('emp_id',$empId)->update(['password' => md5($password)]);

            if($updatepassword == 1 ){
                $updateinemployeetable = DB::table('employee')->where('emp_id', '=', $empId)->update(['password' => md5($password)]);
            }

        return $updatepassword;

    }

    public function resetPasswordInUsersAndEmployeeTable($empId,$password){

        $updatepassword = DB::table('users')->where('user_id',$empId)->update(['password' => md5($password)]);

            // if($updatepassword == 1 ){
            //     $updateinemployeetable = DB::table('employee')->where('user_id', '=', $empId)->update(['password' => md5($password)]);
            // }

        return $updatepassword;

    }

    public function EmployeeHolidayList($empid){
    
    $holidaylist = "select emg.emp_group_id,emg.group_name FROM employee AS emp inner JOIN emp_groups AS emg ON emp.legal_entity_id=emg.legal_entity_id WHERE emp.emp_id='".$empid."'";
        $data = DB::select(DB::raw($holidaylist));
        return $data;        

    }

    public function EmployeeHolidays($groupid,$year = "Y"){

        $fromdate = date("$year-01-01");
        $enddate = date("$year-12-31");
        $holidays = "select hl.holiday_name,DATE_FORMAT(hl.holiday_date,'%d-%b-%Y') as date,DAYNAME(hl.holiday_date) as day,case hl.holiday_type
                            when 1 then 'Fixed'
                            when 0 then 'Optional'
                            else 'Unknown'
                end 'holiday_type' from emp_groups as emg inner join holiday_list as hl on hl.emp_group_id = emg.emp_group_id where hl.emp_group_id = '".$groupid."' and hl.holiday_date between '".$fromdate."' and '".$enddate."' group by hl.holiday_name order by hl.holiday_date asc" ;
                $data = DB::select(DB::raw($holidays));
        return $data;   

    }


    public function getEmployeeId($userid){
            $empid = "select e.emp_id from users as u inner join employee as e on e.emp_id=u.emp_id where u.user_id=$userid";
        $data = DB::select(DB::raw($empid));  

         if($data){
            return $data[0]->emp_id;
        }else{
            return 0;
        }
    }

    public function importList($data){
        if(count($data)>0){
            foreach ($data as $index=>$holidaydata){
                $holidaylist = array("emp_group_id"=>$holidaydata['employee_group_id'],"holiday_name"=>$holidaydata['occasion'],"holiday_date"=>$holidaydata['date'],"holiday_type"=>$holidaydata['is_fixed']);
                $holiday_data=DB::table('holiday_list')->insert($holidaylist);
            }
            return 1;    
        }else{
            return 0;
        }
    }

    public function validateReason($emp_group_id,$year){
       $query = "SELECT holiday_name FROM holiday_list WHERE YEAR(holiday_date) = $year AND emp_group_id =$emp_group_id";
       $result = DB::select(DB::raw($query));
       $rs = json_decode(json_encode($result), true);
       return $rs; 
    }

    public function validateDate($dateformat,$emp_group_id){
        $validatedate=DB::table('holiday_list')
                ->select("holiday_date")
                ->where('holiday_date',$dateformat)
                ->where('emp_group_id',$emp_group_id)
                ->get()->all();
        return $validatedate;
    }

    public function saveProfilePic($data, $id, $empId) {
        try {
            if (!empty($data['file'])) {
                $file = $data['file'];
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'jpeg' || $extension == 'jpg' || $extension == 'png' || $extension == 'JPG') {
                    $productObj = new ProductRepo();
                    $url = $productObj->uploadToS3($file, 'emp_images', 1);
                    if ($url != '') {
                        $update_emp = DB::table('employee')->where('emp_id', $empId);

                        $current_pic = $update_emp->pluck('profile_picture')->all()[0];
                        if($current_pic != NULL){
                            $cur_pic_arr = explode('/', $current_pic);
                            if($cur_pic_arr[2] == 's3.ap-south-1.amazonaws.com'){
                                $picdelete = $productObj->deleteFromS3($current_pic);
                            }
                        }
                        $update_emp = $update_emp->update(['profile_picture' => $url]);
                        if ($update_emp) {
                            $update_user = DB::table('users')->where('emp_id', $empId)->update(['profile_picture' => $url]);
                        }
                    }
                }else{
                    $url = '';
                }
                return $url;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
