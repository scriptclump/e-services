<?php

namespace App\Modules\DmapiV2\Models;
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use App\Modules\Orders\Models\OrderModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\models\Warehouse\warehouseModel;
use Cache;
use App\Lib\Queue;
use Exception;
use Log;
use App\Modules\Orders\Models\Inventory;

class GDSProducts extends Model {

	protected $primaryKey = 'gds_order_prod_id';
    public $timestamps = false;
    protected $table = 'gds_order_products';
    private $gds_order_id;
    private $cust_type;
    private $order_status_id;
    private $buyer_state_id;
    private $seller_state_id;
    private $total_tax = 0;
    private $wh_le_id;
    private $total_line_items = NULL;
    private $total_items = NULL;

    public function getTotalLineItems(){

        return $this->total_items;
    }

    public function getTotalLineItemsCount(){
        return $this->total_line_items;
    }


    public function setGdsOrderId($gds_order_id){
        $this->gds_order_id = $gds_order_id;
    }
    public function setCustType($cust_type){
    	$this->cust_type = $cust_type;
    }

    public function getTotalTax(){
    	return $this->total_tax;
    }

    public function setOrderStatusIdforProductFeild($order_status_id){
    	$this->order_status_id = $order_status_id;
    }

    public function setBuyerStateId($buyer_state_id){

    	$this->buyer_state_id = $buyer_state_id;
    }

    

    public function  setLeWareHouseDetails($warehouseid){
    	$this->wh_le_id = $warehouseid;
    	$_warehouseModel = new warehouseModel;
        $warehouse = $_warehouseModel->getwareHousedata($warehouseid);
        if (count($warehouse) == 0) {
            $warehouse_statecode = 4033;
        } else {
            $warehouse_statecode = $warehouse['state'];
        }
        $this->seller_state_id = (int) $warehouse_statecode;

    }
    /**
     * [insertGdsOrderProduct description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function insertGdsOrderProductBulk($data){

    	try {
    		$products = json_decode($data['orderdata']);
	    	$products = $products->product_info;
        $orderdata = json_decode($data['orderdata'],1);
                if(isset($orderdata['order_info'])){
	    	$order_info = $orderdata['order_info'];
                if(isset($order_info['customer_type'])) {
                    $this->setCustType($order_info['customer_type']);
                }
                }
	    	$gds_products = array();
            $pack_list = array();
            $subtotalwithtax = 0;
            $_product_total = 0;
	    	foreach ($products as $product) {
	    		//get product info from cache no need to visit db again 
                $keyString = 'product_'. $product->scoitemid;
	    		$productData = Cache::get($keyString,false);

	    		if(!$productData){

	    			$productData = DB::table('products')
	                    ->select(
	                            'products.*','product_content.*'
	                    )
	                    ->leftJoin('product_content', 'products.product_id', '=', 'product_content.product_id')
	                    ->where('products.product_id', $product->scoitemid)
	                    ->first();
	                if(!$productData || count($productData) == 0 ){
	                	throw new Exception("product id with $product->scoitemid does not exist");	                	
	                }
	                Cache::put($keyString,json_encode($productData),360);	                
	    		}else {

	    			$productData = json_decode($productData);
	    		}

                try{
                    // EButor lp
                    $elp_date = date('Y-m-d');
                    $lewhid = isset($product->le_wh_id)?$product->le_wh_id:0;
                    $elpQuery = "select getGrossElpByDtLeWhId($product->scoitemid,'$elp_date',$lewhid) as elp";
                    $elpTemp = DB::select($elpQuery);
                    if(count($elpTemp) > 0){
                        $elp = $elpTemp[0]->elp;
                    }else{

                        throw new Exception("Get Elp call form db on $product->scoitemid failed");
                    }

                    //warehouse lp
                    $lewhid = isset($product->le_wh_id)?$product->le_wh_id:0;
                    $wh_elpQuery = "select getProductElpByDtLeWhId($product->scoitemid,'$elp_date',$lewhid) as elp";
                    $wh_elpTemp = DB::select($wh_elpQuery);
                    if(count($wh_elpTemp) > 0){
                        $wh_elp = $wh_elpTemp[0]->elp;
                    }else{

                        throw new Exception("Get Elp call form db on $product->scoitemid failed");
                    }

                    $espQuery = "select getProductEsp_wh($product->scoitemid,$lewhid) as esp";
                    $espQuery = DB::select($espQuery);
                    if(count($espQuery) > 0){
                        $esp = $espQuery[0]->esp;
                    }else{

                        $esp = $product->sellprice;
                    }
                    
                }catch( Exception $e){

                    return $e;
                }
                
	    		/**
	    		 * @PRASENJIT COMPLETELY RELYING ON PRODUCT TOT FOR NAME IF NO NAME 
	    		 * NO ORDER TITLE STRAIGHT FORWARD NNO JUGGAD 
	    		 */

