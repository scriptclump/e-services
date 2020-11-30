<?php
namespace App\Modules\Tax\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;




class ReadLogs extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'excel_upload_logs';
    protected $primaryKey = '_id';
    
    public function readLogs($refnumber)
    {
        try {
                // echo $refid;die;
            $htmldata = "";
            $refnumber = (int)$refnumber;
                $result = $this->where("uniqueref", "=", $refnumber)->get()->all();
                $data = $result;
                // echo "<pre>";print_r($data[0]['log_data']['failed']);die;
                // $explodedata = explode(",", $data[0]['log_data']['failed']);
                // print_r($explodedata);die;
                $failedArray = $data[0]['log_data']['failed'];
                // $size_exploded_data = sizeof($explodedata);
                  $htmldata= $data[0]['log_data']['successcount']." rows inserted <br>";
                $htmldata.= $data[0]['log_data']['updatecount']." rows updated <br>";
                // print_r($failedArray);die;
                if(empty($failedArray))
                {
                    $htmldata .= "0 cells having invalid Data <br>";
                }else
                {
                   for ($i=0; $i < sizeof($failedArray); $i++) { 
                        $htmldata .= $failedArray[$i];
                    }  
                }

                echo $htmldata;
            
        }  catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    }

    public function readMappingLogs($refID)
    {
        try {
            
            $htmlData = "";
            $refNumber = (int)$refID;
            $result = $this->where("uniqueref", "=", $refNumber)->get()->all();
            $data = $result;
            $LOGDATA  = $data[0]['log_data']['success'];
            $htmlData = $data[0]['log_data']['insertcount']." rows inserted <br>";
            if(!empty($LOGDATA['lines']))
            {
                for ($i=0; $i < sizeof($LOGDATA['lines']); $i++) { 
                    $htmlData .= $LOGDATA['lines'][$i];
                }
            }else
            {
               $htmlData .= "0 Columns having invalid TaxClassCode <br>" ;
            }

            if(!empty($LOGDATA['duplicatedata']))
            {
                for ($i=0; $i < sizeof($LOGDATA['duplicatedata']); $i++) { 
                    $htmlData .= $LOGDATA['duplicatedata'][$i];
                }
            }
           // echo "<pre>"; print_r($htmlData);
            echo $htmlData;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    }
    
}