<?php
namespace App\Modules\Cpmanager\Controllers;
use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Log;
use Lang;
use URL;
use DB;
use Illuminate\Http\Request;
use App\Modules\Cpmanager\Models\SearchModel;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Cpmanager\Models\Review;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\catalog;
use App\Central\Repositories\RoleRepo;


/*
    * Class Name: SearchController
    * Description: To get the search data
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 24 June 2016
    * Modified Date & Reason:
    */
class SearchController extends BaseController {

    
    
    public function __construct() {

         $this->search = new SearchModel();
         $this->categoryModel = new CategoryModel();
         $this->Review = new Review();
         $this->catalog = new catalog();
          $this->_rolerepo = new RoleRepo();

      }
      

    /*
    * Function Name: getSearchAjax
    * Description: Used to get autocomplete for search
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 24 June 2016
    * Modified Date & Reason:
    */

    public function getSearchAjax()
            
  {
       if(isset($_POST['data']))
       {
          $json =$_POST['data'];
           $decode_data = json_decode($json,true);
           $hub_id=0;
           if (isset($decode_data['keyword'])) {
                if ($decode_data['keyword'] != '') {
                    $keyword = $decode_data['keyword'];
                } else {
                    return json_encode(array('status' => "failed", 'message' => Lang::get('cp_messages.keyword'), 'data' => ""));
                }
            } else {
                return json_encode(array('status' => "failed", 'message' => Lang::get('cp_messages.keyword'), 'data' => ""));
            }
            if (isset($decode_data['customer_type']) && !empty($decode_data['customer_type'])) {
                $customer_type = $decode_data['customer_type'];
            } else {
                return Array('status' => 'failed', 'message' => Lang::get('cp_messages.customer_type'), 'data' => "");
            }
            if (isset($decode_data['le_wh_id']) && !empty($decode_data['le_wh_id'])) {
                $le_wh_id = $decode_data['le_wh_id'];
                $le_wh_id = "'" . $le_wh_id . "'";
            } else {
                return Array('status' => 'failed', 'message' => Lang::get('cp_messages.le_wh_id'), 'data' => "");
            }
            if (isset($decode_data['segment_id']) && !empty($decode_data['segment_id'])) {
                $segment_id = $decode_data['segment_id'];
            } else {
                $segment_id = '';
            }
            if (isset($decode_data['customer_token']) && !empty($decode_data['customer_token'])) {

                $checkCustomerToken = $this->categoryModel->checkCustomerToken($decode_data['customer_token']);
                if ($checkCustomerToken > 0) {
                    $customer_token = $decode_data['customer_token'];
                    if (isset($decode_data['hub_id'])) {
                        $hub_id = $decode_data['hub_id'];
                    } else {
                        $hub_id = $this->_rolerepo->getUserHubId($customer_token);
                    }
                } else {
                    return Array('status' => 'session', 'message' => Lang::get('cp_messages.InvalidCustomerToken'), 'data' => "");
                }
            }  else {
                $customer_token = "";
                $hub_id = 0;
            }
            if (isset($decode_data['flag'])) {
                $flag = $decode_data['flag'];
            } else {
                $flag = '';
            }

            $array = array();
            $data = array();
            if ($flag == 1) {
                $data = $this->search->getSupplierSearch($keyword, $le_wh_id);
            } else
          {
              $blockedList = $this->search->getBlockedData($customer_token);
               $product= $this->search->getSearchAjaxProduct($keyword,$le_wh_id,$segment_id,$blockedList,$customer_type);
               $brand= $this->search->getSearchAjaxBrand($keyword,$le_wh_id,$segment_id,$blockedList,$customer_type);
               $category=$this->search->getSearchAjaxCategory($keyword,$le_wh_id,$segment_id,$customer_type);
               $manufacturer=$this->search->getSearchAjaxManufacturer($keyword,$le_wh_id,$segment_id,$blockedList,$customer_type);
               $array[]=$category;
               $array[]=$brand;
               $array[]=$manufacturer;
               $array[]=$product;
                    $i=0;
                    foreach ($array as $values) 
                    {
                       foreach ($values as $value) {

                                $value->name = str_replace("amp;", "",str_replace("&amp;","&", $value->name));
                                $data['data'][$i] = $value; 
                                 $i++;             
                                  } 
                            }

                  }
                    if(!empty($data))
                    {
                  return json_encode(array('status'=>"success",'message'=>"getSearchAjax",'data'=>$data));die;

                      }else{

                  return json_encode(array('status'=>"success",'message'=>Lang::get('cp_messages.nodata'),'data'=>[]));die;

                     }
                       }else{
           return json_encode(array('status'=>"failed",'message'=>Lang::get('cp_messages.err_data'),'data'=>""));die;
                       }
                       }


 /*
    * Function Name: getSearch
    * Description: used to get data based on the keyword sent
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 24 June 2016
    * Modified Date & Reason:
    */

