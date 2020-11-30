<?php

namespace App\Modules\Manufacturers\Controllers;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Http\Controllers\BaseController;
use Session;
use View;
use DB;
use \Log;
use URL;
use Image;
use Imagine;
use Carbon\Carbon;
use UserActivity;
use App\Modules\Manufacturers\Models\Legalentities;
use App\Modules\Manufacturers\Models\BrandModel;
use Redirect;
use App\Modules\Pricing\Models\pricingDashboardModel;
use App\Modules\Supplier\Models\LegalentitywarehousesModel;
use App\Modules\Product\Models\ProductAttributes;
use App\Modules\Manufacturers\Models\Users;
use  App\Modules\Roles\Models\Role;
use Illuminate\Http\Request;
use App\Modules\Manufacturers\Models\VwManfBrandsModel;
use App\Modules\Manufacturers\Models\VwManfProductsModel;
use App\Modules\Product\Models\ProductRelations;
use App\Central\Repositories\ProductRepo;
use App\Modules\Product\Models\ProductPolicies;
use App\Modules\Product\Models\ProductPackConfig;
use App\Modules\Product\Models\ProductTOT;
use App\Modules\Product\Models\ProductInfo;
use App\Modules\Product\Models\ProductModel;
use App\Modules\Product\Models\ProductMedia;
use App\Modules\Product\Models\AttributesModel;
use App\Modules\Product\Models\CategoryEloquentModel;
use App\Modules\Product\Models\ProductCharacteristic;
use App\Modules\Product\Models\AttributesSetModel;
use App\Central\Repositories\RoleRepo;
use App\Modules\Product\Controllers\ProductController;
use App\Modules\Product\Models\ProductContent;
use App\Modules\Manufacturers\Models\ApprovalWorkflowHistoryModel;
use App\Modules\Tax\Models\MasterLookUp;
use App\Modules\Tax\Models\TaxClass;
use App\Modules\Supplier\Models\SupplierModel;
use App\Modules\Supplier\Models\ZoneModel;

use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\LegalEntities\Models\Legalentity;
use Illuminate\Support\Facades\Input;
class ManufacturerController extends BaseController
{
    private $brandList;
    public function __construct()
    {
        try
        {
           parent::__construct();
          //For tax Tab
          $this->_masterlookup = new MasterLookUp();
          $this->_taxclass = new TaxClass();
          $this->objPricing = new pricingDashboardModel();
          $this->brandList='<option value="0">Please Select Brand ...</option>';
           //For Raback
            $this->_roleRepo = new RoleRepo();
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });
            parent::Title('Brands -Ebutor');

            //Please fill the grid filed name along with db table field name example 'gridid' => 'table.fieldname'
$this->grid_field_db_match = array(
                'ProductID'             => 'products.product_id',
                'BrandName'             => 'brands.brand_name',
                'Category'              => 'products.category_id',
                'ProductName'           => 'products.product_title',
                'MRP'                   => 'product_tot.mrp',
                'MSP'                   => 'product_tot.msp',
                'Bestprice'             => 'product_tot.base_price',
                'VAT'                   => 'product_tot.vat',
                'CST'                   => 'product_tot.cst',
                'EBP'                   => 'product_tot.cst',
                'RBP'                   => 'product_tot.cst',
                'CBP'                   => 'product_tot.cst',
                'InventoryMode'         => 'product_tot.inventory_mode',
                'Status'                => 'products.is_active',
                'BrandID'               => 'brands.brand_id',
                'Description'           => 'brands.description',
                'Authorized'            => 'brands.is_authorized',
                'Trademark'             => 'brands.is_trademark',
                'BrandName'             => 'vw_brand_details.brand_name',
                'ManufacturerName'      => 'vw_brand_details.manufacturer_name',
                'Products'              => 'vw_brand_details.Products',
                'With_Images'           => 'vw_brand_details.withImages',
                'Without_Images'        => 'vw_brand_details.withoutimages',
                'With_Inventory'        => 'vw_brand_details.withinventory',
                'Without_Inventory'     => 'vw_brand_details.withoutinventory',
                'Approved'              => 'vw_brand_details.approved',
                'Pending'               => 'vw_brand_details.pending'
            );
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
  
  public function getManufacturerSegments()
    {
        $manufacturer_segments = DB::table('master_lookup_categories')
        ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
        ->select('master_lookup.master_lookup_name as segement_name','master_lookup.value as value')
        ->where('master_lookup_categories.mas_cat_id','=','64')
        ->where('master_lookup_categories.mas_cat_name','=','Manufacturer Segments')
        ->get()->all();
        return $manufacturer_segments;
    }

   
    public function getBrandChildList($brand_id,$level,$manf_id,$list)
    {
       $Brand_Model_Obj = new BrandModel();
      
        if(empty($manf_id) && $manf_id==0)
        {
           $brandsList= $Brand_Model_Obj->where('parent_brand_id',$brand_id)->whereIN('brand_id',array_keys($list))->get()->all();
        }else
        {
           $brandsList= $Brand_Model_Obj->where('parent_brand_id',$brand_id)->where('mfg_id',$manf_id)->whereIN('brand_id',array_keys($list))->get()->all();
        }
       

       if (!empty($brandsList)) 
        {
            foreach($brandsList as  $cat1)
            { 
                $css_class='';
                switch ($level) {
                    case 1:
                        $css_class='parent_cat';
                        break;
                    case 2:
                        $css_class='sub_cat';
                        break;
                    case 3:
                        $css_class='prod_class';
                        break;                
                    default:
                        $css_class='prod_class';
                        break;
                }
                $this->brandList.= '<option value="'.$cat1->brand_id.'" class="'.$css_class.'" style="font-size:13px; color:#000000">'.$cat1->brand_name.'</option>';
                $this->getBrandChildList($cat1->brand_id,$level+1,$manf_id,$list);
            }
        }
                 
    }
    
