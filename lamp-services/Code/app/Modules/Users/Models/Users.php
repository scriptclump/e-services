<?php

namespace App\Modules\Users\Models;
use App\Central\Repositories\RoleRepo;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use URL;
use \App\Modules\Roles\Models\Role;

class Users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $roleRepo = 'user_id';
    public $timestamps = false;
    public $_businessUnitDefault = 0;
    public $_categoriesDefault = 0;
    public $_roleList = [];
    
    public function __construct() {
        $this->roleRepo = new RoleRepo();
    }
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
                $this->emp_code = isset($data['emp_code']) ? $data['emp_code'] : '';
                
                $this->save();
                $legal_entity_id = $this->legal_entity_id;
                $userId = $this->user_id;
                if($legal_entity_id)
                {
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
     * [saveTempRoles User Temp Roles for giving temporary Roles for Users]
     * @param  [array] $data   [temporary roles]
     * @param  [int] $userId [user id]
     * @return [array]         [empty array]
     */
    public function saveTempRoles($data,$userId)
    {
        if($userId == '')   return [];
        $currentUserId = \Session::get('userId');   

        $this->deleteOldTempRoles($userId);
        
        if(isset($data['roles']) and !empty($data['roles'])){

            foreach ($data['roles'] as $roleId) {
                // Inserting Into User Roles Temp
                $query = '
                    INSERT INTO
                        user_roles_temp (role_id,user_id,expiry_date,created_by,updated_by)
                    VALUES (?,?,?,?,?)';
                $status = DB::INSERT($query,[$roleId,$userId,date('Y-m-d', strtotime($data['date'])),$currentUserId,$currentUserId]);
                // Log::info("Temp status ".$status);
            
                // Inserting Into User roles
                $query = '
                    INSERT INTO
                        user_roles (role_id,user_id,created_by,updated_by)
                    VALUES (?,?,?,?)';
                $status = DB::INSERT($query,[$roleId,$userId,$currentUserId,$currentUserId]);
                // Log::info("Role status ".$status);
            }
        }
        return [];
    }

   /**
    * [deleteOldTempRoles Method to Delete Temporary Roles before inserting them in to user_roles_temp table]
    * @param  [int] $userId [user id]
    */
    public function deleteOldTempRoles($userId)
    {
        $query = 'DELETE FROM user_roles_temp WHERE user_id = ?';
        return DB::DELETE($query,[$userId]);
    }
    /**
     * [updateReportingManagerByUserId To update ReportingManager of user]
     * @param  [int] $newUserId [new ReportingManager user id]
     * @param  [int] $oldUserId [old ReportingManager user id]
     * @return [int]            [users count]
     */
    public function updateReportingManagerByUserId($newUserId,$oldUserId)
    {
        $childUsers = $this->roleRepo->getAllChildIdsByUser($oldUserId);
        $childUsers = json_decode(json_encode($childUsers), true);
        $count = 0;
        foreach ($childUsers as $user) {
            DB::table('users')
                ->where('user_id',$user['user_id'])
                ->update(['reporting_manager_id' => $newUserId]);
            $count++;
        }
        $this->roleRepo->inactiveUser($oldUserId,0);   // To Block Single User Only...    
        return $count;
    }
    /**
     * [sendEmail To send mail]
     * @param  [int] $legal_entity_id [legal entity id]
     * @param  [int] $userId          [userId]
     * @param  [array] $data            [input data like firwstname, lastname ]
     */
    public function sendEmail($legal_entity_id,$userId, $data) {
        try
        {
            if ($userId) {
                $userId = $this->roleRepo->encodeData($userId);
                $legal_entity_id = $this->roleRepo->encodeData($legal_entity_id);
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
     * [getRedeemExportData  to list cashback redeem details between given date range]
     * @param  [date] $fromDate [from date]
     * @param  [date] $toDate   [to date]
     * @return [array]           [cashback redeem details]
     */
    public function getRedeemExportData($fromDate,$toDate)
    {
        $query = '
            SELECT 
                u.user_id AS "User ID",
                u.emp_code AS "Emp Code",
                u.firstname AS "First Name",
                u.lastname AS "Last Name",
                ROUND(IFNULL(SUM(
                    CASE e.transaction_type
                        WHEN 143002 THEN IFNULL(e.cash_back_amount,0)
                        WHEN 143001 THEN IFNULL(-e.cash_back_amount,0)
                    END),0),2) AS "Commission"
            FROM users as u
            LEFT JOIN ecash_transaction_history AS e ON e.user_id = u.user_id
            LEFt JOIN user_roles ON user_roles.user_id = u.user_id
            WHERE e.transaction_date BETWEEN "'.$fromDate.'" AND "'.$toDate.'"
            AND user_roles.role_id IN (89,53)
            GROUP BY u.user_id';

        $data = DB::SELECT($query);
        return $data;
    }
    /**
     * [getCashBackHistoryById To get cashback history of user]
     * @param  [int] $legal_entity_id [le id]
     * @param  [int] $user_id         [user id]
     * @return [array]                  [cashback history details]
     */
    public function getCashBackHistoryById($legal_entity_id,$user_id = null)
    {
        try
        {
            if(empty($legal_entity_id) or $legal_entity_id == null)
                return null;
            
            $query = DB::table("ecash_transaction_history as e")
                    ->select(
                        'gds_orders.order_code as order_code',
                        'gds_orders.gds_order_id as order_id',
                        DB::RAW('IFNULL(e.delivered_amount,0) as delivery_amt'),
                        DB::RAW('IFNULL(e.cash_back_amount,0) as cash_back_amt'),
                        'e.comment',
                        DB::RAW('getMastLookupValue(e.transaction_type) as transaction_type'),
                        'e.transaction_date'
                        )
                    ->leftJoin('gds_orders', 'gds_orders.gds_order_id', '=', 'e.order_id')
                    ;

            if($user_id == null)
                $query = $query->where('e.legal_entity_id',$legal_entity_id);
            else
                $query = $query->where('e.legal_entity_id',$legal_entity_id)->where('e.user_id',$user_id);

            $query = $query->orderBy('e.ecash_transaction_id','desc')->get()->all();
           
            return $query;

        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }
    /**
     * [updateEcashTransactionHistory to update cashback transaction history when users redeem their cashback]
     * @param  [int] $user_id         [user id]
     * @param  [int] $legal_entity_id [le id]
     * @param  [int] $amount          [amount]
     * @param  [string] $message         [message]
     * @return [boolean]                  [status of transaction]
     */
    public function updateEcashTransactionHistory($user_id,$legal_entity_id,$amount,$message)
    {
        // Log::info(__METHOD__." ".$user_id." ".$legal_entity_id." ".$amount." ".$message);
        if(floatval($amount) <= 0) return 0;
        $updateCash = 0;
        $today = date("Y-m-d H:i:s");
        $insertHistory =
            DB::table('ecash_transaction_history')
            ->insert([
                'user_id' => $user_id,
                'legal_entity_id' => $legal_entity_id,
                'cash_back_amount' => $amount,
                'comment' => $message,
                'transaction_type' => '143001',
                'transaction_date' => $today 
                ]);
        if($insertHistory)
        {
            // Log::info("In INSERT: ".$user_id." Amount ".$amount);
            $updateCash = 
                DB::table('user_ecash_creditlimit')
                ->where('user_id',$user_id)
                ->decrement('cashback',$amount);
        }
        return $updateCash;
    }
    /**
     * [getEcashAmount get ecash amount of user]
     * @param  [int] $userId [user id]
     * @return [int]         [cashback amount]
     */
    public function getEcashAmount($userId)
    {
        try
        {
            $cash = DB::TABLE('user_ecash_creditlimit')
                ->select(DB::RAW('IFNULL(cashback,0)-IFNULL(applied_cashback,0) as ecash'))
                ->where('user_id',$userId)
                ->first();
            $cash = isset($cash->ecash)?$cash->ecash:0;
            return is_null($cash)?0:round($cash,2);        
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
    * getTempRolesData [descripion]
    * TemporRoles Can be Multiple...,
    * @params $userId 
    */
    public function getTempRolesData($userId='')
    {
        if($userId == '')
            $userId = \Session::get('userId');

        $query = '
            SELECT GROUP_CONCAT(role_id) AS role_id
            FROM user_roles_temp
            WHERE user_id = '.$userId;

        $result = DB::SELECT($query);
        return isset($result[0]->role_id)?$result[0]->role_id:'';
    }

    /**
    * getTempRolesExpiryDate [descripion]
    * @params $userId 
    */
    public function getTempRolesExpiryDate($userId='')
    {
        if($userId == '')
            $userId = \Session::get('userId');

        $query = '
            SELECT DISTINCT(expiry_date)
            FROM user_roles_temp
            WHERE user_roles_temp.user_id = '.$userId;

        $result = DB::SELECT($query);
        return $result;
    }
    /**
     * [savePassword To save password]
     * @param  [array] $data [password & confirm password]
     * @return [array]       [sends status whether the password changed/not]
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
                        $this->roleRepo->updateDates('users', $data['user_id'], 'user_id', 0, 1, \Session::get('userId'));
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
     * [activateUser To activate user]
     * @param  [array] $data [contains le id of user]
     */
    public function activateUser($data)
    {
        try
        {
            if(!empty($data))
            {
                $legalEntityId = isset($data['legal_entity_id']) ? $data['legal_entity_id'] : 0;
                if($legalEntityId > 0)
                {
                    $this->assignRole($legalEntityId);
                    $this->assignPermissions($legalEntityId);
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [assignRole To assign role]
     * @param  [int] $legalEntityId [legal entity id]
     */
    public function assignRole($legalEntityId)
    {
        try
        {
            if($legalEntityId > 0)
            {
                $entityType = DB::table('legal_entities')
                        ->where('legal_entity_id', $legalEntityId)
                        ->select('legal_entity_type_id as entity_type', 'business_legal_name')
                        ->first();
                $entityTypeId = property_exists($entityType, 'entity_type') ? $entityType->entity_type  : 0;
                $legalEntityName = property_exists($entityType, 'business_legal_name') ? $entityType->business_legal_name : '';
               // Log::info('entityTypeId');
                //Log::info($entityTypeId);
                if($entityTypeId != 0)
                {
                    $roleId = DB::table('legal_entity_roles')
                            ->where('le_type_id', $entityTypeId)
                            ->select('role_id')
                            ->first();
                  //  Log::info('roleId');
//                    Log::info(var_dump($roleId));
                    if(!empty($roleId))
                    {
                        $this->where('legal_entity_id', $legalEntityId)
                            ->update(['is_active' => 1]);
                        $this->roleRepo->updateDates('legal_entities', $legalEntityId, 'legal_entity_id', 0, 1, \Session::get('userId'));
                        $userId = $this->where('legal_entity_id', $legalEntityId)
                                ->first(['user_id']);
        //                    $userId = DB::select('select user_id from users where legal_entity_id = '.$legalEntityId);
                       // Log::info('userId');
                       // Log::info($userId);
                        if(!empty($userId))
                        {
                            $userDetails = json_decode($userId);
                            if(property_exists($userDetails, 'user_id'))
                            {
//                                $query = "insert into user_roles (role_id, user_id) values (?, ?)";
//                                Log::info('query');
//                                Log::info($query);
//                                DB::insert($query, [$roleId->role_id, $userDetails->user_id]);
                                $this->cloneRoleAndAssign($roleId->role_id, $userDetails->user_id, $legalEntityId, $legalEntityName);
                            }
                        }
                    }else{
                        \Log::error('Unable to assign role to user as role is not mapped to entity type.');
                    }
                }else{
                    \Log::error('Unable to assign role to user as entity type is zero.');
                }
            }
                
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [cloneRoleAndAssign To clone role and assign to new role]
     * @param  [int] $roleId          [old role id]
     * @param  [int] $userId          [user id]
     * @param  [int] $legalEntityId   [le id]
     * @param  [string] $legalEntityName [le display name]
     */
    public function cloneRoleAndAssign($roleId, $userId, $legalEntityId, $legalEntityName = null)
    {
        try
        {
            if($roleId > 0 && $userId > 0)
            {
                $role = Role::find($roleId);
                $existingRoleName = $legalEntityName . ' ' . $role->name;
                $checkRole = Role::where(['name' => $existingRoleName, 'legal_entity_id' => $legalEntityId])->select('role_id')->first();
                if(!is_object($checkRole))
                {
                    $newRole = $role->replicate();
                    $newRole->name = $legalEntityName . ' ' . $newRole->name;
                    $newRole->description = $legalEntityName . ' ' . $newRole->description;
                    $newRole->legal_entity_id = $legalEntityId;
                    $newRole->parent_role_id = $roleId;
                    $newRole->save();
                    $newRoleId = $newRole->role_id;                    
                }else{
                    $newRoleId = $checkRole->role_id;
                    DB::statement('DELETE FROM role_access WHERE role_id = '.$newRoleId);
                }
                DB::statement('INSERT INTO role_access (role_id, feature_id)  '
                            . '(SELECT '.$newRoleId.', feature_id FROM role_access WHERE role_id = '.$roleId.')');
                $checkUserRoles = DB::select("select user_roles_id from user_roles where role_id = ".$newRoleId." and user_id = ".$userId);
                if(empty($checkUserRoles))
                {
                    $query = "insert into user_roles (role_id, user_id) values (".$newRoleId.", ".$userId.")";
                    //Log::info('query');
                    //Log::info($query);
                    DB::insert($query);
                }                
            }else{
                Log::info('Role Id or User Id is not greater than zero.');
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [assignPermissions To assign permissions]
     * @param  [int] $legalEntityId [legal entity id]
     */
    public function assignPermissions($legalEntityId)
    {
        try
        {
            Log::info('we are in '.__METHOD__);
            $userId = 0;
            if($legalEntityId > 0)
            {
                $userInfo = $this->where('legal_entity_id', $legalEntityId)->first(['user_id']);
               // Log::info($userInfo);
                if(!empty($userInfo))
                {
                    $userDetails = json_decode($userInfo);
                    $userId = property_exists($userDetails, 'user_id') ? $userDetails->user_id : 0;
                }
               // Log::info($userId);
                if($userId > 0)
                {
                    $permissionLevels = DB::table('permission_level')->select('permission_level_id')->get()->all();
                    //Log::info($permissionLevels);
                    foreach ($permissionLevels as $permission) {
                        $permissionLevelId = property_exists($permission, 'permission_level_id') ? $permission->permission_level_id : '';
                       // Log::info($permissionLevelId);
                        if($permissionLevelId > 0)
                        {
                            DB::table('user_permssion')->insert(['permission_level_id' => $permissionLevelId,
                                'user_id' => $userId, 'object_id' => 0]);
                        }
                    }
                }
            }
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
    /**
     * [getManagersList To get managers list]
     * @param  [array] $data [contains role id, le id]
     * @return [array]       [users list]
     */
    public function getManagersList($data) {
        try
        {
            $roles = [];
            $legalEntityId = \Session::get('legal_entity_id');
            $currentRoleId = \Session::get('roleId');
            $roleIds = isset($data['role_id']) ? $data['role_id'] : 0;
            $roleLists = [];
            if(!empty($data))
            {
                if(is_array($roleIds))
                {
                    foreach($roleIds as $roleId)
                    {
                        $roleLists = $this->roleRepo->getParentRolesbyRoleId($roleId, $legalEntityId);
                    }
                }
                $userId = isset($data['user_id']) ? $data['user_id'] : 0;
                if(!empty($roleIds) && $legalEntityId > 0)
                {
                    if($userId > 0)
                    {
                        $roles = DB::table('user_roles')
                            ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                            ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                            //->where(['users.legal_entity_id' => $legalEntityId])
                            ->where('users.user_id', '!=', $userId)
                            ->whereIn('user_roles.role_id', $roleLists)
                            ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                            ->groupBy('user_roles.user_id')
                            ->get()->all();
                    }else{
                        $roles = DB::table('user_roles')
                            ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                            ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                            //->where(['users.legal_entity_id' => $legalEntityId])
                            ->whereIn('user_roles.role_id', $roleLists)
                            ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                            ->groupBy('user_roles.user_id')
                            ->get()->all();
                    }
                }elseif(!empty($roleId) && $currentRoleId == 1 && $legalEntityId == 0){
                    if($userId > 0)
                    {
                        $roles = DB::table('user_roles')
                            ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                            ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
//                            ->where(['users.legal_entity_id' => $legalEntityId])
                            ->where('users.user_id', '!=', $userId)
                            ->whereIn('user_roles.role_id', $roleLists)
                            ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                            ->groupBy('user_roles.user_id')
                            ->get()->all();
                    }else{
                        $roles = DB::table('user_roles')
                            ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                            ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
//                            ->where(['users.legal_entity_id' => $legalEntityId])
                            ->whereIn('user_roles.role_id', $roleLists)
                            ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                            ->groupBy('user_roles.user_id')
                            ->get()->all();
                    }
                }
            }
            $currentUserId = \Session::get('userId');
            $currentUser = DB::table('users')
                    ->where(['users.user_id' => $currentUserId, 'users.legal_entity_id' => $legalEntityId])
                    ->select('users.user_id', DB::raw('concat(users.firstname, " ",users.lastname) as name'))
                    ->get()->all();
            // Log::info($roles);
            // Log::info($currentUser);
            $roles = array_unique(array_merge($roles, $currentUser),SORT_REGULAR);
            // Log::info($roles);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($roles);
    }
    /**
     * [getParentRoleList get parent role of a role]
     * @param  [int] $roleId        [role id]
     * @param  [int] $legalEntityId [le id]
     * @return [array]                [parent role details]
     */
    public function getParentRoleList($roleId, $legalEntityId) {
        try
        {
            if(!empty($roleId) && $legalEntityId > 0)
            {
                $roleList = DB::table('roles')
                        ->whereIn('role_id', $roleId)
                        ->select('parent_role_id')
                        ->get()->all();
                if(!empty($roleList))
                {
                    foreach($roleList as $roleData)
                    {
                        $roleDataId = property_exists($roleData, 'parent_role_id') ? $roleData->parent_role_id : 0;
                        if($roleDataId > 0)
                        {
                            $this->_roleList[] = $roleDataId;
//                            $this->getParentRoleList($roleDataId, $legalEntityId);
                        }
                    }
                }
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $this->_roleList;
    }
    /**
     * [getCompanys get legal entity list]
     * @return [array] [Le list]
     */
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
    /**
     * [getSuppliers To get products under supplier]
     * @return [array] [products list]
     */
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
    /**
     * [getLegalEntityIdByUserId To retrieve le id by user id]
     * @param  [int] $userId [user id]
     */
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
    /**
     * [getProducts To get products under a category]
     * @return [array] [products list]
     */
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
    /**
     * [getCategories To get categories list]
     * @return [array] [categories list]
     */
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
    /**
     * [getUserPermission To get user permissions]
     * @param  [int] $permissionLevelId [permission levl id]
     * @param  [int] $userId            [user id]
     * @return [array]                    [permission list]
     */
    public function getUserPermission($permissionLevelId, $userId)
    {
        try
        {
            $result = [];
            if($permissionLevelId > 0 && $userId > 0)
            {
                $result = DB::table('user_permssion')
                        ->where(['permission_level_id' => $permissionLevelId, 'user_id' => $userId])
                        ->pluck(DB::raw('group_concat(object_id) as ids'))->all();
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }
    /**
     * [getBusinessUnit To get business units]
     * @param  [array] $data       [user id]
     * @param  [int] $parentBuId [parent Bu Id]
     * @return [array]             [bu list]
     */
    public function getBusinessUnit($data, $parentBuId) {
        try {
            $result = [];
            $response = [];
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            $status = isset($data['checked']) ? $data['checked'] : 'false';
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
                $userPermissions = $this->getUserPermission($permissionLevelId, $userId);
                if(!empty($userPermissions))
                {
                    $userPermissions = isset($userPermissions[0]) ? array_map('intval', explode(',', $userPermissions[0])) : [];
                }
            }
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
                        if($status != 'false')
                        {
                            $items['checked'] = true;
                        }
                    }
                    if(!empty($items))
                    {
                        $temp['items'][] = $items;
                    }
                    if(!empty($temp))
                    {
                        \Log::info($bu->bu_id);
                        if(!empty($userPermissions) && in_array($bu->bu_id, $userPermissions))
                        {
                            $temp['checked'] = true;
                        }
                        if($status != 'false')
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
    /**
     * [getBusinessUnitList Get bu list by access level]
     * @param  [int] $userId [user id]
     * @return [array]         [bu list]
     */
    public function getBusinessUnitList($userId = null) {
        try {
            $result = [];
            $response = [];
//            $userId = \Session::get('userId');
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
                $userPermissions = $this->getUserPermission($permissionLevelId, $userId);                
                if(!empty($userPermissions))
                {
                    $userPermissions = isset($userPermissions[0]) ? array_map('intval', explode(',', $userPermissions[0])) : [];
                }
            }
            $legalEntityId = \Session::get('legal_entity_id');
            $completeAccess = $this->roleRepo->checkPermissionByFeatureCode('GLB0001');
//            $response = $this->getBusinessUnitCollection(0);
            $response = $this->fetchBussinessUnitsList(0,"",$legalEntityId,$completeAccess);
//            echo "<pre>";print_r($temp);die;
            if(!empty($response))
            {
                $result = $response;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }
    /**
     * [getBusinessUnitData get bu list by access level]
     * @param  [int] $userId [user id]
     * @return [array]         [response]
     */
    public function getBusinessUnitData($userId = null) {
        try {
            $result = 0;
            $response = 0;
//            $userId = \Session::get('userId');
            $userPermissions = [];
            $permissionLevelId = 0;
            if($userId)
            {
                $permissionLevelData = DB::table('permission_level')
                    ->where('name', 'sbu_data')
                    ->pluck('permission_level_id')->all();
                if(!empty($permissionLevelData))
                {
                    $permissionLevelId = isset($permissionLevelData[0]) ? $permissionLevelData[0] : 0;
                }
                $userPermissions = $this->getUserPermission($permissionLevelId, $userId);                
                if(!empty($userPermissions))
                {
                    $response = isset($userPermissions[0]) ? array_map('intval', explode(',', $userPermissions[0])) : [];
                }
            }
//            $response = $this->getBusinessUnitCollection(0);
//            $response = $this->fetchBussinessUnitsList(0);
//            echo "<pre>";print_r($temp);die;
            if(!empty($response))
            {
                $result = $response;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }
    /**
     * [getBusinessUnits get bu list]
     * @return [array] [get bu list with bu name & id]
     */
    public function getBusinessUnits()
    {
        try
        {
            $completeAccess = $this->roleRepo->checkPermissionByFeatureCode('GLB0001');
            $legalEntityId = \Session::get('legal_entity_id');
            $userId=\Session::get('userId');
            $data = DB::table('user_permssion')
                        ->where(['user_id' => $userId, 'permission_level_id' => 6])
                        ->groupBy('object_id')
                        ->pluck('object_id')->all();
            $dataAccess = 
                DB::table('business_units')
                    ->select('bu_id',  DB::raw('concat(bu_name," (",cost_center,")") as bu_name'));
            if(!$completeAccess && !in_array(0,$data))
                $dataAccess = $dataAccess->whereIn('bu_id',$data);

            return $dataAccess->get()->all();
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
    /**
     * [fetchBussinessUnitsList To get bu list]
     * @param  integer $parent          [parent bu id]
     * @param  string  $user_tree_array [bu list html code]
     * @param  string  $legalEntityId   [legal Entity Id]
     * @param  [type]  $completeAccess  [1 if user has global access else 0]
     * @return [string]                   [bu list html code]
     */
    function fetchBussinessUnitsList($parent = 0, $user_tree_array = '',$legalEntityId = "",$completeAccess) {

        if(empty($legalEntityId))
            $legalEntityId = \Session::get('legal_entity_id');
        if (!is_array($user_tree_array))
            $user_tree_array = array();

        $buCollection = 
            DB::table('business_units')
                ->select('bu_id', 'bu_name', 'parent_bu_id')
                ->where('parent_bu_id',$parent);
        // If the logged in user has full access, then he can view all warehouses
        if(!$completeAccess)
            $buCollection = $buCollection->where('legal_entity_id',$legalEntityId);
        $buCollection = $buCollection->get()->all();
        
        if(!empty($buCollection))
        {
            $user_tree_array[] = "<ul>";
            if(!$this->_businessUnitDefault)
            {
                $user_tree_array[] = '<li id="0"> ALL </li>';
                $this->_businessUnitDefault = 1;
            }
            foreach($buCollection as $buData) {
                $user_tree_array[] = '<li id="'.$buData->bu_id.'">' . $buData->bu_name;
                $user_tree_array = $this->fetchBussinessUnitsList($buData->bu_id, $user_tree_array,$legalEntityId,$completeAccess);
                $user_tree_array[] = "</li>";
            }
            $user_tree_array[] = "</ul>";
        }
        return $user_tree_array;
    }
    /**
     * [getCategoryList  To get category list by access level]
     * @return [string] [category html code]
     */
    public function getCategoryList() {
        try {
            $result = [];
            $response = [];
            $userId = \Session::get('userId');
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
                    $userPermissions = isset($userPermissions[0]) ? array_map('intval', explode(',', $userPermissions[0])) : [];
                }
            }
            $segmentList = DB::table('segment_mapping')
                    ->join('master_lookup', 'master_lookup.value', '=', 'segment_mapping.value')
                    ->select('segment_mapping.value', 'master_lookup.master_lookup_name')
                    ->groupBy('segment_mapping.value')
                    ->get()->all();
            $user_tree_array[] = "<ul>";
            $user_tree_array[] = '<li id="0"> ALL </li>';
            if(!empty($segmentList))
            {
                foreach($segmentList as $segmentValue)
                {
                    $segmentData = $segmentValue->value;
                    $segmentName = $segmentValue->master_lookup_name;
//                    $user_tree_array[] = "<ul>";
                    $user_tree_array[] = '<li id="'.$segmentData.'"> '.$segmentName;
                    $response = $this->fetchCategoryList($segmentData, 0, $user_tree_array);
//                    echo "<pre>";print_R($response);die;
                    $user_tree_array[] = '</li>';
//                    $user_tree_array[] = "</ul>";                    
                }
            }
//            $user_tree_array[] = '</li>';
            $user_tree_array[] = "</ul>";
//            $response = $user_tree_array;
            if(!empty($response))
            {
                $result = $response;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }
    /**
     * [fetchCategoryList description]
     * @param  [int]  $segmentData     [segment id]
     * @param  integer $parent          [category parent id]
     * @param  string  $user_tree_array [html code]
     * @return [string]                   [category html code]
     */
    function fetchCategoryList($segmentData, $parent = 0, $user_tree_array = '') {
        if (!is_array($user_tree_array))
            $user_tree_array = array();
        
        $buCollection = DB::table('categories')
                    ->join('segment_mapping', 'segment_mapping.mp_category_id', '=', 'categories.category_id')
                    ->where(['parent_id' => $parent, 'segment_mapping.value' => $segmentData])
                    ->select('categories.category_id', 'categories.cat_name', 'categories.parent_id')
                    ->groupBy('categories.cat_name')
                    ->get()->all();
        
        if(!empty($buCollection))
        {
            $user_tree_array[] = "<ul>";
//            if(!$this->_categoriesDefault)
//            {
//                $user_tree_array[] = '<li id="0"> ALL';
//                $this->_categoriesDefault = 1;
//            }
            foreach($buCollection as $buData) {
                $user_tree_array[] = '<li id="'.$buData->category_id.'">'  . $buData->cat_name ;
                $user_tree_array = $this->fetchCategoryList($segmentData, $buData->category_id, $user_tree_array);
                $user_tree_array[] = "</li>";
            }
            $user_tree_array[] = "</ul>";
        }
        
        return $user_tree_array;
    }
    
    /**
     * [getManufacturesBrandsList get  brands list]
     * @return [string] [ brands html code]
     */
    public function getManufacturesBrandsList() {
        try {
            $result = [];
            $response = [];
            $userId = \Session::get('userId');
            $legalEntityId = \Session::get('legal_entity_id');
            $user_tree_array = [];

            $brandsList = DB::table('brands')
                                ->select('brand_id', 'brand_name')
                                ->orderBy('brand_name', 'asc')
                                ->get()->all();
            if(!empty($brandsList))
            {
                $user_tree_array[] = "<ul>";
                $user_tree_array[] = '<li id="0"> ALL </li>';
                foreach($brandsList as $brand)
                {
                    $brandId = property_exists($brand, 'brand_id') ? $brand->brand_id : 0;
                    $brandName = property_exists($brand, 'brand_name') ? $brand->brand_name : '';
                    if($brandId > 0)
                    {
                        $user_tree_array[] = '<li id="'.$brandId.'" title="brands"> '.$brandName.'</li>';
                    }
                }
                $user_tree_array[] = "</ul>";
            }            
            if(!empty($user_tree_array))
            {
                $result = $user_tree_array;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }
    /**
     * [getBrandsList get manufacturer brands list]
     * @return [string] [manufacturer brands html code]
     */
    public function getBrandsList() {
        try {
            $result = [];
            $response = [];
            $userId = \Session::get('userId');
            $legalEntityId = \Session::get('legal_entity_id');
            $user_tree_array = [];
            
            $manufacturerList = DB::table('legal_entities')
//                    ->where(['legal_entity_type_id' => 1006, 'parent_id' => $legalEntityId])
                    ->where(['legal_entity_type_id' => 1006])
                    ->select('legal_entity_id', 'business_legal_name')
                    ->orderBy('business_legal_name', 'asc')
                    ->get()->all();
            if(!empty($manufacturerList))
            {
                $user_tree_array[] = "<ul>";
                $user_tree_array[] = '<li id="0"> ALL </li>';
                foreach($manufacturerList as $manufacturer)
                {
                    $manufacturerId = property_exists($manufacturer, 'legal_entity_id') ? $manufacturer->legal_entity_id : 0;
                    $manufacturerName = property_exists($manufacturer, 'business_legal_name') ? $manufacturer->business_legal_name : '';                    
                    if($manufacturerId > 0)
                    {
                        $user_tree_array[] = '<li id="'.$manufacturerId.'" title="brands"> '.$manufacturerName;
                        $brandsList = DB::table('brands')
                                ->where(['legal_entity_id' => $manufacturerId])
                                ->select('brand_id', 'brand_name')
                                ->get()->all();
                        if(!empty($brandsList))
                        {
                            $user_tree_array[] = "<ul>";
                            foreach($brandsList as $brand)
                            {
                                $brandId = property_exists($brand, 'brand_id') ? $brand->brand_id : 0;
                                $brandName = property_exists($brand, 'brand_name') ? $brand->brand_name : '';
                                if($brandId > 0)
                                {
                                    $user_tree_array[] = '<li id="'.$brandId.'" title="brands"> '.$brandName.'</li>';
                                }
                            }
                            $user_tree_array[] = "</ul>";
                        }
                        $user_tree_array[] = '</li>';
                    }
                }
                $user_tree_array[] = '</ul>';
            }            
            if(!empty($user_tree_array))
            {
                $result = $user_tree_array;
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $result;
    }
    /**
     * [categoryParentChildTree get category parent child tree]
     * @param  integer $parent              [parent id]
     * @param  string  $spacing             [no of spaces]
     * @param  string  $category_tree_array [category tree array]
     * @return [array]                       [category tree array]
     */
    public function categoryParentChildTree($parent = 0, $spacing = '', $category_tree_array = '') {
        $buCollection = DB::table('business_units')
                    ->where('parent_bu_id', $parent)
                    ->select('bu_id', 'bu_name', 'parent_bu_id')
                    ->get()->all();
//        echo "<pre>";print_R($buCollection);die;
        foreach($buCollection as $buData) {
            $category_tree_array[] = array("id" => $buData->bu_id, "name" => $buData->bu_name);
            $category_tree_array = $this->categoryParentChildTree($buData->bu_id, '', $category_tree_array);
        }
        return $category_tree_array;
    }
    /**
     * [checkForChildBusinessUnits Check child bu's exist]
     * @param  [int] $businessUnitId [bu id]
     * @return [int]                 [count]
     */
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
    /**
     * [getSegmentMapping Get segments ]
     * @param  [array] $data [userid]
     */
    public function getSegmentMapping($data) {
        try {
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            $result = ["label" => "Test", "expanded" => "false"];
            $response = [];
            $segmentData = DB::table('segment_mapping')
                    ->join('master_lookup', 'master_lookup.value', '=', 'segment_mapping.value')
                    ->join('master_lookup_categories', 'master_lookup_categories.mas_cat_id', '=', 'master_lookup.mas_cat_id')
//                    ->join('categories', 'categories.category_id', '=', 'segment_mapping.mp_category_id')
//                    ->where(['master_lookup_categories.mas_cat_name' => 'Business Segments', 'categories.parent_id' => 0])
                    ->where(['master_lookup_categories.mas_cat_name' => 'Business Segments'])
                    ->select('master_lookup.value', 'master_lookup.master_lookup_name')
                    ->groupBy('master_lookup.value')
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
                foreach($segmentData as $masterLookup)
                {
                    $items = [];
                    $temp = [];
                    $temp['label'] = $masterLookup->master_lookup_name."<input type='hidden' name='master_lookup_id' value ='".$masterLookup->value."' />";
//                    $checkForChilds = $this->checkForChildCategories($masterLookup->value);
                    $checkForChilds = DB::table('segment_mapping')
                            ->where('segment_mapping.value', $masterLookup->value)
                            ->count();
                    if($checkForChilds > 0)
                    {
                        $items['value'] = '/users/getparentcategory/'.$userId.'/'.$masterLookup->value;
                        $items['label'] = 'Loading...';
                        $items['id'] = $masterLookup->value;
                    }
                    if(!empty($items))
                    {
                        $temp['items'][] = $items;
                    }
                    if(!empty($temp))
                    {
                        if(!empty($userPermissions) && in_array($masterLookup->value, $userPermissions))
                        {
                            $temp['checked'] = true;
                        }
                        $response[] = $temp;
                    }
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
    /**
     * [getCategoryById get category names by id's]
     * @param  [int] $userId     [user id]
     * @param  [int] $categoryId [category Id]
     * @param  [array] $data       [whether they r selected or not]
     * @return [array]             [category list]
     */
    public function getCategoryById($userId, $categoryId, $data) {
        try {
            $result = [];
            $status = isset($data['checked']) ? $data['checked'] : 'false';
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
                            if($status != 'false')
                            {
                                $items['checked'] = true;
                            }
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
                            if($status != 'false')
                            {
                                $temp['checked'] = true;
                            }
                            $response[] = $temp;
                        }
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
    /**
     * [getParentCategories get parent category of a category]
     * @param  [int] $userId    [user id]
     * @param  [int] $segmentId [category id]
     * @param  [array] $data      [checked->whether they r selected or not]
     * @return [array]            [category html code]
     */
    public function getParentCategories($userId, $segmentId, $data) {
        try {
            $result = [];
            $status = isset($data['checked']) ? $data['checked'] : false;
            if($segmentId > 0)
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
                $segmentData = DB::table('segment_mapping')
                        ->join('categories', 'categories.category_id', '=', 'segment_mapping.mp_category_id')
                        ->where(['segment_mapping.value' => $segmentId, 'categories.parent_id' => 0])
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
                            if($status != 'false')
                            {
                                $items['checked'] = true;
                            }
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
                            if($status != 'false')
                            {
                                $temp['checked'] = true;
                            }
                            $response[] = $temp;
                        }
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
    /**
     * [checkForChildCategories Get child category of parent category]
     * @param  [int] $categoryId [ parent category id]
     * @return [int]             [If it has child categories then 1 else 0]
     */
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
    /**
     * [saveUser To save user]
     * @param  [array]  $data   [user information]
     * @param  integer $userId [give user id for update else empty]
     * @return [int]          [user id]
     */
    public function saveUser($data, $userId = 0) {

        
        if ($userId > 0) {

            if(isset($data['user_group'])){
                unset($data['user_group']); 
            }
            if(isset($data['temp_role_id'])){
                unset($data['temp_role_id']);
            }
             if(isset($data['temp_role_expiry_date'])){
                unset($data['temp_role_expiry_date']);
            }  
            if(isset($data['user_id'])){
                unset($data['user_id']);
            }

            $query=DB::table('users')->where('user_id', $userId)->update($data);

        } else {
            if ((empty($data['getuserid']))) {
                unset($data['getuserid']);
                if(isset($data['user_group'])){
                    unset($data['user_group']);
                }
                $data['created_at'] = date('Y-m-d H:i:s');
                $userId = DB::table('users')->insertGetId($data);                
            } else {
                unset($data['getuserid']);
                DB::table('users')->where('user_id', $userId)->update($data);
            }
        }        
        $this->roleRepo->updateDates('users', $userId, 'user_id', 0, 1, \Session::get('userId'));

        return $userId;
    }
    /**
     * [getUsers To get user info using user id]
     * @param  [int] $userId [user id]
     * @return [array]         [users list]
     */
    public function getUsers($userId) {
        $result = DB::table('users')
                    ->select('user_id','firstname','lastname','email_id','mobile_no')
                    ->where('user_id','=',$userId)
                    ->first();
        return $result;
    }
    /**
     * [checkParentId insert into user permission]
     * @param  [int] $permissionLevelId [permission Level Id]
     * @param  [int] $objectId          [Object id]
     * @param  [array] $insertArray       [user info]
     * @param  [int] $userId            [user id]
     */
    public function checkParentId($permissionLevelId, $objectId, $insertArray, $userId)
    {
        try
        {
            if($permissionLevelId > 0 && $objectId > 0)
            {
                $permissionLevelData = DB::table('permission_level')
                        ->where('permission_level_id', $permissionLevelId)
                        ->first(['name']);
//                echo "<pre>";print_R($permissionLevelName);die;
                if(!empty($permissionLevelData))
                {
                    $permissionLevelName = property_exists($permissionLevelData, 'name') ? $permissionLevelData->name : '';
                    if($permissionLevelName != '')
                    {
                        switch($permissionLevelName)
                        {
                            case 'category':
                                $parentCategoryData = DB::table('categories')
                                    ->where('category_id', $objectId)
                                    ->first(['parent_id']);
                                if(!empty($parentCategoryData))
                                {
                                    $parentCategoryId = property_exists($permissionLevelData, 'parent_id') ? $permissionLevelData->parent_id : 0;
                                    if($parentCategoryId > 0)
                                    {
                                        $response = DB::table('user_permssion')
                                                ->where(['object_id' => $parentCategoryId, 
                                                    'permission_level_id' => $permissionLevelId,
                                                    'user_id' => $userId])
                                                ->pluck('user_permission_id')->all();
                                        echo "<pre>";print_R($response);die;
                                        if(empty($response))
                                        {
                                            $insertArray['object_id'] = $objectId;
                                            $finalInsertArray[] = $insertArray;
                                            DB::table('user_permssion')->insert($finalInsertArray);
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [makeExportUsersExcelDownload To export users]
     * @return [array] [users list]
     */
    public function makeExportUsersExcelDownload()
    {
        $legalEntityId = \Session::get('legal_entity_id');
        $query = '
            SELECT
                @serial:=@serial+1 AS "S. No",
                concat(users.firstname,\' \',users.lastname) AS "Full Name",
                group_concat(roles.name) AS "Role",
                GetUserName(users.reporting_manager_id, 2) AS "Reporting Manager",
                users.email_id AS "Email Id",
                users.mobile_no AS "Mobile No",
                users.emp_code AS "Employee Id",
                if(users.is_active = 1, "Active", "In-Active") AS "Status"
            FROM
                users
            LEFT JOIN user_roles ON users.user_id = user_roles.user_id
            LEFT JOIN roles ON roles.role_id = user_roles.role_id
            LEFT JOIN legal_entities ON legal_entities.legal_entity_id = users.legal_entity_id,
            (SELECT @serial:=0) AS s
            WHERE
                users.legal_entity_id = '.$legalEntityId.' AND
                legal_entities.legal_entity_type_id not like "3%"
            GROUP BY users.user_id;';

        $results = DB::select($query);
        return $results;
    }
    /**
     * [getUsersGroupList To get user groups list]
     * @return [array] [user groups list]
     */
    public function getUsersGroupList(){
        $getUserGroup = DB::table('master_lookup')
                        ->where('mas_cat_id','=','115')
                        ->get()->all();

        return $getUserGroup;
    }
    /**
     * [saveUserGroupData To save user group data]
     * @param  [int] $id      [user id]
     * @param  [int] $groupId [group id]
     * @return [int]          [1]
     */
    public function saveUserGroupData($id,$groupId){


        if($groupId!=0){

            foreach ($groupId as $key => $value) {

                $data="insert into chat_user_groups(user_id,group_id) values($id,$value)";

                $query=DB::insert(DB::raw($data));

            }
        }


        return 1;
    }
    /**
     * [getUserGroup To get  user groups list]
     * @param  [int] $master_cat_id [mas cat id ]
    * @return [array]                [user group info ]
     */
    public function getUserGroup($master_cat_id){
         $response = [];

                $response = DB::table('master_lookup')
                    ->where('master_lookup.mas_cat_id', $master_cat_id)
                    ->select('master_lookup.master_lookup_name', 'master_lookup.value')
                    ->get()->all();
        return $response;
            

    }
    /**
     * [getSelectedUserGroup get user group id of user group]
     * @param  [int] $id [user id]
     * @return [array]     [user group id]
     */
    public function getSelectedUserGroup($id){
        $selected_group ="select group_id FROM chat_user_groups  INNER JOIN master_lookup  ON group_id= VALUE AND user_id=$id";

        $data=DB::select(DB::raw($selected_group));

        return $data;
    }
    /**
     * [delUserGroupData Delete user group]
     * @param  [int] $id [user id]
     */
    public function delUserGroupData($id){


        $deleteDetails = DB::table('chat_user_groups')->where('user_id','=',$id)->delete();

        return $deleteDetails;
    }
    /**
     * [getBusinesUnitData To get business unit list]
     * @return [array] [ business unit list]
     */
    public function getBusinesUnitData(){
        $userId = \Session::get('userId');
        $getBusinessUnitData=DB::selectFromWriteConnection(DB::raw("CALL getBusinessTypesData(".$userId.")"));
        return $getBusinessUnitData;

    }
    /**
     * [getAuthDetails To get email & password of a user]
     * @param  [id] $user_id [user id]
     * @return [array]          [email & password of a user]
     */
    public function getAuthDetails($user_id){
        $query=DB::table('users')
               ->select('email_id','password')
               ->where('user_id',$user_id)
               ->get()->all(); 
        $result=json_decode(json_encode($query),1);
        $getauthdetails = isset($result[0])?$result[0]:'Invalid User';   
        return $getauthdetails;
    }
    /**
     * [getUserPassword To get user password]
     * @param  [int] $user_id [user id]
     * @return [boolean]          [whether the password is default password(ebutor@123/Ebutor@123) then it is 1 else 0]
     */
    public function getUserPassword($user_id){
    $userpassword=DB::table('users')
                ->select('password')
                ->where('user_id',$user_id)
                ->first();
    $result=json_decode(json_encode($userpassword),true);
    if($result['password']=='57d2be4ca4e5f73caa4e99b29efd69c7' ||$result['password']=='901b3f89b1d1c2de43cb97ceb1323c4b'){
    return 1;
    }
    return 0;
   }
   
}
