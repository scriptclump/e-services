<?php
namespace App\Modules\UserActivityLogs\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;




class UploadFilesLogs extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'excel_upload_logs';
    protected $primaryKey = '_id';
    
    public function excelUploadLogsSave($modulename, $uniqueid, $uploadfile, $logData)
    {
        try {
                $username = Session::get("fullname");
                $userId = Session::get('userId');
                $userDetails  = array("userId" => $userId, "username" => $username);
                $now = new DateTime('Asia/Kolkata');
                $timestamp = $now->format('Y-m-d H:i:s');
                $this->modulename = $modulename;
                $this->uniqueref = $uniqueid;
                $this->uploadfile_path = $uploadfile;
                $this->log_data = $logData;
                $this->user_details = $userDetails;
                $this->save();

            
        }  catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    }
}
     