<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Modules\Pricing\Controllers\pricingupdateCURLController;

class pricingupdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "pricing:pricingupdate {updateDate=default}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pricing update command, this will update the product_prices table as per the current date';

    private $_objPricingUpdate='';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_objPricingUpdate = new pricingupdateCURLController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get command argument 
        $updateDate = $this->argument('updateDate');
        if($updateDate == 'default'){
            $updateDate = date('Y-m-d');
        }
        
        echo "================================================\n";
        echo "Started pricingupdate With latest date ..... \n\n";
        $this->_objPricingUpdate->pricingUpdateWithUpdateDate($updateDate);
        echo "\nProcess completed ! \n\n";
        echo "================================================\n";
    }
}
