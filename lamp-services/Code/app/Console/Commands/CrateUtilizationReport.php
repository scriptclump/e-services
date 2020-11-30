<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Session;

use App\Modules\PickerEfficiencyReport\Controllers\CrateUtilizationReportController;

class CrateUtilizationReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrateUtilization {start_date} {end_date} {email}';

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
         $start_date = $arguments['start_date'];
         $end_date = $arguments['end_date'];
         $email = $arguments['email'];
        \Session::put('userId', 0);
        
        $Obj = new CrateUtilizationReportController();
        $result = $Obj->crateUtilizationReport($start_date, $end_date, $email);
        $this->info("Report Email Sent to ".$email);
        return true;  
    }
}
