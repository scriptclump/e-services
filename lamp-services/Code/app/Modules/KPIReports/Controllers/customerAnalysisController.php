<?php 
namespace App\Modules\KPIReports\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\KPIReports\Models\customerModel;

use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;
use Illuminate\Http\Request;
class customerAnalysisController extends BaseController{
	
	function __construct()
	{
		$this->commonController=new commonController();
		$this->_KpiModel=new customerModel();
	}
	

	public function customerAnalysis(){

		$breadCrumbs = array('HOME' => url('/'), 'Customer Trends' => '#');
		parent::Breadcrumbs($breadCrumbs);
		$dcData=$this->commonController->getDcList();
		$hubData=$this->commonController->getTotalhubListData();
		$beatData=$this->commonController->getTotalBeatData();
		$outletdata=$this->commonController->getTotalOutletData();
		$SOData=$this->commonController->getTotalSoData();
		$dc_hubData=$this->commonController->getTotalDcHubData();

		$DC_list=array();

     	return view('KPIReports::customerAnalysis',['DC_list' => $dcData,'HUB_list'=>$hubData,"Beat_List"=>$beatData,"outlet_List"=>$outletdata,"so_List"=>$SOData,"dc_hub_data"=>$dc_hubData]);

	}

	public function getCustomersData(Request $request){
		$post_data = $request->input();
		$end_date = isset($post_data['end_date']) ? $post_data['end_date'] : date('Y-m-d');
		$start_date = isset($post_data['start_date']) ? $post_data['start_date'] : date('Y-m-d');
		$dc = isset($post_data['dc']) ? $post_data['dc'] : 'NULL';
		$hub_id = isset($post_data['hub_id']) ? $post_data['hub_id'] : 'NULL';
		$beat_id = isset($post_data['beat_id']) ? $post_data['beat_id'] : 'NULL';
		$so_id = isset($post_data['so_id']) ? $post_data['so_id'] : 'NULL';
		$outlet_id = isset($post_data['outlet_id']) ? $post_data['outlet_id'] : 'NULL';
		
		if($hub_id == "")
			$hub_id = 'NULL';
		if($dc == "")
			$dc = 'NULL';
		if($beat_id == "")
			$beat_id = 'NULL';
		if($so_id == "")
			$so_id = 'NULL';
		if($outlet_id == "")
			$outlet_id = 'NULL';
		if($end_date == "")
			$end_date = date('Y-m-d');
		if($start_date == "")
			$start_date = date('Y-m-d');	
				
		if(isset($post_data['date_range']) && $post_data['date_range']!= "date_range"){
			$custom_date = $post_data['date_range'];
			$start_date = $this->commonController->getFromDate("$custom_date");
			$end_date = date("Y-m-d");

		}
		$data = $this->_KpiModel->getcustomersDb($dc,$hub_id,$beat_id,$outlet_id,$so_id,$start_date,$end_date);
		if(count($data) <= 0){
			$data = 'No Data Found !';
			$status = "false";
		}else{
			$data = $data;
			$status = "true";
		}

		return Response::json(array('status' => $status, 'data' => $data ));

	}


}