<?php
namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Notifications;
use UserActivity;
use Utility;

class ZeroInventory extends Model {

    protected $table = "vw_inventory_report";
    protected $catList = '';
    protected $brands = '';

    public function getAllInventory() {
        $sql = $this->groupBy('le_wh_id')->get(array('dcname', 'cpvalue', 'ptrvalue', 'mrpvalue', 'le_wh_id'))->all();
        return $sql;
    }

    

    

    
public function getCategoryList() {
        $rolesObj = new Role();
        $DataFilter = json_decode($rolesObj->getFilterData(8, Session::get('userId')), true);
       if(!isset($DataFilter['category']) || empty($DataFilter['category']) ){
            return 0;
        }

        $categoryList = isset($DataFilter['category']) ? $DataFilter['category'] : [];
        $cat = DB::table('categories')
            ->whereIn('categories.category_id', $categoryList)
            ->get(array('category_id', 'cat_name', 'parent_id'))->all();

        $cat = json_decode(json_encode($cat), true);

        $parents = array_unique(array_column($cat, 'parent_id'));

        $relationalArray = array(); $catDetail = array();
        foreach($cat as $key=>$data){
            $relationalArray[$data['parent_id']][$data['category_id']] = $data['category_id'];
            $catDetail[$data['category_id']] = $data['cat_name'];
        }

        $this->makeCatDropdown($relationalArray[0], 1, $catDetail, $relationalArray);

       unset($catDetail, $categoryList, $relationalArray, $cat);
        return $this->catList;
    }

    public function makeCatDropdown($catArray, $level, $catDetail, $relationalArray){
        foreach($catArray as $key=>$cat){

            $this->catList .= '<option value="' . $key . '" class=" parent_child_'.$level.'" > ' . $catDetail[$key] . '</option>';

            if(isset($relationalArray[$cat]) && is_array($relationalArray[$cat])){
                $this->makeCatDropdown($relationalArray[$cat], $level+1, $catDetail, $relationalArray);
            }
        }        
    }

    public function getBrandList() {
        $rolesObj = new Role();
        $DataFilter = json_decode($rolesObj->getFilterData(7, Session::get('userId')), true);
       if(!isset($DataFilter['brand']) || empty($DataFilter['brand']) ){
            return 0;
        }

        $brandList = isset($DataFilter['brand']) ? $DataFilter['brand'] : [];
        $brandList = array_keys($brandList);
        $brand = DB::table('brands')
            ->whereIn('brands.brand_id', $brandList)
            ->get(array('brand_id', 'brand_name', 'parent_brand_id'))->all();

        $brand = json_decode(json_encode($brand), true);
        
        $parents = array_unique(array_column($brand, 'parent_brand_id'));
        $relationalArray = array(); $brandDetail = array();
        foreach($brand as $key=>$data){
            $relationalArray[$data['parent_brand_id']][$data['brand_id']] = $data['brand_id'];
            $brandDetail[$data['brand_id']] = $data['brand_name'];
        }

        $this->makeBrandDropdown($relationalArray[0], 1, $brandDetail, $relationalArray);
        unset($brandDetail, $brandList, $brand, $relationalArray);
        return $this->brands;
    }

