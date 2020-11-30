<?php

namespace App\Modules\KPIReports\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Log;
use Illuminate\Support\Facades\Cache;

Class supplierModel extends Model {

    
	public function getSuppliersDb($dc,$fromDate,$toDate){
        
        $query = DB::select(DB::raw('CALL getKPISupplierAnalysisReport("'.$dc.'","'.$fromDate.'","'.$toDate.'")'));

        return $query;

    }

	public function getDcList(){
		$query="select lp_wh_name,le_wh_id from legalentity_warehouses where dc_type='118001'";
		$data = DB::select($query);

		if(count($data) > 0){
			return $data;
		}else{
			return false;
		} 
	}


}
