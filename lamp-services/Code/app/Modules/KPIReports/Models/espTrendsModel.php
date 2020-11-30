<?php

namespace App\Modules\KPIReports\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Log;
use Illuminate\Support\Facades\Cache;

Class espTrendsModel extends Model {

    
	public function getespData($dc,$start_date,$end_date){
        
        $query = DB::selectFromWriteConnection(DB::raw("CALL getKPIESPTrends('$dc','$start_date','$end_date')"));

        return $query;
    }

	
}
