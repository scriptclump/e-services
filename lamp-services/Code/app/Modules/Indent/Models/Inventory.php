<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Inventory extends Model
{
    protected $table = "inventory";
	protected $primaryKey = "inv_id";
	
	public function getInventoryByProductId($productId) {
		$fieldArr = array('inventory.available_inventory','inventory.product_id');
		$query = DB::table('inventory')->select($fieldArr);
        $query->where('inventory.product_id', '=', $productId);
        return $query->first();
    }
}
