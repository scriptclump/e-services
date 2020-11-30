<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : LegalEntity.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 06-June-2016
  Desc : Model for legal entities table
 */

use Illuminate\Database\Eloquent\Model;

class LegalEntity extends Model{
    protected $primaryKey = "legal_entity_id";
    
    public function byId($legalId) {
        $id_result_set = $this->where('legal_entity_id', $legalId)->get()->all();
        $id_result_set_array = json_decode(json_encode($id_result_set), true);
        return $id_result_set_array;
    }
    
    public function leAddressOnly($legalEntityId) {
        $address_result_set = $this->where('legal_entity_id', $legalEntityId)->get(array('address1', 'address2', 'city'))->all();
        $address_result_array = json_decode(json_encode($address_result_set), true);
        return $address_result_array;
    }
    
}