<?php 
namespace App\Modules\KPIReports\Models;

use App\Modules\LegalEntities\Models\Legalentity;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;
use Log;
use  \Exception;

use \Response;
class ExpenseDataModel extends Model{
	public function __construct(){

	}
	public function getExpensesReportData($dc,$fromdate,$todate){

		

		$query= DB::selectFromWriteConnection(DB::raw("CALL getKPIExpenseAnalysisReport($dc,'$fromdate','$todate')"));

			if(count($query) > 0){
				return Response::json(array('status' => 'true', 'data' => $query ));
			}else{
				return Response::json(array('status' => 'false', 'data' => 'No Data Found !' ));
			}
	}

	function expenseDcHubList(){
		$query="select bu_name,bu_id from business_units where  is_active=1 and bu_id NOT IN(2,3)";



		$data = DB::select($query);


		if(count($data) > 0){

			//$data = json_decode(json_encode($data),true);
			return $data;
			
		}else{
			return false;
		}


	}
}