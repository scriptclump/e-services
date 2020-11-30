<?php

namespace App\Modules\HrmsEmployees\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Mail;
use Session;
use Log;
use App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Utility;

class ExitProcessModel extends Model {

    protected $table = 'employee';
    protected $primaryKey = "emp_id";


    public function getEmpIdWithTableId($tblid) {
        $query = "select emp_id from emp_exit where emp_exit_id=$tblid";
        $data = DB::select(DB::raw($query));

        return $data[0]->emp_id;
    }

    public function getEmployeeApprovalStatus($tbl_id) {
        $query = "select * from employee where emp_id=$tbl_id";
        $data = DB::select(DB::raw($query));
        return $data;
    }

    public function updateMainTableWithStatus($exitdata, $nextstatusid) {

        $updateArray = array(
            'comment' => $exitdata['comments'],
            'status' => $nextstatusid,
            'updated_by' => Session::get('userId'),
            'updated_at' => date('Y-m-d'),
        );

        if (isset($exitdata['join_date']) && $exitdata['join_date'] != "") {
            $updateArray['doj'] = date('Y-m-d', strtotime($exitdata['join_date']));
        }

        $update = DB::table('employee')
                ->where('emp_id', '=', $exitdata['hidden_emp_id'])
                ->update($updateArray);
        // user inactive when isfinal
        if ($nextstatusid == 1) {
            $inactiveuser = DB::table('users')->where('emp_id', '=', $exitdata['hidden_emp_id'])->update(['is_active' => 0]);
            $inactiveemployee = DB::table('employee')->where('emp_id', '=', $exitdata['hidden_emp_id'])->update(['is_active' => 0]);
             
        }
        return $update;

    }

    public function getHrmsApprovalHistorByIDFromDB($tblmainid, $apprName) {
        $approval_flow_func = new CommonApprovalFlowFunctionModel();
        $totalhistory=$approval_flow_func->getApprovalHistoryFromCommentsTable($tblmainid,$apprName);         
        if(count($totalhistory)>0){
                $history=json_decode($totalhistory[0]->comments,1);
                $history=json_decode(json_encode($history));
            }else{
                $history = DB::table('appr_workflow_history as hs')
                        ->join('users as us', 'us.user_id', '=', 'hs.user_id')
                        ->join('user_roles as ur', 'ur.user_id', '=', 'hs.user_id')
                        ->join('roles as rl', 'rl.role_id', '=', 'ur.role_id')
                        ->join('master_lookup as ml', 'ml.value', '=', 'hs.status_to_id')
                        ->select('hs.awf_history_id', 'us.profile_picture', 'us.firstname', 'us.lastname', DB::raw('group_concat(rl.name) as name'), DB::raw("date_format(hs.created_at, '%Y-%m-%d') as 'created_at' "), 'hs.status_to_id', 'hs.status_from_id', DB::raw('getMastLookupValue(hs.status_from_id) as action_name'), 'hs.awf_comment', 'ml.master_lookup_name')
                        ->where('hs.awf_for_id', $tblmainid)
                        ->where('hs.awf_for_type', $apprName)
                        ->groupBy('hs.created_at')
                        ->orderBy('hs.awf_history_id')
                ->get()->all();
            }
        //array_shift($history);
        return $history;
    }

    public function checkingthestatushotcoded($empid) {
        $query = "select status from employee where emp_id=$empid";
        $data = DB::select(DB::raw($query));

        return $data[0]->status;
    }

    // function is for active the user in users table
    public function changeTheUserStatusInEmployeeTable($empid, $exitdata) 
    {
        DB::beginTransaction();
        $activecount = DB::table('users as us')
                ->join('employee as emp', 'us.emp_id', '=', 'emp.emp_id')
                ->where('emp.emp_id', '=', $empid)
                ->where('us.emp_id', '=', $empid)
                ->where('emp.is_active', '=', 1)
                ->where('us.is_active', '=', 1)
                ->count();

        if ($activecount == 0)
         {
            // update the official email id employee table
            $updateOffice_mail = $this->updateofficialEmailId($empid, $exitdata);
            DB::commit();
        }
        else
        {
            DB::rollback();
//            Log::info("office email is not generated");
        }

        return $updateOffice_mail;

    }

    public function updateDOL($empid) {

        $empDetails = DB::table('employee')
                      ->where('emp_id', '=', $empid)
                      ->update(['exit_date'=> NULL,'is_active'=>1]);
        $userDetails =  DB::table('users')->where('emp_id', '=', $empid)->update(['is_active'=>1]);
        if($empDetails==1 && $userDetails==1)
            return $empDetails;
        else
            return 0;
    } 


