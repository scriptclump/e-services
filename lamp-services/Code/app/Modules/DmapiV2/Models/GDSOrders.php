<?php

namespace App\Modules\DmapiV2\Models;
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use App\Modules\Orders\Models\OrderModel;
use App\Central\Repositories\CustomerRepo;
use DB;
use Log;
use Illuminate\Database\Eloquent\Model;
use App\models\Warehouse\warehouseModel;
use \Exception;
class GDSOrders extends Model {

    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    //use UserTrait, RemindableTrait;
    protected $primaryKey = 'gds_order_id';
    public $timestamps = false;
    protected $table = 'gds_orders';
    
    
    
    ////////////////////Start Of Protected variables ///////////////////////////
    protected $gds_order_id = '';
    protected $order_code_sent = '';
    protected $order_status_mp;
    protected $order_status_id_calculated;
    protected $order_ref;
    protected $self_order = NULL;
    ////////////////////End Of Protected variables/////////////////////////////

    ////////////////////Start Of Model Connectors /////////////////////////////
    protected $_orderModel;

    ////////////////////End Of Model Connectors ///////////////////////////////


    ////////////////////Publically accessible Params /////////////////////////
    public $orderProducts = array();
    public $order_status_id = ''; // conflicting with the model structure changing it on
    public $temp_le_wh_id = 0;
    public $order_state_id = '';
    public $order_state = '';
    public $temp_hub_id = 0;
    ////////////////////End of Publically accessible Params //////////////////
    

    public function __construct(){

        $this->_orderModel = new OrderModel();

    }

    public function setGdsOrderId($order_id){
        $this->gds_order_id = $order_id;
    }

    public function getGdsOrderId(){
        return $this->gds_order_id;
    }

    public function setOrderReference($orderRefenceCode){
        $this->order_code_sent = $orderRefenceCode;
    }

    public function getOrderReference(){
        return $this->order_code_sent;
    }

    public function setOrderProducts($products){
        $this->orderProducts = $products;
    }

    public function getOrderProducts(){
        return $this->orderProducts;
    }

    public function setLeWhId($le_wh_id){
        $this->temp_le_wh_id = (int)$le_wh_id;
    }

    public function getLeWhId(){
        return $this->temp_le_wh_id;
    }

    public function setHubId($hub_id){
        $this->temp_hub_id = (int)$hub_id;
    }

    public function getHubId(){
        return $this->temp_hub_id;
    }

    public function getOrderStatusId(){
        return $this->order_status_id_calculated;
    }

    public function setOrderStatusId($order_status_id){
        $this->order_status_id_calculated = $order_status_id;
    }

    public function setOrderMpstatus($orderstatus){

        $this->order_status_mp = $orderstatus;
    }

    public function getOrderMpstatus(){

        return $this->order_status_mp;
    }

    public function setOrderStateId($state_id){
        $this->order_state_id = $state_id;
    }

    public function getOrderStateId(){

        return $this->order_state_id;
    }

    public function getOrderRefernce(){

        return $this->order_ref;
    }

    public function getIsSelf(){

        return $this->self_order;
    }

    public function setIsSelf($is_self){

        $this->self_order = $is_self;
    }

