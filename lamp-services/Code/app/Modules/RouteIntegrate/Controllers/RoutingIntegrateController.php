<?php
namespace App\Modules\RouteIntegrate\Controllers;
date_default_timezone_set("Asia/Kolkata");

use App\Http\Controllers\BaseController;
use View;
use Log;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use App\Modules\ScheduleJobs\Models\InventoryReport;
use App\Modules\ScheduleJobs\Models\InventorySnapshot;
use Illuminate\Support\Facades\Config;

class RoutingIntegrateController extends BaseController {
    
    

    public function index() {

    return View::make('RouteIntegrate::index');

        
    }
}