    public function makeBrandDropdown($brandArray, $level, $brandDetail, $relationalArray){
        foreach($brandArray as $key=>$brand){

            $this->brands .= '<option value="' . $key . '" class=" parent_child_'.$level.'" > ' . $brandDetail[$key] . '</option>'.$level.'<br>';

            if(isset($relationalArray[$brand]) && is_array($relationalArray[$brand])){
                $this->makeBrandDropdown($relationalArray[$brand], $level+1, $brandDetail, $relationalArray);
            }
        }        
    }

public function filterOptions() {
        $filter_array = Array();
        $warehouses_table = DB::table('legalentity_warehouses');
        $products_table = DB::table('products');
        $product_titles = DB::table("products");
        $brands_table = DB::table('brands');
        $category_table = DB::table('categories');

        $filter_array['category_name'] = $this->getCategoryList();
        $filter_array['brand_name'] = $this->getBrandList();
        $roleOb = new Role();
        $rbac_manfacturer_name = json_decode($roleOb->getFilterData(11, Session::get('userId')), true);
        $filter_array['manfacturer_name'] = $rbac_manfacturer_name['manufacturer'];
        // echo Session::get('legal_entity_id');
        if (Session('roleId') != '1') {
            $warehouses_table = $warehouses_table->where('legal_entity_id', Session::get('legal_entity_id'));
            $products_table = $products_table->where('legal_entity_id', Session::get('legal_entity_id'));
            $product_titles = $product_titles->where('legal_entity_id', Session::get('legal_entity_id'));
            $brands_table = $brands_table->where('legal_entity_id', Session::get('legal_entity_id'));
            $category_table = $category_table->where('legal_entity_id', Session::get('legal_entity_id'));
        }

        $filter_array['dc_name'] = $warehouses_table->where('lp_wh_name', '!=', NULL)->where('lp_wh_name', '!=', '')->orderBy('lp_wh_name', 'asc')->pluck('lp_wh_name', 'le_wh_id')->all();
//        $filter_array['manfacturer_name'] = DB::table('legal_entities')->distinct('business_legal_name')->where('legal_entity_type_id', '1006')/*->where('legal_entity_id', Session::get('legal_entity_id'))*/->where('business_legal_name', '!=', NULL)->where('business_legal_name', '!=', '')->orderBy('business_legal_name', 'asc')->lists('business_legal_name', 'legal_entity_id');
//        $filter_array['brand_name'] = $brands_table->distinct('brand_name')->where('brand_name', '!=', NULL)->where('brand_name', '!=', '')->orderBy('brand_name', 'asc')->lists('brand_name');
//        $filter_array['category_name'] = $category_table)->distinct('cat_name')->where('cat_name', '!=', NULL)->where('cat_name', '!=', '')->orderBy('cat_name', 'asc')->lists('cat_name');
        $filter_array['kvi'] = DB::table('master_lookup')->where('mas_cat_id', '=', 69)->orderBy('master_lookup_name', 'asc')->pluck('master_lookup_name', 'value')->all();
        $filter_array['upc_ean'] = $products_table->distinct('upc')->where('upc', '!=', NULL)->where('upc', '!=', '')->orderBy('upc', 'asc')->pluck('upc')->all();
        $filter_array['shelflife'] = $products_table->distinct('shelf_life')->where('shelf_life', '!=', NULL)->where('shelf_life', '!=', '')->orderBy('shelf_life', 'asc')->pluck('shelf_life')->all();
        $filter_array['shelflife_uom'] = DB::table('master_lookup')->where('mas_cat_id', '=', 71)->orderBy('value', 'asc')->pluck('master_lookup_name', 'value')->all();
        $filter_array['product_form'] = DB::table('master_lookup')->where('mas_cat_id', '=', 72)->orderBy('master_lookup_name', 'asc')->pluck('master_lookup_name', 'value')->all();
        $filter_array['product_titles'] = $product_titles->pluck('product_title', 'product_id')->all();
        
        $min_mrp_explode = explode('.', $this->min('mrp'));
        $filter_array['min_mrp'] = $min_mrp_explode[0];
        $max_mrp_explode = explode('.', $this->max('mrp'));
        $filter_array['max_mrp'] = $max_mrp_explode[0];
        $filter_array['min_soh'] = $this->min('soh');
        $filter_array['max_soh'] = $this->max('soh');
        $filter_array['min_invtr'] = $this->min('available_inventory');
        $filter_array['max_invtr'] = $this->max('available_inventory');
        $filter_array['min_map'] = $this->min('map');
        $filter_array['max_map'] = $this->max('map');
        $filter_array['sku'] = $this->distinct('sku')->where('le_wh_id', '!=', NULL)->where('le_wh_id', '!=', '')->orderBy('sku', 'asc')->pluck('sku')->all();
        return $filter_array;
    }