    public function insertgdsOrder($data){

        try{

           // throw new Exception("I got stucked in the blackhole");
            $Dmapiv2Model = new Dmapiv2Model();
            $order_data = json_decode($data['orderdata']);

            ////////////////////////////////////////////////////////////////////////////////
            $orderInfo = $order_data->order_info;
            /**
             * [$order_status_id description]
             * @var [type]
             */
            $beat =  $Dmapiv2Model->getbeat($order_data->customer_info->cust_le_id);
            $this->setOrderStateId($data['extraCalulatedData']['state_id']);
            $this->setOrderMpstatus($orderInfo->orderstatus);

            //check self orders
            
            $this->checkSelfOrder($orderInfo);

            var_dump($this->getLeWhId());
            echo "gdsOrder";
            var_dump($this->getHubId());
            ////////////////////////////////////////////////////////////////////////////////

            /**
             *  Ecash
             */

            if(!isset($orderInfo->applied_ecash)){

                $orderInfo->applied_ecash = NULL;
            }else{

                $orderInfo->applied_ecash = (float)$orderInfo->applied_ecash;
            }

            date_default_timezone_set('Asia/Kolkata');
            
            $this->mp_id = $orderInfo->channelid;
            $this->mp_order_id = '';
            $this->legal_entity_id = $data['extraCalulatedData']['legal_entity_id']; //put legal entity id /** check with **/
            $this->order_status_id = 17016; //orderstatusid for INCORRECTORDER
            $this->order_date = date('Y-m-d H:i:s');
            $this->ship_total = $orderInfo->shippingcost;
            $this->sub_total = $data['extraCalulatedData']['sub_total'];
            $this->tax_total = 0;

            // Decided LP team to subtract from discount anyways we are not receving dicount ammount 
            // when the discount is on products level we get data only when the discount is on the
            // order level
            // @prasenjit in case i m not here p[lese discuss with arjun
            
            //$this->total = $data['extraCalulatedData']['total_amount'];
            $this->shop_name = $order_data->additional_info->company;
            //$this->gds_cust_id = $gds_cust_id;
            $this->cust_le_id = $order_data->customer_info->cust_le_id;
            $this->order_expiry_date = date('Y-m-d H:i:s', strtotime($this->order_date . ' +30 day'));
            $this->firstname = $order_data->customer_info->first_name;
            $this->lastname = $order_data->customer_info->last_name;
            $this->email = $order_data->customer_info->email_address;
            $this->phone_no = $order_data->customer_info->mobile_no;
            $this->platform_id = $order_data->additional_info->platform_id;
            $this->scheduled_delivery_date = $order_data->additional_info->scheduled_delivery_date;
            $this->pref_slab1 = $order_data->additional_info->preferred_delivery_slot1;
            $this->pref_slab2 = $order_data->additional_info->preferred_delivery_slot2;
            // if(isset($orderInfo->customer_type) && $orderInfo->customer_type==3015) {
            //     $beat = env('CNC_BEAT_ID');//552;
            // }else if(isset($orderInfo->customer_type) && $orderInfo->customer_type==3016){
            //     $beat=env('CLEARANCE_BEAT_ID');
            // }
            $this->beat = $beat;
            $this->order_code = $this->getOrderReference();
            $this->hub_id = $this->getHubId();
            $this->le_wh_id = $this->getLeWhId();
            $this->ecash_applied = $orderInfo->applied_ecash;
            $this->discount_before_tax = isset($orderInfo->discount_on_tax_less)?$orderInfo->discount_on_tax_less:0;
            $this->instant_wallet_cashback = isset($orderInfo->instant_wallet_cashback)?$orderInfo->instant_wallet_cashback:0;

            /**
             * [$this->discount_amt description]
             * @var [type]
             */
            
            $discounttype = NULL;
            if(isset($orderInfo->discounttype)){

                if($orderInfo->discounttype == ""){
                    $discounttype = NULL;
                }else{
                    $discounttype = $orderInfo->discounttype;
                }
            }

            $discountamount = 0.00;
            if(isset($orderInfo->discountamount)){

                if($orderInfo->discountamount == "" || $orderInfo->discountamount == 0){
                    $discountamount = 0.00;
                }else{
                    $discountamount = $orderInfo->discountamount;
                }
            }

            $discount = 0;
            if(isset($orderInfo->discount)){

                $discount = $orderInfo->discount;
            }

            echo "Discount";
            var_dump($discount);
            var_dump($data['extraCalulatedData']);
            // if the discount is zero no discount is applied
            if($discount == 0){
                $this->total = $data['extraCalulatedData']['total_amount'];
            }else{
                $this->total = $data['extraCalulatedData']['sub_total'] - $discountamount;
            }
            
            $mfc_id = isset($orderInfo->mfc_id) ? $orderInfo->mfc_id : 0;


            $this->discount = $discount;
            $this->discount_amt = $discountamount;
            $this->discount_type = $discounttype;
            $this->mfc_id = $mfc_id;

            $this->is_self = $this->getIsSelf();
            if($this->is_self==1){
                $this->self_user_id = $order_data->additional_info->created_by;
                $ffId =  $Dmapiv2Model->getFFByBeat($this->beat);
                $this->created_by = ($ffId>0)?$ffId:$order_data->additional_info->created_by;
            }else{
                $this->created_by = $order_data->additional_info->created_by;
            }
            $this->is_cnc = (isset($orderInfo->customer_type) && $orderInfo->customer_type==3015)?1:0;
            //var_dump($this);

            // checking primary sale or secondary sale
            // 1016 - DC,1014 - FC,3001 - Customer
            $this->is_primary_sale = 0;
            $this->primary_dc_id = 0;
            $this->is_secondary_sale = 0;
            $cust_le_type = $this->getLegalEntityTypeId($this->cust_le_id);
//            Log::info($cust_le_type."customer legalentity type id");
            $wh_type_id = $this->getWhareHouseTypeId($this->le_wh_id);
  //          Log::info($wh_type_id."warehouse  type id");
            if($wh_type_id == 1001 && ($cust_le_type == 1014 || $cust_le_type == 1016)){
                // APOB to DC/FC
                $this->is_primary_sale = 1;
                $this->is_secondary_sale = 0;
            }else if($wh_type_id == 1016 && $cust_le_type == 1014 || $wh_type_id == 1014 && $cust_le_type == 1016){
                // DC to FC or FC to DC
                $this->is_primary_sale = 0;
                $this->is_secondary_sale = 0;
            }else if($wh_type_id != 1001 && $cust_le_type != 1014 && $cust_le_type != 1016){
                // DC/FC to Retailers
                $this->is_primary_sale = 0;
                $this->is_secondary_sale = 1;
            }else if($wh_type_id == 1001 && $cust_le_type != 1014 && $cust_le_type != 1016){
                // APOB to Retailer placing order
                    $this->is_primary_sale = 1;
                  //  Log::info($this->is_primary_sale."is primaryKey need to be updated");
                    $this->is_secondary_sale = 1;
            }

            // if($cust_le_type == 1014 or $cust_le_type == 1016){
            //     $this->is_primary_sale = 1;
            // }else{
            //     $this->is_secondary_sale = 1;                
            // }
            $this->primary_dc_id = $this->getParentWhId($this->le_wh_id);

            $this->save();
            $order_id = DB::getPdo()->lastInsertId();
            if($order_id){
                //$data_duce['le_wh_id'] = $this->getLeWhId();
                $data_duce['mp_order_id'] = $order_id;
                // $data_duce['hub_id'] = 
                $this->updateFeildsOrder($data_duce,$order_id);
            }
            return $order_id;
        }catch(Exception $e){

           // return exception to the caller 
            var_dump($e->getMessage());
           return $e;
        }
        
    }

