<?php
namespace App\Modules\Cpmanager\Controllers;
use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Log;
use URL;
use DB;
use Illuminate\Http\Request;
use App\Modules\Cpmanager\Models\RegistrationModel;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\ContainerapiModel;
use App\Central\Repositories\ProductRepo;
use App\Modules\Cpmanager\Models\MasterLookupModel;
date_default_timezone_set('Asia/Kolkata');
/*
    * Class Name: RegistrationApiController
    * Description: Complete Regsitration process handle by this function
        Including 3 steps (mobile,otps verification,address filling
      store the retiler data into users and legal entitiy and 
      legal documents table
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 24 June 2016
    * Modified Date & Reason:
    */
class RegistrationApiController extends BaseController {    
    public function __construct() {
         $this->register = new RegistrationModel(); 
         $this->_role = new RoleRepo(); 
         $this->categoryModel = new CategoryModel();
         $this->repo = new ProductRepo();
         $this->master_lookup=new MasterLookupModel();
      }
      
    /*
    * Function Name: registration()
    * Description: Add registration function to
      store the retiler data into users and legal entitiy and 
      legal documents table
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 24 June 2016
    * Modified Date & Reason:
    */

    public function registration()
  {
    try{

      if(isset($_POST['data'])){

         $json =$_POST['data'];

         $array = json_decode($json,true);

         $data=array();

             
        if(isset($array['flag']) && !empty($array['flag']))
        {

       $flag=$array['flag'];
    

        }else{

        $data['message']="Pass flag";
        $data['status']="failed";
        $data['data']="";
        $final=json_encode($data);
        print_r($final);die;
        }


//Registration
    if( $flag==1)
    {

    if(isset($array['telephone']) && !empty($array['telephone']))
    {
    
     if( strlen($array['telephone']) ==10 && (is_numeric($array['telephone'])))
     {
    $telephone=$array['telephone'];
    }else{
     
    $data['message']="Please send valid mobile number";
    $data['status']="failed";
    $data['data']="";
    $final=json_encode($data);
    print_r($final);die;

    }
  
    }else{

    $data['message']="Pass mobile number";
    $data['status']="failed";
    $data['data']="";
    $final=json_encode($data);
    print_r($final);die;

    }



     if(isset($array['sales_token']) && !empty($array['sales_token']))
    {
                       
        $sales_token=$array['sales_token'];

        }else{

       $sales_token='';

        }


    if(isset($array['business_type_id']) && !empty($array['business_type_id']))

    {

    $buyer_type_id=$array['business_type_id'];
    }else{
    $buyer_type_id='';

    }
        if(isset($array['app_flag']) && !empty($array['app_flag'])){
            $app_flag=$array['app_flag'];
        }else{
            $app_flag=0;
        }
        $result= $this->register->registration($telephone,$buyer_type_id,$sales_token,$app_flag);

        if($result['status'] == 1)
        {

        $data['status']="success";
        $data['message']="Registration";
        $data['data']=$result;

        $final=json_encode($data);

        print_r($final);die;

        }else{
        $data['status']="failed";
        $data['message']=$result['message'];
        $data['data']="";
        $final=json_encode($data);
        print_r($final);die;
        }

        }
        
  //Confirm Otp
        
        
        if($flag==2)
         {
            $device_id=(isset($array['device_id']) && $array['device_id']!='')?$array['device_id']:'';
            $ip_address=(isset($array['ip_address']) && $array['ip_address']!='')?$array['ip_address']:0;
            $reg_id=(isset($array['reg_id']) && $array['reg_id']!='')?$array['reg_id']:0;
            $platform_id=(isset($array['platform_id']) && $array['platform_id']!='')?$array['platform_id']:0;
            $module_id = (isset($array['module_id']) && $array['module_id'] != '') ? $array['module_id'] : 0;
           // $module_id=(isset($array['module_id']) && $array['module_id']!='')?$array['module_id']:0;
            $this->confirmOtp($array['telephone'],$array['otp'],$device_id,$ip_address,$reg_id,$platform_id,$module_id);            
        }
        
        

//Resend OTP
        if( $flag==3)
        {

        if(isset($array['telephone']))
        {
        if(!empty($array['telephone']) && strlen($array['telephone']) ==10 && (is_numeric($array['telephone'])))
        {
        $telephone=$array['telephone'];
        }else{

        $data['message']="Please send valid mobile number";
        $data['status']="failed";
        $data['data']="";
        $final=json_encode($data);
        print_r($final);die;

        }
        }else{

        $data['message']="Pass mobile number";
        $data['status']="failed";
        $data['data']="";
        $final=json_encode($data);
        print_r($final);die;

        }


        if(isset($array['customer_token']))
        {
         $customer_token=$array['customer_token'];
         $custflag=2;

        }else{
        $customer_token='';
         $custflag='';

        }
        if(isset($array['app_flag']) && !empty($array['app_flag'])){
            $app_flag=$array['app_flag'];
        }else{
            $app_flag=0;
        }
        $result=$this->resendOtp($telephone,$customer_token,$custflag,$app_flag);

        if($result['status']==1)
        {
        $data['status']="success";
        $data['message']="resendOtp";
        $data['data']=$result;
        $final=json_encode($data);
        print_r($final);die;
        }else{
        $data['status']="failed";
        $data['message']=$result['message'];
        $data['data']="";
        $final=json_encode($data);
        print_r($final);die;
        }

//add address to database
        }if($flag==4){
            
            //Validations 
   if($array['pincode']==''&& $array['city']==''&& $array['firstname']=='' 
    && $array['business_legal_name']=='' && $array['address1']=='' 
      && $array['segment_id']=='' && $array['address2']=='' )
   {

                $data['message']="Please enter mandatory fields";
                $data['status']="failed";
                $data['data']="";

                $final=json_encode($data);
                print_r($final);die;
      }
                  
            if(isset($array['telephone']))
            {
            if(!empty($array['telephone']) && strlen($array['telephone']) ==10 && (is_numeric($array['telephone'])))
            {

           $mobile_no=$array['telephone'];
            
            }else{

            $data['message']="Please send valid mobile number";
            $data['status']="failed";
            $data['data']="";
            $final=json_encode($data);
            print_r($final);die;

            }            
            
            }else{
            $data['message']="Enter telephone";
            $data['status']="failed";
            $data['data']="";
            $final=json_encode($data);
            print_r($final);die;

            }
      
            // First name
        if(isset($array['firstname'])){

  if ($array['firstname']=='' ||(strlen(($array['firstname'])) < 4) || 
        (strlen(($array['firstname'])) > 32) || 
        !(preg_match ("/^[a-zA-Z\s]+$/",$array['firstname']))) {

                    $data['message']="Please enter firstname between 4 to 32 characters";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                    //$final = "2";
                   print_r($final);die;
        
             }else{

               $firstname=$array['firstname'];
                      }
                }else{
                $data['message']="Enter first name";
                $data['status']="failed";
                $data['data']="";
                $final=json_encode($data);
                print_r($final);die;

                }


                //EmailId
                if(isset($array['email_id']))
                {
                if(!empty($array['email_id'])){
                if($array['email_id']=='' || (strlen($array['email_id']) > 96) 
                || filter_var($array['email_id'], FILTER_VALIDATE_EMAIL) === false) {

            $data['message']="Please enter mail in proper format";
            $data['status']="failed";
            $data['data']="";
            $final=json_encode($data);
            //$final = "1";
            print_r($final);die;      
                            
                            }else{

           $emailchk=$this->register->getEmail($array['email_id']);


           

          if(isset($emailchk[0]->count) && empty($emailchk[0]->count))
          {
 
           $email_id=$array['email_id'];
         }else{

           $data['message']="Email already exist";
            $data['status']="failed";
            $data['data']="";
            $final=json_encode($data);
            //$final = "1";
            print_r($final);die;  


         }
           
            }
               }else
                   {
                   

                    $email_id='';

                    }

                    }else{
                   $email_id='';

                    }
                
         //City name       
         if(isset($array['city']))
        {
  if( $array['city']==''||(strlen(($array['city'])) < 4) ||
            (strlen(($array['city'])) > 32)||
             !(preg_match ("/^[a-zA-Z\s]+$/",$array['city']))){
                    $data['message']="Please enter city between 4 to 32 characters";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                     print_r($final);die;
    }else{

      $city=$array['city'];
    }

        }else{
                $data['message']="Please enter city";
                $data['status']="failed";
                $data['data']="";
                $final=json_encode($data);
                print_r($final);die;

        }

        //Pincode 
        if(isset( $array['pincode']))
        {
        if( $array['pincode']==''||(strlen(($array['pincode'])) < 6)
                                ||(strlen(($array['pincode'])) > 6) ||
            !(is_numeric($array['pincode']))){
                    $data['message']="Please enter pincode 6 digit number";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                    print_r($final);die;

    }else{

    
           $chk_pincode=$this->register->checkPincode($array['pincode']); 

          if(isset($chk_pincode[0]->count) && empty($chk_pincode[0]->count))
          {
          
                   $data['message']="Please pass valid pincode";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                    print_r($final);die;

         }else{

           $pincode=$array['pincode'];
         }
       
        }


        }else{
                $data['message']="Please enter pincode";
                $data['status']="failed";
                $data['data']="";
                $final=json_encode($data);
                 print_r($final);die;


        }


        if(isset($array['business_legal_name']))

        {
  if( $array['business_legal_name']==''||(strlen(($array['business_legal_name'])) < 4) ||
            (strlen(($array['business_legal_name'])) > 32)||
             !(preg_match ("/^[a-zA-Z\s]+$/",$array['business_legal_name']))){
                    $data['message']="Please enter business_legal_name between 4 to 32 characters";
                    $data['status']="failed";
                     $data['data']=" ";
                    $final=json_encode($data);
                 print_r($final);die;
    }else{

    $business_legal_name=$array['business_legal_name'];
    }

        }else{
                $data['message']="Please enter shopname";
                $data['status']="failed";
                $data['data']="";
                $final=json_encode($data);
                 print_r($final);die;

        }

     if(isset($array['address1']))
     {
  if( $array['address1']=='')
      {
                    $data['message']="Please enter address";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                     print_r($final);die;
    }else{
        $address1=$array['address1'];

                        }

        }else{
                $data['message']="Please enter address1";
                $data['status']="failed";
                $data['data']="";
                $final=json_encode($data);
                 print_r($final);die;

        }
        
        
        
         if(isset($array['address2']))
        {
  
        $address2=$array['address2'];


        }else{
               $address2='';

        }
        
        //New Fileds added into 12 oct 2016
        
         if(isset($array['locality']))
        {
        $locality=$array['locality'];
        }else{
               $locality='';
        }
        
         if(isset($array['landmark']))
        {
        $landmark=$array['landmark'];
        }else{
               $landmark='';
        }
        

  //contactno_1
         if(isset($array['contact_no1']) && !empty($array['contact_no1']))
        {
            $result_no1=$this->register->checkUser($array['contact_no1']);
           
            if($result_no1>=1)
            {


                $data['message']="ContactNumber ".$array['contact_no1']."already exists";
                $data['status']="failed";
                $data['data']="";
                $final=json_encode($data);
                 print_r($final);die;
          }else{
             
            $contact_no1=$array['contact_no1'];

          }

        }else{
               $contact_no1='';

        }



  //contactno_2
         if(isset($array['contact_no2']) && !empty($array['contact_no2']))
        {
            $result_no1=$this->register->checkUser($array['contact_no2']);
           
            if($result_no1>=1)
            {
                $data['message']="ContactNumber ".$array['contact_no2']."already exists";
                $data['status']="failed";
                $data['data']="";
                $final=json_encode($data);
                 print_r($final);die;
          }else{
             
            $contact_no2=$array['contact_no2'];

          }

        }else{
               $contact_no2='';

        }

        //contact_name1 
        if(isset($array['contact_name1']) && !empty($array['contact_name1'])){
         
       
       $contact_name1=$array['contact_name1'];

 

        }else{

          $contact_name1='';
                
        }


         //contact_name2 
        if(isset($array['contact_name2']) && !empty($array['contact_name2'])){
         
       
       $contact_name2=$array['contact_name2'];

        }else{

          $contact_name2='';
                
        }


        //Segment id
        if(isset($array['segment_id'])){

        if( $array['segment_id']=='')
            {
                    $data['message']="Please choose segment id";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                   print_r($final);die;
          }else{
              $segment_id=$array['segment_id'];

          }


            }else{
                    $data['message']="Please choose businesstype";
                     $data['status']="failed";
                     $data['data']="";
                     $final=json_encode($data);
                      print_r($final);die;

            }

            //customertype
        if(isset($array['customer_type'])){

        if( $array['customer_type']=='')
            {
                    $data['message']="Please choose customer_type";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                   print_r($final);die;
          }else{
              $customer_type=$array['customer_type'];

          }


            }else{
                    $data['message']="Please enter customer type";
                     $data['status']="failed";
                     $data['data']="";
                     $final=json_encode($data);
                      print_r($final);die;

            }   

        //Tin Number
        if(isset($array['tin_number']))
        {
        
            $tin_number=$array['tin_number'];
           
            }else{

             $tin_number='';

            }

         //volume_class
        if(isset($array['volume_class']))
        {
        
            $volume_class=$array['volume_class'];
           
            }else{

             $volume_class='';

            }

         //noof_shutters
        if(isset($array['noof_shutters']))
        {
        
            $noof_shutters=$array['noof_shutters'];
           
            }else{

             $noof_shutters='';

            }

         //noof_shutters
        if(isset($array['license_type']))
        {
        
            $license_type=$array['license_type'];
           
            }else{

             $license_type='';

            }
        //Latitude
        if(isset($array['latitude']))
        {

        $latitude=$array['latitude'];

        }else{

            $data['message']="Please enter latitude";
            $data['status']="failed";
            $data['data']="";
            $final=json_encode($data);
             print_r($final);die;

            }
            
            if(isset($array['longitude']))
            {

            $longitude=$array['longitude'];

            }else{

                $data['message']="Please enter longitude";
                $data['status']="failed";
                $data['data']="";
                $final=json_encode($data);
                print_r($final);die;

            }

            if(isset($array['pref_value']))
            {

            $pref_value=$array['pref_value'];

            }else{

             $pref_value='';

            }

             if(isset($array['pref_value1']))
            {

            $pref_value1=$array['pref_value1'];

            }else{

             $pref_value1='';

            }

              if(isset($array['bstart_time']))
            {

            $bstart_time=$array['bstart_time'];

            }else{

             $bstart_time='07:00 AM';

            }

               if(isset($array['bend_time']))
            {

            $bend_time=$array['bend_time'];

            }else{

             $bend_time='21:00 PM';

            }
  
     if(isset($array['state_id']) && !empty($array['state_id']))
            {

            $state_id=$array['state_id'];

            }else{

             $state_id='';

            }


           if(isset($array['area']) && !empty($array['area']))
          {
         
          $area=$array['area'];
        
          }else{

         $area='';

          }


           if(isset($array['sales_token']) && !empty($array['sales_token']))
          {
         
         $checkSalesToken = $this->categoryModel->checkCustomerToken($array['sales_token']);
                    if($checkSalesToken>0){
                         
                  $sales_token=$array['sales_token'];
                            }else{

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }
  
        
          }else{

         $sales_token='';

          }

            if(isset($array['master_manf']) && !empty($array['master_manf']))
          {
         
          $master_manf=$array['master_manf'];
        
          }else{

                $master_data=$this->master_lookup->getMasterLookupValues(106);
                $master_manf=array();
                for($i=0;$i<count($master_data);$i++){
                  $master_manf[count($master_manf)]=$master_data[$i]->value;
                }
                $master_manf=implode(",",$master_manf);
          }

            if(isset($array['smartphone']) && !empty($array['smartphone']))
          {
         
          $smartphone=$array['smartphone'];
        
          }else{

         $smartphone='';

          }

          if(isset($array['network']) && !empty($array['network']))
          {
         
          $network=$array['network'];
        
          }else{

         $network='';

          }

          if(isset($array['lastname']) && !empty($array['lastname']))
          {
         
          $lastname=$array['lastname'];
        
          }else{

         $lastname='';

          }

$beat_id=(isset($array['beat_id']) && $array['beat_id']!='')? $array['beat_id']:0;
$gstin=(isset($array['gstin']) && $array['gstin']!='')? $array['gstin']:'';  
$arn_number=(isset($array['arn_number']) && $array['arn_number']!='')? $array['arn_number']:'';            
        //Document name
     //$uploadDir = '/uploads/cp/';
            
  if(isset($_FILES['doc_url']))
  {

        if(!empty($_FILES['doc_url']['name']))
    {

    $allowed = array("application/pdf","image/jpeg", "image/png","image/gif","image/jpg");
    if(!in_array(strtolower($_FILES['doc_url']['type']), $allowed)) {
       $res['message']="Please upload pdf/jpeg/png/gif";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                   print_r($final);die;

    }

    }


  $tin =$_FILES['doc_url'];

  $filepath1_move = date("Y-m-d-H-i-s")."_". $_FILES['doc_url']['name'];
   $filepath1 = "uploads/cp/".$filepath1_move;
   move_uploaded_file($_FILES['doc_url']['tmp_name'], "uploads/cp/".$filepath1_move);
   $filepath1=$this->repo->uploadToS3($filepath1,'customers',2);
    }else{

            $filepath1 ='';
    }
        //Profile Picture
  if(isset($_FILES['profile_picture'])){

    if(!empty($_FILES['profile_picture']['name']))
    {

    $allowed = array("image/jpeg", "image/png","image/gif","image/jpg");
    if(!in_array(strtolower($_FILES['profile_picture']['type']), $allowed)) {
       $res['message']="Please upload image jpeg/png/gif";
                    $data['status']="failed";
                    $data['data']="";
                    $final=json_encode($data);
                    print_r($final);die;

    }

    }
    
  $doc=$_FILES['profile_picture'];
  $filepath2_move = date("Y-m-d-H-i-s")."_". $_FILES['profile_picture']['name'];
  $filepath2="uploads/cp/".$filepath2_move;
  move_uploaded_file($_FILES['profile_picture']['tmp_name'], "uploads/cp/".$filepath2_move);
  $filepath2=$this->repo->uploadToS3($filepath2,'customers',2);
        }else{
        $filepath2 ='';
        }
  
       //////////////////////////////////////////////////////
         if(isset($array['device_id'])){

      $device_id = $array['device_id'];

      if(strlen($device_id) !=0 ){

       $device_id = $array['device_id'];
       }
       else{
       $error = 'Please send valid device Id';
       print_r(json_encode(array('Status'=>"failed",'Message'=> $error, 'Data'=>[])));die;
          }

      }
      else {
      $error = 'Please send device Id';
      print_r(json_encode(array('Status'=>"failed",'Message'=> $error, 'Data'=>[])));die;
         }
        if(isset($array['ip_address']) && $array['ip_address']!='') 
    {
            
            if(!filter_var($array['ip_address'], FILTER_VALIDATE_IP)) {
         $error = "IP Invalid";
         print_r(json_encode(array('Status'=>"failed",'Message'=> $error, 'Data'=>[])));
         }else{
            $ip_address =$array['ip_address'];
   $checkDeviceId = $this->register->checkDeviceId($device_id);
  
   
    if(count($checkDeviceId) == 0)
          {

           $download_token = $this->randStrGen(10);
   
 
          } else{
              
           //   $appId = $checkDeviceId[0]->appId;
               $download_token = $checkDeviceId[0]->appId;
          }
         }
                }
             else
             {
              print_r(json_encode(array('status'=>"failed",'message'=> "IP address not set", 'data'=>[])));die;
             }
          
    $request['parameters'] = $array;
    $request['apiUrl'] = 'registration';
    $this->_containerapi = new ContainerapiModel();
    $this->_containerapi->logApiRequests($request);
                   //Model 
    $facilities=isset($array['facilities'])?$array['facilities']:0;
    $is_icecream=isset($array['is_icecream'])?$array['is_icecream']:0;
    $sms_notification=isset($array['sms_notification'])?$array['sms_notification']:0;
    $is_milk=isset($array['is_milk'])?$array['is_milk']:0;
    $is_fridge=isset($array['is_fridge'])?$array['is_fridge']:0;
    $is_vegetables=isset($array['is_vegetables'])?$array['is_vegetables']:0;
    $is_visicooler=isset($array['is_visicooler'])?$array['is_visicooler']:0;
    $dist_not_serv=isset($array['dist_not_serv'])?$array['dist_not_serv']:'';
    $is_deepfreezer=isset($array['is_deepfreezer'])?$array['is_deepfreezer']:0;
    $is_swipe=isset($array['is_swipe'])?$array['is_swipe']:0;

        $result=$this->register->address($business_legal_name,
               $segment_id,$tin_number,
              $address1,
               $address2,$locality,$landmark,$city,
               $pincode,$firstname,
               $email_id,$mobile_no, $filepath1, $filepath2,$latitude
     ,$longitude,$download_token,$ip_address,$device_id,$pref_value,$pref_value1,
     $bstart_time,$bend_time,$state_id,$noof_shutters,$volume_class,$license_type,$sales_token,$contact_no1,
     $contact_no2,
     $contact_name1,$contact_name2,$area,$master_manf,$smartphone,$network,$lastname,$beat_id,$customer_type,
     $gstin,
     $arn_number,$is_icecream,$sms_notification,$is_milk,$is_fridge,$is_vegetables,$is_visicooler,$dist_not_serv,$facilities,$is_deepfreezer,$is_swipe);
           if($result['status'] == 1)
           {
              if(isset($result['customer_id'])){
                $salesTargetFeature=$this->_role->checkPermissionByFeatureCode('SALESTARGET001',$result['customer_id']);
                if($salesTargetFeature == 1){
                  $result['sales_target'] = 1;
                }else{
                  $result['sales_target'] = 0;
                }
              }else{
                  $result['sales_target'] = 0;
              }    
           $data['status']="success";
           $data['message']="Registered successfully";
           $data['data']=$result;
           $final=json_encode($data);
           print_r($final);die;
           }else{
           $data['status']="failed";
           $data['message']=$result['message'];
           $data['data']="";
           $final=json_encode($data);
           print_r($final);die;
               }
        }   
        }else{
            $error = "Please enter required parameters";
            print_r(json_encode(array('status'=>"failed",'message'=>$error,'data'=>"")));die;
        }


    }catch (Exception $e)
      {
        
          return Array('status' => "failed", 'message' => "Internal server error", 'data' => "");
      } 

  }

/*
* Function name: resendotp
* Description: Used to resend otp
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 June 2016
* Modified Date & Reason:
*/

public function resendOtp($telephone,$customer_token,$custflag,$app_flag=0)
{

try{
      $users_chk = DB::table('users')
                        ->select(DB::raw('users.*'))
                         ->where('mobile_no', '=', $telephone)
                        ->get()->all();


         if($users_chk)
          {

          $user_chkdet=json_decode(json_encode($users_chk[0]),true);

          $ff_check=$this->_role->checkPermissionByFeatureCode('EFF001',$user_chkdet['user_id']);          
          
          if($ff_check!=1)
           {

            $ff_check=$this->_role->checkPermissionByFeatureCode('MFF001',$user_chkdet['user_id']);          
          
           }
          }else{
           
           $ff_check=-1;

          }    

       
       if( $ff_check==1)
       {
           

       $otpflag=1;

     $result=$this->register->resendOtp($telephone,$otpflag,$customer_token,$custflag,$users_chk[0]->user_id,$app_flag);

       }else{

        $master = DB::table('master_lookup as ml')
        ->where("ml.value", "=", 78002)->get()->all();

        $desc= $master[0]->description;
   

      $result_users = DB::table('users as user')
                        ->select(DB::raw('user.user_id,leg.legal_entity_id,leg.is_approved,user.is_active'))
                        ->leftjoin('legal_entities as leg','leg.legal_entity_id','=','user.legal_entity_id')
                         ->where('user.mobile_no', '=', $telephone)
                         ->whereIn('leg.legal_entity_type_id',function($query) use ($desc){
                           $query->select('value')
                                  ->from('master_lookup as ml')
                                  //->where ('ml.parent_lookup_id','=',1003)
                                  ->where('ml.mas_cat_id','=',$desc)
                                  ->where ('ml.is_active','=',1);
                                 // ->where("ml.value", "=", 1001);
                         })
                         ->get()->all();

      $users_num_rows=sizeof($result_users);


      $result_user_temp = DB::table('user_temp')
                        ->select(DB::raw('user_temp.*'))
                         ->where('mobile_no', '=', $telephone)
                        ->get()->all();


      $users_temp_num_rows=sizeof($result_user_temp);


   /* if($users_num_rows>0)
    {


        $is_approved=$result_users[0]->is_approved;

      }else{

        $is_approved=0;
      }
*/

if($users_num_rows>0)
    {
        $is_active=$result_users[0]->is_active;

      }else{

        $is_active=0;
      }



   if($users_num_rows>0)
    {

        $user_id=$result_users[0]->user_id;

      }else{

        $user_id='';
      }

    if($users_num_rows==0 && empty($customer_token) && $users_temp_num_rows==0)
    {

      $otpflag=0;

     $result=$this->register->resendOtp($telephone,$otpflag,$customer_token,$custflag,$user_id,$app_flag);

    }elseif(($users_num_rows>0 &&  $is_active==1) || (!empty($customer_token) && $custflag=2) ||
     ($users_num_rows>0)){
     

       $otpflag=1;

     $result=$this->register->resendOtp($telephone,$otpflag,$customer_token,$custflag,$user_id,$app_flag);


    }else{

      $otpflag=2;
     $result=$this->register->resendOtp($telephone,$otpflag,$customer_token,$custflag,$user_id,$app_flag);


    }

  }

    return $result;
   }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }      

}
    /* end Function Here*/
        
        
   
    
   /*
    * Function Name: Generate AppID
    * Description: After Registration Process,
      with the inputs of device details ,Ipadress
     & customer Id we generate AppID 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 24 June 2016
    * Modified Date & Reason:
    */


 public function generate_Appid() { 

     //$name = Request::all();
     $det = json_decode($request['data'],true);

     //print_r($det);exit;

   
    if(isset($det['ip_address']) && $det['ip_address']!='') 
    {

          if(isset($det['customerId']) && $det['customerId']!='') 
     {

      if(isset($det['device_id'])){

      $device_id = $det['device_id'];

      if(strlen($device_id) !=0 ){

       $device_id = $det['device_id'];
       }
       else{
       $error = 'Please send valid device Id';
       print_r(json_encode(array('Status'=>"failed",'Message'=> $error, 'Data'=>[])));die;
          }

      }
      else {
      $error = 'Please send device Id';
      print_r(json_encode(array('Status'=>"failed",'Message'=> $error, 'Data'=>[])));die;
         }


  
        if(!filter_var($det['ip_address'], FILTER_VALIDATE_IP)) {
         $error = "IP Invalid";
         print_r(json_encode(array('Status'=>"failed",'Message'=> $error, 'Data'=>[])));
         }
         else{

         $checkDeviceId = $this->register->checkDeviceId($device_id);

          if(count($checkDeviceId) == 0)
          {

           $download_token = $this->randStrGen(10);
           //print_r($download_token);exit;

           $download_id  = $this->register->createDownloadtoken($det,$download_token);


           if(!empty($download_token)){
            print_r(json_encode(array('Status'=>"success",'Message'=>"generateAppId",'Data'=>$download_token )));


            }
            else{

            $error = "Failed to insert in database";
            print_r(json_encode(array('Status'=>"failed",'Message'=> $error, 'Data'=>[])));

               }

          }  
          else{
           $appId = array();
           $appId['appId'] = $checkDeviceId[0]->appId;
           print_r(json_encode(array('Status'=>"success",'Message'=>"generateAppId",'Data'=>$appId )));

                 }

             }


              }     
              else
              {
               print_r(json_encode(array('Status'=>"failed",'Message'=> "customerId not set", 'Data'=>[])));die;
              }

                }
             else
             {
              print_r(json_encode(array('Status'=>"failed",'Message'=> "IP address not set", 'Data'=>[])));die;
             }



    }

    /*
    * Function Name: Generate AppID
    * Description: After Registration Process,
      with the inputs of device details ,Ipadress
     & customer Id we generate AppID 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 24 June 2016
    * Modified Date & Reason:
    */

   public function randStrGen($len){
    $result = "";
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $charArray = str_split($chars);
    for($i = 0; $i < $len; $i++){
     $randItem = array_rand($charArray);
     $result .= "".$charArray[$randItem];
    }
    return $result;
  }
  
  
  
  
  
  /*
    * Function Name: confirm OTP
    * Description: After Registration Process,
      while login OTP confirmation functionality
      used to confirm OTP
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28 June 2016
    * Modified Date & Reason:
    */
  public function confirmOtp($telephone,$otp,$device_id,$ip_address,$reg_id,$platform_id,$module_id){ 

 try{
  
  //print_r($det);exit;

  if(isset($telephone) && $telephone!='')
  {

  if(isset($otp) && $otp!='')
  {
    
//     if($otp!='')
//    {
                
         $users=$this->register->checkOtpUser($otp,$telephone);

        


        $usertemp=$this->register->checkOtpUsertemp($otp,$telephone);

       
        
   
  if(count($users)==1 || count($usertemp)==1)
  {
                   
      $appstatus= $this->register->getStatus($telephone);
           
          
            if(isset($appstatus) && !empty($appstatus))
            {
               
                  $appstatus_legal=  $appstatus[0]->legal_entity_id;
            }else{
                $appstatus_legal= '';
                
            }
            

      $result= $this->register->otpConfirm($telephone,$otp,$appstatus_legal,$device_id,$ip_address,$reg_id,$platform_id,$module_id);
      
    $request['parameters'] = $result;
    $request['apiUrl'] = 'login';
    $this->_containerapi = new ContainerapiModel();
    $this->_containerapi->logApiRequests($request);

     if($result['status']==1)

      {
        if(isset($result['data']['customer_id'])){
          $salesTargetFeature=$this->_role->checkPermissionByFeatureCode('SALESTARGET001',$result['data']['customer_id']);
          if($salesTargetFeature == 1){
            $result['data']['sales_target'] = 1;
          }else{
            $result['data']['sales_target'] = 0;
          }
          $salesTargetFeature=$this->_role->checkPermissionByFeatureCode('MBMSU001',$result['data']['customer_id']);
          if($salesTargetFeature == 1){
            $result['data']['must_sku_list'] = 1;
          }else{
            $result['data']['must_sku_list'] = 0;
          }
        }else{
            $result['data']['sales_target'] = 0;
            $result['data']['must_sku_list'] = 0;
        }
                        //print_r($result);exit;


      print_r(json_encode(array('status'=>"success",'message'=>"confirmOtp", 'data'=>$result)));die;        
    
      }
      else{
        
      print_r(json_encode(array('status'=>"failed",'message'=>$result['message'], 'data'=>[])));die;
        
      }

     }
     else{  

          print_r(json_encode(array('status'=>"failed",'message'=>"Please Send Valid OTP", 'data'=>[])));die;
       }

  }

    
    else
  {

    print_r(json_encode(array('status'=>"failed",'message'=> "OTP is not set", 'data'=>[])));die;
  }
  


       
  }

  else
  {

    print_r(json_encode(array('status'=>"failed",'message'=> "Mobile Number is required", 'data'=>[])));die;
  }

     }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }  
      

  }
      
  
  public function getAllCustomers()
 {
  try{
    if (isset($_POST['data'])) 
     { 
            $data = $_POST['data'];                   
            $arr = json_decode($data);                  


      if(isset($arr->customer_token) && !empty($arr->customer_token))
      {

        $checkCustomerToken = $this->categoryModel->checkCustomerToken($arr->customer_token);
        if($checkCustomerToken>0)
        {
             $user_data= $this->categoryModel->getUserId($arr->customer_token); 
             $beat_id=(isset($arr->beat_id) && $arr->beat_id!='')?$arr->beat_id:'';
             $is_billed=(isset($arr->is_billed) && $arr->is_billed!='')?$arr->is_billed:'';
             $offset=(isset($arr->offset) && $arr->offset!='')?$arr->offset:'';
             $offset_limit=(isset($arr->offset_limit) && $arr->offset_limit!='')?$arr->offset_limit:'';
             $search=(isset($arr->search) && $arr->search!='')?$arr->search:'';
             $flag=(isset($arr->flag) && $arr->flag!='')?$arr->flag:'';
             $hub=(isset($arr->hub) && $arr->hub!='')?$arr->hub:'';
             $spoke=(isset($arr->spoke) && $arr->spoke!='')?$arr->spoke:'';
             $sort=(isset($arr->sort) && $arr->sort!='')?$arr->sort:'';
             
            
            $customers=$this->register->getAllCustomers($user_data[0]->user_id,$beat_id,$is_billed,$offset,$offset_limit,$search,$flag,$hub,$spoke,$sort);  
           if(!empty($customers))   
                  {
                    $res['status']="success";
                    $res['message']="getAllCustomers";
                    $res['data'] = $customers;
                    }
                   else
                    {
                    $res['status']="success";
                    $res['message']="No data";
                    $res['data'] = [];                
                    }
                     $response=json_encode($res);
                     echo $response;
             }else{

           return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
         }
  

      }else{
               
               $error="Pass customer token";
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass customer token",'data'=> [])));die;


      }
                        
        }
        else
          {
           $res['status']="failed";  
           $res['message']="Missing Input Data";
           $res['data'] = [];
          }
        
        
        }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }     
                        
      }
  

