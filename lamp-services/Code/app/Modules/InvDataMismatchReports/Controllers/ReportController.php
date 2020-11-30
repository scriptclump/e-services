<?php
namespace App\Modules\InvDataMismatchReports\Controllers;

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
use App\Modules\InvDataMismatchReports\Models\DataReportsModel;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Excel;
use File;
//use Utility;

class ReportController {
    public function index($notify_code='IDMR0001',$mongo_template='InvDataMismatchReports',$options=[])
    {
        try
        {
            DB::enablequerylog();
            $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $userIdData= $notificationObj->getUsersByCode($notify_code);
            $userIdData=json_decode(json_encode($userIdData),true);
            $subject=$notificationObj->getMessageByCode($notify_code);
            $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get()->all();
            $emails=json_decode(json_encode($data,1),true);
            $getEmails=array();
            foreach ($emails as $keyValue )
            {
                $getEmails[]=$keyValue['email_id'];
            }
//            Log::info('ELP change user emails');
  //          Log::info($getEmails);
            $dataReportObj= new DataReportsModel();
            $tableColumnArray= array();
            $tableCaptionsArray =array();
            $tableData= array();

            $getMongoDBCaptions=$dataReportObj->getTableColumnHeadings($mongo_template);
            if(!empty($getMongoDBCaptions))
            {
                $tableCaptionsArray=array_merge($tableCaptionsArray,$getMongoDBCaptions[0]);
            }
            $getMongoDBViewData=$dataReportObj->getTableViewData($mongo_template);
            $getMongoDBViewSummaryData=$dataReportObj->getTableViewSummaryData($mongo_template);
            $conditions = [];
            $is_attach = 0;
            $is_summary = 0;
            if(count($options)>0){
                $conditions = isset($options['conditions'])?$options['conditions']:[];
                $is_attach = isset($options['is_attach'])?$options['is_attach']:0;
                $is_summary = isset($options['is_summary'])?$options['is_summary']:0;
                
            }
            if(!empty($getMongoDBViewData))
            {
                foreach ($getMongoDBViewData[0] as $viewKey)
                {
                    $query ="select * from ".$viewKey;
                    if(count($conditions)>0){
                        $query .= ' where ';
                        foreach ($conditions as $condition) {
                            if ($viewKey == $condition['vw_name']) {
                                $query .= $condition['column'].'='. $condition['val'];
                            }
                        }
                    }
                    $rs= DB::selectFromWriteConnection(DB::raw($query));
                    $view_data = json_decode(json_encode($rs),1);
                    $viewData[]= $view_data;
                    $viewKeys = isset($view_data[0])?array_keys($view_data[0]):[];
                    $viewColumns[]=$viewKeys;//DB::getSchemaBuilder()->getColumnListing($viewKey);
                }
                $tableData= array_merge($tableData,$viewData);
                $tableColumnArray= array_merge($tableColumnArray,$viewColumns);
            }
            $count=0;
            $size=sizeof($tableCaptionsArray);
            $fileName = time().'_'.date('d-M-Y');
            $filePath = public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xls";
            if($is_attach==1){              
                Excel::create($fileName, function($excel) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size) {
                    $excel->sheet('Sheet1',function($sheet) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size) {
                            $sheet->loadView('emails.DataMismatchReports')
                                    ->with('name',"Hi Team")
                                    ->with('tableData',$tableData)
                                    ->with('colunmNames',$tableColumnArray)
                                    ->with("TableCaptions",$tableCaptionsArray)
                                    ->with('count',$count)
                                    ->with('toexcel',1)
                                    ->with('size',$size);
                        });
                })->store('xls', public_path('download'));
                if(isset($tableData[0]) && count($tableData[0])>0){
                    $rss=Mail::send('emails.po',['name' =>"Hi Team",'comment'=>'Please find attached file'], function ($m) use ($getEmails, $subject,$filePath)
                    {
                        $m->to($getEmails, "Hi Team")->subject($subject.'('.date('d-M-Y').')');
                        $m->attach($filePath);
                    });
                    File::delete($filePath);
                    print_r($rss); die();
                }else{
                    echo 'No data found';
                }
            }else {
                if(isset($tableData[0]) && count($tableData[0])>0){
                    $exceldata=array();
                
                if(!empty($getMongoDBViewSummaryData) && $is_summary==1) {
                   
                   $excelViewName = (isset($getMongoDBViewSummaryData[0][0]) && $getMongoDBViewSummaryData[0][0] != "") ? $getMongoDBViewSummaryData[0][0] : "";
                    
                    if($excelViewName != ""){
                        $exceldata = DB::select(DB::raw("select * from ".$excelViewName));
                        $exceldata = json_decode(json_encode($exceldata),1);
                        //$fileName = time().'_'.date('d-M-Y');
                        Excel::create($fileName, function($excel) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size,$exceldata) {
                            $excel->sheet('Sheet1',function($sheet) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size,$exceldata) {
                                    $sheet->fromArray($exceldata);
                                });
                        })->store('xls', public_path('download'));                        
                    }

                }
                log::info('inentorydatamismatch');
                log::info($getEmails);
                $body=array('template'=>'emails.DataMismatchReports','attachment'=>$filePath,'name' =>"Hi Team",'tableData'=>$tableData,'colunmNames'=>$tableColumnArray,"TableCaptions"=>$tableCaptionsArray,'count'=>$count,'size'=>$size);
                $subject=$subject.'('.date('d-M-Y').')';

