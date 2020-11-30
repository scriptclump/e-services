<?php

namespace App\models\Users;
use App\Central\Repositories\RoleRepo;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use URL;
class Users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    /**
     * [saveUsers To save user]
     * @param  [array] $data [user's info]
     * @return [array]       [returns array with user id, status & message]
     */
    public function saveUsers($data)
    {
        try
        {
            $id = 0;
            $status = false; 
            $message = 'Unable to save data please contact admin'; 
            if(!empty($data))
            {
                $status = true; 
                $message = 'Data saved sucessfully';                
                $this->lastname = isset($data['lastname']) ? $data['lastname'] : '';
                $this->firstname = isset($data['firstname']) ? $data['firstname'] : '';
                $this->mobile_no = isset($data['phone_number']) ? $data['phone_number'] : '';
                $this->email_id = isset($data['email']) ? $data['email'] : '';
                $this->legal_entity_id = isset($data['legal_entity_id']) ? $data['legal_entity_id'] : '';
                
                $this->save();
                $legal_entity_id = $this->legal_entity_id;
                $userId = $this->user_id;
                if($legal_entity_id)
                {
                    $id = DB::table('user_legalentity')->insertGetId([
                    'legal_entity_id' => $legal_entity_id,
                    'user_id' => $userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $userId
                    ]);
                    $this->sendEmail($legal_entity_id,$userId,$data);
                }
//                Log::info($legal_entity_id);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode([
                        'id' => $legal_entity_id,
                        'status' => $status,
                        'message' => $message]);
    }
    /**
     * [sendEmail To send email]
     * @param  [int] $legal_entity_id [legal entity id]
     * @param  [int] $userId          [user Id]
     * @param  [array] $data          [users information to whom we need to send mail]
     */
    public function sendEmail($legal_entity_id,$userId, $data) {
        try
        {
            if ($userId) {
                $roleRepo = new RoleRepo;
                $userId = $roleRepo->encodeData($userId);
                $legal_entity_id = $roleRepo->encodeData($legal_entity_id);
                $data['from'] = 'ebutor.buyer@gmail.com';
                $url = URL::asset('signup/' . $legal_entity_id. '/'. $userId );
                $link = $url;
                \Mail::send('emails.register', ['link' => $link, 'username' => $data['firstname'].' '.$data['lastname']], function($message) use ($data) {
                    $message->from($data['from'], 'Ebutor')->to($data['email'])->subject('Registration with FBE');
                });
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }
    /**
     * [savePassword To save password]
     * @param  [array] $data [it contains password & confirm password]
     * @return [array]       [array contains status & message]
     */
    public function savePassword($data){
        try {
                $status = false; 
                $message = 'Unable to save password. Please contact admin'; 
                if(!empty($data['set_password'])){
                    $password = $data['set_password'];
                    $confirm_password = $data['confirm_password'];
                    $st = strcmp($password, $confirm_password);
                    if($st == 0 ){
                        $this->where('user_id', $data['user_id'])->update(['password' => md5($password)]);
                        $status = true;
                        $message = "Password saved successfully.";
                    }
                    else{
                        $message = "Password mismatch. Please retry";
                    }
                }
                else{
                    $message = "Incorrect password type";
                }
                return json_encode([
                            'status' => $status,
                            'message' => $message]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }
    
    /**
     * [checkUnique check whether the email already exists or not]
     * @param  [string] $email [email]
     * @return [array]        [sends users list with the above email]
     */
    public function checkUnique($email) {
        try
        {
            if($email != '')
            {
                $result = 0;
                $response = DB::table('users')->where('email_id', $email)->pluck('user_id')->all();
                if($response)
                {
                    $data['email'] = $email;
                    $data['username'] = 'Test';
//                    $this->sendEmail($response, $data);
                    $result = $response;
                }
                return $result;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getUserId get user id]
     * @param  [string] $email [email id]
     * @return [array]        [users list]
     */
    public function getUserId($email) {
        try
        {
            if($email != '')
            {
                $result = 0;
                $response = DB::table('users')->where('email_id', $email)->first(['user_id']);
                if($response)
                {
                    $result = $response;
                }
                $result = $result->user_id;
                return $result;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getLegalEntityId get le id of user]
     * @param  [int] $userId [user id]
     * @return [int]         [le id of user]
     */
    public function getLegalEntityId($userId)
    {
        try
        {   
            $legal_entity_id = DB::table('users')->where('user_id', $userId)->first(['legal_entity_id']);
            $result = $legal_entity_id->legal_entity_id;
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getChannels get channel list]
     * @return [type] [description]
     */
    public function getChannels() {

        try {
            $channels = DB::table("mp")->select('mp_id', 'mp_name', 'mp_logo', 'mp_type', 'country_code')->get()->all();
            return $channels;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function getCompanys() {
        try {
            $data = \Input::all();
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            $legalEntityId = $this->getLegalEntityIdByUserId($userId);
            DB::enableQueryLog();
            $companysData = DB::table('legal_entities')
                    ->leftJoin('business_units', 'business_units.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                    ->select('legal_entities.legal_entity_id', 'legal_entities.logo', 'legal_entities.rel_manager', 'legal_entities.created_at', DB::raw('COUNT(business_units.`bu_id`) AS business_units'))
                    ->where(['legal_entities.legal_entity_type_id' => 1006, 'legal_entities.parent_id' => $legalEntityId])
                    ->groupBy('legal_entities.legal_entity_id')
                    ->get()->all();
//            echo "<pre>";print_R(DB::getQueryLog());die;
            return $companysData;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function getSuppliers() {
        try {
            $postData = \Input::all();
            $userId = isset($postData['user_id']) ? $postData['user_id'] : 0;
            $legalEntityId = $this->getLegalEntityIdByUserId($userId);
            if($userId > 0)
            {
                $productsData = DB::table('products')
                    ->select('product_id', 'sku', 'product_title', 'primary_image')
                    ->whereIn('manufacturer_id', $userId)
                    ->get()->all();
            }
            return $productsData;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
    
    public function getLegalEntityIdByUserId($userId)
    {
        try
        {
            $legalEntityId = \Session::get('legal_entity_id');
            if(isset($userId))
            {
                $UserId = $userId;
                if($UserId > 0)
                {
                    $legalEntityInfo = DB::table('users')->where('user_id', $UserId)->pluck('legal_entity_id')->all();
                    if(!empty($legalEntityInfo))
                    {
                        $legalEntityId = isset($legalEntityInfo[0]) ? $legalEntityInfo[0] : \Session::get('legal_entity_id');
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $legalEntityId;
    }
    public function getProducts() {
        try {
            $categoryId = 0;
            $postData = \Input::all();
//            echo "<pre>";print_R($data);die;
            $path = isset($postData['path']) ? $postData['path'] : '';
            if ($path != '') {
                $temp = explode(':', $path);
                $categoryId = isset($temp[1]) ? $temp[1] : 0;
            }
            $manufacturerEntities = isset($postData['manufacturerArray']) ? explode(',', $postData['manufacturerArray']) : [];
            $legalEntityId = \Session::get('legal_entity_id');
            if($categoryId > 0)
            {
                $productsData = DB::table('products')
                    ->select('product_id', 'sku', 'product_title', 'primary_image')
                    ->where(['category_id' => $categoryId])
                    ->whereIn('manufacturer_id', $manufacturerEntities)
                    ->get()->all();
            }
            return $productsData;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function getCategories() {
        try {
            $data = \Input::get();
            $legalEntityId = \Session::get('legal_entity_id');
            $legalEntities = isset($data['manufacturerArray']) ? explode(',', $data['manufacturerArray']) : [];
            if(strlen($data['bussinessUnitArray']))
            {
                $bussinessUnits = isset($data['bussinessUnitArray']) ? explode(',', trim($data['bussinessUnitArray'])) : [];
            }else{
                $bussinessUnits = [];
            }
            if(empty($bussinessUnits))
            {
                $categoriesData = DB::table('categories')
                    ->join('products', 'products.category_id', '=', 'categories.category_id')
                    ->join('categories as c2', 'c2.category_id', '=', 'categories.parent_id')
                    ->join('categories as c3', 'c3.category_id', '=', 'c2.parent_id')
                    ->select('products.category_id', 'categories.cat_name as product_class', 'c2.cat_name as sub_category', 'c3.cat_name as category')
                    ->whereIn('products.manufacturer_id', $legalEntities)
                    ->groupBy('products.category_id')
                    ->get()->all();
            }else{
                $categoriesData = DB::table('categories')
                    ->join('products', 'products.category_id', '=', 'categories.category_id')
                    ->join('categories as c2', 'c2.category_id', '=', 'categories.parent_id')
                    ->join('categories as c3', 'c3.category_id', '=', 'c2.parent_id')
                    ->join('business_units', 'business_units.legal_entity_id', '=', 'products.category_id')
                    ->select('products.category_id', 'categories.cat_name as product_class', 'c2.cat_name as sub_category', 'c3.cat_name as category')
                    ->whereIn('products.legal_entity_id', $legalEntities)
                    ->whereIn('business_units.manufacturer_id', $bussinessUnits)
                    ->groupBy('products.category_id')
                    ->get()->all();
            }
            return $categoriesData;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
    
    public function getUserPermission($permissionLevelId, $userId)
    {
        try
        {
            $result = [];
           // Log::info($userId);
           // Log::info($permissionLevelId);
            if($permissionLevelId > 0 && $userId > 0)
            {
                $result = DB::table('user_permssion')
                        ->where(['permission_level_id' => $permissionLevelId, 'user_id' => $userId])
                        ->pluck(DB::raw('group_concat(object_id) as ids'));
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }

    public function getBusinessUnit($data, $parentBuId) {
        try {
            $result = [];
            $response = [];
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            $userPermissions = [];
            if($userId)
            {
                $permissionLevelId = DB::table('permission_level')
                    ->where('name', 'sbu')
                    ->pluck('permission_level_id')->all();
                if(!empty($permissionLevelId))
                {
                    $permissionLevelId = isset($permissionLevelId[0]) ? $permissionLevelId[0] : 0;
                }
                DB::enableQueryLog();
                $userPermissions = $this->getUserPermission($permissionLevelId, $userId);
               // Log::info(DB::getQueryLog());
                //\Log::info('userPermissions');
                //\Log::info($userPermissions);
//                echo "<pre>";print_R($userPermissions);die;
                if(!empty($userPermissions))
                {
                    $userPermissions = isset($userPermissions[0]) ? array_map('intval', explode(',', $userPermissions[0])) : [];
                }
            }
            //\Log::info('userPermissions');
            //\Log::info($userPermissions);
            $buCollection = DB::table('business_units')->where('parent_bu_id', $parentBuId)->select('bu_id', 'bu_name')->get()->all();
            if(!empty($buCollection))
            {
                foreach($buCollection as $bu)
                {
                    $items = [];
                    $temp = [];
                    $temp['label'] = $bu->bu_name."<input type='hidden' name='bu_id' value ='".$bu->bu_id."' />";
                    $checkForChilds = $this->checkForChildBusinessUnits($bu->bu_id);                    
                    if($checkForChilds > 0)
                    {                        
                        $items['value'] = '/users/getbusinessunit/'.$bu->bu_id;
                        $items['label'] = 'Loading...';
                    }
                    if(!empty($items))
                    {
                        $temp['items'][] = $items;
                    }
                    if(!empty($temp))
                    {
                       // \Log::info($bu->bu_id);
                        if(!empty($userPermissions) && in_array($bu->bu_id, $userPermissions))
                        {
                            $temp['checked'] = true;
                        }
                        $response[] = $temp;
                    }
                }
            }
//            echo "<pre>";print_R($response);die;
            if(!empty($response))
            {
                $result = $response;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        echo json_encode($result, JSON_UNESCAPED_SLASHES);
    }
    
    public function checkForChildBusinessUnits($businessUnitId)
    {
        try
        {
            $result = 0;
            $buCollection = DB::table('business_units')
                    ->where('parent_bu_id', $businessUnitId)
                    ->select('bu_id', 'bu_name')
                    ->count();
            if($buCollection > 0)
            {
                $result = $buCollection;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }
    
    public function getSegmentMapping($data) {
        try {
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            $result = ["label" => "Test", "expanded" => "false"];
            $response = [];
            $segmentData = DB::table('segment_mapping')
                    ->join('master_lookup', 'master_lookup.value', '=', 'segment_mapping.value')
                    ->join('master_lookup_categories', 'master_lookup_categories.mas_cat_id', '=', 'master_lookup.mas_cat_id')
                    ->join('categories', 'categories.category_id', '=', 'segment_mapping.mp_category_id')
                    ->where(['master_lookup_categories.mas_cat_name' => 'Business Segments', 'categories.parent_id' => 0])
                    ->select('categories.category_id', 'categories.cat_name')
                    ->get()->all();
            $userPermissions = [];
            if($userId)
            {
                $permissionLevelId = DB::table('permission_level')
                    ->where('name', 'category')
                    ->pluck('permission_level_id')->all();
                if(!empty($permissionLevelId))
                {
                    $permissionLevelId = isset($permissionLevelId[0]) ? $permissionLevelId[0] : 0;
                }
                $userPermissions = $this->getUserPermission($permissionLevelId, $userId);
                if(!empty($userPermissions))
                {
                    $userPermissions = isset($userPermissions[0]) ? explode(',', $userPermissions[0]) : [];
                }
            }
            if(!empty($segmentData))
            {
                foreach($segmentData as $category)
                {
                    $items = [];
                    $temp = [];
                    $temp['label'] = $category->cat_name."<input type='hidden' name='category_id' value ='".$category->category_id."' />";                    
                    $checkForChilds = $this->checkForChildCategories($category->category_id);
                    if($checkForChilds > 0)
                    {
                        $items['value'] = '/users/getcategory/'.$userId.'/'.$category->category_id;
                        $items['label'] = 'Loading...';
                        $items['id'] = $category->category_id;
                    }
                    $temp['items'][] = $items;
                    if(!empty($temp))
                    {
                        if(!empty($userPermissions) && in_array($category->category_id, $userPermissions))
                        {
                            $temp['checked'] = true;
                        }
                    }
                    $response[] = $temp;
                }
            }
            if(!empty($response))
            {
                $result = $response;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        echo json_encode($result, JSON_UNESCAPED_SLASHES);
    }
    
    public function getCategoryById($userId, $categoryId) {
        try {
            $result = ["label" => "Test", "expanded" => "false"];
            if($categoryId > 0)
            {
                $userPermissions = [];
                if($userId)
                {
                    $permissionLevelId = DB::table('permission_level')
                        ->where('name', 'category')
                        ->pluck('permission_level_id')->all();
                    if(!empty($permissionLevelId))
                    {
                        $permissionLevelId = isset($permissionLevelId[0]) ? $permissionLevelId[0] : 0;
                    }
                    $userPermissions = $this->getUserPermission($permissionLevelId, $userId);
                    if(!empty($userPermissions))
                    {
                        $userPermissions = isset($userPermissions[0]) ? explode(',', $userPermissions[0]) : [];
                    }
                }
                $response = [];
                $segmentData = DB::table('categories')
                        ->where('categories.parent_id', $categoryId)
                        ->select('categories.category_id', 'categories.cat_name')
                        ->get()->all();
                if(!empty($segmentData))
                {
                    foreach($segmentData as $category)
                    {
                        $items = [];
                        $temp = [];
                        $temp['label'] = $category->cat_name."<input type='hidden' name='category_id' value ='".$category->category_id."' />";                        
                        $checkForChilds = $this->checkForChildCategories($category->category_id);
                        if($checkForChilds > 0)
                        {
                            $items['value'] = '/users/getcategory/'.$userId.'/'.$category->category_id;
                            $items['label'] = 'Loading...';                            
                            $items['id'] = $category->category_id;
                        }else{
//                            $items['expanded'] = 'false';
                        }
                        if(!empty($items))
                        {                            
                            $temp['items'][] = $items;
                        }
                        if(!empty($temp))
                        {
                            if(!empty($userPermissions) && in_array($category->category_id, $userPermissions))
                            {
                                $temp['checked'] = true;
                            }
                        }
                        $response[] = $temp;
                    }
                }
                if(!empty($response))
                {
                    $result = $response;
                }
            }
            
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        echo json_encode($result, JSON_UNESCAPED_SLASHES);
    }

    public function checkForChildCategories($categoryId)
    {
        try
        {
            $return = 0;
            if($categoryId)
            {
                $categoryCount = DB::table('categories')
                        ->where('categories.parent_id', $categoryId)
                        ->select('categories.category_id', 'categories.cat_name')
                        ->count();
                if($categoryCount > 0)
                {
                    $return = 1;
                }
            }            
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $return;
    }
    
    public function saveUser($data, $userId = 0) {
        if ($userId > 0) {
            DB::table('users')->where('user_id', $userId)->update($data);
        } else {
            if ((empty($data['getuserid']))) {
                unset($data['getuserid']);
                $userId = DB::table('users')->insertGetId($data);
            } else {
                unset($data['getuserid']);
                DB::table('users')->where('user_id', $userId)->update($data);
            }
        }
        return $userId;
    }

    public function getUsers($userId) {
        $result = DB::table('users')
                    ->select('user_id','firstname','lastname','email_id','mobile_no')
                    ->where('user_id','=',$userId)
                    ->first();
        return $result;
    }

}
