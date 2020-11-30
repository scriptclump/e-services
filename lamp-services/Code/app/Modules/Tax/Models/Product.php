<?php

namespace App\Modules\Tax\Models;

/*
  Filename : Product.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 12-July-2016
  Desc : Model for products table
 */

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $primaryKey = 'product_id';

    public function showProducts($page, $pageSize, $orderBy = '', $filterBy = '') {

        $query = $this;
        if (Session('roleId') != '1') {
            $query = $query->where('products.legal_entity_id', Session('legal_entity_id'));
        }

        $query = $query->join('categories', 'categories.category_id', '=', 'products.category_id');
        $query = $query->join('brands', 'brands.brand_id', '=', 'products.brand_id');
//        $query = $query->leftJoin('tax_class_product_map', 'tax_class_product_map.product_id', '=', 'products.product_id');

        if (!empty($orderBy)) {
            $orderByExplode = explode(" ", $orderBy);
            $query = $query->orderby($orderByExplode[0], $orderByExplode[1]);
        }

        if (!empty($filterBy)) {
            foreach ($filterBy as $filterByEach) {
                $filterByEachExplode = explode(' ', $filterByEach);

                $length = count($filterByEachExplode);
                $filter_query_value = '';
                if ($length > 3) {
                    $filter_query_field = $filterByEachExplode[0];
                    $filter_query_operator = $filterByEachExplode[1];
                    for ($i = 2; $i < $length; $i++)
                        $filter_query_value .= $filterByEachExplode[$i] . " ";
                } else {
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
//        dd($query->toSql());exit;
        $final_result['result'] = $query->get(array('products.product_id', 'product_title', 'products.category_id', 'cat_name', 'brands.brand_id', 'brands.brand_name', 'sku' /* 'tax_class_product_map.tax_class_id' */))->all();
        return $final_result;
    }

    public function getProductDetailsofTaxClassCode($productId) {
        $query = $this->join('categories', 'categories.category_id', '=', 'products.category_id')
                ->leftJoin('brands', 'brands.brand_id', '=', 'products.brand_id')
                ->where('products.product_id', '=', $productId)
                ->get(array('product_title', 'cat_name', 'sku', 'brand_name'))->all();
        return $query;
    }

    public function getProductId($sku) {
        $query = $this->where("sku", '=', $sku)->get(array('product_id'))->all();
        $data = $query;

        if (empty($data)) {
            $data = 0;
        } else {

            $data = $data[0]['product_id'];
        }

        return $data;
    }

    public function getBrandsBasedByCategories($cats) {
        $categories = explode(',', $cats);
        $sql = $this->join("brands", "brands.brand_id", "=", "products.brand_id")
                ->whereIn("products.category_id", $categories)
                ->groupBy("brands.brand_id")
                ->pluck("brands.brand_name", "brands.brand_id")->all();

        return $sql;
    }

    public function getCategoriesBasedOnBrands($brands) {
        $brandids = explode(',', $brands);
        $sql = $this->join("categories", "categories.category_id", "=", "products.category_id")
                ->whereIn("products.brand_id", $brandids)
                ->groupBy("categories.category_id")
                ->pluck("categories.cat_name", "categories.category_id")->all();

        return $sql;
    }

    public function getSkuByProductId($productId)
    {
        $sql = $this->where("product_id", "=", $productId)->pluck('sku')->all();
        $result = $sql;
        return $result[0];
    }

}
