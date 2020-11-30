<?php

namespace App\Modules\Product\Controllers;

use App\Modules\Roles\Models\Role;
use App\Http\Controllers\BaseController;
use App\Modules\Pricing\Models\pricingDashboardModel;
use Session;
use View;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Hash;
use App\Modules\Supplier\Models\Suppliers;
use Redirect;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\RoleRepo;
use App\models\MasterLookup\MasterLookup;
use App\Modules\Manufacturers\Models\VwManfProductsModel;
use App\Modules\Product\Models\ProductAttributes;
use App\Modules\Product\Models\ProductCharacteristic;
use App\Modules\Product\Models\ProductContent;
use App\Modules\Product\Models\ProductMedia;
use App\Modules\Product\Models\ProductPolicies;
use App\Modules\Product\Models\ProductModel;
use App\Modules\Product\Models\ProductTOT;
use App\Modules\Supplier\Models\BrandModel;
use App\Modules\Product\Models\CategoryEloquentModel;
use App\Modules\Product\Models\ProductInfo;
use App\Modules\Supplier\Models\SupplierModel;
use App\Modules\Product\Models\ProductRelations;
use App\Modules\Product\Models\ProductPackConfig;
use App\Modules\Product\Models\AttributesModel;
use App\Modules\Product\Models\ProductFreeBie;
use App\Modules\Product\Models\LegalEntityModel;
use App\Modules\Tax\Models\ClassTaxMap;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Supplier\Models\ZoneModel;
use UserActivity;
use App\Modules\Product\Models\ProductGroup;
use App\Modules\Product\Models\ProductEPModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Excel;
use Illuminate\Support\Facades\Config;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Mail;
use File;
  /* * ***Use for only queing and the session mangement****** */
use App\Lib\Queue;
use App\models\Mongo\MongoDmapiModel;
use App\Modules\Product\Models\ProductHistory;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;

class ProductController extends BaseController {

