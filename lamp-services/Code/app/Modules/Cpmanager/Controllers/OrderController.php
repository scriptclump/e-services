<?php
  /*
    * Filename: OrderController.php
    * Description: This file is used for manage retailer & sales orders
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor@2016
    * Version: v1.0
    * Created date: 14th July 2016
    * Modified date: 
  */
  
  /*
    * OrderController is used to manage orders
    * @author    Ebutor <info@ebutor.com>
    * @copyright ebutor@2016
    * @package   Orders
    * @version:  v1.0
  */ 
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
  use View;
  use Illuminate\Http\Request;
  use App\Modules\Cpmanager\Models\OrderModel;
  use App\Modules\Cpmanager\Models\Orderhistory;
  use App\Modules\Cpmanager\Views\order;
  use App\Http\Controllers\BaseController;
  use App\Modules\Cpmanager\Models\accountModel;
  use App\Modules\Roles\Models\Role;
  use App\Modules\Cpmanager\Models\OrderapiLogsModel;
  use App\Modules\Cpmanager\Models\EcashModel;
  use App\Modules\PurchaseOrder\Models\PurchaseOrder;  
  use App\Central\Repositories\RoleRepo;
  use App\Modules\Cpmanager\Models\RegistrationModel;

  
  class OrderController extends BaseController {  
    
    public function __construct() {
      
      $this->order = new OrderModel(); 
      $this->orderhistory = new Orderhistory();
      $this->_account = new accountModel();
      $this->_role = new Role();
      $this->_ecash=new EcashModel();
      $this->_rolerepo=new RoleRepo();
      $this->_register = new RegistrationModel();

    }
    
    /*
      * Class Name: Add Order
      * Description: We display product details  based on frequent Orders ,
      High Margin & categories & featured offers & categories  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 4th July 2016
      * Modified Date & Reason: 4th July 2016
      added validations
    */
  
  public function addOrder1($cartData='') {

  try{
      if($cartData != ""){
        $_POST['data'] = $cartData;
      }
     if(isset($_POST['data'])) { 
        
      $array = json_decode($_POST['data'],true);
      $logs_aray=array();
      $logs_aray['data']=$_POST['data'];
      $logs_aray['legal_entity_id']=$array['legal_entity_id'];
      $logs_aray['order_code']=$array['orderId'];
      if((isset($array['sales_token']) && $array['sales_token']!='') && 
        (isset($array['customer_token']) && $array['customer_token']!='')) {   
          
          if(($array['sales_token']!= $array['customer_token'])) {
            $SalesToken= $this->order->validateToken($array['sales_token']);

            } else {
            print_r(json_encode(array('status'=>"failed",'message'=> "Field Force is not authorised to place Order by himself, so please select retailer",'data'=>[])));die;              
            }             
         }  else {
          $CustomerToken=$this->order->validateToken($array['customer_token']);     
        }
      if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) ||
        (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {

         $is_self=(isset($array['sales_token']) && !empty($array['sales_token']))?0:1;

         if(isset($array['sales_token']) && !empty($array['sales_token']))
         {
         $sales_rep_id=$this->order->getcustomerId($array['sales_token']);
         }else{
         $sales_rep_id=0;
         }
         $customer_id=$this->order->getcustomerId($array['customer_token']);
         if(isset($array['customer_type']) && !empty($array['customer_type']))
         {
         $customer_type=$array['customer_type'];
         }else{
         $customer_type=$this->_ecash->getUserCustomerType($array['legal_entity_id']);
         }

        $order_sucesscount=$this->order->getSuccessOrderCount($array['legal_entity_id']);
        
        $cust_parent=$this->order->getParentCustId($array['legal_entity_id']);
        $cust_user_id = isset($cust_parent->cust_user_id)?$cust_parent->cust_user_id:$customer_id;

        $userEcash=$this->order->getUserEcash($cust_user_id);
        $currentEcash = isset($userEcash->cashback)?($userEcash->cashback):0;
        $userCreditLimit = isset($userEcash->creditlimit)?($userEcash->creditlimit):0;
        $undeliveredvalue = $this->order->custUnDeliveredOrderValue($array['legal_entity_id']);
        
        $this->_poModel = new PurchaseOrder();
        $is_Stockist = $this->_poModel->checkStockist($array['legal_entity_id']);
        $currentEcash += ($is_Stockist>0)?$userCreditLimit:0;


      if(isset($array['type']) && $array['type'] == 'mfc'){
      $user_Credit_limit = isset($array['credit_limit'])?($array['credit_limit']):0;
      $total_price = isset($array['total_price'])?($array['total_price']):0;

            if( $total_price  > $user_Credit_limit ){
                $diff =  $user_Credit_limit - $total_price;
      
            return json_encode(array('status'=>"failed",'message'=>"Credit limit",'data'=>$diff));die;
                    }

          $orderstatus = 'NewFC';          
        } else {
          $orderstatus = 'New';          
        }


        if($currentEcash<0) { // && $array['paymentmode']=='loc'
            return json_encode(array('status'=>"failed",'message'=>"Please clear your previous order payments to continue",'data'=>[]));die;
        }
        if(($undeliveredvalue)>0) {
            return json_encode(array('status'=>"failed",'message'=>"You already have some undelivered orders with LOC",'data'=>[]));die;
        }
        if(($array['total']+$undeliveredvalue)>$userCreditLimit && $array['paymentmode']=='loc') {
            return json_encode(array('status'=>"failed",'message'=>"Order value should not be more than ".$userCreditLimit." with LOC",'data'=>[]));die;
        }
        // To check the Minimum Order Value based on
        // - Customer Type,
        // - Warehouse Id
        // - Order Placed By (Self Order or FF Order)
        $mov_details=$this->_ecash->getCustomertypeMinorderValue($customer_type,$array['le_wh_id'],$sales_rep_id);
        $min_order_value = $mov_details["min_order_value"];
        if( $customer_type==3013 && $array['total']< $min_order_value)
         {
          return json_encode(array('status'=>"failed",'message'=>"Minimum Ordervalue should be".$min_order_value." Rupees",'data'=>[]));die;
       
         }elseif($order_sucesscount >= $mov_details["mov_ordercount"] && $array['total']<$min_order_value)
         { 
         return json_encode(array('status'=>"failed",'message'=>"Minimum Ordervalue should be ".$min_order_value." Rupees",'data'=>[]));die;
         }
       
       //if(isset($array['scheduled_delivery_date']) && $array['scheduled_delivery_date']!='') {

        /**
        *   validating selected Scheduled Delivery Date is in holiday list or not
        */
        if(isset($array['scheduled_delivery_date']) && $array['scheduled_delivery_date']!=''){
          $datetime= $array['scheduled_delivery_date'];
          $date=substr($datetime, 0, 10);
        }else{
          $date ='';
        }
        $validateScheduled_delivery_date=$this->order->ScheduledDeliveryDate($date);

        /*if(!empty($validateScheduled_delivery_date)) {
          $message='Selected scheduled delivery date is a public hoilday, please select another day';
           print_r(json_encode(array('status'=>"failed",'message'=>$message,'data'=>[])));die;
        }*/ 
        /*fetching cart details */
        $cart=$this->order->Cartdetails_new($array);
        //cart logs
        $logs_aray['cart_data']=json_encode($cart);
        $segmentId=(isset($array['segment_id']) && $array['segment_id']!='')? $array['segment_id']:0;
        $legal_entity_id=(isset($array['legal_entity_id']) && $array['legal_entity_id']!='')? $array['legal_entity_id']:'';
        $address_id=(isset($array['address_id']) && $array['address_id']!='')? $array['address_id']:'';
        $latitude=(isset($array['latitude']) && $array['latitude']!='')? $array['latitude']:0;
        $longitude=(isset($array['longitude']) && $array['longitude']!='')? $array['longitude']:0;
        $mfc_id=(isset($array['mfc']) && $array['mfc']!='')? $array['mfc']:0;
       

              $customerAddress=$this->order->getShippingAddress($address_id,$legal_entity_id);
            
              $address=json_decode(json_encode($customerAddress[0]),true);
              
              /**
              *  landmark & locality
              */
              $landmark=(isset($address['landmark']) && $address['landmark']!='')? $address['landmark']:'';
              $locality=(isset($address['locality']) && $address['locality']!='')? $address['locality']:'';
              $order_level_cashback=(isset($array['order_level_cashback']) && $array['order_level_cashback']!='')? $array['order_level_cashback']:''; 
        
              /**
              *  area_id
              */
              $area_id=$this->order->GetAreaID($address['legal_entity_id']);       
            
           if(!empty($address['pin'])) {
               if(!empty($address['Firstname'])) {
                  if(!empty($address['Address'])) {
                    if(!empty($address['telephone'])) {
                      if(!empty($address['state'])) {
                        if(!empty($address['country'])) {

                    $Customerdata=$this->order->getCustomerData($customer_id);

                    $data = json_decode(json_encode($Customerdata),true);    

                    $rand_no=mt_rand();    
            
                $gdsPostInfo = array();
             
			 
             $gdsPostInfo['customer_info']= array
             ('suffix'=>' ','first_name'=> $data['firstname'],
                  'middle_name' => '',
                  'last_name' => $data['lastname'],
                  'email_address' => $data['email_id'],
                  'channel_user_id' => $data['customerId'],
                  'cust_le_id'=>$address['legal_entity_id'],
                  'mobile_no' => $data['telephone'],
                  'dob' => '',
                  'channel_id' => Config::get('dmapi.channelid'),
                  'gender' => '',
                  'registered_date' =>'');
                                 
               $gdsPostInfo['address_info']= array
                 ( 0=>array('address_type' => 'shipping',
                  'first_name' => $address['Firstname'],
                  'middle_name' =>'',
                  'last_name' => '',
                  'email' => $address['email'],
                  'address1' => $address['Address'],
                  'address2' => $address['Address1'],
                  'landmark'=>$landmark,
                  'locality'=>$locality,
                  'city' => $address['City'],
                  'state' => $address['state'],
                  'phone' =>  $address['telephone'],
                  'pincode' => $address['pin'],
                  'country' => $address['country'],
                  'company' => $data['company'],
                  'area_id'=>$area_id,
                  'mobile_no' => ''
                 ),
               1=>array('address_type' => 'billing',
                  'first_name' => $address['Firstname'],
                  'middle_name' =>'',
                  'last_name' => '',
                  'email' => $address['email'],
                  'address1' => $address['Address'],
                  'address2' => $address['Address1'],
                  'landmark'=>$landmark,
                  'locality'=>$locality,
                  'city' => $address['City'],
                  'state' => $address['state'],
                  'phone' =>  $address['telephone'],
                  'pincode' => $address['pin'],
                  'country' => $address['country'],
                  'company' => $data['company'],
                  'area_id'=>$area_id,
                  'mobile_no' => '')) ;       
            
                $dbDiscountAmount = 0;
                $totalDiscountAmount =0;
                $discount = "";
                $discountType = "";
                $orderDiscount = $orderDiscountAmount = 0.00;
                $orderDiscountOnValues = $orderDiscountOn = $orderDiscountType = '';
                $sales_token = isset($array['sales_token'])?$array['sales_token']:'';
                if($sales_token == '')
                {
                  # Code to Check the Discount On Order
                  $orderDiscount = isset($array["discount"])?floatval($array["discount"]):0.00;
                  $orderDiscountType = isset($array["discount_type"])?$array["discount_type"]:'';
                  $orderDiscountOn = isset($array["discount_on"])?$array["discount_on"]:'';
                  $orderDiscountOnValues = isset($array["discount_on_values"])?floatval($array["discount_on_values"]):0.00;

                  $orderDiscountAmount = isset($array["discountAmount"])?floatval($array["discountAmount"]):0;
                  if($orderDiscount > 0 and $orderDiscountType != '' and $orderDiscountOn != '' and $orderDiscountOnValues > 0)
                  {
                    if($this->order->isDiscountApplicable($orderDiscount,$orderDiscountOn,$orderDiscountType,$orderDiscountOnValues))
                    {
                      $totalAmount = isset($array["total"])?floatval($array["total"]):0.00;
                      # if there is order level discount in the table
                      if($orderDiscountType == "percentage" and $totalAmount > 0)
                      {
                        $dbDiscountAmount = $orderDiscount * floatval($totalAmount) / 100;
                      }
                      else if($orderDiscountType == "value" and $totalAmount > 0 and floatval($totalAmount) > $orderDiscount)
                      {
                        $dbDiscountAmount = $orderDiscount;
                      }
                      $dbDiscountAmount = ($dbDiscountAmount < 0)?0:$dbDiscountAmount;
                    }
                    else
                    {
                     return json_encode(array('status'=>'-3','message'=> 'Discount promotion has been Expired. Please update the Cart.','data'=>[])); 
                    }
                  }
                         
                  $totalDiscountAmount=$dbDiscountAmount;
                }
                $Cartdetails = json_decode(json_encode($cart),true);   
                    $odpCounter = 0; 
                $placedProducts = [];
                if(is_array($Cartdetails) && !empty($Cartdetails))
                { 
                foreach($Cartdetails as $Cartdata)
                {                 
                $esu_quantity=(isset($Cartdata['esu_quantity']) && $Cartdata['esu_quantity']!='')? $Cartdata['esu_quantity']:''; 
                $star=(isset($Cartdata['star']) && $Cartdata['star']!='')? $Cartdata['star']:0; 
                $packs=$this->order->getProdPacks($Cartdata['cart_id'],$Cartdata['product_id']);
                $product_id = (isset($Cartdata['product_id']))?$Cartdata['product_id']:'';
                $wh_id = (isset($Cartdata['le_wh_id_list']))?$Cartdata['le_wh_id_list']:'';
                $quantity = (isset($Cartdata['quantity']))?$Cartdata['quantity']:0;
                ///// Started Blocked Quantity
                $isFreebie = $this->order->isFreebie($product_id);
                $warehouseId = DB::select("SELECT GetCPInventoryStatus(" . $product_id . ",'" . $wh_id . "',$segmentId,4) as le_wh_id ");
                $le_wh_id = isset($warehouseId[0]->le_wh_id)?$warehouseId[0]->le_wh_id:0;
                if ($customer_type == 3016) {
                    $query = DB::selectFromWriteConnection(DB::raw("select (dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty from `inventory` where `product_id` = $product_id and `le_wh_id` = $le_wh_id"));
                } else {
                    $checkInventory = DB::table('inventory')
                            ->select(DB::raw('inv_display_mode'))
                            ->where('product_id', '=', $product_id)
                            ->where('le_wh_id', '=', $le_wh_id)
                            ->get()->all();
                    $displaymode = isset($checkInventory[0]->inv_display_mode)?$checkInventory[0]->inv_display_mode:'soh';
                    $query = DB::selectFromWriteConnection(DB::raw("select ($displaymode-(order_qty+reserved_qty)) as availQty from `inventory` where `product_id` = $product_id and `le_wh_id` = $le_wh_id"));
                }
                $avail_quantity = isset($query[0]->availQty)?$query[0]->availQty:0;
                if ($quantity > ($avail_quantity+$quantity)) {
                    return json_encode(array('status'=>'0','message'=> Lang::get('cp_messages.lowinventory'),'data'=>[]));
                }
                /*if(!$isFreebie and (isset($Cartdata['is_slab']) and $Cartdata['is_slab'] == 1))
                {

                   Log::info('is_slab');
                     
                  /// If the Product is not a Freebie. then we check the blocked qty
                  // IF the proudct is a slab and the promotion has applied
                  $rate = (isset($Cartdata['rate']))?$Cartdata['rate']:0;
                  $cartModel = new CartModel();
                  $blckd_qty = $cartModel->getBlockedQty($rate,$product_id,$prmt_det_id);
                  if(empty($blckd_qty)){
                    $lckd_qty=0;
                  }else{
                    $lckd_qty=$blckd_qty->prmt_lock_qty;
                  }
                  if(($lckd_qty!=0) and ($quantity>$lckd_qty)){
                    # If the user is trying to place order than the expected quantity.
                    return json_encode(array('status'=>'-2','message'=> 'You are not allowed to order more than we give to you','data'=>$product_id));
                  }
                  else
                  {
                    Log::info('placedProducts');
                    Log::info($placedProducts);
                    array_push($placedProducts, ["product_id"=>$product_id,"quantity"=>$quantity,"prmt_det_id"=>$prmt_det_id]);
                  }
                }*/
                /////End Blocked Quantity

                /////////Start Discount Validation
                # Discount only for self orders and not for freebies
                $isDiscountApplicable = false;
                $discountError = [];
                if($sales_token == '' and !$isFreebie)
                {
                  $discount = (isset($Cartdata["discount"]) and $Cartdata["discount"] != '')?$Cartdata["discount"]:0.00;
                  $discountType = (isset($Cartdata["discount_type"]) and $Cartdata["discount_type"] != '')?$Cartdata["discount_type"]:'';
                  $discountOn = (isset($Cartdata["discount_on"]) and $Cartdata["discount_on"] != '')?$Cartdata["discount_on"]:'';
                  $discountAmount = (isset($Cartdata["discount_amount"]) and $Cartdata["discount_amount"] != '')?$Cartdata["discount_amount"]:0.00;
                  // $discountOnValues = (isset($Cartdata["discount_on_values"]) and $Cartdata["discount_on_values"] != '')?$Cartdata["discount_on_values"]:'';

                  if($discount != 0.00 and $discountType != '' and $discountOn != '')
                  {
                    $isDiscount = 0;
                    #Function to Check the Validty of the Discount  
                    $total_price = isset($Cartdata['prodtotal'])?$Cartdata['prodtotal']:0;
                    $star = isset($Cartdata['star'])?$Cartdata['star']:null;
                    if(($this->order->isDiscountApplicable($discount,$discountOn,$discountType,$star)) || (isset($array['ignore_discount_check']) and $array['ignore_discount_check'] ==1))
                    {

                      if($discountType == "percentage")
                        $discountAmount = $discount * floatval($total_price) / 100;
                      elseif(($discountType == "value") and floatval($total_price) > $discount)
                        $discountAmount = $discount;
                      $discountAmount = ($discountAmount < 0)?0:$discountAmount;
                      $totalDiscountAmount+=$discountAmount;
                    } 
                    else
                      return json_encode(array('status'=>'-3','message'=> 'Discount promotion has been Expired. Please update the Cart.','data'=>[]));
                 }
                }
                // print_r($total_price);die();
                // End Discount
                //////////////////////////////////////////////////////////////
                
                   $gdsPostInfo['product_info'][$odpCounter]=array(
                     'sku'=> $Cartdata['sku'],
                     'le_wh_id'=>$Cartdata['le_wh_id'],
        					   'hub_id'=>$Cartdata['hub'],
                     'channelId'=> Config::get('dmapi.channelid'),
                     'order_id'=> '',
                     'channelitemid'=> $Cartdata['product_id'],
                     'scoitemid'=> $Cartdata['product_id'],
                     'parent_id'=> $Cartdata['parent_id'],
                     'quantity'=> ($customer_type == 3016 && isset($Cartdata['dit_quantity']))?$Cartdata['dit_quantity']:$Cartdata['quantity'],
                     'esu_quantity'=> $esu_quantity,
                     'price'=> $Cartdata['mrp'],
                     'sellprice'=> $Cartdata['rate'],
                     'discounttype'=> '',
                     'discountprice'=> '',
                     'tax'=> '',
                     'subtotal'=> $Cartdata['prodtotal'],
                     'channelcancelitem'=> $Cartdata['rate'],
                     'total'=> $Cartdata['prodtotal'],
                     'company'=> $data['company'],
                     'servicename'=> '',
                     'servicecost'=> '',
                     'dispatchdate'=> '',
                     'mintimetodispatch'=>'',
                     'maxtimetodispatch'=> '',
                     'timeunits'=> '',
                     'star'=> $Cartdata['star'],
                      'discount'=> ($sales_token == '' and !$isFreebie)?$discount:0,
                      'discount_type'=> ($sales_token == '' and !$isFreebie)?$discountType:'',
                      'discount_on'=> ($sales_token == '' and !$isFreebie)?$discountOn:'',
                      'discount_amount'=>($sales_token == '' and !$isFreebie)?$discountAmount:0.00,
                      'product_slab_id'=> $Cartdata['product_slab_id'],
                     'prmt_det_id'=> $Cartdata['prmt_det_id'],
                     'freebee_qty'=> $Cartdata['freebee_qty'],
                     'freebee_mpq'=> $Cartdata['freebee_mpq'],
                     'packs'=>$packs
                      ) ;     
                    $odpCounter++; 
                  }
//                 Log::info('gdsPostInfo_product_info');
 //                Log::info($gdsPostInfo['product_info']);
               }else{
                   return json_encode(array('status'=>'0','message'=> 'Cart Refreshed','data'=>[]));    

                 }
               

                 
                 if(
                    $orderDiscountAmount != 0 and
                    $totalDiscountAmount !=0 and
                    $orderDiscountOn == "order" and
                    (number_format($totalDiscountAmount,10) != number_format($orderDiscountAmount,10))
                    )
                  {
                    # ERROR: Invalid Order Value Discount
                    return json_encode(array('status'=>'-3','message'=> 'Discount promotion has been Expired. Please update the Cart.','data'=>[]));
                  }

                  $instant_wallet_cashback = isset($array['instant_wallet_cashback'])?$array['instant_wallet_cashback']:0;
                  $trade_dis_cashback_applied = isset($array['trade_dis_cashback_applied'])?$array['trade_dis_cashback_applied']:0;
                  $trade_discount_ids = isset($array['trade_discount_ids'])?$array['trade_discount_ids']:"";
                  if($order_level_cashback == "" && $trade_discount_ids == ""){
                    $instant_wallet_cashback = 0;
                  }
                 $gdsPostInfo['order_info']=array(
                   'channelid'=>Config::get('dmapi.channelid'),     
                   'channelorderid'=>$rand_no,
                   'orderstatus'=>$orderstatus,
                   'orderdate'=>date('d-m-Y'),
                    'paymentmethod'=>$array['paymentmode'],
                    'shippingcost'=>'',
                    'subtotal'=>$array['total'] ,
                    'tax'=>' ',
                    'totalamount'=>$array['total'],
                    'discounttype'=>($sales_token == '' and $totalDiscountAmount != 0)?$orderDiscountType:'',
                    'discount'=>($sales_token == '' and $totalDiscountAmount != 0)?$orderDiscount:0.00,
                    'discountamount'=>($sales_token == '' and $totalDiscountAmount != 0)?$totalDiscountAmount:0,
                    'grandtotal'=>floatval($array['final_amount']),
                    'currencycode'=>'INR',
                    'channelorderstatus'=>'New',
                    'updateddate'=>date('d-m-Y'),
                    'gdsorderid'=>'',
                    'channelcustid'=>$data['customerId'],
                    'is_self'=>$is_self,
                    'customer_type'=>$customer_type,
                    'createddate'=>date('d-m-Y'),
                    'order_level_cashback'=>$order_level_cashback,
                    'mfc_id'=>$mfc_id,
                    'discount_on_tax_less'=>isset($array['discount_on_tax_less'])?$array['discount_on_tax_less']:0,
                    'instant_wallet_cashback'=>$instant_wallet_cashback,
                    'free_qty_offer'=>isset($array['freeSampleIds'])?$array['freeSampleIds']:"",
                    'bf_freeSampleIds'=>isset($array['bf_freeSampleIds'])?$array['bf_freeSampleIds']:"",
                    "trade_dis_cashback_applied"=>$trade_dis_cashback_applied,
                    "trade_discount_ids"=>$trade_discount_ids,
                    'auto_invoice'=>isset($array['auto_invoice'])?$array['auto_invoice']:0

                    );

                 $wCollectTxnId='';
                $merchTranId = '';
                //added Pay & Pickup payment option for all customer type.
                $payment_modes = ['cod','CnC','loc','MFC','PNP']; //cod- cash on delivery , loc- line of credit,PNP -Pay & Pickup
                if(in_array($array['paymentmode'],$payment_modes)){
                    $wCollectTxnId = null;
                    $merchTranId = null;
                  }else if($array['paymentmode'] == 'upi'){
                    $wCollectTxnId = $array['wCollectTxnId'];
                    $merchTranId = $array['merchTranId'];
                  }else{
                    return json_encode(array('status'=>"failed",'message'=> "Please check trasaction and merchant id",'data'=>[]));
                  }
				
                  $gdsPostInfo['payment_info']= array(
                    0=> array(
                     'order_id' => $array['orderId'],
                     'channelid' => Config::get('dmapi.channelid'),      
                     'paymentmethod' => $array['paymentmode'],
                     'paymentstatus' => 'Pending',
                     'paymentcurrency' => 'INR',
                     'amount' => $array['final_amount'],
                     'buyeremail' => $address['email'],
                     'buyername' => $address['Firstname'],
                     'buyerphone' => $address['telephone'],
                      'transactionId' => $wCollectTxnId,
                     'merchTranId' => $merchTranId,
                     'paymentDate' => date('Y-m-d H:i:s')));     
                $sales_token=(isset($array['sales_token']) && $array['sales_token']!='')? $array['sales_token']:''; 
                $platform_id=(isset($array['platform_id']) && $array['platform_id']!='')? $array['platform_id']:'';
                $pref_value=(isset($array['pref_value']) && $array['pref_value']!='')? $array['pref_value']:'';
                $pref_value1=(isset($array['pref_value1']) && $array['pref_value1']!='')? $array['pref_value1']:'';
                $created_by=(isset($array['sales_token']) && !empty($array['sales_token']))?$sales_rep_id:$customer_id;
                $gdsPostInfo['additional_info']= array(
                      'cart_id'=>$array['cartId'],
                      'customer_token'=>$array['customer_token'],  
                      'sales_token'=>$sales_token, 
                      'platform_id'=>$platform_id,
                      'company'=> $data['company'],
                      'preferred_delivery_slot1' =>  $pref_value,   
                      'preferred_delivery_slot2' =>  $pref_value1,             
                      'scheduled_delivery_date'=>$array['scheduled_delivery_date'],           
                      'sms_content'=> 'Thank you, your order has been placed successfully. Your order number is  ".$order_id." and your order will be shipped within 3 days. Order Delivery Date:"$delivery_date"',
                      'customer_id'=>$customer_id,
                      'sales_rep_id'=>$sales_rep_id,
                      'latitude'=>$latitude,
                      'longitude'=>$longitude,
                      'activity'=>107001,
                      'created_by'=>$created_by
                   );  
                $order_data_req = json_encode($gdsPostInfo); 
               
               $logs_aray['order_req']=$order_data_req;
                $request['parameters'] = $logs_aray;
                $request['apiUrl'] = 'orderlogs';
                $this->_orderapi = new OrderapiLogsModel();
                $this->_orderapi->OrderApiRequests($request); 
                $HostUrl=$this->order->getHostURL();                
                $url= 'http://'.$HostUrl.'/dmapi/v2/placeorder'; 
                Log::info("place order url");
                Log::info($url);
                $det= array();
                $det['api_key'] = Config::get('dmapi.GDSAPIKey');
                $det['secret_key'] = Config::get('dmapi.GDSAPISECRETKey');
                $det['orderdata']=$order_data_req;

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_POST, sizeof($det));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $det);
                curl_setopt($ch, CURLOPT_TIMEOUT,10);
                $output = curl_exec($ch);
             
                $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
                curl_close($ch);
                Log::info("Place order API Response");
                Log::info($output);
              //echo 'HTTP code: ' . $httpcode; exit;
               // Log::info('httpcode'); 
               // Log::info( $httpcode);
              if(isset($httpcode) && !empty($httpcode)) 
               {
              if($httpcode != 200 ) {                 
                  print_r(json_encode(array('status'=>"failed",'message'=> "Internal Server Error",'data'=>[])));

               } else {
                $arr = json_decode($output);

                if((isset($arr->Status) && $arr->Status == 1) || (isset($arr->status) && $arr->status == 1))  {
                if($customer_type==3015){
                        $msgss='Thank you for your Cash-n-Carry order. Please visit Ebutor CnC counter to pick up your items.';
                    }else{
                        if($cartData != ""){
                            $this->_poModel = new PurchaseOrder();
                            // here we are updating po so status flag in table since order is placed succesfully entered to queue!
                            $update = $this->_poModel->updateStockistOrderStatus($array['po_id'],$array['orderId'],1);
                            // Log::info("PO Stockist Order Button Clicked and ".$array['orderId']." upadted!");
                        }
                        $is_ff = (isset($array['isFF']) && $array['isFF']!='')? $array['isFF']:0;
                        if($is_ff  == 0){
                          $msgss='Your Order has been placed successfully and will receive confirmation to your register mobile number';
                        }else {
                          $msgss='Order placed successfully!@Order Score:@Target for month:';
                        }

                    }
                    if(isset($array['sales_token'])&& $array['sales_token']!=''){
                      $this->_register->UpdateCheckoutFfComments($array['sales_token'],$customer_id,107001,$latitude,$longitude);
                    }else{
                      DB::connection('mysql-write')->table('offline_cart_details')
                      ->where('cust_id',$customer_id)
                      ->delete();
                    }
           
                 return json_encode(array('status'=>"success",'message'=> $msgss,'data'=>['orderId'=>$array['orderId'],'orderAmount'=>ceil($array['final_amount'])]));

                       if((isset($array['sales_token']) && $array['sales_token']!=''))
                       {
                          
                           if((isset($array['latitude']) && $array['latitude']!='') && (isset($array['longitude']) && $array['longitude']!='')){
//                              log::info('order1');
                              $this->order->FFLogsUpdate_new($array);

                             }else{
  //                            log::info('order2');
                              $this->order->FFLogsUpdate($array['sales_token'],$array['customer_token']);

                             }
                        


                       }
                 }
                 else {
                  if(isset($arr->Message)){
                    $message = $arr->Message;
                  }elseif (isset($arr->message)) {
                    $message = $arr->message;
                  }else{
                    $message = "";
                  }
                  print_r(json_encode(array('status'=>"success",'message'=> "$message",'data'=>['orderId'=>$array['orderId'],'orderAmount'=>$array['final_amount']])));
                 }

              }
              }else{              
                return json_encode(array('status'=>"failed",'message'=> "Internal Server Error",'data'=>[]));            }
            
             }
              else{
             
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping Country is empty",'data'=>[])));die;
                  }
              }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping State is empty",'data'=>[])));die;
                  } 

              }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping telephone is empty",'data'=>[])));die;
                  }
             }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping Address is empty",'data'=>[])));die;
                  }
            }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping Firstname is empty",'data'=>[])));die;
                  }
           }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping pincode is empty",'data'=>[])));die;
                  }
          /* } else {             
                print_r(json_encode(array('status'=>"failed",'message'=>"scheduled delivery date is empty",'data'=>[])));die;
                  }  */       
         
           } else {             
                print_r(json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[])));die;
                  }
         } else {
                print_r(json_encode(array('status'=>"failed",'message'=> 'Please pass required parameters','data'=>""))); 
        die;
      }   

      }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
            }              
}

 public function addOrder() {

  try{

     if(isset($_POST['data'])) { 
        
      $array = json_decode($_POST['data'],true);   
        
      if((isset($array['sales_token']) && $array['sales_token']!='') && 
        (isset($array['customer_token']) && $array['customer_token']!='')) {   
          
          if(($array['sales_token']!= $array['customer_token'])) {
            $SalesToken= $this->order->validateToken($array['sales_token']);
            } else {
            print_r(json_encode(array('status'=>"failed",'message'=> "Field Force is not authorised to place Order by himself, so please select retailer",'data'=>[])));die;              
            }             
         }  else {
          $CustomerToken=$this->order->validateToken($array['customer_token']);     
        }
  
      if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) ||
        (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {
       
        $cart=$this->order->Cartdetails($array);       
        $val_prod= $this->order->val_product($cart);                     
            if($val_prod == 1) { 

              $segmentId=(isset($array['segment_id']) && $array['segment_id']!='')? $array['segment_id']:0;
             // $this->order->checkInventory($cart,$array['le_wh_id'],$segmentId);

              $customerAddress=$this->order->getShippingAddress($array['address_id']);            
              $address=json_decode(json_encode($customerAddress[0]),true); 

              $area_id=$this->order->GetAreaID($address['legal_entity_id']); 
            
           if(!empty($address['pin'])) {
               if(!empty($address['Firstname'])) {
                  if(!empty($address['Address'])) {
                    if(!empty($address['telephone'])) {
                      if(!empty($address['state'])) {
                        if(!empty($address['country'])) {

                    $Customerdata=$this->order->getCustomerData($array['customer_token']);
                    $data = json_decode(json_encode($Customerdata),true);    

                    $rand_no=mt_rand();    
            
                $gdsPostInfo = array();
               
             $gdsPostInfo['customer_info']= array
             ('suffix'=>' ','first_name'=> $data['firstname'],
                  'middle_name' => '',
                  'last_name' => $data['lastname'],
                  'email_address' => $data['email_id'],
                  'channel_user_id' => $data['customerId'],
                  'mobile_no' => $data['telephone'],
                  'dob' => '',
                  'channel_id' => Config::get('dmapi.channelid'),
                  'gender' => '',
                  'registered_date' =>'');
                                 
               $gdsPostInfo['address_info']= array
                 ( 0=>array('address_type' => 'shipping',
                  'first_name' => $address['Firstname'],
                  'middle_name' =>'',
                  'last_name' => '',
                  'email' => $address['email'],
                  'address1' => $address['Address'],
                  'address2' => $address['Address1'],
                  'city' => $address['City'],
                  'state' => $address['state'],
                  'phone' =>  $address['telephone'],
                  'pincode' => $address['pin'],
                  'country' => $address['country'],
                  'company' => $data['company'],
                  'area_id'=>$area_id,
                  'mobile_no' => ''
                 ),
               1=>array('address_type' => 'billing',
                  'first_name' => $address['Firstname'],
                  'middle_name' =>'',
                  'last_name' => '',
                  'email' => $address['email'],
                  'address1' => $address['Address'],
                  'address2' => $address['Address1'],
                  'city' => $address['City'],
                  'state' => $address['state'],
                  'phone' =>  $address['telephone'],
                  'pincode' => $address['pin'],
                  'country' => $address['country'],
                  'company' => $data['company'],
          'area_id'=>$area_id,
                  'mobile_no' => '')) ;       
            
                $gdsPostInfo['order_info']=array(
                   'channelid'=>Config::get('dmapi.channelid'),     
                   'channelorderid'=>$rand_no,
                   'orderstatus'=>'New',
                   'orderdate'=>date('d-m-Y'),
                    'paymentmethod'=>$array['paymentmode'],
                    'shippingcost'=>'',
                    'subtotal'=>$array['total'] ,
                    'tax'=>' ',
                    'totalamount'=>$array['final_amount'],
                    'currencycode'=>'INR',
                    'channelorderstatus'=>'New',
                    'updateddate'=>'',
                    'gdsorderid'=>'',
                    'channelcustid'=>$data['customerId'],
                    'createddate'=>date('d-m-Y'));

                 $Cartdetails = json_decode(json_encode($cart),true);   
                         
                    $odpCounter = 0; 
                foreach($Cartdetails as $Cartdata) {
                  $le_wh_id=$this->order->getwarehouseId($Cartdata['product_id'],$array['le_wh_id'],$segmentId);

                   $gdsPostInfo['product_info'][$odpCounter]=array(
                     'sku'=> $Cartdata['sku'],
                     'le_wh_id'=>$le_wh_id,
                     'channelId'=> Config::get('dmapi.channelid'),
                     'order_id'=> '',
                     'channelitemid'=> $Cartdata['product_id'],
                     'scoitemid'=> $Cartdata['product_id'],
                     'quantity'=> $Cartdata['quantity'],
                     'price'=> $Cartdata['mrp'],
                     'sellprice'=> $Cartdata['rate'],
                     'discounttype'=> '',
                     'discountprice'=> '',
                     'tax'=> '',
                     'subtotal'=> $Cartdata['prodtotal'],
                     'channelcancelitem'=> $Cartdata['rate'],
                     'total'=> $Cartdata['prodtotal'],
                     'company'=> $data['company'],
                     'servicename'=> '',
                     'servicecost'=> '',
                     'dispatchdate'=> '',
                     'mintimetodispatch'=>'',
                      'maxtimetodispatch'=> '',
                      'timeunits'=> '') ;     
            
                    $odpCounter++; 
                  }

                  $gdsPostInfo['payment_info']= array(
                    0=> array(
                     'order_id' => '',
                     'channelid' => Config::get('dmapi.channelid'),      
                     'paymentmethod' => $array['paymentmode'],
                     'paymentstatus' => 'Pending',
                     'paymentcurrency' => 'INR',
                     'amount' => $array['final_amount'],
                     'buyeremail' => $address['email'],
                     'buyername' => $address['Firstname'],
                     'buyerphone' => $address['telephone'],
                     'transactionId' => $array['myPayuId'],
                     'paymentDate' => date('Y-m-d H:i:s')));

                 
                $sales_token=(isset($array['sales_token']) && $array['sales_token']!='')? $array['sales_token']:''; 
                 $platform_id=(isset($array['platform_id']) && $array['platform_id']!='')? $array['platform_id']:'';

                     $gdsPostInfo['additional_info']= array(
                      'cart_id'=>$array['cartId'],
                      'customer_token'=>$array['customer_token'],  
                      'sales_token'=>$sales_token, 
                      'platform_id'=>$platform_id,                   
                      'sms_content'=> 'Thank you, your order has been placed successfully. Your order number is  ".$order_id." and your order will be shipped within 3 days. Order Delivery Date:"$delivery_date"'
                   );  
                $order_data_req = json_encode($gdsPostInfo); 

                //print_r($order_data_req);exit;
            
                $HostUrl=$this->order->getHostURL();                
                $url= 'http://'.$HostUrl.'/dmapi/v2/placeOrder'; 

                $det= array();
                $det['api_key'] = Config::get('dmapi.GDSAPIKey');
                $det['secret_key'] = Config::get('dmapi.GDSAPISECRETKey');
                $det['orderdata']=$order_data_req;
               
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_POST, sizeof($det));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $det);
                curl_setopt($ch, CURLOPT_TIMEOUT,10);
                $output = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

               // echo 'HTTP code: ' . $httpcode; exit;

              if($httpcode != 200 ) {                 
                  print_r(json_encode(array('status'=>"failed",'message'=> "Internal Server Error",'data'=>[])));
               } else {
                $arr = json_decode($output);

                if($arr->Status == 1)  {            
                 print_r(json_encode(array('status'=>"success",'message'=> "Your Order has been placed successfully and will receive confirmation to your register mobile number",'data'=>[])));
                 }
                 else {
                  print_r(json_encode(array('status'=>"success",'message'=> "$arr->Message",'data'=>[])));
                 }
              }
             }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping Country is empty",'data'=>[])));die;
                  }
              }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping State is empty",'data'=>[])));die;
                  } 

              }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping telephone is empty",'data'=>[])));die;
                  }
             }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping Address is empty",'data'=>[])));die;
                  }
            }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping Firstname is empty",'data'=>[])));die;
                  }
           }
              else{
                print_r(json_encode(array('status'=>"failed",'message'=> "Shipping pincode is empty",'data'=>[])));die;
                  }      
           } else {               
                print_r(json_encode(array('status'=>"failed",'message'=> "Invalid Product id",'data'=>[])));die;
                  }  
           } else {             
                print_r(json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[])));die;
                  }
         } else {
                print_r(json_encode(array('status'=>"failed",'message'=> 'Please pass required parameters','data'=>""))); 
        die;
      }   

      }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
            }              
}

    /*
      * Class Name: GetMyOrders
      * Description: Display Orders of customer
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 14th July 2016
      * Modified Date & Reason: 
    */
    
     public function GetMyOrders() {

      try{
      
      if(isset($_POST['data'])) { 

        $array = json_decode($_POST['data'],true);   

        if((isset($array['sales_token']) && $array['sales_token']!='') &&
           (isset($array['customer_token']) && $array['customer_token']!='') ) {  

             $SalesToken= $this->order->validateToken($array['sales_token']);            
          } else {
             $CustomerToken=$this->order->validateToken($array['customer_token']);     
        }
  if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) || (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {
    $legal_entity_id=(isset($array['legal_entity_id']) && $array['legal_entity_id']!='')? $array['legal_entity_id']:''; 
    $offset=(isset($array['offset']) && $array['offset']!='')? $array['offset']:''; 
    $offset_limit=(isset($array['offset_limit']) && $array['offset_limit']!='')? $array['offset_limit']:''; 
    $status_id=(isset($array['status_id']) && $array['status_id']!='')? $array['status_id']:'';       
      if((isset($array['sales_token']) && $array['sales_token']!='') && (isset($array['customer_token']) && $array['customer_token']!='') )
          {
              $sales_rep_id=$this->order->getcustomerId($array['sales_token']);
              $customer_id=$this->order->getcustomerId($array['customer_token']);                              
              if(($array['sales_token']!= $array['customer_token']))   
              {
                
                $order_details=$this->order->getCustomerOrder($customer_id,$sales_rep_id,$legal_entity_id,$offset,$offset_limit,$status_id);
              }
              else
              { 
              
                $sales_rep_id=$this->_role->getTeamByUser($sales_rep_id);  
      
                $order_details=$this->order->getCustomerOrder('',$sales_rep_id,$legal_entity_id,$offset,$offset_limit,$status_id);  
              }              
          }
          else 
          {           
            $customer_id=$this->order->getcustomerId($array['customer_token']);               
            $order_details=$this->order->getCustomerOrder($customer_id,'',$legal_entity_id,$offset,$offset_limit,$status_id);
          }        
            $res['status']="success";
            $res['data'] = array();
                 
            if(empty($order_details['Result'])) {
             $res['message']="No orders";
            }            
            else
            { 
              $res['message']="GetMyOrders";
              $res['data']=$order_details['Result'];
            }
           return json_encode(array('status'=>$res['status'],'message'=>$res['message'],'data'=>$res['data'],'count'=>$order_details['totalOrderCount']));
            } else {
          return json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[]));
          }
        } else {
        return json_encode(array('status'=>"failed",'message'=> 'Please pass required parameters','data'=>"")); 
      } 

      }catch (Exception $e)
            {
               
                return Array('status' => "failed", 'message' =>  "Internal server error", 'data' =>  []);
            } 
    }
    
    public function updateOrderStatus(){
      try{
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $status = "failed";
                    $message = 'Required Parameters not Passed.';
                    $data =[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
        
     //Order Update Notification to the customer via SMS and Email  
        if( isset($params['flag']) && $params['flag']==1){
          
          if(isset($params['orders']) && !empty($params['orders'])){
            $sku = array();
            $status_array = array();
            $failedOrder = array();
            $failedSKU = array();
            $i=0;
           
            foreach ($params['orders'] as $orders) {
               //print_r($orders);exit;
              $validOrder = $this->order->valOrderid($orders['order_id']);
              if($validOrder>0){
                foreach ($orders['products'] as $key=>$value) {
                  if(isset($value['sku_id']) && isset($value['status']) && !empty($value['status'])){
                    $sku_id=$value['sku_id'];
                    $valSku = $this->order->valOrderSku($sku_id,$orders['order_id']);
                    $valStatus = $this->order->valStatus($value['status']);
                    $order_id =$orders['order_id'];
                    
                    if($valSku==1 && $valStatus==1){
                 
                      $skuData = $this->order->getName($sku_id);
                      
                      if(isset($skuData->product_name) ){
                        $name= str_replace('%', ' ', $skuData->product_name);
                        }else{
                        $name = "";
                      }
                      
                      
                      $sku[] = $name;
                      
                      $status_id=$value['status'];
                      $status = $this->order->getStatus($status_id);
                      
                      
                      if(isset($status->master_lookup_name)){
                        $status_name = $status->master_lookup_name;
                        }else{
                        $status_name = "";  
                      }
                      $status_array[] = $status_name;
                      $result = $this->order->updateOrderStatus($status_name,$name, $orders['order_id']);
                      }else{
                      
                      
                      $failedSKU['sku'] = $sku_id;
                      $failedSKU['order_id'] = $order_id;
                      $failedSKUs[] = $failedSKU;
                    }
                    }elseif( (isset($value['status'])) && empty($value['status']) || (!isset($value['status']))){
                    if(isset($value['sku_id']) && !empty($value['sku_id'])){
                      $failedSKU['sku'] = $value['sku_id'];
                      $failedSKU['order_id'] = $order_id;
                      $failedSKUs[] = $failedSKU;
                      }else{
                      $failedSKU['sku'] = "";
                      $failedSKU['order_id'] = $order_id;
                      $failedSKUs[] = $failedSKU; 
                    }
                  }
                  $i++;
                }
                }else{
                $failedOrder[] = $orders['order_id'];
              }
            }
            if(!isset($failedSKUs)){
              
              $failedSKUs = [];
            }
            if(!isset($failedOrders)){
              
              $failedOrders = [];
            }
            if(!isset($failedSmss)){
              
              $failedSmss = [];
            }
            
            $failedCases = array();
            $failedCases['failedSku'] = $failedSKUs;
            $failedCases['failedOrder'] = $failedOrders;
            $failedCases['failedSms'] = $failedSmss;
            return array('status'=>"success",'message'=>"updateOrderStatus", 'data'=>$failedCases);
            }else{
            return Array('status' => 'failed', 'message' => 'Invalid Order', 'data' => []);
          }
          }
//Tracking Details Notification to the customer via SMS and Email
          elseif(isset($params['flag']) && $params['flag']==2){
          if(isset($params['tracking']) && !empty($params['tracking'])){
            
            
            
            $tracking=$params['tracking'];
            
            
            
            foreach ($tracking as $value) {
              
              
              
              $order_id=$value['order_id'];
              
              
              $validOrder = $this->order->valOrderid($order_id);
              
              
              if($validOrder==1){
                
                if(isset($value['seller_invoice_no']) && !empty ($value['seller_invoice_no']))
                {
                  $seller_invoice_no=$value['seller_invoice_no'];
                  }elseif(isset($value['seller_invoice_no']) && empty($value['seller_invoice_no'])){
                  $seller_invoice_error="Sellerinvoice is empty for Order".$order_id;
                  $seller_invoice_errors[] =  $seller_invoice_error;
                }
                
                
                
                if(isset($value['shipped_date']) && !empty($value['shipped_date']))
                {
                  $shipped_date=$value['shipped_date'];
                  }elseif(isset($value['shipped_date']) && empty($value['shipped_date'])){
                  $shipped_date_error="Shipped date is empty for Order".$order_id;
                  $shipped_date_errors[] =  $shipped_date_error;
                }
                
                
                if(isset($value['expected_delivery_date']) && !empty ($value['expected_delivery_date']))
                {
                  $expected_delivery_date=$value['expected_delivery_date'];
                  }elseif(isset($value['expected_delivery_date']) && empty($value['expected_delivery_date'])){
                  $expected_delivery_date_error="Expected Delivery Date is empty for Order".$order_id;
                  $expected_delivery_date_errors[] = $expected_delivery_date_error;
                }
                
                
                
                if(isset($value['tracking_id']) && !empty($value['tracking_id']))
                {
                  
                  $tracking_id=$value['tracking_id'];
                  
                  
                  if(isset($value['products']) && !empty($value['products']) ){
                    $products=$value['products'];
                    
                    $sku ='';
                    $track = '';
                    
                   
                    foreach ($products as $product) 
                    {
                        $sku_id=$product['sku_id'];
                      
                        $valSku = $this->order->valOrderSku($sku_id,$order_id);
      
                      if($valSku==1){
                        $skuData = $this->order->getName($sku_id);
                        if(isset($skuData->product_name) ){
                          $name= str_replace('%', ' ', $skuData->product_name);
                          }else{
                          $name = "";
                        }
                       
                        
                        $sku[] = $name;
                        $track[]=$tracking_id;
                        $tracking_result=$tracking_id;
                        
                        if(!empty ($expected_delivery_date) && !empty ($seller_invoice_no) 
                        && !empty($shipped_date) && !empty($tracking_id) && !empty($order_id) && !empty($sku_id))
                        {
                            $mobile_num = $this->order->getTelephone($order_id);
                            $smsData = json_encode(array_combine($sku,$track)); 
                            
                            $mobile=$mobile_num[0]->phone_no;
                            $message = '';
                            $message =  "For Order Number ".$order_id."\n" ;
                            $message .=  "Your tracking Number "."\n";
                            $message .=$smsData;
                          
                            $smsresult = $this->smsTracking($mobile,$message,$order_id,$mobile_num[0]->email,$smsData);
                             
                            if($smsresult==0){
                              
                              $failedSms['telephone'] = $mobile;
                              $failedSms['order_id'] = $order_id;
                              $failedSmss[] = $failedSms;
                            }
                        }
                        }else{
                        $failedSKU['sku'] = $sku_id;
                        $failedSKU['order_id'] = $order_id;
                        $failedSKUs[] = $failedSKU;    
                      }  
                    } 
                    }else{
                    return array('status'=>"failed",'message'=>"Product Array Empty",'data'=>[]);
                  }
                  }elseif(isset($value['tracking_id']) && empty($value['tracking_id'])){
                  $track_error="TrackingId is empty for Order".$order_id;
                  $track_errors[] = $track_error;
                }
                }else{
                
                
                $failedOrder['order_id'] = $order_id;
                $failedOrders[] = $failedOrder;
              }
            }
            
            if(!isset($failedSKUs)){
              
              $failedSKUs = [];
            }
            if(!isset($failedOrders)){
              
              $failedOrders = [];
            }
            if(!isset($failedSmss)){
              
              $failedSmss = [];
            }
            if(!isset($track_errors)){
              $track_errors= [];
            }
            
            if(!isset( $seller_invoice_errors)){
              $seller_invoice_errors= [];
            }
            
            if(!isset( $shipped_date_errors)){
              $shipped_date_errors= [];
            }
            
            if(!isset($failedTrackings))
            {
              
              $failedTrackings=[];
            }
            if(!isset($expected_delivery_date_errors)){
              $expected_delivery_date_errors= [];
            }
            
            
            
            $failedCases = array();
            $failedCases['failedSku'] = $failedSKUs;
            $failedCases['failedOrder'] = $failedOrders;
            $failedCases['failedSms'] = $failedSmss;
            $failedCases['trackingId'] = $track_errors;
            $failedCases['sellerinvoice'] = $seller_invoice_errors;
            $failedCases['shippeddate'] = $shipped_date_errors;
            $failedCases['expirydate'] =  $expected_delivery_date_errors;
            $failedCases['tracking'] =  $failedTrackings;
            
            return array('status'=>"success",'message'=>"updateShippingTracking",'data'=>$failedCases);
            }else{
            return Array('status' => 'failed', 'message' => 'Required Parameters not Passed', 'data' => []);
          }
          }else{
          return Array('status' => 'failed', 'message' => 'Required Parameters not Passed', 'data' => []);  
        }
        //UpdateOrderStatus UpdateTrackingDetails
                
        
        
      }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
      }
    }
      public function smsTracking($mno,$message,$order_id,$email,$smsData)
      {
        if(!empty($email) && !empty($smsData)){
          
          
          $to = $email;
          $subject = "Order Status :" .$order_id;
          
          $message = '';
          $message =  "For Order Number ".$order_id."\n" ;
          $message .=  "Your tracking Number "."\n";
          $message .=$smsData;
          
          $txt=$message;
          
          
          $headers = 'From: admin@ebutor.com' . "\r\n" .
          'Reply-To: admin@ebutor.com' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();
          mail($to, $subject, $txt, $headers);
        }
        
        $ch = curl_init();
        if(preg_match( '/^[A-Z0-9]{10}$/', $mno) && !empty($message)) {
          $ch = curl_init();
          $user=Config::get('dmapi.DB_USER');
          $receipientno= $mno; 
          $senderID=Config::get('dmapi.DB_SENDER_ID') ; 
          $msgtxt= $message; 
          $msg = str_replace('"', ' ', $msgtxt);
          $msgtxt = str_replace('{', '', $msg);
          $msgtxt = str_replace('}', '', $msgtxt);
          
          curl_setopt($ch,CURLOPT_URL, Config::get('dmapi.DB_URL'));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$msgtxt");
          //print_r($ch);exit;
          $buffer = curl_exec($ch);
          
          if(empty ($buffer))
          { echo " buffer is empty "; 
            
            curl_close($ch);
            return 0; 
          }
          else
          { 
            curl_close($ch);
            return 1;
          }
        } 
      }
    
    /*
* Class Name: cancelOrder
* Description: Display Orders of customer
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 14th July 2016
* Modified Date & Reason: 
*/

