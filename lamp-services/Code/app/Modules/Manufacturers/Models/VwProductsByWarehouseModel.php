<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class VwProductsByWarehouseModel extends Model
{
    public $timestamps = false;
    //protected $fillable = ['manufacturer_id','image','brand_id','product_id','category_name','product_name','mrp','base_price','EBP','RBP','CBP','inventorymode','Schemes','status','MBQ'];
	protected $table = "vw_product_grid_wh";



}
