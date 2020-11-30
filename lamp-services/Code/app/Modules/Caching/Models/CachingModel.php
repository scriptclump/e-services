<?php
namespace App\Modules\Caching\Models;

use DB;
use Log;
use Illuminate\Database\Eloquent\Model;

class CachingModel extends Model {

	public function __construct()
	{

	}

	// The Below Function fetchs all the Beats with there Spoke Names
	public function getBeatInfo($ids = null)
	{
		// If the beat id is un mapped
		if($ids == "0")
			return null;

		$query = DB::table('pjp_pincode_area')
				->select(
					'pjp_pincode_area.pjp_pincode_area_id as beat_id',
					'pjp_pincode_area.pjp_name as beat_name',
					'spokes.spoke_name as spoke_name'
					)
	            ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
	            ->where('pjp_pincode_area.pjp_pincode_area_id','>',0);
	            
		if($ids == null)
			return $query->get()->all();

		else if(is_array($ids))
			return $query->whereIn('pjp_pincode_area.pjp_pincode_area_id',$ids)->get()->all();

		else
			return $query->where('pjp_pincode_area.pjp_pincode_area_id','=',$ids)->get()->all();
		
	}


	// The Below Function fetchs all the active products ...
	public function getProductInfo($productsArr = null)
	{
		
		$result = DB::table('products')
				->select('product_id','product_title','sku as product_sku');

		if($productsArr == null)
			$result = $result->where([['cp_enabled', '=', '1'],['is_sellable', '=', '1'],])->get()->all();
		else if(is_array($productsArr))
		{
			$productIds = array_map('intval', explode(',',$productsArr));
			$result = $result->where([['cp_enabled', '=', '1'],['is_sellable', '=', '1'],])->whereIn('product_id',$productIds)->get()->all();
		}
		else
			$result = $result->where([['cp_enabled', '=', '1'],['is_sellable', '=', '1'],['product_id','=',$productsArr],])->get()->all();
		
		return $result;
	}

	// The Below Function fetchs all the active brands ...
	public function getBrandInfo($brand_id = null)
	{
		if($brand_id == null)
			return DB::table('brands')
				->select('brand_id','brand_name')
				->where('is_active',1)
				->get()->all();
		else
			return DB::table('brands')
					->select('brand_name as item_name')
					->where('brand_id',$brand_id)
					->get()->all();
	}

	// The Below Function fetchs all the active brands ...
	public function getFfUsersInfo($user_id = null)
	{
		if($user_id == null)
			return DB::table('users')
					->select('users.user_id','users.firstname','users.lastname')
					->leftJoin('user_roles', 'users.user_id', '=', 'user_roles.user_id')
					->where('users.is_active',1)
					->where('users.legal_entity_id',2)
					->where('user_roles.role_id',53)
					->get()->all();
		else
			return DB::table('users')
					->select('user_id','firstname','lastname')
					->leftJoin('user_roles', 'users.user_id', '=', 'user_roles.user_id')
					->where('users.is_active',1)
					->where('users.user_id',$user_id)
					->where('users.legal_entity_id',2)
					->where('user_roles.role_id',53)
					->get()->all();
	}

	// The Below Function fetchs all the active Dc`s ...
	public function getDcInfo($dc_id = null)
	{
		// The DC Type 118001, is to retrieve only Dc`s
		if($dc_id == null)
			return DB::table('legalentity_warehouses')
				->select('le_wh_id','lp_wh_name')
				->where([['status',1],['dc_type',118001],])
				->get()->all();
		else
			return DB::table('legalentity_warehouses')
				->select('lp_wh_name as dc_name','le_wh_id')
				->where('le_wh_id',$dc_id)
				->get()->all();

	}

	// The Below Function fetchs all the active Hub`s ...
	public function getHubInfo($hub_id = null)
	{
		// The DC Type 118002, is to retrieve only Hub`s
		if($hub_id == null)
			return DB::table('legalentity_warehouses')
				->select('le_wh_id','lp_wh_name')
				->where([['status',1],['dc_type',118002],])
				->get()->all();
		else
			return DB::table('legalentity_warehouses')
					->select('lp_wh_name as hub_name')
					->where('le_wh_id',$hub_id)
					->get()->all();


	}

