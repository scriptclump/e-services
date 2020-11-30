<?php

namespace App\Modules\DmapiV2\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\DmapiV2\Models\GDSOrders;
use App\Modules\DmapiV2\Models\GDSAddress;
use App\Modules\DmapiV2\Models\GDSPayment;
use App\Modules\DmapiV2\Models\GDSCashback;

use DB;
use Log;
use Config;
use Cache;
use Exception;
use Session;

/* * ***Use for only queing and the session mangement****** */
use App\Lib\Queue;
use App\Modules\Orders\Models\Invoice;

class Dmapiv2Model extends Model {

    protected $primaryKey = 'gds_order_id';
    public $timestamps = false;
    protected $table = 'gds_orders';

    protected $le_wh_id = 0;
    protected $hub_id = 0;
    protected $legal_entity_id = 0;
    public $order_object;
    protected $order_ref = '';
    public $returnArray = array();
      
    // public function setLegal_entity_id($legal_entity_id){

    //     $this->legal_entity_id = $legal_entity_id;
    // }

    // public function getLegal_entity_id(){

    //   return $this->legal_entity_id;
    // }

    public function setOrderObject(GDSOrders $object) {
        $this->order_object =  json_encode($object);
    }

    public function getOrderObject(){
        return $this->order_object;
    }    

    public function checkUserAccess($apikey, $secretkey, $methodName){
        
        $result = Cache::get($apikey.'_'.$secretkey.'_'.$methodName,false);
        
        if(!$result){

            $checkuser = DB::Table('api_session')
            ->select('api_features.feature_name','api_session.legal_entity_id')
            ->join('api_role_mfg', 'api_role_mfg.api_role_mfgid', '=', 'api_session.role_id')
            ->join('api_role_mfgassign', 'api_role_mfgassign.api_role_mfgasid', '=', 'api_role_mfg.api_role_mfgid')
            ->join('api_features', 'api_features.api_fid', '=', 'api_role_mfgassign.api_fid')
            ->where('api_session.api_key', $apikey)
            ->where('api_session.secret_key', $secretkey)
            ->where('api_features.feature_name', $methodName)
            ->get()->all();

            $resp = json_decode(json_encode($checkuser), true);

            if(!empty($resp)){                
                $result = $resp[0];
            }
            else{                
                $result = false;
            }
            Cache::put($apikey.'_'.$secretkey.'_'.$methodName, $result,3600);
        }
        return $result;
    }

    public function beginTrasactional($data){
        
        $result = DB::transaction(function () use ($data) {
           try{



           } catch (\Exception $e) {
                $dataArray = json_decode($data['orderdata'],true);
                $data['orderdata'] = $dataArray;
                $data['exception_message'] = $e->getMessage();
                $data['exception_stacktrace'] = $e->getTraceAsString();
                $data = json_encode($data,JSON_PRETTY_PRINT);
                $this->orderFailureMail($data);
            }
         });
    }


