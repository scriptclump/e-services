<?php namespace App\Central\Repositories;     //Name space define 

/* 
    * This is class is used for access role permision based on user and feature
 */

use Token;
use User;
use DB;  //Include laravel db class
use Session;
use Log;
use UserActivity;
use App\Modules\Roles\Models\Role;

class RoleRepo {    // Define class name is RoleRepo
     protected $_salt;
     protected $roles;
     protected $_reporting_managers_list = [];
     protected $_parent_role_list = [];
     protected $_reporting_mangers = [];
     protected $_child_users = [];
     protected $_child_users_list = [];
     protected $_parent_roles;
     protected $rolesArray=[];

     public function __construct()
     {
         $this->_salt = 'e$e@1';
     }
     public function getAccess($userId, $featureId)
    {
        $result = DB::table('role_access')
                ->join('user_roles','role_access.role_id','=','user_roles.role_id')
                ->where('role_access.feature_id',$featureId)
                ->where('user_roles.user_id',$userId)
                ->count();
        return ($result > 0) ? TRUE : FALSE;
    }
    
    public function authenticateUser($email, $password,$md5_flag=1){
        if($md5_flag == 1){
            $password = md5($password);
        }
        $result = DB::table('users')
                ->join('user_roles', 'user_roles.user_id', '=', 'users.user_id')
                ->leftjoin('roles', 'roles.role_id', '=', 'user_roles.role_id')
                ->select('users.user_id', 'users.legal_entity_id', 'user_roles.role_id','users.profile_picture','users.firstname','users.lastname')
                ->where(array('email_id' => $email, 'password' => ($password), 'users.is_active' => 1, 'roles.is_active' => 1))
                ->get()->all();
        return $result;
    }
    
