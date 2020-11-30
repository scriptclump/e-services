<?php

namespace App\Central\Repositories;
use DB;
use Log;
use Session;
use Config;
use App\Central\Repositories\MongoRepo;
use Utility;

class CustomerRepo {

    public function getCustomerOrders($id, $cust_id, $ima_id) {
        if ($id == 1) {
            $place = DB::select('select value from master_lookup where name="placed"');
            $placed = $place[0]->value;
            if (!empty($cust_id)) {
                $result = DB::table('eseal_orders')
                        ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                        ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                        ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                        ->where('eseal_orders.order_status_id', '=', $placed)
                        ->where('eseal_customer.customer_id', '=', $cust_id)
                        ->get()->all();
            } else {
                $result = DB::table('eseal_orders')
                        ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                        ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                        ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                        ->where('eseal_orders.order_status_id', '=', $placed)
                        ->get()->all();
            }
        }
        if ($id == 2) {
            $approve = DB::select('select value from master_lookup where name="Approve"');
            $approved = $approve[0]->value;
            if (!empty($cust_id)) {
                $result = DB::table('eseal_orders')
                        ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                        ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                        ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                        ->where('eseal_orders.order_status_id', '=', $approved)
                        ->where('eseal_customer.customer_id', '=', $cust_id)
                        ->get()->all();
            } else {
                $result = DB::table('eseal_orders')
                        ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                        ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                        ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                        ->where('eseal_orders.order_status_id', '=', $approved)
                        ->get()->all();
            }
        }
        if ($id == 3) {
            $Deliver = DB::select('select value from master_lookup where name="Delivered"');
            $Delivered = $Deliver[0]->value;
            if (!empty($cust_id)) {
                $result = DB::table('eseal_orders')
                        ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                        ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                        ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                        ->where('eseal_orders.order_status_id', '=', $Delivered)
                        ->where('eseal_customer.customer_id', '=', $cust_id)
                        ->get()->all();
            } else {
                $result = DB::table('eseal_orders')
                        ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                        ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                        ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                        ->where('eseal_orders.order_status_id', '=', $Delivered)
                        ->get()->all();
            }
        }
        if ($id == 0) {
            if (empty($cust_id)) {
                // return 'hi';
                $result = DB::table('eseal_orders')
                        ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                        ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                        ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                        ->get()->all();
            } else {
                $result = DB::table('eseal_orders')
                        ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                        ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                        ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                        ->where('eseal_customer.customer_id', '=', $cust_id)
                        ->where('eseal_orders.ima_id', '=', $ima_id)
                        ->get()->all();
            }
        }
        return $result;
    }

    /*
      This function is used for getting the customer details based on the user id
      params : user_id
      return : customer related details.
     */

    public function getCustomerDetails($cust_id, $user_id) {

        if (!empty($cust_id)) {
            //return 'hi';
            $result = DB::table('eseal_customer')
                    ->select('eseal_customer.*')
                    ->join('users', 'users.customer_id', '=', 'eseal_customer.customer_id')
                    ->where(array('users.user_id' => $user_id, 'users.is_active' => 1))
                    ->get()->all();
        } else {

            $result = DB::table('eseal_customer')
                    ->where('status', 1)
                    ->get()->all();
        }
        return $result;
    }

    public function getAllCustomers($legal_entity_id = '') {
        $result = DB::table('legal_entities');
        if (!empty($legal_entity_id)) {
            $result->where('status', 1);
            $checkParent = $this->checkParent($legal_entity_id);
            if ($checkParent) {
                $childCompany = DB::table('legal_entities')->where('parent_company_id', $checkParent);
                $result->where('legal_entity_id', $legal_entity_id)->union($childCompany);
            } else {
                $result->where('legal_entity_id', $legal_entity_id);
            }
        }
        $legalEntityResult = $result->orderBy('business_legal_name', 'ASC')->get()->all();
        return $legalEntityResult;
    }

    public function checkParent($legal_entity_id) {
        try {
            $parentCompany = DB::table('legal_entities')->where('legal_entities.legal_entity_id', $legal_entity_id)->first(array('legal_entity_id', 'parent_id'));
            if (!empty($parentCompany) && $parentCompany->parent_id == -1) {
                return $parentCompany->legal_entity_id;
            } else {
                return 0;
            }
        } catch (\ErrorException $ex) {
            die($ex);
        }
    }

