<?php

namespace App\Modules\Cpmanager\Controllers;
use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Exception;
use Log;
use URL;
use DB;
use Illuminate\Http\Request;
use App\Modules\Cpmanager\Models\HomeModel;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Central\Repositories\RoleRepo;
use App\Modules\Cpmanager\Models\SearchModel;
use App\Modules\Cpmanager\Models\EcashModel;
use App\Modules\Cpmanager\Models\MasterLookupModel;
use App\Modules\Cpmanager\Models\ProductModel;
use Cache;
/*
    * Class Name: getfeaturedproducts
    * Description: We display data  based on 
      brands, categories & manufacturer data  
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 1st July 2016
    * Modified Date & Reason: 4th July 2016
     added validations
    */


class HomeController extends BaseController {

    
    public function __construct() {

         $this->homepage = new HomeModel(); 
         $this->categoryModel = new CategoryModel();
         $this->_rolerepo = new RoleRepo();
         $this->_search=new SearchModel();
         $this->_lookup=new MasterLookupModel();
         $this->_ecash=new EcashModel();
      }


      public function getfeaturedproducts() {

        try {
            $array = json_decode($_POST['data'], true);
            $flag = (isset($array['flag']) && $array['flag'] != '') ? $array['flag'] : '';
            if (isset($array['customer_token'])) {
                $checkCustomerToken = $this->categoryModel->checkCustomerToken($array['customer_token']);
                if ($checkCustomerToken > 0) {
                    $customer_token = $array['customer_token'];
                    if (isset($array['hub_id'])) {
                        $hub_id = $array['hub_id'];
                    } else {
                        $hub_id = $this->_rolerepo->getUserHubId($customer_token);
                    }
                } else {
                    $customer_token = '';
                    $hub_id = 0;
                }
            } else {
                $customer_token = "";
                $hub_id = 0;
            }
            $blockedList = $this->_search->getBlockedData($customer_token);
            if ((isset($array['segment_id']) && $array['segment_id'] != '') && isset($array['le_wh_id']) && $array['le_wh_id'] != '') {
                $customer_type = isset($array['customer_type']) ? $array['customer_type'] : '';
                $all_top_brands = $this->homepage->ShopbyBrand($array['le_wh_id'], $array['segment_id'], $array['offset_limit'], $array['offset'], $blockedList, $customer_type);

                $getManufacturer = $this->homepage->ShopbyManufacturer($array['le_wh_id'], $array['segment_id'], $array['offset_limit'], $array['offset'], $blockedList, $customer_type);

                $shopbyCategory = $this->homepage->ShopbyCategory($array['le_wh_id'], $array['segment_id'], $array['offset_limit'], $array['offset'], $customer_type);

                $shopbyNewSku = $this->homepage->shopbyNewsku($array['le_wh_id'],5,0);
                $shopbyCustomPacks = $this->homepage->ShopbyCustomPacks($array['le_wh_id'], $array['segment_id'], $array['offset_limit'], $array['offset'], $customer_type);
            } else {
                $error = "legalWarehouseId or segmentId is not set";
                return json_encode(array('status' => "failed", 'message' => $error, 'data' => []));
            }

            /**
             * Without Limit 
             */
            if (isset($array['flag']) && ($array['flag'] == 1 || $array['flag'] == 2 || $array['flag'] == 3 || $array['flag'] == 5)) {
                if ($array['flag'] == 1) {
                    $message = "ShopbyBrand";
                    $data = $all_top_brands;
                } else if ($array['flag'] == 2) {
                    $message = "ShopbyManufacturer";
                    $data = $getManufacturer;
                } else if ($array['flag'] == 3) {
                    $message = "ShopbyCategory";
                    $data = $shopbyCategory;
                } else if ($array['flag'] == 5) {
                    $message = "ShopbyNewsku";
                    $data = $shopbyNewSku;
                } else if ($array['flag'] == 4) {
                    $message = "ShopbyCustomPacks";
                    $data = $shopbyCustomPacks;
                }
                if (!empty($data)) {
                    return Array('status' => "success", 'message' => $message, 'data' => $data);
                } else {
                    return Array('status' => "failed", 'message' => "No data found", 'data' => []);
                }
            } else { // With Limit
                if (empty($all_top_brands)) {
                    $all_top_brands = [];
                } else if (empty($getManufacturer)) {
                    $getManufacturer = [];
                } else if (empty($shopbyCategory)) {
                    $shopbyCategory = [];
                } else if (empty($shopbyNewSku)) {
                    $shopbyNewSku = [];
                }else if (empty($shopbyCustomPacks)) {
                    $shopbyCustomPacks = [];
                }
                $data = array(
                    0 => array(
                        'flag' => 1,
                        'display_title' => 'Shop By Brands',
                        'key' => 'brand_id',
                        'items' => $all_top_brands),
                    1 => array(
                        'flag' => 2,
                        'display_title' => 'Shop By Manufacturer',
                        'key' => 'manufacturer_id',
                        'items' => $getManufacturer),
                    2 => array(
                        'flag' => 3,
                        'display_title' => 'Shop By Category',
                        'key' => 'category_id',
                        'items' => $shopbyCategory),
                    3 => array(
                          'flag' => 4,
                        'display_title' => 'Shop By Packs',
                        'key' => 'cp_pack_id',
                        'items' => $shopbyCustomPacks
                        ),
                    4 =>array(
                        'flag' => 5,
                        'display_title'=> 'Shop By New Sku',  
                        'key'=>'new_sku',                      
                        'items'=> $shopbyNewSku
                        ));
                return json_encode(array('status' => "success", 'message' => 'getfeaturedproducts', 'data' => $data));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            $data = [];
            return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
    }

    /*
    * Class Name: getBanner
    * Description: the function is used to display product Banners  
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 7th July 2016
    * Modified Date & Reason: 
     
    */  

public function getBanner()
{
  try{
   
   $array = json_decode($_POST['data'],true);

      $segmentId=(isset($array['segment_id']) && $array['segment_id']!='')? $array['segment_id']:' ';
      $leWhIds=explode(',', $array['le_wh_id']);
      
        if(isset($array['segment_id']) && $array['segment_id']!='')
        {
            $bannercache = Cache::get('banner_img_'.$array['le_wh_id']);
            if(count($leWhIds)>1){
                  $banner=$this->homepage->getBanner($array);

            }elseif(empty($bannercache)){
                  $banner=$this->homepage->getBanner($array);
                  Cache::put('banner_img_'.$array['le_wh_id'],$banner,60*24*60);
            }else{
                $banner = $bannercache;
            }                   
            
        }
        else
        { 
            $bannercache = Cache::get('banner_img_'.$array['le_wh_id']);
            if(count($leWhIds)>1 && empty($bannercache)){
                  $banner=$this->homepage->getBanner($array);

            }elseif(empty($bannercache)){
                  $banner=$this->homepage->getBanner($array);
                  Cache::put('banner_img_'.$array['le_wh_id'],$banner,60*24*60);
            }else{
                  $banner = $bannercache;
             //$banner=$this->homepage->getBanner($array);
            }
        } 

        if(isset($array['segment_id']) && $array['segment_id']!='')
        {    
            $popupcache = Cache::get('popup_img_'.$array['le_wh_id']);
            if(count($leWhIds)>1 && empty($popupcache)){
                  $popup=$this->homepage->getPopups($array);

            }elseif(empty($popupcache)){
                  $popup=$this->homepage->getPopups($array);
                  Cache::put('popup_img_'.$array['le_wh_id'],$popup,60*24*60);
            }else{
                  $popup=$popupcache;
            }               
            //$popup=$this->homepage->getPopups($array);
        }
        else
        {
          $popupcache = Cache::get('popup_img_'.$array['le_wh_id']);
          if(count($leWhIds)>1 && empty($popupcache)){
                  $popup=$this->homepage->getPopups($array);

            }elseif(empty($popupcache)){
                  $popup=$this->homepage->getPopups($array);
                  Cache::put('popup_img_'.$array['le_wh_id'],$popup,60*24*60);
            }else{
                  $popup=$popupcache;
            }

             //$popup=$this->homepage->getPopups($array);
        } 

                                       
          if(!empty($banner))   
          {
            $res['status']="success";
          $res['message']="getBanner";
          $res['data']['banner']=$banner;

            }
          else
          { 
            $res['status']="failed";
            $res['message']="No Banners found";
            $res['data']['banner']=[];
          }

          if(!empty($popup))   
          {
          $popups[]=$popup[0];
          $res['data']['popup']=$popups;

            }
          else
          { 
            $res['data']['popup']=[];
          }

          $response=json_encode($res);
           echo $response;

           }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
            }
    
}

       
    
     



/*
    * Class Name: sortingDataTabs
    * Description: the function is used to sort by tabs   
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 7th July 2016
    * Modified Date & Reason: 18th July 2016
      reson:added master category_id
     
    */ 


 public function getheaders() {

  try {

  $array = json_decode($_POST['data'],true);

    $masterCategoryId = "55";
    $sortingData = $this->homepage->getSortingData($masterCategoryId);
    $user_id=(isset($array['user_id']) && $array['user_id']!='')? $array['user_id']:'';
    $customer_type=(isset($array['customer_type']) && $array['customer_type']!='')? $array['customer_type']:0;
    $customer_token=(isset($array['customer_token']) && $array['customer_token']!='')? $array['customer_token']:'';
    $segment_id=(isset($array['segment_id']) && $array['segment_id']!='')? $array['segment_id']:'';
    $le_wh_id=(isset($array['le_wh_id']) && $array['le_wh_id']!='')? $array['le_wh_id']:'';

       if(!empty($sortingData))   
          {
           $res['status']="success";
          $res['message']="getheaders";
          
          // 55001 - Slab Offers master lookup value,we will call procedure and check data is present or not
          $prdObj = new ProductModel();
          $flag = 55001;
          $blockedList = $this->_search->getBlockedData($customer_token);
          $le_wh_id = "'".$le_wh_id."'";
          $productIds = $prdObj->getProductsByKPI($flag,0,1,$segment_id,$le_wh_id,-1,
  $blockedList,$customer_type);
          //Log::info('productinfo');
          //Log::info($sortingData);
          $arraySortdata =json_decode(json_encode($sortingData), true);
          if(count($productIds['data']) == 0){
            $arrayKey = array_search($flag, array_column($arraySortdata, 'value'));
            unset($sortingData[$arrayKey]);
            $sortingData = array_values($sortingData);
          }

          //Log::info('productinfo2');
          //Log::info($sortingData);          
          $res['data']=$sortingData;
          if(!empty($user_id))
          {
          $res['ecash_details']=$this->_ecash->getEcashInfo($user_id);

          }else{
             $res['ecash_details']=(object)null;

          }
                                           
            }
          else
          { 
            $res['status']="failed";
            $res['message']="No headers found";
            $res['data']=[];
            $res['ecash_details']=(object)null;
          }
  
     $response=json_encode($res);
      echo $response;

      }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
            }


  }

/*
    * Class Name: getSortingDataFilter
    * Description: the function is used sort data fileds   
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 26th July 2016
    * Modified Date & Reason: 18th July 2016
      reson:added master category_id
       
    */ 
        
