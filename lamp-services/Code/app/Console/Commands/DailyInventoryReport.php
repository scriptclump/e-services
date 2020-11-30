<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Session;

use App\Modules\DailyInventoryReport\Controllers\DailyInventoryReportController;

class DailyInventoryReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DailyInventoryReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // $email = $arguments['email'];
        // \Session::put('userId', 0);
        $dailyInventoryObj = new DailyInventoryReportController();
        $result = $dailyInventoryObj->indexAction();
        $this->info("Report Email Sent to ".$result);
        return true;  
    }
}