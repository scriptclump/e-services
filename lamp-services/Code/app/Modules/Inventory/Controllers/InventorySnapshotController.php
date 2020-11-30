<?php

namespace App\Modules\Inventory\Controllers;



use App\Http\Controllers\BaseController;
use App\Modules\Inventory\Models\InventorySnapshot;
use App\Central\Repositories\RoleRepo;
use Redirect;
use View;
use DB;
use Illuminate\Http\Request;
use Excel;
use DateTime;
use Session;

class InventorySnapshotController extends BaseController {
    
    public function __construct() {
        $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                $this->_inventorySnp = new InventorySnapshot();
                $this->_roleRepo = new RoleRepo();
                $access = $this->_roleRepo->checkPermissionByFeatureCode('IS001');
                    if(!$access) {
                        Redirect::to('/')->send();
                        die();
                    }
            return $next($request);
        });
    }

    public function exportData(Request $request){
        $userId = Session::get('userId');
        $from_date = date('Y-m-d 00:00:00', strtotime($request->get('fdate')));
        $to_date = date('Y-m-d 23:59:59', strtotime($request->get('tdate')));
        $product_id  = $request->get('invproduct_id');   
        $dc_id  = $request->get('snp_dc_id');
        if($product_id=='') 
        $product_id = 'NULL'; 
        $details = json_decode(json_encode($this->_inventorySnp->generateReport($from_date,$to_date,$product_id,$dc_id,$userId)), true);      
    }
    public function opencloseSnapshot(Request $request){
        $data = $request->input();
        $fromDate = (isset($data['from_date']) && $data['from_date']!='') ? $data['from_date'] : date('m/Y');
        $time = explode('/',$fromDate);
        $month = $time[0];
        $year = $time[1];
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $fromDate =$year.'-'.$month.'-'.'01';
        $toDate =$year.'-'.$month.'-'.$days;
        $date = date('m');
        if($date == $month)
            $toDate = date('Y-m-d');
        $dc_id = $request->get('snp_new_dc_id');
        if($dc_id == 0)
            $dc_id = 'NULL';
        $details = json_decode(json_encode($this->_inventorySnp->opencloseReport($dc_id,$fromDate,$toDate)), true);
    }
    public function cycleCountReport(Request $request)
    {
        $fromDate = date('Y-m-d 00:00:00', strtotime($request->get('fdate')));
        $toDate = date('Y-m-d 23:59:59', strtotime($request->get('tdate')));
        $dc_id  = $request->input('cyc_dc_id');
        $details = json_decode(json_encode($this->_inventorySnp->generateCycleCountReport($fromDate,$toDate,$dc_id)), true);

        Excel::create('Cycle Count Report - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Cycle Count Report', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
    }
}

