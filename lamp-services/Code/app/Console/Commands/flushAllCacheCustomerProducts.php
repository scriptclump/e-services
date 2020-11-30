<?php

namespace App\Console\Commands;
date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use Log;
use App\Modules\Caching\Models\CachingModel;
use DB;
use Illuminate\Support\Facades\Cache;
use Utility;
class flushAllCacheCustomerProducts extends Command {

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
    protected $signature = 'flushAllCacheCustomerProducts {customer_type_id} {appKeyData} {dc_id} {user_id}';


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

       
        $arguments = $this->argument();
        $customer_type_id = $arguments['customer_type_id'];
        $this->appKeyData = $arguments['appKeyData'];
        $dc_id = $arguments['dc_id'];  
        $environment = env('APP_ENV');
        $user_id = $arguments['user_id'];      
        Log::info('All products/dc flush is started');
            $subject="Alert - Cache Flush has been completed successfully";
             $userInfo=DB::table('users')->select('email_id','firstname','lastname')->where('user_id',$user_id)->first();
            $toMail =$userInfo->email_id ;
            $fname = $userInfo->firstname;
            $lname = $userInfo->lastname;
            $body = array('template'=>'emails.cacheFlushMail', 'attachment'=>'', 'topMsg'=>'', 'emailContent' =>'','name'=>"Tech Support - " . $environment,'fname'=>$fname,'lname'=>$lname);
            DB::unprepared("call ProdSlabFlatRefreshByProductIdByCust(NULL,$dc_id,NULL)");
            
            Utility::sendEmail($toMail, $subject, $body); 
            log::info('done products/Customers flush');
        
    }
 
}
