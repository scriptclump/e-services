<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class VwProductsMarginModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id','category_name','product_name','mrp','base_price','EBP','RBP','CBP','inventorymode','Schemes','status','MPQ'];
	protected $table = "vw_productsmargin";



}
