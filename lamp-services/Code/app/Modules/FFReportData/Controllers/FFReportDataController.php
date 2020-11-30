<?php

namespace App\Modules\FFReportData\Controllers;

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
use App\Modules\FFReportData\Models\Reports;
use Carbon\Carbon;
use App\Central\Repositories\RoleRepo;
use App\Modules\Orders\Models\Inventory;


class FFReportDataController extends BaseController
{

    public function __construct()
    {
        try
        {
            $this->_Inventory = new Inventory();
            $this->_roleRepo = new RoleRepo();

            $this->middleware(function ($request, $next) {
                session()->forget('business_unitid');
                if (!Session::has('userId'))
                {
                    return \Redirect::to('/');
                }
                return $next($request);
            });

            parent::Title('FF Report Data- Ebutor');
            $this->grid_field_db_match_grouprepo = array(
                'Name' => 'Name',
            );
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction()
    {
        try
        {
            $userId = Session::get('userId');
                $getaccessbuids=$this->getBuidsByUserId($userId);
                $getaccessbuids=explode(',', $getaccessbuids);
                $getaccessbuids=min($getaccessbuids);
                if($getaccessbuids==0 && is_numeric($getaccessbuids)){
                        $buid=DB::table('business_units')
                              ->select('bu_id')
                              ->where('parent_bu_id',$getaccessbuids)
                              ->first();
                        $bu_id=isset($buid->bu_id) ? $buid->bu_id: '';
                }else{
                    $bu_id=$getaccessbuids;
                }
           
            $rolerepo = new RoleRepo();
            $approveAccess = $rolerepo->checkActionAccess($userId, 'FF1001');
            if (!$approveAccess)
            {
                return redirect()->to('/');
            }
            parent::Title('Ebutor - FF Daily Log Report');
            $breadCrumbs = array('Dashboard' => url('/'), 'FF Daily Log Report' => '#');
            parent::Breadcrumbs($breadCrumbs);
                   return View::make('FFReportData::index')->with(['bu_id'=>$bu_id]);
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getReports(Request $request)
    {
        try
        {
            $userId = Session::get('userId');
            $rolerepo = new RoleRepo();
            $business_unit_id = Input::get('business_unit_id');
            $ff_name = Input::get('ff_name');
            $ff_id = Input::get('ff_id');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $start_date = ($start_date == '')?date('Y-m-d'):date('Y-m-d', strtotime($start_date));
            $end_date = ($end_date == '')?date('Y-m-d', strtotime('+1 day')):date('Y-m-d', strtotime($end_date));
            $reports = new Reports();
            $result = $reports->getReports($start_date, $end_date, $request, $business_unit_id,$ff_id,$userId);
            $report_excel = json_decode($result, true);
            return $result;
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function excelSalesReports()
    {
        try
        {
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', -1);
            $userId = Session::get('userId');
            $rolerepo = new RoleRepo();
            $business_unit_id = Input::get('business_unit_id');
            $ff_name = Input::get('ff_name');
            $ff_id = Input::get('ff_id');
            $start_date = Input::get('start_date');
            $end_date = Input::get('end_date');
            $reportDate = Carbon::now();
            $excel_reports = new Reports();
            $report_excel = $excel_reports->excelReports($start_date, $end_date, $business_unit_id,$ff_id,$userId);
            Excel::create('FFReportData_' . $reportDate, function($excel) use($report_excel) {
                /*$excel->sheet('reportsData', function($sheet) use($report_excel) {
                    $sheet->loadView('FFReportData::excel_reports')->with('Reportinfo', $report_excel);
                });*/
                $excel->sheet('reportsData', function($sheet) use($report_excel) {          
                $sheet->fromArray($report_excel);
                }); 
            })->export('xls');
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getFFNames(Request $request)
    {
        try
        {
            $term = $request->get('term');
            $bu_id = $request->get('buid');
            $reportObj = new Reports();
            $namesList = $reportObj->getAllNames($term,$bu_id);
            return $namesList;
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getBuidsByUserId($userid){
        try{
            $buids=DB::table('user_permssion')
                       ->select(DB::raw("GROUP_CONCAT(object_id) as object_id"))
                       ->where('user_id',$userid)
                       ->where('permission_level_id',6)
                       ->get()->all();
             $buids=isset($buids[0]->object_id)?$buids[0]->object_id:'';
             return $buids;
            }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function odersTabGetBuUnit(){
        return $this->_Inventory->businessTreeData();
    }
     public function setBuidInSession(){
            $data=Input::all();
            Session::set('business_unitid', $data['buid']);
            return Session::get('business_unitid');
    }



}
