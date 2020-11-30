<?php
use App\Modules\Notifications\Models\NotificationsModel;
use Carbon\Carbon;
class Notifications
{
    public static function addNotification($notificationData)
    {
        try
        {
            $status = false;
            $finalMessage = '';
            $message = '';
            $notification = new NotificationsModel();
            $messageCode = isset($notificationData['note_code']) ? $notificationData['note_code'] : '';
            $notificationMessage = isset($notificationData['note_message']) ? $notificationData['note_message'] : '';
            $params = isset($notificationData['note_params']) ? $notificationData['note_params'] : '';
            $link = isset($notificationData['note_link']) ? $notificationData['note_link'] : '';
            $priority = isset($notificationData['note_priority']) ? $notificationData['note_priority'] : 1;
            $notificationType = isset($notificationData['note_type']) ? $notificationData['note_type'] : 1;
            $notificationUserId = isset($notificationData['note_users']) ? $notificationData['note_users'] : $notification->getUsersByCode($messageCode);
            if(!extension_loaded("mongo"))
            {
                $message = "Mongo extension not enabled.";
//                Log::info("Mongo extension not enabled.");
                return json_encode(['status' => $status, 'message' => $message]);
            }
            if($messageCode != '')
            {                
                $status = true;
                if(!empty($params) && $notificationMessage != '')
                {
                    $temp = $notificationMessage;
                    foreach($params as $key => $value)
                    {
                        $temp = str_replace($key, $value, $temp);
                    }
                    $finalMessage = $temp;
                }else if(!empty($params)){
                    $templateMessage = $notification->getMessageByCode($messageCode);
                    $temp = $templateMessage;
                    foreach($params as $key => $value)
                    {
                        $temp = str_replace($key, $value, $temp);
                    }
					$finalMessage = $temp;
                }else if($notificationMessage != '' && empty($params))
                {
                    $finalMessage = $notificationMessage;
                }else if($notificationMessage == '' && empty($params))
                {
                    $finalMessage = $notification->getMessageByCode($messageCode);
                }
//                if(!$priority)
//                {
//                    $priority = 1;
//                }
                if($finalMessage == '')
                {
                    $message = 'Please provide notification message';
                }else{
                    if(!empty($notificationUserId))
                    {
                        foreach($notificationUserId as $userId)
                        {
                            $notificaionData[] = ['message_code' => $messageCode, 
                                'message' => $finalMessage, 
                                'link' => $link,
                                'priority' => (int)$priority,
                                'notification_type' => (int)$notificationType,
                                'users' => (int)$userId,
                                'status' => (int)0,
                                'created_at' => new \MongoDate()
                                ];
                        }
                        $response = $notification->addNotification($notificaionData);
                    }else{
                        $status = false;
                        $message = 'No users are assigned for this notification code';
                    }
                    if($response != '')
                    {
                        $tempData = json_decode($response);
                        if(!empty($tempData))
                        {
                            $status = $tempData->status;
                            $message = $tempData->message;
                        }
                    }
                }
            }else{
                $message = 'Please provide data.';
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return json_encode(['status' => $status, 'message' => $message]);
    }
}