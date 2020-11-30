<?php
/**
 *
 * Created By : Prathima Reddy
 * date : 4th June 2019
 * Description : This email is to get birthday email for users on their birthday date.
 * 
 */

namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\RetailerSMS\Controllers\SMSRetailerController;
use App\Modules\RetailerSMS\Models\SMSRetailer;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\ProductRepo;
use DB;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Mail;
class birthdayTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdayemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is used for sending emails to the users on their birthday';

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
        $viewKey = "vw_BirthdayEmp";
        $query =DB::select(DB::raw("select * from ".$viewKey));
                foreach ($query as $key => $value) {
                    $name=$value->NAME;
                    $profile_pic=$value->PROFILE_PICTURE;
                    $Message=$value->SUBJECT;
                    $email=$value->EMAIL;
                    $birthday=$value->BIRTHDAY;
                    $subject = "Happy Birthday ".$value->NAME;
                    $rss=Mail::send('emails.birthdayemailtemplate',['name'=>$name,'profile_pic'=>$profile_pic,'message_text'=>$Message,'birthday_text'=>$birthday], function ($m) use ($email, $subject)
                    {
                        $m->to(["team@Ebutor.com"])->subject($subject);
                        $m->cc($email)->subject($subject);
                        
                    });                        
                }             
    }
}

  