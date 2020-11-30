<?php
    /*
        * File name: ShoppingListController.php
        * Description: ShoppingListController.php file is used to handle the request of shoppinglist  and give
        response
        * Author: Ebutor <info@ebutor.com>
        * Copyright: ebutor 2016
        * Version: v1.0
        * Created Date: 15 July 2016
        * Modified Date & Reason:
    */
    namespace App\Modules\Cpmanager\Controllers;
    use DB;
    
    use Session;
    use App\Http\Controllers\BaseController;
    use App\Modules\Cpmanager\Models\ShoppingList;
    use App\Modules\Cpmanager\Models\ShoppingModel;
    use App\Modules\Cpmanager\Models\category;
    use App\Modules\Cpmanager\Models\Review;
    use App\Modules\Cpmanager\Models\catalog;
    use Illuminate\Support\Facades\Input; 
    use Response;
    use Log;
    use Illuminate\Http\Request;
    
   class ShoppingListController extends BaseController
 {
    public function __construct()
        {
        $this->_list = new ShoppingList();
        $this->_shopping = new ShoppingModel();
        $this->category=new category();
        $this->Review = new Review();
         $this->catalog = new catalog();

        }

    /*
    * Function name: getShoppingList
    * Description: getShoppingList used to get the list details of a particular customer
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */
    public  function getShoppingList()
        {
        if (isset($_POST['data']))
            {
            $json = $_POST['data'];
            $array = json_decode($json, true);
            $data = array();
            if (isset($array['flag']))
                {
                if ($array['flag'] != '')
                    {
                    $flag = $array['flag'];
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'Flag not sent',
                        'data' => []
                    )));
                    die;
                    }
                }
              else
                {
                print_r(json_encode(array(
                    'status' => "failed",
                    'message' => 'Flag not sent',
                    'data' => []
                )));
                die;
                }

// to view lists of the customer

            if ($flag == 1)
                {
                if (isset($array['customer_token']) || $array['customer_token'] != '')
                    {
                    $customer_token = $array['customer_token'];
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'Token not sent',
                        'data' => []
                    )));
                    die;
                    }

                $result = $this->_shopping->checkCustomerToken($customer_token);
                
                //log::info($result);
                if ($result[0]->count == 1)
                    {
                    $list_data = $this->_list->ShoppingLists($result[0]->user_id);

                if(!empty($list_data))
                {
                
                    $data = array();
                    $i = 0;
                    foreach($list_data as $key => $value)
                        {
                        $data[$i]['buyer_listing_id'] = $value['_id'];
                        $data[$i]['listing_name'] = $value['listname'];
                        $data[$i]['create_date'] = $value['created_date'];
                        $i++;
                        }

                        
                
                    if (empty($data))
                        {
                        print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'There are no lists found for this customer',
                            'data' => ""
                        )));
                        die;
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'Shopping List',
                            'data' => $data
                        )));
                        die;
                        }
                          }else{

                        print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'There are no lists found for this customer',
                            'data' => ""
                        )));
                        die;
                    }
                    }else{

                          print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'customer_token is not valid',
                            'data' => ""
                        )));
                        die;
                    }
                }

