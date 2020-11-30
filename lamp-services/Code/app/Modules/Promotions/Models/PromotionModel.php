<?php
/*
FileName :promotionModel.php
Author   :eButor
Description : All the outbound order related functions are here.
CreatedDate :9/jun/2016
*/
//defining namespace
namespace App\Modules\Promotions\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Promotions\Models\PromotionModel;
use DB;
use Session;

class PromotionModel extends Model
{
    protected $table = 'promotion_template';
    protected $primaryKey = "prmt_tmpl_Id";
    /**
	 * [getStateDetails State details]
	 * @return [array] [state information]
	 */
    public function getStateDetails(){
		$getDetails = DB::table('zone')
							->where('status', '=', '1')
							->where('country_id', '=', '99')
							->where('name', 'not like', '%All%')
							->get()->all();
        return $getDetails;
	}
	/**
	 * [getCustomerGroup Customer Group]
	 * @return [array] [CustomerGroup list]
	 */
	public function getCustomerGroup(){
		$getDetails = DB::table('master_lookup')
							->where('mas_cat_id', '=', '3')
							->where('is_active', '=', '1')
							->get()->all();
        return $getDetails;
	}

	// view data in Ignite UI grid
	public function viewPromotiondata($makeFinalSql, $statusFilter, $orderBy, $page, $pageSize){
		if($orderBy!=''){
			$orderBy = ' ORDER BY ' . $orderBy;
		}
		$sqlWhrCls = '';
		$countLoop = 0;
		foreach ($makeFinalSql as $value) {
			if( $countLoop==0 ){
				$sqlWhrCls .= 'WHERE ' . $value;
			}elseif( count($makeFinalSql)==$countLoop ){
				$sqlWhrCls .= $value;
			}else{
				$sqlWhrCls .= ' AND ' .$value;
			}
			$countLoop++;
		}

		if($statusFilter!=''){
			if($sqlWhrCls==''){
				$statusFilter = "WHERE " . $statusFilter;	
			}else{
				$statusFilter = " AND " . $statusFilter;
			}
		}

         
		$sqlQuery ="select *, 
    CONCAT('<center>
    <code>
    <a href=\"javascript:void(0)\" onclick=\"updateData(',prmt_tmpl_Id,')\">
    <i class=\"fa fa-pencil\"></i>
    </a>&nbsp;&nbsp;&nbsp;
    <a href=\"javascript:void(0)\" onclick=\"deleteData(',prmt_tmpl_Id,')\">
    <i class=\"fa fa-trash-o\"></i>
    </a>
    </code>
    </center>') 
    AS 'CustomAction',
    @rowcnt:=@rowcnt+1 AS 'SNO' from promotion_template, (SELECT @rowcnt:= 0) AS rowcnt ".$sqlWhrCls . $statusFilter .$orderBy;

        $allRecallData = DB::select(DB::raw($sqlQuery));
        $TotalRecordsCount = count($allRecallData);

        // prepare for limit
		if($page!='' && $pageSize!=''){
			$page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
			$allRecallData = array_slice($allRecallData, $page, $pageSize);
		}
	    return json_encode(array('results'=>$allRecallData, 'TotalRecordsCount'=>(int)($TotalRecordsCount)));
	}

	//save the promotion data in database
	public function savePromotionData($promotionData){
		$this->prmt_tmpl_name = $promotionData['promotion_name'];
        $this->offer_type = $promotionData['offertype'];
        $this->offer_on =$promotionData['offeron'];
        $this->status='Active';
        $this->is_slab = isset($promotionData['used_for_slab']) ? 1 : 0;
        if ($this->save()) {
        	return true;
        }else{
        	return false;
        }
	}

	/**
	 * [getUpdateData get the promotion template information]
	 * @param  [int] $updateId [template id]
	 * @return [array]           [promotion template information]
	 */
	public function getUpdateData($updateId){
		$getUpdateData = DB::table('promotion_template')
                ->where("promotion_template.prmt_tmpl_Id",$updateId)
                ->first();
        return $getUpdateData;
	}

	//update the promotion data in database
	/**
	 * [updatePromotionData Update promotion template info]
	 * @param  [array] $data [new template info]
	 * @return [boolean]       [true/false]
	 */
	public function updatePromotionData($data){
		$update_data = PromotionModel::find($data['prmt_tmpl_Id']);
    	$update_data->prmt_tmpl_name = $data['promotion_name'];
		$update_data->offer_type = $data['offertype'];
		$update_data->offer_on = $data['offeron'];
		$update_data->status = $data['status'];
		$update_data->is_slab = isset($data['used_for_slab']) ? 1 : 0;
		if ($update_data->save()){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * [deleteData To delete a promotion template]
	 * @param  [int] $deleteData [template id]
	 * @return [string]             [success/failue string]
	 */
	function deleteData($deleteData){
		$promotionData = PromotionModel::find($deleteData);
		if( $promotionData->delete() ){
      		return "Record Deleted";
     	}else{
      		return "Can not delete the record, due to some error ..";
     	}
			
	}
	/**
	 * [getpromotionData Get promotion information]
	 * @return [array] [promotion types array]
	 */
	public function getpromotionData(){
		$getpromotionData = DB::table('promotion_template')->get()->all();
        return $getpromotionData;
	}
	 /**
	 * [getstate State details]
	 * @return [array] [state information]
	 */
	public function getstate(){
		$getstate = DB::table('zone')->where('country_id', '=', 99)->get()->all();
        return $getstate;
	}

	/**
	 * [getBrandDetails brand group]
	 * @return [array] [brand details]
	 */
	public function getBrandDetails(){
        $getBrandDetails = DB::table('brands');
		if(Session::get('legal_entity_id')!=0){
			$getBrandDetails->where('legal_entity_id', '=', Session::get('legal_entity_id'));
		}
        return $getBrandDetails->get()->all();
    }
    /**
	 * [getManufactureDetails Manufacturer group]
	 * @return [array] [Manufacturer details]
	 */
    public function getManufactureDetails(){
        $getDetails = DB::table('legal_entities')
                            ->where('legal_entity_type_id', '=', '1006');
		if(Session::get('legal_entity_id')!=0){
			$getDetails->where('legal_entity_id', '=', Session::get('legal_entity_id'));
		}
        return $getDetails->get()->all();
    }
}