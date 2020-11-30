<?php
namespace App\models\Marketplace;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
class MPOptionMap extends Model {

    public $timestamps = false;
    protected $table = 'mp_option_mapping';
    //put your code here
    
    function optionMap() {
        return $this->hasOne('App\models\Marketplace\Varients', 'mp_option_id', 'mp_option_id');
    }

    public function getMapOptions($mp_id, $feature_id,$page,$pageSize) {
        $query= DB::table('mp_option_mapping')
                ->join('mp_attr_options','mp_attr_options.mp_option_id','=','mp_option_mapping.mp_option_id')
                ->join('attribute_options','attribute_options.option_id','=','mp_option_mapping.option_id')
                ->where('mp_attr_options.featureid',$feature_id)
                ->where('mp_attr_options.mp_id', $mp_id);
                $query->skip($page * $pageSize)->take($pageSize);
        $variants = $query->get()->all();
        return json_decode(json_encode($variants));
    }
    public function getMapOptionsCount($mp_id, $feature_id) {
        $query= DB::table('mp_option_mapping')
                ->join('mp_attr_options','mp_attr_options.mp_option_id','=','mp_option_mapping.mp_option_id')
                ->join('attribute_options','attribute_options.option_id','=','mp_option_mapping.option_id')
                ->where('mp_attr_options.featureid',$feature_id)
                ->where('mp_attr_options.mp_id', $mp_id);
        $variant_count = $query->count();
        //print_r($variants);exit;
        return json_decode(json_encode($variant_count));
    }
}
