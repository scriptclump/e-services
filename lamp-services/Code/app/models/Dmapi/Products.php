<?php

namespace App\models\Dmapi;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class Products extends Model
{
    /**
     * 	[$primaryKey - primary key for the table]
     */

    protected $primaryKey = 'product_id';
    public $timestamps = false;
    protected $table = 'products';

    /**
     * [getManufactureId returns the manufacture Id]
     * @param  [class] $data [productinfo]
     */
    public function getManufactureId($productDetails){

    	$manufacturer_id =   "";
    	if (!empty($productDetails)) {
                foreach ($productDetails as $product) {
                    $manufacturer_id = DB::table('products')->where('sku', $product->sku)->pluck('legal_entity_id')->all();
					if(count($manufacturer_id) > 0)
						$manufacturer_id =   $manufacturer_id[0];
					else
						$manufacturer_id =   "";
                }
        }

        return $manufacturer_id;

    }


}
