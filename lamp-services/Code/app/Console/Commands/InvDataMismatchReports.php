<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\InvDataMismatchReports\Controllers\ReportController;
class InvDataMismatchReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:invdatamismatchreports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is for inventory data mismatch report to show table wise all data.';

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
        $message= $reportsObj->index();
        $this->info($message);
    }
}
