<?php
/*
FileName :CommonApprovalFlowFunctionModel.php
Author   :eButor
Description : Approval Flow  related functions are here.
CreatedDate :22/jul/2016
*/

//defining namespace
namespace App\Modules\ApprovalEngine\Models;

use App\Central\Repositories\ProductRepo;
use Illuminate\Database\Eloquent\Model;
use Carbon;
use Mail;
use DB;
use Notifications;
use Log;
use Utility;

class CommonApprovalFlowFunctionModel extends model
{

    public function __construct(){    
        $this->objPushNotification = new ProductRepo();
    }

    protected $table = 'appr_workflow_history';
  	protected $primaryKey = 'awf_history_id';

    /*
    Name : notifyUserForFirstApproval
    Desc : This function will notify the user which is related to the first lbl Approval, also will send them the first Status
    Params : @flowType, @userID
    */
    public function notifyUserForFirstApproval($flowType, $flowTypeForID, $userID){

        $params = array(
            'FlowName'      => $flowType,
            'UserID'        => $userID
        );

        // Checking for the Invalid Input
        if( $flowType=='' || $userID==''){
            $finalFlowData['status'] = "400";
            $finalFlowData['message'] = "Invalid input";

            // insert the call details into the table
            $tableID = DB::table('appr_workflow_call_details')->insert(
                ['appr_call_for' => $flowType, 'appr_name' => "notifyUserForFirstApproval", 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData), 'appr_call_input' => json_encode($params)]
            );

            return $finalFlowData;
        }else{

            // Get the Approval Flow ID from Master-lookup
            $flowID = DB::table("master_lookup")
                        ->select("value")
                        ->where("mas_cat_id","=","56")
                        ->where("master_lookup_name","=",$flowType)
                        ->first();

            // Get the UserID and information
            $userDetails = DB::table("users")
                        ->where("user_id","=", $userID)
                        ->first();

            // IF User or the FlowName not found in the Table
            if(!$flowID || !$userDetails){

                $finalFlowData['status'] = "400";
                $finalFlowData['message'] = "Invalid User or Flow Name, Bad request!";

                $tableID = DB::table('appr_workflow_call_details')->insert(
                    ['appr_call_for' => $flowType, 'appr_name' => "notifyUserForFirstApproval", 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData), 'appr_call_input' => json_encode($params)]
                );

                return $finalFlowData;
            }

            // Get the First Status of the Flow to send the Current Status ID
            $firstStatus = DB::table("appr_workflow_status_new AS awf")
                        ->join("appr_workflow_status_details AS det", "det.awf_id", "=", "awf.awf_id")
                        ->where("awf.awf_for_id","=", $flowID->value)
                        ->where("awf.legal_entity_id", "=", $userDetails->legal_entity_id)
                        ->orderBy("awf_det_id", "ASC")
                        ->limit(1)
                        ->first();                      

            // IF User or the FlowName not found in the Table
            if(!$firstStatus){

                $finalFlowData['status'] = "400";
                $finalFlowData['message'] = "Flow status not found with the user combination (".$userDetails->legal_entity_id.")!";

                $tableID = DB::table('appr_workflow_call_details')->insert(
                    ['appr_call_for' => $flowType, 'appr_name' => "notifyUserForFirstApproval", 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData), 'appr_call_input' => json_encode($params)]
                );

