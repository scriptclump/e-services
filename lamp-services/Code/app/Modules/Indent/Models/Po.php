<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Po extends Model {

    public function getPoCountForAnIndent($indentId) {
        try {
                $sql = DB::table("po")->where("indent_id", $indentId)->count();
                return $sql;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getIndentStatus($indentId)
    {
    	try {
    		$sql = DB::table("indent")->where("indent_id", $indentId)->pluck("indent_status")->all();
                return $sql[0];
    		
    	} catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }



}
