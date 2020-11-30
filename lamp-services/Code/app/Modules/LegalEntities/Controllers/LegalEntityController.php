<?php

namespace App\Modules\LegalEntities\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use Log;
use Request;
use Redirect;
use \App\Modules\LegalEntities\Models\Legalentity;
use \App\Modules\Users\Models\Users;
use DB;

class LegalEntityController extends BaseController {

    public function __construct() {   
        try
        {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });
            parent::Title('Legal Entity');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }
    
    public function indexAction()
    {
        try
        {
            return View::make('legalentity/index');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function addAction()
    {
        try
        {
            return View::make('legalentity/add');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }    
    
    public function saveAction()
    {
        try
        {
            $response = ['legal_entity_id' => 0,
                        'status' => false,
                        'message' => 'Unable to create, please contact admin'];
            $data = Input::all();
            $validator = Validator::make(
                            array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'mobile_number' => Input::get('phone_number'),
                        'email' => Input::get('email'),
                            ), array(
                        'firstname' => 'required',
                        'lastname' => 'required',
                        'mobile_number' => 'required',
                        'email' => 'required|email|unique:users,email_id',
                            )
            );
            $legalentity = new Legalentity();
//            $legalentity->checkDuplicate(Input::get('email'));
            if ($validator->fails()) {
                $messages = $validator->messages();
                $messageArr = json_decode($messages);
                $errorMsg = '';
                if (isset($messageArr->username[0]))
                    $errorMsg .= $messageArr->username[0];
                if (isset($messageArr->mobile_number[0]))
                    $errorMsg .= '<br>' . $messageArr->mobile_number[0];
                if (isset($messageArr->email[0]))
                    $errorMsg .= $messageArr->email[0];
                return Redirect::to('register')->with('errorMsg', $errorMsg);
            }else {
                $email = isset($data['email']) ? $data['email'] : '';
                $legalEntityId = $legalentity->checkUnique($email);
                if(!$legalEntityId)
                {
                    $legalEntityId = $legalentity->saveLegalentity($data);
                }
                $users = new Users();
                $data['legal_entity_id'] = $legalEntityId;
                $data['customer_type'] = 7001;
                $userId = $users->checkUnique($email);
                if(!$userId)
                {
                    $userId = $users->saveUsers($data);
                }               
                
                $response['legal_entity_id'] = $legalEntityId;
                $response['user_id'] = $userId;
                $response['status'] = true;
                $response['message'] = 'Sucessfully added entity';
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($response);
    }
    
    public function editAction()
    {
        try
        {
            return View::make('legalentity/edit');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function deleteAction()
    {
        try
        {
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function saveBusinessInfo()
    {
        try
        {
            $response = [];
            $data = Input::all();
//            Log::info($data);
            $validator = Validator::make(
                            array(
                        'legal_entity_id' => Input::get('legal_entity_id'),
                        'businessname' => Input::get('businessname'),
                        'business_type' => Input::get('business_type'),
                        'address1' => Input::get('address1'),
                        'city' => Input::get('city'),
                        'state_id' => Input::get('state_id'),
                        'pincode' => Input::get('pincode'),
                        'pan' => Input::get('pan'),
                        'tin' => Input::get('tin')
                            ), array(
                        'legal_entity_id' => 'required',
                        'businessname' => 'required',                        
                        'business_type' => 'required',                        
                        'address1' => 'required',                        
                        'city' => 'required',                        
                        'state_id' => 'required',                        
                        'pan' => 'required',
                        'tin' => 'required'                            )
            );
            $legalentity = new Legalentity();
//            $legalentity->checkDuplicate(Input::get('email'));
            if ($validator->fails()) {
                $messages = $validator->messages();
                $messageArr = json_decode($messages);
                $errorMsg = '';
                if (isset($messageArr->legal_entity_id[0]))
                    $errorMsg .= $messageArr->legal_entity_id[0];
                if (isset($messageArr->businessname[0]))
                    $errorMsg .= '<br>' . $messageArr->businessname[0];
                if (isset($messageArr->business_type[0]))
                    $errorMsg .= $messageArr->business_type[0];
                if (isset($messageArr->address1[0]))
                    $errorMsg .= '<br>' . $messageArr->address1[0];
                if (isset($messageArr->city[0]))
                    $errorMsg .= '<br>' . $messageArr->city[0];
                if (isset($messageArr->state_id[0]))
                    $errorMsg .= '<br>' . $messageArr->state_id[0];
                if (isset($messageArr->pan[0]))
                    $errorMsg .= '<br>' . $messageArr->pan[0];
                return Redirect::to('signup/'.Input::get('legal_entity_id'))->with('errorMsg', $errorMsg);
            }else {                
                $legalEntityData = $legalentity->saveBussinessData($data);
                $legalEntityId = 0;
                //Log::info('legalEntityData');
                //Log::info($legalEntityData);
                if(!empty($legalEntityData))
                {
                    $legalEntityDetails = json_decode($legalEntityData);
                    //Log::info('legalEntityDetails');
//                    Log::info($legalEntityDetails);
                    if(isset($legalEntityDetails->id))
                    {
                        $legalEntityId = $legalEntityDetails->id;
                    }
                }
                //Log::info('legalEntityId');
                //Log::info($legalEntityId);
                
                $users = new Users();
                $data['legal_entity_id'] = $legalEntityId;
                $users->activateUser($data);
                
                $response['legal_entity_id'] = $legalEntityId;
                $response['status'] = true;
                $response['message'] = 'Sucessfully added entity';
            }            
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function resendEmail() {
        try
        {
            $response['status'] = 0;
            $response['message'] = 'Unable to send email at the Moment.. Please try again!';
            $users = new Users();
            $emailId = Input::get('email');
            $userId = $users->getUserId($emailId);
            $legalEntityId = $users->getLegalEntityId($userId);
            $data['firstname'] = Input::get('firstname');
            $data['lastname'] = Input::get('lastname');
            $data['email'] = $emailId;
            $users->sendEmail($legalEntityId,$userId, $data);
            $response['status'] = 1;
            $response['message'] = 'Successfully sent email.';
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($response);
    }

    public function savePassword(){
        try {
            $data = Input::all();
            if($data['set_password']){
            $user = new Users;
            $result = $user->savePassword($data);
            return $result;
            }
            else{
                return json_encode([
                            'status' => false,
                            'message' => "Incorrect entry for password"]);
            }
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function checkUnique(){
        try{
        $data = Input::all();
        $email = isset($data['email']) ? $data['email'] : '';
        $result = false;
        $id = DB::table('users')->where('email_id',$email)->pluck('user_id')->all();
        if(empty($id)) {
            $result = true;
        }
        return json_encode(array('valid' => $result));
        }
     catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function viewProfile($legal_id){
        try {
        parent::Title('My profile');
          $legal_entity_id = Session::get('legal_entity_id');
          if($legal_id == md5($legal_entity_id)){
              $user_id = Session::get('userId');
              $legalentity = new Legalentity();
              $user_data = DB::table('users')->where('user_id',$user_id)->first();
              $user_role = DB::table('user_roles as ur')->leftJoin('legal_entity_roles as ler','ler.role_id','=','ur.role_id')->where('ur.user_id',$user_id)->where('le_type_id',1001)->select('ur.role_id')->first();
              $master_user = DB::table('user_legalentity')->where('user_id',$user_id)->select('legal_entity_id')->first();
              $master_user = isset($master_user->legal_entity_id) ? $master_user->legal_entity_id : 0;
              $user_role = isset($user_role->role_id) ? $user_role->role_id : 0;
              $business_info = $legalentity->getBusinessInfo($legal_entity_id);
              $states = DB::table('zone')->select('zone.zone_id as state_id','zone.name as state')->where('country_id',99)->get()->all();
              $business_types = $legalentity->getBusinessTypes();
              $bank_name = DB::table('bank_info')->select('bank_name')->groupBy('bank_name')->get()->all();
              $preference = DB::table('user_preferences')->where('user_id',$user_id)->select('sms_subscription','email_subscription')->first();
              /*echo "<pre>"; print_r($preference); die();*/
              $account_types = $legalentity->getAccountType();
              $bank_details  = $legalentity->getBankDetails($legal_entity_id);
              $currencyCodes = $legalentity->getCurrencyCode();
              $tin_file = DB::table('legal_entity_docs')->where('legal_entity_id',$legal_entity_id)->where('doc_type', 'tin_file' )->select('doc_name','doc_url','doc_type','doc_id')->first();
              $pan_file = DB::table('legal_entity_docs')->where('legal_entity_id',$legal_entity_id)->where('doc_type', 'pan_file' )->select('doc_name','doc_url','doc_type','doc_id')->first();
              return view('LegalEntities::profile')->with(['tin_file' => $tin_file,'bank_name' => $bank_name,'pan_file' => $pan_file,'user_data' => $user_data,'preference' => $preference ,'business_info' => $business_info,'states' => $states, 'business_types' => $business_types, 'account_types' => $account_types, 'currencyCodes' => $currencyCodes, 'bank_details' => $bank_details,'legal_entity_id'=>$legal_entity_id , 'user_role' => $user_role]);
        }
        else{
            return Redirect::to('/');
        }
    }
         catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getIfscs($bank_name){
        try {
            $ifscs = DB::table('bank_info')->where('bank_name',$bank_name)->select('ifsc')->get()->all();
            return $ifscs;
        }  catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getBankInfo($ifsc){
        try {
         $bank_info = DB::table('bank_info')->where('ifsc',$ifsc)->select('branch','micr','city')->first();
         return json_encode($bank_info);
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function updateDocs(){
        try {
            $data = Input::all();
            if(isset($data['pan_proof']) || isset($data['tin_proof'])){
             $legalentity = new Legalentity();
             $response = $legalentity->updateDocument($data);   
            }
            else{
                $response = json_encode([
                    'status' => '',
                    'message' => ''
                    ]);
            }
            return $response;
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function saveProfilePic(){
        try {
            $status = false;
            $message = "Unable to save profile picture";
            $data = Input::all();
            $legal_entity_id = Input::get('legal_entity_id');
            $user_id = Session::get('userId');
            $legalentity = new Legalentity();
            $path = $legalentity->saveProfilePic($data,$legal_entity_id,$user_id);
            if(!empty($path)){
                $status = true;
                $message = "Success";
            Session::put('userLogoPath',$path);
        }
        else{
            $message = "Incorrect file type!! Please try with JPEG or PNG images";
        }
            return json_encode([
                'path' => $path,
                'status' =>$status,
                'message' => $message
                ]);
        }
         catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        } 
        
    }

    public function checkPassword(){
        try {
            $data = Input::all();
            $result = false;
            $user_id = Session::get('userId');
            $password = md5($data['oldpassword']);          
            $id = DB::table('users')->where('password','=',$password)->where('user_id',$user_id)->pluck('user_id')->all();
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
            DB::table('users')->where('user_id',$userId)->update(['password' => md5($data['newpassword'])]);
            $status = true;
            $message = 'Password changed successfully';
            }
            else{
              $message = 'New Password and Confirm Password doesnt match';
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

    public function saveBasicInfo(){
        try {
            $data = Input::all();
            $status = false;
            $message = 'Unable to save Info at the moment. Please try again..';
            $legal_entity_id = Input::get('legal_entity_id');
            $userId = Session::get('userId');
            DB::table('users')->where('user_id',$userId)->update([
                'firstname' => $data['firstname'],
                'lastname' =>  $data['lastname'],
                'mobile_no' => $data['mobile_no']
                ]);
            $fullname = $data['firstname'] . ' ' . $data['lastname'];
            Session::put('fullname',$fullname);
            $email_sub = 0; $sms_sub = 0;
            if(isset($data['email_sub']) && $data['email_sub'] ==1){
                $email_sub = 1;
            }
            if(isset($data['sms_sub']) && $data['sms_sub'] == 1){
                $sms_sub = 1;
            }
            $user_preference = DB::table('user_preferences')->where('user_id',$userId)->get()->all();
            if(empty($user_preference)){
                $pref_id = DB::table('user_preferences')->insertGetId([
                    'user_id' => $userId,
                    'sms_subscription' => $sms_sub,
                    'email_subscription' => $email_sub
                    ]);
            }
            else{
              DB::table('user_preferences')
                    ->where('user_id',$userId)
                    ->update([
                    'sms_subscription' => $sms_sub,
                    'email_subscription' => $email_sub
                    ]);   
            }
            $data['email_sub'] = $email_sub;
            $data['sms_sub'] = $sms_sub;
            $status = true;
            $message = "Basic Info saved successfully..";
            return json_encode([
                            'status' => $status,
                            'message' => $message,
                            'data' => $data]);
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateBusinessInfo(){
        try {
            $data = Input::all();
            $data['legal_entity_id'] = Input::get('legal_entity_id');
            $legalentity = new Legalentity();
            $result = $legalentity->saveBussinessData($data);
            return $result;
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function saveBankInfo(){
        try {
            $data = Input::all();
            $data['legal_entity_id'] = Input::get('legal_entity_id');
            $legalentity = new Legalentity();
            $result = $legalentity->saveBankInfo($data);
            return $result;
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function adminViewProfile($legal_entity_id){
        try {
            $user_id = Session::get('userId');
          $legalentity = new Legalentity();
          $user_data = DB::table('users')->where('legal_entity_id',$legal_entity_id)->first();
          $business_info = $legalentity->getBusinessInfo($legal_entity_id);
          $states = DB::table('zone')->select('zone.zone_id as state_id','zone.name as state')->where('country_id',99)->get()->all();
          $business_types = $legalentity->getBusinessTypes();
          $account_types = $legalentity->getAccountType();
          $bank_details  = $legalentity->getBankDetails($legal_entity_id);
          $currencyCodes = $legalentity->getCurrencyCode();
          return view('LegalEntities::adminViewProfile')->with(['user_data' => $user_data, 'business_info' => $business_info,'states' => $states, 'business_types' => $business_types, 'account_types' => $account_types, 'currencyCodes' => $currencyCodes, 'bank_details' => $bank_details ]);
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}
