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
use App\Modules\Roles\Models\Role;

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;
use App\Modules\Orders\Controllers\OrdersController;


class ShipmentController extends BaseController {

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
    protected $_roleModel;

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
        $this->_roleModel = new Role();
	       
    }

    public function shipmentAjaxAction($id) {
        try{
        $shipmentsArr = $this->_orderModel->getAllShipments($id);
        $totalShipments = (int)$this->_orderModel->getAllShipments($id, 1);
        $shipmentStatusArr = $this->_orderModel->getOrderStatus('Order Status');

        $dataArr = array();
        #print_r($shipmentsArr);
        if(is_array($shipmentsArr)) {
            foreach($shipmentsArr as $shipment) {
                $shipTrackDetail = $this->_orderModel->getTrackingDetailByShipmentId($shipment->gds_ship_grid_id);
                $shippedFname = isset($shipTrackDetail->ship_fname) ? $shipTrackDetail->ship_fname : '';
                $shippedLname = isset($shipTrackDetail->ship_lname) ? $shipTrackDetail->ship_lname : '';

                $dataArr[] = array('shipmentId'=>$shipment->ship_code,
                                    'orderId'=>$shipment->order_code,
                                    'orderDate'=>date("d-m-Y H:i:s", strtotime($shipment->order_date)),
                                    'shipmentDate'=>date("d-m-Y H:i:s", strtotime($shipment->created_at)),
                                    'shippedTo'=>$shipment->shop_name,
                                    'shippedQty'=>$shipment->totShippedQty,
                                    'Status'=>(isset($shipmentStatusArr[$shipment->status_id]) ? $shipmentStatusArr[$shipment->status_id] : ''),
                                    'shipmentActions'=>'<a title="View" href="/salesorders/shipmentdetail/'.$shipment->gds_ship_grid_id.'"><i class="fa fa-eye"></i></a>'
                                    );

            }
        }
        return Response::json(array('data'=>$dataArr, 'totalShipments'=>$totalShipments));
            }
            catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    public function shipmentDetailAction($shipmentId) {

        try{
            
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD004');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }
            $shipmentArr = $this->_orderModel->getShipmentDetailById($shipmentId);
            $status_id = isset($shipmentArr[0]->status_id) ? $shipmentArr[0]->status_id : '17002';
            if(!isset($shipmentArr) || count($shipmentArr)==0) {
                Redirect::to('/salesorders/index')->send();
            }
            $orderId=$shipmentArr[0]->gds_order_id;
            $statusMatrixArr = $this->_masterLookup->getStatusMatrixByValue($status_id, 53);
            $allProductsArr = $this->_orderModel->getProductByOrderId($orderId);

            $shipmentTrackArr = $this->_orderModel->getTrackingDetailByShipmentId($shipmentId);
            $carrierDetailArr = $this->_orderModel->getCouriers();
            $commentStatusArr = $this->_orderModel->getOrderStatus('Order Status');
            $commentArr = $this->_orderModel->getOrderCommentById($orderId, 'SHIPMENT_STATUS');
            #print_r($commentStatusArr);die;
            $productArr = array();
            foreach($allProductsArr as $product) {
                $productArr[$product->product_id] = $product;
            }

            $carriersArr = array();
            foreach($carrierDetailArr as $carrier) {
                $carriersArr[$carrier->carrier_id] = $carrier->carrier;
            }

            $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
            $billingAndShipping = $this->_OrdersController->convertBillingAndShippingAddress($billingAndShippingArr);
            $orders = $this->_orderModel->getOrderDetailById($orderId);
            $paymentModesArr    =   $this->_masterLookup->getMasterLookupNamesByCategoryId(22);
            //$allUsers       =   $this->_orderModel->getUsersByRoleName(array('Field Force Associate','Field Force Manager'));


            return view('Orders::shipmentDetail')->with('shipmentProductArr', $shipmentArr)
                                                ->with('carriersArr', $carriersArr)
                                                ->with('commentStatusArr', $commentStatusArr)
                                                ->with('statusMatrixArr', $statusMatrixArr)
                                                ->with('commentArr', $commentArr)
                                                ->with('productArr', $productArr)
                                                ->with('actionName', 'shipmentDetail')
                                                ->with('tabHeading', 'Shipment Details')
                                                ->with('orderdata', $orders)
                                                ->with('billing', (isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] : ''))
                                                ->with('shipping', (isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] : ''))
                                                //->with('allUsers', $allUsers)
                                                ->with('paymentModesArr', $paymentModesArr)
                                                ->with('shipmentTrackArr', $shipmentTrackArr);
        }
        catch(Exception $e) {
        }
    }

    public function createShipment($orderId, $productsArr, $shipStatus) {

        DB::beginTransaction();
        try{

            $shipGridId = $this->_orderModel->saveShipmentGrid(array('gds_order_id'=>$orderId, 'status_id'=>$shipStatus));
            $prdStatusId = $shipStatus;
            if($shipGridId) {
                
                foreach($productsArr as $product) {
                    $productId = $product['product_id'];
                    $orderedQty = $product['orderedQty'];
                    $shipQty = $product['shipQty'];
                    $pendingQty = (int)($orderedQty - $shipQty);

                    if($pendingQty) {
                        $prdStatusId = ($orderedQty == $pendingQty) ? $shipStatus : '17013';
                    }

                    $data = array('gds_ship_grid_id'=>$shipGridId, 
                                    'product_id'=>$productId,
                                    'qty'=>$shipQty, 
                                    'status_id'=>$shipStatus
                                );
                    //print_r($data);
                    $this->_orderModel->saveShipmentGridItem($data);

                    $this->_orderModel->updateProductStatus($orderId, $productId, $prdStatusId);
                }
            }

            DB::commit();
            return $shipGridId;
        }
        catch(Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }  
    }

    public function createShipmentAction() {
        $postData = Input::all();
        //echo "<pre>";print_r($postData);die;
        $orderId = $postData['gds_order_id'];
        $shipmentStatus = $postData['shipment_status'];
        $shipment_comment = $postData['shipment_comment'];
        $orderInfo = $this->_orderModel->getOrderInfoById($orderId, array('order_status_id'));

        $orderStatus = isset($orderInfo->order_status_id) ? $orderInfo->order_status_id : '';

        $productsArr = array();
        if(isset($postData['available_qty']) && is_array($postData['available_qty'])) {
            foreach ($postData['available_qty'] as $productId => $shipQty) {
                $item_qty = $postData['item_qty'][$productId];
                $comment = $postData['comments'][$productId];
                if($shipQty) {
                    $productsArr[] = array('product_id'=>$productId, 'shipQty'=>$shipQty, 'orderedQty'=>$item_qty, 'comment'=>$comment);    
                }                
            }
        }
        //print_r($productsArr);die;
        //
        $isValid = $this->validateData($orderId, $productsArr);
        //print_r($isValid);die;

        if($orderStatus == '17020' || $orderStatus == '17021' || $orderStatus == '17013') {

            $shipGridId = $this->createShipment($orderId, $productsArr, $shipmentStatus);
            if($shipGridId) {
                if($shipmentStatus == '17006') {
                    // add tracking details
                    $this->_OrdersController->generateInvoiceByShipmentId($shipGridId);
                }
                else {
                    if($orderStatus == '17020') {
                         $orderedQty = $this->_orderModel->getOrderedQtyByOrderId($orderId);
                        $shippedArr = $this->_orderModel->getShippedQtyByOrderId($orderId);
                        $shippedQty = isset($shippedArr[0]->totShippedQty) ? (int)$shippedArr[0]->totShippedQty : 0;

                        $orderStatusId = ($orderedQty == $shippedQty) ? '17005' : '17013';
                        $this->_orderModel->updateOrderStatusById($orderId, $orderStatusId);
                    }                   
                   
                    $this->_OrdersController->saveComment($orderId, 'SHIPMENT_STATUS', array('comment' => $shipment_comment, 'order_status_id' => $shipmentStatus));
                }
            }
            return Response::json(array('status' => 200, 'message' => Lang::get('salesorders.createdShipment')));
        }
        
        die;
    }

    public function validateData($orderId, $productsArr) {
        $errorInvMsg = array();
        $errorPrdMsg = array();

        $orderInfo = $this->_orderModel->getOrderInfoById($orderId, ['le_wh_id','hub_id']);

        if(!is_object($orderInfo)) {
            return 'Please enter valid order id.'; 
        }

        $le_wh_id = isset($orderInfo->le_wh_id) ? $orderInfo->le_wh_id : 0;
        $hub_id = isset($orderInfo->hub_id) ? $orderInfo->hub_id : 0;
        
        if(is_array($productsArr) && count($productsArr) > 0) {
            foreach ($productsArr as $product) {
                $product_id = isset($product['product_id']) ? (int)$product['product_id'] : 0;

                $prdInfo = $this->_orderModel->getProductByOrderIdProductId($orderId,$product_id);

                $product_id = isset($prdInfo->product_id) ? (int)$prdInfo->product_id : 0;
                $orderedQty = isset($prdInfo->qty) ? (int)$prdInfo->qty : 0;
                
                $invArr = $this->_orderModel->getInventory($product_id, $le_wh_id);
                if($hub_id==10695){
                    $soh = isset($invArr->dit_qty) ? (int)$invArr->dit_qty : 0;
                } else {
                    $soh = isset($invArr->soh) ? (int)$invArr->soh : 0;
                }

                if(!$product_id) {
                    $errorPrdMsg[] = $prdInfo->sku;
                }
                if($soh > 0 && ($orderedQty > $soh)) {
                    $errorInvMsg[] = $prdInfo->sku;
                }
                else if(!$soh){
                    $errorInvMsg[] = $prdInfo->sku;
                }
            }
        }
        
        if(is_array($errorPrdMsg) && count($errorPrdMsg) > 0) {
            $msg = 'Product id '.implode($errorPrdMsg, ', ').' is not valid.';
            return $msg; 
        }

        if(is_array($errorInvMsg) && count($errorInvMsg) > 0) {
            $msg = Lang::get('salesorders.alertInventory');
            $msg = str_replace('{SKU}', implode(', ', $errorInvMsg), $msg);
            return $msg;
        }
    }

    private function verifyShipment($statusCode) {
        $statusArr = array('17002', '17009', '17013', '17015', '17020', '17021');
        if(in_array($statusCode, $statusArr)) {
            return true;
        }
        else {
            return false;
        }
    }

    /*
     * addShipmentAction() method is used to create shipment
     * @param $orderId Numeric
     * @return String
     *
     * Order Status
     *
     * CONFIRMED - 17002
     *
     */

    public function addShipmentAction($orderId) {

        try{
            $orders = $this->_orderModel->getOrderDetailById($orderId);
            if(empty($orders) || count($orders)==0) {
                Redirect::to('/salesorders/index')->send();
            }

            $notifyMessage = '';
            $couriers = array();
            $shipmentProductArr = array();
            $commentStatusArr = array();
            $allShipmentStatusArr = array();
            $statusMatrixArr = array();
            $commentArr = array();
            $shipmentArr = array();
            
            if($this->verifyShipment($orders->order_status_id)) {
                $statusMatrixArr = $this->_masterLookup->getStatusMatrixByValue('17020', 53);
                $products = $this->_orderModel->getProductByOrderId($orderId);
                $cancelArr = $this->_orderModel->getCancelledQtyByOrderId($orderId);
                $shipArr = $this->_orderModel->getShipmentQtyByOrderId($orderId);
                $couriers = $this->_orderModel->getCouriers();
                
                $shipmentArr = array();

                foreach ($products as $product) {
                    $orderedQty = $product->qty;
                    $cancelQty = isset($cancelArr[$product->product_id]) ? $cancelArr[$product->product_id] : 0;
                    $shipQty = isset($shipArr[$product->product_id]) ? $shipArr[$product->product_id] : 0;
                    $pendingQty = $orderedQty - ($cancelQty + $shipQty);
                    if($pendingQty) {
                        $shipmentArr[] = array('product_id'=>$product->product_id, 
                                    'sku'=>$product->sku, 'pname'=>$product->pname,
                                    'shipQty'=>$pendingQty, 'shipedQty'=>$shipQty,
                                    'cancelQty'=>$cancelQty, 'mrp'=>$product->mrp,
                                    'total'=>$product->total,  
                                    'qty'=>$orderedQty);
                    }
                }

                $commentStatusArr = $this->_orderModel->getOrderStatus('Order Status');
                $commentArr = $this->_orderModel->getOrderCommentById($orderId, 'SHIPMENT_STATUS');
            
                $allShipmentStatusArr = $this->_masterLookup->getStatusByPatentName('SHIPMENT_STATUS', array(17007));

                if(empty($notifyMessage) && count($shipmentProductArr) == 0) {
                    $notifyMessage = $this->_OrdersController->getMessage('shipmentNRF');
                }
            }
            else {
                $notifyMessage = $this->_OrdersController->getMessage($orders->order_status_id);
                if(empty($notifyMessage) && count($shipmentProductArr) == 0) {
                    $notifyMessage = $this->_OrdersController->getMessage('shipmentNRF');
                }
            }
            //$deliveryUsers = $this->_orderModel->getUsersByRoleName(array('Delivery Executive'));
            //$deliveryUsers = $this->_roleRepo->getUsersByFeatureCode('DELR002');
            $user=Session::get('userId');
            $dcList = $this->_roleModel->getWarehouseData($user, 6);
            $dcList = json_decode($dcList,true);
             if(isset($dcList['118002'])){
                $parentHubdata=explode(",",$dcList['118002']);
            }else{
                $parentHubdata=[];
            }
            $deliveryUsers = $this->_roleRepo->getUsersByFeatureCodeWithoutLegalentity($parentHubdata,2);
            return view('Orders::addshipment')->with('orderId', $orderId)
                                            ->with('orderdata', $orders)
                                            ->with('couriers', $couriers)
                                            ->with('shipmentArr', $shipmentArr)
                                            ->with('commentStatusArr', $commentStatusArr)
                                            ->with('statusMatrixArr', $allShipmentStatusArr)
                                            ->with('actionName', 'addShipment')
                                            ->with('tabHeading', 'Create Shipment')
                                            ->with('notifyMessage', $notifyMessage)
                                            ->with('allShipmentStatusArr', $statusMatrixArr)
                                            ->with('deliveryUsers', $deliveryUsers)
                                            ->with('commentArr', $commentArr);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
}
