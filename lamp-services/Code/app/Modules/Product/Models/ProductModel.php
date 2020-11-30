<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
 use App\Modules\Supplier\Models\SupplierModel;
 use App\Modules\PurchaseOrder\Models\PurchaseOrder;
 use App\Modules\LegalEntities\Models\Legalentity;
 use App\Modules\Grn\Models\Inward;
 use App\Modules\PurchaseReturn\Models\PurchaseReturn;
 use App\Modules\Manufacturers\Models\VwProductsByWarehouseModel;
 use App\Modules\Manufacturers\Models\VwProductsByAllWarehousesModel;
use App\Central\Repositories\RoleRepo;
use App\Modules\Roles\Models\Role;
use DB;
use Session;
class ProductModel extends Model
{
    public $timestamps = true;
    protected $fillable = ['product_id','legal_entity_id','supplier_id','product_name','product_group_id','primary_image','product_type_id','category_id','business_unit_id','is_gds_enabled','brand_id','weight_uom','weight','lbh_uom','length','breadth','height','product_uom','upc','upc_type','tax_class_id','is_active','date_added','created_by','date_modified','modified_by','sku','seller_sku','is_deleted','is_traceable','moq','is_heavy_weight','no_of_units','is_parent'];
	protected $table = "products";
	protected $primaryKey = 'product_id';


	public function brands()
    {
        return $this->hasOne('App\Modules\Supplier\Models\BrandModel', 'brand_id', 'brand_id');
    }

    public function tot() {
        return $this->hasOne('App\Modules\Supplier\Models\TotModel', 'product_id', 'product_id');
    }

    public function approvalSave($approvalTypeId, $approvalForId) {
        switch ($approvalTypeId) {
            case 'Product PIM':
                $status = ProductModel::select('status')->where('product_id', $approvalForId)->get()->all();
                /*if ($status[0]->status == '57001' || $status[0]->status == 0 || $status[0]->status == null) {
                    $status_code = 'drafted';
                } else {*/
                    $status_code = $status[0]->status;
//                }
                break;
            case 'Supplier':
                $status = SupplierModel::select('status')->where('legal_entity_id', $approvalForId)->get()->all();
                if ($status[0]->status == '57001' || $status[0]->status == 0 || $status[0]->status == null) {
                    $status_code = 'drafted';
                } else {
                    $status_code = $status[0]->status;
                }
                break;
            case 'Purchase Order':
                $status = PurchaseOrder::select('approval_status')->where('po_id', $approvalForId)->get()->all();
                if ($status[0]->status == '57001' || $status[0]->status == 0 || $status[0]->status == null) {
                    $status_code = 'drafted';
                } else {
                    $status_code = $status[0]->status;
                }
                break;
            case 'Retailer':
                $status = Legalentity::select('status_id')->where('legal_entity_id', $approvalForId)->get()->all();
                if ($status[0]->status == '57001' || $status[0]->status == 0 || $status[0]->status == null) {
                    $status_code = 'drafted';
                } else {
                    $status_code = $status[0]->status;
                }
                break;
            case 'GRN':
                $status = Inward::select('approval_status')->where('inward_id', $approvalForId)->get()->all();
                if ($status[0]->status == '57001' || $status[0]->status == 0 || $status[0]->status == null) {
                    $status_code = 'drafted';
                } else {
                    $status_code = $status[0]->status;
                }
                break;
            case 'Purchase Return':
                $status = PurchaseReturn::select('approval_status')->where('pr_id', $approvalForId)->get()->all();
                if ($status[0]->status == '57001' || $status[0]->status == 0 || $status[0]->status == null) {
                    $status_code = 'drafted';
                } else {
                    $status_code = $status[0]->status;
                }
                break;
        }
        return $status_code;
    }


