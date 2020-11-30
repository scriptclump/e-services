<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\TallyConnector\Controllers\tallyGenerateReportController;

class tallyGenerateTallyVSEPReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tally:generateTallyVSEPReport {companyName} {emailList}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will generate weekly Tally VS EP Data Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objReport = new tallyGenerateReportController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get command argument 
        $companyName = $this->argument('companyName');
        $emailList = $this->argument('emailList');

        echo "================================================\n";
        echo "Generating the Report ..... \n";
        $this->_objReport->generateTallyVSEPReport($companyName, $emailList);

        echo "================================================\n";

    }
}
