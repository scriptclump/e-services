<?php

namespace App\Modules\Seller\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class Sellers extends Model {

    public function showSellerList() {
        try {
            $user_id = Session::get('userId');
            $legal_id = DB::table('users')->where('user_id',$user_id)->select('legal_entity_id')->first();
            $sellerResponse = [];
            if ($user_id != 1) {
                    $sellerList = DB::table('legal_entities')
                            ->join('master_lookup', 'master_lookup.value', '=', 'legal_entities.business_type_id')
                            ->where('legal_entity_id', $legal_id->legal_entity_id)
                            ->select('legal_entities.legal_entity_id', 'legal_entities.business_legal_name', 'legal_entities.city', 'legal_entities.profile_completed', 'master_lookup.master_lookup_name as businesstype')
                            ->groupBY('legal_entities.legal_entity_id')
                            ->get()->all();
                if (!empty($sellerList)) {            
                    foreach ($sellerList as $sellerDetails) {
                        $legalEntityId = property_exists($sellerDetails, 'legal_entity_id') ? $sellerDetails->legal_entity_id : 0;
                        $sellerDetails->profile_completed = ($sellerDetails->profile_completed == 1) ? 'Active' : 'In-Active';

                        if ($legalEntityId > 0) {
                            $mpDetails = DB::table('seller_mp_details')
                                    ->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'seller_mp_details.legal_entity_id')
                                    ->join('mp', 'mp.mp_id', '=', 'seller_mp_details.mp_id')
                                    ->join('lp_warehouses', 'lp_warehouses.lp_wh_id', '=', 'seller_mp_details.warehouse_id')
                                    ->join('logistics_partners', 'logistics_partners.lp_id', '=', 'lp_warehouses.lp_id')
                                    ->select('seller_mp_details.se_detail_id', 'mp.mp_logo as channel', DB::raw("concat(logistics_partners.lp_legal_name,' ',lp_warehouses.state,' ', lp_warehouses.lp_wh_name) as fulfillment_name"), DB::raw("'Not Available' as last_order_sync"), DB::raw("'Not Available' as last_inventory_sync"), DB::raw("'Not Available' as connector_status"))
                                    ->where('seller_mp_details.legal_entity_id', $legalEntityId)
                                    ->get()->all();
                            if (!empty($mpDetails)) {
                                foreach ($mpDetails as $configDetails) {
                                    $sellerId = property_exists($configDetails, 'se_detail_id') ? $configDetails->se_detail_id : 0;
                                    $logo = property_exists($configDetails, 'channel') ? $configDetails->channel : '';
                                    if ($logo != '') {
                                        $logo = '<img src="' . \URL::to('/') . $logo . '" />';
                                    }
                                    $actions = '';
                                    $edit = '';
                                    $configDetails->channel = $logo;
                                    $actions = $actions . '<span style="padding-left:5px;">'
                                            . '<a href="edit/' . $legalEntityId . '/' . $sellerId . '">'
                                            . '<span class="badge bg-light-blue">'
                                            . '<i class="fa fa-pencil"></i></span></a></span>';
                                    $configDetails->actions = $actions;
                                    $sellerDetails->seller_accounts[] = json_decode(json_encode($configDetails), true);
                                }
                            }
                            $actions = '';
                            $actions = $actions . '<span style="padding-left:0px;">'
                                    . '<a href="/legalentity/viewProfile/' . $legalEntityId . '">'                                
                                    . '<i class="fa fa-pencil"></i></a></span>'
                                    .'<span style="padding-left:15px;">'
                                    . '<a  href="/seller/add/' . $legalEntityId . '">'
                                    . '<i class="fa fa-plus"></i></a></span>';
                                    
                            $sellerDetails->actions = $actions;
                            $sellerResponse[] = json_decode(json_encode($sellerDetails), true);
                        }
                    }
                }
            } else {
                    $sellerList = DB::table('legal_entities')
                            ->join('master_lookup', 'master_lookup.value', '=', 'legal_entities.business_type_id')
                            ->select('legal_entities.legal_entity_id', 'legal_entities.business_legal_name', 'legal_entities.city', 'legal_entities.profile_completed', 'master_lookup.master_lookup_name as businesstype')
                            ->groupBY('legal_entities.legal_entity_id')
                            ->get()->all();
                if (!empty($sellerList)) {            
                    foreach ($sellerList as $sellerDetails) {
                        $legalEntityId = property_exists($sellerDetails, 'legal_entity_id') ? $sellerDetails->legal_entity_id : 0;
                        $sellerDetails->profile_completed = ($sellerDetails->profile_completed == 1) ? 'Active' : 'In-Active';

                        if ($legalEntityId > 0) {
                            $mpDetails = DB::table('seller_mp_details')
                                    ->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'seller_mp_details.legal_entity_id')
                                    ->join('mp', 'mp.mp_id', '=', 'seller_mp_details.mp_id')
                                    ->join('lp_warehouses', 'lp_warehouses.lp_wh_id', '=', 'seller_mp_details.warehouse_id')
                                    ->join('logistics_partners', 'logistics_partners.lp_id', '=', 'lp_warehouses.lp_id')
                                    ->select('seller_mp_details.se_detail_id', 'mp.mp_logo as channel', DB::raw("concat(logistics_partners.lp_legal_name,' ',lp_warehouses.state,' ', lp_warehouses.lp_wh_name) as fulfillment_name"), DB::raw("'Not Available' as last_order_sync"), DB::raw("'Not Available' as last_inventory_sync"), DB::raw("'Not Available' as connector_status"))
                                    ->where('seller_mp_details.legal_entity_id', $legalEntityId)
                                    ->get()->all();
                            if (!empty($mpDetails)) {
                                foreach ($mpDetails as $configDetails) {
                                    $sellerId = property_exists($configDetails, 'se_detail_id') ? $configDetails->se_detail_id : 0;
                                    $logo = property_exists($configDetails, 'channel') ? $configDetails->channel : '';
                                    if ($logo != '') {
                                        $logo = '<img src="' . \URL::to('/') . $logo . '" />';
                                    }
                                    $actions = '';
                                    $edit = '';
                                    $configDetails->channel = $logo;
                                    $actions = $actions . '<span style="padding-left:5px;">'
                                            . '<a href="edit/' . $legalEntityId . '/' . $sellerId . '">'
                                            . '<span class="badge bg-light-blue">'
                                            . '<i class="fa fa-pencil"></i></span></a></span>';
                                    $configDetails->actions = $actions;
                                    $sellerDetails->seller_accounts[] = json_decode(json_encode($configDetails), true);
                                }
                            }
                            $actions = '';
                            $actions = $actions . '<span style="padding-left:15px;">'
                                    . '<a  href="/seller/add/' . $legalEntityId . '">'
                                    . '<i class="fa fa-plus"></i></a></span>';
                                    
                            $sellerDetails->actions = $actions;
                            $sellerResponse[] = json_decode(json_encode($sellerDetails), true);
                        }
                    }
                }        
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_decode(json_encode($sellerResponse), true);
    }

