<?php

namespace App\Modules\HrmsEmployees\Controllers;

use App\Http\Controllers\BaseController;
use Log;
use URL;
use View;
use DB;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use App\Modules\HrmsEmployees\Models\EmployeeGrid;

class EmployeeGridController extends BaseController {

    public function __construct() {
        try {
            parent::Title('Ebutor - Employee Dashboard');
            $this->grid_field_db_match = array(
                'emp_name' => 'emp_name',
                'bu_name' => 'bu.bu_name',
                'designation' => 'ml.master_lookup_name',
                'reporting_manager_id' => 'reporting_manager_id',
                'emp_code' => 'emp.emp_code',
                'office_email' => 'emp.office_email',
                'doj' => 'emp.doj',
                'role_name' => 'r.name',
                'role_code' => 'r.short_code',
                'exit_date' => 'emp.exit_date',
                'is_active' => 'emp.is_active'
            );
            $this->_roleRepo = new RoleRepo();
            $this->_employeeGrid = new EmployeeGrid();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'HRMS' => url('#'), 'Employee Dashboard' => url('/employee/dashboard'));
            parent::Breadcrumbs($breadCrumbs);
            $addEmployee = $this->_roleRepo->checkPermissionByFeatureCode('EDADDEMP');
            $initiatedTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB001');
            $offerCreatedTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB002');
            $offerApprovedTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB003');
            $onBoardingTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB004');
            $offerRejectedTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB005');
            $offBoardingTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB006');
            $inActiveTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB007');
            $droppedTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB008');
            $onBoardingApprovedTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB009');
            $onBoardingRejectedTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB010');
            $itAssetAssignTab = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB011');
            $offBoardingApproved = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB012');
            $itCleared = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB013');
            $financeCleared = $this->_roleRepo->checkPermissionByFeatureCode('EDTAB014');
            $exportByStatus = $this->_roleRepo->checkPermissionByFeatureCode('EXPSTS001');
            $exportEmployeeAccess = $this->_roleRepo->checkPermissionByFeatureCode('EXPEMP001');
            return View::make('HrmsEmployees::employeeGrid')->with(['add_employee' => $addEmployee, 'initiated' => $initiatedTab, 'offer_created' => $offerCreatedTab,
                        'offer_approved' => $offerApprovedTab, 'on_boarding' => $onBoardingTab, 'offer_rejected' => $offerRejectedTab, 'off_boarding' => $offBoardingTab,
                        'in_active' => $inActiveTab, 'dropped' => $droppedTab, 'on_boarding_approved' => $onBoardingApprovedTab, 'on_boarding_rejected' => $onBoardingRejectedTab,
                        'it_asset_assign' => $itAssetAssignTab, 'off_boarding_approved' => $offBoardingApproved, 'it_cleared' => $itCleared,
                        'finance_cleared' => $financeCleared,'export_by_status'=>$exportByStatus,'export_employees'=>$exportEmployeeAccess]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function employeeGrid(Request $request) {
        try {
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $filter_by = $orderby_array = array();
            if ($request->input('$orderby')) {
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0];
                $order_query_type = $order[1]; //type
                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }
                if (isset($this->grid_field_db_match[$order_query_field])) {
                    $order_by = $this->grid_field_db_match[$order_query_field];
                    $orderby_array = $order_by . " " . $order_by_type;
                }
            }
            if (isset($request['$filter'])) {
                $filter_explode = explode(' and ', $request['$filter']);
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
                    
                    if (strpos($filter_each, ' or ') !== false) {
                        $query_field_arr = explode(' or ', $filter_each);
                        foreach ($query_field_arr as $query_field_data) {               //looping through each filter
                            $filter = explode(' ', $query_field_data);
                            $filter_query_field = $filter[0];
                            $filter_query_operator = $filter[1];
                            $filter_query_value = $filter[2];

                            if (strpos($filter_query_field, 'day(') !== false) {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                                continue;
                            } elseif (strpos($filter_query_field, 'month(') !== false) {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                                continue;
                            } elseif (strpos($filter_query_field, 'year(') !== false) {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['year'] = $filter_query_value;
                                $date[$filter_query_field]["operator"] = $filter_query_operator;
                                $filter_query_operator = $date[$filter_query_field]['operator'];
                                $filter_query_value = "DateTime'" . implode('-', array_reverse($date[$filter_query_field]['value'])) . "'";
                            }
                        }
                    } 
                    else {
                         if (strpos($filter_query_field, 'day(') !== false) {
                         $start = strpos($filter_query_field, '(');
                         $end = strpos($filter_query_field, ')');
                         $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                         $date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                         continue;
                         } elseif (strpos($filter_query_field, 'month(') !== false) {
                         $start = strpos($filter_query_field, '(');
                         $end = strpos($filter_query_field, ')');
                         $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                         $date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                         continue;
                         } elseif (strpos($filter_query_field, 'year(') !== false) {
                         $start = strpos($filter_query_field, '(');
                         $end = strpos($filter_query_field, ')');
                         $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                         $date[$filter_query_field]["value"]['year'] = $filter_query_value;
                         $date[$filter_query_field]["operator"] = $filter_query_operator;
                         $filter_query_operator = $date[$filter_query_field]['operator'];
                         $filter_query_value = "DateTime'" . implode('-', array_reverse($date[$filter_query_field]['value'])) . "'";
                         }
                         // reset($date);
                     }

                    // elseif (substr_count($filter_each, 'DateTime')) {
                    //     $filter_each_explode = explode(' ', $filter_each);
                    //     $filter_query_field = $filter_each_explode[0];
                    //     $filter_query_operator = $filter_each_explode[1];
                    //     $filter_query_value = $filter_each_explode[2];
                    // }
                     
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

            $status = $request->input('status');
            $full_results = $this->_employeeGrid->getEmployeeList($page, $pageSize, $status, $orderby_array, $filter_by);
            $i = 0;
            if (!empty($full_results["result"])) {
                foreach ($full_results["result"] as $result) {
                    $profilePictureLink = '';
                    $profilePicture = isset($result['profile_picture']) ? $result["profile_picture"] : '';
                    if ($profilePicture != '') {
                        $profileRootPath = '';
                        if (strpos($profilePicture, 'www') !== false || strpos($profilePicture, 'http') !== false) {
                            $profilePictureLink = '<img src="' . $profilePicture . '" class="img-circle" style=" cursor:pointer;height: 50px; width: 50px;" onclick ="popupimage(\'' . $profilePicture . '\');"/>';
                        } else {
                            $profilePictureLink = '<img src="' . URL::to('/') . '/' . $profilePicture . '" class="img-circle" style="height: 50px; width: 50px;" />';
                        }
                    } else {
                        $profilePictureLink = '<img src="' . URL::to('/') . '/img/avatar5.png" class="img-circle"  style="height: 50px; width: 50px;" />';
                    }
                    $full_results["result"][$i]["profile_picture"] = $profilePictureLink;

                    $full_results["result"][$i]["actions"] = '<span style="padding-left:20px;" ><a href="/employee/editemployee/' . /*$this->_roleRepo->encodeData(*/$result["emp_id"]/*)*/ . '"><i class="fa fa-eye"></i></span>';

                    $i++;
                }
            }
            echo json_encode($full_results);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function empStatusCount() {
        try {
            $response = json_decode(json_encode($this->_employeeGrid->getEmpStatusCount()), true);
            return $response[0];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function employeeExtensions() {
        try {
            parent::Title('Ebutor - Employee Extensions');
            return View::make('HrmsEmployees::employeeExtensions');

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    } 

    public function getEmpExtensions() {
        try {            

            $emp_extensions = $this->_employeeGrid->getEmpExtensions();

            return json_encode(array('Records' => $emp_extensions));
            

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }

}
