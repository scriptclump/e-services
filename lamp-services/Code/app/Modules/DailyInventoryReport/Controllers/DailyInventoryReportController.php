<?php

/*
 * Filename: DailyInventoryReportController.php
 * Description: This file is used for Daily Inventory Report
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 9th May 2017
 * Modified date: 9th May 2017
 */

/*
 * DailyInventoryReportController is used to Daily Inventory Report
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2017
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\DailyInventoryReport\Controllers;
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);
use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use PDF;
use Notifications;
use UserActivity;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\Roles\Models\Role;
use App\Modules\DailyInventoryReport\Models\DailyInventoryReportModel;
use Mail;
use File;
use App\Central\Repositories\RoleRepo;
use App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;

class DailyInventoryReportController extends BaseController {

    public function __construct() {
        try {
            // if (!Session::has('userId')) {
            //     Redirect::to('/login')->send();
            // }
            // $access = $this->_roleRepo->checkPermissionByFeatureCode('INV1001');
            // if (!$access) {
            //     Redirect::to('/')->send();
            //     die();
            // }
            $this->_inventoryDaily = new DailyInventoryReportModel();
            $this->_roleRepo = new RoleRepo();
            parent::Title('Manage Inventory');

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            
            $feature_id = "DIR001";
           
            $date =  date("Y-m-d H:i:s");
            $date = str_ireplace(":", "-", str_ireplace(" ", "-", $date));
            $headers = array( "Product Id",
                                "SKU",
                                "Product Title",
                                "Dc Name",
                                 "Opening Balance(SOH)",
                                "Grn Qty",
                                "Order Qty",
                                "Sales Return Qty",
                                "Purchase Return Qty",
                                // "Old Quarantine Qty",
                                // "Old Dit Qty",
                                // "Old Missing Qty",
                                "Quarantine Qty",
                                "Dit Qty",
                                "Missing Qty",

                                "Closing Balance(SOH)"


                );
            
            $data = $this->_inventoryDaily->getDialyInventoryData();
            array_unshift($data, $headers);
            $fileName = "DailyInventoryReport".$date;
            $filepath = $this->makeExcelFile($fileName, $data);
            return $this->mailExcelReport($filepath);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function makeExcelFile($fileName, $results) {
        try {
            $i = 0;
            echo "Total Rows: ".count($results)."\n"; //exit;
            Log::info("Total Rows: ".count($results));
             $time = Carbon::now();
             $time = str_ireplace(":", "_", str_ireplace(" ", "_", $time));
             // echo str$time;die;
            Excel::create($fileName, function($excel) use($results, $i, $time) {
                $excel->sheet("DailyInventoryReport", function($sheet) use($results, $i) {
                    $sheet->fromArray($results, null, 'A1', true, false);
                });
            })->store('xls', public_path('download'));
            return public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xls";
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function mailExcelReport($filePath){
        try{
            $feature_id = "DIR001";
            $all_roles = $this->_inventoryDaily->getAllRolesByFeatureCode($feature_id);
            
            $message_aftermail_Sent = "";
            // $all_users_info = json_decode(json_encode($this->_roleRepo->getUsersByRole($all_roles)), true);
            // $all_emails = array_column($all_users_info, "email_id");
            
            $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $userIdData= $notificationObj->getUsersByCode('DIR001');
            $userIdData=json_decode(json_encode($userIdData));
            $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get()->all();
            $emails=json_decode(json_encode($data,1),true);
            $all_emails=array();
            foreach ($emails as $keyValue ){
                $all_emails[]=$keyValue['email_id'];
            }


                $time = Carbon::now();
                $emailBody = "Hello User, <br/><br/>";
                $emailBody .= "Please find attached Daily Inventory Report.<br/><br/>";
                $emailBody .= "*Note: This is an auto generated email !!";
                
                if (Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($all_emails, $filePath, $time) {
                            $message->to($all_emails);
                            $message->subject('Daily Inventory Report '.date('d-m-Y',strtotime($time->toDateTimeString())));
                            $message->attach($filePath);
                        })) {
                    File::delete($filePath);
                    $message_aftermail_Sent .=  "Mail sent to all users !! \n";
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