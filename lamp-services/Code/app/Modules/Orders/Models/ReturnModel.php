<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\Invoice;
use App\models\Dmapi\dmapiOrders;
use App\Modules\Orders\Models\PaymentModel;
use App\Modules\Tax\Models\TaxClass;
use Log;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Ledger\Models\LedgerModel;
use Cache;
use App\Lib\Queue;
use App\Modules\Orders\Models\Inventory;
use App\Modules\WarehouseConfig\Models\WarehouseConfigApi;
use App\Modules\Grn\Models\Grn;
use App\Modules\Orders\Models\GdsBusinessUnit;
use Session;
use App\Central\Repositories\RoleRepo;
class ReturnModel extends Model
{
    protected $table = "gds_return_grid";
    public $timestamps = false;
    protected $_roleRepo;

    /**
     * saveReturns Saves returns initiated
     * @param  array $data 
     * @return boolean
     */
    public function __construct() {
    	$this->_roleRepo = new RoleRepo();
    }
    function saveReturns($data,$userID = NULL,$pricedata = array()){

    	if(is_null($userID)){
    		$userID = Session('userId');
    	}
    	$_OrderModel = new OrderModel();
		$orderId= $data['gds_order_id'];
		$productId= $data['product_id'];
		$tax_data = $_OrderModel->getTaxClassesOnProductIdByOrderId($data['product_id'],$data['gds_order_id']);
		if(empty($pricedata)){
			$price = $_OrderModel->getUnitPricesTaxAndWithoutTaxForLp($data['gds_order_id'],$data['product_id']);
		}else{
			$price = $pricedata;
		}
		$tax_amount = (($price['singleUnitPrice'] * $price['tax_percentage']) / 100 ) * $data['qty'];
		$refcode = $data['reference_no'];

		/**
		 * GST tax calculation
		 */
		
		$sgstPer = isset($price['SGST']) ? $price['SGST'] : 0;
		$cgstPer = isset($price['CGST']) ? $price['CGST'] : 0;
		$igstPer = isset($price['IGST']) ? $price['IGST'] : 0;
		$utgstPer = isset($price['UTGST']) ? $price['UTGST'] : 0;

		$SGSTVal = ( $tax_amount * $sgstPer ) / 100;
		$CGSTVal = ( $tax_amount * $cgstPer ) / 100;
		$IGSTVal = ( $tax_amount * $igstPer ) / 100;
		$UTGSTVal = ( $tax_amount * $utgstPer ) / 100;


		DB::beginTransaction();
		try {
			$return_id = DB::table('gds_returns')
							->insertGetId(array(
								'product_id' => $data['product_id'],
								'gds_order_id' => $data['gds_order_id'],
								'reference_no' => $refcode,
								'return_reason_id' => $data['return_reason_id'],					
								'return_status_id'=>$data['return_status_id'],
								'return_by' => $userID,
								'qty' => $data['qty'],
								'dit_qty' => $data['dit_qty'],
								'dnd_qty' => $data['dnd_qty'],
								'excess_qty' => $data['excess_qty'],
								'unit_price' => $price['singleUnitPriceWithtax'], //storing with tax
								'tax_per' => $price['tax_percentage'],
								'tax_amt' => $price['singleUnitPriceWithtax'] - $price['singleUnitPrice'],
								'tax_total' => $tax_amount,
								'sub_total' => $price['singleUnitPrice'] * $data['qty'], //total without tax
								'total' => $price['singleUnitPriceWithtax'] * $data['qty'], //total with tax

								'SGST' => $SGSTVal,
								'CGST' => $CGSTVal,
								'IGST' => $IGSTVal,
								'UTGST' => $UTGSTVal,

								'approved_quantity' => $data['good_qty'],
								'quarantine_qty' => $data['bad_qty'],
								'return_grid_id' => $data['return_grid_id'],						
								'created_by' => $userID,
								'approval_status' => $data['approval_status'],
	           					'approved_by_user' => $data['approved_by_user'],
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
						));
			$id = DB::table('gds_return_track')
							->insertGetId(array(
								'product_id' => $data['product_id'],
								'initiated_date' => date('Y-m-d'),
								'return_track_status_id' => '',
								'track_id' => '',
								'received_date' => date('Y-m-d H:i:s'),
								'return_reason_id' => $data['return_reason_id'],	
								'approved' => $data['return_status_id'],//order status
								'return_id' => $return_id,				
								'created_by'=> $userID,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_by' => $userID,
								'updated_at' => date('Y-m-d H:i:s')
						));


			/**
			 * output_tax
			 */
			foreach($tax_data as $tax){

				$id = DB::table('output_tax')
					->insertGetId(array(
						'product_id' => $data['product_id'],
						'outward_id' => $data['return_grid_id'],
						'transaction_no' => $refcode,
						'transaction_type' => '101005',
						'transaction_date' => date('Y-m-d'),
						'tax_type' => $tax['tax_class_type'],
						'tax_percent' => $tax['tax'],
						'tax_amount' => '-'.(($price['singleUnitPrice'] * $tax['tax']) / 100 ) * $data['qty'],
						'le_wh_id' => $data['le_wh_id'],
						'created_by'=> $userID,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => $userID,
						'updated_at' => date('Y-m-d H:i:s')
				));

			}
			DB::commit();
    		// all good
			return true;		

        } catch (Exception $e) {

        		DB::rollback();
            	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			}
	}

