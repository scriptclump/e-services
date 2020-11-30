<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class AttributesSetMapping extends Model
{
    public $timestamps = false;
    protected $table = "attribute_set_mapping";

    public function attributeName()
    {
    	 return $this->hasOne('App\Modules\Product\Models\AttributesModel', 'attribute_id', 'attribute_id')->groupBy('attribute_id');     
    }
    public function attributeGroupName()
    {
    	 return $this->hasOne('App\Modules\Product\Models\AttributesGroup', 'attribute_group_id', 'attribute_group_id');     
    }
    public function getAttributesData()
    {
    	return $this->with(array('attributeGroupName' => function($query)
    	{
    		$query->select('attribute_group_id','name');
    	}, 'attributeName' => function($attQuery)
    	{
    		$attQuery->select('attribute_id','name');
    	}))->get(array('attribute_id','attribute_group_id'))->all();

    }

   
}