	//all product download
    public function downloadAllProductInfo($leid='') {

        try{
            if(!empty($leid)){
            $legalentity_id =$leid;
            }else{
            $legalentity_id = Session()->get('legal_entity_id');
            }

            $pim_data = DB::table('template_config_allproducts')->where(array('template_id' => 1, 'is_active' => 1))->orderBy('sort_order', 'asc')->pluck('Label')->all();

        


        $Table_Column_Lookup = DB::table('template_config_allproducts')->select(array('read_col_name', 'read_object_name', 'Label'))->where(array('template_id' => 1, 'is_active' => 1))->orderBy('sort_order', 'asc')->get()->all();

        $Table_Column_Lookup = json_decode(json_encode($Table_Column_Lookup), 1);

/*        $lookup_options = array();
        $lookupObj = new MasterLookup();
        $brandObj = new BrandModel();
        $Length_UOM = $lookupObj->getLengthUOM();
        $Capacity_UOM = $lookupObj->getCapacityUOM();
        $PackSize_UOM = $lookupObj->getPackSizeUOM();*/

        $legalEntityIdArray = array();
        $child_legal_entity_id = DB::table('legal_entities')->select('legal_entity_id')->where(['parent_id' => $legalentity_id, 'legal_entity_type_id' => '1006'])->get()->all();
        foreach ($child_legal_entity_id as $val) {
            $legalEntityIdArray[] = $val->legal_entity_id;
        }

        /*$legalEntityIdArray = array($legalentity_id);
        $Brands = $brandObj->getBrandsBySupplierId($legalEntityIdArray);
        $KVI_Lookup = $lookupObj->getKVI();
        $Pack_Type_Lookup = $lookupObj->getPackType();
        $Shelf_Life_Lookup = $lookupObj->getShelfLife();
        $Product_Form_Lookup = $lookupObj->getProductForm();
        $License_Type_Lookup = $lookupObj->getLicenseType();
        $Preffered_Channels_Lookup = $lookupObj->getPrefferedChannels();
        $Popularity_Lookup = $lookupObj->getPopularity();
        $Eaches_Lookup = $lookupObj->getEachesLookup();
        $Offer_Pack_Lookup = $lookupObj->getOfferPackLookup();
        $Product_Code_Type_Lookup = array('UPC', 'EAN');

        $check_data = array('yes' => 1, 'no' => 0, 'y' => 1, 'n' => 0);
        $retrieve_data = array(1 => 'yes', 0 => 'no');

        $array_count = array(
            'Length_UOM' => count($Length_UOM),
            'Offer_Pack' => count($Offer_Pack_Lookup),
            'Capacity_UOM' => count($Capacity_UOM),
            'Brands' => count($Brands),
            'KVI' => count($KVI_Lookup),
            'Pack_Type' => count($Pack_Type_Lookup),
            'Pack Size UOM' => count($PackSize_UOM),
            'Product Code Type' => count($Product_Code_Type_Lookup),
            'Shelf_Life' => count($Shelf_Life_Lookup),
            'Product_Form' => count($Product_Form_Lookup),
            'License_Type' => count($License_Type_Lookup),
            'Preffered_Channels' => count($Preffered_Channels_Lookup),
            'Popularity' => count($Popularity_Lookup),
            'Level' => count($Eaches_Lookup)
        );*/


        $default_attributes_heading = DB::table('attribute_set_mapping')
                ->join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id')
                ->join('attribute_sets', 'attribute_set_mapping.attribute_set_id', '=', 'attribute_sets.attribute_set_id')
                ->where('attributes.attribute_type', '=', 2)
                ->orderBy('attribute_set_mapping.sort_order', 'asc')
                ->groupBy('name')
                ->pluck('name')->all();


        $other_attributes_heading = DB::table('attribute_set_mapping')
                ->join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id')
                ->join('attribute_sets', 'attribute_set_mapping.attribute_set_id', '=', 'attribute_sets.attribute_set_id')
                ->where('attributes.attribute_type', '!=', 2)
                ->orderBy('attribute_set_mapping.sort_order', 'asc')
                ->groupBy('name')
                ->pluck('name')->all();

        foreach ($Table_Column_Lookup as $Table_Name => $Table_Lookup) {
            if ($Table_Lookup['read_object_name'] == 'attributes') {



                $key = array_search($Table_Lookup['Label'], $default_attributes_heading);
                if ($key !== false) {
                    
                    unset($default_attributes_heading[$key]);
                }
            }
        }

        $pim_data = array_merge($pim_data, $default_attributes_heading);
        $pim_data = array_merge($pim_data, $other_attributes_heading);

        $data['cat_data'] = array(
            $pim_data
        );



            $products_query = DB::table('vw_products_pim')->select('*')->where(['legal_entity_id' => $legalentity_id]);

            $products = $products_query->get()->all();
            $product_data = json_decode(json_encode($products), 1);
            $product_count = 0;


            foreach ($product_data as $product) {
                $product_count++;
                $product['mrp'] = (float)number_format($product['mrp'],2,'.','');
                $product['esu'] = (float)number_format($product['esu'],2,'.','');
                $product['shelf_life'] = (float)number_format($product['shelf_life'],2,'.','');
                //$product['Parent'] = (float)number_format($product['Parent'],2,'.','');
                //$product['product_id'] = (float)number_format($product['product_id'],2,'.','');

                $product_id = $product['product_id'];

                if ($product['pack_size_uom'] != '') {
                    $pack_size_uom = DB::table('master_lookup')->select('description')
                            ->where('master_lookup_name', $product['pack_size_uom'])
                            ->first();
                } else {
                    $pack_size_uom = '';
                }

                $product_parent = ProductRelations::where('product_id', $product['product_id'])->first();
                $parent_id = '';
                if (count($product_parent) > 0 && isset($product_parent->parent_id)) {
                    $parent_id = ($product_parent->parent_id != '') ? $product_parent->parent_id : '';
                }
                $iss_markup = '';

                $perishable = (isset($retrieve_data[strtolower($product['perishable'])])) ? $retrieve_data[strtolower($product['perishable'])] : '';

                $flammable = (isset($retrieve_data[strtolower($product['flammable'])])) ? $retrieve_data[strtolower($product['flammable'])] : '';
                $hazardous = (isset($retrieve_data[strtolower($product['hazardous'])])) ? $retrieve_data[strtolower($product['hazardous'])] : '';
                $odour = (isset($retrieve_data[strtolower($product['odour'])])) ? $retrieve_data[strtolower($product['odour'])] : '';
                $fragile = (isset($retrieve_data[strtolower($product['fragile'])])) ? $retrieve_data[strtolower($product['fragile'])] : '';
                $licence_required = (isset($retrieve_data[strtolower($product['licence_req'])])) ? $retrieve_data[strtolower($product['licence_req'])] : '';

//DB::enableQueryLog();
                $prod = array();
                $other_attributes = DB::table('attribute_set_mapping')
                        ->join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id')
                        ->join('attribute_sets', 'attribute_set_mapping.attribute_set_id', '=', 'attribute_sets.attribute_set_id')
                        ->leftjoin('product_attributes', function($join) use($product_id) {
                            $join->on('product_attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id');
                            $join->on('product_attributes.product_id', '=', DB::raw($product_id));
                        })
                        ->where('attribute_sets.category_id', $product['category_id'])
                        ->where('attributes.attribute_type', '!=', 2)
                        ->orderBy('attribute_set_mapping.sort_order', 'asc')
                        ->pluck('product_attributes.value', 'name')->all();

                $default_attributes = DB::table('attribute_set_mapping')
                        ->join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id')
                        ->join('attribute_sets', 'attribute_set_mapping.attribute_set_id', '=', 'attribute_sets.attribute_set_id')
                        ->leftjoin('product_attributes', function($join) use($product_id) {
                            $join->on('product_attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id');
                            $join->on('product_attributes.product_id', '=', DB::raw($product_id));
                        })
                        ->where(array('attribute_sets.category_id' => $product['category_id'], 'attributes.attribute_type' => 2))
                        ->where('attributes.attribute_type', 2)
                        ->orderBy('attribute_set_mapping.sort_order', 'asc')
                        ->pluck('product_attributes.value', 'name')->all();


                $Image = ProductMedia::where(array('product_id' => $product_id))->pluck('url')->all();

                $Image = json_decode(json_encode($Image), 1);

                $Image_Array = array();

                $Image_Array['primary_image'] = $product['primary_image'];

                foreach ($Image as $Image_Temp) {
                    $Image_Array['img' . count($Image_Array) . '_url'] = $Image_Temp;
                }

                $levelSortOrder = DB::table('master_lookup')->where('mas_cat_id',16)->orderBy('sort_order','asc')->pluck('value')->all();   
                $levelSortOrderIds = implode(',', $levelSortOrder);
                $Product_Pack_Config = DB::table('product_pack_config')
                          ->where('product_id', $product_id)
                          ->orderByRaw(DB::raw("FIELD(level, $levelSortOrderIds)"))->get()->all();

                $Product_Pack_Config = json_decode(json_encode($Product_Pack_Config), 1);

                $Product_Pack_Array = array();
                foreach ($Product_Pack_Config as $key => $Pack_Config_Temp) {

                    if ($Pack_Config_Temp['pack_code_type'] != '') {
                        $pack_code_type = DB::table('master_lookup')->select('description')
                                ->where('value', $Pack_Config_Temp['pack_code_type'])
                                ->first();
                    } else {
                        $pack_code_type = '';
                    }

                    if ($Pack_Config_Temp['weight_uom'] != '') {
                        $weight_uom = DB::table('master_lookup')->select('description')
                                ->where('value', $Pack_Config_Temp['weight_uom'])
                                ->first();
                    } else {
                        $weight_uom = '';
                    }


                    if ($Pack_Config_Temp['level'] != '') {
                        $level = DB::table('master_lookup')->select('master_lookup_name')
                                ->where('value', $Pack_Config_Temp['level'])
                                ->first();
                    } else {
                        $level = '';
                    }

                    $palletization = ($Pack_Config_Temp['palletization'] == 0) ? 'No' : 'Yes';

                    $is_sellable = ($Pack_Config_Temp['is_sellable'] == 0) ? 'No' : 'Yes';
                    ;
                    $pack_level_esu =  (isset($Pack_Config_Temp['esu'])) ? (float)number_format($Pack_Config_Temp['esu'],2,'.','') : '';
                    $Product_Pack_Array['l' . ($key + 1) . '_level'] = (isset($level->master_lookup_name)) ? $level->master_lookup_name : '';
                    $Product_Pack_Array['l' . ($key + 1) . '_product_code'] = $Pack_Config_Temp['pack_sku_code'];
                    $Product_Pack_Array['l' . ($key + 1) . '_esu'] = $pack_level_esu;
                    $Product_Pack_Array['l' . ($key + 1) . '_product_code_type'] = (isset($pack_code_type->description)) ? $pack_code_type->description : '';
                    $Product_Pack_Array['l' . ($key + 1) . '_length'] = (float)number_format($Pack_Config_Temp['length'],2,'.','');
                    $Product_Pack_Array['l' . ($key + 1) . '_breadth'] = (float)number_format($Pack_Config_Temp['breadth'],2,'.','');
                    $Product_Pack_Array['l' . ($key + 1) . '_height'] = (float)number_format($Pack_Config_Temp['height'],2,'.','');
                    $Product_Pack_Array['l' . ($key + 1) . '_weight'] = (float)number_format($Pack_Config_Temp['weight'],2,'.','');
                    $Product_Pack_Array['l' . ($key + 1) . '_weight_uom'] = (isset($weight_uom->description)) ? $weight_uom->description : '';
                    ;
                    $Product_Pack_Array['l' . ($key + 1) . '_issealable'] = $is_sellable;
                    $Product_Pack_Array['l' . ($key + 1) . '_palletizable'] = $palletization;
                    $Product_Pack_Array['l' . ($key + 1) . '_packing_material'] = $Pack_Config_Temp['pack_material'];
                    $Product_Pack_Array['l' . ($key + 1) . '_no_of_eaches'] = (float)number_format($Pack_Config_Temp['no_of_eaches'],2,'.','');
                    $Product_Pack_Array['l' . ($key + 1) . '_stack_height'] = (float)number_format($Pack_Config_Temp['stack_height'],2,'.','');
                    $Product_Pack_Array['l' . ($key + 1) . '_no_of_inners'] = (float)number_format($Pack_Config_Temp['inner_pack_count'],2,'.',''); 
                }


                foreach ($Table_Column_Lookup as $Table_Name => $Table_Lookup) {

                    if ($Table_Lookup['read_object_name'] == 'vw_products_pim') {


                            $prod[$Table_Lookup['Label']] = $product[$Table_Lookup['read_col_name']];
                    } else if ($Table_Lookup['read_object_name'] == 'attributes') {

                        /*                                  if(array_key_exists($Table_Lookup['Label'],$default_attributes))
                          { */

                        if (array_key_exists($Table_Lookup['Label'], $default_attributes)) {
                            $prod[$Table_Lookup['Label']] = $default_attributes[$Table_Lookup['Label']];

                            unset($default_attributes[$Table_Lookup['Label']]);
                        } else {
                            $prod[$Table_Lookup['Label']] = '';
                        }
                        /*                                  }
                          if(array_key_exists($Table_Lookup['Label'],$other_attributes))
                          {
                          unset($other_attributes[$Varient_Array[$Table_Lookup['Label']]]);
                          $prod[$Table_Lookup['Label']] = $other_attributes[$Table_Lookup['read_col_name']];
                          } */
                    } else if ($Table_Lookup['read_object_name'] == 'image') {
                        $prod[$Table_Lookup['Label']] = (isset($Image_Array[$Table_Lookup['read_col_name']])) ? $Image_Array[$Table_Lookup['read_col_name']] : '';
                    } else if ($Table_Lookup['read_object_name'] == 'product_pack_config') {
                        $prod[$Table_Lookup['Label']] = (isset($Product_Pack_Array[$Table_Lookup['read_col_name']])) ? $Product_Pack_Array[$Table_Lookup['read_col_name']] : '';
                    }
                }


                foreach($default_attributes_heading as $temp_default_attribute) {


                    if(array_key_exists($temp_default_attribute,$default_attributes)) {

                        $prod[] = $default_attributes[$temp_default_attribute];                        
                    } else {
                        $prod[] = '';
                    }
                }

                foreach($other_attributes_heading as $temp_other_attribute) {


                    if(array_key_exists($temp_other_attribute,$other_attributes)) {

                        $prod[] = $other_attributes[$temp_other_attribute];                        
                    } else {
                        $prod[] = '';
                    }
                }



                if ($product_count == 1) {

                    if (isset($key_remove) && is_array($key_remove)) {
                        foreach ($key_remove as $key) {
                            unset($data['cat_data'][2][$key]);
                            unset($data['cat_data'][1][$key]);
                        }
                    }
                }


                $data['cat_data'][] = $prod;
            }
            return $data;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }
public function getBinConfigValues($whId)
{

    if($whId)
    {
    $data = DB::table('product_bin_config')
                ->join('products','product_bin_config.prod_group_id','=','products.product_group_id')
                ->select('products.product_id as ProductID',DB::Raw('getBinDimById(bin_type_dim_id) as BinType'),DB::Raw('getMastLookupValue(pack_conf_id) as PackType'),'min_qty as MinCapacity',
                        'max_qty as MaxCapacity')
                ->where('product_bin_config.wh_id',$whId)->get()->all();
    }
    else {
    $data = DB::table('product_bin_config')
                ->join('products','product_bin_config.prod_group_id','=','products.product_group_id')
                ->select('products.product_id as ProductID',DB::Raw('getBinDimById(bin_type_dim_id) as BinType'),DB::Raw('getMastLookupValue(pack_conf_id) as PackType'),'min_qty as MinCapacity',
                        'max_qty as MaxCapacity')->get()->all();       
    }
    return json_decode(json_encode($data),1);
}
        public function checkBinGroupedProduct($product_id)
        {
             $getPid= DB::table('warehouse_config')
                      ->where('res_prod_grp_id','=',$product_id)
                      ->first();
              $getPid=json_decode(json_encode($getPid),true);
            return $getPid;
        }
        public function updateWarehouseConfigByGrpId($pro_grp_id,$exited_Grp_id)
        {
            $grpRs= DB::table('product_bin_config')
                    ->where('prod_group_id',$exited_Grp_id)
                    ->update(['prod_group_id' => $pro_grp_id]);
            return $grpRs;
        }
    public function getObjNameByUrl()
    {
        if(isset($_SERVER["REQUEST_URI"]))
        {
        $referer = $_SERVER["REQUEST_URI"];
        $urlArray = explode('/',$referer);
        $is_provider  = isset($urlArray[2])?$urlArray[2]:'';
        }
        return $is_provider;
    }
    public function getStatusByUrl()
    {
        $status = array();
        $is_provider = $this->getUrltype();
        switch ($is_provider)
        {
            case 'creation':
                $status = array('57002','57001');
                break;
            case 'approval':
                $status = array('57007');
                break;
            case 'filling':
                $status = array('57003');
                break;
            case 'enablement':
                $status = array('57006');
                break;
            case 'open':
                $status = array('57006','57002','57003','57007');
                break;
            default:
                $status = array();
                break;
        }
        return $status;
    }
    
    public function getUrltype()
    {
        $status = array();
        if(isset($_SERVER["HTTP_REFERER"]))
        {
        $referer = $_SERVER["HTTP_REFERER"];
        $urlArray = explode('/',$referer);
        $is_provider  = isset($urlArray[4])?$urlArray[4]:'';
        }
        return $is_provider;
    }
    public function getGridData($request,$approvalStatus) {
        /*$data_access = $this->getBrandAccess();*/
        DB::enableQueryLog();
        $this->_roleRepo = new RoleRepo();
        $this->grid_field_db_match = array(
            'Product_Title' => 'product_title',
            'Brand' => 'brand_name',
            'KVI' => 'kvi',
            'category_name' => 'category_name',
            'pack_size' => 'pack_size',
            'SKU' => 'sku',
            'ManfName' => 'manf_name',
            'MRP' => 'mrp',
            'Created_By' => 'created_by',
            'Status' => 'status',
            'cp_enabled' => 'cp_enabled',
            'is_active' => 'is_active',
			'ELP'=>'elp',
			'ESP'=>'esp',
			'PTR'=>'ptrvalue',
			'TAX'=>'taxper',
			'CFC'=>'cfc_qty',
			'INV'=>'available_inventory'
        );

        $urlType = $this->getUrltype();
      
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        //,'brand_logo as Brand','suppliercnt as Supplier_Count'
        $DcId = Session::get('warehouseId');
        if($DcId==0){

           $Product_Model_Obj = new VwProductsByAllWarehousesModel();
        }else{
           $Product_Model_Obj = new VwProductsByWarehouseModel();    
        }
        

        $Legal_Entity = Session::get('legal_entity_id');
        $parentleQuery=DB::table('legal_entities')
                    ->select('parent_le_id')
                    ->where('legal_entity_id',$Legal_Entity)
                    ->get()->all();
        $parentLegalEntity=$parentleQuery[0]->parent_le_id;


        /* $query = $Product_Model_Obj::select(['product_id as Product_ID','image as ProductLogo','category_name as Category','product_title as Product_Name','mrp as MRP','base_price as BasePrice','EBP as EBP','RBP as RBP','CBP as CBP','inventorymode as Inventory_Mode','Schemes','status as Status','MBQ as MPQ']); */

		
        if($DcId==0){
        $rolesObj = new Role();
        $userid = Session::get('userId');
        $Json = json_decode($rolesObj->getFilterData(6,$userid), 1);
        $filters = json_decode($Json['sbu'], 1);            
        $DcId = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $DcId=explode(',',$DcId);
        array_push($DcId,0);
        //print_r($DcId);exit;
        }else{
            $DcId=explode(',',$DcId);
        }
        $query = $Product_Model_Obj::select(['product_id as Product_ID', 'primary_image as ProductLogo', 'product_title as Product_Title', 'brand_name as Brand',
                    'kvi as KVI', 'manufacturer_name as ManfName', 'category_name',  DB::raw('case when pack_size IS NOT NULL then concat(pack_size," ",pack_size_uom,"") else pack_size end as pack_size'),'sku as SKU', 'mrp as MRP', 'is_approved as IsApproved', 'status as Status', 'cp_enabled as cp_enabled', 'created_by as Created_By','elp as ELP','esp as ESP','ptrvalue as PTR','taxper as TAX','cfc_qty as CFC','available_inventory as INV']);
        $query->where(['product_type_id'=>'130002']);
        $query->whereIn('le_wh_id',$DcId);
        $user_id=Session::get('userId');
        $userData = DB::select(DB::raw('select * from users u join legal_entities l on u.`legal_entity_id`= l.legal_entity_id where l.`legal_entity_type_id` IN (1006,1002,89002) AND  u.is_active=1 and u.user_id='.$user_id ));
        if(empty($userData)){
           // echo 'in empty';
        }
        else{
            $brands=DB::table('user_permssion')
                           ->where(['permission_level_id' => 7, 'user_id' => $user_id])
                         ->pluck('object_id')->all();
            $manufacturer=DB::table('user_permssion')
                           ->where(['permission_level_id' => 11, 'user_id' => $user_id])
                         ->pluck('object_id')->all();            
            if(!empty($manufacturer)){
                $brandsFromManufacturer=DB::table('brands')
                                    ->whereIn('mfg_id',$manufacturer)
                                    ->pluck('brand_id')->all();
                $finalArray=implode(',',array_unique(array_merge($brands,$brandsFromManufacturer)));
                $finalArray=explode(',',$finalArray);
                if(!in_array(0, $finalArray)){
                    $query->whereIn('brand_id',$finalArray);
                    // $urlType='active';
                }
            }else{
               
                    $query->whereIn('brand_id',$brands);
            }  
          
        }
        

        if(!empty($approvalStatus)){
        $query->whereIn('status',$approvalStatus);
        }      
        if($urlType == 'disabled')
        {
            $query->where('status',1)->where(function($q){
                $q->where('is_sellable','=','0')
                ->orwhere('cp_enabled','=','0');
            });
        }
        else if($urlType == 'active')
        {
            $query->where('status',1)->where('cp_enabled','=',1)->where('is_sellable','=',1);
        }
        //echo $query->toSql();exit;

        /* $rolesObj= new Role();
          $brandFilter= $rolesObj->getFilterData(9, Session::get('userId'));
          $brandFilter=json_decode($brandFilter,true);
          $list = isset($DataFilter['brand']) ? $DataFilter['brand'] : []; */
        /*if ($parentLegalEntity != 0) {
            $query->where('legal_entity_id', '=', 2);
        }*/

        if ($request->input('$orderby')) {    //checking for sorting
            $order = explode(' ', $request->input('$orderby'));

            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc

            $order_by_type = 'desc';

            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }

            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }


        if ($request->input('$filter')) {           //checking for filtering
            $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


            foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                $filter = explode(' ', $post_filter_query_sub);
                $length = count($filter);

                $filter_query_field = '';

                if ($length > 3) {
                    for ($i = 0; $i < $length - 2; $i++)
                        $filter_query_field .= $filter[$i] . " ";
                    $filter_query_field = trim($filter_query_field);
                    $filter_query_operator = $filter[$length - 2];
                    $filter_query_value = $filter[$length - 1];
                } else {
                    $filter_query_field = $filter[0];
                    $filter_query_operator = $filter[1];
                    $filter_query_value = $filter[2];
                }

                $filter_query_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                    if ($filter_query_substr == 'startsw') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = $filter_value_array[1] . '%';


                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }


                    if ($filter_query_substr == 'endswit') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = '%' . $filter_value_array[1];


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }




