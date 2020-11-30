<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : ProductSellerMapping.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 24-May-2016
  Desc : Model for product seller mapping table
 */

use Illuminate\Database\Eloquent\Model;

class ProductSellerMapping extends Model
{
    protected $table = "product_seller_mapping";
    protected $primaryKey = "prod_sel_map_id";
    
    public function productIdBySellerId($sellerId) {
        $product_id = $this->where('seller_id', $sellerId)->get(array('product_id'))->all();
        $product_id_array = json_decode(json_encode($product_id), true);
        return $product_id_array;
    }
    
    public function sellerIdByProductId($productId) {
        $seller_id = $this->where('product_id', $productId)->get(array('seller_id'))->all();
        $seller_id_array = json_decode(json_encode($seller_id), true);
        return $seller_id_array[0]['seller_id'];
    }
}