    public function getSellerDetails($sellerId) {
        try {
            $response = [];
            if ($sellerId > 0) {
                $sellerDetails = DB::table('seller_mp_details')
                        ->join('mp_configuration', 'mp_configuration.se_detail_id', '=', 'seller_mp_details.se_detail_id')
                        ->where('seller_mp_details.se_detail_id', $sellerId)
                        ->select('seller_mp_details.mp_id', 'seller_mp_details.sellername', 'seller_mp_details.mp_referance_name', 'seller_mp_details.market_place_user_name', 'seller_mp_details.market_place_password', 'seller_mp_details.warehouse_id', 'seller_mp_details.description', DB::raw("GROUP_CONCAT(mp_configuration.key_name, '=', mp_configuration.key_value) AS config_details"))
                        ->first();
                if (!empty($sellerDetails)) {
                    $sellerInfo = property_exists($sellerDetails, 'config_details') ? $sellerDetails->config_details : '';
                    $sellerCredentials = [];
                    if ($sellerInfo != '') {
                        preg_match_all("/([^,=]+)=([^,=]+)/", $sellerInfo, $result);
                        if (isset($result[1]) && isset($result[2])) {
                            $sellerCredentials = array_combine(array_map('trim', $result[1]), array_map('trim', $result[2]));
                        }
                    }
                    $sellerDetails->config_details = $sellerCredentials;
                    $response = json_decode(json_encode($sellerDetails));
                }
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $response;
    }

    public function getSellerData($id) {
        try {
            $data = DB::table('legal_entities as le')->leftJoin('zone', 'zone.zone_id', '=', 'le.state_id')->where('legal_entity_id', $id)->select('le.*', 'zone.name as state')->first();
            return $data;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateSeller($data, $id) {
        try {
            $status = false;
            $message = 'Unable to update..';
            DB::table('legal_entities')->where('legal_entity_id', $id)->update([
                'address1' => $data['address1'],
                'address2' => $data['address2'],
                'state_id' => $data['state_id'],
                'city' => $data['city'],
                'pincode' => $data['pincode']
            ]);
            $status = true;
            $message = 'Updated successfully';

            return json_encode([
                'status' => $status,
                'message' => $message
            ]);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function saveSellerData($input) {
        try {
            //$date = $date = date('Y-m-d H:i:s');
            $channelId = isset($input['channelId']) ? $input['channelId'] : 0;
            $legalEntityId = isset($input['legal_entity_id']) ? $input['legal_entity_id'] : 0;
            $sellerConfigInfo = $this->sellerConfig($channelId);

            $warehouses = DB::table('seller_mp_details')
                    ->insert(['mp_referance_name' => $input['channelreferancename'],
                'description' => $input['description'],
                'market_place_user_name' => $input['marketplaceusername'],
                'market_place_password' => $input['password'],
                'warehouse_id' => $input['wharehouseId'],
                'sellername' => $input['sellername'],
                'mp_id' => $input['channelId'],
				'status' => 1,
                'legal_entity_id' => $legalEntityId
                   
            ]);

            $getLastId = DB::getPdo()->lastInsertId();
            $getLastSellerName = DB::table('seller_mp_details')
                    ->select('mp_referance_name')
                    ->where('se_detail_id', $getLastId)
                    ->get()->all();

            foreach ($sellerConfigInfo as $sellerConfigInfos) {
                $field_name = $sellerConfigInfos->field_name;
                $field_code = $input[$sellerConfigInfos->field_code];

                $sellerConfig = DB::table('mp_configuration')
                        ->insert(['Key_name' => $field_name,
                    'Key_value' => $field_code,
                    'mp_id' => $input['channelId'],
                    'seller_id' => $legalEntityId,
                    'se_detail_id' => $getLastId]);
            }
            return $getLastSellerName;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function updateSellerDetials($input) {
        $channelId = isset($input['channelId']) ? $input['channelId'] : 0;
        $legalEntityId = isset($input['legal_entity_id']) ? $input['legal_entity_id'] : 0;
        $sellerConfigInfo = $this->sellerConfig($channelId);

        $update = DB::table('seller_mp_details')
                ->where('se_detail_id', $input['seller_id'])
                ->update(['sellername' => $input['sellername'],
            'warehouse_id' => $input['wharehouseId'],
            'mp_referance_name' => $input['channelreferancename'],
            'description' => $input['description'],
            'market_place_user_name' => $input['marketplaceusername'],
            'market_place_password' => $input['password']]);

        foreach ($sellerConfigInfo as $sellerConfigInfos) {
            /* DB::enableQueryLog();
              $query=DB::getQueryLog(); */
            $field_name = trim($sellerConfigInfos->field_name);
            $field_code = $input[trim($sellerConfigInfos->field_name)];
            $sellerConfig = DB::table('mp_configuration')
                    ->where('se_detail_id', $input['seller_id'])
                    ->where('Key_name', $field_name)
                    ->update([ 'Key_value' => $field_code]);
        }
        $getLastSellerName = DB::table('seller_mp_details')
                ->select('mp_referance_name')
                ->where('se_detail_id', $input['seller_id'])
                ->get()->all();
        return $getLastSellerName;
    }

    public function sellerConfig($channelId) {
        try {
            $sellerConfigInfo = DB::table('mp_config_fields')
                            ->select('field_code', 'field_name', 'input_type', 'is_required')
                            ->where('mp_id', $channelId)->get()->all();
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $sellerConfigInfo;
    }

  
    public function showChildSellerList($data) {
        try {
            $path = isset($data['path']) ? $data['path'] : '';
            if ($path != '') {
                $temp = explode(':', $path);
                $legalEntityId = isset($temp[1]) ? $temp[1] : 0;
                if ($legalEntityId > 0) {
                    return ($this->getSellerAccounts($legalEntityId));
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

   
     public function getSellerAccounts($legalEntityId) {
        try {
            if ($legalEntityId > 0) {               
                $mpDetails = DB::table('seller_mp_details')
                       // ->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'seller_mp_details.legal_entity_id')
                        ->join('mp', 'mp.mp_id', '=', 'seller_mp_details.mp_id')
                        //->join('lp_warehouses', 'lp_warehouses.lp_wh_id', '=', 'seller_mp_details.warehouse_id')
                        ->join('legalentity_warehouses', 'legalentity_warehouses.lp_wh_id', '=', 'seller_mp_details.warehouse_id')
                        ->select('seller_mp_details.se_detail_id', 'seller_mp_details.sellername', 'seller_mp_details.status as connector_status', 'mp.mp_logo as channel', 'legalentity_warehouses.lp_wh_name as fulfillment_name', DB::raw("'Not Available' as last_order_sync"), DB::raw("'Not Available' as last_inventory_sync"))
                        ->where('seller_mp_details.legal_entity_id', $legalEntityId)
                        ->groupBy('seller_mp_details.se_detail_id')
                        ->get()->all();                 
                if (!empty($mpDetails)) {
                    foreach ($mpDetails as $configDetails) {
                        $sellerId = property_exists($configDetails, 'se_detail_id') ? $configDetails->se_detail_id : 0;
                        $logo = property_exists($configDetails, 'channel') ? $configDetails->channel : '';
                        if ($logo != '') {
                            $logo = '<img src="' . \URL::to('/') . $logo . '" style="height:40px;width:100px;border: 1px solid #c3c3c3;"/>';
                        }
						
						 if($configDetails->connector_status==1)
                        {
                            $configDetails->connector_status='<i class="fa fa-check"></i>';
                        }
                        else{
                            $configDetails->connector_status = '<i class="fa fa-times"></i>';
                        }
                        $actions = '';
                        $edit = '';
                        $configDetails->channel = $logo;
                        $actions = $actions . '<span style="padding-left:5px;">'
                                .'<a href="edit/' . $legalEntityId . '/' . $sellerId . '">'                                                             .'<i class="fa fa-pencil"></i></a></span>'
                                .'<span style="padding-left:20px;" >'
                                . '<a href="javascript:void(0)" onclick="getLegalentity('.$sellerId.')">'
                                . '<i class="fa fa-trash-o"></i></a></span>';
                        $configDetails->actions = $actions;
                        $sellerDetails[] = $configDetails;
                    }
					return $sellerDetails;
                }

                
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
    public function deleteLegalEnitity($input)
    {
        try{
            
            $LegalEntity = DB::table('seller_mp_details')
                            ->join('mp_configuration', 
                                    'seller_mp_details.se_detail_id','=','mp_configuration.se_detail_id')
                             ->where('mp_configuration.se_detail_id','=',$input['legalEntity'])                                                      ->delete();
            return $LegalEntity;
            
                    
            
        }catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

}