    public function getRolebyUserId($userId) {
        try
        {
            $result = DB::table('user_roles')
                    ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
                    ->select('user_roles.role_id', 'roles.parent_role_id', 'roles.name', 'roles.default_role', 'user_roles.user_roles_id')
                    ->where('user_id', $userId)
                    ->orderBy('user_roles.user_roles_id', 'DESC')
                    ->get()->all();
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $result;
    }
    
    public function getParentRolesbyRoleId($roleId, $legalEntityId) {
        try
        {
            if($roleId > 0 && $roleId != 1)
            {
                $this->roles[] = $roleId;
                if($legalEntityId == 0)
                {
                    $result = DB::table('roles')
                        ->where(['role_id' => $roleId])
                        ->first(['parent_role_id']);
                }else{
                    $result = DB::table('roles')
                        ->where(['role_id' => $roleId])
                        ->first(['parent_role_id']);
                }                
                if(!empty($result))
                {
                    $newRoleId = property_exists($result, 'parent_role_id') ? $result->parent_role_id : 0;
                    if(!in_array($newRoleId, $this->roles))
                    {
                        $this->getParentRolesbyRoleId($newRoleId, $legalEntityId);
                    }
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $this->roles;
    }

    public function getFeaturesByRoleId($roleId){
        return $result = DB::table('role_access')
                ->select('features.*')
                ->join('features','role_access.feature_id','=','features.feature_id')
                ->where(array('features.is_active'=>1,'features.is_menu'=>1, 'role_access.role_id'=>$roleId))
                ->orderby('features.sort_order','ASC')
                ->get()->all();
    }

    public function getRolesByFeatureId($featureId)
    {
        return $result = DB::table('role_access')
                ->select('roles.*')
                ->join('roles','role_access.role_id','=','roles.role_id')
                ->where(array('roles.is_active'=>1,'role_access.feature_id'=>$featureId))
                ->orderby('roles.role_id','ASC')
                ->get()->all();
    }
    
    public function checkActionAccess($userId,$featureCode)
    {        
        $result = DB::table('role_access')
                ->select('features.name')
                ->join('features','role_access.feature_id','=','features.feature_id')
                ->join('user_roles','role_access.role_id','=','user_roles.role_id')
                ->where(array('user_roles.user_id'=>$userId, 'features.feature_code'=>$featureCode))
                ->count();
       
        return ($result > 0) ? TRUE : FALSE;
    }
    
    public function getUsers($roleId = 0)
    {
        $legal_entity_id = Session::get('legal_entity_id');
        $userId = Session::get('userId');
        $currentRoleId = Session::get('roleId');
        $users = DB::table('user_roles')
                ->where('role_id', $roleId)
                ->pluck('user_id')->all();
        $user_ids = json_decode(json_encode($users), true);
        $result = DB::table('users')
                ->select('user_id', 'firstname', 'lastname', 'mobile_no', 'email_id', 'is_active', 'profile_picture')
                ->where('is_active', 1);
        if ($currentRoleId == 1)
        {
            $legal_entity_id = DB::table('users')->where(['user_id' => $userId])->pluck('legal_entity_id');
            $result = $result
//                    ->where(['legal_entity_id' => $legal_entity_id])
//                            ->where('users.created_by', $userId)
                    ->where('users.user_id', '!=', $userId)
                    ->whereNotIn('user_id', $user_ids);
        } else
        {
            if ($legal_entity_id != 1)
            {
                $result = $result->where(['legal_entity_id' => $legal_entity_id])
//                            ->where('users.created_by', $userId)
                        ->where('users.user_id', '!=', $userId)
                        ->whereNotIn('user_id', $user_ids);
            }
        }
        $response = $result->get()->all();
        if (empty($response))
        {
            $response = [];
        }
        return $response;
    }
    public function secoundGridInUsers($roleId){        
        $legal_entity_id = Session::get('legal_entity_id');
        $userId = Session::get('userId');
        $currentRoleId = Session::get('roleId');
        $users = DB::table('user_roles')
                ->where('role_id', $roleId)
                ->pluck('user_id')->all(); 
        $user_ids = json_decode(json_encode($users),true);
//        echo "<pre>";print_R($user_ids);die;
        $result = DB::table('users')
            ->select('user_id', 'firstname', 'lastname',  'mobile_no', 'email_id', 'is_active', 'profile_picture')
                ->where('is_active', 1);
        if($currentRoleId == 1)
        {
            $result = $result->whereIn('user_id', $user_ids);
        }else{
            if ($legal_entity_id != 1) {
                if($roleId > 1 && !empty($roleId)){
                    $result = $result->where('legal_entity_id', '=', $legal_entity_id)
//                            ->where('users.created_by', $userId)
                            ->whereNotIn('user_id', [$userId])
                            ->whereIn('user_id', $user_ids);
                }
            }
        }
        $respose = $result->get()->all();
        return $respose;
    }

   public function getUsersList($orderby_array,$filters= array(),$rowcount=0,$users_grid_fields,$usersDisplayList,$offset=0,$pageSize=20,$allbuids)
    {
        $userId = Session::get('userId');
        $legalEntityId = Session::get('legal_entity_id');
        // The below Condition is to give instant Super Admin Access for any Users (under certain Conditions)

        $instantSuperAdminAccess = 0;
        $instantSuperAdminAccess = DB::select("select count(object_id) as status from user_permssion where object_id = 1 and permission_level_id = 12 and user_id = ".$userId);
        $result=DB::statement("SET SESSION group_concat_max_len = 100000");
        $instantSuperAdminAccess = isset($instantSuperAdminAccess[0]->status)?$instantSuperAdminAccess[0]->status:0;

        $countrecords=0;
        DB::enableQueryLog();
        if ($instantSuperAdminAccess == 1) {
            $results = DB::table('users')
                    ->leftjoin('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                    ->leftjoin('roles', 'roles.role_id', '=', 'user_roles.role_id')
                    ->leftjoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'users.legal_entity_id')
                    ->where('legal_entities.legal_entity_type_id', 'not like', '3%');
            if(count($allbuids) ==1 && $allbuids[0] == 0){
                $results = $results;
            }else{
                $results = $results->leftjoin('user_permssion','user_permssion.user_id','=','users.user_id')
                    ->where('user_permssion.permission_level_id','=',6)
                    ->whereIn('user_permssion.object_id',$allbuids);
            }
           
        } else {
            $employeeList = $this->getUsersListBasedOnReportingManagerHierarchy($userId);
            if(isset($employeeList[0]) and $employeeList[0] == '')
                $employeeList[0] = [$userId];
            else
                $employeeList = array_merge([$userId],$employeeList);
            $results = DB::table('users')
                    ->leftjoin('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                    ->leftjoin('roles', 'roles.role_id', '=', 'user_roles.role_id')
                    ->leftjoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'users.legal_entity_id')
                    ->where(['users.legal_entity_id' => $legalEntityId]);
            if(count($allbuids) ==1 && $allbuids[0] == 0){
                $results = $results;
            }else{
                $results =$results->leftjoin('user_permssion','user_permssion.user_id','=','users.user_id')
                    ->where('user_permssion.permission_level_id','=',6)
                    ->whereIn('user_permssion.object_id',$allbuids);
            }
                if(!empty($employeeList)) {
                 $results = $results->whereIn('users.user_id', $employeeList); 
            }
        }
        $results = $results->select(
                DB::raw('group_concat(distinct roles.name SEPARATOR \', \') as rolename'),
                'users.user_id', 
                DB::raw('GetUserName(users.user_id,2) as full_name'), 
                'users.email_id', 
                DB::raw('GetUserName(users.reporting_manager_id, 2) as reporting_manager'),
                DB::raw("if(users.is_active = 1, 'Active', 'In-Active') as is_active"),
                'users.profile_picture',
                'users.mobile_no',
                'users.emp_code',
                'users.is_disabled',
                'users.otp');

            /*Filtering */
            if (isset($filters['full_name']) && !empty($filters['full_name'])) {
                $results->where(DB::raw('GetUserName(users.user_id, 2)'), $filters['full_name']['operator'], $filters['full_name']['value']);
            }
            if (isset($filters['rolename']) && !empty($filters['rolename'])) {
                
                $results->having('rolename', $filters['rolename']['operator'], $filters['rolename']['value']);
            }
            if (isset($filters['reporting_manager']) && !empty($filters['reporting_manager'])) {
                $results->where(DB::raw('GetUserName(users.reporting_manager_id, 2)'), $filters['reporting_manager']['operator'], $filters['reporting_manager']['value']);
            }
            if (isset($filters['email_id']) && is_array($filters['email_id'])) {
                $results->where('users.email_id', $filters['email_id']['operator'], $filters['email_id']['value']); 

            }
            if (isset($filters['mobile_no']) && !empty($filters['mobile_no'])) {
                $results->having(DB::raw('users.mobile_no'), $filters['mobile_no']['operator'], $filters['mobile_no']['value']);
            }
            if (isset($filters['emp_code']) && !empty($filters['emp_code'])) {
                $results->having(DB::raw('users.emp_code'), $filters['emp_code']['operator'], $filters['emp_code']['value']);
            }
            if (isset($filters['otp']) && !empty($filters['otp'])) {
                $results->having(DB::raw('otp'), $filters['otp']['operator'], $filters['otp']['value']);
            }

           if ($rowcount) {
                if($usersDisplayList == "activeUsersTab")
                $results->where('users.is_active','=',1);
                elseif ($usersDisplayList == "inActiveUsersTab")
                $results->where('users.is_active','=',0);
                $results->groupBy('users.user_id')->get()->all();
                $returnData = count($results->get()->all());
            } else {
                $offset = ($offset * $pageSize);
                if (!empty($orderby_array)) {
                    $order_query_field = $orderby_array[0]; //on which field sorting need to be done
                    $order_query_type = $orderby_array[1]; //sort type asc or desc
                    $order_by_type = 'desc';
                    if ($order_query_type == 'asc') {
                        $order_by_type = 'asc';
                    }
                    $results->orderby($order_query_field, $order_by_type);  //order by query
                } 
                if($usersDisplayList == "activeUsersTab")
                $results->where('users.is_active','=',1);
                elseif ($usersDisplayList == "inActiveUsersTab")
                $results->where('users.is_active','=',0);
                $results->groupBy('users.user_id');
                $results->skip($offset)->take($pageSize);
                $returnData = $results->get()->all();
                
            }

           
             return [
            "results" => $returnData,
            "user_access"=>$instantSuperAdminAccess
            
             ];

    }
    
    /*getting the users count */
    public function getUsersCount($status = 'All',$allbuids)
    {
        $userId = Session::get('userId');
        $legalEntityId = Session::get('legal_entity_id');
        $instantSuperAdminAccess = 0;
        $instantSuperAdminAccess = DB::select("select count(object_id) as status from user_permssion where object_id = 1 and permission_level_id = 12 and user_id = ".$userId);

        $instantSuperAdminAccess = isset($instantSuperAdminAccess[0]->status)?$instantSuperAdminAccess[0]->status:0;
        $countrecords=0;
 
        if ($instantSuperAdminAccess == 1) {
            $results = DB::table('users')
                    ->leftjoin('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                    ->leftjoin('roles', 'roles.role_id', '=', 'user_roles.role_id')
                    ->leftjoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'users.legal_entity_id')
                    ->where('legal_entities.legal_entity_type_id', 'not like', '3%');
            if(count($allbuids) ==1 && $allbuids[0] == 0){
                $results = $results;
            }else{
                $results = $results->leftjoin('user_permssion','user_permssion.user_id','=','users.user_id')
                    ->where('user_permssion.permission_level_id','=',6)
                    ->whereIn('user_permssion.object_id',$allbuids);
            }
        }else{
            $employeeList = $this->getUsersListBasedOnReportingManagerHierarchy($userId);
            if(isset($employeeList[0]) and $employeeList[0] == '')
                $employeeList[0] = [$userId];
            else
                $employeeList = array_merge([$userId],$employeeList);                
                $results =DB::table('users')
                    ->leftjoin('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                    ->leftjoin('roles', 'roles.role_id', '=', 'user_roles.role_id')
                    ->leftjoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'users.legal_entity_id')
                    ->where(['users.legal_entity_id' => $legalEntityId]);
            if(count($allbuids) ==1 && $allbuids[0] == 0){
                $results = $results;
            }else{
                $results = $results->leftjoin('user_permssion','user_permssion.user_id','=','users.user_id')
                    ->where('user_permssion.permission_level_id','=',6)
                    ->whereIn('user_permssion.object_id',$allbuids);
            }
                if(!empty($employeeList)) {
                $results->whereIn('users.user_id', $employeeList);
            }
        }
        $results->groupBy('users.user_id');
        if($status=='All'){
            $records = count($results->get()->all());
        }else if($status=='Active'){
            $records= count($results->where('users.is_active','=',1)->get()->all());
        }else if($status=='Inactive'){
            $records=count($results->where('users.is_active','=',0)->get()->all());
        }
        return $records;
    }

    public function getAllReportingManagers($userId)
    {
        try
        {
            $response = [];
            if($userId > 0)
            {
                if(!in_array($userId, $this->_reporting_managers_list))
                {
                    $response = DB::table('users')
                        ->where(['user_id' => $userId])
                        ->pluck('reporting_manager_id')->all();
                    $this->_reporting_managers_list[] = $userId;
                }
//                Log::info(DB::getQueryLog());
                if(!empty($response))
                {
                    foreach($response as $supplierUserId)
                    {
                        $this->_reporting_mangers[] = $supplierUserId;
                        $this->getAllReportingManagers($supplierUserId);
                    }
                }
            }
            return $this->_reporting_mangers;
            //echo "<pre>";print_R($this->_suppliers);die;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return [];
    }
    
    public function getReportingManagerId($userId)
    {
        try{
            $response = 0;
            if($userId > 0)
            {
                $userResult = DB::table('users')
                        ->where('user_id', $userId)
                        ->select('reporting_manager_id')
                        ->first();
                if(!empty($userResult) && property_exists($userResult, 'reporting_manager_id') && $userResult->reporting_manager_id != '')
                {
                    $response = $userResult->reporting_manager_id;
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }

    public function getUsersListBasedOnReportingManagerHierarchy($user_id,$is_active=NULL,$ignorelegalentityid=NULL)
    {

        if($user_id == "" or empty($user_id))
            return '';

        $legal_entity_id = Session::get('legal_entity_id');
        // If the request is from API, then we won`t have sessions
        if(empty($legal_entity_id)){
            $leId = DB::table("users")->where('user_id',$user_id)->pluck('legal_entity_id')->all();
            if(isset($leId[0]))
                $legal_entity_id = $leId[0];
            else
                return [];
        }
        $data=DB::statement("SET SESSION group_concat_max_len = 100000");
        $query = 
        'SELECT
            GROUP_CONCAT(user_id) AS users_list 
         FROM
            (SELECT *
            FROM
                users';

        if($is_active != NULL || $ignorelegalentityid==NULL){        
          $query.='  WHERE'; 
        }
        if($ignorelegalentityid==NULL){        
           $query.=' legal_entity_id = '.$legal_entity_id;
       }

       if($is_active != NULL && $ignorelegalentityid==NULL){

          $query.=" AND ";
       }
        
        if($is_active != NULL or $is_active == 1)
            $query.=" is_active = 1 ";
        
        if($ignorelegalentityid==NULL){
        $query.=
            ' ORDER BY reporting_manager_id, user_id) users_sorted,
                (SELECT @uid := ?) initialisation
            WHERE
                FIND_IN_SET(reporting_manager_id, @uid)
                AND length(@uid := concat(@uid, ",", user_id))';
        }else{
            $globalAccess = $this->checkPermissionByFeatureCode("GLB0001",$user_id);
            if(!$globalAccess){
                $useridarry=array();
                $useridarray[]=$user_id;
                $reportinguserids = $this->getMyLegalentityIdofReporting($useridarray);
                $reportinguserids=implode(',', $reportinguserids);
                $query.=
            ' and reporting_manager_id IN ('.$reportinguserids.')) users_sorted';
            /*,
                (SELECT @uid IN (?)) initialisation
            WHERE length(@uid := concat(@uid, ",", user_id))';*/
            }else{
              $query.=
            ') users_sorted order by user_id ASC';
            }

        }
        try {
            if($ignorelegalentityid==NULL){
             $result = DB::SELECT($query,[$user_id]);
            }else{
                if(!$globalAccess){
                $result = DB::SELECT($query);
                }else{
                    $result = DB::SELECT($query);
                }
            }
            if(empty($result))
                return '';

            return explode(',', $result[0]->users_list);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            return [];
        }
    }

    public function getUserInfoById($userId)
    {
        try{
            $response = 0;
            if($userId > 0)
            {
                $userResult = DB::table('users')
                        ->where('user_id', $userId)
                        ->select(['firstname','lastname','email_id','mobile_no','reporting_manager_id'])
                        ->first();
                if(!empty($userResult) && property_exists($userResult, 'reporting_manager_id') && $userResult->reporting_manager_id != '')
                {
                    $response = $userResult;
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getReportingManager($reportingManagerId)
    {
        try
        {
            $result = '';
            if($reportingManagerId > 0)
            {
                $response = DB::table('users')
                        ->where('user_id', $reportingManagerId)
                        ->pluck(DB::raw('concat(firstname, " ", lastname) as name'));
                if(!empty($response))
                {
                    $result = isset($response[0]) ? $response[0] : '';
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $result;
    }

    public function verifyUser($password, $userId)
    {   
        return $result = DB::table('users')
                            ->where(array('user_id'=>$userId,'is_active'=>1,'password'=>md5($password)))
                            ->count();
    }
    
     public function saveUser($data, $userId = 0) {
        if ($userId > 0) {
            DB::table('users')->where('user_id', $userId)->update($data);
            $this->updateDates('users', $userId, 'legal_entity_id', 0, 1, Session::get('userId'));
        } else {
            $userId = DB::table('users')->insertGetId($data);
        }
        return $userId;
    }
    
    public function setUserRole($roleId,$userId)
    {
        if(!empty($userId))
        {
            $Id = DB::table('user_roles')->where('user_id', $userId)->update(array('role_id'=>$roleId));
            $this->updateDates('user_roles', $userId, 'user_id', 0, 1, Session::get('userId'));
        }
        else
        {
            $Id = DB::table('user_roles')->insertGetId(array('role_id'=>$roleId, 'user_id'=>$userId));
        }
        return $Id;
    }

    public function getModuleFeatures(){
        /*$result= DB::table('master_lookup')
                ->select('master_lookup.name', '(select GROUP_CONCAT(feature_id) from features where master_lookup_id=master_lookup.value) as feature_id' , '(select GROUP_CONCAT(name) from features where master_lookup_id=master_lookup.value) as feature_name')
                ->join('lookup_categories','lookup_categories.id','=','master_lookup.category_id')
                ->where('lookup_categories.name','Modules')
                ->get()->all();*/
            $roleId = Session::get('roleId');    
            return DB::select(DB::raw("SELECT master_lookup.id,master_lookup.name, "
                     . "(select GROUP_CONCAT(features.feature_id) from features , role_access where master_lookup_id=master_lookup.value and features.feature_id=role_access.feature_id and role_access.role_id=".$roleId.") as feature_id, "
                     . "(select GROUP_CONCAT(features.name) from features, role_access where master_lookup_id=master_lookup.value  and features.feature_id=role_access.feature_id and  role_access.role_id=".$roleId.") as feature_name, "
                     . "(select GROUP_CONCAT(features.parent_id) from features, role_access where master_lookup_id=master_lookup.value  and features.feature_id=role_access.feature_id and  role_access.role_id=".$roleId.") as parent_id "
                     . "FROM `master_lookup` join lookup_categories on lookup_categories.id=master_lookup.mas_cat_id where lookup_categories.name='Modules'"));
        
    }
    
    public function getPermissionModules()
    {
        return DB::table('master_lookup')
                ->select('master_lookup_name','value')
                ->where(array('mas_cat_id' => 4, 'is_active' => 1))
                ->get()->all();                   
    }
    
    public function getFeatures(){
        return DB::table('features')
                ->where('is_active',1)
                ->orderBy('sort_order','asc')
                ->orderBy('name','asc')
                ->get()->all();
    }
    
    public function getFeatureByParentId($parentId)
    {
        return DB::table('features')
                ->where(array('is_active'=>1,'parent_id'=>$parentId))
                ->get()->all();
    }
    
    public function getFeatureswithChilds()
    {
        DB::enableQueryLog();
        $result = DB::table('features as F')
                 ->select('F.master_lookup_id','F.feature_id','F.name','F.parent_id','F1.feature_id as childId1',
                         'F1.name as childName1','F1.parent_id as childParent1',
                         'F2.feature_id as childId2','F2.name as childName2','F2.parent_id as childParent2')
                 ->leftJoin('features as F1','F.feature_id','=','F1.parent_id')
                 ->leftJoin('features as F2','F1.feature_id','=','F2.parent_id');
                 
        
        if(Session::get('roleId')==1)
           $result =  $result->where(array('F.is_active'=>1));
        else{
          $result =   $result->join('role_access as RC','F.feature_id','=','RC.feature_id');  
          $result =   $result->where(array('F.is_active'=>1,'RC.role_id'=>Session::get('roleId')));
        }
        $result = $result->groupBy('F2.feature_id');
        $result = $result->orderBy('F.name','ASC');
        $result = $result->orderBy('F1.feature_id','ASC');
        $result = $result->get()->all();
        
//        $last = \DB::connection('mysql')->getQueryLog();
//        print_r(end($last));
//        echo "<pre>";print_R($result);die;
        return  $result;       
    }
    
    public function getAssignedFeatures() {
        try
        {
            $roleId = Session::get('roleId');
            $roles = Session::get('roles');
            $result = [];
            if($roleId == 1) {
                $result = DB::table('features')
//                    ->join('features', 'features.feature_id', '=', 'role_access.feature_id')
//                    ->where(['features.parent_id' => 0, 'features.is_active' => 1])
                    ->where(['features.parent_id' => 0])
                    ->select('features.master_lookup_id', 'features.feature_id', 'features.name', 'features.parent_id', 'features.feature_code', 'features.is_menu', 'features.sort_order')
                    ->orderBy('features.sort_order')
                    ->orderBy('features.name', 'ASC')
                    ->groupBy('features.feature_id')
                    ->get()->all();
             } else {
                $result = DB::table('role_access')
                    ->join('features', 'features.feature_id', '=', 'role_access.feature_id')
//                    ->where(['role_access.role_id' => $roleId, 'features.parent_id' => 0, 'features.is_active' => 1])
//                    ->where(['role_access.role_id' => $roleId, 'features.parent_id' => 0])
                    ->whereIn('role_access.role_id', explode(',', $roles))
                    ->where(['features.parent_id' => 0])
                    ->select('features.master_lookup_id', 'features.feature_id', 'features.feature_code', 'features.name', 'features.parent_id', 'features.is_menu', 'features.sort_order')
                    ->orderBy('features.sort_order')
                    ->orderBy('features.name', 'ASC')
                    ->groupBy('features.feature_id')
                    ->get()->all();
            }
            return $result;
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function deleteUser($userId,$deleteUser)
    {
        $result['status'] = false;
        if($userId != '' and $userId != null and intval($userId) > 0 and $deleteUser != '' and $deleteUser != null)
        {
            if($deleteUser == "refresh")
            {
                DB::table('users')
                    ->where('user_id',$userId)
                    ->update(['is_active'=>1,'is_disabled'=>0]);
                $result['status'] = true;
            }
            if($deleteUser == "delete")
            {
                DB::table('users')
                    ->where('user_id',$userId)
                    ->update(['is_active'=>0,'is_disabled'=>1]);
                $result['status'] = true;
            }
        }
        return json_encode($result);
    }

    public function getAllChildIdsByUser($userId)
    {
        $result = DB::table('users')
            ->select('user_id')
            ->where([
                ['reporting_manager_id','=',$userId],
                ['legal_entity_id','=',2],
                ['is_active','=',1],
                ])
            ->get()->all();
        $result = json_decode(json_encode($result),true);
        return empty($result)?null:$result;
    }
    
    public function getChildFeatures($featureId) {
        try
        {
            $roleId = Session::get('roleId');
            $result = [];
            DB::enableQueryLog();
            if($roleId == 1)
            {
                $result = DB::table('features')
//                    ->join('features', 'features.feature_id', '=', 'role_access.feature_id')
//                    ->where(['features.parent_id' => $featureId, 'features.is_active' => 1])
                    ->where(['features.parent_id' => $featureId])
//                    ->whereNotIn('role_access.feature_id', [$featureId])
                    ->select('features.master_lookup_id', 'features.feature_id', 'features.feature_code', 'features.name', 'features.is_menu', 'features.parent_id')
                    ->groupBy('features.feature_id')
                    ->orderBy('features.sort_order')
                    ->get()->all();
            }else{
                $result = DB::table('role_access')
                    ->join('features', 'features.feature_id', '=', 'role_access.feature_id')
//                    ->where(['role_access.role_id' => $roleId, 'features.parent_id' => $featureId, 'features.is_active' => 1])
                    ->where(['role_access.role_id' => $roleId, 'features.parent_id' => $featureId])
                    ->whereNotIn('role_access.feature_id', [$featureId])
                    ->select('features.master_lookup_id', 'features.feature_id', 'features.name', 'features.feature_code', 'features.is_menu', 'features.parent_id')
                    ->orderBy('features.sort_order')
                    ->get()->all();
            }
//            Log::info(DB::getQueryLog());
//            $last = DB::getQueryLog();
//            echo "<pre>";print_r(end($last));die;
            return $result;
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function getPermissionFeature()
    {
        $modules = $this->getPermissionModules();        
        $results = array();        
//        $features = $this->getFeatureswithChilds();
        $features = $this->getAssignedFeatures();
//        echo "<pre>";print_R($features);die;
        $i =0;
        foreach ($modules as $module){
//            $results[$i] = json_decode(json_encode($module), true);
            $results[$i] = $module;
            $j=0;
            $finalArray = array();
            $tempArray = array();
            foreach ($features as $feature){                
                if($module->value == $feature->master_lookup_id){
                    $currentFeatureId = property_exists($feature, 'feature_id') ? $feature->feature_id : 0;
                    $currentParentFeatureId = property_exists($feature, 'parent_id') ? $feature->parent_id : 0;
                    $currentFeatureName = property_exists($feature, 'name') ? $feature->name : 0;                    
                    if(!in_array($currentFeatureId, $tempArray))
                    {
//                        $finalArray[$j] = json_decode(json_encode($feature), true);
                        $finalArray[$j] = $feature;
                    }
                    if($currentParentFeatureId == 0)
                    {
                        $tempChildArray = [];
                        $k = 0;                         
                        $childFeatures = $this->getChildFeatures($currentFeatureId);
                        if(!empty($childFeatures))
                        {
                            foreach($childFeatures as $childFeature)
                            {
                                $currentChildFeatureId = property_exists($childFeature, 'feature_id') ? $childFeature->feature_id : 0;
                                $currentChildParentFeatureId = property_exists($childFeature, 'parent_id') ? $childFeature->parent_id : 0;
                                $childChildFeatures = $this->getChildFeatures($currentChildFeatureId);
                                if(!in_array($currentFeatureId, $tempArray))
                                {
//                                    $tempChildArray[$k] = json_decode(json_encode($childFeature), true);
                                    $tempChildArray[$k] = $childFeature;                                    
                                }
                                $tempArray[] = $currentChildFeatureId;
                                if(!empty($childChildFeatures))
                                {
                                    $l = 0;
                                    $testTempArray = [];
//                                    echo "<pre>";print_R($childChildFeatures);die;
                                    foreach($childChildFeatures as $childChildFeaturesData)
                                    {
                                        $currentChildChildFeatureId = property_exists($childChildFeaturesData, 'feature_id') ? $childChildFeaturesData->feature_id : 0;
                                        if(!in_array($currentChildChildFeatureId, $tempArray))
                                        {
//                                            $tempChildArray[$k]['child'][] = json_decode(json_encode($childChildFeaturesData), true);                                            
                                            if(!empty((array)$childChildFeaturesData))
                                            {
//                                                $tempChildArray[$k]->child[] = $childChildFeaturesData;
                                                $testTempArray[$l] = $childChildFeaturesData;
                                            }
                                        }
                                        $tempArray[] = $currentChildFeatureId;                                        
                                        $currentChildChildChildFeatureId = property_exists($childChildFeaturesData, 'feature_id') ? $childChildFeaturesData->feature_id : 0;
                                        $currentChildChildChildParentFeatureId = property_exists($childChildFeaturesData, 'parent_id') ? $childFeature->parent_id : 0;
                                        $childChildChildFeatures = $this->getChildFeatures($currentChildChildChildFeatureId);
//                                        if(!in_array($currentChildChildChildFeatureId, $tempArray))
//                                        {
//        //                                    $tempChildArray[$k] = json_decode(json_encode($childFeature), true);
//                                            $tempChildArray[$k] = $childChildFeaturesData;
//                                        }
//                                        $tempArray[] = $currentChildChildChildFeatureId;
                                        if(!empty($childChildChildFeatures))
                                        {
                                            if(!in_array($currentChildChildChildFeatureId, $tempArray))
                                            {
    //                                            $tempChildArray[$k]['child'][] = json_decode(json_encode($childChildFeaturesData), true);
                                                if(!empty((array)$childChildFeaturesData))
                                                {
                                                    $testTempArray[$l]->child[] = $childChildFeaturesData;
//                                                    $tempChildArray[$k]->child[] = $childChildFeaturesData;
                                                }
                                            }
                                            $tempArray[] = $currentChildChildChildFeatureId;
//                                            echo "<pre>";print_R($childChildChildFeatures);die;
                                            foreach($childChildChildFeatures as $childChildFeaturesData5)
                                            {
                                                $currentChildChildFeatureId5 = property_exists($childChildFeaturesData5, 'feature_id') ? $childChildFeaturesData5->feature_id : 0;
//                                                echo "<pre>";
//                                                print_R($testTempArray);
//                                                print_R($childChildFeaturesData5);
//                                                die;
                                                if(!in_array($currentChildChildFeatureId5, $tempArray))
                                                {
        //                                            $tempChildArray[$k]['child'][] = json_decode(json_encode($childChildFeaturesData), true);
                                                    if(!empty((array)$childChildFeaturesData5))
                                                    {
                                                        $testTempArray[$l]->child[] = $childChildFeaturesData5;
                                                    }
                                                }
                                                $tempArray[] = $currentChildChildFeatureId5;
                                            }
                                        }
                                        $l++;
                                    }
                                    if(!empty($testTempArray))
                                    {
                                        if(!empty($tempChildArray[$k]))
                                        {
                                            $tempChildArray[$k]->child = $testTempArray;
                                        }
                                    }
                                }
                                $k++;
                            }
                            if(!empty((array)$tempChildArray))
                            {
//                                $finalArray[$j]['child'] = $tempChildArray;
                                $finalArray[$j]->child = $tempChildArray;
                            }
                        }else{
                            if(!empty((array)$childFeatures))
                            {
//                                $finalArray[$j]['child'] = json_decode(json_encode($childFeatures), true);
                                $finalArray[$j]->child = $childFeatures;
                            }   
                            $tempArray[] = $currentFeatureId;
                        }
                    }else{
                        if(!empty((array)$feature))
                        {
                            if(!in_array($currentFeatureId, $tempArray))
                            {
//                                $finalArray[$j]['child'] = json_decode(json_encode($feature), true);
                                if(!empty((array)$feature))
                                {
                                    $finalArray[$j]->child = $feature;
                                }
                            }   
                            $tempArray[] = $currentFeatureId;
                        }
                    }
                    $tempArray[] = $currentFeatureId;
                }
                $j++;
            }
//            $results[$i]['child'] = json_decode(json_encode($finalArray), true);
            if(!empty($finalArray))
            {
                $results[$i]->child = $finalArray;
            }            
            $i++; 
        }
//        \Log::info($results);
//        echo "<pre>";print_R($results);die;
        return $results;
    }

    public function SaveRole($data = array(), $role_id = 0) {
        if (!empty($data)) {
            $role_id = '';
            $oldData = [];
            if ($role_id == 0) {
                //echo "not saveing here";exit;                
                $insert_array = array();
                $insert_array['name'] = isset($data['role_name']) ? $data['role_name'] : '';                
                $insert_array['short_code'] = isset($data['short_code']) ? $data['short_code'] : '';                
                $insert_array['description'] = isset($data['description']) ? $data['description'] : '';
                $insert_array['is_active'] = isset($data['is_active']) ? $data['is_active'] : 1;
                $insert_array['legal_entity_id'] = isset($data['legal_entity_id']) ? $data['legal_entity_id'] : 0;
                $insert_array['role_type'] = isset($data['customer_type']) ? $data['customer_type'] : 0;
                $insert_array['parent_role_id'] = isset($data['parent_role_id']) ? $data['parent_role_id'] : 0;
                $insert_array['is_support_role'] = isset($data['is_support_role']) ? $data['is_support_role'] : 0;
                $insert_array['created_by'] = Session::get('userId');
                if(!empty($data['updateroleId'])){                    
                    DB::table('roles')
                           ->where('role_id', '=', $data['updateroleId'])
                           ->update($insert_array);
                    $role_id = $data['updateroleId'];
                }else{
                    $role_id = DB::table('roles')->insertGetId($insert_array);                    
                }
                $this->updateDates('roles', $role_id, 'role_id', 0, 1, Session::get('userId'));
            } else {
                $insert_array = array();
                $insert_array['name'] = isset($data['role_name']) ? $data['role_name'] : '';
                $insert_array['short_code'] = isset($data['short_code']) ? $data['short_code'] : '';
                $insert_array['description'] = isset($data['description']) ? $data['description'] : '';
                $insert_array['is_active'] = isset($data['is_active']) ? $data['is_active'] : 1;
                $insert_array['legal_entity_id'] = isset($data['legal_entity_id']) ? $data['legal_entity_id'] : 0;
                $insert_array['is_support_role'] = isset($data['is_support_role']) ? $data['is_support_role'] : 0;
                $insert_array['role_type'] = isset($data['customer_type']) ? $data['customer_type'] : 0;
                $oldData = DB::table('roles')->where('role_id', '=', $role_id)->get()->all();
                DB::table('roles')
                        ->where('role_id', '=', $role_id)
                        ->update($insert_array);
                $this->updateDates('roles', $data['updateroleId'], 'role_id', 0, 1, Session::get('userId'));
                if (isset($data['user_id']) && !empty($data['user_id'])) {
                    $userRolesOld = DB::table('user_roles')->where('role_id', '=', $role_id)->get()->all();
                    $response1 = UserActivity::userActivityLog("Roles", json_encode($data),__METHOD__,json_encode($userRolesOld), $role_id);
                    \Log::info($response1);
                    DB::table('user_roles')->where('role_id', '=', $role_id)->delete();
                    foreach ($data['user_id'] as $user_id) {
                        DB::table('user_roles')->insert(array('role_id' => $role_id, 'user_id' => $user_id));
                    }
                }
            }
            $respose2 = UserActivity::userActivityLog("Roles", json_encode($data),__METHOD__,json_encode($oldData), $role_id);
            \Log::info($respose2);
            return $role_id;            
        }
    }

    public function insertRoleperMission($data) {
        $expoldeData = explode(',', $data['feature_name']);
        $roleId = isset($data['role_id']) ? $data['role_id'] : 0;
        $updateChilds = isset($data['update_childs']) ? $data['update_childs'] : 0;
        if (isset($expoldeData) && !empty($expoldeData)) {
            DB::enableQueryLog();
            DB::table('role_access')->where('role_id', '=', $data['role_id'])->delete();
            foreach (array_unique($expoldeData) as $featureId) {
                $inserArray = ['role_id' => $roleId, 
                    'feature_id' => $featureId];
                $id = DB::table('role_access')->insertGetId($inserArray);
                $this->updateDates('role_access', $id, 'id', 1, 1, Session::get('userId'));
                $this->saveParentFeature($roleId, $featureId);
                if($updateChilds)
                {
                    $this->updateChildWithFeature($roleId, $featureId);
                }
            }
        }
    }
    
    public function updateDates($tableName, $primaryId, $primaryKey, $isCreated, $isUpdated, $userId)
    {
        try
        {
            $updateArray = [];
            if($isCreated)
            {
                $updateArray['created_at'] = date('Y-m-d H:i:s');
                $updateArray['created_by'] = $userId;
            }
            if($isUpdated){
                $updateArray['updated_at'] = date('Y-m-d H:i:s');
                $updateArray['updated_by'] = $userId;
            }
            DB::table($tableName)->where($primaryKey, $primaryId)->update($updateArray);
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage(). ' ' . $ex->getTraceAsString());
        }
    }
    
    public function saveParentFeature($roleId, $childFeatureId)
    {
        try
        {
            if($roleId > 0 && $childFeatureId > 0)
            {
                $parentFeatureData = DB::table('features')->where('feature_id', $childFeatureId)->first(['parent_id']);
                if(!empty($parentFeatureData))
                {
                    $parentFeatureId = property_exists($parentFeatureData, 'parent_id') ? $parentFeatureData->parent_id : 0;
                    if($parentFeatureId)
                    {
                        $latestRoleId = DB::table('role_access')->where(['role_id' => $roleId, 'feature_id' => $parentFeatureId])->first(['id']);
                        if(empty($latestRoleId))
                        {
                            $id = DB::table('role_access')->insertGetId(['role_id' => $roleId, 'feature_id' => $parentFeatureId]);
                            $this->updateDates('role_access', $id, 'id', 1, 1, Session::get('userId'));
                        }
                    }
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage(). ' ' . $ex->getTraceAsString());
        }
    }
    
    public function updateChildWithFeature($roleId, $featureId)
    {
        try
        {
            if($roleId > 0 && $featureId > 0)
            {
                $childList = DB::table('roles')
                        ->where('parent_role_id', $roleId)
                        ->pluck('role_id')->all();
                if(!empty($childList))
                {
                    foreach($childList as $childRoleId)
                    {
                        $roleAccessId = DB::table('role_access')
                                ->where(['role_id' => $childRoleId, 'feature_id' => $featureId])
                                ->pluck('id');
                        if(empty($roleAccessId))
                        {
                            $roleAccessArray['role_id'] = $childRoleId;
                            $roleAccessArray['feature_id'] = $featureId;
                            $roleAccessArray['created_by'] = Session::get('userId');
                            DB::table('role_access')->insert($roleAccessArray);
                        }
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function insertUsersroles($data)
    {
        $userRolesOld = DB::table('user_roles')->where('role_id', '=', $data['role_id'])->first([DB::raw('group_concat(user_id) as user_ids')]);
        if (isset($data['user_ids']))
        {
            $expoldeData = explode(',', $data['user_ids']);
            if (empty($data['user_ids']))
            {
                DB::table('user_roles')->where('role_id', '=', $data['role_id'])->delete();                
            } else
            {
                DB::table('user_roles')->where('role_id', '=', $data['role_id'])->delete();
                foreach ($expoldeData as $user_id)
                {
                    DB::table('user_roles')->insert(array('role_id' => $data['role_id'], 'user_id' => $user_id));
                }
            }
        }
        UserActivity::userActivityLog("Roles", json_encode($data),__METHOD__,json_encode($userRolesOld), $data['role_id']);
        return;
    }

    public function getPermissionIds($data){
         $result = DB::table('role_access')->select('feature_id')->where('role_id','=',$data['roleId'])->get()->all();
         return $result;
     }
     
    public function getReportingMangers($userId)
    {
        try
        {
            $response = [];
            $legalEntityId = Session::get('legal_entity_id');
//            DB::enableQueryLog();
            if($userId > 0)
            {
                $userRoleId = [];
                $roleId = DB::table('user_roles')
                        ->where(['user_id' => $userId])
                        ->select(DB::raw('group_concat(role_id) as role_ids'))
                        ->first();
                if(!empty($roleId))
                {
                    $userRoleId = property_exists($roleId, 'role_ids') ? $roleId->role_ids : '';
                    $userRoleId = explode(',', $userRoleId);
                }
                foreach($userRoleId as $roleID)
                {
                    if($roleID > 0)
                    {
                        $tempRoles = $this->getParentRolesbyRoleId($roleID, $legalEntityId);
                        if(!empty($tempRoles) && count($tempRoles) > 0)
                        {
                            $userRoleId = array_merge($userRoleId, $tempRoles);                            
                        }
                    }
                }
                array_unique($userRoleId);
                if(!empty($userRoleId))
                {
//                    $parentRoleId = DB::table('roles')->where(['role_id' => $userRoleId])->pluck('parent_role_id');
                    $userCollection = DB::table('users')
                            ->join('user_roles', 'user_roles.user_id', '=', 'users.user_id')
                            ->whereIn('user_roles.role_id', $userRoleId)
                            ->whereNotIn('users.user_id', [1, $userId])
                           // ->where('users.legal_entity_id', $legalEntityId)                            
                            ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                            ->orderBy('users.user_id')
                            ->groupBy('users.user_id')
                            ->get()->all();
                }                
            }else{
                $userCollection = DB::table('users')
                            //->where('users.legal_entity_id', $legalEntityId)                            
                            ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                            ->orderBy('users.user_id')
                            ->get()->all();
            }
            if(empty($userCollection))
            {
                $reportingManagerId = $this->getReportingManagerId($userId);
                $response = DB::table('users')
                        ->where('user_id', $reportingManagerId)
                        ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                        ->get()->all();
            }
            if(!empty($userCollection))
            {
                $response = $userCollection;
            }
//            \Log::info(DB::getQueryLog());
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }

    public function getRole($inheritRole = '') {
        try
        {
            $legal_entity_id = Session::get('legal_entity_id');
            $userId = Session::get('userId');            
            $roleId = Session::get('roleId');
            $roles = array_map('intval', explode(',', Session::get('roles')));
            $temp = [];
            // The below Condition is to give instant Super Admin Access for any Users (under certain Conditions)
            $instantSuperAdminAccess = 0;
            $instantSuperAdminAccess = DB::select("select count(object_id) as status from user_permssion where object_id = 1 and permission_level_id = 13 and user_id = ".$userId);
            $instantSuperAdminAccess = isset($instantSuperAdminAccess[0]->status)?$instantSuperAdminAccess[0]->status:0;
            if ($instantSuperAdminAccess > 0) {
                $result = DB::table('roles')
                        ->select('roles.short_code','roles.name', 'roles.role_id', 'roles.parent_role_id', 
                                'legal_entities.business_legal_name', DB::raw('IF(roles.is_support_role, "Yes", "No") as is_support_role'), DB::raw('GetUserName(roles.created_by, 2) as created_by'))
                        ->leftJoin('legal_entities', 'roles.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->whereNotIn('roles.role_id', [1])
                        ->where('roles.is_deleted', 0)
                        ->groupby('roles.role_id')
                        ->get()->all();
            } else {
                $temp = $this->getMyRolesList($userId);
                if($inheritRole)
                {
                    $ignoreRoleList = [1];
                    $temp = array_merge($temp, $roles);
                }else{
                    $roles[] = 1;
                    $ignoreRoleList = $roles;
                }
                $result = DB::table('roles')
                    ->select('roles.short_code','roles.name', 'roles.role_id', 'roles.parent_role_id', 
                            DB::raw('IF(roles.is_support_role, "Yes", "No") as is_support_role'), DB::raw('GetUserName(roles.created_by, 2) as created_by'))
                    ->where(['roles.is_active' => 1, 'roles.legal_entity_id' => $legal_entity_id, 'roles.is_deleted' => 0])
                    ->whereNotIn('role_id', array_unique($ignoreRoleList))
                    ->whereIn('role_id', array_unique($temp))
                    ->groupBy('roles.role_id')
                    ->orderBy('roles.role_id', 'ASC')
                    ->get()->all();
            }
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $result;
    }
    
    public function getAllParentRoles($roleId)
    {
        try
        {
            $response = [];
            if($roleId > 0)
            {
                if(!in_array($roleId, $this->_parent_role_list))
                {

                    $response = DB::table('roles')
                        ->where(['role_id' => $roleId])
                        ->first(['parent_role_id']);
                    $this->_parent_role_list[] = $roleId;
                }
                if(!empty($response))
                {
                    $this->_parent_roles[] = $roleId;
                    foreach($response as $roleId)
                    {
                        $this->_parent_roles[] = $roleId;
                        $this->getAllParentRoles($roleId);
                    }
                }else{
                    $this->_parent_roles[] = $roleId;
                }
            }
            return $this->_parent_roles;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
        
    public function getMyRolesList($userId)
    {
        try
        {
            $legal_entity_id = Session::get('legal_entity_id');
            $rolesList = [];
            if($userId > 0)
            {
                $rolesList = DB::table('user_roles')
                        ->leftJoin('roles', 'roles.role_id', '=', 'user_roles.role_id')
                        ->where(['user_roles.user_id' => $userId, 'roles.is_deleted' => 0, 'roles.legal_entity_id' => $legal_entity_id])
                        ->pluck('user_roles.role_id')->all();
                $myRoles = DB::table('roles')
                        ->where(['roles.created_by' => $userId, 'roles.is_deleted' => 0, 'roles.legal_entity_id' => $legal_entity_id])
                        ->pluck('roles.role_id')->all();
                $finalRolesList = array_unique(array_merge($rolesList, $myRoles));                
                if(!empty($finalRolesList))
                {
                    $tempRoles = [];
                    $rolesCompleted = [];
                    foreach($finalRolesList as $roleId)
                    {
                        $tempRoleList = $this->getAllChildRoles($roleId, $legal_entity_id, $tempRoles, $rolesCompleted, []);
                        $tempRoles = array_unique(array_merge($tempRoles, $tempRoleList));
                    }
                    $rolesList = $tempRoles;
//                    $rolesList = DB::table('roles')
//                        ->whereIn('roles.parent_role_id', $finalRolesList)
//                        ->where(['roles.is_deleted' => 0, 'roles.legal_entity_id' => $legal_entity_id])
//                        ->pluck('roles.role_id')->all();
                }
            }
            return $rolesList;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getAllChildRoles($roleId, $legal_entity_id, $tempRoles, $rolesCompleted, $rolesList)
    {
        try
        {
            if($roleId > 0 && !in_array($roleId, $rolesCompleted))
            {
                $rolesCompleted[] = $roleId;
                $tempRoles[] = $roleId;
                $rolesList = DB::table('roles')
                        ->where('roles.parent_role_id', $roleId)
                        ->where(['roles.is_deleted' => 0, 'roles.legal_entity_id' => $legal_entity_id])
                        ->pluck('roles.role_id')->all();                
                if(!empty($rolesList))
                {
                    $tempRoles = array_unique(array_merge($tempRoles, $rolesList));
                    foreach($rolesList as $roleId1)
                    {
                        $temp2 = $this->getAllChildRoles($roleId1, $legal_entity_id, $tempRoles, $rolesCompleted, $rolesList);
                        $tempRoles = array_unique(array_merge($tempRoles, $temp2));
                    }
                }
            }else{
                if(is_array($rolesList) && !empty($rolesList))
                {
                    $tempRoles = array_unique(array_merge($tempRoles, $rolesList));
                }
            }
            return $tempRoles;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
        
    public function getParentRoleId($roleId, $inheritRole)
    {
        try{
            $response = 0;
            if($roleId > 0)
            {
                $parentRoleId = DB::table('roles')->where('role_id', $roleId)->select('parent_role_id')->first();
                if(!empty($parentRoleId))
                {
                    $response = $parentRoleId->parent_role_id;
                }
            }
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getParentRoleName($parentRoleId)
    {
        try
        {
            $parentRoleName = '';
            if($parentRoleId > 0)
            {
                $roleData = DB::table('roles')->where(['role_id' => $parentRoleId])->pluck('name')->all();
                if(!empty($roleData))
                {
                    $parentRoleName = isset($roleData[0]) ? $roleData[0] : '';
                }
            }
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $parentRoleName;
    }
    
    public function getRolesList($legalEntityId, $roleId) {
        try
        {
            $response = [];
            $userId = Session::get('userId');
            if($roleId > 0)
            {
                if($userId == 1)
                {
                    $response = DB::table('roles')
//                        ->where('legal_entity_id', $legalEntityId)
                        ->whereNotIn('role_id', [$roleId])
                        ->select('role_id', 'name')
                        ->get()->all();
                }else{
                    $response = DB::table('roles')
                        ->where('legal_entity_id', $legalEntityId)
                        ->whereNotIn('role_id', [$roleId])
                        ->select('role_id', 'name')
                        ->get()->all();
                }                
            }
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }

    public function getUsersByRole($roleName)
    {
        $legalEntityId = Session::get('legal_entity_id');
        $result = DB::table('roles')
                ->select('roles.name', 'roles.role_id', DB::raw("concat(users.firstname, ' ', users.lastname) as username"), 'users.user_id', 'users.email_id', 'users.mobile_no')
                ->Join('user_roles', 'roles.role_id', '=', 'user_roles.role_id')
                ->Join('users', 'user_roles.user_id', '=', 'users.user_id')
                ->where(['users.is_active' => 1, 'roles.is_deleted' => 0, 'roles.legal_entity_id' => $legalEntityId]);
        
        if(is_array($roleName))
           $result = $result->whereIn('roles.name', $roleName);
        else
           $result = $result->where('roles.name', $roleName);

        // Checking the Global Access to View & Assign all the Users
        $globalAccess = $this->checkPermissionByFeatureCode("GLB0001");
        if(!$globalAccess){
            // If the logged in User doesnot have access then we
            // restrict him with specific legal entity users
            $legalEntityId = Session::get('legal_entity_id');
            $result = $result->where('users.legal_entity_id',$legalEntityId);
        }

        $usersList =
            $result
                ->groupBy('users.user_id')
                ->orderBy(DB::raw("concat(users.firstname, ' ', users.lastname)"), 'ASC')
                ->get()->all();
        return $usersList;
    }
    public function getUsersByRoleCode($roleCode)
    {
        $result = DB::table('roles')
                ->select('roles.name', 'roles.role_id', DB::raw("concat(users.firstname, ' ', users.lastname) as username"), 'users.user_id', 'users.email_id', 'users.mobile_no')
                ->Join('user_roles', 'roles.role_id', '=', 'user_roles.role_id')
                ->Join('users', 'user_roles.user_id', '=', 'users.user_id')
                ->where(['users.is_active' => 1, 'roles.is_deleted' => 0]);
        
        if(is_array($roleCode))
           $result = $result->whereIn('roles.short_code', $roleCode);
        else
           $result = $result->where('roles.short_code', $roleCode);
        
        // Checking the Global Access to View & Assign all the Users
        $globalAccess = $this->checkPermissionByFeatureCode("GLB0001");
        if(!$globalAccess){
            // If the logged in User doesnot have access then we
            // restrict him with specific legal entity users
            $legalEntityId = Session::get('legal_entity_id');
            $result = $result->where('users.legal_entity_id',$legalEntityId);
        }

        $usersList =
            $result
                ->groupBy('users.user_id')
                ->orderBy(DB::raw("concat(users.firstname, ' ', users.lastname)"), 'ASC')
                ->get()->all();
        return $usersList;
    }

    public function getRoles()
    {
        return DB::table('roles')
                ->select('roles.name','roles.role_id')
                ->where('roles.role_id',Session::get('roleId'))
                ->orWhere('roles.parent_role_id',Session::get('roleId'))
                ->get()->all();
    }
    
    public function getRolesCount($roleId)
    {        
        $legal_entity_id = Session::get('legal_entity_id');
        $userId = Session::get('userId');
        if (Session::get('roleId') == 1) {
            $userscount = DB::table('user_roles')
                ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                ->where(['user_roles.role_id' => $roleId])
                ->count();
        }else{
            $userscount = DB::table('user_roles')
                ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                ->where(['user_roles.role_id' => $roleId, 
                    'users.is_active' => 1, 
                    'users.legal_entity_id' => $legal_entity_id])
                ->count();
        }
        return $userscount;
    }

   public function DeleteRole($role_id) {
        DB::table('role_access')->where('role_id','=',$role_id)->delete();
        DB::table('user_roles')->where('role_id','=',$role_id)->delete();
        DB::table('roles')->where('role_id', $role_id)->delete();
        return TRUE;
    }
    
    public function getRoleById($role_id){
        
//        return DB::select(DB::raw("SELECT roles.role_id,roles.name,roles.description,roles.parent_role_id,roles.is_active, roles.legal_entity_id, roles.role_type, roles.is_support_role,"
//                . "(select GROUP_CONCAT(feature_id) from role_access where role_id=roles.role_id) as feature_id, "
//                . "(select GROUP_CONCAT(user_id) from user_roles where role_id=roles.role_id) as user_id FROM `roles`where roles.role_id=".$role_id));
        $roleDetails = DB::table('roles')
                ->select('roles.role_id', 'roles.short_code', 'roles.name', 'roles.description', 'roles.parent_role_id', 'roles.is_active',
                        'roles.legal_entity_id', 'roles.role_type', 'roles.is_support_role')
                ->where(['role_id' => $role_id, 'is_active' => 1])
                ->first();
        
        $featureIds = DB::table('role_access')
                ->where('role_id', $role_id)
                ->pluck('feature_id')->all();
        
        $userIds = DB::table('user_roles')
                ->leftJoin('users', 'users.user_id', '=', 'user_roles.user_id')
                ->where(['role_id' => $role_id, 'is_active' => 1, 'legal_entity_id' => 2])
                ->pluck('users.user_id')->all();
        $roleDetails->feature_id = $featureIds;
        $roleDetails->user_id = $userIds;
        return $roleDetails;
    }
    
    public function checkAccessToken($access_token){
        
        return DB::table('users_token')
                ->select('user_id','access_token')
                ->where('access_token',$access_token)
                ->get()->all();
    }
    
    public function checkPermissionByFeatureCode($featureCode, $userId = null)
    {
        if(!$userId)
        {
            $userId = Session::get('userId');
        }
        /*
         * User ID - 1 for Super Admin
         */ 
        if($userId == 1) {
            return true;
        }

        $result = DB::table('role_access')
                ->select('features.name')
                ->join('features','role_access.feature_id','=','features.feature_id')
                ->join('user_roles','role_access.role_id','=','user_roles.role_id')
                ->where([
                    'user_roles.user_id'=>$userId, 
                    'features.feature_code'=>$featureCode,
                    'features.is_active'=>1
                ])
                ->count();
       
        return ($result > 0) ? TRUE : FALSE;
        
    }
    
    public function getUsersByFeatureCode($featureCode,$userId = "")
    {
        $usersList = [];
        if($featureCode != '')
        {
            $msdata = DB::table('master_lookup')
                ->select('master_lookup_id','description','value')
                ->where('value',78014)->first();
            $roletoIgnore = isset($msdata->description)?explode(',',$msdata->description):[];
            $usersList = DB::table('role_access')                
                ->select('users.user_id','users.user_id as id','users.firstname','users.lastname','users.email_id','users.mobile_no',DB::raw('GetUserName(users.user_id,2) as name'),DB::raw('GetUserName(users.user_id,2) as username'))
                ->join('features','role_access.feature_id','=','features.feature_id')
                ->join('user_roles','role_access.role_id','=','user_roles.role_id')
                ->join('users','users.user_id','=','user_roles.user_id')
                ->where([
                    'features.feature_code' => $featureCode,
                    'users.is_active' => 1
                    ])
                ->whereNotIn('role_access.role_id',$roletoIgnore);
            // Checking the Global Access to View & Assign all the Beats
            $globalAccess = $this->checkPermissionByFeatureCode("GLB0001");
            if(!$globalAccess){
                // If the logged in User doesnot have access then we
                // restrict him with specific legal entity users
                $legalEntityId = Session::get('legal_entity_id');
                if(empty($legalEntityId) and !empty($userId)){
                    $userLegalEntityId = DB::table('users')->select('legal_entity_id')->where('user_id',$userId)->first();
                    $legalEntityId = isset($userLegalEntityId->legal_entity_id)?$userLegalEntityId->legal_entity_id:""; 
                }
                $usersList = $usersList->where('users.legal_entity_id',$legalEntityId);
            }
            $usersList = $usersList
                ->groupBy('users.user_id')
                ->orderBy('users.user_id', 'ASC')
                ->get()->all();
        }
        return $usersList;
    }
    public function getUsersByFeatureAndLeWareHouseId($le_wh_id, $feature_code) {
        
        $msdata = DB::table('master_lookup')
                ->select('master_lookup_id','description','value')
                ->where('value',78014)->first();
        $roletoIgnore = isset($msdata->description)?$msdata->description:'0';
        $query = "SELECT users.`user_id`, 
        GetUserName(users.`user_id`, 2) AS `name`, 
        users.email_id, roles.`name` AS `role` 
        FROM `user_permssion` 
        LEFT JOIN legalentity_warehouses ON legalentity_warehouses.`bu_id` = user_permssion.`object_id` 
        LEFT JOIN users ON users.`user_id` = user_permssion.`user_id` 
        LEFT JOIN user_roles ON user_roles.`user_id` = users.`user_id` 
        LEFT JOIN roles ON roles.`role_id` = user_roles.`role_id` 
        JOIN role_access ON roles.`role_id` = role_access.`role_id` 
        JOIN features ON role_access.feature_id = features.feature_id 
        WHERE le_wh_id IN ($le_wh_id) AND permission_level_id = 6 AND users.is_active=1 AND features.feature_code = '".$feature_code."'
        AND roles.role_id NOT IN ($roletoIgnore)
        GROUP BY user_permssion.user_id";
        $db_data = DB::select($query);
        if (count($db_data) > 0) {
            $db_data = json_decode(json_encode($db_data), true);
            return $db_data;
        } else {
            return array();
        }
    }

    public function checkPermissionByApi($access_token,$featureCode)
    {
        $users_result = $this->checkAccessToken($access_token);
        if(!empty($users_result))
        {
            $userId = $users_result[0]->user_id;
            $result = $this->checkActionAccess($userId,$featureCode);
            return ($result) ? 1 : 0;
        }else
            return 0;
    }
    
    public function checkPermissionByUrl($contName,$methodName='')
    { 
        $url =(!empty($methodName)) ? $contName.'/'.$methodName : $contName;
        $result = DB::table('features')
                ->select('feature_code')
                ->where('url',$url)
                ->get()->all();
        
        if(!empty($result))
        {
            $permission = $this->checkPermissionByFeatureCode($result[0]->feature_code);
            return ($permission) ? TRUE : FALSE;
        }  else {
            return FALSE;
        }
    }
    
    public function getUserRoldIdByUserId($userId)
    {
        return DB::table('user_roles')->where('user_id',$userId)->get()->all();
    }

    public function assign_rand_value($num)
    {
        switch($num)
        {
            case "1":
                $rand_value = "a";
            break;
            case "2":
                $rand_value = "b";
            break;
            case "3":
                $rand_value = "c";
            break;
            case "4":
                $rand_value = "d";
            break;
            case "5":
                $rand_value = "e";
            break;
            case "6":
                $rand_value = "f";
            break;
            case "7":
                $rand_value = "g";
            break;
            case "8":
                $rand_value = "h";
            break;
            case "9":
                $rand_value = "i";
            break;
            case "10":
                $rand_value = "j";
            break;
            case "11":
                $rand_value = "k";
            break;
            case "12":
                $rand_value = "l";
            break;
            case "13":
                $rand_value = "m";
            break;
            case "14":
                $rand_value = "n";
            break;
            case "15":
                $rand_value = "o";
            break;
            case "16":
                $rand_value = "p";
            break;
            case "17":
                $rand_value = "q";
            break;
            case "18":
                $rand_value = "r";
            break;
            case "19":
                $rand_value = "s";
            break;
            case "20":
                $rand_value = "t";
            break;
            case "21":
                $rand_value = "u";
            break;
            case "22":
                $rand_value = "v";
            break;
            case "23":
                $rand_value = "w";
            break;
            case "24":
                $rand_value = "x";
            break;
            case "25":
                $rand_value = "y";
            break;
            case "26":
                $rand_value = "z";
            break;
            case "27":
                $rand_value = "0";
            break;
            case "28":
                $rand_value = "1";
            break;
            case "29":
                $rand_value = "2";
            break;
            case "30":
                $rand_value = "3";
            break;
            case "31":
                $rand_value = "4";
            break;
            case "32":
                $rand_value = "5";
            break;
            case "33":
                $rand_value = "6";
            break;
            case "34":
                $rand_value = "7";
            break;
            case "35":
                $rand_value = "8";
            break;
            case "36":
                $rand_value = "9";
            break;
        }
        return $rand_value;
    }

    public function checkToken($module_id,$access_token){

         $access = \Token::where(array('module_id'=>$module_id,'access_token'=>$access_token))->get()->all();
         if(!empty($access[0])){
            return 1;
         }
         else{
            return 0;
         }
        }

    public function checkPermission($module_id,$access_token){

                         $access = $this->checkToken($module_id,$access_token);
                         return $access;
                    
                 }

    public function getErp($access_token){

        $mfg_id = $this->getMfgIdByToken($access_token);
        $data = DB::table('erp_integration')->where('manufacturer_id',$mfg_id)->first(['web_service_url','token','sap_client']);
        return $data;
    }   

    public function getLocTypeByAccessToken($access_token){
       
       $user_id= Token::where('access_token',$access_token)->pluck('user_id');
       $loc_type_id = DB::table('users')
                             ->join('locations','locations.location_id','=','users.location_id')
                             
                             ->where('users.user_id',$user_id)
                             ->pluck('locations.location_type_id');
                            
        return ($loc_type_id) ? $loc_type_id : FALSE;                   


    }    
    public function getUserDetailsByUserId($user_id){
        $details = DB::table('users')
                       ->where('user_id',$user_id)
                       //->orWhere('username',$user_id)
                       ->orWhere('email_id',$user_id)
                       ->first(['legal_entity_id']);
        return ($details) ? $details : FALSE;
    }   

    public function getUserId($user_id){
        $details = DB::table('users')
                       ->where('user_id',$user_id)
                       ->orWhere('username',$user_id)
                       ->orWhere('email_id',$user_id)
                       ->pluck('user_id');
        return ($details) ? $details : FALSE;
    }   
    
    public function getUserIdByLegalEntityId($legalEntityId){
        $details = DB::table('users')
                       ->where('legal_entity_id',$legalEntityId)
                       ->pluck('user_id');
        return ($details) ? $details : FALSE;
    }   

    public function getMfgIdByToken($access_token){
            $user_id= Token::where('access_token',$access_token)->pluck('user_id');
            $manufacturer_id = User::where('user_id',$user_id)->pluck('legal_entity_id');
            return $manufacturer_id;   
    }    

    public function getLocIdByToken($access_token){
            $user_id= Token::where('access_token',$access_token)->pluck('user_id');
            $location_id = User::where('user_id',$user_id)->pluck('location_id');
            return $location_id;   
    }  
    public function getErpDetailsByUserId($access_token){
        $user_id= Token::where('access_token',$access_token)->pluck('user_id');
        $erp = DB::table('users')->where('user_id',$user_id)
                                 ->whereNotNull('erp_username')
                                 ->whereNotNull('erp_password')
                                 ->get(['erp_username','erp_password'])->all();
        if(!empty($erp))                         
           return $erp;
        else
           return FALSE; 
    }
    
    public function encodeData($value)
    {
        return \Crypt::encrypt($this->_salt.$value);
    }
    
    public function decodeData($value)
    {
        try{
            if(strlen($value) > 10)
            {
                return str_replace($this->_salt, '', \Crypt::decrypt($value));
            }else{
                return $value;
            }            
        } catch (\ErrorException $ex) {
            return str_replace($ex->getMessage());
        }
    }
    
    public function mapRole($user_id, $roleIds)
    {
        try
        {
            if($user_id > 0 && !empty($roleIds))
            {
                DB::table('user_roles')->where(['user_id' => $user_id])->delete();
                foreach($roleIds as $roleId)
                {
//                    $id = DB::table('user_roles')
//                        ->where(['user_id' => $user_id, 'role_id' => $roleId])
//                        ->pluck('user_roles_id');
//                    if(empty($id))
//                    {
//                        $id = DB::table('user_roles')->insertGetId(['user_id' => $user_id, 'role_id' => $roleId]);
//                        $this->updateDates('user_roles', $id, 'user_roles_id', 1, 1, Session::get('userId'));
//                    }                    
                    $id = DB::table('user_roles')->insertGetId(['user_id' => $user_id, 'role_id' => $roleId]);
                    $this->updateDates('user_roles', $id, 'user_roles_id', 1, 1, Session::get('userId'));
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function updateNewFeature($featureId)
    {
        try
        {
            if($featureId > 0)
            {
                $roleList = $this->getMyRoles();
                foreach($roleList as $roleId)
                {
                    DB::table('role_access')->insert(['role_id' => $roleId, 'feature_id' => $featureId]);
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getMyRoles($userId = null)
    {
        try
        {
            $roleList = [];
            if(!$userId)
            {
                $currentUserId = Session::get('userId');
            }else{
                $currentUserId = $userId;
            }
            if($currentUserId > 0)
            {
                $roleList = DB::table('user_roles')
                        ->where(['user_id' => $currentUserId])
                        ->orderBy('user_roles.user_roles_id')
                        ->pluck('role_id')->all();
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $roleList;
    }


    public function getMyLegalentityId($userId = null)
    {
        try
        {
            $legalEntityIdUser='';
            if(!$userId)
            {
                $currentUserId = Session::get('userId');
            }else{
                $currentUserId = $userId;
            }
            if($currentUserId > 0)
            {
                $legalEntityIdUser = DB::table('users')
                        ->select('user_id')
                        ->where(['user_id' => $currentUserId])
                        ->pluck('user_id')->all();
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $legalEntityIdUser;
    }
    
    public function getProfilePicutre($userId)
    {
        try
        {
            $retailerPicture = url('img/avatar5.png');
            $result = DB::table('users')
                    ->where('user_id', $userId)
                    ->first(['profile_picture']);
            if(!empty($result))
            {
                $retailerPicture = property_exists($result, 'profile_picture') ? $result->profile_picture : '';
                if ($retailerPicture != '') {
                    if (strpos($retailerPicture, 'www') !== false || strpos($retailerPicture, 'http') !== false) {
//                            $results->profile_picture = $retailerPicture;
                    } else {
                        $retailerPicture = url('/'). $retailerPicture;
                    }
                }else{
                    $retailerPicture = url('img/avatar5.png');
                }
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $retailerPicture;
    }
    
    public function getUserNameById($userId)
    {
        try
        {
            $result = DB::table('users')
                    ->where('user_id', $userId)
                    ->select(DB::raw('concat(firstname, " ", lastname) as name'))
                    ->first();
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $result;
    }
    
    public function getMyDashboardData()
    {
        try
        {
            $legalEntityId = \Session::get('legal_entity_id');
            $currentTimeStamp = date('H:i:s');
//            $response = DB::select('SELECT SUM(go.total) AS order_total, COUNT(DISTINCT go.gds_order_id) AS total_orders  ,SUM(qty) AS total_qty
// FROM  gds_orders go, `gds_order_products` gp
// WHERE go.legal_entity_id = '.$legalEntityId.' AND go.gds_order_id = gp.gds_order_id
//AND go.order_date BETWEEN CURDATE() AND CONCAT(CURDATE() ," '.$currentTimeStamp.'")');
//            $response = DB::select('SELECT SUM(go.total) AS order_total, COUNT(DISTINCT go.gds_order_id) AS total_orders,SUM(go.`tax_total`) as total_tax,
//(SELECT COUNT(legal_entity_id) AS `users` FROM  `legal_entities` gp 
//WHERE DATE(gp.`created_at`) = DATE(CURDATE()) AND gp.legal_entity_type_id = 3001) AS totalusers
// FROM gds_orders go WHERE DATE(go.order_date) = DATE(CURDATE()) and go.legal_entity_id = '.$legalEntityId);
            /*$response = DB::select(' SELECT SUM(go.total) AS order_total,COUNT( DISTINCT go.`gds_cust_id`) AS unqoutletsbill,
(SUM(go.total)/COUNT(DISTINCT go.gds_order_id)) AS AvgBillValue,
 COUNT(DISTINCT go.gds_order_id) AS total_orders  , COUNT(gp.`product_id`) AS totallinescut,
 COUNT(DISTINCT gp.`product_id`) AS unqlinescut,
 (COUNT(gp.`product_id`)/COUNT(DISTINCT go.gds_order_id)) AS avglinescut,
SUM(go.`tax_total`) total_tax, 
(SELECT COUNT(legal_entity_id) AS `users` FROM  `legal_entities` gp 
WHERE DATE(gp.`created_at`) = DATE(CURDATE()) AND gp.legal_entity_type_id = 3001) AS totalusers
 FROM  gds_orders go,
 `gds_order_products` gp
 WHERE go.legal_entity_id='.$legalEntityId.' 
AND go.order_date AND DATE(go.order_date) = DATE(CURDATE*/
            $response = DB::select('CALL getDashboardReport()');
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getMasterLookupData($masterLookupName = null, $masterLookupCatId = null)
    {
        try
        {
            $response = [];
            if($masterLookupName)
            {
                $response = DB::table('master_lookup_categories')
                    ->leftJoin('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                    ->where('master_lookup_categories.mas_cat_name', $masterLookupName)
                    ->select('master_lookup.master_lookup_name', 'master_lookup.value')
                    ->get()->all();
            }else if($masterLookupCatId)
            {
                $response = DB::table('master_lookup')
                    ->where('master_lookup.mas_cat_id', $masterLookupCatId)
                    ->select('master_lookup.master_lookup_name', 'master_lookup.value')
                    ->get()->all();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getUsersByLegalEntityId($legalEntityId)
    {
        try
        {
            $response = 0;
            if($legalEntityId > 0)
            {
                $response = DB::table('users')
                        ->leftjoin('user_roles','user_roles.user_id','=','users.user_id')
                        ->leftjoin('roles','roles.role_id','=','user_roles.role_id')
                        ->where('users.legal_entity_id', $legalEntityId)
                        ->select('users.user_id', 'users.firstname', 'users.lastname', 
                                DB::raw('GetUserName(users.created_by, 2)'), DB::raw('GetUserName(users.user_id, 2) as name'),
                                'users.mobile_no', 'users.email_id','users.aadhar_id','users.profile_picture',DB::raw("GROUP_CONCAT(roles.name)  AS rolename"),'users.otp')
                        ->get()->all();
            }
            if(!empty($response))
            {
                $i = 0;
                foreach($response as $retailerUserDetails)
                {
                    $userId = $retailerUserDetails->user_id;
                    $profilePic = $this->getProfilePicutre($userId);
                    if($profilePic != '')
                    {
                        
                        $response[$i]->profile_picture = '<img src="'.$profilePic.'" class="img-circle" style="height: 50px; width: 50px;" />';
                    }
                    $response[$i]->action = '<span style="padding-left:15px;" ><a href="#" onclick="editUser('.$userId.')" >'
                        . '<i class="fa fa-pencil"></i></a></span>'; 
                    $i++;
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getDocumentList($legalEntityId)
    {
        try
        {
            $response = 0;
            if($legalEntityId > 0)
            {
                $response = DB::table('legal_entity_docs')
                        ->where('legal_entity_id', $legalEntityId)
                        ->select('doc_id', 'doc_name', 'doc_type', DB::raw('GetUserName(created_by, 2) as created_by'), 'reference_no', 'doc_url', 'created_at')
                        ->get()->all();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    public function getAreaList($pincode)
    {
        try
        {
            $response = 0;
            if($pincode > 0)
            {
                $response = DB::table('cities_pincodes')                        
                        ->where('pincode', $pincode)
                        ->select('city_id', 'officename')
                        ->get()->all();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    public function getAreaData($pincode)
    {
        try
        {
            $response = 0;
            if($pincode > 0)
            {
                $response = DB::table('cities_pincodes')                        
                        ->where('pincode', $pincode)
                        ->select('city_id', 'officename')
                        ->get()->all();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getBeatData($spokeId)
    {
        try
        {
            $response = [];
            if($spokeId > 0)
            {
                $response = DB::table('pjp_pincode_area')
                        //->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
                        //->where('spokes.spoke_id', $spokeId)
                        ->select('pjp_pincode_area.pjp_pincode_area_id', 'pjp_pincode_area.pjp_name')
                        ->groupBy('pjp_pincode_area.pjp_pincode_area_id')
                        ->get()->all();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getSpokeData($hubId)
    {
        try
        {
            $response = [];
            if($hubId > 0)
            {
                $response = DB::table('spokes')
                        ->where('spokes.le_wh_id', $hubId)
                        ->select('spoke_id', 'spoke_name')
                        ->get()->all();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getServicableList($pincode)
    {
        try
        {
            $response = 0;
            if($pincode > 0)
            {
                $response = DB::table('wh_serviceables')
                        ->leftJoin('legalentity_warehouses as le', 'le.le_wh_id', '=', 'wh_serviceables.le_wh_id')
                        ->leftJoin('zone', 'zone.zone_id', '=', 'le.state')
                        ->where('wh_serviceables.pincode', $pincode)
                        ->select('lp_wh_name', 'contact_name', 'phone_no', 'email', 'address1', 'address2', 'city', DB::raw('zone.name as state'))
                        ->groupBy('le.le_wh_id')
                        ->get()->all();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }

    public function getCollectionDetails($legalEntityId)
    {
        try
        {
/*            select distinct
gds_orders.order_code,
collections.collection_code,
collection_history.amount as 'Amount',
GetUserName(collections.customer_id,2) AS 'Paid By',
GetUserName(collection_history.collected_by,2) as 'Deliverd By',
getMastLookupValue(collection_history.payment_mode) as 'Payment Mode',
collections.collected_amount,

from collections
left join `gds_orders` on `gds_orders`.`gds_order_id` = `collections`.`gds_order_id`
left join `collection_history` on `collection_history`.`collection_id` = `collections`.`collection_id`
left join `users` on `users`.`legal_entity_id` = `gds_orders`.`cust_le_id`
where `users`.`legal_entity_id` = 7131;
*/
            if($legalEntityId > 0)
            {
                $query = DB::table('collections')
                            ->distinct()
                            ->select(
                                DB::raw('DATE_FORMAT(collection_history.collected_on,"%d-%m-%Y") as date'),
                                'gds_orders.order_code',
                                'collections.collection_code',
                                DB::raw('round(collection_history.amount,2) as amount'),
                                DB::raw('GetUserName(collections.customer_id,2) as paid_by'),
                                DB::raw('GetUserName(collection_history.collected_by,2) as delivered_by'),
                                DB::raw('round(collections.collected_amount,2) as collected_amount'),
                                DB::raw('getMastLookupValue(collection_history.payment_mode) as Payment_Mode')
                                )
                            ->leftjoin('gds_orders','gds_orders.gds_order_id', '=', 'collections.gds_order_id')
                            ->leftjoin('collection_history','collection_history.collection_id','=','collections.collection_id')
                            ->leftJoin('users', 'users.legal_entity_id', '=', 'gds_orders.cust_le_id')
                            ->where('users.legal_entity_id', $legalEntityId)
                            ->get()->all();
                return $query;                            
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getOrdersList($legalEntityId)
    {
        try
        {
            $response = 0;
            if($legalEntityId > 0)
            {
                $response = DB::table('gds_orders')
                        ->leftJoin('users', 'users.legal_entity_id', '=', 'gds_orders.cust_le_id')
                        ->leftJoin('master_lookup', 'master_lookup.value', '=', 'gds_orders.order_status_id')
                        ->leftJoin('customers', 'customers.le_id', '=', 'gds_orders.cust_le_id')
                        ->leftJoin('cities_pincodes', 'cities_pincodes.city_id', '=', 'customers.area_id')
                        ->leftJoin('pjp_pincode_area', 'pjp_pincode_area.pjp_pincode_area_id', '=', 'gds_orders.beat')
                        ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'gds_orders.hub_id')
                        ->where('users.legal_entity_id', $legalEntityId)
                        ->select(
                            'gds_orders.gds_order_id',
                            'gds_orders.order_code',
                            'gds_orders.order_status_id',
                            'gds_orders.shop_name',
                            'master_lookup.master_lookup_name',
                            DB::raw('TRUNCATE(gds_orders.total,2) as total'),
                            'gds_orders.email',
                            'gds_orders.phone_no',
                            DB::raw('DATE_FORMAT(gds_orders.order_date, "%d/%m/%Y %H:%i:%s") as order_date'),
                            'pjp_pincode_area.pjp_name as beat',
                            'cities_pincodes.officename as areaname',
                            'legalentity_warehouses.lp_wh_name as hub'
                            )
                        ->distinct()
                        ->orderBy('gds_orders.order_date', 'DESC')
                        ->get()->all();
            $i=0;
            foreach($response as $order)
            {
                $order->order_code = "<a href='/salesorders/detail/".$order->gds_order_id."' target='_blank'>".$order->order_code."</a>";
                $actions = '';
                $editPermission = $this->checkPermissionByFeatureCode("ACB001");
                if($editPermission && in_array($order->order_status_id,['17001','17005','17020']))
                   $actions.= '<span class="actionsStyle" ><a onclick="editOrderCode('.$order->gds_order_id.')"</a><i class="fa fa-pencil"></i></span> ';
                $response[$i++]->actions = $actions;
            }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getOrderCount($legalEntityId)
    {
        try
        {
            $response = 0;
            if($legalEntityId > 0)
            {
                $response = DB::table('gds_orders')
                        ->leftJoin('gds_customer', 'gds_customer.gds_cust_id', '=', 'gds_orders.gds_cust_id')
                        ->leftJoin('users', 'users.user_id', '=', 'gds_customer.mp_user_id')
                        ->leftJoin('master_lookup', 'master_lookup.value', '=', 'gds_orders.order_status_id')
                        ->where('users.legal_entity_id', $legalEntityId)
                        ->count();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getLastOrderDate($legalEntityId)
    {
        try
        {
            $response = 0;
            if($legalEntityId > 0)
            {
                $response = DB::table('gds_orders')
                        ->leftJoin('gds_customer', 'gds_customer.gds_cust_id', '=', 'gds_orders.gds_cust_id')
                        ->leftJoin('users', 'users.user_id', '=', 'gds_customer.mp_user_id')
                        ->leftJoin('master_lookup', 'master_lookup.value', '=', 'gds_orders.order_status_id')
                        ->where('users.legal_entity_id', $legalEntityId)                        
                        ->orderBy('gds_orders.order_date', 'DESC')
                        ->first(['gds_orders.order_date']);
                if(!empty($response))
                {
                    $response = property_exists($response, 'order_date') ? $response->order_date : '';
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function getUserPreferrences($userId)
    {
        try
        {
            $response = 0;
            if($userId > 0)
            {
                $response = DB::table('user_preferences')
                        ->where('user_id', $userId)
                        ->first();
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }
    
    public function inactiveUser($userId, $status) {        // To Block Single User
        try
        {
            $result['status'] = false;
            if($userId > 0)
            {
                if($status == 1)
                {
                    $mobile_no=DB::table('users')->select('mobile_no')->where('user_id',$userId)->get()->all();
                    if(count($mobile_no)>0){
                        $checkUserExist=DB::table('users')
                        ->where('mobile_no',$mobile_no[0]->mobile_no)
                        ->where('user_id','!=',$userId)
                        ->where('is_active',1)
                        ->get()->all();
                        if(count($checkUserExist)>0){
                            $result['status'] = false;
                        }else{
                            DB::table('users')
                            ->where('user_id', $userId)
                            ->update([
                                'is_active' => $status,
                                'is_disabled' => 0
                                ]); 
                            $result['status'] = true;
                        }

                    }                    
                }

                elseif ($status == 0)
                {
                    DB::table('users')
                    ->where('user_id', $userId)
                    ->update([
                        'is_active' => $status,
                        'is_disabled' => 1
                        ]);
                    $result['status'] = true;
    
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return json_encode($result);
    }
    public function inactiveUsers($legalEntityId, $status) {        //  To Block Multiple Users at Once
        try
        {
            $result['status'] = false;
            if($legalEntityId > 0)
            {
                DB::table('users')
                        ->where('legal_entity_id', $legalEntityId)
                        ->update(['is_active' => $status]);
                DB::table('retailer_flat')
                        ->where('legal_entity_id', $legalEntityId)
                        ->update(['is_active' => $status]);
                $result['status'] = true;
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return json_encode($result);
    }
    
    public function getActiveUsers($legalEntityId) {
        try
        {
            $result = 0;
            if($legalEntityId > 0)
            {
                $collection = DB::table('users')
                        ->where(['legal_entity_id' => $legalEntityId, 'is_active' => 1])
                        ->pluck('user_id')->all();
                if(!empty($collection))
                {
                    $result = 1;
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }
    
    public function filterData($request, $produc_grid_field_db_match, $minimumFields = null)
    {
        try
        {
            $request_input = $request->input();
            $filter_by = '';
            $filterBy = '';
            $orderby_array = [];
            $order_by = '';
            $date = array();
            if (isset($request_input['$filter'])) {
                $filterBy = $request_input['$filter'];
            } elseif (isset($request_input['%24filter'])) {
                $filterBy = urldecode($request_input['%24filter']);
            }
            \Log::info($request_input);
            
            if ($request->input('$orderby'))
            {             //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';

                if ($order_query_type == 'asc')
                {
                    $order_by_type = 'asc';
                }

                if (isset($produc_grid_field_db_match[$order_query_field]))
                { //getting appropriate table field based on grid field
                    $order_by = $produc_grid_field_db_match[$order_query_field];
                }
                $orderby_array = $order_by . " " . $order_by_type;
            }

            if (isset($filterBy) && $filterBy != '') {
                $filter_explode = explode(' and ', $filterBy);

                foreach ($filter_explode as $filter_each) {
                    $filter_each_explode = explode(' ', $filter_each);
                    $length = count($filter_each_explode);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter_each_explode[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter_each_explode[$length - 2];
                        $filter_query_value = $filter_each_explode[$length - 1];
                    } else {
                        $filter_query_field = $filter_each_explode[0];
                        $filter_query_operator = $filter_each_explode[1];
                        $filter_query_value = $filter_each_explode[2];
                    }                    
                    if (strpos($filter_each, ' or ') !== false)
                    {
                        $query_field_arr = explode(' or ', $filter_each);
                        foreach ($query_field_arr as $query_field_data)
                        {
                            $filter = explode(' ', $query_field_data);
                            $filter_query_field = $filter[0];
                            $filter_query_operator = $filter[1];
                            $filter_query_value = $filter[2];

                            if (strpos($filter_query_field, 'day(') !== false)
                            {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                                continue;
                            } elseif (strpos($filter_query_field, 'month(') !== false)
                            {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                                continue;
                            } elseif (strpos($filter_query_field, 'year(') !== false)
                            {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['year'] = $filter_query_value;
                                $date[$filter_query_field]["operator"] = $filter_query_operator;
                                $filter_query_operator = $date[$filter_query_field]['operator'];
                                $filter_query_value = implode('-', array_reverse($date[$filter_query_field]['value']));
                            }
                        }
                    } else
                    {
                        if (strpos($filter_query_field, 'day(') !== false)
                        {
                            $start = strpos($filter_query_field, '(');
                            $end = strpos($filter_query_field, ')');
                            $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                            continue;
                        } elseif (strpos($filter_query_field, 'month(') !== false)
                        {
                            $start = strpos($filter_query_field, '(');
                            $end = strpos($filter_query_field, ')');
                            $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                            continue;
                        } elseif (strpos($filter_query_field, 'year(') !== false)
                        {
                            $start = strpos($filter_query_field, '(');
                            $end = strpos($filter_query_field, ')');
                            $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['year'] = $filter_query_value;
                            $date[$filter_query_field]["operator"] = $filter_query_operator;
                            $filter_query_operator = $date[$filter_query_field]['operator'];
                            $filter_query_value = implode('-', array_reverse($date[$filter_query_field]['value']));
                        }
                        reset($date);
                    }

                    $filter_query_field_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
                        //Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

                        if ($filter_query_field_substr == 'startsw') {
                            $filter_query_field_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = $filter_query_field_value_array[1] . '%';

                            foreach ($produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $starts_with_value = $produc_grid_field_db_match[$key] . ' like ' . $filter_value;
                                    $filter_by[] = $starts_with_value;
                                } else {
                                    $starts_with_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'endswit') {
                            $filter_query_field_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = '%' . $filter_query_field_value_array[1];

                            foreach ($produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $ends_with_value = $produc_grid_field_db_match[$key] . ' like ' . $filter_value;
                                    $filter_by[] = $ends_with_value;
                                } else {
                                    $ends_with_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'tolower') {
                            $filter_query_value_array = explode("'", $filter_query_value);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = $filter_query_value_array[1];
                            if ($filter_query_operator == 'eq') {
                                $like = ' = ';
                            } else {
                                $like = ' != ';
                            }
                            foreach ($produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $to_lower_value = $produc_grid_field_db_match[$key] . $like . $filter_value;
                                    $filter_by[] = $to_lower_value;
                                } else {
                                    $to_lower_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'indexof') {
                            $filter_query_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_query_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = ' like ';
                            } else {
                                $like = ' not like ';
                            }
                            foreach ($produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $indexof_value = $produc_grid_field_db_match[$key] . $like . $filter_value;
                                    $filter_by[] = $indexof_value;
                                } else {
                                    $indexof_value = "";
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :
                                $filter_operator = ' = ';
                                break;

                            case 'ne':
                                $filter_operator = ' != ';
                                break;

                            case 'gt' :
                                $filter_operator = ' > ';
                                break;

                            case 'lt' :
                                $filter_operator = ' < ';
                                break;

                            case 'ge' :
                                $filter_operator = ' >= ';
                                break;

                            case 'le' :
                                $filter_operator = ' <= ';
                                break;
                        }

                        if (isset($produc_grid_field_db_match[$filter_query_field])) {
                            //getting appropriate table field based on grid field
                            $filter_field = $produc_grid_field_db_match[$filter_query_field];
                        }
                        if (strpos($filter_query_value, 'DateTime') !== false && $filter_field == 'last_order_date')
                        {
                            $temp = str_replace("DateTime'", '', $filter_query_value);
                            $tempArray = explode('T', $temp);
                            $filter_query_value = isset($tempArray[0]) ? $tempArray[0] : $filter_query_value;
                        }
                        $filter_by[] = $filter_field . $filter_operator . '"'.$filter_query_value.'"';
                    }
                }
            }
            Log::info(DB::getQueryLog());
            return $filter_by;
//            return ['Records' => $results, 'totalCustomerCount' => $totalCount];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function userListFilter($request, $produc_grid_field_db_match)
    {
        try
        {
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax
            $filter_by = $this->filterData($request, $produc_grid_field_db_match);
            if (!empty($filter_by))
            {
                foreach ($filter_by as $filterByEach)
                {
                    $filterByEachExplode = explode(' ', $filterByEach);

                    $length = count($filterByEachExplode);
                    $filter_query_value = '';
                    if ($length > 3)
                    {
                        $filter_query_field = $filterByEachExplode[0];
                        $filter_query_operator = $filterByEachExplode[1];
                        for ($i = 2; $i < $length; $i++)
                            $filter_query_value .= $filterByEachExplode[$i] . " ";
                    } else
                    {
                        $filter_query_field = $filterByEachExplode[0];
                        $filter_query_operator = $filterByEachExplode[1];
                        $filter_query_value = $filterByEachExplode[2];
                    }

                    $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                    if (in_array(trim($filter_query_operator), $operator_array))
                    {
                        $results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                    } else
                    {
                        $results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                    }
                }
                $tempCountCollection = clone $results;
                $results = $results->skip($page * $pageSize)->take($pageSize)->get()->all();
                $totalCount = $tempCountCollection->count();
            } else
            {
                $tempCountCollection = clone $results;
                $results = $results->skip($page * $pageSize)->take($pageSize)->get()->all();
                $totalCount = $tempCountCollection->count();
            }
        } catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function hasChildRoles($roleId)
    {
        try
        {
            $hasChilds = 0;
            if($roleId > 0)
            {
                $list = DB::table('roles')
                        ->where('parent_role_id', $roleId)
                        ->pluck('role_id')->all();
                if(!empty($list))
                {
                    $hasChilds = 1;
                }
            }
            return $hasChilds;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
public function getUserHubId($customerToken)
 {
  try{
   $hubId = [];
   if($customerToken != '')
   {
    $legalEntityData = DB::table('users')
    ->where('password_token', $customerToken)
    ->orWhere('lp_token', $customerToken)
    ->first(['legal_entity_id', 'user_id']);
    if(!empty($legalEntityData))
    {
     $legalEntityId = property_exists($legalEntityData, 'legal_entity_id') ? $legalEntityData->legal_entity_id : 0;
     $userId = property_exists($legalEntityData, 'user_id') ? $legalEntityData->user_id : 0;
     if($legalEntityId == 2)
     {
      $roleModel = new \App\Modules\Roles\Models\Role();
      $DataFilter=$roleModel->getFilterData(6,$userId);
       $decode_data=json_decode($DataFilter,true);
       $sbu_lits = isset($decode_data['sbu']) ? $decode_data['sbu']:[];
       $decode_sbulist= json_decode($sbu_lits,true);
       $hubId = (isset($decode_sbulist[118002])&& !empty($decode_sbulist[118002])) ? $decode_sbulist[118002] : 0;
     }elseif($legalEntityId > 0){
      $hubDetails = DB::table('pjp_pincode_area')
       ->leftJoin('customers', 'customers.beat_id', '=', 'pjp_pincode_area.pjp_pincode_area_id')
       ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
       ->where('customers.le_id', $legalEntityId)
       ->first(['spokes.le_wh_id']);

      if(!empty($hubDetails))
      { 
       $hubId = property_exists($hubDetails, 'le_wh_id') ? $hubDetails->le_wh_id : 0;
      } 
      
     }
    }
   } 
  return $hubId;
  } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
 }
 
    public function getBeatByUserId($customerToken) {
        try {
            $beats = [];
            if ($customerToken != '') {
                $userDetails = DB::table('users')
                        ->where("password_token", $customerToken)
                        ->orwhere('lp_token', $customerToken)
                        ->useWritePdo()
                        ->first(['user_id', 'legal_entity_id']);
                if (!empty($userDetails)) {
                    $userId = property_exists($userDetails, 'user_id') ? $userDetails->user_id : 0;
                    $legalEntityId = property_exists($userDetails, 'legal_entity_id') ? $userDetails->legal_entity_id : 0;
                    if ($userId > 0) {
                        // Default Flag is 0
                        $flag = 0;
                        // If the User is an FF or from the Company
                        $hubDetails = DB::table('pjp_pincode_area')->where('rm_id',$userId)->select('le_wh_id as hub_id')->first();
                        // If the User is a Retailer
                        if(empty($hubDetails)){
                            // If the $hubDetails is empty, then he is a retailer only
                            $flag = 2;
                            $hubDetails = DB::table('retailer_flat')->where('legal_entity_id',$legalEntityId)->select('hub_id')->first();
                        }
                        $hubId = isset($hubDetails->hub_id)?$hubDetails->hub_id:"";
                        // This Feature is to check, wheather the user
                        // has the access to all the Beats. Thats it
                        $allBeatsAccess = $this->checkPermissionByFeatureCode("ALLBEAT1");
                        // If the user has access, then the flag is set to be 1 else 0.
                        if($allBeatsAccess) $flag = 1;

                        /* Flag stats
                            0 -> Normal FF User
                            1 -> FF with All Beats Access
                            2 -> Retailer */
                        $query = "CALL getBeatDetails(?,?,?,?,1000,0)";
                        $beatsList = DB::SELECT($query,[$userId,$legalEntityId,$hubId,$flag]);
                        // In this proc call, we get 4 fields for each row, but we want only
                        // one row, i.e. 'Beat ID'. Thats y I used array_map here
                        $beatsArray = array_map(
                            function($args){
                                return $args->{'Beat ID'};
                            },$beatsList);
                        // The above will return all the Beats array, & in the below we send
                        // the array as a , seperated string. Bingo
                        return implode(",",$beatsArray);
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return [];
        }
        return $beats;
    }

    public function getSpokesByBeats($beats) {
        try {
            $spokes = [];
            if ($beats != '') {
                if (!is_array($beats)) {
                    $beats = explode(',', $beats);
                }
                if (!empty($beats)) {
                    DB::enableQueryLog();
                    $spokesData = DB::table('pjp_pincode_area')
                            ->whereIn('pjp_pincode_area_id', $beats)
                            ->groupBy('spoke_id')
                            ->first([DB::raw('group_concat(distinct(spoke_id)) as spokes')]);
                    if (!empty($spokesData)) {
                        $spokes = property_exists($spokesData, 'spokes') ? $spokesData->spokes : [];
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $spokes;
    }

    public function getSpokesByHubs($spokes) {
        try {
            $hubs = [];
            if ($spokes != '') {
                if (!is_array($spokes)) {
                    $spokes = explode(',', $spokes);
                }
                if (!empty($spokes)) {
                    DB::enableQueryLog();
                    $hubData = DB::table('spokes')
                            ->whereIn('spoke_id', $spokes)
                            ->groupBy('le_wh_id')
                            ->first([DB::raw('group_concat(distinct(le_wh_id)) as hubs')]);
                    if (!empty($hubData)) {
                        $hubs = property_exists($hubData, 'hubs') ? $hubData->hubs : [];
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $hubs;
    }

    public function getSpokesByDc($hubs) {
        try {
            $dcs = [];
            if ($hubs != '') {
                if (!is_array($hubs)) {
                    $hubs = explode(',', $hubs);
                }
                if (!empty($hubs)) {
                    DB::enableQueryLog();
                    $dcData = DB::table('dc_hub_mapping')
                            ->whereIn('hub_id', $hubs)
                            ->groupBy('dc_id')
                            ->first([DB::raw('group_concat(distinct(dc_id)) as dc')]);
                    if (!empty($dcData)) {
                        $dcs = property_exists($dcData, 'dc') ? $dcData->dc : [];
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $dcs;
    }
    
    public function getBlockedList($dcs, $hubs, $spokes, $beats)
    {
        try
        {
            $result = [];
            $temp['manf'] = [];
            $temp['brands'] = [];
            if(!empty($dcs))
            {
                $temp = $this->processData('DC', $dcs, $temp);
            }
            if(!empty($hubs))
            {
                $temp = $this->processData('HUB', $hubs, $temp);
            }
            if(!empty($spokes))
            {
                $temp = $this->processData('SPOKE', $spokes, $temp);
            }
            if(!empty($beats))
            {
                $temp = $this->processData('BEAT', $beats, $temp);
            }
            $result = $temp;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $result;
    }
    
    public function processData($scopeType, $scopeIds, $resultData)
    {
        try
        {
            if(!is_array($scopeIds))
            {
                $scopeIds = explode(',', $scopeIds);
            }
            $response = DB::table('hub_product_mapping')
                        ->where('scope_type', $scopeType)
                        ->whereIn('scope_id', $scopeIds)
                        ->select('ref_type', 'ref_id')
                        ->get()->all();
            if(!empty($response))
            {
                foreach($response as $details)
                {
                    $type = property_exists($details, 'ref_type') ? $details->ref_type : '';
                    $data = property_exists($details, 'ref_id') ? $details->ref_id : 0;
                    if($type == 'brands')
                    {
                        $tempBrands = (array)$resultData['brands'];
                        $tempBrands[] = $data;
                        $resultData['brands'] = $tempBrands;
                    }
                    if($type == 'manufacturers')
                    {
                        $tempManf = (array)$resultData['manf'];
                        $tempManf[] = $data;
                        $resultData['manf'] = $tempManf;
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $resultData;
    }

    public function getAllDcs($user_id){
        $roleObj = new Role();
        $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
        $filters = json_decode($Json['sbu'], 1);            
        $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
         
        $allDCS = "select * FROM legalentity_warehouses AS lw INNER JOIN zone AS z ON lw.state = z.zone_id WHERE lw.dc_type=118001 AND lw.status=1 AND lw.le_wh_id IN (".$dc_acess_list.")";
        $allData = DB::select(DB::raw($allDCS));
         return $allData;
    }
    public function getLEWHDetailsById($le_wh_id) {
        try{
            $fieldArr = array(
                            'warehouses.le_wh_id',
                            'warehouses.legal_entity_id',
                            'warehouses.lp_wh_name',
                            'warehouses.address1',
                            'warehouses.address2',
                            'warehouses.city',
                            'warehouses.pincode',
                            'warehouses.phone_no',
                            'warehouses.email',
                            'warehouses.credit_limit_check',
                            'warehouses.state as state_id',
                            'zone.name as state',
                            'zone.code as state_code'
                        );
            $query = DB::table('legalentity_warehouses as warehouses')->select($fieldArr);
            $query->join('legal_entities as legal', 'warehouses.legal_entity_id', '=', 'legal.legal_entity_id');
            $query->leftJoin('zone', 'zone.zone_id', '=', 'warehouses.state');
            $query->where('warehouses.le_wh_id', $le_wh_id);
            return $query->first();
        }
        catch(Exception $e) {

        }
    }

    /*Get All FCs List*/
    public function getAllFcs($dcfctype)
    {
        $roleObj = new Role();
        $user_id = Session::get('user_id');
        $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
        $filters = json_decode($Json['sbu'], 1);            
        $fc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $allData=array();
        if(($fc_acess_list!='' || $fc_acess_list!=undefined) && ($dcfctype!=''|| $dcfctype!=undefined))
        {
        $allFcs = "SELECT lw.`display_name`,
                          lw.`bu_id`,
                          le.`legal_entity_type_id`,
                          lw.`dc_type` 
                        FROM
                          legal_entities le 
                          INNER JOIN legalentity_warehouses lw 
                          ON le.`legal_entity_id` = lw.`legal_entity_id` 
                          WHERE FIND_IN_SET(lw.`le_wh_id`,'".$fc_acess_list."') 
                          AND le.`legal_entity_type_id` IN (".$dcfctype.")  
                          AND lw.`dc_type` IN (118001)";
        $allData = DB::select(DB::raw($allFcs));
        return $allData;

        }
        else
        {
           return $allData; 
        }

                 
    }





    public function getUsersByFeatureCodeWithoutLegalentity($hubdata,$flag)
    {
        $usersList = [];
        /*if($featureCode != '')
        {
            $msdata = DB::table('master_lookup')
                ->select('master_lookup_id','description','value')
                ->where('value',78014)->first();
            $roletoIgnore = isset($msdata->description)?explode(',',$msdata->description):[];
            $usersList = DB::table('role_access')                
                ->select('users.user_id','users.user_id as id','users.firstname','users.lastname','users.email_id','users.mobile_no',DB::raw('GetUserName(users.user_id,2) as name'),DB::raw('GetUserName(users.user_id,2) as username'))
                ->join('features','role_access.feature_id','=','features.feature_id')
                ->join('user_roles','role_access.role_id','=','user_roles.role_id')
                ->join('users','users.user_id','=','user_roles.user_id')
                ->where([
                    'features.feature_code' => $featureCode,
                    'users.is_active' => 1
                    ])
                ->whereNotIn('role_access.role_id',$roletoIgnore);      
            $usersList = $usersList
                ->groupBy('users.user_id')
                ->orderBy('users.user_id', 'ASC')
                ->get()->all();
        }*/
        $hubdata=implode($hubdata,',');
        $usersList=DB::selectFromWriteConnection(DB::raw("call get_PEDEUsersListByDC(null,'$hubdata', $flag)"));        
        return $usersList;
    }


    /*public function getSubroles($superroleid,$ress=[]){
        
       
        if(count($ress)==0){
             $res=array();
        }else{
            $res=$ress;
           
        }
        $getsubrole=DB::table('roles')
                      ->select(DB::raw('GROUP_CONCAT(role_id) AS role_id'))
                      ->whereIn('parent_role_id',$superroleid)
                      ->get()->all();
                     
      $getsubrole=json_decode(json_encode($getsubrole),true);
      if(!empty($getsubrole[0]['role_id'])){
        $getsubroleids=explode(',',$getsubrole[0]['role_id']);
        $res=array_merge($res,$getsubroleids);
        $getsubrole[0]['role_id']=explode(',',$getsubrole[0]['role_id']);
        $this->getSubroles($getsubrole[0]['role_id'],$res);
      }else{
        return $res;
      }
    }
*/
    public function getSubroles($superroleid,$userId,$ress=array()){
         
         if(empty($ress)){
             $res=array();
             $res=array_merge($res, $superroleid);
        }else{
            $res=$ress;
        }
         
         $globalAccess = $this->checkPermissionByFeatureCode("GLB0001",$userId);
         if($globalAccess){

            $getsubrole=DB::table('roles')
                      ->select(DB::raw('GROUP_CONCAT(role_id) AS role_id'))
                      ->where('is_active',1)
                      ->get()->all();
                     
          $getsubrole=json_decode(json_encode($getsubrole),true);
          
          if(!empty($getsubrole[0]['role_id'])){
            $getsubroleids=explode(',',$getsubrole[0]['role_id']);
            $res=array_merge($res,$getsubroleids);
        }
           return $res;
         }else{
        $getsubrole=DB::table('roles')
                      ->select(DB::raw('GROUP_CONCAT(role_id) AS role_id'))
                      ->whereIn('parent_role_id',$superroleid)
                      ->get()->all();
                     
          $getsubrole=json_decode(json_encode($getsubrole),true);
          
          if(!empty($getsubrole[0]['role_id'])){
            $getsubroleids=explode(',',$getsubrole[0]['role_id']);
            $res=array_merge($res,$getsubroleids);
            return $this->getSubroles($getsubroleids,$userId,$res);
          }else{
          return $ress;
          }
      }
          
    }


    public function getMyLegalentityIdofReporting($reportingid,$ress=array()){
         
         if(empty($ress)){
             $res=array();
             $res=array_merge($res,$reportingid);
        }else{
            $res=$ress;
        }


        $getsubreporting=DB::table('users')
                      ->select(DB::raw('GROUP_CONCAT(user_id) AS user_id'))
                      ->whereIn('reporting_manager_id',$reportingid)
                      ->where('is_active',1)
                      ->get()->all();
                     
          $getsubreporting=json_decode(json_encode($getsubreporting),true);
          
          if(!empty($getsubreporting[0]['user_id'])){
            $getsubreportingids=explode(',',$getsubreporting[0]['user_id']);
            $res=array_merge($res,$getsubreportingids);
            return $this->getMyLegalentityIdofReporting($getsubreportingids,$res);
          }else{
          return $ress;
          }
    
}
public function getParentBU($bu_id,$parent_ress=array()){
    try{
        if(empty($parent_ress)){
             $res=array();
             $res=array_merge($res,$bu_id);
             //$parent_ress = array_merge($parent_ress,$bu_id);
        }else{
            $res=$parent_ress;
        }
        $getparentBuunits = DB::table('business_units')
                            ->select(DB::raw('GROUP_CONCAT(parent_bu_id) AS parent_bu_id'))
                            ->whereIn('bu_id',$bu_id)
                            ->get()->all();

        $getparentBuunits=json_decode(json_encode($getparentBuunits),true);
        if(!empty($getparentBuunits[0]['parent_bu_id'])){
            $getbuids=explode(',',$getparentBuunits[0]['parent_bu_id']);
            $res=array_merge($res,$getbuids);
            return $this->getParentBU($getbuids,$res);
          }else{
            if(empty($parent_ress)){
                $parent_ress = array_merge($parent_ress,$bu_id);
            }
          return $parent_ress;
          }
    }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
}
public function getBuidsByUserId($userid){
        try{
            $buids=DB::table('user_permssion')
                       ->select(DB::raw("GROUP_CONCAT(object_id) as object_id"))
                       ->where('user_id',$userid)
                       ->where('permission_level_id',6)
                       ->get()->all();
             $buids=isset($buids[0]->object_id)?$buids[0]->object_id:'';
             return $buids;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
 public function getBrands($data){
    try{
          $manufid=$data['manufid'];
          $result=DB::table('brands')
                       ->select('brands.brand_name', 'brands.brand_id')
                       ->where('mfg_id',$manufid)
                       ->get()->all();
           $result = json_decode(json_encode($result), True);
            $resreturn='<option value="">Select Brands</option>';
        
            foreach ($result as $result) {
              $resreturn.='<option value="'.$result['brand_id']. '"> '.$result['brand_name'].'</option>';
            } 
        return Array('status'=>200,'message'=>'success','res'=>$resreturn);          
    }catch(Exception $e) {
          return 'Message: ' .$e->getMessage();
      }
}
  public function getProductGroupByBrand($data){
    try{
          $brandid=$data['brandid'];
          $result=DB::table('product_groups as pg')
                        ->select('pg.product_grp_ref_id','pg.product_grp_name')
                        ->join('products as p','pg.product_grp_ref_id','=','p.product_group_id')
                        ->join('brands as b','b.brand_id','=','p.brand_id')
                        ->where('p.brand_id',$brandid)
                        ->groupBy('pg.product_grp_ref_id')
                       ->get()->all();
           $result = json_decode(json_encode($result), True);
            $resreturn='<option value="">Select Product Group</option>';
        
            foreach ($result as $result) {
              $resreturn.='<option value="'.$result['product_grp_ref_id']. '"> '.$result['product_grp_name'].'</option>';
            } 
        return Array('status'=>200,'message'=>'success','res'=>$resreturn);          
    }catch(Exception $e) {
          return 'Message: ' .$e->getMessage();
      }    

 }
   /*Getting the Zones,states,Dcs,Fcs list based on userid */
   public function getBusinessUnitsByUserid($user_id)
   {
    try {
        $zonesData=array();
        $statesData=array();
        $dcsData=array();
        $fcsData=array();
       $zones_data=DB::selectFromWriteConnection(DB::raw("call getBubyUserAccess($user_id)"));
       if(isset($zones_data[0]->zones)){
          $zonesData= DB::table('business_units')
                       ->select('business_units.bu_id', 'business_units.bu_name')
                       ->whereIn('bu_id',explode(',', $zones_data[0]->zones))
                       ->get()->all(); 
        }
        if(count($zonesData)>0)
           $zonesData=$zonesData;
        if(isset($zones_data[0]->states)){
          $statesData= DB::table('business_units')
                       ->select('business_units.bu_id', 'business_units.bu_name')
                       ->whereIn('bu_id',explode(',', $zones_data[0]->states))
                       ->get()->all(); 
                
        }
        if(count($statesData)>0)
           $statesData=$statesData;
        if(isset($zones_data[0]->dc)){
          $dcsData= DB::table('business_units')
                       ->select('business_units.bu_id', 'business_units.bu_name')
                       ->whereIn('bu_id',explode(',', $zones_data[0]->dc))
                       ->get()->all(); 
        }
        if(count($dcsData)>0)
           $dcsData=$dcsData;
        if(isset($zones_data[0]->fc)){
          $fcsData= DB::table('business_units')
                       ->select('business_units.bu_id', 'business_units.bu_name')
                       ->whereIn('bu_id',explode(',', $zones_data[0]->fc))
                       ->get()->all(); 
         }
        if(count($fcsData)>0)
           $fcsData=$fcsData;
        
        
        return    json_encode(['zonedata'=>$zonesData,'statesdata'=>$statesData,'dcsData'=>$dcsData,'fcsData'=>$fcsData]);

        
    } catch (Exception $e) {
        return 'Message: ' .$e->getMessage();
    }
    


   }

   /*Getting the states,DC,Fc based on the parent business unit ID*/
   public function getBusinessUnitList($parent_id,$user_id)
   {
    try {
       if(isset($parent_id)&& $user_id != ''){
          $ojectID=$this->getBuidsByUserId($user_id);
          $ojectID=explode(',', $ojectID);        
          $resultData= DB::table('business_units')
                       ->select('business_units.bu_id', 'business_units.bu_name');
           if(!in_array(0, $ojectID))
           {
            $resultData=$resultData->whereIn('bu_id',$ojectID);
           }
           $resultData=$resultData->whereIn('parent_bu_id',explode(',', $parent_id))
                       ->get()->all(); 
          $bu_data = json_decode(json_encode($resultData), True);              
            
        }
        if(count($bu_data)>0)
          return $bu_data;
        else 
          return array(); 
           
    } catch (Exception $e) {
        return 'Message: ' .$e->getMessage();
    }
    


   }

    /**
     * Fetch the APOB & DC list
     * @param  int $bu_id  Business unit iD
     * @return Mixed Array          Array of DC+APOB with Business unit ID
     */
    public function getApobDcList($bu_id) {
        try {
            if($bu_id){
                $sql = "SELECT 
                    `lw`.`display_name`,
                    `bu_id`
                FROM
                    `legalentity_warehouses` `lw`
                        LEFT JOIN
                    `legal_entities` `le` ON `le`.`legal_entity_id` = `lw`.`legal_entity_id`
                WHERE
                    (`lw`.`legal_entity_id` IN (21837,2)
                        OR `le`.`legal_entity_type_id` = '1016')
                        AND `lw`.`dc_type` = '118001'
                        AND `lw`.`is_disabled` = '0'
                        AND (`lw`.`bu_id` IN (SELECT 
                            `bu_id`
                        FROM
                            `business_units`
                        WHERE
                            `bu_id` IN (SELECT 
                                    `bu_id`
                                FROM
                                    `business_units`
                                WHERE
                                    `parent_bu_id` = '$bu_id')
                                OR `parent_bu_id` IN (SELECT 
                                    `bu_id`
                                FROM
                                    `business_units`
                                WHERE
                                    `parent_bu_id` = '$bu_id')))";          
                $result = DB::select(DB::raw($sql));
                $bu_data = json_decode(json_encode($result), True);              
     
                if(count($bu_data)>0)
                  return $bu_data;
                else 
                  return array();
            }
               
        } catch (Exception $e) {
            return 'Message: ' .$e->getMessage();
        }
    }


   public function getWikiDataByLink($page_link){
        if($page_link != ""){
            $page_link = trim($page_link);
            $wikidata = DB::table("features")->select(["wiki_url","wiki_description"])->where("url","like","%".$page_link."%")->get()->all();
            return isset($wikidata[0]->wiki_url)?$wikidata[0]:[];
        }else{
            return [];
        }
    }


    public function getUsersByMasterLookupValue($value)
    {
        $usersList = [];
        if($value != '')
        {
            $msdata = DB::table('master_lookup')
                ->select('master_lookup_id','description','value')
                ->where('value',$value)->first();
            $rolesToFetch = isset($msdata->description)?explode(',',$msdata->description):[];
            $usersList = DB::table('role_access')                
                ->select('users.user_id','users.user_id as id','users.firstname','users.lastname','users.email_id','users.mobile_no',DB::raw('GetUserName(users.user_id,2) as name'),DB::raw('GetUserName(users.user_id,2) as username'))
                ->join('user_roles','role_access.role_id','=','user_roles.role_id')
                ->join('users','users.user_id','=','user_roles.user_id')
                ->where([
                    'users.is_active' => 1
                    ])
                ->whereIn('role_access.role_id',$rolesToFetch);
            
            $usersList = $usersList
                ->groupBy('users.user_id')
                ->orderBy('users.user_id', 'ASC')
                ->get()->all();
        }
        
        return $usersList;
    }
}
