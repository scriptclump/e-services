<?php
/*
FileName : expensesTrackGetAPIController
Author   : eButor
Description :
CreatedDate : 26/Dec/2016
*/

//defining namespace
namespace App\Modules\ExpensesTracker\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\ExpensesTracker\Models\expensesAPIModel;

use Illuminate\Http\Request;
use Input;
use Log;
use Session;


class expensesTrackGetAPIController extends BaseController{

	private $objAPIModel = '';
	private $finalResponse = '';

	public function __construct(){
		$this->objAPIModel = new expensesAPIModel();
	}

	public function checkAuthentication($auth_token){
    	if( $auth_token=='E446F5E53AD8835EAA4FA63511E22' ){
    		return true;
    	}else{
    		return false;
    	}
    }

    public function getAllExpenses(Request $request){

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
			$this->objAPIModel->getAPIcallsdetails("getAllExpenses", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}



		// Take the Inputs
		$userID 			= $request->input('user');
		$expType 			= $request->input('expensestype');
		$withAdjustFlag		= $request->input('withbalanceamt');

		// Matching the Input Query
		if( $userID=='' || $expType=='' ){
			$finalResponse = array(
				'message'	=> 'Invalid Argument Send! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getAllExpenses", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Get all the data as per the Param
		$finalData = $this->objAPIModel->getAllExpensesFromDB($userID, $expType, $withAdjustFlag);

		// Get Total Wallet Total Amount
		$totalWalletAmt = $this->objAPIModel->getTotalWalletAmount($userID);

		$message = "Record Found";
		if( count($finalData)==0 )
		{
			$message = "No Record Found!";
		}

		$finalResponse = array(
				'message'		=> $message,
				'status'		=> 'success',
				'code'			=> '200',
				'walletTotal'	=> $totalWalletAmt[0]->WalletTotal ? $totalWalletAmt[0]->WalletTotal : 0,
				'data'			=> $finalData
			);


		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getAllExpenses", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

    public function getExpensesByID(Request $request){

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
			$this->objAPIModel->getAPIcallsdetails("getExpensesByID", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		$expID = $request->Input('exp_id');

		// if authentication does not match then send a return
		if( trim($expID)=='' ){
			$finalResponse = array(
				'message'	=> 'Argument mismatched! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getExpensesByID", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}
		
		$finalData = $this->objAPIModel->getAllExpensesByIDFromDB($expID);

		$finalResponse = array(
				'message'	=> 'Call Successful',
				'status'	=> 'success',
				'code'		=> '200',
				'data'		=> $finalData
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getExpensesByID", $hostIP, $paramData, $finalResponse);

		return $finalResponse;
    }

    public function getMasterLookupValueForExp(Request $request){

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
			$this->objAPIModel->getAPIcallsdetails("getMasterLookupValueForExp", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		$finalData = $this->objAPIModel->getMasterLookupValueForExpFromDB();

		$finalResponse = array(
				'message'	=> 'Call Successful',
				'status'	=> 'success',
				'code'		=> '200',
				'data'		=> $finalData
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getMasterLookupValueForExp", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

    public function getExpensesLineItems(Request $request){

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
			$this->objAPIModel->getAPIcallsdetails("getExpensesLineItems", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Take the Inputs
		$userID = $request->input('UserID');
		$recordType = $request->input('RecordType');
		// Matching the Input Query
		if( $userID=='' || $recordType=='' ){
			$finalResponse = array(
				'message'	=> 'Invalid Argument Send! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getExpensesLineItems", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Make a DB call to get the line item as pre the param sent
		$expensesLineData = $this->objAPIModel->getExpensesLineItemFromDB($userID, $recordType);

		$message = "Call Successful with " . count($expensesLineData) . " Data!";

		$finalResponse = array(
				'message'	=> $message,
				'status'	=> 'success',
				'code'		=> '200',
				'data'		=> $expensesLineData
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getExpensesLineItems", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

    public function getTallyLedgers(Request $request){

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
			$this->objAPIModel->getAPIcallsdetails("getTallyLedgers", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}


		// get Tally related data from table
		$finalData = $this->objAPIModel->getTallyLedgerDetails();


		$finalResponse = array(
				'message'		=> 'records avail',
				'status'		=> 'success',
				'code'			=> '200',
				'data'			=> $finalData
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getTallyLedgers", $hostIP, $paramData, $finalResponse);
		return $finalResponse;

	}
    public function getAllUsersExpenses(Request $request){

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
			$this->objAPIModel->getAPIcallsdetails("getAllUsersExpenses", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}



		// Take the Inputs
		$userID 			= $paramData['UserID'];
		
		//$expType 			= $request->input('expensestype');

		// Matching the Input Query
		if( $userID==''){
			$finalResponse = array(
				'message'	=> 'Invalid Argument Send! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->getAPIcallsdetails("getAllUsersExpenses", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		// Get all the data as per the Param
		$finalData = $this->objAPIModel->getAllUsersExpensesFromDB($userID);

		$finalResponse = array(
				'status'		=> 'success',
				'code'			=> '200',
				'data'			=> $finalData
			);


		// Save the information into the expenses api call details table
		$this->objAPIModel->getAPIcallsdetails("getAllUsersExpenses", $hostIP, $paramData, $finalResponse);
		return $finalResponse;

    }
}