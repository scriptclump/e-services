<?php

namespace App\Console\Commands;
date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use Log;
use App\Modules\Caching\Models\CachingModel;
use DB;
use Illuminate\Support\Facades\Cache;
use Utility;
class flushAllCache extends Command {

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
    protected $signature = 'flushAllCache {customer_type_id} {appKeyData} {dc_id} {user_id}';


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
       log::info("All rpoducts & dc flush started");
        $arguments = $this->argument();
        $environment = env('APP_ENV');
        $customer_type_id = $arguments['customer_type_id'];
        $this->appKeyData = $arguments['appKeyData'];
        $dc_id = $arguments['dc_id'];
        $user_id = $arguments['user_id'];
        $subject= "Alert - Cache Flush has been completed successfully";
         $userInfo=DB::table('users')->select('email_id','firstname','lastname')->where('user_id',$user_id)->first();
        $toMail =$userInfo->email_id ;
        $fname = $userInfo->firstname;
        $lname = $userInfo->lastname;
        $body = array('template'=>'emails.cacheFlushMail', 'attachment'=>'', 'topMsg'=>'', 'emailContent' =>'','name'=>"Tech Support - " . $environment,'fname'=>$fname,'lname'=>$lname); 
        DB::unprepared("CALL getRefreshProducts(1)");
        Utility::sendEmail($toMail, $subject, $body);  
        log::info('done');
        
    }
 
}
