<?php

namespace App\Modules\Inbound\Controllers;

/*
  Filename : InboundRequestController.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 20-May-2016
  Desc : Controller for products dispaly and select for inbound requests
 */

use View;
use Illuminate\Support\Facades\Session;
use Validator;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use Redirect;
use Title;
use App\Http\Controllers\BaseController;
use App\Modules\Inbound\Models\InboundRequest;
use App\Modules\Inbound\Models\InboundProduct;
use App\Modules\Inbound\Models\InboundProductList;
use App\Modules\Inbound\Models\ProductSellerMapping;
use App\Modules\Inbound\Models\SellerAccount;
use App\Modules\Inbound\Models\Category;
use App\Modules\Inbound\Models\Product;
use App\Modules\Inbound\Models\InboundWmsResponse;
use App\Modules\Inbound\Models\ApiNodeJs;
use App\Modules\Inbound\Models\LegalEntity;
use App\Modules\Inbound\Models\LegalentityWarehouses;
use App\Modules\Inbound\Models\InboundDoc;
use Illuminate\Http\Request;
use File;

class InboundRequestController extends BaseController {

    public function __construct() {
        try {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            parent::Title('Inbound');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        $this->_seller_list = new SellerAccount();
        $this->_mapping_list = new ProductSellerMapping();
        $this->_products_list = new InboundProductList();
        $this->_create_inward_req = new InboundRequest();
        $this->_category = new Category();
        $this->_product = new Product();
        $this->_inbound_wms_response = new InboundWmsResponse();
        $this->_api_node_js = new ApiNodeJs();
        $this->_legal_entity = new LegalEntity();
        $this->_wh_location = new LegalentityWarehouses();
        $this->_inbound_docs = new InboundDoc();
        $this->grid_field_db_match = array(
            'name' => 'name',
            'available_inventory' => 'available_inventory',
            'product_id' => 'product_id',
            'category_id' => 'category_id',
            'brand_id' => 'brand_id',
        );
    }

    public function indexAction() {
        try {
            $breadCrumbs = array('Service Requests' => url('#'), 'Inbound' => 'inbound/index');
            parent::Breadcrumbs($breadCrumbs);

            return View::make('Inbound::index');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function addAction() {
        try {
            $breadCrumbs = array('Service Requests' => url('#'), 'Inbound' => 'inbound/index', 'add' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $le_en_id = Session::get('legal_entity_id');
            if ($le_en_id) {
                $legal_entity_products = $this->productsForLegalEntity($le_en_id);
                $this->truncateProductsMongoDb();
                $this->insertProductsMongoDb($legal_entity_products);
            } else {
                $seller_products = $this->productsForSeller($seller_id);
                $this->truncateProductsMongoDb();
                $this->insertProductsMongoDb($seller_products);
            }

            $pick_up_location = $this->pickUpLocation($le_en_id);
            $delivery_location = $this->deliveyLocation($le_en_id);
            $brands = $this->_product->distinctBrands();
            $categories = $this->_product->distinctCategories();
            return View('Inbound::add')->
                            with(['pickup_location' => $pick_up_location, 'delivery_location' => $delivery_location, 'brands' => $brands, 'categories' => $categories]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function editAction() {
        try {
            return view('Inbound::edit');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function legalEntities($legal_id) {
        try {
            $legal_all = $this->_legal_entity->byId($legal_id);
            return $legal_all;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function sellerAccounts($legalEntityId) {
        try {
            $sel_all = $this->_seller_list->byLegalEntityId($legalEntityId);
            return $sel_all;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function productSellerMapping($sellerId) {
        try {
            $product_id = $this->_mapping_list->productIdBySellerId($sellerId);
            return $product_id;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getSellerId($productId) {
        try {
            $seller_id = $this->_mapping_list->sellerIdByProductId($productId);
            return $seller_id;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getProductsDetails($productId) {
        try {
            $product_list_all = $this->_products_list->productsDisplayForInboundRequest($productId);

            $category_id = $product_list_all['category_id'];
            $category_name = $this->categories($category_id);

            $products_final = array_replace_recursive($product_list_all, $category_name);
            return $products_final;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function categories($categoryId) {
        try {
            $category_names = $this->_category->categoryNameById($categoryId);
            return $category_names;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function pickUpLocation($legalEntityId) {
        try {
            $legal_address = $this->_legal_entity->leAddressOnly($legalEntityId);
            return $legal_address;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function deliveyLocation($dLegalEntityId) {
        try {
            $warehouse_address = $this->_wh_location->whAddressOnly($dLegalEntityId);
            return $warehouse_address;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function productsForLegalEntity($legal_entity_id) {
        try {
            $sellers = $this->sellerAccounts($legal_entity_id);
            $seller_products = array();
            foreach ($sellers as $seller) {
                $seller_products[] = $this->productsForSeller($seller['seller_id']);
            }
            return $seller_products;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function productsForSeller($seller_id) {
        try {
            $product_mapping = array();
            $products = array();
            $products_final = array();
            $product_mapping = $this->productSellerMapping($seller_id);
            foreach ($product_mapping as $product) {
                $products = $this->getProductsDetails($product);
                $products['seller_id'] = $seller_id;
                $products_final[] = $products;
            }
            return $products_final;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function insertProductsMongoDb($productEach) {
        try {
            foreach ($productEach as $products) {
                foreach ($products as $product) {
                    $this->_product = new Product();
                    $this->_product->createProduct($product);
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function truncateProductsMongoDb() {
        try {
            $this->_product->truncate();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function allProductsFromMongoDb(Request $request) {
        try {
            $request_input = $request->input();

            $order_by = '';
            $filter_by = '';
            $brand_id = '';
            $category_id = '';
            $page = $request->input('page');   //Page number
            $page_size = $request->input('pageSize'); //Page size for ajax call

            if (isset($request_input['brandid']) && trim($request_input['brandid']) != '') {
                $brand_id_explode = explode(',', $request_input['brandid']);
                $brand_id_explode_count = count($brand_id_explode);
                for ($i = 0; $i < $brand_id_explode_count; $i++) {
                    $brand_id[] = (int) $brand_id_explode[$i];
                }
            }

            if (isset($request_input['categoryid']) && trim($request_input['categoryid']) != '') {
                $category_id_explode = explode(',', $request_input['categoryid']);
                $category_id_explode_count = count($category_id_explode);
                for ($j = 0; $j < $category_id_explode_count; $j++) {
                    $category_id[] = (int) $category_id_explode[$j];
                }
            }

            if (isset($request_input['$orderby'])) {
                $order_explode = explode(' ', $request_input['$orderby']);
                $order_query_field = $order_explode[0]; //on which field sorting need to be done
                $order_query_type = $order_explode[1]; //sort type asc or desc
                $order_by = $order_query_field . " " . $order_query_type;
            }

            if (isset($request_input['$filter'])) {
                $filter_explode = explode(' and ', $request_input['$filter']);

                foreach ($filter_explode as $filter_each) {
                    $filter_each_explode = explode(' ', $filter_each);
                    $length = count($filter_each_explode);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter_each_explode[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter_each_explode[$length - 2];
                        $filter_query_value = $filter_each_explode[$length - 1];
                    } else {
                        $filter_query_field = $filter_each_explode[0];
                        $filter_query_operator = $filter_each_explode[1];
                        $filter_query_value = $filter_each_explode[2];
                    }

                    $filter_query_field_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
                        //Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

                        if ($filter_query_field_substr == 'startsw') {
                            $filter_query_field_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = $filter_query_field_value_array[1] . '%';

                            foreach ($this->grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $starts_with_value = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                    $filter_by[] = $starts_with_value;
                                } else {
                                    $starts_with_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'endswit') {
                            $filter_query_field_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = '%' . $filter_query_field_value_array[1];

                            foreach ($this->grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $ends_with_value = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                    $filter_by[] = $ends_with_value;
                                } else {
                                    $ends_with_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'tolower') {
                            $filter_query_value_array = explode("'", $filter_query_value);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = $filter_query_value_array[1];
                            if ($filter_query_operator == 'eq') {
                                $like = ' = ';
                            } else {
                                $like = ' != ';
                            }
                            foreach ($this->grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $to_lower_value = $this->grid_field_db_match[$key] . $like . $filter_value;
                                    $filter_by[] = $to_lower_value;
                                } else {
                                    $to_lower_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'indexof') {
                            $filter_query_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_query_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = ' like ';
                            } else {
                                $like = ' not like ';
                            }
                            foreach ($this->grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $indexof_value = $this->grid_field_db_match[$key] . $like . $filter_value;
                                    $filter_by[] = $indexof_value;
                                } else {
                                    $indexof_value = "";
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :
                                $filter_operator = ' = ';
                                break;

                            case 'ne':
                                $filter_operator = ' != ';
                                break;

                            case 'gt' :
                                $filter_operator = ' > ';
                                break;

                            case 'lt' :
                                $filter_operator = ' < ';
                                break;

                            case 'ge' :
                                $filter_operator = ' >= ';
                                break;

                            case 'le' :
                                $filter_operator = ' <= ';
                                break;
                        }

                        if (isset($this->grid_field_db_match[$filter_query_field])) {
                            //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match[$filter_query_field];
                        }

                        $filter_by[] = $filter_field . $filter_operator . $filter_query_value;
                    }
                }
            }

            $mongo_product_list = $this->_product->productListing($page, $page_size, $order_by, $filter_by, $brand_id, $category_id);
            $mongo_product_list_array = $mongo_product_list['result'];

            foreach ($mongo_product_list_array as $mongo_product_each) {
                $new_values['product_id'] = $mongo_product_each['product_id'];
                $new_values['image'] = "<img src=" . $mongo_product_each['image'] . " width='30' height='30'/>";
                $new_values['name'] = $mongo_product_each['name'] . "<br>MRP: " . $mongo_product_each['mrp'];
                $new_values['available_inventory'] = $mongo_product_each['available_inventory'];
                $new_values['sku'] = $mongo_product_each['sku'];
                $new_values['product_flag'] = $mongo_product_each['product_flag'];
                $new_values['category_id'] = $mongo_product_each['category_id'];
                $new_values['brand_id'] = $mongo_product_each['brand_id'];
                $new_values_final[] = $new_values;
            }

            if (isset($new_values_final)) {
                echo json_encode(array('results' => $new_values_final, 'TotalRecordsCount' => $mongo_product_list['count']));
            } else {
                echo json_encode(array('results' => '0', 'TotalRecordsCount' => $mongo_product_list['count']));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateProductFlagMongoDb($productId, $flagValue) {
        try {
            $flag_update = $this->_product->updateProductFlag($productId, $flagValue);
            return $flag_update;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function createInwardRequest(Request $request) {
        try {
            $accepted_extensions = array('txt','pdf','doc','docx','xls','xlsx','csv','jpg','jpeg','png');
            $create_data_details = $request->input();
            $file_data = $request->file('stn_docs');
            $upload_data['file_name'] = $file_data->getClientOriginalName();
            $upload_data['file_extension'] = $file_data->getClientOriginalExtension();

            $Stn_document_size = Input::file('stn_docs')->getSize();
            $Stn_document_size = ($Stn_document_size/1024/1024);
	    
                if(!in_array($upload_data['file_extension'], $accepted_extensions))
                {
                    $response  = ['status'=> 'failed','data'=> null,'message'=> 'invalid File format'];
                    return json_encode($response);
                }

                if($Stn_document_size > 4)
                {
                   $response  = ['status'=> 'failed','data'=> null,'message'=> 'File size should be less than 4MB'];
                    return json_encode($response); 
                }
		
                $inward_request_id = $this->_create_inward_req->createInboundRequest($create_data_details);
            if (!empty($file_data)) {
                   
                    $timestamp = md5(microtime(true));
                    $upload_data['file_name'] = pathinfo($upload_data['file_name'], PATHINFO_FILENAME)."_".$timestamp.".".pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                    $file_data->move(base_path() . '/public/images/upload/', strtolower($upload_data['file_name']));
                    $upload_data['file_location'] = '/images/upload/' . $upload_data['file_name'];
                    $upload_data['inbound_request_id'] = $inward_request_id;
                    $this->_inbound_docs->uploadFile($upload_data);
                }

                    $product_details_array = $create_data_details['product_details'];
                    $product_details_array = json_decode($product_details_array, true);
                    if ($inward_request_id) {
                        foreach ($product_details_array as $create_product_details) {
                        $this->_create_inward_product = new InboundProduct();
                        $this->_create_inward_product->createInboundProducts($inward_request_id, $create_product_details);
                        $prod_details_api[] = array(
                            'prod_number' => $create_product_details['product_id'],
                            'qty' => $create_product_details['product_qty'],
                        );
                    }
                }
		
                $final[] = array(
                    'source_number' => $inward_request_id,
                    'auxilliary_vendor' => '',
                    'facility_num' => 'eButor',
                    'prod_details' => $prod_details_api,
                );
                $params = array('load' => $final);

                $url = "http://10.175.8.12:3000/inbound/createagn";
                $api_result = $this->_api_node_js->nodeJsApi($url, 'POST', $params);
                $api_result_array = json_decode($api_result, true);

                $create_wms_response_result = $this->_inbound_wms_response->createWmsResponse($inward_request_id, $api_result_array);
                if ($create_wms_response_result) {
                    $response  = ['status'=> 'success','message'=> 'Your request has been successfully placed, your request id is '.$inward_request_id];
                    return json_encode($response);
                } else {
                    $response  = ['status'=> 'failed','message'=> 'Your request is unsuccessfully'];
                    return json_encode($response);
                }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
