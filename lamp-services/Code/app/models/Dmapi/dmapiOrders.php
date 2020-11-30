<?php

namespace App\models\Dmapi;

use Illuminate\Database\Eloquent\Model;
use App\Central\Repositories\CustomerRepo;
use DB;
use Log;
use Config;
use Cache;

class dmapiOrders extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    //use UserTrait, RemindableTrait;
    protected $primaryKey = 'gds_order_id';
    public $timestamps = false;
    protected $table = 'gds_orders';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    /* protected $fillable = array('mp_id','mp_order_id','legal_entity_id','order_status_id',
      'order_date','ship_total','sub_total', 'tax_total', 'total', 'gds_cust_id',
      'firstname','lastname','email','phone_no'); */

    /*
     * getOrderDetailById() method is used to get order information by order id
     * @param $orderId Integer
     * @return Array
     */

    public function getOrderDetailById($orderId) {
        $fieldArr = array(
            'orders.legal_entity_id',
            'orders.gds_order_id',
            'orders.mp_order_id',
            'orders.firstname',
            'orders.lastname',
            'orders.email',
            'orders.phone_no',
            'orders.order_date',
            'orders.total as order_value',
            'orders.order_status_id',
            'orders.ship_total',
            'orders.tax_total',
            'orders.sub_total',
            'orders.discount as discount_total',
            'orders.total as grand_total',
            'orders.discount_type',
            'mp.mp_name',
            'mp.mp_logo',
            'mp.mp_url',
            'payment.payment_method_id',
            'payment.payment_status_id',
            'currency.code',
            'currency.symbol_left as symbol',
        );

        $query = DB::table('gds_orders as orders')->select($fieldArr);
        $query->where('orders.gds_order_id', $orderId);
        $query->join('mp', 'orders.mp_id', '=', 'mp.mp_id');
        $query->join('gds_orders_payment as payment', 'payment.gds_order_id', '=', 'orders.gds_order_id');
        $query->join('currency', 'payment.currency_id', '=', 'currency.currency_id');
        //echo $query->toSql();die;		
        $orders = $query->first();
        return $orders;
    }

    /**
     * [getOrderDetailByChannelOrderId description]
     * @param  [mixed] $channelOrderId [description]
     * @param  [int] 	$mp_id [channel/marketplaceId]
     * @return [array]          [description]
     */
    public function getOrderDetailByChannelOrderId($channelOrderId, $mp_id) {
        $fieldArr = array(
            'orders.legal_entity_id',
            'orders.gds_order_id',
            'orders.mp_order_id',
            'orders.firstname',
            'orders.lastname',
            'orders.email',
            'orders.phone_no',
            'orders.order_date',
            'orders.total as order_value',
            'orders.order_status_id',
            'orders.ship_total',
            'orders.tax_total',
            'orders.sub_total',
            'orders.discount as discount_total',
            'orders.total as grand_total',
            'orders.discount_type',
            'mp.mp_name',
            'mp.mp_logo',
            'mp.mp_url',
            'payment.payment_method_id',
            'payment.payment_status_id',
            'currency.code',
            'currency.symbol_left as symbol',
        );

        $query = DB::table('gds_orders as orders')->select($fieldArr);
        $query->where('orders.mp_order_id', $channelOrderId);
        $query->where('orders.mp_id', $mp_id);
        $query->join('mp', 'orders.mp_id', '=', 'mp.mp_id');
        $query->join('gds_orders_payment as payment', 'payment.gds_order_id', '=', 'orders.gds_order_id');
        $query->join('currency', 'payment.currency_id', '=', 'currency.currency_id');
        //echo $query->toSql();die;		
        $orders = $query->first();
        return $orders;
    }

    /**
     * getBillingAndShippingAddressByOrderId() method is used to get 
     * billing and shipping address by order id
     * @param Null
     * @return Array
     */

    public function getBillingAndShippingAddressByOrderId($orderId) {

        try {
            $fieldArr = array(
                'address.fname',
                'address.mname',
                'address.lname',
                'address.company',
                'address.address_type',
                'address.addr1',
                'address.addr2',
                'address.city',
                'address.postcode',
                'address.suffix',
                'address.telephone',
                'address.mobile',
                'countries.name as country_name',
                'zone.name as state_name'
            );

            $query = DB::table('gds_orders_addresses as address')->select($fieldArr);
            $query->where('address.gds_order_id', $orderId);
            $query->leftJoin('countries', 'countries.country_id', '=', 'address.country_id');
            $query->leftJoin('zone', 'zone.zone_id', '=', 'address.state_id');
            $address = $query->get()->all();
            return $address;
        } catch (Exception $e) {
            
        }
    }

    /**
     * [gdsOrderStatusTrack collects order status by product from data base view 'vw_gds_order_track']
     * @param  [int] $productId [holds product id]
     * @param  [int] $orderId   [holds order id]
     * @return [array]            [returns order status array]
     */
    public function gdsOrderStatusTrack($productId, $orderId) {
        $query = DB::table('vw_gds_order_track')->select('*');
        $query->where('product_id', $productId);
        $query->where('gds_order_id', $orderId);
        $status = $query->get()->all();
        $status = json_decode(json_encode($status[0]), true);
        return $status;
    }

    public function checkOrderStatus($Orderstatusid) {
        
        $status = Cache::get('master_lookup_'.$Orderstatusid);
        
        if(is_null($status)){

            $query = DB::table('master_lookup');
            $query->where('value', $Orderstatusid);
            $status = $query->pluck('master_lookup_name')->all();
            Cache::put('master_lookup_'.$Orderstatusid,$status,3600);
            
        }
        return $status; 
        
    }

    /**
     * [getShipmentIdByOrderId gets shipment id details by order id and product id]
     * @info Can be call statically / joined table is gds_ship_products
     * @param  [int] $orderId   [holds order id]
     * @param  [int] $productId [holds product id]
     * @return [int]            [returns shipmentId]
     */
    public static function getShipmentIdByOrderIdProductId($orderId, $productId){
        $query = DB::table('gds_ship_grid as gsg')->select('gsg.gds_ship_grid_id');
        $query->leftJoin('gds_ship_products as gsp' , 'gsp.gds_ship_grid_id', '=' , 'gsg.gds_ship_grid_id');
        $query->where('gsg.gds_order_id' , $orderId);
        $query->where('gsp.product_id' , $productId);
        $shipmentId = $query->get()->all();
        $shipmentId = json_decode(json_encode($shipmentId[0]), true);
        return $shipmentId;
    }

    public static function getShipmentIdByOrderId($orderId){
        $query = DB::table('gds_ship_grid')->select('gds_ship_grid_id');
        $query->where('gds_order_id' , $orderId);
        $shipment = $query->get()->all();
        $shipment = json_decode(json_encode($shipment), true);
        return $shipment;
    }

    public function getShipmentTrackDetails($ship_grid_id,$orderId){
        $query = DB::table('gds_ship_track_details as gstd')->select('gstd.ship_service_id','gstd.ship_method',
                'gstd.tracking_id',
                'gstd.vehicle_number','gstd.rep_name as representative_name','gstd.contact_number','gst.qty',
                'gst.product_id');
        $query->leftJoin('gds_ship_track as gst' , 'gst.gds_ship_grid_id', '=' , 'gstd.gds_ship_grid_id');
        $query->where('gstd.gds_order_id' , $orderId);
        $query->where('gstd.gds_ship_grid_id' ,$ship_grid_id);
        $shipment = $query->get()->all();
        $shipment = json_decode(json_encode($shipment), true);
        $shipment_arr=array();
        $shipArr = array();
    foreach ($shipment as $key => $value) {
        $quer = DB::table('carriers as cr');
        $quer->leftjoin('shipping_services as ss','ss.carrier_id','=','cr.carrier_id');
        $quer->where('ss.service_id',$value['ship_service_id']);
        $carrier = $quer->pluck('cr.name');
        $carrier= $carrier[0];
        $shipment_arr[]= array('carrier_name'=>$carrier,'ship_method'=>$value['ship_method'],'tracking_id'=>$value['tracking_id'],
                   'vehicle_number'=>$value['vehicle_number'],'representative_name'=>$value['representative_name'],
                    'contact_number'=>$value['contact_number'],'qty'=>$value['qty'],'product_id'=>$value['product_id']);
        }
                
        //$shipment()
        array_push($shipArr,$shipment_arr);
        
        return $shipArr;
        
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

    /*
    * Function name: cartcancel
    * Description: used to  cancel cart items 
    * Author: Prasenjit <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14th July 2016
    * Modified Date & Reason:
    * Dont know why i m doing this just copied from cpmanger team :/
    */ 


    public function cartcancel($customer_token) {

      $result = DB::table('users')
                        ->select(DB::raw('user_id'))
                         ->where('password_token', '=', $customer_token)
                        ->get()->all();       

                $data = json_decode(json_encode($result[0]),true);
                $customerId=$data['user_id'];                          

       $cart= DB::table('cart')->where('user_id',$customerId)->delete();

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

      $result = DB::table('users')
                        ->select(DB::raw('user_id'))
                         ->where('password_token', '=', $customer_token)
                        ->get()->all();       

                $data = json_decode(json_encode($result[0]),true);
                $customerId=$data['user_id'];                          

       $cart= DB::table('cart')->where('user_id',$customerId)->update(['status' => $status]);

    }

    /*
    * Function name: findUser
    * Description: used to  find user using token
    * Author: Prasenjit <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 20th August 2016 Its Satrday i m stuck in shit code :/
    * Modified Date & Reason:
    * Dont know why i m doing this just copied from cpmanger team :/
    * Remember to change the calls 
    */ 


    public function findUserIdByPassword($customer_token){

        $result = DB::table('users')
                        ->select(DB::raw('user_id'))
                         ->where('password_token', '=', $customer_token)
                         ->orWhere('lp_token', '=', $customer_token)
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
     * @modified by prasenjit on 7th november 2016
     * [orderReference description]
     * @param  [type] $stateId [state code ex TS - telegana]
     * @param  [type] $type      [type for reference - ]
     * @return [type]            [description]
     */
    public function orderReference($stateId,$type){
        try{
            $custrepo = new CustomerRepo();
            $orderCode = $custrepo->getRefCode($type,$stateId);
            var_dump($orderCode);
            return $orderCode;

        }catch(\Exception $e){
            $orderCode = '';
        }
        
        return $orderCode;
    }

    /**
     * [updateTaxToZero updates tax in gds_orders to zero to restucture tax]
     * @param  [type] $orderID [description]
     * @return [boolean]          [description]
     */
    public function updateTaxToZero($orderID){

        try{
            $update =   DB::table('gds_orders')
                        ->where('gds_order_id',$orderID)
                        ->update(['tax_total' => 0]);
            return true;
        }catch(\Exception $e){
            return false;
        }

    }

    /**
     * [deleteGdsTaxFeilds description]
     * @return [type] [description]
     */
    public function deleteGdsTaxFeilds($orderID){

        try{
            DB::delete("
                        DELETE FROM `gds_orders_tax` WHERE gds_order_prod_id IN
                        (SELECT gds_order_prod_id FROM gds_order_products WHERE  gds_order_id = $orderID)"
                    );
            return true;
        }catch(\Exception $e){

            return false;
        }
    }

    /**
     *  [get beat id using customer_le_id]
     */
    
    public function getbeat($cust_le_id){

        if($cust_le_id == 0){
            return 0;
        }else{

            $result =   DB::table('customers')
                        ->where('le_id',$cust_le_id)
                        ->get()->all();
            if(count($result) > 0){
                $data = json_decode(json_encode($result[0]),true);
                $beatId=$data['beat_id'];
                return $beatId;
            }else{
                return 0;
            }

        }
    }
}
