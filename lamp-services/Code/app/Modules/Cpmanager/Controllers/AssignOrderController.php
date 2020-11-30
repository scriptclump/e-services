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
  use Lang;
  use Config;
  use View;
  use Illuminate\Http\Request;
  use App\Modules\Cpmanager\Models\AssignOrderModel;
  use App\Modules\Cpmanager\Models\MasterLookupModel;
  use App\Modules\Cpmanager\Views\order;
  use App\Http\Controllers\BaseController;
  use App\Modules\Cpmanager\Models\CategoryModel;
  use App\Modules\DmapiV2\Models\Dmapiv2Model;
  use App\Modules\Orders\Models\OrderModel;
  use App\Modules\Orders\Models\OrderTrack;
  use App\Modules\Roles\Models\Role;
  use App\Modules\CrateManagement\Models\CrateManagement;
  use App\Modules\Orders\Models\Inventory;
  use App\Modules\Orders\Models\CancelModel;
  use UserActivity;
  

  class AssignOrderController extends BaseController {  
    
    protected $order;
    protected $_Dmapiv2Model;
    protected $_orderModel;
    protected $_AssignOrderModel;
    protected $_OrderTrack;
    protected $_category;
    protected $_roleModel;
    protected $_Inventory;
    protected $_cancelModel;

    public function __construct() {
      $this->order = new OrderModel();
      $this->_Dmapiv2Model = new Dmapiv2Model();
      $this->_orderModel = new OrderModel();
      $this->_AssignOrderModel = new AssignOrderModel();
      $this->_OrderTrack = new OrderTrack();
      $this->_category = new CategoryModel();
      $this->_roleModel = new Role();
      $this->_crateManagement = new CrateManagement();
      $this->_Inventory = new Inventory();
      $this->_cancelModel = new CancelModel();
    }

    /**
     *  assignOrderToPickerAction() method is used to assign order to picker
     *  @param Null
     *  @return JSON
     */

    public function assignOrderToPickerAction() {
      
      DB::beginTransaction();

      try{
          $data = Input::all();
          #print_r($data);
          $postData = json_decode($data['data'], true);                   
          $apiKey = isset($data['api_key']) ? $data['api_key'] : Config::get('dmapi.GDSAPIKey');
          $secretKey = isset($data['secret_key']) ? $data['secret_key'] : Config::get('dmapi.GDSAPISECRETKey');
          $userId = isset($postData['user_id']) ? (int)$postData['user_id'] : 0;
          $pickerId = isset($postData['picker_id']) ? (int)$postData['picker_id'] : 0;
          
          /**
           * Validate input data
           */
          
          if(empty($data)) {
            return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
          }

          $response = $this->_Dmapiv2Model->checkUserAccess($apiKey, $secretKey, 'assignOrderToPicker');
          
          if(!$response) {
            return Response::json(array('Status' => 404, 'Message' => 'API Authentication failed.'));
          }

          // set userId
          
          $this->setSession($userId);

          if($userId <= 0 || $pickerId <= 0) {
            return Response::json(array('Status' => 404, 'Message' => 'Please verify User ID / Picker ID.'));
          }

          $orderStatusArr = $this->verifyOrderStatus($postData['orders']);
          #print_r($orderStatusArr);die;

          if(is_array($orderStatusArr) && count($orderStatusArr) <= 0) {
            return Response::json(array('Status' => 404, 'Message' => 'Please verify order id.'));
          }

          if(!in_array('17001', $orderStatusArr) || count($orderStatusArr) > 1) {
            return Response::json(array('Status' => 404, 'Message' => 'Make sure order status should be open.'));
          }
          
          /**
           * Assign order to picker
           */
          
          $orderData = array('ids'=>$postData['orders'], 'pickedBy'=>$pickerId, 'docArea'=>'');
          $result = $this->_orderModel->savePicklist($orderData);

          foreach($result as $value){
            if(!empty($value['cancelledArr'])){
              $uniq = array('order_id'=>$value['cancelledArr']['Order_id']);
              UserActivity::userActivityLog("CancelItemQtyOnPicking", $value['cancelledArr']['product_list'], "Cancel Item qty at Picklist generation" , '', $uniq);

              $this->cancelOrderItem($value['cancelledArr']['Order_id'], $value['cancelledArr']['product_list'], '17015', true, 'system');
            }
          }
          
          DB::commit();

          return Response::json(array('Status' => 402, 'Message' => $result));
      }
      catch(Exception $e) {
        DB::rollback();
        // Log::info("Internal server error" . ' => ' . $e->getTraceAsString());
        return Response::json(array('Status' => 404, 'Message' => 'Failed'));
      }
    }

    /**
     * assignOrderToDeliveryAction() method is to assign order to delivery person
     * @param Null
     * @return JSON
     */
    
     public function assignOrderToDeliveryAction() {
      
      DB::beginTransaction();

      try{
          $data = Input::all();
          #print_r($data);
          
          $apiKey = isset($data['api_key']) ? $data['api_key'] : Config::get('dmapi.GDSAPIKey');
          $secretKey = isset($data['secret_key']) ? $data['secret_key'] : Config::get('dmapi.GDSAPISECRETKey');
          $postData = json_decode($data['data'], true);
          $userId = isset($postData['user_id']) ? (int)$postData['user_id'] : 0;
          $deliveryExId = isset($postData['delivery_id']) ? (int)$postData['delivery_id'] : 0;

          /**
           * Validate input data
           */
          
          if(empty($data)) {
            return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
          }

          $response = $this->_Dmapiv2Model->checkUserAccess($apiKey, $secretKey, 'assignOrderToDelivery');
          
          if(!$response) {
            return Response::json(array('Status' => 404, 'Message' => 'API Authentication failed.'));
          }
                  
          // set userId
          
          $this->setSession($userId);

          if($userId <= 0 || $deliveryExId <= 0) {
            return Response::json(array('Status' => 404, 'Message' => 'Please verify User ID / Delivery Person ID.'));
          }
          
          $orderStatusArr = $this->verifyOrderStatus($postData['orders']);
          
          if(is_array($orderStatusArr) && count($orderStatusArr) <= 0) {
            return Response::json(array('Status' => 404, 'Message' => 'Please verify order id.'));
          }

          if(count($orderStatusArr) > 3 || !in_array('17021', $orderStatusArr) && !in_array('17025', $orderStatusArr) && !in_array('17014', $orderStatusArr)) {
            return Response::json(array('Status' => 404, 'Message' => 'Make sure order status should be Invoice /Stock in hub / Hold.'));
          }
          
          /**
           * assign order to delivery person
           */
          
          if(isset($postData['orders']) && is_array($postData['orders']) ) {
              foreach ($postData['orders'] as $orderId) {
                  $this->_orderModel->updateDeliveryStatusById($orderId, $deliveryExId, date('Y-m-d H:i:s'));
                  $this->_orderModel->updateOrderStatusById($orderId, '17026');
				  $comment = 'Assigned to delivery executive(Delivery ID: '.$deliveryExId.') by App';
                  $this->saveOrderComment($orderId, $comment, '17026', $userId); 
              }
          }
          /**
           * Send sms to retailer
           */

          $this->_OrderTrack->sendSmsToRetails($postData['orders']);

         DB::commit();
         return Response::json(array('Status' => 200, 'Message' => 'Assigned Successfully!'));
      }
      catch(Exception $e) {
        DB::rollback();
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        return Response::json(array('Status' => 404, 'Message' => 'Failed'));        
      }
    }

    /**
     * stockTransferAction() method is used to recieve stock in hub
     * @param Null
     * @return JSON
     */
    
    public function stockTransferAction() {
      DB::beginTransaction();

      try{

         $data = Input::all();
         //print_r($data);die;
                   
          $postData = json_decode($data['data'], true);
          $apiKey = isset($data['api_key']) ? $data['api_key'] : Config::get('dmapi.GDSAPIKey');
          $secretKey = isset($data['secret_key']) ? $data['secret_key'] : Config::get('dmapi.GDSAPISECRETKey');
          $deliveryId = isset($postData['delivery_id']) ? $postData['delivery_id'] : '';
          $deliveryMobile = isset($postData['delivery_mobile']) ? $postData['delivery_mobile'] : '';
          $vehicalNo = isset($postData['vehical_no']) ? $postData['vehical_no'] : '';
          $driverName = isset($postData['driver_name']) ? $postData['driver_name'] : '';
          $driverMobile = isset($postData['driver_mobile']) ? $postData['driver_mobile'] : '';
          $orders = isset($postData['orders']) ? $postData['orders'] : '';
          $orderComment = 'Stock transfer from DC-HUB by app';
          //print_r($postData);die;
          
          /**
           * Set userId in session
           */
          
          $userId = isset($postData['user_id']) ? (int)$postData['user_id'] : 0;
          $this->setSession($userId);

          /**
           * Validate input data, API
           */
          
          if($userId <= 0 || $deliveryId <= 0) {
            return Response::json(array('Status' => 404, 'Message' => 'Please verify User ID / Delivery Person ID.'));
          }

          $response = $this->_Dmapiv2Model->checkUserAccess($apiKey, $secretKey, 'stockTransfer');
          
          if(!$response) {
            return Response::json(array('Status' => 404, 'Message' => 'API Authentication failed.'));
          }
          
          $orderStatusArr = $this->verifyOrderStatus($postData['orders']);
          if(count($orderStatusArr) > 1 || (count($orderStatusArr) > 0 && !in_array('17021', $orderStatusArr))) {
            return Response::json(array('Status' => 404, 'Message' => 'Make sure order status should be invoice.'));
          }
         
          /**
           * Stock recieve by docket no. with order id
           */

          if(is_array($orders) && count($orders) > 0) {
            $docket_code = $this->_orderModel->getRefCode('TR');
            $stockData = array('st_del_ex_id' => $deliveryId, 
                              'st_del_date'=>date('Y-m-d H:i:s'), 
                              'st_vehicle_no'=>$vehicalNo, 
                              'st_driver_name'=>$driverName,
                              'st_driver_mobile'=>$driverMobile,
                              'st_docket_no'=>$docket_code);
            
            $orderComment .= '# Vehicle No: '.$vehicalNo.', Driver Name: '.$driverName.',Driver Mobile: '.$driverMobile;

            foreach ($orders as $orderId) {
              $this->_AssignOrderModel->updateTrackDetailByOrderId($orderId, $stockData);
              $this->saveOrderComment($orderId, $orderComment, '17024', $userId);
              $this->_AssignOrderModel->insertTrackHistoryByOrderId($orderId, $stockData);
            }

            $this->_orderModel->updateOrder($orders, array('order_status_id'=>'17024'));
          }

         DB::commit();
         return Response::json(array('Status' => 200, 'Message' => 'Stock Transit Successfully!'));
      }
      catch(Exception $e) {
        DB::rollback();        
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        return Response::json(array('Status' => 404, 'Message' => 'Failed'));
      }

    }

    /**
     * recieveStockInHubAction() method is used to recieve stock in hub
     * @param Null
     * @return JSON
     */
    
    public function recieveStockInHubAction() {
      DB::beginTransaction();

      try{

         $data = Input::all();
         //print_r($data);die;
                   
          $postData = json_decode($data['data'], true);
          $apiKey = isset($data['api_key']) ? $data['api_key'] : Config::get('dmapi.GDSAPIKey');
          $secretKey = isset($data['secret_key']) ? $data['secret_key'] : Config::get('dmapi.GDSAPISECRETKey');
          $docketNo = isset($postData['docket_no']) ? $postData['docket_no'] : '';
          $orders = isset($postData['orders']) ? $postData['orders'] : '';
          $orderComment = 'Received stock at Hub from app, Docket No#'.$docketNo;

          /**
           * Set userId in session
           */
          $userId = isset($postData['user_id']) ? (int)$postData['user_id'] : 0;
          $this->setSession($userId);

          /**
           * Validate input data, API and Docket No.
           */

          if(!$userId) {
            return Response::json(array('Status' => 404, 'Message' => 'Invalid user id'));
          }
          
          $response = $this->_Dmapiv2Model->checkUserAccess($apiKey, $secretKey, 'recieveStockInHub');
          
          if(!$response) {
            return Response::json(array('Status' => 404, 'Message' => 'Authentication failed. Verify credentials'));
          }
          
          $hasDocketNo = $this->_AssignOrderModel->verifyDocketNo($docketNo, $orders);    
          
          if(!isset($docketNo) || empty($docketNo) || $hasDocketNo <=0) {
              return Response::json(array('Status' => 404, 'Message' => 'Invalid docket number.'));
          }

          $orderStatusArr = $this->verifyOrderStatus($postData['orders']);
          //echo '<pre>';print_r($orderStatusArr);die;
          if(count($orderStatusArr) > 1 || (count($orderStatusArr) > 0 && !in_array('17024', $orderStatusArr))) {
            return Response::json(array('Status' => 404, 'Message' => 'Make sure order status should be stock in transit.'));
          }
          
          $result = $this->_AssignOrderModel->verifyStockInTransitByDocketNo($docketNo, $orders);

          if($result) {
            return Response::json(array('Status'=>400, 'Message'=>'Stock already received.'));
          }        
     
          /**
           * Stock recieve by docket no. only
           */
          
          if(!empty($docketNo) && is_array($orders) && count($orders) <= 0) {
            $this->_orderModel->confirmStockAtHub($docketNo, $userId);
            $orderIds = $this->_orderModel->getOrdersByStDocketId($docketNo);
            if(count($orderIds)) {
              foreach($orderIds as $order) {
              $this->_AssignOrderModel->insertTrackHistoryForConfirmStock(array('gds_order_id'=>$order->gds_order_id,'st_received_by'=>$userId,'st_received_at'=>date('Y-m-d H:i:s'),'created_by'=>Session('userId'),'created_at'=>date('Y-m-d H:i:s'),'st_docket_no'=>$docketNo));
                $this->saveOrderComment($order->gds_order_id, $orderComment, '17025', $userId);
              }
                $this->apiUpdateCrateStatus($orderIds, "", 137006, "", $userId);
            }            
          }

          /**
           * Stock recieve by docket no. with order id
           */

          if(is_array($orders) && count($orders) > 0 && !empty($docketNo)) {
            foreach ($orders as $orderId) {
              $this->_AssignOrderModel->updateTrackDetailByOrderId($orderId, array('st_received_by'=>$userId,'st_received_at'=>date('Y-m-d H:i:s')));
              $this->_AssignOrderModel->insertTrackHistoryForConfirmStock(array('gds_order_id'=>$orderId,'st_received_by'=>$userId,'st_received_at'=>date('Y-m-d H:i:s'),'created_by'=>Session('userId'),'created_at'=>date('Y-m-d H:i:s'),'st_docket_no'=>$docketNo));
              $this->saveOrderComment($orderId, $orderComment, '17025', $userId);
            }
            $this->_orderModel->updateOrder($orders, array('order_status_id'=>'17025'));
            $this->apiUpdateCrateStatus($orders, "", 137006, "", $userId);
          }

         DB::commit();
         return Response::json(array('Status' => 200, 'Message' => 'Stock Received Successfully!'));
      }
      catch(Exception $e) {
        DB::rollback();        
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        return Response::json(array('Status' => 404, 'Message' => 'Failed'));
      }

    }


    /**
     * recieveStockInDcAction() method is used to recieve stock in DC
     * @param Null
     * @return JSON
     */
    
    public function recieveStockInDcAction() {
        DB::beginTransaction();
        try {
            $data = Input::all();
            $postData = json_decode($data['data'], true);
            $apiKey = isset($data['api_key']) ? $data['api_key'] : Config::get('dmapi.GDSAPIKey');
            $secretKey = isset($data['secret_key']) ? $data['secret_key'] : Config::get('dmapi.GDSAPISECRETKey');
            $docketNo = isset($postData['docket_no']) ? $postData['docket_no'] : '';
            $orders = isset($postData['orders']) ? $postData['orders'] : array();
            $orderComment = 'Stock received in dc from app, Docket No#' . $docketNo;
            $delivered_orders = isset($postData['delivered_orders']) ? $postData['delivered_orders'] : array();
            $rahDeliveredOrders = array_merge($orders, $delivered_orders);
            
            if((count($orders) <= 0) && (count($delivered_orders) <= 0)){
                return Response::json(array('Status' => 404, 'Message' => 'Both RAH & Delivered orders cannot be empty'));
            }

            /* Set userId in session */
            $userId = isset($postData['user_id']) ? (int) $postData['user_id'] : 0;
            $this->setSession($userId);
            /* Validate input data, API and Docket No. */
            if (!$userId) {
                return Response::json(array('Status' => 404, 'Message' => 'Invalid user id'));
            }

            $response = $this->_Dmapiv2Model->checkUserAccess($apiKey, $secretKey, 'recieveStockInDc');
            if (!$response) {
                return Response::json(array('Status' => 404, 'Message' => 'Authentication failed. Verify credentials'));
            }

            $hasDocketNo = $this->_AssignOrderModel->verifyReturnDocketNo($docketNo, $rahDeliveredOrders);
            if (!isset($docketNo) || empty($docketNo) || $hasDocketNo <= 0) {
                return Response::json(array('Status' => 404, 'Message' => 'Invalid docket number.'));
            }

            if(count($postData['orders']) > 0){
                $orderStatusArr = $this->verifyOrderStatus($postData['orders']);
                if (count($orderStatusArr) > 0 && !in_array('17022', $orderStatusArr) && !in_array('17023', $orderStatusArr)) {
                    return Response::json(array('Status' => 404, 'Message' => 'Make sure order status should be returned / partial delivered.'));
                }

                $result = $this->_AssignOrderModel->verifySITDCByDocketNo($docketNo, $orders);
                if ($result) {
                    return Response::json(array('Status' => 400, 'Message' => 'Stock already received.'));
                }
            }

            /* Stock recieve by docket no. only */
//          if(!empty($docketNo) && is_array($orders) && count($orders) <= 0) {
//            $this->_orderModel->confirmStockAtDc($docketNo, $userId);
//            $orderIds = $this->_orderModel->getOrdersByStDocketId($docketNo);
//            if(count($orderIds)) {
//              foreach($orderIds as $order) {
//                $this->_AssignOrderModel->insertTrackHistoryForConfirmStock(array('gds_order_id'=>$order->gds_order_id,'st_received_by'=>$userId,'st_received_at'=>date('Y-m-d H:i:s'),'created_by'=>Session('userId'),'created_at'=>date('Y-m-d H:i:s'),'st_docket_no'=>$docketNo));
//                
//                $this->saveOrderComment($order->gds_order_id, $orderComment, '17028', $userId);
//              }
//                $this->apiUpdateCrateStatus($orderIds, "", 137007, "", $userId);
//            }            
//          }

            /* Stock recieve by docket no. with order id */
            if (is_array($orders) && count($orders) > 0 && !empty($docketNo)) {
                foreach ($orders as $orderId) {
//                    $this->_AssignOrderModel->updateTrackDetailByOrderId($orderId, array('rt_received_by' => $userId, 'rt_received_at' => date('Y-m-d H:i:s')));
                    $this->saveOrderComment($orderId, $orderComment, '17028', $userId);
                    $this->_AssignOrderModel->insertTrackHistoryForConfirmStock(array('gds_order_id' => $orderId, 'rt_received_by' => $userId, 'rt_received_at' => date('Y-m-d H:i:s'), 'created_by' => Session('userId'), 'created_at' => date('Y-m-d H:i:s'), 'rt_docket_no' => $docketNo));
                }
                $this->_orderModel->updateOrder($orders, array('order_transit_status' => '17028'));
                $this->apiUpdateCrateStatus($orders, "", 137007, "", $userId);
            }

            if (is_array($delivered_orders) && count($delivered_orders) > 0) {
                $this->apiUpdateCrateStatus($delivered_orders, "", 137001, "", $userId);
            }
            
            if(is_array($rahDeliveredOrders) && count($rahDeliveredOrders) > 0){
                foreach ($rahDeliveredOrders as $eachOrderId) {
                    $this->_AssignOrderModel->updateTrackDetailByOrderId($eachOrderId, array('rt_received_by' => $userId, 'rt_received_at' => date('Y-m-d H:i:s')));
                }
            }

            DB::commit();
            return Response::json(array('Status' => 200, 'Message' => 'Stock Received Successfully!'));
        } catch (Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('Status' => 404, 'Message' => 'Failed'));
        }
    }

    public function apiUpdateCrateStatus($orderIds, $statusId, $transactionStatus, $lpToken, $userId) {
        $crates = array();
        $le_wh = '';

        foreach($orderIds as $eachOrderId){
            $crateDetails = $this->_crateManagement->getOrderCrate($eachOrderId);
            if(!empty($crateDetails)){
                foreach ($crateDetails as $eachDetail) {
                    !empty ($eachDetail['le_wh_id'] != '') ? $le_wh = $eachDetail['le_wh_id'] : $le_wh = '';
                    $temp = array();
                    $temp['crate_code'] = $eachDetail['container_barcode'];
                    $temp['status'] = $statusId;
                    $temp['transaction_status'] = $transactionStatus;
                    array_push($crates, $temp);
                }
            }
        }
        
        if($lpToken != "" && $userId == ""){
            $token = $lpToken;
        }
        
        if($userId != "" && $lpToken == ""){
            $token = $this->_crateManagement->getLpToken($userId);
        }

        $callDataArr = array();
        $callDataArr["lp_token"] = $token;
        $callDataArr["le_wh_id"] = $le_wh;
        $callDataArr["crate_info"] = $crates;

        $finalData['cratestatuslist_params'] = json_encode($callDataArr);

        $HostUrl = $this->_crateManagement->getHostURL();
        $url = 'http://' . $HostUrl . '/cratemanagement/setcratestatus';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, sizeof($finalData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $finalData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
    }

    /**
     * saveOrderComment() method is used for insert order comment
     * @param  $orderId Number
     * @param $comment String
     * @param  $orderStatus Number
     * @param  $userId Number
     * @param  $type Number, defauly 17
     * @return Boolean
     */
    
    private function saveOrderComment($orderId, $comment, $orderStatus, $userId, $type=17) {
      try{
          $commentArr = array('entity_id'=>$orderId, 
                          'comment_type'=>$type,
                          'comment'=>$comment,
                          'commentby'=>$userId,
                          'created_by'=>$userId,
                          'order_status_id'=>$orderStatus,
                          'created_at'=>date('Y-m-d H:i:s'),
                          'comment_date'=>date('Y-m-d H:i:s')
                          );

        $this->_orderModel->saveComment($commentArr);
        return true;
      }
      catch(Exception $e) {
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
    }

    /**
     * setSession() method is used for set user id in session
     * @param  $userId Number
     * @return Null
     */
    
    private function setSession($userId) {
        if($userId) {
          Session::put('userId', $userId);
        }
    }    

    /**
     * verifyOrderStatus() method is used for get order status
     * @param  $orders Array
     * @return Array
     */
    
    private function verifyOrderStatus($orders) {
      try{
        $ordersArr = $this->_AssignOrderModel->getOrderInfo($orders, array('orders.order_status_id'));
        $response = array();
        if(is_array($ordersArr) && count($ordersArr) > 0) {
          foreach ($ordersArr as $order) {
            $response[$order->order_status_id] = $order->order_status_id;
          }
        }
        return $response;
      }
      catch(Exception $e) {
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }      
    }

     /**
     * stockTransferHubToDcAction() method is used to stock transfer from hub to dc
     * @param Null
     * @return JSON
     */
    
     public function stockTransferHubToDcAction() {
        DB::beginTransaction();
        try {
            $data = Input::all();
            $postData = json_decode($data['data'], true);
            $apiKey = isset($data['api_key']) ? $data['api_key'] : Config::get('dmapi.GDSAPIKey');
            $secretKey = isset($data['secret_key']) ? $data['secret_key'] : Config::get('dmapi.GDSAPISECRETKey');
            $deliveryId = isset($postData['delivery_id']) ? $postData['delivery_id'] : '';
            $deliveryMobile = isset($postData['delivery_mobile']) ? $postData['delivery_mobile'] : '';
            $driverName = isset($postData['driver_name']) ? $postData['driver_name'] : '';
            $driverMobile = isset($postData['driver_mobile']) ? $postData['driver_mobile'] : '';
            $orders = isset($postData['orders']) ? $postData['orders'] : '';
            $orderComment = 'Stock transfer from HUB to DC by app';
            $delivered_orders = isset($postData['delivered_orders']) ? $postData['delivered_orders'] : array();
            $rahDeliveredOrders = array_merge($orders, $delivered_orders);

            if ((count($orders) <= 0) && (count($delivered_orders) <= 0)) {
                return Response::json(array('Status' => 404, 'Message' => 'Both RAH & Delivered orders cannot be empty'));
            }
            /* Set userId in session */
            $userId = isset($postData['user_id']) ? (int) $postData['user_id'] : 0;
            $this->setSession($userId);
            /* Validate input data, API */
            if ($userId <= 0 || $deliveryId <= 0) {
                return Response::json(array('Status' => 404, 'Message' => 'Please verify User ID / Delivery Person ID.'));
            }
            $response = $this->_Dmapiv2Model->checkUserAccess($apiKey, $secretKey, 'stockTransferHubToDc');
            if (!$response) {
                return Response::json(array('Status' => 404, 'Message' => 'API Authentication failed.'));
            }
            if (isset($postData['vehicle_id']) && $postData['vehicle_id'] != '') {
                if (isset($postData['vehical_no']) && $postData['vehical_no'] != '') {
                    /* Stock recieve by docket no. with order id */
                    $docket_code = $this->_orderModel->getRefCode('TR');
                    $stockData = array('rt_del_ex_id' => $deliveryId,
                        'rt_del_mobile' => $deliveryMobile,
                        'rt_del_date' => date('Y-m-d H:i:s'),
                        'rt_vehicle_no' => $postData['vehical_no'],
                        'rt_driver_name' => $driverName,
                        'rt_driver_mobile' => $driverMobile,
                        'rt_docket_no' => $docket_code,
                        'rt_vehicle_id' => $postData['vehicle_id']);

                    if (is_array($orders) && count($orders) > 0) {
                        $orderStatusArr = $this->verifyOrderStatus($postData['orders']);
                        if (count($orderStatusArr) > 2) {
                            return Response::json(array('Status' => 404, 'Message' => 'Make sure order status should be same status.'));
                        }

                        if (count($orderStatusArr) > 0 && !in_array('17022', $orderStatusArr) && !in_array('17023', $orderStatusArr)) {
                            return Response::json(array('Status' => 404, 'Message' => 'Make sure order status should be returned / partial delivered.'));
                        }

                        $returnStatusArr = $this->_AssignOrderModel->getReturnStatusByOrderId($postData['orders']);
                        if (!in_array('57067', $returnStatusArr)) {
                            return Response::json(array('Status' => 404, 'Message' => 'Make sure return should approve at Hub.'));
                        }

                        $orderComment .= '# Vehicle No: ' . $postData['vehical_no'] . ', Driver Name: ' . $driverName . ',Driver Mobile: ' . $driverMobile;

                        foreach ($orders as $orderId) {
    //              $this->_AssignOrderModel->updateTrackDetailByOrderId($orderId, $stockData);
                            $this->_AssignOrderModel->insertTrackHistoryByOrderId($orderId, $stockData);
                            $this->saveOrderComment($orderId, $orderComment, '17027', $userId);
                        }

                        $this->_orderModel->updateOrder($orders, array('order_transit_status' => '17027'));

                        $this->apiUpdateCrateStatus($orders, "", 137003, "", $userId);
                    }

                    if (is_array($rahDeliveredOrders) && count($rahDeliveredOrders) > 0) {
                        foreach ($rahDeliveredOrders as $eachOrderId) {
                            $this->_AssignOrderModel->updateTrackDetailByOrderId($eachOrderId, $stockData);
                        }
                    }

                    if (is_array($delivered_orders) && count($delivered_orders) > 0) {
                        $this->apiUpdateCrateStatus($delivered_orders, 136001, 137003, "", $userId);
                    }

                    DB::commit();
                    return Response::json(array('Status' => 200, 'Message' => 'Stock Transit Successfully!'));
                } else {
                    return Response::json(array('Status' => 404, 'Message' => 'Please send Vehicle Number'));
                }
            } else {
                return Response::json(array('Status' => 404, 'Message' => 'Please send Vehicle Id'));
            }
        } catch (Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('Status' => 404, 'Message' => 'Failed'));
        }
    }

    /**
     * getDataByCurl() method is used for get data by curl
     * @param  $orderData JSON
     * @return Null
     */
    
    private function getDataByCurl($orderData) {

      $hostUrl = $this->order->getHostURL();                
      $url= 'http://'.$hostUrl.'/dmapi/v2/placeorder'; 

      $data = array();
      $data['api_key'] = Config::get('dmapi.GDSAPIKey');
      $data['secret_key'] = Config::get('dmapi.GDSAPISECRETKey');
      $data['orderdata'] = $orderData;
                
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch,CURLOPT_POST, sizeof($data));
      curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_TIMEOUT,10);
      $output = curl_exec($ch);
   
      $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
      curl_close($ch);
    }

        /**
     * getPendingCollectionDate() method is used to get pending collection dates for an user  
     * @param Null
     * @return JSON
     */
    
    public function getPendingCollectionDate() {

      try{

         $data = Input::all();
         //print_r($data);die;
                   
          $postData = json_decode($data['data'], true);

          /**
           * Set userId in session
           */
          $userId = isset($postData['user_id']) ? (int)$postData['user_id'] : 0;
          $this->setSession($userId);

          /**
           * Validate input data, API and Docket No.
           */

          if(!$userId) {
            return Response::json(array('Status' => 404, 'Message' => 'Invalid user id'));
          }
          $admin_token = $postData['admin_token'];
          $response = $this->_category->checkCustomerToken($admin_token);
          if(!$response) {
            return Response::json(array('Status' => 404, 'Message' => 'Authentication failed. Verify credentials'));
          }
          $masterObj = new MasterLookupModel();
          $masterdesc = $masterObj->getMasterLokup(78017);
          $allow_trip = isset($masterdesc->description)?$masterdesc->description:0;
          $collectedOn = $this->_AssignOrderModel->getPendingCollectionDate($userId);
          
            $masvalue = $masterObj->getMasterLokup(78019);
            $submitedbydo = isset($masvalue->description)?$masvalue->description:0;
            $submitdo = 1;
            if($submitedbydo==1){
                //57055-submitted by DO
                $submitdo = $this->_AssignOrderModel->getPendingCollectionHI($userId,[57055]);
            }
            $masvalue1 = $masterObj->getMasterLokup(78020);
            $remittedtoHI = isset($masvalue1->description)?$masvalue1->description:0;
            $remittedhi = 1;
            if($remittedtoHI==1){
                //57051-rimitted to Hub Incharge
                $remittedhi = $this->_AssignOrderModel->getPendingCollectionHI($userId,[57051]);
            }     
         return Response::json(array('status' => 'success','collected_on'=>$collectedOn,'allow_trip'=>$allow_trip,'hubpending'=>$submitdo,'remittedhi'=>$remittedhi));
      }
      catch(Exception $e) {
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        return Response::json(array('status' => 'failed', 'message' => "Internal server error"));
      }

    }


        /**
     * getVehiclesByUserId() method is used to get all vehicles based on user  
     * @param Null
     * @return JSON
     */
    
    public function getVehiclesByUserId() {

      try{

         $data = Input::all();
         //print_r($data);die;
                   
          $postData = json_decode($data['data'], true);

          /**
           * Set userId in session
           */
          $userId = isset($postData['user_id']) ? (int)$postData['user_id'] : 0;
          $this->setSession($userId);

          /**
           * Validate input data, API and Docket No.
           */

          if(!$userId) {
            return Response::json(array('Status' => 404, 'Message' => 'Invalid user id'));
          }
          $admin_token = $postData['admin_token'];
          $response = $this->_category->checkCustomerToken($admin_token);
          if(!$response) {
            return Response::json(array('Status' => 404, 'Message' => 'Authentication failed. Verify credentials'));
          }
          

          $Json = json_decode($this->_roleModel->getFilterData(6,$userId), 1);
          $Json = json_decode($Json['sbu'], 1);

          $resultArray = array('hub_vehicles'=>'','dc_vehicles'=>'');

          
          $Hubs_Assigned = array();
          $Dcs_Assigned = array();

            if(isset($Json['118001'])) {
            $Dcs_Assigned = explode(',',$Json['118001']);
          }

            if(isset($Json['118002'])) {
            $Hubs_Assigned = explode(',',$Json['118002']);
          }

          $resultArray['hub_vehicles'] = $this->_AssignOrderModel->getVehiclesByHubIds($Hubs_Assigned,'Hub');

          $resultArray['dc_vehicles'] = $this->_AssignOrderModel->getVehiclesByHubIds($Dcs_Assigned,'DC',$Hubs_Assigned);


         return Response::json(array('status' => 'success','data'=>$resultArray));
      }
      catch(Exception $e) {
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        return Response::json(array('status' => 'failed', 'message' => "Internal server error"));
      }

    }

    public function cancelOrderItem($orderId, $productsArr, $orderStatusId='17015', $itemStatus=false, $user='default') {
      DB::beginTransaction();

      try{
        /**
         * 17015- CANCELLED BY EBUTOR
         * 17009- CANCELLED BY CUSTOMER
         */
        if(is_array($productsArr) && count($productsArr) > 0) {
          $cancel_code = $this->_orderModel->getRefCode('SC');
          $gridData = array('gds_order_id'=>$orderId, 
                  'cancel_status_id'=>$orderStatusId, 
                  'cancel_code'=>$cancel_code);
          $cancelGridId = $this->_orderModel->cancelGrid($gridData,$user);

          $order = $this->_orderModel->getOrderStatusById($orderId);
        $le_wh_id = isset($order->le_wh_id) ? $order->le_wh_id : 0;

          if($cancelGridId) {
            $invLogs = array();
            $totCancelQty = 0;
            $totCancelAmt = 0;          
          foreach($productsArr as $product){
            $product_id = $product['product_id'];
            $qty = $product['qty'];
            $cancel_reason_id = isset($product['cancel_reason_id'])?$product['cancel_reason_id']:null;
            $priceArr = $this->getUnitPriceOfOdrItems($orderId, array($product_id));
            $unit_price = isset($priceArr[$product_id]['unitPrice']) ? $priceArr[$product_id]['unitPrice'] : 0;
            $total_price = ($unit_price * $product['qty']);

            $itemData = array('product_id'=>$product_id,
                    'qty'=>$qty,
                    'cancel_status_id'=>$orderStatusId,
                    'cancel_grid_id'=>$cancelGridId,
                    'cancel_reason_id' => $cancel_reason_id,
                    'unit_price'=>$unit_price, 
                    'total_price'=>$total_price);
            $this->_orderModel->cancelGridItem($itemData,$user);

            if($itemStatus) {
              $this->_orderModel->updateProductStatus($orderId, $product_id, $orderStatusId);
            }

            $totCancelQty = $totCancelQty + $qty;
            $totCancelAmt = $totCancelAmt + $total_price;
          }
                    
          $this->_Inventory->updateInventory($productsArr, $le_wh_id, 'substract', $cancel_code);

          $this->_cancelModel->updateCancelGrid($cancelGridId, array('cancel_value'=>$totCancelAmt, 'cancel_qty'=>$totCancelQty));

          // send sms to customer & field force         
            //$this->sendCancelSMS($orderId, $cancelGridId);

            // send email to customer, field force and logistic manager's teams
            //$this->sendCancelEmail($orderId);
          }

          DB::commit();
          return true;
        }
      }
      catch(Exception $e) {
        DB::rollback();
          Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
    }

//getAssignedVerificationList which are in RTD state and not verified

    public function getAssignedVerificationList(){
        try {
            $data = isset($_POST['data'])?$_POST['data']:'';
            $data = json_decode($data,true);

            if(empty($data) or $data == [] or $data == null)
                return ['status' => "failed", 'message' =>"Invalid Input", 'data' =>[]];

            $token = 0;
            if(isset($data['user_token']) and !empty($data['user_token']))
                $token = $this->_category->checkCustomerToken($data['user_token']);
            if($token <= 0)
                return ['status' => "failed", 'message' =>"Invalid Customer Token", 'data' =>[]];

            if(!isset($data['from_time']) or empty($data['from_time']))
                return ['status' => "failed", 'message' =>"from time  is Mandatory", 'data' =>[]];

            if(!isset($data['to_time']) or empty($data['to_time']))
                return ['status' => "failed", 'message' =>"to Time is Mandatory", 'data' =>[]];

            $result=$this->_AssignOrderModel->getAssignedVerificationList($data);
            return ['status' =>$result["status"], 'message' =>$result["message"], 'data' =>$result["data"]];

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return ['status' => "failed", "message" => "Server Error", "data" => []];
        }
    }


  /**
   * [cancelItemAction description]
   * @return [type] [description]
   */

  

  public function getUnitPriceOfOdrItems($odrID, $itemArray){
    //Getting Order Item details from 'gds_order_products' table...
    $odrProdDetails = json_decode(json_encode($this->_orderModel->getProductByOrderId($odrID, $itemArray)), true);
    $odrProd = array();

    foreach($odrProdDetails as $odrProdData){
      //Calculating UNIT PRICE WITH TAXES for each item
      $odrProd[$odrProdData['product_id']]['unitPrice'] = ($odrProdData['qty']>0) ? ($odrProdData['total']/$odrProdData['qty']):0.00;
    }
    return $odrProd;
  }

    /**
    * [saveTemporaryVehicles description]
    * @return [type] [description]
    */
    public function saveTemporaryVehicles(){
        try {
            
            $data = isset($_POST['data'])?$_POST['data']:'';

            if(empty($data) or $data == [] or $data == null)
                return ['status' => "failed", 'message' =>"Invalid Input", 'data' =>[]];

            if(!isset($data['user_id']) or empty($data['user_id']))
                return ['status' => "failed", 'message' =>"Invalid User ID", 'data' =>[]];
                
            if(!isset($data['date']) or empty($data['date']))
                return ['status' => "failed", 'message' =>"Invalid Date", 'data' =>[]];

            if(!isset($data['temp_vehicle']) or empty($data['temp_vehicle']))
                return ['status' => "failed", 'message' =>"Invalid Data", 'data' =>[]];

            $token = $this->categoryModel->checkCustomerToken(isset($data['token'])?$data['token']:'');
            if($token <= 0)
                return ['status' => "failed", 'message' =>"Invalid Customer Token", 'data' =>[]];

            $result=$this->_AssignOrderModel->saveTemporaryVehicleModal($data);

            return ['status' => "success", 'message' =>"Temporary Vehicle Saved!", 'data' =>$result];

        } catch (Exception $e) {
            
        }
    }

    /**
    * -> To Get all the Active Checkers
    * @var [checkerId] - optional
    * @return {[user_id, user_name]}
    */
    public function getCheckersList(){
        try {
            $data = isset($_POST['data'])?$_POST['data']:'';
            $data = json_decode($data,true);



            if(empty($data) or $data == [] or $data == null)
                return ['status' => "failed", 'message' =>"Invalid Input", 'data' =>[]];

            $token = 0;
            if(isset($data['user_token']) and !empty($data['user_token']))
                $token = $this->_category->checkCustomerToken($data['user_token']);
            if($token <= 0)
                return ['status' => "failed", 'message' =>"Invalid Customer Token", 'data' =>[]];
              $data = DB::select(DB::Raw("select user_id from users where lp_token ='". $data['user_token']."'"));
              $data['user_id'] = $data[0]->user_id;
            $result=$this->_AssignOrderModel->getCheckersListModal($data);
            return ['status' =>$result["status"], 'message' =>$result["message"], 'data' =>$result["data"]];

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return ['status' => "failed", "message" => "Server Error", "data" => []];
        }
    }


    //get Orders List which are in RTD state


     public function getRtdOrdersList(){
        try {
            $data = isset($_POST['data'])?$_POST['data']:'';
            $data = json_decode($data,true);



            if(empty($data) or $data == [] or $data == null)
                return ['status' => "failed", 'message' =>"Invalid Input", 'data' =>[]];

            $token = 0;
            if(isset($data['user_token']) and !empty($data['user_token']))
                $token = $this->_category->checkCustomerToken($data['user_token']);
            if($token <= 0)
                return ['status' => "failed", 'message' =>"Invalid Customer Token", 'data' =>[]];

            if(!isset($data['from_time']) or empty($data['from_time']))
                return ['status' => "failed", 'message' =>"from time  is Mandatory", 'data' =>[]];

            if(!isset($data['to_time']) or empty($data['to_time']))
                return ['status' => "failed", 'message' =>"to Time is Mandatory", 'data' =>[]];

            $result=$this->_AssignOrderModel->getRtdOrdersList($data);
            return ['status' =>$result["status"], 'message' =>$result["message"], 'data' =>$result["data"]];

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return ['status' => "failed", "message" => "Server Error", "data" => []];
        }
    }


    /**
    * -> To Assign all the Pickers
    * @var [checkerId] - optional
    * @var [orders_arr] - optional
    */
    public function assignOrdersForChecker(){
        try {
            $data = isset($_POST['data'])?$_POST['data']:'';

            $data = json_decode($data,true);


            if(empty($data) or $data == [] or $data == null)
                return ['status' => "failed", 'message' =>"Invalid Input", 'data' =>[]];

            if(!isset($data['orders']) or empty($data['orders']))
                return ['status' => "failed", 'message' =>"No Orders Specified", 'data' =>[]];

            if(!isset($data['checker_id']) or empty($data['checker_id']))
                return ['status' => "failed", 'message' =>"Checker Id is Mandatory", 'data' =>[]];

            $token = 0;
            if(isset($data['user_token']) and !empty($data['user_token']))
                $token = $this->_category->checkCustomerToken($data['user_token']);
            if($token <= 0)
                return ['status' => "failed", 'message' =>"Invalid Customer Token", 'data' =>[]];

            $result=$this->_AssignOrderModel->assignOrdersForCheckerModal($data['orders'],$data['checker_id'],$data['date']);
            return ['status' =>$result["status"], 'message' =>$result["message"], 'data' =>$result["data"]];

        } catch (Exception $e) {
            return ['status' => "failed", "message" => "Server Error", "data" => []];
        }
    }

    public function getPendingtVerificationList(){
     

              // print_r($_POST);
              $data = isset($_POST['data'])?$_POST['data']:'';
              $data = json_decode($data,true);

              if(empty($data) or $data == [] or $data == null)
              return ['status' => "failed", 'message' =>"Invalid Input", 'data' =>[]];
              
              if(!isset($data['checker_id']) or empty($data['checker_id']))
                return ['status' => "failed", 'message' =>"Checker Id is Mandatory", 'data' =>[]];

              if(!isset($data['from_date']) or empty($data['from_date']))
                return ['status' => "failed", 'message' =>"From Date is Mandatory", 'data' =>[]];

              if(!isset($data['to_date']) or empty($data['to_date']))
                return ['status' => "failed", 'message' =>"To Date is Mandatory", 'data' =>[]];

               $token = 0;
              if(isset($data['user_token']) and !empty($data['user_token']))
                  $token = $this->_category->checkCustomerToken($data['user_token']);
              if($token <= 0)
                  return ['status' => "failed", 'message' =>"Invalid Customer Token", 'data' =>[]];

              $data=$this->_AssignOrderModel->getOrdersNotYetVerifiedListData($data['checker_id'],$data['from_date'],$data['to_date']);

              //print_r($data);exit;
              $data_Object=$data;

              $array=json_decode($data,true);
              if(!empty($array))
              return json_encode(['status' => "200", "message" => "Success", "data" => $array]);

              return ['status' => "200", "message" => "No Data Found", "data" => []];



    }

    /**
    *Getting Checkers Count
    * @return [Total Line Items] [Verified Count] [Pending Count]
    */
    public function getCheckersCount(){
        try {
            
              $data = isset($_POST['data'])?$_POST['data']:'';
              $data = json_decode($data,true);

              if(empty($data) or $data == [] or $data == null)
                return ['status' => "failed", 'message' =>"Invalid Input", 'data' =>[]];
                $token = 0;
              if(isset($data['user_token']) and !empty($data['user_token']))
                $token = $this->_category->checkCustomerToken($data['user_token']);

              if($token <= 0)
                return ['status' => "failed", 'message' =>"Invalid Customer Token", 'data' =>[]];  

              if(!isset($data['checker_id']) or $data['checker_id']=="")
                return ['status' => "failed", 'message' =>"Invalid Checker ID", 'data' =>[]];
              if(!isset($data['from_date']) )
                return ['status' => "failed", 'message' =>"Invalid From Date", 'data' =>[]];
              if(!isset($data['to_date']) )
                return ['status' => "failed", 'message' =>"Invalid To Date", 'data' =>[]];
               if (($data['from_date'] =="" || $data['to_date'] =="" )and ($data['checker_id'] !=0 and $data['checker_id']=="" )) {
                  return ['status' => "failed", 'message' =>"Invalid Dates", 'data' =>[]];
                }
  
              $result=$this->_AssignOrderModel->getCheckersCountModel($data);
                return json_encode($result);

            } catch (Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
              return ['status' => "failed", "message" => "Server Error", "data" => []];
        }
  }
  public function userAuthorization(){

      $data = isset($_POST['data'])?$_POST['data']:'';
      $data = json_decode($data,true);
      $result=$this->_AssignOrderModel->userAuthentication($data);
      return $result;
  }

    
}