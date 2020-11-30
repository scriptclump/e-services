<?php
namespace App\Modules\Notifications\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\Notifications\Models\NotificationsMysqlModel;
use \App\Central\Repositories\RoleRepo;
use Illuminate\Http\Request;
use \Log;
use \Input;
use \Session;

class NotificationsController extends BaseController
{
    public function __construct(RoleRepo $roleAccess) {
        parent::__construct();
        $this->roleAccess = $roleAccess;
    }
    
    public function indexAction()
    {
        try
        {
            parent::Title(trans('dashboard.dashboard_title.company_name').' - Notification Templates');
            parent::Breadcrumbs(array('Home' => '/', 'Administration' => '#', 'Notifications' => '#'));
            $notificationModel = new NotificationsMysqlModel();
            $roles = [];
            if(Session::has('roles'))
            {
                $roles = explode(',', Session::get('roles'));
            }
            $rolesList = [];
            if(!empty($roles))
            {
                foreach($roles as $roleId)
                {
                    $temp = $notificationModel->getMyRolesList($roleId);
                    if(!empty($temp)){
                        $rolesList = array_merge($temp, $rolesList);
                    }
                }
            }
            $usersDetails = $notificationModel->getUsersNames();
            $legalEntitiesDetails = $notificationModel->getLegalEntitiesNames();
            $addPermission = $this->roleAccess->checkPermissionByFeatureCode('NOT001');
            return view('Notifications::index')
                    ->with([
                        'roles' => $rolesList, 
                        'users' => $usersDetails, 
                        'legal_entities' => $legalEntitiesDetails,
                        'add_permission' => $addPermission,
                    ]);
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage());
            \Log::info($ex->getTraceAsString());
        }
    }
    
    public function getTemplates()
    {
        try
        {
            $notificationModel = new NotificationsMysqlModel();
            $notificationTemplateCollection = $notificationModel->getAllNotificationTemplates();            
            if(!empty($notificationTemplateCollection))
            {
                $editPermission = $this->roleAccess->checkPermissionByFeatureCode('NOT002');
                $deletePermission = $this->roleAccess->checkPermissionByFeatureCode('NOT003');
                $i = 0;
                foreach($notificationTemplateCollection as $templateData)
                {
                    $actions = '';
                    if($editPermission)
                    {
                        $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0)" onclick="editEntityType(' . $templateData->notification_template_id . ')"><i class="fa fa-pencil"></i></span>';
                    }
                    if($deletePermission)
                    {
                        $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0)" onclick="deleteEntityType(' . $templateData->notification_template_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                    }
                    if($templateData->notify_rm)
                    {
                        $notificationTemplateCollection[$i]->notify_rm = '<label class="switch">'
                                . '<input class="switch-input notify_rm ' . $templateData->notification_template_id . '" type="checkbox" checked="true" onclick="notifyRm(' . $templateData->notification_template_id . ')" name="' . $templateData->notification_code . '" value="' . $templateData->notification_template_id . '" />'
                                . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                . '<span class="switch-handle"></span></label>';
                    }else{
                        $notificationTemplateCollection[$i]->notify_rm = '<label class="switch">'
                                . '<input class="switch-input notify_rm ' . $templateData->notification_template_id . '" type="checkbox" check="false" onclick="notifyRm(' . $templateData->notification_template_id . ')" name="' . $templateData->notification_code . '" value="' . $templateData->notification_template_id . '" />'
                                . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                . '<span class="switch-handle"></span></label>';
                    }
                    $notificationTemplateCollection[$i]->actions = $actions;
                    $i++;
                }
            }
            return json_encode(['Records' => $notificationTemplateCollection]);
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage());
            \Log::info($ex->getTraceAsString());
        }
    }
    
    public function addNotification()
    {
        try
        {
            echo \Notifications::addNotification(['note_code' => 'ORD005', 'note_priority' => 0, 'note_type' => 1, 'note_params' => ['ORDID' => 554454], 'note_link' => 'http://google.com']);
            die('we are hererere');
            $data = Input::all();            
            $status = false;
            $message = '';
            if(extension_loaded("mongo"))
            {
                if(!empty($data))
                {
                    $notifications = new NotificationsModel();                    
                    $message = $notifications->addNotification($data);
                    $status = true;
                }else{
                    $message = 'Please provide data.';
                }
            }else{
                $message = 'Mongo not loaded.';
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(['status' => $status, 'message' => $message]);
    }
    
    public function getMyNotifications($type_id) {
        try
        {
            if(extension_loaded("mongo"))
            {
                $notifications = new NotificationsModel();
                return $notifications->getNotifications($type_id);
            }
            return json_encode(['status' => false, 'message' => '', 'data' => '', 'count' => 0]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function changeStatus()
    {
        try
        {
            $data = Input::all();
            $notifications = new NotificationsModel();
            return $notifications->updateStatus($data);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getTasks()
    {
        try
        {
            $data = Input::all();
            $notifications = new NotificationsModel();
            $tasks = $notifications->getNotifications(2);
            return view('Notifications::tasks')->with(['notifications_tasks' => $tasks]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function addTemplate()
    {
        try
        {
            $result['message'] = 'Unable to save data';
            $result['status']=false;
            $data = Input::all();
//            echo "<prE>";print_R($data);die;
            $notificationCode = isset($data['notification_code']) ? $data['notification_code'] : '';
            $notificationMessage = isset($data['notification_message']) ? $data['notification_message'] : '';
            $notifyRm = isset($data['notify_rm']) ? $data['notify_rm'] : 0;
            if($notificationCode != '')
            {
                $notificationModel = new NotificationsMysqlModel();
                $response = $notificationModel->validateNotificationCode($notificationCode);
                if(!empty($response) && isset($reponse['valid']) && $reponse['valid'])
                {
                    $result['message'] = 'Duplicate Code cannot be created.';
                }else
                {
                    $notificationId = $notificationModel->saveTemplate($notificationCode, $notificationMessage, $notifyRm);
                    if($notificationId > 0)
                    {
                        $notificationModel->saveTemplateRecipients($notificationId, $data);
                        $result['status']=true;
                        $result['message'] = "Template created successfully.";
                    }
                }
            }
            return $result;
//            return json_encode(['message' => $message]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function updateTemplate()
    {
        try{
           $data = Input::all();
           $result['status']=false;
           $result['message']='Unable to update data';
           $notificationId = isset($data['notification_template_id']) ? $data['notification_template_id'] : '';
            $notificationCode = isset($data['notification_code']) ? $data['notification_code'] : '';
            $notificationMessage = isset($data['notification_message']) ? $data['notification_message'] : '';
    //            $notifyRm = isset($data['notify_rm']) ? $data['notify_rm'] : 0;
            if($notificationId > 0 && $notificationCode != '')
            {
                $notificationModel = new NotificationsMysqlModel();
                $response = $notificationModel->validateNotificationCode($notificationCode, $notificationId);
                if(!empty($response) && isset($reponse['valid']) && $reponse['valid'])
                {
                    $result['message'] = 'Duplicate Code cannot be created.';
                }else{
                    $notificationModel->updateTemplate($notificationId, $notificationCode, $notificationMessage);
                    if($notificationId > 0)
                    {
                        $notificationModel->updateTemplateRecipients($notificationId, $data);
                        $result['status']=true;
                        $result['message'] = "Template updated successfully.";
                    }
                }
            }
            return $result;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }    
    }
    
    public function validateCode()
    {
        try
        {
            $data = Input::all();
            $response = ["valid" => false];
            $notificationTemplateId = isset($data['notification_template_id']) ? $data['notification_template_id'] : 0;
            $notificationCode = isset($data['notification_code']) ? $data['notification_code'] : '';
            if($notificationCode != '')
            {
                $notificationModel = new NotificationsMysqlModel();
                $response = $notificationModel->validateNotificationCode($notificationCode, $notificationTemplateId);
            }
            return json_encode($response);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function deleteTemplate($id)
    {
        try
        {
            $templateId = isset($id) ? $id : 0;
            if($templateId > 0)
            {
                $notificationModel = new NotificationsMysqlModel();
                $status = $notificationModel->getDeleteTemplate($templateId);
                return $status;
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return 0;
        }
    }
    
    public function notifyRm()
    {
        try
        {
            $data = Input::all();
            $templateId = isset($data['template_id']) ? $data['template_id'] : 0;
            $status = isset($data['status']) ? $data['status'] : 0;
            if($templateId > 0)
            {
                $notificationModel = new NotificationsMysqlModel();
                // \DB::enableQueryLog();
                $notificationModel->updateNotifyTemplate($templateId, $status);
                // Log::info(\DB::getQueryLog());
            }
            return;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function editTemplate()
    {
        try
        {
            $data = Input::all();
            $templateDetails = [];
            $templateId = isset($data['template_id']) ? $data['template_id'] : 0;
            if($templateId > 0)
            {
                $notificationModel = new NotificationsMysqlModel();
                $templateDetails = $notificationModel->getTemplateData($templateId);
            }
            return json_encode($templateDetails);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function viewAll()
    {
        try
        {
            parent::Title(trans('dashboard.dashboard_title.company_name').' - View All Notifications');
            parent::Breadcrumbs(array('Home' => '/', 'Administration' => '#', 'View All Notifications' => '/notification/viewall'));
            return view('Notifications::viewall');
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function allNotifications(Request $request)
    {
        try
        {
            $notifications = new NotificationsModel();
            $notificaitonList = $notifications->getAllNotifications($request);
            $notificaitonCount = 0;
            $notificaitonData = [];
            if(!empty($notificaitonList))
            {
                $notificationCollection = json_decode($notificaitonList);
                if(!empty($notificationCollection))
                {
                    $notificaitonCount = $notificationCollection->count;
                    $notificaitonData = $notificationCollection->data;
                }
            }
            return json_encode(['Records' => $notificaitonData, 'totalCount' => $notificaitonCount]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}