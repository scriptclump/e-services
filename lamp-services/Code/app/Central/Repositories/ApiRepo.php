<?php 
namespace App\Central\Repositories;

use Token;
use User;
use DB;  
use Session;
use \Log;


Class ApiRepo{

    public function getXml($json_data,$apiname,$auth_token,$input_type,$product_attributes=''){
        
        $product_attributes=json_decode($product_attributes);
        $dispute_size = sizeof($json_data);

        if($input_type=='json' || $input_type=='xml'){
        if($input_type=='json')
        {
        $apirequest=$apiname.'Request';
        $xml_input='<?xml version=\'1.0\' encoding=\'utf-8\'?><'.$apirequest.' xmlns="urn:ebay:apis:eBLBaseComponents"></'.$apirequest.'>';
        $xml_user_info = new \SimpleXMLElement($xml_input);
        $test=$this->array_to_xml($json_data,$xml_user_info);
        $json_data = $xml_user_info->asXML();
       // print_r($json_data);exit;
        }
        
          
        $doc = new \DOMDocument();
        $doc->loadXML($json_data);
        $fragment = $doc->createDocumentFragment();
        $fragment->appendXML("<RequesterCredentials>
            <eBayAuthToken>".$auth_token."</eBayAuthToken>
        </RequesterCredentials>
          ");

        $doc->documentElement->appendChild($fragment);
        $str = $doc->saveXML($doc->documentElement);
        $test_input='<?xml version=\'1.0\' encoding=\'utf-8\'?>';
        
        $dom = new \DOMDocument();
        
        $dom->loadXML($str);
        
        $str = $dom->saveXML($dom->documentElement);
        
        $final_xml=$test_input.$str;
        
        if($apiname=="AddDispute"){
        
        for($y=0;$y<=$dispute_size;$y++)
            {
             
            $item = '<item'.$y.'>';
            $itemend= '</item'.$y.'>';
            $final_xml = str_replace($item,' ',$final_xml);
            $final_xml = str_replace($itemend,' ',$final_xml);
            
            }
          
        }
         return $final_xml;
        }
       
    }
       
       
        

    public function sendRequest($url,$xml_data,$api_name,$product_id='',$item_id='',$json_data='',$status='',$order_id='',$DisputeExplanation='',$seller_id='',$headers=''){
              
              
              if(empty($headers)){
              
              $headers = $this->getHeaders($api_name,$product_id,$seller_id);
              
              }
              
              
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //Set curl to return the data instead of printing it to the browser.
                curl_setopt($ch, CURLOPT_TIMEOUT,1200);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,600); # timeout after 100 seconds, you can increase it
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "xmlRequest=" . $xml_data);
                curl_setopt($ch, CURLOPT_URL, $url);
                
                $result = curl_exec($ch);
                curl_close($ch);
                 
                //Log::info($result);

                return $result;

    }
   
    public function getHeaders($api_name,$product_id='',$ebay_seller_id=''){
              
              $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id');
              
              if(!empty($product_id))
              $ebay_seller_id  = DB::table('product_seller_mapping')->where('product_id',$product_id)->pluck('seller_id');
              
              $dev_token = DB::table('channel_configuration')
                           ->where(array('Key_name'=>'dev_token','channel_id'=>$eBay_channel_id,'seller_id'=>$ebay_seller_id))
                           ->pluck('Key_value');

              $app_token = DB::table('channel_configuration')
                           ->where(array('Key_name'=>'app_token','channel_id'=>$eBay_channel_id,'seller_id'=>$ebay_seller_id))
                           ->pluck('Key_value');

              $cert_token = DB::table('channel_configuration')
                           ->where(array('Key_name'=>'cert_token','channel_id'=>$eBay_channel_id,'seller_id'=>$ebay_seller_id))
                           ->pluck('Key_value');                      
              
             if($api_name == 'AddDispute'){
              $site_id=0;
              }
              else{
               $site_id=203; 
              }

              
              $headers=array('X-EBAY-API-COMPATIBILITY-LEVEL:967',
              'X-EBAY-API-DEV-NAME:'.$dev_token,
              'X-EBAY-API-APP-NAME:'.$app_token,
              'X-EBAY-API-CERT-NAME:'.$cert_token,
              'X-EBAY-API-SITEID:'.$site_id,
              'X-EBAY-API-CALL-NAME:'.$api_name,);
            
           //return 'here';

          return $headers;
    }
   
    public function array_to_xml($array, $xml_user_info) {
     
        foreach($array as $key => $value) {
     
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml_user_info->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                }else{
                    $subnode = $xml_user_info->addChild("item$key");
                    $this->array_to_xml($value, $subnode);
                }
            }else {
                $xml_user_info->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }

   
     public function getXmlArray($quantity,$category_id='',$upc,$address){
      
      $paypal_account = 'venkatmyd@gmail.com';
      
      $upc_number = 'NA';
      
      //print_r($upc_number);exit;
     
      $new_product_category = '1000';
      $PostalCode = '500039';
      $status_update = 'false'; 
      $shipping_cost = '0.00';

      $total_address = $address[0]->warehouse_name.",".$address[0]->city.",".$address[0]->state;
      $xml_array=array();
      
      
      $xml_array['PrimaryCategory']['CategoryID'] = $category_id;
      $xml_array['ConditionID']=$new_product_category;
      $xml_array['Country']="IN";
      $xml_array['Currency']="INR";
      $xml_array['DispatchTimeMax']="3";
      $xml_array['ListingDuration']="Days_30";
      $xml_array['ListingType']="FixedPriceItem";
      $xml_array['ItemSpecifics']="";
      //$xml_array['PaymentMethods']='CreditCard';
      //$xml_array['PaymentMethods']='DirectDebit';
      $xml_array['PaymentMethods']='PaisaPayEscrow';
      //$xml_array['PaymentMethods']='PaisaPayEscrow';
      //$xml_array['PaymentMethods']='PaisaPayEscrowEMI';
     // $xml_array['PaymentMethods']='COD';
      $xml_array['Location'] = $total_address; 
      $xml_array['PayPalEmailAddress']= $paypal_account;
      $xml_array['PostalCode']= $PostalCode;
      $xml_array['ProductListingDetails']['UPC']=$upc_number;
      //$xml_array['ProductListingDetails']['IncludeStockPhotoURL']="true";
      //$xml_arrfay['ProductListingDetails']['IncludePrefilledItemInformation']=$status_update;
      //$xml_array['ProductListingDetails']['UseFirstProduct']=$status_update;
      //$xml_array['ProductListingDetails']['UseStockPhotoURLAsGallery']=$status_update;
      //$xml_array['ProductListingDetails']['ReturnSearchResultOnDuplicates']=$status_update;
      $xml_array['Quantity']=$quantity;
      $xml_array['ReturnPolicy']['ReturnsAcceptedOption']="ReturnsAccepted";
      //$xml_array['ReturnPolicy']['RefundOption']="MoneyBack";
      $xml_array['ReturnPolicy']['ReturnsWithinOption']="Days_30";
      //$xml_array['ReturnPolicy']['ShippingCostPaidByOption']="Buyer";
      $xml_array['ShippingDetails']['ShippingType']="Flat";
      $xml_array['ShippingDetails']['ShippingServiceOptions']['ShippingService']="IN_Courier";
      $xml_array['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']="0.00";
      //$xml_array['ShippingDetails']['ShippingServiceOptions']['ShippingService']="USPSPriorityMailInternational";
      //$xml_array['ShippingDetails']['ShippingServiceOptions']['FreeShipping']="true";
      $xml_array['ShippingDetails']['ShippingServiceOptions']['ShippingServiceAdditionalCost']=$shipping_cost;
      $xml_array['ShipToLocations']="IN";
      $xml_array['Site']="India";
      
      return $xml_array;
     
     }
     
    

   
    public function getUpdatedOrder($channel_id)
    {
      
        //$eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id'); 
        
        return DB::table('channel_orders as Co')
              ->leftJoin('channel_order_item_details as Cod','Cod.order_id','=','Co.order_id')
              ->leftJoin('channel_order_payment as Cop','Cop.order_id','=','Cod.order_id')
              ->leftJoin('channel_order_shipping_details as Cosd','Cosd.order_id','=','Cop.order_id')
              ->leftJoin('channel_orders_address as Cosa','Cosa.order_id','=','Cosd.order_id')
              ->leftJoin('channel as ch','ch.channel_id','=','Co.channel_id')
              ->where('Cod.order_status',1)
              ->where('ch.channel_id',$channel_id)
              ->select('Cosa.*','Cod.channel_item_id as ItemID','Cop.payment_status as OrderStatus','Co.payment_method as PaymentMethodUsed','Cop.amount','Cosd.service_name','Cod.transaction_id','Co.channel_order_id')
              ->get()->all();
        
    }

    public function completeorderdetails()
    {
    /* $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id'); 

     return DB::table('channel_order_item_details as coid')
            ->leftJoin('channel_orders as co','co.order_id','=','coid.order_id')
            ->where(array('coid.order_status'=>1))
            ->select('co.channel_order_id','coid.channel_item_id')->get()->all();*/
    
    }
    
           
    public function getPaymentZoneDetails($postcode){
      
      $state_name= DB::table('cities_pincodes')
                                        ->where('PinCode',$postcode)
                                        ->pluck('State');
                   
      $payment_state_name = ucwords(strtolower($state_name));
                   
      $zones = DB::table('zone')
                       ->where('name',$state_name)->first();
      
      return $zones;
    
    }

  
    public function getUrl($product_id=" ",$seller_id=" "){
                
                $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id');

                $ebay_seller_id  = DB::table('product_seller_mapping')->where('product_id',$product_id)->pluck('seller_id');
                

                if($product_id != " "){
                 
                $url = DB::table('channel_configuration as conf')
                           ->leftJoin('channel as ch','conf.channel_id','=','ch.channel_id')
                           ->leftJoin('product_seller_mapping as psm','psm.seller_id','=','conf.seller_id')
                           ->select ('conf.Key_value','ch.channel_url')
                           ->where(array('conf.Key_name'=>'auth_token','conf.channel_id'=>$eBay_channel_id,'conf.seller_id'=>$ebay_seller_id,'psm.product_id'=>$product_id))
                           ->first();
                 }
                else{
                  
                  $url = DB::table('channel_configuration as conf')
                         ->leftJoin('channel as ch','conf.channel_id','=','ch.channel_id')
                         ->where(array('conf.Key_name'=>'auth_token','conf.channel_id'=>$eBay_channel_id,'seller_id'=>$seller_id))   
                         ->first();

                 }
                return $url;
              
   }

   public function sellerIds(){
      
      $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id');
      
    return          DB::table('ebutor_seller as es')
                   ->leftjoin('channel_configuration as cc','cc.seller_id','=','es.seller_id')
                   ->where('cc.channel_id',$eBay_channel_id)
                   ->groupBy('cc.seller_id')
                   ->select('cc.seller_id')->get()->all();

   }

   public function getToken(){

               $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id');

               return DB::table('channel_configuration')->where(array('channel_id'=>$eBay_channel_id,'Key_name'=>'auth_token'))->first();
   }

   public function getDMAccess(){

          $channel_id=DB::table('channel')->select('channel_id')->where('channnel_name','dmapi')->first();
 
          $api_key=DB::table('channel_configuration as cf')
              ->leftjoin('channel as c','c.channel_id','=','cf.channel_id')
              ->where(array('cf.channel_id'=>$channel_id->channel_id,'Key_name'=>'api_key'))
              ->first();

          $secret_key=DB::table('channel_configuration as cf')
              ->leftjoin('channel as c','c.channel_id','=','cf.channel_id')
              ->where(array('cf.channel_id'=>$channel_id->channel_id,'Key_name'=>'secret_key'))
              ->first();
                        
          $url=array('api_key'=>$api_key,'secret_key'=>$secret_key);
                        
           return $url;
   }

   public function gdsEnabled(){

        $finished_product=DB::table('master_lookup')->where('name','Finished Product')->first();
      
          return DB::table('products as pd')
                 ->leftJoin('channel_product as cp','cp.product_id','=','pd.product_id')
                 ->leftJoin('channel_categories as cc','cc.ebutor_category_id','=','pd.category_id')
                 ->where(array('pd.is_gds_enabled'=>1,'pd.product_type_id'=>$finished_product->value,'pd.content_approved'=>1))
                 ->select('cp.channel_id','pd.product_id','pd.is_channel_updated','cp.status','cc.ebutor_category_id','pd.is_deleted')->get()->all();
      
   
   }

  public function channelInsert($gds_enabled){
       
       $result = [];
       
       foreach($gds_enabled as $key=>$value){
      
      $product_exists = DB::table('channel_product_add_update as cpu')
                         ->where(array('cpu.product_id'=>$value->product_id,'cpu.channel_id'=>$value->channel_id))
                         ->get()->all(); 
       
       //print_r($product_exists);exit;
       /*if($value->product_id==5337){
        print_r($value);exit;
       }*/
      

        if($value->is_channel_updated==1){

           DB::table('channel_product_add_update')
          ->where('product_id', $value->product_id)
          ->update(array('is_update'=> 1,
                   ));
         }
        
        if($value->is_deleted==1){

           DB::table('channel_product_add_update')
          ->where('product_id', $value->product_id)
          ->update(array('is_deleted'=> 1,
                   ));
         }
         
         $result['channel_id'] = $value->channel_id;
         $result['product_id'] = $value->product_id;
         $result['message'] = "Successfully inserted into gdsDatabase";
    }

    //echo "<pre>"; print_r($result); die();
    return $result;
   
   }
   
   public function getGDSorderData($data)
    {
        try
        {
            $responseData = array();
            $locationId = isset($data['location_id']) ? $data['location_id'] : 0;
            $manufacturerId = isset($data['manufacturer_id']) ? $data['manufacturer_id'] : 0;
            $myDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . "-3 month"));
            $fromDate = isset($data['from_date']) ? (($data['from_date'] != '') ? $data['from_date'] : $myDate) : $myDate;
            $toDate = isset($data['to_date']) ? (($data['to_date'] != '') ? $data['to_date'] : date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
            $order_status = isset($data['order_status']) ? (($data['order_status'] != '') ? [$data['order_status']] : [17002, 17003, 17004, 17005, 17006, 17007, 17008, 17009]) : [17002, 17003, 17004, 17005, 17006, 17007, 17008, 17009];
            $channelId = isset($data['channel_id']) ? (($data['channel_id'] != '') ? $data['channel_id'] : $this->getAllchannelIds()) : $this->getAllchannelIds();
            $logoPath = \URL::to('/');
            if ($locationId)
            {
                $orderDetails = DB::table('gds_orders')
                    ->join('gds_customer as cust', 'cust.gds_cust_id', '=', 'gds_orders.gds_cust_id')
                    ->join('gds_order_products as prds', 'prds.gds_order_id', '=', 'gds_orders.gds_order_id')
                    ->leftJoin('product_locations as loc', 'loc.product_id', '=', 'prds.pid')
                    // ->leftJoin('gds_orders_addresses as addrs', 'addrs.gds_order_id', '=', 'gds_orders.gds_order_id')
                    ->join('locations', 'locations.manufacturer_id', '=', DB::raw(-1))
                    ->join('products', 'products.product_id', '=', 'prds.pid')
//                    ->join(DB::raw('locations on locations.manufacturer_id = -1'))
                    ->join('channel_orders as orders','orders.order_id','=','gds_orders.channel_order_id')
                    ->join('channel as chn','chn.channel_id','=','gds_orders.channel_id')
                    ->join('channel_order_payment as pay','pay.order_id','=','orders.order_id')
                    ->join('channel_orders_address as ship','ship.order_id','=','orders.order_id')
                    ->join('master_lookup as lookup','lookup.value','=','gds_orders.order_status_id')
//                        ->where('gds_orders.order_date', '>', $fromDate)
                    ->whereBetween('gds_orders.order_date', [$fromDate, $toDate])
                    ->whereNotNull('prds.pid')
                    ->where('loc.location_id', $locationId)
                    ->whereIn('chn.channel_id', explode(',', $channelId))
                    ->whereIn('gds_orders.order_status_id', $order_status)
                    ->select('gds_orders.gds_order_id', 'gds_orders.gds_cust_id', 
                            'gds_orders.channel_id', 'gds_orders.channel_order_id', 
                            'gds_orders.order_date', 'cust.email_address', 'chn.channnel_name',
                            'chn.channel_url', DB::raw("CONCAT('".$logoPath."', chn.channel_logo) as channel_logo"), 
                            'lookup.name as order_status', 
                            'orders.payment_method',
                            'pay.payment_currency', 
                            DB::raw("concat(cust.firstname, ' ',cust.lastname) as customer_name"),
//'prds.pid', 'prds.pname', 'prds.qty', 'prds.price',
                            DB::raw("IFNULL(CONCAT(locations.firstname , ' ',locations.lastname), '') as name"), 
                            DB::raw("IFNULL(gds_orders.erp_order_id, '') as erp_order_id"), 
                            DB::raw("IFNULL(pay.payment_status, '') as payment_status"), 
                            DB::raw("IFNULL(locations.location_id, '') as location_id"), 
                            DB::raw("IFNULL(locations.location_name, '') as company"), 
                            DB::raw("IFNULL(locations.location_address, '') as addr1"), 
                            DB::raw("IFNULL(locations.location_details, '') as addr2"), 
                            DB::raw("IFNULL(locations.city, '') as city"), 
                            DB::raw("IFNULL(locations.pincode, '') as postcode"), 
                            DB::raw("IFNULL(locations.state, '') as state_id"), 
                            DB::raw("IFNULL(locations.country, '') as country_id"), 
                            DB::raw("IFNULL(locations.phone_no, '') as telephone"), 
                            DB::raw("IFNULL(locations.phone_no, '') as mobile"),
                            DB::raw("IFNULL(ship.address1, '') as address1"),
                            DB::raw("IFNULL(ship.address2, '') as address2"),
                            DB::raw("IFNULL(ship.city, '') as city"),
                            DB::raw("IFNULL(ship.state, '') as state"),
                            DB::raw("IFNULL(ship.country, '') as country"),
                            DB::raw("IFNULL(ship.pincode, '') as pincode"))
                        ->groupBy('gds_orders.gds_order_id')
                    ->get()->all();
//                $last = DB::getQueryLog();
//                echo "<pre>";print_R(end($last));die;
//                echo "<pre>";print_R($orderDetails);die;
                if (!empty($orderDetails))
                {
                    $esealTable = 'eseal_' . $manufacturerId;
                    foreach ($orderDetails as $orders)
                    {
                        $temp = array();
                        $temp['order_data'] = $orders;
                        $productDetails = DB::table('gds_orders')
                                ->join('gds_order_products as prds', 'prds.gds_order_id', '=', 'gds_orders.gds_order_id')
                                ->leftJoin('products', 'products.product_id', '=', 'prds.pid')
                                ->join('product_inventory', 'product_inventory.product_id', '=', 'products.product_id')
                                ->where(array(
                                    'gds_orders.gds_order_id' => $orders->gds_order_id
//                                    ,'product_inventory.location_id' => $locationId                                 
                                    ))
                                ->select(DB::raw("IFNULL(prds.pid, '') as product_id"), 
                                        DB::raw("IFNULL(prds.pname, '') as product_name"), 
                                        DB::raw("IFNULL(products.material_code, '') as material_code"), 
                                        DB::raw("IFNULL(prds.price, '') as price"),
                                        DB::raw("IFNULL(prds.qty, '') as order_quantity"), 
                                        'products.is_supply_chain_enabled',
                                        DB::raw('product_inventory.available_inventory as available_quantity'))
                                ->get()->all();
                        $temp['product_data'] = $productDetails;
                        $responseData['order_details'][] = $temp;
                    }
                }
                $counts = 0;
                $statusCountData = DB::table('gds_orders as orders')
                        ->join('gds_order_products AS prod', 'prod.gds_order_id', '=', 'orders.gds_order_id')
                        ->join('product_locations as loc', 'loc.product_id', '=', 'prod.pid')
                        ->join('master_lookup as lookup', 'lookup.value', '=', 'orders.order_status_id')
                        ->where(['loc.location_id' => $locationId])
                        ->whereNotNull('orders.order_status_id')
                        ->where('orders.order_status_id', '>', 0)
                        ->whereBetween('orders.order_date', [$fromDate, $toDate])
                        ->select(DB::raw('count(*) as count, orders.order_status_id, lookup.name'))
                        ->groupBy('orders.order_status_id')->get()->all();
                $statusData = [];
                $fetchOrderProcess = DB::table('master_lookup')
                        ->where(['category_id' => 17])
                        ->where('value', '>', 17001)
                        ->get(['name'])->all();
                foreach($fetchOrderProcess as $processes)
                {
                    $statusData[$processes->name] = 0;
                }
                if(!empty($statusCountData))
                {
                    foreach($statusCountData as $status)
                    {
                        $statusData[$status->name] = $status->count;
                    }
                }
                if(!empty($statusData))
                {
                    $responseData['status_reports'] = $statusData;
                }
            } else
            {
                return 'No location';
            }
            return $responseData;
        } catch (ErrorException $ex)
        {            
            \Log::info($ex->getTraceAsString());
            return $ex->getMessage();
            return $ex->getTraceAsString();
        }
    }
    
    public function getInvoiceDetails($data)
    {
        try
        {
            $responseData = array();
            $orderId = isset($data['order_id']) ? $data['order_id'] : 0;
            $esealImageLink = 'http://ebutor.com/images/ebutor-logo-white.png';
            $constactUs = 'Block I Floor IV, Sai Pragathi, NSL Sez Arena, Uppal - Ramanthapur Rd, IDA Uppal, Uppal, Hyderabad, Telangana 500039';
            $taxInvoiceNumber = '123123123';
            $vatTin = '123123213';
            $serviceTaxNumber = '12312321';
            $cstNumber = '213123123213';
            if ($orderId)
            {
                $invoiceDetails = DB::table('gds_order_invoice')
//                    ->leftJoin('gds_order as orders', 'orders.gds_order_id', '=', 'gds_order_invoice.gds_order_invoice_id')
                    ->leftJoin('gds_invoice_items as items', 'items.gds_order_invoice_id', '=', 'gds_order_invoice.gds_order_invoice_id')
//                    ->leftJoin('gds_orders_addresses as address', 'address.gds_order_id', '=', 'gds_order_invoice.order_id')
                    ->where('gds_order_invoice.order_id', $orderId)
//                    ->where('address.address_type', 'shipping')
                    ->select(DB::raw("'".$esealImageLink."' as eseal_image_link"),
                            DB::raw("'".$constactUs."' as contact_us"),
                            DB::raw($taxInvoiceNumber." as tax_invoice_number"),
                            DB::raw($vatTin." as vat_tin"),
                            DB::raw($serviceTaxNumber." as service_tax_number"),
                            DB::raw($cstNumber." as cst_number"),
                            'gds_order_invoice.order_id', 'gds_order_invoice.gds_order_invoice_id',
                            'gds_order_invoice.grand_total', 'gds_order_invoice.shipping_amount',
                            'gds_order_invoice.total_qty', 'gds_order_invoice.discount_amount',
                            'gds_order_invoice.currency_code', 'gds_order_invoice.order_currency_code',
                            'gds_order_invoice.tax_amount', 
                            DB::raw('gds_order_invoice.created_at as invoice_date')
                            )
                    ->get()->all();
                $addressDetails = DB::table('gds_orders_addresses')
                        ->join('gds_order_invoice', 'gds_order_invoice.order_id', '=', 'gds_orders_addresses.gds_order_id')
                        ->where('gds_order_invoice.order_id', $orderId)
                        ->select(DB::raw('gds_orders_addresses.*'))
                        ->get()->all();
                $responseData['address_details'] = $addressDetails;
//                $last = DB::getQueryLog();
//                echo "<pre>";print_R(end($last));die;
                if (!empty($invoiceDetails))
                {
                    foreach ($invoiceDetails as $invoice)
                    {
                        $temp = array();
                        $responseData['invoice_data'] = $invoice;
//                        echo "<pre>";print_R($invoice);die;
                        $productDetails = DB::table('gds_invoice_items')
                                ->join('gds_order_invoice as invoice', 'invoice.gds_order_invoice_id', '=', 'gds_invoice_items.gds_order_invoice_id')
                                ->join('products', 'products.product_id', '=', 'gds_invoice_items.product_id')
                                ->join('prod_text_det as text', 'text.product_id', '=', 'products.product_id')
                                ->where('invoice.order_id', $orderId)
                                ->select('products.product_id', 'products.sku', 'products.name as product_name', 
                                        'products.material_code', 'gds_invoice_items.qty', 
                                        'gds_invoice_items.tax_amount', 'gds_invoice_items.row_total', 'gds_invoice_items.discount_amount',
                                        'gds_invoice_items.price', 
                                        DB::raw("IFNULL(text.warranty_policy, '') as warranty_policy"), 
                                        DB::raw("IFNULL(text.ship_policy, '') as ship_policy"), 
                                        DB::raw("IFNULL(text.return_policy, '') as return_policy"))
                                ->get()->all();
                        $responseData['product_data'] = $productDetails;
//                        $responseData = $temp;
                    }
                }
            } else
            {
                return 'No location';
            }
            return $responseData;
        } catch (Exception $ex)
        {
            \Log::info($ex->getTraceAsString());
            return $ex->getTraceAsString();
        }
    }

    public function getAllchannelIds()
    {
        try
        {
            $channelIds = DB::table('channel')
                    ->select(DB::raw('group_concat(channel_id) as channel_ids'))->first();
            if(!empty($channelIds))
            {
                $channelIds = $channelIds->channel_ids;
            }
            if($channelIds == '')
            {
                $channelIds = 1;
            }
            return $channelIds;
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage());
        }
    }

   
    
    public function updateOrderDetails($data)
    {
        try
        {
//            \Log::info(__METHOD__.' => '.__LINE__);
            //Log::info($data);
            $channelId = isset($data['channel_id']) ? $data['channel_id'] : 0;
            $statusId = isset($data['status_id']) ? $data['status_id'] : 0;
            $gdsOrderId = isset($data['gds_order_id']) ? $data['gds_order_id'] : 0;
            $gdsOrderMessage = isset($data['message']) ? $data['message'] : 0;
            $tpAttributes = isset($data['tp_attributes']) ? $data['tp_attributes'] : array();
            if($statusId != 17003)
            {
                $updateData['order_status_id'] = $statusId;
                $whereConditions['gds_order_id'] = $gdsOrderId;
                if($channelId)
                {
                    $whereConditions['channel_id'] = $channelId;
                }
                DB::table('gds_orders')->where($whereConditions)->update($updateData);                
            }else{
                $mfgId = $data['mfgID'];
                $locationId = $data['location_id'];
    $module_id = trim($data['module_id']);
    $access_token = trim($data['access_token']);
                $tpData = isset($data['tp_details']) ? json_decode($data['tp_details'], true) : array();
    $child_listJson = isset($tpData['child_list']) ? $tpData['child_list'] : array();
    $child_listArray = $child_listJson;
//    echo "<pre/>";print_r($child_list);exit;
//    $new_pallet = trim($tpData['new_pallet']);
//    $stock_transfer = trim($tpData['stock_transfer']);
    $tp = trim($tpData['tp']);
      //$srcLocationId = trim($tpData['srcLocationId'));
      $destLocationId = isset($tpData['destLocationId']) ? trim($tpData['destLocationId']) : 0;
      $transitionId = isset($tpData['transitionId']) ? trim($tpData['transitionId']) : 0;
      $transitionTime = isset($tpData['transitionTime']) ? trim($tpData['transitionTime']) : date('Y-m-d H:i:s');
      $tpDataMapping = isset($tpData['tpDataMapping']) ? trim($tpData['tpDataMapping']): '';
      $pdfFileName = isset($tpData['pdfFileName']) ? trim($tpData['pdfFileName']) : '';
      $pdfContent = isset($tpData['pdfContent']) ? trim($tpData['pdfContent']) : '';
      $sapcode = isset($tpData['sapcode']) ? trim($tpData['sapcode']): '';
                
                $palletsArray=array();
                $productsArray=$child_listArray;
               // \Log::info(__METHOD__.' => '.__LINE__);
//                $productsArray=array();
//                foreach ($child_listArray as $key => $value)
//                {
//                    $child_list = explode(',', $value['ids']);
//                    $weight = $value['weight'];
//                    $totweight = $totweight + $weight;
//                    $i = 0;
//                    foreach ($child_list as $key1 => $val1)
//                    {
//                        $getPallet = DB::Table('eseal_' . $mfgId)
//                                ->where(array('primary_id' => $val1, 'level_id' => 0))
//                                ->pluck('parent_id');
//                        if (in_array($getPallet, $palletsArray))
//                        {
//                            
//                        } else
//                        {
//                            $palletsArray[] = $getPallet;
//                        }
//                        $pallet_weight = DB::Table('eseal_' . $mfgId)
//                                ->where(array('primary_id' => $getPallet, 'level_id' => 8))
//                                ->pluck('pkg_qty');
//                        $new_weight = $pallet_weight - $weight;
//                        //return $new_weight;
//                        DB::table('eseal_' . $mfgId)
//                                ->where('primary_id', $getPallet)
//                                ->update(['pkg_qty' => $new_weight]);
//
//                        $pres_pallet_weight = DB::Table('eseal_' . $mfgId)
//                                ->where(array('primary_id' => $getPallet, 'level_id' => 8))
//                                ->pluck('pkg_qty');
//                        if ($pres_pallet_weight == 0)
//                        {
//                            DB::table('eseal_' . $mfgId)
//                                    ->where('primary_id', $getPallet)
//                                    ->update(['bin_location' => 'NULL']);
//                        }
//                        $productsArray[] = $val1;
//                    }
//                }
                $ids = implode(',',$productsArray);
                $request = \Request::create('scoapi/SyncStockOut', 'POST', 
                        array('module_id'=> intval($module_id),
                            'access_token'=>$access_token,
                            'ids'=>$ids,
                            'codes'=>$tp,
                            'srcLocationId'=>$locationId,
                            'destLocationId'=>$destLocationId,
                            'transitionTime'=>$transitionTime,
                            'transitionId'=>$transitionId,
                            'tpDataMapping'=>$tpDataMapping,
                            'pdfContent'=>$pdfContent,
                            'pdfFileName'=>$pdfFileName,
                            'sapcode'=>$sapcode));
            }
            if($statusId > 0)
            {
                $fetchOrderStatusString = DB::table('channel_order_status')->where('master_lookup_id', $statusId)->pluck('status_value');
                $getchannelOrderId = DB::table('gds_orders')->where('gds_order_id', $gdsOrderId)->pluck('channel_order_id');
                $updatechannelOrders = DB::table('channel_orders')
                        ->where(['channel_id' => $channelId, 'channel_order_id' => $getchannelOrderId])
                        ->update(['order_status' => $fetchOrderStatusString]);
            }
            return 'Updated sucessfully';
        } catch (Exception $ex) {
            \Log::info($ex->getTraceAsString());
            return $ex->getTraceAsString();
        }
    }
  
  public function getCategoryCharge($category_id,$price){

              $category_charge = DB::table('channel_categories as cc')
                                ->leftJoin('category_charges as cac','cac.category_id','=','cc.ebutor_category_id')
                                ->where('cc.channel_category_id',$category_id)
                                ->get()->all();
             
             $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id');
             
             $service_type_id = DB::table('channel_service_type')->where('service_type_name','PRODUCT_CATEGORY_LIST_FEE')->pluck('service_type_id');
             
             $percentage_charge = ($category_charge[0]->charges*$price)/100;
             
             $eseal_fee = DB::table('channel_categories as cc')
                          ->leftjoin('ebutor_categories as ec','ec.ebutor_category_id','=','cc.ebutor_category_id')
                          ->where('cc.channel_category_id',$category_id)
                          ->pluck('ec.charge');

                        

             $insert_charge  = DB::table('manf_charges')
                               ->insert([
                                'charges' => $percentage_charge,
                                'channel_id' => $eBay_channel_id,
                                'service_type_id' => $service_type_id,
                                'eseal_fee' => $eseal_fee,
                                'created_date' => date('Y-m-d H:i:s'),
                                'currency_id' => 4
                                ]);
            
  }

  


    public function DMAPI_CURL($form_url, $data) {
      
       $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $form_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, sizeof($data));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $catResult = curl_exec($curl);
        curl_close($curl);
        
        print_r($catResult);
    }

  

 }