//to view products of particular list
            if ($flag == 2)
                {
                if (isset($array['customer_token']))
                    {
                    if ($array['customer_token'] != '')
                        {
                        $customer_token = $array['customer_token'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'Token not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'Token not sent',
                        'data' => []
                    )));
                    die;
                    }

                if (isset($array['buyer_listing_id']))
                    {
                    if ($array['buyer_listing_id'] != '')
                        {
                        $buyer_listingid = $array['buyer_listing_id'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'buyer_listing_id not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'buyer_listing_id not sent',
                        'data' => []
                    )));
                    die;
                    }

                $result = $this->_shopping->checkCustomerToken($customer_token);
                
                //log::info($result);
                if ($result[0]->count == 1)
                    {
                    $listproduct_data = $this->_list->ListProducts($result[0]->user_id, $buyer_listingid);

                    if(!empty($listproduct_data))
                    {
               
                    $dataprod=array_values(array_unique($listproduct_data[0]['product_id']));
                  //print_r($dataprod);exit;
                   if($dataprod)
                   {
                    $temp = $this->catalog->getProducts($category_id='',$offset='',$offset_limit='',
                        $sort_id='',$customer_token,$api=5,$dataprod,$pincode='');
                    //print_r($data1);exit;

                    //$TotalProducts = sizeof($temp['data']);
                    $allprodId = array();
                    $allprodId = array_values(array_unique($listproduct_data[0]['product_id']));
                    $dataprod = array();
                    $i = 0;
                   

                    $data = $temp;
                   // $data['TotalProducts'] = $TotalProducts;
                    if (!empty($temp))
                        {
                        $data = $temp;
                        //$data['TotalProducts'] = $TotalProducts;
                        print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'Shopping List',
                            'data' => $data
                        )));
                        die;
                        }
                      else
                        {
                        $data = [];
                       // $data['TotalProducts'] = "0";
                        print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'There is no products in list',
                            'data' => $data
                        )));
                        die;
                        }

                    }else{

                     $data = [];
                       // $data['TotalProducts'] = "0";
                        print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'There is no products in list',
                            'data' => $data
                        )));
                        die;

                    }

                           }else{

                        $data = [];
                        //$data['TotalProducts'] = "0";
                        print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'There is no products in list',
                            'data' => $data
                        )));
                        die;
                    }
                    }else{

                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'customer_token is not valid',
                            'data' => ""
                        )));
                        die;
                    }
                }
            }
          else
            {
            $error = "Please pass required parameters";
            print_r(json_encode(array(
                'status' => "failed",
                'message' => $error,
                'data' => ""
            )));
            die;
            }
        }


 /*
    * Function name: productListOperations
    * Description: productListOperations used to do DML opeartion on list
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */
    public  function productListOperations()
        {
        if (isset($_POST['data']))
            {
            $json = $_POST['data'];
            $array = json_decode($json, true);
            $data = array();
            if (isset($array['flag']))
                {
                if ($array['flag'] != '')
                    {
                    $flag = $array['flag'];
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'Flag not sent',
                        'data' => []
                    )));
                    die;
                    }
                }
              else
                {
                print_r(json_encode(array(
                    'status' => "failed",
                    'message' => 'Flag not sent',
                    'data' => []
                )));
                die;
                }

// add/update products in list
            if ($flag == 1)
                {
                if (isset($array['customer_token']))
                    {
                    if ($array['customer_token'] != '')
                        {
                        $customer_token = $array['customer_token'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'Token not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'Token not sent',
                        'data' => []
                    )));
                    die;
                    }

                if (isset($array['buyer_listing_id']))
                    {
                    if ($array['buyer_listing_id'] != '')
                        {
                        $buyer_listingid = $array['buyer_listing_id'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'buyer_listing_id not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'buyer_listing_id not sent',
                        'data' => []
                    )));
                    die;
                    }

                     if (isset($array['product_id']))
                    {
                    if ($array['product_id'] != '')
                        {
                        $product_id = $array['product_id'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'product_id not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'product_id not sent',
                        'data' => []
                    )));
                    die;
                    }

                $result = $this->_shopping->checkCustomerToken($customer_token);
                if ($result[0]->count == 1)
                    {
                    $listproduct_data = $this->_list->ListProducts($result[0]->user_id, $buyer_listingid);
                   
                     if(!empty($listproduct_data))
                     {
                   //log::info($listproduct_data);
                    if(empty(in_array($product_id, $listproduct_data[0]['product_id'])))
                    {
                 // array_push($listproduct_data['product_id'],$product_id);

                  $add_product = $this->_list->addProduct($product_id,$result[0]->user_id,$buyer_listingid);
                    print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'Product was added to the list successfully',
                            'data' => []
                        )));
                        die;     


                    }else{
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'Product is already added',
                            'data' => []
                        )));
                        die;

                    }


                    }else{

                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'unable to fetch data from mongo',
                            'data' => ""
                        )));
                        die;
                    }
                   
                    }else{

                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'customer_token is not valid',
                            'data' => ""
                        )));
                        die;
                    }
                }
