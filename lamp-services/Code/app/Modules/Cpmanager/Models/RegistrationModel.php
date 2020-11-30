<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use URL;
use Cache;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\CustomerRepo;
use App\Modules\Roles\Models\Role;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Cpmanager\Models\HomeModel;
use App\Modules\Cpmanager\Models\EcashModel;
use Log;
use App\Modules\Retailer\Models\Retailer;
use App\Modules\Cpmanager\Models\accountModel;
date_default_timezone_set('Asia/Kolkata');
use App\Modules\Inventory\Models\Inventory;


class RegistrationModel extends Model
{
     public $timestamps = false;


    public function __construct() {

         $this->_role = new RoleRepo(); 
         $this->team = new Role(); 
         $this->_cust = new CustomerRepo();
         $this->homepage = new HomeModel();
         $this->_ecash = new EcashModel(); 
         $this->rolem = new Role();
         $this->_account = new accountModel();  
      }
     
 /*
    * Function name: registrationss
    * Description: Used to store first screen details
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason 12:
    */
    public function registration($telephone,$buyer_type,$sales_token,$app_flag=0)
    {
     
     try
     {
       
       $master = DB::table('master_lookup as ml')
                       ->where("ml.value", "=", 78002)->get()->all();

       $desc= $master[0]->description;
    
       $userchk = DB::table('users')
                      ->where("mobile_no", "=", $telephone)
                      ->where("is_active", 1)->get()->all();


              if(empty($buyer_type))
              {
               
               $buyer_type_id=3001;

              }else{

                 $buyer_type_id=$buyer_type;
              }


          if($userchk)
          {

          $user_chkdet=json_decode(json_encode($userchk[0]),true);

          $ff_check=$this->_role->checkPermissionByFeatureCode('EFF001',$user_chkdet['user_id']);          
          $srm_check=$this->_role->checkPermissionByFeatureCode('SRM001',$user_chkdet['user_id']);
          $customer_chk=$this->_role->checkPermissionByFeatureCode('MCU001',$user_chkdet['user_id']);
          $lp_feature=$this->getFeatures($user_chkdet['user_id'],1);
          $mobile_feature=$this->getFeatures($user_chkdet['user_id'],2);

          }else{
           
           $ff_check=0;
           $srm_check=0;
           $customer_chk=0;
           $lp_feature=[];
           $mobile_feature=[];

          }    

          if($ff_check==1 || $srm_check==1 || !empty($lp_feature) ||  (!empty($mobile_feature) && $customer_chk==0))
          {
            
           
            if(!empty($sales_token))
            {
             
                $result=array();
                $result['message']="Already Registered FieldForce";
                $result['status']=0;
                return $result;

            }else{

          $otpflag=1;

          $result= $this->generateOtp($user_chkdet['user_id'],$telephone,$buyer_type_id,$otpflag,$app_flag);
          return $result;
               }
                }else{


      $result_users = DB::table('users as user')
                        ->select(DB::raw('user.user_id,leg.legal_entity_id,leg.is_approved,user.is_active'))
                        ->leftjoin('legal_entities as leg','leg.legal_entity_id','=','user.legal_entity_id')
                         ->where('user.mobile_no', '=', $telephone)
                         ->where('user.is_active', '=', 1)
                         ->whereIn('leg.legal_entity_type_id',function($query) use ($desc){
                           $query->select('value')
                                  ->from('master_lookup as ml')
                                  ->where('ml.mas_cat_id','=',$desc)
                                  ->where("ml.is_active", "=", 1);
                         })
                         ->get()->all();


      $result_user_temp = DB::table('user_temp')
                        ->select(DB::raw('user_temp.*'))
                         ->where('mobile_no', '=', $telephone)
                        ->get()->all();

        $users_num_rows=sizeof($result_users);


        $users_temp_num_rows=sizeof($result_user_temp);

        
             if(!empty($result_users)){

                           $customer_id= $result_users[0]->user_id;

                                }else{

                       $customer_id='';
                                  
                       }
                 
        if($users_num_rows>0)
        {

         $is_active=$result_users[0]->is_active;
        
          }else{

            $is_active=0;
          }

        if($users_num_rows==0 && $users_temp_num_rows==0)
        {

          $otpflag=0;

          $result= $this->generateOtp($customer_id,$telephone,$buyer_type_id,$otpflag,$app_flag);

          return $result;
        }elseif($users_num_rows > 0 &&  $is_active==1 && empty($sales_token)){
       
           $otpflag=1;

          $result= $this->generateOtp($customer_id,$telephone,$buyer_type_id,$otpflag,$app_flag);
          return $result;

        }elseif(($users_num_rows > 0 && !empty($sales_token))||(($users_num_rows > 0) && ($is_active==0)))
        {
     
    $result=array();
    if($is_active==1)
    {         
   
    $result['message']="Already Registered";
    $result['status']=0;
  }else{
      
    $result['message']="We are sorry your shop is not being serviced at the moment.";
    $result['status']=0;

  }
          
    return $result;

        }
        else
        { 
          $otpflag=2;

          $result= $this->generateOtp($customer_id,$telephone,$buyer_type_id,$otpflag,$app_flag);

          return $result;

        }

       }
   }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }   
                      

}

    /*
    * Function name: generateOtp
    * Description: Used to generate OTP
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason:
    */

    public function generateOtp($customer_id,$phonenumber,$buyer_type_id,$otpflag,$app_flag=0)
    {
      try
      {
      

        $randnumber= rand(100000,999999);
        
        $userid =  $customer_id;
        
        $ch = curl_init();
        $mno = $phonenumber;
        $app_unique_key = env("CP_APP_UNIQUE_KEY");
        if($app_flag == 1){
            $app_unique_key = env("LP_APP_UNIQUE_KEY");
        }
        $message =  "<#> Your OTP for Ebutor is " .$randnumber."\n - ".$app_unique_key;
        // $message =  "Your OTP for Ebutor is  " .$randnumber;
        if(preg_match( '/^[A-Z0-9]{10}$/', $mno) && !empty($message))
         {
            $ch = curl_init();
            $user=Config::get('dmapi.DB_USER');
            $receipientno= $mno; 
            $senderID=Config::get('dmapi.DB_SENDER_ID'); 
           
            $msgtxt= $message;

            curl_setopt($ch,CURLOPT_URL, Config::get('dmapi.DB_URL') );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$msgtxt");
        //  print_r($ch);exit;
            $buffer = curl_exec($ch);

            if(empty ($buffer))
            { 
                  
            //  echo " buffer is empty "; 

                  $res['message']="Not Valid";
                  $res['status']=0;
             }
            else
            { 


             $res=array();
              if($otpflag==0)
              {

                  DB::table('user_temp')->insert(['mobile_no' => $mno,
                                             'otp' =>  $randnumber, 
                                             'legal_entity_type_id' => $buyer_type_id,
                      'created_at' => date("Y-m-d H:i:s")]);

                    $res['message']="Please Confirm  OTP ";
                    $res['status']=1;
                    return $res;

              }elseif($otpflag==1){

                DB::Table('users')  
                          ->where('user_id', $userid)
                          ->where('is_active', 1)
                         ->update(array('otp' => $randnumber,'updated_at' => date("Y-m-d H:i:s")));

                $res['message']="Please Confirm  OTP ";
                $res['status']=1;
  
                return $res;

              }else{

                      DB::Table('user_temp')  
                          ->where('mobile_no', $mno)
                         ->update(array('otp' => $randnumber,'updated_at' => date("Y-m-d H:i:s")));

                $res['message']="Please Confirm  OTP ";
                $res['status']=1;
  
                return $res;

              }

        }
            curl_close($ch);
        } 
     }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  

}


/*
* Function name: resendOtp
* Description: Used to resend OTP
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 June 2016
* Modified Date & Reason:
*/



