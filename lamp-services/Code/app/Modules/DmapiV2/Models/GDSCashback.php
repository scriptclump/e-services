<?php

namespace App\Modules\DmapiV2\Models;
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use App\Modules\Orders\Models\OrderModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use \Exception;
use Log;
use App\Modules\Orders\Models\PaymentModel;

class GDSCashback extends Model {

	private $gds_order_id;
	private $_orderModel;
	private $_instant_cashback;
	private $_instant_array=array();

	public function setOrderId($gds_order_id){

		$this->gds_order_id = $gds_order_id;
	}

	public function getOrderId(){

		return $this->gds_order_id;
	}

	public function getInstantCashback(){

		return $this->_instant_cashback;
	}

	public function getInstantCashbackArray($array){
		
		return $this->_instant_array;
	}

	public function __construct(){

        $this->_orderModel = new OrderModel();

    }

    /**
     * [storeCashback description]
     * @return [type] [description]
     */
    public function storeCashback($data){

    	try {
    		$order_data = json_decode($data['orderdata']);
	    	$products = $order_data->product_info;
	    	$order_info = $order_data->order_info;
	    	$orderId = $this->getOrderId();

	    	$insertCashBack = array();
	    	$insertFreeQty = array();
	    	$instantCashback = array();
	    	$insertTradeCashBack = array();
	    	$insertCashBackFlag = 0;
	    	foreach ($products as $product) {

	    		if(isset($product->packs)){
	                    foreach ($product->packs as $pack) {
	                        
	                        if(isset($pack->pack_cashback)){
	                        	if ((int)$pack->pack_cashback != 0) {
	                        		$packCashback = explode(',',$pack->pack_cashback);
	                        		foreach($packCashback as $pack_cashback){

		                        		$data = $this->getCashbackDataFromPromotionCashbackDetails($pack_cashback);
		                        		if(!$data){
		                        			throw new Exception("$pack_cashback not found for order");
		                        		}else{

		                        			$temp['product_id'] = $product->scoitemid;
		                        			$temp['cbk_source_type'] = $data['cbk_source_type'];
		                        			$temp['cbk_label'] = $data['cbk_label'];
		                        			$temp['customer_type'] = $data['customer_type'];
		                        			$temp['benificiary_type'] = $data['benificiary_type'];
		                        			$temp['product_star'] = $data['product_star'];
		                        			$temp['gds_order_id'] = $orderId;
		                        			$temp['pack_size'] = $pack->pack_qty;
		                        			$temp['range_from'] = $data['range_from'];
		                        			$temp['range_to'] = $data['range_to'];
		                        			$temp['cbk_type'] = $data['cbk_type'];
		                        			$temp['cbk_value'] = $data['cbk_value'];
		                        			$temp['cbk_ref_id'] = $data['cbk_ref_id'];
		                        			$temp['cbk_id'] = $data['cbk_id'];
		                        			$temp['brand_id'] = $data['brand_id'];
	            							$temp['manufacturer_id'] = $data['manufacturer_id'];
	            							$temp['product_value'] = $data['product_value'];
	            							$temp['cap_limit'] = $data['cap_limit'];
		                        			$temp['is_applied'] = 1; // add this as a marker to check waht is the entry now

		                        			$insertCashBack[] = $temp;
		                        		}
		                        	}
	                        	}
	                        }

	                        if(isset($product->all_pack_cashback)){

	                        	if ((int)$pack->all_pack_cashback != 0) {
	                        		$packCashbackAll = explode(',',$pack->all_pack_cashback);
	                        		foreach($packCashbackAll as $all_pack_cashback){

		                        		$data = $this->getCashbackDataFromPromotionCashbackDetails($all_pack_cashback);
		                        		if(!$data){
		                        			throw new Exception("$all_pack_cashback not found for order");
		                        		}else{

		                        			$temp['product_id'] = $product->scoitemid;
		                        			$temp['cbk_source_type'] = $data['cbk_source_type'];
		                        			$temp['cbk_label'] = $data['cbk_label'];
		                        			$temp['customer_type'] = $data['customer_type'];
		                        			$temp['benificiary_type'] = $data['benificiary_type'];
		                        			$temp['product_star'] = $data['product_star'];
		                        			$temp['gds_order_id'] = $orderId;
		                        			$temp['pack_size'] = $pack->pack_qty;
		                        			$temp['range_from'] = $data['range_from'];
		                        			$temp['range_to'] = $data['range_to'];
		                        			$temp['cbk_type'] = $data['cbk_type'];
		                        			$temp['cbk_value'] = $data['cbk_value'];
		                        			$temp['cbk_ref_id'] = $data['cbk_ref_id'];
		                        			$temp['cbk_id'] = $data['cbk_id'];
		                        			$temp['brand_id'] = $data['brand_id'];
	            							$temp['manufacturer_id'] = $data['manufacturer_id'];
	            							$temp['product_value'] = $data['product_value'];
	            							$temp['cap_limit'] = $data['cap_limit'];
		                        			$temp['is_applied'] = 0; // add this as a marker to check waht is the entry now

		                        			$insertCashBack[] = $temp;
		                        		}
		                        	}
	                        	}

	                        }

	                    }
	                }
	    	}

	    	//order level cashback setting
	    	if(isset($order_info->order_level_cashback)){
	    		if((int)$order_info->order_level_cashback != 0){
	    			$orderCashback = explode(',',$order_info->order_level_cashback);
		    		foreach ($orderCashback as $order_cb){

		    			$data = $this->getCashbackDataFromPromotionCashbackDetails($order_cb);
	            		
	            		if(!$data){
	            			throw new Exception("$order_cb not found for order id");
	            		}else{

	            			$temp['product_id'] = $data['product_id'];
	            			$temp['cbk_source_type'] = $data['cbk_source_type'];
	            			$temp['cbk_label'] = $data['cbk_label'];
	            			$temp['customer_type'] = $data['customer_type'];
	            			$temp['benificiary_type'] = $data['benificiary_type'];
	            			$temp['product_star'] = $data['product_star'];
	            			$temp['gds_order_id'] = $orderId;
	            			$temp['pack_size'] = $pack->pack_qty;
	            			$temp['range_from'] = $data['range_from'];
	            			$temp['range_to'] = $data['range_to'];
	            			$temp['cbk_type'] = $data['cbk_type'];
	            			$temp['cbk_value'] = $data['cbk_value'];
	            			$temp['cbk_ref_id'] = $data['cbk_ref_id'];
	            			$temp['cbk_id'] = $data['cbk_id'];
	            			$temp['brand_id'] = $data['brand_id'];
	            			$temp['manufacturer_id'] = $data['manufacturer_id'];
	            			$temp['product_value'] = $data['product_value'];
	            			$temp['cap_limit'] = $data['cap_limit'];
	            			$temp['is_applied'] = 1;

	            			$insertCashBack[] = $temp;

	            			if($order_info->instant_wallet_cashback){

	            				$product_match_total = 0;
	            				$walletCashBack = 0;
		            			foreach ($products as $product) {
			            			// adding cahback to users eacsh
			            			$walletCashBack = 0;
			            			$product_id = $product->scoitemid;

			            			$brand_ids = DB::table("products")->select("brand_id","product_group_id")->where("product_id",$product_id)->first();
			            			$brand_id = $brand_ids->brand_id;
			            			$brands =  explode(',',$data['brand_id']);
			            			$exclude_brands = explode(',',$data['excl_brand_id']);
			            			$product_group_id=$brand_ids->product_group_id;
			            			$prdgrps = explode(',', $data['product_group_id']);
			            			$exclde_prdgrps = explode(',', $data['excl_prod_group_id']);
			            			$product_min_total =  $data['product_value'];
			            			if( (in_array($brand_id, $brands) || in_array('0', $brands) || in_array($product_group_id, $prdgrps) || in_array('0', $prdgrps)) || 
			            				(!in_array($brand_id, $exclude_brands) && 
			            				!in_array($product_group_id, $exclde_prdgrps))){

			            				$product_match_total += $product->total;
			            				
			            			}else{
			            				Log::info("Brand Not Matched for cashback");
			            			}
			            			
		            			}

		            			if($order_info->grandtotal>=$data['range_from'] && $product_match_total>=$product_min_total && $product_min_total != ""){
			            					
	            					$cbk_value = $data['cbk_value'];
	            					if($data['cbk_type'] == 1){
	            						// percentage
	            						$walletCashBack = ($order_info->grandtotal) * ($cbk_value/100);
	            					}else{
	            						$walletCashBack = $cbk_value;
	            					}

	            				}else{
	            					Log::info("casbback not applicable".$product_min_total."   total ---".$product_match_total);
	            				}

	            				if($walletCashBack >= $data['cap_limit'] && $data['cap_limit'] !=""){
	            					$walletCashBack = $data['cap_limit'];
	            				}
		            			if($walletCashBack > 0 && $order_info->instant_wallet_cashback){
		            				$insertCashBackFlag = $order_info->instant_wallet_cashback;
		            				$legal_entity_id = $order_data->customer_info->cust_le_id;
		            				$user_id = DB::table('users')->select("user_id")->where("legal_entity_id",$legal_entity_id)->where("is_parent",1)->first();
		            				$user_id = isset($user_id->user_id)?$user_id->user_id:0;
		            				$cashBackAraray = array("user_id"=>$user_id,
		            					"cashback"=>$walletCashBack,
		            					"order_total"=>0,
		            					"order_id"=>$orderId,
		            					"transType"=>143002);
		            				array_push($instantCashback, $cashBackAraray);
		            			}

	            			}

	            		}
		    		}
	    		}
	    	}

	    	//order level cashback setting
	    	if(isset($order_info->order_level_all_cashbacks)){
	    		if((int)$order_info->order_level_all_cashbacks != 0){
	    			$orderCashbackAll = explode(',',$order_info->order_level_all_cashbacks);

		    		foreach ($orderCashbackAll as $order_cb){

		    			$data = $this->getCashbackDataFromPromotionCashbackDetails($order_cb);
	            		
	            		if(!$data){
	            			throw new Exception("$order_cb not found for order id");
	            		}else{

	            			$temp['product_id'] = $data['product_id'];
	            			$temp['cbk_source_type'] = $data['cbk_source_type'];
	            			$temp['cbk_label'] = $data['cbk_label'];
	            			$temp['customer_type'] = $data['customer_type'];
	            			$temp['benificiary_type'] = $data['benificiary_type'];
	            			$temp['product_star'] = $data['product_star'];
	            			$temp['gds_order_id'] = $orderId;
	            			$temp['pack_size'] = $pack->pack_qty;
	            			$temp['range_from'] = $data['range_from'];
	            			$temp['range_to'] = $data['range_to'];
	            			$temp['cbk_type'] = $data['cbk_type'];
	            			$temp['cbk_value'] = $data['cbk_value'];
	            			$temp['cbk_ref_id'] = $data['cbk_ref_id'];
	            			$temp['cbk_id'] = $data['cbk_id'];
	            			$temp['brand_id'] = $data['brand_id'];
	            			$temp['manufacturer_id'] = $data['manufacturer_id'];
	            			$temp['product_value'] = $data['product_value'];
	            			$temp['cap_limit'] = $data['cap_limit'];
		                    $temp['is_applied'] = 0; // add this as a marker to check waht is the entry now
	            			$insertCashBack[] = $temp;
	            		}
		    		}
	    		}
	    	}
	    	
	    	if(count($insertCashBack)>0){
	    		DB::table('gds_order_cashback_data')->insert($insertCashBack);

	    		// adding instant cashback to wallet ,we are inserting cashback in gds order cashback model for every product,but now we accepting only 1 instant wallet cashnack in array
                // checking _instant_cashback
                if($insertCashBackFlag){
                  $cashBackArray = $instantCashback;
                  if(isset($cashBackArray[0])){
                    $cashBackArray = $cashBackArray[0];
                    $userId = $cashBackArray['user_id'];
                    $cashback = $cashBackArray['cashback'];
                    $order_total = $cashBackArray['order_total'];
                    $order_id = $cashBackArray['order_id'];
                    $transType = $cashBackArray['transType'];
                    $comment = "Order Placed with cashback.";
                    $this->paymentmodel = new PaymentModel();
                    $this->paymentmodel->updateUserEcash($userId,$cashback,$order_total,$order_id,$transType,$comment,17001);
                    DB::table('gds_orders')->where('gds_order_id', $order_id)->update(['cashback_amount' => $cashback]);
                  }
                }
	    	}	    	

	    	// free qty/samples offer which are applied for current order total
	    	if(isset($order_info->free_qty_offer)){
	    		if((int)$order_info->free_qty_offer != 0){
	    			$freeQty = explode(',',$order_info->free_qty_offer);
		    		foreach ($freeQty as $fq){

		    			$freeQtyData = $this->getFreeQtyDataFromPromotionDetails($fq);
	            		
	            		if(!$freeQtyData){
	            			throw new Exception("$fq not found for order id");
	            		}else{

	            			$freeQtytemp['ref_id'] = $freeQtyData['ref_id'];
	            			$freeQtytemp['gds_order_id'] = $orderId;
	            			$freeQtytemp['product_id'] = $freeQtyData['product_id'];
	            			$freeQtytemp['product_qty'] = $freeQtyData['product_qty'];
	            			$freeQtytemp['pack_type'] = $freeQtyData['pack_type'];
	            			$freeQtytemp['pack_level'] = $freeQtyData['pack_level'];
	            			$freeQtytemp['range_from'] = $freeQtyData['range_from'];
	            			$freeQtytemp['range_to'] = $freeQtyData['range_to'];
	            			$freeQtytemp['is_sample'] = $freeQtyData['is_sample'];
	            			$freeQtytemp['customer_type'] = $freeQtyData['customer_type'];
	            			$freeQtytemp['is_applied'] = 1;
	            			$insertFreeQty[] = $freeQtytemp;	            				

	            		}

	            	}
		    	}
	    	}
	    	
	    	// free qty/samples offer which are applied ,but less than order total
	    	if(isset($order_info->bf_freeSampleIds)){
	    		if((int)$order_info->bf_freeSampleIds != 0){
	    			$freeQty = explode(',',$order_info->bf_freeSampleIds);
		    		foreach ($freeQty as $fq){

		    			$freeQtyData = $this->getFreeQtyDataFromPromotionDetails($fq);
	            		
	            		if(!$freeQtyData){
	            			throw new Exception("$fq not found for order id");
	            		}else{

	            			$freeQtytemp['ref_id'] = $freeQtyData['ref_id'];
	            			$freeQtytemp['gds_order_id'] = $orderId;
	            			$freeQtytemp['product_id'] = $freeQtyData['product_id'];
	            			$freeQtytemp['product_qty'] = $freeQtyData['product_qty'];
	            			$freeQtytemp['pack_type'] = $freeQtyData['pack_type'];
	            			$freeQtytemp['pack_level'] = $freeQtyData['pack_level'];
	            			$freeQtytemp['range_from'] = $freeQtyData['range_from'];
	            			$freeQtytemp['range_to'] = $freeQtyData['range_to'];
	            			$freeQtytemp['is_sample'] = $freeQtyData['is_sample'];
	            			$freeQtytemp['customer_type'] = $freeQtyData['customer_type'];
	            			$freeQtytemp['is_applied'] = 0;
	            			$insertFreeQty[] = $freeQtytemp;	            				

	            		}

	            	}
		    	}
	    	}

	    	if(count($insertFreeQty)>0){
	    		Log::info(json_encode($insertFreeQty));
	    		DB::table('gds_free_qty_data')->insert($insertFreeQty);
	    	}

	    	//order level trade discount cashback setting
	    	if(isset($order_info->trade_discount_ids)){
	    		if((int)$order_info->trade_discount_ids != 0){
	    			$orderCashbackAll = explode(',',$order_info->trade_discount_ids);

		    		foreach ($orderCashbackAll as $order_cb){

		    			$data = $this->getTradeDiscDataFromPromotionCashbackDetails($order_cb);
	            		
	            		if(!$data){
	            			throw new Exception("$order_cb not found for order id");
	            		}else{

	            			$temp['ref_id'] = $data['ref_id'];
	            			$temp['trade_disc_id'] = $order_cb;
	            			$temp['object_type'] = $data['object_type'];
	            			$temp['object_ids'] = $data['object_ids'];
	            			$temp['warehouse_ids'] = $data['warehouse_ids'];
	            			$temp['state_ids'] = $data['state_ids'];
	            			$temp['cust_types'] = $data['cust_types'];
	            			$temp['gds_order_id'] = $orderId;
	            			$temp['cust_le_id'] = $data['cust_le_id'];
	            			$temp['pack_type'] = $data['pack_type'];
	            			$temp['from_range'] = $data['from_range'];
	            			$temp['to_range'] = $data['to_range'];
	            			$temp['disc_value'] = $data['disc_value'];
	            			$temp['is_percent'] = $data['is_percent'];
	            			$temp['cap_limit'] = $data['cap_limit'];
	            			$temp['from_date'] = $data['from_date'];
	            			$temp['to_date'] = $data['to_date'];
		                    $temp['is_applied'] = 1; // add this as a marker to check waht is the entry now

	            			$insertTradeCashBack[] = $temp;

	            		}
		    		}


		    		if(count($insertTradeCashBack)>0){
			    		DB::table('gds_orders_trade_disc_det')->insert($insertTradeCashBack);

		                $insertCashBackFlag =  isset($order_info->instant_wallet_cashback) ? $order_info->instant_wallet_cashback : 0;
		                $tradeDisCashback =  isset($order_info->trade_dis_cashback_applied) ? $order_info->trade_dis_cashback_applied : 0;

		                if($insertCashBackFlag && $tradeDisCashback > 0){
							$cashback = $tradeDisCashback;
							$legal_entity_id = $order_data->customer_info->cust_le_id;
            				$user_id = DB::table('users')->select("user_id")->where("legal_entity_id",$legal_entity_id)->where("is_parent",1)->first();
            				$user_id = isset($user_id->user_id)?$user_id->user_id:0;
							$comment = "Order Placed with cashback.";
							$this->paymentmodel = new PaymentModel();
							$this->paymentmodel->updateUserEcash($user_id,$cashback,0,$orderId,143002,$comment,17001);
							DB::table('gds_orders')->where('gds_order_id', $orderId)->update(['cashback_amount' => $cashback]);

		                }
			    	}	
	    		}
	    	}

    	}catch(Exception $e){
    		Log::info($e);
    		return $e;
    	}
    	

    }

    /**
     * [getCashbackDataFromPromotionCashbackDetails description]
     * @return [type] [description]
     */
    public function getCashbackDataFromPromotionCashbackDetails($cbk_id){

    	$query = "select * from promotion_cashback_details where cbk_id = $cbk_id";
    	$data = DB::select($query);
    	if(count($data) > 0){

    		$data = json_decode(json_encode($data),true);
    		return $data[0];
    	}else{

    		return false;
    	}
    }

    public function getTradeDiscDataFromPromotionCashbackDetails($trade_disc_id){

    	$query = "select * from trade_disc_det where trade_disc_id = $trade_disc_id";
    	$data = DB::select($query);
    	if(count($data) > 0){

    		$data = json_decode(json_encode($data),true);
    		return $data[0];
    	}else{

    		return false;
    	}
    }

    public function getFreeQtyDataFromPromotionDetails($free_id){

    	$query = "select * from promotions_freeqty_sample_details where free_id = $free_id";
    	$freedata = DB::select($query);
    	if(count($freedata) > 0){

    		$freedata = json_decode(json_encode($freedata),true);
    		return $freedata[0];
    	}else{

    		return false;
    	}
    }

}
