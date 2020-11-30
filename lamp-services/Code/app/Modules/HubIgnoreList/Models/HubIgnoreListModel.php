<?php

namespace App\Modules\HubIgnoreList\Models;

use DB;
use Log;
use Illuminate\Database\Eloquent\Model;

class HubIgnoreListModel extends Model{

	public function __construct()
	{
	}

	// The Below Function fetchs all the active Brands
	public function getBrandInfo($ids = null)
	{
		if($ids == null)
			return DB::table('brands')
					->select('brand_id','brand_name')
					->where('is_active',1)
					->get()->all();

		else if(is_array($ids))
			return DB::table('brands')
					->select('brand_id','brand_name as item_name')
					->where('is_active',1)
					->whereIn('brand_id',$ids)
					->get()->all();

		else
			return DB::table('brands')
					->select('brand_name')
					->where([['is_active','=',1],['brand_id','=',$ids],])
					->get()->all();
	}

	// The Below Function fetchs all the Beats with there Spoke Names
	public function getBeatInfo($ids = null)
	{
		if($ids == null)
			return DB::table('pjp_pincode_area')
				->select(
					'pjp_pincode_area.pjp_pincode_area_id as beat_id',
					'pjp_pincode_area.pjp_name as beat_name',
					'spokes.spoke_name as spoke_name'
					)
	            ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
	            ->where('pjp_pincode_area.pjp_pincode_area_id','>',0)
			  ->get()->all();

		else if(is_array($ids))
			return DB::table('pjp_pincode_area')
				->select(
					'pjp_pincode_area.pjp_pincode_area_id as beat_id',
					'pjp_pincode_area.pjp_name as beat_name',
					'spokes.spoke_name as spoke_name'
					)
	            ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
	            ->where('pjp_pincode_area.pjp_pincode_area_id','>',0)
	            ->whereIn('pjp_pincode_area.pjp_pincode_area_id',$ids)
			  ->get()->all();

		else
			return DB::table('pjp_pincode_area')
				->select(
					'pjp_pincode_area.pjp_pincode_area_id as beat_id',
					'pjp_pincode_area.pjp_name as beat_name',
					'spokes.spoke_name as spoke_name'
					)
	            ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
	            ->where('pjp_pincode_area.pjp_pincode_area_id','=',$ids)
			  ->get()->all();
	}

	// The Below Function fetchs all the Spokes
	public function getSpokeInfo($ids = null)
	{
		if($ids == null)
			return DB::table('spokes')
					->select('spoke_id','spoke_name')
					->get()->all();

		else if(is_array($ids))
			return DB::table('spokes')
					->select('spoke_id','spoke_name')
					->whereIn('spoke_id',$ids)
					->get()->all();

		else
			return DB::table('spokes')
					->select('spoke_name')
					->where('spoke_id',$ids)
					->get()->all();
						
	}

	// The Below Function fetchs all the active products ...
	public function getProductInfo($ids = null)
	{
		if($ids == null)
			return DB::table('products')
				->select('product_id','product_title')
				->where([['cp_enabled', '=', '1'],['is_sellable', '=', '1'],['is_active', '=', '1'],])
				->get()->all();

		else if(is_array($ids))
			return DB::table('products')
				->select('product_id','product_title as item_name')
				->where([['cp_enabled', '=', '1'],['is_sellable', '=', '1'],['is_active', '=', '1'],])
				->whereIn('product_id',$ids)
				->get()->all();

		else
			return DB::table('products')
				->select('product_id','product_title')
				->where([['cp_enabled', '=', '1'],['is_sellable', '=', '1'],['is_active', '=', '1'],['product_id', '=', $ids],])
				->get()->all();
	}

	// The Below Function fetchs all the active Hub`s ...
	public function getHubInfo($ids = null)
	{
		// The DC Type 118002, is to retrieve only Hub`s
		if($ids == null)
			return DB::table('legalentity_warehouses')
				->select('le_wh_id','lp_wh_name')
				->where([['status',1],['dc_type',118002],])
				->get()->all();

		else if(is_array($ids))
			return DB::table('legalentity_warehouses')
				->select('le_wh_id','lp_wh_name')
				->where([['status',1],['dc_type',118002],])
				->whereIn('le_wh_id', $ids)
				->get()->all();

		else
			return DB::table('legalentity_warehouses')
				->select('lp_wh_name')
				->where([['status',1],['dc_type',118002],['le_wh_id',$ids],])
				->get()->all();

	}

	// The Below Function fetchs all the active Manufacturer`s ...
	public function getManufacturerInfo($ids = null)
	{
		// The Legal Entity Type Id 1006, is to retrieve only Manufacturer`s
		if($ids == null)
			return DB::table('legal_entities')
				->select('legal_entity_id','business_legal_name')
				->where([['is_approved',1],['legal_entity_type_id',1006],])
				->get()->all();

		else if(is_array($ids))
			return DB::table('legal_entities')
				->select('legal_entity_id','business_legal_name as item_name')
				->where([['is_approved',1],['legal_entity_type_id',1006],])
				->whereIn('legal_entity_id',$ids)
				->get()->all();

		else
			return DB::table('legal_entities')
				->select('business_legal_name')
				->where([['is_approved',1],['legal_entity_type_id',1006],['legal_entity_id',$ids],])
				->get()->all();
	}