    public function updateProducts($atpval, $sohval, $wareId, $prodId, $sku)
    {
        $returnval = 0;
        $timestamp = date('Y-m-d H:i:s');
        $result = DB::table('inventory')
            ->where('le_wh_id', $wareId)
            ->where('product_id', $prodId)
            ->update(['atp' => $atpval, 'soh' => $sohval, 'updated_by' => Session::get('userId')]);

            if($result)
            {
                $returnval = 1;
                $DBentries = array("SOH" => $sohval, "ATP" => $atpval, "warehouse_id" => $wareId, "Product_id" => $prodId);
                $DBentries = json_encode($DBentries);
                UserActivity::userActivityLog("Inventory", $DBentries, "SOH and ATP values has been updated in Inventory Table");
                Notifications::addNotification(['note_code' => 'INVT0012', 'note_priority' => 0, 'note_type' => 1, 'note_params' => ['SOHVAL' => $sohval, 'ATPVAL' => $atpval, 'SKU' => $sku]]);
            }

            return $returnval;
            

    }

    public function wareHouseNameById($warehouseId)
    {
        $result = DB::table("legalentity_warehouses")->where('le_wh_id', '=', $warehouseId)->get(array('lp_wh_name'))->all();
        $results = json_decode(json_encode($result), true);
        return $results[0]['lp_wh_name'];
    }

    public function getSkuByProductId($product_id)
    {
        $result = DB::table("products")->where("product_id", "=", $product_id)->get(array('sku'))->all();
        $results = json_decode(json_encode($result), true);
        return $results[0]['sku'];
    }

    public function checkWareHouseAndProductId($warehouseId, $productId)
    {
        $sql = $this->where("product_id", "=", $productId)->where("le_wh_id", "=", $warehouseId);
        $count  = $sql->count();
        return $count;

    }

    

