<?php
namespace App\models\Marketplace;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
class MPAttrMap extends Model {

    public $timestamps = false;
    protected $table = 'mp_attribute_map';
    //put your code here
    
    public function getMapAttributes($mp_id, $cat_id,$page,$pageSize) {
        $query= DB::table('mp_attribute_map')
                ->select('mp_attribute_map.attribute_id','mp_attribute_map.mp_att_id','mp_attributes.mp_category_id','mp_attributes.mp_id','mp_attributes.feature_name','mp_attributes.feature_id','attributes.name')
                ->rightjoin('mp_attributes','mp_attributes.mp_att_id','=','mp_attribute_map.mp_att_id')
                ->rightjoin('attributes','attributes.attribute_id','=','mp_attribute_map.attribute_id')
                //->where('mp_attribute_map.mp_cat_id',$cat_id)
                ->where('mp_attributes.mp_category_id',$cat_id)
                ->where('mp_attribute_map.mp_id', $mp_id);        
                $query->skip($page * $pageSize)->take($pageSize);
        $attributes = $query->get()->all();
        return json_decode(json_encode($attributes));
    }
    public function getMapAttributeCount($mp_id, $cat_id) {
        $query= DB::table('mp_attribute_map')
                ->rightjoin('mp_attributes','mp_attributes.mp_att_id','=','mp_attribute_map.mp_att_id')
                ->rightjoin('attributes','attributes.attribute_id','=','mp_attribute_map.attribute_id')
                ->where('mp_attributes.mp_category_id',$cat_id)
                ->where('mp_attribute_map.mp_id', $mp_id);
        $attribute_count = $query->count();
        return json_decode(json_encode($attribute_count));
    }
}
