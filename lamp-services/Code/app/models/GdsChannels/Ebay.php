<?php

namespace GdsChannels;
/*use Central\Repositories\CustomerRepo;
use Central\Repositories\RoleRepo;
use Central\Repositories\SapApiRepo;*/
use \DB;
use \Response;
use \URL;
use \Log;
use \Session;
use \Redirect;

class Ebay extends \Eloquent {


public function getGdsProducts($seller_id = " "){
     	
        $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all();
        
        $base_query  =    DB::table('channel_product_add_update')
                        ->leftJoin('products as prod','channel_product_add_update.product_id','=','prod.product_id')
                        ->leftJoin('product_seller_mapping as pm','pm.product_id','=','channel_product_add_update.product_id')
                        ->leftJoin('product_inventory','product_inventory.product_id','=','channel_product_add_update.product_id')
                        ->leftJoin('locations','product_inventory.location_id','=','locations.location_id')
                        ->leftJoin('product_attributes','product_attributes.product_id','=','prod.product_id')
                        ->leftJoin('attributes','product_attributes.attribute_id','=','attributes.attribute_id')
                        ->leftJoin('channel_categories','channel_categories.ebutor_category_id','=','channel_product_add_update.category_id')
                        ->where('is_added',1)
                        ->where('channel_product_add_update.channel_id',$eBay_channel_id)
                        ->where('channel_categories.channel_id',$eBay_channel_id);
                
            if($seller_id != " "){

              $subquery = $base_query->where('pm.seller_id',$seller_id); 
            
            } 
          else{
                
              $subquery = $base_query;
            
            }   

            $finalquery = $subquery->select(DB::raw('CONCAT(group_concat(product_attributes.value,"$",attributes.name)) as product_attributes'),'prod.name as Title','prod.description as Description','prod.product_id','channel_product_add_update.channel_sales_price as StartPrice','prod.image as PictureURL','channel_categories.channel_category_id','prod.upc','locations.location_address','locations.city','pm.seller_id')
                ->groupBy('prod.product_id')
                ->take(10)->get()->all();
            
            //In case of multiple products or bulk products we have to make sure that bulk producta are inserted into "channel_product_add_update"
            //Inventory should be not null
            //category should be mapped with ebay categories.
            //sellers should be configured in "ebutor_seller" table.
            //product to seller are mapped in "product_seller_mapping".
          return $finalquery;
      }

      public function ProductListUpdate($product_id,$listing_date,$next_listing_date,$channel_product_key){

	  $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all();

	    DB::table('channel_product_add_update')
	    ->where('product_id', $product_id)
	    ->where('channel_id', $eBay_channel_id)
	    ->update(array('is_added'=> 0 ,
	                   'listing_date' => $listing_date,
	                   'next_listing_date' => $next_listing_date,
	                   'channel_product_key' => $channel_product_key
	    ));
	 
  }

   public function getUpdateItem()
    {
              $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all(); 
              
              return  DB::table('products as prod')
                      ->leftJoin('channel_product_add_update as cpau','prod.product_id','=','cpau.product_id')
                      ->leftJoin('product_attributes','product_attributes.product_id','=','prod.product_id')
                      ->leftJoin('attributes','product_attributes.attribute_id','=','attributes.attribute_id')
                      ->select(DB::raw('CONCAT(group_concat(product_attributes.value,"$",attributes.name)) as product_attributes'),'prod.name as Title','prod.description as Description','prod.product_id','cpau.channel_sales_price as StartPrice','cpau.channel_product_key as ItemID','prod.image as PictureURL')
                      ->where('is_update','=','1')
                      ->where('cpau.channel_id',$eBay_channel_id)
                      ->groupBy('prod.product_id')
                      ->take(10)
                      ->get()->all();
   
   }


    public function UpdateItemdb($item_id){
    
    $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all();

    DB::table('channel_product_add_update')
          ->where(array('channel_product_key'=>$item_id,'channel_id'=>$eBay_channel_id))
          ->update(array('is_update'=> 0
          )); 


  }

    
    public function get_updated_qty()
    {       
          $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all(); 
          
          $result = DB::table('channel_product_add_update')
                     ->select('channel_product_key as ItemID','expose_inventory as Quantity','product_id')
                     ->where(array('is_update'=>1,'channel_id'=>$eBay_channel_id))
                     ->get()->all();
          
          return  $result;

    }

