<?php
  namespace App\Modules\Cpmanager\Controllers;
  
  use Log;
  use DB;
  use Illuminate\Http\Request;
  use App\Http\Controllers\BaseController;
  use App\Modules\Cpmanager\Models\CategoryModel;
  use App\Modules\Roles\Models\Role;
  use App\Modules\Cpmanager\Models\AttendanceModel;
  use App\Modules\Cpmanager\Models\MasterLookupModel;
  use App\Central\Repositories\RoleRepo;

  
  
 class AttendanceController extends BaseController {
    
    public function __construct() 
    {  
      
      $this->_role = new Role();      
      $this->categoryModel=new CategoryModel();
      $this->_attendance=new AttendanceModel();
      $this->lookup = new MasterLookupModel();
      $this->_roleRepo = new RoleRepo();
     }


    /*
    * Function name: getFeedbackReasons
    * Description: Used to get feedback reasons based on feedbackgroupid
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 11th nov 2016
    * Modified Date & Reason:
    */
  
  public function getHubUsers() {
      
    try{

       if (isset($_POST['data'])) 
       { 
        $data = $_POST['data'];                   
        $array = json_decode($data); 

        // Added Legal Entity Id as we deal with mulitple stockicts
        if(!isset($array->legal_entity_id) or (isset($array->legal_entity_id) && $array->legal_entity_id == ''))
          return json_encode(Array('status' => 'failed', 'message' =>'Legal Entity Id is not sent', 'data' => []));

        if(!isset($array->dcid) or (isset($array->dcid) && $array->dcid == ''))
          return json_encode(Array('status' => 'failed', 'message' =>'DC Id is not sent', 'data' => []));
        
       if(isset($array->token) && $array->token!='') {
       if(isset($array->user_id) && $array->user_id!='') { 
         if(isset($array->date) && $array->date!='') { 

          $valToken = $this->categoryModel->checkCustomerToken($array->token);

          if($valToken>0) { 

           /*  $team=$this->_role->getTeamByUser($array->user_id);
              if(($key = array_search($array->user_id,$team)) !== false) 
                         {

                          unset($team[$key]);

                         } */
             $currentminRoles=array();
             $currentRoles = $this->_roleRepo->getMyRoles($array->user_id); 
             $currentminRoles[]=min($currentRoles);

             if(!empty($currentRoles)){
              $getsubrole=$this->_roleRepo->getSubroles($currentminRoles,$array->user_id);
             }
             //print_r($getsubrole);exit;
             $getsubrole=(object) $getsubrole;

             $userlegalid = $this->_roleRepo->getMyLegalentityId($array->user_id);
            
             $reportinglegalid = $this->_roleRepo->getMyLegalentityIdofReporting($userlegalid); 
          //print_r($reportinglegalid);exit;
             $reportinglegalid=(object) $reportinglegalid;
             $leid=$this->getlegalidbasedondcid($array->dcid);
             //print_r($leid);exit;
             $leid=json_decode(json_encode($leid->legal_entity_id), True);
             $ignoreuserid='';
             $team=$this->_role->getSuppliersByUser($array->user_id,$array->legal_entity_id,$array->dcid,$getsubrole,$reportinglegalid,$ignoreuserid); 
             //print_r($team);exit;
             $result=$this->_attendance->getUserData($team,$array->date);

             if(!empty($result))
             {

            return json_encode(Array('status'=>'success','message'=>"getHubUsers",'data'=>$result));
            }else{
             
            return json_encode(Array('status'=>'success','message'=>"No data",'data'=>[]));

            } 

            }else{
             return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          }
           }else{
             return json_encode(Array('status' => 'failed', 'message' =>'Date is not sent', 'data' => []));    
          }
           
           }else{
             return json_encode(Array('status' => 'failed', 'message' =>'User id is not sent', 'data' => []));    
          }

        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'token is not sent', 'data' => []));
       }
        }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

    }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' =>"Internal server error", 'data' =>[]);
      } 

  }

  
  public function saveAttendance() {
      
    try{

       if (isset($_POST['data'])) 
       { 
        $data = $_POST['data'];                   
        $array = json_decode($data); 

       if(isset($array->token) && $array->token!='') {
       if(isset($array->user_id) && $array->user_id!='') { 
        if(isset($array->date) && $array->date!='') { 
          if(isset($array->attendance_data) && $array->attendance_data!='') { 

          $valToken = $this->categoryModel->checkCustomerToken($array->token);

          if($valToken>0) { 

             $result=$this->_attendance->saveAttendances($array);

             if(!empty($result))
             {

            return json_encode(Array('status'=>'success','message'=>"saveAttendance",'data'=>[]));
            }else{
             
            return json_encode(Array('status'=>'success','message'=>"No data",'data'=>[]));

            } 

            }else{
             return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          }
             
           }else{
             return json_encode(Array('status' => 'failed', 'message' =>'attendance_data is not sent', 'data' => []));    
          }
           }else{
             return json_encode(Array('status' => 'failed', 'message' =>'Date is not sent', 'data' => []));    
          }
            } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Userid is not sent', 'data' => []));
       }

        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'token is not sent', 'data' => []));
       }
     }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

    }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' =>"Internal server error", 'data' =>[]);
      } 
}

  /**
    * [getVehicleIdsByUserId    description]
    * @return [json] []
    */
    public function getVehicleIdsByUserId(){
        try{
            if(!isset($_POST['data']) or empty($_POST['data']))
                return Array('status' => "failed", 'message' =>"Invalid Data Format", 'data' =>[]);

            $data = json_decode($_POST['data'],true);

            if(!isset($data['user_id']) or empty($data['user_id']))
                return Array('status' => "failed", 'message' =>"Invalid user details", 'data' =>[]);
            
            $token = isset($data['admin_token'])?$data['admin_token']:'';
            if($token == '')
                return Array('status' => "failed", 'message' =>"Empty Customer Token", 'data' =>[]);

            $checkToken = $this->categoryModel->checkCustomerToken($token);
            
            if($checkToken <= 0)
                return Array('status' => "session", 'message' => "Your Session Has Expired. Please Login Again.", 'data' => []);
            
            $result['hub_vehicles']=$this->_attendance->getVehicleIdsByUserIdModel($data['user_id']);

            return ['status' => 'success','data'=>$result];

        }catch(Exception $e)
        {
            return Array('status' => "failed", 'message' =>"Internal server error", 'data' =>[]);
        } 
    } 
  /**
    * [saveVehicleAttendance    description]
    * @return [json] []
    */
    public function saveVehicleAttendance(){
        try{
        	
        	if(!isset($_POST['data']) or empty($_POST['data']))
				return Array('status' => "failed", 'message' =>"Invalid Data Format", 'data' =>[]);

			$data = json_decode($_POST['data'],true);

			if(!isset($data['user_id']) or empty($data['user_id']))
				return Array('status' => "failed", 'message' =>"Invalid user details", 'data' =>[]);
			
			if(!isset($data['date']) or empty($data['date']))
				return Array('status' => "failed", 'message' =>"Invalid Attendance Date", 'data' =>[]);

			if(!isset($data['attendance_data']) or empty($data['attendance_data']))
				return Array('status' => "failed", 'message' =>"Invalid Attendance Data", 'data' =>[]);

			$token = $this->categoryModel->checkCustomerToken(isset($data['token'])?$data['token']:'');
			if($token <= 0)
				return Array('status' => "session", 'message' => "Your Session Has Expired. Please Login Again.", 'data' => []);
			
			$result=$this->_attendance->saveVehicleAttendances($data);

			return Array('status' => "success", 'message' =>"Attendance Saved!", 'data' =>$result);

		}catch(Exception $e)
		{
			return Array('status' => "failed", 'message' =>"Internal server error", 'data' =>[]);
		} 
    }

    /**
    * [saveTemporaryVehicleAttendance    description]
    * @return [json] []
    */
    public function saveTemporaryVehicle(){
        try{
            
            if(!isset($_POST['data']) or empty($_POST['data']))
                return Array('status' => "failed", 'message' =>"Invalid Data Format", 'data' =>[]);

            $data = json_decode($_POST['data'],true);

            $token = $this->categoryModel->checkCustomerToken(isset($data['token'])?$data['token']:'');
            if($token <= 0)
                return Array('status' => "session", 'message' => "Your Session Has Expired. Please Login Again.", 'data' => []);

            if(!isset($data['user_id']) or empty($data['user_id']))
                return Array('status' => "failed", 'message' =>"Invalid user details");
            
            if(!isset($data['vehicle_type']) or empty($data['vehicle_type']))
                return Array('status' => "failed", 'message' => "Vehicle Type is required");

            if(!isset($data['vehicle_reg_no']) or empty($data['vehicle_reg_no']))
                return Array('status' => "failed", 'message' => "Vehicle Reg No is required");

            if(!isset($data['replace_with']) or empty($data['replace_with']))
                $data['replace_with'] = NULL;

            $result=$this->_attendance->saveTemporaryVehicleData($data);

            return Array('status' => $result['status'], 'message' => $result['message']);

        }catch(Exception $e)
        {
            return Array('status' => "failed", 'message' =>"Internal server error");
        } 
    }

    public function getlegalidbasedondcid($dcid){
        $legalid = DB::table('legalentity_warehouses')
                    ->select('legal_entity_id')
                    ->where('le_wh_id', $dcid)
                    ->first();
        return $legalid;
    } 
}