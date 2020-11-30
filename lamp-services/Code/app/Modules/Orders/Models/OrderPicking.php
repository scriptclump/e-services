<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class OrderPicking extends Model
{
	
	/*
	 * getOrderStatus() method is used to get order name with value
	 * @param Null
	 * @return Array
	 */
	 
	public function getOrderPickings() {
        try {
			$fieldArr = array('picking.*', 'warehouse.lp_wh_name');
			$query = DB::table('gds_orders_picking as picking')->select($fieldArr);
			$query->leftJoin('lp_warehouses as warehouse', 'warehouse.lp_wh_id', '=', 'picking.lp_wh_id');
			
			#echo $query->toSql();die;
			return $query->get()->all();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
    
    public function getOrderPickingCount() {
        try {
			
			$query = DB::table('gds_orders_picking as picking')->count();
			return $query;
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
				$row = $query->first();
				return isset($row->master_lookup_id) ? $row->master_lookup_id : 0;
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
			$query->orderBy('lookup2.sort_order', 'ASC');
			if(is_array($ignoreIds) && count($ignoreIds) > 0) {
				$query->whereNotIn('lookup2.value', $ignoreIds);
			}
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
    
}
