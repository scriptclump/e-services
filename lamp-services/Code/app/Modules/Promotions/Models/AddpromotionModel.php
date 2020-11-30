<?php
/*
FileName : AddpromotionModel.php
Author   :eButor
Description : All the function for Add / Update the promotion.
CreatedDate : 9/jun/2016
*/
//defining namespace
namespace App\Modules\Promotions\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Promotions\Models\AddpromotionModel;
use DB;
use Session;
use UserActivity;

class AddpromotionModel extends Model
{
    protected $table = 'promotion_details';
    protected $primaryKey = "prmt_det_id";

    /**
     * [getpromotionData Get promotion template information]
     * @return [array] [Promotion information]
     */
    public function getpromotionData(){
		$getpromotionData = DB::table('promotion_template')->get()->all();
        return $getpromotionData;
	}
	/**
	 * [getSelectFreeProduct Get products list]
	 * @return [array] [products list]
	 */
	public function getSelectFreeProduct(){
		$getSelectFreeProduct = DB::table('products')->get()->all();
		return $getSelectFreeProduct;
	}


	/**
	 * [saveNewPromotionData save the new promotion data in database]
	 * @param  [array] $newPromotionData [Promotion information]
	 * @param  [int] $entity_id        [Legal entity id]
	 * @param  [int] $created_by       [user id]
	 * @return [int]                   [On successful insertion sending promotion inserted id]
	 */
	public function saveNewPromotionData($newPromotionData,$entity_id,$created_by){
	

		$this->prmt_det_name = $newPromotionData['promotion_name'];
		$this->prmt_tmpl_Id = $newPromotionData['select_offer_tmpl'];
		
		$oldData = array(
			'NEWVALUES'   => 'Added Data',
		);
		// checking for the Conditon value in Promotion
		if( isset($newPromotionData['offertype']) ){
			
			if( $newPromotionData['offertype']=='FreeQty' ){
				// Setting values for Free Qty Promotion
				$this->prmt_offer_value = "0";
				$this->is_percent_on_free = "0";
				$this->prmt_free_qty = isset($newPromotionData['free_qty']) ? $newPromotionData['free_qty'] : '';
				if(isset($newPromotionData['select_product'])){
		        	$this->prmt_free_product = implode( ',', array_values($newPromotionData['select_product']));
		        }

			}elseif($newPromotionData['offertype']=='Discount'){
				// Setting values for Discount Promotion
				$this->prmt_offer_value = $newPromotionData['discount_offer'];
				$this->is_percent_on_free = isset($newPromotionData['offon_percent']) ? 1 : 0;
				$this->prmt_free_qty = '';
		        $this->prmt_free_product = '';
			}else{
				// Defence check if No Condition
				$this->prmt_offer_value = '0';
				$this->is_percent_on_free =  isset($newPromotionData['offon_percent']) ? 1 : 0;
				$this->prmt_free_qty = '';
		        $this->prmt_free_product = '';
			}
		}else{
			// If there is no condition available ( like Slab promotion )
			$this->prmt_offer_value = '0';
			$this->is_percent_on_free =  '0';
			$this->prmt_free_qty = '';
	        $this->prmt_free_product = '';
		}


        //$this->prmt_offer_value = is_array( $newPromotionData['discount_offer']) ? "" : $newPromotionData['discount_offer'];
        $this->start_date =$newPromotionData['start_date'];
        $this->end_date =$newPromotionData['end_date'];
        $this->prmt_lock_qty =$newPromotionData['prmt_lock_qty'];
        
        $this->prmt_description = $newPromotionData['description'];
        $this->prmt_label = $newPromotionData['label'];
        $this->prmt_det_status = isset($newPromotionData['promotion_status']) ? 1 : 0;
        $this->is_repeated = isset($newPromotionData['is_repeated']) ? 1 : 0;
        $this->offon_free_product = isset($newPromotionData['offon_free_product']);
        $this->legal_entity_id = $entity_id;
        $this->prmt_condition_value1 = $newPromotionData['set_qty'];
      	$this->prmt_offer_on = $newPromotionData['gridCallType'];
      	$this->warehouse = implode( ',', $newPromotionData['warehouse_details']);

      	if( $newPromotionData['select_offer_tmpl']!='5' ){
	      	if(isset($newPromotionData['bill_value'])){
	      		$this->prmt_condition_value2 = isset($newPromotionData['bill_value']) ? $newPromotionData['bill_value'] : 0;

	      	}else{
	      		$this->prmt_condition_value2 = is_array( $newPromotionData['value_two']) ? "" : $newPromotionData['value_two'];
	      	}
	    }
	    if( $newPromotionData['select_offer_tmpl']!='7' ){
	      	if(isset($newPromotionData['bill_value'])){
	      		$this->prmt_condition_value2 = isset($newPromotionData['trade_from_range']) ? $newPromotionData['trade_from_range'] : 0;

	      	}else{
	      		$this->prmt_condition_value2 = is_array( $newPromotionData['trade_to_range']) ? "" : $newPromotionData['trade_to_range'];
	      	}
	    }
      	
        if( $newPromotionData['select_offer_tmpl']=='1' ){
        	$this->prmt_offer_type = "Slab";
        	
            foreach ($newPromotionData['value_two'] as $key => $value) {

	        	$this->product_star = 	implode( ',', array_values($newPromotionData['product_star_color_table']));
	        	$this->pack_type	=	implode( ',', array_values($newPromotionData['pack_number_table']));
	        	$this->esu          =	implode( ',', array_values($newPromotionData['pack_value_table']));
	        }


        }else if( $newPromotionData['select_offer_tmpl']=='4' ){
        	$this->prmt_offer_type = "OnBill";
        	$this->prmt_offer_on   = "Bill";
        	//$this->is_percent_on_free = '1';
        	$this->prmt_condition_value1=0;        	
        	$this->prmt_offer_value=$newPromotionData['discount_offer_bill'];

        	$this->is_percent_on_free =  isset($newPromotionData['offon_percent']) ? 1 : 0;

        }else if($newPromotionData['select_offer_tmpl']=='5' || $newPromotionData['select_offer_tmpl']=='7'){
        	
        	$this->prmt_offer_type = "CashBack";
        	$this->prmt_offer_on   = "Bill";
        	
        	/*$this->prmt_condition_value1=$newPromotionData['cash_back_from'];
        	$this->prmt_condition_value2=$newPromotionData['cash_back_to'];        	
        	$this->prmt_offer_value=$newPromotionData['discount_offer_on_bill'];
        	$this->is_percent_on_free =  isset($newPromotionData['offon_percent']) ? 1 : 0;	

        	if(isset($newPromotionData['offertypemanf']))
		    {
		    	$this->prmt_manufacturers =  implode( ',', array_values($newPromotionData['offertypemanf']));
		    }
		    if(isset($newPromotionData['offertypbrand']))
		    {
		    	$this->prmt_brands =  implode( ',', array_values($newPromotionData['offertypbrand']));
		    }	
		    if(isset($newPromotionData['ProductStar']))
		    {
		    	$this->product_star =  implode( ',', array_values($newPromotionData['ProductStar']));
		    }
		    if(isset($newPromotionData['Benificiary']))
		    {
		    	$this->order_type =  implode( ',', array_values($newPromotionData['Benificiary']));
 
		    }
		    if(isset($newPromotionData['wareHouseId']))
		    {
		    	$this->warehouse =  implode( ',', array_values($newPromotionData['wareHouseId']));
		    }
*/
        }else{
        	$this->prmt_offer_type = isset($newPromotionData['offertype']) ? $newPromotionData['offertype'] : '';
        }

        if( $newPromotionData['select_offer_tmpl']=='1' || $newPromotionData['select_offer_tmpl']=='5' ){
        	$this->prmt_condition = "Range";
        }else{
        	$this->prmt_condition = "=";
        }

        $this->created_by = $created_by;

        // State and customer group is common for every temp
        // but for Cashback (5) we are taking from the Grid table not from the dropdown
        // That is why the below condition applied
        $this->prmt_states = implode( ',', $newPromotionData['state'] );

        if($newPromotionData['select_offer_tmpl']!='5'){
        	$this->prmt_customer_group = implode( ',', $newPromotionData['customer_group'] );
        }
        
        if(isset($newPromotionData['item_id']))
        {
        	$this->applied_ids =  implode( ',', array_values($newPromotionData['item_id']));
        }

        // This for for On bill discount, because of the template section we had to give different name to the control
        // even though we have same option in Cashback but with differnt name of the control
        // So condition written for individual promotion
     	if($newPromotionData['select_offer_tmpl']=='4' )
	    { 		
	        if(isset($newPromotionData['offertypeman']))
	        {
	        	$this->prmt_manufacturers =  implode( ',', array_filter($newPromotionData['offertypeman']));
	        }
	        if(isset($newPromotionData['offertypebrand']))
	        {
	        	$this->prmt_brands =  implode( ',', array_filter($newPromotionData['offertypebrand']));
	        }
	        if(isset($newPromotionData['ProductStar_on_bill']))
	        {
	        	$product_bill = array_filter($newPromotionData['ProductStar_on_bill'], function($value) {
		   		 return ($value !== null && $value !== false && $value !== ''); 
				});
	        	$this->product_star =  implode( ',', $product_bill);
        	}
	    }
	    // This is specific for Cashback
	    if($newPromotionData['select_offer_tmpl']=='5' )
	    { 		
	        if(isset($newPromotionData['offertypemanf_table']))
	        {
	        	$this->prmt_manufacturers =  implode( ',', array_filter($newPromotionData['offertypemanf_table']));
	        }

	        if(isset($newPromotionData['offertypbrand_table']))
	        {
	        	$this->prmt_brands =  implode( ',', array_filter($newPromotionData['offertypbrand_table']));
	        }
	       /* if(isset($newPromotionData['state_table']))
	        {
	        	$this->prmt_states =  implode( ',', array_values($newPromotionData['state_table']));
	        }*/
	        if(isset($newPromotionData['customer_group_table']))
	        {
	        	$this->prmt_customer_group =  implode( ',', array_values($newPromotionData['customer_group_table']));
	        }
	        if(isset($newPromotionData['Benificiary_table']))
	        {
	        	$this->order_type =  implode( ',', array_filter($newPromotionData['Benificiary_table']));
	        }
	        if(isset($newPromotionData['ProductStar_table']))
	        {

	        	$product = array_filter($newPromotionData['ProductStar_table'], function($value) {
		   		 return ($value !== null && $value !== false && $value !== ''); 
				});
	        	$this->product_star =  implode( ',', $product);
	        }
	        if(isset($newPromotionData['wareHouseId_table']))
	        {
	        	$this->warehouse =  implode( ',', array_filter($newPromotionData['wareHouseId_table']));
	        }        
	        if(isset($newPromotionData['cash_back_from_table']))
	        {
	        	$this->prmt_condition_value1 =  implode( ',', array_values($newPromotionData['cash_back_from_table']));
	        }
	        if(isset($newPromotionData['cash_back_to_table']))
	        {
	        	$this->prmt_condition_value2 =  implode( ',', array_values($newPromotionData['cash_back_to_table']));
	        }
	        if(isset($newPromotionData['discount_offer_on_bill_table']))
	        {
	        	$this->prmt_offer_value =  implode( ',', array_values($newPromotionData['discount_offer_on_bill_table']));
	        }
	        if(isset($newPromotionData['offon_percent_table']))
	        {
	        	$this->is_percent_on_free =  implode( ',', array_values($newPromotionData['offon_percent_table']));
	        }

	    }

	   
        if ($this->save()) {
        	 //UserActivity::userActivityLog('Promotions', $newPromotionData, 'Promotions Data added by the User'); 
        	return $this->prmt_det_id;
        }else{
        	return false;
        }
	}
	/**
	 * [deletepromotiondetails To delete promotion]
	 * @param  [int] $deleteData [Promotion id]
	 * @return [string]             [success or failure message]
	 */
	public function deletepromotiondetails($deleteData){

		$promotionData = AddPromotionModel::find($deleteData);
		$oldData = array(
			'OLDVALUES'   =>  json_decode(json_encode($promotionData)),
			'NEWVALUES'   => 'Deleted data',
		);
     
		if( $promotionData->delete() ){
			UserActivity::userActivityLog('Promotions', $oldData, 'Promotions Data Deleted by the User');
      		return "Record Deleted";
     	}else{
      		return "Can not delete the record, due to some error ..";
     	}
	}
	/**
	 * [getProductDetailsWithOffertype Ig grid related filter function]
	 */
	public function getProductDetailsWithOffertype($makeFinalSql, $page, $pageSize, $orderBy, $notindata, $calltype){
		if($orderBy!=''){
			$orderBy = ' ORDER BY ' . $orderBy;
		}

		$legalEntiryQuery = "";
		if(Session::get('legal_entity_id')!=0){
			$legalEntityQuery = " WHERE legal_entity_id='" . Session::get('legal_entity_id') . "'";
		}

		//check for Not In ProductID
		$sqlWhrCls = "";
		if($notindata!=''){
			$notindataArray=array_filter(explode(",", $notindata));
			$notindata=implode($notindataArray, ",");
			$sqlWhrCls .= " WHERE innertbl.item_id not in(".$notindata.")";
		}

		if( isset($makeFinalSql[0]) ){
			$sqlWhrCls .= $sqlWhrCls=="" ? " WHERE" : " AND";
			$sqlWhrCls .= " innertbl.".$makeFinalSql[0];	
		}

		$calltype = 'Product';
		if($calltype == 'Product'){
			// run the query for total count
			$sqlQuery ="select COUNT(*) AS cnt FROM
			(
			SELECT prd.product_id AS 'item_id', CONCAT('<img height=\"50\" width=\"50\" src=\"', prd.primary_image,'\"><br>Product : ', prd.product_title, '<br>SKU : ',  prd.sku) AS 'list_details'
			FROM products AS prd ". $legalEntiryQuery ."
			) AS innertbl". $sqlWhrCls;
			$allData = DB::select(DB::raw($sqlQuery));
			$countTotalRows = $allData[0]->cnt;

			// Query for the Total Records
			$sqlQuery ="select * FROM
			(
				SELECT prd.product_id AS 'item_id', 
				CONCAT('<div style=\"float:left; padding-right: 10px;\"><img height=\"50\" width=\"50\" src=\"', prd.primary_image,'\"></div><div>Product : ', 
				prd.product_title, '<br>SKU : ',  prd.sku, ' MRP : ', prd.mrp,
				(SELECT IFNULL(CONCAT('<br><span style=\"color:red;\">Promotions : ',GROUP_CONCAT(prmtdet.prmt_det_name), '</span>'),'')
				FROM promotion_details AS prmtdet
				WHERE FIND_IN_SET(prd.product_id, prmtdet.applied_ids) and prmtdet.prmt_det_status=1), '</div>') AS 'list_details'
				FROM products AS prd " . $legalEntiryQuery . "
			) AS innertbl". $sqlWhrCls;

		}elseif($calltype == 'category'){
			
			// run the query for total count
			$sqlQuery ="select COUNT(*) AS cnt FROM
			(
			SELECT cat.category_id AS 'item_id', cat.cat_name AS 'list_details'
			FROM `categories` AS cat 
			) AS innertbl ". $sqlWhrCls;
			$allData = DB::select(DB::raw($sqlQuery));
			$countTotalRows = $allData[0]->cnt;

			// Query for the Total Records
			$sqlQuery ="select * FROM
			(
				SELECT cat.category_id AS 'item_id', 
				CONCAT(cat.cat_name, 
				(SELECT IFNULL(CONCAT('<br><span style=\"color:red;\">Promotions : ',GROUP_CONCAT(prmtdet.prmt_det_name), '</span>'),'')
				FROM promotion_details AS prmtdet
				WHERE FIND_IN_SET(cat.category_id, prmtdet.applied_ids) and prmtdet.prmt_det_status=1)
				 ) AS 'list_details'
				FROM `categories` AS cat 
			) AS innertbl ". $sqlWhrCls;

		}

		$pageLimit = '';
		if($page!='' && $pageSize!=''){
			$pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
		}

		$allData = DB::select(DB::raw($sqlQuery . $pageLimit));
		return json_encode(array('results'=>$allData, 'TotalRecordsCount'=>(int)( $countTotalRows )));
	}
	/**
	 * [getWarehouseData get warehouse information]
	 * @param  [string] $data [comma seperated warehouses]
	 * @return [array]       [warehouse list]
	 */
	public function getWarehouseData($data){
        $query="select le_wh_id,legal_entity_id,le_wh_code,CONCAT(lp_wh_name,' ','(',le_wh_code,')') as 'name' from legalentity_warehouses where le_wh_id In(".$data.")";
        $queryResult=DB::select(DB::raw($query));
        $query=$queryResult;
        return $query;
    }
    /**
     * [getProductData Get free bee products]
     * @return [array] [free bee products]
     */
    public function getProductData(){
    	return DB::select(DB::raw("select * from vw_product_freebee"));
    }
    /**
     * [getBrandsData Get brands data on the basis of manufacturer]
     * @param  [int] $manf [manufacturer id]
     * @return [array]       [brands data]
     */
    public function getBrandsData($manf){
    	$brandsData = DB::table('brands')
			    	->select('brand_id','brand_name');
		if (!in_array(0, $manf)){
			$brandsData =$brandsData->whereIn('mfg_id',$manf);
		}
		$brandsData = $brandsData->get()->all();
		return $brandsData;
    }
}
