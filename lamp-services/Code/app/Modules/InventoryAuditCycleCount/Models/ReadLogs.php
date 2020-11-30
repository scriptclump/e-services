<?php
namespace App\Modules\InventoryAuditCycleCount\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;




class ReadLogs extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'excel_upload_logs';
    protected $primaryKey = '_id';
    


    public function readExcelLogsAudit($RefId)
    {

        try {
            
            $htmlData = "";
            $refNumber = (int)$RefId;
            $result = $this->where("uniqueref", "=", $refNumber)->get()->all();
            $data = json_decode($result, true);
            $logData = $data[0]['log_data'];
                // print_r($logData['success']['soherrors']);die;
            // echo sizeof($logData['success']['soherrors']);die;
            // print_r($logData['success']['productId_error']);die;
            
          if(!empty($logData['success']['commenterrors']))
            {
                $logData['success']['commenterrors'] = array_values(array_unique($logData['success']['commenterrors']));
                for($i=0; $i < sizeof($logData['success']['commenterrors']);$i++)
                {
                  $htmlData .=   $logData['success']['commenterrors'][$i];
                }
            }


            if(!empty($logData['success']['productIderrors']))
            {
                $logData['success']['productIderrors'] = array_values(array_unique($logData['success']['productIderrors']));
                for($i=0; $i < sizeof($logData['success']['productIderrors']);$i++)
                {
                  $htmlData .=   $logData['success']['productIderrors'][$i];
                }
            }

            // if(!empty($logData['success']['replanishmenterrors']))
            // {
            //     for($i=0; $i < sizeof($logData['success']['replanishmenterrors']);$i++)
            //     {
            //       $htmlData .=   $logData['success']['replanishmenterrors'][$i];
            //     }
            // }

            // if(!empty($logData['success']['replanishment_uom_errors']))
            // {
            //     for($i=0; $i < sizeof($logData['success']['replanishment_uom_errors']);$i++)
            //     {
            //       $htmlData .=   $logData['success']['replanishment_uom_errors'][$i];
            //     }
            // }

            if(!empty($logData['success']['userserrors']))
            {
                $logData['success']['userserrors'] = array_values(array_unique($logData['success']['userserrors']));
                for($i=0; $i < sizeof($logData['success']['userserrors']);$i++)
                {
                  $htmlData .=   $logData['success']['userserrors'][$i];
                }
            }


            if(!empty($logData['success']['productId_error']))
            {
                $logData['success']['productId_error'] = array_values(array_unique($logData['success']['productId_error']));
                for($i=0; $i < sizeof($logData['success']['productId_error']);$i++)
                {
                  $htmlData .=   $logData['success']['productId_error'][$i];
                }
            }
            if(!empty($logData['success']['wrongCombination']))
            {
                $logData['success']['wrongCombination'] = array_values(array_unique($logData['success']['wrongCombination']));
                for($i=0; $i < sizeof($logData['success']['wrongCombination']);$i++)
                {
                  $htmlData .=   $logData['success']['wrongCombination'][$i];
                }
            }


            echo "<pre>";print_r($htmlData);die;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    
    }
}