                    if ($filter_query_substr == 'tolower') {

                        $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = $filter_value_array[1];

                        if ($filter_query_operator == 'eq') {
                            $like = '=';
                        } else {
                            $like = '!=';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }

                    if ($filter_query_substr == 'indexof') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_value_array[1] . '%';

                        if ($filter_query_operator == 'ge') {
                            $like = 'like';
                        } else {
                            $like = 'not like';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }
                } else {

                    switch ($filter_query_operator) {
                        case 'eq' :

                            $filter_operator = '=';

                            break;

                        case 'ne':

                            $filter_operator = '!=';

                            break;

                        case 'gt' :

                            $filter_operator = '>';

                            break;

                        case 'lt' :

                            $filter_operator = '<';

                            break;

                        case 'ge' :

                            $filter_operator = '>=';

                            break;

                        case 'le' :

                            $filter_operator = '<=';

                            break;
                    }


                    if (isset($this->grid_field_db_match[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }

                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
            
        }
        $query->groupBy('product_id');
           $query->orderBy('product_id','desc');
        $row_count = count($query->get()->all());
       

        $query->skip($skip)->take($pageSize);

        $Manage_Products = $query->get()->all();
       
        $work_flow = DB::table('master_lookup')->where('mas_cat_id', 57)->pluck('master_lookup_name', 'value')->all();
        $aprovel_names = json_decode(json_encode($work_flow), true);

        foreach ($Manage_Products as $k => $list) {


            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $Manage_Products[$k]['ProductLogo'])) {
                $Manage_Products[$k]['ProductLogo'] = '/uploads/products/' . $Manage_Products[$k]['ProductLogo'];
            }

            $UoM = '';
            if ($Manage_Products[$k]['UoM']) {

                $weight_uom = DB::table('master_lookup')->select('description')->where('value', $Manage_Products[$k]['UoM'])->get()->all();
                $UoM = $weight_uom[0]->description;
            }
            if ($list->IsApproved == 1) {
                $IsApproved = '<span style="display:block; position: absolute;margin-left:35px" class="ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
            } else {
                $IsApproved = '<span style="display:block position: absolute;margin-left:35px" class="ui-igcheckbox-small-off ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
            }
            $cp_enabled = '';
            if ($list->cp_enabled == 1) {
                $cp_enabled = '<span style="display:block; margin-left:15px" class="ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
            } else {
                $cp_enabled = '<span style="display:block; margin-left:15px" class="ui-igcheckbox-small-off ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
            }
            $Status = '';
			if (array_key_exists($Manage_Products[$k]['Status'],$aprovel_names))
			  {
			  $Status = $aprovel_names[$Manage_Products[$k]['Status']];
			  }

            /* if($list->Status == 0 || empty($list->Status)) {
              $Status = '<span style="display:block; margin-left:30px" class="ui-igcheckbox-small-off ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
              } else {
              $Status = '<span style="display:block; margin-left:30px" class="ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
              } */

            $Manage_Products[$k]['Weight'] = round($Manage_Products[$k]['Weight'], 2) . ' ' . $UoM;
            $Manage_Products[$k]['IsApproved'] = $IsApproved;
            $Manage_Products[$k]['cp_enabled'] = $cp_enabled;
            $Manage_Products[$k]['Statuss'] = $Status;
            $action = '';
            $approve_product = $this->_roleRepo->checkPermissionByFeatureCode('PRD004');
            $edit_product    = $this->_roleRepo->checkPermissionByFeatureCode('PRD002');
            $pricing_product = $this->_roleRepo->checkPermissionByFeatureCode('PRD005');
            $delete_product  = $this->_roleRepo->checkPermissionByFeatureCode('PRD003');
			$quickedit_product = $this->_roleRepo->checkPermissionByFeatureCode('PRD008');
            if($approve_product == 1) {
               $action .= '&nbsp;&nbsp;<a data-toggle="modal" title="Product Approval" href="/productpreview/' . $Manage_Products[$k]['Product_ID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp'; 
            }
            if ($edit_product == 1) {
                $action .= '<a data-toggle="modal" title="Product Edit" href="/editproduct/' . $Manage_Products[$k]['Product_ID'] . '"> <i class="fa fa-pencil"></i></a>&nbsp';
            }
            if ($pricing_product == 1) {
                $action .= '<a href="javascript:void(0)" title="Set Price" onclick="savePriceDataFromPrice('.$Manage_Products[$k]['Product_ID'].')"> <i class="fa fa-rupee"></i> </a>&nbsp';            
            }
			if ($quickedit_product == 1) {
                $action .='<a class="quickedit" title="Quick Product Edit" href="/quickProductUpdate/'.$Manage_Products[$k]['Product_ID'].'"> <i class="fa fa-fast-forward" aria-hidden="true"></i> </a>';
            } 			
            if ($delete_product == 1) {
                $action .='<a class="deleteProduct" title="Delete Product" href="' . $Manage_Products[$k]['Product_ID'] . '"> <i class="fa fa-trash-o"></i> </a>';
            }         
                        
            $Manage_Products[$k]['Action'] = $action;
        }
		//&nbsp;<a class="quickProductUpdate" title="Quick Product Update" href="quickProductUpdate/' . $Manage_Products[$k]['Product_ID'] . '"> <i class="fa fa-fast-forward" aria-hidden="true"></i> </a>
        return  json_encode(array('Records' => $Manage_Products, 'TotalRecordsCount' => $row_count));

    }

