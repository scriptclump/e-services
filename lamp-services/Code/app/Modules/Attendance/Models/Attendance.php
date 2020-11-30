<?php

namespace App\Modules\Attendance\Models;

use App\Modules\Attendance\Models\AttendanceMongoModel;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DateTime;
class Attendance extends Model {

    public function __construct() {
        try {
            $this->grid_field_db_match_reports = array(
                'user_name' => 'user_name',
                'role_id' => 'role_id',
                'first_checkin_time' => 'first_checkin_time',
                'last_checkout_time' => 'last_checkout_time'
            );
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getAttendReports($start_date, $end_date, $request,$user_role) {
        $end_date = date('Y-m-d', strtotime($end_date)).' 23:59:59';
            $this->grid_field_db_match_reports = array(
                'user_name' => 'user_name',
                'role_id' => 'role_id',
                'first_checkin_time' => 'first_checkin_time',
                'last_checkout_time' => 'last_checkout_time'
        );
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize');
        if (empty($page) && empty($pageSize)) {
            $page = 1;
            $pageSize = 1;
        }

        if ($start_date == '1970-01-01') {
            $start_date = '';
        }
        if ($end_date == '1970-01-01') {
            $end_date = '';
        }

		$view = $this->getRoleBasedView($user_role);
        $query = DB::table($view)->select('first_checkin_time','last_checkout_time','role_id','user_name');
		
        if (!empty($start_date) && !empty($end_date)) {
            $reports = $query->where('first_checkin_time', '>=', $start_date)
                             ->where('first_checkin_time', '<=', $end_date);
        } else {
            return json_decode(json_encode(array('Records'=>array())), true);
        }
       
        if ($user_role)
        {   
            $query = $query->where('role_id', $user_role);
        }
		$query->orderBy('first_checkin_time', 'desc');
        if ($request->input('$orderby')) {    //checking for sorting
            $order = explode(' ', $request->input('$orderby'));
            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->grid_field_db_match_reports[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match_reports[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }
        if ($request->input('$filter')) {           //checking for filtering                
            $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'
            foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter                    
                $filter = explode(' ', $post_filter_query_sub);
                $length = count($filter);
                $filter_query_field = '';
                if ($length > 3) {
                    for ($i = 0; $i < $length - 2; $i++)
                        $filter_query_field .= $filter[$i] . " ";
                    $filter_query_field = trim($filter_query_field);
                    $filter_query_operator = $filter[$length - 2];
                    $filter_query_value = $filter[$length - 1];
                } else {
                    $filter_query_field = $filter[0];
                    $filter_query_operator = $filter[1];
                    $filter_query_value = $filter[2];
                }
                $filter_query_substr = substr($filter_query_field, 0, 7);
                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    if ($filter_query_substr == 'startsw') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = $filter_value_array[1] . '%';
                        foreach ($this->grid_field_db_match_reports as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match_reports[$key], 'like', $filter_value);
                            }
                        }
                    }
                    if ($filter_query_substr == 'endswit') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1];
                        foreach ($this->grid_field_db_match_reports as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match_reports[$key], 'like', $filter_value);
                            }
                        }
                    }
                    if ($filter_query_substr == 'tolower') {
                        $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = $filter_value_array[1];
                        if ($filter_query_operator == 'eq') {
                            $like = '=';
                        } else {
                            $like = '!=';
                        }
                        foreach ($this->grid_field_db_match_reports as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match_reports[$key], $like, $filter_value);
                            }
                        }
                    }
                    if ($filter_query_substr == 'indexof') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1] . '%';
                        if ($filter_query_operator == 'ge') {
                            $like = 'like';
                        } else {
                            $like = 'not like';
                        }
                        foreach ($this->grid_field_db_match_reports as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match_reports[$key], $like, $filter_value);
                            }
                        }
                    }
                } else {
                    switch ($filter_query_operator) {
                        case 'eq' :
                            $filter_operator = '=';
                            break;
                        case 'ne':
                            $filter_operator = '!=';
                            break;
                        case 'gt' :
                            $filter_operator = '>';
                            break;
                        case 'lt' :
                            $filter_operator = '<';
                            break;
                        case 'ge' :
                            $filter_operator = '>=';
                            break;
                        case 'le' :
                            $filter_operator = '<=';
                            break;
                    }
                    if (isset($this->grid_field_db_match_reports[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match_reports[$filter_query_field];
                    }
                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }

        $count = $query->count();
        $result = array();
        $result['count'] = $count;
        $reports = $query->skip($page * $pageSize)->take($pageSize)->get()->all();

        if (!empty($reports)) {
            $report_result = json_encode(array('Records' => $reports, 'TotalRecordsCount' => $result['count']));
        } else {
            $report_result = json_encode(array('Records' => $reports, 'TotalRecordsCount' => $result['count']));
        }
        return $report_result;
    }

    public function excelAttendanceReports($start_date, $end_date, $user_role) {
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date)).' 23:59:59';
        if ($start_date == '1970-01-01') {
            $start_date = '';
        }
        if ($end_date == '1970-01-01') {
            $end_date = '';
        }
        
        $view = $this->getRoleBasedView($user_role);
        $query = DB::table($view)->select('first_checkin_time','last_checkout_time','role_id','user_name');
        
        if (!empty($start_date) && !empty($end_date)) {
            $reports = $query->where('first_checkin_time', '>=', $start_date)
                             ->where('first_checkin_time', '<=', $end_date);
        } else {
            $reports = $query;
        }
        if ($user_role)
        {   
            $query = $query->where('role_id', $user_role);
        }
        $data = json_decode(json_encode($reports->get()->all()), true);
        return $data;
    }
	
    public function getRoleBasedView($user_role)
    {
        $view = '';
        switch($user_role)
        {
            case 'Field Force Associate': $view = 'vw_ff_att';break;
            case 'Picker': $view = 'vw_picker_att';break;
            case 'Delivery Executive': $view = 'vw_delivery_att';break;                
        }
        return $view;        
    }

}
