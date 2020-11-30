<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class GdsBusinessUnit extends Model
{
    public $timestamps = false;

    public function getBusinesUnitLeWhId($leWhId, $fields=array('bu.*','wh.state')) {
        try {
            $query = DB::table('business_units as bu')->select($fields);
            $query->join('legalentity_warehouses as wh','bu.bu_id','=','wh.bu_id');
            $query->where('wh.le_wh_id', $leWhId);
            return $query->first();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getBusinesUnitByParentId($parentId, $fields=array('bu.*')) {
        try {
            $query = DB::table('business_units as bu')->select($fields);
            $query->where('bu.bu_id', $parentId);
            return $query->first();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getAllBusinesUnits($fields=array('bu.*')) {
        try {
            $query = DB::table('business_units as bu')->select($fields);
            $query->where('bu.is_active', 1);
            $query->where('bu.parent_bu_id','!=', 0);
            $query->orderBy('bu.parent_bu_id');
            return $query->get()->all();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getBusinesUnitByCostcenter($costcenter,$fields=array('bu.*')) {
        try {
            $query = DB::table('business_units as bu')->select($fields);
            $query->where('bu.cost_center', $costcenter);
            return $query->first();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
}
