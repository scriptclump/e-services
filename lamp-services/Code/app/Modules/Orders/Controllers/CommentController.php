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

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;

class CommentController extends BaseController {

	protected $_orderModel;
	protected $_masterLookup;
	protected $_commentTypeArr;
	protected $_roleRepo;
	protected $_refund;
	protected $_leModel;
	protected $_filterStatus;
	
    public function __construct() {
  		$this->middleware(function ($request, $next) {
        if (!Session::has('userId')) {
          Redirect::to('/login')->send();
        }
        return $next($request);
      });

  		$this->_orderModel = new OrderModel();
  		$this->_masterLookup = new MasterLookup();
  		$this->_roleRepo = new RoleRepo();
  		$this->_leModel = new LegalEntity();
  		$this->_commentTypeArr = array('17'=>'Order Status', 'SHIPMENT_STATUS', 'INVOICE_STATUS', 'Cancel Status', '66'=>'REFUNDS', '67'=>'RETURNS', '136' => 'Container Status');
    }

    public function commentHistoryAction($orderId) {
       try{
           $commentArr = $this->_orderModel->getOrderCommentById($orderId);                
            $orderStatusArr = $this->_orderModel->getOrderStatus('Order Status');
            /**
             * $returnStatus Storing return status 
             * @var array
             */
            $returnStatus = $this->_orderModel->getOrderStatus('RETURNS');
           
           
               $orderStatusArr[54001] = 'PENDING';
               $orderStatusArr[54002] = 'SUCCESS';
               $orderStatusArr[136007] = json_decode(json_encode(DB::select(DB::raw("select getMastLookupValue(136007) as status"))), true)[0]["status"];

               foreach ($returnStatus as $key => $value) {
                   $orderStatusArr[$key] =$value;
               }


           $dataArr = array();
           $commentTypeArr = $this->_masterLookup->getLookupCatgory($this->_commentTypeArr);

           if(is_array($commentArr)) {
               $slno = 1;
               foreach($commentArr as $comment) {
                   $commentType = $commentTypeArr[$comment->comment_type];
                   $commentType = strtoupper(str_replace(array('_'), array(' '), $commentType));

                   $dataArr[] = array('SNo'=>$slno,
                       'commentType'=>$commentType,
                       'commentDate'=>date("d-m-Y H:i:s", strtotime($comment->comment_date)),
                       'Status'=>(isset($orderStatusArr[$comment->order_status_id]) ? $orderStatusArr[$comment->order_status_id] : ''),
                       'commentBy'=>$comment->user_name,
                       'Comment'=>$comment->comment
                       );
                   $slno = $slno+1;

               }
           }

           return Response::json(array('data'=>$dataArr, 'totalComment'=>count($commentArr)));
       }
       catch(ErrorException $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
       }
   }

}
