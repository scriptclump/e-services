<?php

namespace App\Modules\LegalEntities\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
class Legalentity extends Model
{
    protected $table = 'legal_entities';
    protected $primaryKey = 'legal_entity_id';
    public $timestamps = false;
    
    public function saveLegalentity($data)
    {
        try
        {
            $legalEntityId = 0;
            $status = false; 
            $message = 'Unable to save data please contact admin'; 
            if(!empty($data))
            {
                $status = true; 
                $message = 'Data saved sucessfully';                
//                Log::info($data);
               // $this->legal_name = isset($data['username']) ? $data['username'] : '';
                $this->save();
                $legalEntityId = $this->legal_entity_id;
  //              Log::info($legalEntityId);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $legalEntityId;
    }
    
    public function saveBussinessData($data)
    {
        try
        {
            $legalEntityId = 0;
            $status = false; 
            $message = 'Unable to save data please contact admin'; 
            if(!empty($data))
            {
                $status = true; 
                $message = 'Data saved sucessfully';                
                //Log::info($data);
                $legalEntityId = isset($data['legal_entity_id']) ? $data['legal_entity_id'] : '';

                DB::table('legal_entities')->where('legal_entity_id', $legalEntityId)->update([
                    'business_legal_name' => isset($data['businessname']) ? $data['businessname'] : '',
                    'legal_entity_type_id' => 1001,
                    'business_type_id' => isset($data['business_type']) ? $data['business_type'] : '',
                    'address1' => isset($data['address1']) ? $data['address1'] : '',
                    'address2' => isset($data['address2']) ? $data['address2'] : '',
                    'city' => isset($data['city']) ? $data['city'] : '',
                    'state_id' => isset($data['state_id']) ? $data['state_id'] : '',
                    'pincode'=> isset($data['pincode']) ? $data['pincode'] : '',
                    'pan_number' => isset($data['pan']) ? $data['pan'] : '',
                    'tin_number' => isset($data['tin']) ? $data['tin'] : '',
                    'profile_completed' => 1
                ]);

                if(!empty($data['doc_files'])){
                    $type = 'pan_file';
                    $id = $this->saveFile($data['doc_files'],$legalEntityId,$type);
                }
                if(!empty($data['tin_file'])){
                     $type = 'tin_file';
                     $id = $this->saveFile($data['tin_file'],$legalEntityId,$type);
                }
                $data['business_type_name'] = DB::table('master_lookup')->where('value',$data['business_type'])->select('master_lookup_name')->first();
                $data['business_type_name'] = $data['business_type_name']->master_lookup_name;
                $data['state'] = DB::table('zone')->where('zone_id',$data['state_id'])->select('name as state')->first();
                $data['state'] = $data['state']->state;
              //  Log::info($legalEntityId);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode([
                        'id' => $legalEntityId,
                        'status' => $status,
                        'message' => $message,
                        'data' => $data]);
    }

    public function saveProfilePic($data,$id,$userId){
        try {
        if(!empty($data['file'])){
         $file = $data['file'];
         $fName = 'LE'.'_'.$id.'_'.'_'.$file->getClientOriginalName();
         $destinationPath = public_path().'/uploads/LegalEntities/profile_pics/'.$fName;
         $extension = $file->getClientOriginalExtension();
         if($extension == 'jpeg' || $extension == 'jpg' || $extension == 'png'){
         $path = '/uploads/LegalEntities/profile_pics/'.$fName;
         copy($file,$destinationPath);
         DB::table('users')
                        ->where('user_id',$userId)
                        ->update([
                          'profile_picture' => $path,
                    ]);
         }
         else{
            $path = '';
         }
        return $path;
      }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateDocument($data){
        try {
            $status = 0;
            $message = "Unable to update Docs";
            if(isset($data['pan_proof'])){
                $fName = 'LE'.'_'.$data['legal_entity_id'].'_'.$data['pan_proof']->getClientOriginalName();
                $destinationPath = public_path().'/uploads/LegalEntities/'.$fName;
                $extension = $data['pan_proof']->getClientOriginalExtension();
                $path = '/uploads/LegalEntities/'.$fName;
                copy($data['pan_proof'],$destinationPath);
                DB::table('legal_entity_docs')
                        ->where(['doc_id' => $data['pan_doc_id'], 'legal_entity_id' => $data['legal_entity_id']])
                        ->update([
                            'doc_url' => $path,
                            'doc_name' =>  $fName
                            ]);
                $status = 1;
                $message = "Document saved sucessfully";

            }
            if(isset($data['tin_proof'])){
                $fName = 'LE'.'_'.$data['legal_entity_id'].'_'.$data['tin_proof']->getClientOriginalName();
                $destinationPath = public_path().'/uploads/LegalEntities/'.$fName;
                $extension = $data['tin_proof']->getClientOriginalExtension();
                $path = '/uploads/LegalEntities/'.$fName;
                copy($data['tin_proof'],$destinationPath);
                DB::table('legal_entity_docs')
                        ->where(['doc_id' => $data['tin_doc_id'], 'legal_entity_id' => $data['legal_entity_id']])
                        ->update([
                            'doc_url' => $path,
                            'doc_name' =>  $fName
                            ]);
                $status = 1;
                $message = "Document saved sucessfully";
            }

            return json_encode([
                'status' => $status,
                'message' => $message
                ]);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function saveFile($file,$id,$type){
      //echo "<pre>"; print_r($file); print_r($id); die();
      if(!empty($file)){
         $fName = 'LE'.'_'.$id.'_'.$file->getClientOriginalName();
         $destinationPath = public_path().'/uploads/LegalEntities/'.$fName;
         $extension = $file->getClientOriginalExtension();
         $path = '/uploads/LegalEntities/'.$fName;
         copy($file,$destinationPath);
         $doc_id = DB::table('legal_entity_docs')
                        ->insertGetId([
                          'doc_name' => $fName,
                          'legal_entity_id' => $id,
                          'doc_url' => $path,
                          'doc_type' => $type
                    ]);
          if($doc_id){
            return $doc_id;
          }
          else{
            return 0;
          }
      }
    }

    public function getBusinessInfo($legal_entity_id){

        $businessInfo = DB::table('legal_entities')
                            ->leftjoin('zone','zone.zone_id','=','legal_entities.state_id')
                            ->leftjoin('master_lookup','master_lookup.value','=','legal_entities.business_type_id')
                            ->where('master_lookup.mas_cat_id',47)
                            ->where('legal_entity_id',$legal_entity_id)
                            ->select('zone.name as state','legal_entities.*','master_lookup.master_lookup_name as business_type')
                            ->first();
        return $businessInfo;
    }

    public function getBankDetails($legal_entity_id){
        $bank_details = DB::table('bank_details')
                            ->where('legal_entity_id',$legal_entity_id)
                            ->leftjoin('master_lookup as ma','ma.value','=','bank_details.account_type')
                            ->leftjoin('master_lookup as mb','mb.value','=','bank_details.currency_code')
                            ->select('bank_details.*','mb.master_lookup_name as currency_code_name','ma.master_lookup_name as account_type_name')
                            ->first();
        return $bank_details;
    }
    public function getBusinessTypes(){
        
      $business_types = DB::table('master_lookup')
                        ->select('master_lookup_name as business_type','master_lookup.value as business_type_id')
                        ->where('master_lookup.mas_cat_id',47)
                        ->get()->all();
      return $business_types; 
    }

    public function getCurrencyCode()
    {
        $currency_data = DB::table('master_lookup')
        ->select('master_lookup.master_lookup_name as currency_name','master_lookup.value as id')
        ->where('master_lookup.mas_cat_id','=','46')
        ->get()->all();
        return $currency_data;
    }

    public function getAccountType()
    {
        $account_data = DB::table('master_lookup')
        ->select('master_lookup.master_lookup_name as account_type_name','master_lookup.value as id')
        ->where('master_lookup.mas_cat_id','=','31')
        ->get()->all();
        return $account_data;
    }

    public function checkUnique($email) {
        try
        {
            if($email != '')
            {
                $result = '';
                $response = DB::table('users')->where('email_id', $email)->pluck('legal_entity_id')->all();
                if($response)
                {
                    $result = $response;
                }
                return $result;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function saveBankInfo($data){
        try {
            $message = 'Unable to save Bank Info';
            $status = false;
            $bank_detail_id = DB::table('bank_details')->where('legal_entity_id',$data['legal_entity_id'])->pluck('bank_detail_id')->all();
            if(empty($bank_detail_id)){
                DB::table('bank_details')->insertGetId([
                    'legal_entity_id' => $data['legal_entity_id'],
                    'account_name' => $data['account_name'],
                    'bank_name' => $data['bank_name'],
                    'account_no' => $data['account_no'],
                    'account_type' => $data['account_type'],
                    'ifsc_code' => $data['ifsc_code'],
                    'branch_name' => $data['branch_name'],
                    'city' => $data['b_city'],
                    'micr_code' => $data['micr_code'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $data['legal_entity_id'],
                    'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                else{
                    DB::table('bank_details')
                            ->where('legal_entity_id',$data['legal_entity_id'])
                            ->update([
                    'account_name' => $data['account_name'],
                    'bank_name' => $data['bank_name'],
                    'account_no' => $data['account_no'],
                    'account_type' => $data['account_type'],
                    'ifsc_code' => $data['ifsc_code'],
                    'city' => $data['b_city'],
                    'branch_name' => $data['branch_name'],
                    'micr_code' => $data['micr_code'],
                    'currency_code' => $data['currency'],
                    'updated_by' => $data['legal_entity_id'],
                    'updated_at' => date('Y-m-d H:i:s')
                    ]); 
                }
            $message = 'Data saved successfully.';
            $status = true;
            $data['account_type_name'] = DB::table('master_lookup')->where('value',$data['account_type'])->select('master_lookup_name')->first();
            $data['account_type_name'] = $data['account_type_name']->master_lookup_name;
            $data['currency_code_name'] = DB::table('master_lookup')->where('value',$data['currency'])->select('master_lookup_name')->first();
            $data['currency_code_name'] = $data['currency_code_name']->master_lookup_name;
            return json_encode([
                'status' => $status,
                'message' => $message,
                'data' => $data
                ]);
        
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getUsersOnRoleAndLeWareHouseId($le_wh_id,$role_id){

        $query = "SELECT users.`user_id`, 
        GetUserName(users.`user_id`, 2) AS `name`, 
        users.email_id, roles.`name` AS `role` 
        FROM `user_permssion` 
        LEFT JOIN legalentity_warehouses ON legalentity_warehouses.`bu_id` = user_permssion.`object_id` 
        LEFT JOIN users ON users.`user_id` = user_permssion.`user_id` 
        LEFT JOIN user_roles ON user_roles.`user_id` = users.`user_id` 
        LEFT JOIN roles ON roles.`role_id` = user_roles.`role_id` 
        WHERE le_wh_id = $le_wh_id AND permission_level_id = 6 AND user_roles.`role_id` = $role_id 
        GROUP BY user_permssion.user_id";

        $db_data = DB::select($query);

        if(count($db_data) > 0){

            $db_data = json_decode(json_encode($db_data),true);
            return $db_data;

        }else{

            return array();
        }
    }

    public function getActiveUsersRouting($le_wh_id,$role_id){

        $query = "SELECT users.`user_id`, 
        GetUserName(users.`user_id`, 2) AS `name`, 
        users.email_id, roles.`name` AS `role` 
        FROM `user_permssion` 
        LEFT JOIN legalentity_warehouses ON legalentity_warehouses.`bu_id` = user_permssion.`object_id` 
        LEFT JOIN users ON users.`user_id` = user_permssion.`user_id` 
        LEFT JOIN user_roles ON user_roles.`user_id` = users.`user_id` 
        LEFT JOIN roles ON roles.`role_id` = user_roles.`role_id` 
        WHERE permission_level_id = 6 AND user_roles.`role_id` = $role_id AND users.`is_active` = 1
        GROUP BY user_permssion.user_id";

        $db_data = DB::select($query);

        if(count($db_data) > 0){

            $db_data = json_decode(json_encode($db_data),true);
            return $db_data;

        }else{

            return array();
        }
    }
}