public function generateRetailerToken(){

try{
  
 if(isset($_POST['data']))
 {

  $data=$_POST['data'];


  $array=json_decode($data,true);

   $data= array();


    if(isset($array['telephone'])){

    if(!empty($array['telephone']) && strlen($array['telephone']) ==10 && (is_numeric($array['telephone']))){
    $phonenumber=$array['telephone'];

    }else{
    $data['message']="Enter telephone";
    $data['status']="failed";
    $data['data']="";
    $final=json_encode($data);
    print_r($final);die;

    }
    }else{

    $data['message']="Enter telephone";
    $data['status']="failed";
    $data['data']="";
    $final=json_encode($data);
    print_r($final);die;

    }
  
       if(isset($array['sales_token']) && !empty($array['sales_token']))
       {
        
        $checkCustomerToken = $this->categoryModel->checkCustomerToken($array['sales_token']);
        if($checkCustomerToken>0)
        {

       if(isset($array['latitude']) && !empty($array['latitude']))
      {
      $latitude = $array['latitude'];
      }
      else
      {
    
        $latitude ='';
      }

      if(isset($array['longitude']) && !empty($array['longitude']))
      {
      $longitude = $array['longitude'];
      }
      else
      {
    
      $longitude ='';

      }

     
       $result=$this->register->generateRetailerToken($phonenumber,$array['sales_token'],$latitude,$longitude); 

      if($result['status']==1)
      {
      $data['status']="success";
      $data['message']="generateRetailerToken";
      $data['data']=$result;
      $final=json_encode($data);
      print_r($final);die;

      }else{
      $data['status']="failed";
      $data['message']="Failed to generate retailer token";
      $data['data']=$result;
      $final=json_encode($data);
      print_r($final);die; 
      }
    }else{

           return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
         }   
  }else{

           return Array('status' => 'failed', 'message' =>'Please pass sales_token', 'data' => []);
             
         }              

}else{

                $error = "Please pass required parameters";
                print_r(json_encode(array('status'=>"failed",'message'=>$error,'data'=>"")));die;

}

   }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }  
}