    public function getChildDetails() {
        try {
            $currentUserId = \Session::get('userId');
            $manufacturerDetails = DB::table('users')->where('user_id', $currentUserId)->pluck('customer_id');
            $parent = $this->checkParent($manufacturerDetails);

            if ($manufacturerDetails > 0) {
                if ($parent != 0) {

                    $childCompanies = DB::table('eseal_customer')
                            ->select('eseal_customer.brand_name', 'eseal_customer.customer_id', 'eseal_customer.parent_company_id')
                            ->where('eseal_customer.parent_company_id', '=', $parent)
                            ->orwhere('eseal_customer.customer_id', '=', $parent)
                            ->get()->all();
                } else {

                    $childCompanies = DB::table('eseal_customer')
                            ->select('eseal_customer.brand_name', 'eseal_customer.customer_id', 'eseal_customer.parent_company_id')
                            ->where('eseal_customer.customer_id', '=', $manufacturerDetails)
                            ->get()->all();
                }
            } else {

                $manufArray = DB::table('user_manufacturer')->where('user_id', $currentUserId)->pluck('manufacturer_id')->all();


                if (!empty($manufArray)) {

                    $childCompanies = DB::table('eseal_customer')
                            ->select('eseal_customer.brand_name', 'eseal_customer.customer_id', 'eseal_customer.parent_company_id', 'eseal_customer.status')
                            ->whereIn('eseal_customer.customer_id', $manufArray)
                            ->get()->all();
                } else {

                    $childCompanies = DB::table('eseal_customer')
                            ->select('eseal_customer.brand_name', 'eseal_customer.customer_id', 'eseal_customer.parent_company_id')
                            //->where('eseal_customer.parent_company_id','=',$parent)
                            ->get()->all();
                    $temp = new \stdClass();
                    $temp->brand_name = 'Ebutor';
                    $temp->customer_id = 0;
                    $temp->parent_company_id = -1;
                    $ebutorTemp = $temp;
                    array_unshift($childCompanies, $ebutorTemp);
//                echo "<pre>";print_r($childCompanies);die;                          
                }
            }
            return $childCompanies;
        } catch (\ErrorException $ex) {
            die($ex);
        }
    }

    public function getAllCustomerDetails() {
        $result = DB::table('eseal_customer')
                ->where('customer_type_id', 1001)
                ->where('approved', 1)
                ->where('status', 1)
                ->select('customer_id', 'brand_name')
                ->get()->all();
        return $result;
    }

    public function getUserDetails($user_id) {
        $result = DB::table('users')->where('user_id', '=', $user_id)->get()->all();
        return $result;
    }

    // get location details
    public function prepareLocationData($manufacturerId) {
        if ($manufacturerId) {
            $locs = DB::Table('location_types')
                    //->join('eseal_customer', 'eseal_customer.customer_id', '=', 'location_types.manufacturer_id')
                    ->where('manufacturer_id', $manufacturerId)
                    ->select('location_types.location_type_name', 'location_types.location_type_id', 'location_types.manufacturer_id')
                    ->get()->all();

            $locas = DB::Table('locations')
                    ->where('manufacturer_id', $manufacturerId)
                    ->select('locations.location_id', 'locations.location_name', 'locations.manufacturer_id', 'locations.parent_location_id', 'locations.location_type_id', 'locations.location_email', 'locations.location_address', 'locations.location_details', 'locations.state', 'locations.region', 'locations.longitude', 'locations.latitude', 'locations.erp_code')
                    ->get()->all();

            $manu = DB::Table('eseal_customer')
                    ->where('customer_id', $manufacturerId)
                    ->select('customer_id', 'brand_name')
                    ->get()->all();
        } else {
            $locs = DB::Table('location_types')
                    ->select('location_types.location_type_name', 'location_types.location_type_id', 'location_types.manufacturer_id')
                    ->get()->all();

            $locas = DB::Table('locations')
                    ->select('locations.location_id', 'locations.location_name', 'locations.manufacturer_id', 'locations.parent_location_id', 'locations.location_type_id', 'locations.location_email', 'locations.location_address', 'locations.location_details', 'locations.state', 'locations.region', 'locations.longitude', 'locations.latitude', 'locations.erp_code')
                    ->get()->all();

            $manu = DB::Table('eseal_customer')
                    ->select('customer_id', 'brand_name')
                    ->get()->all();
        }

        return array('locs' => $locs, 'locas' => $locas, 'manu' => $manu);
    }