    public function UpdateInventorydb($item_id){
  
	  $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all();    
	  
	  DB::table('channel_product_add_update')
	           ->where(array('channel_product_key'=> $item_id,'channel_id'=>$eBay_channel_id))
	          ->update(array('is_update'=> 0
	          ));   
  }

  public function getAddInventory($pid){
     
     $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all();

      return DB::table('channel_product_add_update')
             ->select(DB::raw('expose_inventory as qty'))
             ->where(array('product_id'=>$pid,'channel_id'=>$eBay_channel_id))
             ->pluck('qty')->all();  
     
     }

       public function getselleraddress($seller_id){

      $address=DB::table('ebutor_seller_warehouse_mapping as ebwm')
             ->leftJoin('ebutor_warehouses as ew','ew.warehouse_id','=','ebwm.warehouse_id')
             //->leftjoin('ebutor_warehouses as ews','ews.state','=','zone.zone_id')
             ->where('ebwm.seller_id',$seller_id)
             ->select('ew.warehouse_name','ew.city','ew.warehouse_id')
             ->get()->all(); 
       
       $state=DB::table('ebutor_warehouses as ew')
              ->leftjoin('zone as zn','ew.state','=','zn.zone_id')
              ->where('ew.warehouse_id',$address[0]->warehouse_id)
              ->pluck('zn.name')->all();        

        $address[0]->state=$state;

        return $address;

    }       

      public function ProductAvailable($item_id){
     
     return  DB::table('channel_product_add_update as Cpau')
            ->leftJoin('product_inventory as pi','pi.product_id','=','Cpau.product_id')
            ->leftJoin('products as pd','pd.product_id','=','Cpau.product_id')
            ->where('channel_product_key',$item_id)
            ->first();   

    }

     public function getdispute()
    {
         $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all();

         $cancel_orders = DB::table('gds_order_products as gop')
                           ->leftJoin('gds_orders as go','go.gds_order_id','=','gop.gds_order_id')
                           ->leftJoin('gds_orders_payment as gopp','gop.gds_order_id','=','gopp.gds_order_id')
                           ->where('go.channel_id',$eBay_channel_id)
                           ->where('gop.order_status',1)
                           ->get()->all();

         foreach($cancel_orders as $cancelOrder){
             
            $if_exists = DB::table('channel_order_disputes')->where('order_id',$cancelOrder->channel_order_id)->pluck('order_id');
            
            if(empty($if_exists)){
            
            DB::table('channel_order_disputes')
            ->insert([ 'order_id' => $cancelOrder->channel_order_id,
                      'item_id' => $cancelOrder->channel_item_id,
                      'dispute_status' => "WaitingForBuyerResponse",
                      'dispute_explanation' => "BuyerNoLongerWantsItem",
                      'dispute_reason' => "TransactionMutuallyCanceled",
                      'raise_dispute' =>1,
                      'transaction_id' => $cancelOrder->transaction_id,
                      ]);
            
           }
           
         }                       
         
         return DB::table('channel_order_disputes')
              ->where('raise_dispute',1)
               ->orWhere('raise_dispute',2)
               ->select('dispute_reason','dispute_explanation','order_id','item_id','transaction_id','raise_dispute')
               ->get()->all();
    }

      public function updateDispute($order_id,$DisputeID,$links,$Timestamp){
    
      DB::table('channel_order_disputes')
      ->where('order_id',$order_id)
      ->update([
          'dispute_id'=>$DisputeID,
          'ack'=>$links,
          'dispute_created_time'=>$Timestamp,
          'raise_dispute'=>'0'
          ]);

  }

   public function getraisedDisputes() 
    {
      return DB::table('channel_order_disputes')
               ->where('dispute_status','WaitingForBuyerResponse')
               ->where(DB::raw('DATEDIFF(dispute_modified_time,dispute_created_time)'),'>',7)
               ->select(DB::raw("DATEDIFF(dispute_modified_time,dispute_created_time) AS Days"),'channel_order_disputes.dispute_id')
               ->get()->all();
    } 


