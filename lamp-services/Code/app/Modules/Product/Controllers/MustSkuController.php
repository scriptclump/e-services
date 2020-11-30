<?php

namespace App\Modules\Product\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Redirect;
use Session;
use App\Http\Controllers\BaseController;
use App\Modules\Product\Models\MustSkuProduct;
use App\Modules\Roles\Models\Role;
use App\Central\Repositories\RoleRepo;
use App\Modules\Cpmanager\Models\ProductModel;
use App\Modules\Pricing\Controllers\uploadPriceSlabFiles;


class MustSkuController extends BaseController
{

    public function __construct()
    {
        try
        {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                $this->mustskuproductModel=new MustSkuProduct();
                $this->roleObj = new Role();
                $this->_roleRepo = new RoleRepo();
                $skugridaccess  = $this->_roleRepo->checkPermissionByFeatureCode('PRDSKU001');
                if (!$skugridaccess)
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });
             
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function mustSkuindex()
    {
        try
        {
            $Json=json_decode($this->roleObj->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);      
            $warehouse=$this->roleObj->GetWareHouses($filters);
            $warehouse = json_decode(json_encode($warehouse), True);
            
            return view('Product::mustSku')
                ->with(['dcs' => json_decode(json_encode($warehouse))]);

        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getMustSkuProducts(Request $request)
    {
        try
        {
            $result = $this->mustskuproductModel->getMustSKUGridData($request);
            return $result;

        }catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function searchMustSku()
    {
        try{
            $data = \Input::all();
            $skus = $this->mustskuproductModel->getMustSkus($data);
            return $skus;
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function addMustSkuProduct(){
        $data=Input::all();
        $msg=[];

        $le_wh_id=isset($data['dcid'])?$data['dcid']:'';
        $product_id=isset($data['addproduct_id'])?$data['addproduct_id']:'';

        if(empty($le_wh_id))
        {
            $msg[]="Warehouse Can't be Empty<br/>";
        }

        if(empty($product_id))
        {
            $msg[]="Product Can't be Empty<br/>";
        }

        $checkifproductexitsinskulist=$this->mustskuproductModel->checkIfProductExitsInskulist($data);
        if(!$checkifproductexitsinskulist)
        {
            $msg[]="Product Already Exists";
        }

        if(count($msg)<=0)
        {
            $addskus=$this->mustskuproductModel->addMustSku($data);
            if($addskus){
               $msg[]='SKU Added Successfully';
            }else{
               $msg[]='Failed to add SKU';    
            }
            
        }
        $messg = json_encode(array('status_messages' => $msg));
        return $messg; 

    }


    public function deleteSKUProduct(){
        $data=Input::all();
        $pid=$data['product_id'];
        $le_wh_id=$data['le_wh_id'];

        $deleteskus=$this->mustskuproductModel->deleteMustSku($pid,$le_wh_id);
        $messg = json_encode(array('status_messages' => $deleteskus));
        return $messg;

    }

    public function changeSKUProductStatus(){
        $data=Input::all();
        $pid=$data['product_id'];
        $le_wh_id=$data['le_wh_id'];
        $status=$data['status'];

        $skusts=$this->mustskuproductModel->changeMustSkuStatus($pid,$le_wh_id,$status);
        $messg = json_encode(array('status_messages' => $skusts['msg'],'product_name' => $skusts['product_name']));
        return $messg;

    }

    public function productMobileView(){
        try{
            $data=Input::all();
            $product_id=$data['product_id'];
            $le_wh_id=$data['dcid'];
            $cust_type=$data['customer_type'];
            $productmodel=new ProductModel();
            $getmedia=$this->mustskuproductModel->getMedia($product_id);
            $desc=$productmodel->getDescription($product_id);
            $productslabs=$productmodel->getPricing($product_id, $le_wh_id, Session::get('userId'), $cust_type);
            $getmrpforproducts=$productmodel->getProducts($product_id,$le_wh_id,$cust_type);
            //echo "<pre/>";print_r($getmrpforproducts['data'][0]->mrp);exit;
            $mrp=isset($getmrpforproducts['data'][0]->mrp)?$getmrpforproducts['data'][0]->mrp:0;
            //echo "<pre/>";print_r($productslabs);//exit;
            $specifications=$productmodel->getProductSpecifications($product_id);
            $ptr=isset($productslabs[0]["ptr"])?$productslabs[0]["ptr"]:0;
            $inv=isset($productslabs[0]["stock"])?$productslabs[0]["stock"]:0;
            $cfc=isset($productslabs[0]["cfc"])?$productslabs[0]["cfc"]:0;
            $img=(isset($getmedia) && count($getmedia)>0)?$getmedia->image:'/img/Ebutor_img_logo.jpg';
            $res='<div id="replace_div_mobile_view"><div class="col-md-6">
                    <img class="timeline-badge-userpic" src="'.$img.'" style="height:200px;width:180px">
                    </div><div class="col-md-6"><div class="col-md-6">MRP</div><div class="col-md-6"><b>'.$mrp.'</b></div><div class="col-md-6">PTR</div><div class="col-md-6"><b>'.$ptr.'</b></div>
                    <div class="col-md-6">INV</div><div class="col-md-6"><b>'.$inv.'</b></div>
                    <div class="col-md-6">CFC</div><div class="col-md-6"><b>'.$cfc.'</b></div></div><div class="col-md-12">';
            $res.='<table class="table table-bordered table-advance"><tr>
                    <th>Pack Size</th>
                    <th>ESU</th>
                    <th>Pack Price</th>
                    <th>Unit Price</th>
                    <th>Margin</th>
                    </tr>';
            if(is_array($productslabs) && count($productslabs)>0){
                foreach ($productslabs as $key => $value) 
                {
                    $esu=isset($productslabs[$key]["esu"])?$productslabs[$key]["esu"]:0;
                    $packprice=isset($productslabs[$key]["dealer_price"])?$productslabs[$key]["dealer_price"]:0;
                    $unitprice=isset($productslabs[$key]["unit_price"])?$productslabs[$key]["unit_price"]:0;
                    $margin=isset($productslabs[$key]["margin"])?$productslabs[$key]["margin"]:0;
                    $pack_size=isset($productslabs[$key]["pack_size"])?$productslabs[$key]["pack_size"]:0;
                       $res.='<tr>
                                <td>'.$pack_size.'</td>
                                <td>'.$esu.'</td>
                                <td>'.$packprice.'</td>
                                <td>'.$unitprice.'</td>
                                <td>'.$margin.'</td>
                                <tr>'; 
                                
                }
            }else{
                        $res.='<tr>
                                <td colspan="5" style="text-align:center">No Packs Found</td>
                                <tr>';
            }
                $res.='</table></div>';
                $res.='<div class="col-md-12"><b>Product Description</b><br/><br/></div>
                        <div class="col-md-12">'.$desc.'</div>';
                $res.='<div class="col-md-12"><b>Specifications</b><br/><br/></div>';
            if(is_array($specifications) && count($specifications)>0)
            {
                foreach ($specifications as $specifications) 
                {
                   $res.='<br/><br/><div class="col-md-6">'.$specifications->name.'</div><div class="col-md-6">'.$specifications->value.'</div>';
                }
            }else{
                    $res.='<br/><br/><div class="col-md-12">No Specifications Found</div>';
            }
                   $res.='</div>';     
                    echo $res;
        }catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function productCacheFlush(){
        try{
            $data=Input::all();
            $product_id=$data['product_id'];
            $dc_id=$data['dcid'];
            $customer_type=$data['customer_type'];
            $pricObj = new uploadPriceSlabFiles();
  
                $appKeyData = env('DB_DATABASE');
                $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type.'_le_wh_id_'.$dc_id;
                $cache_array = array("product_id"=>$product_id,"le_wh_id"=>$dc_id,"customer_type"=>$customer_type);
                $pricObj->clearCache($keyString,1,$cache_array);
                return 1;
            
        }catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return 0;
        }
    }
}
