<?php

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Log;
use Illuminate\Support\Facades\Cache;

class Reports extends Model {
    public $grid_field_db_match_reports=array();

    public function __construct() {
        try {
             //$this->grid_field_db_match_reports=array();
            /*$this->grid_field_db_match_reports = array(
                'ff_rp_id'=>'ff_rp_id',
                'name' => 'name',
                'order_cnt' => 'order_cnt',
                'calls_cnt' => 'calls_cnt',
                'tbv' => 'tbv',
                'uob' => 'uob',
                'abv' => 'abv',
                'tlc' => 'tlc',
                'ulc' => 'ulc',
                'alc' => 'alc',
                'hub_name'=>'hub_name',
                'contrib' => 'contrib',
                'margin' => 'ff.margin',
                'delivered_margin' => 'ff.delivered_margin',
                'order_date' => 'order_date',
                'beat' => 'beat',
                'cancel_ord_cnt' => 'cancel_ord_cnt',
                'cancel_ord_val' => 'ff.cancel_ord_val',
                'return_ord_cnt' => 'return_ord_cnt',
                'return_ord_val' => 'ff.return_ord_val',
                'display_name'   => 'display_name'
            );*/
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getReports($bu_id,$request) {
        if(!empty(Session::get('ffcolumns'))){
            $this->grid_field_db_match_reports=Session::get('ffcolumns');
        }
        $bydaymonth=$request->get('bydaymonth');
        $ff_id = $request->get('ff_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $start_date = ($start_date == '')?date('Y-m-d'):date('Y-m-d', strtotime($start_date));
        $end_date = ($end_date == '')?date('Y-m-d', strtotime('+1 day')):date('Y-m-d', strtotime($end_date));
        if(empty($bu_id)){
            $report_result = json_encode(array('Records' => [], 'TotalRecordsCount' => 0));
            return  $report_result;
            }else{
                $bu_id = $request->get('business_unit_id');
        }

        $data = DB::select("call getBuHierarchy_proc($bu_id,@le_wh_ids)");
        $data = DB::select(DB::raw('select @le_wh_ids as wh_list'));
        $data = json_decode(json_encode($data),1);
        $wh_list = $data[0];
        $wh_list = explode(",",$wh_list['wh_list']);

        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize');
        if (empty($page) && empty($pageSize)) {
            $page = 0;
            $pageSize = 10;
        }
        //DB::enableQueryLog();
        /*$query = DB::table('ff_report as ff')
                ->leftJoin('users','users.user_id','=','ff.user_id')
                ->leftJoin('legalentity_warehouses as lew','lew.legal_entity_id','=','users.legal_entity_id')
                ->select('ff.ff_rp_id','ff.tbv',
                        'ff.order_date','ff.order_cnt',
                        'ff.calls_cnt','ff.uob','ff.abv',
                        'ff.tlc','ff.ulc','ff.alc',
                        'ff.contrib','ff.name',
                        'ff.cancel_ord_cnt','ff.cancel_ord_val',
                        DB::raw("(CASE WHEN (ff.cancel_ord_val>0 AND ff.tbv >0) THEN
                        (ff.cancel_ord_val*100/ff.tbv) ELSE 0 END) AS 'cancel_percent'"),
                        DB::raw("(CASE WHEN ff.return_ord_val>0 AND ff.tbv >0 THEN
                        (ff.return_ord_val*100/ff.tbv) ELSE 0 END) AS 'return_percent'"),
                        DB::raw("(ff.tbv-(ff.cancel_ord_val + ff.return_ord_val)) AS 'today_business'"),
                        'ff.return_ord_cnt','ff.return_ord_val',
                        'ff.hub_name','ff.beat','ff.margin',
                        'ff.delivered_margin','lew.display_name')
                ->where('lew.dc_type','=','118001')*/
        if($bydaymonth=='1'){
            $query = DB::table('vw_ff_report');
        }else{
            $query = DB::table('vw_ff_report_month');    
        }
                
           $query= $query->whereIn('1_le_wh_id',$wh_list)->whereBetween(DB::raw('DATE(Order_Date)'),[$start_date,$end_date]);

        if($ff_id){
        $query = $query->where('1_user_id',$ff_id);
        }
        $query->orderBy('Order_Date', 'desc');

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
        if ($request->input('$filter')) {
            //checking for filtering                
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
        
        $count = $query->count(DB::raw('DISTINCT 1_ff_rp_id'));
        $query = $query->groupBy('1_user_id','Order_Date');
        $result = array();
        $result['count'] = $count;
        $reports = $query->skip($page * $pageSize)->take($pageSize)->get()->all();
        $i = 0;
        // log::info(DB::getQueryLog());
        // foreach ($reports as $report) {
        //     if ($report->order_date) {
        //         $dataFormat = date('d-m-Y', strtotime($report->order_date));
        //         $reports[$i]->order_date = $dataFormat;
        //         if($reports[$i]->cancel_ord_val > 0 and $reports[$i]->tbv > 0){
        //         $reports[$i]->cancel_percent = round((($reports[$i]->cancel_ord_val * 100)/$reports[$i]->tbv),2);}
        //         else {$reports[$i]->cancel_percent = 0;}
                
        //         if($reports[$i]->return_ord_val > 0 and $reports[$i]->tbv > 0){
        //         $reports[$i]->return_percent = round((($reports[$i]->return_ord_val * 100)/$reports[$i]->tbv),2);}
        //         else {$reports[$i]->return_percent = 0;}
                
        //         $reports[$i]->today_business = round($reports[$i]->tbv - ($reports[$i]->cancel_ord_val + $reports[$i]->return_ord_val),2);
        //     }
        //     $i++;
        // }
        //$reports['data']=json_decode(json_encode($reports),1);
        $header=json_decode(json_encode($reports),1);
        if(isset($header[0])){
            
            foreach($header[0] as $key => $value){
                
                    $headers[]=$key;
                    if(!array_key_exists($key, $this->grid_field_db_match_reports)){
                        $this->grid_field_db_match_reports[$key]=$key;
                        Session::set('ffcolumns', $this->grid_field_db_match_reports);
                    }
            }
        }else{
            $headers[]=[];
        }
       
        if (!empty($reports)) {
            $report_result = json_encode(array('Records' => $reports, 'TotalRecordsCount' => $result['count'],'headers'=>$headers));
        } else {
            $report_result = json_encode(array('Records' => $reports, 'TotalRecordsCount' => $result['count'],'headers'=>$headers));
        }
        return $report_result;
    }

    public function excelReports($bu_id,$request) {
        $ff_id = $request->get('ff_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $bydaymonth = $request->get('by_day_month');
        $start_date = ($start_date == '')?date('Y-m-d'):date('Y-m-d', strtotime($start_date));
        $end_date = ($end_date == '')?date('Y-m-d', strtotime('+1 day')):date('Y-m-d', strtotime($end_date));
        if(empty($bu_id)){
            return [];
        }else{
            $bu_id = $request->get('business_unit_id');
        }
        $reports = DB::select(DB::raw("CALL getConsolidatedFFReport('".$bu_id."','".$start_date."','".$end_date."','".$bydaymonth."')"));
                
        $data = json_decode(json_encode($reports),true);      
        return $data; 
    }

    
    public function getAllNames($term,$bu_id)
    {
        $dctype=DB::table('legalentity_warehouses as lw')->join('legal_entities as le','lw.legal_entity_id','=','le.legal_entity_id')->where('lw.bu_id',$bu_id)->groupBy('lw.bu_id')->first();
        $dctype=isset($dctype->legal_entity_type_id)?$dctype->legal_entity_type_id:0;
        $data=DB::statement("SET SESSION group_concat_max_len = 100000");
        $data = DB::select("call getBuHierarchy_bu($bu_id,$dctype,@buids)");
        $bu = DB::select(DB::raw('select @buids as buids'));
        $bu=isset($bu[0]->buids)?$bu[0]->buids:0;
        $bu=explode(',', $bu);
        $users_list = DB::table('user_permssion')->whereIn('object_id',$bu)->where('permission_level_id',6)->pluck('user_id')->all();
        $nameslist = array();
        $query = DB::table('ff_report')
                ->where('name','like','%'.$term.'%')
                ->pluck(DB::Raw('DISTINCT(user_id) as user_id'))->all();
        $query = array_intersect($users_list, $query);
        $query = DB::table('users')
                ->whereIn('user_id',$query)
                ->select(DB::raw('concat(GetUserName(user_id,2),CASE WHEN getwhName(user_id) IS NOT NULL 
       THEN concat("(",getwhName(user_id),")")
       ELSE ""
END) as name'),'user_id')
                ->get()->all();
        $name = json_decode(json_encode($query),1);


        foreach($name as $names){
            $names = array("label" => $names['name'],"ff_id" => $names['user_id']);
            array_push($nameslist,$names);
        }
        return $nameslist;

    }
    public function getFFMonthlyAttReport($start_date,$end_date){
        $query = DB::selectFromWriteConnection(DB::raw("CALL getFFMonthlyAttReport('".$start_date."','".$end_date."')"));
        return $query; 
    }
    public function getFFMonthlyAttReportUser($start_date,$end_date){
        $query = DB::selectFromWriteConnection(DB::raw("CALL getFFMonthlyAttReportData('".$start_date."','".$end_date."')"));
        return $query; 
    }

    public function getAvgStockData($bu_id) {
        try{
           
            return DB::selectFromWriteConnection(DB::raw("CALL  get_daily_avg_bal($bu_id)"));


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return false;
        }
    }
    public function getSalesConsolidateData($fromdate,$todate) {
        try{
           
            return DB::select(DB::raw("CALL  getSalesConsolidateReport('".$fromdate."','".$todate."')"));


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return false;
        }    

    }

}