// create list
   if ($flag == 2)
                {
                if (isset($array['customer_token']))
                    {
                    if ($array['customer_token'] != '')
                        {
                        $customer_token = $array['customer_token'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'Token not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'Token not sent',
                        'data' => []
                    )));
                    die;
                    }

                if (isset($array['Listname']))
                    {
                    if ($array['Listname'] != '')
                        {
                        $listname = $array['Listname'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'Listname not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'Listname not sent',
                        'data' => []
                    )));
                    die;
                    }

                 
                $result = $this->_shopping->checkCustomerToken($customer_token);
                if ($result[0]->count == 1)
                    {

                    $listproduct_data = $this->_list->checkListName($result[0]->user_id, $listname);

                   
                        //log::info($listproduct_data);
                    if(empty( $listproduct_data))
                    {
                          $list = $this->_list->createList($result[0]->user_id,$listname);
                           $list_data = $this->_list->ShoppingLists($result[0]->user_id);
                           if(!empty($list_data))
                          {
                   
                    $data = array();
                    $i = 0;
                    foreach($list_data as $key => $value)
                        {
                        $data[$i]['buyer_listing_id'] = $value['_id'];
                        $data[$i]['listing_name'] = $value['listname'];
                        $data[$i]['create_date'] = $value['created_date'];
                        $i++;
                        }
                          print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'Shopping list created',
                            'data' => $data
                        )));
                        die;
                    }
                        

                    }else{

                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'listname is already added',
                            'data' => ""
                        )));
                        die;

                    }
                   
                    }else{

                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'customer_token is not valid',
                            'data' => ""
                        )));
                        die;
                    }
                }


// deleteproduct list

   if ($flag == 3)
                {
                if (isset($array['customer_token']))
                    {
                    if ($array['customer_token'] != '')
                        {
                        $customer_token = $array['customer_token'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'Token not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'Token not sent',
                        'data' => []
                    )));
                    die;
                    }

                if (isset($array['buyer_listing_id']))
                    {
                    if ($array['buyer_listing_id'] != '')
                        {
                        $buyer_listing_id = $array['buyer_listing_id'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'buyer_listing_id not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'buyer_listing_id not sent',
                        'data' => []
                    )));
                    die;
                    }

                      if (isset($array['product_id']))
                    {
                    if ($array['product_id'] != '')
                        {
                        $product_id = $array['product_id'];
                        }
                      else
                        {
                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'product_id not sent',
                            'data' => []
                        )));
                        die;
                        }
                    }
                  else
                    {
                    print_r(json_encode(array(
                        'status' => "failed",
                        'message' => 'product_id not sent',
                        'data' => []
                    )));
                    die;
                    }

                 
                $result = $this->_shopping->checkCustomerToken($customer_token);
               if ($result[0]->count == 1)
                    {
                    $listproduct_data = $this->_list->ListProducts($result[0]->user_id, $buyer_listing_id);
                    if(!empty($listproduct_data))
                    {
                    //Log::info($listproduct_data);
                    
                    if(empty(in_array($product_id, $listproduct_data[0]['product_id'])))
                    {
                 // array_push($listproduct_data['product_id'],$product_id);

                 // $add_product = $this->_list->addProduct($product_id,$result[0]->user_id,$buyer_listingid);
                    print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'Product_id is not their in the list',
                            'data' => []
                        )));
                        die;     


                    }else{

                        $this->_list->deleteProduct($product_id,$result[0]->user_id,$buyer_listing_id);
                        print_r(json_encode(array(
                            'status' => "success",
                            'message' => 'Product is deleted  successfully',
                            'data' => []
                        )));
                        die;

                    }
              
                }else{

                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'unable to fetch data from mongo',
                            'data' => ""
                        )));
                        die;
                    }

                }
                   
                    }else{

                        print_r(json_encode(array(
                            'status' => "failed",
                            'message' => 'customer_token is not valid',
                            'data' => ""
                        )));
                        die;
                    }
                }
            



            
          else
            {
            $error = "Please pass required parameters";
            print_r(json_encode(array(
                'status' => "failed",
                'message' => $error,
                'data' => ""
            )));
            die;
            }
        }



  
    }