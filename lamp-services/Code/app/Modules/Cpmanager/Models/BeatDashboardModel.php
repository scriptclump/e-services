<?php

namespace App\Modules\Cpmanager\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use App\Modules\Cpmanager\Controllers\accountController;

use DB;
use Log;

class BeatDashboardModel extends Model {
    
    public function getBeatInformation($data)
    {
        try
        {
            $status = 0;
            $result = [];
            $message = [];
            $fromDate = isset($data['from_date']) ? $data['from_date'] : date('Y-m-d');
            $toDate = isset($data['to_date']) ? $data['to_date'] : date('Y-m-d');
//            $userId = isset($data['ff_id']) ? $data['ff_id'] : 0;
            $warehouserId = isset($data['wh_id']) ? $data['wh_id'] : 0;
            $hubId = isset($data['hub_id']) ? $data['hub_id'] : 0;
            $beatId = isset($data['beat_id']) ? $data['beat_id'] : 0;
            $token = $data['customer_token'] ? $data['customer_token'] : 0;
//            $userInfo = \DB::table('users')                                
//                            ->where(['password_token' => $token,
//                                'is_active' => 1])
//                            ->first(['user_id']);
////            echo "<pre>";print_R($userInfo);
//            $userId = property_exists($userInfo, 'user_id') ? $userInfo->user_id : 0;
            $userId = $this->getMyUserId();
            if($userId > 0)
            {
                if($hubId == 0)
                {
                    $rolesModel = new Role();
                    $warehouseDetails = $rolesModel->getWarehouseData($userId, 6);
    //                echo "warehouseDetails<prE>";print_R($warehouseDetails);die;
                    if($warehouseDetails != '')
                    {
                        $warehouseInfo = (array) json_decode($warehouseDetails, true);
    //                    echo "<pre>";var_dump($warehouseInfo['118002']);die;
                        $hubId = isset($warehouseInfo['118002']) ? $warehouseInfo['118002'] : 0;
                    }
                }                
                $userId = 0;
//                echo $hubId;die;
                DB::enableQueryLog();
                $response = DB::select('CALL getHubDashboardData('.$userId.',"'.$fromDate.'","'.$toDate.'",'.
                        $warehouserId.',"'.$hubId.'","'.$beatId.'")');
                //Log::info(DB::getQueryLog());
            }else{
                $message = 'Customer token not valied';
            }
            if(!empty($response))
            {
                $status = 1;
                $result = $response;
                $message = 'Sucessfully fetched data.';
            }
            return json_encode(['status' => $status, 'message' => $message, 'result' => $result]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            $message = $ex->getMessage();
            return json_encode(['status' => $status, 'message' => $message, 'result' => $result]);
        }
    }
    
    public function getAllDC($returnAll)
    {
        try
        {
            $result = [];
            $message = '';
            $status = 0;
            $dcAccessList = $this->getMyAccessList(118001);
            if($dcAccessList == 0)
            {
                $hubAccessList = $this->getMyAccessList(118002);
                if($hubAccessList != 0)
                {
                    $dcAccessList = DB::table('dc_hub_mapping')
                        ->whereIn('hub_id', explode(',', $hubAccessList))
                        ->pluck('dc_id')->all();
                    if(!empty($dcAccessList) && is_array($dcAccessList))
                    {
                        $dcAccessList = implode(',', $dcAccessList);
                    }
                }
            }
//            echo "<pre>";print_r($dcAccessList);die;
            DB::enableQueryLog();
            if($dcAccessList != 0 && $returnAll == 0)
            {
                $result = DB::table('legalentity_warehouses')
                    ->where(['dc_type' => 118001, 'status' => 1])
                    ->whereIn('le_wh_id', explode(',', $dcAccessList))
                    ->select(DB::raw('le_wh_id as id'), DB::raw('lp_wh_name as name'))
                    ->get()->all();
                $status = 1;
            }else{
                if($returnAll)
                {
                    $result = DB::table('legalentity_warehouses')
                        ->where(['dc_type' => 118001, 'status' => 1])
//                        ->whereIn('le_wh_id', explode(',', $dcAccessList))
                        ->select(DB::raw('le_wh_id as id'), DB::raw('lp_wh_name as name'))
                        ->get()->all();
                    $status = 1;
                }else{
                    $message = "Please assign DC to the user";
                }                
            }
            //Log::info(DB::geTqueryLog());
//            return $result;
            return ['status' => $status, 'message' => $message, 'data' => $result];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return [];
        }
    }
    
    public function getHubsById($dcId, $returnAll)
    {
        try
        {
            $result = [];
            DB::enableQuerylog();
            $message = '';
            $status = 0;
            $hubAccessList = $this->getMyAccessList(118002);
//            echo "<prE>";print_r($hubAccessList);die;
            if($hubAccessList != 0 && $returnAll == 0)
            {
                if($dcId != '')
                {
                    $result = DB::table('dc_hub_mapping')
                            ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'dc_hub_mapping.hub_id')
                            ->whereIn('dc_id', explode(',', $dcId))
                            ->where(['legalentity_warehouses.dc_type' => 118002, 'legalentity_warehouses.status' => 1])
                            ->whereIn('legalentity_warehouses.le_wh_id', explode(',', $hubAccessList))
                            ->select(DB::raw('legalentity_warehouses.le_wh_id as id'), DB::raw('legalentity_warehouses.lp_wh_name as name'))
                            ->groupBy('legalentity_warehouses.le_wh_id')
                            ->get()->all();
                }else{
                    $result = DB::table('legalentity_warehouses')
                            ->whereIn('le_wh_id', explode(',', $hubAccessList))
                            ->where(['legalentity_warehouses.dc_type' => 118002, 'legalentity_warehouses.status' => 1])
                            ->select(DB::raw('legalentity_warehouses.le_wh_id as id'), DB::raw('legalentity_warehouses.lp_wh_name as name'))
                            ->get()->all();
                }
                $status = 1;
            }else{
                if($returnAll)
                {
                    if($dcId != '')
                    {
                        $result = DB::table('dc_hub_mapping')
                                ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'dc_hub_mapping.hub_id')
                                ->whereIn('dc_id', explode(',', $dcId))
                                ->where(['legalentity_warehouses.dc_type' => 118002, 'legalentity_warehouses.status' => 1])
//                                ->whereIn('legalentity_warehouses.le_wh_id', explode(',', $hubAccessList))
                                ->select(DB::raw('legalentity_warehouses.le_wh_id as id'), DB::raw('legalentity_warehouses.lp_wh_name as name'))
                                ->groupBy('legalentity_warehouses.le_wh_id')
                                ->get()->all();
                    }else{
                        $result = DB::table('legalentity_warehouses')
                                ->where(['legalentity_warehouses.dc_type' => 118002, 'legalentity_warehouses.status' => 1])
//                                ->whereIn('le_wh_id', explode(',', $hubAccessList))
                                ->select(DB::raw('legalentity_warehouses.le_wh_id as id'), DB::raw('legalentity_warehouses.lp_wh_name as name'))
                                ->get()->all();
                    }
                    $status = 1;
                }else{
                    $message = "Please assign HUB to the user";
                }
            }
            //Log::info(DB::getQueryLog());
            return ['status' => $status, 'message' => $message, 'data' => $result];
//            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return [];
        }
    }
    
    public function getMyAccessList($entityId)
    {
        try
        {
            $dcId = 0;
            $userId = $this->getMyUserId();
            if($userId > 0)
            {
                $rolesModel = new Role();
                $warehouseDetails = $rolesModel->getWarehouseData($userId, 6);
//                echo "warehouseDetails<prE>";print_R($warehouseDetails);die;
                if($warehouseDetails != '')
                {
                    $warehouseInfo = (array) json_decode($warehouseDetails, true);
//                    echo "<pre>";var_dump($warehouseInfo['118002']);die;
                    $dcId = isset($warehouseInfo[$entityId]) ? $warehouseInfo[$entityId] : 0;
                }
            }
            return $dcId;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return 0;
        }
    }
    
    public function getMyBeats()
    {
        try
        {
            $userId = $this->getMyUserId();
            if($userId > 0)
            {
                $rolesModel = new Role();
                $warehouseDetails = $rolesModel->getWarehouseData($userId, 6);
                echo "warehouseDetails<prE>";print_R($warehouseDetails);die;
                if($warehouseDetails != '')
                {
                    $warehouseInfo = (array) json_decode($warehouseDetails, true);
//                    echo "<pre>";var_dump($warehouseInfo['118002']);die;
                    $hubId = isset($warehouseInfo['118002']) ? $warehouseInfo['118002'] : 0;
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return 0;
        }
    }
    
    public function getMyUserId()
    {
        try
        {
            $userId = 0;
            $data = \Input::all();
            if(isset($data['data']))
            {
                $request = json_decode($data['data'], true);
                $customer_token = isset($request['customer_token']) ? $request['customer_token'] : '';
                if($customer_token != '')
                {
                    $account = new accountController();
                    $userInfo=$account->getDataFromToken(1,$customer_token,'user_id');
                    if(!empty($userInfo))
                    {
                        $userId = $userInfo->user_id;
                    }
                }
            }
            return $userId;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return 0;
        }
    }
    
    public function getSpokesById($hubId, $returnBeats)
    {
        try
        {
            $result = [];
            if($hubId != '')
            {
                DB::enableQueryLog();
                if($returnBeats == 1)
                {
                    $result = DB::table('pjp_pincode_area')
                        ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
                        ->whereIn('spokes.le_wh_id', explode(',', $hubId))
                        ->select(DB::raw('pjp_pincode_area_id as id'), DB::raw('pjp_name as name'))
//                        ->groupBy('spokes.spoke_id')
                        ->get()->all();
                }else{
                    $result = DB::table('spokes')
                        ->whereIn('le_wh_id', explode(',', $hubId))
                        ->select(DB::raw('spoke_id as id'), DB::raw('spoke_name as name'), 'pincode')
//                        ->groupBy('spokes.spoke_id')
                        ->get()->all();
                }
                //Log::info(DB::getQueryLog());
            }
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return [];
        }
    }
    
    public function getBeatsById($hubId)
    {
        try
        {
            $result = [];
            if(!empty($hubId))
            {
                DB::enableQueryLog();
                $result = DB::table('pjp_pincode_area')
//                        ->whereIn('le_wh_id', $hubId)
                        ->whereIn('spoke_id', explode(',', $hubId))
                        ->select(DB::raw('pjp_pincode_area_id as id'), DB::raw('pjp_name as name'))
                        ->groupBy('pjp_pincode_area.pjp_pincode_area_id')
                        ->get()->all();
                //Log::info(DB::getQueryLog());
            }
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return [];
        }
    }
    
    public function getAreasById($spokeId)
    {
        try
        {
            $result = [];
            if($spokeId != '')
            {
                $result = DB::table('pincode_area')
                        ->leftJoin('cities_pincodes', 'cities_pincodes.city_id', '=', 'pincode_area.area_id')
                        ->whereIn('pjp_pincode_area_id', explode(',', $spokeId))
                        ->select(DB::raw('pincode_area.area_id as id'), DB::raw('cities_pincodes.officename as name'))
                        ->groupBy('pincode_area.area_id')
                        ->get()->all();
            }
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return [];
        }
    }
    
    public function saveSpokeData($request)
    {
        try
        {
            $message = 'Unable to save data';
            $spokeId = 0;
            $spokeName = isset($request['name']) ? $request['name'] : '';
//            $spokeWhId = isset($request['le_wh_id']) ? $request['le_wh_id'] : 0;
            $spokeHubId = isset($request['le_wh_id']) ? $request['le_wh_id'] : '';
            $spokeUserId = isset($request['user_id']) ? $request['user_id'] : '';
            $spokePincode = isset($request['pincode']) ? $request['pincode'] : '';
            $spokeId = isset($request['id']) ? $request['id'] : 0;
            if($spokeName != '' && $spokeHubId > 0)
            {
                $insertData['spoke_name'] = $spokeName;
                $insertData['le_wh_id'] = $spokeHubId;
                $insertData['pincode'] = $spokePincode;
                if($spokeId > 0)
                {
//                    $insertData['updated_by'] = $spokeUserId;
                    DB::table('spokes')
                            ->where('spoke_id', $spokeId)
                            ->update($insertData);
                    $message = "Updated Sucessfully";
                }else{
                    $check = DB::table('spokes')
                        ->where(['spoke_name' => $spokeName, 
                            'le_wh_id' => $spokeHubId])
                        ->first(['spoke_id']);
//                    echo "<pre>";print_R($check);die;
//                    Log::info($check);
                    if(empty($check))
                    {
                        $request['id'] = DB::table('spokes')
                            ->insertGetId($insertData);
                        $message = "Inserted Sucessfully";
                    }else{
                        $request['id'] = $check->spoke_id;
                        DB::table('spokes')
                            ->where('spoke_id', $check->spoke_id)
                            ->update($insertData);
                        $message = "Updated Sucessfully";
                    }
                }
            }else{
                $message = "Spoke data cant be empty.";
            }
            $request['message'] = $message;
            return $request;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            $message = $ex->getMessage();
            return $request;
        }
    }
    
    public function saveBeatData($request)
    {
        try
        {
            $message = 'Unable to save data';
            $pjpName = isset($request['name']) ? $request['name'] : '';
            $pjpDays = isset($request['days']) ? $request['days'] : '';
            $pjpRmId = isset($request['rm_id']) ? $request['rm_id'] : 0;
            $pjpWhId = isset($request['le_wh_id']) ? $request['le_wh_id'] : 0;
            $pjpSpokeId = isset($request['spoke_id']) ? $request['spoke_id'] : 0;
            $pjpUserId = isset($request['user_id']) ? $request['user_id'] : '';
            $pjpId = isset($request['id']) ? $request['id'] : 0;
            if($pjpName != '' && $pjpRmId > 0 && $pjpWhId > 0 && $pjpDays != '')
            {
                $insertData['pjp_name'] = $pjpName;
                $insertData['days'] = $pjpDays;
                $insertData['rm_id'] = $pjpRmId;
                $insertData['le_wh_id'] = $pjpWhId;
                $insertData['spoke_id'] = $pjpSpokeId;
                if($pjpId > 0)
                {
                    $insertData['updated_by'] = $pjpUserId;
                    DB::table('pjp_pincode_area')
                            ->where('pjp_pincode_area_id', $pjpId)
                            ->update($insertData);
                    $data = DB::statement("call get_retailer_update(".$pjpId.",".$pjpSpokeId.",".$pjpWhId.")");
                    $message = "Updated Sucessfully";
                }else{
                    $insertData['created_by'] = $pjpUserId;
                    DB::table('pjp_pincode_area')
                            ->insert($insertData);
                    $message = "Inserted Sucessfully";
                }
            }else{
                $message = "Beat data cant be empty.";
            }
            return $message;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return $message;
        }
    }
}
