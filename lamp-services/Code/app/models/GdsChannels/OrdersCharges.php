<?php
namespace App\models\GdsChannels;
/*use Central\Repositories\CustomerRepo;
use Central\Repositories\RoleRepo;
use Central\Repositories\SapApiRepo;*/
use DB;
use Response;
use URL;
use Log;
use Session;
use Redirect;
use Illuminate\Database\Eloquent\Model;

class OrdersCharges extends Model
{

public function createOrders($channelName,$order_id){

	try{

     $channel_id = DB::table('mp')->where('mp_name',$channelName)->pluck('mp_id');
      

      if($channelName=="Factail"){

            $category = DB::table('gds_order_products as coid')
                         ->leftJoin('products as pd','pd.product_id','=','coid.product_id')
                         ->where('gds_order_id',$order_id)
                         ->select('pd.mrp','pd.category_id','pd.legal_entity_id')
                         ->get();
            
//        Log::info($category[0]->legal_entity_id);

          if(!empty($category)){
                
          $eBay_channel_id = DB::table('mp')->where('mp_name',$channelName)->pluck('mp_id');
            
          $category_charge = DB::table('mp_category_mapping as cc')
                                ->leftJoin('categories as ca','ca.category_id','=','cc.category_id') 
                                ->leftJoin('mp_categories as mc' , 'mc.mp_id', '=', 'cc.mp_id' )   
                                ->where(array('cc.category_id'=>$category[0]->category_id,'cc.mp_id'=>$eBay_channel_id))
                                ->select('mc.mp_commission as market_percentage','ca.charges as ebutor_percentage')
                                ->get()->all();
          
          $service_type_id = DB::table('mp_service_type')->where('service_type_name','PRODUCT_CATEGORY_LIST_FEE')->pluck('service_type_id');
          if (count($category_charge) == 0) {
              $market_charge = 0;
              $ebutor_charge = 0;
          }else{
            $market_charge = ($category_charge[0]->market_percentage*$category[0]->mrp)/100;
            
            $ebutor_charge = ($category_charge[0]->ebutor_percentage*$category[0]->mrp)/100;
          }
             
        
          $insert_charge  = DB::table('le_charges')
                               ->insert([
                                'charges' => $market_charge,
                                'mp_id' => $eBay_channel_id[0],
                                'service_type_id' => $service_type_id[0],
                                'ed_fee' => $ebutor_charge,
                                'created_at' => date('Y-m-d H:i:s'),
                                'currency_id' => 4 ,
                                'legal_entity_id' => $category[0]->legal_entity_id ,
                                ]);
            
            }  

     
     }
     
     $category_charge = DB::table('mp_charges as cc')
                            ->leftJoin('mp_service_type as cst','cst.service_type_id','=','cc.service_type_id')
                            ->where('cc.mp_id',$channel_id)
                            ->get()->all();
     
     foreach($category_charge as $value){
     	 
     	 $calculatecharges = $this->calculateOrderCharges($value,$order_id);
         
         }
     	 
	$message = "Successfully Updated the charges";
	}
	catch(Exception $e){

		$message = $e->getMessage();
	}		

	return $message;
}

 public function calculateOrderCharges($chargedata,$order_id){
 
 
    try{
 		
        if(!empty($chargedata)){
 		
 		$product_data = DB::table('gds_order_products as cod')
 						->leftJoin('gds_orders as go','go.gds_order_id','=','cod.gds_order_id')
                        ->leftJoin('products as pd','pd.product_id','=','cod.product_id')
 						->select('pd.mrp','pd.legal_entity_id','go.mp_id','go.total')
 						->where('cod.gds_order_id',$order_id)
 						->get()->all();	
 		
        if($chargedata->charge_type==1){
        
        $percentage_charge = ($chargedata->charges*$product_data[0]->total)/100;

        }
        else{

        $percentage_charge = $chargedata->charges;

        }
        

        DB::table('le_charges')
           ->insert([
            'charges' => $percentage_charge,
            'mp_charges_id' =>  $chargedata->channel_charges_id,
            'mp_id' => $product_data[0]->channel_id,
            'ed_fee'  => $chargedata->eseal_fee,
            'service_type_id' => $chargedata->service_type_id,
            'reference_id' => $order_id,
            'created_at' => date('Y-m-d H:i:s'),
            'currency_id' => 4,
            'legal_entity_id' => $product_data[0]->legal_entity_id,
            ]);
 			
 		

 		}
 	
 	}
 	catch(Exception $e){
 		$e->getMessage();
 	}
 	
 }
}
