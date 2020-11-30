<?php

/*
  Filename : DmsReportMail.php
  Author : Ebutor
  CreateData : 29-Dec-2016
  Desc : Command to send the DMS report in mail daily @ 23:00
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\MeanProducts\Controllers\DmsEmailSetupController;

class DmsReportMailConsole extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DmsReportMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule job to DMS report as mail @ 23:00 daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->_DmsEmailSetupController = new DmsEmailSetupController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->_DmsEmailSetupController->dmsRepMail();
    }

}
