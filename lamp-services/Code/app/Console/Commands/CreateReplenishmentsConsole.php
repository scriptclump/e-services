<?php
/*
  Filename : copyInventoryConsole.php
  Author : Ebutor
  CreateData : 19-Sep-2016
  Desc : Command to copy all Inventory data from 'vw_inventory_report' daily @ 23:00
 */
namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\WarehouseConfig\Models\WmsReplenishmentApiModel;

class CreateReplenishmentsConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateReplenishments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule Job to create replenishments from Bin-Inventory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_replenishment = new WmsReplenishmentApiModel();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            echo "Replenishment Process..\n";
            echo "\nStart time: ".date('Y-m-d H:i:s')."\n";
            $this->_replenishment->reservedReplanishment(109003, 4497);
            $this->_replenishment->reservedReplanishment(109004, 4497);
            
            echo "End time: ".date('Y-m-d H:i:s')."\n";
        } catch  (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            echo $ex;
        }
    }
}
