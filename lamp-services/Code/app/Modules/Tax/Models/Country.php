<?php

namespace App\Modules\Tax\Models;

/*
  Filename : Counter.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 05-July-2016
  Desc : Model for tax classes mongo table, to store tax class names
 */

use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    protected $table = "countries";
    protected $primaryKey = "country_id";

    public function countryName($countryID) {
        $states_query = $this->where('country_id', '=', $countryID)->get(array('name', 'country_id'))->all();
        return $states_query;
    }

}
