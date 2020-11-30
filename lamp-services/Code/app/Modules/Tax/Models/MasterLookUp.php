<?php

namespace App\Modules\Tax\Models;

/*
  Filename : Counter.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 05-July-2016
  Desc : Model for tax classes mongo table, to store tax class names
 */

use Illuminate\Database\Eloquent\Model;

class MasterLookUp extends Model {

    protected $table = "master_lookup";
    protected $primaryKey = "inbound_request_id";

    public function allStates() {
        $states_query = $this->where('mas_cat_id', '=', '40')->get(array('master_lookup_id', 'master_lookup_name'))->all();
        return $states_query;
    }

    public function getStateId($statename) {
        $query = $this->where('master_lookup_name', '=', $statename)->get(array('master_lookup_id'))->all();
        $state_id = $query;
        return $state_id[0]['master_lookup_id'];
    }

    public function getTaxTypes() {
        $query = $this->where('mas_cat_id', '=', '9')->get(array('master_lookup_name', 'master_lookup_id'))->all();
        $result = $query;
        return $result;
    }

    public function getParentLookUpId($taxtype)
    {
        $query = $this->where('master_lookup_name' , '=', $taxtype)->where('mas_cat_id', '=', '9')->pluck('parent_lookup_id')->all();
        return $query;
    }

    public function getTaxTypesByParentLookUpId($parentlookupid)
    {
        $query = $this->where('parent_lookup_id', '=', $parentlookupid)->pluck('master_lookup_name', 'master_lookup_id')->all();
        return $query;
    }
    
    public function getMasterLookupName($masterLookupValue) {
        return json_decode(json_encode($this->where('mas_cat_id', 57)->where('value', $masterLookupValue)->pluck('master_lookup_name', 'value')->all()), true);
    }

}
