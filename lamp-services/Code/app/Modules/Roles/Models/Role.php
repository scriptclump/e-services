<?php

namespace App\Modules\Roles\Models;
use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Log;
use Illuminate\Support\Facades\Cache;
use App\Central\Repositories\RoleRepo;

class Role extends Model
{
    protected $primaryKey = 'role_id';
    protected $_userId = 0;
    protected $_legalEntityId = 0;
    protected $_roleRepo;
    protected $_suppliers = [];
    protected $_user_list = [];
    protected $_fetched_reporting_ids = [];
    
    public function __construct() {
        $this->_userId = Session::get('userId');
        $this->_legalEntityId = Session::get('legal_entity_id');
        $this->_roleRepo = new RoleRepo();
    }
    
    public function getFilterData($permissionLevelId, $userId = null)
    {
        try
        {
            $respose = [];
            if($permissionLevelId > 0)
            {

                if(Session::get('userId') ){
                    $this->_userId = Session::get('userId');
                } else{
                    $this->_userId = 0;
                }

                if($userId)
                {
                    $currentUserId = $userId;
                }else{
                    $currentUserId = $this->_userId;
                }
                $getPermissionLevelName = $this->getPermissionLevelData($permissionLevelId);
//                DB::enableQueryLog();
                switch($getPermissionLevelName)
                {
                    case 'brand':
                        $legalEntityId = $this->getLegalEntityId($currentUserId);
                        $respose[$getPermissionLevelName] = $this->getBrandByUser($currentUserId, $legalEntityId);
                        break;
                    case 'category':                        
                        $respose[$getPermissionLevelName] = $this->getCategoryByUser($currentUserId, $permissionLevelId);
                        break;
                    case 'manufacturer':
                        $legalEntityId = $this->getLegalEntityId($currentUserId);
                        $respose[$getPermissionLevelName] = $this->getManufacturerByUser($currentUserId, $permissionLevelId, $legalEntityId);
                        break;
                    case 'supplier':
                        $legalEntityId = $this->getLegalEntityId($currentUserId);
                        $this->_suppliers[] = $currentUserId;
                        $temp = $this->getSuppliersByUser($currentUserId, $legalEntityId);
                        $respose[$getPermissionLevelName] = $temp;
                        break;
                    case 'products':
                        $legalEntityId = $this->getLegalEntityId($currentUserId);
                        $respose[$getPermissionLevelName] = $this->getProductsByUser($currentUserId, $permissionLevelId, $legalEntityId);
                        break;
                    case 'sbu':
                        $respose[$getPermissionLevelName] = $this->getWarehouseData($currentUserId, $permissionLevelId);
                        break;
                    case 'customer':
                        $customers = [];
                        $users = $this->getTeamByUser($currentUserId);                        
//                        if(count($users) > 1)
//                        {
//                            $i = 0;
//                            foreach($users as $userId)
//                            {
//                                $temp = [];
//                                echo "User ID => ".$userId."<br/>";
//                                if($userId != $currentUserId){
//                                    $temp = $this->getTeamByUser($userId);
//                                }
//                                echo "<pre> temp => ";print_R($temp);
//                                if(!empty($temp))
//                                {
//                                    $users = array_unique(array_merge($users, $temp));
//                                }
//                                echo "<pre> users => ";print_R($users);
//                                echo 'count => '.count($users).'<br/>';                                
//                            }
//                        }
                        $customers = $users;
                        $respose[$getPermissionLevelName] = $customers;
                        break;
                    default:
                        $respose[$getPermissionLevelName] = $this->getPermissionsByUser($currentUserId, $permissionLevelId);
                        break;
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($respose);
    }
    
    public function getPermissionLevelData($permissionLevelId)
    {
        try
        {
            $permissionName = '';
            if($permissionLevelId > 0)
            {
                $response = DB::table('permission_level')
                        ->where('permission_level_id', $permissionLevelId)
                        ->first(['name']);
                if(!empty($response))
                {
                    $permissionName = $response->name;
                }
            }
            return $permissionName;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getTeamByUser($userId)
    {
        try
        {
            $response[] = $userId;            
            if($userId > 0)
            {
                $childUserList = $this->getTeamList($userId);
                $response = array_merge($response, $childUserList);                
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getTeamList($userId)
    {
        try
        {
            $response = 0;
            $userList = [];
            $isSupportRole = $this->getSupportRole($userId);
            if(!empty($isSupportRole))
            {
                foreach($isSupportRole as $roleId)
                {
                    if($roleId > 0)
                    {
                        $usersList = DB::table('user_roles')
                            ->where(['role_id' => $roleId])
                            ->groupBy('user_id')
                            ->pluck('user_id')->all();
                        if(!empty($usersList))
                        {
                            foreach($usersList as $userId)
                            {
                                $tempArray = [];
                                if($userId > 0)
                                {
                                    $tempArray = DB::table('users')
                                            ->where('reporting_manager_id', $userId)
                                            ->pluck('user_id')->all();
                                    $userList = array_merge($userList, $tempArray);                                    
                                }
                            }
                        }
                    }
                }
                $response = $userList;
            }else{
                if($userId > 0)
                {
                    $response = DB::table('users')
                            ->where('reporting_manager_id', $userId)
                            ->pluck('user_id')->all();                
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getSuppliersByUser($userId, $legalEntityId,$dcid="",$roleid=[],$reportinglegalid=[],$ignoreusers=0)
    {
        try
        {

            $response = [];
            $isSupportRole=[];
            $isSupportRole = $this->getSupportRole($userId);
            $final_array = array();
            //$roleid=json_decode(json_encode($roleid),true);
            $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLB0001",$userId);
            if($userId > 0)
            {
                $ffusers=$this->_roleRepo->checkPermissionByFeatureCode('FFUSERS001',$userId);
                if($ffusers)
                {
//                    $response = DB::table('users')
//                        ->where(['reporting_manager_id' => $userId, 'legal_entity_id' => $legalEntityId])
//                        ->pluck('user_id')->all();

                    /*$response = DB::table('users')
                        ->where(['legal_entity_id' => $legalEntityId])
                        ->pluck('user_id')->all();*/

                      $roleid=json_decode(json_encode($roleid), True);
                      $reportinglegalid=json_decode(json_encode($reportinglegalid), True);
                      //db::enableQueryLog();
                        $response = DB::table('users');
                        if(!empty($dcid)){
                        $response =$response->Join('user_roles','users.user_id','=','user_roles.user_id'); 
                        $response =$response->Join('user_permssion','user_permssion.user_id','=','users.user_id');
                        $response =$response->Join('legalentity_warehouses','legalentity_warehouses.bu_id','=','user_permssion.object_id');
                    }
                        //$response =$response->where('legalentity_warehouses.legal_entity_id', $legalEntityId);
                        if(!empty($dcid)  && $ignoreusers==1){
                                $response =$response->where('legalentity_warehouses.le_wh_id', $dcid);
                            }
                            
                            if(!empty($reportinglegalid)){
                                if(!$globalAccess){
                                $response =$response->whereIn('users.reporting_manager_id', $reportinglegalid);
                                }
                            }elseif($ignoreusers==''){
                                $response =$response->where('users.reporting_manager_id', $userId);
                            }
                        if(!empty($roleid) && $ignoreusers==1 ){
                        $response =$response->whereIn('user_roles.role_id',$roleid);
                        //$response =$response->where('legalentity_warehouses.le_wh_id', $dcid);
                        $response =$response->where('user_permssion.permission_level_id',6);
                        
                        $response =$response->groupBy('user_roles.user_id');
                    }
                    $response=$response->where('is_active',1);
                    $response =$response->groupBy('users.user_id');
                        $response =$response->pluck('users.user_id')->all();
                    //dd(db::getQueryLog());exit;
                        //print_r($response);
                    return $response;
                }else{
                    if(!in_array($userId, $this->_fetched_reporting_ids))
                    {
                        $response = DB::table('users');
                           if($dcid!=''){
                            $response =$response->Join('legalentity_warehouses','legalentity_warehouses.legal_entity_id','=','users.legal_entity_id');
                           }
                            $response =$response->where(['users.reporting_manager_id' => $userId]);
                            if(!empty($dcid) && $ignoreusers==1){
                            $response =$response->where('legalentity_warehouses.le_wh_id', $dcid);
                        }
                            $response =$response->pluck('users.user_id')->all();
                        $this->_fetched_reporting_ids[] = $userId;
                    }
                }
                //print_r($response);exit;
                if(!empty($response))
                {
                    foreach($response as $supplierUserId)
                    {
                        $this->_suppliers[] = $supplierUserId;
                        $resulted_data = $this->getSuppliersByUser($supplierUserId, $legalEntityId,$dcid,$roleid,$reportinglegalid,$ignoreusers);
                        if(!empty($resulted_data)){
                        array_push($final_array, $resulted_data);
                       }
                    }
                    return isset($final_array[0])?$final_array[0]:[];
                }
            }
            //echo "<pre>";print_R($this->_suppliers);die;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getPermissionsByUser($userId, $permissionLevelId)
    {
        try
        {
            $response = [];
            if($userId > 0 && $permissionLevelId > 0)
            {
                $response = DB::table('user_permssion')
                        ->where(['user_id' => $userId, 'permission_level_id' => $permissionLevelId])
                        ->groupBy('object_id')
                        ->pluck('object_id')->all();
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $response;
    }    
    
    public function getBrandByUser($userId, $legalEntityId)
    {
        try
        {
            $response = [];
            if($userId > 0)
            {
               
               /* $response = DB::table('products')
                        ->join('brands', 'brands.brand_id', '=', 'products.brand_id')
                        ->join('user_permssion', 'user_permssion.object_id', '=', 'products.category_id')
                        ->where(['user_permssion.user_id' => $userId, 
                            'user_permssion.permission_level_id' => 8,
                            'brands.legal_entity_id' => $legalEntityId])
                        ->groupBy('brands.brand_id')
                        ->pluck('brands.brand_name', 'brands.brand_id')->all();*/
                $response = DB::table('brands')
                            // ->where(['brands.legal_entity_id' => $legalEntityId]) removing legal entity check
                            ->groupBy('brands.brand_id')
                            ->pluck('brands.brand_name', 'brands.brand_id')->all();
            
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getCategoryByUser($userId, $permissionLevelId)
    {
        try
        {
            $response = [];
            if($userId > 0 && $permissionLevelId > 0)
            {
                $allCategoryPermission = $this->getUserPermission($userId, $permissionLevelId);
                if($allCategoryPermission)
                {
                    $response = DB::table('categories')
                        ->pluck('categories.category_id')->all();
                }else{
                    $response = DB::table('categories')
                        ->join('user_permssion', 'user_permssion.object_id', '=', 'categories.category_id')
                        ->where(['user_permssion.user_id' => $userId, 
                            'user_permssion.permission_level_id' => $permissionLevelId])
                        ->groupBy('categories.category_id')
                        ->pluck('categories.category_id')->all();
                }                
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getProductsByUser($userId, $permissionLevelId, $legalEntityId)
    {
        try
        {
            $response = [];
            if($userId > 0 && $permissionLevelId > 0)
            {
                $allCategoryPermission = $this->getUserPermission($userId, 8);
                if($allCategoryPermission)
                {
                    $response = DB::table('products')
                        ->where(['products.legal_entity_id' => $legalEntityId])
                        ->pluck('products.product_title','products.product_id')->all();
                }else{
                    $response = DB::table('products')
                        ->join('user_permssion', 'user_permssion.object_id', '=', 'products.category_id')
                        ->where(['user_permssion.user_id' => $userId, 
                            'user_permssion.permission_level_id' => 8,
                            'products.legal_entity_id' => $legalEntityId])
                        ->groupBy('products.product_id')
                        ->pluck('products.product_title','products.product_id')->all();
                }
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getManufacturerByUser($userId, $permissionLevelId, $legalEntityId)
    {
        try
        {
            $response  = [];
            if($userId > 0 && $permissionLevelId > 0)
            {
                /*$response = DB::table('products')
                        ->join('brands', 'brands.brand_id', '=', 'products.brand_id')
                        ->join('user_permssion', 'user_permssion.object_id', '=', 'products.category_id')
                        ->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'brands.legal_entity_id')
                        ->where(['user_permssion.user_id' => $userId, 
                            'user_permssion.permission_level_id' => 8,
                            'legal_entities.legal_entity_type_id' => 1006])
                        ->groupBy('legal_entities.legal_entity_id')
                        ->pluck('legal_entities.business_legal_name', 'legal_entities.legal_entity_id')->all();*/
                     $response = DB::table('legal_entities')
                            // ->where(['parent_id' => $legalEntityId]) removing legal entity check for brands
                            ->where(['legal_entity_type_id' => 1006])
                            ->groupBy('legal_entity_id')
                            ->orderBy('business_legal_name','asc')
                            ->pluck('business_legal_name', 'legal_entity_id')->all();
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getLegalEntityId($userId) {
        try
        {
            $legalEntityId = 0;
            if($userId > 0)
            {
                $legalEntityData = DB::table('users')
                        ->where('user_id', $userId)
                        ->first(['legal_entity_id']);
                if(!empty($legalEntityData))
                {
                    $legalEntityId = property_exists($legalEntityData, 'legal_entity_id') ? $legalEntityData->legal_entity_id : 0;
                }
            }            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $legalEntityId;
    }
    
    public function getUserPermission($userId, $permissionLevelId)
    {
        try
        {
            $response = [];
            $allCategoryPermission = 0;
            if($userId > 0 && $permissionLevelId > 0)
            {
                $response = DB::table('user_permssion')
                        ->where(['user_id' => $userId, 'permission_level_id' => $permissionLevelId])
                        ->select(DB::raw('group_concat(object_id) as object_id'))
                        ->first();
                $categoryPermissionArray = [];                
                if(!empty($response))
                {
                    $categoryPermissionList = property_exists($response, 'object_id') ? $response->object_id : '';
                    if($categoryPermissionList != '')
                    {
                        $categoryPermissionArray = explode(',', $categoryPermissionList);
                        if(in_array(0, $categoryPermissionArray))
                        {
                            $allCategoryPermission = 1;
                        }
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $allCategoryPermission;
    }
    
    public function getAllPermissions($userId = null) {
        try
        {

            if(Session::get('userId') ){
                $this->_userId = Session::get('userId');
            } else{
                $this->_userId = 0;
            }

            if($userId)
            {
                $currentUserId = $userId;
            }else{
                $currentUserId = $this->_userId;
            }
            $response = DB::table('user_permssion')
                        ->where(['user_id' => $userId])
                        ->select('permission_level_id', DB::raw('GROUP_CONCAT(object_id) as object_id'))
                        ->first();
            //echo "<pre>";print_R($response);die;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function flushCache($flushTagType)
    {
        try
        {
            $userId = Session::get('userId');
            Log::error($flushTagType);
            Cache::tags(['ebutor', $flushTagType])->flush();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getSupportRole($userId)
    {
        try
        {
            $isSupportRole = [];
            $currentRoles = $this->_roleRepo->getMyRoles($userId);
            if(!is_array($currentRoles))
            {
                $currentRoles = explode(',', $currentRoles);
            }
            $isSupportRole = DB::table('roles')
                    ->whereIn('role_id', $currentRoles)
                    ->where('is_support_role', 1)
                    ->pluck('role_id')->all();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $isSupportRole;
    }
    
    public function getWarehouseData($currentUserId, $permissionLevelId, $active = 1) {
        try {
            $response = [];
            if ($currentUserId > 0 && $permissionLevelId > 0) {
                $globalFeature = $this->_roleRepo->checkPermissionByFeatureCode('GLB0001',$currentUserId);
                $inActiveDCAccess = $this->_roleRepo->checkPermissionByFeatureCode('GLBWH0001',$currentUserId);

                if ($active == 0) {
                    $inActiveDCAccess = 1;
                }

                $result = DB::table('user_permssion')
                        ->where(['user_id' => $currentUserId, 'permission_level_id' => $permissionLevelId])
                        ->groupBy('object_id')
                        ->pluck('object_id')->all();
                if (!empty($result)) {
                    $query = DB::table('legalentity_warehouses')
                            ->where('dc_type', '>', 0)->where('is_disabled',0);
                    if ($inActiveDCAccess == 0) { // if user dont have access to inactive dc's
                        $query->where(['status' => 1]); //query returns only active records
                    }
                    if (!$globalFeature) {
                        if (count($result) == 1 || in_array(0,$result)) {
                            if (isset($result[0]) && ($result[0] == 0 || in_array(0,$result))) {
                                $query = $query->whereIn('dc_type', [118001,118002]);
                                //$query = $query->orWhere('dc_type', 118002);
                            } else {
                                $query = $query->whereIn('bu_id', $result);
                            }
                        } else {
                            $query = $query->whereIn('bu_id', $result);
                        }
                        $data=DB::select(DB::raw("SET SESSION group_concat_max_len = 100000"));

                        $query = $query->select(DB::raw('GROUP_CONCAT(le_wh_id) as le_wh_id'), 'dc_type')
                                ->groupBy('dc_type')
                                ->get()->all();
                        if (!empty($query)) {
                            foreach ($query as $details) {
                                $response[$details->dc_type] = $details->le_wh_id;
                            }
                        }
                    } else if ($globalFeature) {
                        $query = DB::table('legalentity_warehouses')
                                ->where('dc_type', '>', 0)->where('is_disabled',0);
                        if ($inActiveDCAccess == 0) { // if user dont have access to inactive dc's
                            $query->where(['status' => 1]); //query returns only active records
                        }
                        $data=DB::select(DB::raw("SET SESSION group_concat_max_len = 100000"));

                        $query = $query->select(DB::raw('GROUP_CONCAT(le_wh_id) as le_wh_id'), 'dc_type')
                                ->groupBy('dc_type')
                                ->get()->all();
                        if (!empty($query)) {
                            foreach ($query as $details) {
                                $response[$details->dc_type] = $details->le_wh_id;
                            }
                        }
                    }
                }
            }
            return json_encode($response);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getUsersByLeId($data)
    {
        try
        {
            $leId = isset($data['le_wh_id']) ? explode(',', $data['le_wh_id']) : 0;
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            $roleId = isset($data['role_id']) ? $data['role_id'] : 0;
            $permissionLevelId = 6;
            $response = [];
            $userList = [];
            DB::enableQueryLog();
            $response = DB::table('user_permssion')
                    ->leftJoin('business_units', 'business_units.bu_id', '=', 'user_permssion.object_id')
                    ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.bu_id', '=', 'business_units.bu_id');
//                    ->whereIn('object_id', $leId);
            if($userId > 0)
            {
                $userList = $this->getTeamList($userId);
//                if(empty($userList))
//                {
//                    $userList[] = $userId;
//                }
                $userList[] = $userId;
                
                $response = $response->whereIn('user_permssion.user_id', array_unique($userList))
                    ->where(['user_permssion.permission_level_id' => $permissionLevelId]);
            }
            if($roleId > 0)
            {
//                $userList = $this->getTeamList($userId);
                $response = $response
                        ->leftJoin('user_roles', 'user_roles.user_id', '=', 'user_permssion.user_id')
                        ->leftJoin('roles', 'roles.role_id', '=', 'user_roles.role_id')
                        ->where('roles.role_id', $roleId)
                    ->where(['user_permssion.permission_level_id' => $permissionLevelId]);
            }
            if($leId != '')
            {
                $response = $response->whereIn('object_id', $leId);
            }
            $response = $response->groupBy('user_permssion.user_id')
                        ->select(DB::raw('IF(user_permssion.`object_id` = 0, "ALL", GROUP_CONCAT(legalentity_warehouses.lp_wh_name)) as warehouse_name'),
                                DB::raw('user_permssion.object_id as warehouse_id'),
                                'user_permssion.user_id')
                        ->get()->all();
//            Log::info(DB::getQueryLog());
            if(!empty($response))
            {
                $responseData = json_decode(json_encode($response), true);
                if(array_search('ALL', array_column($responseData, 'warehouse_name')) !== false) {
                    $allWarehouses = DB::table('legalentity_warehouses')
                            ->where(['status' => 1])
                            ->where('dc_type', '>', 0)
                            ->pluck('lp_wh_name')->all();
                    foreach($responseData as $index => $data)
                    {
                        if(isset($data['warehouse_name']) && $data['warehouse_name'] == 'ALL'){
                            $response[$index]->warehouse_name = implode(',', $allWarehouses);
                        }
                    }
                }
            }
            return json_encode($response);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

   public function GetWareHouses($dclist){
    $result=DB::table('legalentity_warehouses')
                ->select('le_wh_id','lp_wh_name','display_name')
                ->where(['legalentity_warehouses.dc_type'=>'118001'])
                ->where(['legalentity_warehouses.status'=>'1'])
                ->whereIn('legalentity_warehouses.le_wh_id',explode(',', isset($dclist['118001'])?$dclist['118001']:''))
                ->get()->all();
                return $result;
   } 


   public function GetHubs($hublist,$dclist=""){
    $dcs=explode(',', $dclist);
   // DB::enableQueryLog();
    $result=DB::table('legalentity_warehouses')
                ->Join('dc_hub_mapping','dc_hub_mapping.hub_id','=','legalentity_warehouses.le_wh_id')
                ->select('legalentity_warehouses.le_wh_id','legalentity_warehouses.lp_wh_name','dc_hub_mapping.dc_id')
                ->where(['legalentity_warehouses.dc_type'=>'118002'])
                ->where(['legalentity_warehouses.status'=>'1'])
                ->whereIn('dc_hub_mapping.dc_id',$dcs)
                ->whereIn('legalentity_warehouses.le_wh_id',explode(',', isset($hublist['118002'])?$hublist['118002']:''))
                ->get()->all();        
                //dd(DB::getQueryLog());exit;
                return $result;
   } 

    public function getProductGroups(){
     
     try{ 
          $result=DB::table('product_groups as pg')
                        ->select('pg.product_grp_ref_id as product_grp_id','pg.product_grp_name','b.brand_id')
                        ->join('products as p','pg.product_grp_ref_id','=','p.product_group_id')
                        ->join('brands as b','b.brand_id','=','p.brand_id')
                        ->orderBy('pg.product_grp_name','asc')
                        ->groupBy('product_grp_id')
                        ->get()->all();
           return $result;            
       } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
   }

    public function GetWareHouseByLeId($legalEntityId){
        $result=DB::table('legalentity_warehouses')
                ->select('le_wh_id','lp_wh_name','display_name')
                ->where(['legalentity_warehouses.dc_type'=>'118001'])
                ->where(['legalentity_warehouses.status'=>'1'])
                ->where('legalentity_warehouses.legal_entity_id',$legalEntityId)
                ->get()->all();
        return $result;
   }
    
}
