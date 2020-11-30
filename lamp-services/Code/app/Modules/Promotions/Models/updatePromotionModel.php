<?php
/*
FileName : AddpromotionModel.php
Author   :eButor
Description : All the function for Update the promotion.
CreatedDate : 9/jun/2016
*/
//defining namespace
namespace App\Modules\Promotions\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Promotions\Models\AddpromotionModel;
use DB;
use Session;
use UserActivity;
class updatePromotionModel extends Model
{
    protected $table = 'promotion_details';
    protected $primaryKey = "prmt_det_id";
    /**
     * [getpromotiondetailsData get promotion information]
     * @param  [int] $updateId [promotion id]
     * @return [array]         [promotion info]
     */
	public function getpromotiondetailsData($updateId){

		$getpromotiondetailsData = DB::table('promotion_details as pmtdet')
				->join("promotion_template as pmttmpl", "pmtdet.prmt_tmpl_Id", "=", "pmttmpl.prmt_tmpl_Id")
                ->where("pmtdet.prmt_det_id",$updateId);
			if(Session::get('legal_entity_id')!=0){
			$getpromotiondetailsData->where('legal_entity_id', '=', Session::get('legal_entity_id'));
			}
        return $getpromotiondetailsData->first();
    }
    /**
     * [getstate get states list]
     * @return [array] [states list]
     */
    public function getstate(){
		$getstate = DB::table('zone')->where('country_id', '=', 99)->get()->all();
        return $getstate;
	}
	/**
	 * [getFreeProductByID Products list]
	 * @return [array] [products list]
	 */
	public function getFreeProductByID(){
		$freeProductDet = DB::table("products")
						->get()->all();
		return $freeProductDet;
	}
	/**
	 * [getAppliedItem get promotion applied items list]
	 * @param  [string]  $offeron    [product/category]
	 * @param  [string]  $appliedIds [comma seperated applied id's ]
	 * @param  integer $offerType  [percentage/value]
	 * @return [array]              [promotion details]
	 */
	public function getAppliedItem($offeron,$appliedIds, $offerType=1){
		$getproductDetails = array();

		$legalEntiryQuery = "";
		if(Session::get('legal_entity_id')!=0){
			$legalEntityQuery=" WHERE legal_entity_id='" . Session::get('legal_entity_id') . "'";
		}

		
		if($appliedIds==''){
			return $getproductDetails;
		}else{
			if($offeron=='Product'){
				if($offerType==1){
					$sqlQuery = "select prd.product_id AS 'ItemID',
							CONCAT('<div style=\"float:left; padding-right: 12px;\"><img height=\"50\" width=\"50\" src=\"', prd.primary_image,'\"></div><div>', prd.product_title,'<br/>',prd.sku) AS 'ItemName' 
							FROM products AS prd WHERE prd.product_id IN (".$appliedIds.")";
	
				}else{
					$sqlQuery = "select prd.product_id AS 'ItemID',
							prd.product_title AS 'ItemName' 
							FROM products AS prd ". $legalEntiryQuery;

				}
				
				$getproductDetails = DB::select(DB::raw($sqlQuery));
				return $getproductDetails;

			}elseif($offeron=='Category'){
				$sqlCategory = "select cat.category_id AS 'ItemID',
					cat.cat_name AS 'ItemName'
					FROM categories AS cat WHERE cat.category_id IN (".$appliedIds.")";

				$getCategoryDetails = DB::select(DB::raw($sqlCategory));			
				return $getCategoryDetails;

			// This section is for Discount on Bill	
			}elseif($offeron=='Bill'){
				$billQuery = "";
			}
		}
	}
	/**
	 * [getAppliedItemForBundle Get promotion applied items list]
	 * @param  [int] $updateId [promotion id]
	 * @return [array]           [product details]
	 */
	public function getAppliedItemForBundle($updateId){
		$sqlQuery = "select prd.product_id AS 'ItemID',
						CONCAT('<div style=\"float:left; padding-right: 12px;\"><img height=\"50\" width=\"50\" src=\"', prd.primary_image,'\"></div><div>', prd.product_title,'<br/>',prd.sku) AS 'ItemName', bndl.product_qty
						from promotion_details as prmtdet
						inner join promotion_bundle_product as bndl on prmtdet.prmt_det_id=bndl.prmt_det_id
						inner join products AS prd on prd.product_id=bndl.applied_ids
						and prmtdet.prmt_det_id=$updateId;";
				$getproductDetails = DB::select(DB::raw($sqlQuery));
				return $getproductDetails;

	}
	/**
	 * [updateNewPromotionData update Promotion Data]
	 * @param  [array] $updatePromotionData [Promotion information]
	 * @param  [int] $entity_id           [legal entity id]
	 * @return [int/boolean]                      [on success promotion id/ on failure false]
	 */
	public function updateNewPromotionData($updatePromotionData,$entity_id){
	
		
		$updatedetails_data = AddPromotionModel::find($updatePromotionData['prmt_det_id']);
		$updatedetails_data->prmt_det_name = $updatePromotionData['promotion_name'];
        $oldData = array(
			'OLDVALUES'   =>  json_decode(json_encode($updatePromotionData)),
			'NEWVALUES'   => 'Deleted data',
		);

		$updatedetails_data->prmt_condition_value1 = isset($updatePromotionData['set_qty']) ? $updatePromotionData['set_qty'] : 0;
     	
		// checking for the Conditon value in Promotion
		if( isset($updatePromotionData['condition']) ){

			if( $updatePromotionData['condition']=='FreeQty' ){

				// Setting values for Free Qty Promotion
				$updatedetails_data->prmt_offer_value = "0";
				$updatedetails_data->is_percent_on_free = "0";
				$updatedetails_data->prmt_free_qty = isset($updatePromotionData['free_qty']) ? $updatePromotionData['free_qty'] : '';
				if(isset($updatePromotionData['select_product'])){
		        	$updatedetails_data->prmt_free_product = implode( ',', array_values($updatePromotionData['select_product']));
		        }

			}elseif($updatePromotionData['condition']=='Discount'){
				// Setting values for Discount Promotion
				$updatedetails_data->prmt_offer_value = $updatePromotionData['discount_offer'];
				$updatedetails_data->is_percent_on_free = isset($updatePromotionData['offon_percent']) ? 1 : 0;
				$updatedetails_data->prmt_free_qty = '';
		        $updatedetails_data->prmt_free_product = '';
			}else{
				// Defence check if No Condition
				$updatedetails_data->prmt_offer_value = '0';
				$updatedetails_data->is_percent_on_free =  '0';
				$updatedetails_data->prmt_free_qty = '';
		        $updatedetails_data->prmt_free_product = '';
			}

		}else{
			// If there is no condition available ( like Slab promotion )
			$updatedetails_data->prmt_offer_value = '0';
			$updatedetails_data->is_percent_on_free =  '0';
			$updatedetails_data->prmt_free_qty = '';
	        $updatedetails_data->prmt_free_product = '';
		}

        // Common Part of Promotion
		$updatedetails_data->prmt_tmpl_Id = $updatePromotionData['select_offer_tmpl'];
		$updatedetails_data->product_star = 	isset($updatePromotionData['product_star_color_table'])?implode( ',', array_values($updatePromotionData['product_star_color_table'])):0;
		$updatedetails_data->pack_type = 	isset($updatePromotionData['pack_number_update'])?implode( ',', array_values($updatePromotionData['pack_number_update'])):0;
		$updatedetails_data->esu  = 	isset($updatePromotionData['pack_value_update'])?implode( ',', array_values($updatePromotionData['pack_value_update'])):0;
		$updatedetails_data->start_date = $updatePromotionData['start_date'];
		$updatedetails_data->end_date = $updatePromotionData['end_date'];
		$updatedetails_data->prmt_lock_qty = $updatePromotionData['prmt_lock_qty'];
		$updatedetails_data->prmt_description = $updatePromotionData['description'];
        $updatedetails_data->prmt_label = $updatePromotionData['label'];
        $updatedetails_data->offon_free_product = isset($updatePromotionData['offon_free_product']) ? $updatePromotionData['offon_free_product'] : 0;
		$updatedetails_data->prmt_det_status = isset($updatePromotionData['promotion_status']) ? 1 : 0;
		$updatedetails_data->is_repeated = isset($updatePromotionData['is_repeated']) ? 1 : 0;
		$updatedetails_data->prmt_offer_on = $updatePromotionData['gridCallType'];
		$updatedetails_data->legal_entity_id = $entity_id;
		$updatedetails_data->updated_by = Session::get('userId');  
		$updatedetails_data->warehouse=implode( ',',$updatePromotionData['warehouse_details']);     	


		$updatedetails_data->prmt_states = isset($updatePromotionData['state']) ? implode( ',', $updatePromotionData['state'] ) : "";
		$updatedetails_data->prmt_customer_group = isset($updatePromotionData['customer_group']) ? implode( ',', $updatePromotionData['customer_group'] ) : "";
		

		// Needed for CashBack
		// Geting the Range Value for the promotion if not cashback then only the end value in the else part
		if($updatePromotionData['select_offer_tmpl'] == '5'){

        	$updatedetails_data->updated_by = Session::get('userId');       	


			$updatedetails_data->prmt_condition_value1 =  $updatePromotionData['update_from_cashback'] ;

			$updatedetails_data->prmt_condition_value2 = $updatePromotionData['update_to_cashback'] ;

			if( is_array($updatePromotionData['offertypemanf_table_update']) ){
				$updatedetails_data->prmt_manufacturers = isset($updatePromotionData['offertypemanf_table_update']) ? implode( ',', array_filter($updatePromotionData['offertypemanf_table_update']) ) : "";
			}
			if( is_array($updatePromotionData['offertypbrand_table_update']) ){

				$updatedetails_data->prmt_brands = isset($updatePromotionData['offertypbrand_table_update']) ? implode( ',', array_filter($updatePromotionData['offertypbrand_table_update'] )) : "";
			}

			$updatedetails_data->prmt_offer_value=isset($updatePromotionData['discount_offer_on_bill_table_update'])?implode(',', array_values($updatePromotionData['discount_offer_on_bill_table_update'])):0;

			if( is_array($updatePromotionData['ProductStar_table_update']) ){


				if(isset($updatePromotionData['ProductStar_table_update']))
		        {
		        	$product_cashback = array_filter($updatePromotionData['ProductStar_table_update'], function($value) {
			   		 return ($value !== null && $value !== false && $value !== ''); 
					});
		        	$updatedetails_data->product_star =  implode( ',', $product_cashback);
	        	}/*

				$updatedetails_data->product_star= isset($updatePromotionData['ProductStar_table_update']) ? implode( ',', array_filter($updatePromotionData['ProductStar_table_update'] )) : "";*/
			}

			if( is_array($updatePromotionData['Benificiary_table_update']) ){

				$updatedetails_data->order_type= isset($updatePromotionData['Benificiary_table_update']) ? implode( ',', array_filter($updatePromotionData['Benificiary_table_update'] )) : "";
			}

			// if( is_array($updatePromotionData['wareHouseId_table_update']) ){

			// 	$updatedetails_data->warehouse = isset($updatePromotionData['wareHouseId_table_update']) ? implode( ',', array_filter($updatePromotionData['wareHouseId_table_update']) ): '';
			// }

			$updatedetails_data->prmt_condition_value1=isset($updatePromotionData['cash_back_from_table_update'])?implode(',', array_values($updatePromotionData['cash_back_from_table_update'])):0;

			$updatedetails_data->prmt_condition_value2=isset($updatePromotionData['cash_back_to_table_update'])?implode(',', array_values($updatePromotionData['cash_back_to_table_update'])):0;

			//$updatedetails_data->prmt_offer_value = $updatePromotionData['discount_offer_cashback'];
			$updatedetails_data->is_percent_on_free = isset($updatePromotionData['offon_percent_table_update']) ? 1 : 0;

		}else if($updatePromotionData['select_offer_tmpl'] == '4'){
			$updatedetails_data->prmt_condition_value2 = isset($updatePromotionData['update_bill_value']) ? $updatePromotionData['update_bill_value'] : 0;
			$updatedetails_data->prmt_manufacturers = isset($updatePromotionData['offertypeman_discount']) ? implode( ',', $updatePromotionData['offertypeman_discount'] ) : "";
			$updatedetails_data->prmt_brands = isset($updatePromotionData['offertypebrand_discount']) ? implode( ',', $updatePromotionData['offertypebrand_discount'] ) : "";
			$updatedetails_data->prmt_offer_value=isset($updatePromotionData['discount_offer_on_billvalue'])?$updatePromotionData['discount_offer_on_billvalue']:0;

			if(isset($updatePromotionData['ProductStar_on_bill_update']))
	        {
	        	$product_bill = array_filter($updatePromotionData['ProductStar_on_bill_update'], function($value) {
		   		 return ($value !== null && $value !== false && $value !== ''); 
				});
	        	$updatedetails_data->product_star =  implode( ',', $product_bill);
        	}/*

			$updatedetails_data->product_star=isset($updatePromotionData['ProductStar_on_bill_update'])?implode( ',',$updatePromotionData['ProductStar_on_bill_update']):"";*/
			$updatedetails_data->is_percent_on_free = isset($updatePromotionData['offon_percent_onbill']) ? 1 : 0;			
		}else{
			$updatedetails_data->prmt_offer_value=isset($updatePromotionData['discount_offer'])?$updatePromotionData['discount_offer']:0;
			$updatedetails_data->is_percent_on_free = isset($updatePromotionData['offon_percent']) ? 1 : 0;
		}

		$updatedetails_data->applied_ids = isset($updatePromotionData['item_id']) ? implode( ',', array_values($updatePromotionData['item_id'])) : "";
	
		if ($updatedetails_data->save()) {
        	UserActivity::userActivityLog('Promotions', $oldData, 'Promotions Data Updated by the User');
        	return $updatedetails_data->prmt_det_id;
        }else{
        	return false;
        }
	}
	/**
	 * [getFreeSampleDataFromChild Get free quantity data]
	 * @param  [int] $id [promotion id]
	 * @return [array]     [promotion information]
	 */
	public function getFreeSampleDataFromChild($id){
		$data = DB::table('promotions_freeqty_sample_details')
			->where('ref_id','=',$id)
			->first();
		return $data;
	}
	/**
	 * [getTradeCashbackDataFromChild Get trade discount promotion info]
	 * @param  [int] $id [promotion id]
	 * @return [array]     [trade discount data]
	 */
	public function getTradeCashbackDataFromChild($id){
		$data = DB::table('trade_disc_det')
				->where('ref_id',$id)
				->first();
		return $data;
	}

}