    public function getProductsDetails($filterBy="",$page="", $page_size="", $orderBy="")
    {
        // print_r($filter_by);die;
        $sql = $this;
        if (!empty($orderBy)) {
            $orderByExplode = explode(" ", $orderBy);
            $sql = $sql->orderby($orderByExplode[0], $orderByExplode[1]);
        }

        // dd($sql->toSql());
        // print_r($filter_by);die;
        //Grid filters started
         /*if (!empty($filter_by)) {
            // print_r($filter_by);die;
            foreach ($filter_by as $filterByEach) {`
                // print_r($filterByEach);die;
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
                // echo $filter_query_field."<br>".$filter_query_operator."<br>".$filter_query_value;
                $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                
                if (in_array(trim($filter_query_operator), $operator_array)) {
                    $sql = $sql->where($filter_query_field, $filter_query_operator, (int) $filter_query_value);
                } else {
                    $sql = $sql->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                }
            }
        }*/
        //Grid filters ends here

        if(!empty($filterBy))
        {
            if (!empty($filterBy['manf_name'])) {
                $sql = $sql->whereIn('manufacturer_id', $filterBy['manf_name']);
            }

            if (!empty($filterBy['brand'])) {
            $sql = $sql->whereIn('brand_id', $filterBy['brand']);
            }

            if (!empty($filterBy['category'])) {
                $sql = $sql->whereIn('product_class_id', $filterBy['category']);
            }

           /* if (!empty($filterBy['kvi'])) {
                $sql = $sql->whereIn('kvi', $filterBy['kvi']);
            }

            if (!empty($filterBy['ean_upc'])) {
                $sql = $sql->whereIn('upc', $filterBy['ean_upc']);
            }

            if (!empty($filterBy['product_titles'])) {
                $sql = $sql->whereIn('product_id', $filterBy['product_titles']);
            }*/

            if (!empty($filterBy['shelf_life'])) {
                $sql = $sql->where('shelflife', $filterBy['shelf_life'])->where('shelf_life_uom', $filterBy['shelf_life_uom'][0]);
            }

            if (!empty($filterBy['product_char'])) {
                $all_char = Array('perishable', 'flammable', 'hazardous', 'odour', 'fragile');
                $diff_arry = array_diff($all_char, $filterBy['product_char']);
                foreach ($filterBy['product_char'] as $key => $value) {
                    $sql = $sql->where($value, '=', 1);
                }
                if (!empty($diff_arry)) {
                    foreach ($diff_arry as $key => $value) {
                        $sql = $sql->where($value, '=', 0);
                    }
                }
            }

            if (!empty($filterBy['product_form'])) {
                $sql = $sql->whereIn('product_form', $filterBy['product_form']);
            }

            /*if (isset($filterBy['mrp_max']) && ((int)$filterBy['mrp_max'] == (int)$filterBy['mrp_min'])) {
                $sql = $sql->where('mrp', [(int) $filterBy['mrp_max']]);
            }else if (isset($filterBy['mrp_max']) && ((int)$filterBy['mrp_max'] != (int)$filterBy['mrp_min'])) {
                $sql = $sql->whereBetween('mrp', [(int) $filterBy['mrp_min'], (int) $filterBy['mrp_max']]);
            }

            if (isset($filterBy['soh_max']) && ((int)$filterBy['soh_max'] == (int)$filterBy['soh_min'])) {
                $sql = $sql->where('soh', [(int) $filterBy['soh_min']]);
            }
            else if(isset($filterBy['soh_max']) && ((int)$filterBy['soh_max'] != (int)$filterBy['soh_min']))
            {
                $sql = $sql->whereBetween('soh', [(int) $filterBy['soh_min'], (int) $filterBy['soh_max']]);
            }
            
            if (isset($filterBy['map_max']) && ((int)$filterBy['map_max'] == (int)$filterBy['map_min'])) {
                $sql = $sql->where('map', [(int) $filterBy['map_max']]);
            }else if(isset($filterBy['map_max']) && ((int)$filterBy['map_max'] != (int)$filterBy['map_min'])) {
                $sql = $sql->whereBetween('map', [(int) $filterBy['map_min'], (int) $filterBy['map_max']]);   
            }

            if (isset($filterBy['inv_max']) && ((int)$filterBy['inv_max'] == (int)$filterBy['inv_min'])) {
                $sql = $sql->where('available_inventory', [(int) $filterBy['inv_max']]);
            }else if (!isset($filterBy['inv_max']) && ((int)$filterBy['inv_max'] != (int)$filterBy['inv_min'])) {
                $sql = $sql->whereBetween('available_inventory', [(int) $filterBy['inv_min'], (int) $filterBy['inv_max']]);
            }
*/
            /*if (!empty($filterBy['sku'])) {
                $sql = $sql->whereIn('sku', $filterBy['sku']);
            }*/
    }

        $count = $sql->count();
        // $query = $sql->skip((int) $page * (int) $page_size)->take((int) $page_size);
      $sql = $sql->where("soh", "=", 0)
                ->get(array('primary_image','dcname', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'product_id','category_name', 'sub_category_name', 'manufacturer_name', 'freebee_sku', 'frebee_desc', 'cfc_qty', 'is_Sellable', 'pack_type', 'reserved_qty', 'cp_enabled', 'esu', 'dit_qty', 'dnd_qty', 'quarantine_qty', 'esp', 'elp', 'state_id', 'ptrvalue'))->all();
                // dd($sql->toSql());
                // $results['results'] = json_decode($sql, true);
                $result = $sql;//json_decode($sql, true);
                foreach($result as $key=>$data){
            $po_details = $this->getPODetails($data['product_id']);
            // echo "<pre>";print_r($po_details);
            $result[$key]['po_code'] = (isset($po_details['po_code']) ? $po_details['po_code'] : '');
            $result[$key]['po_date'] = (isset($po_details['po_date']) ? $po_details['po_date'] : '');
            $result[$key]['vatpercentage'] = $this->getVatPercentage($data['product_id'], $data['state_id']);
            if($data['cfc_qty'] == 0)
            {
                $result[$key]['available_cfc_qty'] = 0;
            }
            else
            {
                $result[$key]['available_cfc_qty'] = ($data['soh']/$data['cfc_qty']);
            }
            
        }
        // echo "<pre>";print_r($result);die;

                return $result;
    }

