<?php

/*
 * Filename: PickerEfficiencyReportController.php
 * Description: This file is used to generate report for Picker Efficiency 
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 3rd Jan 2017
 * Modified date: 3rd Jan 2017
 */

/*
 * CrateUtilizationReportController is used to generate report for Crate Utilization
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2017
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\PickerEfficiencyReport\Controllers;
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);
use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\Roles\Models\Role;
use App\Modules\PickerEfficiencyReport\Models\PickerEfficiencyModel;
use Mail;
use File;


class CrateUtilizationReportController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                return $next($request);
            });
            $this->_roleRepo = new RoleRepo();
            $this->_role = new Role();
            $this->_picker_effeciency_report = new PickerEfficiencyModel;
            parent::Title('Picker Efficiency Report'); 
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function crateUtilizationReport($start_date, $end_date, $email){
        $start  = $start_date." 00:00:00";
        $end    = $end_date." 23:59:59";

        $datesconcatination = $start_date."-".$end_date;

        $result = $this->_picker_effeciency_report->crateUtilization($start,$end);
        $filename = "Crate-Utilization-".$datesconcatination;

        $filepath = $this->makeExcelFile($filename, $result);

        
            $mail = $this->mailExcelReport($filepath, $email, $datesconcatination);
            echo $mail;
        
        
    }

    public function makeExcelFile($fileName, $results) {
        try {
            $i = 0;
//            Log::info("File Name: ".count($results));
  //          Log::info("Total Rows: ".count($results));
            Excel::create($fileName, function($excel) use($results, $i) {
                $excel->sheet("CrateUtilization", function($sheet) use($results) {
                    $sheet->fromArray($results);
                    
                });
            })->store('xls', public_path('download'));
            return public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xls";
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    

    public function mailExcelReport($filePath, $email, $bodyDates){
        try{
            $message_aftermail_Sent = "";
            $time = Carbon::now();
            $emailBody = "Hello User, <br/><br/>";
            $emailBody .= "Please find attached Daily Inventory Report.<br/><br/>";
            $emailBody .= "*Note: This is an auto generated email !!";
                
            if (Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($email, $filePath, $time, $bodyDates) {
                        $message->to($email);
                        $message->subject('Crate Utilization Report '.$bodyDates);
                        $message->attach($filePath);
                    })) {
                File::delete($filePath);
                $message_aftermail_Sent .=  "Mail sent to ".$email." !! \n";
            } else {
                $message_aftermail_Sent .= "Error in sending mail  !!\n";
            }

            return $message_aftermail_Sent;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

   

}