public function cancelOrder() {

  try{

   if(isset($_POST['data'])) { 
    
     $array = json_decode($_POST['data'],true); 

        if((isset($array['sales_token']) && $array['sales_token']!='') &&
           (isset($array['customer_token']) && $array['customer_token']!='') ) {  

             $SalesToken= $this->order->validateToken($array['sales_token']);            
          } else {
             $CustomerToken=$this->order->validateToken($array['customer_token']);     
        }

        if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) || 
           (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {

        if((isset($array['reason_id']) && $array['reason_id']!='' )&&
           (isset($array['comments']) && $array['comments']!='') && 
            (isset($array['orderID']) && $array['orderID']!='')) {
  
        
                 $this->cancelorderdmapi($array); 
            } else {
                  print_r(json_encode(array('status'=>"failed",'message'=>"reason_id or comments or orderID is not passed",'data'=>[])));die; 
                   }
          } else {
                  print_r(json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[])));die; 
                 }
        } else {
                  print_r(json_encode(array('status'=>"failed",'message'=> 'Please pass required parameters','data'=>""))); die; 
                 } 

 }catch (Exception $e)
{
    $status = "failed";
    $message = "Internal server error";
    $data = [];
    return Array('status' => $status, 'message' => $message, 'data' => $data);
} 
}    
    
    public function cancelorderdmapi($array) {

      if(isset($array['orderID']) && isset($array['product_id'])) {

       $gdsprd=$this->order->getProdetails($array['orderID'],$array['product_id']);

       }
       else {
       $gdsprd=$this->order->getProdetails($array['orderID'],'');
       }

       if(isset($array['orderID']) && $array['orderID']!=''){

        $checkOrderStatus=$this->order->getOrderStatusById($array['orderID']);
        $checkOrderStatus=isset($checkOrderStatus[0]->order_status_id)?$checkOrderStatus[0]->order_status_id:'';
        $openpickrtdstatusarray=array('17001','17020','17005');
        if(!in_array($checkOrderStatus,$openpickrtdstatusarray)){
              $res['data']['requestId']= $array['orderID']  ;
              $res['data']['status']=$checkOrderStatus;
              print_r(json_encode(array('status'=>"success",'message'=> "You cannot cancel this order,Invoice already Generated",'data'=>$res['data']))); die();
        }
       }

       $sales_token=(isset($array['sales_token']) && $array['sales_token']!='')? $array['sales_token']:'';

        $data = array();
         
        $data['channel_order_id']=$array['orderID']; 
        $data['order_id']=$array['orderID'];
        $data['channelId']= Config::get('dmapi.channelid');
        $data['sales_token']=$sales_token;
        $data['customer_token']=$array['customer_token'];

        $i = 0;
        foreach($gdsprd as $gds)
        {     
              
        $data['product_info'][$i]['sku']= $gds['sku'];
        $data['product_info'][$i]['channelitemid']= $gds['product_id']; 
        $data['product_info'][$i]['product_id']= $gds['product_id'];  
        $data['product_info'][$i]['quantity']= $gds['qty']; 
        $data['product_info'][$i]['cancel_reason_id']= $array['reason_id'];
        $data['product_info'][$i]['comments']= $array['comments'];       
        $i++; 
       }

      $order_data_req = json_encode($data);

      $HostUrl=$this->order->getHostURL();                
      $url= 'http://'.$HostUrl.'/dmapi/cancelOrder'; 

      $res= array();
      $res['api_key'] = Config::get('dmapi.CR_GDSAPIKey');
      $res['secret_key'] = Config::get('dmapi.CR_GDSAPISECRETKey');
      $res['orderdata']=$order_data_req;

      $ch = curl_init();
      //set the url, number of POST vars, POST data
      curl_setopt($ch,CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch,CURLOPT_POST, count($res));
      curl_setopt($ch,CURLOPT_POSTFIELDS, $res);

      $result_api = curl_exec($ch);
  
            
      curl_close($ch);
      $arr = json_decode($result_api,TRUE);

     //print_r($result_api);exit;

      if($arr['Status'] == 1)
      {
        
       
        $order_status="CANCELLED BY CUSTOMER";                         
            
        $res['data']['requestId']= $array['orderID']  ;
        $res['data']['status']=$order_status;
      
        print_r(json_encode(array('status'=>"success",'message'=> "Your order has been successfully cancelled",'data'=>$res['data'])));       
        /**
        *  order code from order table against order id
        */
        $order_id=$this->order->getOrderCode($array['orderID']);
               
         /*  if(isset($array['product_id']) && $array['product_id']!='') {

           $message = "Your ".$gdsprd[0]['pname']." of Order ". $order_id." having quantity ".$gdsprd[0]['qty']." has been cancelled successfully and your request will be processed  shortly."; 
      
            $messageContent = Lang::get('cp_messages.CancelorderProduct');        
            $message =  str_replace(['{order_id}','{product_name}','{product_qty}'],[$order_id,$gdsprd[0]['pname'],$gdsprd[0]['qty']], $messageContent);      
        
          } else{          
            //$message="Your order ".$order_id." has been cancelled successfully and your request will be processed  shortly.";
             $messageContent = Lang::get('cp_messages.Cancelorder');            
            $message = str_replace('{order_id}', $order_id, $messageContent);
                       
          }
           
             $this->order->sendsms($array['customer_token'],$message);

             if(isset($array['sales_token']) && $array['sales_token']!='') {
               $this->order->sendsms($array['sales_token'],$message);
             }*/

            $order_status_id="17009";           
            $this->orderhistory->orderhistory($array['orderID'],$data['product_info'],$order_status,$order_status_id);
                        
      } 
      else if($arr['Status'] == 500)
      { 
        
        print_r(json_encode(array('status'=>"failed",'message'=> "Order is already cancelled",'data'=>[])));die;  
      }
      else if($arr['Status'] == 0)
       {     
             $message=$arr['Message'];
             print_r(json_encode(array('status'=>"failed",'message'=>$message,'data'=>[])));
       }
       else if($arr['Status'] == 404)
       {     
             $message=$arr['Message'];
             print_r(json_encode(array('status'=>"failed",'message'=>$message,'data'=>[])));
       }
      else 
      {          
         print_r(json_encode(array('status'=>'failed','message'=> 'Order Cancellation is Unsuccessful','data'=>[])));die;  
      }
}

