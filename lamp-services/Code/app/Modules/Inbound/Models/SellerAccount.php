<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : SellerAccount.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 24-May-2016
  Desc : Model for  seller account table
 */

use Illuminate\Database\Eloquent\Model;

class SellerAccount extends Model
{
    protected $primaryKey = "seller_id";
    
    public function byLegalEntityId($legalEntityId) {
        $id_result_set = $this->where('legal_entity_id', $legalEntityId)->get()->all();
        $id_result_set_array = json_decode(json_encode($id_result_set), true);
        return $id_result_set_array;
    }

    public function getAllSellerIds($legal_entity_id){
                $query = $this->where('legal_entity_id', $legal_entity_id)->get(['seller_id'])->all();
                return json_decode($query,true);
    }
}