    public function getCustomerLogo($customerId) {
        return DB::table('users')->select('profile_picture')
                        ->where(array('user_id' => $customerId, 'is_active' => 1))->get()->all();
    }

    public function getManufacturerId() {
        $currentUserId = \Session::get('userId');
        $manufacturerDetails = DB::table('users')->where('user_id', $currentUserId)->first(array('customer_id'));
        $manufacturerId = -1;
        if (!empty($manufacturerDetails)) {
            $manufacturerId = $manufacturerDetails->customer_id;
        }
        return $manufacturerId;
    }

    public function getGdsOrders($cust_id) {
        $order_status = DB::select('select value from master_lookup where name="GDS orders"');
        // return $order_status;
        $order_status = $order_status[0]->value;

        if (!empty($cust_id)) {
            $result = DB::table('eseal_orders')
                    ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                    ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                    ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                    ->where('eseal_orders.order_type', '=', $order_status)
                    ->where('eseal_customer.customer_id', '=', $cust_id)
                    ->orderBy('eseal_orders.order_id', 'desc')
                    ->get()->all();
        } else {
            $result = DB::table('eseal_orders')
                    ->select('eseal_customer.*', 'eseal_orders.*', 'master_lookup.name')
                    ->Leftjoin('eseal_customer', 'eseal_orders.customer_id', '=', 'eseal_customer.customer_id')
                    ->Leftjoin('master_lookup', 'master_lookup.value', '=', 'eseal_orders.order_status_id')
                    ->where('eseal_orders.order_type', '=', $order_status)
                    ->orderBy('eseal_orders.order_id', 'desc')
                    ->get()->all();
        }
        return $result;
    }

    public function softDelete($manufacturerId, $tableName) {
        try {
            if ($tableName && $manufacturerId) {
                $updateFields['is_deleted'] = 1;
                if ($tableName == 'customer_products_plans') {
                    DB::table($tableName)->where('customer_id', $manufacturerId)->update($updateFields);
                } else {
                    DB::table($tableName)->where('manufacturer_id', $manufacturerId)->update($updateFields);
                }
            }
            return 1;
        } catch (\ErrorException $ex) {
            return $ex->getMessage();
        }
    }

    public function hardDelete($manufacturerId, $tableName) {
        try {
            if ($tableName && $manufacturerId) {
                if ($tableName == 'customer_products_plans') {
                    DB::table($tableName)->where('customer_id', $manufacturerId)->delete();
                } else {
                    DB::table($tableName)->where('manufacturer_id', $manufacturerId)->delete();
                }
            } else {
                return 0;
            }
            return 1;
        } catch (\ErrorException $ex) {
            return $ex->getMessage();
        }
    }

    public function getCountryData() {
        $countryData = DB::table('countries')->get(array('country_id', 'name'))->all();
        $countryArray = array();
        foreach ($countryData as $country) {
            $countryArray[$country->country_id] = $country->name;
        }
        return $countryArray;
    }

    public function getZones($countryId) {
        try {
            $zones = DB::table('zone')
                    ->where('country_id', '=', $countryId)
                    ->where('status', '=', 1)
                    ->get(array('zone_id', 'name'))->all();
            $zonesArray = array();
            $zonesArray[0] = 'Please select..';
            foreach ($zones as $zone) {
                $zonesArray[$zone->zone_id] = $zone->name;
            }
            return $zonesArray;
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
        }
    }

    public function getZonesByName($countryName) {
        try {
            $zones = DB::table('zone')
                    ->join('countries', 'countries.country_id', '=', 'zone.country_id')
                    ->where('countries.name', '=', $countryName)
                    ->where('zone.status', 1)
                    ->get(array('zone.zone_id', 'zone.name'))->all();
            $zonesArray = array();
            foreach ($zones as $zone) {
                $zonesArray[$zone->zone_id] = $zone->name;
            }
            return $zonesArray;
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
        }
    }
	public function getChannelName($channel_id) {
		return DB::table('mp')->where('mp_id', $channel_id)->pluck('mp_name');
    }
    
