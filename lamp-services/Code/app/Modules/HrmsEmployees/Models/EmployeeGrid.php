<?php

namespace App\Modules\HrmsEmployees\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class EmployeeGrid extends Model {
    protected $table = 'employee';

    public function getEmployeeList($page, $pageSize, $status, $orderBy = "", $filterBy = "") {
        try {
            $employee_list = DB::table("employee as emp")
                    ->leftJoin('master_lookup as ml', 'ml.value', '=', 'emp.designation')
                    ->leftJoin('roles as r', 'r.role_id', '=', 'emp.role_id')
                    ->leftJoin('business_units as bu', 'bu.bu_id', '=', 'emp.business_unit_id');

            if ($status == 57160) {
                $employee_list = $employee_list->where('emp.is_active',0);
            } else {
                $employee_list = $employee_list->where("emp.status", $status);
            }

            if ($status == 57155) {
                $employee_list = $employee_list->where("emp.is_active", 1);
            }

            if (!empty($orderBy)) {
                $orderClause = explode(" ", $orderBy);
                $employee_list = $employee_list->orderby($orderClause[0], $orderClause[1]);  //order by query
            }

            if (!empty($filterBy)) {
                foreach ($filterBy as $filterByEach) {
                    $filterByEachExplode = explode(' ', $filterByEach);

                    $length = count($filterByEachExplode);
                    $filter_query_value = '';
                    if ($length > 3) {
                        $filter_query_field = $filterByEachExplode[0];
                        $filter_query_operator = $filterByEachExplode[1];
                        for ($i = 2; $i < $length; $i++)
                            $filter_query_value .= $filterByEachExplode[$i] . " ";
                    } else {
                        $filter_query_field = $filterByEachExplode[0];
                        $filter_query_operator = $filterByEachExplode[1];
                        $filter_query_value = $filterByEachExplode[2];
                    }

                    $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                    if (in_array(trim($filter_query_operator), $operator_array)) {
                        if ($filter_query_field == "emp.is_active") {
                            $filter_query_value == "true" ? $filter_query_value = 1 : $filter_query_value = 0;
                            $employee_list = $employee_list->where($filter_query_field, $filter_query_operator, (int) $filter_query_value);
                        } elseif ($filter_query_field == "emp.doj" || $filter_query_field == "emp.exit_date") {
                            $filter_query_value = str_replace(array('DateTime', "'"), "", $filter_query_value);
                            if ($filter_query_operator == "=") {
                                $employee_list = $employee_list->whereDate($filter_query_field, $filter_query_operator, $filter_query_value);
                            } else {
                                $employee_list = $employee_list->where($filter_query_field, $filter_query_operator, $filter_query_value);
                            }
                        } else {
                            $employee_list = $employee_list->where($filter_query_field, $filter_query_operator, (int) $filter_query_value);
                        }
                    } else {
                        if ($filter_query_field == "emp_name") {
                            $employee_list = $employee_list->where(DB::raw("CONCAT(firstname, IF(middlename IS NULL, '', CONCAT(' ', middlename)), ' ', lastname)"), $filter_query_operator, trim($filter_query_value));
                        } else if ($filter_query_field == "reporting_manager_id") {
                            $employee_list = $employee_list->where(DB::raw("GetUserName(emp.reporting_manager_id, 2)"), $filter_query_operator, trim($filter_query_value));
                        } else {
                            $employee_list = $employee_list->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                        }
                    }
                }
            }

            $count = $employee_list->count();
            $final_result['recordsCount'] = $count;
            $employee_list = $employee_list->skip($page * $pageSize)->take($pageSize);

            $final_result["result"] = $employee_list->get(["emp.profile_picture", "emp.emp_id",
                DB::raw("CONCAT(firstname, IF(middlename IS NULL, '', CONCAT(' ', middlename)), ' ', lastname) AS emp_name"),
                DB::raw("ml.master_lookup_name as designation"), "bu.bu_name",
                DB::raw("getRepManagernameHierarchy(emp.reporting_manager_id) as reporting_manager_id"), "emp.emp_code", "emp.office_email", "emp.doj", DB::raw("r.name as role_name"),
                "emp.exit_date", "r.short_code as role_code", DB::raw("IF(emp.is_active = 1, 'true', 'false') AS is_active"), "emp.status"])->all();
            return json_decode(json_encode($final_result), true);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getEmpStatusCount() {
        try {
            $countQuery = DB::table("employee as emp")
                    ->select(DB::raw("SUM(IF(emp.status = 57148, 1, 0)) AS initiated, SUM(IF(emp.status = 57149, 1, 0)) AS offer_created,"
                                    . " SUM(IF(emp.status = 57150, 1, 0)) AS offer_approved, SUM(IF(emp.status = 57151, 1, 0)) AS on_boarded,"
                                    . " SUM(IF(emp.status = 57152, 1, 0)) AS on_boarding_approved, SUM(IF(emp.status = 57153, 1, 0)) AS it_assets_assigned,"
                                    . " SUM(IF(emp.status = 57155, 1, 0)) AS active, SUM(IF(emp.status = 57156, 1, 0)) AS exit_initiated,"
                                    . " SUM(IF(emp.status = 57157, 1, 0)) AS exit_approved, SUM(IF(emp.status = 57158, 1, 0)) AS it_cleared,"
                                    . " SUM(IF(emp.status = 57159, 1, 0)) AS finance_cleared, SUM(IF(emp.status = 1, 1, 0)) AS in_active,"
                                    . " SUM(IF(emp.status = 57161, 1, 0)) AS offer_rejected, SUM(IF(emp.status = 57167, 1, 0)) AS dropped,"
                                    . " SUM(IF(emp.status = 57168, 1, 0)) AS on_boarding_rejected"))
                    ->get()->all();
            $in_active = DB::table('employee as emp')->where('emp.is_active',0)->count();
            $countQuery[0]->in_active = $in_active;
            return $countQuery;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getEmpExtensions() {
        try {
            
            $result = DB::table('vw_emp_landline_extension')->get();
            return $result;

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
