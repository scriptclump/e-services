<?php
  namespace App\Modules\Cpmanager\Models; 
  use \DB;
  use Cache;
  use App\Modules\Cpmanager\Models\RegistrationModel;
  use App\Modules\Cpmanager\Controllers\accountController;
  use Session;


class MasterLookupModel extends \Eloquent {

  public function __construct() {
      $this->_registration = new RegistrationModel();
      $this->_mas_cat_ids = array(48,96,97,106,107,110,115,140,3);
      $this->segment_id=48;
      $this->volume_class_id=96;
      $this->custtype_id = 3;
      $this->license_id=97;
      $this->master_manf = 106;
      $this->ff_comment = 107;
      $this->delivery_slot = 110;
      $this->customer_feedback = 115;
      $this->product_star = 140;
      $this->buyer_type = 3;

    }
    /*
      * Function Name: getMasterLookupSegmentss
      * Description: getMasterLookupSegments function is used to get all the segments
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 6 July 2016
      * Modified Date & Reason:
    */     
    public function getMasterLookup($decode_data)
  {
     if(isset($decode_data['key']) && $decode_data['key'] == 'discounts')
     {
       //// Adding Discounts Code HERE below
      $last_result['discounts']= $this->getDiscounts();;
      //// End Discounts
      return $last_result;
      }else{
     
      $result = DB::table('master_lookup as ml')
                  ->select(DB::raw("ml.master_lookup_name as name ,ml.value,ml.description,ml.image,ml.is_display"))
                  ->whereIn("ml.mas_cat_id", array(47,48))
                  ->where("ml.is_active", "=", 1)
                  ->orderBy('ml.sort_order', 'ASC')
                  ->get()->all();
      $i = 0;
      $data = array();
      foreach($result as $key => $values)
      {
      $data[$i]['segment_id'] = $values->value;
      $data[$i]['segment_name'] = $values->name;
      $data[$i]['image'] = $values->image;
      $data[$i]['is_display'] = $values->is_display;

      $i++;
      }
     $last_result=array();
     $last_result['segments']=$data;
    $buyer_desc=$this->_registration->getMaterDescription(78002);
    $last_result['buyer_type']= DB::table('master_lookup as ml')->select(DB::raw("ml.master_lookup_name as name ,ml.value,ml.description,ml.image,ml.is_display"))
                  ->where("ml.mas_cat_id", "=",$buyer_desc)->where("ml.is_active", "=", 1)->whereNotIn("ml.value",[3014])->orderBy('ml.sort_order', 'ASC')->get()->all();
    $last_result['volume_class']=$this->getMasterLookupValues(96);
    $last_result['license']=$this->getMasterLookupValues(97);
    $last_result['master_manf']=$this->getMasterLookupValues(106);
    $ff_comments = DB::table('master_lookup as ml')->select(DB::raw("ml.master_lookup_name as name ,ml.value"))->where("ml.mas_cat_id", "=",107)
    ->whereNotIn("ml.value",[107001,107000])->where("ml.is_active", "=", 1)->orderBy('ml.sort_order', 'ASC')->get()->all();
    $last_result['ff_comments']=$ff_comments;
    $last_result['delivery_slots']=$this->getMasterLookupValues(110);
    $cp_sync =$this->_registration->getMaterDescription(78006);
    $last_result['cp_sync']=(int)$cp_sync;
    $otp_popup = $this->_registration->getMaterDescription(78007);
    if(!empty($otp_popup))
    {  
    $last_result['otp_popup']=(int)$otp_popup;
    }else{
    $last_result['otp_popup']=0;
    }
    $last_result['checkin_distance']= (float)$this->_registration->getMaterDescription(78009);
    $last_result['customer_feedback']=$this->getMasterLookupValues(115);
    $last_result['stars']=$this->getMasterLookupValues(140);
    //// Adding Discounts Code HERE below
    $last_result['discounts']= $this->getDiscounts();
    //// End Discounts
     $gstStateCodesList = DB::table('zone')
                ->where('gst_state_code', '>', 0)
                ->orderBy('gst_state_code', 'ASC')
                ->pluck(DB::raw('group_concat(gst_state_code) as gst_state_code'));
  $last_result['gst_state_codes'] = $gstStateCodesList;
  $last_result['attendance_days']= (int)$this->_registration->getMaterDescription(78010);
    return $last_result;
   }

  }

/*
* Function Name: getDiscounts
* Description: getDiscounts function is used to get all the discount types
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2017
* Version: v1.0
* Created Date: 20 June 2017
* Modified Date & Reason:
* The below method is also being used by Cpmanager/PickerModel.php (getOrderDetailByInvoice)
*/
  public function getDiscounts($order_date = null)
  {
      $today = ($order_date == null)?date('Y-m-d H:i:s'):$order_date;
      $query = DB::table('customer_discounts')
      ->select('discount_type','discount_on','discount_on_values','discount','priority')
      ->where('discount_start_date','<=',$today)
      ->where('discount_end_date','>=',$today)
      ->get()->all();
      return $query;
  }

/*
* Function Name: getMasterLookupBuyerTypes
* Description: getMasterLookupBuyerTypes function is used to get all the buyer types
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 6 July 2016
* Modified Date & Reason:
*/
public function getMasterLookupBuyerTypes()
  {

  $master = DB::table('master_lookup as ml')
                       ->where("ml.value", "=", 78002)->get()->all();

  $buyer = DB::table('master_lookup as ml')->select(DB::raw("ml.master_lookup_name as name ,ml.value"))
  ->where("ml.parent_lookup_id", "=", 1003)->where("ml.mas_cat_id", "=", $master[0]->description)
  ->orWhere("ml.value", "=", 1003)->get()->all();
  return $buyer;
  }