    public function getStates($country_id) {
        $states = DB::table('zone')
                ->where('country_id', $country_id)
                ->select('zone_id as state_id', 'name as state')
                ->get()->all();
        return $states;
    }
    
    public function getUserPermissions($userId)
    {
        try
        {
            $permissions = [];
            if($userId > 0)
            {
//                DB::enableQueryLog();
                $permissions = DB::table('user_permssion')
                        ->join('permission_level', 'permission_level.permission_level_id' , '=' , 'user_permssion.permission_level_id')
                        ->where(['user_permssion.user_id' => $userId])
                        ->select('permission_level.name', DB::raw('GROUP_CONCAT(user_permssion.object_id) AS object_id'))
                        ->groupBy('permission_level.name')
                        ->get()->all();
//                $last = DB::getQueryLog();
//                echo "<pre>";print_R(end($last));die;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $permissions;
    }
    
    public function getRefCode($prefix,$stateId='4033') {
        try
        {
            $response = '';
            if($prefix != '')
            {
                //$refNoArr = DB::connection('mysql')->selectFromWriteConnection("CALL reference_no(?,?);",[$stateId,$prefix])
		//changed by prasenjit @31st July               
                $stateCode = DB::table('zone')->select('code')->where('zone_id',$stateId)->first();
                $stateCode = isset($stateCode->code) ? $stateCode->code : "TS";
		        $response = Utility::getReferenceCode($prefix,$stateCode);
            }

            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return '';
        }
    }

    public function getSateIdByDcId($le_wh_id){
        $state_id = DB::table("legalentity_warehouses")->select("state")->where("le_wh_id",$le_wh_id)->first();
        return isset($state_id->state)?$state_id->state:4033;
    }

    public function getSateCodeById($state_id){
        $zone = DB::table("zone")->select("code")->where("zone_id",$state_id)->first();
        return isset($zone->code)?$zone->code:'TS';
    }
    
    /**     * [sendSMS description]
     * @param  [type] $number  [description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function sendSMS($referenceId, $userId, $number, $message, $state = null, $ishex = null, $dcs = null)
    {
        $postfields['user'] = Config::get('dmapi.SMS_USER');
        $postfields['senderID'] = Config::get('dmapi.SMS_SENDER_ID');
        $postfields['msgtxt'] = $message;
        $postfields['ishex'] = ($ishex == null) ? 0 : $ishex;
        $postfields['dcs'] = ($dcs == null) ? 0 : $dcs;
        $postfields['receipientno'] = (is_array($number)) ? implode(',', $number) : $number;
        $postfields['state'] = ($state == null) ? 4 : $state;
        $postfields = http_build_query($postfields);
        $response = $this->curlRequest(Config::get('dmapi.SMS_URL'), $postfields);
//        $userId = 0;
//        if (Session::has('userId')) {
//            $userId = Session::get('userId');
//        }
        $tableName = 'message_history';
        $insertData['reference_id'] = $referenceId;
        $insertData['requested_by'] = $userId;
        $insertData['request_type'] = 'sms';
        $insertData['number'] = $number;
        $insertData['message'] = json_encode($message);
        $insertData['response'] = json_encode($response);
        date_default_timezone_set('Asia/Kolkata');
        $insertData['created_on'] = new \MongoDate();
        $mongoRepo = new MongoRepo();
        $mongoRepo->insert($tableName, $insertData);
        return $response;
    }

    private function curlRequest($url, $postfields)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $buffer = curl_exec($ch);
        if (empty($buffer))
        {
            return false;
        } else
        {
            return $buffer;
        }
    }
    public function getInvCode($stateId ='4033',$le_id,$prefix){
        try{
            $response = '';
            $state_code = DB::table('zone')->select('code')->where('zone_id',$stateId)->first();
            $state_code = isset($state_code->code) ? $state_code->code : "TS";
             if($prefix != ''){

                 $response = DB::connection('mysql-write')->select(DB::raw("CALL  getSerialCode('".$state_code."','".$le_id."','".$prefix."')"));

               // $response = DB::selectFromWriteConnection(DB::raw("CALL  getSerialCode('".$state_code."','".$le_id."','".$prefix."')"));
            }
            return $response;

        } catch  (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return '';
        }

    }
}
?>
