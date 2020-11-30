<?php
namespace App\Modules\DmapiV2\Controllers;

date_default_timezone_set('Asia/Kolkata');

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use Event;
use App\Events\DashboardEvent;
use App\Central\Repositories\ReportsRepo;
use DB;
use Cache;

/* * ***Use for only queing and the session mangement****** */
use App\Lib\Queue;
use App\models\Mongo\MongoDmapiModel;
use \Input;
use \Response;
use App\Modules\DmapiV2\Models\GDSProducts;
use App\Modules\DmapiV2\Models\GDSOrders;
use App\Modules\DmapiV2\Models\GDSCashback;
use App\models\Warehouse\warehouseModel;
use App\Modules\Orders\Models\PaymentModel;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Cpmanager\Models\EcashModel;
use App\Central\Repositories\ProductRepo;
//use Log;

class Dmapiv2Controller extends BaseController{

    private $le_wh_id;
    private $hub_id;
    private $legal_entity_id;
    public $_Dmapiv2Model;
    public $queue;


    public function __construct() {
        $this->_Dmapiv2Model = new Dmapiv2Model();
        $this->queue = new Queue();
        $this->_ecash = new EcashModel();
        $this->repo = new ProductRepo();

    }


    /*
      
      @param type $data
      $data includes keys :
                @$api_name  

      @return type is a string message which tlls you whether the user has permission or not
      Description: This api request is used  to authenticate user and check  user permissions.
     */

    public function checkUserPermission($api_name) {

        $data = Input::get();
        $data['api_name'] = $api_name;

        $result = $this->_Dmapiv2Model->checkUserAccess($data['api_key'], $data['secret_key'], $data['api_name']);
        $data['checkUserPermission_result'] = $result;
        //$logWrite = $this->putIntorequestLog($api_name, $data);

        /*
          result is the name of the method to be called
        */
        if($result){
          if(isset($data['orderdata'])){

            $method = $result['feature_name'];
            $Orderdata = json_decode($data['orderdata']);
            $userId = isset($Orderdata->customer_info->channel_user_id) ? $Orderdata->customer_info->channel_user_id : 2;
            // As now, Ebutor is dealing with more than 1 company and DC, we need to fetch
            // the Parent Legal Entity Id of the Retailer based on his User Id
            //$this->legal_entity_id = $this->getLeIdByUserId($userId);
            $result = $this->$method($data);
           
            if ($result instanceof Exception) {
                
                return Exception($result->getMessage());

            }else{
              return $result;
            }
            
          }else{
            $status = 0;
                $message = 'orderdata key is not available**required to pass to the method****';
                return $this->returnJsonMessage($status, $message);
          }
          
        }else{
            $status = 0;
                $message = 'Keys Are not accurate or you have no permission to access!!!';
                return $this->returnJsonMessage($status, $message);
        }
    }

    /**
    * @param [userId] -> Customer User ID
    * @return [parentLeId] -> parentLegalEntityId
    */
    public function getLeIdByUserId($userId)
    {
      $parent_le_id =
        DB::table("retailer_flat as rf")
          ->select("rf.parent_le_id")
          ->leftJoin("users","users.legal_entity_id","=","rf.legal_entity_id")
          ->where("users.user_id",$userId)
          ->first();
      $parentLeId = isset($parent_le_id->parent_le_id)?$parent_le_id->parent_le_id:0;
      return $parentLeId;
    }
    public function getWhDetails($le_wh_id)
    {
      $wh_details = DB::table("legalentity_warehouses as lw")
          ->select("lw.le_wh_id","lw.legal_entity_id", "lw.lp_wh_name AS warehouse_name", "lw.address1", "lw.address2", "lw.city", "lw.pincode", "lw.phone_no", "lw.email", "lw.contact_name")
          ->where("lw.le_wh_id",$le_wh_id)
          ->first();
      return $wh_details;
    }