    public function gdsOrderSetTaxDetails() {

        try {
            
            $order_id = $this->gds_order_id;
            //var_dump($order_id);
            if (isset($this->order_state_id)){
                $state_id = $this->order_state_id;
            } else {

                $state_id = DB::table('zone')->where('name', $this->order_state)
                        ->where('country_id', 99)
                        ->pluck('zone_id');
                if (empty($state_id)) {
                    $state_id = 0;
                } else {
                    $state_id = $state_id[0];
                }
                $this->order_state_id = $state_id;
            }
            
            $total_tax = 0;
            $products = json_decode(json_encode($this->_orderModel->getCompleteProductByOrderId($order_id)),true);

            if (count($products) > 0) {
                
                $tax_product = array();
                $_warehouseModel = new warehouseModel();
                foreach ($products as $key => $product) {
                    $data['product_id'] = (int) $product['product_id'];
                    $product_total = $product['total'];
                    $data['buyer_state_id'] = (int) $this->order_state_id;
                    $warehouseid = $this->getLeWhId();
                    $warehouse = $_warehouseModel->getwareHousedata($warehouseid);
                    if (count($warehouse) == 0) {
                        $warehouse_statecode = 4033;
                    } else {
                        $warehouse_statecode = $warehouse['state'];
                    }
                    $data['seller_state_id'] = (int) $warehouse_statecode;

                    $product_tax = 0;
                    $taxInfo = $this->getTaxInfo($data, $order_id);
                    //var_dump($taxInfo);
                    $data = array();
                    if ($taxInfo != 'No data from API' && count($taxInfo) > 0) {

                        $data = array();
                        foreach ($taxInfo as $value) {

                            $taxClass_id = $value['Tax Class ID'];                       
                            $tax_temp = array('gds_order_prod_id' => $product['gds_order_prod_id'], 'tax_class' => $taxClass_id, 'tax' => $value['Tax Percentage']);
                            $product_tax += $value['Tax Percentage'];
                            $data[] = $tax_temp;
                        }

                        $actual_ammount = ($product_total * 100) / (100 + $product_tax);
                        $actual_ammount = round($actual_ammount, 5);
                        $product_tax_value = round(($product_total - $actual_ammount), 5);
                        $total_tax += $product_tax_value;

                        /* 
                            Update query done from single hand multiple update not done
                        */
                        DB::table('gds_order_products')
                                    ->where('gds_order_prod_id', $product['gds_order_prod_id'])
                                    ->update([
                                            'tax' => $product_tax_value,
                                            'price' => $actual_ammount
                                    ]);
                        $tax_data = array();
                        foreach ($data as $value) {
                            $temp_tax_data = array();
                            $temp_tax_data = $value;
                            $percentage = $value['tax'];
                            $temp_tax_data['tax_value'] = $product_tax_value * ($percentage / $product_tax);
                            $tax_data[] = $temp_tax_data;
                        }
                    }

                    if (count($data) > 0) {
                        DB::table('gds_orders_tax')->insert($tax_data); /* Query Builder */
                    }
                }
            }

            return $total_tax;

        } catch (Exception $e) {
            return $e;
        }
    }