  /*
* Function Name: getVolumeClass
* Description: getVolumeClass function is used to get all the volume class
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 20 Aug 2016
* Modified Date & Reason:
*/
public function getVolumeClass()
  {


  $volume_class = DB::table('master_lookup as ml')->select(DB::raw("ml.master_lookup_name as name ,ml.value"))
  ->where("ml.mas_cat_id", "=",96 )
  ->get()->all();

  return $volume_class;
  }


 /* 
* Function Name: getLicenseType
* Description: getLicenseType function is used to get all the license types
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 20 Aug 2016
* Modified Date & Reason:
*/

public function getLicenseType()
  {

  $license = DB::table('master_lookup as ml')->select(DB::raw("ml.master_lookup_name as name ,ml.value"))
  ->where("ml.mas_cat_id", "=",97 )
  ->get()->all();

  return $license;
  }

/* 
* Function Name: getPincodeAreas
* Description: getPincodeAreas function is used to get all the areas based on pincode
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 8 Sep 2016
* Modified Date & Reason:
*/

public function getPincodeAreas($pincode)
  {

  $areas = DB::table('cities_pincodes as cp')->select(DB::raw("cp.officename"))
  ->where("cp.pincode", "=",$pincode)
  ->get()->all();

  return $areas;
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

public function getOrdersDashboard($users,$start_date,$end_date)
{
  $fromdate = str_replace("'","", $start_date);
  $todate = str_replace("'","", $end_date);
  $user_id = str_replace("'","", $users);
  $response = \Cache::get('dasboard_report'.$user_id.'_1_'.$fromdate.'_'.$todate,false);
    if(!$response){
      $dash_report=DB::select("CALL getOrdersDashboardNew($users,1,$start_date,$end_date)");
      return $dash_report[0];
    }else{
      foreach ($response as $key => $value) {
        $result[strtoupper(str_replace('_', ' ', $key))]=$value; //reversing the data in Reports repo
      }
      return $result;
    }
  
  }

/* 
* Function Name: getFirstname
* Description: getFirstname used to get name based on userid
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 27 Sep 2016
* Modified Date & Reason:
*/


  public function getFirstname($user_id){
            $result = DB::table('users as u')
            ->select(DB::raw("CONCAT(u.firstname,' ',u.lastname) as firstname")) 
            ->where("u.user_id","=",$user_id)   
            ->get()->all();


            return $result[0]->firstname;
    }

    /* 
* Function Name: getFieldForceIds
* Description: getFieldForceIds used to get fieldforce ids based on their logs
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 6 oct 2016
* Modified Date & Reason:
*/



  public function getFieldForceIds($users,$start_date,$end_date){
     

    $s_date=trim($start_date,"'");
    $e_date=trim($end_date,"'");

            $result = DB::table('ff_call_logs')
            ->select(DB::raw("distinct ff_id")) 
            ->whereIn("ff_id",$users)
            ->whereBetween(DB::RAW("DATE(created_at)"),array($s_date,$e_date))
            ->get()->all();

         //  print_r($result);exit;
           $data=array();
           if(!empty($result))
             {
                  foreach ($result as $key => $value) 
                  {

                    foreach ($value as $key => $value) 
                    {
                       $data[]=$value;
                    }

                   }

             }

            return $data;
    }

    /* 
* Function Name: getPincodeData
* Description: getPincodeData used to areas and 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 27 Sep 2016
* Modified Date & Reason:
*/


  public function getPincodeData($pincode)
  {
          
  $state = DB::table('cities_pincodes as cp')
           ->select(DB::raw("distinct state"))
           ->where("cp.pincode", "=",$pincode)
           ->get()->all();

  if(!empty($state))
  { 

  $state_id=DB::table('zone')
              ->select("zone_id")
              ->where("name","LIKE",$state[0]->state)
              ->get()->all();

 $data['state_id']=$state_id[0]->zone_id;
 $data['state_name']=$state[0]->state;
  return $data;


    }                   

        return $state;

    }


/* 
* Function Name: getCancelReason
* Description: getCancelReason used to cancel reason
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 27 Sep 2016
* Modified Date & Reason:
*/


  public function getCancelReason()
  {

  try{  
          
  $reason = DB::table('master_lookup as ml')
                  ->select(DB::raw("ml.master_lookup_name as name ,ml.value"))
                  ->where("ml.mas_cat_id", "=",60)
                  ->where("ml.is_active", "=", 1)
                  ->whereNotIn("ml.value", [60001,60002,60012])
                  ->orderBy('ml.sort_order', 'ASC')
                  ->get()->all();

        return $reason;

        }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  

    }

/* 
* Function Name: getFfBeat
* Description: getFfBeat used to get based on beat based on ff_id
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 Dec 2016
* Modified Date & Reason:
*/


  public function getFfBeat($ff_id,$hub)
  {

  try{  
   
   $beat = DB::table('pjp_pincode_area')
                  ->select(DB::raw("pjp_name ,pjp_pincode_area_id,pdp,pdp_slot"))
                  ->join('legalentity_warehouses as lew','pjp_pincode_area.le_wh_id','=','lew.le_wh_id')
          ->Where('lew.status',1);
            
if(empty($hub))
{  

   $beat= $beat->where(function($query) use ($ff_id) {
                              $query->whereIn('rm_id',$ff_id)
                                  ->orwhere('pjp_pincode_area_id',0);
              });

}else{
$hub= explode(",",$hub);
$beat= $beat->join('spokes as sp','pjp_pincode_area.spoke_id','=','sp.spoke_id')
->whereIn('sp.le_wh_id',$hub);
}
         return  $beat->get()->all();

        }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  

    }

  public function getMasterLookupValues($mas_cat_id)
  {

  try{  
          
  $values = DB::table('master_lookup as ml')
                  ->select(DB::raw("ml.master_lookup_name as name ,ml.value,ml.description"))
                  ->where("ml.mas_cat_id", "=",$mas_cat_id)
                  ->where("ml.is_active", "=", 1)
                  ->orderBy('ml.sort_order', 'ASC')
                  ->get()->all();

        return $values;

        }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
      }  

    }
    public function getMasterLokup($value) {
        $data = DB::table('master_lookup')
                        ->select('master_lookup_id', 'description', 'value')
                        ->where('value', $value)->first();
        return $data;
    }

