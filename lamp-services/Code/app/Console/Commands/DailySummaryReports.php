<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\summary_reports\Controllers\SummaryReportController;

class DailySummaryReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:dailysummaryreports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'We are sending daily summary reports for Sales and CRM, Logistics, purchase';

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
        $reportsObj = new SummaryReportController();
        $message= $reportsObj->index();
        $this->info($message);
    }
}