    public function getTaxInfo($data, $orderid) {
        Log::info(json_encode($data).'URL='.env('APP_TAXAPI').'=orderid=='.$orderid);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('APP_TAXAPI'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        Log::info('TAX Class Response:'.$response);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            
        } else {

            $response = json_decode($response, true);
            if ($response) {
                if ($response['Status'] != 200) {
                    if (isset($response['ResponseBody'])) {
                        $args = array("ConsoleClass" => 'taxmail', 'arguments' => array('DmapiTaxTemplate', $orderid, json_encode($data), $response['ResponseBody']));
                    }else{
                        $args = array("ConsoleClass" => 'taxmail', 'arguments' => array('DmapiTaxTemplate', $orderid, json_encode($data), $response['Message']));  
                    }                    
                    //$token = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                    return 'No data from API';
                } else {

                    return $response['ResponseBody'];
                }
            } else {
                
            }
            return $response;
        }
    }


    public function updateFeildsOrder($data,$orderid){
    
        try{
            $taxupdate = DB::table('gds_orders')->where('gds_order_id', $orderid)
                    ->update($data);
        }catch(\Exception $e){
            echo  $e->getMessage();
            return $e;
        }
    }

    /**
     * [orderReference description]
     * @return [type] [description]
     * created by @Optimizer team 
     * created Date 13 Nov Its Sunday !!!!
     */
    public function orderReference(){

        //$OrderReference = $_DmapiorderModel->orderReference($state_id, 'SO');
        $stateId = $this->getOrderStateId();
        $type = 'SO';

        try{
            $custrepo = new CustomerRepo();
            $orderCode = $custrepo->getRefCode($type,$stateId);
            $this->setOrderReference($orderCode);
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
                        DELETE FROM `gds_orders_tax` WHERE gds_order_id IN
                        ($orderID)"
                    );
            return true;
        }catch(\Exception $e){

            return false;
        }
    }

    public function updateOrderTaxTotal($orderId,$tax){
        try{
            $update =   DB::table('gds_orders')
                        ->where('gds_order_id',$orderId)
                        ->update(['tax_total' => $tax]);
            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    public function checkSelfOrder($orderInfo){

        if(isset($orderInfo->is_self)){

            $this->setIsSelf($orderInfo->is_self);
        }else{
            $this->setIsSelf(NULL);
        }
    }


    /**
     *  reverseEcash
     */

    public function reverseEcash($orderData){

        $orderData = json_decode($data['orderdata'],true);
        $orderInfo = $orderData['order_info'];
        $legal_entity_id = $orderData['customer_info']['cust_le_id'];
        $user_id = $orderData['additional_info']['customer_id'];
        $ecash_applied = $orderData['orderInfo']['applied_ecash'];
        $created_at = date('Y-m-d H:i:s');
        $transaction_date = date('Y-m-d H:i:s');
        $sales_rep_id = $orderData['additional_info']['sales_rep_id'];
        $is_self = $orderData['order_info']['is_self'];

        if($is_self == 0){

            $created_by = $sales_rep_id;
        }else{

            $created_by = $user_id;
        }

        DB::table('ecash_transaction_history')->insert([

                'user_id' => $user_id,
                'legal_entity_id' => $legal_entity_id,
                'cash_back_amount' => $ecash_applied,
                'transaction_date' => $transaction_date,
                'transaction_type' => 143002,
                'created_at' => $created_at,
                'created_by' => $created_by

            ]);
        // INSERT INTO `qcebutor`.`ecash_transaction_history` (`user_id`, `legal_entity_id`, `cash_back_amount`, `transaction_date`, `created_by`, `created_at`) VALUES ($user_id, $legal_entity_id, , '2017-07-31 11:53:46', 4164, '2017-07-31 11:58:02');
        // 143002

    }

    public function getLegalEntityTypeId($le_id){
        $legal_entity_type_id = DB::table("legal_entities")->select('legal_entity_type_id')->where('legal_entity_id',$le_id)->first();
        return isset($legal_entity_type_id->legal_entity_type_id)?$legal_entity_type_id->legal_entity_type_id:0;
    }

    public function getParentWhId($le_wh_id){
        $parent_dc_id = DB::table("dc_fc_mapping")
                            ->select('dc_le_wh_id')
                            ->where('fc_le_wh_id',$le_wh_id)
                            ->first();
        return isset($parent_dc_id->dc_le_wh_id)?$parent_dc_id->dc_le_wh_id:0;
    }

    public function getWhareHouseTypeId($le_wh_id){
        $query = DB::table('legalentity_warehouses as lw')
                ->select('le.legal_entity_type_id')
                ->leftJoin('legal_entities as le','le.legal_entity_id','=','lw.legal_entity_id')
                ->where('lw.le_wh_id','=',$le_wh_id)
                ->first();
        return isset($query->legal_entity_type_id)?$query->legal_entity_type_id:0;
    }

}