	// The Below Function fetchs all the active Beats
	public function getCategoryInfo($cat_id = null)
	{
		if($cat_id == null)
			return DB::table('categories')
				->select(
					'category_id',
					'cat_name'
					)
				->where('is_active',1)
				->get()->all();
		else
			return DB::table('categories')
				->select('cat_name as item_name')
				->where('category_id',$cat_id)
				->get()->all();
	}

	// The Below Function fetchs all the active Manufacturer`s ...
	public function getManufacturerInfo($man_id = null)
	{
		// The Legal Entity Type Id 1006, is to retrieve only Manufacturer`s
		if($man_id == null)
			return DB::table('legal_entities')
				->select('legal_entity_id','business_legal_name')
				->where('legal_entity_type_id',1006)
				->get()->all();
		else
			return DB::table('legal_entities')
				->select('business_legal_name as item_name')
				->where('legal_entity_id',$man_id)
				->get()->all();
	}

	// The Below Function fetchs all the active Retailer`s ...
	public function getRetailerInfo($man_id = null)
	{
		// The Legal Entity Type Id '%30%', is to retrieve only Retailer`s
		if($man_id == null or $man_id == ''){
			return DB::table('users')
				->select(
					'users.user_id as retailer_id',
					DB::RAW("CONCAT(users.firstname,' ',users.lastname) as retailer_name")
					)
				->leftJoin('legal_entities','legal_entities.legal_entity_id','=','users.legal_entity_id')
				->where('legal_entities.legal_entity_type_id','LIKE','%30%')
				->where('users.is_active',1)
				->get()->all();
		}
		elseif(intval($man_id)){
			return DB::table('users')
				->select(
					'users.user_id as retailer_id',
					DB::RAW("CONCAT(users.firstname,' ',users.lastname) as retailer_name")
					)
				->where('users.user_id',$man_id)
				->get()->all();
		}
		else{
			return DB::table('users')
				->select(
					'users.user_id as retailer_id','users.mobile_no as retailer_number',
					DB::RAW("CONCAT(users.firstname,' ',users.lastname) as retailer_name")
					)
				->leftJoin('legal_entities','legal_entities.legal_entity_id','=','users.legal_entity_id')
				->where('legal_entities.legal_entity_type_id','LIKE','%30%')
				->where('users.is_active',1)
				->where(DB::RAW("CONCAT(users.firstname,'',users.lastname)"),'LIKE','%'.$man_id.'%')
				->get()->all();
		}
		/*"select CONCAT(qcebutor.users.firstname,'',qcebutor.users.lastname) as name from qcebutor.users left JOIN qcebutor.legal_entities 
		on qcebutor.legal_entities.legal_entity_id = qcebutor.users.legal_entity_id
		where qcebutor.legal_entities.legal_entity_type_id LIKE '%30%' "*/
	}

	public function getSegmentNameById($segment_id=0)
	{
		if($segment_id!=0)
			return DB::table('master_lookup')
				->select('master_lookup_name')
				->where('value',$segment_id)
				->first();
	}

	public function getCustomerType($typeId=0)
	{	
		$query = DB::table('master_lookup')
				->select('value','master_lookup_name')
				->where('is_active',1)
				->where('mas_cat_id',3);

		if($typeId!=0)
			$query = $query
					->where('value',$typeId)
					->get()->all();
		else
			$query = $query->get()->all();

		return $query;
	}

	public function getDynamicKeysList()
	{
		$query = DB::table('dynamic_cache_keys')
				->select('pattern','key_title')
				->get()->all();
		
		return $query;			
	}
	public function getSegments(){
		$query = DB::table('master_lookup')
				->where('mas_cat_id',48)
				->pluck('master_lookup_name','value')->all();
		return $query;
	}
}
