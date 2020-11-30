<?php

namespace App\Modules\Tax\Models;

/*
  Filename : Category.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 07-July-2016
  Desc : Model for category table
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use App\Modules\Roles\Models\Role;
use DB;

class Category extends Model {

    protected $primaryKey = "category_id";

    public function getAllCategories() {
        $allcategories = $this->where('is_active', '=', 1)->pluck('cat_name', 'category_id')->all();
        return $allcategories;
    }

    public function getFilteredCategories($array) {
        $allcategories = $this->where('is_active', '=', 1)->whereNotIn('category_id', $array)->pluck('cat_name', 'category_id')->all();
        return $allcategories;
    }

    public function getCategoryList() {
        $this->getChildCategories(0, 1);
        return $this->categoryList;
    }

    public function getChildCategories($cat_id, $level) {
        $rolesObj = new Role();
        $DataFilter = json_decode($rolesObj->getFilterData(8, Session::get('userId')), true);

        $categoryList = isset($DataFilter['category']) ? $DataFilter['category'] : [];

        $cat = DB::table('categories')
                ->where('categories.parent_id', $cat_id)
                ->whereIn('categories.category_id', $categoryList)
                ->get()->all();
        if (!empty($cat)) {
            foreach ($cat as $cat1) {
                $this->categoryList.= '<option value="' . $cat1->category_id . '" class=" parent_child_' . $level . '" > ' . $cat1->cat_name . '</option>';
                $this->getChildCategories($cat1->category_id, $level + 1);
            }
        }
    }

}