   public function getSortingDataFilter() {

    try{

       $data = Input::all();            
       $arr = isset($data['data'])?json_decode($data['data']):array();
       $arr->flag=isset($arr->flag)?$arr->flag:0;
     if($arr->flag==1)
     {
      if (isset($arr->sales_token) && !empty($arr->sales_token)) {

               $checkToken = $this->categoryModel->checkCustomerToken($arr->sales_token);
               if($checkToken > 0) {
                $sortingData =$this->_lookup->getMasterLookupValues(146);

                  } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
               } else {
                return json_encode(array('status' => "failed", 'message' => "Pass admin token", 'data' => []));

            }



        }else{
          $sortingData = $this->homepage->getSortingDataFilter();
        }
         if(!empty($sortingData))   
            {
                  $res['status']="success";
                  $res['message']="getfilters";
                  $res['data']=$sortingData;

                }
                  else
                  { 
                    $res['status']="failed";
                    $res['message']="No Filters found";
                    $res['data']=[];
                  }
  
     $response=json_encode($res); 
           return $response; 

           }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
            }  
                        
            
        }


          
/*
    * Class Name: Getversion 
    * Description: the function is used sort data fileds   
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 26th July 2016
    * Modified Date & Reason: 18th July 2016
      reson:added master category_id
       
    */ 
        
   public function getversion(){
       
        if (isset($_POST['data']))
            {
        
         $json = $_POST['data'];
            $version = json_decode($json, true);
            $data = array();
           $user_id=(isset($version['user_id']) && $version['user_id']!='')?$version['user_id']:0;
            $device_id=(isset($version['device_id']) && $version['device_id']!='')?$version['device_id']:'';
            $ip_address=(isset($version['ip_address']) && $version['ip_address']!='')?$version['ip_address']:0;
            $reg_id=(isset($version['reg_id']) && $version['reg_id']!='')?$version['reg_id']:0;
            $platform_id=(isset($version['platform_id']) && $version['platform_id']!='')?$version['platform_id']:0;
          
         if(!empty($device_id) && $user_id !=0)
            { 
               $this->homepage->InsertDeviceDetails($user_id,$device_id,$ip_address,$platform_id,$reg_id);
           }
        $versioncheck = $this->homepage->versioncheck($version['number'],$version['type']);
       //print_r($versioncheck);die;
        $number = '';
        $name = '';
        $type = '';
        foreach($versioncheck as $version){
            
           $number = $version->number;
           $type = $version->type;
        }

         if($number!='' && $type!='')   
            {
             $res['status']="update";
                  $res['versionUpdateStatus']="1";
                  $res['version_number']=$number;

                }
                  else
                  { 
                    $res['status']="notrequired";
                    $res['versionUpdateStatus']="0";
                    $res['version']='No Update required';
                  }
     $res['currentdate']=date('d-m-Y');
     $response=json_encode($res); 
           return $response;   
                        
            
        }
  
   }


   /*
      * Class Name: getBeatsbyffID
      * Description: Function used to get order details of picklist  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 26 Oct 2016
      * Modified Date & Reason: 
    */
  public function getBeatsbyffID() {

    try{

    $array = json_decode($_POST['data'],true);
        
        if(isset($array['sales_token']) && $array['sales_token']!='') {     

          $valToken = $this->categoryModel->checkCustomerToken($array['sales_token']);
          $limit  = isset($array['limit']) ? $array['limit']: '';
          $offset = isset($array['offset']) ? $array['offset']: '';
          if($limit!=''){
            if($offset!=''){
              if($valToken>0){
                $data= $this->homepage->beatsbyffID($array['sales_token'],$limit,$offset);
                                
                if($data['beats'] == -1)
                  return ['status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []];

                return Array('status'=>"success",'message'=> 'Successfully retrieved Beats','data'=>$data);
              }else{
                 return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);    
              }
            }else{
               return Array('status' => 'failed', 'message' =>'Please send offset', 'data' => []);
            }
          }else{
             return Array('status' => 'failed', 'message' =>'Please send limit', 'data' => []);
          }        
       
     } else {
             return Array('status' => 'failed', 'message' =>'Sales token is not sent', 'data' => []);
       }




     }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      }              

  }


  /*
    * Class Name: unBilledskus
    * Description: the function is used to share sku products based on  billed and not billed
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 4 Nov 2016
    * Modified Date & Reason: 
     
    */ 

