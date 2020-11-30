<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : Category.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 25-May-2016
  Desc : Model for category table
 */

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = "category_id";
    
    public function categoryNameById($categoryId) {
        $category_names = $this->where('category_id', $categoryId)->get(array('cat_name'))->all();
        $category_names_array = json_decode(str_replace(array('[',']'), "",json_encode($category_names)), true);
        return $category_names_array;
    }
}