<?php
namespace App\Modules\Cpmanager\Controllers;

use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Log;
use URL;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\CartModel;
use App\Modules\Cpmanager\Models\BeatDashboardModel;

class BeatDashboardController extends BaseController {
    
    protected $_beat;
    
    public function __construct() {
        $this->_beat = new BeatDashboardModel();
        $this->cart = new CartModel();
    }
    
    public function getBeatInfo()
    {
        try
        {
            $response = [];
            $status = 0;
            $message = 'Unable to process';
            $data = Input::all();
            // Log::info($data);
            if(isset($data['data']))
            {
                $request = json_decode($data['data'], true);
                if (isset($request['customer_token']) && $request['customer_token'] != '')
                {
                    $token = $request['customer_token'];
                    $val = $this->cart->valToken($token);
                    if ($val['token_status'] == 1)
                    {
//                        $request['customer_token'] = $token;
                        $response = $this->_beat->getBeatInformation($request);
                        return $response;
                    }
                }else{
                    $message = 'Customer token is not sent';
                }
            }else{                
                $message = 'No input data.';
            }
            return json_encode(['status' => $status, 'message' => $message, 'result' => $response]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getAllData()
    {
        try
        {
            $data = Input::all();
            $status = "success";
            $message = 'No data found.';
            $response = [];
            if(isset($data['data']))
            {
                $request = json_decode($data['data'], true);
//                echo "<prE>";print_R($request);die;
                if (isset($request['customer_token']) && $request['customer_token'] != '')
                {
                    $token = $request['customer_token'];
                    $val = $this->cart->valToken($token);
                    if ($val['token_status'] == 1)
                    {
                        $dcType = isset($request['dc_type']) ? $request['dc_type'] : 0;
                        $requestId = isset($request['request_id']) ? $request['request_id'] : 0;
//                        echo $dcType;die;
                        if($dcType > 0)
                        {
                            switch($dcType)
                            {
                                case 118001:                                    
                                    $response = [];
                                    $returnAll = isset($request['return_all']) ? $request['return_all'] : 0;
                                    $result = $this->_beat->getAllDC($returnAll);
                                    if(is_array($result))
                                    {
                                        $status = isset($result['status']) ? $result['status'] : 0;
                                        if($status == 1)
                                        {
                                            $response = isset($result['data']) ? $result['data'] : [];
                                        }else{
                                            $message = isset($result['message']) ? $result['message'] : '';
                                        }
                                    }                                    
                                    break;
                                case 118002:
                                    $response = [];
                                    $returnAll = isset($request['return_all']) ? $request['return_all'] : 0;
                                    $result = $this->_beat->getHubsById($requestId, $returnAll);
                                    if(is_array($result))
                                    {
                                        $status = isset($result['status']) ? $result['status'] : 0;
                                        if($status == 1)
                                        {
                                            $response = isset($result['data']) ? $result['data'] : [];
                                        }else{
                                            $message = isset($result['message']) ? $result['message'] : '';
                                        }
                                    }
                                    break;
                                case 118003:
                                    $returnBeats = isset($request['returnBeats']) ? $request['returnBeats'] : 0;
                                    $response = $this->_beat->getSpokesById($requestId, $returnBeats);
                                    break;
                                case 118004:
                                    $response = $this->_beat->getBeatsById($requestId);
                                    break;
                                case 118005:
                                    $response = $this->_beat->getAreasById($requestId);
                                    break;
                            }
                            if(!empty($response))
                            {
                                $status = 'success';
                                $message = "Successful.";
                            }
                        }
                    }else{
                        $status = "failed";
                        $message = "Invalid token";
                    }
                }else{
                    $status = "failed";
                    $message = "Customer token missing";
                }
            }else{
                $status = "failed";
                $message = "Please provide data.";
            }
            return json_encode(['status' => $status, 'message' => $message, 'data' => $response]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function storeSpoke()
    {
        try
        {
            $status = "success";
            $data = Input::all();
            $spokeId = 0;
            $response = [];
            if(isset($data['data']))
            {
                $request = json_decode($data['data'], true);
//                echo "<prE>";print_R($request);die;
                if (isset($request['customer_token']) && $request['customer_token'] != '')
                {
                    $token = $request['customer_token'];
                    $val = $this->cart->valToken($token);
                    if ($val['token_status'] == 1)
                    {
                        $response = $this->_beat->saveSpokeData($request);
                        if(is_array($response))
                        {
                            $message = isset($response['message']) ? $response['message'] : '';
                            unset($response['message']);
//                            $spokeId = isset($response['spoke_id']) ? $response['spoke_id'] : 0;
                        }
                        if (strpos($message, 'Sucessfully') !== false) {
                            $status = "success";
                        }
                    }else{
                        $status = "failed";
                        $message = "Invalid token";
                    }
                }else{
                    $status = "failed";
                    $message = "Customer token missing";
                }
            }else{
                $status = "failed";
                $message = "Please provide data.";
            }
            return json_encode(["status" => $status, "message" => $message, 'data' => $response]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function storeBeat()
    {
        try
        {
            $status = "failed";
            $data = Input::all();
            if(isset($data['data']))
            {
                $request = json_decode($data['data'], true);
//                echo "<prE>";print_R($request);die;
                if (isset($request['customer_token']) && $request['customer_token'] != '')
                {
                    $token = $request['customer_token'];
                    $val = $this->cart->valToken($token);
                    if ($val['token_status'] == 1)
                    {
                        $message = $this->_beat->saveBeatData($request);
                        if (strpos($message, 'Sucessfully') !== false) {
                            $status = "success";
                        }
                    }else{
                        $message = "Invalid token";
                    }
                }else{
                    $message = "Customer token missing";
                }
            }else{
                $message = "Please provide data.";
            }
            return json_encode(["status" => $status, "message" => $message]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
}