<?php
/*
FileName : expensesTrackController
Author   : eButor
Description : Function Written for all controller function related to Expenses.
CreatedDate : 26/Dec/2016
*/
//expenses dashboard 
namespace App\Modules\ExpensesTracker\Controllers;
use App\Http\Controllers\BaseController;
use App\Modules\ExpensesTracker\Controllers\commonIgridController;
use App\Modules\ExpensesTracker\Models\expensesTrackModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\ExpensesTracker\Models\expensesAPIModel;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\RoleRepo;
use Log;
use Illuminate\Http\Request;
use Response;
use Redirect;
use Session;
use Input;
use Excel;
use Carbon\Carbon;

date_default_timezone_set('Asia/Kolkata');


class expensesTrackController extends BaseController {

	private $objExpensesTracker='';
	private $objApproval='';
	private $objCommonGrid = '';
	private $makeFinalSql = '';
	private $sqlForSession = '';
	private $objAPIModel = '';

	public function __construct(){
		$this->_roleRepo = new RoleRepo();
		$this->objExpensesTracker = new expensesTrackModel();
		$this->objApproval = new CommonApprovalFlowFunctionModel();
		$this->objCommonGrid = new commonIgridController();
		$this->_roleRepo = new RoleRepo();
		$this->objAPIModel = new expensesAPIModel();

		$this->objPushNotification 		= new ProductRepo();

		try {
			parent::Title('ExpensesTracker');
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                $access = $this->_roleRepo->checkPermissionByFeatureCode('EXP001');
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

	// Expenses Tracker DashBoard / Index Controller
	public function expensesDashboard(){
		try{
			$breadCrumbs = array('Home' => url('/'),'Expenses' => '#', 'Dashboard' => '#');
			parent::Breadcrumbs($breadCrumbs);

			// Get all the variable value
			$getTallyDetails = $this->objAPIModel->getTallyLedgerDetails();
			$expensesDebitData = $this->objExpensesTracker->getExpensesDrData();
			$expensesCreditData = $this->objExpensesTracker->getExpensesCrData();

			// Assign all the variables value
			$downloadAccess=1;
  			$dashboardAccess=1;
  			$userAccess=1;
  			$directExpAccess=1;

  			if(Session::get('legal_entity_id')!=0){
				$downloadAccess = $this->_roleRepo->checkPermissionByFeatureCode('EXP002');
				$dashboardAccess = $this->_roleRepo->checkPermissionByFeatureCode('EXP003');
				$userAccess = $this->_roleRepo->checkPermissionByFeatureCode('EXP005');
				$directExpAccess = $this->_roleRepo->checkPermissionByFeatureCode('EXP006');
         	}
			return view('ExpensesTracker::expensestrack',['userAccess'=>$userAccess,'expensesDebitData'=>$expensesDebitData,'expensesCreditData'=>$expensesCreditData,'downloadAccess'=>$downloadAccess,'dashboardAccess'=>$dashboardAccess,'getTallyDetails'=>$getTallyDetails,'directExpAccess'=>$directExpAccess]);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}
	public function expensesTrackerDashboard(Request $request){
		try{

		$request->session()->put('expGlobalQuery', "");
		$this->makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }

        $sqlForSession = array();

        // make sql for Expense Ref Type
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ExpensesType", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }

        // make sql for Expense submitted by
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("SubmittedBy", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;


        }
        // make sql for Expense approval status
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ApprovalStatus", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }
        // make sql for Expense Code
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("exp_code", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }
         // make sql for Expense Date
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ExpSumitDate", $filter, true);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
        }

        // make sql for Expense Actual Amount
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("exp_actual_amount", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }

        // make sql for Expense Approval Amount
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("exp_approved_amount", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }
         // make sql for Business unit name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("business_name", $filter);

        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;

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

		$request->session()->put('expGlobalQuery',$this->sqlForSession);

		return $this->objExpensesTracker->showexpensesDetails($this->makeFinalSql, $orderBy, $page, $pageSize);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function getExpensesDetails($expid){

		try{
		// Get the Expenses data as per the expID
		$expensesData = $this->objExpensesTracker->getExpensesData($expid);
		$refType = $this->objExpensesTracker->getMasterlookupdata();
		$busData = $this->objExpensesTracker->getBusinessUnitWithID();

		$detailsHTMLPart = "";
		$submittedByID = "";
		$expensesMainId = "";

		foreach ($expensesData as $value) {

			// Arrange expenses type drop-down
			$submittedByID= $value->submited_by_id;
			$expensesMainId = $value->exp_id;

			$expDropDown = "";
			foreach ($refType as $expTypeVal) {

				$images_data = $value->exp_det_proof;
				$images =explode(',', $images_data);

				$ImageData = "";
				for( $j=0;$j < count($images);$j++){
					if($images[$j]!=""){
                		$ImageData .= '<a href="'.$images[$j].'" id="proof_file" target="_blank" ><img id="expense_file" name="expense_file" src="'.$images[$j].'" style = "width:30px; height:30px;"/></a>&nbsp;&nbsp';
                	}
                }


				$selectedTxt = "";
				if($expTypeVal->value==$value->exp_type){
					$selectedTxt = "selected";
				}
				$expDropDown .= "<option value='" . $expTypeVal->value . "' ".$selectedTxt.">" . $expTypeVal->master_lookup_name . "</option>";
			}

			// Manage line item as per the approval
			$updateAmtDisable = $value->exp_appr_status=='1' ? 'disabled="disabled"' : '';  
			$updateButton = $value->exp_appr_status=='1' ? '' : '<div class="col-md-3"><button type="button" href="javascript:void(0)" cl style="margin-left: -21%;margin-top: 0px;height: 34px;;"  onclick="updateAmount('.$value->exp_det_id.','.$value->exp_id.')">
                            <i class="fa fa-pencil"></i>
                            </button></div>';

			$detailsHTMLPart .= '<tr class="gradeX odd">
		        <td data-val="list_details" class="prom-font-size" id="update_ref_type" name="update_ref_type[]"><div class="row"><div class="col-md-9"><select id="ref_type_id_expense" name= "ref_type_id_expense" class="form-control">'.$expDropDown.'</select></div
		        ><div class="col-md-3"><button  type = "button" href="javascript:void(0)" cl style="margin-left: -21%;margin-top: 0px;height: 32px;;"  onclick="updateName('.$value->exp_det_id.')">
                            <i class="fa fa-pencil"></i>
                            </button></div>
                            </div>
                            </td>
                <td data-val="list_details" class="prom-font-size" id="upda_descc_'.$value->exp_det_description.'" name="upda_descc_[]" >'.$value->exp_det_description.'</td>
		        <td data-val="list_details" class="prom-font-size" id="update_det_actual_amount_'.$value->exp_det_id.'" name="update_det_actual_amount[]" >'.$value->exp_det_actual_amount.'</td>
		        <td data-val="list_details"> <input type="text" class="col-md-9 amount" id="update_approved_amount_'.$value->exp_det_id.'" name= "update_approved_amount[]" value="'.$value->exp_det_approved_amount . '" '. $updateAmtDisable .'  />'.$updateButton.'</td>
		        <td data-val="list_details" class="prom-font-size">'.$ImageData.'</td><input type="hidden"  value = "'.$value->exp_det_id.'" id="hidden_approve_id" name="hidden_approve_id[]" class="form-control">
		        </tr>';
		}

		// get BuID from that submitted by user id
		$businessID = $this->objExpensesTracker->getBusinesID($submittedByID, $expensesMainId);
		$businessID = explode(";", $businessID);

		$businessID = isset($businessID[1]) && $businessID[1]>0 ? $businessID[1] : $businessID[0]; 

		// Make data for BU Dropdown
		$buDropDown = "<select name='business_unit_dp' id='business_unit_dp' class='form-control' style='float: left !important; width: 220px !important;'>";
		$buDropDown .= "<option value='0'>Please Select</option>";
		
		foreach ($busData as $value) {

			$text = "";
			if($value->bu_id == $businessID){
				$text = "selected";
			}

			$buDropDown .= "<option value='" . $value->bu_id ."' ".$text." >" . $value->BuName . "</option>";
		}

		$buDropDown .= '</select><button type="button" style="float: left !important; height:34px !important;" onclick="updateBusiness()"><i class="fa fa-pencil"></i></button>';


		// Store the approval Status Name
		$apprName = "AdvanceExpensesAppr";
		if($expensesData[0]->exp_req_type == '122001'){
			$apprName = 'AdvanceExpensesAppr';
		}elseif( $expensesData[0]->exp_req_type == '122002' ){
			$apprName = 'ReimbursementExpensesAppr';
		}elseif( $expensesData[0]->exp_req_type == '122003' ) {
			$apprName = 'VendorPaymentExpensesAppr';
		}

		// Get the Approval Data as per the user Role
		if($expensesData[0]->exp_appr_status==0 || $expensesData[0]->exp_appr_status==''){
			$approvalData = $this->objApproval->getApprovalFlowDetails( $apprName, 'drafted', Session::get('userId') );
		}else{
			$approvalData = $this->objApproval->getApprovalFlowDetails( $apprName, $expensesData[0]->exp_appr_status, Session::get('userId'), $expid );
		}

		$apprrovalPermission=$approvalData['status'];

		$finalApprArray = array();
		if( isset($approvalData['data']) ){

			$currentStatusID = $approvalData['currentStatusId'];
			$loopCounter=0;
			foreach($approvalData['data'] as $apprData){

				$finalApprArray[$loopCounter]['condition'] 				= $apprData['condition'];
				$finalApprArray[$loopCounter]['nextStatusId'] 			= $apprData['nextStatusId'];
				$finalApprArray[$loopCounter]['isFinalStep'] 			= $apprData['isFinalStep'];
				$finalApprArray[$loopCounter]['currentStatusID'] 		= $currentStatusID;

				$loopCounter++;
			}
		}
		return array('expensesData' => $expensesData, 'apprData' => $finalApprArray, 'refType' =>$detailsHTMLPart,'businessData' => $buDropDown,'apprrovalPermission' => $apprrovalPermission);
		}catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}


	public function updateExpenseData(Request $request){

		//echo "asdf";exit;
		
		$next = $request->input('NextStatusID');
		$explodevalue = explode(',', $next);
		$userID = $request->input("UserId");

		//$flowType 			= trim($request->input('FlowType'));
		$ExpensesMainID 	= trim($request->input('ExpensesMainID'));
		$currentStatusID 	= trim($request->input('CurrentStatusID'));
		$NextStatusID 		= $explodevalue[0];
		$Comment 			= trim($request->input('Comment'));
		$ApprovalAmount 	= trim($request->input('Approval_amount'));
		$userID 			= $userID;
		$tallyLedgerName 	= trim($request->input('TallyLedgerName'));
		$isFinalStep 		= $explodevalue[1];
		$RequestFlowType  = trim($request->input('RequestFlowType'));
		$flowType = $RequestFlowType . "ExpensesAppr";

		if($isFinalStep!=1){
			$isFinalStep=$NextStatusID;
		}

		$this->objAPIModel->updateExpensesMainTableforApproval($ExpensesMainID, $ApprovalAmount, $isFinalStep,$tallyLedgerName);
		// Return and call the Approval History Data
		$approvalDataResp = $this->objApproval->storeWorkFlowHistory($flowType, $ExpensesMainID, $currentStatusID, $NextStatusID, $Comment, $userID);

		// Save data into Vouchers table if it is final work flow
		if($isFinalStep == 1){
			$this->saveVoucherDetails($ExpensesMainID, $RequestFlowType);
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
			}else{
				$message_init = 'Your ' . $requestTypeName . ' of : Rs. ' .  $ApprovalAmount . ' is Approved!';
			}
			
			$pushNotification = $this->objPushNotification->pushNotifications($message_init, $tokenDetails_init);
		}

		return "Success";
	}


	//update only expenses type in expenses details table
	public function UpdateExpensesTypeInDetailsTable($exp_id,Request $request){

		$exp_type = $request->input('master_lookup');
		$this->objExpensesTracker->updateDetailsTableWithId($exp_id,$exp_type);

	}
	public function UpdateExpensesAmountInDetailsTable( Request $request){
		
		$exp_det_approved_amount = $request->input('ApprovedAmount');
		$id = $request->input('detailId');
		$expenseId = $request->input('expensesMainId');

		$appr_status=$this->objExpensesTracker->getApprovalStatusByExpID($expenseId);

		$returnMSG = "Expenses Amount Updated Successfully!";
		$amount = $exp_det_approved_amount;

		if($appr_status == 1){
			$returnMSG = "Expenses is already approved or not exist, Update aborted!!";
		}elseif( trim($exp_det_approved_amount)=="" || trim($id)=='' || trim($expenseId)=='' ){
			$returnMSG = "Invalid data sent, Update aborted!";
		}else{
			$amount = $this->objExpensesTracker->updateDetailsTableAmountWithId($id,$expenseId,$exp_det_approved_amount);
		}

		return $returnMSG . "==!!" . $amount;
	}
	public function updateBusinessUnit(Request $request){
		$exp_id = $request->input('ExpenseID');
		$bus_id = $request->input('BusinessId');
		$this->objExpensesTracker->updateMainBusinessUnit($exp_id,$bus_id);
	}

	// Prepare tally ledger data and call the function
    public function saveVoucherDetails($ExpensesMainID, $RequestFlowType){

    	if( $RequestFlowType=='Advance' ){

	    	// Get Main Table Data
	    	$mainTableData = $this->objExpensesTracker->getTallyLedgerNameForAdvanceDr($ExpensesMainID);
	    	// Get the Dr Name
	    	$exptally['expenses_dr'] = $mainTableData[0]->TallyLedgerForAdvanceDr;

	    	// Get the Cr Name
	    	$mainTableData = $this->objExpensesTracker->getExpensesDetailsWithId($ExpensesMainID);
			$exptally['expenses_cr'] = $mainTableData[0]->tally_ledger_name;

	    	if(isset($mainTableData[0])){
	    		$storeintovouchers = $this->objExpensesTracker->saveIntoVouchersTableAdvance($mainTableData,$exptally['expenses_dr'],$exptally['expenses_cr']);
	    	}

	    }else{

	    	// Get Main Table Data
	    	$mainTableData = $this->objExpensesTracker->getTallyLedgerNameForAdvanceDr($ExpensesMainID);

	    	// Get the Cr Name
	    	$exptally['expenses_cr'] = $mainTableData[0]->TallyLedgerForAdvanceDr;

	    	// Get the Dr Details
	    	$expDetailsData = $this->objExpensesTracker->downloadExpenseDetailsData($ExpensesMainID);
	    	$mainTableData = $this->objExpensesTracker->getExpensesDetailsWithId($ExpensesMainID);

	    	if(isset($mainTableData[0])){
	    		$storeintovouchers = $this->objExpensesTracker->saveIntoVouchersTableForReimbursment($mainTableData, $expDetailsData, $exptally['expenses_cr']);
	    	}
	    }
    }

	public function downloadExpensesData(Request $request){

			$start_date = $request->input('start_date');
			$end_date =  $request->input('end_date');

			$mytime = Carbon::now();

			$headers_line = array('Exp Code', 'Exp Type', 'Exp Subject', 'Submitted By', 'Submitted Date', 'Amount Asked', 'Amount Approved',	'Approval Status');

			$exceldata = $this->objExpensesTracker->downloadAsPerData($start_date,$end_date);
			
			$allData = array();
			$htmlData = "";

			foreach ($exceldata as $value) {

				$detailsData = $this->objExpensesTracker->downloadExpenseDetailsData($value->exp_id);

				$htmlData .= "<tr>
						<td>$value->exp_code</td>
						<td>$value->ExpensesType</td>
						<td>$value->exp_subject</td>
						<td>$value->SubmittedBy</td>
						<td>$value->ExpSumitDate_grid</td>
						<td>$value->exp_actual_amount</td>
						<td>$value->exp_approved_amount</td>
						<td>$value->ApprovalStatus</td>
						</tr/>";
				$loopCounter = 1;
				foreach ($detailsData as $detValue) {
					
					$htmlData .= "<tr>
						<td>$loopCounter</td>
						<td>$detValue->ExpensesType</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>$detValue->exp_det_actual_amount</td>
						<td>$detValue->exp_det_approved_amount</td>
						<td>&nbsp;</td>
						</tr/>";

					$loopCounter++;
				}
				
			}
			
			Excel::create('expenses_download_sheet-'.$mytime->toDateTimeString(), function($excel) use($headers_line, $htmlData){

				$excel->sheet("ExpensesDetails", function($sheet) use($headers_line, $htmlData)
		        {
		            $sheet->loadView('ExpensesTracker::expensesdownloadtemplate', array('headers_one' => $headers_line, 'data' => $htmlData)); 
		        });

			})->export('xlsx');
		
	}
	
	public function getHistoryExpensesData($expid){

		//get the expensesmain id from table
		$flowType = $this->objExpensesTracker->getExpensesTypeFromTable($expid);

		$expensesData = $this->objExpensesTracker->getExpensesData($expid);

		$apprName = "";
		if($flowType == '122001'){
			$apprName = 'AdvanceExpensesAppr';
		}elseif( $flowType == '122002' ){
			$apprName = 'ReimbursementExpensesAppr';
		}elseif ( $flowType == '122003' ) {
			$apprName = 'VendorPaymentExpensesAppr';
		}

		//get the history
		$expensesData1 = $this->objAPIModel->getApprovalHistorByIDFromDB($expid, $apprName);

		if( count($expensesData1)>1 ){
			$expensesData1[0]->master_lookup_name = $expensesData1[0]->master_lookup_name . ' / Ticket Created';
		}else{
			if(isset($expensesData1[0])){
				$expensesData1[0]->master_lookup_name = $expensesData1[0]->master_lookup_name . ' / Ticket Created';
			}
		}

		$historyHTML = "";
		$loopCounter = 1;
			$bp = url('uploads/LegalEntities/profile_pics');
	        $base_path = $bp."/";   
	        $img = $base_path."avatar5.png";
		foreach ($expensesData1 as $value) {
				$timeLineCSS = "";
				if( $loopCounter==count($expensesData1) ){
					$timeLineCSS = "timeline_last";
				}else{
					$timeLineCSS="timeline";
				}

				$historyHTML .= '
				<div class="'.$timeLineCSS.'"  >
	                <div class="timeline-item timline_style">  
	                    <div class="timeline-badge">
	                        <img class="timeline-badge-userpic" src="'.$img.'" style = "width:60px;position:relative;z-index:999 !important">
	                    </div>
	                    <div class="timeline-body">
	                        <div class="row">
	                        <div class="col-md-3 changedByName" id = "changedByName" style="margin-right:-55px">'.$value->firstname.'
	                        <p>
	                            <span id="recordAddedByName"></span>
	                        </p>
	                        </div>
	                        <div class="col-md-2" id = "hist_date">'.$value->created_at.'
		                        </div>

		                        
	                        <div class="col-md-2" id="prev_status">'.$value->master_lookup_name.'</div> 
	                        <div class="col-md-3 push_right" id="Role">'.$value->name.'</div>
	                        

	                        <div class="col-md-2 push_right" id="comment">'.$value->awf_comment.'</div></div>                
	                    </div>
	                </div>
	            </div>
				';
				$loopCounter++;
			}


			$returnDataArray = array(
				'historyHTML'	=> $historyHTML,
				'expensesData' => $expensesData
			);
			return $returnDataArray;
	}
}