     public function getPODetails($productId)
    {
        $sql = $this
                ->join("po_products", "po_products.product_id", "=", "vw_inventory_report.product_id")
                ->join("po", "po.po_id", "=","po_products.po_id")
                ->where("vw_inventory_report.product_id", "=", $productId)
                ->orderBy("po_products.created_at", "desc")
                ->orderBy("po_products.po_product_id", "desc")
                ->limit(1)
                ->get(array("po_code", "po.po_date"))->all(); 
        $returnval = $sql;//json_decode($sql, true);
        return (isset($returnval[0])?$returnval[0]:"");
    }


        public function getVatPercentage($productId, $buyerStateId)
    {

        $Current_date = date('Y-m-d H:i:s');
        $returnValue = "";
        $sql = DB::table("tax_class_product_map")
        ->join("tax_classes", "tax_classes.tax_class_id", "=", "tax_class_product_map.tax_class_id")
        ->join("master_lookup", "master_lookup.master_lookup_name", "=", "tax_classes.tax_class_type")
        ->where("tax_class_product_map.product_id", "=", $productId)
        ->whereIn("tax_classes.state_id", array($buyerStateId, $buyerStateId))
        ->whereIn("master_lookup.parent_lookup_id", array("10001"))
        ->where("tax_classes.status", "=", "Active")
        ->where("tax_classes.date_start", "<=", $Current_date)
        ->orderBy("tax_classes.date_start", "DESC")
        ->limit(1)
        ->get(array("tax_classes.tax_percentage"))->all();
        $jsonvals = json_decode(json_encode($sql), true);
        
        if($jsonvals)
        {
            $returnValue = $jsonvals[0]['tax_percentage'];
        }
        return $returnValue;
    }



        


    public function getAllProductsByOnlyWareHouseIdForExcel($warehouseId) {
        $rolesObj  = new Role();
        $productIDs = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        $prod_ids = array_keys($productIDs['products']);
        $sql = $this->where('le_wh_id', '=', $warehouseId);
        $sql = $sql->whereIn('product_id', $prod_ids);
        $result = $sql->get(array('primary_image', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'product_id','category_name', 'sub_category_name', 'manufacturer_name', 'freebee_sku', 'frebee_desc', 'cfc_qty', 'is_Sellable', 'pack_type', 'reserved_qty', 'cp_enabled', 'esu', 'dit_qty', 'dnd_qty', 'quarantine_qty', 'esp', 'elp', 'state_id', 'ptrvalue'))->all();
        $result = json_decode(json_encode($result), true);
        // print_r($result);die;
        foreach($result as $key=>$data){
            if($data['cfc_qty'] == 0)
            {
                $result[$key]['available_cfc_qty'] = 0;
            }
            else
            {
                $result[$key]['available_cfc_qty'] = ($data['available_inventory']/$data['cfc_qty']);
            }
            
        }
        return $result;
    }


