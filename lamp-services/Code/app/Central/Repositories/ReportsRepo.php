<?php
namespace App\Central\Repositories;
use DB;
use App\models\Mongo\MongoMailModel;
use App\Modules\Inventory\Controllers\InventoryController;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use Cache;
use Log;

class ReportsRepo {
    private $_mailMongo = NULL;
    private $filePath = NULL;
    
    public function __construct() {
        $this->_mailMongo = new MongoMailModel();
        $path = realpath(dirname(__FILE__));
        $ds = DIRECTORY_SEPARATOR;
        $pathArray = explode($ds, $path);
        array_pop($pathArray);
        array_pop($pathArray);
        array_pop($pathArray);
        $pathArray = implode($ds, $pathArray);
        $laravelViewPath = $pathArray . $ds . 'resources' . $ds . 'views' . $ds . 'emails';
        $this->filePath = $laravelViewPath;
    }

    public function getSelfOrders($fromDate='',$toDate=''){

        if($fromDate == '')
        {
            $fromDate = date('Y-m-d');
        }
        if($toDate == '')
        {
            $toDate = date('Y-m-d', strtotime(' +1 day'));
        }
        
        $result = DB::select('CALL getSelfOrders("'.$fromDate.'","'.$toDate.'")');
        // $result = json_decode(json_encode($result), true);
        return $result;
    }
    
    public function getMyDashboardData($userId, $fromDate, $toDate, $flag = 1,$buid="",$brandid='NULL',$manufid='NULL',$productgrpid='NULL',$categoryid='NULL')
    {
        try
        {
            $result = [];         
            if($fromDate == '')
            {
                $fromDate = date('Y-m-d');
            }
            if($toDate == '')
            {
                $toDate = date('Y-m-d', strtotime(' +1 day'));
            }
            $CACHE_TAG = "dncDashboard_".\Session::get('legal_entity_id');
            $response = Cache::tags($CACHE_TAG)->get('dasboard_report'.$userId.'_1_'.$fromDate.'_'.$toDate.'_'.$flag.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid,false);
            $last_updated = Cache::tags($CACHE_TAG)->get('dasboard_report'.$userId.'_1_'.$fromDate.'_'.$toDate.'_'.$flag.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid.'_last_updated',false);
           
            if(!$response){
                $date = date('Y-m-d h:i a');
                $response = $this->validateData($userId,$flag, $fromDate, $toDate,$buid,$brandid,$manufid,$productgrpid,$categoryid);
                Cache::tags($CACHE_TAG)->put('dasboard_report'.$userId.'_1_'.$fromDate.'_'.$toDate.'_'.$flag.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid,$response,5);
                Cache::tags($CACHE_TAG)->put('dasboard_report'.$userId.'_1_'.$fromDate.'_'.$toDate.'_'.$flag.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$productgrpid.'_'.$categoryid.'_last_updated',$date,5);
                $last_updated = $date;
            }

            $result['dashboard'] = json_decode(json_encode($response));            
            $result['last_updated'] = $last_updated;
            
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        } // who wrote it this way 
        // what is the use of catch then 
        
        return $result;
    }

    public function getSalesTargetData($fromDate, $toDate, $flag = 0,$dcid=null, $user_id = 0)
    {

        try
        {
            $result = [];         
            if($fromDate == '')
            {
                $fromDate = date('Y-m-d');
            }
            if($toDate == '')
            {
                $toDate = date('Y-m-d', strtotime(' +1 day'));
            }
            $CACHE_TAG = "salesTargetDashboard_".\Session::get('legal_entity_id');
            //$response = Cache::tags($CACHE_TAG)->get('sales_target_report'.$fromDate.'_'.$toDate.'_'.$flag.'_'.$dcid,false);
            $last_updated = Cache::tags($CACHE_TAG)->get('sales_target_report'.$fromDate.'_'.$toDate.'_'.$flag.'_'.$dcid.'_last_updated',false);
           
            //if(!$response){
                $date = date('Y-m-d h:i a');
                $response = $this->salesTargetData($fromDate, $toDate,$flag, $dcid, $user_id);
                Cache::tags($CACHE_TAG)->put('sales_target_report'.$fromDate.'_'.$toDate.'_'.$flag.'_'.$dcid,$response,5);
                Cache::tags($CACHE_TAG)->put('sales_target_report'.$fromDate.'_'.$toDate.'_'.$flag.'_'.$dcid.'_last_updated',$date,5);
                $last_updated = $date;
            //}
            $response = json_decode(json_encode($response));
            //$result['dashboard'] = json_decode(json_encode($response));            
            //$result['last_updated'] = $last_updated;
            
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        } // who wrote it this way 
        // what is the use of catch then 
        
        return $response;
    }
    
