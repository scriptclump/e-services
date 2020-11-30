<?php
namespace App\Modules\UserActivityLogs\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;




class UserActivityLogs extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'user_activity_logs';
    protected $primaryKey = '_id';
    
    public function saveUserActivityLogs($module, $newvalues, $action, $oldvalues, $uniqueval)
    {
    	try {
                // $tz = new DateTimeZone('Asia/Kolkata'); // or whatever zone you're after
                $ipaddress = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
                $username = Session::get("fullname");
                $userId = Session::get('userId');
                $userDetails  = array("userId" => $userId, "username" => $username);
                $now = new DateTime('Asia/Kolkata');
                $timestamp = $now->format('Y-m-d H:i:s');
                $this->module = $module;
                $this->oldvalues = $oldvalues;
                $this->newvalues = $newvalues;
                $this->action = $action;
                $this->Uniquevalue = $uniqueval;
				$this->userDetails = $userDetails;
                $this->ipaddress = $ipaddress;
				$this->created_at = $timestamp;
                $this->updated_at = $timestamp;
				$this->save();
    		
    	} catch (\ErrorException $ex) {
	            Log::info($ex->getMessage());
	            Log::info($ex->getTraceAsString());
        }
    	
    }

    public function getHistoryOfUserActivity($modulename)
    {
        try {
            $result = $this::where("module", "=", $modulename)->get();
            $result = json_decode($result, true);
            return $result;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    }
}
     