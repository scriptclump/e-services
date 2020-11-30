<?php
/*
FileName : expensesTrackPostAPIController
Author   : eButor
Description :
CreatedDate : 26/Dec/2016
*/

//defining namespace
namespace App\Modules\ExpensesTracker\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\ExpensesTracker\Models\expensesAPIModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Central\Repositories\ProductRepo;

use Illuminate\Http\Request;
use Input;
use Log;
use Session;


class expensesTrackPostAPIController extends BaseController{

	private $objAPIModel = '';
	private $finalResponse = '';
	private $objPushNotification = '';
	private $objApproval = '';

	public function __construct(){
		$this->objAPIModel 				= new expensesAPIModel();
		$this->objPushNotification 		= new ProductRepo(); 
		$this->objApproval 				= new CommonApprovalFlowFunctionModel();
	}

	public function checkAuthentication($auth_token){
    	if( $auth_token=='E446F5E53AD8835EAA4FA63511E22' ){
    		return true;
    	}else{
    		return false;
    	}
    }

    public function addExpencesDetails(Request $request){

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
			$this->objAPIModel->getAPIcallsdetails("addExpencesDetails", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Validate All the Inputs
		$RequestFoID 			=	trim($request->input('RequestFoID'));
		$RequestForTypeID 		=	trim($request->input('RequestForTypeID'));
		$Subject				=	trim($request->input('Subject'));
		$ActualAmount			=	trim($request->input('Amount'));
		$SubmitDate				=	trim($request->input('SubmitDate'));
		$SubmitedByID			=	trim($request->input('SubmitedByID'));
		$ReffIDs				=	trim($request->input('ReffIDs'));
		$tallyledgername		=	"101000 : Cash";

		if( $RequestFoID=='' || $RequestForTypeID=='' || $ActualAmount=='' || $SubmitDate=='' || $SubmitedByID=='' ){
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("addExpencesDetails", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		$expDetails = $request->input('DetailsData');

		// Build the data array to save
		$mainTableData = array(
			'exp_req_type'				=> $RequestFoID,
			'exp_req_type_for_id'		=> $RequestForTypeID,
			'exp_subject'				=> $Subject,
			'exp_actual_amount'			=> $ActualAmount,
			'submit_date'				=> $SubmitDate,
			'submited_by_id'			=> $SubmitedByID,
			'exp_reff_id'				=> $ReffIDs,
			'tally_ledger_name'			=> $tallyledgername
		);

		// Add the Data into Main Table
		$mainTableLastID = $this->objAPIModel->addExpensesDataIntoDB($mainTableData);

		// Add the Data into Details Table
		if( count($expDetails)>0 ){
			$detailsTable = $this->objAPIModel->addExpensesDataIntoDetailsTable($mainTableLastID, $expDetails, $SubmitedByID);
		}

		// Save the data into History Table
		$flowType = "AdvanceExpensesAppr";
		if($RequestFoID=='122001'){
            $flowType = "AdvanceExpensesAppr";

        }elseif($RequestFoID=='122002'){
            $flowType = "ReimbursementExpensesAppr";
            
        }elseif($RequestFoID=='122003'){
            $flowType = "VendorPaymentExpensesAppr";
        }

		$this->objApproval->notifyUserForFirstApproval($flowType, $mainTableLastID, $SubmitedByID);

		// Push Notification Function
		$userIds = $this->objAPIModel->getUserIds($mainTableLastID);

		$message = "";
		$tokenDetails = "";
		
		if(!empty($userIds)){
			// Get User as per Role
			$RegId = $this->objAPIModel->getRegId(implode($userIds, ","));
			$tokenDetails = json_decode((json_encode($RegId)), true);

			// Get value from user table
			$submitedByName = $this->objAPIModel->getDataFromTable("users", "user_id", $SubmitedByID);
			if($submitedByName){
				$submitedByName = $submitedByName[0]->firstname . ' '. $submitedByName[0]->lastname;
			}else{
				$submitedByName = 'An employee ID : ' . $submitedByName[0]->user_id;
			}
			
			// get value from master lookup table
			$requestTypeNmae = $this->objAPIModel->getDataFromTable("master_lookup", "value", $RequestFoID);
			if($requestTypeNmae){
				$requestTypeNmae = $requestTypeNmae[0]->master_lookup_name;
			}else{
				$requestTypeNmae = ' ';
			}

			$message = $submitedByName . ' requested for ' . $requestTypeNmae . ' of : Rs. ' . $ActualAmount . ', Waiting for your action!';
			$pushNotification = $this->objPushNotification->pushNotifications($message, $tokenDetails);
		}

		$logArray = array(
				'noficationMsg' => $message,
				'tokenDetails'	=> $tokenDetails,
				"userDetails"	=> !empty($userIds) ? json_encode($userIds) : "User Not found"
			);


		$finalResponse = array(
				'message'	=> 'Details Mapped Successfully!',
				'status'	=> 'success',
				'code'		=> '200'
			);

		$finalResponse['data'] = $logArray;


		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("addExpencesDetails", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

    public function saveExpensesLineItems( Request $request ){

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
			$this->objAPIModel->getAPIcallsdetails("saveExpensesLineItems", $hostIP, $paramData, $finalResponse);
			return $finalResponse;

		}



		// Validate All the Inputs
		$ExpID 					=	trim($request->input('ExpID'));
		$ExpDetActualAmount		=	trim($request->input('ExpDetActualAmount')); 
		$ExpDetType 			=	trim($request->input('ExpDetType'));
		$ExpDetDate				=	trim($request->input('ExpDetDate')); 
		$Description 			=	trim($request->input('Description'));
		$ExpDetProofKey			=	trim($request->input('ExpDetProofKey')); 
		$ExpDetRecordType 		=	trim($request->input('ExpDetRecordType'));
		$UserID					=	trim($request->input('UserID'));




		//This column is for the reference purpose of remittance 
		if(empty($request->input('ReferenceID')))
		{
			$Remit_refer_id=0;
		}else
		{
			$Remit_refer_id=trim($request->input('ReferenceID'));

		}
		

		if( $ExpDetActualAmount=='' || $ExpDetType=='' || $ExpDetDate=='' || $ExpDetRecordType=='' || $UserID=='' ){
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  field not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("saveExpensesLineItems", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Making Details table data array  
		$detailsTableData = array(
			"exp_id"						=> $ExpID,
			"exp_det_actual_amount"			=> $ExpDetActualAmount,
			"exp_det_approved_amount"		=> $ExpDetActualAmount,
			"exp_det_date"					=> $ExpDetDate,
			"exp_type"						=> $ExpDetType,
			"exp_det_description"			=> $Description,
			"exp_det_proof"					=> $ExpDetProofKey,
			"exp_det_type"					=> $ExpDetRecordType,
			"created_by"					=> $UserID,
			"created_at"					=> date("Y-m-d H:i:s"),
			"reff_id"						=> $Remit_refer_id
		);

		// Save the record in the Expenses Details Table
		$detailsTableLastID = $this->objAPIModel->addExpensesDataIntoDetailsTableSingleRecord($detailsTableData);

		$finalResponse = array(
				'message'	=> 'Single Record Added Successful',
				'status'	=> 'success',
				'code'		=> '200'
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("saveExpensesLineItems", $hostIP, $paramData, $finalResponse);
		return $finalResponse;	
    }

    public function mapDetailsWithExpenses( Request $request ){

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
			$this->objAPIModel->getAPIcallsdetails("mapDetailsWithExpenses", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}



		// Validate All the Inputs
		$MainTableID 			=	trim($request->input('MainTableID'));
		$RequestFoID 			=	trim($request->input('RequestFoID'));
		$RequestForTypeID 		=	trim($request->input('RequestForTypeID'));
		$Subject				=	trim($request->input('Subject'));
		$ActualAmount			=	trim($request->input('Amount'));
		$SubmitDate				=	trim($request->input('SubmitDate'));
		$SubmitedByID			=	trim($request->input('SubmitedByID'));
		$ReffIDs				=	trim($request->input('ReffIDs'));
		$MapIDs					=	trim($request->input('MapIDs'));		
		$tallyledgername		=	"101000 : Cash";

		$MainTableIDForNotificaion= $MainTableID;

		if( $MainTableID == '0' ||  $MainTableID=='' ){
			if( $MainTableID=='' || $RequestFoID=='' || $RequestForTypeID=='' || $ActualAmount=='' || $SubmitDate=='' || $SubmitedByID=='' || $MapIDs=='' ){
				$finalResponse = array(
					'message'	=> 'Invalid input send, Mandatory fields are not matched!',
					'status'	=> 'failed',
					'code'		=> '400'
				);
				// Save the information into the expenses api call details table
				$this->objAPIModel->getAPIcallsdetails("mapDetailsWithExpenses", $hostIP, $paramData, $finalResponse);
				return $finalResponse;
			}
		}elseif( $MapIDs=='' ){

			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("mapDetailsWithExpenses", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Build the data array to save
		$mainTableData = array(
			'exp_req_type'				=> $RequestFoID,
			'exp_req_type_for_id'		=> $RequestForTypeID,
			'exp_subject'				=> $Subject,
			'exp_actual_amount'			=> $ActualAmount,
			'submit_date'				=> $SubmitDate,
			'submited_by_id'			=> $SubmitedByID,
			'exp_reff_id'				=> $ReffIDs,			
			'tally_ledger_name'			=> $tallyledgername
		);

		// Add the Data into Main Table
		$mainTableLastID = $this->objAPIModel->addExpensesDataIntoDB($mainTableData, $MainTableID);

		// Add the Data into Main Table
		$mainTableLastIDRsponse  = $this->objAPIModel->updateExpensesLineItemWithMainTableID($mainTableLastID, $MapIDs);

		// If main table ID is 0 then User is mapping and creating so we need send first notification
		$message = "";
		$tokenDetails = "";
		if( $MainTableIDForNotificaion==0 ){

			$flowType = "AdvanceExpensesAppr";
			if($RequestFoID=='122001'){
	            $flowType = "AdvanceExpensesAppr";

	        }elseif($RequestFoID=='122002'){
	            $flowType = "ReimbursementExpensesAppr";
	            
	        }elseif($RequestFoID=='122003'){
	            $flowType = "VendorPaymentExpensesAppr";
	        }

			$this->objApproval->notifyUserForFirstApproval($flowType, $mainTableLastID, $SubmitedByID);

			// Get main table Data
			$mainTableData = $this->objAPIModel->getDataFromTable("expenses_main", "exp_id", $mainTableLastID);
			// Get user as per the user Role
			$userIds = $this->objAPIModel->getUserIds($mainTableLastID);

			// Push Notification Function
			if( !empty($userIds) ){

				// Get User as per Role
				$RegId = $this->objAPIModel->getRegId( implode($userIds, ",") );
				$tokenDetails = json_decode((json_encode($RegId)), true);

				// Get value from user table
				$submitedByName = $this->objAPIModel->getDataFromTable("users", "user_id", $mainTableData[0]->submited_by_id);
				if($submitedByName){
					$submitedByName = $submitedByName[0]->firstname . ' ' . $submitedByName[0]->lastname;
				}else{
					$submitedByName = 'An employee ID : ' . (isset($submitedByName[0]->user_id));
				}
				
				// get value from master lookup table
				$requestTypeName = $this->objAPIModel->getDataFromTable("master_lookup", "value", $mainTableData[0]->exp_req_type);
				if($requestTypeName){
					$requestTypeName = $requestTypeName[0]->master_lookup_name;
				}else{
					$requestTypeName = ' ';
				}

				$message = $submitedByName . ' requested for ' . $requestTypeName . ' of : Rs. ' .  $mainTableData[0]->exp_actual_amount . ', Waiting for your Action!';
				
				$pushNotification = $this->objPushNotification->pushNotifications($message, $tokenDetails);
			}
		}


		$logArray = array(
				'noficationMsg' => $message,
				'tokenDetails'	=> $tokenDetails
			);

		$finalResponse = array(
				'message'	=> 'Details Mapped Successfully!',
				'status'	=> 'success',
				'code'		=> '200'
			);

		$finalResponse['data'] = $logArray;

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("mapDetailsWithExpenses", $hostIP, $paramData, $finalResponse);
		
		return $finalResponse;
    }

    // API to delete an UnClaimed API
    public function deleteUnclaimedExp( Request $request ){


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
			$this->objAPIModel->getAPIcallsdetails("deleteUnclaimedExp", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Validate All the Inputs
		$unclaimedID 			=	trim($request->input('unclaimedID'));

		if( $unclaimedID=='' ){
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("deleteUnclaimedExp", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Check the expenses maped or not
		$checkUnclaimed = $this->objAPIModel->checkUnclaimedInDB($unclaimedID);

		if($checkUnclaimed>0){
			$finalResponse = array(
				'message'	=> "Single or Multiple Expenses already mapped or not exist, can't be deleted!",
				'status'	=> "success",
				'code'		=> '200'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("deleteUnclaimedExp", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Finally Delete thte Expenses Line Tracker
		$deleteResp = $this->objAPIModel->deleteUnclaimedExpFromDB($unclaimedID);

		$finalResponse = array(
			'message'	=> "Unclaimed Expenses Deleted Successfully!",
			'status'	=> "success",
			'code'		=> '200'
		);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("deleteUnclaimedExp", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }


    // API to add Direct Advance Expenses
    // This does not required Approval
    // This will direct save the Voucher into Tally
    public function addDirectAdvanceExpenses(Request $request){

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
			$this->objAPIModel->getAPIcallsdetails("addExpencesDetails", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Validate All the Inputs
		$RequestFoID 			=	trim($request->input('RequestFoID'));
		$RequestForTypeID 		=	trim($request->input('RequestForTypeID'));
		$Subject				=	trim($request->input('Subject'));
		$ActualAmount			=	trim($request->input('Amount'));
		$SubmitDate				=	trim($request->input('SubmitDate'));
		$SubmitedByID			=	trim($request->input('SubmitedByID'));
		$ReffIDs				=	trim($request->input('ReffIDs'));
		$tallyledgername		=	"101000 : Cash";

		if( $RequestFoID=='' || $RequestForTypeID=='' || $ActualAmount=='' || $SubmitDate=='' || $SubmitedByID=='' ){
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("addExpencesDetails", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Build the data array to save
		$mainTableData = array(
			'exp_req_type'				=> $RequestFoID,
			'exp_req_type_for_id'		=> $RequestForTypeID,
			'exp_subject'				=> $Subject,
			'exp_actual_amount'			=> $ActualAmount,
			'submit_date'				=> $SubmitDate,
			'submited_by_id'			=> $SubmitedByID,
			'exp_reff_id'				=> $ReffIDs,
			'tally_ledger_name'			=> $tallyledgername
		);

		// Add the Data into Main Table
		$mainTableLastID = $this->objAPIModel->addExpensesDataIntoDB($mainTableData, 0, 1);

		// Get Main Table Data
    	$mainTableDataNew = $this->objAPIModel->getTallyLedgerNameForAdvanceDr($mainTableLastID);
    	// Get the Dr Name
    	$exptally['expenses_dr'] = $mainTableDataNew[0]->TallyLedgerForAdvanceDr;

    	// Get the Cr Name
    	$mainTableDataNew = $this->objAPIModel->getExpensesDetailsWithId($mainTableLastID);
		$exptally['expenses_cr'] = $mainTableDataNew[0]->tally_ledger_name;

    	if(isset($mainTableDataNew[0])){
    		$storeintovouchers = $this->objAPIModel->saveIntoVouchersTableAdvance($mainTableDataNew,$exptally['expenses_dr'],$exptally['expenses_cr']);
    	}

    	$finalResponse = array(
			'message'	=> 'Direct Expenses added Successfully!',
			'status'	=> 'success',
			'code'		=> '200',
			'data'		=> $mainTableData
		);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("addExpencesDetails", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

}