    public function getLegalEntityIdByCustomerToken($token)
    {
      $account = new accountController();
      $legalId=$account->getDataFromToken(1,$token,'legal_entity_id');

      return isset($legalId->legal_entity_id)?$legalId->legal_entity_id:-1;
    }
  public function getFfBeatByPincodewise($ff_id,$hub,$pincode){
    try{
      if(empty($hub)){
        $hub='NULL';
      }
      $query = DB::select(DB::raw("CALL getMobileBeatDetailsByPin('$ff_id','$pincode',$hub)"));
      return $query;
    }catch (Exception $e){          
      return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
    } 
  }

  public function getffLegalentity($ff_id){
    try{
      $query=DB::select(DB::raw("select legal_entity_id from users where user_id=".$ff_id));
      $query=json_decode(json_encode($query),1);
      if(count($query)>0){
        return $query[0]['legal_entity_id'];
      }else{
        return 0;
      }
    }catch (Exception $e){          
      return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
    } 
  }
  public function getPincodeLegalentity($pincode){
    try{
      $query=DB::select(DB::raw("select legal_entity_id from wh_serviceables where pincode=".$pincode));
      $query=json_decode(json_encode($query),1);
      if(count($query)>0){
        return $query[0]['legal_entity_id'];
      }else{
        return 0;
      }
    }catch (Exception $e){          
      return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
    }
  }
  public function getBeatsForGlobalAccess($pincode){
    try{
      $beats="select pjp.`pjp_name`,pjp.`pjp_pincode_area_id` FROM wh_serviceables ws, dc_hub_mapping lw, pjp_pincode_area pjp, spokes s, legalentity_warehouses lew WHERE ws.`le_wh_id` = lw.`dc_id`AND pjp.`le_wh_id` = lw.`hub_id`AND s.`spoke_id` = pjp.`spoke_id`AND lew.`le_wh_id` = pjp.`le_wh_id`AND ws.`pincode` = ".$pincode ;

      $beats=DB::select(DB::raw($beats));
      return $beats;
    }catch(Exception $e){
      return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
    }
  }

