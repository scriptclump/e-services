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

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;

class RefundController extends BaseController {

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

		$this->_commentTypeArr = array('17'=>'Order Status', 'SHIPMENT_STATUS', 'INVOICE_STATUS', 'Cancel Status', '66'=>'REFUNDS', '67'=>'RETURNS');
		$this->_filterStatus = array('open'=>'17001', 'confirmed'=>'17002', 'picked'=>'17003', 'packed'=>'17004', 'dispatch'=>'17005', 'shipped'=>'17006', 'processing'=>'17013', 'delivered'=>'17007', 'completed'=>'17008', 'cancelled'=>array('17009', '17015'), 'hold'=>'17014', 'picklist'=>'17020', 'invoiced'=>'17021', 'partial'=>'17013', 'return'=>array('17022', '17023'));	        
    }

    /**
	 * getRefundsAction() method is used to get all refunds by order id
	 * @param $orderId Numeric
	 * @return  Array
	 *
	 */

	public function getRefundsAction($orderId) {

		try{
			$refundsArr = $this->_refund->getAllRefunds($orderId);
			$totalRefunds = (int)$this->_refund->getAllRefunds($orderId, 1);
			$dataArr = array();
			#print_r($refundsArr);die;
			if(is_array($refundsArr)) {
				foreach($refundsArr as $row) {
					$dataArr[] = array('refundId'=>$row->refund_grid_id,
										'orderId'=>$row->gds_order_id,
										'totAmount'=>$row->total_amount,
										'refundAmount'=>$row->refund_amount,
										'refundDate'=>date("d-m-Y H:i:s", strtotime($row->created_at)),
										'Actions'=>'<a href="javascript:void(0);"><i class="fa fa-eye"></i></a>'
										);

				}
			}

			return Response::json(array('data'=>$dataArr, 'totalRefunds'=>$totalRefunds));
		}
		catch(Exception $e) {
			return Response::json(array('data'=>array(), 'totalRefunds'=>0));
		}
	}


	/**
	 * getRefundsAction() method is used to get all refunds by order id
	 * @param $orderId Numeric
	 * @return  Array
	 *
	 */

	public function getRefundDetailAction($refundId) {

		try{

			/*$hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD008');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }
			*/

			$productArr = $this->_refund->getRefundById($refundId);
			//print_r($productArr);die;
			$orderId = $productArr[0]->gds_order_id;
			$orders = $this->_orderModel->getOrderDetailById($orderId);
			if(count($orders)==0) {
				Redirect::to('/salesorders/index')->send();
			}

			$commentStatusArr = $this->_masterLookup->getStatusByPatentName('ORDER_STATUS');
			$commentArr = $this->_orderModel->getOrderCommentById($orderId, 'Cancel Status');
			return view('Orders::refundDetail')
					->with('orderdata',$orders)
					->with('products', $productArr)
					->with('commentArr', $commentArr)
					->with('commentStatusArr', $commentStatusArr)
					->with('actionName', 'refundDetail')
					->with('tabHeading', 'Details');

		}
		catch(Exception $e) {

		}
	}

}
