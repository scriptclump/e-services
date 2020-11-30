<?php
namespace App\Modules\Promotions\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Promotions\Models\tradeDiscountModel;
use DB;
use Session;
class tradeDiscountModel extends Model{
	protected $table='trade_disc_det';
	protected $primaryKey='free_id';
	/**
	 * [saveTradeDiscData Trade discount information]
	 * @param  [array] $data [trade discount info]
	 * @return [boolean]       [true]
	 */
	public function saveTradeDiscData($data){
		$this->insert($data);
		return true;
	}
	/**
	 * [updateTradeDiscData Update trade discount data]
	 * @param  [array] $data [PRomotion information]
	 * @return [boolean]       [true]
	 */
	public function updateTradeDiscData($data){
		DB::table('trade_disc_det')	
		->where('ref_id',$data['ref_id'])
		->update($data);	
		return true;

	}
	/**
	 * [deleteTradeDetails delete trade discount information]
	 * @param  [int] $deleteData [promotion id]
	 * @return [boolean]             [true/false]
	 */
    public function deleteTradeDetails($deleteData){
        $deleteDetails = DB::table('trade_disc_det')->where('ref_id','=',$deleteData)->delete();
        if( $deleteDetails ){
            return true;
        }else{
            return false;
        }   
    }
}
?>