  function returnLegalentityAllBrands()
  {

    $Brand_Model_Obj = new BrandModel();

    $legalEntityIdArray=array();
    
    $legal_entity_id = Session::get('legal_entity_id');
      $child_legal_entity_id = DB::table('legal_entities')->select('legal_entity_id')->where(['parent_id'=> $legal_entity_id,'legal_entity_type_id'=>'1006'])->get()->all();
      foreach ($child_legal_entity_id as $val)
      {
        $legalEntityIdArray[] = $val->legal_entity_id;
      }
            

            if ($legal_entity_id == 0)
            {
                $query = $Brand_Model_Obj::select(['brands.brand_id','brands.brand_name']);
            }
            else
            {


                $query = $Brand_Model_Obj::select(['brands.brand_id','brands.brand_name'])->whereIn('legal_entity_id', $legalEntityIdArray);
            }   

      return $query->get()->all();
        
  }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Session::has('userId'))
        {
            return Redirect::to('/');
        }
        $breadCrumbs = array('Home' => url('/'), 'Products' => url('/'), 'Brands' => url('/') . '/brands', 'Add' => '#');
        parent::Breadcrumbs($breadCrumbs);
        $manu_org_type = $this->getManuCompanytype();                    
        $categories = $this->categoriesList();
        $returns_location_types = $this->returnsLocationType();
        $manu = $this->getManufacturerList('1006');
        $manufacturer_segments=$this->getManufacturerSegments();
        $Brand_Model_Obj = new BrandModel();
         $purchase_manager = $Brand_Model_Obj->getPurchaseManager();
        //$query  = $Brand_Model_Obj::all();
        //$brands_Data=json_decode(json_encode($query));
        $brands_Data  = $this->returnLegalentityAllBrands();
        Session::put('brand_id', '');
        return View::make('Manufacturers::manufacturers', ['manu_org_type'=>$manu_org_type,'returns_location_types_list' => $returns_location_types, 'category_list' => $categories, 'manufacturer_segments' => $manufacturer_segments, 'brands' => $brands_Data,'purchase_manager'=>$purchase_manager]);
    }
    
   
     
    //creating relative products
    public function createRelativeProducts(Request $request)
    {       
        $childExit = DB::table('product_relations')->where('parent_id',$request->parent_id)
                        ->where('product_id',$request->product_id)->get()->all(); 
        $count_childs = DB::table('product_relations')->where('parent_id',$request->product_id)->count();  // for check the parent exit or not              
        if(empty($childExit) && $count_childs == 0) { 
        $productRelationObj= new ProductRelations();
        $productRelationObj->parent_id = $request->parent_id;
        $productRelationObj->product_id = $request->product_id;
        $productRelationObj->save();
        $statusUpdate = DB::table('products')->where('product_id', $request->product_id)->update(['is_parent'=>'0']);
        } else {            
            $productRelationObj = '1';            
        }
       return $productRelationObj;
    }
    //get seareched products
    public function getSearchedProducts($brand_id)
    {
      
        $productInfoObj = new ProductInfo();
        $productRelationObj= new ProductRelations();
        $searchTerm = $_GET['term']; 
        $product_data= $productInfoObj
                      ->select(DB::raw('group_concat(product_id) as getIds'))
                      ->where('brand_id',$brand_id)->first();  
      
        $rs= json_decode(json_encode($product_data),true);
       //get product relation table if existed products
        $product_id_string=$rs['getIds'];
        $product_id_array=explode(',', $product_id_string);
        $get_exited_product_relations= $productRelationObj
                                      ->select(DB::raw('group_concat(product_id) as getProductRelatedIds'))
                                      ->whereIn('product_id',$product_id_array)
                                      ->first();
 /* $d=DB::getquerylog();
  print_r(end($d));*/

       /* print_r(json_encode($get_exited_product_relations));
        die();*/
        $get_related_ids=json_decode(json_encode($get_exited_product_relations),true); 
        
        $related_product_id_array=explode(',', $get_related_ids['getProductRelatedIds']);
      //get product titles with out have product relation tbl
      $product_data= $productInfoObj->select('product_id as id','product_title as value')->where('brand_id',$brand_id)->whereNotIn('product_id',$related_product_id_array)->where('product_title', 'like','%'.$searchTerm.'%')->get()->all();
      

         echo json_encode(json_decode($product_data));
    }
    public function getMasterLookUpData($id,$name)
    {
      $returnData = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value','sort_order')
            ->where('master_lookup_categories.mas_cat_id','=',$id)
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=',$name)
             ->get()->all();
      return $returnData;
    }
     public function getMasterLookUpPackageData($id,$name)
    {
      $returnData = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value')
            ->where('master_lookup_categories.mas_cat_id','=',$id)
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=',$name)
            ->orderBy('master_lookup.sort_order', 'asc')
            ->get()->all();
      return $returnData;
    }
   public function getMasterLookUpWeightUom($id,$name)
    {
      $returnData = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value')
            ->where('master_lookup_categories.mas_cat_id','=',$id)
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=',$name)
            ->orderBy('master_lookup.value', 'asc')
            ->get()->all();
      return $returnData;
    }

    public function getManufacturerList($type_id)
    {
    $legalEntityId = Session::get('legal_entity_id');
        if($legalEntityId==0)
        {
            $legalObj = Legalentities::select('legal_entity_id', 'business_legal_name')->where('legal_entity_type_id',$type_id)->get()->all();
        }
        else
        {
            $legalObj = Legalentities::select('legal_entity_id', 'business_legal_name')->where(['legal_entity_type_id'=>$type_id,'parent_id'=>$legalEntityId])->get()->all();
        }
        $manu[''] = '-- Select a Manufacturer --';
        foreach ($legalObj as $obj)
        {
            $manu[$obj->legal_entity_id] = $obj->business_legal_name;
        }
       
        return $manu;
    }

    public function editAction($brandId)
    {
        $breadCrumbs = array('Home' => url('/'), 'Products' => url('/'), 'Brands' => url('/') . '/brands', 'Edit' => '#');
        parent::Breadcrumbs($breadCrumbs);
        Session::forget('brand_id');
        Session::put('brand_id', $brandId);
        $brand = BrandModel::find($brandId);
        $manuLogoObj = Legalentities::select('logo')->where('legal_entity_id',$brand->mfg_id)->first();
        $manuLogo = $manuLogoObj->logo;
        $manu_org_type = $this->getManuCompanytype();
        $categories = $this->categoriesList();
        $returns_location_types = $this->returnsLocationType();
        $manu = $this->getManufacturerList('1006');
        $manufacturer_segments=$this->getManufacturerSegments();
        $Brand_Model_Obj = new BrandModel();
        $purchase_manager = $Brand_Model_Obj->getPurchaseManager();
        //$query  = $Brand_Model_Obj::all();
        //$brands_Data=json_decode(json_encode($query));
        $brands_Data  = $this->returnLegalentityAllBrands();
        return View::make('Manufacturers::manufacturers', ['manu_logo'=>$manuLogo,'manu_org_type'=>$manu_org_type,'brand' => $brand, 'returns_location_types_list' => $returns_location_types, 'category_list' => $categories, 'manufacturerslist' => $manu, 'manufacturer_segments' => $manufacturer_segments, 'brands' => $brands_Data,'purchase_manager'=>$purchase_manager]);
    }

    /**
     * Save Brand
     * @param Request $request
     */
    public function brandSave(Request $request)
    {
        $is_authorized = 0;
        $is_trademarked = 0;
        /*$logo_file = ($request->hasFile('logo')) ? time() . $request->file('logo')->getClientOriginalName() : '';
        if(strstr($logo_file,'http')) {
            $logo_file = $logo_file;   //full path saving in database  
        } else {             
            $logo_file = URL::to('/uploads/brand_logos/'.$logo_file); //full path saving in database 
        }*/
        //$logo_file = URL::to('/uploads/brand_logos/'.$logo_file);  //full path saving in database  
        $user = (Session::get('userId')) ? Session::get('userId') : '';
        $brandId = (Session::get('brand_id')) ? Session::get('brand_id') : 0;
        /*if ($request->hasFile('logo'))
        {
            $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/brand_logos/';
            $request->file('logo')->move($logoPath, $logo_file);
        }*/
		
		//s3
		$file = $request->file('logo');
		$productObj = new ProductRepo();
		if ($request->hasFile('logo'))
        {
		  $s3ImageUrl = $productObj->uploadToS3($file,'brands',1);	
            $s3BrandThumb = $this->thumbnaiImage($file,'brand_thumbnails');
		}		
		
        if ($brandId == 0)
        {
            $this->save($request, $is_authorized, $is_trademarked, $s3ImageUrl, $user,$s3BrandThumb);
        }
        else
        {
           BrandModel::where('brand_id', $brandId)
                    ->update(['legal_entity_id' => Session::get('legal_entity_id'),
                        'mfg_id'=>$request->get('legal_entity_id'),
                        'brand_name' => trim($request->get('brand_name')),
                        'parent_brand_id' => $request->get('brand_id'),
                        'description' => $request->get('brand_desc'),
                        'is_authorized' => $is_authorized,
                        'is_trademark' => $is_trademarked,
                        'is_active' => 1,
                        'updated_by' => $user]);

            if ($request->hasFile('logo'))
            {
                BrandModel::where('brand_id', $brandId)->update(['logo_url' => $s3ImageUrl,'logo_thumbnail'=>$s3BrandThumb]);
            }
            Session::forget('brand_id');
        }
        return @trans('brands.brands.save_brand');
    }

    public function manufacturerSave(Request $request) {
        $legalEntity = new Legalentities();
        $legalEntityId = ($request->get('manu_id')) ? $request->get('manu_id') : '';
        $businessLegalName = ($request->get('legal_entity_id')) ? $request->get('legal_entity_id') : '';
        $businessTypeId = ($request->get('manu_org_type')) ? $request->get('manu_org_type') : '';
        $manu_segment = ($request->get('manu_segment')) ? $request->get('manu_segment') : '';
        $rel_manager = ($request->get('pur_mgr')) ? $request->get('pur_mgr') : '';
        $parentId = (Session::get('legal_entity_id')) ? Session::get('legal_entity_id') : '';
        $userId = (Session::get('userId')) ? Session::get('userId') : '';

        /*$logo_file = ($request->hasFile('logo')) ? time() . $request->file('logo')->getClientOriginalName() : '';        
        if(empty($logo_file)) {
           $logo_file = $request->get('existing_logo');
        }
        
        if(strstr($logo_file,'http')) {
            $logo_file = $logo_file;   //full path saving in database  
        } else {             
            $logo_file = URL::to('/uploads/manufacturer_logos/'.$logo_file); //full path saving in database 
        }
        
        if ($request->hasFile('logo') || $request->hasFile('existing_logo')) {
            $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/manufacturer_logos/';
            $request->file('logo')->move($logoPath, $logo_file);
        }*/
		
		//s3
		$s3ImageUrl = '';
		$file = $request->file('logo');
		$productObj = new ProductRepo();
		if ($request->hasFile('logo'))
        {
			$s3ImageUrl = $productObj->uploadToS3($file,'manufacturers',1);
            $s3ManuThumb = $this->thumbnaiImage($file,'manu_thumbnails');
		}
        if ($legalEntityId) {
            //$img = ($request->get('existing_logo')) ? $request->get('existing_logo') : '';
            if ($s3ImageUrl) {                
                $logo = $s3ImageUrl;
                $where = ['business_legal_name' => $businessLegalName, 'logo' => $logo,'logo_thumbnail' => $s3ManuThumb,
                    'business_type_id' => $businessTypeId, 'parent_id' => $parentId, 'reach_id' => $manu_segment, 'updated_by' => $userId,'rel_manager'=>$rel_manager];
            } else {
                $where = ['business_legal_name' => $businessLegalName,
                    'business_type_id' => $businessTypeId, 'parent_id' => $parentId, 'reach_id' => $manu_segment, 'updated_by' => $userId,'rel_manager'=>$rel_manager];
            }

            $legalEntity->where('legal_entity_id', $legalEntityId)
                    ->update($where);
        } else {
            $legalEntity->reach_id = $manu_segment;
            $legalEntity->business_legal_name = $businessLegalName;
            $legalEntity->logo = $s3ImageUrl;
            $legalEntity->logo_thumbnail = $s3ManuThumb;
            $legalEntity->legal_entity_type_id = '1006';
            $legalEntity->business_type_id = $businessTypeId;
            $legalEntity->parent_id = $parentId;
            $legalEntity->created_by = $userId;
            $legalEntity->rel_manager = $rel_manager;
            $legalEntity->save();
        }
        return @trans('brands.manufacturers.manuf_save');        
    }
    
    public function manufacturerEdit(Request $request)
    {
        $data = $request->all();
        $manuId = key($data);
        $legalEntity = new Legalentities();
        $legalObj = $legalEntity->where('legal_entity_id',$manuId)->first();
        return json_encode(array('manu_org_name'=>$legalObj->business_legal_name,'manu_logo_name'=>$legalObj->logo,'org_type_id'=>$legalObj->business_type_id,'org_segment_id'=>$legalObj->reach_id,'pur_mgr'=>$legalObj->rel_manager));
        //return $legalObj;
    }
    
    
    public function getManuCompanytype()
    {
        $company_data = DB::table('master_lookup_categories')
        ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
        ->select('master_lookup.master_lookup_name as company_type','master_lookup.value as id')
        ->where('master_lookup_categories.mas_cat_name','=','Company Types')
        ->get()->all();
        return $company_data;
    }
    public function save($request, $is_authorized, $is_trademarked, $logo_file, $user,$thumb_file)
    {

        $brandModel = new BrandModel();

        $brandModel->brand_name = ($request->get('brand_name')) ? $request->get('brand_name') : '';
        $existingBrand = BrandModel::where('brand_name', $brandModel->brand_name)->first();
        $brandModel->description = ($request->get('brand_desc')) ? $request->get('brand_desc') : '';
        $brandModel->is_authorized = $is_authorized;
        $brandModel->is_trademark = $is_trademarked;
        $brandModel->legal_entity_id = Session::get('legal_entity_id');
        $brandModel->mfg_id = ($request->get('legal_entity_id')) ? $request->get('legal_entity_id') : '';
        $brandModel->is_active = 1;
        $brandModel->parent_brand_id = ($request->get('brand_id')) ? $request->get('brand_id') : '';
        $brandModel->logo_url = $logo_file;
        $brandModel->logo_thumbnail = $thumb_file;
        $brandModel->created_by = $user;
        $brandModel->save();
    }

    function returnsLocationType()
    {
        $returns_location_types = DB::table('master_lookup_categories')->select('master_lookup.master_lookup_name as location_name', 'master_lookup.value as location_value')
                        ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                        ->where('mas_cat_name', 'returns_location_type')->get()->all();
        return $returns_location_types;
    }
   
    function categoriesList()
    {
        $categories = DB::table('categories')->where('is_active', 1)->get()->all();
        return $categories;
    }
   
  
    
        public function getWarehouseBylegalentity() {
        $LegalentitywarehousesModel = new LegalentitywarehousesModel();
        $legal_entity_id = Session::get('legal_entity_id');
        $legalentity_warehouses = $LegalentitywarehousesModel->where('legal_entity_id', $legal_entity_id)->get()->all();
        return $legalentity_warehouses;
    }

    
    // product preview

    public function getBrandProducts($brand_id){
      try {
        $products = DB::table('products')->where('brand_id',$brand_id)->select('product_id','product_title')->get()->all();
        return $products;
      }  catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

     
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        try
        {

            $Legalentity_Id = $request->legalentity_id;

            SupplierModel::where('legal_entity_id', $Legalentity_Id)->delete();
        }
        catch (\ErrorException $ex)
        {

            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

   public function getManufacturers(Request $request)
   {

        try
        {

            $this->grid_field_db_match = array('Manufacturer' => 'user_name', 'Contact' => 'contact', 'SRM' => 'rel_manager', 'Brands' => 'Brands', 'Products' => 'Products', 'Warehouses' => 'warehouses', 'Documents' => 'Documents',
                'Created_By' => 'created_by', 'Created_On' => 'created_at');

            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;

            $sm = new VwManagemanufacturerModel();


            $legalEntityIdArray = array();

            $loggedInuserId = Session::get('userId');
            //$legal_entity_id = DB::table('users')->select('legal_entity_id')->where('user_id', $loggedInuserId)->get()->all();
            $legal_entity_id = Session::get('legal_entity_id');//$legal_entity_id[0]->legal_entity_id;


            if ($legal_entity_id == 0)
            {
                $query = $sm::select(['logo as ManufacturerLogo', 'manufacturer_id as ManufacturerID', 'user_name as Manufacturer', 'contact as Contact', 'rel_manager as SRM', 'Brands as Brands', 'Products as Products', 'Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On']);
            }
            else
            {

                $query = $sm::select(['logo as ManufacturerLogo', 'manufacturer_id as ManufacturerID', 'user_name as Manufacturer', 'contact as Contact', 'rel_manager as SRM', 'Brands as Brands', 'Products as Products', 'Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On'])->where('manufacturer_id', $legal_entity_id);
            }


            if ($request->input('$orderby'))
            {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc')
                {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match[$order_query_field]))
                { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter'))
            {           //checking for filtering
                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


                foreach ($post_filter_query as $post_filter_query_sub)
                {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);

                    $filter_query_field = $filter[0];
                    $filter_query_operator = $filter[1];
                    $filter_query_value = $filter[2];

                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower')
                    {
                        //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                        if ($filter_query_substr == 'startsw')
                        {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = $filter_value_array[1] . '%';


                            foreach ($this->grid_field_db_match as $key => $value)
                            {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0)
                                {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                                }
                            }
                        }


                        if ($filter_query_substr == 'endswit')
                        {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = '%' . $filter_value_array[1];


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match as $key => $value)
                            {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0)
                                {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'tolower')
                        {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq')
                            {
                                $like = '=';
                            }
                            else
                            {
                                $like = '!=';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match as $key => $value)
                            {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0)
                                {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof')
                        {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge')
                            {
                                $like = 'like';
                            }
                            else
                            {
                                $like = 'not like';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match as $key => $value)
                            {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0)
                                {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                                }
                            }
                        }
                    }
                    else
                    {

                        switch ($filter_query_operator)
                        {
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


                        if (isset($this->grid_field_db_match[$filter_query_field]))
                        { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }

            $row_count = count($query->get()->all());

            $query->skip($skip)->take($pageSize);

            $Manage_Manufacturers = $query->get()->all();


            /* $totDocsList = DB::table('documents_master')->select('business_type_id','country',DB::raw('COUNT(is_doc_required) as doc_count'))->where('is_doc_required',1) 
              ->groupBy('country','business_type_id')->get()->all();
              $docsArray = array();
              foreach($totDocsList as $obj)
              {
              $docsArray[$obj->business_type_id][$obj->country] = $obj->doc_count;
              } */

            foreach ($Manage_Manufacturers as $k => $list)
            {

                $totDocs = 6; //$docsArray[$businesstypeIdValue][$countryId];         
                $Manage_Manufacturers[$k]['Documents_count'] = $Manage_Manufacturers[$k]['Documents'] . '/' . $totDocs;

                //$SRM = $Manage_Suppliers[$k]['SRM'];
                //$SRM_Name = DB::table('users')->select(DB::raw('CONCAT(firstname," ", lastname) as user_name'))->where('user_id','=',$SRM)->get()->all();


                if ($Manage_Manufacturers[$k]['ManufacturerLogo'] != '' && file_exists(public_path('uploads/Suppliers_Docs/' . $Manage_Manufacturers[$k]['ManufacturerLogo'])))
                {
                    $Manage_Manufacturers[$k]['ManufacturerLogo'] = "<img src='/uploads/Suppliers_Docs/" . $Manage_Manufacturers[$k]['ManufacturerLogo'] . "' height='33' width='100' />";
                }
                else
                {
                    $Manage_Manufacturers[$k]['ManufacturerLogo'] = "<img src='/uploads/Suppliers_Docs/notfound.png' height='33' width='100' />";
                }
                if ($list['Status'] == '1')
                {
                    $Manage_Manufacturers[$k]['Status_checked'] = '<i class="fa fa-check"></i>';
                }
                else
                {
                    $Manage_Manufacturers[$k]['Status_checked'] = '<i class="fa fa-times"></i>';
                }

                /* if(isset($SRM_Name[0]->user_name))
                  {
                  $Manage_Suppliers[$k]['SRM'] = $SRM_Name[0]->user_name;
                  }
                  else
                  {
                  $Manage_Suppliers[$k]['SRM'] = '';
                  } */

                $Manage_Manufacturers[$k]['Action'] = '<a data-toggle="modal" href="brands/edit/' . $Manage_Manufacturers[$k]['ManufacturerID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="deleteManufacturer" href="' . $Manage_Manufacturers[$k]['ManufacturerID'] . '"> <i class="fa fa-trash-o"></i> </a>';
            }

            echo json_encode(array('Records' => $Manage_Manufacturers, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getManfBrandsFromView(Request $request) {
       try {

            $this->grid_field_db_match = array(
                'BrandName'          => 'vw_brand_details.brand_name',
                'ManufacturerName'   => 'vw_brand_details.manufacturer_name',
                'Products'           => 'vw_brand_details.Products',
                'With_Images'        => 'vw_brand_details.withImages',
                'Without_Images'     => 'vw_brand_details.withoutimages',
                'With_Inventory'     => 'vw_brand_details.withinventory',
                'Without_Inventory'  => 'vw_brand_details.withoutinventory',
                'Approved'           => 'vw_brand_details.approved',
                'Pending'            => 'vw_brand_details.pending'
            );


            $Brand_Model_Obj = new VwManfBrandsModel();


            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;



            $legalEntityIdArray = array();

            $loggedInuserId = Session::get('userId');
            $legal_entity_id = Session::get('legal_entity_id');
            $child_legal_entity_id = DB::table('legal_entities')->select('legal_entity_id')->where(['parent_id' => $legal_entity_id, 'legal_entity_type_id' => '1006'])->get()->all();
            foreach ($child_legal_entity_id as $val) {
                $legalEntityIdArray[] = $val->legal_entity_id;
            }


            if ($legal_entity_id == 0) {
                $query = $Brand_Model_Obj::select(['brand_id as BrandID', 'brand_log as BrandLogo', 'brand_name as BrandName',
                            'manufacturer_logo as ManufacturerLogo', 'manufacturer_name as ManufacturerName', 'IS Trademarked',
                            'Authorised as Authorised', 'Products as Products', 'manufacturer_id', 'WithImages as With_Images',
                            'WithoutImages as Without_Images',
                            'withInventory as With_Inventory', 'WithoutInventory as Without_Inventory', 'approved as Approved',
                            'pending as Pending']);
            } else {

                $rolesObj = new Role();
                $brandFilter = $rolesObj->getFilterData(7, Session::get('userId'));
                $brandFilter = json_decode($brandFilter);
                if ($brandFilter->brand) {
                    $brandList = (array) $brandFilter->brand;
                }

                $brandvalues = '';
                if(!empty($brandList)) {
                    $brandvalues = array_flip($brandList);  
                }

                $query = $Brand_Model_Obj::select(['brand_id as BrandID', 'brand_log as BrandLogo', 'brand_name as BrandName',
                            'manufacturer_logo as ManufacturerLogo', 'manufacturer_name as ManufacturerName', 'IS Trademarked',
                            'Authorised as Authorised', 'Products as Products', 'manufacturer_id', 'WithImages as With_Images',
                            'WithoutImages as Without_Images',
                            'withInventory as With_Inventory', 'WithoutInventory as Without_Inventory', 'approved as Approved',
                            'pending as Pending'])->whereIn('manufacturer_id', $legalEntityIdArray);
                $query->whereIn('brand_id', $brandvalues);
            }

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


            $row_count = count($query->get()->all());


            $query->skip($skip)->take($pageSize);


            $Manage_Brands = $query->get()->all();


            foreach ($Manage_Brands as $k => $list) {

                if ($list['IS Trademarked'] == '1') {
                    $Manage_Brands[$k]['Trademarked'] = '<i class="fa fa-check"></i>';
                } else {
                    $Manage_Brands[$k]['Trademarked'] = '<i class="fa fa-times"></i>';
                }

                if ($list['Authorised'] == '1') {
                    $Manage_Brands[$k]['is_authorised'] = '<i class="fa fa-check"></i>';
                } else {
                    $Manage_Brands[$k]['is_authorised'] = '<i class="fa fa-times"></i>';
                }

                //$Manage_Brands[$k]['Action'] = '<a data-toggle="modal" class="editBrand" href="brands/edit/' . $Manage_Brands[$k]['BrandID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="deleteBrand" href="' . $Manage_Brands[$k]['BrandID'] . '"> <i class="fa fa-trash-o"></i> </a>';

                $edit_brand     = $this->_roleRepo->checkPermissionByFeatureCode('BRAND003');
                $delete_brand   = $this->_roleRepo->checkPermissionByFeatureCode('BRAND004');
                
                $Manage_Brands[$k]['Action']='';
                
                if($edit_brand == 1) {                    
                    $Manage_Brands[$k]['Action']  = '<a data-toggle="modal" class="editBrand" href="brands/edit/' . $Manage_Brands[$k]['BrandID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';
                }
                if($delete_brand == 1) {
                    $Manage_Brands[$k]['Action'] .= '<a class="deleteBrand" href="' . $Manage_Brands[$k]['BrandID'] . '"> <i class="fa fa-trash-o"></i> </a>';
                }

                 if ($Manage_Brands[$k]['BrandName'] != '') {
                    if (strstr($Manage_Brands[$k]['BrandLogo'], 'http')) {
                        $Manage_Brands[$k]['BrandName'] = "<img src='" . $Manage_Brands[$k]['BrandLogo'] . "' height='auto' width='32' /> &nbsp;&nbsp;&nbsp;&nbsp;" . $list->BrandName ;
                    } else {
                        $Manage_Brands[$k]['BrandName'] = "<img src='/uploads/brand_logos/" . $Manage_Brands[$k]['BrandLogo'] . "' height='auto' width='32' /> &nbsp;&nbsp;&nbsp;&nbsp;" . $list->BrandName ;
                    }
                } else {
                    $Manage_Brands[$k]['BrandName'] = "<img src='/uploads/brand_logos/notfound.png' height='auto' width='32' />";
                }

                if ($Manage_Brands[$k]['ManufacturerName'] != '') {
                    if (strstr($Manage_Brands[$k]['ManufacturerLogo'], 'http')) {
                        $Manage_Brands[$k]['ManufacturerName'] = "<img src='" . $Manage_Brands[$k]['ManufacturerLogo'] . "' height='auto' width='32' /> &nbsp;&nbsp;&nbsp;&nbsp;" . $list->ManufacturerName;
                    } else {
                        $Manage_Brands[$k]['ManufacturerName'] = "<img src='/uploads/manufacturer_logos/" . $Manage_Brands[$k]['ManufacturerLogo'] . "' height='auto' width='32' /> &nbsp;&nbsp;&nbsp;&nbsp;" . $list->ManufacturerName;
                    }
                } else {
                    $Manage_Brands[$k]['ManufacturerName'] = "<img src='/uploads/Suppliers_Docs/notfound.png' height='auto' width='32' /> &nbsp;&nbsp;&nbsp;&nbsp;" . $list->ManufacturerName;
                }
            }


            if(!empty($Manage_Brands) ) {
                echo json_encode(array('Records' => $Manage_Brands, 'TotalRecordsCount' => $row_count));
            } else {
                echo json_encode(array('Records' => '[]', 'TotalRecordsCount' => '[]'));
            }
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function getManfProductsFromView(Request $request)
    {



        try
        {


            $path = explode('/', $request->input('path'));
            //$suppl_path_temp = explode(':',$path[0]);


            $path_temp = explode(':', $path[0]);

            $BrandID = $path_temp[1];

            //$Manufacturer_ID = $suppl_path_temp[1];


            $Product_Model_Obj = new VwManfProductsModel();


            /* $query = $Product_Model_Obj::select(['product_id as Product_ID','image as ProductLogo','category_name as Category','product_name as Product_Name','mrp as MRP','base_price as BasePrice','EBP as EBP','RBP as RBP','CBP as CBP','inventorymode as Inventory_Mode','Schemes','status as Status','MBQ as MPQ']); */


            $query = $Product_Model_Obj::select(['product_id as Product_ID', 'image as ProductLogo', 'brand_logo as Brand', 'product_title as Product_Name', 'product_title as Product_Title', 'seller_sku as SKU', 'upc as UPC', 'suppliercnt as Supplier_Count', 'pack_size as Weight',
                        'pack_size_uom as UoM','created_by as Created_By', 'created_at as Created_On', 'approved_by as Approved_By', 'approved_at as Approved_On']);

            $query->where('brand_id', '=', $BrandID);
            //$query->where('manufacturer_id','=',$Manufacturer_ID);

            $Manage_Products = $query->get()->all();


            foreach ($Manage_Products as $k => $list)
            {
                
                                    $UoM = '';
                                    if($Manage_Products[$k]['UoM'])
                                    {
                                    $UoM = $Manage_Products[$k]['UoM'];
                                    }
                                    $Manage_Products[$k]['Weight'] = round($Manage_Products[$k]['Weight'],2).' '.$UoM;  
                                        
                
                $Manage_Products[$k]['Logos'] = '<img src=' . $Manage_Products[$k]['Logo'] . '/>';
                $brandPath = url('/') . '/uploads/brand_logos/' . $Manage_Products[$k]['Brand'];
                $Manage_Products[$k]['Action'] = '<a data-toggle="modal" href="/productpreview/' . $Manage_Products[$k]['Product_ID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp;&nbsp;<a data-toggle="modal" href="/editproduct/' . $Manage_Products[$k]['Product_ID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="deleteProduct" href="' . $Manage_Products[$k]['Product_ID'] . '"> <i class="fa fa-trash-o"></i> </a>';


                if ($Manage_Products[$k]['ProductLogo'] != '')
                {
                    $Manage_Products[$k]['ProductLogo'] = "<img src='".$Manage_Products[$k]['ProductLogo'] . "' height='48' width='48' />";
                }
                else
                {
                    $Manage_Products[$k]['ProductLogo'] = "<img src='/uploads/products/notfound.png' height='48' width='48' />";
                }

                if ($Manage_Products[$k]['BrandLogo'] != '' && file_exists(public_path('uploads/brand_logos/' . $Manage_Products[$k]['BrandLogo'])))
                {
                    $Manage_Products[$k]['BrandLogo'] = "<img src='/uploads/brand_logos/" . $Manage_Products[$k]['BrandLogo'] . "' height='33' width='100' />";
                }
                else
                {
                    $Manage_Products[$k]['BrandLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                }
            }


            $row_count = $Product_Model_Obj->count();
            echo json_encode(array('Records' => $Manage_Products, 'TotalRecordsCount' => $row_count));
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    /**
     * get product info from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProducts(Request $request, $Supplier_Id)
    {

        //start


        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        $pm = new ProductModel();





        $query = $pm::select(['products.product_id as ProductID', 'products.primary_image as Logo', 'brands.logo_url as Brand', 'products.product_title as ProductTitle', 'products.product_title as ProductName', 'products.seller_sku as SKU', 'products.upc as UPC', 'products.pack_size as Weight',
                    'products.created_by as CreatedBy', 'products.created_at as CreatedOn', 'products.approved_by as ApprovedBy', 'products.approved_at as ApprovedOn']);

        $query->leftjoin('brands', 'brands.brand_id', '=', 'products.brand_id');
        //$query->leftjoin('categories','categories.category_id','=','products.category_id');
        //$query->leftjoin('product_tot','product_tot.product_id','=','products.product_id');

        $query->where('products.manufacturer_id', '=', $Supplier_Id);

        if ($request->input('$orderby'))
        {    //checking for sorting
            $order = explode(' ', $request->input('$orderby'));

            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc

            $order_by_type = 'desc';

            if ($order_query_type == 'asc')
            {
                $order_by_type = 'asc';
            }

            if (isset($this->grid_field_db_match[$order_query_field]))
            { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }


        if ($request->input('$filter'))
        {           //checking for filtering
            $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


            foreach ($post_filter_query as $post_filter_query_sub)
            {    //looping through each filter
                $filter = explode(' ', $post_filter_query_sub);

                $filter_query_field = $filter[0];
                $filter_query_operator = $filter[1];
                $filter_query_value = $filter[2];

                $filter_query_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower')
                {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                    if ($filter_query_substr == 'startsw')
                    {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = $filter_value_array[1] . '%';


                        foreach ($this->grid_field_db_match as $key => $value)
                        {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }


                    if ($filter_query_substr == 'endswit')
                    {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = '%' . $filter_value_array[1];


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value)
                        {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }




                    if ($filter_query_substr == 'tolower')
                    {

                        $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = $filter_value_array[1];

                        if ($filter_query_operator == 'eq')
                        {
                            $like = '=';
                        }
                        else
                        {
                            $like = '!=';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value)
                        {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }

                    if ($filter_query_substr == 'indexof')
                    {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_value_array[1] . '%';

                        if ($filter_query_operator == 'ge')
                        {
                            $like = 'like';
                        }
                        else
                        {
                            $like = 'not like';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value)
                        {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }
                }
                else
                {

                    switch ($filter_query_operator)
                    {
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


                    if (isset($this->grid_field_db_match[$filter_query_field]))
                    { //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }

                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }

//            $query->groupBy('products.product_id');
//            echo $page*$pageSize;exit;

        $row_count = count($query->get()->all());


        $query->skip($skip)->take($pageSize);
        $products_list = $query->get()->all();


        foreach ($products_list as $k => $list)
        {
            //echo $products_list[$k]['Brand']; die;
            $products_list[$k]['Logos'] = '<img width="90" height="33" src=' . $products_list[$k]['Logo'] . '/>';
            $brandPath = url('/') . '/uploads/brand_logos/' . $products_list[$k]['Brand'];
            $products_list[$k]['BrandLogo'] = '<img width="90" height="33" src=' . $brandPath . '>';
            $products_list[$k]['Weight'] = number_format($products_list[$k]['Weight'], 2);
            $products_list[$k]['Action'] = '<a data-toggle="modal" href="/productpreview/' . $products_list[$k]['ProductID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="deleteProduct" href="' . $products_list[$k]['ProductID'] . '"> <i class="fa fa-trash-o"></i> </a>';
        }

//            echo $query->toSql();
        echo json_encode(array('Records' => $products_list, 'TotalRecordsCount' => $row_count));

        //end
    }

    /**
     * get brands info from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBrands(Request $request, $Legal_Entity_Id)
    {

        //start

        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        $bm = new BrandModel();

        $query = $bm::select(['brands.brand_id as BrandID', 'brands.brand_name as BrandName', 'brands.description as Description', DB::raw("count(products.product_id) as Products"), 'brands.is_authorized as Authorized', 'brands.is_trademark as Trademark']);

        $query->leftjoin('products', 'products.brand_id', '=', 'brands.brand_id');

        $query->where('brands.legal_entity_id', '=', $Legal_Entity_Id);

        if ($request->input('$orderby'))
        {    //checking for sorting
            $order = explode(' ', $request->input('$orderby'));


            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc

            $order_by_type = 'desc';

            if ($order_query_type == 'asc')
            {
                $order_by_type = 'asc';
            }


            if (isset($this->grid_field_db_match[$order_query_field]))
            { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }


        if ($request->input('$filter'))
        {           //checking for filtering
            $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


            foreach ($post_filter_query as $post_filter_query_sub)
            {    //looping through each filter
                $filter = explode(' ', $post_filter_query_sub);

                $filter_query_field = $filter[0];
                $filter_query_operator = $filter[1];
                $filter_query_value = $filter[2];

                $filter_query_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower')
                {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                    if ($filter_query_substr == 'startsw')
                    {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = $filter_value_array[1] . '%';


                        foreach ($this->grid_field_db_match as $key => $value)
                        {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }


                    if ($filter_query_substr == 'endswit')
                    {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = '%' . $filter_value_array[1];


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value)
                        {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }




                    if ($filter_query_substr == 'tolower')
                    {

                        $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = $filter_value_array[1];

                        if ($filter_query_operator == 'eq')
                        {
                            $like = '=';
                        }
                        else
                        {
                            $like = '!=';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value)
                        {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }

                    if ($filter_query_substr == 'indexof')
                    {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_value_array[1] . '%';

                        if ($filter_query_operator == 'ge')
                        {
                            $like = 'like';
                        }
                        else
                        {
                            $like = 'not like';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value)
                        {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }
                }
                else
                {

                    switch ($filter_query_operator)
                    {
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


                    if (isset($this->grid_field_db_match[$filter_query_field]))
                    { //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }

                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }

        $query->groupBy('brands.brand_id');

        $row_count = count($query->get()->all());

//            echo $page*$pageSize;exit;
        $query->skip($skip)->take($pageSize);
        $brands_list = $query->get()->all();


        foreach ($brands_list as $k => $list)
        {
            $brands_list[$k]['Action'] = '<a data-toggle="modal" class="editBrand" href="' . $brands_list[$k]['BrandID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="deleteBrand" href="' . $brands_list[$k]['BrandID'] . '"> <i class="fa fa-trash-o"></i> </a>';
        }

        echo json_encode(array('Records' => $brands_list, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);

        //end
    }

   
    public function brandList(Request $request)
    {   
       $checkBrandPermissions=$this->_roleRepo->checkPermissionByFeatureCode('BRAND001');
        if ($checkBrandPermissions==0)
        {
            return Redirect::to('/');
        }
        $breadCrumbs = array('Home' => url('/'), 'Products' => url('/'), 'Brands' => '#');
        parent::Breadcrumbs($breadCrumbs);
        $returns_location_types = DB::table('master_lookup_categories')->select('master_lookup.master_lookup_name as location_name', 'master_lookup.value as location_value')
                        ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                        ->where('mas_cat_name', 'returns_location_type')->get()->all();
        $categories = DB::table('categories')->where('is_active', 1)->get()->all();
        $add_brand = $this->_roleRepo->checkPermissionByFeatureCode('BRAND002');
         return View::make('Manufacturers::manufacturerlist', ['category_list' => $categories, 
          'returns_location_types_list' => $returns_location_types, 'add_brand' => $add_brand]);

    }

    /**
     * desc : Deletes brands
     * @return success
     */
    public function deleteBrandAction($brand_id)
    {

        $Brand_Model_Obj = new BrandModel();

        //$query = $Brand_Model_Obj::select('legal_entity_id')->where('brand_id', $brand_id)->get()->all();       
        //$manufacturerId = $query[0]->legal_entity_id;
        $productCount = ProductModel::where('brand_id',$brand_id)->count();

        if($productCount)
        {
            return '0';
        }
        else
        {
        $brand = $Brand_Model_Obj::where('brand_id', $brand_id);   
        $brand->delete();
        return '1';            
        }
    }

    /**
     * desc : Deletes products
     * @return success
     */
    public function deleteProductAction($product_id)
    {  
        try{                  
            //$get_product_Id = DB::table('indent_products')->where('product_id', $product_id)->pluck('product_id')->all();
            //print_R($get_product_Id);die;
            //if(empty($get_product_Id)) { 
                $productMethod = new ProductController();
                $productMethod->destroy($product_id);             
            //} else {
            //    return '1';
            //}           
       } catch (\ErrorException $ex) {           
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function editBrandAction($Brand_ID)
    {
        $Brand_Model_Obj = new BrandModel();
        $query = $Brand_Model_Obj->where('brand_id', $Brand_ID)->get()->all();

        return $query;
    }

    public function editProductAction($Product_ID)
    {

        $Product_Model_Obj = new ProductModel();

        $query = $Product_Model_Obj::select(['products.product_id', 'brands.brand_name', 'categories.category_id', 'products.product_name', 'product_content.short_description', 'product_content.title', 'products.seller_sku', 'product_tot.mrp', 'product_tot.msp', 'product_tot.rlp', 'product_tot.dlp', 'product_tot.cbp', 'product_tot.credit_days', 'product_tot.return_location_type', 'product_tot.delivery_terms', 'product_tot.is_return_accepted', 'product_tot.base_price', 'products.upc', 'product_tot.vat',
                    'product_tot.cst', 'product_tot.is_markup', 'product_tot.inventory_mode', 'products.is_active']);

        $query->leftjoin('brands', 'brands.brand_id', '=', 'products.brand_id');
        $query->leftjoin('categories', 'categories.category_id', '=', 'products.category_id');
        $query->leftjoin('product_tot', 'product_tot.product_id', '=', 'products.product_id');
        $query->leftjoin('product_content', 'product_content.product_id', '=', 'products.product_id');

        $query->where('products.product_id', '=', $Product_ID);

        $result = $query->get()->all();

        return $result;
    }
    
    
    /**
     * approval Save
     * @param Request $request
     */
    public function approvalSave(Request $request)
    {
        $current_time = Carbon::now()->toDateTimeString();
        $approvalCommonFlow = new CommonApprovalFlowFunctionModel();        
        $approvalForId = $request->get('approval_for_id');
        $approvalTypeId = $request->get('approval_type_id');        
        $saveAprovalModel = new ProductModel();
        $status_code = $saveAprovalModel->approvalSave($approvalTypeId, $approvalForId);              
        $userId  = Session::get('userId');
        $result = $approvalCommonFlow->getApprovalFlowDetails($approvalTypeId, $status_code, $userId);
                
        $approvalSring = $result;
        $prevStatus = (isset($approvalSring['currentStatusId']))?$approvalSring['currentStatusId']:$status_code;        
        $comments = $request->get('approval_comments');
        $status = $request->get('approval_select_id');
        $where = ['status'=>$status,'approved_by'=>$userId];
        $flag = 0;
        
        $data = (isset($approvalSring['data']))?$approvalSring['data']:array();
        if(is_array($data))
        {
            foreach($data as $value)
            {
                if($value['nextStatusId'] == $status && $value['isFinalStep'] == '1')
                {
                  $flag = 1;
                }
            }
        }
                
        switch($approvalTypeId)
        {
            case 'Product PIM':
                if($flag == 1){
                  $where['status'] = 1;
                }
                $where['is_approved'] = $flag;
                $where['is_active'] = $flag;
                //$where['cp_enabled'] = $flag;
                $where['approved_at'] = $current_time;
                ProductModel::where('product_id',$approvalForId)->update($where);
                break;
            case 'Supplier':
                $where['is_approved'] = $flag;
                $where['approved_at'] = $current_time;
                SupplierModel::where('legal_entity_id',$approvalForId)->update($where);
                $fields = array('status_id' => $status, 'is_approved' => $flag, 'approved_by' => $userId, 'approved_at' => $current_time);
                Legalentity::where('legal_entity_id', $approvalForId)->update($fields);       
                break;
           case 'Purchase Order':
                $poFields = array('is_approved'=>$flag, 'approved_at'=>$current_time, 'approval_status'=>$status, 'approved_by'=>$userId);
                PurchaseOrder::where('po_id', $approvalForId)->update($poFields);
                break;     
           case 'Retailer':
                $fields = array('status_id' => $status, 'is_approved' => $flag, 'approved_by' => $userId, 'approved_at' => $current_time);
                Legalentity::where('legal_entity_id', $approvalForId)->update($fields);
                break;   
      case 'GRN':
        $inwardFields = array('is_approved'=>$flag, 'approved_at'=>$current_time, 'approval_status'=>$status, 'approved_by'=>$userId);
                Inward::where('inward_id', $approvalForId)->update($inwardFields);
                break;  
      case 'Purchase Return':
        $prFields = array('is_approved'=>$flag, 'approved_at'=>$current_time, 'approval_status'=>$status, 'approved_by'=>$userId);
                PurchaseReturn::where('pr_id', $approvalForId)->update($prFields);
                break;
        }
                
        $approvalCommonFlow->storeWorkFlowHistory($approvalTypeId, $approvalForId, $prevStatus, $status, $comments, $userId);
        
        //print_r($where); die;
    }


    public function manfuniq(Request $request)
    {
        $manu_id = $request->input('manu_id');
        $man_name = $request->input('manu_org_name');
        $legal_entity_id = Session::get('legal_entity_id');
        $Legalentities = new Legalentities();
        
        $manu = $this->getManufacturerList('1006');
        $manu = json_decode(json_encode($manu),true);
        
        $duplicate_manu = '';
        foreach($manu as $key=>$value)
        {
            if($value == $man_name)
            {
                $duplicate_manu = $value;
            }
        }
        
        $where = ['business_legal_name'=>$man_name];
        $whexists = $Legalentities->where($where)->first();
        if(isset($whexists) && isset($manu_id) && $manu_id > 0 && $whexists->legal_entity_id != $manu_id && isset($duplicate_manu) && $duplicate_manu != '')
        {
            echo "true";
        }
        else if(isset($whexists) && empty($manu_id) && isset($whexists->legal_entity_id) && isset($duplicate_manu) && $duplicate_manu != '')
        {
            echo "false";
        }
        else
        {
            echo "true";
        }
    }
    
    public function brandfuniq(Request $request) {
        $brand_name = $request->input('brand_name');
        $legal_entity_id = Session::get('legal_entity_id');        
        $brand_data = DB::table('brands')->select('brand_id')->where('legal_entity_id', $legal_entity_id)->where('brand_name', $brand_name)->first();        
        if(!empty($brand_data)) { 
         $brand_id = $brand_data->brand_id;
        }        
        $count = '';
        $count1 = '';
        if(isset($brand_id) && !empty($brand_id)) { 
        $count = DB::table('brands')->where('legal_entity_id', $legal_entity_id)
                ->where('brand_name', $brand_name)
                ->where('brand_id', Session::get('brand_id'))
                ->count();        
        //$count1 = DB::table('brands')->where('legal_entity_id', $legal_entity_id)->where('brand_id', $brand_id)->where('brand_name', $brand_name)->count();
            if(!empty(Session::get('brand_id'))) {
                if($count == 1) {
                echo "true";
                } else {
                    echo "false";
                }               
            } else {
               echo "false"; 
            }                                 
        } else  {
            echo "true";
        }

    }   
    public function thumbnaiImage($file,$s3Folder) 
    {
        $thumbnail_path = 'uploads/thumbnail_images/';

        //Get real extension according to mime type
        $ext                = $file->guessClientExtension();  
        // Client file name, including the extension of the client getPrimaryImage
        $fullname           = $file->getClientOriginalName(); 
        // Hash processed file name, including the real extension
        $hashname           = time().'.'.$ext; 

        $upload_success     = $file->move($thumbnail_path, $hashname);

        $thumbnail_name = 'brand_thumbnail_img'.$hashname;

        $thumbnail = Image::open($thumbnail_path . $hashname)

                ->thumbnail(new Imagine\Image\Box(270, 90));

        $thumbnail->save($thumbnail_path . $thumbnail_name);
		$thumb = new ProductRepo();
		$s3ImageUrl2 = $thumb->uploadToS3($thumbnail_path . $thumbnail_name, $s3Folder, 2);
		
        return $s3ImageUrl2;
    }
  
}
