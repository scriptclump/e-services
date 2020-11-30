<?php

namespace App\Modules\Tax\Controllers;

use View;
use Illuminate\Support\Facades\Session;
use Validator;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use Redirect;
use Title;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Modules\Tax\Models\TaxClass;
use App\Modules\Tax\Models\ClassTaxMap;
use App\Modules\Tax\Models\MasterLookUp;
use App\Modules\Tax\Models\Product;
use App\Modules\Tax\Models\Zone;
use App\Modules\Tax\Models\Country;
use App\Modules\Tax\Models\ReadLogs;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Notifications;
use Carbon\Carbon;
use App\Central\Repositories\ProductRepo;

class TaxController extends BaseController {

    public function __construct() {
        $this->_taxclass = new TaxClass();
        $this->_taxmap = new ClassTaxMap();
        $this->_masterlookup = new Masterlookup();
        $this->_zone = new Zone();
        $this->_product = new Product();
        $this->_Country = new Country();
        $this->_readlogs = new ReadLogs();
        $this->grid_field_db_match = array(
            'tax_percentage' => 'tax_classes.tax_percentage',
            'tax_class_type' => 'tax_classes.tax_class_type',
            'tax_class_code' => 'tax_classes.tax_class_code',
            'coutryname' => 'master_lookup.master_lookup_name',
            'name' => 'zone.name',
            'date_start' => 'date_start'
        );

        try {

           $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                parent::Title('Tax Classes - Ebutor');
                $this->_roleRepo = new RoleRepo();
                $access = $this->_roleRepo->checkPermissionByFeatureCode('TM001');
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

    public function index() {
        $breadCrumbs = array('Home' => url('/'), 'Finance' => url('/'), 'Tax Master' => url('#'));
        parent::Breadcrumbs($breadCrumbs);
        $states = $this->_zone->allStates();
        $types = $this->_masterlookup->getTaxTypes();
        //Check all accesses here
        $taxClassCreateAccess = $this->_roleRepo->checkPermissionByFeatureCode('TM002');
        $taxClassImportAccess = $this->_roleRepo->checkPermissionByFeatureCode('TM005');
        return View::make('Tax::index')->with(['states' => $states, 'types' => $types, 'taxClassCreateAccess' => $taxClassCreateAccess, 'taxClassImportAccess' => $taxClassImportAccess]);
    }

    public function dashBoard(Request $request) {
        $request_input = $request->input();
        $explode = explode('/', $request->input('path'));
        $ZoneId = $explode[1];
        $explodefor_stateId = explode(":", $ZoneId);
        $STATEID = $explodefor_stateId[1];
        $countryID = $request->input('country_id');
        $orderby_array = "";
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $filter_by = "";
        if ($request->input('$orderby')) {             //checking for sorting
            $order = explode(' ', $request->input('$orderby'));
            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
            }
            $orderby_array = $order_by . " " . $order_by_type;
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

        $data = $this->_taxclass->getAllData($page, $pageSize, $orderby_array, $filter_by, $STATEID);
        $decodedData = json_decode($data['result'], true);
        $GridData = array();
        //Collecting accesses for Buttons
        $taxClassEditAccess = $this->_roleRepo->checkPermissionByFeatureCode('TM003');
        $taxClassDeleteAccess = $this->_roleRepo->checkPermissionByFeatureCode('TM004');
        foreach ($decodedData as $key => $value) {
            $decodedData[$key]['coutryname'] = "India";
            if ($decodedData[$key]['date_start'] == '1970-01-01') {
                $decodedData[$key]['date_start'] = '--';
            }
            $decodedData[$key]['action'] = '<code style="cursor: pointer;">';
            //check Edit access
            if ($taxClassEditAccess == 1) {
                $decodedData[$key]['action'] .= '<a data-type="edit" data-id="' . @$value['tax_class_id'] . '" data-toggle="modal" data-target="#createrule-modal"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            } else {
                $decodedData[$key]['action'] .= '';
            }

            //check Delete access
            if ($taxClassDeleteAccess == 1) {
                $taxclassCode = "'".$value['tax_class_code']."'";
                $decodedData[$key]['action'] .=
                        '<span  style="padding-left:15px;"><i  class="fa fa-trash" onclick="deleteVal(' . @$value['tax_class_id'] . ','.$taxclassCode.');"></i></span>';
            } else {
                $decodedData[$key]['action'] .= '';
            }
            $decodedData[$key]['action'] .= '</code>';


            $decodedData[$key]['mappingcount'] = "<span align='center'>" . $this->_taxmap->taxCountBasedonTaxClassId($value['tax_class_id']) . "</span>";
        }

        echo json_encode(array('results' => $decodedData, 'TotalRecordsCount' => $data['count']));
    }

    public function deleterule($ruleid) {
        $message = "The rule was not deleted";
        $disable = $this->_taxclass->disableTaxRule($ruleid);
        Notifications::addNotification(['note_code' => 'TAX003', 'note_priority' => 0, 'note_type' => 1, 'note_params' => ['TAXCODE' => $disable['tax_class_code']]]);
        if ($disable['value'] == 1) {
            $message = "The rule was deleted successfully";
        }
        return $message;
    }

    public function showAllStates() {
        $states = $this->_masterlookup->allStates();
        return $states;
    }

    public function addAction() {
        $states = $this->showAllStates();
        return View::make('Tax::addClassTax')->with(['states' => $states]);
    }

    public function editAction($taxClassId) {
        $result = array();
        $tax_class_data = $this->_taxclass->find($taxClassId);
        $states = $this->_zone->allStates();
        $result['taxclassdata'] = $tax_class_data;
        $result['states'] = $states;
        print_r(json_encode($result));
    }


    public function createAction(Request $createRequest) {
        $request_input = json_decode($createRequest->input('details'), true);
        $taxclass_result_id = $this->_taxclass->createTaxClass($request_input);
        if(($taxclass_result_id['id_message'] != 'error') && ($taxclass_result_id['id_message'] != 'effective-exists')){
            Notifications::addNotification(['note_code' => 'TAX001', 'note_priority' => 0, 'note_type' => 1, 'note_params' => ['TAXCODE' => $taxclass_result_id['tax_class_code']]]);
        }
        return $taxclass_result_id['id_message'];
    }

    public function updateAction(Request $updateRequest) {
        $update_input = json_decode($updateRequest->input('details'), true);
        $update_res = $this->_taxclass->updateTaxClass($update_input);
        Notifications::addNotification(['note_code' => 'TAX002', 'note_priority' => 0, 'note_type' => 1, 'note_params' => ['TAXCODE' => $update_res['tax_class_code']]]);
        return $update_res['message'];
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

    public function uploadexcelsheet(Request $request) {
        $productObj = new ProductRepo();
        if (Input::hasFile('upload_doc')) {
            $path = Input::file('upload_doc')->getRealPath();
            $data = $this->readExcel($path);
            $data1 = json_decode(json_encode($data), true);
            $headingdata = $data['header_data'];


            $taxdata = $data1['tax_rules'];


            $file_data = Input::file('upload_doc');
            $url = $productObj->uploadToS3($file_data,'tax',1);
            
            $upload_data['file_name'] = $file_data->getClientOriginalName();
            $upload_data['file_extension'] = $file_data->getClientOriginalExtension();

            $timestamp = md5(microtime(true));
            $upload_data['file_name'] = pathinfo($upload_data['file_name'], PATHINFO_FILENAME) . "_" . $timestamp . "." . pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
            // $file_data->move('uploads/Taxrulescreating/', strtolower($upload_data['file_name']));

            $Templatepath = 'download/Tax_class_template.xlsx';
            $templateHeaders = $this->readExcel($Templatepath);
            if ($data['header_data'] != $templateHeaders['header_data']) {
                // $result = 0;
                $timestamp = md5(microtime(true));
                // $file_path = 'download' . DIRECTORY_SEPARATOR . 'Tax-logs' . DIRECTORY_SEPARATOR . 'Taxcreating-' . $timestamp . '.txt';
                // $msg = '';


                // $msg .= "failed Records : 0 " . PHP_EOL;
                // $msg .= "Inserted Records : 0" . PHP_EOL;
                // $msg .= "Updated Records : 0" . PHP_EOL;
                // $file = fopen($file_path, "w");
                // fwrite($file, $msg);
                // fclose($file);
                print_r(json_encode(array('headcount' => 0, 'failedcount' => "Headers mis-matched in Excel sheet", 'success' => 0, 'update' => 0)));
                die;
            } else {
                $result = $this->_taxclass->storeTaxrules($taxdata, $url);
                $timestamp = md5(microtime(true));
                $file_path = 'tax/accesslogs/'.$result['reference'];
                print_r(json_encode($result));
                Notifications::addNotification(['note_code' => 'TAX004', 'note_priority' => 0, 'note_type' => 1, 'note_message' => 'Upload Process For Tax Classes Creation Completed, <a href="/' . $file_path . '" target="_blank">View Details</a>']);
            }
        }
    }

    public function getTaxTypes() {
        $types = $this->_masterlookup->getTaxTypes();
    }

    public function taxRules(Request $taxRequest) {
        try {
            $tax_list = $this->_taxclass->displayTaxRules();
            echo json_encode(array('results' => $tax_list['result']));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function formatJson(Request $data) {
        try {
            $totalTaxClass = json_decode($data->input('resultJson'), true);
            $IDs = $data->input('arrIDs');
            $count = 0;
            $finalArr = array();
            $delArr = array();
            foreach ($totalTaxClass['results'] as $taxClass) {
                if (in_array($taxClass['tax_class_id'], $IDs)) {
                    $delArr['results'][] = $taxClass;
                    unset($totalTaxClass['results'][$count]);
                } else {
                    $finalArr['results'][] = $taxClass;
                }
                $count++;
            }
            return json_encode(array('add' => $finalArr, 'del' => $delArr));
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function countryName(Request $request) {
        $countryId = $request->input('country_id');
        $countryname = $this->_Country->countryName($countryId);
        return json_encode(array('results' => $countryname));
    }

    public function onlyStateNames(Request $req) {
        $countryId = $req->input('country_id');
        $onlystates = $this->_zone->getAllStates($countryId);
        $i = 0;

        $taxClassAccess = $this->_roleRepo->checkPermissionByFeatureCode('TM002');
        foreach ($onlystates as $onlystate) {
            if ($taxClassAccess == 1) {
                $onlystates[$i]['actions'] = '<code style="cursor: pointer;"><a data-type="add" id="clickState"  data-id="' . $onlystate['zone_id'] . '" data-toggle="modal" data-target="#createrule-modal"><span  style="padding-left:15px;"><i class="fa fa-plus-square"></i></span></a></code>';
            } else {

                $onlystates[$i]['actions'] = '';
            }

            $onlystates[$i]['mappingactions'] = '<code style="cursor: pointer;"><a data-type="add" id="clickState"  data-stateid="' . $onlystate['zone_id'] . '" data-toggle="modal" data-target="#create-mapping"><span  style="padding-left:15px;"><i class="fa fa-plus"></i></span></a></code>';
            $i++;
        }
        return json_encode(array('states' => $onlystates));
    }

    public function accessLogs($refid)
    {
        $result = $this->_readlogs->readLogs($refid);
    }

    

}
