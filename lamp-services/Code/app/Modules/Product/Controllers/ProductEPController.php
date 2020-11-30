<?php

namespace App\Modules\Product\Controllers;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Http\Controllers\BaseController;
use Session;
use View;
use DB;
use \Log;
use URL;
use Image;
use Imagine;
use UserActivity;
use App\Modules\Manufacturers\Models\Legalentities;
use App\Modules\Manufacturers\Models\BrandModel;
use Redirect;
use App\Modules\Pricing\Models\pricingDashboardModel;
use App\Modules\Supplier\Models\LegalentitywarehousesModel;
use App\Modules\Product\Models\ProductAttributes;
use App\Modules\Roles\Models\Role;
use Illuminate\Http\Request;
use App\Modules\Product\Models\ProductRelations;
use App\Modules\Product\Models\ProductComments;
use App\Modules\Product\Models\ProductHistory;
use App\Central\Repositories\ProductRepo;
use App\Modules\Product\Models\ProductPolicies;
use App\Modules\Product\Models\ProductPackConfig;
use App\Modules\Product\Models\ProductTOT;
use App\Modules\Product\Models\ProductInfo;
use App\Modules\Product\Models\ProductEPModel;
use App\Modules\Product\Models\ProductModel;
use App\Modules\Product\Models\ProductMedia;
use App\Modules\Product\Models\AttributesModel;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Product\Models\CategoryEloquentModel;
use App\Modules\Product\Models\ProductCharacteristic;
use App\Modules\Product\Models\AttributesSetModel;
use App\Modules\Product\Controllers\commonIgridController;
use App\Central\Repositories\RoleRepo;
use App\Modules\Product\Controllers\ProductController;
use App\Modules\Tax\Models\MasterLookUp;
use App\Modules\Tax\Models\TaxClass;
use App\Modules\Supplier\Models\ZoneModel;

