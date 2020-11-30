<?php

namespace App\Modules\FFReportData\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Log;
use Illuminate\Support\Facades\Cache;
use App\Modules\Roles\Models\Role;
use \App\Central\Repositories\RoleRepo;

class Reports extends Model
{

    public function getReports($start_date, $end_date, $request, $business_unit_id, $ff_id,$userId)
    {
        if(empty($ff_id)){
             $ff_id = 'NULL';
        }
        if($business_unit_id){
            $bu_id = $business_unit_id;
        }else {
            $bu_id = 1;
        }        
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize');
        if (empty($page) && empty($pageSize))
        {
            $page = 1;
            $pageSize = 1;
        }
        $query = DB::select("CALL getFFReportData(".$userId.",".$bu_id.",'".$start_date."','".$end_date."',".$ff_id.")");

        $count = count($query);
        $result = array();
        $result['count'] = $count;
        $reports = $query;
        $i = 0;

        
        if (!empty($reports))
        {
            $reports=json_decode(json_encode($reports),1);
            $header=json_decode(json_encode($reports),1);
            foreach($header[0] as $key => $value){
                    $headers[]=$key;
                    
            }
            $report_result = json_encode(array('Records' => $reports, 'TotalRecordsCount' => $result['count'],'headers'=>$headers));
        }
        else
        {
            $report_result = json_encode(array('Records' => $reports, 'TotalRecordsCount' => $result['count'],'headers'=>[]));
        }
        return $report_result;
    }

    public function fieldForceUserIds()
    {
        $loggedInUser = Session::get('userId');
        $roleRepoClass = new RoleRepo();
        
        // Checking the Access
        $globalAccess = $roleRepoClass->checkPermissionByFeatureCode('GLB0001');
        if($globalAccess == true){
            // Access Roles for Field Force Officers and Field Force Mangers
            $dataSet = $roleRepoClass->getUsersByRoleCode(['SSLL','SSLO']);
            // Retrieveing only UserIds
            $usersList = [];
            foreach ($dataSet as $record)
                array_push($usersList, $record->user_id);
            return $usersList;
        }

        // To get all SubOrdinates
        return $roleRepoClass->getUsersListBasedOnReportingManagerHierarchy($loggedInUser,1);
    }

    public function excelReports($start_date, $end_date, $business_unit_id, $ff_id,$userId)
    {    
        
        if(empty($ff_id)){
             $ff_id = 'NULL';
        }
        if($business_unit_id){
            $bu_id = $business_unit_id;
        }else{
            $bu_id = 1;
        } 
        $start_date = ($start_date == '')?date('Y-m-d'):date('Y-m-d', strtotime($start_date));
        $end_date = ($end_date == '')?date('Y-m-d', strtotime('+1 day')):date('Y-m-d', strtotime($end_date));
        $query = DB::select("CALL getFFReportData(".$userId.",".$bu_id.",'".$start_date."','".$end_date."',".$ff_id.")");
        $reports = $query;
        $data = json_decode(json_encode($reports), true);
        $result = array();
        $result = $data;
        return $result;
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
        $bu = array_unique($bu);
        $users_list = DB::table('user_permssion')->whereIn('object_id',$bu)->where('permission_level_id',6)->pluck('user_id');
        $users_list = array_unique($users_list);
        $nameslist = array();
        $query = DB::table('users')
                    ->whereIn('user_id',$users_list)
                    ->Where(DB::raw('concat(users.firstname," ",users.lastname)'), 'like', '%' . $term . '%')
                    ->pluck('user_id')->all();
        $user_ids = array_intersect($users_list, $query);
        $name = DB::table('ff_call_logs')
                ->whereIn('ff_id', $user_ids)
                ->select(DB::raw('concat(GetUserName(ff_id,2)," (",getwhName(ff_id),")") as user_id'),'ff_id')
                ->get()->all();
        $name = json_decode(json_encode($name),1);
        $name = array_map("unserialize", array_unique(array_map("serialize", $name)));
        $nameslist = array();
        foreach($name as $names){
            $names = array("label" => $names['user_id'],"ff_id" => $names['ff_id']);
            array_push($nameslist,$names);
        }
        return $nameslist;
    }
    
}