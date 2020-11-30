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

class slabDetailsModel extends Model
{
    protected $table = 'products_slab_rates';
    protected $primaryKey = "product_slab_id";

    // insert data into details table
    /**
     * [insertSlabDetails Insert slab details]
     * @param  [array] $slab_data [slab promotion info]
     * @return [boolean]            [true]
     */
    public function insertSlabDetails($slab_data){
        $this->insert($slab_data);
        return true;
    }
    /**
     * [deleteSlabDetails delete slab details]
     * @param  [array] $deleteData [promotion id]
     * @return [boolean]             [true/false]
     */
    public function deleteSlabDetails($deleteData){
    	$deleteDetails = DB::table('products_slab_rates')->where('prmt_det_id','=',$deleteData)->delete();
        if( $deleteDetails ){
            return true;
        }else{
            return false;
        }  
    }
    /**
     * [getPrmtSlabData Get promotion slab information]
     * @param  [int] $prmt_det_id [promotion id]
     * @return [array]              [promotion info]
     */
    public function getPrmtSlabData($prmt_det_id){
        $getSlabRate ="select *,
                       (SELECT m.description FROM master_lookup m WHERE m.value=p.`product_star_slab`) AS 'PrdStar',
                       (SELECT m1.master_lookup_name FROM master_lookup m1 WHERE m1.value=p.`pack_type`) AS 'packtype' FROM products_slab_rates p WHERE p.`prmt_det_id`=$prmt_det_id";
        $getslabdata    = DB::select(DB::raw($getSlabRate));
        return $getslabdata;
    }

    // Check for the slabExists
    /**
     * [checkSlabExist  Check for whether the slabExists ]
     * @param  [int] $updatedate [Promotion id]
     * @param  [int] $value      [promotion end range]
     * @param  [int] $packnumber [pack number]
     * @param  [int] $esu        [no of esu]
     * @return [int]             [no of slabs exists with this configuration]
     */
    public function checkSlabExist($updatedate, $value,$packnumber,$esu){
        $checkSlab = DB::table("products_slab_rates")
                    ->where("prmt_det_id","=",$updatedate)
                    ->where("end_range","=",$value)
                   ->where("pack_type","=",$packnumber)
                    ->where("esu","=",$esu)
                    ->count();
        return $checkSlab;
    }
}