<?php

namespace App\Modules\HubInventory\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Log;
use DB;
use App\Modules\HubInventory\Models\HubInventory;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;
use App\Modules\Roles\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Excel;
Class HubInventoryController extends BaseController
{
    public $hubs;

    
     public function __construct() {
        $this->middleware(function ($request, $next) {
            $role = new Role();
            $sbuData = json_decode($role->getFilterData(6),1);
            $sbu = isset($sbuData['sbu'])?$sbuData['sbu']:0;
            $hubsData = json_decode($sbu,1); 
            $this->hubs = isset($hubsData['118002'])?$hubsData['118002']:'';
            return $next($request);
        });
     }
    public function indexAction()
    {
        try
        {
            if (!Session::has('userId'))
            {
                return redirect()->to('/');
            }

            $roleRepo = new RoleRepo();
            $userId = Session::get('userId');
            $approveAccess = $roleRepo->checkActionAccess($userId, 'INV2001');
            $xlsAccess = $roleRepo->checkActionAccess($userId, 'INV2002');
            if (!$approveAccess)
            {
                return redirect()->to('/');
            }
            parent::Title('Hub Inventory - Ebutor');
            $breadCrumbs = array('Dashboard' => url('/'), 'Hub Inventory' => '#');
            parent::Breadcrumbs($breadCrumbs);
            return View::make('HubInventory::index',['xlsaccess'=>$xlsAccess,'hubs'=>$this->hubs]);
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getHubInventory()
    {
        try
        {
			Session::set('selectedHubIds','');
            $bu = (Input::get('bu'))?Input::get('bu'):$this->hubs;
			Session::put('selectedHubIds',$bu);
            $HubInventory = new HubInventory();
            $reports = $HubInventory->getHubInventory($bu);
            //print_r($reports); die;
            foreach ($reports as $report)
            {
                $report->mrp = number_format($report->mrp,3);      
                $report->total = ($report->sum_dit_qty + $report->sum_dnd_qty + $report->sum_hid_qty + $report->sum_ret_qty);
            }
            $report_result = json_encode(array('Records' => $reports));
            return $report_result;
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getHubOrderInventory(Request $request)
    {
        $bu = Session::get('selectedHubIds');
        $path = explode(':', $request->input('path'));
        $pid = $path[1];
        try
        {
            // = (Input::get('bu'))?Input::get('bu'):0;
            $HubInventory = new HubInventory();
            $reports = $HubInventory->getOrderItems($bu,$pid);
            
            foreach ($reports as $report)
            {  
                $report->total = ($report->hld_qty + $report->ret_qty + $report->dnd_qty + $report->dit_qty);
            }
            $report_result = json_encode(array('Records' => $reports));
            return $report_result;

        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function hubInventoryXls() {
        try {
            $bu = (Input::get('bu'))?Input::get('bu'):$this->hubs;
            
            if(strpos($bu,",")=== false)
            {
                $data = \DB::table('legalentity_warehouses')->where('le_wh_id',$bu)->pluck('lp_wh_name')->all();
                $bu_name = $data[0];
            }
            else
            {
             $bu_name = 'All Hubs';   
            }
	    $reportDate = Carbon::now(); 			
            $excel_reports = new HubInventory();
            $report_excel = $excel_reports->excelReports($bu);                        
            Excel::create('Hub_Inventory_'.$reportDate, function($excel) use($report_excel,$bu_name) {                        
                $excel->sheet('HubInventory', function($sheet) use($report_excel,$bu_name) {                        
                        $sheet->loadView('HubInventory::xls_reports')->with(['Reportinfo'=> $report_excel,'bu'=>$bu_name]);
                    });
        })->export('xls');        
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getHubs()
    {
        $hub = new HubInventory();
        $hubsArray = explode(',',$this->hubs);
        $hublist =  $hub->getHubs($hubsArray);
        return $hublist;
    }

}