public function Orderdetails() {
  try{

     if(isset($_POST['data'])) { 
    
     $array = json_decode($_POST['data'],true); 

     if((isset($array['sales_token']) && $array['sales_token']!='') &&
           (isset($array['customer_token']) && $array['customer_token']!='') ) {  

             $SalesToken= $this->order->validateToken($array['sales_token']);            
          } else {
             $CustomerToken=$this->order->validateToken($array['customer_token']);     
        }

        if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) || 
           (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {

             $valOrder = $this->order->valOrderProd($array['orderID'],'');            

             if($valOrder > 0) {                 
               
                 $data['orderid']=$array['orderID'];
                 $data['channelid']=Config::get('dmapi.channelid');
                 $order_data_req = json_encode($data);

                 $HostUrl=$this->order->getHostURL();                
                 $url= 'http://'.$HostUrl.'/dmapi/getOrderDetails'; 

                    $res= array();
                    $res['api_key'] = Config::get('dmapi.CR_GDSAPIKey');
                    $res['secret_key'] = Config::get('dmapi.CR_GDSAPISECRETKey');
                    $res['orderdata']=$order_data_req;

                  $ch = curl_init();

                  //set the url, number of POST vars, POST data
                  curl_setopt($ch,CURLOPT_URL, $url);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                  curl_setopt($ch,CURLOPT_POST, count($res));
                  curl_setopt($ch,CURLOPT_POSTFIELDS, $res);
                   
                  
                  $result_api = curl_exec($ch);                   
                  
                  curl_close($ch);
                  $arr = json_decode($result_api,TRUE);
                   if($arr['Status'] == 1)  {

                  $res =array();

                   $res=array(
                  'order_id'=>$arr['Message']['gds_order_id'],
                  'order_code'=>$arr['Message']['order_code'],
                  'date_added'=>$arr['Message']['order_date'],
                  'tax_total'=>$arr['Message']['tax_total'],
                  'total'=>$arr['Message']['grand_total'],
                  'sub_total'=>$arr['Message']['sub_total'],
                  'coupon'=>'',
                  'discount_amount'=>$arr['Message']['discount_amount'],
                  'status'=>  $arr['Message']['order_status'],
                  'shipping_firstname' => $arr['Message']['shipping']['fname'],
                  'shipping_lastname'=> $arr['Message']['shipping']['lname'],
                  'shipping_email'=> $arr['Message']['email'],
                  'shipping_telephone'=> $arr['Message']['shipping']['telephone'],
                  'shipping_address'=> $arr['Message']['shipping']['addr1'],
                  'shipping_address2'=> $arr['Message']['shipping']['addr2'],
                  'shipping_city'=> $arr['Message']['shipping']['city'],
                  'shipping_pin'=>$arr['Message']['shipping']['postcode'],
                  'shipping_state'=> $arr['Message']['shipping']['state_name'],
                  'shipping_country'=> $arr['Message']['shipping']['country_name']
                  );

                  if(!empty($arr['Message']['shipping_track_details']))
                  {
                    
                     foreach($arr['Message']['shipping_track_details'] as $trackking) {

                      $res['shipping_track_details']=$trackking;                    
                      }
                  }                  
                  
                  $i=0;
                            if(isset($arr['Message']['products'])){
                  foreach($arr['Message']['products'] as $val) {

                    $order_id=$arr['Message']['gds_order_id'];
                    $product_id=$val['product_id'];
                    
                    $res['products'][$i]=array(
                   'product_id'=> $val['product_id'],
                   'name'=> $val['product_name'],                   
                   'variant_id'=> $val['product_id'],
                   'image' => $val['product_image'],
                   'pack_size'=>'',
                   'dealer_price'=>$val['total'],
                   'unit_price'=>$val['unit_price'],
                   'margin'=>'',
                   'order_status'=>$val['product_order_status'],
                   'qty' =>$val['order_qty'],
                   'tax'=>$val['tax'],
                   'status' => $val['status']
                   );
                
                   $i++;
                  }
                  $res['order_track'] = $arr['Message']['order_track'];
                  $res['delivery_slot'] = $arr['Message']['delivery_slot'];

                  }
                    print_r(json_encode(array('status'=>"success",'message'=> "orderDetails",'data'=>$res)));               
                  }
                  else if($arr['Status'] == 0)  {

                    print_r(json_encode(array('status'=>"failed",'message'=>$arr['Message'],'data'=>[])));       
                  
                  }

            }  
            else {
              print_r(json_encode(array('status'=>"failed",'message'=>"OrderId mismatch",'data'=>[])));die; 
                 }            
          } else {
              print_r(json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[])));die; 
                 }
         } else {
               print_r(json_encode(array('status'=>"failed",'message'=> 'Please pass required parameters','data'=>""))); die; 
                }   

   }catch (Exception $e)
    {
        $status = "failed";
        $message = "Internal server error";
        $data = [];
        return Array('status' => $status, 'message' => $message, 'data' => $data);
    }      
      } 

    /*
* Class Name: returnOrder
* Description: return the order
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 14th July 2016
* Modified Date & Reason: 
*/