                return $finalFlowData;
            }
            $HubWiseMail = $firstStatus->hub_data;

            // Check for the Role is a Imidiate Reporter or not
            $roleDetails = DB::table("roles")
                        ->select("name")
                        ->where("role_id", "=", $firstStatus->applied_role_id)
                        ->first();

            // if Role is ImmidiateRepoter then send the mail to his reporting manager
            $toEmails = array();
            $userIDs = array();

            

            if($roleDetails->name=='ImmediateReporter'){

                $getUserForMail = DB::table("users")
                        ->where("user_id", "=", $userDetails->reporting_manager_id)
                        ->where("is_active", "=",1)
                        ->first();
                if(isset($getUserForMail->email_id)){
                    $toEmails[] = $getUserForMail->email_id;
                    $userIDs[] = $getUserForMail->user_id;
                }

            }elseif ($roleDetails->name=='Initiator') {

                 $getUserIdForMail = DB::table("users")
                        ->where("user_id", "=", $userID)
                        ->where("is_active", "=",1)
                        ->first();

                if(isset($getUserIdForMail->email_id)){
                    $toEmails[] = $getUserIdForMail->email_id;
                    $userIDs[] = $getUserIdForMail->user_id;
                }

            }else{

                if($HubWiseMail == 1){

                    $getHubWiseMail = "select * 
                                            FROM users AS usr
                                            INNER JOIN user_roles AS rls ON rls.`user_id`=usr.`user_id`
                                            INNER JOIN user_permssion AS per ON per.`user_id`=usr.`user_id`
                                            WHERE rls.`role_id`=".$firstStatus->applied_role_id."
                                            AND per.`permission_level_id`=6
                                            AND usr.is_active=1
                                            AND per.`object_id` IN (
                                            SELECT perm.object_id
                                            FROM user_permssion as perm
                                            WHERE perm.user_id=".$userID."
                                            AND perm.permission_level_id=6
                                            )";

                    $getUserForMail = DB::select(DB::raw($getHubWiseMail));

                }else{

                    $getUserForMail = DB::table("users AS usr")
                        ->join("user_roles AS rls", "rls.user_id", "=", "usr.user_id")
                        ->where("rls.role_id", "=", $firstStatus->applied_role_id)
                        ->where("usr.is_active", "=",1)
                        ->get()->all();

                }

                foreach($getUserForMail as $userData){
                    $toEmails[] = $userData->email_id;
                    $userIDs[] = $userData->user_id;
                }
            }
            
            if($flowID->value==56022 ||$flowID->value==56024){
                $get_role_id=DB::table('roles')
                      ->select('role_id')
                      ->where('name','Expenses Reporting Manager')
                      ->first();
                $checkRole=DB::table('user_roles')
                      ->select('role_id')
                      ->where('role_id', $get_role_id->role_id)
                      ->where('user_id', $userDetails->reporting_manager_id)
                      ->count();
                if($checkRole==0)     
                $update_user_roles=DB::table('user_roles')->insert(['role_id' => $get_role_id->role_id, 'user_id' => $userDetails->reporting_manager_id]);

            }

            $toEmails = array_unique($toEmails);
            $userIDs = array_unique($userIDs);

            // =======================================================
            // Save Information Into the History Table
            // ======================================================
            $dataToSave = array(
                    'awf_for_type'          => $flowType,
                    'awf_for_type_id'       => $flowID->value,
                    'awf_for_id'            => $flowTypeForID,
                    'awf_comment'           => "First Ticket for Initiator, Created by System",
                    'status_from_id'        => $firstStatus->awf_status_id,
                    'status_to_id'          => $firstStatus->awf_status_to_go_id,
                    'user_id'               => $userID,
                    'next_lbl_role'         => $firstStatus->applied_role_id,
                    'is_final'              => 0,
                    'condition_id'          => $firstStatus->awf_condition_id,
                    'ticket_created_by'     => $userID,
                    'created_by_manager'    => $userDetails->reporting_manager_id,
                    'created_by'            => $userID,
                    //'created_at'            => date('Y-m-d H:i:s')
            );
            //Insert the data into History table and then send the email notification
            $this->insert($dataToSave);
                $this->saveWorkflowModuleWise($dataToSave,$flowTypeForID,$flowID->value);

            // =========================================================
            // Get user Name || Prepare the Email Content 
            // =========================================================
            $userName = isset($userDetails->firstname) ? $userDetails->firstname : 'Unknown User';

            $emailContent = "A Ticket has been raised for " . $flowType . "(<a href='".$firstStatus->redirect_url."'>".$flowTypeForID."</a>)<br><br>";
            $emailContent .= "Ticket No :  TKT" . $flowTypeForID."<br>";
            $emailContent .= "Assigned By : " . $userName . "<br><br>";
            $emailContent .= "Please refer to <a href='".url("/")."/approvalworkflow/approvalticket'>Approval Ticket Page</a> for more details.<br><br> Thanks,<br><br>Team Ebutor.";

            $emailFlag = 1;
            $notificaionFlag = 1;
            $body = array('template'=>'emails.approvalWorkflowNotificationMail', 'attachment'=>'', 'name'=>'Hello!', 'comment'=>$emailContent);

            if($emailFlag == 1 ){
                if( count($toEmails)>0 ){
                    //Replaced email functionality with email queue
                    $subject='Your Approval Is Pending For - TKT' . $flowTypeForID;
                    Utility::sendEmail($toEmails, $subject, $body);

                    // Mail::send('emails.approvalWorkflowNotificationMail', ['comment' => $emailContent], function ($message) use ($toEmails, $flowTypeForID) {
                    //     $message->to($toEmails);
                    //     $message->subject('Your Approval Is Pending For - TKT' . $flowTypeForID);
                    // });

                }
            }

            // ============================================================
            // Send Mobile Notification
            // ============================================================
            if( $firstStatus->awf_mobile_notification==1 ){
                $this->sendMobileNotification($userIDs,$userID,$flowType);
            }

            $finalFlowData['status']            = "200";
            $finalFlowData['message']           = "Call Successful";
            $finalFlowData['currentStatusId']   = $firstStatus->awf_status_id;
            $finalFlowData['emails']            = $toEmails;
            $finalFlowData['mobileUserIDs']     = $userIDs;
            $finalFlowData['roleDetails']       = $roleDetails;

            // insert the call details into the table
            $tableID = DB::table('appr_workflow_call_details')->insert(
                ['appr_call_for' => $flowType, 'appr_name' => "notifyUserForFirstApproval", 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData), 'appr_call_input' => json_encode($params)]
            );

            return $finalFlowData;

        }
    }


    // Common function to send Notification to the User
    private function sendMobileNotification($userIds, $submittedByID, $flowType, $isFinalStep=0, $currentStatusID=0, $nextStatusID=0){

        // Push Notification Function
        $message = "";
        $tokenDetails = "";
        if($userIds){

            // Get User as per Role
            $userIds = implode($userIds, ",");
            $RegId = $this->getRegIds($userIds);
            $tokenDetails = json_decode((json_encode($RegId)), true);

            // Get value from user table
            $submitedByName = $this->getDataFromTables("users", "user_id", $submittedByID);

            if($submitedByName){
                $submitedByName = $submitedByName[0]->firstname . ' ' . $submitedByName[0]->lastname;
            }else{
//                $submitedByName = 'An employee ID : ' . (isset($submitedByName[0]->user_id));
                $submitedByName = 'An employee ID : ' . $submittedByID;
            }

            if( $currentStatusID!=0 && $nextStatusID!=0){

                // Get the Workflow Details and compose the Message
                $flowDetails = $this->getFlowDetailsFromDBchange($currentStatusID, $nextStatusID);
                $previousRole="Person";
                $conditionName="varified";
                if($flowDetails){
                    $previousRole=$flowDetails[0]->PreviousRole;
                    $conditionName=$flowDetails[0]->ConditionName;
                }

//               $message = $previousRole . " has ".$conditionName." : ".$flowType." requested by ".$submitedByName.", Waiting for your action!";
               $message = $previousRole . " has ".$conditionName." : ".$flowType;
               $flow_explode = explode(" ", $flowType);
               if($flow_explode[0] != "HRMS"){
                   $message .= " requested by ".$submitedByName;
               }
               $message .= ", Waiting for your action!";
            }else{

                $message = "Approval Notification : " . $submitedByName . ' requested for ' . $flowType . ', Waiting for your Action!';
            }

            $pushNotification = $this->objPushNotification->pushNotifications($message, $tokenDetails, $flowType);
        }

        if($isFinalStep==1){

            // Send Push Notification for the Initiator 
            // Get User as per Role
            $RegId = $this->getRegIds($submittedByID);
            $tokenDetails_init = json_decode((json_encode($RegId)), true);
            // Get Current Stauts ID
            $message_init="";

            $masterLookupName =  $this->getDataFromTables("master_lookup", "value", $nextStatusID);
            $message_init = 'Current status of your ' . $flowType . ' is : ' . $masterLookupName[0]->master_lookup_name;

            $pushNotification = $this->objPushNotification->pushNotifications($message_init, $tokenDetails_init, $flowType);

        }
    }


    //Get Registration Id  for Mobile notification
    public function getRegIds($userIds){

        $sqlUser = "select registration_id, platform_id FROM device_details WHERE user_id IN (".$userIds.")";
        $allData = DB::select(DB::raw($sqlUser));
        return $allData;
    }

    // Get data from a SindleTable
    public function getDataFromTables($tableName, $whereFld, $value){
        $allData = DB::table($tableName)
                    ->where($whereFld, "=", $value)
                    ->get()->all();

        return $allData;
    }

    // Function to get the Workflow details for the Message
    public function getFlowDetailsFromDBchange($currentStatusID, $NextStatusID){

        $sqlUser = "
        select 
        (SELECT NAME FROM roles WHERE role_id=det.applied_role_id) AS 'PreviousRole',
        (SELECT master_lookup_name FROM master_lookup WHERE VALUE=det.`awf_condition_id`) AS 'ConditionName'
        FROM appr_workflow_status_details AS det
        WHERE det.`awf_status_id`=".$currentStatusID."
        AND det.`awf_status_to_go_id`=".$NextStatusID."
        LIMIT 1
        ";

        $allData = DB::select(DB::raw($sqlUser));
        return $allData;

    }

    /*
    Name : getApprovalFlowDetails
    Desc : Get all the approval flow and retuns it as per the user roles
    Params : @flowType, @currentStatusID, @userID
    */
    public function getApprovalFlowDetails($flowType, $currentStatusID, $userID, $yourTableID=""){

        // start the flow here
        $finalFlowData = array();
        $responseBody = array();

		if( $flowType=='' || $currentStatusID=='' || $userID==''){
			$finalFlowData['status'] = "0";
            $finalFlowData['message'] = "Invalid input";

            // insert the call details into the table
            $tableID = DB::table('appr_workflow_call_details')->insert(
                ['appr_call_for' => $flowType, 'appr_current_status_id' => $currentStatusID, 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData)]
            );

			return $finalFlowData;
		}else{

            // get LegalEntiry ID

            $legalEntiryID = $this->getUserLegalEntity($userID);
            if($legalEntiryID==0 || $legalEntiryID==''){

                $finalFlowData['status'] = "0";
                $finalFlowData['message'] = "Invalid UserID or legalEntityID";

                // insert the call details into the table
                $tableID = DB::table('appr_workflow_call_details')->insert(
                    ['appr_call_for' => $flowType, 'appr_current_status_id' => $currentStatusID, 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData)]
                );

                return $finalFlowData; 
            }

			$flow_for_id = $this->getFlowForID($flowType);

            // Take the currentStat if it is Drafted
            if($currentStatusID=='drafted'){
                $sqlQuery = "select * 
                    FROM master_lookup AS ml 
                    WHERE ml.`parent_lookup_id`= (SELECT ml.`master_lookup_id` parent_lookup_id FROM master_lookup AS ml WHERE ml.`value`=".$flow_for_id." LIMIT 0,1)
                    AND ml.`master_lookup_name`='drafted'";

                $allData = DB::select(DB::raw($sqlQuery));
                if( isset($allData[0]->value) ){
                    $currentStatusID = $allData[0]->value;
                }else{
                    $currentStatusID =0;
                }
            }

            
			// check for the valid status
	        $checkStatusExist = DB::table('appr_workflow_status_new AS aws')
                      ->join("appr_workflow_status_details AS det", "det.awf_id", "=", "aws.awf_id")
                      ->where("aws.awf_for_id", "=", $flow_for_id)
                      ->where("aws.legal_entity_id", "=", $legalEntiryID)
                      ->where('det.awf_status_id', '=', $currentStatusID)
                      ->get()->all();

            $checkStatusToGoExist = DB::table('appr_workflow_status_new AS aws')
                      ->join("appr_workflow_status_details AS det", "det.awf_id", "=", "aws.awf_id")
                      ->where("aws.awf_for_id", "=", $flow_for_id)
                      ->where("aws.legal_entity_id", "=", $legalEntiryID)
                      ->where('det.awf_status_to_go_id', '=', $currentStatusID)
                      ->count();

            // if status not exit send response
            if($checkStatusExist==0 && $checkStatusToGoExist==0){
            	$finalFlowData['status'] = "0";
            	$finalFlowData['message'] = "Invalid Status Sent or wrong legalEntityID";
                // insert the call details into the table
                $tableID = DB::table('appr_workflow_call_details')->insert(
                    ['appr_call_for' => $flowType, 'appr_current_status_id' => $currentStatusID, 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData)]
                );
            	return $finalFlowData;
            }elseif($checkStatusExist==0 && $checkStatusToGoExist==1){
            	$finalFlowData['status'] = "1";
            	$finalFlowData['message'] = "No next lavel found";
            	$finalFlowData['data'] = array(
            			'0' => 'Done'
            		);
                // insert the call details into the table
                $tableID = DB::table('appr_workflow_call_details')->insert(
                    ['appr_call_for' => $flowType, 'appr_current_status_id' => $currentStatusID, 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData)]
                );
            	return $finalFlowData;
            }

	        // get all the flow as per current status
	        $nextFlowData = DB::table('appr_workflow_status_new AS aws')
                      ->select('aws.awf_id', 'det.awf_status_id', 'aws.awf_name', 'aws.awf_for_id', 'det.awf_condition_id', 'det.awf_status_to_go_id', 'det.applied_role_id', 'det.is_final')
                      ->join("appr_workflow_status_details AS det", "det.awf_id", "=", "aws.awf_id")
                      ->where("aws.awf_for_id", "=", $flow_for_id)
                      ->where("aws.legal_entity_id", "=", $legalEntiryID)
                      ->where('det.awf_status_id', '=', $currentStatusID)
                      ->get()->all();

            if(count($nextFlowData)>0){
            	$loopCounter = 0;
            	foreach($nextFlowData as $data){

                    $getdataDet = DB::table("master_lookup")
                                    ->where("value","=",$data->awf_condition_id)
                                    ->where("mas_cat_id","=",58)
                                    ->first();

                    $isFinal = $data->is_final;
                    if($getdataDet->master_lookup_name == "Rejected" && $data->is_final=="1"){
                        $isFinal = 0;
                    }

                    // Check if the rolese assigned to Immidiate repoter
                    $roleName = "";
                    $roleName = DB::table("roles")->select("name")->where("role_id","=",$data->applied_role_id)->first();
                    if($roleName){
                        $roleName=$roleName->name;
                    }

                    $countUserRole = 0;
                    if($roleName=='ImmediateReporter'){

                        // get Record Submitted by ID
                        $submittedByID = DB::table("appr_workflow_history AS hist")
                                        ->where("hist.awf_for_type", "=", $flowType)
                                        ->where("hist.awf_for_id", "=", $yourTableID)
                                        ->first();


                        if($submittedByID){

                            $countUserRole = DB::table("users")
                                    ->select("reporting_manager_id")
                                    ->where("user_id","=", $submittedByID->ticket_created_by)
                                    ->where("reporting_manager_id","=", $userID)
                                    ->count();
                        }

                    }elseif($roleName=='Initiator'){

                        // get Record Submitted by ID
                        $submittedByID = DB::table("appr_workflow_history AS hist")
                                        ->where("hist.awf_for_type", "=", $flowType)
                                        ->where("hist.awf_for_id", "=", $yourTableID)
                                        ->first();

                        if($submittedByID->ticket_created_by == $userID){
                            $countUserRole = 1;
                        }

                    }else{

                        // checks for the user access to the flow
                        $countUserRole = DB::table('user_roles AS rls')
                            ->where('rls.user_id', '=', $userID)
                            ->where('rls.role_id', '=', $data->applied_role_id)
                            ->count();
                    }

		          	if($countUserRole>0){

		          		$conditionName = DB::table('master_lookup')
		          						->where('value','=',$data->awf_condition_id)
		          						->first();
		          		$conditionName = $conditionName->master_lookup_name;
		          		$statusToGoName = DB::table('master_lookup')
		          						->where('value','=',$data->awf_status_to_go_id)
		          						->first();
		          		$statusToGoName = $statusToGoName->master_lookup_name;

		            	$responseBody[$loopCounter]['conditionId'] = $data->awf_condition_id;
		            	$responseBody[$loopCounter]['condition'] = $conditionName;
						$responseBody[$loopCounter]['nextStatusId'] = $data->awf_status_to_go_id;
						$responseBody[$loopCounter]['nextStatus'] = $statusToGoName;
						$responseBody[$loopCounter]['isFinalStep'] = $isFinal;
						$loopCounter++;
		          	}
	        	}
            }
            $checkRoleId=array();
           if($currentStatusID==57078){
                $checkRoleId=DB::table("appr_workflow_history")
                                        ->select('created_by_manager','status_from_id')
                                        ->where('awf_for_id', $yourTableID)
                                        ->where('awf_for_type_id',$flow_for_id)
                                        ->where('status_from_id',$currentStatusID)
                                        ->get()->all();
                $checkRoleId=json_decode(json_encode($checkRoleId),true);                      
            }
            $statusFromId=isset($checkRoleId[0]['status_from_id'])?$checkRoleId[0]['status_from_id']:0;
            $createdByManager=isset($checkRoleId[0]['created_by_manager'])?$checkRoleId[0]['created_by_manager']:0;

            if(($flow_for_id==56022 || $flow_for_id==56024) && $statusFromId==57078 && $createdByManager!=$userID){
                    $finalFlowData['status'] = 200;
                    $finalFlowData['message'] = "Your not permitted to approve since your not his reporting manager";
            }elseif(count($responseBody)==0){

                $addedMsg = $yourTableID=="" ? " or table ID could be blanck!" : "";

            	$finalFlowData['status'] = "0";
            	$finalFlowData['message'] = "User does not have role".$addedMsg;


            }else{

            	$currentStatusName = DB::table('master_lookup')
          						->where('value','=',$data->awf_status_id)
          						->first();
          		$currentStatusName = $currentStatusName->master_lookup_name;


            	$finalFlowData['status'] = "1";
            	$finalFlowData['message'] = "Flow found";
            	$finalFlowData['currentStatusName']=$currentStatusName;
            	$finalFlowData['currentStatusId']=$data->awf_status_id;
            	$finalFlowData['data'] = $responseBody;
            }     
            // insert the call details into the table
            $tableID = DB::table('appr_workflow_call_details')->insert(
                ['appr_call_for' => $flowType, 'appr_current_status_id' => $currentStatusID, 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData)]
            );
        	return $finalFlowData;
      	}
    }

    /*
    Name : storeWorkFlowStory
    Desc : Store work flow story in a flat table 
    Params : @flowType, @flowTypeForID, @currentStatusID, @nextStatusId, @userID
    */
    public function storeWorkFlowHistory($flowType, $flowTypeForID, $currentStatusID, $nextStatusId, $flowComment, $userID, $new_title=""){
    	$currentTime = Carbon\Carbon::now();
    	$currentTime = $currentTime->toDateTimeString();

        // get LegalEntiry ID
        $legalEntiryID = $this->getUserLegalEntity($userID);

        // get the ID for Approval Type
        $flow_for_id = $this->getFlowForID($flowType);
        
        // get next Role ID
        $nextLblRole = DB::table('appr_workflow_status_new AS awf')
                    ->select("awf.awf_id", "awf.awf_name", "det.applied_role_id", "det.is_final","det.hub_data")
                    ->join("appr_workflow_status_details AS det", "det.awf_id", "=", "awf.awf_id")
                    ->where("awf.awf_for_id", '=', $flow_for_id)
                    ->where("det.awf_status_id", '=', $nextStatusId)
                    ->where("awf.legal_entity_id", '=', $legalEntiryID)
                    ->get()->all();


        // Arrange data for Hubwise notification
        $nextLblRoleIDForHubData = array();
        foreach ($nextLblRole as $value) {
            if($value->hub_data==1){
                $nextLblRoleIDForHubData[] = $value->applied_role_id;
            }
        }   
        $nextLblRoleIDForHubData = array_unique($nextLblRoleIDForHubData);      

        // Taking all the next lbl role IDs
        $nextLblRoleID = array();
        foreach ($nextLblRole as $value) {
            $nextLblRoleID[] = $value->applied_role_id;
        }   
        $nextLblRoleID = array_unique($nextLblRoleID);        
        
        //$nextLblRoleID = count($nextLblRole)>0 ? $nextLblRole->applied_role_id : 0;
        // get flow is final flag
        $currentStatusData = DB::table('appr_workflow_status_new AS awf')
                    ->select("awf.awf_id", "awf.awf_name", "det.applied_role_id", "det.is_final", "det.awf_condition_id")
                    ->join("appr_workflow_status_details AS det", "det.awf_id", "=", "awf.awf_id")
                    ->where("awf.awf_for_id", '=', $flow_for_id)
                    ->where("det.awf_status_id", '=', $currentStatusID)
                    ->where("det.awf_status_to_go_id", "=", $nextStatusId)
                    ->where("awf.legal_entity_id", '=', $legalEntiryID)
                    ->first();

        $isFinalFlag = count($currentStatusData)>0 ? $currentStatusData->is_final : 0;
        $conditionID = count($currentStatusData)>0 ? $currentStatusData->awf_condition_id : 0;

        // Update previous data with isfinal 1
        $getPreviousHistoryID = DB::table('appr_workflow_history AS hist')
                                ->where("hist.awf_for_id", "=", $flowTypeForID)
                                ->where("hist.awf_for_type_id", "=", $flow_for_id)
                                ->orderBy("hist.awf_history_id", "desc")
                                ->first();
        $previousHistoryID = count($getPreviousHistoryID)>0 ? $getPreviousHistoryID->awf_history_id : 0;
        // update Table with 1
        DB::table('appr_workflow_history AS hist')
            ->where("hist.awf_history_id", "=", $previousHistoryID)
            ->update( ['is_final' => '1'] );


        //==================================================================================//
        // Save the data in history table
        //==================================================================================//
        // Get the User ID and Reporting Manager from History Table,  assuming this two data are same for a ticket
        $ticketCrID = count($getPreviousHistoryID)>0 ? $getPreviousHistoryID->ticket_created_by : 0;
        $ticketCrMgrID = count($getPreviousHistoryID)>0 ? $getPreviousHistoryID->created_by_manager : 0;

        if($flow_for_id==56022 || $flow_for_id==56024){
            $get_role_id=DB::table('roles')
                      ->select('role_id')
                      ->where('name','Expenses Reporting Manager')
                      ->first();
            if($currentStatusID!=57078 && $ticketCrMgrID>0){
                $checkTicket=DB::table('appr_workflow_history AS awf')
                      ->select('awf_history_id')
                      ->join("expenses_main AS em", "em.exp_id", "=", "awf.awf_for_id")
                      ->where('em.exp_appr_status',57078)
                      ->where('awf.created_by_manager',$ticketCrMgrID)
                      ->where('awf.next_lbl_role',$get_role_id->role_id)
                      ->where('awf.awf_for_type_id',$flow_for_id)
                      ->count();
                if($checkTicket==0){
                   $unset_manager_role=DB::Table('user_roles')
                        ->where('user_id',$ticketCrMgrID)
                        ->where('role_id',$get_role_id->role_id)
                        ->delete();
                }
            }
        }

        $dataToSave = array(
            'awf_for_type'                  => $flowType,
            'awf_for_type_id'               => $flow_for_id,
            'awf_for_id'                    => $flowTypeForID,
            'awf_comment'		            => $flowComment,
            'status_from_id'	            => $currentStatusID,
            'status_to_id'		            => $nextStatusId,
            'user_id'			            => $userID,
            'next_lbl_role'                 => implode($nextLblRoleID, ","),
            'is_final'                      => $isFinalFlag,
            'condition_id'                  => $conditionID,
            'ticket_created_by'             => $ticketCrID,
            'created_by_manager'            => $ticketCrMgrID,
            'created_by'		            => $userID
            //'created_at'		            => $currentTime
        );
        if($new_title !=""){
            $dataToSave['title'] = $new_title;
        }
    	//Insert the data into History table and then send the email notification
    	if ($this->insert($dataToSave)){
            if ($flow_for_id==56017) {
                $explode_dataToSave=explode(",",$dataToSave['status_to_id']);
                $dataToSave['status_to_id']=$explode_dataToSave[0];
            }
            $this->saveWorkflowModuleWise($dataToSave,$flowTypeForID,$flow_for_id);

            // get approval details for notificaion or email
            $getApprovalDetails = DB::table("appr_workflow_status_new")
                                ->where("awf_for_id","=",$flow_for_id)
                                ->where("legal_entity_id", '=', $legalEntiryID)
                                ->first();
            $emailFlag = 0;
            $notificaionFlag = 0;
            $mobileNotifyFlag = 0;
            $redirectURL = url("/")."/approvalworkflow/approvalticket";

            if($getApprovalDetails){
                $emailFlag = $getApprovalDetails->awf_email;
            }
            if($getApprovalDetails){
                $notificaionFlag = $getApprovalDetails->awf_notification;
            }
            if($getApprovalDetails){
                $redirectURL = $getApprovalDetails->redirect_url;
                $redirectURL = str_replace("##", $flowTypeForID, $redirectURL);
            }
            if($getApprovalDetails){
                 $mobileNotifyFlag = $getApprovalDetails->awf_mobile_notification;
            }

           /* // Get the First record of the history
            $getFirstRecord = DB::table("appr_workflow_history")
                            ->where("awf_for_type_id", "=",  $flow_for_id)
                            ->where("awf_for_id", "=", $flowTypeForID)
                            ->where("status_to_id", "=", $nextStatusId)
                            ->orderBy("awf_history_id", "DESC")
                            ->limit(1)
                            ->first();*/

        $getFirstRecord = DB::selectFromWriteConnection(DB::raw("select * from appr_workflow_history where awf_for_type_id = '".$flow_for_id."' and awf_for_id = '".$flowTypeForID."' and status_to_id = '".$nextStatusId."' order by awf_history_id desc limit 1"));
        
            $toEmails = array();
            $userIDs = array();

            // Check for the Role is a Imidiate Reporter or not
            $roleDetails = DB::table("roles")
                    ->select("name")
                    ->where("role_id", "=", $getFirstRecord[0]->next_lbl_role)
                    ->first();

            if($roleDetails){
                if($roleDetails->name=='ImmediateReporter'){

                    $getUserForMail = DB::table("users")
                            ->where("user_id", "=", $ticketCrMgrID)
                            ->where("is_active", "=",1)
                            ->first();
                    if(isset($getUserForMail->email_id)){
                        $toEmails[] = $getUserForMail->email_id;
                        $userIDs[] = $getUserForMail->user_id;
                    }

                }elseif ($roleDetails->name=='Initiator') {

                     $getUserIdForMail = DB::table("users")
                            ->where("user_id", "=", $ticketCrID)
                            ->where("is_active", "=",1)
                            ->first();
                    if(isset($getUserIdForMail->email_id)){
                        $toEmails[] = $getUserIdForMail->email_id;
                        $userIDs[] = $getUserIdForMail->user_id;
                    }

                }else{
                    if(count($nextLblRoleIDForHubData) > 0 && $ticketCrID > 0){
                        $getHubWiseMail = "select * 
                                            FROM users AS usr
                                            INNER JOIN user_roles AS rls ON rls.`user_id`=usr.`user_id`
                                            INNER JOIN user_permssion AS per ON per.`user_id`=usr.`user_id`
                                            WHERE rls.`role_id`in (".implode(",", $nextLblRoleIDForHubData).")
                                            AND per.`permission_level_id`=6
                                            AND usr.is_active=1
                                            AND per.`object_id` IN (
                                                SELECT perm.object_id
                                                FROM user_permssion as perm
                                                WHERE perm.user_id=".$ticketCrID."
                                                AND perm.permission_level_id=6
                                            )";

                        $userInformation = DB::select(DB::raw($getHubWiseMail));
                    }else{
                         $userInformation = DB::table('appr_workflow_status_new AS awf')
                                            ->select("awf.awf_id", "awf.awf_name", "rls.user_roles_id", "urs.user_id", "urs.firstname", "urs.lastname", "urs.email_id", "det.applied_role_id")
                                            ->join("appr_workflow_status_details AS det", "det.awf_id", "=", "awf.awf_id")
                                            ->join("user_roles as rls","rls.role_id", "=", "det.applied_role_id")
                                            ->join("users as urs", "urs.user_id", "=", "rls.user_id")
                                            ->where("urs.is_active", '=',1)
                                            ->where("awf.awf_for_id", '=', $flow_for_id)
                                            ->where("det.awf_status_id", '=', $nextStatusId)
                                            ->where("awf.legal_entity_id", '=', $legalEntiryID)
                                            ->distinct()
                                            ->get()->all();
                    }
                    
                    
                    foreach($userInformation as $userData){

                        $toEmails[] = $userData->email_id;
                        $userIDs[] = $userData->user_id;
                    }
                }
            }

            $toEmails = array_unique($toEmails);
            $userIDs = array_unique($userIDs);

            // =========================================================
            // Get user Name || Prepare the Email Content 
            // =========================================================
            $userName = DB::table('users')
                        ->where("user_id", "=", $userID)
                        ->first();
            $userName = isset($userName->firstname) ? $userName->firstname : 'Unknown User';

            if($new_title!=""){
                $flowType = $new_title;
            }

            $emailContent = "A Ticket has been raised for " . $flowType . "(<a href='".$redirectURL."'>".$flowTypeForID."</a>)<br><br>";
            $emailContent .= "Ticket No :  TKT" . $flowTypeForID."<br>";
            $emailContent .= "Assigned By : " . $userName . "<br><br>";
            $emailContent .= "Please refer to <a href='".url("/")."/approvalworkflow/approvalticket'>Approval Ticket Page</a> for more details.<br><br> Thanks,<br><br>Team Ebutor.";
            //==========================================================

            $body = array('template'=>'emails.approvalWorkflowNotificationMail', 'attachment'=>'', 'name'=>'Hello!', 'comment'=>$emailContent);

            if($emailFlag == 1 ){
    			if( count($toEmails)>0 ){
                    //Replaced email functionality with email queue
                    $subject='Your Approval Is Pending For - TKT' . $flowTypeForID;
                    Utility::sendEmail($toEmails, $subject, $body);
    	            // Mail::send('emails.approvalWorkflowNotificationMail', ['emailContent' => $emailContent], function ($message) use ($toEmails, $flowTypeForID) {
    	            //     $message->to($toEmails);
    	            //     $message->subject('Your Approval Is Pending For - TKT' . $flowTypeForID);
    	            // });

    			}
            }

            if($notificaionFlag == 1){
                if( count($userIDs)>0 ){
                    Notifications::addNotification(['note_code' => 'DEFAULT','note_message' => 'You have an Approval for '.$flowType.' pending', 'note_users' => $userIDs]);
                }
            }

            //send mobile Notification
            if( $mobileNotifyFlag == 1){
                $this->sendMobileNotification($userIDs, $ticketCrID, $flowType, $isFinalFlag, $currentStatusID, $nextStatusId);
            }

            $finalFlowData['status'] = "1";
            $finalFlowData['message'] = "Flow found";
            $finalFlowData['emails'] = $toEmails;
            $finalFlowData['userIDs'] = $userIDs;

            // insert the call details into the table
            $tableID = DB::table('appr_workflow_call_details')->insert(
                ['appr_call_for' => $flowType, 'appr_name' => "storeWorkFlowHistory", 'appr_call_user_id'=> $userID, 'appr_call_made_at' => date('Y-m-d h:i:s'), 'appr_call_response' =>  json_encode($finalFlowData), 'appr_call_input' => json_encode($dataToSave)]
            );

    		return true;
    	}else{
    		return false;
    	}
    }


    private function getFlowForID($flowForName){

        $flowForID = DB::table("master_lookup")
                    ->where("mas_cat_id","=","56")
                    ->where("master_lookup_name","=",$flowForName)
                    ->first();
        if(count($flowForID)>0){
            return $flowForID->value;
        }else{
            return "0";
        }
    }

    // This function returns legalentity ID for the user
    private function getUserLegalEntity($userID){
        $userLGL = DB::table("users")
                    ->where("user_id", "=", $userID)
                    ->first();

        if($userLGL){
            return $userLGL->legal_entity_id;
        }else{
            return 0;
        }
    }
    public function saveWorkflowModuleWise($dataToSave,$flowTypeForID,$flow_for_id){
        $query=DB::select(DB::raw("select getMastLookupValue(".$dataToSave['status_from_id'].") as status_from_name, getMastLookupValue(".$dataToSave['status_to_id'].") as status_to_name, getMastLookupValue(".$dataToSave['status_to_id'].") as master_lookup_name, GetUserName(".$dataToSave['user_id'].",2) as user_name,getMastLookupValue(".$dataToSave['condition_id'].") as condition_name, GetUserName(".$dataToSave['created_by'].",2) as created_by_name,getUserProfilePicture(".$dataToSave['user_id'].") as profile_picture, getUserFirstName(".$dataToSave['user_id'].") as firstname,getUserLastName(".$dataToSave['user_id'].") as lastname,getUserRoleName(".$dataToSave['user_id'].") as name"));
                $query=json_decode(json_encode($query),1);
                if(count($query)>0){
                    foreach ($query[0] as $key => $value) {
                        $dataToSave[$key] = $value;
                    }
                }
        $poData = json_encode($dataToSave);
        $dataExist = DB::table('appr_comments')->select('comments')->where('comments_id',$flowTypeForID)->where('awf_for_type_id',$flow_for_id)->get()->all();
        if(count($dataExist)>0){
            $lastData=json_decode(json_encode($dataExist[0]->comments),true);
            $finalArray=json_decode($lastData,1);
            $poData=json_decode($poData,1);
            $poData['created_at']=date('Y-m-d H:i:s');
            $finalArray[count($finalArray)]=$poData;
            $finalArray=json_encode($finalArray);
            $updateData = array('comments_id'=> $flowTypeForID,'comments'=>$finalArray,'awf_for_type_id'=>$flow_for_id);
            DB::table('appr_comments')->where('comments_id',$flowTypeForID)->where('awf_for_type_id',$flow_for_id)->update($updateData);
            return 1;
        }else{
            $poData=json_decode($poData,true);
            $tempArray[0]=$poData;
            $tempArray[0]['created_at']=date('Y-m-d H:i:s');
            $temp = json_encode($tempArray);
            $po = array('comments_id'=> $flowTypeForID,'comments'=>$temp,'awf_for_type_id'=>$flow_for_id);
            DB::table('appr_comments')->insert($po);
            return 1;
        }
    }

    public function getApprovalHistory($module,$id){
        $history=DB::table('appr_workflow_history as hs')
                        ->join('users as us','us.user_id','=','hs.user_id')
                        ->join('user_roles as ur','ur.user_id','=','hs.user_id')
                        ->join('roles as rl','rl.role_id','=','ur.role_id')
                        ->join('master_lookup as ml','ml.value','=','hs.status_to_id')
                        ->select('us.profile_picture','us.firstname','us.lastname',DB::raw('group_concat(rl.name) as name'),'hs.created_at','hs.status_to_id','hs.status_from_id','hs.awf_comment','ml.master_lookup_name')
                        ->where('hs.awf_for_id',$id)
                        ->where('hs.awf_for_type',$module)
                        ->groupBy('hs.created_at')
                        ->orderBy('hs.created_at','desc')
                        ->get()->all();
        return $history;
    }

    public function getApprovalHistoryFromCommentsTable($id,$module){
        $getMaterLookupValue=DB::table('master_lookup')
                       ->select('value')
                       ->where('master_lookup_name',$module)
                       ->where('mas_cat_id',56)
                       ->get()->all();
        $result=isset($getMaterLookupValue[0]->value)?$getMaterLookupValue[0]->value:'';
        $totalhistory=DB::table('appr_comments')
                ->select('comments','created_at')
                ->where('comments_id',$id)
                ->where('awf_for_type_id',$result)
                ->get()->all();
        return $totalhistory;

    }  
}