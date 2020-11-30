<?php
namespace App\Modules\Promotions\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Promotions\Models\freeQtyModel;
use DB;
use Session;
class freeQtyModel extends Model{
	protected $table='promotions_freeqty_sample_details';
	protected $primaryKey='free_id';
	/**
	 * [saveFreeQtyData To Save free sample promotion ]
	 * @param  [array] $data [promotion data]
	 * @return [boolean]       [true]
	 */
	public function saveFreeQtyData($data){
		$this->insert($data);
		return true;
	}
	/**
	 * [updateFreeQtyData To edit free qty promotion]
	 * @param  [array] $data [promotion data]
	 * @return [boolean]       [true]
	 */
	public function updateFreeQtyData($data){
		DB::table('promotions_freeqty_sample_details')	
		->where('ref_id',$data['ref_id'])
		->update($data);	
		return true;

	}
	/**
	 * [deleteFreePromotionDetails To Delete promotion details]
	 * @param  [int] $deleteData [promotion id]
	 * @return [boolean]             [true/false]
	 */
	public function deleteFreePromotionDetails($deleteData){
		$deleteDetails = DB::table('promotions_freeqty_sample_details')->where('ref_id','=',$deleteData)->delete();
        if( $deleteDetails ){
            return true;
        }else{
            return false;
        }   
	}
}
?>