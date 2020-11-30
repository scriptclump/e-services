<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use DB;
use App\Lib\Queue;
use Log;
use Session;
use App\Modules\Product\Controllers\PIMAllProductDownloadController;

class AllPIMDownloadReportConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AllPIMDownloadReport {data} {leid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will be the console front of the PIMDownloadController  controller';

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
        //log::info("arguments-----");
        //log::info(print_r($arguments['data'],true));
        $userId = $arguments['data'];
        $leid = $arguments['leid'];
        //log::info($userId);
       
        \Session::put('userId', $userId);
        //Log::info("Console command for all product download report...");
        $productobj = new PIMAllProductDownloadController();
        //echo $productobj->sample();
        $productobj->downloadAllProductInfo($userId,$leid);
        return true;
        
    }

}
