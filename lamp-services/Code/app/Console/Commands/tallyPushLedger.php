<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\TallyConnector\Controllers\tallyTransactionController;

class tallyPushLedger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tally:pushLedger {companyName} {emailList}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will push all the supplier and Customer to Tally';

    /**
     * Create a global object to get the process.
     *
     * @var Object
     */
    private $_objLedger='';    

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objLedger = new tallyTransactionController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
        //10.175.8.79

        // Get command argument 
        $companyName = $this->argument('companyName');
        $emailList = $this->argument('emailList');
        
        echo "Starting Tally Import ..... \n\n";

        echo "================================================\n";
        echo "Importing Tally Sundry Debtors (Supplier)\n";
        $this->_objLedger->pushTallyLedgerDebtors($companyName);
        echo "Sundry Debtors Imported\n\n";

        echo "================================================\n";
        echo "Importing Tally Sundry Creditors (Customer)\n";
        $this->_objLedger->pushTallyLedgerCreditors($companyName);
        echo "Sundry Creditors Imported\n\n";

        echo "================================================\n";
        echo "Sending mail\n";
        $this->_objLedger->sendMailForLedger($emailList);
        echo "Cron Process completed\n\n";
        echo "================================================\n";
    }
}
