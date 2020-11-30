<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Session;
use App\Modules\Inventory\Controllers\InventoryController;


class InventoryFinalApprovalMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InventoryFinalApprovalMail {emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        var_dump($arguments);
        $data = base64_decode($arguments['emails']);
        
        $email = json_decode($data, true);

        \Session::put('userId', $email['user']);

        $Obj                    = new InventoryController();
        $file                   = isset($email['file'])?$email['file']:"";
        $bulkuploadvals         = isset($email['bulkupload'])?$email['bulkupload']:"";
        $newVals                = isset($email['newvals'])?$email['newvals']: "";


        $result = $Obj->mailInventoryApproved($email['email'], $file, $bulkuploadvals, $newVals);

        // var_dump($result);
        // $this->info("Report Email Sent to ".$email['email']);
        return true;
        }
}
