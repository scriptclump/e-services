<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SupplierWarehouses extends Model {

    public function getSupWarehouseByLegalEntityId($entityId) {
        try {
			$fieldArr = array('warehouse.*');
			$query = DB::table('supplier_warehouses as warehouse')->select($fieldArr);
			$query->where('warehouse.legal_entity_id', $entityId);			
			return $query->get()->all();
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    public function getWarehouseById($le_wh_id) {
        try {
			$fieldArr = array('warehouse.*');
			$query = DB::table('legalentity_warehouses as warehouse')->select($fieldArr);
			$query->where('warehouse.le_wh_id', $le_wh_id);			
			return $query->first();
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

}