    public function getAllProductsByWareHouse($filterBy = '') {
        // print_r($filterBy);die;
        $this->_roleRepo = new RoleRepo();
        // $warehousename = $this->wareHouseNameById($warehouseId);
        $result = array();
        $sql = $this->where("soh", "=", "0");

        if(!empty($filterBy))
        {
            if (!empty($filterBy['manf_name'])) {
                $sql = $sql->whereIn('manufacturer_id', $filterBy['manf_name']);
            }

            if (!empty($filterBy['brand'])) {
            $sql = $sql->whereIn('brand_id', $filterBy['brand']);
            }

            if (!empty($filterBy['category'])) {
                $sql = $sql->whereIn('product_class_id', $filterBy['category']);
            }

           /* if (!empty($filterBy['kvi'])) {
                $sql = $sql->whereIn('kvi', $filterBy['kvi']);
            }

            if (!empty($filterBy['ean_upc'])) {
                $sql = $sql->whereIn('upc', $filterBy['ean_upc']);
            }

            if (!empty($filterBy['product_titles'])) {
                $sql = $sql->whereIn('product_id', $filterBy['product_titles']);
            }*/

            if (!empty($filterBy['shelf_life'])) {
                $sql = $sql->where('shelflife', $filterBy['shelf_life'])->where('shelf_life_uom', $filterBy['shelf_life_uom'][0]);
            }

            if (!empty($filterBy['product_char'])) {
                $all_char = Array('perishable', 'flammable', 'hazardous', 'odour', 'fragile');
                $diff_arry = array_diff($all_char, $filterBy['product_char']);
                foreach ($filterBy['product_char'] as $key => $value) {
                    $sql = $sql->where($value, '=', 1);
                }
                if (!empty($diff_arry)) {
                    foreach ($diff_arry as $key => $value) {
                        $sql = $sql->where($value, '=', 0);
                    }
                }
            }

            if (!empty($filterBy['product_form'])) {
                $sql = $sql->whereIn('product_form', $filterBy['product_form']);
            }

            /*if (isset($filterBy['mrp_max']) && ((int)$filterBy['mrp_max'] == (int)$filterBy['mrp_min'])) {
                $sql = $sql->where('mrp', [(int) $filterBy['mrp_max']]);
            }else if (isset($filterBy['mrp_max']) && ((int)$filterBy['mrp_max'] != (int)$filterBy['mrp_min'])) {
                $sql = $sql->whereBetween('mrp', [(int) $filterBy['mrp_min'], (int) $filterBy['mrp_max']]);
            }

            if (isset($filterBy['soh_max']) && ((int)$filterBy['soh_max'] == (int)$filterBy['soh_min'])) {
                $sql = $sql->where('soh', [(int) $filterBy['soh_min']]);
            }
            else if(isset($filterBy['soh_max']) && ((int)$filterBy['soh_max'] != (int)$filterBy['soh_min']))
            {
                $sql = $sql->whereBetween('soh', [(int) $filterBy['soh_min'], (int) $filterBy['soh_max']]);
            }
            
            if (isset($filterBy['map_max']) && ((int)$filterBy['map_max'] == (int)$filterBy['map_min'])) {
                $sql = $sql->where('map', [(int) $filterBy['map_max']]);
            }else if(isset($filterBy['map_max']) && ((int)$filterBy['map_max'] != (int)$filterBy['map_min'])) {
                $sql = $sql->whereBetween('map', [(int) $filterBy['map_min'], (int) $filterBy['map_max']]);   
            }

            if (isset($filterBy['inv_max']) && ((int)$filterBy['inv_max'] == (int)$filterBy['inv_min'])) {
                $sql = $sql->where('available_inventory', [(int) $filterBy['inv_max']]);
            }else if (!isset($filterBy['inv_max']) && ((int)$filterBy['inv_max'] != (int)$filterBy['inv_min'])) {
                $sql = $sql->whereBetween('available_inventory', [(int) $filterBy['inv_min'], (int) $filterBy['inv_max']]);
            }
*/
            /*if (!empty($filterBy['sku'])) {
                $sql = $sql->whereIn('sku', $filterBy['sku']);
            }*/
    }

        
        
        $result = $sql->get(array('primary_image', 'dcname', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'product_id','category_name', 'sub_category_name', 'manufacturer_name', 'freebee_sku', 'frebee_desc', 'cfc_qty', 'is_Sellable', 'pack_type', 'reserved_qty', 'cp_enabled', 'esu', 'dit_qty', 'dnd_qty', 'quarantine_qty', 'esp', 'elp', 'state_id', 'ptrvalue'))->all();
        // echo "hii";print_r($result);die;
        $resultArr = json_decode(json_encode($result), true);
        /* $inventoryEditAccess = $this->_roleRepo->checkPermissionByFeatureCode('INV1002');
        foreach($resultArr as $key=>$data){
        
        if($inventoryEditAccess == 1)
            {
                $resultArr[$key]['actions'] = '<a data-type="edit" data-dcname="'. $warehousename .'" data-skuid="'. $data['sku'] .'" data-producttitle="'. $data['product_title'] .'" data-warehouseid="' . $warehouseId . '" data-prodid="' . $data['product_id'] . '" data-soh="'. $data['soh'] .'" data-atp = "'. $data['atp'] .'" data-toggle="modal" data-target="#edit-products"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            }
            else
            {
                $resultArr[$key]['actions'] = '';
            }
            
        }*/
        
        if(isset($resultArr)){
            $result = $resultArr;
        } else {
            $result = '';
        }
        // print_r($result);die;
        return $result;
    }


}
