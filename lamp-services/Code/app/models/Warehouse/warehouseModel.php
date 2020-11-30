<?php

namespace App\models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Cache;
use DB;

class warehouseModel extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
        //use UserTrait, RemindableTrait;
        protected $primaryKey = 'le_wh_id';
        public $timestamps = false;
        protected $table = 'legalentity_warehouses';

        public function getwareHousedata($le_wh_id){

        	$value = Cache::get('warehouseid_'.$le_wh_id);
        	if(!is_null($value)){
        		$value = json_decode($value,true);
        		return $value;
        	}else{

        		$warehouse = DB::table('legalentity_warehouses')->where('le_wh_id',$le_wh_id)->get()->all();
        		if($warehouse){

        			$warehouse = json_decode(json_encode($warehouse[0]),true);
        		}else{
        			$warehouse = array();
        		}
        		
        		Cache::put('warehouseid_'.$le_wh_id, json_encode($warehouse), 120);
        		return $warehouse;

        	}
        }
}
