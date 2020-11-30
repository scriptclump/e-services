<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : LegalentityWarehouses.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 06-June-2016
  Desc : Model for warehouse locations table
 */

use Illuminate\Database\Eloquent\Model;

class LegalentityWarehouses extends Model{
    protected $table = "legalentity_warehouses";
    protected $primaryKey = "le_wh_id";
    
    public function whAddressOnly($legalEntityId) {
        $address_result_set = $this->where('legal_entity_id', $legalEntityId)->get(array('le_wh_id', 'address1', 'address2', 'city'))->all();
        $address_result_array = json_decode(json_encode($address_result_set), true);
        return $address_result_array;
    }

    public function getWareHousename($warehouseId)
    {
        $warehousename = $this->where('le_wh_id', $warehouseId)->get(array('contact_name'))->all();
        $value =  json_decode($warehousename, true);
        return  $value[0]['contact_name'];

    }
    
}