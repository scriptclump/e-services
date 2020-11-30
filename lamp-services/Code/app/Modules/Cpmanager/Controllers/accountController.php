<?php //
    /*
        * File name: accountController.php
        * Description: accountController.php file is used to handle the requests related to user      account and give response
        * Author: Ebutor <info@ebutor.com>
        * Copyright: ebutor 2016
        * Version: v1.0
        * Created Date: 24 June 2016
        * Modified Date & Reason:
    */
    namespace App\Modules\Cpmanager\Controllers;
    use DB;
    use Config;
    use Session;
    use App\Http\Controllers\BaseController;
    use App\Modules\Cpmanager\Models\accountModel;
    use App\Modules\Cpmanager\Models\RegistrationModel;
    use Illuminate\Support\Facades\Input;
    use Lang;
    use Response;
    use Illuminate\Http\Request;
    use App\Central\Repositories\ProductRepo;
    use Log;
    use App\Modules\Cpmanager\Models\EcashModel;    
    use App\Modules\Cpmanager\Models\MasterLookupModel;
    
    class accountController extends BaseController {
        
        
        
        public function __construct() {
            $this->accountModel = new accountModel(); 
           $this->register = new RegistrationModel(); 
            $this->repo = new ProductRepo();
            $this->_ecash = new EcashModel();
            $this->master_lookup = new MasterLookupModel();
        }
        /*
            * Function name: index
            * Description: index function is used to fetch the customer Data i.e; customer name, phone number, email, address based on the customer_token passed.
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 6 July 2016
            * Modified Date & Reason:
        */
        function index(){
            
            try{
                
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }
                
                if(isset($params['customer_token']) && !empty($params['customer_token'])){
                    
                    $checkCustomerToken = $this->accountModel->checkCustomerToken($params['customer_token']);
                    if($checkCustomerToken>0){
                        $customer_token = $params['customer_token'];
                        
                        $data = $this->accountModel->getCustomerData($customer_token);
                        if(!empty($data)){
                            $status = "success";
                            $message = 'getProfile';
                            
                            }else{
                            $status = "failed";
                            $message = 'No data found';
                            $data =[];
                        }
                        
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                        
                        }else{
                        $status = "session";
                        //$message = 'Your Session Has Expired. Please Login Again.';
                        $message=Lang::get('cp_messages.InvalidCustomerToken');
                        $data=[];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                    
                    }else{
                    $status = "session";
                    //$message = 'Your Session Has Expired. Please Login Again.';
                    $message=Lang::get('cp_messages.InvalidCustomerToken');
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                    
                }
                
                
                
            }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
            }
            
        }


        /*
            * Function name: updateProfile
            * Description: updateProfile function is used to update the customer profile usong the customer_token passed.
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 6 July 2016
            * Modified Date & Reason:
        */
        public function updateProfile(){
      
            try{
                
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $status = "failed";
                    //$message = 'Required Parameters not Passed.';
                    $message=Lang::get('cp_messages.InvalidInput');
                    $data =[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }
                if(isset($params['customer_token']) && !empty($params['customer_token'])){
                    
                    $checkCustomerToken = $this->accountModel->checkCustomerToken($params['customer_token']);
                    if($checkCustomerToken>0)
                    {
                        $customer_token = $params['customer_token'];
                         $le_id=$this->accountModel->customerLegalid($customer_token);
                        if($le_id==2)
                        {
                                return Array('status' => "failed", 'message' => "Sales token Passed", 'data' => []);
              
                        }
                        
                    }else
                    {
                        $status = "session";
                        //$message = 'Your Session Has Expired. Please Login Again.';
                        $message=Lang::get('cp_messages.InvalidCustomerToken');
                        $data=[];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                    
                    }else{
                    $status = "failed";
                    //$message = 'Your Session Has Expired. Please Login Again.';
                    $message=Lang::get('cp_messages.InvalidCustomerToken');
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                    
                }
                $le_type_id= $this->accountModel->getLegalEntityTypeId($customer_token);
                if($le_type_id==0){
                    return array('status'=>"failed",'message'=>"You don't have permission to edit this retailer", 'data'=>[]);
                }
               //Profile picture update 
                if((isset($_FILES['DOC']) || isset($params['firstname']) || isset($params['lastname'])) && isset($params['flag']) && ($params['flag']==1)) {
                    
                  
                    if(isset($_FILES['DOC'])){
                        $uploadDir = url('/').'/uploads/cp/';
                        
                        $doc=$_FILES['DOC'];
                        $docType = $_FILES['DOC']['type'];      
                        $allowed = array("image/jpeg", "image/png","image/gif");
                        if(in_array($docType, $allowed)) { 
                            $filename = date("Y-m-d-H-i-s")."_".$_FILES['DOC']['name'];
                            $filepath2 = 'uploads/cp/'.$filename;
                            $filetemppath =$_FILES['DOC']['tmp_name'];
                            move_uploaded_file($_FILES['DOC']['tmp_name'], "uploads/cp/".$filename);
                            //Log::info($filepath2);
                            //move_uploaded_file($filetemppath,$uploadDir.$filename);
                            $filepath2=$this->repo->uploadToS3($filepath2,'customers',2);
                            }else{                                    
                                // $image ='';
                            $CustomerData = $this->accountModel->getCustomerData($customer_token);
                            return array('status'=>"failed",'message'=>Lang::get('cp_messages.invalidFileType'), 'data'=>$CustomerData); 
                        }
                        }else{
                        $filepath2 = $this->accountModel->getDocument($customer_token);
                        if(!empty($filepath2)){
                            //Log::info($filepath2);
                          $filepath2 = $filepath2[0]->documents;
                        }else{
                            $filepath2='';
                        }
                        if(empty($filepath2)){
                            $filepath2 = '';
                        }
                    }
         
                    if(isset($params['firstname']) || isset($params['lastname'])){
                        
                        if(!empty($params['firstname']) && (preg_match ("/^[a-zA-Z\s]+$/",$params['firstname'])) && (strlen(($params['firstname'])) < 32) && (strlen(($params['firstname'])) > 1) ){
                            $firstname = $params['firstname'];
                            }else{
                            $data = $this->accountModel->getCustomerData($customer_token);
                            //$error = "firstname not valid";
                            $error=Lang::get('cp_messages.FirstName');
                            return array('status'=>"failed",'message'=>$error, 'data'=>$data);
                            
                        }
                        if(!empty($params['lastname']) && (preg_match ("/^[a-zA-Z\s]+$/",$params['lastname'])) && (strlen(($params['lastname'])) < 32) && (strlen(($params['lastname'])) > 1) ){
                            $lastname = $params['lastname'];
                            }else{
                            $data = $this->accountModel->getCustomerData($customer_token);
                            $error=Lang::get('cp_messages.LastName');
                            return array('status'=>"failed",'message'=>$error, 'data'=>$data);
                            
                        }
                        
                        
                        
                        }else{
                        
                        
                        $firstname = $this->accountModel->getFirstname($customer_token);
                        
                        if(!empty($firstname)){
                          $firstname = $firstname[0]->firstname;
                        }else{
                            $firstname='';
                        }
                        if(empty($firstname)){
                            
                            $firstname = '';
                        }
						
						 $lastname = $this->accountModel->getLastname($customer_token);
                        
                        if(!empty($lastname)){
                          $lastname = $lastname[0]->lastname;
                        }else{
                            $lastname='';
                        }
                        if(empty($lastname)){
                            
                            $lastname = '';
                        }
                        
                        
                    }
                    
                   // $lastname=(isset($lastname) && $lastname!='')? $lastname:'';
                   
                    $CustomerData = $this->accountModel->updateProfile($customer_token, $firstname , $filepath2, $lastname);
                    if(!empty( $CustomerData))
                    {   
                    return array('status'=>"success",'message'=>'Profile updated successfully.', 'data'=>$CustomerData);
                    }else{
                        return array('status'=>"failed",'message'=>'Something went wrong. Plz contact support on - 04066006442.', 'data'=>[]);
                    }                    
                    //update the flag1
                }
                     
               //email update and sending a notification mail 
                if(isset($params['email']) && isset($params['flag']) && ($params['flag']==2)){
                   
                    if(!empty($params['email']) && (!filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) ){
                        $email = $params['email'];
                      
                        if(!empty($email)){
                             
                            $count = $this->accountModel->updateEmail($customer_token,$email);
                            if($count==1){
                                $to      = $email;
                                $subject = 'successful updation';
                                $message = 'Thanks for updating email in Ebutor.';
                                
                                $headers = 'From: support@ebutor.com' . "\r\n" .
                                'Reply-To: support@ebutor.com' . "\r\n" .
                                'X-Mailer: PHP/' . phpversion();
                                
                                mail($to, $subject, $message, $headers);
                                $result = array();
                                $result['email'] = $email;
                                
                                return array('status'=>"success",'message'=>'Email Successfully Updated', 'data'=>$result);
                                }else{
                                $error = "Failed to update Database";
                                return array('status'=>"failed",'message'=>$error, 'data'=>[]);
                                
                            }
                            
                            
                        }
                        
                        }else{
                        $data = $this->accountModel->getCustomerData($customer_token);
                        //$error = "email not valid";
                        $error=Lang::get('cp_messages.email');
                        return array('status'=>"failed",'message'=>$error, 'data'=>$data);
                        
                    }
                    
                   
                    
                }
                
               //Sending otp for updating new mobile number 
                if(isset($params['telephone'])  && isset($params['flag']) && ($params['flag']==3)){
                    if(!empty($params['telephone']) && strlen($params['telephone']) ==10 && (is_numeric($params['telephone']))){
                        
                        $allTelephone = $this->accountModel->allTelephone($params['telephone'],$params['customer_token']);
            
                        $telephone = $this->accountModel->getTelephone($customer_token);
                       
                        if($telephone == $params['telephone']){
                            
                            $data = $this->accountModel->getCustomerData($customer_token);
                            //$error = "No change in Mobile Number";
                            $error=Lang::get('cp_messages.duplicateMobile');

                            return array('status'=>"failed",'message'=>$error, 'data'=>$data);
                            }else{
                            if($allTelephone==0){
                                
                                $otp = $this->accountModel->generateOtp($customer_token,$params['telephone']);
                                $result_telephone = array();
                                $result_telephone['telephone'] = $params['telephone'];
                                $result_telephone['otp'] = $otp;
                                
                                return array('status'=>"success",'message'=>'Telephone', 'data'=>$result_telephone);
                                
                                
                                }else{
                                $CustomerData = $this->accountModel->getCustomerData($customer_token);
                                //$error = "Mobile Number already exists";
                                $error=Lang::get('cp_messages.existMobile');
                                return array('status'=>"failed",'message'=>$error, 'data'=>$CustomerData);
                                
                            }
                        }
                        
                        }else{
                        
                        
                        $CustomerData = $this->accountModel->getCustomerData($customer_token);
                        //$error = "telephone not valid";
                        $error=Lang::get('cp_messages.errorMobile');
                        return array('status'=>"failed",'message'=>$error, 'data'=>$CustomerData);
                        
                    }
                    
                }
                
                //validating mobile number
                if(isset($params['telephone']) && isset($params['otp_sent']) && isset($params['flag']) && $params['flag']==4){
                    $otp = $this->accountModel->getOtp($customer_token);
                    
                    if(!empty($params['otp_sent']) && strlen($params['otp_sent'])==6 && $params['otp_sent']== $otp[0]->otp){
                        $this->accountModel->updateTelephone($customer_token,$params['telephone']);
                        $updatedTel['telephone']= $params['telephone'];
                        return array('status'=>"success",'message'=>'Telephone number Successfully Updated.', 'data'=>$updatedTel);
                        
                        }else{
                        //$error = "Otp is not valid";
                        $error=Lang::get('cp_messages.InvalidOtp');
                        return array('status'=>"failed",'message'=>$error, 'data'=>[]);
                        
                        
                    }   
                }
                
                if(isset($params['flag']) && ($params['flag']==5)) {
                    

                    if((isset($params['business_type']) && !empty($params['business_type'])) || 
                        isset($params['company']) && !empty($params['company']) || isset($params['buyer_type'])){
                      $this->accountModel->updateBussinessType($params['business_type'], $params['company'], $params['buyer_type'], $params['customer_token']);


                    }

                    if(isset($params['postcode']) && !empty($params['postcode']) 
                        && strlen($params['postcode'])==6 &&
                         !filter_var($params['postcode'],
                          FILTER_VALIDATE_INT) === false){
                        
                        $checkPincode = $this->accountModel->serviceablePincode($params['postcode']);
                        if($checkPincode>0){
                        $postcode = $params['postcode'];
                        }else{
                        $error=Lang::get('cp_messages.Pincode');
                        return array('status'=>"failed",'message'=>$error, 'data'=>[]);}
 
                        }else{
                        $error=Lang::get('cp_messages.Pincode');
                        return array('status'=>"failed",'message'=>$error, 'data'=>[]);
                    }
                     if(isset($params['city']) && !empty($params['city'])){
                        $city= $params['city'];
                        }else{
                        $error=Lang::get('cp_messages.City');
                        return array('status'=>"failed",'message'=>$error, 'data'=>[]); 
                    }
                    //print_r($params);exit;
                    if(isset($params['state']) && !empty($params['state'])){
                        $state= $params['state'];
                        }else{

                        $error=Lang::get('cp_messages.State');
                        return array('status'=>"failed",'message'=>$error, 'data'=>[]); 
                    }

        $pref_value1=(isset($params['pref_value1']) && $params['pref_value1']!='')? $params['pref_value1']:'';
        $delivery_time=(isset($params['delivery_time']) && $params['delivery_time']!='')? $params['delivery_time']:'';
        $beat_id=(isset($params['beat_id']) && $params['beat_id']!='')? $params['beat_id']:0;
  

                    if((isset($params['internet_availability']) || isset($params['manufacturers']) || isset($params['No_of_shutters']) || isset($params['area_id']) || isset($params['volume_class'])|| isset($params['business_start_time']) || isset($params['business_end_time']) || isset($params['postcode']) || isset($params['area']) || isset($params['smartphone']) ) && isset($params['city']) || isset($params['state'])|| $beat_id){
                        $facilities=isset($params['facilities'])?$params['facilities']:0;
                        $is_icecream=isset($params['is_icecream'])?$params['is_icecream']:0;
                        $sms_notification=isset($params['sms_notification'])?$params['sms_notification']:0;
                        $is_milk=isset($params['is_milk'])?$params['is_milk']:0;
                        $is_fridge=isset($params['is_fridge'])?$params['is_fridge']:0;
                        $is_vegetables=isset($params['is_vegetables'])?$params['is_vegetables']:0;
                        $is_visicooler=isset($params['is_visicooler'])?$params['is_visicooler']:0;
                        $dist_not_serv=isset($params['dist_not_serv'])?$params['dist_not_serv']:'';
                        $is_deepfreezer=isset($params['is_deepfreezer'])?$params['is_deepfreezer']:0;
                        $is_swipe=isset($params['is_swipe'])?$params['is_swipe']:0;
                        $master_data=$this->master_lookup->getMasterLookupValues(106);
                        $master_manf=array();
                        for($i=0;$i<count($master_data);$i++){
                          $master_manf[count($master_manf)]=$master_data[$i]->value;
                        }
                        $master_manf=implode(",",$master_manf);
                        $this->accountModel->updateCustomerTable($params['internet_availability'],$master_manf,$params['No_of_shutters'],$params['area'],$params['volume_class'],$delivery_time,$pref_value1,$params['business_start_time'],$params['business_end_time'], $params['postcode'],$params['city'], $params['smartphone'], $params['customer_token'],$params['state'],$beat_id,$is_icecream,$sms_notification,$is_milk,$is_fridge,$is_vegetables,$is_visicooler,$dist_not_serv,$facilities,$is_deepfreezer,$is_swipe);
                    }



                    if(isset($params['address_1']) && !empty($params['address_1'])){
                        $address_1 = $params['address_1'];
                        }else{
                        $error=Lang::get('cp_messages.errorAddress');
                        return array('status'=>"failed",'message'=>$error, 'data'=>[]); 
                    }  
            $address_2=(isset($params['address_2']) && $params['address_2']!='')? $params['address_2']:'';
            $locality=(isset($params['locality']) && $params['locality']!='')? $params['locality']:'';    
            $landmark=(isset($params['landmark']) && $params['landmark']!='')? $params['landmark']:'';          
            $gstin=(isset($params['gstin']) && $params['gstin']!='')? $params['gstin']:'';  
            $arn_number=(isset($params['arn_number']) && $params['arn_number']!='')? $params['arn_number']:'';
  
             if(isset($params['gstin']) && !empty($params['gstin'])){

                 $gstin_no = $this->accountModel->getGstinNo($params['gstin']);
                  $user_gstin_no = $this->accountModel->getUserGstinNo($customer_token );
                    
                 if($gstin_no==0 || $params['gstin']==$user_gstin_no){
                       $gstin = $params['gstin'];
                                }else{
                     return array('status'=>"failed",'message'=>"Gstin Already Exist", 'data'=>[]);
                                
                            }
                     
                        }
                if(isset($params['arn_number']) && !empty($params['arn_number'])){

                 $arn_no = $this->accountModel->getArnNo($params['arn_number']);
                $user_arn_no = $this->accountModel->getUserArnNo($customer_token );
               
                 if($arn_no==0 || $user_arn_no==$params['arn_number']){
                       $arn_number = $params['arn_number'];
                                }else{
                     return array('status'=>"failed",'message'=>"Arn No Already Exist", 'data'=>[]);
                                
                            }
                     
                        }         

                    if((isset($params['contact_no1']) && !empty($params['contact_no1']) && (isset($params['contact_name1']) && !empty($params['contact_name1'])) )  
                        || (isset($params['contact_no2']) && !empty($params['contact_no2']) && 
                         (isset($params['contact_name2']) && !empty($params['contact_name2'])) && isset($params['user_id2']) && !empty($params['user_id2']) )){

                        if(!isset($contact_no2)){
                            $contact_no2='';
                            $contact_name2 = '';
                            $user_id2 = '';
                        }
                        if(!isset($contact_no1)){
                            $contact_no1 = '';
                            $contact_name1='';
                            $user_id1 = '';
                        }

                        if(isset($params['user_id1']) && !empty($params['user_id1'])  && isset($params['contact_no1']) && isset($params['contact_name1'])){
                         $checkTelephone = $this->accountModel->allTelephone($params['contact_no1'],$params['customer_token']); 
                         $mobile_no = $this->accountModel->getMobile($params['user_id1']);
                     
                         if($checkTelephone==0|| ($params['contact_no1']== $mobile_no)){
                            $contact_no1 = $params['contact_no1'];
                            $contact_name1 = $params['contact_name1'];
                            if(isset($params['user_id1'])){
                                $user_id1 = $params['user_id1'];
                            }
                           // $user_id1 = $params['user_id1'];
                          
                            if(isset($user_id1) && !empty($user_id1)){
                             $customerData =  $this->accountModel->updateCustomerContact($user_id1,$contact_no1,$contact_name1);   
                            }
                        }else{
                            $error=Lang::get('cp_messages.existMobile');
                        return Array('status'=>'failed','message'=>$error,'data'=>[]);
                        } 

                            
                            
                        }
                        if(isset($params['user_id2']) && !empty($params['user_id2'])&& isset($params['contact_no2']) && isset($params['contact_name2'])){
                            $checkTelephone = $this->accountModel->allTelephone($params['contact_no2'],
                                    $params['customer_token']);
                            $mobile_no = $this->accountModel->getMobile($params['user_id2']);
                        if($checkTelephone ==0|| ($params['contact_no2']== $mobile_no)){
                            $contact_no2 = $params['contact_no2'];
                            $contact_name2 = $params['contact_name2'];
                          //  $user_id2 = $params['user_id2'];
                            if(isset($params['user_id2'])){
                                $user_id2 = $params['user_id2'];
                            }

                            if(isset($user_id2) && !empty($user_id2)){
                        $customerData =  $this->accountModel->updateCustomerContact($user_id2,$contact_no2,$contact_name2);   
            }
                        }else{
                            $error=Lang::get('cp_messages.existMobile');
                        return Array('status'=>'failed','message'=>$error,'data'=>[]);
                        }

                            
                        }
 
                        if(isset($params['contact_no2']) && isset($params['user_id2'])  && empty($params['user_id2'])){
                            
                              $checkTelephone = $this->accountModel->allTelephone($params['contact_no2'],$params['customer_token']); 

                         if($checkTelephone ==0 ){
                            $contact_no1 = $params['contact_no2'];
                            $contact_name1 = $params['contact_name2'];
                            $params['user_id2'] = $this->accountModel->AddContact($params['contact_no2'], $params['contact_name2'], $params['customer_token']);
                  
                        }else{
                            $error=Lang::get('cp_messages.existMobile');
                        return Array('status'=>'failed','message'=>$error,'data'=>[]);
                        } 

                        }
                        
                               
                        if(isset($params['contact_no1']) && isset($params['user_id1'])  && empty($params['user_id1'])){
                         
                              $checkTelephone = $this->accountModel->allTelephone($params['contact_no1'],$params['customer_token']); 

                         if($checkTelephone ==0 ){
                            $contact_no1 = $params['contact_no1'];
                            $contact_name1 = $params['contact_name1'];

                           $params['user_id1']= $this->accountModel->AddContact($params['contact_no1'], $params['contact_name1'], $params['customer_token']);
                  
                        }else{
                            $error=Lang::get('cp_messages.existMobile');
                        return Array('status'=>'failed','message'=>$error,'data'=>[]);
                        } 
                            
                            //$params['user_id2'] = $this->accountModel->AddContact($params['contact_no2'], $params['contact_name2'], $params['customer_token']);
                        }


                        


                        //return array('status'=>"success",'message'=>"Contact Update", 'data'=>$customerData);
                            

                    }
                    
                   //Email duplicate validation checking
                   if(!empty($params['email']) && (!filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) ){
                      $email = $params['email']; 
                     
                        //Already exists mail id needs to skip for update
                    $email_verification=$this->accountModel->eMailcheck($customer_token);
                   if($email_verification[0]->email_id == $params['email']){
                       $CustomerData = $this->accountModel->updateAddressData($customer_token,$address_1,
                       $address_2,$locality,$landmark,$city,$postcode,$state,$gstin,$arn_number);
                            $data = $this->accountModel->getCustomerData($customer_token);
                           
                            
                            if(!empty( $CustomerData))
                            {    
                                $error=Lang::get('cp_messages.Noemailupdate');
                            return array('status'=>"success",'message'=>$error, 'data'=>$data);
                            }else{
                              return array('status'=>"failed",'message'=>'Not Updated', 'data'=>
                                []);
                           
                            }
                            }
 
                            //Email Exists check     
                            $emailchk=$this->register->getEmail($email);
                           if(isset($emailchk[0]->count) && empty($emailchk[0]->count))
                           {
                            $email=$params['email']; 
                          }else{

                            $data['message']="Email Already Exist";
                             $data['status']="failed";
                             $data['data']="";
                             $final=json_encode($data);
                             print_r($final);die;  

                          } 
   
                   }else{
                       
                     $CustomerData = $this->accountModel->updateAddressData($customer_token,$address_1,$address_2,$locality,$landmark,$city,$postcode,$state,$gstin,$arn_number);  
                       
                   }
                   
                   if(isset($params['email']) && !empty($params['email']) && (!filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) ){
                        $email = $params['email'];
                        if(!empty($email)){
  
             $this->accountModel->updateEmail($customer_token,$email);
               $CustomerData =$this->accountModel->updateAddressData($customer_token,$address_1,$address_2,$locality,$landmark,$city,$postcode,$state,$gstin,$arn_number);
                            
                            
                        }
                        
                        }
 
                    
                     $CustomerData = $this->accountModel->getCustomerData($customer_token);  
                     
                                $error=Lang::get('cp_messages.Noemailupdate');
                            return array('status'=>"success",'message'=>$error, 'data'=>$CustomerData);
                           
                }
                
                    if(isset($params['flag']) && $params['flag']==6 && isset($params['customer_token']) && !empty($params['customer_token'])){
                    if((isset($params['contact_no1']) && !empty($params['contact_no1']) && (isset($params['contact_name1']) && !empty($params['contact_name1'])) && isset($params['user_id1']) && !empty($params['user_id1']))  
                        || (isset($params['contact_no2']) && !empty($params['contact_no2']) && 
                         (isset($params['contact_name2']) && !empty($params['contact_name2'])) && isset($params['user_id2']) && !empty($params['user_id2']) )){

                        if(isset($params['contact_no1']) && isset($params['contact_name1'])){
                         $checkTelephone = $this->accountModel->allTelephone($params['contact_no1'],$params['customer_token']); 
                        
                         if($checkTelephone ==0){
                            $contact_no1 = $params['contact_no1'];
                            $contact_name1 = $params['contact_name1'];
                            $user_id1 = $params['user_id1'];
                        }else{

                        return Array('status'=>'failed','message'=>'Mobile Number already exists','data'=>[]);
                        } 

                            
                            
                        }
                        if(isset($params['contact_no2']) && isset($params['contact_name2'])){
                            $checkTelephone = $this->accountModel->allTelephone($params['contact_no2'],$params['customer_token']);
                        if($checkTelephone ==0){
                            $contact_no2 = $params['contact_no2'];
                            $contact_name2 = $params['contact_name2'];
                            $user_id2 = $params['user_id2'];
                        }else{
                            $error=Lang::get('cp_messages.existMobile');
                        return Array('status'=>'failed','message'=>$error,'data'=>[]);
                        }

                            
                        }
                        if(!isset($contact_no2)){
                            $contact_no2='';
                            $contact_name2 = '';
                            $user_id2 = '';
                        }
                        if(!isset($contact_no1)){
                            $contact_no1 = '';
                            $contact_name1='';
                            $user_id1 = '';
                        }



                        $customerData =  $this->accountModel->updateCustomerContact($user_id1,$user_id2,$contact_no1,$contact_no2,$contact_name1,$contact_name2);

						// Log::info(['Status' =>1, 'Message' => 'Success', 'Data' => $customerData]);
                        return array('status'=>"success",'message'=>"Updated successfully", 'data'=>$customerData);
                            

                    }else{
                      $error=Lang::get('cp_messages.InvalidInput');  
                     //$error = "Invalid Input";
                        return array('status'=>"failed",'message'=>$error, 'data'=>[]);   
                    }
                }
                
            }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
            }
        }
           /*
            * Function name: getShippingAddress
            * Description: getShippingAddress function is used to handle the request of getting all the shipping address of the customer based on the customer_token passed.
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 8 July 2016
            * Modified Date & Reason:
        */ 
            public function getShippingAddress(){
                try{
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $status = "failed";
                    $message = 'Required Parameters not Passed.';
                    $data =[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }
                if(isset($params['customer_token']) && !empty($params['customer_token'])){
                    
                    $checkCustomerToken = $this->accountModel->checkCustomerToken($params['customer_token']);
                    if($checkCustomerToken>0)
                    {
                        $customer_token = $params['customer_token'];
                        
                    }else
                    {
                        $status = "session";
                        //$message = 'Your Session Has Expired. Please Login Again.';
                        $message=Lang::get('cp_messages.InvalidCustomerToken');
                        $data=[];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                    
                    }else{
                    $status = "session";
                    //$message = 'Your Session Has Expired. Please Login Again.';
                    $message=Lang::get('cp_messages.InvalidCustomerToken');
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                    
                }

                if($customer_token){
                    
                    $legal_entity_id=(isset($params['legal_entity_id']) && $params['legal_entity_id']!='')? $params['legal_entity_id']:'';
                    $customer_type = (isset($params['customer_type']) && $params['customer_type']!='')? $params['customer_type']:0;
                    $customer_id=$this->accountModel->getUserIdByCustomerToken($customer_token);
                    $result['address'] = $shippingAddress = $this->accountModel->getShippingAddress($customer_token,$legal_entity_id,$customer_type);
                    $result['flag'] = 0;
                    $result['ecash_amount'] = $this->_ecash->getExistingEcash($customer_id);
                    //print_r($shippingAddress);exit;
                     return Array('status' => 'success', 'message' => 'getShippingAddress', 'data' => $result);
                }

                    
                    
                }catch (Exception $e)
                {
                    $status = "failed";
                    $message = "Internal server error";
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }
                
                
            }
            
       /*
            * Function name: saveAddress
            * Description: saveAddress function is used add address for the customer_token passed.
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 14 July 2016
            * Modified Date & Reason:
        */  
        
        public function saveAddress(){
            
           
           try{
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    return Array('status' => 'failed', 'message' => 'Required parameters missing.', 'data' => []);
                }

                if(isset($params['customer_token']) && !empty($params['customer_token'])){
                 $checkCustomerToken = $this->accountModel->checkCustomerToken($params['customer_token']);
             
                    if($checkCustomerToken>0){
                        $customer_token = $params['customer_token'];
                        
                        }else{
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);  
                    }

                }else{

                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);  
                }

                if(isset($params['flag'])&& !empty($params['flag']) && ($params['flag']==1 || $params['flag']==2) ){

                    $flag=$params['flag'];
                }else{
                   return Array('status' => 'failed', 'message' => 'Required parameters missing.', 'data' => []);   
                }


                if($flag==1){
                if(is_array($params['Details'])){
                        $Details_decode =   $params['Details'];
                    }else{
                        $Details_decode = json_decode($params['Details'],true);
                    }
                
                if(empty(sizeof($params['Details']))){
                  return Array('status' => 'failed', 'message' => 'Required parameters missing.', 'data' => []);    
                }

                for($i=0;$i<count($Details_decode);$i++)
                    {
                       $FirstName = $Details_decode[$i]['FirstName'];
                        $LastName = $Details_decode[$i]['LastName'];
                        $Address = $Details_decode[$i]['Address'];
                        $Address1 = $Details_decode[$i]['Address1'];
                         $locality = $Details_decode[0]['locality'];
                        $landmark = $Details_decode[0]['landmark'];
                        $City = $Details_decode[$i]['City'];
                        $pin = $Details_decode[$i]['pin'];
                        $state = $Details_decode[$i]['state'];
                        $country = $Details_decode[$i]['country'];
                        $addressType = $Details_decode[$i]['addressType']; 
                        $telephone = isset($Details_decode[$i]['telephone']) ? $Details_decode[$i]['telephone'] : '';


                        $email = isset($Details_decode[$i]['email']) ? $Details_decode[$i]['email'] : '';
                        
                        
                     //validations
                        $check_Address_duplicate = $this->accountModel->check_duplicate_address($Details_decode[$i],$customer_token);
                        if($check_Address_duplicate >= 1)
                        {
                            return array('status'=>"session",'message'=> 'Already this address existed','data'=>[]);  
                            
                        }                       
                    if(empty($FirstName))
                    {
                       return array('status'=>"failed",'message'=> 'FirstName not sent','data'=>[]);    
                            
                    }

                    if(empty($Address))
                    {
                        return array('status'=>"failed",'message'=> 'Address not sent','data'=>[]);  
                            
                    }

                    if(empty($City))
                    {
                        return array('status'=>"failed",'message'=> 'City not sent','data'=>[]); 
                          
                    }
                     if(!empty($telephone) && strlen($telephone)!==10){
                     return array('status'=>"failed",'message'=> 'Invalid Telephone','data'=>[]);    
                    }

                    if(empty($pin) || strlen($pin)!==6)
                    {
                        return array('status'=>"failed",'message'=> 'Invalid Pincode','data'=>[]);  
                            
                    }
                    if(empty($state))
                    {
                       return array('status'=>"failed",'message'=> 'State not sent','data'=>[]);    
                            
                    }
                    if(empty($country))
                    {
                        return array('status'=>"failed",'message'=> 'country not sent','data'=>[]);  
                            
                    }
                    if(empty($addressType))
                    {
                        return array('status'=>"failed",'message'=> 'addressType not sent','data'=>[]);  
                           
                    }
                  $addAddress = $this->accountModel->Addaddress($Details_decode,$customer_token);

                  return array('status'=>"success",'message'=> 'Addaddress','data'=>$addAddress);

                    }

                }elseif($flag==2){
                   if(is_array($params['Details'])){
                        $Details_decode =   $params['Details'];
                    }else{
                        $Details_decode = json_decode($params['Details'],true);
                    }
                
                if(empty(sizeof($params['Details']))){
                  return Array('status' => 'failed', 'message' => 'No address passed', 'data' => []);    
                }

                        $le_wh_id = $Details_decode[0]['address_id'];
                        $FirstName = $Details_decode[0]['FirstName'];
                        $LastName = $Details_decode[0]['LastName'];
                        $Address = $Details_decode[0]['Address'];
                        $Address1 = $Details_decode[0]['Address1'];
                         $locality = $Details_decode[0]['locality'];
                        $landmark = $Details_decode[0]['landmark'];
                        $City = $Details_decode[0]['City'];
                        $pin = $Details_decode[0]['pin'];
                        $state = $Details_decode[0]['state'];
                        $country = $Details_decode[0]['country'];
                        $addressType = $Details_decode[0]['addressType']; 
                        $telephone = isset($Details_decode[0]['telephone']) ? $Details_decode[0]['telephone'] : '';


                        $email = isset($Details_decode[0]['email']) ? $Details_decode[0]['email'] : '';
                        
                        
                      
                     //validations
                        $check_Address_duplicate = $this->accountModel->check_duplicate_address($Details_decode[0],$customer_token);
                        if($check_Address_duplicate >= 1)
                        {
                            return array('status'=>"failed",'message'=> 'Already this address existed','data'=>[]);  
                            
                        }                       
                    if(empty($FirstName))
                    {
                       return array('status'=>"failed",'message'=> 'FirstName not sent','data'=>[]);    
                            
                    }

                    if(empty($Address))
                    {
                        return array('status'=>"failed",'message'=> 'Address not sent','data'=>[]);  
                            
                    }

                    if(empty($City))
                    {
                        return array('status'=>"failed",'message'=> 'City not sent','data'=>[]); 
                          
                    }
                    if(!empty($telephone) && strlen($telephone)!==10){
                     return array('status'=>"failed",'message'=> 'Invalid Telephone','data'=>[]);    
                    }

                    if(empty($pin) || strlen($pin)!==6)
                    {
                        return array('status'=>"failed",'message'=> 'Invalid Pincode','data'=>[]);  
                            
                    }
                    if(!empty($pin)){
                       
                        $pincodeList = $this->accountModel->availablePin($pin);
                       
                        if($pincodeList[0]->count<1){
                         return array('status'=>"failed",'message'=> 'Service Unavailable At this pincode.','data'=>[]);     
                        }
                        
                    }
                    if(empty($state))
                    {
                       return array('status'=>"failed",'message'=> 'State not sent','data'=>[]);    
                            
                    }
                    if(empty($country))
                    {
                        return array('status'=>"failed",'message'=> 'country not sent','data'=>[]);  
                            
                    }
                    if(empty($addressType))
                    {
                        return array('status'=>"failed",'message'=> 'addressType not sent','data'=>[]);  
                           
                    }
                  $editAddress = $this->accountModel->editAddress($Details_decode,$customer_token);

                  return array('status'=>"success",'message'=> 'editAddress','data'=>$editAddress);
 
                }

           }catch (Exception $e)
                {
                    $status = "failed";
                    $message = "Internal server error";
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }

        }


        public function editAddress(){
        try{
         if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }

                if(isset($params['token']) && !empty($params['token'])){
                 $checkCustomerToken = $this->accountModel->checkCustomerToken($params['token']);
             
                    if($checkCustomerToken>0){
                        $customer_token = $params['token'];
                        
                        }else{
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);  
                    }

                }else{

                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);  
                }
            if($customer_token){
                if(is_array($params['Details'])){
                        $Details_decode =   $params['Details'];
                    }else{
                        $Details_decode = json_decode($params['Details'],true);
                    }
                
                if(empty(sizeof($params['Details']))){
                  return Array('status' => 'failed', 'message' => 'No address passed', 'data' => []);    
                }
//print_r($Details_decode);exit;
                        $le_wh_id = $Details_decode[0]['address_id'];
                        $FirstName = $Details_decode[0]['FirstName'];
                        $LastName = $Details_decode[0]['LastName'];
                        $Address = $Details_decode[0]['Address'];
                        $Address1 = $Details_decode[0]['Address1'];
                        $locality = $Details_decode[0]['locality'];
                        $landmark = $Details_decode[0]['landmark'];
                        $City = $Details_decode[0]['City'];
                        $pin = $Details_decode[0]['pin'];
                        $state = $Details_decode[0]['state'];
                        $country = $Details_decode[0]['country'];
                        $addressType = $Details_decode[0]['addressType']; 
                        $telephone = isset($Details_decode[0]['telephone']) ? $Details_decode[0]['telephone'] : '';


                        $email = isset($Details_decode[0]['email']) ? $Details_decode[0]['email'] : '';
                     //validations
                        $check_Address_duplicate = $this->accountModel->check_duplicate_address($Details_decode[0],$customer_token);
                        if($check_Address_duplicate >= 1)
                        {
                            return array('status'=>"failed",'message'=> 'Already this address existed','data'=>[]);  
                            
                        }                       
                    if(empty($FirstName))
                    {
                       return array('status'=>"failed",'message'=> 'FirstName not sent','data'=>[]);    
                            
                    }

                    if(empty($Address))
                    {
                        return array('status'=>"failed",'message'=> 'Address not sent','data'=>[]);  
                            
                    }

                    if(empty($City))
                    {
                        return array('status'=>"failed",'message'=> 'City not sent','data'=>[]); 
                          
                    }
                    if(!empty($telephone) && strlen($telephone)!==10){
                     return array('status'=>"failed",'message'=> 'Invalid Telephone','data'=>[]);    
                    }

                    if(empty($pin) || strlen($pin)!==6)
                    {
                        return array('status'=>"failed",'message'=> 'Invalid Pincode','data'=>[]);  
                            
                    }
                    if(empty($state))
                    {
                       return array('status'=>"failed",'message'=> 'State not sent','data'=>[]);    
                            
                    }
                    if(empty($country))
                    {
                        return array('status'=>"failed",'message'=> 'country not sent','data'=>[]);  
                            
                    }
                    if(empty($addressType))
                    {
                        return array('status'=>"failed",'message'=> 'addressType not sent','data'=>[]);  
                           
                    }
                  $editAddress = $this->accountModel->editAddress($Details_decode,$customer_token);

                  return array('status'=>"success",'message'=> 'editAddress','data'=>$editAddress);

                    

                }


        }catch (Exception $e)
                {
                    $status = "failed";
                    $message = "Internal server error";
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }



        }


        public function getStateCountries(){
             try{
                   if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                   return Array('status' => 'failed', 'message' => 'Required Parameters not passed', 'data' => []);
                }

                if(isset($params['flag']) && !empty($params['flag']) && ($params['flag']==1 || $params['flag']==2)){

                $flag = $params['flag'];

                }else{
                    $error=Lang::get('cp_messages.InvalidInput');  
                 return Array('status' => 'failed', 'message' => $error, 'data' => []);   
                }


                if($flag==1){
                $coutries = $this->accountModel->getCountries();
                return array('status'=>'success','message'=>'getStateCountries','data'=>$coutries);


                }elseif ($flag==2) {
                 if(isset($params['country']) && !empty($params['country'])){
                $states = $this->accountModel->getStates($params['country']);
                return array('status'=>'success','message'=>'getStateCountries','data'=>$states);
                 } else{
                    $error=Lang::get('cp_messages.Country');  
                 return Array('status' => 'failed', 'message' => $error, 'data' => []);   
                 }  
                    
                }

             }catch (Exception $e)
                {
                    $status = "failed";
                    $message = "Internal server error";
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }



        }


