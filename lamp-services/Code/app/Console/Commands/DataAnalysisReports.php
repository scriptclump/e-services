<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\DataAnalasisReports\Controllers\ReportController;
class DataAnalysisReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:dataanalysisreports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is for Data Analysis report  to show table wise all data. ';


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
