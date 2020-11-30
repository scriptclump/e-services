<?php 
namespace App\Modules\KPIReports\Models;

use App\Modules\LegalEntities\Models\Legalentity;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;
use Log;
use  \Exception;
use \Response;
use Session;

class CommonDataModel extends Model{

	public function __construct(){

	}
	public function getDcListData(){

		$query="select lp_wh_name,le_wh_id from legalentity_warehouses where dc_type='118001' and status=1";
		$data = DB::select($query);
		if(count($data) > 0){

			//$data = json_decode(json_encode($data),true);
			return $data;

		}else{
			return false;
		}

	}
	public function getDcHubData(){
		$query="select hub_id,dc_id from dc_hub_mapping";
		$data = DB::select($query);
		if(count($data) > 0){

			return $data;

		}else{
			return false;
		}
	}
	public function gethubData(){
		$query = "select l.lp_wh_name,l.`le_wh_id`,d.`dc_id`,d.`hub_id` FROM dc_hub_mapping d INNER JOIN legalentity_warehouses l ON d.hub_id = l .le_wh_id";
		$data = DB::select($query);
		if(count($data) > 0){

			return $data;

		}else{
			return false;
		} 
	}
	public function getBeatdata(){
		$query = "select pjp_pincode_area_id,pjp_name,le_wh_id FROM pjp_pincode_area";
		$data = DB::select($query);
		if(count($data) > 0){
			return $data;

		}else{
			return false;
		} 
	}
	public function getTotalOutlet(){
		/*		$query = "select r.legal_entity_id,r.hub_id,r.business_legal_name,r.beat_id,d.`hub_id` AS hub ,d.`dc_id` FROM dc_hub_mapping d JOIN retailer_flat r ON d.`hub_id`=r.`hub_id`";*/
		$query="select * FROM (SELECT pjp_pincode_area_id, r.beat_id,p.`rm_id`, r.`business_legal_name`, 
				r.`legal_entity_id`,r.`hub_id` FROM pjp_pincode_area p JOIN retailer_flat r WHERE p.`pjp_pincode_area_id`= r.`beat_id`)AS innertbl  JOIN  dc_hub_mapping d ON d.`hub_id`=innertbl.hub_id";
		$data = DB::select($query);
		if(count($data) > 0){
			return $data;

		}else{
			return false;
		} 
	}
	public function getSoData(){
		$query="select p.`pjp_pincode_area_id`,u.`user_id`,p.le_wh_id,CONCAT(u.firstname,' ',u.lastname,'(',p.pjp_name,')')AS Fullname,p.`rm_id` FROM pjp_pincode_area p JOIN users u ON p.rm_id=u.`user_id`";
		$data = DB::select($query);
		if(count($data) > 0){
			return $data;

		}else{
			return false;
		} 

	}
	 public function getProductKpiData($dc,$hub,$beat,$outlet,$so,$fromDate,$toDate){


    	$query = DB::selectFromWriteConnection(DB::raw("CALL getKPIProductAnalysisReport($dc,$hub,$beat,$outlet,$so,'$fromDate','$toDate')"));

		if(count($query) > 0){
			return Response::json(array('status' => 'true', 'data' => $query ));
		}else{
			return Response::json(array('status' => 'false', 'data' => 'No Data Found !' ));
		}
    }


    public function newCustomerData($from,$to){

    	$id=Session::get('legal_entity_id');

    	$query = DB::selectFromWriteConnection(DB::raw("CALL getLegalEntitiesExportData('$from','$to','$id')"));

		
		if(count($query) > 0){
			return Response::json(array('status' => TRUE, 'data' => $query ));
		}else{
			return Response::json(array('status' => FALSE, 'data' => 'No data found !' ));
		}

    }

    public function getSalesTrends($dc,$hub,$userid,$reporttype,$fromdate,$todate,$periodTypeVal){


    	 $query = 'CALL getKPIReportsLogisticsData(' . $dc . ',' . $hub . ',' . $userid . ',"' . $reporttype . '","' . $fromdate . '","' . $todate . '",' . $periodTypeVal . ')';


       $query_res = json_decode(json_encode(DB::select(DB::raw($query))), true);




       return $query_res;
    }

    public function newSalesData($from,$to){

    	$data='NULL';


    	$query = DB::selectFromWriteConnection(DB::raw("CALL getFFReport(0,'$from','$to',1,'0','$data')"));

		
		if(count($query) > 0){
			return Response::json(array('status' => TRUE, 'data' => $query ));
		}else{
			return Response::json(array('status' => FALSE, 'data' => 'No data found !' ));
		}
	}

    public function getAllRetailersBySearch($queryString,$offset,$limit){
    	$query = '
    		SELECT 
    			legal_entity_id AS retailer_id,
    			business_legal_name AS retailer_name
    		FROM
    			legal_entities
    		WHERE
    			legal_entity_type_id LIKE "%30%"
    		';

    	if(empty($queryString) or $queryString == ''){
    		$query.= " limit $limit offset $offset";
    	}else{
    		$query.= " AND business_legal_name LIKE '%$queryString%' limit $limit offset $offset";
    	}
    	
    	$data = DB::SELECT($query);
    	
    	if(count($data)>0)	return $data;
    	return FALSE;
    }

    public function getAllOperationalOfficers($role_id){
        $query = 
            "SELECT
                user_roles.user_id AS 'UserId',
                CONCAT(users.firstname,' ',users.lastname) AS 'UserName',
                legalentity_warehouses.le_wh_id as 'WarehouseId'
            FROM
                user_roles
            JOIN users ON users.user_id = user_roles.user_id
            LEFT JOIN legalentity_warehouses ON legalentity_warehouses.bu_id = users.business_unit_id
            WHERE
                user_roles.role_id = ?
                AND users.is_active = ?";

        $result = DB::SELECT($query,[$role_id,1]);
        
        if(empty($result))
            return [];
        else
            return $result;
    }

    public function getFFSalesDataByPeriod($userid,$fromdate,$todate,$flagForChart,$periodTypeVal,$dc){



    	$queryFF = 'CALL getFFReport(' . $userid . ',"' . $fromdate . '","' . $todate . '","' . $flagForChart . '","' . $periodTypeVal . '","' . $dc . '")';

    	// $query_res = json_decode(json_encode(DB::selectFromWriteConnection(DB::raw($query))), true);


    	$query_resFF = json_decode(json_encode(DB::selectFromWriteConnection(DB::raw($queryFF))));


    	return $query_resFF;


    }

     public function getCollectionData($dc,$hub,$userid,$report,$fromdate,$todate,$periodtype){


    	$queryFF = 'CALL getKPIReportsHubOpsData( '.$dc.', '.$hub.','.$userid.',"'.$report.'","' . $fromdate . '","' . $todate . '",' . $periodtype . ')';


    	$query_resFF = json_decode(json_encode(DB::selectFromWriteConnection(DB::raw($queryFF))),true);
    	//print_r($query_resFF);exit;



    	return $query_resFF;
    }

    public function getSalesFFReport()
    {
      $query = DB::select(DB::raw('SELECT u.user_id,CONCAT(u.firstname," " ,u.lastname) AS FF_Name FROM users u JOIN user_roles r ON u.`user_id`=r.`user_id` WHERE r.`role_id` IN(52,53) ORDER BY FF_Name ASC'));
      return $query;
    }
    public function getProductKpiDataExport($dc,$hub,$beat,$outlet,$so,$fromDate,$toDate){


    	$query = DB::selectFromWriteConnection(DB::raw("CALL getKPIProductAnalysisReport($dc,$hub,$beat,$outlet,$so,'$fromDate','$toDate')"));

		
		return json_encode($query);
		
    }


}