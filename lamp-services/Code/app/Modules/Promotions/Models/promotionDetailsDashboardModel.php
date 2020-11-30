<?php
/*
FileName : promotionDetailsDashboardModel.php
Author   :eButor
Description : All the function for Add / Update the promotion.
CreatedDate : 9/sept/2016
*/
//defining namespace
namespace App\Modules\Promotions\Models;

use DB;
use Session;

class promotionDetailsDashboardModel
{
	// view data in Ignite UI grid
	public function showpromotionsDetails($makeFinalSql, $statusFilter, $orderBy, $page, $pageSize,$editAccess,$deleteAccess){

		//echo "<pre/>";print_r($makeFinalSql);
		//exit;

		if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }else{
            $orderBy = ' ORDER BY end_date desc';
        }

        
		$sqlWhrCls = '';
		$countLoop = 0;

		$legalEntityQuery = "";
		if(Session::get('legal_entity_id')!=0){
			$legalEntityQuery=" WHERE legal_entity_id='" . Session::get('legal_entity_id') . "'";
		}
		foreach ($makeFinalSql as $value) {
			if( $countLoop==0 ){
				$sqlWhrCls .= 'AND ' . $value;
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

		$concatQueryFirst = "CONCAT('<center><code>',";
		$editQuery = "'<a href=\"javascript:void(0)\" onclick=\"updateDetailsData(',prmt_det_id,')\">
						<i class=\"fa fa-pencil\"></i>
						</a>&nbsp;&nbsp;&nbsp;',";
		$deleteQuery = "'<a href=\"javascript:void(0)\" onclick=\"deleteDetailsData(',prmt_det_id,')\">
					   <i class=\"fa fa-trash-o\"></i>
					   </a>&nbsp;&nbsp;&nbsp;',";
		$concatQuerySecond ="'</code>
						</center>') 
						AS 'CustomAction'";

		$concatQuery = $concatQueryFirst;
		if($editAccess== 1){
			$concatQuery .= $editQuery;
		}
		if($deleteAccess== 1){
			$concatQuery .= $deleteQuery;
		}
		$concatQuery .= $concatQuerySecond;

		$sqlQuery ="select * from vw_promotion_grid ".$legalEntityQuery.$sqlWhrCls . $statusFilter .$orderBy;

        $allRecallData = DB::select(DB::raw($sqlQuery));
        $TotalRecordsCount = count($allRecallData);

        // prepare for limit
		if($page!='' && $pageSize!=''){
			$page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
			$allRecallData = array_slice($allRecallData, $page, $pageSize);
		}
	    return json_encode(array('results'=>$allRecallData, 'TotalRecordsCount'=>(int)($TotalRecordsCount)));
	}
	/**
	 * [getStateDetailsDropdown State details]
	 * @return [array] [state information]
	 */
	public function getStateDetailsDropdown(){
		$getDetails = DB::table('zone')
							->where('status', '=', '1')
							->where('country_id', '=', '99')
							->where('name', 'not like', '%All%')
							->orderBy("sort_order")
							->get()->all();
        return $getDetails;
	}
	/**
	 * [getCustomerGroupDropdown Customer group details]
	 * @return [array] [Customer group info]
	 */
	public function getCustomerGroupDropdown(){
		$getDetails = DB::table('master_lookup')
							->where('mas_cat_id', '=', '3')
							->where('is_active', '=', '1')
							->orderBy("master_lookup_name")
							->get()->all();
        return $getDetails;
	}
	/**
	 * [getManufactureDetailsDropdown Manufacturer group]
	 * @return [array] [Manufacturer details]
	 */
	public function getManufactureDetailsDropdown(){
        $getDetails = DB::table('legal_entities')
							->where('legal_entity_type_id', '=', '1006')
							->orderBy("business_legal_name");							
        return $getDetails->get()->all();
        
    }
    /**
     * [getBrandDetailsDropdown Brand details]
     * @return [array] [brand information]
     */
    public function getBrandDetailsDropdown(){
        $getBrandDetails = DB::table('brands');
		if(Session::get('legal_entity_id')!=0){
			$getBrandDetails->where('legal_entity_id', '=', Session::get('legal_entity_id'))
							->orderBy("brand_name");
		}
        return $getBrandDetails->get()->all();
    }
    /**
     * [getProductStar get product stars list]
     * @return [array] [product star information]
     */
    public function getProductStar(){
    	 $getProduct = DB::table('master_lookup')
    	 			->where('mas_cat_id', '=', '140')
    	 			->get()->all();
    	 
    	 $getProduct = json_decode( json_encode($getProduct), true);
    	 return $getProduct;

    }
    /**
     * [getProductForBill get product stars list]
     * @return [array] [product star information]
     */
    public function getProductForBill(){
    	 $getProductBill = DB::table('master_lookup')
    	 			->where('mas_cat_id', '=', '140')
    	 			->get()->all();
    	return $getProductBill;
    }
    /**
     * [getOrderType Get roles list]
     * @return [array] [Roles list]
     */
    public function getOrderType(){
    	$getOrderType = DB::table('roles')
    				->get()->all();
    	
    	$getOrderType = json_decode( json_encode($getOrderType), true);
    	 return $getOrderType;

    }
    /**
     * [getWareHouseId Warehouse information]
     * @return [array] [Warehouse list]
     */
    public function getWareHouseId(){
    	$getWareHouseId =  DB::table('legalentity_warehouses')
    					->where('lp_name', '=', 'Custom')
    					->where('status','=',1)
    					->where('dc_type','=',118001)
    					->get()->all();
    					
    	$getWareHouseId = json_decode( json_encode($getWareHouseId), true);
    	 return $getWareHouseId;

    }
    /**
     * [getPackdataForProduct Pack information of a product]
     * @param  [int] $id [product id]
     * @return [array]     [pack information]
     */
    public function getPackdataForProduct($id){
    	$getPackQuery  ="select p.`product_id`, p.`level`,p.`star`,p.`esu`,m.`master_lookup_name`, p.`no_of_eaches`, 
						CONCAT( m.`master_lookup_name`,' ', '( QTY: ', p.`no_of_eaches`, ' )' ) AS 'DPValue',
						(SELECT mi.`description` FROM master_lookup mi WHERE mi.value=p.star) AS 'StarColor'
						FROM product_pack_config p 
						INNER JOIN master_lookup m ON m.`value`=p.`level`
						WHERE p.product_id=$id and p.is_sellable=1";
		$getPackData    = DB::select(DB::raw($getPackQuery));

		return $getPackData;

    }
    /**
     * [getProductId Get product id's to which promotion is applying]
     * @param  [int] $id [promotion id]
     * @return [string]     [comma seperated id's]
     */
    public function getProductId($id){
    	$productQuery="select applied_ids from promotion_details where prmt_det_id=$id";
    	$getId    = DB::select(DB::raw($productQuery));
    	return $getId[0]->applied_ids; 							
    }
    /**
     * [getcashBackdataFromTable Cashback promotion information]
     * @param  [int] $updateId [promotion id]
     * @return [array]           [promotion info]
     */
    public function getcashBackdataFromTable($updateId){
    	$sqlQuery = "select pd.`cbk_id`, pd.`cbk_label`,pd.product_group_id,
    	(SELECT GROUP_CONCAT(product_grp_name) from product_groups where FIND_IN_SET(product_grp_id,pd.product_group_id)) as 'prod_group_name', pd.range_from,pd.manufacturer_id ,
    	(SELECT GROUP_CONCAT(business_legal_name) FROM legal_entities le WHERE FIND_IN_SET(
      le.legal_entity_id,pd.manufacturer_id)) AS 'manfName',pd.cap_limit as cap_limit,pd.product_value as product_value,pd.is_self as is_self,
    	pd.brand_id ,(SELECT GROUP_CONCAT(brand_name) FROM brands br WHERE FIND_IN_SET(br.brand_id , pd.brand_id)) AS 'brandName', pd.excl_brand_id, (SELECT GROUP_CONCAT(brand_name) FROM brands br WHERE FIND_IN_SET(br.brand_id , pd.excl_brand_id)) AS 'excl_brandName',
				pd.`state_id`, (SELECT GROUP_CONCAT(NAME) FROM zone zn WHERE FIND_IN_SET(zn.zone_id,pd.`state_id`)) AS 'StateName',
				pd.`customer_type`, (SELECT GROUP_CONCAT(master_lookup_name) FROM master_lookup mst WHERE FIND_IN_SET(mst.value,pd.`customer_type`)) AS 'CustomerType',pd.`cbk_label` AS 'Description',
				pd.`wh_id`, (SELECT GROUP_CONCAT(lp_wh_name) FROM legalentity_warehouses lw WHERE FIND_IN_SET(lw.le_wh_id,pd.wh_id) ) AS 'WareHouse',
				pd.`benificiary_type`, (SELECT NAME FROM roles rl WHERE rl.role_id=pd.`benificiary_type` ) AS 'Benificiary',
				pd.`product_star`, if(pd.`product_star`=0, 'All', (SELECT master_lookup_name FROM master_lookup mst WHERE mst.value=pd.`product_star`)) AS 'ProductStar',
				DATE_FORMAT(pd.`start_date`,'%d-%m-%Y') AS 'StartDate', DATE_FORMAT(pd.`end_date`,'%d-%m-%Y') AS 'endDate', pd.`range_to`,
				pd.`cbk_type`, if(pd.`cbk_type`=1, '%', '&#8377;') as 'cbk_type_txt', pd.`cbk_value`,pd.excl_category_id,(SELECT GROUP_CONCAT(cat_name) from categories where FIND_IN_SET(category_id,pd.excl_category_id)) as 'excl_cat_name',pd.excl_prod_group_id, (SELECT GROUP_CONCAT(product_grp_name) from product_groups where FIND_IN_SET(product_grp_ref_id,pd.excl_prod_group_id)) as 'excl_prod_group_name',pd.excl_manf_id,(SELECT GROUP_CONCAT(business_legal_name) from legal_entities where FIND_IN_SET(legal_entity_id,pd.excl_manf_id)) as excl_man_name
				FROM promotion_cashback_details  AS pd  
				WHERE pd.`cbk_ref_id`=$updateId";
		$allData = DB::select(DB::raw($sqlQuery));
        return $allData;
    }

    /**
     * [getAllPromotionsActiveInactive  get all the active and inactive data]
     * @return [array] [Promotions list]
     */
    public function getAllPromotionsActiveInactive(){

		$sqlQuery ="select * from vw_promotion_inactive_report";
        $allRecallData = DB::select(DB::raw($sqlQuery));
        return $allRecallData;

    }
    /**
     * [getData Get pack information]
     * @param  [int] $product_id [product id]
     * @return [array]             [pack information of product]
     */
    public function getData($product_id){
     	$getPackQuery  ="select  p.`level`,m.`master_lookup_name`
						FROM product_pack_config p 
						INNER JOIN master_lookup m ON m.`value`=p.`level`
						WHERE p.product_id=$product_id";
		$getPackData    = DB::select(DB::raw($getPackQuery));

		return $getPackData;
	}
	/**
	 * [getTradeDataItems Get trade discount items on basis of trade type]
	 * @param  [int] $id [trade type]
	 * @return [array]     [trade items list]
	 */
	public function getTradeDataItems($id){
		$getData = DB::select(DB::raw("CALL getTradeItems(".$id.")"));
		return $getData;
	}
	/**
	 * [productGroupData Get Product group list]
	 * @return [array] [Product groups list]
	 */
	public function productGroupData(){
		$query = DB::table('product_groups')->select(['product_grp_ref_id','product_grp_name'])->orderBy('product_grp_name','asc')->groupBy('product_grp_name')->get()->all();
		return $query;
	}
	/**
	 * [categorieGroupData GEt categories list]
	 * @return [array] [category list]
	 */
	public function categorieGroupData(){
		$query = DB::table('categories')->select(['category_id','cat_name'])->orderBy('cat_name','asc')->groupBy('cat_name')->get()->all();
		return $query;
	}
	/**
	 * [manufactureGroupData Get manufacturer list]
	 * @return [array] [manufacturer list]
	 */
	public function manufactureGroupData(){
		$query = DB::table('legal_entities')->select('legal_entity_id','business_legal_name')->where('legal_entity_type_id','=',1006)->groupBy('business_legal_name')->orderBy('business_legal_name','asc')->get()->all();
		return $query;
	}
}


