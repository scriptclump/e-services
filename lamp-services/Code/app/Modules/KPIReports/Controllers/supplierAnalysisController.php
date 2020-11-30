<?php 
namespace App\Modules\KPIReports\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\KPIReports\Models\supplierModel;

use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;
use Illuminate\Http\Request;
class supplierAnalysisController extends BaseController{

	
	public function __construct(){
		$this->_KpiModel = new supplierModel();

	}

	public function supplierAnalysis(){	
		
		$breadCrumbs = array('HOME' => url('/'),'Reports' => '#', 'Supplier Analysis Report' => '#');
		parent::Breadcrumbs($breadCrumbs);
		$dcData=json_encode($this->_KpiModel->getDcList());
		return view('KPIReports::supplierAnalysis',["dc_list"=>$dcData]);
	}


	public function getSuppliersData(Request $request){
		
		$post_data = $request->input();

		$toDate = $post_data['to_date'];
		$start_date = $post_data['start_date'];
		$dc = $post_data['dc'];
		if(isset($post_data['custom_date']) && $post_data['custom_date']!= "custom_date"){
			$custom_date = $post_data['custom_date'];
			$start_date = $this->getFromDate("$custom_date");
			$toDate = date("Y-m-d");

		}
		$data = $this->_KpiModel->getSuppliersDb($dc,$start_date,$toDate);
		if(count($data) <= 0){
			$data = 'No Data Found !';
			$status = "false";
		}else{
			$data = $data;
			$status = "true";
		}

		return Response::json(array('status' => $status, 'data' => $data ));

	}

	public function getFromDate($filterDate=''){
        $fromDate = date('Y-m-d');
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
            case 'yesterday':
	            $fromDate = date('Y-m-d', time()-86400);
	            break;
	        case 'std':
	            $fromDate = date('Y-m-d', -1);
	            break;
            default:
                $fromDate = date('Y-m-d');
                break;
        }
        return $fromDate;
    }


	
}