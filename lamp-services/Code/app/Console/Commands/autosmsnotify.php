<?php

/**
 *
 * Created By : Rasheed Ahamed Shaik
 * date : 30th Jan 2019
 * Description : Single base controll is used for sending sms/notification/email to their for the users.
 * 
 */

namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\RetailerSMS\Controllers\SMSRetailerController;
use App\Modules\RetailerSMS\Models\SMSRetailer;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\ProductRepo;
use DB;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\InvDataMismatchReports\Controllers\ReportController;
class autosmsnotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autosmsnotify {notification_code} {params?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is used for sending sms/notification to their for the users';

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
    public function handle()
    {
        $arguments = $this->argument();
        $notify_code = $arguments['notification_code'];
        $params = $arguments['params'];
        $smsObj= new SMSRetailer();
        $getMongoDBCaptions = $smsObj->getTableColumnHeadings($notify_code);
        if(!empty($getMongoDBCaptions)){
            $sms_subject = $getMongoDBCaptions['subject'];
            if($params != "" && is_array($params)){
                // if array
                $params = implode(',', $params);
            }else{
                // if already comma seperated
                $params = $params;
            }
            $query = DB::select("CALL ".$getMongoDBCaptions['db_table']."($params)");
            $query_data = json_decode(json_encode($query), 1);
            if(!empty($query_data)){
                $template_varaibles = array_keys($query_data[0]);
                $sms_text_update = $sms_subject;
                foreach ($query_data as $data_value) {
                    foreach ($template_varaibles as $key => $value) {
                        # code...
                        $sms_text_update = str_replace('#'.$value.'#', $data_value[$value], $sms_text_update);
                    }

                    // checking sms flag
                    if(isset($data_value['SEND_SMS']) && $data_value['SEND_SMS'] == 1){
                        if(isset($data_value['MOBILE_NO']) && $data_value['MOBILE_NO']!="" && $sms_text_update!=""){
                            $_sms = new CustomerRepo();
                            $_sms->sendSMS(0, 1,$data_value['MOBILE_NO'] , $sms_text_update,'','','');
                        }
                    }

                    // checking notifcation flag
                    if(isset($data_value['SEND_NOTIFICATION']) && $data_value['SEND_NOTIFICATION'] == 1){
                        if(isset($data_value['PUSH_NOTIFY_USER_ID']) && $data_value['PUSH_NOTIFY_USER_ID']!="" && $sms_text_update!=""){
                            $this->repo = new ProductRepo();
                            $params_data = json_encode(array());
                            $notificationMessage = $sms_text_update;
                            $user_id = $data_value['PUSH_NOTIFY_USER_ID'];
                            $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                            $notifyData = $approvalFlowObj->getRegIds($user_id);
                            $pushNotification = $this->repo->pushNotifications($notificationMessage, $notifyData, "EBUTOR",'Ebutor','','','',$params_data);
                        }
                    }

                    // checking email flag
                    if(isset($data_value['SEND_EMAIL']) && $data_value['SEND_EMAIL'] == 1){
                        if(isset($data_value['EMAIL_ID']) && $data_value['EMAIL_ID']!="" && isset($data_value['EMAIL_TEMPLATE']) && $data_value['EMAIL_TEMPLATE']!=""){
                                $reportsObj = new ReportController();
                                $email_template = $data_value['EMAIL_TEMPLATE'];
                                $email_id = [];
                               $email_id = explode(',', $data_value['EMAIL_ID']);
                                $email_subject = isset($data_value['EMAIL_SUBJECT']) ? $data_value['EMAIL_SUBJECT'] : "Ebutor";
                                $procedure = isset($data_value['EMAIL_PROCEDURE']) ? $data_value['EMAIL_PROCEDURE'] : "";
                                $caption = isset($data_value['EMAIL_CAPTION']) ? $data_value['EMAIL_CAPTION'] : "";
                                $email_border = isset($data_value['EMAIL_BORDER']) ? $data_value['EMAIL_BORDER'] : 1;
                                $reportsObj->sendEmailByCode($caption,$email_subject,$email_id,$email_template,$procedure,$email_border,$options=[]);
                        }
                    }
                }
            }
        }

        return true;
    }
}
