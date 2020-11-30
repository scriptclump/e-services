<?php
  namespace App\Modules\Cpmanager\Models;
  use Illuminate\Database\Eloquent\Model;
  use App\Modules\Cpmanager\Models\SrmModel;
  use App\Modules\Roles\Models\Role;
  use App\Modules\Cpmanager\Models\PickerModel;
  use DB;  
  
 
  class FieldForceDashboardModel extends Model {


  public function __construct()
   {  
         
            $this->srmModel = new SrmModel();  
            $this->_role = new Role();  
            $this->_picker = new PickerModel();
            

            
    }


Public function getSoDashboard($created_by,$le_wh_id,$beat,$start_date,$end_date) 
  {  
       $end_date= $end_date.' '.'23:59:59';
  
         $count=$this->getSoDashboardCount($created_by,$le_wh_id,$beat,$start_date,$end_date);
//db::enablequerylog();
         $result= DB::table('gds_orders as go')
                      ->select(db::raw('COUNT(go.order_status_id) as count'),
                        db::raw('getMastLookupValue(go.order_status_id) as status'),
                        db::raw('ROUND(SUM(total),2) as total'),
                        db::raw('ROUND(((COUNT(go.`order_status_id`)/"'.$count.'")
                        *100 ),2) AS percentage'));             
         if(!empty($created_by))
         {
           $result->whereRaw('FIND_IN_SET(created_by,"'.$created_by.'")');
          }    
         if(!empty($le_wh_id) && $le_wh_id!='NULL')
         {
           $result->whereRaw('FIND_IN_SET(le_wh_id,"'.$le_wh_id.'")');
          } 
          if(!empty($beat) && $beat!='NULL')
          {
           $result->whereRaw('FIND_IN_SET(beat,"'.$beat.'")');
          }  
          if(!empty($start_date) && !empty($end_date))
          {
           $result->whereRaw('go.order_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
          }         
       return $result->groupBy('order_status_id')->get()->all();            
   }

    Public function getSoDashboardCount($created_by,$le_wh_id,$beat,$start_date,$end_date) 
  {  
         $end_date= $end_date.' '.'23:59:59';
         $result= DB::table('gds_orders as go')
                      ->select(db::raw('COUNT(go.order_status_id) as count'));                    
         if(!empty($created_by))
         {           
           $result->whereRaw('FIND_IN_SET(created_by,"'.$created_by.'")');
          }    
         if(!empty($le_wh_id) && $le_wh_id!='NULL')
         {
           $result->whereRaw('FIND_IN_SET(go.le_wh_id,"'.$le_wh_id.'")');
          } 
          if(!empty($beat) && $beat!=-1 && $beat!='NULL')
          {           
           $result->whereRaw('FIND_IN_SET(go.beat,"'.$beat.'")');
          }  
          if(!empty($start_date) && !empty($end_date))
          {
           $result->whereRaw('go.order_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

          }         
          
         $result=$result->get()->all();

          return $result[0]->count;              


   }
 /*
      * Class Name: getSoDashboardPicker
      * Description: Function used to get so dashboard
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 9 dec 2016
      * Modified Date & Reason:   
    */

  Public function getSoDashboardPicker($le_wh_id,$beat,$start_date,$end_date,$hub) 
  {  
    
      $result_picker = DB::select("CALL getPickerDashboard(0,0,'".$start_date."','".$end_date."',$le_wh_id,$beat,$hub)");
      return $result_picker;

   }

    Public function getSoDashboardPickerCount($le_wh_id,$beat,$start_date,$end_date,$hub) 
  {  
     
         $result= DB::table('gds_orders as go')
                      ->select(
                        db::raw('COUNT(go.order_status_id) as count'))
                     ->join('gds_order_track as got','go.gds_order_id','=','got.gds_order_id');
                   //  ->whereRaw('FIND_IN_SET(go.order_status_id,"17020,17005")');
       

         if(!empty($le_wh_id) && $le_wh_id!='NULL')
         {
           
           $result->whereRaw('FIND_IN_SET(go.le_wh_id,'.$le_wh_id.')');

          } 

          if(!empty($hub) && $hub!='NULL')
         {
           
           $result->whereRaw('FIND_IN_SET(go.hub_id,'.$hub.')');

          } 

          if(!empty($beat) && $beat!=-1 && $beat!='NULL')
          {
           
           $result->whereRaw('FIND_IN_SET(go.beat,'.$beat.')');

          }  
          if(!empty($start_date) && !empty($end_date))
          {
           
           $result->whereRaw('DATE(go.order_date) BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

          }         
                  
                  
         $result=$result->get()->all(); 

          return $result[0]->count;              


   }

/*
      * Class Name: getSoDashboardDeliver
      * Description: Function used to get so dashboard
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 3 jan 2017
      * Modified Date & Reason: 
    */

  Public function getSoDashboardDeliver($le_wh_id,$beat,$start_date,$end_date,$status,$hub) 
  {  
    
    $result_deliver = DB::select("CALL getDeliverDashboard(0,0,'".$start_date."','".$end_date."',$le_wh_id,
      $beat,$hub)");
      return $result_deliver;

   }

    Public function getSoDashboardDeliverCount($le_wh_id,$beat,$start_date,$end_date,$status,$hub) 
  {  
         $result= DB::table('gds_orders as go')
                      ->select(
                        db::raw('COUNT(go.order_status_id) as count'))
                     ->join('gds_order_track as got','go.gds_order_id','=','got.gds_order_id')
                     ->whereRaw('FIND_IN_SET(go.order_status_id,"17021,17007,17014,17022")');
    
          if(!empty($le_wh_id) && $le_wh_id!='NULL')
         {
           
           $result->whereRaw('FIND_IN_SET(go.le_wh_id,"'.$le_wh_id.'")');

          } 

          if(!empty($hub) && $hub!='NULL')
         {
           
           $result->whereRaw('FIND_IN_SET(go.hub_id,"'.$hub.'")');

          } 

          if(!empty($beat) && $beat!=-1 && $beat!='NULL')
          {
           
           $result->whereRaw('FIND_IN_SET(go.beat,"'.$beat.'")');

          }  
          if(!empty($start_date) && !empty($end_date))
          {
           
           $result->whereRaw('DATE(got.delivery_date) BETWEEN "'.$start_date.'" AND "'.$end_date.'"');

          }         
                  
                  
         $result=$result->get()->all();

          return $result[0]->count;              


   }

 Public function getSoDashboardFilters($flag,$user_id,$legal_entity_id) 
  {  
     
           $warehouse=$this->getWarehouseHubs($user_id,$legal_entity_id,1);
           
        $last_result['warehouse_list']= DB::table('legalentity_warehouses as lew')
                                        ->select(DB::raw("lew.le_wh_id as id ,lp_wh_name as name"))
                                        ->whereRaw('FIND_IN_SET(lew.le_wh_id,"'.$warehouse.'")')
                                        ->get()->all();

        $hub=$this->getWarehouseHubs($user_id,$legal_entity_id,2);
       
        $last_result['hubs']= DB::table('legalentity_warehouses as lew')
                            ->select(DB::raw("lew.le_wh_id as id ,lp_wh_name as name"))
                            ->whereRaw('FIND_IN_SET(lew.le_wh_id,"'.$hub.'")')
                            ->get()->all();

         // $last_result['hubs']=$hubs;           
        
         $last_result['beats']= DB::table('pjp_pincode_area as ppa')
                                ->select(DB::raw("ppa.pjp_pincode_area_id as id ,ppa.pjp_name as name"))
                                ->whereRaw('FIND_IN_SET(ppa.le_wh_id,"'.$hub.'")')
                                ->get()->all();

       // $last_result['beats']=$beats;
    

        $ff_name = DB::table('users')
                    ->select('users.user_id as id','users.firstname as name')
                    ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                    ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                    ->where('users.is_active',1)
                   // ->whereIn('roles.name','Picker')
                    ->where('roles.role_id',57)
                    ->get()->all();
  

        $last_result['ff_name']=$ff_name;


        $business_unit = DB::table('business_units as bu')
                          ->select(DB::raw("bu.bu_id as id,bu.bu_name as name"))
                          ->where("bu.is_active",1)
                          ->get()->all();

        $last_result['business_unit']=$business_unit;
        
        

         return $last_result;

   }

public function getWarehouseHubs($user_id,$legal_entity_id,$flag)
{
try{

     $team=$this->_role->getTeamByUser($user_id); 

   // $data = $this->_picker->getUsersByRoleNameId(['Delivery Executive','Picker','HUB Incharge','Logistics Manager','DC Manager'],$team);
  
   if(!empty($team))
   {
    foreach ($team as $key => $value) 
    {
    
             $DataFilter=$this->_role->getFilterData(6,$value);
             $decode_data=json_decode($DataFilter,true);
             $sbu_lits = isset($decode_data['sbu']) ? $decode_data['sbu'] : [];

              if(!empty($sbu_lits))
                {

                 $decode_sbulist= json_decode($sbu_lits,true);
                 
                if($flag==1)
                {
                $data = (isset($decode_sbulist[118001])&& !empty($decode_sbulist[118001])) ? $decode_sbulist[118001] : '';
                }else{
              
               $data = (isset($decode_sbulist[118002])&& !empty($decode_sbulist[118002])) ? $decode_sbulist[118002] : '';
               
                }
               
      
              } else{
              
              $data ='';

            }

          }
          }
          else{

          $data ='';

          }

     
   return  $data;
     
     }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>  []);
      } 




}


public function getSoDashboardHub($start_date,$end_date) 
  {  
        
      $data = DB::select("CALL getHubDcDashboard('".$start_date."','".$end_date."') ");

      return $data;
   }

   public function getReturnsDashboard($start_date,$end_date,$hub) 
  {  
    try{
    $end_date= $end_date.' '.'23:59:59';
    $result=DB::select(db::raw("SELECT got.`delivered_by`, GetUserName (got.delivered_by, 2) AS DeliveredBy,SUM(
    CASE
      gr.return_reason_id
      WHEN 59001
      THEN gr.total 
      ELSE 0 
    END
  ) AS ONPR, SUM(
    CASE
      gr.return_reason_id
      WHEN 59002
      THEN gr.total 
      ELSE 0 
    END
  ) AS WOP , SUM(
    CASE
      gr.return_reason_id
      WHEN 59003
      THEN gr.total 
      ELSE 0 
    END
  ) AS WAD , SUM(
    CASE
      gr.return_reason_id
      WHEN 59004
      THEN gr.total 
      ELSE 0 
    END
  ) AS NC,SUM(
    CASE
      gr.return_reason_id
      WHEN 59005
      THEN gr.total 
      ELSE 0 
    END
  ) AS Credit,SUM(
    CASE
      gr.return_reason_id
      WHEN 59006
      THEN gr.total 
      ELSE 0 
    END
  ) AS SS,SUM(
    CASE
      gr.return_reason_id
      WHEN 59007
      THEN gr.total 
      ELSE 0 
    END
  ) AS Delay,SUM(
    CASE
      gr.return_reason_id
      WHEN 59008
      THEN gr.total 
      ELSE 0 
    END
  ) AS QATC,SUM(
    CASE
      gr.return_reason_id
      WHEN 59009
      THEN gr.total 
      ELSE 0 
    END
  ) AS INVMIS,SUM(
    CASE
      gr.return_reason_id
      WHEN 59010
      THEN gr.total 
      ELSE 0 
    END
  ) AS PFD,SUM(
    CASE
      gr.return_reason_id
      WHEN 59011
      THEN gr.total 
      ELSE 0 
    END
  ) AS MRP,SUM(
    CASE
      gr.return_reason_id
      WHEN 59012
      THEN gr.total 
      ELSE 0 
    END
  ) AS ESP,SUM(
    CASE
      gr.return_reason_id
      WHEN 59013
      THEN gr.total 
      ELSE 0 
    END
  ) AS MultipleAtt,SUM(
    CASE
      gr.return_reason_id
      WHEN 59014
      THEN gr.total 
      ELSE 0 
    END
  ) AS Others    FROM gds_orders AS go JOIN gds_order_track AS got ON go.`gds_order_id`=got.`gds_order_id` 
JOIN gds_return_grid AS grg ON got.gds_order_id=grg.`gds_order_id`
JOIN gds_returns AS gr ON grg.`return_grid_id`=gr.`return_grid_id`
WHERE delivered_by IS NOT NULL AND delivered_by !=0
and  grg.created_at BETWEEN '".$start_date."'
 AND '".$end_date."'
AND FIND_IN_SET(go.hub_id,IFNULL(".$hub.",go.hub_id))
GROUP BY delivered_by"));

 return $result;
  } catch(Exception $e) {
            
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }  
   }

}
  
  ?>