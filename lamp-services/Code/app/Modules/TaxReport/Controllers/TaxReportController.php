<?php
/*
 */

namespace App\Modules\TaxReport\Controllers;
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);
use App\Http\Controllers\BaseController;
use View;
use Log;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use App\Modules\TaxReport\Models\InputTax;
use App\Modules\TaxReport\Models\OutputTax;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use PDF;

class TaxReportController extends BaseController {

    public function __construct() {
        $this->_inputtax = new InputTax();
        $this->_outputtax = new OutputTax();
        $this->_roleRepo = new RoleRepo();
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                $access = $this->_roleRepo->checkPermissionByFeatureCode('TAXREP001');
                parent::Title('Tax Report');
          
                if (!$access) {
                    Redirect::to('/')->send();
                    die();
                }
                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

/*

 */
    public function index() {
        $breadCrumbs = array('Home' => url('/'), 'Report' => url('#'), 'Tax Report' => url('/taxreport/index'));
        parent::Breadcrumbs($breadCrumbs);
        $options = json_decode(json_encode($this->_inputtax->filterOptions()), true);
        return view('TaxReport::index')->with(['filter_options' => $options]);
    }

    public function inwardDashboard(Request $request, $data = '') {
        try {
            $decoded_input = json_decode($request->input('filterData'), true);

            $page = $request->input('page');
            $page_size = $request->input('pageSize');
            $filters = '';

            if (!empty($decoded_input)) {
                $filters = $decoded_input;
                $page = 0;
            }


            $taxreport_list = json_decode(json_encode($this->_inputtax->inwardGridData($page, $page_size, $filters)), true);
            foreach ($taxreport_list['result'] as $key => $each) {
                $taxreport_list['result'][$key]['state'] = $this->_inputtax->getStateName($each['state']);
            }

            if (isset($taxreport_list['result'])) {
                echo json_encode(array('results' => $taxreport_list['result'], 'TotalRecordsCount' => $taxreport_list['count']));
            } else {
                echo json_encode(array('results' => '0', 'TotalRecordsCount' => $taxreport_list['count']));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function outwardDashboard(Request $request) {
        try {
            $decoded_input = json_decode($request->input('filterData'), true);
            $page = $request->input('page');
            $page_size = $request->input('pageSize');
            $filters = '';

            if (!empty($decoded_input)) {
                $filters = $decoded_input;
            }

            $taxreport_list = $this->_outputtax->outwardGridData($page, $page_size, $filters);
            foreach ($taxreport_list['result'] as $key => $each) {
                $taxreport_list['result'][$key]['state'] = $this->_inputtax->getStateName($each['state']);
            }

            if (isset($taxreport_list['result'])) {
                echo json_encode(array('results' => $taxreport_list['result'], 'TotalRecordsCount' => $taxreport_list['count']));
            } else {
                echo json_encode(array('results' => '0', 'TotalRecordsCount' => $taxreport_list['count']));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function taxReportExcelExport(Request $request) {
        try {
            $decoded_input = json_decode($request->input('data'), true);
            // $period = $request->input("period");
            // if($period == "this_week")
            // {
            //     $current_dayname = date("l");
            //     $start_date = date("Y-m-d",strtotime('monday this week'));
            //     $end_date   = date("Y-m-d",strtotime("sunday this week")); 
            // }elseif ($period == "last_week") 
            // {
            //     $previous_week = strtotime("-1 week +1 day");
            //     $start_week = strtotime("last monday midnight",$previous_week);
            //     $end_week = strtotime("next sunday",$start_week);
            //     $start_date = date("Y-m-d",$start_week);
            //     $end_date = date("Y-m-d",$end_week);
            // }elseif ($period == "this_month") 
            // {
               
            //     $start_date =  date('Y-m-01');
            //     $end_date = date('Y-m-t'); 
            // }elseif ($period == "last_month")
            // {
            //     $start_date = date('Y-m-d', strtotime("first day of last month"));
            //     $end_date   = date('Y-m-d', strtotime("last day of last month"));
            // }

            $mytime = Carbon::now();
            $page = 0;
            $page_size = 1000000000;
            $result = "";
            
            $requestType = $request->input('type');

            // echo "stop point place ".$requestType;

            $exportType = $request->input('exportType');
            $data = json_decode($request->input('data'), true);
            // echo $exportType;die;
            Log::error("Type of request" . $requestType);

            if ($requestType == 'Inward') {
                Log::error("Inward type request started");
                $taxreport_list = json_decode(json_encode($this->_inputtax->inwardGridDataExport($decoded_input)), true);
                // echo "<pre>";print_r($taxreport_list);die;
                Log::error("Inward type request ended");
            }

            if ($requestType == 'Outward') {
                Log::error("Outward type request started");
                $taxreport_list = json_decode(json_encode($this->_outputtax->getDataForReport($decoded_input)), true);
                // echo "hiii <pre>";print_r($taxreport_list);die;
                Log::error("Outward type request ended");
            }
            Log::error("assigning state name to state id");
            // print_r($taxreport_list);die;
            foreach ($taxreport_list['result'] as $key => $each) {
                $taxreport_list['result'][$key]['state'] = $this->_inputtax->getStateName($each['state']);
            }
            $result = $taxreport_list['result'];
            if ($exportType == 'excel') {
                Log::error("Excel writing started");
                Excel::create('Tax Report-' . $requestType . '-' . $mytime->toDateTimeString(), function($excel) use($result, $requestType) {
                    $excel->sheet($requestType, function($sheet) use($result, $requestType) {
                        $sheet->loadView('TaxReport::taxreport', array('data' => $result, 'type' => $requestType));
                    });
                })->download('xls');
                Log::error("Export to excel is started");
            }

            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('TaxReport::taxreport', array('data' => $result, 'type' => $requestType));
                return $pdf->download('Tax Report-' . $requestType . '-' . $mytime->toDateTimeString() . ".pdf");
                Log::error("Export to excel is started");
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
