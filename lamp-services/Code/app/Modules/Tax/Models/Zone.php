<?php
namespace App\Modules\Tax\Models;

/*
  Filename : Counter.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 05-July-2016
  Desc : Model for tax classes mongo table, to store tax class names
 */

use Illuminate\Database\Eloquent\Model;

class Zone extends Model {

    protected $table = "zone";
    protected $primaryKey = "zone_id";

    public function allStates() {
        //Telangana Come top in list
        $states_query = $this->where('country_id', '=', '99')->orderBy('sort_order', 'asc')->get(array('zone_id', 'name', 'code'))->all();
        return $states_query;
    }

    public function allStatesExceptAll() {
        //Telangana Come top in list
        $states_query = $this->where('country_id', '=', '99')->orderBy('sort_order', 'asc')->get(array('zone_id', 'name', 'code'))->all();
        return $states_query;
    }

    public function getStateId($statename) {
        $query = $this->where('name', '=', $statename)->get(array('zone_id'))->all();
        $state_id = $query;
        return $state_id[0]['zone_id'];
    }

    public function getAllStates($countryId) {
        $query = $this->where('country_id', '=', $countryId)->orderBy('sort_order', 'asc')->get(array('zone_id', 'name'))->all();
        return $query;
    }

    public function getSatetIdBasedonCode($statecode) {
        $query = $this->where('code', '=', $statecode)->where('country_id', '=', '99')->get(array('zone_id'))->all();
        $state_id = $query;
        return @$state_id[0]['zone_id'];
    }

    public function getStateCode($statename) {
        $query = $this->where('name', '=', $statename)->get(array('code'))->all();
        $statecode = json_decode($query, true);
        $code = @$statecode[0]['code'];
        return $code;
    }
    
    public function getStateName($zoneId) {
        return $this->where('zone_id', $zoneId)->pluck('name')->all();
    }

}
