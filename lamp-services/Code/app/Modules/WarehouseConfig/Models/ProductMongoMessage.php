<?php
namespace App\Modules\WarehouseConfig\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;

class ProductMongoMessage extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'excel_upload_logs';
    protected $primaryKey = '_id';
    
    public function readMappingLogs($refID)
    {
        try {      
            $htmlData = "";
            $refNumber = (int)$refID;
            $result = $this->where("uniqueref", "=", $refNumber)->get()->all();            
            $data = $result;
            $LOGDATA  = (isset($data[0]['log_data']))?$data[0]['log_data']:array();            
            foreach($LOGDATA as $data)
            {
               $htmlData .= $data."<br>" ;
            }
            echo "<pre>"; print_r($htmlData);
            //echo $htmlData;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    }
}
     