    /**
     * @param  [data]
     * @return [response]
     */
    public function placeOrder($data){

      Log::info('$$$Log starts now');

      $MongoDmapiModel = new MongoDmapiModel();
      //$data = json_encode($data);

        try {
            // @$ebutorChannelId = Config('dmapi.channelid');
            // if ($ebutorChannelId == $this->channelId) {

            $Orderdata = json_decode($data['orderdata']);
            $customer_token = isset($Orderdata->additional_info->customer_token) ? $Orderdata->additional_info->customer_token : null;
            $wh_id = isset($Orderdata->product_info[0]->le_wh_id)?$Orderdata->product_info[0]->le_wh_id:'';
          Log::info('$$$Before whdetails');

            if($wh_id!=""){
              $whdetails = $this->getWhDetails($wh_id);
              $data['legal_entity_id'] = (int)$whdetails->legal_entity_id;
          Log::info($data['legal_entity_id']);
            }else{
              return Response::json(['status' => 400,'message'=> 'warehouse id should not be empty']);
            }
            if (!is_null($customer_token) || !empty($customer_token)) {
                $this->_Dmapiv2Model->updateCart($customer_token, 0);
            }
         Log::info('$$$After update cart');
 
            //}
        } catch (Exception $e) {
            echo "Mail or email send failed";
        }

        //check if $data['retry_token'] exists;
        if(isset($data['retry_token'])){

         Log::info('$$$In retry');

          $token = $data['retry_token']; // when the token is recreated mongo insertid
          

          $result = $MongoDmapiModel->updateTokenOnRetry($token);

          if(is_array($result)){

            if($result['status'] == 200){
                $data = json_encode($order);
                $data = base64_encode($data);
                $args = array("ConsoleClass" => 'DmapiVer2', 'arguments' => array('placeOrder', $data, $token));
                $token_job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                return Response::json(['status' => 200,'message'=> 'Order re-queued please check status']);
            }else{

              return Response::json($result);
            }
          
          }else{

              return Response::json(['status' => 400,'message'=> 'Error in requeing try again later !!!']);
          }

        }else{

         Log::info('$$$In breakdown');
          //break the order here on basis of data
          $orderSplit = $this->_Dmapiv2Model->placeOrderBreakDown($data);
          if (!$orderSplit) {
         Log::info('$$$Break failed');
              return Response::json(['Status' => 1, 'Message' => 'Your Order is placed you will recieve the updated order details soon', 'transactionId' => $token, 'token' => $token_job]);
          } else {

              $token_array = array();
              $token_job_array = array();
              foreach ($orderSplit as $order) {

                  $token = $MongoDmapiModel->insertDmapiRequest('placeOrder', $order);
                  $data = json_encode($order);
                  $data = base64_encode($data);
                  $args = array("ConsoleClass" => 'DmapiVer2', 'arguments' => array('placeOrder', $data, $token));
                  $token_job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                  array_push($token_array, $token);
                  array_push($token_job_array, $token_job);
              }
         Log::info('$$$After Dmapi req');

              return Response::json(['Status' => 1, 'Message' => 'Your Order is placed you will recieve the updated order details soon', 'transactionId' => implode(',', $token_array), 'token' => implode(',', $token_job_array)]);
          }

        }       
     
    }
    public function placeOrderConsole($data) {

      try {

         Log::info('$$$In placeOrderConsole');
          $ordersArray = [];
          $status = 0;
          $message = '';
          $order_id = 0;
          $orderData = $data['orderdata'];

          // Log::info("testingdata");
          // Log::info($data);
          // retry count fo pushing back to queue
          if(isset($data['retry_count'])){
              $data['retry_count'] += 1;
          }else{
              $data['retry_count'] = 0;
          }
          
          if (empty($orderData)) {
              return $this->returnJsonMessage(0, 'Request Format not correct.');
          }
          //Extract Order data
          $order_data = json_decode($data['orderdata']);
          

          if (property_exists($order_data, 'order_info')) {
              $orderInfo = $order_data->order_info;
          }

          if (empty($orderInfo)) {
              return $this->returnJsonMessage(0, 'No Order info.');
          }


          $shipping_pincode = NULL;
          //check for address info will be requiring it for inventory check
          if (property_exists($order_data, 'address_info')) {

              if (empty($order_data->address_info)) {
                  return $this->returnJsonMessage(0, 'Address missing for order.');
              } else {

                  foreach ($order_data->address_info as $address) {
                      if ($address->address_type == 'shipping') {
                          $shipping_state = $address->state;
                          $shipping_pincode = $address->pincode;
                      }
                  }
              }
          }
          else{
            return $this->returnJsonMessage(0, 'Address missing for order.');
          }
      
      if (property_exists($order_data, 'payment_info')) {
              $orderInfo = $order_data->order_info;
          }
        
          $productDetails = null;
          $extraCalulatedData = array();
          $extraCalulatedData['shop_name'] = '';
          $extraCalulatedData['total_amount'] = 0;
          $extraCalulatedData['sub_total'] = 0;
          $extraCalulatedData['shipping_state'] = $shipping_state;
          $extraCalulatedData['pincode'] = $shipping_pincode;
          $extraCalulatedData['state_id'] = $this->_Dmapiv2Model->getStateIdFromStateName($shipping_state);
          $extraCalulatedData['le_wh_id'] = (int)$this->le_wh_id;
          
          $extraCalulatedData['legal_entity_id'] = (int)$data['legal_entity_id'];

          $extraCalulatedData['order_reference'] =  $order_data->payment_info[0]->order_id;
          
          if (property_exists($order_data, 'product_info')) {

              if (empty($order_data->product_info)) {
                  return $this->returnJsonMessage(0, 'Product Info missing.');
              } else {
                  foreach ($order_data->product_info as $products_array) {


                      if (empty($products_array->sku)) {
                          $status = 0;
                          $message = 'Product Sku missing.';
                          return $this->returnJsonMessage($status, $message);
                      }else {
                          $this->le_wh_id = $products_array->le_wh_id;
                          
                          if(!property_exists($products_array, 'hub_id')){
                              $this->hub_id = 0;
                          }else{
                              $this->hub_id = $products_array->hub_id;
                          }
                          var_dump($products_array->hub_id);
                          
                          $discountamount = 0.00;
                          if(isset($products_array->discount_amount)){

                                
                                if($products_array->discount_amount == "" || $products_array->discount_amount == 0){
                                    $discountamount = 0.00;
                                }else{
                                    $discountamount = $products_array->discount_amount;
                                }
                          }

                          $extraCalulatedData['sub_total']      += $products_array->subtotal;
                          $extraCalulatedData['total_amount']   += $products_array->total - $discountamount;
                      }
                  }
                  
                  $data['extraCalulatedData'] = $extraCalulatedData;
              }
          } else {
            return $this->returnJsonMessage(0, 'Product info missing.');
          }

          $ordersArray = array();
          $ordersIdActual = array();

            if (property_exists($order_data, 'order_info')) {
                $orderInfo = $order_data->order_info;
            }
            if (empty($orderInfo)) {
                return $this->returnJsonMessage(0, 'No Order info.');
            }
            if (!property_exists($order_data->customer_info, 'cust_le_id')) {
                $order_data->customer_info->cust_le_id = 0;
            }

          if (property_exists($order_data, 'additional_info')) {

            $additional_info = $order_data->additional_info;
            $customer_token = 0;
            $sales_token = 0;
            $fieldforce_token = 0;

            /**
             * Checking Property Exists Scheduled Delivery Date
             */
            if (property_exists($order_data->additional_info, 'scheduled_delivery_date')) {
                $order_data->additional_info->scheduled_delivery_date = date('Y-m-d',strtotime($order_data->additional_info->scheduled_delivery_date));
            }else{
                 $order_data->additional_info->scheduled_delivery_date = date( "Y-m-d", strtotime( date('Y-m-d')." +1 day" ) );
            }

            if (!property_exists($order_data->additional_info, 'platform_id')) {

                $order_data->additional_info->platform_id = 0;
            }

            if (!property_exists($order_data->additional_info, 'customer_token')) {

                $order_data->additional_info->customer_token = 0;

            }else{

                $customer_token = $order_data->additional_info->customer_token;
            }

            if (!property_exists($order_data->additional_info, 'sales_token')) {

                $order_data->additional_info->sales_token = 0;
            }else{

                $sales_token = $order_data->additional_info->sales_token;
            }

            $actual_token = 0;
            if ( $order_data->additional_info->sales_token == "" ) {

                $actual_token = $order_data->additional_info->customer_token;
                
            } else {
                
                $actual_token = $order_data->additional_info->sales_token;

            }
            $order_data->additional_info->created_by = $this->_Dmapiv2Model->findUserIdByPassword($actual_token);

            $fieldforce_token = isset($order_data->additional_info->sales_token) ? $order_data->additional_info->sales_token : null;
          }
          $data['orderdata'] = json_encode($order_data);
          //echo $data['orderdata'];
          $this->_Dmapiv2Model->setLeWhId($this->le_wh_id);
          $this->_Dmapiv2Model->setHubId($this->hub_id);
          $this->_Dmapiv2Model->setOrderReference($extraCalulatedData['order_reference']);

         Log::info('$$$In gdsOrderDetails');
          $orderInfo  = $this->_Dmapiv2Model->gdsOrderDetails($data);
         Log::info('$$$After gdsOrderDetails');

          if($orderInfo instanceof Exception){ // for some reason the order call is false

            echo "Order Genenration failed on controller";
            $this->_Dmapiv2Model->updateCart($customer_token, 1);
            throw new Exception("Order Id Returned False !!");

          }else{

                $orderInfo = $this->_Dmapiv2Model->returnArray;
                var_dump($orderInfo);
              $order_id   = $orderInfo['order_id'];
              $order_code = $orderInfo['order_code'];
              $customer_name = $order_data->customer_info->first_name;
              $cu_name = $order_data->customer_info->first_name.' '.$order_data->customer_info->last_name;
              $mobile_no = $this->_Dmapiv2Model->getUserMobile($customer_token);

              if($order_id){

                $auto_invoice = isset($order_data->order_info->auto_invoice)?$order_data->order_info->auto_invoice:0;
                if($auto_invoice == 1){
                    $_invoiceModel = new Invoice();
                    $_invoiceModel->generateOpenOrderInvoice($order_id, true, 'Auto Invoice generated from Open.');
                }
                $extraCalulatedData['total_amount'] = $orderInfo['order_total'];
                date_default_timezone_set('Asia/Kolkata');
                $delivery_date = $order_data->additional_info->scheduled_delivery_date;
                $delivery_date = date('Y-m-d', strtotime($delivery_date));
                $message = "Your order number is \n#".$order_code." Order Value Rs: \n".$extraCalulatedData['total_amount'];
                $message_sales = "Your order against customer: $customer_name  has successfully place order id: #" . $order_code . " Ordered Total :  ".$extraCalulatedData['total_amount'] ;
                $cust_type = (isset($order_data->order_info->customer_type)) ? $order_data->order_info->customer_type:0;
                // if($cust_type==3015){
                //     $hub=($this->hub_id!='' && $this->hub_id!=0)?$this->hub_id:10694;
                //     $_warehouseModel = new warehouseModel();
                //     $warehouse = $_warehouseModel->getwareHousedata($hub);
                //     $hub_address = '';
                //     if (count($warehouse) != 0) {
                //         $hubaddr1 = isset($warehouse['address1'])?$warehouse['address1']:'';
                //         $hubaddr2 = isset($warehouse['address2'])?' '.$warehouse['address2']:'';
                //         $hubcity = isset($warehouse['city'])?' '.$warehouse['city']:'';
                //         $hubpincode = isset($warehouse['pincode'])?'-'.$warehouse['pincode']:'';
                //         $hubphone_no = isset($warehouse['phone_no'])?' M:'.$warehouse['phone_no']:'';
                //         $hub_address = $hubaddr1.$hubaddr2.$hubcity.$hubpincode.$hubphone_no;
                //     }
                //     $message .=" will be available to collect from the Ebutor CnC Counter. \n".$hub_address;
                //     $message_sales .=" please notify the customer to collect from the CnC Counter";                    
                // }else{
                //     $message .=" and product will be shipped in 1 day. Scheduled Delivery Date: $delivery_date";
                // }
                $message .=" and product will be shipped in 1 day. Scheduled Delivery Date: $delivery_date";
                $this->slackMsg($message, '#place_orders', $order_code);
                $this->_Dmapiv2Model->cartcancel($customer_token);
                $args = array("ConsoleClass" => 'mail', 'arguments' => array('DmapiOrderTemplate', $order_id));
                $token = $this->queue->enqueue('default', 'ResqueJobRiver', $args);

                \Notifications::addNotification(['note_code' => 'ORD001', 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['ORDID' => $order_code]]);
         Log::info('$$$Some where');

                if($order_data->additional_info->sales_token){//checking for ff 
//                  log::info('incentive log start');
                  $incentive = $this->_ecash->getIncentives($order_id,1);  
  //                log::info($incentive);
    //              log::info('incentive log end');
                  if($incentive['status']==200 && $incentive['message']['Field Force Associate']){
      //              log::info('amount notification');
                    $notificationMessage="Your incentive for ".$cu_name." is Rs. ".$incentive['message']['Field Force Associate'];
        //            log::info("string".$notificationMessage);
                    $string = $notificationMessage;
                    $substring ='s.';
                    $lastIndex = strripos($string, $substring);
                    $start  = $lastIndex + 2 ;
                    $end  =  strlen($string);
                   // echo  'Last index = '. $lastIndex   . ' ' . $end. '' .$start.'';exit; 
                    $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                    $deviceToken = $approvalFlowObj->getRegIds($order_data->additional_info->created_by);
                    $params_data = array("order_code"=>$order_code);
                    $pushNotification = $this->repo->pushNotifications($notificationMessage, $deviceToken, "default",'Ebutor','','','','' ,$start , $end);
                  }

                }
                
                //Event::fire(new DashboardEvent($order_data->additional_info->created_by)); 
                
                // Added Legal Entity Id recently
         Log::info('$$$Before fire');
                $this->dashboardEventFire($order_data->additional_info->created_by,$extraCalulatedData['legal_entity_id'],$this->le_wh_id);
         Log::info('$$$After fire');
            
              }else{
                echo "Order Genenration failed on controller";
                $message = "You order could not be placed please contact support";
                $message_sales = "You order against customer $customer_name could not be placed  contact support";
                $this->_Dmapiv2Model->updateCart($customer_token, 1);

              }

              $cust_le_type = $this->_Dmapiv2Model->getLegalEntityTypeId($order_data->customer_info->cust_le_id);
              $send_po_to_sms = DB::select(DB::raw("select `getMastLookupDescByValue`(179002) as send_po_to_sms"));
              $send_po_to_sms = isset($send_po_to_sms[0]->send_po_to_sms) ? $send_po_to_sms[0]->send_po_to_sms : 0;
          //    Log::info("cust_type".$cust_le_type);
            //  Log::info("send_po_to_sms".$send_po_to_sms);
              if(($cust_le_type != 1014 && $cust_le_type != 1016) || (($cust_le_type == 1014 || $cust_le_type == 1016) && $send_po_to_sms == 1)){
                $this->_Dmapiv2Model->sendSMS($mobile_no, $message);
                echo "SMS will be sent to " . $mobile_no;
              }
              /**
               * Sending SMS to FieldForce
               */
              if (!is_null($fieldforce_token) || !empty($fieldforce_token)) {
                @$fieldforce_mobile_no = $this->_Dmapiv2Model->getUserMobile($sales_token);
                if ($fieldforce_mobile_no != "" || $fieldforce_mobile_no != 0) {
                    $this->_Dmapiv2Model->sendSMS($fieldforce_mobile_no, $message_sales);
                }
              }
         Log::info('$$$At end');
              $returnArray['Message'] = $message;
              $returnArray['order_id_actual'] =  $order_id;
              $returnArray['order_id'] = $order_code;
              $returnArray['Status'] = 1;
              return $returnArray; 
            }

                 
    } catch (Exception $e) {

      $returnArray['message'] = $e->getMessage();
      $returnArray['status'] = 0;
      return $returnArray;
    }
}




