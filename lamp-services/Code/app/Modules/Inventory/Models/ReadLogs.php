<?php
namespace App\Modules\Inventory\Models;

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
                $data = $result;//json_decode($result, true);
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
            $data = $result;//json_decode($result, true);
            $LOGDATA  = $data[0]['log_data']['success'];
            $htmlData = $data[0]['log_data']['insertcount']." rows inserted <br>";
            if(!empty($LOGDATA['lines']))
            {
                for ($i=0; $i < sizeof($LOGDATA['lines']); $i++) { 
                    $htmlData .= $LOGDATA['lines'][$i];
                }
            }else
            {
               $htmlData .= "0 Columns having invalid TaxClassCode" ;
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


    public function readExcelLogs($RefId)
    {
        try {
            
            $htmlData = "";
            $refNumber = (int)$RefId;
            $result = $this->where("uniqueref", "=", $refNumber)->get()->all();
            $data = $result;//json_decode($result, true);
            $logData = $data[0]['log_data'];

                // print_r($logData['success']['soherrors']);die;
            // echo sizeof($logData['success']['soherrors']);die;
            if(!empty($logData['success']['soherrors']))
            {
                for($i=0; $i < sizeof($logData['success']['soherrors']);$i++)
                {
                  $htmlData .=   $logData['success']['soherrors'][$i];
                }
            }
            
          if(!empty($logData['success']['commenterrors']))
            {
                for($i=0; $i < sizeof($logData['success']['commenterrors']);$i++)
                {
                  $htmlData .=   $logData['success']['commenterrors'][$i];
                }
            }

            if(!empty($logData['success']['wrongCombination']))
            {
                for($i=0; $i < sizeof($logData['success']['wrongCombination']);$i++)
                {
                  $htmlData .=   $logData['success']['wrongCombination'][$i];
                }
            }

            if(!empty($logData['success']['productIderrors']))
            {
                for($i=0; $i < sizeof($logData['success']['productIderrors']);$i++)
                {
                  $htmlData .=   $logData['success']['productIderrors'][$i];
                }
            }

            if(!empty($logData['success']['reasonerrors']))
            {
                for($i=0; $i < sizeof($logData['success']['reasonerrors']);$i++)
                {
                  $htmlData .=   $logData['success']['reasonerrors'][$i];
                }
            }

            if(!empty($logData['success']['reason_mismatch_errors']))
            {
                for($i=0; $i < sizeof($logData['success']['reason_mismatch_errors']);$i++)
                {
                  $htmlData .=   $logData['success']['reason_mismatch_errors'][$i];
                }
            }

            if(!empty($logData['success']['atperrors']))
            {
                for($i=0; $i < sizeof($logData['success']['atperrors']);$i++)
                {
                  $htmlData .=   $logData['success']['atperrors'][$i];
                }
            }

            if(!empty($logData['success']['diterrors']))
            {
                for($i=0; $i < sizeof($logData['success']['diterrors']);$i++)
                {
                  $htmlData .=   $logData['success']['diterrors'][$i];
                }
            }

            if(!empty($logData['success']['dnderrors']))
            {
                for($i=0; $i < sizeof($logData['success']['dnderrors']);$i++)
                {
                  $htmlData .=   $logData['success']['dnderrors'][$i];
                }
            }

            if(!empty($logData['success']['samerecords']))
            {
                for($i=0; $i < sizeof($logData['success']['samerecords']);$i++)
                {
                  $htmlData .=   $logData['success']['samerecords'][$i];
                }
            }
            if(!empty($logData['success']['negative_values']))
            {

                for($i=0; $i < sizeof($logData['success']['negative_values']);$i++)
                {
                  $htmlData .=   $logData['success']['negative_values'][$i].'<br>';
                }
            }
            if(!empty($logData['success']['elpespnotFound']) && isset($logData['success']['elpespnotFound']))
            {

                for($i=0; $i < sizeof($logData['success']['elpespnotFound']);$i++)
                {
                  $htmlData .=   $logData['success']['elpespnotFound'][$i].'<br>';
                }
            }
            if(isset($logData['success']['negativesoh']) && !empty($logData['success']['negativesoh']))
            {

                for($i=0; $i < sizeof($logData['success']['negativesoh']);$i++)
                {
                    if(isset($logData['success']['negativesoh'][$i]['message'])){  
                        $htmlData .=   $logData['success']['negativesoh'][$i]['message'].'<br>';
                    }else{
                        $htmlData .=   $logData['success']['negativesoh'][$i].'<br>';
                    }
                }
            }

            echo "<pre>";print_r($htmlData);die;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    }

    public function readExcelLogsReplanishment($RefId)
    {

        try {
            
            $htmlData = "";
            $refNumber = (int)$RefId;
            $result = $this->where("uniqueref", "=", $refNumber)->get()->all();
            $data = $result;//json_decode($result, true);
            $logData = $data[0]['log_data'];
                // print_r($logData['success']['soherrors']);die;
            // echo sizeof($logData['success']['soherrors']);die;

            
          if(!empty($logData['success']['commenterrors']))
            {
                for($i=0; $i < sizeof($logData['success']['commenterrors']);$i++)
                {
                  $htmlData .=   $logData['success']['commenterrors'][$i];
                }
            }


            if(!empty($logData['success']['productIderrors']))
            {
                for($i=0; $i < sizeof($logData['success']['productIderrors']);$i++)
                {
                  $htmlData .=   $logData['success']['productIderrors'][$i];
                }
            }

            if(!empty($logData['success']['replanishmenterrors']))
            {
                for($i=0; $i < sizeof($logData['success']['replanishmenterrors']);$i++)
                {
                  $htmlData .=   $logData['success']['replanishmenterrors'][$i];
                }
            }

            if(!empty($logData['success']['replanishment_uom_errors']))
            {
                for($i=0; $i < sizeof($logData['success']['replanishment_uom_errors']);$i++)
                {
                  $htmlData .=   $logData['success']['replanishment_uom_errors'][$i];
                }
            }

            if(!empty($logData['success']['reason_mismatch_errors']))
            {
                for($i=0; $i < sizeof($logData['success']['reason_mismatch_errors']);$i++)
                {
                  $htmlData .=   $logData['success']['reason_mismatch_errors'][$i];
                }
            }

            if(!empty($logData['success']['wrongCombination']))
            {
                for($i=0; $i < sizeof($logData['success']['wrongCombination']);$i++)
                {
                  $htmlData .=   $logData['success']['wrongCombination'][$i];
                }
            }

             if(!empty($logData['success']['samerecords']))
            {
                for($i=0; $i < sizeof($logData['success']['samerecords']);$i++)
                {
                  $htmlData .=   $logData['success']['samerecords'][$i];
                }
            }

            echo "<pre>";print_r($htmlData);die;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    
    }
}