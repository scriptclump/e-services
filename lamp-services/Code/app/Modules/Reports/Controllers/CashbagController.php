<?php

namespace App\Modules\Reports\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Excel;
use Illuminate\Support\Facades\Config;
use App\Modules\Reports\Models\Reports;
use Carbon\Carbon;
use App\Central\Repositories\RoleRepo;

class CashbagController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return \Redirect::to('/');
                }
                return $next($request);
            });	
			
            parent::Title('Commission Report - Ebutor');
            $this->grid_field_db_match_grouprepo = array(
                'Name' => 'Name',                
            );
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function cashbagReport()
    {   
        return View::make("Reports::cashbag");
    }
    public function master_lookup($id)
    {
        $rs = DB::table("master_lookup")
                ->where('mas_cat_id',"=",$id)
                ->pluck('value')->all();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function getRoles()
    {
        $valuesData = $this->master_lookup('147');
        $data = DB::table('roles')
                ->wherein('role_id',$valuesData)
                ->select('role_id','name')
                ->get()->all();
        $data =  json_decode(json_encode($data), true);
        $list = '<option value="0">All Roles ...</option>';
        if(!empty($data))
        {
            foreach ($data as $value) 
            {
                $list.= '<option value="' .  $value['role_id'] . '" >' . $value['name'].'</option>';
            }
        }
        return $list;
    }
    public function usersInfo()
    {
        $valuesData = $this->master_lookup('147');
        $usersList = DB::table('users')
                    ->join('user_roles','users.user_id','=','user_roles.user_id')
                    ->select('users.user_id','firstname','lastname')
                    ->wherein('role_id',$valuesData)
                    ->get()->all();
        $usersList = json_decode(json_encode($usersList), true);
        $list = '<option value="0">All users ...</option>';
        if(!empty($usersList))
        {
            foreach ($usersList as $value) 
            {
                $list.= '<option value="' .  $value['user_id'] . '" >' . $value['firstname'] ." ".$value['lastname']. '</option>';
            }
        }
        return $list;
    }
    public function cashBagData(Request $request)
    {
        $page = $request->page;
        $user_id = (empty($request->user_id))?0:$request->user_id;
        $role_id = (empty($request->role_id))?0:$request->role_id;

        $from_date =  date('Y-m-d', strtotime($request->from_date));
        $to_date =  date('Y-m-d', strtotime($request->to_date));
        $from_Data = ($from_date == "1970-01-01")?Date('Y-m-d'):$from_date;
        $to_Data = ($to_date == "1970-01-01")?Date('Y-m-d'):$to_date;
        $pageSize = $request->pageSize;
        if($page ==0)
        {
            $page =1;
            $pageSize = 0;
        }
        $query = DB::select("call getECashReport(".$user_id.",".$role_id.",'".$from_Data."','".$to_Data."')");
        $columnHeadings= json_decode(json_encode($query), true);

        $counts=  count($query);
        $offSet = ($page * $pageSize);
        if($offSet == 0)
        {
            $offSet=10;
        }
        //echo $offSet.'==='.$page;
       // $query = array_slice($query, $pageSize, $offSet, true);
        $query = json_encode($query, true);

        $report_result = json_encode(array('Records' => $query, 'TotalRecordsCount' => $counts));
        return $report_result;
    }
    public function getProcedureHeadings(Request $request)
    {
        $from_date =  date('Y-m-d', strtotime($request->from_date));
        $to_date =  date('Y-m-d', strtotime($request->to_date));
        $from_Data = ($from_date == "1970-01-01")?Date('Y-m-d'):$from_date;
        $to_Data = ($to_date == "1970-01-01")?Date('Y-m-d'):$to_date;
        $user_id = (empty($request->user_id))?0:$request->user_id;
        $role_id = (empty($request->role_id))?0:$request->role_id;
        
        $query = DB::select("call getECashReport(".$user_id.",".$role_id.",'".$from_Data."','".$to_Data."')");
        $columnHeadings= json_decode(json_encode($query), true);
        $array_keyData ="";
        if(!empty($columnHeadings))
        {
            $array_keyData = array_keys($columnHeadings[0]);
        }
        return $array_keyData;
    }
    public function getRoleId($id)
    {
        $sqlRs = DB::table('user_roles')
                ->where('user_id','=',$id)
                ->pluck('role_id')->all();
        if($sqlRs)
        {
            return $sqlRs[0];
        }else
        {
            return 0;
        }
        
    }
}   
