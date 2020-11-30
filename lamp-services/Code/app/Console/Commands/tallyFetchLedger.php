<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\TallyConnector\Controllers\tallyFetchLedgerCURLController;

class tallyFetchLedger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tally:fetchLedger {companyName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fetchs all the ledger master data from Tally';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objFetchLedger = new tallyFetchLedgerCURLController();
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
        
        echo "================================================\n";
        echo "Started Feathing Tally Ledger ..... \n\n";
        print_r($this->_objFetchLedger->fetchAndStoreTallyLedger($companyName));
        echo "\nProcess completed ! \n\n";
        echo "================================================\n";
    }
}
