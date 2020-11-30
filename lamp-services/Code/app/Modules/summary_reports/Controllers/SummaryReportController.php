<?php
namespace App\Modules\summary_reports\Controllers;
use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mail;
use DB;
use Excel;
use Log;
use Config;
use \App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\summary_reports\Models\LogisticsReports;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

class SummaryReportController{

    
    public function index()
    {
        try
        {
             $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $lsrData= $notificationObj->getUsersByCode('LSR0001');
            $crmData= $notificationObj->getUsersByCode('CRMSR0001');
            $salesData= $notificationObj->getUsersByCode('SSR0001');
            $purchageData= $notificationObj->getUsersByCode('PSR0001');

            $lsrData=json_decode(json_encode($lsrData));
            $crmData=json_decode(json_encode($crmData));
            $salesData=json_decode(json_encode($salesData));
            $purchageData=json_decode(json_encode($purchageData));

            $lsrSubject=$notificationObj->getMessageByCode('LSR0001');
            $crmSubject=$notificationObj->getMessageByCode('CRMSR0001');
            $salesSubject=$notificationObj->getMessageByCode('SSR0001');
            $purchageSubject=$notificationObj->getMessageByCode('PSR0001');

            $lsrMail= $usersObj->wherein('user_id',$lsrData)->select('email_id')->get()->all();
            $lsrEmail=json_decode(json_encode($lsrMail,1),true);

            $crmMail= $usersObj->wherein('user_id',$crmData)->select('email_id')->get()->all();
            $crmMail=json_decode(json_encode($crmMail,1),true);

            $salesMail= $usersObj->wherein('user_id',$salesData)->select('email_id')->get()->all();
            $salesMail=json_decode(json_encode($salesMail,1),true);

            $purchageMail= $usersObj->wherein('user_id',$purchageData)->select('email_id')->get()->all();
            $purchageMail=json_decode(json_encode($purchageMail,1),true);
            
            $lsrEmails=array();
            $crmEmails=array();
            $salesEmails=array();
            $purchageEmails=array();
            foreach ($lsrMail as $keyValue )
            {
                $lsrEmails[]=$keyValue['email_id'];
            }
            foreach ($crmMail as $keyValue )
            {
                $crmEmails[]=$keyValue['email_id'];
            }
            foreach ($salesMail as $keyValue )
            {
                $salesEmails[]=$keyValue['email_id'];
            }
            foreach ($purchageMail as $keyValue )
            {
                $purchageEmails[]=$keyValue['email_id'];
            }
            $responeDataFinal = array();
            $logisticObj1 = new LogisticsReports();
            $emails= $logisticObj1->getEmailData();
            Excel::create('Logistic Summary Reports('.date('Y-M-d').')', function($excel) 
            {
                $whList = DB::table('legalentity_warehouses')->where(['dc_type'=>'118001'])->pluck('le_wh_id')->all();
                $excel->setTitle('Logistic Summary Reports........................');
                foreach($whList as $wh)
                {
                $responeData=DB::select("call getDlyLogSumRep('".date('Y-m-d')."',$wh)");
                $responeDataFinal[] = $responeData[0];
                }             
                $responeData =json_decode(json_encode($responeDataFinal),1); 
                $logisticObj = new LogisticsReports();
                $logisticData[]= $logisticObj->getLogisticData();
                
                $excel->sheet('sheet1', function($sheet) use ($logisticData,$responeData)
                {
                    $sheet->fromArray($logisticData, null, 'A1', false, false);
                    $sheet->fromArray($responeData, null, 'A1', false, false);
                });
            })->store('xlsx', storage_path());
            $rss=Mail::send('emails.daily_summary_reports', ['name' =>"Dear User",'body'=>"Please find logistic sales summary reports."], function ($m) use($lsrEmails, $lsrSubject) 
            {
                $m->attach(storage_path().'/Logistic Summary Reports('.date('Y-M-d').').xlsx');
                $m->to($lsrEmails, "Dear User")->subject($lsrSubject.'('.date('d-M-Y').')');
            });
        echo "Logistic Summary Reports email sent";   
        Excel::create('CRM Summary Reports('.date('Y-M-d').')', function($excel) 
            {
                $excel->setTitle('CRM Summary Reports........................');
                $logisticObj = new LogisticsReports();
                $crmDataHeaders[]= $logisticObj->getCRMDataHeaders(); 
                $crmData= $logisticObj->getCRMData(); 
                $excel->sheet('sheet1', function($sheet) use ($crmDataHeaders, $crmData) {
                $sheet->fromArray($crmDataHeaders, null, 'A1', false, false);
                $sheet->fromArray($crmData, null, 'A1', false, false);
                });
            })->store('xlsx', storage_path());
            $rss=Mail::send('emails.daily_summary_reports',['name' =>"Dear User",'body'=>"Please find CRM sales summary reports."], function ($m) use($crmEmails, $crmSubject)
            {
                $m->attach(storage_path().'/CRM Summary Reports('.date('Y-M-d').').xlsx');
                $m->to($crmEmails, "Dear User")->subject($crmSubject.'('.date('d-M-Y').')');
            });
        echo "\nCRM Summary Reports email sent";     
            
            Excel::create('Sales Summary Reports('.date('d-M-Y').')', function($excel)
           {
            $whList = DB::table('legalentity_warehouses')->where(['dc_type'=>'118001'])->pluck('le_wh_id')->all();   
                foreach($whList as $wh)
                {
                $responeData=DB::select("call getDlySaleSumRep('".date('Y-m-d')."',$wh)");
                $responeDataFinal[] = $responeData[0];
                }                 
                $responeData =json_decode(json_encode($responeDataFinal),1); 
                $excel->setTitle('Sales Summary Reports........................');
                $logisticObj = new LogisticsReports();
                $salesData[]= $logisticObj->getSalesData();                
                $excel->sheet('sheet1', function($sheet) use ($salesData, $responeData)
                {
                    $sheet->fromArray($salesData, null, 'A1', false, false);
                    $sheet->fromArray($responeData, null, 'A1', false, false);
                });
            })->store('xlsx', storage_path());
            
            $rss=Mail::send('emails.daily_summary_reports', ['name' =>"Dear User",'body'=>"Please find daily sales summary reports."], function ($m) use($salesEmails, $salesSubject)
            {
                $m->attach(storage_path().'/Sales Summary Reports('.date('d-M-Y').').xlsx');
                $m->to($salesEmails, "Dear User")->subject($salesSubject."(".date('d-M-Y').")");
            });   
            echo "\nSales Summary Reports email sent"; 
            
            Excel::create('Purchase Summary Reports('.date('Y-M-d').')', function($excel)
           {
            $whList = DB::table('legalentity_warehouses')->where(['dc_type'=>'118001'])->pluck('le_wh_id')->all();    
                foreach($whList as $wh)
                {
                $responeData=DB::select("call getDlyPurSumRep('".date('Y-m-d')."',$wh)");
                $responeDataFinal[] = $responeData[0];
                } 
            
                $responeData =json_decode(json_encode($responeDataFinal),1);        
                $excel->setTitle('Purchase Summary Reports........................');
                $logisticObj = new LogisticsReports();
                $purchageData[]= $logisticObj->getPurchageData();
                $excel->sheet('sheet1', function($sheet) use ($purchageData, $responeData)
                {
                    $sheet->fromArray($purchageData, null, 'A1', false, false);
                    $sheet->fromArray($responeData, null, 'A1', false, false);                   
                });
                
            })->store('xlsx', storage_path());
            $rss=Mail::send('emails.daily_summary_reports',['name' =>"Dear User",'body'=>"Please find daily purchase summary reports."], function ($m) use ($purchageEmails, $purchageSubject)
            {
                $m->attach(storage_path().'/Purchase Summary Reports('.date('Y-M-d').').xlsx');
                $m->to($purchageEmails, "Dear User")->subject($purchageSubject.'('.date('d-M-Y').')');
            });
            echo "\nPurchase Summary Reports email sent"; 
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
}
