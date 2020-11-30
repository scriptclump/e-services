<?php 
namespace App\Modules\Promotions\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
class commonModel{
	/**
	 * [getpromotionData Get promotion information]
	 * @return [array] [Promotion information]
	 */
	public function getpromotionData(){
		$getpromotionData = DB::table('promotion_template')->get()->all();
		return $getpromotionData;
	}
	/**
	 * [getstate Get states list]
	 * @return [array] [state information]
	 */
	public function getstate(id){
			$getstate = DB::table('zone')->where('country_id', '=', 99);
			$query = ->where('status', '=', '1')
					 ->where('name', 'not like', '%All%')
					 ->orderBy("sort_order");
			$concat = $getstate;
			if(id==1){
					$concat .= $query;
				}	
			return $concat->get()->all();
	}
}