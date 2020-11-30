<?php
  namespace App\Modules\Cpmanager\Models;
  use \DB;
  use App\Modules\Cpmanager\Models\Review;
  use App\Central\Repositories\RoleRepo;
  use Log;

  class SearchModel extends \Eloquent {
    /*
      * Function Name: getSearchAjaxProduct
      * Description: getSearchAjaxProduct function is used to get all the products based on the keyword passed
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 7 July 2016
      * Modified Date & Reason:
    */
      
   public function __construct() {

         $this->Review = new Review();
          $this->_rolerepo = new RoleRepo();

      }
      
    public function getSearchAjaxProduct($keyword, $le_wh_id, $segment_id, $blockedList, $customer_type) {

        // $hub_id="'".$hub_id."'";
        //  $brands=$this-> getHubBrandsManf($hub_id,1);
        //$manf=$this-> getHubBrandsManf($hub_id,2);
        $brands = isset($blockedList['brands']) ? $blockedList['brands'] : 0;
        $manf = isset($blockedList['manf']) ? $blockedList['manf'] : 0;

        if (is_array($brands)) {
            $brands = implode(',', $brands);
        }

        if (is_array($manf)) {
            $manf = implode(',', $manf);
        }

        $le = trim($le_wh_id, "'");
        $le = explode(',', $le);

        $result = DB::table('products as prod')
                ->select(db::raw("distinct prod.product_id,prod.product_title as name,IFNULL(getParentPrdId(prod.product_id),prod.product_id) AS parent_id"))
                ->join("segment_mapping as sm", "prod.category_id", '=', 'sm.mp_category_id')
                ->join("product_prices as pp", "prod.product_id", '=', 'pp.product_id')
                ->join('product_cpenabled_dcfcwise as pcdfw','pcdfw.product_id','=','prod.product_id')
                ->where(function($query) use ($keyword) {
                    $query->where('prod.product_title', 'LIKE', '%' . $keyword . '%')
                    ->orwhere('prod.meta_keywords', 'LIKE', '%' . $keyword . '%');
                })
                ->where('pcdfw.cp_enabled', '=', 1)
                ->whereRaw('FIND_IN_SET(pp.dc_id,' . $le_wh_id . ')')
                ->where('pp.customer_type', '=', $customer_type)
                ->where('pcdfw.is_sellable', '=', 1)
                ->where('sm.value', '=', $segment_id)
                ->whereRaw('FIND_IN_SET(pcdfw.le_wh_id,' . $le_wh_id . ')')
                ->where(db::raw('GetCPInventoryByProductId(prod.product_id,' . $le_wh_id . ')'), '>', 0);
        //->whereRaw('CASE prod.brand_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(prod.brand_id,'.$brands.') END')
        // ->whereRaw('CASE prod.manufacturer_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(prod.manufacturer_id,'.$manf.') END')
        if ($brands != 0) {
            $result = $result->whereRaw('CASE prod.brand_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(prod.brand_id,"' . $brands . '") END');
        }
        if ($manf != 0) {
            $result = $result->whereRaw('CASE prod.manufacturer_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(prod.manufacturer_id,"' . $manf . '") END');
        }
        //echo $result->tosql();exit;
        return $result->get()->all();
    }

    /*
      * Function Name: getSearchAjaxBrand
      * Description: getSearchAjaxBrand function is used to get the brands based on keywords
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 7 July 2016
      * Modified Date & Reason:
    */
 
   public function getSearchAjaxBrand($keyword, $le_wh_id, $segment_id, $blockedList, $customer_type) {
        //  $hub_id="'".$hub_id."'";
        $desc = $this->getMastDesc();
        //  $brands=$this-> getHubBrandsManf($hub_id,1);
        $brands = isset($blockedList['brands']) ? $blockedList['brands'] : 0;
        if (is_array($brands)) {
            $brands = implode(',', $brands);
        }
        $result = DB::table('brands as brand')
                ->select(DB::RAW("DISTINCT brand.brand_id,brand.brand_name as name"))
                ->join("products as p", "p.brand_id", '=', 'brand.brand_id')
                ->join("product_prices as pp", "pp.product_id", '=', 'p.product_id')
                ->where('pp.customer_type', '=', $customer_type)
                ->whereRaw('FIND_IN_SET(pp.dc_id,' . $le_wh_id . ')')
                ->where('brand.brand_name', 'LIKE', '%' . $keyword . '%')
                ->where('brand.legal_entity_id', '=', $desc)
                ->where(db::raw('GetCPInventoryByProductId(p.product_id,' . $le_wh_id . ')'), '>', 0);
        //->where(db::raw('GetCPInventoryStatus2(brand.brand_id,  '.$le_wh_id.','.$segment_id.',1)'),'>',0);
        //->whereRaw('CASE brand.brand_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(brand.brand_id,'.$brands.') END')
        if ($brands != 0) {
            $result = $result->whereRaw('CASE brand.brand_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(brand.brand_id,"' . $brands . '") END');
        }
        return $result->get()->all();
    }

    /*
      * Function Name: getSearchAjaxCategory
      * Description: getSearchAjaxCategory function is used to get the categories based on keyword
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 7 July 2016
      * Modified Date & Reason:
    */
 
  public function getSearchAjaxCategory($keyword, $le_wh_id, $segment_id, $customer_type) {
        $result = DB::table('categories as cat')
                ->select(db::raw("distinct cat.category_id as category_id,cat.cat_name as name"))
                ->join("products as p", "p.category_id", '=', 'cat.category_id')
                ->join("product_prices as pp", "pp.product_id", '=', 'p.product_id')
                ->where('pp.customer_type', '=', $customer_type)
                ->whereRaw('FIND_IN_SET(pp.dc_id,' . $le_wh_id . ')')
                ->where('cat.cat_name', 'LIKE', '%' . $keyword . '%')
                ->where('cat.is_active', '=', 1)
                ->where(db::raw('GetCPInventoryByProductId(p.product_id,' . $le_wh_id . ')'), '>', 0)
                //->where(db::raw('GetCPInventoryStatus2(cat.category_id,'.$le_wh_id.','.$segment_id.',3)'),'>',0)
                ->get()->all();
        return $result;
    }

    /*
      * Function Name: getSearchAjaxManufacturer
      * Description: getSearchAjaxManufacturer function is used to get the manufacturer based on keyword
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 25 July 2016
      * Modified Date & Reason:
    */

      public function getSearchAjaxManufacturer($keyword, $le_wh_id, $segment_id, $blockedList, $customer_type) {
        //  $hub_id="'".$hub_id."'";
        $desc = $this->getMastDesc();
        $manf = isset($blockedList['manf']) ? $blockedList['manf'] : 0;
        //  $manf=$this->getHubBrandsManf($hub_id,2);
        if (is_array($manf)) {
            $manf = implode(',', $manf);
        }
        $result = DB::table('legal_entities as le')
                ->select(DB::RAW("DISTINCT le.legal_entity_id as manufacturer_id,le.business_legal_name as name"))
                ->join("products as p", "p.manufacturer_id", '=', 'le.legal_entity_id')
                ->join("product_prices as pp", "pp.product_id", '=', 'p.product_id')
                ->where('pp.customer_type', '=', $customer_type)
                ->whereRaw('FIND_IN_SET(pp.dc_id,' . $le_wh_id . ')')
                ->where(db::raw('GetCPInventoryByProductId(p.product_id,' . $le_wh_id . ')'), '>', 0)
                ->where('le.business_legal_name', 'LIKE', '%' . $keyword . '%')
                ->where('le.parent_id', '=', $desc)
                ->where('le.legal_entity_type_id', '=', 1006)
                ->where(db::raw('GetCPInventoryByProductId(p.product_id,' . $le_wh_id . ')'), '>', 0);
        //->where(db::raw('GetCPInventoryStatus2(le.legal_entity_id,'.$le_wh_id.','.$segment_id.',2)'),'>',0)
        //->whereRaw('CASE le.legal_entity_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(le.legal_entity_id,'.$manf.') END')
        if ($manf != 0) {
            $result = $result->whereRaw('CASE le.legal_entity_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(le.legal_entity_id,"' . $manf . '") END');
        }
        return $result->get()->all();
    }

    /*
      * Function Name: getSearch
      * Description: getSearch function is used to data for the keyword sent
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 25 July 2016
      * Modified Date & Reason:
    */

  public function getSearch($keyword, $segment_id, $category_id, $brand_id,$manufacturer_id, $filters, 
    $offset, $offset_limit,$sort_id,$le_wh_id,$customer_type,$blockedList)
  {
        DB::enablequerylog();
  //   $hub_id="'".$hub_id."'";
//     $brands=$this->getHubBrandsManf($hub_id,1);
//     $manf=$this->getHubBrandsManf($hub_id,2);
     $brands = isset($blockedList['brands']) ? $blockedList['brands'] : 0;
     $manf = isset($blockedList['manf']) ? $blockedList['manf'] : 0;
    /* if(!is_array($brands))
     {
         $brands = explode(',', $brands);
     }
     if(!is_array($manf))
     {
         $manf = explode(',', $manf);
     }*/

     if(is_array($brands))
     {
         $brands = implode(',', $brands);
     }

     if(is_array($manf))
     {
         $manf = implode(',', $manf);
     }
     $le_wh_id=$le_wh_id;

  $result=DB::table('product_cpenabled_dcfcwise as pcd')->select(db::raw('GROUP_CONCAT(DISTINCT pif.product_id  SEPARATOR ",") AS product_id,
(CASE i.inv_display_mode WHEN soh THEN (i.soh - (i.order_qty + i.reserved_qty)) WHEN atp THEN (i.atp - (i.order_qty + i.reserved_qty)) ELSE ((i.soh + i.atp) - (i.order_qty + i.reserved_qty)) END) AS inv'))->join('products_inventory_flat as pif','pif.product_id','=','pcd.product_id')->join('inventory as i',function($query){
        $query->on('i.le_wh_id','=', 'pcd.le_wh_id')->on('i.product_id', '=', 'pcd.product_id');
  })->where('pcd.cp_enabled',1);
  if(!empty($keyword))
    {
   
                  $data= $result
                                     -> where(function($query) use ($keyword) {
                              $query->where('pif.product_title', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('pif.sub_category_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('pif.product_class_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('pif.category_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('pif.brand_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('pif.manufacturer_name', 'LIKE', '%' . $keyword . '%');
                                    //->orwhere('pif.meta_keywords', 'LIKE', '%' . $keyword . '%');
    });
    }
    else
    {
    $data = $result;
    }
    /*if($segment_id)
    {
    
    $data=$data->where('pif.segment_id', '=', $segment_id);

    }*/

  if (!empty($category_id))
    {
    $data = $data->where('pif.product_class_id', '=', $category_id);
    }
   

  if (!empty($brand_id))
    {
    $data = $data->where('pif.brand_id', '=', $brand_id);
    }

     if (!empty($manufacturer_id))
    {
    $data = $data->where('pif.manufacturer_id', '=', $manufacturer_id);
    }
    
    

  if (!empty($filters))
  {

    $last_result = $data;

      if (!empty($filters['product_filters']))
        {

          $filters_att=explode(',',$filters['product_filters']['attribute_id']);
           $filters_value=explode(',',$filters['product_filters']['values']);
        $last_result =  $last_result->leftjoin('product_attributes AS pa', 'pa.product_id', '=', 'pif.product_id')
        ->whereIn('pa.attribute_id',$filters_att)
        ->whereIn('value',$filters_value);
        
      }

   //brand filter  
    if(empty($brand_id))
    {  
        
      if (!empty($filters['brand']))
        {
          $filters_brand=explode(',',$filters['brand']['brand_id']);
         
         // print_r($filters_brand);exit;
        $last_result =  $last_result->whereIn('pif.brand_id',$filters_brand);
        
        }
      }

   // category filter
      if(empty($category_id))
      {
      if (!empty($filters['categories']))
      {
        $filters_cat=explode(',',$filters['categories']['category_id']);
      $last_result = $last_result->whereIn('pif.product_class_id', $filters_cat);
      }
      }

      //manufacturer filter
      
      if(empty($manufacturer_id))
      {
      if(!empty($filters['manf']))
      {
         $filters_manf=explode(',',$filters['manf']['maf_id']);
      $last_result = $last_result->whereIn('pif.manufacturer_id', $filters_manf);
      }
      }

      /*if (!empty($filters['margin']))
        {
        $minmargin = $filters['margin']['minvalue'];
        $maxmargin = $filters['margin']['maxvalue'];
        $last_result =  $last_result->whereBetween('psr.margin',array($minmargin,$maxmargin));
        }*/

         if (!empty($filters['mrp']))
        {
        $minmrp = $filters['mrp']['minvalue'];
        $maxmrp = $filters['mrp']['maxvalue'];
        $last_result =  $last_result->whereBetween('pif.mrp',array($minmrp,$maxmrp));
        }

      if (!empty($filters['price']))
        {

          //getProductEsp_wh
        $minprice = $filters['price']['minvalue'];
        $maxprice = $filters['price']['maxvalue'];
        $last_result =  $last_result->whereBetween((db::raw('getProductEsp_wh(pif.product_id,'.$le_wh_id.')*pif.pack_size')),array($minprice,$maxprice));
        }

         if (!empty($filters['fastmoving']))
        {
        if($filters['fastmoving']['value']=="true")
        {
         $last_result =  $last_result->orderBy('pif.kvi', 'ASC');
      
        }
       
       
        }

       //product filters   
      if(!empty($filters['rating']) )
        {
     
         $min=$filters['rating']['minvalue'];
         $max=$filters['rating']['maxvalue'];
         $rating=$this->Review->FilterRatingProducts($min,$max); 

       $last_result= $last_result
        ->whereIn('pif.product_id', $rating);
          
        }
        $last_result= $last_result->where('i.le_wh_id',$le_wh_id);
        //$last_result= $last_result->skip($offset)->take($offset_limit)->get()->all();
        $data= $last_result->having('inv','>',0);
        
    }else{
        $data= $data->where('i.le_wh_id',$le_wh_id);
        $data= $data->having('inv','>',0);
    }

    
  /*$result = DB::table('vw_cp_products as prod')
  ->select(db::raw("group_concat(distinct prod.product_id  separator ',')as product_id"))
  ->join('vw_category_products as cat','cat.product_id','=','prod.product_id')
  ->leftJoin('vw_product_slab_rates as psr',function ($join) use ($customer_type)
                     {
                     
                      $join->on('prod.product_id', '=', 'psr.product_id')
                           ->where('psr.customer_type', '=', $customer_type);


                     })
  ->where(db::raw('GetCPInventoryByProductId(prod.product_id,'.$le_wh_id.')'),'>',0);

    if($brands != 0)
    {
        $result = $result->whereRaw('CASE cat.brand_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(cat.brand_id,"'.$brands.'") END');
    }
   if($manf != 0)
   {
       $result = $result->whereRaw('CASE cat.manufacturer_id WHEN 0 THEN 1=1 ELSE !FIND_IN_SET(cat.manufacturer_id,"'.$manf.'") END');
   }
   

  if(!empty($keyword))
    {
   
                  $data= $result
                                     -> where(function($query) use ($keyword) {
                              $query->where('prod.product_title', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.sub_category_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.product_class_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.category_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.brand_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.manufacturer_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.meta_keywords', 'LIKE', '%' . $keyword . '%');
    });
    }
    else
    {
    $data = $result;
    }

    if($segment_id)
    {
    
    $data=$data->where('cat.segemnt_id', '=', $segment_id);

    }

  if (!empty($category_id))
    {
    $data = $data->where('cat.product_class_id', '=', $category_id);
    }
   

  if (!empty($brand_id))
    {
    $data = $data->where('cat.brand_id', '=', $brand_id);
    }

     if (!empty($manufacturer_id))
    {
    $data = $data->where('cat.manufacturer_id', '=', $manufacturer_id);
    }
    
    

  if (!empty($filters))
    {

  $last_result = $data;

    if (!empty($filters['product_filters']))
      {

        $filters_att=explode(',',$filters['product_filters']['attribute_id']);
         $filters_value=explode(',',$filters['product_filters']['values']);
      $last_result =  $last_result->leftjoin('product_attributes AS pa', 'pa.product_id', '=', 'prod.product_id')
      ->whereIn('pa.attribute_id',$filters_att)
      ->whereIn('value',$filters_value);
      
    }

 //brand filter  
  if(empty($brand_id))
  {  
      
    if (!empty($filters['brand']))
      {
        $filters_brand=explode(',',$filters['brand']['brand_id']);
       
       // print_r($filters_brand);exit;
      $last_result =  $last_result->whereIn('cat.brand_id',$filters_brand);
      
      }
    }

 // category filter
    if(empty($category_id))
    {
    if (!empty($filters['categories']))
    {
      $filters_cat=explode(',',$filters['categories']['category_id']);
    $last_result = $last_result->whereIn('cat.product_class_id', $filters_cat);
    }
    }

    //manufacturer filter
    
    if(empty($manufacturer_id))
    {
    if(!empty($filters['manf']))
    {
       $filters_manf=explode(',',$filters['manf']['maf_id']);
    $last_result = $last_result->whereIn('cat.manufacturer_id', $filters_manf);
    }
    }

    if (!empty($filters['margin']))
      {
      $minmargin = $filters['margin']['minvalue'];
      $maxmargin = $filters['margin']['maxvalue'];
      $last_result =  $last_result->whereBetween('psr.margin',array($minmargin,$maxmargin));
      }

       if (!empty($filters['mrp']))
      {
      $minmrp = $filters['mrp']['minvalue'];
      $maxmrp = $filters['mrp']['maxvalue'];
      $last_result =  $last_result->whereBetween('prod.mrp',array($minmrp,$maxmrp));
      }

    if (!empty($filters['price']))
      {
      $minprice = $filters['price']['minvalue'];
      $maxprice = $filters['price']['maxvalue'];
      $last_result =  $last_result->whereBetween((db::raw('psr.unit_price*psr.pack_size')),array($minprice,$maxprice));
      }

       if (!empty($filters['fastmoving']))
      {
      if($filters['fastmoving']['value']=="true")
      {
       $last_result =  $last_result->orderBy('prod.key_value_index', 'ASC');
    
      }
     
     
      }

     //product filters   
    if(!empty($filters['rating']) )
      {
   
       $min=$filters['rating']['minvalue'];
       $max=$filters['rating']['maxvalue'];
       $rating=$this->Review->FilterRatingProducts($min,$max); 

     $last_result= $last_result
      ->whereIn('prod.product_id', $rating);
        
      }

     // $last_result= $last_result->skip($offset)->take($offset_limit)->get()->all();

    }*/
    
    /*if($sort_id==65001){
        $sort_column="margin";
        $sort_by = "desc";
      }elseif ($sort_id==650002) {
        $sort_column="margin";
        $sort_by = "asc";
      }else*/
      if ($sort_id==650003) {
        $sort_column=DB::RAW("getProductEsp_wh(pif.product_id,'.$le_wh_id.')*pif.pack_size");
        $sort_by = "asc";
      }elseif ($sort_id==650004) {
        $sort_column=DB::raw("getProductEsp_wh(pif.product_id,'.$le_wh_id.')*pif.pack_size");
        $sort_by = "asc";
      }elseif ($sort_id==650005) {
        $sort_column=DB::raw("pif.product_title");
        $sort_by = "desc";
      }elseif ($sort_id==650006) {
        $sort_column=DB::raw("pif.product_title");
        $sort_by = "asc";
      }

  /*if (empty($filters))
    {
*/
           
     if(isset($sort_column)){
        
 
           $last_result  = $data
                        ->orderBy($sort_column,$sort_by)
                        ->skip($offset)
                        ->take($offset_limit)
                        ->get()->all();    
      }else{
         $last_result  = $data
                         ->skip($offset)
                         ->take($offset_limit)
                         ->get()->all();
      }

   // }
//print_r( db::getquerylog() );exit;
  //print_r($last_result);exit;
  return $last_result;
  }


   /*
      * Function Name: getSearch
      * Description: getSearch function is used to data for the keyword sent
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 25 July 2016
      * Modified Date & Reason:
    */

      public function getSearchTotal($keyword, $segment_id, $category_id, $brand_id,$manufacturer_id, $filters,$le_wh_id,$customer_type)
    {


       $le_wh_id=explode(',',$le_wh_id);


       $result = DB::table('vw_cp_products as prod')->select(db::raw("count(distinct(prod.product_id)) 
        as TotalProducts"))
        ->join('vw_category_products as cat','cat.product_id','=','prod.product_id')
        ->Join('vw_product_slab_rates as psr','prod.product_id','=','psr.product_id')
        ->Join('inventory','prod.product_id','=','inventory.product_id')
        //->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
        //->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
        //->where('prod.segemnt_id', '=', $segment_id)
        //->where('prod.variant_value1','!=','')
        ->whereIn('inventory.le_wh_id',$le_wh_id);

  if (!empty($keyword))
    {
                              $data= $result
                                     -> where(function($query) use ($keyword) {
                              $query->where('prod.product_title', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.sub_category_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.product_class_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.category_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.brand_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.manufacturer_name', 'LIKE', '%' . $keyword . '%')
                                    ->orwhere('cat.meta_keywords', 'LIKE', '%' . $keyword . '%');
    });
    }
    else
    {
    $data = $result;
    }

     if($segment_id)
    {
    
    $data=$data->where('cat.segemnt_id', '=', $segment_id);

    }

  if (!empty($category_id))
    {
    $data = $data->where('cat.product_class_id', '=', $category_id);
    }
   
  if (!empty($brand_id))
    {
    $data = $data->where('cat.brand_id', '=', $brand_id);
    }

    if (!empty($manufacturer_id))
    {
    $data = $data->where('cat.manufacturer_id', '=', $manufacturer_id);
    }
   

  if (!empty($filters))
    {

      $last_result = $data->where('customer_type','=',$customer_type);

   //product filters   
    if (!empty($filters['product_filters']))
      {
      $last_result = $last_result->leftjoin('product_attributes AS pa', 'pa.product_id', '=', 'pif.product_id')
      ->whereIn('pa.attribute_id', [$filters['product_filters']['attribute_id']])
      ->whereIn('value', [$filters['product_filters']['values']]);
      }

 //brand filter  
  if(empty($brand_id))
  {  
    if (!empty($filters['brand']))
      {

      $last_result =  $last_result->whereIn('cat.brand_id',[$filters['brand']['brand_id']]);
      
      }
    }

 // category filter
    if(empty($category_id))
    {
    if (!empty($filters['categories']))
    {
    $last_result = $last_result->whereIn('cat.product_class_id', [$filters['categories']['category_id']]);
    }
    }

    //manufacturer filter
    
    if(empty($manufacturer_id))
    {
    if(!empty($filters['manf']))
    {
    $last_result = $last_result->whereIn('cat.manufacturer_id', [$filters['manf']['maf_id']]);
    }
    }

    if (!empty($filters['margin']))
      {
      $minmargin = $filters['margin']['minvalue'];
      $maxmargin = $filters['margin']['maxvalue'];
      $last_result =  $last_result->whereBetween('psr.margin',array($minmargin,$maxmargin));
      }

    if (!empty($filters['price']))
      {
      $minprice = $filters['price']['minvalue'];
      $maxprice = $filters['price']['maxvalue'];
      $last_result =  $last_result->whereBetween((db::raw('psr.unit_price*psr.pack_size')),array($minprice,$maxprice));
      }

       if (!empty($filters['mrp']))
      {
      $minmrp = $filters['mrp']['minvalue'];
      $maxmrp= $filters['mrp']['maxvalue'];
      $last_result =  $last_result->whereBetween('prod.mrp',array($minmrp,$maxmrp));
      }

       if (!empty($filters['fastmoving']))
      {
      if($filters['fastmoving']['value']=="true")
      {

       $last_result =  $last_result->orderBy('prod.key_value_index', 'ASC');
    
      }
     
     
      }

     //product filters   
    if (!empty($filters['rating']) )
      {
   
       $min=$filters['rating']['minvalue'];
       $max=$filters['rating']['maxvalue'];
       $rating=$this->Review->FilterRatingProducts($min,$max); 

     $last_result= $last_result
      ->whereIn('prod.product_id', $rating);
        
      }

      $last_result= $last_result->get()->all();

    }


    if (empty($filters))
    {
   
     $last_result  = $data->get()->all();

    }

      
      return $last_result[0]->TotalProducts; 
    }



     public function applyFilterTabs($segment_id, $filter_tabs, $offset, $offset_limit,$sort_id,$pincode)
  {

//db::enablequerylog();

  $query = DB::table('vw_cp_products as prod')->select(db::raw("distinct(prod.product_id)"))
  ->join('vw_category_products as cat','cat.product_id','=','prod.product_id')
  ->Join('products_slab_rates as psr','prod.product_id','=','psr.product_id')
  ->Join('inventory','prod.product_id','=','inventory.product_id')
  ->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
  ->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
  //->where('prod.segemnt_id', '=', $segment_id)
  ->where('wh_serviceables.pincode','=',$pincode);
  if (!empty($filter_tabs['categories']['category_id']))
    {
    $last_result = $query->whereIn('cat.product_class_id', [$filter_tabs['categories']['category_id']]);
    }
    else
    {
    $last_result = $query;
    }

      if($segment_id)
    {
    
     $last_result=$last_result->where('cat.segemnt_id', '=', $segment_id);

    }

  if (!empty($filter_tabs['manf']['maf_id']))
    {
    $last_result = $last_result->whereIn('cat.manufacturer_id', [$filter_tabs['manf']['maf_id']]);
    }

  if (!empty($filter_tabs['brand']['brand_id']))
    {
    $last_result = $last_result->whereIn('cat.brand_id', [$filter_tabs['brand']['brand_id']]);
    }

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
        
 
           $last_result  = $last_result
                        ->orderBy($sort_column,$sort_by)
                        ->skip($offset)
                        ->take($offset_limit)
                        ->get()->all();
      
       
      }else{
         $last_result  = $last_result
                         ->skip($offset)
                         ->take($offset_limit)
                         ->get()->all();
      }

  
//print_r(db::getquerylog());exit;
  return $last_result;


  }



 public function applyFilterTabsTotalProducts($segment_id, $filter_tabs,$pincode)
  {

  $query = DB::table('vw_cp_products as prod')->select(db::raw("count(distinct(prod.product_id)) as TotalProducts"))
  ->join('vw_category_products as cat','cat.product_id','=','prod.product_id')
  ->Join('products_slab_rates as psr','prod.product_id','=','psr.product_id')
  ->Join('inventory','prod.product_id','=','inventory.product_id')
  ->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
  ->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
  //->where('prod.segemnt_id', '=', $segment_id)
  ->where('wh_serviceables.pincode','=',$pincode);
  if (!empty($filter_tabs['categories']['category_id']))
    {
    $last_result = $query->whereIn('cat.product_class_id', [$filter_tabs['categories']['category_id']]);
    }
    else
    {
    $last_result = $query;
    }

      if($segment_id)
    {
    
     $last_result=$last_result->where('cat.segemnt_id', '=', $segment_id);

    }

  if (!empty($filter_tabs['manf']['maf_id']))
    {
    $last_result = $last_result->whereIn('cat.manufacturer_id', [$filter_tabs['manf']['maf_id']]);
    }

  if (!empty($filter_tabs['brand']['brand_id']))
    {
    $last_result = $last_result->whereIn('cat.brand_id', [$filter_tabs['brand']['brand_id']]);
    }

  
    $last_result  = $last_result
                         ->get()->all();
    

  return $last_result[0]->TotalProducts;

  // print_r(db::getquerylog());exit;

  }

    

public function getParentChild($productids)
{

 $result=DB::select(DB::Raw( 'SELECT
GROUP_CONCAT(DISTINCT(`products`.`product_id`))  AS `product_id`, 
COUNT(distinct IFNULL(getParentPrdId(products.product_id),products.product_id)) AS  COUNT 
FROM `products`  WHERE products.cp_enabled =1 and (FIND_IN_SET(getParentPrdId(products.product_id),'.$productids.') OR  
FIND_IN_SET(products.product_id,'.$productids.'))'));


return $result;

  

}

/*
    * Function name: getSupplierSearch
    * Description: Used to products based on keyword and warehouse_id
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 29 Sep 2016
    * Modified Date & Reason:
    */

 public function getSupplierSearch($keyword,$le_wh_id)
    {

    //  db::enablequerylog();
      $le=trim($le_wh_id,"'");

        $le=explode(',',$le);

       $result = DB::table('products as prod')
      ->select(db::raw("distinct prod.product_id,prod.product_title as name"))
      ->Join('inventory','prod.product_id','=','inventory.product_id')
      ->where('prod.product_title','LIKE','%'.$keyword.'%')
      ->whereIn('inventory.le_wh_id',$le)
      ->get()->all();

      return $result; 
    }

 public function getHubBrandsManf($hub_id,$flag)
    {

       $result = DB::table('hub_product_mapping')
      ->select(db::raw("IFNULL(GROUP_CONCAT(hub_product_mapping.ref_id),0) as ref_id"));
       if($flag==1)
      {  
      $result->where('hub_product_mapping.ref_type','brands')
      ->whereRaw('FIND_IN_SET(hub_product_mapping.hub_id,'.$hub_id.')');
      }else{
      $result->where('hub_product_mapping.ref_type','manufacturers')
       ->whereRaw('FIND_IN_SET(hub_product_mapping.hub_id,'.$hub_id.')');
      
      }
     $result= $result->first();

      return "'".$result->ref_id."'"; 
    }

     public function getMastDesc()
    {
$result = DB::table('master_lookup as ml')
                       ->select('description')
                       ->where("ml.value", "=", 78001)->first();

      return $result->description; 
    }

      public function getBlockedData($customer_token)
  {

        $beats = $this->_rolerepo->getBeatByUserId($customer_token);

        $spokes = $this->_rolerepo->getSpokesByBeats($beats);

        $hubs = $this->_rolerepo->getSpokesByHubs($spokes);

        $dcs = $this->_rolerepo->getSpokesByDc($hubs);
        $blockedList = $this->_rolerepo->getBlockedList($dcs, $hubs, $spokes, $beats);
        //Log::info('blocked list');
        //Log::info($blockedList);

        return $blockedList;
  }
  public function getRoleFeatures($roles,$text)
   {
    if (!empty($roles)) {
            $results = DB::select(DB::raw("select role_access.role_id,role_access.feature_id, features.name, features.parent_id, features.url, features.icon , features.sort_order FROM role_access left join features on role_access.feature_id = features.feature_id  JOIN features ff
    ON ff.`feature_id` =  features.`parent_id` AND ff.`is_menu`=1 where role_access.role_id IN (" . $roles . ") and features.is_menu = 1 and features.is_active = 1 and features.name like '%".$text."%' and features.url is not null and features.url!='' group by features.feature_id order by features.parent_id ASC, features.sort_order ASC"));
        } else {
            $results = array();
        }
        return $results;
    } 
    
  }   
  
  