<?php
namespace App\Modules\FFMSchedules\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;
use Session;
use App\Modules\Roles\Models\Role;

class FFMScheduleModel extends Model{

    public function __construct(){
        $this->roleObj = new Role();
        $this->schedule_grid_fields = array(
            'fps_id' => 'fps_id',
            'state' => 'state',
            'mobile_no' => 'mobile_no',
            'ff_name' => 'ff_name',
            'city' => 'city',
            'pincode' => 'pincode',
            'le_wh_id' => DB::raw('getLeWhName(le_wh_id)'),
            'date' => 'date',
            );
    }
    public function getSchedules($page,$pageSize,$orderByData,$filterData){
        $loginwh = $this->getaccessWarehouse(Session::get('userId'),6);
        $getffmsforemptydc=DB::table('ffm_pjp_schedules')->selectRaw('GROUP_CONCAT(ff_id) as ffms')->whereRaw('le_wh_id is NULL')->first();
        $ffmslistwithoutwarehouse=array();
        if(!empty($getffmsforemptydc->ffms)){
            $getffmslist=explode(',', $getffmsforemptydc->ffms);
            foreach ($getffmslist as $key => $value) {
                $ffmswh = $this->getaccessWarehouse($value,6);
                if(array_intersect($ffmswh, $loginwh)){
                    array_push($ffmslistwithoutwarehouse, $value);
                }
            }
        }
        $loginwh=implode(',', $loginwh);
        $ffmslistwithoutwarehouse=implode(',', $ffmslistwithoutwarehouse);
        if(empty($ffmslistwithoutwarehouse))
            $ffmslistwithoutwarehouse = 0;
        $query = DB::table('ffm_pjp_schedules')
                ->select('fps_id','mobile_no','ff_id','ff_name','city','state','pincode',DB::raw('getLeWhName(le_wh_id)  AS le_wh_id'),
                    'date','created_by','created_at','updated_by','updated_at')
                ->whereRaw('(le_wh_id in ('.$loginwh.') or ff_id in ('.$ffmslistwithoutwarehouse.'))');
        
        // Sorting
        if ($orderByData) {
            $order = explode(' ', $orderByData);
            $order_query_field = $order[0];
            $order_query_type = $order[1]; 
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->schedule_grid_fields[$order_query_field])) {
                $order_by = $this->schedule_grid_fields[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }

        // Filtering
        if ($filterData) {
            $post_filter_query = explode(' and ', $filterData); //multiple filtering seperated by 'and'
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
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the i
                        $filter_value = $filter_value_array[1] . '%';
                        foreach ($this->schedule_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->schedule_grid_fields[$key], 'like', $filter_value);
                            }
                        }
                    }
                    if ($filter_query_substr == 'endswit') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1];
                        foreach ($this->schedule_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->schedule_grid_fields[$key], 'like', $filter_value);
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
                        foreach ($this->schedule_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {
                                $query->where($this->schedule_grid_fields[$key], $like, $filter_value);
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
                        foreach ($this->schedule_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->schedule_grid_fields[$key], $like, $filter_value);
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
                    if (strpos($filter_query_field, 'day(') !== false){
                        $start = strpos($filter_query_field, '(');
                        $end = strpos($filter_query_field, ')');
                        $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                        $date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                        continue;
                    } elseif (strpos($filter_query_field, 'month(') !== false){
                        $start = strpos($filter_query_field, '(');
                        $end = strpos($filter_query_field, ')');
                        $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                        $date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                        continue;
                    } elseif (strpos($filter_query_field, 'year(') !== false){
                        $start = strpos($filter_query_field, '(');
                        $end = strpos($filter_query_field, ')');
                        $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                        $date[$filter_query_field]["value"]['year'] = $filter_query_value;
                        $date[$filter_query_field]["operator"] = $filter_query_operator;
                        $filter_query_operator = $date[$filter_query_field]['operator'];
                        $filter_query_value = implode('-', array_reverse($date[$filter_query_field]['value']));
                    }
                    if (isset($this->schedule_grid_fields[$filter_query_field])) {
                        $filter_field = $this->schedule_grid_fields[$filter_query_field];
                    }
                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }
        $result = array();
        $result['count'] = $query->count();
        if($result['count'] < $pageSize)
            $result['data'] = $query->orderby('date','DESC')->get()->all();
        else
            $result['data'] = $query->skip($page * $pageSize)->take($pageSize)->orderby('date','DESC')->get()->all();
        if(!empty($result))
            return $result;
        else
            return FALSE;
    }
    public function insertUploadSchedules($slab_data){
        $returnResult = array();
        try{
            date_default_timezone_set('Asia/Kolkata');
            $created_at = date("Y-m-d H:i:s");
            $cur_date = date_create(date("Y-m-d"));
            $date = date_create($slab_data['date']);
            if($date >= $cur_date){
                // Check in the schedules Unique Table
                $getUniqueData = DB::table("ffm_pjp_schedules")
                                    ->where('ff_id', '=', $slab_data['ff_id'])
                                    ->where('pincode', '=', $slab_data['pincode'])
                                    ->where('date', '=', $slab_data['date'])
                                    ->first();
                $update_flag=0;
                if( $getUniqueData ){
                    DB::table('ffm_pjp_schedules')
                        ->where('fps_id', $getUniqueData->fps_id )
                        ->update(['mobile_no' => $slab_data['mobile_no'],'city' => $slab_data['city'],'le_wh_id' => $slab_data['le_wh_id'],'updated_by' => $slab_data['created_by'], 'updated_at' => $created_at]);
                    $update_flag=1;
                    $returnResult['message'] = "Updated Successfully!";
                    $returnResult['counter_flag'] = "1";
                }else{
                    $slab_data['created_at'] = $created_at;
                    // inserting
                    DB::table("ffm_pjp_schedules")->insert($slab_data);
                    $update_flag = 1;
                    $returnResult['message'] = "Schedule(s) Inserted Successfully";
                    $returnResult['counter_flag'] = "2";
                }
            }else{
                $returnResult['message'] = "Cannot insert/update schedules for previous dates.";
                $returnResult['counter_flag'] = "3";
            }
        }catch(\ErrorException $ex){
            $returnResult['message'] = "Error occures, please check with system admin.";
            $returnResult['counter_flag'] = "3";
        }
        return $returnResult;
    }
    public function addNewSchedule($data){
        try{
            $statecity = explode(' [',$data['city_code']);
            $city = $statecity[0];
            $state = explode(']', $statecity[1])[0];
            if(empty($data['le_wh_id'])){
                $data['le_wh_id'] = NULL;
            }
            date_default_timezone_set('Asia/Kolkata');
            $ff_name = DB::table('users')->where('mobile_no',$data['mobile_no'])->select(DB::raw('GetUserName(users.user_id,2) as ffm_name'))->get()->all();
            $from_date = date('Y-m-d', strtotime($data['from_date']));
            $to_date = date('Y-m-d', strtotime($data['to_date']));
            while ($from_date<=$to_date){
                $schedule = DB::table('ffm_pjp_schedules')->where('date',$from_date)->where('ff_id',$data['ffm_id'])->where('pincode',$data['pincode'])->get()->all();
                if(empty($schedule)){
                    $result = DB::table('ffm_pjp_schedules')->insert([
                                    "ff_name" => $ff_name[0]->ffm_name,
                                    "ff_id" => $data["ffm_id"],
                                    "mobile_no" => $data["mobile_no"],
                                    "le_wh_id" => $data["le_wh_id"],
                                    "city" => $city,
                                    "state" => $state,
                                    "pincode" => $data["pincode"],
                                    "date" => $from_date,
                                    "created_by" => Session::get('userId'),
                                ]);
                }else{
                    $result = DB::table('ffm_pjp_schedules')->update([
                                    "le_wh_id" => $data["le_wh_id"],
                                    "updated_by" => Session::get('userId'),
                                    "updated_at" => date("Y-m-d H:i:s"),
                                ]);
                }
            $from_date = date('Y-m-d',strtotime("+1 day", strtotime($from_date)));
            }
                return $result;
        }catch(Exception $e){
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return false;
        }
    }
    public function updateSchedule($data){
        if(empty($data['le_wh_id'])){
            $data['le_wh_id'] = NULL;
        }
        $data['city_code'] = explode(' ',$data['city_code']);
        $date = date('Y-m-d', strtotime($data['date']));
        $schedule = DB::table('ffm_pjp_schedules')->where('date',$date)->where('ff_id',$data['ffm_id'])->where('pincode',$data['pincode'])->get()->all();
        if(empty($schedule)){
            date_default_timezone_set('Asia/Kolkata');
            $query = DB::table('ffm_pjp_schedules')->where('fps_id',$data['fps_id'])->update(['ff_name' => $data['ffm_name'],'city' => $data['city_code'][0],'pincode' => $data['pincode'],'le_wh_id' => $data['le_wh_id'],'date' => $date,'updated_by' => Session::get('userId'),'updated_at' => date("Y-m-d H:i:s") ]);
        return 1;
        }else{
            return 2;
        }        
    }
    public function getSchedule($id){
        $query = DB::table('ffm_pjp_schedules')->where('fps_id',$id)->get()->all();
        if(!empty($query)){
            $query[0]->date = date('d-m-Y',strtotime($query[0]->date));
            return $query;
        }
        return NULL;
    }

    public function deleteSchedule($id){
        $query = DB::table('ffm_pjp_schedules')->where('fps_id',$id)->delete();
        if(!empty($query))
            return true;
        return false;
    }
    public function excelFFMschedules($fdate,$tdate,$ff_id){
        $fdate = date('Y-m-d', strtotime($fdate)).' 00:00:00';
        $tdate = date('Y-m-d', strtotime($tdate)).' 23:59:59';
        $data = DB::table('ffm_pjp_schedules')->select('ff_name as FFM_Name','mobile_no as MobileNo',DB::raw('getLeWhName(le_wh_id)  AS BusinessUnit'),'state as Sate','city as City','pincode as Pincode',DB::raw('date_format(date,"%d-%b-%Y") as Date'))->where('ff_id',$ff_id)->whereBetween('date',array($fdate,$tdate))->get()->all();
        $data = json_decode(json_encode($data),1);
        return $data;
    }
    public function getlist($column,$manager=""){
        $list = DB::table('users')
                    ->leftJoin('user_roles','user_roles.user_id','=','users.user_id')
                    ->where('user_roles.role_id',52)
                    ->where('users.is_active',1);
        if($column == 'user_id'){
            $list = $list->select('users.user_id',DB::raw('GetUserName(users.user_id,2) as ffm'))
                    ->orderby('users.reporting_manager_id');
        }else{
            $list = $list->select('users.reporting_manager_id',DB::raw('GetUserName(users.reporting_manager_id,2) as name'))
                    ->where('users.user_id',$manager);
        }
        $list = json_decode(json_encode($list->get()->all()),1);
        return $list;
    }
    public function getaccessWarehouse($user_id){
        $warehouses = $this->roleObj->getWarehouseData($user_id, 6);
        $warehouses = json_decode($warehouses,1);
        $warehouses = isset($warehouses['118001'])?explode(',',$warehouses['118001']):[];
        return $warehouses;
    }
    public function getWarehouse($ffm){
        $mobile_no = DB::table('users')->where('user_id',$ffm)->pluck('mobile_no')->all();
        $mobile_no = $mobile_no[0];
        $loginwh = $this->getaccessWarehouse(Session::get('userId'),6);
        $ffmwh = $this->getaccessWarehouse($ffm);
        $wh = array_intersect($loginwh, $ffmwh);
        $data = array();
        $le_wh_id = DB::table('legalentity_warehouses')->select('le_wh_id','display_name')->wherein('le_wh_id',$wh)->get()->all();
        $le_wh_id = json_decode(json_encode($le_wh_id),1);
        $data['le_wh_id'] = $le_wh_id;
        $data['mobile_no'] = $mobile_no;
        return $data;
    }
    public function getAllNames($city,$dc)
    {
        $nameslist = array();
        $query = DB::table('state_city_codes')->where('city_name','like','%'.$city.'%')->select('city_name','state_name')->groupby('city_name');
        $name = json_decode(json_encode($query->get()->all()),1);
        foreach($name as $names){
            $names = array("label" => $names['city_name'].' ['.$names['state_name'].']',"city_code"=> $names['city_name']);
            array_push($nameslist,$names);
        }
        return $nameslist;
    }
}