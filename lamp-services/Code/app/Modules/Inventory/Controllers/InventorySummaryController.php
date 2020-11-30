<?php

namespace App\Modules\Inventory\Controllers;



use App\Http\Controllers\BaseController;
use App\Modules\Inventory\Models\InventorySummary;
use App\Central\Repositories\RoleRepo;
use Redirect;
use View;
use DB;
use Illuminate\Http\Request;
use Excel;
use DateTime;
use session;

class InventorySummaryController extends BaseController {
    
    public function __construct() {
        $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                $this->_inventorySummary = new InventorySummary();
                $this->_roleRepo = new RoleRepo();
                // $access = $this->_roleRepo->checkPermissionByFeatureCode('ISUM001');
                //     if(!$access) {
                //         Redirect::to('/')->send();
                //         die();
                //     }
                return $next($request);
            });
    }

    public function exportData(Request $request){
        $from_date = date('Y-m-d 00:00:00', strtotime($request->get('sum_start_date')));
        $to_date = date('Y-m-d 23:59:59', strtotime($request->get('sum_end_date')));
        $product_id  = $request->get('invSumProduct_id');     
        $dc_id  = $request->get('dc_name');     
        
        if($product_id=='' || (isset($product_id[0]) && $product_id[0] == "")) 
            $product_id = NULL;

        $details = json_decode(json_encode($this->_inventorySummary->generateReport($from_date,$to_date,$product_id,$dc_id)), true); 
             
        // Excel::create('Inventory Summary Report - '. date('Y-m-d'), function($excel) use($details) {
        //     $excel->sheet('Inventory Summary Report', function($sheet) use($details) {          
        //     $sheet->fromArray($details);
        //     });      
        // })->export('CSV');
    }

}