public function returnOrder() {

  try{

   if(isset($_POST['data'])) { 
    
     $array = json_decode($_POST['data'],true); 

     if((isset($array['sales_token']) && $array['sales_token']!='') &&
           (isset($array['customer_token']) && $array['customer_token']!='') ) {  

             $SalesToken= $this->order->validateToken($array['sales_token']);            
          } else {
             $CustomerToken=$this->order->validateToken($array['customer_token']);     
        }

        if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) || 
           (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {

        if((isset($array['reason_id']) && $array['reason_id']!='' )&&
           (isset($array['comments']) && $array['comments']!='') && 
           (isset($array['orderID']) && $array['orderID']!='')) {
 
            $this->returnorderdmapi($array);                                              
        } else {
              print_r(json_encode(array('status'=>"failed",'message'=>"reason_id or comments or orderID is not passed",'data'=>[])));die; 
               }
      } else {
              print_r(json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[])));die; 
             }
    } else {
              print_r(json_encode(array('status'=>"failed",'message'=> 'Please pass required parameters','data'=>""))); die; 
             } 


 }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
            }             
}    
    
  public function returnorderdmapi($array) {

    if(isset($array['orderID']) && isset($array['product_id']) && (isset($array['quanitity']) && $array['quanitity']!='')) {
      
       $gdsprd=$this->order->getProdetails($array['orderID'],$array['product_id']);

        $data = array();         
        $data['channelorderid']=$array['orderID']; 
        $data['orderid']=$array['orderID'];
        $data['channelid']=Config::get('dmapi.channelid');

        $data['orderitems']['sku']= $gdsprd[0]['sku'];
        $data['orderitems']['channelid']= Config::get('dmapi.channelid');
        $data['orderitems']['channelitemid']= $array['product_id']; 
        $data['orderitems']['product_id']= $array['product_id'];  
        $data['orderitems']['quantity']= $array['quanitity']; 
        $data['orderitems']['returnreasonid']= $array['reason_id'];
        $data['orderitems']['comments']= $array['comments'];    
               
    }

    else
    {
       
      if(isset($array['orderID']) && (isset($array['product_id']) && $array['product_id']!='') ) {

       $gdsprd=$this->order->getProdetails($array['orderID'],$array['product_id']);

       }
       else {
       $gdsprd=$this->order->getProdetails($array['orderID'],'');
       }


        $data = array();         
        $data['channelorderid']=$array['orderID']; 
        $data['orderid']=$array['orderID'];
        $data['channelid']=Config::get('dmapi.channelid');
        $i = 0;
        foreach($gdsprd as $gds)
        {            
        
        $data['orderitems'][$i]['sku']= $gds['sku'];
        $data['orderitems'][$i]['channelid']= Config::get('dmapi.channelid');
        $data['orderitems'][$i]['channelitemid']= $gds['product_id']; 
        $data['orderitems'][$i]['product_id']= $gds['product_id'];  
        $data['orderitems'][$i]['quantity']= $gds['qty']; 
        $data['orderitems'][$i]['returnreasonid']= $array['reason_id'];
        $data['orderitems'][$i]['comments']= $array['comments'];       
        $i++; 
       } 
    }

    $order_data_req = json_encode($data);


     $HostUrl=$this->order->getHostURL();                
     $url= 'http://'.$HostUrl.'/dmapi/returnOrder'; 
     
      $res= array();
      $res['api_key'] = Config::get('dmapi.CR_GDSAPIKey');
      $res['secret_key'] = Config::get('dmapi.CR_GDSAPISECRETKey');
      $res['orderdata']=$order_data_req;

      $ch = curl_init();
      
      curl_setopt($ch,CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch,CURLOPT_POST, count($res));
      curl_setopt($ch,CURLOPT_POSTFIELDS, $res);

      $result_api = curl_exec($ch);
  
            
      curl_close($ch);
      $arr = json_decode($result_api,TRUE);
      
      if($arr['Status'] == 1)
      {       
      
        $order_status="RETURN INITIATED";                         
               
        $res['data']['requestId']=$array['orderID'];
        $res['data']['status']= $order_status;
        $res['data']['pickupDate']=" your product will be picked within 2 to 3 days";
      
        print_r(json_encode(array('status'=>"success",'message'=>"Your order is successfully Returned",'data'=>$res['data'])));
        /**
        * Gorder code from order table against order id
        */
        $order_id=$this->order->getOrderCode($array['orderID']);

         if(isset($array['orderID']) && (isset($array['product_id']) && $array['product_id']!='') )  {

          $quanity=(isset($array['quantity']) && $array['quantity']!='')? $array['quantity']:$gdsprd[0]['qty'];

              /*$message="The return request for your product ".$gdsprd[0]['pname']." of order  ". $order_id." having quantity ".$quanity."  has been placed successfully and the goods will be picked up within 3 days. Your money will be refunded within 4 business days after your goods are picked up."; */

            $messageContent = Lang::get('cp_messages.ReturnorderProduct');        
            $message =  str_replace(['{order_id}','{product_name}','{product_qty}'],[$order_id,$gdsprd[0]['pname'],$quanity],$messageContent);        
          } 
          else if(isset($array['orderID']) && empty($array['product_id'])){          
           /* $message = "The return request for your order  ". $order_id."  has been placed successfully and the goods will be picked up within 3 days. Your money will be refunded within 4 business days after your goods are picked up.";*/
            
			$messageContent = Lang::get('cp_messages.Returnorder');            
            $message = str_replace('{order_id}', $order_id, $messageContent);
          }
          /**
          * Sms to customer & field force
          */
           $this->order->sendsms($array['customer_token'],$message);

           if(isset($array['sales_token']) && $array['sales_token']!='') {
             $this->order->sendsms($array['sales_token'],$message);
           }

       
        $order_status_id="17010"; 
        $this->orderhistory->orderhistory($array['orderID'],$data['orderitems'],$order_status,$order_status_id);
     } 
     else if($arr['Status'] == 400)
     {
          $message=$arr['Message'];
          print_r(json_encode(array('status'=>"failed",'message'=>$message,'data'=>[])));
     } 
     else if($arr['Status'] == 0)
     {     
           $message=$arr['Message'];
           print_r(json_encode(array('status'=>"failed",'message'=>$message,'data'=>[])));
     }
     else if($arr['Status'] == 500)
     {     
           $message=$arr['Message'];
           print_r(json_encode(array('status'=>"failed",'message'=>$message,'data'=>[])));
     }
       else 
      {          
         print_r(json_encode(array('status'=>"failed",'message'=> "Return request is unsuccessfull",'data'=>[])));die;  
      }
}

