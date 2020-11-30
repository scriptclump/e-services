<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProductMedia extends Model
{
    public $timestamps = true;
    protected $table = "product_media";
    protected $primaryKey = 'prod_media_id';

    public function getProductImages($pid)
    {
    	return $this->where('product_id',$pid)->first(array('media_type','url','product_id','prod_media_id'));

    }
    public function getProductGelleryImage($product_id)
    {
    	return $this->where('product_id',$product_id)->get(array('url'))->all();

    }

}
