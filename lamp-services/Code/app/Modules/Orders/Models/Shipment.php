<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Shipment extends Model
{
	

	public function getShipmentGridByOrderId($orderId) {
        try {
		$query = DB::table('gds_ship_grid as grid')->select('grid.*');
		$query->where('grid.gds_order_id', $orderId);
		return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    public function getShippedProductByGridId($gridId, $productId) {
		try {
	            $fieldArr = array('products.*');

	            $query = DB::table('gds_ship_products as products')->select($fieldArr);
	            $query->where('products.gds_ship_grid_id', $gridId);
	            $query->where('products.product_id', $productId);
	            return $query->first();
	        } catch (Exception $e) {
	            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}	

	public function getShipmentQtyWithProductById($shipmentId) {
		try {
	            $fieldArr = array(
	            	'products.product_id',
	            	DB::raw('SUM(products.qty) as shippedQty')
	            	);

	            /*$query = DB::table('gds_ship_grid as grid')->select($fieldArr);
	            $query->join('gds_ship_products as products', 'grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');
	            
	            $query->where('grid.gds_ship_grid_id', $shipmentId);
	            $query->groupBy('products.product_i');
	            //echo $query->toSql();die;
	            $shippedArr = $query->get();*/
	            $query="select `products`.`product_id`, SUM(products.qty) as shippedQty from `gds_ship_grid` as `grid` inner join `gds_ship_products` as `products` on `grid`.`gds_ship_grid_id` = `products`.`gds_ship_grid_id` where `grid`.`gds_ship_grid_id` = ".$shipmentId." group by `products`.`product_id`";
	            $shippedArr = DB::selectFromWriteConnection($query);
	            $dataArr = array();
				if(is_array($shippedArr)) {
					foreach($shippedArr as $data){
						$dataArr[$data->product_id] = $data->shippedQty; 
					}
				}
				return $dataArr;
	        } catch (Exception $e) {
	            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}	
	 public function getShipmentProductsById($shipmentId) {
        try {
	            $fieldArr = array('grid.*',
	            	'products.product_id',
	            	'gdsproducts.qty',
	            	'products.qty as shippedQty',
	            	'gdsproducts.sku',
	            	'gdsproducts.pname',
	            	'gdsproducts.seller_sku',
	            	'gdsproducts.mrp',
	            	'gdsproducts.tax',
	            	'gdsproducts.total',
	            	'gdsproducts.price',
	            	'currency.symbol_left as symbol',
	            	//'orders.order_date'
	            	);

	            $query = DB::table('gds_ship_grid as grid')->select($fieldArr);
	            $query->join('gds_orders as orders', 'grid.gds_order_id', '=', 'orders.gds_order_id');
	            $query->join('gds_ship_products as products', 'grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');
	            $query->join('gds_order_products as gdsproducts', 'products.product_id', '=', 'gdsproducts.product_id');
	            $query->join('currency', 'orders.currency_id', '=', 'currency.currency_id');
	            $query->where('grid.gds_ship_grid_id', $shipmentId);
	            $query->groupBy('grid.gds_ship_grid_id');
	            //echo $query->toSql();die;
	            return $query->get()->all();
	        } catch (Exception $e) {
	            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    public function getShippedProductsById($shipmentId) {
        try {
	            $fieldArr = array('grid.*',
	            	'products.product_id',
	            	'products.qty as shippedQty'
	            	);

	            $query = DB::table('gds_ship_grid as grid')->select($fieldArr);
	            $query->join('gds_ship_products as products', 'grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');
	            $query->where('grid.gds_ship_grid_id', $shipmentId);
	            //echo $query->toSql();die;
	            return $query->get()->all();
	        } catch (Exception $e) {
	            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
 

    public function updateShipmetProduct($dataArr, $shipProdId) {
		try{
			$dataArr['updated_by'] = Session('userId');
			$dataArr['updated_at'] = Date('Y-m-d H:i:s');	
			
			DB::table('gds_ship_products')->where('gds_ship_prd_id', $shipProdId)->update($dataArr);
		}
		catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function updateShipmetProductByGridId($dataArr, $shipGridId) {
		try{
			$dataArr['updated_by'] = Session('userId');
			$dataArr['updated_at'] = Date('Y-m-d H:i:s');	
			
			DB::table('gds_ship_products')->where('gds_ship_grid_id', $shipGridId)->update($dataArr);
		}
		catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function updateShipmentStatusByOrderId($dataArr, $orderId) {
		try{

			$dataArr['updated_by'] = Session('userId');
			$dataArr['updated_at'] = Date('Y-m-d H:i:s');	
			$shipInfo = $this->getShipmentGridByOrderId($orderId);
			$gdsShipGridId = isset($shipInfo->gds_ship_grid_id) ? $shipInfo->gds_ship_grid_id : 0;
			
			DB::table('gds_ship_grid')->where('gds_order_id', $orderId)->update($dataArr);
			if($gdsShipGridId) {
				$this->updateShipmetProductByGridId($dataArr, $gdsShipGridId);
			}			
		}
		catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function deleteShippedProduct($shipId) {
		try{

			DB::table('gds_ship_products')->where('gds_ship_grid_id', '=', $shipId)->delete();		
		}
		catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}		
	}

	public function deleteShippedGrid($shipId) {
		try{

			DB::table('gds_ship_grid')->where('gds_ship_grid_id', '=', $shipId)->delete();		
		}
		catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}		
	}

	public function updateShipmetGrid($gridId, $data) {
		try{
			DB::table('gds_ship_grid')->where('gds_ship_grid_id', $gridId)->update($data);
		}
		catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getPickedQtyByOrderId($orderId) {
        try {
            $query = DB::table('gds_ship_grid as grid')->select(DB::raw('SUM(products.qty) as shippedQty'));
            $query->join('gds_ship_products as products','products.gds_ship_grid_id','=','grid.gds_ship_grid_id');
            $query->where('grid.gds_order_id', $orderId);
            $result = $query->first();
            return isset($result->shippedQty) ? (int)$result->shippedQty:0;
           
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

	public function verifyShipmentByOrderId($orderId, $fields=array('grid.*')) {
        try {
			$query = DB::table('gds_ship_grid as grid')->select($fields);
			$query->join('gds_ship_products as products', 'grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');
			$query->join('gds_orders as orders', 'grid.gds_order_id', '=', 'orders.gds_order_id');
			$query->where('grid.gds_order_id', $orderId);
			$query->whereIn('orders.order_status_id', ['17005','17020']);
			return $query->first();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }


    public function checkPicklist($orderIds, $fields=array('grid.*')) {
        try {
        	$query = DB::table('gds_orders as orders')->select($fields);
        	$query->leftjoin('gds_ship_grid as grid', 'orders.gds_order_id', '=', 'grid.gds_order_id');
        	$query->leftjoin('gds_ship_products as products', 'grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');

			$query->whereIn('orders.gds_order_id', $orderIds);
			$query->whereNotNull('grid.gds_order_id');						
			$query->groupBy('orders.gds_order_id');
			return $query->get()->all();
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

	public function getOrderProductById($orderId) {
		try {
	            $fieldArr = array(
	            	'products.product_id',
	            	DB::raw('SUM(products.qty) as shippedQty')
	            	);

	            $query = DB::table('gds_orders')->select($fieldArr);
	            $query->join('gds_order_products as products', 'gds_orders.gds_order_id', '=', 'products.gds_order_id');
	            
	            $query->where('gds_orders.gds_order_id', $orderId);
	            $query->groupBy('products.product_id');
	            //echo $query->toSql();die;
	            $shippedArr = $query->get()->all();
	            $dataArr = array();
				if(is_array($shippedArr)) {
					foreach($shippedArr as $data){
						$dataArr[$data->product_id] = $data->shippedQty; 
					}
				}
				return $dataArr;
	        } catch (Exception $e) {
	            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}	

}
