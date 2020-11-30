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

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;

class PaymentController extends BaseController {

	protected $_orderModel;
	protected $_paymentModel;
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

    public function __construct() {
		$this->middleware(function ($request, $next) {
			if (!Session::has('userId')) {
				Redirect::to('/login')->send();
			}
            return $next($request);
        });
		$this->_orderModel = new OrderModel();
		$this->_paymentModel = new PaymentModel();
		$this->_masterLookup = new MasterLookup();
		$this->_invoiceModel = new Invoice();
		$this->_shipmentModel = new Shipment();
		$this->_roleRepo = new RoleRepo();
		$this->_sms = new dmapiOrders();
		$this->_refund = new Refund();
		$this->_leModel = new LegalEntity();
		$this->_returnModel = new ReturnModel();

	       
    }


  	public function createCollection(Request $request) {


        try {
            	$postData = Input::all();

 		        $proof_file = ($request->hasFile('proof')) ? time() . $request->file('proof')->getClientOriginalName() : '';
 

 		        $postData['proof'] = '';

	            if ($request->hasFile('proof')) {
	                $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/collection_proofs/';

	                $request->file('proof')->move($logoPath, $proof_file);
	            	
	            	$postData['proof'] = $proof_file;

	            }


			    $this->_paymentModel->saveCollection($postData);  	
				$message = 'Collection created successfully';
				$status = 200;

				$data['status'] = $status;
				$data['message'] = $message;		

				return json_encode($data);

			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }

	}


	public function updateCollectionDetails() {
        try {


		  		$data = Input::all();

		  		if(isset($data['edit_coll_collection_history_id'])) {

				    $this->_paymentModel->updateCollection($data);  	
			          return Response::json(array('status'=>200, 'message'=>'Collection updated successfully.'));

		  		} else {

			          return Response::json(array('status'=>400, 'message'=>'collection missing'));
		  		}

			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }
	}

	public function getAllCollectionsByOrderId($orderId) {


        try {

				$editAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD012');

				$Collections = $this->_paymentModel->getAllCollectionsByOrderId($orderId,$editAccess);

				$totalOrders = count($Collections);

				echo json_encode(array('data'=>$Collections, 'TotalRecordsCount'=>$totalOrders));



			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }


	}


	public function getInvoicesListByOrderid($orderId) {



        try {
 
				$allInvoiceCodes = $this->_invoiceModel->getAllInvoiceCodesByOrderid($orderId);

				
				$Invoicehtml = '<option value="">Select Invoice</option>';

				foreach ($allInvoiceCodes as $key => $invoiceCode) {
					
					if(trim($invoiceCode->invoice_code)=='') {

						$allInvoiceCodes[$key]->invoice_code = $invoiceCode->gds_invoice_grid_id;				
					
					}	


					$Invoicehtml.='<option value='.$invoiceCode->gds_invoice_grid_id.'>'.$allInvoiceCodes[$key]->invoice_code.'</option>';

				}

			echo $Invoicehtml;

			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }


	}

	public function getInvoiceDueAmount($invoiceId) {

        try {
			$this->_invoiceModel->getInvoiceDueAmount($invoiceId);
			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }

	}


	public function getOrderPickerDetails() {

        try {


		  		$data = Input::all();

		        if(isset($data['ids']) && !empty($data['ids'])) {
	
					$this->_paymentModel->getOrderPickerDetails($data['ids']);

		        }

			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }

	}


	public function getOrderMarkDeliveredDetails() {

        try {


		  		$data = Input::all();

		        if(isset($data['ids']) && !empty($data['ids'])) {
	
					$this->_paymentModel->getOrderMarkDeliveredDetails($data['ids']);

		        }

			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }

	}

	public function getTotalPaymentsByOrderId($orderId) {

        try {

					return $this->_paymentModel->getTotalPaymentsByOrderId($orderId);
			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }
	}

	public function getCollectionByCollectionHistoryId($collectionHistoryId) {

        try {

					return json_encode($this->_paymentModel->getCollectionByCollectionHistoryId($collectionHistoryId));
			}	      	
		      	catch(Exception $ex) {
		          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
		          return Response::json(array('status'=>400, 'message'=>'Failed'));
		    }
	}

	public function getPendingPaymentHistoryAction($orderId) {
        try {	
        		$histotyData = $this->_paymentModel->getPaymentPendingHistoryByOrderId($orderId);
        		return Response::json(array('data'=>$histotyData, 'message'=>'Failed'));
			}	      	
	      	catch(Exception $ex) {
	          Log::error($ex->getMessage().' '.$ex->getTraceAsString());
	          return Response::json(array('data'=>'', 'message'=>'Failed'));
		    }
	}




}
