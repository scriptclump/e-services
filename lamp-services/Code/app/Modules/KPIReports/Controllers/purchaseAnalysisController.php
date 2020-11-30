<?php 
namespace App\Modules\KPIReports\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\KPIReports\Models\purchasereturnsModel;
use App\Modules\KPIReports\Models\CommonDataModel;
use App\Central\Repositories\RoleRepo;

use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;
use Illuminate\Http\Request;
use Excel;
date_default_timezone_set("Asia/Kolkata"); 

class purchaseAnalysisController extends BaseController{
	
	function __construct(){

		$this->commonController=new commonController();
		$this->_KpiModel=new purchasereturnsModel();
		$this->_commonDataModel=new CommonDataModel();
		$this->_RoleAccess = new RoleRepo();

	}
	

	public function purchaseAnalysis(){

		$breadCrumbs = array('HOME' => url('/'),'Pricing' => '#',trans('headings.LP').''.'Trends ' => '#');
		parent::Breadcrumbs($breadCrumbs);
		$dcData=$this->commonController->getDcList();
        $elpAccess = $this->_RoleAccess->checkPermissionByFeatureCode('ELP001');
        
     	return view('KPIReports::purchaseReturns',['DC_list' => $dcData,'elpAccess'=>$elpAccess]);

	}

	public function getpurchaseReturnsData(Request $request){
		$post_data = $request->input();
		$end_date = isset($post_data['end_date']) ? $post_data['end_date'] : date('Y-m-d');
		$start_date = isset($post_data['start_date']) ? $post_data['start_date'] : date('Y-m-d');
		$dc = isset($post_data['dc']) ? $post_data['dc'] : 'NULL';

		if($end_date == ""){
			$end_date = date('Y-m-d');
		}else{
			$start_date = strtotime($start_date);
			$start_date = date("Y-m-d", $start_date);
		}
		
		if($start_date == ""){
			$start_date = date('Y-m-d');	
		}else{
			$end_date = strtotime($end_date);
			$end_date = date("Y-m-d", $end_date);
		}
		
		if(isset($post_data['date_range']) && $post_data['date_range']!= "date_range"){
			$custom_date = $post_data['date_range'];
			$start_date = $this->commonController->getFromDate("$custom_date");
			$end_date = date("Y-m-d");
			if ($custom_date == "yesterday") {
				# code...
				$end_date = $start_date;
			}
		}

		$data = $this->_KpiModel->getpurchaseData($dc,$start_date,$end_date);
		if(count($data) <= 0){
			$data = 'No Data Found !';
			$status = "false";
		}else{
			$data = $data;
			$status = "true";
		}

		return Response::json(array('status' => $status, 'data' => $data ));

	}

	public function getelpExcel(Request $request){
		$post_data = $request->input();
		$end_date = isset($post_data['end_date']) ? $post_data['end_date'] : date('Y-m-d');
		$start_date = isset($post_data['start_date']) ? $post_data['start_date'] : date('Y-m-d');
		$dc = isset($post_data['kpi_dc']) ? $post_data['kpi_dc'] : 'NULL';

		if($end_date == ""){
			$end_date = date('Y-m-d');
		}else{
			$start_date = strtotime($start_date);
			$start_date = date("Y-m-d", $start_date);
		}
		
		if($start_date == ""){
			$start_date = date('Y-m-d');	
		}else{
			$end_date = strtotime($end_date);
			$end_date = date("Y-m-d", $end_date);
		}

		if(isset($post_data['date_range']) && $post_data['date_range']!= "date_range"){
			$custom_date = $post_data['date_range'];
			$start_date = $this->commonController->getFromDate("$custom_date");
			$end_date = date("Y-m-d");
			if ($custom_date == "yesterday") {
				# code...
				$end_date = $start_date;
			}

		}

		$details = json_decode(json_encode($this->_KpiModel->getpurchaseData($dc,$start_date,$end_date)), true);
		Excel::create('ELP Trends - '. date('Y-m-d h:i:s'), function($excel) use($details) {
            $excel->sheet('ELP Trends', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
	}


}