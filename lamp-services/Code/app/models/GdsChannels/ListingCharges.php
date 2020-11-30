<?php

namespace App\models\GdsChannels;

use Illuminate\Database\Eloquent\Model;

class ListingCharges extends Model {
	public function Publish($name = " "){
	try{
			

			if($name=="eBay")
			{
			
			$form_url= URL::asset('/ebaydeveloper/AddItem');
			
			}	
			if(empty($name)){
			
			//print_r('expression');exit;
			
			$form_url = URL::asset('/ebaydeveloper/AddItem');
			
			}
			
			$curl = curl_init();
           
            curl_setopt($curl,CURLOPT_URL, $form_url);

            curl_setopt($curl,CURLOPT_RETURNTRANSFER, TRUE);

            $catResult = curl_exec($curl);
            
            curl_close($curl);
            
            $message = json_decode($catResult);
            
            if(!empty($message->Message[0]->item_id)){
       		
       		$this->calculateCharges($message->Message[0]->item_id,$name);
       		
      		 }
			
		}
	catch(Exception $e){
			$message=$e->getMessage();
		}
		
		return $message;			
	
	}

	public function calculateCharges($item_id,$name){
	
	try{
			 if(!empty($item_id)){
			 
			 $category = DB::table('channel_product_add_update as cpad')
			 			 ->leftJoin('products as pd','pd.product_id','=','cpad.product_id')
			 			 ->where('cpad.channel_product_key',$item_id)
			 			 ->select('pd.mrp','cpad.category_id','pd.manufacturer_id')
			 			 ->get()->all();
			 
			 }
			 
//			 Log::info($category[0]->manufacturer_id);

			 if(!empty($category)){
			 	
			 $eBay_channel_id = DB::table('channel')->where('channnel_name',$name)->pluck('channel_id')->all();

			 $category_charge = DB::table('channel_categories as cc')
                                ->leftJoin('category_charges as cac','cac.category_id','=','cc.ebutor_category_id')
                                ->leftjoin('ebutor_categories as ec','ec.ebutor_category_id','=','cc.category_id')
                                ->where(array('cc.ebutor_category_id'=>$category[0]->category_id,'cc.channel_id'=>$eBay_channel_id))
                                ->select('cac.charges as market_percentage','ec.charge as ebutor_percentage')
                                ->get()->all();
            
             
             $service_type_id = DB::table('channel_service_type')->where('service_type_name','PRODUCT_CATEGORY_LIST_FEE')->pluck('service_type_id');
				             
             $market_charge = ($category_charge[0]->market_percentage*$category[0]->mrp)/100;

             $ebutor_charge = ($category_charge[0]->ebutor_percentage*$category[0]->mrp)/100;
             
			
			 $insert_charge  = DB::table('manf_charges')
                               ->insert([
                                'charges' => $market_charge,
                                'channel_id' => $eBay_channel_id,
                                'service_type_id' => $service_type_id,
                                'eseal_fee' => $ebutor_charge,
                                'created_date' => date('Y-m-d H:i:s'),
                                'currency_id' => 4 ,
                                'manf_id' => $category[0]->manufacturer_id ,
                                ]);
			
			}
			
		
		}
		catch(Exception $e){
			$message=$e->getMessage();
		}
	}
}