    public function getAcessDetails(){

        $this->_roleModel = new Role();
        $Json = json_decode($this->_roleModel->getFilterData(6), 1);
        $filters = json_decode($Json['sbu'], 1);
        $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $hub_acess_list = isset($filters['118002']) ? $filters['118002'] : 'NULL';
        $data['dc_acess_list'] = $dc_acess_list;
        $data['hub_acess_list'] = $hub_acess_list;
        
        return $data;
    }

    public function getBrandAccess(){

        $this->_roleModel = new Role();
        $Json = json_decode($this->_roleModel->getFilterData(7), 1);
        
        return $Json;
    }

    public function checkProductProp($product_id,$le_wh_id){
        $check = DB::table("products")
                ->where("product_id",$product_id)
                ->where("is_sellable",1)
                ->where("cp_enabled",1)
                ->count();
        return $check;
    }

    public function getProductSU($product_id){
        $esu = DB::table("products")
                ->select("esu")
                ->where("product_id",$product_id)
                ->first();
        return $esu;
    }

    public function getProductCPStatusByWarehouse($productid,$le_wh_id,$cp_enabled){
        $date=date('Y-m-d H:i:s');
        $productinfo="select product_cpenabled_dcfc_id from product_cpenabled_dcfcwise where product_id=".$productid." and le_wh_id=".$le_wh_id;
        $productinfo_query=DB::selectFromWriteConnection(DB::raw($productinfo));

        if(count($productinfo_query)>0){

            $warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                            ->where('le_wh_id',$le_wh_id)
                            ->where('product_id',$productid)
                            ->update(['cp_enabled'=>$cp_enabled,'updated_by'=>Session::get('userId'),'updated_at'=>$date]);

        }else{

            $warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                              ->insert(['product_id'=>$productid,
                                'le_wh_id'=>$le_wh_id,
                                'cp_enabled'=>$cp_enabled,'created_by'=>Session::get('userId'),'created_at'=>$date]);

        }
        if($warehouseproduct){
            return true;
        }else{
            return false;
        }
    }

