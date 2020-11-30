<?php
namespace App\Modules\Dashboard\Controllers;

use DB;
use Log;
use View;
use Cache;
use Input;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\ReportsRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\Dashboard\Controllers\DashboardController;

class MFCDashboardController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                     Redirect::to('/login')->send();
            }
            $this->reports = new ReportsRepo();
            $this->roleAccess = new RoleRepo();
            $this->dashboard = new DashboardController();

            // Date Objects of Tommorow and Date
            $this->tomorrow = new \DateTime('tomorrow');
            $this->yesterday = new \DateTime('yesterday');

            // Logged In User Legal Entity Id
            $this->legal_entity_id = \Session::get('legal_entity_id');
         return $next($request);
        });
    }

    public function indexAction() {
        try {
            $result = $this->getDashboardData();
//            Log::info($result);
            $last_updated = isset($result['last_updated'])?$result['last_updated']:date('Y-m-d h:i a');
            unset($result['last_updated']);
            //dd($result);
            if(!empty($data)){
                return json_encode(['order_details' => $result,'last_updated' => $last_updated]);
            }else{
                return View('Dashboard::mfcindex')->with(['order_details' => $result,'last_updated' => $last_updated]);
            }
            //return View('Dashboard::hello')->with(['order_details' => $result]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }

    public function getDashboardData($data = null)
    {
        $datesArr = $this->dashboard->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];

        // Code to Check weather the User has TGM Access or not
        $checkTGMAccess = $this->roleAccess->checkPermissionByFeatureCode('USRTGM01');
        $flag = ($checkTGMAccess)?4:1;

        /* getMFCDashboard_web
        * - mfc_legal_entity_id => MFC Legal Id
        * - start_date => Start Date of the result
        * - end_date => End Date of the result
        */
        $query = "CALL getMFCDashboard_web(?,?,?)";
        $result = DB::select($query,[$this->legal_entity_id,$fromDate,$toDate]);
        return $result;
    }
    
    public function getIndexData() {
        try {
            $data = Input::all();            
            if(!empty($data))
            {
                $result = $this->getDashboardData($data);
                $last_updated = isset($result['last_updated'])?$result['last_updated']:date('Y-m-d h:i a');
                unset($result['last_updated']);
                
                return json_encode(['order_details' => $result,'last_updated' => $last_updated]);
            }
            // If the Data is invalid, then it will be routed to GET method!
            return $this->indexAction();           
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something went wrong. Please contact the Admin or check the logs";
        }
    }
}