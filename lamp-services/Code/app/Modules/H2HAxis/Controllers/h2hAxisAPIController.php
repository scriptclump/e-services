<?php
/*
FileName : h2hAxisAPIController
Author   : eButor
Description :
CreatedDate : 12/Mar/2017
*/

//defining namespace
namespace App\Modules\H2HAxis\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\H2HAxis\Models\h2hAxisAPIModel;
use App\Central\Repositories\ProductRepo;

use Illuminate\Http\Request;
use Input;
use Log;
use Session;


class h2hAxisAPIController extends BaseController{

	private $objAPIModel = '';

	public function __construct(){
		$this->objAPIModel 				= new h2hAxisAPIModel();
	}

	public function checkAuthentication($auth_token){
    	if( $auth_token=='E446F5E53AD8835EAA4FA63511E22' ){
    		return true;
    	}else{
    		return false;
    	}
    }

    public function sendPaymentRequestToAxis(Request $request, $data=array()){
    	$hostIP = $request->ip();
    	$paramData = $request->input();
    	// check for the header authentication
		$auth_token = $request->header('auth');
		
		if(count($data) == 0){
        
        // if authentication does not match then send a return
			if( !$this->checkAuthentication($auth_token) ){
				$finalResponse = array(
					'message'	=> 'Invalid authentication! Call aborted',
					'status'	=> 'failed',
					'code'		=> '400'
				);

			// Save the information into the expenses api call details table
			$this->objAPIModel->storeAPICallDetails("sendPaymentRequestToAxis", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
			}
		}	
	  
    	if(count($data) ==0){
    		$data = $request->input();
    	}

    	if( trim($data['TxnAmount'])=='' || trim($data['BeneName'])=='' || trim($data['BeneAccNum'])=='' || trim($data['BeneIFSCCode'])=='' || trim($data['BeneBankName'])=='' ||  trim($data['TransmissionDate'])=='' ){
			
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);
            
			// Save the information into the expenses api call details table
			$this->objAPIModel->storeAPICallDetails("sendPaymentRequestToAxis", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}
		$mainTableData = array(
    		'pay_date'				=> trim($data['TransmissionDate']),
			'pay_amount'			=> trim($data['TxnAmount']),
			'reff_id'				=> trim($data['TxnReffIds']),
			'pay_utr_code'			=> trim($data['PayUTRCode']),
			'pay_to_id'				=> trim($data['TxnToID']),
			'pay_for'				=> isset($data['TxnForID']) ? trim($data['TxnForID']) : 0,
			'pay_type'				=> ($data['PayType']!='')?trim($data['PayType']):'H2H',
			'pay_status'			=> 'Initiate',
			'txn_reff_code'			=> trim($data['TxnReffCode']),
			'ledger_group'			=> trim($data['LedgerGroup']),
			'ledger_account'		=> trim($data['LedgerAccount']),
			'cost_center'			=> trim($data['CostCenter']),
			'cost_center_group'		=> trim($data['CostCenterGroup']),
			'txn_tolegal_id'		=> trim($data['TxnToLegalID']),
			'pay_for_module'		=> trim($data['PayForModule']),
			'auto_initiate'		=> ($data['AutoInit']==1) ? 1:0,
			'created_at'			=> date('Y-m-d H:i:s'),
			'created_by'			=> trim($data['CreatedBy']),
			'payment_from'			=>isset($data['payment_from']) ? trim($data['payment_from']) : 0,
			'deposite_type'			=>isset($data['deposite_type']) ? trim($data['deposite_type'])  : 0,
    	);
		//Log::info($data["state_code"]."state_code before hitting");
         if(isset($data["state_code"])){

         	$state_code = $data["state_code"];
         //	Log::info($state_code."++++++++++");
         }else{
         	$state_code = "TS";
         }
		// Add the Data into Main Table
		$mainTableLastID = $this->objAPIModel->addPaymentInformationIntoDB($mainTableData,$state_code);
		$axisResponse = $mainTableLastID['payment_ref'];
		$_mainTableLastID = $mainTableLastID['p_pay_id'];

		if( trim($request->input('AutoInit'))=='1' ){
			$axisResponse = $this->objAPIModel->callAxisH2HAPI($mainTableLastID, $paramData);
		}

		$finalResponse = array(
				'message'	=> 'Details Mapped Successfully!',
				'status'	=> 'Success',
				'code'		=> '200',
				'response'	=>	$axisResponse,
				'p_pay_id'  => 	$_mainTableLastID
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->storeAPICallDetails("sendPaymentRequestToAxis", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

    public function h2hCallBackResponseAxis( Request $request ){

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
			$this->objAPIModel->storeAPICallDetails("h2hCallBackResponseAxis", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		$uniqueRefNumber 	= $request->input('uniqueRefNumber');
		$responseBody 		= $request->input('responseBody');

		if( $uniqueRefNumber=='' || $responseBody=='' ){
			
			$finalResponse = array(
				'message'	=> 'Invalid input send, Mandatory  fields are not matched!',
				'status'	=> 'failed',
				'code'		=> '400'
			);

			// Save the information into the expenses api call details table
			$this->objAPIModel->storeAPICallDetails("h2hCallBackResponseAxis", $hostIP, $paramData, $finalResponse);
			return $finalResponse;
		}

		$finalResponse = array(
				'message'	=> 'Response received Successfully!',
				'status'	=> 'Success',
				'code'		=> '200',
				'response'	=>	$responseBody
			);

		// Save the information into the expenses api call details table
		$this->objAPIModel->storeAPICallDetails("h2hCallBackResponseAxis", $hostIP, $paramData, $finalResponse);
		return $finalResponse;
    }

}