/*
    * Class Name: returnReasons
    * Description: the function is used sort data fileds   
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 26th July 2016
    * Modified Date & Reason: 18th July 2016
      reson:added master category_id
       
    */ 
        
   public function returnReasons(){

        $reasons = $this->order->returnReasons();
         if(!empty($reasons))   
            {
             $res['status']="success";
                  $res['message']="returnresons";
                  $res['data']=$reasons;

                }
                  else
                  { 
                    $res['status']="failed";
                    $res['message']="No returnresons found";
                    $res['data']=[];
                  }
  
     $response=json_encode($res); 
           return $response;   
        }
        
        /*
    * Class Name: returnReasons
    * Description: the function is used sort data fileds   
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 26th July 2016
    * Modified Date & Reason: 18th July 2016
      reson:added master category_id
       
    */ 
        
   public function cancelReasons_old(){

        $reasons = $this->order->cancelReasons();
         if(!empty($reasons))   
            {
             $res['status']="success";
                  $res['message']="cancelReasons";
                  $res['data']=$reasons;

                }
                  else
                  { 
                    $res['status']="failed";
                    $res['message']="No cancelReasons found";
                    $res['data']=[];
                  }
  
     $response=json_encode($res); 
           return $response;   
        }

        /*
    * Class Name: printInvoice
    * Description: Printing invoice content call function.  
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 26th July 2016
    * Modified Date & Reason: 18th July 2016
      reson:added master category_id
       
    */ 

    public function generateInvoice($orderId)
    {
            
        try
        {
            /**
             * Define invoice id and order id required.
             * @var string
             *
            */
            $order_id = $orderId;

            $order_invoice = $this->order->getInvoice($order_id);
            if(empty($order_invoice))
            {
               print_r(json_encode(array('status'=>"success",'message'=>"No invoice details",'data'=>[])));

            }
            else {

                $HostUrl=$this->order->getHostURL();                
                $form_url= 'http://'.$HostUrl.'/dmapi/printInvoice';
                
                $res= array();
                $res['api_key'] = Config::get('dmapi.CR_GenerateInvoiceAPIKey');
                $res['secret_key'] = Config::get('dmapi.CR_GenerateInvoiceAPISECRETKey');
                $res['orderdata'] = '{   "invoiceId": '.$order_invoice.',   "orderId": '.$order_id.' }';

                $ch = curl_init();
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $form_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch,CURLOPT_POST, count($res));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $res);
                $result_api = curl_exec($ch);                     
                curl_close($ch);

                $arr = json_decode($result_api,TRUE);
                $arr = json_decode(json_encode($arr), FALSE);
                //print_r($arr); exit;
                 if($arr->Status == 1)  {

                 $billing        = $arr->Message->billing;
                 $products       = $arr->Message->products;
                 $shipping       = $arr->Message->shipping;
                 $taxArr         = $arr->Message->taxArr;
                 $taxSummaryArr  = $arr->Message->taxSummaryArr;
                 $leInfo         = $arr->Message->leInfo;
                 $lewhInfo       = $arr->Message->lewhInfo;
                 $companyInfo    = $arr->Message->companyInfo;
                 $taxBreakup     = $arr->Message->taxBreakup ;
                 $trackInfo      = $arr->Message->trackInfo ;
                 $orderDetails   = $arr->Message->orderDetails ;
                 $legalEntity    = $arr->Message->legalEntity ;
                 $userInfo       = $arr->Message->userInfo;
                 $prodTaxes      = $arr->Message->prodTaxes;

                  //header("Content-type: application/pdf");
                  $pdf = PDF::loadView('invoice.dmapiInvoice', compact(
                    'billing',
                    'products',
                    'shipping',
                    'taxArr',
                    'taxSummaryArr',
                    'leInfo',
                    'lewhInfo',
                    'companyInfo',
                    'taxBreakup',
                    'trackInfo',
                    'orderDetails',
                    'legalEntity',
                    'userInfo',
                    'prodTaxes'
                    )
                  );
                /**
                * return $pdf->stream('invoice.pdf'); //to display in browser
                */
                    return $pdf->download('invoice.pdf');

                 }
                 else{                

                  print_r(json_encode(array('status'=>"success",'message'=>$arr->Message,'data'=>[])));

                 }

          }
        }
        catch(Exception $e)
        {
            echo "error"."Internal server error";
        }
    }

    /*
    * Class Name: cancelReasons
    * Description: the function is used sort cancel reasons  
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15th Nov 2016       
    */ 

