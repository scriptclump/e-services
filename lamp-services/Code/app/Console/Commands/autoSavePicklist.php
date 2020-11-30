<?php

/*
  Filename : autoReassignpickerChecker.php
  Author : Ebutor
  CreateData : 16-Apr-2018
  Desc : Schedule Job to take capture Employee Attendance @ 04:00 hrs daily
 */

namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\Orders\Controllers\AutoAssignPickerCheckerController;
use App\Modules\Orders\Controllers\OrdersController;
use Session;
use Log;

class autoSavePicklist extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoSavePicklist {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Save Picklist -- daily';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct() {
        parent::__construct();
        Session::set('userId', 0);
        $this->_autoAssign = new AutoAssignPickerCheckerController();
        $this->_orderController = new OrdersController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        echo "\nStart AutosavePicklist time: " . date('Y-m-d H:i:s') . "\n";
//        Log::info('AutoSavePicklist Console start');
        $arguments = $this->argument();
        $data = $arguments['data'];        
        $data = base64_decode($data);
        $data = json_decode($data,true);
        $result = $this->_orderController->savePicklistAction($data);
    //    Log::info('AutoSavePicklist Console End');
        //$result = $this->_autoAssign->autoAssignPicker();
        //echo "<pre>"; print_r($allData);
        if ($result == 'Success')
            echo "job Done !!\n";
        else
            echo $result;
        echo "End time: " . date('Y-m-d H:i:s') . "\n";
    }
}
