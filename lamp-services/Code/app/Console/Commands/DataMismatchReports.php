<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\TechSupportDataReports\Controllers\ReportController;
class DataMismatchReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:datamismatchreports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is for tech support team to show table wise all data. like gds order, legalentity warehouse, gds orders products';

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