    //written common function for save data
    public function saveDataIntoUsersTable($allDetails) {
        $query = "select * from users where mobile_no = '".$allDetails[0]->mobile_no."' and  (emp_id = 0 or emp_id is null) order by user_id desc limit 1";
        $isDataExist = DB::select(DB::raw($query));

        $isDataExist=json_decode(json_encode($isDataExist), true);
        $savedata = array(
            'emp_id' => $allDetails[0]->emp_id,
            'business_unit_id' => $allDetails[0]->business_unit_id,
            'password' => $allDetails[0]->password,
            'firstname' => $allDetails[0]->firstname,
            'lastname' => $allDetails[0]->lastname,
            'email_id' => $allDetails[0]->office_email,
            'department' => $allDetails[0]->department,
            'designation' => $allDetails[0]->designation,
            'mobile_no' => $allDetails[0]->mobile_no,
            'legal_entity_id' => $allDetails[0]->legal_entity_id,
            'landline_ext' => $allDetails[0]->landline_ext,
            'profile_picture' => $allDetails[0]->profile_picture,
            'is_active' => 1,
            'reporting_manager_id' => $allDetails[0]->reporting_manager_id,
            'emp_code' => $allDetails[0]->emp_code,
            'updated_by'=>$allDetails[0]->updated_by,
        );
        if(count($isDataExist)>0){
            $updateAlreadyExistedData=DB::table('users')
                                    ->where('user_id',$isDataExist[0]['user_id'])
                                    ->update($savedata);
            $user_id=$isDataExist[0]['user_id'];

        }else{
            
            $save = DB::table("users")->insert($savedata);
            $user_id = DB::getPdo()->lastInsertId($save);
        }        
        return $user_id;
    }

    //write a function for save data into user_roles table

    public function saveIntoUserRolesTable($ids, $user_id) {
        $save = "";
        foreach ($ids as $eachId) {
            $qty_data = array(
                'role_id' => $eachId,
                'user_id' => $user_id,
                'created_by' => Session::get('userId'),
                'created_at' => date('Y-m-d')
            );
            $save[$user_id][] = DB::table("user_roles")->insert($qty_data);
        }
        return $save;
    }

    //update emp code in employee table
    public function generateTheEmpCode($empid) {
        DB::beginTransaction();
        $empDetails = DB::table('employee')
                ->where('emp_id', '=', $empid);
        $emptype = $empDetails->get(["employment_type"])->all();

        $employee_code = DB::select(DB::raw("CALL getEmpCode(" . $emptype[0]->employment_type . ");"));
//        $emptype = $empDetails->get(["emp_id", DB::raw("getMastLookupValue(employment_type) as employment_type")]);
//
//        if ($emptype[0]->employment_type == "FTA [Full Time Associate]") {
//            $employee_code = "FTA_" . $empid;
//        } else if ($emptype[0]->employment_type == "ICA [Individual Contractor]") {
//            $employee_code = "ICA_" . $empid;
//        } else {
//            $employee_code = "PTA_" . $empid;
//        }
        $update = $empDetails->update(['emp_code' => $employee_code[0]->emp_code]);
        if($update){
            DB::commit(); 
            return $update;
        }else{
            DB::rollback();
           // Log::info("Employee code is not generated");
        }

        return "Employee code is not generated";
    }

     public function addLeavesByEmpId($empid) {
        $empDetails = DB::table('employee')
                ->where('emp_id', '=', $empid)->get()->all();
        if(count($empDetails)>0){
            if($empDetails[0]->employment_type == 152001){

                $joining_date = $empDetails[0]->doj;
                $finaldate = date_parse_from_format("Y-m-d", $joining_date);
                $remaining = $finaldate['month']%3;
                if($remaining == 0){
                    $no_of_leaves = 1;
                }else if($remaining == 1){
                    $no_of_leaves = 3;
                }else if($remaining == 2){
                    $no_of_leaves = 2;
                }
                $leave_master[0] = array(
                    'emp_id' => $empid,
                    'leave_type' => 148001,
                    'no_of_leaves' => $no_of_leaves,
                    'created_by' => Session::get('userId'),
                    'created_at' => date('Y-m-d')
                );

                $leave_master[1] = array(
                    'emp_id' => $empid,
                    'leave_type' => 148002,
                    'no_of_leaves' => $no_of_leaves,
                    'created_by' => Session::get('userId'),
                    'created_at' => date('Y-m-d')
                );

                $leave_master[2] = array(
                    'emp_id' => $empid,
                    'leave_type' => 148005,
                    'no_of_leaves' => 2,
                    'created_by' => Session::get('userId'),
                    'created_at' => date('Y-m-d')
                );

            }else{
                $leave_master[0] = array(
                    'emp_id' => $empid,
                    'leave_type' => 148002,
                    'no_of_leaves' => 1,
                    'created_by' => Session::get('userId'),
                    'created_at' => date('Y-m-d')
                );

                $leave_master[1] = array(
                    'emp_id' => $empid,
                    'leave_type' => 148005,
                    'no_of_leaves' => 2,
                    'created_by' => Session::get('userId'),
                    'created_at' => date('Y-m-d')
                );

            }
            return DB::table("leave_master")->insert($leave_master);

        }else{
            return 0;
        }
    }

