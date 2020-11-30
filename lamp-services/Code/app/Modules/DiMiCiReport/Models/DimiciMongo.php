<?php

namespace App\Modules\DiMiCiReport\Models;

/*
  Filename : DimiciMongo.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 28-Feb-2017
  Desc : Model for storing dimici indent report requests in mongo table
 */

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Session;

class DimiciMongo extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'dimici_excel_upload';
    protected $primaryKey = '_id';
    
    public function storeDimiciDetails($url, $timestamp) {
        $this->description = "DiMiCi report upload";
        $this->url = $url;
        $this->unique_id = $timestamp;
        $this->user_id = Session::get('userId');
        $this->user_name = Session::get('userName');
        $this->save();
        return $this->_id;
    }
    
    public function updateDimiciDetails($id, $email_status) {
        $update_result = $this->where('_id', $id)->first();
        $update_result->email_status = $email_status;
        return $update_result->save();
    }
    
}