	/**
	 * [getReturnedByOrderId description]
	 * @return [type] [description]
	 */
	public function getReturnedByOrderId($orderId){

		$query = "	select `returns`.product_id,sum(qty) as returned from gds_return_grid as grid  
					join gds_returns as `returns` on (grid.return_grid_id = `returns`.return_grid_id  
																	and grid.gds_order_id = `returns`.gds_order_id)
					where grid.gds_order_id = $orderId
					group by `returns`.product_id";
		$data = DB::selectFromWriteConnection($query);
		return $data;

	}
	/**
	 * updateReturnStatus for updating return status against return id
	 * @param  int $return_grid_id Holds return grid id
	 * @param  int $status_id      status id (from master look up table)
	 * @return int void
	 */
	public function updateApprovedReturns($return_grid_id,$status_id,$data,$userID = null,$isFinal = 0){


    	if(is_null($userID)){
    		$userID = Session('userId');
    	}
		//setting status id @lasya code status is the return status 
		//DB::beginTransaction(); 
		try {
			DB::table('gds_returns')->where(['return_grid_id'=>$return_grid_id,'product_id'=>$data['product_id']])
									->update([
										'approved_quantity' =>$data['apprvd_qty'],
										'approval_status' => $data['approval_status'],
	           							'approved_by_user' => $data['approval_user'],
										'quarantine_qty' => $data['quaratined_qty'],
										'dit_qty' => $data['dit_qty'],
										'dnd_qty' => $data['dnd_qty'],
										'excess_qty' => $data['excess_qty'],
										'return_status_id' =>$status_id,
										'updated_at' => date('Y-m-d H:i:s')
										]);
			/**
			 * Update gds_return_grid table
			 */
			DB::table('gds_return_grid')
				->where(array('return_grid_id'=>$return_grid_id))
				->update(array('approval_status'=> $data['approval_status'],'return_status_id' => $status_id));
										
			if($isFinal == 1){

				//put the change to system queue for log
				$queue = new Queue();
				$insertdata['le_wh_id'] = $data['le_wh_id'];
				$insertdata['product_id'] =  $data['product_id'];
				$insertdata['soh'] = '';
				$insertdata['order_qty'] = $data['apprvd_qty'];
				$insertdata['ref'] =  $data['reference_no'];
				$insertdata['ref_type'] = "Sales Returns";
				               
				//encode the array to json
				// $insertdata = json_encode($insertdata);
				// $insertdata = base64_encode($insertdata);
				// $args = array("ConsoleClass" => 'inventoryLog', 'arguments' => array('insert', $data));
				// $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);
				
				//stock inward Trigger setup
				
				$inventory = new Inventory();
				$products[0]['le_wh_id'] = $data['le_wh_id'];
				$products[0]['product_id'] = $data['product_id'];
				$products[0]['soh'] = $data['apprvd_qty'] + $data['excess_qty'];
				$products[0]['quarantine_qty'] = $data['quaratined_qty'];
				$products[0]['dit_qty'] = $data['dit_qty'];
				$products[0]['dnd_qty'] = $data['dnd_qty'];

				$invInsert = $inventory->inventoryStockInward($products,$data['le_wh_id'], $data['reference_no'],"Sales Returns");
				
				 DB::table('stock_inward')
				 			->insertGetId(array(
				 				'le_wh_id' => $data['le_wh_id'],
				 				'product_id' => $data['product_id'],
				 				'good_qty' => $data['apprvd_qty'],
				 				'quarantine_qty' => $data['quaratined_qty'],
				 				'dit_qty' => $data['dit_qty'],
				 				'dnd_qty' => $data['dnd_qty'],
				 				'inward_date' => date('Y-m-d'),
				 				'reference_no' => $data['reference_no'],
				 				'status' => $status_id,
				 				'created_by' => $userID

				 		));	
			}			
			
			//DB::commit();
    		//$this->updateReturnStatus($return_grid_id,$status_id);
			return true;		
        } catch (Exception $e) {
        		//DB::rollback();
            	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
	public function updateReturnStatus($return_grid_id,$status_id){
		$update= "UPDATE `gds_return_track` SET `approved` = {$status_id} where `return_id`= {$return_grid_id} ";
		$data = DB::select($update);
		return $data;
	}

	/**
	 * saveReturnGrid Inserting into return gird
	 * @param array holds return order data
	 * @return int Return Grid Id
	 */
	function saveReturnGrid($data){

		try {
			$returnGridId = DB::table('gds_return_grid')
									->insertGetId(array(
										'gds_order_id' => $data['gds_order_id'],
										'return_order_code' => $data['reference_no'],
										'total_return_value' => $data['total_return_value'],
										'total_return_items' => $data['total_return_items'],
										'total_return_item_qty' => $data['total_return_item_qty'],
										'approval_status' => $data['approval_status'],
										'return_status_id' => $data['return_status_id'],
										'created_at' => date('Y-m-d H:i:s'),
										'updated_at' => date('Y-m-d H:i:s'),
										'created_by'=>Session('userId')
										));
			return $returnGridId;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}

	}
	/**
	 * getReturnDetailById Retrives return order details against return id
	 * @param  int $returnId Holds return id
	 * @return void
	 */
	public function getReturnDetailById($returnId) {
		try{

			$fieldArr = array('gds_return_grid.*', 'gsp.*','product.*','ml.master_lookup_name as return_stat');
			$query = DB::table('gds_return_grid')->select($fieldArr);
			$query->join('gds_returns as gsp' ,'gsp.return_grid_id', '=', 'gds_return_grid.return_grid_id');
            $query->leftjoin('products as product','gsp.product_id','=','product.product_id');
            $query->join('gds_order_products as gop','gop.product_id','=','gsp.product_id');
            $query->leftjoin('master_lookup as ml','ml.value','=','gsp.return_status_id');
			$query->where('gds_return_grid.return_grid_id', $returnId);
			$query->groupBy('gds_return_grid.return_grid_id');
			$query->groupBy('gsp.product_id');
            $query->orderBy('gop.pname');
			return $query->get()->all();
		}
		catch(Exception $e){
		}
	}
	/**
	 * getAllReturns Gives all Retruns against Order ID
	 * @param  int  $orderId Holds order ID
	 * @param  int $rowCount Passing Row Count (0,1)
	 * @param  int $offset   
	 * @param  int $perpage 
	 * @param  array   $filter   
	 * @return void            
	 */
	public function getAllReturns($orderId, $rowCount=0, $offset=0, $perpage=10, $filter=array()) {

		$fieldArr = array('items.reference_no','orders.order_code','grid.return_grid_id','grid.gds_order_id','grid.created_at',
							DB::raw("sum(items.qty) as qty"),
							DB::raw("sum(items.total) as total"),
							DB::raw("orders.created_at as order_date"));
		$query = DB::table('gds_return_grid as grid')->select($fieldArr);
                $query->join('gds_returns as items','items.return_grid_id','=','grid.return_grid_id');
                 $query->join('gds_orders as orders', 'grid.gds_order_id', '=', 'orders.gds_order_id');
				$query->where('grid.gds_order_id', $orderId);
				$query->groupBy('grid.return_grid_id');
		if($rowCount) {
			return count($query->get()->all());//$query->count();
		}
		else {
			return $query->get()->all();
		}
	}
	/**
	 * [getReturnValueByOrderId description]
	 * @param  [type] $orderId [description]
	 * @return [type]          [description]
	 */
	public function getReturnValueByOrderId($orderId) {
       try {
            $fieldArr = array(DB::raw('SUM(returns.total) as returnAmt'));
            $query = DB::table('gds_returns as returns')->select($fieldArr);
            $query->where('returns.gds_order_id', $orderId);
            $returnArr = $query->first();
            return isset($returnArr->returnAmt) ? $returnArr->returnAmt : 0;
       }
       catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
   }

   /**
    * [updateOrderStatusonOrderId description]
    * @param  [type] $orderId [description]
    * @return [type]          [description]
    */
    public function updateReturnOrderStatusonOrderId($orderId,$refcode){
    	try{
	        $fieldForceNumber = $this->getFieldFrcPhoneNo(Session('userId'));
	          
			if($fieldForceNumber){

			  $message = "Products in orderId ".$orderId." has been returned"." with return reference_no $refcode";
			  // $this->sendSMS($fieldForceNumber,$message);
			}

			$_OrderModel = new OrderModel();
			$invoicedItems = $_OrderModel->getAllInvoiceGridByOrderId($orderId);
			$returnedItems = $this->getReturnedByOrderId($orderId);
			//log::info(json_decode(json_encode($returnedItems),1));
			$returnedKeyVal = array();
			$productArray = array();
	        if(count($returnedItems) > 0){

	            foreach ($returnedItems as $value) {
	                $productArray[$value->product_id] = array(    
	                                                            'returned' => (int)$value->returned ,
	                                                            'invoiced' => 0
	                                                        );
	            }
	        }
	        //log::info(json_decode(json_encode($invoicedItems),1));
	        foreach($invoicedItems as $invoiced){

	            if(isset($productArray[$invoiced->product_id])){

	                $productArray[$invoiced->product_id]['invoiced'] = (int)$invoiced->qty;

	            }else{

	                $productArray[$invoiced->product_id] = array(    
	                                                            'returned' => 0,
	                                                            'invoiced' => (int)$invoiced->qty
	                                                        );
	            }
	        }
	        //$remaining = true;
	        $complete_deliver_count = 0;
	        $complete_partial_return_count = 0;
	        $complete_full_return_count = 0;
	       
	        foreach ($productArray as $key => $value) {            
	            if($productArray[$key]['invoiced'] == $productArray[$key]['returned'] ){
	            	
	            	$checkproductstatus=DB::select(DB::raw("select product_id,order_status from gds_order_products where gds_order_id =$orderId and product_id=$key"));
						foreach ($checkproductstatus as $checkstatus) {
							$checkstatus = get_object_vars($checkstatus);
							$order_status = $checkstatus['order_status'];
							$productId = $checkstatus['product_id'];
							if($order_status== 17009 || $order_status== 17015){
								$this->updateReturnStatusProductRows($key,$orderId,$order_status);  
							}else{
								$complete_full_return_count += 1;
								$this->updateReturnStatusProductRows($key,$orderId,'17022');  
							}
						}

	              
	            }elseif($productArray[$key]['invoiced'] > 0 && $productArray[$key]['returned']==0){
	            	// $orderedprdqty=$this->getGDSProductsQtyByPrdID($key,$orderId);
	            	// if($orderedprdqty->qty!=$productArray[$key]['invoiced']){
	            	// 		$this->updateReturnStatusProductRows($key,$orderId,'17023');	
	            	// }else{
	            			$complete_deliver_count += 1;
	            			$this->updateReturnStatusProductRows($key,$orderId,'17007');	
	            	//}
	               	    
	            }else{
	                $complete_partial_return_count += 1;
	             	$this->updateReturnStatusProductRows($key,$orderId,'17023');
	            }
	        }
	       	
	       	if($complete_partial_return_count > 0 && $complete_full_return_count > 0){
	       		//log::info("partialreturned.........".$orderId);
	            $_OrderModel->updateOrderStatusById($orderId, '17023');
	            return '17023';
	        }elseif($complete_full_return_count > 0 && $complete_deliver_count > 0){
	        	$_OrderModel->updateOrderStatusById($orderId, '17023');
	            return '17023';
	    	}elseif($complete_partial_return_count > 0 && $complete_deliver_count > 0){
	        	$_OrderModel->updateOrderStatusById($orderId, '17023');
	            return '17023';
	        }elseif($complete_partial_return_count > 0){
	        	$_OrderModel->updateOrderStatusById($orderId, '17023');
	            return '17023';
	        }else{
	        	//log::info("returned.........".$orderId);
	            $_OrderModel->updateOrderStatusById($orderId, '17022');    
	            return '17022';
	        }   
	    } catch (Exception $e) {
             Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());    
        }
	}

   /**
    * [getFieldFrcPhoneNo description]
    * @param  [type] $created_by [description]
    * @return [type]             [description]
    */
   public function getFieldFrcPhoneNo($created_by){
        try {
            if(!empty($created_by)){
                $to_phoneno = DB::table('users')->where('user_id',$created_by)->select('mobile_no')->first();
                $to_phoneno = isset($to_phoneno->mobile_no) ? $to_phoneno->mobile_no : '';
                return $to_phoneno;
            }
        } catch (Exception $e) {
             Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());    
        }
    }

