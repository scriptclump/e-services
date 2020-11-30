<?php

/*
  Filename : InventorySnapshot.php
  Author : Ebutor
  CreateData : 16-Sep-2016
  Desc : Model for inventory_snapshot mongodb document
 */

namespace App\Modules\ScheduleJobs\Models;

date_default_timezone_set("Asia/Kolkata");

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;

class InventorySnapshot extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'inventory_snapshot';
    protected $primaryKey = '_id';
    public $timestamps = false;

    public function copyInventoryData($data) {
        try {
            if (!empty($data)) {
                $arrKeys = array_keys($data);
                foreach ($arrKeys as $key) {
                    if ($key == "le_wh_id") {
                        $this->$key = (int) $data[$key];
                    } else if ($key == "product_id") {
                        $this->$key = (int) $data[$key];
                    } else if ($key == "product_group_id") {
                        $this->$key = (int) $data[$key];
                    } else {
                        $this->$key = $data[$key];
                    }
                }
                $this->created_at = date('Y-m-d H:i:s');
                $this->updated_at = date('Y-m-d H:i:s');
                $this->save();
            }
        } catch (\ErrorException $ex) {
            echo "Error..";
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

}
