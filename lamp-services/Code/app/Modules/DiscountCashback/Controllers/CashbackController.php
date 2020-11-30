<?php

namespace App\Modules\DiscountCashback\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Cache;

use Session;
use View;
use Log;
use Request;
use Redirect;
use DB;
use Response;
use Input;

use App\Modules\DiscountCashback\Models\CashbackModel; 
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\ReturnModel;



class CashbackController extends BaseController{


	private $_cashbackModel; 
    public function __construct(){
       
    	$this->_cashbackModel = new CashbackModel();

    }

    public function index(){
        
    }


    /**
     * [getCashbackApplicable description]
     * input json {
	           'product_id'
	           'le_wh_id'
	           'cbk_id'
	           'value'
     }
     * @return [type] [description]
     */
    public function getCashbackApplicable(){

    	$data = Input::get();

    	if(!isset($data['product_info'])){

    		return Response::json(['status' => 400,'message'=> 'product_info key missing']);
    	}

    	$product_data = json_decode($data['product_info'],true);

    	if(is_null($product_data) || count($product_data) == 0){

    		return Response::json(['status' => 403,'message'=> 'product_info json is wrong']);
    	}

    	foreach ($product_data as $key => $product) {
    		
    		
    		$product_id = $product['product_id'];
    		$le_wh_id = $product['le_wh_id'];
    		$cbk_id = $product['cbk_id'];
    		$date = date('Y-m-d H:i:s');
    		$cashbackData = $this->_cashbackModel->getAppliedCash($cbk_id,$product_id,$le_wh_id);
    		if(!$cashbackData){

    			$product_data[$key]['cashback'] = 0;
    			$product_data[$key]['message'] = "no data found";

    		}else{

			    if (($date > $cashbackData['start_date']) && ($date < $cashbackData['end_date'])){
			      
			      $product_data[$key]['cashback'] = 0;
    			  $product_data[$key]['message'] = "date not with range for cashback";
			    
			    }

			    if($cashbackData['cbk_type'] == 1){

			    	if($cashbackData['range_to'] <= $product['value']){

			    		$product_data[$key]['cashback'] = ($cashbackData['cbk_value'] / 100) *$product['value'];	
			    	}
					
					$product_data[$key]['message'] = "cashback on percentage";
			    }

			    if($cashbackData['cbk_type'] == 0){

			    	if($cashbackData['range_to'] <= $product['value']){

			    		$product_data[$key]['cashback'] = $product_data[$key]['cashback'] = $cashbackData['cbk_value'];	
			    	}

			    	
					$product_data[$key]['message'] = "cashback on value";
			    }

			    $product_data[$key]['benificiary_type'] = $cashbackData['role_given'];
			    $product_data[$key]['customer_type'] = $cashbackData['customer_type'];
    		}
    			
    	}

    	return Response::json(['status' => 200,'message'=> $product_data]);
    		

    }

    public function getCashbackApplicableByOrder(){
        $data = Input::get();
        if (!isset($data['order_id'])) {
            return Response::json(['status' => 403,'message'=> 'Order id missing']);
        }else{
            $order_id = $data['order_id'];
        }

        $cashbackDataOnOrder = $this->_cashbackModel->getCashBackOnOrderId($order_id);
        $grouped = [];
        if (!$cashbackDataOnOrder) {
            return Response::json(['status' => 403,'message'=> "No cashback found for order_id: $order_id"]);
        }
        foreach ($cashbackDataOnOrder as $key => $cashback) {
            $grouped[$cashback["role_given"]][]= $cashback;
        }
        
        $_gdsOrder = new OrderModel();
        $_invoice = new Invoice();
        $_returnModel = new ReturnModel();
        $GdsProductOrderData = $_gdsOrder->getProductByOrderId($order_id);
        $invoicedPrice = $_invoice->getInvoicedPriceWithOrderID($order_id);
        $retrunedPrice = $_returnModel->getReturnValueByOrderId($order_id);
        $GdsProductOrderData = json_decode(json_encode($GdsProductOrderData),true);

        $validCashbackBill = $invoicedPrice - $retrunedPrice;
        //var_dump($GdsProductOrderData);

        $product_singleUnitPrice = array();
        foreach ($GdsProductOrderData as $value) {

        	$product_singleUnitPrice[$value['product_id']] = $value['unit_price'];

        }
       	
       	$cashbackValues = array();

        foreach ($grouped as $key => $value) {

        	$cashbackValues[$key] = 0;

        	foreach ($value as $cbk) {

                /* Cheking for SKU level cash back */
        		if($cbk['cbk_source_type'] == 2){
                    $unitprice = $product_singleUnitPrice[$cbk['product_id']];
                    $packData = $this->_cashbackModel->getPackDetailsOnOrder($order_id,$cbk['product_id'],$cbk['product_star']);
                    $invoicedProductQty = $_invoice->getInvoicedQtyByOrderIdAndProductId($order_id,$cbk['product_id']);
                    $returnedProductQty = $this->_cashbackModel->getReturnQtyByOrderIdAndProductId($order_id,$cbk['product_id']);
                    $appliedProductQty = $invoicedProductQty->invoicedQty - $returnedProductQty['qty'];
                    if ($appliedProductQty >= $cbk['range_to']) {
                        /* Cheking for SKU level pacentage */
                        if($cbk['cbk_type'] == 1){
                            if(!$packData){

                            }else{

                                $packQty = $packData['pack_qty'];
                                $price = $unitprice * $appliedProductQty;
                                $cashback = ($cbk['cbk_value'] / 100) * $price; 

                            }

                        }

                        /* Cheking for SKU level amount */
                        if($cbk['cbk_type'] == 0){

                            $cashback = $cbk['cbk_value'];
                        }
                    }else{

                    	//check for new cashback here and apply here
                    	//if possible set data else delete
                    	
                    	// if(){

                    	// }else{

                    	// 	$cashback = 0;
                    	// }
                        
                    	$cashback = 0;

                    }
                    

        			$cashbackValues[$key] += number_format($cashback,2,'.','');

	        	}

                /* Cheking for BILL level cash back */
	        	if($cbk['cbk_source_type'] == 1){

	        		$star = $cbk['product_star'];
                    $order_id = $cbk['gds_order_id'];
                    $appliedBillValue = $this->_cashbackModel->getBillValueByStar($order_id,$star);


                    if ((float)$appliedBillValue['applied_bill'] >= $cbk['range_from'] ) {
                        
                        
                        /* Cheking for BILL level perentage */
                        if($cbk['cbk_type'] == 1){

                            if ($cbk['product_star'] != null) {

                                if( (float) $appliedBillValue['applied_bill'] >= $cbk['range_from']){

									$cashback = ($cbk['cbk_value'] / 100) * $appliedBillValue['applied_bill'];
								
								}else{

									$cashback = 0;

								}

                        	}
                        }

                        /* Cheking for BILL level amount */
                        if($cbk['cbk_type'] == 0){

                            $cashback = $cbk['cbk_value'];
                        }
                    }else{
                        $cashback = 0;
                    }                   

                    $cashbackValues[$key] += $cashback;
                    


	        	}
        	}	
        }

       return Response::json([
        'status' => 200,
        'message'=> $cashbackValues,
        'delivered_amount' => $validCashbackBill
        ]);
    }
}