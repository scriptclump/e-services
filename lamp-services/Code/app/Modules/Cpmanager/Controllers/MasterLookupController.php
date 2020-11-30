<?php
namespace App\Modules\Cpmanager\Controllers;
use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Log;
use URL;
use DB;
use Illuminate\Http\Request;
use App\Modules\Cpmanager\Models\MasterLookupModel;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Cpmanager\Models\PickerModel;
use App\Modules\Roles\Models\Role;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\EcashModel;
use App\Central\Repositories\RoleRepo;
use App\Modules\Cpmanager\Models\accountModel;

/*
* Class Name: MasterLookupController
* Description: To get all the segmentsd and buyertypes
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 6 July 2016
* Modified Date & Reason:
*/
class MasterLookupController extends BaseController
  {
    
  public function __construct()
    {
    $this->lookup = new MasterLookupModel();
    $this->categoryModel = new CategoryModel();
    $this->_role = new Role(); 
    $this->_picker = new PickerModel();
    $this->_ecash= new EcashModel();
    $this->_account=new accountModel();
    }

    
  /*
  * Function Name: getMasterLookup()
  * Description: Used to get Buyer types and Segments
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 6 July 2016
  * Modified Date & Reason:
  */
  public function getMasterLookup()
    {
    if(isset( $_POST['data']))  
     { 
    $json = $_POST['data'];
    $decode_data = json_decode($json, true);  
    }else{
      $decode_data ='';
    }
    $data = $this->lookup->getMasterLookup($decode_data);
    //$buyer_type = $this->lookup->getMasterLookupBuyerTypes();
    //$volume = $this->lookup->getVolumeClass();
    //$license = $this->lookup->getLicenseType();
    //$data = array();
   // $data['segments'] = $segments;
    //$data['buyer_type'] = $buyer_type;
   // $data['volume_class'] = $volume;
   // $data['license'] = $license;
    if (!empty($data))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getMasterLookup",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => $data
      ));
      }
    }


public function getPincodeAreas()
  {
  if (isset($_POST['data']))
    {
    $json = $_POST['data'];
    $decode_data = json_decode($json, true);
    if (isset($decode_data['pincode']) && !empty($decode_data['pincode']))
      {
      $pincode = $decode_data['pincode'];
      }
      else
      {
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "Please pass pincode",
        'data' => []
      )));
      die;
      }

    $data = $this->lookup->getPincodeAreas($pincode);
    if (!empty($data))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getPincodeAreas",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      }
    
  }
  else
  {
  $error = "Please pass required parameters";
  print_r(json_encode(array(
    'status' => "failed",
    'message' => $error,
    'data' => []
  )));
  die;
  }
}



