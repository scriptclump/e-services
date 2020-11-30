<?php
/*
FileName : approvalAPIController
Author   : eButor
Description :
CreatedDate : 06/Jan/2017
*/

//defining namespace
namespace App\Modules\ExpensesTracker\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\ExpensesTracker\Models\expensesAPIModel;
use App\Modules\ExpensesTracker\Models\expensesTrackModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Central\Repositories\ProductRepo;
use Illuminate\Http\Request;
use Input;
use Log;
use Session;


class approvalAPIController extends BaseController{

	private $objAPIModel = '';
	private $finalResponse = '';
	private $objApproval = '';
	private $objPushNotification = '';
	private $ObjExpenses = '';

	public function __construct(){
		$this->objAPIModel = new expensesAPIModel();
		$this->objApproval = new CommonApprovalFlowFunctionModel();
		$this->objPushNotification 		= new ProductRepo();

		$this->ObjExpenses = new expensesTrackModel();
	}

	public function checkAuthentication($auth_token){
    	if( $auth_token=='E446F5E53AD8835EAA4FA63511E22' ){
    		return true;
    	}else{
    		return false;
    	}
    }

    public function getApprovalData(Request $request){

    	$hostIP = $request->ip();
    	$paramData = $request->input();
    	// check for the header authentication
		$auth_token = $request->header('auth');

		// if authentication does not match then send a return
		if( !$this->checkAuthentication($auth_token) ){
			$finalResponse = array(
				'message'	=> 'Invalid authentication! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getApprovalData", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Getting the input
		$flowType 			= trim($request->input('FlowType'));
		$currentStatusID 	= trim($request->input('CurrentStatus'));
		$userID 			= trim($request->input('UserID'));
		$yourTableID 			= trim($request->input('yourTableID'));

		if( $flowType=='' || $currentStatusID=='' || $userID=='' ){
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getApprovalData", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		$flowType = $flowType . "ExpensesAppr";

		if( $currentStatusID=='0' ){
			$currentStatusID = "drafted";
		}

		// Save the information into the expenses api call details table
		$finalResponse = $this->objApproval->getApprovalFlowDetails($flowType, $currentStatusID, $userID, $yourTableID);
		
		$this->objAPIModel->getAPIcallsdetails("getApprovalData", $hostIP, $paramData, $finalResponse);

		return $finalResponse;
    }

    public function saveApprovalData(Request $request){

    	$hostIP = $request->ip();
    	$paramData = $request->input();

    	// check for the header authentication
		$auth_token = $request->header('auth');

		// if authentication does not match then send a return
		if( !$this->checkAuthentication($auth_token) ){
			$finalResponse = array(
				'message'	=> 'Invalid authentication! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("saveApprovalData", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Getting the input
		$flowType 			= trim($request->input('FlowType'));
		$ExpensesMainID 	= trim($request->input('ExpensesMainID'));
		$currentStatusID 	= trim($request->input('CurrentStatusID'));
		$NextStatusID 		= trim($request->input('NextStatusID'));
		$Comment 			= trim($request->input('Comment'));
		$ApprovalAmount 	= trim($request->input('ApprovalAmount'));
		$userID 			= trim($request->input('UserID'));
		$tallyLedgerName 	= trim($request->input('TallyLedgerName'));
		$isFinalStep 		= trim($request->input('isFinalStep'));


		$flowTypeForMSG = $flowType;
		
		if( $flowType=='' || $ExpensesMainID=='' || $currentStatusID=='' || $NextStatusID=='' || $userID=='' || $ApprovalAmount=='' ){
			
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("saveApprovalData", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		$flowType = $flowType . "ExpensesAppr";

		// Update the mainTable with next Approval ID
		if($isFinalStep!=1){
			$isFinalStep=$NextStatusID;
		}

		$this->objAPIModel->updateExpensesMainTableforApproval($ExpensesMainID, $ApprovalAmount, $isFinalStep,$tallyLedgerName);

		// Return and call the Approval History Data
		$approvalDataResp = $this->objApproval->storeWorkFlowHistory($flowType, $ExpensesMainID, $currentStatusID, $NextStatusID, $Comment, $userID);

		// Save data into Vouchers table if it is final work flow
		if($request->input('isFinalStep') == 1){
			$this->saveVoucherDetails($ExpensesMainID,$flowType);
		}

		// Get main table Data
		$mainTableData = $this->objAPIModel->getDataFromTable("expenses_main", "exp_id", $ExpensesMainID);

		$userIds = $this->objAPIModel->getUserIds($ExpensesMainID);
		// Push Notification Function
		$message = "";
		$tokenDetails = "";

		if(!empty($userIds)){

			// get value from master lookup table
			$requestTypeName = $this->objAPIModel->getDataFromTable("master_lookup", "value", $mainTableData[0]->exp_req_type);
			if($requestTypeName){
				$requestTypeName = $requestTypeName[0]->master_lookup_name;
			}else{
				$requestTypeName = ' ';
			}

			if($isFinalStep!=1){

				// Get the Workflow Details and compose the Message
				$flowDetails = $this->objAPIModel->getFlowDetailsFromDB($currentStatusID, $NextStatusID);
				$previousRole="Person";
				$conditionName="varified";
				if($flowDetails){
					$previousRole=$flowDetails[0]->PreviousRole;
					$conditionName=$flowDetails[0]->ConditionName;
				}

				// Get User as per Role
				$RegId = $this->objAPIModel->getRegId(implode($userIds, ","));
				$tokenDetails = json_decode((json_encode($RegId)), true);

				// Get value from user table
				$submitedByName = $this->objAPIModel->getDataFromTable("users", "user_id", $mainTableData[0]->submited_by_id);
				if($submitedByName){
					$submitedByName = $submitedByName[0]->firstname . ' '. $submitedByName[0]->lastname;
				}else{
					$submitedByName = 'An employee ID : ' . (isset($submitedByName[0]->user_id));
				}

				$message = $previousRole . " has ".$conditionName." Rs ".$ApprovalAmount." of ".$submitedByName.", Waiting for your action!";

				// Send notification to the next lbl users only it the Approval process is not Final
			
				$pushNotification = $this->objPushNotification->pushNotifications($message, $tokenDetails);
			}

			// Send Push Notification for the Initiator 
			// Get User as per Role
			$RegId = $this->objAPIModel->getRegId($mainTableData[0]->submited_by_id);
			$tokenDetails_init = json_decode((json_encode($RegId)), true);
			// Get Current Stauts ID
			$message_init="";
			if($isFinalStep!=1){
				$masterLookupName = $this->objAPIModel->getDataFromTable("master_lookup", "value", $NextStatusID);
				$message_init = 'Current status of your ' . $requestTypeName . ' of : Rs. ' .  $ApprovalAmount . ' : ' . $masterLookupName[0]->master_lookup_name;
				$pushNotification = $this->objPushNotification->pushNotifications($message_init, $tokenDetails_init);
			}
		}

		// Sending Final Notificaton
		if($isFinalStep==1){
			// Send Push Notification for the Initiator 
			// Get User as per Role
			$RegId = $this->objAPIModel->getRegId($mainTableData[0]->submited_by_id);
			$tokenDetails = json_decode((json_encode($RegId)), true);

			// Get Current Stauts ID
			$message = 'Your ' . $flowTypeForMSG . ' of : Rs. ' .  $ApprovalAmount . ' is Approved!';
			$pushNotification = $this->objPushNotification->pushNotifications($message, $tokenDetails);
		}

		$logArray = array(
				'noficationMsg' => $message,
				'tokenDetails'	=> $tokenDetails,
				"userDetails"	=> !empty($userIds) ? json_encode($userIds) : "User Not found"
			);

		if($approvalDataResp==1){
			$finalResponse = array(
				'message'	=> 'Call Successful',
				'status'	=> 'success',
				'code'		=> '200'
			);
		}else{
			$finalResponse = array(
				'message'	=> 'Something went wrong, Please check!',
				'status'	=> 'failed',
				'code'		=> '400'
			);
		}
		$finalResponse['data'] = $logArray;

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("saveApprovalData", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }


    // Prepar tally ledger data and call the function
    public function saveVoucherDetails($ExpensesMainID,$flowType){

    	if( $flowType=='AdvanceExpensesAppr' ){

	    	// Get Main Table Data
	    	$mainTableData = $this->objAPIModel->getTallyLedgerNameForAdvanceDr($ExpensesMainID);
	    	// Get the Dr Name
	    	$exptally['expenses_dr'] = $mainTableData[0]->TallyLedgerForAdvanceDr;

	    	// Get the Cr Name
	    	$mainTableData = $this->objAPIModel->getExpensesDetailsWithId($ExpensesMainID);
			$exptally['expenses_cr'] = $mainTableData[0]->tally_ledger_name;

	    	if(isset($mainTableData[0])){
	    		$storeintovouchers = $this->objAPIModel->saveIntoVouchersTableAdvance($mainTableData,$exptally['expenses_dr'],$exptally['expenses_cr']);
	    	}

	    }else{

	    	// Get Main Table Data
	    	$mainTableData = $this->objAPIModel->getTallyLedgerNameForAdvanceDr($ExpensesMainID);
	    	// Get the Cr Name
	    	$exptally['expenses_cr'] = $mainTableData[0]->TallyLedgerForAdvanceDr;

	    	// Get the Dr Details
	    	$expDetailsData = $this->objAPIModel->downloadExpenseDetailsData($ExpensesMainID);
	    	$mainTableData = $this->objAPIModel->getExpensesDetailsWithId($ExpensesMainID);

	    	if(isset($mainTableData[0])){
	    		$storeintovouchers = $this->objAPIModel->saveIntoVouchersTableForReimbursment($mainTableData, $expDetailsData, $exptally['expenses_cr']);
	    	}
	    }
    }

    public function getExpensesAsPerApprovalRole(Request $request){

    	$hostIP = $request->ip();
    	$paramData = $request->input();

    	// check for the header authentication
		$auth_token = $request->header('auth');

		// if authentication does not match then send a return
		if( !$this->checkAuthentication($auth_token) ){
			$finalResponse = array(
				'message'	=> 'Invalid authentication! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getExpensesAsPerApprovalRole", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Take the Inputs
		$userID 	= trim($request->input('userid'));
		$expType 	= trim($request->input('exptype'));
		if( $userID==''){
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getExpensesAsPerApprovalRole", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		$expensesData = $this->objAPIModel->getExpensesAsPerApprovalRoleFromDB($userID, $expType);

		$finalResponse = array(
				'message'	=> 'Call Successful',
				'status'	=> 'success',
				'code'		=> '200',
				'data'		=> $expensesData
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getExpensesAsPerApprovalRole", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

    public function getApprovalHistoryByID(Request $request){

    	$hostIP = $request->ip();
    	$paramData = $request->input();

    	// check for the header authentication
		$auth_token = $request->header('auth');

		// if authentication does not match then send a return
		if( !$this->checkAuthentication($auth_token) ){
			$finalResponse = array(
				'message'	=> 'Invalid authentication! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getApprovalHistoryByID", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Take the Inputs
		$mainExpensesID 	= trim($request->input('mainexpensesid'));
		$expensesType		= trim($request->input('expensestype'));
		if( $mainExpensesID=='' || $expensesType==''){
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getApprovalHistoryByID", $hostIP, $paramData, $finalResponse);
			return $finalResponse;

		}

		$expensesType = $expensesType . "ExpensesAppr";

		//$expensesDat = $this->objAPIModel->getApprovalHistorByID($mainExpensesID);

		$expensesData1 = $this->objAPIModel->getApprovalHistorByIDFromDB($mainExpensesID, $expensesType);

		if( count($expensesData1)>1 ){
			$expensesData1[0]->master_lookup_name = $expensesData1[0]->master_lookup_name . ' / Ticket Created';
		}else{
			if(isset($expensesData1[0])){
				$expensesData1[0]->master_lookup_name = $expensesData1[0]->master_lookup_name . ' / Ticket Created';
			}
		}
        
        $expensesData = $expensesData1;
        
		$finalResponse = array(
				'message'	=> 'Call Successful',
				'status'	=> 'success',
				'code'		=> '200',
				'data'		=> json_decode(json_encode($expensesData), true)
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getApprovalHistoryByID", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

    // This function with return all the workflow activity for an user
    public function getApprovalActivityDetails(Request $request){

    	$hostIP = $request->ip();
    	$paramData = $request->input();

    	// check for the header authentication
		$auth_token = $request->header('auth');

		// if authentication does not match then send a return
		if( !$this->checkAuthentication($auth_token) ){
			$finalResponse = array(
				'message'	=> 'Invalid authentication! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getApprovalActivityDetails", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Take the Inputs
		$mainExpensesID 	= trim($request->input('mainExpensesID'));
		$userID				= trim($request->input('userID'));

		if( $userID==''){
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getApprovalActivityDetails", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Get Activity Data from DB
		$activityData = $this->objAPIModel->getApprovalActivityDetailsFromDB($mainExpensesID, $userID);

		$message = "No Data Found on Your Combination!";
		if( count($activityData) > 0 ){
			$message = "Total activity found : " . count($activityData);
		}

		$finalResponse = array(
				'message'	=> $message,
				'status'	=> 'success',
				'code'		=> '200',
				'data'		=> json_decode(json_encode($activityData), true)
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getApprovalActivityDetails", $hostIP, $paramData, $finalResponse);

		return $finalResponse;
    }

}