/*
* Function name: getOtp
* Description: Used to getotp
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 06 Sep 2016
* Modified Date & Reason:
*/

    public function getOtp()
    {
      try{
        
     if(isset($_POST['data'])) {
        $data=$_POST['data'];
         $array=json_decode($data,true);
         $data= array();
         $result=$this->register->checkMobileNumber($array['telephone']);
          if(isset($array['telephone']) && !empty($array['telephone'])){
          if(isset($array['customer_token']) && !empty($array['customer_token'])){
           $checkCustomerToken = $this->categoryModel->checkCustomerToken($array['customer_token']);
        if($checkCustomerToken>0){
           $result = $this->register->getSalesOtp($array['customer_token']);
        if(!empty( $result))
        {
         return json_encode(array('status'=>"success",'message'=>"autofillotp",'data'=>[$result]));
        }else{
        return json_encode(array('status'=>"failed",'message'=>"PLease send valid mobile number",'data'=>""));
        }
        }else{
         return json_encode(array('status'=>"failed",'message'=>"Pass valid sales token",'data'=>""));
        }
    }
    if(empty($result) )
    {
     return json_encode(array('status'=>"failed",'message'=>"Pass valid Mobile Number",'data'=>""));
    }else{
      
             return json_encode(array('status'=>"success",'message'=>"autofillotp",'data'=>[$result]));
        }
         }else{         
           return json_encode(array('status'=>"failed",'message'=>"Mobilenumber is empty",'data'=>""));
         }

    }else{
                
     return json_encode(array('status'=>"failed",'message'=>"Please pass required parameters",'data'=>""));            
    }

       }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }  
    //return $result;

}


 public function updateRetailerData()
 {
  try{
      if (isset($_POST['data'])) 
       { 
            $data = $_POST['data'];                   
            $arr = json_decode($data);                  


       if (isset($arr->sales_token)) {

      if(!empty($arr->sales_token))
      {
  $checkSalesToken = $this->categoryModel->checkCustomerToken($arr->sales_token);
                    if($checkSalesToken>0){

                          //$customer_token= $arr->sales_token;
                          $user_data= $this->categoryModel->getUserId($arr->sales_token); 
                            }else{

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }
      }else{
               
               $error="Pass sales token";
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass sales_token",'data'=>"")));die;


      }
                        
        } else {

          print_r(json_encode(array('status'=>"failed",'message'=> "Pass sales_token",'data'=>"")));die;

        
      }     

       if (isset($arr->user_id)) {

      if(!empty($arr->user_id))
      {

        $user_id= $arr->user_id;
      }else{
            
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass user_id",'data'=>"")));die;


      }
                        
        } else {

          print_r(json_encode(array('status'=>"failed",'message'=> "Pass user_id",'data'=>"")));die;

        
      } 

      if (isset($arr->segment_id)) {

      if(!empty($arr->segment_id))
      {

        $segment_id= $arr->segment_id;
      }else{
            
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass segment_id",'data'=>"")));die;


      }
                        
        } else {

          
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass segment_id",'data'=>"")));die;

        
      }   

        if (isset($arr->volume_class)) {

      if(!empty($arr->volume_class))
      {

        $volume_class= $arr->volume_class;
      }else{
            
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass volume_class",'data'=>"")));die;


      }
                        
        } else {

          
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass volume_class",'data'=>"")));die;

        
      }     


        if (isset($arr->noof_shutters)) {

      if(!empty($arr->noof_shutters))
      {

        $noof_shutters= $arr->noof_shutters;
      }else{
            
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass noof_shutters",'data'=>"")));die;


      }
                        
        } else {

          
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass noof_shutters",'data'=>"")));die;

        
      }  


       if (isset($arr->master_manf)) {

      if(!empty($arr->master_manf))
      {

        $master_manf= $arr->master_manf;
      }else{
            
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass master_manf",'data'=>"")));die;


      }
                        
        } else {

          
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass master_manf",'data'=>"")));die;

        
      } 


       if (isset($arr->smartphone)) {

      
        $smartphone= $arr->smartphone;
     
                        
        } else {

          
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass smartphone ",'data'=>"")));die;

        
      }

        if (isset($arr->network)) {

      
        $network= $arr->network;
     
                        
        } else {

          
          print_r(json_encode(array('status'=>"failed",'message'=> "Pass network ",'data'=>"")));die;

        
      }

        if (isset($arr->buyer_type)) {

      
        $buyer_type= $arr->buyer_type;
     
                        
        } else {

          $buyer_type='';
        
        
      }
                  
                 $update_data=$this->register->updateRetailerData($user_id
                  ,$segment_id,$volume_class,$noof_shutters,$master_manf,$smartphone,
                  $network,$buyer_type,$user_data[0]->user_id);  
           
                                                      
                  if(!empty($update_data))   
                  {
                   
                    $res['status']="success";
                    $res['message']="Updated Successfully";
                    $res['data'] = "";
                    }
                   else
                    {
                    $res['status']="failed";
                    $res['message']="unsuccessfull";
                    $res['data'] = "";
                  
                    }
     
                  }
                  else
                    {
                     $res['status']="failed";  
                     $res['message']="Missing Input Data";
                     $res['data'] = [];
                    }
                  
        
         $response=json_encode($res);
         echo $response;

      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
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

   public function InsertFfComments()
   {
     
    try{ 
    if (isset($_POST['data']))
    {
    $json = $_POST['data'];
    $decode_data = json_decode($json, true);

     if (isset($decode_data['sales_token']) && !empty($decode_data['sales_token']))
      {
      
       $checkSalesToken = $this->categoryModel->checkCustomerToken($decode_data['sales_token']);
                   
                    if($checkSalesToken>0){

                          $sales_token = $decode_data['sales_token'];

                            }else{

                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                            }
      }
      else
      {
     
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "Pass sales_token",
        'data' => ""
      )));
      die;
      }

       if (isset($decode_data['user_id']) && !empty($decode_data['user_id']))
      {
      $user_id = $decode_data['user_id'];
      }
      else
      {
     
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "Pass user_id",
        'data' => ""
      )));
      die;
      }

      if(isset($decode_data['activity']) && !empty($decode_data['activity']))
      {
      $activity = $decode_data['activity'];
      }
      else
      {
     
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "Pass activity",
        'data' => ""
      )));
      die;
      }

       if(isset($decode_data['latitude']) && !empty($decode_data['latitude']))
      {
      $latitude = $decode_data['latitude'];
      }
      else
      {
    
        $latitude ='';
      }

      if(isset($decode_data['longitude']) && !empty($decode_data['longitude']))
      {
      $longitude = $decode_data['longitude'];
      }
      else
      {
    
      $longitude ='';
      }

       if(isset($decode_data['flag']) && $decode_data['flag']== 2) { 

          $data=$this->register->UpdateCheckoutFfComments($sales_token,$user_id,$activity,$latitude,$longitude);               
        

           }elseif(isset($decode_data['flag']) && $decode_data['flag']== 1){

           $data=$this->register->InsertNewFfComments($sales_token,$user_id,$activity,$latitude,$longitude);               
        
           } else{ 

          $data=$this->register->InsertFfComments($sales_token,$user_id,$activity,$latitude,$longitude);

          }


        
        

        if(!empty($data))
        {
           
      print_r(json_encode(array(
      'status' => "success",
      'message' => "Added Successfully",
      'data' => ""
    )));
    die;

        }else{

     print_r(json_encode(array(
      'status' => "failed",
      'message' => "Not Inserted",
      'data' => ""
    )));
    die;

        }



      }else
        {
    
    
    print_r(json_encode(array(
      'status' => "failed",
      'message' => "Please pass required parameters",
      'data' => ""
    )));
    die;
    }
      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }     

  }
    
    //auth key
    public function checkAuthentication($auth_token){
        if( $auth_token=='E446F5E53AD8835EAA4FA63511E22' ){
            return true;
        }else{
            return false;
        }
    }

    public function getAllDcByuserId(Request $request){
        $paramData = $request->input();
        // check for the header authentication
        $auth_token = $request->header('auth');

        // if authentication does not match then send a return
       /* if( !$this->checkAuthentication($auth_token) ){
            $finalResponse = array(
                'message'   => 'Invalid authentication! Call aborted',
                'status'    => 'failed',
                'code'      => '400'
            );
            return $finalResponse;
        }*/

        $data = json_decode($request->input('data'));
        $userId            =   trim($data->userId);

        if( $userId==''){
            $finalResponse = array(
                'message'   => 'Please send The UserID!',
                'status'    => 'failed',
                'code'      => '400'
            );
            return $finalResponse;
        }

        // check the feature  code and get dcs
         $response = $this->_role->getAllDcs($userId);

        $finalResponse = array(
            'message'   => 'Data Found!',
            'status'    => 'success',
            'code'      => '200',
            'data'      => $response
        );
        return $finalResponse;
    }



  public function getAllStockists(Request $request){

    $paramData = $request->input();
    $data = json_decode($request->input('data'));

    $userId   =   trim($data->userId);

        if( $userId==''){
            $finalResponse = array(
                'message'   => 'Please send The UserID!',
                'status'    => 'failed',
                'code'      => '400'
            );
            return $finalResponse;
        }

        // check the feature  code and get stockists
        $check_feature_stockist=$this->_role->checkPermissionByFeatureCode('STDRP001',$userId);          
        if($check_feature_stockist == 1){

        $response = $this->register->getAllstockists($userId);
        $finalResponse = array(
            'message'   => 'Data Found!',
            'status'    => 'success',
            'code'      => '200',
            'data'      => $response
        );
        
        }else{
          $finalResponse = array(
            'message'   => '',
            'status'    => 'success',
            'code'      => '400',
            'data'      => []
        );
        }
        return $finalResponse;

  }


  public function getBrandsManufacturerProductGroupByUser(Request $request){

    $paramData = $request->input();
    $data = json_decode($request->input('data'));

    $userId   =   trim($data->userId);

        if( $userId==''){
            $finalResponse = array(
                'message'   => 'Please send The UserID!',
                'status'    => 'failed',
                'code'      => '400'
            );
            return $finalResponse;
        }

        // check the feature  code and get stockists
        $check_feature_brands=$this->_role->checkPermissionByFeatureCode('BRDMNUPRDGRP001',$userId);          
        if($check_feature_brands == 1){

        $response = $this->register->getBrandsManufacturerProductGroupByUser($userId);
        $finalResponse = array(
            'message'   => 'Data Found!',
            'status'    => 'success',
            'code'      => '200',
            'data'      => $response
        );
        
        }else{
          $brandObj=(object)array();
           $manufObj=(object)array();
           $product_grp=(object)array();
          $finalResponse = array(
            'message'   => '',
            'status'    => 'success',
            'code'      => '400',
            'data'      => array('brands' => [], 'manufacturer' => [], 'product_group' => [])
        );
        }
        return $finalResponse;

  }

    public function wikiLinks(Request $request){
    $featureCode = $_POST['data'];
    $data = json_decode($featureCode);
    $feature_code = isset($data->feature)?$data->feature:"";
        if( $feature_code == ''){
            $finalResponse = array(
                'message'   => 'Please send Feature Code!',
                'status'    => 'failed',
                'code'      => '400'
            );
            return $finalResponse;
        }
        // check the feature  code
        $response = $this->register->wikiLinkUrl($feature_code);          
        if(!empty($response)){
          $finalResponse = array(
              'message'   => 'Data Found!',
              'status'    => 'success',
              'code'      => '200',
              'data'      => $response
          );       
        }else{
            $finalResponse = array(
              'message'   => 'Invalid',
              'status'    => 'failed',
              'code'      => '400',
              'data'      => null
          );
        }
        return $finalResponse;
  }    
  public function deleteRetailerwithnoOrder()
  {
    try {
      $data = json_decode($_POST['data'],true);
      $result=array();
      $result['status'] = "success";
      $result['data'] = [];
      if(isset($data['mobile_no'])){
        if(!empty($data['mobile_no']) && strlen($data['mobile_no']) ==10 && (is_numeric($data['mobile_no']))){
          $cust_le_id=$this->register->getCustleid($data['mobile_no']);
          $ordercount=$this->register->getOrderCount($cust_le_id);
          if(isset($ordercount) && $ordercount!=0){
            $updateretailer=$this->register->updateRetailer($cust_le_id);
            $updateusers=$this->register->updateUser($cust_le_id);
            
            if($updateretailer==$updateusers){
              if(isset($updateretailer) && !empty($updateretailer)){
                $result['message']="Deactivated successfully";
              }else{
                $result['message']="Already deactivated the retailer";
              }
            }else{
                $result['message']="Failed to deactivate the retailer";
                $result['status']="failed";
            }   

          }else{
            if (isset($cust_le_id) && $cust_le_id!=0){
              $deleteretailer=$this->register->deleteRetailer($cust_le_id);
              if($deleteretailer == True){
                $result['message']="Deleted successfully";
              }else{
                $result['message']="Failed to delete the retailer";
                $result['status']="failed";
              }
            }else{
                $result['message']="Already deleted the retailer";
            } 
          }
        }else{
          $result['message']="Please send valid mobile number";
          $result['status']="failed";
        }            
      }else{
        $result['message']="Enter mobile number";
        $result['status']="failed";
      }
      return $result;
    }catch (Exception $e){
        return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
    }           
  }
    
}
