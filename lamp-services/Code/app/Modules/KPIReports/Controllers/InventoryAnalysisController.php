<?php
namespace App\Modules\KPIReports\Controllers;
use App\Modules\KPIReports\Models\CommonDataModel;
use App\Modules\KPIReports\Models\InventoryDataModel;
use App\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;
class InventoryAnalysisController  extends BaseController{
	protected $commonController;
	protected $CommonDataModel;
	protected $InventoryDataModel;
	
	function __construct()
	{
		$this->commonController=new commonController();
		$this->CommonData=new CommonDataModel();
		$this->inventoryData=new InventoryDataModel();

	}
	public function inventoryDetails(){
		$data = Input::all();
		$dc =isset($data['inventory_Dc'])?$data['inventory_Dc']:'NULL';
		$fromDate =isset($data['inventory_from'])?$data['inventory_from']:date("Y-m-d");
		$toDate =isset($data['inventory_to'])?$data['inventory_to']:date("Y-m-d");


		if($dc==""){
			$dc='NULL';
		}if($fromDate==""){
			$fromDate=date("Y-m-d");
		}
		if($toDate==""){
			$toDate=date("Y-m-d");
		}

		$fromDate = strtotime($fromDate);
   		$fromDate = date("Y-m-d", $fromDate);
   		$toDate = strtotime($toDate);
   		$toDate = date("Y-m-d", $toDate);
   		
		if(isset($data['inventory_date_range']) && $data['inventory_date_range']!= "inventory_date_range"){
		   $date_type = $data['inventory_date_range'];
		   $fromDate = $this->commonController->getFromDate("$date_type");
		   $toDate = date("Y-m-d");
		 }


		 $KpiInventoryData=$this->inventoryData->getInventoryReportData($dc,$fromDate,$toDate);

		return $KpiInventoryData;

	}
	public function inventoryData(){
		$breadCrumbs = array('HOME' => url('/'),'Reports' => '#', 'Inventory Analysis Report
		' => '#');
		parent::Breadcrumbs($breadCrumbs);
		$dcData=$this->commonController->getDcList();

		return view('KPIReports::inventoryAnalysisReport',['DC_list' => $dcData]);
	}
}