<?php
/*
  Filename  : inventoryReportConsole.php
  Author    : Ebutor
  Date      : 5-Jan-2018
  Desc      : Console to generate and email inventory Report to requested user
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Session;

use App\Modules\Inventory\Controllers\InventoryController;
use App\Lib\Queue;

class InventoryReportConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InventoryReport {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console to generate and email Inventory Report to requested user';

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
    public function handle(){

        $arguments = $this->argument();
        $data = base64_decode($arguments['data']);
        //$this->info("Parameters ".$data);
        $data = json_decode($data, true);

        \Session::put('userId', $data['userId']);
        $inventory = new InventoryController();
        $getproductInfo = $data['getproductInfo'];
        $decoded_input = $data['decoded_input'];
        $getallinventory = $data['getallinventory'];
        $filters = $data['filters'];
        $productid = $data['productid'];
        $userName = $data['userName'];
        $userId = $data['userId'];
        $this->info("getproductInfo ");
        $result = $inventory->createExcelBkground($userName,$userId,$getproductInfo, $decoded_input, $getallinventory, $filters, $productid);
        
        $this->info("Report Email Sent to ".$result);
        return true;      
        
    }

}