    /**
     * [sendSMS description]
     * @param  [type] $number  [description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function sendSMS($number,$message){

        $postfields['user']=Config::get('dmapi.SMS_USER');
        $postfields['senderID']=Config::get('dmapi.SMS_SENDER_ID');
        $postfields['msgtxt']= $message;
        $postfields['receipientno'] = $number;
        $postfields = http_build_query($postfields);
        $this->curlRequest(Config::get('dmapi.SMS_URL'),$postfields);
    }

    private function curlRequest($url,$postfields){
        $ch = curl_init();        
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postfields);
        $buffer = curl_exec($ch);
        if(empty ($buffer)){ 
            return false;
        }else{ 
            return true;
        } 
    }

    /**
     * [savereturnApi description]
     * @param  [type] $data [description]
     * @param  string $flag [description]
     * @return [type]       [description]
     */
    public function savereturnApi($data,$flag = "readValues"){

    	$_OrderModel = new OrderModel();
    	$_InvoiceModel = new Invoice();
    	$_DmapiOrdersModel = new dmapiOrders();
    	$orderDetails = $_OrderModel->getOrderDetailById($data['order_id']);
    	$userID = $_DmapiOrdersModel->findUserIdByPassword($data['deliver_token']);

    	if($flag == 1){ //"readValues"

    		$returnValue = 0; 
    		if(isset($data['returns'])){

    			foreach($data['returns'] as $return){
    				$price = $_OrderModel->getUnitPricesTaxAndWithoutTaxForLp($data['order_id'],$return['product_id']);
    				$returnValue += $price['singleUnitPriceWithtax'] * $return['return_qty'];
    			}

    		}

    		$invoiceData = $_InvoiceModel->getInvoicedPriceWithOrderIDInvoiceID($data['order_id'],$data['invoice_id']);
    		$returnValueArr['returnValue'] = $returnValue;
    		$returnValueArr['invoicedValue'] = $invoiceData;
    		$returnValueArr['orderValue'] = $orderDetails->grand_total;

    		return $returnValueArr;

    	}elseif ($flag == 2 ) { //"writeValues"
    		
    		$status_id = 67002;

    		$returned = $this->getReturnedByOrderId($data['order_id']);
    		$returnedKeyVal = array();

    		if(count($returned) > 0){
    			return false; // done only to stop double return considering that one order will have one return only
			}

    		if(isset($data['returns'])){

    			$returndata = array();
    			$key = 0;
    			$returnValue = 0;
    			$total_return_items = 0;
            	$total_return_item_qty = 0;
    			foreach($data['returns'] as $return){
    					
    					if($return['return_qty'] > 0 ){

    						$price = $_OrderModel->getUnitPricesTaxAndWithoutTaxForLp($data['order_id'],$return['product_id']);
    						$returnValue += $price['singleUnitPriceWithtax'] * $return['return_qty'];
    						$returndata[$key]['product_id'] = $return['product_id'];
		           			$returndata[$key]['qty'] = $return['return_qty'];
		           			$returndata[$key]['good_qty'] = $return['return_qty'];
		           			$returndata[$key]['bad_qty'] = 0;
		           			$returndata[$key]['dnd_qty'] = 0;
	           				$returndata[$key]['dit_qty'] = 0;
	           				$returndata[$key]['excess_qty'] = 0;
		           			$returndata[$key]['return_reason_id'] = $return['return_reason'];
		           			$returndata[$key]['return_status_id'] = $status_id;
		           			$returndata[$key]['approval_status'] = $status_id;
		           			$returndata[$key]['approved_by_user'] = $userID;
		           			$returndata[$key]['gds_order_id'] = $data['order_id'];
		           			$returndata[$key]['le_wh_id'] = $orderDetails->le_wh_id;
		           			$returndata[$key]['tax_details'] = $price;
		           			$key++;
		           			//adding return qty
	                        		$total_return_items += 1;
		           			$total_return_item_qty += $return['return_qty'];
    					}
    					
    			}

    			if(count($returndata) > 0){

    				$orderData['gds_order_id'] = $data['order_id'];
				    $orderData['total_return_value'] = $returnValue;
				    $orderData['return_status_id'] = $status_id;
				    $orderData['approval_status'] = $status_id;
				    $orderData['total_return_items'] = $total_return_items;
				    $orderData['total_return_item_qty'] = $total_return_item_qty;
		            
				    $query = DB::table('gds_orders')->select('le_wh_id as whareHouseID')->where('gds_order_id',$data['order_id'])->first();
		        	$whId = isset($query->whareHouseID) ? $query->whareHouseID: '';
		        	$whdetails =$this->_roleRepo->getLEWHDetailsById($whId);
		        	$statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
		        	// Log::info("return models +++++++++".$statecode);
		            $refcode = $_OrderModel->getRefCode('SR',$statecode);
		            // Log::info("return models ------------".$refcode);
		            $orderData['reference_no'] = $refcode;
		            $returnGridId = $this->saveReturnGrid($orderData);

		            foreach ($returndata as $key => $orderData) {	

						if($returnGridId){

							$orderData['return_grid_id'] = $returnGridId;
							$orderData['reference_no'] = $refcode;
							$returnData[$key]['return_grid_id'] = $returnGridId;
							$returnData[$key]['reference_no'] = $refcode;

							$return = $this->saveReturns($orderData,$userID);
								if($return){
									$return = true;
									$status = 200;
									$message = $refcode;
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
		            $return_gst = $this->updateGstOnReturnGrid($orderData['gds_order_id']);

					$this->updateReturnOrderStatusonOrderId($orderData['gds_order_id'],$refcode);
					$args = array("ConsoleClass" => 'mail', 'arguments' => array('DmapiReturnOrderTemplate', $returnGridId));
					$InvoiceReference = $_OrderModel->getInvoiceCodefromInvoiceID($data['invoice_id']);
					$collectionData['order_id'] = $data['order_id'];
					$collectionData['return_id'] = $returnGridId;
					$return_data['status'] = $status;
					$return_data['message'] = $message;
					$collectionData['invoice'] = $data['invoice_id'];
					$collectionData['invoice_reference'] = $InvoiceReference[0]->invoice_code;
					$collectionData['collected_on'] = date('Y-m-d');
					$collectionData['collected_by'] = $userID;
					$collectionData['mode_of_payment'] = '';
					$collectionData['reference_num'] = $refcode;
					$collectionData['collection_amount'] = $returnValue; //as per naresh instruction 1st november '-'.$returnValue;:9
					$collectionData['gst'] = $return_gst;
					 //$this->legderEntry($collectionData,$userID);
					//$returnVouchers = $this->saveReturnsVoucher($returndata,$collectionData);saveReturnsVoucherGST
					
					$returnVouchers = $this->saveReturnsVoucherGST($returndata,$collectionData);

					return $return_data;
    			}else{

    				return "No Returns";
    			}
    			


    		}

    	}


    }


    public function updateReturnStatusProductRows($productId,$orderId,$statusId){

    	DB::table('gds_order_products')->where(array('gds_order_id' => $orderId,'product_id' => $productId))
    							->update(array('order_status' => $statusId));
    }


    public function legderEntry($collectionData,$userID){

    	$paymentMethod = new PaymentModel();
    	$paymentMethod->saveCollection($collectionData, $userID);

    }

    public function saveReturnsVoucher($returnData,$collectionData){
    	
    	try{
    			$_OrderModel = new OrderModel;
		    	$_TaxClass = new TaxClass;
		    	$orderData = $_OrderModel->getOrderDetailById($collectionData['order_id']);
				
				if($orderData->cust_le_id == 0){
		    		return false;
		    	}

		    	// $costCenterData = $_BusinessUnit->getBusinesUnitLeWhId($hubId, array('bu.cost_center','bu.bu_name'));
       //          $costCenterGroupData = $_BusinessUnit->getBusinesUnitLeWhId($leWhId, array('bu.cost_center'));
                
       //          $costCenterName = isset($costCenterData->cost_center) ? $costCenterData->cost_center : 'Z1R1D1';
       //          $bu_name = isset($costCenterData->bu_name) ? $costCenterData->bu_name : '';
                
       //          $costCenter = $costCenterName.' - '.$bu_name;

       //          $costCenterGroup = isset($costCenterGroupData->cost_center) ? $costCenterGroupData->cost_center : 'Z1R1';

		    	
		    	$collectionData['collection_amount'] = number_format((float)$collectionData['collection_amount'], 2, '.', ''); 
		    	$legalentity_data = $this->legalEntityDataForVouchers($orderData->cust_le_id);
		  		$date = date('Y-m-d H:i:s');
		    	$Insertdata = array();
					
					$data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
					$data['voucher_type'] = 'Credit Note';  
					$data['voucher_date'] = $collectionData['collected_on'];
					$data['ledger_group'] = 'Sundry Debtors';  //get from 
					$data['ledger_account'] = $legalentity_data[0]->business_legal_name.'-'.$legalentity_data[0]->le_code;  
					$data['tran_type'] = 'Cr'; 
					$data['amount'] = $collectionData['collection_amount'];
					$data['naration'] = 'Being the sales returns made from '.$legalentity_data[0]->business_legal_name.' Order No. '.$orderData->order_code.' dated '.$orderData->order_date.' with return no '.$collectionData['reference_num'].' dated '.$date; 
					$data['cost_centre'] = 'Z1R1D1'; 
					$data['cost_centre_group'] = 'Z1R1'; 
					$data['reference_no'] = $collectionData['invoice_reference'];
					$data['Remarks'] = 'Sales Return';
					$data['is_posted'] = 0;

				$segregationArray= array();
				//$segregationArray['total']['sales'] = 0;
				foreach ($returnData as $return) {

					if($return['tax_details']['singleUnitPrice'] != 0){

						$tax_info = $_TaxClass->getTaxInfoByTaxClassId($return['tax_details']['tax_class']);
						$tax_info = json_decode($tax_info['tally_reference'],true);
						$tax_percentage = $return['tax_details']['tax_percentage'];
						$pricewithOutTax = $return['qty']  * $return['tax_details']['singleUnitPrice'];
						$amountPaidOnTax = $return['qty']  * ( $return['tax_details']['singleUnitPriceWithtax'] - $return['tax_details']['singleUnitPrice']);
						$amountPaidOnTax = number_format((float)$amountPaidOnTax, 2, '.', '');
						if(isset($segregationArray[$tax_percentage])){
							$segregationArray[$tax_percentage]['sales'] += number_format((float)$pricewithOutTax, 2, '.', '');
							$segregationArray[$tax_percentage]['output_tax'] += $amountPaidOnTax;
							$segregationArray[$tax_percentage]['tax_class'] = $tax_info;

						}else{
							$segregationArray[$tax_percentage]['sales'] = number_format((float)$pricewithOutTax, 2, '.', '');
							$segregationArray[$tax_percentage]['output_tax'] = $amountPaidOnTax;
							$segregationArray[$tax_percentage]['tax_class'] = $tax_info;
						}

					}			

				}
				//DB::enableQueryLog();
				//var_dump($segregationArray);
				$drSum = 0;
				foreach ($segregationArray as $key => $values){			


							$temp_data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
							$temp_data['voucher_type'] = 'Credit Note';  
							$temp_data['voucher_date'] = $collectionData['collected_on'];
							$temp_data['ledger_account'] = 'Output '.$values['tax_class']['IO_CODE'];
							$temp_data['ledger_group'] = 'Duties & Taxes';
							$temp_data['tran_type'] = 'Dr'; 
							$temp_data['amount'] = $values['output_tax'];
							$temp_data['naration'] = 0; 
							$temp_data['cost_centre'] = 'Z1R1D1'; 
							$temp_data['cost_centre_group'] = 'Z1R1'; 
							$temp_data['reference_no'] = $collectionData['invoice_reference'];
							$temp_data['Remarks'] = 'Sales Return';
							$temp_data['is_posted'] = 0;
							$drSum += $values['output_tax']; 

							$insertdata[] = $temp_data;	

							$temp_data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
							$temp_data['voucher_type'] = 'Credit Note';  
							$temp_data['voucher_date'] = $collectionData['collected_on'];
							$temp_data['ledger_account'] = $values['tax_class']['RETURN_CODE'];//$checkinTallyOutput[0]->tlm_group;
							$temp_data['ledger_group'] = 'Sales Account';//$checkinTallyOutput[0]->tlm_name;
							$temp_data['tran_type'] = 'Dr'; 
							$temp_data['amount'] = $values['sales'];
							$temp_data['naration'] = 0; 
							$temp_data['cost_centre'] = 'Z1R1D1'; 
							$temp_data['cost_centre_group'] = 'Z1R1'; 
							$temp_data['reference_no'] = $collectionData['invoice_reference'];
							$temp_data['Remarks'] = 'Sales Return';
							$temp_data['is_posted'] = 0;
							$drSum += $values['sales'];		

							$insertdata[] = $temp_data;	
							

				}

				// dr-cr
				// dr > 0 thenn cr
				// dr < 0 then dr 
				
				if($drSum != $collectionData['collection_amount']){	

							$diff = $drSum - $collectionData['collection_amount'];
							$temp_data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
							$temp_data['voucher_type'] = 'Credit Note';  
							$temp_data['voucher_date'] = $collectionData['collected_on'];
							$temp_data['ledger_account'] = '711900 : Round off';//$checkinTallyOutput[0]->tlm_group;
							$temp_data['ledger_group'] = '710000 : General Admin Expenses';//$checkinTallyOutput[0]->tlm_name;
							$temp_data['tran_type'] = ($diff > 0)?'Cr' : 'Dr';
							$temp_data['amount'] = abs($diff);;
							$temp_data['naration'] = 0; 
							$temp_data['cost_centre'] = 'Z1R1D1'; 
							$temp_data['cost_centre_group'] = 'Z1R1'; 
							$temp_data['reference_no'] = $collectionData['invoice_reference'];
							$temp_data['Remarks'] = 'Sales Return';
							$temp_data['is_posted'] = 0;
							$insertdata[] = $temp_data;	
				}

				DB::table('vouchers')->insert($data);
				DB::table('vouchers')->insert($insertdata);
				return true;
    	}catch(\Exception $e){
    		//Log::error($e->getMessage());
    		return false;
    	}   	

    }

	/**
	 * [saveReturnsVoucherGST description]
	 * @param  [type] $returnData     [description]
	 * @param  [type] $collectionData [description]
	 * @return [type]                 [description]
	 */
    public function saveReturnsVoucherGST($returnData,$collectionData){
    	
    	try{
    			$_OrderModel = new OrderModel;
		    	$_TaxClass = new TaxClass;
		    	$orderData = $_OrderModel->getOrderDetailById($collectionData['order_id']);
				if($orderData->cust_le_id == 0){
		    		return false;
		    	}


                $hubId = isset($orderData->hub_id) ? $orderData->hub_id : 0;
                $leWhId = isset($orderData->le_wh_id) ? $orderData->le_wh_id : 0;

                $gdsObj = new GdsBusinessUnit();                        

                $costCenterData = $gdsObj->getBusinesUnitLeWhId($hubId, array('bu.cost_center','bu.bu_name'));
                $costCenterGroupData = $gdsObj->getBusinesUnitLeWhId($leWhId, array('bu.cost_center'));
                
                $costCenterName = isset($costCenterData->cost_center) ? $costCenterData->cost_center : 'Z1R1D1';
                $bu_name = isset($costCenterData->bu_name) ? $costCenterData->bu_name : '';
                
                $costcenter = $costCenterName.' - '.$bu_name;

                $costcenter_grp = isset($costCenterGroupData->cost_center) ? $costCenterGroupData->cost_center : 'Z1R1';

		    	
		    	$collectionData['collection_amount'] = number_format((float)$collectionData['collection_amount'], 2, '.', ''); 
		    	$legalentity_data = $this->legalEntityDataForVouchers($orderData->cust_le_id);
		  		$date = date('Y-m-d H:i:s');
		    	
				$data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
				$data['voucher_type'] = 'Credit Note';  
				$data['voucher_date'] = $collectionData['collected_on'];
				$data['ledger_group'] = 'Sundry Debtors';  //get from 
				$data['ledger_account'] = $legalentity_data[0]->business_legal_name.'-'.$legalentity_data[0]->le_code;  
				$data['tran_type'] = 'Cr'; 
				$data['amount'] = $collectionData['collection_amount'];
				$data['naration'] = 'Being the sales returns made from '.$legalentity_data[0]->business_legal_name.' Order No. '.$orderData->order_code.' dated '.$orderData->order_date.' with return no '.$collectionData['reference_num'].' dated '.$date; 
				$data['cost_centre'] = $costcenter; 
				$data['cost_centre_group'] = $costcenter_grp; 
				$data['reference_no'] = $collectionData['invoice_reference'];
				$data['Remarks'] = 'Sales Return';
				$data['is_posted'] = 0;

				$master_values = array(142003,142004,142005,142011,142012,142018);
				$master_lookupValues = $this->getMasterLookupValues($master_values);

				$Insertdata = array();
				$total_tax_paid = 0;

				/*
					'cgst_total' => $data->totCGST,
					'sgst_total' => $data->totSGST,
					'igst_total' => $data->totIGST,
					'utgst_total' => $data->totUTGST

				 */

				unset($collectionData['gst']['utgst_total']);
				unset($collectionData['gst']['igst_total']);
				$dr_value = 0;
				// foreach ($collectionData['gst'] as $key => $value){


				// 	$insert_flag = false;
				// 	$value = number_format((float)$value, 2, '.', '');

					
				// 	if($key == 'cgst_total' && (float)$value > 0){// making it zero to keet the data cool

				// 		$master_lk_value = 142005;
				// 		$insert_flag = true;
				// 	}

				// 	if($key == 'sgst_total' && (float)$value > 0){ // making it zero to keet the data cool

				// 		$master_lk_value = 142003;
				// 		$insert_flag = true;
				// 	}

				// 	// if($key = 'igst_total' && (int)$value != 0){ // making it zero to keet the data cool

				// 	// 	$master_lk_value = 142004;
				// 	// 	$insert_flag = true;
				// 	// }

				// 	// if($key => 'utgst_total' && (int)$value != 0){ // making it zero to keet the data cool

				// 	// 	$value = 142005;
				// 	// 	$insert_flag = true;
				// 	// }
				

				// 	if($insert_flag){

				// 		$dr_value += $value;
				// 		$temp_data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
				// 		$temp_data['voucher_type'] = 'Credit Note';  
				// 		$temp_data['voucher_date'] = $collectionData['collected_on'];
				// 		$temp_data['ledger_account'] = $master_lookupValues[$master_lk_value]['master_lookup_name'];
				// 		$temp_data['ledger_group'] = $master_lookupValues[$master_lk_value]['description'];
				// 		$temp_data['tran_type'] = 'Dr'; 
				// 		$temp_data['amount'] = $value;
				// 		$temp_data['naration'] = 0; 
				// 		$temp_data['cost_centre'] = $costcenter; 
				// 		$temp_data['cost_centre_group'] = $costcenter_grp; 
				// 		$temp_data['reference_no'] = $collectionData['invoice_reference'];
				// 		$temp_data['Remarks'] = 'Sales Return';
				// 		$temp_data['is_posted'] = 0;
				// 		$insertdata[] = $temp_data;
				// 	}					
				// }

				$taxData = DB::select(DB::raw("SELECT ROUND(gr.tax_per) AS 'tax_per',SUM(ROUND(gr.sub_total,2)) AS 'sub_total',SUM(gr.CGST) as 'cgst_total',SUM(gr.SGST) as 'sgst_total',SUM(gr.IGST) as 'igst_total',SUM(gr.UTGST) as 'utgst_total',gr.gds_order_id FROM `gds_returns` gr WHERE return_grid_id=".$collectionData['return_id']."  GROUP BY tax_per;
"));
				$salesReturnAmmount = 0;
				foreach ($taxData as $key => $value) {
					# code...
					$insert_flag = true;
					$value->cgst_total = number_format((float)$value->cgst_total, 2, '.', '');
					$value->sgst_total = number_format((float)$value->sgst_total, 2, '.', '');
					$value->igst_total = number_format((float)$value->igst_total, 2, '.', '');
					$value->utgst_total = number_format((float)$value->utgst_total, 2, '.', '');
					$gstVals = [];

					if($value->sgst_total>0 || $value->cgst_total>0){
						$tax_per = $value->tax_per/2;

					$gstVals = [
								'142005'=>['amount'=>$value->cgst_total,'tax_per'=>$tax_per],
								'142003'=>['amount'=>$value->sgst_total,'tax_per'=>$tax_per]
								];

					}else if($value->igst_total>0){
						$tax_per = $value->tax_per;
						$gstVals = ['142004'=>['amount'=>$value->igst_total,'tax_per'=>$tax_per]];
					}

					if($value->utgst_total>0){
						$tax_per = $value->tax_per/2;

							$gstVals = [
							'142005'=>['amount'=>$value->cgst_total,'tax_per'=>$tax_per],
							'142018'=>['amount'=>$value->utgst_total,'tax_per'=>$tax_per]];
					}

					foreach ($gstVals as $maskey => $masvalues) {
						$dramount = $masvalues['amount'];
						$dr_value += $dramount;
						$taxper = $masvalues['tax_per'];
						$temp_data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
						$temp_data['voucher_type'] = 'Credit Note';  
						$temp_data['voucher_date'] = $collectionData['collected_on'];
						$temp_data['ledger_account'] = $master_lookupValues[$maskey]['master_lookup_name'].'@'.($tax_per).'%';
						$temp_data['ledger_group'] = $master_lookupValues[$maskey]['description'];
						$temp_data['tran_type'] = 'Dr'; 
						$temp_data['amount'] = $dramount;
						$temp_data['naration'] = 0; 
						$temp_data['cost_centre'] = $costcenter; 
						$temp_data['cost_centre_group'] = $costcenter_grp; 
						$temp_data['reference_no'] = $collectionData['invoice_reference'];
						$temp_data['Remarks'] = 'Sales Return';
						$temp_data['is_posted'] = 0;
						$insertdata[] = $temp_data;
					}

					if($insert_flag){
                        $salesReturnAmmount += $value->sub_total;
                        $sub_total = $value->sub_total;
						/// sales return entry collection value - taxvalue
						$temp_data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
						$temp_data['voucher_type'] = 'Credit Note';  
						$temp_data['voucher_date'] = $collectionData['collected_on'];
						$temp_data['ledger_account'] = $master_lookupValues[142012]['master_lookup_name'].'@'.$value->tax_per.'%';
						$temp_data['ledger_group'] = $master_lookupValues[142012]['description'];
						$temp_data['tran_type'] = 'Dr'; 
						$temp_data['amount'] = $sub_total;
						$temp_data['naration'] = 0; 
						$temp_data['cost_centre'] = $costcenter; 
						$temp_data['cost_centre_group'] = $costcenter_grp; 
						$temp_data['reference_no'] = $collectionData['invoice_reference'];
						$temp_data['Remarks'] = 'Sales Return';
						$temp_data['is_posted'] = 0;
						$insertdata[] = $temp_data;

					}
				}



				// $salesReturnAmmount = $collectionData['collection_amount'] - $dr_value;
				// /// sales return entry collection value - taxvalue
				// $temp_data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
				// $temp_data['voucher_type'] = 'Credit Note';  
				// $temp_data['voucher_date'] = $collectionData['collected_on'];
				// $temp_data['ledger_account'] = $master_lookupValues[142012]['master_lookup_name'];
				// $temp_data['ledger_group'] = $master_lookupValues[142012]['description'];
				// $temp_data['tran_type'] = 'Dr'; 
				// $temp_data['amount'] = $salesReturnAmmount;
				// $temp_data['naration'] = 0; 
				// $temp_data['cost_centre'] = $costcenter; 
				// $temp_data['cost_centre_group'] = $costcenter_grp; 
				// $temp_data['reference_no'] = $collectionData['invoice_reference'];
				// $temp_data['Remarks'] = 'Sales Return';
				// $temp_data['is_posted'] = 0;
				// $insertdata[] = $temp_data;

				
				$drSumCheck = $collectionData['collection_amount'] - ($salesReturnAmmount + $dr_value);

				if($drSumCheck != 0){
							$drSum = $salesReturnAmmount + $dr_value;

							$diff = $drSum - $collectionData['collection_amount'];
							$temp_data['voucher_code'] = $collectionData['reference_num'];//$collectionData['reference_num']; 
							$temp_data['voucher_type'] = 'Credit Note';  
							$temp_data['voucher_date'] = $collectionData['collected_on'];
							$temp_data['ledger_account'] = $master_lookupValues[142011]['master_lookup_name'];
							$temp_data['ledger_group'] = $master_lookupValues[142011]['description'];
							$temp_data['tran_type'] = ($diff > 0)?'Cr' : 'Dr';
							$temp_data['amount'] = abs($diff);
							$temp_data['naration'] = 0; 
							$temp_data['cost_centre'] = $costcenter; 
							$temp_data['cost_centre_group'] = $costcenter_grp; 
							$temp_data['reference_no'] = $collectionData['invoice_reference'];
							$temp_data['Remarks'] = 'Sales Return';
							$temp_data['is_posted'] = 0;
							$insertdata[] = $temp_data;
				}
			    //print_r($insertdata);die();
				DB::table('vouchers')->insert($data);
				DB::table('vouchers')->insert($insertdata);
				return true;
    	}catch(\Exception $e){
    		
    		//Log::error($e->getMessage());
    		return $e->getMessage();
    	} 

    }


    public function getMasterLookupValues($array){

    	$idlist = implode(",",$array);
    	$query = "select * from master_lookup where value in ($idlist)";
    	$data = DB::select($query);

    	$data = json_decode(json_encode($data),true);
    	$return = array();
    	foreach ($data as $value) {
    		
    		$return[$value['value']] = $value;
    	}

    	return $return;

    }

    public function legalEntityDataForVouchers($legal_entity_id){

    	$query = DB::table('legal_entities')
            ->where('legal_entity_id',$legal_entity_id);
        $data = $query->get()->all();
        return $data;
    }


    public function updateOrderCompleteStatusOnFinanceApproval($order_id){

    	try{

    		$remmitanceApproval =  new LedgerModel();
	    	$remmitanceApprovalStatus = $remmitanceApproval->getRemittanceApprStatusByOrderId($order_id);
	    	if($remmitanceApprovalStatus){
	    		return true;
	    	}else{
	    		return false;
	    	}
	    	    		
    	}catch(Exception $e){
    		//return false;
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    	}
    	
    }

    public function putawaylist($return_id){

    	$data['putaway_source'] = 'SR';
    	$data['source_id'] = $return_id;
    	$data['putaway_status'] = 12803;
    	$putawaylist_id = DB::table('putaway_list')->insertGetId($data);
    	$WarehouseConfigApi = new WarehouseConfigApi();
    	 $result = $WarehouseConfigApi->putawayBinAllocation($putawaylist_id);
    	$result = json_decode($result,true);
    	if($result['status'] == 'failed'){

    		$grnMail = new Grn();
    		$grnMail->sendPutAwayFailedMail($putawaylist_id,$result['data']);
    	}

    	return;
    }

	public function getReturnGridInfoByOrderId($orderId, $fields, $resultSet='first') {
       try {
            $query = DB::table('gds_return_grid')->select($fields);
            $query->where('gds_return_grid.gds_order_id', $orderId);
            if($resultSet == 'first') {
            	return $query->first();
            }

            if($resultSet == 'all') {
            	return $query->get()->all();
            }
       }
       catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
   	}

    public function getReturnCountWithStatus($filters) {
    	$fields = array('orders.order_status_id', 'orders.order_transit_status', 'grid.return_status_id', 
    		DB::raw('COUNT(DISTINCT orders.gds_order_id) as total'));

    	$query = DB::table('gds_orders as orders')->select($fields);
    	$query->join('gds_return_grid as grid','orders.gds_order_id','=','grid.gds_order_id');
    	//$query->join('gds_returns as returns','grid.return_grid_id','=','returns.return_grid_id');
    	$query->whereIn('orders.order_status_id', array(17022,17023));
    	
        if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0){
            $bu_id=Session::get('business_unitid');
            $userID = Session('userId');
            $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLB0001",$userID);
            if($globalAccess){
            	$data = DB::select(DB::raw("call getAllBuHierarchyByID($bu_id)"));
            }
            else{
            	$data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
            }
            // $data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
            $le_wh_ids=isset($data[0]->le_wh_ids) ? $data[0]->le_wh_ids : 0;
            $array = explode(',', $le_wh_ids);
            $hubdata = DB::table('dc_hub_mapping')->select(DB::raw('GROUP_CONCAT(hub_id) as hubids'))->whereIn('dc_id',$array)->get()->all();
            $hubdata = isset($hubdata[0]->hubids) ? $hubdata[0]->hubids : 0;
        }else{
            $query->whereRaw("orders.le_wh_id IN (0)");
        }

        if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && count($array)>0 && $le_wh_ids!=''){
            
            $query->whereRaw("orders.le_wh_id IN (".$le_wh_ids.")");
        }

        if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && !empty($hubdata)){

            $hubdata=explode(',', $hubdata);
            $query->whereIn("orders.hub_id", $hubdata);

        }
		
		$query->groupBy(array('orders.order_status_id', 'grid.return_status_id', 'orders.order_transit_status'));

		$returnDataArr = $query->get()->all();
		$dataArr = array();
		if(is_array($returnDataArr) && count($returnDataArr) > 0 ) {
			foreach ($returnDataArr as $data) {
				if(empty($data->order_transit_status)) {
					$index = $data->order_status_id.'_'.$data->return_status_id;
				}
				else {
					$index = $data->order_status_id.'_'.$data->return_status_id.'_'.$data->order_transit_status;
				}
								
				if(array_key_exists($index, $dataArr)) {
					$dataArr[$index] = $dataArr[$index] + $data->total;
				}
				else {
					$dataArr[$index] = $data->total;
				}
				
			}
		}
		#echo '<pre>';print_r($dataArr);die;
        return $dataArr;           
    }
   public function getReturnCount($data,$filters){
        $fields=array('orders.order_status_id','orders.order_transit_status','grid.return_status_id', 
            DB::raw('COUNT(DISTINCT orders.gds_order_id) as total'));
        $query = DB::table('gds_orders as orders')->select($fields);
        $query->join('gds_return_grid as grid','orders.gds_order_id','=','grid.gds_order_id');
        $query->join('gds_returns as returns','grid.return_grid_id','=','returns.return_grid_id');
        $query->whereIn('orders.order_status_id', array(17022,17023,17008));
        if($data =='totRWMQ'){
            $query->where('returns.dnd_qty','>',0);
        }
        if($data =='totRWDQ'){
    		$query->where('returns.dit_qty','>',0);
    	}
        $array=array();
        if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0){
            $bu_id=Session::get('business_unitid');
            $userID = Session('userId');
            $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLB0001",$userID);
            if($globalAccess){
            	$data = DB::select(DB::raw("call getAllBuHierarchyByID($bu_id)"));
            }
            else{
            	$data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
            }
            // $data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
            $le_wh_ids=trim(isset($data[0]->le_wh_ids)) ? trim($data[0]->le_wh_ids) : 0;
            $array = explode(',', $le_wh_ids);
            $hubdata = DB::table('dc_hub_mapping')->select(DB::raw('GROUP_CONCAT(hub_id) as hubids'))->whereIn('dc_id',$array)->get()->all();
            $hubdata = isset($hubdata[0]->hubids) ? $hubdata[0]->hubids : 0;
            
        }

        if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && count($array)>0 && $le_wh_ids!=''){
           	$query->whereRaw("orders.le_wh_id IN (".$le_wh_ids.")");    
        }else{
            $query->whereRaw("orders.le_wh_id IN (0)");
        }

        if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && !empty($hubdata)){

            $query->whereRaw("orders.hub_id IN (".$hubdata.")");
        }        
       return $query->get()->all();
    }
	public function getShortCollections($filters)
    {
       	$leGalId = Session::get('legal_entity_id');
   
	$querydc='';				
	    if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0)
	    {
	                $bu_id=Session::get('business_unitid');
	                $userID = Session('userId');
		            $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLB0001",$userID);
		            if($globalAccess){
		            	$data = DB::select(DB::raw("call getAllBuHierarchyByID($bu_id)"));
		            }
		            else{
		            	$data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
		            }
	                // $data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
	                $le_wh_ids=isset($data[0]->le_wh_ids) ? $data[0]->le_wh_ids : 0;
	                $array = explode(',', $le_wh_ids);
	     }
        
        if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && count($le_wh_ids)>0 && $le_wh_ids != "")
        {  
                $querydc = ' WHERE `orders`.`le_wh_id` IN ('.$le_wh_ids.')';
        }else{
        	    $querydc = ' WHERE `orders`.`le_wh_id` IN (0)';
        }
	$querycount = DB::select(DB::raw('SELECT SUM(innr.cnt) as totOrders FROM ( 
	SELECT COUNT(DISTINCT(orders.gds_order_id)) AS cnt
	FROM `collections` AS `c` 
	INNER JOIN `gds_orders` AS `orders` ON `c`.`gds_order_id` = `orders`.`gds_order_id` 
	INNER JOIN `collection_history` as `ch` ON `c`.`collection_id`=`ch`.`collection_id`' 
	.$querydc.' 
	GROUP BY `orders`.`gds_order_id` 
	HAVING ROUND(SUM(IFNULL(c.invoice_amount,0)-IFNULL(c.return_total,0)-IFNULL(c.collected_amount,0)-IFNULL(c.discount_amt,0)),2)>=1) innr'));
	return isset($querycount[0]->totOrders) ? (int)$querycount[0]->totOrders : 0;			
    }

    public function getReturnApproved($data,$filters){
      $fields=array('orders.order_status_id','orders.order_transit_status','grid.return_status_id', 
    		DB::raw('COUNT(DISTINCT orders.gds_order_id) as total'));
      $query = DB::table('gds_orders as orders')->select($fields);
    	$query->join('gds_return_grid as grid','orders.gds_order_id','=','grid.gds_order_id');
    	$query->join('gds_returns as returns','grid.return_grid_id','=','returns.return_grid_id');
    	$query->whereIn('orders.order_status_id', array(17022,17023,17008));
    	$array=array();
    	if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0)
    	{
			$bu_id=Session::get('business_unitid');
			$userID = Session('userId');
            $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLB0001",$userID);
            if($globalAccess){
            	$data = DB::select(DB::raw("call getAllBuHierarchyByID($bu_id)"));
            }
            else{
            	$data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
            }
			// $data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
            $le_wh_ids=isset($data[0]->le_wh_ids) ? $data[0]->le_wh_ids : 0;
            $array = explode(',', $le_wh_ids);
	        $hubdata = DB::table('dc_hub_mapping')->select(DB::raw('GROUP_CONCAT(hub_id) as hubids'))->whereIn('dc_id',$array)->get()->all();
	        $hubdata = isset($hubdata[0]->hubids)? $hubdata[0]->hubids : 0;
		}
    	if($data =='totRAWMQ'){
    		$query->where('returns.dnd_qty','>',0);
    	    $query->where('returns.return_status_id','=',57066);
    	}
        if($data =='totRAWDQ'){
    		$query->where('returns.dit_qty','>',0);
			$query->where('returns.return_status_id','=',57066);
    	}   	
    	if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && count($array)>0 && $le_wh_ids != ""){
			
			$query->whereRaw("orders.le_wh_id IN (".$le_wh_ids.")");	
		}else{
			$query->whereRaw("orders.le_wh_id IN (0)");
		}
    	if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && !empty($hubdata)){
			$query->whereRaw("orders.hub_id IN (".$hubdata.")");	
		}		
       return $query->get()->all();
    }

    /**
     * [rectifyTaxOnGdsReturnId description]
     * @return [type] [description]
     */
    public function rectifyTaxOnGdsReturnId($gds_returns_id,$tax_amount,$tax_class_details,$qty){

    	DB::table('gds_returns')
    	->where('return_id', $gds_returns_id)
    	->update(array(

    				'unit_price' => $tax_class_details['singleUnitPriceWithtax'], //storing with tax
					'tax_per' => $tax_class_details['tax_percentage'],
					'tax_amt' => $tax_class_details['singleUnitPriceWithtax'] - $tax_class_details['singleUnitPrice'],
					'tax_total' => $tax_amount,
					'sub_total' => $tax_class_details['singleUnitPrice'] * $qty, //total without tax
					'total' => $tax_class_details['singleUnitPriceWithtax'] * $qty,

    		));    	

    }
    
    /**
     * [updateGstOnReturnGrid description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public function updateGstOnReturnGrid($orderId) {

    	try{
    		

	    	$query = DB::table('gds_returns')->select(array(
												    		DB::raw('SUM(SGST) AS totSGST'),
													    	DB::raw('SUM(CGST) AS totCGST'),
													    	DB::raw('SUM(IGST) AS totIGST'),
															DB::raw('SUM(UTGST) AS totUTGST')
												    		),'',false);
	            $query->where('gds_order_id',$orderId);
	        $data = $query->first();

	    	DB::table('gds_return_grid')
	    	->where('gds_order_id', $orderId)
	    	->update(array(
	    				'cgst_total' => $data->totCGST,
						'sgst_total' => $data->totSGST,
						'igst_total' => $data->totIGST,
						'utgst_total' => $data->totUTGST
	    				));


	    	return array(
	    				'cgst_total' => $data->totCGST,
						'sgst_total' => $data->totSGST,
						'igst_total' => $data->totIGST,
						'utgst_total' => $data->totUTGST
	    				);			 
				    	    		

    	}catch(\Exception $e){
    		return false;
    	}

    }

    /**
     * [getGstValuesOnReturnCode description]
     * @return [type] [description]
     */
    public function getGstValuesOnReturnCode($return_id){

    	//$query = DB::table('gds_return_grid')->select()

    }

    /**
     * saveExcessReturn Saves excess returns
     * @param  array $data 
     * @return boolean
     */

    public function saveExcessReturn($data,$userID = NULL){

    	if(is_null($userID)){
    		$userID = Session('userId');
    	}
    	$_OrderModel = new OrderModel();
		$orderId= $data['gds_order_id'];
		$productId= $data['product_id'];
		$refcode = $data['reference_no'];

		DB::beginTransaction();
		try {
			$return_id = DB::table('gds_returns')
							->insertGetId(array(
								'product_id' => $data['product_id'],
								'gds_order_id' => $data['gds_order_id'],
								'reference_no' => $refcode,
								'return_reason_id' => $data['return_reason_id'],					
								'return_status_id'=>$data['return_status_id'],
								'return_by' => $userID,
								'qty' => $data['qty'],
								'dit_qty' => $data['dit_qty'],
								'dnd_qty' => $data['dnd_qty'],
								'excess_qty' => $data['excess_qty'],

								'approved_quantity' => $data['good_qty'],
								'quarantine_qty' => $data['bad_qty'],
								'return_grid_id' => $data['return_grid_id'],	

								'is_extra'=>1,					
								'created_by' => $userID,
								'approval_status' => $data['approval_status'],
	           					'approved_by_user' => $data['approved_by_user'],
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
						));
			$id = DB::table('gds_return_track')
							->insertGetId(array(
								'product_id' => $data['product_id'],
								'initiated_date' => date('Y-m-d'),
								'return_track_status_id' => '',
								'track_id' => '',
								'received_date' => date('Y-m-d H:i:s'),
								'return_reason_id' => $data['return_reason_id'],	
								'approved' => $data['return_status_id'],//order status
								'return_id' => $return_id,				
								'created_by'=> $userID,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_by' => $userID,
								'updated_at' => date('Y-m-d H:i:s')
						));


			DB::commit();
    		// all good
			return true;		

        } catch (Exception $e) {

        		DB::rollback();
            	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			}
	}

	public function checkPurchaseReturn($sr_inv_no){
		$purchase_data = DB::table('purchase_returns')
						->where('sr_invoice_code',$sr_inv_no)
						->orderBy('created_at','desc')
						->first();
		return $purchase_data;
	}
	public function excelReports($fdate,$tdate){
		$reports = DB::select(DB::raw("CALL getDCFCSalesReport('".$fdate."','".$tdate."')"));        
        $data = json_decode(json_encode($reports),true);      
        return $data;
	}
	public function retailerExcelReports($fdate,$tdate){
		$reports = DB::select(DB::raw("CALL getAPOBDCSalesReport('".$fdate."','".$tdate."')"));        
        $data = json_decode(json_encode($reports),true);      
        return $data;
	}
	public function apobExcelReports($fdate,$tdate){
		$reports = DB::select(DB::raw("CALL getRetailerSalesReport('".$fdate."','".$tdate."')"));        
        $data = json_decode(json_encode($reports),true);      
        return $data;
	}
	public function getGDSProductsQtyByPrdID($prdid,$orderid){
		$getprdqty=DB::table('gds_invoice_items')->select('qty')->where('gds_order_id',$orderid)->where('product_id',$prdid)->first();
		return $getprdqty;
	}
}