               // $rss=Utility::sendEmail($getEmails,$subject,$body);

                $rss=Mail::send('emails.DataMismatchReports',['name' =>"Hi Team",'tableData'=>$tableData,'colunmNames'=>$tableColumnArray,"TableCaptions"=>$tableCaptionsArray,'count'=>$count,'size'=>$size], function ($m) use ($getEmails, $subject,$exceldata,$filePath)
                    {
                        $m->to($getEmails, "Hi Team")->subject($subject.'('.date('d-M-Y').')');
                        if(count($exceldata))
                        $m->attach($filePath);
                    });
                    File::delete($filePath);
                    print_r($rss); die();
                }else{
                    echo 'No data found';
                }
            }
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage().' '.$ex->getTraceAsString();
        }
    }

    public function sendEmailByCode($email_subject,$caption,$email_id,$mongo_template,$procedure,$email_border=1,$options=[])
    {
        try
        {
            DB::enablequerylog();
            $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $subject=$email_subject;
            $emails=$email_id;
            $getEmails=array();
            foreach ($emails as $keyValue )
            {
                $getEmails[]=$keyValue;
            }
            $dataReportObj= new DataReportsModel();
            $tableColumnArray= array();
            $tableCaptionsArray[] = $caption;
            $tableData= array();
            $getMongoDBCaptions=$dataReportObj->getTableColumnHeadings($mongo_template);
            $getMongoDBViewData=$dataReportObj->getTableViewData($mongo_template);
            $conditions = [];
            $is_attach = 0;
            if(count($options)>0){
                $conditions = isset($options['conditions'])?$options['conditions']:[];
                $is_attach = isset($options['is_attach'])?$options['is_attach']:0;
            }  
            if($procedure != ""){
                $query = "CALL ".$procedure;
                $rs= DB::selectFromWriteConnection(DB::raw($query));
                $viewData[]= json_decode(json_encode($rs),1);
                $procedureColumns[] = array_keys(json_decode(json_encode($rs[0]),1));
                $tableData= array_merge($tableData,$viewData);
                $tableColumnArray= array_merge($tableColumnArray,$procedureColumns);
            }
            $count=0;
            $size=sizeof($tableCaptionsArray);
            if($is_attach==1){
                $fileName = time().'_'.date('d-M-Y');
                Excel::create($fileName, function($excel) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size) {
                    $excel->sheet('Sheet1',function($sheet) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size) {
                            $sheet->loadView('emails.DataMismatchReports')
                                    ->with('name',"Hi")
                                    ->with('tableData',$tableData)
                                    ->with('colunmNames',$tableColumnArray)
                                    ->with("TableCaptions",$tableCaptionsArray)
                                    ->with('count',$count)
                                    ->with('toexcel',1)
                                    ->with('size',$size);
                        });
                })->store('xls', public_path('download'));
                $filePath = public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xls";
                if(isset($tableData[0]) && count($tableData[0])>0){
                    $rss=Mail::send('emails.po',['name' =>"Hi",'comment'=>'Please find attached file'], function ($m) use ($getEmails, $subject,$filePath)
                    {
                        $m->to($getEmails, "Hi")->subject($subject.'('.date('d-M-Y').')');
                        //$m->bcc("satish.racha@ebutor.com", "Hi Team")->subject($subject.'('.date('d-M-Y').')');
                        $m->attach($filePath);
                    });
                    File::delete($filePath);
                    print_r($rss);
                }else{
                    echo 'No data found';
                }
            }else {
                if(isset($tableData[0]) && count($tableData[0])>0){
                    $rss=Mail::send('emails.dynamictemailtemplate',['name' =>"Hi",'tableData'=>$tableData,'colunmNames'=>$tableColumnArray,"TableCaptions"=>$tableCaptionsArray,'count'=>$count,'size'=>$size,"email_border"=>$email_border], function ($m) use ($getEmails, $subject)
                    {
                        $m->to($getEmails, "Hi")->subject($subject.'('.date('d-M-Y').')');
                        //$m->bcc("satish.racha@ebutor.com", "Hi Team")->subject($subject.'('.date('d-M-Y').')');
                    });
                    print_r($rss);
                }else{
                    echo 'No data found';
                }
            }
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage().' '.$ex->getTraceAsString();
        }
    }

    public function sendEmailByMultipleExcel($notify_code='RETORD001')
    {
        try
        {
            $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $userIdData= $notificationObj->getUsersByCode($notify_code);
            $userIdData=json_decode(json_encode($userIdData),true);
            $subject=$notificationObj->getMessageByCode($notify_code);
            $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get();
            $emails=json_decode(json_encode($data,1),true);
            $getEmails=array();
            foreach ($emails as $keyValue )
            {
                $getEmails[]=$keyValue['email_id'];
            } 
            
           Excel::create('RetailersQuarterlyDetailReport('.date('Y-M-d').')', function($excel) 
                {
                    $stateList = DB::table('zone')->where(['country_id'=>'99'])->pluck('name','zone_id');
                    $excel->setTitle('Retailers Quarterly Reports........................');
                    foreach($stateList as $stateid=>$value)
                    {
                        $responeData=DB::select("call getRetailerQuarterlyDetails($stateid)");
                        $responeData= json_decode(json_encode($responeData), true);
                        $excel->sheet($value, function($sheet) use ($responeData)
                        {
                            $sheet->fromArray($responeData);
                        });
                    }   
                   
                })->store('xlsx', storage_path());
                $rss=Mail::send('emails.retailerQuarterlyDetails', ['name' =>"Hi",'body'=>"Please find retailers quarterly report."], function ($m) use($getEmails, $subject) 
                {
                    $m->attach(storage_path().'/RetailersQuarterlyDetailReport('.date('Y-M-d').').xlsx');
                    $m->to($getEmails, "Hi")->subject($subject.'('.date('d-M-Y').')');
                });
                echo "Retailers Quarterly Report email sent"; 
              
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage().' '.$ex->getTraceAsString();
        }
    }

    public function sendEmailByProcedure($notify_code='IDMR0001',$mongo_template='InvDataMismatchReports',$options=[])
    {
        try
        {
            $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $userIdData= $notificationObj->getUsersByCode($notify_code);
            $userIdData=json_decode(json_encode($userIdData),true);
            $subject=$notificationObj->getMessageByCode($notify_code);
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
            $getMongoDBCaptions=$dataReportObj->getTableColumnHeadings($mongo_template);
            if(!empty($getMongoDBCaptions))
            {
                $tableCaptionsArray=array_merge($tableCaptionsArray,$getMongoDBCaptions[0]);
            }
            $getMongoDBProcedureData=$dataReportObj->getTableProcedureData($mongo_template);
            $getMongoDBProcedureSummaryData=$dataReportObj->getTableProcedureSummaryData($mongo_template);
            $conditions = [];
            $is_attach = 0;
            $is_summary = 0;
            if(count($options)>0){
                $conditions = isset($options['conditions'])?$options['conditions']:[];
                $is_attach = isset($options['is_attach'])?$options['is_attach']:0;
                $is_summary = isset($options['is_summary'])?$options['is_summary']:0;
                
            }
            if(!empty($getMongoDBProcedureData))
            {
                foreach ($getMongoDBProcedureData[0] as $procedureKey)
                {
                    $query ="CALL ".$procedureKey;
                    if(count($conditions)>0){
                        $query .= ' where ';
                        foreach ($conditions as $condition) {
                            if ($procedureKey == $condition['procedure_name']) {
                                $query .= $condition['column'].'='. $condition['val'];
                            }
                        }
                    }
                    $rs= DB::selectFromWriteConnection(DB::raw($query));
                    $procedure_data = json_decode(json_encode($rs),1);
                    $procedureData[]= $procedure_data;
                    $procedureKeys = isset($procedure_data[0])?array_keys($procedure_data[0]):[];
                    $procedureColumns[]=$procedureKeys;//DB::getSchemaBuilder()->getColumnListing($viewKey);
                }
                $tableData= array_merge($tableData,$procedureData);
                $tableColumnArray= array_merge($tableColumnArray,$procedureColumns);
            }
            $count=0;
            $size=sizeof($tableCaptionsArray);
            $fileName = time().'_'.date('d-M-Y');
            $filePath = public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xls";
            if($is_attach==1){              
                Excel::create($fileName, function($excel) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size) {
                    $excel->sheet('Sheet1',function($sheet) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size) {
                            $sheet->loadView('emails.DataMismatchReports')
                                    ->with('name',"Hi Team")
                                    ->with('tableData',$tableData)
                                    ->with('colunmNames',$tableColumnArray)
                                    ->with("TableCaptions",$tableCaptionsArray)
                                    ->with('count',$count)
                                    ->with('toexcel',1)
                                    ->with('size',$size);
                        });
                })->store('xls', public_path('download'));
                if(isset($tableData[0]) && count($tableData[0])>0){
                    $rss=Mail::send('emails.po',['name' =>"Hi Team",'comment'=>'Please find attached file'], function ($m) use ($getEmails, $subject,$filePath)
                    {
                        $m->to($getEmails, "Hi Team")->subject($subject.'('.date('d-M-Y').')');
                        $m->attach($filePath);
                    });
                    File::delete($filePath);
                    print_r($rss); die();
                }else{
                    echo 'No data found';
                }
            }else {
                if(isset($tableData[0]) && count($tableData[0])>0){
                    $exceldata=array();
                
                if(!empty($getMongoDBProcedureSummaryData) && $is_summary==1) {
                   
                   $excelProcedureName = (isset($getMongoDBProcedureSummaryData[0][0]) && $getMongoDBProcedureSummaryData[0][0] != "") ? $getMongoDBProcedureSummaryData[0][0] : "";
                    
                    if($excelProcedureName != ""){
                        $exceldata = DB::select(DB::raw("CALL".$excelProcedureName));
                        $exceldata = json_decode(json_encode($exceldata),1);
                        //$fileName = time().'_'.date('d-M-Y');
                        Excel::create($fileName, function($excel) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size,$exceldata) {
                            $excel->sheet('Sheet1',function($sheet) use($tableData,$tableColumnArray,$tableCaptionsArray,$count,$size,$exceldata) {
                                    $sheet->fromArray($exceldata);
                                });
                        })->store('xls', public_path('download'));                        
                    }

                }
                log::info('inentorydatamismatch');
                log::info($getEmails);
                $body=array('template'=>'emails.DataMismatchReports','attachment'=>$filePath,'name' =>"Hi Team",'tableData'=>$tableData,'colunmNames'=>$tableColumnArray,"TableCaptions"=>$tableCaptionsArray,'count'=>$count,'size'=>$size);
                $subject=$subject.'('.date('d-M-Y').')';

               // $rss=Utility::sendEmail($getEmails,$subject,$body);

                $rss=Mail::send('emails.DataMismatchReports',['name' =>"Hi Team",'tableData'=>$tableData,'colunmNames'=>$tableColumnArray,"TableCaptions"=>$tableCaptionsArray,'count'=>$count,'size'=>$size], function ($m) use ($getEmails, $subject,$exceldata,$filePath)
                    {
                        $m->to($getEmails, "Hi Team")->subject($subject.'('.date('d-M-Y').')');
                        if(count($exceldata))
                        $m->attach($filePath);
                    });
                    File::delete($filePath);
                    print_r($rss); die();
                }else{
                    echo 'No data found';
                }
            }
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage().' '.$ex->getTraceAsString();
        }
    }


}