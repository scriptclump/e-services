<?php
namespace App\Modules\RetailerSMS\Controllers;

use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Log;
use Config;
use App\Http\Controllers\BaseController;
use \App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\RetailerSMS\Models\SMSRetailer;

use App\Central\Repositories\CustomerRepo;

use Illuminate\Support\Facades\Route;

class SMSRetailerController {
    
    public function index($notify_code,$mongo_template='RetailerSMS')
    {
        try
        {
          
            DB::enablequerylog();
            $notificationObj= new NotificationsModel();
            $smsretailerObj= new SMSRetailer();
            $tableColumnArray= array();
            $tableCaptionsArray =array();
            $tableData= array();
            $getMongoDBCaptions=$smsretailerObj->getTableColumnHeadings($notify_code);
            $static_key_array = ["#TOMMOROW#","#FROMMONTHNAME#","#NEXT2DAY#","#TOMONTHNAME#","#TODAY#"];
            $static_value_array = [];
            $datetime = new \DateTime('tomorrow');
            $todayDate = new \DateTime('today');
            $today = $todayDate->format('D');
            $tommow = $datetime->format('D');
            if($tommow == "Sun"){
                $datetime = new \DateTime('tomorrow + 1day');
                $tommow = $datetime->format('dS');
            }else{
                $tommow = $datetime->format('dS');
            }
            if($today == "Sun"){
                $todayDate = new \DateTime('today + 1day');
                $today = $todayDate->format('dS');
            }else{
                $today = $todayDate->format('dS');
            }
            $tommow = $datetime->format('dS');
            $today = $todayDate->format('dS');
            $monthname = $datetime->format('M');
            //$currentTimeStamp = date('H:i:s');

            $next2day = date('D', strtotime("+2 days"));
            if($next2day == "Sun"){
                $next2day = date('dS', strtotime("+3 days"));
                $nextmonthname = date('M', strtotime("+3 days"));
            }else{
                $next2day = date('dS', strtotime("+2 days"));
                $nextmonthname = date('M', strtotime("+2 days"));
            }

            
            if(!empty($getMongoDBCaptions))
            {
                $sms_subject = $getMongoDBCaptions['subject'];

                $sms_subject = str_replace("#TOMMOROW#", $tommow, $sms_subject);
                $sms_subject = str_replace("#FROMMONTHNAME#", $monthname, $sms_subject);
                $sms_subject = str_replace("#NEXT2DAY#", $next2day, $sms_subject);
                $sms_subject = str_replace("#TOMONTHNAME#", $nextmonthname, $sms_subject);
                $sms_subject = str_replace("#TODAY#", $today, $sms_subject);

                $query = DB::select("CALL ".$getMongoDBCaptions['db_table']."()");
                $query_data = json_decode(json_encode($query), 1);

                if(!empty($query_data)){
                    foreach ($query_data as $data_value) {
                     //   $dynamic_key_array = array();
                        $sms_text_update = $sms_subject;                        
                     //   $dynamic_key_array = array();
                        $sms_text_update = str_replace("#RETAILERNAME#", $data_value['RETAILERNAME'], $sms_text_update);
                        $sms_text_update = str_replace("#SALESPERSON#", $data_value['SALESPERSON'], $sms_text_update);
                        $sms_text_update = str_replace("#SALESPERSONMOBILE#", $data_value['SALESPERSONMOBILE'], $sms_text_update);
                        $sms_text_update = str_replace("#RETAILERMOBILE#", $data_value['RETAILERMOBILE'], $sms_text_update);
                      if(isset($data_value['RETAILERMOBILE']) && $data_value['RETAILERMOBILE']!="" && $sms_text_update!=""){
                            //echo $data_value['RETAILERMOBILE']."-----".$sms_text_update;
                            $_sms = new CustomerRepo();
                            $_sms->sendSMS(0, 1,$data_value['RETAILERMOBILE'] , $sms_text_update,'','','');
                        }
                        //array_push($subject_dynamic_array, $subject_concate);
                       // array_push($mobile_array, $data_value['RETAILERMOBILE'] );
                    }
                }               
            } 
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
   
}
