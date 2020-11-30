<?php

namespace App\Http\Controllers;

use View;
use Log;
use App\Central\Repositories\ReportsRepo;

class ReportApiController extends BaseController {

    public function __construct(ReportsRepo $reports) {
        $this->reports = $reports;
        parent::__construct();
    }
    
    public function testReport() {
//        $this->reports->sendReport();
        $order_details = $this->reports->getMyDashboardData();
        $order_details = json_decode(json_encode($order_details), true);
        return View::make('emails.OrderReportDashboard')->with(['order_details' => $order_details]);
    }

    public function index() {
        try {
            $data = \Input::all();
            $fromDate = date('Y-m-d');
            $datetime = new \DateTime('tomorrow');
            $toDate = $datetime->format('Y-m-d');
            if(!empty($data))
            {
                $filterDate = isset($data['filter_date']) ? $data['filter_date'] : '';
                if($filterDate != '')
                {
                    switch($filterDate)
                    {
                        case 'wtd':
                            $currentWeekSunday = strtotime("last sunday");
                            $sunday = date('w', $currentWeekSunday)==date('w') ? $currentWeekSunday + 7*86400 : $currentWeekSunday;
                            $lastSunday = date("Y-m-d",$sunday);
                            $fromDate = $lastSunday;
                            break;
                        case 'mtd':
                            $fromDate = date('Y-m-01');
                            break;
                        case 'ytd':
                            $fromDate = date('Y-01-01');
                            break;
                        default:
                            break;
                    }
                }
            }
            $result = $this->reports->getMyDashboardData(0, $fromDate, $toDate);
            $result['dashboard'] = json_decode(json_encode($result['dashboard']));
            $last_updated = $result['last_updated'];
            unset($result['last_updated']);

            if(!empty($data))
            {
                return json_encode(['order_details' => $result,'last_updated' => $last_updated]);
            }else{
                return View::make('welcome.hello')->with(array('order_details' => $result,'last_updated' => $last_updated));
            }            
        } catch (Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
        }
    }
}