use Illuminate\Support\Facades\Input;
class ProductEPController extends BaseController
{
    private $brandList;
    public function __construct()
    {
      try
      {
        parent::__construct();
        $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
          parent::Title('Brands -Ebutor');
          $this->userId = Session::get('userId');
          $this->legal_entity_id = Session::get('legal_entity_id');
          $this->rolesObj= new Role();
          $this->productObj = new ProductRepo();
          $this->_roleRepo = new RoleRepo();
          $this->Product_Model_Obj = new ProductModel();
          $this->productRelationObj= new ProductRelations();
          $this->productInfoObj = new ProductInfo();
          $this->_masterlookup = new MasterLookUp();
          $this->_taxclass = new TaxClass();
          $this->objPricing = new pricingDashboardModel();
          $this->productTotObj= new ProductTOT();
          $this->ProductCharacteristicObj= new ProductCharacteristic();
          $this->Brand_Model_Obj = new BrandModel();
          $this->productMediaObj = new ProductMedia();
          $this->categoryObj= new CategoryEloquentModel();
          $this->Legalentities = new Legalentities();
          $this->ProductEPModelObj= new ProductEPModel();
          $this->ZoneModelObj= new ZoneModel();
          $this->ProductAttributesObj = new ProductAttributes();
          $this->productComments = new ProductComments();
          $this->AttributesSetObj = new AttributesSetModel();
          $this->attributeObj= new AttributesModel();
          $this->productPackConfigObj= new ProductPackConfig(); 
          $this->LegalentitywarehousesModel = new LegalentitywarehousesModel();  
          $this->approvalCommonFlow = new CommonApprovalFlowFunctionModel();
          $this->_inventory = new Inventory();
          $this->Related_Products = $this->_roleRepo->checkPermissionByFeatureCode('PTAB002');
          $this->Freebie_Configuration= $this->_roleRepo->checkPermissionByFeatureCode('PTAB003');
          $this->Packing_Configuration= $this->_roleRepo->checkPermissionByFeatureCode('PTAB004');
          $this->Tax_Information = $this->_roleRepo->checkPermissionByFeatureCode('PTAB005');
          $this->Suppliers= $this->_roleRepo->checkPermissionByFeatureCode('PTAB006');
          $this->Pricing_tab= $this->_roleRepo->checkPermissionByFeatureCode('PTAB007');
          $this->Inventory= $this->_roleRepo->checkPermissionByFeatureCode('PTAB008');
          $this->Approval_History = $this->_roleRepo->checkPermissionByFeatureCode('PTAB009');
          $this->warehouse_info= $this->_roleRepo->checkPermissionByFeatureCode('PWH0001');
          $this->manufacturer_kvi = $this->ProductEPModelObj->getMasterLookUpData('69','KVI');
          $this->product_star = $this->ProductEPModelObj->getMasterLookUpData('140','Product Star');
          $this->product_form_data= $this->ProductEPModelObj->getMasterLookUpData('72','Product Form');
          $this->license_typ_data= $this->ProductEPModelObj->getMasterLookUpData('73','License Type');
          $this->bin_category_type= $this->ProductEPModelObj->getMasterLookUpData('141','Bin Categories');
          $this->shelf_uom_data= $this->ProductEPModelObj->getMasterLookUpData('71','Shelf Life UOM');
          $this->offer_pack_data=$this->ProductEPModelObj->getMasterLookUpData('102','Offer Pack');
          $this->packageLevel = $this->ProductEPModelObj->getMasterLookUpPackageData('16','Levels');
          $this->packageWeightUOM= $this->ProductEPModelObj->getMasterLookUpWeightUom('86','Weight UoM');
          $this->packageVolumeUOM= $this->ProductEPModelObj->getMasterLookUpPackageData('15','Volume UOM');
          $this->packageLBHUOM= $this->ProductEPModelObj->getMasterLookUpPackageData('12','Length UOM');
          $this->grouped_Products = $this->_roleRepo->checkPermissionByFeatureCode('GRPPRDTAB001');
          $this->brandList='<option value="0">Please Select Brand ...</option>';
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
          return $next($request);
        });
      }
      catch (\ErrorException $ex)
      {
          Log::error($ex->getMessage());
          Log::error($ex->getTraceAsString());
      }
    }
    public function getBrandChildList($brand_id,$level,$manf_id,$list)
    {
      if(empty($manf_id) && $manf_id==0)
      {
         $brandsList= $this->Brand_Model_Obj->where('parent_brand_id',$brand_id)->whereIN('brand_id',array_keys($list))->get()->all();
      }else
      {    
         $brandsList= $this->Brand_Model_Obj->where('mfg_id',$manf_id)->whereIN('brand_id',array_keys($list))->get()->all();

      }
      $css_class='sub_cat';
      for($i=0;$i<count($brandsList);$i++){
        $this->brandList.= '<option value="'.$brandsList[$i]['brand_id'].'" style="font-size:13px; color:#000000">'.$brandsList[$i]['brand_name'].'</option>';
      }
      /*if (!empty($brandsList)) 
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
      } */         
    }
    public function getBrandsList($manf_id)
    { 
      $user_id=Session::get('userId');
      $userData = DB::select(DB::raw('select * from users u join legal_entities l on u.`legal_entity_id`= l.legal_entity_id where l.`legal_entity_type_id` IN (1006,1002,89002) AND  u.is_active=1 and u.user_id='.$user_id ));
      if(!empty($userData)){
        $brandList=DB::table('user_permssion')
                    ->where(['permission_level_id' => 7, 'user_id' => $user_id])
                    ->pluck('object_id')->all();
        if(!empty($brandList)){
          $getbrand=DB::table('brands')
                  ->whereIn('brand_id',$brandList)
                  ->groupBy('brands.brand_id')
                  ->pluck('brands.brand_name', 'brands.brand_id')->all();
          if(!empty($getbrand)){
            $DataFilter['brand']=$getbrand;
          }
        }
      }else{
            $DataFilter= $this->rolesObj->getFilterData(7, $this->userId);
            $DataFilter=json_decode($DataFilter,true);
      }


        $list = isset($DataFilter['brand']) ? $DataFilter['brand'] : [];
       // print_r($list);exit;
        if($manf_id!=01)
        {
          //print_r($DataFilter);exit;
          $this->getBrandChildList(0,1,$manf_id,$list);
          return $this->brandList; 
        }
        else
        {
          return '<option value="0">Please Select Brand...</option>';
        }
    }
    //get brand list data
    public function getManufacturersList()
    {  
      //Log::info('We are in '.__METHOD__);
      $manufacturerList='<option value="0">Please Select Manufacturer ...</option>';
      $user_id=Session::get('userId');
      $userData = DB::select(DB::raw('select * from users u join legal_entities l on u.`legal_entity_id`= l.legal_entity_id where l.`legal_entity_type_id` IN (1006,1002,89002) AND  u.is_active=1 and u.user_id='.$user_id ));
      if(!empty($userData)){
        $manufacturer=DB::table('user_permssion')
                    ->where(['permission_level_id' => 11, 'user_id' => $user_id])
                    ->pluck('object_id')->all();
        $Data = DB::table('legal_entities')
                            ->where(['legal_entity_type_id' => 1006])
                            ->whereIn('legal_entity_id',$manufacturer)
                            ->groupBy('legal_entity_id')
                            ->pluck('business_legal_name', 'legal_entity_id')->all();

        if(!empty($Data)){
          $DataFilter['manufacturer']=$Data;
        }
        $DataFilter=json_encode($DataFilter);

      }
      else{
        $DataFilter= $this->rolesObj->getFilterData(11, $this->userId);

      }
      //Log::info('DataFilter');
      //Log::info($DataFilter);
      $DataFilter=json_decode($DataFilter,true);
      $list = isset($DataFilter['manufacturer']) ? $DataFilter['manufacturer'] : [];
      foreach ($list as $listValue => $value)
      {
        $manufacturerList.= '<option value="'.$listValue.'" >'.$value.'</option>';
      }
      return $manufacturerList;
    }  
    public function saveProductImgages(Request $request,$product_id)
    {
      $input = Input::all();
	try{
      $rules = array(
          'file' => 'image|max:3000',
      );
      $logoPath    = '/uploads/products';
      $imageCount= $this->productMediaObj->select(DB::raw('count(prod_media_id) as image_cnt'))->where('product_id',$product_id)->first();
      $imageCount= json_decode(json_encode($imageCount),1);
      if(!empty($imageCount['image_cnt']) && $imageCount['image_cnt']>= '7')
      {
        return "false";
      }

      $product_thumbnail_path = 'uploads/product_thumbnail/';
      $dbPath=$logoPath.'/'.$product_id.'/product';
      $file = Input::file('file');
      $s3ImageUrl = $this->productObj->uploadToS3($file,'products',1);
      $ext                = $file->guessClientExtension();  
      $fullname           = $file->getClientOriginalName(); 
      $hashname           = time().'.'.$ext;     
      $upload_success     = Input::file('file')->move($product_thumbnail_path, $hashname);      
      $thumbnail_name = 'product_'.$product_id.'_thumbnail_img'.$hashname;
	  
       $thumbnail = Image::open($product_thumbnail_path . $hashname)
                 ->thumbnail(new Imagine\Image\Box(204, 250));
      $value = $thumbnail->save($product_thumbnail_path . $thumbnail_name);
//Log::info($value);
	  //saving thunbmail image
	   $thumb = new ProductRepo();
       $s3ImageUrl2 = $thumb->uploadToS3($product_thumbnail_path.$thumbnail_name, 'thumbnails', 2);
       ProductInfo::where('product_id', $product_id)->update(['thumbnail_image' => $s3ImageUrl2]);
		
      //checking product have image or not
      $checkPrimaryImage=$this->productInfoObj->getPrimaryImage($product_id);
      if(!empty( $checkPrimaryImage->primary_image))
      {
        $this->productMediaObj->media_type   = '85003';
        $this->productMediaObj->product_id = $product_id;
        $this->productMediaObj->url    = $s3ImageUrl;
		$this->productMediaObj->thumbnail_url = $s3ImageUrl2;
        $this->productMediaObj->created_by    = $this->userId;
        $this->productMediaObj->save();
      }else
      {
        ProductInfo::where('product_id',$product_id)->update(['primary_image'=> $product_id.'/product/'.$hashname,'thumbnail_image' => $s3ImageUrl2]);
      }
      return "success";
	}
	catch(Exception $ex)
	{
		Log::error($ex->getMessage());
	}
    }
    //show product create page 
    public function productCreation()
    {
        $access = $this->_roleRepo->checkPermissionByFeatureCode('PRC0001');
            if (!$access) {
                Redirect::to('/')->send();
                die();
            }
      parent::Title("Create Product -Ebutor"); 
      $breadCrumbs = array('Home'=>url('/'),'products'=>url('/products/creation'),'Add'=>'#');
      parent::Breadcrumbs($breadCrumbs);  
      $query  = BrandModel::all();
      $brands_Data=json_decode(json_encode($query));
      $offer_pack=  $this->ProductEPModelObj->getMasterLookUpData('102','Offer Pack'); 
      $supplier_list = $this->ProductEPModelObj->getActiveSuppliers();
	$product_stars = $this->ProductEPModelObj->getMasterLookUpData('140','Product Star');
  $kvidata = $this->ProductEPModelObj->getMasterLookUpData('69','KVI');
	return View::make('Product::createproduct',['brands'=>$brands_Data,'offer_pack'=>$offer_pack,'supplier_list'=>$supplier_list,'product_stars'=>$product_stars,'kvi'=>$kvidata]);
    }
    //save product ofter creation   
    public function saveProduct(Request $request) {
      DB::beginTransaction();
      try{
        $is_sellable = 0;
        $brand_id = $request->input('brand');
        $category_id = $request->input('category');
        $product_title = $request->input('product_title');
        $manufacturer_id = $request->input('manufacturer_name');
        $product_esu = $request->input('product_esu');
        $product_star = $request->input('product_star');
        $product_mrp = $request->input('product_mrp');
        $product_offer_pack = $request->input('product_offer_pack');
        $product_each_qty = $request->input('product_each_qty');
        $product_cfc_qty = $request->input('product_cfc_qty');
        $manufacturer_id = $request->input('manufacturer_name');
        $pack_size = $request->input('product_pack_size');
        $pack_size_uom = $request->input('product_pack_size_uom');
        $supplier = $request->input('product_suppliers');
        $kvi = $request->input('kvi');
//      if(!empty($request->input('product_is_sellable')))
//      {
//        $is_sellable=1;
//      }   
        //get attribute set id by category

        $res_approval_flow_func = $this->approvalCommonFlow->getApprovalFlowDetails('Product PIM', 'drafted', $this->userId);

        if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
            $att_Set_id = $this->AttributesSetObj->getAttributeSetId($category_id);
            $this->Product_Model_Obj->product_title = $product_title;
            $this->Product_Model_Obj->category_id = $category_id;
            $this->Product_Model_Obj->brand_id = $brand_id;
            $this->Product_Model_Obj->legal_entity_id = $this->legal_entity_id;
            $this->Product_Model_Obj->sku = $this->productObj->generateSKUcode();
            $this->Product_Model_Obj->manufacturer_id = $manufacturer_id;
            //$this->Product_Model_Obj->is_sellable = $is_sellable;
            $this->Product_Model_Obj->mrp = $product_mrp;
            $this->Product_Model_Obj->esu = $product_esu;
            $this->Product_Model_Obj->created_by = $this->userId;
            $this->Product_Model_Obj->star = $product_star;
            $this->Product_Model_Obj->product_type_id = 130002;
            $this->Product_Model_Obj->primary_image = 'https://s3.ap-south-1.amazonaws.com/ebutormedia/products/168+products/harisharan/no-image_zpseqbcsx2n.jpg';
            $this->Product_Model_Obj->kvi = $kvi;
            $this->Product_Model_Obj->save();
            $this->ProductCharacteristicObj->product_id = $this->Product_Model_Obj->product_id;
            $this->ProductCharacteristicObj->save();

            if ($this->Product_Model_Obj->product_id) {
                $current_status_id = $res_approval_flow_func["currentStatusId"];
                $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];

                /*$update_result = $this->Product_Model_Obj->where('product_id', $this->Product_Model_Obj->product_id)->first();
                $update_result->status = $next_status_id;
                $update_result->approved_by = $this->userId;
                $update_result->approved_at = date('Y-m-d H:i:s');
                $update_result->updated_by = $this->userId;
                $update_result->updated_at = date('Y-m-d H:i:s');
                $update_result->save();*/
                 DB::table('products')->where('product_id',$this->Product_Model_Obj->product_id)->update(['status'=>$next_status_id,'approved_by'=>$this->userId,'approved_at'=>date('Y-m-d H:i:s'),'updated_by'=>$this->userId,'updated_at'=>date('Y-m-d H:i:s')]);

                $workflowhistory = $this->approvalCommonFlow->storeWorkFlowHistory('Product PIM', $this->Product_Model_Obj->product_id, $current_status_id, $next_status_id, 'System approval at the time of insertion', $this->userId);

                //product subscribe to supplier
                $DcId = env('APOB_DCID');//Session::get('warehouseId');
                $productEPModel = new ProductEPModel();
                $data = $productEPModel->quickProductSupp($supplier, $DcId, $this->Product_Model_Obj->product_id);
                $productEPModel->sendNotificationAlert($this->Product_Model_Obj->sku, $this->Product_Model_Obj->product_id, $this->Product_Model_Obj->product_title, $product_mrp, $this->Product_Model_Obj->primary_image);

                ProductPackConfig::insert(['product_id' => $this->Product_Model_Obj->product_id, 'no_of_eaches' => $product_each_qty, 'level' => 16001, 'created_by' => $this->userId, 'effective_date' => date('Y-m-d')]);
                ProductPackConfig::insert(['product_id' => $this->Product_Model_Obj->product_id, 'no_of_eaches' => $product_cfc_qty, 'level' => 16004, 'created_by' => $this->userId, 'effective_date' => date('Y-m-d')]);
                $getOfferPack_id = $this->productInfoObj->getOfferPackAttId();
                $getPackSize = $this->getAllAttributes('pack_size');
                $getPackSize_uom = $this->getAllAttributes('pack_size_uom');
                $prod_attr = array('product_id' => $this->Product_Model_Obj->product_id,
                    'attribute_id' => $getOfferPack_id[0],
                    'value' => $product_offer_pack,
                    'attribute_set_id' => $att_Set_id['attribute_set_id']);
                $prod_attr1 = array(array('product_id' => $this->Product_Model_Obj->product_id,
                        'attribute_id' => $getPackSize[0],
                        'value' => $pack_size,
                        'attribute_set_id' => $att_Set_id['attribute_set_id']),
                    array('product_id' => $this->Product_Model_Obj->product_id,
                        'attribute_id' => $getPackSize_uom[0],
                        'value' => $pack_size_uom,
                        'attribute_set_id' => $att_Set_id['attribute_set_id']));
                $attr = ProductAttributes::insert($prod_attr);
                ProductAttributes::insert($prod_attr1);
                $rr = $this->Product_Model_Obj->product_id;
                $this->getProductGroupId($this->Product_Model_Obj->product_id);
                DB::commit();
                return $rr;
            }
        }
        return "false";
      }catch(\ErrorException $ex) {
        DB::rollback();
        Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        return "true";
      }
    }

    //set edit page attributes
    function getAttGroupByCategory($category_id,$product_id)
    {
       return  $this->productInfoObj->getAttributeWithAttGroup($category_id,$product_id);
    }
    public function productComments(Request $request)
    {
      $product_id =  (int )$request->product_id;
      $rs = $this->productComments->createProductComments($request->comments,$product_id);
      return json_encode($rs);
    }
    public function getProductComments(Request $request)
    {
      $product_id =  (int )$request->product_id;      
      $rs = $this->productComments->getProductComments($product_id);
      return json_encode($rs);
    }
    public function editPackageLevel(Request $request,$id)
    {
      $packageLevel = $request->get('packageLevel');
      $effective_date = $request->get('effective_date');
      
      $effective_date = date('Y-m-d', strtotime($effective_date));
      
      $levelCheck = $this->productPackConfigObj->where(['product_id'=>$id])->where('level',$packageLevel)->select('level')->first();
      $packData = $this->productPackConfigObj->where(['product_id'=>$id,'level'=>$packageLevel])->where('effective_date','=',$effective_date)->select('level')->first();
      $pack_id = (isset($request->pack_id))?$request->pack_id:'';
      if($pack_id == "")
      {
        $checkSameEchaes = $this->productPackConfigObj
                        ->where('product_id','=',$id)
                        ->where('is_sellable',1)
                        ->select('no_of_eaches','esu')
                        ->get()->all();
      }else
      {
         $checkSameEchaes = $this->productPackConfigObj
                        ->where('product_id','=',$id)
                        ->where('is_sellable',1)
                        ->where('pack_id','!=',$pack_id)
                        ->select('no_of_eaches','esu')
                        ->get()->all();
      }
      $checkSameEchaes = json_decode(json_encode($checkSameEchaes), true);
      $eaches_status = 0;
      if(!empty($checkSameEchaes))
      {
        $dataArray = array();
        $packEaches_esu = array();
        foreach ($checkSameEchaes as $value)
        {
          $dataArray[] = $value['no_of_eaches'];
          $packEaches_esu[] = $value['no_of_eaches']*$value['esu'];
        }
        if(!empty($dataArray) && in_array($request->packEaches, $dataArray) && $request->is_sellable == "on")
        {
          return '4';
        }
        if(!empty($packEaches_esu) && $request->is_sellable == "on")
        {
          //pack eaches*esu by lower and upper pack
          $packEaches= $request->packEaches;
          $esu= $request->packEsu;
          $pack_number = $packEaches*$esu;
          $pack_config_validation = '';
          $nearLowestPackValue = $this->packNearLowestValue($packEaches_esu,$pack_number);
          $nearHightPackValue = $this->packNearHighestValue($packEaches_esu,$pack_number);
          $existedEditPackEaches = $request->existedEditPackEaches;
          if(!empty($nearLowestPackValue))
          {
            $pack_config_validation = $pack_number/$nearLowestPackValue;
          }
          if(!empty($nearHightPackValue))
          {
            $pack_config_validation = $nearHightPackValue/$pack_number;
          }
          if(!empty($nearHightPackValue) && !empty($nearLowestPackValue) && ($existedEditPackEaches != $packEaches))
          {
            $pack_config_validation = $nearHightPackValue/$pack_number;
            $packLow = $pack_number/$nearLowestPackValue;
            if(!is_float($packLow) || !is_float($pack_config_validation))
              return 5;
          }
          if(is_float($pack_config_validation) && $pack_config_validation!="")
          {
            return 5;
          }
        }
      }     
      $packId = $request->get('edit_pack_id');
      if($packId)
      {
        $levelCheckByDate = $this->productPackConfigObj->where(['product_id'=>$id,'effective_date'=>$effective_date])->where('level',$packageLevel)->select('level','effective_date')->first();
        
        $actualData = $this->productPackConfigObj->where(['pack_id'=>$packId])->select('level','effective_date')->first();
        
        $levelCheckByDateValue = (isset($levelCheckByDate))? $levelCheckByDate->effective_date : null;
        $levelCheckByDateLevel = (isset($levelCheckByDate))? $levelCheckByDate->level : null;
        
        //save product for no changes
        if($actualData->level == $levelCheckByDateLevel && $levelCheckByDateValue == $actualData->effective_date)
        {
          return '1';
        }
        else
        {
          if(isset($levelCheck) && $levelCheck->level == 16001)
          return '2';       
          
          if(isset($levelCheckByDate))
          return '3';       
        }
        
      }
      
    
      if(isset($levelCheck) && $levelCheck->level == 16001)
        return '2';
          
          if(isset($packData)&& $packData->level)   
          {
          return '0';
      }     
        else
       return '1';  
    } 
    public function editPackageConfiguration($pack_id)
    {
      $packData = $this->productPackConfigObj->where('pack_id',$pack_id)->first();
      if($packData->effective_date == '0000-00-00')
      {
        $packData->effective_date =null;  
      }
      else
      {
        $packData->effective_date = date('Y-m-d', strtotime($packData->effective_date));
      } 
        $packDataObj= json_encode($packData);
        return $packDataObj;
    }
    public function packageConfiguration(Request $request)
    {
      DB::beginTransaction();
      try{
      UserActivity::userActivityLog("Products", array($request->all()), "Products pack configuration has been added/ updated in product page", "", array("Product_id" => $request->pack_product_id));
      //get request fields.....
      $palletization=0;
      $product_id= $request->pack_product_id;
      $packageLevel= $request->packageLevel;
      $packSkuCode= $request->packSkuCode;
      $packEaches= $request->packEaches;
      $packInner= $request->packInner;
      $lbh_uom= $request->lbh_uom;
      $pack_lenght= $request->pack_lenght;
      $pack_breadth= $request->pack_breadth;
      $pack_height= $request->pack_height;
      $packWeightUOM= $request->packWeightUOM;
      $weight= $request->weight;
      $esu= $request->packEsu;
      $pack_star= $request->product_pack_star;
      if(!empty($request->is_sellable))
      {
        $is_sellable=1;
      }else
      {
        $is_sellable=0;
      }
      if(!empty($request->is_cratable))
      {
        $is_cratable=1;
      }else
      {
        $is_cratable=0;
      }
      $packVolumeWeightUOM= $request->packVolumeUOM;
      $valumetricWeight= ($pack_lenght*$pack_breadth*$pack_height)/5000;
      $stackHeight= $request->stackHeight;
      $packingMeterial= $request->packingMeterial;
      if(isset($request->palletization))
      {
        $palletization=1;
      }
      $palleteCapacity= $request->palleteCapacity;
      $status= $request->pack_status;
      $pack_id= $request->pack_id;
      $packDate = date('Y-m-d', strtotime($request->get('effective_date')));
      $effectiveDate = $packDate;
      if($status=='edit')
      {
       $this->productPackConfigObj= $this->productPackConfigObj->where('pack_id', $pack_id)->update(['level' => $packageLevel,'no_of_eaches' => $packEaches,'pack_sku_code' => $packSkuCode,'inner_pack_count' => $packInner,'pallet_capacity' => $palleteCapacity,'length' => $pack_lenght,'breadth' => $pack_breadth,'esu'=>$esu,'star'=>$pack_star,'height' => $pack_height,'weight_uom' => $packWeightUOM,'weight' => $weight,'is_sellable'=>$is_sellable,'vol_weight_uom' => $packVolumeWeightUOM,'vol_weight' => $valumetricWeight,'stack_height' => $stackHeight,'pack_material' => $packingMeterial,'palletization' => $palletization,'effective_date'=>$effectiveDate,'is_cratable'=>$is_cratable]);
      }else
      {
        //adding to db
        $this->productPackConfigObj->product_id= $product_id;
        $this->productPackConfigObj->level= $packageLevel;
        $this->productPackConfigObj->no_of_eaches= $packEaches;
        $this->productPackConfigObj->pack_sku_code= $packSkuCode;
        $this->productPackConfigObj->inner_pack_count= $packInner;
        $this->productPackConfigObj->esu= $esu;
        $this->productPackConfigObj->star = $pack_star;
        $this->productPackConfigObj->lbh_uom= $lbh_uom;
        $this->productPackConfigObj->length= $pack_lenght;
        $this->productPackConfigObj->breadth= $pack_breadth;
        $this->productPackConfigObj->height= $pack_height;
        $this->productPackConfigObj->weight_uom= $packWeightUOM;
        $this->productPackConfigObj->weight= $weight;
        $this->productPackConfigObj->is_sellable= $is_sellable;
        $this->productPackConfigObj->is_cratable= $is_cratable;        
        $this->productPackConfigObj->vol_weight_uom= $packVolumeWeightUOM;
        $this->productPackConfigObj->vol_weight= $valumetricWeight;
        $this->productPackConfigObj->stack_height= $stackHeight;      
        $this->productPackConfigObj->pack_material= $packingMeterial;
        $this->productPackConfigObj->palletization= $palletization;
        $this->productPackConfigObj->pallet_capacity= $palleteCapacity;
        $this->productPackConfigObj->created_by= $this->userId;
        
    $this->productPackConfigObj->effective_date= $effectiveDate;

        $this->productPackConfigObj->save();
      }
      $packStar = $this->productStarUpdate($product_id);
      DB::commit();
      return $this->productPackConfigObj;
      }catch(\ErrorException $ex) {
        DB::rollback();
        Log::error($ex->getMessage().' '.$ex->getTraceAsString());
         return 6;
      }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //delete product image
    public function deleteProductImage(Request $request,$product_id)
    {
      $rs= ProductMedia::where('product_id',$product_id)->where('prod_media_id',$request->dropProductId)->delete();
      return $rs;
    }
    //set as defaullt image in products media
    public function setAsDefaultImage(Request $request,$product_id)
    {
        $getPrimaryImage = $this->productInfoObj->getPrimaryImage($product_id);        
        $thumbailImageObj = $this->productInfoObj->getThumbnailImage($product_id);
        $thumbailImage = (isset($thumbailImageObj[0]))?$thumbailImageObj[0]:null; 
        
        // $getPrimaryImage->primary_image;
        $newThumbnail = ProductMedia::where('product_id', $product_id)->where('prod_media_id', $request->asDefautlImageId)->pluck('thumbnail_url')->all();
        $newThumbnailImage = (isset($newThumbnail[0]))?$newThumbnail[0]:null; 
        
        $rs = ProductInfo::where('product_id', $product_id)->update(['primary_image' => $request->url_path,'thumbnail_image' => $newThumbnailImage]);
        if ($rs == 1)
        {
            $rs = ProductMedia::where('product_id', $product_id)->where('prod_media_id', $request->asDefautlImageId)->update(['url' => $getPrimaryImage->primary_image,'thumbnail_url'=>$thumbailImage]);
        }
        return $rs;
    }
    //get product images form product media 
    public function getProductGalleryImage($product_id)
    {
      $getPrimaryImage= $this->productMediaObj->getProductGelleryImage($product_id);
      return $getPrimaryImage;
    }
     //get product primary image in product edit
    public function getProductPrimaryImage($product_id)
    {
      $getPrimaryImage= $this->productInfoObj->getPrimaryImage($product_id);
      return $getPrimaryImage;
    }
    //save product image url
    public function  saveProductUrlImage(Request $request,$product_id)
    {
         $checkPrimaryImage=$this->productInfoObj->getPrimaryImage($product_id);
       //$rs= DB::getquerylog();
      $imageCount= $this->productMediaObj->select(DB::raw('count(prod_media_id) as image_cnt'))->where('product_id',$product_id)->first();
      $imageCount= json_decode(json_encode($imageCount),1);
      if(!empty($imageCount['image_cnt']) && $imageCount['image_cnt']>= '7')
      {
        return "false";
      }else{
          if(!empty( $checkPrimaryImage->primary_image))
        {
          $this->productMediaObj->product_id= $product_id;
          $this->productMediaObj->media_type= "85003";
          $this->productMediaObj->url=$request->image_preview;
          $this->productMediaObj->save();
        }
        else
        {
          $this->productMediaObj= ProductInfo::where('product_id',$product_id)->update(['primary_image'=>$request->image_preview]);
        }
        return "success";
      }
      
       
        
    }
    public function getWarehouseBylegalentity()
    {
        $legalentity_warehouses = $this->LegalentitywarehousesModel->where('legal_entity_id', $this->legal_entity_id)->get()->all();
        return $legalentity_warehouses;
    }
     //product edit screen 
    public function editProduct($product_id)
    {
	  $esuPermission=$this->_roleRepo->checkPermissionByFeatureCode('PRD0010');
	  $InventoryReasonCodes = $this->_inventory->getInventoryReasonCodes();  		
      $checkEPPermissions=$this->_roleRepo->checkPermissionByFeatureCode('PRD002');
	  $cpPermissions=$this->_roleRepo->checkPermissionByFeatureCode('PRD011');
      $sellablePermissions=$this->_roleRepo->checkPermissionByFeatureCode('PRD012');
   
    $duplicate_product_permissions = $this->_roleRepo->checkPermissionByFeatureCode('CDP001');
    $supplier_login_permissions = $this->_roleRepo->checkPermissionByFeatureCode('PSL001');
  
      if ($checkEPPermissions==0)
      {
          return Redirect::to('/');
      }
	  $tab = 'all';
	  if (isset($_SERVER["HTTP_REFERER"]))
        {
            $referer = $_SERVER["HTTP_REFERER"];
            $urlArray = explode('/', $referer);
            $tab = (isset($urlArray[4])) ? $urlArray[4] : 0;
        }
		
	    if(is_numeric($tab))
        {
            $tab = 'all';
        }	
	   $breadCrumbs = array('Home' => url('/'), 'Products' => url('/products').'/'.$tab, 'Edit Product' => '#');
        parent::Breadcrumbs($breadCrumbs);               
        $brandList =BrandModel::all();    
        $productHistoryObj = new ProductHistory();   
        $productData= $this->productInfoObj->getProductData($product_id);
        $Data= json_decode(json_encode($productData)); 
        $brands= BrandModel::find($Data->brand_id);      
        $parent_id=$Data->get_category_model->parent_id;  
        $getCategory_id= $Data->category_id; 
        parent::Title($Data->product_title);
        $category_name= $this->productInfoObj->categoryLoopLink($Data->category_id);
        $category_name= rtrim($category_name,' >> ');
        $checkPolicyData= ProductPolicies::where('product_id', $product_id)->get()->all();
        $polocy_type = array('warranty_policy', 'return_policy');
        $warranty_policy='';
        $return_policy='';
        $checkPolicyData= json_decode(json_encode($checkPolicyData),true);
        if(!empty($checkPolicyData))
        {
          for ($p = 0; $p < sizeof($checkPolicyData); $p++)
          {
            if($checkPolicyData[$p]['policy_type_name']=='warranty_policy')
            {
              $warranty_policy=$checkPolicyData[$p]['policy_details'];
            }
            if($checkPolicyData[$p]['policy_type_name']=='return_policy')
            {
              $return_policy=$checkPolicyData[$p]['policy_details'];
            }           
          }
        } 

        //check attribute set have or not
        $att_set = $this->productInfoObj->checkProductAttributeSet($product_id);
        $checkPolicyData= json_decode(json_encode($checkPolicyData),true);
        //get legal entity wise ware house for freebie product
        $getZoneData= $this->ZoneModelObj->select('zone_id','name')->where('country_id','99')->get()->all();
        $getZoneData= json_decode(json_encode($getZoneData),true);
        $brandData=json_decode(json_encode($brands));
        $manufacturer_name=$productData->manufacturer_id;; 
        //get attribute set id for product wise
        $getAttSetIdData=$this->ProductAttributesObj->where('product_id',$product_id)->first();
        $getAttSetId= json_decode(json_encode($getAttSetIdData),true);
        //get product attributes data with group
        $attributeIdValueData= $this->productInfoObj->getAttributeIdValue($product_id,$getAttSetId['attribute_set_id']);
        //get attribute group data
        $getAttributeGroup= $this->productInfoObj->getAttGroupData($product_id,$getAttSetId['attribute_set_id']);
        $productAttributGroupInfo = $this->productInfoObj->getEditProductGroupAtt($product_id,$Data->category_id);
        $getProductCharacterstics =$this->ProductCharacteristicObj->where('product_id',$product_id)->first();
        $getProductCharacterstics=json_decode(json_encode($getProductCharacterstics),true); 
         $where = ['product_id'=>$product_id];
        $productImages = $this->productMediaObj->where($where)->skip(0)->take(7)->get()->all();
        $productAttributInfo = $this->productInfoObj->getAttributeData($product_id);
        $history = $this->ProductEPModelObj->productWorkFlowModel($product_id);
        //Section for Tax Tab settings
        $alltaxes = $this->_masterlookup->getTaxTypes();
        sort($alltaxes);
        //Adding 'Action' item in array for Modal Table.
        $taxArr = array();
        $taxArr['master_lookup_name'] = 'Action';
        $taxArr['master_lookup_id'] = '0';
        $alltaxes[] = $taxArr;
        $state_wise_tax_classes = $this->stateWiseTaxClasses();
         // load this data for price modal
        $getCustomerGroup = $this->objPricing->getCustomerGroup();
        $getStateDetails = $this->objPricing->getStateDetails();            
        //$pricing = $this->ProductEPModelObj->productSlabModel($product_id,$this->userId);
        $pricing = $this->ProductEPModelObj->getPricingForProduct($product_id);
        $tax = $this->ProductEPModelObj->productTaxModel($product_id);
        $legalentity_warehouses = $this->getWarehouseBylegalentity();
        $legalentity_warehouses = json_decode(json_encode($legalentity_warehouses), true);
        $product_group_data=$this->ProductEPModelObj->getProductGroupInfo($product_id);
       // $product_history_data= json_encode($product_history_data);

    /*    foreach ($product_history_data as $historyValue =>$hisData) 
        {

          if(!empty($hisData['newvalues']))
          {
            echo $hisData['newvalues'][0]['product_data'];
          }
          echo $historyValue['pic'].'________________';
           //$historyValue['newvalues']['product_data'];
         
          print_r($hisData['updated_at']);
           echo "--------------------------";
        }
        die();*/
        $pro_grp_edit = $this->_roleRepo->checkPermissionByFeatureCode('PGE0001');
        $pro_grp_add = $this->_roleRepo->checkPermissionByFeatureCode('PGA0001');
        $pro_history_tab = $this->_roleRepo->checkPermissionByFeatureCode('PHR0001');
        $cp_enable_tab = $this->_roleRepo->checkPermissionByFeatureCode('CPTAB001');
        $warehouse_data=$this->ProductEPModelObj->getWarehousesList();
        $bin_type= $this->ProductEPModelObj->GetBinDimensionLevels();
        $hss_code =$this->getHssCode($product_id);
        $pro_packs_new_tab = $this->_roleRepo->checkPermissionByFeatureCode('PRPK0001');
        $beneficiaryName = $this->objPricing->getBenificiaryName();
        $wareHouse = $this->objPricing->getWarehouses();
        $product_stars = $this->objPricing->getProductStars();
        //$dcs = $this->objPricing->getAllDCS();
        $dcs=$this->_roleRepo->getAllDcs($this->userId);
        $product_mobile_view = $this->_roleRepo->checkPermissionByFeatureCode('PRDMBLVEW001');
        $prd_elp_hst = $this->_roleRepo->checkPermissionByFeatureCode('PRELPHST001');
        $prd_cust_esu = $this->_roleRepo->checkPermissionByFeatureCode('PRESUCUST001');

        return View::make('Product::editProduct',['productData'=>$Data,'category_link'=>$category_name,
            'category_id'=>$getCategory_id,'productAttributGroupInfo'=>$productAttributGroupInfo,
            'productAttributInfo'=>$productAttributInfo,'manufacturer_name'=> $manufacturer_name,'productImages' => $productImages,
            'product_id'=>$product_id,'brandlist'=>$brandList,'packageLevel'=>$this->packageLevel,'packageVolumeUOM'=>$this->packageVolumeUOM,
            'packageLBHUOM'=>$this->packageLBHUOM,'packageWeightUOM'=>$this->packageWeightUOM,'kvi_data'=>$this->manufacturer_kvi,
            'product_form_data'=>$this->product_form_data,'license_typ_data'=>$this->license_typ_data,'shelf_uom_data'=>$this->shelf_uom_data,
            'getProductCharacterstics'=>$getProductCharacterstics,'attributeIdValueData'=>$attributeIdValueData,
            'getAttributeGroup'=>$getAttributeGroup,'history'=>$history, 'state_wise_tax_classes' => $state_wise_tax_classes,
            'alltax' => $alltaxes,'getZoneData'=>$getZoneData,'getStateDetails'=>$getStateDetails,
            'getCustomerGroup'=>$getCustomerGroup,'offer_pack_data'=>$this->offer_pack_data,
            'legalentity_warehouses' => $legalentity_warehouses, 'tax'=>$tax,'pricing'=>$pricing, 
            'Related_Products'=>$this->Related_Products, 'Freebie_Configuration'=>$this->Freebie_Configuration, 'Packing_Configuration'=>$this->Packing_Configuration,
            'Tax_Information'=>$this->Tax_Information, 'Suppliers'=>$this->Suppliers, 'Pricing_tab'=>$this->Pricing_tab, 'Inventory'=>$this->Inventory,
            'Approval_History'=>$this->Approval_History,'warranty_policy'=>$warranty_policy,'return_policy'=>$return_policy,'warehouse_data'=>$warehouse_data,'bin_type'=>$bin_type,'product_group_data'=>$product_group_data,'warehouse_info'=> $this->warehouse_info,'inventory_reason_Codes' => $InventoryReasonCodes,'esuPermission'=>$esuPermission,'cpPermissions'=>$cpPermissions,'sellablePermissions'=>$sellablePermissions,"product_star"=>$this->product_star,'hss_code'=>$hss_code,"bin_category_type"=>$this->bin_category_type,'beneficiaryName' => $beneficiaryName ,'wareHouses' => $wareHouse,'product_stars'=>$product_stars,"pro_grp_edit_feature"=>$pro_grp_edit,"pro_grp_add_feature"=>$pro_grp_add,"pro_history_tab"=>$pro_history_tab,"pro_packs_new_tab"=>$pro_packs_new_tab,'dcs'=>$dcs, "supplier_login_permissions" => $supplier_login_permissions, "duplicate_product_permissions" => $duplicate_product_permissions,'grouped_Products'=>$this->grouped_Products,'cp_tab'=>$cp_enable_tab,'product_mobile_view'=>$product_mobile_view,'prd_elp_hst'=>$prd_elp_hst,'prd_cust_esu'=>$prd_cust_esu]);
 }
	
	
    public function freeBieConfigurations(Request $request)
    {
      $data= $request->all();
      return $this->productInfoObj->addFreeBieProducts($data);
    }
    public function editFreebieConfiguration($freebie_id)
    {
       $rs= DB::table('freebee_conf')->where('free_conf_id',$freebie_id)->get()->all();
      return $rs;
    }
    public function deleteFreebieProduct($freebie_id)
    {

      $freebie_data = DB::table('freebee_conf')->where('free_conf_id',$freebie_id)
      ->select("main_prd_id","mpq","free_prd_id","qty","stock_limit","state_id","le_wh_id","start_date","end_date","is_stock_limit","freebee_desc")->get()->all();
        $freebie_data = json_decode(json_encode($freebie_data), true);
        $pid = (string)$freebie_data[0]['main_prd_id'];
       UserActivity::userActivityLog("Products", $freebie_data, "Freebie configuration has been deleted.", "", array("Product_id" => $pid ));
      $rs= DB::table('freebee_conf')->where('free_conf_id',$freebie_id)->delete();

      $packconfig=DB::table('product_attributes')->select('value','prod_att_id')->where('attribute_id',6)->where('product_id',$pid)->first();
      $offerpack=isset($packconfig->value)?$packconfig->value:'';
      if($offerpack=='Consumer Pack Outside'){
        $productData= ProductInfo::where('product_id',$pid)
                                ->update(['is_sellable'=>0]);
        DB::table('product_attributes')->where('attribute_id',6)->where('product_id',$pid)->where('prod_att_id',$packconfig->prod_att_id)->update(['value'=>'Regular']);
      }
      echo $rs;
    }
    public function getAllAttributes($condition_type)
    {
      $attData= $this->attributeObj->where('attribute_code',$condition_type)->pluck('attribute_id')->all();
      $attData= $attData;//json_decode($attData);
      return $attData;
    }
    //get all product name for freebie products
    public function getAllProducts($product_id)
   {
    $getAttId= $this->getAllAttributes('offer_pack');
    return $this->productInfoObj->getFreebieProducts($this->legal_entity_id,$getAttId[0],$product_id);
   } 
    //Used for Tax Tab
    public function stateWiseTaxClasses() {
        return json_encode($this->_taxclass->stateWise());
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
    public function saveCPEnabled($prodId){
      try {
        $status = false;
        $message = "Unable to change CP";
        $data = Input::all();
        DB::table('products')->where('product_id',$prodId)->update(['cp_enabled' => $data['checked']]);
        $status = true;
        $message = "CP successfully enabled";
        return json_encode(['status' => $status,
          'message' => $message]);
      }  catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function productPreview($product_id)
    {
     try{ 
    $prd_elp_hst = $this->_roleRepo->checkPermissionByFeatureCode('PRELPHST001');
    $cp_enable_tab = $this->_roleRepo->checkPermissionByFeatureCode('CPTAB001');
		$esuPermission=$this->_roleRepo->checkPermissionByFeatureCode('PRD0010');
		$InventoryReasonCodes = $this->_inventory->getInventoryReasonCodes();
        $checkPPermissions=$this->_roleRepo->checkPermissionByFeatureCode('PRD009');
        $pro_history_tab = $this->_roleRepo->checkPermissionByFeatureCode('PHR0001');
        $pro_packs_new_tab = $this->_roleRepo->checkPermissionByFeatureCode('PRPK0001');
        $prd_cust_esu = $this->_roleRepo->checkPermissionByFeatureCode('PRESUCUST001');
        if ($checkPPermissions==0)
        {
            return Redirect::to('/');
        }
		$tab = 'all';
        if (isset($_SERVER["HTTP_REFERER"]))
        {
            $referer = $_SERVER["HTTP_REFERER"];
            $urlArray = explode('/', $referer);
            $tab = (isset($urlArray[4])) ? $urlArray[4] : 0;
        }
		if($tab == 'approvalticket'){$tab = 'all';}
		$breadCrumbs = array('Home' => url('/'), 'Products' => url('/products').'/'.$tab, 'View Product' => '#');
        parent::Breadcrumbs($breadCrumbs);       
        $productData=$this->productInfoObj->getProductData($product_id);
        $Data= json_decode(json_encode($productData));
        $parent_id=$Data->get_category_model->parent_id;  
        $category_name= $this->productInfoObj->categoryLoopLink($Data->category_id);
        $category_name= rtrim($category_name,' >> ');
        $checkPolicyData= ProductPolicies::where('product_id', $product_id)->get()->all();
        $polocy_type = array('warranty_policy', 'return_policy');
        $warranty_policy='';
        $return_policy='';
        $checkPolicyData= json_decode(json_encode($checkPolicyData),true);
        if(!empty($checkPolicyData))
        {
          for ($p = 0; $p < sizeof($checkPolicyData); $p++)
          {
            if($checkPolicyData[$p]['policy_type_name']=='warranty_policy')
            {
              $warranty_policy=$checkPolicyData[$p]['policy_details'];
            }
            if($checkPolicyData[$p]['policy_type_name']=='return_policy')
            {
              $return_policy=$checkPolicyData[$p]['policy_details'];
            }           
          }
        } 
        $brands= BrandModel::find($Data->brand_id);
        $brandList =BrandModel::all();
        $brandData=json_decode(json_encode($brands));
        $getAttSetIdData=$this->ProductAttributesObj->where('product_id',$product_id)->first();
        $getAttSetId= json_decode(json_encode($getAttSetIdData),true);
        //get product attributes data with group
        $attributeIdValueData= $this->productInfoObj->getAttributeIdValue($product_id,$getAttSetId['attribute_set_id']);        
        //get attribute group data
        $getAttributeGroup= $this->productInfoObj->getAttGroupData($product_id,$getAttSetId['attribute_set_id']);
        $where = ['product_id'=>$product_id];
        $productImages = $this->productMediaObj->where($where)->get()->all();
        $wherehistory = ['awf_for_id'=>$product_id];
        $history = $this->ProductEPModelObj->productWorkFlowModel($product_id);
        $productAttributGroupInfo =$this->productInfoObj->getAttributeGroupData($product_id,$Data->category_id);
         $productAttributInfo =$this->productInfoObj->getAttributeData($product_id);
        $getProductCharacterstics =$this->ProductCharacteristicObj->where('product_id',$product_id)->first();
        $getProductCharacterstics=json_decode(json_encode($getProductCharacterstics),true); 
         $where = ['product_id'=>$product_id];
        $productImages = $this->productMediaObj->where($where)->get()->all();
        $productAttributInfo = $this->productInfoObj->getAttributeData($product_id);

        $user_name = $this->Legalentities->where('legal_entity_id','=',$Data->manufacturer_id)->select('business_legal_name')->first();
        $user_name= json_decode(json_encode($user_name),true);
        $manufacturer_name= $user_name['business_legal_name']; 
         parent::Title($Data->product_title);
         //Section for Tax Tab settings
        $alltaxes = $this->_masterlookup->getTaxTypes();
        $product_star = $this->getPackTypeName($Data->star);
        $product_star= (isset($product_star['master_lookup_name']))?$product_star['master_lookup_name']:'';
        
        sort($alltaxes);
        //Adding 'Action' item in array for Modal Table.
        $taxArr = array();
        $taxArr['master_lookup_name'] = 'Action';
        $taxArr['master_lookup_id'] = '0';
        $alltaxes[] = $taxArr;
        $state_wise_tax_classes = $this->stateWiseTaxClasses();
        $hss_code = $this->getHssCode($product_id);
        return View::make('Product::productpreview',['productData'=>$Data,'category_link'=>$category_name,
            'productAttributGroupInfo'=>$productAttributGroupInfo,'productImages' => $productImages,'product_id'=>$product_id,
            'kvi_data'=>$this->manufacturer_kvi,'product_form_data'=>$this->product_form_data,'license_typ_data'=>$this->license_typ_data,
            'shelf_uom_data'=>$this->shelf_uom_data,'getProductCharacterstics'=>$getProductCharacterstics,
            'manufacturer_name'=> $manufacturer_name,'productAttributInfo'=>$productAttributInfo,
            'attributeIdValueData'=>$attributeIdValueData,'getAttributeGroup'=>$getAttributeGroup,'history'=>$history, 
            'state_wise_tax_classes' => $state_wise_tax_classes, 'alltax' => $alltaxes,'Related_Products'=>$this->Related_Products, 
            'Freebie_Configuration'=>$this->Freebie_Configuration, 'Packing_Configuration'=>$this->Packing_Configuration,
            'Tax_Information'=>$this->Tax_Information, 'Suppliers'=>$this->Suppliers, 'Pricing_tab'=>$this->Pricing_tab, 'Inventory'=>$this->Inventory,
            'Approval_History'=>$this->Approval_History,'warranty_policy'=>$warranty_policy,'return_policy'=>$return_policy,'warehouse_info'=> $this->warehouse_info,'inventory_reason_Codes' => $InventoryReasonCodes,'esuPermission'=>$esuPermission,'product_star'=>$product_star,'hss_code'=>$hss_code,"bin_category_type"=>$this->bin_category_type,"pro_history_tab"=>$pro_history_tab,'pro_packs_new_tab'=>$pro_packs_new_tab,'grouped_Products'=>$this->grouped_Products,'cp_tab'=>$cp_enable_tab,'prd_elp_hst'=>$prd_elp_hst,'prd_cust_esu'=>$prd_cust_esu]);
      }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return Redirect::to('/');
      } 
    }   
    public function approvalAccess(Request $request)
    {        
        $approvalForId = $request['approval_for_id'];
        $approvalTypeId = $request['approval_type_id'];
        $status_code = $this->Product_Model_Obj->approvalSave($approvalTypeId, $approvalForId);
        $result = $this->approvalCommonFlow->getApprovalFlowDetails($approvalTypeId, $status_code, $this->userId);
        $flag = 1;
        $options = array();
        if($result['status'])
        {
            $data = $result['data'];
            foreach($data as $valuearray)
            {
              $options[$valuearray['nextStatusId']] = $valuearray['condition'];
            }
        }
        else
        {
            return 0;
        }      
        $opt = '<option value="">Please select</option>';
        foreach($options as $key=>$val)
        {
            $opt .= '<option value="'.$key.'">'.$val.'</option>';
        }
        $select = $opt;
        return $select;
    }
    public function productNameChecking(Request $request)
    {
      DB::enableQueryLog();
      $data= $request->all();
      $data = $this->productInfoObj->uniqueProductName($data['product_title']);
      return $data;
    }
    public function getUserNameById($user_id)
    {
         $data=$this->productInfoObj->getUserName($user_id);
         return $data;
    }   
    public function groupedProducts($product_id)
    {
        $product_image;
        $querys= array();
        $getProductGroupedQuerys = DB::table('products')->where('product_id', $product_id)->pluck('product_group_id')->all();
         $querys = DB::table('products')->where('product_group_id',$getProductGroupedQuerys[0])
         ->orwhere('product_id',$getProductGroupedQuerys[0])->get()->all();         
          $querys = json_decode(json_encode($querys), true);
          foreach ($querys as $key => $value) {
             
               if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $querys[$key]['primary_image'])) {
                  $product_image = '/uploads/products/' . $querys[$key]['primary_image'];
              }else
              {
                $product_image=$querys[$key]['primary_image'];
              }
              $querys[$key]['product_image']='<img style="max-height: 32px; max-width: 32px; height:auto; width:auto;vertical-align: middle;" src="'.$product_image.'">';
              $querys[$key]['sku'] = $querys[$key]['sku'];
              $querys[$key]['product_title'] = $querys[$key]['product_title'];
              $querys[$key]['mrp'] = number_format($querys[$key]['mrp'],2);
              $querys[$key]['cp_enabled'] = ($querys[$key]['cp_enabled']== 1)?'Yes':'No';
              if($querys[$key]['created_by'])
              {
                 $querys[$key]['created_by'] = $this->getUserNameById($querys[$key]['created_by']); 
              }else
              {
                  $querys[$key]['created_by'] = NULL;
              }
              
              if($querys[$key]['updated_by'])
              {
                $querys[$key]['updated_by'] = $this->getUserNameById($querys[$key]['updated_by']);
              }
              else
              {
                $querys[$key]['updated_by'] = NULL;
              }
              $querys[$key]['created_at'] = $querys[$key]['created_at'];
              $querys[$key]['updated_at'] = $querys[$key]['updated_at'];
              $querys[$key]['Action'] = '&nbsp;<a class="delete" href="'.$querys[$key]['product_id'].'" > <i class="fa fa-pencil" aria-hidden="true"></i> </a>';
          }
        

        echo json_encode(array('Records' => $querys, 'TotalRecordsCount' => count($querys)));
    }
    public function getWhBinConfig($product_id)
    {
        $product_image;
        $querys= array();
               $querys = DB::table('product_bin_config')
                ->join('products','product_bin_config.prod_group_id','=','products.product_group_id')
                ->select('prod_bin_conf_id','wh_id','prod_group_id','bin_type_dim_id','pack_conf_id','min_qty','max_qty')
                ->where('products.product_id',$product_id)->get()->all();
        $querys = json_decode(json_encode($querys), true);
          foreach ($querys as $key => $value) {
            $bin_name=$this->getBinTypeName($querys[$key]['bin_type_dim_id']);
            $pack_name=$this->getPackTypeName($querys[$key]['pack_conf_id']);
              $querys[$key]['wh_name'] =  $this->getWhName($querys[$key]['wh_id']);
              $querys[$key]['bin_type'] = (empty($bin_name['master_lookup_name'])?'':$bin_name['master_lookup_name']);
               $querys[$key]['pack_conf_id'] =(empty($pack_name['master_lookup_name'])?'':$pack_name['master_lookup_name']); 
              $querys[$key]['min_capacity'] = $querys[$key]['min_qty'];
              $querys[$key]['max_capacity'] = $querys[$key]['max_qty'];
              $querys[$key]['Action'] = '&nbsp;<a class="delete" onclick="editWhBinConfig('.$querys[$key]['prod_bin_conf_id'].')" > <i class="fa fa-pencil" aria-hidden="true"></i> </a>';
          }
        echo json_encode(array('Records' => $querys, 'TotalRecordsCount' => count($querys)));
    }
    public function getWhName($wh_id)
    {
      $rs=DB::table('legalentity_warehouses')
        ->where('le_wh_id',$wh_id)->pluck('lp_wh_name')->all();
      return $rs;
    }
    public function getBinTypeName($bin_type_id)
    {
        $rs=DB::table('bin_type_dimensions as bin_dim')
            ->join('master_lookup as msl','bin_dim.bin_type','=','msl.value')
            ->where('bin_dim.bin_type_dim_id',$bin_type_id)
            ->select(DB::raw('CONCAT(master_lookup_name,"(",LENGTH,",",breadth,",",heigth,")") AS master_lookup_name'))
            ->first();
        $rs=json_decode(json_encode($rs),true);
      return $rs;
    }
    public function getPackTypeName($pack_id)
    {
        $rs=DB::table('master_lookup')
            ->where('value',$pack_id)
            ->select('master_lookup_name')
            ->first();
        if(!empty($rs))
        $rs=json_decode(json_encode($rs),true);
      else
        $rs='';
      return $rs;
    }
    public function saveWhBinConfigData($product_id, Request $request)
    {
      DB::enableQueryLog();
      $data= $request->all();
      $getProductGrpId=DB::table('products')
                      ->where('product_id',$product_id)
                      ->pluck('product_group_id')->all();
      $getProductGrpId=json_decode(json_encode($getProductGrpId),true);
      $checkData= DB::table('product_bin_config')
                  ->where('wh_id',$request->wh_id)
                  ->where('bin_type_dim_id',$request->bin_type)
                  ->where('pack_conf_id',$request->wh_pack_type)
                  ->where('prod_group_id',$getProductGrpId[0])
                  ->where('min_qty',$request->pro_min_capacity)
                  ->where('max_qty',$request->pro_max_capacity)
                  ->pluck('wh_id')->all();
      if(!empty($checkData))
      {
        return "false";
      }else
      {
        if(empty($request->edit_wh_id))
        {
          $rs=DB::table('product_bin_config')->insert(['prod_group_id'=> $getProductGrpId[0],'wh_id'=>$request->wh_id,'bin_type_dim_id'=>$request->bin_type,'pack_conf_id'=>$request->wh_pack_type,'min_qty'=>$request->pro_min_capacity,'max_qty'=>$request->pro_max_capacity]);
        }else
        {
          $rs=DB::table('product_bin_config')->where('prod_bin_conf_id',$request->edit_wh_id)->update(['bin_type_dim_id'=>$request->bin_type,'wh_id'=>$request->wh_id,'pack_conf_id'=>$request->wh_pack_type,'min_qty'=>$request->pro_min_capacity,'max_qty'=>$request->pro_max_capacity]);
        }      
      }
	  if($rs)
		return 1;
	  else
		return 0;
    }
    public function saveProductGroup(Request $request)
    {
      $name=$request->product_group_name;
      $grp_id = $request->product_group_id;
      $checkGrName= DB::table('product_groups')
                    ->where('product_grp_name','=',$name)
                    ->get()->all();
      if($grp_id=='null' || $grp_id=='')
      {
           $pid = $request->pid;
           $getProductdetails=DB::table('products')->join('product_attributes','product_attributes.product_id','=','products.product_id')->where('products.product_id','=',$pid)->wherein('attribute_id',array(1,2))->select('brand_id','manufacturer_id','category_id','value')->get()->all();
            $brandid=isset($getProductdetails[0]->brand_id)?$getProductdetails[0]->brand_id:'';
            $categoryid=isset($getProductdetails[0]->category_id)?$getProductdetails[0]->category_id:'';
            $manufacturerid=isset($getProductdetails[0]->manufacturer_id)?$getProductdetails[0]->manufacturer_id:'';
            $pack_type_value = isset($getProductdetails[0]->value)?$getProductdetails[0]->value:0;
            $pack_type = ($getProductdetails[1]->value != "")?$getProductdetails[1]->value:0;
            $value=$pack_type_value.','.$pack_type;
         if(empty($checkGrName))
        {
          //$pid = $request->pid;
          $rs=DB::table('product_groups')->insertGetId(['product_grp_name'=> $name,'created_by'=>$this->userId]);
            $rss=DB::table('product_groups')->where('product_grp_id',$rs)->update(['product_grp_ref_id'=> $rs.'000','updated_by'=>$this->userId]);
            $updateProductGrp = DB::table('products')->where('product_id',$pid)->update(['product_group_id'=> $rs.'000','updated_by'=>$this->userId]);

            $productGrpHstySave=DB::table('product_group_history')->insertGetId(['product_id'=>$pid,'brand_id'=>$brandid,'category_id'=>$categoryid,'manufacturer_id'=>$manufacturerid,'created_at'=>date('Y-m-d H:i:s'),'created_by'=>$this->userId,'updated_at'=>date('Y-m-d'),'updated_by'=>$this->userId,'value'=>$value,'product_grp_ref_id'=>$rs.'000']);
           return $rs.'000';
         }else
         {
          $productGrpHstySave=DB::table('product_group_history')->insertGetId(['product_id'=>$pid,'brand_id'=>$brandid,'category_id'=>$categoryid,'manufacturer_id'=>$manufacturerid,'created_at'=>date('Y-m-d H:i:s'),'created_by'=>$this->userId,'updated_at'=>date('Y-m-d'),'updated_by'=>$this->userId,'value'=>$value,'product_grp_ref_id'=>$checkGrName[0]->product_grp_ref_id]);
          $updateProductGrp = DB::table('products')->where('product_id',$pid)->update(['product_group_id'=> $checkGrName[0]->product_grp_ref_id,'updated_by'=>$this->userId]);
          return $updateProductGrp;
          //return "false";
         }
      }
      else
      {
        $checkGrNameExist= DB::table('product_groups')
                    ->where('product_grp_name','=',$name)
                    ->where('product_grp_ref_id','!=',$grp_id)
                    ->get()->all();
        if(empty($checkGrNameExist))
        {
           $rss=DB::table('product_groups')->where('product_grp_ref_id',$grp_id)->update(['product_grp_name'=> $name,'updated_by'=>$this->userId]);
            return $grp_id;
        }
        else{
         return "false";
        }
      }
    }
    public function getProductGroupName($pid)
    {
      $getPid= DB::table('products as p')
              ->join('product_groups as p_grp','p.product_group_id','=','p_grp.product_grp_ref_id')
              ->select('p.product_title AS product_name',
              'p_grp.product_grp_ref_id AS product_grp_id')
              ->where('p.product_id','=',$pid)
              ->select('p_grp.product_grp_name AS product_name',
              'p_grp.product_grp_ref_id AS product_grp_id')
              ->first();
          $getPid=json_decode(json_encode($getPid),true);
      return $getPid;
    }
    public function getProductGroupList()
    {
        $grpOptions='';
        $getPid= DB::table('product_groups')
                ->select('product_grp_name','product_grp_ref_id')
                ->get()->all();
        $getPid=json_decode(json_encode($getPid),true);
        foreach ($getPid as $grpValue){
        $grpOptions.= '<option value="'.$grpValue['product_grp_ref_id'].'">'.$grpValue['product_grp_name'].'</option>';
        }
        return $grpOptions;
    }
    public function getWhBinConfigDataByBinId($wh_id)
    {
      $getPid= DB::table('product_bin_config')
              ->where('prod_bin_conf_id','=',$wh_id)
              ->first();
      $getPid=json_decode(json_encode($getPid),true);
      return $getPid;
      
    }
    public function productStarUpdate($product_id)
    {
        $packData = DB::table('product_pack_config')
                    ->where(['product_id'=>$product_id,'is_sellable'=>1])
                    ->where('is_sellable','=',1)
                    ->pluck('star')->all();
        $color_code='140004';
        if(!empty($packData))
        {
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
    public function getHssCode($pid)
    {
  		try{
          $hss_code = DB::table('tax_class_product_map')
                      ->where('product_id',$pid)
                      ->pluck('hsn_code')->all();
  		$values = array_filter($hss_code);
      //$hsncode = array_pop($values);
      $hsncode = isset($values[0])?$values[0]:'';
      $masterhsncode = DB::table('HSN_Master')
                      ->where('ITC_HSCodes',$hsncode)
                      ->get()->all();
      $values = isset($masterhsncode[0]->ITC_HSCodes)?$masterhsncode[0]->ITC_HSCodes:'';
          //return array_pop($values);
        return $values;
  		}
  		catch(Exception $e)
  		{
  			return $e->getMessage();
  		}
    }
    public function getProductGroupId($pid)
    {
      $productSql="select `product_title`, `brand_id`, `category_id`, `manufacturer_id`, `product_group_id`, `product_title`, `attribute_id`, `value` from `products` inner join `product_attributes` on `product_attributes`.`product_id` = `products`.`product_id` where `products`.`product_id` = ".$pid." and `attribute_id` in (1, 2)";
      $productSql=DB::selectFromWriteConnection(DB::raw($productSql));              

      $productSql =  json_decode(json_encode($productSql), true);
      $brand_id = isset($productSql[0]['brand_id'])?$productSql[0]['brand_id']:'';
      $cat_id = isset($productSql[0]['category_id'])?$productSql[0]['category_id']:'';
      $manf_id = isset($productSql[0]['manufacturer_id'])?$productSql[0]['manufacturer_id']:'';
      $pack_type_value = isset($productSql[0]['value'])?$productSql[0]['value']:'';
      $pack_type = isset($productSql[1]['value'])?$productSql[1]['value']:'';
      //Log::info('product group updating------');
      $attributeStatus = DB::table('product_attributes')
                        ->join('products','product_attributes.product_id','=','products.product_id')
                        ->where('brand_id',$brand_id)
                        ->where('category_id',$cat_id)
                        ->where('manufacturer_id',$manf_id)
                        ->wherein('attribute_id', [1,2])
                        ->wherein('value',[$pack_type_value,$pack_type])
                        ->select('product_group_id')
                        ->groupby('product_attributes.product_id')
                        ->havingRaw('COUNT(DISTINCT(product_attributes.value)) = 2')
                        ->first();
      $attributeStatus = json_decode(json_encode($attributeStatus), true);
      //Log::info($attributeStatus);
      /*if($attributeStatus['product_group_id'] == 0)
      {*/
        $title = $productSql[0]['product_title'];
        $conditonRs=0;
        if(strstr($title, 'mrp', true))
        {
          $title = strstr($title, 'mrp', true);
          $conditonRs = 1;
        }
        else if(strstr($title, 'MRP', true))
        {
          $title = strstr($title, 'MRP', true);
          $conditonRs = 1;
        }
        $getGrpId = DB::table('product_groups')
                  ->insertGetId(['product_grp_name'=>$title,"created_by"=>$this->userId]);
        $rss = DB::table('product_groups')
                ->where('product_grp_id',$getGrpId)
                ->update(['product_grp_ref_id'=> $getGrpId.'000','updated_by'=>$this->userId]); 
        $value=$pack_type_value.','.$pack_type;        
        $productGrpHstySave=DB::table('product_group_history')->insertGetId(['product_id'=>$pid,'brand_id'=>$brand_id,'category_id'=>$cat_id,'manufacturer_id'=>$manf_id,'created_at'=>date('Y-m-d H:i:s'),'created_by'=>$this->userId,'updated_at'=>date('Y-m-d'),'updated_by'=>$this->userId,'value'=>$value,'product_grp_ref_id'=>$getGrpId.'000']);       
        DB::table('products')
          ->where('product_id', $pid)
          ->update(['product_group_id' => $getGrpId.'000']);
      /*}else if($attributeStatus['product_group_id']!=0)
      {
         DB::table('products')
          ->where('product_id', $pid)
          ->update(['product_group_id' => $attributeStatus['product_group_id']]);
      }*/
    }
    public function getProductGroupListByManf($pid)
    {
      $productSql = DB::table('products')
                    ->join('product_attributes','product_attributes.product_id','=','products.product_id')
                    ->where('products.product_id','=',$pid)
                    ->wherein('attribute_id',array(1,2))
                    ->select('brand_id','category_id','manufacturer_id','product_group_id','product_title','attribute_id','value')
                    ->get()->all();

      $productSql =  json_decode(json_encode($productSql), true);
      $brand_id = $productSql[0]['brand_id'];
      $cat_id = $productSql[0]['category_id'];
      $manf_id = $productSql[0]['manufacturer_id'];
      $pack_type_value = $productSql[0]['value'];
      $pack_type = ($productSql[1]['value'] != "")?$productSql[1]['value']:0;

      $attributeStatus = DB::table('product_attributes')
                        ->join('products','product_attributes.product_id','=','products.product_id')
                        ->where('brand_id',$brand_id)
                        ->where('category_id',$cat_id)
                        ->where('manufacturer_id',$manf_id)
                        ->wherein('attribute_id', [1,2])
                        ->wherein('value',[$pack_type_value,$pack_type])
                        ->groupby('product_attributes.product_id')
                        ->havingRaw('COUNT(DISTINCT(product_attributes.value)) = 2')
                        ->pluck('product_group_id')->all();
      $recordsInProductHistory=DB::table('product_group_history')->select('*')->where('brand_id',$brand_id)->where('category_id',$cat_id)->where('manufacturer_id',$manf_id)->pluck('product_grp_ref_id')->all(); //echo "<pre/>";print_r($attributeStatus);echo "<pre/>";print_r($recordsInProductHistory);
      $attributeStatus=array_unique(array_merge($attributeStatus,$recordsInProductHistory), SORT_REGULAR);//echo '=============================================================';echo "<pre/>";print_r($attributeStatus);exit;                 
        $grpOptions='';
        if(!empty($attributeStatus))
        {
          $getPid= DB::table('product_groups')
                ->wherein('product_grp_ref_id',$attributeStatus)
                ->select('product_grp_name','product_grp_ref_id')
                ->get()->all();
          $getPid=json_decode(json_encode($getPid),true);
          foreach ($getPid as $grpValue){
          $grpOptions.= '<option value="'.$grpValue['product_grp_ref_id'].'">'.$grpValue['product_grp_name'].'</option>';
          }
        }
        return $grpOptions;
    }
    public function packNearLowestValue($pack_array, $current_pack)
    {
      sort($pack_array);
      $lowest= null;
      foreach ($pack_array as $a) {
          if($a < $current_pack)
                $lowest=$a;
          }
      return $lowest;
    }
    public function packNearHighestValue($pack_array, $current_pack)
    {
      sort($pack_array);
      $highest = null;
      foreach ($pack_array as $a) {
        if ($a > $current_pack)
        {
          $highest = $a;
          return $highest;
        }
      }
    }
    public function productHistoryGrid($product_id, Request $request)
    {
        $pageSize = (int)$request->pageSize;
        $page = ($request->page==0)?1:$request->page;
        $product_image;
        $myarray= array();
        $productHistoryObj = new ProductHistory(); 
        $product_history_data= $productHistoryObj->productHistoryData($product_id,$pageSize,$page);
        $querys = json_decode(json_encode($product_history_data), true);
        foreach ($querys as $key => $value) {
          $myarray[$key]['user_name'] = $value['userDetails']['username'];
          $myarray[$key]['reason_type'] =  $value['action'];
          $myarray[$key]['updated_at'] =  $value['updated_at'];
          $myarray[$key]['data']  = json_encode($value['newvalues']);
         
        }
        unset($myarray['count']);
        return array('Records' => $myarray, 'TotalRecordsCount' => $querys['count']);
    }


    public function getProductpPacks(Request $request){
        $this->objCommonGrid = new commonIgridController();
        $product_id = $request->input('product_id');
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("margin", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("customer_type", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("elp", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("esp", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pack", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("dcname", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $orderBy = "";
        $orderBy = $request->input('%24orderby');
        if($orderBy==''){
            $orderBy = $request->input('$orderby');
        }

        // Arrange data for pagination
        $page="";
        $pageSize="";
        if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
            $page = $request->input('page');
            $pageSize = $request->input('pageSize');
        }

        return $this->ProductEPModelObj->productPacksGrid($makeFinalSql, $orderBy, $page, $pageSize,$product_id);
    }

    /*Function : checkConsumerPackOutsideforProduct
      Functionality:check whether product has freebie configured or not and freebie should be outside the product pack
      Example:Santor(Buy 3 get 1 which is inside the pack and Renolds pen free on buying santoor soap is consumer pack outside)
      Input:productId
      Output:return true or false
    */
    public function checkConsumerPackOutsideforProduct(){
      try{
          $data=Input::all();
          $productId=$data['product_id'];
          return $this->ProductEPModelObj->checkFreeBieConfiguredIsConsumerpackOutside($productId);
      } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}
