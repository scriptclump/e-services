<?php
    /*
        * File name: categoryController.php
        * Description: categoryController.php file is used to handle the request of categories  and give
        response
        * Author: Ebutor <info@ebutor.com>
        * Copyright: ebutor 2016
        * Version: v1.0
        * Created Date: 24 June 2016
        * Modified Date & Reason:
    */
    namespace App\Modules\Cpmanager\Controllers;
    use DB;
    
    use Session;
    use App\Http\Controllers\BaseController;
    use App\Modules\Cpmanager\Models\CategoryModel;
    use App\Modules\Cpmanager\Models\ProductModel;
    use App\Modules\Cpmanager\Models\Review;
    use App\Modules\Cpmanager\Models\catalog;
    use Illuminate\Support\Facades\Input;
    use App\Central\Repositories\RoleRepo;
    use Response;
    use Log;
    use Lang;
    use Cache;
    use Illuminate\Http\Request;
    use App\Modules\Cpmanager\Models\SearchModel;
    
    define('CACHE_TIME', 60);
    
    class categoryController extends BaseController {
        
        public function __construct() {
            $this->categoryModel = new CategoryModel(); 
            $this->productModel = new ProductModel(); 
            $this->Review = new Review();
            $this->catalog = new catalog();
            $this->_rolerepo = new RoleRepo();
            $this->_search = new SearchModel();
        }
        /*
            * Function name: getCategories
            * Description: getCategories function is used to handle the request of getting all the categories  and give
            response along with the productNo, segment_id and names if no category_id is passed. It returns the products of the category if the categiry_id is passed..
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 24 June 2016
            * Modified Date & Reason:
        */
       public function getCategories() {


        $status = 0;
        $result = array();

        try {

            $beat_id = 0;

            $appKeyData = env('DB_DATABASE');
            $cacheKeyString = $appKeyData;
            if (isset($_POST['data'])) {
                $params = $_POST['data'];

                $params = json_decode($params, true);
            } else {
                $params = "";
            }
            if (isset($params['sort_id']) && !empty($params['sort_id'])) {

                $sort_id = $params['sort_id'];
            } else {
                $sort_id = "";
            }
            if (isset($params['sync_date']) && !empty($params['sync_date'])) {

                $sync_date = $params['sync_date'];
            } else {
                $sync_date = "";
            }

            if (isset($params['le_wh_id']) && !empty($params['le_wh_id'])) {

                $le_wh_id = $params['le_wh_id'];
                $le_wh_id = "'" . $le_wh_id . "'";
                //print_r($le_wh_id);exit;
            } else {
                $error = Lang::get('cp_messages.le_wh_id');
                return Array('status' => 'failed', 'message' => $error, 'data' => []);
            }
            $cust_type = isset($params['customer_type']) ? $params['customer_type'] : '';
            $cacheKeyString .='_' . $cust_type . '_le_wh_id_' . trim($le_wh_id, "'");
            ///print_r($pincode);exit;
            if (isset($params['segment_id']) && !empty($params['segment_id'])) {

                $segment_id = $params['segment_id'];
            } else {
                $error = Lang::get('cp_messages.segment_id');
                return Array('status' => 'failed', 'message' =>$error , 'data' => []);
            }


                
                // to get brand products
                
            if (isset($params['brand_id']) && !empty($params['brand_id'])) {

                $checkBrandId = $this->categoryModel->checkBrandId($params['brand_id']);
                if ($checkBrandId < 1) {
                    $status = "failed";
                    //$message = 'Brand Id not valid.';
                    $message = Lang::get('cp_messages.InvalidBrandId');
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                } else {
                    $brand_id = $params['brand_id'];
                    if (isset($params['offset']) && $params['offset'] != null && $params['offset'] >= 0) {
                        $offset = $params['offset'];
                    } else {
                        $status = "failed";
                        $message = 'Offset not valid.';
                        $data = [];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                    if (isset($params['offset_limit']) && $params['offset_limit'] != null && $params['offset_limit'] >= 0) {
                        $offset_limit = $params['offset_limit'];
                    } else {
                        $status = "failed";
                        $message = 'offset_limit not valid.';
                        $data = [];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                    if (isset($params['customer_token'])) {

                        $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['customer_token']);

                        if ($checkCustomerToken > 0) {
                            $customer_token = $params['customer_token'];
                            $beats = $this->_rolerepo->getBeatByUserId($customer_token);
                            if (empty($beats) or $beats == [] or $beats == null) {
                                return ['status' => "failed", 'message' => "User does not have any Beats assigned", 'data' => []];
                            }
                            $beat_id = trim($beats, "'");
                            $beat_id = str_replace(',', '_', $beat_id);
                        } else {
                            $customer_token = '';
                        }
                    } else {
                        $customer_token = "";
                    }
                }



                if ($brand_id) {
                    if (empty($sort_id)) {
                        $sort_id = -1;
                    }

                    $keyString = $cacheKeyString . '_getbrands_';
                    // Log::info("GET Brands CACHE key -> ".$keyString);
                    $cacheProductList = Cache::get($keyString);
                    // Log::info("GET Brands CACHE res -> ");
                    //Log::info($cacheProductList);
                    if ($cacheProductList != '') {
                        $cacheProductList = json_decode($cacheProductList, true);
                    }


                   /* if (isset($cacheProductList[$beat_id][$brand_id][$offset])) {

                        $productData = $cacheProductList[$beat_id][$brand_id][$offset];
                    } else {*/

                        $blockedList = $this->_search->getBlockedData($customer_token);
                        $productData = $this->productModel->getProductIdsList($brand_id, $offset_limit, $offset, $flag = 1, $blockedList, $cust_type, $le_wh_id);

                        if (!empty($productData) && isset($productData[0])) {
                            $productData = $productData[0];
                        }

                        $cacheProductList[$beat_id][$brand_id][$offset] = $productData;
                        // Log::info("PUT Brands CACHE res -> ");
                        //Log::info($cacheProductList);
                        Cache::put($keyString, json_encode($cacheProductList), CACHE_TIME);
                    //}

                    if (!empty($productData)) {

                        if (is_array($productData)) {
                            $product_id = $productData['product_id'];
                        } else {
                            $product_id = $productData->product_id;
                        }
                        if ($product_id) {
                            $status = "success";
                            $message = 'brandProduct';
                            $data = $productData;
                            //$data['TotalProducts'] = $TotalProducts; 
                            return Array('status' => $status, 'message' => $message, 'data' => $data);
                        } else {

                            $status = "success";
                            $message = 'brandProduct';
                            $data['data'] = ((object) null);
                            //$data['TotalProducts'] ="0";
                            return Array('status' => $status, 'message' => $message, 'data' => $data);
                        }
                    } else {
                        $status = "success";
                        $message = 'brandProduct';
                        $data['data'] = ((object) null);
                        //$data['TotalProducts'] ="0";
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                }
            }



            // to get manufacturer products

            if (isset($params['manufacturer_id']) && !empty($params['manufacturer_id'])) {

                $manufacturer_id = $params['manufacturer_id'];
                if (isset($params['offset']) && $params['offset'] != null && $params['offset'] >= 0) {
                    $offset = $params['offset'];
                } else {
                    $status = "failed";
                    $message = 'Offset not valid.';
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }
                if (isset($params['offset_limit']) && $params['offset_limit'] != null && $params['offset_limit'] >= 0) {
                    $offset_limit = $params['offset_limit'];
                } else {
                    $status = "failed";
                    $message = 'offset_limit not valid.';
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }
                if (isset($params['customer_token'])) {

                    $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['customer_token']);

                    if ($checkCustomerToken > 0) {
                        $customer_token = $params['customer_token'];
                        $beats = $this->_rolerepo->getBeatByUserId($customer_token);
                        if (empty($beats) or $beats == [] or $beats == null) {
                            return ['status' => "failed", 'message' => "User does not have any Beats assigned", 'data' => []];
                        }
                        $beat_id = trim($beats, "'");
                        $beat_id = str_replace(',', '_', $beat_id);
                    } else {
                        $customer_token = '';
                    }
                } else {
                    $customer_token = '';
                }

                if ($manufacturer_id) {

                    if (empty($sort_id)) {
                        $sort_id = -1;
                    }
                    $keyString = $cacheKeyString . '_getmanufacturers_';
                    // Log::info("GET Manuf. CACHE key -> ".$keyString);
                    $cacheProductList = Cache::get($keyString);
                    // Log::info("GET Manuf. CACHE res -> ");
                    //Log::info($cacheProductList);
                    if ($cacheProductList != '') {
                        $cacheProductList = json_decode($cacheProductList, true);
                    }

                    /*if (isset($cacheProductList[$beat_id][$manufacturer_id][$offset])) {

                        $productData = $cacheProductList[$beat_id][$manufacturer_id][$offset];
                    } else {*/

                        $blockedList = $this->_search->getBlockedData($customer_token);

                        $productData = $this->productModel->getProductIdsList($manufacturer_id, $offset_limit, $offset, $flag = 2, $blockedList, $cust_type, $le_wh_id);
                        if (!empty($productData) && isset($productData[0])) {
                            $productData = $productData[0];
                        }
                        $cacheProductList[$beat_id][$manufacturer_id][$offset] = $productData;
                        Cache::put($keyString, json_encode($cacheProductList), CACHE_TIME);
                        // Log::info("PUT Manuf. CACHE res -> ");
                        //Log::info($cacheProductList);
                    //}

                    if (!empty($productData)) {
                        if (is_array($productData)) {
                            $product_id = $productData['product_id'];
                        } else {
                            $product_id = $productData->product_id;
                        }
                        if ($product_id) {
                            $status = "success";
                            $message = 'ManufacturerProduct';
                            $data = $productData;
                            return Array('status' => $status, 'message' => $message, 'data' => $data);
                        } else {

                            $status = "success";
                            $message = 'ManufacturerProduct';
                            $data['data'] = ((object) null);
                            //$data['TotalProducts'] ="0";
                            return Array('status' => $status, 'message' => $message, 'data' => $data);
                        }
                    } else {
                        $status = "success";
                        $message = 'ManufacturerProduct';
                        $data['data'] = ((object) null);
                        //$data['TotalProducts'] ="0";
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                }
            }

            if (isset($params['category_id']) && !empty($params['category_id'])) {
                $checkCategoryId = $this->categoryModel->checkCategoryId($params['category_id']);
                if ($checkCategoryId < 1) {
                    $status = "failed";
                    $message = Lang::get('cp_messages.InvalidCategoryId');
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                } else {
                    $category_id = $params['category_id'];
                    if (isset($params['offset']) && $params['offset'] != null && $params['offset'] >= 0) {
                        $offset = $params['offset'];
                    } else {
                        $status = "failed";
                        $message = Lang::get('cp_messages.InvalidOffset');
                        //$message = 'Offset not valid.';
                        $data = [];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                    if (isset($params['offset_limit']) && $params['offset_limit'] != null && $params['offset_limit'] >= 0) {
                        $offset_limit = $params['offset_limit'];
                    } else {
                        $status = "failed";
                        $message = Lang::get('cp_messages.InvalidLimit');
                        //$message = 'offset_limit not valid.';
                        $data = [];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);
                    }
                    if (isset($params['customer_token'])) {

                        $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['customer_token']);
                        if ($checkCustomerToken > 0) {
                            $customer_token = $params['customer_token'];
                            $beats = $this->_rolerepo->getBeatByUserId($customer_token);
                            if (empty($beats) or $beats == [] or $beats == null) {
                                return ['status' => "failed", 'message' => "User does not have any Beats assigned", 'data' => []];
                            }
                            $beat_id = trim($beats, "'");
                            $beat_id = str_replace(',', '_', $beat_id);
                        } else {
                            $customer_token = '';
                        }
                    } else {
                        $customer_token = '';
                    }
                }
            } else {

                $category_id = '';
            }

            if ($category_id) {
                ///print_r($category_id);exit;
                if (empty($sort_id)) {
                    $sort_id = -1;
                }
                $keyString = $cacheKeyString . '_getcategories_';
                // Log::info("GET Categories CACHE key -> ".$keyString);
                $cacheProductList = Cache::get($keyString);
                // Log::info("GET Categories CACHE res -> ");
                //Log::info($cacheProductList);
                if ($cacheProductList != '') {
                    $cacheProductList = json_decode($cacheProductList, true);
                }

                /*if (isset($cacheProductList[$beat_id][$category_id][$offset])) {

                    $productData = (object) $cacheProductList[$beat_id][$category_id][$offset];
                } else {*/

                    $blockedList = $this->_search->getBlockedData($customer_token);

                    $productData = $this->productModel->getProductIdsList($category_id, $offset_limit, $offset, $flag = 3, $blockedList, $cust_type, $le_wh_id);


                    if (!empty($productData) && isset($productData[0])) {
                        $productData = $productData[0];
                    }
                    $cacheProductList[$beat_id][$category_id][$offset] = $productData;
                    Cache::put($keyString, json_encode($cacheProductList), CACHE_TIME);
                    // Log::info("PUT Categories CACHE res -> ");
                    //Log::info($cacheProductList);
                //}

                if (!empty($productData)) {
                    if (is_array($productData)) {
                        $product_id = $productData['product_id'];
                    } else {
                        $product_id = $productData->product_id;
                    }

                    if ($product_id) {

                        $status = "success";
                        $message = 'categoryProduct';
                        $data['product_id'] = $productData->product_id;
                        $data['count'] = $productData->count;
                        $data['is_subcategory'] = "-1";
                        // $data['TotalProducts'] = $TotalProducts; 
                    } else {
                        $status = "success";
                        $message = 'categoryProduct';
                        $data['data'] = ((object) null);
                    }
                } else {
                    $status = "success";
                    $message = 'categoryProduct';

                    $data['data'] = ((object) null);
                }

                //}
            } else {
                if($sync_date==""){
                    $sync_date='2015-01-01';
                }
                /*$keyString = 'categories_'.$sync_date. $segment_id . '_le_wh_id_' . trim($le_wh_id, "'");
                $categoryList = Cache::get($keyString);
                if (!empty($categoryList)) {
                    $data = $categoryList;
                } else {*/                    
                    $result = $this->categoryModel->getCategories($segment_id, $le_wh_id,$sync_date);
                    $data=array();
                    $data['created']=array();
                    $data['updated']=array();
                    foreach ($result as $key => $value) {
                        if($value->created_at >= $sync_date){
                            unset($value->created_at);
                            unset($value->updated_at);
                            $data['created'][]=$value;
                        }else if($value->updated_at >= $sync_date){
                            unset($value->created_at);
                            unset($value->updated_at);
                            $data['updated'][]=$value;
                        }
                    }
                    /*Cache::put($keyString, $data, CACHE_TIME);
                }*/
                $status = "success";
                $message = 'getCategories';
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            $data = [];
        }
        return Array('status' => $status, 'message' => $message, 'data' => $data);
        // json_decode(str_replace('null', 0, (json_encode($data))));
    }

    /*
            * Function name: productDetails
            * Description: productDetails function is used to handle the request of getting the product related data
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 1 July 2016
            * Modified Date & Reason:
        */
        public function productDetails(){
            
            $status = 0;
            $result = array();
            try
            {
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    if(!empty($params)){
                        if(isset($params['product_id']) && !empty($params['product_id'])){
                            $checkProductId = $this->categoryModel->checkProductId($params['product_id']);  
                            if($checkProductId<1){
                                $status = "failed";
                                $message = "Invalid product details.";
                                $data = [];
                                return Array('status' => $status, 'message' => $message, 'data' => $data);  
                                }else{
                                $product_id= $params['product_id'];
                                if(isset($params['customer_token']) && !empty($params['customer_token'])){
                                    $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['customer_token']);
                                    if($checkCustomerToken<1){
                                        $customer_token="";
                                        }else{
                                        $customer_token = $params['customer_token'];
                                    }
                                    }else{
                                    $customer_token = "";
                                }
                            }
                            }else{
                            $status = "failed";
                            $message = "Invalid product details.";
                            $data = [];
                            return Array('status' => $status, 'message' => $message, 'data' => $data); 
                        }
                        
                        }else{
                        $status = "failed";
                        $message = "Required parameter not passed.";
                        $data = [];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);   
                    }

                    if(isset($params['segment_id']) && !empty($params['segment_id'])){
                    
                    $segment_id = $params['segment_id'];
                    
                    }else{
                        $error = Lang::get('cp_messages.segment_id');
                    return Array('status' => 'failed', 'message' =>  $error, 'data' =>[]);
                } 

               /* if(isset($params['pincode']) && !empty($params['pincode'])){
                    
                    $pincode = $params['pincode'];
                    
                    }else{
                    
                    return Array('status' => 'failed', 'message' => 'segment_id Not passed', 'data' =>[]);
                }*/

                if(isset($params['le_wh_id']) && !empty($params['le_wh_id'])){
                    
                    $le_wh_id = $params['le_wh_id'];
                    $le_wh_id = "'".$le_wh_id."'";
                    
                    }else{
                    
                    return Array('status' => 'failed', 'message' => 'Warehouse details are missing.', 'data' =>[]);
                }

                    }else{
                    $status = "failed";
                    $message = "Required parameter not passed.";
                    $data = [];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }
                
                if($product_id){
                    $temp = array();
                    $temp =  $this->productModel->getProductDetails($product_id,$offset='',$offset_limit='',$sort_id='',$customer_token,$api=2,$prodData='',$le_wh_id,$segment_id);
                    // $variants = $this->categoryModel->getVariantData($product_id);
                    
                    // $productData = $this->model_product_getcategories->getProductData($dataprod);
                    
                    /*  $variants = json_decode(json_encode($variants),true);
                        $j=0;
                        //Pack Loop
                        foreach ($variants as $variant) { 
                        if(!empty($customer_token)){
                        $packdata = $this->categoryModel->getPackData($variant['variant_id']);
                        }else
                        {
                        $packdata = $this->categoryModel->getGuestPackData($variant['variant_id']);
                        }
                        
                        
                        $variants[$j]['pack'] = $packdata;
                        $variants[$j]['images'] = $this->categoryModel->getVariantImages($variant['variant_id']);
                        $attributes = $this->categoryModel->Attributes($variant['variant_id']);
                        $key = array_column($attributes,'attribute_name');
                        $value= array_column($attributes,'value');
                        $variants[$j]['specifications']= array_combine($key, $value);
                        if(empty($variants[$j]['specifications'])){
                        $key= array('-1');
                        $value = array('No specifications');
                        
                        $variants[$j]['specifications'] = array_combine($key, $value);
                        }
                        $j++;
                        
                        
                    }*/
                    
                }
                
                /*$temp['product_id'] = $product_id;
                    //$temp['name'] = $variants['product_name'];
                    $temp['rating'] = $this->Review->Review($product_id);
                    //$temp['review'] = $productData['review'];
                    //$temp['description'] = $productData['description'];
                    
                $temp['variants'] = $variants['data'];*/
                //$reviews= $this->model_product_productDetails->productReviews($product_id);
                //$related_products= $this->model_product_productDetails->RelatedProducts($product_id);
               /* $array = array();
                
                if(isset($temp['data'][0]) && !empty($temp['data'][0])){
                    $array = $temp['data'][0];  
                    }else{
                    $array = ((object) null);
                }
                */
                //$array['reviews'] = [];
                // $array['related_products'] = [];
                $data['data'] = (object)($temp);;
                $status = "success";
                $message = "productDetails";
                
                //print_r(str_replace('null', 0, (json_encode($data))));exit;
                return Array('status' => $status, 'message' => $message, 'data' => json_decode(str_replace('null', 0, (json_encode($data)))));
                
                
            }
            catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);
                
            }
            
            
            
        }
        
        
        public function getOfferProducts(){
            try
            {
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }
                // $this->Review->topRated($dataprod->product_id);
                if(isset($params['customer_token']) && !empty($params['customer_token'])){
                    
                    $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['customer_token']);
                    //print_r($checkCustomerToken);exit;
                    if($checkCustomerToken>0){
                        $customer_token = $params['customer_token'];
                        $le_wh_id = $params['le_wh_id'];
                        $le_wh_id = "'".$le_wh_id."'";
                        
                        }else{
                        $customer_token ='';
                        if(isset($params['le_wh_id'])){
                            $le_wh_id = $params['le_wh_id'];   
                            $le_wh_id = "'".$le_wh_id."'";
                            }else{
                                $message=Lang::get('cp_messages.le_wh_id');
                            return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                        }
                        /*if(isset($params['le_wh_id'])){
                            $pincode = $params['le_wh_id'];   
                            }else{
                            return Array('status' => 'failed', 'message' => 'le_wh_id Not valid', 'data' => []);  
                        }*/
                        
                    }
                    }else{
                    $customer_token = "";
                    if(isset($params['le_wh_id'])){
                        $le_wh_id = $params['le_wh_id'];
                        $le_wh_id = "'".$le_wh_id."'";   
                        }else{
                            $message=Lang::get('cp_messages.le_wh_id');
                        return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                    }
                  
                }
                
                if(isset($params['segment_id']) && !empty($params['segment_id'])){
                    $segment_id =  "'".$params['segment_id']."'";
                    }else{

                        $message=Lang::get('cp_messages.segment_id');
                    return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                }
                
                if(isset($params['sort_id']) && !empty($params['sort_id'])){
                    
                    $sort_id = $params['sort_id'];
                    
                    }else{
                    $sort_id="-1";
                } 
              
                
                if(isset($params['offset']) && $params['offset'] !=null && $params['offset'] >= 0){
                    $offset = $params['offset'];
                }else
                {
                    $status = "failed";
                    $message=Lang::get('cp_messages.InvalidOffset');
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }
                if(isset($params['offset_limit']) && $params['offset_limit'] !=null && $params['offset_limit'] >= 0){
                    $offset_limit = $params['offset_limit'];
                    }else{
                    $status = "failed";
                    $message=Lang::get('cp_messages.InvalidLimit');
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }   
                
                if(isset($params['flag'])){
                    
                    /*if( ($params['flag']==55001) ||($params['flag']==55003)||($params['flag']==55004) ){*/
                        $flag = $params['flag'];
                        if($flag==55004){
                            
                            $allprodId = $this->Review->gettopRated($offset,$offset_limit,$segment_id);
                            if(isset($allprodId['data'])){
                                $allprodId = array_values(array_unique(array_column($allprodId['data'],'entity_id'))); 
                                $temp = $this->catalog->getProducts($category_id='',$offset,$offset_limit,$sort_id='',$customer_token,$api=3,$allprodId,$le_wh_id,$segment_id);  
                                }else{
                                $allprodId = [];
                                $temp =[];
                            }
                            
                             
                            
                            $allprodId['TotalProducts'][0]['@total'] = sizeof($allprodId);
                            }else{
                            $blockedList = $this->_search->getBlockedData($customer_token); 
                            $cust_type = isset($params['customer_type'])?$params['customer_type']:0;
                            $allprodId = $this->productModel->getProductsByKPI($flag,$offset,$offset_limit,$segment_id,$le_wh_id,$sort_id,$blockedList,$cust_type); 
                            
                        }
                        //print_r($allprodId);exit;
                        
                        if(!empty($allprodId)){ 
                            
                      
                            $totalProd = json_decode(json_encode($allprodId),true);
                            if(!isset($totalProd['TotalProducts'][0]['@total'])){
                                $prdcnt = 0;
                                }else{
                                $prdcnt =$totalProd['TotalProducts'][0]['@total'];
                            }
                            $finalResult['count']= $prdcnt;
                            
                     
                           $t = array_column(array_values($totalProd['data']),'product_id');
                          
                           $finalResult['product_id'] = implode(",",$t);

                            return Array('status' => 'success', 'message' => 'getOfferProducts', 'data' => $finalResult);
                            }else{
                            
                            return Array('status' => 'failed', 'message' => 'No products Found', 'data' => []);
                        }
                  /*      }else{
                        $status = "failed";
                        $message = 'Flag not valid.';
                        $data=[];
                        return Array('status' => $status, 'message' => $message, 'data' => $data);   
                    }
                    */
                    }else{
                    $status = "failed";
                    $message = 'Flag not sent.';
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
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
        
        
        
        public function addReviewRating(){
            try{
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }
                //print_r($params);exit;
                if(isset($params['customer_token'])){
                    
                    $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['customer_token']);
                    // print_r($checkCustomerToken);
                    if($checkCustomerToken>0){
                        $customer_token = $params['customer_token'];
                        }else{
                            $message=Lang::get('cp_messages.InvalidCustomerToken');
                        return Array('status' => 'session', 'message' => $message, 'data' => []);
                    }
                    }else{
                        $message=Lang::get('cp_messages.InvalidCustomerToken');
                    return Array('status' => 'session', 'message' => $message, 'data' => []);
                }
                $user_id = $this->categoryModel->getUserId($customer_token);
                
                $data = $this->Review->addReviewRating($user_id[0]->user_id,$params,$user_id[0]->firstname,$user_id[0]->lastname);
                //print_r($data);exit;
                if($data['status']==1){
                    return Array('status' => 'success', 'message' => 'addReviewRating', 'data' => $data);
                    }else{
                        $message = Lang::get('cp_messages.AlreadyRated');
                    return Array('status' => 'failed', 'message' => $message, 'data' => []);
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
            * Function name: getProductSlabs
            * Description: getProductSlabs function is used to fetch the slab rates of the product_id passed.
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 29 August 2016
            * Modified Date & Reason:
        */
    public function getProductSlabs() {
        try {
            if (isset($_POST['data'])) {
                $params = $_POST['data'];
                $params = json_decode($params, true);
            } else {
                $params = "";
            }
            if (isset($params['product_id'])) {
                $checkProductId = $this->categoryModel->checkProductId($params['product_id']);
                // print_r($checkCustomerToken);
                if ($checkProductId > 0) {
                    $product_id = $params['product_id'];
                } else {
                    $message = Lang::get('cp_messages.InvalidProductId');
                    return Array('status' => 'failed', 'message' => $message, 'data' => []);
                }
            } else {
                $message = Lang::get('cp_messages.InvalidProductId');
                return Array('status' => 'failed', 'message' => $message, 'data' => []);
            }
            if (isset($params['customer_token']) && !empty($params['customer_token'])) {
                $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['customer_token']);
                if ($checkCustomerToken > 0) {
                    $user_id = $this->categoryModel->getUserId($params['customer_token']);
                    $user_id = $user_id[0]->user_id;
                } else {
                    $user_id = 0;
                    $message = Lang::get('cp_messages.InvalidCustomerToken');
                    return Array('status' => 'session', 'message' => $message, 'data' => []);
                }
            } else {
                $user_id = 0;
                //return Array('status' => 'failed', 'message' => 'Invalid customer_token', 'data' => []);
            }
            if (isset($params['le_wh_id']) && !empty($params['le_wh_id'])) {
                $le_wh_id = $params['le_wh_id'];
                $le_wh_id = "'" . $le_wh_id . "'";
            } else {
                $message = Lang::get('cp_messages.le_wh_id');
                return Array('status' => 'failed', 'message' => $message, 'data' => []);
            }
            //$data = DB::select("CALL getProductSlabs($product_id,$le_wh_id,$user_id)");
            $cust_type = isset($params['customer_type']) ? $params['customer_type'] : 'NULL';
            $data = $this->productModel->getPricing($product_id, $le_wh_id, $user_id, $cust_type);
            return Array('status' => 'success', 'message' => 'getProductSlabs', 'data' => $data);
        } catch (\Exception $e) {
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }

    /*
        * getOfflineProducts
        * 
        * 
        * 
        * 
        */
       public function getOfflineProducts(){

        if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    $params= json_decode($params,true);     
               

/*if(isset($params['offset']) && $params['offset'] !=null
                         && $params['offset'] >= 0){
                            $offset = $params['offset'];
                        }else
                        {
                            $status = "failed";
                            $message = 'Offset not valid.';
                            $data=[];
                            return Array('status' => $status, 'message' => $message, 'data' => $data);
                        }

                        if(isset($params['offset_limit']) 
                          && $params['offset_limit'] !=null && $params['offset_limit'] >= 0){
                            $offset_limit = $params['offset_limit'];
                            }else{
                            $status = "failed";
                            $message = 'offset_limit not valid.';
                            $data=[];
                            return Array('status' => $status, 'message' => $message, 'data' => $data);
                        }*/

             if(isset($params['le_wh_id']) && !empty($params['le_wh_id']))
             {
              $le_wh_id = $params['le_wh_id'];
              $le_wh_id = "'".$le_wh_id."'";
                }else{
            
             $le_wh_id = '1';

                }       
   
                if(isset($params['product_ids'])){
                            $product_ids= $params['product_ids'];
                             $product_ids = "'".$product_ids."'";
                            }else{
                           $message = Lang::get('cp_messages.InvalidProductId');
                            return Array('status' => "failed", 'message' => $message, 'data' => []);
                        }
    $cust_type = isset($params['customer_type'])?$params['customer_type']:'';
    $data = $this->productModel->getProducts($product_ids,$le_wh_id,$cust_type);

   
    if (!empty($data))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "MobileProd",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => $data
      ));
      }

        }else{
            
            $error = Lang::get('cp_messages.InvalidInput');
            print_r(json_encode(array('status'=>"failed",'message'=>$error,'data'=>"")));die;

                       }
    }

    /*
    * Function name: getMediaDesc
    * Description: used to get inventory
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28th Sept 2016
    * Modified Date & Reason:
    */
   public function getMediaDesc(){
        try{
           
           if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }

                if(isset($params['product_id']) && !empty($params['product_id'])){
                 $checkProductId = $this->categoryModel->checkProductId($params['product_id']);  
                            if($checkProductId<1){
                                $status = "failed";
                                //$message = "product_id is invalid";
                                $message = Lang::get('cp_messages.InvalidProductId');
                                $data = [];
                                return Array('status' => $status, 'message' => $message, 'data' => $data);  
                                }else{
                                    $product_id = $params['product_id'];
                                    $getMedia['images'] = $this->productModel->getMedia($product_id);
                                    $description['description'] = $this->productModel->getDescription($product_id);
                                    $getMediaDesc = array_merge($getMedia,$description);
                                    return Array('status'=>'success','message'=>'getMediaDesc','data'=>$getMediaDesc);
                                }   

                }else{
                    $error = Lang::get('cp_messages.InvalidInput');
                   //$error = "Please pass required parameters";
            return array('status'=>"failed",'message'=>$error,'data'=>object(null)); 
                }
                



        }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
            }
    }

    /*
    * Function name: getReviewSpec
    * Description: used to get inventory
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 28th Sept 2016
    * Modified Date & Reason:
    */
    public function getReviewSpec(){

        try{
           
           if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }

                if(isset($params['product_id']) && !empty($params['product_id'])){
                 $checkProductId = $this->categoryModel->checkProductId($params['product_id']);  
                            if($checkProductId<1){
                                $status = "failed";
                                $message = Lang::get('cp_messages.InvalidProductId');
                                $data = [];
                                return Array('status' => $status, 'message' => $message, 'data' => $data);  
                                }else{
                                    $product_id = $params['product_id'];
                                    $specifications['specifications'] = $this->productModel->getProductSpecifications($product_id);

                                    $reviews['reviews'] = $this->Review->getReviews($product_id);
                                    
                                    $getMediaDesc = array_merge($specifications,$reviews);
                                    return Array('status'=>'success','message'=>'getMediaDesc','data'=>$getMediaDesc);
                                }   

                }else{
                   $error = Lang::get('cp_messages.InvalidInput');
            return array('status'=>"failed",'message'=>$error,'data'=>object(null)); 
                }
                



        }catch (Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
            }
    }

    /*




    */


    public function getMustSkuProductsList(){
            try
            {
                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }
                if(isset($params['customer_token']) && !empty($params['customer_token'])){
                    
                    $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['customer_token']);
                    if($checkCustomerToken>0){
                        $customer_token = $params['customer_token'];
                        $le_wh_id = $params['le_wh_id'];
                        $le_wh_id = "'".$le_wh_id."'";
                        
                        }else{
                        $customer_token ='';
                        if(isset($params['le_wh_id'])){
                            $le_wh_id = $params['le_wh_id'];   
                            $le_wh_id = "'".$le_wh_id."'";
                            }else{
                                $message=Lang::get('cp_messages.le_wh_id');
                            return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                        }
                        
                    }
                    }else{
                    $customer_token = "";
                    if(isset($params['le_wh_id'])){
                        $le_wh_id = $params['le_wh_id'];
                        $le_wh_id = "'".$le_wh_id."'";   
                        }else{
                            $message=Lang::get('cp_messages.le_wh_id');
                        return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                    }
                  
                }
                
                if(isset($params['segment_id']) && !empty($params['segment_id'])){
                    $segment_id =  "'".$params['segment_id']."'";
                    }else{

                        $message=Lang::get('cp_messages.segment_id');
                    return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                }
                
                if(isset($params['sort_id']) && !empty($params['sort_id'])){
                    
                    $sort_id = $params['sort_id'];
                    
                    }else{
                    $sort_id="-1";
                } 
              
                
                if(isset($params['offset']) && $params['offset'] !=null && $params['offset'] >= 0){
                    $offset = $params['offset'];
                }else
                {
                    $status = "failed";
                    $message=Lang::get('cp_messages.InvalidOffset');
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }
                if(isset($params['offset_limit']) && $params['offset_limit'] !=null && $params['offset_limit'] >= 0){
                    $offset_limit = $params['offset_limit'];
                    }else{
                    $status = "failed";
                    $message=Lang::get('cp_messages.InvalidLimit');
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
                }   
                
                if(isset($params['flag'])){
                    
                       $flag = $params['flag'];
                        if($flag==180001){
                            
                            $blockedList = $this->_search->getBlockedData($customer_token); 
                            $cust_type = isset($params['customer_type'])?$params['customer_type']:0;
                            $allprodId = $this->productModel->getCPMustSkuList($flag,$offset,$offset_limit,$segment_id,$le_wh_id,$sort_id,$blockedList,$cust_type); 
                            
                        }
                        //print_r($allprodId);exit;
                        
                        if(!empty($allprodId)){ 
                            
                      
                            $totalProd = json_decode(json_encode($allprodId),true);
                            if(!isset($totalProd['TotalProducts'][0]['@total'])){
                                $prdcnt = 0;
                                }else{
                                $prdcnt =$totalProd['TotalProducts'][0]['@total'];
                            }
                            $finalResult['count']= $prdcnt;
                            
                     
                           $t = array_column(array_values($totalProd['data']),'product_id');
                          
                           $finalResult['product_id'] = implode(",",$t);

                            return Array('status' => 'success', 'message' => 'getSKUProducts', 'data' => $finalResult);
                            }else{
                            
                            return Array('status' => 'failed', 'message' => 'No products Found', 'data' => []);
                        }
                    }else{
                    $status = "failed";
                    $message = 'Flag not sent.';
                    $data=[];
                    return Array('status' => $status, 'message' => $message, 'data' => $data);
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

        public function getCustomPackData(){
            try{

                if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    $params= json_decode($params,true);
                    if(isset($params['customer_token']) && !empty($params['customer_token'])){
                        $checkCustomerToken = $this->categoryModel->getCustomerIdByToken($params['customer_token']);
                        if(count($checkCustomerToken) > 0){
                            if(isset($params['le_wh_id']) && !empty($params['le_wh_id'])){
                                if(isset($params['customer_type']) && !empty($params['customer_type'])){
                                    if(isset($params['segment_id']) && !empty($params['segment_id'])){
                                        if(isset($params['pack_id']) && !empty($params['pack_id'])){
                                                $pack_id = $params['pack_id'];
                                                $cusPackData = $this->categoryModel->getCustomPackDataById($pack_id);
                                                $cusPackData = json_decode(json_encode($cusPackData),1);
                                                if(count($cusPackData)){
                                                    $cust_type =  $params['customer_type'];
                                                    $cust_id =  $checkCustomerToken->user_id;
                                                    $all_packs_data = [];
                                                    $cart_data = [];
                                                    $product_packs = array();
                                                    $loop = 0;
                                                    foreach ($cusPackData as $key => $value) {
                                                        # code...
                                                        $data = array();
                                                        $product_id = $value['product_id'];
                                                        $le_wh_id =  $value['le_wh_id'];
                                                        $data = $this->productModel->getProducts($product_id,$le_wh_id,$cust_type);

                                                        if(isset($data['data']) && count($data['data'])){
                                                            foreach ($data['data'] as $inkey => $invalue) {
                                                                $product_id = $invalue->product_id;
                                                                $product_packs = $this->productModel->getPricing($product_id,$le_wh_id,$cust_id,$cust_type);
                                                                $product_packs = json_decode(json_encode($product_packs),1);
                                                                $all_packs = array();
                                                                $req_qty = $value['qty'];
                                                                if(count($product_packs)){
                                                                    // $all_packs = $this->createPacksByQty($product_packs,$req_qty,$cust_id);
                                                                }
                                                                $packs = array();

                                                                $product_data[$loop]['product_data'] = $invalue;
                                                                $product_data[$loop]['product_data']->packs = $product_packs;
                                                                $product_data[$loop]['product_data']->min_qty = $req_qty;
                                                                $product_data[$loop]['cart_data'] = [];
                                                                $loop++;
                                                            }
                                                        }
                                                        // else{                                             
                                                        //     $product_data[$loop] = [];
                                                        //     $product_data[$loop]['product_data'] = [];
                                                        //     $product_data[$loop]['product_data']['product_id'] = $product_id;
                                                        //     $product_data[$loop]['product_data']['packs'] = [];
                                                        //     $product_data[$loop]['product_data']['min_qty'] = 0;
                                                        //     $product_data[$loop]['cart_data'] = [];
                                                        //     $loop++;
                                                        // }
                                                        
                                                    }
                                                    return Array('status' => 'success', 'message' => "getCustomPackData", 'data' => $product_data);  

                                                }else{
                                                    $status = "failed";
                                                    $message = "Invalid Pack Id.";
                                                    $data = [];
                                                    return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                                                }


                                        }else if(isset($params['product_id']) && !empty($params['product_id'])){
                                
                                        }else{
                                            $status = "failed";
                                            $message = "Please send Product or Pack Id.";
                                            $data = [];
                                            return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                                        }
                                    }else{
                                        $status = "failed";
                                        $message = "Please send Segment Id.";
                                        $data = [];
                                        return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                                    }
                                }else{
                                    $status = "failed";
                                    $message = "Please send Customer Type.";
                                    $data = [];
                                    return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                                }
                            }else{
                                $status = "failed";
                                $message = "Please send warehouse.";
                                $data = [];
                                return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                            }
                            
                        }else{
                            $status = "session";
                            $message = "Invalid Token.";
                            $data = [];
                            return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                            
                        }
                    }else{
                        
                        $status = "failed";
                        $message = "Please send token.";
                        $data = [];
                        return Array('status' => 'failed', 'message' => $message, 'data' => []);  
                    }
                }
            }catch(\Exception $ex){
                Log::info($ex);
                $status = "failed";
                $message = $ex->getMessage();
                $data = [];
                return Array('status' => $status, 'message' => $message, 'data' => $data);            }
        }
        
        public function createPacksByQty($product_packs,$req_qty,$cust_id){
            array_multisort( array_column($product_packs, "pack_qty"), SORT_DESC, $product_packs );
            $cart_packs_array = [];
//            Log::info($req_qty);
            foreach ($product_packs as $inkey => $invalue) {
                
                if($invalue['pack_qty'] <= $req_qty && $req_qty > 0 ){
                    $pack_mult = floor($req_qty/$invalue['pack_qty']);
                    $product_packs[$inkey]['pack_match_qty'] = $pack_mult;
                    //Log::info("pack_mult".$pack_mult);
                    //Log::info("unit_price".$product_packs[$inkey]['unit_price']);
                    $product_packs[$inkey]['total'] = $product_packs[$inkey]['unit_price'] * $pack_mult;
                    //Log::info("total----".$product_packs[$inkey]['total']);
                    if($req_qty > 0)
                        $cart_packs_array[] = array(
                                        "cashBackIds" => "",
                                        "customerId" => $cust_id,
                                        "esu" => $invalue['esu'],
                                        "level" => $invalue['pack_level'],
                                        "packQty" => $invalue['pack_qty'],
                                        "packSize" => $invalue['pack_size'],
                                        "productId" => $invalue['product_id'],
                                        "qty" => $req_qty,
                                        "star" => $invalue['star']
                                    );
                    $req_qty -= $pack_mult * $invalue['pack_qty'];
                }
            }
            if($req_qty > 0){
                // means pack configuration not matching for req qty
                return [];
            }
            return array("cart_packs_array"=>$cart_packs_array);
        }
    
    }           