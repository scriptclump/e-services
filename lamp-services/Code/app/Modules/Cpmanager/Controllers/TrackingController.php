<?php
namespace App\Modules\Cpmanager\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Modules\Cpmanager\Models\TrackingModel;
use App\Modules\Cpmanager\Models\RegistrationModel;
use App\Modules\Cpmanager\Models\CategoryModel;
use Response;
use Input;
class TrackingController extends BaseController {	
    
    public function __construct() {

         $this->tracking = new TrackingModel(); 
          $this->_reg = new RegistrationModel(); 
        $this->_category = new CategoryModel();

      }
		 
    /*
    * Class Name: index
    * Description: 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 24 June 2016
    * Modified Date & Reason:
    */
    function index(){
  
      if(isset($_POST['data'])){

        $json =$_POST['data'];
        $data = json_decode($json,true);
        //$tracking_id=$data['tracking_id'];

         
        //Get cuteomer Token Count
        if(isset($data['customer_token'])){
					
                $customer_token = $data['customer_token'];

                $checkCount = $this->tracking->checkCustomerToken($customer_token);

                if($checkCount=="1"){
                        $customer_token = $data['customer_token'];

                        $customerId = $this->tracking->getCustomerId($customer_token);
                        }else{
                        $error = "Invalid customer_token";
                        print_r(json_encode(array('status'=>"failed",'message'=>$error, 'data'=>[])));die;


                }
                }else{

                $error = "customer_token not passed";
                print_r(json_encode(array('status'=>"failed",'message'=>$error, 'data'=>[])));die;

        }
        
          //Get tracking_id  Count
          if(isset($data['tracking_id'])){

            $checkCount = $this->tracking->checkTrackingId($data['tracking_id']);

            if($checkCount!=="0"){
               
               $tracking_id=$data['tracking_id'];

            }else{
           
            $error = "Invalid Tracking ID";
             print_r(json_encode(array('status'=>"failed",'message'=>$error, 'data'=>[])));die;


            }
            
            
            }else{

           $error = "Tracking ID not passed";
            print_r(json_encode(array('status'=>"failed",'message'=>$error, 'data'=>[])));die;
					
                }


            }else{

              $error = "Please Pass required parameters";
              print_r(json_encode(array('status'=>"failed",'message'=>$error, 'data'=>[])));die;


            }
    
         
            
            if(!empty($data['tracking_id'])){
                
            $TrackingData = array();
            $TrackingData['trackingData'] = $this->tracking->getTrackingData($tracking_id);
            
            //print_r($TrackingData['trackingData']);
         
             print_r(json_encode(array('status'=>"success",'message'=>"getTrackingData", 'data'=>$TrackingData)));die;
            }else{
           $error = "docket_no not passed";
            print_r(json_encode(array('status'=>"failed",'message'=>$error, 'data'=>[])));die;
            }
     
    }  
    
    
     /*
    * Class Name: CheckPincode
    * Description:Check the pincodes in serviceable areas.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 19th July 2016
    * Modified Date & Reason: 
     
    */

    public function CheckPincode(){
          
    try{
         if(isset($_POST['data'])){

                $json =$_POST['data'];
                $data = json_decode($json,true);
                
                 $pincode= $data['pincode'];           
                 // Added 2nd args to get Legal Entity Id
                 $CheckPincode = $this->_reg->getWarehouseid($pincode,true);

             if(empty($CheckPincode))	{

                    $data = "false";                                  
            		    return Array('status' =>'failed', 'message' =>'Not Serviceable', 'data' => json_decode(json_encode($data)));
          		}
          		else {                                   
                     $data['serviceable'] ="true";            
                     //$data['le_wh_id'] =array($CheckPincode);
                     $data['le_wh_id'] = $CheckPincode['le_wh_id'];
                     // From now, we send the new legal entity Id as for cross validation
                     $data['legal_entity_id'] = $CheckPincode['legal_entity_id'];
                      
                     return Array('status' =>'success', 'message' =>'Serviceable', 'data' => json_decode(json_encode($data)));

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


  public function getFFGeoData() {

        try {

            $data = Input::all();
            $arr = isset($data['data'])?json_decode($data['data']):array();

            if (isset($arr->token) && !empty($arr->token)) {

              $checkToken = $this->_category->checkCustomerToken($arr->token);

              if ($checkToken > 0) {

                if (isset($arr->ff_manager_id) && $arr->ff_manager_id>0) {

                  $Response = $this->tracking->getFFGeoData($arr->ff_manager_id);

                  $Response = json_decode(json_encode($Response),true);

                  if(!empty($Response)) {

                    return Response::json(array('status' => 'success', 'message' => '', 'data' => $Response));
                  } else {

                    return Response::json(array('status' => 'success', 'message' => 'Data Not Found', 'data' => []));
                  }

                } else {
                  return Response::json(array('status' => 'failed', 'message' => 'FF Manager Id is missing', 'data' => []));
                }

              } else {

                return Response::json(array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
              }


            } else {
                return Response::json(array('status' => "failed", 'message' => "Pass  token", 'data' => []));
            }
        } catch (Exception $e) {

            return Response::json(array('status' => "failed", 'message' => "Internal server error", 'data' => []));
        }
  }
}