public function UnBilledskus() {         
         
 try
        {
            $data = input::all();
            if(isset($data['data'])){
                $params = $data['data'];
                
                $array= json_decode($params,true); 
                }else{
                $array ="";
            }
   
      if(isset($array['sales_token']) && $array['sales_token']!='') {
        if(isset($array['ff_id']) && $array['ff_id']!='') { 
         // if(isset($array['id']) && $array['id']!='') {
            if(isset($array['offset']) && $array['offset']!='') {
              if(isset($array['offset_limit']) && $array['offset_limit']!='') {
                //if(isset($array['sort_by']) && $array['sort_by']!='') {
                  if(isset($array['is_billed']) && $array['is_billed']!='') {
                    if(isset($array['start_date']) && $array['start_date']!='') {
                     // if(isset($array['beat_id']) && $array['beat_id']!='') {


      $valToken = $this->categoryModel->checkCustomerToken($array['sales_token']);

      if($valToken>0)
      {               

            $allprodId = $this->homepage->unBilledskus($array);  


            if(!empty($allprodId['product_id'])){ 

                return Array('status' => 'success', 'message' => 'getUnbilledSKUS', 'data' => $allprodId);
                }else{
                
                return Array('status' => 'failed', 'message' => 'No products SKUs Found', 'data' => []);
            }


      }else{
         return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);    
      }
      /*} else {
         return Array('status' => 'failed', 'message' =>'area code is not sent', 'data' => []);
      }*/
      } else {
         return Array('status' => 'failed', 'message' =>'start date is not sent', 'data' => []);
      }
      } else {
         return Array('status' => 'failed', 'message' =>'is billed is not sent', 'data' => []);
      }
      /*} else {
         return Array('status' => 'failed', 'message' =>'sort id is not sent', 'data' => []);
      }*/
      } else {
         return Array('status' => 'failed', 'message' =>'offset limit is not sent', 'data' => []);
      }
      } else {
         return Array('status' => 'failed', 'message' =>'offset is not sent', 'data' => []);
      }
      /*} else {
         return Array('status' => 'failed', 'message' =>'id is not sent', 'data' => []);
      }*/
      } else {
         return Array('status' => 'failed', 'message' =>'FFid is not sent', 'data' => []);
      }          
   
      } else {
         return Array('status' => 'failed', 'message' =>'Sales token is not sent', 'data' => []);
      }       
}
catch (Exception $e)
{
    $status = "failed";
    $message = "Internal server error";
    $data = [];
    return Array('status' => $status, 'message' => $message, 'data' => $data);
    
}
}  
 

  public function checkInValidation()
  {
    try {
      $array = json_decode($_POST['data'],true);
      // Token Validation
      $valToken = $this->categoryModel->checkCustomerToken($array['customer_token']);
      if($valToken <= 0)
        return ["status" => "session", "message" => "Invalid Customer Token", "data" => []];

      // Inputs
      $salesUserId = $array['sales_user_id'];
      $companyLegalEntityId = $array['sales_legal_entity_id'];
      $customerLegalEntityId = $array['legal_entity_id'];
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return ["status" => "failed", "message" => "Invalid Inputs", "data" => "Internal server error"];      
    }

    // We check the parent and child (company) relations
    $validParentChildRelation = DB::table("legal_entities")
      ->where(["legal_entity_id" => $customerLegalEntityId,"parent_le_id" => $companyLegalEntityId])
      ->count();
      if($validParentChildRelation==0){
        if($customerLegalEntityId == $companyLegalEntityId){
          $validParentChildRelation =1;
        }
      }
      // return $validParentChildRelation;
    // If the field force and retailer doesn`t belong to same company, then we throw them
    if($validParentChildRelation == 0){
      $data= array('display' => 1 );
      return ["status" => "failed", "message" => "You are not allowed to Check In this Retailer", "data" => $data];
    }

    $result = $this->homepage->checkValidRelation($salesUserId,$customerLegalEntityId);

    if($result["status"] == "success")
      return array_merge($result,["message" => "Valid Check In"]);
    // If failed,
    return $result;
  }
  public function getFFPincodeList(){
    $data=Input::all();
    $data=json_decode($data['data']);
    $pincodeList=$this->homepage->getFFPincode($data->user_id);
    return Array('status'=>'success','message'=>'success','data'=>$pincodeList);

  }
   public function updateRetailerPincode(){
    $json = $_POST['data'];
    $decode_data = json_decode($json, true);

    if (isset($decode_data['ff_id']) && !empty($decode_data['ff_id'])){
      if (isset($decode_data['ff_le_id']) && !empty($decode_data['ff_le_id'])){
          if (isset($decode_data['legal_entity_id']) && !empty($decode_data['legal_entity_id'])){
            $updateData=$this->homepage->updateRetailerPincodeData($decode_data);
            if($updateData){
              $data['ff_le_id']=$updateData;
              return Array('status'=>'success','message'=>'updated Successfully','data'=>$data);
            }else{
              return Array('status'=>'failed','message'=>'Error in processing the request','data'=>'');
            }
          }else{
            return Array('status'=>'failed','message'=>'legal_entity_id  is required','data'=>'');
          }
      }else{
      return Array('status'=>'failed','message'=>'ff le id is required','data'=>'');
      }
    }else{
      return Array('status'=>'failed','message'=>'failed','data'=>'');
    }

  }

 
    
  }    


    

?>        