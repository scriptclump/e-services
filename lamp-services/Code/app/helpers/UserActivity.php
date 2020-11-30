<?php

use App\Modules\UserActivityLogs\Models\UserActivityLogs;
use App\Modules\UserActivityLogs\Models\UploadFilesLogs;
use App\Modules\UserActivityLogs\Models\ApiActivityLogs;

class UserActivity {

    static public function userActivityLog($module = "", $newvalues = "", $action = "", $oldvalues = "", $uniqueval = "") {
        $Useractivity = new UserActivityLogs();
        $result = $Useractivity->saveUserActivityLogs($module, $newvalues, $action, $oldvalues, $uniqueval);
        $retrunvalue = 1;

        return $retrunvalue;
    }

    static public function userActivityHistory($modulename) {
        $Useractivity = new UserActivityLogs();
        $results = $Useractivity->getHistoryOfUserActivity($modulename);
        $results = json_encode($results);
        return $results;
    }

    static public function excelUploadFileLogs($modulename, $uniqueid, $uploadfile, $logData) {
        /* here it will store the uploaded files W.R.T to the uploaded path, logs */
        $exceluploadLogs = new UploadFilesLogs();
        $result = $exceluploadLogs->excelUploadLogsSave($modulename, $uniqueid, $uploadfile, $logData);
    }

    static public function apiActivityLog($module = "", $api = "", $request = "", $action = "", $response = "") {
        $api_activity = new ApiActivityLogs();
        $result = $api_activity->saveApiActivityLogs($module, $api, $request, $action, $response);
        return $result;
    }

    static public function apiUpdateActivityLog($id, $updateParams) {
        $api_activity = new ApiActivityLogs();
        $result = $api_activity->updateApiActivityLog($id, $updateParams);
        return $result;
    }

}