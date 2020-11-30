<?php
namespace App\Http\Controllers;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\CustomerRepo;
use Session;
use Illuminate\Support\Facades\Cache;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use DB;
use Log;
use Caching;
use Redirect;
use App\models\Locations\Locations;
use App\models\BusinessUnit\BusinessUnit;
use App\models\MasterLookup\MasterLookup;
use App\models\User\User;
use Illuminate\Http\Request;
use App\models\EmailTemplate\EmailTemplate;
use \URL;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AuthenticationController extends BaseController {

    public $roleAccess;
    public $custRepoObj;

    public function __construct(RoleRepo $roleAccess, CustomerRepo $custRepoObj) {
        $this->roleAccess = $roleAccess;
        $this->custRepoObj = $custRepoObj;
    }

    function index(Request $request) {
        if (Session::has('userId'))
        {
            return Redirect::to('/');
        }else{
            $redirectUrl = $request->path();
            return View::make('login')->with(['redirect_url' => $redirectUrl]);
        }
    }

    function checkAuth() {
        try {
            $status = 0;
            $validator = Validator::make(
                            array(
                        'email' => Input::get('email'),
                        'password' => Input::get('password')
                            ), array(
                        'email' => 'required|email',
                        'password' => 'required'
                            )
            );
            if ($validator->fails()) {
                $row = Input::get();
                $messages = $validator->messages();
                $messageArr = json_decode($messages);
                $errorMsg = '';
                if (isset($messageArr->email[0]))
                    $errorMsg .= $messageArr->email[0];
                if (isset($messageArr->password[0]))
                    $errorMsg .= '<br>' . $messageArr->password[0];
                //print_r(Input::get()); die;
                //return View::make('login')->with(array('row'=>$row,'errorMsg'=>$errorMsg));
//                return Redirect::to('login')->with('errorMsg', $errorMsg);
                return json_encode(['status' => $status, 'message' => $errorMsg]);
            }else {
                $email = Input::get('email');
                $password = Input::get('password');
                $result = $this->roleAccess->authenticateUser($email, $password);
                $check_email=DB::table('users')->select('user_id')->where('email_id',$email)->count();
                if($check_email==0){
                    $errorMsg='Invalid email';
                   return json_encode(['status' => $status, 'message' => $errorMsg]);
                }
                $get_invalid_pswd_count=DB::Table('users')->select('invalid_password_count')->where('is_active',1)->where('email_id',$email)->first();
                if($get_invalid_pswd_count=='' || $get_invalid_pswd_count->invalid_password_count==5){
                   $update_pwswd_count=DB::Table('users')->where('is_active',1)->where('email_id',$email)->update(['invalid_password_count'=>0,'is_active'=>0]);
                   $errorMsg='Due to multiple wrong attempts user is deactivated,Please contact admin';
                   if($get_invalid_pswd_count=='')
                   $errorMsg='User is deactivated,Please contact admin';
                   return json_encode(['status' => $status, 'message' => $errorMsg]);
                }
                $data_count=$get_invalid_pswd_count->invalid_password_count;
                if (empty($result)) {
                    $data_count+=1;
                    $update_pwswd_count=DB::Table('users')->where('email_id',$email)->where('is_active',1)->update(['invalid_password_count'=>$data_count]);
                    $row = Input::get();
                    $errorMsg = "Invalid email or password";
                    return json_encode(['status' => $status, 'message' => $errorMsg]);
                  // return View::make('login')->with(array('row' => $row));
                } else {
                    $update_pswd_count=DB::Table('users')->where('email_id',$email)->where('is_active',1)->update(['invalid_password_count'=>0]);
                    $result = $result[0];
                    $role = $this->roleAccess->getRolebyUserId($result->user_id);
                    $cusomerLogo = '';
                    if ($result->legal_entity_id > 0) {
                        $cusomerLogo = $this->custRepoObj->getCustomerLogo($result->user_id);
                        $cusomerLogo = isset($cusomerLogo[0]) ? $cusomerLogo[0]->profile_picture : '';
                    }
                    if (!empty($role)) {
                        $rolesArray = [];
                        foreach($role as $roleInfo)
                        {
                            $roleId = property_exists($roleInfo, 'role_id') ? $roleInfo->role_id : '';
                            $rolesArray[] = $roleId;
                        }
                        Session::put('userId', $result->user_id);
                        Session::put('userName', $result->firstname.' '.$result->lastname);
                        Session::put('roleId', $role[0]->role_id);
                        Session::put('roles', implode(',', $rolesArray));
                        Session::put('fullname', $result->firstname.' '.$result->lastname);
                        Session::put('legal_entity_id', $result->legal_entity_id);
                        Session::put('password', $password);
                        date_default_timezone_set('Asia/Kolkata');
                        $loginTime = date('Y-m-d H:i:s');
                        Session::put('login_time', $loginTime);
                        
                        if (!empty($cusomerLogo) && $cusomerLogo != '') {
                            Session::put('customerLogoPath', 'uploads/customers/' . $cusomerLogo);
                            Session::put('parentcustomerLogoPath', 'uploads/customers/' . $cusomerLogo);
                        }
                        if (!empty($result->profile_picture)) {
                            Session::put('userLogoPath', $result->profile_picture);
                            Session::put('parentuserLogoPath', $result->profile_picture);
                        }
                        Session::put('parentuser_id',$result->user_id);
                        Session::put('parentuserName', $result->firstname.' '.$result->lastname);
                        Session::put('parentroleId', $role[0]->role_id);
                        Session::put('parentroles', implode(',', $rolesArray));
                        Session::put('parentfullname', $result->firstname.' '.$result->lastname);
                        Session::put('parentlegal_entity_id', $result->legal_entity_id);
                        Session::put('parentpassword', $password);
                    } else {
                        $UrlerrorMsg = 'You don`t have permission to access this page';
                        return json_encode(['status' => $status, 'message' => $UrlerrorMsg]);
//                        return Redirect::to(URL::previous())->with('errorMsg', $UrlerrorMsg);
                    }
                    $status = 1;
                    return json_encode(['status' => $status, 'message' => '']);
//                    return Redirect::to('/');
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function forgot() {
         try {
    
            $validator = Validator::make(
                            array(
                        'email' => Input::get('emailId'),
                            ), array(
                        'email' => 'required|email',
                            )
            );
            if ($validator->fails()) {
                $messages = $validator->messages();
                $messageArr = json_decode($messages);
                $errorMsg = '';
                if (isset($messageArr->email[0]))
                    $errorMsg .= $messageArr->email[0];

                return $errorMsg;
                exit;
            }else {
                $user = DB::Table('users')->select('user_id')->where('email_id', Input::get('emailId'))->get()->all();
                $token = md5(uniqid(rand(), 1));
                if (isset($user[0]->user_id)) {
                    DB::Table('users')
                            ->where('user_id', $user[0]->user_id)
                            ->update(array('password_token' => $token,
                                            'is_password_updated' => 0,
                                            'password_updated_date' => date('Y-m-d H:i:s')
                                ));
                    $errorMsg = "Please check your email for password reset link.";
                    \Mail::send(['html' => 'emails.auth.reminder'], array('token' => $token), function($message)  {
                    $message->to(Input::get('emailId'))->subject('Reset Password');
                     });
                    
                    return $errorMsg;
                    exit;
                } else {
                    $errorMsg = "Email address not found. Please enter correct email address";
                    return $errorMsg;
                    exit;
                }
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function reset($token) {
        //return $token;
        try {
            $user = DB::table('users')->where('password_token', $token)->first();
            if(empty($user)){
                return Redirect::to('login')->withFlashMessage('Token mismatch for password reset. Please try again');
            }else{
                $pwd_date = strtotime($user->password_updated_date);
                $pwd_flag = $user->is_password_updated;
                $user_id = $user->user_id;
                $curr_date = strtotime(date('Y-m-d H:i:s'));
                $interval  = abs($curr_date - $pwd_date);
                $minutes   = round($interval / 60);
//                Log::info('Minutes: '); Log::info($minutes);
  //              Log::info('pwd_flag: '); Log::info($pwd_flag);
    //            Log::info('curr_date: '); Log::info($curr_date);
      //          Log::info('pwd_date: '); Log::info($pwd_date);

                if($minutes > 60)
                {
                    if($pwd_flag == 1)
                    {
                        return Redirect::to('login')->withFlashMessage('Password reset link expired!!');
                    }elseif($pwd_flag == 0){
                        return Redirect::to('login')->withFlashMessage('Password reset link expired!!');
                    }
                }else if($minutes <= 60){
                    if($pwd_flag == 1)
                    {
                        return Redirect::to('login')->withFlashMessage('You have already reset your password.');
                    }elseif($pwd_flag == 0){
                        return View::make('password.reset')->with('user', $user);
                    }
                }
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            
        }
    }

    public function passwordreset() {
        try {
            $data = Input::all();
            if ($data['resetpswd'] == $data['confirmpswd']) {
                DB::Table('users')
                        ->where('user_id', $data['user_id'])
                        ->update(array('password' => md5($data['confirmpswd']),
                                        'is_password_updated' => 1,
                                        'password_updated_date' => date('Y-m-d H:i:s')
                                        ));
                $ds = DB::getQueryLog();

                return Redirect::to('/login')->withFlashMessage('Password updated successfully');
            } else {
                return Response::json([
                            'status' => false,
                            'message' => 'Passwords not matching.'
                ]);
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function logout() {
        try {
            $userId = Session::get('userId');
            Cache::forget('menu_'.$userId);
            Cache::forget('attributes_list_'.$userId);
            Caching::flush($userId);
//            Cache::tags(['ebutor', 'categories'])->flush('get_category_list_'.$userId);
//            Cache::tags(['ebutor', 'categories'])->flush('get_all_category_list_'.$userId);
//            Cache::tags(['ebutor', 'features'])->flush('get_features_list_'.$userId);
            Session::forget('userId');
            Session::forget('userName');
            Session::forget('userType');
            Session::forget('roleId');
            Session::forget('login_time');
            if (Session::has('roles'))
                Session::forget('roles');
            Session::forget('customerId');
            Session::forget('manf_id');
            if (Session::has('customerLogoPath'))
                Session::forget('customerLogoPath');
            if (Session::has('userLogoPath'))
                Session::forget('userLogoPath');
            if (Session::has('notification_codes'))
                Session::forget('notification_codes');
            if (Session::has('warehouseId'))
                Session::forget('warehouseId');
            if(Session::has('business_unitid'))
                Session::forget('business_unitid');
            if(Session::has('dashboardRedirect'))
                Session::forget('dashboardRedirect');            
            return Redirect::to('/login');
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function users() {
        parent::Breadcrumbs(array('Home' => '/', 'Admistration' => '', 'Users' => '/users/index'));
        $addPermission = $this->roleAccess->checkPermissionByFeatureCode('USR002');
        return View::make('users.index')->with(array('addPermission' => $addPermission));
    }
    

    /*=======Switch Users=========*/

    public function switchUser($id){
        try {
            $session_data = Session::all();
            $session = Session::all();
            $current_user = DB::table('users')->where('user_id',$id)->select('firstname','lastname','legal_entity_id','profile_picture','password','email_id')->first();
            $role = DB::table('user_roles')->where('user_id',$id)->select('role_id')->first();
            if(Session::has('superadmin')){
                $superadmin = Session::get('superadmin');
            }
            else{
                $superadmin = '';
            }
            if(Session::has('otherUser')){
                $otherUser = Session::get('otherUser');   
            }
            else{
                $otherUser = '';
            }
            if(Session::get('userId') == 1 && empty($superadmin)){
                Session::put('superadmin',$session_data);
            }
            if (Session::get('userId') != 1 && empty($otherUser)) {
                Session::put('otherUser',$session_data);               
            }        
            Session::put('userId',$id);
            Session::put('userName',$current_user->firstname.' '.$current_user->lastname);
            Session::put('roleId',$role->role_id);
            Session::put('fullname',$current_user->firstname.' '.$current_user->lastname);
            Session::put('legal_entity_id',$current_user->legal_entity_id);
            Session::put('password',$current_user->password);
            Session::put('userLogoPath',$current_user->profile_picture);
            return json_encode(['status' => true]);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    /*========Switch Admin=========*/

    public function switchAdmin(){
        try {
            $data = Input::all();
            $session = Session::all();
            if(Session::has('superadmin') && $data['user'] == "admin"){
                $superadmin_data = $session['superadmin'];
                Session::forget('superadmin');    
                Session::forget('otherUser');
            }
            if(Session::has('otherUser') && $data['user'] == "parent"){
                $superadmin_data = $session['otherUser'];       
                Session::forget('otherUser');
            }
            Session::put('userId',$superadmin_data['userId']);
            Session::put('userName',$superadmin_data['userName']);
            Session::put('roleId',$superadmin_data['roleId']);
            Session::put('fullname',$superadmin_data['fullname']);
            Session::put('legal_entity_id',$superadmin_data['legal_entity_id']);
            Session::put('password',$superadmin_data['password']);
            Session::put('userLogoPath',isset($superadmin_data['userLogoPath']));

            return json_encode(['status' => true]);

        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
    /*======= Add New Users=======*/ 
    
    public function addUser() {
        try {
            $roles = $this->roleAccess->getRole();
            if (Session::get('legal_entity_id') == 0) {                
                $lookups = MasterLookup::where('mas_cat_id', 7)->get()->all();
                $legalEntitys = $this->custRepoObj->getAllCustomers();               
                //$locationsall = array();
                $businessunits = array();
                return View::make('users.add')->with(array('roles' => $roles, 'lookups' => $lookups, 'legal_Entitys' => $legalEntitys,'businessunits' => $businessunits));
                exit;
            } else {                
                $lookups = MasterLookup::where('mas_cat_id', 7)->get()->all();
                $legalEntitys = $this->custRepoObj->getAllCustomers();
                //$locationsall = Locations::where('manufacturer_id', Session::get('legal_entity_id'))->get()->all();
                $businessunits = BusinessUnit::where('legal_entity_id', Session::get('legal_entity_id'))->get()->all();

                return View::make('users.add')->with(array('roles' => $roles,'businessunits' => $businessunits, 'legal_Entitys' => $legalEntitys, 'lookups' => $lookups));
                exit;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function addNewUser() {
        try {
            $roles = $this->roleAccess->getUserRole();
            if (Session::get('legal_entity_id') == 0) {
                $lookups = MasterLookup::where('category_id', 7)->get()->all();
                $customers = $this->custRepoObj->getAllCustomers();
                $users = DB::table('users')->where(['is_active' => 1, 'customer_type' => 7001])->select('user_id', 'email_id')->get()->all();
                $locationsall = array();
                $businessunits = array();

                return View::make('users.addnew')->with(array('roles' => $roles, 'lookups' => $lookups, 'customers' => $customers, 'locationsall' => $locationsall, 'businessunits' => $businessunits, 'users' => $users));
                exit;
            } else {
                $locationsall = Location::where('manufacturer_id', Session::get('legal_entity_id'))->get()->all();
                $businessunits = BusinessUnit::where('manufacturer_id', Session::get('legal_entity_id'))->get()->all();

                return View::make('users.addnew')->with(array('roles' => $roles, 'locationsall' => $locationsall, 'businessunits' => $businessunits));
                exit;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

      public function saveUser($userId = 0) {
        try {
            if ($userId == 0) {
                $validator = Validator::make(
                                array(
                            'firstname' => Input::get('firstname'),
                            'lastname' => Input::get('lastname'),
                            'customer_type' => Input::get('customer_type'),
                            'email_id' => Input::get('email'),                            
                            'password' => Input::get('password'),
                            'confirm_password' => Input::get('confirm_password'),
                            'phone_no' => Input::get('phone_no')
                                ), array(
                            'firstname' => 'required',
                            'lastname' => 'required',                            
                            'email_id' => 'required|email|unique:users',                            
                            'password' => 'required',
                            'confirm_password' => 'required|same:password',
                            'phone_no' => 'numeric|digits:10'
                                )
                );
            } else {
                $rules = array(
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'email' => 'required|email',
                    'phone_no' => 'numeric|digits:10'
                );

                $fields_value = array(
                    'firstname' => Input::get('firstname'),
                    'lastname' => Input::get('lastname'),
                    'customer_type' => Input::get('customer_type'),
                    'email' => Input::get('email'),
                    'username' => Input::get('username'),
                    'phone_no' => Input::get('phone_no'),
                );
                if (Input::get('password') != '') {
                    $fields_value['password'] = Input::get('password');
                    $fields_value['confirm_password'] = Input::get('confirm_password');
                    $rules['password'] = 'required';
                    $rules['confirm_password'] = 'required|same:password';
                }
                $validator = Validator::make($fields_value, $rules);
            }
            if ($validator->fails()) {

                $messages = $validator->messages();
                return 'fail|' . $messages;
                exit;
            } else {
                $data = Input::get();
                $data['created_by'] = Session::get('userId');               
                $data['created_at'] = date('Y-m-d H:i:s');
                if ($userId == 0) {
                    $password = Input::get('password'); 
                    $data['password'] = md5($password);
                    unset($data['confirm_password']);
                } elseif (Input::get('password') != '') {
                    $password = Input::get('password');
                    $data['password'] = md5($password);
                    unset($data['confirm_password']);
                } else {
                    unset($data['password']);
                    unset($data['confirm_password']);
                }
                if (isset($data['phone_no']))
                    $data['mobile_no'] = $data['phone_no'];
                unset($data['phone_no']);
                if (isset($data['email']))
                    $data['email_id'] = $data['email'];
                unset($data['email']);
                $data['legal_entity_id'] = Session::get('legal_entity_id');

                /* if (isset($data['username']))
                  $data['user_name'] = $data['username']; */
                unset($data['username']);
                unset($data['_method']);
                unset($data['_token']);
                if(isset($data['_Token']))
                {
                    unset($data['_Token']);
                }                
                //unset($data['customer_type']);
                unset($data['manufacturer_id']);
                $roleId = $data['role_id'];
                unset($data['role_id']);
//                \Log::info($data);
                if (!isset($data['is_active']))
                    $data['is_active'] = 0;
                if ($userId > 0) {
                    $user_id = $this->roleAccess->saveUser($data, $userId);
                } else {
                    $user_id = $this->roleAccess->saveUser($data);
                }
      //          \Log::info($user_id);
                if (!empty($roleId)) {
                    $this->roleAccess->setUserRole($roleId, $user_id);
                }

                //===============================Mail to User =======================================
                /* if ($userId == 0) {

                  $template = EmailTemplate::where('Code', 'ET1000')->get()->all();
                  $emailVariable = array('firstName' => $data['firstname'], 'lastName' => $data['lastname'], 'user_name' => $data['email'], 'password' => $password);
                  Mail::send(array('html' => 'emails.welcome_newuser'), $emailVariable, function($msg) use ($template, $data) {
                  $msg->from($template[0]->From, 'ebutor')->to($data['email'])->subject($template[0]->Subject);
                  });
                  } */
                
                if (is_numeric($user_id)) {
                    // \Log::info('in if');
                    return json_encode(array('status' => true, 'user_id' => $user_id, 'email_id' => $data['email_id']));
                } else {
                   //  \Log::info('in else');
                    return json_encode(array('status' => false, 'messge' => 'Please try again'));
                }
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }


    function newsave() {
        try{
        $data = Input::all();
        if ($data) {

            $user_id = $data['user_id'];
            $manf_id = $data['manf_id'];
            //print_r($user_id);exit;
            foreach ($manf_id as $keyValue) {
                $newSave = DB::Table('user_legalentity')
                        ->insertGetId(array(
                    'legal_entity_id' => $keyValue, 'user_id' => $user_id));
            }
            return Redirect::to('/users/index');
        }
        }
         catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    function uploadProfilePic() {
        // echo "<pre>";        print_r(Input::file('file')); die;
        $filename = Input::file('file')->getClientOriginalName();
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profile_picture/';
        $filename = date('YmdHis') . $filename;
        Input::file('file')->move($destinationPath, $filename);
        echo $filename;
        die;
        //echo $files['name']; die;
        //print_r($files); die;
    }
/*======= Edit the existing users=======*/
    
    function editUser($user_id) {
        try{
        $user = User::find($user_id);
        $rolesIds = $this->roleAccess->getUserRoldIdByUserId($user_id);
        $rolesIds = !empty($rolesIds[0]) ? $rolesIds[0] : 0;
        $roles = $this->roleAccess->getRole();        
        if (Session::get('legal_entity_id') == 0) {            
            $lookups = MasterLookup::where('mas_cat_id', 7)->get()->all();
            $legalEntitys = $this->custRepoObj->getAllCustomers();            
            $locationsall = array();
            $businessunits = array();
            $manufacturer = DB::table('user_legalentity')->where('user_id', $user_id)->select(DB::raw('group_concat(legal_entity_id) as legal_entity_id'))->first();
            $manfSelected = [];
            if (!empty($manufacturer)) {
                $manfSelected = explode(',', $manufacturer->legal_entity_id);
            }
            return View::make('users.edit')->with(array('user' => $user, 'roles' => $roles, 'lookups' => $lookups, 'legalEntitys' => $legalEntitys, 'rolesId' => $rolesIds, 'locationsall' => $locationsall, 'businessunits' => $businessunits, 'manufacturers' => $manfSelected));
            exit;
        } else {         
            $legalEntitys = $this->custRepoObj->getAllCustomers();
            $manufacturer = DB::table('user_legalentity')->where('user_id', $user_id)->select(DB::raw('group_concat(legal_entity_id) as legal_entity_id'))->first();
            $manfSelected = [];
            if (!empty($manufacturer)) {
                $manfSelected = explode(',', $manufacturer->legal_entity_id);
            }
            
            $lookups = MasterLookup::where('mas_cat_id', 7)->get()->all();
            $customers = $this->custRepoObj->getAllCustomers();
            $locationsall = Locations::where('manufacturer_id', Session::get('legal_entity_id'))->get()->all();
            $businessunits = BusinessUnit::where('legal_entity_id', Session::get('legal_entity_id'))->get()->all();
            return View::make('users.edit')->with(array('user' => $user, 'roles' => $roles, 'rolesId' => $rolesIds, 'locationsall' => $locationsall, 'businessunits' => $businessunits, 'legalEntitys' => $legalEntitys, 'customers' => $customers,'manufacturers' => $manfSelected, 'lookups' => $lookups));
            exit;
        }
        }
         catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function deleteUser($userId) {
        User::destroy($userId);
        return Redirect::to('/users/index');
    }

    public function setSessionData($mfgId) {
        try {
            //if($mfgId > 0)
            //{
            Session::put('manf_id', $mfgId);
            //}
        } catch (ErrorException $ex) {
            die($ex);
        }
    }

    public function Signup() {
        return View::make('signup');
    }

    public function register(){
        return view('LegalEntities::register');
    }

     public function bussinessSignup($legalEntityId,$userId) {
     try{
        $states = $this->custRepoObj->getStates(99);
        $legalEntityId = $this->roleAccess->decodeData($legalEntityId);
        $userId = $this->roleAccess->decodeData($userId);
        $entity_type = DB::table('master_lookup')->where('mas_cat_id',47)->where('master_lookup_name','!=',"Logistics Partners")->select('value as entity_type_id','master_lookup_name as entity_type_name')->get()->all();
        $profile_completed = DB::table('legal_entities')->where('legal_entity_id',$legalEntityId)->select('profile_completed')->first();
        $password = DB::table('users')->where('user_id',$userId)->select('password')->first();
        if($profile_completed->profile_completed != 0){
            return Redirect::to('/login');
        }
        elseif (empty($password->password)) {
            $active = '';
            return View::make('LegalEntities::signup')->with(array('legal_id' => $legalEntityId,'id' => $userId, 'states' => $states, 'entity_type' => $entity_type, 'active' => $active));
        }
        else
        {
            $active = 1;
            return View::make('LegalEntities::signup')->with(array('legal_id' => $legalEntityId,'id' => $userId, 'states' => $states, 'entity_type' => $entity_type, 'active' => $active ));
        }
    }
    catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
    }
  }

    public function checkEmail(){
        try{
            $data = Input::all();
            $email = isset($data['emailId']) ? $data['emailId'] : '';
            $result = false;
            $id = DB::table('users')->where('email_id',$email)->where('is_active',1)->pluck('user_id')->all();
            if(empty($id)) {
                $result = false;
            }
            else{
                $result = true;
            }
            return json_encode(array('valid' => $result));
        }catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}
