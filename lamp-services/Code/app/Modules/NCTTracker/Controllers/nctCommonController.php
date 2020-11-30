<?php
namespace App\Modules\NCTTracker\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\NCTTracker\Models\nctTrackerModel;
use App\Modules\NCTTracker\Models\nctTrackerHistoryModel;
use App\Central\Repositories\RoleRepo;

use Log;
use Illuminate\Http\Request;
use Response;
use Input;
use DB;

class nctCommonController extends BaseController {

	public function __construct(){
		$this->objNctTracker = new nctTrackerModel();
		$this->objNctTrackerHistory = new nctTrackerHistoryModel();
	}

	public function NctBulkUpdate($nctdata){

		$deposited_by = $nctdata['deposited_by'];
		$status 			= $nctdata['status'];
		$deposited_to 		= $nctdata['bankname'];
		$depositeddate 		= $nctdata['depositedate'];
		$Created_by  		= $nctdata['userId'];

		$returnJson = array();
		if( !is_array($nctdata) || empty($nctdata) ){
			$returnJson['code'] = 400;
			$returnJson['status'] = 0;
			$returnJson['message'] = "Data send is empty or invalid";
		}else{

			// ** if all the validation works correct then only we need to inset
			// Read the Array with foreach and make a new array for bulk insert
			// Check for the mandet fiels 
			// if anything is blank then return back the error
			// 
			$Response = array();
			$loopCounter = 0;

			$errorFlag = 0;
			$errorMsg = '';

			$successloop = 0;
			$errorloop = 0;

			$successMsg = "Validation for All Fileds are Successfully done";
			$updateFlag =0;

			$errorArrayBody = array();
			$successArrayBody = array();

			foreach ($nctdata['data'] as $value) {

				$errorFlag = 0;
				$deposited_by 		= $deposited_by;
				$depositeddate 		= $depositeddate;
				$refNumber 			= $value['refNumber'];
				$customerName 		= $value['customerName'];
				$chequeAmount 		= $value['chequeAmount'];
				$holderBank 		= $value['holderBank'];
				$holderName 		= $value['holderName'];
				$branch 			= $value['branch'];
				$status 			= $status;
				$proof_image 		= $value['proof'];
				$deposited_to 		= $deposited_to;
				$collectionId		= $value['historyId'];
        		$issuedate 			= date("Y-m-d", strtotime($value['issuedate']) );
				if(trim($deposited_by) =='' ){
					$errorFlag=1;
					$errorMsg .= "Deposited By is Invalid || ";
				}
				if(trim($depositeddate) ==''){
					$errorFlag=1;
					$errorMsg .= "Deposited Date is Invalid || ";
				}
				if(trim($refNumber) ==''){
					$errorFlag=1;
					$errorMsg .= "Referenece number is Invalid || ";

				}
				if(trim($customerName) ==''){
					$errorFlag=1;
					$errorMsg .= "Customer Name is Invalid || ";

				}
				if(trim($chequeAmount) ==''){
					$errorFlag=1;
					$errorMsg .= "Cheque Amount is Invalid || ";
				}
				if(trim($issuedate) ==''){
					$errorFlag=1;
					$errorMsg .= "Issue Date is Invalid || ";
				}
				if(trim($holderBank) ==''){
					$errorFlag=1;
					$errorMsg .= "Holder Bank  is Invalid || ";
				}
				if(trim($holderName) ==''){
					$errorFlag=1;
					$errorMsg .= "Holder Name  is Invalid || ";
				}
				if(trim($branch) ==''){
					$errorFlag=1;
					$errorMsg .= "Branch  is Invalid || ";
				}
				if(trim($status) ==''){
					$errorFlag=1;
					$errorMsg .= "Status is Invalid || ";
				}

				// get the deposited by name
				$deposited_by_name = $this->objNctTrackerHistory->getDepositedByName($deposited_by);
				//get the previous status of the cheque
				$nctInformation = $this->objNctTrackerHistory->getThePreviousStatus($collectionId,$chequeAmount,$refNumber);

				// Making data for NCT Information
				$nctInformation = explode(';', $nctInformation);

				$nctId = $nctInformation[0];
				$previousStatus = isset($nctInformation[1]) ? $nctInformation[1] : 0;

				//this condition check for status
				if( $status == $previousStatus ){
					$errorFlag = 1;	
					$errorMsg .= " || Previous Status And Current Status is Same Please Check";
				}else if( $status == 11905 && ($previousStatus!=0 || $previousStatus==11906 || $previousStatus==11903 ) ){
					$errorFlag = 1;
					$errorMsg .= " || Please Check The Current Status";
				}else if($status == 11902 && $previousStatus != 11905 ){
					$errorFlag = 1;
					$errorMsg .= " || Current status combination is wrong";
				}


				if($errorFlag == 0 ){
					// Making array for main Table
					$nct_tracking[$successloop] = array(

						"nct_history_id"	=> $collectionId,
						"nct_ref_no" 		=> $refNumber,
						"nct_bank" 			=> $holderBank,
						"nct_branch" 		=> $branch,
						"nct_holdername" 	=> $customerName,
						"nct_issue_date" 	=> $issuedate,
						"transcation_type" 	=> '1',
						"nct_collected_by" 	=> $deposited_by_name,
						"nct_amount" 		=> $chequeAmount,
						"nct_comment" 		=> "Bulk Update Successfully.",
						"nct_deposited_to" 	=> $deposited_to,
						"nct_status" 		=> $status,
						"proof_image" 		=> $proof_image,
						"previous_status"	=> $previousStatus,
						"created_by"		=> $Created_by

	                );

					$successArrayBody[$successloop]['dataRowNumber'] 			= $loopCounter;
					$successArrayBody[$successloop]['collectionHistoryID'] 		= $collectionId;
					$successArrayBody[$successloop]['reffNumber'] 				= $refNumber;
					$successArrayBody[$successloop]['respMSG'] 					= $successMsg;

	                $successloop++;

				}else{

					$errorArrayBody[$errorloop]['dataRowNumber'] 				= $loopCounter;
					$errorArrayBody[$errorloop]['collectionHistoryID'] 			= $collectionId;
					$errorArrayBody[$errorloop]['reffNumber'] 					= $refNumber;
					$errorArrayBody[$errorloop]['respMSG'] 						= $errorMsg;

					$errorloop++;	
				}
				$errorMsg = "";
	            $loopCounter++;
			}

			$responseMsg['code'] 			= $errorloop==0 ? '200' : '401';
			$responseMsg['message'] 		= $errorloop==0 ? 'Success' : 'Error';
			$responseMsg['successBody']		= $successArrayBody;
			$responseMsg['errBody']			= $errorArrayBody;
			if( $responseMsg['code']=='200'){

				$successloop = 1;
				foreach ($nct_tracking as $value) {
					if( $value['previous_status'] == '0' ){

						// Insert into main table
						$nctMainData = $value;
						unset($nctMainData['previous_status']);
						$save = DB::table("nct_transcation_tracking")->insert($nctMainData);
						$lastid = DB::getPdo()->lastInsertId($save);

					}else{

						// update into sub table
						$updatedata = array(
							'reference_no' 		=> $value['nct_ref_no'],
							'bank_name' 		=> $value['nct_bank'], 
							'status' 			=> $value['nct_status'], 
							'branch_name' 		=> $value['nct_branch'], 
							'holder_name' 		=> $value['nct_holdername'],
							'nct_issue_date' 	=> $value['nct_issue_date'],
							'collected_by' 		=> $value['nct_collected_by'],
							'amount' 			=> $value['nct_amount'],
							'created_by' 		=> $Created_by,
							'updated_by' 		=> $Created_by
						);

						//get the last updaetd id
				        $lastid = $this->objNctTrackerHistory->getNctId($value['nct_history_id']);

						$data = $this->objNctTracker->updateMainTableNctData($lastid, $updatedata);

						// save the data into vpuchers table
						$updatedata['deposited_to'] = $value['nct_deposited_to'];
						$updatedata['issued_date'] = $value['nct_issue_date'];

					} 

					// Making array for History Table
					$nct_tracking_history[$successloop] = array(

							"nct_id"			=>	$lastid,
							"nct_ref_no" 		=> 	$value['nct_ref_no'],
							"hist_date" 		=> 	$value['nct_issue_date'],
							"prev_status" 		=> 	$value['previous_status'],
							"current_status" 	=> 	$value['nct_status'],
							"nct_bank" 			=> 	$value['nct_bank'],
							"nct_branch" 		=> 	$value['nct_branch'],
							"changed_by" 		=> 	$value['nct_collected_by'],
							"created_by" 		=> 	$Created_by,
							"updated_by" 		=> 	$Created_by,
							"comment" 			=> 	$value['nct_collected_by'] . " Collected : " . $value['nct_amount'] . " on " . $value['nct_issue_date'] . " <br>" . $value['nct_bank'] . " : " . $value['nct_branch'] ."<br> Holder : " . $value['nct_holdername'] .  "  - Bulk Update Successfull"

	                );
	                // save into history table
					$data = DB::table("nct_transcation_history")->insert($nct_tracking_history);

					// Insert into Voucher Table if NCT Status = Deposited in bank
					$ledgerNameGroup = $this->objNctTracker->getNameAsperHoldername($value['nct_holdername']);
					/*$collection_data = $this->objNctTracker->getCollectionCode($value['nct_history_id']);

					$InvoiceCode = $this->objNctTracker->getInvoiceCode($value['nct_history_id']);
					$invoice_code = isset($InvoiceCode[0]) ? $InvoiceCode[0]->invoice_code : 0;*/
					$CollInvoiceCode = $this->objNctTracker->getCollInvoiceCode($value['nct_history_id']);

					$invoice_code = $CollInvoiceCode[0]->invoice_code;
					$cost_center = $CollInvoiceCode[0]->cost_center;
					
					if($value['nct_status'] == 11902) {
						$this->objNctTracker->saveIntoVouchersTable($lastid,$updatedata,$ledgerNameGroup,0,$cost_center,$invoice_code);
					}
				}
			}
		}
		return json_encode($responseMsg);
	}
}