<?php

namespace App\Modules\MFC\Controllers;
use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Log;
use URL;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\RegistrationModel;
use App\Modules\Cpmanager\Models\ContainerapiModel;
use App\Modules\MFC\Models\MFCRegistrationModel;
date_default_timezone_set('Asia/Kolkata');

/*
    * Class Name: MFCRegistrationController
    * Description: Writing initially for Registration process for BFIL. Writing in BFIL Building :P
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2018
    * Version: v1.0
    * Created Date: 17 August 2018
    * Modified Date & Reason:
    */
class MFCRegistrationController extends BaseController {    

    public function __construct() {
         $this->register = new RegistrationModel(); 
         $this->mfcRegister = new MFCRegistrationModel(); 
      }
      
    /*
    * Function Name: registration()
    * Description: Add registration function to
      store the retiler data into users and legal entitiy and 
      legal documents table.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2018
    * Version: v1.0
    * Created Date: 17 August 2018
    * Modified Date & Reason:
    */

    public function registration() {

      try {

        //$this->_containerapi = new ContainerapiModel();
        //$this->_containerapi->logApiRequests($request);

        $json =$_POST['data'];

        $MFC_Response = json_decode($json);

        $Business_Legal_Name  = isset($MFC_Response->business_legal_name) ? $MFC_Response->business_legal_name : ''; 
        $Telephone            = isset($MFC_Response->telephone) ? trim($MFC_Response->telephone) : '';
        $First_Name           = isset($MFC_Response->firstname) ? $MFC_Response->firstname : ''; 
        $Last_Name            = isset($MFC_Response->lastname) ? $MFC_Response->lastname : ''; 
        $ARN_Number           = ''; 
        $Address_1            = isset($MFC_Response->address1) ? $MFC_Response->address1 : ''; 
        $Address_2            = isset($MFC_Response->address2) ? $MFC_Response->address2 : ''; 
        $City                 = isset($MFC_Response->city) ? $MFC_Response->city : ''; 
        $Ip_Address           = isset($MFC_Response->ip_address) ? $MFC_Response->ip_address : ''; 
        $Device_Id            = isset($MFC_Response->device_id) ? $MFC_Response->device_id : '';
        $Pincode              = isset($MFC_Response->pincode) ? $MFC_Response->pincode : ''; 
        $Latitude             = isset($MFC_Response->latitude) ? $MFC_Response->latitude : ''; 
        $Longitude            = isset($MFC_Response->longitude) ? $MFC_Response->longitude : '';
        $Email_Id             = isset($MFC_Response->email_id) ? $MFC_Response->email_id : ''; 
        $Segment_Id           = isset($MFC_Response->segment_id) ? $MFC_Response->segment_id : 48001; 
        $Pref_Value           = '9am-6pm'; 
        $Pref_Value_2         = '9am-6pm'; 
        $Business_Start_Time  = '9:00:00'; 
        $Business_End_Time    = '9:00:00'; 
        $State_Id             = isset($MFC_Response->state_id) ? $MFC_Response->state_id : ''; 
        $Volume_Class         = '96001'; 
        $No_Of_Shutters       = 1; 
        $License_Type         = ''; 
        $Master_Manf          = isset($MFC_Response->master_manf) ? $MFC_Response->master_manf : '106001'; 
        $Smartphone           = 1; 
        $Network              = 1; 
        $Locality             = isset($MFC_Response->locality) ? $MFC_Response->locality : ''; 
        $Landmark             = isset($MFC_Response->landmark) ? $MFC_Response->landmark : ''; 
        $Aadhar_id            = isset($MFC_Response->aadhar_id) ? $MFC_Response->aadhar_id : ''; 
        $Credit_Limit         = isset($MFC_Response->credit_limit) ? $MFC_Response->credit_limit : '';
        $MFC                  = isset($MFC_Response->mfc) ? $MFC_Response->mfc : '';
        // here we get code from MFC and we get legal_entity_id aginst code
        $MFC                  = $this->mfcRegister->getLeIdByCode($MFC);
        $Tin_Number           = '';
        $Image_1              = '';     
        $Image_2              = '';     
        $Download_Token       = '';     
        $Sales_Token          = '';     
        $Contact_Name1        = '';     
        $Contact_Name2        = '';     
        $Contact_No1        = '';     
        $Contact_No2        = '';     
        $Area                 = '';     
        $Beat                 = '';     
        $Customer_Type        = 3017; // new customer group as BFIL  
        $GSTIN                = '';     

        $Error_MSG            = '';

        if($Business_Legal_Name=='') {
          $Error_MSG.='Business Legal Name is required ';
        }
        if($Telephone=='') {
          $Error_MSG.='Mobile number is required ';
        }
        if($First_Name=='') {
          $Error_MSG.='First Name is required ';
        }
        if($Address_1=='') {
          $Error_MSG.='Address is required ';
        }
        if($City=='') {
          $Error_MSG.='City is required ';
        }
        if($Pincode=='') {
          $Pincode.='Pincode is required ';
        }
        if($State_Id=='') {
          $Error_MSG.='State is required ';
        }
        if($Aadhar_id=='') {
          $Error_MSG.='Aadhar Number is required ';
        }
        if($Credit_Limit=='') {
          $Error_MSG.='Credit Limit is required ';
        }
        if($MFC=='') {
          $Error_MSG.='MFC is required ';
        }

        if($Error_MSG=='') {

          $User_Exist = $this->mfcRegister->getCustomerByAadhar($Aadhar_id);     
          $Is_New_User = 0;

          if($User_Exist) {
            
            //User exist so update only some information

            $User_Id     = $User_Exist->user_id;            

            $updateArray = array('legal_entity_id'=>    $User_Exist->legal_entity_id,
                                 'address1'       =>    $Address_1,
                                 'address2'       =>    $Address_2,
                                 'latitude'       =>    $Latitude,
                                 'longitude'      =>    $Longitude,
                                 'credit_limit'   =>    $Credit_Limit,
                                 'mfc_id'         =>    $MFC
                                );

            $this->mfcRegister->updateCustomerDetails($User_Id,$updateArray);     

          } else {

            //User doesn't exit so creating new retailer

            $cityStateInfo = $this->mfcRegister->getCityStateByPincode($Pincode);

            if(empty($cityStateInfo)) {

              return Response::json(array('status' => 'failed', 'data' => '','message'=>'Pincode is not serviceable'));

            }

            $State_Id = $cityStateInfo->zone_id;
            $City     = $cityStateInfo->city;


            $beatData = $this->mfcRegister->getPjpAreaByPincode($Pincode);


            if(!empty($beatData)) {
                $Beat = $beatData[0]->pjp_pincode_area_id;
            } else {
                $Beat = 0;
            }


            $result     =   $this->register->address($Business_Legal_Name,$Segment_Id,$Tin_Number,$Address_1,$Address_2,$Locality,$Landmark,$City,$Pincode,$First_Name,$Email_Id,$Telephone,$Image_1,$Image_2,$Latitude,$Longitude,$Download_Token,$Ip_Address,$Device_Id,$Pref_Value,$Pref_Value_2,$Business_Start_Time,$Business_End_Time,$State_Id,$No_Of_Shutters,$Volume_Class,$License_Type,$Sales_Token,$Contact_No1,$Contact_No2,$Contact_Name1,$Contact_Name2,$Area,$Master_Manf,$Smartphone,$Network,$Last_Name,$Beat,$Customer_Type,$GSTIN,$ARN_Number,$Aadhar_id,$Credit_Limit,$MFC);

            $Is_New_User = 1;

            $User_Exist = $this->mfcRegister->getCustomerByAadhar($Aadhar_id);
          }


          $OTPData = $this->register->otpConfirm($User_Exist->mobile_no,$User_Exist->otp,$User_Exist->legal_entity_id,'','','','','');
          $beat_id = $this->mfcRegister->getBeatIdByLeID($User_Exist->legal_entity_id);
          $hub=$this->register->getHub($beat_id);
          if($OTPData['data']['segment_id'] == ""){
            $OTPData['data']['segment_id'] = $Segment_Id;
          }  
          if(!empty($OTPData)) {
            $OTPData['data']['business_legal_name'] = $Business_Legal_Name;
            $OTPData['data']['no_of_shutters']      = $No_Of_Shutters;
            $OTPData['data']['address1']            = $Address_1;
            $OTPData['data']['address2']            = $Address_2;
            $OTPData['data']['manf_ids']            = $Master_Manf;
            $OTPData['data']['volume_class']        = $Volume_Class;
            $OTPData['data']['telephone']           = $Telephone;
            $OTPData['data']['service_check']       = 1;
            $OTPData['data']['is_new_user']         = $Is_New_User;
            $OTPData['data']['aadhar_id']           = $Aadhar_id;
            $OTPData['data']['credit_limit']        = $Credit_Limit;
            $OTPData['data']['beat_id']             = $beat_id;
            $OTPData['data']['hub']                 = $hub;
            $OTPData['data']['mfc']                 = $MFC;

          } else {

            return Response::json(array('status' => 'failed', 'data' => '', 'message'=>'Something went wrong at EDPL:Confirm OTP'));
          }  

          
          return Response::json(array('status' => 'success', 'data' => $OTPData, 'message'=>''));

        } else {
          return Response::json(array('status' => 'failed', 'data' => '','message'=>$Error_MSG));
        }


    }catch (Exception $e) {
        
        return Response::json(array('status' => 'failed', 'data' => '', 'message'=>'Something went wrong at EDPL'));

    }    

  }
}
?>