/* 
* Function Name: getDashboardReport
* Description: getDashboardReport function is used to get all the reports of orders
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 16 Sep 2016
* Modified Date & Reason:
*/
public function getDashboardReport()
  {

  if (isset($_POST['data']))
    {
    $json = $_POST['data'];
    $decode_data = json_decode($json, true);

    if (isset($decode_data['sales_token']) && !empty($decode_data['sales_token']))
      {
        $checkSalesToken = $this->categoryModel->checkCustomerToken($decode_data['sales_token']);
      if($checkSalesToken>0)
      {
     
      $user_data= $this->categoryModel->getUserId($decode_data['sales_token']); 
      $user_id=$user_data[0]->user_id;
      $legal_entity_id=$user_data[0]->legal_entity_id;
     
       }else{

       return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
         }
      }
      else
      {
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "Please pass sales_token",
        'data' => []
      )));
      die;
      }
      $reportsRepo = new \App\Central\Repositories\ReportsRepo();

       if (isset($decode_data['flag']) && !empty($decode_data['flag']))
      {
      $flag = $decode_data['flag'];
      }
      else
      {
        
        $flag ='';
      }

       if (isset($decode_data['start_date']) && !empty($decode_data['start_date']))
      {
      $start_date = $decode_data['start_date'];
      }
      else
      {
        
        $start_date =date("Y-m-d");
      }

     if(isset($decode_data['end_date']) && !empty($decode_data['end_date']))
      {
      $end_date = $decode_data['end_date'];
      }
      else
      {
        
        $end_date =date("Y-m-d");
      }
      
      if(isset($decode_data['manuf_id']) && !empty($decode_data['manuf_id']) && $decode_data['manuf_id']!=0)
      {
      $manufid = $decode_data['manuf_id'];
      }
      else
      {
        
        $manufid ='NULL';
      }

      if(isset($decode_data['brand_id']) && !empty($decode_data['brand_id']) && $decode_data['brand_id']!=0)
      {
      $brandid = $decode_data['brand_id'];
      }
      else
      {
        
        $brandid ='NULL';
      }

      if(isset($decode_data['product_grup']) && !empty($decode_data['product_grup']) && $decode_data['product_grup']!=0)
      {
      $productgrup = $decode_data['product_grup'];
      }
      else
      {
        
        $productgrup ='NULL';
      }

      if(isset($decode_data['cat_id']) && !empty($decode_data['cat_id']) && $decode_data['cat_id']!=0)
      {
      $catid = $decode_data['cat_id'];
      }
      else
      {
        
        $catid ='NULL';
      }

     // $team=$this->_role->getTeamByUser($user_id); 5th oct
      // As we deal with more than 1 company, we need to filter with Legal Entity Id
      $legal_entity_id = $this->lookup->getLegalEntityIdByCustomerToken($decode_data['sales_token']);
      $dc_id = isset($decode_data['dc_id'])?$decode_data['dc_id']:"";
      if($dc_id==""){
        return Array('status' => 'failed', 'message' =>'Warehouse ID should not be empty', 'data' => []);
      }
      if($flag==1)
       {


              //  $users = implode(',',$team); 5th oct

                  $users="'".$user_id."'";
                  
                  //$result= $this->lookup->getOrdersDashboard($users,$start_date,$end_date);
                  $data = $reportsRepo->validateData($users,$flag,$start_date,$end_date,$dc_id,$brandid,$manufid,$productgrup,$catid);
                  
                  //$l=0;
//                  foreach ($result as $col => $value) {
//                   
//                   $data[$l]['key']= $col;
//                   $data[$l]['value']= $value;
//                   $l++;
//                  }
                

       }elseif($flag==2)
       {


             /* if (($key = array_search($user_id, $team)) !== false) 
                 {
                  if(count($team)>1) 5th oct
                  {
                  unset($team[$key]);*/
                 $team=$this->_role->getSuppliersByUser($user_id,$legal_entity_id,$dc_id); 
                // $team= $this->_role->getTeamByUser($user_id); 
                 $ff_ids=$this->lookup->getFieldForceIds($team,$start_date,$end_date);
               
                 $k=0;

                 foreach ($ff_ids as $key => $value1) 
                 {

                 //$result= $this->lookup->getOrdersDashboard($value1,$start_date,$end_date);
                 $proc_data = $reportsRepo->validateData($value1,$flag,$start_date,$end_date,$dc_id,$brandid,$manufid,$productgrup,$catid);
                
                // $result[$k]->name=$this->lookup->getFirstname($value1);
                // $result[$k]->user_id=$value1;
                /* if($result->order_total>0)
                 {*/
                // $ff_name

                  $data[$k]['name']= $this->lookup->getFirstname($value1);
                  $data[$k]['user_id']= $value1;

                   $n=0;
                  // $j=0;
                 // foreach ($result as $key => $value2) 
                 // {

                  // $data[$j]['value']= $this->lookup->getFirstname($value1);
                   
                  //foreach ($result as $col => $values)
//                    {
//                   $proc_data[$n]['key']= $col;
//                   $proc_data[$n]['value']= $values;
//                   $n++;
//
//                  }
                   
                 // $j++;
                 // }
                  $data[$k]['data']=$proc_data;
                

                 
               //}
               $k++;
                 }
                     
                 /* } 5th oct
 
                 }  */          
       }elseif($flag==3)
       {
             
             if(isset($decode_data['user_id']) && !empty($decode_data['user_id']))
            {
            $user_id = $decode_data['user_id'];
            }
            else
            {
              
          print_r(json_encode(array(
                'status' => "failed",
                'message' => "Please send user_id",
                'data' => []
              )));
              die;
            }

              //  $users = implode(',',$team); 5th oct

               //   $users="'".$user_id."'";
                  
                  //$result= $this->lookup->getOrdersDashboard($user_id,$start_date,$end_date);
                  $data = $reportsRepo->validateData($user_id,$flag,$start_date,$end_date,$dc_id,$brandid,$manufid,$productgrup,$catid);
                  //$l=0;
//                  foreach ($result as $col => $value) {
//                   
//                   $data[$l]['key']= $col;
//                   $data[$l]['value']= $value;
//                   $l++;
//                  }
                

       }elseif($flag==5) //Flag 5 for CNC Dashboard
       {
            $data = $reportsRepo->validateData($user_id, $flag, $start_date, $end_date,$dc_id,$brandid,$manufid,$productgrup,$catid);
       }elseif($flag==6){
          //this flag indicates consolidated data of all ff's under an field force manager,
          //added on 5th Aug 2019
          if(isset($decode_data['user_id']) && !empty($decode_data['user_id']))
              {
              $user_id = $decode_data['user_id'];
              }
              else
              {
                
            print_r(json_encode(array(
                  'status' => "failed",
                  'message' => "Please send user_id",
                  'data' => []
                )));
                die;
              }
          $data = $reportsRepo->validateData($user_id, $flag, $start_date, $end_date,$dc_id,$brandid,$manufid,$productgrup,$catid);

       }else{
         

     //$result = $this->lookup->getOrdersDashboard('0',$start_date,$end_date);
//
//                $m=0;
//                  foreach ($result as $col => $value) {
//                   
//                   $data[$m]['key']= $col;
//                   $data[$m]['value']= $value;
//                   $m++;
//                  }
           $roleRepo = new RoleRepo();
            $ff_check=$roleRepo->checkPermissionByFeatureCode('USRTGM01',$user_id);
            $flag1=1;
            if($ff_check){
                $flag1=4;
            }
            $data = $reportsRepo->validateData(0,$flag1,$start_date,$end_date,$dc_id,$brandid,$manufid,$productgrup,$catid);
       } 

    if (!empty($data))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getDashboardReport",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "No data",
        'data' => []
      ));
      }
    
  }
  else
  {
  $error = "Please pass required parameters";
  print_r(json_encode(array(
    'status' => "failed",
    'message' => $error,
    'data' => []
  )));
  die;
  }
}


/* 
* Function Name: getPincodeData
* Description: getPincodeData function is used to get the areas,its city and state
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 25 Oct 2016
* Modified Date & Reason:
*/

public function getPincodeData()
  {
   
  if (isset($_POST['data']))
    {
    $json = $_POST['data'];
    $decode_data = json_decode($json, true);
    if (isset($decode_data['pincode']) && !empty($decode_data['pincode']))
      {
      $pincode = $decode_data['pincode'];
      }
      else
      {
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "Please pass pincode",
        'data' => []
      )));
      die;
      }
   
    
    $data = $this->lookup->getPincodeAreas($pincode);
    $state = $this->lookup->getPincodeData($pincode);

    if (!empty($state))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getPincodeData",
        'data' => $data,
        'state_id' => $state['state_id'],
        'state_name' => $state['state_name']
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "No data",
        'data' => [],
        'state_id' => "",
        'state_name' => ""
      ));
      }
    
  }
  else
  {
  $error = "Please pass required parameters";
  print_r(json_encode(array(
    'status' => "failed",
    'message' => $error,
    'data' => []
  )));
  die;
  }
     
}



/* 
* Function Name: getCancelReason
* Description: getCancelReason function is used to get the cancel reason
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 12 Nov 2016
* Modified Date & Reason:
*/