  public function getSearch()
  {
  if (isset($_POST['data']))
    {
    $json = $_POST['data'];
    $decode_data = json_decode($json, true);
    $hub_id=0;
    
    if (isset($decode_data['offset']) && $decode_data['offset'] != null && $decode_data['offset'] >= 0)
      {
      $offset = $decode_data['offset'];
      }
      else
      {
      $status = "failed";
     // $message = 'Offset not valid.';
      $data = [];
      print_r(json_encode(array(
        'status' => $status,
        'message' => Lang::get('cp_messages.InvalidOffset'),
        'data' => $data
      )));
      die;
      }

    if (isset($decode_data['offset_limit']) && $decode_data['offset_limit'] != null && $decode_data['offset_limit'] >= 0)
      {
      $offset_limit = $decode_data['offset_limit'];
      }
      else
      {
      $status = "failed";
     // $message = 'offset_limit not valid.';
      $data = [];
      print_r(json_encode(array(
        'status' => $status,
        'message' => Lang::get('cp_messages.InvalidLimit'),
        'data' => $data
      )));
      die;
      }

         if(isset($decode_data['le_wh_id']) && !empty($decode_data['le_wh_id'])){
                    
                    $le_wh_id = $decode_data['le_wh_id'];

                    }else{
                    
                    return Array('status' => 'failed', 'message' =>  Lang::get('cp_messages.le_wh_id'), 'data' =>[]);
                }

                     if(isset($decode_data['customer_token']) && !empty($decode_data['customer_token'])){
                    
                    $checkCustomerToken = $this->categoryModel->checkCustomerToken($decode_data['customer_token']);
                    if($checkCustomerToken>0){
                        $customer_token = $decode_data['customer_token'];
                           }else{

                    return Array('status' => 'session', 'message' => Lang::get('cp_messages.InvalidCustomerToken'), 'data' => []);
             
                  
                    }
                    }else{
                    $customer_token = "";
                }

    if (isset($decode_data['sort_id']) && !empty($decode_data['sort_id']))
      {
      $sort_id = $decode_data['sort_id'];
      }
      else
      {
      $sort_id = "";
      }

      if (isset($decode_data['filters']))
        {
        $filters = $decode_data['filters'];
        }
        else
        {
        $filters = '';
        }

      if (isset($decode_data['segment_id']))
        {
        $segment_id = $decode_data['segment_id'];
        }
        else
        {
        $segment_id = '';
        }

      if (isset($decode_data['category_id']))
        {
        $category_id = $decode_data['category_id'];
        }
        else
        {
        $category_id = '';
        }

      if (isset($decode_data['brand_id']))
        {
        $brand_id = $decode_data['brand_id'];
        }
        else
        {
        $brand_id = '';
        }


      if (isset($decode_data['manufacturer_id']))
        {
        $manufacturer_id = $decode_data['manufacturer_id'];
        }
        else
        {
        $manufacturer_id = '';
        }

      if (isset($decode_data['keyword']))
        {
        $keyword = $decode_data['keyword'];
        }
        else
        {
        $keyword = '';
        }

        if (isset($decode_data['customer_type']))
        {
        $customer_type = $decode_data['customer_type'];
        }
        else
        {
        $customer_type = '';
        }
        $blockedList = $this->search->getBlockedData($customer_token);
        $prodData = $this->search->getSearch($keyword, $segment_id, $category_id, 
        $brand_id,$manufacturer_id, $filters, $offset, $offset_limit, $sort_id,$le_wh_id,$customer_type,$blockedList);

      if (!empty($prodData))
        {
        $status = "success";
        $message = 'search';
        if($prodData[0]->product_id)
        {
          
          $prods = "'".$prodData[0]->product_id."'";

      $totaldata = $this->search->getParentChild($prods);
     

        $data['product_id'] = $totaldata[0]->product_id;
        $data['count'] = $totaldata[0]->COUNT;
      }
      else
        {
        $status = "success";
        $message = Lang::get('cp_messages.noprod');
        $data = ((object) null);
        }
       // $result[]=$data;

        }
        else
        {
        $status = "success";
        $message = Lang::get('cp_messages.noprod');
        $data = ((object) null);
        }

      print_r(json_encode(array(
        'status' => $status,
        'message' => $message,
        'data' => $data
      )));
      die;
      
    }
    else
    {
   
    print_r(json_encode(array(
      'status' => "failed",
      'message' => Lang::get('cp_messages.err_data'),
      'data' => ""
    )));
    die;
    }
  }
  public function getFeaturesBySearch(){
    $data = Input::all();
    //print_r($data);exit;
    $roles = session::get('roles');
    $features = $this->search->getRoleFeatures($roles,$data['text']);
    $list = '';
    if(count($features)>0){
      for($index=0;$index<count($features);$index++){
          $url = (isset($features[$index]->url) && $features[$index]->url!='') ?URL::asset($features[$index]->url): 'javascript:void(0);';
          $list = $list. '<li class="search_result_item"> 
                            <a style="color:black"  href="'.$url.'" >
                              <i class="fa fa-angle-right"></i> '.$features[$index]->name.'
                            </a>
                          </li>';
      }
    }
    return array('status' => "success", 'message' => "response", 'data' => $list);
  }
  
    
    
}

