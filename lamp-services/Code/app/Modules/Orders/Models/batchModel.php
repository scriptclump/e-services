<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;

class batchModel extends Model {

    protected $table = "gds_orders_batch";

    public function insertBatchHistory($batch_history_array){
        DB::table("inventory_batch_history")->insert($batch_history_array);
        return 1;
    }

    public function insertBatch($batch_array){
        DB::table("inventory_batch")->insert($batch_array);
        return 1;
    }

}


