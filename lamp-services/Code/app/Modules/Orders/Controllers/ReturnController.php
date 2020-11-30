<?php

namespace App\Modules\Orders\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Session;
use View;
use Log;
use DB;
use Auth;
use Response;
use Illuminate\Support\Facades\Redirect;
use App\Modules\Orders\Controllers\OrdersController;

use Illuminate\Support\Facades\Input;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\Shipment;
use App\Modules\Orders\Models\Refund;
use App\Modules\Orders\Models\ReturnModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\LegalEntity\Controllers\LegalEntityController;
use Excel;
use PDF;
use App\models\Mongo\MongoApiLogsModel;
/**
 * Return Controller Class ReturnController
 * @Type: Child Class
 * @Author: Ebutor
 * @Created Date: 30th September 2016
 * @Module: Order Module
 */
class ReturnController extends BaseController
{

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
	protected $_orderController;
	protected $_returnModel;
	protected $_approvalModel;

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
		$this->_orderController = new OrdersController();
		$this->_returnModel = new ReturnModel ();
		$this->_approvalModel = new  CommonApprovalFlowFunctionModel();

		$this->_commentTypeArr = array('17'=>'Order Status', 'SHIPMENT_STATUS', 'INVOICE_STATUS', 'Cancel Status', '66'=>'REFUNDS', '67'=>'RETURNS');
		$this->_filterStatus = array('open'=>'17001', 'confirmed'=>'17002', 'picked'=>'17003', 'packed'=>'17004', 'dispatch'=>'17005', 'shipped'=>'17006', 'processing'=>'17013', 'delivered'=>'17007', 'completed'=>'17008', 'cancelled'=>array('17009', '17015'), 'hold'=>'17014', 'picklist'=>'17020', 'invoiced'=>'17021');

