<?php

namespace App\Modules\InventoryStatusReports\Controllers;

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
use App\Modules\InventoryStatusReports\Models\InventoryStatusReports;
use App\Central\Repositories\RoleRepo;
Class InventoryStatusReportsController extends BaseController {

    public function indexAction() {
        try {
			if (!Session::has('userId')) {
				return redirect()->to('/');
			}
			
			$roleRepo = new RoleRepo();
			$userId = Session::get('userId');
			$approveAccess = $roleRepo->checkActionAccess($userId, 'ISR001');
			if(!$approveAccess)
			{
				return redirect()->to('/');
			}
			
            parent::Title('Inventory Status Reports - Ebutor');
            $breadCrumbs = array('Dashboard' => url('/'), 'Inventory Status Reports' => '#');
            parent::Breadcrumbs($breadCrumbs);
            return View::make('InventoryStatusReports::index');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getInventoryStatusReports(Request $request) {
        try {
            $inventory_reports = new InventoryStatusReports();
            $reports = $inventory_reports->getInventoryStatusReports();
            $i = 0;
			$finalReport = array();
            foreach ($reports as $report) {
                $reports[$i]->product_title = '<u><a href="/editproduct/' . $report->product_id . '">' . $report->product_title . '</a></u>';
				
				if($reports[$i]->is_sellable)
				{
					$reports[$i]->is_sellable = '<i class="fa fa-check" aria-hidden="true"></i>';
				}
				else
				{
					$reports[$i]->is_sellable = '<i class="fa fa-times" aria-hidden="true"></i>';
				}
				
				if($reports[$i]->cp_enabled)
				{
					$reports[$i]->cp_enabled = '<i class="fa fa-check" aria-hidden="true"></i>';
				}
				else
				{
					$reports[$i]->cp_enabled = '<i class="fa fa-times" aria-hidden="true"></i>';
				}				
                $i++;
            }
            $report_result = json_encode(array('Records' => $reports));
            return $report_result;           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function excelInventoryReports() {
        try {
            $reports = new InventoryStatusReports ();
            $report_exceldata = $reports->getInventoryStatusReports();
            Excel::create('InventoryStatusReports', function($excel) use($report_exceldata) {
                $excel->sheet('reportsData', function($sheet) use($report_exceldata) {
                    $sheet->loadView('InventoryStatusReports::excel_reports')->with('Reportinfo', $report_exceldata);
                    ;
                });
            })->export('xls');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
