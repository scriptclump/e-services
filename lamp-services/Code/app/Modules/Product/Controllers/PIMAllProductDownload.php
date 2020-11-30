<?php

namespace App\Modules\Product\Controllers;

use App\Modules\Roles\Models\Role;
use App\Http\Controllers\BaseController;
use Session;
use View;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Hash;
use Redirect;
use App\Modules\Product\Models\ProductModel;
use UserActivity;
use App\Modules\Product\Models\ProductEPModel;
use Excel;
use Illuminate\Support\Facades\Config;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Mail;
use File;
  /* * ***Use for only queing and the session mangement****** */
use App\Lib\Queue;
use App\models\Mongo\MongoDmapiModel;

class PIMAllProductDownload extends BaseController {

    public function __construct() {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
             return $next($request);
            });
    }
   
    public function downloadAllProductInfo() {

        try{
           // ini_set('max_execution_time', 1200);
            //Log::info("PIMAllProductDownload Start Downloading all products.. ");
            $cat_data = array();
            $supplier_id = (Session::get('supplier_id') != '') ? Session::get('supplier_id') : Session::get('EditSupplier_id');

            $ProductModelObj = new ProductModel();
            //Log::info("Calling product modle downloadAllProductInfo().. ");
            $data = $ProductModelObj->downloadAllProductInfo();
            //Log::info("Preparing Excel sheet");
            $file_name = 'All Products';
            $result = Excel::create($file_name, function($excel) use($data) {
                        $excel->sheet('Sheet1', function($sheet) use($data) {
                            $sheet->fromArray($data['cat_data'], null, 'A1', false, false);
                            $sheet->protectCells('A1', 'password');
                            $sheet->protectCells('B1', 'password');
                        });
                        // Set sheets
                    })->store('xls', public_path('download'));
            $path_name = public_path('download') . DIRECTORY_SEPARATOR . $file_name . ".xls";
            echo $this->mailExcelReport($path_name,Session::get('userId'));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

     public function mailExcelReport($filePath, $userId){
        try{
             $userId = ($userId=="")?3:$userId;
            // log::info("user id =".$userId);
            $srmQuery = json_decode(json_encode(DB::table("users")->where('user_id','=',$userId)->pluck('email_id')->all()), true);
            $email = $srmQuery[0];
            $time = Carbon::now();
            $emailBody = "Hello " . ucwords(str_replace(".", " ", explode("@", $email)[0])) . ", <br/><br/>";
            $emailBody .= "Please find attached ALL Products Report.<br/><br/>";
            $emailBody .= "*Note: This is an auto generated email !!";
            if (Mail::send('emails.pimMail', ['emailBody' => $emailBody], function ($message) use ($email, $filePath, $time) {
                        $message->to($email);
                        $message->subject('PIM Report '.date('d-m-Y',strtotime($time->toDateTimeString())));
                        $message->attach($filePath);
                    })) {
                File::delete($filePath);
                // echo "Mail sent to - ".$email." !! Temp file deleted !!\n";
                //Log::info("Mail sent to - ".$email." !! Temp file deleted !!");
                return  "Mail sent to - ".$email.". Please check mail.";
            } else {
                 return  "Error in sending mail to ".$email." !!";
                //Log::info("error in sending mail to ".$email." !!");
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Log::error("Error in sending mail to ".$email." !!");
        }
    }
    public function QueueDownloadAllProductInfo(){
        try{
            $userid = Session::get('userId');
           // Log::info("Download all product info with queue.....user id...".$userid);
            $this->queue = new Queue();
            $args = array("ConsoleClass" => 'AllPIMDownloadReport', 'arguments' => array());
           // Log::info(print_r($args,true));
            $this->queue->enqueue('default', 'ResqueJobRiver', $args);
            return "Please check your mail.";

        }catch (\ErrorException $ex) {
            //Log::info("QueueDownloadAllProductInfo method error");
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}
