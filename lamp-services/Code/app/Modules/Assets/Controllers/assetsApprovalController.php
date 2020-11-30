<?php

namespace App\Modules\Assets\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Assets\Models\assetsApprovalModel;
use Log;
use App\Modules\Assets\Controllers\commonIgridController;
use App\Central\Repositories\RoleRepo;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\ExpensesTracker\Models\expensesAPIModel;
use Illuminate\Http\Request;
use Response;
use Redirect;
use Session;
use Input;
use Notifications;
use DB;
use Carbon\Carbon;

class assetsApprovalController extends BaseController {

	public function __construct(){

		$this->objApprove = new assetsApprovalModel();
		$this->objCommonGrid = new commonIgridController();
		$this->objApproval = new CommonApprovalFlowFunctionModel();

		$this->objAPIModel = new expensesAPIModel();
	}

	public function assetApprovalDashboard(){
		try{
		$breadCrumbs = array('HOME' => url('/'),'ASSETS DASHBOARD' => '#', 'DASHBOARD' => '#');
		parent::Breadcrumbs($breadCrumbs);

		$approvalproduct=$this->objApprove->getApproveProduct();

		$getManufactureDetails = $this->objApprove->getManufactureDetails();
		$getCategoryDetails = $this->objApprove->getCategoryDetails();
		$allocationNames = $this->objApprove->getNamesFromUsersTable();
		$businessUnit = $this->objApprove->getBusinessData();
		 


		return view('Assets::assetsapproval',['approvalproduct'=>$approvalproduct,'getManufactureDetails'=>$getManufactureDetails,'getCategoryDetails'=>$getCategoryDetails,'allocationNames'=>$allocationNames,'businessUnit'=>$businessUnit]);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function getApprovalData(Request $request){
		try{

			$makeFinalSql = array();

			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }

		    // make sql for AssetName
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("product_title", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    

		    // make sql for Allocated Name
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("AllocatedName", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for Comment
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("asset_comment", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for Master loopup name
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("master_lookup_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for created_at
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_at", $filter,true);
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

		    return $this->objApprove->approvalProductsData($makeFinalSql, $orderBy, $page, $pageSize);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function getBrandsAsManufac($manufac){
		 $returnArray = array();
		  if($manufac!=0){
		   	$returnArray = $this->objApprove->getBrandsAsManufacId($manufac);
		  }

		  return $returnArray;
	}

	public function getProductAsPerCategory($category, $brand){

		return  $this->objApprove->getProductIdByCategory($category, $brand);
	}

	public function saveApprovalData(Request $request){
		try{

			$approvalData = $request->input();

			$flowTypeForID=$this->objApprove->saveApprovalWithProduct($approvalData);

			$this->objApprove->updateApprovalStatusToDB($flowTypeForID, "57123");

			$flowType="Asset Approval Flow";
			$userID=Session::get('userId');

			$this->objApproval->notifyUserForFirstApproval($flowType, $flowTypeForID, $userID);


			return "Your Approval Request Saved Successfully";
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}


	public function getApprovalAsset($id){
		try{	
			// Getting the detail to display information of the top of the screen
			$prod_information = $this->objApprove->getInformationFromTable($id);
			$apprName = "Asset Approval Flow";

			$approvalData = $this->objApproval->getApprovalFlowDetails( $apprName, $prod_information[0]->asset_approval_status_id , Session::get('userId'),$id);

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
			return array('prodInfrom' => $prod_information, 'apprData' => $finalApprArray);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function updateApproveStatus(Request $request){
		try{	
			$data = $request->input();

			$flowType = "Asset Approval Flow";
			$ExpensesMainID = $data['hidden_approval_id'];
			$currentStatusID = $data['CurrentStatusID'];
			$next = $data['NextStatusID'];
	        $NextStatusID = explode(',', $next);
	        $Comment = $data['approve_comment'];
	        $userID = Session::get('userId');
			$this->objApprove->updateApproveColoumnInTable($data);
			// Return and call the Approval History Data
			$approvalDataResp = $this->objApproval->storeWorkFlowHistory($flowType, $ExpensesMainID, $currentStatusID, $NextStatusID[0], $Comment, $userID);

			return "You Succesfully Approved The Request";
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}

	}

	public function getHistoryAssetsApprovaData($id){
		try{
			$apprName="Asset Approval Flow";

			$getData = $this->objAPIModel->getApprovalHistorByIDFromDB($id, $apprName);

			$historyHTML = "";
			$loopCounter = 1;

			foreach ($getData as $value) {
					$timeLineCSS = "";
					if( $loopCounter==count($getData) ){
						$timeLineCSS = "timeline_last";
					}else{
						$timeLineCSS="timeline";
					}

					$historyHTML .= '		
							<tr>
								<td id = "changedByName" >'.$value->firstname.'<span id="recordAddedByName"></span></td>
								<td id = "hist_date">'.$value->created_at.'</td>
								<td id="prev_status">'.$value->master_lookup_name.'</td>
								<td id="Role">'.$value->name.'</td>
								<td id="comment">'.$value->awf_comment.'</td>
							</tr>
					';
					$loopCounter++;
				}

				$returnDataArray = array(
					'historyHTML'	=> $historyHTML
				);
				return $returnDataArray;
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}

	}


	

}
