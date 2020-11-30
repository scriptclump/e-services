<?php

namespace App\Modules\HrmsEmployees\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Mail;
use Session;

class ExportEmployeeModel extends Model {

    protected $table = 'employee';
    protected $primaryKey = "emp_id";

    
   public function getEmployeeAllDetails($inputs){
    if($inputs['exp_employee'] == 1){
        $status = '57155,57156,57157,57158,57159';
        /*$query = "select emp_id from employee where status in(57155,57156,57157,57158,57159)";        
        $allData = DB::select(DB::raw($query));
        $allData = json_decode(json_encode($allData), true);
        $empids = array_column($allData, 'emp_id');
        $implode_empids =  implode( ',', array_values($empids));*/
    }else{
        $status = '1';
        /*$query = "select emp_id from employee where status in(1)";
        $allData = DB::select(DB::raw($query));
        $allData = json_decode(json_encode($allData), true);
        $empids = array_column($allData, 'emp_id');
        $implode_empids =  implode( ',', array_values($empids));*/
    } 

            /*if($implode_empids == ""){
                $implode_empids = 0;
            }*/
    
            $exportEmployeeDetails = "select e.`emp_id`,e.`emp_code`, e.`firstname`,e.`lastname`,date_format(e.`dob`,'%d-%b-%Y') as dob,e.`gender`,e.`marital_status`,e.`pan_card_number`,date_format(e.`doj`,'%d-%b-%Y') as doj,ec.`pe_address`,e.`mobile_no`,e.`email_id`,e.uan_number,e.alternative_mno,ei.card_number,ei.tpa,ei.tpa_contact_number,date_format(ei.spouse_dob,'%d-%b-%Y') as spouse_dob,e.office_email,
            ec.`emergency_contact_one`,getMastLookupValue(e.`designation`) as designation,ew.`organization_name`,getRepManagernameHierarchy(e.`reporting_manager_id`) AS reporting_manager,e.`landline_ext`,ee.`degree`,ec.`pe_address`,e.`email_id`,e.`blood_group`,
            ec.`emergency_contact_one`,ec.`emergency_contact_two`,date_format(e.`exit_date`,'%d-%b-%Y') as exit_date,ec.`spouse_name`,ec.no_of_childerns,ec.`child1_name`,
            IF(ec.`child1_dob`='00-00-0000', '', date_format(ec.`child1_dob`,'%d-%b-%Y')) as child1_dob,ec.`child1_age`,ec.`child2_name`,
            IF(ec.`child2_dob`='00-00-0000', '', date_format(ec.`child2_dob`,'%d-%b-%Y')) as child2_dob,
            ec.`child2_age`,GetUserName(e.`updated_by`,2) AS created_by,date_format(e.`updated_at`,'%d-%b-%Y') as updated_at,e.`father_name`,ec.`emergency_relation`,ec.`cu_address`,
            getStateNameById(ec.`pe_state`) as pe_state,
            ec.`pe_city`,ec.cu_city,getStateNameById(ec.cu_state) as cu_state,ec.`pe_zip_code`,ec.`cu_zip_code`,ec.emergency_name,getBusinessUnitName(e.business_unit_id) AS 'Business_unit',
                getMastLookupValue(e.`department`) AS department,getMastLookupValue(e.`employment_type`) AS employment_type,e.`nationality`,
                CASE WHEN e.status = 1 THEN 'Active' ELSE getMastLookupValue(e.`status`) END AS `status`,
                e.aadhar_number,e.mother_name,e.aadhar_name,e.pan_card_name,
                (SELECT but.cost_center FROM business_units but WHERE but.bu_id=e.`business_unit_id`) AS 'Cost_Center',eb.bank_name as bank_name,
                eb.branch_name as branch_name,eb.acc_type as account_type,eb.acc_no as account_no,eb.ifsc_code as ifsc_code
                FROM employee e
                LEFT JOIN emp_skills es ON es.`employee_id` = e.`emp_id`
                LEFT JOIN emp_contact ec ON ec.`employee_id` = e.`emp_id`
                LEFT JOIN emp_education ee ON ee.`emp_id` = e.`emp_id`
                LEFT JOIN emp_work_experience ew ON ew.`employee_id` = e.`emp_id`
                LEFT JOIN emp_insurance ei ON ei.`employee_id` = e.`emp_id`
                LEFT JOIN emp_bank eb ON eb.emp_id=e.emp_id
                WHERE e.`status` IN (".$status.")
                GROUP BY e.`emp_id`";
                //e.`emp_id` IN (".$implode_empids.")
        $allData = DB::select(DB::raw($exportEmployeeDetails));
        return $allData;

    }


    public function getDetailsByStatusId($statusid){

      /*  $activeinactiveEmployees = DB::table('employee as emp')
                ->where('emp.status', '=', $statusid)
                ->pluck('emp.emp_id')->all();

            $implode_empids = implode(',', $activeinactiveEmployees);


            if($implode_empids == ""){
                $implode_empids = 0;
            }
*/
            $exportEmployeeDetails = "select e.`emp_id`,e.`emp_code`, e.`firstname`,e.`lastname`,e.`doj`,e.`email_id`,
            ec.`emergency_contact_one`,getMastLookupValue(e.`designation`) as designation,ew.`organization_name`,getRepManagernameHierarchy(e.`reporting_manager_id`) AS reporting_manager,
            e.`exit_date`,getBusinessUnitName(e.business_unit_id) AS 'Business_unit',
                getMastLookupValue(e.`department`) AS department,getMastLookupValue(e.`employment_type`) AS employment_type,
                CASE WHEN e.status = 1 THEN 'In Active' ELSE getMastLookupValue(e.`status`) END AS `status`
                FROM employee e
                LEFT JOIN users u on e.`emp_id` = u.`emp_id`
                LEFT JOIN emp_skills es ON es.`employee_id` = e.`emp_id`
                LEFT JOIN emp_contact ec ON ec.`employee_id` = e.`emp_id`
                LEFT JOIN emp_education ee ON ee.`emp_id` = e.`emp_id`
                LEFT JOIN emp_work_experience ew ON ew.`employee_id` = e.`emp_id`
                WHERE e.status=".$statusid."
                GROUP BY e.`emp_id`";//e.emp_id IN (".$implode_empids.")

        $allData = DB::select(DB::raw($exportEmployeeDetails));
        return $allData;

    }

}
