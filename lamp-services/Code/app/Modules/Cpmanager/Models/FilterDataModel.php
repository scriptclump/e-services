<?php
  namespace App\Modules\Cpmanager\Models;
  
  
  use \DB;
  class FilterDataModel extends \Eloquent {
 
   /*
      * Function Name: getFilters
      * Description: getFilters function is used to get filter groupname
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 11 July 2016
      * Modified Date & Reason:
    */
    public function getFilters($category_id,$brand_id,$search,$segment_id,$manufacturer_id,$le_wh_id)
    {
        // db::enableQueryLog(); 

                     /* $getColums=DB::Table('attributes as att')
                      ->select( DB::raw('distinct att.name  ,att.attribute_id'))
                      ->join('attribute_set_mapping as asm','att.attribute_id','=','asm.attribute_id')
                      ->leftjoin('product_attributes AS pa','pa.attribute_id', '=', 'asm.attribute_id')
                      ->leftjoin('products as p','p.product_id','=' ,'pa.product_id' )
                      ->join('mp_product_add_update as mp','mp.product_id','=','p.product_id')
                      ->leftjoin('mp_category_mapping as cm','p.category_id','=','cm.category_id')
                      ->leftjoin('mp_categories as cat','cat.mp_category_id','=','cm.mp_category_id')
                      ->leftJoin('segment_mapping as sm','cat.mp_category_id','=','sm.mp_category_id')
                      ->where(['mp.mp_id'=>1,'mp.is_added'=>1,'p.is_active'=>1,'asm.is_filterable'=>1]);*/

                      $getColums=DB::Table('attributes as att')
                      ->select( DB::raw('distinct att.name  ,att.attribute_id'))
                      ->join('attribute_set_mapping as asm','att.attribute_id','=','asm.attribute_id')
                       ->leftjoin('product_attributes AS pa','pa.attribute_set_id', '=', 'asm.attribute_set_id')
                      ->leftjoin('vw_category_products as p','p.product_id','=' ,'pa.product_id' )
                      ->leftjoin('vw_cp_products as prod','p.product_id','=' ,'prod.product_id' )
                      //->Join('vw_product_slab_rates as psr','prod.product_id','=','psr.product_id')
                     
                      ->Join('inventory','p.product_id','=','inventory.product_id')
                     // ->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
                     // ->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
                     // ->where('wh_serviceables.pincode','=',$pincode)
                      ->whereIn('inventory.le_wh_id',$le_wh_id)
                      ->where('asm.is_filterable','=',1);
                      //->where('prod.variant_value1','!=','');
               

                      if(!empty($category_id)){

                      $query= $getColums  
                              ->where('p.product_class_id','=',$category_id);
                      }else{
                           
                           $query=$getColums;
                      }


                       if(!empty($brand_id)){
                      $query= $getColums
                       ->where('p.brand_id','=',$brand_id);
                       
                      }else{
                           $query=$getColums;
                      }


                       if(!empty($manufacturer_id)){
                      $query= $getColums
                       ->where('p.manufacturer_id','=',$manufacturer_id);
                       
                      }else{
                           $query=$getColums;
                      }

                        if(!empty($search)){

                      $words =  preg_replace('/[\s\.\-\(\)]+/', ' ',$search);

                      $query= $getColums
                                     -> where(function($query) use ($words) {
                              $query->where('prod.product_title', 'LIKE','%'.$words.'%')
                                ->orwhere('p.sub_category_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.product_class_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.category_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.brand_name', 'LIKE', '%'.$words.'%')
                                ->orwhere('p.manufacturer_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.meta_keywords', 'LIKE','%'.$words.'%');
    });

                       
                      }else{
                           $query=$getColums;
                      }
//commenting segment id since products are to be displayed irrespective to customer segment 
                        /*if(!empty($segment_id)){
                      $result= $query
                       ->where('p.segemnt_id','=',$segment_id);

                      }else{*/
                           $result=$query;
                      //}

         
              return $result->groupBy('p.product_id')->get()->all();

             //  print_r(db::getQueryLog());


    }

    /*
      * Function Name: getFilterValue
      * Description: getFilterValue function is used to get filtervalues
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 11 July 2016
      * Modified Date & Reason:
    */
 

       public function getFilterValue($attribute_id)
         {

                       $attribute_value =  DB::table('product_attributes as pa')
                            ->select(DB::Raw("distinct value"))
                            ->where("attribute_id",'=',$attribute_id)
                            ->where("value",'!=','')
                            ->get()->all();       
                                  
                            return $attribute_value;

       }

/*
      * Function Name: getFilterProducts
      * Description: getFilterProducts used to get the product_id
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 12 July 2016
      * Modified Date & Reason:
    */
 

     public function getFilterProducts($category_id,$brand_id,$search,$segment_id,$manufacturer_id,$le_wh_id,$customer_type)
    {  
   //  DB::enableQueryLog();

      

                      /*$getColums=DB::Table('vw_category_products as p')

                      ->select(DB::raw('distinct p.product_id'))
                       ->leftjoin('vw_cp_products as prod','p.product_id','=' ,'prod.product_id' )
                     ->leftjoin('vw_product_slab_rates as psr',function ($join) use ($customer_type)
                     {
                     
                      $join->on('prod.product_id', '=', 'psr.product_id')
                           ->where('psr.customer_type', '=', $customer_type);


                     })
                       ->Join('inventory','p.product_id','=','inventory.product_id')
                           // ->Join('legalentity_warehouses','inventory.le_wh_id','=','legalentity_warehouses.le_wh_id')
                           // ->Join('wh_serviceables','legalentity_warehouses.pincode','=','wh_serviceables.pincode')
                            ->whereIn('inventory.le_wh_id',$le_wh_id);
                           //  ->where('psr.customer_type','=',$customer_type);
                           // ->where('wh_serviceables.pincode','=',$pincode);
                          //  ->where('prod.variant_value1','!=','');
                      

                    //  print_r(DB::getQueryLog());

                         if(!empty($category_id)){
                      $query= $getColums  
                       ->where('p.product_class_id','=',$category_id);
                       

                      }else{
                           $query=$getColums;
                      }

                     

                       if(!empty($brand_id)){
                      $query= $getColums
                       ->where('p.brand_id','=',$brand_id);
                       
                      }else{
                           $query=$getColums;
                      }



                       if(!empty($manufacturer_id)){
                      $query= $getColums
                       ->where('p.manufacturer_id','=',$manufacturer_id);
                       
                      }else{
                           $query=$getColums;
                      }
                      

                        if(!empty($search)){
                      $words =  preg_replace('/[\s\.\-\(\)]+/', ' ',$search);
                      $query= $getColums
                             -> where(function($query) use ($words) {
                              $query->where('prod.product_title', 'LIKE','%'.$words.'%')
                                ->orwhere('p.sub_category_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.product_class_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.category_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.brand_name', 'LIKE', '%'.$words.'%')
                                ->orwhere('p.manufacturer_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.meta_keywords', 'LIKE','%'.$words.'%');
                      });

                       
                      }else{
                           $query=$getColums;
                      }
                   

                       if(!empty($segment_id)){
                      $result= $query
                       ->where('p.segemnt_id','=',$segment_id);

                      }else{
                           $result=$query;
                      }*/

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
                          });
                          }
                          else
                          {
                          $data = $result;
                          }
                         /* if($segment_id)
                          {
                          
                          $data=$data->where('pif.segment_id', '=', $segment_id);

                          }*/// since we need to show brands,categories,manufactures irrespective of segment this code is commented


                      $data=$result->get()->all();
 
 //print_r(db::getQueryLog());exit;
                     return $data;



    }


      /*
      * Function Name: getFilterRanges
      * Description: getFilterRanges function is used to get filter ranges
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 11 July 2016
      * Modified Date & Reason:
    */
 

       public function getFilterRanges($products)
         {
          db::enableQueryLog();

                       /*$range =  DB::table('vw_cp_products as p')
                            ->select(DB::Raw("min(psr.unit_price*psr.pack_size) as minprice,
                      max(psr.unit_price*psr.pack_size) as maxprice,min(psr.margin) as minmargin,
                       max(psr.margin) as maxmargin,min(p.mrp) as minmrp
                      ,max(p.mrp) as maxmrp"))
                             ->join('vw_product_slab_rates as psr','p.product_id','=','psr.product_id')
                            ->whereIn("p.product_id",$products)
                            
                            ->get()->all();*/

                            $range =  DB::table('vw_cp_products as p')
                            ->select(DB::Raw("min(psr.unit_price*psr.pack_size) as minprice,
                      max(psr.unit_price*psr.pack_size) as maxprice,min(psr.margin) as minmargin,
                       max(psr.margin) as maxmargin,min(p.mrp) as minmrp
                      ,max(p.mrp) as maxmrp"))
                             ->join('product_slab_flat as psr','p.product_id','=','psr.product_id')
                            ->whereIn("p.product_id",$products)
                            
                            ->get()->all();       
                        
                            return $range;

       }



      /*
      * Function Name: getBrandFilter
      * Description: getBrandFilter function is used to get brand filter
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 11 aug 2016
      * Modified Date & Reason:
    */
 

       public function getBrandFilter($manufacturer_id,$category_id,$search,$segment_id)
         {
          db::enableQueryLog();

                       $getColums=  DB::table('vw_category_products as p')
                            ->select(DB::Raw("distinct p.brand_id,p.brand_name"))
                            ->leftjoin('vw_cp_products as prod','p.product_id','=' ,'prod.product_id' );
                            // ->join('vw_product_slab_rates as psr','p.product_id','=','psr.product_id')
                            // ->where('prod.variant_value1','!=','');
                           
              if(!empty($category_id)){
                      $query= $getColums  
                       ->where('p.product_class_id','=',$category_id);
                       
                      }else{
                           $query=$getColums;
                      }

                     

                       if(!empty($manufacturer_id)){
                      $query= $getColums
                       ->where('p.manufacturer_id','=',$manufacturer_id);
                       
                      }else{
                           $query=$getColums;
                      }
                      

                        if(!empty($search)){
                      $words =  preg_replace('/[\s\.\-\(\)]+/', ' ',$search);
                      $query= $getColums
                             -> where(function($query) use ($words) {
                              $query->where('prod.product_title', 'LIKE','%'.$words.'%')
                                ->orwhere('p.sub_category_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.product_class_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.category_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.brand_name', 'LIKE', '%'.$words.'%')
                                ->orwhere('p.manufacturer_name', 'LIKE','%'.$words.'%')
                                ->orwhere('p.meta_keywords', 'LIKE','%'.$words.'%');
                      });

                       
                      }else{
                           $query=$getColums;
                      }
                   
//commenting segment code because products are to be shown irrespective to customer segments
                       /*if(!empty($segment_id)){
                      $result= $query
                       ->where('p.segemnt_id','=',$segment_id);

                      }else{*/
                           $result=$query;
                     // }

                      return $result->get()->all();

                   //    print_r(db::getQueryLog());
                           
       }

   /*
      * Function Name: getmanufacturer based on segments
      * Description: getmanufacturer function is used to get manufacturers based on segments
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 12 Aug 2016
      * Modified Date & Reason:
    */
 

       public function getManufacturerFilter($result)
         {

                       /*$manuf_data =  DB::table('products_inventory_flat as p')
                            ->select(DB::Raw("distinct manufacturer_id,manufacturer_name"))
                            ->whereIn("p.product_id",$result)
                            ->get()->all();*/
                        $result=implode(',', $result);    
                        $manuf_data="SELECT DISTINCT manufacturer_id,manufacturer_name FROM `products_inventory_flat` AS `p` WHERE `p`.`product_id` IN (".$result.")";
                        $manuf_data=DB::select(DB::raw($manuf_data));                           
                            return $manuf_data;

       }

      /*
      * Function Name: getBrandFilterTabs
      * Description: getBrandFilterTabs function is used to get brands based on manufacturer
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 12 Aug 2016
      * Modified Date & Reason:
    */
 

       public function getBrandFilterTabs($manufacturer_id,$result)
        {

         /*              $brand_data =  DB::table('products_inventory_flat as p')
                            ->select(DB::Raw("distinct p.brand_id,p.brand_name"))
                            ->where("manufacturer_id",$manufacturer_id)
                            ->whereIn("p.product_i",$result)
                            ->get()->all();*/
                            $result=implode(',', $result);
                        $brand_data="SELECT DISTINCT p.brand_id,p.brand_name FROM `products_inventory_flat` AS `p` WHERE `manufacturer_id` = ".$manufacturer_id." AND `p`.`product_id` IN (".$result.")";
                        $brand_data=DB::select(DB::raw($brand_data));           
                          
                            return $brand_data;

       }

 /*
      * Function Name: getCategoryFilterTabs
      * Description: getCategoryFilterTabs function is used to categories based on brands
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 12 Aug 2016
      * Modified Date & Reason:
    */
 

       public function getCategoryFilterTabs($brand_id,$result)
        {
                        

                        /*$cat_data =  DB::table('products_inventory_flat as p')
                            ->select(DB::Raw("  group_concat(distinct p.product_class_id separator ',') 
                              as category_id"))
                            ->whereIn("p.product_i",$result)
                            ->where("p.brand_id",$brand_id)
                            ->get()->all();*/  
                          $result=implode(',', $result);
                          $cat_data="SELECT GROUP_CONCAT(DISTINCT p.product_class_id SEPARATOR ',') 
AS category_id FROM `products_inventory_flat` AS `p` WHERE `p`.`product_id` IN (".$result.") AND `p`.`brand_id` = ".$brand_id;
                          $cat_data=DB::select(DB::raw($cat_data));                  
                          
                            return $cat_data;

       }

        /*
      * Function Name: getCategories
      * Description: getCategories function is used to categories based on products
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 12 Aug 2016
      * Modified Date & Reason:
    */
 

       public function getCategories($result)
        {
                        

                        /*$cat =  DB::table('products_inventory_flat as p')
                            ->select(DB::Raw("distinct p.product_class_id,p.product_class_name"))
                            ->whereIn("p.product_id",$result)
                            ->get()->all();*/
                            $result=implode(',', $result);    
                        $cat="SELECT DISTINCT p.product_class_id,p.product_class_name FROM `products_inventory_flat` AS `p` WHERE `p`.`product_id` IN (".$result.")";
                        $cat=DB::select(DB::raw($cat));       
                          
                            return $cat;

       }
       /*
      * Function Name: getBrandByCat
      * Description: getBrandByCat function is used to get brand by cat
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 12 Aug 2016
      * Modified Date & Reason:
    */
 

       public function getBrandByCat($category_id,$result)
        {
                        

                        /*$cat =  DB::table('products_inventory_flat as p')
                            ->select(DB::Raw(" group_concat(distinct p.brand_id separator ',')
                              as brand_id"))
                            ->where("p.product_class_id",$category_id)
                            ->whereIn("p.product_id",$result)
                            ->get()->all();*/
                            $result=implode(',', $result);
                            $cat = "SELECT GROUP_CONCAT(DISTINCT p.brand_id SEPARATOR ',')
AS brand_id FROM `products_inventory_flat` AS `p` WHERE `p`.`product_class_id` = ".$category_id." AND `p`.`product_id` IN (".$result.")";      
                        $cat=DB::select(DB::raw($cat));  
                            return $cat;

       }
 
        /*
      * Function Name: getManfByCat
      * Description: getManfByCat function is used to get manufacturer by cat
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 12 Aug 2016
      * Modified Date & Reason:
    */
 

       public function getManfByCat($category_id,$result)
        {
                        

                        /*$cat =  DB::table('products_inventory_flat as p')
                            ->select(DB::Raw("group_concat(distinct p.manufacturer_id separator ',')
                              as manufacturer_id"))
                            ->where("p.product_class_id",$category_id)
                             ->whereIn("p.product_i",$result)
                            ->get()->all();*/
                            $result=implode(',', $result);
                          $cat="SELECT GROUP_CONCAT(DISTINCT p.manufacturer_id SEPARATOR ',')
AS manufacturer_id FROM `products_inventory_flat` AS `p` WHERE `p`.`product_class_id` = ".$category_id." AND `p`.`product_id` IN (".$result.")";
                          $cat=DB::select(DB::raw($cat));         
                          
                            return $cat;

       }
    
  }   
  