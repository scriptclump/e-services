<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class AttributesSetModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "attribute_set_id";
    protected $table = "attribute_sets";
 	
 	public function getAttributeSetId($category_id)
 	{
 		$rs=$this->select('attribute_set_id')->where('category_id',$category_id)->first();
 		return json_decode(json_encode($rs),true);
 	}
 	
}