	            /***
	            	Optional Esu quantity 
	            **/

	            if (isset($product->esu_quantity)) {
	                $esu_quantity = $product->esu_quantity;
	            } else {
	                $esu_quantity = NULL;
	            }

	            if (isset($product->parent_id)) {

	                if ($product->scoitemid == $product->parent_id) {
	                    $parent_id = NULL;
	                } else {

	                    $parent_id = $product->parent_id;
                        if($parent_id == "" || $parent_id == null || $parent_id == "null"){
                            $parent_id = NULL;
                        }
	                }
	            } else {
	                $parent_id = NULL;
	            }

                if (isset($product->star)) {
                    $star = $product->star;
                }else{
                    $star = 0;
                }

                /*
                    Promotion ID @product_slab_id
                 */
                if (isset($product->product_slab_id)) {
                    $product_slab_id = $product->product_slab_id;
                }else{
                    $product_slab_id = 0;
                }

                /**
                 * [$discount some concepts without any use case]
                 * @var decimal
                 */
                $discount = 0;
                if(isset($product->discount)){

                    if($product->discount == ""){
                        $discount = 0;
                    }else{

                        $discount = (float)$product->discount;
                    }
                }

                $discount_amount = 0.00000;
                if(isset($product->discount_amount)){

                    if($product->discount_amount == ""){
                        $discount_amount = 0.00000;
                    }else{

                        $discount_amount = $product->discount_amount;
                    }
                }


                //calculating tax minus the discount we are giving
                $final_total = $product->total - $discount_amount;
                $orderdatacheck = json_decode($data['orderdata']);
                $orderdatacheck = $orderdatacheck->order_info;
                $discount_before_tax = isset($orderdatacheck->discount_on_tax_less)?$orderdatacheck->discount_on_tax_less:0;

                $taxInfo =  $this->gdsProductsGetTax($this->gds_order_id,$product->scoitemid,$final_total,$discount_before_tax);



	            if($taxInfo instanceof \Exception){

                    throw new Exception($taxInfo->getMessage());
                    
                }


                /**
                 * Line items and line qty count 
                 */
                
                if(is_null($this->total_items)){
                    $this->total_items = 0;
                }

                if(is_null($this->total_line_items)){
                    $this->total_line_items = 0;
                }

                $this->total_items += 1;
                $this->total_line_items += $product->quantity;

	            //var_dump($productData);
                $_product_total = ($discount_before_tax==0)?($product->total - $product->discount_amount):($product->total - $product->discount_amount+$taxInfo['tax']);
	           	$gds_products[] = array(
				                            'gds_order_id' => $this->gds_order_id,
				                            'product_id' => $product->scoitemid,
				                            'mp_product_id' => $product->channelitemid,
				                            'qty' => $product->quantity,
				                            'mrp' => $product->price,
				                            'price' => $taxInfo['price'],
				                            'cost' => $product->subtotal,//as reffer by Satish
                                            'elp' => $elp,
                                            'wh_lp' => $wh_elp,
				                            'tax' => $taxInfo['tax'],
				                            'total' => $_product_total, //$product->quantity * $product->price
				                            'pname' => $productData->product_title,
				                            'upc' => $productData->upc,
				                            'order_status' => $this->order_status_id,
				                            'sku' => $productData->sku,
				                            'unit_price' => $product->sellprice, //isset($productData->unit_price) ? $productData->unit_price : '',
				                            'no_of_units' => $esu_quantity,
                                            'discount' => $discount,
                                            'discount_amt' => $product->discount_amount,
                                            'discount_type' => $product->discount_type,
				                            'parent_id' => $parent_id,
                                            'star' => $star,
                                            'product_slab_id' => $product_slab_id,
                                            'hsn_code' => $taxInfo['hsn_code'],
                                            'actual_esp'=>$esp
			                        );

                $subtotalwithtax += $_product_total;