	        $this->grid_field_db_match = array(
        	'ChannelName'   => 'master_lookup.master_lookup_name',
            'OrderID'        => 'orders.order_code',
            'OrderDate'      => 'orders.order_date',
            'OrderExpDate'      => 'orders.order_expiry_date',
            'Customer' => 'orders.shop_name',
            'User' => "name",
            'OrderValue' => 'orders.total',
            'Status'=> 'master_lookup.master_lookup_name'
        );
    }

    /**
     * [returnDetailAction description]
     * @param  [type] $returnId [description]
     * @return [type]           [description]
     */
    public function returnDetailAction($returnId) {

        try {

            $reasons_status = $this->_masterLookup->getMasterLookupByCategoryName('Return Reasons');
            $returnArr = $this->_returnModel->getReturnDetailById($returnId);

            $userID = Session::get('userId');
            $status_id = isset($returnArr[0]->return_status_id) ? $returnArr[0]->return_status_id : '17007';
            if (!isset($returnArr) || count($returnArr) == 0) {
                Redirect::to('/salesorders/index')->send();
            }
            $orderId = $returnArr[0]->gds_order_id;
            if($returnArr[0]->return_status_id == 67002){
            	$return_status = $this->_approvalModel->getApprovalFlowDetails('Sales Return',57065, $userID);
            }else{
            	$return_status = $this->_approvalModel->getApprovalFlowDetails('Sales Return',$returnArr[0]->return_status_id, $userID);	
            }
            
            
            if((int)$return_status['status'] == 1 ){

            	if(isset($return_status['data'][0]['nextStatusId']) ){
	            	$viewOnly = 1;
	            }else{
	            	$viewOnly = 0;
	            }
            }else{

            	$viewOnly = 0;
            }
			// var_dump($viewOnly);
   //      	var_dump($return_status);
   //       	exit;
            $allProductsArr = $this->_orderModel->getProductByOrderId($orderId);
            $commentStatusArr = $this->_orderModel->getOrderStatus('Order Status');
        	$commentArr = $this->_orderModel->getOrderCommentById($orderId, 'RETURNS'); 
            $productArr = array();
            foreach ($allProductsArr as $product) {
                $productArr[$product->product_id] = $product;
            }
            $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
            $billingAndShipping = $this->_orderController->convertBillingAndShippingAddress($billingAndShippingArr);
            $orders = $this->_orderModel->getOrderDetailById($orderId);

            $transitMessage = '';
            if($orders->order_transit_status == NULL && $returnArr[0]->return_status_id == 57067 ){

            	$transitMessage = 'In HUB Waiting to be dispatched to DC';
                $viewOnly = 0;
            }

	        if($orders->order_transit_status == 17027 && $returnArr[0]->return_status_id == 57067){

	        	$transitMessage = 'SIT HUB - DC : On the way to DC';
	        	$viewOnly = 0;
	        }


	        $paymentModesArr    =   $this->_masterLookup->getMasterLookupNamesByCategoryId(22);
	        //$allUsers       =   $this->_orderModel->getUsersByRoleName(array('Field Force Associate','Field Force Manager'));
            
            return view('Orders::returnDetail')->with('returnProductArr', $returnArr)
                            ->with('commentStatusArr', $commentStatusArr)
                            ->with('statusMatrixArr', $return_status)
                            ->with('commentArr', $commentArr)
                            ->with('productArr', $productArr)
                            ->with('actionName', 'returnDetail')
                            ->with('tabHeading', 'Return Details')
                            ->with('orderdata', $orders)
                            ->with('billing', (isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] : ''))
		                    //->with('allUsers', $allUsers)
							->with('paymentModesArr', $paymentModesArr)
                            ->with('shipping', (isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] : ''))
                            ->with('returnReason', $reasons_status)
                            ->with('viewOnly',$viewOnly)
                            ->with('transitMessage',$transitMessage);
        } catch (Exception $e) {
            
        }
    }
    
    /**
     * [updateReturn description]
     * @param  [type] $gridId [description]
     * @return [type]         [description]
     */
    public function updateReturnActionAjax(){

        DB::beginTransaction();
    	try{
    		$returns = Input::all();
    		$return_data = Input::all();
    		$return_stat = explode(",", $return_data['return_status']);
			$return_status = $return_stat[0];
			$userId = Session::get('userId');
				if($return_stat[1] == 0){
					$approval_status = $return_stat[0];
				}
				else{
					$approval_status = 1;	
				}
    		$return_grid_id = $return_data['return_id'];
    		$returnArr = $this->_returnModel->getReturnDetailById($return_grid_id);
    		if($returnArr[0]->return_status_id == 67002){
            	
            	$return_status_db = $this->_approvalModel->getApprovalFlowDetails('Sales Return',57065, $userId);
            
            }else{
            	$return_status_db = $this->_approvalModel->getApprovalFlowDetails('Sales Return',$returnArr[0]->return_status_id, $userId);	
           	}

            if((int)$return_status_db['status'] == 1 ){

       			if(isset($return_status_db['data'][0]['nextStatusId']) ){
	        		$viewOnly = 1;
	            }else{
	        		$viewOnly = 0;
	            }
            }else{

            	$viewOnly = 0;
            }

            if($viewOnly == 0){

            	$return['status'] = 200;
	    		$return['message'] = "Status Already Updated Please Refresh";
	    		return json_encode($return);
            }
            
    		$order_id = $return_data['order_id'];
    		$refcode = $return_data['return_order_code'];
   			$comment = $return_data['return_comment'];
    		$return_array = [];
    		$i = 0;
    		foreach ($return_data['return_qty'] as $key => $value) {
    			$return_array[$i]['product_id'] = $key;	
    			$return_array[$i]['approval_status'] = $approval_status;
    			$return_array[$i]['return_qty'] = $return_data['return_qty'][$key];
    			$return_array[$i]['apprvd_qty'] = $return_data['apprvd_qty'][$key];
    			$return_array[$i]['quaratined_qty'] = $return_data['quaratined_qty'][$key];
    			$return_array[$i]['dit_qty'] = $return_data['dit_qty'][$key];
    			$return_array[$i]['dnd_qty'] = $return_data['dd_qty'][$key];
                $return_array[$i]['excess_qty'] = $return_data['excess_qty'][$key];
                $return_array[$i]['le_wh_id'] = $return_data['le_wh_id'];
    			$return_array[$i]['approval_user'] = $userId;
    			$return_array[$i]['reference_no'] = $refcode;
    			$i++;
    		}

            //check count of returns posted from view/grid with the count of returns got from the returns query($returnArr)
            if(count($return_array) != count($returnArr))
            {
                $MongoApiLogsModel = new MongoApiLogsModel();
                $mongoInsertId = $MongoApiLogsModel->insertApiLogsRequest('updateReturn', $return_array,'Returns Approve status');
                $return['status'] = 400;
                $return['message'] = "Status Update Failed,Please Try Again";
                return json_encode($return);
            }
    		$result = [];
    		foreach ($return_array as $key => $value) {
    			$result[$key]['result'] = $this->_returnModel->updateApprovedReturns($return_grid_id,$return_status,$value,$userId,$approval_status);
    		}
    		$workflow = $this->_approvalModel->storeWorkFlowHistory('Sales Return', $return_grid_id, $returns['currentStatusID'], $returns['nextStatusId'], $comment, $userId);
    		//update comment
     		$complete = $this->_returnModel->updateReturnOrderStatusonOrderId($order_id,$refcode);
			// $this->_returnModel->legderEntry($collectionData,Session('userId'));
			if($approval_status == 1){

     			$fields = array('order_transit_status' => NULL);
     			$financeStatus = $this->_returnModel->updateOrderCompleteStatusOnFinanceApproval($order_id);
     			if($financeStatus){

     				$fields['order_status_id'] = 17008;

     			}

     			$this->_orderModel->updateOrder(array($order_id), $fields);
                //putaway                                 
                $this->_returnModel->putawaylist($return_grid_id);

                // adding payment to legalentity if this a sales return against purchase return
                $invoiceInfo = $this->_invoiceModel->getInvoiceGridOrderId(array($order_id), array('grid.gds_invoice_grid_id','grid.invoice_code'));
                if (isset($invoiceInfo)) {
                    $invoice_code = $invoiceInfo[0]->invoice_code;
                    $pr_data = $this->_returnModel->checkPurchaseReturn($invoice_code);
                    if(count($pr_data)){
                        $le_wh_data = $this->_leModel->getWarehouseById($pr_data->le_wh_id);
                        $legalCtrl = new LegalEntityController();
                        $request = new Request();
                        $legalentity_id = $le_wh_data->legal_entity_id;
                        $data = array("legalentity_id"=>$legalentity_id,
                            "payment_amount_stockist"=>$pr_data->pr_grand_total,
                            "mode_payment_type"=>16503,
                            "payment_type_stockist"=>22011,
                            "payment_ref"=>$pr_data->pr_code,
                            "transmission_date"=>date("Y-m-d"),
                            "paid_through_stockist"=>"Update Payment Against Purchase Return",
                            "add_in_tally"=>0);
                        $data = $legalCtrl->saveStockistDetails($request,$data);
                    }
                }

     		}

            $CommentMessage = '';
     		if($returnArr[0]->return_status_id == 67002 ){

            	$CommentMessage = 'Stock Received at HUB';
            }

	        if($returnArr[0]->return_status_id == 57067){

	        	$CommentMessage = 'Stock Received at DC';
	        }

	        if($comment != ''){
	        	$comment = $CommentMessage.'#'.$comment; 	
	        }else{
	        	$comment = $CommentMessage;
	        }

			/*store comment*/
           	//if($returnGridId){
           		$this->saveComment($order_id, 'RETURNS', array('comment'=> $comment, 'order_status_id'=>$complete));
			//}

           	// save order history as completed	
           	if(isset($financeStatus) && $financeStatus == 1) {
           		$this->saveComment($order_id, 'Order Status', array('comment'=> 'Order completed from web', 'order_status_id'=>'17008'));
           	}
            DB::commit();
    		$return['status'] = 200;
    		$return['message'] = "Status Updated ";
    		return json_encode($return);


    	}catch(\Exception $e){
            DB::rollback();
    		$return['status'] = 400;
    		$return['message'] = "Status Update Failed";
    		return json_encode($return);
    	}

    }
    /**
     * getReturnsAction (Returns all returns against order id)
     * @param  int $orderId  Holds order id
     * @return json          Response on JSON
     */
    public function getReturnsAction($orderId) {

		$returnsArr = $this->_returnModel->getAllReturns($orderId);
		$totalReturns = (int)$this->_returnModel->getAllReturns($orderId, 1);
		$dataArr = array();
		if(is_array($returnsArr)) {
			foreach($returnsArr as $returnRow) {
				$dataArr[] = array('returnId'=>$returnRow->reference_no,
									'orderId'=>$returnRow->order_code,
									'orderDate'=>date('d-m-Y H:i:s', strtotime($returnRow->order_date)),
									'returnDate'=>date('d-m-Y H:i:s', strtotime($returnRow->created_at)),
									'qtyReturned'=>$returnRow->qty,
									'returnValue'=>$returnRow->total,
									'Actions'=>'<a href="/salesorders/returndetail/'.$returnRow->return_grid_id.'"><i class="fa fa-eye"></i></a>'
									);
			}
		}

		return Response::json(array('data'=>$dataArr, 'totalReturns'=>$totalReturns));
	}

	/**
	 * saveReturnActionAjax Initate Ajax call for return order
	 * @return JSON       Response on JSON
	 */
	public function saveReturnActionAjax(){

			$_OrderModel = new OrderModel();
			$returnValue = 0;
			$userId = Session::get('userId');
			$returns = Input::all();

            $total_return_items = 0;
            $total_return_item_qty = 0;
           	foreach ($_POST['retchk'] as $key => $value) {
           		if($key != 0 && $value == 'on') {

           			if($_POST['return_reason'][$key] == 0){

           				$_POST['return_reason'][$key] = 59003;
           			}

           			if($_POST['return_qty'][$key] != 0){

           				$data[$key]['product_id'] = $key;
	           			$data[$key]['qty'] = $_POST['return_qty'][$key];
	           			$data[$key]['good_qty'] = $_POST['good_qty'][$key];
	           			$data[$key]['bad_qty'] = $_POST['bad_qty'][$key];

				        //adding return qty
				        $total_return_items += 1;
	           			$total_return_item_qty += $_POST['return_qty'][$key];

	           			if(isset($_POST['dd_qty'][$key])){
	           				$data[$key]['dnd_qty'] = $_POST['dd_qty'][$key];
	           			}else{
	           				$data[$key]['dnd_qty'] = 0;	
	           			}

	           			if(isset($_POST['dit_qty'][$key])){
	           				$data[$key]['dit_qty'] = $_POST['dit_qty'][$key];
	           			}else{
	           				$data[$key]['dit_qty'] = 0;	
	           			}

                        if(isset($_POST['excess_qty'][$key])){
                            $data[$key]['excess_qty'] = $_POST['excess_qty'][$key];
                        }else{
                            $data[$key]['excess_qty'] = 0; 
                        }

	           			if(isset($_POST['return_status'])){
	           				$return_stat = explode(",", $_POST['return_status']);
	           				if($return_stat[1] == 0){
	           					$return_status = $return_stat[0];
	           				}
	           				else{
	           					$return_status = 1;
	           				}
	           			}
	           			$price = $_OrderModel->getUnitPricesTaxAndWithoutTax($_POST['gds_order_id'],$key);
    					$returnValue += $price['singleUnitPriceWithtax'] * $_POST['return_qty'][$key];
	           			$data[$key]['return_reason_id'] = $_POST['return_reason'][$key];
	           			$data[$key]['return_status_id'] = $return_status;
	           			$data[$key]['approval_status'] = $return_status;
	           			$data[$key]['approved_by_user'] = $userId;
	           			$data[$key]['gds_order_id'] = $_POST['gds_order_id'];
	           			$data[$key]['le_wh_id'] = $_POST['le_wh_id'];
	           			$data[$key]['tax_details'] = $price;	
           			}

           		}

           }
           
           $orderData['gds_order_id'] = $_POST['gds_order_id'];
           $orderData['total_return_value'] = $returnValue;
           $orderData['return_status_id'] = $return_status;
           $orderData['approval_status'] = $return_status;
           $orderData['total_return_items'] = $total_return_items;
           $orderData['total_return_item_qty'] = $total_return_item_qty;
           $comment = $_POST['order_comment'];
           $status_id = $return_status;
           $query = DB::table('gds_orders')->select('le_wh_id as whareHouseID')->where('gds_order_id', $_POST['gds_order_id'])->first();
           $whId = isset($query->whareHouseID) ? $query->whareHouseID: '';
           $whdetails =$this->_roleRepo->getLEWHDetailsById($whId);
           $statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
           $refcode = $this->_orderModel->getRefCode('SR',$statecode);
           // Log::info("reference code from saveReturnActionAjax".$refcode);
           $orderData['reference_no'] = $refcode;
           $returnGridId = $this->_returnModel->saveReturnGrid($orderData);           
           
           
			$status = 400;
			
			foreach ($data as $orderData) {	

				if($returnGridId){

					$orderData['return_grid_id'] = $returnGridId;
					$orderData['reference_no'] = $refcode;
					$return = $this->_returnModel->saveReturns($orderData);
						if($return){
							$return = true;
							$status = 200;
							$message = $returnGridId;
						}else{
							$return = false;
							$status = 200;
							$message = "failed";
						}
				}else{
					$return = false;
					$status = 400;
					$message = "failed";
				}
			}

            /**
             * Update return grid GST value
             */
            $return_gst = $this->_returnModel->updateGstOnReturnGrid($orderData['gds_order_id']);

		  	$workflow = $this->_approvalModel->storeWorkFlowHistory('Sales Return', $returnGridId, $returns['currentStatusID'], $returns['nextStatusId'], $returns['order_comment'], $userId);
			$invoice_id = $_OrderModel->getInvoiceIdFromOrderId($orderData['gds_order_id']);
			
			$collectionData['order_id'] = $orderData['gds_order_id'];
			$collectionData['return_id'] = $returnGridId;
			$collectionData['invoice'] = $invoice_id[0]->gds_order_invoice_id;
			$collectionData['invoice_reference'] = $invoice_id[0]->invoice_code;
			$collectionData['collected_on'] = date('Y-m-d');
			$collectionData['mode_of_payment'] = '';
			$collectionData['reference_num'] = $refcode;
			//$collectionData['collection_amount_debit'] =  '-'.$returnValue;
			$collectionData['collection_amount'] = $returnValue; //as per naresh instruction 1st november '-'.$returnValue;
			$collectionData['collected_by'] = '';
            $collectionData['gst'] = $return_gst;
			$complete = $this->_returnModel->updateReturnOrderStatusonOrderId($orderData['gds_order_id'],$refcode);
			//$this->_returnModel->legderEntry($collectionData,Session('userId'));
			//var_dump($data);
			//$returnVouchers = $this->_returnModel->saveReturnsVoucher($data,$collectionData);
            
            $returnVouchers = $this->_returnModel->saveReturnsVoucherGST($data,$collectionData);

			/*store comment*/
           	if($returnGridId){
           		$this->saveComment($orderData['gds_order_id'], 'RETURNS', array('comment'=> $comment, 'order_status_id'=>$complete));           		
			}
			$args = array("ConsoleClass" => 'mail', 'arguments' => array('DmapiReturnOrderTemplate', $returnGridId));

			$data['status'] = $status;
			$data['message'] = $message;		
			return json_encode($data);
	}
	/**
	 * createReturnAction initrats return action 
	 * @param  int $orderId Holds order id.
	 * @return [type]   
	 */
	public function createReturnAction($orderId){
		$data = array();
		$orderdata = array();
		$orderdata = $this->_orderModel->getOrderDetailById($orderId);
		$invoices = $this->_orderModel->getAllInvoiceGridByOrderId($orderId);
		$returned = $this->_returnModel->getReturnedByOrderId($orderId);
		$userId = Session::get('userId');
		//echo "<pre>"; print_r($userId); die();
		$approvals = $this->_approvalModel->getApprovalFlowDetails('Sales Return','drafted',$userId);
		$returnedKeyVal = array();
		if(count($returned) > 0){

			foreach ($returned as $value) {
				
				$returnedKeyVal[$value->product_id] = (int)$value->returned;

			}
		}

		foreach ($invoices as $key => $invoice) {
			
			$invoiced = (int)$invoices[$key]->qty;
			if(isset($returnedKeyVal[$invoices[$key]->product_id])){

				$returnValue = $invoiced - $returnedKeyVal[$invoices[$key]->product_id];
				$invoices[$key]->qty = $returnValue;

			}else{

				$invoices[$key]->qty = $invoiced;
			}

			if($invoices[$key]->qty == 0){
				unset($invoices[$key]);
			}

		}
		
		$reasons_status = array();
		$reasons_status = $this->_masterLookup->getMasterLookupByCategoryName('Return Reasons');
        $return_status = array();
        $return_status = $this->_masterLookup->getAllOrderStatus('RETURNS');
        $commentStatusArr = $this->_orderModel->getOrderStatus('RETURNS');
        //echo "<pre>"; print_r($return_status); die();
        $commentArr = $this->_orderModel->getOrderCommentById($orderId, 'RETURNS');   
        $paymentModesArr    =   $this->_masterLookup->getMasterLookupNamesByCategoryId(22);
        //$allUsers       =   $this->_orderModel->getUsersByRoleName(array('Field Force Associate','Field Force Manager'));
		return  view('Orders::createReturn')->with('orderdata',$orderdata)
		  									->with('actionName','createReturn')
	 										->with('productArr',$invoices)
	 										->with('returnReason',$reasons_status)
                                            ->with('returnStatus',$return_status)
                                            ->with('commentArr', $commentArr)
                                            ->with('tabHeading', 'Create Return')
						                    //->with('allUsers', $allUsers)
						                    ->with('approvals',$approvals)
						                    ->with('paymentModesArr', $paymentModesArr)
                                            ->with('commentStatusArr', $commentStatusArr);

                      
	}
	/**
	 * saveComment adding comment against particular return.
	 * @param  int $orderId     
	 * @param  int $commentType 
	 * @param  Array $dataArr     
	 * @return void              
	 */
	private function saveComment($orderId, $commentType, $dataArr) {
		$typeId = $this->_orderModel->getCommentTypeByName($commentType);
		$commentArr = array('entity_id'=>$orderId, 'comment_type'=>$typeId,
						'comment'=>$dataArr['comment'],
						'commentby'=>Session('userId'),
						'order_status_id'=>$dataArr['order_status_id'],
						'created_at'=>date('Y-m-d H:i:s'),
						'comment_date'=>date('Y-m-d H:i:s')
						);

		$this->_orderModel->saveComment($commentArr);
	}

	public function checkCreateReturns(){

		$addReturnAccess=1;
	    if(Session::get('legal_entity_id')!=0){

	           $addReturnAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD010');
	    }
		// $userId = Session::get('userId');
		// $approvals = $this->_approvalModel->getApprovalFlowDetails('Sales Return','drafted',$userId);
		// if($approvals['status'] == 0){
		// 	$result = 0;
		// }
		// else{
		// 	$result = 1;
		// }
		//echo $addReturnAccess;exit;
		return (int)$addReturnAccess;
	}


	public function getReturnDetailByIdApi($returnId){

		if($returnId == "" || !isset($returnId)){
			return Response::json(['Status' => 'false', 'Message' => 'Return Data Missing']);
		}else{

			try{

				$userID = 0;
				Session::put('userId', 0);
				$returnProductArr = $this->_returnModel->getReturnDetailById($returnId);
				if (!isset($returnProductArr) || count($returnProductArr) == 0) {
	                return Response::json(['Status' => 'false', 'Message' => 'No return found On products']);
	            }
				//var_dump($returnProductArr);exit;
				//$allProductsArr = $this->_orderModel->getProductByOrderId($orderId);
				$orderId = $returnProductArr[0]->gds_order_id;
	            $commentStatusArr = $this->_orderModel->getOrderStatus('RETURNS');
	        	$commentArr = $this->_orderModel->getOrderCommentById($orderId, 'RETURNS');
	        	$orders = $this->_orderModel->getOrderDetailById($orderId);
	        	$return_status = $this->_approvalModel->getApprovalFlowDetails('Sales Return',57065, $userID);

	        	$returnDetail['return_id'] = $returnProductArr[0]->return_grid_id;
				$returnDetail['order_id'] = $returnProductArr[0]->gds_order_id;
				$returnDetail['return_order_code'] = $returnProductArr[0]->return_order_code;
				$returnDetail['return_status_id'] = $returnProductArr[0]->return_status_id;
				$returnDetail['le_wh_id'] = $orders->le_wh_id;
				$returnDetail['order_code'] = $orders->order_code;
				$returnDetail['status'] = $returnProductArr[0]->return_stat;
				$returnDetail['return_order_code'] = $returnProductArr[0]->return_order_code;
				$returnDetail['products'] = $returnProductArr;
				$returnDetail['comments'] = $commentArr;
				return Response::json(['Status' => 'true', 'Message' => $returnDetail]);

			}catch(Exception $e){

				return Response::json(['Status' => 'false', 'Message' => $e->getMessage()]);
			}
			
		}

	}

	/**
	 * [updateReturnApiApproval description]
	 * @return [type] [description]
	 *
	 * @post request send data 
	 * {    "admin_token": "bc568bad53b432cd4c152c822b7e12fd",  
	 * 		"user_id": "2507",  
	 * 		"return_id": "1486",  
	 * 		"products": [    {   "product_id": "22",     
	 * 							 "return_qty": "10",
	 * 							 "quaratined_qty": "0",
	 * 							 "dit_qty": "0",
	 * 							 "dnt_qty": "0",
	 * 							 "approved_qty": "10"    
	 * 						  }  
	 * 				    ]}
	 * 
	 */
	public function updateReturnApiApproval(){

		$input_data = Input::all();

		try{

			if($input_data){

				if(isset($input_data['data'])){

					$returnData = json_decode($input_data['data'],true);

					//Session::put('userId', $returnData['user_id']);
					$userId = $returnData['user_id'];
					$returnId = $returnData['return_id'];
					$returnProductArr = $this->_returnModel->getReturnDetailById($returnId);

					if($returnProductArr[0]->return_status_id == 67002){
            			$return_status = $this->_approvalModel->getApprovalFlowDetails('Sales Return',57065, $userId);
            		}else{
            			$return_status = $this->_approvalModel->getApprovalFlowDetails('Sales Return',$returnProductArr[0]->return_status_id, $userId);	
            		}

            		if((int)$return_status['status'] == 1 ){

            			if(isset($return_status['data'][0]['nextStatusId']) ){
	            			$viewOnly = 1;
	            		}else{
	            			$viewOnly = 0;
	            	}
            		}else{

            			$viewOnly = 0;
            		}

            		$order_id = $returnProductArr[0]->gds_order_id;
            		$orders = $this->_orderModel->getOrderDetailById($order_id);

            		if($orders->order_transit_status == NULL && $returnProductArr[0]->return_status_id == 57067 ){

            				$transitMessage = 'In HUB Waiting to be dispatched to DC';
                			$viewOnly = 0;
                			return Response::json(['Status' => 'false', 'Message' => 'Not Allowed '.$transitMessage]);
            		}

	        		if($orders->order_transit_status == 17027 && $returnProductArr[0]->return_status_id == 57067){

	        				$transitMessage = 'SIT HUB - DC : On the way to DC';
	        				$viewOnly = 0;
	        				return Response::json(['Status' => 'false', 'Message' => 'Not Allowed '.$transitMessage]);
	        		}

            		if($viewOnly == 0){

            			return Response::json(['Status' => 'false', 'Message' => 'User Does\'nt have access to approve return at this level']);
            		}

					// $return_status = $this->_approvalModel->getApprovalFlowDetails('Sales Return',57065, $userId);
					$return_products = $returnData['products'];
					if(isset($return_status['data'][0]['isFinalStep'])){
						$approval_status = $return_status['data'][0]['isFinalStep'];
					}else{
						$approval_status = 0;
					}
					
					if(!$returnProductArr || count($returnProductArr) < 0){

						return Response::json(['Status' => 'false', 'Message' => 'No Return Items for the returns check id']);
					}else{

						if(isset($returnProductArr[0]->return_status_id) && $returnProductArr[0]->return_status_id == '57066'){
							return Response::json(['Status' => 'false', 'Message' => 'Return Already done !!!']);
						}
						$return_grid_id = $returnData['return_id'];
			    		
			    		$refcode = $returnProductArr[0]->return_order_code;
						
						$le_wh_id = $orders->le_wh_id;
						
						$return_array = [];
						foreach ($returnProductArr as $rets) {

							$return_array[$rets->product_id]['product_id'] = $rets->product_id;	
			    			$return_array[$rets->product_id]['approval_status'] = $approval_status;
			    			$return_array[$rets->product_id]['return_qty'] = $rets->qty;
			    			$return_array[$rets->product_id]['apprvd_qty'] = $rets->qty;
                            $return_array[$rets->product_id]['excess_qty'] = $rets->excess_qty;
			    			$return_array[$rets->product_id]['quaratined_qty'] = $rets->quarantine_qty;
			    			$return_array[$rets->product_id]['dit_qty'] = $rets->dit_qty;
			    			$return_array[$rets->product_id]['dnd_qty'] = $rets->dnd_qty;
			    			$return_array[$rets->product_id]['le_wh_id'] = $le_wh_id;
			    			$return_array[$rets->product_id]['approval_user'] = $userId;
			    			$return_array[$rets->product_id]['reference_no'] = $refcode;
						}

						$sum = 0;
						$retSum = 0;
						$excess_array = [];
                        $_OrderModel = new OrderModel();

                        foreach ($return_products as $product) {

							$product_id = $product['product_id'];

							if(array_key_exists($product_id,$return_array) || (array_key_exists('is_extra',$product) && $product['is_extra'] == 1)){

								$retSum = $product['approved_qty'] + $product['quaratined_qty'] + $product['dit_qty'] + $product['dnt_qty'];
								
                                if($product['is_extra'] == 1) {

                                    $return_array[$product_id]['return_qty'] = $product['return_qty'];

                                }


                                if($retSum <= $return_array[$product_id]['return_qty']){
									
									$return_array[$product_id]['return_qty'] = $product['return_qty'];
					    			$return_array[$product_id]['apprvd_qty'] = $product['approved_qty'];
					    			$return_array[$product_id]['quaratined_qty'] = $product['quaratined_qty'];
					    			$return_array[$product_id]['dit_qty'] = $product['dit_qty'];
					    			$return_array[$product_id]['dnd_qty'] = $product['dnt_qty'];
                                    $return_array[$product_id]['excess_qty'] = $product['excess_qty'];

                                    if(array_key_exists('is_extra',$product) && $product['is_extra'] == 1) {



                                    $return_array[$product_id]['product_id'] = $product_id;
                                    $return_array[$product_id]['approval_status'] = 0;
                                    $return_array[$product_id]['le_wh_id'] = $le_wh_id;
                                    $return_array[$product_id]['approval_user'] = $userId;
                                    $return_array[$product_id]['reference_no'] = $refcode;





                                        $excess_array[$product_id]['return_grid_id'] = $return_grid_id;
                                        $excess_array[$product_id]['reference_no'] = $refcode;
                                        $excess_array[$product_id]['product_id'] = $product['product_id'];
                                        $excess_array[$product_id]['qty'] = $product['return_qty'];
                                        $excess_array[$product_id]['good_qty'] = $product['approved_qty'];
                                        $excess_array[$product_id]['bad_qty'] = $product['quaratined_qty'];

                                        if(isset($product['dnd_qty'])){
                                            $excess_array[$product_id]['dnd_qty'] = $product['dnd_qty'];
                                        }else{
                                            $excess_array[$product_id]['dnd_qty'] = 0; 
                                        }

                                        if(isset($product['dit_qty'])){
                                            $excess_array[$product_id]['dit_qty'] = $product['dit_qty'];
                                        }else{
                                            $excess_array[$product_id]['dit_qty'] = 0; 
                                        }

                                        if(isset($product['excess_qty'])){
                                            $excess_array[$product_id]['excess_qty'] = $product['excess_qty'];
                                        }else{
                                            $excess_array[$product_id]['excess_qty'] = 0; 
                                        }


                                        $excess_array[$product_id]['return_reason_id'] = 59016;
                                        $excess_array[$product_id]['return_status_id'] = 67002;
                                        $excess_array[$product_id]['approval_status'] = 0;
                                        $excess_array[$product_id]['approved_by_user'] = $userId;
                                        $excess_array[$product_id]['gds_order_id'] = $order_id;
                                        $excess_array[$product_id]['le_wh_id'] = $le_wh_id;
                                    }


								}else{
									return Response::json(['Status' => 'false', 'Message' => 'Return Sum does not match up with dnd + dit + quaratined !!!']);
								}

																
							}else{

                                    return Response::json(['Status' => 'false', 'Message' => 'Line items do not match for returns !!!']);

							}
						
						}
                        DB::beginTransaction();
						$returns = array();
						
						if(count($excess_array) > 0) {
                            
                            foreach ($excess_array as $excessReturn) {

                                $return = $this->_returnModel->saveExcessReturn($excessReturn);
                                if(!$return){
                                    
                                    return Response::json(['Status' => 'false', 'Message' => 'Unable to save excess products !!!']);

                                }

                            }


                        }

                        foreach ($return_array as $key => $value) {
    							$results[$key]['result'] = $this->_returnModel->updateApprovedReturns($return_grid_id,$return_status['data'][0]['nextStatusId'],$value,$userId,$approval_status);
    					}
    					//comment put as blank
    					$workflow = $this->_approvalModel->storeWorkFlowHistory('Sales Return', $return_grid_id, $return_status['currentStatusId'], $return_status['data'][0]['nextStatusId'], '', $userId);
			     		$complete = $this->_returnModel->updateReturnOrderStatusonOrderId($order_id,$refcode);

			     		$CommentMessage = '';
			     		if($returnProductArr[0]->return_status_id == 67002 ){

            				$CommentMessage = 'Stock Received at HUB';
            			}

	        			if($returnProductArr[0]->return_status_id == 57067){

	        				$CommentMessage = 'Stock Received at DC';
	        			}

	        			$this->saveComment($order_id, 'RETURNS', array('comment'=> $CommentMessage, 'order_status_id'=>$complete));

	        			// save order history as completed	
			           	
			     		if($approval_status == 1){

			     				$fields = array('order_transit_status' => NULL);
				     			$financeStatus = $this->_returnModel->updateOrderCompleteStatusOnFinanceApproval($order_id);
				     			if($financeStatus){

				     				$fields['order_status_id'] = 17008;

				     				$this->saveComment($order_id, 'Order Status', array('comment'=> 'Order completed', 'order_status_id'=>17008));
				     			}

				     			$this->_orderModel->updateOrder(array($order_id), $fields);
                                //putaway
                                $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PUTAWAY001');
                                if($hasAccess){                                 
                                    $this->_returnModel->putawaylist($return_grid_id);
                                }
				     			// $fields = array('order_transit_status' => NULL);
				     			// $this->_orderModel->updateOrder(array($order_id), $fields);
     					}
                        DB::commit();
    					return Response::json(['Status' => 'true', 'Message' => 'Return Approved return code : '.$refcode]);
					}
				}else{

					return Response::json(['Status' => 'false', 'Message' => 'Input parameter Data feild missing']);	
				}

			}else{

				return Response::json(['Status' => 'false', 'Message' => 'Input parameters missing']);	
			}

		}catch(\Exception $e){
            DB::rollback();
			return Response::json(['Status' => 'false', 'Message' => $e->getMessage()]);
		}
	}

    /**
     * [fixReturnTax description]
     * @return [type] [description]
     */
    public function fixReturnTax($input_data){
        
        if(!isset($input_data['return_id'])){
            return Response::json(array('status'=>'false'));
        }else{

            $return_grid_id = $input_data['return_id'];
            
            $returnArr = $this->_returnModel->getReturnDetailById($return_grid_id);   

            if(count($returnArr) > 0){
                $returnArr = json_decode(json_encode($returnArr),true);
                $_OrderModel = new OrderModel();
                foreach ($returnArr as $value) {
                   
                   $price = $_OrderModel->getUnitPricesTaxAndWithoutTax($value['gds_order_id'],$value['product_id']);
                   $tax_amount = (($price['singleUnitPrice'] * $price['tax_percentage']) / 100 ) * $value['qty'];
                   $this->_returnModel->rectifyTaxOnGdsReturnId($value['return_id'],$tax_amount,$price,$value['qty']);
                   echo PHP_EOL;
                   echo "products_id ".$value['product_id'].PHP_EOL;
                   print_r($price);
                   print_r($tax_amount);
                }
                return Response::json(array('status'=>'true'));

                
            }else{
                return Response::json(array('status'=>'false'));
            }
        }

    }
}
