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
use App\Modules\Orders\Models\PaymentModel;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\Shipment;
use App\Modules\Orders\Models\Refund;
use App\Modules\Orders\Models\ReturnModel;
use App\Modules\Orders\Models\OrderTrack;
use App\Modules\Orders\Models\CancelModel;
use App\Modules\Orders\Models\Inventory;

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;
use App\models\Mongo\MongoApiLogsModel;
use App\Modules\CrateManagement\Models\CrateManagement;
use UserActivity;

class OrdersController extends BaseController {

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
	protected $_paymentModel;
	protected $_OrderTrack;
	protected $_cancelModel;
	protected $_Inventory;
	
    public function __construct($forApi=0) {
		$this->middleware(function ($request, $next) use($forApi){
    		if (!Session::has('userId') && $forApi==0) {
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
		$this->_paymentModel = new PaymentModel();
		$this->_OrderTrack = new OrderTrack();
		$this->_cancelModel = new CancelModel();
		$this->_Inventory = new Inventory();

		$this->_commentTypeArr = array('17'=>'Order Status', 'SHIPMENT_STATUS', 'INVOICE_STATUS', 'Cancel Status', '66'=>'REFUNDS', '67'=>'RETURNS');
		$this->_crateManagement = new CrateManagement();		
    }

    
	/*
	 * convertBillingAndShippingAddress() method is used to convert billing and shipping
	 * address in array format
	 * @param $billingAndShippingArr Array
	 * @return Array
	 */

	public function convertBillingAndShippingAddress($billingAndShippingArr) {
		try{
				$billingAndShipping = array();
				if(isset($billingAndShippingArr) && is_array($billingAndShippingArr) && count($billingAndShippingArr) > 0) {
					foreach($billingAndShippingArr as $billingAndShippingData) {
						if($billingAndShippingData->address_type == 'shipping') {
							$billingAndShipping['shipping'] = $billingAndShippingData;
						}

						if($billingAndShippingData->address_type == 'billing') {
							$billingAndShipping['billing'] = $billingAndShippingData;
						}
					}
				
					if(count($billingAndShipping)==1) {

						$billingAndShipping['billing'] = $billingAndShipping['shipping'];						

					}
				}
				return $billingAndShipping;
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}


	/*
	 * convertBillingAndShippingAddress() method is used to convert billing and shipping
	 * address in array format
	 * @param $billingAndShippingArr Array
	 * @return Array
	 */

	public function convertBillAndShipAddrLE($billingAndShippingArr) {
		try{
				$billingAndShipping = array();
				if(isset($billingAndShippingArr) && is_array($billingAndShippingArr) && count($billingAndShippingArr) > 0) {
					foreach($billingAndShippingArr as $billingAndShippingData) {
							$billingAndShipping['shipping'] = $billingAndShippingData;
							$billingAndShipping['billing'] = $billingAndShippingData;
					}
				}
				return $billingAndShipping;
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	/*
	 * detailAction() method is used to display order detail by order id
	 * @param $orderId Integer
	 * @return String
	 *
	 * Lookup category : 17 - Order Status
	 * Default order status - 17001 (New)
	 *
	 * NEW ORDER - 17001
	 * CONFIRMED - 17002
	 * CANCELLED BY CUSTOMER - 17009
	 * CANCELLED BY EBUTOR - 17015
	 * HOLD - 17014
	 * PROCESSING - 17013
	 *
	 */

	public function detailAction($orderId) {
		try {

			$hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD002');
			$roll_back = $this->_roleRepo->checkPermissionByFeatureCode('RollBack001');

            if($hasAccess == false) {
                return View::make('Indent::error');
            }

			$orders = $this->_orderModel->getOrderDetailById($orderId);
			if(empty($orders) || count($orders)==0) {
				Redirect::to('/salesorders/index')->send();
				die();
			}
			$paymentModesArr = $this->_masterLookup->getMasterLookupNamesByCategoryId(22,array(1,2,3));
			//$allLogisticFieldforceUsers		=	$this->_orderModel->getUsersByRoleName(array('Field Force Associate','Field Force Manager'));
			//$deliveryExecutives = $this->_orderModel->getUsersByRoleName(array('Delivery Executive'));
            $deliveryExecutives = $this->_roleRepo->getUsersByFeatureCode('DELR002');
            $editOrder = $this->_roleRepo->checkPermissionByFeatureCode('EDITORDER01');
           

			return view('Orders::detail')
					->with('orderdata',$orders)
					->with('actionName', 'orderDetail')
					->with('paymentModesArr', $paymentModesArr)
					//->with('allUsers', $allLogisticFieldforceUsers)
					->with('deliveryExecutives', $deliveryExecutives)
					->with('roll_back',$roll_back)
                    ->with('editOrder',$editOrder)
					->with('tabHeading', 'Details');
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
	

	private function sendSms($orderId, $message) {
		$order = $this->_orderModel->getOrderInfoById($orderId, array('orders.phone_no', 'orders.order_code'));
    	if(isset($order->phone_no) && !empty($order->phone_no)) {
    		$message = str_replace('{ORDER_CODE}', $order->order_code, $message);
    		if(!empty($order->phone_no)) {
    			$this->_sms->sendSMS($order->phone_no, $message);
    		}
    	}
	}
	
	public function updateOrderStatusAction() {

		try{

			$postData = Input::all();
            
			$orderId = isset($postData['gds_order_id']) ? (int)$postData['gds_order_id'] : 0;
			$orderStatusId = isset($postData['orderStatus']) ? (int)$postData['orderStatus'] : 0;
			$orderComment = isset($postData['order_comment']) ? (string)$postData['order_comment'] : '';
			$commentDate = isset($postData['gds_comment_date']) ? (date('Y-m-d H:i:s', strtotime($postData['gds_comment_date']))) : date('Y-m-d H:i:s');

			if($orderId) {
				$order = $this->_orderModel->getOrderStatusById($orderId);
                                if(!isset($order->gds_order_id)) {
					$response = array('status'=>304, 'message'=>'Invalid order id');
				}
				else if($order->order_status_id == '17009' || $order->order_status_id == '17015') {
					$response = array('status'=>304, 'message'=>'Already cancelled');
				}else if ($order->order_status_id=='17021' || $order->order_status_id=='17007' || $order->order_status_id=='17026') {
					$response = array('status'=>304, 'message'=>'You cannot cancel Invoiced Orders');
				}
				else {

					/*
					 * Cancel order status
					 *
					 * 17009 - CANCELLED BY CUSTOMER
					 * 17015 - CANCELLED BY EBUTOR
					 *
					 */

					if($orderStatusId == '17009' || $orderStatusId == '17015') {
						$prdArr = $this->_orderModel->getProductByOrderId($orderId);
						//getting previous cancel qty
						$cancelledArr = $this->_orderModel->getCancelledQtyByOrderId($orderId);
						
						$productsArr = array();
						if(is_array($prdArr)) {
							foreach($prdArr as $product){
								
								$canledQty = isset($cancelledArr[$product->product_id]) ? $cancelledArr[$product->product_id] : 0;
								$productsArr[] = array('product_id'=>$product->product_id, 'qty'=>($product->qty - $canledQty));
							}
							if(count($productsArr)) {
								$cancel_status = $this->cancelOrderItem($orderId, $productsArr, $orderStatusId, true);
								// while cancelling from web end,we are rolling back cashback
								if($cancel_status == true){
									// reducing cashback,if order has cashback added instantly
									$this->revertCashbackFromOrder($orderId,$orderStatusId,"Order Cancelled!");

								}
							}
						}						
					}

					/**
					 * Update product status picklist generated
					 */
					
					if($orderStatusId == '17020'){
						$productArr = $this->_orderModel->getProductByOrderId($orderId);
						if(is_array($productArr)) {
							foreach($productArr as $product){
								$this->_orderModel->updateProductStatus($orderId, $product->product_id, $orderStatusId);
							}
						}
					}

                    if($orderStatusId==0){
                        $orderStatusId = $order->order_status_id;
                    }
					$this->_orderModel->updateOrderStatusById($orderId, $orderStatusId);

					if(empty($orderComment) && empty($postData['orderStatus'])) {
						$response = array('status'=>400, 'message'=>Lang::get('salesorders.errorInputData'));
						return Response::json($response);
					}

					$this->saveComment($orderId, 'Order Status', array('comment'=>$orderComment, 'order_status_id'=>$orderStatusId));

					$response = array('status'=>200, 'message'=>Lang::get('salesorders.successUpdateStatus'));
                   Notifications::addNotification(['note_code' => 'ORD016','note_message'=>Lang::get('salesorders.successUpdateStatus'), 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['ORDID' => $orderId], 'note_link' => '/salesorders/detail/'.$orderId]);
				}
			}
			else {
				$response = array('status'=>400, 'message'=>Lang::get('salesorders.errorInputData'));
			}
			return Response::json($response);
		}
		catch(ErrorException $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorApiRequest')));
		}
	}

	public function saveComment($orderId, $commentType, $dataArr) {
		$typeId = $this->_orderModel->getCommentTypeByName($commentType);
  		$commentby = (int)Session('userId');
  		
  		// code start for cancelled by customer
  		$customerId = Session('customerId');
  		$created_by = null;
  		if(isset($customerId)) {
  			$created_by = $customerId;
		}
		// code end for cancelled by customer
	 	
	 	/*
	 	* cancelled by picker	
	 	 */
  		if(!$commentby) {
  			$trackDetail = $this->_orderModel->getGdsTrackDetail($orderId);
  			$commentby = isset($trackDetail->picker_id) ? $trackDetail->picker_id : 0;
  		}		
		$date = date('Y-m-d H:i:s');
		$commentArr = array('entity_id'=>$orderId, 'comment_type'=>$typeId,
						'comment'=>(string)$dataArr['comment'],
						'commentby'=>$commentby,
						'created_by'=>$created_by,
						'order_status_id'=>$dataArr['order_status_id'],
						'created_at'=>(string)$date,
						'comment_date'=>(string)$date
						);

		$this->_orderModel->saveComment($commentArr);
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

	public function shippingAction() {
            try{
		$postData = Input::all();

		if($postData['courierId'] == 'Others') {
			$services = '<input type="text" id="service_name" name="service_name" class="form-control">';
		}
		else {
			$servicesArr = $this->_orderModel->getShippingServiceName($postData['courierId']);
			$services = '<select id="service_name" name="service_name" class="form-control" onchange="removeError(\'service_name\');" onblur="removeError(\'service_name\');">';
			$services .= '<option value="">Select Service</option>';
			if(is_array($servicesArr) && count($servicesArr) > 0) {
				foreach($servicesArr as $serviceData) {
					$services .= '<option value="'.$serviceData->service_name.'">'.$serviceData->service_name.'</option>';
				}
			}
			$services .= '</select>';
		}

		return Response::json(array('status' => 200, 'message' => 'Success', 'data'=>$services));
            }
            catch(ErrorException $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
                return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
            }
	}

	
	

    public function verifyCancel($statusValue) {
    	$statusArr = array('17001', '17002', '17003', '17004', '17005', '17013', '17020');
    	if(in_array($statusValue, $statusArr)) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

    public function getMessage($statusValue) {
    	switch ($statusValue) {
    		case '17001':
    			return Lang::get('salesorders.orderShipment');
			break;

    		case '17014':
    			return Lang::get('salesorders.orderHold');
			break;
			case '17009':
    			return Lang::get('salesorders.successCancelOrder');
			break;
			case '17015':
    			return Lang::get('salesorders.successCancelOrder');
			break;
			case 'cancelNRF':
    			return Lang::get('salesorders.cancelNRF');
			break;
			case 'invoiceNRF':
    			return Lang::get('salesorders.invoiceNRF');
			break;
			case 'shipmentNRF':
    			return Lang::get('salesorders.shipmentNRF');
			break;

    		default:
    			# code...
			break;
    	}
    }

    
	
	/**
	 * getOrderDetailAction() - Get order information by ajax request
	 * @param  Request $request Object
	 * @return String
	 */

	public function getOrderDetailAction(Request $request) {

		try{
			if($request->ajax()){

			$orderId = Input::get('orderId');
			$orders = $this->_orderModel->getOrderDetailById($orderId);
			if(count($orders)==0) {
				return Response::json(array('status' => 400, 'message' => 'Failed'));
			}
			$checkuserfeature = $this->_roleRepo->checkPermissionByFeatureCode('ORDC001');
	        $statusMatrixArr = $this->_masterLookup->getStatusMatrixByValue($orders->order_status_id, 17);
            if(!$checkuserfeature){
            	$statusMatrixArr=[];

            }

			$orderStatusArr = $this->_orderModel->getOrderStatus('Order Status');
			$paymentStatusArr = $this->_orderModel->getOrderStatus('Payment Status');
			$paymentMethodArr = $this->_orderModel->getOrderStatus('Payment Type');
			$orderStatusValue = $orders->order_status_id;
			if (array_key_exists($orders->order_status_id, $orderStatusArr)) {
				$orderStatus = $orderStatusArr[$orders->order_status_id];
			}

			if (array_key_exists($orders->payment_status_id, $paymentStatusArr)) {
				$paymentStatus = $paymentStatusArr[$orders->payment_status_id];
			}

			if (array_key_exists($orders->payment_method_id, $paymentMethodArr)) {
				$paymentMethod = $paymentMethodArr[$orders->payment_method_id];
			}

			$orders->order_status_id = isset($orderStatus) ? $orderStatus : '';
			$orders->payment_status_id = isset($paymentStatus) ? $paymentStatus : '';
			$orders->payment_method_id = isset($paymentMethod) ? $paymentMethod : '';

			$billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);

			$billingAndShipping = $this->convertBillingAndShippingAddress($billingAndShippingArr);
			$products = $this->_orderModel->getProductByOrderId($orderId);
			$itemShippedArr = $this->_orderModel->getShipmentQtyByOrderId($orderId);
			$itemInvoiceArr = $this->_orderModel->getItemInvoicedQtyByOrderId($orderId);
			$itemCancelArr = $this->_orderModel->getCancelledQtyByOrderId($orderId);
			$taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);

			$taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
			$taxSummary = isset($taxSummaryArr['summary']) ? $taxSummaryArr['summary'] : '';
			$productTaxArr = isset($taxSummaryArr['item']) ? $taxSummaryArr['item'] : '';
			$taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
			$whInfo = $this->_leModel->getWarehouseById($orders->le_wh_id);
			$userInfo = '';
			if($orders->created_by) {
				$userInfo = $this->_leModel->getUserById($orders->created_by);
			}

			$productReturnsArr = $this->_returnModel->getReturnedByOrderId($orderId);
			$returns = array();
			if(is_array($productReturnsArr)) {
				foreach ($productReturnsArr as $row) {
					$returns[$row->product_id] = $row->returned;
				}
			}
		    $whDetails = $this->_leModel->getWarehouseById($orders->le_wh_id);
			$hubInfo = $this->_leModel->getWarehouseById($orders->hub_id);
			$gstin = $this->_orderModel->getGstin($orderId);
                        $url = env('CASHBACK_URL');
                        $post_feild = ["order_id"=>$orderId];
                        $headers = array("cache-control: no-cache","content-type: multipart/form-data");
                        $cb_response = Utility::sendcUrlRequest($url, $post_feild, $headers,0);
            $editOrder = $this->_roleRepo->checkPermissionByFeatureCode('EDITORDER01');
            $openInvoice = $this->_roleRepo->checkPermissionByFeatureCode('OPNINV001');

			$view = view('Orders::Form.orderDetailForm')
                ->with('orderdata',$orders)
                ->with('products',$products)
                ->with('itemInvoiceArr',$itemInvoiceArr)
                ->with('itemShippedArr',$itemShippedArr)
                ->with('itemCancelArr', $itemCancelArr)
           		->with('openInvoice',$openInvoice)			

                ->with('billing', (isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] : ''))
                ->with('shipping', (isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] : ''))
                ->with('statusMatrixArr', $statusMatrixArr)
                ->with('productTaxArr', $productTaxArr)
                ->with('taxSummary', $taxBreakup)
                ->with('returns', $returns)
                ->with('hubInfo', $hubInfo)
                ->with('whInfo', $whInfo)
                ->with('gstin', $gstin)
                ->with('userInfo', $userInfo)
                ->with('cb_response', $cb_response)
                ->with('orderStatusValue', $orderStatusValue)
                ->with('checkuserfeature', $checkuserfeature)
                ->with('editOrder',$editOrder);


			$contents = $view->render();
			return Response::json(array('status' => 200, 'message' => $contents));
			}
		}
		catch(Exception $e){
			return Response::json(array('status' => 400, 'message' => 'Failed'));
		}
	}

	/**
	 * getStatsAction() is used to get stats
	 * @param Null
	 * @return JSON
	 */

	public function getStatsAction() {
		$orderId = Input::get('orderId');

		$totalInvoices = (int)$this->_orderModel->getTotalInvoicedByOrderId($orderId);
		$totalShipments = (int)$this->_orderModel->getShipmentCount($orderId);
		$totalComments = (int)$this->_orderModel->getOrderCommentCountById($orderId);
		$totCancelled = $this->_orderModel->getCanceledCountByOrderId($orderId);
		$totReturns = (int)$this->_orderModel->getReturnedCountByOrderId($orderId);
		$totalPayments = (int)$this->_paymentModel->getTotalPaymentsByOrderId($orderId);
		$totalVerification = (int)$this->_orderModel->getTotalVerificationByOrderId($orderId);

		$totNctHistory = $this->_paymentModel->getPaymentPendingHistoryByOrderId($orderId, 'count');
		
		$totRefunds = 0;

		$dataArr = array('totalInvoices'=>$totalInvoices, 'totalShipments'=>$totalShipments, 'totalComments'=>$totalComments,
		'totCancelled'=>$totCancelled, 'totReturns'=>$totReturns, 'totRefunds'=>$totRefunds, 'totPayments' => $totalPayments, 'totNctHistory'=>$totNctHistory,'totVerification'=>$totalVerification);
		return Response::json(array('status' => 400, 'message' => $dataArr));
	}

	
	private function verifyInvoice($statusCode) {
		$statusArr = array('17002', '17003', '17004', '17005', '17006', '17014', '17013','17007','17008', '17020');
		if(in_array($statusCode, $statusArr)) {
			return true;
		}
		else {
			return false;
		}
	}

	
    public function saveOutputTax($gridId) {
        try{
            $productTax = $this->_orderModel->getProductInvoicedTaxByGridId($gridId);
            $data = $this->_orderModel->saveOutputTax($productTax);
            return $data;
        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }

    public function saveStockOutward($orderId) {
    	//DB::beginTransaction();//commented by Nishanth
        try{
            $productStock = $this->_orderModel->getProductStockByOrderId($orderId);
            if(count($productStock)>0){
                $invLogs = array();
                $batch_history_array = array();
                $batch_array = array();
                $invKey = 0;
	            $batchModel = new \App\Modules\Orders\Models\batchModel();
	            $batch_inventory_update = "";
                foreach ($productStock as $stock){
                    $invInfo = $this->_Inventory->getInventory($stock->product_id, $stock->le_wh_id);
                    $hub_id = $stock->hub_id;
                    $prevSOH = isset($invInfo->soh) ? $invInfo->soh : 0;
                    $prevDitOrderQty = isset($invInfo->dit_order_qty) ? $invInfo->dit_order_qty : 0;
                    $prevOrderQty = isset($invInfo->order_qty) ? $invInfo->order_qty : 0;
                    $prevQuarantineQty = isset($invInfo->quarantine_qty) ? $invInfo->quarantine_qty : 0;
                    $prevDndQty = isset($invInfo->dnd_qty) ? $invInfo->dnd_qty : 0;
                    $prevDitQty = isset($invInfo->dit_qty) ? $invInfo->dit_qty : 0;
                    $invLogs[$invKey] = array(
                        'le_wh_id' => $stock->le_wh_id,
                        'product_id' => $stock->product_id,
                        'soh' => 0,
                        'order_qty' => 0,
                        'ref' => $stock->invoice_code,
                        'ref_type' => 2,
                        'old_soh' => $prevSOH,
                        'old_order_qty' => $prevOrderQty,
                        'old_dit_order_qty' => $prevDitOrderQty,
                        'old_quarantine_qty' => $prevQuarantineQty,
                        'old_dnd_qty' => $prevDndQty,
                        'old_dit_qty' => $prevDitQty,
                        'comments' => 'SOH and QrderQty Substracted'
                    );
                    if ($hub_id == 10695) {
                        $invLogs[$invKey]['dit_qty'] = '-' . $stock->qty;
                        $invLogs[$invKey]['dit_order_qty'] = '-' . $stock->qty;
                        $fields = array('dit_qty' => DB::raw('(dit_qty-' . $stock->qty . ')'), 'dit_order_qty' => DB::raw('(dit_order_qty-' . $stock->qty . ')'));
                    } else {
                        $invLogs[$invKey]['soh'] = '-' . $stock->qty;
                        $invLogs[$invKey]['order_qty'] = '-' . $stock->qty;
                        $fields = array('soh' => DB::raw('(soh-' . $stock->qty . ')'), 'order_qty' => DB::raw('(order_qty-' . $stock->qty . ')'));
                    }
                    $this->_Inventory->updateInventoryByProductIdAndWhId($fields, $stock->product_id, $stock->le_wh_id);
                    $invKey++;
                    $batch_inv_array = $this->getBatchesByData($stock->product_id,$stock->le_wh_id,$stock->qty,0,10,[]);
                    foreach ($batch_inv_array as $ikey => $ivalue) {
                    	//creating batch array
	                    $batch_id = $ivalue->inward_id;
	                    $invb_id = $ivalue->invb_id;
	                    $elp = $ivalue->elp;
	                    $req_qty = $stock->qty;
	                    if($req_qty > $ivalue->qty){
	                    	$used_qty = $ivalue->qty;
	                    }else if($ivalue->qty >= $req_qty){
	                    	$used_qty = $req_qty;
	                    }
	                    if(count($batch_inv_array) == 1){
	                    	$batch_ord_qty = $stock->ord_qty;
	                    }else{
	                    	$batch_ord_qty = $used_qty;
	                    }
	                    $batch_array[] = array("gds_order_id"=>$stock->gds_order_id,
	                						"inward_id"=>$batch_id,
	                						"le_wh_id"=>$stock->le_wh_id,
	                						"product_id"=>$stock->product_id,
	                						"ord_qty"=>$batch_ord_qty,
	                						"inv_qty"=>$used_qty,
	                						"esp"=>$stock->actual_esp,
	                						"elp"=>$elp,
	                						'main_batch_id'=>$ivalue->main_batch_id);

	                    $batch_history_array[] = array("inward_id"=>$batch_id,
	                						"le_wh_id"=>$stock->le_wh_id,
	                						"product_id"=>$stock->product_id,
	                						"qty"=>'-'.$used_qty,
	                						"old_qty"=>$ivalue->qty,
	                						'ref'=>$stock->invoice_code,
				                            'ref_type'=>2,
				                            'dit_qty'=>0,
				                            'old_dit_qty'=>0,
				                            'dnd_qty'=>0,
				                            'old_dnd_qty'=>0,
				                            'comments'=>"Qty Substracted for Batch Id:$batch_id");
	                    $stock->qty = $req_qty - $used_qty;
	                    $batch_inventory_update .= "UPDATE inventory_batch SET qty=qty-$used_qty where invb_id = $invb_id;";
                    }


                }

                if(count($invLogs)) {
                    $this->_Inventory->addInQueueWithBulk($invLogs);
                }
                $this->_orderModel->saveStockOutward($productStock);
                if(count($batch_array)){
	                //inserting batch data
	                $batchModel->insert($batch_array);
	                if(isset($batch_inventory_update) && $batch_inventory_update != ""){
	                	DB::unprepared($batch_inventory_update);
	                }
	                $batchModel->insertBatchHistory($batch_history_array);

	            }
            }
            
          //  DB::commit();//commented by Nishanth
            return true;

        }catch(Exception $e) {
        //	DB::rollback();//commented by Nishanth
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }
    }

    public function bulkPrintAction() {
        try{
        	$orderIdsArr = Session::get('orderIds');
        //	print_r($orderIdsArr);die;
        	$bulkPrintData = array();
        	if(is_array($orderIdsArr[0])) {
        		foreach ($orderIdsArr[0] as $orderId) { 	
        		$orderDetails = $this->_orderModel->getOrderDetailById($orderId);
				if(isset($orderDetails->gds_order_id)){

		            $products = $this->_orderModel->getProductByOrderId($orderId);
		            $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);

		            //echo "<pre>";print_r($orderDetails);die;

					$billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
					$billingAndShipping = $this->convertBillingAndShippingAddress($billingAndShippingArr);
					$legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id,$orderDetails->le_wh_id);

		            $prodTaxes = $this->_orderModel->getTaxWithProduct($taxArr);
		            $taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
					$taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
					//echo '<pre>';print_r($prodTaxes);die;
					$leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
					$lewhInfo = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);

					$companyInfo = $this->_leModel->getCompanyAccountByLeId($orderDetails->legal_entity_id);
					$userInfo = '';
					if($orderDetails->created_by) {
						$userInfo = $this->_leModel->getUserById($orderDetails->created_by);
					}
					$billing = isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] : '';
					$shipping = isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] : '';

					$data = array('orderDetails'=>$orderDetails, 
									'products'=>$products, 
								'billing'=>$billing, 
								'shipping'=>$shipping,
								'taxBreakup'=>$taxBreakup, 'leInfo'=>$leInfo, 
								'userInfo'=>$userInfo,
								'prodTaxes'=>$prodTaxes, 'lewhInfo'=>$lewhInfo, 
								'companyInfo'=>$companyInfo, 
								'orderDetails'=>$orderDetails, 
								'legalEntity'=>$legalEntity
								);
						//$bulkPrintData[] = $data;
						$bulkPrintData[] = view('Orders::print')->with($data)->render();	
	            	}
				}
				//echo "<pre>";print_r($bulkPrintData);die;
				return  view('Orders::bulkprint')->with('bulkPrintData', $bulkPrintData);
        	}
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
	}

	private function getChallanData($orderId) {

		$orderDetails = $this->_orderModel->getOrderDetailById($orderId);
		if(!isset($orderDetails->gds_order_id)) {
			Redirect::to('/salesorders/index')->send();
			die();
		}

		if(isset($orderDetails->gds_order_id)){

            $products = $this->_orderModel->getProductByOrderId($orderId);
            $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
           
			$billAndShippArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
			$billAndShipp = $this->convertBillingAndShippingAddress($billAndShippArr);
			$legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id,$orderDetails->le_wh_id);

            $prodTaxes = $this->_orderModel->getTaxWithProduct($taxArr);
            $taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
			$taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
			//echo '<pre>';print_r($taxBreakup);die;
			$leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
			$lewhInfo = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);

			$userInfo = '';
			if($orderDetails->created_by) {
				$userInfo = $this->_leModel->getUserById($orderDetails->created_by);
			}

			$data = array('orderDetails'=>$orderDetails, 
						'products'=>$products, 
						'billing' => $billAndShipp['billing'], 
						'shipping'=>$billAndShipp['shipping'], 
						'taxBreakup'=>$taxBreakup, 
						'leInfo'=>$leInfo, 
						'prodTaxes'=>$prodTaxes, 
						'lewhInfo'=>$lewhInfo, 
						'orderDetails'=>$orderDetails, 
						'legalEntity'=>$legalEntity, 
						'userInfo'=>$userInfo
						);
			return $data;
		}
	}
		
	public function printAction($orderId) {
        try{
        	
			$data = $this->getChallanData($orderId);
            return view('Orders::print')->with($data);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
	}

	public function pdfAction($orderId) {
        try{
			$data = $this->getChallanData($orderId);
			$pdf = PDF::loadView('Orders::pdf', $data);
			return $pdf->download('challan_'.$orderId.'.pdf');
            //return view('Orders::pdf')->with($data);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
	}

	public function getOrderStatsAction() {
		$postData = Input::all();
		//print_r($postData);die;
		$statusArr = $this->_orderModel->getStats($postData);
		$totOrders = 0;
		foreach ($statusArr as $qty) {
			$totOrders = $totOrders + $qty;
		}

		$arrForJson = array('all'=>$totOrders, 'process'=>(isset($statusArr[17013]) ? $statusArr[17013] : 0),
			'delivered'=>(isset($statusArr[17007]) ? $statusArr[17007] : 0),
			'completed'=>(isset($statusArr[17008]) ? $statusArr[17008] : 0));
		return Response::json($arrForJson);
	}
	
	public function savePicklistAction($data=[]) {
		DB::beginTransaction();
	  	try {
                    if(count($data)==0){
	  		$data = Input::all();
                    }
	  		//print_r($data);die;
            //        Log::info('in save picklist action');
                    $statusArr = array();
	  		if(isset($data['statusCodes']) && is_array($data['statusCodes'])) {
	  			$statusArr = array_unique($data['statusCodes']);	
	  		}
	  		//print_r($statusArr);die;
	  		
	  		/**
	  		 * Verify picking
	  		 */
	  		$shippedArr = $this->_shipmentModel->checkPicklist($data['ids'], array('orders.order_code'));
	  		
	  		$orderPickedArr = array();

	  		if(is_array($shippedArr) && count($shippedArr) > 0) {
	  			foreach ($shippedArr as $ship) {
	  				$orderPickedArr[] = $ship->order_code;
	  			}
	  		}
	  		#print_r($orderPickedArr);die;
			
	  		if(count($orderPickedArr)) {
	  			DB::commit();
	  			$message = 'Picking Already Completed of '.implode($orderPickedArr, ', ').' orders. So, you can not assign / re-assign to picker.';
				return Response::json(array('status'=>400, 'message'=>$message));
	  		}
	  		else if(count($statusArr) > 1) {
	  			DB::commit();
				$message = 'You have selected orders with different status. Make sure order status should be same.';
				return Response::json(array('status'=>400, 'message'=>$message));
			}
			else if(isset($statusArr[0]) && ($statusArr[0] != '17001' && $statusArr[0] != '17020')) {
				DB::commit();
				$message = "Sorry, you can't generate picklist. Make sure order status should be Open.";
				return Response::json(array('status'=>400, 'message'=>$message));
			}
			else {
				Session::set('printPicklist',$data);
				$result = $this->_orderModel->savePicklist($data);
				
				foreach($result as $value){
					if(!empty($value['cancelledArr'])){
						$uniq = array('order_id'=>$value['cancelledArr']['Order_id']);
						UserActivity::userActivityLog("CancelItemQtyOnPicking", $value['cancelledArr']['product_list'], "Cancel Item qty at Picklist generation" , '', $uniq);

						$this->cancelOrderItem($value['cancelledArr']['Order_id'], $value['cancelledArr']['product_list'], '17015', true, 'system');
					}
				}

				DB::commit();
				if($result=='Success'){
					return Response::json(array('Status' => 200, 'Message' => 'Picklist generated successfully'));
				}else{
					return Response::json(array('Status' => 402, 'Message' => $result));
				}
			}
			DB::commit();
	      }
	      catch(Exception $ex) {
	      	DB::rollback();
	          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
	          return Response::json(array('status'=>400, 'message'=>'Failed'));
	      }
  	}

	public function saveBulkInvoiceOrdersAction() {
	  	try {
	  		$data = Input::all();
	  		//print_r($data);die;

	  		$orderStatusArr = array('17021', '17008', '17014', '17022', '17024', '17025', '17026', '17027', '17028', '17023', '17007', '17008');
	  		
			if(isset($statusArr[0]) && !in_array($statusArr[0], $orderStatusArr)) {
				$message = "Sorry, you can't generate invoice.";
				return Response::json(array('status'=>400, 'message'=>$message));
			}
			else {
				Session::put('orderIds', array());
				Session::push('orderIds', $data['ids']);

				return Response::json(array('status' => 200, 'message' => 'Invoice generated successfully'));
			}

	      }
	      catch(Exception $ex) {
	          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
	          return Response::json(array('status'=>400, 'message'=>'Failed'));
	      }
  	}

	public function generateInvoiceFromOpen() {
	  	try {
	  		$data = Input::all();
	  		// print_r($data);die;
	  		$action = isset($data['action']) ? $data['action'] : '';

            if(!isset($data['ids'])) {
                $errorMsg = 'Please select at least one order.';
            }

	  		$statusArr = array();
	  		if(isset($data['statusCodes']) && is_array($data['statusCodes'])) {
	  			$statusArr = array_unique($data['statusCodes']);	
	  		}
	  		//print_r($statusArr);die;
	  		$message = '';

	  		if(count($statusArr) > 1) {
				$message = 'You have selected orders with different status. Make sure order status should be same.';				
			}	

			if(isset($statusArr[0]) && !in_array($statusArr[0], array('17001'))) {
				$message = "Sorry, you can't create Invoice. Make sure order status should be Open.";
			}

	  		if(!empty($message)) {
	  			return Response::json(array('status'=>400, 'message'=>$message));
	  		}else {

                $status = [];
                $orderidsts=[];
                $successorderids=[];
                if(is_array($data['ids']) && count($data['ids']) > 0) {
                    foreach ($data['ids'] as $orderId) {
                    	$getordersts=$this->_orderModel->getOrderStatusFromOrderId($orderId);
                    	if($getordersts>0){
	                    	$invoiceId = $this->_invoiceModel->generateOpenOrderInvoice($orderId, true, 'Invoice generated from Open.');
	                        if(is_array($invoiceId) && isset($invoiceId['status']) && $invoiceId['status']=400){
	                            return Response::json(array('status'=>400, 'message'=>$invoiceId['message']));
	                        }
	                        array_push($successorderids, $orderId);
                    	}else{
                    		array_push($orderidsts, $orderId);
                    	}
                    }
                }

                if(count($orderidsts)>1 && count($orderidsts)!=count($successorderids)){
                	return Response::json(array('status'=>400, 'message'=>$successorderids.' orders are Invoiced and '.$orderidsts.' orders are not invoiced'));	
                }elseif(count($orderidsts)==1){
                	return Response::json(array('status'=>400, 'message'=>'Order should be in Open status to Invoice'));
                }else{
		        	return Response::json(array('status'=>200, 'message'=>'Success'));
		        }
			}
	      }
	      catch(\Exception $ex) {
	          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
	          return Response::json(array('status'=>400, 'message'=>'Failed'));
	      }
  	}
	
	public function saveOrderInSessionAction() {
	  	try {
	  		$data = Input::all();
	  		//print_r($data);die;
	  		$action = isset($data['action']) ? $data['action'] : '';

	  		$statusArr = array();
	  		if(isset($data['statusCodes']) && is_array($data['statusCodes'])) {
	  			$statusArr = array_unique($data['statusCodes']);	
	  		}
	  		//print_r($statusArr);die;
	  		$message = '';

	  		if(count($statusArr) > 1) {
				$message = 'You have selected orders with different status. Make sure order status should be same.';				
			}	

	  		if($action == 'createShipment') {
	  			$shipmentStatusArr = array('17020', '17013', '17005');
	  			 
	  			 if(isset($statusArr[0]) && !in_array($statusArr[0], $shipmentStatusArr)) {
					$message = "Sorry, you can't create shipment. Make sure order status should be Picklist Generated / Ready to Dispatch.";
				 }
	  		}

	  		if($action == 'printChallan') {
	  			$challanStatusArr = array('17001', '17020', '17013', '17005');

	  			if(isset($statusArr[0]) && !in_array($statusArr[0], $challanStatusArr)) {
					$message = "Sorry, you can't print challan. Make sure order status should be Open / Picklist Generated / Ready to Dispatch.";
				}
	  		}

	  		if(!empty($message)) {
	  			return Response::json(array('status'=>400, 'message'=>$message));
	  		}
			else {
				Session::put('orderIds', array());
		        Session::push('orderIds', $data['ids']);
		        return Response::json(array('status'=>200, 'message'=>'Success'));
			}
	      }
	      catch(Exception $ex) {
	          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
	          return Response::json(array('status'=>400, 'message'=>'Failed'));
	      }
  	}
	
	public function printBulkInvoiceAction($flag=0) {
	
        try{
        	$orderIdsArr = Session::get('orderIds');
        	
			$bulkPrintData = array();
        	if(is_array($orderIdsArr[0])) {
          $masvalue = $this->_masterLookup->getMasterLokup(78018);                    
          $termsdisplay = isset($masvalue->description)?$masvalue->description:0;
        	foreach ($orderIdsArr[0] as $orderId) { 	
				
					$products = $this->_invoiceModel->getInvoiceProductByOrderId($orderId);
				
		            if(empty($products))
					{
						continue;
					}
					$orderId = $products[0]->gds_order_id;
                                        $ecash_applied = isset($products[0]->ecash_applied)?$products[0]->ecash_applied:0;
					$orderDetails = $this->_orderModel->getOrderDetailById($orderId);
					$trackInfo = $this->_OrderTrack->getTrackDetailByOrderId($orderId);
                    #echo '<pre>';print_r($products);die;
					if(count($orderDetails)==0) {
						Redirect::to('/salesorders/index')->send();
					}
		            $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
					$billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
					$billingAndShipping = $this->convertBillingAndShippingAddress($billingAndShippingArr);
					$legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id,$orderDetails->le_wh_id);
					
					$leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
					$lewhInfo = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
					$lewhInfo->authorized_by = str_replace('#LOGO', '<img style="width: 9px;padding-left:2px;vertical-align: middle;" src="/assets/admin/layout/img/small-logo.png" alt="logo" class="small-logo"/>', $lewhInfo->authorized_by);

		            $prodTaxes = array();
		            $hasGstProdTaxes = array();

		            foreach ($taxArr as $tax) {
		            	$prodTaxes[$tax->product_id] = array('name'=>$tax->name, 'tax_value'=>$tax->tax_value, 'tax'=>$tax->tax, 
                            'cgstPer'=>(($tax->tax * $tax->CGST)/100),  
                            'sgstPer'=>(($tax->tax * $tax->SGST)/100), 
                            'igstPer'=>(($tax->tax * $tax->IGST)/100), 
                            'utgstPer'=>(($tax->tax * $tax->UTGST)/100), );
                        
                        if($tax->CGST > 0 || $tax->SGST > 0 || $tax->IGST > 0) {
                            $hasGstProdTaxes[] = $tax->CGST;
                        }
		            }
					$userInfo = '';
					if($orderDetails->created_by) {
						$userInfo = $this->_leModel->getUserById($orderDetails->created_by);
					}

        			$delSlots = $this->_masterLookup->getMasterLookupByCategoryName('Delivery Slots');                                        
					$cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);
					$pickerInfo = $this->_OrderTrack->getPickerByOrderId($orderId);
					
					$template = 'printinvoice';
                    if(is_array($hasGstProdTaxes) && count($hasGstProdTaxes) > 0) {
                        $template = 'printinvoicenew';
                    }
                   $data = array('orderDetails'=>$orderDetails, 
									'products'=>$products,
									'billing'=>(isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] : ''),
									'shipping'=>(isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] : ''),
									'leInfo'=>$leInfo,
									'prodTaxes'=>$prodTaxes,
									'trackInfo'=>$trackInfo,
									'lewhInfo'=>$lewhInfo,
									'userInfo'=>$userInfo,
									'delSlots'=>$delSlots,
									'pickerInfo'=>$pickerInfo,
                  'cratesList'=>$cratesList,
									'legalEntity'=>$legalEntity,
									'hasGstProdTaxes'=>$hasGstProdTaxes,
                  'ecash_applied'=>$ecash_applied,
                  'termsdisplay'=>$termsdisplay
								);

                  	if($flag>0){
						$template ='printMobileInvoice';
					}
					$bulkPrintData[] = view('Orders::'.$template)->with($data)->render();
						

				}
				//echo "<pre>";print_r($bulkPrintData);die;
				return  view('Orders::bulkinvoice')->with('bulkPrintData',$bulkPrintData);
        	}
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }	
	
	}
	
	public function markAsDelivered() {
	        try {
	              $data = Input::all();
	              //print_r($data);die;

	              $statusArr = array();
	              if(isset($data['statusCodes']) && is_array($data['statusCodes'])) {
	                  $statusArr = array_unique($data['statusCodes']);    
	              }
	              //print_r($statusArr);die;
	            if(count($statusArr) > 1) {
	                $message = 'You have selected orders with different status. Make sure order status should be same.';
	                return Response::json(array('status'=>400, 'message'=>$message));
	            }
	            else if(isset($statusArr[0]) && ($statusArr[0] != '17007' && $statusArr[0] != '17021' && $statusArr[0] != '17014')) {
	                $message = "Sorry, you can not mark as delivered. Make sure order status should be invoices/shipped.";
	                return Response::json(array('status'=>400, 'message'=>$message));
	            }
	            else {
	                //print_r($data['ids']);die;
	                
	                if(isset($data['ids']) && is_array($data['ids']) ) {
	                    foreach ($data['ids'] as $orderId) {
	                        $this->_orderModel->updateOrderStatusById($orderId, '17007');
	                        $this->_orderModel->updateDeliveryStatusById($orderId,$data['deliveredBy'], $data['deliveredDate']);
	                        $commentDelivered = 'Order Id: '.$orderId.' marked delivered';
	                        
	                        $this->saveComment($orderId, 'SHIPMENT_STATUS', array('comment'=>$commentDelivered, 'order_status_id'=>'17007'));    

	                          $this->_orderModel->changeProductStatus($orderId, '17007');

	                          $this->_shipmentModel->updateShipmentStatusByOrderId(array('status_id'=>'17007'), $orderId);
	                    	
	                    	// send sms to customer & field force
				  			$this->sendMarkDeliveredSMS($orderId);

				  			// send email to customer, field force and logistic manager's teams
							$this->sendMarkDeliveredEmail($orderId);

	                    }
	                }
	                return Response::json(array('status'=>200, 'message'=>'Success'));
	            }
	          }
	          catch(Exception $ex) {
	              Log::error($ex->getMessage().' '.$ex->getTraceAsString());
	              return Response::json(array('status'=>400, 'message'=>'Failed'));
	          }
	    }

	/**
	 * assignDeliveryExec() method is used to assign order to delivery executive for delivery to the customer
	 * @param Null
	 * @return Object
	 * 
	 */
	
	public function assignDeliveryExec() {
		
		DB::beginTransaction();
  
  	        try {
	              $data = Input::all();
                  //print_r($data);die;
				  $vehicle_no = isset($data['vehicle_no']) ? $data['vehicle_no'] : '';
				  $vehicle_id = isset($data['vehicle_id']) ? $data['vehicle_id'] : '';

	              $statusArr = array();
	              if(isset($data['statusCodes']) && is_array($data['statusCodes'])) {
	                  $statusArr = array_unique($data['statusCodes']);    
	              }
	              //print_r($statusArr);die;
	             
	            if(count($statusArr) > 1) {
	                $message = 'You have selected orders with different status. Make sure order status should be same.';
	                return Response::json(array('status'=>400, 'message'=>$message));
	            }
	            else if(isset($statusArr[0]) && ($statusArr[0] != '17007' && $statusArr[0] != '17021' && $statusArr[0] != '17014' && $statusArr[0] != '17025' && $statusArr[0] != '17026')) {
	                $message = "Sorry, you can not mark as delivered. Make sure order status should be invoices/shipped.";
	                return Response::json(array('status'=>400, 'message'=>$message));
	            }
	            else {
	            	//print_r($data['ids']);die;
	                //$deliveryDate = date('Y-m-d', strtotime($data['deliveredDate']));
	                //$deliveryDateTime = $deliveryDate.' '.date('H:i:s');

	            	$deliveryDateTime = date('Y-m-d H:i:s');
	            		foreach ($data['ids'] as $key=>$orderId) {
	            			$order = $this->_orderModel->getOrderStatusById($orderId);
	            			if(isset($order->order_status_id) && !in_array($order->order_status_id, array('17021','17014','17026'))){
	            				unset($data['ids'][$key]);
	            			}	
	            		}
	                if(isset($data['ids']) && is_array($data['ids']) ) {
	                	foreach ($data['ids'] as $orderId) {
			  				$comment = 'Assigned to delivery executive(Delivery ID: '.$data['deliveredBy'].')';
			  				$this->saveComment($orderId, 'SHIPMENT_STATUS', array('comment'=>$comment, 'order_status_id'=>17026));
	                    }
	                    
	                    /**
	                     * Assign order to delivery person and update track, orders
	                     */
	                    
	                    $trackInfo = array('delivered_by' => $data['deliveredBy'], 'delivery_date'=>$deliveryDateTime, 'st_vehicle_no'=>$vehicle_no, 'vehicle_id'=>$vehicle_id);
	                    $this->_OrderTrack->updateTrackDetail($data['ids'], $trackInfo);

	                    $this->_orderModel->updateOrder($data['ids'], array('order_status_id'=>'17026'));
	                    /**
	                    	* Updating product status
	                    	*/
	                   	foreach ($data['ids'] as $orderId) {
							$checkPrdStatus = DB::select(DB::raw("SELECT GROUP_CONCAT(product_id) AS productids FROM gds_order_products WHERE gds_order_id= $orderId AND order_status=17021"));
							$checkPrdStatus = $checkPrdStatus[0]->productids;
							$orderStatusId = 17026;
							$this->_orderModel->updateProductStatus($orderId, $checkPrdStatus, $orderStatusId);
                        }
	                    /**
	                     * Send sms to retailers
	                     */
	                    $this->_OrderTrack->sendSmsToRetails($data['ids']);
	                   	                    
	                }
	                DB::commit();
	                return Response::json(array('status'=>200, 'message'=>'Success'));
	            }
	          }
	          catch(Exception $ex) {
	          	DB::rollback();	
	              Log::error($ex->getMessage().' '.$ex->getTraceAsString());
	              return Response::json(array('status'=>400, 'message'=>'Failed'));
	          }
	    }
	
  	public function cancelOrderItem($orderId, $productsArr, $orderStatusId='17015', $itemStatus=false, $user='default') {

  		//DB::beginTransaction();Commenting transactions since parent function already had transactions Dated:30th Jan,2020

  		try{
  			/**
  			 * 17015- CANCELLED BY EBUTOR
  			 * 17009- CANCELLED BY CUSTOMER
  			 */
  			$query = DB::table('gds_orders')->select('le_wh_id as whareHouseID')->where('gds_order_id',$orderId)->first();
        	$whId = isset($query->whareHouseID) ? $query->whareHouseID: '';
        	$whdetails =$this->_roleRepo->getLEWHDetailsById($whId);
        	$statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
  			if(is_array($productsArr) && count($productsArr) > 0) {
  				$cancel_code = $this->_orderModel->getRefCode('SC',$statecode);
  				// Log::info("cancelorderitem SC COde gen".$cancel_code);
	  			$gridData = array('gds_order_id'=>$orderId, 
	  							'cancel_status_id'=>$orderStatusId, 
	  							'cancel_code'=>$cancel_code);
				$cancelGridId = $this->_orderModel->cancelGrid($gridData,$user);
				$order = $this->_orderModel->getOrderStatusById($orderId);
				$le_wh_id = isset($order->le_wh_id) ? $order->le_wh_id : 0;
				//$cancelledArr = $this->_orderModel->getCancelledQtyByOrderId($orderId);

  				if($cancelGridId) {
  					$invLogs = array();
  					$totCancelQty = 0;
  					$totCancelAmt = 0;  				
					foreach($productsArr as $product){
						$product_id = $product['product_id'];
						//$cancelQty = isset($cancelledArr[$product_id])?$cancelledArr[$product_id]:0;
						$qty = $product['qty'];
						// if($cancelQty < 0){
						// 	return false;
						// }
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
//							log::info($orderStatusId);
							$this->_orderModel->updateProductStatus($orderId, $product_id, $orderStatusId);
						}

						$totCancelQty = $totCancelQty + $qty;
						$totCancelAmt = $totCancelAmt + $total_price;
					}
										
					$this->_Inventory->updateInventory($productsArr, $orderId, 'substract', $cancel_code);

					$this->_cancelModel->updateCancelGrid($cancelGridId, array('cancel_value'=>$totCancelAmt, 'cancel_qty'=>$totCancelQty));

					// send sms to customer & field force					
		  			//$this->sendCancelSMS($orderId, $cancelGridId);

		  			// send email to customer, field force and logistic manager's teams
		  			//$this->sendCancelEmail($orderId);
  				}
				
  		//		DB::commit();
  				return true;
  			}
  		}
  		catch(Exception $e) {
  		//	DB::rollback();
        	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
  		}
  	}

  	public function createShipmentByOrderId($orderId, $productsArr) {
  		//DB::beginTransaction();Commenting this transaction since this parent's function is having transactions Dated:30th January,2020
  		try{

  			$orderStatus = $prdStatusId = '17005';

  			$shipGridId = $this->_orderModel->saveShipmentGrid(array('gds_order_id'=>$orderId, 'status_id'=>$orderStatus));
  			
  			if($shipGridId) {
  				$cancelArr = $this->_orderModel->getCancelledQtyByOrderId($orderId);
  				$canProdArr = array();
  				$prodStatusArr = array();
  				$totShipQty = 0;
				$ship_value = 0;
    
  				foreach($productsArr as $product) {
  					$productId = $product['product_id'];
  					$orderedQty = $product['orderedQty'];
  					$shipQty = $product['pickedQty'];
  					$canQty = isset($cancelArr[$productId]) ? (int)$cancelArr[$productId] : 0;

  					$pendingQty = (int)($orderedQty - ($shipQty + $canQty));
  					//log::info($pendingQty);
  					$cancel_reason_id = isset($product['cancel_reason_id']) ? $product['cancel_reason_id'] : 0;
  					if($pendingQty) {
  						$canProdArr[] = array('product_id'=>$productId, 'qty'=>$pendingQty, 'cancel_reason_id'=>$cancel_reason_id);
  						$prdStatusId = ($orderedQty == $pendingQty) ? '17015' : '17013';
  					//	log::info($prdStatusId);
  						//$prodStatusArr[$prdStatusId] = $prdStatusId;
  					}
  					else{
  					//	log::info($prdStatusId);
  						$prdStatusId = $orderStatus;
  					}
  					if($shipQty) {
  						$priceArr = $this->getUnitPriceOfOdrItems($orderId, array($productId));

						$unit_price = isset($priceArr[$productId]['unitPrice']) ? $priceArr[$productId]['unitPrice'] : 0;
						
						$total_price = ($unit_price * $shipQty);

  						$data = array('gds_ship_grid_id'=>$shipGridId, 
									'product_id'=>$productId,
									'qty'=>$shipQty,
									'unit_price'=>$unit_price,
									'total_price'=>$total_price, 
									'status_id'=>$orderStatus
								);
	  					//print_r($data);
	  					//log::info($data);
	  					//log::info($prdStatusId);
						$this->_orderModel->saveShipmentGridItem($data);
						$this->_orderModel->updateProductStatus($orderId, $productId, $prdStatusId);

						$totShipQty = $totShipQty + $shipQty;
						$ship_value = $ship_value + $total_price;
  					}elseif($pendingQty){
  						//log::info($canQty);
  						$this->_orderModel->updateProductStatus($orderId, $productId, $prdStatusId);
  					}  					
  				}

  				$this->_shipmentModel->updateShipmetGrid($shipGridId, array('ship_qty'=>$totShipQty, 'ship_value'=>$ship_value));

  				/**
  				 * Create cancellation
  				 */
  				
  				if(count($canProdArr)) {
  					$this->cancelOrderItem($orderId, $canProdArr);
  				}

  				/**
  				 * Create invoice
  				 */
  				$comment = 'Order Id: '.$orderId.' marked as ready to dispatch from app';
  				$this->saveComment($orderId, 'SHIPMENT_STATUS', array('comment'=>$comment, 'order_status_id'=>$orderStatus));
  				  				
  				$this->_orderModel->updateOrderStatusById($orderId, $orderStatus);
  				//Utility::putLog('shipment_api', 'create Shipment : shipGridId: '.$shipGridId, 'so_logs');
  				
  			//	DB::commit();  				
  				return $shipGridId;
  			}
  			else {
  				return false;  			
	  		}	  		
  		}
  		catch(Exception $e) {
  			//DB::rollback();
        	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
  		}  
  	}

  	public function createShipmentByPicklistApiAction($data) {
  		$getflag=json_decode($data['orderdata'],true);
  		$setorgettransactionflag=isset($getflag['orderdata']['needTransactions'])?$getflag['orderdata']['needTransactions']:true;
  		if($setorgettransactionflag){
  			DB::beginTransaction();
  		}
  		try{
  			if(empty($data)) {
  				return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
  			}

  			$postData = json_decode($data['orderdata'], true);
  			$MongoApiLogsModel = new MongoApiLogsModel();
            $mongoInsertId = $MongoApiLogsModel->insertApiLogsRequest('createShipmentByPicklistApi', $data);

  			$allowShipStatusArr = array('17020');
   			//print_r($postData);die;
  			$productsArr = $postData['orderdata']['products'];
  			$orderId = isset($postData['orderdata']['order_id']) ? (int)$postData['orderdata']['order_id'] : 0;
  			$reasonCode = isset($postData['orderdata']['cancel_reason_id']) ? $postData['orderdata']['cancel_reason_id'] : '';
  			
  			$cfc_cnt =  isset($postData['orderdata']['cfc']) ? $postData['orderdata']['cfc'] : 0;
  			$bags_cnt =  isset($postData['orderdata']['bags']) ? $postData['orderdata']['bags'] : 0;
  			$crates_cnt =  isset($postData['orderdata']['carats']) ? $postData['orderdata']['carats'] : 0;
  			
  			// set picker id in session
  			$pickerId = isset($postData['orderdata']['picker_id']) ? (int)$postData['orderdata']['picker_id'] : 0;

  			if($pickerId) {
  				Session::put('userId', $pickerId);
  			}
  			  			
  			$trackData = array('cfc_cnt'=>$cfc_cnt, 'bags_cnt'=>$bags_cnt, 'crates_cnt'=>$crates_cnt, 'picked_by'=>$pickerId, 'picked_date'=>date('Y-m-d H:i:s'));
  			  			
			// code start for data validation

  			$errorInvMsg = array();
  			$errorPrdMsg = array();
  			$errorPrdQtyMsg = array();
  			$orderInfo = $this->_orderModel->getOrderInfoById($orderId, 'le_wh_id');
  			if(!is_object($orderInfo)) {
  				return Response::json(array('Status' => 404, 'Message' => 'Please enter valid order id.')); 
  			}
  			$shippInfo = $this->_shipmentModel->getShipmentGridByOrderId($orderId);
  			$shippId = isset($shippInfo->gds_ship_grid_id) ? (int)$shippInfo->gds_ship_grid_id : 0;

  			if($shippId) {
  				return Response::json(array('Status' => 404, 'Message' => 'Shipment already created.')); 
  			}

  			$le_wh_id = isset($orderInfo->le_wh_id) ? (int)$orderInfo->le_wh_id : 0;
			if(!$le_wh_id) {
  				return Response::json(array('Status' => 404, 'Message' => 'Warehouse ID is empty in order.')); 
  			}

  			if(is_array($productsArr) && count($productsArr) > 0) {
  				foreach ($productsArr as $product) {
  					$pickedQty = isset($product['pickedQty']) ? (int)$product['pickedQty'] : 0;
  					if($pickedQty) {
  						$product_id = isset($product['product_id']) ? (int)$product['product_id'] : 0;

	  					$prdInfo = $this->_orderModel->getProductByOrderIdProductId($orderId,$product_id);

	  					$product_id = isset($prdInfo->product_id) ? (int)$prdInfo->product_id : 0;
	  					$orderedQty = isset($prdInfo->qty) ? (int)$prdInfo->qty : 0;
	  					// $orderwh=DB::select(DB::raw("select is_binusing from  legalentity_warehouses l  where l.le_Wh_id = ".$le_wh_id));

						$pname = isset($prdInfo->pname) ? $prdInfo->pname : '';
						$pname = str_replace(array('"', "'"), '', $pname);

						// if($orderwh[0]->is_binusing == 1){
	                	$invArr = $this->_orderModel->getInventory($product_id, $le_wh_id);
						$soh = isset($invArr->soh) ? (int)$invArr->soh : 0;
						if($soh > 0 && ($pickedQty > $soh)) {
							$errorInvMsg[] = $pname;
						}
						else if($soh <= 0){
							$errorInvMsg[] = $pname;
						}
		                // }
		                
						if(!$product_id) {
	  						$errorPrdMsg[] = $pname;
	  					}
	  					if($pickedQty > $orderedQty) {
							$errorPrdQtyMsg[] = $pname;
						}
	  					
	  					
  					}
  				}
  			}
  			
  			if(is_array($errorPrdQtyMsg) && count($errorPrdQtyMsg) > 0) {
  				$msg = 'Pick qty can not be greater than order qty. Please verify '.implode($errorPrdQtyMsg, ', ').' product.';
  				return Response::json(array('Status' => 404, 'Message' => $msg)); 
  			}

  			if(is_array($errorPrdMsg) && count($errorPrdMsg) > 0) {
  				$msg = 'Sorry, you are creating shipment of wrong product.';
  				return Response::json(array('Status' => 404, 'Message' => $msg)); 
  			}

  			if(is_array($errorInvMsg) && count($errorInvMsg) > 0) {
  				$msg = Lang::get('salesorders.alertInventory');
				$msg = str_replace('{SKU}', implode(', ', $errorInvMsg), $msg);
  				return Response::json(array('Status' => 404, 'Message' => $msg)); 
  			}
  			//print_r($errorPrdMsg);die;

  			if(!$orderId) {
  				return Response::json(array('Status' => 404, 'Message' => 'Please enter valid order id.')); 
  			}

  			$statusArr = $this->_orderModel->getOrderStatusById($orderId);
			$statusId = isset($statusArr->order_status_id) ? (int)$statusArr->order_status_id : 0;
			
			if(!in_array($statusId, $allowShipStatusArr)) {
				return Response::json(array('Status' => 404, 'Message' => 'You can not create shipment. Make sure order status should be picklist generated.')); 
			}

			// code end for data validation
  			
  			/**
  			 * Cancel full Orders
  			 */
  			if(is_array($productsArr) && count($productsArr) <=0) {
  				$prdArr = $this->_orderModel->getProductByOrderId($orderId);
  				$cancelArr = $this->_orderModel->getCancelledQtyByOrderId($orderId);
				$productsArr = array();
				if(is_array($prdArr)) {
					foreach($prdArr as $product){

						$orderedQty = $product->qty;
	  					$shipQty = 0;
	  					$canQty = isset($cancelArr[$product->product_id]) ? (int)$cancelArr[$product->product_id] : 0;

	  					$pendingQty = (int)($orderedQty - ($shipQty + $canQty));
	  					if($pendingQty) {
	  						$productsArr[] = array('product_id'=>$product->product_id, 'qty'=>$pendingQty,'cancel_reason_id'=>$reasonCode);
	  					}						
					}
					if(count($productsArr)) {
						$this->cancelOrderItem($orderId, $productsArr, '17015', true);
					}
				}
				$canReason = $this->_masterLookup->getMasterLookupByStatus($reasonCode, 'Cancel Reasons');
				$cancel_reason = isset($canReason->master_lookup_name) ? $canReason->master_lookup_name : '';	
				$this->_orderModel->updateOrderStatusById($orderId, '17015');
				$this->saveComment($orderId, 'Cancel Status', array('comment'=>$cancel_reason, 'order_status_id'=>'17015'));
				
				// revert cashback if canel qty = order qty
				$orderedQty = $this->_orderModel->getOrderedQtyByOrderId($orderId);
				$cancelledQty = $this->_orderModel->getCancelledTotalQtyByOrderId($orderId);
				if($cancelledQty == $orderedQty){
					$this->revertCashbackFromOrder($orderId,17015,"Order Cancelled While Picking!");
				}
				if($setorgettransactionflag){
					DB::commit();
				}
				$MongoApiLogsModel->updateResponse($mongoInsertId, array('Status' => 200, 'Message' => 'Successfully.'), $orderId);
  				return Response::json(array('Status' => 200, 'Message' => 'Order has been cancelled successfully.'));
  			}
  			log::info('orderidinorderscontroller'.$orderId);
  			if($orderId) {
  				log::info('createShipmentByOrderIdfunction'.$orderId);
  				$shipGridId = $this->createShipmentByOrderId($orderId, $productsArr);
				/**
  				 * Update gds_order_track
  				 */
  				
  				$this->_orderModel->saveOrderTrackData($orderId, $trackData);

				if($shipGridId) {
					if($setorgettransactionflag){
						DB::commit();
					}
					$response = array('Status' => 200, 'Message' => Lang::get('salesorders.createdShipment'), 'Id'=>$shipGridId);
	  			}
	  			else {
	  				if($setorgettransactionflag){
	  					DB::rollback();
	  				}
	  				$response = array('Status' => 404, 'Message' => 'Oops something went wrong');
	  			}

	  			$MongoApiLogsModel->updateResponse($mongoInsertId, $response, $orderId);
	  			return Response::json($response);   
  			}else{
  				DB::rollback();
  				$response = array('Status' => 404, 'Message' => 'Order ID missing');
  			}
  		}
  		catch(Exception $e) {
  			if($setorgettransactionflag){
  				DB::rollback();
  			}	
  			return Response::json(array('Status' => 404, 'Message' => 'Oops something went wrong')); 
  		}
  	}

	public function getInvoiceDueAmount($invoiceId) {

			$this->_invoiceModel->getInvoiceDueAmount($invoiceId);

	}

	public function sendCancelSMS($orderId, $cancelGridId) {
	  	try {
	  		
	  		$orderedQty = $this->_orderModel->getOrderedQtyByOrderId($orderId);
			$cancelledQty = $this->_orderModel->getCancelledTotalQtyByOrderId($orderId);
			
			$message = Lang::get('sms.orderPartialCancelled');
			if($orderedQty == $cancelledQty) {
				$message = Lang::get('sms.orderCancelled');	
			}
	  		
	  		$currency_code = Lang::get('salesorders.symbol');
	  		$order = $this->_orderModel->getOrderInfoById($orderId, array('orders.phone_no','orders.order_date','orders.total','orders.currency_id' ,'orders.order_code','orders.created_by'));

	  		$newDate = date("d-m-Y", strtotime($order->order_date));
	  		
	    	$cancelValue = $this->_orderModel->getCancelledValueByOrderId($orderId, $cancelGridId);
	    	$cancelValue = number_format($cancelValue, 2);
	  		$orderValue = number_format($order->total, 2);
	    	
	    	if(isset($order->phone_no) && !empty($order->phone_no)) {
	    		$message = str_replace('{ORDER_CODE}', $order->order_code, $message);
	    		$message = str_replace('{ORDER_DATE}', $newDate, $message);
	    		$message = str_replace('{ORDER_VALUE}', $currency_code.' '.$orderValue, $message);
	    		$message = str_replace('{CANCEL_VALUE}', $currency_code.' '.$cancelValue, $message);

	    		$field_frc_no = $this->_orderModel->getFieldFrcPhoneNo($order->created_by);
	    		
	    		if(!empty($order->phone_no)) {
	    			$this->_sms->sendSMS($order->phone_no, $message);
	    		}
	    		if(!empty($field_frc_no)){
	    			$this->_sms->sendSMS($field_frc_no, $message);
	    		}
		 	}
	  	} 
	  	catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			return Response::json(array('status' => 400, 'message' => 'Failed'));
		}
	}

	public function sendCancelEmail($orderId){
		return true;
	  	try {
	  		
	  	  	 if ($orderId) {
	  	  	 		$ordercode = $this->_orderModel->getOrderInfoById($orderId, array('orders.order_code','orders.created_by','orders.email'));
	  	  	 		//echo "<pre>"; print_r($ordercode); die();
	  	  	 		$order_code = isset($ordercode->order_code) ? $ordercode->order_code : '';
	  	  	 		$to_email = $this->_orderModel->getToEmail($ordercode->created_by);
	  	  	 		$roleName = ['Logistics Manager'];
	  	  	 		$Logistics = $this->_orderModel->getUserEmailByRoleName($roleName);
	  	  	 		
	  	  	 		$emails = array();
	  	  	 		if(!empty($to_email)) {
	  	  	 			$emails[] = $to_email;
	  	  	 		}
	  	  	 		if(!empty($ordercode->email)) {
	  	  	 			$emails[] = $ordercode->email;
	  	  	 		}

	  	  	 		foreach ($Logistics as $key => $value) {
	  	  	 			$emails[] = $value->email_id;
	  	  	 		}

	  	  	 		$orderDetail = $this->getOrderDetailForEmail($orderId);
	  	  	 		$body = array('template'=>'emails.sendCancelEmail', 'attachment'=>'', 'ordercode' => $order_code, 'orderDetail'=>$orderDetail);
	  	  	 		$subject='Order #'.$order_code .' cancelled';
            		Utility::sendEmail($emails, $subject, $body);
	            	// $content = \Mail::send('emails.sendCancelEmail', ['ordercode' => $order_code, 'orderDetail'=>$orderDetail], function($message) use ($order_code,$emails) {
	             //    		$message->from('tracker@ebutor.com', 'Ebutor')->to($emails)->subject('Order #'.$order_code .' cancelled');
	            	// 	});
	            
	        }

	  	} catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			return Response::json(array('status' => 400, 'message' => 'Failed'));
		}
  	}

  	public function getOrderDetailForEmail($orderId) {
  		$data = $this->getChallanData($orderId);
  		$productReturnsArr = $this->_returnModel->getReturnedByOrderId($orderId);
		$returns = array();
		if(is_array($productReturnsArr)) {
			foreach ($productReturnsArr as $row) {
				$returns[$row->product_id] = $row->returned;
			}
		}

		$invoiceArr = $this->_orderModel->getItemInvoicedQtyByOrderId($orderId);
		$cancelArr = $this->_orderModel->getCancelledQtyByOrderId($orderId);
		$data['returns'] = $returns;
		$data['invoiceArr'] = $invoiceArr;
		$data['cancelArr'] = $cancelArr;

		$orderInfo = view('Orders::orderInfo')->with($data)->render();	
		return $orderInfo;
  	}


	public function sendMarkDeliveredSMS($orderid) {
	  	try {

	  		$message = Lang::get('sms.orderDelivered');
	  		$currency_code = Lang::get('salesorders.symbol');
	  		$order = $this->_orderModel->getOrderInfoById($orderid, array('orders.phone_no','orders.order_date','orders.total','orders.currency_id' ,'orders.order_code','orders.created_by'));
	  		$invoiceAmount = $this->_invoiceModel->getInvoicedPriceWithOrderID($orderid);
	    	if(isset($order->phone_no) && !empty($order->phone_no)) {
	    		$orderValue = number_format($invoiceAmount, 2);
	    		$order_date = date('d-m-Y', strtotime($order->order_date));
	    		$message = str_replace('{ORDER_CODE}', $order->order_code, $message);
	    		$message = str_replace('{ORDER_DATE}', $order_date, $message);
	    		$message = str_replace('{ORDER_VALUE}', $currency_code.' '.$orderValue, $message);

	    		$field_frc_no = $this->_orderModel->getFieldFrcPhoneNo($order->created_by);
	    		
	    		if(!empty($order->phone_no)) {
	    			$this->_sms->sendSMS($order->phone_no, $message);
	    		}
	    		if(!empty($field_frc_no)){
	    			$this->_sms->sendSMS($field_frc_no, $message);
	    		}
		 	}
	  	} 
	  	catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			return Response::json(array('status' => 400, 'message' => 'Failed'));
		}
	}

	public function sendMarkDeliveredEmail($orderId){
		/*return true;
	  	try {*/
	  		
	  	  	 if ($orderId) {
	  	  	 		$ordercode = $this->_orderModel->getOrderInfoById($orderId, array('orders.order_code','orders.created_by','orders.email'));
	  	  	 		//echo "<pre>"; print_r($ordercode); die();
	  	  	 		$order_code = isset($ordercode->order_code) ? $ordercode->order_code : '';
	  	  	 		$to_email = $this->_orderModel->getToEmail($ordercode->created_by);
	  	  	 		$roleName = ['Logistics Manager'];
	  	  	 		$Logistics = $this->_orderModel->getUserEmailByRoleName($roleName);
	  	  	 		
	  	  	 		$emails = array();
	  	  	 		$emails[] = $ordercode->email;
	  	  	 		if(!empty($to_email)) {
	  	  	 			$emails[] = $to_email;
	  	  	 		}

	  	  	 		foreach ($Logistics as $key => $value) {
	  	  	 			$emails[] = $value->email_id;
	  	  	 		}

	  	  	 		$orderDetail = $this->getOrderDetailForEmail($orderId);
	  	  	 		$body = array('template'=>'emails.orderDelivered', 'attachment'=>'', 'ordercode' => $order_code, 'orderDetail'=>$orderDetail);
	  	  	 		$subject='Order #'.$order_code .' delivered';
	  	  	 		log::info('órder delivery mail');
	  	  	 		log::info($emails);
            		Utility::sendEmail($emails, $subject, $body);
	            	// $content = \Mail::send('emails.orderDelivered', ['ordercode' => $order_code, 'orderDetail'=>$orderDetail], function($message) use ($order_code,$emails) {
	             //    		$message->from('tracker@ebutor.com', 'Ebutor')->to($emails)->subject('Order #'.$order_code .' delivered');
	            	// 	});

	        }

	  	/*} catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			return Response::json(array('status' => 400, 'message' => 'Failed'));
		}*/
  	}

  	public function stockTransfer() {
  		DB::beginTransaction();
  		try{
  			$data = Input::all();
          	//print_r($data);die;

	          $statusArr = array();
	          if(isset($data['statusCodes']) && is_array($data['statusCodes'])) {
	              $statusArr = array_unique($data['statusCodes']);    
	          }
	          //print_r($statusArr);die;
	        if($data['transfer_type']=='dctohub' && count($statusArr) > 1) {
	            $message = 'You have selected orders with different status. Make sure order status should be same.';
	            return Response::json(array('status'=>400, 'message'=>$message));
	        }
	        else if($data['transfer_type']=='hubtodc' && count($statusArr) > 2) {
	            $message = 'You have selected orders with different status. Make sure order status should be same.';
	            return Response::json(array('status'=>400, 'message'=>$message));
	        }
	        else if(isset($statusArr[0]) && $data['transfer_type']=='dctohub' && ($statusArr[0] != '17021')) {
	            $message = "Sorry, you can not mark as stock transfer. Make sure order status should be invoice.";
	            return Response::json(array('status'=>400, 'message'=>$message));
	        }
	        else if(isset($statusArr[0]) && $data['transfer_type']=='hubtodc' && ($statusArr[0] != '17022' && $statusArr[0] != '17023')) {
	            $message = "Sorry, you can not mark as stock transfer. Make sure order status should be returned/partially delivered.";
	            return Response::json(array('status'=>400, 'message'=>$message));
	        }
	        else {
	            
	            if(isset($data['ids']) && is_array($data['ids']) ) {
	                
	                $docket_code = $this->_orderModel->getRefCode('TR');
	                foreach ($data['ids'] as $orderId) {
	                    
	                    $transfer_status = '17024';	//stock transit dc to hub
	                    $commentType = 'Order Status';
	                    if($data['transfer_type']=='hubtodc') { // stock transit hub to dc
	                    	$transfer_status = '17027';
	                    	$commentType = 'RETURNS';
	                    }

	                    $this->_orderModel->updateStockTransfer($orderId,$data,$docket_code,$transfer_status);
	 
	                    $commentDelivered = 'Order Id: '.$orderId.' stock transferred, DE Name : '.$data['stock_delivered_by_name'].', DE Mobile : '.$data['stock_delivered_mobile'].', Vehicle Number : '.$data['stock_vehicle_number'].', Driver Name : '.$data['stock_driver_name'].',Driver Mobile : '.$data['stock_driver_mobile'];
	                    
	                    $this->saveComment($orderId, $commentType, array('comment'=>$commentDelivered, 'order_status_id'=>$transfer_status));
	                }

	                /**
	                 * Change order status
	                 */
	               
	                if($data['transfer_type']=='dctohub') {
	                	$this->_orderModel->updateOrder($data['ids'], array('order_status_id'=>'17024'));
	                }
                        
                        $statusId = ($data["transfer_type"] == "hubtodc") ? 137003 : 137002;
                        $this->apiUpdateCrateStatus($data["ids"], $statusId, "Transaction Status");
	            }
	            DB::commit();
	            return Response::json(array('status'=>200, 'message'=>'Success'));
	        }
  		}
  		catch(Exception $e) {
  			DB::rollback();
  			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());	
  		}
          
  	}
  	
  	public function confirmStockDocket() {
  		DB::beginTransaction();
		try{
              $data = Input::all();
              #print_r($data);die;

              if(!isset($data['docket_number'])) {

	                $message = "Sorry, Please enter Dock Number.";
	                return Response::json(array('status'=>400, 'message'=>$message));

              } else {

					$result = $this->_orderModel->confirmStockDocket($data);
					//print_r($result);exit;
					
              		if(!$result) {
              			return Response::json(array('status'=>400, 'message'=>'Stock Already Received / Invalid Docket Number.'));
              		}
              		$commentType = 'Order Status';
					if($data['confirm_stock_type']=='dc') {
						$comment = 'Received stock at DC, Docket No# '.$data['docket_number'];
						$status = 17028;
						$commentType = 'RETURNS';
					} else {
						$comment = 'Received stock at Hub, Docket No# '.$data['docket_number'];
						$status = 17025;
						$commentType = 'Order Status';
					}
                                        
					foreach($result as $orderId) {
						$this->saveComment($orderId, $commentType, array('comment'=>$comment, 'order_status_id'=>$status));
					}
                                        $this->apiUpdateCrateStatus($result, 137007, "Transaction Status");

                DB::commit();
					return Response::json(array('status'=>200, 'message'=>'Stock received successfully'));	
              }
		}
		catch(Exception $e) {
			DB::rollback();
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			return Response::json(array('status'=>400, 'message'=>'Failed'));
		}

  	}
    

    public function verificationListAction($orderId) {
       try{
           $verificationListArr = $this->_orderModel->getVerificationListById($orderId);                
           $dataArr = array();

           if(is_array($verificationListArr)) {
               $slno = 1;
               foreach($verificationListArr as $verificationArr) {

                   $dataArr[] = array('SNo'=>$slno,
                       'ContainerName'=>$verificationArr->container_name,
                       'FilePath'=>'<a target="_blank" href="'.$verificationArr->file_path.'" ><img src="'.$verificationArr->file_path.'" height="40px" width="40px" /></a>',
                       'VerifiedBy'=>$verificationArr->verified_by,
                       'CreatedOn'=>date("d-m-Y H:i:s", strtotime($verificationArr->created_at))
                       );
                   $slno = $slno+1;

               }
           }

           return Response::json(array('data'=>$dataArr, 'totalVerification'=>count($verificationListArr)));
       }
       catch(ErrorException $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
       }
   }

    public function apiUpdateCrateStatus($orderIds, $statusId, $statusType) {
        $crates = array();
        $le_wh = '';
        if($statusType == "Status"){
            $status = $statusId;
            $transaction = "";
        } else if($statusType == "Transaction Status") {
            $status = "";
            $transaction = $statusId;
        }

        foreach($orderIds as $eachOrderId){
            $crateDetails = $this->_crateManagement->getOrderCrate($eachOrderId);
            if(!empty($crateDetails)){
                foreach ($crateDetails as $eachDetail) {
                    !empty ($eachDetail['le_wh_id'] != '') ? $le_wh = $eachDetail['le_wh_id'] : $le_wh = '';
                    $temp = array();
                    $temp['crate_code'] = $eachDetail['container_barcode'];
                    $temp['status'] = $status;
                    $temp['transaction_status'] = $transaction;
                    array_push($crates, $temp);
                }
            }
        }

        $callDataArr = array();
        $callDataArr["lp_token"] = $this->_crateManagement->getLpToken(Session("userId"));
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
    
    public function revertCashbackFromOrder($orderId,$order_status_id,$comment){
    	// checking order cahback and removing cashback if it not matched 
    	$order_ecash_added = $this->_orderModel->getEcashByOrderId($orderId,143002);
        $walletCashBack = 0;
        if($order_ecash_added > 0){
        	$order_data = $this->_orderModel->getOrderInfo(array($orderId),array('gds_order_id','order_code','created_at','le_wh_id','is_self','cust_le_id'));
            $userId  = $this->_orderModel->getOrderUserId($orderId);
            $cust_le_id = isset($order_data[0]->cust_le_id) ? $order_data[0]->cust_le_id : 0;
            $eCash = ['cashback'=> DB::raw('(cashback-' . $order_ecash_added . ')')];
            $this->_paymentModel->updateEcash($userId, $eCash);
            $userEcash = $this->_paymentModel->getUserEcash($userId);
        	$finalEcashAmount =isset($userEcash->cashback)?$userEcash->cashback:0;
            $ecashHistory = ['user_id'=>$userId,
                'legal_entity_id'=>$cust_le_id,
                'order_id'=>$orderId,
                'delivered_amount'=>0,
                'cash_back_amount'=>$order_ecash_added,
                'balance_amount'=>$finalEcashAmount,
                'transaction_type'=>143001,
                'transaction_date'=>date('Y-m-d H:i:s'),
                'order_status_id'=>$order_status_id,
                'comment'=>$comment
                ];
            $this->_paymentModel->saveEcashHistory($ecashHistory); 
        }
        return true;
    }

    public function getBatchesByData($product_id,$le_wh_id,$req_qty,$offset=0,$batch_limit,$batches=[]){
        $batch_data = DB::table("inventory_batch")
                ->where("le_wh_id",$le_wh_id)
                ->where("product_id",$product_id)
                ->where("qty",">",0)
                ->skip($offset)
                ->limit($batch_limit)
                ->orderby("inward_id","ASC")
                ->get()->all();
        $offset = $batch_limit;
        $batch_limit = $batch_limit + 10;
        foreach ($batch_data as $key => $value) {
            # code...
            if($req_qty > 0){
                $batches[] = $value;
            }
            else{
                break;
            }
            $req_qty -= $value->qty;
            if($req_qty <= 0 ){
                break;
            }
        }
        if($req_qty > 0 && count($batch_data)){
            $this->getBatchesByData($product_id,$le_wh_id,$req_qty,$batch_limit,$batch_limit,$batches);
        }else{
            $batches = $batches;
        }
        return $batches;
    }

    public function getBatchesByDataDND($product_id,$le_wh_id,$req_qty,$offset=0,$batch_limit,$batches=[],$add_cond = 0){
    	$cond = "dnd_qty";
    	if($add_cond == 1){
    		$cond = "dnd_qty + ($req_qty)";
    	}
        $batch_data = DB::table("inventory_batch")
                ->where("le_wh_id",$le_wh_id)
                ->where("product_id",$product_id)
                ->whereRaw($cond . ">0")
                ->skip($offset)
                ->limit($batch_limit)
                ->orderby("inward_id","ASC")
                ->get();
        $offset = $batch_limit;
        $batch_limit = $batch_limit + 10;
        foreach ($batch_data as $key => $value) {
            # code...
            if($req_qty > 0){
                $batches[] = $value;
            }
            else{
                break;
            }
            $req_qty -= $value->dnd_qty;
            if($req_qty <= 0 ){
                break;
            }
        }
        if($req_qty > 0 && count($batch_data)){
            $this->getBatchesByDataDND($product_id,$le_wh_id,$req_qty,$batch_limit,$batch_limit,$batches);
        }else{
            $batches = $batches;
        }
        return $batches;
    }

    public function getBatchesByDataDIT($product_id,$le_wh_id,$req_qty,$offset=0,$batch_limit,$batches=[],$add_cond = 0){
    	$cond = "dit_qty";
    	if($add_cond == 1){
    		$cond = "dit_qty + ($req_qty)";
    	}
        $batch_data = DB::table("inventory_batch")
                ->where("le_wh_id",$le_wh_id)
                ->where("product_id",$product_id)
                ->whereRaw($cond . ">0")
                ->skip($offset)
                ->limit($batch_limit)
                ->orderby("inward_id","ASC")
                ->get();
        $offset = $batch_limit;
        $batch_limit = $batch_limit + 10;
        foreach ($batch_data as $key => $value) {
            # code...
            if($req_qty > 0){
                $batches[] = $value;
            }
            else{
                break;
            }
            $req_qty -= $value->dit_qty;
            if($req_qty <= 0 ){
                break;
            }
        }
        if($req_qty > 0 && count($batch_data)){
            $this->getBatchesByDataDIT($product_id,$le_wh_id,$req_qty,$batch_limit,$batch_limit,$batches);
        }else{
            $batches = $batches;
        }
        return $batches;
    }
                
    public function editOrder()
    {
    	$data = Input::all();
    	//log::info($data);
    	$orderId=$data['order_ids'];
    	$orders = $this->_orderModel->getOrderDetailById($orderId);
   		$statuscount = $this->_orderModel->getOrderStatusFromOrderId($orderId);
    	if($statuscount == 0){
			Redirect::to('/salesorders/index')->send();
    	}
    	
    	//order_status_id
    	$orderdetails = $this->_orderModel->getProductByOrderId($orderId);
    	//log::info($orderdetails);
		$taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);

		$taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
		$taxSummary = isset($taxSummaryArr['summary']) ? $taxSummaryArr['summary'] : '';
		$productTaxArr = isset($taxSummaryArr['item']) ? $taxSummaryArr['item'] : '';
		$taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
		$whInfo = $this->_leModel->getWarehouseById($orders->le_wh_id);
		$hubInfo = $this->_leModel->getWarehouseById($orders->hub_id);
		$retailerinfo = $this->_orderModel->getRetailerInfo($orders->phone_no);
        $userInfo = '';
		if($orders->created_by) {
			$userInfo = $this->_leModel->getUserById($orders->created_by);
		}
				$retailerinfo = json_decode(json_encode($retailerinfo));

    	return view("Orders::reorder")
    	    ->with('orderdata',$orders)
    		->with('products',$orderdetails)
    		->with('productTaxArr', $productTaxArr)
            ->with('taxSummary', $taxBreakup)
            ->with('whInfo', $whInfo)
            ->with('hubInfo',$hubInfo)
            ->with('userInfo',$userInfo)
            ->with('retailerInfo',$retailerinfo)
            ->with('order_id',$orderId);
    }
    public function getList()
    {
    	$data = Input::all();
    	$model = $this->_orderModel->getProductList($data['term'],$data['le_wh_id'],$data['customer_type']);
    	//log::info($model);
    	return $model;
    }
    public function getPacks()
    {
    	$input = Input::all();
    	$id =$input['product_id'];
    	$wh_id = $input['le_wh_id'];
    	$customer_type = $input['customer_type'];
    	$data = $this->_orderModel->getPacksData($id,$wh_id,$customer_type);
    	$inventory = $this->_orderModel->getInventory($id,$wh_id);
    	$soh = isset($inventory->soh) ? (int)$inventory->soh : 0;
    	$order_qty = isset($inventory->order_qty) ? (int)$inventory->order_qty : 0;
    	$reserved_qty = isset($inventory->reserved_qty) ? (int)$inventory->reserved_qty : 0;
    	$available_inv = $soh-($order_qty+$reserved_qty);
    	$result['data'] = $data;
    	$result['available_inv']= $available_inv;
    	return $result;
    	# code...
    }
    public function addProductIntoOrder(Request $request)
    {
    	$input = Input::all();
    	$statuscount = $this->_orderModel->getOrderStatusFromOrderId($input['order_id']);
    	if($statuscount == 0){
			return Response::json(array('status'=>false, 'message'=>'Sorry, you can not edit order. Make sure order status should be open.'));
    	}else{

    		$cancelcount = $this->_orderModel->getCancellationData($input['order_id']);

    		if($cancelcount > 0){
    			return Response::json(array('status'=>false, 'message'=>'You are not allowed to edit as there are cancel items in this order!'));
    		}else{
	    		$addProduct = $this->_orderModel->addProductToOrder($input['product_data'],$input['order_id'],$input['le_wh_id'],$input['product_id'],$input['product_title'],$input['sku'],$input['mrp'],$input['total_qty'],$input['customer_type']);
	    		return $addProduct;
	    	}
    	}    	
    }
    public function deleteOrder()
    {
    	$input = Input::all();
    	$data =$input['product_data'];
    	$statuscount = $this->_orderModel->getOrderStatusFromOrderId($input['order_id']);
    	if($statuscount == 0){
			return Response::json(array('status'=>false, 'message'=>'Sorry, you can not edit order. Make sure order status should be open.'));
    	}else{
    		$cancelcount = $this->_orderModel->getCancellationData($input['order_id']);

    		if($cancelcount > 0){
    			return Response::json(array('status'=>false, 'message'=>'You are not allowed to edit as there are cancel items in this order!'));
    		}else{
    			$res = $this->_orderModel->delteProductFromOpenOrder($input['product_id'],$input['order_id'],$data,$input['le_wh_id'],$input['customer_type']);
		    	$freebeedel = DB::table('gds_order_products')
						        ->where('gds_order_id',$input['order_id'])
						        ->where('parent_id',$input['product_id'])
						        ->get()->all();
		        $freebeedel = json_decode(json_encode($freebeedel),1);
		        if(count($freebeedel) >0){
		        	$freebeedel =$freebeedel[0];
					$this->_orderModel->delteProductFromOpenOrder($freebeedel['product_id'],$input['order_id'],$freebeedel,$input['le_wh_id'],$input['customer_type']);
		        }				
		        return Response::json(array('status'=>$res, 'message'=>'Deleted successfully'));
    		}
    		
    	}
    	
    }
    public function checkCancellations()
    {
    	$data = Input::all();
    	$orderId=$data['order_id'];
    	$cancelcount = $this->_orderModel->getCancellationData($orderId);

    	return Response::json(array('count' => $cancelcount ));

    }
}