    public function sendReport()
    {
        try
        {
            $message = 'No fresh orders to send mail';
            $lastHourOrders = DB::table('gds_orders')
                    ->where('order_date', '>=', DB::raw('DATE_SUB(NOW(),INTERVAL 1 HOUR)'))
                    ->pluck('gds_order_id')->all();
            if(!empty($lastHourOrders))
            {
                $message = $this->sendMail();
            }else{
                \Log::info($message);
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $message;
    }
    
    public function sendMail() {
        try
        {
            $message = 'Unable to send mail';
            $templateName = 'OrderReportDashboard';                
            $templateData = $this->_mailMongo->getMailTemplateByName($templateName);
            if(!empty($templateData))
            {
                $templateInfo = isset($templateData[0]) ? $templateData[0] : [];
                if(!empty($templateInfo))
                {
                    $templateEncodeData = $templateInfo->template;
                    $templateName = $templateInfo->templateName;
                    $templatePath = $this->getMailTemplate($templateEncodeData, $templateName);
                    $fromDate = date('Y-m-d');
                    $datetime = new \DateTime('tomorrow');
                    $toDate = $datetime->format('Y-m-d');
                    $reportData = $this->getMyDashboardData(0, $fromDate, $toDate);
                    $order_details = json_decode(json_encode($reportData), true);
//                    $todayDate = date('Y-m-d_H:i:s');
                    //\Log::info(date_default_timezone_get());
                    date_default_timezone_set('Asia/Kolkata');
                    //\Log::info(date_default_timezone_get());
                    $todayDate = date('Y-m-d_H:i:s');
                    $subject = $templateInfo->subject.' ON '.$todayDate;
                    $fromEmail = $templateInfo->from_email;
                    $fromName = $templateInfo->from_name;
                    $toEmail = $templateInfo->to_email;
                    $ccEmail = explode(',', $templateInfo->cc_emails);
//                        $bccEmail = $templateInfo->bcc_emails;
//                    echo "<pre>";print_R($order_details);die;
                    @\Mail::send(['html' => 'emails.' . $templateName], ['order_details' => $order_details], function ($message)  use ($order_total, $order_details, $subject, $toEmail, $ccEmail, $fromEmail, $fromName)
                    {
                        $message->from($fromEmail, $fromName)
                                ->to($toEmail)
                                ->bcc($ccEmail)
                                ->subject($subject);
                    });
                    $message = 'Mail sent successfully';
                }
            }
        } catch (\ErrorException $ex) {
            $message = $ex->getMessage();
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $message;
    }
    
    public function getMailTemplate($templateView, $templateName) {
        try
        {
            $data = '';
            if (count($templateView) > 0) { //check db for data
                $templateView = base64_decode($templateView);
                // data stored in mongo is base64 encoded
                $templateData = $templateView;
                $data = $this->emailTo($templateName, $templateData);
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $data;
    }
    
    public function emailTo($templateName, $templateData) {
        if (!is_null($templateData)) {
            $ds = DIRECTORY_SEPARATOR;
            $temppath = $this->filePath . $ds . $templateName . '.blade.php';
            $file = fopen($temppath, "w");
            fwrite($file, $templateData);
            fclose($file);
            return $temppath;
        } else {
            return false;
        }
    }
    
    public function sendMailReport()
    {
        try
        {
            \Session::put('userId', 3); 
            \Session::put('roleId', 2); 
            \Session::put('legal_entity_id', 2);
            $templateName = 'InventoryReport';
            $inventory = new InventoryController();
            $request = new Request();
            $input[] = '';
            $request->input(json_encode($input));
            $fileName = $inventory->getExport($request, 1);
            $file = storage_path('Inventory/Exports/'.$fileName);
            $this->sendMails($templateName, $file, $fileName);
            
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    
    public function sendSONotifyMail()
    {
        try
        {
            //\Session::put('userId', 3); 
//            \Session::put('roleId', 2); 
//            \Session::put('legal_entity_id', 2);
            $templateName = 'SONotify';                        
            return $this->sendMails($templateName);
            
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function sendMails($templateName, $file = null, $fileName = null)
    {
        try
        {
            Log::info('call sendmails');
            $templateData = $this->_mailMongo->getMailTemplateByName($templateName);
            if(!empty($templateData))
            {
                $templateInfo = isset($templateData[0]) ? $templateData[0] : [];
                if(!empty($templateInfo))
                {
                    $isActive = property_exists($templateInfo, 'active') ? $templateInfo->active : 0;
                    if(!$isActive)
                    {
                        return;
                    }
                    $sendMail = 1;
                    if($sendMail)
                    {
                        $templateName = property_exists($templateInfo, 'templateName') ? $templateInfo->templateName : '';
                        $templateEncodeData = property_exists($templateInfo, 'template') ? $templateInfo->template : '';
                        if($templateEncodeData != '')
                        {
                            $this->getMailTemplate($templateEncodeData, $templateName);
                        }
                        $toEmails = property_exists($templateInfo, 'to_email') ? $templateInfo->to_email : 'satish.racha@ebutor.com';
                        $subject = property_exists($templateInfo, 'subject') ? $templateInfo->subject : 'Todays OOS Report for last 3 hours';
                        $ccEmail = property_exists($templateInfo, 'cc_emails') ? explode(',', $templateInfo->cc_emails) : [];
                        //\Log::info($ccEmail);
                        if($fileName)
                        {
                            $body = array('template' => 'emails.'.$templateName, 
                                'attachment' => $file,
                                'file_name' => $fileName,
                                'name' => 'Team');
                            date_default_timezone_set('Asia/Kolkata');
                            $fields = array('mailTo'=>$toEmails, 'subject'=>$subject.' ON '.date('Y-m-d_H:i:s'), 'attachment'=>$body['attachment'], 'file_name'=>(isset($body['file_name']) ? $body['file_name'] : ''),'cc_emails' => $ccEmail);
                            //echo "<pre>";print_r($fields);die;
                            $result = \Mail::send($body['template'], array('name'=>$body['name'], 'comment'=>(isset($body['comment']) ? $body['comment'] : '')), function ($message) use ($fields, $fileName) {
                                $message->to($fields['mailTo']);
                                $message->bcc($fields['cc_emails']);
                                $message->subject($fields['subject']);
                                $message->attach($fields['attachment'], 
                                            array('as' => $fileName, 
                                                'mime' => 'application/vnd.ms-excel'));
                            }); 
                            return $result;   
                        }elseif($templateName == 'SONotify'){
                            $frequency = property_exists($templateInfo, 'frequency') ? $templateInfo->frequency : 3;
                            $reportDetails = DB::table('inventory_request')
                                ->leftJoin('products', 'products.product_id', '=', 'inventory_request.product_id')
                                ->where('inventory_request.created_at', '>', DB::raw('DATE_ADD(NOW(), INTERVAL -'.$frequency.' HOUR)'))
                                ->select('products.product_title', DB::raw('SUM(inventory_request.`total_qty`) AS `requested_qty`'),
                                        DB::raw('getLeWhName(inventory_request.`le_wh_id`) as `le_wh_id`'))
                                ->groupBy('inventory_request.product_id')
                                ->get()->all();
                            if(!empty($reportDetails))
                            {
                                $body = array('template' => 'emails.'.$templateName, 'name' => 'Hi Team', 'productList' => $reportDetails);
                                date_default_timezone_set('Asia/Kolkata');
                                $fields = array('mailTo'=>$toEmails, 'subject'=>$subject.' ON '.date('Y_m_d_H_i_s'), 'cc_emails' => $ccEmail);
                                //echo "<pre>";print_r($fields);die;
                                $result = \Mail::send($body['template'], array('name'=>$body['name'], 'productList'=>(isset($body['productList']) ? $body['productList'] : [])), function ($message) use ($fields) {
                                    $message->to($fields['mailTo']);
                                    if(!empty($fields['cc_emails']))
                                    {
                                        $message->bcc($fields['cc_emails']);    
                                    }
                                    $message->subject($fields['subject']);
                                });
                                return $result;    
                            }else{
                                return 'No data to send';   
                            }
                        }else{
                            $body = array('template' => 'emails.'.$templateName, 'name' => 'Team');
                            date_default_timezone_set('Asia/Kolkata');
                            $fields = array('mailTo'=>$toEmails, 'subject'=>$subject.' ON '.date('Y_m_d_H_i_s'), 'cc_emails' => $ccEmail);
                            //echo "<pre>";print_r($fields);die;
                            $result = \Mail::send($body['template'], array('name'=>$body['name'], 'comment'=>(isset($body['comment']) ? $body['comment'] : '')), function ($message) use ($fields) {
                                $message->to($fields['mailTo']);
                                $message->bcc($fields['cc_emails']);
                                $message->subject($fields['subject']);
                            });
                            return $result;
                        }
                    }
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function salesTargetData($fromDate, $toDate, $flag, $dcid ="", $user_id = 0)
    {
        try
        {
            // This is a WEB legal Id, got from SERVER Sessions
             $webLegalEntityId = \Session::get('legal_entity_id');
            if(!empty($mobileLegalEntityId)){
                // If the call is from Mobile, then we update the mobLeId to webLeId
                $webLegalEntityId = $mobileLegalEntityId;
                
            }
            if($dcid==""){
                $roleRepo = new RoleRepo();
                $dcs = $roleRepo->getAllDcs($user_id);
                // If the call is from Mobile, then we update the dcid
                $dcid=isset($dcs[0]->le_wh_id)?$dcs[0]->le_wh_id:"";
            }            
            $tempData = [];
            if($flag==1){
                //$response = DB::selectFromWriteConnection(DB::raw('CALL getCnCDashboard_web(0, 0, "'.$fromDate.'", "'.$toDate.'",'.$dcid.')'));
                $response = DB::select(DB::raw('CALL getDynamicSalesTargets_web("'.$fromDate.'", "'.$toDate.'","'.$dcid.'",1,'.$user_id.')'));

                
            }else{
                // Hiding this Code as the proc is no longer needed!
                // $response = DB::selectFromWriteConnection(DB::raw('CALL getOrdersDashboard_web('.$userId.', '.$flag.', "'.$fromDate.'", "'.$toDate.'")'));
                if($dcid!=""){
                    $checkdchubmapping=$this->checkDcHubMapping($dcid);
                    if(count($checkdchubmapping)>0){
                    $whLegal = $this->getlegalidbasedondcid($dcid);
                    $webLegalEntityId = isset($whLegal->legal_entity_id)?$whLegal->legal_entity_id:\Session::get('legal_entity_id');
                    $response = DB::select(DB::raw('CALL getDynamicSalesTargets_web("'.$fromDate.'", "'.$toDate.'","'.$dcid.'",1,'.$user_id.')'));
                   }else{
                        $response['message'] = 'There is No DC-HUB Mapping,Please Contact Admin';
                     }
                    //Log::info('dashboard-qury');
                    //Log::info('CALL getDynamicDnCDashboard_web('.$userId.', '.$flag.', "'.$fromDate.'", "'.$toDate.'",'.$webLegalEntityId.','.$dcid.')');
                }else{
                    $response['message'] = 'You dont warehouse access';
                }
                //$response = DB::selectFromWriteConnection(DB::raw('CALL getDynamicDnCDashboard_web('.$userId.', '.$flag.', "'.$fromDate.'", "'.$toDate.'",'.$webLegalEntityId.','.$dcid.')'));
            }
            $response = json_decode(json_encode($response),true);
            if(!empty($response) && isset($response[0]['Target_Dashboard']))
            {
                $tempData = json_decode($response[0]['Target_Dashboard'],true);    
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $tempData;
    }
    
    public function validateData($userId, $flag = 1, $fromDate, $toDate,$buid = "",$brandid='NULL',$manufid='NULL',$productgrup='NULL',$categoryid='NULL')
    {
        try
        {            
            $tempData = [];
            if($flag==5){
                $response = DB::select(DB::raw('CALL getCnCDashboard_web(0, 0, "'.$fromDate.'", "'.$toDate.'")'));

            }else{
               // echo 'CALL getDynamicDnCDashboardByBU_web('.$userId.', '.$flag.', "'.$fromDate.'", "'.$toDate.'",'.$buid.','.$brandid.','.$manufid.','.$productgrup.','.$categoryid.')';exit;
                // Hiding this Code as the proc is no longer needed!
                $data = DB::select("call getBuHierarchy_proc($buid,@le_wh_ids)");
                $data =DB::select(DB::raw('select @le_wh_ids as wh_list'));
                $data=explode(',',$data[0]->wh_list);
                //echo count($data);exit;
                if($buid!="" && count($data)>0){
                    $response = DB::select(DB::raw('CALL getDynamicDnCDashboardByBU_web('.$userId.', '.$flag.', "'.$fromDate.'", "'.$toDate.'",'.$buid.','.$brandid.','.$manufid.','.$productgrup.','.$categoryid.')'));
                }else{
                    $response['message'] = 'You dont warehouse access';
                }
            }
            $response = json_decode(json_encode($response),true);
            if(!empty($response) && isset($response[0]['Dashboard']))
            {
                $tempData = json_decode($response[0]['Dashboard'],true);    
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $tempData;
    }
    public function getlegalidbasedondcid($dcid){
        $legalid = DB::table('legalentity_warehouses')
                    ->select('legal_entity_id','bu_id')
                    ->where('le_wh_id', $dcid)
                    ->first();
        return $legalid;
    }

    public function checkDcHubMapping($dcid){

        $activehubs=DB::table('dc_hub_mapping')
                    ->select('is_active')
                    ->where('dc_id',$dcid)
                    ->where('is_active',1)
                    ->get()->all();

        return $activehubs;

    }

    public function getdcidbasedonbuid($buid){
        $legalid = DB::table('legalentity_warehouses')
                    ->select('legal_entity_id','le_wh_id')
                    ->where('dc_type',118001)
                    ->where('bu_id', $buid)
                    ->first();
        return $legalid;
    }
}