public function resendOtp($telephone,$otpflag,$customer_token,$custflag,$user_id,$app_flag=0)
{
  try
  {
  
        $randnumber= rand(100000,999999);

        $ch = curl_init();
        $mno = $telephone;
        $app_unique_key = env("CP_APP_UNIQUE_KEY");
        if($app_flag == 1){
            $app_unique_key = env("LP_APP_UNIQUE_KEY");
        }
        $message =  "<#> Your OTP for Ebutor is " .$randnumber."\n - ".$app_unique_key;
        if(preg_match( '/^[A-Z0-9]{10}$/', $mno) && !empty($message)) {
            $ch = curl_init();
            $user=Config::get('dmapi.DB_USER');
            $receipientno= $mno; 
           // $senderID='FCTAIL'; 
            $senderID=Config::get('dmapi.DB_SENDER_ID'); 
            $msgtxt= $message; 
            curl_setopt($ch,CURLOPT_URL,Config::get('dmapi.DB_URL'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$msgtxt");
            $buffer = curl_exec($ch);

            if(empty ($buffer))
            {

                $res['message']="Not Valid";
                $res['status']=0;

             }
            else
            { 

     if($otpflag==0)
              {
    DB::table('user_temp')->insert(['mobile_no' => $mno,
                                'otp' =>  $randnumber, 
                                'legal_entity_type_id' => $buyer_type_id,
                                'created_at' => date("Y-m-d H:i:s") ]);

     $res['message']="Please Confirm  OTP ";
     $res['status']=1;

  
     return $res;

              }elseif($otpflag==1){
 //&& !empty($customer_token) && $custflag==2

                 DB::Table('users')  
                          ->where('mobile_no', $mno)
                          ->where('user_id', $user_id)
                          ->where('is_active', 1)
                         ->update(array('otp' => $randnumber,'updated_at' => date("Y-m-d H:i:s")));

                $res['message']="Please Confirm  OTP ";
                $res['status']=1;
  
                return $res;

              }else{
         DB::Table('user_temp')  
                          ->where('mobile_no', $mno)
                         ->update(array('otp' => $randnumber,'updated_at' => date("Y-m-d H:i:s")));

                $res['message']="Please Confirm  OTP ";
                $res['status']=1;
  
                return $res;
              }

        }
            curl_close($ch);
        } 

//}
//}
        else {
          $res=array();
          $res['message']="Not Valid Information";
          $res['status']=0;
 // $final=json_encode($res);
          return $res;
        }

      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  
}
     
     /*
    * Function Name: address()
    * Description: Add address function is used to add the 
      address to regirstration process third step.
    * fillable variable is difined to store the data fields into the database
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason:
  */
     public function address($business_legal_name,
               $segment_id,$tin_number,
              $address1,
               $address2,$locality,$landmark,$city,
               $pincode,$firstname,
               $email,$mobile_no, $filepath1, $filepath2,$latitude
     ,$longitude,$download_token,$ip_address,$device_id,$pref_value,$pref_value1,
     $bstart_time,$bend_time,$state_id,$noof_shutters,$volume_class,$license_type,$sales_token,$contact_no1,
     $contact_no2,
     $contact_name1,$contact_name2,$area,$master_manf,$smartphone,$network,$lastname,$beat,$customer_type,$gstin,
     $arn_number,$is_icecream=0,$sms_notification=0,$is_milk=0,$is_fridge=0,$is_vegetables=0,$is_visicooler=0,$dist_not_serv='',$facilities=0,$is_deepfreezer=0,$is_swipe=0,$aadhar_id='',$credit_limit=0,$mfc=''){
     
      DB::beginTransaction(); 
        try {
           
          $query =DB::table('user_temp')
                ->select(DB::raw("mobile_no,status,otp,legal_entity_type_id"))
                ->where("mobile_no",'=', $mobile_no)
                ->get()->all();
        $query=reset($query);
        //$legal_entity_type_id=(isset($query->legal_entity_type_id) && $query->legal_entity_type_id!='')? $query->legal_entity_type_id:0;
        $otp=(isset($query->otp) && $query->otp!='')? $query->otp:0;
            $res=array();

            if(!empty($sales_token))  
            {
               $password_token =DB::table('users')
                ->select("user_id","legal_entity_id")
                ->where("password_token",'=', $sales_token)
                ->get()->all();
                if(!empty($password_token))
                {
                 $ff_uid= $password_token[0]->user_id;
                 $ff_le_id= isset($password_token[0]->legal_entity_id)?$password_token[0]->legal_entity_id:null;
                }else{
                  $ff_uid='';
                  $ff_le_id=null;
                }
                  }else{
                   $ff_uid='';
                   $ff_le_id=null;
                  } 
           /*$pinCheck = $this->serviceablePincode($pincode);

              if(isset($pinCheck[0]->count) && empty($pinCheck[0]->count))
              {
                $is_approved = 0;
                $status = 0;
                return Array('status' => "failed", 'message' => "Please enter serviceable pincode", 'data' =>"");

              }else{
                $is_approved =1;
                $status =1;
              }*/
            $is_approved =1;
            $status =1;
            $stateName=DB::table('zone')
                      ->select("name")
                      ->where("zone_id", "=", $state_id)
                      ->get()->all();
            $pincode_state_chk = DB::table('cities_pincodes')
                                ->where('pincode',$pincode)
                                ->where('state',$stateName[0]->name)
                                ->count();
            if($pincode_state_chk<=0){
              $is_approved = 0;
              $status = 0;
              return Array('status' => "failed", 'message' => "Please select correct state", 'data' =>"");
            }

         $legal_entitycompany_type =DB::table('master_lookup')
             ->select(DB::raw("description"))
                ->where("value",'=',78001)
               ->get()->all();       
        $le_code = $this->_cust->getRefCode('CU',$state_id); 
        //$parent_le_id = $this->getParentLeId($pincode);
        if($ff_uid && !empty($ff_uid) )
        $parent_le_id=$this->getParentLeIdFromFFId($ff_uid);
        else if($beat && !empty($beat))
        $parent_le_id=$this->getLeFromBeat($beat);
        else
        $parent_le_id=0;


          
         $last_insert_legal_id= DB::table('legal_entities')->insertGetId(['business_legal_name' => $business_legal_name,
                                 'address1' => $address1,
                                 'legal_entity_type_id' => $customer_type,
                                 'address2' => $address2,
                                'locality' =>$locality,
                                'landmark' => $landmark,
                                'tin_number' =>$tin_number,
                                'country'=>99,
                                'state_id'=>$state_id,
                                'is_approved' => $is_approved,
                                'city' => $city,
                                'pincode' => $pincode,
                                'business_type_id' => $segment_id,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'le_code' => $le_code,
                                'parent_id' => $legal_entitycompany_type[0]->description,
                                'created_by' => $ff_uid,
                                'gstin'=>$gstin,
                                'arn_number'=>$arn_number,
                                'created_at' => date('Y-m-d H:i:s'),
                                'parent_le_id' => $parent_le_id,
                              ]);
         //   $last_insert_legal_id = DB::getPdo()->lastInsertId();

            if(!empty($email))
            {
             
             $email_id=$email;

            }else{
                  
             $email_id=$mobile_no.'@nomail.com';

            }

             $customer_token = md5(uniqid(mt_rand(), true));
            
            
             //Insert data into user table
             $last_insert_user_id= DB::table('users')->insertGetId(['firstname' => $firstname,
                                   'lastname' => $lastname,
                                  'email_id' => $email_id, 
                                  'mobile_no' => $mobile_no,
                                  'profile_picture' => $filepath2,
                                  'otp' => $otp,
                                  'password_token' => $customer_token,
                                  'legal_entity_id' =>   $last_insert_legal_id,
                                  'is_active' => $status,
                                  'is_parent' => 1,
                                  'aadhar_id' => $aadhar_id,
                                  'created_by' => $ff_uid,
                                  'created_at' => date('Y-m-d H:i:s'),
                                  'aadhar_id'  => $aadhar_id
                                      ]);
          //  $last_insert_user_id = DB::getPdo()->lastInsertId();

            if(empty($ff_uid))
            {
              $ff_uid=$last_insert_user_id;

            }

             
             if(empty($sales_token))  
            {

             DB::table('users as us')          
              ->where("us.user_id","=", $last_insert_user_id)   
               ->update(array('created_by' => $ff_uid,
                             'updated_at' => date("Y-m-d H:i:s")  ));

               DB::table('legal_entities as le')          
              ->where("le.legal_entity_id","=", $last_insert_legal_id)   
               ->update(array('created_by' => $ff_uid,
                             'updated_at' => date("Y-m-d H:i:s")  ));

             }

            $last_insert_user1_id='';

          if(!empty($contact_no1))
          {
           $chk_user1=$this->checkUser($contact_no1);
           if($chk_user1>=1)
           {
         
            DB::table('legal_entities')->where('legal_entity_id', $last_insert_legal_id )->delete(); 
            DB::table('users')->where('user_id', $last_insert_user_id )->delete();
            DB::table('legalentity_warehouses')->where('legal_entity_id', $last_insert_legal_id )->delete(); 
           
                $res['message']="ContactNumber already exists".$contact_no1;
                $res['status']="failed";
                $res['data']="";

                return $res;
           

                 }else{ 
             //Insert data into user table
              $last_insert_user1_id=DB::table('users')->insertGetId(['firstname' => $contact_name1,
                                      'email_id' => $contact_no1.'@nomail.com', 
                                      'mobile_no' => $contact_no1,
                                      'profile_picture' => $filepath2,
                                      'otp' => $otp,
                                     'legal_entity_id' =>   $last_insert_legal_id,
                                      'is_active' => $status,
                                      'created_by' => $ff_uid,
                                      'created_at' => date('Y-m-d H:i:s')
                                          ]);
            // $last_insert_user1_id=DB::getPdo()->lastInsertId();
           }
               }
          if(!empty($contact_no2))
           {
           $chk_user2=$this->checkUser($contact_no2);
           if($chk_user2>=1)
           {
           DB::table('legal_entities')->where('legal_entity_id', $last_insert_legal_id )->delete(); 
           DB::table('users')->where('user_id', $last_insert_user_id )->delete();
           DB::table('legalentity_warehouses')->where('legal_entity_id', $last_insert_legal_id )->delete(); 
           if(!empty($last_insert_user1_id))
           {
           DB::table('users')->where('user_id',$last_insert_user1_id)->delete();
           }
          

                $res['message']="ContactNumber already exists".$contact_no2;
                $res['status']="failed";
                $res['data']="";

                return $res;
           

                 }else
                      {
             //Insert data into user table
            $last_insert_user2_id= DB::table('users')->insertGetId(['firstname' => $contact_name2,
                               
                                    'email_id' => $contact_no2.'@nomail.com', 
                                    'mobile_no' => $contact_no2,
                                    'profile_picture' => $filepath2,
                                    'otp' => $otp,
                                  //  'password_token' => $customer_token,
                                    'legal_entity_id' =>   $last_insert_legal_id,
                                    'is_active' => $status,
                                    'created_by' => $ff_uid,
                                    'created_at' => date('Y-m-d H:i:s')
                                        ]);
           }
           }

                $area_chk = DB::table('cities_pincodes as cp')
                       ->select("cp.city_id")
                       ->where("cp.pincode", "=", $pincode)
                       ->where("cp.officename","LIKE",'%'.$area.'%')
                       ->where("cp.city","LIKE",'%'.$city.'%')
                       ->get()->all();

          //$le_wh_id =$this->getWarehouseid($pincode);
          if($ff_uid && !empty($ff_uid) )
          $le_wh_id=$this->getWarehouseFromLeId($parent_le_id);
          else if($beat && !empty($beat))
          $le_wh_id=$this->getWhFromBeat($beat);


          if(empty($le_wh_id))
          {
           
           $le_wh_id='';
          
          }      
           
           $hub=$this->getHub($beat);

             if(empty($hub))
             {
               $hub='';
             } 
            if(!empty($area_chk))
            {

                  //Insert data into customers
            $area_chk_id= DB::table('customers')->insertGetId(['le_id' => $last_insert_legal_id,
                                   'volume_class' => $volume_class, 
                                   'No_of_shutters' => $noof_shutters,
                                   'area_id'=>$area_chk[0]->city_id,
                                   'master_manf' =>$master_manf,
                                   'smartphone' =>$smartphone,
                                   'network' =>$network,
                                   'created_by' =>$ff_uid,
                                   'beat_id'=> $beat,
                                   'created_at' => date("Y-m-d H:i:s"),
                                   'is_icecream' => $is_icecream,
                                   //'sms_notification' => $sms_notification, 
                                   'is_milk' => $is_milk, 
                                   'is_fridge' => $is_fridge, 
                                   'is_vegetables' => $is_vegetables, 
                                   'is_visicooler' => $is_visicooler, 
                                   'dist_not_serv' => $dist_not_serv,
                                   'facilities' => $facilities,
                                   'is_deepfreezer'=>$is_deepfreezer,
                                   'is_swipe' => $is_swipe
                                    ]);
                   
                 }else{

                  $state_name=DB::table('zone')
                                ->select("name")
                                ->where("zone_id", "=", $state_id)
                                ->get()->all();

                  $last_insert_city_id= DB::table('cities_pincodes')->insertGetId(['country_id' =>99,
                                        'pincode' => $pincode, 
                                        'city' => $city,
                                        'state' => $state_name[0]->name,
                                        'officename'=> $area
                                            ]);
             
           // $last_insert_city_id = DB::getPdo()->lastInsertId();
             //Insert data into customers
            $area_chk_id= DB::table('customers')->insertGetId(['le_id' => $last_insert_legal_id,
                                    'volume_class' => $volume_class, 
                                    'No_of_shutters' => $noof_shutters,
                                    'area_id'=>$last_insert_city_id,
                                    'master_manf' =>$master_manf,
                                     'smartphone' =>$smartphone,
                                      'network' =>$network,
                                     'created_by' =>$ff_uid,
                                      'beat_id'=> $beat,
                                      'created_at' => date("Y-m-d H:i:s"),
                                   'is_icecream' => $is_icecream,
                                  // 'sms_notification' => $sms_notification, 
                                   'is_milk' => $is_milk, 
                                   'is_fridge' => $is_fridge, 
                                   'is_vegetables' => $is_vegetables, 
                                   'is_visicooler' => $is_visicooler, 
                                   'dist_not_serv' => $dist_not_serv,
                                   'facilities' => $facilities,
                                   'is_deepfreezer'=>$is_deepfreezer,
                                   'is_swipe' => $is_swipe
                                        ]);

                 }
             
               $master = DB::table('master_lookup as ml')
                       ->where("ml.value", "=", 78002)->get()->all();

               $desc= $master[0]->description;        
             
             $users = DB::table('users as u')
                ->select(DB::raw("u.user_id,u.mobile_no,u.is_active,u.otp,le.legal_entity_id,u.profile_picture"))
                ->leftJoin('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
                ->where("mobile_no",'=', $mobile_no)
                ->where("u.is_active",1)
              ->whereIn('le.legal_entity_type_id',function($query) use ($desc){
                           $query->select('value')
                                  ->from('master_lookup as ml')
                                  ->where('ml.mas_cat_id','=',$desc)
                                  ->where("ml.is_active", "=", 1);
                         })
                ->get()->all();


              $legal = DB::table('legal_entities')
                ->select(DB::raw("legal_entity_id,legal_entity_type_id"))
                ->where("legal_entity_id",'=', $last_insert_legal_id)
                ->get()->all();


                $license = DB::table('master_lookup')
                ->select(DB::raw("master_lookup_name"))
                ->where("value",'=', $license_type)
                ->get()->all();
             
             if(!empty($filepath1))
             {
            
             //Insert data into lelgal entity doc table
           $last_insert_doc_id= DB::table('legal_entity_docs')->insertGetId(['legal_entity_id' => $last_insert_legal_id,
                                
                                'doc_url' => $filepath1,
                                'doc_type' =>  $license[0]->master_lookup_name,
                              
                                'created_at' => date("Y-m-d H:i:s")]);
            }
         
         if(!empty($pref_value) || !empty($bstart_time) || !empty($bend_time))
         {
              //Insert data into user_prefences 
           $last_insert_userpref_id= DB::table('user_preferences')->insertGetId(['user_id' =>$users[0]->user_id,   
                                        'preference_name' => "expected delivery", 
                                        'preference_value' => $pref_value,
                                        'preference_value1' => $pref_value1,
                                        'business_start_time' =>$bstart_time, 
                                        'business_end_time' =>$bend_time,
                                        'sms_subscription' => $sms_notification, 
                                        'create_at' => date("Y-m-d H:i:s")]);
          }
        
           //  $this->createDownloadtoken($ip_address,$device_id,$users[0]->user_id,$download_token);

         if(!empty($sales_token))
         {
         $last_insert_fflog_id=  DB::table('ff_call_logs')->insertGetId([
                                'ff_id' => $ff_uid,
                                'user_id'=>$users[0]->user_id,
                                'legal_entity_id'=>$legal[0]->legal_entity_id,
                                'activity'=> 107000,
                                 'check_in'=>date("Y-m-d H:i:s"),
                                'check_in_lat'=>$latitude,
                                'check_in_long'=>$longitude,
                                'created_at'=> date("Y-m-d H:i:s")
                              ]);
          }
        
          $role_id=$this->getRoleId();
//Log::info('cust  roles id='.$role_id);
              if(!empty($role_id))
              {
               //   Log::info('insert cust roles id='.$role_id.'==userid=='.$users[0]->user_id);
              $last_insert_role_id=DB::table('user_roles')->insertGetId([
                                    'role_id' => $role_id,
                                    'user_id'=>$users[0]->user_id,
                                    'created_by' =>$ff_uid,
                                    'created_at'=> date("Y-m-d H:i:s")
                                  ]);
              }
            $mobile_feature=$this->getFeatures($users[0]->user_id,2);  
            if(empty($mobile_feature))
             {
           $mobile_feature=[];
             } 
         
            DB::table('user_ecash_creditlimit')->insertGetId([
                                        'user_id' => $users[0]->user_id,
                                        'creditlimit'=>$credit_limit
                                      ]);
          // }

           if($mfc!='') {

              DB::table('mfc_customer_mapping')->insertGetId([
                                        'mfc_id' => $mfc,
                                        'cust_le_id'=>$legal[0]->legal_entity_id,
                                        'credit_limit'=>$credit_limit,
                                        'is_active'=>1
                                      ]);

           }


           if(!empty($last_insert_legal_id)  && !empty($last_insert_user_id) && !empty($area_chk_id) && !empty($last_insert_role_id) && !empty($last_insert_userpref_id))
           {  

           if($status==1)
           {
               $res['message']="Registered Successfully";
            }else{

           $res['message']="We are sorry your shop is not being serviced at the moment.";

           }

            $res['business_legal_name']=$business_legal_name;
            $res['firstname']=$firstname;
            $res['lastname']=$lastname;
            $res['legal_entity_id']=$legal[0]->legal_entity_id;
            $res['customer_group_id']=$legal[0]->legal_entity_type_id;
            $res['customer_token']=$customer_token;
            $res['customer_id']=$users[0]->user_id;
            $res['image']=$users[0]->profile_picture;
            $res['segment_id']=$segment_id;
            $res['pincode']=$pincode;
            $res['is_ff']=0;
            $res['is_srm']=0;
            $res['is_dashboard']=0;
            $res['le_wh_id']=$le_wh_id;
            $res['hub']=$hub;
            $res['is_active']=$users[0]->is_active;
            $res['status']=1;
            $res['has_child']=0;
            $res['lp_feature']=[];
            $res['mobile_feature']=$mobile_feature;
            $res['beat_id']=$beat;
            $res['latitude']=$latitude;
            $res['longitude']=$longitude;
            $res['parent_le_id']=$parent_le_id;
            
           DB::table('user_temp')->where('mobile_no', $mobile_no)->delete();
               DB::commit();
                     $retailer = new Retailer($this->_role);
                     $retailer->updateFlatTable($legal[0]->legal_entity_id);
               return $res;
              }else{

              DB::rollback();
              
              return Array('status' => "failed", 'message' => "Please submit again", 'data' =>"");
  

            }

     }catch (Exception $e)
      {
          DB::rollback();
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }                   
            
     }
/*
    * Function Name: Generate APPID-checkDeviceId
    * Description: To validate the device Id details are available are not 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason:
    */


  public function checkDeviceId($device_id)
  {

    $query =DB::table('device_details as ded')
                ->select(DB::raw("ded.device_id as device_id,ded.app_id as appId"))
                ->where("ded.device_id",'=', $device_id)
                ->get()->all();

                return  $query;
    }


    /*
    * Function Name: Generate APPID-createDownloadtoken
    * Description: We insert required details with appid when device details are not available  
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason:
    */

    public function createDownloadtoken($ip_address,$device_id,$user_id,$download_token)
  {
       

        $result= DB::table('device_details')->insert([
              'user_id' => $user_id,
              'device_id'=>$device_id,
              'app_id'=>$download_token,
              'ip_address'=>$ip_address,
              'created_at'=> date("Y-m-d H:i:s"),
              'last_used_date' => date("Y-m-d H:i:s")
                   
            ]);



    }
    
    
    /*
    * Function Name: getStatus
    * Description: Check wether mobile number is confirmed or not 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason:
    */
    
    public function getStatus($phonenumber)
    {
      try{
   
        $master = DB::table('master_lookup as ml')
                       ->where("ml.value", "=", 78002)->get()->all();

        $desc= $master[0]->description;

        $chk_user = DB::table('users')
                       ->where("mobile_no", '=', $phonenumber)
                       ->where("is_active",1)->get()->all();
    if( $chk_user)
    {                   

        $user_chkdet=json_decode(json_encode($chk_user[0]),true);                   

         $ff_check=$this->_role->checkPermissionByFeatureCode('EFF001',$user_chkdet['user_id']);
         $srm_check=$this->_role->checkPermissionByFeatureCode('SRM001',$user_chkdet['user_id']);
         $customer_chk=$this->_role->checkPermissionByFeatureCode('MCU001',$user_chkdet['user_id']);
        // $feature=$this->getFeatureByUserId($user_chkdet['user_id']); 
         $lp_feature=$this->getFeatures($user_chkdet['user_id'],1);
         $mobile_feature=$this->getFeatures($user_chkdet['user_id'],2);    

     }else{
         
          $ff_check=0;
          $srm_check=0;
          $customer_chk=0;
          $lp_feature='';
          $mobile_feature='';

   }
 
  if($ff_check==1 || $srm_check==1 || !empty($lp_feature) ||  (!empty($mobile_feature) && $customer_chk==0))
   {


              $query = DB::table('users')
                       ->select(DB::raw('users.legal_entity_id'))
                       ->where("mobile_no", '=', $phonenumber)
                       ->where("is_active",1)->get()->all();

                }else{


                    $query = DB::table('users as user')
                        ->select(DB::raw('user.legal_entity_id'))
                        ->leftjoin('legal_entities as leg','leg.legal_entity_id','=','user.legal_entity_id')
                         ->where
                         ('user.mobile_no', '=', $phonenumber)
                         ->whereIn('leg.legal_entity_type_id',function($query) use ($desc){
                            $query->select('value')
                                  ->from('master_lookup as ml')
                                  ->where('ml.mas_cat_id','=',$desc)
                                  ->where("ml.is_active", "=", 1);
                         })
                         ->get()->all();

               
                }

    


                return $query;

      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }          
     }
             

      /*
    * Function Name: otpConfirm
    * Description: To confirm OTP
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason:
    */
    public function otpConfirm($phonenumber,$otp,$legal_entity_id,$device_id,$ip_address,$reg_id,$platform_id,$module_id)
    {
       try{ 

        if(!empty($legal_entity_id))
        {
          
          $master = DB::table('master_lookup as ml')
                       ->where("ml.value", "=", 78002)->get()->all();

        $desc= $master[0]->description;


          // The reason to Join Legal Entity Id, is to get their parent legal entity id
         $userchk = DB::table('users as u')
              ->leftJoin('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
              ->select("u.*","le.parent_le_id")
              ->where("u.mobile_no", "=", $phonenumber)
              ->useWritePdo()
              ->where("u.is_active",1)->get()->all();


         if($userchk)
         {        
          $user_chkdet=json_decode(json_encode($userchk[0]),true);

          $ff_check=$this->_role->checkPermissionByFeatureCode('EFF001',$user_chkdet['user_id']);          
          $srm_check=$this->_role->checkPermissionByFeatureCode('SRM001',$user_chkdet['user_id']);
          $customer_chk=$this->_role->checkPermissionByFeatureCode('MCU001',$user_chkdet['user_id']);
          $lp_feature=$this->getFeatures($user_chkdet['user_id'],1);
          $mobile_feature=$this->getFeatures($user_chkdet['user_id'],2);  

          $mfc_data = DB::table('mfc_customer_mapping')
                    ->select(array('mfc_id'))
                    ->where(array('cust_le_id'=>$user_chkdet['legal_entity_id'],'is_active'=>1))
                    ->first();
          
          if(!empty($mfc_data) && isset($mfc_data->mfc_id) && $mfc_data->mfc_id!='') {
            $mfc = $mfc_data->mfc_id;
          } else {
            $mfc = 0;
          }
          if(!empty($device_id)){
            $this->homepage->InsertDeviceDetails($user_chkdet['user_id'],$device_id,$ip_address,$platform_id,$reg_id);
          }
          $creditlimit = DB::table('user_ecash_creditlimit')
                        ->select(array('creditlimit'))
                        ->where(array('le_id' => $user_chkdet['legal_entity_id']))
                        ->first();
          if(!empty($creditlimit) && isset($creditlimit->creditlimit) && $creditlimit->creditlimit!='') {
            $creditlimit =$creditlimit->creditlimit;
          }else{
            $creditlimit =0;
          }
         }else{
          $ff_check=0;
          $srm_check=0;
          $customer_chk=0;
          $lp_feature=[];
          $mobile_feature=[];
          $mfc = 0;
          $creditlimit =0;
         }   

          
          if($ff_check==1 || $srm_check==1 || !empty($lp_feature) || (!empty($mobile_feature) && $customer_chk==0))
          {
            if(!empty($ff_check))
             {

              $is_ff=1;
             }else{
              $is_ff=0;
             } 
              if(!empty($srm_check))
             {
              $is_srm=1;
             }else{
              $is_srm=0;
             } 
           $customer_token = md5(uniqid(mt_rand(), true));
          $dashboard=$this->_role->checkPermissionByFeatureCode('FFD001',$user_chkdet['user_id']);
          $new_dashboard=$this->_role->checkPermissionByFeatureCode('MFD001',$user_chkdet['user_id']);
          if($dashboard==1 || $new_dashboard==1)
          {
            $is_dashboard=1;
                 $team=$this->team->getTeamByUser($user_chkdet['user_id']);
                 if(($key = array_search($user_chkdet['user_id'], $team)) !== false) 
                 {
                  unset($team[$key]);
                  if(count($team)>=1)
                  {
                 $has_child=1;
                  }else{
                  $has_child=0;
                  }
                 }
                }else{
                $is_dashboard=0;
                $has_child=0;
                }
         if($module_id==1)
         {
            $Update_custoken= DB::table('users as us')->where("us.user_id","=", $user_chkdet['user_id'])->useWritePdo()->update(array('lp_token' => $customer_token,'updated_at' => date("Y-m-d H:i:s")));
                }else if($module_id==2)
                {
                   $Update_custoken= DB::table('users as us')->where("us.user_id","=", $user_chkdet['user_id'])->useWritePdo()->update(array('chat_token' => $customer_token,'updated_at' => date("Y-m-d H:i:s"))); 
         }else {
             $Update_custoken= DB::table('users as us')->where("us.user_id","=", $user_chkdet['user_id'])->useWritePdo()->update(array('password_token' => $customer_token,'updated_at' => date("Y-m-d H:i:s")  )); 
         }
                if($user_chkdet['profile_picture']==null)
                {
                 $profile_picture=""; 
                }else{
                  $profile_picture=$user_chkdet['profile_picture'];
                }       
       $DataFilter=$this->rolem->getFilterData(6,$user_chkdet['user_id']);
       $decode_data=json_decode($DataFilter,true);
       $sbu_lits = isset($decode_data['sbu']) ? $decode_data['sbu']:[];
       $decode_sbulist= json_decode($sbu_lits,true);
       $hub = (isset($decode_sbulist[118002])&& !empty($decode_sbulist[118002])) ? $decode_sbulist[118002] : '';
       $le_wh_id = (isset($decode_sbulist[118001])&& !empty($decode_sbulist[118001])) ? $decode_sbulist[118001] : '';
      
       $ff_ecash_details=$this->_ecash->getEcashInfo($user_chkdet['user_id']); 

      $data=array();
      $data['customer_group_id']='';
      $data['customer_token']=$customer_token;
      $data['customer_id']=$user_chkdet['user_id'];
      $data['legal_entity_id']=$user_chkdet['legal_entity_id'];
      $data['parent_le_id']=$user_chkdet['parent_le_id'];
      $data['firstname']=$user_chkdet['firstname'];
      $data['lastname']=$user_chkdet['lastname'];
      $data['image']=$profile_picture;
      $data['segment_id']='';
      $data['pincode']='';
      $data['le_wh_id']=$le_wh_id;
      $data['hub']=$hub;
      $data['is_active']=$user_chkdet['is_active'];
      $data['is_ff']=$is_ff;
      $data['is_srm']=$is_srm;
      $data['is_dashboard']=$is_dashboard;
      $data['has_child']=$has_child;
      $data['lp_feature']=$lp_feature;
      $data['mobile_feature']=$mobile_feature;
      $data['beat_id']='';
      $data['latitude']='';
      $data['longitude']='';
      $data['ff_ecash_details']=$ff_ecash_details;
      $data['mfc']=$mfc;
      $data['ff_full_name'] = $user_chkdet['firstname'].' '.$user_chkdet['lastname'];
      $data['ff_profile_pic'] = $user_chkdet['profile_picture'];
      $data['credit_limit']=$creditlimit;
      $data['aadhar_id']=$user_chkdet['aadhar_id'];
     $promotions = DB::select(DB::raw("select COUNT(*) as count FROM promotion_cashback_details pc 
                  JOIN legalentity_warehouses lw  ON FIND_IN_SET(lw.`le_wh_id`, pc.`wh_id`) 
                  AND lw.`legal_entity_id` = ".$user_chkdet['legal_entity_id']." WHERE cbk_status = 1 AND is_self in (0,2) AND CURDATE() BETWEEN start_date AND end_date"));
      $data['promotion_count'] = $promotions[0]->count;
            $res=array();
            $res['message']="Thank you for confirming your Mobile Number";
            $res['status']=1;
            $res['approved']=1;
            $res['data']=$data;
          return $res;
                 
                }else
                {
             
                  $is_ff=0;
                  $is_srm=0;

                   $user_leg =DB::table('users as us')
                ->select(DB::raw("us.*,le.is_approved"))
                ->leftJoin('legal_entities as le','le.legal_entity_id','=','us.legal_entity_id')
                ->where("us.mobile_no",'=', $phonenumber)
                ->whereIn('le.legal_entity_type_id',function($query) use ($desc){
                         $query->select('value')
                                  ->from('master_lookup as ml')
                                 ->where('ml.mas_cat_id','=',$desc)
                                  ->where("ml.is_active", "=", 1);
                         })
                ->get()->all(); 

      if(!empty( $user_leg))
      {

        $user_det=json_decode(json_encode($user_leg[0]),true);


        $user_query=sizeof($user_leg);


        // ------After completing Registartion-------


 if( ($user_query==1 && $user_det['is_active']==1) ){
  //||($user_query==1 && $ff_check==1 )
   
           $customer_id=$user_det['user_id'];
          
          

              $customer_token = md5(uniqid(mt_rand(), true));
          

           $Update_custoken= DB::table('users as us')          
              ->where("us.user_id","=", $customer_id)
              ->useWritePdo()   
               ->update(array('password_token' => $customer_token,
                             'updated_at' => date("Y-m-d H:i:s")  )); 
             
/*
             if($ff_check !=1)
             {*/
            $segment= DB::table('legal_entities as le')
                ->select(DB::raw("business_type_id,legal_entity_type_id,pincode,latitude,longitude,parent_le_id"))
                ->where("le.legal_entity_id",'=', $legal_entity_id)
                ->get()->all();
            

               $segment_det=json_decode(json_encode($segment[0]),true);
              

               $business_type_id=$segment_det['business_type_id'];

               $pincode=$segment_det['pincode'];
               $legal_entity_type_id=$segment_det['legal_entity_type_id'];
               // Adding Parent Le Id 
               $parent_le_id=$segment_det['parent_le_id'];

                if($user_det['profile_picture']==null)
                {

                 $profile_picture=""; 
                }else{

                  $profile_picture=$user_det['profile_picture'];
                }


              $le_wh_id =$this->getWarehouseIdByMobileNo($phonenumber);

                if(empty($le_wh_id))
                {
                 
                 $le_wh_id='';
                
                }


 }
          $beat_id = DB::table('customers')
                    ->select(DB::raw("beat_id"))
                    ->where("le_id",'=', $user_det['legal_entity_id'])
                    ->get()->all();
      $hub='';
      if(!empty($beat_id) && count($beat_id)>0){
        $hub=$this->getHub($beat_id[0]->beat_id);  
      }    
       if(empty($hub))
       {
         $hub='';
       }     
      $ecash_details=$this->_ecash->getEcashInfo($user_det['user_id']);  

      $data=array();

      if(isset($legal_entity_type_id) and isset($parent_le_id) and isset($pincode) and isset($business_type_id)){
        $data['customer_group_id']=$legal_entity_type_id;
        $data['customer_token']=$customer_token;
        $data['customer_id']=$user_det['user_id'];
        $data['legal_entity_id']=$user_det['legal_entity_id'];
        $data['parent_le_id']=$parent_le_id;
        $data['firstname']=$user_det['firstname'];
        $data['lastname']=$user_det['lastname'];
        $data['image']=$profile_picture;
        $data['segment_id']=$business_type_id;
        $data['pincode']=$pincode;
        $data['le_wh_id']=$le_wh_id ;
        $data['hub']=$hub;
        $data['is_active']=$user_det['is_active'];
        $data['is_ff']=$is_ff;
        $data['is_srm']=$is_srm;
        $data['is_dashboard']=0;
        $data['lp_feature']=[];
        $data['mobile_feature']=$mobile_feature;
        $data['beat_id']=$beat_id[0]->beat_id;
        $data['latitude']=$segment_det['latitude'];
        $data['longitude']=$segment_det['longitude'];
        $data['ecash_details']=$ecash_details;
        $data['ff_full_name']=$user_det['firstname'].' '.$user_det['lastname'];
        $data['ff_profile_pic']=$user_det['profile_picture'];
        $data['mfc']=$mfc;
        $data['aadhar_id']=$user_det['aadhar_id'];
        $data['credit_limit']=$creditlimit;
        if($le_wh_id!=''){
          $promotions = DB::select(DB::raw("select count(*) as count from promotion_cashback_details where  FIND_IN_SET (".$le_wh_id.",wh_id) and cbk_status=1 and is_self in (1,2) and CURDATE() between start_date and end_date and customer_type like '%".$legal_entity_type_id."%'"));
          $data['promotion_count'] = $promotions[0]->count;
        }else{
          $data['promotion_count'] =0;
        }
  
              $res=array();
              $res['message']="Thank you for confirming your Mobile Number";
              $res['status']=1;
              $res['approved']=1;
              $res['data']=$data;
        //$final=json_encode($res);
          // print_r($final);exit;
            return $res;
      }else {
        $res=array();
        $res['message']="Your account has been deactivated. Please contact a Ebutor administrator";
        $res['status']=0;
        $res['approved']=0;
        $res['data'] = [];
        return $res;
      }
      



    } 





        }


}
        //print_r($user_det);exit;
       
 
         $use_temp =DB::table('user_temp as ustemp')
                ->select(DB::raw("*"))
                ->where("ustemp.mobile_no",'=', $phonenumber)
                ->where("ustemp.otp",'=', $otp)
                ->useWritePdo()
                ->get()->all();  



      if(!empty($use_temp))
      {
         $user_temp_det=json_decode(json_encode($use_temp[0]),true); 

    

          $user_temp_query=sizeof($use_temp);
         

        
    // ------Before completing Registartion-------

    if($user_temp_query ==1 ){


         $Update_status= DB::table('user_temp as ustmp')          
              ->where("ustmp.mobile_no","=", $phonenumber)    
               ->update(array('status' => '1',
                             'updated_at' => date("Y-m-d H:i:s")  ))   ;   


            if($Update_status){
            $res=array();
            $res['message']="Thank you for confirming your Mobile Number";
            $res['status']=1;
            $res['approved']=0;
            $res['data']=[];
          
          $final=json_encode($res);
          //print_r($final);exit;

            return $res;

           }

    }                   
    }
      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  

  }
  
  
    /*
    * Function Name: checkOtpUser
    * Description: To check OTP avalibilty from users
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 30 June 2016
    * Modified Date & Reason:
    */

    public function checkOtpUser($otp,$telephone)

    {
            $query = DB::selectFromWriteConnection(DB::raw("select * from users us where us.otp =".$otp ." and  us.mobile_no=".$telephone));
            return $query;

    }
 /*
    * Function Name: checkOtpUsertemp
    * Description: To check OTP avalibilty from user_temp table
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 30 June 2016
    * Modified Date & Reason:
    */
    public function checkOtpUsertemp($otp,$telephone)

    {
         
          $query = DB::selectFromWriteConnection(DB::raw("select * from user_temp ustmp where ustmp.otp =".$otp ." and  ustmp.mobile_no=".$telephone));
          return $query;

    }
    
  /*
    * Function Name: getAllCustomers
    * Description: To get all the customers
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 29 July 2016
    * Modified Date & Reason:
    */   

  public function getAllCustomers($ff_id,$beat_id,$is_billed,$offset,$offset_limit,$search,$flag,$hub,$spoke,$sort)
 {
try{
  if(empty($flag) && $sort== ""){
    $sort=146006;
  }
  if($flag==1)
      {
        $query =DB::table('legal_entities AS leg')
				  ->select('leg.business_legal_name AS company','leg.latitude','leg.longitude',
          'leg.address1 AS address_1','leg.address2','leg.legal_entity_id',
          db::raw("CONCAT(users1.firstname,' ',users1.lastname) as firstname"),
          'cust.beat_id',db::raw("getBeatName(cust.beat_id) as beatname"),
          db::raw("getRetailerCheck_in( leg.legal_entity_id) as check_in"),
          'users1.mobile_no AS telephone','users1.user_id AS customer_id',
          'users1.password_token as customer_token',
          'No_of_shutters','volume_class','business_type_id','master_manf',
          'leg.legal_entity_type_id as buyer_type',db::raw("CASE
          WHEN volume_class IS NULL OR volume_class='' OR No_of_shutters IS NULL OR No_of_shutters=''
          OR master_manf IS NULL OR master_manf='' THEN 1
          ELSE 0
          END AS popup"));
      }else{
        $query =DB::table('legal_entities AS leg')
          ->select('leg.business_legal_name AS company','leg.latitude','leg.longitude',
          'leg.address1 AS address_1','leg.address2','leg.legal_entity_id',
          db::raw("CONCAT(users1.firstname,' ',users1.lastname) as firstname"),
          'cust.beat_id',db::raw("getBeatName(cust.beat_id) as beatname"),
          db::raw("getRetailerCheck_in( leg.legal_entity_id) as check_in"),
          'users1.mobile_no AS telephone','users1.user_id AS customer_id',
          'users1.password_token as customer_token',
          'No_of_shutters','volume_class','business_type_id','master_manf',
          'leg.legal_entity_type_id as buyer_type',db::raw("CASE
          WHEN volume_class IS NULL OR volume_class='' OR No_of_shutters IS NULL OR No_of_shutters=''
          OR master_manf IS NULL OR master_manf='' THEN 1
          ELSE 0
          END AS popup"),
          DB::raw("getRetailerOrdersCount(leg.legal_entity_id) AS no_of_orders"),
          DB::raw("getRetailerReturnsCount(leg.legal_entity_id) AS return_orders"),
          DB::raw("getRetailerTotalBusiness(leg.legal_entity_id) AS total_business"),
          DB::raw("getRetailerAvgBillValue(leg.legal_entity_id) AS avg_bill_val"),
         // DB::raw("getRetailerRating(leg.legal_entity_id) AS rank"),
          DB::raw("0 as 'rank'"),
           DB::raw('IFNULL((select ROUND(uec.cashback,2) from users us join user_ecash_creditlimit uec ON uec.user_id=us.user_id where us.legal_entity_id=leg.legal_entity_id and us.is_parent=1 limit 1),0) as remain_bal')
          );
      }
      
            $query = $query->Join('users as users1','leg.legal_entity_id','=','users1.legal_entity_id')
                ->where('users1.is_active',1)
                ->where ('users1.is_parent',1)
                ->where(function ($query) {
                  $query->where('legal_entity_type_id', 'LIKE', '%30%')
                        ->orWhere('legal_entity_type_id', 'LIKE', 1014)
                        ->orWhere('legal_entity_type_id', 'LIKE', 1016);
                });
        
          if($flag==1)
           { 

            if(!empty($beat_id))
             { 

           if(($key = array_search(-1,$beat_id)) !== false) 
           {
              
              $query->Join('customers as cust','leg.legal_entity_id','=','cust.le_id');

          }else{
            //$beat_id= explode(",",$beat_id);
            $query->Join('customers as cust',function ($join) use ($beat_id)
                     {                 
                      $join->on('leg.legal_entity_id', '=', 'cust.le_id')
                           ->whereIn('cust.beat_id',$beat_id);
                     });
             }
           }

           if(!empty($hub) || !empty($spoke))
           {
         $query->Join('pjp_pincode_area as pa','pa.pjp_pincode_area_id','=','cust.beat_id')
           ->Join('spokes as sp','pa.spoke_id','=','sp.spoke_id');
           }
           if(!empty($hub) && empty($spoke))
           {
             
              $query->whereRaw('FIND_IN_SET(sp.le_wh_id,"'.$hub.'")');

           }
            
            if(!empty($spoke) && !empty($hub))
           {
            
              $query->whereRaw('FIND_IN_SET(sp.spoke_id,"'.$spoke.'")');

           }


       }else{
          if($beat_id!=-1)
           {
           $query->Join('customers as cust',function ($join) use ($beat_id)
                     {                 
                      $join->on('leg.legal_entity_id', '=', 'cust.le_id')
                           ->where('cust.beat_id', '=', $beat_id);
                     });
         }else{
          $query->Join('customers as cust','leg.legal_entity_id','=','cust.le_id');
         }
       }
           if($is_billed==1) 
             {
              
              $date=date("Y-m-d");
              $result=$query->whereNotIn('leg.legal_entity_id', function($orders) use($date,$ff_id){
                           $orders->select(db::raw('go.cust_le_id'))
                                  ->from('gds_orders as go')
                                  ->where(db::raw(" DATE(created_at)"),$date)
                                  ->where('go.created_by',$ff_id);
                                });

             }else{

                $result=$query;
             }

              if(!empty($search)) 
             {
              
              $result=$query->where(function($query) use ($search) {
                              $query->where('leg.business_legal_name','LIKE','%'.$search.'%')
                                  ->orwhere('users1.mobile_no','LIKE','%'.$search.'%');
              });
     
             }

         if(!empty($sort))
         {
           if($sort==146001)
           {
            $result->orderBy('avg_bill_val','ASC');
          }elseif($sort==146006){
             $result->orderBy('avg_bill_val','DESC');
          }elseif($sort==146002){
           $result->orderBy('leg.business_legal_name','ASC');
          }elseif($sort==146005){
             $result->orderBy('leg.business_legal_name','DESC');
          }/*elseif($sort==146007){
            $result->orderBy('rank','ASC');
          }elseif($sort==146004){
            $result->orderBy('rank','DESC');
          }*/elseif($sort==146003){
          $result->orderBy('check_in','ASC');
          }elseif($sort==146010){
            $result->orderBy('remain_bal','ASC');
          }elseif($sort==146009){
            $result->orderBy('remain_bal','DESC');
          }else{
            $result->orderBy('check_in','DESC');
          }
     }

     if(!empty($offset_limit)){

           $result  = $result
                      //  ->orderBy('leg.business_legal_name','ASC')
                        ->skip($offset)
                        ->take($offset_limit)
                        ->get()->all();    
      }else{
         $result  = $result
        // ->orderBy('leg.business_legal_name','ASC')
         ->get()->all();
      }

      return $result;

      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  

    }
  
    /*
    * Function Name: serviceablePincode
    * Description: To get check wether pincode is in servicable location or not
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 12 Aug 2016
    * Modified Date & Reason:
    */ 

    public function serviceablePincode($pincode){
      $query =DB::table('wh_serviceables as whs')
                ->select(DB::raw("count(le_wh_id) as count"))
                ->where("whs.pincode",'=', $pincode)
                ->get()->all();

                return $query;
    }


       /*
    * Function Name: serviceablePincode
    * Description: To get check wether pincode is in servicable location or not
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 12 Aug 2016
    * Modified Date & Reason:
    */ 

    public function generateRetailerToken($phonenumber,$ff_token,$latitude,$longitude){

     try{
    $desc=$this->getMaterDescription(78002);
    $user_leg =DB::table('users as us')
                ->select(DB::raw("us.*,le.is_approved,le.business_type_id,le.legal_entity_type_id,le.pincode,le.latitude,le.longitude,le.parent_le_id"))
                ->leftJoin('legal_entities as le','le.legal_entity_id','=','us.legal_entity_id')
                ->where("us.mobile_no",'=', $phonenumber)
                ->where("us.is_active",1)
                ->where(function ($query) {
                  $query->where('le.legal_entity_type_id', 'LIKE', '%30%')
                        ->orWhere('le.legal_entity_type_id', 'LIKE', 1014)
                        ->orWhere('le.legal_entity_type_id', 'LIKE', 1016);
                })->get()->all();
      if(!empty( $user_leg))
      {
        $user_det=json_decode(json_encode($user_leg[0]),true);
        $user_query=sizeof($user_leg);
        
        // ------After completing Registartion-------

 if($user_query==1 && $user_det['is_active']==1){



           $customer_id=$user_det['user_id'];
           $customer_token = md5(uniqid(mt_rand(), true));

           $Update_custoken= DB::table('users as us')          
              ->where("us.user_id","=", $customer_id)   
               ->update(array('password_token' => $customer_token,
                             'updated_at' => date("Y-m-d H:i:s")  ));  
         
          $data=$this->InsertNewFfComments($ff_token,$customer_id,107000,$latitude,$longitude);

          //$le_wh_id =$this->getWarehouseid($user_det['pincode']);
          $le_wh_id=$this->getWarehouseIdByMobileNo($phonenumber);
          if($le_wh_id==''){
            return Array('status' => "failed", 'message' => 'give proper input', 'data' =>""); 
          }

          if(empty($le_wh_id))
          {
           
           $le_wh_id='';
          
          }
               
       $beat_id = DB::table('customers')
                    ->select(DB::raw("beat_id"))
                    ->where("le_id",'=', $user_det['legal_entity_id'])
                    ->get()->all();
  
       $hub=$this->getHub($beat_id[0]->beat_id);
        
       if(empty($hub))
       {
         $hub='';
       }              
      $mobile_feature=$this->getFeatures($user_det['user_id'],2); 
      $ecash_details=$this->_ecash->getEcashInfo($user_det['user_id']); 

      $ff_id=$this->_account->getUserIdByCustomerToken($ff_token); 

      $ff_ecash_details=$this->_ecash->getEcashInfo($ff_id);     

      $data=array();
      $data['customer_group_id']=$user_det['legal_entity_type_id'];
      $data['customer_token']=$customer_token;
      $data['legal_entity_id']=$user_det['legal_entity_id'];
      $data['parent_le_id']=$user_det['parent_le_id'];
      $data['customer_id']=$user_det['user_id'];
      $data['firstname']=$user_det['firstname'];
      $data['lastname']=$user_det['lastname'];
      $data['image']=$user_det['profile_picture'];
       $data['segment_id']=$user_det['business_type_id'];
       $data['pincode']=$user_det['pincode'];
       $data['is_active']=$user_det['is_active'];
       $data['le_wh_id']=$le_wh_id;
       $data['hub']=$hub;
       $data['is_ff']=0;
       $data['lp_feature']=[];
       $data['mobile_feature']=$mobile_feature;
       $data['beat_id']=$beat_id[0]->beat_id;
       $data['latitude']=$user_det['latitude'];
       $data['longitude']=$user_det['longitude'];
       $data['ecash_details']=$ecash_details;
       $data['ff_ecash_details']=$ff_ecash_details;
        if($le_wh_id!=''){
         $promotions = DB::select(DB::raw("select count(*) as count from promotion_cashback_details where  FIND_IN_SET (".$le_wh_id.",wh_id) and cbk_status=1 and is_self = 0 and CURDATE() between start_date and end_date and customer_type like '%".$user_det['legal_entity_type_id']."%'"));
         $data['promotion_count'] = $promotions[0]->count;
        }else{
          $data['promotion_count'] = 0;
        }

            $res=array();
            $res['message']="Token generated";
            $res['status']=1;
            $res['approved']=1;
            $res['data']=$data;
            return $res;

    } 

    }

       }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  

    }

/*
    * Function Name: checkUser
    * Description: To checkUser with that phonenumber
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason:
    */


  public function checkUser($contact)
  {
    try{

       $master = DB::table('master_lookup as ml')
                       ->where("ml.value", "=", 78002)->get()->all();

        $desc= $master[0]->description;
        $query =DB::table('users as us')
                ->select(DB::raw("COUNT(us.user_id) as count"))
                ->leftJoin('legal_entities as le','le.legal_entity_id','=','us.legal_entity_id')
                ->where("us.mobile_no",'=', $contact)
                 ->whereIn('le.legal_entity_type_id',function($query) use ($desc){
                           $query->select('value')
                                  ->from('master_lookup as ml')
                                  ->where('ml.mas_cat_id','=',$desc)
                                  ->where("ml.is_active", "=", 1);
                         })
                ->get()->all();
                return  $query[0]->count;

      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }          

    }

    /*
    * Function Name: checkfiledfource
    * Description: To checkUser with that phonenumber
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 Sep 2016
    * Modified Date & Reason:
    */

      public function checkfiledfource($telephone)
     {
      try{
       
        $master = DB::table('master_lookup as ml')
                       ->where("ml.value", "=", 78002)->get()->all();

      $desc= $master[0]->description;

        $userchk = DB::table('users')
              ->where("mobile_no", "=", $telephone)
              ->where("is_active",1)->get()->all();
        
         if($userchk)
          {

          $user_chkdet=json_decode(json_encode($userchk[0]),true);

          $ff_check=$this->_role->checkPermissionByFeatureCode('EFF001',$user_chkdet['user_id']);          
          $srm_check=$this->_role->checkPermissionByFeatureCode('SRM001',$user_chkdet['user_id']);
          $customer_chk=$this->_role->checkPermissionByFeatureCode('MCU001',$user_chkdet['user_id']);
          $lp_feature=$this->getFeatures($user_chkdet['user_id'],1);
          $mobile_feature=$this->getFeatures($user_chkdet['user_id'],2);

          }else{
           
           $ff_check=0;
           $srm_check=0;
           $customer_chk=0;
           $lp_feature=array();
           $mobile_feature=array();

          }
          
          if($ff_check==1 || $srm_check==1 || !empty($lp_feature) ||  (!empty($mobile_feature) && $customer_chk==0)){
              
                $result_users =DB::table('users as us')
               ->select(DB::raw("us.otp as otp_number"))
               ->where("us.mobile_no",'=', $telephone)
                ->get()->all();
          }else{

             $result_users = DB::table('users as user')
                              ->select(DB::raw('user.otp as otp_number'))
                                ->leftjoin('legal_entities as leg','leg.legal_entity_id','=','user.legal_entity_id')
                               ->where('mobile_no', '=', $telephone)
                             ->whereIn('leg.legal_entity_type_id',function($query) use ($desc){
                            $query->select('value')
                                   ->from('master_lookup as ml')
                                   ->where('ml.mas_cat_id','=',$desc)
                                   ->where("ml.is_active", "=", 1);
                          })
                              ->get()->all();

  $result_users2 =DB::table('user_temp as us')
               ->select(DB::raw("us.otp as otp_number"))
               ->where("us.mobile_no",'=', $telephone)
                ->get()->all();
          
          }
                
               if(!empty($result_users2)){
                return $result_users2;
                
                }else{
                    
                    return $result_users; 
                }

           
         }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }   
    
               

    }
    
  public function checkSalesToken($sales_token){

  $user_id = DB::table('users')
  ->select('user_id')
  ->where('password_token','=',$sales_token)
  ->get()->all();
  if(!empty($user_id)){
    $user_id = $user_id[0]->user_id;
    $ff_check=$this->_role->checkPermissionByFeatureCode('EFF001',$user_id);
    if($ff_check!=1)
           {

            $ff_check=$this->_role->checkPermissionByFeatureCode('MFF001',$user_chkdet['user_id']);          
          
           }
  }else{
    $ff_check = 0;
  }
  
  return $ff_check;



  }
  
  
  public function getSalesOtp($sales_token){
 
   /*$salesotp = DB::table('users')
    ->select('otp as otp_number')
    ->where('password_token','=',$sales_token)
    ->orwhere('lp_token','=',$sales_token)
    ->first();*/
    $salesotp = DB::selectFromWriteConnection(DB::raw("select otp as otp_number from users where (password_token='".$sales_token."' or lp_token =  '".$sales_token ."')" ));
    if(count($salesotp)>0){
      return $salesotp[0];
    }else{
      return '';
    }
     
  }

/*
    * Function Name:getEmail
    * Description: To getEmail with count
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 11 sep 2016
    * Modified Date & Reason:
    */

   public function getEmail($email){
    $mail = DB::table('users')
    ->select(DB::raw("count(email_id) as count"))
    ->where('email_id','=',$email)
    ->get()->all();
    return $mail;
  }

  /*
    * Function Name:UpdateRetailerData
    * Description: To UpdateRetailerData 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 sep 2016
    * Modified Date & Reason:
    */


   public function updateRetailerData($user_id
                  ,$segment_id,$volume_class,$noof_shutters,$master_manf,$smartphone,
                  $network,$buyer_type,$ff_id){
   try{ 
        $retailerObj = new Retailer($this->_role);
       $user_id = DB::table('users as user')
          ->select(DB::raw("leg.legal_entity_id"))
          ->join('legal_entities as leg','leg.legal_entity_id','=','user.legal_entity_id')                 
          ->where('user_id','=',$user_id)
          ->get()->all();
     
     if(!empty($buyer_type))
     {

    $Update_segment= DB::table('legal_entities')          
              ->where("legal_entity_id","=", $user_id[0]->legal_entity_id)   
                            ->whereNotIn("legal_entity_type_id",[1014,1016])  
               ->update(array('business_type_id' => $segment_id,
                              'legal_entity_type_id'  =>$buyer_type,
                              'updated_by'  =>$ff_id,
                             'updated_at' => date("Y-m-d H:i:s"))); 
       }else{
        
          $Update_segment= DB::table('legal_entities')          
              ->where("legal_entity_id","=", $user_id[0]->legal_entity_id)
              ->whereNotIn("legal_entity_type_id",[1014,1016])   
              ->update(array('business_type_id' => $segment_id,
                               'updated_by'  =>$ff_id,  
                             'updated_at' => date("Y-m-d H:i:s")  
                             )); 

       }        

      $cust_chk = DB::table('customers')                
          ->where('le_id','=',$user_id[0]->legal_entity_id)
          ->get()->all();

      if(!empty($cust_chk))
      {
     $result= DB::table('customers')          
              ->where("le_id","=", $user_id[0]->legal_entity_id)   
               ->update(array('volume_class' => $volume_class,
                              'No_of_shutters' =>$noof_shutters,
                              'master_manf' =>$master_manf,
                              'dist_not_serv'=>$master_manf,
                              'smartphone' =>$smartphone,
                              'network' =>$network,
                               'updated_by'  =>$ff_id,  
                             'updated_at' => date("Y-m-d H:i:s")));
        $retailer_smartphone=$smartphone==1?'YES':'NO'; 
        $retailer_network=$network==1?'YES':'NO';
        $volumeClassName = explode(',', $volume_class);
        $volumeClassName=$retailerObj->getNameFromMaster($volumeClassName);
        $volumeClassName=implode(",", $volumeClassName);

        $noofSuppliers = explode(',', $master_manf);
        $noofSuppliers=$retailerObj->getNameFromMaster($noofSuppliers); 
        $noofSuppliers=implode(",", $noofSuppliers);

        $retailer_flat=DB::table('retailer_flat')
                        ->where("legal_entity_id","=",$user_id[0]->legal_entity_id)
                        ->update(
                          array(
                            'volume_class'=>$volumeClassName,
                            'volume_class_id'=>$volume_class,
                            'No_of_shutters' =>$noof_shutters,
                            'master_manf' =>$master_manf,
                            'dist_not_serv'=>$master_manf,
                            'suppliers' => $noofSuppliers,
                            'smartphone'=>$retailer_smartphone,
                            'network'=>$retailer_network,
                            'updated_by'  =>$ff_id,  
                            'updated_at' => date("Y-m-d H:i:s")
                          )
                        ); 

             }else{
 

        $result= DB::table('customers')->insert([
                                'le_id' => $user_id[0]->legal_entity_id,
                                'volume_class' => $volume_class,
                                'No_of_shutters' =>$noof_shutters,
                              'master_manf' =>$master_manf,
                              'smartphone' =>$smartphone,
                              'network' =>$network,
                               'updated_by'  =>$ff_id,  
                             'updated_at' => date("Y-m-d H:i:s")
                  
            ]);
                $retailer_smartphone=$smartphone==1?'YES':'NO'; 
                $retailer_network=$network==1?'YES':'NO';
                $volumeClassName = explode(',', $volume_class);
                $volumeClassName=$retailerObj->getNameFromMaster($volumeClassName); 
                $volumeClassName=implode(",", $volumeClassName);

                $noofSuppliers = explode(',', $master_manf);
                $noofSuppliers=$retailerObj->getNameFromMaster($noofSuppliers); 
                $noofSuppliers=implode(",", $noofSuppliers);               
                $retailer_flat=DB::table('retailer_flat')
                                ->where("legal_entity_id","=",$user_id[0]->legal_entity_id)
                                ->update(
                                  array(
                                    'volume_class'=>$volumeClassName,
                                    'volume_class_id'=>$volume_class,
                                    'No_of_shutters' =>$noof_shutters,
                                    'master_manf' =>$master_manf,
                                    'suppliers' => $noofSuppliers,
                                    'smartphone'=>$retailer_smartphone,
                                    'network'=>$retailer_network,
                                    'updated_by'  =>$ff_id,  
                                    'updated_at' => date("Y-m-d H:i:s")
                                  )
                                );   


                $retailer_smartphone=$smartphone==1?'YES':'NO'; 
                $retailer_network=$network==1?'YES':'NO';
                $volumeClassName = explode(',', $volume_class);
                $volumeClassName=$retailerObj->getNameFromMaster($volumeClassName); 
                $volumeClassName=implode(",", $volumeClassName);

                $noofSuppliers = explode(',', $master_manf);
                $noofSuppliers=$retailerObj->getNameFromMaster($noofSuppliers); 
                $noofSuppliers=implode(",", $noofSuppliers);               
                $retailer_flat=DB::table('retailer_flat')
                                ->where("legal_entity_id","=",$user_id[0]->legal_entity_id)
                                ->update(
                                  array(
                                    'volume_class'=>$volumeClassName,
                                    'volume_class_id'=>$volume_class,
                                    'No_of_shutters' =>$noof_shutters,
                                    'master_manf' =>$master_manf,
                                    'suppliers' => $noofSuppliers,
                                    'smartphone'=>$retailer_smartphone,
                                    'network'=>$retailer_network,
                                    'updated_by'  =>$ff_id,  
                                    'updated_at' => date("Y-m-d H:i:s")
                                  )
                                );   

             }


    return true;

       }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  


  }

  /*
    * Function Name: InsertFfComments
    * Description: To insert into ff_call_logs table
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15 sep 2016
    * Modified Date & Reason:
    */

   public function InsertFfComments( $sales_token,$user_id,$activity,$latitude,$longitude)
   {
    try{
        
       /* if(!empty($sales_token))
        {  
        $ff_id= DB::table('users')
              ->select('user_id')
              ->where('password_token','=',$sales_token)
              ->get()->all();
       $ff_id=$ff_id[0]->user_id;
         }else{
          $ff_id='';
         }     

        $legal_entity_id= DB::table('users')
              ->select('legal_entity_id')
              ->where('user_id','=',$user_id)
              ->get()->all();

     
       if($activity==107000)   
       {  
          DB::table('ff_call_logs')->insert([
              'ff_id' => $ff_id,
              'user_id'=>$user_id,
              'legal_entity_id'=>$legal_entity_id[0]->legal_entity_id,
              'activity'=>$activity,
               'check_in'=>date("Y-m-d H:i:s"),
              'check_in_lat'=>$latitude,
              'check_in_long'=>$longitude,
              'created_at'=> date("Y-m-d H:i:s")
            ]);
       }else{
       

         DB::table('ff_call_logs')          
              ->where("ff_id","=",$ff_id)   
               ->where("user_id","=",$user_id)
               ->where("legal_entity_id","=",$legal_entity_id[0]->legal_entity_id)
               ->where("created_at","=", date("Y-m-d H:i:s"))
               ->update(array('activity' => $activity,
                              'check_out'=> date("Y-m-d H:i:s"))); 
                  

       }*/

       
        
        $ff_id= DB::table('users')
              ->select('user_id')
              ->where('password_token','=',$sales_token)
              ->get()->all();

        $legal_entity_id= DB::table('users')
              ->select('legal_entity_id')
              ->where('user_id','=',$user_id)
              ->get()->all();

          $result= DB::table('ff_call_logs')->insert([
              'ff_id' => $ff_id[0]->user_id,
              'user_id'=>$user_id,
              'legal_entity_id'=>$legal_entity_id[0]->legal_entity_id,
              'activity'=>$activity,
              'latitude'=>$latitude,
              'longitude'=>$longitude,
              'created_at'=> date("Y-m-d H:i:s"),
              'check_in'=>date("Y-m-d H:i:s"),
              'check_in_lat'=>$latitude,
              'check_in_long'=>$longitude                   
            ]);     

      return $result;

         }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  
      

  }


 /*
    * Function Name: InsertNewFfComments
    * Description: To insert into ff_call_logs table
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15 sep 2016
    * Modified Date & Reason:
    */

   public function InsertNewFfComments( $sales_token,$user_id,$activity,$latitude,$longitude)
   {
    try{
        if(!empty($sales_token))
        {  
        $ff_id= DB::table('users')
              ->select('user_id')
              ->where('password_token','=',$sales_token)
              ->useWritePdo()
              ->get()->all();
       $ff_id=$ff_id[0]->user_id;
         }else{
          $ff_id='';
         }     

        $legal_entity_id= DB::table('users')
              ->select('legal_entity_id')
              ->where('user_id','=',$user_id)
              ->get()->all();

  
       if($activity==107000)   
       { 
  

    $log_chk= DB::table('ff_call_logs as fcl')
              ->select('log_id')
              ->where('legal_entity_id','=',$legal_entity_id[0]->legal_entity_id)
              ->where('ff_id','=',$ff_id)
              ->where(db::raw("DATE(created_at)"),date("Y-m-d"))
              ->get()->all();
             
              /*if(empty($log_chk))
              {*/
                DB::table('ff_call_logs')->insert([
                  'ff_id' => $ff_id,
                  'user_id'=>$user_id,
                  'legal_entity_id'=>$legal_entity_id[0]->legal_entity_id,
                  'activity'=>$activity,
                   'check_in'=>date("Y-m-d H:i:s"),
                  'check_in_lat'=>$latitude,
                  'check_in_long'=>$longitude,
                  'created_at'=> date("Y-m-d H:i:s")
                ]);
              //}
       }else{
       
         $log_chk= DB::table('ff_call_logs as fcl')
              ->select('log_id')
              ->where('legal_entity_id','=',$legal_entity_id[0]->legal_entity_id)
              ->where('ff_id','=',$ff_id)
              //->where(db::raw("DATE(created_at)"),date("Y-m-d"))
              ->orderBy('log_id','desc')
              ->take(1)
              ->get()->all();
          if(count($log_chk)>0){
            DB::table('ff_call_logs')          
              ->where("ff_id","=",$ff_id)   
               ->where("user_id","=",$user_id)
              ->where("legal_entity_id","=",$legal_entity_id[0]->legal_entity_id)
              ->where("log_id",$log_chk[0]->log_id)
              //->where("created_at","=", date("Y-m-d H:i:s"))
              ->where(db::raw("DATE(created_at)"),date("Y-m-d"))
               ->update(array('activity' => $activity,
                              'check_out'=> date("Y-m-d H:i:s"),
                              'check_out_lat' => $latitude,
                              'check_out_long'=>$longitude));
          }     

       }

      return true;

      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  
      

  }

  public function UpdateCheckoutFfComments($sales_token,$user_id,$activity,$latitude,$longitude)
   {
    try{

        $ff_id= DB::table('users')
              ->select('user_id')
              ->where('password_token','=',$sales_token)
              ->useWritePdo()
              ->get()->all();

        $checkIndate= DB::table('ff_call_logs')
              ->select('check_in')
              ->where('user_id','=',$user_id)
              ->where('ff_id','=',$ff_id[0]->user_id)
              ->orderBy('check_in', 'desc')
              ->get()->all();

            //print_r($checkIndate);exit;

        $check_date= $checkIndate[0]->check_in; 

        $check_in_date=substr($check_date, 0, 10); 
        $logindate=date("Y-m-d");

        //if($check_in_date == $logindate)  { 

          $result= DB::table('ff_call_logs')
                        ->where('ff_id',$ff_id[0]->user_id)
                        ->where('user_id',$user_id)
                        ->where('activity',107000)
                        ->update(array('check_out_lat' => $latitude,'check_out_long'=>$longitude,'activity'=> $activity,'check_out' => date("Y-m-d H:i:s")));

          log::info('write connection');
          DB::connection('mysql-write')->table('offline_cart_details')
          ->where('cust_id',$user_id)
          ->delete();

          /*} else { 

            $result='';

          }*/

        //print_r($result);exit;       


        return $result;
     }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }      

  }


   /*
    * Function Name: checkPincode
    * Description: To check pincode is valid or not
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15 sep 2016
    * Modified Date & Reason:
    */

   public function checkPincode($pincode)
   {

        $result= DB::table('cities_pincodes')
              ->select(db::raw('count(distinct pincode) as count'))
              ->where('pincode','=',$pincode)
              ->get()->all();

        
      return $result;
      

  }

   /*
    * Function name: getFeatureByUserId
    * Description: Used to to features based on userid
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 oct 2016
    * Modified Date & Reason:
    */

   public function getFeatureByUserId($userid){

         $result = DB::table('user_roles')
                   ->select('features.feature_code','features.name')
                   ->leftJoin('users','users.user_id','=','user_roles.user_id')
                   ->leftJoin('role_access','role_access.role_id','=','user_roles.role_id')
                   ->leftJoin('features','features.feature_id','=','role_access.feature_id')
                   ->where('users.is_active',1)
                   ->where('user_roles.user_id','=',$userid)
                   ->where('features.is_mobile_enabled',1)
                   ->where('features.is_active',1)
                   ->orderBy('features.sort_order', 'ASC')
                   ->get()->all();
         return $result;
     }
    

   /*
    * Function name: getFeatures
    * Description: Used to to features based on userid
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 oct 2016
    * Modified Date & Reason:
    */

  public function getFeatures($userid,$flag)
     {
     
     //$userid=1641;
      if($flag==1)
       { 
       $feature_id=DB::table('features')
                    ->select('feature_id')
                    ->where('feature_code','=','LP0001')
                    ->get()->all();
       }else{             

       $feature_id=DB::table('features')
                    ->select('feature_id')
                    ->where('feature_code','=','M00001')
                    ->get()->all();
        }


           if(!empty( $feature_id))    
           {     
       
           $parent_id=$this->getMobileFeatures($userid,$feature_id[0]->feature_id);
        
         if(!empty($parent_id))
         {

          foreach ($parent_id as $key => $value) 
          {
             $features[]=$this->getMobileFeatures($userid,$value->feature_id);
            
          }


           foreach ($features as $key => $value) {
             foreach ($value as $key => $value1) {
               $result[]=$value1;
             }
           }

         if(!empty($result))
         {

           $data=array_merge($result,$parent_id);
         }else{

          $data=$parent_id;
         }
       
         }else{

          $data='';
         }
         }else{

          $data='';
         }

        return $data;
           
    
         
     }

     
 

   

     /*
    * Function name: getHub
    * Description: Used to get hub based on pincode
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 16 dec 2016
    * Modified Date & Reason:
    */
   public function getHub($beat_id){

         $hub = DB::table('pjp_pincode_area as ppa')
                ->select(DB::raw("distinct lew.le_wh_id as hub"))
                ->join('legalentity_warehouses as lew','ppa.le_wh_id','=','lew.le_wh_id')
                ->where("ppa.pjp_pincode_area_id",'=', $beat_id)
                ->where("lew.dc_type",'=', 118002)
                ->get()->all();
          
        if(!empty($hub)) 
        {
          if($hub[0]->hub)
           { 
          return $hub[0]->hub;
           }else{

            return '';
           }
        } else{

          return '';
        }      

         
     } 


     /*
    * Function name: getParentLeId
    * Description: To get Parent Legal Entity Id based on Pincode
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2018
    * Version: v1.0
    * Created Date: 15 may 2018
    * Modified Date & Reason:
    */
     public function getParentLeId($pincode){

      $parent = 
        DB::table('wh_serviceables')
          ->select('legal_entity_id')
          ->where('pincode',$pincode)
          ->first();

      if(isset($parent->legal_entity_id) and !empty($parent->legal_entity_id))
        return $parent->legal_entity_id;
      return '';
     }
     /*
    * Function name: getWarehouseid
    * Description: Used to get warehouse_id
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 26 dec 2016
    * Modified Date & Reason:
    */

     public function getWarehouseid($pincode,$getLegalEntityID = false){

      $le_wh = DB::table('wh_serviceables as ws')
                ->select("ws.le_wh_id as le_wh_id","ws.legal_entity_id")
                ->join('legalentity_warehouses as lew','ws.le_wh_id','=','lew.le_wh_id')
                ->where("ws.pincode",'=', $pincode)
                ->where("lew.dc_type",'=',118001)
                ->where("lew.status",'=',1)
                ->get()->all();

        if(!empty($le_wh[0]))   
        {
          // If the API wants legal Entity Id, then its set to 1(true)
          if($getLegalEntityID){
            return
              ['le_wh_id' => $le_wh[0]->le_wh_id,
              'legal_entity_id' => $le_wh[0]->legal_entity_id];
          }
          // If its only warehouse Id
          return $le_wh[0]->le_wh_id;
        }else{
          return '';
        }
    }

  
  /* GetMobileFeature */
  

     public function getMobileFeatures($userid,$feature_id)
     {


      $result = DB::table('user_roles')
                   ->select(DB::raw("distinct features.feature_code,features.name,features.is_menu,features.parent_id,features.feature_id"))
                   ->leftJoin('users','users.user_id','=','user_roles.user_id')
                   ->leftJoin('role_access','role_access.role_id','=','user_roles.role_id')
                   ->leftJoin('features','features.feature_id','=','role_access.feature_id')
                   ->where('users.is_active',1)
                   ->where('user_roles.user_id','=',$userid)
                   ->where('features.parent_id','=',$feature_id)
                   ->where('features.is_active',1)
                   ->orderBy('features.sort_order', 'ASC')
                   ->get()->all();

         return $result;
     }

   

    
  

  /*
    * Function name: getPjp
    * Description: Used to check weather pjp are assigned to ff person
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 27 oct 2016
    * Modified Date & Reason:
    */

   public function getPjp($ff_id){

         $result = DB::table('pjp_pincode_area as ppa')
                   ->select('rm_id')
                   ->where('ppa.rm_id','=',$ff_id)
                   ->get()->all();

         return $result;
     }    

   /*
    * Function name: getPjpBasedOnPincode
    * Description: Used to get pjps based on pincode
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 oct 2016
    * Modified Date & Reason:
    */

   public function getPjpBasedOnPincode($pincode,$le_wh_id){
    try{
     
        $beat= DB::table('pincode_area as pa')
                        ->select(DB::raw("ppa.pjp_pincode_area_id"))
                        ->join('pjp_pincode_area as ppa','pa.pjp_pincode_area_id','=','ppa.pjp_pincode_area_id')
                        ->where("pa.pincode",'=', $pincode);
                      

               if(!empty($le_wh_id))
               {
                 $result=$beat->whereRaw('FIND_IN_SET(ppa.le_wh_id,"'.$le_wh_id.'")');
                // where("ppa.le_wh_id",'=', $le_wh_id);
                 
               }else{

                 $result=$beat;
               }
             
               return  $result->take(1)->get()->all();
               
      
      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  
      } 


    /*
    * Function name: getRoleId
    * Description: Used to get role id for customers
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 9th jan  2017
    * Modified Date & Reason:
    */

   public function getRoleId(){

         $result = DB::table('legal_entity_roles as ler')
                   ->select(db::raw('DISTINCT ler.role_id'))
                   ->where('ler.le_type_id','LIKE','%30%')
                   ->get()->all();

         return $result[0]->role_id;
     }  

  public function getWarehouseidByHub($hub_id){

       $le_wh = DB::table('legalentity_warehouses as lew')
               ->select(['lew.le_wh_id'])
               ->leftJoin('dc_hub_mapping as dhm','dhm.dc_id','=','lew.le_wh_id')
               ->where("dhm.hub_id",'=',$hub_id)
               ->where("lew.dc_type",'=',118001)
               ->get()->all();
  
       $le_wh_ids = '';
       if(is_array($le_wh) && count($le_wh)>0){
           foreach($le_wh as $lewh)   
           {
               $le_wh_ids .= $lewh->le_wh_id.',';
           }
           $le_wh_ids=trim($le_wh_ids, ',');
       }
       return $le_wh_ids;
    }  
    

 public function checkMobileNumber($mobile_no){

      $salesotp = DB::selectFromWriteConnection(DB::raw("select otp as otp_number from users where mobile_no =".$mobile_no));
      if(count($salesotp)>0){
        return $salesotp[0];
      }else{
        $temp = DB::selectFromWriteConnection(DB::raw("select otp as otp_number from user_temp where mobile_no =".$mobile_no));
        if(count($temp)>0){
          return $temp[0];
        }else{
          return '';
        }

      }

        /*$result_users = DB::table('users')
                   ->select(db::raw('otp as otp_number'))
                   ->where('mobile_no',$mobile_no)
                   ->first();
        
        if(is_object($result_users))
         {

          return $result_users;
         } else{
          
          $result_temp = DB::table('user_temp')
                   ->select(db::raw('otp as otp_number'))
                   ->where('mobile_no',$mobile_no)
                   ->first();
           if(is_object($result_temp))    
           {

            return $result_temp;
           }else{
            return '';
           }

         }*/

     }

public function getMaterDescription($value){
    try {
      if(!empty($value)){
        $mast_desc = DB::table('master_lookup')->where('value',$value)->where('is_active',1)->select('description')->first();
        $mast_desc = isset($mast_desc->description) ? $mast_desc->description : '';
        return $mast_desc;
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }     





  public function getAllstockists($userId){
      try{
      // $data = DB::table('legal_entities')
      //           ->select('legal_entities.legal_entity_id','legal_entities.business_legal_name','users.user_id')
      //           ->join('users','legal_entities.legal_entity_id','=','users.legal_entity_id')
      //           ->where('legal_entities.legal_entity_type_id',1014)
      //           ->groupBy('legal_entities.legal_entity_id')
      //           ->get()->all();

      //$query = DB::selectFromWriteConnection(DB::raw("CALL getStockistList()")); 
        $query = $this->businessTreeData($userId);
        return $query;
      }catch(Exception $e){
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }  
  }


   public function businessTreeData($userId){
        try{
            $objBusinessinventory = new Inventory();
            $allBusinessUnits = $objBusinessinventory->allBusinessUnits($userId);
            $allBusinessUnits = json_decode($allBusinessUnits,true);

            $finalArr = array();
            $parentWiseArr = array();
            $tempArray=array();
            
            foreach($allBusinessUnits as $key=>$businessData){
                if($businessData['parent_bu_id'] == 0){
                    //$parentWiseArr[$businessData['bu_id']]=array($businessData['bu_id']=>$businessData['bu_name']);
                    $data = $objBusinessinventory->getLeWhByBu($businessData['bu_id']);
                    if($data>0){
                        if(!in_array($businessData['bu_id'], $tempArray)){
                            $tempArray[]=$businessData['bu_id'];             
                            $parentWiseArr[$businessData['bu_id']]=array($businessData['bu_id']=>$businessData['bu_name']);
                        }
                    }
                    unset($allBusinessUnits[$key]);
                    $child = $this->getNextBusinessChild($businessData['bu_id'], $allBusinessUnits,2);
                    $parentWiseArr=array_merge($parentWiseArr,$child);
                    
                }else{
                    $data = $objBusinessinventory->getLeWhByBu($businessData['bu_id']);
                    if($data>0){
                        if(!in_array($businessData['bu_id'], $tempArray)){
                            $tempArray[]=$businessData['bu_id'];             
                            $parentWiseArr[$businessData['bu_id']]=array($businessData['bu_id']=>$businessData['bu_name']);
                        }
                    }
                    unset($allBusinessUnits[$key]);
                    $child = $this->getNextBusinessChild($businessData['bu_id'], $allBusinessUnits,2);
                    
                    $parentWiseArr=array_merge($parentWiseArr,$child);
                }
            }
           //$parentWiseArr=array_unique($parentWiseArr, SORT_REGULAR);
           $parentWiseArr = array_map("unserialize", array_unique(array_map("serialize", $parentWiseArr)));
           $parentWiseArr=array_values($parentWiseArr);   
            return $parentWiseArr;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }
    public function getNextBusinessChild($catId, $businessArr,$level){

        $collectChild = array();

        $temp = array();
        if(!empty($businessArr)){
            foreach($businessArr as $key=>$value){
                if($value['parent_bu_id']==$catId){
                    unset($temp);
                    $temp = array();
                    $objBusinessinventory = new Inventory();
                    $data = $objBusinessinventory->getLeWhByBu($value['bu_id']);
                    if($data>0){
                        if(!in_array($value['bu_id'], $temp)){
                            
                         $temp[] =array($value['bu_id']=>$value['bu_name']);
                        }
                    }
                    unset($businessArr[$key]);
                    $child = $this->getNextBusinessChild($value['bu_id'], $businessArr,$level+1);
                    if(count($child)>0){
                      $temp=array_merge($temp,$child);
                    }
                    $collectChild = array_merge($collectChild,$temp); 
                }
            } 
        }
        else{
            return $collectChild;
        }
        return $collectChild;
    }
  public function getParentLeIdFromFFId($id){
      $parent = DB::table('users')
                ->select('legal_entity_id')
                ->where('user_id',$id)
                ->first();

      if(isset($parent->legal_entity_id) and !empty($parent->legal_entity_id))
        return $parent->legal_entity_id;
      return '';
  }
  public function getParentIdFromLegalEntity($legal_entity_id){
    $parent= DB::table('legal_entities')
                ->select('parent_le_id')
                ->where('legal_entity_id',$legal_entity_id)
                ->first();

      if(isset($parent->parent_le_id) and !empty($parent->parent_le_id))
        return $parent->parent_le_id;
      return '';
  }
  public function getWarehouseIdByMobileNo($mobile_no){
      $warehouse_id=DB::select(DB::raw("select d.dc_id FROM retailer_flat r 
                    JOIN pjp_pincode_area p ON r.`beat_id`=p.`pjp_pincode_area_id` 
                    JOIN dc_hub_mapping d ON d.`hub_id`=p.`le_wh_id`
                    WHERE r.`mobile_no`=".$mobile_no));
      if(count($warehouse_id)>0){
        return $warehouse_id[0]->dc_id;
      }
      return '';
  }

  public function getBrandsManufacturerProductGroupByUser($userid){
    try {
            $finalarray=array();
            //$brandObj = json_decode($this->rolem->getFilterData(7,$userid), 1); 
            //$manufObj = json_decode($this->rolem->getFilterData(11,$userid), 1);
            $product_grp=$this->rolem->getProductGroups();
            $brandObj = DB::table('brands')
                            ->groupBy('brands.brand_id')
                            ->select('brands.brand_name', 'brands.brand_id','brands.mfg_id')
                            ->get()->all();
                            
            $manufObj = DB::table('legal_entities')
                            ->select('business_legal_name', 'legal_entity_id')
                            ->where(['legal_entity_type_id' => 1006])
                            ->groupBy('legal_entity_id')
                            ->get()->all();
                            
            return array('brands' => $brandObj, 'manufacturer' => $manufObj, 'product_group' => $product_grp);
         } catch (\Exception $e) {
        return json_encode(['status' => "failed", 'message' => "Error", "full_message" => $e->getMessage()]);
      }   
  }
  public function getLeFromBeat($beat){
    $le_id=DB::select(DB::raw("select l.`legal_entity_id` FROM pjp_pincode_area p inner JOIN legalentity_warehouses l ON p.`le_wh_id` = l.le_wh_id WHERE p.pjp_pincode_area_id=".$beat));
      if(count($le_id)>0){
        return $le_id[0]->legal_entity_id;
      }
      return 0;
  }
  public function getWhFromBeat($beat){
    $le_id=DB::select(DB::raw("select d.dc_id FROM pjp_pincode_area p INNER JOIN dc_hub_mapping d ON p.`le_wh_id` = d.hub_id WHERE p.pjp_pincode_area_id=".$beat));
      if(count($le_id)>0){
        return $le_id[0]->dc_id;
      }
      return 0;
  }
  public function getWarehouseFromLeId($id){
      $parent = DB::table('legalentity_warehouses')
               ->select('le_wh_id')
               ->where('legal_entity_id',$id)
               ->where('dc_type',118001)
               ->first();

      if(isset($parent->le_wh_id) and !empty($parent->le_wh_id))
        return $parent->le_wh_id;
      return '';
  }
public function wikiLinkUrl($feature_code){
      if($feature_code != ""){
        $feature_code = trim($feature_code);
        $wikidata = DB::table("features")->select(["wiki_url","wiki_description"])->where("feature_code",'=',$feature_code)->first();
        return isset($wikidata->wiki_url)?$wikidata:[];
    }else{
        return [];
    }
  }


  public function getCustleid($mobile_no){
    $result=DB::table('retailer_flat')
                ->select('legal_entity_id')
                ->where('mobile_no',$mobile_no)
                ->first();
    if(isset($result->legal_entity_id) and !empty($result->legal_entity_id))
        return $result->legal_entity_id;
    return '';
  }

  public function getOrderCount($cust_le_id){
    $result=DB::table('gds_orders')
                ->select(DB::raw('count(gds_order_id) as count'))
                ->where('cust_le_id',$cust_le_id)
                ->get()->all();
    return $result[0]->count;
  }

  public function updateUser($cust_le_id){
    $result=DB::table('users')
                ->where('legal_entity_id',$cust_le_id)
                ->update(['is_active' =>0]);
    return $result;                  
  }
  
  public function updateRetailer($cust_le_id){
    $result=DB::table('retailer_flat')
                ->where('legal_entity_id',$cust_le_id)
                ->update(['is_active' =>0]);
    return $result;                  
  }

  public function deleteRetailer($cust_le_id){
    $result=DB::selectFromWriteConnection(DB::raw("CALL get_DeleteRetailerByLeID($cust_le_id)"));
    return $result[0]->Is_Deleted;
  }

}
