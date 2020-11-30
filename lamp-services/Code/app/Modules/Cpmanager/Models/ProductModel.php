<?php
  namespace App\Modules\Cpmanager\Models;
  use \DB;
  use Cache;
  use Caching;
  use Log;
  use App\Modules\Cpmanager\Models\EcashModel;
  
  class ProductModel extends \Eloquent {

  public function __construct() {  
      $this->Review = new Review();  
      $this->_ecash = new EcashModel();
    }  

//Returns only productIds based on search criteria.
//Works with category_id, brand_id, manufacturer_id   
    public function getProductIdsList($object_id, $limit, $offset, $flag, $blockedList, $cust_type, $le_wh_id) {
        $brands = isset($blockedList['brands']) ? $blockedList['brands'] : 0;
        $manf = isset($blockedList['manf']) ? $blockedList['manf'] : 0;
        
        if (is_array($brands)) {
            $brands = implode(',', $brands);
        }
        if (is_array($manf)) {
            $manf = implode(',', $manf);
        }
        $le_wh_id = trim($le_wh_id,"'");
        //$result = DB::selectFromWriteConnection(DB::raw("CALL getCpProductsIdsList($object_id,$limit,$offset,$flag,'".$brands."','".$manf."')"));
        $result = DB::selectFromWriteConnection(DB::raw("CALL  getCpProductsIdsList_ByCust($object_id,$limit,$offset,$flag,0,0,$cust_type,'$le_wh_id')"));
        return $result;
    }

//returns the product Data along with variants and parent child relationship
public function getProducts($product_ids,$le_wh_id,$cust_type=''){
    if ($cust_type == 3016) {
            $result['data'] = DB::select("CALL  getCpProductsDIT($product_ids,$le_wh_id)");
        } else {
            $result['data'] = DB::select("CALL  getCpProducts($product_ids,$le_wh_id)");
        }
        return $result; 
}
 public function getProductDetails($product_id,$offset,$offset_limit,$sort_id,$customer_token='',$api='',$prodData,$pincode,$segment_id){//mandatory product_id, customer_token can be null ,$pincode, segment_id
    $productDatas= array();
   $productDatas['product_id'] = $product_id;
    
    $product_name= DB::table('products as prod')
    ->select(DB::raw("prod.product_title as product_name, pc.description,prod.primary_image,prod.esu,prod.mrp"))
    
    ->leftJoin('product_content as pc','pc.product_id','=','prod.product_id')
    ->where('prod.product_id','=',$product_id)
    ->get()->all();
    $productDatas['product_name'] = $product_name[0]->product_name;
    $productDatas['description'] = $product_name[0]->description;
    $productDatas['mrp'] = $product_name[0]->mrp;
    $productDatas['rating'] = $this->Review->Review($product_id);
    $productDatas['reviews'] = $this->Review->getReviews($product_id);
    $productDatas['related_products'] = [];
    $productDatas['image'] = $product_name[0]->primary_image;
    $productDatas['primary_image'] = $product_name[0]->primary_image;
    $productDatas['images'] = $this->getMedia($product_id);
    $productDatas['esu'] = $product_name[0]->esu;
    //print_r($productDatas);exit;
    
    $inventory =0;  
    // if($product['product_id']==4812){    
    $childProducts = $this->getChildProduct($product_id);
    $childProducts = json_encode($childProducts);
    $childProducts = json_decode($childProducts,true);
    $variantname1 = array_values(array_unique(array_column($childProducts,'variant_value1')));

    if(!empty($variantname1[0])){
            $variantsize1 = sizeof($variantname1);
      }else{
            $variantsize1 = 0;
    }
    $variantname2 = array_values(array_unique(array_column($childProducts,'variant_value2')));
    if(!empty($variantname2[0])){
            $variantsize2 = sizeof($variantname2);
      }else{
    $variantsize2 = 0;
    }
    $variantname3 = array_values(array_unique(array_column($childProducts,'variant_value3')));
    if(!empty($variantname3[0])){
    $variantsize3 = sizeof($variantname3);
    }else{
    $variantsize3 = 0;
    }


    if($variantsize1 > 0){
      for($i=0;$i<$variantsize1;$i++){
        $k=0;
      //$l=0;
      $x=0;
    foreach($childProducts as $childProduct){

    $inventory = $this->getInventory($childProduct['product_id'],$pincode,$segment_id);
      if($variantname1[$i]==$childProduct['variant_value1']){

     $productDatas['variants'][$i]['variant_name'] = $childProduct['variant_value1'];
     // $productDatas['variants'][$i]['has_inner_varients'] = 1;
      $productDatas['variants'][$i]['product_id'] = $childProduct['product_id'];
      $productDatas['variants'][$i]['product_name'] = $childProduct['product_title'];
      $productDatas['variants'][$i]['quantity'] = $inventory;
      $productDatas['variants'][$i]['description'] =  $this->getDescription($childProduct['product_id']);
      $productDatas['variants'][$i]['mrp'] = $childProduct['mrp'];
      $productDatas['variants'][$i]['image'] = $childProduct['primary_image'];
      $productDatas['variants'][$i]['images'] = $this->getMedia($childProduct['product_id']);
      $spec = $this->getProductSpecifications($childProduct['product_id']);
      $productDatas['variants'][$i]['specifications'] = $spec;
      $Reviews = $this->Review->getReviews($childProduct['product_id']);
      $productDatas['variants'][$i]['reviews'] = $Reviews;
      $productDatas['variants'][$i]['rating'] = $this->Review->Review($product_id);
      $productDatas['variants'][$i]['esu'] = $childProduct['esu'];
      //$productDatas['variants'][$i]['has_inner_varients'] = 1;

      if(empty($childProduct['variant_value2'])){
      $productDatas['variants'][$i]['has_inner_varients'] = 0;
    //  $productDatas['variants'][$i]['packs'] = [];
      $productDatas['variants'][$i]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
      
      }else{

      for($y=0;$y<$variantsize2;$y++){

        $productDatas['variants'][$i]['has_inner_varients'] = 1;
      if($variantname2[$y]==$childProduct['variant_value2']){

      $productDatas['variants'][$i]['variants'][$k]['product_id'] = $childProduct['product_id'];
      $productDatas['variants'][$i]['variants'][$k]['variant_name'] = $childProduct['variant_value2'];
      $productDatas['variants'][$i]['variants'][$k]['product_name'] = $childProduct['product_title'];
      $productDatas['variants'][$i]['variants'][$k]['quantity'] = $inventory;
      $productDatas['variants'][$i]['variants'][$k]['description'] = $this->getDescription($childProduct['product_id']);
      $productDatas['variants'][$i]['variants'][$k]['mrp'] = $childProduct['mrp'];
      $productDatas['variants'][$i]['variants'][$k]['image'] = $childProduct['primary_image'];
      $productDatas['variants'][$i]['variants'][$k]['images'] = $this->getMedia($childProduct['product_id']);
                                                                 
      $productDatas['variants'][$i]['variants'][$k]['specifications'] = $this->getProductSpecifications($childProduct['product_id']);

      $productDatas['variants'][$i]['variants'][$k]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
      $productDatas['variants'][$i]['variants'][$k]['rating'] = $this->Review->Review($product_id);
      $productDatas['variants'][$i]['variants'][$k]['esu'] = $childProduct['esu'];
  
        if(empty($childProduct['variant_value3'])){
        $productDatas['variants'][$i]['variants'][$k]['has_inner_varients']=0;
        //$productDatas['variants'][$i]['variants'][$k]['packs'] = [];
        $productDatas['variants'][$i]['variants'][$k]['packs'] =  $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
        }else{
        
        $l=0;
        for($z=0;$z<$variantsize3;$z++){
        
        if($variantname3[$z]==$childProduct['variant_value3']){
          $productDatas['variants'][$i]['variants'][$k]['has_inner_varients'] = 1;
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['product_id'] = $childProduct['product_id'];
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['variant_name'] = $childProduct['variant_value3']; 
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['product_name'] = $childProduct['product_title'];
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['quantity'] = $inventory;
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['description'] = $this->getDescription($childProduct['product_id']);
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['mrp'] = $childProduct['mrp'];
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['image'] = $childProduct['primary_image'];
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['images'] = $this->getMedia($childProduct['product_id']);
                                                                   
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['specifications'] = $this->getProductSpecifications($childProduct['product_id']);
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['esu'] =  $childProduct['esu'];
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['rating'] = $this->Review->Review($product_id);
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['has_inner_varients'] = 0;
          $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
         //$productDatas['variants'][$i]['variants'][$k]['variants'][$l]['packs'] = []; 
         $l++;
          }

          }
          
          }
          $k++;
        }

          }
          
          }

          $x++;
          
          }//variantname1 condition

          }//$i increment
          }//child product foreach loop
          } //variantsize1 not empty condition
   
          return $productDatas;
 
        }  
    
//returns the product Details along with images attributes reviews ratings specification in an multivariant array.
public function getProductDetails1($product_id,$offset,$offset_limit,$sort_id,$customer_token='',$api='',$prodData,$pincode,$segment_id){
$api= 2;
   $productDatas= array();
            $productDatas[0]['product_id'] = $product_id; 
            $product_name= DB::table('products as prod')
            ->select(DB::raw("prod.product_title as product_name, pc.description,prod.primary_image,prod.mrp"))
            
            ->leftJoin('product_content as pc','pc.product_id','=','prod.product_id')
            ->where('prod.product_id','=',$product_id)
            ->get()->all();
            $productDatas[0]['product_title'] = $product_name[0]->product_name;
            $productDatas[0]['description'] = $product_name[0]->description;
            $productDatas[0]['mrp'] = $product_name[0]->mrp;
            $productDatas[0]['rating'] = $this->Review->Review($product_id);
            $productDatas[0]['reviews'] = $this->Review->getReviews($product_id);
            $productDatas[0]['related_products'] = [];
            $productDatas[0]['image'] = $product_name[0]->primary_image;
            $productDatas[0]['images'] = $this->getMedia($product_id);
            $products = json_encode($productDatas);
            $products  = json_decode($products,true);
            $productDatas= sizeof($productDatas);

            $array = array();
            $i=0;

       //Multivariant Loop    
      foreach ($products as $product) {
        if(!empty($product)){
          $inventory =0;  
          // if($product['product_id']==4812){    
          $childProducts = $this->getChildProduct($product['product_id']);
          $array[$i]['product_id'] = $product['product_id'];
          if(isset($product['product_title'])){
            $array[$i]['product_name'] = $product['product_title']; 
            }else{
            $array[$i]['product_name'] = $product['product_name'];
          }
          
          $array[$i]['mrp'] = $product['mrp'];

          if($api==2){
            $array[$i]['description'] = $product['description'];
            if(isset($product['primary_image']) ){
              $array[$i]['image'] = $product['primary_image']; 
              $array[$i]['images']= $this->getMedia($product['product_id']);
              }else{
              $array[$i]['image'] = $product['image']; 
              $array[$i]['images'] = $this->getMedia($product['product_id']); 
            }
            $array[$i]['reviews'] = $this->Review->getReviews($product['product_id']);
          }
          $array[$i]['rating'] = $this->Review->Review($product['product_id']);       
          $array[$i]['related_products'] = [];

          if(isset($product['primary_image'])  && $api !=2){
            $array[$i]['image'] = $product['primary_image']; 
            //$array[$i]['images'][]['image'] = $product['primary_image'];
            }else{
            $array[$i]['image'] = $product['image']; 
            //  $array[$i]['images'][]['image'] = $product['image']; 
          } 
          $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
          $array[$i]['quantity'] = $inventory;
          
          $x=0;
          $l=0;
          $j=0;
          
          if(!empty($childProducts)){
            
            foreach ($childProducts as $childProduct) {
              $childProduct = json_encode($childProduct);
              $childProduct = json_decode($childProduct,true);
              $inventory = $this->getInventory($childProduct['product_id'],$pincode,$segment_id);

              $variants1 = array();
              $variants3 = array();
              $variants2 = array();
              //$inventory = 0;
              $j=0;
              if(!empty($childProduct['variant_value1'])){
                
                $variants1[$j]['product_name'] = $childProduct['product_title'];
                $variants1[$j]['product_id'] = $childProduct['product_id'];
                if($api==2){
                  $variants1[$j]['description'] = $childProduct['description'];
                  $spec = $this->getProductSpecifications($childProduct['product_id']);
                  $variants1[$j]['specifications'] = $spec;
                  $Reviews = $this->Review->getReviews($childProduct['product_id']);
                  $variants1[$j]['reviews'] = $Reviews;
                  $variants1[$j]['related_products'] = [];
                  $variants1[$j]['images'] = $this->getMedia($childProduct['product_id']);
                }

                $variants1[$j]['mrp'] = $childProduct['mrp'];
                $variants1[$j]['quantity'] = $inventory;
                $variants1[$j]['rating'] = $this->Review->Review($childProduct['product_id']);

                $variants1[$j]['image'] = $childProduct['primary_image'];
                $variants1[$j]['variant_name'] = $childProduct['variant_value1'];

                $l=0;
                if(!empty($childProduct['variant_value2'])){
                  $variants1[$j]['has_inner_varients'] = 1;
                  $variants2[$l]['product_name'] = $childProduct['product_title'];
                  $variants2[$l]['product_id'] = $childProduct['product_id'];
                  $variants2[$l]['mrp'] = $childProduct['mrp'];
                  
                  $variants2[$l]['image'] = $childProducts[0]->primary_image;
                  if($api==2){
                    $variants2[$l]['images'] = $this->getMedia($childProduct['product_id']); 
                    $variants2[$l]['description'] = $childProduct['description'];
                    //$spec = 
                    if(isset($spec)){
                      $variants2[$l]['specifications'] = $spec; 
                      }else{
                      $variants2[$l]['specifications'] = $this->getProductSpecifications($childProduct['product_id']);
                    }
                    
                    if(isset($Reviews)){
                      $variants2[$l]['reviews'] = $Reviews;
                      }else{
                      $variants2[$l]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                    }
  
                  }
                  $variants2[$l]['quantity'] = $inventory;
                  $variants2[$l]['variant_name'] = $childProduct['variant_value2'];
                  $k=0;
                  if(!empty($childProduct['variant_value3'])){
                    $variants2[$l]['has_inner_varients'] = 1;
                    $variants3[$k]['variant_name'] = $childProduct['variant_value3'];
                    $variants3[$k]['has_inner_varients'] = 0;
                    $variants3[$k]['quantity'] = $inventory;
                    $variants3[$k]['product_name'] = $childProduct['product_title'];
                    $variants3[$k]['product_id'] = $childProduct['product_id'];
                    $variants3[$k]['mrp'] = $childProduct['mrp'];
                    
                    $variants3[$k]['image'] = $childProduct['primary_image'];
                    if($api==2){
                      $variants3[$k]['images'] = $this->getMedia($childProduct['product_id']);
                      
                      if(isset($Reviews)){
                        $variants3[$k]['reviews'] = $Reviews;  
                        }else{
                        $variants3[$k]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                      }
                      if(isset($spec)){
                        $variants3[$k]['specifications'] = $spec; 
                        }else{
                        $variants3[$k]['specifications'] = $this->getProductSpecifications($childProduct['product_id']); 
                      }
                      
                    }
                    
                    $variants3[$k]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
                    $variants2[$l]['variants'] = $variants3;
                    
                    $k++;
                    
                    }else{
                    $variants2[$l]['has_inner_varients'] = 0;
                    $variants2[$l]['quantity'] = $inventory;
                    $variants2[$l]['product_name'] = $childProduct['product_title'];
                    $variants2[$l]['product_id'] = $childProduct['product_id'];
                    $variants2[$l]['mrp'] = $childProduct['mrp'];
                    $variants2[$l]['image'] = $childProduct['primary_image'];
                    if($api==2){
                      $variants2[$l]['images'] =$this->getMedia($childProduct['product_id']);
                      if(isset($Reviews)){
                        $variants2[$l]['reviews'] = $Reviews;  
                        }else{
                        $variants2[$l]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                      }
                      if(isset($spec)){
                        $variants2[$l]['specifications'] = $spec; 
                        }else{
                        $variants2[$l]['specifications'] = $this->getProductSpecifications($childProduct['product_id']); 
                      }
                    }
                    
                    $variants2[$l]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);  
                  }
                  $variants1[$j]['variants'] = $variants2;
                  $l++;
                  
                  }else{
                  $variants1[$j]['has_inner_varients'] = 0;
                  $variants1[$j]['quantity'] = $inventory;  
                  $variants1[$j]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
                }
                
                $j++;
                $array[$i]['variants'][$x] = $variants1[0];   
              }
              $x++; 
            }
 
          } 
          if(!isset($array[$i]['variants'])){
            unset($array[$i]); 
            $i = $i-1;
          }

          $i++;
        }
//productData null check
      }
        $variants1 = array();
        $variants1['data'] = $array;
      return $variants1; 
}
// returns price details along with promotions.
    /* Get products slab including Cache */
    public function getPricing($product_id, $le_wh_id, $user_id, $cust_type) {
        // Log::info(__METHOD__);
        $le_wh_id = $temp = trim($le_wh_id, "'");
        $temp = str_replace(',', '_', $temp);
        $appKeyData = env('DB_DATABASE');
        $legal_entity_id = $this->_ecash->getLegalEntityId($user_id);
        if ($cust_type == 'NULL') {
            $customer_type = $this->_ecash->getUserCustomerType($legal_entity_id);
        } else {
            $customer_type = $cust_type;
        }
        $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type.'_le_wh_id_'.trim($le_wh_id,"'");
        // Log::info("GET ProductSlabs CACHE key -> ".$keyString);
        $response = Cache::get($keyString);
        // Log::info("GET ProductSlabs CACHE res -> ");
        //Log::info($response);
        $entryExists = 1;
        $slabDetails = [];
        if ($user_id == 0) {
            $temp = 0;
        }
        if ($response != '') {
            $slabDetails = json_decode($response, true);
            if (isset($slabDetails[$temp])) {
                $data = $slabDetails[$temp];
            } else {
                $entryExists = 0;
            }
        } else {
            $entryExists = 0;
        }
        if (!$entryExists) {
            $data = DB::selectFromWriteConnection(DB::raw("CALL getProductSlabsByCust($product_id,'" . $le_wh_id . "',$user_id,$customer_type)"));
            $data = json_decode(json_encode($data), true);
            $slabDetails[$temp] = $data;
            if (!empty($data)) {
                Cache::put($keyString, json_encode($slabDetails), 60);
                // Log::info("PUT ProductSlabs CACHE res -> ");
                //Log::info(json_encode($slabDetails));
            }
        } else {
            if ($customer_type == 3016) {
                $stock = DB::selectFromWriteConnection(DB::raw("SELECT getDITCPInventoryByPId($product_id,'".$le_wh_id."') as stock"));
            } else {
                $stock = DB::selectFromWriteConnection(DB::raw("SELECT GetCPInventoryByProductId($product_id,'".$le_wh_id."') as stock"));
            }
            $stock = (isset($stock[0]->stock))?$stock[0]->stock:0;
            foreach($data as $key=>$pack){
               $data[$key]['stock']= $stock;
            }
        }
        return $data;
    }

