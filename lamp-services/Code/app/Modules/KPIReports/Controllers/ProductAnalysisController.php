<?php 
namespace App\Modules\KPIReports\Controllers;
use App\Modules\KPIReports\Models\CommonDataModel;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;
use Excel;
use Carbon\Carbon;
class ProductAnalysisController extends BaseController{

	protected $commonController;
	protected $CommonDataModel;
	
	function __construct()
	{
		$this->commonController=new commonController();
		$this->CommonData=new CommonDataModel();

	}
	public function productSummary(){
		$data = Input::all();

		$dc =isset($data['product_dc'])?$data['product_dc']:'NULL';
		$hub =isset($data['product_hub'])?$data['product_hub']:'NULL';
		$beat =isset($data['product_beat'])?$data['product_beat']:'NULL';
/*		$outlet =isset($data['product_outlet'])?$data['product_outlet']:'NULL';*/
	$so =isset($data['product_so'])?$data['product_so']:'NULL';	
		$fromDate =isset($data['product_from'])?$data['product_from']:date("Y-m-d");
		$toDate =isset($data['product_to'])?$data['product_to']:date("Y-m-d");

		if($hub==""){
			$hub='NULL';
		}
		if($dc==""){
			$dc='NULL';
		}
		if($beat==""){
			$beat='NULL';
		}
		/*if($outlet==""){
			$outlet='NULL';
		}*/
		if($so==""){
			$so='NULL';
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

		if(isset($data['date_range']) && $data['date_range']!= "date_range"){
		   $date_type = $data['date_range'];
		   $fromDate = $this->commonController->getFromDate("$date_type");
		   $toDate = date("Y-m-d");
		   if($date_type=='yesterday'){
				$toDate = $fromDate;
	 
			}
		 }
		 $KpiData=$this->CommonData->getProductKpiData($dc,$hub,$beat,'NULL',$so,$fromDate,$toDate);

		return $KpiData;
		
	}

	public function productAnalysis()
	{

		$breadCrumbs = array('HOME' => url('/'),'Reports' => '#', 'Product Analysis Report
		' => '#');
		parent::Breadcrumbs($breadCrumbs);
		$dcData=$this->commonController->getDcList();
		$hubData=$this->commonController->getTotalhubListData();
		$beatData=$this->commonController->getTotalBeatData();
		$outletdata=$this->commonController->getTotalOutletData();
		$SOData=$this->commonController->getTotalSoData();
		$dc_hubData=$this->commonController->getTotalDcHubData();
		$salesData=$this->commonController->getSalesIds();
		// print_r($salesData);die();
		$DC_list=array();
     	return view('KPIReports::productAnalysis',['DC_list' => $dcData,'HUB_list'=>$hubData,"Beat_List"=>$beatData,"outlet_List"=>$outletdata,"so_List"=>$SOData,"dc_hub_data"=>$dc_hubData,"salesData"=>$salesData]);
	}

	public function getNewCustomers(){

		$data=Input::all();
 		$fromDate =isset($data['from_date'])?$data['from_date']:date("Y-m-d");
		$toDate =isset($data['to_date'])?$data['to_date']:date("Y-m-d");

		$date_type = $data['date'];
		$fromDate = strtotime($fromDate);
   		$fromDate = date("Y-m-d", $fromDate);
   		$toDate = strtotime($toDate);
   		$toDate = date("Y-m-d", $toDate);


		if($date_type!='Customer_date_range'){

	        $fromDate = $this->commonController->getFromDate("$date_type");       
	        $toDate = date("Y-m-d");
	        if($date_type=='yesterday'){
	        	$toDate = $fromDate;
	        }
	    }

   		$KpiData=$this->CommonData->newCustomerData($fromDate,$toDate);


   		return $KpiData;

	}

	 public function getTodayFFUsersList()
    {
        $data = Input::all();

        $fromDate =isset($data['from_date'])?$data['from_date']:date("Y-m-d");
		$toDate =isset($data['to_date'])?$data['to_date']:date("Y-m-d");

        $date_type = $data['date'];
        $fromDate = strtotime($fromDate);
   		$fromDate = date("Y-m-d", $fromDate);
   		$toDate = strtotime($toDate);
   		$toDate = date("Y-m-d", $toDate);

        if($date_type!='sales_date_range'){

	        $fromDate = $this->commonController->getFromDate("$date_type");       
	        $toDate = date("Y-m-d");
	        if($date_type=='yesterday'){
	        	$toDate = $fromDate;
	        }
	    }
       	$KpiData=$this->CommonData->newSalesData($fromDate,$toDate);

       	return $KpiData;

        
      
    }

	public function getSalesTrendsData(){


		$data = Input::all();



		$dc =isset($data['chart_dc'])?$data['chart_dc']:'NULL';

		$hub =isset($data['chart_hub'])?$data['chart_hub']:'NULL';

		$fromdate =isset($data['chart_from'])?$data['chart_from']:date("Y-m-d");

		$todate =isset($data['chart_to'])?$data['chart_to']:date("Y-m-d");

		$diff=$data['diff'];

		if($hub==""){
			$hub='NULL';
		}
		if($dc==""){
			$dc='NULL';
		}
		if($fromdate==""){
			$fromdate=date("Y-m-d");
		}
		if($todate==""){
			$todate=date("Y-m-d");
		}	
		$periodTypeVal=1;
		if(isset($data['date_range'])){
			if($data['date_range']=='wtd'){
				$periodTypeVal = 2;

			}if($data['date_range']=='mtd'||$data['date_range']=='yesterday'||$data['date_range']=='today'){
				$periodTypeVal = 1;

			}if($data['date_range']=='quarter'){
				$periodTypeVal = 4;

			}if($data['date_range']=='ytd'){
				$periodTypeVal = 3;

			}
		}

		if(isset($data['date_range']) && $data['date_range']!= "chart_date_range"){
		   $date_type = $data['date_range'];
		   $fromdate = $this->commonController->getFromDate("$date_type");		  
		   $todate = date("Y-m-d");


		   if($date_type=='yesterday'){
		   		$todate=$fromdate;	
		   }
		}

		if($data['date_range']=="chart_date_range" && $diff>0){

			if($diff < 7){
	        	$periodTypeVal = 2;
	       	}else if($diff <= 31){
	       		$periodTypeVal = 1;
	       	}else if($diff > 31){
	        	$periodTypeVal = 3;
	       	}


		}

		$reporttype='getKPITotalSalesTrends';
		$userid=0;

		$resultQuery =$this->CommonData->getSalesTrends($dc,$hub,$userid,$reporttype,$fromdate,$todate,$periodTypeVal);

		$resultArray = array();
        $key_name = '';
        $getHubInfoArray = array();
        $rs_hub_id = '';

        if($resultQuery!=""){
	        foreach ($resultQuery as $value) {
	                if (isset($getHubInfoArray[$value['inp_date']])) {
	                    $find = array_search($value['hub_name'], array_column($getHubInfoArray[$value['inp_date']], 'hub'));

	                    if ($find === false)
	                        $getHubInfoArray[$value['inp_date']][] = array("hub" => $value['hub_name'], "order_count" => $value['count_no'] ,"order_value" => $value['value_tot']);
	                    else{
	                        $getHubInfoArray[$value['inp_date']][$find]["order_count"] += $value['count_no'];
	                        $getHubInfoArray[$value['inp_date']][$find]["order_value"] += $value['value_tot'];
	                    }
	                } else
	                    $getHubInfoArray[$value['inp_date']][] = array("hub" => $value['hub_name'], "order_count" => $value['count_no'] ,"order_value" => $value['value_tot']);
	            }

	            $dateArr = array();
	            foreach ($resultQuery as $value) {
	                if (isset($getHubInfoArray[$value['inp_date']])) {
	                    $find = array_search($value['hub_name'], array_column($getHubInfoArray[$value['inp_date']], 'hub'));
	                    if ($find !== false) {
	                        $getHubInfoArray[$value['inp_date']][$find]["values"][] = array($value['key_name'] => array("count" => $value['count_no'], "count_value" => $value['value_tot'], 'tool_tip' => $value['tooltip']));
	                    }
	                }
	                if (!isset($dateArr[$value['inp_date']][$value['key_name']]['count'])) {
	                    $dateArr[$value['inp_date']][$value['key_name']]['count'] = $value['count_no'];
	                    $dateArr[$value['inp_date']][$value['key_name']]['count_value'] = $value['value_tot'];
	                    $dateArr[$value['inp_date']][$value['key_name']]['tool_tip'] = $value['tooltip'];
	                } else {
	                    $dateArr[$value['inp_date']][$value['key_name']]['count'] += $value['count_no'];
	                    $dateArr[$value['inp_date']][$value['key_name']]['count_value'] += $value['value_tot'];
	                    $dateArr[$value['inp_date']][$value['key_name']]['tool_tip'] = $value['tooltip'];
	                }
	            }

	            $dates = array_keys($getHubInfoArray);
	            $allTot = array();
	            foreach ($dates as $data) {
	                $grandTot = array_sum(array_column($getHubInfoArray[$data], 'order_count'));
	                $grandVal = array_sum(array_column($getHubInfoArray[$data], 'order_value'));
	                $temp = array();
	                foreach ($dateArr[$data] as $key => $grandValue) {
	                    $temp[] = array($key => $grandValue);
	                    if (isset($allTot[$key])) {
	                        $allTot[$key]['count'] += $grandValue['count'];
	                        $allTot[$key]['count_value'] += $grandValue['count_value'];
	                    } else {
	                        $allTot[$key]['count'] = $grandValue['count'];
	                        $allTot[$key]['count_value'] = $grandValue['count_value'];
	                    }
	                }
	                $getHubInfoArray[$data][] = array("hub" => "Grand Total", "order_count" => $grandTot, "order_value" => $grandVal, "values" => $temp);
	            }
	            $getHubInfoArray['Total'][] = $allTot;

	            return json_encode(array("status" => "Success", "message" => "Report Data.", "data" => $getHubInfoArray));   
        }else{

        		return json_encode(array("status" => "Failed", "message" => "Please provide valid inputs like dc_id, hub_id.", "data" => []));
        }



	}

	public function getFFDataByPeriod(Request $request){

		$data=Input::all();

		$dc =isset($data['dc'])?$data['dc']:'NULL';

		$fromdate =isset($data['chart_from'])?$data['chart_from']:date("Y-m-d");

		$todate =isset($data['chart_to'])?$data['chart_to']:date("Y-m-d");

		$diff=$data['diff'];

		$salesId=[];

		$salesId= $data['ff_id'];


        $ffId = implode(",",$salesId);
        $ffId = '"'.$ffId.'"';

		if($dc==""){
			$dc='NULL';
		}
		if($fromdate==""){
			$fromdate=date("Y-m-d");
		}
		if($todate==""){
			$todate=date("Y-m-d");
		}	
		$periodTypeVal=1;
		if(isset($data['date_range'])){
			if($data['date_range']=='wtd'){
				$periodTypeVal = 2;

			}if($data['date_range']=='mtd'||$data['date_range']=='yesterday'||$data['date_range']=='today'){
				$periodTypeVal = 1;

			}if($data['date_range']=='quarter'){
				$periodTypeVal = 4;

			}if($data['date_range']=='ytd'){
				$periodTypeVal = 3;

			}
		}
		if(isset($data['date_range']) && $data['date_range']!= "sales_date_range"){
		   $date_type = $data['date_range'];
		   $fromdate = $this->commonController->getFromDate("$date_type");		  
		   $todate = date("Y-m-d");
		   if($date_type=='yesterday'){
		   		$todate=$fromdate;	
		   }
		}

		if($data['date_range']=="sales_date_range" && $diff>0){

			if($diff < 7){
	        	$periodTypeVal = 2;
	       	}else if($diff <= 31){
	       		$periodTypeVal = 1;
	       	}else if($diff > 31){
	        	$periodTypeVal = 3;
	       	}


		}
		$userid=0;
		$flagForChart=2;
		$temp=1;
		$fromdate = date('Y-m-d',strtotime($fromdate));
		$todate = date('Y-m-d',strtotime($todate));
		$resultQuery=$this->CommonData->getFFSalesDataByPeriod($ffId,$fromdate,$todate,$flagForChart,$periodTypeVal,$dc);
				$salesId=[];


		if($resultQuery!=""){


			$resultQuery = json_decode(json_encode($resultQuery),true);

			/*
			0] => stdClass Object
	        (
	            [Name] => Bhoomaiah Bhusani
	            [Date] => 0000-00-00
	            [TBV] => 0
	            [Orders Count] => 0
	            [Calls Count] => 0
	            [UOB] => 0
	            [ABV] => 0
	            [TLC] => 0
	            [ULC] => 0
	            [ALC] => 0
	            [Cancel Order Count] => 0
	            [Cancel Order Value] => 0
	            [Return Order Count] => 0
	            [Return Order Value] => 0.0000
	        )
			*/

			$final = array();
			foreach($resultQuery as $result){
				$temp = array();
				$temp['Label']='TBV';
				$temp['name'] = $result['Name'];
				$temp['TBV'] = $result['TBV'];
				$value = array();
				foreach($result as $key=>$resData){
					if($key!= 'Name' && $key!= 'Date' && $key!= 'TBV'&& $key!= 'Log_Date'){
						$tmp = array();
						$tmp[$key] = array('count'=>$resData);
						$value[] = $tmp;
					}
				}
				$temp['values'] = $value;
				$final[$result['Log_Date']][] = $temp;
			}

			return json_encode(array("status" => "Success", "message" => "Report Data.", "data" => $final)); 
		}else{
			return json_encode(array("status" => "Failed", "message" => "Please provide valid inputs like dc_id.", "data" => []));

		}



	}

	public function productExport(){

		$data = Input::all();
		$dc =isset($data['product_dc'])?$data['product_dc']:'NULL';
		$hub =isset($data['product_hub'])?$data['product_hub']:'NULL';
		$beat =isset($data['product_beat'])?$data['product_beat']:'NULL';
	    $so =isset($data['product_so'])?$data['product_so']:'NULL';	
		$fromDate =isset($data['start_date'])?$data['start_date']:date("Y-m-d");
		$toDate =isset($data['end_date'])?$data['end_date']:date("Y-m-d");

		if($hub==""){
			$hub='NULL';
		}
		if($dc==""){
			$dc='NULL';
		}
		if($beat==""){
			$beat='NULL';
		}
		if($so==""){
			$so='NULL';
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

		if(isset($data['date_range']) && $data['date_range']!= "date_range"){
		   $date_type = $data['date_range'];
		   $fromDate = $this->commonController->getFromDate("$date_type");
		   $toDate = date("Y-m-d");
		   if($date_type=='yesterday'){
				$toDate = $fromDate;
	 
			}
		 }

		 $KpiData=$this->CommonData->getProductKpiDataExport($dc,$hub,$beat,'NULL',$so,$fromDate,$toDate);
		
		 $headers=array('Product ID',"Product Name","SKU","MRP","Category ID","Category Name","SOH","Available CFC","DIT","Missing","CP Enabled","KVI","SubCategory","CFC Sold","Total Orders","TBV","TBV Contrib","TGM","TGM Contrib","Inventory stock","Color","Brand ID","Brand Name","Manufacture ID","Manufacture Name","Is sellable","Hub Name ","Dc Name","Order Date");
		 $KpiData=json_decode($KpiData);
		 $downloadedtime=Carbon::now();
		 
		 Excel::create('ProductAnalysisSheet-'.$downloadedtime->toDateTimeString(),function($excel) use($headers,$KpiData){

		 	$excel->sheet('Product Analysis',function($sheet) use($headers,$KpiData){

		 		$sheet->loadView("KPIReports::ProductExcel",array('headers'=>$headers,'data'=>$KpiData));
		 	});
		 })->export('xlsx');

		 

	}


}