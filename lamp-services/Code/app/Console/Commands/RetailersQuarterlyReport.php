<?php

/**
 *
 * Created By : Prathima Reddy
 * date : 13th Dec 2019
 * Description : This command is used to send retailers quarterly data state wise excel.
 * 
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\InvDataMismatchReports\Controllers\ReportController;
class RetailersQuarterlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:retailersquarterlyreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is for retailers quarterly data  report to show state wise all data.';

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
        $reportsObj = new ReportController();
        $message= $reportsObj->sendEmailByMultipleExcel();
        $this->info($message);
    }
}
