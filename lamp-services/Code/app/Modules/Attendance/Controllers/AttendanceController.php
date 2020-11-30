<?php

namespace App\Modules\Attendance\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Log;
use DB;
use Excel;
use App\Modules\Attendance\Models\Attendance;
use Carbon\Carbon;
use App\Central\Repositories\RoleRepo;

class AttendanceController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return \Redirect::to('/');
                }
            return $next($request);
            });
            parent::Title('Attendance Report - Ebutor');
            $breadCrumbs = array('Dashboard' => url('/'), 'Attendance' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $this->grid_field_db_match_grouprepo = array(
                'Name' => 'Name',
            );
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            $userId = Session::get('userId');
            $rolerepo = new RoleRepo();
            $approveAccess = $rolerepo->checkActionAccess($userId, 'FF2001');
            if(!$approveAccess)
            {
            return \redirect()->to('/');
            }
            $roles = DB::table('roles')->whereIn('name',array('Delivery Executive','Picker','Field Force Associate'))->pluck('name')->all();
            //print_r($roles);die;
            return View::make('Attendance::index')->with('roles',$roles);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getAttendanceReports(Request $request) {
        try {
            //$user_name = Input::get('user_name');
            $user_role = Input::get('user_role');
            //echo $user_role;
            $start_date = date('Y-m-d', strtotime($request->get('start_date')));
            $end_date = date('Y-m-d', strtotime($request->get('end_date')));
            $Attendreports = new Attendance();
            //$result = $reports->getReports($start_date, $end_date, $request,$user_name,$user_role);
            $result = $Attendreports->getAttendReports($start_date, $end_date, $request,$user_role);
                                            
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function excelAttendanceReports() {
        try {
            $user_name = Input::get('user_name');
            $user_role = Input::get('user_role');
            $start_date = Input::get('start_date');
            $end_date   = Input::get('end_date'); 
			$reportDate = Carbon::now(); 			
            $Attendreports = new Attendance();
//            $report_excel = $excel_reports->excelReports($start_date, $end_date,$user_name,$user_role);                        
            $report_excel = $Attendreports->excelAttendanceReports($start_date, $end_date,$user_role);                        
            Excel::create('Attendance_'.$reportDate, function($excel) use($report_excel) {                        
                $excel->sheet('reportsData', function($sheet) use($report_excel) {                        
                        $sheet->loadView('Attendance::excel_reports')->with('Reportinfo', $report_excel);
                    });
        })->export('xls');        
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
//        public function getUserNames(Request $request) {
//        try {	
//            $term = $request->get('term');
//            $reportObj = new Reports();
//            $namesList = $reportObj->getAllNames($term);
//            return $namesList;
//        } catch (\ErrorException $ex) {
//            Log::error($ex->getMessage());
//            Log::error($ex->getTraceAsString());
//        }
//    }
}

