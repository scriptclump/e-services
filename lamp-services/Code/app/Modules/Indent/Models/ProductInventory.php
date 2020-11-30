<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductInventory extends Model
{
    protected $table = "product_inventory";
	protected $primaryKey     = "prod_inv_id";
	
	public function getProductInventoryById($productId) {
		$fieldArr = array('inventory.available_inventory','inventory.product_id');
		$query = DB::table('product_inventory as inventory')->select($fieldArr);
        $query->where('inventory.product_id', '=', $productId);
        return $query->first();
    }
}
