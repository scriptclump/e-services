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
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Tax\Models\Product;
use Log;
use Mail;
use Carbon\Carbon;
use App\Lib\Queue;
use \App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;
use Cache;
use Caching;
use App\Modules\BusinessUnit\Models\businessUnitDashboardModel;
use App\Modules\Inventory\Models\InventorySOH;
use App\Modules\Orders\Controllers\OrdersController;


class Inventory extends Model {

    protected $table        = "vw_inventory_report";
    protected $catList      = '';
    protected $brands       = '';
    protected $tempArray =array();
  
    public function getAllInventory() {
        $sql = $this->groupBy('le_wh_id')->get(array('dcname', 'cpvalue', 'ptrvalue', 'mrpvalue', 'le_wh_id', 'espvalue'))->all();
        return $sql;
    }

    public function getAllProductsByWareHouse($warehouseId, $filterBy = '', $productId) {
        
        $this->_roleRepo    = new RoleRepo();
        $warehousename      = $this->wareHouseNameById($warehouseId);
        $result             = array();
        $sql                = $this->where('vw_inventory_report.le_wh_id', '=', $warehouseId);

        

        if($productId != 0)
        {
            $sql = $sql->where('product_id', '=', $productId);
        }

        if(!empty($filterBy))
        {
            if (!empty($filterBy['manf_name'])) {
                $sql = $sql->whereIn('manufacturer_id', $filterBy['manf_name']);
            }
                
            if (isset($filterBy['sellable'])) {
                $sql = $sql->where('is_sellable', $filterBy['sellable']);
            }

            if (isset($filterBy['cpEnabled'])) {
                $sql = $sql->where('cp_enabled', $filterBy['cpEnabled']);
            }
            
            if (!empty($filterBy['brand'])) {
                $sql = $sql->whereIn('brand_id', $filterBy['brand']);
            }

            if (!empty($filterBy['category'])) {
                $sql = $sql->whereIn('product_class_id', $filterBy['category']);
            }

            if (!empty($filterBy['kvi'])) {
                $sql = $sql->whereIn('kvi', $filterBy['kvi']);
            }

            if (!empty($filterBy['ean_upc'])) {
                $sql = $sql->whereIn('upc', $filterBy['ean_upc']);
            }

            if (!empty($filterBy['product_titles'])) {
                $sql = $sql->whereIn('product_id', $filterBy['product_titles']);
            }

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

            if (isset($filterBy['mrp_max']) && ((int)$filterBy['mrp_max'] == (int)$filterBy['mrp_min'])) {
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

            if (!empty($filterBy['sku'])) {
                $sql = $sql->whereIn('sku', $filterBy['sku']);
            }
    }

    
        $allReplanishmencodes = $this->getReplanishmentCodes();
        $result['results'] = $sql->get(array('inv_id', 'primary_image', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'ptrvalue', 'cp', 'vw_inventory_report.le_wh_id', 'product_id', 'reserved_qty', 'quarantine_qty', 'inv_display_mode', 'dit_qty', 'dnd_qty', 'product_group_id', 'min-pickface-replenishment as replanishment_level', 'replenishment_UOM', 'star', 'esp'))->all();
            
        $resultArr              = json_decode(json_encode($result['results']), true);
        $inventoryEditAccess    = $this->_roleRepo->checkPermissionByFeatureCode('INV1002');
        foreach($resultArr as $key=>$data){
            $resultArr[$key]['bin_location'] = $this->getBinNames($data['le_wh_id'], $data['product_id']);
            $resultArr[$key]['re_pending_qty'] = $this->pendingReturns($data["product_id"], $warehouseId);
            $resultArr[$key]['replanishment_uom'] = isset($allReplanishmencodes[$data['replenishment_UOM']])?$allReplanishmencodes[$data['replenishment_UOM']]:"";
            $resultArr[$key]['product_id'] = "<a href='/editproduct/".$data['product_id']."' target='_blank'><strong>".$data['product_id']."</strong></a>";
            if($inventoryEditAccess == 1) {
                $resultArr[$key]['actions'] = '<a data-type="edit" data-ditqty="'.$data['dit_qty'].'" data-dndqty = "'.$data['dnd_qty'].'" data-dcname="'. $warehousename .'" data-skuid="'. $data['sku'] .'" data-producttitle="'. $data['product_title'] .'" data-warehouseid="' . $warehouseId . '" data-prodid="' . $data['product_id'] . '" data-soh="'. $data['soh'] .'" data-atp = "'. $data['atp'] .'" data-toggle="modal" data-target="#edit-products"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            }
            else {
                $resultArr[$key]['actions'] = '';
            }
            
        }
        
        if(isset($resultArr)){
            $result['results'] = $resultArr;
        } else {
            $result['results'] = '';
        }
        return $result;
    }
    
    public function pendingReturns($productId, $wh_id) {
        $re_query = DB::table("gds_returns")
                    ->select([DB::raw("SUM(qty) as re_qty")])
                    ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_returns.gds_order_id")
                    ->where("approval_status", "!=", 1)
                    ->where("approval_status", "!=", 0)
                    ->where("approval_status", "!=", NULL)
                    ->where("gds_orders.le_wh_id", "=", $wh_id)
                    ->where("product_id", $productId)
                    ->get()->all();

        if(isset($re_query[0])){
            $re_query_en = json_decode(json_encode($re_query[0]), true);
        }
        else{
            $re_query_en = json_decode(json_encode(reset($re_query)), true);
        }

        if($re_query_en["re_qty"]){
            $final_res = $re_query_en["re_qty"];
        } else {
            $final_res = 0;
        }
        return $final_res;
    }

    public function getAllProductsByOnlyWareHouseId($warehouseId) {
        $rolesObj               = new Role();
        $productIDs             = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        $prod_ids               = array_keys($productIDs['products']);
        $sql                    = $this->where('le_wh_id', '=', $warehouseId);
        $sql                    = $sql->whereIn('product_id', $prod_ids);
        $result                 = $sql->get('inv_id', 'primary_image', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'ptrvalue', 'cp', 'le_wh_id', 'product_id', 'reserved_qty', 'quarantine_qty')->all();
        $result                 = json_decode(json_encode($result), true);
        
        foreach($result as $key=>$data){
           if($data['cfc_qty'] == 0)
            {
                $result[$key]['available_cfc_qty'] = 0;
            }
            else
            {
                $result[$key]['available_cfc_qty'] = ($data['available_inventory']/$data['cfc_qty']);
            }
            //Changed as per Satish || Date : Oct 20 2016
            // New calculation should be '$data['available_inventory']/$data['cfc_qty']'


            //$result[$key]['available_cfc_qty'] = ($data['soh']/$data['cfc_qty']);
            // $result[$key]['available_cfc_qty'] = 0;
        }
        return $result;
    }

    
public function getCategoryList() {
        $rolesObj           = new Role();
        $DataFilter         = json_decode($rolesObj->getFilterData(8, Session::get('userId')), true);
       if(!isset($DataFilter['category']) || empty($DataFilter['category']) ){
            return 0;
        }

        $categoryList       = isset($DataFilter['category']) ? $DataFilter['category'] : [];
        $cat                = DB::table('categories')
                                ->whereIn('categories.category_id', $categoryList)
                                ->get(array('category_id', 'cat_name', 'parent_id'))->all();

        $cat                = json_decode(json_encode($cat), true);

        $parents            = array_unique(array_column($cat, 'parent_id'));

        $relationalArray    = array(); $catDetail = array();
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
        $rolesObj       = new Role();
        $DataFilter     = json_decode($rolesObj->getFilterData(7, Session::get('userId')), true);
       if(!isset($DataFilter['brand']) || empty($DataFilter['brand']) ){
            return 0;
        }

        $brandList = isset($DataFilter['brand']) ? $DataFilter['brand'] : [];
        $brandList = array_keys($brandList);
        $brand = DB::table('brands')
            ->whereIn('brands.brand_id', $brandList)
            ->get(array('brand_id', 'brand_name', 'parent_brand_id'))->all();

        $brand              = json_decode(json_encode($brand), true);
        
        $parents            = array_unique(array_column($brand, 'parent_brand_id'));
        $relationalArray    = array(); $brandDetail = array();
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
        $filter_array                       = Array();
        $warehouses_table                   = DB::table('legalentity_warehouses');
        $products_table                     = DB::table('products');
        $product_titles                     = DB::table("products");
        $brands_table                       = DB::table('brands');
        $category_table                     = DB::table('categories');

        $filter_array['category_name']      = $this->getCategoryList();
        $filter_array['brand_name']         = $this->getBrandList();
        $roleOb                             = new Role();
        $rbac_manfacturer_name              = json_decode($roleOb->getFilterData(11, Session::get('userId')), true);
        $filter_array['manfacturer_name']   = $rbac_manfacturer_name['manufacturer'];
        
        if (Session('roleId') != '1') {
            $products_table                 = $products_table->where('legal_entity_id', Session::get('legal_entity_id'));
            $product_titles                 = $product_titles->where('legal_entity_id', Session::get('legal_entity_id'));
            $brands_table                   = $brands_table->where('legal_entity_id', Session::get('legal_entity_id'));
            $category_table                 = $category_table->where('legal_entity_id', Session::get('legal_entity_id'));
        }

        $rolesObj       = new Role();
        $wh_list = json_decode($rolesObj->getWarehouseData(Session::get('userId'),6,0), 1);
        $dc_acess_list = isset($wh_list['118001']) ? $wh_list['118001'] : 'NULL';
        $filter_array['dc_name'] =DB::table('legalentity_warehouses')->whereIn('le_wh_id', explode(',', $dc_acess_list))->pluck('lp_wh_name', 'le_wh_id')->all();

//        $filter_array['manfacturer_name'] = DB::table('legal_entities')->distinct('business_legal_name')->where('legal_entity_type_id', '1006')/*->where('legal_entity_id', Session::get('legal_entity_id'))*/->where('business_legal_name', '!=', NULL)->where('business_legal_name', '!=', '')->orderBy('business_legal_name', 'asc')->lists('business_legal_name', 'legal_entity_id');
//        $filter_array['brand_name'] = $brands_table->distinct('brand_name')->where('brand_name', '!=', NULL)->where('brand_name', '!=', '')->orderBy('brand_name', 'asc')->lists('brand_name');
//        $filter_array['category_name'] = $category_table)->distinct('cat_name')->where('cat_name', '!=', NULL)->where('cat_name', '!=', '')->orderBy('cat_name', 'asc')->lists('cat_name');
        //$filter_array['kvi']                = DB::table('master_lookup')->where('mas_cat_id', '=', 69)->orderBy('master_lookup_name', 'asc')->lists('master_lookup_name', 'value');
        $filter_array['upc_ean']            = $products_table->distinct('upc')->where('upc', '!=', NULL)->where('upc', '!=', '')->orderBy('upc', 'asc')->pluck('upc')->all();
        $filter_array['shelflife']          = $products_table->distinct('shelf_life')->where('shelf_life', '!=', NULL)->where('shelf_life', '!=', '')->orderBy('shelf_life', 'asc')->pluck('shelf_life')->all();
        $filter_array['shelflife_uom']      = DB::table('master_lookup')->where('mas_cat_id', '=', 71)->orderBy('value', 'asc')->pluck('master_lookup_name', 'value')->all();
        $filter_array['product_form']       = DB::table('master_lookup')->where('mas_cat_id', '=', 72)->orderBy('master_lookup_name', 'asc')->pluck('master_lookup_name', 'value')->all();
        $filter_array['product_titles']     = $product_titles->pluck('product_title', 'product_id')->all();
        
        //$min_mrp_explode                    = explode('.', $this->min('mrp'));
        //$filter_array['min_mrp']            = $min_mrp_explode[0];
        //$max_mrp_explode                    = $this->max('mrp');
        //$filter_array['max_mrp']            = $max_mrp_explode;
        //$filter_array['min_soh']            = $this->min('soh');
        //$filter_array['max_soh']            = $this->max('soh');
        //$filter_array['min_invtr']          = $this->min('available_inventory');
        //$filter_array['max_invtr']          = $this->max('available_inventory');
        //$filter_array['min_map']            = $this->min('map');
        //$filter_array['max_map']            = $this->max('map');
        //$filter_array['sku']                = $this->distinct('sku')->where('le_wh_id', '!=', NULL)->where('le_wh_id', '!=', '')->orderBy('sku', 'asc')->lists('sku');
        return $filter_array;
    }

    public function updateProducts($excess_qty, $sohval, $wareId, $prodId, $sku, $comments, $dit_qty, $dnd_qty, $reason="", $bulk_upload_id="")
    {
    try{
        $approval_flow_func                 = new CommonApprovalFlowFunctionModel();
        
        $returnval                          = 1;
        $timestamp                          = date('Y-m-d H:i:s');
        $oldvalues                          = $this->getOldSOHAndATPValues($prodId, $wareId);
        
        if($bulk_upload_id != "")
        {
            //when bulk upload came then only
            if(strlen($sohval) == 0)
                $sohval = $oldvalues['soh'];

            // if(strlen($atpval) == 0)
            //     $atpval = $oldvalues['atp'];

            if(strlen($dit_qty) == 0)
                // $dit_qty = $oldvalues['dit_qty'];
                $dit_qty = 0;

            if(strlen($dnd_qty) == 0){
                // $dnd_qty = $oldvalues['dnd_qty'];
                $dnd_qty = 0;
            }
        }

        $stock_difference                   = $sohval - $oldvalues['soh'];
        $dit_diff                           = $dit_qty - $oldvalues['dit_qty'];
        $dnd_diff                           = $dnd_qty - $oldvalues['dnd_qty'];
        $user_ID                            = Session::get('userId');
        if($bulk_upload_id == "" || $bulk_upload_id == 0 || $bulk_upload_id == NULL)
        {
            $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('Inventory', 'drafted', \Session::get('userId'));
        }
        else
        {
            $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('Inventory Bulk Upload', 'drafted', \Session::get('userId'));            
        }
        
        
        if($res_approval_flow_func['status'] == 1)
        {
            $curr_status_ID = $res_approval_flow_func['currentStatusId'];
            $nextlevelStatusId = $res_approval_flow_func['data'][0]['nextStatusId'];
            $quarantine_QTY = ($dit_qty+$dnd_qty);
            $getcuurent_quarantine_qty  = $this->getInventoryDetailsBasedOnProductId($prodId, $wareId);
            $curr_quarantine_qty = $getcuurent_quarantine_qty[0]['quarantine_qty'] + $quarantine_QTY;
            $update_inventory = DB::table("inventory")
                ->where("product_id", "=", $prodId)
                ->where("le_wh_id", "=", $wareId)
                ->update(["quarantine_qty" => $curr_quarantine_qty]);

            $insert_array = array("product_id"      => $prodId,
                               "le_wh_id"           => $wareId,
                               "activity_type"      => $reason,
                               "approval_status"    => $nextlevelStatusId,
                               // "stock_diff"         => $stock_difference,
                               // "old_soh"            => $oldvalues['soh'],
                               // "new_soh"            => $sohval,
                               // "old_atp"            => $oldvalues['atp'],
                               // "new_atp"            => $atpval,
                               // "old_dit_qty"        => $oldvalues['dit_qty'],
                               // "new_dit_qty"        => $dit_qty,
                               "dit_diff"           => $dit_qty,

                               "dnd_diff"           => $dnd_qty,
                               "excess"             => $excess_qty,
                               // "old_dnd_qty"        => $oldvalues['dnd_qty'],
                               // "new_dnd_qty"        => $dnd_qty,

                               "created_by"         => $user_ID,
                               "approved_by"        => $user_ID,
                               "remarks"            => $comments
                               ,"bulk_upload_id"    => $bulk_upload_id,
                               "quarantine_qty"     => $quarantine_QTY
                               );
            $inv_track_id = DB::table("inventory_tracking")->insertGetId($insert_array);
            if(!$inv_track_id)
            {
                return "failed"; // here dit_diff and dnd_diff data type is un-signed if negitive value came query won't execute then we are returning failed
            }
            if($bulk_upload_id != "")
            {
                // $approval_flow_func->storeWorkFlowHistory('Inventory Bulk Upload', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId'));
            }
            else
            {
                $approval_flow_func->storeWorkFlowHistory('Inventory', $inv_track_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId'));                
            }
            

            //Check if current step is final..
            if($res_approval_flow_func['data'][0]['isFinalStep'] == 1)
            {
                $tableUpdateID = 1;
                // update the inventory table
                $updateInventory = $this->updateInventoryTable($inv_track_id);
                $update_tracking_table = $this->updateTrackingTableWithStatus($tableUpdateID, $inv_track_id);
            }
        }
        elseif($res_approval_flow_func['status'] == 0)
        {
            $returnval = 0;
        }
        
        return $returnval;
            
    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    }        

    }


    public function updateProductsForReplanishment($replanishment_level, $replanishment_uom, $product_id, $warehouse_id)
   {
        try {
            $sql = DB::table("inventory")
                    ->where("le_wh_id", $warehouse_id)
                    ->where("product_id", $product_id)
                    ->update(["min-pickface-replenishment" => $replanishment_level, "replenishment_UOM" => $replanishment_uom]);
            return $sql;            
            
        } catch (Exception $e) {
            
        }
   } 

        public function updateStatusAWF($table,$unique_column,$approval_unique_id, $next_status_id){
        try{
            $status = explode(',',$next_status_id);
            $new_status = ($status[1]==0)?$status[0]:$status[1];
            $invoice = array(
                'approval_status'=>$new_status,
                'approved_by'=>\Session::get('userId'),
                'approved_at'=>date('Y-m-d H:i:s')
            );
            DB::table($table)->where($unique_column, $approval_unique_id)->update($invoice);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    

    public function wareHouseNameById($warehouseId)
    {
        $result         = DB::table("legalentity_warehouses")->where('le_wh_id', '=', $warehouseId)->get(array('lp_wh_name'))->all();
        $results        = json_decode(json_encode($result), true);
        if(isset($results)){
            $results = reset($results);
        }
        return $results['lp_wh_name'];
    }

    public function getSkuByProductId($product_id)
    {
        $result         = DB::table("products")->where("product_id", "=", $product_id)->get(array('sku'))->all();
        $results        = json_decode(json_encode($result), true);
        if(isset($results)){
            $results = reset($results);
        }
        return $results['sku'];
    }


    public function checkWareHouseAndProductId($warehouseId, $productId)
    {
        $sql        = $this->where("product_id", "=", $productId)->where("le_wh_id", "=", $warehouseId);
        $count      = $sql->count();
        return $count;

    }

    public function updating_SOH_ATP_Values($productsData, $warewhouseid, $URL="")
    {
    try{
        $approval_flow_func     = new CommonApprovalFlowFunctionModel();
        $rolesObj               = new Role();
        $inventorySOH           = new InventorySOH();
        $errorArray             = array();
        $mainArr                = array();
        $updateCounter          = 0;
        $elpespCount            = 0;
        $roleRepo               = new RoleRepo();
        $validateelpesp         = $roleRepo->checkPermissionByFeatureCode('VLDESPELP001');
        //$allproductsForUSer     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        //$allproductIds          = $allproductsForUSer['products'];
        $getallWarehouseIds     = $this->filterOptions();
        $i=1;
        $timestamp              = date('Y-m-d H:i:s');
        $current_timeStamp      = strtotime($timestamp);
       
        $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Inventory Bulk Upload', 'drafted', \Session::get('userId'));
        
        $curr_status_ID         = $res_approval_flow_func['currentStatusId'];
        $nextlevelStatusId      = $res_approval_flow_func['data'][0]['nextStatusId'];

        $insert_array = array("filepath"                => $URL,
                               "approval_status"         => $nextlevelStatusId,
                               "created_by"              => \Session::get('userId')
                            );

        $bulk_upload_id = DB::table("inventory_bulk_upload")->insertGetId($insert_array);
        // $approval_flow_func->storeWorkFlowHistory('Inventory Bluk Upload', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId'));
        $negvalinvupload = array();
        $cnt_inv = 3;
        $neg_cnt =0;
        foreach ($productsData as $excelValue) {
                if($excelValue['reason']!="") {
                $product_ID         = $excelValue['product_id'];
         
                $getinventory_table_values = $this->getInventoryDetailsBasedOnProductId($product_ID, $warewhouseid);
                $inventory_table_soh = $getinventory_table_values[0]['soh'];
                $inventory_table_dit_qty = $getinventory_table_values[0]['dit_qty'];
                $inventory_table_dnd_qty = $getinventory_table_values[0]['dnd_qty'];
                $inventory_table_quarantine = $getinventory_table_values[0]['quarantine_qty'];  

                $exce_quarantine_qty = $excelValue['missing']+$excelValue['dit_qty'];
                $excel_dit = $excelValue['dit_qty'];
                $excel_dnd = $excelValue['missing'];
                $excel_excess = $excelValue['excess'];

                $resulted_SOH       = $inventory_table_soh - $exce_quarantine_qty +$excel_excess;
                $resulted_dit_qty   = $inventory_table_dit_qty + $excel_dit;

                $resulted_dnd_qty   = $inventory_table_dnd_qty + $excel_dnd - $excel_excess;
                if($resulted_dnd_qty <0 || $resulted_dit_qty <0 || $resulted_SOH <0 ){
//                    Log::info("count__".$cnt_inv);
                    $resulted_quaratine_qty = $getinventory_table_values[0]['quarantine_qty'];
                    $errorArray['wrongCombination'][] = 'Line #' . ($cnt_inv)." Product Does not existed for this warehouse <br>";
                    $negvalinvupload["negative_values"][] =  'Line #' . ($cnt_inv)." Uploaded Inventory Sheet got negative values. Product Id #".$product_ID." SOH # ".$resulted_SOH." DIT QTY # ".$resulted_dit_qty." DND QTY # ".$resulted_dnd_qty." Quarantine QTY # ".$resulted_quaratine_qty;
                    
                }
                $neg_cnt++;
            }
            $cnt_inv++;
           
        }
        if(!empty($negvalinvupload))
        {
            $mainArr['success'] = $negvalinvupload;
            $mainArr['error_count'] = (isset($negvalinvupload['negative_values'])?count($negvalinvupload['negative_values']):0);
            $mainArr['reference'] = $current_timeStamp;
            $mainArr['updated_count']            = 0;//$neg_cnt;
            $mainArr['dpulicate_count']            = 0;
            $log_array = $mainArr;
            UserActivity::excelUploadFileLogs("INVENTORY", $current_timeStamp, $URL, $log_array);
            return $mainArr;
        }
        foreach ($productsData as $value) {

            $elpesp_flag=0;
            $neg_inv_flag=0;
        
           $countWareAndProductId = $this->checkWareHouseAndProductId($warewhouseid, $value['product_id']);

           // if(!isset($allproductIds[$value['comments']]))
           //  {
           //      $errorArray['commenterrors'][] = $i+2;
           //      $i++;
           //      continue;
           //  }
           // echo $i++."<br>";

           if($countWareAndProductId < 1)
           {
                $errorArray['wrongCombination'][] = 'Line #' . ($i+2)." Product Does not existed for this warehouse <br>";
                $i++;
                continue;
           }

           
           if($validateelpesp)
           {
               $check_espandelp_indcandapob=$inventorySOH->checkEspElpIn_Parent_DC_APOB($value['product_id'],$warewhouseid);

               if($check_espandelp_indcandapob===false)
               {
                    $errorArray['elpespnotFound'][] = 'Line #' . ($i+2)." Product Does not have ELP,ESP for this warehouse or Parent DC/APOB <br>";
                    $elpesp_flag=1;
                    $elpespCount++;
                    $i++;
                    continue;
               }
           }

           /* if(!isset($allproductIds[$value['product_id']]))
            {
                $errorArray['productIderrors'][] = 'Line #' . ($i+2)." Invalid Product Ids <br>";
                $i++;
                continue;
            }*/
            //checyking all inputs weather it is empty or not
            if(empty($value['excess']) && empty($value['dit_qty']) && empty($value['missing']))
            {
                $errorArray['commenterrors'][] = 'Line #' . ($i+2)." Blank inputs  <br>";
                $i++;
                continue;
            }
            if($value['excess'] == 0 && $value['dit_qty'] == 0 && $value['missing'] == 0)
            {
                $errorArray['commenterrors'][] = 'Line #' . ($i+2)." Blank inputs  <br>";
                $i++;
                continue;
            }
            if($value['comments'] == "" || empty($value['comments']))
           {
                $errorArray['commenterrors'][] = 'Line #' . ($i+2)." Empty commments <br>";
                $i++;
                continue;
           }
            if($value['reason'] == "" || empty($value['reason']))
           {
                $errorArray['reasonerrors'][] = 'Line #' . ($i+2)." Empty Reasons <br>";
                $i++;
                continue;
           }

           $check_valid_reason_or_not = $this->getReasonCodeBasedOnReasonType($value['reason']);
           if($check_valid_reason_or_not[0] == 0)
           {
                $errorArray['reason_mismatch_errors'][] = 'Line #' . ($i+2)." Invalid Reasons <br>";
                $i++;
                continue;
           }
           
           if((!is_int($value['soh']) && ($value['soh']!='')) || ($value['soh'] < 0 && ($value['soh']!='') ))
           {
                $errorArray['soherrors'][] = 'Line #' . ($i+2)." Invalid data in SOH <br>";
                $i++;
                continue;
           }
          // Log::info("i m in exce____".$value['reason']);
            if(($value['excess'] < 0) || (!is_int($value['excess']) && ($value['excess']!='')) || ($value['excess'] < 0 && $value['excess'] ==''))
           {
           // Log::info("i m in error_____".$value['excess']);
                $errorArray['atperrors'][] = 'Line #' . ($i+2)." Invalid data in Excess <br>";
                $i++;
                continue;
           }
           if((!is_int($value['dit_qty']) && ($value['dit_qty']!='')) || ($value['dit_qty'] < 0 && ($value['dit_qty']!='') ))
           {
                $errorArray['diterrors'][] = 'Line #' . ($i+2)." Invalid data in DIT QTY <br>";
                $i++;
                continue;
           }

           if((!is_int($value['missing']) && ($value['missing']!='')) || ($value['missing'] < 0) && ($value['missing']!='') )
           {
                $errorArray['dnderrors'][] = 'Line #' . ($i+2)." Invalid data in Missing <br>";
                $i++;
                continue;
           }
           $dndditsum=$value['missing']+$value['dit_qty'];
           $checkinv=$this->checkInvNegativeAgainstOrderQty($warewhouseid,$value['product_id'],$value['excess'],$dndditsum);

           if($checkinv['status']==400)
           {
            $errorArray['negativesoh'][]=$checkinv['message'];
            $i++;
            $neg_inv_flag++;
            continue;
           }
           $getcurrent_soh = $this->getSOH($value['product_id'], $warewhouseid);
           $total_orderd_qty = $this->getOrderdQty($value['product_id'], $warewhouseid); // getting here total orderd qty against product and warehouseId
           $checkcount_tracking_table =$this->getOpenProductsInTracking_WorkFlow($value['product_id'], $warewhouseid);

           $getOldValues = $this->getOldSOHAndATPValues($value['product_id'], $warewhouseid);
           $curr_dit = $value['dit_qty'];
           $curr_dnd = $value['missing'];

           if(strlen($value['dit_qty']) == 0)
                $curr_dit = $getOldValues['dit_qty'];

            if(strlen($value['missing']) == 0)
                    $curr_dnd = $getOldValues['dnd_qty'];


           // $dit_diff = $curr_dit - $getOldValues['dit_qty'];
           // $dnd_diff = $curr_dnd - $getOldValues['dnd_qty'];

           

           if($curr_dit < 0 || $curr_dnd < 0)
           {
                $errorArray['dnderrors'][] = 'Line #' . ($i+2)." Negative(-ve) values are not allowed for DIT or Missing !! <br>";
                $i++;
                continue;
           }

           // $resulted_soh = ($getcurrent_soh[0]['soh'] - $total_orderd_qty) - ($dit_diff + $dnd_diff);
           $resulted_soh =0;
           if(!empty($curr_dit) && !empty($curr_dnd))
            $resulted_soh = ($getcurrent_soh[0]['soh'] - $total_orderd_qty) - ($curr_dit + $curr_dnd);

           if($resulted_soh < 0)
           {
                $errorArray['dnderrors'][] = 'Line #' . ($i+2)." sum of dit qty and missing qty should be less than soh!! <br>";
                $i++;
                continue;
           }

           if($checkcount_tracking_table != 0)
           {
                $errorArray['dnderrors'][] = 'Line #' . ($i+2)." Error !! Approval request for same product is pending. Please close pending requests first to continue. <br>";
                $i++;
                continue;
           }


           // if(!isset($getallWarehouseIds['dc_name'][$warewhouseid]))
           // {
           //      $errorArray['warehouseerrors'][] = $i+2;
           //      $i++;
           //      continue;
           // }

           
       if($elpesp_flag==0 && $neg_inv_flag==0){
           $updatevals = $this->updateProducts($value['excess'], $value['soh'], $warewhouseid, $value['product_id'], $value['sku'], $value['comments'], $value['dit_qty'], $value['missing'], $check_valid_reason_or_not[0], $bulk_upload_id);

            if($updatevals == "failed")
            {
                $errorArray['dnderrors'][] = 'Line #' . ($i+2)." Quarantine Quantity always be less than or equals to soh!! <br>"; //server side validations
                $i++;
                continue;
            }           
           
           if($updatevals == 1)
           {
                $updateCounter++;
           }

           if($updatevals == 0)
           {
            $errorArray['samerecords'][] = 'Line #' . ($i+2)." Duplicate data <br>";
           }
        }

            $i++;
       }

       if($updateCounter > 0)
       $approval_flow_func->storeWorkFlowHistory('Inventory Bulk Upload', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId')); //creating tickets
       $mainArr['success']                  = $errorArray;
       $mainArr['dpulicate_count']          = (isset($errorArray['samerecords'])?count($errorArray['samerecords']):0);
       $mainArr['updated_count']            = $updateCounter;
       $wrong_combination                   = (isset($errorArray['wrongCombination'])?count($errorArray['wrongCombination']):0);
       $product_errors                      = (isset($errorArray['productIderrors'])?count($errorArray['productIderrors']):0);
       $soh_error_count                     = (isset($errorArray['soherrors'])?count($errorArray['soherrors']):0);
       $atp_error_count                     = (isset($errorArray['atperrors'])?count($errorArray['atperrors']):0);
       $commetErrorsCount                   = (isset($errorArray['commenterrors'])?count($errorArray['commenterrors']):0);
       $reasonErrorsCount                   = (isset($errorArray['reasonerrors'])?count($errorArray['reasonerrors']):0);
       $reason_mismatch_Count               = (isset($errorArray['reason_mismatch_errors'])?count($errorArray['reason_mismatch_errors']):0);
       $ditErrorsCount                      = (isset($errorArray['diterrors'])?count($errorArray['diterrors']):0);
       $dndErrorsCount                      = (isset($errorArray['dnderrors'])?count($errorArray['dnderrors']):0);
       $negativesohError                  = (isset($errorArray['negativesoh'])?count($errorArray['negativesoh']):0);

       $mainArr['error_count']              = $wrong_combination + $product_errors + $soh_error_count + $atp_error_count + $commetErrorsCount + $ditErrorsCount + $dndErrorsCount+$reasonErrorsCount + $reason_mismatch_Count+$negativesohError; 
       $elpespnotfoundCount                      = (isset($errorArray['elpespnotFound'])?count($errorArray['elpespnotFound']):0);
       $mainArr['elpesp_count']              =   $elpespnotfoundCount;

       $log_array                           = $mainArr;
       $mainArr['reference'] = $current_timeStamp;
       UserActivity::excelUploadFileLogs("INVENTORY", $current_timeStamp, $URL, $log_array);
       
       return $mainArr;
    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    }
    }

    public function updatingReplanishmentQty($productsData, $warewhouseid, $URL)
    {
       try {
            // echo "model file";print_r($productsData);die;
        // $approval_flow_func     = new CommonApprovalFlowFunctionModel();
        $rolesObj               = new Role();
        $inventorySOH           = new InventorySOH();
        $errorArray             = array();
        $mainArr                = array();
        $updateCounter          = 0;
        $allproductsForUSer     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        $allproductIds          = $allproductsForUSer['products'];
        $getallWarehouseIds     = $this->filterOptions();
        $i=1;
        $timestamp              = date('Y-m-d H:i:s');
        $current_timeStamp      = strtotime($timestamp);
       
        $allReplanishmenttCode = $this->getReplanishmentValue();
        $roleRepo               = new RoleRepo();
        $validateelpesp         = $roleRepo->checkPermissionByFeatureCode('VLDESPELP001');

       foreach ($productsData as $value) {
        
           $countWareAndProductId = $this->checkWareHouseAndProductId($warewhouseid, $value['product_id']);

           // if(!isset($allproductIds[$value['comments']]))
           //  {
           //      $errorArray['commenterrors'][] = $i+2;
           //      $i++;
           //      continue;
           //  }
           // echo $i++."<br>";
           if($countWareAndProductId < 1)
           {
                $errorArray['wrongCombination'][] = 'Line #' . ($i+2)." Product Does not existed for this warehouse <br>";
                $i++;
                continue;
           }

           
           if($validateelpesp)
           {

               $check_espandelp_indcandapob=$inventorySOH->checkEspElpIn_Parent_DC_APOB($value['product_id'],$warewhouseid);

               if($check_espandelp_indcandapob===false)
               {
                    $errorArray['elpespnotFound'][] = 'Line #' . ($i+2)." Product Does not have ELP,ESP for this warehouse or Parent DC/APOB <br>";
                    $i++;
                    continue;
               }
           }

            if(!isset($allproductIds[$value['product_id']]))
            {
                $errorArray['productIderrors'][] = 'Line #' . ($i+2)." Invalid Product Ids <br>";
                $i++;
                continue;
            }

            if($value['replenishment_level'] == "" || empty($value['replenishment_level']))
           {
                $errorArray['replanishmenterrors'][] = 'Line #' . ($i+2)." Empty Replanishment Level <br>";
                $i++;
                continue;
           }

           if(!is_numeric($value['replenishment_level']))
           {
                $errorArray['replanishmenterrors'][] = 'Line #' . ($i+2)." Invalid Replanishment level <br>";
                $i++;
                continue;
           }

          if($value['replenishment_level'] < 0)
           {
                $errorArray['replanishmenterrors'][] = 'Line #' . ($i+2)." Invalid Replanishment level <br>";
                $i++;
                continue;
           }

            if($value['replenishment_uom'] == "" || empty($value['replenishment_uom']))
           {
                $errorArray['replanishment_uom_errors'][] = 'Line #' . ($i+2)." Empty Replanishment UOM <br>";
                $i++;
                continue;
           }

           $check_valid_reason_or_not = $this->getReplanishmentType($value['replenishment_uom']);
           if($check_valid_reason_or_not[0] == 0)
           {
                $errorArray['reason_mismatch_errors'][] = 'Line #' . ($i+2)." Invalid Replanishment UOM <br>";
                $i++;
                continue;
           }
        
           $updatevals = $this->updateProductsForReplanishment($value['replenishment_level'], $allReplanishmenttCode[$value['replenishment_uom']], $value['product_id'], $warewhouseid);
           
           if($updatevals == 1)
           {
                $updateCounter++;
           }

           if($updatevals == 0)
           {
            $errorArray['samerecords'][] = 'Line #' . ($i+2)." Duplicate data <br>";
           }

            $i++;
       }
       
       $mainArr['success']                  = $errorArray;
       $mainArr['dpulicate_count']          = (isset($errorArray['samerecords'])?count($errorArray['samerecords']):0);
       $mainArr['updated_count']            = $updateCounter;
       $wrong_combination                   = (isset($errorArray['wrongCombination'])?count($errorArray['wrongCombination']):0);
       $product_errors                      = (isset($errorArray['productIderrors'])?count($errorArray['productIderrors']):0);
       // $soh_error_count                     = (isset($errorArray['soherrors'])?count($errorArray['soherrors']):0);
       // $atp_error_count                     = (isset($errorArray['atperrors'])?count($errorArray['atperrors']):0);
       $commetErrorsCount                   = (isset($errorArray['replanishmenterrors'])?count($errorArray['replanishmenterrors']):0);
       $reasonErrorsCount                   = (isset($errorArray['replanishment_uom_errors'])?count($errorArray['replanishment_uom_errors']):0);
       $reason_mismatch_Count               = (isset($errorArray['reason_mismatch_errors'])?count($errorArray['reason_mismatch_errors']):0);
       $elpespnotfoundCount                      = (isset($errorArray['elpespnotFound'])?count($errorArray['elpespnotFound']):0);
       $mainArr['elpesp_count']              =   $elpespnotfoundCount;
       // $ditErrorsCount                      = (isset($errorArray['diterrors'])?count($errorArray['diterrors']):0);
       // $dndErrorsCount                      = (isset($errorArray['dnderrors'])?count($errorArray['dnderrors']):0);
       $mainArr['error_count']              = $wrong_combination + $product_errors + $commetErrorsCount+$reasonErrorsCount + $reason_mismatch_Count ;

       $log_array                           = $mainArr;
       
       $mainArr['reference'] = $current_timeStamp;
       
       UserActivity::excelUploadFileLogs("INVENTORY Replanishment", $current_timeStamp, $URL, $log_array);
       
       return $mainArr;
    
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getReplanishmentValue()
    {
        $sql = DB::table("master_lookup")
                    ->where("mas_cat_id", "=", 129)
                    ->pluck("value", "master_lookup_name")->all();
        return $sql;
    }

    public function getProductsDetails($product_id)
    {
      $sql = $this->leftJoin("warehouse_config",function($join){
                                $join->on("vw_inventory_report.le_wh_id","=", "warehouse_config.le_wh_id")
                                ->on("vw_inventory_report.product_id", "=", "warehouse_config.pref_prod_id");
                                })->where("vw_inventory_report.product_id", "=", $product_id)
                ->groupBy("dcname")
                ->get(array("dcname", "primary_image", "product_title", "sku", "kvi", "mrp", "soh", "atp", 'map', 'order_qty', 'available_inventory', 'upc', 'ptrvalue', 'cp', 'vw_inventory_report.le_wh_id', 'product_id', 'category_name', 'sub_category_name', 'manufacturer_name', 'freebee_sku', 'frebee_desc', 'cfc_qty', 'is_Sellable', 'pack_type', 'reserved_qty', 'dit_qty', 'dnd_qty', DB::raw("group_concat(wh_location) as bin_location")))->all();
                $results['results'] = $sql;//json_decode($sql, true);

                return $results;
    }



    public function getAllProductsByWareHouseForExcel($warehouseId, $filterBy = '', $productId) {
        // echo "<pre>";print_r($filterBy);die;
        $this->_roleRepo = new RoleRepo();
        $warehousename = $this->wareHouseNameById($warehouseId);
        $result = array();
        $sql = $this->where('vw_inventory_report.le_wh_id', '=', $warehouseId);

        

        if($productId != 0)
        {
            $sql = $sql->where('product_id', '=', $productId);
        }

        if(!empty($filterBy))
        {
            if (!empty($filterBy['manf_name'])) {
                $sql = $sql->whereIn('manufacturer_id', $filterBy['manf_name']);
            }

            if (isset($filterBy['sellable'])) {
                $sql = $sql->where('is_sellable', $filterBy['sellable']);
            }

            if (isset($filterBy['cpEnabled'])) {
                $sql = $sql->where('cp_enabled', $filterBy['cpEnabled']);
            }

            if (!empty($filterBy['brand'])) {
            $sql = $sql->whereIn('brand_id', $filterBy['brand']);
            }

            if (!empty($filterBy['category'])) {
                $sql = $sql->whereIn('product_class_id', $filterBy['category']);
            }

            if (!empty($filterBy['kvi'])) {
                $sql = $sql->whereIn('kvi', $filterBy['kvi']);
            }

            if (!empty($filterBy['ean_upc'])) {
                $sql = $sql->whereIn('upc', $filterBy['ean_upc']);
            }

            if (!empty($filterBy['product_titles'])) {
                $sql = $sql->whereIn('product_id', $filterBy['product_titles']);
            }

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

            if (isset($filterBy['mrp_max']) && ((int)$filterBy['mrp_max'] == (int)$filterBy['mrp_min'])) {
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
            }else if (isset($filterBy['inv_max']) && ((int)$filterBy['inv_max'] != (int)$filterBy['inv_min'])) {
                $sql = $sql->whereBetween('available_inventory', [(int) $filterBy['inv_min'], (int) $filterBy['inv_max']]);
            }

            if (!empty($filterBy['sku'])) {
                $sql = $sql->whereIn('sku', $filterBy['sku']);
            }
    }


        $result['results']      = $sql->get(array('inv_id', 'primary_image', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'ptrvalue', 'cp', 'vw_inventory_report.le_wh_id', 'vw_inventory_report.product_id', 'category_name', 'sub_category_name', 'manufacturer_name', 'freebee_sku', 'frebee_desc', 'cfc_qty', 'is_Sellable', 'pack_type', 'reserved_qty', 'cp_enabled', 'esu', 'dit_qty', 'dnd_qty', 'quarantine_qty', 'esp', 'elp', 'state_id', 'ptrvalue', 'product_group_id', 'star'
            
        ))->all();
        
        $resultArr              = json_decode(json_encode($result['results']), true);
        $inventoryEditAccess    = $this->_roleRepo->checkPermissionByFeatureCode('INV1002');
        
        foreach($resultArr as $key=>$data){
            $resultArr[$key]['bin_location'] = $this->getBinNames($data['le_wh_id'], $data['product_id']);
            $po_details = $this->getPODetails($data['product_id']);
           if($filterBy['freebeedata'] != 'YESF001') {
                //Filter out SKU's which falls under below conditions
                if($data['is_sellable']==0){
             //            if($data['is_sellable']==0 && !isset($po_details['po_code']) && $data['soh'] == 0){
                    unset($resultArr[$key]); continue;
                }
            }

            $resultArr[$key]['po_code']         = (isset($po_details['po_code']) ? $po_details['po_code'] : '');
            $resultArr[$key]['po_date']         = (isset($po_details['po_date']) ? $po_details['po_date'] : '');
            $resultArr[$key]['po_qty']          = (isset($po_details['qty']) ? $po_details['qty'] : '');
            $resultArr[$key]['po_packType']     = (isset($po_details['packType']) ? $po_details['packType'] : '');
            $tax_Details                        = json_decode(json_encode($this->getVatPercentage($data['product_id'], $data['state_id'])), true);

            $resultArr[$key]['vatpercentage']   = isset($tax_Details[0]['tax_percentage'])?$tax_Details[0]['tax_percentage']:"";
            $resultArr[$key]['hsn_code']   = isset($tax_Details[0]['hsn_code'])?$tax_Details[0]['hsn_code']:"";
            $resultArr[$key]['re_pending_qty']   = $this->pendingReturns($data["product_id"], $warehouseId);
           if($data['cfc_qty'] == 0)
            {
                $resultArr[$key]['available_cfc_qty'] = 0;
            }
            else
            {
                $resultArr[$key]['available_cfc_qty'] = ($data['available_inventory']/$data['cfc_qty']);
            }
            //Changed as per Satish || Date : Oct 20 2016
            // New calculation should be '$data['available_inventory']/$data['cfc_qty']'
            //$resultArr[$key]['available_cfc_qty'] = ($data['soh']/$data['cfc_qty']);
            
            if($inventoryEditAccess == 1)
            {
                $resultArr[$key]['actions'] = '<a data-type="edit" data-ditqty="'.$data['dit_qty'].'" data-dndqty = "'.$data['dnd_qty'].'" data-dcname="'. $warehousename .'" data-skuid="'. $data['sku'] .'" data-producttitle="'. $data['product_title'] .'" data-warehouseid="' . $warehouseId . '" data-prodid="' . $data['product_id'] . '" data-soh="'. $data['soh'] .'" data-atp = "'. $data['atp'] .'" data-toggle="modal" data-target="#edit-products"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            }
            else
            {
                $resultArr[$key]['actions'] = '';
            }
            
        }

        if(isset($resultArr)){
            $result['results'] = $resultArr;
        } else {
            $result['results'] = '';
        }
        return $result;
    }


    public function getAllProductsByOnlyWareHouseIdForExcel($warehouseId) {

        $rolesObj       = new Role();
        //$productIDs     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        //$prod_ids       = array_keys($productIDs['products']);
        $sql            = $this->where('vw_inventory_report.le_wh_id', '=', $warehouseId);
        //$sql            = $sql->whereIn('product_id', $prod_ids);
        $result         = $sql->get(array('primary_image', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'product_id','category_name', 'sub_category_name', 'manufacturer_name', 'freebee_sku', 'frebee_desc', 'cfc_qty', 'is_Sellable', 'pack_type', 'reserved_qty', 'cp_enabled', 'esu', 'dit_qty', 'dnd_qty', 'quarantine_qty', 'esp', 'elp', 'state_id', 'ptrvalue', 'product_group_id', 'star'))->all();
        $result         = json_decode(json_encode($result), true);
        
        foreach($result as $key=>$data){
            $po_details = $this->getPODetails($data['product_id']);
            $result[$key]['bin_location'] = $this->getBinNames($warehouseId, $data['product_id']);
            //Filter out SKU's which falls under below conditions
            if($data['is_sellable']==0){
//            if($data['is_sellable']==0 && !isset($po_details['po_code']) && $data['soh'] == 0){
                unset($result[$key]); continue;
            }

            $result[$key]['po_code'] = (isset($po_details['po_code']) ? $po_details['po_code'] : '');
            $result[$key]['po_date'] = (isset($po_details['po_date']) ? $po_details['po_date'] : '');
            $result[$key]['po_qty'] = (isset($po_details['qty']) ? $po_details['qty'] : '');
            $result[$key]['po_packType'] = (isset($po_details['packType']) ? $po_details['packType'] : '');
            $tax_Details                        = json_decode(json_encode($this->getVatPercentage($data['product_id'], $data['state_id'])), true);

            $result[$key]['vatpercentage']   = isset($tax_Details[0]['tax_percentage'])?$tax_Details[0]['tax_percentage']:"";
            $result[$key]['hsn_code']   = isset($tax_Details[0]['hsn_code'])?$tax_Details[0]['hsn_code']:"";
            $result[$key]['re_pending_qty'] = $this->pendingReturns($data["product_id"], $warehouseId);
            if($data['cfc_qty'] == 0)
            {
                $result[$key]['available_cfc_qty'] = 0;
            }
            else
            {
                $result[$key]['available_cfc_qty'] = ($data['available_inventory']/$data['cfc_qty']);
            }
            //Changed as per Satish || Date : Oct 20 2016
            // New calculation should be '$data['available_inventory']/$data['cfc_qty']'
            //$resultArr[$key]['available_cfc_qty'] = ($data['soh']/$data['cfc_qty']);
            
        }
        // array_unique($result);
        return $result;
    }

    public function getAllProductsByOnlyWareHouseIdForExcelReplanishment($warehouseId) {

        $rolesObj       = new Role();
        //$productIDs     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);
        
        //$prod_ids       = array_keys($productIDs['products']);
        $sql            = $this->where('vw_inventory_report.le_wh_id', '=', $warehouseId);
        //$sql            = $sql->whereIn('product_id', $prod_ids);
        $result         = $sql->get(array('product_id', 'primary_image', 'product_title', 'sku', 'is_Sellable', 'min-pickface-replenishment', 'replenishment_UOM'))->all();
        $result         = json_decode(json_encode($result), true);
        // echo "<pre>";print_r($result);die;
        foreach ($result as $key => $value) {
            $replanishmentcodes = $this->getReplanishmentCodes();
            $result[$key]['replanishmentname'] = isset($replanishmentcodes[$value['replenishment_UOM']])?$replanishmentcodes[$value['replenishment_UOM']]:"";
        }
        return $result;
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
        ->get(array("tax_classes.tax_percentage", "hsn_code"))->all();
        $jsonvals = json_decode(json_encode($sql), true);
        
        // if($jsonvals)
        // {
        //     $returnValue = $jsonvals[0]['tax_percentage'];
        // }
        return $jsonvals;
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
                ->get(array("po_code", "po.po_date", "po_products.qty", DB::raw("getMastLookupValue(uom) as packType")))->all(); 

        $returnval = $sql;//json_decode($sql, true);
        return (isset($returnval[0])?$returnval[0]:"");
    }

    public function getOldSOHAndATPValues($productId, $warehouseID)
    {
        $sql = DB::table("inventory")
                ->where("product_id", "=", $productId)
                ->where("le_wh_id", "=", $warehouseID)
                ->get(array("soh", "atp", "dit_qty", "dnd_qty"))->all();

        $result = json_decode(json_encode($sql), true);
        if(isset($result)){
            $result = reset($result);
        }
        return $result;
    }

    
    /*iNVENTORY approvalworkflow usage*/
    public function getInventoryReasonCodes()
    {
        $sql  = DB::table("master_lookup")->where("mas_cat_id", "=", 117)->where('is_active','=',1)->pluck("master_lookup_name","value")->all();
        return $sql;
    }

    public function getReplanishmentCodes()
    {
        $sql  = DB::table("master_lookup")->where("mas_cat_id", "=", 129)->pluck("master_lookup_name","value")->all();
        return $sql;
    }

    public function getProductIdBasedonTrackingId($trackingId)
    {
        $sql = DB::table("inventory_tracking")->where("inv_track_id", "=", $trackingId)->pluck("product_id")->all();
        return $sql['0'];
    }

    public function getInventoryDetails($trackingID)
    {
        $check_web_or_excel = $this->getActivityTypeFromTrackingTable($trackingID);
        $fecthvals = array("old_soh", "new_soh", "old_atp", "new_atp", "remarks", "approval_status");
        $sql = DB::table("inventory_tracking");   
        if($check_web_or_excel != 0)
        {
            $fecthvals = array("old_soh", "new_soh", "old_atp", "new_atp", "remarks", "master_lookup_name", "approval_status");
            $sql = $sql->join("master_lookup", "inventory_tracking.activity_type", "=", "master_lookup.value")
                        ->where("master_lookup.mas_cat_id", "=", "117");
        }

        $sql = $sql->where("inv_track_id", "=", $trackingID)->get($fecthvals)->all();
        $details = json_decode(json_encode($sql), true);
        return $details[0];
    }

    public function getActivityTypeFromTrackingTable($trackingID)
    {
       $sql = DB::table("inventory_tracking")->where("inv_track_id", "=", $trackingID)->pluck("activity_type")->all();
       return  $sql[0];
    }

    public function updateTrackingTableWithStatus($tableUpdateID, $tracingID)
    {
        $userId  = \Session::get('userId');
        $getoldStatus = $this->getOldStatusFromTracking($tracingID);
        $uniquevalues = array("product_id" => $getoldStatus['product_id']);
        $oldvaluesArray = array("old_status"=> $getoldStatus['approval_status']);

        $sql = DB::table("inventory_tracking")
                ->where("inv_track_id", "=", $tracingID)
                ->update(['approved_by'=> $userId, "approval_status" => $tableUpdateID]);



        $DBentries = array("new_status"=>$tableUpdateID);    
            
        UserActivity::userActivityLog("Inventory", $DBentries, "Changing the workflow status from".$getoldStatus['approval_status']." to".$tableUpdateID , $oldvaluesArray, $uniquevalues);

                

    }

    public function updateTrackingTableWithStatusforBulk($tableUpdateID, $tracingID)
    {
    try{
//        log::info("im in update tracking table with statu id..".$tableUpdateID);
        date_default_timezone_set("Asia/Kolkata");
        $userId  = \Session::get('userId');
        $getoldStatus = $this->getOldStatusFromTrackingforBulk($tracingID);
        foreach ($getoldStatus as $value) {
            $uniquevalues = array("product_id" => $value['product_id']);
            $oldvaluesArray = array("old_status"=> $value['approval_status']);



            $DBentries = array("new_status"=>$tableUpdateID);    
                
            UserActivity::userActivityLog("Inventory", $DBentries, "Changing the workflow status from".$value['approval_status']." to".$tableUpdateID , $oldvaluesArray, $uniquevalues);
        }
        $sql = DB::table("inventory_tracking")
                ->where("bulk_upload_id", "=", $tracingID)
                ->update(['approved_by'=> $userId, "approval_status" => $tableUpdateID]);

        $sql_bulk  = DB::table("inventory_bulk_upload")
                        ->where("bulk_upload_id", "=", $tracingID)
                        ->update(["approved_by" => $userId, "approval_status" => $tableUpdateID, "approved_at" => date('Y-m-d H:i:s')]);
    }catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    }
                

    }

    public function getOldStatusFromTracking($tracingID)
    {
        $sql = DB::table("inventory_tracking")->where("inv_track_id", "=", $tracingID)->get(array("approval_status", "product_id"))->all();
        $data = json_decode(json_encode($sql), true);
        return $data[0];
    }

    public function getOldStatusFromTrackingforBulk($bulkuploadId)
    {
        $sql = DB::table("inventory_tracking")->where("bulk_upload_id", "=", $bulkuploadId)->get(array("approval_status", "product_id"))->all();
        $data = json_decode(json_encode($sql), true);
        return $data;
    }

    public function updateInventoryTable($trackId)
    {
        date_default_timezone_set("Asia/Kolkata");
        $batchModel = new \App\Modules\Orders\Models\batchModel();
        $batch_inventory_update = "";
        $batch_history_array = array();
        $get_Soh_atp_vals   = $this->getTrackingDetails($trackId);
        $product_ID         = $get_Soh_atp_vals['product_id'];
        $wareHouse_id       = $get_Soh_atp_vals['le_wh_id'];
        $getinventory_table_values = $this->getInventoryDetailsBasedOnProductId($product_ID, $wareHouse_id);

        $inventory_table_soh = $getinventory_table_values[0]['soh'];
        $inventory_table_dit_qty = $getinventory_table_values[0]['dit_qty'];
        $inventory_table_dnd_qty = $getinventory_table_values[0]['dnd_qty'];
        $inventory_table_quarantine = $getinventory_table_values[0]['quarantine_qty'];
        $inventory_table_Order_qty = $getinventory_table_values[0]['order_qty'];


        /*$sohval             = $get_Soh_atp_vals['new_soh'];
        $soh_diff           = $get_Soh_atp_vals['stock_diff'];*/
        $dit_Diff           = $get_Soh_atp_vals['dit_diff'];
        $dnd_Diff           = $get_Soh_atp_vals['dnd_diff'];
        $excess             = $get_Soh_atp_vals['excess'];

        $atpvalue           = $get_Soh_atp_vals['new_atp'];
        $old_sohvalue       = $get_Soh_atp_vals['old_soh'];
        $old_atpvalue       = $get_Soh_atp_vals['old_atp'];
        $product_id         = $get_Soh_atp_vals['product_id'];
        $tracking_quarantine_qty = $get_Soh_atp_vals['quarantine_qty'];

        $resulted_dit_qty   = $inventory_table_dit_qty + $dit_Diff;
        $resulted_dnd_qty   = $inventory_table_dnd_qty + $dnd_Diff;
        $resulted_dit_dnd   = $inventory_table_dit_qty+$inventory_table_dnd_qty;
        $resulted_quarantine_qty = $inventory_table_quarantine - $tracking_quarantine_qty;
        $resulted_soh = $inventory_table_soh -($dnd_Diff+$dit_Diff)+$excess;
        $mytime = Carbon::now();
        $update_array = array("soh" => $resulted_soh, "quarantine_qty" => $resulted_quarantine_qty, "dit_qty" => $resulted_dit_qty, "dnd_qty" => $resulted_dnd_qty);

        //update approved at in inventory tracking
        $sql_inv_tracking = DB::table("inventory_tracking")->where("inv_track_id", $trackId)->update(array("approved_at" => date('Y-m-d H:i:s')));
        
        //Updating inventory table
        $update_inventory        = DB::table("inventory")
                                    ->where("product_id", "=", $product_id)
                                    ->where("le_wh_id", "=", $wareHouse_id)
                                    ->update($update_array);
        $sum_dit_dnd_excess  = $excess - ($dit_Diff+$dnd_Diff);
        $deduct_quarantine   = -($dit_Diff+$dnd_Diff);

        $inventory_update_logs_arr = array('le_wh_id'=>$wareHouse_id,
                                        'old_soh'=>$inventory_table_soh,
                                        'old_order_qty'=>$inventory_table_Order_qty,
                                        'old_quarantine_qty'=>$inventory_table_quarantine,
                                        'old_dnd_qty'=>$inventory_table_dnd_qty,
                                        'old_dit_qty'=>$inventory_table_dit_qty,
                                        'product_id'=>$product_id,
                                        // 'soh'=>$resulted_soh,
                                        'soh' => $sum_dit_dnd_excess,
                                        'order_qty'=>0,
                                        // 'ref'=>'Inventory Module',
                                        'ref_type'=>7,
                                        // 'quarantine_qty'=>$resulted_quarantine_qty,
                                        // 'dit_qty'=>$resulted_dit_qty,
                                        // 'dnd_qty'=>$resulted_dnd_qty,
                                        'quarantine_qty'=>$deduct_quarantine,
                                        'dit_qty'=>$dit_Diff,
                                        'dnd_qty'=>$dnd_Diff,
                                        'comments'=>$get_Soh_atp_vals['remarks']);

        // adding data to batches,considering recent batch id as ref
        // $batchData = DB::table("inventory_batch")
        //                     ->where("product_id",$product_id)
        //                     ->where("le_wh_id",$wareHouse_id)
        //                     // ->where("qty",">",0)
        //                     ->whereRaw("qty + ($excess) >= $excess")
        //                     ->orderBy("inward_id","ASC")
        //                     ->first();
        // if(count($batchData) > 0){
        //     $batch_inventory_update .= "UPDATE inventory_batch SET qty= qty + ($excess) where product_id = $product_id and le_wh_id=$wareHouse_id and inward_id=$batchData->inward_id;";
        //     $batch_history_array[] = array("inward_id"=>$batchData->inward_id,
        //                                 "le_wh_id"=>$wareHouse_id,
        //                                 "product_id"=>$product_id,
        //                                 "qty"=>$excess,
        //                                 "old_qty"=>$batchData->qty,
        //                                 'ref'=>$trackId,
        //                                 'ref_type'=>7,
        //                                 'dit_qty'=>$dit_Diff,
        //                                 'old_dit_qty'=>$batchData->dit_qty,
        //                                 'dnd_qty'=>$dnd_Diff,
        //                                 'old_dnd_qty'=>$batchData->dnd_qty,
        //                                 'comments'=>"Qty Updated by Inventory Tracking ID:$trackId");
        // }
        // //updating batch data
        // if(isset($batch_history_array) && count($batch_history_array)) {
        //     $batchModel->insertBatchHistory($batch_history_array);
        // }
        // if(isset($batch_inventory_update) && $batch_inventory_update != ""){
        //     DB::unprepared($batch_inventory_update);
        // }

            $updating_inventory_update_logs = $this->addInQueueWithBulk($inventory_update_logs_arr);   //inventory update logs into inv_update_log

        $uniquevalues = array("product_id" => $product_id);                    
        $oldvaluesArray = array("SOH" => $inventory_table_soh,"DIT_QTY" => $inventory_table_dit_qty, "DND_QTY" => $inventory_table_dnd_qty );
        $DBentries = array("SOH" => $resulted_soh, "DIT_QTY"=>$resulted_dit_qty, "DND_QTY" => $resulted_dnd_qty, "warehouse_id" => $wareHouse_id, "Product_id" => $product_id);
        UserActivity::userActivityLog("Inventory", $DBentries, "SOH, DIT_QTY and D&D_QTY values has been updated in Inventory Table", $oldvaluesArray, $uniquevalues);
    }

    public function revertInventoryTable($trackId)
    {
      try {
        date_default_timezone_set("Asia/Kolkata");

        $get_Soh_atp_vals   = $this->getTrackingDetails($trackId);
        $product_ID         = $get_Soh_atp_vals['product_id'];
        $wareHouse_id       = $get_Soh_atp_vals['le_wh_id'];
        $getinventory_table_values = $this->getInventoryDetailsBasedOnProductId($product_ID, $wareHouse_id);

        $inventory_table_quarantine = $getinventory_table_values[0]['quarantine_qty'];
        
        $tracking_quarantine_qty = $get_Soh_atp_vals['quarantine_qty'];

        $resulted_quarantine_qty = $inventory_table_quarantine - $tracking_quarantine_qty;
        $sql_inv_tracking = DB::table("inventory_tracking")->where("inv_track_id", $trackId)->update(array("approved_at" => date('Y-m-d H:i:s')));
        //Updating inventory table
        $update_inventory                   = DB::table("inventory")
                                                ->where("product_id", "=", $product_ID)
                                                ->where("le_wh_id", "=", $wareHouse_id)
                                                ->update(array("quarantine_qty" => $resulted_quarantine_qty));
    } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        } 
    }


    public function updateInventoryTableforBulk($bulkuploadId)
    {
        try{
            date_default_timezone_set("Asia/Kolkata");
            $get_Soh_atp_vals   = $this->getBulkUploadDetails($bulkuploadId);
            $invnegativearray = array();
            $batchModel = new \App\Modules\Orders\Models\batchModel();
            $batch_inventory_update = "";
            $batch_history_array = array();
            $orderCtrl =  new OrdersController();
            foreach ($get_Soh_atp_vals as $value) {
                $product_ID         = $value['product_id'];
                $wareHouse_id       = $value['le_wh_id'];
                $comments           = $value['remarks'];
                $getinventory_table_values = $this->getInventoryDetailsBasedOnProductId($product_ID, $wareHouse_id);

                $inventory_table_soh        = $getinventory_table_values[0]['soh'];
                $inventory_table_dit_qty    = $getinventory_table_values[0]['dit_qty'];
                $inventory_table_dnd_qty    = $getinventory_table_values[0]['dnd_qty'];
                $inventory_table_quarantine = $getinventory_table_values[0]['quarantine_qty'];
                $inventory_table_Order_qty  = $getinventory_table_values[0]['order_qty'];

                // $sohval             = $value['new_soh'];
                // $soh_diff           = $value['stock_diff'];
                $dit_Diff           = $value['dit_diff'];
                $dnd_Diff           = $value['dnd_diff'];
                $excess             = $value['excess'];
                $atpvalue           = $value['new_atp'];
                $old_sohvalue       = $value['old_soh'];
                $old_atpvalue       = $value['old_atp'];
                $product_id         = $value['product_id'];

                $resulted_SOH       = $inventory_table_soh - $value['quarantine_qty'] +$excess;
                $resulted_dit_qty   = $inventory_table_dit_qty + $dit_Diff;

                $resulted_dnd_qty   = $inventory_table_dnd_qty + $dnd_Diff - $excess;
                
                $resulted_quaratine_qty = $getinventory_table_values[0]['quarantine_qty'] - $value['quarantine_qty'];
                $inventory_reason = $value['remarks'];
               
                if($resulted_SOH < 0)
                {

//                    Log::info($value['product_id']."____negative in inventory bulk uploda----soh----___".$resulted_SOH.'_____'.$resulted_dit_qty.'___'.$resulted_dnd_qty.'___'.$resulted_quaratine_qty);
                    $invnegativearray[] = array("product_id"=>$value['product_id'],"","product_title"=>$value['product_title'],"inv_soh"=>$inventory_table_soh,"cur_soh"=>$resulted_SOH,"dit_qty"=>$resulted_dit_qty,"dnd_qty"=>$resulted_dnd_qty,"quarantine_qty"=>$resulted_quaratine_qty);
                    continue;
                }
                if($resulted_dit_qty < 0)
                {

  //                  Log::info($value['product_id']."___negative in inventory bulk uploda---dit-----___".$resulted_SOH.'_____'.$resulted_dit_qty.'___'.$resulted_dnd_qty.'___'.$resulted_quaratine_qty);
                    $invnegativearray[] = array("product_id"=>$value['product_id'],"","product_title"=>$value['product_title'],"inv_soh"=>$inventory_table_soh,"cur_soh"=>$resulted_SOH,"dit_qty"=>$resulted_dit_qty,"dnd_qty"=>$resulted_dnd_qty,"quarantine_qty"=>$resulted_quaratine_qty);
                    continue;
                }
                if($resulted_dnd_qty < 0)
                {
                    
                   // Log::info($value['product_id']."__negative in inventory bulk uploda----dnd----___".$resulted_SOH.'_____'.$resulted_dit_qty.'___'.$resulted_dnd_qty.'___'.$resulted_quaratine_qty);
                    $invnegativearray[] = array("product_id"=>$value['product_id'],"","product_title"=>$value['product_title'],"inv_soh"=>$inventory_table_soh,"cur_soh"=>$resulted_SOH,"dit_qty"=>$resulted_dit_qty,"dnd_qty"=>$resulted_dnd_qty,"quarantine_qty"=>$resulted_quaratine_qty);
                    continue;
                }
                if($resulted_quaratine_qty < 0)
                {
                   // Log::info($value['product_id']."___negative in inventory bulk uploda-----qua---___".$resulted_SOH.'_____'.$resulted_dit_qty.'___'.$resulted_dnd_qty.'___'.$resulted_quaratine_qty);
                    $invnegativearray[] = array("product_id"=>$value['product_id'],"","product_title"=>$value['product_title'],"inv_soh"=>$inventory_table_soh,"cur_soh"=>$resulted_SOH,"dit_qty"=>$resulted_dit_qty,"dnd_qty"=>$resulted_dnd_qty,"quarantine_qty"=>$resulted_quaratine_qty);
                    continue;
                }
            }
           // Log::info("negative array___");
           // Log::info(print_r($invnegativearray, true));
            if(empty($invnegativearray))
            {
                foreach ($get_Soh_atp_vals as $value) {
                    $product_ID         = $value['product_id'];
                    $wareHouse_id       = $value['le_wh_id'];
                    $comments           = $value['remarks'];
                    $getinventory_table_values = $this->getInventoryDetailsBasedOnProductId($product_ID, $wareHouse_id);

                    $inventory_table_soh        = $getinventory_table_values[0]['soh'];
                    $inventory_table_dit_qty    = $getinventory_table_values[0]['dit_qty'];
                    $inventory_table_dnd_qty    = $getinventory_table_values[0]['dnd_qty'];
                    $inventory_table_quarantine = $getinventory_table_values[0]['quarantine_qty'];
                    $inventory_table_Order_qty  = $getinventory_table_values[0]['order_qty'];

                    // $sohval             = $value['new_soh'];
                    // $soh_diff           = $value['stock_diff'];
                    $dit_Diff           = $value['dit_diff'];
                    $dnd_Diff           = $value['dnd_diff'];
                    $excess             = $value['excess'];
                    $atpvalue           = $value['new_atp'];
                    $old_sohvalue       = $value['old_soh'];
                    $old_atpvalue       = $value['old_atp'];
                    $product_id         = $value['product_id'];

                    $resulted_SOH       = $inventory_table_soh - $value['quarantine_qty'] +$excess;
                    $resulted_dit_qty   = $inventory_table_dit_qty + $dit_Diff;

                    $resulted_dnd_qty   = $inventory_table_dnd_qty + $dnd_Diff - $excess;

                    $resulted_quaratine_qty = $getinventory_table_values[0]['quarantine_qty'] - $value['quarantine_qty'];

                    $inventory_reason = $value['remarks'];
                    
                    $sum_dit_dnd_excess  = $excess - ($dit_Diff+$dnd_Diff);
                    $deduct_quarantine   = -($dit_Diff+$dnd_Diff);


                    $mytime = Carbon::now();

                    $update_array = array("soh"         => $resulted_SOH, 
                                          // "atp"         => $atpvalue, 
                                          "dit_qty"     =>$resulted_dit_qty, 
                                          "dnd_qty"     => $resulted_dnd_qty,
                                          "quarantine_qty" => $resulted_quaratine_qty
                                          );
                   
                    //Update Inventory Tracking values
                    // $this->updateInventoryTrackingforBulk($bulkuploadId, $inventory_table_soh, $resulted_SOH, $inventory_table_dit_qty, $resulted_dit_qty, $inventory_table_dnd_qty, $resulted_dnd_qty, $product_ID, $wareHouse_id);

                   // Log::info("update invnetory table------------");
                    //update approved at in inventory tracking
                    $sql_inv_tracking = DB::table("inventory_tracking")->where("bulk_upload_id", $bulkuploadId)->update(array("approved_at" => $mytime->toDateTimeString()));
                    //Updating inventory table
                    $update_inventory                   = DB::table("inventory")
                                                            ->where("product_id", "=", $product_id)
                                                            ->where("le_wh_id", "=", $wareHouse_id)
                                                            ->update($update_array);
                                                                       
                    $inventory_update_logs_arr = array('le_wh_id'=>$wareHouse_id,
                                                'old_soh'=>$inventory_table_soh,
                                                'old_order_qty'=>$inventory_table_Order_qty,
                                                'old_quarantine_qty'=>$inventory_table_quarantine,
                                                'old_dnd_qty'=>$inventory_table_dnd_qty,
                                                'old_dit_qty'=>$inventory_table_dit_qty,
                                                'product_id'=>$product_id,
                                                // 'soh'=>$resulted_SOH,
                                                'soh' => $sum_dit_dnd_excess,
                                                'quarantine_qty'=>$deduct_quarantine,
                                                'dit_qty'=>$dit_Diff,
                                                'dnd_qty'=>$dnd_Diff,
                                                'order_qty'=>0,
                                                // 'ref'=>'Inventory Module',
                                                'ref_type'=>7,
                                                // 'quarantine_qty'=>$resulted_quaratine_qty,
                                                // 'dit_qty'=>$resulted_dit_qty,
                                                // 'dnd_qty'=>$resulted_dnd_qty,
                                                'comments'=>$comments);


                    $updating_inventory_update_logs = $this->addInQueueWithBulk($inventory_update_logs_arr); //inventory update logs into inv_update_logs

                    $uniquevalues = array("product_id" => $product_id);                    
                    $oldvaluesArray = array("SOH" => $inventory_table_soh,"DIT_QTY" => $inventory_table_dit_qty, "DND_QTY" => $inventory_table_dnd_qty );
                    $DBentries = array("SOH" => $resulted_SOH, "DIT_QTY"=>$resulted_dit_qty, "DND_QTY" => $resulted_dnd_qty, "warehouse_id" => $wareHouse_id, "Product_id" => $product_id);
                    UserActivity::userActivityLog("Inventory Bulk", $DBentries, "SOH, DIT_QTY and D&D_QTY values has been updated in Inventory Table", $oldvaluesArray, $uniquevalues);

                    // adding data to batches,considering recent batch id as ref

                    $batchData = array();
                    $batch_inventory_update = "";
                    // batches for EXCESS
                    if($excess > 0){
                        $batchData_ = DB::table("inventory_batch")
                                        ->where("product_id",$product_id)
                                        ->where("le_wh_id",$wareHouse_id)
                                        // ->where("qty",">",0)
                                        ->whereRaw("qty + ($excess) > $excess")
                                        ->orderBy("inward_id","ASC")
                                        ->first();
                        $batchData[] = $batchData_;
                    }

                    foreach ($batchData as $key => $batch) {
                        //creating batch array
                        $batch_id = $batch->inward_id;
                        $invb_id = $batch->invb_id;
                        $elp = $batch->elp;
                        $req_qty = $excess;
                        $used_qty = $excess;

                        Log::info("used_qty".$used_qty);
                        Log::info("batch->qty".$batch->qty);
                        $batch_history_array[] = array("inward_id"=>$batch_id,
                                            "le_wh_id"=>$wareHouse_id,
                                            "product_id"=>$product_id,
                                            "qty"=>'+'.$used_qty,
                                            "old_qty"=>$batch->qty,
                                            'ref'=>$bulkuploadId,
                                            'ref_type'=>7,
                                            'dit_qty'=>0,
                                            'old_dit_qty'=>$batch->dit_qty,
                                            'dnd_qty'=>0,
                                            'old_dnd_qty'=>$batch->dnd_qty,
                                            'comments'=>"Qty Updated by Inventory Import ID:$bulkuploadId (Excess Qty)");

                        $positive_excess = $req_qty - $used_qty;
                        $batch_inventory_update .= "UPDATE inventory_batch SET qty=qty+$used_qty where invb_id = $invb_id;";
                    } 
                    Log::info("Excess Qty");
                    Log::info($batch_inventory_update);
                    if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                        DB::unprepared($batch_inventory_update);
                    }

                    $batchData = array();
                    $batch_inventory_update = "";
                    // batches for DND
                    $positive_dnd = abs($dnd_Diff);
                    $batchData = $orderCtrl->getBatchesByData($product_id,$wareHouse_id,$positive_dnd,0,10,[]);

                    foreach ($batchData as $key => $batch) {
                        //creating batch array
                        $batch_id = $batch->inward_id;
                        $invb_id = $batch->invb_id;
                        $elp = $batch->elp;
                        $req_qty = $positive_dnd;
                        if($req_qty > $batch->qty){
                            $used_qty = $batch->qty;
                        }else if($batch->qty >= $req_qty){
                            $used_qty = $req_qty;
                        }
                        if(count($batchData) == 1){
                            $batch_ord_qty = $positive_dnd;
                        }else{
                            $batch_ord_qty = $used_qty;
                        }
                        Log::info("used_qty".$used_qty);
                        Log::info("batch->qty".$batch->qty);
                        $batch_history_array[] = array("inward_id"=>$batch_id,
                                            "le_wh_id"=>$wareHouse_id,
                                            "product_id"=>$product_id,
                                            "qty"=>'-'.$used_qty,
                                            "old_qty"=>$batch->qty,
                                            'ref'=>$bulkuploadId,
                                            'ref_type'=>7,
                                            'dit_qty'=>0,
                                            'old_dit_qty'=>$batch->dit_qty,
                                            'dnd_qty'=>$used_qty,
                                            'old_dnd_qty'=>$batch->dnd_qty,
                                            'comments'=>"Qty Updated by Inventory Import ID:$bulkuploadId (DND Qty)");

                        $positive_dnd = $req_qty - $used_qty;
                        $batch_inventory_update .= "UPDATE inventory_batch SET qty=qty-$used_qty , dnd_qty= dnd_qty + $used_qty where invb_id = $invb_id;";
                    } 
                    Log::info("DND Qty");
                    Log::info($batch_inventory_update);
                    if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                        DB::unprepared($batch_inventory_update);
                    }
                    
                    $batchData = array();
                    $batch_inventory_update = "";
                    // batches for DND Again (Excess) removing excess from dnd,because when ever excess given ,it should be available in dnd
                    $positive_dnd = abs($excess);
                    $batchData = $orderCtrl->getBatchesByDataDND($product_id,$wareHouse_id,$positive_dnd,0,10,[]);
                    Log::info("DND Again (Excess)");
                    Log::info(json_encode($batchData));
                    foreach ($batchData as $key => $batch) {
                        //creating batch array
                        $batch_id = $batch->inward_id;
                        $invb_id = $batch->invb_id;
                        $elp = $batch->elp;
                        $req_qty = $positive_dnd;
                        $batch_qty = $batch->dnd_qty;
                        if($req_qty > $batch_qty){
                            $used_qty = $batch_qty;
                        }else if($batch_qty >= $req_qty){
                            $used_qty = $req_qty;
                        }
                        if(count($batchData) == 1){
                            $batch_ord_qty = $positive_dnd;
                        }else{
                            $batch_ord_qty = $used_qty;
                        }
                        Log::info("used_qty".$used_qty);
                        Log::info("batch->qty".$batch->qty);
                        $batch_history_array[] = array("inward_id"=>$batch_id,
                                            "le_wh_id"=>$wareHouse_id,
                                            "product_id"=>$product_id,
                                            "qty"=>0,
                                            "old_qty"=>$batch->qty,
                                            'ref'=>$bulkuploadId,
                                            'ref_type'=>7,
                                            'dit_qty'=>0,
                                            'old_dit_qty'=>$batch->dit_qty,
                                            'dnd_qty'=>"-".$used_qty,
                                            'old_dnd_qty'=>$batch->dnd_qty,
                                            'comments'=>"Qty Updated by Inventory Import ID:$bulkuploadId (DND Excess Qty)");

                        $positive_dnd = $req_qty - $used_qty;
                        $batch_inventory_update .= "UPDATE inventory_batch SET  dnd_qty= dnd_qty - $used_qty where invb_id = $invb_id;";
                    }
                    Log::info("DND Excess Qty");
                    Log::info($batch_inventory_update);
                    if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                        DB::unprepared($batch_inventory_update);
                    }

                    $batchData = array();
                    $batch_inventory_update = "";
                    // batches for DIT
                    $positive_dit = abs($dit_Diff);
                    $batchData = $orderCtrl->getBatchesByData($product_id,$wareHouse_id,$positive_dit,0,10,[]);
                    foreach ($batchData as $key => $batch) {
                        //creating batch array
                        $batch_id = $batch->inward_id;
                        $invb_id = $batch->invb_id;
                        $elp = $batch->elp;
                        $req_qty = $positive_dit;
                        if($req_qty > $batch->qty){
                            $used_qty = $batch->qty;
                        }else if($batch->qty >= $req_qty){
                            $used_qty = $req_qty;
                        }
                        if(count($batchData) == 1){
                            $batch_ord_qty = $positive_dit;
                        }else{
                            $batch_ord_qty = $used_qty;
                        }
                        Log::info("used_qty".$used_qty);
                        Log::info("batch->qty".$batch->qty);
                        $batch_history_array[] = array("inward_id"=>$batch_id,
                                            "le_wh_id"=>$wareHouse_id,
                                            "product_id"=>$product_id,
                                            "qty"=>'-'.$used_qty,
                                            "old_qty"=>$batch->qty,
                                            'ref'=>$bulkuploadId,
                                            'ref_type'=>7,
                                            'dit_qty'=>$used_qty,
                                            'old_dit_qty'=>$batch->dit_qty,
                                            'dnd_qty'=>0,
                                            'old_dnd_qty'=>$batch->dnd_qty,
                                            'comments'=>"Qty Updated by Inventory Import ID:$bulkuploadId (DIT Qty)");

                        $positive_dit = $req_qty - $used_qty;
                        $batch_inventory_update .= "UPDATE inventory_batch SET qty=qty-$used_qty , dit_qty= dit_qty + $used_qty where invb_id = $invb_id;";
                    }
                    Log::info("DIT Qty");
                    Log::info($batch_inventory_update);
                    if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                        DB::unprepared($batch_inventory_update);
                    }
                    

                    
                } 


                //updating batch data
                if(isset($batch_history_array) && count($batch_history_array)) {
                    $batchModel->insertBatchHistory($batch_history_array);
                }
                
            }
            else
            {
                
                UserActivity::userActivityLog("Inventory Bulk",$invnegativearray, "Negative values in invenotry bulk uploaded.");
                $notificationObj= new NotificationsModel();
                $usersObj = new Users(); 
                $userIdData= $notificationObj->getUsersByCode('INVRM001');
                $userIdData=json_decode(json_encode($userIdData),true); 
                $subject=$notificationObj->getMessageByCode('INVRM001'); 
               
                $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get()->all();
                 $emails=json_decode(json_encode($data,1),true); 
                $getEmails=array(); 
                foreach ($emails as $keyValue )
                {
                 $getEmails[]=$keyValue['email_id']; 
                }
                $subject=$subject.'('.date('d-M-Y').')';
                $body = array('template'=>'emails.InvRejectedTemplate', 'attachment'=>'',"tableData"=>$invnegativearray);

                // Mail::send('emails.InvRejectedTemplate',["tableData"=>  
                //     $invnegativearray], function ($message) use ($getEmails, $subject) {
                //             $message->to($getEmails);
                //             $message->subject($subject.'('.date('d-M-Y').')' );
                //         });
                Utility::sendEmail($getEmails, $subject, $body);
              //  Log::info("Inventory email function");
                //Log::info(print_r($getEmails,true));
                
                return $invnegativearray;
            }

            
        }catch(\ErrorException $ex)
        {
            //Log::info("Inventory final approval ");
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return array("Invenoty final level approval getting error.");
        }
    }

    public function revertInventoryTableforBulk($bulkuploadId)
    {
        date_default_timezone_set("Asia/Kolkata");
        $get_Soh_atp_vals   = $this->getBulkUploadDetails($bulkuploadId);
        foreach ($get_Soh_atp_vals as $value) {
            $product_ID                 = $value['product_id'];
            $wareHouse_id               = $value['le_wh_id'];
            $getinventory_table_values  = $this->getInventoryDetailsBasedOnProductId($product_ID, $wareHouse_id);

            $inventory_table_soh        = $getinventory_table_values[0]['soh'];
            $inventory_table_dit_qty    = $getinventory_table_values[0]['dit_qty'];
            $inventory_table_dnd_qty    = $getinventory_table_values[0]['dnd_qty'];
            
            $dit_Diff                   = $value['dit_diff'];
            $dnd_Diff                   = $value['dnd_diff'];

            $atpvalue                   = $value['new_atp'];
            $old_sohvalue               = $value['old_soh'];
            $old_atpvalue               = $value['old_atp'];
            $product_id                 = $value['product_id'];

            $resulted_quaratine_qty     = $getinventory_table_values[0]['quarantine_qty'] - $value['quarantine_qty'];

            $update_array = array(
                                  "quarantine_qty" => $resulted_quaratine_qty
                                  );
            $sql_inv_tracking = DB::table("inventory_tracking")->where("bulk_upload_id", $bulkuploadId)->update(array("approved_at" => date('Y-m-d H:i:s')));
            $update_inventory                   = DB::table("inventory")
                                                    ->where("product_id", "=", $product_id)
                                                    ->where("le_wh_id", "=", $wareHouse_id)
                                                    ->update($update_array);

            
        }
    
    }

    public function updateInventoryTrackingTableforOldValues($trackingId, $soh, $ditval, $dndval)
    {
        $update_array_list  = array("old_soh" => $soh,
                                   "old_dit_qty" => $ditval,
                                   "old_dnd_qty" => $dndval);
        $sql                = DB::table("inventory_tracking")
                                    ->where("inv_track_id", "=", $trackingId)
                                    ->update($update_array_list);
    }

    public function updateInventoryTrackingTableforNewValues($trackingId, $soh, $ditval, $dndval)
    {
        $update_array_list      = array("new_soh" => $soh,
                                   "new_dit_qty" => $ditval,
                                   "new_dnd_qty" => $dndval);
        $sql                    = DB::table("inventory_tracking")
                                    ->where("inv_track_id", "=", $trackingId)
                                    ->update($update_array_list);
    }

    public function updateInventoryTracking($trackingId, $soh, $newSoh, $oldDit, $newDit, $oldDnd, $newDnd)
    {
        $update_list            = array(
                                    "old_soh" => $soh,
                                    "new_soh" => $newSoh,
                                    "old_dit_qty" => $oldDit,
                                    "new_dit_qty" => $newDit,
                                    "old_dnd_qty" => $oldDnd,
                                    "new_dnd_qty" => $newDnd
                                    );
        $sql                    = DB::table("inventory_tracking")
                                    ->where("inv_track_id", "=", $trackingId)
                                    ->update($update_list);
    }

    public function updateInventoryTrackingforBulk($bulkuploadId, $soh, $newSoh, $oldDit, $newDit, $oldDnd, $newDnd, $productId, $warehouseId)
    {
        $update_list            = array(
                                    "old_soh" => $soh,
                                    "new_soh" => $newSoh,
                                    "old_dit_qty" => $oldDit,
                                    "new_dit_qty" => $newDit,
                                    "old_dnd_qty" => $oldDnd,
                                    "new_dnd_qty" => $newDnd
                                    );
        $sql                    = DB::table("inventory_tracking")
                                    ->where("bulk_upload_id", "=", $bulkuploadId)
                                    ->where("product_id", "=", $productId)
                                    ->where("le_wh_id", "=", $warehouseId)
                                    ->update($update_list);
    }
    /* bulk upload tracking table and Bulk upload table updating */
    public function updateBulkInventoryTracking($bulkuploadId, $oldsohValue, $newSohValue, $oldDit, $newDit, $oldDnd, $newDnd, $ProductId, $warehouseId)
    {
     $update_list            = array(
                                    "old_soh" => $oldsohValue,
                                    "new_soh" => $newSohValue,
                                    "old_dit_qty" => $oldDit,
                                    "new_dit_qty" => $newDit,
                                    "old_dnd_qty" => $oldDnd,
                                    "new_dnd_qty" => $newDnd
                                    );
        $sql                    = DB::table("inventory_tracking")
                                    ->where("bulk_upload_id", "=", $bulkuploadId)
                                    ->where("product_id", "=", $ProductId)
                                    ->where("le_wh_id", "=", $warehouseId)
                                    ->update($update_list);
    
    }

    public function getTrackingDetails($trackId)
    {
        $sql    = DB::table("inventory_tracking")
                    ->select('quarantine_qty', 'product_id', 'stock_diff','dit_diff', 'dnd_diff', 'new_atp', 'new_soh', 'product_id', 'old_soh', 'old_atp', 'le_wh_id', 'old_dit_qty', 'new_dit_qty', 'old_dnd_qty', 'new_dnd_qty', "remarks", "approval_status", "master_lookup_name", DB::raw('GetUserName(inventory_tracking.created_by, 2) as user'), DB::raw('getLeWhName(inventory_tracking.le_wh_id) as warehouse'), "inventory_tracking.created_at", "excess")
                    ->leftJoin("master_lookup", "inventory_tracking.activity_type", "=", "master_lookup.value")
                    // ->where("master_lookup.mas_cat_id", "=", "117")
                    ->where("inventory_tracking.inv_track_id", "=", $trackId)
                    ->get()->all();
        $data   = json_decode(json_encode($sql), true);
        return $data[0];
    }

    public function getInventoryDetailsBasedOnProductId($productId, $leWhId)
    {
        /*$getvalues      = array("soh", "dit_qty", "dnd_qty", "quarantine_qty", "order_qty");
        $sql            = DB::table("inventory")
                        ->where("product_id", "=", $productId)
                        ->where("le_wh_id", "=", $leWhId)
                        ->lockForUpdate()
                        ->get($getvalues);*/
        $query ="select soh,dit_qty,dnd_qty,quarantine_qty, order_qty from inventory where product_id =".$productId." and le_wh_id=".$leWhId;
        $sql =  DB::selectFromWriteConnection($query);
        return json_decode(json_encode($sql), true);
    }

    public function getReasonCodeBasedOnReasonType($reason_type)
    {
        $sql = DB::table("master_lookup")
                ->where("mas_cat_id", "=", '117')
                ->where("master_lookup_name","=", $reason_type)
                ->pluck("value")->all();
        if(empty($sql))
        {
            $sql = "0";
        }
        return $sql;
    }

    public function getReplanishmentType($replanishmentType)
    {
        $sql = DB::table("master_lookup")
                ->where("mas_cat_id", "=", '129')
                ->where("master_lookup_name","=", $replanishmentType)
                ->pluck("value")->all();
        if(empty($sql))
        {
            $sql = "0";
        }
        return $sql;
    }

    public function getBulkUploadDetails($bulkId)
    {
               $sql    = DB::table("inventory_tracking")
                    ->select('inventory_tracking.product_id','products.product_title','mrp','esu','dit_diff', 'dnd_diff', 'new_atp', 'new_soh', 'inventory_tracking.product_id', 'old_soh', 'old_atp', 'le_wh_id', 'old_dit_qty', 'new_dit_qty', 'old_dnd_qty', 'new_dnd_qty', "remarks", "approval_status", "master_lookup_name", DB::raw('GetUserName(inventory_tracking.created_by, 2) as user'), DB::raw('getLeWhName(inventory_tracking.le_wh_id) as warehouse'),DB::raw('getProductElp_wh(inventory_tracking.product_id,inventory_tracking.le_wh_id) as elp'), "inventory_tracking.created_at", "quarantine_qty", "excess", "remarks","products.sku")
                    ->join("master_lookup", "inventory_tracking.activity_type", "=", "master_lookup.value")
                    ->join('products','products.product_id','=','inventory_tracking.product_id')
                    ->where("master_lookup.mas_cat_id", "=", "117")
                    ->where("inventory_tracking.bulk_upload_id", "=", $bulkId)
                    ->get()->all();
        // $sql = DB::table("inventory_tracking")->where("bulk_upload_id", "=", $bulkId)->get();
        $data = json_decode(json_encode($sql), true);
        return $data;
    }

    public function getBinNames($warehouse_id, $prod_id)
    {
        $returnVal = "";
        $sql1 = DB::select(DB::raw("select wh_location from warehouse_config where pref_prod_id = '".$prod_id."' and le_wh_id = '".$warehouse_id."'"));
        $data = json_decode(json_encode($sql1), true);
        
        if(!empty($data) && sizeof($data) == 1)
        {   
            $returnVal = $data[0]['wh_location'];
        }else
        {
            foreach ($data as $value) {
                $returnVal .= $value['wh_location'].",";
            }
        }
        $returnVal = rtrim($returnVal, ",");
        return $returnVal;
    }

    public function getAllCurrentInventory($allprod_ids, $warehouseId)
    {
        $sql = DB::table("inventory")->whereIn("product_id", $allprod_ids)->where("le_wh_id", "=", $warehouseId)->get(array("dit_qty", "dnd_qty", "product_id","soh"))->all();
        $data = json_decode(json_encode($sql), true);
        return $data;
    }

    public function getSOH($product_id, $wh_id)
    {
        try {
                $sql = DB::table("inventory")->where("product_id", $product_id)->where("le_wh_id", $wh_id)->get(array("soh","dit_qty","dnd_qty"))->all();
                $data = json_decode(json_encode($sql), true);
                return $data;
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function getOrderdQty($pid, $wh_id)
    {
        try {
                $statusArr = array("17001, 17005, 17020");
                // $sql1 = DB::select(DB::raw(" select sum(qty) as qty from gds_orders  as GO join gds_order_products as GOP on GOP.gds_order_id = GO.gds_order_id where GO.order_status_id in (17001, 17005, 17020) and GO.le_wh_id = ".$wh_id." and GOP.product_id = ".$pid."  "));
                // $data = json_decode(json_encode($sql1), true);
                // return isset($data[0]['qty'])?$data[0]['qty']:0;
                $sql  = DB::table("inventory")
                        ->where("product_id", $pid)
                        ->where("le_wh_id", $wh_id)
                        ->get(array("order_qty"))->all();
                $data = json_decode(json_encode($sql), true);
                return isset($data[0]['order_qty'])?$data[0]['order_qty']:0;

        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
        
    }

    public function getOpenProductsInTracking_WorkFlow($product_id, $wh_id)
    {
        try {
                DB::enablequerylog();
                $statusArr = array("1", "57089", "57075");
                $sql1 = DB::selectFromWriteConnection(DB::raw(" select count(*) as count1 from inventory_tracking where product_id = ".$product_id." and le_wh_id = ".$wh_id." and approval_status not in (57089, 57075, 1)  "));
                $rrr=DB::getquerylog();
                $data = json_decode(json_encode($sql1), true);
                return isset($data[0]['count1'])?$data[0]['count1']:0;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function addInQueueWithBulk($data) {
        
       $queue = new Queue();
       $data = json_encode($data);
       $data = base64_encode($data);
       $args = array("ConsoleClass" => 'inventoryLog', 'arguments' => array('insert', $data));
       $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);
    }

    public function userEmailsByIds($userIdArr)
    {
        try {
                $emails = DB::table("users")->where('is_active',1)->whereIn("user_id", $userIdArr)->pluck("email_id")->all();
                return $emails;
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function getUploadedFile($bulkuploadId)
    {
        try {

            $sql = DB::table("inventory_bulk_upload")->where("bulk_upload_id", $bulkuploadId)->get()->all();
            $sql = json_decode(json_encode($sql), true);
            return $sql;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
    public function checkInvNegativeValues($miss_qua,$dit,$dnd,$excess){



        $resulted_SOH       = $inventory_table_soh - $value['quarantine_qty'] +$excess;
        $resulted_dit_qty   = $inventory_table_dit_qty + $dit_Diff;

        $resulted_dnd_qty   = $inventory_table_dnd_qty + $dnd_Diff - $excess;
        
        $resulted_quaratine_qty = $getinventory_table_values[0]['quarantine_qty'] - $value['quarantine_qty'];
    }


        public function getAllProductsByWareHouseBySelection($warehouseId, $filterBy = '', $productId,$makeFinalSql,$offset,$perpage,$orderBy) {

        $this->_roleRepo    = new RoleRepo();
        $warehousename      = $this->wareHouseNameById($warehouseId);
        $result             = array();

        $sql    = $this->where('vw_inventory_report.le_wh_id', '=', $warehouseId);


        if($orderBy!=''){
            $sql->orderByRaw($orderBy);
        }

        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if(substr_count($value,'sku')){
                $skuArr = explode('%', $value);
                $strs = str_replace(",","','" , $skuArr[1]);
                $sqlWhrCls .= " sku like '%" .$strs."%'";

            }else if( substr_count($value,'product_id') ){
                $sqlWhrCls .= "vw_inventory_report".'.'.$value;
            }else if( $countLoop==0 ){
                $sqlWhrCls .=  $value;
            }else if( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }
        if($sqlWhrCls!=''){
            $sql->whereRaw($sqlWhrCls);
        }



        if($productId != 0)
        {
            $sql = $sql->where('product_id', '=', $productId);
        }

        if(!empty($filterBy))
        {
            if (!empty($filterBy['manf_name'])) {
                $sql = $sql->whereIn('manufacturer_id', $filterBy['manf_name']);
            }
            
            if (isset($filterBy['sellable'])) {
                $sql = $sql->where('is_sellable', $filterBy['sellable']);
            }

            if (isset($filterBy['cpEnabled'])) {
                $sql = $sql->where('cp_enabled', $filterBy['cpEnabled']);
            }
            
            if (!empty($filterBy['brand'])) {
                $sql = $sql->whereIn('brand_id', $filterBy['brand']);
            }

            if (!empty($filterBy['category'])) {
                $sql = $sql->whereIn('product_class_id', $filterBy['category']);
            }

            /*if (!empty($filterBy['kvi'])) {
                $sql = $sql->whereIn('kvi', $filterBy['kvi']);
            }*/

            if (!empty($filterBy['ean_upc'])) {
                $sql = $sql->whereIn('upc', $filterBy['ean_upc']);
            }

            /*if (!empty($filterBy['product_titles'])) {
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
            }*/

           /* if (isset($filterBy['soh_max']) && ((int)$filterBy['soh_max'] == (int)$filterBy['soh_min'])) {
                $sql = $sql->where('soh', [(int) $filterBy['soh_min']]);
            }
            else if(isset($filterBy['soh_max']) && ((int)$filterBy['soh_max'] != (int)$filterBy['soh_min']))
            {
                $sql = $sql->whereBetween('soh', [(int) $filterBy['soh_min'], (int) $filterBy['soh_max']]);
            }*/
            
           /* if (isset($filterBy['map_max']) && ((int)$filterBy['map_max'] == (int)$filterBy['map_min'])) {
                $sql = $sql->where('map', [(int) $filterBy['map_max']]);
            }else if(isset($filterBy['map_max']) && ((int)$filterBy['map_max'] != (int)$filterBy['map_min'])) {
                $sql = $sql->whereBetween('map', [(int) $filterBy['map_min'], (int) $filterBy['map_max']]);   
            }

            if (isset($filterBy['inv_max']) && ((int)$filterBy['inv_max'] == (int)$filterBy['inv_min'])) {
                $sql = $sql->where('available_inventory', [(int) $filterBy['inv_max']]);
            }else if (!isset($filterBy['inv_max']) && ((int)$filterBy['inv_max'] != (int)$filterBy['inv_min'])) {
                $sql = $sql->whereBetween('available_inventory', [(int) $filterBy['inv_min'], (int) $filterBy['inv_max']]);
            }*/
            




    }

    
        $allReplanishmencodes = $this->getReplanishmentCodes();
       
        /*$sql->leftJoin(DB::raw("(SELECT SUM(di) AS DI,SUM(mi) AS MI,SUM(ci) AS CI,product_id FROM product_di_mi_ci_flat WHERE prod_date 
            BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()  GROUP BY product_di_mi_ci_flat.product_id) as a"),"a.product_id", "=", "vw_inventory_report.product_id");*/


        if($perpage==0 && $offset==0) {
            $resultcount = $sql->get(array('inv_id'))->all();
            return count($resultcount);
        } else {
            $sql->skip($offset)->take($perpage);
        }
        $result['results'] = $sql->get(array('inv_id', 'primary_image', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'ptrvalue', 'cp', 'vw_inventory_report.le_wh_id', 'vw_inventory_report.product_id', 'reserved_qty', 'quarantine_qty', 'inv_display_mode', 'dit_qty', 'dnd_qty', 'product_group_id', 'min-pickface-replenishment as replanishment_level', 'replenishment_UOM', 'star', 'esp','di','mi','ci','isd7','isd30','isd',
        ))->all();
          
        $resultArr = json_decode(json_encode($result['results']), true);
        $inventoryEditAccess    = $this->_roleRepo->checkPermissionByFeatureCode('INV1002');
        foreach($resultArr as $key=>$data){
            $resultArr[$key]['bin_location'] = $this->getBinNames($data['le_wh_id'], $data['product_id']);
            $resultArr[$key]['re_pending_qty'] = $this->pendingReturns($data["product_id"], $warehouseId);
            $resultArr[$key]['replanishment_uom'] = isset($allReplanishmencodes[$data['replenishment_UOM']])?$allReplanishmencodes[$data['replenishment_UOM']]:"";
            $resultArr[$key]['product_id'] = "<a href='/editproduct/".$data['product_id']."' target='_blank' class='idallign'><strong>".$data['product_id']."</strong></a>";
            if($inventoryEditAccess == 1) {
                $resultArr[$key]['actions'] = '<a data-type="edit" data-ditqty="'.$data['dit_qty'].'" data-dndqty = "'.$data['dnd_qty'].'" data-dcname="'. $warehousename .'" data-skuid="'. $data['sku'] .'" data-producttitle="'. $data['product_title'] .'" data-warehouseid="' . $warehouseId . '" data-prodid="' . $data['product_id'] . '" data-soh="'. $data['soh'] .'" data-atp = "'. $data['atp'] .'" data-toggle="modal" data-target="#edit-products"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            }
            else {
                $resultArr[$key]['actions'] = '';
            }
            
        }
        if(isset($resultArr)){
            $result['results'] = $resultArr;
        } else {
            $result['results'] = '';
        }
        return $result;
    }




    // this function is used for added extra coloums like isd,isd7,isd30,di,mi,ci

        public function getAllProductsByWareHouseForExcelNewValues($warehouseId, $filterBy = '', $productId) {
        $this->_roleRepo = new RoleRepo();
        $warehousename = $this->wareHouseNameById($warehouseId);
        $result = array();
        $sql = $this->where('vw_inventory_report.le_wh_id', '=', $warehouseId);

        

        if($productId != 0)
        {
            $sql = $sql->where('product_id', '=', $productId);
        }

        if(!empty($filterBy))
        {
            if (!empty($filterBy['manf_name'])) {
                $sql = $sql->whereIn('manufacturer_id', $filterBy['manf_name']);
            }

            if (isset($filterBy['sellable'])) {
                $sql = $sql->where('is_sellable', $filterBy['sellable']);
            }

            if (isset($filterBy['cpEnabled'])) {
                $sql = $sql->where('cp_enabled', $filterBy['cpEnabled']);
            }

            if (!empty($filterBy['brand'])) {
            $sql = $sql->whereIn('brand_id', $filterBy['brand']);
            }

            if (!empty($filterBy['category'])) {
                $sql = $sql->whereIn('product_class_id', $filterBy['category']);
            }

            if (!empty($filterBy['kvi'])) {
                $sql = $sql->whereIn('kvi', $filterBy['kvi']);
            }

            if (!empty($filterBy['ean_upc'])) {
                $sql = $sql->whereIn('upc', $filterBy['ean_upc']);
            }

            if (!empty($filterBy['product_titles'])) {
                $sql = $sql->whereIn('product_id', $filterBy['product_titles']);
            }

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

            if (isset($filterBy['mrp_max']) && ((int)$filterBy['mrp_max'] == (int)$filterBy['mrp_min'])) {
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
            }else if (isset($filterBy['inv_max']) && ((int)$filterBy['inv_max'] != (int)$filterBy['inv_min'])) {
                $sql = $sql->whereBetween('available_inventory', [(int) $filterBy['inv_min'], (int) $filterBy['inv_max']]);
            }

            if (!empty($filterBy['sku'])) {
                $sql = $sql->whereIn('sku', $filterBy['sku']);
            }
    }


        $result['results']      = $sql->get(array('inv_id', 'primary_image', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'ptrvalue', 'cp', 'vw_inventory_report.le_wh_id', 'vw_inventory_report.product_id', 'category_name', 'sub_category_name', 'manufacturer_name', 'freebee_sku', 'frebee_desc', 'cfc_qty', 'is_Sellable', 'pack_type', 'reserved_qty', 'cp_enabled', 'esu', 'dit_qty', 'dnd_qty', 'quarantine_qty', 'esp', 'elp', 'state_id', 'ptrvalue', 'product_group_id', 'star','di','mi','ci','isd7','isd30','isd',
            
        ))->all();
        
        $resultArr              = json_decode(json_encode($result['results']), true);
        $inventoryEditAccess    = $this->_roleRepo->checkPermissionByFeatureCode('INV1002');
    
        foreach($resultArr as $key=>$data){
            $resultArr[$key]['bin_location'] = $this->getBinNames($data['le_wh_id'], $data['product_id']);
            $po_details = $this->getPODetails($data['product_id']);
           if($filterBy['freebeedata'] != 'YESF001') {
                //Filter out SKU's which falls under below conditions
                if($data['is_sellable']==0){
             //            if($data['is_sellable']==0 && !isset($po_details['po_code']) && $data['soh'] == 0){
                    unset($resultArr[$key]); continue;
                }
            }
            

            $resultArr[$key]['po_code']         = (isset($po_details['po_code']) ? $po_details['po_code'] : '');
            $resultArr[$key]['po_date']         = (isset($po_details['po_date']) ? $po_details['po_date'] : '');
            $resultArr[$key]['po_qty']          = (isset($po_details['qty']) ? $po_details['qty'] : '');
            $resultArr[$key]['po_packType']     = (isset($po_details['packType']) ? $po_details['packType'] : '');
            $tax_Details                        = json_decode(json_encode($this->getVatPercentage($data['product_id'], $data['state_id'])), true);

            $resultArr[$key]['vatpercentage']   = isset($tax_Details[0]['tax_percentage'])?$tax_Details[0]['tax_percentage']:"";
            $resultArr[$key]['hsn_code']   = isset($tax_Details[0]['hsn_code'])?$tax_Details[0]['hsn_code']:"";
            $resultArr[$key]['re_pending_qty']   = $this->pendingReturns($data["product_id"], $warehouseId);
           if($data['cfc_qty'] == 0)
            {
                $resultArr[$key]['available_cfc_qty'] = 0;
            }
            else
            {
                $resultArr[$key]['available_cfc_qty'] = ($data['available_inventory']/$data['cfc_qty']);
            }
            //Changed as per Satish || Date : Oct 20 2016
            // New calculation should be '$data['available_inventory']/$data['cfc_qty']'
            //$resultArr[$key]['available_cfc_qty'] = ($data['soh']/$data['cfc_qty']);
            
            if($inventoryEditAccess == 1)
            {
                $resultArr[$key]['actions'] = '<a data-type="edit" data-ditqty="'.$data['dit_qty'].'" data-dndqty = "'.$data['dnd_qty'].'" data-dcname="'. $warehousename .'" data-skuid="'. $data['sku'] .'" data-producttitle="'. $data['product_title'] .'" data-warehouseid="' . $warehouseId . '" data-prodid="' . $data['product_id'] . '" data-soh="'. $data['soh'] .'" data-atp = "'. $data['atp'] .'" data-toggle="modal" data-target="#edit-products"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            }
            else
            {
                $resultArr[$key]['actions'] = '';
            }
            
        }
        if(isset($resultArr)){
            $result['results'] = $resultArr;
        } else {
            $result['results'] = '';
        }
        return $result;
    }
    public function getUserEmail($userId){
        $srmQuery = json_decode(json_encode(DB::table("users")->where('user_id','=',$userId)->where('is_active','=',1)->pluck('email_id')->all()), true);
        return ($srmQuery[0]);
    }
    public function getWriteoffUploadedFile($bulkuploadId)
    {
        try {

            $sql = DB::table("inventory_writeoff_upload")->where("writeoff_upload_id", $bulkuploadId)->get()->all();
            $sql = json_decode(json_encode($sql), true);
            return $sql;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    public function getProductsInfo($productsArr = null)
    {
        $query =
            'SELECT 
                product_id,
                CONCAT(product_title," (",sku,")") as product_title
            FROM
                products';

        $result = DB::SELECT($query);

        return json_encode($result);
    }

    public function getKPIOOSReportModal($parameters,$periodType=1)
    {
        // Log::info($periodType);
        // Procedure for Chart UI
        /**
        * Params List
        * 1 -> Product Id => NULL (for all products)
        * 2 -> From Date => YYYY-MM-DD
        * 3 -> To Date => YYYY-MM-DD
        * 3 -> Flag => 2 (for Chart), 1 (for Grid)
        * 4 -> Period Type => 
        *       1 -> for MTD or for Yesterday or for Today 
        *       2 -> for WTD 
        *       3 -> for YTD
        *       4 -> for Quarter
        */ 
        $query = "CALL getKPIOOSReport(?,?,?,2,?)";
        $parameters['productId'] = ($parameters['productId'] == "0")?NULL:$parameters['productId'];
        try {
            $chartResult = 
                DB::SELECT($query,[
                    $parameters['productId'],
                    $parameters['startDate'],
                    $parameters['endDate'],
                    $periodType,
                ]);
        } catch (\Exception $e) {
            return -1;
        }
        if(empty($chartResult)) return [];
        
        // Procedure for IG Grid
        $query = "CALL getKPIOOSReport(?,?,?,1,0)";
        $gridResult = 
            DB::SELECT($query,[
                $parameters['productId'],
                $parameters['startDate'],
                $parameters['endDate'],
            ]);

        if(empty($gridResult))  return [];
        
        return [
            "chart" => json_decode(json_encode($chartResult)),
            "grid" => json_decode(json_encode($gridResult)),
        ];
    }
     public function getAllProductsByOnlyWareHouseIdForInvAdjExcel($warehouseId) {

        $rolesObj       = new Role();
        $productIDs     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        $prod_ids       = array_keys($productIDs['products']);
        $sql            = $this->where('vw_inventory_report.le_wh_id', '=', $warehouseId);
        $sql            = $sql->whereIn('product_id', $prod_ids);
        $result         = $sql->get(array('primary_image', 'product_title', 'sku', 'kvi', 'mrp', 'atp', 'soh', 'map', 'order_qty', 'available_inventory', 'upc', 'product_id','category_name', 'sub_category_name', 'manufacturer_name', 'freebee_sku', 'frebee_desc', 'cfc_qty', 'is_Sellable', 'pack_type', 'reserved_qty', 'cp_enabled', 'esu', 'dit_qty', 'dnd_qty', 'quarantine_qty', 'esp', 'elp', 'state_id', 'ptrvalue', 'product_group_id', 'star'))->all();
        $result         = json_decode(json_encode($result), true);
        
        foreach($result as $key=>$data){
            $po_details = $this->getPODetails($data['product_id']);
            $result[$key]['bin_location'] = $this->getBinNames($warehouseId, $data['product_id']);
            //Filter out SKU's which falls under below conditions
            if($data['is_sellable']==0){
//            if($data['is_sellable']==0 && !isset($po_details['po_code']) && $data['soh'] == 0){
                unset($result[$key]); continue;
            }

            $result[$key]['po_code'] = (isset($po_details['po_code']) ? $po_details['po_code'] : '');
            $result[$key]['po_date'] = (isset($po_details['po_date']) ? $po_details['po_date'] : '');
            $result[$key]['po_qty'] = (isset($po_details['qty']) ? $po_details['qty'] : '');
            $result[$key]['po_packType'] = (isset($po_details['packType']) ? $po_details['packType'] : '');
            $tax_Details                        = json_decode(json_encode($this->getVatPercentage($data['product_id'], $data['state_id'])), true);

            $result[$key]['vatpercentage']   = isset($tax_Details[0]['tax_percentage'])?$tax_Details[0]['tax_percentage']:"";
            $result[$key]['hsn_code']   = isset($tax_Details[0]['hsn_code'])?$tax_Details[0]['hsn_code']:"";
            $result[$key]['re_pending_qty'] = $this->pendingReturns($data["product_id"], $warehouseId);
            if($data['cfc_qty'] == 0)
            {
                $result[$key]['available_cfc_qty'] = 0;
            }
            else
            {
                $result[$key]['available_cfc_qty'] = ($data['available_inventory']/$data['cfc_qty']);
            }
            //Changed as per Satish || Date : Oct 20 2016
            // New calculation should be '$data['available_inventory']/$data['cfc_qty']'
            //$resultArr[$key]['available_cfc_qty'] = ($data['soh']/$data['cfc_qty']);
            
        }
        // array_unique($result);
        return $result;
    }
    //Inventory Adjustment
     public function getInventoryAdjReasonCodes()
    {
        $sql  = DB::table("master_lookup")->join("master_lookup_categories",'master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id')->where("master_lookup_categories.mas_cat_name", "=","Inventory Adjustment")->where('master_lookup.is_active','=',1)->pluck("master_lookup_name","value")->all();
        return $sql;
    }
      public function updatingInvAdjValues($productsData, $warewhouseid, $URL="")
    {
    try{
        $approval_flow_func     = new CommonApprovalFlowFunctionModel();
        $rolesObj               = new Role();
        $inventorySOH           = new InventorySOH();
        $errorArray             = array();
        $mainArr                = array();
        $updateCounter          = 0;
        $elpespCount            = 0;
        $roleRepo               = new RoleRepo();
        $validateelpesp         = $roleRepo->checkPermissionByFeatureCode('VLDESPELP001');
        //$allproductsForUSer     = json_decode($rolesObj->getFilterData(9, Session::get('userId')), true);

        //$allproductIds          = $allproductsForUSer['products'];
        $getallWarehouseIds     = $this->filterOptions();
        $i=1;
        $timestamp              = date('Y-m-d H:i:s');
        $current_timeStamp      = strtotime($timestamp);
       
        $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Inventory Adjustment', 'drafted', \Session::get('userId'));
        
        $curr_status_ID         = $res_approval_flow_func['currentStatusId'];
        $nextlevelStatusId      = $res_approval_flow_func['data'][0]['nextStatusId'];

        $insert_array = array("filepath"                => $URL,
                               "approval_status"         => $nextlevelStatusId,
                               "created_by"              => \Session::get('userId'),
                               "inv_type"                => "inv_adjustment"
                            );

        $bulk_upload_id = DB::table("inventory_bulk_upload")->insertGetId($insert_array);
        // $approval_flow_func->storeWorkFlowHistory('Inventory Bluk Upload', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId'));
        $negvalinvupload = array();
        $cnt_inv = 3;
        $neg_cnt =0;
        $checkneg = 0;
        foreach ($productsData as $value) {
            $elpesp_flag=0;
           $countWareAndProductId = $this->checkWareHouseAndProductId($warewhouseid, $value['product_id']);

           if($countWareAndProductId < 1)
           {
                $errorArray['wrongCombination'][] = 'ProductId #' . $value['product_id']." Product Does not existed for this warehouse <br>";
                $checkneg++;
                continue;
           }

           if($validateelpesp)
           {

                   $check_espandelp_indcandapob=$inventorySOH->checkEspElpIn_Parent_DC_APOB($value['product_id'],$warewhouseid);

                   if($check_espandelp_indcandapob===false)
                   {
                        $errorArray['elpespnotFound'][] = 'Line #' . ($i+2)." Product Does not have ELP,ESP for this warehouse or Parent DC/APOB <br>";
                        $elpesp_flag=1;
                        $elpespCount++;
                        $i++;
                        continue;
                   }
           }

           /* if(!isset($allproductIds[$value['product_id']]))
            {
                $errorArray['productIderrors'][] = 'ProductId #' . $value['product_id']." Invalid Product Ids <br>";
                $checkneg++;
                continue;
            } */           //checyking all inputs weather it is empty or not
            if(empty($value['excess']) && empty($value['dit_qty']) && empty($value['missing']))
            {
                $errorArray['commenterrors'][] = 'ProductId #' . $value['product_id']." Blank inputs  <br>";
                $checkneg++;
                continue;
            }
            if($value['comments'] == "" || empty($value['comments']))
           {
                $errorArray['commenterrors'][] = 'ProductId #' . $value['product_id']." Empty commments <br>";
                $checkneg++;
                continue;
           }
            if($value['reason'] == "" || empty($value['reason']))
           {
                $errorArray['reasonerrors'][] = 'ProductId #' . $value['product_id']." Empty Reasons <br>";
                $checkneg++;
                continue;
           }

           $check_valid_reason_or_not = $this->getInvAdjReasonCodeBasedOnReasonType($value['reason']);
           if($check_valid_reason_or_not[0] == 0)
           {
                $errorArray['reason_mismatch_errors'][] = 'ProductId #' . $value['product_id']." Invalid Reasons <br>";
                $checkneg++;
                continue;
           }
           $getcurrent_soh = $this->getSOH($value['product_id'], $warewhouseid);
           $checkcount_tracking_table =$this->getOpenProductsInTracking_WorkFlowForInvAdj($value['product_id'], $warewhouseid);
            $cal_dit = 0;
            $cal_dnd = 0;
            $excess_soh_qty = 0;
            $cal_dnd = ($getcurrent_soh[0]['dnd_qty'])+($value['missing']);
            $cal_dit = ($getcurrent_soh[0]['dit_qty'])+($value['dit_qty']);
            $excess_soh_qty = ($getcurrent_soh[0]['soh'])+($value['excess']);
           
          
           if($cal_dit < 0 || $cal_dnd < 0 || $excess_soh_qty < 0)
           {
                $errorArray['dnderrors'][] = 'ProductId #' . $value['product_id']." Getting Negative(-ve) values for Excess(".$excess_soh_qty.") or DIT(".$cal_dit.") or Missing(".$cal_dnd.") !! <br>";
                $i++;
                $checkneg++;
                continue;
           }
           if($checkcount_tracking_table != 0)
           {
                $errorArray['dnderrors'][] = 'ProductId #' . $value['product_id']." Error !! Approval request for same product is pending. Please close pending requests first to continue. <br>";
                $i++;
                $checkneg++;
                continue;
           }
           $checkinv=$this->checkInvNegativeAgainstOrderQty($warewhouseid,$value['product_id'],$value['excess'], 0);

           if($checkinv['status']==400)
           {
            $errorArray['negativesoh'][]=$checkinv['message'];
            $checkneg++;
            continue;
           }
       }

       if($checkneg == 0)
       {
            foreach ($productsData as $value) {
            
               $countWareAndProductId = $this->checkWareHouseAndProductId($warewhouseid, $value['product_id']);

               if($countWareAndProductId < 1)
               {
                    $errorArray['wrongCombination'][] = 'Line #' . ($i+2)." Product Does not existed for this warehouse <br>";
                    $i++;
                    continue;
               }

                /*if(!isset($allproductIds[$value['product_id']]))
                {
                    $errorArray['productIderrors'][] = 'Line #' . ($i+2)." Invalid Product Ids <br>";
                    $i++;
                    continue;
                }*/
  //              log::info("checking excess and dit and dnd...");
//                log::info("excess qty....".$value['excess']."....dit_qty//....".$value['dit_qty'].'____dnd_Qt...'.$value['missing']);
                //checyking all inputs weather it is empty or not
                if(empty($value['excess']) && empty($value['dit_qty']) && empty($value['missing']))
                {
                    $errorArray['commenterrors'][] = 'Line #' . ($i+2)." Blank inputs  <br>";
                    $i++;
                    continue;
                }
                if($value['comments'] == "" || empty($value['comments']))
               {
                    $errorArray['commenterrors'][] = 'Line #' . ($i+2)." Empty commments <br>";
                    $i++;
                    continue;
               }
                if($value['reason'] == "" || empty($value['reason']))
               {
                    $errorArray['reasonerrors'][] = 'Line #' . ($i+2)." Empty Reasons <br>";
                    $i++;
                    continue;
               }

               $check_valid_reason_or_not = $this->getInvAdjReasonCodeBasedOnReasonType($value['reason']);
               if($check_valid_reason_or_not[0] == 0)
               {
                    $errorArray['reason_mismatch_errors'][] = 'Line #' . ($i+2)." Invalid Reasons <br>";
                    $i++;
                    continue;
               }
               $getcurrent_soh = $this->getSOH($value['product_id'], $warewhouseid);
               $checkcount_tracking_table =$this->getOpenProductsInTracking_WorkFlowForInvAdj($value['product_id'], $warewhouseid);
                $cal_dit = 0;
                $cal_dnd = 0;
                $excess_soh_qty = 0;
                $cal_dnd = ($getcurrent_soh[0]['dnd_qty'])+($value['missing']);
                $cal_dit = ($getcurrent_soh[0]['dit_qty'])+($value['dit_qty']);
                $excess_soh_qty = ($getcurrent_soh[0]['soh'])+($value['excess']);

              
               if($cal_dit < 0 || $cal_dnd < 0 || $excess_soh_qty < 0)
               {
                    $errorArray['dnderrors'][] = 'Line #' . ($i+2)." Getting Negative(-ve) values for Excess(".$excess_soh_qty.") or DIT(".$cal_dit.") or Missing(".$cal_dnd.") !! <br>";
                    $i++;
                    continue;
               }
               if($checkcount_tracking_table != 0)
               {
                    $errorArray['dnderrors'][] = 'Line #' . ($i+2)." Error !! Approval request for same product is pending. Please close pending requests first to continue. <br>";
                    $i++;
                    continue;
               }

           if($elpesp_flag==0)
           {
             
               $updatevals = $this->invAdjUpdateProducts($value['excess'], $value['soh'], $warewhouseid, $value['product_id'], $value['sku'], $value['comments'], $value['dit_qty'], $value['missing'], $check_valid_reason_or_not[0], $bulk_upload_id);

                if($updatevals == "failed")
                {
                    $errorArray['dnderrors'][] = 'Line #' . ($i+2)." Quarantine Quantity always be less than or equals to soh!! <br>"; //server side validations
                    $i++;
                    continue;
                }           
               
               if($updatevals == 1)
               {
                    $updateCounter++;
               }

               if($updatevals == 0)
               {
                $errorArray['samerecords'][] = 'Line #' . ($i+2)." Duplicate data <br>";
               }
            }

                $i++;
           }
       }

       if($updateCounter > 0)
       $approval_flow_func->storeWorkFlowHistory('Inventory Adjustment', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId')); //creating tickets
       $mainArr['success']                  = $errorArray;
       $mainArr['dpulicate_count']          = (isset($errorArray['samerecords'])?count($errorArray['samerecords']):0);
       $mainArr['updated_count']            = $updateCounter;
       $wrong_combination                   = (isset($errorArray['wrongCombination'])?count($errorArray['wrongCombination']):0);
       $product_errors                      = (isset($errorArray['productIderrors'])?count($errorArray['productIderrors']):0);
       $soh_error_count                     = (isset($errorArray['soherrors'])?count($errorArray['soherrors']):0);
       $atp_error_count                     = (isset($errorArray['atperrors'])?count($errorArray['atperrors']):0);
       $commetErrorsCount                   = (isset($errorArray['commenterrors'])?count($errorArray['commenterrors']):0);
       $reasonErrorsCount                   = (isset($errorArray['reasonerrors'])?count($errorArray['reasonerrors']):0);
       $reason_mismatch_Count               = (isset($errorArray['reason_mismatch_errors'])?count($errorArray['reason_mismatch_errors']):0);
       $ditErrorsCount                      = (isset($errorArray['diterrors'])?count($errorArray['diterrors']):0);
       $dndErrorsCount                      = (isset($errorArray['dnderrors'])?count($errorArray['dnderrors']):0);
       $negativesohError                  = (isset($errorArray['negativesoh'])?count($errorArray['negativesoh']):0);
       $mainArr['error_count']              = $wrong_combination + $product_errors + $soh_error_count + $atp_error_count + $commetErrorsCount + $ditErrorsCount + $dndErrorsCount+$reasonErrorsCount + $reason_mismatch_Count+$negativesohError ;
       $elpespnotfoundCount                      = (isset($errorArray['elpespnotFound'])?count($errorArray['elpespnotFound']):0);
       $mainArr['elpesp_count']              =   $elpespnotfoundCount;

       $log_array                           = $mainArr;
       $mainArr['reference'] = $current_timeStamp;
       UserActivity::excelUploadFileLogs("INVENTORY", $current_timeStamp, $URL, $log_array);
       
       return $mainArr;
    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    }
    }
    public function getInvAdjReasonCodeBasedOnReasonType($reason_type)
    {
        $sql  = DB::table("master_lookup")->join("master_lookup_categories",'master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id')->where("master_lookup_categories.mas_cat_name", "=","Inventory Adjustment")->where('master_lookup.is_active','=',1)->where('master_lookup.master_lookup_name','=',$reason_type)->pluck("value")->all();
        if(empty($sql))
        {
            $sql = "0";
        }
        return $sql;
    }

    public function invAdjUpdateProducts($excess_qty, $sohval, $wareId, $prodId, $sku, $comments, $dit_qty, $dnd_qty, $reason="", $bulk_upload_id="")
    {
    try{
        $approval_flow_func                 = new CommonApprovalFlowFunctionModel();
        
        $returnval                          = 1;
        $timestamp                          = date('Y-m-d H:i:s');
        $oldvalues                          = $this->getOldSOHAndATPValues($prodId, $wareId);
        
        if($bulk_upload_id != "")
        {
            //when bulk upload came then only
            if(strlen($sohval) == 0)
                $sohval = $oldvalues['soh'];

            // if(strlen($atpval) == 0)
            //     $atpval = $oldvalues['atp'];

            if(strlen($dit_qty) == 0)
                // $dit_qty = $oldvalues['dit_qty'];
                $dit_qty = 0;

            if(strlen($dnd_qty) == 0){
                // $dnd_qty = $oldvalues['dnd_qty'];
                $dnd_qty = 0;
            }
        }

        $stock_difference                   = ($sohval) - ($oldvalues['soh']);
        $dit_diff                           = ($dit_qty) - ($oldvalues['dit_qty']);
        $dnd_diff                           = ($dnd_qty) - ($oldvalues['dnd_qty']);
        $user_ID                            = Session::get('userId');
        if($bulk_upload_id == "" || $bulk_upload_id == 0 || $bulk_upload_id == NULL)
        {
            $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('Inventory Adjustment', 'drafted', \Session::get('userId'));
        }
        else
        {
            $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('Inventory Adjustment', 'drafted', \Session::get('userId'));            
        }
        
        
        if($res_approval_flow_func['status'] == 1)
        {
            $curr_status_ID = $res_approval_flow_func['currentStatusId'];
            $nextlevelStatusId = $res_approval_flow_func['data'][0]['nextStatusId'];
           // Log::info("-----current status id---".$curr_status_ID.'-----next level id-----'.$nextlevelStatusId);
            $insert_array = array("product_id"      => $prodId,
                               "le_wh_id"           => $wareId,
                               "activity_type"      => $reason,
                               "approval_status"    => $nextlevelStatusId,
                               "dit_diff"           => $dit_qty,
                               "dnd_diff"           => $dnd_qty,
                               "excess"             => $excess_qty,
                               "created_by"         => $user_ID,
                               "approved_by"        => $user_ID,
                               "remarks"            => $comments
                               ,"bulk_upload_id"    => $bulk_upload_id,
                               );
            $inv_track_id = DB::table("inventory_tracking")->insertGetId($insert_array);
            if(!$inv_track_id)
            {
                return "failed"; // here dit_diff and dnd_diff data type is un-signed if negitive value came query won't execute then we are returning failed
            }
            if($bulk_upload_id != "")
            {
                // $approval_flow_func->storeWorkFlowHistory('Inventory Bulk Upload', $bulk_upload_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId'));
            }
            else
            {
                $approval_flow_func->storeWorkFlowHistory('Inventory', $inv_track_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId'));                
            }
            

            //Check if current step is final..
            if($res_approval_flow_func['data'][0]['isFinalStep'] == 1)
            {
                $tableUpdateID = 1;
                // update the inventory table
                $updateInventory = $this->updateInventoryTable($inv_track_id);
                $update_tracking_table = $this->updateTrackingTableWithStatus($tableUpdateID, $inv_track_id);
            }
        }
        elseif($res_approval_flow_func['status'] == 0)
        {
            $returnval = 0;
        }
        
        return $returnval;
    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    }        
            

    }
    /*inv adjustment process*/
     public function getInvAdjustmentTrackingDetails($trackId)
    {
        $sql    = DB::table("inventory_tracking")
                    ->select('quarantine_qty', 'product_id', 'stock_diff','dit_diff', 'dnd_diff', 'new_atp', 'new_soh', 'product_id', 'old_soh', 'old_atp', 'le_wh_id', 'old_dit_qty', 'new_dit_qty', 'old_dnd_qty', 'new_dnd_qty', "inventory_tracking.remarks", "inventory_tracking.approval_status", "master_lookup_name", DB::raw('GetUserName(inventory_tracking.created_by, 2) as user'), DB::raw('getLeWhName(inventory_tracking.le_wh_id) as warehouse'), "inventory_tracking.created_at", "excess")
                    ->join("inventory_bulk_upload","inventory_bulk_upload.bulk_upload_id","=","inventory_tracking.bulk_upload_id")
                    ->leftJoin("master_lookup", "inventory_tracking.activity_type", "=", "master_lookup.value")
                    // ->where("master_lookup.mas_cat_id", "=", "117")
                    ->where("inventory_bulk_upload.bulk_upload_id", "=", $trackId)
                    ->get()->all();
        $data   = json_decode(json_encode($sql), true);
        return $data[0];
    }
    public function getInvAdjustmentBulkTrackingDetails($trackId)
    {
         $sql    = DB::table("inventory_tracking")
                    ->select('inventory_tracking.product_id','products.product_title','mrp','esu','dit_diff', 'dnd_diff', 'new_atp', 'new_soh', 'inventory_tracking.product_id', 'old_soh', 'old_atp', 'le_wh_id', 'old_dit_qty', 'new_dit_qty', 'old_dnd_qty', 'new_dnd_qty', "remarks", "approval_status", "master_lookup_name", DB::raw('GetUserName(inventory_tracking.created_by, 2) as user'), DB::raw('getLeWhName(inventory_tracking.le_wh_id) as warehouse'), "inventory_tracking.created_at", "quarantine_qty", "excess", "remarks","approve_comment")
                   ->Join("master_lookup", "inventory_tracking.activity_type", "=", "master_lookup.value")
                    ->join('products','products.product_id','=','inventory_tracking.product_id')
                    //->where("master_lookup.mas_cat_id", "=", "117")
                    ->where("inventory_tracking.bulk_upload_id", "=", $trackId)
                    ->get()->all();
                    




        $data = json_decode(json_encode($sql), true);
        return $data;
    }
     public function invAdjUpdateInventoryTableforBulk($bulkuploadId)
    {
        try{
            date_default_timezone_set("Asia/Kolkata");
            $get_Soh_atp_vals   = $this->getInvAdjustmentBulkTrackingDetails($bulkuploadId);
            $invnegativearray = array();
            $batchModel = new \App\Modules\Orders\Models\batchModel();
            $orderCtrl =  new OrdersController();
            $batch_inventory_update = "";
            $batch_history_array = array();
            foreach ($get_Soh_atp_vals as $value) {
                $product_ID         = $value['product_id'];
                $wareHouse_id       = $value['le_wh_id'];
                $comments           = $value['remarks'];
                $getinventory_table_values = $this->getInventoryDetailsBasedOnProductId($product_ID, $wareHouse_id);

                $inventory_table_soh        = $getinventory_table_values[0]['soh'];
                $inventory_table_dit_qty    = $getinventory_table_values[0]['dit_qty'];
                $inventory_table_dnd_qty    = $getinventory_table_values[0]['dnd_qty'];

                // $sohval             = $value['new_soh'];
                // $soh_diff           = $value['stock_diff'];
                $dit_Diff           = $value['dit_diff'];
                $dnd_Diff           = $value['dnd_diff'];
                $excess             = $value['excess'];
                $atpvalue           = $value['new_atp'];
                $old_sohvalue       = $value['old_soh'];
                $old_atpvalue       = $value['old_atp'];
                $product_id         = $value['product_id'];

                $resulted_SOH       = $inventory_table_soh + ($excess);
                $resulted_dit_qty   = $inventory_table_dit_qty + ($dit_Diff);
                $resulted_dnd_qty   = $inventory_table_dnd_qty + ($dnd_Diff);
                
               
                if($resulted_SOH < 0)
                {
                    $invnegativearray[] = array("product_id"=>$value['product_id'],"","product_title"=>$value['product_title'],"upl_excess"=>$excess,"cur_dit"=>$inventory_table_dit_qty,"up_dit_qty"=>$dit_Diff,"cur_dnd_qty"=>$inventory_table_dnd_qty,"up_dnd_qty"=>$dnd_Diff);
                    continue;
                }
                if($resulted_dit_qty < 0)
                {
                   $invnegativearray[] = array("product_id"=>$value['product_id'],"","product_title"=>$value['product_title'],"upl_excess"=>$excess,"cur_dit"=>$inventory_table_dit_qty,"up_dit_qty"=>$dit_Diff,"cur_dnd_qty"=>$inventory_table_dnd_qty,"up_dnd_qty"=>$dnd_Diff);
                    continue;
                }
                if($resulted_dnd_qty < 0)
                {
                    
                    $invnegativearray[] = array("product_id"=>$value['product_id'],"","product_title"=>$value['product_title'],"upl_excess"=>$excess,"cur_dit"=>$inventory_table_dit_qty,"up_dit_qty"=>$dit_Diff,"cur_dnd_qty"=>$inventory_table_dnd_qty,"up_dnd_qty"=>$dnd_Diff);
                    continue;
                }
            }
           // Log::info("negative array___");
            //Log::info(print_r($invnegativearray, true));
            if(empty($invnegativearray))
            {
                foreach ($get_Soh_atp_vals as $value) {
                    $product_ID         = $value['product_id'];
                    $wareHouse_id       = $value['le_wh_id'];
                    $comments           = $value['remarks'];
                    $getinventory_table_values = $this->getInventoryDetailsBasedOnProductId($product_ID, $wareHouse_id);

                    $inventory_table_soh        = $getinventory_table_values[0]['soh'];
                    $inventory_table_dit_qty    = $getinventory_table_values[0]['dit_qty'];
                    $inventory_table_dnd_qty    = $getinventory_table_values[0]['dnd_qty'];
                    $inventory_table_quarantine = $getinventory_table_values[0]['quarantine_qty'];
                    $inventory_table_Order_qty  = $getinventory_table_values[0]['order_qty'];

                    // $sohval             = $value['new_soh'];
                    // $soh_diff           = $value['stock_diff'];
                    $dit_Diff           = $value['dit_diff'];
                    $dnd_Diff           = $value['dnd_diff'];
                    $excess             = $value['excess'];
                    $atpvalue           = $value['new_atp'];
                    $old_sohvalue       = $value['old_soh'];
                    $old_atpvalue       = $value['old_atp'];
                    $product_id         = $value['product_id'];
                    $resulted_SOH       = $inventory_table_soh + ($excess);
                    $resulted_dit_qty   = $inventory_table_dit_qty + ($dit_Diff);
                    $resulted_dnd_qty   = $inventory_table_dnd_qty + ($dnd_Diff);
                    $sum_dit_dnd_excess  = $excess - ($dit_Diff+$dnd_Diff);

                    $inventory_reason = $value['remarks'];
               
                    $mytime = Carbon::now();

                    $update_array = array("soh"         => $resulted_SOH, 
                                          // "atp"         => $atpvalue, 
                                          "dit_qty"     =>$resulted_dit_qty, 
                                          "dnd_qty"     => $resulted_dnd_qty
                                          );
                   
                    //Update Inventory Tracking values

           //         Log::info("update invnetory table------------");
                    //update approved at in inventory tracking
                    $sql_inv_tracking = DB::table("inventory_tracking")->where("bulk_upload_id", $bulkuploadId)->update(array("approved_at" => $mytime->toDateTimeString()));
                    //Updating inventory table
                    $update_inventory                   = DB::table("inventory")
                                                            ->where("product_id", "=", $product_id)
                                                            ->where("le_wh_id", "=", $wareHouse_id)
                                                            ->update($update_array);

                    // adding data to batches,considering recent batch id as ref
                    // $batchData = array();
                    // $batch_inventory_update = "";
                    // // batches for excess
                    // if($excess > 0){
                    //     $batchData_ = DB::table("inventory_batch")
                    //                     ->where("product_id",$product_id)
                    //                     ->where("le_wh_id",$wareHouse_id)
                    //                     // ->where("qty",">",0)
                    //                     ->whereRaw("qty + ($excess) > $excess")
                    //                     ->orderBy("inward_id","ASC")
                    //                     ->first();
                    //     $batchData[] = $batchData_;
                    // }else{
                    //     $positive_excess = abs($excess);
                    //     $batchData = $orderCtrl->getBatchesByData($product_id,$wareHouse_id,$positive_excess,0,10,[]);
                    // }
                    // if(count($batchData) > 0){

                    //     $addNgt = ($excess > 0 ) ? "+" : "-";
                    //     foreach ($batchData as $key => $batch) {
                    //         Log::info(json_encode($batch));
                    //         Log::info("product_id".$product_id);
                    //         Log::info("excess".$excess);
                    //         if(count($batchData) == 1 && $excess > 0){
                    //             $batch_inventory_update .= "UPDATE inventory_batch SET qty= qty + ($excess) where product_id = $product_id and le_wh_id=$wareHouse_id and inward_id=$batch->inward_id;";
                    //             $batch_history_array[] = array("inward_id"=>$batch->inward_id,
                    //                                         "le_wh_id"=>$wareHouse_id,
                    //                                         "product_id"=>$product_id,
                    //                                         "qty"=>$addNgt.$excess,
                    //                                         "old_qty"=>$batch->qty,
                    //                                         'ref'=>$bulkuploadId,
                    //                                         'ref_type'=>6,
                    //                                         'dit_qty'=>0,
                    //                                         'old_dit_qty'=>$batch->dit_qty,
                    //                                         'dnd_qty'=>0,
                    //                                         'old_dnd_qty'=>$batch->dnd_qty,
                    //                                         'comments'=>"Qty Updated by Inventory Adjustment ID:$bulkuploadId (EXCESS QTY)");
                    //         }else{
                    //             //creating batch array
                    //             $batch_id = $batch->inward_id;
                    //             $invb_id = $batch->invb_id;
                    //             $elp = $batch->elp;
                    //             $req_qty = $positive_excess;
                    //             if($req_qty > $batch->qty){
                    //                 $used_qty = $batch->qty;
                    //             }else if($batch->qty >= $req_qty){
                    //                 $used_qty = $req_qty;
                    //             }
                    //             if(count($batchData) == 1){
                    //                 $batch_ord_qty = $positive_excess;
                    //             }else{
                    //                 $batch_ord_qty = $used_qty;
                    //             }
                    //             Log::info("used_qty".$used_qty);
                    //             Log::info("batch->qty".$batch->qty);
                    //             $batch_history_array[] = array("inward_id"=>$batch_id,
                    //                                 "le_wh_id"=>$wareHouse_id,
                    //                                 "product_id"=>$product_id,
                    //                                 "qty"=>'-'.$used_qty,
                    //                                 "old_qty"=>$batch->qty,
                    //                                 'ref'=>$bulkuploadId,
                    //                                 'ref_type'=>6,
                    //                                 'dit_qty'=>0,
                    //                                 'old_dit_qty'=>$batch->dit_qty,
                    //                                 'dnd_qty'=>0,
                    //                                 'old_dnd_qty'=>$batch->dnd_qty,
                    //                                 'comments'=>"Qty Updated by Inventory Adjustment ID:$bulkuploadId (EXCESS QTY)");
                    //             $positive_excess = $req_qty - $used_qty;
                    //             $batch_inventory_update .= "UPDATE inventory_batch SET qty=(qty-$used_qty) where invb_id = $invb_id;";
                    //         }
                    //     } 
                    // }

                    // Log::info("EXCESS QTY");
                    // Log::info($batch_inventory_update);
                    // if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                    //     DB::unprepared($batch_inventory_update);
                    // }

                    // $batchData = array();
                    // $batch_inventory_update = "";
                    // // batches for DIT
                    // $cond = 1;
                    // $positive_dit = abs($dit_Diff);
                    // $batchData = $orderCtrl->getBatchesByDataDIT($product_id,$wareHouse_id,$positive_dit,0,10,[],$cond);
                    // $addNgt = ($dit_Diff > 0 ) ? "+" : "-";
                    // foreach ($batchData as $key => $batch) {
                    //     //creating batch array
                    //     $batch_id = $batch->inward_id;
                    //     $invb_id = $batch->invb_id;
                    //     $elp = $batch->elp;
                    //     $req_qty = $positive_dit;
                    //     if($dit_Diff < 0){
                    //         $batch_qty = $batch->dit_qty;
                    //     }else{
                    //         $batch_qty = $batch->qty;
                    //     }
                    //     if($req_qty > $batch_qty){
                    //         $used_qty = $batch_qty;
                    //     }else if($batch_qty >= $req_qty){
                    //         $used_qty = $req_qty;
                    //     }
                    //     if(count($batchData) == 1){
                    //         $batch_ord_qty = $positive_dit;
                    //     }else{
                    //         $batch_ord_qty = $used_qty;
                    //     }
                    //     Log::info("used_qty".$used_qty);
                    //     Log::info("batch->qty".$batch->qty);
                    //     Log::info("batch->dit_qty".$batch->dit_qty);
                    //     Log::info("batch_qty".$batch_qty);
                    //     if($used_qty > 0)
                    //         $batch_history_array[] = array("inward_id"=>$batch_id,
                    //                             "le_wh_id"=>$wareHouse_id,
                    //                             "product_id"=>$product_id,
                    //                             "qty"=>0,
                    //                             "old_qty"=>$batch->qty,
                    //                             'ref'=>$bulkuploadId,
                    //                             'ref_type'=>6,
                    //                             'dit_qty'=>$addNgt.$used_qty,
                    //                             'old_dit_qty'=>$batch->dit_qty,
                    //                             'dnd_qty'=>0,
                    //                             'old_dnd_qty'=>$batch->dnd_qty,
                    //                             'comments'=>"Qty Updated by Inventory Adjustment ID:$bulkuploadId (DIT QTY)");

                    //     $positive_dit = $req_qty - $used_qty;
                    //     $dit_cond = "(dit_qty + $used_qty)";
                    //     if($dit_Diff < 0){
                    //         $dit_cond = "(dit_qty - $used_qty)";
                    //     }
                    //     $batch_inventory_update .= "UPDATE inventory_batch SET dit_qty=$dit_cond where invb_id = $invb_id;";
                    // }

                    // Log::info("DIT QTY");
                    // Log::info($batch_inventory_update);
                    // if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                    //     DB::unprepared($batch_inventory_update);
                    // }

                    // $batchData = array();
                    // $batch_inventory_update = "";
                    // // batches for DND
                    // $cond = 1;
                    // $positive_dnd = abs($dnd_Diff);
                    // $batchData = $orderCtrl->getBatchesByDataDND($product_id,$wareHouse_id,$positive_dnd,0,10,[],$cond);
                    // Log::info("DND Query");
                    // Log::info(json_encode($batchData));
                    // Log::info(json_encode($cond));
                    // Log::info(json_encode($positive_dnd));
                    // $addNgt = ($dnd_Diff > 0 ) ? "+" : "-";
                    // foreach ($batchData as $key => $batch) {
                    //     //creating batch array
                    //     Log::info(json_encode($batch));
                    //     $batch_id = $batch->inward_id;
                    //     $invb_id = $batch->invb_id;
                    //     $elp = $batch->elp;
                    //     $req_qty = $positive_dnd;
                    //     $batch_qty = $batch->dnd_qty;
                    //     if($dnd_Diff < 0){
                    //         $batch_qty = $batch->dnd_qty;
                    //     }else{
                    //         $batch_qty = $batch->qty;
                    //     }
                    //     if($req_qty > $batch_qty){
                    //         $used_qty = $batch_qty;
                    //     }else if($batch_qty >= $req_qty){
                    //         $used_qty = $req_qty;
                    //     }
                    //     if(count($batchData) == 1){
                    //         $batch_ord_qty = $positive_dnd;
                    //     }else{
                    //         $batch_ord_qty = $used_qty;
                    //     }
                    //     Log::info("used_qty".$used_qty);
                    //     Log::info("batch->qty".$batch->qty);
                    //     Log::info("batch->dnd_qty".$batch->dnd_qty);
                    //     Log::info("batch_qty".$batch_qty);
                    //     if($used_qty > 0)
                    //         $batch_history_array[] = array("inward_id"=>$batch_id,
                    //                             "le_wh_id"=>$wareHouse_id,
                    //                             "product_id"=>$product_id,
                    //                             "qty"=>0,
                    //                             "old_qty"=>$batch->qty,
                    //                             'ref'=>$bulkuploadId,
                    //                             'ref_type'=>6,
                    //                             'dit_qty'=>0,
                    //                             'old_dit_qty'=>$batch->dit_qty,
                    //                             'dnd_qty'=>$addNgt.$used_qty,
                    //                             'old_dnd_qty'=>$batch->dnd_qty,
                    //                             'comments'=>"Qty Updated by Inventory Adjustment ID:$bulkuploadId (DND QTY)");

                    //     $positive_dnd = $req_qty - $used_qty;
                    //     Log::info($positive_dnd);
                    //     $dnd_cond = "(dnd_qty + $used_qty)";
                    //     if($dnd_Diff < 0){
                    //         $dnd_cond = "(dnd_qty - $used_qty)";
                    //     }
                    //     $batch_inventory_update .= "UPDATE inventory_batch SET dnd_qty=$dnd_cond where invb_id = $invb_id;";
                    // } 

                    // Log::info("DND QTY");
                    // Log::info($batch_inventory_update);
                    // if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                    //     DB::unprepared($batch_inventory_update);
                    // }
                  
                } 
                

                //updating batch data
                // if(isset($batch_history_array) && count($batch_history_array)) {
                //     $batchModel->insertBatchHistory($batch_history_array);
                // }
                
            }
            else
            {
                
                $notificationObj= new NotificationsModel();
                $usersObj = new Users(); 
                $userIdData= $notificationObj->getUsersByCode('INVADJ');
                $userIdData=json_decode(json_encode($userIdData),true); 
                $subject=$notificationObj->getMessageByCode('INVADJ'); 
               
                $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get()->all();
                 $emails=json_decode(json_encode($data,1),true); 
                $getEmails=array(); 
                foreach ($emails as $keyValue )
                {
                 $getEmails[]=$keyValue['email_id']; 
                }

                // Mail::send('emails.InvAdjustmentTemplate',["tableData"=>  
                //     $invnegativearray], function ($message) use ($getEmails, $subject) {
                //             $message->to($getEmails);
                //             $message->subject($subject.'('.date('d-M-Y').')' );
                //         });
                $subject=$subject.'('.date('d-M-Y').')';
                $body = array('template'=>'emails.InvAdjustmentTemplate', 'attachment'=>'',"tableData"=>$invnegativearray);
                Utility::sendEmail($getEmails, $subject, $body);
           //     Log::info("Inventory Adjustment email function");
             //   Log::info(print_r($getEmails,true));

                
                
                return $invnegativearray;
            }


        }catch(ErrorException $ex)
        {
            //Log::info("Inventory Adjustment final approval ");
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return array("Inventory Adjustment final level approval getting error.");
        }
    }
      public function getOpenProductsInTracking_WorkFlowForInvAdj($product_id, $wh_id)
    {
        try {
                DB::enablequerylog();
                $statusArr = array("1", "57089", "57075");
                $sql1 = DB::selectFromWriteConnection(DB::raw(" select count(*) as count1 from inventory_tracking where product_id = ".$product_id." and le_wh_id = ".$wh_id." and approval_status not in (57194,57089, 1)  "));
                $rrr=DB::getquerylog();
                $data = json_decode(json_encode($sql1), true);
                return isset($data[0]['count1'])?$data[0]['count1']:0;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

     public function inventoryStockistReports($dcid,$fromdate,$todate){

      try{
         $query = DB::selectFromWriteConnection(DB::raw("CALL getInventoryLedgerByDC('".$dcid."','".$fromdate."','".$todate."')")); 
         return $query;
      }catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
    public function getWarehouseListByAccessLevel(){
        $rolesObj = new Role();
        $userID = Session::get('userId');
        $warehouse = $rolesObj->getWarehouseData($userID,6);
        $warehouse=json_decode($warehouse,1);
        $warehouseData=array();
        if(count($warehouse)>0){
            if(isset($warehouse[118001])){
                $warehouseArray=explode(',',$warehouse[118001]);
                $warehouseData=DB::table('legalentity_warehouses')
                            ->select('le_wh_id','display_name')
                            ->whereIn('le_wh_id',$warehouseArray)
                            ->get()->all();
                $warehouseData=/*json_decode((json_encode(*/$warehouseData/*)),1)*/;

            }
        }
        return $warehouseData;
    }
    public function getExportInventoryData($warehouse,$userId){
        $data = DB::selectFromWriteConnection(DB::raw("CALL getInventoryReport(".$userId.",".$warehouse.")"));
        $data=json_decode(json_encode($data),1);
        $res['results']=$data;
        return $res;
    }
    public function getDcData($dcName){
        $data=DB::table('legalentity_warehouses')
                ->whereIn('le_wh_id',$dcName)
                ->get(array('le_wh_id','display_name as dcname'))->all();
        return $data;
    }
    public function getWarehouseByBu($bu_id){
        if(is_array($bu_id)){
            $bu_id = $bu_id[0];
        }
        $data = DB::select("call getBuHierarchy_proc($bu_id,@le_wh_ids)");
        $data =DB::select(DB::raw('select @le_wh_ids as wh_list'));
        if(count($data)>0){
            $result = explode(',',$data[0]->wh_list);
            $result = $this->getDcData($result);
            return $result;
        }else{
            return array();
        }
    }
    public function businessTreeData(){
        try{
            $allBusinessUnits = $this->allBusinessUnits();
            $allBusinessUnits = json_decode($allBusinessUnits,true);

            $finalArr = array();
            $parentWiseArr = array();
            $userId=Session::get('userId');
             $rawQuery = " SELECT GROUP_CONCAT(object_id) as object_id FROM `user_permssion` WHERE `user_id`=$userId and permission_level_id=6";
            $access =  DB::select(DB::raw($rawQuery));
            $access = isset($access[0]->object_id)?$access[0]->object_id:-1;
            //$access = (string) isset($access[0]->object_id)?$access[0]->object_id:'';
            $access=explode(',', $access);
            if(in_array(0, $access)){
                $allbuaccess=0;
                $costcenter = Cache::get('CostCenter_'.$allbuaccess);
            }else{
                $allbuaccess='';
                $costcenter = '';
            }

            if(empty($costcenter)){


            foreach($allBusinessUnits as $key=>$businessData){

                if($businessData['parent_bu_id'] == 0){
                    $data = $this->getLeWhByBu($businessData['bu_id']);
                    if($data>0){             
                        $parentWiseArr[count($parentWiseArr)]="<option value='".$businessData['bu_id']."' class='bu1' >".$businessData['bu_name']."</option>";
                    }
                    unset($allBusinessUnits[$key]);
                    $child = $this->getNextBusinessChild($businessData['bu_id'], $allBusinessUnits,2);

                    if(!empty($child)){
                        foreach ($child as $key => $value) {                          
                            foreach ($value as $keyIndex => $val) {
                                $parentWiseArr[count($parentWiseArr)]=$val;
                            }
                        }
                    }
                }else{
                    $data = $this->getLeWhByBu($businessData['bu_id']);
                    if($data>0){
                        if(!in_array($businessData['bu_id'], $this->tempArray)){
                            $this->tempArray[]=$businessData['bu_id'];             
                            $parentWiseArr[count($parentWiseArr)]="<option value='".$businessData['bu_id']."' class='bu1' >".$businessData['bu_name']."</option>";
                        }
                    }
                    unset($allBusinessUnits[$key]);
                    $child = $this->getNextBusinessChild($businessData['bu_id'], $allBusinessUnits,2);
                    if(!empty($child)){
                        foreach ($child as $key => $value) {                          
                            foreach ($value as $keyIndex => $val) {
                                $parentWiseArr[count($parentWiseArr)]=$val;
                            }
                        }
                    }
                }
            }
            $parentWiseArr = array_unique($parentWiseArr);
        }else{
            $parentWiseArr = $costcenter; 
        }

        if($allbuaccess==0 && empty($costcenter)){ 
                Cache::put('CostCenter_'.$allbuaccess,$parentWiseArr,60*24*60);
        }

            return $parentWiseArr;
        
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }
    public function getNextBusinessChild($catId, $businessArr,$level){

        $collectChild = array();

        $temp = array();
        if(!empty($businessArr)){
            foreach($businessArr as $key=>$value){
                if($value['parent_bu_id']==$catId){
                    unset($temp);
                    $temp=array();                   
                    $data = $this->getLeWhByBu($value['bu_id']);
                    if($data>0){
                        if(!in_array($value['bu_id'], $this->tempArray)){
                            $this->tempArray[]=$value['bu_id'];
                            $temp[] ="<option value='".$value['bu_id']."' class='bu".$level."' >".$value['bu_name']."</option>";
                        }
                    }  
                    unset($businessArr[$key]);
                    $child = $this->getNextBusinessChild($value['bu_id'], $businessArr,$level+1);
                    if(!empty($child)){
                        foreach ($child as $keyIndex => $value) {
                            
                            foreach ($value as $key => $val) {
                                $temp[count($temp)] = $val;
                            }
                        }
                    }
                    $collectChild[] = $temp; 
                }
            } 
        }
        else{
            return $collectChild;
        }
        return $collectChild;
    }
    public function getLeWhByBu($bu_id){
        /*$data =  DB::select(DB::raw("select count(*) as count FROM legalentity_warehouses WHERE dc_type=118001 and status=1 and bu_id=".$bu_id));
        $data = json_decode(json_encode($data),1);        
        return $data[0]['count'];*/
        $data = DB::select("call getBuHierarchy_proc($bu_id,@le_wh_ids)");
        $data =DB::select(DB::raw('select @le_wh_ids as wh_list'));
        if(!empty($data[0]->wh_list)){
            //log::info('budatacheck');
            //log::info($data);
            $result = explode(',',$data[0]->wh_list);
            return count($result);
        }
        return 0;
    }
     public function allBusinessUnits($userId=null){
        if(empty($userId)){
         $userId = Session::get('userId');
        }else{
          $userId =$userId;
        }
        $rawQuery = " SELECT GROUP_CONCAT(object_id) as object_id FROM `user_permssion` WHERE `user_id`=$userId and permission_level_id=6";
        $access =  DB::select(DB::raw($rawQuery));
        $access = isset($access[0]->object_id)?$access[0]->object_id:'';
        if($access==''){
            return json_encode(array());
        }
        $access = explode(',', $access);
        $data = [];
        if(in_array(0, $access)){
            $query1 = "SELECT `bu_name`, `bu_id`, `parent_bu_id`, `description`, `is_active`, `cost_center` FROM `business_units`";
            $data = DB::select(DB::raw($query1));
            $bu_id_exist = array_column(json_decode(json_encode($data),1), 'bu_id');
        }else{
            $businesData = DB::table("business_units")
                        ->select(['bu_name', 'bu_id', 'parent_bu_id', 'description', 'is_active', 'cost_center'])
                        ->whereIn('bu_id', $access)
                        ->get()->all();
            //Log::info($businesData);
            $bu_id_exist = [];
            $bu_id_exist = array_column(json_decode(json_encode($businesData),1), 'bu_id');
            $data = $businesData;
        }
        $data = json_decode(json_encode($data),1);
        return json_encode($data);
    }
    public function getAllWarehouseDataByAccess($userId=null){
        if(empty($userId)){
         $userId = Session::get('userId');
        }else{
          $userId =$userId;
        }
        $rawQuery = " SELECT GROUP_CONCAT(object_id) as object_id FROM `user_permssion` WHERE `user_id`=$userId and permission_level_id=6";
        $access =  DB::select(DB::raw($rawQuery));
        $access = isset($access[0]->object_id)?$access[0]->object_id:0;
        $access = explode(',', $access);
        $data = [];
        $query = DB::table("business_units as bu")
                        ->join('legalentity_warehouses as lw','lw.bu_id','=','bu.bu_id')
                        ->select(['lw.le_wh_id',DB::raw('lw.display_name as dcname'),'bu_name', 'bu.bu_id','bu.parent_bu_id', 'bu.description', 'bu.is_active', 'bu.cost_center'])
                        ->where('lw.dc_type','118001');
        if(!in_array(0, $access)){
            $query = $query->whereIn('bu.bu_id', $access);
        }
        $businesData = $query->get()->all();
        return $businesData;
    }

     //used to get DCFC Details
     public function getDcFCTreeData(){
        try{
            //from session fetching use
                       $userId=Session::get('userId');
            $roleObj = new Role();
            $Json = json_decode($roleObj->getFilterData(6,$userId), 1);
            $filters = json_decode($Json['sbu'], 1);            
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $data =DB::select(DB::raw("select * FROM legalentity_warehouses AS lw INNER JOIN zone AS z ON lw.state = z.zone_id WHERE lw.dc_type=118001 AND lw.status=1 AND lw.le_wh_id IN (".$dc_acess_list.")"));
            if(count($data)>0){
                return $data;
            }else{
                return array();
            }
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }

    /**
     * Get the legal entity warehouse ID
     * @param  int $bu  Business unit ID
     * @return int    Legal entity warehouse ID
     */
    public function getWarehouseID($bu){
        $data =DB::select(DB::raw('SELECT le_wh_id FROM legalentity_warehouses WHERE bu_id='.$bu));
        if(count($data)>0){
            $result[0] = $data[0]->le_wh_id;
            return $result;
        }
    }

    public function getWhByData($bu){
        $data = DB::select("call getBuHierarchy_proc($bu,@le_wh_ids)");
        $data =DB::select(DB::raw('select @le_wh_ids as wh_list'));
        if(count($data)>0){
            $result = explode(',',$data[0]->wh_list);
            return $result;
        }
        return array();
    }
    
    public function getWhareHouseTypeId($le_wh_id){
        $query = DB::table('legalentity_warehouses as lw')
                ->select('le.legal_entity_type_id')
                ->leftJoin('legal_entities as le','le.legal_entity_id','=','lw.legal_entity_id')
                ->where('lw.le_wh_id','=',$le_wh_id)
                ->first();
        return $query;        
    }

    public function checkInvNegativeAgainstOrderQty($WhId,$productId,$excess,$dndditsum){
        try{
            $getinventory_table_values = $this->getInventoryDetailsBasedOnProductId($productId, $WhId);
            $inventory_table_soh        = $getinventory_table_values[0]['soh'];
            $inventory_table_Order_qty  = $getinventory_table_values[0]['order_qty'];
            $resulted_SOH               = $inventory_table_soh+($excess)-$dndditsum;
            log::info($productId.'======================tablesoh'.$inventory_table_soh.'========excess '.$excess);
log::info($productId.'======================tableordersoh'.$inventory_table_Order_qty.'========resultedsoh '.$resulted_SOH);
            if($resulted_SOH < $inventory_table_Order_qty)
            {

                $diff_soh=$inventory_table_Order_qty-$resulted_SOH;

                $productstodeletefromorders=$this->skuWithNegativeSOHByWarehouse($WhId,$productId,$diff_soh);
                $msg= $productstodeletefromorders;
                return array('status'=>400,'message'=>$msg);
            }
            return array('status'=>200,'message'=>'');                     
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $productsku=DB::table('products')->select('sku')->where('product_id',$productId)->first();
            $warehouse=DB::table('legalentity_warehouses')->select('display_name')->where('le_wh_id',$WhId)->first();
            return array('status'=>400,'message'=>'No Proper Information is seen for Product '. $productsku->sku. ' in '. $warehouse->display_name.' warehouse');
        }
    }

    public function skuWithNegativeSOHByWarehouse($WhId,$productId,$diff_soh,$str='',$orderarray=array()){
        try{
            if($str==''){
                $str='<table>
                        <tr>
                        <th>SKU</th>
                        <th>Comment</th>
                        </tr>';
            }
            if($diff_soh>0){
                    $getorderIdsquery="select go.gds_order_id as gds_order_id,go.order_code as order_code,gop.qty as qty,getSkuById(gop.product_id) as sku,sum(goc.`qty`) AS cancelqty,gr.qty AS returnqty,go.order_status_id as order_status_id,gii.qty as invqty,gr.return_status_id as return_status_id from gds_orders go join gds_order_products gop on go.gds_order_id=gop.gds_order_id
                        LEFT JOIN gds_cancel_grid gcg ON gcg.`gds_order_id`=go.`gds_order_id`
                        LEFT JOIN gds_order_cancel goc ON goc.`cancel_grid_id`=gcg.`cancel_grid_id` AND gop.`product_id`=goc.`product_id` 
                        LEFT JOIN gds_returns gr ON gr.gds_order_id=go.`gds_order_id` AND gr.`product_id`=gop.`product_id`
                        LEFT JOIN gds_invoice_items gii on go.gds_order_id=gii.gds_order_id AND gii.`product_id`=gop.`product_id`  
                        where le_wh_id=".$WhId." and gop.product_id=".$productId." and go.order_status_id in (17001,17020,17005,17022,17023) and (gop.order_status in (17001,17020,17005,17013) OR gr.return_status_id NOT IN (1,57066) and gop.order_status NOT IN (17007)) ";
                    if(count($orderarray)>0){
                        $gdsorderids=implode(',', $orderarray);
                        $getorderIdsquery.=" and gop.gds_order_id not in (".$gdsorderids.")";    
                    }
                    
                     $getorderIdsquery.="  group by gop.gds_order_id order by gop.gds_order_prod_id desc  limit 1";
                     log::info($getorderIdsquery);
                    $getorderIds=DB::selectFromWriteConnection($getorderIdsquery);
                    $productsoh = isset($getorderIds[0]->qty)?$getorderIds[0]->qty:0;
                    $cancelqty = (isset($getorderIds[0]->cancelqty) && $getorderIds[0]->cancelqty!='')?$getorderIds[0]->cancelqty:0;
                    $returnqty = (isset($getorderIds[0]->returnqty) && $getorderIds[0]->returnqty!='')?$getorderIds[0]->returnqty:0;
                    $orderarray[]= isset($getorderIds[0]->gds_order_id)?$getorderIds[0]->gds_order_id:0;
                    $invqty = (isset($getorderIds[0]->invqty) && $getorderIds[0]->invqty!='')?$getorderIds[0]->invqty:0;
                    log::info($diff_soh);
                    
                    if(isset($getorderIds[0]->return_status_id) && $getorderIds[0]->return_status_id!=1 && $getorderIds[0]->return_status_id!=''){

                        $diff_soh = $diff_soh-$returnqty;//subtracting diffsoh from return qty and getting final differnce soh
                        
                        $productsoh  = $returnqty;
                        if($diff_soh<0){
                            //if final soh is negative we are adding difference soh(which is negative)  to return qty so that we can get number of skus to be approved or to be cancelled for inv adjustment
                            $productsoh=$returnqty+$diff_soh;
                        }
                        
                    }elseif($cancelqty!='' && $cancelqty!=0){
                        $prdqtyaftercancel=$productsoh-$cancelqty-$returnqty;
                        $diff_soh = $diff_soh-$prdqtyaftercancel;
                        $productsoh=$productsoh-$cancelqty;
                        if($diff_soh<0){   // || $diff_soh==0
                            //to get actual sku count from which qty to be subtracted we remove cancel qty from product soh  so that we can get the qty from which skus to be cancelled for inv adjustment
                            $productsoh=$productsoh+$diff_soh;
                        }/*else{
                            //$productsoh  = $diff_soh;
                            $productsoh  = $productsoh;
                        }*/
                    }else{
                        $diff_soh = $diff_soh-$productsoh;
                        if($diff_soh<0){
                            $productsoh  = $productsoh+$diff_soh;//$diff_soh;
                        }else{
                            $productsoh  =$productsoh;
                        }
                    }
                    
                    if(isset($getorderIds[0])){
                        $str.='
                            <tr>
                                <td>'.$getorderIds[0]->sku.'</td>
                                <td> This product has '.$productsoh.'  excess qty in '.$getorderIds[0]->order_code.'. Cancel/Approve Returns for this excess quantity before proceeding </td>
                            </tr>';
                    }else{
                        $str.='<tr><td colspan=2>No Orders Found in Open/Picklist/rtd/returns for  product ID'.$productId.' to delete sku differene between stock on hand and ordered qty</td></tr>';
                    }
                    if($diff_soh>0 && isset($getorderIds[0])){
                       return $this->skuWithNegativeSOHByWarehouse($WhId,$productId,$diff_soh,$str,$orderarray);
                          
                    }else{
                        $str.='</table>';
                        return $str;                        
                    }

            }
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            //return array('status'=>400,'message'=>'No Proper Information is seen for product'. $productId);
            $productsku=DB::table('products')->select('sku')->where('product_id',$productId)->first();
            $warehouse=DB::table('legalentity_warehouses')->select('display_name')->where('le_wh_id',$WhId)->first();
            return array('status'=>400,'message'=>'No Proper Information is seen for Product '. $productsku->sku. ' in '. $warehouse->display_name.' warehouse');
        }
    }

    public function getBatchHistoryList($makeFinalSql, $orderBy, $page, $pageSize){

       if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }

        $sqlWhrCls = '';
        $sqldateCls = '';
        $countLoop = 0;

        
        foreach ($makeFinalSql as $value) {

            if(substr_count($value,'fromdate') || substr_count($value,'todate')){
                $data = explode('=',$value);           
                $date = isset($data[1])?trim($data[1]):'';
                log::info($date);
                if($sqldateCls=='' && substr_count($value,'fromdate')){
                  $date=str_replace("/","-",$date);

                  $sqldateCls = ' created_at between "'.date('Y-m-d', strtotime($date)).'"';
                }else{
                  $date=str_replace("/","-",$date);
                  $sqldateCls .= ' and "'.date('Y-m-d', strtotime($date)).' 23:59:59"';
                }
            }elseif( $countLoop==0 ){
                    $sqlWhrCls .=  $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
                $countLoop++;
            }

        /*$sqlQuery=DB::table('gds_orders_batch as gob')
                        ->select(DB::RAW('getLeWhName(gob.le_wh_id) AS display_name'),
                            'p.product_title AS SKU','ord_qty','inv_qty','gob.esp','gob.elp',
                            'gob.created_at','gob.main_batch_id','le.business_legal_name',
                            'rf.legal_entity_type',DB::raw('DATE_FORMAT(ipd.mfg_date, "%d-%m-%Y") as mfg_date'),'ipd.exp_date')
                        ->join('gds_orders as go','gob.gds_order_id','=','go.gds_order_id')
                        ->join('products as p','p.product_id','=','gob.product_id')
                        ->join('legal_entities as le','le.legal_entity_id','=','go.cust_le_id')
                        ->join('retailer_flat as rf','rf.legal_entity_id','=','le.legal_entity_id');
        // $sqlQuery->join('inventory_batch as ib',function($join){
        //     $join->on('ib.main_batch_id','=','gob.main_batch_id')
        //         ->on('ib.le_wh_id','=','gob.le_wh_id')
        //         ->on('ib.product_id','=','gob.product_id')
        //         ->on('gob.inward_id','=','ib.inward_id');
        // });
        $sqlQuery->join('inward_products as ip',function($join){
            $join->on('ip.product_id','=','gob.product_id')
                ->on('ip.inward_id','=','gob.inward_id');
        });
        $sqlQuery->join('inward_product_details as ipd',function($join){
            $join->on('ipd.product_id','=','ip.product_id')
                ->on('ipd.inward_prd_id','=','ip.inward_prd_id');
        });*/
        $sqlQuery=DB::table('vw_batch_history');
         if($sqlWhrCls!='')
         {
            
              $sqlQuery=$sqlQuery->whereRaw($sqlWhrCls);
         }
         if($sqldateCls!='')
         {
            
              $sqlQuery=$sqlQuery->whereRaw($sqldateCls);
         }
          $result['TotalRecordsCount'] = $sqlQuery->count();     
       

        $sqlQuery->skip($page)->take($pageSize);

         $result['Records']=$sqlQuery->get()->all();


                        
       return json_decode(json_encode($result),true);


    }

    public function getBatchIdsBySKU($pid){
        try{
            $resultset=DB::table('gds_orders_batch')
                        ->selectRaw('distinct(main_batch_id) as main_batch_id')
                        ->where('product_id',$pid)
                        ->get()->all();
            return $resultset;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getBatchSkus($data)
    {
        try
        {
           $term = $data['term'];
           $legal_entity_id = \Session::get('legal_entity_id');
           $supplier_id = $data['supplier_id'];
           $warehouse_id = $data['warehouse_id'];
                $products = DB::table('products')
                //->where('tot.supplier_id',$supplier_id)
                //->where('tot.le_wh_id',$warehouse_id)
                //->where('tot.subscribe',1)
                //->where('products.is_sellable',1)
                ->where(function ($query) use($term) {
                    $query->orWhere('products.sku','like', '%'.$term.'%')
                          ->orWhere('products.product_title','like', '%'.$term.'%')
                          ->orWhere('products.upc','like', '%'.$term.'%');
                          //->orWhere('content.product_name','like', '%'.$term.'%');
                })
                ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                ->leftJoin('product_content as content','products.product_id','=','content.product_id')
                ->leftJoin('product_tot as tot','products.product_id','=','tot.product_id')
                ->select('products.product_id','products.product_title','products.upc','products.sku','products.pack_size','products.seller_sku','products.mrp','brands.brand_id','brands.brand_name')
                ->groupBy('tot.product_id')->get()->all();

            $prodAry = array();
            if(count($products)>0){
                foreach($products as $product){
                    $brand = $product->brand_name;
                    $product_name = $product->product_title.' ( '.$brand.' )';
                    $product_id = $product->product_id;
                    $product_title = $product->product_title;
                    $sku = $product->sku;
                    $mrp = ($product->mrp!='')?$product->mrp:0;
                    $prod_arr = array("label" => $product_name, "product_id" => $product_id, "product_title" => $product_title, "brand" => $brand, "sku" => $sku,'mrp'=>'Rs. '.$mrp);
                    array_push($prodAry, $prod_arr);
                }
            }else{
                $prod_arr = array("label" => 'No Result Found','value'=>'');
                array_push($prodAry, $prod_arr);
            }
            echo json_encode($prodAry);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function getBatchReport($data){

       /*$sqlQuery=DB::table('gds_orders_batch as gob')
                        ->select('gob.main_batch_id AS Batch_ID','p.product_title AS Product_Name',DB::RAW('getLeWhName(gob.le_wh_id) AS Display_Name'),
                            'le.business_legal_name AS Business_Legal_Name','ord_qty AS Order_Qty','inv_qty as Inv_Qty','gob.esp as ESP','gob.elp AS ELP',
                            'gob.created_at as Created_At',
                            'rf.legal_entity_type as Type','ipd.mfg_date as Mfg_Date','ipd.exp_date as Exp_Date')
                        ->join('gds_orders as go','gob.gds_order_id','=','go.gds_order_id')
                        ->join('products as p','p.product_id','=','gob.product_id')
                        ->join('legal_entities as le','le.legal_entity_id','=','go.cust_le_id')
                        ->join('retailer_flat as rf','rf.legal_entity_id','=','le.legal_entity_id');
        // $sqlQuery->join('inventory_batch as ib',function($join){
        //     $join->on('ib.main_batch_id','=','gob.main_batch_id')
        //         ->on('ib.le_wh_id','=','gob.le_wh_id')
        //         ->on('ib.product_id','=','gob.product_id')
        //         ->on('gob.inward_id','=','ib.inward_id');
        // });
        $sqlQuery->join('inward_products as ip',function($join){
            $join->on('ip.product_id','=','gob.product_id')
                ->on('ip.inward_id','=','gob.inward_id');
        });
        $sqlQuery->join('inward_product_details as ipd',function($join){
            $join->on('ipd.product_id','=','ip.product_id')
                ->on('ipd.inward_prd_id','=','ip.inward_prd_id');
        });*/
        //$sqlQuery=DB::table('vw_batch_report');
        if(!isset($data['addproduct_id']) || $data['addproduct_id']=='')
        {
            $productID='NULL';
        }else{
            $productID=$data['addproduct_id'];
        }

        if(!isset($data['main_batch_idlist']) || $data['main_batch_idlist']==''  || $data['main_batch_idlist']==' ')
        {
            $Batch_ID='NULL';
        }else{
            $Batch_ID=$data['main_batch_idlist'];
        }

        if(!isset($data['fromdate']) || $data['fromdate']=='' || !isset($data['todate']) || $data['todate']=='')
        {
            $fromDate='NULL';
            $toDate  ='NULL';
        }else{
            $data['fromdate']=date('Y-m-d',strtotime($data['fromdate']));
            $data['todate']  =date('Y-m-d',strtotime($data['todate']));
            $fromDate="'".$data['fromdate']."'";
            $toDate  ="'".$data['todate']."'";
        }
        //echo "CALL getBatchReport(".$fromDate.",".$toDate.",".$Batch_ID.",".$productID.")";exit;
        return $query = DB::select(DB::raw("CALL getBatchReport(".$fromDate.",".$toDate.",".$Batch_ID.",".$productID.")"));        

    }

}