    /*
    * @input: 
    *   $roleCodes -> An array of role short codes
    *   $usersList -> An array of user Id`s
    */
    public function getUsersByRoleCodePermission($roleCodes,$usersList = [])
    {
      $result = DB::table('users')
        ->select('users.user_id', 'users.firstname', 'users.lastname', 'users.email_id', 'users.mobile_no')
        ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
        ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
        ->where('users.is_active',1)
        ->whereIn('roles.short_code', $roleCodes);

        if($usersList == [] or $usersList == null){
          return $result->groupBy('users.user_id')->get()->all();
        }
        // If the users list is NOT empty, then we filter the query
        return $result
          ->whereIn('user_roles.user_id', $usersList)
          ->groupBy('users.user_id')
          ->get()->all();
        
    }
    public function getCustomerType($user_id){
      $buyer_userid=$user_id;
      $buyer_featureCode='CNC001';

      $buyer_result = DB::table('role_access')
                ->select('features.name')
                ->join('features','role_access.feature_id','=','features.feature_id')
                ->join('user_roles','role_access.role_id','=','user_roles.role_id')
                ->where([
                    'user_roles.user_id'=>$buyer_userid, 
                    'features.feature_code'=>$buyer_featureCode,
                    'features.is_active'=>1
                ])
                ->count();
      if($buyer_result>0){
         $data= DB::table('master_lookup as ml')->select(DB::raw("ml.master_lookup_name as name ,ml.value,ml.description"))
                    ->where("ml.mas_cat_id", "=",3)->where("ml.is_active", "=", 1)
                    ->whereNotIn("ml.description",[0])
                    ->whereNotIn("ml.value",[3014])->orderBy('ml.sort_order', 'ASC')->get()->all();  
      }else{
        $data= DB::table('master_lookup as ml')->select(DB::raw("ml.master_lookup_name as name ,ml.value,ml.description"))
                    ->where("ml.mas_cat_id", "=",3)->where("ml.is_active", "=", 1)
                    ->whereNotIn("ml.description",[0,1])
                    ->whereNotIn("ml.value",[3014])->orderBy('ml.sort_order', 'ASC')->get()->all();   
      }

      return $data;

    }


