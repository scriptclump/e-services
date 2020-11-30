<?php

namespace App\Modules\Dashboard\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use Carbon\Carbon;

class ChartProcedures extends Model {

    protected $table = "dashboard_preference";
    protected $primaryKey = "dashboard_pref_id";

    public function userDashboardPreference() {
//        if (Session('roleId') != '1') {
//            $legal_entity_id = Session::get('legal_entity_id');
//        } else {
//            $legal_entity_id = 0;
//        }
        $legal_entity_id = 0;

        $query = $this->join('dashboard_master', 'dashboard_master.dashboard_master_id', '=', 'dashboard_preference.dashboard_master_id');
        $query = $query->where('legal_entity_id', $legal_entity_id)
                ->where('dashboard_master.is_active', 1)
                ->where('dashboard_preference.is_active', 1)
                ->get(['dashboard_master.dashboard_master_id', 'dashboard_master.dashboard_name', 'dashboard_master.x-axis_name', 'dashboard_master.y-axis_name', 'dashboard_master.chart_type', 'dashboard_master.period', 'dashboard_master.proc_name', 'dashboard_preference.sort_order'])->all();
        $preference = json_decode(json_encode($query), true);
        
        foreach($preference as $each_key => $each_pref){
            $period = json_decode(json_encode(DB::select("select getMastLookupValue(?) as period", [$each_pref['period']])), true);
            $preference[$each_key]['period'] = $period[0]['period'];
            
            $dates = $this->fromtoDate($preference[$each_key]['period']);
            
            $chart = json_decode(json_encode(DB::select("select getMastLookupValue(?) as chart_type", [$each_pref['chart_type']])), true);
            $preference[$each_key]['chart_type'] = $chart[0]['chart_type'];
            
            $preference[$each_key]['dt_from'] = $dates['from_date'];
            $preference[$each_key]['dt_to'] = $dates['to_date'];
            
            $preference[$each_key]['status_list'] = -1;
            
            if($each_pref['dashboard_name'] == 'Orders'){
                $preference[$each_key]['filter_by'] = DB::table('master_lookup')->where('is_active', 1)->where('mas_cat_id', 17)->orderBy('master_lookup_name', 'asc')->where('value', '!=', NULL)->pluck('master_lookup_name')->all();
            } elseif($each_pref['dashboard_name'] == 'Purchases'){
                $preference[$each_key]['filter_by'] = DB::table('master_lookup')->where('is_active', 1)->where('mas_cat_id', 87)->orderBy('master_lookup_name', 'asc')->pluck('master_lookup_name')->all();
            }
            
            $today_str = strtotime(date('Y-m-d'));
            $today = date("Y-m-d", $today_str);
            $preference[$each_key]['period_by']['Today'] = $today." and ".$today;
            
            $yesterday = date("Y-m-d", strtotime("-1 day"));
            $preference[$each_key]['period_by']['Yesterday'] = $yesterday." and ".$yesterday;
            
            $this_week_start = (date('w', $today_str) == 0) ? $today_str : strtotime('last sunday', $today_str);
            $this_week_end = (date('w', $today_str) == 6) ? $today_str : strtotime('this saturday', $today_str);
            $preference[$each_key]['period_by']['This Week'] = date('Y-m-d', $this_week_start)." and ".date("Y-m-d", $this_week_end);
            
            $previous_week = strtotime("-1 week +1 day");	
            $last_week_start = strtotime("last sunday midnight",$previous_week);
            $last_week_end = date("Y-m-d", strtotime("next saturday",$last_week_start));
            $preference[$each_key]['period_by']['Last Week'] = date("Y-m-d", $last_week_start)." and ".$last_week_end;
            
            $this_month = date("Y-m-d", strtotime("first day of this month"))." and ".date("Y-m-d", strtotime("last day of this month"));
            $preference[$each_key]['period_by']['This Month'] = $this_month;
            
            $last_month = date("Y-m-d", strtotime("first day of last month"))." and ".date("Y-m-d", strtotime("last day of last month"));
            $preference[$each_key]['period_by']['Last Month'] = $last_month;
            
//            $current_quarter = ceil(date("m")/3);
//            $quarter = $this->quarterDates($current_quarter);
//            $preference[$each_key]['period_by']['This Quarter'] = $quarter[0];
//            $preference[$each_key]['period_by']['Last Quarter'] = $quarter[1];
            
            $this_year = date_format(date_create(date("Y")."-01-01"), "Y-m-d")." and ".date_format(date_create(date("Y")."-12-31"), "Y-m-d");
            $preference[$each_key]['period_by']['This Year'] = $this_year;
            
            $last_year = date_format(date_create(date("Y", strtotime("-1 year"))."-01-01"), "Y-m-d")." and ".date_format(date_create(date("Y", strtotime("-1 year"))."-12-31"), "Y-m-d");
            $preference[$each_key]['period_by']['Last Year'] = $last_year;
        }
        
        return $preference;
    }
    
