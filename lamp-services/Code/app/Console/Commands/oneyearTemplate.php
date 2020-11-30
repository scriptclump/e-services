<?php
/**
 *
 * Created By : Prathima Reddy
 * date : 26th June 2019
 * Description : This email is to get one year completion email for users in this company.
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
class oneyearTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oneyearemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This email is to get one year completion email for users in this company.';

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
        $viewKey = "vw_EmpGreeting";
        $query =DB::select(DB::raw("select * from ".$viewKey));
                foreach ($query as $key => $value) {
                    $name=$value->NAME;
                    $year=$value->YEARS;
                    $profile_pic=$value->Profile_Picture;
                    $Message=$value->Greeting;
                    $email=$value->EMAIL;
                    $subject = "Congratulations".$value->NAME;
                    $rss=Mail::send('emails.oneyearemailtemplate',['name'=>$name,'profile_pic'=>$profile_pic,'message_text'=>$Message,'year'=>$year], function ($m) use ($email, $subject)
                    {
                        $m->to(["tech.team@Ebutor.com"])->subject($subject);
                        $m->cc($email)->subject($subject);
                        
                    });                        
                }             
    }
}

  