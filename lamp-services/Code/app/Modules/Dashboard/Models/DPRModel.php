<?php

namespace App\Modules\Dashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Carbon\Carbon;
use Log;

class DPRModel extends Model {

    public function getDprData($whid,$fdate,$tdate,$flag,$dc_fc_flag) {
    	try{
    		
    		return DB::selectFromWriteConnection(DB::raw("CALL getDPRSheet($whid,'$fdate','$tdate',$flag,$dc_fc_flag)"));


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return false;
        }

    }	
}