  public function getProduct($DisputeID){

    return  DB::table('channel_product_add_update as cpad')
          ->leftJoin('channel_order_disputes as cod','cod.item_id','=','cpad.channel_product_key')    
          ->where('cod.dispute_id',$DisputeID)
          ->pluck('cpad.product_id');
 }


 public function UpdateAllDisputes($DisputeID,$DisputeStatus,$DisputeReason,$DisputeExplanation,$Timestamp,$ItemID){

    
    DB::table('channel_order_disputes')
     ->where('dispute_id', $DisputeID)
     ->update(array( 'dispute_status' => $DisputeStatus,
    'dispute_reason'=>$DisputeReason,
    'dispute_explanation'=>$DisputeExplanation,
    'dispute_modified_time'=>$Timestamp,
    'raise_dispute' => 0                     
    ));

    $order_id=DB::table('gds_order_products')->where('channel_item_id',$ItemID)->pluck('gds_order_id')->all();

    $cancelled = DB::table('master_lookup as ml')
                             ->leftjoin('lookup_categories as lp','lp.id','=','ml.category_id') 
                             ->where('lp.name','Order Status')  
                             ->where('ml.name','Canceled')
                             ->pluck('ml.value');
    DB::table('gds_orders')
     ->where('gds_order_id',$order_id)
     ->update(array('order_status_id' => $cancelled));

  }

   public function endProducts($seller_id = " "){
    try{

        $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all(); 
        
        $result = DB::table('channel_product_add_update as ch')
                  ->leftJoin('product_seller_mapping as pm','pm.product_id','=','ch.product_id')
                  ->where(array('ch.channel_id'=>$eBay_channel_id,'ch.is_deleted'=>1));

         if($seller_id != " "){

          $subquery = $result->where('pm.seller_id',$seller_id);
        }
        else{

          $subquery = $result;

        }
        
        $finalquery =$subquery->get()->all();
        
        return $finalquery;
        
        }
        catch(Exception $e){
            $e->getMessage();
        }
    }

     public function updatedeletedproduct($product_id){

    $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all(); 

    DB::table('channel_product_add_update')
    ->where(array('channel_id'=>$eBay_channel_id,'product_id'=> $product_id))
    ->update(array('is_deleted'=> 0
          ));   

    return DB::table('channel_product_add_update')->where(array('channel_id' => $eBay_channel_id,'product_id'=>$product_id))->pluck('channel_product_key');

  }

     public function getrelistproducts($seller_id = " ") 
    {
      
      $eBay_channel_id = DB::table('channel')->where('channnel_name','eBay')->pluck('channel_id')->all();
      

      $result = DB::table('channel_product_add_update as ch')
                ->leftJoin('product_seller_mapping as pm','pm.product_id','=','ch.product_id')
                ->where(DB::raw('DATEDIFF(NOW(),listing_date)'),'>','30')
                ->where('ch.channel_id',$eBay_channel_id)
                ->where(DB::raw('DATEDIFF(NOW(),listing_date)'),'<','90');
       
       if($seller_id != " "){
        
        $subquery = $result->where('pm.seller_id',$seller_id);

       }
       else{

        $subquery = $result;
       
       }        

       $finalquery = $subquery->select('ch.channel_product_key as ItemID','ch.product_id','ch.channel_id')
                      ->get()->all();

        return $finalquery;              
     
    }

     public function UpdateRelisting($product_id,$StartTime,$EndTime,$newItemID,$eBay_channel_id,$ItemID){

       $getData = DB::table('channel_product_add_update')->where('channel_product_key',$ItemID)->get()->all();
        
       DB::table('channel_product_add_update')
            ->insert([
                'product_id' => $product_id,
                'channel_sales_price' => $getData[0]->channel_sales_price,
                'expose_inventory' => $getData[0]->expose_inventory,
                'category_id' => $getData[0]->category_id,
                'listing_date'=> $StartTime,
                'next_listing_date'=> $EndTime,
                'channel_product_key'=>$newItemID,
                'channel_id'=>$eBay_channel_id
                ]);

     DB::table('channel_product_add_update')->where('channel_product_key',$ItemID)->update(array('listing_date'=>"0"));

 }

   public function getallCategories(){

    return DB::table('ebay_categories')->get()->all();
   
   }
 



}
