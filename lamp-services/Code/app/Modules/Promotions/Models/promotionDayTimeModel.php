<?php
/*
FileName :AddpromotionModel.php
Author   :eButor
Description : All the outbound order related functions are here.
CreatedDate :9/jun/2016
*/
//defining namespace
namespace App\Modules\Promotions\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class promotionDayTimeModel extends Model
{
    protected $table = 'promotion_date_time_details';
    protected $primaryKey = "prmt_time_id";
    /**
     * [getDateAndTime description]
     * @param  [int] $updateId [Promotion id]
     * @return [array]           [Promotion date time information]
     */
    public function getDateAndTime($updateId){
        $getDateAndTime = DB::table('promotion_date_time_details')->where('prmt_det_id','=',$updateId)->get()->all();
        return $getDateAndTime;
    }
 
    /**
     * [insertDayTimeDetails To insert date time details]
     * @param  [array] $dayTimeDetails [day time details]
     * @return [boolean]                 [true/false]
     */
    public function insertDayTimeDetails($dayTimeDetails){
        $this->insert($dayTimeDetails);
        return true;
    }
    /**
     * [insertSelectAllDays Days information of promotion]
     * @param  [array] $newPromotionData [promotion information]
     * @param  [int] $main_tbl_id      [Reference id]
     * @return [boolean]                   [true/false]
     */
    public function insertSelectAllDays($newPromotionData,$main_tbl_id){
        $this->prmt_det_id= $main_tbl_id;
        $this->day_name=$newPromotionData['all_days'];
        $this->day_time_from=$newPromotionData['select_all'];
        $this->day_time_to=$newPromotionData['select_to'];
         if ($this->save()) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * [deleteDayTimeDetails Delete day & time information of a promotion]
     * @param  [int] $deleteData [promotion id]
     * @return [boolean]             [true/false]
     */
    public function deleteDayTimeDetails($deleteData){
        $deleteData = DB::table('promotion_date_time_details')->where('prmt_det_id','=',$deleteData)->delete();
        if( $deleteData ){
            return true;
        }else{
            return false;
        }   
    }
    /**
     * [deleteDetails Delete promotion]
     * @param  [int] $deleteData [Promotion id]
     * @return [boolean]             [true/false]
     */
    public function deleteDetails($deleteData){
        $deleteDetails = DB::table('promotion_date_time_details')->where('prmt_det_id','=',$deleteData)->delete();
        if( $deleteDetails ){
            return true;
        }else{
            return false;
        }   
    }


}