	// The Below Function fetchs all the active Dc`s ...
	public function getDcInfo($ids = null)
	{
		// The DC Type 118001, is to retrieve only Dc`s
		if($ids == null)
			return DB::table('legalentity_warehouses')
				->select('le_wh_id','lp_wh_name')
				->where([['status',1],['dc_type',118001],])
				->get()->all();

		else if(is_array($ids))
			return DB::table('legalentity_warehouses')
				->select('le_wh_id','lp_wh_name')
				->where([['status',1],['dc_type',118001],])
				->whereIn('le_wh_id',$ids)
				->get()->all();

		else
			return DB::table('legalentity_warehouses')
				->select('lp_wh_name')
				->where([['status',1],['dc_type',118001],['le_wh_id',$ids],])
				->get()->all();
	}


	public function checkHubIgnore($ref_id = null,$ref_type = null,$scope_id = null,$scope_type = null){
		if(isset($ref_id) and ($ref_id != '') and isset($ref_type) and ($ref_type != '') and isset($scope_id) and ($scope_id != '') and isset($scope_type) and ($scope_type != ''))
		{
			$status = false;
			// DB::enableQueryLog();
			$status = DB::table('hub_product_mapping')
							->where([
								['ref_id', '=', $ref_id],
	                            ['ref_type', '=', $ref_type."s"],
	                            ['scope_id', '=', $scope_id],
	                            ['scope_type', '=', strtoupper($scope_type)]
                            ])
							->count();
			// Log::info(DB::getQueryLog());
			// Log::info('status = '.$status);
			if(intval($status))
				return true;
		}
		return false;
	}

	public function getItemsList($itemsArray = null, $itemType = null){
		if(isset($itemsArray) and isset($itemType))
		{
			$items = null;
			$itemNames = array();
			if($itemType == 'manufacturer')
			{
				if(is_array($itemsArray))	
					$items = $this->getManufacturerInfo($itemsArray);
				else
					$items = $this->getManufacturerInfo([$itemsArray]);
			}
			else if($itemType == 'brand')
			{
				if(is_array($itemsArray))
					$items = $this->getBrandInfo($itemsArray);
				else
					$items = $this->getBrandInfo([$itemsArray]);
			}
			else if($itemType == 'product')
			{
				if(is_array($itemsArray))
					$items = $this->getProductInfo($itemsArray);
				else
					$items = $this->getProductInfo([$itemsArray]);	
			}

			if($items != null)
			{
				// $items = json_decode(json_encode($items),true)
				foreach ($items as $item) {
					// Log::info("fail ".$item->item_name);
					array_push($itemNames, "<b>".$item->item_name."</b>");
				}
				return $itemNames;
			}

			return null;
		}
	}

	public function insertNewHubIgnore($ref_id = null,$ref_type = null,$scope_id = null,$scope_type = null){
		if(isset($ref_id) and ($ref_id != '') and isset($ref_type) and ($ref_type != '') and isset($scope_id) and ($scope_id != '') and isset($scope_type) and ($scope_type != ''))
			return DB::table('hub_product_mapping')
                        ->insertGetId([
                            'ref_id' => $ref_id,
                            'ref_type' => $ref_type."s",
                            'scope_id' => $scope_id,
                            'scope_type' => strtoupper($scope_type)
                            ]);
		else
	   		return 0;
	}

	public function getHubIgnoreList(){

		$result = DB::table('hub_product_mapping')
					->select(
						'hbm_id',
						'ref_id',
						'ref_type',
						'scope_id',
						'scope_type'
						)
					->orderBy('hbm_id','desc')
					->get()->all();

		$count = DB::table('hub_product_mapping')->count();

		return ["result" => $result, "count" => $count];
	}

	public function getManufacturerName($id = null)
	{
		$name = null;
		if($id != null){
			$result = DB::table('legal_entities')
					->select('business_legal_name')
					->where([['is_approved',1],['legal_entity_type_id',1006],['legal_entity_id',$id]])
					->first();

			if($result)
				$name = $result->business_legal_name;
		}
		return $name;
	}

	public function getBrandName($id = null)
	{
		$name = null;
		if($id != null)
		{
			$result = DB::select("select GetBrandName(?) as name",[$id]);
			$name = $result[0]->name;
		}

		return $name;
	}

	public function getBeatName($id = null)
	{
		$name = null;
		if($id != null)
		{
			$result = $this->getBeatInfo($id);
			if($result != null)
			{	
				if($result[0]->spoke_name != null)
					$name = $result[0]->beat_name.' ('.$result[0]->spoke_name.')';
				else
					$name = $result[0]->beat_name;
			}
		}
		return $name;
	}

	public function getLeWhName($id = null)
	{
		$name = null;
		if($id != null)
		{
			$result = DB::select("select getLeWhName(?) as name",[$id]);
			$name = $result[0]->name;
		}
		return $name;
	}

	public function getSpokeName($id = null)
	{
		$name = null;
		if($id != null)
		{
			$result = DB::table('spokes')
						->where('spoke_id',$id)
						->first();
			$name = $result->spoke_name;
		}
		return $name;
	}

	public function deleteHubIgnoreListById($id = null)
	{
		$status = 0;
		if($id != null)
		{
			$status = DB::table("hub_product_mapping")
						->where("hbm_id",$id)
						->delete();
		}
		return $status;
	}


}