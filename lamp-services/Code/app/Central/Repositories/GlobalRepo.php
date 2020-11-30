<?php 
namespace App\Central\Repositories;

use Illuminate\Support\Facades\Input;
use App\Central\Repositories\MongoRepo;
use Session;
use Log;

class GlobalRepo
{
    public function logRequest($request)
    {
        try
        {
            $inpuData = Input::all();
            $userName = '';
            $userId = 0;
            if(Session::has('userName'))
            {
                $userName = Session::get('userName');
            }
            if(Session::has('userId'))
            {
                $userId = Session::get('userId');
            }
            $requestData['root_url'] = $request->root();
            $requestData['user_id'] = $userId;
            $requestData['user_name'] = $userName;
            $requestData['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $requestData['ipaddress'] = $_SERVER['REMOTE_ADDR'];
            $requestData['route'] = $request->path();
            $requestData['is_ajax'] = $request->ajax();
            $requestData['method'] = $request->method();
            $requestData['input_data'] = json_encode($inpuData);
            date_default_timezone_set('Asia/Kolkata');
            $requestData['request_time'] = date('Y-m-d H:i:s');
//            Log::info($requestData);
            $tableName = 'request_log';
            $mongoRepo = new MongoRepo();
            $mongoRepo->insert($tableName, $requestData);
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
}
?>