<?php
namespace App\Modules\Notifications\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\Modules\Notifications\Models\NotificationsMysqlModel;
use \Log;
use \Session;
use Carbon\Carbon;

class NotificationsModel extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'notification_data';
    protected $primaryKey = '_id';
    protected $dates = ['created_at'];
    
    public function addNotification($data)
    {
        try
        {            
            $status = false;
            $response = '';
            $message = '';
            if(!empty($data))
            {
//                foreach($data as $key => $value){
//                    $this->$key = $value;
//                }
                if($this->insert($data))
                {
                    $status = true;
                    $message = "Data saved";
                }else{
                    $message = "Unable to save data";
                }
            }else{
                $message = 'Please provide data.';
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $message = $ex->getMessage();
        }
        return json_encode(['status' => $status, 'message' => $message]);
    }
        
    public function getNotifications($typeId)
    {
        try
        {
            $status = false;
            $message = '';
            $data = [];
            $count = 0;
            $notifications = [];
            $currentUserId = Session::get('userId');
            $fromDate = date('Y-m-d 00:00:00', strtotime('-7 days'));
//            echo "<pre>";print_R($fromDate);die;
            $dt = Carbon::now()->startOfYear();
//            echo "<pre>";print_R($dt);die;
            \DB::connection($this->connection)->enableQueryLog();
            $notifications = $this
                ->where(['status' => (int)0, 'users' => (int)$currentUserId, 'notification_type' => (int)$typeId])
//                ->where('created_at', 'like', $dt)
                ->select('message_code', 'message', 'created_at', 'notification_type', 'link')
                ->orderBy('created_at', 'DESC')
                ->skip(0)->take(20)->get()->all();
//            echo "<pre>";print_R(\DB::connection($this->connection)->getQueryLog());
            // Log::info(\DB::connection($this->connection)->getQueryLog());
            $notificationCount = $this
                ->where(['status' => (int)0, 'users' => (int)$currentUserId, 'notification_type' => (int)$typeId])
//                ->where('created_at', 'like', $dt)
                ->select('message_code', 'message', 'created_at', 'notification_type', 'link')
                ->orderBy('created_at', 'DESC')
                ->count();            
//            echo "<pre>";print_R($notifications);die;
            if(!empty($notifications))
            {                
                $status = true;
                $message = 'Recieved data';
                $data = $notifications->toArray();
                $i = 0;
                foreach($data as $notificationData)
                {                    
                    $createdAt = isset($notificationData['created_at']) ? $notificationData['created_at'] : '';
                    if($createdAt != '')
                    {
                        $start_date = new \DateTime($createdAt);
                        $since_start = $start_date->diff(new \DateTime(date('Y-m-d H:i:s')));
                        $difference = '';
                        if($since_start->d > 0)
                        {
                            $difference = $since_start->d.' Days ';
                        }
                        if($since_start->h > 0)
                        {
                            $difference = $difference . $since_start->h.'H ';
                        }
                        if($since_start->i > 0)
                        {
                            $difference = $difference . $since_start->i.'M ';
                        }
//                        if($since_start->s > 0)
//                        {
//                            $difference = $difference . $since_start->s.'S ';
//                        }
                        if($since_start->d == 0 && $since_start->h == 0 && $since_start->i == 0 && $since_start->s < 10)
                        {
                            $difference = 'Just Now';
                        }
                        $data[$i]['time_delay'] = $difference;
                        $i++;
                    }
                }
//                $count = count($data);
                $count = $notificationCount;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $message = $ex->getMessage();
        }
        return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'count' => $count]);
    }
    
    public function updateStatus($data) {
        try
        {
            $status = false;
            if(!empty($data))
            {
                $id = isset($data['_id']) ? $data['_id'] : 0;
                $userId = Session::get('userId');
//                DB::connection('mysql2')->select(...);
                if($id != 'ALL' && $id != 0)
                {
//                    \DB::connection($this->connection)->table('notification_status')                    
                    $temp = $this->find($id);
//                    \DB::connection($this->connection)->enableQueryLog();
                    $temp->delete();
//                    echo "<pre>";print_R(\DB::connection($this->connection)->getQueryLog());die;
                }elseif($id == 'ALL'){
                    $temp = $this->where('users', (int)3)->get()->all();
                    foreach($temp as $test)
                    {
                        $test->delete();
                    }
//                    \DB::connection($this->connection)->enableQueryLog();
//                    $temp->delete();
//                    echo "<pre>";print_R(\DB::connection($this->connection)->getQueryLog());die;
                }
            }
            $status = true;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(['status' => $status]);
    }
    
    public function getMessageByCode($messageCode) {
        try
        {
            $status = false;
            if(!empty($messageCode))
            {
                $notificationsMysqlModel = new NotificationsMysqlModel();
                return $notificationsMysqlModel->getMessageByCode($messageCode);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(['status' => $status]);
    }
    
    public function getUsersByCode($messageCode) {
        try
        {
            $status = false;
            if(!empty($messageCode))
            {
                $notificationsMysqlModel = new NotificationsMysqlModel();
                return $notificationsMysqlModel->getUsersByCode($messageCode);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(['status' => $status]);
    }
    
    public function checkReportingManager($messageCode)
    {
        try
        {
            $status = false;
            if(!empty($messageCode))
            {
                $notificationsMysqlModel = new NotificationsMysqlModel();
                $response = $notificationsMysqlModel->checkIfReportingEnabled($messageCode);
                if(!empty($response))
                {
                    return isset($response[0]) ? $response[0] : 0;
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(['status' => $status]);
    }
    
    public function getReportingManager($userId)
    {
        try
        {
            $status = false;
            if(!empty($userId))
            {
                $notificationsMysqlModel = new NotificationsMysqlModel();
                $response = $notificationsMysqlModel->getReportingManagerByUserId($userId);
                if(!empty($response))
                {
                    return isset($response[0]) ? $response[0] : 0;
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(['status' => $status]);
    }
    
    public function getAllNotifications($request)
    {
        try
        {
            $typeId = 1;
            $status = false;
            $message = '';
            $data = [];
            $count = 0;
            $notifications = [];
            $currentUserId = Session::get('userId');
            $dt = Carbon::now()->startOfYear();
            $notificationCount = 0;
            \DB::connection($this->connection)->enableQueryLog();
            $notificationCollection = $this->getNotificationList($request, $currentUserId);
            if(!empty($notificationCollection))
            {
                $notificationDetails = isset($notificationCollection['Records']) ? $notificationCollection['Records'] : [];
                $notificationCount = isset($notificationCollection['totalUserCount']) ? $notificationCollection['totalUserCount'] : 0;
            }
            if(!empty($notificationDetails))
            {                
                $status = true;
                $message = 'Recieved data';
                $i = 0;
                foreach($notificationDetails as $notificationData)
                {
                    if(isset($notificationData->attributes['created_at']) && is_array($notificationData->attributes['created_at']))
                    {
                        $createdDate = $notificationData->attributes['created_at'];
                        unset($notificationData->attributes['created_at']);
                        if(isset($createdDate['sec']))
                        {
                            $createdAt = date('Y-m-d H:i:s', $createdDate['sec']);
                        }else{
                            $createdAt = date('Y-m-d H:i:s');
                        }
                        $notificationData = $notificationData->toArray();
                    }else{
                        $notificationData = $notificationData->toArray();
                        $createdAt = isset($notificationData['created_at']) ? $notificationData['created_at'] : '';
                    }
                    $notifications[] = $notificationData;
                }
//                echo "<pre>";print_R($notifications);die;
//                $count = count($data);
                $count = $notificationCount;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $message = $ex->getMessage();
        }
        return json_encode(['status' => $status, 'message' => $message, 'data' => $notifications, 'count' => $count]);
    }
    
    public function getNotificationList($request, $currentUserId) {
        $grid_fields = [
                'message_code' => 'message_code',
                'message' => 'message',
                'link' => 'link',
                'created_at' => 'created_at'
            ];
        $results = $this->where(['status' => (int)0, 'users' => (int)$currentUserId, 'notification_type' => 1])
                ->select('message_code', 'message', 'created_at', 'notification_type', 'link');
//                ->orderBy('created_at', 'DESC')
//                ->skip(0)->take(20)->get();
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax
        $filter_by = $this->filterData($request, $grid_fields);
//         Log::info('filter_by');
//         Log::info($filter_by);
//         echo "<prE>";print_R($filter_by);
//         Log::info($filter_by);
        $order_by = '';
        $orderby_array = [];
        $totalCount1 = 0;
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
                } elseif($filter_query_operator == 'like' && $filter_query_field == 'created_at') {
                    $results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                }else 
                {
                    $results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                }
            }
        }
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

            if (isset($grid_fields[$order_query_field]))
            { //getting appropriate table field based on grid field
                $order_by = $grid_fields[$order_query_field];
            }
            $orderby_array = $order_by . " " . $order_by_type;
        }
        // Log::info('orderby_array');
        // Log::info($orderby_array);
//        echo "<pre>";print_R($orderby_array);die;
        if (!empty($orderby_array)) {
            $orderClause = explode(" ", $orderby_array);
            $results = $results->orderby($orderClause[0], $orderClause[1]);  //order by query 
        }else{
            $results = $results->orderBy('created_at', 'DESC');
        }
        if($page == '' || $page < 0)
        {
            $page = 0;
        }
        if($pageSize == '' || $pageSize < 0)
        {
            $pageSize = 10;
        }
        $countResult = clone $results;
        \DB::connection($this->connection)->enableQueryLog();                    
        $results = $results->skip($page * $pageSize)->take($pageSize)->get()->all();
        // Log::info(\DB::connection($this->connection)->getQueryLog());
        $totalCount1 = $countResult->get()->all();
        $totalCount = count($totalCount1);
        return ['Records' => $results, 'totalUserCount' => $totalCount];
    }
    
    public function filterData($request, $grid_fields)
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
            // \Log::info($request_input);
            
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

                if (isset($grid_fields[$order_query_field]))
                { //getting appropriate table field based on grid field
                    $order_by = $grid_fields[$order_query_field];
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
//                            $filter_by[] =  $filter_query_field . ' like ' . $filter_value;
                            foreach ($grid_fields as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $starts_with_value = $grid_fields[$key] . ' like ' . $filter_value;
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
//                            $filter_by[] =  $filter_query_field . ' like ' . $filter_value;
                            foreach ($grid_fields as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $ends_with_value = $grid_fields[$key] . ' like ' . $filter_value;
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
//                            $filter_by[] = $filter_query_field . $like . $filter_value;
                            foreach ($grid_fields as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $to_lower_value = $grid_fields[$key] . $like . $filter_value;
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
//                            $filter_by[] = $filter_query_field . $like . $filter_value;
                            foreach ($grid_fields as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $indexof_value = $grid_fields[$key] . $like . $filter_value;
                                    $filter_by[] = $indexof_value;
                                } else {
                                    $indexof_value = "";
                                }
                            }
                        }
                    } else {
                        if($filter_query_field == 'created_at')
                        {
                            $filter_operator = ' like ';
//                            $filter_query_value = $filter_query_value.'';
                        }else{
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
                        }

                        if (isset($grid_fields[$filter_query_field])) {
                            //getting appropriate table field based on grid field
                            $filter_field = $grid_fields[$filter_query_field];
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
           // Log::info(DB::getQueryLog());
            return $filter_by;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
} 