    public function fromtoDate($period){
        $dates = array();
        $today_str = strtotime(date('Y-m-d'));
        $today = date("Y-m-d", $today_str);
        if($period == 'Day'){
            $dates['from_date'] = $today;
            $dates['to_date'] = $today;
        } elseif($period == 'Week'){
            $this_week_start = (date('w', $today_str) == 0) ? $today_str : strtotime('last sunday', $today_str);
            $this_week_end = (date('w', $today_str) == 6) ? $today_str : strtotime('this saturday', $today_str);
            $dates['from_date'] = date('Y-m-d', $this_week_start);
            $dates['to_date'] = date("Y-m-d", $this_week_end);
        } elseif($period == 'Month'){
            $dates['from_date'] = date("Y-m-d", strtotime("first day of this month"));
            $dates['to_date'] = date("Y-m-d", strtotime("last day of this month"));
        } elseif($period == 'Year'){
            $dates['from_date'] = date_format(date_create(date("Y")."-01-01"), "Y-m-d");
            $dates['to_date'] = date_format(date_create(date("Y")."-12-31"), "Y-m-d");
        }
        
        return json_decode(json_encode($dates), true);
    }
    
    public function quarterDates($quarter){
        $today = date("Y-m-d", strtotime(date('Y-m-d')));
        if($quarter == 1){
            $this_quarter = date("Y-m-d", strtotime("first day of january"))." and ".$today;
            $last_quarter = date("m-d", strtotime("first day of october"))." and ".date("m-d", strtotime("last day of december"));
        } elseif($quarter == 2){
            $this_quarter = date("Y-m-d", strtotime("first day of april"))." and ".$today;
            $last_quarter = date("Y-m-d", strtotime("first day of january"))." and ".date("Y-m-d", strtotime("last day of march"));
        } elseif($quarter == 3){
            $this_quarter = date("Y-m-d", strtotime("first day of july"))." and ".$today;
            $last_quarter = date("Y-m-d", strtotime("first day of april"))." and ".date("Y-m-d", strtotime("last day of june"));
        }elseif($quarter == 4){
            $this_quarter = date("Y-m-d", strtotime("first day of october"))." and ".$today;
            $last_quarter = date("Y-m-d", strtotime("first day of july"))." and ".date("Y-m-d", strtotime("last day of september"));
        }
        $quarter_dates = array($this_quarter, $last_quarter);
        return $quarter_dates;
    }
    
    public function getChartData($procedureName, $fromDate, $toDate, $statusList_implode) {
        $rolesObj = new Role();
        $userIds = json_decode($rolesObj->getFilterData(4, Session::get('userId')), true);
        $userIds_implode = "'".implode(',', $userIds['customer'])."'";
        
        $allData = json_decode(json_encode(DB::select("CALL ".$procedureName."('".$fromDate."', '".$toDate."', 0, 0, ".$statusList_implode.", ".$userIds_implode.")")), true);
        
        $pieArr = $allArr = $finalArray = array();
        // Data for Pie Chart
        foreach($allData as $eachData){
            $pieArr[$eachData['STATUS']][] = (int)$eachData['count1'];
        }
        $finalArray['Pie'][0] = array('Status', 'Count');
        $i = 1;
        foreach($pieArr as $key => $value){
            $finalArray['Pie'][$i] = array($key, (int)array_sum(array_values($value)));
            $status_count[] = (int)array_sum(array_values($value));
            $i++;
        }
        // Data for other Charts
        foreach($allData as $eachData){
            $all_status[] = (string)$eachData['STATUS'];
            $allArr[$eachData['date1']][0] = (string)$eachData['date1'];
            $allArr[$eachData['date1']][] = (int)$eachData['count1'];
        }
        $finalArray['All'] = array_combine(range(1, count($allArr)), array_values($allArr));
        $unique_status = array_unique($all_status);
        array_unshift($unique_status, "Date");
        $finalArray['All'][0] = $unique_status;
        ksort($finalArray['All']);
        
        return $finalArray;
    }

}
