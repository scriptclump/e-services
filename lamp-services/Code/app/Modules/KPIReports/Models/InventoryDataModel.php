<?php 
namespace App\Modules\KPIReports\Models;
use App\Modules\LegalEntities\Models\Legalentity;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;
use Log;
use \Exception;
use \Response;
class InventoryDataModel extends Model{
	public function __construct(){

	}
	public function getInventoryReportData($dc,$fromdate,$todate){
	
	
		$query= DB::selectFromWriteConnection(DB::raw("CALL getKPIInventoryAnalysisReport($dc,'$fromdate','$todate')"));
		

			if(count($query) > 0){
				return Response::json(array('status' => 'true', 'data' => $query ));
			}else{
				return Response::json(array('status' => 'false', 'data' => 'No Data Found !' ));
			}
					
	}
}
