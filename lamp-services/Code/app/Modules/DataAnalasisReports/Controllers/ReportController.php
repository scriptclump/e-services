<?php
namespace App\Modules\DataAnalasisReports\Controllers;

use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mail;
use DB;
use Log;
use Config;
use App\Http\Controllers\BaseController;
use \App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\DataAnalasisReports\Models\DataReportsModel;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

class ReportController {
    
    public function index()
    {
        try
        {
            DB::enablequerylog();
            $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $userIdData= $notificationObj->getUsersByCode('DAR0001');
            $userIdData=json_decode(json_encode($userIdData));
            $subject=$notificationObj->getMessageByCode('DAR0001');
            $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get()->all();
            $emails=json_decode(json_encode($data,1),true);
            $getEmails=array();
            foreach ($emails as $keyValue )
            {
                $getEmails[]=$keyValue['email_id'];
            }
            $dataReportObj= new DataReportsModel();
            $tableColumnArray= array();
            $tableCaptionsArray =array();
            $tableData= array();
            $getMongoDBCaptions=$dataReportObj->getTableColumnHeadings();
            if(!empty($getMongoDBCaptions))
            {
                $tableCaptionsArray=array_merge($tableCaptionsArray,$getMongoDBCaptions[0]);
            }
            $getMongoDBViewData=$dataReportObj->getTableViewData();  
            if(!empty($getMongoDBViewData))
            {
                foreach ($getMongoDBViewData[0] as $viewKey)
                {
                        $rs= DB::select("select * from ".$viewKey);
                        $viewData[]= json_decode(json_encode($rs),1);
                        $viewColumns[]=DB::getSchemaBuilder()->getColumnListing($viewKey);
                }
                $tableData= array_merge($tableData,$viewData);
                $tableColumnArray= array_merge($tableColumnArray,$viewColumns);
            }
            $count=0;
            $size=sizeof($tableCaptionsArray);
            $subject=$subject.'('.date('d-M-Y').')';
            // $body = array('template'=>'emails.DataMismatchReports', 'attachment'=>'', 'name'=>'Hi Team', 'tableData'=>$tableData,'colunmNames'=>$tableColumnArray,"TableCaptions"=>$tableCaptionsArray,'count'=>$count,'size'=>$size);

            // Utility::sendEmail($getEmails, $subject, $body);

            $rss=Mail::send('emails.DataMismatchReports',['name' =>"Hi Team",'tableData'=>$tableData,'colunmNames'=>$tableColumnArray,"TableCaptions"=>$tableCaptionsArray,'count'=>$count,'size'=>$size], function ($m) use ($getEmails, $subject)
            {
                $m->to($getEmails, "Hi Team")->subject($subject.'('.date('d-M-Y').')');
            }); 
            print_r($rss); die();
           
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
}
