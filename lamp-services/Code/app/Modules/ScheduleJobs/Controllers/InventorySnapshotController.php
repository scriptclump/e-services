<?php
namespace App\Modules\ScheduleJobs\Controllers;
date_default_timezone_set("Asia/Kolkata");

use App\Http\Controllers\BaseController;
use View;
use Log;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use App\Modules\ScheduleJobs\Models\InventoryReport;
use App\Modules\ScheduleJobs\Models\InventorySnapshot;
use Illuminate\Support\Facades\Config;

class InventorySnapshotController extends BaseController {
    
    public function __construct() {
        $this->_InventoryReport = new InventoryReport();
        //$this->_InventorySnapshot = new InventorySnapshot();
    }

    public function index() {
        $allData = json_decode(json_encode($this->_InventoryReport->getAllInventory()), true);
        //echo "<pre>"; print_r($allData);
        echo "Start time: ".date('Y-m-d H:i:s')."<br>";

        $cnt = 0;        
        foreach($allData as $eachData){
            $this->_InventorySnapshot = new InventorySnapshot();
            $this->_InventorySnapshot->copyInventoryData($eachData);
            $cnt++;
        }
        echo "Total # of records inserted: $cnt <br>";
        echo "End time: ".date('Y-m-d H:i:s')."<br>";
    }
}