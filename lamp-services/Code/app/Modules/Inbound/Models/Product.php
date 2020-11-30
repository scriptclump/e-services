<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : Product.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 31-May-2016
  Desc : Model for product mongo table
 */

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Product extends Eloquent {

    protected $connection = 'mongo';
    protected $primaryKey = '_id';

    public function bySellerId($sellerId) {
        $mongo_seller_id = $this->where('seller_id', $sellerId)->get(array('seller_id'))->all();
        $mongo_seller_id_array = json_decode(json_encode($mongo_seller_id, true));
        return $mongo_seller_id_array;
    }

    public function createProduct($productDetails) {
        $this->product_id = $productDetails['product_id'];
        $this->image = $productDetails['primary_image'];
        $this->name = $productDetails['product_name'];
        $this->mrp = $productDetails['price']['mrp'];
        $this->available_inventory = (int)$productDetails['inventory']['available_inventory'];
        $this->brand_id = (int)$productDetails['brand']['brand_id'];
        $this->brand_name = $productDetails['brand']['brand_name'];
        $this->category_id = (int)$productDetails['category_id'];
        $this->category_name = $productDetails['cat_name'];
        $this->sku = $productDetails['seller_sku'];
        $this->seller_id = $productDetails['seller_id'];
        $this->product_flag = (int)"0";
        $this->save();
    }

    public function productListing($page, $pageSize, $orderBy = '', $filterBy = '', $brandId = array(), $categoryId = array()) {
        $query = $this->where('product_flag', '!=', (int)'1');
        
        if(!empty($brandId)){
            $query = $query->whereIn('brand_id', $brandId);
        }
        
        if(!empty($categoryId)){
            $query = $query->whereIn('category_id', $categoryId);
        }

        if (!empty($orderBy)) {
            $orderByExplode = explode(" ", $orderBy);
            $query = $query->orderby($orderByExplode[0], $orderByExplode[1]);
        }
        
        if (!empty($filterBy)) {
            foreach ($filterBy as $filterByEach) {
                $filterByEachExplode = explode(' ', $filterByEach);
                
                $length = count($filterByEachExplode);
                $filter_query_value = '';
                if($length > 3){
                    $filter_query_field = $filterByEachExplode[0];
                    $filter_query_operator = $filterByEachExplode[1];
                    for($i=2;$i<$length;$i++)
                        $filter_query_value .= $filterByEachExplode[$i]." ";
                }
                else{
                    $filter_query_field = $filterByEachExplode[0];
                    $filter_query_operator = $filterByEachExplode[1];
                    $filter_query_value = $filterByEachExplode[2];
                }
                
                $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                if (in_array(trim($filter_query_operator), $operator_array)) {
                    $query = $query->where($filter_query_field, $filter_query_operator, (int) $filter_query_value);
                } else {
                    $query = $query->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                }
            }
        }
        $count = $query->count();
        $final_result = array();
        $final_result['count'] = $count;
        $query = $query->skip((int) $page * (int) $pageSize)->take((int) $pageSize);
        $final_result['result'] = $query->get()->all();
        return $final_result;
    }
    
    public function updateProductFlag($productId, $flagValue) {
        $update_query = $this::where('product_id', '=', (int)$productId)->first();
        $update_query->product_flag = (int)$flagValue;
//        return $update_query->save();
        if($update_query->save()){
            echo "flag updated";
        } else {
            echo "flag update fail!";
        }
    }
    
    public function distinctBrands() {
        $brands_query = $this->groupBy('brand_name')->get(array('brand_id'))->all();
        $brands_query_array = json_decode(json_encode($brands_query), true);
        return $brands_query_array;
    }
    
    public function distinctCategories() {
        $categories_query = $this->groupBy('category_name')->get(array('category_id'))->all();
        $categories_query_array = json_decode(json_encode($categories_query), true);
        return $categories_query_array;
    }

}
