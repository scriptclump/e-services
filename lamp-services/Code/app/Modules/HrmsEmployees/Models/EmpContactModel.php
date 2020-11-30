<?php

namespace App\Modules\HrmsEmployees\Models;

use App\Central\Repositories\RoleRepo;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Session;
use URL;
class EmpContactModel extends Model {

    protected $table = 'emp_contact';
    protected $primaryKey = 'employee_contact_id';
    public $timestamps = false;
    public function __construct() 
    {
        
    }
    public function getEmpPersonalDetails($empId)
    {
        $empPersonalInfo = $this->where('employee_id',$empId)
                                ->select(DB::raw('getStateNameById(cu_state) as cu_state'),DB::raw('getCountryNameById(cu_country) as cu_country'),DB::raw('getStateNameById(pe_state) as pe_state'),DB::raw('getCountryNameById(pe_country) as pe_country'),"emergency_name","emergency_relation","emergency_contact_one","emergency_contact_two","cu_address","cu_address2","cu_city","cu_zip_code","pe_address","pe_address2","pe_city","pe_zip_code","cu_state as cu_state_id","cu_country as cu_country_id","pe_state as pe_state_id","pe_country as pe_country_id","ref_one_relation","ref_one_contact_no","ref_one_address","ref_one_city",DB::raw('getStateNameById(ref_one_state) as ref_one_state'),"ref_one_state as ref_one_state_id",DB::raw('getCountryNameById(ref_one_country) as ref_one_country'),"ref_one_country as ref_one_country_id","ref_two_contact_no","ref_one_pin_code","ref_two_address","ref_two_city",DB::raw('getStateNameById(ref_two_state) as ref_two_state'),"ref_two_state as ref_two_state_id",DB::raw('getCountryNameById(ref_two_country) as ref_two_country'),"ref_two_country as ref_two_country_id","ref_two_pin_code","ref_two_relation")
                                ->first();
        return $empPersonalInfo;
    }
     public function InsertCertificationDetails($empId, $data)
    {
        $rs= DB::table("emp_certification")
            ->insert(array("employee_id"=>$empId,"certification_name"=>$data['certification_name'],"institution_name"=>$data['institution_name'],"grade"=>$data['grade'],"certified_on"=>$data['certified_on'],"valid_upto"=>$data['valid_upto']));
        return $rs;
    }
     public function getEmpCertiDetails($empId)
    {
       $rs= DB::table("emp_certification")
            ->where('employee_id',$empId)
            ->select(DB::raw('DATE_FORMAT(certified_on,"%d-%b-%Y") as certified_on'),DB::raw('DATE_FORMAT(valid_upto,"%d-%b-%Y") as valid_upto'),"certification_name","institution_name","grade","employee_certification_id")
            ->get()->all();
        $rs = json_decode(json_encode($rs), true);    
        return $rs;
    }
    public function DeleteCertificationDetails($cer_id)
    {
        $rs= DB::table('emp_certification')
            ->where('employee_certification_id',$cer_id)
            ->delete();
        return $rs;
    }
    public function empBankInfo($empid)
    {
        $query = "select * FROM emp_bank WHERE emp_id = $empid limit 1";       
         $empData = DB::selectFromWriteConnection($query);        
         $empData = json_decode(json_encode($empData),true);       
            if(!empty($empData))      
            return $empData[0];
            else
            return $empData;
    }
     public function empSkillInfo($empid)
    {
        $empData = Db::table('emp_skills')
                    ->where('employee_id',$empid)
                    ->first();
        $empData = json_decode(json_encode($empData),true);
        return $empData;
    }
    public function UpdateBankDetails($data,$emp_id)
    {
        $rs = DB::table("emp_bank")
                ->where('emp_id',$emp_id)
                ->update($data);
        return $rs;

    }
    public function InsertBankDetails($data)
    {
        $rs = DB::table("emp_bank")
                ->insert($data);
        return $rs;

    }
    public function getAccounttype() {
        $account_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.value as id')
                ->where('master_lookup_categories.mas_cat_id', '=', '31')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Account_Type')
                ->get()->all();
        return $account_data;
    }
    public function getCurrency() {
        $currency_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as currency_name', 'master_lookup.value as id')
                ->where('master_lookup_categories.mas_cat_id', '=', '46')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Currency')
                ->get()->all();
        return $currency_data;
    }
    public function getEduTypeData()
    {
        $rs = DB::table('education')
                ->get()->all();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function InsertEductionDetails($empId, $data)
    {
        $data['emp_id'] = $empId;
        $rs =  DB::table('emp_education')
                ->insert($data);
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
     public function UpdateEductionDetails($id, $data)
    {
        $rs =  DB::table('emp_education')
                ->where("emp_education_id",$id)
                ->update($data);
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function getEmpEductionDetails($empId)
    {
        $rs =  DB::table('emp_education')
                ->where('emp_id',$empId)
                ->select(DB::raw('DATE_FORMAT(from_year,"%d-%b-%Y") as from_year'),DB::raw('DATE_FORMAT(to_year,"%d-%b-%Y") as to_year'),"specilization","degree","institute","grade","emp_education_id")
                ->orderby('emp_education_id','DESC')
                ->get()->all();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function checkEmpInsurance($empId)
    {
        $rs =DB::table('emp_insurance')
            ->where('employee_id',$empId)
            ->select(DB::raw('DATE_FORMAT(spouse_dob,"%d-%b-%Y") as spouse_dob'),DB::raw('DATE_FORMAT(child_one_dob,"%d-%b-%Y") as child_one_dob'),
                DB::raw('DATE_FORMAT(child_two_dob,"%d-%b-%Y") as child_two_dob'),"spouse_name","no_of_child","child_one_name","child_two_name","card_number","tpa","tpa_contact_number")
            ->first();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function UpdateEmpInsurance($empId,$data)
    {
        $rs =DB::table('emp_insurance')
            ->where('employee_id',$empId)
            ->update($data);
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function InsertEmpInsurance($empId,$data)
    {
        $data['employee_id'] = $empId;
        $rs =DB::table('emp_insurance')
            ->insert($data);
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function getExperienceData($empId)
    {
        $rs = DB::table('emp_work_experience')
                ->where('employee_id',$empId)
                ->select(DB::raw('DATE_FORMAT(from_date,"%d-%b-%Y") as from_date'),DB::raw('DATE_FORMAT(to_date,"%d-%b-%Y") as to_date'),"work_experience_id","employee_id","ep_emp_id","organization_name","designation","location","reference_name","reference_contact_number")
                ->orderby("work_experience_id","DESC")
                ->get()->all();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function InsertEmpExperience($empId,$data)
    {
        $data['employee_id'] = $empId;
        $data['reference_contact_number'] = ($data['reference_contact_number']!="")?$data['reference_contact_number']:null;
        $rs =DB::table('emp_work_experience')
            ->insert($data);
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function getEmpExperienceDataById($id)
    {
        $rs =DB::table('emp_work_experience')
              ->where('work_experience_id',$id)
              ->select(DB::raw('DATE_FORMAT(from_date,"%d-%b-%Y") as from_date'),DB::raw('DATE_FORMAT(to_date,"%d-%b-%Y") as to_date'),"work_experience_id","employee_id","ep_emp_id","organization_name","designation","location","reference_name","reference_contact_number")
            ->first();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function UpdateEmpExperience($empId,$data)
    {
        $data['reference_contact_number'] = ($data['reference_contact_number']!="")?$data['reference_contact_number']:null;
        $rs =DB::table('emp_work_experience')
            ->where('employee_id',$empId)
            ->where('work_experience_id',$data['work_experience_id'])
            ->update($data);
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
     public function DeleteEmpExperience($id)
    {
        $rs =DB::table('emp_work_experience')
            ->where('work_experience_id',$id)
            ->delete();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function getEmpCertificationData($id)
    {
        $rs =DB::table('emp_certification')
            ->where('employee_certification_id',$id)
            ->select(DB::raw('DATE_FORMAT(certified_on,"%d-%b-%Y") as certified_on'),DB::raw('DATE_FORMAT(valid_upto,"%d-%b-%Y") as valid_upto'),"employee_certification_id","employee_id","certification_name","institution_name","grade")
            ->first();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }
    public function editEducationById($id)
    {
        $rs =  DB::table('emp_education')
                ->where('emp_education_id',$id)
                ->select(DB::raw('DATE_FORMAT(from_year,"%d-%b-%Y") as from_year'),DB::raw('DATE_FORMAT(to_year,"%d-%b-%Y") as to_year'),"degree","institute","grade","specilization","emp_education_id","emp_education_id")
                ->first();
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }

    public function getempfullname($empId){
        $fullname=DB::table('emp_bank')
                          ->join('employee', 'employee.emp_id', '=', 'emp_bank.emp_id')
                          ->where('emp_bank.emp_id',$empId)
                          ->select('emp_code',DB::raw("CONCAT(employee.firstname,' ',employee.lastname) AS fullname"))
                          ->get()->all();
        $fullname = isset($fullname[0]->emp_code)? json_decode(json_encode($fullname[0]), true): [];
        return $fullname;

    }


}