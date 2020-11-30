<?php
/*
  Filename : InventorySnapshot.php
  Author : Ebutor
  CreateData : 16-Sep-2016
  Desc : Model for inventory_snapshot mongodb document
 */
namespace App\Modules\Cpmanager\Models;

date_default_timezone_set("Asia/Kolkata");

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;

class OrderapiLogsModel extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'order_cp_logs';
    protected $primaryKey = '_id';
    public  $timestamps = false;
    
    public function OrderApiRequests($data)
    {
    	try {

    	
            $arrKeys = array_keys($data);

            if(isset($data['apiUrl'])){
                $this->apiUrl = $data['apiUrl'];
            }
            if(isset($data['parameters'])){
                $this->parameters = $data['parameters'];
            }
            
            $this->created_at = date('Y-m-d H:i:s');
            //$this->updated_at = date('Y-m-d H:i:s');
            $this->save();
            
    	} catch (\ErrorException $ex) {
            echo "Error..";
	            Log::info($ex->getMessage());
	            Log::info($ex->getTraceAsString());
        }
    	
    }
}
     