    /**
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    private function returnJsonMessage($status, $message) {
        if ($status == 0) {
            //$this->orderFailureMail($message);
        }
        //Log::info(json_encode(array('Status' => $status, 'Message' => $message)));
        return Response::json(array('Status' => $status, 'Message' => $message));
    }

    

    /**
     * [putIntorequestLog description]
     * @param  [string] $api_name [Api Name]
     * @param  [string] $data     [Data pushed latter on put this into the mongo central for 
     *                             Work around ]
     * @return [type]           [description]
     */
    public function putIntorequestLog($api_name, $data) {

        try {
            $folder_path = trim(storage_path() . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "DMAPI_logs" . DIRECTORY_SEPARATOR . " ");
            $write_file = $api_name . '.log';
            $file_name = $folder_path . $write_file;
            $write_data = '' . PHP_EOL;
            $write_data .= 'Date:' . date('d-m-Y H:i:s') . PHP_EOL;
            $write_data .= 'Request Data ' . PHP_EOL;
            $write_data .= json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
            $file = fopen($file_name, "a");
            fwrite($file, $write_data);
            fclose($file);
            return true;
        } catch (ErrorException $e) {
            $order_id = 0;
            $message = $e->getTraceAsString();
            return false;
        }
    }

    /**
     * slackMsg for sending message to slack
     * @param  string $msg [text to send]
     * @param  string $channel [name of the slack group]
     * @return void      [description]
     */
    public function slackMsg($msg, $channel, $order_id = '')
    {
        $url = 'https://hooks.slack.com/services/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';
        $data['channel'] = $channel;
        $data['username'] = 'DmApiV2';
        if (!is_null($order_id)) {
            $data['text'] = '#NEW ORDER:'.$order_id.'\n'.$msg;
        }else{
            $data['text'] = $msg; 
        }
        $data['icon_url'] = 'http://portal1.ebutor.com/assets/admin/layout/img/small-logo.png';

        $request['payload'] = json_encode($data, true);
        $ch = curl_init();        
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$request);
        $buffer = curl_exec($ch);
        if(empty ($buffer)){ 
            return false;
        }else{ 
            return true;
        } 
    }

    /**
     * [checkTaxApi description]
     * @return [type] [description]
     */
    public function checkTaxApi(){

        $data = Input::get();

        if(!isset($data['product_id']) || !isset($data['buyer_state_id']) || !isset($data['buyer_state_id'])){

            return Response::json(array('Status' => 0 , 'Message' => "All parameters are invalid"));

        }
                // $data['product_id'] = (int) $product_id;
                // $data['buyer_state_id'] = (int) $this->buyer_state_id;
                // $data['seller_state_id'] = (int) $this->seller_state_id;
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

        curl_close($curl);
        if($response === false){
            $err = curl_error($curl);
            return Response::json(array('Status' => 0 , 'Message' => $err));
        }else{

            $response = json_decode($response, true);
            return Response::json($response);   
        }



    }

    /**
     * [dashboardEventFire description]
     * @param [type] [user_id]
     * @param [type] [legal_entity_id]
     * @return [type] [description]
     */
    public function dashboardEventFire($user_id,$legal_entity_id,$le_wh_id){
        //Log::info("dashboard");
        $reports = new ReportsRepo();
        $fromDate = date('Y-m-d');
        $datetime = new \DateTime('tomorrow');
        $toDate = $datetime->format('Y-m-d');
        $date = date('Y-m-d h:i:s a');
        $brandid='NULL';
        $manufid='NULL';
        $productgrpid='NULL';
        $categoryid='NULL';
        $leid=$reports->getlegalidbasedondcid($le_wh_id);
        $legal_entity_id=isset($leid->legal_entity_id)?$leid->legal_entity_id:$legal_entity_id;
        $buid=isset($leid->bu_id)?$leid->bu_id:1;
        // Closing this Code as the proc is no longer needed!
        // $response = DB::select("CALL getOrdersDashboard_web(0, 1,'$fromDate','$toDate')");
        // $response = DB::select("CALL getDnCDashboard_web(0, 1,'$fromDate','$toDate')");
        //$response = DB::select("CALL getDynamicDnCDashboard_web(0, 1,'$fromDate','$toDate',$legal_entity_id,$le_wh_id)");
        $response=DB::select(DB::raw('CALL getDynamicDnCDashboardByBU_web(0,1, "'.$fromDate.'", "'.$toDate.'",'.$buid.',NULL,NULL,NULL,NULL)'));
        $response = json_decode(json_encode($response),true);

        $CACHE_TAG = "dncDashboard_".$legal_entity_id;
        if(!empty($response) && isset($response[0]['Dashboard'])){
          $tempData = json_decode($response[0]['Dashboard'],true);    
          // Putting Cache for Flag 1 -- Without TGM
          Cache::tags($CACHE_TAG)->put('dasboard_report'.'0'.'_1_'.$fromDate.'_'.$toDate.'_1_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid,$tempData,5);
          Cache::tags($CACHE_TAG)->put('dasboard_report'.'0'.'_1_'.$fromDate.'_'.$toDate.'_1_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid.'_last_updated',$date,5);
        }

        // Response with TGM
        // Reason to Store TGM
        // $response_TGM = DB::select("CALL getDnCDashboard_web(0, 4,'$fromDate','$toDate')");
        //$response_TGM = DB::selectFromWriteConnection(DB::raw("CALL getDynamicDnCDashboard_web(0, 4,'$fromDate','$toDate',$legal_entity_id,$le_wh_id)"));
        $response_TGM=DB::select(DB::raw('CALL getDynamicDnCDashboardByBU_web(0,4, "'.$fromDate.'", "'.$toDate.'",'.$buid.',NULL,NULL,NULL,NULL)'));
        $response_TGM = json_decode(json_encode($response_TGM),true);
        if(!empty($response_TGM) && isset($response_TGM[0]['Dashboard'])){
          $tempData_with_TGM = json_decode($response_TGM[0]['Dashboard'],true);    
              // Putting Cache for Flag 4 -- For TGM Access
            Cache::tags($CACHE_TAG)->put('dasboard_report'.'0'.'_1_'.$fromDate.'_'.$toDate.'_4_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid,$tempData_with_TGM,5);
            Cache::tags($CACHE_TAG)->put('dasboard_report'.'0'.'_1_'.$fromDate.'_'.$toDate.'_4_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid.'_last_updated',$date,5);
        }        

        $last_updated = $date;
        $dashboard_result['data'] = $tempData;

        if($user_id != 0){

          // Closing this Code as the proc is no longer needed!
          // $responseD = DB::select("CALL getOrdersDashboard_web($user_id, 1,'$fromDate','$toDate')");
          // $responseD =  DB::select("CALL getDnCDashboard_web($user_id, 1,'$fromDate','$toDate')");
          //$responseD =  DB::selectFromWriteConnection(DB::raw("CALL getDynamicDnCDashboard_web($user_id, 1,'$fromDate','$toDate',$legal_entity_id,$le_wh_id)"));
          $responseD=DB::select(DB::raw('CALL getDynamicDnCDashboardByBU_web('.$user_id.', 1, "'.$fromDate.'", "'.$toDate.'",'.$buid.',NULL,NULL,NULL,NULL)'));
          $responseD = json_decode(json_encode($responseD),true);
          if(!empty($responseD) && isset($responseD[0]['Dashboard'])){
                $tempDataD = json_decode($responseD[0]['Dashboard'],true);
                Cache::tags($CACHE_TAG)->put('dasboard_report'.$user_id.'_1_'.$fromDate.'_'.$toDate.'_1_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid,$tempDataD,5);
                Cache::tags($CACHE_TAG)->put('dasboard_report'.$user_id.'_1_'.$fromDate.'_'.$toDate.'_1_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid.'_last_updated',$date,5);
          }

          // Userd Id Proc Call with TGM
          // $responseD_with_TGM =  DB::select("CALL getDnCDashboard_web($user_id, 4,'$fromDate','$toDate')");
          //$responseD_with_TGM =  DB::selectFromWriteConnection(DB::raw("CALL getDynamicDnCDashboard_web($user_id, 4,'$fromDate','$toDate',$legal_entity_id,$le_wh_id)"));
          $responseD_with_TGM=DB::select(DB::raw('CALL getDynamicDnCDashboardByBU_web('.$user_id.', 4, "'.$fromDate.'", "'.$toDate.'",'.$buid.',NULL,NULL,NULL,NULL)'));
          $responseD_with_TGM = json_decode(json_encode($responseD_with_TGM),true);
          if(!empty($responseD_with_TGM) && isset($responseD_with_TGM[0]['Dashboard'])){
                $tempDataD_with_TGM = json_decode($responseD_with_TGM[0]['Dashboard'],true);
                Cache::tags($CACHE_TAG)->put('dasboard_report'.$user_id.'_1_'.$fromDate.'_'.$toDate.'_4_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid,$tempDataD_with_TGM,5);
                Cache::tags($CACHE_TAG)->put('dasboard_report'.$user_id.'_1_'.$fromDate.'_'.$toDate.'_4_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid.'_last_updated',$date,5);  
          }           
          $dashboard_result['data'] = $tempData_with_TGM;
        }

        $dashboard_result['time'] = $last_updated;
        $dashboard_result['bu_id'] = $buid;
        //var_dump($dashboard_result);
        //Log::info("hitting dashboard socket url");
        $this->socketCurl($dashboard_result);
    }

    /**
     * [socketCurl description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function socketCurl($data){
      //Log::info("hitting dashboard socket url");
      echo 'hitting '.env('SOCKET_IO')."/post";
      $curl = curl_init();
      //url-ify the data for the POST
      $data = http_build_query($data);

      curl_setopt_array($curl, array(
      CURLOPT_PORT => env('SOCKET_IO_PORT'),
      CURLOPT_URL => env('SOCKET_IO')."/post",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
                                "cache-control: no-cache",
                                "content-type: application/x-www-form-urlencoded"
                              ),
      ));

      $response = curl_exec($curl);
      //var_dump($response);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
        //Log::info("hitting dashboard socket url if loop");
      } else {
        echo $response;
        //Log::info("socket url else");
      }
    }

    /**
     * [rectifyTaxOnOrderId description]
     * @param  [type] $data = $data = array(
        'api_key' => 'cp_prod',
        'secret_key' => '1TqUmN38d6fak6AZ',
        'orderId' => '{"orderid":"'.$orderID.'","channelid":"1"}'
        )
     * @return [type]          [description]
     */
    public function rectifyTaxByOrderId($orderId){
      /*
          delete all gds_tax entry using the gds_order_product from gds_order_products
       */
      if(!empty($orderId)){
        echo "<pre>Order_id: $orderId <br>"; 
        $orders = new GDSOrders();
        $odrData = $orders->deleteGdsTaxFeilds($orderId);

        $products = new GDSProducts();

        $prodList = $products->getOrderProducts($orderId);

        $taxTotal = 0;
        if(count($prodList)>0){
          foreach($prodList as $prod){
            print_r($prod);
            $temp = $products->gdsProductsTaxRectify($orderId,$prod['product_id'],$prod['cost']);
            print_r($temp);
            $taxTotal += $temp['tax'];
            $products->updateProductsByRectifyTax($orderId,$prod['product_id'],$temp['tax'],$temp['price']);
            $products->updateGDSOrderTaxRectify($orderId,$prod['product_id']);
          }

          $orders->updateOrderTaxTotal($orderId,$taxTotal);
          return Response::json(array("Status"=>"True"));
        }
        else{
          return Response::json(array("Status"=>"False"));
        }
      }
      else
        return Response::json(array("Status"=>"Wrong Order ID"));
    }
}