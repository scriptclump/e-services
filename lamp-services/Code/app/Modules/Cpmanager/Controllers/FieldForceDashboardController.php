<?php

  namespace App\Modules\Cpmanager\Controllers;
  
  use Illuminate\Support\Facades\Input;
  use Session;
  use Response;
  use Log;
  use URL;
  use DB;
  use PDF;
  use Lang;
  use Config;
  use Illuminate\Http\Request;
  use App\Modules\Roles\Models\Role;
  use App\Modules\Cpmanager\Models\FieldForceDashboardModel;
  use App\Modules\Cpmanager\Models\CategoryModel;
   use App\Modules\Cpmanager\Models\AdminOrderModel;
  use App\Http\Controllers\BaseController;
  
  
  
 class FieldForceDashboardController extends BaseController {
    
    public function __construct() {  
           
           $this->_dashboard = new FieldForceDashboardModel();
           $this->_category = new CategoryModel(); 
           $this->_role = new Role();  
           $this->_admin=new AdminOrderModel();  
            
    }



 /*
  * Function Name: getSoDashboard()
  * Description: Used to get sales order dashboard
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 13th Dec 2016
  * Modified Date & Reason:
  */
  public function getSoDashboard()
  {

   try{ 

   if (isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 
        if(isset($arr->sales_token) && !empty($arr->sales_token) )
             {
               $checkSalesToken = $this->_category->checkCustomerToken($arr->sales_token);
                if($checkSalesToken>0)
                  {
                  $le_wh_id=(isset($arr->warehouse_list) && $arr->warehouse_list!='')?"'".$arr->warehouse_list."'":"NULL";
                  $hub=(isset($arr->hubs) && $arr->hubs!='')?"'".$arr->hubs."'":"NULL";
                  $created_by=(isset($arr->ff_name) && $arr->ff_name!='')?"'".$arr->ff_name."'":'';
                  $beat=(isset($arr->beats) && $arr->beats!='')?"'".$arr->beats."'":"NULL";
                  $flag=(isset($arr->flag) && $arr->flag!='')?$arr->flag:'';
                  $start_date=(isset($arr->start_date) && $arr->start_date!='')?$arr->start_date:date('Y-m-d');
                  $end_date=(isset($arr->end_date) && $arr->end_date!='')?$arr->end_date:date('Y-m-d'); 
                  $user_id=(isset($arr->user_id) && $arr->user_id!='')?$arr->user_id:''; 
                  

                     if(!empty($user_id))   
                     { 
                     $DataFilter=$this->_role->getFilterData(6,$user_id);
                     $decode_data=json_decode($DataFilter,true);
                     $sbu_lits = isset($decode_data['sbu']) ? $decode_data['sbu'] : [];
                     $decode_sbulist= json_decode($sbu_lits,true);
                     $hub = (isset($decode_sbulist[118002])&& !empty($decode_sbulist[118002])) ? "'".$decode_sbulist[118002]."'" :"NULL";  
                     }

                  if($flag==1)
                   {
                 
                     //   $status='17001,17020,17005';
                        $data = $this->_dashboard->getSoDashboardPicker($le_wh_id,$beat,$start_date,$end_date,$hub); 
                        $status_count=  $this->_dashboard->getSoDashboard($created_by,$le_wh_id,$beat,$start_date,$end_date); 
                      
                     }elseif($flag==2){

                        $status='17014,17007,17022';
                        $data = $this->_dashboard->getSoDashboardDeliver($le_wh_id,$beat,$start_date,$end_date,$status,$hub); 
                        $status_count= $this->_dashboard->getSoDashboard($created_by,$le_wh_id,$beat,$start_date,$end_date);
                        }elseif($flag==3){

                       
                        $data = $this->_dashboard->getSoDashboardHub($start_date,$end_date); 
                        $status_count= [];
                               }elseif($flag==4){
                       
                        $data = $this->_dashboard->getReturnsDashboard($start_date,$end_date,$hub); 
                        $status_count= [];
                               }else{
                  
                  
                   //$picker = $this->_dashboard->getSoDashboard($created_by,$le_wh_id,$beat,$start_date,$end_date,1); 
                   $data = $this->_dashboard->getSoDashboard($created_by,$le_wh_id,$beat,$start_date,$end_date); 
                   //print_r( $deliver);exit;
                   ///$data=array_merge($picker,$deliver);
                  // print_r($data);exit;

                   $status_count= [];     
                        }
                           if (!empty($data))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "getSoDashboard",
                                    'data' => $data,
                                   'status_count'=>$status_count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => [],
                                    'status_count'=>[]
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
       
          return Array('status' => "failed", 'message' => 'Internal server error', 'data' =>  []);
      } 
    

    }
    

/*
  * Function Name: getSoDashboardFilters()
  * Description: Used to get sales order dashboard
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 20th Dec 2016
  * Modified Date & Reason:
  */
  public function getSoDashboardFilters()
  {

   try{ 

   if (isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 


             if(isset($arr->sales_token) && !empty($arr->sales_token) )
             {
                  
                 $checkSalesToken = $this->_category->checkCustomerToken($arr->sales_token);
                
                  if($checkSalesToken>0)
                  {
               if(isset($arr->legal_entity_id) && !empty($arr->legal_entity_id) )
                {
                  if(isset($arr->ff_id) && !empty($arr->ff_id) )
                {

                     $flag=(isset($arr->flag) && !empty($arr->flag))?$arr->flag:0;

                   //  $team=$this->_role->getTeamByUser($user_data[0]->user_id);

                        $data = $this->_dashboard->getSoDashboardFilters($flag,$arr->ff_id,$arr->legal_entity_id); 
                         
                           if (!empty($data))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "getSoDashboardFilters",
                                    'data' => $data
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
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass user_id",'data'=> [])));die;

               }     
             }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass legal_entity_id",'data'=> [])));die;

               }

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
       
          return Array('status' => "failed", 'message' => 'Internal server error', 'data' =>  []);
      } 
    

    }
    
   

  

}
