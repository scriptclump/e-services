<?php 
namespace App\Modules\KPIReports\Controllers;
use App\Modules\KPIReports\Models\CommonDataModel;
use App\Modules\KPIReports\Models\ExpenseDataModel;

use App\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;

class ExpensesDetailsController extends BaseController{

	protected $commonController;
	protected $CommonDataModel;
	
	function __construct()
	{
		$this->commonController=new commonController();
		$this->CommonData=new CommonDataModel();
		$this->ExpenseData=new ExpenseDataModel();



	}
	function expensesDetails(){

		$breadCrumbs = array('HOME' => url('/'),'Reports' => '#', 'Expenses Details Report
		' => '#');
		parent::Breadcrumbs($breadCrumbs);
		$dcData=$this->commonController->getDcList();
		$hubData=$this->commonController->getTotalhubListData();
		$SOData=$this->commonController->getTotalSoData();
		$dchubData=$this->ExpenseData->expenseDcHubList();

		$dc_hub= json_encode($dchubData,true);



		return view('KPIReports::expensesDetailsReport',['DC_list' => $dc_hub]);
	}

	function expensesData(){
		$data = Input::all();
		$dc =isset($data['expense_dc'])?$data['expense_dc']:'NULL';
		$fromDate =isset($data['expense_from'])?$data['expense_from']:date("Y-m-d");
		$toDate =isset($data['expense_to'])?$data['expense_to']:date("Y-m-d");


		if($dc==""){
			$dc='NULL';
		}		
		if($fromDate==""){
			$fromDate=date("Y-m-d");
		}
		if($toDate==""){
			$toDate=date("Y-m-d");
		}

		$fromDate = strtotime($fromDate);
   		$fromDate = date("Y-m-d", $fromDate);
   		$toDate = strtotime($toDate);
   		$toDate = date("Y-m-d", $toDate);

		if(isset($data['expense_date_range']) && $data['expense_date_range']!= "expense_date_range"){
		   $date_type = $data['expense_date_range'];
		   $fromDate = $this->commonController->getFromDate("$date_type");
		   $toDate = date("Y-m-d");
		   if($date_type=='yesterday'){
		   		$toDate=$fromDate;
		   }
		 }

		$KpiExpenseData=$this->ExpenseData->getExpensesReportData($dc,$fromDate,$toDate);

		return $KpiExpenseData;
	}
}