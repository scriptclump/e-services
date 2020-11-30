<?php namespace App\Central\Repositories;

use DB;

class OrderRepo
{
	public function addOrders()
	{
		//return DB::table('eseal_customer')->get();
	}
	public function getCustomerCartDetails($id,$customer_id,$ima_id)
	{
		 
     $result_customer = DB::table('customer_cart')
                ->select('customer_cart.*','customer_products_plans.*')
                ->join('customer_products_plans','customer_products_plans.customer_product_plan_id','=','customer_cart.product_id');
                
     $result = DB::table('customer_cart')
                ->select('customer_cart.*','eseal_price_master.*')
                ->join('eseal_price_master','eseal_price_master.id','=','customer_cart.product_id');

    if($id==1){
    if(empty($customer_id)){
    $result = $result->where(array('customer_cart.customer_id'=>$customer_id,'customer_cart.ima_id'=>$ima_id,'eseal_price_master.name'=>'IoT'))
                ->get()->all();
        return $result;
	 }
  
   else{
    $result = $result_customer->where(array('customer_cart.customer_id'=>$customer_id,'customer_cart.ima_id'=>$ima_id,'customer_products_plans.name'=>'IoT'))
              ->get()->all();
    return $result;    
   }
  }
  if($id==9){
   if(empty($customer_id)){
    $result = $result->where(array('customer_cart.customer_id'=>$customer_id,'customer_cart.ima_id'=>$ima_id))
                ->whereNotIn('eseal_price_master.name',array('IoT'))
                ->get()->all();
        return $result;
   }
  
   else{
    $result = $result_customer->where(array('customer_cart.customer_id'=>$customer_id,'customer_cart.ima_id'=>$ima_id))
                ->whereNotIn('customer_products_plans.name',array('IoT'))
                ->get()->all();
        return $result;
   } 
  }
  }
	public function getComponentType($id,$customer_id,$status,$temp_cust_id)
	{
	   $comp_id=DB::select('select value from master_lookup where name="AIDC"');
     $comp_id=$comp_id[0]->value;
     $aidc_components=DB::table('eseal_price_master')
                          ->leftJoin('customer_products_plans','customer_products_plans.product_plan_id','=','eseal_price_master.id') 
                          ->where('eseal_price_master.component_type_lookup_id',$comp_id);
                          
	   $iot_components=DB::table('customer_products_plans')
                    ->where('customer_id',$customer_id);

     if(!empty($customer_id)){
	   if(!empty($id)){
     if($id==9){
         $component_types=$aidc_components->where('customer_products_plans.customer_id',$customer_id)
                          ->select('customer_products_plans.*')->get()->all();
         }
         else 
         $component_types = $iot_components->where('product_plan_id',$id)->select('*')->get()->all();
        }
        else{
        if($status==1){
          $component_types = $iot_components->where('product_plan_id',$status)->select('*')->get()->all();
         }
         if($status==0){
          $component_types=$aidc_components->where('customer_products_plans.customer_id',$customer_id)
                          ->select('customer_products_plans.*')->get()->all();
         }
         }
        }
        else{
          if(!empty($id)){
         if($id==9){
         $component_types=$aidc_components->where('customer_products_plans.customer_id',$temp_cust_id)
                          ->select('eseal_price_master.*')->get()->all();
         }
      else{
        $component_types = DB::select('select DISTINCT(master_lookup.name),eseal_price_master.name,eseal_price_master.price,eseal_price_master.id,eseal_price_master.image_url,eseal_price_master.description from master_lookup left join lookup_categories on lookup_categories.id= master_lookup.category_id 
    		inner join eseal_price_master on master_lookup.value=eseal_price_master.component_type_lookup_id
    		where master_lookup.category_id=2 and eseal_price_master.id='.$id);	
         }
          }
         else{
          if($status==1)
          $component_types = DB::select('select * from customer_products_plans where customer_id="'.$temp_cust_id.'" and product_plan_id='.$status);
          if($status==0){
          $component_types=$aidc_components->where('customer_products_plans.customer_id',$temp_cust_id)
                          ->select('eseal_price_master.*')->get()->all();
          }
         }
        }
          
        
        return $component_types;
	}

	public function getProductCost($pid)
	{
    
    $product_cost=DB::select('select epm.*,ml.description from eseal_price_master epm 
    left join master_lookup ml ON ml.value=epm.tax_class_id where epm.id='.$pid);
         
	     return $product_cost;
	}
	public function checkCustId($pid,$customer_id,$ima_id)
	{
		//return $customer_id;
		$check=DB::select('select id from customer_cart where product_id="'.$pid.'" 
			and customer_id="'.$customer_id.'" and ima_id='.$ima_id);
	    return $check;
	}
	public function getCartQuantity($cust_id,$ima_id,$check_id)
  {
		if($check_id==1){
  if(!empty($cust_id)){
    $product_id=DB::select('select customer_product_plan_id from customer_products_plans where customer_id="'.$cust_id.'" and name="IoT"');
    $product_id=$product_id[0]->customer_product_plan_id;
    $cart_qty=DB::select('select count(distinct product_id) as cart_qty from customer_cart where customer_id="'.$cust_id.'" and ima_id="'.$ima_id.'" and product_id='.$product_id);
	 }
   else
   $cart_qty=DB::select('select count(distinct product_id) as cart_qty from customer_cart where customer_id="'.$cust_id.'" and ima_id="'.$ima_id.'" and product_id=1');
   
   }
   if($check_id==9){
  if(!empty($cust_id)){
    $product_id=DB::select('select customer_product_plan_id from customer_products_plans where customer_id="'.$cust_id.'" and name="IoT"');
    $product_id=$product_id[0]->customer_product_plan_id;
    $cart_qty=DB::select('select count(distinct product_id) as cart_qty from customer_cart where customer_id="'.$cust_id.'" and ima_id="'.$ima_id.'" and product_id!='.$product_id);
  }
  else
   $cart_qty=DB::select('select count(distinct product_id) as cart_qty from customer_cart where customer_id="'.$cust_id.'" and ima_id="'.$ima_id.'" and product_id!=1');
   }
  	
    return $cart_qty;
	 
  }
    public function checkOut($cust_id,$ima_id,$id)
    {
    	  $finaldata=DB::table('customer_cart as cc')
                  ->leftJoin('eseal_price_master as epm','cc.product_id','=','epm.id')
                  ->leftJoin('master_lookup as ml','ml.value','=','epm.tax_class_id')
                  ->where('cc.ima_id',$ima_id);
        $finaldata_pid=DB::table('customer_cart as cc')
                      ->leftJoin('customer_products_plans as epm','cc.product_id','=','epm.customer_product_plan_id')
                      ->leftJoin('master_lookup as ml','ml.value','=','epm.tax_class_id')
                      ->where('cc.ima_id',$ima_id);

        if(empty($cust_id))
        {
          if($id==1)
          {         
              $finaldata=$finaldata->where('cc.customer_id',$cust_id)->where('cc.product_id',1)->select('epm.id as pid','epm.*','ml.description as tax','cc.*')->get()->all();
          }
         if($id==9)
         {
            $finaldata=$finaldata->where('cc.customer_id',$cust_id)->where('cc.product_id','!=',1)->select('epm.id as pid','epm.*','ml.description as tax','cc.*')->get()->all();
         }
        }
        else
        {
            $product_id=DB::select('select customer_product_plan_id from customer_products_plans where customer_id="'.$cust_id.'" and name="IoT"');
            $product_id=$product_id[0]->customer_product_plan_id;
            if($id==1)
            {
                $finaldata=$finaldata_pid->where('cc.customer_id',$cust_id)->where('cc.product_id',$product_id)->select('epm.customer_product_plan_id as pid','epm.*','ml.description as tax','cc.*')->get()->all();
            }
            if($id==9)
            {
              $finaldata=$finaldata_pid->where('cc.customer_id',$cust_id)->where('cc.product_id','!=',$product_id)->select('epm.customer_product_plan_id as pid','epm.*','ml.description as tax','cc.*')->get()->all();
            }
        }
        return $finaldata;

    }
    public function editCart($pid,$cust_id)
    {
        if(empty($cust_id))
        $product_cost=DB::select('select epm.price,ml.description from eseal_price_master epm left join master_lookup ml ON ml.value=epm.tax_class_id where epm.id='.$pid);
        else
         $product_cost=DB::select('select epm.*,ml.description from customer_products_plans epm 
         left join master_lookup ml ON ml.value=epm.tax_class_id where epm.customer_product_plan_id='.$pid);
        
        return $product_cost;
    }
    public function getCountries()
    {
    	$countries=DB::select('select country_id,name from countries');
        return $countries;
    }
   public function getZones()
   {
   		$zones=DB::select('select zone_id,name from zone');
   		return $zones; 
   }
   public function mapping($id)
   {
     $mapping=DB::select('select  master_lookup.* from eseal_price_master inner join master_lookup on master_lookup.value=eseal_price_master.component_type_lookup_id
   where eseal_price_master.id='.$id);
     return$mapping;   
  }
    public function getProductCostcust($pid,$cid)
    {
         $product_cost=DB::select('select epm.*,ml.description from customer_products_plans epm 
            left join master_lookup ml ON ml.value=epm.tax_class_id where epm.customer_product_plan_id='.$pid.' and customer_id='.$cid);

         return $product_cost;
    }
    
    public function orderStatus()
    {
        return DB::select('SELECT ml.name, ml.value FROM lookup_categories as ls , master_lookup as ml where ls.id = ml.category_id and ls.name ="Order Status"');
    }
    
    public function getOrders($data=array())
    {
        if(empty($data))
        {
            //$order_status = '17006';
            $filter_type = '';
        }else{
            if(isset($data['order_status_id']) && !empty($data['order_status_id']))
            {
                $order_status = $data['order_status_id'];
            }else{
                $order_status = '';
            }
            if(isset($data['customer_id']) && !empty($data['customer_id']))
            {
               $customer_id = $data['customer_id'];
            }else{
               $customer_id = '';
            }
            if(isset($data['from_date']) && !empty($data['from_date']))
            {
               $from_date = $data['from_date'];
            }else{
                $from_date = '';
            }
            
            if(isset($data['to_date']) && !empty($data['to_date']))
            {
               $to_date = $data['to_date'];
            }else{
                $to_date = '';
            }
            if(isset($data['filter_type']) && $data['filter_type'])
            {
                $filter_type = $data['filter_type'];
            }else{
                 $filter_type = '';
            }    
        }
        $where = '';
        //$results = DB::table('eseal_orders')->select('count(*) AS total','date_added');
        if(!empty($order_status)){
            //$results = $results->where('order_status_id',$order_status);
            $where .= (empty($where)) ? 'order_status_id = '.$order_status : ' and order_status_id = '.$order_status;
        }
        
        if(!empty($customer_id)){
            //$results = $results->where('customer_id',$customer_id);
            $where .= (empty($where)) ? 'customer_id = '.$customer_id : ' and customer_id = '.$customer_id;
        }
        if(!empty($from_date)){
            //$results = $results->where('data_added', '>=' ,$from_date);
            $where .= (empty($where)) ? "DATE(date_added) >= '".date('Y-m-d',strtotime($from_date))."'" : " and DATE(date_added) >= '".date('Y-m-d',strtotime($from_date))."'"; 
            
        }
        if(!empty($to_date)){
            //$results = $results->where('data_added', '<=' ,$to_date);
            $where .= (empty($where)) ? " DATE(date_added) <= '".date('Y-m-d',strtotime($to_date))."'" : " and DATE(date_added) <= '".date('Y-m-d',strtotime($to_date))."'";  
        }
        if(!empty($where)){
            $where = ' WHERE '.$where;
        }
        
        if($filter_type == 'MONTH'){
            $groupBy = "group by MONTH(date_added)";
            //$coulmn = 'count(order_id) as total, MONTH(date_added) as date, YEAR(date_added) as yeardate';
        }elseif($filter_type == 'YEAR')
        {
            $groupBy = "group by YEAR(date_added)";
            //$coulmn = 'count(order_id) as total, YEAR(date_added) as date';
        }else{
            $groupBy = "group by date_added";
            //$coulmn = 'count(order_id) as total, date_added as date';
        }    
        $coulmn = 'count(order_id) as total, date_added as date';
        //echo 'SELECT '.$coulmn.' FROM eseal_orders '.$where.' '.$groupBy.' order by date_added'; die;
        $result = DB::select('SELECT '.$coulmn.' FROM eseal_orders '.$where.' '.$groupBy.' order by date_added');
       //$results->groupby('order_status_id');
        //$results = $results->get()->all();

        return $result;
    }
    
    public function sendOrderEmail($orderId) {
        try {
//            \Log::info(__METHOD__);
            $sendMailStatus = DB::table('gds_order_email_status')
                    ->where('gds_order_id', $orderId)
                    ->first(['gds_order_email_id']);
    //        \Log::info('sendMailStatus');
            $response = [];
            if ($orderId > 0) {
                $response = $this->getOrderCompleteDetails($orderId);
            }
            $subject = 'New Order';
            $channel = $this->getOrderChannel($orderId);
            if (!empty($channel)) {
                $channel = property_exists($channel, 'channnel_name') ? $channel->channnel_name : '';
                if ($channel != '') {
                    $subject = $subject . ' For ' . $channel;
                }
            }
            $subject = $subject . ' Order Id ' . $orderId;
            $imageUrl = url() . '/img/ebutor-logo.png';
            $response['image'] = (object) (['imageUrl' => $imageUrl]);
           // \Log::info($response);
            try {
                if(empty($sendMailStatus))
                {
                    $emailDetails['gds_order_id'] = $orderId;
                    $emailDetails['from_email'] = 'sandeep.jeedula@ebutor.com';
                    $emailDetails['to_email'] = 'sandeep.jeedula@ebutor.com';
                    $emailDetails['cc_email'] = 'sandeep.jeedula@ebutor.com';
                    $emailDetails['email_subject'] = $subject;
                    $emailDetails['email_template'] = 'emails.ordersemails';
                    $emailDetails['email_status'] = 1;
                    $emailDetails['email_data'] = json_encode($response);
                    @\Mail::send(['html' => 'emails.ordersemails'], $response, function($message) use ($response, $subject) {
                        $message->to('prasenjit.chowdhury@ebutor.com')->subject($subject);
                    });
                    DB::table('gds_order_email_status')->insert($emailDetails);
                }
            } catch (Exception $ex) {
                \Log::info($ex->getMessage());
                \Log::info($ex->getTraceAsString());
            }
            return $response;
        } catch (ErrorException $ex) {
            \Log::info($ex->getMessage());
            \Log::info($ex->getTraceAsString());
        }
    }

    public function getOrderCompleteDetails($orderId) {
        try {
            $response = array();
            $ebutorAddress = DB::table('ebutor_seller')
                    ->leftJoin('countries as co', 'co.country_id', '=', 'ebutor_seller.seller_country')
                    ->leftJoin('zone as zn', 'zn.zone_id', '=', 'ebutor_seller.seller_state')
                    ->where('seller_id', '=', '1')
                    ->select('ebutor_seller.*', 'co.name as seller_country', 'zn.name as seller_state')
                    ->get()->all();
            $response['ebutor_address'] = $ebutorAddress;
            $orderDetails = DB::table('gds_orders as go')
                    ->leftJoin('gds_order_products as gopr', 'go.gds_order_id', '=', 'gopr.gds_order_id')
                    ->leftJoin('gds_orders_payment as gop', 'gop.gds_order_id', '=', 'go.gds_order_id')
                    ->leftJoin('gds_orders_addresses as gad', 'gad.gds_order_id', '=', 'go.gds_order_id')
                    ->leftJoin('mp as ch', 'ch.mp_id', '=', 'go.mp_id')
                    ->leftJoin('gds_cust_address as gca', 'gca.gds_cust_id', '=', 'go.gds_cust_id')
                    //->leftJoin('gds_invoice_grid as gig','gig.gds_order_invoice_id','=','go.gds_order_id')
                    //->where('gad.address_type',"billing")
                    ->select('go.*', 'go.gds_order_id as gdsorderid', 'ch.*', 'gopr.*', 'gop.*', 'gad.*', 'gca.*')
                    ->where('go.gds_order_id', $orderId)
                    ->get()->all();
            
            $response['order_details'] = $orderDetails;            
            $address = DB::table('gds_orders_addresses')
                    ->leftJoin('gds_orders', 'gds_orders.gds_order_id', '=', 'gds_orders_addresses.gds_order_id')
                    ->leftJoin('zone', 'zone.zone_id', '=', 'gds_orders_addresses.state_id')
                    ->leftJoin('countries', 'countries.country_id', '=', 'gds_orders_addresses.country_id')
                    ->where('gds_orders.gds_order_id', $orderId)
                    ->select('gds_orders_addresses.*', 'countries.name as country', 'zone.name as state')
                    ->get()->all();
            $response['address'] = $address;

            $tax_price_cal = DB::table('gds_order_products as gop')
                    ->leftJoin('products', 'products.product_id', '=', 'gop.pid')
                    ->select(DB::raw('sum(price) as tax_price,tax'))
                    ->where('gds_order_id', $orderDetails[0]->gdsorderid)
                    ->groupBy('tax')
                    ->get()->all();
            $response['tax_price_cal'] = $tax_price_cal;

            $order_product_details = DB::table('gds_order_products as gop')
                    ->leftJoin('products', 'products.product_id', '=', 'gop.pid')
                    /* ->leftJoin('channel_product_add_update as cpau','cpau.channel_product_key','=','cod.channel_item_id') */
                    ->where('gds_order_id', $orderDetails[0]->gdsorderid)
                    ->get()->all();
            $response['order_product_details'] = $order_product_details;

            $product_array = array();
            $final_product_array = [];
            $pan_array = '';
            $sno = 1;
            foreach ($order_product_details as $details) {
                $product_details = DB::table('products')
                        ->where('product_id', $details->pid)
                        ->get()->all();
                $pan_array.= $details->product_id . ',';
                $product_array['sno'] = $sno;
                $product_array['product_name'] = isset($product_details[0]->name) ? $product_details[0]->name : '';
                $product_array['sku'] = isset($product_details[0]->sku) ? $product_details[0]->sku : '';
                $product_array['quantity'] = $details->qty;
                $product_array['discount_price'] = $details->discount;
                $product_array['mrp'] = isset($details->unit_price) ? $details->unit_price : $details->price;
                $product_array['price'] = $details->price;
                $product_array['subtotal'] = $details->price * $details->qty;
                $product_array['tax'] = $details->tax;
                $product_array['total'] = $product_array['subtotal'] + $product_array['tax'] + $orderDetails[0]->ship_total;
                $final_product_array[] = $product_array;
                $sno++;
            }
            $response['final_product_array'] = $final_product_array;

            $pan_data = rtrim($pan_array, ',');
            $panDetails = DB::table('legal_entities as ec')
                    ->Join('products as p', 'p.legal_entity_id', '=', 'ec.customer_id')
                    ->whereIn('p.product_id', array($pan_data))
                    ->groupBy('p.manufacturer_id')
                    ->get()->all();
            $response['pan_details'] = $panDetails;
            return $response;
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    
    public function getOrderChannel($orderId)
    {
        try
        {
            if($orderId > 0)
            {
                return DB::table('gds_orders')
                        ->join('channel', 'channel.channel_id', '=', 'gds_orders.channel_id')
                        ->where('gds_orders.gds_order_id', $orderId)
                        ->first(['channel.channnel_name']);
            }
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
}
?>