public function getCancelReason()
  {

  try{  

  if (isset($_POST['data']))
    {

    $json = $_POST['data'];
    $decode_data = json_decode($json);

    if(isset($decode_data->picker_token) && $decode_data->picker_token!='') {    

    $reason = $this->lookup->getCancelReason();

    if (!empty($reason))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getCancelReason",
        'data' =>  $reason
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "No data",
        'data' => []
      ));
      }

       } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Picker token is not sent', 'data' => []));
       }
    
  }
  else
  {
  $error = "Please pass required parameters";
  print_r(json_encode(array(
    'status' => "failed",
    'message' => $error,
    'data' => []
  )));
  die;
  }
      
      }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }  
}




 /*
  * Function Name: getFieldForceList()
  * Description: Used to get fieldforce list based on its reporting managerid
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 13th Dec 2016
  * Modified Date & Reason:
  */
  public function getFieldForceList()
  {

   try{ 

   if (isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 


             if(isset($arr->sales_token) && !empty($arr->sales_token) )
             {
                  
                 $checkSalesToken = $this->categoryModel->checkCustomerToken($arr->sales_token);
                
                  if($checkSalesToken>0)
                  {
                    $flag=(isset($arr->flag) && $arr->flag!='')? $arr->flag:0;

                    if(isset($arr->user_id) && !empty($arr->user_id) )
                    {

                      $user_id=$arr->user_id;

                    }else{

                          print_r(json_encode(array('status'=>"failed",'message'=> "Please send user_id",'data'=> [])));die;

                    }

                    if($flag==1)
                    {  
                      // Here if the user has *All Beat Access* permission, then we retrieve the list irrespective of users, if the user doesnot have access then we wont
                       $roleRepo = new RoleRepo();
                       $allBeatAccess = $roleRepo->checkPermissionByFeatureCode('ALLBEAT1',$user_id);
                       $team = [];
                       if(!$allBeatAccess){
                         // If he doesnot have access, then we will filter the users list
                         // $team = $this->_role->getTeamByUser($user_id); 
                         // $team=$this->_role->getSuppliersByUser($user_id,2); 
                         // 1 is to get Active Users
                         // The below method will fetch all the users based on their reporting manager hierarchy
                        $ignorelegalentityid=1;
                         $team = $roleRepo->getUsersListBasedOnReportingManagerHierarchy($user_id,1,$ignorelegalentityid);
                         if(gettype($team) == "string") $team = explode(",", $team);
                         array_push($team, $user_id);
                       }

                      //$ff_data=$this->_picker->getUsersByRoleNameId(['Field Force Manager','Field Force Associate','Sales Agent'],$team);
                      // $ff_data=$this->_picker->getUsersByRoleCode
                       //'SSLL' removed to show only field force associates
                      $ff_data=$this->lookup->getUsersByRoleCodePermission(['SSLO','SSLA'],$team);
                        
                     
                    } elseif($flag==3){

                      if(isset($arr->bu_id) && !empty($arr->bu_id) )
                      {

                        $buid=$arr->bu_id;

                      }else{

                        print_r(json_encode(array('status'=>"failed",'message'=> "Please send bu_id",'data'=> [])));die;

                      }
                      $legal_entity_id='';
                      $dc_id='';
                      $reportsRepo = new \App\Central\Repositories\ReportsRepo();
                        $whleid=$reportsRepo->getdcidbasedonbuid($buid);
                        if(count($whleid)>0){
                          $legal_entity_id = isset($whleid->legal_entity_id)?$whleid->legal_entity_id:'';
                          $dc_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';
                        }
                        if(empty($legal_entity_id) || $legal_entity_id==NULL){
                            print_r(json_encode(array('status'=>"failed",'message'=> "Please send legalentity_id",'data'=> [])));die;
                        }
                        if(empty($legal_entity_id) || $legal_entity_id==NULL){
                            print_r(json_encode(array('status'=>"failed",'message'=> "Please send dc_id",'data'=> [])));die;
                        }
                        $roleid=$this->lookup->getRoleIdByRoleCode('SSLO');
                        $ignoreuserid=1;
                        //$team = $this->_role->getTeamByUser($user_id);  
                        $ff_ids=$this->_role->getSuppliersByUser($user_id,$legal_entity_id,$dc_id,$roleid,'',$ignoreuserid); //previous variable in place of $ff_ids is $team
                       // $ff_ids=$this->lookup->getFieldForceIds($team,$start_date="'".date("Y-m-d")."'",$end_date="'".date("Y-m-d")."'");
                      if(is_array($ff_ids) && !empty($ff_ids) && count($ff_ids)>0){
                        if(($key = array_search($user_id,$ff_ids)) !== false) 
                         {

                          unset($ff_ids[$key]);

                         } 

                        
                         $i=0;
                         foreach ($ff_ids as $key => $value) {
                         
                          $ff_data[$i]['ff_id']= $value;

                          $ff_data[$i]['name']=$this->lookup->getFirstname($value);
                         $i++;
                         }
                        }else{
                        $ff_data=[];
                       }

                    } else{ 
                     if(isset($arr->legal_entity_id) && !empty($arr->legal_entity_id) )
                    {

                      $legal_entity_id=$arr->legal_entity_id;

                    }else{

                          print_r(json_encode(array('status'=>"failed",'message'=> "Please send legal_entity_id",'data'=> [])));die;

                    }

                    if(isset($arr->dc_id) && !empty($arr->dc_id) )
                    {

                      $dc_id=$arr->dc_id;

                    }else{

                          print_r(json_encode(array('status'=>"failed",'message'=> "Please send dc_id",'data'=> [])));die;

                    }

                        $roleid=$this->lookup->getRoleIdByRoleCode('SSLO');
                        $ignoreuserid=1;
                        //$team = $this->_role->getTeamByUser($user_id);  
                        $ff_ids=$this->_role->getSuppliersByUser($user_id,$legal_entity_id,$dc_id,$roleid,'',$ignoreuserid); //previous variable in place of $ff_ids is $team
                       // $ff_ids=$this->lookup->getFieldForceIds($team,$start_date="'".date("Y-m-d")."'",$end_date="'".date("Y-m-d")."'");
                      if(is_array($ff_ids) && !empty($ff_ids) && count($ff_ids)>0){
                        if(($key = array_search($user_id,$ff_ids)) !== false) 
                         {

                          unset($ff_ids[$key]);

                         } 

                        
                         $i=0;
                         foreach ($ff_ids as $key => $value) {
                         
                          $ff_data[$i]['ff_id']= $value;

                          $ff_data[$i]['name']=$this->lookup->getFirstname($value);
                         $i++;
                         }
                        }else{
                        $ff_data=[];
                       }

           
                    } 



                           if (!empty($ff_data))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "getFieldForceList",
                                    'data' => $ff_data
                                  //  'count'=>$count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => []
                                   // 'count' =>$count
                                  ));
                               }

           
                     
                   //   $team=$this->_role->getTeamByUser($user_data[0]->user_id);
                       
                    }
                    else
                    {

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }

               }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass sales token",'data'=> [])));die;

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
       
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>  []);
      } 
    

    }
    

/* 
* Function Name: getFfBeat
* Description: getFfBeat used to beat based on ff_id
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 Dec 2016
* Modified Date & Reason:
*/

