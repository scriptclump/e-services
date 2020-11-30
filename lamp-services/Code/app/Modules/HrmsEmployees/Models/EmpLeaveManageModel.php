<?php

namespace App\Modules\HrmsEmployees\Models;

use App\Central\Repositories\RoleRepo;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Session;
use URL;
use App\Modules\Roles\Models\Role;
use App\Modules\HrmsEmployees\Models\EmpLeaveManageModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Central\Repositories\ProductRepo;

class EmpLeaveManageModel extends Model {

    
    public function __construct() {
        $this->roleRepo = new RoleRepo();
        
    }

    public function getLeaveTypes($empid,$empgroupid){

        try {
            $types = DB::table("master_lookup")
                    ->whereIn("mas_cat_id", [148, 149])
                    ->get(["mas_cat_id", "master_lookup_name", "value"])->all();

            $optionalholidays = DB::table("holiday_list")
                                ->whereIn("holiday_type",[0,1])
                                ->where('holiday_date', '>=',date('Y').'-01-01')
                                ->where("emp_group_id", $empgroupid)
                                ->get(["holiday_type as is_fixed","holiday_name as reason","holiday_list_id as holiday_id","holiday_date as date"])->all();
            foreach ($types as $each_value){
                if ($each_value->mas_cat_id == 148){
                    if($each_value->value != 148003){
                        $leave_info["leave_type"][$each_value->value] = $each_value->master_lookup_name;
                    }
                }
                else if($each_value->mas_cat_id == 149){
                    $leave_info["leave_reason_type"][$each_value->value] = $each_value->master_lookup_name;
                }
            }
            foreach ($optionalholidays as $each_value){
                if($each_value->is_fixed == 0){
                    $leave_info["leave_holiday_type"][$each_value->holiday_id] = $each_value->reason." "."[".$each_value->date."]";
                }
            }
            
            $leave_info["current_leave_count"] = DB::table("leave_master")->where("emp_id", $empid)
            ->select(DB::raw("getMastLookupValue(leave_type) as leave_type"), "no_of_leaves")->get()->all();

            return $leave_info;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }

    }

    public function getEmployeegroupId($userid){

       $ids = DB::table("employee as emp")->join("users as u", function ($join) {
                        $join->on("emp.emp_id", "=", "u.emp_id");
                    })->where("u.is_active", 1)->where("emp.is_active", 1)
                    ->where("u.user_id", $userid)
                    ->get(["emp.emp_id", "emp.emp_group_id", "u.user_id"])->all();

        if($ids){
            return json_decode(json_encode($ids), true)[0];
        }else{
            return 0;
        }
    }
    public function getFromdate($optional_leave){
        $fromdate = array();
        $fromdate = DB::table("holiday_list")
                    ->where("holiday_list_id",$optional_leave['optional_leave'])
                    ->get(["holiday_date as date"])->all();
        $from_date = $fromdate[0]->date;
        return $from_date;
    }

    public function getapplyLeaveHistory($empid,$makeFinalSql, $orderBy, $page, $pageSize){ 

        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= ' AND ' . $value;
            }elseif(count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }

$leave_info = "select IF(STATUS=57163,CONCAT('<center><code>','<a class=\"btn btn-info\" href=\"javascript:void(0)\" onclick=\"LeaveWithdraw(',leave_history_id,')\">\n Withdraw\n </a>&nbsp;&nbsp;&nbsp;\n </code>\n </center>'),'') AS `CustomAction`, getMastLookupValue(leave_type) as leave_type,no_of_days,DATE_FORMAT(from_date,'%m/%d/%Y') as from_date,DATE_FORMAT(to_date,'%m/%d/%Y') as to_date,contact_number,  (CASE WHEN leave_type = 148005 THEN getOptionalLeaveName(DATE_FORMAT(from_date,'%Y-%m-%d') )ELSE   getMastLookupValue (reason) END) as reason,getMastLookupValue(status) as status from leave_history where emp_id=$empid " . $sqlWhrCls . " order by leave_history_id DESC" ; 

    $allRecallData = DB::select(DB::raw($leave_info));
    $TotalRecordsCount = count($allRecallData);
    if($page!='' && $pageSize!=''){
        $page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
        $allRecallData = array_slice($allRecallData, $page , $pageSize);
    }
    $arr =  json_encode(array('results'=>$allRecallData,
        'TotalRecordsCount'=>(int)($TotalRecordsCount))); 
    return $arr;
    }

    public function getwithdrawdata($leaveid){
     $leave_info = "select * from leave_history where leave_history_id=$leaveid"; 
    $allData = DB::select(DB::raw($leave_info)); 
    return $allData;   

    }
}
