<?php

namespace App\Modules\UserActivityLogs\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;

class ApiActivityLogs extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'api_activity_logs';
    protected $primaryKey = '_id';

    public function saveApiActivityLogs($module, $api, $request, $action, $response) {
        try {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
            $username = Session::get("fullname");
            $userId = Session::get('userId');
            $userDetails = array("userId" => $userId, "username" => $username);
            $now = new DateTime('Asia/Kolkata');
            $timestamp = $now->format('Y-m-d H:i:s');
            $this->module = $module;
            $this->api = $api;
            $this->request = $request;
            $this->response = $response;
            $this->action = $action;
            $this->userDetails = $userDetails;
            $this->ipaddress = $ipaddress;
            $this->created_at = $timestamp;
            $this->updated_at = $timestamp;
            $this->save();
            return $this->_id;
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
    
    public function updateApiActivityLog($id, $updateDetails) {
        return $this::where("_id", $id)->update(array("response" => $updateDetails));
    }

}