    public function updateofficialEmailId($empid, $exitdata) {

//        $office_email = isset($exitdata['employee_email_id']) ? $exitdata['employee_email_id'] : '';
        
        $empDetails = DB::table('employee')
                ->where('emp_id', '=', $empid)
                ->update(['office_email' => $exitdata['employee_email_id']]);
        return $empDetails;
    }

    public function updateTheExitDate($empid, $exitdata) {
        DB::beginTransaction();
        $exit_emp_date = isset($exitdata['employee_exit_date']) ? $exitdata['employee_exit_date'] : '';
        $date = date('Y-m-d', strtotime($exit_emp_date));
        $empDetails = DB::table('employee')
                ->where('emp_id', '=', $empid)
                ->update(['exit_date' => $date]);
        $inactiveuser = DB::table('users')->where('emp_id', '=', $empid)->update(['is_active' => 0]);
        $inactiveemployee = DB::table('employee')->where('emp_id', '=', $empid)->update(['is_active' => 0]);
        if($empDetails && $inactiveuser && $inactiveemployee){
            DB::commit();
        }else{
            DB::rollback();
           // Log::info("exit date is not updated");
        }
        return 1;

        }




    public function sendEmailNotificationInEveryStep($exitdata,$NextStatusID){

        try{
        $environment = env('APP_ENV');

        $empName = DB::table("employee")
                ->where("emp_id", "=", $exitdata['hidden_emp_id'])
                ->get()->all();

        $roleid = Session::get('roleId');
        $updatedBy = Session::get('userName');
        $currentdate  = date('d-m-Y H:i:s');
        $legalEntiryID = Session::get('legal_entity_id');

        $getrolename = DB::table("roles")
                ->where("role_id", "=", $roleid)
                ->select('name')
                ->get()->all();

        //get the current status name
                $getCurrentStatusname = DB::table("master_lookup")
                ->where("value", "=", $exitdata['currentStatusId'])
                ->select('master_lookup_name')
                ->get()->all();
                         
            $topMsg  = "Please find the below Update.";
            $emailContent = "Employee Name: ".$empName[0]->firstname. " ".$empName[0]->lastname." <br/><br/>";
            $emailContent .= "Updated by: ".$updatedBy. " (".$getrolename[0]->name.")<br/><br/>";
            $emailContent .= "Updated at: ".$currentdate. "<br/><br/>";
           // $emailContent .= "Action Taken : ".$exitdata['nextstatusname']." By ".$getrolename[0]->name. " and the current status is ".$getCurrentStatusname[0]->master_lookup_name.".<br/><br/>"; 

            $emailContent .= "Previous Status: ".$getCurrentStatusname[0]->master_lookup_name."<br/><br/>";
            $emailContent .= "Action Taken: ".$exitdata['condition']."<br/><br/>";
            $emailContent .= "Current Status: ".$exitdata['nextstatusname']."<br/><br/>";

            $subsubject = "".$exitdata['nextstatusname']." - ".$empName[0]->firstname. " ".$empName[0]->lastname." ";

            $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $userIdData= $notificationObj->getUsersByCode('HRMSH0001');
            $userIdData=json_decode(json_encode($userIdData));
            $subject=$notificationObj->getMessageByCode('HRMSH0001');
            $subject = str_replace(array("#Nextstatusname","#firstname","#lastname"),array($exitdata['nextstatusname'],$empName[0]->firstname,$empName[0]->lastname), $subject);
            $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get()->all();
            $emails=json_decode(json_encode($data,1),true);
            $getEmails=array();
            foreach ($emails as $keyValue ){
                $getEmails[]=$keyValue['email_id'];
            }
            $body = array('template'=>'emails.hrmsMail', 'attachment'=>'', 'topMsg'=>$topMsg, 'emailContent' =>  $emailContent,'name'=>"Tech Support - " . $environment);

            Utility::sendEmail($getEmails, $subsubject, $body);
                    
        // Mail::send('emails.hrmsMail', ['topMsg'=>$topMsg, 'emailContent' =>  $emailContent], function ($message) use ($environment,$subsubject,$getEmails) {
                        
        // $message->from("tracker@ebutor.com", $name = "Tech Support - " . $environment);
                  /*if( $environment=='production'){
                            $message->to("hr@ebutor.com");
                        }else{
                            $message->to("hr@yopmail.com");
                        }
                        $message->subject($subsubject);*/

        //     $message->to($getEmails)->subject($subsubject);
                        
        // });
        
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
         }

    }

