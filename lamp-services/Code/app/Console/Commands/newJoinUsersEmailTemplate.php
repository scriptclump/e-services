<?php
/**
 *
 * Created By : Prathima Reddy
 * date : 1st July 2019
 * Description : This email is to get new join email for users on their joining date.
 * 
 */

namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\ProductRepo;
use DB;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Mail;
class newJoinUsersEmailTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newjoinemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is used for sending emails to the users on their joining date in company';

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
        $viewKey = "vw_new_joinee_email";
        $query =DB::select(DB::raw("select * from ".$viewKey));
                foreach ($query as $key => $value) {
                    $name=$value->NAME;
                    $profile_pic=$value->PROFILE_PICTURE;
                    $Message=$value->SUBJECT;
                    $email=$value->EMAIL;
                    $designation=$value->DESIGNATION;
                    $subject = "Welcome Email ".$value->NAME;
                    $rss=Mail::send('emails.newjoinemployeemailtemplate',['name'=>$name,'profile_pic'=>$profile_pic,'message_text'=>$Message,'designation_text'=>$designation], function ($m) use ($email, $subject)
                    {
                        $m->to(["tech.team@Ebutor.com","Shravan.Mavurapu@Ebutor.com"])->subject($subject);
                        $m->cc($email)->subject($subject);
                        
                    });                        
                }             
    }
}

  