<?php

/*
  Filename  : LeaveApprovalsNotificationConsole.php
  Author    : Ebutor
  Date      : 11-October-2017
  Desc      : Console to send a notification to managers regarding the pending leave approvals
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\LeaveManagement\Controllers\LeaveManagementController;

class LeaveApprovalsNotificationConsole extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LeaveApprovalsNotify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console to send a notification to managers regarding the pending leave approvals';

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
    public function handle() {
        $this->info("LeaveApprovalsNotify command has been executed!");
        
        $leaveMng = new LeaveManagementController();
        
        $leaveMng->pendingApprovalNotifications();
        
        $this->info("Push notification has been sent!");
        
        return true;
    }

}
