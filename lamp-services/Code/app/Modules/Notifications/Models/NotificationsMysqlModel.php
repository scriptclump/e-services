<?php
namespace App\Modules\Notifications\Models;
use Illuminate\Database\Eloquent\Model;
use \DB;
use \Log;
use \Session;

class NotificationsMysqlModel extends Model {

    protected $table = 'notification_template';
    protected $primaryKey = 'notification_template_id';
    protected $_rolesList;
    
    public function getAllTemplates($roleId, $userId, $legalEntityId) {
        try
        {
            $collection = ['DEFAULT'];
            $templateCollection = $this
                    ->join('notification_recipients', 'notification_recipients.notification_template_id', '=', 'notification_template.notification_template_id')                    
                    ->orWhere(['notification_recipients.notificaiton_recipient_users' => $userId])
                    ->orWhereIn('notification_recipients.notificaiton_recipient_roles', explode(',', $roleId))
                    ->orWhere(['notification_recipients.notificaiton_recipient_legal_entities' => $legalEntityId])
                    ->select(DB::raw('group_concat(DISTINCT notification_template.notification_code) as notification_codes'))
                    ->first();
            if(!empty($templateCollection))
            {
                $collection = $templateCollection->toArray();
                if(!Session::has('notification_codes'))
                {
                    Session::put($collection);
                }
            }            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $collection;
    }
    
    public function getAllNotificationTemplates() {
        try
        {
            $collection = ['DEFAULT'];
            DB::enableQueryLog();
//            $templateCollection = $this                    
//                    ->join('notification_recipients as nr', 'notification_recipients.notification_template_id', '=', 'notification_template.notification_template_id')
//                    ->leftJoin(DB::raw('users ON FIND_IN_SET(users.user_id, nr.notificaiton_recipient_users)'))
//                    ->leftJoin(DB::raw('roles ON FIND_IN_SET(roles.role_id, nr.notificaiton_recipient_roles)'))
//                    ->select('notificaiton_template.*'
////                            DB::raw('(SELECT GROUP_CONCAT(users.firstname, " ", users.lastname) FROM users WHERE FIND_IN_SET(user_id, nr.notificaiton_recipient_users)) AS user_name'),
////                            DB::raw('(SELECT GROUP_CONCAT(NAME) FROM roles WHERE FIND_IN_SET(role_id, nr.notificaiton_recipient_roles)) AS role_name')
//                            )
//                    ->groupBy('nr.notification_template_id')
//                    ->get();

               $templateCollection = DB::SELECT(" SELECT nt.`notification_message`,nt.`notification_code`,nt.`notify_rm`,nt.`notification_template_id`,GROUP_CONCAT(`GetUserName`(users.`user_id`,2)) AS users_list,GROUP_CONCAT(`getRolesNameById`(role_id)) AS roles_list,GROUP_CONCAT(`getBusinessLegalName`(legal_entities.legal_entity_id)) AS legal_entity_list
                   FROM notification_template AS nt 
                   LEFT JOIN notification_recipients AS nr ON nr.`notification_template_id` = nt.notification_template_id 
                   LEFT JOIN legal_entities ON FIND_IN_SET(legal_entities.`legal_entity_id`, nr.notificaiton_recipient_legal_entities)
                   LEFT JOIN users ON FIND_IN_SET(users.`user_id`, nr.notificaiton_recipient_users)
                   LEFT JOIN roles ON FIND_IN_SET(roles.`role_id`, nr.notificaiton_recipient_roles)
                   GROUP BY nr.`notification_template_id`
                   ORDER BY nt.notification_template_id DESC ");

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $templateCollection;
    }
    
    public function getTemplatesByRole($currentRoleId) {
        try
        {
            $templateCollection = $this
                    ->join('notification_recipients', 'notification_recipients.notification_template_id', '=', 'notification_template.notification_template_id')
                    ->where('notification_recipients.notificaiton_recipient_roles', $currentRoleId)
                    ->select(DB::raw('group_concat(notification_template.notification_code) as notification_codes'))
                    ->first();
            return $templateCollection->toArray();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getTemplatesByUsers($currentUserId) {
        try
        {
            $templateCollection = $this
                    ->join('notification_recipients', 'notification_recipients.notification_template_id', '=', 'notification_template.notification_template_id')
                    ->where('notification_recipients.notificaiton_recipient_users', $currentUserId)
                    ->select(DB::raw('group_concat(notification_template.notification_code) as notification_codes'))
                    ->first();
            return $templateCollection->toArray();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getTemplatesByLegalEntity($currentLegalEntityId) {
        try
        {
            $templateCollection = $this
                    ->join('notification_recipients', 'notification_recipients.notification_template_id', '=', 'notification_template.notification_template_id')
                    ->where('notification_recipients.notificaiton_recipient_legal_entities', $currentLegalEntityId)
                    ->select(DB::raw('group_concat(notification_template.notification_code) as notification_codes'))
                    ->first();
            return $templateCollection->toArray();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getMessageByCode($messageCode) {
        try
        {
            $templateMessage = $this
                    ->where('notification_code', $messageCode)
                    ->first(['notification_message']);
            if(!empty($templateMessage))
            {
                $templateMessage = $templateMessage->notification_message;
            }
            return $templateMessage;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getUsersByCode($messageCode) {
        try
        {
            $userList = [];
            $notificationUsers = $this
                    ->leftJoin('notification_recipients', 'notification_recipients.notification_template_id', '=', 'notification_template.notification_template_id')
                    ->where('notification_template.notification_code', $messageCode)
                    ->first(['notification_recipients.notificaiton_recipient_roles', 'notification_recipients.notificaiton_recipient_users', 'notification_recipients.notificaiton_recipient_legal_entities']);
            if(!empty($notificationUsers))
            {
                $roles = explode(',', $notificationUsers->notificaiton_recipient_roles);
                $users = explode(',', $notificationUsers->notificaiton_recipient_users);
                $legalEntities = explode(',', $notificationUsers->notificaiton_recipient_legal_entities);
                if (!empty($users)) {
                    $activeUsers = DB::table('users')
                            ->where('users.is_active', 1)
                            ->whereIn('users.user_id', $users)
                            ->pluck('users.user_id')->all();
                    if (count($activeUsers) > 0) {
                        $userList = $activeUsers;
                    }
                }
                if(!empty($roles))
                {
                    foreach($roles as $roleId)
                    {
                        if($roleId > 0)
                        {
                            $rolesList = DB::table('user_roles')
                                ->join('users', 'users.user_id', '=', 'user_roles.user_id')
                                ->where('users.is_active', 1)
                                ->where('user_roles.role_id', $roleId)
                                ->pluck('user_roles.user_id')->all();
                            if(!empty($userList))
                            {
                                $userList = array_merge($userList, $rolesList);
                            }else{
                                $userList = $rolesList;
                            }
                        }                        
                    }
                }
                if(!empty($legalEntities))
                {
                    foreach($legalEntities as $legalEntityId)
                    {
                        if($legalEntityId > 0)
                        {
                            $legalEntityList = DB::table('users')
                                ->where('legal_entity_id', $legalEntityId)
                                ->where('is_active', 1)
                                ->pluck('user_id')->all();
                            if(!empty($userList))
                            {
                                $userList = array_merge($userList, $legalEntityList);
                            }else{
                                $userList = $legalEntityList;
                            }
                        }
                    }
                }
            }else{
                $userList[] = Session::get('userId');
            }
            return array_unique($userList);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getDeleteTemplate($templateId)
    {
        try
        {
            if($templateId > 0)
            {
                DB::table('notification_recipients')
                        ->where('notification_template_id', $templateId)
                        ->delete();
                $this->where('notification_template_id', $templateId)->delete();
                return 1;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return 0;
        }
    }
    
    public function validateNotificationCode($notificationCode, $notificationTemplateId = 0)
    {
        try
        {
            if($notificationCode != '')
            {
                if($notificationTemplateId > 0)
                {
                    $response = $this->where('notification_code', $notificationCode)
                            ->where('notification_template_id', '!=', $notificationTemplateId)
                            ->first();
                }else{
                    $response = $this->where('notification_code', $notificationCode)->first();
                }
                if(!empty($response))
                {
                    return [ "valid" => false];
                }else{
                    return [ "valid" => true];
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function saveTemplate($notificationCode, $notificationMessage, $notifyRm)
    {
        try
        {
            $notificationId = 0;
            if($notificationCode != '')
            {
                $notificationId = $this->insertGetId(['notification_code' => $notificationCode, 
                    'notification_message' => $notificationMessage, 
                    'notify_rm' => $notifyRm, 'created_by' => Session::get('userId')]);
            }
            return $notificationId;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function saveTemplateRecipients($notificationId, $data)
    {
        try
        {
            if($notificationId > 0)
            {
                $inserData['notification_template_id'] = $notificationId;
                if(isset($data['notificaiton_recipient_roles']))
                    $inserData['notificaiton_recipient_roles'] = isset($data['notificaiton_recipient_roles']) ? implode(',', $data['notificaiton_recipient_roles']) : ''; 
                if(isset($data['notificaiton_recipient_users']))
                    $inserData['notificaiton_recipient_users'] = isset($data['notificaiton_recipient_users']) ? implode(',', $data['notificaiton_recipient_users']) : ''; 
                if(isset($data['notificaiton_recipient_legal_entities']))
                    $inserData['notificaiton_recipient_legal_entities'] = isset($data['notificaiton_recipient_legal_entities']) ? implode(',', $data['notificaiton_recipient_legal_entities']) : ''; 
                DB::table('notification_recipients')->insert($inserData);
            }
            return;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function updateNotifyTemplate($templateId, $status)
    {
        try
        {
            if($templateId > 0)
            {
                // Log::info($status);
                if($status == 'true')
                {
                    $status = 1;
                }else{
                    $status = 0;
                }
                // Log::info($status);
                $notificationId = $this->where('notification_template_id', $templateId)
                        ->update(['notify_rm' => $status, 'updated_by' => Session::get('userId')]);
            }
            return $notificationId;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function updateTemplate($notificationId, $notificationCode, $notificationMessage)
    {
        try
        {
            if($notificationId > 0 && $notificationCode != '')
            {
                $notificationId = $this->where('notification_template_id', $notificationId)
                        ->update(['notification_code' => $notificationCode, 
                    'notification_message' => $notificationMessage, 
                    'updated_by' => Session::get('userId')]);
            }
            return $notificationId;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function updateTemplateRecipients($notificationId, $data)
    {
        try
        {
            if($notificationId > 0)
            {
                $inserData['notificaiton_recipient_roles'] = isset($data['notificaiton_recipient_roles']) ? implode(',', $data['notificaiton_recipient_roles']) : ''; 
                $inserData['notificaiton_recipient_users'] = isset($data['notificaiton_recipient_users']) ? implode(',', $data['notificaiton_recipient_users']) : ''; 
                $inserData['notificaiton_recipient_legal_entities'] = isset($data['notificaiton_recipient_legal_entities']) ? implode(',', $data['notificaiton_recipient_legal_entities']) : ''; 
//                if(isset($data['notificaiton_recipient_roles']))
//                    $inserData['notificaiton_recipient_roles'] = isset($data['notificaiton_recipient_roles']) ? implode(',', $data['notificaiton_recipient_roles']) : ''; 
//                if(isset($data['notificaiton_recipient_users']))
//                    $inserData['notificaiton_recipient_users'] = isset($data['notificaiton_recipient_users']) ? implode(',', $data['notificaiton_recipient_users']) : ''; 
//                if(isset($data['notificaiton_recipient_legal_entities']))
//                    $inserData['notificaiton_recipient_legal_entities'] = isset($data['notificaiton_recipient_legal_entities']) ? implode(',', $data['notificaiton_recipient_legal_entities']) : ''; 
                
                $recipientId = DB::table('notification_recipients')
                        ->where('notification_template_id', $notificationId)
                        ->first(['notification_recipient_id']);
                DB::enableQueryLog();
                if(!empty($recipientId))
                {
                    DB::table('notification_recipients')
                            ->where('notification_template_id', $notificationId)
                            ->update($inserData);
                }else{
                    $inserData['notification_template_id'] = $notificationId;
                    DB::table('notification_recipients')->insert($inserData);
                }               
                
                
                // Log::info(DB::getQueryLog());
            }
            return;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getMyRolesList($roleId)
    {
        try
        {
            $roleList = [];
            if($roleId > 0)
            {
                $roleList = DB::table('roles')
//                        ->where(['parent_role_id' => $roleId])
                        ->select('role_id', 'name')
                        ->orderBy('roles.role_id')
                        ->get()->all();
//                        ->lists('role_id');
//                if(!empty($roleList))
//                {
//                    foreach($roleList as $role)
//                    {
//                        $this->_rolesList[] = $role;
//                        $this->getMyRolesList($role);
//                    }
//                }
                $this->_rolesList = $roleList;
            }
            return $this->_rolesList;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getRoleNames($rolesList)
    {
        try
        {
            $response = [];
            if(!empty($rolesList))
            {
                foreach($rolesList as $roleId)
                {
                    $temp = DB::table('roles')
                            ->where('role_id', $roleId)
                            ->select('role_id', 'name')
                            ->first();
                    $response[] = $temp;
                }
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getUsersNames()
    {
        try
        {
            $response = [];
            $legalEntityId = Session::get('legal_entity_id');
            if($legalEntityId > 0)
            {
                $response = DB::table('users')
                    ->where('legal_entity_id', $legalEntityId)
                    ->select('user_id', DB::raw('concat(firstname, " ", lastname) as name'))
                    ->get()->all();
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getLegalEntitiesNames()
    {
        try
        {
            $response = [];
            $legalEntityId = Session::get('legal_entity_id');
            if($legalEntityId > 0)
            {
                $response = DB::table('legal_entities')
                        ->where('legal_entity_id', $legalEntityId)
                        ->select('legal_entity_id', DB::raw("'My Legal Entity' as name" ))
                        ->get()->all();
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getTemplateData($templateId)
    {
        try
        {
            $response = [];
            if($templateId > 0)
            {
                $response = DB::table('notification_template')
                        ->leftJoin('notification_recipients', 'notification_recipients.notification_template_id', '=', 'notification_template.notification_template_id')
                        ->where('notification_template.notification_template_id', $templateId)
                        ->first();
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function checkIfReportingEnabled($messageCode)
    {
        try
        {
            $response = 0;
            if($messageCode != '')
            {
                $response = DB::table('notification_template')
                        ->where('notification_code', $messageCode)
                        ->pluck('notify_rm')->all();
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getReportingManagerByUserId($userId)
    {
        try
        {
            $response = 0;
            if($userId > 0)
            {
                $response = DB::table('users')
                    ->where(['user_id' => $userId, 'is_active' => 1, 'is_disabled' => 0])
                    ->pluck('reporting_manager_id')->all();
                // Log::info('reporting_manager_id');
                // Log::info($response);
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
}