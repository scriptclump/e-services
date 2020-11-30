<?php
namespace App\Modules\HrmsEmployees\Controllers;
use View;
use Session;
use Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BaseController;
use URL;
use Log;
use Response;
use Illuminate\Http\Request;
use Redirect;
use \App\Modules\HrmsEmployees\Models\ExportEmployeeModel;
use Carbon\Carbon;
use Excel;

Class ExportEmployeeController extends BaseController {
    
    
    public function __construct() {
      
        $this->ExportEmployeModel = new ExportEmployeeModel();
    }



     // download Export all active inactive Employees report
   public function exportEmployeesData(Request $request){
    $inputs = $request->input();
    $data = $this->ExportEmployeModel->getEmployeeAllDetails($inputs);
    $data = json_decode(json_encode($data), true);
        $emp_array =array();
        foreach ($data as $value) {
            //$emp_array[$value['emp_id']]['emp_id'] = $value['emp_id'];
            $emp_array[$value['emp_id']]['emp_code'] = $value['emp_code'];
            $emp_array[$value['emp_id']]['firstname'] = $value['firstname'];
            $emp_array[$value['emp_id']]['lastname'] = $value['lastname'];
            $emp_array[$value['emp_id']]['doj'] = $value['doj'];
            $emp_array[$value['emp_id']]['designation'] = $value['designation'];
            $emp_array[$value['emp_id']]['reporting_manager'] = $value['reporting_manager'];
            $emp_array[$value['emp_id']]['Business_unit'] = $value['Business_unit'];
            $emp_array[$value['emp_id']]['Cost_Center'] = $value['Cost_Center'];
            $emp_array[$value['emp_id']]['department'] = $value['department'];
            $emp_array[$value['emp_id']]['employment_type'] = $value['employment_type'];
            $emp_array[$value['emp_id']]['exit_date'] = $value['exit_date'];
            $emp_array[$value['emp_id']]['mobile_no'] = $value['mobile_no'];
            $emp_array[$value['emp_id']]['alternative_mno'] = $value['alternative_mno'];
            $emp_array[$value['emp_id']]['status'] = $value['status'];
            $emp_array[$value['emp_id']]['email_id'] = $value['email_id'];
            $emp_array[$value['emp_id']]['office_email'] = $value['office_email'];
            $emp_array[$value['emp_id']]['dob'] = $value['dob'];
            $emp_array[$value['emp_id']]['gender'] = $value['gender'];
            $emp_array[$value['emp_id']]['marital_status'] = $value['marital_status'];
            $emp_array[$value['emp_id']]['aadhar_number'] = $value['aadhar_number'];
            $emp_array[$value['emp_id']]['pan_card_number'] = $value['pan_card_number'];
            $emp_array[$value['emp_id']]['uan_number'] = $value['uan_number'];
            $emp_array[$value['emp_id']]['pe_address'] = $value['pe_address'];
            $emp_array[$value['emp_id']]['pe_city'] = $value['pe_city'];
            $emp_array[$value['emp_id']]['pe_state'] = $value['pe_state'];
            $emp_array[$value['emp_id']]['pe_zip_code'] = $value['pe_zip_code'];
            $emp_array[$value['emp_id']]['emergency_contact_one'] = $value['emergency_contact_one'];
            $emp_array[$value['emp_id']]['landline_ext'] = $value['landline_ext'];
            $emp_array[$value['emp_id']]['degree'] = $value['degree'];
            $emp_array[$value['emp_id']]['blood_group'] = $value['blood_group'];
            $emp_array[$value['emp_id']]['emergency_contact_two'] = $value['emergency_contact_two'];
            $emp_array[$value['emp_id']]['spouse_name'] = $value['spouse_name'];
            $emp_array[$value['emp_id']]['spouse_dob'] = $value['spouse_dob'];
            $emp_array[$value['emp_id']]['no_of_childerns'] = $value['no_of_childerns'];
            $emp_array[$value['emp_id']]['child1_name'] = $value['child1_name'];
            $emp_array[$value['emp_id']]['child1_dob'] = $value['child1_dob'];
            $emp_array[$value['emp_id']]['child2_name'] = $value['child2_name'];
            $emp_array[$value['emp_id']]['child2_dob'] = $value['child2_dob'];
            $emp_array[$value['emp_id']]['created_by'] = $value['created_by'];
            $emp_array[$value['emp_id']]['updated_at'] = $value['updated_at'];
            $emp_array[$value['emp_id']]['father_name'] = $value['father_name'];
            $emp_array[$value['emp_id']]['emergency_name'] = $value['emergency_name'];
            $emp_array[$value['emp_id']]['card_number'] = $value['card_number'];
            $emp_array[$value['emp_id']]['tpa'] = $value['tpa'];
            $emp_array[$value['emp_id']]['tpa_contact_number'] = $value['tpa_contact_number'];
            $emp_array[$value['emp_id']]['cu_address'] = $value['cu_address'];
            $emp_array[$value['emp_id']]['cu_city'] = $value['cu_city'];
            $emp_array[$value['emp_id']]['cu_state'] = $value['cu_state'];
            $emp_array[$value['emp_id']]['cu_zip_code'] = $value['cu_zip_code'];
            $emp_array[$value['emp_id']]['nationality'] = $value['nationality'];
            $emp_array[$value['emp_id']]['mother_name'] = $value['mother_name'];
            $emp_array[$value['emp_id']]['bank_name'] = $value['bank_name'];
            $emp_array[$value['emp_id']]['branch_name'] = $value['branch_name'];
            $emp_array[$value['emp_id']]['account_type'] = $value['account_type'];
            $emp_array[$value['emp_id']]['account_no'] = (int)$value['account_no'];
            $emp_array[$value['emp_id']]['ifsc_code'] = $value['ifsc_code'];
        }

        $headers = array('Emp Code','First Name','Last Name','DOJ','Designation','Reporting Manager','Business Unit','Cost Center','Department','Employment Type','Exit Date','Mobile Number','Alt Mobile','Status','Email ID','Office Email','DOB','Gender','Marital Status','Aadhar Number','PAN Card Number','UAN Number','Permanent Address','Permanent City','Permanent State','Permanent Pincode','Emergency Contact','LandlineExt','Degree','Blood Group','Emergency Contact Two','Spouse Name','Spouse DOB','No. Of Childrens','Child1Name','Child1DOB','Child2Name','Child2_DOB','Created By','Updated At','FatherName','Emergency Contact Person','Card Number','TPA','TFA Contact Number','Current Address','Current City','Current State','Current Pincode','Nationality','Mother Name','Bank Name','Branch Name','Account Type','Account No','IFSC Code');
        $mytime = Carbon::now();

            Excel::create('Export Employees Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers,$emp_array) 
            {
                $excel->sheet("EmployeesReport", function($sheet) use($headers, $emp_array)
                {
                    $sheet->loadView('HrmsEmployees::ExportEmployeeReport', array('headers' => $headers,'data' => $emp_array)); 
                });
            })->export('xlsx');

    }




    public function exportDataByStatus($statusid){
        $data = $this->ExportEmployeModel->getDetailsByStatusId($statusid);
        $data = json_decode(json_encode($data), true);
        $emp_array =array();
        foreach ($data as $value) {
           // $emp_array[$value['emp_id']]['emp_id'] = $value['emp_id'];
            $emp_array[$value['emp_id']]['emp_code'] = $value['emp_code'];
            $emp_array[$value['emp_id']]['firstname'] = $value['firstname'];
            $emp_array[$value['emp_id']]['lastname'] = $value['lastname'];
            $emp_array[$value['emp_id']]['doj'] =isset($value['doj'])?date('d-M-Y', strtotime( $value['doj'] )):"";
            $emp_array[$value['emp_id']]['email_id'] = $value['email_id'];
            $emp_array[$value['emp_id']]['designation'] = $value['designation'];
            $emp_array[$value['emp_id']]['reporting_manager'] = $value['reporting_manager'];
            $emp_array[$value['emp_id']]['exit_date'] =isset($value['exit_date'])?date('d-M-Y', strtotime( $value['exit_date'] )):"";
            $emp_array[$value['emp_id']]['Business_unit'] = $value['Business_unit'];
            $emp_array[$value['emp_id']]['department'] = $value['department'];
            $emp_array[$value['emp_id']]['employment_type'] = $value['employment_type'];
            $emp_array[$value['emp_id']]['status'] = $value['status'];
        }

        $headers = array('Emp Code','First Name','Last Name','DOJ','Email Id','Designation','Reporting Manager','Exit Date','Business Unit','Department','Employement Type','Status');
        $mytime = Carbon::now();

            Excel::create('Export Employees Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers,$emp_array) 
            {
                $excel->sheet("EmployeesReport", function($sheet) use($headers, $emp_array)
                {
                    $sheet->loadView('HrmsEmployees::ExportSingleStatusReport', array('headers' => $headers,'data' => $emp_array)); 
                });
            })->export('xlsx');

    }





     
}
