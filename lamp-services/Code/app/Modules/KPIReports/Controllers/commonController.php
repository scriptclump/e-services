<?php 
namespace App\Modules\KPIReports\Controllers;

use App\Modules\KPIReports\Models\CommonDataModel;
use App\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;
class commonController extends BaseController{

	
	protected $CommonDataModel;
	function __construct()
	{
		$this->CommonDataModel=new CommonDataModel();
	}


	function getDcList(){

		$DcData = $this->CommonDataModel->getDcListData();

		return json_encode($DcData,true);
	}
	public function getTotalhubListData(){

		$hubData=$this->CommonDataModel->gethubData();

		return json_encode($hubData,true);
	}
	public function getTotalOutletData(){
		$outletdata=$this->CommonDataModel->getTotalOutlet();
		return json_encode($outletdata,true);
	}
	public function getTotalBeatData(){
		$beatData = $this->CommonDataModel->getBeatdata();
		return json_encode($beatData,true);
	}
	public function getTotalSoData(){
		$soData = $this->CommonDataModel->getSoData();
		return json_encode($soData,true);
	}
	public function getTotalDcHubData(){
		$dcHubData=$this->CommonDataModel->getDcHubData();
		return json_encode($dcHubData,true);
	}
	public function getSalesIds(){
		$salesIds=$this->CommonDataModel->getSalesFFReport();
		return $salesIds;
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
            case 'quarter':
                $fromDate = date('Y-01-01');
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

    public function getCollectionData(){

    	$data = Input::all();
    	$data=$data['data'];

    	$fromdate =isset($data['from_date'])?$data['from_date']:date("Y-m-d");
		$todate =isset($data['to_date'])?$data['to_date']:date("Y-m-d");
		$dc =isset($data['dc_id'])?$data['dc_id']:'NULL';
		$hub =isset($data['hub_id'])?$data['hub_id']:'NULL';
		$userid =isset($data['user_id'])?$data['user_id']:'NULL';
		$periodtype =$data['period_type'];
		$reporttype=$data['report_type'];

		$CollectionData = $this->CommonDataModel->getCollectionData($dc,$hub,$userid,$reporttype,$fromdate,$todate,$periodtype);

    	$resultArray = array();
        $key_name = '';
        $getHubInfoArray = array();
        $rs_hub_id = '';

        if($CollectionData!=""){
	        foreach ($CollectionData as $value) {
	                if (isset($getHubInfoArray[$value['inp_date']])) {
	                    $find = array_search($value['hub_name'], array_column($getHubInfoArray[$value['inp_date']], 'hub'));

	                    if ($find === false)
	                        $getHubInfoArray[$value['inp_date']][] = array("hub" => $value['hub_name'], "value_count" => $value['value'] ,"valuepct_count" => $value['value_pct']);
	                    else{
	                        $getHubInfoArray[$value['inp_date']][$find]["value_count"] += $value['value'];
	                        $getHubInfoArray[$value['inp_date']][$find]["valuepct_count"] += $value['value_pct'];
	                    }
	                } else
	                    $getHubInfoArray[$value['inp_date']][] = array("hub" => $value['hub_name'], "value_count" => $value['value'] ,"valuepct_count" => $value['value_pct']);
	            }

	            $dateArr = array();
	            foreach ($CollectionData as $value) {
	                if (isset($getHubInfoArray[$value['inp_date']])) {
	                    $find = array_search($value['hub_name'], array_column($getHubInfoArray[$value['inp_date']], 'hub'));
	                    if ($find !== false) {
	                        $getHubInfoArray[$value['inp_date']][$find]["values"][] = array($value['key_name'] => array("count" => $value['value'], "count_pct" => $value['value_pct'], 'tool_tip' => $value['tooltip']));
	                    }
	                }
	                if (!isset($dateArr[$value['inp_date']][$value['key_name']]['count'])) {
	                    $dateArr[$value['inp_date']][$value['key_name']]['count'] = $value['value'];
	                    $dateArr[$value['inp_date']][$value['key_name']]['count_pct'] = $value['value_pct'];
	                    $dateArr[$value['inp_date']][$value['key_name']]['tool_tip'] = $value['tooltip'];
	                } else {
	                    $dateArr[$value['inp_date']][$value['key_name']]['count'] += $value['value'];
	                    $dateArr[$value['inp_date']][$value['key_name']]['count_pct'] += $value['value_pct'];
	                    $dateArr[$value['inp_date']][$value['key_name']]['tool_tip'] = $value['tooltip'];
	                }
	            }

	            $dates = array_keys($getHubInfoArray);
	            $allTot = array();
	            foreach ($dates as $data) {
	                $grandTot = array_sum(array_column($getHubInfoArray[$data], 'value_count'));
	                $grandVal = array_sum(array_column($getHubInfoArray[$data], 'valuepct_count'));
	                $temp = array();
	                foreach ($dateArr[$data] as $key => $grandValue) {
	                    $temp[] = array($key => $grandValue);
	                    if (isset($allTot[$key])) {
	                        $allTot[$key]['count'] += $grandValue['count'];
	                        $allTot[$key]['count_pct'] += $grandValue['count_pct'];
	                    } else {
	                        $allTot[$key]['count'] = $grandValue['count'];
	                        $allTot[$key]['count_pct'] = $grandValue['count_pct'];
	                    }
	                }
	                $getHubInfoArray[$data][] = array("hub" => "Grand Total", "value_count" => $grandTot, "valuepct_count" => $grandVal, "values" => $temp);
	            }
	            $getHubInfoArray['Total'][] = $allTot;


	            //print_r($getHubInfoArray);exit;

	            return json_encode(array("status" => "Success", "message" => "Report Data.", "data" => $getHubInfoArray));   
        }else{

        		return json_encode(array("status" => "Failed", "message" => "Please provide valid inputs like dc_id, hub_id.", "data" => []));
        }
	}
}