public function getFfBeat()
  {

   try{
    
  if (isset($_POST['data']))
    {
    $json = $_POST['data'];
    $decode_data = json_decode($json, true);
    if (isset($decode_data['sales_token']) && !empty($decode_data['sales_token']))
      {
      
      $checkSalesToken = $this->categoryModel->checkCustomerToken($decode_data['sales_token']);
                
      if($checkSalesToken>0)
      {
       
      if (isset($decode_data['ff_id']) && !empty($decode_data['ff_id']))
      {
    $hub_list=(isset($decode_data['hub']) && $decode_data['hub']!='')?$decode_data['hub']:'';    
    $team=$this->_role->getTeamByUser($decode_data['ff_id']);
    $data = $this->lookup->getFfBeat($team,$hub_list);
    if (!empty($data))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getFfBeat",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "No data",
        'data' => []
      ));
      }
         

      }else
      {

       return Array('status' => 'failed', 'message' =>'Please send ff_id', 'data' => []);
           
      }
      }
      else
      {

       return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
      }
      }
      else
      {
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "Please send sales token",
        'data' => []
      )));
      die;
      }
  
      }
      else
      {
      $error = "Please pass required parameters";
      print_r(json_encode(array(
        'status' => "failed",
        'message' => $error,
        'data' => []
      )));
      die;
      }
      }catch (Exception $e)
        {
            
            return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
        }  
}


 public function getOrderCashbackData() {    
        try {

            $data = Input::all();  
            $arr = isset($data['data'])?json_decode($data['data']):array();
            if (isset($arr->customer_token) && !empty($arr->customer_token)) {
             $checkAdminToken = $this->categoryModel->checkCustomerToken($arr->customer_token);
             $isSelfOrder = isset($arr->self_order) ? $arr->self_order: 0;
             $productData=isset($arr->products) ? $arr->products : array();
             $productData=json_decode(json_encode($productData),1);
             $tradeData=array();
             $rangeValue=0;
             $finalData=[];
             foreach ($productData as $key => $value) {
               foreach($value as $productId=>$productValue){
                 $rangeValue+=$productValue;
               }
             }                      
             if ($checkAdminToken > 0) {
                if(isset($arr->customer_type) && $arr->customer_type!='') {
                  if(isset($arr->le_wh_id) && $arr->le_wh_id!='') { 
                  $today="'".date('Y-m-d')."'";
                  $data=$this->_ecash->getOrderCashbackDatas($today,$rangeValue,$arr->le_wh_id,$arr->customer_type,$isSelfOrder);

                  $stateId = $this->_ecash->getState('token',$arr->customer_token);
                  if($stateId>0){
                      $resultdata = $this->calculatePromotionValue($arr->products,$stateId,$arr->le_wh_id,$arr->customer_type,$isSelfOrder);
                      if(count($resultdata) >0){
                        $tradeData[0]=$resultdata;
                        $tradeData[0]['benificiary_type']=62;
                        $tradeData[0]['applyCashback']=1;
                      }
                  }
                   if (!empty($data)) {
                      for($i=0;$i<count($data);$i++){  
                        $brandsData = $data[$i]->brand_id;
                        $brandsData=array_filter(explode(",",$brandsData)); 
                        $excl_brand_id = $data[$i]->excl_brand_id;
                        $excl_brand_id=array_filter(explode(",",$excl_brand_id));
                        $productGrpData = $data[$i]->product_group_id;
                        $productGrpData=array_filter(explode(",",$productGrpData));
                        $excl_prod_group_id = $data[$i]->excl_prod_group_id;
                        $excl_prod_group_id=array_filter(explode(",",$excl_prod_group_id)); 
                        $brandsData=array_diff($brandsData, ['null']);
                        $excl_brand_id=array_diff($excl_brand_id, ['null']);
                        $productGrpData=array_diff($productGrpData, ['null']); 
                        $excl_prod_group_id=array_diff($excl_prod_group_id, ['null']);
                        if(in_array(0,$brandsData)){
                          $productIdData=DB::table('products');
                          $productIdData=$productIdData
                                      ->where(function($query) use($excl_prod_group_id,$excl_brand_id){
                                          if(count($excl_brand_id)>0){
                                            $query->whereNotIn('brand_id',$excl_brand_id);
                                          }
                                          if(count($excl_prod_group_id)>0){
                                            $query->orwhereNotIn('product_group_id',$excl_prod_group_id);
                                          }
                                        });
                        }else{
                          $productIdData=DB::table('products')
                                          ->where(function($query) use($productGrpData,$brandsData){
                                              if(count($brandsData)>0){
                                                $query->whereIn('brand_id',$brandsData);
                                              }
                                              if(count($productGrpData)>0){
                                                $query->orwhereIn('product_group_id',$productGrpData);
                                              }
                                            })
                                          ->where(function($query) use($excl_brand_id,$excl_prod_group_id){
                                              if(count($excl_brand_id)>0){
                                                $query->whereNotIn('brand_id',$excl_brand_id);
                                              }
                                              if(count($excl_prod_group_id)>0){
                                                $query->whereNotIn('product_group_id',$excl_prod_group_id);
                                              }
                                            });
                        }
                        $productIdData=$productIdData->pluck('product_id')->all();
                       
                        if(count($productIdData)>0){
                          $productBillValue=0;
                          $TotalBillValue=0;
                          foreach ($productData as $key => $value) {
                            foreach($value as $productId=>$productValue){
                              $TotalBillValue+=$productValue;
                              if(in_array($productId, $productIdData)){
                                $productBillValue+=$productValue;
                              }else{
                                $TotalBillValue -= $productValue;
                              }
                            }                            
                          }
                          if($TotalBillValue>=$data[$i]->qty_from_range && $productBillValue>=$data[$i]->product_value){
                              $applyCashback=1;
                          }else{
                            $applyCashback=0;
                          }

                          $data[$i]->applyCashback=$applyCashback;
                          if($applyCashback==1){
                            if(count($finalData)!=0){
                              $data[$i]->cashback_applied=($TotalBillValue*$data[$i]->cbk_value)/100;
                              if($data[$i]->cashback_applied >  $data[$i]->cap_limit){
                                $data[$i]->cashback_applied= $data[$i]->cap_limit;
                              }
                              if($finalData[0]->cashback_applied<$data[$i]->cashback_applied){
                                $finalData[0]=$data[$i];                                
                              }  
                            
                            }else{
                              $finalData[0]=$data[$i];
                              $finalData[0]->cashback_applied=($TotalBillValue*$finalData[0]->cbk_value)/100;
                              if($finalData[0]->cashback_applied >  $finalData[0]->cap_limit){
                                $finalData[0]->cashback_applied= $finalData[0]->cap_limit;
                              }
                            }
                          }
                        }else{
                          $data[$i]->applyCashback=0;
                        }
                      }
                        if(count($tradeData)>0 && count($finalData)>0){
                          if($finalData[0]->cashback_applied<$tradeData[0]['cashback_applied']){
                            $finalData = $tradeData;
                          }
                        }else if(count($finalData)==0 && count($tradeData)>0){
                            $finalData = $tradeData;
                        }
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "getOrderCashbackData",
                            'data' => $finalData
                        ));
                    } else {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "No data",
                            'data' => $tradeData
                        ));
                    }

                 } else{                   
                 return json_encode(array('status' => "failed", 'message' => "please send le_wh_id", 'data' => []));
                 } 
                 } else{                   
                 return json_encode(array('status' => "failed", 'message' => "please send customer_type", 'data' => []));
                 }   
                } else {
                    return json_encode(array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                }
            } else {
               return json_encode(array('status' => "failed", 'message' => "Pass Customer token", 'data' => []));
                
            }
        } catch (Exception $e) {
       
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }


 public function getCashbackHistory() {    
        try {
           $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();
            if (isset($arr->customer_token) && !empty($arr->customer_token)) {
             $checkAdminToken = $this->categoryModel->checkCustomerToken($arr->customer_token);
             if ($checkAdminToken > 0) {
                if(isset($arr->user_id) && $arr->user_id!='') {
                 
                  $data=$this->_ecash->getCashbackHistory($arr);  
                   if (!empty($data)) {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "getCashbackHistory",
                            'data' => $data
                        ));
                    } else {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "No data",
                            'data' => []
                        ));
                    }

                 } else{                   
                 return json_encode(array('status' => "failed", 'message' => "please send customer_type", 'data' => []));
                 }   
                } else {
                    return json_encode(array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                }
            } else {
               return json_encode(array('status' => "failed", 'message' => "Pass Customer token", 'data' => []));
                
            }
        } catch (Exception $e) {
       
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }
  public function getFFBeatByPincode(){
    try{
      
      if (isset($_POST['data'])){
        $json = $_POST['data'];
        $decode_data = json_decode($json, true);
        if (isset($decode_data['sales_token']) && !empty($decode_data['sales_token'])){        
          $checkSalesToken = $this->categoryModel->checkCustomerToken($decode_data['sales_token']);                  
          if($checkSalesToken>0){         
            if (isset($decode_data['ff_id']) && !empty($decode_data['ff_id'])){
              $roleRepo = new RoleRepo();
              $globalAccess = $roleRepo->checkPermissionByFeatureCode("GLB0001",$decode_data['ff_id']);
              if(!$globalAccess){
                $hub_list=(isset($decode_data['hub']) && $decode_data['hub']!='')?$decode_data['hub']:'';    
                $team=$this->_role->getTeamByUser($decode_data['ff_id']);
                $team=implode(',',$team);
                $pincode=$decode_data['pincode'];
                $ffLegalEntity=$this->lookup->getffLegalentity($decode_data['ff_id']);
                $pincodeLegalEntity=$this->lookup->getPincodeLegalentity($pincode);
                if($ffLegalEntity!=0&&$pincodeLegalEntity!=0){
                  if($ffLegalEntity == $pincodeLegalEntity){
                    $data = $this->lookup->getFfBeatByPincodewise($team,$hub_list,$pincode);
                    if (!empty($data)){
                      return json_encode(Array(
                        'status' => "success",
                        'message' => "getFfBeat",
                        'data' => $data
                      ));
                    }
                    else{
                      return json_encode(Array(
                        'status' => "success",
                        'message' => "No data",
                        'data' => []
                      ));
                    }
                  }else{
                    return Array('status' => 'failed', 'message' =>'Incorrect location mapping with retailer. Contact Ebutor Support', 'data' => []); 
                  }
                }else if($ffLegalEntity==0 || $pincodeLegalEntity==0){
                  if($ffLegalEntity==0){
                    return Array('status' => 'failed', 'message' =>'ff legalentity is wrong', 'data' => []); 
                  }
                  else if($pincodeLegalEntity==0){
                    return Array('status' => 'failed', 'message' =>'Incorrect location mapping with retailer. Contact Ebutor Support', 'data' => []); 
                  }else{
                    return Array('status' => 'failed', 'message' =>'Incorrect location mapping with retailer. Contact Ebutor Support', 'data' => []); 
                  }
                }
              }else{
                $beatsForGlobalAccess=$this->lookup->getBeatsForGlobalAccess($decode_data['pincode']);
                return json_encode(Array(
                      'status' => "success",
                      'message' => "getFfBeat",
                      'data' => $beatsForGlobalAccess
                ));
              }
            }else
            {
              return Array('status' => 'failed', 'message' =>'Please send ff_id', 'data' => []);             
            }
          }
          else{

           return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);               
          }
        }
        else{
          print_r(json_encode(array(
            'status' => "failed",
            'message' => "Please send sales token",
            'data' => []
          )));
          die;
        }    
      }
      else{
        $error = "Please pass required parameters";
        print_r(json_encode(array(
          'status' => "failed",
          'message' => $error,
          'data' => []
        )));
        die;
      }
    }catch (Exception $e){              
      return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
    }  
  }
  public function getOrderEcashValue($productArray=[],$order_date = null,$le_wh_id,$cust_type,$is_self=0,$cust_le_id=0){
        try{
          $productData=json_decode(json_encode($productArray),1);
          $rangeValue=0;
          $finalData=[];
          $tradeData=[];
          foreach ($productData as $key => $value) {
            foreach($value as $productId=>$productValue){
              $rangeValue+=$productValue;
            }
          }
          if($order_date == null){
            $order_date="'".date('Y-m-d')."'";
          }else{
            $order_date="'".$order_date."'";
          }    
          $data=$this->_ecash->getOrderCashbackDatas($order_date,$rangeValue,$le_wh_id,$cust_type,$is_self);  
	        $stateId = $this->_ecash->getState('legal_entity_id',$cust_le_id);
          if($stateId>0){
              $resultdata = $this->calculatePromotionValue($productData,$stateId,$le_wh_id,$cust_type,$is_self);
              if(count($resultdata) >0){
                $tradeData[0]=$resultdata;
                $tradeData[0]['benificiary_type']=62;
                $tradeData[0]['applyCashback']=1;
              }
          }  
          if (!empty($data)) {
            for($i=0;$i<count($data);$i++){
              $brandsData = $data[$i]->brand_id;
              $brandsData=array_filter(explode(",",$brandsData)); 
              $excl_brand_id = $data[$i]->excl_brand_id;
              $excl_brand_id=array_filter(explode(",",$excl_brand_id));
              $productGrpData = $data[$i]->product_group_id;
              $productGrpData=array_filter(explode(",",$productGrpData));
              $excl_prod_group_id = $data[$i]->excl_prod_group_id;
              $excl_prod_group_id=array_filter(explode(",",$excl_prod_group_id)); 
              $brandsData=array_diff($brandsData, ['null']);
              $excl_brand_id=array_diff($excl_brand_id, ['null']);
              $productGrpData=array_diff($productGrpData, ['null']); 
              $excl_prod_group_id=array_diff($excl_prod_group_id, ['null']); 
              if(in_array(0,$brandsData)){
                $productIdData=DB::table('products');
                $productIdData=$productIdData
                                ->where(function($query) use($excl_prod_group_id,$excl_brand_id){
                                    if(count($excl_brand_id)>0){
                                      $query->whereNotIn('brand_id',$excl_brand_id);
                                    }
                                    if(count($excl_prod_group_id)>0){
                                      $query->orwhereNotIn('product_group_id',$excl_prod_group_id);
                                    }
                                  });
              }else{
                $productIdData=DB::table('products')
                                ->where(function($query) use($productGrpData,$brandsData){
                                              if(count($brandsData)>0){
                                                $query->whereIn('brand_id',$brandsData);
                                              }
                                              if(count($productGrpData)>0){
                                                $query->orwhereIn('product_group_id',$productGrpData);
                                              }
                                            })
                                          ->where(function($query) use($excl_brand_id,$excl_prod_group_id){
                                              if(count($excl_brand_id)>0){
                                                $query->whereNotIn('brand_id',$excl_brand_id);
                                              }
                                              if(count($excl_prod_group_id)>0){
                                                $query->whereNotIn('product_group_id',$excl_prod_group_id);
                                              }
                                            });
              }
              $productIdData=$productIdData->pluck('product_id')->all();
             
              if(count($productIdData)>0){
                $productBillValue=0;
                $TotalBillValue=0;
                foreach ($productData as $key => $value) {
                  foreach($value as $productId=>$productValue){
                    $TotalBillValue+=$productValue;
                    if(in_array($productId, $productIdData)){
                      $productBillValue+=$productValue;
                    }else{
                      $TotalBillValue -= $productValue;
                    }
                  }                            
                }
                if($TotalBillValue>=$data[$i]->qty_from_range && $productBillValue>=$data[$i]->product_value){
                    $applyCashback=1;
                }else{
                  $applyCashback=0;
                }

                $data[$i]->applyCashback=$applyCashback;
                if($applyCashback==1){
                  if(count($finalData)!=0){
                    $data[$i]->cashback_applied=($TotalBillValue*$data[$i]->cbk_value)/100;
                    if($data[$i]->cashback_applied >  $data[$i]->cap_limit){
                      $data[$i]->cashback_applied= $data[$i]->cap_limit;
                    }
                    /*if($finalData[0]->cbk_value<$data[$i]->cbk_value){
                      $finalData[0]=$data[$i];
                      $finalData[0]->cashback_applied=($TotalBillValue*$finalData[0]->cbk_value)/100;
                      if($finalData[0]->cashback_applied >  $finalData[0]->cap_limit){
                        $finalData[0]->cashback_applied= $finalData[0]->cap_limit;
                      }
                    }*/ 
                    if($finalData[0]->cashback_applied<$data[$i]->cashback_applied){
                      $finalData[0]=$data[$i];                                
                    }                            
                  }else{
                    $finalData[0]=$data[$i];
                    $finalData[0]->cashback_applied=($TotalBillValue*$finalData[0]->cbk_value)/100;
                    if($finalData[0]->cashback_applied >  $finalData[0]->cap_limit){
                      $finalData[0]->cashback_applied= $finalData[0]->cap_limit;
                    }
                  }
                }
              }else{
                $data[$i]->applyCashback=0;
              }
            }
              if(count($tradeData)>0 && count($finalData)>0){
                if($finalData[0]->cashback_applied<$tradeData[0]['cashback_applied']){
                  $finalData = $tradeData;
                }
              }else if(count($finalData)==0 && count($tradeData)>0){
                  $finalData = $tradeData;
              }
              return json_encode(Array(
                  'status' => "success",
                  'message' => "getOrderCashbackData",
                  'data' => $finalData
              ));
          } else {
              return json_encode(Array(
                  'status' => "success",
                  'message' => "No data",
                  'data' => $tradeData
              ));
          }    
            
        } catch (Exception $e) {
       
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }

  }
  public function getffPincodeList(){
      $data=Input::all();
      $data=isset($data['data'])?$data['data']:'';
      $data=json_decode($data,1);
      $user_id=$this->_account->getUserIdByCustomerToken($data['ff_token']);
      $Pincode = $this->_account->getFFPincode($user_id);
      return json_encode(Array(
                  'status' => "success",
                  'message' => "success",
                  'data' => $Pincode
      ));
  }
  public function iconData(){
      $data=array();
      $fridges = $this->lookup->getIconData(170001);
      $alsoselling = $this->lookup->getIconData(170003);
      $notification = $this->lookup->getIconData(170004);
      $other = $this->lookup->getIconData(170005);

      $data['fridges'] = $fridges;
      $data['alsoselling'] = $alsoselling;
      $data['notification'] = $notification;
      $data['others'] = $other;
      return json_encode(Array('status'=>'success','message'=>'success','data'=>$data));
  }

  public function getOrderFreeQtyData($productData = [],$customer_type = '',$wh_id = '')
  {  
    if(count($productData) == 0){
    $Inputdata = Input::all();  
    $arr = isset($Inputdata['data'])?json_decode($Inputdata['data']):array();
    $productData=isset($arr->products) ? $arr->products : array();
    $productData=json_decode(json_encode($productData),1);
    $customer_type=isset($arr->customer_type) ? $arr->customer_type :'';
    $wh_id=isset($arr->le_wh_id) ? $arr->le_wh_id : ''; 
    $flag=isset($arr->proceedtopay)?$arr->proceedtopay:0; 
    }
    if($customer_type== ''){
      return json_encode(Array(
                    'status' => "failed",
                    'message' => "Customer type is required",
                    'data' => [])); 
    }
    $rangeValue=0;
    foreach ($productData as $key => $value) {
      foreach($value as $productId=>$productValue){
        $rangeValue+=$productValue;
      }
    }
    $finalData=[];
    $data=$this->_ecash->getOrderFreeQty($rangeValue,$customer_type,$wh_id);
    $hub_id=$this->_ecash->getHubId($arr->customer_token);
    if(count($hub_id)>0){
      if(count($data) > 0){
        $data[0]->status=-6;
        $data[0]->parent_id=null;
        $data[0]->total_qty=$data[0]->product_qty;
        $data[0]->esu_quantity=0;
        $data[0]->total_price=0;
        $data[0]->applied_margin=0;
        $data[0]->discount=0;
        $data[0]->discount_type=0;
        $data[0]->discount_on=0;
        $data[0]->unit_price=0;
        $data[0]->is_slab=0;
        $data[0]->blocked_qty=0;
        $productpack=$this->_ecash->getProductPackInfo($data[0]->product_id,$data[0]->pack_level);
        $data[0]->star=$productpack->star;
        $data[0]->hub=$hub_id[0]->hub_id;
        $data[0]->prmt_det_id=0;
        $data[0]->product_slab_id=0;
        $data[0]->esu=$productpack->esu;
        $data[0]->freebee_mpq=0;
        $data[0]->freebee_qty=0;
        $data[0]->pack_type='freebie';
        $data[0]->proceedtopay=$flag;
        $data[0]->packs=array(array(
          'esu' => $productpack->esu,
          'qty' => $data[0]->product_qty,
          'pack_qty' =>$productpack->no_of_eaches*$data[0]->product_qty,
          'pack_size' =>$productpack->no_of_eaches,
          'pack_level'=>$data[0]->pack_level,
          'star' => $data[0]->star,
          'pack_cashback'=>""
          ));
        $relatedData=$this->_ecash->getRelatedData($data[0],$customer_type,$wh_id);
        $data[0]->relatedData=$relatedData;
        $finalData[count($finalData)]=$data[0];
        for($index=1;$index<count($data);$index++){
          $flag = 0;
          $data[$index]->status=-6;
          for($finalIndex=0;$finalIndex<count($finalData);$finalIndex++){
            if($finalData[$finalIndex]->is_sample == $data[$index]->is_sample && $finalData[$finalIndex]->product_id == $data[$index]->product_id && $finalData[$finalIndex]->product_qty <= $data[$index]->product_qty){
                $flag=1;
                array_splice($finalData, $finalIndex,1);
                $data[$index]->parent_id=null;
                $data[$index]->total_qty=$data[$index]->product_qty;
                $data[$index]->esu_quantity=0;
                $data[$index]->total_price=0;
                $data[$index]->applied_margin=0;
                $data[$index]->discount=0;
                $data[$index]->discount_type=0;
                $data[$index]->discount_on=0;
                $data[$index]->unit_price=0;
                $data[$index]->is_slab=0;
                $data[$index]->blocked_qty=0;
                $data[$index]->pack_type='freebie';
                $data[$index]->proceedtopay=$flag;
                $productpack=$this->_ecash->getProductPackInfo($data[$index]->product_id,$data[$index]->pack_level);
                $data[$index]->star=$productpack->star;
                $data[$index]->hub=$hub_id[0]->hub_id;
                $data[$index]->prmt_det_id=0;
                $data[$index]->product_slab_id=0;
                $data[$index]->esu=$productpack->esu;
                $data[$index]->freebee_mpq=0;
                $data[$index]->freebee_qty=0;
                $data[$index]->packs=array(array(
                  'esu' => $productpack->esu,
                  'qty' => $data[$index]->product_qty,
                  'pack_qty' =>$productpack->no_of_eaches*$data[$index]->product_qty,
                  'pack_size' =>$productpack->no_of_eaches,
                  'pack_level'=>$data[$index]->pack_level,
                  'star' => $data[$index]->star,
                  'pack_cashback'=>""
                  ));
                $relatedData=$this->_ecash->getRelatedData($data[$index],$customer_type,$wh_id);
                $data[$index]->relatedData=$relatedData;
                $finalData[count($finalData)]=$data[$index];
            }         
          }
          if($flag == 0){
              $productSampleCombination=0;
              for($key=0;$key<count($finalData);$key++){
                  if($finalData[$key]->is_sample == $data[$index]->is_sample && 
                    $finalData[$key]->product_id == $data[$index]->product_id){

                    $productSampleCombination++;

                  }
              }  
              if($productSampleCombination == 0){        
                $data[$index]->parent_id=null;
                $data[$index]->total_qty=$data[$index]->product_qty;
                $data[$index]->esu_quantity=0;
                $data[$index]->total_price=0;
                $data[$index]->applied_margin=0;
                $data[$index]->discount=0;
                $data[$index]->discount_type=0;
                $data[$index]->discount_on=0;
                $data[$index]->unit_price=0;
                $data[$index]->is_slab=0;
                $data[$index]->blocked_qty=0;
                $data[$index]->pack_type='freebie';
                $data[$index]->proceedtopay=$flag;
                $productpack=$this->_ecash->getProductPackInfo($data[$index]->product_id,$data[$index]->pack_level);
                $data[$index]->star=$productpack->star;
                $data[$index]->hub=$hub_id[0]->hub_id;
                $data[$index]->prmt_det_id=0;
                $data[$index]->product_slab_id=0;
                $data[$index]->esu=$productpack->esu;
                $data[$index]->freebee_mpq=0;
                $data[$index]->freebee_qty=0;
                $data[$index]->packs=array(array(
                  'esu' => $productpack->esu,
                  'qty' => $data[$index]->product_qty,
                  'pack_qty' =>$productpack->no_of_eaches*$data[$index]->product_qty,
                  'pack_size' =>$productpack->no_of_eaches,
                  'pack_level'=>$data[$index]->pack_level,
                  'star' => $data[$index]->star,
                  'pack_cashback'=>""
                  ));              
                $relatedData=$this->_ecash->getRelatedData($data[$index],$customer_type,$wh_id);
                $data[$index]->relatedData=$relatedData;
                $finalData[count($finalData)] = $data[$index];
              }
          }

        }      
        return json_encode(Array(
                      'status' => "success",
                      'message' => "getFreeQtyData",
                      'data' => $finalData
                  ));
      }else{
        return json_encode(Array(
                      'status' => "success",
                      'message' => "No Data Found",
                      'data' => []));        
      }
    }else{
      return json_encode(Array(
                      'status' => "failed",
                      'message' => "token expired",
                      'data' => []));  
    }

  }


  /* 
  * Function Name: getSalesTargetReport
  * Description: getSalesTargetReport function is used to get salestarget
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2019
  * Version: v1.0
  * Created Date: 5 Feb 2019
  * Modified Date & Reason:
  */
    public function getSalesTargetReport() {

    if (isset($_POST['data'])) {

      $json = $_POST['data'];
      $decode_data = json_decode($json, true);

      if (isset($decode_data['sales_token']) && !empty($decode_data['sales_token'])) {

        $checkSalesToken = $this->categoryModel->checkCustomerToken($decode_data['sales_token']);

        if($checkSalesToken>0)
        {

          $user_data= $this->categoryModel->getUserId($decode_data['sales_token']); 
          $user_id=$user_data[0]->user_id;
          $legal_entity_id=$user_data[0]->legal_entity_id;

        }else{

          return Response::json(array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
             
        }
      } else {

          return Response::json(array('status' => 'failed', 'message' => 'Please pass sales_token', 'data' => []));
      }

      $reportsRepo = new \App\Central\Repositories\ReportsRepo();

      if (isset($decode_data['flag']) && !empty($decode_data['flag']))
      {
        $flag = $decode_data['flag'];
      }
      else
      {
        $flag =0;
      }

       if($flag == 1){
        $user_id = (isset($decode_data['user_id'])) ? $decode_data['user_id'] : 0;
      } else {
        $user_id = 0;
      } 
      //$user_id = (isset($decode_data['user_id'])) ? $decode_data['user_id'] : 0;


      if(isset($decode_data['start_date']) && !empty($decode_data['start_date'])) {
        $start_date = $decode_data['start_date'];
      } else {
        $start_date =date("Y-m-d");
      }

      if(isset($decode_data['end_date']) && !empty($decode_data['end_date'])) {
        $end_date = $decode_data['end_date'];
      } else {
        $end_date =date("Y-m-d");
      }


      // As we deal with more than 1 company, we need to filter with Legal Entity Id
      $legal_entity_id = $this->lookup->getLegalEntityIdByCustomerToken($decode_data['sales_token']);
      $dc_id = isset($decode_data['dc_id'])?$decode_data['dc_id']:"";

      if($dc_id==""){

        return Response::json(array('status' => 'failed', 'message' => 'Warehouse ID should not be empty', 'data' => []));
      }


      $data = $reportsRepo->getSalesTargetData($start_date, $end_date, $flag,$dc_id, $user_id);
      $result=array();
      if($flag==0)
      {
        
       $salesTarget = DB::selectFromWriteConnection(DB::raw('CALL getDynamicFFTGTDashboard_grid("'.$dc_id.'", "'.$start_date.'", "'.$end_date.'",1,0)'));
       if(count($salesTarget)>0){
        $salesTargetHeaders=json_decode(json_encode($salesTarget),true);
          $result['headers']=array_keys($salesTargetHeaders[0]);
       }
       $salesdataarray=array();
       $salesTarget=json_decode(json_encode($salesTarget),true);
       for ($s=0;$s<count($salesTarget);$s++) {
           $salesdataarray[$s]=array_values($salesTarget[$s]);
       }
       $salesdataarray=(object)$salesdataarray;
       $result['salesresult']=$salesdataarray;
      }
       
              
      if(!empty($data)) {

        return Response::json(array('status' => 'success', 'message' => 'getSalesTargetReport', 'data' => $data,'salesdata'=>$result));
      }else{

        return Response::json(array('status' => 'success', 'message' => 'No data', 'data' => []));
      }

    } else {

        return Response::json(array('status' => 'failed', 'message' => 'Please pass required parameters', 'data' => []));
    }
  }
  public function getCustomerType(){
    $data=Input::all();
    $data=isset($data['data'])?$data['data']:'';
    $data=json_decode($data,1);
    if(!empty($data)){
      if(isset($data['user_id'])){
        $result=$this->lookup->getCustomerType($data['user_id']);
        return Array('status' => 'success', 'message' =>'success', 'data' => $result);  
      }else{
        return Array('status'=>'fail','message'=>'Parameters Required','data'=>[]);
      }
    }
    else {
      return Array('status'=>'fail','message'=>'Parameters Required','data'=>[]);
    }
  }
  public function calculatePromotionValue($products,$state,$Warehouse,$customertype,$is_self){
    $productData = array();
    foreach ($products as $key => $value) {
      foreach ($value as $childKey => $childValue) {      
        $productData[$childKey] = $childValue;
      }
    }
    $productData=json_encode($productData);
    $query = DB::select("CALL getPromotionCashback('".$productData."',$Warehouse,$state,$customertype,$is_self,1)");
    if(count($query)>0){
      return json_decode(json_encode($query),1)[0];
    }else{
      return array();
    }
  }


   public function getDCFCManagerList() {

    if (isset($_POST['data'])) {

      $json = $_POST['data'];
      $decode_data = json_decode($json, true);

      if (isset($decode_data['sales_token']) && !empty($decode_data['sales_token'])) {

        $checkSalesToken = $this->categoryModel->checkCustomerToken($decode_data['sales_token']);

        if($checkSalesToken>0)
        {

          if(isset($decode_data['buid']) && !empty($decode_data['buid'])){
                        $reportsRepo = new \App\Central\Repositories\ReportsRepo();
                        $whleid=$reportsRepo->getdcidbasedonbuid($decode_data['buid']);
                        if(count($whleid)>0){
                          $dc_id = isset($whleid->le_wh_id)?$whleid->le_wh_id:'';
                          $managerslist=$this->lookup->getManagerListForDCFC($dc_id);
                          return Response::json(array('status' => 'success', 'message' => 'Managers List', 'data' =>$managerslist));
                        }else{
                            return Response::json(array('status' => 'success', 'message' => 'This business unit has more than one dc mapped', 'data' => []));            
                        }
          }else{
              return Response::json(array('status' => 'failed', 'message' => 'Please send Business unit', 'data' => []));  
          }         
          
        }else{

          return Response::json(array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
             
        }
      } else {

          return Response::json(array('status' => 'failed', 'message' => 'Please pass sales_token', 'data' => []));
      }

    } else {

        return Response::json(array('status' => 'failed', 'message' => 'Please pass required parameters', 'data' => []));
    }
  }
  public function getFFByHub()
  {
    $data = json_decode($_POST['data'],1);
    if(isset($data['hub_id']) && isset($data['user_id'])){
      $data = $this->lookup->getRmData($data['user_id'],$data['hub_id']);
      return Response::json(array('status'=>'success','message'=>'FFinfo','data'=>$data));
    }else{
        return Response::json(array('status' => 'failed', 'message' => 'Please pass required parameters', 'data' => []));
    }
  }
  public function syncData()
  {
    if(isset( $_POST['data'])){ 
      $json = $_POST['data'];
      $decode_data = json_decode($json, true); 
    }else{
      $decode_data ='';
    }
    $syncdate = isset($decode_data['sync_date'])?$decode_data['sync_date']:'';
    if(!$syncdate){
      $syncdate = '2015-01-01';
    }
    $data = $this->lookup->getMasterLookupOnBasisSyncdate($decode_data,$syncdate);

      return json_encode(Array(
        'status' => "success",
        'message' => "Sync data",
        'data' => $data
      ));

  }
}