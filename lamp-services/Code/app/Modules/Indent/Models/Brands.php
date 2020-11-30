<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Brands extends Model {

    public function getBrandsByLegalEntityId($entityId) {
        try {
            $fieldArr = array('brands.*');
            $query = DB::table('brands')->select($fieldArr);
            $query->where('brands.legal_entity_id', $entityId);
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

}
