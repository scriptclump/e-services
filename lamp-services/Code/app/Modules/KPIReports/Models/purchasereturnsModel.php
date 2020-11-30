<?php

namespace App\Modules\KPIReports\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Log;
use Illuminate\Support\Facades\Cache;

Class purchasereturnsModel extends Model {

    
	public function getpurchaseData($dc,$start_date,$end_date){
        
        $query = DB::selectFromWriteConnection(DB::raw("CALL getKPIELPTrends($dc,'$start_date','$end_date')"));

        return $query;
    }

	
}
