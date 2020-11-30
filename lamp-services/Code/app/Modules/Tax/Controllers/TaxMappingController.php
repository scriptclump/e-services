<?php

namespace App\Modules\Tax\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

use View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Title;
use Redirect;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Modules\Tax\Models\TaxClass;
use App\Modules\Tax\Models\ClassTaxMap;
use App\Modules\Tax\Models\Product;
use App\Modules\Tax\Models\Brand;
use App\Modules\Tax\Models\Category;
use App\Modules\Tax\Models\Zone;
use App\Modules\Tax\Models\MasterLookUp;
use App\Central\Repositories\RoleRepo;
use App\Modules\Tax\Controllers\TaxApprovalController;
// use App\Central\Repositories\ErrorLoggingRepo;
use Log;
use Excel;
use Illuminate\Support\Facades\Config;
use App\Modules\Tax\Models\ReadLogs;
use Carbon\Carbon;
use Notifications;
use UserActivity;
use App\Central\Repositories\ProductRepo;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use DB;

class TaxMappingController extends BaseController {

    public function __construct() {
        $this->_taxclass = new TaxClass();
        $this->_taxmap = new ClassTaxMap();
        $this->_product = new Product();
        $this->_zone = new Zone();
        $this->_roleRepo = new RoleRepo();
        $this->_brand = new Brand();
        $this->_category = new Category();
        $this->_readlogs = new ReadLogs();
        $this->_masterlookup = new MasterLookUp();
        $this->_taxworkflow = new TaxApprovalController();
        // $this->_errorlogging = new ErrorLoggingRepo();
        $this->produc_grid_field_db_match = array(
            'product_id' => 'product_id',
            'product_title' => 'product_title',
            'category_id' => 'category_id',
            'cat_name' => 'cat_name',
            'sku' => 'sku',
            'brand_name' => 'brand_name',
        );

        try {
           $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                parent::Title('Tax Mapping - Ebutor');
                $access = $this->_roleRepo->checkPermissionByFeatureCode('PTM001');
                if (!$access) {
                    Redirect::to('/')->send();
                    die();
                }
                return $next($request);
            });    
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function dashboardAction() {
        try {
        $breadCrumbs = array('Home' => url('/'), 'Products' => url('/'), 'Tax Mapping' => url('#'));
        parent::Breadcrumbs($breadCrumbs);
            $state_id = array();
            $states = $this->_zone->allStates();
            $allBrands = $this->_brand->getBrandList();

            $allCats = $this->_category->getCategoryList();
            $alltaxes = $this->_masterlookup->getTaxTypes();
            sort($alltaxes);

            //Adding 'Action' item in array for Modal Table.
            $taxArr = array();
            $taxArr['master_lookup_name'] = 'Action';
            $taxArr['master_lookup_id'] = '0';
            $alltaxes[] = $taxArr;
            
            foreach ($states as $each_state) {
                $state_id['states'][] = $each_state['zone_id'];
            }
            $state_wise_tax_classes = $this->stateWiseTaxClasses();

            //Check ALL Accesses here
            $gridCellUpdate = $this->_roleRepo->checkPermissionByFeatureCode('PTM002');
            $taxMapImportAccess = $this->_roleRepo->checkPermissionByFeatureCode('PTM004');


            return View::make('Tax::dashboardMapTax')->with([
                        'state_wise_tax_classes' => $state_wise_tax_classes, 'all_state_ids' => json_encode($state_id), 'brands' => $allBrands, "allCats" => $allCats, 'alltax' => $alltaxes, 'states' => $states, 'taxMapAccess' => $gridCellUpdate, 'taxMapImportAccess' => $taxMapImportAccess]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function stateWiseTaxClasses() {
        return json_encode($this->_taxclass->stateWise());
    }

    public function dashboardProducts(Request $request) {
        try {
            $request_input = $request->input();
            $order_by = '';
            $filter_by = '';
            $page = $request->input('page');   //Page number
            $page_size = $request->input('pageSize'); //Page size for ajax call

            if (isset($request_input['$orderby'])) {
                $orderBy = $request_input['$orderby'];
            } elseif (isset($request_input['%24orderby'])) {
                $orderBy = urldecode($request_input['%24orderby']);
            }

            if (isset($orderBy)) {
                $order_explode = explode(' ', $orderBy);
                $order_query_field = $order_explode[0]; //on which field sorting need to be done
                $order_query_type = $order_explode[1]; //sort type asc or desc
                $order_by = $order_query_field . " " . $order_query_type;
            }

            if (isset($request_input['$filter'])) {
                $filterBy = $request_input['$filter'];
            } elseif (isset($request_input['%24filter'])) {
                $filterBy = urldecode($request_input['%24filter']);
            }

            if (isset($filterBy)) {
                $filter_explode = explode(' and ', $filterBy);

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

                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $starts_with_value = $this->produc_grid_field_db_match[$key] . ' like ' . $filter_value;
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

                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $ends_with_value = $this->produc_grid_field_db_match[$key] . ' like ' . $filter_value;
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
                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $to_lower_value = $this->produc_grid_field_db_match[$key] . $like . $filter_value;
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
                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $indexof_value = $this->produc_grid_field_db_match[$key] . $like . $filter_value;
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

                        if (isset($this->produc_grid_field_db_match[$filter_query_field])) {
                            //getting appropriate table field based on grid field
                            $filter_field = $this->produc_grid_field_db_match[$filter_query_field];
                        }

                        $filter_by[] = $filter_field . $filter_operator . $filter_query_value;
                    }
                }
            }

            $product_list = $this->_product->showProducts($page, $page_size, $order_by, $filter_by);

            foreach ($product_list['result'] as $key => $value) {
                $valueArr = json_decode(json_encode($value), true);
                $taxGroup = json_decode(json_encode($this->_taxmap->displayTaxMap($valueArr['product_id'])), true);
                if (empty($taxGroup)) {
                    continue;
                }
                
                foreach ($taxGroup as $stateID => $taxCode) {
                    $product_list['result'][$key]["_" . $stateID] = $taxCode;
                }
            }
            foreach ($product_list['result'] as $product_each) {
                $product_each['actions'] = '<code style="cursor: pointer;">'
                        . '<a data-id="' . $product_each['product_id'] . '" data-toggle="modal" data-target="#permissions">'
                        . '<span  style="padding-left:15px;"><i class="fa fa-eye"></i></span></a>'
                        . '</code>';
            }

            if (isset($product_list['result'])) {
                echo json_encode(array('results' => $product_list['result'], 'TotalRecordsCount' => $product_list['count']));
            } else {
                echo json_encode(array('results' => '0', 'TotalRecordsCount' => $product_list['count']));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function taxTypeProducts(Request $request) {
        try {
            $request_input = $request->input();
            $order_by = '';
            $filter_by = '';
            if (isset($request_input['taxtype'])) {
                $tax_type = $request_input['taxtype'];
            } else {
                $tax_type = 'VAT';
            }
            $page = $request->input('page');   //Page number
            $page_size = $request->input('pageSize'); //Page size for ajax call

            if (isset($request_input['$orderby'])) {
                $orderBy = $request_input['$orderby'];
            } elseif (isset($request_input['%24orderby'])) {
                $orderBy = urldecode($request_input['%24orderby']);
            }

            if (isset($orderBy)) {
                $order_explode = explode(' ', $orderBy);
                $order_query_field = $order_explode[0]; //on which field sorting need to be done
                $order_query_type = $order_explode[1]; //sort type asc or desc
                $order_by = $order_query_field . " " . $order_query_type;
            }

            if (isset($request_input['$filter'])) {
                $filterBy = $request_input['$filter'];
            } elseif (isset($request_input['%24filter'])) {
                $filterBy = urldecode($request_input['%24filter']);
            }

            if (isset($filterBy)) {
                $filter_explode = explode(' and ', $filterBy);

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

                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $starts_with_value = $this->produc_grid_field_db_match[$key] . ' like ' . $filter_value;
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

                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $ends_with_value = $this->produc_grid_field_db_match[$key] . ' like ' . $filter_value;
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
                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $to_lower_value = $this->produc_grid_field_db_match[$key] . $like . $filter_value;
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
                            foreach ($this->produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $indexof_value = $this->produc_grid_field_db_match[$key] . $like . $filter_value;
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

                        if (isset($this->produc_grid_field_db_match[$filter_query_field])) {
                            //getting appropriate table field based on grid field
                            $filter_field = $this->produc_grid_field_db_match[$filter_query_field];
                        }

                        $filter_by[] = $filter_field . $filter_operator . $filter_query_value;
                    }
                }
            }

            $product_list = $this->_product->showProducts($page, $page_size, $order_by, $filter_by);

            foreach ($product_list['result'] as $key => $value) {
                $valueArr = json_decode(json_encode($value), true);
                $taxGroup = json_decode(json_encode($this->_taxmap->displayAllTaxMap($valueArr['product_id'], $tax_type)), true);
                if (empty($taxGroup)) {
                    continue;
                }
                foreach ($taxGroup as $stateID => $taxCode) {
                    $product_list['result'][$key]["__" . $stateID] = $taxCode;
                }
            }

            if (isset($product_list['result'])) {
                echo json_encode(array('results' => $product_list['result'], 'TotalRecordsCount' => $product_list['count']));
            } else {
                echo json_encode(array('results' => '0', 'TotalRecordsCount' => $product_list['count']));
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function productTaxMap(Request $requestData) {
        $input_data = json_decode($requestData->input('ig_transactions'), true);
        foreach ($input_data as $each_data) {
            $product_id = '';
            $product_id = $each_data['rowId'];
            $parentId=$this->_taxclass->getApprovalIdForTax();
            $gethsnCode  = $this->_taxmap->gethsnCodeByProdId($product_id);
            // $hsn_res = $this->_taxmap->allHsnCodes('All', $each_data['rowId']);
            // if($hsn_res['exist'] == 'yes'){
            //     $hsn_code = json_decode(json_encode($hsn_res['hsn_codes'][0]), true)['ITC_HSCodes'];
                foreach ($each_data['value'] as $each_value) {
                    $tax_class_id = json_decode(json_encode($this->_taxclass->getTaxClassId($each_value['text'])), true)[0];
                    $result_map_id = json_decode(json_encode($this->_taxmap->checkTaxClassMap($product_id, $tax_class_id)), true);
                    $product_details = $this->_product->find($product_id);
                    if (empty($result_map_id)) {
    //                    print_r($each_value['text']);
                        $this->_taxmap = new ClassTaxMap();
                        $this->_taxmap->insertTaxClassMap($product_id, $tax_class_id, isset($gethsnCode['hsn_code'])?$gethsnCode['hsn_code']:"",$parentId);
                        $oldValues = "";
                        $new_values = array("SKU" => $product_details->sku, "TAXCLASSCODE" => $each_value['text'], "PRODUCT_ID" => $product_details->product_id);
                        $uniqueIdForTaxMApping = array("product_id" => $product_details->product_id);
                        $mongodb = UserActivity::userActivityLog("TAXMAPPING", $new_values, "TAXMAPPING was Creating to the Table tax_class_product_map", $oldValues, $uniqueIdForTaxMApping);
                        Notifications::addNotification(['note_code' => 'TAX005', 'note_priority' => 0, 'note_type' => 1, 'note_params' => ['SKU' => $product_details->sku, 'TAXCODE' => $each_value['text']]]);
                    }
                }
            // } elseif($hsn_res['exist'] == 'no'){
                
            // }
        }
    }

    public function deleteTaxMap(Request $inputRequest) {
        $old_explode = explode(',', $inputRequest->input('oldArr'));
        $new_explode = explode(',', $inputRequest->input('newArr'));
        $final_array = array_values(array_diff($old_explode, $new_explode));
        $tax_class_id = json_decode(json_encode($this->_taxclass->getTaxClassId($final_array[0])), true)[0];
        $result_map_id = json_decode(json_encode($this->_taxmap->checkTaxClassMap($inputRequest->input('rowId'), $tax_class_id)), true);
        $product_details = $this->_product->find($inputRequest->input('rowId'));
        
        if (!empty($result_map_id)) {
            $this->_taxmap->deleteTaxClassMap($result_map_id[0],'map_id');
            $oldvalues = "";
            $newvalues = array("SKU" => $product_details->sku, "TAXCLASSCODE" => $final_array[0], "PRODUCT_ID" => $product_details->product_id);
            $uniqueId  = array("Product_id" => $product_details->product_id);
            $mongodb = UserActivity::userActivityLog("TAXMAPPING", $newvalues, "TAXMAPPING was deleted from the Table tax_class_product_map", $oldvalues, $uniqueId);
            Notifications::addNotification(['note_code' => 'TAX006', 'note_priority' => 0, 'note_type' => 1, 'note_params' => ['SKU' => $product_details->sku, 'TAXCODE' => $final_array[0]]]);
        }
    }

    public function getDetailsOfProducts($productid) {
        $result = array();
        $productInfo = $this->_product->getProductDetailsofTaxClassCode($productid);
        $result['productinfo'] = $productInfo;
        return json_encode($result);
    }

    public function allTaxcodesByStateProductID(Request $request) {
        $request_input = $request->input();
        $status = "";
        $productId = $request->input('productId');
        $path = $request->input('path');
        $explode = explode(':', $path);
        $StateId = $explode[1];
        $result = array();
        $productMappingDetais = $this->_taxmap->getAlltaxClassRulesbasedonProductID($productId, $StateId);
        for ($i = 0; $i < sizeof($productMappingDetais); $i++) {
            if ($productMappingDetais[$i]['status'] == 0) {
                $status = '<a href="javascript:" class="btn green-meadow" map-id="' . $productMappingDetais[$i]['map_id'] . '" onclick="statuschaging(' . $productMappingDetais[$i]['map_id'] . ')" class="statusbutton" id="statusbutton">Approve</a>';
            }
            if ($productMappingDetais[$i]['status'] == 1) {
                $status = '<label class="control-label">Approved</label>';
            }
            $productMappingDetais[$i]['mappingstatus'] = $status;
        }
        $result['result'] = $productMappingDetais;
        return json_encode($result);
    }
    
    public function getTaxMappingStateWise(Request $request) {
        $productId = $request->input('productId');
        $alltaxes = $this->_masterlookup->getTaxTypes();
        $taxTypes = array();
        foreach ($alltaxes as $value) {
            $taxTypes[] = trim($value['master_lookup_name']);
        }
        ksort($taxTypes);
        $allStates = $this->_zone->allStates();
        $productMappingDetais = $this->_taxmap->taxMapProductWise($productId);
        $taxWiseArr = array();
        foreach ($productMappingDetais as $value) {
            if (in_array($value['tax_class_type'], $taxTypes)) {
                $taxArr = array();
                unset($taxArr);
                $taxArr['text'] = $value['tax_class_code'];
                $taxArr['value'] = $value['tax_class_code'];
                $taxWiseArr[$value['tax_class_type']]['tax_type'] = $value['tax_class_type'];
                $taxWiseArr[$value['tax_class_type']]['_' . $value['state_id']][] = $taxArr;
            }
        }
        foreach ($taxTypes as $value) {
            if (empty($taxWiseArr[$value])) {
                $taxWiseArr[$value]['tax_type'] = $value;
            }
        }
        ksort($taxWiseArr);
        foreach ($allStates as $state) {
            $taxArr = array();
            unset($taxArr);
            $checkmappingExists = $this->_taxmap->checkMappingExists($productId, $state['zone_id']);

            if ($checkmappingExists > 0) {
                $taxStatusArry = json_decode(json_encode($this->_taxmap->getAllStatusesByProductId($productId, $state['zone_id'])), true);
                $statusCodeArry = json_decode(json_encode($this->_taxmap->statusCodeByRoleIdAEF()), true);
                $checkArry = array_intersect($taxStatusArry, $statusCodeArry);
                if(empty($checkArry)){
                    $taxArr['text'] = '--';
                } else {
                    $taxArr['text'] = '<a href="taxapprovaldashboard/'.$productId.'/'.$state['zone_id'].'" target="_blank" class="btn green-meadow">Approve</a>';
                }

                $taxWiseArr['Action']['tax_type'] = 'Action';
                $taxWiseArr['Action']['_' . $state['zone_id']][] = $taxArr;
            }
        }
        foreach ($taxWiseArr as $key => $value) {
            $result['result'][] = $value;
        }
        return json_encode($result);
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function updateMappingStatus(Request $req) {
        $returnvalue = "";
        $mapId = $req->input('map_id');
        $statusUpdate = $this->_taxmap->mappingStatusUpate($mapId);

        if ($statusUpdate == 1) {
            $returnvalue = "updated successfully";
        }

        if ($statusUpdate == 0) {
            $returnvalue = "Not updated";
        }
        return $returnvalue;
    }

    public function productTaxClassCodeMapping(Request $request) {
        try{
            DB::beginTransaction();
            $productObj = new ProductRepo();
            if (Input::hasFile('upload_mapping_rules')) {
                $path = Input::file('upload_mapping_rules')->getRealPath();
                $data = $this->readExcel($path);
                $data1 = json_decode(json_encode($data), true);
                $headingdata = $data1['header_data'];
                sort($headingdata);
                $taxdata = $data1['tax_rules'];

                
                $workflowDetails = $this->_taxworkflow->getApprovalWorkFlowStatusforTax();
                if($workflowDetails == 0)
                {
                    //user not having the approval workflow permission
                    $result['workflowstatus'] = "No permission";

                }else{
                // user having approval workflow permission
                $file_data = Input::file('upload_mapping_rules');
                $url = $productObj->uploadToS3($file_data,'tax',1);
                
    //            $file_data = Input::file('upload_mapping_rules');
                $upload_data['file_name'] = $file_data->getClientOriginalName();
                $upload_data['file_extension'] = $file_data->getClientOriginalExtension();
                $timestamp = md5(microtime(true));
                $Templatepath = 'download/mapping-sheet.xlsx';
                $templateHeaders = $this->readExcel($Templatepath);
                $templateHeaders['header_data'] = json_decode($templateHeaders['header_data'], true);
                sort($templateHeaders['header_data']);

               /* if ($headingdata != $templateHeaders['header_data']) {
                    $result = 0;
                } else {*/
                    $result = $this->_taxclass->storeMAppingWithTaxClassesAndProduct($taxdata, $url);

                    $result['linkdownload'] = 'tax/mappinglogs/'.$result['uniqueRef'];
               /* }*/
                Notifications::addNotification(['note_code' => 'TAX007', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Products Tax Mapping Completed, <a href="/' . $result['linkdownload'] . '" target="_blank">View Details</a>']);
                DB::commit();
            }
                print_r(json_encode($result));
            }
        }catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $result['workflowstatus']="rollback";
            print_r(json_encode($result));
        } 
    }

    public function allStates(Request $req) {
        $countryId = $req->input('country_id');
        $onlystates = $this->_zone->getAllStates($countryId);

        return json_encode(array('states' => $onlystates));
    }
    
    public function hsnCodes(Request $request){
        return json_encode($this->_taxmap->allHsnCodes($request->input('type'), $request->input('productid')));
    }

    public function readExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['header_data'] = $cat_data;
            $data['tax_rules'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getAvaliableTaxesForStateAndProduct(Request $request) {
        $productId = $request->input('product_id');
        $stateId = $request->input('state_id');
        $alltaxes = $this->_taxmap->getAllTaxesForStateByProductId($productId, $stateId);
        return $alltaxes;
    }

    public function taxMapByPermissionGrid(Request $req) {
        $approval_flow_func                 = new CommonApprovalFlowFunctionModel();
        $res_approval_flow_func             = $approval_flow_func->getApprovalFlowDetails('TAX', 'drafted', \Session::get('userId'));
        $approval_status                    = $res_approval_flow_func['status'];
        $returndata                         = "";        
        $parentId=$this->_taxclass->getApprovalIdForTax();
        if($approval_status == 0)
        {
            //user doesn't have permission to tax approval workflow
            $returndata = "no permission";
        }
        else
        {
            //user have permission for approval work flow

            $productID = $req->input('productId');
            $taxclassID = $req->input('taxclassId');
            $effectiveDate = $req->input('effectiveDate');
            $stateId = $req->input('stateid');
            $hsnCode = $req->input('hsnCode');
            $checkhsncode = DB::select(DB::raw("SELECT * FROM HSN_Master WHERE ITC_HSCodes = $hsnCode"));
            if(count($checkhsncode)==0){
                $returndata =  "hsn_error";
                return $returndata;
            }
            $sku = $this->_product->getSkuByProductId($productID);
            $taxclassCode  = $this->_taxclass->getTaxClassCodeByTaxId($taxclassID);
            
            $pending_for_approvals = json_decode(json_encode($this->_taxmap->checkPendingForApprovals($productID)), true);
            if (empty($pending_for_approvals)) {
                $re_eff_date_map_id = json_decode(json_encode($this->_taxmap->checkTaxClassByDate($productID, $effectiveDate, $stateId,$taxclassID)), true);
               // print_r($re_eff_date_map_id);exit;
                if (empty($re_eff_date_map_id)) {
                    $result_map_id = json_decode(json_encode($this->_taxmap->checkTaxClassMap($productID, $taxclassID)), true);
                    if (empty($result_map_id)) {
                        $result = $this->_taxmap->insertTaxClassMap($productID, $taxclassID, $effectiveDate, $req->input('hsnCode'),$parentId);
                        if (!empty($result)) {
                            $returndata = 1;
                        }
                        $oldValues = "";
                        $new_values = array("SKU" => $sku, "TAXCLASSCODE" => $taxclassCode, "PRODUCT_ID" => $productID);
                        $uniqueIdForTaxMApping = array("product_id" => $productID);
                        $mongodb = UserActivity::userActivityLog("TAXMAPPING", $new_values, "TAXMAPPING was Creating to the Table tax_class_product_map through component", $oldValues, $uniqueIdForTaxMApping);
                    } else {
                        $returndata = "already done";
                    }
                } else {
                    $returndata = "effective date";
                }
            } else {
                $returndata = "pending approval";
            }
            Notifications::addNotification(['note_code' => 'TAX005', 'note_priority' => 0, 'note_type' => 1, 'note_params' => ['SKU' => $sku, 'TAXCODE' => $taxclassCode]]);
        }
        

        echo $returndata;
    }

    public function downloadExcelForMapping(Request $request) {
        $legal_entity_id = Session::get('legal_entity_id');
        $withdata = $request->input("withdata");
        //here sending these values in from hidden values because sumo select not giving the values in form submit thats why i used here hidden vals
        $cats = $request->input('hiddencats');
        $brands = $request->input('hiddenbrands');
        $taxtypes = $request->input('tax_type');
        $getproductInfo = array();
        $states = $this->_zone->allStatesExceptAll();
        $mytime = Carbon::now();
        $headers = array('Brand', 'Product Name', 'Category', 'SKU', 'HSN Code');
        for ($i = 0; $i < sizeof($states); $i++) {
            if ($states[$i]['code'] == '*') {
                $states[$i]['code'] = "*(ALL)";
            }
            $headers[] = $states[$i]['code'];
        }

        $alltaxcodesByStates = $this->_taxclass->getTaxClassCodesByState();
        $exceldata = $this->_taxmap->getAllExcelDataWithFilters($cats, $brands, $taxtypes, $legal_entity_id);

        Excel::create('Tax Mapping-' . $mytime->toDateTimeString(), function($excel) use($headers, $exceldata, $states, $alltaxcodesByStates) {
            $excel->sheet("Tax Mapping", function($sheet) use($headers, $exceldata) {
                // $sheet->fromArray($getproductInfo);
                $sheet->loadView('Tax::taxmapping', array('headers' => $headers, 'data' => $exceldata));
            });
            $excel->sheet("Tax Rules", function($sheet) use($states, $alltaxcodesByStates) {
                // $sheet->fromArray($getproductInfo);
                $sheet->loadView('Tax::taxrules', array('headers' => $states, 'taxclass_codes' => $alltaxcodesByStates));
            });
        })->download('xls');
    }

    public function getBrandsBasedOnCats(Request $request) {
        $cats = $request->input('catfilters');
        $allBrands = $this->_product->getBrandsBasedByCategories($cats);
        print_r(json_encode($allBrands));
    }

    public function getCatsBasedOnBarnd(Request $request) {

        $brands = $request->input('brandfilters');
        $allCats = $this->_product->getCategoriesBasedOnBrands($brands);
        print_r(json_encode($allCats));
    }

    public function getAllTaxTypes(Request $request) {
        $productId = $request->input("prod_id");
        $alltaxes = $this->_masterlookup->getTaxTypes();
        sort($alltaxes);
        //Collect Accesses here
        $createTaxMapAccess = $this->_roleRepo->checkPermissionByFeatureCode('PTM002');
        for ($i = 0; $i < sizeof($alltaxes); $i++) {
            $alltaxes[$i]['actions'] = '';
            if ($createTaxMapAccess == '1') {
                $alltaxes[$i]['actions'] = '<code style="cursor: pointer;">'
                        . '<a data-productid="' . $productId . '" data-id="' . $alltaxes[$i]['master_lookup_name'] . '" data-toggle="modal" data-target="#mapping-board">'
                        . '<span  style="padding-left:15px;"><i class="fa fa-plus"></i></span></a>'
                        . '</code>';
            }
        }
        $temp['master_lookup_name'] = 'Action';
        $temp['master_lookup_id'] = '0';
        $alltaxes[] = $temp;
        echo json_encode($alltaxes);
    }

    public function approveAllTaxes(Request $request) {
        $productId = $request->input("prodId");
        $stateId = $request->input("stateId");

        $approveallTaxes = $this->_taxmap->getApproveAllTaxes($productId, $stateId);
        return $approveallTaxes;
    }

    public function getAvailableTaxesByProductId(Request $request) {
        $productId = $request->input('product_id');
        $stateid = $request->input('stateId');
        $taxtype = $request->input('taxtype');
        $availabletaxes = $this->_taxmap->getAvailableTaxesByProductId($productId, $stateid, $taxtype);
        return json_encode($availabletaxes);
    }

    public function taxMappingLogs($refId)
    {
        $result = $this->_readlogs->readMappingLogs($refId);
    }
    
    public function downloadHsnDetails() {
        $hsnHeaders = array('HSN Codes', 'Description', 'Tax Percentage');
        $allHsnCodeDetails = json_decode(json_encode($this->_taxmap->allHsnCodes('All', 'NA')), true)['hsn_codes'];
        $mytime = Carbon::now();
        
        Excel::create('HSN-Details-' . $mytime->toDateTimeString(), function($excel) use($hsnHeaders, $allHsnCodeDetails) {
            $excel->sheet("HSN Details", function($sheet) use($hsnHeaders, $allHsnCodeDetails) {
                $sheet->loadView('Tax::hsndetails', array('hsn_headers' => $hsnHeaders, 'hsn_code_details' => $allHsnCodeDetails));
                $sheet->setWidth(array(
                        'A' => 15,
                        'B' => 100,
                        'C' => 15,
                        ));
                $sheet->getDefaultStyle()->getAlignment()->setWrapText(true);
            });
        })->download('xls');
    }

    public function getHSNInfo(Request $request)
    {
        $term = $request->input('term');
        $gethsninfo = $this->_taxmap->getHsnInfo($term);
        return json_encode($gethsninfo);
    }

    public function hsnDetails()
    {
        parent::Title('HSN codes - Ebutor');
        $hsnHeaders = array('HSN Codes', 'Description', 'Tax Percentage'); 
        $allHsnCodeDetails = json_decode(json_encode($this->_taxmap->allHsnCodes('All', 'NA')), true)['hsn_codes'];
        return View::make('Tax::taxHsnDetails')->with(['hsn_headers' => $hsnHeaders, 'hsn_code_details' => $allHsnCodeDetails]);
    }

}
