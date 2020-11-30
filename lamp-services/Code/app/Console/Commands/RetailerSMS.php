<?php
namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\RetailerSMS\Controllers\SMSRetailerController;

class RetailerSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:autoretailersms { notification_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is for retailers for sending sms to their sales executive for order placing and delivery';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$arguments = $this->argument();
        $notification_code = $arguments['notification_code'];
        $smsretailerObj = new SMSRetailerController();
        $message= $smsretailerObj->index($notification_code);
    }
}
