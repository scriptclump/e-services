<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : InboundProductList.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 20-May-2016
  Desc : Model for products table
 */

use Illuminate\Database\Eloquent\Model;

class InboundProductList extends Model
{
    protected $table = 'products';
    protected $primaryKey = "product_id";
    
    public function inventory()
    {
        return $this->hasOne('App\Modules\Inbound\Models\ProductInventory', 'product_id', 'product_id');
    }
    
    public function price()
    {
        return $this->hasOne('App\Modules\Inbound\Models\ProductPrice', 'product_id', 'product_id');
    }
    
    public function brand()
    {
        return $this->hasOne('App\Modules\Inbound\Models\Brand', 'brand_id', 'brand_id');
    }
    
    public function productsDisplayForInboundRequest($productId) {
        $pro_list_all = $this->with(array(
                    'inventory' => function($query) {
                        $query->select('product_id', 'available_inventory');
                    },
                    'price' => function($query) {
                        $query->select('product_id', 'mrp', 'cbp');
                    },
                    'brand' => function($query) {
                        $query->select('brand_id', 'brand_name');
                    }
                ))->where('product_id', $productId)->get(array('product_id', 'product_name', 'seller_sku', 'primary_image', 'category_id', 'brand_id'))->all();
        $pro_list_all_array = json_decode(str_replace(array('[',']'), "", json_encode($pro_list_all)), true);
        return $pro_list_all_array;
    }
    
    /*
     * @param $productId is the product Id
     * 
     * This function will gives the product name about the particular inward request 
     * 
     * @return the Product name
    */

    public function productName($productId)
    {
        $productName = $this->find($productId)->product_name;
        return $productName;
    }
    
    /*
     * @param $productId is the product Id
     * 
     * This function will gives the product image path about the particular inward request 
     * 
     * @return the Product image path
    */

    public function productimage($productId)
    {
        $productimage = $this->find($productId)->primary_image;
        return $productimage;
    }
    
}