    public function checkTheEmailExistOrNot($emailid){

        $emailDetails = DB::table('employee')
                ->where('office_email', '=', $emailid)
                ->where('is_active','=',1)
                ->count(); 

        $useremailcheck = DB::table('users')
                ->where('email_id', '=', $emailid)
                ->where('is_active','=',1)
                ->count(); 

        return ($emailDetails && $useremailcheck > 0) ? TRUE : FALSE;
    }

    public function saveSkillsIntoTable($skilldata){
        $data = DB::table("emp_skills")->insert($skilldata);
        return DB::getPdo()->lastInsertId($data);
    }

    public function getTheSkillsData($id){

        $skillDetails = DB::table('emp_skills_master as esm')
                ->join('emp_skills as es', 'es.emp_skill_id', '=', 'esm.emp_skill_id')
                ->where('es.employee_id', '=', $id)
                ->get()->all();
        return $skillDetails;

    }

    public function deleteSkillEmployee($id){

        $deleteSkillData = DB::table('emp_skills')
                            ->where('skill_id', '=', $id)
                            ->delete();
        return $deleteSkillData;
    }

    public function getSkillnames($term){

        $getlist = "select * from emp_skills_master
                    where  skill_name like '%".$term."%'";
        $allData = DB::select(DB::raw($getlist));
        $users_arr = array();

        foreach($allData  as $getnames) {
            $users = array("label" => $getnames->skill_name,"skill_id" => $getnames->emp_skill_id);
            array_push($users_arr, $users); 
        }
        return $users_arr;
    }

    public function checkTheSkillName($skillname){

        $emailDetails = DB::table('emp_skills_master')
                ->select('emp_skill_id')
                ->where('skill_name', '=', $skillname)
                ->get()->all();

        return $emailDetails;
    }

    public function saveSkillsIntoMasterTable($skilldata){

        $savedata = array(
            'skill_name' => $skilldata['skill'],
        );
        $data = DB::table("emp_skills_master")->insert($savedata);
        return DB::getPdo()->lastInsertId($data);
    }

    public function saveintoskillstable($skilldata,$skilllastid){

         $savedata = array(
            'employee_id' => $skilldata['employee_hidden_id'],
            'emp_skill_id' => $skilllastid
        );
         $data = DB::table("emp_skills")->insert($savedata);
         return $data;
    }
    public function checkTheDuplicateSkillid($skillid,$employee_hidden_id){
        $emailDetails = DB::table('emp_skills as es')
                //->join('emp_skills_master as esm', 'es.emp_skill_id', '=', 'esm.emp_skill_id')
                ->where('es.emp_skill_id', '=', $skillid)
                //->where('esm.skill_name','=',$skillname)
                ->where('es.employee_id','=',$employee_hidden_id)
                ->count();
        return $emailDetails;
    }

    public function getTheSkillIdByname($name){
        $emailDetails = DB::table('emp_skills_master')
                ->where('skill_name', '=', $name)
                ->get()->all();
        return $emailDetails;

    }

    public function saveIntoUsertable($empid,$exitdata){

        if($exitdata['currentStatusId'] == 57153 || $exitdata['currentStatusId'] == 57152){
            DB::beginTransaction();
            $empDetails = DB::table('employee')
                    ->where('emp_id', '=', $empid);
            //insert into users table
            $allDetails = $empDetails->get()->all();

            // write a function for save data into users table
            $user_id = $this->saveDataIntoUsersTable($allDetails);

            //save data into user_roles table
            $ids = explode(',', $allDetails[0]->role_id);
            if($user_id){
                $user_roles = $this->saveIntoUserRolesTable($ids, $user_id);
            }
            
            $update = $empDetails->update(['is_active' => 1]);
            if ($update) {
                $addleaves = $this->addLeavesByEmpId($empid);
                DB::commit(); 
            }else{
                DB::rollback();
           //     Log::info("Hrms leaves and userdata has roll back");
            }

            return $update;

        }else{

            return 1;

        }
        
    }

    public function checkSkillNameBySelectSkillname($skillname,$empid){

        $query = "select COUNT(*) AS COUNT 
                FROM emp_skills_master esm, emp_skills es
                WHERE esm.emp_skill_id = es.emp_skill_id
                AND esm.skill_name = '".$skillname."'
                AND es.employee_id = $empid";

        $data = DB::select(DB::raw($query));
        return $data[0]->COUNT;

    }


}