public function DisableContactuser() {

 $params = $_POST['data'];        
 $params= json_decode($params,true); 

 if(isset($params['customer_token']) && !empty($params['customer_token'])){

    if(isset($params['telephone']) && !empty($params['telephone'])) {

     $checkCustomerToken = $this->accountModel->checkCustomerToken($params['customer_token']);
 
        if($checkCustomerToken>0){
          
            $DisableContactuser = $this->accountModel->DisableContactuser($params['customer_token'],$params['telephone']);

                    $data['telephone']=$params['telephone'];
                 
                   if($DisableContactuser >0) {
                    return Array('status' => 'success', 'message' => 'Successfully disabled contact user', 'data' => $data);  
                    }
                    else
                    {
                       return Array('status' => 'failed', 'message' => 'Unable to disable contact user', 'data' => $data);  
                    } 

            
            }else{
            return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);  
        }
    }else{
        return Array('status' => 'failed', 'message' => 'Telephone is empty', 'data' => []);  
    }

    }else{
                    $status = "session";
                    //$message = 'Your Session Has Expired. Please Login Again.';
                    $message=Lang::get('cp_messages.InvalidCustomerToken');
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
    }



}
    public function timeslotData(){
        if(isset($_POST['data'])){
            $params = $_POST['data'];
            $params= json_decode($params,true);
            $checkCustomerToken = $this->accountModel->checkCustomerToken($params['customer_token']); 
            if($checkCustomerToken>0){
                $data = $this->accountModel->getTimeslotData();
                if($data){
                    return Array('status'=>'success','message'=>'success','data'=>$data);
                }else{
                    return Array('status'=>'failed','message'=>'failed','data'=>[]);
                }
            }else{
                return Array('status'=>'failed','message'=>'invalid token','data'=>[]);
            }        
        }

    }
    public function getRetailerData()
    {
        $data =json_decode($_POST['data'],1);
        if(isset($data['mobile_no']) && !empty($data['mobile_no'])){

            $retailer_info = $this->accountModel->updateRetailerData($data['mobile_no']);
            if(count($retailer_info)>0){
                return json_encode(array('status' =>'success' ,'message'=>'retailer info','data'=>$retailer_info ));
            }else{
                return json_encode(array('status' =>'failed' ,'message'=>'No retailer exist with this mobile no','data'=>[] ));
            }
        }else{
            return json_encode(array('status' =>'failed' ,'message'=>'Please send mobile no','data'=>[] ));
        }
    }

    public function getDataFromToken($flag,$token,$datatoget){

        return $this->accountModel->getDataFromToken($flag,$token,$datatoget); 
    }
}       