public function cancelReasons() {

 $result= $this->order->getcancelReasons();   
    
     if(!empty($result))
     {
        $res['status']="success";
        $res['message']="cancelReasons";
        $res['data']=$result;
      }else{
        $res['status']="success";
        $res['message']="No data";
        $res['data']=[];
      }
  
       $response=json_encode($res); 
       return $response;                  
  }  
  
   public function generateOtpOrder() {

 $data = Input::all();            
 $arr = isset($data['data'])?json_decode($data['data']):array();

if (isset($arr) && !empty($arr)) {

 if((isset($arr->sales_token) && $arr->sales_token!='') &&
           (isset($arr->customer_token) && $arr->customer_token!='') ) {  

             $SalesToken= $this->order->validateToken($arr->sales_token);            
          } else {
             $CustomerToken=$this->order->validateToken($arr->customer_token);     
        }

        if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) || 
           (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {

          if(isset($arr->telephone) && !empty($arr->telephone)) 
          {
                                
             $otp = $this->_account->generateOtp($arr->customer_token,$arr->telephone);
     
                   if($otp) {

                    return array('status'=>"success",'message'=>'Please Confirm Otp', 'data'=>$otp);
                             
                    }
                    else
                    {
                       return Array('status' => 'failed', 'message' => 'Unable to get otp', 'data' => "");  
                    } 
                    }else{
                        return Array('status' => 'failed', 'message' => 'Telephone is empty', 'data' => []);  
                    }
                  } else {
                      return json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[]));
                     }
            } else {
                return json_encode(array('status' => "failed", 'message' => "Pass  token", 'data' => []));
                die;
            }
}

  public function orderOtpConfirmation() {

 $data = Input::all();            
 $arr = isset($data['data'])?json_decode($data['data']):array();

if (isset($arr) && !empty($arr)) {

 if((isset($arr->sales_token) && $arr->sales_token!='') &&
           (isset($arr->customer_token) && $arr->customer_token!='') ) {  

             $SalesToken= $this->order->validateToken($arr->sales_token);            
          } else {
             $CustomerToken=$this->order->validateToken($arr->customer_token);     
        }

        if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) || 
           (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {

          if(isset($arr->telephone) && !empty($arr->telephone)) 
          {
          if(isset($arr->otp) && !empty($arr->otp)) 
          {  
          $otp = $this->_account->getOtp($arr->customer_token);
          if( $arr->otp!= $otp[0]->otp)
          {
            
         return array('status'=>"failed",'message'=>Lang::get('cp_messages.InvalidOtp'), 'data'=>[]);
         }else{

           return array('status'=>"success",'message'=>"Valid Otp", 'data'=>[]);
         }
                   }else{
                        return Array('status' => 'failed', 'message' => 'Otp is empty', 'data' => []);  
                    }
                    }else{
                        return Array('status' => 'failed', 'message' => 'Telephone is empty', 'data' => []);  
                    }
                  } else {
                      return json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[]));
                     }
            } else {
                return json_encode(array('status' => "failed", 'message' => "Pass  token", 'data' => []));
                die;
            }
}

 public function getFilterOrderStatus() {
 $data = Input::all();            
 $arr = isset($data['data'])?json_decode($data['data']):array();
if (isset($arr) && !empty($arr)) {
 if((isset($arr->sales_token) && $arr->sales_token!='') &&(isset($arr->customer_token) && $arr->customer_token!='') ) {  
             $SalesToken= $this->order->validateToken($arr->sales_token);            
          } else {
             $CustomerToken=$this->order->validateToken($arr->customer_token);     
        }
if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1) || (isset($SalesToken['token_status']) && $SalesToken['token_status']==1 )) {
          $result = $this->order->getFilterOrderStatus();
          if($result)
          {
        return json_encode(array('status'=>"success",'message'=>"getFilterOrderStatus",'data'=>$result));  
          }else{
         return json_encode(array('status'=>"success",'message'=>"No data",'data'=>[]));  
          }
           } else {
           return json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[]));
            }
            } else {
                return json_encode(array('status' => "failed", 'message' => "Pass  token", 'data' => []));
                die;
            }
}
   /**
     * [orderReference description]
     * @return [type] [description]
     * created by @Optimizer team 
     * created Date 13 Nov Its Sunday !!!!
     
    public function genarateOrderref(){
     
        $return = array();
        $return['status'] = false;
        $le_wh_id = json_decode($_POST['data'],TRUE); 
        try{
            $state = $this->order->generateOrderref($le_wh_id['le_wh_id']);
            $return['status'] = true;
            $return['data'] =  $state;
        }catch(\Exception $e){
            
            $return['data'] = '';
        }
        
       $response=json_encode($return); 
       return $response;  
    }*/
	 public function genarateOrderref(){
		    $status = "failed";
        try{			
      			$array = json_decode($_POST['data'],true);
      			if(isset($array['le_wh_id']) && $array['le_wh_id']!='') { 
			
            $state = $this->order->generateOrderref($array['le_wh_id']);
			
    			  if(empty($state)){
                  $message='Details not found';
                  $data=[];
                }else
                {
                  $status = "success";
                  $message='Order Code';
                  $data= $state;
                }
                return json_encode(Array('status'=>$status,'message'=>$message,'data'=>$data)); 
    			  } else {
              return json_encode(Array('status' => $status, 'message' =>'legal warehouse id not sent', 'data' =>''));
            }
        }catch(\Exception $e){
    			$message = "Internal server error";
    			$data = [];
    			return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
    }
    public function getPickerDeliveryData(){
      try{
          $data=json_decode($_POST['data']);
          if (isset($data) && !empty($data)) {

            $parentHubdata=explode(",",$data->hub);
            $pickerDeliverydata=$this->_rolerepo->getUsersByFeatureCodeWithoutLegalentity($parentHubdata,$data->flag);
            return Array('status'=>'success','message'=>'success','data'=>$pickerDeliverydata);
          }else{
            return Array('status'=>'failed','message'=>'input is required','data'=>[]);
          }

      }catch(Exception $e){

        return Array('status'=>'failed','message'=>"Internal server error",'data'=>[]);

      }
    }

    public function getffmaps(){
        $input = json_decode($_POST['data'],true);
        $data = $this->order->getFfDynamicData($input);
        return $data;
    }
    public function getSalesList(Request $request){
         try {
          if (isset($_POST['data'])) {
              $data = $_POST['data'];
              $array = json_decode($data,true);
              $data =array();
              if (isset($array['mb_num'])) {
                  if (!empty($array['mb_num']) && strlen($array['mb_num'])==10 && is_numeric($array['mb_num'])) {
                      $phone_no =$array['mb_num'];
                  }else{
                    return Array('status' => 'failed', 'message' =>'PLease Enter valid Mobile Number', 'data' => []);
                  }
              }else{
              }
              if (isset($array['fr_date']) && !empty($array['fr_date'])) {
                      $fromDate = $array['fr_date'];
              }else{
                  return Array('status' => 'failed', 'message' =>'PLease Enter From Date', 'data' => []);
              }if (isset($array['to_date']) && !empty($array['to_date'])) {
                  $toDate =$array['to_date'];
              }else{
                  return Array('status' => 'failed', 'message' =>'PLease Enter To Date', 'data' => []);
              }
              // if (isset($array['user_id']) && !empty($array['user_id']) && is_numeric($array['user_id'])) {
              //     $userId = $array['user_id'];
              // }else{
              //   return Array('status' => 'failed', 'message' =>'PLease Enter User Id', 'data' => []); 
              // }
              $flag = isset($array['flag'])? $array['flag']:1;
              $data = $this->order->getSalesOrderData($phone_no,$fromDate,$toDate,$flag);
              $totals = last($data);             
              array_pop($data);
              $data = array("data"=>$data,"totals"=>$totals);
              return Array('status' => 'success', 'message' =>'Data Found', 'data' => $data);

          }else{
                $error = "Please pass required parameters";
              return array('status'=>"failed",'message'=>$error,'data'=>"");die;
          }
          
        } catch (Exception $e) {
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
          
        }    
      }

     /**
     * Get the cart recomendation for the user
     * @param  int $cust_le_id Customer legal entity ID
     * @param  int $repeat     Past buying count
     * @param  int $limit      Number of prodcut recomendation
     * @return JSON $result    Cart recomendation with status
     */
    function recomendedCart(){
        try{
            $token = '';
            $array = json_decode($_POST['data'],true);
            if( isset($_POST['data']) && isset($array['customer_token']) && $array['customer_token'] != '' ) {                
                $token = $this->order->validateToken($array['customer_token']);
                if( isset($token['token_status']) && $token['token_status'] == 1 ){
                    $cust_id = $array['cust_le_id'];
                    $le_wh_id = $array['le_wh_id'];
                    $limit   = 10;
                    $repeat  = $array['repeat'];
                    $customertype  = $array['customertype'];
                    $isFF  = $array['isFF'];
                    if($cust_id && $limit && $repeat){
                        $data = $this->order->generateRecomendedProducts($cust_id, $le_wh_id, $customertype, $limit, $repeat, $isFF);
                        return array('status' => "success", 'message' =>  "Recomendation product", 'data' =>  $data);
                    }                    
                }else{
                    return array('status' => "failed", 'message' =>  "Customer token is required", 'data' =>  []);
                }                
            }else{
                return array('status' => "failed", 'message' =>  "Post data is required", 'data' =>  []);
            }
        }catch (Exception $e){               
            return array('status' => "failed", 'message' =>  "Response not available. Please contact support team.", 'data' =>  []);
        }
    }
}