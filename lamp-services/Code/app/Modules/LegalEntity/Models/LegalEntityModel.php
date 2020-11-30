<?php
namespace App\Modules\LegalEntity\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\LegalEntity\Models\LegalEntityModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use DB;
use Session;
use UserActivity;
use App\Modules\Roles\Models\Role;
use App\Modules\Orders\Models\PaymentModel;
use App\Central\Repositories\RoleRepo;
class LegalEntityModel extends Model
{
    public $bussinessUnitList;
  public function __construct() {
        
    $this->roleAccess = new RoleRepo();
        $this->bussinessUnitList= '<option value="">Please Select Bussiness Unit ....</option> ';
  
  }
  public function GetAllLegalEntities($makeFinalSql, $orderBy, $page, $pageSize,$legalid){
        try{
         if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'WHERE ' . $value;
            }elseif(count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= 'AND' .$value;
            }
            $countLoop++;
        }

        $roleObj = new Role();
        $user_id = Session::get('user_id');
     $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
     $filters = json_decode($Json['sbu'], 1);            
     $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
      $legaEntityQuery = "";
      if(Session::get('legal_entity_id')!=2){
      $legaEntityQuery=" where dc_id in (".$dc_acess_list.")";
     }

     
       $legalentityDetails = "select * from vw_legalentity_grid " .$legaEntityQuery. $sqlWhrCls.$orderBy;
      $allData = DB::select(DB::raw($legalentityDetails));
      return $allData;
      }catch(Exception $e){
        return 'Message: ' .$e->getMessage();
      }
    }
   //  public function editGridId($id){
   //     try{
   //     $data ='SELECT
   //          le.`legal_entity_id`,
   //          le.`le_code`,
   //          le.`pan_number`,
   //          le.`city`,
   //          le.`pincode`,
   //          le.`logo`,
   //          le.`state_id`,
   //          le.`display_name`,
   //          le.`gstin`,
   //          le.`is_self_tax`,
   //          u.`mobile_no`,
   //          u.`email_id`,
   //          u.`otp`,
   //          u.`user_id`,
   //          lew.`le_wh_code`,
   //          `getMastLookupValue`(
   //          `le`.`legal_entity_type_id`)  AS `Warehouse`,
   //          (SELECT CONCAT(un.`firstname`," ",un.`lastname`) FROM users un WHERE un.`user_id` = le.`created_by`) AS created_byname,
   //          (SELECT CONCAT(un.`firstname`," ",un.`lastname`) FROM users un WHERE un.`user_id` = le.`updated_by`) AS updated_byname,
   //          u.`firstname`,
   //          u.`lastname`,
   //          le.`business_legal_name`,
   //          u.`is_active`,
   //          le.`address2`,
   //          le.`address1` ,
   //          uec.`creditlimit`,
   //          round(uec.`cashback`,2) as cashback,
   //          (SELECT SUM(pay_amount) FROM payment_details WHERE payment_from='.$id.' AND deposite_type=1) as cashback_deposite,
   //          uec.`applied_cashback`
   //          FROM
   //          legal_entities AS le 
   //          LEFT JOIN users AS u   ON  le.`legal_entity_id` =   u.`legal_entity_id` AND u.is_parent = 1 
   //          LEFT JOIN user_ecash_creditlimit AS uec ON uec.user_id = u.user_id
   //          LEFT JOIN legalentity_warehouses AS lew ON lew.legal_entity_id = le.legal_entity_id AND lew.dc_type = 118001
   //          WHERE le.`legal_entity_id` = '.$id.'
   //          GROUP BY le.`legal_entity_id`';

   //      $gridData = DB::select(DB::raw($data));
   //      return $gridData;

   //   }catch(Exception $e) {
   //    return 'Message: ' .$e->getMessage();
   // }

   //  }
    public function editGridId($id){
      try{
          $query = DB::selectFromWriteConnection(DB::raw("CALL get_DcFcGridData(".$id.")"));
          return $query;
        }catch(Exception $e) {
        return 'Message: ' .$e->getMessage();
      }

    }
    public function getStates(){
        try{
          $data = "select * from zone where country_id= 99";
          $stateData = DB::select(DB::raw($data));
        return $stateData;
    }catch(Exception $e) {
      return'Message: ' .$e->getMessage();
    }

  }
    public function upDateAlldata($data,$userdataid){
        DB::beginTransaction();
          try{
        $checkbox_active = isset($data['le_check_active']) ? 1 : 0;
        $this->warehouseMapping($data,$userdataid);

        $users = DB::table('users')
                        ->where('user_id', '=',$data['user_id_data'])
                        ->update([
                            'mobile_no'      =>$data['mobile_no'],
                            'email_id'       =>$data['email_id'],
                            'firstname'      =>  $data['f_name'],
                            'is_active'      =>  $checkbox_active
                           ]);

        $legalentity_warehouses = DB::table('legalentity_warehouses')
                                  ->where('legal_entity_id','=',$data['le_hidden1_id'])
                                  ->update([
                                    'phone_no'     =>$data['mobile_no'],
                                    'email'        =>$data['email_id'],
                                    'status'       =>1,
                                  ]);

                     DB::commit();

        return $legalentity_warehouses;
    }catch(Exception $e) {
          DB::rollback();
         return 'Message: ' .$e->getMessage();
   }
  }

  public function warehouseMapping($data,$userdataid){
     if(isset($data['le_check_active']) ? 1 : 0){
           $leid = $data['le_hidden1_id'];
           $warehousesquery ="select *,getMastLookupValue(dc_type)  AS types from users As u inner join legalentity_warehouses AS lw on lw.legal_entity_id= u.legal_entity_id where user_id = $userdataid ";
           $warehousesdata = DB::select(DB::raw($warehousesquery));
           $details = $this->dcHubMappingTable($warehousesdata);
           return $details;
        }
  }

  public function GetAllUsersData($makeFinalSql, $orderBy, $page, $pageSize,$leID){
    if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'AND ' . $value;
            }elseif(count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' WHERE ' .$value;
            }
            $countLoop++;
        }
      $concat_query = 'CONCAT("<center><code>","<a href=\'javascript:void(0)\' onclick=\'updateUsersDetailsData(",user_id,")\'>
              <i class=\'fa fa-pencil\'></i></a>&nbsp;&nbsp;&nbsp;</code> </center>") AS `CustomAction`';

      $query = "SELECT *,".$concat_query." FROM `users` WHERE `legal_entity_id`=".$leID."  ". $sqlWhrCls  . $orderBy ;
      $query1 = DB::select(DB::raw($query));          
      return $query1;
  }

  public function GetWareHousesByLegalId($makeFinalSql, $orderBy, $page, $pageSize,$Le_ID){


     if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'AND ' . $value;
            }elseif(count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' WHERE ' .$value;
            }
            $countLoop++;
        }
    $warehouses ="select *,getMastLookupValue(dc_type)  AS types from users As u inner join legalentity_warehouses AS lw on lw.legal_entity_id= u.legal_entity_id where u.legal_entity_id = $Le_ID AND u.is_parent=1" .$sqlWhrCls.$orderBy;
    $warehouses = DB::select(DB::raw($warehouses));   
      return $warehouses;
  }

  public function dcHubMappingTable($warehousesdata){

  if (isset($warehousesdata[0]->legal_entity_id)){

  $id = $warehousesdata[0]->legal_entity_id;

  $leResult = DB::table('legal_entities')->select('business_legal_name')->where('legal_entity_id', '=', $id)->get()->all();


  $dcid = DB::table('legalentity_warehouses as lw')->select(['lw.le_wh_id','lw.dc_type'])->where('legal_entity_id', '=', $id)->where('dc_type', 118001)->first();

  $hubid = DB::table('legalentity_warehouses as lw')->select(['lw.le_wh_id','lw.dc_type'])->where('legal_entity_id', '=', $id)->where('dc_type', 118002)->first();

  $define = DB::table('dc_hub_mapping')->select('*')->where('dc_id', '=', $dcid->le_wh_id)->where('hub_id', '=', $hubid->le_wh_id)->get()->all();


  if (empty($define)) {
    $query = DB::table('dc_hub_mapping')->insert(['dc_id' => $dcid->le_wh_id, 'hub_id' => $hubid->le_wh_id, 'is_active' => 1]);
  }
  $data = DB::table('retailer_flat')->select('legal_entity_id')->where('legal_entity_id', '=', $id)->get()->all();
  if (empty($data)) {
    $query = DB::table('retailer_flat')->insert(['legal_entity_id' => $id]);
  }
  if (isset($warehousesdata[0]->le_wh_id)) {

    $ischeck = DB::table('spokes')->select(['le_wh_id','spoke_id'])->where('le_wh_id', '=', $hubid->le_wh_id)->first();
    if (empty($ischeck)) {
      $spoke_id = DB::table('spokes')->insertGetId(['spoke_name'=>$leResult[0]->business_legal_name,'le_wh_id' => $hubid->le_wh_id]);
    }else{
      $spoke_id = $ischeck->spoke_id;
    }

    $leWhId = $warehousesdata[0]->le_wh_id;
    $check = DB::table('pjp_pincode_area')->select('le_wh_id')->where('le_wh_id', '=', $hubid->le_wh_id)->get()->all();
    if(empty($check)) {
      $query = DB::table('pjp_pincode_area')->insert(['pjp_name'=>$leResult[0]->business_legal_name,'le_wh_id' => $hubid->le_wh_id,'spoke_id'=>$spoke_id]);
    }
    
  }
  return $query;
  }
 }
  public function getCountries(){
    $country ="select * from countries";
    $country = DB::select(DB::raw($country));
      return $country;
  }

  public function registeredData($data){
    // DB::beginTransaction();
          try{    
        $date=date('Y-m-d H:i:s');    
        $data['legal_entity_id'] = $data['le_hidden_id'];
        $userid = $data['user_hidden_id'];
        if(empty($userid)){
          return 0;die();
        }
       // db::enableQueryLog();
        $update = DB::table('legal_entities')
                    ->where('legal_entity_id', '=',         $data['legal_entity_id'] )
                    ->update([                    
                            'gstin'                     =>  $data['gstin'], 
                            'pincode'                   =>  $data['org_pincode'],
                            'business_legal_name'       =>  $data['legalentity_name'],
                            'address1'                  =>  $data['org_address1'],
                            'address2'                  =>  $data['org_address2'],
                            'city'                      =>  $data['org_city'],
                            'display_name'              =>  $data['screen_name'],
                            'is_self_tax'               =>isset($data['is_self_tax_update']),
                            'state_id'                  =>  $data['satename'],
                            'updated_by'                =>  Session::get('userId'),
                            'updated_at'                =>  $date,
                            'fssai'                     =>  $data['lic_num']
                            ]);
                    //dd(db::getQueryLog());

                     if($update){
                            $update=1;
                        }else{
                            $update=0;
                        }
                  
          /*if($update && $data['credit_limit']!=0){
            //db::enableQueryLog();
            $update=DB::table('user_ecash_creditlimit')
                    ->where('le_id','=',$data['legal_entity_id'])
                    ->where('user_id','=',$userid)
                    ->update([                    
                            'pre_approve_limit'               =>  $data['credit_limit'],
                            'approval_status'           => 0,
                            'updated_by'                =>  Session::get('userId'),
                             'updated_at'                =>  $date
                            ]);
            $checkusercreditlimit=DB::table('user_ecash_creditlimit')
                                  ->select('user_id','user_ecash_id')
                                  ->where('le_id','=',$data['legal_entity_id'])
                                  ->where('user_id','=',$userid)
                                  ->get();
             $checkusercreditlimit=json_decode(json_encode($checkusercreditlimit),true);                     
             if(!$update && empty($checkusercreditlimit[0]['user_id']) && $userid!=0){
                
                   $updateuserecash = DB::table('user_ecash_creditlimit')->insert(['user_id'=>$userid,'pre_approve_limit'=>$data['credit_limit'],'le_id'  => $data['legal_entity_id'],'created_by'=>Session::get('userId')]);

                   $userecashid = DB::getPdo()->lastInsertId();

                   if($updateuserecash){
                   $update = DB::table('user_ecash_credit_details')->insert(['user_ecash_id'=>$userecashid,'amount_requested_to_approve'=>$data['credit_limit'],'from_date'=>date('Y-m-d',strtotime($data['fromdate'])),'to_date'=>date('Y-m-d',strtotime($data['todate'])),'status'=>1,'user_id'=>$userid,'le_id'  => $data['legal_entity_id'],'created_by'=>Session::get('userId'),'created_at'=>$date]);

                        if($update){
                            $update=1;
                        }else{
                            $update=0;
                        }
                   }else{
                    $update=0;
                   }
              }else{
                $creditlimitstatusdetails=DB::table('user_ecash_credit_details')
                                             ->select('user_ecash_details_id')
                                             ->where('status',1)
                                             ->where('user_ecash_id',$checkusercreditlimit[0]['user_ecash_id'])
                                             ->get();
                 if(!empty($creditlimitstatusdetails)){
                  $updatecreditdetailsstatus=DB::table('user_ecash_credit_details')
                                              ->where('status',1)
                                             ->where('user_ecash_id',$checkusercreditlimit[0]['user_ecash_id'])
                                             ->update(['status'    => 0,
                                                       'updated_by'=>  Session::get('userId'),
                                                       'updated_at'=> $date]);
                        }
                    
                        $update = DB::table('user_ecash_credit_details')->insert(['user_ecash_id'=>$checkusercreditlimit[0]['user_ecash_id'],'amount_requested_to_approve'=>$data['credit_limit'],'from_date'=>date('Y-m-d',strtotime($data['fromdate'])),'to_date'=>date('Y-m-d',strtotime($data['todate'])),'status'=>1,'user_id'=>$userid,'le_id'  => $data['legal_entity_id'],'created_by'=>Session::get('userId'),'created_at'=>$date]);

                        if($update){
                            $update=1;
                        }else{
                            $update=0;
                        }                         
              }

            $leid = $data['legal_entity_id'];
            $le_sts= $this->legalentityStatus($userid,$leid);
            $le_sts=json_decode(json_encode($le_sts),1);
            $le_sts=(isset($le_sts[0]['approval_status']))?$le_sts[0]['approval_status']:'';
             $approval_flow = new CommonApprovalFlowFunctionModel();
             /*$approval_flow_func->storeWorkFlowHistory('Credit Limit', $leid, $le_sts, 57197, 'Credit Limit has been modified hence moving to intiated', \Session::get('userId'));*/
              /*$approvalStatusDetails = $approval_flow->getApprovalFlowDetails('Credit Limit',57197, Session::get('userId'));
              $approvalData = isset($approvalStatusDetails['data'])?$approvalStatusDetails['data']:"";
                    foreach($approvalData as $data){
                       
                        $NextStatusId  = isset($data['nextStatusId'])?$data['nextStatusId']:"";
                    }
                    $currentStatusId  = isset($approvalStatusDetails['currentStatusId'])?$approvalStatusDetails['currentStatusId']:"";

                    $approvalDataResp =  $approval_flow->storeWorkFlowHistory("Credit Limit", $userid, $currentStatusId, $NextStatusId, "Credit Limit has been modified", Session::get('userId'));
                    //dd(db::getQueryLog());
          }*/          
        // $users = DB::table('users')

        //                 ->where('legal_entity_id', '=',$data['legal_entity_id'])
        //                 ->update([
        //                     // 'mobile_no'     =>$data['mobile_no'],
        //                     // 'email_id'        =>$data['email_id'],
        //                     // 'firstname'      =>  $data['f_name'],
        //                     // // 'lastname'       =>  $data['l_name'],
        //                     // 'is_active'      =>  $checkbox_active,
        //                    ]);

        // $legalentity_warehouses = DB::table('legalentity_warehouses')
        //                           ->where('legal_entity_id','=',$data['legal_entity_id'])
        //                           ->update([
                                    // 'phone_no'     =>$data['mobile_no'],
                                    // 'email'        =>$data['email_id'],
                                    // // 'state'        =>$data['satename'],
                                    // // 'pincode'      =>$data['pincode'],
                                    // // 'city'         =>$data['city_name'],
                                  // ]);

                     // DB::commit();
          //echo $update;exit;
        return $update;
    }catch(Exception $e) {
          DB::rollback();
         return 'Message: ' .$e->getMessage();
     }
   }

   public function getStockistDetails(){

    $leResult = DB::table('legal_entities as le')
                ->select(DB::raw("CONCAT(u.firstname,' ',u.lastname) as fullname, le.legal_entity_id"))
                ->leftjoin('users as u', 'u.legal_entity_id','=','le.legal_entity_id')->where('le.legal_entity_type_id',1014)->get()->all();
    return $leResult; 

   }

    public function getDataForStockistPayments($makeFinalSql, $orderBy, $page, $pageSize,$leid){

      if($orderBy!=''){
        $orderBy = ' ORDER BY ' . $orderBy;
      }else{
        $orderBy = ' ORDER BY Order_ID DESC';
      }
      $sqlWhrCls = '';
      $countLoop = 0;
      foreach ($makeFinalSql as $value) {
        if( $countLoop==0 ){
          $sqlWhrCls .= ' AND ' . $value;
        }elseif(count($makeFinalSql)==$countLoop ){
          $sqlWhrCls .= $value;
        }else{
          $sqlWhrCls .= ' AND ' . $value;
        }
        $countLoop++;
      }
      // Intitally, we retrieve the count, and then we apply the skip.
      // To Retrieve the Count
      $stockCountSql ="select count(Le_ID) AS count from vw_stockist_orders where Le_ID = '".$leid."'". $sqlWhrCls;
      $stockCount = DB::SELECT($stockCountSql);
      $stockCount = isset($stockCount[0]->count)?$stockCount[0]->count:0;
      // Page Limits
      $limit = $this->pageLimitQuery($page,$pageSize);
      // Result Query with Limits
      $stock ="select * from vw_stockist_orders where Le_ID = '".$leid."'". $sqlWhrCls.$orderBy.$limit;
      $stock = DB::select(DB::raw($stock)); 

      return compact('stock','stockCount');
    }

   public function getstockistuserid($leid){

    $leResult = DB::table('users as u')
                ->leftjoin('user_ecash_creditlimit as uec', 'uec.user_id','=','u.user_id')
                ->where('u.legal_entity_id',$leid)
                ->where('u.is_parent',1)
                ->limit(1)
                ->get()->all();
    return $leResult; 

   }

   public function updatethecashlimit($userid,$cashback,$leid){
    // check the record exist or not 
      $checkuserid  = DB::table('user_ecash_creditlimit')->where('user_id', '=', $userid)->get()->all();
      if(count($checkuserid) > 0 ){
        $update = DB::table('user_ecash_creditlimit')->where('user_id', '=', $userid)->update(['cashback' => DB::raw('(cashback+'.$cashback.')')]);
        return 2;
      }else{
          $data  = array(
          'user_id'  => $userid,
          'creditlimit'    =>0,
          'cashback'         =>$cashback,
          'created_by'  =>Session::get('legal_entity_id'),
          'le_id'     =>$leid
      );   
        $query = DB::table('user_ecash_creditlimit')->insert($data);
        
      return 1;

      }
      
   }

   public function deductthecashlimit($userid,$cashback,$leid){
    // check the record exist or not 
      $checkuserid  = DB::table('user_ecash_creditlimit')->where('user_id', '=', $userid)->get()->all();
      if(count($checkuserid) > 0 ){
        $update = DB::table('user_ecash_creditlimit')->where('user_id', '=', $userid)->update(['cashback' => DB::raw('(cashback-'.$cashback.')')]);
        return 2;
      }else{
          $data  = array(
          'user_id'  => $userid,
          'creditlimit'    =>0,
          'cashback'         =>'-'.$cashback,
          'created_by'  =>Session::get('userId'),
          'le_id'     =>$leid
      );   
      $query = DB::table('user_ecash_creditlimit')->insert($data); 
      return 1;

      }
   }

   public function addcashbackdata($userid,$cashback,$legalEntity_Id,$modeofpayment,$p_pay_id=0,$transaction_type=143002,$comment=""){
      $this->paymentmodel = new PaymentModel(); 
      $userEcash = $this->paymentmodel->getUserEcash($userid);
      $finalEcashAmount = $userEcash->cashback;
      $data  = array(
          'user_id'  => $userid,
          'cash_back_amount'    =>$cashback,
          'comment'         =>$comment,
          'transaction_type'  =>$transaction_type,
          'legal_entity_id'   => $legalEntity_Id,
          'created_by'        => Session::get('userId'),
          'mode_type_payment' =>$modeofpayment,
          "balance_amount"  =>$finalEcashAmount,
          'pay_id'            =>$p_pay_id
      );    
       $query = DB::table('ecash_transaction_history')->insert($data);
   }

   // public function getPaymentDetailsFromView($leid){
   //  $paymentdetails ="select * from vw_stockist_payment_details where cust_le_id = '".$leid."'";
   //  $paymentdetails = DB::select(DB::raw($paymentdetails));
   //  return $paymentdetails;
   // }
  public function getPaymentDetailsFromView($leid){
      $query = DB::selectFromWriteConnection(DB::raw("CALL get_StockistPaymentDetails(".$leid.")"));
      return $query;
   }

    public function getPaymentHistory($makeFinalSql, $orderBy, $page, $pageSize,$Le_id){

      if($orderBy!='')
        $orderBy = ' ORDER BY ' . $orderBy;
      else
        $orderBy = ' ORDER BY Created_At DESC';
      $sqlWhrCls = '';
      $sqldateCls = '';
      $countLoop = 0;
      foreach ($makeFinalSql as $value) {

      if(substr_count($value,'from_date') || substr_count($value,'to_date')){
            $data = explode('=',$value);

            $date = isset($data[1])?trim($data[1]):'';
            if($sqldateCls==''){
              $sqldateCls = ' AND transaction_date between "'.date('Y-m-d', strtotime($date)).'"';
            }else{
              $sqldateCls .= ' AND "'.date('Y-m-d', strtotime($date)).'"';
            }
          }elseif($countLoop==0){
              $sqlWhrCls .= ' AND ' . $value;
          }elseif(count($makeFinalSql)==$countLoop ){
              $sqlWhrCls .= $value;
          }else{
              $sqlWhrCls .= ' AND ' . $value;
          }
          $countLoop++;
      } 
      $sqlWhrCls = str_replace("Mode_Type", "CONVERT(Mode_Type USING utf8)", $sqlWhrCls);   
      $sqlWhrCls .= $sqldateCls;
      // Intitally, we retrieve the count, and then we apply the skip.
      // To Retrieve the Count
      // $usersID = DB::table('user_ecash_creditlimit')->select('user_id')->where('le_id' ,'=', $Le_id)->first();
      $userID = DB::select("select getLeParentUser($Le_id) AS User_ID");
      if($userID){
        $query = "select count(user_id) as count from vw_stockist_payment_history where user_id=".$userID[0]->User_ID." ". $sqlWhrCls.$orderBy;
        
        $resultCount = DB::SELECT($query);
        $resultCount = isset($resultCount[0]->count)?$resultCount[0]->count:0;
        // Page Limits
        $limit = $this->pageLimitQuery($page,$pageSize);
        $actions='';
        $checkfeaturefordelete=$this->roleAccess->checkPermissionByFeatureCode('PAYD001');
        if($checkfeaturefordelete==1){
          $actionstring = '<a class="Delete deletePayment" data-pay_id="';
          $actionstring1 = '" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a>';
          $actions .= ",CONCAT('".$actionstring."',pay_id,'".$actionstring1."') AS action";
        }else{
          $actionstring = "";
          $actionstring1 = "";
        }
        // Result Query with Limits
       $resultQuery ="SELECT * ".$actions."
         FROM vw_stockist_payment_history where user_id=".$userID[0]->User_ID." ". $sqlWhrCls.$orderBy.$limit;
        $result = DB::SELECT($resultQuery);
      }
      return compact('result','resultCount');
   }

    function pageLimitQuery($page,$pageSize=100){
      if($page == "0")
        $limit = " LIMIT 0,".$pageSize;
      else
        $limit = " LIMIT ".(int)$page * (int)$pageSize.",".$pageSize;
      return $limit;
    }

   public function savePaymnetsInVouchertable($data){
    try{
    $str = $data['paid_through_stockist'];
    $pieces = explode("===", $str);
    $pay_code = $data['pay_code'];
    //echo "<pre/>";print_r($pieces[1]);exit;
        $le_id = $data['legalentity_id'];
        $cost_centre = "Z1R1";
        $cost_centre_group = "Z1R1";
        $stockist_name = $this->getTheStockistName($le_id);
        $fcmap = DB::table('dc_fc_mapping')
        ->select(['dc_le_wh_id','dc_le_id'])
        ->where('fc_le_id','=',$le_id)
        ->first();
        if(count($fcmap)>0){
          $le_wh_id = $fcmap->dc_le_wh_id;
          $bu_mapped = DB::table('legalentity_warehouses')
          ->select(['bu_id'])
          ->where('le_wh_id','=',$fcmap->dc_le_wh_id)
          ->first();
          if(isset($bu_mapped->bu_id) && $bu_mapped->bu_id!=""){
            $budetails = $this->getBuDetails($bu_mapped->bu_id);
            $cost_centre = isset($budetails->cost_center)?$budetails->cost_center:"";
            $parent_bu_id = isset($budetails->parent_bu_id)?$budetails->parent_bu_id:"";
            $pbudetails = $this->getBuDetails($parent_bu_id);
            $cost_centre_group = isset($pbudetails->cost_center)?$pbudetails->cost_center:"";
          }
        }

        $saveData[0] = array(
            "voucher_code"                      =>  $pay_code,
            "voucher_type"                      =>  "Receipt",
            "voucher_date"                      =>  $data['transmission_date'],
            "ledger_group"                      =>  "Sundry Debtors",
            "ledger_account"                    =>  $stockist_name,
            "tran_type"                         =>  'Cr',
            "amount"                            =>  $data['payment_amount_stockist'],
            "naration"                         =>   0,
            "cost_centre"                       =>  $cost_centre,
            "cost_centre_group"                 =>  $cost_centre_group,
            "reference_no"                      =>  $pay_code,
            "is_posted"                         =>  0
        );

        $saveData[1] = array(
            "voucher_code"                      =>  $pay_code,
            "voucher_type"                      =>  "Receipt",
            "voucher_date"                      =>  $data['transmission_date'],
            "ledger_group"                      =>  $pieces[1],
            "ledger_account"                    =>  $pieces[0],
            "tran_type"                         =>   'Dr',
            "amount"                            =>  $data['payment_amount_stockist'],
            "naration"                          =>  "Being Money received from " . $stockist_name ." as Transaction Reference No ". $pay_code,
            "cost_centre"                       =>  $cost_centre,
            "cost_centre_group"                 =>  $cost_centre_group,
            "reference_no"                      =>  $pay_code,
            "is_posted"                         =>  0
        );
        $save = DB::table("vouchers") 
            ->insert($saveData);

        return 1;
    }
    catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    }   
    }

    public function getTheStockistName($leid){
      $group =  DB::table("users as u")
                        ->select('le.business_legal_name','le.le_code')
                        ->leftjoin('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
                        ->where('le.legal_entity_id', '=', $leid)
                        ->first();
      if( !empty($group) ){
            return $group->business_legal_name ." - ". $group->le_code;
        }else{
            return "";
        }

    }
    public function getBuDetails($bu_id){
      $bu_data = DB::table('business_units')
            ->select(['bu_id','parent_bu_id','cost_center'])
            ->where('bu_id','=',$bu_id)
            ->first();
      return $bu_data;
    }
    public function getpaymenttypes(){
      $query = DB::table('master_lookup')->whereIn("value",[16501,16502])->get()->all();
      return $query;
    }
    public function editGridUsersId($id){
        $query = DB::table('users')->select('*')->where('user_id','=',$id)->first();
        return $query;
    }
    public function dcFCTypeFromLp(){
      $query = DB::table('master_lookup')->select(['master_lookup_id','master_lookup_name','description','value'])->where('mas_cat_id','=',1)
                                                     ->whereIn('value',[1014,1016])                                                 
                                                     ->get()->all();
      return $query;
    }
    public function upLoadPathInToDB($docsArr){
        try {
            $id = DB::table('legal_entity_docs')->insertGetId($docsArr);
            return $id;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    
    }
    public function legalEntityDoc($id){
        try {
            $fieldArr = array('legal_entity_docs.*');
            
            $query = DB::table('legal_entity_docs')->select('legal_entity_docs.*',DB::raw("getMastLookupValue(legal_entity_docs.doc_type) as doc_type"),DB::raw("GetUserName(legal_entity_docs.created_by,2) as fullname"));
            $query->where('legal_entity_docs.legal_entity_id', $id);
            return $query->get()->all();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    public function deleteRecord($id){
        try {
            DB::table('legal_entity_docs')->where('doc_id', '=', $id)->delete();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }

    public function legalentityStatus($userid,$leid){

      $query=DB::table('user_ecash_creditlimit')->select(['approval_status'])->where('le_id','=',$leid)->where('user_id','=',$userid)->get()->all();

      return $query;
    }

    public function getCitiesForStates($stateid){

      try{

        //get state code,based on state code we can get city name
      $statename=DB::table('zone')
                 ->select('gst_state_code')
                 ->where('zone_id',$stateid)
                 ->get()->all();
         $statename=json_decode(json_encode($statename),true);
         
        $getcitybystatecode=DB::table('state_city_codes')
                            ->select('scc_id','city_name')
                            ->where('state_code',$statename[0]['gst_state_code'])
                            ->get()->all();
       return $getcitybystatecode;
    }catch(Exception $e){
        return 'Message: ' .$e->getMessage();
      }
  }

  public function getCodeForDcFc($data){

      try{

        $statecode=DB::table('zone')
                 ->select('gst_state_code')
                 ->where('zone_id',$data['stateid'])
                 ->get()->all();
         $statecode=json_decode(json_encode($statecode),true);

        //get city code based on scc_id in state city codes
        $citycode=DB::table('state_city_codes')
                 ->select('city_code')
                 ->where('scc_id',$data['ctyid'])
                 ->get()->all();
         $citycode=json_decode(json_encode($citycode),true);
    
       if(!empty($data['dcfcid'])){
             $flag=$this->getDcFcTypeById($data['dcfcid']); 
        }else{
             $getlegalentitytypebylegalentityid=DB::table('legal_entities')
                                               ->select('legal_entity_type_id')
                                               ->where('legal_entity_id',Session::get('legal_entity_id'))
                                               ->get()->all();
            $getlegalentitytypebylegalentityid=json_decode(json_encode($getlegalentitytypebylegalentityid),true);
            $flag=$this->getDcFcTypeById($getlegalentitytypebylegalentityid[0]['legal_entity_type_id']);                                   
        }
       $flag=json_decode(json_encode($flag),true); 
        Session::put('dcfc_legalentitytype', $flag[0]['master_lookup_name']);
        Session::put('state_code', $statecode[0]['gst_state_code']);
        Session::put('city_code', $citycode[0]['city_code']); 
        $response = DB::selectFromWriteConnection(DB::raw('CALL getDCFCCode("'.$statecode[0]['gst_state_code'].'","'.$citycode[0]['city_code'].'","'.$flag[0]['master_lookup_name'].'")'));
       return $response;
    }catch(Exception $e){
        return 'Message: ' .$e->getMessage();
      }
  }

  public function getDcFcTypeById($dcfcid){
      $query = DB::table('master_lookup')
                  ->select(['master_lookup_name'])
                  ->where('mas_cat_id','=',1)
                  ->where('value',$dcfcid)
                  ->get()->all();
      return $query;
    }


   public function getFcDcs($dcid=''){
    $fcDc=DB::table('legalentity_warehouses as lw')
                ->leftJoin('legal_entities as l', 'l.legal_entity_id', '=', 'lw.legal_entity_id')->groupBy('l.legal_entity_id')->where('dc_type','=',118001)->where('l.legal_entity_type_id','=',1016)->where('l.is_virtual','=',0);
                if($dcid!=''){
                 $fcDc= $fcDc->where('le_wh_id',$dcid);
                }
               $fcDc=$fcDc->get()->all();
         return $fcDc;
              }

   public function getLegalentityForDc($dcid){

    $leid= DB::table('legalentity_warehouses')
               ->select('legal_entity_id')
               ->where('le_wh_id',$dcid)
               ->get()->all();

          return $leid;     
   }  

   public function getCityName($sccid){

    $ctyname= DB::table('state_city_codes')
               ->select('city_name')
               ->where('scc_id',$sccid)
               ->get()->all();

          return $ctyname;     
   }         

    public function stockistCreditLimitInsert($data){

        $date=date('Y-m-d H:i:s');
        $data['legal_entity_id'] = $data['stockist_le_id'];
        $userid = $data['stockist_user_id'];

            if(empty($userid))
            {
              echo 'No User Found';die();
            }

        $leid = $data['legal_entity_id'];
        $le_sts= $this->legalentityStatus($userid,$leid);
        $le_sts=json_decode(json_encode($le_sts),1);
        $le_sts=(isset($le_sts[0]['approval_status']))?$le_sts[0]['approval_status']:'';
        $approval_flow = new CommonApprovalFlowFunctionModel();
        $approvalStatusDetails = $approval_flow->getApprovalFlowDetails('Credit Limit',57197, Session::get('userId'));
        $approvalData = isset($approvalStatusDetails['data'])?$approvalStatusDetails['data']:"";

            if(empty($approvalData)){
                echo "You don't have permission to Update Credit Limit";die();
              }

            if(($data['credit_limit'])>=0){

              $checkusercreditlimit=DB::table('user_ecash_creditlimit')
                                    ->select('user_id','user_ecash_id','approval_status','pre_approve_limit')
                                    ->where('le_id','=',$data['legal_entity_id'])
                                    ->where('user_id','=',$userid)
                                    ->get()->all();
              $checkusercreditlimit=json_decode(json_encode($checkusercreditlimit),true);

            
                $update=DB::table('user_ecash_creditlimit')
                              ->where('le_id','=',$data['legal_entity_id'])
                              ->where('user_id','=',$userid)
                              ->update([                    
                                      'pre_approve_limit'   =>  $data['credit_limit'],
                                      'approval_status'     => 57197,
                                      'updated_by'          =>  Session::get('userId'),
                                       'updated_at'         =>  $date
                                      ]);

          $description='Amount of '.$data['credit_limit'].' has been Initiated for Credit Limit';                

        if(!$update && empty($checkusercreditlimit[0]['user_id']) && $userid!=0){

                
                   $updateuserecash = DB::table('user_ecash_creditlimit')->insert(['user_id'=>$userid,'pre_approve_limit'=>$data['credit_limit'],'le_id'  => $data['legal_entity_id'],'created_by'=>Session::get('userId')]);

                    $userecashid = DB::getPdo()->lastInsertId();

                  if($updateuserecash){
                        $update = DB::table('user_ecash_credit_details')->insert(['user_ecash_id'=>$userecashid,'amount_requested_to_approve'=>$data['credit_limit'],'from_date'=>date('Y-m-d',strtotime($data['fromdate'])),'to_date'=>date('Y-m-d',strtotime($data['todate'])),'status'=>0,'user_id'=>$userid,'le_id'  => $data['legal_entity_id'],'created_by'=>Session::get('userId'),'created_at'=>$date,'description'=>$description]);

                        if($update){
                              $update='Credit Limit has been Initiated';
                        }else{
                              $update='Failed to Initiate Credit Limit';
                        }
                   }else{
                    $update='Failed to Initiate Credit Limit';
                   }
              }else{
                /*$creditlimitstatusdetails=DB::table('user_ecash_credit_details')
                                             ->select('user_ecash_details_id')
                                             ->where('status',1)
                                             ->where('user_ecash_id',$checkusercreditlimit[0]['user_ecash_id'])
                                             ->get();
                 if(!empty($creditlimitstatusdetails)){
                  $updatecreditdetailsstatus=DB::table('user_ecash_credit_details')
                                              ->where('status',1)
                                             ->where('user_ecash_id',$checkusercreditlimit[0]['user_ecash_id'])
                                             ->update(['status'    => 0,
                                                       'updated_by'=>  Session::get('userId'),
                                                       'updated_at'=> $date]);
                        }*/
                      $userecashid=$checkusercreditlimit[0]['user_ecash_id'];      
                        $update = DB::table('user_ecash_credit_details')->insert(['user_ecash_id'=>$checkusercreditlimit[0]['user_ecash_id'],'amount_requested_to_approve'=>$data['credit_limit'],'from_date'=>date('Y-m-d',strtotime($data['fromdate'])),'to_date'=>date('Y-m-d',strtotime($data['todate'])),'status'=>0,'user_id'=>$userid,'le_id'  => $data['legal_entity_id'],'created_by'=>Session::get('userId'),'created_at'=>$date,'description'=>$description]);

                        if($update){
                            $update='Credit Limit has been Initiated';
                        }else{
                            $update='Failed to Initiate Credit Limit';
                        }                         
              }

            /*$leid = $data['legal_entity_id'];
            $le_sts= $this->legalentityStatus($userid,$leid);
            $le_sts=json_decode(json_encode($le_sts),1);
            $le_sts=(isset($le_sts[0]['approval_status']))?$le_sts[0]['approval_status']:'';
             $approval_flow = new CommonApprovalFlowFunctionModel();
              $approvalStatusDetails = $approval_flow->getApprovalFlowDetails('Credit Limit',57197, Session::get('userId'));
              $approvalData = isset($approvalStatusDetails['data'])?$approvalStatusDetails['data']:"";

              if(empty($approvalData)){
                return "You don't have permission to Update Credit Limit";
              }*/
                    foreach($approvalData as $data){
                       
                        $NextStatusId  = isset($data['nextStatusId'])?$data['nextStatusId']:"";
                    }
                    $currentStatusId  = isset($approvalStatusDetails['currentStatusId'])?$approvalStatusDetails['currentStatusId']:"";

                    $approvalDataResp =  $approval_flow->storeWorkFlowHistory("Credit Limit", $userecashid, $currentStatusId, $NextStatusId, "Credit Limit has been modified", Session::get('userId'));

                    return $update;
                   
          }

    }
    public function stockistCreditDebitInsert($data){
      $date=date('Y-m-d H:i:s');
      $data['legal_entity_id'] = $data['stockist_le_id'];
      $userid = $data['stockist_user_id'];
      if(empty($userid)){
        echo 'No User Found';die();
      }
      if(empty($data['trans_date'])){
        $data['trans_date'] = date('Y-m-d H:i:s');
      }
      $leid = $data['legal_entity_id'];
      $le_sts= $this->legalentityStatus($userid,$leid);
      $le_sts=json_decode(json_encode($le_sts),1);
      $le_sts=(isset($le_sts[0]['approval_status']))?$le_sts[0]['approval_status']:'';
      $approval_flow = new CommonApprovalFlowFunctionModel();
      $approvalStatusDetails = $approval_flow->getApprovalFlowDetails('Credit or Debit Note',57208, Session::get('userId'));
      $approvalData = isset($approvalStatusDetails['data'])?$approvalStatusDetails['data']:"";

      if(empty($approvalData)){
        echo "You don't have permission to Update Credit or Debit Note";die();
      }
      if(($data['payment_amount_stockist'])>=0){
        // $checkcreditdebit=DB::table('credit_debit_note')
        //                   ->where('ref_no','=',$data['payment_ref'])
        //                   ->get();
        // $checkcreditdebit=json_decode(json_encode($checkcreditdebit),true);
        // $update=DB::table('credit_debit_note')
        //         ->where('ref_no','=',$data['payment_ref'])
        //         ->update([                    
        //             'amount'   =>  $data['payment_amount_stockist'],
        //             'approval_status'     => 57208,
        //             'updated_by'          =>  Session::get('userId'),
        //             'updated_at'         =>  $date
        //           ]);
        //$cdlimit = $checkcreditdebit[0]['cdID'];
        //if(!$update && empty($checkcreditdebit) && $userid!=0){
          $updatecreditdebit = DB::table('credit_debit_note')->insert(['transaction_type'=>$data['payment_type'],'approval_status'=>57208,'amount'=>$data['payment_amount_stockist'],'trans_date'  => $data['trans_date'],'ref_no'=> $data['payment_ref'],'mode_of_deposit'=>$data['mode_payment'],'business_legal_name'=>$data['legalentity_id'],'created_by'=>Session::get('userId')]);
          
          $cdlimit = DB::getPdo()->lastInsertId();
                
                if($updatecreditdebit){
                  $update='Credit Debit Note has been Initiated';
                }else{
                    $update='Failed to Initiate Credit Debit Note';
                }
              //}
              foreach($approvalData as $data){
                $NextStatusId  = isset($data['nextStatusId'])?$data['nextStatusId']:"";
              }
              $currentStatusId  = isset($approvalStatusDetails['currentStatusId'])?$approvalStatusDetails['currentStatusId']:"";
              $approvalDataResp =  $approval_flow->storeWorkFlowHistory("Credit or Debit Note", $cdlimit, $currentStatusId, $NextStatusId, "Credit or Debit Note has been modified", Session::get('userId'));
              return $update;     
            }
          }
    public function getStateCode($stId){
    $query = DB::table('zone')->select('*')->where('zone_id','=',$stId)->first();
    return $query;
  }
  public function generatingCostCenter($data){
      try{
          $bu_id = isset($data['bu_id'])?$data['bu_id']:0;
          $dc_fc = isset($data['dc_fc'])?$data['dc_fc']:0; 
                        
          $costcenter = "";
          if($bu_id>0 && $dc_fc!=''){
            $budata = DB::table('business_units')->select(['bu_id','cost_center'])->where('bu_id',$bu_id)->first();
            $bu_costcenter = isset($budata->cost_center)?$budata->cost_center:"";
            $buchildcount = DB::table('business_units')->select(['bu_id','cost_center'])->where('parent_bu_id',$bu_id)->count();
            $is_exist = 1;
            if($dc_fc==1014){
              $bucount = $buchildcount;
              $bu_costcenter .='F';
            }else if($dc_fc==1016){
              $bucount = $buchildcount+1;
              $bu_costcenter .='D';
            }
            while ($is_exist==1) {
              $costcenter =$bu_costcenter.$bucount;
              $check =  DB::table('business_units')->select('cost_center')->where('cost_center',$costcenter)->first();
              if(count($check)>0){
                $bucount++;
              }else{
                $is_exist=0;
              }
            }

          }else{
            $dcfcs=$this->getBussinessUnit($dc_fc);
            echo json_encode(array('status' => '200', 'bu_list' => $dcfcs,'flag'=>'1'));die();
          }
          echo json_encode(array('status' => '200', 'costcenter' => $costcenter,'flag'=>'0'));die();          
      //return $costcenter;
    }catch(Exception $e) {
    return 'Message: ' .$e->getMessage();
   }
  }
  public function gettingStateID($id){
    $stateID = DB::table('legal_entities AS lg')->select(['lg.state_id','zn.code'])
                                         ->join('zone AS zn','zn.zone_id','=','lg.state_id')
                                         ->where('lg.legal_entity_id','=',$id)
                                         ->first();
    return $stateID;
  }


    public function getApprovedCreditLimit($userid,$leid){
      try{

        $creditdetails=DB::table('user_ecash_creditlimit')
                       ->select('creditlimit')
                       ->where('user_id',$userid)
                       ->where('le_id',$leid)
                       ->first();
          return $creditdetails;
      }catch(Exception $e) {
         return 'Message: ' .$e->getMessage();
      }
    }
  public function deletePayment($pay_id) {
    try {
            
      $query = DB::table('vouchers')->select('voucher_code')->join('payment_details','payment_details.pay_code','=','vouchers.voucher_code')->where('payment_details.pay_id','=',$pay_id)->where('vouchers.is_posted',1)->first();
        if(!count($query)){
          $query= DB::selectFromWriteConnection(DB::raw("CALL remove_Payment(".$pay_id.")"));
          return true;
        }
        else{
           return false;
        }

      } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getPaymentLedger($makeFinalSql, $orderBy, $page, $pageSize,$legalentity_id){
      $orderBy = ' ORDER BY eth.created_at DESC';
      $sqlWhrCls = '';
      $sqldateCls = '';
      $countLoop = 0;
      foreach ($makeFinalSql as $value) {
      if(substr_count($value,'from_date') || substr_count($value,'to_date')){
            $data = explode('=',$value);           
            $date = isset($data[1])?trim($data[1]):'';
            if($sqldateCls==''){
              $date=str_replace("/","-",$date);
              $sqldateCls = ' AND date(transaction_date) between "'.date('Y-m-d', strtotime($date)).'"';
            }else{
              $date=str_replace("/","-",$date);
              $sqldateCls .= ' AND "'.date('Y-m-d', strtotime($date)).'"';
            }
          }elseif($countLoop==0){
              $sqlWhrCls .= ' AND ' . $value;
          }elseif(count($makeFinalSql)==$countLoop ){
              $sqlWhrCls .= $value;
          }else{
              $sqlWhrCls .= ' AND ' . $value;
          }
          $countLoop++;
      } 
        $limit = $this->pageLimitQuery($page,$pageSize);
        // Result Query with Limits
        $userInfo = DB::table('users')->select('user_id')
                  ->where('legal_entity_id','=',$legalentity_id)
                  ->where('is_parent','=',1)->first();
        $userId = isset($userInfo->user_id)? $userInfo->user_id:0;         
        $resultQuery ="SELECT eth.ecash_transaction_id,eth.user_id,eth.legal_entity_id,eth.cash_back_amount,eth.transaction_type,eth.pay_id,eth.transaction_date,eth.comment,eth.created_at,
        (CASE WHEN order_id IS NOT NULL THEN gds_orders.`order_code` 
         WHEN order_id IS NULL THEN pd.`pay_code` END) AS `reference_no`,
        (CASE WHEN transaction_type=143001 THEN cash_back_amount ELSE '' END) AS `dr_amount`,
        (CASE WHEN transaction_type=143002 THEN cash_back_amount ELSE '' END) AS `cr_amount`,
        eth.balance_amount
        FROM  ecash_transaction_history as eth
        LEFT JOIN gds_orders ON gds_orders.`gds_order_id` = eth.order_id
        LEFT JOIN payment_details pd ON pd.`pay_id` = eth.pay_id
        WHERE user_id=".$userId." AND eth.is_deleted=0 ". $sqlWhrCls.$sqldateCls.$orderBy;
        $countQuery=$resultQuery;
        if($limit!=''){
          $resultQuery.=$limit;
        }
        $result = DB::SELECT($resultQuery);
        // $countQuery ="select count(*) as total from  ecash_transaction_history where user_id=".$userId." AND is_deleted=0 ". $sqlWhrCls." ".$sqldateCls;
        $resultCount = DB::SELECT($countQuery);
        $total = count($resultCount);
       $dd =  compact('result','total');
       return $dd;
   }
    public function getBussinessUnit($dc_fc){
          $bussinesUnits = DB::select('CALL get_legal_entity_business_units("'.$dc_fc.'")');
           foreach($bussinesUnits as  $units)
            { 
                $this->bussinessUnitList.= '<option value="'.$units->bu_id.'" value_type="'.$units->legal_entity_type_id.'"> '.$units->bu_name.'</option>';
            }
        return $this->bussinessUnitList;
    }
    public function exportDataDownload($legalEntityID,$fromDate,$toDate){
        $query = DB::select("CALL get_fc_payment_export_ledger('".$legalEntityID."','".$fromDate."','".$toDate."')");
        return $query;
    }

    public function getCreditLimitHistory($makeFinalSql, $orderBy, $page, $pageSize,$legalentity_id){
      //$orderBy = ' ORDER BY ecash_transaction_id DESC';
      $sqlWhrCls = '';
      $sqldateCls = '';
      //$sqldateCls2='';
      $countLoop = 0;
      foreach ($makeFinalSql as $value) {
      if(substr_count($value,'from_date') || substr_count($value,'to_date')){
            $data = explode('=',$value);           
            $date = isset($data[1])?trim($data[1]):'';
            if($sqldateCls==''){
              $date=str_replace("/","-",$date);

              $sqldateCls = ' AND ("'.date('Y-m-d', strtotime($date)).'" BETWEEN From_Date AND To_Date';
            }else{
              $date=str_replace("/","-",$date);
              $sqldateCls .= ' OR "'.date('Y-m-d', strtotime($date)).'" BETWEEN From_Date AND To_Date)';
            }
          }elseif($countLoop==0){
              $sqlWhrCls .= ' AND ' . $value;
          }elseif(count($makeFinalSql)==$countLoop ){
              $sqlWhrCls .= $value;
          }else{
              $sqlWhrCls .= ' AND ' . $value;
          }
          $countLoop++;
      } 
        $limit = $this->pageLimitQuery($page,$pageSize);
        // Result Query with Limits        
        $resultQuery ="select * from vw_creditlimit_history where legal_entity_id=".$legalentity_id . $sqlWhrCls.$sqldateCls.$orderBy.$limit;
        $result = DB::SELECT($resultQuery);
        $i =0;
        $result = json_decode(json_encode($result),1);
        foreach ($result as $results) {
          $actions = '';
          $editPermission = $this->roleAccess->checkPermissionByFeatureCode("RCL01");
          $details = DB::table('user_ecash_credit_details')->select('status','updated_status')->where('user_ecash_details_id',$result[$i]['user_ecash_details_id'])->get()->all();
          $updated_status = $details[0]->updated_status;
          $status = $details[0]->status;
          if($editPermission && $updated_status==0 && $status==1){
            $actions.= '<span class="actionsStyle" style="margin-right:10px;" ><a onclick="editCreditLimit('.$results['user_ecash_details_id'].')"</a> <button type="submit" class="btn green-meadow btnn supp_info" id="limit_submit">Expire</button></span> ';
            $actions.= '<span class="actionsStyle" ><a onclick="editCreditHistory('.$results['user_ecash_details_id'].')"</a><i class="fa fa-pencil"></i></span> ';
          }
          $result[$i++]['actions'] = $actions;
        }
        $countQuery ="select count(*) as total from  vw_creditlimit_history where legal_entity_id=".$legalentity_id." ". $sqlWhrCls." ".$sqldateCls;
        $resultCount = DB::SELECT($countQuery);
        $total = $resultCount[0]->total;
       $dd =  compact('result','total');
       return $dd;
   }

   public function getLegalEntity($legal_entity_id){
      if($legal_entity_id != ""){
        $data = DB::table("legal_entities")
                ->where("legal_entity_id",$legal_entity_id)
                ->first();
        return $data;
      }else{
        return array();
      }
    }

    public function getDocumentTypes() {
        try {
            $fields = array('lookup.value','lookup.master_lookup_name');
                        $query = DB::table('master_lookup as lookup')->select($fields);
                        $query->where('lookup.mas_cat_id',188);
                        return $query->pluck('lookup.master_lookup_name','lookup.value')->all();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    
    public function getLoginUserInfo() {
        try{
            $userId = Session::get('userId');//Session('userId'),
            $fieldArr = array('users.*');
            $query = DB::table('users')->select($fieldArr);
            $query->where('users.user_id', $userId);
            $userdata = $query->first();
            return $userdata;
        }
        catch(Exception $e) {

        }
    }
    public function getDocumentById($id) {
        try {
            $fieldArr = array('legal_entity_docs.*');
            
            $query = DB::table('legal_entity_docs')->select($fieldArr);           
            $query->where('legal_entity_docs.doc_id', $id);
            return $query->first();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    public function getleDocDetails($id) {
        try
        {
            $result = [];
            if($id > 0)
            {
                $result = DB::table('legal_entity_docs')
                        ->where('legal_entity_id', $id)
                        ->select('doc_name','legal_entity_docs.created_at as created_at', 
                                'doc_url', 'doc_type',DB::raw('GetUserName(legal_entity_docs.created_by, 2) as created_by'), 
                                'doc_id')
                        ->get()->all();
            }
            return $result;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}