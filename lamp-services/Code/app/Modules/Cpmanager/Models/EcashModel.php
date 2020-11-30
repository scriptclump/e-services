<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use URL;
use Cache;
use Illuminate\Http\Request;
use Log;
use Response;
date_default_timezone_set('Asia/Kolkata');


class EcashModel extends Model
{
     public $timestamps = false;
     public function __construct() {

      }

public function getEcashInfo($user_id){
    try {
   
      if(!empty($user_id)){
       
        $ecash_user_data = DB::table('user_ecash_creditlimit')->select(db::raw('creditlimit,(cashback-applied_cashback) as ecash,payment_due'))->where('user_id',$user_id)->first();  
 
        return $ecash_user_data;
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }
  public function getCustomertypeEcashInfo($customer_type){
    try {
   
      if(!empty($segment_id)){
        $ecash_user_data = DB::table('ecash_creditlimit')->select('creditlimit','ecash','ecash_id')->where('segment_id',$customer_type)->first();
        
        return $ecash_user_data;
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }   

    public function getCustomertypeMinorderValue($customerType,$leWhId,$isSelfOrder){
    try {
   
      if(!empty($customerType)){
        $order_user_data = DB::table('ecash_creditlimit')
          ->select('minimum_order_value','self_order_mov','mov_ordercount')
          ->where([
            'customer_type' => $customerType,
            'dc_id' => $leWhId,
          ])
          ->first();
        
        if(!$isSelfOrder){
          // For Self Orders
          $order_min_value= isset($order_user_data->self_order_mov) && !empty($order_user_data->self_order_mov)?$order_user_data->self_order_mov:0;
        }else{
          // For Field Force Placed Orders
          $order_min_value= isset($order_user_data->minimum_order_value) && !empty($order_user_data->minimum_order_value)?$order_user_data->minimum_order_value:0;
        }

        $mov_ordercount = isset($order_user_data->mov_ordercount) && !empty($order_user_data->mov_ordercount)?$order_user_data->mov_ordercount:0;

        // Here we return, 2 fields, 
        // 1 is minimum bill value and
        // 2nd is success orders count
        return ["min_order_value" => $order_min_value, "mov_ordercount" => $mov_ordercount];
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
       return ["min_order_value" => 0, "mov_ordercount" => 0];
    }
  } 

    public function getUserCustomerType($legal_entity_id){
    try {
  
      if(!empty($legal_entity_id)){
        $legal_data = DB::table('legal_entities')->select('legal_entity_type_id')->where('legal_entity_id',$legal_entity_id)->first();
       
        $legal_entity_type_id= isset($legal_data->legal_entity_type_id) && !empty($legal_data->legal_entity_type_id)?$legal_data->legal_entity_type_id:0;

        return $legal_entity_type_id;
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }

     public function getLegalEntityId($user_id){
    try {
  
      if(!empty($user_id)){
        $user_data = DB::table('users')->select('legal_entity_id')->where('user_id',$user_id)->first();
      
        $legal_entity_type_id= isset($user_data->legal_entity_id) && !empty($user_data->legal_entity_id)?$user_data->legal_entity_id:0;

        return $legal_entity_type_id;
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }


  
    public function updateUserEcash($data){
    try {

        $ecash_amount=$this->getExistingEcash($data['customer_id']);
         
         if($ecash_amount >=$data['applied_ecash'])
         {
          $ecash_amount=$ecash_amount-$data['applied_ecash'];
        $user_ecash_data = DB::Table('user_ecash_creditlimit')->where('user_id',$data['customer_id'])
        ->update(array('ecash'=>$ecash_amount,'updated_by'=>$data['customer_id'],'updated_at' => date("Y-m-d H:i:s")));    

        $ecash_amount=$this->getUserCashback($data['customer_id']);
        $ecash_amount = isset($userEcash->cashback)?$userEcash->cashback:0;
        $transaction_ecashdata=DB::table('ecash_transaction_history')
                        ->insert(['user_id' => $data['customer_id'],
                           'legal_entity_id' => $data['legal_entity_id'], 
                           'cash_back_amount' => $data['applied_ecash'],
                           'balance_amount'=> $ecash_amount,
                           'transaction_type' => 143001,
                           'created_by'=>$data['customer_id'],
                           'created_at' => date("Y-m-d H:i:s")
                           ]);   
       
        return true;
      }else{
        return false;
      }
   
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }

   public function getExistingEcash($user_id){
    try {
  
      if(!empty($user_id)){
        $userecash_data = DB::table('user_ecash_creditlimit')->select(db::raw('(cashback-applied_cashback) as ecash'))->where('user_id',$user_id)->first();
      
        $ecash_amount= isset($userecash_data->ecash) && !empty($userecash_data->ecash)?$userecash_data->ecash:0;

        return $ecash_amount;
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }

  public function getUserCashback($userId){
      try{
          if($userId>0 && $userId != ''){
              $data = DB::selectFromWriteConnection(DB::raw('select * from `user_ecash_creditlimit` as `uec` where `uec`.`user_id` = '.$userId.' limit 1'));                
              return isset($data[0])?$data[0]:[];
          }
      } catch (Exception $ex) {

      }
        
  }

public function getUserIdBasedLegalEntityId($legal_entity_id){
    try {
  
      if(!empty($legal_entity_id)){
        $usere_data = DB::table('users')->select('user_id')->where('legal_entity_id',$legal_entity_id)->first();
      
        $user_id= isset($usere_data->user_id) && !empty($usere_data->user_id)?$usere_data->user_id:0;

        return $user_id;
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }

    public function getAppliedCashback($user_id){
    try {
  
      if(!empty($user_id)){
        $userecash_data = DB::table('user_ecash_creditlimit')->select('applied_cashback')->where('user_id',$user_id)->first();
      
        $applied_cashback= isset($userecash_data->applied_cashback) && !empty($userecash_data->applied_cashback)?$userecash_data->applied_cashback:0;

        return $applied_cashback;
      }
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }



    public function getOrderCashbackIds($product_id,$le_wh_id,$customer_id,$discount_amount){
    try {
       foreach ($product_id as $key => $value)
       {
            
        $cart_ids[]=$value['cartId'];

      
       }

$cartProductStarsList = DB::table('cart_product_packs')
      ->select(db::raw(" GROUP_CONCAT(distinct cart_product_packs.star) AS stars"))
      ->whereIn('cart_id',$cart_ids)
      ->get()->all();
//      log::info('$cartProductStarsList');

  //    log::info($cartProductStarsList);
$cartProductStarsList=json_decode(json_encode($cartProductStarsList),true);
$cartProductStarsList=isset($cartProductStarsList) && !empty($cartProductStarsList)?$cartProductStarsList[0]:'';
 $today = date('Y-m-d');
 $legal_entity_id=$this->getLegalEntityId($customer_id);
 $customer_type=$this->getUserCustomerType($legal_entity_id);

if($cartProductStarsList)
{

$star_total_bill =  DB::select('SELECT * FROM promotion_cashback_details 
  WHERE "'.$today.'" Between start_date and end_date
                      and cbk_source_type = 1 and wh_id = '.$le_wh_id.'  
                      and customer_type = '. $customer_type.'
                      and  `product_star` IS NOT NULL');
$cbkid_stars=array();

      //log::info('$star_total_bill');

      //log::info($star_total_bill);
if(isset($star_total_bill) && !empty($star_total_bill))
{

$star_total_bill=json_decode(json_encode($star_total_bill),true);

foreach ($star_total_bill as $key => $value) {

$star_total_data= DB::table('cart_product_packs')
      ->select(db::raw("SUM(pack_price)"))
      ->whereIn('cart_id',$cart_ids)
      ->whereRaw('FIND_IN_SET(star,"'.$value['product_star'].'")')
      ->groupBy('star')
      ->HAVING('SUM(pack_price)','>=',$value['range_to'])
      ->get()->all();
      if(isset($star_total_data) && !empty($star_total_data))
      {
 $cbkid_stars[]=$value['cbk_id'];

      }

}

}

$withoutstar_total_bill =  DB::select('SELECT * FROM promotion_cashback_details 
  WHERE "'.$today.'" Between start_date and end_date
                      and cbk_source_type = 1 and wh_id = '.$le_wh_id.'  
                      and customer_type = '. $customer_type.'
                      and  `product_star` IS  NULL');

if(isset($withoutstar_total_bill) && !empty($withoutstar_total_bill))
{

$withoutstar_total_bill=json_decode(json_encode($withoutstar_total_bill),true);
foreach($withoutstar_total_bill as $key => $order_bill) {
 $cbkid_stars[]= DB::table('cart_product_packs')
      ->select(db::raw(" cbk_id,cbk_label"))
      ->whereIn('cart_id',$cart_ids)
      ->HAVING('SUM(pack_price)','>=',$order_bill['range_to'])
      ->get()->all();

}
}

 $data=DB::table('promotion_cashback_details')->select(db::raw(" cbk_id,cbk_label"))->whereIn('cbk_id',$cbkid_stars)->get()->all();
if( $data)
{
return $data;
}else{
return [];
}


}else{

  return [];
}


      /*  $cart_pack_data=json_decode(json_encode($cart_pack_data),true);
        $star_value=array();

        foreach ($cart_pack_data as $key => $value) {
          $star[]=$value['star'];
          $star_value[$value['star']]= (isset($star_value[$value['star']])&& !empty($star_value[$value['star']])?$star_value[$value['star']]:0)+$value['pack_price'];      
         $order_total_value=(isset( $order_total_value)&& !empty( $order_total_value)? $order_total_value:0)+$value['pack_price'];
        }
       */
    
     
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }

    public function getOrderCashbackDatas($today,$rangevalue,$le_wh_id,$customer_type,$is_self=0){
    try {
       // $today="'".date('Y-m-d')."'";

        $ordercash_data = DB::table('promotion_cashback_details')->select(db::raw("cbk_type as cashback_type,cbk_value as cbk_value,excl_brand_id,excl_prod_group_id,product_group_id,IFNULL(range_from,0) as qty_from_range,
          IFNULL(range_to,0) as qty_to_range,cbk_label as cashback_description,cbk_ref_id as reference_id,cbk_id as cashback_id,cap_limit,
          cbk_source_type as cbk_source_type,benificiary_type as benificiary_type,product_star,brand_id,product_value"))
          ->where('cbk_source_type',1)
          ->where('cbk_status',1)
          ->where('benificiary_type',62)
          //->where('is_self',$is_self)
          ->where('customer_type','like','%'.$customer_type.'%')
           //->where('wh_id',$le_wh_id) 
          ->where('wh_id','like','%'.$le_wh_id.'%') 
           ->whereRaw($today." between start_date and end_date")
           ->whereRaw($rangevalue." >= range_from")
           ->where(function ($query) use ($is_self) {
                $query->where('is_self', '=',$is_self)
                      ->orWhere('is_self', '=', 2);
            });

       /* if($array->customer_type==3013)
        {  
        $ordercash_data->where('customer_type',3013);
        }else{
         $ordercash_data->where('customer_type',3014);
        }*/
        //$ordercash_data->where('customer_type',3014);

        return $ordercash_data->get()->all();
   
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }

   public function getCashbackHistory($array){
    try {
       
        $user_cash_data = DB::table('ecash_transaction_history')
        ->select(db::raw("getOrderCode(order_id) as order_code,order_id,cash_back_amount,getMastLookupValue(transaction_type) as cashback_type,transaction_date"))
          ->where('user_id',$array->user_id)
          ->orderBy('transaction_date', 'DESC')
          ->get()->all();
        
        return $user_cash_data;
   
    } catch (Exception $e) {
       Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
    }
  }
  public function getOrderFreeQty($rangevalue,$customer_type,$wh_id){
    $today="'".date('Y-m-d')."'";
    $freeQtyData=DB::Select(DB::raw("select pf.free_id,pf.product_id,pf.product_qty,p.`product_title`, pf.`pack_level`,p.`thumbnail_image`,pf.is_sample,pf.free_id,pf.range_to FROM promotions_freeqty_sample_details  pf INNER JOIN products p ON p.`product_id` = pf.`product_id` WHERE ( $today BETWEEN pf.start_date AND pf.end_date) AND ($rangevalue BETWEEN pf.range_from AND pf.range_to)  AND (pf.wh_id in ($wh_id) OR pf.`wh_id` = 0) AND (pf.customer_type = $customer_type OR pf.`customer_type` = 3014) AND pf.is_active=1 AND pf.is_sample = 0 order by pf.free_id desc"));
    $sampleData=DB::Select(DB::raw("select pf.free_id,pf.product_id,pf.product_qty,p.`product_title`, pf.`pack_level`,p.`thumbnail_image`,pf.is_sample,pf.free_id,pf.range_to FROM promotions_freeqty_sample_details  pf INNER JOIN products p ON p.`product_id` = pf.`product_id` WHERE ( $today BETWEEN pf.start_date AND pf.end_date) AND ($rangevalue >= pf.range_from)  AND (pf.wh_id in ($wh_id) OR pf.`wh_id` = 0) AND (pf.customer_type = $customer_type OR pf.`customer_type` = 3014) AND pf.is_active=1 AND pf.is_sample = 1 order by pf.free_id desc"));
    $data = array_merge($freeQtyData,$sampleData);

    return $data;
  }
  public function getProductPackInfo($productId,$uom) {
        try {
            $fields = array('lookup.value','starConfig.description as starCode','lookup.master_lookup_name as uomName','pack.no_of_eaches','esu','star');
            $query = DB::table('product_pack_config as pack');
            $query->leftJoin('master_lookup as lookup','pack.level','=','lookup.value');
            $query->leftJoin('master_lookup as starConfig','pack.star','=','starConfig.value');
            $query->select($fields);
            $query->where('pack.product_id',$productId);
            $query->where('pack.level',$uom);
            $packStatus = $query->first();
            return $packStatus;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getHubId($token){
      $data="select r.hub_id from retailer_flat r join users u on u.legal_entity_id=r.legal_entity_id where  u.password_token='".$token."' limit 1";
      $data=DB::selectFromWriteConnection(DB::raw($data));
      return $data;      
    }
    public function getRelatedData($data,$customer_type,$wh_id){
      $today="'".date('Y-m-d')."'";
      if($data->is_sample == 0){

        $freeQtyData=DB::Select(DB::raw("select pf.free_id FROM promotions_freeqty_sample_details  pf INNER JOIN products p ON p.`product_id` = pf.`product_id` WHERE ( $today BETWEEN pf.start_date AND pf.end_date)  AND pf.range_to<=$data->range_to AND (pf.wh_id = $wh_id OR pf.`wh_id` = 0) AND (pf.customer_type = $customer_type OR pf.`customer_type` = 3014) AND pf.is_active=1 AND pf.is_sample = 0 AND pf.product_id = $data->product_id AND pf.free_id !=$data->free_id"));
        $data = $freeQtyData;
      }else if($data->is_sample == 1){
         $sampleData=DB::Select(DB::raw("select pf.free_id FROM promotions_freeqty_sample_details  pf INNER JOIN products p ON p.`product_id` = pf.`product_id` WHERE ( $today BETWEEN pf.start_date AND pf.end_date) AND pf.range_to<=$data->range_to AND (pf.wh_id = $wh_id OR pf.`wh_id` = 0) AND (pf.customer_type = $customer_type OR pf.`customer_type` = 3014) AND pf.is_active=1 AND pf.is_sample = 1 AND pf.product_id =$data->product_id AND pf.free_id!=$data->free_id order by free_id desc"));
        $data = $sampleData;
      }

     
      //$data=json_encode($data,1);
      return $data;
    }
    public function getState($column,$value){
        $data = DB::table('users as u')
                ->join('retailer_flat as r','r.legal_entity_id','=','u.legal_entity_id');

        if($column == 'token'){
          $data = $data->where(function($query) use($value) {
                    $query->where('password_token',$value)
                      ->orwhere('lp_token',$value);
                });
        }
        if($column == 'legal_entity_id'){
          $data = $data->where('r.legal_entity_id',$value);
        }
        $data = $data->select('state_id')
                ->get()->all();

        if(count($data)>0){
          return $data[0]->state_id;
        }else{
          return 0;
        }
    }
    //get FF incentives on the basis of color code & role
    public function getIncentives($order_id,$flag){
       $data =  DB::selectFromWriteConnection(DB::raw("call getFFIncentives($order_id,53,$flag)"));
       $data = json_decode(json_encode($data),1);
       $cbValues = array();
       foreach ($data as $key => $value) {
        if($value['Role'] && $value['Discount'])
        $cbValues[$value['Role']] = $value['Discount'];
       }
      if(count($cbValues)>0){
        return Array('status' => 200, 'message' =>$cbValues );
      }else{
        return Array('status' => 403,'message'=> "No cashback found for order_id: $order_id");
      }
    } 

}
