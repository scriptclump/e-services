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
use App\Modules\ScheduleJobs\Models\InventoryReport;
use App\Modules\ScheduleJobs\Models\InventorySnapshot;

class CopyInventoryConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CopyInventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule Job to take snapshot of vw_inventory_report @ 23:00 daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_InventoryReport = new InventoryReport();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allData = json_decode(json_encode($this->_InventoryReport->getAllInventory()), true);
        //echo "<pre>"; print_r($allData);
        echo "\nStart time: ".date('Y-m-d H:i:s')."\n";

        $cnt = 0;        
        foreach($allData as $eachData){
            $this->_InventorySnapshot = new InventorySnapshot();
            $this->_InventorySnapshot->copyInventoryData($eachData);
            $cnt++;
        }
        echo "Total # of records inserted: $cnt \n";
        echo "End time: ".date('Y-m-d H:i:s')."\n";
    }
}
