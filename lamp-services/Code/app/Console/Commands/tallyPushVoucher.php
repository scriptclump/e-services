<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\TallyConnector\Controllers\tallyPushVoucherController;

class tallyPushVoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tally:pushVoucher {companyName} {emailList}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will push all the Vouchers to Tally';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objVoucher = new tallyPushVoucherController();
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
        
        echo "Starting Tally Vouchers Import ..... \n\n";
        echo "================================================\n";
        echo "Importing Tally Vouchers\n";
        $this->_objVoucher->pushTallyVouchers($companyName);
        echo "Tally Vouchers imported\n\n";
        echo "================================================\n";
        echo "Sending mail\n";
        $this->_objVoucher->sendMailForVoucher($emailList);
        echo "Cron Process completed\n\n";
        echo "================================================\n";
    }
}