    public function getRoleIdByRoleCode($roleid){

      $result=DB::table('roles')
              ->select('role_id')
              ->where('short_code',$roleid)
              ->get()->all();
               //echo '<pre/>';print_r($result);exit;
      return $result;         
    }
    public function getIconData($value){
        $masterlookupData= DB::table('icons_list')
                            ->select('id','label','url','icon_code')
                            ->where('icon_type',$value)
                            ->get()->all();
        return $masterlookupData;
    }

    public function getManagerListForDCFC($dcid){

      try{
          $shortcode=DB::table('roles')->select(DB::raw('group_concat(role_id) as role_id'))->where('short_code','SSLL')->get()->all();
          $shortcode=$shortcode[0]->role_id;
          $managers_query=DB::select(DB::raw('CALL getManagersListByDC('.$dcid.', "'.$shortcode.'")'));

          return $managers_query;
        }catch (Exception $e)
        {
          $managers_query=[];
          return $managers_query;
        }
    }
    public function getRmData($user_id,$hub_id)
    {
      $data = DB::select(DB::raw('select u.user_id,u.firstname,u.lastname,u.email_id,u.mobile_no FROM
            users u JOIN legalentity_warehouses l ON u.`legal_entity_id` = l.`legal_entity_id`
            JOIN user_roles ur ON u.`user_id` = ur.`user_id`
            JOIN roles r ON r.`role_id` = ur.`role_id` 
            WHERE u.`reporting_manager_id` = '.$user_id.'
            AND l.`le_wh_id`='.$hub_id.'
            AND r.`short_code` IN ("SSLO","SSLA")
            AND u.`is_active`=1 group by u.user_id'));
      return $data;
    }

    public function getMasterLookupOnBasisSyncdate($value,$syncdate)
    {
        $rawData = [];
        $licenses = [];
        $segments = []; 
        $volumeclass = []; 
        $manf = [];
        $ff_comment = [];  
        $delivery_slot =[];  
        $feedback = [];
        $stars = []; 
        $buyer_type=[]; 
        $values = DB::table('master_lookup as ml')
                  ->select(DB::raw("ml.master_lookup_name as name ,ml.value,ml.description,ml.mas_cat_id,date(created_at) as created_at,date(updated_at) as updated_at"))
                  ->whereIn("ml.mas_cat_id",$this->_mas_cat_ids)
                  ->whereNotIn("ml.value",[107001,107000])
                  ->where("ml.is_active", "=", 1);

        if($syncdate){
          $values = $values->where(function($query) use($syncdate){
              $query->whereRaw("date(created_at) >= '$syncdate'")
                    ->orWhereRaw("date(updated_at)>= '$syncdate'");
          });
        }
        $values = $values->orderBy('ml.sort_order', 'ASC')
                  ->get()->all();
        $values = json_decode(json_encode($values),1);
        foreach ($values as $key => $value) {
          if($value['mas_cat_id'] == $this->license_id){
            unset($value['mas_cat_id']);
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $licenses['created'][] = $value;

            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $licenses['updated'][] = $value;
            }
          }else if($value['mas_cat_id'] ==  $this->segment_id){
            unset($value['mas_cat_id']);            
            $temp = array();
            $temp['segment_id'] = $value['value'];
            $temp['segment_name'] = $value['name'];
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $segments['created'][] = $temp;
            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $segments['updated'][] = $temp;
            }
          }else if($value['mas_cat_id'] ==  $this->volume_class_id){
            unset($value['mas_cat_id']);            
            $temp = array();
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $volumeclass['created'][] = $value;
            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $volumeclass['updated'][] = $value;
            }
          }else if($value['mas_cat_id'] ==  $this->master_manf){
            unset($value['mas_cat_id']);            
            $temp = array();
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $manf['created'][] = $value;
            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $manf['updated'][] = $value;
            }
          }else if($value['mas_cat_id'] == $this->ff_comment){
            unset($value['mas_cat_id']);            
            $temp = array();
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $ff_comment['created'][] = $value;
            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $ff_comment['updated'][] = $value;
            }
          }else if($value['mas_cat_id'] == $this->delivery_slot){
            unset($value['mas_cat_id']);            
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $delivery_slot['created'][] = $value;
            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $delivery_slot['updated'][] = $value;
            }
          }else if($value['mas_cat_id'] == $this->customer_feedback){
            unset($value['mas_cat_id']);            
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $feedback['created'][] = $value;
            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $feedback['updated'][] = $value;
            }
          }else if($value['mas_cat_id'] == $this->product_star){
            unset($value['mas_cat_id']);            
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $stars['created'][] = $value;
            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $stars['updated'][] = $value;
            }
          }
        } 
        $rowData['license'] = $licenses;
        $rowData['segments'] = $segments;
        $rowData['volume_class'] = $volumeclass;
        $rowData['master_manf'] = $manf;
        $rowData['ff_comments'] =$ff_comment;
        $rowData['delivery_slots'] = $delivery_slot;
        $rowData['customer_feedback'] = $feedback;
        $rowData['stars'] = $stars;
        $buyerlist= DB::table('master_lookup as ml')
                                    ->select(DB::raw("ml.master_lookup_name as name ,ml.value,ml.description,date(created_at) as created_at,date(updated_at) as updated_at"))
                                    ->where("ml.mas_cat_id", "=",3)
                                    ->where("ml.is_active", "=", 1)
                                    ->whereNotIn("ml.value",[3014])
                                    ->where("ml.is_display","=",1)
                                    ->orderBy('ml.sort_order', 'ASC')->get()->all();
        foreach ($values as $key => $value) {
            if($value['created_at'] >= $syncdate)
            {
              unset($value['created_at']);
              unset($value['updated_at']);
              $buyer_type['created'][] = $value;
            }
            else{
              unset($value['created_at']);
              unset($value['updated_at']);
              $buyer_type['updated'][] = $value;
            }
        }
        $rowData['buyer_type'] = $buyer_type;

        $desc = DB::table('master_lookup')
          ->select('description','value')
          ->whereIn('value',array(78006,78007,78009,78010))
          ->pluck('description','value')->all();
        //$desc = json_decode(json_encode($desc),1);


        $rowData['cp_sync'] = (int)$desc[78006];
        $rowData['checkin_distance'] = (float)$desc[78009];
        $rowData['attendance_days'] = (int)$desc[78010];
        if(!empty($rowData[78007]))
        {  
          $rowData['otp_popup']=(int)$otp_popup;
        }else{
          $rowData['otp_popup']=0;
        }
        $rowData['discounts']= $this->getDiscounts();
        $gstStateCodesList = DB::table('zone')
                ->where('gst_state_code', '>', 0)
                ->orderBy('gst_state_code', 'ASC')
                ->pluck(DB::raw('group_concat(gst_state_code) as gst_state_code'))->all();
        $rowData['gst_state_codes'] = $gstStateCodesList;

        
        return $rowData;
        // print_r($rowData);
        // exit;
    }

}