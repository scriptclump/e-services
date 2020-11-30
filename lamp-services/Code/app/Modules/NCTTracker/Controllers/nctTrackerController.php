<?php
//NCT dashboard 
namespace App\Modules\NCTTracker\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\NCTTracker\Models\nctTrackerModel;
use App\Modules\NCTTracker\Controllers\commonIgridController;
use App\Modules\NCTTracker\Models\nctTrackerHistoryModel;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\ProductRepo;
use Log;
use App\Modules\Ledger\Models\LedgerModel;
use Illuminate\Http\Request;
use Response;
use Redirect;
use Session;
use Input;
use App\Modules\Roles\Models\Role;
use Notifications;
//use DB;

class nctTrackerController extends BaseController {

	private $objNctTracker = "";
	private $objNctTrackerHistory = "";
	private $objCommonGrid = "";
	private $objLedger = "";

	public function __construct(){

		$this->objNctTracker = new nctTrackerModel();
		// This is required for Sales Order Code
		$this->objLedger = new LedgerModel();
		$this->objNctTrackerHistory = new nctTrackerHistoryModel();
		$this->objCommonGrid = new commonIgridController();
		$this->_roleRepo = new RoleRepo();
		$this->_productRepo = new ProductRepo();
		$this->NctBulkObj = new nctCommonController();


		// Check the Access label and permission
		try {
			$this->middleware(function ($request, $next) {
	            if (!Session::has('userId')) {
	                Redirect::to('/login')->send();
	            }

	            parent::Title('NON CASH TRANSACTION TRACKER');
	            $access = $this->_roleRepo->checkPermissionByFeatureCode('NCT001');


	            if (!$access && Session::get('legal_entity_id')!=0) {
		                Redirect::to('/')->send();
	                die();
	            }
	            	 return $next($request);
            });

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
	}

	
	// ncttracker DashBoard / Index Controller
	public function nctDashboard(){
		try{

			$breadCrumbs = array('HOME' => url('/'),'NCT Tracker Dashboard' => '#', 'Dashboard' => '#');
			parent::Breadcrumbs($breadCrumbs);

       		$dashboardAccess = $this->_roleRepo->checkPermissionByFeatureCode('NCT004');
       		$bankNames = $this->objNctTracker->getBankNameFromTalleyLedgerMaster();
			$statusdetails = $this->objNctTracker->getStatusFromMasterlookup();

			$holdername = $this->objNctTracker->getNameLedgerNames();
			return view('NCTTracker::nctTracker',['statusdetails'=>$statusdetails,'dashboardAccess'=>$dashboardAccess,'bankNames'=>$bankNames,'holdername'=>$holdername]);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	// GET THE USER LIST ON AJAX SEARCH
	public function getUsersList(){
		try{

			$term = Input::get('term');
			$user_name = $this->objNctTracker->getUserName($term);
		    echo json_encode($user_name);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function getIFSCList(){
        try{

            $term = Input::get('term');
            $user_name = $this->objNctTracker->getIfsclist($term);
            echo json_encode($user_name);
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }

    // get deposited to

    public function getDeposited($option){
        try{


            $options = $this->objNctTracker->getDepositeTypes($option);
            return $options;

        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }

	//get name from tally ledgers
	public function getNameFromTallyLedgers(){
		try{

			$term = Input::get('term');
			$user_name = $this->objNctTracker->getHolderName($term);
		    echo json_encode($user_name);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		    echo json_encode('Error');
		}
	}

	// FUNCTION FOR DASHBOARD
	public function nctTrackerDataDashboard(Request $request){

		try{

			$makeFinalSql = array();

			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }

		    // make sql for collection code
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("collection_code", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for master lookup name
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("master_lookup_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for reference no
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("reference_no", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for amount
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("amount", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for amount
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("CurrentStatusName", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for CollectionStatus
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("CollectionStatus", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for date
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("collected_on", $filter,true);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for collected by
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("FullName", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("BuName", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("OrderCode", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $orderBy = "";
		    $orderBy = $request->input('%24orderby');
		    if($orderBy==''){
		        $orderBy = $request->input('$orderby');
		    }

		    // Arrange data for pagination
		    $page="";
		    $pageSize="";
		    if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
		        $page = $request->input('page');
		        $pageSize = $request->input('pageSize');
		    }

		    $editNCTAcess=1;
		    $addNCTAccess=1;
			if(Session::get('legal_entity_id')!=0){
			    $addNCTAccess = $this->_roleRepo->checkPermissionByFeatureCode('NCT002');
			    $editNCTAcess = $this->_roleRepo->checkPermissionByFeatureCode('NCT003');
			}

		    $condition =($addNCTAccess == 1);
		    
			//Fetching Main table data
			$mainTableData = json_decode( json_encode( $this->objNctTracker->viewNctTrackerData($makeFinalSql, $orderBy, $page, $pageSize) ), true);
			
			$nctDetailsData = array();
			$loopCounter=0;
			foreach ($mainTableData as $value) {
				// Get data from NCT table

				$NCTData = $this->objNctTracker->getDatafromNCTByID($value['history_id']);

				$actionButton = $addNCTAccess != 1 ? '' : '<a data-type="add" data-toggle="modal"  onclick="addNctData('.$value['history_id'].','.$value['balance'].')" ><i class="fa fa-plus"></i></a>';
				
				$actionButton .= '';
				
				if($value['proof']!=''){
					$actionButton .= '&nbsp&nbsp&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="viewNctCheckPage('.$value['history_id'].')"><i class="fa-picture-o"></i></a>';
				}

				$nctDetailsData[$loopCounter] = array(
					"history_id"				=> 	$value['history_id'],
					"collection_code"			=> 	$value['collection_code'],
					"master_lookup_name"		=> 	$value['master_lookup_name'],
					"reference_no"				=> 	$value['reference_no'],
					"amount"					=> 	$value['amount'],
					"collected_on"				=> 	$value['collected_on'],
					"FullName"					=> 	$value['FullName'],
					"CustomAction"				=> 	$actionButton,
					"CollectionStatus"			=> 	$value['CollectionStatus'],
					"CollectionStatus_Srch"		=> 	$value['CollectionStatus_Srch'],
					"OrderCode"					=> 	$value['OrderCode'],
					"BuName"					=> 	$value['BuName'],
					"nct_details"				=> 	json_decode(json_encode( $this->objNctTracker->totalNctTrackerDetails($value['history_id'],$editNCTAcess) ) )
				);

				$loopCounter++;
			}

			return json_encode($nctDetailsData);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			return json_encode('Error');
		}
	}

	// SAVE NCT DATA INTO TABLE
	public function saveNctDetails(Request $request){
		try{

			$nctdata = $request->input();
			// Check for the Reff number in the table
			$MaintableId = $nctdata['MaintableId'];
			$CheckRefNocount = $this->objNctTracker->CheckRefNoExist($nctdata['reference_no'],$MaintableId);
			$insertUpdateFlag = 0;
			$getMainTableID = "";

			// If Reff number is exist then ignore adding it
			if($CheckRefNocount == 0){
		       	//Product Image code
				$photo = $request->file('nct_proof_image');
				$EntityType="collections";
				$type=1;
				$url="";
				if(is_object($photo)){
					//uploading  proof image to S3
					$url=$this->_productRepo->uploadToS3($photo,$EntityType,$type);
				}
				// check the count
	            $nctdata['proof_image'] =$url;


				//get the invoice  code from collections table for reference of voucherentry
				$CollInvoiceCode = $this->objNctTracker->getCollInvoiceCode($nctdata['MaintableId']);
				$invoice_code = $CollInvoiceCode[0]->invoice_code;
				$cost_center = $CollInvoiceCode[0]->cost_center;
				// Check in the NCT table for the history ID
            	$getMainTableID = $this->objNctTracker->saveNctDetailsIntoDB($nctdata);
				//saving the data into history table
				$historyid=$this->objNctTrackerHistory->saveIntoHistoryTable($getMainTableID,$nctdata);
				$insertUpdateFlag = 1;

				$holder_name = isset($nctdata['holder_name']) ? $nctdata['holder_name'] : "";
				$ledgerNameGroup = $this->objNctTracker->getNameAsperHoldername( $holder_name );
				//update the colletion_history table if check bounced and Deposited in Bank
				if($nctdata['status'] == 11902 && $ledgerNameGroup!="" ){ 
					$this->objNctTracker->saveIntoVouchersTable($historyid, $nctdata, $ledgerNameGroup, 0, $cost_center,$invoice_code);

				}elseif( ($nctdata['status'] == 11903 || $nctdata['status'] == 11906) && $ledgerNameGroup!="" ){ 

					$this->objNctTracker->saveIntoVouchersTable($historyid, $nctdata, $ledgerNameGroup, 1, $cost_center,$invoice_code);
				}

				// Complete the order on specific Status
				if($nctdata['status'] == 11904){
					$this->objLedger->getChequeStatusByHistoryId($nctdata['MaintableId']);
				}
			}else{
				$insertUpdateFlag = 2;
			}
			$displayMSG = "";

		    if($insertUpdateFlag == 1){
		        $displayMSG = "Tracking details added Successfully";
		    }else{
		        $displayMSG = "Reference No Already Exist,Please Update  or Add new!";
		    }

		    return $displayMSG;
        }
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		    return "Some Error Found Please Try Again!";
		}
	}

	// TO DISPLAY ALL THE DATA AT THE TIME OF UPDATE
	public function getNctTrackerDetailsById($nctid){
		try{	
			return $this->objNctTracker->getNctDetails($nctid);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}


	public function getHistoryDetailsById($nctid){

		try{
			$historyData = $this->objNctTracker->getNctTrackerHistoryDetails($nctid);
			$reffNo = "";
			$amount = "";
			$collectedBy = "";
			$profImage = "";

			$bp = url('uploads/LegalEntities/profile_pics');
	        $base_path = $bp."/";   
	        $img = $base_path."avatar5.png";
			$historyHTML = "";
			$loopCounter = 1;
			
			$nctHistoryID = "";
			foreach ($historyData as $value) {
				$reffNo = $value->nct_ref_no;
				$amount = $value->nct_amount;
				$collectedBy = $value->nct_collected_by;
				$nctHistoryID = $value->nct_history_id;
				$profImage = $value->NctHistProof;

				$timeLineCSS = "";
				if( $loopCounter==count($historyData) ){
					$timeLineCSS = "timeline_last";
				}else{
					$timeLineCSS="timeline";
				}
				$historyHTML .= '
				<div class="'.$timeLineCSS.'"  >
	                <div class="timeline-item timline_style">  
	                    <div class="timeline-badge">
	                        <img class="timeline-badge-userpic" src="'.$img.'" style = "width:60px;">
	                    </div>
	                    <div class="timeline-body">
	                        <div class="row">
	                        <div class="col-md-2 changedByName" id = "changedByName"><b>Logged By</b> : <br>'.$value->RecordAddedByName.' <br> <b>Against</b>: <br>'.$value->ChangedByName.'
	                        <p>
	                            <span id="recordAddedByName"></span>
	                        </p>
	                        </div>

		                        <div class="col-md-2" id = "reffNo">'.$reffNo.'
		                        </div>

	                        <div class="col-md-1" id="hist_date">'.$value->hist_date.'</div> 
	                        <div class="col-md-3 push_right" id="prev_status">'.$value->CurrentStaus.'</div>
	                        <div class="col-md-3 push_right" id="comment">'.$value->comment.'  </br> <b>Extra Charges :'.$value->extra_charges.'</b></div></div>                
	                    </div>
	                </div>
	            </div>
				';
				$loopCounter++;
			}

			// Get proof image from collection history
			if(trim($profImage)==""){
				$profImage = $this->objNctTracker->getDatafromCollectionHistory($nctHistoryID);
				$profImage = isset($profImage[0]) ? $profImage[0]->proof : "";
			}

			// Arranging the data
			$returnDataArray = array(
				'reffNo'		=> $reffNo,
				'amount'		=> $amount,
				'collectedBy'	=> $collectedBy,
				'historyHTML'	=> $historyHTML,
				'profImage'		=> $profImage
			);

			return $returnDataArray;
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	// Collection image
	public function getCoillectionImageByID($collectionID){
		$collectionImage = $this->objNctTracker->getDatafromCollectionHistory($collectionID);

		if( !empty($collectionImage) ){
			$collectionImage = $collectionImage[0]->proof;
		}else{
			$collectionImage = "";
		}
		return $collectionImage;
	}

	public function getNctDetailsById($nctid){

		try{
		// check the data already enterend in the NCT table or nor
			$returnData = array();
				// Take the data from Collection history Data
				$checkNCTData = $this->objNctTracker->getNCTDataFromDB(0, $nctid);
				$returnData = array(
						"ReffNo"				=> $checkNCTData->reference_no,
						"BankName"				=> "",
						"BranchName"			=> "",
						"Holdername"			=> "",
						"IssuedDate"			=> "",
						"CollectedByName"		=> $checkNCTData->UserName,
						"Amount"				=> round($checkNCTData->amount,2),
						"Status"				=> "11905",
						"collectedById"			=> $checkNCTData->collected_by,
					);
			return $returnData;
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function getNctDataByRow($ncit){
			// the NCT table then take the data from there
			$returnData = array();
			// Take the data from NCT tracking table
			$historyref=$this->objNctTrackerHistory->getNctDataByRowDB($ncit);
			$checkBalance=$this->objNctTrackerHistory->CheckBalanceAmount($historyref->nct_history_id);
			$returnData = array(
						"ReffNo"				=> $historyref->nct_ref_no,
						"BankName"				=> $historyref->nct_bank,
						"BranchName"			=> $historyref->nct_branch,
						"Holdername"			=> $historyref->nct_holdername,
						"IssuedDate"			=> date('d/m/Y', strtotime( $historyref->nct_issue_date) ),
						"IssuedDateForDate"		=> date('Y-m-d', strtotime( $historyref->nct_issue_date) ),
						"CollectedByName"		=> $historyref->UserName,
						"Amount"				=> $historyref->nct_amount,
						"Status"				=> $historyref->nct_status,
						"collectedById"			=> $historyref->nct_collected_by,	
						"DepositedTo"			=> $historyref->nct_deposited_to,				
						"NCTid"					=> $historyref->nct_id,				
						"Balance"				=> $checkBalance,				
						"MaintableId"			=> $historyref->nct_history_id				
			);

		return $returnData;
	}

	// UPDATE NCT DATA
	public function updateEachNctDetails(Request $request){

		$nctdata = $request->input();
		$insertUpdateFlag = 0;
		$holder_name = isset($nctdata['holder_name_view']) ? $nctdata['holder_name_view'] : "";
		$MaintableId=$nctdata['nct_id_view'];
		$nctID = $nctdata['nct_id_view'];
        $checkPrevStatus = $this->objNctTrackerHistory->checkCurrStatusById($MaintableId);
        $checkCurrStatus = $nctdata['status_view'];
		$errorFlag =0;
		$errorMsg = "";
		$displayMSG = "";
		$extraCharge = $nctdata['extra_charge_view'];
		$TallyFlag =0;
		$collectedbycash = 0;

		// Below are the flow combination for NCT
		//==================================================================================================
		// 11905 -> Collected || 11902 -> Deposit in Bank || 11907 -> Collected in cash
		// 11904 -> Payment Clear || 11906 -> Cheque bounced || 11908 -> Extra charges collected

		// if $TallyFlag == 1 then we are not sending data into tally
		if($checkPrevStatus == 11905 && ($checkCurrStatus == 11902 || $checkCurrStatus == 11907)){
			$errorFlag = 1;

		}else if(($checkPrevStatus == 11902) && ($checkCurrStatus == 11904 || $checkCurrStatus == 11906)){
			$errorFlag = 1;

		}else if(($checkPrevStatus == 11907 ) && ($checkCurrStatus == 11904)){
			$collectedbycash = 1;
			$errorFlag = 1;

		}else if( $checkPrevStatus == 11906 && $checkCurrStatus == 11905 ){
			$errorFlag = 1;

		}else if( $checkCurrStatus == 11908  && $extraCharge!='' ){
			$errorFlag = 1;

		}else if( $checkPrevStatus == 11908 ){
			$errorFlag = 1;
			// When extra charges collected is in between the flow we need to allow the remaining in tally entries
			//$TallyFlag = 1;

		}else if($checkCurrStatus == $checkPrevStatus){
			$TallyFlag = 1;
			$errorFlag = 1;
		}else{
			$errorFlag = 0;
			$displayMSG = "Wrong Status Selected! Select Correct Status";
		}

		$Paymentclearaccess = $this->_roleRepo->checkPermissionByFeatureCode('NCT005');
		if($checkCurrStatus == 11904 && $Paymentclearaccess != 1){
			$errorFlag = 0;
			$displayMSG = "You Don't Have Permission to Clear Payment!";
		}
		if($errorFlag == 1){
			// need history id for invoice code
			$historyid=$this->objNctTrackerHistory->saveIntoHistoryTableOnUpdate($MaintableId,$nctdata);
			//get the invoice  code ,cost center from collections table for reference of voucherentry
			$CollInvoiceCode = $this->objNctTracker->getCollInvoiceCode($nctdata['MaintableId_view']);
			$invoice_code = $CollInvoiceCode[0]->invoice_code;
			$cost_center = $CollInvoiceCode[0]->cost_center;

			// Get tally ledger from tally table
			$ledgerNameGroup = $this->objNctTracker->getNameAsperHoldername($CollInvoiceCode[0]->TallyLedger);

			// Insert into vouchers table for Tally entry
			// Deposited in Bank, Extra charge collected and Collected in Cash with Payment clear option we are sending data to Tally
			if(($nctdata['status_view'] == 11902 || $nctdata['status_view'] == 11908 || $collectedbycash == 1 ) && $ledgerNameGroup!="" && $TallyFlag == 0 ){ 
				$this->objNctTrackerHistory->saveIntoVouchersTableOnUpdate($historyid, $nctdata, $ledgerNameGroup, $CollInvoiceCode[0]->TallyLedger, 0, $cost_center,$invoice_code);

			}elseif(($nctdata['status_view'] == 11903 || $nctdata['status_view'] == 11906 ) && $ledgerNameGroup!="" && $TallyFlag == 0){
				// Even for Cheque bounced or canceled by Bank also we are sending to Tally
				$this->objNctTrackerHistory->saveIntoVouchersTableOnUpdate($historyid, $nctdata, $ledgerNameGroup, $CollInvoiceCode[0]->TallyLedger, 1, $cost_center,$invoice_code);
			}

			if($nctdata['status_view'] == 11904 ){
					$this->objLedger->getChequeStatusByHistoryId($nctdata['MaintableId_view']);
			}

			$updateeach=$this->objNctTrackerHistory->UpdateEachDetailsNct($nctdata);
			$displayMSG = "";
		    if($updateeach){
  		        $displayMSG = "Tracking details Updated Successfully!";
			}
		}
		return $displayMSG;
	}
	public function NctBulkDataUpdate(Request $request){
/*	$nctdata = array
	(
	    'deposited_by' => 6706,
	    'depositedate' => '29/06/2017',
	    'status' => 11902,
	    'userId' => 3,
	    'bankname' => '101200  Axis Bank - 916020030420599 (C/A)',
	    'data' => array
	        (
	             array
	                (
	                    'historyId' => 8713,
	                    'refNumber' => '0098778',
	                    'customerName' => '201002 : DMART Ramanthapur',
	                    'chequeAmount' => 575,
	                    'issuedate' => '06/29/2017',
	                    'holderBank' => 'kldf',
	                    'holderName' => 'ksdl',
	                    'branch' => 'klsd',
	                    'proof' => 'https://s3.ap-south-1.amazonaws.com/ebutormedia-test/collections/1498568840.jpeg'
	                ),

	             array
	                (
	                    'historyId' => 8338,
	                    'refNumber' => '100001100009',
	                    'customerName' => '201002 : DMART Ramanthapur',
	                    'chequeAmount' => '575',
	                    'issuedate' => '06/30/2017',
	                    'holderBank' => 'dkf',
	                    'holderName' => 'ksdml',
	                    'branch' => 'sdk',
	                    'proof' => "https://s3.ap-south-1.amazonaws.com/ebutormedia-test/collections/1498568921.jpeg"
	                )

	        )

	);
*/
	 	$nctdata = $request->input();

		$send = $this->NctBulkObj->NctBulkUpdate($nctdata);
	}
}