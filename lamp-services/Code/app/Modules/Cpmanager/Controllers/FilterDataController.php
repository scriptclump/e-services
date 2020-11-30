<?php

namespace App\Modules\Cpmanager\Controllers;
use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Log;
use URL;
use DB;
use Illuminate\Http\Request;
use App\Modules\Cpmanager\Models\FilterDataModel;
use App\Modules\Cpmanager\Models\Review;
use App\Modules\Cpmanager\Models\category;
use App\Http\Controllers\BaseController;



/*
    * Class Name: FilterDataController
    * Description: To get the filter data based on the input
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 11 July 2016
    * Modified Date & Reason:
    */
class FilterDataController extends BaseController {

    
    
    public function __construct() {

         $this->_filter = new FilterDataModel(); 
         $this->_review=new Review();
          $this->category = new category(); 

      }
      

    /*
    * Function Name: getSearchAjax()
    * Description: Used to get autocomplete for search
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 11 July 2016
    * Modified Date & Reason:
    */

    public function getFilters()
  {


            if(isset($_POST['data'])){


                      $json =$_POST['data'];

                      $array = json_decode($json,true);

                      if(isset( $array['category_id'])){

                              $category_id=$array['category_id'];

                                 }else{

                              $category_id='';
                                                          

                           }

                             if(isset( $array['flag'])){

                              $flag=$array['flag'];

                                 }else{

                              $flag='';
                                                          

                           }

                          if(isset( $array['manufacturer_id']))
                          {

                          $manufacturer_id=$array['manufacturer_id'];

                          }else{

                          $manufacturer_id='';
                          }

                            if(isset( $array['brand_id']))
                          {

                          $brand_id=$array['brand_id'];

                          }else{

                          $brand_id='';
                          }


                          if(isset( $array['search'])){

                          $search=$array['search'];

                          }else{

                          $search='';
                          }

                          if(isset( $array['segment_id'])){

                          $segment_id=$array['segment_id'];

                          }else{

                          $segment_id='';

                          }



              if(isset($array['le_wh_id']) && !empty($array['le_wh_id'])){
                    
                    $le_wh_id = $array['le_wh_id'];

                     $le_wh_id=explode(',',$le_wh_id);

                    
                    }else{
                    
                    return Array('status' => 'failed', 'message' => 'le_wh_id Not passed', 'data' =>[]);
                }


                 if(isset($array['customer_type']) && !empty($array['customer_type'])){
                    
                    $customer_type = $array['customer_type'];

                    
                    }else{
                    

                    return Array('status' => 'failed', 'message' => 'customer_type Not passed', 'data' =>[]);
                }




                     if(isset($array['customer_token']) && !empty($array['customer_token'])){
                    
                    $checkCustomerToken = $this->category->checkCustomerToken($array['customer_token']);
                    if($checkCustomerToken>0){
                        $customer_token = $array['customer_token'];
                       // $pincode = $this->category->getPincode($array['customer_token']);
                        }else{
                        $customer_token ='';
                    /*    if(isset($array['pincode'])){
                         $pincode = $array['pincode'];   
                     }else{
                      return Array('status' => 'failed', 'message' => 'Pincode Not valid', 'data' => []);  
                     }*/
                       
                    }
                    }else{
                    $customer_token = "";
                  /*  if(isset($array['pincode'])){
                         $pincode = $array['pincode'];   
                     }else{
                        return Array('status' => 'failed', 'message' => 'Pincode Not valid', 'data' => []);  
                     }*/
                   // $pincode = '';
                }
                  
               
              
                  if (isset($array['segment_id']))
                    {
                    $segment_id = $array['segment_id'];
                    }
                    else
                    {
                    print_r(json_encode(array(
                      'status' => "success",
                      'message' => "Please send segment_id",
                      'data' => []
                    )));
                    die;
                    }



               $filterproducts= $this->_filter->getFilterProducts($category_id,$brand_id,$search
                ,$segment_id,$manufacturer_id,$le_wh_id,$customer_type); 

               
                            if(!empty($filterproducts))
                            {

                            for($k = 0; $k <count($filterproducts); $k++)
                            { 

                        //   $ratings[]=$this->_review->RatingRange($filterproducts[$k]->product_id); 
                            $result[]=$filterproducts[$k]->product_id;

                            }
                           // $products=implode(',', $result);
                             

                          }else{


               print_r(json_encode(array('status'=>"success",'message'=>"getFilters",'data'=> ((object)null))));die;

                          }
 
                          if(!isset($result) || empty($result[0])){
                            $error = "No Products Found under this segment";
                            print_r(json_encode(array('status'=>"success",'message'=>$error,'data'=>"")));die;

                          }
                  $result_manf = $this->_filter->getManufacturerFilter($result);



                  if(!empty($result_manf))
                  {
                  $data['manufacturers'] = $result_manf;

                  $i = 0;
                  foreach($result_manf as $key => $values)
                    {
                    $brand_result = $this->_filter->getBrandFilterTabs($values->manufacturer_id,$result);


                    
                    if(!empty($brand_result))
                    {
                    foreach($brand_result as $key => $value)
                      {
                      $brand[$i]['manufacturer_id'] = $values->manufacturer_id;
                      $brand[$i]['brand_name'] = $value->brand_name;
                      $brand[$i]['brand_id'] = $value->brand_id;
                      $category_result = $this->_filter->getCategoryFilterTabs($value->brand_id,$result);

                      foreach($category_result as $key => $cat)
                        {
                          if($cat->category_id)
                          {
                        $brand[$i]['category_id'] =explode(',',$cat->category_id);
                          }else{
                          
                           $brand[$i]['category_id'] =[];

                          }

                        }

                      $i++;
                      }

                  $data['brand'] = $brand;
                    }else{
                    
                  $data['brand'] = [];

                  $data['categories']=[];

                    }
                    }

                  $cat_result = $this->_filter->getCategories($result);

                  if(!empty($cat_result))
                  {
                  $cat = array();

                  // print_r( $cat_result);

                  $i = 0;
                  foreach($cat_result as $key => $value)
                    {
                    $cat[$i]['category_id'] = $value->product_class_id;
                    $cat[$i]['category_name'] = $value->product_class_name;
                    $brands = $this->_filter->getBrandByCat($value->product_class_id,$result);
                    foreach($brands as $key => $brand)
                      {

                      if($brand->brand_id)
                      {  
                      $cat[$i]['brand_id'] = explode(',',$brand->brand_id);
                    }else{
                     
                     $cat[$i]['brand_id'] = [];

                    }
                      }

                    $manf_data = $this->_filter->getManfByCat($value->product_class_id,$result);
                    foreach($manf_data as $key => $manf)
                      {

                      if($manf->manufacturer_id)  
                      {
                      $cat[$i]['manf_id'] = explode(',',$manf->manufacturer_id);
                    }else{
                   
                   $cat[$i]['manf_id'] =[];

                    }
                      }

                    $i++;
                    }


                  $data['categories'] = $cat;
                  }else{
                  
                  $data['categories']=[];

                  }

                  }else{
                  
                  $data['manufacturers']=[];
                   $data['categories']=[];
                    $data['brand']=[];

                  }

                   //   print_r($data);exit;
                      $filter_data=array();
                        
                        if($flag==2)
                        {


                       //    $data= array();
                    // $range =array();
                    $filtergroup= $this->_filter->getFilters($category_id,$brand_id,$search,$segment_id,
                      $manufacturer_id,$le_wh_id); 
                     
                    for ($k = 0; $k <count($filtergroup); $k++) 
                          {
                            
                           $filter_data[$k]['attribute_id'] = $filtergroup[$k]->attribute_id;
                           $filter_data[$k]['name'] = $filtergroup[$k]->name;
                           $filter_data[$k]['type']='checkbox';

                           $filter=$this->_filter->getFilterValue($filtergroup[$k]->attribute_id);

                           for($j = 0; $j <count($filter); $j++)
                            { 
                                  $filter_data[$k]['option'][$j]['filter_id']="";

                                  $filter_data[$k]['option'][$j]['filtername']=$filter[$j]->value;

                                }

                        }

                           $ratings=$this->_review->RatingRange($result); 

                          if($ratings[0]>0)
                           {
                           $filter_data[$k]['attribute_id'] = "";
                           $filter_data[$k]['name'] = "rating";
                           $filter_data[$k]['type']='range';
                           $filter_data[$k]['option'][0]['minvalue']=$ratings[0];
                           $filter_data[$k]['option'][0]['maxvalue']=$ratings[1];

                         }

                         $k++;


                       }

                       if($flag=="")
                       {
                        
                        $k=0;

                       }

                 //  $filter_data=array();
             
                           $filterrange=$this->_filter->getFilterRanges($result); 
                           
                      //   $counter++;
                           if($filterrange[0]->minprice!='')
                           {

                           $filter_data[$k]['attribute_id'] = "";
                           $filter_data[$k]['name'] = "price";
                           $filter_data[$k]['type']='range';
                           $filter_data[$k]['option'][0]['minvalue']=$filterrange[0]->minprice;
                           $filter_data[$k]['option'][0]['maxvalue']=$filterrange[0]->maxprice;
                         }

                         $k++;
                       /*  else{
                           $data[$i]['attribute_id'] = "";
                           $data[$i]['name'] = "price";
                           $data[$i]['type']='range';
                           $data[$i]['option']=[];


                         }*/
                       
                      ///  $counter=$counter+1;

                           if($filterrange[0]->minmargin>0)
                           {
                           $filter_data[$k]['attribute_id'] = "";
                           $filter_data[$k]['name'] = "margin";
                           $filter_data[$k]['type']='range';
                           $filter_data[$k]['option'][0]['minvalue']=$filterrange[0]->minmargin;
                           $filter_data[$k]['option'][0]['maxvalue']=$filterrange[0]->maxmargin;

                         }
                       $k++;
                           

                         ///  $counter=$counter+1;

                           if($filterrange[0]->minmrp>0)
                           {
                           $filter_data[$k]['attribute_id'] = "";
                           $filter_data[$k]['name'] = "mrp";
                           $filter_data[$k]['type']='range';
                           $filter_data[$k]['option'][0]['minvalue']=$filterrange[0]->minmrp;
                           $filter_data[$k]['option'][0]['maxvalue']=$filterrange[0]->maxmrp;

                         }

                        $k++;

                       
                  $data['otherfilters']=array_values($filter_data);

       
                  if(!empty($data))
                  {

                  print_r(json_encode(array(
                    'status' => "success",
                    'message' => "getfilter",
                    'data' => $data
                  )));
                  die;
                 }else{

                 
                  print_r(json_encode(array(
                    'status' => "success",
                    'message' => "getfilter",
                    'data' => ((object)null)
                  )));
                  die;

                 }


                       }else{
            $error = "Please pass required parameters";
            print_r(json_encode(array('status'=>"failed",'message'=>$error,'data'=>"")));die;

                       }
               


                       }

    
      }
    