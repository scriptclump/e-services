<?php

/*
  Filename  : OutwardSupplyReportConsole.php
  Author    : Ebutor
  Date      : 20-July-2017
  Desc      : Console to generate and email Outward Supply Report to requested user
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\GSTReports\Controllers\GstReportsController;

class OutwardSupplyReportConsole extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OutwardSupplyReport {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console to generate and email Outward Supply Report to requested user';

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
        $arguments = $this->argument();
        $data = base64_decode($arguments['data']);
        $this->info("Parameters " . $data);
        $data = json_decode($data, true);

        \Session::put('userId', $data['userId']);
        $gstReport = new GstReportsController();
        $result = $gstReport->createExcel($data['userId'], $data['start'], $data['end'], $data['type']);

        $this->info("Report Email Sent to " . $result);
        return true;
    }

}