// returns the product Data along with variants and parent child relationship for key performance indicators like high  margin, fast moving, high ptr etc.
public function getProductsByKPI($flag,$offset,$offset_limit,$segment_id,$le_wh_id,$sort_id,
  $blockedList,$cust_type=0){
   
     $brands = isset($blockedList['brands']) ? $blockedList['brands'] : 0;
     $manf = isset($blockedList['manf']) ? $blockedList['manf'] : 0;

     if(is_array($brands))
     {
         $brands = implode(',', $brands);
     }

     if(is_array($manf))
     {
         $manf = implode(',', $manf);
     }
     if($brands==""){
        $brands=0;
     }
     if($manf==""){
        $manf =0;
     }
       $t = '@total';
      //Log::info('calllllllllllllllll');
      //Log::info("CALL getProductsByKPI_ByCust($flag,$offset_limit,$offset,$segment_id,$le_wh_id,$sort_id,'".$brands."','".$manf."',$cust_type,$t)");
      $result['data'] = DB::select("CALL getProductsByKPI_ByCust($flag,$offset_limit,$offset,$segment_id,$le_wh_id,$sort_id,'".$brands."','".$manf."',$cust_type,$t)");

      $result['TotalProducts'] = DB::select("SELECT @total");
      return $result; 
}
    /*
      * Function Name: getProductSpecifications()
      * Description: getProductSpecifications function is used to get the product specifiation for the product_id passed. 
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */ 
    public function getProductSpecifications($product_id){
      
      $result = DB::table('product_attributes as pa')
      ->select('pa.attribute_id','pa.value', 'a.name')
      ->leftJoin('attributes as a','a.attribute_id','=','pa.attribute_id')
      ->where('pa.product_id','=',$product_id)
      ->where('pa.value','!=','')
      ->get()->all();
      return $result;
    }
    /*
      * Function Name: getMedia()
      * Description: getMedia function is used to get the multiple product images for the product_id passed.
      
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    public function getMedia($product_id){
      $image1 = DB::table('product_media as pm')
      ->select('pm.url as image')
      ->where('pm.product_id','=',$product_id)
      ->where('pm.media_type','=','85003')
      ->get()->all();
      $image2 = DB::table('products as p')
        ->select('p.primary_image as image')
        ->where('p.product_id','=',$product_id)
        ->get()->all();
        $image = array_merge($image2,$image1);
     
      return $image1; 
    }

/*
      * Function Name: getChildProduct()
      * Description: getChildProduct function is used to get the child product of the product_id passed
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */  
    public function getChildProduct($product_id){
      $product_id = '"'.$product_id.'"';
      $query=DB::select("CALL  getCpProducts($product_id)");
      return $query;   
  
    }
    /*
      * Function Name: getPackData()
      * Description: getPackData function is used to get slabs for the product_id passed based on customer_token and pincode.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    public function getPackData($product_id,$customer_token,$pincode){
      
      if(!empty($customer_token)){
        $user = DB::table('users as u')
        ->select("u.user_id")
        ->where('u.password_token','=',$customer_token)
        ->get()->all();   
        if(!empty($user)){
          $user_id = $user[0]->user_id;
          }else{
          $user_id = 0;
        }
        }else{
        $user_id = 0;
      }
      //user_id=0 when customer is not logged in.
     // $query = DB::select("CALL getProductSlabs($product_id,$pincode,$user_id)"); 
    
    // In the below Line, $pincode is the LeWhId
     $keyString = 'product_slab'.$product_id.'_'.$pincode.'_'.$user_id.'_le_wh_id_'.trim($pincode,"'");
	   $data = Cache::get($keyString,false) ;
      
      if(!$data){
        $data = DB::select("CALL getProductSlabs($product_id,$pincode,$user_id)");
        Cache::put($keyString,$data,10);
      }      
      return json_decode(json_encode($data),true);   
      
    }
    
  /*
  * Function Name: getInventory()
  * Description: getInventory function is used to fetch the inventory based on the pincode, product_id and segment_id
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 18 July 2016
  * Modified Date & Reason:
*/ 
    public function getInventory($product_id,$pincode,$segment_id){
      $inventory = DB::select("SELECT GetCPInventoryStatus($product_id,$pincode, $segment_id,4) as inventory"); 
      $inventory = $inventory[0]->inventory;        
      return $inventory;
    }

public function getDescription($product_id){
$description = DB::table('product_content')
            ->select('description')
            ->where('product_id','=',$product_id)
            ->get()->all();
             if(!empty($description)){
               $description = $description[0]->description;
            }
            return $description;
}


public function getCPMustSkuList($flag,$offset,$offset_limit,$segment_id,$le_wh_id,$sort_id,
  $blockedList,$cust_type=0){
   
     $brands = isset($blockedList['brands']) ? $blockedList['brands'] : 0;
     $manf = isset($blockedList['manf']) ? $blockedList['manf'] : 0;

     if(is_array($brands))
     {
         $brands = implode(',', $brands);
     }

     if(is_array($manf))
     {
         $manf = implode(',', $manf);
     }
       $t = '@total';
      //Log::info('calllllllllllllllll');
      //Log::info("CALL getProductsByMust_Sku($flag,$offset_limit,$offset,$segment_id,$le_wh_id,$sort_id,'".$brands."','".$manf."',$cust_type,$t)");
      $result['data'] = DB::select("CALL getProductsByMust_Sku($flag,$offset_limit,$offset,$le_wh_id,$sort_id,$cust_type,$t)");

      $result['TotalProducts'] = DB::select("SELECT @total");
      return $result; 
}

  }