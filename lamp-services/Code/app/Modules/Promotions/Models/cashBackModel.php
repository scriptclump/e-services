<?php
namespace App\Modules\Promotions\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Promotions\Models\cashBackModel;
use DB;
use Session;

class cashBackModel extends Model
{
    protected $table = 'promotion_cashback_details';
    protected $primaryKey = "cbk_id";
    /**
     * [saveNewcashBackData Save cashback promotion]
     * @param  [array] $newPromotionData [promotion info]
     * @return [boolean]                   [true]
     */
    public function saveNewcashBackData($newPromotionData){       

     
        $this->insert($newPromotionData); 
        return true;
    	

    }

    /**
     * [updateCashBackData Update cashback information]
     * @param  [array] $newPromotionData [promotion info]
     * @param  [int] $created_by       [promotion created by]
     * @return [boolean]                   [true]
     */
    public function updateCashBackData($newPromotionData,$created_by){
      
        $deleteQuery = DB::table('promotion_cashback_details')->where('cbk_ref_id', $newPromotionData['prmt_det_id'])->delete();
        $this->cbk_ref_id = $newPromotionData['prmt_det_id'];
        $this->created_by = $created_by;        
        $this->cbk_label = $newPromotionData['promotion_name'];
        $this->start_date = $newPromotionData['start_date'];
        $this->end_date = $newPromotionData['end_date'];
        $this->range_from = $newPromotionData['update_from'];
        $this->range_to = $newPromotionData['update_to'];
        $this->cbk_type = $newPromotionData['offon_percent_cashback'].is(":checked")? '%' : '&#8377;';
        $this->cbk_value = $newPromotionData['discount_offer_cashback'];
        $this->cbk_status = $newPromotionData['promotion_status'];
        $this->cbk_source_type = 1;
        if(isset($newPromotionData['Product_star']))
            {
                $this->product_star =  implode( ',', array_values($newPromotionData['Product_star']));
            }
        if(isset($newPromotionData['Order']))
         {
            $this->benificiary_type =  implode( ',', array_values($newPromotionData['Order']));

         }
         if(isset($newPromotionData['state']))
            {
                $this->state_id =  implode( ',', array_values($newPromotionData['state']));
            }
         if(isset($newPromotionData['customer_group']))
            {
                $this->customer_type =  implode( ',', array_values($newPromotionData['customer_group']));
            }
        if(isset($newPromotionData['wareHouseId']))
         {
            $this->wh_id =  implode( ',', array_values($newPromotionData['wareHouseId']));

         }
        $this->save();
        return true;
    }
    /**
     * [deleteCashBackDetails Delete cashback information ]
     * @param  [id] $deleteData [cashback id]
     * @return [boolean]             [true/false]
     */
    public function deleteCashBackDetails($deleteData){
        $deleteDetails = DB::table('promotion_cashback_details')->where('cbk_ref_id','=',$deleteData)->delete();
        if( $deleteDetails ){
            return true;
        }else{
            return false;
        }   
    }
}
?>
