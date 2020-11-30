<?php
  namespace App\Modules\Cpmanager\Models;
  

  
  use \DB;
  class category extends \Eloquent {
    /*
      * Function Name: getCategories()
      * Description: getCategories function is used to get all the categories aloing with their parent_category_id and the segment_id
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 24 June 2016
      * Modified Date & Reason:
    */
    
      public function getCategories($segment_id,$pincode){
      //DB::enableQueryLog();
     /* $categories =  DB::table('mp_categories as cat')
      ->select(DB::Raw("DISTINCT(cat.mp_category_id) AS category_id, c.parent_id,cat.category_name AS name,
      cat.image_url AS image,IFNULL(GROUP_CONCAT( DISTINCT(sm.value)), 0) AS segment_id "))
      
        
      ->leftJoin('segment_mapping as sm','cat.mp_category_id','=','sm.mp_category_id')
      
      ->leftJoin('mp_category_mapping as cm','cat.mp_category_id','=','cm.mp_category_id')

   
      ->Join('categories as c','c.category_id','=','cm.category_id')
      ->where("cat.mp_id",'=','1')
      ->where("cat.is_approved",'=','1')
      ->GROUPBY('sm.mp_category_id')
   
      ->get()->all();       

      print_r($categories);exit;*/

      $categories = DB::select("CALL getCPCategories($pincode,$segment_id,'2015-01-01',0,0) ");
      //print_r(DB::raw("CALL getCPCategories($pincode,$segment_id,0,0) "));exit;
     //print_r(DB::getQueryLog());exit;       
      return $categories;
      
    }
    /*
      * Function Name: checkCategoryId()
      * Description: checkCategoryId function is used to check if the category_id passed is valid.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 24 June 2016
      * Modified Date & Reason:
    */    
    
    public function checkCategoryId($category_id){
      
      $result = DB::table('mp_categories as cat')
      ->select(DB::raw("count(cat.mp_category_id) as count"))
      ->where("cat.mp_category_id","=",$category_id)
      ->where("cat.mp_id",'=','1')
      ->get()->all();
      
      return $result[0]->count;
      
      
    }
    /*
      * Function Name: categoryChild()
      * Description: categoryChild function is used to get all the child categories of the category_id passed.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 24 June 2016
      * Modified Date & Reason:
    */       
    
    public function categoryChild($category_id){
      
      
      $query = DB::table('mp_categories')
      ->select("mp_category_id as category_id", "category_name as name", "parent_category_id as parent_id ")
      ->where("parent_category_id","=",$category_id)
      ->where("is_approved",'=','1')
      ->where("mp_id",'=','1')
      ->get()->all();
      
      return $query;
      
      
    }
    /*
      * Function Name: getProducts()
      * Description: getProducts function is used to get all the products of the category_id passed within the mentioned limits.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 24 June 2016
      * Modified Date & Reason:
    */   
    public function getProducts($category_id,$offset,$offset_limit,$sort_id){
      
      //echo "hi";exit;
      $query =  //DB::table("mp_product_add_update as mpau")
     /* DB::table("products as prod")
      ->select(DB::raw("Distinct(prod.product_id)"))
      ->leftJoin('mp_product_add_update as mpau','mpau.product_id','=','prod.product_id')
      ->leftJoin('mp_category_mapping as cm','prod.category_id','=','cm.category_id')
      ->leftJoin('mp_categories as cat','cat.mp_category_id','=','cm.mp_category_id')
      ->Join('products_slab_rates as psr','prod.product_id','=','psr.product_id')
      ->LeftJoin('master_lookup as ml','ml.description','=','mpau.mp_id')
      ->where("cat.mp_category_id","=",$category_id)
      ->where("prod.is_active",'=',1)
      ->where("mpau.is_added",'=',1)
      ->where("ml.value",'=',78003);*/
      DB::table('vw_cp_products as cp')
       ->select(DB::raw("Distinct(cp.product_id)"))
       ->Join('products_slab_rates as psr','cp.product_id','=','psr.product_id')
      ->where('cp.product_class_id','=',$category_id)

      
      ->where('cp.is_default','=',1)
      ->where('cp.variant_value1','!=','');
     /* ->skip($offset)
      ->take($offset_limit)
      ->get()->all();*/
      if($sort_id==65001){
        $sort_column="margin";
        $sort_by = "desc";
      }elseif ($sort_id==650002) {
        $sort_column="margin";
        $sort_by = "asc";
      }elseif ($sort_id==650003) {
        $sort_column="price";
        $sort_by = "asc";
      }elseif ($sort_id==650004) {
        $sort_column="price";
        $sort_by = "asc";
      }elseif ($sort_id==650005) {
        $sort_column="product_title";
        $sort_by = "desc";
      }elseif ($sort_id==650006) {
        $sort_column="product_title";
        $sort_by = "asc";
      }

if(isset($sort_column)){
        
 
           $productDatas = $query
                        ->orderBy($sort_column,$sort_by)
                        ->skip($offset)
                        ->take($offset_limit)
                        ->get()->all();
      
       
      }else{
         $productDatas = $query
                         ->skip($offset)
                         ->take($offset_limit)
                         ->get()->all();
      }





    //$result['size']  = sizeof($query);
      
    $result['data'] = $productDatas;
    
      
      return $result;
      
    }
    /*
      * Function Name: getTotalProducts()
      * Description: getTotalProducts function is used to get the total count of all the products of the category_id passed.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 27 June 2016
      * Modified Date & Reason:
    */  
    public function getTotalProducts($category_id){
      
      $query = DB::table('vw_cp_products as cp')
       ->select(DB::raw("Count(Distinct(cp.product_id)) as count"))
       ->Join('products_slab_rates as psr','cp.product_id','=','psr.product_id')
      ->where('cp.product_class_id','=',$category_id)

      
      ->where('cp.is_default','=',1)
      ->where('cp.variant_value1','!=','')



     /* DB::table("mp_product_add_update as mpau")
      ->select(DB::raw("COUNT(mpau.product_id) as count"))
      ->leftJoin('products as prod','mpau.product_id','=','prod.product_id')
      ->leftJoin('mp_category_mapping as cm','prod.category_id','=','cm.category_id')
      ->leftJoin('mp_categories as cat','cat.mp_category_id','=','cm.mp_category_id')
      ->Join('products_slab_rates as psr','prod.product_id','=','psr.product_id')
      ->LeftJoin('master_lookup as ml','ml.description','=','mpau.mp_id')
      ->where("cat.mp_category_id","=",$category_id)
      ->where("prod.is_active",'=',1)
      ->where("mpau.is_added",'=',1)
      ->where("ml.value",'=',78003)*/
      ->get()->all();
      
      return $query[0]->count;
    }
    /*
      * Function Name: getVariantData()
      * Description: getVariantData function is used to get the product variants of the product_id passed.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 27 June 2016
      * Modified Date & Reason:
   
    */
    
    public function getVariantData($product_id){
      
      $query = DB::select(DB::raw("SELECT p.product_id AS product_variant_id,p.product_id as variant_id,p.product_id,pc.description,p.product_title as product_name, p.is_default AS is_default,
      p.mrp, p.product_inventory AS quantity, p.primary_image as image,  p.variant_value1 AS name 
      FROM  vw_cp_products AS `p`
     
      /*LEFT JOIN `product_attributes` AS `pa` ON `p`.`product_id` = `pa`.`product_id`*/
      INNER JOIN  products_slab_rates as psr ON p.product_id=psr.product_id
      LEFT JOIN `product_content` AS `pc` ON `p`.`product_id` = `pc`.`product_id`  
     
    /*  LEFT JOIN  inventory AS pv ON   p.`product_id`= pv.`product_id`*/
      WHERE p.product_id = '".$product_id."' GROUP BY psr.product_id"));
     
      return $query;
    }
    /*
      * Function Name: getPackData()
      * Description: getPackData function is used to get the slab rates of the product product_id passed when the customer is logged in.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 27 June 2016
      * Modified Date & Reason:
    */
    public function getPackData($product_id){
      
      
      $query = DB::table("products_slab_rates as psr")
      ->select(DB::raw("psr.product_slab_id as variant_price_id,psr.end_range as pack_size,psr.price as unit_price,(psr.price*psr.end_range) as dealer_price, (p.mrp-psr.price)*100/psr.price AS margin"))
      ->Join('products as p','p.product_id','=','psr.product_id')
      ->where("p.product_id","=",$product_id)
      ->get()->all();
      
      return $query;
      
    }
    /*
      * Function Name: getGuestPackData()
      * Description: getGuestPackData function is used to get the slab rates of the product product_id passed when the customer is not logged in.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 28 June 2016
      * Modified Date & Reason:
    */
    public function getGuestPackData($product_id){
      
      $query = DB::table("products_slab_rates as psr")
      ->select(DB::raw("psr.product_slab_id as variant_price_id,psr.end_range as pack_size,(select '' from dual) as  unit_price,(select '' from dual) as  dealer_price, (select '' from dual) as  margin"))
      ->Join('products as p','p.product_id','=','psr.product_id')
      ->where("p.product_id","=",$product_id)
      ->get()->all();
      
      return $query;
      
      
    }
    /*
      * Function Name: checkCustomerToken()
      * Description: checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 28 June 2016
      * Modified Date & Reason:
    */
    public function checkCustomerToken($customer_token){

     // db::enableQueryLog();
      $query = DB::table("users as u")
      ->select(DB::raw("count(u.password_token) as count")) 
      ->where("u.password_token","=",$customer_token)   
      ->get()->all();
     // print_r(db::getquerylog());exit;
      return $query[0]->count;   
      
      
    }
    
    /*
      * Function Name: checkProductId()
      * Description: checkProductId function is used to check if the product_id passed is valid.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 1 July 2016
      * Modified Date & Reason:
    */

    public function checkProductId($product_id){

      $query = DB::table("products as p")
      ->select(DB::raw("count(p.product_id) as count")) 
      ->where("p.product_id","=",$product_id)  
      ->get()->all();
      
      return $query[0]->count;   
 
    }

    /*
      * Function Name: getVariantImages()
      * Description: getVariantImages function is used to fetch the images of the product_id passed .
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 1 July 2016
      * Modified Date & Reason:
    */
    public function getVariantImages($product_id){

      $query = DB::table("product_media as pm")
      ->select(DB::raw("pm.prod_media_id as product_image_id,url as image"))
     // ->Join('products as p','p.product_id','=','psr.product_id')
      ->where("pm.product_id","=",$product_id)
      ->get()->all();
      
      return $query;
      
    }
    /*
      * Function Name: Attributes()
      * Description: Attributes function is used to fetch the attributes of the product_id passed .
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 1 July 2016
      * Modified Date & Reason:
    */

    public function Attributes($product_id){
      $query = DB::table("product_attributes as pa")
      ->select(DB::raw("pa.product_id,pa.attribute_id,pa.value,a.attribute_id,a.name as attribute_name"))
      ->LeftJoin('attributes as a','a.attribute_id','=','pa.attribute_id')
      ->where("pa.product_id","=",$product_id)
      ->get()->all();
      
      return $query;
      

      
    }



        /*
      * Function Name: getBrandProducts()
      * Description: getBrandProducts function is used to get all the products of the brand_id passed within the mentioned limits.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 24 June 2016
      * Modified Date & Reason:
    */   
    public function getBrandProducts($brand_id,$offset,$offset_limit,$sort_id,$le_wh_id,$segment_id){
      
     // db::enableQueryLog();

      $le_wh_id=trim($le_wh_id,"'");
     
$query =  DB::table("vw_cp_products as prod")
      ->select(db::raw("distinct(prod.product_id) as product_id"))
      ->Join('vw_category_products as p','prod.product_id','=','p.product_id')
    // ->Join('vw_product_slab_rates as psr','prod.product_id','=','psr.product_id')
      ->Join('inventory','prod.product_id','=','inventory.product_id')
      //->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
     // ->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
      ->where('prod.is_default','=',1)
     // ->where('prod.variant_value1','!=','')
      ->where("p.segemnt_id","=",$segment_id)
      ->where("p.brand_id","=",$brand_id);

       if($le_wh_id)
       {
      $query=$query->whereIn('inventory.le_wh_id',[$le_wh_id]);
      }

       if($sort_id==65001){
        $sort_column="margin";
        $sort_by = "desc";
      }elseif ($sort_id==650002) {
        $sort_column="margin";
        $sort_by = "asc";
      }elseif ($sort_id==650003) {
        $sort_column="unit_price";
        $sort_by = "asc";
      }elseif ($sort_id==650004) {
        $sort_column="unit_price";
        $sort_by = "asc";
      }elseif ($sort_id==650005) {
        $sort_column="product_title";
        $sort_by = "desc";
      }elseif ($sort_id==650006) {
        $sort_column="product_title";
        $sort_by = "asc";
      }

if(isset($sort_column)){
        
 
           $productDatas = $query
                        ->orderBy($sort_column,$sort_by)
                        ->skip($offset)
                        ->take($offset_limit)
                        ->get()->all();
      
       
      }else{
         $productDatas = $query
                         ->skip($offset)
                         ->take($offset_limit)
                         ->get()->all();
      }


    // print_r(DB::getQueryLog());exit;

    // print_r(DB::enableQueryLog());
      
      
      return $productDatas;
      
    }
    
     /*
      * Function Name: getTotalBrandProducts()
      * Description: getTotalProducts function is used to get the total count of all the products of the category_id passed.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 27 June 2016
      * Modified Date & Reason:
    */  
    public function getTotalBrandProducts($brand_id,$le_wh_id,$segment_id){


       $le_wh_id=trim($le_wh_id,"'");

      $query =  DB::table("vw_cp_products as prod")
      ->select(db::raw("COUNT(distinct prod.product_id) as count"))
      ->Join('vw_category_products as p','prod.product_id','=','p.product_id')
     //->Join('vw_product_slab_rates as psr','prod.product_id','=','psr.product_id')
      ->Join('inventory','prod.product_id','=','inventory.product_id')
      //->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
     // ->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
      ->where('prod.is_default','=',1)
      //->where('prod.variant_value1','!=','')
      ->where("p.brand_id","=",$brand_id)
      ->whereIn('inventory.le_wh_id',[$le_wh_id])
       ->where("p.segemnt_id","=",$segment_id)
      ->get()->all();
      
      return $query[0]->count;
    }

     /*
      * Function Name: checkBrandId()
      * Description: checkBrandId function is used to check if the brand_id passed is valid.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 24 June 2016
      * Modified Date & Reason:
    */    
    
    public function checkBrandId($brand_id){
      
      $result = DB::table('brands')
      ->select(DB::raw("count(brand_id) as count"))
      ->where("brand_id","=",$brand_id)
      ->get()->all();
      
      return $result[0]->count;
      
      
    }
    
    public function getOfferProducts($flag,$offset,$offset_limit,$segment_id,$pincode,$sort_id){
   // DB::enableQueryLog();
      if(empty($segment_id)){
        $segment_id = -1;
      }
      if(empty($pincode)){
        $pincode = -1;
      }
      if(empty($sort_id)){
        $sort_id = -1;
      }
     /* print_r($flag);
      echo "<pre>";
      print_r($offset);
      echo "<pre>";
      print_r($offset_limit);
      echo "<pre>";
      print_r($segment_id);
      echo "<pre>";
      exit;*/


//$offset = 20;
       $t = '@total';
      //print_r(DB::raw("CALL getSortdata_margintest($flag,$offset_limit,$offset,$segment_id,$pincode,$sort_id,$t)"));exit;
     
      $result['data'] = DB::select("CALL getSortdata($flag,$offset_limit,$offset,$segment_id,$pincode,$sort_id,$t)");
     // CALL getSortdata_test(55003,20,0,NULL,@total); print_r($result);exit;

      $result['TotalProducts'] = DB::select("SELECT @total");

      return $result;

      /*if($flag==1){
      $query =  DB::table("products_slab_rates as psr")
      ->select(DB::raw("psr.product_id"))
      ->Join('mp_product_add_update as mpau','mpau.product_id','=','psr.product_id')
      ->leftJoin('products as prod','mpau.product_id','=','prod.product_id')
      //->Join('products_slab_rates as psr','prod.product_id','=','psr.product_id')
      
      ->where("prod.is_active",'=',1)
      ->where("mpau.is_added",'=',1)
      ->where("mpau.mp_id",'=',1)
      ->orderBy("psr.margin","desc")
      ->skip($offset)
      ->take($offset_limit)
      ->get()->all();
    // print_r(DB::getQueryLog());exit;
      return $query;
    
      
      }
      elseif($flag==2){
        
       $query1 = DB::table("products AS p")
       ->select(DB::raw("p.product_id"))
       ->leftJoin('gds_order_products as gop','gop.product_id','=','p.product_id')
       ->leftJoin('gds_orders as go','go.gds_order_id','=',"gop.gds_order_id")
       ->Join('products_slab_rates as psr','p.product_id','=','psr.product_id')
       ->leftJoin('mp_category_mapping as cm','p.category_id','=',"cm.category_id")
       ->leftJoin('mp_categories as cat','cat.mp_category_id','=',"cm.mp_category_id");
       
      
      if(!empty($segment_id)){
        $query = $query1
               ->leftJoin('segment_mapping as sm','sm.mp_category_id','=',"cat.mp_category_id")
               ->where("sm.value",'=',$segment_id)
               ->where("go.mp_id",'=',2)
               ->GROUPBY("p.product_id")
               ->orderBy(DB::raw("COUNT(p.product_id)"),"desc")
               ->skip($offset)
               ->take($offset_limit)
               ->get()->all();
      }else{
        $query = $query1
               
               ->where("go.mp_id",'=',2)
               ->GROUPBY("p.product_id")
               ->orderBy(DB::raw("COUNT(p.product_id)"),"desc")
               ->skip($offset)
               ->take($offset_limit)
               ->get()->all();

      }

     



     
      return $query;
      }*/
      
   
  
    }
    /*
      * Function Name: checkSegmentId()
      * Description: checkSegmentId function is used to check if the segment_id passed is valid.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 12 July 2016
      * Modified Date & Reason:
    */ 
    public function checkSegmentId($segment_id){
      $query = DB::table("segment_mapping as sm")
      ->select(DB::raw("count(sm.value) as count")) 
      ->where("sm.value","=",$segment_id)   
      ->get()->all();
      
      return $query[0]->count;   
      
      
    }

    public function getUserId($customer_token){
      $query = DB::table("users as u")
      ->select("user_id","firstname","lastname")
      ->where("u.password_token","=",$customer_token)   
      ->get()->all();
      
      return $query;   
      
      
    }


        /*
      * Function Name: getManufacturerProducts()
      * Description: getManufacturerProducts function is used to get all the products of the manufacturer_id passed within the mentioned limits.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 8 Aug 2016
      * Modified Date & Reason:
    */   
    public function getManufacturerProducts($manufacturer_id,$offset,$offset_limit,$sort_id,$le_wh_id,$segment_id){
      

       $le_wh_id=trim($le_wh_id,"'");
     // print_r($le_wh_id);exit;
     //db::enableQueryLog();
       $le_wh_id=trim($le_wh_id,"'");
      $query =  DB::table("vw_cp_products as prod")
      ->select(db::raw("distinct prod.product_id"))
     // ->Join('vw_product_slab_rates as psr','prod.product_id','=','psr.product_id')
      ->Join('vw_category_products as p','prod.product_id','=','p.product_id')
      ->Join('inventory','prod.product_id','=','inventory.product_id')
     // ->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
      //->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
      ->where('prod.is_default','=',1)
      //->where('prod.variant_value1','!=','')
      ->where('p.segemnt_id','=',$segment_id)
      ->where("p.manufacturer_id","=",$manufacturer_id)
      ->whereIn('inventory.le_wh_id',[$le_wh_id]);
      
       if($sort_id==65001){
        $sort_column="margin";
        $sort_by = "desc";
      }elseif ($sort_id==650002) {
        $sort_column="margin";
        $sort_by = "asc";
      }elseif ($sort_id==650003) {
        $sort_column="unit_price";
        $sort_by = "asc";
      }elseif ($sort_id==650004) {
        $sort_column="unit_price";
        $sort_by = "asc";
      }elseif ($sort_id==650005) {
        $sort_column="product_title";
        $sort_by = "desc";
      }elseif ($sort_id==650006) {
        $sort_column="product_title";
        $sort_by = "asc";
      }

if(isset($sort_column)){
        
 
           $productDatas = $query
                        ->orderBy($sort_column,$sort_by)
                        ->skip($offset)
                        ->take($offset_limit)
                        ->get()->all();
      
       
      }else{
         $productDatas = $query
                         ->skip($offset)
                         ->take($offset_limit)
                         ->get()->all();
      }


     //print_r(DB::getQueryLog());exit;

    // print_r(DB::enableQueryLog());
      
      
      return $productDatas;
      
    }
    
    /* 
      * Function Name: getTotalManufacturerProducts
      * Description: getTotalManufacturerProducts function is used to get the total count of
       all the products of the manufacturer_id passed.
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 8 Aug 2016
      * Modified Date & Reason:
    */
     
    public function getTotalManufacturerProducts($manufacturer_id,$le_wh_id,$segment_id){
      
       $le_wh_id=trim($le_wh_id,"'");
      $query =  DB::table("vw_cp_products as prod")
      ->select(db::raw("COUNT(distinct prod.product_id) as count"))
      //->Join('vw_product_slab_rates as psr','prod.product_id','=','psr.product_id')
      ->Join('vw_category_products as p','prod.product_id','=','p.product_id')
      ->Join('inventory','prod.product_id','=','inventory.product_id')
     // ->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
     // ->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
      ->where('prod.is_default','=',1)
      //->where('prod.variant_value1','!=','')
      ->whereIn('inventory.le_wh_id',[$le_wh_id])
      ->where("p.segemnt_id","=",$segment_id)
      ->where("p.manufacturer_id","=",$manufacturer_id)
      ->get()->all();
      
      return $query[0]->count;
    }


    public function getPincode($customer_token){
      $query = DB::table('users as u')
               ->select('lew.pincode')
               ->leftJoin('legalentity_warehouses as lew','lew.legal_entity_id','=','u.legal_entity_id')
               ->where('u.password_token','=',$customer_token)
               ->get()->all();
              return $query[0]->pincode;
    }
  }   
  