                if(isset($product->packs)){

                    
                    foreach ($product->packs as $pack) {
                        
                        $pack_list[] = array(

                                    'gds_order_id' => $this->gds_order_id,
                                    'product_id' => $product->scoitemid,
                                    'pack_id' => $pack->pack_level,
                                    'esu' => $pack->esu,
                                    'esu_qty' => $pack->esu_quantity,
                                    'pack_qty' => $pack->pack_qty,
                                    'star' => $pack->star,
                                    'order_status' => $this->order_status_id
                            );

                    }
                }



	    	}// end for for loop on the product info received from front end
    	   

	    	if(count($gds_products) == 0){
	    		throw new Exception('Gds order products insert array could\'nt be constructed check the error log');
	    	}else{
	    		//var_dump($gds_products);
	    		//echo $this->toSql();
	    		try {
	    			$this->insert($gds_products);

                    if(count($pack_list) > 0){

                        $insertpack = $this->savePackOnProducts($pack_list);
                        if($insertpack instanceof \Exception){
                            
                            throw new Exception($insertpack->getMessage());                        
                        }
                    }

	    			$this->inventory_update_order($gds_products);
                    $this->updateGDSOrderTax($this->gds_order_id); //temporARY
	    		}catch(Exception $e){

	    			return $e;
	    		}
	    		return $gds_products;
	    	}
    	}catch(Exception $e){
			return $e;
    	}

    }

    public function gdsProductsGetTax($order_id,$product_id,$product_total,$discount_before_tax=0){

        //Log::notice("Under Function gdsProductsGetTax: ".date("Y-m-d H:i:s"));
        //Log::info($product_total);
        //Log::info("discount_before_tax in gds products");
        //Log::info($discount_before_tax);
        //echo "\n Under Function gdsProductsGetTax: ".date("Y-m-d H:i:s")."\n";

    	if (isset($this->buyer_state_id)){

                $data['product_id'] = (int) $product_id;
                $data['buyer_state_id'] = (int) $this->buyer_state_id;
                $data['seller_state_id'] = (int) $this->seller_state_id;

                $product_tax = 0;

                $taxInfo = $this->getTaxInfo($data, $order_id);

                if(!$taxInfo['status']){

                    throw new Exception($taxInfo['message']);
                }

                if($taxInfo['status']) {

                    $taxInfo = $taxInfo['message'];
                    $data = array();
                    foreach ($taxInfo as $value) {
                        $taxClass_id = $value['Tax Class ID'];

                        $tax_temp = array(
                                            'product_id' => $product_id, 
                                            'tax_class' => $taxClass_id, 
                                            'tax' => $value['Tax Percentage'],
                                            'gds_order_id' => $order_id,
                                            'SGST' => is_null($value['SGST'])?0.00:$value['SGST'],
                                            'CGST' => is_null($value['CGST'])?0.00:$value['CGST'],
                                            'IGST' => is_null($value['IGST'])?0.00:$value['IGST'],
                                            'UTGST' => is_null($value['UTGST'])?0.00:$value['UTGST']
                                        );
                        $product_tax = $value['Tax Percentage'];
                        $data = $tax_temp;
                        $HSN_Code = $value['HSN_Code'];
                    }
                    // print_r($data);
                    // Log::info($discount_before_tax);
                    if($discount_before_tax==0){
                        $actual_ammount = ($product_total * 100) / (100 + $product_tax);
                        $actual_ammount = round($actual_ammount, 5);
                        $product_tax_value = round(($product_total - $actual_ammount), 5);
                    }else{
                        // this is used when discount is calculated on (total - tax_amount) and in this case the $product_total should sent (total-tax_amount)
                        // Log::info("in discount_before_tax");
                        $actual_ammount = round($product_total, 5);
                        $product_tax_value = round((($product_total * $product_tax)/100), 5);
                    }
                    $this->total_tax += $product_tax_value;

                    /* 
                        Update query done from single hand multiple update not done
                    */
                    $return = array(
                                        'tax' => $product_tax_value,
                                        'price' => $actual_ammount,
                                        'hsn_code' => $HSN_Code
                                );
                    
                    $data['tax_value'] = $product_tax_value; //* ($data['tax'] / $product_tax);
                    // $data['SGST'] = $data['tax_value'] * ($data['SGST']/100);
                    // $data['CGST'] = $data['tax_value'] * ($data['CGST']/100);
                    // $data['IGST'] = $data['tax_value'] * ($data['IGST']/100);
                    // $data['UTGST'] = $data['tax_value'] * ($data['UTGST']/100);


                    DB::table('gds_orders_tax')->insert($data); /* Query Builder */

                } else {

                	$return['tax'] = 0;
        			$return['price'] = 0;
                    $return['hsn_code'] = "";

                }
                print_r($return);
	            return $return;

        }else {

        	throw new Exception("Something is wrong with buyer state!!");
        }
    }

    public function getTaxInfo($data, $orderid) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('APP_TAXAPI'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);        
        curl_close($curl);

        if($response === false) {
            $err = curl_error($curl);
            $args = array("ConsoleClass" => 'taxmail', 'arguments' => array('DmapiTaxTemplate', $orderid, json_encode($data),$err));
            $queue = new Queue();                   
            $token = $queue->enqueue('default', 'ResqueJobRiver', $args);
            $return['status'] = false;
            $return['message'] = "curl to api failed";
            return $return;

        } else {

            $response = json_decode($response, true);
            if ($response) {
                if ($response['Status'] != 200) {
                    if (isset($response['ResponseBody'])) {
                        $args = array("ConsoleClass" => 'taxmail', 'arguments' => array('DmapiTaxTemplate', $orderid, json_encode($data), $response['ResponseBody']));
                    }else{
                        $args = array("ConsoleClass" => 'taxmail', 'arguments' => array('DmapiTaxTemplate', $orderid, json_encode($data), $response['Message']));  
                    }
                    $queue = new Queue();                   
                    $token = $queue->enqueue('default', 'ResqueJobRiver', $args);

                    if(isset($response['ResponseBody'])){

                        $return['status'] = false;
                        $return['message'] = $response['ResponseBody'];
                        return $return;
                    }else{
                        $return['status'] = false;
                        $return['message'] = 'No data from API';
                        return $return;
                    }
                    
                } else {

                    $return['status'] = true;
                    $return['message'] = $response['ResponseBody'];
                    return $return;
                }
            } else {
                
                $args = array("ConsoleClass" => 'taxmail', 'arguments' => array('DmapiTaxTemplate', $orderid, json_encode($data),'No response form tax API'));
                $queue = new Queue();                   
                $token = $queue->enqueue('default', 'ResqueJobRiver', $args);
                $return['status'] = false;
                $return['message'] = 'No data from API';
                return $return;
            }
            
        }
    }

    public function inventory_update_order($products){
    	if(is_array($products) && count($products) > 0){
    		foreach ($products as $product) {
                $invLogs=array();
                    if($this->cust_type==3016){
                        DB::statement("UPDATE inventory SET inventory.dit_order_qty = (inventory.dit_order_qty +".$product['qty'].") WHERE inventory.product_id = ".$product['product_id']." and inventory.le_wh_id = ".$this->wh_le_id);
                    }else{
                        $Inventory = new Inventory();
                        $invInfo = $Inventory->getInventory($product['product_id'], $this->wh_le_id);
                        $prevSOH = isset($invInfo->soh) ? $invInfo->soh : 0;
                        $prevDitOrderQty = isset($invInfo->dit_order_qty) ? $invInfo->dit_order_qty : 0;
                        $prevOrderQty = isset($invInfo->order_qty) ? $invInfo->order_qty : 0;
                        $prevQuarantineQty = isset($invInfo->quarantine_qty) ? $invInfo->quarantine_qty : 0;
                        $prevDndQty = isset($invInfo->dnd_qty) ? $invInfo->dnd_qty : 0;
                        $prevDitQty = isset($invInfo->dit_qty) ? $invInfo->dit_qty : 0;
                        $newOrderQty = $product['qty'];//+$prevSOH;
                        //log::info('gds_order_id'.$product['gds_order_id']);
                        $invLogs[] = array(
                            'le_wh_id' => $this->wh_le_id,
                            'product_id' => $product['product_id'],
                            'soh' => 0,
                            'order_qty' => $newOrderQty,
                            'ref' => 0,
                            'ref_type' => 2,
                            'old_soh' => $prevSOH,
                            'old_order_qty' => $prevOrderQty,
                            'old_dit_order_qty' => $prevDitOrderQty,
                            'old_quarantine_qty' => $prevQuarantineQty,
                            'old_dnd_qty' => $prevDndQty,
                            'old_dit_qty' => $prevDitQty,
                            'comments' => 'OrderQty Added by placing Order  with gds_order_id='.$product['gds_order_id'],
                            'gds_order_id'=>$product['gds_order_id']
                        );
                        if(count($invLogs)) {
                            //log::info('invlogd');
                            $Inventory->addInQueueWithBulk($invLogs);
                        }
                        DB::statement("UPDATE inventory SET inventory.order_qty = (inventory.order_qty +".$product['qty'].") WHERE inventory.product_id = ".$product['product_id']." and inventory.le_wh_id = ".$this->wh_le_id);
                    }                    
    		}
    	}
    }

    public function updateGDSOrderTax($orderId){
        DB::statement("UPDATE gds_orders go, gds_order_products gop, gds_orders_tax got
                       SET `got`.`gds_order_prod_id` = `gop`.`gds_order_prod_id`
                        WHERE go.`gds_order_id` = gop.`gds_order_id`
                        AND `got`.`gds_order_id` = go.`gds_order_id`
                        AND gop.`product_id` = `got`.`product_id`
                        AND `got`.`gds_order_prod_id` IS NULL AND `go`.`gds_order_id`=".$orderId);
        /*
        $query = DB::table('gds_orders_tax')
                ->select('*')->where('gds_order_prod_id',NULL)->get()->all();
        
        foreach ($query as $value) {
            
            
            $products = DB::table('gds_order_products')
                ->select('gds_order_prod_id')
                ->where('gds_order_id',$value->gds_order_id)
                ->where('product_id',$value->product_id)                
                ->get()->all();
            if(count($products) > 0){

                $products = $products[0];
                DB::statement( "UPDATE gds_orders_tax SET gds_orders_tax.gds_order_prod_id = $products->gds_order_prod_id WHERE gds_orders_tax.gds_order_id = $value->gds_order_id AND gds_orders_tax.product_id = $value->product_id");
            }
            

        } */

        return true;
    }

    public function getOrderProducts($orderId){
        $query = DB::table('gds_order_products')
                ->select('product_id','cost','gds_order_prod_id')->where('gds_order_id',$orderId)->get()->all();
        $query = json_decode(json_encode($query), true);

        if(count($query)>0)
            return $query;
        else
            return array();
    }

    public function gdsProductsTaxRectify($order_id,$product_id,$product_total){

        $data['product_id'] = (int) $product_id;
        $data['buyer_state_id'] = 4033;
        $data['seller_state_id'] = 4033;

        $product_tax = 0;
        $taxInfo = $this->getTaxInfo($data, $order_id);
        //var_dump($taxInfo);
        $data = array();
        if ($taxInfo != 'No data from API' && count($taxInfo) > 0) {

            $data = array();
            foreach ($taxInfo as $value) {

                $taxClass_id = $value['Tax Class ID'];                       
                $tax_temp = array('product_id' => $product_id, 'tax_class' => $taxClass_id, 'tax' => $value['Tax Percentage'],'gds_order_id' => $order_id);
                $product_tax += $value['Tax Percentage'];
                $data[] = $tax_temp;
            }

            $actual_ammount = ($product_total * 100) / (100 + $product_tax);
            $actual_ammount = round($actual_ammount, 5);
            $product_tax_value = round(($product_total - $actual_ammount), 5);
            $this->total_tax += $product_tax_value;

            /* 
                Update query done from single hand multiple update not done
            */
            $return = array(
                                'tax' => $product_tax_value,
                                'price' => $actual_ammount
                        );
            $tax_data = array();
            foreach ($data as $value) {
                $temp_tax_data = array();
                $temp_tax_data = $value;
                $percentage = $value['tax'];
                $temp_tax_data['tax_value'] = $product_tax_value * ($percentage / $product_tax);
                $tax_data[] = $temp_tax_data;
            }
            DB::table('gds_orders_tax')->insert($tax_data); /* Query Builder */
        } else {

            $return['tax'] = 0;
            $return['price'] = 0;
        }

        return $return;
    }

    public function updateProductsByRectifyTax($orderId,$productId,$tax,$price){
        echo "UPDATE gds_order_products SET gds_order_products.price = $price,
        gds_order_products.tax = $tax WHERE gds_order_products.gds_order_id = $orderId AND gds_order_products.product_id = $productId <br>";
        DB::statement( "UPDATE gds_order_products SET gds_order_products.price = $price,
        gds_order_products.tax = $tax WHERE gds_order_products.gds_order_id = $orderId AND gds_order_products.product_id = $productId");
    }

    public function updateGDSOrderTaxRectify($orderId,$productId){
        $products = DB::table('gds_order_products')
            ->select('gds_order_prod_id')
            ->where('gds_order_id',$orderId)
            ->where('product_id',$productId)                
            ->get()->all();
        $products = $products[0];
        DB::statement( "UPDATE gds_orders_tax SET gds_orders_tax.gds_order_prod_id = $products->gds_order_prod_id WHERE gds_orders_tax.gds_order_id = $orderId AND gds_orders_tax.product_id = $productId");
    }

    /**
     *  Added new level of pack saving in the shit load system with no use
     */
    public function savePackOnProducts($data){

        try{

            DB::table('gds_order_product_pack')->insert($data);

        }catch(\Exception $e){
            return $e;
        }
    }
}