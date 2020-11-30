<?php

namespace App\Modules\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;

class Brand extends Model {

    protected $primaryKey = "brand_id";

    public function getAllBrands() {
        $sql = $this->pluck('brand_name', 'brand_id')->all();
        return $sql;
    }

    public function getBrandList() {
        $rolesObj = new Role();
        $DataFilter = json_decode($rolesObj->getFilterData(7, Session::get('userId')), true);
        $brand_list = isset($DataFilter['brand']) ? $DataFilter['brand'] : [];
        $this->getChildBrands(0, 1, $brand_list);
        return $this->brandList;
    }

    public function getChildBrands($parent_id, $level, $brandList = '') {
        $brand = DB::table('brands')
                ->where('brands.parent_brand_id', $parent_id)
                ->whereIn('brands.brand_id', array_keys($brandList))
                ->get()->all();
        if (!empty($brand)) {
            foreach ($brand as $brand1) {
                $this->brandList.= '<option value="' . $brand1->brand_id . '" class=" parent_child_' . $level . '" > ' . $brand1->brand_name . '</option>';
                $this->getChildBrands($brand1->brand_id, $level + 1, $brandList);
            }
        }
    }

}
