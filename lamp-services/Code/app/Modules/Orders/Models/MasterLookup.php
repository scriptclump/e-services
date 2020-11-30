<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

use Cache;
use DB;

class MasterLookup extends Model
{
	/*
	 * getOrderStatus() method is used to get order name with value
	 * @param Null
	 * @return Array
	 */
	 
	public function getAllOrderStatus($catName = 'Order Status', $is_active=array(1)) {
        try {
			$fieldArr = array('orderStatus.master_lookup_name as name', 'orderStatus.value');
			$query = DB::table('master_lookup as orderStatus')->select($fieldArr);
			$query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','orderStatus.mas_cat_id');
			$query->where('master_lookup_categories.mas_cat_name', $catName);
			$query->whereIn('orderStatus.is_active', $is_active);
			#echo $query->toSql();die;
			$allOrderStatusArr = $query->get()->all();
			
			$orderStatusArr = array();
			if(is_array($allOrderStatusArr)) {
				foreach($allOrderStatusArr as $data){
					$orderStatusArr[$data->value] = $data->name;
				}
			}
			
			return $orderStatusArr;
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
    
	/*
	 * getMasterLookupIdByStatus() method is used to get master_lookup_id with value
	 * @param Null
	 * @return number
	 */
	 
    public function getMasterLookupIdByStatus($statusValue, $lookupCatName) {
        try {
				$fieldArr = array('lookup.master_lookup_id');
				$query = DB::table('master_lookup as lookup')->select($fieldArr);
				$query->join('master_lookup_categories as category','lookup.mas_cat_id','=','category.mas_cat_id');
				$query->where('category.mas_cat_name', $lookupCatName);
				$query->where('lookup.value', $statusValue);
				$query->where('lookup.is_active', 1);
				$row = $query->first();
				return isset($row->master_lookup_id) ? $row->master_lookup_id : 0;
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    /*
	 * getMasterLookupIdByStatus() method is used to get master_lookup_id with value
	 * @param Null
	 * @return number
	 */
	 
    public function getMasterLookupByStatus($statusValue, $lookupCatName) {
        try {
				$fieldArr = array('lookup.master_lookup_name');
				$query = DB::table('master_lookup as lookup')->select($fieldArr);
				$query->join('master_lookup_categories as category','lookup.mas_cat_id','=','category.mas_cat_id');
				$query->where('category.mas_cat_name', $lookupCatName);
				$query->where('lookup.value', $statusValue);
				$query->where('lookup.is_active', 1);
				return $query->first();
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
    
    
     /*
	 * getStatusMatrixById() method is used to get order status matrix values with value
	 * @param Null
	 * @return Array
	 */
	 
    public function getStatusMatrixById($lookup_id) {
        try {
			$fieldArr = array('orderStatus.master_lookup_name as name', 'orderStatus.value');
			$query = DB::table('master_lookup as orderStatus')->select($fieldArr);
			$query->join('master_lookup_matrix','master_lookup_matrix.has_next_status','=','orderStatus.master_lookup_id');
			$query->where('master_lookup_matrix.master_lookup_id', $lookup_id);
			$query->where('orderStatus.is_active', 1);
			$allOrderStatusArr = $query->get()->all();
			$orderStatusArr = array();
			if(is_array($allOrderStatusArr)) {
				foreach($allOrderStatusArr as $data){
					$orderStatusArr[$data->value] = $data->name;
				}
			}
			
			return $orderStatusArr;
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
    
    public function getStatusByPatentName($parentName, $ignoreIds=array()) {
        try {
			$fieldArr = array('lookup2.master_lookup_name as name', 'lookup2.value');
			$query = DB::table('master_lookup as lookup1')->select($fieldArr);
			$query->join('master_lookup as lookup2','lookup1.master_lookup_id','=','lookup2.parent_lookup_id');
			$query->where('lookup1.master_lookup_name', (string)$parentName);
			$query->where('lookup1.is_active', 1);
			$query->orderBy('lookup2.sort_order', 'ASC');
			if(is_array($ignoreIds) && count($ignoreIds) > 0) {
				$query->whereNotIn('lookup2.value', $ignoreIds);
			}
			#echo $query->toSql();die;
			$allOrderStatusArr = $query->get()->all();
			$orderStatusArr = array();
			if(is_array($allOrderStatusArr)) {
				foreach($allOrderStatusArr as $data){
					$orderStatusArr[$data->value] = $data->name;
				}
			}
			
			return $orderStatusArr;
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
    
     public function getStatusMatrixByValue($statusValue, $lookupCatId) {
        try {
			$fieldArr = array('lookup.master_lookup_name as name', 'lookup.value');
			$query = DB::table('master_lookup as lookup')->select($fieldArr);
			$query->join('master_lookup_matrix as matrix','matrix.has_next_status_value','=','lookup.value');
			$query->where('matrix.master_lookup_value', $statusValue);
			$query->where('matrix.mas_cat_id', $lookupCatId);
			$query->where('lookup.is_active', 1);
			$allOrderStatusArr = $query->get()->all();
			#echo $query->toSql();die;
			$orderStatusArr = array();
			if(is_array($allOrderStatusArr)) {
				foreach($allOrderStatusArr as $data){
					$orderStatusArr[$data->value] = $data->name;
				}
			}
			
			return $orderStatusArr;
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
    
    public function getLookupCatgory($nameArr) {
		$fieldArr = array('category.mas_cat_name', 'category.mas_cat_id');
		$query = DB::table('master_lookup_categories as category')->select($fieldArr);
		
		$query->whereIn('category.mas_cat_name', $nameArr);
		$categoryArr = $query->get()->all();
		$lookupCategoryArr = array();
		foreach($categoryArr as $category) {
			$lookupCategoryArr[$category->mas_cat_id] = $category->mas_cat_name;
		}
		return $lookupCategoryArr;
	}
    	/*
	 * getAllPaymentMethods() method is used to get Payment Method name with value
	 * @param Null
	 * @return Array
	 */
	 
	public function getMasterLookupByCategoryName($catName = 'Payment Type') {
        try {
            $fieldArr = array('orderStatus.master_lookup_name as name', 'orderStatus.value');
            $query = DB::table('master_lookup as orderStatus')->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','orderStatus.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', $catName);
            $query->where('orderStatus.is_active', 1);
            #echo $query->toSql();die;
            $allOrderStatusArr = $query->get()->all();

            $orderStatusArr = array();
            if(is_array($allOrderStatusArr)) {
                    foreach($allOrderStatusArr as $data){
                            $orderStatusArr[$data->value] = $data->name;
                    }
            }

            return $orderStatusArr;
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

	public function getMasterLookupNamesByCategoryId($catId, $isActive=array(1)) {
        try {
            $fieldArr = array('master_lookup_name as name','value');
            $query = DB::table('master_lookup')->select($fieldArr);
            $query->where('mas_cat_id', $catId);
            $query->whereIn('is_active', $isActive);
            #echo $query->toSql();die;
            $allLookupNamesArr = $query->get()->all();

            $lookupNamesArr = array();
            if(is_array($allLookupNamesArr)) {
                    foreach($allLookupNamesArr as $data){
                            $lookupNamesArr[$data->value] = $data->name;
                    }
            }

            return $lookupNamesArr;
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    public function getCachedMasterLookupByCatName($catName, $cacheKey, $cacheExpTime=7200) {

    	$cacheKey = $cacheKey.'_'.str_replace(' ', '_', $catName);

    	if (Cache::has($cacheKey)) {
            $mastLookupInfo = Cache::get($cacheKey);
            if(is_null($mastLookupInfo)) {
                $mastLookupInfo = $this->getMasterLookupByCategoryName($catName);
                Cache::put($cacheKey, json_encode($mastLookupInfo), $cacheExpTime);
                return $mastLookupInfo;
            }
            else if(!is_null($mastLookupInfo)) {
            	return json_decode($mastLookupInfo, true);
            }
        }
        else {
            $mastLookupInfo = $this->getMasterLookupByCategoryName($catName);
            Cache::add($cacheKey, json_encode($mastLookupInfo), $cacheExpTime);
            return $mastLookupInfo;
        }
    }

	public function getMasterLokup($value) {
        $data = DB::table('master_lookup')
                        ->select('master_lookup_id', 'description', 'value')
                        ->where('value', $value)->first();
        return $data;
    }

}
