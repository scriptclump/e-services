<?php

/*
 * Filename: OrdersController.php
 * Description: This file is used for manage sales orders
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 23 June 2016
 * Modified date: 23 June 2016
 */

/*
 * OrdersController is used to manage orders
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\Orders\Controllers;


use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Log;
use DB;
use Auth;
use Response;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Input;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\Shipment;
use App\Modules\Orders\Models\Refund;
use App\Modules\Orders\Models\ReturnModel;
use App\Modules\Orders\Controllers\OrdersController;

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;
use App\Modules\Orders\Models\CancelModel;
class CancelController extends BaseController {

	protected $_orderModel;
	protected $_masterLookup;
	protected $_commentTypeArr;
	protected $_invoiceModel;
	protected $_shipmentModel;
	protected $_roleRepo;
	protected $_sms;
	protected $_refund;
	protected $_leModel;
	protected $_filterStatus;
	protected $_returnModel;
    	protected $_OrdersController;
    protected $_cancelOrders;

    public function __construct() {
	   $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });
		$this->_orderModel = new OrderModel();
		$this->_masterLookup = new MasterLookup();
		$this->_invoiceModel = new Invoice();
		$this->_shipmentModel = new Shipment();
		$this->_roleRepo = new RoleRepo();
		$this->_sms = new dmapiOrders();
		$this->_refund = new Refund();
		$this->_leModel = new LegalEntity();
		$this->_returnModel = new ReturnModel();
        	$this->_OrdersController = new OrdersController();
            $this->_cancelOrders = new CancelModel();
	       
    }

    /*
     * invoicesAjaxAction() method is used to fetch all cancellations
     * @param $id Numbner
     * @return Array
     */

    public function orderCancelList($id) {
        try{
        $cancelArr = $this->_orderModel->getAllCancellations($id);
        $totalCancel = (int)$this->_orderModel->getAllCancellations($id, 1);
        $commentStatusArr = $this->_masterLookup->getAllOrderStatus();
        $dataArr = array();

        if(is_array($cancelArr)) {
            foreach($cancelArr as $cancel) {
                $cancelStatus = isset($commentStatusArr[$cancel->cancel_status_id]) ? $commentStatusArr[$cancel->cancel_status_id] : '';
                $dataArr[] = array('cancelId'=>$cancel->cancel_code,
                                    'orderId'=>$cancel->order_code,
                                    'orderDate'=> date("d-m-Y H:i:s", strtotime($cancel->order_date)),
                                    'cancelDate'=>date("d-m-Y H:i:s", strtotime($cancel->created_at)),
                                    'qtyCancelled'=>(int)$cancel->qty,
                                    'cancelledAmt'=>(float)$cancel->total,
                                    'status'=>$cancelStatus,
                                    'Actions'=>'<a title="View" href="/salesorders/canceldetail/'.((int)$cancel->cancel_grid_id).'"><i class="fa fa-eye"></i></a>'
                                    );

            }
        }

        return Response::json(array('data'=>$dataArr, 'totalCancel'=>$totalCancel));
            }
            catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    public function cancelDetailAction($cancelId) {

        try{
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD008');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }

            $canceledProductArr = $this->_orderModel->getCancelledProductById($cancelId);
            $orderId = isset($canceledProductArr[0]->gds_order_id) ? $canceledProductArr[0]->gds_order_id : 0;

            if($orderId<=0) {
                Redirect::to('/salesorders/index')->send();
            }

            $orders = $this->_orderModel->getOrderDetailById($orderId);
            if(count($orders)==0) {
                Redirect::to('/salesorders/index')->send();
            }
            $allProductsArr = $this->_orderModel->getProductByOrderId($orderId);

            //$commentStatusArr = $this->_masterLookup->getStatusByPatentName('ORDER_STATUS');
            $cacelReasonArr = $this->_masterLookup->getAllOrderStatus('Cancel Reasons');
            $productArr = array();
            foreach($allProductsArr as $product) {
                $productArr[$product->product_id] = $product;
            }

            //$commentArr = $this->_orderModel->getOrderCommentById($orderId, 'Cancel Status');
            $paymentModesArr    =   $this->_masterLookup->getMasterLookupNamesByCategoryId(22);
            //$allUsers       =   $this->_orderModel->getUsersByRoleName(array('Field Force Associate','Field Force Manager'));
            
            return view('Orders::cancelDetail')
                    ->with('orderdata',$orders)
                    ->with('products',$canceledProductArr)
                    ->with('productArr', $productArr)
                    //->with('commentArr', $commentArr)
                    ->with('cacelReasonArr', $cacelReasonArr)
                    ->with('tabHeading', 'Cancel Details')
                    ->with('actionName', 'cancelDetail')
                    //->with('allUsers', $allUsers)
                    ->with('paymentModesArr', $paymentModesArr);
                    //->with('commentStatusArr', $commentStatusArr);
        }
        catch(Execption $e) {
        }
    }

    public function addOrderCancelation($orderId) {

        try{
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD007');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }
       
            /**
             *
             */

            parent::Breadcrumbs(array('Home' => '/', 'Orders' => '/salesorders/index','Order Details'=>'/salesorders/detail/'.$orderId));

            /**
             * [$orders description]
             * @var [type]
             */

            $orders = $this->_orderModel->getOrderDetailById($orderId);
            if(count($orders)==0) {
                Redirect::to('/salesorders/index')->send();
                die();
            }
            $cacelReasonArr = $this->_masterLookup->getAllOrderStatus('Cancel Reasons');

            /**
             * verify cancel
             */

            $finalProductArr = array();
            $commentArr = array();
            $commentStatusArr = array();
            $notifyMessage = '';

            if($this->_OrdersController->verifyCancel($orders->order_status_id)) {

                $itemShippedArr = $this->_orderModel->getShipmentQtyByOrderId($orderId);
                $itemInvoicedArr = $this->_orderModel->getItemInvoicedQtyByOrderId($orderId);

                $productArr = $this->_orderModel->getProductByOrderId($orderId);

                $commentStatusArr = $this->_masterLookup->getStatusByPatentName('ORDER_STATUS', array('17001', '17002', '17013', '17014', '17016'));
                $commentArr = $this->_orderModel->getOrderCommentById($orderId, 'Cancel Status');

                foreach ($productArr as $product) {

                    $canceledQty = $this->_orderModel->getCancelledProductqty($orderId,$product->product_id);


                    $shippedQty = isset($itemShippedArr[$product->product_id]) ? (int)$itemShippedArr[$product->product_id] : 0;

                    $invoicedQty = isset($itemInvoicedArr[$product->product_id]) ? (int)$itemInvoicedArr[$product->product_id] : 0;

                    $availQty = ((int)$product->qty - ($canceledQty + $shippedQty));

                    if((int)$availQty > 0 && (int)$product->qty >= (int)$availQty) {

                        $finalProductArr[] = (object)array('product_id'=>$product->product_id,
                                                            'sku'=>$product->sku,
                                                            'seller_sku'=>$product->seller_sku,
                                                            'pname'=>$product->pname,
                                                            'qty'=>$product->qty,
                                                            'mrp'=>$product->mrp,
                                                            'price'=>$product->unitPrice,
                                                            'cancelled_qty'=>$canceledQty,
                                                            'avail_qty'=>$availQty,
                                                            'shippedQty'=>$shippedQty,
                                                            'invoicedQty'=>$invoicedQty
                                                            );

                    }
                }

                if(empty($notifyMessage) && count($finalProductArr) == 0) {
                    $notifyMessage = $this->_OrdersController->getMessage('cancelNRF');
                }
            }
            else {
                $notifyMessage = $this->_OrdersController->getMessage($orders->order_status_id);
                if(empty($notifyMessage) && count($finalProductArr) == 0) {
                    $notifyMessage = $this->_OrdersController->getMessage('cancelNRF');
                }
            }

            return view('Orders::cancellation')
                    ->with('orderdata',$orders)
                    ->with('commentArr', $commentArr)
                    ->with('tabHeading', 'Cancel Order')
                    ->with('actionName', 'createCancellation')
                    ->with('notifyMessage', $notifyMessage)
                    ->with('productArr', $finalProductArr)
                    ->with('commentStatusArr', $commentStatusArr)
                    ->with('cacelReasonArr', $cacelReasonArr);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function cancelItemAction() {
        DB::beginTransaction();
        try{
            $postData = Input::all();
            //echo "<pre>"; print_r($postData);die;

            $orderId = isset($postData['gds_order_id']) ? $postData['gds_order_id'] : 0;
            $order_comment = isset($postData['order_comment']) ? $postData['order_comment'] : 'Cancel order from web';
            $errorMsg = array();
            $finalCancelItemArr = array();
            $errorAvailQty = array();
            $cancelPrdArr = array();

            /*
             * Validate data
             */
            $orderStatus = $this->_orderModel->getOrderDetailById($orderId);
            if(!$orderId) {
                $errorMsg[] = Lang::get('salesorders.errorInputData');
            }
            else if(isset($orderStatus->order_status_id) && in_array($orderStatus->order_status_id,array('17021','17009','17015','17022','17007'))){
                $errorMsg[] = Lang::get('salesorders.errorInputData');
            }
            else if(is_array($postData['orderItems'])) {

                $cancelledArr = $this->_orderModel->getCancelledQtyByOrderId($orderId);
                //print_r($cancelledArr);die;
                $itemStatus = array();

                foreach($postData['orderItems'] as $productId) {

                    $getproductstatusByOrderId = $this->_orderModel->getProductByOrderId($orderId,array($productId));

                    $getcancelqtyByProduct=$this->_orderModel->getCancelledProductqty($orderId,$productId);
                    $totalcancelqtyforproduct=$postData['available_qty'][$productId]+$getcancelqtyByProduct;
                    $cancel_reason_id = isset($postData['cancelReason'][$productId]) ? $postData['cancelReason'][$productId] : 0;
                    if(isset($cancelledArr[$productId]) && $cancelledArr[$productId] > 0 && ($cancelledArr[$productId] >= $postData['item_qty'][$productId])) {
                        $errorAvailQty[] = $postData['item_sku'][$productId];
                    }

                    if(($postData['available_qty'][$productId] > $postData['item_qty'][$productId])) {
                        $errorAvailQty[] = $postData['item_sku'][$productId];
                    }

                    if($postData['available_qty'][$productId] > 0 && ($postData['available_qty'][$productId] <= $postData['item_qty'][$productId]) && isset($getproductstatusByOrderId[0]->order_status) && !in_array($getproductstatusByOrderId[0]->order_status, array('17021','17015','17009','17022','17007')) && $totalcancelqtyforproduct<=$postData['item_qty'][$productId]) {
                        $cancelPrdArr[] = array('product_id'=>$productId, 'qty'=>$postData['available_qty'][$productId], 'cancel_reason_id'=>$cancel_reason_id);
                    }else{
                        $errorAvailQty[] = $postData['item_sku'][$productId];
                    }
                    
                }

                if(count($errorAvailQty)) {
                  
                    $errorMsg[] = Lang::get('salesorders.errorCancelItem');
                }
            }

            $totOrderedQty = $this->_orderModel->getOrderedQtyByOrderId($orderId);
            $totCancelledQty = $this->_orderModel->getCancelledTotalQtyByOrderId($orderId);
            if($totOrderedQty == $totCancelledQty) {
                $errorMsg[] = 'All item has been cancel. Please verify.';
            }
          
            if(count($errorMsg)) {
                return Response::json(array('status' => 400, 'message' => $errorMsg));
            }
            else if(count($cancelPrdArr)){
                $can_sts = $this->_OrdersController->cancelOrderItem($orderId, $cancelPrdArr, $postData['cancel_status'], false);
              
                if($can_sts == true){

                    foreach ($cancelPrdArr as $prd) {
                        $productId = $prd['product_id'];
                        $ordQty = $postData['item_qty'][$productId];
                        $canledQty = isset($cancelledArr[$productId]) ? $cancelledArr[$productId] : 0;
                        $totCanQty = ($canledQty + $prd['qty']);
                        $itemStatus = ($ordQty == $totCanQty ? $postData['cancel_status'] : '17013');
                        $this->_orderModel->updateProductStatus($orderId, $productId, $itemStatus);
                    }

                    $totOrderedQty = $this->_orderModel->getOrderedQtyByOrderId($orderId);
                    $totCancelledQty = $this->_orderModel->getCancelledTotalQtyByOrderId($orderId);
                    if($totOrderedQty == $totCancelledQty) {
                        $this->_orderModel->updateOrderStatusById($orderId, $postData['cancel_status']);
                        $this->_OrdersController->revertCashbackFromOrder($orderId,$postData['cancel_status'],"Order Cancelled!");
                    }

                    /*
                     * Save comment
                     */
                    $this->_OrdersController->saveComment($orderId, 'Order Status', array('comment'=>$order_comment, 'order_status_id'=>$postData['cancel_status']));
                }else{
                    return Response::json(array('status' => 400, 'message' => Lang::get('salesorders.errorInputData')));
                }

                DB::commit();
                return Response::json(array('status' => 200, 'message' => Lang::get('salesorders.successCancelItem')));
            }
            else {
                DB::rollback();
                return Response::json(array('status' => 400, 'message' => Lang::get('salesorders.errorInputData')));
            }

        }
        catch(ErrorException $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }

    public function orderRollBack(Request $request){
        $orderId = $request->input();
        $gdsorderId = $orderId['order_id'];
        $gdsorderId = $this->_cancelOrders->rollBackOrders($gdsorderId,1);
        $msg_data = json_decode(json_encode($gdsorderId), True);
        
        return (isset($msg_data[0]['Msg']))?$msg_data[0]['Msg']:'';        
    }
}
