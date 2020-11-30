<?php

namespace App\Modules\ScheduleJobs\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;

class InventoryReport extends Model {

    protected $table = 'vw_inventory_report';
    

    public function getAllInventory() {
        $sql = $this->get()->all();
        return $sql;
    }
}

