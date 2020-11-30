<?php

namespace App\Console\Commands;
date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use Log;
use App\Modules\Caching\Models\CachingModel;
use DB;
use Illuminate\Support\Facades\Cache;
use Utility;
class flushAllCacheProducts extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'command:flushAllCacheProducts {customer_type_id} {appKeyData} {dc_id} {user_id}';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        $undeleteCount = -1;
        $deleteCount = -1;
        $typeDeleteCount = -1;
        $typeUndeleteCount = -1;
        $arguments = $this->argument();
        $customer_type_id = $arguments['customer_type_id'];
        $this->appKeyData = $arguments['appKeyData'];
        $dc_id = $arguments['dc_id'];   
        $user_id = $arguments['user_id'];
        $environment = env('APP_ENV');     
        Log::info('All products flush is started');
        $this->cachingObj=new CachingModel();

        $customerTypes = $this->cachingObj->getCustomerType($customer_type_id);
        
        $productIds = $this->cachingObj->getProductInfo();
        $subject="Alert - Cache Flush has been completed successfully";
         $userInfo=DB::table('users')->select('email_id','firstname','lastname')->where('user_id',$user_id)->first();
        $toMail =$userInfo->email_id ;
        $fname = $userInfo->firstname;
        $lname = $userInfo->lastname;
        $body = array('template'=>'emails.cacheFlushMail', 'attachment'=>'', 'topMsg'=>'', 'emailContent' =>'','name'=>"Tech Support - " . $environment,'fname'=>$fname,'lname'=>$lname);
        DB::unprepared("call ProdSlabFlatRefreshByProductIdByCust(NULL,$dc_id,$customer_type_id)");
        Utility::sendEmail($toMail, $subject, $body);    
        log::info('done');
         
            if(!empty($productIds))
            
                foreach($productIds as $product) {

                    foreach ($customerTypes as $customerType) {
                         
                        $keyString = $this->appKeyData.'_product_slab_'.$product->product_id.'_customer_type_'.$customerType->value.'_le_wh_id_'.$dc_id;
                        log::info($keyString);
                        $response = null;
                        $response = Cache::get($keyString);
                        $status = $this->flushSingleProduct($product->product_id,$customerType->value,$dc_id,$response);
                        
                      
                        //if($status)
                            // $deleteCount++;             // If the product has been flushed.
                         //else
                             //$undeleteCount++;   
                                     // If the product has not been flushed.
                }
            }
            /*if(($undeleteCount == -1) and ($deleteCount != -1))
                $message= trans('caching.messages.all_products');
            else
                $message= ($deleteCount+1).trans('caching.messages.all_products_flush.1').($undeleteCount+1).trans('caching.messages.all_products_flush.2');*/
          //  DB::selectFromWriteConnection(DB::raw("CALL getRefreshProducts(1)"));
        
            
        
    }
    public function flushSingleProduct($product_id = null,$customer_type_id = null,$dc_id = null,$response = null){

        $status = false;
        if(($dc_id == "all") and !empty($response)) {
            // The Below Code is to Delete all the Dc`s Cache for a Single Product...
            // Cache::flush($response);
            
            $keyString = $this->appKeyData.'_product_slab_'.$product_id.'_customer_type_'.$customer_type_id.'_le_wh_id_'.$dc_id;

            Cache::forget($keyString);
            $status = true;
            
            return $status;
        }

        if(($dc_id != null) and !empty($response)) {
            $unSetCount=0;
            $response = json_decode($response,true);
            foreach ($response as $key => $value) {
                if($dc_id == $key){
                    unset($response[$key]);
                    $unSetCount++;
                    // Here, we are removing the data, which is not required tobe flushed.
                }
            }
            if($unSetCount>0 and empty($response))  $status = true;
        }

        if($status == true or !empty($response)) {

            $keyString = $this->appKeyData.'_product_slab_'.$product_id.'_customer_type_'.$customer_type_id.'_le_wh_id_'.$dc_id;
            Cache::put($keyString, json_encode($response), CACHE_TIME);
            $status = true;
        }

        return $status;
    }
}
