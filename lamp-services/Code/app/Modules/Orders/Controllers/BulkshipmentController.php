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
use App\Modules\Orders\Controllers\OrdersController;

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
use App\Modules\Roles\Models\Role;

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Utility;

class BulkshipmentController extends BaseController {

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
	protected $_orderController;
	protected $_Inventory;
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
		$this->_paymentModel = new PaymentModel();
		$this->_OrderTrack = new OrderTrack();
		$this->_cancelModel = new CancelModel();
		$this->_orderController = new OrdersController();
		$this->_Inventory = new Inventory();
		$this->_roleModel=new Role();

		$this->_commentTypeArr = array('17'=>'Order Status', 'SHIPMENT_STATUS', 'INVOICE_STATUS', 'Cancel Status', '66'=>'REFUNDS', '67'=>'RETURNS');		
    }

    
	/**
	 * [bulkShipmentAction description]
	 * @return [type] [description]
	 */
	
	public function bulkShipmentAction() {
		try{

			$hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD003');
			if($hasAccess == false) {
					return View::make('Indent::error');
			}

			$orderIdsArr = Session::get('orderIds');
			$orderIds = (is_array($orderIdsArr) && count($orderIdsArr) > 0) ? $orderIdsArr[0] : 0;
			
			$tracksArr = $this->_OrderTrack->getOrderTrackDetails($orderIds);
			$picklistArr = array();
			$ordersArr = $this->_orderModel->getProductByOrderId($orderIds);
			$canReasonArr = $this->_masterLookup->getMasterLookupByCategoryName('Cancel Reasons');
			$shopNameArr = array();
			$shippedArr = array();
			$canceledArr = array();
			$orderIdsArr = array();
			$cancelReasonArr = array();

			if(is_array($ordersArr)) {
				foreach($ordersArr as $order) {
					$key = !empty($order->order_code) ? $order->order_code : $order->gds_order_id;
					$shopNameArr[$key] = $order->shop_name;
					$picklistArr[$key][] = (object)$order;
					$shippedQty = $this->_orderModel->getShippedQtyByOrderIdAndProductId($order->gds_order_id, $order->product_id);

					$shippedArr[$order->gds_order_id][$order->product_id] = (int)$shippedQty;

					//$canceledQty = $this->_orderModel->getCancelledProductqty($order->gds_order_id, $order->product_id);

					$ordCanReason = $this->_cancelModel->getCancelledQtyWithReason($order->gds_order_id, $order->product_id);
					
					$cancelReason = isset($ordCanReason->cancel_reason_id) ? $ordCanReason->cancel_reason_id : 0;
					$canceledQty = isset($ordCanReason->cancelledQty) ? $ordCanReason->cancelledQty : 0;

					$canceledArr[$order->gds_order_id][$order->product_id] = (int)$canceledQty;
					$orderIdsArr[$key] = $order->gds_order_id;
					$cancelReasonArr[$order->gds_order_id][$order->product_id] = $cancelReason;
				}
			}
			$allStatusArr = $this->_masterLookup->getAllOrderStatus();
			//echo "<pre>"; print_r($allStatusArr); die;
			$couriers = $this->_orderModel->getCouriers();
			//$deliveryUsers 		= 	$this->_orderModel->getUsersByRoleName(array('Delivery Executive'));
			//$deliveryUsers 		= 	$this->_roleRepo->getUsersByFeatureCode('DELR002');
			//print_r($ordersArr);die;
			$user=Session::get('userId');
            $dcList = $this->_roleModel->getWarehouseData($user, 6);
            $dcList = json_decode($dcList,true);
             if(isset($dcList['118002'])){
                $parentHubdata=explode(",",$dcList['118002']);
            }else{
                $parentHubdata=[];
            }
			$deliveryUsers = $this->_roleRepo->getUsersByFeatureCodeWithoutLegalentity($parentHubdata,2);
			return View::make('Orders::bulkshipment')
													->with('shopNameArr', $shopNameArr)
													->with('ordersArr', $picklistArr)
													->with('shippedArr', $shippedArr)
													->with('canceledArr', $canceledArr)
													->with('couriers', $couriers)
													->with('orderIdsArr', $orderIdsArr)
													->with('tracksArr', $tracksArr)
													->with('deliveryUsers', $deliveryUsers)
													->with('cancelReasonArr', $cancelReasonArr)
													->with('canReasonArr', $canReasonArr)
													->with('orderStatus', $allStatusArr);
			}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	/**
  	 * 
  	 */

	public function saveBulkShipmentAction() {
		DB::beginTransaction();
		try{
			
			$postData = Input::all();
			//print_r($postData);die;
			/**
			 * Create shipment as Ready to Dispatch
			 *
			 * Status Code
			 * Ready to Dispatch - 17005
			 * Shipped - 17006
			 * DELIVERED - 17007
			 * INVOICE - 17021
			 * 
			 */
			
			if(isset($postData['btnPicked']) && $postData['btnPicked'] == 'Ready to Dispatch') {
				$errorMsg = $this->validateData($postData);
				if(!empty($errorMsg)) {
					return Response::json(array('status' => 400, 'message' => $errorMsg));
				}
				else {
					$this->saveBulkShipment($postData, '17005');					
					$this->saveBulkCancel($postData, '17015');
					$this->changeProductStatus($postData, '17005');
					DB::commit();
					return Response::json(array('status' => 200, 'message' => Lang::get('salesorders.createdShipment')));
				}
			}

			/**
			 * Create shipment as shipped
			 */
			
			if(isset($postData['btnInvoice']) && $postData['btnInvoice'] == 'Invoice') {
				$errorMsg = $this->validateData($postData, 'invoice');
				
				if(!empty($errorMsg)) {
					return Response::json(array('status' => 400, 'message' => $errorMsg));
				}
				else {
					$this->saveBulkShipment($postData, '17021');
					$this->saveBulkCancel($postData, '17015');
					//Log::info("Bulk shipment before invoice");
					$this->createBulkInvoiceNew($postData);
					//Log::info("Bulk shipment after invoice");
					//$this->changeProductStatus($postData, '17021');
					//Log::info("Bulk shipment after status update");
					DB::commit();
					return Response::json(array('status' => 200, 'message' => Lang::get('salesorders.createdShipment'),'gds_ship_grid_id'=>''));
				}
			}			
		}
		catch(\Exception $e) {
			DB::rollback();
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			return Response::json(array('status' => 400, 'message' => 'Failed'));
		}
	}
	
  	/**
  	 * saveBulkShipment() method is used to create bulk shipment
  	 * @param [type] $[name] [<description>]
  	 */

  	public function saveBulkShipment($postData, $orderStatus, $insert=false) {
  		//DB::beginTransaction();//commented by Nishanth
  		try{
  			if(!isset($postData['products']) || !is_array($postData['products']) || count($postData['products']) <=0) {
	  			return false;
	  		}
	  		else {
	  			$shipmentDataArr = $this->getFormPostData($postData);
	  			$shipmentData2Arr = $this->getFormPostData($postData, 'products', false);
	  			//$orderStatus = ($orderStatus == '17006' ? '17021' : $orderStatus);

	  			/**
	  			 * Delete shipped product if shipment has no data
	  			 */
	  			if(count($shipmentData2Arr) > 0 ) {
	  				foreach ($shipmentData2Arr as $orderId => $prdArr) {
	  					
	  					if(isset($postData['change_inv_qty'][$orderId]) && $postData['change_inv_qty'][$orderId] == $orderId) {
	  						$shipGrid = $this->_shipmentModel->getShipmentGridByOrderId($orderId);
							$shipGridId = isset($shipGrid->gds_ship_grid_id) ? (int)$shipGrid->gds_ship_grid_id : 0;

	  						if($shipGridId) {
	  							$this->_orderModel->updateShipmetStatus(array('status_id'=>$orderStatus), $shipGridId);
	  							// delete shipped product
	  							
	  							$this->_shipmentModel->deleteShippedProduct($shipGridId);
	  							$this->_shipmentModel->deleteShippedGrid($shipGridId);
		  					}
	  					}
	  				}
	  			}

	  			/**
	  			 * Delete shipped product if shipment has data
	  			 */
	  			
	  			//print_r($shipmentDataArr);die;
	  			if(count($shipmentDataArr)) {
	  				foreach ($shipmentDataArr as $orderId => $prdArr) {
  						
  						if(isset($postData['change_inv_qty'][$orderId]) && $postData['change_inv_qty'][$orderId] == $orderId) {
	  						$shipGrid = $this->_shipmentModel->getShipmentGridByOrderId($orderId);
							$shipGridId = isset($shipGrid->gds_ship_grid_id) ? (int)$shipGrid->gds_ship_grid_id : 0;

	  						if($shipGridId) {
	  							$this->_orderModel->updateShipmetStatus(array('status_id'=>$orderStatus), $shipGridId);
	  							// delete shipped product
	  							
	  							$this->_shipmentModel->deleteShippedProduct($shipGridId);
		  					}
		  					else {
		  						$shipGridId = $this->_orderModel->saveShipmentGrid(array('gds_order_id'=>$orderId, 'status_id'=>$orderStatus));
		  					}
		  					//var_dump($shipGridId);die;
		  					if($shipGridId) {
		  						$totShipQty = 0;
		  						$ship_value = 0;
		  						foreach($prdArr as $productId=>$shipQty) {

		  							$priceArr = $this->_orderController->getUnitPriceOfOdrItems($orderId, array($productId));

									$unit_price = isset($priceArr[$productId]['unitPrice']) ? $priceArr[$productId]['unitPrice'] : 0;
									
									$total_price = ($unit_price * $shipQty);

		  							/**
		  							 * Save shipment product details
		  							 */
									
									$comment = isset($postData['comment'][$orderId][$productId]) ? $postData['comment'][$orderId][$productId] : '';

									$data = array('gds_ship_grid_id'=>$shipGridId, 
												'product_id'=>$productId,
												'qty'=>$shipQty, 
                        'status_id'=>$orderStatus,
												'unit_price'=>$unit_price,
												'total_price'=>$total_price,
												'comment'=>$comment);
									$this->_orderModel->saveShipmentGridItem($data);
									$totShipQty = $totShipQty + $shipQty;
									$ship_value = $ship_value + $total_price;
		  						}


		  						$this->_shipmentModel->updateShipmetGrid($shipGridId, array('ship_qty'=>$totShipQty, 'ship_value'=>$ship_value));

		  						/*
								 * Update tracking information
								 */
								/*$trackData = $this->getTrackingInfo($postData, $shipGridId);

								if(isset($trackData['carriers']) && is_array($trackData['carriers'])) {
									$this->saveShipmentTracking($orderId, $trackData);
								}*/

								$cfc_cnt = isset($postData['cartons'][$orderId]) ? $postData['cartons'][$orderId] : 0;
								$bags_cnt = isset($postData['bags'][$orderId]) ? $postData['bags'][$orderId] : 0;
								$crates_cnt = isset($postData['crates'][$orderId]) ? $postData['crates'][$orderId] : 0;
								
								$dataArr = array('cfc_cnt'=>$cfc_cnt, 'bags_cnt'=>$bags_cnt, 'crates_cnt'=>$crates_cnt);
								$this->_orderModel->saveOrderTrackData($orderId, $dataArr);
		  					}
		  				}
	  				}
	  			}
	  		}
	  		//DB::commit();//commented by Nishanth
  			return true;
  		}
  		catch(Exception $e) {
  			//DB::rollback();//commented by Nishanth
        	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
  		}  		
  	}

  	public function deleteCancelProductAndUpdateInventory($orderId, $gridId) {
  		//DB::beginTransaction();//commented by Nishanth
  		try {
  			$cancelProducts = $this->_cancelModel->getCancelProductByGridId($gridId);
			$productsArr = $this->_cancelModel->convertObjectToArray($cancelProducts);
			$canGridInfo = $this->_cancelModel->getCancelGridByGridId($gridId);
			$cancel_code = isset($canGridInfo->cancel_code) ? $canGridInfo->cancel_code : '';
						
			#print_r($productsArr);die;

			$order = $this->_orderModel->getOrderStatusById($orderId);
			$le_wh_id = isset($order->le_wh_id) ? $order->le_wh_id : 0;
			$this->_Inventory->updateInventory($productsArr, $orderId, 'add', $cancel_code);

  			//DB::commit();//commented by Nishanth
			return true;
  		} catch (Exception $e) {
  			//DB::rollback();//commented by Nishanth
        	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
  		}
  	}

  	public function saveBulkCancel($data, $statusCode) {

  		//DB::beginTransaction();//commented by Nishanth

  		try{
  			if(!isset($data['cancel'])) {
	  			return false;
	  		}

	  		$cancelDataArr = $this->getFormPostData($data, 'cancel');
	  		$cancelData2Arr = $this->getFormPostData($data, 'cancel', false);
  			/**
			 * Verify and Delete product entry from cancel table if cancel has no data
			 */
			
			if(is_array($cancelData2Arr) && count($cancelData2Arr) > 0) {
				foreach ($cancelData2Arr as $orderId=>$productArr) {
					if(isset($data['change_inv_qty'][$orderId]) && $data['change_inv_qty'][$orderId] == $orderId) {
						// checking cancel grid data
						$canGridArr = $this->_cancelModel->getCancelGridByOrderId($orderId);
  					
  						if(is_array($canGridArr) && count($canGridArr) > 0 ) {
		  					foreach ($canGridArr as $canData) {
		  						$this->deleteCancelProductAndUpdateInventory($orderId, $canData->cancel_grid_id);
		  						$this->_cancelModel->deleteCancelProduct(array($canData->cancel_grid_id));
		  						$this->_cancelModel->deleteCancelGrid($canData->cancel_grid_id);
		  					}
		  				}
		  			}
				}
			}
			
			/**
			 * Verify and Delete product entry from cancel table if cancel has data
			 */
				  		
	  		if(is_array($cancelDataArr) && count($cancelDataArr) > 0) {
	  			foreach ($cancelDataArr as $orderId=>$productArr) {
	  				if(isset($data['change_inv_qty'][$orderId]) && $data['change_inv_qty'][$orderId] == $orderId) {
	  				// checking cancel grid data
	  				$canGridArr = $this->_cancelModel->getCancelGridByOrderId($orderId);
  					$cancelGridId = isset($canGridArr[0]->cancel_grid_id) ? (int)$canGridArr[0]->cancel_grid_id : 0;
  					
  					if($cancelGridId) {
  						if(is_array($canGridArr) && count($canGridArr) > 0 ) {
		  					foreach ($canGridArr as $canData) {
		  						$this->deleteCancelProductAndUpdateInventory($orderId, $canData->cancel_grid_id);
		  						$this->_cancelModel->deleteCancelProduct(array($canData->cancel_grid_id));
		  						if($cancelGridId != $canData->cancel_grid_id) {
		  							$this->_cancelModel->deleteCancelGrid($canData->cancel_grid_id);
		  						}
		  					}
		  				}
  					}
  					else {
			  			$query = DB::table('gds_orders')->select('le_wh_id as whareHouseID')->where('gds_order_id',$orderId)->first();
			        	$whId = isset($query->whareHouseID) ? $query->whareHouseID: '';
			        	$whdetails =$this->_roleRepo->getLEWHDetailsById($whId);
			        	$statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
			        	// Log::info("123445 BulkshipmentController".$statecode);
  						$cancel_code = $this->_orderModel->getRefCode('SC',$statecode);
  						// Log::info("10000 BulkshipmentController".$cancel_code);
			  			$gridData = array('gds_order_id'=>$orderId, 
			  							'cancel_status_id'=>$statusCode, 
			  							'cancel_code'=>$cancel_code);
			  			$cancelGridId = $this->_orderModel->cancelGrid($gridData,'default');
  					}

  					/**
  					 * save cancel product (gds_order_cancel) table
  					 */

  					if($cancelGridId) {
  						$canGridInfo = $this->_cancelModel->getCancelGridByGridId($cancelGridId);
						$cancel_code = isset($canGridInfo->cancel_code) ? $canGridInfo->cancel_code : '';

  						if(is_array($productArr)) {
  							$cancelProductArr = array();
  							$totCancelQty = 0;
  							$totCancelAmt = 0;  

		  					foreach($productArr as $product_id=>$qty){
								$priceArr = $this->_orderController->getUnitPriceOfOdrItems($orderId, array($product_id));
								$unit_price = isset($priceArr[$product_id]['unitPrice']) ? $priceArr[$product_id]['unitPrice'] : 0;
								$total_price = ($unit_price * $qty);

								$cancel_reason_id = isset($data['cancelReason'][$orderId][$product_id]) ? $data['cancelReason'][$orderId][$product_id] : 0;

								$itemData = array('product_id'=>$product_id,
												'qty'=>$qty,
												'cancel_status_id'=>$statusCode,
												'cancel_grid_id'=>$cancelGridId,
												'cancel_reason_id'=>$cancel_reason_id,
												'unit_price'=>$unit_price, 
												'total_price'=>$total_price);
								$this->_orderModel->cancelGridItem($itemData,"");
								$cancelProductArr[] = array('product_id'=>$product_id, 'qty'=>$qty);
								$totCancelQty = $totCancelQty + $qty;
								$totCancelAmt = $totCancelAmt + $total_price;
							}

							// update inventory
							$order = $this->_orderModel->getOrderStatusById($orderId);
							$le_wh_id = isset($order->le_wh_id) ? $order->le_wh_id : 0;
							$this->_Inventory->updateInventory($cancelProductArr, $orderId, 'substract', $cancel_code);

							$this->_cancelModel->updateCancelGrid($cancelGridId, array('cancel_value'=>$totCancelAmt, 'cancel_qty'=>$totCancelQty));

							// send sms to customer & field force					
				  			//$this->_orderController->sendCancelSMS($orderId, $cancelGridId);

				  			// send email to customer, field force and logistic manager's teams
				  			//$this->_orderController->sendCancelEmail($orderId);
						}
  					}
	  			}
	  			}
	  		}
	  		// revert cashback if canel qty = order qty
            $orderedQty = $this->_orderModel->getOrderedQtyByOrderId($orderId);
            $cancelledQty = $this->_orderModel->getCancelledTotalQtyByOrderId($orderId);
            if($cancelledQty == $orderedQty){
                $this->_orderController->revertCashbackFromOrder($orderId,17015,"Order Cancelled From Bulk Shipment!");
            }
  			//DB::commit();//commented by Nishanth
			return true;
  		}
  		catch(Exception $e) {
  			//DB::rollback();//commented by Nishanth
        	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
  		}  	
  	}

  	private function createBulkInvoiceNew($postData) {
  		//DB::beginTransaction();//commented by Nishanth
  		try {
  			$invoiceDataArr = $this->getFormPostData($postData, 'products');
  			$cancelDataArr = $this->getFormPostData($postData, 'cancel');
			if(is_array($invoiceDataArr) && count($invoiceDataArr) > 0) {
				foreach ($invoiceDataArr as $orderId => $prdArr) {
					Log::info('bulk invoice orderid--'.$orderId);
					$this->_invoiceModel->generateInvoiceByOrderId($orderId, 0, 1,'Invoice created from bulkShipment page');
				}
			}else if(is_array($cancelDataArr) && count($cancelDataArr) > 0){
				foreach ($cancelDataArr as $orderId => $productArr) {
					//Log::info('bulk invoice orderid--'.$orderId);

					if(is_array($productArr)) {
	  					$statusArr = array();
	  					$totCancelQty = 0;
						foreach ($productArr as $productId=>$product) {
							$canProdQty = $this->_orderModel->getCancelledProductqty($orderId, $productId);
	  						$canProdQty = empty($canProdQty) ? 0 : $canProdQty;

			 				$orderedQty = (int)$postData['orderedQty'][$orderId][$productId];
			 				$shipQty = isset($postData['products'][$orderId][$productId]) ? (int)$postData['products'][$orderId][$productId] : 0;
			 				$cancelQty = isset($postData['cancel'][$orderId][$productId]) ? (int)$postData['cancel'][$orderId][$productId] : 0;

			 				if($orderedQty == $canProdQty) {
			 					$statusArr[$orderId][17015] =  '17015';
			 					$this->_orderModel->updateProductStatus($orderId, $productId, '17015');
			 				}
			 				else {
			 					$statusArr[$orderId][17013] =  '17013';
		 						$this->_orderModel->updateProductStatus($orderId, $productId, '17013');
			 				}
			 				if(isset($statusArr[$orderId]) && count($statusArr[$orderId]) == 1) {
								$statusCodeArr = array_values($statusArr[$orderId]);
								$statuscode = isset($statusCodeArr[0]) ? $statusCodeArr[0] : '';
							}
							else {
								$statuscode = '17013';
							}
							
							if($statuscode == '17013') {
								$statuscode = '17005';
							}
							else if($statuscode == '17015') {
								$statuscode = '17015';
							}

							if(!empty($statuscode)) {
								$this->_orderModel->updateOrderStatusById($orderId, $statuscode);
							}

							/**
							 * Update comment
							 */
							$commentType = 'SHIPMENT_STATUS';
							if($statuscode == '17015') {
								$commentType = 'Cancel Status';
								$shipment_comment = 'Bulk Shipment cancelled by Ebutor(web end).';

							}
							Log::info("change product statuss== ". $orderId);
							$this->_orderController->saveComment($orderId, $commentType, array('comment'=>$shipment_comment, 'order_status_id'=>$statuscode));	 				
			  			}
					}
				}
			}

  			//DB::commit();//commented by Nishanth
  			return true;
  		}
  		catch (Exception $e) {
    		//DB::rollback();//commented by Nishanth
        	Log::info('bulk invoice--'.$e->getMessage() . ' => ' . $e->getTraceAsString());
		}
  	}

  	private function createBulkInvoice($postData) {
  		DB::beginTransaction();
  		try {
  			$invoiceDataArr = $this->getFormPostData($postData, 'products');
			$comment = isset($postData['shipment_comment']) ? $postData['shipment_comment'] : '';
			if(is_array($invoiceDataArr) && count($invoiceDataArr) > 0) {

			foreach ($invoiceDataArr as $orderId => $prdArr) {

					$grandTotal = $baseGrandTotal = $shippingTaxAmount = $totalQty = $taxAmount = $shippingAmount = $subTotal = $discountAmount = 0;
					
					$itemsArr = array();
					$invoice_status = '54002';
					$billing_name = $this->getBillingName($orderId);
					
					foreach($prdArr as $productId=>$product) {

						$qty = $postData['products'][$orderId][$productId];
						if($qty) {
							$comment = isset($postData['comment'][$orderId][$productId]) ? $postData['comment'][$orderId][$productId] : '';
							$orderedQty = $postData['orderedQty'][$orderId][$productId];
							
							$product = $this->_orderModel->getProductByOrderIdProductId($orderId, $productId);
							$tax_per_object = $this->_orderModel->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
							$tax_per = $tax_per_object->tax_percentage;


							//get tax percentage
							$singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);
							$net_value = ($singleUnitPrice * $qty);

							$singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;

							$tax_amount = (($singleUnitPrice * $tax_per) / 100 ) * $qty;

							$rowTotal = $net_value;
							
							$rowTotalInclTax = ($tax_amount + $net_value);

							$taxAmount = $taxAmount + $tax_amount;

							$grandTotal = $grandTotal + $rowTotalInclTax; 
							$totalQty = $totalQty + $qty;

							$subTotal = $subTotal + $net_value;

							$baseGrandTotal = $baseGrandTotal + $net_value;
							
							$key = $orderId.'-'.$productId;
							$discount_amount = 0;

							$packConfig = $this->_invoiceModel->getCFCPackConfig($productId);
							$no_of_eaches = isset($packConfig->no_of_eaches) ? $packConfig->no_of_eaches : 0;

							$itemsArr[$key] = array('gds_order_invoice_id'=>'',
													'gds_order_id'=>$orderId,
													'product_id'=>$productId,
													'qty'=>$qty,
													'price'=>$singleUnitPrice,
													'price_incl_tax'=>$singleUnitPriceWithtax,
													'base_cost'=>$singleUnitPrice,
													'tax_amount'=>$tax_amount, // add the actual tax products from orders
													'discount_amount'=>$discount_amount,
													'row_total'=>$rowTotal,
													'row_total_incl_tax'=>$rowTotalInclTax,
													'invoice_status'=>$invoice_status,
													'comments'=>$comment,
													'created_by'=>Session('userId'),
													'eaches_in_cfc'=>$no_of_eaches,
													'created_at'=>(string)Date('Y-m-d H:i:s'));

						}
					}
					//print_r($itemsArr);die;
					/**
					 * save data in invoice grid, order invoice and invoice item
					 */
					$orderwhid = $this->_orderModel->getOrderInfoById($orderId,['le_wh_id']);
					$lewhid = isset($orderwhid->le_wh_id)?$orderwhid->le_wh_id:0;
					$whdata = $this->_roleRepo->getLEWHDetailsById($lewhid);

					$state_code = isset($whdata->state_code)?$whdata->state_code:"TS";
            		$invoiceCode = Utility::getReferenceCode("IV",$state_code);
			
					$gridDataArr = array('invoice_code'=>(string)$invoiceCode,
										'grand_total'=>$grandTotal, 'gds_order_id'=>$orderId,
									'billing_name'=>$billing_name, 'invoice_status'=>$invoice_status,
									'created_by'=>Session('userId'),'created_at'=>Date('Y-m-d H:i:s'), 'invoice_qty'=>$totalQty);
					$invoiceGridId = $this->_orderModel->invoiceGrid($gridDataArr);
					
					if($invoiceGridId) {
						$invoiceDataArr = array('gds_invoice_grid_id'=>$invoiceGridId,
												'base_grand_total'=>$baseGrandTotal,
												'shipping_tax_amount'=>$shippingTaxAmount,
												'tax_amount'=>$taxAmount,
												'grand_total'=>$grandTotal,
												'shipping_amount'=>$shippingAmount,
												'total_qty'=>$totalQty,
												'subtotal'=>$subTotal,
												'discount_amount'=>$discountAmount,
												'status'=>$invoice_status,
												'created_by'=>Session('userId'),
												'created_at'=>Date('Y-m-d H:i:s'));
						$invoiceId = $this->_orderModel->gdsOrderInvoice($invoiceDataArr);

						if($invoiceId) {
							foreach($prdArr as $productId=>$product) {
								$key = $orderId.'-'.$productId;
								$qty = $postData['products'][$orderId][$productId];
								if($qty) {
									$itemsArr[$key]['gds_order_invoice_id'] = $invoiceId;
								}								
							}
							//print_r($itemsArr);die;
							$this->_orderModel->insertBulkInvoiceGridItems($itemsArr);
						}

						$this->_orderController->saveOutputTax($invoiceGridId);
						$this->_orderController->saveStockOutward($invoiceGridId);
						$this->_invoiceModel->saveSalesVoucher($invoiceGridId);
					}         
                    $this->_orderModel->updateTrackBulkshipment($orderId,$postData['representative_name']);	
				}
			}

  			DB::commit();
  			return true;
  		}
  		catch (Exception $e) {
    		DB::rollback();
        	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
  	}

  	public function changeProductStatus($data, $orderStatusCode) {
  		//DB::beginTransaction();//commented by Nishanth

  		try{

  			$shipment_comment = isset($data['shipment_comment']) ? $data['shipment_comment'] : '';
  			if($orderStatusCode == '17021' && empty($shipment_comment)) {
  				$shipment_comment = 'Invoice created from bulkshipment(web end).';
  			}
  			if(isset($data['products']) && is_array($data['products']) && count($data['products']) > 0) {
  			foreach ($data['products'] as $orderId=>$productArr) {
  				
  				if(is_array($productArr)) {
  					$statusArr = array();
  					$totCancelQty = 0;
					foreach ($productArr as $productId=>$product) {
						$canProdQty = $this->_orderModel->getCancelledProductqty($orderId, $productId);
  						$canProdQty = empty($canProdQty) ? 0 : $canProdQty;

		 				$orderedQty = (int)$data['orderedQty'][$orderId][$productId];
		 				$shipQty = isset($data['products'][$orderId][$productId]) ? (int)$data['products'][$orderId][$productId] : 0;
		 				$cancelQty = isset($data['cancel'][$orderId][$productId]) ? (int)$data['cancel'][$orderId][$productId] : 0;
		 				
		 				if($shipQty ==0 && $cancelQty == 0) {
		 					//continue;
		 				}

		 				if($orderedQty == $shipQty) {
		 					$statusArr[$orderId][$orderStatusCode] =  $orderStatusCode;
	 						$this->_orderModel->updateProductStatus($orderId, $productId, $orderStatusCode);
		 				}
		 				else if($orderedQty == $canProdQty) {
		 					$statusArr[$orderId][17015] =  '17015';
		 					$this->_orderModel->updateProductStatus($orderId, $productId, '17015');
		 				}
		 				else {
		 					$statusArr[$orderId][17013] =  '17013';
	 						$this->_orderModel->updateProductStatus($orderId, $productId, '17013');
		 				}	 				
		  			}
				}
				//Log::info("change product status ". $orderId);
				//print_r($statusArr);die;
				if(isset($statusArr[$orderId]) && count($statusArr[$orderId]) == 1) {
					$statusCodeArr = array_values($statusArr[$orderId]);
					$statuscode = isset($statusCodeArr[0]) ? $statusCodeArr[0] : '';
				}
				else {
					$statuscode = '17013';
				}
				
				if($orderStatusCode == '17005' && $statuscode == '17013') {
					$statuscode = '17005';
				}
				else if($orderStatusCode == '17021' && $statuscode == '17013') {
					$statuscode = '17021';
				}

				if(!empty($statuscode)) {
					$this->_orderModel->updateOrderStatusById($orderId, $statuscode);
				}

				/**
				 * Update comment
				 */
				$commentType = 'SHIPMENT_STATUS';
				if($statuscode == '17015') {
					$commentType = 'Cancel Status';
				}
				//Log::info("change product statuss== ". $orderId);
				$this->_orderController->saveComment($orderId, $commentType, array('comment'=>$shipment_comment, 'order_status_id'=>$statuscode));
			}
  			//DB::commit();//commented by Nishanth
			return true;
			}
  		}
  		catch(Exception $e) {
  			//DB::rollback();//commented by Nishanth
        	Log::info('update order status exception--'.$e->getMessage() . ' => ' . $e->getTraceAsString());
  		}
  	}

  	private function saveShipmentTracking($orderId, $trackData) {
    	$shippingArr = $this->_orderModel->getBillingAndShippingAddressByOrderId($orderId);
		$shipping = $this->_orderController->convertBillingAndShippingAddress($shippingArr);

		if(isset($trackData['carriers']) && is_array($trackData['carriers']) && count($trackData['carriers']) > 0) {

			foreach($trackData['carriers'] as $carrier) {
					$shipTrackData = array(
					'gds_ship_grid_id'=>$carrier['shiment_id'],
					'gds_order_id'=>$orderId,
					'ship_service_id'=>$carrier['ship_service_id'],
					'ship_method'=>(isset($carrier['ship_method']) ? $carrier['ship_method'] : ''),
					'tracking_id'=>(isset($carrier['tracking_id']) ? $carrier['tracking_id'] : ''),
					'vehicle_number'=>(isset($carrier['vehicle_number']) ? $carrier['vehicle_number'] : ''),
					'rep_name'=>(isset($carrier['rep_name']) ? $carrier['rep_name'] : ''),
					'contact_number'=>(isset($carrier['contact_number']) ? $carrier['contact_number'] : ''),
					'updated_by'=>Session('userId'),
					'updated_at'=>date('Y-m-d H:i:s'),
					'ship_fname'=>(isset($shipping['shipping']->fname) ? $shipping['shipping']->fname : ''),
					'ship_lname'=>(isset($shipping['shipping']->lname) ? $shipping['shipping']->lname : ''),
					'ship_company'=>(isset($shipping['shipping']->company) ? $shipping['shipping']->company : ''),
					'ship_addr1'=>(isset($shipping['shipping']->addr1) ? $shipping['shipping']->addr1 : ''),
					'ship_addr2'=>(isset($shipping['shipping']->addr2) ? $shipping['shipping']->addr2 : ''),
					'ship_city'=>(isset($shipping['shipping']->city) ? $shipping['shipping']->city : ''),
					'ship_postcode'=>(isset($shipping['shipping']->postcode) ? $shipping['shipping']->postcode : ''),
					'ship_country_id'=>(isset($shipping['shipping']->country_id) ? $shipping['shipping']->country_id : ''),
					'ship_state_id'=>(isset($shipping['shipping']->state_id) ? $shipping['shipping']->state_id : '')

					);
					//print_r($shipTrackData);
					$this->_orderModel->saveTrackingDetail($shipTrackData);
			}
    	}
	}

	private function getTrackingInfo($postData, $shipGridId=0) {
		$trackData = array();
		if(isset($postData['carriers']) && is_array($postData['carriers']) && count($postData['carriers']) > 0) {
			foreach($postData['carriers'] as $key=>$shipTrackId) {
				$trackData['carriers'][] = array(
					'shiment_id'=>$shipGridId,
					'ship_service_id'=>(isset($postData['carriers'][$key]) ? $postData['carriers'][$key] : 0),
					'ship_method'=>(isset($postData['services'][$key]) ? $postData['services'][$key] : ''),
					'tracking_id'=>(isset($postData['track_numbers'][$key]) ? $postData['track_numbers'][$key] : ''),
					'vehicle_number'=>(isset($postData['vehicle_numbers'][$key]) ? $postData['vehicle_numbers'][$key] : ''),
					'rep_name'=>(isset($postData['representatives'][$key]) ? $postData['representatives'][$key] : ''),
					'contact_number'=>(isset($postData['contacts'][$key]) ? $postData['contacts'][$key] : '')
					);
			}
		}
		else if(!empty($postData['courier']) && !empty($postData['service_name'])) {
			$trackData['carriers'][] = array(
							'shiment_id'=>$shipGridId,
							'ship_service_id'=>$postData['courier'],
							'ship_method'=>$postData['service_name'],
							'tracking_id'=>$postData['track_number'],
							'vehicle_number'=>$postData['vehicle_number'],
							'rep_name'=>$postData['representative_name'],
							'contact_number'=>$postData['contact_num']
							);
		}
		return $trackData;
	}

	private function getFormPostData($postData, $field='products', $hasQty=true) {
		$dataArr = array();
		if(isset($postData[$field]) && is_array($postData[$field])) {
			foreach ($postData[$field] as $orderId => $prdArr) {
				foreach($prdArr as $productId=>$shipQty) {

					if($hasQty) {
						if($shipQty) {	  						
							$dataArr[$orderId][$productId] = $shipQty;
						}
					}
					else if($shipQty==0){
						$dataArr[$orderId][$productId] = $shipQty;
					}
				}
			}
		}
		return $dataArr;	
	}

	public function validateData($postData, $action='') {
  		$errorInv = array();
  		$alertMsg = '';

  		$shipArr = $this->getFormPostData($postData, 'products');
  		$cancelArr = $this->getFormPostData($postData, 'cancel');
  		$ordersArr = array();

  		if(isset($postData['products']) && is_array($postData['products']) ) {
			foreach ($postData['products'] as $orderId => $prdArr) {
				$ordersArr[] = $orderId;

				$orderInfo = $this->_orderModel->getOrderInfoById($orderId, ['le_wh_id','hub_id']);
				$le_wh_id = isset($orderInfo->le_wh_id) ? $orderInfo->le_wh_id : 0;
                                $hub_id = isset($orderInfo->hub_id) ? $orderInfo->hub_id : 0;

				foreach($prdArr as $productId=>$product) {
					$qty = $postData['products'][$orderId][$productId];
					if($qty) {
						$invArr = $this->_orderModel->getInventory($productId, $le_wh_id);
                                                if ($hub_id == 10695) {
                                                    $soh = isset($invArr->dit_qty) ? (int) $invArr->dit_qty : 0;
                                                } else {
                                                    $soh = isset($invArr->soh) ? (int)$invArr->soh : 0;
                                                }
						if($soh > 0 && ($postData['products'][$orderId][$productId] > $soh)) {
							$errorInv[] = $postData['item_sku'][$productId][0];
						}
						else if(!$soh){
                                                    $errorInv[] = $postData['item_sku'][$productId][0];
						}
					}
				}	
			}
		}

		/**
		 * Verify invoice
		 */

		$invoiceError = array();

		if($action == 'invoice') {
			$invoiceArr = $this->_invoiceModel->getInvoiceGridOrderId($ordersArr, array('grid.gds_order_id'));
			if(count($invoiceArr)) {
				foreach ($invoiceArr as $invoice) {
					$invoiceError[] = $postData['orders'][$invoice->gds_order_id];
				}
			}
		}		
		
		//print_r($postData);die;
		if(count($invoiceError) > 0) {
			$alertMsg = 'Invoice already created of '.implode(', ', $invoiceError).' orders.';
		}
		else if(count($shipArr)==0 && count($cancelArr) == 0) {
			$alertMsg = 'Please enter shipment / cancel quantity of product.';
		}
		else if(isset($errorInv) && is_array($errorInv) && count($errorInv) > 0) {
			$msg = Lang::get('salesorders.alertInventory');
			$alertMsg = str_replace('{SKU}', '<strong>'.implode(', ', $errorInv).'</strong>', $msg);
		}
		return $alertMsg;
  	}


	public function getBillingName($orderId) {
		$orderInfo = $this->_orderModel->getOrderInfoById($orderId);
		$billing_name = isset($orderInfo->shop_name) ? $orderInfo->shop_name : '';
	
		return $billing_name;
	}

}
