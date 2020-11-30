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

class AddPromotionBundleQuantity extends Model
{
    protected $table = 'promotion_bundle_product';
    protected $primaryKey = "prmt_bundle_id";
    /**
     * [insertBundleQuantity Inserts bundle type promotion]
     * @param  [array] $bundle_data [promotion information]
     * @return [boolean]              [return boolean]
     */
    public function insertBundleQuantity($bundle_data){
        $this->insert($bundle_data);
        return true;
    }
    /**
     * [deleteBundleQuantity Delete bundle promotion]
     * @param  [int] $deleteData [Promotion id]
     * @return [boolean]             [On successful deletion return true else false]
     */
    public function deleteBundleQuantity($deleteData){
        $deleteData = DB::table('promotion_bundle_product')->where('prmt_det_id','=',$deleteData)->delete();
        if( $deleteData ){
            return true;
        }else{
            return false;
        }   
    }
    /**
     * [deleteBundleQty Delete bundle promotion]
     * @param  [int] $deleteData [Promotion id]
     * @return [boolean]             [On successful deletion return true else false]
     */
     public function deleteBundleQty($deleteData){
        $deleteData = DB::table('promotion_bundle_product')->where('prmt_det_id','=',$deleteData)->delete();
        if( $deleteData ){
            return true;
        }else{
            return false;
        }   
    }

}