    public function getProductIsSellableStatusByWarehouse($productid,$le_wh_id,$issellable){
        $date=date('Y-m-d H:i:s');
        $productissellableinfo="select product_cpenabled_dcfc_id from product_cpenabled_dcfcwise where product_id=".$productid." and le_wh_id=".$le_wh_id;
        $check_warehouseproductid=DB::selectFromWriteConnection(DB::raw($productissellableinfo));

        if(count($check_warehouseproductid)>0){
            $insert_warehouseproductissellable=DB::table('product_cpenabled_dcfcwise')
                                    ->where('le_wh_id',$le_wh_id)
                                    ->where('product_id',$productid)
                                    ->update(['is_sellable'=>$issellable,'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
       }else{
            $insert_warehouseproductissellable=DB::table('product_cpenabled_dcfcwise')
                                    ->insert(['product_id'=>$productid,
                                    'le_wh_id'=>$le_wh_id,
                                    'is_sellable'=>$issellable,'created_by'=>Session::get('userId'),'created_at'=>$date]);
       }
        if($insert_warehouseproductissellable){
            return true;
        }else{
            return false;
        }
    }

    public function getProductElpHistory($productid,$sqlWhrCls,$skip,$pageSize){
        try{
            /*$getprodutselp="select * from vw_ProductElpHistory where product_id=".$productid;
            if($sqlWhrCls!=''){
                $getprodutselp.=$sqlWhrCls;
            }
            $getprodutselp.=" limit ".$skip.','.$pageSize;
            $resultset['Records']=DB::selectFromWriteConnection(DB::raw($getprodutselp));*/
           /* $result=array();
            $getprodutselp=DB::table('vw_ProductElpHistory')
                          ->select('*')->where('Product_ID',$productid);
            if($sqlWhrCls!='')
                $getprodutselp=$getprodutselp->whereRaw($sqlWhrCls);
            
            $result['count'] = $getprodutselp->count(); 
        if(is_numeric($skip)){                 
            $result['records']=$getprodutselp->skip($skip)->take($pageSize)
                          ->get();
          }else{
            $result['records']=$getprodutselp->get();
          }*/
          if($sqlWhrCls!=''){
            $sqlWhrCls=" and ".$sqlWhrCls;
          }else{
            $sqlWhrCls='';
          }
          $productelphistory="SELECT
                              `po`.`po_code`         AS `PO_Code`,
                              `pph`.`product_id`     AS `Product_ID`,
                              `getProductName`(
                            `pph`.`product_id`)  AS `Product_Title`,
                              `getBusinessLegalName`(
                            `pph`.`supplier_id`)  AS `Supplier`,
                              (SELECT
                                 `getLeWhName`(
                            `dc_fc_mapping`.`dc_le_wh_id`) 
                               FROM `dc_fc_mapping`
                               WHERE (`dc_fc_mapping`.`fc_le_wh_id` = `pph`.`le_wh_id`)) AS `DC`,
                              IFNULL((SELECT `getStateNameById`(`legalentity_warehouses`.`state`) FROM (`dc_fc_mapping` JOIN `legalentity_warehouses` ON((`legalentity_warehouses`.`le_wh_id` = `dc_fc_mapping`.`dc_le_wh_id`))) WHERE (`dc_fc_mapping`.`fc_le_wh_id` = `pph`.`le_wh_id`)),`getStateNameById`(`le`.`state_id`)) AS `State`,
                              `getLeWhName`(
                            `pph`.`le_wh_id`)  AS `FC`,
                              `pph`.`elp`            AS `Dlp_Flp`,
                               `pph`.`actual_elp`     AS `Actual_Elp`,
                               date(`pph`.`effective_date`) AS `Effective_Date`
                            FROM ((`purchase_price_history` `pph`
                                LEFT JOIN `po`
                                  ON ((`po`.`po_id` = `pph`.`po_id`)))
                               LEFT JOIN `legal_entities` `le`
                                 ON ((`le`.`legal_entity_id` = `pph`.`supplier_id`))) where `pph`.`product_id`=".$productid." ".$sqlWhrCls." order by `pph`.`pur_price_id` desc limit ".$skip.",".$pageSize;
        $result['records']=DB::select(DB::raw($productelphistory));
        $productelphistorycount="SELECT count(`pph`.`pur_price_id`) as count
                            FROM ((`purchase_price_history` `pph`
                                LEFT JOIN `po`
                                  ON ((`po`.`po_id` = `pph`.`po_id`)))
                               LEFT JOIN `legal_entities` `le`
                                 ON ((`le`.`legal_entity_id` = `pph`.`supplier_id`))) where `pph`.`product_id`=".$productid." ".$sqlWhrCls;
         $resultelpcount=DB::select(DB::raw($productelphistorycount));
         $result['count']=isset($resultelpcount[0]->count)?$resultelpcount[0]->count:0;
            return $result;
        }catch (\ErrorException $ex) {
            
            $result['count']=0;
            $result['records']=[];
            return $result;
        }
    }


    function getMappedSuppliersForManufacturer($manufacturer){

        $getmappedSuppliers=DB::table('supplier_brand_mapping')
                            ->select(DB::raw('GROUP_CONCAT(supplier_id) as supplier'));
        if(!in_array(0, $manufacturer)){
          $getmappedSuppliers=$getmappedSuppliers->whereIn('manufacturer_id',$manufacturer);
        }
        $getmappedSuppliers=$getmappedSuppliers->first();
        $getmappedSuppliers=explode(',', $getmappedSuppliers->supplier);
        return $getmappedSuppliers;
    }
    
    //Product Color Configuration Functions - Start
    public function getProductColorGridData($request) {
        
        DB::enableQueryLog();
        $this->_roleRepo = new RoleRepo();
        $this->grid_field_db_match = array(
            'ColorWhId' => 'color_wh_id',
            'LeWhId' => 'le_wh_id',
            'DisplayName' => 'display_name',
            'ProductName' => DB::raw('CONVERT(getProductName(product_id) USING utf8)'),
            'Pack' => DB::raw('getMastLookupDescByValue(pack_id)'),
            'CustomerType' => DB::raw('getMastLookupDescByValue(customer_type)'),
            'Color' => DB::raw('getMastLookupValue(color_code)'),
            'Elp' => 'elp',
            'Esp' => 'esp',
            'Margin' => 'ppc.margin'
        );
      
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        //////////////////////////////////////
        $rolesObj = new Role();
        $userid = Session::get('userId');
        $Json = json_decode($rolesObj->getFilterData(6,$userid), 1);
        $filters = json_decode($Json['sbu'], 1);            
        $DcId = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $DcId = explode(',',$DcId);
        array_push($DcId,0);
        //////////////////////////////////////

        ////////////////////////////////////////////
        $query = DB::table('product_pack_color_wh AS ppc')
                    ->select('ppc.color_wh_id','ppc.le_wh_id','display_name',DB::raw('getProductName(product_id) AS product_name'),DB::raw('
 getMastLookupDescByValue(pack_id) AS pack'),
DB::raw('getMastLookupDescByValue(customer_type) AS customer_type'),
DB::raw('getMastLookupValue(color_code) AS color'),'ppc.elp','ppc.esp','ppc.margin')
                    ->join('legalentity_warehouses AS lw','lw.le_wh_id', '=' ,'ppc.le_wh_id')
                    ->whereIn('lw.le_wh_id',$DcId);
        // echo $query->toSql();exit;            
                       
        ////////////////////////////////////////////

        if ($request->input('$orderby')) {    //checking for sorting
            $order = explode(' ', $request->input('$orderby'));

            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc

            $order_by_type = 'desc';

            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }

            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }


        if ($request->input('$filter')) {           //checking for filtering
            $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


            foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                $filter = explode(' ', $post_filter_query_sub);
                $length = count($filter);

                $filter_query_field = '';

                if ($length > 3) {
                    for ($i = 0; $i < $length - 2; $i++)
                        $filter_query_field .= $filter[$i] . " ";
                    $filter_query_field = trim($filter_query_field);
                    $filter_query_operator = $filter[$length - 2];
                    $filter_query_value = $filter[$length - 1];
                } else {
                    $filter_query_field = $filter[0];
                    $filter_query_operator = $filter[1];
                    $filter_query_value = $filter[2];
                }

                $filter_query_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                    if ($filter_query_substr == 'startsw') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = $filter_value_array[1] . '%';


                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }


                    if ($filter_query_substr == 'endswit') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = '%' . $filter_value_array[1];


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }




                    if ($filter_query_substr == 'tolower') {

                        $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = $filter_value_array[1];

                        if ($filter_query_operator == 'eq') {
                            $like = '=';
                        } else {
                            $like = '!=';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }

                    if ($filter_query_substr == 'indexof') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_value_array[1] . '%';

                        if ($filter_query_operator == 'ge') {
                            $like = 'like';
                        } else {
                            $like = 'not like';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }
                } else {

                    switch ($filter_query_operator) {
                        case 'eq' :

                            $filter_operator = '=';

                            break;

                        case 'ne':

                            $filter_operator = '!=';

                            break;

                        case 'gt' :

                            $filter_operator = '>';

                            break;

                        case 'lt' :

                            $filter_operator = '<';

                            break;

                        case 'ge' :

                            $filter_operator = '>=';

                            break;

                        case 'le' :

                            $filter_operator = '<=';

                            break;
                    }


                    if (isset($this->grid_field_db_match[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }

                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
            
        }

        $row_count = $query->count();		
       

        $query->skip($skip)->take($pageSize);

        $Manage_prod_color_codes = $query->get()->all();
       
        $Manage_prod_color_codes1 = array();
        foreach ($Manage_prod_color_codes as $k => $list) {
            
            $Manage_prod_color_codes1[$k]['ColorWhId']  = $list->color_wh_id;
            $Manage_prod_color_codes1[$k]['LeWhId']     = $list->le_wh_id;
            $Manage_prod_color_codes1[$k]['DisplayName'] = $list->display_name;
            $Manage_prod_color_codes1[$k]['ProductName'] = $list->product_name;
            $Manage_prod_color_codes1[$k]['Pack']        = $list->pack;
            $Manage_prod_color_codes1[$k]['CustomerType'] = $list->customer_type;
            $Manage_prod_color_codes1[$k]['Color'] = $list->color;
            $Manage_prod_color_codes1[$k]['Elp'] = $list->elp;
            $Manage_prod_color_codes1[$k]['Esp'] = $list->esp;
            $Manage_prod_color_codes1[$k]['Margin'] = $list->margin;

            $editdeletepermission=$this->_roleRepo->checkPermissionByFeatureCode('ETDLT001');
            $action = "";
            if ($editdeletepermission == 1) 
            {

                $action.= '<span class="actionsStyle" ><a onclick="editProdColorConfigRecord('.$Manage_prod_color_codes1[$k]['ColorWhId'].')"</a><i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;&nbsp;</span> ';
                $action.= '<span class="actionsStyle" ><a onclick="deleteProdColorConfifRecord('.$Manage_prod_color_codes1[$k]['ColorWhId'].')"</a><i class="fa fa-trash-o"></i></span>';
            }

            $Manage_prod_color_codes1[$k]['Action'] = $action;
        }

        return  json_encode(array('Records' => $Manage_prod_color_codes1, 'TotalRecordsCount' => $row_count));

    }

    public function getProductNamesForSearch($data)
    {
        try
        {
            $term = $data['term'];
            $products = DB::table('products')
            ->where('products.is_sellable',1)
            ->where(function ($query) use($term) {
                $query->orWhere('products.sku','like', '%'.$term.'%')
                      ->orWhere('products.product_title','like', '%'.$term.'%')
                      ->orWhere('products.upc','like', '%'.$term.'%');
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

    public function getProductColorConfigRecord($id)
    {
        $query = '
            SELECT
                le_wh_id AS WareHouse_Name,
                product_id AS Product_Id,
                '.DB::raw('getProductName(product_id) AS Product_Name').',
                pack_id AS Pack,
                customer_type AS Customer_Type,
                color_code AS Color,
                elp AS Elp,
                esp AS Esp,
                margin AS Margin
            FROM 
                product_pack_color_wh
            WHERE 
                color_wh_id = ?';
        $result = DB::SELECT($query,[$id]);
        if(!empty($result))
            return $result;
        return NULL;
    }

    public function insertProductColorConfigRecord($data)
    {
        $result = DB::TABLE('product_pack_color_wh')
                ->insertGetId([
                    "product_id" => $data["add_product_id"],
                    "pack_id" => $data['Add_Pack'],
                    "le_wh_id" => $data["Add_WareHouse_Name"],
                    "customer_type" => $data["Add_Customer_Type"],
                    "color_code" => $data["Add_Color"]
                    /*,
                    "margin" => $data['Add_Margin'],
                    "esp" => $data["Add_Esp"],
                    "elp" => $data["Add_Elp"]*/
                ]);
        return $result;
    }

    public function validateProdColorConfig($data)
    {
        $query = '
            SELECT
                COUNT(1) AS totCount
            FROM
                product_pack_color_wh
            WHERE
                product_id = ? AND pack_id = ? AND le_wh_id = ? AND customer_type = ?';

        
        $result = DB::SELECT($query,[$data["add_product_id"],$data['Add_Pack'],$data["Add_WareHouse_Name"],$data["Add_Customer_Type"]]);

        $result = isset($result[0]->totCount)?$result[0]->totCount:$result;
        
        return ($result<1)?TRUE:FALSE;
    }
    
    public function delProductColorConfigRecord($id)
    {
        $query = 'DELETE FROM product_pack_color_wh WHERE color_wh_id = ?';
        $status = DB::DELETE($query,[$id]);
        if(!empty($status))
            return $status;
        return false;
    }

    public function updateProductColorConfigRecord($data)
    {
        $query = '
            UPDATE 
                product_pack_color_wh
            SET
                color_code = ?
            WHERE
                color_wh_id = ?';

        $result = DB::UPDATE($query,[
            $data['Edit_Color'],
            $data['Primary_Id']
        ]);

        return $result;
    }

    public function getDownlodColorTemplateInfo($wareHouseInfo ,$getCustomerGroup ,$packageLevel , $colors)
    {
        $download_data_arr = array();

        //Building Reference Sheet - Start
        $download_data_arr['warehouselist'] = $wareHouseInfo;
        $download_data_arr['pack_types_data'] = $packageLevel;

        $download_data_arr['star_lookup_data'] = $colors;

        $download_data_arr['customer_data'] = $getCustomerGroup;  

        $warehouse_arr = array();
        foreach($wareHouseInfo as $wareHouseObj)
        {
            array_push($warehouse_arr, $wareHouseObj->le_wh_id);
        }

        $products = DB::table('products')
                    ->where('products.is_sellable',1)
                    ->whereIn('tot.le_wh_id',$warehouse_arr)
                    ->leftJoin('product_tot as tot','products.product_id','=','tot.product_id')
                    ->select('products.product_title','products.sku')
                    ->groupBy('tot.product_id')->get()->all();
        $download_data_arr['products'] = $products;    

        return $download_data_arr;
    }

    public function checkProductPacks($product_id, $pack_id)
    {
        $flag = false;
        $levelSortOrder = DB::table('master_lookup')->where('mas_cat_id',16)->orderBy('sort_order','asc')->pluck('value')->all();   
        $levelSortOrderIds = implode(',', $levelSortOrder);
        $Product_Pack_Config = DB::table('product_pack_config')
                  ->where('product_id', $product_id)
                  ->select('level')
                  ->orderByRaw(DB::raw("FIELD(level, $levelSortOrderIds)"))->get()->all();

        foreach($Product_Pack_Config as $prod_pack_obj)
        {
            if($prod_pack_obj->level == $pack_id)
            {
                $flag = true;
                break;
            }
        }
        return $flag;
    }

    public function checkProductWarehouse($warehouse_id, $product_id)
    {
        $product_warehouse = DB::table('product_tot')
                            ->where(['le_wh_id'=>$warehouse_id, 'product_id'=>$product_id ])
                            ->count();
        if($product_warehouse>0)
        {
            return true;
        }   
        else{
            return false;
        }            

    }
}
