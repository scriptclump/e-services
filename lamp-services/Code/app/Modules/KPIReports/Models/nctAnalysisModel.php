<?php

namespace App\Modules\KPIReports\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Log;
use Illuminate\Support\Facades\Cache;

Class nctAnalysisModel extends Model {

    
	public function getnctDetailsDb($dc,$hub_id,$beat_id,$outlet_id,$so_id,$start_date,$end_date){
        
        $query = DB::select(DB::raw("CALL getKPINCTTrackerReport($dc,$hub_id,$beat_id,$outlet_id,$so_id,'$start_date','$end_date')"));

        return $query;
    }

	
}