    private  $categoryList; 
    public function __construct() {
       try {
            parent::__construct();
            $this->objPricing = new pricingDashboardModel();
            $this->productModel = new ProductModel();
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                $this->categoryList;
                /*if (!Session::has('userId')) {
                    return Redirect::to('/');
                }*/
                //For Raback
                $this->_roleRepo = new RoleRepo();
                // to get Pricing object    
                parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('products.titles.products'));
                $this->approvalCommonFlow = new CommonApprovalFlowFunctionModel();
                $this->userId = Session::get('userId');
                $this->grid_field_db_match = array(
                    'ProductID' => 'products.product_id',
                    'BrandName' => 'brands.brand_name',
                    'Category' => 'products.category_id',
                    'ProductName' => 'products.product_title',
                    'MRP' => 'product_tot.mrp',
                    'MSP' => 'product_tot.msp',
                    'Bestprice' => 'product_tot.base_price',
                    'VAT' => 'product_tot.vat',
                    'CST' => 'product_tot.cst',
                    'EBP' => 'product_tot.cst',
                    'RBP' => 'product_tot.cst',
                    'CBP' => 'product_tot.cst',
                    'InventoryMode' => 'product_tot.inventory_mode',
                    'Status' => 'products.is_active',
                    'BrandID' => 'brands.brand_id',
                    'Description' => 'brands.description',
                    'Authorized' => 'brands.is_authorized',
                    'Trademark' => 'brands.is_trademark',
                    'business_legal_name' => 'le.business_legal_name',
                    'group_repo' => 'pr.product_title',
                    'brand_name' => 'br.brand_name',
                    'cat_name' => 'ct.cat_name',
                    'cp_enabled' => 'pr.cp_enabled'
                );
                $this->grid_field_db_match_grouprepo = array(
                    'mfg_name' => 'le.business_legal_name',
                    'group_repo' => 'pr.product_title',
                    'brand_name' => 'br.brand_name',
                    'cat_name' => 'ct.cat_name'
                );
                //dd(session()->all());
                if(!Session::get('warehouseId'))
                {
                    $rolesObj = new Role();
                    $user_id=Session::get('userId');
                    $is_supplier=$rolesObj->getPermissionsByUser($user_id,5);
                    $is_brand=$rolesObj->getPermissionsByUser($user_id,7);
                    $is_manufacturer=$rolesObj->getPermissionsByUser($user_id,11);

                    if(!empty($is_supplier) || !empty($is_brand) || !empty($is_manufacturer)){
                        Session::set('warehouseId',0);
                    }else{
                    $dcs = $this->_roleRepo->getAllDcs($user_id);
                    $wh_id=isset($dcs[0]->le_wh_id)?$dcs[0]->le_wh_id:""; 
                    Session::set('warehouseId',$wh_id);
                    }
                }
                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    //this is for Rback

    public function getProductsList($brand_id) {
        $rolesObj = new Role();
        $ProductModelObj = new ProductModel();
        $productList = '<option value="0">Please Select Product ...</option>';
        $DataFilter = $rolesObj->getFilterData(9, Session::get('userId'));
        $DataFilter = json_decode($DataFilter, true);
        $parent_id = Input::get('parent_id');
        $list = isset($DataFilter['products']) ? $DataFilter['products'] : [];
        /* print_r(array_keys($list)); die(); */
        $productData = $ProductModelObj->where('brand_id', $brand_id)->where('is_parent', 1)->whereIn('product_id', array_keys($list))->get()->all();
        foreach ($productData as $value) {
            if ($parent_id != $value->product_id) {
                $productList.= '<option value="' . $value->product_id . '" >' . $value->product_title . '</option>';
            }
        }
        return $productList;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAction() {
        try {

            $breadCrumbs = array('Dashboard' => url('/'), 'Products' => '#');
            parent::Breadcrumbs($breadCrumbs);

            return View::make('Product::index');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
  
    public function updateInventory(Request $request,$product_id)
    {
        $soh= $request->soh;
        $atp= $request->atp;
        $rs= DB::table('inventory')
            ->where('product_id',$product_id)
            ->update(['soh' => $soh,'atp'=>$atp]);
        $inventoryData= DB::table('inventory')
                            ->join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')->where('product_id',$product_id)->select('lp_wh_name','soh','atp',DB::raw('(CASE inventory.inv_display_mode WHEN "soh" THEN (inventory.soh - (inventory.order_qty + inventory.reserved_qty)) WHEN "atp" THEN (inventory.atp - (inventory.order_qty + inventory.reserved_qty)) ELSE ((inventory.soh + inventory.atp) - (inventory.order_qty + inventory.reserved_qty)) END) AS available_inventory'))->first();
        $inventoryData=json_decode(json_encode($inventoryData),true);   
        return $inventoryData;
     }
     public function quickProductUpdate($product_id)
    {
        $checkPPermissions=$this->_roleRepo->checkPermissionByFeatureCode('PRD008');
        if ($checkPPermissions==0)
        {
            return Redirect::to('/');
        }
        DB::enableQueryLog();
        $product_ptr='';
        $product_esp='';
        $product_customer_type='';
        $product_state_id='';
        $product_tax_class='';
        $taxClass='';
        $each_qty='';
        $each_date='';
        $inner_date='';
        $cfc_date='';
        $each_issellable='';
        $inner_qty='';
        $inner_issellable='';
        $product_price_id='';
        $cfc_qty='';
        $cfc_issellable='';
        $invetory_soh='';
        $invetory_atp='';
        $available_inventory='';
        $shelf_uom='';
        $warehouse_name='';
        $order_qty='';
        $price_effective_data='';
        $breadCrumbs = array('Home' => url('/'), 'Products' => url('/products/creation'), 'Quick Product Update' => '#');
        parent::Breadcrumbs($breadCrumbs);
        $productInfoObj = new ProductInfo();
        $Brand_Model_Obj = new BrandModel();
        $ZoneModelObj= new ZoneModel();

        $LegalEntityModelObj= new LegalEntityModel();
        $objPricing = new pricingDashboardModel();
        $categoryObj= new CategoryEloquentModel();
        $productData= ProductInfo::where('product_id',$product_id)->select('product_title','product_id','primary_image','category_id','kvi','esu','mrp','is_sellable','cp_enabled','shelf_life_uom','shelf_life','pack_size','manufacturer_id','brand_id')->get()->all();
        $productData=json_decode(json_encode($productData),true);
        //$categoryLink="";
        $categoryLink= $productInfoObj->categoryLoopLink($productData[0]['category_id']);        
        parent::Title(trans('dashboard.dashboard_title.company_name')." - ".$productData[0]['product_title']);
        $categoryLink= rtrim($categoryLink,' >> ');
        $manf_name= LegalEntityModel::where('legal_entity_id',$productData[0]['manufacturer_id'])->select('business_legal_name')->first();
        $manf_name= json_decode(json_encode($manf_name),1);
        $brand_name= BrandModel::where('brand_id',$productData[0]['brand_id'])->select('brand_name')->first();
        $brand_name= json_decode(json_encode($brand_name),1);
        $shelf_uom_data=  $this->getMasterLookUpData('71','Shelf Life UOM');
        $shelf_uom_data=json_decode(json_encode($shelf_uom_data),1);
        foreach ($shelf_uom_data as $shelfValue)
        {
            if($shelfValue['value']==$productData[0]['shelf_life_uom'])
            {
                $shelf_uom=$shelfValue['name'];
            }
        }
        $getOfferPackAttId=$this->getAllAttributes('offer_pack');
        $getPackSizeAttId=$this->getAllAttributes('pack_size');
        $offer_pack= ProductAttributes::where('product_id',$product_id)->where('attribute_id',$getOfferPackAttId[0])->select('value')->get()->all();
        $pack_size= ProductAttributes::where('product_id',$product_id)->where('attribute_id',$getPackSizeAttId[0])->select('value')->first();
        $pack_size=json_decode(json_encode($pack_size),1);
        $pack_size= (empty($pack_size['value']))?'':$pack_size['value'];
        $offer_pack=json_decode(json_encode($offer_pack),1);
        $offer_pack= (empty($offer_pack[0]['value']))?'':$offer_pack[0]['value'];
        $is_sellable = ($productData[0]['is_sellable']==1)?'checked="true"':'check="false"'; 
        $cp_enabled=  ($productData[0]['cp_enabled']==1)?'checked="true"':'check="false"'; 

        //price configuration pro
        // loadthis data for price modal
        $getCustomerGroup = $objPricing->getCustomerGroup();
        $getStateDetails = $objPricing->getStateDetails();
        $getProductPriceData= pricingDashboardModel::where('product_id',$product_id)->select('product_price_id','price','ptr','state_id','customer_type','effective_date')->orderBy('product_price_id', 'desc')->first();
        $getProductPriceData=json_decode(json_encode($getProductPriceData),1);
       
        //echo $manf_name['business_legal_name']; die();
        if(!empty($getProductPriceData))
        {
            $product_price_id= $getProductPriceData['product_price_id'];
            $product_ptr=$getProductPriceData['ptr'];
            $product_esp= $getProductPriceData['price'];
            $price_effective_data= $getProductPriceData['effective_date'];
            if($price_effective_data!='0000-00-00')
            {
                $price_effective_data=date("d/m/Y", strtotime($price_effective_data));
            }            
            $product_customer_type= $getProductPriceData['customer_type'];
            $product_state_id= $getProductPriceData['state_id'];
            $taxClass=$this->getTaxClassDropDown($product_state_id);
            $taxClass= json_decode(json_encode($taxClass),true);
            
        }
        //tax mapping related 

        $getTaxClassData= ClassTaxMap::where('product_id',$product_id)->first();
        $getTaxClassData=json_decode(json_encode($getTaxClassData),true);
         //print_r($getTaxClassData); die();
        if(!empty($getTaxClassData))
        {
            $product_tax_class= $getTaxClassData['tax_class_id'];
        }
        //pack configuration produc
         $productPackObj = ProductPackConfig::where('product_id', $product_id)->select('level','no_of_eaches','inner_pack_count','pack_id','level','is_sellable','effective_date')->get()->all();
            foreach ($productPackObj as $productPack) 
            {
                if($productPack->level=='16001')
                {
                    $each_qty=$productPack->no_of_eaches;
                    $each_date=$productPack->effective_date;
                    if($each_date!='0000-00-00')
                    {
                       $each_date= date('d/m/Y', strtotime($productPack->effective_date));
                    } else
                    {
                        $each_date='';
                    }
                    $each_issellable=($productPack->is_sellable==1)?'checked="true"':'check="false"'; 
                }
                if($productPack->level=='16003')
                {
                    $inner_qty=$productPack->no_of_eaches;
                    $inner_date=$productPack->effective_date;
                    if($inner_date!='0000-00-00')
                    {
                       $inner_date= date('d/m/Y', strtotime($productPack->effective_date));
                    } else
                    {
                        $inner_date='';
                    }
                    $inner_issellable=($productPack->is_sellable==1)?'checked="true"':'check="false"'; 
                }
                if($productPack->level=='16004')
                {
                    $cfc_qty= $productPack->no_of_eaches;
                    $cfc_date=$productPack->effective_date;
                    if($cfc_date!='0000-00-00')
                    {
                        $cfc_date=date('d/m/Y', strtotime($productPack->effective_date));
                    }else
                    {
                        $cfc_date='';
                    } 
                    $cfc_issellable=($productPack->is_sellable==1)?'checked="true"':'check="false"'; 
                }
            }
             //get legal entity wise ware house for freebie product
        $getZoneData= $ZoneModelObj->select('zone_id','name')->where('country_id','99')->get()->all();
        $getZoneData= json_decode(json_encode($getZoneData),true);
        //inventory tab
        $inventoryData= DB::table('inventory')
                            ->join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')->where('product_id',$product_id)->select('lp_wh_name','soh','atp','order_qty',DB::raw('(CASE inventory.inv_display_mode WHEN "soh" THEN (inventory.soh - (inventory.order_qty + inventory.reserved_qty)) WHEN "atp" THEN (inventory.atp - (inventory.order_qty + inventory.reserved_qty)) ELSE ((inventory.soh + inventory.atp) - (inventory.order_qty + inventory.reserved_qty)) END) AS available_inventory'))->first();

        $inventoryData=json_decode(json_encode($inventoryData),true);
         
        if(!empty($inventoryData))
        {
            $invetory_soh =$inventoryData['soh'];
            $warehouse_name=$inventoryData['lp_wh_name'];
            $invetory_atp=$inventoryData['atp'];
            $available_inventory=$inventoryData['available_inventory'];
            $order_qty=$inventoryData['order_qty'];
        }
        $le_wh_id = Session::get('warehouseId');
        $le_wh_id = explode(',',$le_wh_id);
        $supplierIds = DB::table('product_tot')
                ->join('legal_entities','legal_entity_id','=','supplier_id')
                ->whereIn('le_wh_id',$le_wh_id)
                ->where(['product_id'=>$product_id])->pluck('business_legal_name')->all();

        $suppliers = implode(',',$supplierIds);
        return View::make('Product::quickProductUpdate',['productData'=>$productData,'category_link'=>$categoryLink,'manf_name'=>$manf_name['business_legal_name'],'brand_name'=>$brand_name['brand_name'],'shelf_uom_data'=>$shelf_uom,'offer_pack'=>$offer_pack,'is_sellable'=>$is_sellable,'cp_enabled'=>$cp_enabled,'getStateDetails'=>$getStateDetails, 'getCustomerGroup'=>$getCustomerGroup,'getProductPriceData'=>$getProductPriceData,'product_esp'=>$product_esp,'price_effective_data'=>$price_effective_data,'product_ptr'=>$product_ptr,'product_state_id'=>$product_state_id,'product_customer_type'=>$product_customer_type,'product_tax_class'=>$product_tax_class,'taxClass'=>$taxClass,'each_qty'=>$each_qty,'each_date'=>$each_date,'inner_date'=>$inner_date,'cfc_date'=>$cfc_date,'inner_qty'=>$inner_qty,'cfc_qty'=>$cfc_qty,'cfc_issellable'=>$cfc_issellable,'inner_issellable'=>$inner_issellable,'each_issellable'=>$each_issellable,'invetory_soh'=>$invetory_soh,'invetory_atp'=>$invetory_atp,'warehouse_name'=>$warehouse_name,'available_inventory'=>$available_inventory,'getZoneData'=>$getZoneData,'product_id'=>$product_id,'pack_size'=>$pack_size,'product_price_id'=>$product_price_id,'order_qty'=>$order_qty,'suppliers'=>$suppliers]);

    }

    public function saveProductIsSellable(Request $request)
    {
        $product_id=$request->product_id;
        $is_sellable= $request->is_sellable;
        if($is_sellable==1 || $is_sellable==true){
            $checkconsumerpack=DB::table('product_attributes')->select('value')->where('product_id',$product_id)->where('attribute_id',6)->first();
            $freebie=DB::table('freebee_conf')->select('*')->where('main_prd_id',$product_id)->get()->all();
            if($checkconsumerpack->value=='Consumer Pack Outside' && count($freebie)==0){
                return "Consumer Pack Outside Should be configured with Freebie";
            }elseif($checkconsumerpack->value!='Consumer Pack Outside' && count($freebie)>0){
                return "Improper Offer Pack Configuration";
            }       
        }
        $productData= ProductInfo::where('product_id',$product_id)
                                ->update(['is_sellable'=>$is_sellable]);
        return "Successfully updated.";
    }
    public function saveProductGeneralInfo(Request $request,$product_id)
    {
        $data=$request->all();
        $product_title= $request['product_title'];
        $mrp= $request['mrp'];
        $esu= $request['esu'];
        $pack_size= $request['pack_size'];
        $offer_pack= $request['offer_pack'];
        $shelf_life_uom= $request['shelf_life_uom'];
        $shelf_life= $request['shelf_life'];
        $is_sellable= $request['is_sellable'];
        $cp_enabled= $request['cp_enabled'];
        $getOfferPackAttId=$this->getAllAttributes('offer_pack');
        $getPackSizeAttId=$this->getAllAttributes('pack_size');

        $productData= ProductInfo::where('product_id',$product_id)
                                ->update(['product_title' => $product_title,'esu'=>$esu,'mrp'=>$mrp,'pack_size'=>$pack_size,'shelf_life_uom'=>$shelf_life_uom,'shelf_life'=>$shelf_life,'cp_enabled'=>$cp_enabled,'is_sellable'=>$is_sellable]);
        $checkFreebieProduct=ProductFreeBie::where('main_prd_id',$product_id)->first();
        if($checkFreebieProduct && ($offer_pack !='Freebie') && ($offer_pack !='Consumer Pack Outside'))
        {
            $productData="false";
        }else
        {
            $product_attributes= ProductAttributes::where('product_id',$product_id)->where('attribute_id',$getOfferPackAttId[0])->update(['value'=>$offer_pack]);
            ProductAttributes::where('product_id',$product_id)->where('attribute_id',$getPackSizeAttId[0])->update(['value'=>$pack_size]);
             
        }        
        echo $productData;

    }
     public function saveProductPackInfo(Request $request,$product_id)
    {
        $userid = Session::get('userId');
        $data=$request->all();
        $pack_each_qty= $request['pack_each_qty'];
        $pack_inner_qty= $request['pack_inner_qty'];
        $pack_cfc_qty= $request['pack_cfc_qty'];
        $each_qty_sellable= $request['each_qty_sellable'];
        $inner_qty_sellable= $request['inner_qty_sellable'];
        $cfc_qty_sellable= $request['cfc_qty_sellable'];
        $cfc_date = explode('/', $request['cfc_date']);
        $cfc_date = $cfc_date[2].'-'.$cfc_date[1].'-'.$cfc_date[0];
        $inner_date = explode('/', $request['inner_date']);
        $inner_date = $inner_date[2].'-'.$inner_date[1].'-'.$inner_date[0];
        $each_date = explode('/', $request['each_date']);
        $each_date = $each_date[2].'-'.$each_date[1].'-'.$each_date[0];
        $checkPackData=ProductPackConfig::where('product_id',$product_id)->select('level')->get()->all();
        $checkPackData=json_decode(json_encode($checkPackData),true);
        $level_id='16001';
        foreach ($checkPackData as $PackValue)
        {
            $levelArray[]=$PackValue['level'];
        }

        if(in_array('16001',$levelArray))
        {
            $packInfo = array(
                    'no_of_eaches' => $pack_each_qty,
                    'is_sellable' => $each_qty_sellable,
                    'effective_date' => $each_date,
                    'updated_by'=>$userid
                );   
             $this->quickUpdateProductPackInfo($packInfo,$product_id,16001);

        }else
        {
            $attArray['product_id'] = $product_id;
            $attArray['level'] = '16001';
            $attArray['effective_date'] = $each_date;
            $attArray['no_of_eaches'] = $pack_each_qty;
            $attArray['is_sellable'] = $each_qty_sellable;
            $attArray['created_by']= $userid;
            $this->quickSaveProductPackInfo($attArray);
        }

        if(in_array('16003',$levelArray))
        {
            $packInfo = array(
                    'no_of_eaches' => $pack_inner_qty,
                    'is_sellable' => $inner_qty_sellable,
                    'effective_date' => $inner_date,
                    'updated_by'=>$userid
                );   
             $this->quickUpdateProductPackInfo($packInfo,$product_id,16003);
        }else
        {
            $attArray['product_id'] = $product_id;
            $attArray['level'] = '16003';
            $attArray['effective_date'] = $inner_date;
            $attArray['no_of_eaches'] = $pack_inner_qty;
            $attArray['is_sellable'] = $inner_qty_sellable;
            $attArray['created_by']= $userid;
            $this->quickSaveProductPackInfo($attArray);
        }

        if(in_array('16004',$levelArray))
        {
            $packInfo = array(
                    'no_of_eaches' => $pack_cfc_qty,
                    'is_sellable' => $cfc_qty_sellable,
                    'effective_date' => $cfc_date,
                    'updated_by'=>$userid
                );   
             $this->quickUpdateProductPackInfo($packInfo,$product_id,16004);
        }else
        {
            $attArray['product_id'] = $product_id;
            $attArray['level'] = '16004';
            $attArray['effective_date'] = $cfc_date;
            $attArray['no_of_eaches'] = $pack_cfc_qty;
            $attArray['is_sellable'] = $cfc_qty_sellable;
            $attArray['created_by']= $userid;
            $this->quickSaveProductPackInfo($attArray);
        }       
        return $checkPackData;
    }

    public function saveProductPriceInfo(Request $request,$product_id)
    {
        $userid = Session::get('userId');
        $data=$request->all();
        $rs=1;
        $price_esp= $request['price_esp'];
        $price_product_id=$request['price_product_id'];
        $price_ptr= $request['price_ptr'];
        $price_state= $request['price_state'];
        //echo $request['price_effective_data'];
        $effective_date = explode('/', $request['price_effective_data']);
        $effective_date = $effective_date[2].'-'.$effective_date[1].'-'.$effective_date[0];
       /* echo $effective_date;
        die();*/
        $mdl_custgroup= $request['mdl_custgroup'];
        $tax_class= $request['tax_class'];
        $PriceArray['product_id'] = $product_id;
        $PriceArray['ptr'] = $price_ptr;
        $PriceArray['price'] = $price_esp;
        $PriceArray['state_id'] = $price_state;
        $PriceArray['effective_date']=$effective_date;
        $PriceArray['customer_type'] = $mdl_custgroup;        
        $PriceArray['created_by']= $userid;
        $PriceArray['legal_entity_id']= Session::get('legal_entity_id');
        $arr= array();
        $priceData= pricingDashboardModel::where('product_price_id',$price_product_id)->first();
        $priceData=json_decode(json_encode($priceData),true);
        $priceData['product_price_id'];
        if($price_state== $priceData['state_id'] && $mdl_custgroup== $priceData['customer_type'] && $effective_date==$priceData['effective_date'] || $priceData['effective_date']=='0000-00-00')
        {
            $rs= pricingDashboardModel::where('product_price_id',$price_product_id)->update($PriceArray);
        }
        else
        {         
            $price_product_id= pricingDashboardModel::insertGetId($PriceArray);
        }
        $tax = ClassTaxMap::where('product_id', $product_id)->select('tax_class_id')->first();
        
        if($tax_class!='' && $price_state!='all')
        {
            if($tax)
            {
                ClassTaxMap::where('product_id',$product_id)->update(['tax_class_id'=>$tax_class]);
            }else
            {
                $taxArray['product_id']= $product_id;
                $taxArray['tax_class_id']= $tax_class;
                $taxArray['created_by']=Session::get('legal_entity_id');
                 ClassTaxMap::insert($taxArray);
            }            
        }
        $Customer_type=$this->getCustomerType($mdl_custgroup);
        $state=$this->getStates($price_state);
        $TaxClassName=DB::table('tax_classes')->where('tax_class_id',$tax_class)->pluck('tax_class_code')->all();
        $arr = array("price_product_id"=>$price_product_id,"customer_type" => $Customer_type,'price_state'=>$price_state,'tax_class'=>$TaxClassName[0]);
       return json_encode($arr);
       
    }


    public function quickSaveProductPackInfo($data)
    {
        return ProductPackConfig::insert($data);
    }
    public function quickUpdateProductPackInfo($data,$product_id,$level)
    {
        return ProductPackConfig::where('product_id',$product_id)->where('level',$level)->update($data);
    }
    public function getOfferPackData()
    {
        return  $this->getMasterLookUpData('102','Offer Pack');
    }
    public function getShelfLifeUOMdata()
    {
        return $this->getMasterLookUpData('71','Shelf Life UOM');
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
    public function getTaxClassDropDown($state_id)
    {
        $rss= DB::table('tax_classes')
            ->where('state_id',$state_id)
            ->select('tax_class_id','state_id','tax_class_code')
            ->get()->all();
        return $rss;

    }
        
    public function QuickProductCpStatus(Request $request) {
        try {
                $productId = $request->get('ProductId');
                $flag = $request->get('flag');
                $rs=0;

                $userId = Session::get('userId');
                $pricing = DB::table('product_prices')
                            ->where('product_id',$productId)
                            ->pluck('price')->all();
                $tax = DB::table('tax_class_product_map')->select('tax_class_id')
                                ->where('product_id', $productId)->first();
                $is_sellable= DB::table('products')->where('product_id', $productId)->select('is_sellable')->pluck('is_sellable')->all();
                if($pricing && !empty($tax) && $is_sellable[0]==1)
                {
                     $cp_enabled = DB::table('products')
                    ->where('product_id', $productId)
                    ->update(['cp_enabled' => $flag]);
                    $rs=1;
                }
                if($flag==0 )
                {
                    $cp_enabled = DB::table('products')
                    ->where('product_id', $productId)
                    ->update(['cp_enabled' => $flag]);                    
                    $rs=1;                                       
                }
                return $rs;
               
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
    public function products(Request $request) {
        DB::beginTransaction();
        try {    
           
            $checkEditProductPermissions=$this->_roleRepo->checkPermissionByFeatureCode('PRD0001');
            if ($checkEditProductPermissions==0)
            {
                return Redirect::to('/');
            }
            DB::enableQueryLog();      
            $breadCrumbs = array('Dashboard' => url('/'), 'Products' => '/products/all');
            parent::Breadcrumbs($breadCrumbs);
            $userId = Session::get('userId');
            $ProductAttributesObj = new ProductAttributes();
            $ProductModelObj = new ProductModel();
            $ProductCharacteristicObj = new ProductCharacteristic();
            $ProductContentObj = new ProductContent();
            $checkFreebie=0;
            $enabledFreebieBtn=0;
            $extendedGrid =0;
            $warehouseFeature=0;
            $createProductFeature =0;
            $uploadProductFeature =0;
            $importexportFeature=0;
            $exportAllProductElpFeature=0;
            $extendedGrid = $this->_roleRepo->checkPermissionByFeatureCode('PRD007');
            $warehouseFeature = $this->_roleRepo->checkPermissionByFeatureCode('WHPU0001');
            $importexportFeature = $this->_roleRepo->checkPermissionByFeatureCode('IEPPC001');
            $createProductFeature = $this->_roleRepo->checkPermissionByFeatureCode('PRC0001');
            $uploadProductFeature = $this->_roleRepo->checkPermissionByFeatureCode('PRU0001');
            $allProductFeature = $this->_roleRepo->checkPermissionByFeatureCode('APDW0001');

            $issupplier = $this->_roleRepo->checkPermissionByFeatureCode('SUP001');

            $isbrand = $this->_roleRepo->checkPermissionByFeatureCode('BRAND001');

            $ismanufacturer = $this->_roleRepo->checkPermissionByFeatureCode('USR009');
            $exportAllProductElpFeature = $this->_roleRepo->checkPermissionByFeatureCode('ALPRELPHST01');
            $access_alldcs=false;
            if($issupplier || $isbrand || $ismanufacturer){
            $access_alldcs=true;
            }
            $dcs = $this->objPricing->getAllDCS();



            if ($request->all() != null) 
            {
                $rs=0;
                $productInfo= json_decode($request->product_data);
                $productAttInfo= json_decode($request->att_data);
                foreach($productInfo as $val)
                {
                    $proData[$val->name] = $val->value; 
                }
                if(empty($proData['product_group']))
                {
                    $proData['product_group']='0';
                }
                $product_is_sellable=0;
                $product_id = $proData['product_id'];
                UserActivity::userActivityLog("Products", $productInfo+$productAttInfo, "Products has been updated in product page", "", array("Product_id" => $product_id));
                $checkPolicyData= ProductPolicies::where('product_id', $product_id)->get()->all();
                $checkPolicyData= json_decode(json_encode($checkPolicyData),true);

                    $policy = array();
                    $polocy_type = array('warranty_policy', 'return_policy');
                    for ($p = 0; $p <= 1; $p++) {
                        $policy_name = $polocy_type[$p];
                        $policy[] = array(
                            'product_id' => $product_id,
                            'policy_type_name' => $polocy_type[$p],
                            'policy_details' => $proData[$policy_name]
                        );
                    }

                    if (count($policy) > 0) 
                    {
                        ProductPolicies::where('product_id', $product_id)->delete();
                        ProductPolicies::insert($policy);
                    } 

                    $product_title = $proData['product_title'];
                  
                    $product_mrp = $proData['product_mrp'];
                               
                    $product_des = $proData['product_description'];
                    $product_kvi = $proData['kvi_name'];

                   
                   
                    $legal_entity_id = isset($proData['manufacturer_name'])?$proData['manufacturer_name']:'';
                    $get_brand_id = isset($proData['brand_id'])?$proData['brand_id']:'';
                    $product_esu = isset($proData['product_esu'])?$proData['product_esu']:'';
                   
                    if(isset($proData['product_is_sellable']))
                    {
                        $product_is_sellable=1;
                    }
                //charactertics 
                    $product_perishable = $proData['perishable'];
                    $product_product_form = $proData['product_form'];
                    $product_flammable = $proData['flammable'];
                    $product_hazardous = $proData['hazardous'];
                    $product_odour = $proData['odour'];
                    $product_fragile = $proData['fragile'];
                    $product_shelf_life= isset($proData['shelf_life'])?$proData['shelf_life']:0;
                   
                    $product_license_required = $proData['license_required'];
                    $product_license_type = $proData['license_type'];
                    $product_shelf_life_uom =isset($proData['shelf_life_uom'])?$proData['shelf_life_uom']:0;
                    $Product_Characteristics = array(
                        'product_form' => $product_product_form,
                        'perishable' => $product_perishable,
                        'flammable' => $product_flammable,
                        'hazardous' => $product_hazardous,
                        'odour' => $product_odour,
                        'fragile' => $product_fragile,
                        'licence_req' => $product_license_required,
                        'licence_type' => $product_license_type,
                        'bin_category_type' =>$proData['bin_category_type'],
                    );

                    $checkProChar = $ProductCharacteristicObj->where('product_id', $product_id)->first();
                    if ($checkProChar) 
                    {
                        $Product_Characteristics['updated_by'] = $userId;
                        $this->productCharacteristicsUpdate($Product_Characteristics, $product_id);
                    } 
                    else
                    {
                        $Product_Characteristics['created_by'] = $userId;
                        $Product_Characteristics['product_id'] = $product_id;
                        $this->productCharacteristics($Product_Characteristics);
                    }
                    //$status = '57001';
                    $product_title = ltrim($product_title);
                    $product_des = ltrim($product_des);
                    $product_title = preg_replace("/'/", '`', $product_title);
                    $supplier_login_permissions = $this->_roleRepo->checkPermissionByFeatureCode('PSL001');
                 
                    if(isset($proData['product_is_sellable']))
                    {
                        if($supplier_login_permissions == 1){
                              $ProductModelObj->where('product_id', $product_id)
                        ->update([ 'product_title' => $product_title,  'mrp' => $product_mrp, 'esu' => $product_esu,'updated_by' => $userId, 'kvi' => $product_kvi, 'shelf_life_uom' => $product_shelf_life_uom, 'shelf_life' => $product_shelf_life,'is_sellable'=>$product_is_sellable ,'status' =>57007, 'is_approved' => 0]);
                        }else{
                            $product_sku = $proData['product_sku'];
                            if(isset($proData['product_group']) && $proData['product_group']!=0)
                            {
                                $product_group= $proData['product_group'];
                             }else
                             {
                                $product_group= $proData['product_group_id'];
                             }
                             $product_star = isset($proData['product_star'])?$proData['product_star']:'';
                             $ProductModelObj->where('product_id', $product_id)
                        ->update(['manufacturer_id' => $legal_entity_id,'product_group_id'=>$product_group, 'brand_id' => $get_brand_id, 'product_title' => $product_title, 'seller_sku' => $product_sku, 'mrp' => $product_mrp, 'esu' => $product_esu,'star'=>$product_star, 'updated_by' => $userId, 'kvi' => $product_kvi, 'shelf_life_uom' => $product_shelf_life_uom, 'shelf_life' => $product_shelf_life,'is_sellable'=>$product_is_sellable ]);
                        }
                    }
                    else
                    {
                        if($supplier_login_permissions == 1){
                             $ProductModelObj->where('product_id', $product_id)
                            ->update([ 'product_title' => $product_title,  'mrp' => $product_mrp, 'esu' => $product_esu,'updated_by' => $userId, 'kvi' => $product_kvi, 'shelf_life_uom' => $product_shelf_life_uom, 'shelf_life' => $product_shelf_life,'is_sellable'=>$product_is_sellable ,'status' =>57007, 'is_approved' => 0]); 
                        }else{

                            $product_sku = $proData['product_sku'];
                            if(isset($proData['product_group']) && $proData['product_group']!=0)
                            {
                                $product_group= $proData['product_group'];
                             }else
                             {
                                $product_group= $proData['product_group_id'];
                             }
                             $product_star = isset($proData['product_star'])?$proData['product_star']:'';

                             $ProductModelObj->where('product_id', $product_id)
                            ->update(['manufacturer_id' => $legal_entity_id,'product_group_id'=>$product_group, 'brand_id' => $get_brand_id, 'product_title' => $product_title, 'seller_sku' => $product_sku, 'mrp' => $product_mrp, 'esu' => $product_esu,'star'=>$product_star, 'updated_by' => $userId, 'kvi' => $product_kvi, 'shelf_life_uom' => $product_shelf_life_uom, 'shelf_life' => $product_shelf_life]);
                        }                      
                    }
                    //check this product group have warehouse configuration or not and update also 
                    //$ProductModelObj->updateWarehouseConfigByGrpId($product_group,$proData['product_group_id']);
                    $productResult = $ProductCharacteristicObj->where('product_id', $product_id)->get()->all();

                    $productContentRs = $ProductContentObj->where('product_id', $product_id)->update(['description' => $product_des]);
                    if ($productContentRs != 1) 
                    {
                        $ProductContentObj->product_id = $product_id;
                        // $ProductContentObj->product_name = $request->get('product_name');
                        $ProductContentObj->description = $product_des;
                        $ProductContentObj->save();
                    }
                    $getOfferPackAttId=$this->getAllAttributes('offer_pack');

                    foreach ($productAttInfo as $attributeobj) 
                    {  
                         $checkAttStatus= $this->checkProductAttributes($product_id,$attributeobj->attribute_id); 
                        if($getOfferPackAttId[0] == $attributeobj->attribute_id)
                        {
                            if(($attributeobj->attribute_val !='Freebie') && ($attributeobj->attribute_val !='Consumer Pack Outside'))
                            {
                                if($checkAttStatus['value']=='Freebie' || $checkAttStatus['value']=='Consumer Pack Outside') 
                                {
                                    $checkFreebieProducts= $this->checkFreebieProducts($product_id);
                                    if(!empty($checkFreebieProducts))
                                    {
                                        $checkFreebie=1;
                                        $enabledFreebieBtn=1;
                                        if($product_is_sellable==1)
                                        {
                                            $enabledFreebieBtn=1;
                                        }else
                                        {
                                            $enabledFreebieBtn=0;
                                        }
                                    }
                                }
                            }else if(($attributeobj->attribute_val =='Freebie') || ($attributeobj->attribute_val =='Consumer Pack Outside'))
                            {
                                $enabledFreebieBtn=1;
                                if($product_is_sellable==1)
                                {
                                    $enabledFreebieBtn=1;
                                }else
                                {
                                    $enabledFreebieBtn=0;
                                }
                            }
                        }
                                           
                        if($checkFreebie == 0)
                        {
                            if(empty($checkAttStatus))
                            {
                                $attArray['product_id'] = $product_id;
                                $attArray['attribute_id'] = $attributeobj->attribute_id;
                                $attArray['value'] = $attributeobj->attribute_val;
                                $attArray['created_by'] = $userId;
                                if($attributeobj->attribute_val!="")
                                {
                                    $rs= ProductAttributes::insert($attArray);
                                }
                            }
                            else
                            {
                                log::info($attributeobj->attribute_val);
                                $rs = $ProductAttributesObj
                                ->where('attribute_id', $attributeobj->attribute_id)
                                ->where('product_id', $product_id)
                                ->update(['value' => $attributeobj->attribute_val,'updated_by'=>$userId]);

                            }                       
                        }else
                        {
                            DB::commit();
                            return "false";
                        }
                       
                    }
                    DB::commit();
                    return $enabledFreebieBtn;                   
            }
            DB::commit();
            $brands = $this->returnLegalentityAllBrands();
            // load this data for price modal
            $getCustomerGroup = $this->objPricing->getCustomerGroup();
            $getStateDetails = $this->objPricing->getStateDetails();
        $dc_access = $ProductModelObj->getAcessDetails();
        $dc_access = isset($dc_access['dc_acess_list']) ? $dc_access['dc_acess_list'] : array();
        $wh_list = DB::table('legalentity_warehouses')->where(['status'=>1,'dc_type'=>118001])
                ->whereIn('le_wh_id', explode(",", $dc_access))
                ->orderBy('le_wh_id', 'desc')->pluck('lp_wh_name','le_wh_id')->all();                
        $status =  $this->productModel->getObjNameByUrl(); 
        $whId = Session::get('warehouseId');
        //$counts = $this->getCounts($whId);
        //print_r($counts);exit;
        $user_id=Session::get('userId');
        $userData = DB::select(DB::raw('select * from users u join legal_entities l on u.`legal_entity_id`= l.legal_entity_id where l.`legal_entity_type_id` IN (1006,1002,89002) AND  u.is_active=1 and u.user_id='.$user_id ));
        $showFilterData=true;
        if(!empty($userData)){
            $showFilterData=false;
        }
            $beneficiaryName = $this->objPricing->getBenificiaryName();         
            $wareHouse = $this->objPricing->getWarehouses();          
            $product_stars = $this->objPricing->getProductStars();
            $pack_types_data=  $this->getMasterLookUpData('16','Levels');
            return View::make('Product::products', ['brands_data' => $brands, 'getStateDetails'=>$getStateDetails, 'getCustomerGroup'=>$getCustomerGroup,'extendedGrid'=>$extendedGrid,'warehouseFeature'=>$warehouseFeature,'importexportFeature'=>$importexportFeature,'status'=>$status,'wh_list'=>$wh_list,'wh_id'=>$whId,'createProductFeature'=>$createProductFeature,'uploadProductFeature'=>$uploadProductFeature,'beneficiaryName' => $beneficiaryName ,'wareHouses' => $wareHouse,'product_stars'=>$product_stars, "pack_types_data"=>$pack_types_data,"allProductFeature"=>$allProductFeature,'dcs'=>$dcs,'showFilterData'=>$showFilterData,'access_alldcs'=>$access_alldcs,'exportAllProductElpFeature'=>$exportAllProductElpFeature]);
        } catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return 'false2';
        }
    }
    public function checkFreebieProducts($product_id)
    {
        $ProductFreeBieObj = ProductFreeBie::where('main_prd_id',$product_id)->pluck('main_prd_id')->all();

        //$ProductFreeBieObj= json_decode($ProductFreeBieObj);
        return $ProductFreeBieObj;
    }
    public function getAllAttributes($condition_type)
    {
      $attributeObj= new AttributesModel();
      $attData= $attributeObj->where('attribute_code',$condition_type)->pluck('attribute_id')->all();
      //$attData= json_decode($attData);
      return $attData;
    }
    public function checkProductAttributes($product_id,$attribute_id)
    {
         $ProductAttributesObj = new ProductAttributes();
         $rs= $ProductAttributesObj->where('product_id',$product_id)->where('attribute_id',$attribute_id)->select('prod_att_id','value')->first();
         $rs= json_decode(json_encode($rs),true);
         return $rs;
    }
    public function importPIMExcel() {
        DB::beginTransaction();
        try{
            ini_set('max_execution_time', 1200);
            $productMethod = new ProductController();
            $message = array();
            $msg = '';
            $mail_msg = '';
            $report_table = '';
            $status = 'failed';
            $replace_values = array('NA', 'N/A');
            $productObj = new ProductRepo();

            $Brands = $productMethod->returnLegalentityAllBrands();
            $brandObj = new BrandModel();

            $Brands_Values = array();

            foreach ($Brands as $Temp) {
                $Brands_Values[$Temp->brand_name] = $Temp->brand_id;
            }

            if (Input::hasFile('import_file')) {
                $path = Input::file('import_file')->getRealPath();
                $data = $this->readExcel($path);
                $data = json_decode(json_encode($data), 1);


                $getall_query = DB::table('template_config')->select(array('write_object_name', 'write_col_name', 'label', DB::raw("IF(is_required = 1, 'required','') as Required")))->where(array('template_id' => 1, 'is_active' => 1))->orderBy('sort_order', 'asc')->get()->all();

                $getall_query = json_decode(json_encode($getall_query), true);

                $all_fields = array();

                foreach ($getall_query as $field) {

                    $key = implode('_', explode(' ', strtolower(trim($field['label']))));
                    $all_fields[$key] = array('required' => $field['Required'], 'write_object_name' => $field['write_object_name'], 'write_col_name' => $field['write_col_name']);
                }


                if (isset($data['prod_data']) && count($data['prod_data']) > 0) {
                    $cat_data = $data['cat_data'];
                    $prod_data = $data['prod_data'];
                    $cat_data = array_values($cat_data);
                    $catgory_data = (isset($cat_data[0])) ? $cat_data[0] : '';
                    $category = explode('-', $catgory_data);
                    $category_id = (isset($category[1])) ? $category[1] : '';
                    $legalentity_id = Session()->get('legal_entity_id');
                    $check_data = array('yes' => 1, 'no' => 0, 'y' => 1, 'n' => 0);
                    $fillable_data = array('yes' => 1, 'no' => 0, 'y' => 1, 'n' => 0);
                    $is_margin_arr = array('markup' => 1, 'markdown' => 0);
                    if ($category_id != '') {
                        $pr_scount = 0;
                        $pr_fcount = 0;
                        foreach ($prod_data as $product) {
                            $brand = trim($product['brand']);
                            $required_check_msg = array();
                            foreach ($all_fields as $required_data_key => $required) {
                                if ($required['required'] == 'required') {

                                    if ($product[$required_data_key] == '') {
                                        $required_check_msg[] = $required_data_key;
                                    }
                                }
                            }

                            $product_title = $product['product_title'];
                            if (count($required_check_msg) == 0) {
                                if ($brand != '' && isset($Brands_Values[$brand])) {
                                    if ($product_title != '') {

                                        $product_title_exist = ProductModel::where('legal_entity_id', $legalentity_id)
                                                //->where('legal_entity_id', $legalentity_id)
                                                ->where('product_title', $product_title)
                                                ->first();

                                        if (($product['product_id'] == '' && count($product_title_exist) == 0) || ($product['product_id'] != '')) {
                                            $pack_size = $product['pack_size'];
                                            $manufacturer_sku_code = $product['manufacturer_sku_code'];
                                            $shelf_life = $product['shelf_life'];

                                            $Brand_Id = $Brands_Values[$brand];
                                            $Manufacturer_Query = $brandObj->select('mfg_id')->where('brand_id', $Brand_Id)->first();
                                            $Manufacturer_Id = $Manufacturer_Query->mfg_id;

                                            $pack_size_uom = $product['pack_size_uom'];
                                            $main_image_url = $product['main_image_url'];
                                            $kvi = $product['kvi'];
                                            $Product_Id = $product['product_id'];
                                            $shelf_life_uom = $product['shelf_life_uom'];
                                            $product_form = $product['product_form'];
                                            $pack_type = $product['pack_type'];
                                            $license_type = $product['license_type'];
                                            $preffered_channels = $product['preffered_channels'];
                                            $star_color = (isset($product['star']))?$product['star']:0;
                                            if($star_color)
                                            {
                                            $star_color = MasterLookup::where('master_lookup_name', $star_color)->pluck('value')->all();
                                            $star_color = (isset($star_color[0])) ? $star_color[0] : '';
                                            }


                                            $pack_size_uom = MasterLookup::where('description', $pack_size_uom)->pluck('value')->all();
                                            $pack_size_uom = (isset($pack_size_uom[0])) ? $pack_size_uom[0] : '';
                                            $kvi = MasterLookup::where('master_lookup_name', $kvi)->pluck('value')->all();
                                            $kvi = (isset($kvi[0])) ? $kvi[0] : '';
                                            $shelf_life_uom = MasterLookup::where('master_lookup_name', $shelf_life_uom)->pluck('value')->all();
                                            $shelf_life_uom = (isset($shelf_life_uom[0])) ? $shelf_life_uom[0] : '';
                                            $product_form = MasterLookup::where('master_lookup_name', $product_form)->pluck('value')->all();
                                            $product_form = (isset($product_form[0])) ? $product_form[0] : '';
                                            $license_type = MasterLookup::where('master_lookup_name', $license_type)->pluck('value')->all();
                                            $license_type = (isset($license_type[0])) ? $license_type[0] : '';
                                        $pack_type = MasterLookup::where(['master_lookup_name'=> $pack_type,'mas_cat_id'=>68])->pluck('value')->all();
                                            $pack_type = (isset($pack_type[0])) ? $pack_type[0] : '';
                                            $preffered_channels = MasterLookup::where('master_lookup_name', $preffered_channels)->pluck('value')->all();
                                            $preffered_channels = (isset($preffered_channels[0])) ? $preffered_channels[0] : '';

                                            $perishable = $check_data[preg_replace('/\s+/', '', strtolower($product['perishable']))];
                                            $flammable = $check_data[preg_replace('/\s+/', '', strtolower($product['flammable']))];
                                            $hazardous = $check_data[preg_replace('/\s+/', '', strtolower($product['hazardous']))];
                                            $odour = $check_data[preg_replace('/\s+/', '', strtolower($product['odour']))];
                                            $fragile = $check_data[preg_replace('/\s+/', '', strtolower($product['fragile']))];
                                            $licence_required = $check_data[preg_replace('/\s+/', '', strtolower($product['license_required']))];
                                            //$product_issellable = ($product['is_sellable'] == 'Yes') ? 1 : 0;


//                                  echo 'KVI => '.$kvi.' && popularity => '.$popularity.' && shelf_life_uom => '.$shelf_life_uom.' && product_form => '.$product_form.' && license_type => '.$license_type.' && pack_type => '.$pack_type.' && preffered_channels=>'.$preffered_channels;exit;

                                            if ($kvi != '' && $shelf_life_uom != '' && $product_form != '' && $license_type != '' && $pack_type != '' && $preffered_channels != '') {
                                                $skuid = $productObj->generateSKUcode();
                                                $Product_Array = array(
                                                    'brand_id' => $Brand_Id,
                                                    'product_title' => $product_title,
                                                    'seller_sku' => $manufacturer_sku_code,
                                                    'category_id' => $category_id,
                                                    'kvi' => $kvi,
                                                    'pack_type' => $pack_type,
                                                    'mrp' => $product['mrp'],
                                                    'primary_image' => $main_image_url,
                                                    'shelf_life' => $shelf_life,
                                                    'shelf_life_uom' => $shelf_life_uom,
                                                    'prefered_channels' => $preffered_channels,
                                                    'pack_size' => $pack_size,
                                                    'pack_size_uom' => $pack_size_uom,
                                                    //'is_sellable' => $product_issellable
                                                    'star'=>$star_color
                                                );

                                                $product_exist = array();
                                                $product_exist = ProductModel::where('legal_entity_id', $legalentity_id)
                                                        //->where('legal_entity_id', $legalentity_id)
                                                        ->where('product_id', $Product_Id)
                                                        ->first();



                                                $Product_Pack_Config = array();
                                                for ($count = 1; $count <= 5; $count++) {

                                                    if ($product['l' . $count . '_level'] != '') {

                                                        $level = $product['l' . $count . '_level'];
                                                        $level = MasterLookup::where('master_lookup_name', $level)->where('mas_cat_id',16)->pluck('value')->all();
                                                        $level = (isset($level[0])) ? $level[0] : '';

                                                        $ispalletizable = ($product['l' . $count . '_ispalletizable'] == 'Yes') ? 1 : 0;
                                                        $issellable = ($product['l' . $count . '_issealable'] == 'Yes') ? 1 : 0;

                                                        $weight_uom = $product['l' . $count . '_weight_uom'];
                                                        $weight_uom = MasterLookup::where('description', $weight_uom)->pluck('value')->all();
                                                        $weight_uom = (isset($weight_uom[0])) ? $weight_uom[0] : '';

                                                        $product_code_type = $product['l' . $count . '_product_code_type'];
                                                        $product_code_type = MasterLookup::where('description', $product_code_type)->pluck('value')->all();
                                                        $product_code_type = (isset($product_code_type[0])) ? $product_code_type[0] : '';

                                                        $vol_wt = ($product['l' . $count . '_length'] * $product['l' . $count . '_breadth'] * $product['l' . $count . '_height']) / 5000;
                                                                                                                
                                                        $effective_date='';
                                                        if(isset($product['l' . $count . '_effective_date']['date'])) {
                                                            $effective_date =date('Y-m-d',strtotime($product['l' . $count . '_effective_date']['date']));
                                                        }
                                                        elseif(isset($product['l' . $count . '_effective_date'])) {
                                                            $effective_date =date('Y-m-d',strtotime($product['l' . $count . '_effective_date']));
                                                        }                                                                      
                                                        $pack_level_esu = (isset($product['l' . $count . '_esu']))?$product['l' . $count . '_esu']:0;           
                                                        $Product_Pack_Config[] = array(
                                                            'level' => $level,
                                                            'pack_sku_code' => $product['l' . $count . '_product_code'],
                                                            'pack_code_type' => $product_code_type,
                                                                                          'esu' => $pack_level_esu,
                                                            'no_of_eaches' => (array_key_exists('l' . $count . '_no_of_eaches', $product)) ? $product['l' . $count . '_no_of_eaches'] : 1,
                                                            'inner_pack_count' => (array_key_exists('l' . $count . '_no_of_inners', $product)) ? $product['l' . $count . '_no_of_inners'] : 0,
                                                            'lbh_uom' => '12005',
                                                            'vol_weight' => $vol_wt,
                                                            'vol_weight_uom' => '13002',
                                                            'stack_height' => (array_key_exists('l' . $count . '_stack_height', $product)) ? $product['l' . $count . '_stack_height'] : 0,
                                                            'palletization' => $ispalletizable,
                                                            'pallet_capacity' => '0',
                                                            'length' => $product['l' . $count . '_length'],
                                                            'effective_date' => $effective_date,                                                            
                                                            'breadth' => $product['l' . $count . '_breadth'],
                                                            'height' => $product['l' . $count . '_height'],
                                                            'weight' => $product['l' . $count . '_weight'],
                                                            'weight_uom' => $weight_uom,
                                                            'pack_material' => $product['l' . $count . '_packing_material'],
                                                            'is_sellable' => $issellable
                                                        );
                                                    }
                                                }
                                                //print_R($Product_Pack_Config);exit;
                                                $Product_Characteristics = array(
                                                    'product_form' => $product_form,
                                                    'perishable' => $perishable,
                                                    'flammable' => $flammable,
                                                    'hazardous' => $hazardous,
                                                    'odour' => $odour,
                                                    'fragile' => $fragile,
                                                    'licence_req' => $licence_required,
                                                    'licence_type' => $license_type
                                                );



                                                if (count($product_exist) == 0) {
//                                             $message[] = $product_title . ' Product creation not allowed.';
//                                             $mail_msg.= $product_title . ' Product creation not allowed';
//                                              continue;                                                     
                                                    $res_approval_flow_func = $this->approvalCommonFlow->getApprovalFlowDetails('Product PIM', 'drafted', $this->userId);
                                                    if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                                                        $Product_Array['legal_entity_id'] = $legalentity_id;
                                                        $Product_Array['created_by'] = Session::get('userId');
                                                        $Product_Array['sku'] = $skuid;
                                                        $Product_Array['status'] = '0';
                                                        $Product_Array['is_active'] = '0';
                                                        $Product_Array['cp_enabled'] = '0';
                                                        $Product_Array['is_sellable'] = '0';
                                                        $Product_Array['status'] = '57001';
                                                        $Product_Array['product_type_id'] = 130002;
                                                        $Product_Array['manufacturer_id'] = $Manufacturer_Id;
                                                        $Product_Id = $productMethod->create($Product_Array);
//                                                        $findproduct = ProductModel::find($Product_Id);
//                                                        $findproduct->product_group_id  = $Product_Id;
//                                                        $findproduct->save();
                                                        
                                                        $current_status_id = $res_approval_flow_func["currentStatusId"];
                                                        $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                                                        
                                                        $findproduct = ProductModel::find($Product_Id);
                                                        $findproduct->product_group_id  = $Product_Id;
                                                        $findproduct->status = $next_status_id;
                                                        $findproduct->approved_by = $this->userId;
                                                        $findproduct->approved_at = date('Y-m-d H:i:s');
                                                        $findproduct->updated_by = $this->userId;
                                                        $findproduct->updated_at = date('Y-m-d H:i:s');
                                                        $findproduct->save();

                                                        $workflowhistory = $this->approvalCommonFlow->storeWorkFlowHistory('Product PIM', $Product_Id, $current_status_id, $next_status_id, 'System approval at the time of insertion', $this->userId);

                                                        $insert_inventory_dc=DB::table('inventory')
                                                                        ->insert(['le_wh_id'=>env('APOB_DCID'),'product_id'=>$Product_Id,'updated_by'=> Session::get('userId'),'updated_at'=>date('Y-m-d H:i:s')]);
                                                   if($insert_inventory_dc){
                                                        DB::table('product_cpenabled_dcfcwise')->insert(['product_id'=> $Product_Id,'le_wh_id'=> env('APOB_DCID'),'cp_enabled'=> 0, 'is_sellable'=>0,'esu'=> '','created_by'=>Session::get('userId'),'created_at'=>date("Y-m-d H:i:s")]);
                                                   }
                
                                                        $Product_Content = array(
                                                            'product_id' => $Product_Id,
                                                            'description' => $product['description']
                                                        );
                                                        $productMethod->productContent($Product_Content);

                                                        
                                                        foreach ($Product_Pack_Config as $Key => $Product_Pack) {
                                                            $Product_Pack_Config[$Key]['product_id'] = $Product_Id;
                                                        }



                                                        //print_r($Product_Pack_Config);exit;
                                                        $productMethod->productPackConfig($Product_Id, $Product_Pack_Config);


                                                        $Product_Characteristics['product_id'] = $Product_Id;

                                                        $productMethod->productCharacteristics($Product_Characteristics);


                                                        $message[] = $product_title . ' Created Successfully.';
                                                        $mail_msg.= $product_title . ' Created Successfully' . PHP_EOL;
                                                    } else {
                                                        $message[] = "You don't have permission to create the product";
                                                        $mail_msg.= "You don't have permission to create the product" . PHP_EOL;
                                                    }
                                                } else {
                                                    $Product_Array['updated_by'] = Session::get('userId');
                                                    $update = ProductModel::where('product_id', $Product_Id)
                                                            ->update($Product_Array);
                                                    $Product_Content = array(
                                                        'description' => $product['description']
                                                    );
                                                    ProductContent::where('product_id', $Product_Id)
                                                            ->update($Product_Content);


                                                    foreach ($Product_Pack_Config as $Key => $Product_Pack) {
                                                        $Product_Pack_Config[$Key]['product_id'] = $Product_Id;
                                                    }

                                                    $productMethod->productPackConfig($Product_Id, $Product_Pack_Config);

                                                    $productMethod->productCharacteristicsUpdate($Product_Characteristics, $Product_Id);

                                                    $message[] = $product_title . ' Updated Successfully.';
                                                    $mail_msg.= $product_title . ' Updated Successfully' . PHP_EOL;
                                                }



                                                $ischild = $product['parent'];
                                                if ($ischild != '') {
                                                    $parent_product_id = ProductModel::Where('product_id', $ischild)
                                                            ->first(array('product_id'));
                                                    if (isset($parent_product_id->product_id) && $parent_product_id->product_id != '') {
                                                        $linkId = ProductRelations::where(['product_id' => $Product_Id])->pluck('link_id')->all();
                                                        $super_link['product_id'] = $Product_Id;
                                                        $super_link['parent_id'] = $ischild;
                                                        if (count($linkId) == 0) {
                                                            ProductRelations::insertGetId($super_link);
                                                        } else {
                                                            ProductRelations::where('product_id', $Product_Id)->update(array('parent_id' => $ischild));
                                                        }
                                                        ProductModel::where('product_id', $ischild)
                                                                ->update(array('is_parent' => 1));
                                                        ProductModel::where('product_id', $Product_Id)
                                                                ->update(array('is_parent' => 0));
                                                    }
                                                } else {
                                                    ProductModel::where('product_id', $Product_Id)
                                                            ->update(array('is_parent' => 1));
                                                    ProductRelations::where('product_id', $Product_Id)->delete();
                                                }


                                                $img = array();
                                                for ($im = 2; $im <= 8; $im++) {
                                                    $img_count = 'image_url' . $im;
                                                    if ($product[$img_count] != '') {
                                                        $img[] = array(
                                                            'product_id' => $Product_Id,
                                                            'url' => $product[$img_count],
                                                        );
                                                    }
                                                }
                                                if (count($img) > 0) {
                                                    ProductMedia::where('product_id', $Product_Id)->delete();
                                                    $media = ProductMedia::insert($img);
                                                }

                                                $policy = array();
                                                $polocy_type = array('warranty_policy', 'return_policy');
                                                for ($p = 0; $p <= 1; $p++) {
                                                    $policy_name = $polocy_type[$p];
                                                    $policy[] = array(
                                                        'product_id' => $Product_Id,
                                                        'policy_type_name' => $polocy_type[$p],
                                                        'policy_details' => $product[$policy_name]
                                                    );
                                                }
                                                if (count($policy) > 0) {
                                                    ProductPolicies::where('product_id', $Product_Id)->delete();
                                                    ProductPolicies::insert($policy);
                                                }
                                                $attr_result = DB::table('attribute_sets')
                                                        ->select('attribute_sets.attribute_set_id', 'attribute_sets.attribute_set_name')
                                                        ->where('attribute_sets.category_id', $category_id)
                                                        ->first();
                                                $attribute_ids = array();
                                                if (isset($attr_result->attribute_set_id)) {
                                                    $attr_set_id = $attr_result->attribute_set_id;
                                                    $attributes = DB::table('attribute_set_mapping')
                                                            ->select('attributes.attribute_id', 'attributes.name')
                                                            ->join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id')
                                                            ->where('attribute_set_mapping.attribute_set_id', $attr_set_id)
                                                            ->get()->all();
                                                    $prod_attr = array();



                                                    foreach ($attributes as $attribute) {

                                                        $attr_name = strtolower(str_replace(array(' ', '(', ')', '-', '/'), array('_', '', '', '_', ''), $attribute->name));
                                                        $attr_name = rtrim($attr_name, '_');
                                                        if ($product[$attr_name] != '') {
                                                            $attr_exist = DB::table('product_attributes')
                                                                    ->where('product_id', $Product_Id)
                                                                    ->where('attribute_id', $attribute->attribute_id)
                                                                    ->first();
                                                            if($kvi==69010){
                                                                $product[$attr_name]='Freebie';
                                                            }
                                                            $prod_attr[] = array(
                                                                'product_id' => $Product_Id,
                                                                'attribute_id' => $attribute->attribute_id,
                                                                'attribute_set_id' => $attr_result->attribute_set_id,
                                                                'value' => $product[$attr_name]
                                                            ); //print_r($prod_attr);
                                                        }
                                                    }

                                                    if (count($prod_attr) > 0) {
                                                        ProductAttributes::where('product_id', $Product_Id)->delete();
                                                        $attr = ProductAttributes::insert($prod_attr);
                                                    }
                                                }
                                                $pr_scount++;
                                            } else {
                                                $message[] = 'Master Lookup data not exist in DB for ' . $product_title;
                                                $mail_msg.= 'Master Lookup data not exist in DB for ' . $product_title . PHP_EOL;
                                                $pr_fcount++;
                                            }
                                        } else {
                                            $message[] = $product_title . ' already existed';
                                            $mail_msg.= $product_title . ' already existed' . PHP_EOL;
                                            $pr_fcount++;
                                        }
                                    } else {
                                        $message[] = 'Unable to create product ' . $product_title;
                                        $mail_msg.= 'Unable to create product ' . $product_title . PHP_EOL;
                                        $pr_fcount++;
                                    }
                                } else {
                                    $message[] = 'Invalid Brand Name for ' . $product_title;
                                    $mail_msg.= 'Invalid Brand Name for ' . $product_title . PHP_EOL;
                                    $pr_fcount++;
                                }
                            } else {
                                $message[] = 'All mandatory fields need to be filled for ' . $product_title;
                                $mail_msg.= 'All mandatory fields need to be filled for ' . $product_title . PHP_EOL;
                                $pr_fcount++;
                            }
                        }
                        $msg = $pr_scount . ' Products Created/Updated Successfully and ' . $pr_fcount . ' Products failed to Create/Update';
                        $status = 'success';

                        $file_path = public_path() . '/download/product_import_report.txt';
                        $supplierObj = new SupplierModel();
                        if (file_exists($file_path)) {
                            $file = fopen($file_path, "w");
                            fwrite($file, $mail_msg);
                            fclose($file);
                            $userId = Session::get('userId');
                            $fmName = $supplierObj->getUserNameById($userId);
                            $fmEmail = $supplierObj->getUserEmailById($userId);
                            $name = "Ebutor";
                            $template = 'product_import_report';
                            $subject = 'Product Import Report';
                            $supplierObj->sendEmail($fmName, $fmEmail, $name, $template, $subject, $file_path);
                        }
                    } else {
                        $msg = 'Supplier ID or Category ID should not be empty';
                    }
                } else {
                    $msg = 'No data available';
                }
            } else {
                $msg = 'Please upload file';
            }
        $messg = json_encode(array('status' => $status, 'message' => $msg, 'status_messages' => $message));
        //Log::info($messg);
        DB::commit();
        return $messg;
    }catch(\ErrorException $ex) {
        DB::rollback();
        Log::error($ex->getMessage().' '.$ex->getTraceAsString());
         return json_encode(array('status' => 'failed', 'message' => "Sorry failed to upload products!", 'status_messages' => "Sorry failed to upload products"));
      }
    }

    public function downloadPIMExcel() {

        $category_id = Input::get('category_id');
        $input_brand_id = Input::get('brand_id');

        $cat_data = array();
        $supplier_id = (Session::get('supplier_id') != '') ? Session::get('supplier_id') : Session::get('EditSupplier_id');
        $cat_data[] = 'Category ID -' . $category_id;
        $legalentity_id = Session()->get('legal_entity_id');



        $pim_data = DB::table('template_config')->where(array('template_id' => 1, 'is_active' => 1))->orderBy('sort_order', 'asc')->pluck('Label')->all();

        $required_data = DB::table('template_config')->where(array('template_id' => 1, 'is_active' => 1))->orderBy('sort_order', 'asc')->pluck(DB::raw("IF(is_required = 1, 'required','') as Required"))->all();

        $required_data = json_decode(json_encode($required_data), true);


        $Table_Column_Lookup = DB::table('template_config')->select(array('read_col_name', 'read_object_name', 'Label'))->where(array('template_id' => 1, 'is_active' => 1))->orderBy('sort_order', 'asc')->get()->all();

        $Table_Column_Lookup = json_decode(json_encode($Table_Column_Lookup), 1);


        $lookup_options = array();
        $lookupObj = new MasterLookup();
        $brandObj = new BrandModel();
        $Length_UOM = $lookupObj->getLengthUOM();
        $Capacity_UOM = $lookupObj->getCapacityUOM();
        $PackSize_UOM = $lookupObj->getPackSizeUOM();

        $legalEntityIdArray = array();
        $child_legal_entity_id = DB::table('legal_entities')->select('legal_entity_id')->where(['parent_id' => $legalentity_id, 'legal_entity_type_id' => '1006'])->get()->all();
        foreach ($child_legal_entity_id as $val) {
            $legalEntityIdArray[] = $val->legal_entity_id;
        }

        $legalEntityIdArray = array($legalentity_id);
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
        
        $star_lookup = DB::table('master_lookup')->select('master_lookup.master_lookup_name as name', 'master_lookup.value')
            ->leftJoin('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id')
            ->where('master_lookup_categories.mas_cat_name', 'Product Star')->get()->all();

        $Product_Code_Type_Lookup = array('UPC', 'EAN');

        $check_data = array('yes' => 1, 'no' => 0, 'y' => 1, 'n' => 0);
        $retrieve_data = array(1 => 'yes', 0 => 'no');

        $headings = array('LBH Unit Of Measure', 'Offer Pack', 'Weight Unit Of Measure', 'Brands', 'KVI', 'Pack Type', 'Pack Size UOM', 'Product Code Type', 'Shelf Life', 'Product Form', 'License Type', 'Preffered Channels', 'Segment', 'Level','Star');
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
            'Level' => count($Eaches_Lookup),
            'Star'=>count($star_lookup),
        );
        $sort = arsort($array_count);
        $data['options'][] = $headings;
        for ($i = 1; $i <= max($array_count); $i++) {
            //echo '======'.$i;            
            $data['options'][$i][] = isset($Length_UOM[$i - 1]) ? $Length_UOM[$i - 1]->name : '';
            $data['options'][$i][] = isset($Offer_Pack_Lookup[$i - 1]) ? $Offer_Pack_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Capacity_UOM[$i - 1])) ? $Capacity_UOM[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Brands[$i - 1])) ? $Brands[$i - 1]->brand_name : '';
            $data['options'][$i][] = (isset($KVI_Lookup[$i - 1])) ? $KVI_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Pack_Type_Lookup[$i - 1])) ? $Pack_Type_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($PackSize_UOM[$i - 1])) ? $PackSize_UOM[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Product_Code_Type_Lookup[$i - 1])) ? $Product_Code_Type_Lookup[$i - 1] : '';

            $data['options'][$i][] = (isset($Shelf_Life_Lookup[$i - 1])) ? $Shelf_Life_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Product_Form_Lookup[$i - 1])) ? $Product_Form_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($License_Type_Lookup[$i - 1])) ? $License_Type_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Preffered_Channels_Lookup[$i - 1])) ? $Preffered_Channels_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Popularity_Lookup[$i - 1])) ? $Popularity_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Eaches_Lookup[$i - 1])) ? $Eaches_Lookup[$i - 1]->name : '';
                  $data['options'][$i][] = (isset($star_lookup[$i - 1])) ? $star_lookup[$i - 1]->name : '';
        }


        $attr_result = DB::table('attribute_sets')
                ->select('attribute_sets.attribute_set_id', 'attribute_sets.attribute_set_name')
                ->where('attribute_sets.category_id', $category_id)
                ->first();

        $attr_set_id = $attr_result->attribute_set_id;

        $default_attributes_heading = DB::table('attribute_set_mapping')
                ->join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id')
                ->join('attribute_sets', 'attribute_set_mapping.attribute_set_id', '=', 'attribute_sets.attribute_set_id')
                ->where('attribute_set_mapping.attribute_set_id', $attr_set_id)
                ->where('attributes.attribute_type', '=', 2)
                ->orderBy('attribute_set_mapping.sort_order', 'asc')
                ->pluck('name')->all();


        $other_attributes_heading = DB::table('attribute_set_mapping')
                ->join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id')
                ->join('attribute_sets', 'attribute_set_mapping.attribute_set_id', '=', 'attribute_sets.attribute_set_id')
                ->where('attribute_set_mapping.attribute_set_id', $attr_set_id)
                ->where('attributes.attribute_type', '!=', 2)
                ->orderBy('attribute_set_mapping.sort_order', 'asc')
                ->pluck('name')->all();


        $Varient_Array = DB::select(DB::raw('select getVarientNameBycat(' . $category_id . ',1) as Varient1,getVarientNameBycat(' . $category_id . ',2) as Varient2,getVarientNameBycat(' . $category_id . ',3) as Varient3'));

        $Varient_Array = json_decode(json_encode($Varient_Array), 1);
        $Varient_Array = $Varient_Array[0];



        foreach ($Table_Column_Lookup as $Table_Name => $Table_Lookup) {
            if ($Table_Lookup['read_object_name'] == 'vw_products_pim') {

                if ($Table_Lookup['Label'] == 'Varient1' || $Table_Lookup['Label'] == 'Varient2' || $Table_Lookup['Label'] == 'Varient3') {


                    $Search = $Varient_Array[$Table_Lookup['Label']];
                    $found = 'false';


                    array_walk($Table_Column_Lookup, function ($k, $v) use ($Search, &$found) {

                        if (array_search($Search, $k) == true) {

                            $found = 'true';
                        }
                    });

                    if ($Varient_Array[$Table_Lookup['Label']] != '' && $found == 'false') {

                        $key = array_search($Varient_Array[$Table_Lookup['Label']], $default_attributes_heading);
                        if ($key !== false) {
                            unset($default_attributes_heading[$key]);
                        }

                        $key = array_search($Varient_Array[$Table_Lookup['Label']], $other_attributes_heading);

                        if ($key !== false) {
                            unset($other_attributes_heading[$key]);
                        }

                        $key = array_search($Table_Lookup['Label'], $pim_data);
                        $pim_data[$key] = $Varient_Array[$Table_Lookup['Label']];       //Replacing varient1 with varient names
                    } else {

                        $key = array_search($Table_Lookup['Label'], $pim_data);
                        unset($pim_data[$key]);       //Replacing varient1 with varient names
                        unset($required_data[$key]);
                    }
                }
            } else if ($Table_Lookup['read_object_name'] == 'attributes') {

                $key = array_search($Table_Lookup['Label'], $default_attributes_heading);
                if ($key !== false) {
                    unset($default_attributes_heading[$key]);
                }
            }
        }


        $pim_data = array_merge($pim_data, $default_attributes_heading);
        $pim_data = array_merge($pim_data, $other_attributes_heading);

        $data['cat_data'] = array(
            $cat_data,
            $required_data,
            $pim_data
        );



        if (Input::get('with_data') != '') {

            $products_query = DB::table('vw_products_pim')->select('*')->where(['category_id' => $category_id, 'legal_entity_id' => $legalentity_id]);
            if ($input_brand_id != '' && $input_brand_id != 0) {
                $products_query->where('brand_id', $input_brand_id);
            }




            $Varient_Positions = array();

            $products = $products_query->get()->all();
            $product_data = json_decode(json_encode($products), 1);
            $product_count = 0;
            foreach ($product_data as $product) {
                $product_count++;

                $product_id = $product['product_id'];

                if ($product['pack_size_uom'] != '') {
                    $pack_size_uom = MasterLookup::select('description')
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
                        ->where('attribute_sets.category_id', $category_id)
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
                        ->where(array('attribute_sets.category_id' => $category_id, 'attributes.attribute_type' => 2))
                        ->orderBy('attribute_set_mapping.sort_order', 'asc')
                        ->pluck('product_attributes.value', 'name')->all();


                $Image = ProductMedia::where(array('product_id' => $product_id))->pluck('url')->all();

                $Image = json_decode(json_encode($Image), 1);

                $Image_Array = array();

                $Image_Array['primary_image'] = $product['primary_image'];

                foreach ($Image as $Image_Temp) {
                    $Image_Array['img' . count($Image_Array) . '_url'] = $Image_Temp;
                }



                $Product_Pack_Config = DB::table('product_pack_config')->where(array('product_id' => $product_id))->get()->all();

                $Product_Pack_Config = json_decode(json_encode($Product_Pack_Config), 1);

                $Product_Pack_Array = array();
                foreach ($Product_Pack_Config as $key => $Pack_Config_Temp) {

                    if ($Pack_Config_Temp['pack_code_type'] != '') {
                        $pack_code_type = MasterLookup::select('description')
                                ->where('value', $Pack_Config_Temp['pack_code_type'])
                                ->first();
                    } else {
                        $pack_code_type = '';
                    }

                    if ($Pack_Config_Temp['weight_uom'] != '') {
                        $weight_uom = MasterLookup::select('description')
                                ->where('value', $Pack_Config_Temp['weight_uom'])
                                ->first();
                    } else {
                        $weight_uom = '';
                    }


                    if ($Pack_Config_Temp['level'] != '') {
                        $level = MasterLookup::select('master_lookup_name')
                                ->where('value', $Pack_Config_Temp['level'])
                                ->first();
                    } else {
                        $level = '';
                    }

                    $palletization = ($Pack_Config_Temp['palletization'] == 0) ? 'No' : 'Yes';

                    $is_sellable = ($Pack_Config_Temp['is_sellable'] == 0) ? 'No' : 'Yes';
                            
                    $effective_date = date("m/d/Y", strtotime($Pack_Config_Temp['effective_date']));
                    
                    $pack_level_esu =  (isset($Pack_Config_Temp['esu'])) ? (float)number_format($Pack_Config_Temp['esu'],2,'.','') : '';
                    $Product_Pack_Array['l' . ($key + 1) . '_esu'] = $pack_level_esu;
                    $Product_Pack_Array['l' . ($key + 1) . '_level'] = (isset($level->master_lookup_name)) ? $level->master_lookup_name : '';
                    $Product_Pack_Array['l' . ($key + 1) . '_product_code'] = $Pack_Config_Temp['pack_sku_code'];
                    $Product_Pack_Array['l' . ($key + 1) . '_product_code_type'] = (isset($pack_code_type->description)) ? $pack_code_type->description : '';
                    $Product_Pack_Array['l' . ($key + 1) . '_length'] = $Pack_Config_Temp['length'];
                    $Product_Pack_Array['l' . ($key + 1) . '_breadth'] = $Pack_Config_Temp['breadth'];
                    $Product_Pack_Array['l' . ($key + 1) . '_height'] = $Pack_Config_Temp['height'];
                    $Product_Pack_Array['l' . ($key + 1) . '_weight'] = $Pack_Config_Temp['weight'];
                    $Product_Pack_Array['l' . ($key + 1) . '_weight_uom'] = (isset($weight_uom->description)) ? $weight_uom->description : '';
                    ;
                    $Product_Pack_Array['l' . ($key + 1) . '_issealable'] = $is_sellable;
                    $Product_Pack_Array['l' . ($key + 1) . '_effective_date'] = $effective_date;
                    $Product_Pack_Array['l' . ($key + 1) . '_palletizable'] = $palletization;
                    $Product_Pack_Array['l' . ($key + 1) . '_packing_material'] = $Pack_Config_Temp['pack_material'];
                    $Product_Pack_Array['l' . ($key + 1) . '_no_of_eaches'] = $Pack_Config_Temp['no_of_eaches'];
                    $Product_Pack_Array['l' . ($key + 1) . '_stack_height'] = $Pack_Config_Temp['stack_height'];
                    $Product_Pack_Array['l' . ($key + 1) . '_no_of_inners'] = $Pack_Config_Temp['inner_pack_count'];
                }



                foreach ($Table_Column_Lookup as $Table_Name => $Table_Lookup) {

                    if ($Table_Lookup['read_object_name'] == 'vw_products_pim') {


                        if ($Table_Lookup['Label'] == 'Varient1' || $Table_Lookup['Label'] == 'Varient2' || $Table_Lookup['Label'] == 'Varient3') {


                            $Search = $Varient_Array[$Table_Lookup['Label']];
                            $found = 'false';


                            array_walk($Table_Column_Lookup, function ($k, $v) use ($Search, &$found) {

                                if (array_search($Search, $k) == true) {

                                    $found = 'true';
                                }
                            });

                            if ($Varient_Array[$Table_Lookup['Label']] != '' && $found == 'false') {

                                if (array_key_exists($Varient_Array[$Table_Lookup['Label']], $default_attributes)) {
                                    unset($default_attributes[$Varient_Array[$Table_Lookup['Label']]]);
                                }
                                if (array_key_exists($Varient_Array[$Table_Lookup['Label']], $other_attributes)) {
                                    unset($other_attributes[$Varient_Array[$Table_Lookup['Label']]]);
                                }

                                $prod[$Varient_Array[$Table_Lookup['Label']]] = $product[$Table_Lookup['read_col_name']];
                            }
                        } else {
                            $prod[$Table_Lookup['Label']] = $product[$Table_Lookup['read_col_name']];
                        }
                    } else if ($Table_Lookup['read_object_name'] == 'attributes') {

                        /*                                  if(array_key_exists($Table_Lookup['Label'],$default_attributes))
                          { */

                        if (array_key_exists($Table_Lookup['Label'], $default_attributes)) {
                            $prod[$Table_Lookup['Label']] = $default_attributes[$Table_Lookup['Label']];

                            unset($default_attributes[$Table_Lookup['Label']]);
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


                $prod = array_merge($prod, $default_attributes);
                $prod = array_merge($prod, $other_attributes);

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
        }

        $file_name = 'PIM Template_' . $category_id;
        $result = Excel::create($file_name, function($excel) use($data) {
                    $excel->sheet('Sheet1', function($sheet) use($data) {
                        $sheet->fromArray($data['cat_data'], null, 'A1', false, false);
                        $sheet->protectCells('A1', 'password');
                        $sheet->protectCells('B1', 'password');
                    });
                    // Our second sheet
                    $excel->sheet('Sheet2', function($sheet) use($data) {
                        $sheet->fromArray($data['options'], null, 'A1', false, false);
                    });
                    // Set sheets
                })->export('xls');
        exit;
    }

    public function getProducts(Request $request) {

        $this->grid_field_db_match = array(
            'Product_Title' => 'product_title',
            'Brand' => 'brand_name',
            'KVI' => 'kvi',
            'category_name' => 'category_name',
            'pack_size' => 'pack_size',
            'SKU' => 'sku',
            'ProductCode' => 'upc',
            'ManfName' => 'manf_name',
            'MRP' => 'mrp',
            'Supplier_Count' => 'suppliercnt',
            'Images' => 'image_count',
            'Created_By' => 'created_by',
            'Status' => 'status',
            'cp_enabled' => 'cp_enabled',
            'is_active' => 'is_active',
            'ELP'=>'elp',
            'ESP'=>'esp',
            'PTR'=>'ptr',
            'TAX'=>'taxper',
            'CFC'=>'cfc_qty'
        );


        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        //,'brand_logo as Brand','suppliercnt as Supplier_Count'

        $Product_Model_Obj = new VwManfProductsModel();

        $Legal_Entity = Session::get('legal_entity_id');
        /* $query = $Product_Model_Obj::select(['product_id as Product_ID','image as ProductLogo','category_name as Category','product_title as Product_Name','mrp as MRP','base_price as BasePrice','EBP as EBP','RBP as RBP','CBP as CBP','inventorymode as Inventory_Mode','Schemes','status as Status','MBQ as MPQ']); */


        $query = $Product_Model_Obj::select(['product_id as Product_ID', 'image as ProductLogo', 'product_title as Product_Title', 'brand_name as Brand',
                    'kvi as KVI', 'manf_name as ManfName', 'category_name', DB::raw('case when pack_size IS NOT NULL then concat(pack_size," ",pack_size_uom,"") else pack_size end as pack_size'), 'sku as SKU', 'upc as ProductCode', 'mrp as MRP', 'suppliercnt as Supplier_Count',
                    'image_count as Images', DB::raw('0 as Schemes'), 'is_approved as IsApproved', 'status as Status', 'cp_enabled as cp_enabled', 'created_by as Created_By','elp as ELP','esp as ESP','ptr as PTR','taxper as TAX','cfc_qty as CFC']);
        /* $rolesObj= new Role();
          $brandFilter= $rolesObj->getFilterData(9, Session::get('userId'));
          $brandFilter=json_decode($brandFilter,true);
          $list = isset($DataFilter['brand']) ? $DataFilter['brand'] : []; */
        if ($Legal_Entity != 0) {
            $query->where('legal_entity_id', '=', $Legal_Entity);
            //$query->whereIn('brand_id',$list);
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
                $cp_enabled = 'Yes';
            } else {
                $cp_enabled = 'No';
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
        echo json_encode(array('Records' => $Manage_Products, 'TotalRecordsCount' => $row_count));
    }

    public function getCockpitProducts(Request $request) {

        $page = $request->input('page');
        $pageSize = $request->input('pageSize');

        $skip = $page * $pageSize;

        $query = ProductModel::select('product_id as ProductID', 'primary_image as ProductLogo', 'product_title as Name', 'mrp as mrp', 'is_active as ED_Enabled', 'is_deleted as Temp');

        $count = $query->count();

        $result = $query->skip($page * 5)->take(5)->get()->all();


        foreach ($result as $k => $product) {

            $result[$k]->Action = '<a data-toggle="modal" href="product/edit/' . $result[$k]->ProductID . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete" href="product/delete/' . $result[$k]->ProductID . '"> <i class="fa fa-trash-o"></i> </a>';

            if ($result[$k]->ProductLogo != '') {
                $result[$k]->ProductLogo = "<img src='" . $result[$k]->ProductLogo . "' height='48' width='48' />";
            } else {
                $result[$k]->ProductLogo = "<img src='/uploads/products/notfound.png' height='48' width='48' />";
            }

            $result[$k]->ED_Enabled = '<label class="switch "><input class="switch-input "  type="checkbox" check="false" id="vr_enabled_id"><span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';

            $result[$k]->SalesControl = '<i class="fa fa-map-marker" ></i> <i class="fa fa-money"></i> <i class="fa fa-pencil"></i> <i class="fa fa-trash"></i><a href="/productpreview/' . $result[$k]->ProductID . '"><i class="fa fa-eye"></i></a> ';

            $result[$k]->Approvals = '<i class="fa fa-check-square-o"></i>     <i class="fa fa-credit-card"></i>';
        }

        echo json_encode(array('Records' => $result, 'TotalRecordsCount' => $count));

        /* echo '{"Records":[{ "ProductID": 1, "Name": "Amsterdam", "ProductNumber": "BA-8444", "MakeFlag": false, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 1000, "ReorderPoint": 750, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "694215b7-08f7-4c0d-acb1-d734ba44c0c8", "ModifiedDate": "\/Date(1078992096827)\/" }, 
          { "ProductID": 2, "Name": "Bearing Ball", "ProductNumber": "BA-8327", s"MakeFlag": false, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 1000, "ReorderPoint": 750, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "58ae3c20-4f3a-4749-a7d4-d568806cc537", "ModifiedDate": "\/Date(1078992096827)\/" },
          { "ProductID": 3, "Name": "BB Ball Bearing", "ProductNumber": "BE-2349", "MakeFlag": true, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 800, "ReorderPoint": 600, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 1, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "9c21aed2-5bfa-4f18-bcb8-f11638dc2e4e", "ModifiedDate": "\/Date(1078992096827)\/" },
          { "ProductID": 4, "Name": "Headset Ball Bearings", "ProductNumber": "BE-2908", "MakeFlag": false, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 800, "ReorderPoint": 600, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "ecfed6cb-51ff-49b5-b06c-7d8ac834db8b", "ModifiedDate": "\/Date(1078992096827)\/" },
          { "ProductID": 316, "Name": "Blade", "ProductNumber": "BL-2036", "MakeFlag": true, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 800, "ReorderPoint": 600, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 1, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "e73e9750-603b-4131-89f5-3dd15ed5ff80", "ModifiedDate": "\/Date(1078992096827)\/" },
          { "ProductID": 317, "Name": "LL Crankarm", "ProductNumber": "CA-5965", "MakeFlag": false, "FinishedGoodsFlag": false, "Color": "Black", "SafetyStockLevel": 500, "ReorderPoint": 375, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": "L ", "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "3c9d10b7-a6b2-4774-9963-c19dcee72fea", "ModifiedDate": "\/Date(1078992096827)\/" } ]}
          '; */
    }

    public function getCockpitChilds(Request $request) {
        $path = explode(':', $request->input('path'));

        $Product_ID = $path[1];

        $result = DB::table('products')->select('products.product_id as ID', 'mp_name as ProductName', 'mp_url as UnitPrice', 'pack_size as UnitsInStock')
                ->join('mp_product_add_update', 'mp_product_add_update.product_id', '=', 'products.product_id')
                ->join('mp', 'mp.mp_id', '=', 'mp_product_add_update.mp_id')
                ->get()->all();

        echo json_encode(array('Records' => $result, 'TotalRecordsCount' => 16));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($Product_Array) {
        return $Product_Table = DB::table('products')->insertGetId($Product_Array);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($Product_Array, $Product_Id) {
        return $Product_Table = DB::table('products')->where('product_id', '=', $Product_Id)->update($Product_Array);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function productContent($Product_Content) {
        $Product_Table = DB::table('product_content')->insertGetId($Product_Content);
    }

    public function productPackConfig($Product_ID, $Product_Pack_Config) {

        DB::table('product_pack_config')->where('product_id', $Product_ID)->delete();
        $Product_Table = DB::table('product_pack_config')->insert($Product_Pack_Config);
    }

    public function productContentUpdate($Product_Content, $Product_Id) {
        $Product_Table = DB::table('product_content')->where('product_id', '=', $Product_Id)->update($Product_Content);
    }

    public function productCharacteristics($Product_Characteristics) {
        $Product_Table = DB::table('product_characteristics')->insert($Product_Characteristics);
    }

    public function productCharacteristicsUpdate($Product_Characteristics, $Product_Id) {
        $Product_Table = DB::table('product_characteristics')->where('product_id', '=', $Product_Id)->update($Product_Characteristics);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show() {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
    try {             
        $gds_orders = DB::table('indent_products')->where('product_id', $id)->count(); 
        $inward_products = DB::table('inward_products')->where('product_id', $id)->count(); 
        $po_products = DB::table('po_products')->where('product_id', $id)->count();  
        $gds_products = DB::table('gds_order_products')->where('product_id', $id)->count();
        if ($gds_orders > 0 || $inward_products > 0 || $po_products > 0 || $gds_products > 0) {
            $delet_product = 0;
            echo $delet_product;
        } else {
        DB::table('product_tot')->where('product_id', '=', $id)->delete();
        DB::table('product_content')->where('product_id', '=', $id)->delete();
        DB::table('product_pack_config')->where('product_id', '=', $id)->delete();
        DB::table('mp_product_add_update')->where('product_id', '=', $id)->delete();
        DB::table('product_media')->where('product_id', '=', $id)->delete();
        DB::table('products_slab_rates')->where('product_id', '=', $id)->delete();
        DB::table('product_characteristics')->where('product_id', $id)->delete();
        DB::table('product_content')->where('product_id', $id)->delete();
        DB::table('product_policies')->where('product_id', $id)->delete();
        DB::table('product_media')->where('product_id', $id)->delete();
        DB::table('product_tot')->where('product_id', $id)->delete();
        DB::table('product_characteristics')->where('product_id', $id)->delete();
        DB::table('freebee_conf')->where('free_prd_id', $id)->delete();
        DB::table('product_attributes')->where('product_id', $id)->delete();            
        $delet_product = DB::table('products')->where('product_id', '=', $id)->delete();
        echo $delet_product;
        }         
    } catch (\ErrorException $ex) {
        Log::info($ex->getMessage());
        Log::info($ex->getTraceAsString());
    }
}

    function returnLegalentityAllBrands() {

        $Brand_Model_Obj = new BrandModel();



        $legal_entity_id = Session::get('legal_entity_id');
        /*          $child_legal_entity_id = DB::table('legal_entities')->select('legal_entity_id')->where(['parent_id'=> $legal_entity_id,'legal_entity_type_id'=>'1006'])->get();
          foreach ($child_legal_entity_id as $val)
          {
          $legalEntityIdArray[] = $val->legal_entity_id;
          } */


        if ($legal_entity_id == 0) {
            $query = $Brand_Model_Obj::select(['brands.brand_id', 'brands.brand_name']);
        } else {


            $query = $Brand_Model_Obj::select(['brands.brand_id', 'brands.brand_name'])->where('legal_entity_id', $legal_entity_id);
        }

        return $query->get()->all();
    }

    public function getRelatedProducts(Request $request) {
        $producturl = $request->getPathInfo();
        $pidArray = explode('/', $producturl);
        $product_id = array_pop($pidArray);
        $final = array();
        //$pr = new ProductRelations();

        $parentidsArray = ProductRelations::select('product_id')->where('parent_id', $product_id)->get()->all();
        //echo "<pre>";print_r($parentidsArray); die;
        foreach ($parentidsArray as $obj) {
            $final[] = $obj->product_id;
        }
        //echo "<pre>";print_r($final); die;

        $querys = ProductModel::select('products.product_id as ProductId', 'products.primary_image as PrimaryImage', 'product_tot.product_name as ProductName', 'products.product_title as ProductTitle', 'products.seller_sku as SellerSKU', 'products.upc as UPC', 'products.pack_size as PackSize', 'products.pack_size_uom as PackSizeUOM', 'products.is_active as SupplierCount', 'products.created_by as CreatedBy', 'products.created_at as CreatedOn', 'products.approved_by as ApprovedBy', 'products.approved_at as ApprovedOn')
                        ->leftjoin('product_content', 'product_content.product_id', '=', 'products.product_id')
                        ->leftjoin('product_tot', 'product_tot.product_id', '=', 'products.product_id')
                        ->whereIn('products.product_id', $final)->get()->all();

        $suppliersCnt = DB::table('product_tot')
                ->select('product_id', DB::raw('count(*) as total'))
                ->groupBy('product_id')
                ->get()->all();

        foreach ($suppliersCnt as $cntObj) {
            $Supplierfinal[$cntObj->product_id] = $cntObj->total;
        }

        foreach ($querys as $key => $value) {

            $UoM = '';
            if ($querys[$key]['PackSizeUOM']) {

                $weight_uom = DB::table('master_lookup')->select('description')->where('value', $querys[$key]['PackSizeUOM'])->get()->all();
                $UoM = $weight_uom[0]->description;
            }
            $querys[$key]['PackSize'] = round($querys[$key]['PackSize'], 2) . ' ' . $UoM;


            if (array_key_exists($querys[$key]['ProductId'], $Supplierfinal)) {
                $querys[$key]['SupplierCount'] = $Supplierfinal[$querys[$key]['ProductId']];
            }
            $querys[$key]['PrimaryImage'] = '<img src="' . $querys[$key]['PrimaryImage'] . '" width="48" height="48"/>';
            $querys[$key]['Action'] = '<a class="delete" onclick="deleteRelatedproduct(' . $querys[$key]['productId'] . ')"> <i class="fa fa-trash-o"></i> </a>';
        }
        //echo "<pre>";print_r($querys); die;
        echo json_encode(array('Records' => $querys, 'TotalRecordsCount' => count($querys)));
    }

    public function deleteRelatedProduct(Request $request) {
        $producturl = $request->getPathInfo();
        $pidArray = explode('/', $producturl);
        $product_id = array_pop($pidArray);
        ProductRelations::where('product_id', $product_id)->delete();
    }

    public function deleteProductPack($pack_id, $product_id) {
        $pack_data = ProductPackConfig::where('product_id', $product_id)->where('pack_id', $pack_id)->get()->all();
        $pack_data = json_decode(json_encode($pack_data), true);
        UserActivity::userActivityLog("Products", $pack_data, "Pack configuration has been deleted.", "", array("Product_id" => $product_id));
        $rs = ProductPackConfig::where('product_id', $product_id)->where('pack_id', $pack_id)->delete();
        
        return $rs;
    }

    public function deleteSupplierProduct(Request $request) {
        $producturl = $request->getPathInfo();
        $pidArray = explode('/', $producturl);
        $product_id = array_pop($pidArray);
        ProductTOT::where('product_id', $product_id)->delete();
    }

    public function getProductSuppliers(Request $request) {
        $producturl = $request->getPathInfo();
        $pidArray = explode('/', $producturl);
        $product_id = array_pop($pidArray);
        $final = array();

        $suppliersList  = ProductTOT::where('product_id',$product_id)->pluck('supplier_id')->all();

        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        $pm = new ProductModel();

            $query = DB::table('product_tot as pt')
                    ->join('products as p','p.product_id', '=', 'pt.product_id')
                    ->leftjoin('brands','brands.brand_id', '=', 'p.brand_id')
                    ->select('pt.product_id as ProductID', DB::raw('getLeWhName(pt.le_wh_id) as WarehouseName'),DB::raw('getManfName(pt.supplier_id) as supplier') ,DB::raw('getBrandName(p.brand_id) AS BrandName'), 'pt.product_name as ProductName', 'pt.dlp as ELP',
                            'pt.base_price as Bestprice','p.sku as sku','p.seller_sku as seller_sku', DB::raw('getTaxTypeName(pt.tax_type) as TaxType'), 'pt.tax as Tax', 'pt.prod_price_id', 'pt.rlp as PTR','distributor_margin as EbutorMargin',
                            DB::raw('getMastLookupValue(pt.inventory_mode) AS InventoryMode'), 'pt.atp AS ATP','p.mrp as MRP','pt.effective_date as EffectiveDate',
                            DB::raw('getMastLookupValue(pt.atp_period) AS ATPPeriod'),DB::raw( 'case when pt.is_markup=1 then (p.mrp-pt.rlp)*100/p.mrp else (p.mrp-pt.rlp)*100/pt.rlp end as margin' ),
                            'pt.subscribe as Status',DB::raw('pt.dlp-pt.base_price as Tax_Amt'))->where(['pt.product_id'=>$product_id,'pt.subscribe' => '1'])->whereIn('pt.supplier_id',$suppliersList);

            $currency = DB::table('currency')->where('code','INR')->select('currency_id','symbol_left')->first();

            
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
            $products_list = $query->get()->all();
        $row_count = count($query->get()->all()); 
        $i=0;
        foreach ($products_list as $products_lists) {            
            $actions = '<a data-toggle="modal" class="set_price"  href="' . $products_lists->prod_price_id . '"> <i class="fa fa-inr" style="color:#3598dc !important;"></i></a>&nbsp;&nbsp;';
            $products_list[$i]->actions = $actions;
            $i++;
        }
        echo json_encode(array('Records' => $products_list, 'TotalRecordsCount' => $row_count));
  }

    public function getPackingConfigproduct(Request $request) {
        $producturl = $request->getPathInfo();
        $pidArray = explode('/', $producturl);
        $product_id = array_pop($pidArray);
        $final = array();
        $levelSortOrder = DB::table('master_lookup')->where('mas_cat_id',16)->orderBy('sort_order','asc')->pluck('value')->all();   
        $levelSortOrderIds = implode(',', $levelSortOrder);
        $querys = ProductPackConfig::select('pack_id', 'product_id as ProductId', 'level as Level', 'pack_sku_code as PackSkuCode', 'no_of_eaches as Eaches','esu','star', 'inner_pack_count as Inners', DB::raw('CONCAT(truncate(length,3)," x ",truncate(breadth,3)," x ",truncate(height,3)) as LBH'), 'weight as Weight', 'weight_uom as WeightUOM', 'vol_weight as VolWeight', 'vol_weight_uom as VolWeightUOM', 'lbh_uom as LBHUOM', 'stack_height as StackHeight', 'pack_material as PackMaterial', 'is_sellable as is_sellable','effective_date as effectiveDate','is_cratable')
                        ->where('product_id', $product_id)
                        ->orderByRaw(DB::raw("FIELD(level, $levelSortOrderIds)"))->get()->all();

        foreach ($querys as $key => $value) {
            $LBHUOM = '';
            $volUOM = '';
            $weightUOM = '';
            $star=0;
            if($querys[$key]['effectiveDate'] == '0000-00-00')
                $querys[$key]['effectiveDate'] = '';
            if($querys[$key]['effectiveDate'])
            {
                    $querys[$key]['effectiveDate'] = date('Y-m-d', strtotime($querys[$key]['effectiveDate']));
            }
             
            if ($querys[$key]['LBHUOM']) {
                $LBHUOM = $this->masterValue($querys[$key]['LBHUOM']);
            }
            $querys[$key]['LBH'] = $querys[$key]['LBH'] . ' ' . $LBHUOM;

            if ($querys[$key]['VolWeightUOM']) {
                $volUOM = $this->masterValue($querys[$key]['VolWeightUOM']);
            }
            $querys[$key]['VolWeight'] = round($querys[$key]['VolWeight'], 3) . ' ' . $volUOM;

            if ($querys[$key]['WeightUOM']) {
                $weightUOM = $this->masterValue($querys[$key]['WeightUOM']);
            }
            $querys[$key]['Weight'] = round($querys[$key]['Weight'], 3) . ' ' . $weightUOM;
            if ($querys[$key]['Level']) {
                $querys[$key]['Level'] = $this->masterValue($querys[$key]['Level']);
            }
            if($querys[$key]['star']!=0 ||$querys[$key]['star']!='' )
            {
                $star = $this->getCustomerType($querys[$key]['star']);
            }
            $querys[$key]['star'] = $star;
            $querys[$key]['StackHeight'] = round($querys[$key]['StackHeight'], 3);
            $querys[$key]['is_sellable'] = ($querys[$key]['is_sellable'] == 1) ? 'Yes' : 'No';
            $querys[$key]['is_cratable'] = ($querys[$key]['is_cratable'] == 1) ? 'Yes' : 'No';
            $querys[$key]['Action'] = '&nbsp;<a class="delete" onclick="editPackageConfiguration(' . $querys[$key]['pack_id'] . ')"> <i class="fa fa-pencil" aria-hidden="true"></i> </a> &nbsp;&nbsp;<a class="delete" onclick="delete_product_pack(' . $querys[$key]['pack_id'] . ')"> <i class="fa fa-trash-o"></i> </a>';
        }

        echo json_encode(array('Records' => $querys, 'TotalRecordsCount' => count($querys)));
    }

    public function masterValue($lookUpId) {
        $lookupDesc = '';
        $lookupRow = DB::table('master_lookup')->select('description')->where('value', $lookUpId)->first();
        $lookupDesc = '';
        if ($lookupRow) {
            $lookupDesc = $lookupRow->description;
        }
        return $lookupDesc;
    }

    public function productList() {
        try {
            $breadCrumbs = array('Dashboard' => url('/'), 'Products' => '#', 'Products Group' => '#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title('Product List - Ebutor');
            return View::make('Product::products_list');
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    //get all ware houses by state wise
    public function getAllWareHouse($state_id) {
        $whNameQuery = DB::table('legalentity_warehouses')->where('state', $state_id)->where('dc_type','=','118001')->orwhereNull('dc_type')->whereNotNull('lp_wh_name')->select('lp_wh_name', 'le_wh_id')->get()->all();
        $rrr= DB::getquerylog();
        $whNameQuery = json_decode(json_encode($whNameQuery), true);
        return $whNameQuery;
    }

    public function freeBieProducts(Request $request) {
        $producturl = $request->getPathInfo();
        $pidArray = explode('/', $producturl);
        $product_id = array_pop($pidArray);
        $final = array();
        $querys = DB::table('freebee_conf')->where('main_prd_id', $product_id)->get()->all();
        $querys = json_decode(json_encode($querys), true);
        foreach ($querys as $key => $value) {
            $LBHUOM = '';
            $volUOM = '';
            $weightUOM = '';

            //$querys[$key]['main_prd_id'] = $querys[$key]['main_prd_id'];


            $querys[$key]['mpq'] = $querys[$key]['mpq'];
            $querys[$key]['is_stock_limit'] = ($querys[$key]['is_stock_limit'] == 1) ? 'Yes' : 'No';
            $querys[$key]['article_no'] = $this->getProductArticleNo($querys[$key]['free_prd_id']);
            $querys[$key]['free_prd_des'] = $querys[$key]['freebee_desc'];
            $querys[$key]['free_prd_id'] = $this->getProductName($querys[$key]['free_prd_id']);
            $querys[$key]['qty'] = $querys[$key]['qty'];
            $querys[$key]['state_id'] = $this->getStates($querys[$key]['state_id']);
            $querys[$key]['stock_limit'] = $querys[$key]['stock_limit'];
            $querys[$key]['le_wh_id'] = $this->getWarehouse($querys[$key]['le_wh_id']);
            $querys[$key]['start_date'] = $querys[$key]['start_date'];
            $querys[$key]['end_date'] = $querys[$key]['end_date'];
            $querys[$key]['end_date'] = $querys[$key]['end_date'];

            $querys[$key]['Action'] = '&nbsp;<a class="delete" onclick="editFreebieProduct(' . $querys[$key]['free_conf_id'] . ')"> <i class="fa fa-pencil" aria-hidden="true"></i> </a> &nbsp;&nbsp;<a class="delete" onclick="deleteFreebieProduct(' . $querys[$key]['free_conf_id'] . ')"> <i class="fa fa-trash-o"></i> </a>';
        }

        echo json_encode(array('Records' => $querys, 'TotalRecordsCount' => count($querys)));
    }

    public function getproductList(Request $request) {
        try {
            $this->grid_field_db_match_grouprepo = array(
                'mfg_name' => 'le.business_legal_name',
                'group_repo' => 'pr.product_title',
                'brand_name' => 'br.brand_name',
                'cat_name' => 'ct.cat_name',
                'cp_enabled' => 'pr.cp_enabled'
            );
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize');
            if (empty($page) && empty($pageSize)) {
                $page = 1;
                $pageSize = 1;
            }
            $legal_entity_id = Session::get('legal_entity_id');
            $query = DB::table('products as pr')
                    ->leftjoin('brands as br', 'br.brand_id', '=', 'pr.brand_id')
                    ->leftjoin('legal_entities as le', 'le.legal_entity_id', '=', 'pr.manufacturer_id')
                    ->leftjoin('categories as ct', 'ct.category_id', '=', 'pr.category_id')
                    ->where('pr.is_parent', 1)
                    ->where('pr.legal_entity_id', $legal_entity_id)
                    ->select('pr.product_id', 'pr.product_title as group_repo', 'br.brand_name', 'ct.cat_name', 'le.business_legal_name as mfg_name', 'pr.cp_enabled', 'pr.is_sellable');

            /* ->select('pr.product_id', 'pr.product_title as group_repo', DB::raw('getBrandName(pr.brand_id) AS brand_name'),
              DB::raw('getCategoryName(pr.category_id) AS cat_name'), DB::raw('getManfName(pr.manufacturer_id) AS mfg_name'),
              'pr.cp_enabled'); */

            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';
                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }
                if (isset($this->grid_field_db_match_grouprepo[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_grouprepo[$order_query_field];
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
                            foreach ($this->grid_field_db_match_grouprepo as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_grouprepo[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'endswit') {
                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = '%' . $filter_value_array[1];
                            foreach ($this->grid_field_db_match_grouprepo as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_grouprepo[$key], 'like', $filter_value);
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
                            foreach ($this->grid_field_db_match_grouprepo as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_grouprepo[$key], $like, $filter_value);
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
                            foreach ($this->grid_field_db_match_grouprepo as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_grouprepo[$key], $like, $filter_value);
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
                        if (isset($this->grid_field_db_match_grouprepo[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_grouprepo[$filter_query_field];
                        }
                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
            $count = $query->count();
            $result = array();
            $result['count'] = $count;
            $product_lists = $query->skip($page * $pageSize)->take($pageSize)->get()->all();
            $i = 0;
            foreach ($product_lists as $product_list) {
                if (!empty($product_list->product_id)) {
                    $count = DB::table('product_relations')->where('parent_id', $product_list->product_id)->where('product_id', '!=', 0)->count();
                    $product_lists[$i]->Count = $count + 1;
                    /* if ($product_list->cp_enabled == 1) {
                      $product_lists[$i]->Count = $count + 1;
                      } else {
                      $product_lists[$i]->Count = $count;
                      } */
                }
                $userId = Session::get('userId');
                $supplier = DB::table('product_tot')->where('product_id', $product_list->product_id)->select('supplier_id')->first();
                // Adding new Warehouse Id
                $whId = Session::get('warehouseId'); 
                $pricing = DB::select('call getProductSlabs(?,?,?)', array($product_list->product_id, $whId, $userId));
                $tax = DB::table('tax_class_product_map')->select('tax_class_id')
                                ->where('product_id', $product_list->product_id)->first();
                
                if (empty($pricing)) {
                    $pricing_val = '';
                } else {
                    $pricing_val = $pricing[0]->pack_size;
                }
                if (empty($supplier)) {
                    $supplier_info = '';
                } else {
                    $supplier_info = $supplier->supplier_id;
                }
                if (empty($tax)) {
                    $tax_val = '';
                } else {
                    $tax_val = $tax->tax_class_id;
                }

                $with_price_childs = DB::table('product_relations as pr')->join('vw_products_list as vw', 'vw.product_id', '=', 'pr.product_id')->where('pr.parent_id', $product_list->product_id)->select('vw.product_id', 'vw.product_title')->get()->all();
                if (!empty($with_price_childs)) {
                    // Added new Warehouse Id in getProductslab call
                    foreach ($with_price_childs as $with_price_child) {
                        $pricing = DB::select('call getProductSlabs(?,?,?)', array($with_price_child->product_id, $whId, $userId));
                        $tax = DB::table('tax_class_product_map')->select('tax_class_id')->where('product_id', $with_price_child->product_id)->first();
                        if (!empty($pricing)) {
                            $prdTitles[] = $with_price_child->product_title;
                        } else {
                            $prdTitles[] = '';
                        }
                    }
                } else {
                    $prdTitles[] = '';
                }
                $wit_pricing_prdTitles = $product_list->group_repo . ', ' . ltrim(implode(', ', $prdTitles), ',');
                $cp_enabled = $this->_roleRepo->checkPermissionByFeatureCode('PRG003');
                if ($cp_enabled == 1) {
                    if ($product_list->cp_enabled == 1) {
                        $cp_enabled = '<label class="switch cpenabled"><input  class="switch-input enableDisableProduct disable productrepo"'
                                . ' data_attr_productid="' . $product_list->product_id . '" type="checkbox" checked="true"   name="' . $product_list->group_repo . '"  id="cp_parent' . $product_list->product_id . '"  '
                                . ' value="' . $pricing_val . '" data_attr_supplierId="' . $supplier_info . '" data_attr_tax="' . $tax_val . '" data_without_priceing="' . $wit_pricing_prdTitles . '" data_is_sellable = "' . $product_list->is_sellable . '">'
                                . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                    } else {
                        $cp_enabled = '<label class="switch cpenabled"><input class="switch-input enableDisableProduct disable productrepo" '
                                . 'data_attr_productid="' . $product_list->product_id . '" type="checkbox" check="false" name="' . $product_list->group_repo . '"  id="cp_parent' . $product_list->product_id . '" '
                                . ' value="' . $pricing_val . '" data_attr_supplierId="' . $supplier_info . '" data_attr_tax="' . $tax_val . '" data_without_priceing="' . $wit_pricing_prdTitles . '" data_is_sellable = "' . $product_list->is_sellable . '">'
                                . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                    }
                }
                $edit_prdout_group = $this->_roleRepo->checkPermissionByFeatureCode('PRG002');
                if ($edit_prdout_group == 1) {
                    $action = "<span style='margin-left: 25px !important;'><a href='/productlist/editgrouprepo/$product_list->product_id'><i class='fa fa-pencil'></i></a></span>";
                    $product_lists[$i]->actions = $action;
                }
                $product_lists[$i]->cp_enabled = $cp_enabled;
                $i++;
            }
            if (!empty($product_lists)) {
                return json_encode(array('Records' => $product_lists, 'TotalRecordsCount' => $result['count']));
            } else {
                return json_encode(array('Records' => '[]', 'TotalRecordsCount' => '[]'));
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function childProdutList() {
        try {
            $path = explode(':', Input::get('path'));
            $parent_id = $path[1];            
            $childData = new ProductGroup();            
            $product_viw_data = $childData->childProdutList($parent_id);            
                            
            $userId = Session::get('userId');
            $i = 0;
            foreach ($product_viw_data as $product_data) {
                if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $product_data->primary_image)) {
                    $product_image = "<img width='48' height='48'  src='" . $product_data->primary_image . "'/>";
                    $product_viw_data[$i]->primary_image = $product_image;
                } else {
                    $baseurl = url('uploads/products');
                    $product_image = "<img width='48' height='48'  src='" . $baseurl . '/' . $product_data->primary_image . "'/>";
                    $product_viw_data[$i]->primary_image = $product_image;
                }
                //Rback
                $actions = '';
                $relate_product_view = $this->_roleRepo->checkPermissionByFeatureCode('PRG004');
                $relate_product_edit = $this->_roleRepo->checkPermissionByFeatureCode('PRG005');
                if ($relate_product_view == 1) {
                    $actions = "<a href='/productpreview/$product_data->product_id'><i class='fa fa-eye'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                if ($relate_product_edit == 1) {
                    $actions .= " <a href='/editproduct/$product_data->product_id'><i class='fa fa-pencil'></i></a>";
                }

                $is_parent = '';
                if (isset($product_data->is_parent) && $product_data->is_parent == 1) {
                    $is_parent = '<span style="display:block; margin-left:20px; " class="ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
                }
                
                $pricing = $childData->pricing($product_data->product_id, $userId);                                
                $supplier = $childData->getSupplier($product_data->product_id);                                
                $tax = DB::table('tax_class_product_map')->select('tax_class_id')->where('product_id', $product_data->product_id)->first();
                if (empty($pricing)) {
                    $pricing_val = '';
                } else {
                    $pricing_val = $pricing[0]->pack_size;
                }
                if (empty($supplier)) {
                    $supplier_info = '';
                } else {
                    $supplier_info = $supplier->supplier_id;
                }
                if (empty($tax)) {
                    $tax_val = '';
                } else {
                    $tax_val = $tax->tax_class_id;
                }
                if ($product_data->cp_enabled == 1) {
                    $cp_enabled = '<label class="switch cpenabled"><input  class="switch-input cp_enabled"'
                            . ' data_attr_productid="' . $product_data->product_id . '" type="checkbox" checked="true"  name="' . $product_data->product_title . '"  id="cp_chaild' . $product_data->product_id . '"'
                            . ' value="' . $pricing_val . '" data_attr_supplierId="' . $supplier_info . '" data_attr_tax="' . $tax_val . '"  data_parent="' . $i . '" data_is_sellable = "' . $product_data->is_sellable . '">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $cp_enabled = '<label class="switch cpenabled"><input class="switch-input cp_enabled" '
                            . 'data_attr_productid="' . $product_data->product_id . '" type="checkbox" check="false"  name="' . $product_data->product_title . '"  id="cp_chaild' . $product_data->product_id . '"'
                            . ' value="' . $pricing_val . '" data_attr_supplierId="' . $supplier_info . '" data_attr_tax="' . $tax_val . '" data_parent="' . $i . '" data_is_sellable = "' . $product_data->is_sellable . '">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }
                $currency = DB::table('currency')->where('code', 'INR')->select('currency_id', 'symbol_left')->first();
                $product_viw_data[$i]->currency = $currency->symbol_left;
                $product_viw_data[$i]->actions = $actions;
                $product_viw_data[$i]->cp_enabled = $cp_enabled;
                $product_viw_data[$i]->is_parent = $is_parent;
                $product_viw_data[$i]->pricing = $pricing;
                $i++;
            }
            //print_R($product_viw_data);exit;
            if ($product_viw_data) {
                return json_encode(["productData" => $product_viw_data]);
            } else {
                return json_encode(["productData" => []]);
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function editGroupRepo($parent_id) {
        try {
            $breadCrumbs = array('Dashboard' => url('/'), 'Products' => url('productlist/index'), 'Edit Product Group' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $brands = DB::table('brands')->select('brand_id', 'brand_name')->get()->all();
            $add_product = $this->_roleRepo->checkPermissionByFeatureCode('PRG007');
            return View::make('Product::edit_product_repo')->with(['parent_id' => $parent_id, 'brands' => $brands, 'add_product' => $add_product]);
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function getRepoProducts() {
        try {            
            $productId = Input::get('paent_id');            
            $isParentCheck = DB::table('products')->where('product_id',$productId)->pluck('is_parent')->all();            
            if($isParentCheck[0]) {
                $parent_id = Input::get('paent_id');
            } else { 
                $parentArray = DB::table('product_relations')->where('product_id',$productId)->pluck('parent_id')->all();
                $parent_id = $parentArray[0];
            }                                                                        
            $productRepo = new ProductGroup();
            $product_viw_data = $productRepo->getRepoProducts($parent_id);
            $i = 0;
            $userId = Session::get('userId');
            foreach ($product_viw_data as $product_data) {
                if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $product_data->primary_image)) {
                    $product_image = "<img width='48' height='48'  src='" . $product_data->primary_image . "'/>";
                    $product_viw_data[$i]->primary_image = $product_image;
                } else {
                    $baseurl = url('uploads/products');
                    $product_image = "<img width='48' height='48'  src='" . $baseurl . '/' . $product_data->primary_image . "'/>";
                    $product_viw_data[$i]->primary_image = $product_image;
                }
                $is_parent = $this->_roleRepo->checkPermissionByFeatureCode('PRG006');
                $isParent = '';
                $actions = '';
                if ($is_parent == 1) {
                    if ($product_data->product_id == $parent_id) {
                        $isParent = '<label class="switch "><input onclick="productRepo(' . $product_data->product_id . ')" class="switch-input enableDisableProduct disable repo"'
                                . ' data_attr_productid="' . $product_data->product_id . '" type="radio" checked="true" name="repo"  disabled>'
                                . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                    } else {
                        $isParent = '<label class="switch "><input class="switch-input enableDisableProduct disable repo" '
                                . 'data_attr_productid="' . $product_data->product_id . '" type="radio" check="false" name="repo" onclick="productRepo(' . $product_data->product_id . ')" >'
                                . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                        $actions = '<a class="deletechild" href="javascript:void(0)" data_attr_chaild_id= "' . $product_data->product_id . '" data_attr_productName="' . $product_data->product_title . '"> <i class="fa fa-trash-o"></i> </a>';
                    }
                }
                
                $pricing = $productRepo->pricing($product_data->product_id, $userId);
                $supplier = $productRepo->getSupplier($product_data->product_id);                                
                $tax = DB::table('tax_class_product_map')->select('tax_class_id')->where('product_id', $product_data->product_id)->first();
                if (empty($pricing)) {
                    $pricing_val = '';
                } else {
                    $pricing_val = $pricing[0]->pack_size;
                }
                if (empty($supplier)) {
                    $supplier_info = '';
                } else {
                    $supplier_info = $supplier->supplier_id;
                }

                if (empty($tax)) {
                    $tax_val = '';
                } else {
                    $tax_val = $tax->tax_class_id;
                }


                if ($product_data->cp_enabled == 1) {
                    $cp_enabled = '<label class="switch cpenabled"><input  class="switch-input cp_enabled"'
                            . ' data_attr_productid="' . $product_data->product_id . '" type="checkbox" checked="true"  name="' . $product_data->product_title . '"  id="cp_chaild' . $product_data->product_id . '"'
                            . ' value="' . $pricing_val . '" data_attr_supplierId="' . $supplier_info . '" data_attr_tax="' . $tax_val . '" data_is_sellable = "' . $product_data->is_sellable . '">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $cp_enabled = '<label class="switch cpenabled"><input class="switch-input cp_enabled" '
                            . 'data_attr_productid="' . $product_data->product_id . '" type="checkbox" check="false"  name="' . $product_data->product_title . '"  id="cp_chaild' . $product_data->product_id . '"'
                            . ' value="' . $pricing_val . '" data_attr_supplierId="' . $supplier_info . '" data_attr_tax="' . $tax_val . '" data_is_sellable = "' . $product_data->is_sellable . '">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }

                $product_title = "<a href='/editproduct/$product_data->product_id'>" . $product_data->product_title . "</a>";
                $product_viw_data[$i]->product_title = $product_title;
                $product_viw_data[$i]->cp_enabled = $cp_enabled;
                $currency = DB::table('currency')->where('code', 'INR')->select('currency_id', 'symbol_left')->first();
                $product_viw_data[$i]->currency = $currency->symbol_left;
                $product_viw_data[$i]->is_parent = $isParent;
                $product_viw_data[$i]->actions = $actions;
                $i++;
            }
            if ($product_viw_data) {
                return json_encode(["productData" => $product_viw_data]);
            } else {
                return json_encode(["productData" => []]);
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function updateGroupRepo(Request $request) {
        try {
            $prevParentId = $request['prev_parent_id'];
            $currentParentId = $request['curr_parent_id'];
            $repo = new ProductRepo();
            $result = $repo->updateParentRelations($prevParentId, $currentParentId);
            return $result;
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function cpStatus(Request $request) {
        try {
            $productId = $request->get('ProductId');
            $flag = $request->get('flag');
            $cp_enabled = DB::table('products')
                    ->where('product_id', $productId)
                    ->update(['cp_enabled' => $flag]);

            if($flag==0){
                $date=date('Y-m-d H:i:s');
                $insert_warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                                                            ->where('product_id',$productId)
                                                            ->update(['cp_enabled'=>$flag,'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
            }        
            if ($cp_enabled) {
                return $cp_enabled;
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function cpChildStatus(Request $request) {
        try {
            $parent_product_id = $request->get('ProductId');
            $flag = $request->get('flag');
            $get_product_ids = DB::table('product_relations')->where('parent_id', $parent_product_id)->select('product_id')->pluck('product_id')->all();
            $ecnode_productids = json_decode(json_encode($get_product_ids), true);
            $userId = Session::get('userId');
             // Added new Warehouse Id
            $whId = Session::get('warehouseId');
            foreach ($ecnode_productids as $productIds) {
                $pricing = DB::select('call getProductSlabs(?,?,?)', array($productIds, $whId, $userId));
                if (!empty($pricing[0]->pack_size)) {
                    DB::table('products')->where('product_id', $productIds)->update(['cp_enabled' => $flag]);
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function readExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 3;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function slabPrices(Request $request) {
        try {

            $userId = Session::get('userId');
            $product_id = $request->get('product_id');
            // Adding new Warehouse Id
            //$whId = Session::get('warehouseId'); 
            $rolesObj = new Role();
            $dcids=json_decode($rolesObj->getFilterData('6',$userId),1);
            $dcids = json_decode($dcids['sbu'], 1); 
            $whId = isset($dcids['118001']) ? $dcids['118001'] : 'NULL';
            $whId=explode(',', $whId);
            $results=array();
                $productsArray = DB::table('product_slab_flat')
                         ->select('pack_size','wh_id','unit_price','state_id','product_id','customer_type','margin','product_price_id')
                         ->where('product_id',$product_id)
                         ->wherein('wh_id',$whId)
                         ->get()->all();
                $results=array_merge($results,$productsArray);
            $i = 0;
            foreach ($results as $result) {
                $j=$i;
                $i=$i+1;
                $results[$j]->state = $this->getStates($result->state_id);
                $results[$j]->customer_name = $this->getCustomerType($result->customer_type);
                $results[$j]->dc = $this->getDcName($result->wh_id);
                
                // making the code to edit the price
                $actions= '';
                
                // if($results[$j]->is_slab == 0)
                // {
                //     if(isset($results[$j+1]) && $results[$j]->pack_size == $results[$j+1]->pack_size)
                //     {
                //         continue;
                //     }
                // }
                if($results[$j]->product_price_id != 0){
                    //$results[$j]->product_price_id = 74;
                    $history_reff_id = DB::table('product_prices')->where('product_price_id',$results[$j]->product_price_id)->pluck('history_reff_id');
                    $histId = (isset($history_reff_id[0]))?$history_reff_id[0]:0;
                    $actions = '<center><a href="javascript:void(0)" onclick="updatePriceData('.$histId.')" ><i class="fa fa-pencil"></i></a></center>';        
                    $results[$j]->actions = $actions;

                }else{
                    $results[$j]->actions = '';
                }
            }
            if ($results) {
                return json_encode(["Records" => $results]);
            } else {
                return json_encode(["Records" => []]);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getStates($zone_id) {
        try {
            $states = DB::table('zone')->where('zone_id', $zone_id)->select('name')->get()->all();
            return ($states[0]->name);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getProductName($product_id) {
        try {
            $states = DB::table('products')->where('product_id', $product_id)->select('product_title')->get()->all();
            return ($states[0]->product_title);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getProductArticleNo($product_id) {
        try {
            $states = DB::table('products')->where('product_id', $product_id)->select('sku')->get()->all();
            return ($states[0]->sku);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getWarehouse($warehouse_id) {
        try {
            $states = DB::table('legalentity_warehouses')->where('le_wh_id', $warehouse_id)->select('lp_wh_name')->get()->all();
            return ($states[0]->lp_wh_name);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getCustomerType($customer_value) {
        try {
            $customer_type = DB::table('master_lookup')->where('value', $customer_value)->select('master_lookup_name')->get()->all();
            return ($customer_type[0]->master_lookup_name);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getDcName($le_wh_id) {
        try {
            $dc_name = DB::table('legalentity_warehouses')->where('le_wh_id', $le_wh_id)->select('display_name')->get()->all();
            return isset($dc_name[0]->display_name) ? $dc_name[0]->display_name : '';
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function deleteChildproducts(Request $request) {
        try {
            $parentId = $request->get('parentId');
            $chaildId = $request->get('chaildId');
            $deleteChilds = DB::table('product_relations')->where('parent_id', $parentId)->where('product_id', $chaildId)->delete();
            $statusUpdate = DB::table('products')->where('product_id', $chaildId)->update(['is_parent' => '1']);
            return $deleteChilds;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function deleteProduct(Request $request) {
        try {
            $product_id = $request->get('product_id');
            $gds_orders = DB::table('indent_products')->where('product_id', $product_id)->count(); 
            $inward_products = DB::table('inward_products')->where('product_id', $product_id)->count(); 
            $po_products = DB::table('po_products')->where('product_id', $product_id)->count();  
            $gds_products = DB::table('gds_order_products')->where('product_id', $product_id)->count();
            if ($gds_orders > 0 || $inward_products > 0 || $po_products > 0 || $gds_products > 0) {
                $delet_product = 0;
            } else {
                DB::table('product_pack_config')->where('product_id', $product_id)->delete();
                DB::table('mp_product_add_update')->where('product_id', $product_id)->delete();
                DB::table('product_content')->where('product_id', $product_id)->delete();
                DB::table('product_media')->where('product_id', $product_id)->delete();
                DB::table('product_tot')->where('product_id', $product_id)->delete();
                DB::table('products_slab_rates')->where('product_id', $product_id)->delete();
                DB::table('product_characteristics')->where('product_id', $product_id)->delete();
                DB::table('product_content')->where('product_id', $product_id)->delete();
                DB::table('product_policies')->where('product_id', $product_id)->delete();
                DB::table('product_media')->where('product_id', $product_id)->delete();
                DB::table('product_tot')->where('product_id', $product_id)->delete();
                DB::table('product_characteristics')->where('product_id', $product_id)->delete();
                DB::table('freebee_conf')->where('free_prd_id', $product_id)->delete();
                DB::table('product_attributes')->where('product_id', $product_id)->delete();
                $delet_product = DB::table('products')->where('product_id', $product_id)->delete();            
            }
            return $delet_product;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function duplicateProduct(Request $request) {
        DB::beginTransaction();
        try{
        $productRepo = new ProductRepo;
        $genSku = $productRepo->generateSKUcode();
        $productId = $request->get('pid');
        $title = $request->get('title');

        $res_approval_flow_func = $this->approvalCommonFlow->getApprovalFlowDetails('Product PIM', 'drafted', $this->userId);

        if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
            //product
            $current_status_id = $res_approval_flow_func["currentStatusId"];
            $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
            $products = ProductModel::find($productId);
            $new_product = $products->replicate();
            $new_product->product_title = trim($title);
            $new_product->sku = $genSku;
            $new_product->is_active = 0;
            $new_product->is_approved = 0;
            $new_product->cp_enabled = 0;
            $new_product->is_sellable = 0;
            $new_product->is_parent = 1;
            $new_product->duplicate_from = $productId;
            $new_product->created_by = $this->userId;
            $new_product->product_group_id = $products->product_group_id;
            $new_product->status = $next_status_id;
            $new_product->approved_by = $this->userId;
            $new_product->approved_at = date('Y-m-d H:i:s');
            $new_product->updated_by = $this->userId;
            $new_product->updated_at = date('Y-m-d H:i:s');
            $new_product->save();

            $productNewId = $new_product->product_id;
            
            if ($productNewId) {
                

                //$update_result = $this->productModel->where('product_id', $productNewId)->first();
                //$update_result->save();

                $workflowhistory = $this->approvalCommonFlow->storeWorkFlowHistory('Product PIM', $productNewId, $current_status_id, $next_status_id, 'System approval at the time of insertion', $this->userId);
            }

            //notification on product duplicate
            $productEPModel = new ProductEPModel();
            $productEPModel->sendNotificationAlert($genSku, $productNewId, $title, $products->mrp, $products->primary_image);

            //product content
            $productContent = ProductContent::where('product_id', $productId)->first();
            if ($productContent && $productContent->prod_content_id) {
                $newProductContent = ProductContent::find($productContent->prod_content_id);
                $this->tableReplicate($newProductContent, $productNewId);
            }

            //product pack conf        
            $productPackObj = ProductPackConfig::where('product_id', $productId)->get()->all();
            foreach ($productPackObj as $productPack) {
                if ($productPack && $productPack->pack_id) {
                    $productsPack = ProductPackConfig::find($productPack->pack_id);
                    $this->tableReplicate($productsPack, $productNewId);
                }
            }

            //product policies
            $productPolicy = ProductPolicies::where('product_id', $productId)->first();
            if ($productPolicy && $productPolicy->product_policy_id) {
                $productPolicies = ProductPolicies::find($productPolicy->product_policy_id);
                $this->tableReplicate($productPolicies, $productNewId);
            }

            //product media        
            $productMedia = ProductMedia::where('product_id', $productId)->first();
            if ($productMedia && $productMedia->prod_media_id) {
                $productsMedia = ProductMedia::find($productMedia->prod_media_id);
                $this->tableReplicate($productsMedia, $productNewId);
            }

            //product tot
            /* $productTotObj = ProductTOT::where('product_id', $productId)->get();
              foreach ($productTotObj as $productTot) {
              if ($productTot && $productTot->prod_price_id) {
              $productsTot = ProductTOT::find($productTot->prod_price_id);
              $this->tableReplicate($productsTot, $productNewId);
              }
              } */


            //product characteristics
            $productChar = ProductCharacteristic::where('product_id', $productId)->first();
            if ($productChar && $productChar->charateristic_id) {
                $productsChar = ProductCharacteristic::find($productChar->charateristic_id);
                $this->tableReplicate($productsChar, $productNewId);
            }

            //freebie conf
            $ProductFreeBieObj = ProductFreeBie::where('main_prd_id', $productId)->get()->all();
            foreach ($ProductFreeBieObj as $ProductFreeBie) {
                if ($ProductFreeBie && $ProductFreeBie->free_conf_id) {
                    $ProductsFreeBie = ProductFreeBie::find($ProductFreeBie->free_conf_id);
                    $newTableModel = $ProductsFreeBie->replicate();
                    $newTableModel->main_prd_id = $productNewId;
                    $newTableModel->created_by = Session::get('userId');
                    $newTableModel->save();
                }
            }

            //product attributes
             $getAttData = DB::Table("product_attributes")
                            ->where('product_id',$productId)
                            ->select("attribute_id","value","attribute_set_id",
                            "language_id")
                            ->get()->all();
            $getAttData = json_decode(json_encode($getAttData),true);
            $main_array = array();

            foreach ($getAttData as  $value) {
                $main_array[] = array("attribute_id"=>$value["attribute_id"],"value"=>$value["value"],"attribute_set_id"=>$value["attribute_set_id"],"language_id"=>$value["language_id"],"product_id"=>$productNewId);
            }
            $insertAtt = DB::table("product_attributes")
            ->insert($main_array);
//            Log::info("insert attribute info in product attribute...");
  //          Log::info($main_array);
            $insert_inventory_dc=DB::table('inventory')
                                ->insert(['le_wh_id'=>env('APOB_DCID'),'product_id'=>$productNewId,'updated_by'=> Session::get('userId'),'updated_at'=>date('Y-m-d H:i:s')]);
           if($insert_inventory_dc){
                DB::table('product_cpenabled_dcfcwise')->insert(['product_id'=> $productNewId,'le_wh_id'=> env('APOB_DCID'),'cp_enabled'=> 0, 'is_sellable'=>0,'esu'=> '','created_by'=>Session::get('userId'),'created_at'=>date("Y-m-d H:i:s")]);
           }
           DB::commit();
            return $productNewId;
        } else {
            return "false";
        }
        } catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            return "false1";
        }
    }

    public function tableReplicate($tableModel, $productId) {
        $newTableModel = $tableModel->replicate();
        $newTableModel->product_id = $productId;
        $newTableModel->created_by = Session::get('userId');
        $newTableModel->save();
    }

    public function downloadAllProductInfo() {

        try{
           // ini_set('max_execution_time', 1200);
           // Log::info("Start Downloading all products.. ");
            $cat_data = array();
            $supplier_id = (Session::get('supplier_id') != '') ? Session::get('supplier_id') : Session::get('EditSupplier_id');

            $ProductModelObj = new ProductModel();
            //Log::info("Calling product modle downloadAllProductInfo().. ");
            $data = $ProductModelObj->downloadAllProductInfo();
            //Log::info("Preparing Excel sheet");
            $file_name = 'All Products';
            $result = Excel::create($file_name, function($excel) use($data) {
                        $excel->sheet('Sheet1', function($sheet) use($data) {
                            $sheet->fromArray($data['cat_data'], null, 'A1', false, false);
                            $sheet->protectCells('A1', 'password');
                            $sheet->protectCells('B1', 'password');
                        });
                        // Set sheets
                    })->store('xls', public_path('download'));
            $path_name = public_path('download') . DIRECTORY_SEPARATOR . $file_name . ".xls";
            echo $this->mailExcelReport($path_name,Session::get('userId'));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

     public function getWarehouseBylegalentity() {
        $LegalentitywarehousesModel = new LegalentitywarehousesModel();
        $legal_entity_id = Session::get('legal_entity_id');
        $legalentity_warehouses = $LegalentitywarehousesModel->where('legal_entity_id', $legal_entity_id)->get()->all();
        return $legalentity_warehouses;
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
    
    public function getWhList()
    { 
        $state_id = 4033;
        $list = $this->getAllWareHouse($state_id);
        $opt = '<option value="">Please Select Warehouse...</option>';
        foreach($list as $whArr)
        {
           $opt .=  '<option value="'.$whArr['le_wh_id'].'">'.$whArr['lp_wh_name'].'</option>';            
        }
        return $opt;
    }
    
    public function downloadWhExcel(Request $request)
    {
    $withData = $request->get('with_data');
    $proModel = new ProductModel;     
    $wh_id = $request->get('wh_list_id'); 
    if($withData== 'on'){
        $data = $proModel->getBinConfigValues($wh_id);
    }
    else{
        $data= array();
    }    
    if(empty($data)){
        $data = array('ProductID','BinType','PackType','MinCapacity','MaxCapacity');
    }   
    $pack_types_data2 = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as PackType')
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=','Levels')
             ->get()->all();    
    
    $bin_types_data2 = DB::table('bin_type_dimensions')->select(DB::Raw('getBinDimById(bin_type_dim_id) as BinType'))->get()->all() ;
                         
    $bin_types_data = json_decode(json_encode($bin_types_data2),1);
    $pack_types_data = json_decode(json_encode($pack_types_data2),1);
     
    Excel::create('Warehouse Configuration', function($excel) use($data,$wh_id,$bin_types_data,$pack_types_data) {
        $excel->sheet('Bin Configuration', function($sheet) use($data,$wh_id) {          
        $sheet->fromArray($data);
        $sheet->prependRow(1, array('Warehouse ID', $wh_id));
        /*$sheet->cell('A2:C2', function($cell) {
            $cell->setBackground('#F5EB2F');
        });
        $sheet->setAutoFilter('A2:C2');*/
        });
        
        $excel->sheet('Pack config Data', function($sheet) use($pack_types_data) {          
        $sheet->fromArray($pack_types_data);
        }); 
        $excel->sheet('Bin config Data', function($sheet) use($bin_types_data) {          
        $sheet->fromArray($bin_types_data);
        });         
    })->export('xls');
    }
    
    public function importWhExcel(Request $request)
    {
        DB::beginTransaction();
        try{    
        $file = Input::file('import_file')->getRealPath();
        $data = $this->readExcelData($file);
        $data = json_decode(json_encode($data), 1);
        $finalBin = array();
        $whId = $data['wh_data'][1]; 
        $prodData = $data['prod_data'];
        $pack_types_data=  $this->getMasterLookUpData('16','Levels');
        $bin_types_data=  $this->getMasterLookUpData('109','Bin Types');
        //print_r($pack_types_data); die;
        $message = array();
        $uploadCount = 0;
        $errorCount = 0;
        foreach($pack_types_data as $bin)
        {
            $packConf[$bin->name] = $bin->value;
        }
        foreach($bin_types_data as $bin)
        {
            $finalBin[$bin->name] = $bin->value;
        }
        //print_r($prodData); die;
        foreach($prodData as $prod){
            //$binType = trim($prod['bintype']);
            $binDimValues = explode('(', $prod['bintype']);
            $binName = $binDimValues[0];
            $lbh = explode('(',$binDimValues[1]);
            $lbhvalues = explode(',',$lbh[0]);
            $binNameId = $finalBin[$binName];
            $l = $lbhvalues[0];
            $b = $lbhvalues[1];
            $h = $lbhvalues[2];
            
            $groupId = DB::table('products')->where('product_id',$prod['productid'])->pluck('product_group_id')->all();
            $binTypeDimId = DB::table('bin_type_dimensions')->where(['bin_type'=>$binNameId,'length'=>$l,'breadth'=>$b,'heigth'=>$h])->select('bin_type_dim_id')->first();
           
            $binTypeDimVal = $binTypeDimId->bin_type_dim_id;
            
            $packConfId = $packConf[$prod['packtype']];
            $exists = DB::table('product_bin_config')->where(['wh_id' =>$whId ,'prod_group_id' => $groupId[0],'bin_type_dim_id'=>$binTypeDimVal,'pack_conf_id'=>$packConfId])->pluck('prod_bin_conf_id')->all();

            if(empty($exists))
            {
            DB::table('product_bin_config')->insert(
                array('wh_id' =>$whId ,'prod_group_id'=>$groupId[0],'bin_type_dim_id'=>$binTypeDimVal,'pack_conf_id'=>$packConfId,
                    'min_qty'=>$prod['mincapacity'],'max_qty'=>$prod['maxcapacity'])
            );
            
            array_push($message, "Product ID ".$prod['productid']." configured successfully");
            $uploadCount++;
            }
            else
            {
               $errorCount++; 
               array_push($message, "Product ID ".$prod['productid']." already configured");
            }
        }
        array_push($message, "Warehouse configuration successful for ".$uploadCount." products");
        $messg = json_encode(array('status_messages' => $message));
        DB::commit();
        return $messg;
        }catch(\ErrorException $ex) {
        DB::rollback();
        Log::error($ex->getMessage().' '.$ex->getTraceAsString());
         return json_encode(array('status_messages' => "Failed to upload warehouse configuration!"));
      }
    }    
    public function readExcelData($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['wh_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
    public function readPackExcelData($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['wh_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
     public function readMultiPackExcelData($path,$sheetNo) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex($sheetNo)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $prod_data = Excel::selectSheetsByIndex($sheetNo)->load($path, function($reader) {
                        
                    })->get();
            $data['wh_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
    public function donwloadPackConfigExcel()
    {

        $mainPackArray = array();
        $packMasterLookUpData =array();
        $packMasterLookUpData[] = ["Length_UOM","Weight UOM","Star"];
        $pack_types_data=  $this->getMasterLookUpData('16','Levels');
        $pack_types_data = json_decode(json_encode($pack_types_data),true);
        
        foreach ($pack_types_data as $packValue) 
        {
            $PackAppendingArray=array();

            $PackAppendingArray[] = array("product_id","product_sku","product_title","mrp","kvi","no_of_eaches",
                                    "esu","star","length","breadth","height","lbh_uom","weight",
                                    "weight_uom","is_sellable","effective_date");

            $packValueData=DB::table('products')
                            ->join('product_pack_config as pack_config','pack_config.product_id','=','products.product_id')  
                            ->where('pack_config.level','=',$packValue['value'])
                            ->select("products.product_id","products.sku","product_title","products.mrp",DB::raw("getMastLookupValue(products.kvi) as  kvi"),"pack_config.capacity","pack_config.no_of_eaches",DB::raw("getMastLookupValue(pack_config.lbh_uom) as lbh_uom"),"pack_config.length","pack_config.esu",DB::raw("getMastLookupValue(pack_config.star) as star"),"pack_config.breadth","pack_config.height",DB::raw("getMastLookupValue(pack_config.weight_uom) as weight_uom"),"pack_config.weight","pack_config.is_sellable","pack_config.effective_date")
                            ->orderBy('products.product_id')
                            ->get()->all();
            foreach ($packValueData as $product)
            {
                $pack_effective_date = ($product->effective_date !="0000-00-00")?date("d-m-Y", strtotime($product->effective_date)):"";

                $PackAppendingArray[] =array($product->product_id,
                                        $product->sku,
                                        $product->product_title,
                                        (float)number_format($product->mrp, 2, '.', ''),
                                        $product->kvi,
                                        $product->no_of_eaches,
                                        $product->esu,
                                        $product->star,
                                        (float)number_format($product->length, 2, '.', ''),
                                        (float)number_format($product->breadth, 2, '.', ''),
                                        (float)number_format($product->height, 2, '.', ''),
                                        $product->lbh_uom,
                                        (float)number_format($product->weight, 2, '.', ''),
                                        $product->weight_uom,  
                                        $product->is_sellable,
                                        $pack_effective_date
                                        );
            } 
            $mainPackArray[$packValue['name']] = $PackAppendingArray;
        }
        $arrayKeyValues =array_keys($mainPackArray);

        $products_pack = DB::table('master_lookup')
                            ->select("master_lookup_name as pack_type",'mas_cat_id')
                            ->whereIn('mas_cat_id',array(12,86,140))
                            ->where('is_active','=','1')
                            ->get()->all();
        $products_pack = json_decode(json_encode($products_pack), true);
        foreach($products_pack as $packValues) {
            $tempData[$packValues['mas_cat_id']][] = $packValues['pack_type'];
        }
        array_unshift($tempData[12], 'Length UOM');
        array_unshift($tempData[86], 'Weight UOM');
        array_unshift($tempData[140], 'Star');
        for ($i = 0; $i < count($tempData[86]); $i++){
            if(isset($tempData[12][$i])){
                $finalData[$i] = array($tempData[12][$i], $tempData[86][$i]);
            } else {
                $finalData[$i] = array('', $tempData[86][$i]);
            }
            if(isset($tempData[140][$i])){
                array_push($finalData[$i], $tempData[140][$i]);
            } else {
                array_push($finalData[$i], '');
            }
        }
        Excel::create('products_pack_config_data', function($excel) use ($arrayKeyValues,$mainPackArray,$packMasterLookUpData,$finalData)  
        {

        // Set the spreadsheet title, creator, and description
            $excel->setTitle('Pack Config');
            $excel->setDescription('Product Pack Configuration');

            for($i=0;$i<sizeof($arrayKeyValues);$i++)
            {
                $excel->sheet($arrayKeyValues[$i], function($sheet) use ($mainPackArray,$arrayKeyValues,$i) {
                $sheet->fromArray($mainPackArray[$arrayKeyValues[$i]], null, 'A1', false, false);
                });
            }
        // Build the spreadsheet, passing in the payments array
            $excel->sheet('Pack Info', function($sheet) use($finalData)
            {
                
                $sheet->fromArray($finalData, null, 'A1', false,false);
            });
        })->export('xls');
    }
    public function uploadPackConfigExcel(Request $request){
    DB::beginTransaction();
    try{        
        DB::enableQueryLog();
        $file = Input::file('import_file')->getRealPath();
        $sheetsCnt=Excel::load($file)->getSheetNames();
        $pack_types_data=  $this->getMasterLookUpData('16','Levels');
        $pack_types_data = json_decode(json_encode($pack_types_data), true);
        $pack_types_column = array_column($pack_types_data, 'name');
        $pack_type_values = array_column($pack_types_data, 'value');
        
        foreach ($sheetsCnt as $sheetsValue) 
        {
            if(in_array($sheetsValue,$pack_types_column))
            {
                $arrayPossition = array_search($sheetsValue,$sheetsCnt);
                $data = $this->readMultiPackExcelData($file,$arrayPossition);
                $data = json_decode(json_encode($data), 1);
                $pro_sku = $data['wh_data'][0];
                $prodData = $data['prod_data'];
                foreach ($prodData as $proValue) 
                {     
                    $pack_array =array();
                    $pack_value= $this->getMasterLookupValue($sheetsValue,16);
                    
                    if($proValue[7]!="")
                    {
                        $star= $this->getMasterLookupValue($proValue[7],140);
                        $star =(!empty($star['value']))?$star['value']:0;
                        $pack_array['star']=$star;
                    }                    
                    if($proValue[8]!="")
                    $pack_array['length']=$proValue[8];

                    if($proValue[9]!="")
                    $pack_array['breadth']=$proValue[9];

                    if($proValue[10]!="")
                    $pack_array['height']=$proValue[10];
                    
                    if($proValue[11]!="")
                    {
                        $length_uom= $this->getMasterLookupValue($proValue[11],12);
                        $pack_array['lbh_uom'] =$length_uom['value'];
                    }
                    if($proValue[12]!="")
                    $pack_array['weight'] = (!empty($proValue[12]))?$proValue[12]:0;
                    
                    if($proValue[13]!="")
                    {
                        $weight_uom= $this->getMasterLookupValue($proValue[13],86);
                        $pack_array['weight_uom'] = $weight_uom['value'];
                    }
                    if($proValue[8]!="" && $proValue[9]!="" && $proValue[10]!="")
                    {
                        $height = $proValue[10];
                        $length = $proValue[8];
                        $breadth = $proValue[9];
                        $vol_wt = ($length * $breadth * $height) / 5000;
                        $pack_array['vol_weight'] = $vol_wt;
                    }
                    if(!empty($sheetsValue) && !empty($pack_array))
                    {                
                        $checkPackConfig = DB::table('products')
                                            ->join('product_pack_config as pack_config','pack_config.product_id','=','products.product_id')
                                            ->select('pack_config.*','products.product_id as pid')
                                            ->where('products.sku','=',$proValue[1])
                                            ->where('pack_config.level','=',$pack_value['value'])
                                            ->first();
                        $checkPackConfig = json_decode(json_encode($checkPackConfig),1);
                        if($checkPackConfig['pack_id']!="")
                        {
                            $updatePackConfig = DB::table('product_pack_config')
                                                ->where('pack_id',$checkPackConfig['pack_id'])
                                                ->update($pack_array); 
                            if($proValue[7]!="")             
                            $this->productStarUpdate($checkPackConfig['pid']); 
                        }
                    }
                }
            }
        }
        DB::commit();
        return "Successfully Saved.";
        }catch(\ErrorException $ex) {
        DB::rollback();
        Log::error($ex->getMessage().' '.$ex->getTraceAsString());
         return "Failed to upload!";
      }
    }
     public function getMasterLookupValue($name,$master_value)
    {
        $LookupData = DB::table('master_lookup')
                    ->where('master_lookup_name',$name)
                    ->where('mas_cat_id',$master_value)
                    ->select('value')
                    ->first();
        $LookupData = json_decode(json_encode($LookupData),true);
        return $LookupData;
    }
    public function getMasterLookupName($id)
    {
        $LookupData = DB::table('master_lookup')
                    ->where('value',$id)
                    ->select('master_lookup_name')
                    ->first();
        $LookupData = json_decode(json_encode($LookupData),true);
        return $LookupData;
    }
    
   public function getCreationProducts(Request $request) {
        try {

            $status = $this->productModel->getStatusByUrl();
            $result = $this->productModel->getGridData($request,$status);                                                   
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getCounts()
    {
        DB::enableQueryLog();
        $data=Input::all();
        $whId=$data['product_wh'];
        $user_id=Session::get('userId');
        $userData = DB::select(DB::raw('select * from users u join legal_entities l on u.`legal_entity_id`= l.legal_entity_id where l.`legal_entity_type_id` IN (1006,1002,89002) AND  u.is_active=1 and u.user_id='.$user_id ));
        if(empty($userData)){
           // echo 'in empty';
            $counts=$this->getCountsWithoutSpecificToBrand($whId);
        }
        else{
            $brands=DB::table('user_permssion')
                           ->where(['permission_level_id' => 7, 'user_id' => $user_id])
                         ->pluck('object_id')->all();
            $manufacturer=DB::table('user_permssion')
                           ->where(['permission_level_id' => 11, 'user_id' => $user_id])
                         ->pluck('object_id')->all();            
            $finalArray=array();
            if(!empty($manufacturer)){
                $brandsFromManufacturer=DB::table('brands')
                                    ->whereIn('mfg_id',$manufacturer)
                                    ->pluck('brand_id')->all();
                $finalArray=implode(',',array_unique(array_merge($brands,$brandsFromManufacturer)));
                $finalArray=explode(',',$finalArray);
            }else{
                $finalArray=$brands;
            }
            if(in_array(0, $finalArray)){
                $counts=$this->getCountsWithoutSpecificToBrand($whId);
            }else{

                $counts = DB::table('vw_manf_brand_prod_details')->where('status','!=',null)->where('product_type_id','130002')->where('status','!=',0)->where('le_wh_id',$whId)->whereIn('brand_id',$finalArray)->select(DB::raw('count(product_id) as count'),'status')->groupBy('status')->get()->all();
                $counts['open'] = DB::table('vw_manf_brand_prod_details')->where('product_type_id','130002')->whereIn('status',array(57006,57002,57007,57003))->where('le_wh_id',$whId)->whereIn('brand_id',$finalArray)->pluck(DB::raw('count(product_id) as count'))->all();                    
                $counts['disabled'] = DB::table('vw_manf_brand_prod_details')
                        ->where('product_type_id','130002')
                        ->where('le_wh_id',$whId)
                        ->whereIn('brand_id',$finalArray)
                        ->where('status',1)
                        ->where(function($q){
                    $q->where('is_sellable','=','0')
                    ->orwhere('cp_enabled','=','0');
                    })->pluck(DB::raw('count(product_id) as count'))->all();                    
                $counts['active'] =   DB::table('vw_manf_brand_prod_details')->where('le_wh_id',$whId)->where('product_type_id','130002')->where('status',1)->where('cp_enabled','=',1)->where('is_sellable','=',1)->whereIn('brand_id',$finalArray)->pluck(DB::raw('count(product_id) as count'))->all();                  
                $counts['all'] =  DB::table('vw_manf_brand_prod_details')->where('product_type_id','130002')->where('le_wh_id',$whId)->whereIn('brand_id',$finalArray)->pluck(DB::raw('count(product_id) as count'))->all();
            }
        }                 
        return $counts;
    }
    
    public function whChange(Request $request)
    {        
        if (isset($_SERVER["HTTP_REFERER"]))
        {
            $referer = $_SERVER["HTTP_REFERER"];
            $urlArray = explode('/', $referer);
            $tab = (isset($urlArray[4])) ? $urlArray[4] : 0;
        }
        if($request->product_wh){
        $productWh = $request->product_wh;}
        else{
        $productWh = 0;    
        }
        Session::set('warehouseId',$productWh);
        $response = 1;//array('status' => $status, 'grid_id' => $gridToRefresh, 'message' => $message,'count'=>$count->COUNT);        
        return $response;
    }    

     public function productStarUpdate($product_id)
    {
        $packData = DB::table('product_pack_config')
                    ->where('product_id',$product_id)
                     ->where('is_sellable','=','1')
                    ->pluck('star')->all();
        $packData = json_decode(json_encode($packData),true);
        if(!empty($packData))
        {
            $color_code='140004';
            if(in_array("140001",$packData))
            {
              $color_code="140001";
            }else if(in_array("140002",$packData))
            {
              $color_code="140002";
            }else if(in_array("140003",$packData))
            {
              $color_code="140003";
            }
            DB::table('products')
                ->where('product_id',$product_id)
                ->update(['star'=> $color_code]); 
        }     
    }
     public function mailExcelReport($filePath, $userId){
        try{
             $userId = ($userId=="")?3:$userId;
             //log::info("user id =".$userId);
            $srmQuery = json_decode(json_encode(DB::table("users")->where('user_id','=',$userId)->pluck('email_id')->all()), true);
            $email = $srmQuery[0];
            $time = Carbon::now();
            $emailBody = "Hello " . ucwords(str_replace(".", " ", explode("@", $email)[0])) . ", <br/><br/>";
            $emailBody .= "Please find attached ALL Products Report.<br/><br/>";
            $emailBody .= "*Note: This is an auto generated email !!";
            if (Mail::send('emails.pimMail', ['emailBody' => $emailBody], function ($message) use ($email, $filePath, $time) {
                        $message->to($email);
                        $message->subject('PIM Report '.date('d-m-Y',strtotime($time->toDateTimeString())));
                        $message->attach($filePath);
                    })) {
                File::delete($filePath);
                // echo "Mail sent to - ".$email." !! Temp file deleted !!\n";
               // Log::info("Mail sent to - ".$email." !! Temp file deleted !!");
                return  "Mail sent to - ".$email.". Please check mail.";
            } else {
                 return  "Error in sending mail to ".$email." !!";
                //Log::info("error in sending mail to ".$email." !!");
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Log::error("Error in sending mail to ".$email." !!");
        }
    }
    public function QueueDownloadAllProductInfo(){
        try{
            $userid = Session::get('userId');
            $leid=Session::get('legal_entity_id');
            //Log::info("Download all product info with queue.....user id...".$userid);
            $this->queue = new Queue();
            $args = array("ConsoleClass" => 'AllPIMDownloadReport', 'arguments' => array("user_id"=>$userid,"legal_entity_id"=>$leid));
            $this->queue->enqueue('default', 'ResqueJobRiver', $args);
            return "Please check your mail.";

        }catch (\ErrorException $ex) {
           // Log::info("QueueDownloadAllProductInfo method error");
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function  getCountsWithoutSpecificToBrand($whId){
        
        if($whId==0){
        $rolesObj = new Role();
        $userid = Session::get('userId');
        $Json = json_decode($rolesObj->getFilterData(6,$userid), 1);
        $filters = json_decode($Json['sbu'], 1);            
        $whId = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $whId=explode(',',$whId);
        //print_r($whId);exit;
        }else{
            $whId=explode(',',$whId);

           /* $counts = DB::table('vw_product_grid_wh')->where('status','!=',null)->where('product_type_id','130002')->where('status','!=',0)->whereIn('le_wh_id',$whId)->select(DB::raw('count(product_id) as count'),'status')->groupBy('status')->get();
        $counts['open'] = DB::table('vw_product_grid_wh')->where('product_type_id','130002')->whereIn('status',array(57006,57002,57007,57003))->whereIn('le_wh_id',$whId)->pluck(DB::raw('count(product_id) as count'));                    
        $counts['disabled'] = DB::table('vw_product_grid_wh')
                ->where('product_type_id','130002')
                ->whereIn('le_wh_id',$whId)
                ->where('status',1)
                ->where(function($q){
            $q->where('is_sellable','=','0')
            ->orwhere('cp_enabled','=','0');
            })->pluck(DB::raw('count(product_id) as count'));                    
        $counts['active'] =   DB::table('vw_product_grid_wh')->whereIn('le_wh_id',$whId)->where('product_type_id','130002')->where('status',1)->where('cp_enabled','=',1)->where('is_sellable','=',1)->pluck(DB::raw('count(product_id) as count'));                  
        $counts['all'] =  DB::table('vw_product_grid_wh')->where('product_type_id','130002')->whereIn('le_wh_id',$whId)->pluck(DB::raw('count(product_id) as count'));*/
        $counts=DB::table('vw_products_status_counts')->whereIn('le_wh_id',$whId)->get()->all();
        $counts=json_decode(json_encode($counts),1);
        $counts=array('Drafted'=>$counts[0]['Drafted'],'Creation'=>$counts[0]['Creation'],'Filling'=>$counts[0]['Filling'],'Enablement'=>$counts[0]['Enablement'],'Approval'=>$counts[0]['Approval'],'open'=>$counts[0]['open'],'active'=>$counts[0]['active'],'all'=>$counts[0]['allskus'],'disabled'=>$counts[0]['disabled']);
        }

        
        return $counts;
    }




    //tetsing function 



    public function productConfig(Request $request) {
        try {    
           
            $checkEditProductPermissions=$this->_roleRepo->checkPermissionByFeatureCode('PRD0001');
            if ($checkEditProductPermissions==0)
            {
                return Redirect::to('/');
            }      
            $breadCrumbs = array('Dashboard' => url('/'), 'Products' => '/products/all');
            parent::Breadcrumbs($breadCrumbs);
            $userId = Session::get('userId');
            //$ProductAttributesObj = new ProductAttributes();
            //$ProductModelObj = new ProductModel();
            //$ProductCharacteristicObj = new ProductCharacteristic();
            //$ProductContentObj = new ProductContent();
            //$checkFreebie=0;
            //$enabledFreebieBtn=0;
            //$extendedGrid =0;
            $warehouseFeature=0;
            //$createProductFeature =0;
            $uploadProductFeature =0;
            $importexportFeature=0;
            $exportAllProductElpFeature=0;
            //$extendedGrid = $this->_roleRepo->checkPermissionByFeatureCode('PRD007');
            $warehouseFeature = $this->_roleRepo->checkPermissionByFeatureCode('WHPU0001');
            $importexportFeature = $this->_roleRepo->checkPermissionByFeatureCode('IEPPC001');
            //$createProductFeature = $this->_roleRepo->checkPermissionByFeatureCode('PRC0001');
            $uploadProductFeature = $this->_roleRepo->checkPermissionByFeatureCode('PRU0001');
            $allProductFeature = $this->_roleRepo->checkPermissionByFeatureCode('APDW0001');
            $cp_enable_sheet = $this->_roleRepo->checkPermissionByFeatureCode('CPUST001');
            $exportAllProductElpFeature = $this->_roleRepo->checkPermissionByFeatureCode('ALPRELPHST01');
            $import_prd_cust_esu = $this->_roleRepo->checkPermissionByFeatureCode('IMPCESU001');
            //$dcs = $this->objPricing->getAllDCS();

            //$brands = $this->returnLegalentityAllBrands();
            // load this data for price modal
            //$getCustomerGroup = $this->objPricing->getCustomerGroup();
            //$getStateDetails = $this->objPricing->getStateDetails();
       /* $dc_access = $ProductModelObj->getAcessDetails();
        $dc_access = isset($dc_access['dc_acess_list']) ? $dc_access['dc_acess_list'] : array();
        $wh_list = DB::table('legalentity_warehouses')->where(['status'=>1,'dc_type'=>118001])
                ->whereIn('le_wh_id', explode(",", $dc_access))
                ->orderBy('le_wh_id', 'desc')->pluck('lp_wh_name','le_wh_id');*/                
        //$status =  $this->productModel->getObjNameByUrl(); 
        //$whId = Session::get('warehouseId');
        //$counts = $this->getCounts($whId);
        //print_r($counts);exit;
       /* $user_id=Session::get('userId');
        $userData = DB::select(DB::raw('select * from users u join legal_entities l on u.`legal_entity_id`= l.legal_entity_id where l.`legal_entity_type_id` IN (1006,1002,89002) AND  u.is_active=1 and u.user_id='.$user_id ));
        $showFilterData=true;
        if(!empty($userData)){
            $showFilterData=false;
        }*/
            //$beneficiaryName = $this->objPricing->getBenificiaryName();         
            //$wareHouse = $this->objPricing->getWarehouses();          
            //$product_stars = $this->objPricing->getProductStars();
            //$pack_types_data=  $this->getMasterLookUpData('16','Levels');
            return View::make('Product::products_config', ['warehouseFeature'=>$warehouseFeature,'importexportFeature'=>$importexportFeature,'uploadProductFeature'=>$uploadProductFeature,"allProductFeature"=>$allProductFeature,'cp_Enable_Sheet'=>$cp_enable_sheet,'exportAllProductElpFeature'=>$exportAllProductElpFeature,'import_prd_cust_esu'=>$import_prd_cust_esu]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function cpEnableDcFcProducts()
    {

         $data=Input::all();
         $page = isset($data['page'])?$data['page']:'';   //Page number
         $pageSize = isset($data['pageSize'])?$data['pageSize']:''; //Page size for ajax call
         if(is_numeric($page) && is_numeric($pageSize)){
         $skip = $page * $pageSize;
         }else{
           $skip =''; 
         }
         $makeFinalSql = array();
         $filter = isset($data['$filter'])?$data['$filter']:'';
         $roleObj = new Role();
         $user_id = Session::get('user_id');
         $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
         $filters = json_decode($Json['sbu'], 1);            
         $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
         $dc_acess_list=explode(',', $dc_acess_list);
         $this->objCommonGrid = new commonIgridController();
         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("display_name", $filter, false);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $this->objCommonGrid = new commonIgridController();
         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("esu", $filter, false);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        if(isset($data['%24orderby'])){
            $orderBy = $data['%24orderby'];
            if($orderBy==''){
                $orderBy = $data['$orderby'];
            }else{
                $orderBy = '';
            }
        }else{
            $orderBy = '';
        }

        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }

        $sqlWhrCls = '';
        $countLoop = 0;

        
        foreach ($makeFinalSql as $value) {

           
            if( $countLoop==0 ){
                $sqlWhrCls .=  $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }
        if(!empty($sqlWhrCls)){
            $sqlWhrCls.=' and';
        }
        $sqlWhrCls .= ' lw.status=1';
         $getdcfclistbyproduct=DB::table('legalentity_warehouses as lw')
                          ->select('lw.le_wh_id','lw.display_name','pcdf.cp_enabled','pcdf.is_sellable','pcdf.esu','pcdf.product_id','pcdf.esu')
                          ->leftjoin('product_cpenabled_dcfcwise as pcdf',function($query)use($data)
                          {
                           $query->on('lw.le_wh_id','=','pcdf.le_wh_id');
                           $query->on('pcdf.product_id','=',DB::raw($data['product_id']));
            });
                         $getdcfclistbyproduct=$getdcfclistbyproduct->whereIn('lw.le_wh_id',$dc_acess_list);
                         $getdcfclistbyproduct=$getdcfclistbyproduct->whereRaw($sqlWhrCls);
        $getdcfclistbyproductcount = $getdcfclistbyproduct->count(); 
        if(is_numeric($skip)){                 
        $getdcfclistbyproduct=$getdcfclistbyproduct->skip($skip)->take($pageSize)
                          ->get()->all();
          }else{
        $getdcfclistbyproduct=$getdcfclistbyproduct->get()->all();
          }                                 
                        
        $getdcfclist=json_decode(json_encode($getdcfclistbyproduct),true);
        for($dcfc=0;$dcfc<count($getdcfclist);$dcfc++)
        {
         
            if(!empty($getdcfclist[$dcfc]['product_id']) && $data['product_id']==$getdcfclist[$dcfc]['product_id'] && $getdcfclist[$dcfc]['cp_enabled']!='' && $getdcfclist[$dcfc]['cp_enabled']!=NULL && $getdcfclist[$dcfc]['cp_enabled']!=0)
             {   
               $getdcfclist[$dcfc]['cp_dcfc_enable']=' <label class="switch "><input class="switch-input vr_status4"  type="checkbox" checked="true" id="product_cp_enabled_'.$getdcfclist[$dcfc]['le_wh_id'].'"  name="product_cp_enabled_'.$getdcfclist[$dcfc]['le_wh_id'].'" onclick="cpenableproduct('.$getdcfclist[$dcfc]['le_wh_id'].')" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>';
              }else{
                   $getdcfclist[$dcfc]['cp_dcfc_enable']='<label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="product_cp_enabled_'.$getdcfclist[$dcfc]['le_wh_id'].'"  name="product_cp_enabled_'.$getdcfclist[$dcfc]['le_wh_id'].'"  onclick="cpenableproduct('.$getdcfclist[$dcfc]['le_wh_id'].')" ><span class="switch-label" data-off="No"></span><span class="switch-handle"></span></label>';
              } 
         }
         


        for($dcfc=0;$dcfc<count($getdcfclist);$dcfc++)
        {
          
            if(!empty($getdcfclist[$dcfc]['product_id']) && $data['product_id']==$getdcfclist[$dcfc]['product_id'] && $getdcfclist[$dcfc]['is_sellable']!='' && $getdcfclist[$dcfc]['is_sellable']!=NULL && $getdcfclist[$dcfc]['is_sellable']!=0)
             {   
               $getdcfclist[$dcfc]['sellable_dcfc_enable']=' <label class="switch "><input class="switch-input vr_status4"  type="checkbox" checked="true" id="product_is_sellable_'.$getdcfclist[$dcfc]['le_wh_id'].'" name="product_is_sellable_'.$getdcfclist[$dcfc]['le_wh_id'].'" onclick="issellableproduct('.$getdcfclist[$dcfc]['le_wh_id'].')"><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>';
              }else{
                           // echo 'nulllll';echo '<br/>';
                   $getdcfclist[$dcfc]['sellable_dcfc_enable']='<label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="product_is_sellable_'.$getdcfclist[$dcfc]['le_wh_id'].'" name="product_is_sellable_'.$getdcfclist[$dcfc]['le_wh_id'].'" onclick="issellableproduct('.$getdcfclist[$dcfc]['le_wh_id'].')"><span class="switch-label" data-off="No"></span><span class="switch-handle"></span></label>';
              } 
         }
         

        for($dcfc=0;$dcfc<count($getdcfclist);$dcfc++)
        {
         
            if(!empty($getdcfclist[$dcfc]['esu']) && $data['product_id']==$getdcfclist[$dcfc]['product_id'])
             {   
               $getdcfclist[$dcfc]['esu']='<input type="text" id="esu_'.$getdcfclist[$dcfc]['le_wh_id'].'" name="esu_'.$getdcfclist[$dcfc]['le_wh_id'].'" value='.$getdcfclist[$dcfc]['esu'].' readonly="readonly"><i id="edit-esu_'.$getdcfclist[$dcfc]['le_wh_id'].'" class="fa fa-pencil-square-o btn green-meadow" title = "Edit Esu" onclick="editEsu('.$getdcfclist[$dcfc]['le_wh_id'].')"></i><i id="save-esu_'.$getdcfclist[$dcfc]['le_wh_id'].'" class="fa fa-floppy-o btn green-meadow" title = "Save ESU" onclick="esuSave('.$getdcfclist[$dcfc]['le_wh_id'].')"></i>';
              }else{
                $getdcfclist[$dcfc]['esu']='<input type="text" id="esu_'.$getdcfclist[$dcfc]['le_wh_id'].'" name="esu_'.$getdcfclist[$dcfc]['le_wh_id'].'" readonly="readonly"><i id="edit-esu_'.$getdcfclist[$dcfc]['le_wh_id'].'" class="fa fa-pencil-square-o btn green-meadow" title = "Edit ESU" onclick="editEsu('.$getdcfclist[$dcfc]['le_wh_id'].')"></i><i id="save-esu_'.$getdcfclist[$dcfc]['le_wh_id'].'" class="fa fa-floppy-o btn green-meadow" title = "Save ESU" onclick="esuSave('.$getdcfclist[$dcfc]['le_wh_id'].')"></i>';
              } 
         }
                           
         echo json_encode(array('Records' => $getdcfclist, 'TotalRecordsCount' => $getdcfclistbyproductcount));
    }

    public function cpEnabled1($data=array()){
        try{
            if(!count($data))
                $data=Input::all();
            else
                $data = $data;
           $date=date('Y-m-d H:i:s');
           if($data['dcid']!=0){
           

           $this->userId = Session::get('userId');
           $this->ProductEPModelObj= new ProductEPModel();
           $pricing = $this->ProductEPModelObj->productPricing($data['productid'],$this->userId,$data['dcid']);
           $tax = $this->productTaxModelByWarehouse($data['productid'],$data['dcid']);
 
           $productInfoObj = new ProductInfo();  
           //$productData= $productInfoObj->getProductData($data['productid']);
           $productData= $this->isSellableProductByDcid($data);
           $sellableprdt= json_decode(json_encode($productData),true);
           if($data['cpenable']==1)
           {
                  if($pricing=='' || $tax=='' || $sellableprdt[0]['is_sellable']=='0' || empty($sellableprdt) || $sellableprdt[0]['is_sellable']=='')
                  {
                   return 'Tax/Pricing/is Sellable  not available for this product';
                   die();
                  }

                  /*if($tax==2)
                  {
                    return 'One/More Tax approval is pending for this product';
                    die();
                  }*/
           }

           $check_warehouseproductid=DB::table('product_cpenabled_dcfcwise')
                                         ->select('product_cpenabled_dcfc_id')
                                         ->where('product_id',$data['productid'])
                                         ->where('le_wh_id',$data['dcid'])
                                         ->get()->all();

           if(count($check_warehouseproductid)>0){
            $insert_warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                                            ->where('le_wh_id',$data['dcid'])
                                            ->where('product_id',$data['productid'])
                                            ->update(['cp_enabled'=>$data['cpenable'],'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
           }else{
             $insert_warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                                      ->insert(['product_id'=>$data['productid'],
                                                'le_wh_id'=>$data['dcid'],
                                                 'cp_enabled'=>$data['cpenable'],'created_by'=>Session::get('userId'),'created_at'=>$date]);
           }
           $getwarehouse_name=DB::table('legalentity_warehouses')
                                   ->select('display_name')
                                   ->where('le_wh_id',$data['dcid'])
                                   ->where('status',1)
                                   ->get()->all();   
            $getwarehouse_name=json_decode(json_encode($getwarehouse_name),true);
           if($insert_warehouseproduct){
            $insert_warehouseproduct='CP Enable Status Updated Successfully for '.$getwarehouse_name[0]['display_name'];
           }else{
            $insert_warehouseproduct='Failed to Update CP Enable Status for '.$getwarehouse_name[0]['display_name'];
           }
        }else{

            $roleObj = new Role();
             $user_id = Session::get('user_id');
             $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
             $filters = json_decode($Json['sbu'], 1);            
             $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
             $dc_acess_list=explode(',', $dc_acess_list);
            $getallactivedcs=DB::table('legalentity_warehouses')
                                      ->select('le_wh_id')
                                      ->where('status',1)
                                      ->whereIn('le_wh_id',$dc_acess_list)
                                      ->get()->all();
              $getallactivedcs=json_decode(json_encode($getallactivedcs),true);
              
                foreach($getallactivedcs as $dcs)
                {
                   $insert_warehouseproduct='';
                   $this->userId = Session::get('userId');
                   $this->ProductEPModelObj= new ProductEPModel();
                   $pricing = $this->ProductEPModelObj->productPricing($data['productid'],$this->userId,$dcs['le_wh_id']);
                   $tax = $this->productTaxModelByWarehouse($data['productid'],$dcs['le_wh_id']);
         
                   $productInfoObj = new ProductInfo(); 
                   $data['dcid']= $dcs['le_wh_id'];
                   $productData= $this->isSellableProductByDcid($data);
                   $sellableprdt= json_decode(json_encode($productData),true);
                   $check_warehouseproductid=DB::table('product_cpenabled_dcfcwise')
                                             ->select('product_cpenabled_dcfc_id')
                                             ->where('product_id',$data['productid'])
                                             ->where('le_wh_id',$dcs['le_wh_id'])
                                             ->get()->all();

                   if($data['product_cp_enable']==1)
                   {

                    if($pricing!='' && $tax!='' && $sellableprdt[0]['is_sellable']!='0' && !empty($sellableprdt) && $sellableprdt[0]['is_sellable']!='')
                    {
                              
                           if(count($check_warehouseproductid)>0)
                           {
                            $insert_warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                                                            ->where('le_wh_id',$dcs['le_wh_id'])
                                                            ->where('product_id',$data['productid'])
                                                            ->update(['cp_enabled'=>$data['product_cp_enable'],'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
                                    if($insert_warehouseproduct)
                                    {
                                         $insert_warehouseproduct='CP Enable Status Updated Successfully For All Warehouses having Pricing and Tax';
                                    }else{
                                        $insert_warehouseproduct='CP Status Changed Successfully';
                                    }                            
                           }else{
                             $insert_warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                                                      ->insert(['product_id'=>$data['productid'],
                                                                'le_wh_id'=>$dcs['le_wh_id'],
                                                                 'cp_enabled'=>$data['product_cp_enable'],'created_by'=>Session::get('userId'),'created_at'=>$date]);
                             if($insert_warehouseproduct)
                                {
                                     $insert_warehouseproduct='CP Enable Status Updated Successfully For All Warehouses having Pricing and Tax';
                                }else{
                                    $insert_warehouseproduct='CP Status Changed Successfully';
                                }

                           }  

                    }else{
                                
                             $insert_warehouseproduct='CP Status Changed Successfully';
                   }
                    }else{
                        if(count($check_warehouseproductid)>0)
                           {
                            $insert_warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                                                            ->where('le_wh_id',$dcs['le_wh_id'])
                                                            ->where('product_id',$data['productid'])
                                                            ->update(['cp_enabled'=>$data['product_cp_enable'],'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
                                    if($insert_warehouseproduct)
                                    {
                                         $insert_warehouseproduct='CP Enable Status Updated Successfully For All Warehouses having Pricing and Tax';
                                    }else{
                                        $insert_warehouseproduct='CP Status Changed Successfully';
                                    }                            
                           }else{
                             $insert_warehouseproduct=DB::table('product_cpenabled_dcfcwise')
                                                      ->insert(['product_id'=>$data['productid'],
                                                                'le_wh_id'=>$dcs['le_wh_id'],
                                                                 'cp_enabled'=>$data['product_cp_enable'],'created_by'=>Session::get('userId'),'created_at'=>$date]);
                             if($insert_warehouseproduct)
                                {
                                     $insert_warehouseproduct='CP Enable Status Updated Successfully For All Warehouses having Pricing and Tax';
                                }else{
                                    $insert_warehouseproduct='CP Status Changed Successfully';
                                }

                           }
                    }
                }
                                        

        }                                
           return $insert_warehouseproduct; 
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

public function isSellable($data=array()){
    try{
        $date=date('Y-m-d H:i:s');
        if(!count($data))
            $data=Input::all();
        else
            $data = $data;
        
        if($data['dcid']!=0){    
               $check_warehouseproductid=$this->productModel->getProductIsSellableStatusByWarehouse($data['productid'],$data['dcid'],$data['issellable']);

               $getwarehouse_name=DB::table('legalentity_warehouses')
                                       ->select('display_name')
                                       ->where('le_wh_id',$data['dcid'])
                                       ->where('status',1)
                                       ->get()->all();   
                $getwarehouse_name=json_decode(json_encode($getwarehouse_name),true);
               if($check_warehouseproductid){
                $insert_warehouseproductissellable='Is Sellable Status Updated Successfully for '.$getwarehouse_name[0]['display_name'];
               }else{
                $insert_warehouseproductissellable='Failed to Update Is Sellable Status for '.$getwarehouse_name[0]['display_name'];
               }

        }else{
               $roleObj = new Role();
                 $user_id = Session::get('user_id');
                 $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
                 $filters = json_decode($Json['sbu'], 1);            
                 $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
                 $dc_acess_list=explode(',', $dc_acess_list);
               $getallactivedcs=DB::table('legalentity_warehouses')
                                      ->select('le_wh_id')
                                      ->where('status',1)
                                      ->whereIn('le_wh_id',$dc_acess_list)
                                      ->get()->all();
              $getallactivedcs=json_decode(json_encode($getallactivedcs),true);
              $success=0;
              $failed=0;
              
                foreach($getallactivedcs as $dcs)
                {
                    $check_warehouseproductid=$this->productModel->getProductIsSellableStatusByWarehouse($data['productid'],$dcs['le_wh_id'],$data['issellable_prdt']);
                    if($check_warehouseproductid){
                            $success=$success+1;
                    }else{
                        $failed=$failed+1;
                    } 

                }                        

                $insert_warehouseproductissellable="For ".$success." dcs Sellable status  Updated Successfully and for ".$failed." dcs failed to update Sellable status for not having tax/pricing/sellable";



        }                            
           return $insert_warehouseproductissellable; 
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

 public function saveEsuforDc(){
    try{
        $data=Input::all();
        $date=date('Y-m-d H:i:s');
        if($data['dcid']!=0){
               $check_warehouseproductid=DB::table('product_cpenabled_dcfcwise')
                                             ->select('product_cpenabled_dcfc_id')
                                             ->where('product_id',$data['productid'])
                                             ->where('le_wh_id',$data['dcid'])
                                             ->get()->all();

               if(count($check_warehouseproductid)>0){
                $insert_warehouseproduct_esu=DB::table('product_cpenabled_dcfcwise')
                                                ->where('le_wh_id',$data['dcid'])
                                                ->where('product_id',$data['productid'])
                                                ->update(['esu'=>$data['esuval'],'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
               }else{
                 $insert_warehouseproduct_esu=DB::table('product_cpenabled_dcfcwise')
                                          ->insert(['product_id'=>$data['productid'],
                                                    'le_wh_id'=>$data['dcid'],
                                                     'esu'=>$data['esuval'],'created_by'=>Session::get('userId'),'created_at'=>$date]);
               } 

               $getwarehouse_name=DB::table('legalentity_warehouses')
                                       ->select('display_name')
                                       ->where('le_wh_id',$data['dcid'])
                                       ->where('status',1)
                                       ->get()->all();   
                $getwarehouse_name=json_decode(json_encode($getwarehouse_name),true);
               if($insert_warehouseproduct_esu){
                $insert_warehouseproduct_esu='ESU Updated Successfully for '.$getwarehouse_name[0]['display_name'];
               }else{
                $insert_warehouseproduct_esu='Failed to Update ESU for '.$getwarehouse_name[0]['display_name'];
               }

        }else{
               $roleObj = new Role();
                 $user_id = Session::get('userId');
                 $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
                 $filters = json_decode($Json['sbu'], 1);            
                 $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
                 $dc_acess_list=explode(',', $dc_acess_list);
              $getallactivedcs=DB::table('legalentity_warehouses')
                                      ->select('le_wh_id')
                                      ->where('status',1)
                                      ->whereIn('le_wh_id',$dc_acess_list)
                                      ->get()->all();
              $getallactivedcs=json_decode(json_encode($getallactivedcs),true);
              
                foreach($getallactivedcs as $dcs)
                {
                    $check_warehouseproductid=DB::table('product_cpenabled_dcfcwise')
                                             ->select('product_cpenabled_dcfc_id')
                                             ->where('product_id',$data['productid'])
                                             ->where('le_wh_id',$dcs['le_wh_id'])
                                             ->get()->all();

                   if(count($check_warehouseproductid)>0){
                    $insert_warehouseproduct_esu=DB::table('product_cpenabled_dcfcwise')
                                                    ->where('le_wh_id',$dcs['le_wh_id'])
                                                    ->where('product_id',$data['productid'])
                                                    ->update(['esu'=>$data['esu_val'],'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
                        if($insert_warehouseproduct_esu)
                        {
                            $insert_warehouseproduct_esu='ESU Updated Successfully';
                        }else{
                            $insert_warehouseproduct_esu='Failed to Update ESU';
                        }                            
                   }else{
                     $insert_warehouseproduct_esu=DB::table('product_cpenabled_dcfcwise')
                                              ->insert(['product_id'=>$data['productid'],
                                                        'le_wh_id'=>$dcs['le_wh_id'],
                                                         'esu'=>$data['esu_val'],'created_by'=>Session::get('userId'),'created_at'=>$date]);
                        if($insert_warehouseproduct_esu)
                        {
                            $insert_warehouseproduct_esu='ESU Updated Successfully';
                        }else{
                            $insert_warehouseproduct_esu='Failed to Update ESU';
                        }                      
                   } 

                }                        

            }                         
           return $insert_warehouseproduct_esu; 
    }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function isSellableProductByDcid($data){
        try{
         $checkissellablebywarehouse=DB::table('product_cpenabled_dcfcwise')
                                          ->select('is_sellable')
                                          ->where('product_id',$data['productid'])
                                          ->where('le_wh_id',$data['dcid'])
                                          ->get()->all();
        if(empty($checkissellablebywarehouse)){
            $checkissellablebywarehouse[0]['is_sellable']='';
            //$checkissellablebywarehouse=json_decode($checkissellablebywarehouse,true);
            $checkissellablebywarehouse=(object) $checkissellablebywarehouse;

        }
        return $checkissellablebywarehouse;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }   

    public function productTaxModelByWarehouse($pid,$le_wh_id){
        try{

            $taxcheckforproductbywarehouse=DB::select("select getProductTaxByWarehouse('".$pid."','".$le_wh_id."') as taxcount");
            //print_r($taxcheckforproductbywarehouse);exit;
            $taxcount=$taxcheckforproductbywarehouse[0]->taxcount;

            return $taxcount;
            }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function checkProductProp($product_id,$le_wh_id){
        return $this->productModel->checkProductProp($product_id,$le_wh_id);
    }

    public function getProductSU($product_id){
        return $this->productModel->getProductSU($product_id);
    }

    public function downloadCPEnableExcel(){
       
        //$inputproductid = Input::get('product_id');
        
        $pim_data = array(
            'Product ID','Product Title','CP Enabled', 'Is Sellable', 'ESU','Warehouse ID');

        $required_data = array('product_id' => 'required','product_title'=>'required', 'cp_enabled'=>'required', 'is_sellable' => 'required', 'esu' => 'required','le_wh_id' => 'required'); 




        $data['cp_data'] = array(
            $required_data,
            $pim_data
        );

        
        if (Input::get('with_data') != '') {
            $products_query = DB::table('product_cpenabled_dcfcwise')->join('products','product_cpenabled_dcfcwise.product_id','=','products.product_id')->select('product_cpenabled_dcfcwise.product_id','product_cpenabled_dcfcwise.cp_enabled','product_cpenabled_dcfcwise.is_sellable','product_cpenabled_dcfcwise.esu','products.product_title','product_cpenabled_dcfcwise.le_wh_id');
            $products = $products_query->get()->all();

            
            $product_data = json_decode(json_encode($products), True);


            foreach ($product_data as $product) {

                if($product['product_id']!=null && $product['product_id']!=0){
                   $product_id = $product['product_id'];    
                }else{
                    $product_id =0;
                }

                if($product['cp_enabled']!=null && $product['cp_enabled']!=0){
                   $cp_enabled = $product['cp_enabled'];    
                }else{
                    $cp_enabled =0;
                }

                if($product['is_sellable']!=null && $product['is_sellable']!=0){
                   $is_sellable = $product['is_sellable'];    
                }else{
                    $is_sellable =0;
                }

                if($product['esu']!=null && $product['esu']!=0){
                   $esu = $product['esu'];    
                }else{
                    $esu ='';
                }
                
            
                $prod = array(
                    'product_id' => $product_id,
                    'product_title'=>$product['product_title'],
                    'cp_enabled' => $cp_enabled,
                    'is_sellable' => $is_sellable,
                    'esu' => $esu,
                    'le_wh_id'=>$product['le_wh_id']
                );

                $data['cp_data'][] = $prod;
            }
        }

        $headings = array('Warehouse ID', 'Warehouse Name',  'Warehouse Code');
        $data['warehouse'][]=$headings;

        $warehouselist=DB::table('legalentity_warehouses')->select('le_wh_id','display_name','le_wh_code')->where('dc_type','118001')->get()->all();

        $warehouselist=json_decode(json_encode($warehouselist),true);

        foreach ($warehouselist as $warehouselist) {
            $warehouselist=array(
                'Warehouse ID'=>$warehouselist['le_wh_id'],
                'Warehouse Name'=>$warehouselist['display_name'],
                'Warehouse Code'=>$warehouselist['le_wh_code'],
            );
            $data['warehouse'][]=$warehouselist;
        }
        $file_name = 'CP Status Template_' . date('Y-m-d');
        $result = Excel::create($file_name, function($excel) use($data) {
                    $excel->sheet('Sheet1', function($sheet) use($data) {
                        $sheet->fromArray($data['cp_data'], null, 'A1', false, false);
                        $sheet->protectCells('A1', 'password');
                        $sheet->protectCells('B1', 'password');
                    });

                     $excel->sheet('Sheet2', function($sheet) use($data) {
                        $sheet->fromArray($data['warehouse'], null, 'A1', false, false);
                    });
                })->export('xlsx');
        exit;
    }


    public function uploadCPEnableExcelSheet(){
        try{
                    DB::beginTransaction();
                    ini_set('max_execution_time', 1200);
                    $message = '';
                    $msg = '';
                    $mail_msg = '';
                    $report_table = '';
                    $status = 'failed';
                    $replace_values = array('NA', 'N/A');
                    $supplierModel = new SupplierModel();
               
                    $required_data = array('product_id' => 'required', 'product_title' => 'required','cp_enabled' => 'required', 'is_sellable' => 'required', 'esu' => 'required','warehouse_id' => 'required'); 


                    if (Input::hasFile('import_file')) {
                        $path = Input::file('import_file')->getRealPath();
                        $data = $this->readExcelCpEnable($path);
                        $data = json_decode(json_encode($data), 1);
                        if (isset($data['prod_data']) && count($data['prod_data']) > 0) {
                            $prod_data = $data['prod_data'];
                                $pr_scount = 0;
                                $pr_fcount = 0;
                                foreach ($prod_data as $product) {
                                    $product_name = (isset($product['product_name'])) ? $product['product_name'] : '';

                                    $required_check_msg = array();
                                    
                                    foreach($required_data as $required_data_key=>$required) {
                                        if($required=='required')
                                        {
                                            if(!isset($product[$required_data_key]) && $product[$required_data_key] == '')
                                            {

                                                $required_check_msg[]=$required_data_key;
                                            }                                   
                                        }
                                        
                                    }
                                    if (count($required_check_msg) == 0) {

                                        $timestamp = md5(microtime(true));
                                        $txtFileName = 'cpstatus-import-' . $timestamp . '.html';
                                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'cpstatus_logs' . DIRECTORY_SEPARATOR . $txtFileName;
                                        $files_to_delete = File::files('download' . DIRECTORY_SEPARATOR . 'cpstatus_logs/');
                                        File::delete($files_to_delete);

                                            $product_id = $product['product_id'];
                                            $cp_enabled = $product['cp_enabled'];
                                            $is_sellable = $product['is_sellable'];
                                            $esu = $product['esu'];
                                            $dcid =$product['warehouse_id'];

                                            $dc_name=DB::table('legalentity_warehouses')->select('display_name')->where('le_wh_id',$dcid)->first();

                                            //find whether product exits in products table 

                                            $findproductexits=DB::table('products')->select('product_id','cp_enabled','is_sellable')->where('product_id',$product_id)->first();
                                            $this->userId = Session::get('userId');

              

                                                    if (count($findproductexits) != 0 && count($dc_name)!=0) {

                                                        if($findproductexits->cp_enabled!=0 && $findproductexits->is_sellable!=0){

                                                            $this->ProductEPModelObj= new ProductEPModel();
                                                            $pricing = $this->ProductEPModelObj->productPricing($product_id,$this->userId,$dcid);
                                                            $tax=$this->productTaxModelByWarehouse($product_id,$dcid);

                                                            if($pricing!='' && $tax!='' && ($is_sellable==1 && !empty($is_sellable)) && ($cp_enabled==1) && !empty($cp_enabled)){

                                                                $Tot_Array = array(
                                                                    'product_id' => $product_id,
                                                                    'cp_enabled' =>$cp_enabled,
                                                                    'is_sellable' =>$is_sellable,
                                                                    'esu'         =>$esu,
                                                                    'updated_by' => Session::get('userId')
                                                                );
                                                                $message .= 'Sellable,CP Enabled and ESU Updated  for '.$product['product_title'].' with Product ID ('.$product_id.') for Warehouse '.$dc_name->display_name;

                                                            }elseif($is_sellable==1 && $cp_enabled==0){
                                                                $Tot_Array = array(
                                                                    'product_id' => $product_id,
                                                                    'cp_enabled' =>0,
                                                                    'is_sellable' =>$is_sellable,
                                                                    'esu'         =>$esu,
                                                                    'updated_by' => Session::get('userId')
                                                                );
                                                                $message .= 'Sellable Enabled,CP Disabled  for '.$product['product_title'].' with Product ID ('.$product_id.') for warehouse '.$dc_name->display_name;
                                                            }else{
                                                                $Tot_Array = array(
                                                                    'product_id' => $product_id,
                                                                    'cp_enabled' =>0,
                                                                    'is_sellable' =>$is_sellable,
                                                                    'esu'         =>$esu,
                                                                    'updated_by' => Session::get('userId')
                                                                );
                                                                if($is_sellable==1){
                                                                    $sellable_sts='enabled';
                                                                }else{
                                                                    $sellable_sts='disabled';
                                                                }
                                                                $message .= $product['product_title'].'  with Product ID ('.$product_id.')'.' does not have pricing or tax and sellable is '.$sellable_sts.',CP can\'t be enabled for Warehouse '.$dc_name->display_name;
                                                            }

                                                            //$checkrecordincpenabletable=DB::table('product_cpenabled_dcfcwise')->where('product_id',$product_id)->where('le_wh_id',$dcid)->get();

                                                            $checkrecordincpenabletable="select product_cpenabled_dcfc_id from product_cpenabled_dcfcwise where product_id=".$product_id." and le_wh_id=".$dcid;

                                                            $checkrecordincpenabletable=DB::selectFromWriteConnection(DB::raw($checkrecordincpenabletable));

                                                            if(count($checkrecordincpenabletable)>0){
                                                                $Tot_Array['updated_at']=date('Y-m-d H:i:s');
                                                                $updaterecordincptable=DB::table('product_cpenabled_dcfcwise')->where(array('product_id'=>$product_id, 'le_wh_id'=>$dcid))->update($Tot_Array);
                                                                if($updaterecordincptable){
                                                                    $pr_scount++;//success count
                                                                }else{
                                                                    $pr_fcount++;//fail count
                                                                }
                                                                
                                                            }else{
                                                                $Tot_Array['le_wh_id']=$dcid;
                                                                $Tot_Array['created_by']=Session::get('userId');
                                                                $Tot_Array['created_at']=date('Y-m-d H:i:s');
                                                                $Tot_Array['updated_at']=date('Y-m-d H:i:s');
                                                                $insertrecordincptable = DB::table('product_cpenabled_dcfcwise')->insert($Tot_Array);
                                                                if($insertrecordincptable){
                                                                    $pr_scount++;//success count
                                                                }else{
                                                                    $pr_fcount++;//fail count
                                                                }
                                                            }



                                                        }else{
                                                            $message .=$product['product_title'].' product is not active at Product Level';
                                                        }
                                                           
                                                    } else {
                                                        $message .=$product['product_title'].' product not subscribed or Warehouse ID Entered is Wrong';
                                                        
                                                    }                                
                                    } else {
                                        $message .= 'All mandatory fields need to be filled for '.$product['product_title'];
                                        $pr_fcount++;
                                    }
                                    $message .='<br/><br/>';
                                }
                                $msg .= $pr_scount . ' Products Created/Updated Successfully and ' . $pr_fcount . ' Products failed to Create/Update';
                                $status = 'success';
                                }
                                DB::commit();
                    } else {
                        DB::rollback();
                        $msg = 'Please upload file';
                    }

                            $message .= PHP_EOL;
                            $status = 400;
                            $url = "";
                            //create the log file as per the excel sheet
                            if(isset($file_path)){
                                $file = fopen($file_path, "w");
                                fwrite($file, $message);
                                fclose($file);
                                $url = $file_path;
                                $message = "Click <a href=".'/'.$file_path." target='_blank'> here </a> to view details.";
                            }
                    /* } catch (\ErrorException $ex) {
                      Log::error($ex->getMessage());
                      } */
                    if(!empty($message)){
                        Session::flash('test', $message);
                    }  
                    $messg = json_encode(array('status' => $status, 'message' => $msg, 'status_messages' => $message));
                    return $messg;
        }catch (\ErrorException $ex) {
            DB::rollback();
            $messg = json_encode(array('status' => 400, 'message' =>'', 'status_messages' => "Sorry Failed to Upload Sheet,Reverting all Records. Please check log for More Details"));
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $messg;
        }
    }


        public function readExcelCpEnable($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

        public function cpEnabled($data=array()){
        try{
            if(!count($data))
                $data=Input::all();
            else
                $data = $data;
           $date=date('Y-m-d H:i:s');
           if($data['dcid']!=0){
           

           $this->userId = Session::get('userId');
           if($data['cpenable']==1)
           {
                    $this->ProductEPModelObj= new ProductEPModel();
                    $pricing = $this->ProductEPModelObj->productPricing($data['productid'],$this->userId,$data['dcid']);
                    $tax = $this->productTaxModelByWarehouse($data['productid'],$data['dcid']);
         
                    $productInfoObj = new ProductInfo();  
                    $productData= $this->isSellableProductByDcid($data);
                    $sellableprdt= json_decode(json_encode($productData),true);

                      if($pricing=='' || $tax=='' || $sellableprdt[0]['is_sellable']=='0' || empty($sellableprdt) || $sellableprdt[0]['is_sellable']=='')
                      {
                        return 'Tax/Pricing/is Sellable  not available for this product';
                        die();
                      }
           }

           $insert_warehouseproduct=$this->productModel->getProductCPStatusByWarehouse($data['productid'],$data['dcid'],$data['cpenable']);

           $getwarehouse_name=DB::table('legalentity_warehouses')
                                   ->select('display_name')
                                   ->where('le_wh_id',$data['dcid'])
                                   ->where('status',1)
                                   ->get()->all();   
            $getwarehouse_name=json_decode(json_encode($getwarehouse_name),true);
           if($insert_warehouseproduct){
            $insert_warehouseproduct='CP Enable Status Updated Successfully for '.$getwarehouse_name[0]['display_name'];
           }else{
            $insert_warehouseproduct='Failed to Update CP Enable Status for '.$getwarehouse_name[0]['display_name'];
           }
        }else{

            $roleObj = new Role();
             $user_id = Session::get('user_id');
             $Json = json_decode($roleObj->getFilterData(6,$user_id), 1);
             $filters = json_decode($Json['sbu'], 1);            
             $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
             $dc_acess_list=explode(',', $dc_acess_list);
            $getallactivedcs=DB::table('legalentity_warehouses')
                                      ->select('le_wh_id')
                                      ->where('status',1)
                                      ->whereIn('le_wh_id',$dc_acess_list)
                                      ->get()->all();
              $getallactivedcs=json_decode(json_encode($getallactivedcs),true);
              $success=0;
              $failed=0;
                foreach($getallactivedcs as $dcs)
                {
                   $insert_warehouseproduct='';
                   $this->userId = Session::get('userId');
                   $this->ProductEPModelObj= new ProductEPModel();
                   $pricing = $this->ProductEPModelObj->productPricing($data['productid'],$this->userId,$dcs['le_wh_id']);
                   $tax = $this->productTaxModelByWarehouse($data['productid'],$dcs['le_wh_id']);
         
                   $productInfoObj = new ProductInfo(); 
                   $data['dcid']= $dcs['le_wh_id'];
                   $productData= $this->isSellableProductByDcid($data);
                   $sellableprdt= json_decode(json_encode($productData),true);

                    if(($pricing!='' && $tax!='' && $sellableprdt[0]['is_sellable']!='0' && !empty($sellableprdt) && $sellableprdt[0]['is_sellable']!='') || $data['product_cp_enable']==0)
                    {
                        $insert_warehouseproduct=$this->productModel->getProductCPStatusByWarehouse($data['productid'],$data['dcid'],$data['product_cp_enable']);

                        if($insert_warehouseproduct){
                            $success=$success+1;
                        }else{
                            $failed=$failed+1;
                        }


                    }else{
                            $failed=$failed+1;
                   }
                    
                }
                 $insert_warehouseproduct="For ".$success." dcs CP status  Updated Successfully and for ".$failed." dcs failed to update CP status for not having tax/pricing/sellable";                       

        }                                
           return $insert_warehouseproduct; 
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
/*Below function takes in product id as parameter to show list of all elp's,poid,product title,supplier and effective date,po code,DC or FC and parent of DC OR FC*/
    public function getElpHistoryByProduct()
    {

         $data=Input::all();
         $page = isset($data['page'])?$data['page']:'';   //Page number
         $pageSize = isset($data['pageSize'])?$data['pageSize']:''; //Page size for ajax call
         if(is_numeric($page) && is_numeric($pageSize)){
            $skip = $page * $pageSize;
         }else{
           $skip =''; 
         }
         $productid=$data['product_id'];
         $makeFinalSql = array();
         $filter = isset($data['$filter'])?$data['$filter']:'';
         $this->objCommonGrid = new commonIgridController();
         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("FC", $filter, false);
         $fieldQuery =str_replace('FC', '`getLeWhName`(
                            `pph`.`le_wh_id`)', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("DC", $filter, false);
        $fieldQuery =str_replace('DC', '(SELECT
                                 `getLeWhName`(
                            `dc_fc_mapping`.`dc_le_wh_id`) 
                               FROM `dc_fc_mapping`
                               WHERE (`dc_fc_mapping`.`fc_le_wh_id` = `pph`.`le_wh_id`))', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Supplier", $filter, false);
        $fieldQuery =str_replace('Supplier', '`getBusinessLegalName`(
                            `pph`.`supplier_id`)', $fieldQuery);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("PO_Code", $filter, false);
        $fieldQuery =str_replace('PO_Code', 'po_code', $fieldQuery);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Actual_Elp", $filter, false);
        $fieldQuery =str_replace('Actual_Elp', 'actual_elp', $fieldQuery);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("State", $filter, false);
        $fieldQuery =str_replace('State', 'IFNULL((SELECT `getStateNameById`(`legalentity_warehouses`.`state`) FROM (`dc_fc_mapping` JOIN `legalentity_warehouses` ON((`legalentity_warehouses`.`le_wh_id` = `dc_fc_mapping`.`dc_le_wh_id`))) WHERE (`dc_fc_mapping`.`fc_le_wh_id` = `pph`.`le_wh_id`)),`getStateNameById`(`le`.`state_id`))', $fieldQuery);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Dlp_Flp", $filter, false);
        $fieldQuery =str_replace('Dlp_Flp', 'elp', $fieldQuery);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Effective_Date", $filter, true);
        $fieldQuery =str_replace('Effective_Date', 'date(Effective_Date)', $fieldQuery);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        if(isset($data['%24orderby'])){
            $orderBy = $data['%24orderby'];
            if($orderBy==''){
                $orderBy = $data['$orderby'];
            }else{
                $orderBy = '';
            }
        }else{
            $orderBy = '';
        }

        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }

        $sqlWhrCls = '';
        $countLoop = 0;

        
        foreach ($makeFinalSql as $value) {

           
            if( $countLoop==0 ){
                $sqlWhrCls .=  $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }
        $productelphistory=$this->productModel->getProductElpHistory($productid,$sqlWhrCls,$skip,$pageSize);
                           
         echo json_encode(array('Records' => $productelphistory['records'],'TotalRecordsCount'=>$productelphistory['count']));
    }
/*Based on product Id we are importing elp's for every poid from purhcase price history table*/
    public function exportElpsByProductId(){
        $data=Input::all();
        //$productelphistory=DB::table('vw_ProductElpHistory')->select('*')->where('Product_ID',$data['product_id'])->get();
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
                               WHERE (`dc_fc_mapping`.`fc_le_wh_id` = `pph`.`le_wh_id`)) AS `APOB/DC`,
                              IFNULL((SELECT `getStateNameById`(`legalentity_warehouses`.`state`) FROM (`dc_fc_mapping` JOIN `legalentity_warehouses` ON((`legalentity_warehouses`.`le_wh_id` = `dc_fc_mapping`.`dc_le_wh_id`))) WHERE (`dc_fc_mapping`.`fc_le_wh_id` = `pph`.`le_wh_id`)),`getStateNameById`(`le`.`state_id`)) AS `State`,
                              `getLeWhName`(
                            `pph`.`le_wh_id`)  AS `Warehouse`,
                              `pph`.`elp`            AS `Dlp_Flp`,
                              `pph`.`actual_elp`     AS `Actual_Elp`,
                              date_format(`pph`.`effective_date`,'%d-%m-%Y') AS `Effective_Date`
                            FROM ((`purchase_price_history` `pph`
                                LEFT JOIN `po`
                                  ON ((`po`.`po_id` = `pph`.`po_id`)))
                               LEFT JOIN `legal_entities` `le`
                                 ON ((`le`.`legal_entity_id` = `pph`.`supplier_id`))) where `pph`.`product_id`=".$data['product_id']." order by `pph`.pur_price_id desc";
        $productelphistory=DB::select(DB::raw($productelphistory));
        $productelphistory=json_decode(json_encode($productelphistory),true);
        $file_name = 'Product_ELP_Template_'.$data['product_id'].'_'. date('d-m-Y');
        $result = Excel::create($file_name, function($excel) use($productelphistory) {
                    $excel->sheet('Sheet1', function($sheet) use($productelphistory) {
                        $sheet->fromArray($productelphistory);
                    });
                })->export('xlsx');
        exit;        
    }

    public function exportAllProductsElps(){
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', -1);
        $data=Input::all();
        $fdate = (isset($data['fdate']) && !empty($data['fdate'])) ? $data['fdate'] : date('Y-m').'-01';
        $fdate = date('Y-m-d 00:00:00', strtotime($fdate));
        $fdate=trim($fdate);
        $tdate = (isset($data['tdate']) && !empty($data['tdate'])) ? $data['tdate'] : date('Y-m-d');
        $tdate = date('Y-m-d 23:59:59', strtotime($tdate));
        $tdate=trim($tdate);
        $allproductelphistory="SELECT
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
                               WHERE (`dc_fc_mapping`.`fc_le_wh_id` = `pph`.`le_wh_id`)) AS `APOB/DC`,
                              IFNULL((SELECT `getStateNameById`(`legalentity_warehouses`.`state`) FROM (`dc_fc_mapping` JOIN `legalentity_warehouses` ON((`legalentity_warehouses`.`le_wh_id` = `dc_fc_mapping`.`dc_le_wh_id`))) WHERE (`dc_fc_mapping`.`fc_le_wh_id` = `pph`.`le_wh_id`)),`getStateNameById`(`le`.`state_id`)) AS `State`,
                              `getLeWhName`(
                            `pph`.`le_wh_id`)  AS `Warehouse`,
                              `pph`.`elp`            AS `Dlp_Flp`,
                              `pph`.`actual_elp`     AS `Actual_Elp`,
                              date_format(`pph`.`effective_date`,'%d-%m-%Y') AS `Effective_Date`
                            FROM ((`purchase_price_history` `pph`
                                LEFT JOIN `po`
                                  ON ((`po`.`po_id` = `pph`.`po_id`)))
                               LEFT JOIN `legal_entities` `le`
                                 ON ((`le`.`legal_entity_id` = `pph`.`supplier_id`)))  WHERE `pph`.`created_at` BETWEEN '$fdate' AND '$tdate' order by `pph`.pur_price_id desc";
        $allproductelphistory=DB::select(DB::raw($allproductelphistory));
        $allproductelphistory=json_decode(json_encode($allproductelphistory),true);
        $file_name = 'All_Product_ELP_Template_'.'_'. date('d-m-Y');
        $result = Excel::create($file_name, function($excel) use($allproductelphistory) {
                    $excel->sheet('Sheet1', function($sheet) use($allproductelphistory) {
                        $sheet->fromArray($allproductelphistory);
                    });
                })->export('xlsx');
        exit;  

    }

    //Product Color Configuration Functions - Start
    //function used to display the color configurations for the products
    public function productColorConfig(Request $request)
    {
        try {

            $checkEditProductPermissions=$this->_roleRepo->checkPermissionByFeatureCode('PRD0001');
            if ($checkEditProductPermissions==0)
            {
                return Redirect::to('/');
            }
            DB::enableQueryLog();      
            $breadCrumbs = array('Dashboard' => url('/'), 'Products' => '/products/all','Product Color Configurations'=>'#');
            parent::Breadcrumbs($breadCrumbs);

            parent::Title('Ebutor - '.trans('product_color_config.heads.title'));

            $userId = Session::get('userId');

            $wareHouseInfo = $this->_roleRepo->getAllDcs($userId);
            $getCustomerGroup = $this->objPricing->getCustomerGroup();
            $productEPModel = new ProductEPModel();
            $packageLevel = $productEPModel->getMasterLookUpPackageData('16','Levels');
            $colors = json_decode($this->objPricing->getProductStarsExcel(), true);

            $editdeletepermission=$this->_roleRepo->checkPermissionByFeatureCode('ETDLT001');

            //Need to prepare the json to display the data
            return View::make('Product::productColorConfig',['wareHouseInfo'=>$wareHouseInfo,'getCustomerGroup'=>$getCustomerGroup,'packageLevel'=>$packageLevel,'colors'=>$colors, 'editdeletepermission'=>$editdeletepermission]);
        }   
        catch(\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());            
        }         
    }

    //function used to display the product color config records in grid
    public function getProductColorConfig(Request $request) {

        try {

            // $status = $this->productModel->getStatusByUrl();
            $result = $this->productModel->getProductColorGridData($request); 
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    //function used to search the product in the add/edit color configuraton popup
    public function getProductNamesForSearch()
    {
        try{
            $data = \Input::all();
            
            $skus = $this->productModel->getProductNamesForSearch($data);
            return $skus;die;
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function addProductColorConfig()
    {
        $data = Input::all();
        // print_r($data);
        $result['data'] = $data;
        $result['status'] = false;

        $data = $this->validateInputData($data, "New");
       // print_r($data);
        if($data == []) return $result;

        //Adding New Record in the Table
        $result['status'] = $this->productModel->insertProductColorConfigRecord($data);
        return $result;
    }

    public function validateProdColorConfig()
    {
        $data = Input::all();
        $response["valid"] = FALSE;
        
        $result = $this->productModel->validateProdColorConfig($data);
        $response["valid"] = $result;
        
        return json_encode($response);

    }

    //function used to get the recored for edit
    public function editProductColorConfig($id)
    {
        if($id < 0 or $id != null){
            $data = $this->productModel->getProductColorConfigRecord($id);
            // print_r($data);
            if(!empty($data)){
                $result['status'] = true;
                $result['WareHouse_Name'] = $data[0]->WareHouse_Name;
                $result['Product_Id'] = $data[0]->Product_Id;
                $result['Product_Name'] = $data[0]->Product_Name;
                $result['Pack'] = $data[0]->Pack;
                $result['Customer_Type'] = $data[0]->Customer_Type;
                $result['Color'] =  $data[0]->Color;
                $result['Elp'] =  $data[0]->Elp;
                $result['Esp'] =  $data[0]->Esp;
                $result['Margin'] =  $data[0]->Margin;

                return $result;
            }
        }
        // If it reaches here, then it return false
        return ["status"=>false];
    }

    //function used to update the existing record
    public function updateProductColorConfig()
    {
        $data = Input::all();
        $result['data'] = $data;
        $result['status'] = false;

        $data = $this->validateInputData($data, "Old");
       
        // if($data == []) return $result;

        //Adding New Record in the Table        
        $result['status'] = $this->productModel->updateProductColorConfigRecord($data);
       
        return $result;
    }

    //function used to delete the record
    public function delProductColorConfig($id)
    {
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->productModel->delProductColorConfigRecord($id);
        return ["status" => $status];        
    }

    public function validateInputData($data,$action="")
    {
        $result = [];
        
        if($action == "New")
        {
            //Server End Validations
            if(empty($data['Add_WareHouse_Name'])) return $result;
            if(empty($data['add_product_id'])) return $result;
            if(empty($data['Add_Pack'])) return $result;
            if(empty($data['Add_Customer_Type'])) return $result;
            if(empty($data['Add_Color'])) return $result;
            // if(empty($data['Add_Elp'])) return $result;
            // if(empty($data['Add_Esp'])) return $result;
            // if(empty($data['Add_Margin'])) return $result;
        }
        else
        {
            //Server End Validations
            if(empty($data['Edit_WareHouse_Name'])) return $result;
            if(empty($data['edit_product_id'])) return $result;
            if(empty($data['Edit_Pack'])) return $result;
            if(empty($data['Edit_Customer_Type'])) return $result;
            if(empty($data['Edit_Color'])) return $result;
            // if(empty($data['Edit_Elp'])) return $result;
            // if(empty($data['Edit_Esp'])) return $result;
            // if(empty($data['Edit_Margin'])) return $result;
        }

        return $data;
    }

    public function downloadProdColorConfigExcel(){
       
        //$inputproductid = Input::get('product_id');
        
        $color_import_data = array(
            'Warehouse ID','Product SKU','Pack', 'Customer Type', 'Color');

        $required_data = array('le_wh_id' => 'required','product_id'=>'required', 'pack'=>'required', 'customer_type' => 'required', 'color' => 'required'); 

        $data['cp_data'] = array(
            $required_data,
            $color_import_data
        );

        //Building Reference Sheet - Start
        $userId = Session::get('userId');

        $wareHouseInfo = $this->_roleRepo->getAllDcs($userId);


        $getCustomerGroup   = $this->objPricing->getCustomerGroup();
        $productEPModel     = new ProductEPModel();
        $packageLevel       = $productEPModel->getMasterLookUpPackageData('16','Levels');
        $colors             = json_decode($this->objPricing->getProductStarsExcel(), true);

        $download_data_arr  = $this->productModel->getDownlodColorTemplateInfo($wareHouseInfo ,$getCustomerGroup ,$packageLevel , $colors);


        $headings = array('Warehouse ID', 'Warehouse Name','Product SKU' ,'Product Name','Pack', 'Customer Type', 'Color');

        $array_count = array(
            'Warehouse ID' => count($download_data_arr['warehouselist']),
            'Warehouse Name' => count($download_data_arr['warehouselist']),
            'Product SKU' => count($download_data_arr['products']),
            'Pack Type' => count($download_data_arr['pack_types_data']),
            'Customer Type' => count($download_data_arr['customer_data']),
            'Color' => count($download_data_arr['star_lookup_data']),
        );

        $sort = arsort($array_count);
        $data['options'][] = $headings;
        for ($i = 1; $i <= max($array_count); $i++) {
            $data['options'][$i][] = isset($download_data_arr['warehouselist'][$i-1]) ? $download_data_arr['warehouselist'][$i-1]->le_wh_id : '';
            $data['options'][$i][] = isset($download_data_arr['warehouselist'][$i-1]) ? $download_data_arr['warehouselist'][$i-1]->display_name : '';
            $data['options'][$i][] = isset($download_data_arr['products'][$i-1]) ? $download_data_arr['products'][$i-1]->sku : '';
            $data['options'][$i][] = isset($download_data_arr['products'][$i-1]) ? $download_data_arr['products'][$i-1]->product_title : '';
            $data['options'][$i][] = isset($download_data_arr['pack_types_data'][$i-1]) ? $download_data_arr['pack_types_data'][$i-1]->name : '';
            $data['options'][$i][] = isset($download_data_arr['customer_data'][$i-1]) ? $download_data_arr['customer_data'][$i-1]->master_lookup_name : '';
            $data['options'][$i][] = isset($download_data_arr['star_lookup_data'][$i-1]) ? $download_data_arr['star_lookup_data'][$i-1]['master_lookup_name'] : '';
        }
        //Building Reference Sheet End

        //Generating the excel file
        $file_name = 'Product_Color_Config_Template_' . date('Y-m-d');
        $result = Excel::create($file_name, function($excel) use($data) {
                    $excel->sheet('Sheet1', function($sheet) use($data) {
                        $sheet->fromArray($data['cp_data'], null, 'A1', false, false);
                    });
                    // Our second sheet
                    $excel->sheet('Sheet2', function($sheet) use($data) {
                        $sheet->fromArray($data['options'], null, 'A1', false, false);
                    });
                    // Set sheets
                })->export('xlsx');
        exit;
    }

    public function importProdColorConfigExcel(Request $request)
    {
        ini_set('max_execution_time', 1200);

        $message        = array();
        $uploadCount    = 0;
        $errorCount     = 0; 


        $file = Input::file('import_file')->getRealPath();
        $data = $this->readProdColorConfigExcelData($file);
        $data = json_decode(json_encode($data), 1);
        $prodData = $data['prod_data'];

        $pack_types_data        =  $this->getMasterLookUpData('16','Levels');
        $customer_types_data    = $this->_roleRepo->getMasterLookupData('Customer Types');
        $star_lookup_data       = DB::table('master_lookup')->select('master_lookup.master_lookup_name as color', 'master_lookup.value as Id')
            ->leftJoin('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id')
            ->where('master_lookup_categories.mas_cat_name', 'Product Star')->get()->all();

        //Building Reference Sheet - Start
        $userId = Session::get('userId');
        $wareHouseInfo = $this->_roleRepo->getAllDcs($userId);    


        $products_data = DB::table('products')
            ->where('products.is_sellable',1)
            ->select('products.product_id','products.sku')
            ->get()->all();    

        foreach($wareHouseInfo as $wareHouseObj)
        {
            $warehouseConf[$wareHouseObj->le_wh_id] = $wareHouseObj->display_name; 
        }    
        
        foreach($pack_types_data as $pack)
        {
            $packConf[strtolower($pack->name)] = $pack->value;
        }

        foreach($customer_types_data as $customer)
        {
            $custConf[strtolower($customer->master_lookup_name)] = $customer->value;
        }
        
        foreach($star_lookup_data as $color)
        {
            $colorConf[strtolower($color->color)] = $color->Id;
        }

        foreach($products_data as $prod)
        {
            $prodInfo[strtolower($prod->sku)] = $prod->product_id; 
        }

        $required_data = array('warehouse_id' => 'required','product_sku'=>'required', 'pack'=>'required', 'customer_type' => 'required', 'color' => 'required');

        foreach($prodData as $row_num =>$prod){

            $required_check_msg = array();
            $row_num = $row_num+3;
                                    
            foreach($required_data as $required_data_key=>$required) 
            {
                if($required=='required')
                {
                    if(!isset($prod[$required_data_key]) && $prod[$required_data_key] == '')
                    {

                        $required_check_msg[]=$required_data_key;
                    }                                   
                }
                
            }            

            if(count($required_check_msg)==0)
            {

                //Validating the input data by comparing with the existing keys
                $invalid_config_msg = array();
                
                if(!array_key_exists(strtolower($prod['warehouse_id']), $warehouseConf))
                {
                    array_push($invalid_config_msg, "Warehouse ID");
                }
                if(!array_key_exists(strtolower($prod['product_sku']), $prodInfo))
                {
                    array_push($invalid_config_msg, "Product SKU");
                }
                if(!array_key_exists(strtolower($prod['pack']), $packConf))
                {
                    array_push($invalid_config_msg, "Pack");
                }
                if(!array_key_exists(strtolower($prod['customer_type']), $custConf))
                {
                    array_push($invalid_config_msg, "Customer Type");
                }
                if(!array_key_exists(strtolower($prod['color']), $colorConf))
                {
                    array_push($invalid_config_msg, "Color");
                }

                if(count($invalid_config_msg) == 0)
                {
                    $warehouse_id = $prod['warehouse_id'];
                    $product_id   = $prodInfo[strtolower($prod['product_sku'])]; //need to get the product id w.r.to sku
                    $product_sku  = $prod['product_sku']; //display purpose in the warning messages
                    $pack_id      = $packConf[strtolower($prod['pack'])];
                    $customer_type_id = $custConf[strtolower($prod['customer_type'])];
                    $color_id     = $colorConf[strtolower($prod['color'])];

                    if($this->productModel->checkProductWarehouse($warehouse_id , $product_id))
                    {
                        //validate the product and its pack config available or not in the warehouse
                        if($this->productModel->checkProductPacks($product_id, $pack_id))
                        {
                            $exists = DB::table('product_pack_color_wh')->where(['le_wh_id' =>$warehouse_id ,'product_id' => $product_id,'pack_id'=>$pack_id,'customer_type'=>$customer_type_id])->pluck('color_wh_id')->all();

                            if(empty($exists))
                            {
                                DB::table('product_pack_color_wh')->insert(
                                    array('le_wh_id' =>$warehouse_id ,'product_id'=>$product_id,'pack_id'=>$pack_id,'customer_type'=>$customer_type_id,
                                        'color_code'=>$color_id)
                                );
                                
                                array_push($message, "Product SKU ".$product_sku." configured successfully");
                                $uploadCount++;
                            }
                            else
                            {
                               $errorCount++; 
                               $uploadCount++;
                               $rs  = DB::table('product_pack_color_wh')
                                    ->where(['le_wh_id' =>$warehouse_id ,'product_id' => $product_id,'pack_id'=>$pack_id,'customer_type'=>$customer_type_id])
                                    ->update(['color_code'=>$color_id]);

                                array_push($message, "Product SKU ".$product_sku." configured successfully"); //need to display as updated successfully
                               // array_push($message, "Product SKU ".$product_sku." already configured");
                            }
                        }
                        else
                        {
                            array_push($message , "Pack is not configured to the given SKU ".$product_sku." for row $row_num");//.$product_id);
                        }
                    }
                    else
                    {
                        array_push($message , "Product is not configured to the Warehouse for row $row_num");
                    }                    
                }
                else
                {
                    array_push($message , "Invalid fields ".implode(",", $invalid_config_msg)." for row $row_num");
                }
            }
            else
            {
                array_push($message , "All mandatory fields need to be filled for row $row_num");//.$product_id);
            }
        }
        // array_push($message, "Color configuration successful for ".$uploadCount." products");
        $messg = json_encode(array('status_messages' => $message));
        return $messg;
    }    

    public function readProdColorConfigExcelData($path) {
        try {
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get()->all();
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    //Product Color Configuration Functions - End

    //Customer Type ESU across India

    public function customerTypeEsu(){
         $data=Input::all();
         $page = isset($data['page'])?$data['page']:'';   //Page number
         $pageSize = isset($data['pageSize'])?$data['pageSize']:''; //Page size for ajax call
         if(is_numeric($page) && is_numeric($pageSize)){
         $skip = $page * $pageSize;
         }else{
           $skip =''; 
         }
         $makeFinalSql = array();
         $filter = isset($data['$filter'])?$data['$filter']:'';
         $this->objCommonGrid = new commonIgridController();
         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("master_lookup_name", $filter, false);
         $fieldQuery =str_replace('master_lookup_name', 'getMastLookupValue(ml.value)', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $this->objCommonGrid = new commonIgridController();
         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("esu", $filter, false);
         if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        if(isset($data['%24orderby'])){
            $orderBy = $data['%24orderby'];
            if($orderBy==''){
                $orderBy = $data['$orderby'];
            }else{
                $orderBy = '';
            }
        }else{
            $orderBy = '';
        }

        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }

        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {

           
            if( $countLoop==0 ){
                $sqlWhrCls .=  $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }
        if(!empty($sqlWhrCls)){
            $sqlWhrCls.=' and';
        }
        $sqlWhrCls .= ' ml.is_active=1';
         $getcustesubyproduct=DB::table('master_lookup as ml')
                          ->select('ml.value','ml.master_lookup_name','cem.esu','cem.product_id')
                          ->leftjoin('custype_esu_mapping as cem',function($query)use($data)
                          {
                           $query->on('ml.value','=','cem.customer_type');
                           $query->on('cem.product_id','=',DB::raw($data['product_id']));
            });
                         $getcustesubyproduct=$getcustesubyproduct->where('ml.mas_cat_id',3);
                         $getcustesubyproduct=$getcustesubyproduct->whereRaw($sqlWhrCls);
        $getcustesubyproductcount = $getcustesubyproduct->count(); 
        if(is_numeric($skip)){                 
        $getcustesubyproduct=$getcustesubyproduct->skip($skip)->take($pageSize)
                          ->get()->all();
          }else{
        $getcustesubyproduct=$getcustesubyproduct->get()->all();
          }                                 
                        
        $getcustesulist=json_decode(json_encode($getcustesubyproduct),true);
        for($cust=0;$cust<count($getcustesulist);$cust++){
         
            if(!empty($getcustesulist[$cust]['esu']) && $data['product_id']==$getcustesulist[$cust]['product_id']){  
               $getcustesulist[$cust]['esu']='<input type="text" id="esu_'.$getcustesulist[$cust]['value'].'" name="esu_'.$getcustesulist[$cust]['value'].'" value='.$getcustesulist[$cust]['esu'].' readonly="readonly"><i id="edit-esu_'.$getcustesulist[$cust]['value'].'" class="fa fa-pencil-square-o btn green-meadow" title = "Edit Esu" onclick="editEsu('.$getcustesulist[$cust]['value'].')"></i><i id="save-esu_'.$getcustesulist[$cust]['value'].'" class="fa fa-floppy-o btn green-meadow" title = "Save ESU" onclick="custEsuSave('.$getcustesulist[$cust]['value'].')"></i>';
            }else{
                $getcustesulist[$cust]['esu']='<input type="text" id="esu_'.$getcustesulist[$cust]['value'].'" name="esu_'.$getcustesulist[$cust]['value'].'" readonly="readonly"><i id="edit-esu_'.$getcustesulist[$cust]['value'].'" class="fa fa-pencil-square-o btn green-meadow" title = "Edit ESU" onclick="editEsu('.$getcustesulist[$cust]['value'].')"></i><i id="save-esu_'.$getcustesulist[$cust]['value'].'" class="fa fa-floppy-o btn green-meadow" title = "Save ESU" onclick="custEsuSave('.$getcustesulist[$cust]['value'].')"></i>';
            } 
        }
        echo json_encode(array('Records' => $getcustesulist, 'TotalRecordsCount' => $getcustesubyproductcount));
    }

    public function saveCustEsu(){
        try{
            $data=Input::all();
            $date=date('Y-m-d H:i:s');
            if($data['cust_id']!=0){
               $check_custproductid=DB::table('custype_esu_mapping')
                                         ->select('cem_id')
                                         ->where('product_id',$data['productid'])
                                         ->where('customer_type',$data['cust_id'])
                                         ->get()->all();

                if(count($check_custproductid)>0){
                   $insert_custproduct_esu=DB::table('custype_esu_mapping')
                                        ->where('customer_type',$data['cust_id'])
                                        ->where('product_id',$data['productid'])
                                        ->update(['esu'=>$data['esuval'],'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
                }else{
                    $insert_custproduct_esu=DB::table('custype_esu_mapping')
                                        ->insert(['product_id'=>$data['productid'],
                                                'customer_type'=>$data['cust_id'],
                                                'esu'=>$data['esuval'],'created_by'=>Session::get('userId'),'created_at'=>$date]);
                } 
                $getcust_name=DB::table('master_lookup')
                                       ->select('master_lookup_name')
                                       ->where('value',$data['cust_id'])
                                       ->where('is_active',1)
                                       ->get()->all();   
                $getcust_name=json_decode(json_encode($getcust_name),true);
               if($insert_custproduct_esu){
                $insert_custproduct_esu='ESU updated successfully for '.$getcust_name[0]['master_lookup_name'];
               }else{
                $insert_custproduct_esu='Failed to update ESU for '.$getcust_name[0]['master_lookup_name'];
               }

            }else{
                $user_id = Session::get('userId');
                $getallcust=DB::table('master_lookup')
                                      ->select('value')
                                      ->where('is_active',1)
                                      ->where('mas_cat_id',3)
                                      ->get()->all();
                $getallcust=json_decode(json_encode($getallcust),true);
                foreach($getallcust as $custtype){
                    $check_custproductid=DB::table('custype_esu_mapping')
                                         ->select('customer_type_id')
                                         ->where('product_id',$data['productid'])
                                         ->where('customer_type',$custtype['value'])
                                         ->get()->all();

                   if(count($check_custproductid)>0){
                        $insert_custproduct_esu=DB::table('custype_esu_mapping')
                                        ->where('customer_type',$custtype['value'])
                                        ->where('product_id',$data['productid'])
                                        ->update(['esu'=>$data['esu_val'],'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
                        if($insert_custproduct_esu)
                        {
                            $insert_custproduct_esu='ESU updated successfully';
                        }else{
                            $insert_custproduct_esu='Failed to update ESU';
                        }                            
                    }else{
                        $insert_custproduct_esu=DB::table('custype_esu_mapping')
                                        ->insert(['product_id'=>$data['productid'],
                                                'customer_type'=>$custtype['value'],
                                                'esu'=>$data['esu_val'],'created_by'=>Session::get('userId'),'created_at'=>$date]);
                        if($insert_custproduct_esu)
                        {
                            $insert_custproduct_esu='ESU updated successfully';
                        }else{
                            $insert_custproduct_esu='Failed to update ESU';
                        }                      
                    } 
                }                        

            }                         
            return $insert_custproduct_esu; 
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function downloadCustEsuExcel(){
        $headers_data = array(
            'Product ID','Product Title','Customer Type ID','ESU');
        $required_data = array('product_id' => 'required','product_title'=>'required','customer_type' => 'required', 'esu' => 'required'); 
        $data['cp_data'] = array(
            $required_data,
            $headers_data
        );

        if (Input::get('with_data') != '') {
            $products_query = DB::table('custype_esu_mapping as cem')->join('products','cem.product_id','=','products.product_id')->select('cem.product_id','cem.esu','products.product_title','cem.customer_type');
            $products = $products_query->get()->all();
            $product_data = json_decode(json_encode($products), True);
            foreach ($product_data as $product) {
                if($product['product_id']!=null && $product['product_id']!=0){
                   $product_id = $product['product_id'];    
                }else{
                    $product_id =0;
                }

                if($product['esu']!=null && $product['esu']!=0){
                   $esu = $product['esu'];    
                }else{
                    $esu ='';
                }
                
            
                $prod = array(
                    'product_id' => $product_id,
                    'product_title'=>$product['product_title'],
                    'customer_type'=>$product['customer_type'],
                    'esu' => $esu
                );

                $data['cp_data'][] = $prod;
            }
        }

        $headings = array('Product Id', 'Product Title','Customer Type ID' , 'Customer Type');
        $data['customer_type_esu'][]=$headings;

        $productslist=DB::table('products')->select('product_id','product_title')->get()->all();
        $productslist['product_data']=json_decode(json_encode($productslist),true);

        $customertype_list=DB:: table('master_lookup')->select('master_lookup_name','value')->where('mas_cat_id',3)->where('is_active',1)->get()->all();
        $customertype_list['customer_type']=json_decode(json_encode($customertype_list),1);
        $array_count = array(
            'Product Id' => count($productslist['product_data']),
            'Product Title' => count($productslist['product_data']),
            'Customer Type ID' =>count($customertype_list['customer_type']),
            'Customer Type' => count($customertype_list['customer_type'])
        );
        for ($i = 1; $i <= max($array_count); $i++) {
            $data['customer_type_esu'][$i][] = isset($productslist['product_data'][$i-1]) ? $productslist['product_data'][$i-1]['product_id'] : '';
            $data['customer_type_esu'][$i][] = isset($productslist['product_data'][$i-1]) ? $productslist['product_data'][$i-1]['product_title'] : '';
            $data['customer_type_esu'][$i][] = isset($customertype_list['customer_type'][$i-1]) ? $customertype_list['customer_type'][$i-1]['value'] : '';
            $data['customer_type_esu'][$i][] = isset($customertype_list['customer_type'][$i-1]) ? $customertype_list['customer_type'][$i-1]['master_lookup_name'] : '';
        }
        $file_name = 'Customer Type Esu Template_' . date('Y-m-d');
        $result = Excel::create($file_name, function($excel) use($data) {
                    $excel->sheet('Sheet1', function($sheet) use($data) {
                        $sheet->fromArray($data['cp_data'], null, 'A1', false, false);
                        $sheet->protectCells('A1', 'password');
                        $sheet->protectCells('B1', 'password');
                    });

                    $excel->sheet('Sheet2', function($sheet) use($data) {
                        $sheet->fromArray($data['customer_type_esu'], null, 'A1', false, false)->setWidth(array('A' => 20,'B' => 50,'C' =>20,'D'=>'30')) ;
                    });
                })->export('xlsx');
        exit;
    }

    public function uploadCustEsuExcel(){
        try{
            DB::beginTransaction();
            ini_set('max_execution_time', 1200);
            $message = '';
            $msg = '';
            $status = 'failed';
            $required_data = array('product_id' => 'required', 'product_title' => 'required','customer_type_id' => 'required','esu' => 'required'); 
            if (Input::hasFile('import_file')) {
                $path = Input::file('import_file')->getRealPath();
                $data = $this->readExcelCpEnable($path);
                $data = json_decode(json_encode($data), 1);
                if (isset($data['prod_data']) && count($data['prod_data']) > 0) {
                    $prod_data = $data['prod_data'];
                    $pr_scount = 0;
                    $pr_fcount = 0;
                    foreach ($prod_data as $product) {
                        $product_name = (isset($product['product_name'])) ? $product['product_name'] : '';
                        $required_check_msg = array();
                        foreach($required_data as $required_data_key=>$required) {
                            if($required=='required')
                            {
                                if(!isset($product[$required_data_key]) && $product[$required_data_key] == '')
                                {

                                    $required_check_msg[]=$required_data_key;
                                }                                   
                            }
                            
                        }
                        if (count($required_check_msg) == 0) {

                            $timestamp = md5(microtime(true));
                            $txtFileName = 'custesu-import-' . $timestamp . '.html';
                            $file_path = 'download' . DIRECTORY_SEPARATOR . 'cpstatus_logs' . DIRECTORY_SEPARATOR . $txtFileName;
                            $files_to_delete = File::files('download' . DIRECTORY_SEPARATOR . 'cpstatus_logs/');
                            File::delete($files_to_delete);

                            $product_id = $product['product_id'];
                            $esu = $product['esu'];
                            $cust_id =$product['customer_type_id'];

                            $customer_type_name=DB::table('master_lookup')->select('master_lookup_name')->where('value',$cust_id)->first();

                            //find whether product exits in products table 

                            $findproductexits=DB::table('products')->select('product_id')->where('product_id',$product_id)->first();
                            $this->userId = Session::get('userId');
                            if (count($findproductexits) != 0 && count($customer_type_name)!=0) {
                                $Tot_Array = array(
                                    'product_id' => $product_id,
                                    'esu'         =>$esu,
                                    'updated_by' => Session::get('userId')
                                );
                                $message .= 'ESU Updated  for '.$product['product_title'].' with Product ID ('.$product_id.') for Customer Type '.$customer_type_name->master_lookup_name;

                               $checkrecordincpenabletable="select cem_id from custype_esu_mapping where product_id=".$product_id." and customer_type=".$cust_id;

                               $checkrecordincpenabletable=DB::selectFromWriteConnection(DB::raw($checkrecordincpenabletable));

                               if(count($checkrecordincpenabletable)>0){
                                    $Tot_Array['updated_at']=date('Y-m-d H:i:s');
                                    $updaterecordincptable=DB::table('custype_esu_mapping')->where(array('product_id'=>$product_id, 'customer_type'=>$cust_id))->update($Tot_Array);
                                    if($updaterecordincptable){
                                        $pr_scount++;//success count
                                    }else{
                                        $pr_fcount++;//fail count
                                    }
                                
                                }else{
                                    $Tot_Array['customer_type']=$cust_id;
                                    $Tot_Array['created_by']=Session::get('userId');
                                    $Tot_Array['created_at']=date('Y-m-d H:i:s');
                                    $Tot_Array['updated_at']=date('Y-m-d H:i:s');
                                    $insertrecordincptable = DB::table('custype_esu_mapping')->insert($Tot_Array);
                                    if($insertrecordincptable){
                                        $pr_scount++;//success count
                                    }else{
                                        $pr_fcount++;//fail count
                                    }
                                }
                            } else {
                                $message .=$product['product_title'].' product not subscribed or Customer Type ID entered is Wrong';
                            }                                
                        } else {
                            $message .= 'All mandatory fields need to be filled for '.$product['product_title'];
                            $pr_fcount++;
                        }
                        $message .='<br/><br/>';
                    }
                    $msg .= $pr_scount . ' Customer Type Updated Successfully and ' . $pr_fcount . ' records failed to Update';
                    $status = 'success';
                }
                DB::commit();
            } else {
                DB::rollback();
                $msg = 'Please upload file';
            }
            $message .= PHP_EOL;
            $status = 400;
            $url = "";
            //create the log file as per the excel sheet
            if(isset($file_path)){
                $file = fopen($file_path, "w");
                fwrite($file, $message);
                fclose($file);
                $url = $file_path;
                $message = "Click <a href=".'/'.$file_path." target='_blank'> here </a> to view details.";
            }
            if(!empty($message)){
                Session::flash('test', $message);
            }  
            $messg = json_encode(array('status' => $status, 'message' => $msg, 'status_messages' => $message));
            return $messg;
        }catch (\ErrorException $ex) {
            DB::rollback();
            $messg = json_encode(array('status' => 400, 'message' =>'', 'status_messages' => "Sorry Failed to Upload Sheet,Reverting all Records. Please check log for More Details"));
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $messg;
        }
    }
}
