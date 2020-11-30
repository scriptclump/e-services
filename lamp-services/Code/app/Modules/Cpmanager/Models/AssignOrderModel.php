<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Cpmanager\Views\order;
use App\Modules\Cpmanager\Models\MasterLookupModel;
use DB;
use views;
use view;
use Config;
use App\Modules\Roles\Models\Role;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Central\Repositories\RoleRepo;



class AssignOrderModel extends Model
{
  public function getOrderInfo($orderIds, $fields, $resType='all') {
      try {
            $query = DB::table('gds_orders as orders')->select($fields);
            $query->whereIn('orders.gds_order_id', $orderIds);
            
            if($resType == 'all') {
              return $query->get()->all();
            }
            else if($resType == 'first') {
              return $query->first();
            }           
      }
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }

  public function verifyDocketNo($docketNo, $orderIds=array(), $count=true) {
        try{

            $fields = array('track.gds_order_id');
            $query = DB::table('gds_order_track as track')->select($fields);
            $query->where('track.st_docket_no', $docketNo);
            if(count($orderIds)) {
              $query->whereIn('track.gds_order_id', $orderIds);
            }

            if($count) {
                return $query->count();
            }
            else {
                return $query->get()->all();
            }        
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }

    public function verifyReturnDocketNo($docketNo, $orderIds) {
        try {
            $query = DB::table('gds_order_track')
                     ->select(['gds_order_id'])
                     ->where('rt_docket_no', $docketNo)
                     ->whereIn('gds_order_id', $orderIds)
                     ->count();
            return $query;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function verifyStockInTransitByDocketNo($docketNo, $orders=array(), $count=true) {
        try{
            $fields = array('orders.order_status_id');
            $query = DB::table('gds_orders as orders')->select($fields);
            $query->leftJoin('gds_order_track as track', 'orders.gds_order_id', '=', 'track.gds_order_id');
            $query->where('orders.order_status_id', '17025');
            $query->where('track.st_docket_no', $docketNo);
            if(count($orders)) {
              $query->whereIn('track.gds_order_id', $orders); 
            }

            if($count) {
                return $query->count();
            }
            else {
                return $query->get()->all();
            }
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }

  public function verifySITDCByDocketNo($docketNo, $orders=array(), $count=true) {
        try{
            $fields = array('orders.order_status_id');
            $query = DB::table('gds_orders as orders')->select($fields);
            $query->leftJoin('gds_order_track as track', 'orders.gds_order_id', '=', 'track.gds_order_id');
            $query->where('orders.order_transit_status', '17028');
            $query->where('track.rt_docket_no', $docketNo);
            if(count($orders)) {
              $query->whereIn('track.gds_order_id', $orders); 
            }

            if($count) {
                return $query->count();
            }
            else {
                return $query->get()->all();
            }
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }



  public function updateTrackDetailByOrderId($orderId, $fields) {
        try{

            DB::table('gds_order_track')->where('gds_order_id', $orderId)->update($fields);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }

  public function insertTrackHistoryByOrderId($orderId, $fields, $status=17027) {
        DB::beginTransaction(); 
        try{

            $ordFields = array('gds_order_id','le_wh_id','hub_id');
            $orderDetail = $this->getOrderInfo(array($orderId), $ordFields);
            
            if($status==17027)
             { 
            $fields['from_wh_id'] = $orderDetail[0]->hub_id;
            $fields['to_wh_id'] = $orderDetail[0]->le_wh_id;
             }else{
            $fields['from_wh_id'] =  $orderDetail[0]->le_wh_id;
            $fields['to_wh_id'] =$orderDetail[0]->hub_id;
             }
            $fields['gds_order_id'] = $orderDetail[0]->gds_order_id;
            
            $fields['status'] = $status;
            $fields['created_by'] = Session('userId');
            $fields['created_at'] = date('Y-m-d H:i:s');

            DB::table('gds_stock_transfer_history')->insert($fields);
          DB::commit();
        }
        catch(Exception $e) {
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }

  public function insertTrackHistoryForConfirmStock($fields) {
        try{
            DB::table('gds_stock_transfer_history')->insert($fields);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }

  public function getReturnStatusByOrderId($orderIds) {
        try{
          $query = DB::table('gds_returns as returns')->select(array('returns.gds_order_id', 'returns.return_status_id'));
          $query->whereIn('returns.gds_order_id', $orderIds);
          $query->groupBy('returns.gds_order_id');
          $returnsArr = $query->get()->all();
          $dataArr = array();
          if(count($returnsArr)) {
            foreach ($returnsArr as $return) {
             $dataArr[$return->gds_order_id] = $return->return_status_id;
            }
          }
          return $dataArr;
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }  

  public function getPendingCollectionDate($userId) {
        try{
          $query = DB::table('collection_history')
                  ->leftJoin('remittance_mapping as mapping','mapping.collection_id','=','collection_history.collection_id')
                  ->select(array(DB::raw('MIN(DATE(collected_on)) as collected_on')));
          $query->where('collected_by', $userId)->whereNull('mapping.remittance_id');
          $resultArr = $query->get()->all();
          
          if(empty($resultArr) || $resultArr[0]->collected_on=='') {
            return date('Y-m-d');
          }

          return $resultArr[0]->collected_on;

        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }
  public function getPendingCollectionHI($userId,$apprStatus) {
        try{
          $query = DB::table('collection_history')
                  ->leftJoin('remittance_mapping as mapping','mapping.collection_id','=','collection_history.collection_id')        
                  ->leftJoin('collection_remittance_history as rmhistory','rmhistory.remittance_id','=','mapping.remittance_id')        
                  ->leftJoin('collections','collections.collection_id','=','collection_history.collection_id')        
                  ->leftJoin('gds_orders','gds_orders.gds_order_id','=','collections.gds_order_id')        
                  ->leftJoin('gds_orders_payment','gds_orders_payment.gds_order_id','=','gds_orders.gds_order_id')        
                  ->select(array(DB::raw('COUNT(mapping.collection_id) as count')));
          $query->where('collected_by', $userId)->whereIn('rmhistory.approval_status', $apprStatus)
                  ->where('gds_orders_payment.payment_status_id',32003)
                  ->whereIn('gds_orders.order_status_id',[17007,17023]);
          $resultArr = $query->get()->all();
          if(isset($resultArr[0]->count) && $resultArr[0]->count>0){
              return 0;
          }else{
              return 1;
          }          
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }  


  public function getAssignedVerificationList($data){

    $start = date('Y-m-d 00:00:00', strtotime($data['from_time']));
    $end = date('Y-m-d 23:59:59', strtotime($data['to_time']));
    $start=trim($start);
    $end=trim($end);

        $query="SELECT go.order_code,go.gds_order_id, go.hub_id, lw.lp_wh_name, got.`checker_id`, CONCAT(u.`firstname`,' ',u.`lastname`) AS checker_name FROM gds_order_track got
              JOIN picker_container_mapping pcm ON got.gds_order_id = pcm.order_id
              JOIN gds_orders go ON go.gds_order_id = got.gds_order_id 
              JOIN legalentity_warehouses lw ON lw.le_wh_id = go.hub_id
              JOIN users u ON u.`user_id`=got.`checker_id`
              WHERE pcm.is_verified = '0' 
              AND got.checker_id IS NOT NULL AND go.`order_status_id` in (17005,17021)
              AND go.order_date BETWEEN '$start' AND '$end' GROUP BY go.order_code";

        $result=DB::select($query);     
        if(!empty($result))
            return ['status' => "200", "message" => "Success", "data" => $result];

            return ['status' => "200", "message" => "No Data Found", "data" => []];
    }

  public function getVehiclesByHubIds($Hub_Dc_Assigned,$Type='Hub',$HubIds=array()) {
        try{

            if($Type=='DC' && empty($Hub_Dc_Assigned)) {
              
              $Assoc_DC = DB::table('dc_hub_mapping')
                    ->whereIn('hub_id',$HubIds)
                    ->pluck('dc_id')->all();

              if(!empty($Assoc_DC)) {
                $Hub_Dc_Assigned = $Assoc_DC; 
              } 
            }
            $selectArr = array('le.business_legal_name AS vehicleName',
              'vehicle.reg_no AS vehicleno',
              'vehicle.vehicle_id',
              'vehicle.vehicle_type'
              );
            return DB::table('legal_entities as le')
                ->select($selectArr)
                ->join('vehicle','le.legal_entity_id','=','vehicle.legal_entity_id')
                ->join('vehicle_attendance as va','va.vehicle_id','=','vehicle.vehicle_id')
                ->leftJoin('legalentity_warehouses','vehicle.hub_id','=','legalentity_warehouses.le_wh_id')
                ->where(array('le.legal_entity_type_id'=>1008,
                        'vehicle.is_active'=>1))
                ->where('va.attn_date' ,date('Y-m-d'))
                ->where('va.is_present' , 1)
                ->whereIn('vehicle.hub_id',$Hub_Dc_Assigned)
                ->get()->all();

        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }

    public function saveTemporaryVehicleModal($data){
        if(isset($data["temp_vehicle"])){
            foreach ($data["temp_vehicle"] as $record) {
                
            }
        }
        else
            return FALSE;
    }

    public function getCheckersListModal($data)
    {
      $rolesModel = new Role();
      $rolerepo = new RoleRepo();
      $query = "
      SELECT
          users.user_id,CONCAT(users.firstname,' ',users.lastname) AS username
        FROM
          users
        LEFT JOIN user_roles ON user_roles.user_id = users.user_id";
        
        /*if(isset($data['checker_id']) and !empty($data['checker_id']))
        $query.=' AND users.user_id = '.$data['checker_id'];*/
        $getbus=DB::table('business_units')->select(DB::raw('max(bu_id) as bu_id'))->first();
        $getbus=" concat(1,'-',".$getbus->bu_id.")";
      
      if(isset($data['user_id']) && !empty($data['user_id'])){

        $query.=" join user_permssion ur on users.user_id=ur.user_id and permission_level_id in (6) join legalentity_warehouses lw on lw.bu_id=( case ur.object_id when 0 then true else ur.object_id end)";
        
        $getwarehouses =   json_decode($rolesModel->getFilterData(6,$data['user_id']), 1); 
        $getwarehouses = json_decode($getwarehouses['sbu'], 1);            
        $dc_acess_list = isset($getwarehouses['118001']) ? $getwarehouses['118001'] : 'NULL';
      }
      $query.=" WHERE user_roles.role_id = ?
        AND users.is_active = ?";
      $globalFeature = $rolerepo->checkPermissionByFeatureCode('GLB0001',$data['user_id']);
      if(!$globalFeature && !empty($dc_acess_list)){
          $query.= " and lw.le_wh_id in (".$dc_acess_list.")";
      }

      if(isset($data['checker_id']) and !empty($data['checker_id']))
        $query.=' AND users.user_id = '.$data['checker_id'];
        $query.=' group by users.user_id';
        $result = DB::select($query,[84,1]);
      //print_r($result);exit;
      if(!empty($result))
        return ['status' => "200", "message" => "Success", "data" => $result];

      return ['status' => "200", "message" => "No Data Found", "data" => []];
    }


    public function getRtdOrdersList($data){

      //print_r($data);

      $start = date('Y-m-d 00:00:00', strtotime($data['from_time']));
      $end = date('Y-m-d 23:59:59', strtotime($data['to_time']));
      //echo $start;exit;
      $start=trim($start);
      $end=trim($end);

      $rolesModel = new Role();
      $categoryModel = new CategoryModel();
      $user_data = $categoryModel->getUserId($data['user_token']);
      $userId = isset($user_data[0]->user_id) ? $user_data[0]->user_id : 0;
      $warehouseDetails = $rolesModel->getWarehouseData($userId, 6);
       if($warehouseDetails != '')
        {
            $warehouseInfo = (array) json_decode($warehouseDetails, true);
            $warehouseId = isset($warehouseInfo['118002']) ? $warehouseInfo['118002'] : 0;
            $query="SELECT go.order_code,go.gds_order_id, go.hub_id, lw.lp_wh_name FROM gds_order_track got
              LEFT JOIN picker_container_mapping pcm ON got.gds_order_id = pcm.order_id
              JOIN gds_orders go ON go.gds_order_id = got.gds_order_id 
              JOIN legalentity_warehouses lw ON lw.le_wh_id = go.hub_id
              WHERE pcm.is_verified = '0' 
              AND got.checker_id IS NULL AND go.`order_status_id` in (17005,17021) 
              AND go.order_date BETWEEN '$start' AND '$end' 
              AND go.hub_id in ($warehouseId) GROUP BY go.order_code";

              $result=DB::select($query);     

              if(!empty($result))
                  return ['status' => "200", "message" => "Success", "data" => $result];

              return ['status' => "200", "message" => "No Data Found", "data" => []];
          }else{
              return ['status' => "200", "message" => "No Data Found", "data" => []];
          }
    }

    
     public function updateTrackDetailBychecker($orderId,$fields) {
        try{

            DB::table('gds_order_track')->where('gds_order_id','=', $orderId)
            ->update($fields);
			//->update(['assign_date' => $date]);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
  }


    public function assignOrdersForCheckerModal($orders,$checker_id,$date)
    {
      foreach ($orders as $orderId) {
        //$isOrderExist = DB::table('gds_order_track')->where('gds_order_id', $orderId)->count();
		
		$fields=['checker_id' => $checker_id,'assign_checker_date' => $date];
	
        //if($isOrderExist>0){
          $this->updateTrackDetailBychecker($orderId,$fields);
        //}
      }

      return ['status' => "200", "message" => "Successfully Assigned", "data" => []];
    }
    
    public function getOrdersNotYetVerifiedListData($verifierId,$fromDate,$toDate){
      $query='
        SELECT
          go.order_code,got.checker_id,getLeWhName(go.hub_id) as hub_name,
          group_concat(distinct pcm.container_barcode) as container_barcode
        FROM 
          gds_order_track got
        JOIN picker_container_mapping pcm ON got.gds_order_id = pcm.order_id
        JOIN gds_orders go ON go.gds_order_id = got.gds_order_id
        WHERE 
          got.checker_id = ?
          AND pcm.is_verified = "0"
          AND go.`order_status_id` in (17005,17021)
          AND pcm.created_at between ? and ?
        GROUP BY go.order_code,go.gds_order_id';
      
      $result = DB::SELECT($query,[$verifierId,$fromDate." 00:00:00",$toDate." 23:59:59"]);
      
      return json_encode($result);
    }

  
    public function getCheckersCountModel($data)
    {
      $checkerid=$data['checker_id'];
      $query = "SELECT  IFNULL(a.order_date,'') AS order_date,  IFNULL(SUM(a.TotalLineItems),0)  AS TotalLineItems ,IFNULL(SUM(a.VerifiedLinesCount),0)AS VerifiedLinesCount ,COUNT(DISTINCT(a.TotalOrders)) AS TotalOrders,COUNT(DISTINCT(a.VerifiedOrders)) AS VerifiedOrders,IFNULL(GROUP_CONCAT(DISTINCT (a.checker_id)),0) AS checker_id,IFNULL(GROUP_CONCAT(DISTINCT (GetUserName(a.checker_id,2))),'') AS checker_name 

        FROM (SELECT DATE_FORMAT(got.`assign_checker_date`,'%Y-%m-%d') as order_date,IFNULL(COUNT(DISTINCT (pcm.`productid`)),0) AS 'TotalLineItems',
        COUNT(DISTINCT( CASE
          WHEN pcm.`is_verified` = '1' 
          THEN pcm.`productid`
      END
    
      )) AS 'VerifiedLinesCount',
      pcm.order_id AS 'TotalOrders',
      CASE WHEN pcm.`is_verified` = '1'
          THEN pcm.`order_id` END AS 'VerifiedOrders',
        GROUP_CONCAT(DISTINCT(got.`checker_id`)) AS checker_id,
        GROUP_CONCAT(DISTINCT (GetUserName(got.checker_id,2))) AS checker_name, 
        GROUP_CONCAT(DISTINCT(got.`gds_order_id`)) AS orde
      FROM
      picker_container_mapping pcm,gds_order_track got,gds_order_products gop,
      gds_orders gd 
        WHERE got.`gds_order_id` = gop.`gds_order_id`
        AND gd.`gds_order_id`=gop.`gds_order_id`
        AND gop.`gds_order_id` = pcm.`order_id`
        AND gop.`product_id` = pcm.`productid`
          ";
          
          if(isset($data['checker_id']) and $data['checker_id']!=0){
            $query .= "AND got.checker_id = '$checkerid'";
          }
          
          if (($data['checker_id']==0 || $data['checker_id'] != "") and (empty($data['to_date']) and empty($data['from_date']))) {
            $today=date("Y-m-d").' 00:00:00';
            $today_end=date("Y-m-d").' 23:59:59';
            $query .= "AND got.`assign_checker_date` between '$today' and '$today_end'";
          }

          if ($data['from_date'] !="" and $data['to_date'] !="" and $data['checker_id'] ==0 ) {

            $today=date("Y-m-d",strtotime($data['from_date'])).' 00:00:00';
            $today_end=date("Y-m-d",strtotime($data['to_date'])).' 23:59:59';
            $query .= "AND got.`assign_checker_date` between '$today' and '$today_end'";
          }

          if ($data['from_date'] !="" and $data['to_date'] !="" and $data['checker_id'] !="" and $data['checker_id'] !=0) {

            $today=date("Y-m-d",strtotime($data['from_date'])).' 00:00:00';
            $today_end=date("Y-m-d",strtotime($data['to_date'])).' 23:59:59';
            $query .= "AND got.`assign_checker_date` between '$today' and '$today_end'";
          }

            $checkerQuery = $query;
            $query.="GROUP BY got.checker_id,got.`gds_order_id`,pcm.`productid`) a";
            $result = DB::select($query);
            
            //VerifiedCount TotalLineItems
            $result = json_decode(json_encode($result));
            if(!empty($result)){
              $checker_id = $result[0]->checker_id;
              $OrderDate = $result[0]->order_date;
              $result[0]->Pending= $result[0]->TotalLineItems - $result[0]->VerifiedLinesCount;
              $result[0]->PendingOrders=$result[0]->TotalOrders - $result[0]->VerifiedOrders;
              $TotalLineItems = $result[0]->TotalLineItems;
              $PendingOrders = $result[0]->PendingOrders;
              $TotalOrders = $result[0]->TotalOrders;
              $VerifiedOrders = $result[0]->VerifiedOrders;
              $returnsArr = array();

              array_push($returnsArr, array("Status"=>"PendingCount","Count"=>$result[0]->Pending));
              if($result[0]->VerifiedLinesCount==null){
                $result[0]->VerifiedLinesCount=0;
              }
              array_push($returnsArr, array("Status"=>"VerifiedCount","Count"=>$result[0]->VerifiedLinesCount));
              $checkerArr = array();
              $checkIds = explode(",",$result[0]->checker_id);
              foreach ($checkIds as $value) {
                        
                         # code...
                        $checkerQuery1= $checkerQuery;
                        $checkerQuery1.="AND got.checker_id = '$value' GROUP BY got.checker_id,got.`gds_order_id`,pcm.`productid`)a";

                        $resultA =DB::select($checkerQuery1);
            
                        array_push($checkerArr,$resultA[0]);
             
                       }         


              $result = json_encode($returnsArr);

              // if($checker_id== null){
              //   $checker_id=0;
              // }
              // if($OrderDate== null){
              //   $OrderDate='';
              // }
              // if($TotalLineItems== null){
              //   $TotalLineItems=0;
              // }
          return ['status' => "200", 
          "message" => "Success", 
          "checker_id" =>$checker_id,
          "OrderDate"=>$OrderDate,
          "TotalLineItemsCount"=>$TotalLineItems, 
          "TotalOrdersCount"=>$TotalOrders,
          "VerifiedOrdersCount"=>$VerifiedOrders,
          "PendingOrdersCount"=>$PendingOrders,
          "data" => json_decode($result),"profile"=>json_decode(json_encode($checkerArr))];
        }

          return ['status' => "200", "message" => "No Data Found", "data" => []];
  }

  public function userAuthentication($data){

    $password=md5($data['password']);

    $mail=$data['mail'];
    $isString=0;
    $isNumber=0;
  
    $query="select * from users where email_id = '$mail'  AND password= '$password' ";
    $isUserExist=DB::select($query);


    if(count($isUserExist)==0){
       if(is_string($mail)){
          $isString=1;
        }
       if(is_numeric($mail)){
          $isNumber=1;
       }

       if($isString!=1){
      
        $query="select * from users where mobile_no = '$mail' AND password= '$password'";
        $isUserExist=DB::select($query);
        if(count($isUserExist)==0){  

          $query="select * from users where emp_id = '$mail' AND password= '$password'";
              $isUserExist=DB::select($query);
        }

      }
    }
   

    if(count($isUserExist)>0){
      $isUserExist=json_decode(json_encode($isUserExist),true);
      $dataSet=array(
        'user_id'=>$isUserExist[0]['user_id'],
        'lp_token'=>$isUserExist[0]['lp_token']
      );

      return ['status'=>"success","message"=>"success","data"=>$dataSet];

    }else{
      return ['status'=>"fail","message"=>"Invalid credentials","data"=>0];
    }
  }


}