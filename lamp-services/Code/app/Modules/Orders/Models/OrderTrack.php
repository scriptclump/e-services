<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use App\models\Dmapi\dmapiOrders;
use App\Central\Repositories\CustomerRepo;
use App\Modules\Orders\Models\PaymentModel;
use Lang;

class OrderTrack extends Model
{
    public function getOrderTrackDetails($orderIds) {
    	try{
    		$fields = array('track.gds_order_id', 'track.cfc_cnt', 'track.crates_cnt', 'track.bags_cnt');
	    	$query = DB::table('gds_order_track as track')->select($fields);
	    	$query->whereIn('gds_order_id', $orderIds);
	    	$tracksArr = $query->get()->all();
	    	$tracks = array();
	    	if(is_array($tracksArr)) {
	    		foreach ($tracksArr as $track) {
	    			$tracks[$track->gds_order_id] = $track;
	    		}
	    	}
	    	return $tracks;
    	}
    	catch(Exception $e) {
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    	}
    }

    public function getTrackDetailByOrderId($orderId) {
    	try{
    		$fields = array('track.cfc_cnt', 'track.crates_cnt', 'track.bags_cnt');
	    	$query = DB::table('gds_order_track as track')->select($fields);
	    	$query->where('gds_order_id', $orderId);
	    	return $query->first();
    	}
    	catch(Exception $e) {
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    	}
    }

    public function updateTrackDetail($orderIds, $fields) {
        try{
            DB::table('gds_order_track')->whereIn('gds_order_id', $orderIds)->update($fields);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getRetailerAndOrderDetail($orderIds) {
        try{

            $fields = array('track.delivery_date', 'orders.order_code', 'orders.order_date', 'invgrid.grand_total', 'users.firstname', 'users.lastname', 'users.mobile_no as deliveryMobile');
            $fields[] = DB::raw('(SELECT mobile_no FROM users WHERE users.legal_entity_id = orders.`cust_le_id` and users.is_parent=1 LIMIT 1) AS retailerMobile');
            $fields[] = DB::raw('(select user_id from users where users.legal_entity_id=orders.cust_le_id and users.is_parent=1 limit 1) as cust_user_id');

            $query = DB::table('gds_orders as orders')->select($fields);
            $query->Join('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
            $query->Join('gds_invoice_grid as invgrid', 'invgrid.gds_order_id', '=', 'orders.gds_order_id');
            $query->Join('users', 'users.user_id', '=', 'track.delivered_by');
            //$query->Join('users as retailer', 'retailer.legal_entity_id', '=', 'orders.cust_le_id');
            $query->whereIn('orders.gds_order_id', $orderIds);
            return $query->get()->all();
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function sendSmsToRetails($orderid) {
      try {

        if(is_array($orderid) && count($orderid) > 0) {
          //$_sms = new dmapiOrders();
          $_sms = new CustomerRepo();
          $currency_code = Lang::get('salesorders.symbol');
          $orders = $this->getRetailerAndOrderDetail($orderid);

          if(is_array($orders) && count($orders) > 0) {
            foreach ($orders as $order) {
             if(isset($order->retailerMobile) && !empty($order->retailerMobile)) {
                $message = Lang::get('sms.orderDeliveryToRetailer');          
                $invoiceValue = number_format($order->grand_total, 2);
                $order_date = date('d-m-Y', strtotime($order->order_date));
                $delivery_date = date('d-m-Y', strtotime($order->delivery_date));
                $delivery_person = $order->firstname.' '.$order->lastname;
                          
                $message = str_replace('{ORDER_CODE}', $order->order_code, $message);
                $message = str_replace('{ORDER_DATE}', $order_date, $message);
                $message = str_replace('{INVOICE_VALUE}', $currency_code.' '.$invoiceValue, $message);
                $message = str_replace('{DELIVERY_PERSON}', $delivery_person, $message);
                $message = str_replace('{DELIVERY_DATE}', $delivery_date, $message);
                $message = str_replace('{DELIVERY_MOBILE}', $order->deliveryMobile, $message);
                //echo $message;die;
                $cust_user_id = isset($order->cust_user_id)?$order->cust_user_id:0;
                $walletamntmsg='';
                if($cust_user_id>0){
                    $paymentObj = new PaymentModel();
                    $userEcash = $paymentObj->getUserEcash($cust_user_id);
                    $currentEcash = isset($userEcash->cashback)?$userEcash->cashback:0;
                    if($currentEcash>=1){
                        $walletamntmsg = Lang::get('sms.userWalletAmnt');
                        $walletamntmsg = str_replace('{WALLET_CASH}', round($currentEcash,2), $walletamntmsg);
                    }
                }
                $message=$message.$walletamntmsg;
                //$_sms->sendSMS($order->retailerMobile, $message);
                $_sms->sendSMS(0, $cust_user_id, $order->retailerMobile, $message,'','','');
                //$_sms->sendSMS('9711854636', $message);
              }
            }         
          }
        }
      } 
      catch(Exception $e) {
      Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      return Response::json(array('status' => 400, 'message' => 'Failed'));
    }
  }

  public function getPickerByOrderId($orderId) {
    try
        {
            $fields = array('users.firstname', 'users.lastname', 'users.mobile_no');
            $query = DB::table('gds_orders as orders')->select($fields);
            $query->Join('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
            $query->Join('users', 'users.user_id', '=', 'track.picker_id');
            $query->where('orders.gds_order_id', $orderId);
            return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getPickers($orderIds) {
        try
        {
            $fields = array('track.gds_order_id', 'users.firstname', 'users.lastname', 'users.mobile_no');
            $query = DB::table('gds_orders as orders')->select($fields);
            $query->Join('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
            $query->Join('users', 'users.user_id', '=', 'track.picker_id');
            $query->whereIn('orders.gds_order_id', $orderIds);
            $pickerInfo = $query->get()->all();
            $pickers = array();

            if(count($pickerInfo)) {
                foreach ($pickerInfo as $picker) {
                    $pickers[$picker->gds_order_id] = $picker;
                }
            }
            
            return $pickers;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
    public function getTrackDetails($orderIds) {
        try{
            $fields = array('track.gds_order_id', 'track.cfc_cnt', 'track.crates_cnt', 'track.bags_cnt', 'users.firstname', 'users.lastname', 'users.mobile_no');
            $query = DB::table('gds_order_track as track')->select($fields);
            $query->Join('users', 'users.user_id', '=', 'track.picker_id');
            
            $query->whereIn('gds_order_id', $orderIds);
            $trackInfo = $query->get()->all();
            $tracks = array();

            if(count($trackInfo)) {
                foreach ($trackInfo as $track) {
                    $tracks[$track->gds_order_id] = $track;
                }
            }
            
            return $tracks;
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
}
