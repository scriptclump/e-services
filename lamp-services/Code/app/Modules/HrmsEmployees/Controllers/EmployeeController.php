<?php

namespace App\Modules\HrmsEmployees\Controllers;

use View;
use Session;
use Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BaseController;
use URL;
use Log;
use Mail;
use Excel;
use Config;
use File;
use Response;
use Illuminate\Http\Request;
use Redirect;
use App\models\channels\channels;
use App\Modules\Seller\Models\Sellers;
use App\Modules\HrmsEmployees\Models\EmployeeModel;
use App\Modules\HrmsEmployees\Models\ExitProcessModel;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\GlobalRepo;
use Illuminate\Support\Facades\DB;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\HrmsEmployees\Models\EmpContactModel;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use Utility;
use Carbon\Carbon;

Class EmployeeController extends BaseController {

    public $roleAccess;
    protected $user_grid_fields;

    public function __construct(RoleRepo $roleAccess, CustomerRepo $custRepoObj, Request $request) {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                return Redirect::to('/');
            }
                return $next($request);
        });
        $global = new GlobalRepo();
        $global->logRequest($request);
        $this->roleAccess = $roleAccess;
        $this->custRepoObj = $custRepoObj;
        $this->employeeModel = new EmployeeModel();
        $this->apprHistoryModel = new ExitProcessModel();
        $this->_approvalFlowMethod = new CommonApprovalFlowFunctionModel();
        $this->empContactModelObj = new EmpContactModel();
    }

    /**
     * Get the document number from the image using AWS tesseract
     * @param  String $img_path      URL the image
     * @param  Integer $doc_tyoe     Document Type (PAN|Aadhar|Others)
     * @return String $doc_no        Document number from the image
     */
    function getDocumentNumberFromImage( $img_path, $doc_type ){
        $config = [
            'region' => env("AWS_TEXTRACT_REGION"),
            'version' => env("AWS_TEXTRACT_VERSION"), //'2018-06-27',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => env("AWS_TEXTRACT_KEY"),
                'secret' => env("AWS_TEXTRACT_SECRET")
        ]];
        $client = \AWS::createClient('textract', $config);
        $contents = $this->getRemoteURLContent($img_path);
        $options = [
            'Document' => [
                // 'S3Object' => [
                //     'Bucket' => 'ebutormedia-test',
                //     'Name' => 'emp_docs/emp_records/1584692418.jpg'
                // ]
                'Bytes' => $contents
            ],
            'FeatureTypes' => ['FORMS'], // REQUIRED
        ];
        $result = $client->analyzeDocument($options);
        $doc_no = '';
        // If debugging:
      //  echo print_r($result, true);
        $blocks = $result['Blocks'];
        // Loop through all the blocks:
        foreach ($blocks as $key => $value) {
            if (isset($value['BlockType']) && $value['BlockType']) {
                $blockType = $value['BlockType'];
                if (isset($value['Text']) && $value['Text']) {
                    $text = $value['Text'];
                    if ($blockType == 'WORD') { // $blockType LINE
                        if($doc_type == 'Aadhar'){
                            $pattern1 = '/^\d{4}\s\d{4}\s\d{4}$/';
                            $pattern2 = '/^[0-9]{12}$/';
                            if( preg_match($pattern1, $text) || preg_match($pattern2, $text) ){
                                $doc_no = $text;
                                break;
                            }
                        } elseif($doc_type == 'PAN'){
                            $pattern = '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/';
                            if( preg_match($pattern, $text) ){
                                $doc_no = $text;
                                break;
                            }
                        }                         
                    } elseif ($blockType == 'LINE') { // $blockType LINE
                        if($doc_type == 'Aadhar'){
                            $pattern1 = '/^\d{4}\s\d{4}\s\d{4}$/';
                            $pattern2 = '/^[0-9]{12}$/';
                            if( preg_match($pattern1, $text) || preg_match($pattern2, $text) ){
                                $doc_no = $text;
                                break;
                            }
                        } elseif($doc_type == 'PAN'){
                            $pattern = '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/';
                            if( preg_match($pattern, $text) ){
                                $doc_no = $text;
                                break;
                            }
                        }                         
                    }
                }
            }
        }
        return $doc_no;
    }

    /**
     * Get the content of the image from the URL
     * @param  String $filePath URL of the document
     * @return String $content  Content of the document
     */
    function getRemoteURLContent($filePath){
        $file = fopen($filePath,"rb");
        $content = "";
        while (!feof($file)){ // while not the End Of File
             $content.= fread($file,1024); //reads 1024 bytes at a time and appends to the variable as a string.
        }
        return $content;
        fclose($file);
    }

    public function validateEmail() {
        try {
            $response = json_encode([ "valid" => false]);
            $data = Input::all();
            $emp_id = isset($data['emp_id']) ? $data['emp_id'] : '';
            $emailId = isset($data['email_id']) ? $data['email_id'] : '';
            if ($emailId != '') {
                if ($emp_id != '') {
                    $isEmailAvailble = DB::table('employee')->where([['email_id',$emailId],['is_active',1],['emp_id','<>',$emp_id]])->count();
                } else {
                    $isEmailAvailble = DB::table('employee')->where([['email_id',$emailId],['is_active',1]])->count();
                }
                if ($isEmailAvailble == 0) 
                    $response = json_encode([ "valid" => true]);
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return $response;
    }

    public function validateMobileno() {
        try {
            DB::enablequerylog();
            $response = json_encode([ "valid" => false]);
            $data = Input::all();
            $emp_id = isset($data['emp_id']) ? $data['emp_id'] : '';
            $mobileNo = isset($data['mobile_no']) ? $data['mobile_no'] : '';
            if ($mobileNo != '' && $emp_id=="") {

                $checkMno = DB::table("users")
                            //->join('employee','users.emp_id','=','employee.emp_id')
                            ->where("users.mobile_no",$mobileNo)
                            ->where("users.legal_entity_id","=","2")
                            ->where("users.is_active",1)
                            ->whereNotNull("users.emp_id")
                            ->where("users.emp_id",'!=', 0)
                            ->first();
                if(!empty($checkMno))
                {
                    $response = json_encode([ "valid" => false]);
                }else
                {
                    $response = json_encode([ "valid" => true]);
                }
            }
            if ($emp_id!="") {

                $checkMno = DB::table("users")
                            ->join('employee','users.emp_id','=','employee.emp_id')
                            ->where("users.mobile_no",$mobileNo)
                            ->where("users.legal_entity_id","=","2")
                            ->where('users.emp_id','!=',$emp_id)
                            ->where("users.is_active",1)
                            ->first();
                if(!empty($checkMno))
                {
                    $response = json_encode([ "valid" => false]);
                }else
                {
                    $response = json_encode([ "valid" => true]);
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
            $response = json_encode([ "valid" => "Sorry, Mobile Validation Fails. Please Come back later"]);
        }
        return $response;
    }

    public function addUsers() {
        try {
            $addEmployee = $this->roleAccess->checkPermissionByFeatureCode('EDADDEMP');
            if (!$addEmployee) {
                Redirect::to('/employee/dashboard')->send();
            }
            $breadCrumbs = array('Home' => url('/'), 'HRMS' => '#', 'Employee Dashboard' => url('employee/dashboard'), 'Add Employee' => '#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title('Ebutor - Add Employee');
            $roles = $this->employeeModel->getRole();
            $reportingMangers = $this->roleAccess->getReportingMangers(0,0);
            $getDesignations = $this->roleAccess->getMasterLookupData('Designations');
            $getDepartments = $this->roleAccess->getMasterLookupData('Departments');
            $businessUnit = new EmployeeModel();
            $buCollection = $businessUnit->getBusinessUnits();
            $employment_types = $this->employeeModel->master_lookup(152);
            $empGroup = $this->employeeModel->employeeGroup();

            $countries = $this->employeeModel->getCountries();
            $countries = json_decode(json_encode($countries), true);
            $states = $this->employeeModel->getStates();
            $states = json_decode(json_encode($states), true);
            return view('HrmsEmployees::addusers')
                            ->with(['roles' => $roles,
                                'getDepartments' => $getDepartments,
                                'getDesignations' => $getDesignations,
                                'reportingMangers' => $reportingMangers,
                                'buCollection' => $buCollection,
                                'employment_types' => $employment_types,
                                'emp_group' => $empGroup,
                                'countries' => $countries,
                                'states' => $states
            ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getReportingManagers() {
        try {
            $data = Input::all();
            return $this->getReportingMangers($data['role_id'], $data['emp_id']);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function saveUser() {
        DB::beginTransaction();
        try {
            $messages = '';
            $status = false;
            $userId = 0;
            if (isset($data['user_id']) && $data['user_id'] == '') {
                $validator = Validator::make(
                                array(
                            'firstname' => Input::get('firstname'),
                            'lastname' => Input::get('lastname'),
                            'email_id' => Input::get('email_id'),
                            'mobile_no' => Input::get('mobile_no')
                                ), array(
                            'firstname' => 'required',
                            'lastname' => 'required',
                            'email_id' => 'required|email|unique:users',
                            'mobile_no' => 'numeric|digits:10'
                                )
                );
            } else {
                $userId = Input::get('user_id');
                $validator = Validator::make(
                                array(
                            'firstname' => Input::get('firstname'),
                            'lastname' => Input::get('lastname'),
                            'email_id' => Input::get('email_id'),
                            'mobile_no' => Input::get('mobile_no')
                                ), array(
                            'firstname' => 'required',
                            'lastname' => 'required',
                            'email_id' => 'required|email|unique:users,email_id,' . $userId . ',user_id',
                            'mobile_no' => 'numeric|digits:10'
                                )
                );

            }
            Log::info($validator->fails());
            if ($validator->fails()) {
                $failureMessages = $validator->messages();
                $messageArr = json_decode($failureMessages);
                foreach ($messageArr as $msg) {
                    $messages = $messages . (isset($msg[0]) ? $msg[0] : '') . '  ';
                }
            } else {
                $data = Input::get();
                $data['legal_entity_id'] = Session::get('legal_entity_id');
                $data['created_by'] = Session::get('userId');
                $data['created_at'] = date('Y-m-d H:i:s');
                $roleId = $data['role_id'];
                $role_ids = '';
                if (sizeof($roleId) > 1) {
                    $role_ids = implode(",", $roleId);
                } else {
                    $role_ids = $roleId[0];
                }
                $data['role_id'] = $role_ids;
                $password = "ebutor@123"; //str_random(20);
                $data['password'] = md5($password);


                if (isset($data['_token'])) {
                    unset($data['_token']);
                }
                $productRepo = new ProductRepo();
                if((isset($_FILES['aadhar_image'])) && !empty($_FILES['aadhar_image']['name'])){
                   $aadhar_pic_move = $data['legal_entity_id']."_".date("Y-m-d-H-i-s")."_". $_FILES['aadhar_image']['name'];
                   $aadhar_pic_path="uploads/EmployeeDoc/".$aadhar_pic_move;
                   move_uploaded_file($_FILES['aadhar_image']['tmp_name'], $aadhar_pic_path);
                   $aadhar_pic=$productRepo->uploadToS3($aadhar_pic_path,'emp_records',2);
                   unlink($aadhar_pic_path);
                }else{
                    $aadhar_pic ='';
                }
                if((isset($_FILES['pan_card_image'])) && !empty($_FILES['pan_card_image']['name'])){  
                   $pan_pic_move = $data['legal_entity_id']."_".date("Y-m-d-H-i-s")."_". $_FILES['pan_card_image']['name'];
                   $pan_pic_path="uploads/EmployeeDoc/".$pan_pic_move;
                   move_uploaded_file($_FILES['pan_card_image']['tmp_name'], $pan_pic_path);
                   $pan_pic=$productRepo->uploadToS3($pan_pic_path,'emp_records',2);
                   unlink($pan_pic_path);
                }else{
                    $pan_pic ='';

                }
                $employeeModel = new EmployeeModel();
                if (isset($data['user_id']) && $data['user_id'] > 0) {
                    unset($data['email_id']);
                    $status = false;
                    $userId = $employeeModel->saveUsers($data,$aadhar_pic,$pan_pic,$data['user_id']);
                    $userFirstName = isset($data['firstname']) ? $data['firstname'] : '';
                    $userLastName = isset($data['lastname']) ? $data['lastname'] : '';
                    $userName = $userFirstName . ' ' . $userLastName;
                    $messages = 'User Updated Sucessfully';

                    //@\Notifications::addNotification(['note_code' => 'USR002', 'note_params' => ['USERNAME' => $userName]]);
                } else {
                    unset($data['user_id']);
                    $status = true;
                    $message = 'Data saved sucessfully';

                    $data['dob'] = date('Y-m-d', strtotime($data['dob']));
                    $userId = $employeeModel->saveUsers($data,$aadhar_pic,$pan_pic);
                    $messages = 'New User Created Sucessfully';
                    $userFirstName = isset($data['firstname']) ? $data['firstname'] : '';
                    $userLastName = isset($data['lastname']) ? $data['lastname'] : '';
                    $userName = $userFirstName . ' ' . $userLastName;

                    //@\Notifications::addNotification(['note_code' => 'USR001', 'note_params' => ['USERNAME' => $userName]]);                    
                }/*
                  if (is_numeric($userId)) {
                  $this->roleAccess->mapRole($userId, $roleId);
                  $status = 1;
                  } */
                //===============================Mail to User =======================================
                 /*if ($userId == 0) {

                  $template = EmailTemplate::where('Code', 'ET1000')->get()->all();
                  $emailVariable = array('firstName' => $data['firstname'], 'lastName' => $data['lastname'], 'user_name' => $data['email'], 'password' => $password);
                  Mail::send(array('html' => 'emails.welcome_newuser'), $emailVariable, function($msg) use ($template, $data) {
                  $msg->from($template[0]->From, 'ebutor')->to($data['email'])->subject($template[0]->Subject);
                  });
                  } */
            }
        }catch(\Exception $ex){
            DB::rollback();
            return json_encode(array('status' => false, 'message' => "Status Update Failed"));
        }
        DB::commit();
        return json_encode(array('status' => $status, 'message' => $messages, 'user_id' => $userId));
    }

    public function updateUser() {
        DB::beginTransaction();
        try {
            $status = "Employee Sucessfully updated.";
            $data = Input::get();
            $userId = Session::get('userId');
            $data['updated_by'] = Session::get('userId');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $roleId = $data['role_id'];
            // if (isset($data['is_active']))
            //     $data['is_active'] = 1;
            // else
            //     $data['is_active'] = 0;
            if (isset($data['_token'])) {
                unset($data['_token']);
            }
            $role_ids = '';
            if (sizeof($roleId) > 1) {
                $role_ids = implode(",", $roleId);
            } else {
                $role_ids = $roleId[0];
            }
            $data['role_id'] = $role_ids;
            $emp_id = $data['emp_id'];
            $data['dob'] = date('Y-m-d', strtotime( $data['dob'] ));
            $data['doj'] = date('Y-m-d', strtotime( $data['doj'] ));
            $employeeObj = new EmployeeModel();
            $employeeObj->updateSaveUser($data, $emp_id);

            @\Notifications::addNotification(['note_code' => 'USR002']);

            $role_name = $this->employeeModel->getEmployeeRoleNames($roleId);
            $data['dob'] = date('d-m-Y', strtotime( $data['dob'] ));
            $data['role_name'] = $role_name;
            $data['reporting_manager_name'] = $this->employeeModel->getUserNameByUserId($data['reporting_manager_id']);
            $data['designation_name'] = $this->employeeModel->getMasterLookupNameById($data['designation']);
            $data['department_name'] = $this->employeeModel->getMasterLookupNameById($data['department']);


            $cost_name = DB::table('business_units')
                    ->where('bu_id', $data['business_unit_id'])
                    ->pluck('bu_name')->all();
            if (empty($cost_name)) {
                $cost_name = "";
            } else {
                $cost_name = $cost_name[0];
            }
            $data['business_unit_id'] = $cost_name;
            $data['employment_type'] = $this->master_lookup($data['employment_type']);
            DB::commit();
            return json_encode(array('status' => $status, 'user_id' => $userId, 'data' => $data));
        }catch(\Exception $e){
            DB::rollback();
            return json_encode(array('status' => 400, 'message' => "Status Update Failed"));
        }
    }

    public function editUsers($empId,$myProfile_id ="") {
        try {
//            if (is_numeric($empId)) {
//                return redirect('employee/dashboard')->withFlashMessage('Invalid Employee Id');
//                exit;
//            }
//            $empId = $this->roleAccess->decodeData($empId);
            $roles = $this->employeeModel->getRole();
            if($myProfile_id =="")
            {
                $breadCrumbs = array('Home' => url('/'), 'HRMS' => '#', 'Employee Dashboard' => url('employee/dashboard'), 'Edit Employee' => '#');
                parent::Breadcrumbs($breadCrumbs);
            }
            
            $userData = [];
            $empInfo = $this->employeeModel->where(['emp_id' => $empId])->first();
            if (!empty($empInfo)) {
                $userData = $empInfo;
            }
            $firstName = isset($userData['firstname']) ? $userData['firstname'] : '';
            $lastName = isset($userData['lastname']) ? $userData['lastname'] : '';
            $name = '';
            if ($lastName != '') {
                $name = $firstName . ' ' . $lastName;
            } else {
                $name = $firstName;
            }
            if ($name != '' && $myProfile_id == "") {
                parent::Title('Ebutor - Employee (' . $name . ')');
            } else {
                parent::Title('Ebutor - My Profile');
            }
            $userData['dob'] = date('d-M-Y', strtotime( $userData['dob'] ));
            $userData['doj'] = date('d-M-Y', strtotime( $userData['doj'] ));
            $userData['firstname'] = $firstName;
            $userData['lastname'] = $lastName;

            $role_name = $this->employeeModel->getEmployeeRoleNames($userData['role_id']);

            
            $userData['role_name'] = $role_name;
            if($userData['reporting_manager_id']!="")
            {
                $userData['reporting_manager_name'] = $this->employeeModel->getUserNameByUserId($userData['reporting_manager_id']);
            }else
            {
                $userData['reporting_manager_name'] = "";
            }
            $userData['designation_name'] = $this->employeeModel->getMasterLookupNameById($userData['designation']);
            $userData['department_name'] = $this->employeeModel->getMasterLookupNameById($userData['department']);

            $reportingMangers = $this->getReportingMangers($userData['role_id'], $empId);
            $getDesignations = $this->roleAccess->getMasterLookupData('Designations');
            $getDepartments = $this->roleAccess->getMasterLookupData('Departments');
            $cost_name = DB::table('business_units')
                    ->where('bu_id', $userData['business_unit_id'])
                    ->pluck('bu_name')->all();
            if (empty($cost_name)) {
                $cost_name = "";
            } else {
                $cost_name = $cost_name[0];
            }
            $businessUnit = new EmployeeModel();
            $buCollection = $businessUnit->getBusinessUnits();

            $prefix = array("Mr.", "Ms.", "Mrs.");
            $gender = array("Male", "Female");
            $marital = array("Single", "Married");

            $document_types = $this->employeeModel->master_lookup(150);
            $employment_types = $this->employeeModel->master_lookup(152);

            // get the data from master lookup table
            $masterlookup_data = $this->employeeModel->getdataforreasondropdown();

            // get approval details
            $approvalStatusDetails = $this->_approvalFlowMethod->getApprovalFlowDetails('HRMS Onboard Flow', $userData['status'], Session::get('userId'));

            // get approval history from table
            $apprName = "HRMS Onboard Flow";
            $approvalHistoryData = $this->apprHistoryModel->getHrmsApprovalHistorByIDFromDB($empId, $apprName);


            //checking if the data is there or not
            if (isset($approvalStatusDetails['data'])) {
                $appDropdown = $approvalStatusDetails['data'];
            } else {
                $appDropdown = "";
            }


            $empGroup = $this->employeeModel->employeeGroup();

            $docArray = DB::table('emp_docs')
                        ->join('master_lookup', 'master_lookup.value', '=', 'emp_docs.doc_type')
                        ->select('doc_id', 'master_lookup_name as doc_type', 'reference_no', 'extract_text', 'doc_url', DB::raw('GetUserName(emp_docs.created_by,2) as created_by'))
                        ->where('employee_id', $empId)
                        ->get()->all();
            $empPersonalInfo = $this->empContactModelObj->getEmpPersonalDetails($empId);
            $empPersonalInfo = json_decode(json_encode($empPersonalInfo), true);
            $countries = $this->employeeModel->getCountries();
            $countries = json_decode(json_encode($countries), true);
            $states = $this->employeeModel->getStates();
            $states = json_decode(json_encode($states), true);
            $editAccess = $this->roleAccess->checkPermissionByFeatureCode('EDEDITEMP');
            $editBankdetails = $this->roleAccess->checkPermissionbyFeatureCode('EDBANKDET');
            $editColAccess = $this->roleAccess->checkPermissionByFeatureCode('EDEDITCOL');
            
            $usrTblEmpId = json_decode(json_encode($this->employeeModel->getUsers(Session::get('userId'))), true)['emp_id'];
            ($empId == $usrTblEmpId) ? $picAccess = 1 : $picAccess = 0;
            $bankInfo =$this->empContactModelObj->empBankInfo($empId);
            $skillInfo =$this->empContactModelObj->empSkillInfo($empId);
            $account_data = $this->empContactModelObj->getAccounttype();
            $currency_data = $this->empContactModelObj->getCurrency();
            $eduTypeData =  $this->empContactModelObj->getEduTypeData();
            
             $eduArray = $this->empContactModelObj->getEmpEductionDetails($empId);
            $certificationArray = $this->empContactModelObj->getEmpCertiDetails($empId);
            $InsuranceArray = $this->empContactModelObj->checkEmpInsurance($empId);
            $experienceData  = $this->empContactModelObj->getExperienceData($empId);
            $empType = $this->employeeModel->getMasterLookupNameById($userData['employment_type']);
            $employeeAssets = $this->employeeModel->getEmployeeAssets($empId);
            $Assets = json_decode(json_encode($employeeAssets),true);
            return view('HrmsEmployees::editusers')->with(array('roles' => $roles,
                        'userData' => $userData,
                        'getDesignations' => $getDesignations,
                        'getDepartments' => $getDepartments,
                        'buCollection' => $buCollection,
                        'reportingMangers' => $reportingMangers,
                        'prefix' => $prefix,
                        'gender' => $gender,
                        'marital' => $marital,
                        'document_types' => $document_types,
                        'docsArr' => $docArray,
                        'employment_types' => $employment_types,
                        'cost_name' => $cost_name,
                        "lookup_data" => $masterlookup_data,
                        "appDropdown" => $appDropdown,
                        "approvaldata" => $approvalStatusDetails,
                        'emp_group' => $empGroup,
                        'approvalHistory'=>$approvalHistoryData,
                        'countries' =>$countries,
                        'states' => $states,
                        'empPersonalInfo' => $empPersonalInfo,
                        'editAccess' => $editAccess,
                        'editBankdetails' => $editBankdetails,
                        'editColAccess' => $editColAccess,
                        'picAccess' => $picAccess,
                        'myProfile_id' => $myProfile_id,
                        'bankInfo' => $bankInfo,
                        'skillInfo' => $skillInfo,
                        'account_data' => $account_data,
                        'currency_data' => $currency_data,
                        'eduTypeData' => $eduTypeData,
                        'eduArray' => $eduArray,
                        'certificationArray' => $certificationArray,
                        'InsuranceArray' => $InsuranceArray,
                        'empType'=>$empType,
                        'experienceData' => $experienceData,
                        'employeeAssets'   =>$Assets
                        ));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function checkEmailExist() {

        $email_id = strtolower(str_replace(' ', '', Input::get('email_id')));
        $mp = DB::select("SELECT email_id FROM users WHERE LOWER(REPLACE(email_id,' ',''))=?", array($email_id));
        $data = json_decode(json_encode($mp, true));
        if (count($data) == 0) {
            return '{"valid":true}';
        } else {
            return '{"valid":false}';
        }
    }

    public function master_lookup($value) {
        $rss = DB::table('master_lookup')
                ->where('value', $value)
                ->pluck('master_lookup_name')->all();
        return $rss[0];
    }

    public function employeeDocs(Request $request) {
        try {
            DB::enablequerylog();
            $postData = Input::all();
            $url = NULL;
            $docText = ''; /*
              $supplierId = (Session::has('supplier_id')) ? Session::get('supplier_id') : 0; */
            $documentType = isset($postData['documentType']) ? $postData['documentType'] : '';
            $document_name = $this->master_lookup($documentType);
            $emp_id = isset($postData['doc_emp_id']) ? $postData['doc_emp_id'] : '';
            $ref_no = isset($postData['ref_no']) ? $postData['ref_no'] : '';
            $ref_no = str_replace(' ', '', $ref_no);
            if ($request->hasFile('upload_file')) {
                $extension = Input::file('upload_file')->getClientOriginalExtension();
                if (!in_array($extension, array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'))) {
                    return Response::json(array('status' => 400, 'message' => 'Invalid extension'));
                }
                $doc_id_exists = 0;
                $destinationPath = public_path() . '/uploads/EmployeeDoc';
                $imageObj = $request->file('upload_file');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath);
                }
                $fileName = Input::file('upload_file')->getClientOriginalName();
                $productObj = new ProductRepo();
                $url = $productObj->uploadToS3($imageObj, 'emp_records', 1);
            } else {
                $fileName = NULL;
                $url = NULL;
            }
            $doc_id_exists = DB::table('emp_docs')
                            ->select('doc_id')
                            ->where(['employee_id' => $emp_id, 
                                'doc_type' => $documentType])
                            ->first();

            $doc_id = isset($doc_id_exists->doc_id) ? $doc_id_exists->doc_id : '';
            $docTypeName = $this->getDocumentTypeName($documentType);
            $extract_text = '';
            // Extract the document number from the document Image
            if($url !=NULL){
                $extract_text =  $this->getDocumentNumberFromImage($url, $docTypeName);
                $extract_text =  str_replace(' ', '', $extract_text);
                if($docTypeName == 'PAN'){
                    $data = array(
                        'pan_card_number' => $ref_no,
                        'pan_card_image' => $url
                       );
                }
                if($docTypeName == 'Aadhar'){
                    $data = array(
                        'aadhar_number' => $ref_no,
                        'aadhar_image' => $url
                       );
                }
                if(count($data) > 0){
                    DB::table('employee')->where('emp_id', $emp_id)->update($data);
                }               
            }
            if ($doc_id_exists && $doc_id_exists->doc_id) {
                DB::table('emp_docs')
                        ->where('doc_id', $doc_id_exists->doc_id)
                        ->update(['employee_id' => $emp_id,
                            'reference_no' => $ref_no,
                            'doc_name' => $fileName,
                            'doc_type' => $documentType,
                            'doc_url' => $url,
                            'extract_text' => $extract_text,
                            'updated_by' => Session('userId'),
                            'updated_at' => date('Y-m-d H:i:s')
                ]);
                $docArray = DB::table('emp_docs')
                        ->join('master_lookup', 'master_lookup.value', '=', 'emp_docs.doc_type')
                        ->select('doc_id', 'master_lookup_name as doc_type', 'reference_no', 'doc_url', 'extract_text', DB::raw('GetUserName(emp_docs.created_by,2) as created_by'))
                        ->where('employee_id', $emp_id)
                        ->get()->all();
                   
                foreach ($docArray as $doc) {
                    if ($doc->doc_url && $doc->doc_url != '') {
                        $status_img = '<img src="/img/unverified.png" width="50">';
                        if($doc->reference_no == $doc->extract_text ){
                            $status_img = '<img src="/img/verified.jpeg" width="50">';
                        }
                        $docText .='<tr>
                        <td>' . $doc->doc_type . '</td>
                        <td>' . $doc->reference_no . '</td>
                        <td>' . $status_img . '</td>
                        <td>' . $doc->extract_text . '</td>
                        <td>' . $this->employeeModel->getUserNameById($doc->created_by) . '</td>
                        <td align="center">
                             <span><a href="' . $doc->doc_url . '" target="_blank"><i class="fa fa-download"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a class="delete grn-del-doc" id="' . $doc->doc_id . '" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a>
                            </span>
                        </td>
                        </tr>';
                    } else {
                        $status_img = '<img src="/img/unverified.png" width="50">';
                        if($doc->reference_no == $doc->extract_text ){
                            $status_img = '<img src="/img/verified.jpeg" width="50">';
                        }
                        $docText .='<tr>
                        <td>' . $doc->doc_type . '</td>
                        <td>' . $doc->reference_no . '</td>
                        <td>' . $status_img . '</td>
                        <td>' . $doc->extract_text . '</td>
                        <td>' . $this->employeeModel->getUserNameById($doc->created_by) . '</td>
                        <td align="center">
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="delete grn-del-doc" id="' . $doc->doc_id . '" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a>
                        </td>
                        </tr>';
                    }
                }
                return Response::json(array('status' => 200, 'message' => 'Successfully uploaded.', 'docText' => $docText, 'refresh' => 1,'count' => count($docArray)));
            } else {
                $docsArr = array(
                    'employee_id' => $emp_id,
                    'reference_no' => $ref_no,
                    'doc_name' => $fileName,
                    'doc_type' => $documentType,
                    'doc_url' => $url,
                    'extract_text' => $extract_text,
                    'created_by' => Session('userId'),
                    'created_at' => date('Y-m-d H:i:s')
                );
                $createdBy = $this->employeeModel->getUserNameById($docsArr['created_by']);
                $doc_id = DB::table('emp_docs')->insertGetId($docsArr);
                if (isset($docsArr['doc_url']) && $docsArr['doc_url'] != null) {
                    $status_img = '<img src="/img/unverified.png" width="50">';
                    if($docsArr['reference_no'] == $docsArr['extract_text'] ){
                        $status_img = '<img src="/img/verified.jpeg" width="50">';
                    }
                    $docText = '<tr>
                        <td><input type="hidden" name="docs[]" value="' . $emp_id . '">' . $document_name . '</td>
                        <td>' . $docsArr['reference_no'] . '</td>
                        <td>' . $status_img . '</td>
                        <td>' . $docsArr['extract_text'] . '</td>
                        <td>' . $createdBy . '</td>
                        <td align="center"><span><a href="' . $docsArr['doc_url'] . '" target="_blank"><i class="fa fa-download"></i></a> &nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="delete grn-del-doc" id="' . $doc_id . '" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a></span>
                        </td>
                    </tr>';
                } else {
                    $status_img = '<img src="/img/unverified.png" width="50">';
                    if($docsArr['reference_no'] == $docsArr['extract_text'] ){
                        $status_img = '<img src="/img/verified.jpeg" width="50">';
                    }
                    $docText = '<tr>
                        <td><input type="hidden" name="docs[]" value="' . $emp_id . '">' . $document_name . '</td>
                        <td>' . $docsArr['reference_no'] . '</td>
                        <td>' . $status_img . '</td>
                        <td>' . $docsArr['extract_text'] . '</td>
                        <td>' . $createdBy . '</td>
                        <td align="center">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="delete grn-del-doc" id="' . $doc_id . '" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>';
                }
                return Response::json(array('status' => 200, 'message' => 'Successfully uploaded.', 'docText' => $docText,"count" =>count($docsArr)));
            }           
        } catch (Exception $e) {
            return Response::json(array('status' => 400, 'message' => 'Failed to upload'));
        }
    }

    public function deleteDoc($empid) {
        if ($empid != null) {
            $url = DB::table('emp_docs')->where('doc_id', $empid)->pluck('doc_url')->all();
            $objectUrl = isset($url[0]) ? $url[0] : null;
            $productObj = new ProductRepo();
            $result = $productObj->deleteFromS3($objectUrl);
            DB::table('emp_docs')
                    ->where("doc_id", $empid)
                    ->delete();
        }
    }

    public function saveProfilePic($empid) {
        try {
            $status = false;
            $message = "Unable to save profile picture";
            $data = Input::all();
            $user_id = Session::get('userId');
            $path = $this->employeeModel->saveProfilePic($data, $user_id, $empid);
            if (!empty($path)) {
                $status = true;
                $message = "Success";
                // Session::put('userLogoPath',$path);
            } else {
                $message = "Incorrect file type!! Please try with JPEG or PNG images";
            }
            return json_encode([
                'path' => $path,
                'status' => $status,
                'message' => $message
            ]);
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getReportingMangers($role_array,$empId)
    {
        try
        {
            Db::enablequerylog();
            $userId = Session::get('userId'); 
            $userRoleId=array();
            $userCollection = "";
            $role_parent = DB::table('roles')
                ->where(['role_id' => $role_array])
                ->first(['parent_role_id']);
            if(!empty($role_parent->parent_role_id))
            {
                $tempRoles = $this->getParentRolesbyRoleId($role_parent->parent_role_id);
                if (!empty($tempRoles) && count($tempRoles) > 0) {
                     $userRoleId = array_merge($userRoleId, $tempRoles);
                } 
            }
            if (!empty($userRoleId)) {
                $super_id = array(1);
                $userRoleId =array_unique($userRoleId);
                $userRoleId = array_diff($userRoleId,$super_id);
                $userCollection = DB::table('users')
                        ->join('user_roles', 'user_roles.user_id', '=', 'users.user_id')
                        ->join('employee', 'employee.emp_id', '=', 'users.emp_id')
                        ->whereIn('user_roles.role_id', $userRoleId)
                        ->where('users.is_active', 1)
                        ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                        ->orderBy('users.user_id')
                        ->groupBy('users.user_id')
                        ->get()->all();
            }
            foreach ($userCollection as $userDetails) {
                $userData= DB::select('select getRepManagernameHierarchy(' . $userDetails->user_id . ') as name');
                $result = json_decode(json_encode($userData), true);
                $userDetails->name=$result[0]['name'];
            }
            $response = $userCollection;
//            \Log::info(DB::getQueryLog());
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return $response;
    }

    public function getParentRolesbyRoleId($roleId) {
        try {
            if ($roleId > 0) {
                $this->roles[] = $roleId;
                $result = DB::table('roles')
                        ->where(['role_id' => $roleId])
                        ->first(['parent_role_id']);
                if (!empty($result)) {
                    $newRoleId = property_exists($result, 'parent_role_id') ? $result->parent_role_id : 0;
                    if (!in_array($newRoleId, $this->roles)) {
                        $this->getParentRolesbyRoleId($newRoleId);
                    }
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return $this->roles;
    }

    public function updateEmployeePersonalDetails(Request $request)
    {
        try {
            $data = $request->all();
            $emp_id = $data['emp_personal_id'];
            unset($data['_token']);
            unset($data['emp_personal_id']);
            $empPersonalInfo = $this->empContactModelObj->getEmpPersonalDetails($emp_id);
            if($empPersonalInfo!="")
            {
                $res = $this->empContactModelObj->
                    where("employee_id",$emp_id)
                    ->update($data);
            }
            else
            {
                $data['employee_id'] = $emp_id;
                $res = $this->empContactModelObj
                        ->insert($data);
            }
            $empPersonalInfo = $this->empContactModelObj->getEmpPersonalDetails($emp_id);

            $empPersonalInfo = json_decode(json_encode($empPersonalInfo), true);
            return json_encode([
                'status' => true,
                'message' => "Successfully updated.",
                'data' => $empPersonalInfo
            ]);
       }
       catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    
    public function validateAadharno()
    {
        try {
            $response = json_encode([ "valid" => false]);
            $data = Input::all();
            $emp_id = isset($data['emp_id']) ? $data['emp_id'] : '';
            $aadhar_number = isset($data['aadhar_number']) ? $data['aadhar_number'] : '';
            if (empty($emp_id))
            {
                $checkRs = DB::table('employee')
                            ->where('aadhar_number',$aadhar_number)
                            ->where('is_active',1)
                            ->first();
                if(!empty($checkRs))
                {
                    $response = json_encode([ "valid" => false]);
                }else
                {
                    $response = json_encode([ "valid" => true]);
                }
            }else
            {
                 if (!empty($emp_id))
                {
                    $checkRs = DB::table('employee')
                                ->where('aadhar_number',$aadhar_number)
                                ->where('emp_id','!=',$emp_id)
                                ->where('is_active',1)
                                ->first();
                    if(!empty($checkRs))
                    {
                        $response = json_encode([ "valid" => false]);
                    }else
                    {
                        $response = json_encode([ "valid" => true]);
                    }
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
            $response = json_encode([ "valid" => "Sorry, Mobile Validation Fails. Please Come back later"]);
        }
        return $response;
        
    }

   public function saveCertificationDetails(Request $request,$empId)
    {
        try{
            DB::enablequerylog();
            $data = $request->all();
            $cerText ="";
            $data['certified_on'] = isset($data['certified_on'])?date('Y-m-d', strtotime( $data['certified_on'] )):"";
            if(isset($data['valid_upto']) && $data['valid_upto']!=null)
            {
                $data['valid_upto'] = date('Y-m-d', strtotime( $data['valid_upto'] ));
            }else
            {
                $data['valid_upto'] = null;
            }
            

            if($data['employee_certification_id']=="")
            {
                unset($data['employee_certification_id']);
                $rs = $this->empContactModelObj->InsertCertificationDetails($empId,$data);
            }else
            {
                $rs = $this->employeeModel->UpdateCertificationDetails($data['employee_certification_id'],$data);
            }
            $certificationArray = $this->empContactModelObj->getEmpCertiDetails($empId);
            foreach ($certificationArray as $doc)
            {
                $cerText .='<tr><td>'.$doc['certification_name'].'</td><td>' . $doc['institution_name'] . '</td><td>' . $doc['grade'] . '</td><td>' .$doc['certified_on']. '</td><td>' . $doc['valid_upto'] . '</td>   <td><span><a  onclick="editCertifications('.$doc['employee_certification_id'].')" id="'.$doc['employee_certification_id'].'"  href="javascript:void(0);"><i class="fa fa-pencil"></i></a></span>&nbsp;&nbsp;&nbsp;&nbsp;<span><a class="delete grn-del-doc" id="'.$doc['employee_certification_id'].'"  href="javascript:void(0);"><i class="fa fa-trash-o"></i></a></span></td></tr>';
            }
        }catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
            $response = json_encode([ "valid" => "Sorry, Mobile Validation Fails. Please Come back later"]);
        }
        return Response::json(array('status' => 200, 'message' => 'Successfully updated.', 'cerText' => $cerText, 'count' => count($cerText)));
    }
    public function deleteCertification($certification_id,$empId)
    {
        try{
            $rs = $this->empContactModelObj->DeleteCertificationDetails($certification_id);
            
        }
        catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
            $response = json_encode([ "valid" => "Sorry, Mobile Validation Fails. Please Come back later"]);
        }
        return Response::json(array('status' => 200, 'message' => 'Successfully deleted.'));
    }
    public function myProfile($my_user_id)
    {
        $session_userId = Session::get("userId");
        if(md5($session_userId) == $my_user_id)
        {
            $emp_id = $this->getEmpIdByUserId($session_userId);
            return $this->editUsers($emp_id,$my_user_id);
        }
    }
    public function getEmpIdByUserId($userId)
    {
       $emp_id = DB::table('users')
                ->where('user_id',$userId)
                ->pluck('emp_id')->all();
        return $emp_id[0];
    }
      public function updateEmpBankInfo(Request $request,$empId)
    {
        try {
            $data = $request->all();
            $bankInfo =$this->empContactModelObj->empBankInfo($empId);
            $oldbankdetails=$bankInfo;
            if(!empty($oldbankdetails)){ 
                $oldbankdetails['acc_type'] = $this->employeeModel->getMasterLookupNameById($oldbankdetails['acc_type']);
                $oldbankdetails['currency_code'] = $this->employeeModel->getMasterLookupNameById($oldbankdetails['currency_code']);
            }

            if(count($bankInfo)>0)
            {
                $res = $this->empContactModelObj->
                    UpdateBankDetails($data,$empId);
            }
            else
            {
                $data['emp_id']=$empId;
                $res = $this->empContactModelObj
                        ->InsertBankDetails($data);
            }
            $empfullname= $this->empContactModelObj->
                     getempfullname($empId);
            
            $bankInfo =$this->empContactModelObj->empBankInfo($empId);
            $bankInfo['acc_type'] = $this->employeeModel->getMasterLookupNameById($data['acc_type']);
            $bankInfo['currency_code'] = $this->employeeModel->getMasterLookupNameById($data['currency_code']);
            if($res){
                $notificationObj= new NotificationsModel();
                $userIdData= $notificationObj->getUsersByCode('HRMS001');

                $userIdData=json_decode(json_encode($userIdData));

                $purchaseOrder = new PurchaseOrder();
                $userEmailArr = $purchaseOrder->getUserEmailByIds($userIdData);

                $toEmails = array();
                $subject = "Bank Information Update Alert";
                if(is_array($userEmailArr) && count($userEmailArr) > 0) {
                    foreach($userEmailArr as $userData){
                        $toEmails[] = $userData['email_id'];
                    }
                }

                try{
                    // Mail::send('emails.hrmsbankinfomail', ['oldbankdetails'=>$oldbankdetails,'empfullname'=>$empfullname,'bankInfo'=>$bankInfo], function ($message) use ($toEmails,$subject) {
                    //         $message->to($toEmails)->subject($subject );
                    //     });
                    $body = array('template'=>'emails.hrmsbankinfomail', 'attachment'=>'', 'name'=>'Hello!','oldbankdetails'=>$oldbankdetails, 'empfullname'=>$empfullname,'bankInfo'=>$bankInfo);

                    Utility::sendEmail($toEmails, $subject, $body);
                }
                catch (\ErrorException $ex) {
                    \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
                }

            }
            
            return json_encode([
                'status' => true,
                'message' => "Successfully Updated.",
                'data' => $bankInfo
            ]);

        }
       catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    public function uploadEductionDetails(Request $request,$empId)
    {
        $data = $request->all();
        $eduText ="";
        $data['from_year'] = isset($data['from_year'])?date('Y-m-d',strtotime( $data['from_year'] )):"";
        if(isset($data['to_year']) && $data['to_year']!=null)
        {
            $data['to_year'] = date('Y-m-d', strtotime( $data['to_year'] ));
        }else
        {
            $data['to_year'] = null;
        }
        
        if($data['emp_education_id'] == "")
        {
            $rs = $this->empContactModelObj->InsertEductionDetails($empId,$data);
        }else
        {
            $rs = $this->empContactModelObj->UpdateEductionDetails($data['emp_education_id'],$data);
        }
        $eduArray = $this->empContactModelObj->getEmpEductionDetails($empId);
        foreach ($eduArray as $doc)
        {
            $eduText .='<tr><td>'.$doc['institute'].'</td><td style="text-align: right;">' . $doc['degree'] . '</td><td>' . $doc['specilization'] . '</td><td style="text-align: right;">' . $doc['grade'] . '</td><td style="text-align: right;">' . $doc['from_year'] . '</td><td style="text-align: right;">' . $doc['to_year'] . '</td><td><span><a onclick="editEducation('.$doc['emp_education_id'].')" href="javascript:void(0);"><i class="fa fa-pencil"></i></a></span>&nbsp;&nbsp;&nbsp;&nbsp;<span><a class="delete delete_educations" id="'.$doc['emp_education_id'].'" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a></span></td></tr>';
        }
        return Response::json(array('status' => 200, 'message' => 'Successfully updated.', 'eduText' => $eduText, 'count' => count($eduArray)));
    }
    public function deleteeducation($empid) 
    {
        $rs = DB::table('emp_education')
            ->where('emp_education_id', $empid)
            ->delete();
        return $rs;

    }
      public function saveInsuranceDetails($empid,Request $request)
    {
        $data = $request->all();
        if(!empty($data))
        {
            if(isset($data['child_one_dob']) && $data['child_one_dob']!= null)
            {
                $data['child_one_dob'] = date('Y-m-d', strtotime( $data['child_one_dob'] ));
            }else
            {
                $data['child_one_dob'] = null; 
            }
            if(isset($data['spouse_dob']) && $data['spouse_dob']!= null)
            {
                $data['spouse_dob'] = date('Y-m-d', strtotime( $data['spouse_dob'] ));
            }else
            {
                $data['spouse_dob'] = null;         
            }
            if(isset($data['child_two_dob']) && $data['child_two_dob']!= null)
            {
                $data['child_two_dob'] = date('Y-m-d', strtotime( $data['child_two_dob'] ));
            }else
            {
                $data['child_two_dob'] = null; 
            }
            $checkEmpInsu = $this->empContactModelObj->checkEmpInsurance($empid);
            if($checkEmpInsu =="")
            {
                $data = array_filter($data);
                if(!empty($data))
                $checkEmpInsu = $this->empContactModelObj->InsertEmpInsurance($empid,$data);
            }else
            {
                $checkEmpInsu = $this->empContactModelObj->UpdateEmpInsurance($empid,$data);
            }
            $checkEmpInsu = $this->empContactModelObj->checkEmpInsurance($empid);
            return Response::json(array('status' => 200, 'message' => 'Successfully saved.', 'data' => $checkEmpInsu));
        }else
        {
            return Response::json(array('status' => "false", 'message' => 'Successfully saved.', 'data' => ""));
        }       
    }
    public function saveEmpExperienceInfo($empId, Request $request)
    {
        try{

            $data =  $request->all();
            $data['from_date'] = isset($data['from_date'])?date('Y-m-d', strtotime( $data['from_date'] )):"";
            $data['to_date'] = isset($data['to_date'])?date('Y-m-d', strtotime( $data['to_date'] )):"";
            if($data['work_experience_id']=="")
            {
                unset($data['work_experience_id']);
                $rs  = $this->empContactModelObj->InsertEmpExperience($empId,$data);
             }else
             {
                $rs  = $this->empContactModelObj->UpdateEmpExperience($empId,$data);
             }
           
            return $this->getExperienceInfo($empId);
        }
        catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
     public function getExperienceInfo($empId)
    {
        try{
            $rs  = $this->empContactModelObj->getExperienceData($empId);
            $eduText ="";
            if(!empty($rs))
            {
                foreach ($rs as $experienceValue)
                {
                    $eduText .="<tr><td>".$experienceValue['designation']."</td><td>".$experienceValue['organization_name']."</td><td>".$experienceValue['from_date']."</td><td>".$experienceValue['to_date']."</td><td>".$experienceValue['location']."</td><td>".$experienceValue['reference_name']."</td><td>".$experienceValue['reference_contact_number']."</td><td><span><a class='delete' id='".$experienceValue['work_experience_id']."' onclick='editExperience(".$experienceValue['work_experience_id'].")'  href='javascript:void(0);'><i class='fa fa-pencil'></i></a></span>&nbsp;&nbsp;&nbsp;&nbsp;<span><a class='delete delete_experience' id='".$experienceValue['work_experience_id']."'  href='javascript:void(0);'><i class='fa fa-trash-o'></i></a></span></td></tr>";
                }
            }
            return Response::json(array('status' => 200, 'message' => 'Successfully saved.',"data"=> $eduText , "count"=>count($eduText)));
        }
        catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    public function getEmpExperienceInfobyid($experiId)
    {
        $rs  = $this->empContactModelObj->getEmpExperienceDataById($experiId);
        return Response::json(array('status' => 200, 'message' => 'Successfully saved.',"data"=> $rs ));
    }
    public function deleteExperience($experiId){
        $rs  = $this->empContactModelObj->DeleteEmpExperience($experiId);
        return Response::json(array('status' => 200, 'message' => 'Successfully saved.' ));
    }


    public function getIfscListFromDatabase(){
        try{
            $term = Input::get('term');
            $user_name = $this->employeeModel->getIfsclist($term);
            echo json_encode($user_name);
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }

    public function editCertification($id)
    {
        $rs = $this->empContactModelObj->getEmpCertificationData($id);
        return $rs;
    }
    public function editEducation($id)
    {
        $rs = $this->empContactModelObj->editEducationById($id);
        return $rs;
    }


     public function checkPassword(){
        try {
            $data = Input::all();
            $result = false;
            $password = md5($data['oldpassword']); 
            $empid = $data['empid'];
            $id  = $this->employeeModel->checkPasswordInTable($password,$empid);
            if(empty($id)) {
                $result = false;
            }
            else{
                $result = true;
            }
            return json_encode(array('valid' => $result));
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function passwordReset(){
        try {
            $data = Input::all();
            $result = false;
            $password = md5($data['resetoldpassword']); 
            $empid = $data['empid'];
            /* emp_id it is the emp_id its user_id in users table*/
            $id  = $this->employeeModel->checkPasswordInUsersTable($password,$empid);
            if(empty($id)) {
                $result = false;
            }
            else{
                $result = true;
            }
            return json_encode(array('valid' => $result));
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function changePassword(){
        try {
            $data = Input::all();
            $status = false;
            $message = 'Unable to change password at the moment. Please try again..';
            $userId = Session::get('userId');

            if($data['newpassword'] == $data['confirmpassword']){
            $id  = $this->employeeModel->changePasswordInUsersAndEmployeeTable($data['empid_update_password'],$data['newpassword']);
            $status = true;
            $message = 'Password changed successfully';
            }
            else{
              $message = 'New password and confirm password does not match';
            }
            return json_encode([
                            'status' => $status,
                            'message' => $message]);
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function accessSpecificChangePassword(){
        try {
            $data = Input::all();
            $status = false;
            $message = 'Unable to change password at the moment. Please try again..';
            $userId = Session::get('userId');

            if($data['resetnewpassword'] == $data['userpasswordconfirm']){
            $id  = $this->employeeModel->resetPasswordInUsersAndEmployeeTable($data['userId_update_password'],$data['resetnewpassword']);
            $status = true;
            $message = 'Password changed successfully';
            }
            else{
              $message = 'New password and confirm password does not match';
            }
            return json_encode([
                            'status' => $status,
                            'message' => $message]);
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    } 

     public function holidaydaashboard(){
        // get empid by userid
        $empId = $this->employeeModel->getEmployeeId(Session::get('userId'));
        if($empId == 0){
            Redirect::to('/')->send();
        }
        $holidaylists = $this->employeeModel->EmployeeHolidayList($empId);
            //echo "<pre/>";print_r($holidaylists[0]->emp_group_id);exit;
        $holidays = $this->employeeModel->EmployeeHolidays($holidaylists[0]->emp_group_id);
        $Importpermission = $this->roleAccess->checkPermissionByFeatureCode('IMPEMP001');
        return view('HrmsEmployees::empHolidayList')->with(array(
                        'holidaylist'      =>$holidaylists,
                        'holidays'         =>$holidays,
                        'Importpermission' =>$Importpermission
                        ));

    }

    public function getholidaylist(Request $request){

        $data = $request->input();
        $data = $this->employeeModel->EmployeeHolidays($data['holiday'],$data['year']);
        return $data;
    }

    public function downloadHolidayImportExcel(){
        $mytime = Carbon::now();
        $headers = array('Occasion','Date(dd/mm/yyyy)','Is Fixed','Employee Group Id');
        $headers_second_page = array('Employee group Id','Employee group name');
        $ffmDet = DB::table('emp_groups')
                    ->select('emp_group_id','group_name')->get()->all();
        $ffmDet = json_decode(json_encode($ffmDet),1);
        $loopCounter = 0;
        $exceldata_second = array();
        foreach($ffmDet as $val){
            $exceldata_second[$loopCounter]['emp_group_id'] = $val['emp_group_id'];
            $exceldata_second[$loopCounter]['group_name'] = $val['group_name'];
            $loopCounter++;
        }
        $file_name = 'Holiday Template Sheet_' . $mytime->toDateTimeString();
        $result = Excel::create($file_name, function($excel) use($headers, $headers_second_page, $exceldata_second) {
            $excel->sheet('Holiday List', function($sheet) use($headers) {
                    $sheet->fromArray($headers);
                    $sheet->setColumnFormat(array(
                        'B' => 'dd/mm/yyyy'));
                });
            $excel->sheet("Holiday Data", function($sheet) use($headers_second_page, $exceldata_second){
                $sheet->loadView('HrmsEmployees::holidayimport',array('headers' => $headers_second_page,'data' => $exceldata_second)); 
            });
        })->export('xlsx');
    }

    public function  importHolidayExcel(){
        try{
            $msg = '';
            $environment    = env('APP_ENV');
            $file_data                      = Input::file('holidaycalender');
            $file_extension                 = $file_data->getClientOriginalExtension();

            if($file_extension != 'xlsx'){
                $returnArray = array('status' => "failed", 'message' =>"Invalid file type");
            }else{
                if (Input::hasFile('holidaycalender')) {
                    $path = Input::file('holidaycalender')->getRealPath();
                    $result = $this->readImportExcel($path);
                    $data = json_decode(json_encode($result['prod_data']), 1);
                    $headers                        = json_decode(json_encode($result['cat_data']), true);
                    $headers[1]                     = 'Date(dd/mm/yyyy)';
                    $headers1                       = array('Occasion','Date(dd/mm/yyyy)','Is Fixed','Employee Group Id');
                    $recordDiff                     = array_diff($headers,$headers1);
                    if(empty($recordDiff) && count($recordDiff)==0){
                        $timestamp = md5(microtime(true));
                        $txtFileName = 'schedules-import-' . $timestamp . '.txt';
                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'schedules_log' . DIRECTORY_SEPARATOR . $txtFileName;
                        $excelRowcounter = 1;
                        $data = array_filter($data);
                        foreach ($data as $index=>$holidaydata) {
                            $import = 'true';
                             $excelRowcounter++;
                            $msg .= $excelRowcounter.".";
                            if(!isset($holidaydata['employee_group_id']) || (isset($holidaydata['employee_group_id']) && (!in_array($holidaydata['employee_group_id'],["1","2"])))){
                                $import='false';
                                $msg .="Employee group id  should be 1(Ebutor tech group) or 2(Ebutor others).  " . PHP_EOL;
                            }else{
                                if(!isset($holidaydata['dateddmmyyyy']) || (isset($holidaydata['dateddmmyyyy']) && empty($holidaydata['dateddmmyyyy']))){
                                    $import='false';
                                    $msg .="Date should not be empty.  " . PHP_EOL;
                                }else{
                                    $date = is_array($holidaydata) ? $holidaydata['dateddmmyyyy'] :'1970-01-01' ;
                                    $date = date("Y-m-d", strtotime($date['date']));
                                    $year= $finaldate=strtok($date, '-');
                                    $invalid_date=substr($year, 0,2);
                                    if($date=="" || $date=='1970-01-01'|| (strpos($date,'1900') !== false)||($invalid_date==19) ){
                                    $import='false';
                                    $msg .="Date is not valid, please check date format (dd/mm/yyyy)!" . PHP_EOL;
                                    }else{
                                        $year= $finaldate=strtok($date, '-');
                                        $valid=$this->employeeModel->validateDate($date,$holidaydata['employee_group_id']);
                                        if(count($valid)== 0){
                                            $data[$index]['date']=$date;
                                        }else{
                                            $import='false';
                                            $msg .="Date already exist for given employee group id.  " . PHP_EOL;
                                        }
                                        unset($data[$index]['dateddmmyyyy']);
                                        if(!isset($holidaydata['occasion']) || (isset($holidaydata['occasion']) && empty($holidaydata['occasion']))){
                                            $import='false';
                                            $msg .="Occasion should not be empty.  " . PHP_EOL;
                                        }else{
                                            $valid=$this->employeeModel->validateReason($holidaydata['employee_group_id'],$year);
                                            if($valid==""){
                                                $holidaydata['occasion']=$holidaydata['occasion'];
                                            }else{
                                                $lowercase=strtolower($holidaydata['occasion']);
                                                foreach($valid as $string){
                                                    $finalstring=strtolower($string['holiday_name']);
                                                    if($lowercase==$finalstring){
                                                        $import='false';
                                                        $msg .="Occasion already exist for given employee group id.  " . PHP_EOL;
                                                    }else{
                                                        $holidaydata['occasion']=$holidaydata['occasion'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if(!isset($holidaydata['is_fixed']) || (isset($holidaydata['is_fixed']) && (!in_array($holidaydata['is_fixed'],["0","1"])))){
                                $import='false';
                                $msg .="Type should be 0(Optional) or 1(Fixed).  " . PHP_EOL;
                            }
                            unset($data[$index][0]);
                            if($import == 'true'){
                                $msg = str_replace($excelRowcounter.".", "", $msg);
                            }
                        }
                        if(($import == 'true') && ($msg=='')){
                            $insert_list =  $this->employeeModel->importList($data);
                            if($insert_list){
                                $returnArray = array( 'status' => "success", 'message' => 'Uploaded successfully');
                            }else{
                                $returnArray = array( 'status' => "failed", 'message' => "Please try again");
                            }
                        }else{
                            $file = fopen($file_path, "w");
                            fwrite($file, $msg);
                            fclose($file);
                            $message = "Click <a href=".'/'.$file_path." target='_blank'> here </a> to view details.";
                            $returnArray = array('status' => "failed", 'message' =>$message);
                        }
                    }else{
                       $returnArray = array('status' => "failed", 'message' =>'Invalid data!');
                    }
                }else{
                    $returnArray = array( 'status' => "failed", 'message' => 'Please select file');
                }
            }
            return Response::json($returnArray);
        }catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' =>'Invalid'));
        }

    }

    public function readImportExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        $reader->ignoreEmpty();
                    })->first();
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                     $reader->ignoreEmpty();   
                    })->get()->all();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) { 
            Log::error($ex->getMessage());
        }
    }


    public function savePanPic($empid) {
        try {
            $status = false;
            $message = "Unable to save pan image";
            $data = Input::all();
            $user_id = Session::get('userId');
            $path = $this->employeeModel->savePanPic($data, $user_id, $empid);
            if (!empty($path)) {
                $status = true;
                $message = "Success";
            } else {
                $message = "The selected file is not valid";
            }
            return json_encode([
                'path' => $path,
                'status' => $status,
                'message' => $message
            ]);
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function saveAadharPic($empid) {
        try {
            $status = false;
            $message = "Unable to save Aadhar image";
            $data = Input::all();
            $user_id = Session::get('userId');
            $path = $this->employeeModel->saveAadharPic($data, $user_id, $empid);
            if (!empty($path)) {
                $status = true;
                $message = "Updated successfully";
            } else {
                $message = "The selected file is not valid";
            }
            return json_encode([
                'path' => $path,
                'status' => $status,
                'message' => $message
            ]);
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    /** 
     * Get the name of the Document 
     * @param  integer $id Document ID from the database
     * @return String     Name of the document
     */
    function getDocumentTypeName($id){
        $docArr = [
                    '150001' => 'PAN',
                    '150002' => 'Aadhar',
                    '150003' => 'VoterCard',
                    '150004' => 'DrivingLicense',
                ];
        return $docArr[$id];
    }
}