<?php

namespace App\Modules\HrmsEmployees\Controllers;

use Session;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BaseController;
use Log;
use Illuminate\Http\Request;
use \App\Modules\HrmsEmployees\Models\ExitProcessModel;
use Illuminate\Support\Facades\DB;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Carbon\Carbon;
use Excel;
use Redirect;

Class ExitProcessController extends BaseController {

    public $roleAccess;
    protected $user_grid_fields;

    public function __construct() {
        $this->exitModel = new ExitProcessModel();
        $this->objApproval = new CommonApprovalFlowFunctionModel();
    }


    public function exitprocessWithUserId($emp_id) {
        try {
            $results = $this->exitModel->getEmployeeApprovalStatus($emp_id);

            $userid = DB::table('users')
                    ->where('emp_id', $emp_id)
                    ->pluck('user_id')->all();
            $approvalStatusDetails = $this->objApproval->getApprovalFlowDetails('HRMS Onboard Flow', $results[0]->status, Session::get('userId'));

            if (!isset($approvalStatusDetails['data'])) {
                $appDropdown = "";
            } else {
                $appDropdown = $approvalStatusDetails['data'];
            }

            return view('HrmsEmployees::exitEmployee')->with(array("appDropdown" => $appDropdown, "exit_table_data" => $results, "approvaldata" => $approvalStatusDetails));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());

        }
    }

    public function exitApprovalByAssigned(Request $request) {
        try{
            $data =array();
        $exitdata = $request->input();
        $checkstatus = $this->exitModel->checkingthestatushotcoded($exitdata['hidden_emp_id']);
        if ($checkstatus == 0) {
            return $data['data'] =  "Please check with the administrator";
        }else {
            $next = $exitdata['nextStatusId'];
            $explodevalue = explode(',', $next);
            $NextStatusID = $explodevalue[0];
            $isFinalStep = $explodevalue[1];

            if ($isFinalStep != 1) {
                $isFinalStep = $NextStatusID;
            }
           //generetae the emp code once got business aapproved
            if ($NextStatusID == "57152") {
                 $response = $this->exitModel->generateTheEmpCode($exitdata['hidden_emp_id']);
                 if($response != 1){
                    return $data['data'] = "Employee code is not generated";
                 }
            }else if($NextStatusID == "57153"){
                $response = $this->exitModel->changeTheUserStatusInEmployeeTable($exitdata['hidden_emp_id'],$exitdata);
                if($response != 1){
                    return $data['data'] = "OfficeEmail is not generated";
                    
                }
            }else if($NextStatusID == "57155"){
                if ($exitdata["currentStatusId"] == "57152") 
                {
                    if (isset($exitdata["employee_email_id"]) && $exitdata["employee_email_id"] !== "") 
                    {
                        $response = $this->exitModel->changeTheUserStatusInEmployeeTable($exitdata['hidden_emp_id'], $exitdata);
                        if ($response != 1)
                         {
                            return $data['data'] = "OfficeEmail is not generated";
                         }

                    } 
                    else 
                    {
                        return $data["data"] = "Please give the office email id";
                    }
                }
                if($exitdata["currentStatusId"] == "57156" || $exitdata["currentStatusId"] == "57159") {
                    $dolResponse = $this->exitModel->updateDOL($exitdata['hidden_emp_id']);
                    if($dolResponse != 1){
                        return $data["data"] = "Date of leaving was not update";
                    }  
                }
                $response = $this->exitModel->saveIntoUsertable($exitdata['hidden_emp_id'],$exitdata);
                if($response != 1){
                   return $data['data'] = "User is not actived";
                }
            }else if($NextStatusID == "57156"){
                $response = $this->exitModel->updateTheExitDate($exitdata['hidden_emp_id'],$exitdata);
                if($response != 1){
                    return $data['data'] = "Exit date is not updated";
                }
            }

            $update = $this->exitModel->updateMainTableWithStatus($exitdata, $isFinalStep);
            $flowType = "HRMS Onboard Flow";
            $userID = Session::get('userId');

            $title = "HRMS ".$exitdata['nextstatusname']." - ".$exitdata['first_name_approval']." ".$exitdata['last_name_approval'];
            
            // send email for hr every time
            if($update){
            $emailforeverytime = $this->exitModel->sendEmailNotificationInEveryStep($exitdata,$NextStatusID);
            //flowtype,tableid,currentstatusfrom flow,nextstatusid dropdown,comment,userid
            $approvalDataResp = $this->objApproval->storeWorkFlowHistory($flowType, $exitdata['hidden_emp_id'], $exitdata['currentStatusId'], $NextStatusID, $exitdata['comments'], $userID,$title);
            if($approvalDataResp){
            return $data['data'] = "Approval added successfully";
            }
            }else{
                return $data['data'] = "workflow history is not updated";
            }
            
            $returnDataArray = array(
                'data' => $data
            );
            return $returnDataArray;
        
        }
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    // get the getHistoryApprovalData
    public function getHistoryApprovalData($tblmainid) {
        try{
        $apprName = "HRMS Onboard Flow";
        //get the hrms history
        $expensesData1 = $this->exitModel->getHrmsApprovalHistorByIDFromDB($tblmainid, $apprName);

        $historyHTML = "";
        $loopCounter = 1;
        $bp = url('uploads/LegalEntities/profile_pics');
        $base_path = $bp . "/";
        $img = $base_path . "avatar5.png";
        foreach ($expensesData1 as $value) {
            $timeLineCSS = "";
            if ($loopCounter == count($expensesData1)) {
                $timeLineCSS = "timeline_last";
            } else {
                $timeLineCSS = "timeline";
            }

            $historyHTML .= '
                <div class="' . $timeLineCSS . '"  >
                    <div class="timeline-item timline_style"><div class="timeline-badge"><img class="timeline-badge-userpic" src="' . $img . '" style = "width:60px;"></div>
                        <div class="timeline-body">
                            <div class="row">
                            <div class="col-md-3 changedByName" id = "changedByName">' . $value->firstname . '
                            <p><span id="recordAddedByName"></span></p>
                            </div>
                            <div class="col-md-2" id = "hist_date">' . $value->created_at . '
                            </div>
                            <div class="col-md-2 push_right" id="Role">' . $value->action_name . '</div>
                            <div class="col-md-2" id="prev_status">' . $value->master_lookup_name . '</div>
                            <div class="col-md-2 push_right" id="comment">' . $value->awf_comment . '</div></div>                
                        </div>
                    </div>
                </div>
                ';
            $loopCounter++;
        }

        $returnDataArray = array(
            'historyHTML' => $historyHTML
        );
        return $returnDataArray;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    //check the offical email id
    public function checkOffcialEmailIdInTable(Request $request){
        try{
        $email = $request->input();
        $count = $this->exitModel->checkTheEmailExistOrNot($email['employee_email_id']);

        if($count > 0)
           {
             echo json_encode([ "valid" => false]); 
           }
           else
           {
               echo json_encode([ "valid" => true]);
           }
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
         }

    }



    public function saveSkillsEmployee(Request $request){
        try{
            $skilldata = $request->input();
            // check for duplicates for skills
            $checkskillcount = $this->exitModel->checkTheDuplicateSkillid($skilldata['emp_skill_id_master'],$skilldata['employee_hidden_id']);
            $checkname = $this->exitModel->checkSkillNameBySelectSkillname($skilldata['skill'],$skilldata['employee_hidden_id']);

            if($checkskillcount >= 0 && $checkname >=1 || $checkskillcount >= 1 && $checkname >=0){
                $data = "Danger";
            }else{
            // check the name is exist or not
            $skillname = $this->exitModel->checkTheSkillName($skilldata['skill']); 
            //echo count($skillname);exit;
            if(count($skillname >=1)){
                $save_skill_data = array(
                    'employee_id'                => $skilldata['employee_hidden_id'],
                    //'emp_skill_id'               => $skilldata['emp_skill_id_master']
                    'emp_skill_id'               =>  $skillname[0]->emp_skill_id
                );
                $skilllastid = $this->exitModel->saveSkillsIntoTable($save_skill_data);
                
            }else{
                $skilllastid = $this->exitModel->saveSkillsIntoMasterTable($skilldata);
                if($skilllastid > 0)
                 {
                    $saveskill = $this->exitModel->saveintoskillstable($skilldata,$skilllastid);
                }
               
            }
                $data = "Success";
            }
           
            $historyHTML = $this->getSkillsWithId($skilldata['employee_hidden_id']);
                 $returnDataArray = array(
                    'historyHTML'   => $historyHTML,
                    'data'          => $data
                    );
            return $returnDataArray;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        }

        public function getSkillsWithId($id){
           
            $skilldata = $this->exitModel->getTheSkillsData($id);
            $historyHTML = "";
            $loopCounter = 1;
            
                $historyHTML .= '
                <div class="row" style="border: 1px solid rgb(239, 239, 239);padding-top: 18px;">
                <div class="col-md-12" >
                <div class="row">';
                    foreach ($skilldata as $value) {
                $historyHTML .= '<div class="col-md-3 delete_'.$value->skill_id.'" style="margin-bottom:15px;">
                <div style="background:#ecf0f1; padding:8px;border-radius: 30px !important;">
                <span style="word-break: break-all;">'.$value->skill_name.'</span>  
                <a class="btn hrmsskills pull-right" style="display:none; margin-top:-6px;" onclick="deleteskill('.$value->skill_id.')">
                    <i class="fa fa-close"></i>
                </a>
                </div>             
                </div>';    
                }
                $historyHTML .='</div></div></div>';
                $loopCounter++;
            

            $returnDataArray = array(
                'historyHTML'   => $historyHTML,
            );

            return $returnDataArray;
        }
        // delete the skill
        public function deleteSkillForEmployee($id){
            try{
            $skilldata = $this->exitModel->deleteSkillEmployee($id);
            return $skilldata;
            }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
         }
        }

        //get skill name by selection
        public function getSkillNameBySelection(){
            try{
            $term = Input::get('term');

            $skill_name = $this->exitModel->getSkillnames($term);
            echo json_encode($skill_name);
            }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }

        }

}