    /**
     * [gdsOrderDetails description]
     * @param  [type] $data [description]
     * @return [object $GDSOrders]       [description]
     * Author: Code Optimization [Vicky,Legend,Jision] optimize Horaha hai <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v2.0
    */
    public function gdsOrderDetails($data){

        DB::transaction(function () use ($data){

            try{

                $gds_orders     = new GDSOrders;
                $gds_products   = new GDSProducts;
                $gds_Address    = new GDSAddress;
                $gds_Payment    = new GDSPayment;
                $gds_cashback   = new GDSCashback;

                if($this->le_wh_id == 0 ){

                throw new Exception("le_wh_id sent is 0");

                }
                //set GdsOrderModelLewhId
                $gds_orders->setLeWhId($this->le_wh_id);
                //set GdsOrderModelHUbId
                $gds_orders->setHubId($this->hub_id);

                $gds_orders->setOrderReference($this->order_ref);

                /**
                 * [$order_id description]
                 * @var [type]
                 *  catch an exception if found
                 */
                $order_id = $gds_orders->insertgdsOrder($data);
                if ($order_id instanceof Exception) {
                    echo "Order Could not be generated...";
                    throw new Exception($order_id);
                }

                //testing purpose delete on production
                //throw new Exception($order_id);

                if(!$order_id || is_null($order_id)){
                    //echo $order_id->getMessage();
                    throw new Exception("Order Id Could'nt be generated .. Rollback issued");
                }else{
                    $gds_orders->setGdsOrderId($order_id);
                    $order_status_id = $this->getmpStatusMapping($gds_orders->getOrderMpstatus());
                    $gds_orders->setOrderStatusId($order_status_id);
                    $order_status_id = $gds_orders->getOrderStatusId();
                    /////////////////////////All Data Seting Work For Gds Products///////////////////////////////////
                    //setting gds_order_id 
                    $gds_products->setGdsOrderId($order_id);
                    $gds_products->setOrderStatusIdforProductFeild($order_status_id);
                    $gds_products->setBuyerStateId($gds_orders->getOrderStateId());
                    //No need to drag again from Orders Model send only the Le warehouseid
                    $gds_products->setLeWareHouseDetails($this->le_wh_id);
                    ///////////////////////////////////////////////////////////////////////////
                    
                    $orderdatacheck = json_decode($data['orderdata']);
                    $orderdatacheck = $orderdatacheck->order_info;
                    $discount_before_tax = isset($orderdatacheck->discount_on_tax_less)?$orderdatacheck->discount_on_tax_less:0;
                    $order_total = 0;
                    $gds_productsInsert = $gds_products->insertGdsOrderProductBulk($data);
                    if($gds_productsInsert instanceof Exception){

                        throw new Exception($gds_productsInsert);

                    }else{

                        if(is_array($gds_productsInsert)){

                            $gds_orders->setOrderProducts($gds_productsInsert);
                            $adress_insert = $gds_Address->insertCustomerAddress($data,$gds_orders);
                            $payment_insert = $gds_Payment->gdsOrderPayment($data,$gds_orders);
                            if($adress_insert instanceof Exception){

                                throw new Exception($adress_insert);
                            }

                            $order_total = array_sum(array_column($gds_productsInsert, "total"));

                        }else{

                            throw new Exception("Returned products Inserted is not an array ");

                        }
                    }

                    //do the last job update address and Tax and leave
                    $taxTotal = $gds_products->getTotalTax();
                    if($taxTotal instanceof Exception){

                        throw new Exception($taxTotal);

                    }else{
                        // $orderReference = $_DmapiorderModel->orderReference($state_id, 'SO');
                            // //update gdstable tax
                        $update_data['tax_total']  = $taxTotal;
                        if($discount_before_tax==1){
                            $update_data['total']  = DB::raw('total+'.$taxTotal);
                        }
                        $update_data['order_status_id'] = $gds_orders->getOrderStatusId();
                        $update_data['total_items'] = $gds_products->getTotalLineItems();
                        $update_data['total_item_qty'] = $gds_products->getTotalLineItemsCount();
                        //$update_data['le_wh_id'] = $gds_orders->getLeWhId();
                        $gds_orders->updateFeildsOrder($update_data,$order_id);
                    }

                    $gds_cashback->setOrderId($order_id);
                    $cashBack = $gds_cashback->storeCashback($data);

                    if($cashBack instanceof Exception){

                        throw new Exception($cashBack);
                    }

                    
                    $returnArray['order_code'] = $gds_orders->getOrderReference();
                    $returnArray['order_id'] = $gds_orders->getGdsOrderId();
                    $returnArray['order_total'] = $order_total;
                    $this->returnArray = $returnArray;
                    
                    return true;
                }
                return false;                
            }catch (Exception $e) {


                    if($data['retry_count'] <= env('GDS_ORDERS_RETRY')){

                        var_dump('will be retrying');
                        var_dump($data);
                        $token = $data['token'];
                        //$data['orderdata'] = json_decode($data['orderdata'],true);
                        $data = json_encode($data);
                        $data = base64_encode($data);
                        $args = array("ConsoleClass" => 'DmapiVer2', 'arguments' => array('placeOrder', $data, $token));
                        $queue = new Queue();
                        $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);
                        
                    }else{

                        var_dump('will be sending mail');
                        $dataArray = json_decode($data['orderdata'],true);
                        $data['no_of_attempts'] = 'Order Failed After -'.$data['retry_count'];
                        $data['orderdata'] = $dataArray;
                        $data['exception_message'] = $e->getMessage();
                        $data['exception_stacktrace'] = $e->getTraceAsString();
                        $data = json_encode($data,JSON_PRETTY_PRINT);
                        $this->orderFailureMail($data);
                    }

                    throw new Exception($e);
                    
            }
        });
        

    }

    public function setLeWhId($le_wh_id){

        $this->le_wh_id = $le_wh_id;
    }

    public function setHubId($hub_id){
        var_dump($hub_id);
        $this->hub_id = $hub_id;
    }



    public function setOrderReference($reference){

        $this->order_ref = $reference;
    }

    public function getOrderRefernce(){

        return $this->order_ref;
    }

    /**
     * @param [$msg] json data
     * Not returning any thing killing it with die 
     * this will stop the script and make the controller respond fast
     */
    private function orderFailureMail($msg) {


        $data = $msg;
        $mail = 0;
        $mail = \Mail::raw($data, function ($message) {
            $message->from('tracker@ebutor.com', 'Ebutor');
            $message->subject('Order failed check the enclosed json');
            $message->to('techandsupport@ebutor.com');
        });

        // Failed order interface starts here

        $orderdata = json_decode($msg,1);
        $orderjsondata = $orderdata['orderdata'];
        
        $legal_entity_id=$orderjsondata['customer_info']['cust_le_id']; 

        $orderdate=$orderjsondata['order_info']['orderdate'];
        $orderformatdate=date("Y-m-d", strtotime($orderdate));

        $extraCalulatedData=$orderdata['extraCalulatedData'];
        $orderjsoncode=$extraCalulatedData['order_reference'];

        $cus_mobile_no=$orderjsondata['customer_info']['mobile_no'];

        $fail_data_array = array("legal_entity_id" =>$legal_entity_id,
                            "order_data" => json_encode($orderjsondata),
                            "order_code" =>$orderjsoncode,
                            "order_date" =>$orderformatdate,
                            "cus_mobile_no" =>$cus_mobile_no);

        $fail_data = json_encode($fail_data_array);
        $fail_data = base64_encode($fail_data);
        $args = array("ConsoleClass" => 'FailOrderV1', 'arguments' => array('failOrder', $fail_data));
        $queue = new Queue();
        $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);

        if ($mail) {
            die('Failed mail sent successfully');
        } else {
           die('Failed mail not sent');
        }
    }

    /**
     * [getmpStatusMapping description]
     * @param  [type] $orderstatus [description]
     * @return [type]              [description]
     */
    public function getmpStatusMapping($orderstatus){

        /**
         * [$order_status_id description]
         * @var [type] check this 
         */
        $order_status_id = DB::table('mp_status_mapping')
                            ->where('mp_status', $orderstatus)
                            ->pluck('ebutor_status_id');

        if (count($order_status_id) > 0)
            $order_status_id = $order_status_id[0];
        else
            $order_status_id = 0;
        return $order_status_id;
    }

     /*
    * Function name: findUser
    * Description: used to  find user using token
    * Author: Code Optimization [Vicky,Legend,Jision] optimize Horaha hai <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v2.0
    * Created Date: 11th November 2016 :/
    * Modified Date & Reason:
    * 
    */ 
    
    public function getbeat($cust_le_id){

       // echo "cust le " . $cust_le_id;
        if($cust_le_id == 0){
            return 0;
        }else{
            $result = Cache::get('customer_le_id_'.$cust_le_id);
            if($result){
                return $result;
            }else{

                $result =   DB::table('customers')
                        ->where('le_id',$cust_le_id)
                        ->get()->all();
                if(count($result) > 0){
                    $data = json_decode(json_encode($result[0]),true);
                    $beatId=$data['beat_id'];
                    Cache::put('custormer_le_id_'.$cust_le_id,$beatId,3600);
                    return $beatId;
                }else{
                    return 0;
                }
            }
            

        }
    }
    /*
    * Function name: getFFByBeat
    * Description: used to get FF ID of a beat
    * Author: Raju.A
    * Copyright: ebutor 2017
    * Version: v2.0
    * Created Date: 12th December 2017 :/
    * Modified Date & Reason:
    *
    */
    public function getFFByBeat($beat_id){
        if($beat_id == 0){
            return 0;
        }else{
            $result = Cache::get('ff_id_bybeat_'.date('Y-m-d').$beat_id);
            if($result){
                return $result;
            }else{
                $result = DB::table('pjp_pincode_area')->select(['rm_id'])
                ->where('rm_id','>',0)
                ->where('pjp_pincode_area_id',$beat_id)->first();
                if(count($result) > 0){
                    $data = json_decode(json_encode($result),true);
                    $ffId=$data['rm_id'];
                    Cache::put('ff_id_bybeat_'.date('Y-m-d').$beat_id,$ffId,3600);
                    return $ffId;
                }else{
                    return 0;
                }
            }
        }
    }

    /*
    * Function name: findUser
    * Description: used to  find user using token
    * Author: Prasenjit <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 20th August 2016 Its Satrday i m stuck in shit code :/
    * Modified Date & Reason:
    * copied again on 9 th nov
    * Dont know why i m doing this just copied from cpmanger team :/
    * Remember to change the calls 
    */ 
    public function findUserIdByPassword($customer_token){

        $result = DB::table('users')
                        ->select(DB::raw('user_id'))
                         ->where('password_token', '=', $customer_token)
                         ->useWritePdo()
                        ->get()->all();       
        if(count($result) > 0){
            $data = json_decode(json_encode($result[0]),true);
            $customerId=$data['user_id'];
            return $customerId;
        }else{
            return 0;
        }
        
    }

    /**
     * [getStateIdFromStateName description]
     * created by @prasenjit
     * created date : 13 th November 2016
     * Will be used to get zone code for a particular state 
     * @param  [type] $state_name [description]
     * @return [type]             [description]
     */
    public function getStateIdFromStateName($state_name){

        $state_id = Cache::get($state_name.'_zoneid',false);
        if(!$state_id){
            $state_id = DB::table('zone')->where('name',$state_name)
                        ->where('country_id', 99) //made only for india shift later
                        ->pluck('zone_id');
            if (empty($state_id)) {
                    $state_id = 0;
                } else {
                    $state_id = $state_id[0];
                    Cache::put($state_name.'_zoneid',$state_id,3600);
                    return $state_id;
                }
        }else {

            return $state_id;
        }
        

    }


    /**
     * [getCountryIdFromCountryName description]
     * @param  [type] $country_name [description]
     * @return [type]               [description]
     * Author: Code Optimization [Vicky,Legend,Jision] optimize Horaha hai <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v2.0
     * Created Date: 11th November 2016 :/
     */
    public function getCountryIdFromCountryName($country_name){

        $country_id = Cache::get($country_name.'_id',false);
        if(!$country_id){

            $country_id = DB::table('countries')->where('name', $country_name)->pluck('country_id');
            if (count($country_id) > 0){

                $country_id = $country_id[0];
                Cache::put($country_name.'_id',$country_id,3600);
            } else {
                $country_id = 0;  
            }
            return $country_id;
        }else{
            
            return $country_id;
        }
    }

    /**
     * [test description]
     * @return [type] [description]
     * Author: Code Optimization [Vicky,Legend,Jision] optimize Horaha hai <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v2.0
     * Created Date: 11th November 2016 :/
     */
    public function getCurrencyIdFromCurrency($currency){

        $currency_id = Cache::get($currency.'_id',false);

        if(!$currency_id){
            $payment_currency_id = DB::table('currency')->where('code', $currency)->pluck('currency_id');
            if (count($payment_currency_id) > 0){
                $payment_currency_id = $payment_currency_id[0];
                Cache::put($currency.'_id',$payment_currency_id,3600);
            }
            else{
                $payment_currency_id = 0;
            }

            return $payment_currency_id;
        }else{

            return $currency_id;
        }
        
    }

    /**
     * [getPaymentIdFromMethod description]
     * @param  [type] $paymentmethod [description]
     * @return [type]                [description]
     * Author: Code Optimization [Vicky,Legend,Jision] optimize Horaha hai <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v2.0
     * Created Date: 11th November 2016 :/
     */
    public function getPaymentIdFromPaymentMethod($paymentmethod){

        $payment_method_id = Cache::get($paymentmethod.'_id',false);
        if(!$payment_method_id){

            $payment_method_id = DB::table('mp_status_mapping')->where('mp_status', $paymentmethod)->pluck('ebutor_status_id');
            if (count($payment_method_id) > 0){
                $payment_method_id = $payment_method_id[0];
                Cache::put($paymentmethod.'_id',$payment_method_id,3600);
            }else{
                $payment_method_id = 0;
            }
            return $payment_method_id;
        }else{
            return $payment_method_id;
        }
        
    }
    
    /**
     * [getPaymentStatusIdFromPaymentStatus description]
     * @param  [type] $paymentstatus [description]
     * @return [type]                [description]
     * Author: Code Optimization [Vicky,Legend,Jision] optimize Horaha hai <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v2.0
     * Created Date: 11th November 2016 :/
     */
    public function getPaymentStatusIdFromPaymentStatus($paymentstatus){
        
        $payment_status_id = Cache::get($paymentstatus.'_id',false);
        if(!$payment_status_id){

            $payment_status_id = DB::table('mp_status_mapping')->where('mp_status',$paymentstatus)->pluck('ebutor_status_id');
            if (count($payment_status_id) > 0){
                $payment_status_id = $payment_status_id[0];
                Cache::put($paymentstatus.'_id',$payment_status_id,3600);
            }else{
                $payment_status_id = 0;
            }
            return $payment_status_id;
        }else{
            return $payment_status_id;
        }
        
    }

    /*
    * Function name: Update
    * Description: used to  cancel cart items 
    * Author: Prasenjit <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 20th August 2016
    * Modified Date & Reason:
    * Dont know why i m doing this just copied from cpmanger team :/
    */ 

    public function updateCart($customer_token,$status) {

      $result = $this->findUserIdByPassword($customer_token);

                                          
        if(count($result) > 0 ){

            $customerId=$result;
            $cart= DB::table('cart')->where('user_id',$customerId)->update(['status' => $status]);
            $product_cart = DB::table('cart_product_packs')->where('user_id',$customerId)->update(['status' => $status]);

        }      

    }

    /*
    * Function name: cartcancel
    * Description: used to  cancel cart items 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14th July 2016
    * Modified Date & Reason:
    */ 


    public function cartcancel($customer_token) {

      $result = $this->findUserIdByPassword($customer_token);

        if(count($result) > 0 ){
            $customerId=$result;
            $cart= DB::table('cart')->where('user_id',$customerId)->delete();
            $product_cart = DB::table('cart_product_packs')->where('user_id',$customerId)->delete();
               
        }

    }

    public function getUserMobile($customer_token){ 

        $query = DB::table('users')
        ->select('mobile_no')
        ->where('password_token', '=', $customer_token)
        ->get()->all();
        if(count($query) > 0){
            @$telephone=$query[0]->mobile_no;    
        }
        else{
            $telephone = 0;
        }
        return $telephone;   

    }


    /**
     * [placeOrderBreakDown description]
     * @param  [type] $data [json]
     * @return [array] [ data with similar type of array ]
     * Author: Prasenjit <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v2.0
     * Created Date: 14th November 2016
     * Modified Date & Reason:
     */
    public function placeOrderBreakDown($data) {

        $functional_Data = $data;

        $functional_orderSubParts = json_decode($functional_Data['orderdata'], true);
        $functional_orderProducts = $functional_orderSubParts['product_info'];
        //clearing the product array from the data
        unset($functional_orderSubParts['product_info']);

        //segregrating orders on basis of warehouse 
        $warehouse_lists = array();
        foreach ($functional_orderProducts as $product) {
            $warehouse_lists[$product['le_wh_id']][] = $product;
        }

        if (count($warehouse_lists) > 0) {
            $split_orders = array();
            $count = 0; //for array merory allocation we will avoid on fly memory alloacation
            foreach ($warehouse_lists as $value) {
                $split_orders[$count]['api_key'] = $functional_Data['api_key'];
                $split_orders[$count]['secret_key'] = $functional_Data['secret_key'];
                $split_orders[$count]['legal_entity_id'] = $functional_Data['legal_entity_id'];
                $split_orders[$count]['orderdata'] = $functional_orderSubParts;
                $split_orders[$count]['orderdata']['product_info'] = $value;
                $json_encrpt = json_encode($split_orders[$count]['orderdata']);
                unset($split_orders[$count]['orderdata']);
                $split_orders[$count]['orderdata'] = $json_encrpt;
                $count++;
            }

            return $split_orders;
        } else {
            return false;
        }
    }

    public function sendSMS($number,$message){

        $postfields['user']=Config::get('dmapi.SMS_USER');
        $postfields['senderID']=Config::get('dmapi.SMS_SENDER_ID');
        $postfields['msgtxt']= $message;
        $postfields['receipientno'] = $number;
        $postfields = http_build_query($postfields);
        $this->curlRequest(Config::get('dmapi.SMS_URL'),$postfields);

    }


    private function curlRequest($url,$postfields){

        $ch = curl_init();        
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postfields);
        $buffer = curl_exec($ch);
        if(empty ($buffer)){ 
            return false;
        }else{ 
            return true;
        } 
    }

    public function getSingleRecord($id)
    {
        $query="SELECT 
                  `legal_entity_id`,
                  `order_data`,
                  `order_date`,
                  `order_code`,
                  GetUserName (updated_by, 2) AS updated_by,
                  is_processed AS processed
                FROM
                  failed_order
                WHERE failed_order_id = ?"; 
        $result = DB::SELECT($query,[$id]);
        if(!empty($result))
            return $result;
        return NULL;
    }

    public function getfailedorderList($makeFinalSql,$orderBy,$page,$pageSize)
    {
        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }else{
            $orderBy = ' ORDER BY failed_order_id desc';
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= ' WHERE ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }
        $query="SELECT 
                  `failed_order_id`,
                  `legal_entity_id`,
                  `order_data`,
                  `order_date`,
                  `order_code`,
                  GetUserName (updated_by, 2) AS updated_by,
                  getMastLookupValue(is_processed) AS processed
                FROM
                  failed_order 
                ". $sqlWhrCls . $orderBy ;
        $allData = DB::select(DB::raw($query));
        $TotalRecordsCount = count($allData);
        if($page!='' && $pageSize!=''){
            $page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
            $allData = array_slice($allData, $page, $pageSize);
        }
        $arr = array('results'=>$allData,
        'TotalRecordsCount'=>(int)($TotalRecordsCount)); 
        return $arr;      
    }
    public function updateorder($edit_failed_order_id,$orderjsondata)
    {
        $userId = Session::get('userId');
        $result=DB::table('failed_order')
            ->where('failed_order_id',$edit_failed_order_id)
            ->update(['is_processed' =>185004,
                      'order_data' =>$orderjsondata,
                      'updated_by'=>$userId]);
        return $result;
    }

    public function insertFailedOrder($data) {  
        $userId = Session::get('userId');
        $failedorderexists=DB::table('failed_order')
                ->select('failed_order_id')
                ->where('order_code',$data['order_code'])
                ->where('cus_mobile_no',$data['cus_mobile_no'])
                ->get()->all();
        if(count($failedorderexists)==0)
        {
            DB::table('failed_order')
               ->insert(["legal_entity_id" =>$data['legal_entity_id'],
                        "order_data" =>$data['order_data'],
                        "order_code" =>$data['order_code'],
                        "order_date" =>$data['order_date'],
                        "cus_mobile_no" =>$data['cus_mobile_no']
                ]);

        }else{
                DB::table('failed_order')
                    ->where('failed_order_id',$failedorderexists[0]->failed_order_id)
                    ->update(['order_data'=>$data['order_data'],
                            'is_processed' =>185003,
                            'updated_by'=>$userId
                        ]);
        }
    } 

    public function updateOrderStatus($data){
        $userId = Session::get('userId');
        $query= DB::table('failed_order')
                    ->where('failed_order_id',$data['failed_order_id'])
                    ->update(['is_processed' =>$data['order_status'],
                            'updated_by'=>$userId
                        ]);
        if(empty($query))
           return false;
        return true;
    }

    public function getstatusInfo($ids = null){
        if($ids == null)
        return DB::table('master_lookup')
                ->select('description','value')
                ->where('mas_cat_id','=','185')
                ->where('is_display','=',1)
                ->get()->all();
    }

    /**
     * [getLegalEntityTypeId, return legal_entity_type_id] 
     * @param  [$le_id] int
     * @return [int]
     */
    public function getLegalEntityTypeId($le_id){
        $legal_entity_type_id = DB::table("legal_entities")->select('legal_entity_type_id')->where('legal_entity_id',$le_id)->first();
        return isset($legal_entity_type_id->legal_entity_type_id)?$legal_entity_type_id->legal_entity_type_id:0;
    }

    public function getProductInfo($product_sku){
        $product_id=DB::table('products')
                     ->select('product_id')
                     ->where('sku',$product_sku)
                     ->get()->all();
        $result=isset($product_id[0]->product_id)?$product_id[0]->product_id:'';
        return $result;
    }

    public function getProductInventory($le_wh_id,$product_id){
        $get_pro_inv=DB::table('inventory')
                     ->select(DB::raw('(soh-order_qty) as availQty'))
                     ->where('product_id',$product_id)
                     ->where('le_wh_id',$le_wh_id)
                     ->get()->all();
        $avail_qty=isset($get_pro_inv[0]->availQty)?$get_pro_inv[0]->availQty:'';
        return $avail_qty;
    }

    public function checkOrderStatus($mobile_no,$order_id){
        $result=DB::table('failed_order')
                ->select('failed_order_id')
                ->where('order_code',$order_id)
                ->where('cus_mobile_no',$mobile_no)
                ->where('is_processed',185004)
                ->get()->all();
        return $result;

    }

}