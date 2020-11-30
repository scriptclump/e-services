<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class ProductAttributesModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "prod_att_id";
    protected $table = "product_attributes";

    public function AttributeName()
    {
    	 return $this->hasOne('App\Modules\Product\Models\AttributesModel', 'attribute_id', 'attribute_id');     
    }
    public function getProductAttributeName($product_id)
    {
    	return $this->where('product_id',$product_id)->get(array('attribute_id','product_id','value'))->all();
    }
}

