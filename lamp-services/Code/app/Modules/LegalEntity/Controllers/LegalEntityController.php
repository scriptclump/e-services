<?php
namespace App\Modules\LegalEntity\Controllers;
use App\Http\Controllers\BaseController;
use App\Modules\LegalEntity\Models\LegalEntityModel;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\Assets\Controllers\commonIgridController;
use Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use session;
use Utility;
use Input;
use DB;
use App\Modules\Roles\Models\Role;
use Excel;
use \App\Modules\SellerWarehouses\Models\SellerWarehouses;
use Redirect;
use App\Modules\H2HAxis\Controllers\h2hAxisAPIController;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Retailer\Models\Retailer;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Response;
use Lang;

class LegalEntityController extends BaseController {

    private $legalEntity;
    protected $_roleRepo;
    protected $_docTypes;
	
    public function __construct(){
	    //updated middleware to access sessions within constructor (4/11/2019)
	$this->middleware(function ($request, $next) {
		if (!Session::has('userId')) {
		    Redirect::to('/login')->send();
		}
		$this->_roleRepo = new RoleRepo();	
		$legridaccess = $this->_roleRepo->checkPermissionByFeatureCode('LGRID0001');
		if(!$legridaccess){
			echo "You don't have access,Please Contact Admin";die();
		  }
		return $next($request);
	 });
		$this->_roleRepo = new RoleRepo();	
		$this->roleObj = new Role(); 
		// $this->roleRepo->checkPermissionByFeatureCode('L');
		$this->legalEntity = new LegalEntityModel();
		$this->_masterLookup = new MasterLookup();
		$this->_productRepo = new ProductRepo();
		$this->objCommonGrid = new commonIgridController();
		$this->docTypes = array();

	}
	public function index(){
		try{
			$breadCrumbs = array('Home' => url('/'),'DC/FC Center' => '#', 'Dashboard' => '#');
			parent::Breadcrumbs($breadCrumbs);
			$hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ADSTK');
			$stockistab = $this->_roleRepo->checkPermissionByFeatureCode('STKINV001');
			$se_wh = new SellerWarehouses();
            //$businessUnits=$this->legalEntity->getBussinessUnit();
			$state =$this->legalEntity->getStates();
			$fcDc= $this->legalEntity->getFcDcs();
			$dcFcTypes=$this->legalEntity->dcFCTypeFromLp();
			$Json=json_decode($this->roleObj->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);      
            $warehouse=$this->roleObj->GetWareHouses($filters);
            $warehouse = json_decode(json_encode($warehouse), True);
			return view('LegalEntity::legalentity')->with(['state'=>$state,'hasAccess'=>$hasAccess,'dcFcTypes'=>$dcFcTypes,'dcs'=>json_decode(json_encode($warehouse)),'stockistab'=>json_decode(json_encode($stockistab)),'fcDc'=>json_decode(json_encode($fcDc))]);
		}
		catch(Exception $e) {
		  return 'Message: ' .$e->getMessage();
		}
	}

	public function legalentityGridData(Request $request){
		try{
			$legalid = Session::get('legal_entity_id');
		    $makeFinalSql = array();			
			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }

		    // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("name_Display", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Warehouse", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for cbusiness_legal_name
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("business_legal_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
             // make sql for contact name
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("contact_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for pan_number
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pan_number", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for phone_no
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("phone_no", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for email
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("email", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for pincode
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pincode", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for state_id
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("StateName", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for city
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("city", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for gstin
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("gstin", $filter);
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
			$legaldata['data']=  $this->legalEntity->GetAllLegalEntities($makeFinalSql, $orderBy, $page, $pageSize,$legalid);
			$legaldata['data']=json_decode(json_encode($legaldata['data']),true);
			$header=json_decode(json_encode($legaldata),true);

			for($i=0;$i<count($legaldata['data']);$i++) {
				$activeStatus='<label class="switch" style="float:right;"><input class="switch-input block_users" type="checkbox" ';
                            $activeStatus.=($legaldata['data'][$i]['Is_Active'] == 1) ? 'checked="true" ' : 'check="false" ';
                            $activeStatus.='name="'.$legaldata['data'][$i]['Legal_Entity_ID'].'" id="'.$legaldata['data'][$i]['Legal_Entity_ID'].'" value="'.$legaldata['data'][$i]['Legal_Entity_ID'].'" ><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';    
                            $legaldata['data'][$i]['Is_Active'] = $activeStatus;
			}

			/*for($i=0;$i<count($legaldata['data']);$i++) {
				$view='<center><code><a href="/legalentity/viewdetails/' .$legaldata['data'][$i]['Legal_Entity_ID']. '"><i class="fa fa-eye-open"></i></a></code></center>'.$legaldata['data'][$i]['CustomAction'];    
                            $legaldata['data'][$i]['CustomAction'] = $view;
			}*/
              foreach($header['data'][0] as $key => $value){
              	$headers[]=$key;
              	
              }
              $legaldata['headers']=$headers;
			return $legaldata;
		}catch(Exception $e) {
		  return 'Message: ' .$e->getMessage();
	  }
  }

    public function editGridDetails($id){
    	try{ 
      	$details = $this->legalEntity->editGridUsersId($id);
    	$result = json_decode(json_encode($details), True);
    	return $result;
    }catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}
   }

    public function updateintotable(Request $request){
    	try{
    	$userdataid = Session::get('getting_userInfo');
     	$data = $request->input();
    	$updateintoalltables = $this->legalEntity->upDateAlldata($data,$userdataid);
    	return $updateintoalltables;
    }catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}
  }

    public function updateCustomerInfo(Request $request){
    	try{
    	$data = $request->input();
    	$updateintoalltables = $this->legalEntity->registeredData($data);
    	return $updateintoalltables;
    }catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}
  }

  public function legalentityDetails($id){
	$userId = Session::put('getting_userInfo',$id);
   	$details = $this->legalEntity->editGridId($id);
  	$state =$this->legalEntity->getStates();
  	$country = $this->legalEntity->getCountries();
  	$stockistpaymentdetails = $this->legalEntity->getPaymentDetailsFromView($details[0]->legal_entity_id);

  	$creditlimiteditView=$this->_roleRepo->checkPermissionByFeatureCode('CRTLTGDTD001');
  	$creditlimiteditfeature=$this->_roleRepo->checkPermissionByFeatureCode('CRTLTDETD001');
  	$updateFeature=$this->_roleRepo->checkPermissionByFeatureCode('DCFCUPDATE001');
  	$paymentGrid = $this->_roleRepo->checkPermissionByFeatureCode('LGRGRD001');
  	// The below two lines split "keys" and "values" and it for the Orders tab 
  	$docTypes = $this->legalEntity->getDocumentTypes();
  	$docsArr = $this->legalEntity->legalEntityDoc($id);
  	$leDocDetails = $this->legalEntity->getleDocDetails($id); 
  	if(!empty($stockistpaymentdetails)){
  		$stockistpaymentkeys = array_keys((array) $stockistpaymentdetails[0]);
  	$paymentDetails = array_combine($stockistpaymentkeys, (array) $stockistpaymentdetails[0]);
	
  	} 

  	$view='';
  	
  	parent::Title('Edit DC/FC -  '.' '. ($details[0]->business_legal_name));
  	$paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type', [2, 3]);
	$modeofpayment = DB::table('master_lookup')->whereIn("value",[16503,16504])->get();

	return view('LegalEntity::legalentitydetails',[
		'details'=>$details,
		'paymentType'=>$paymentType,
		'modeofpayments'=>$modeofpayment,
		'state'=>$state,
		'country'=>$country,
		'userlegalentityid'=>$details[0]->legal_entity_id,
		'userid'=>$id,
		'le_id' =>$id,
		'docsArr'=>$docsArr,
		'docTypes'=> $docTypes,
		'leDocDetails' =>$leDocDetails,
		'paymentDetails' => isset($paymentDetails) ? $paymentDetails : 0,'creditlimiteditView'=>$creditlimiteditView,'view'=>$view,'updateFeature'=>$updateFeature,'creditlimiteditfeature'=>$creditlimiteditfeature,'paymentGrid'=>$paymentGrid]);
  }

  public function getUsersList(Request $request){
  	$leID = Session::get('getting_userInfo');
  	$makeFinalSql = array();			
			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }
		    // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("firstname", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for phone_no
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("mobile_no", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for email
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("email_id", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for pincode
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("otp", $filter);
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
   		  	$userdata =  $this->legalEntity->GetAllUsersData($makeFinalSql, $orderBy, $page, $pageSize,$leID);
		  return json_encode(array('results'=>$userdata)); 

  } 

  public function warehousesList(Request $request){
  	$Le_ID = Session::get('getting_userInfo');
    $makeFinalSql = array();			
			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }
		    // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("contact_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for phone_no
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("lp_wh_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for email
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("phone_no", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		  
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("address1", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		     $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pincode", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		     $fieldQuery = $this->objCommonGrid->makeIGridToSQL("address1", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for pincode
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("city", $filter);
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
   		  	$userdata =  $this->legalEntity->GetWareHousesByLegalId($makeFinalSql, $orderBy, $page, $pageSize,$Le_ID);

  	return json_encode(array('results'=>$userdata));
  }

  public function createLegalEntity(Request $request){
  	try{
  	    $data = $request->input();
  	    $logo = $request->file('logo');
  	    $stId = isset($data['state_id'])? $data['state_id']:'4033';
  	    $codeName = $this->legalEntity->getStateCode($stId);
  	    $data['state_code'] = $codeName->code;
  	    $data['parent_le_id'] = Session::get('legal_entity_id');
  	    $data['created_by'] = Session::get('userId');
  	    $data['updated_by'] = Session::get('userId');
  	    $data['dcfc_legalentitytype'] = Session::get('dcfc_legalentitytype');
  	    $data['i_state_code'] = Session::get('state_code');
  	    $data['city_code'] = Session::get('city_code');
  	    $apiURL = env('EBUTOR_NODE_URL') . "/Signup/signupall";
  	    
		$finaldata['data'] = json_encode($data);
		
		$finaldata['logo'] = $logo;

		$ch = curl_init();  

		curl_setopt($ch,CURLOPT_URL,$apiURL);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                         "Content-Type: multipart/form-data") 
                    );


		//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_POST, count($finaldata));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $finaldata);   
		 
		$output=curl_exec($ch);
		curl_close($ch);

		$output = json_decode($output, true);
		if (Session::has('dcfc_legalentitytype'))
                Session::forget('dcfc_legalentitytype');
        if (Session::has('state_code'))
                Session::forget('state_code');
        if (Session::has('city_code'))
                Session::forget('city_code');
		return $output;
		}catch(Exception $e) {
      	return 'Message: ' .$e->getMessage();
    }
  }

  public function StockistPaymentsByLeID($leid,Request $request){

  		$makeFinalSql = array();			
			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }
		    // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Stockist_Code", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for phone_no
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Stockist_Name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for email
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Order_Code", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		  
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Invoice_Code", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		     $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Order_Total", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		     $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Invoice_Total", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for pincode
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Return_Total", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for pincode
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Delivered_Total", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Invoice_Date", $filter,true);
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
   		  	$userdata =  $this->legalEntity->getDataForStockistPayments($makeFinalSql, $orderBy, $page, $pageSize,$leid);
   		  	foreach ((array)$userdata['stock'] as $order) {
   		  		if(isset($order->Order_Code) and isset($order->Order_ID))
	   		  		$order->Order_Code = "<a href='/salesorders/detail/".$order->Order_ID."' target='_blank'>".$order->Order_Code."</a>";
   		  	}

  	return ['results' => $userdata['stock'], 'totalPayments' => $userdata['stockCount']];




  		//return $this->legalEntity->getDataForStockistPayments($leid);
  }

  public function saveStockistDetails(Request $request,$paydata=array()){

  	try{
  	$data1 = $request->input();
  	if(count($paydata)){
  		$data1 = $paydata;
  	}
  	if(!empty($data1['trans_date'])){
  		$data1['transmission_date'] = $data1['trans_date'];
  	}
  	$legalEntity_Id = $data1['legalentity_id'];
  	$stateID = $this->legalEntity->gettingStateID($legalEntity_Id);

  	$data1['add_in_tally'] = isset($data1['add_in_tally'])?$data1['add_in_tally']:1;
  	//$usersessionid  = $data1['payment_hidden_sessionid'];
  	$legalData =  $this->legalEntity->getLegalEntity($legalEntity_Id);
  	$business_legal_name = isset($legalData->business_legal_name) ? $legalData->business_legal_name : "";
  	$le_code = isset($legalData->le_code) ? $legalData->le_code : "";
  	$le_ledger_name = "";
  	if($le_code != "" && $business_legal_name != "")
  		$le_ledger_name = $business_legal_name . " - " . $le_code;


  	$stockistamount = $data1['payment_amount_stockist'];
  	$modeofpayment = $data1['mode_payment_type'];
  	$modeofpayment2 = $data1['mode_payment_type'];
  	if($modeofpayment == 16501){
  		$modeofpayment = 1;
  	 }else if($modeofpayment == 16503){
  	 	// credit note
  	 	$data1['add_in_tally'] = 0;
  		$modeofpayment = 2;
  		$data1['paid_through_stockist'] = $le_ledger_name;
  	 }else if($modeofpayment == 16504){
  	 	// debit note
  	 	$data1['add_in_tally'] = 0;
  	 	$data1['paid_through_stockist'] = $le_ledger_name;
  		$modeofpayment = 3;
  	 }else{
  	 	$modeofpayment=0;
  	 }
  	//getlegalentityid
  	$userID = DB::select("select getLeParentUser($legalEntity_Id) AS User_ID");
    $userid = $userID[0]->User_ID;
  	//$url = env('H2HAxis_API');

  	   $data = [
				'PayUTRCode' => '',
				'TxnAmount' => $data1['payment_amount_stockist'],
				'TransmissionDate' => date('Y-m-d H-i-s', strtotime($data1['transmission_date'])), //'2017-03-12 05-45-01',
				'BeneName' => (isset($supplierInfo->sup_account_name) && $supplierInfo->sup_account_name != '') ? $supplierInfo->sup_account_name : 'null',
				'BeneAccNum' => (isset($supplierInfo->sup_account_no) && $supplierInfo->sup_account_no != '') ? $supplierInfo->sup_account_no : 'null',
				'BeneIFSCCode' => (isset($supplierInfo->sup_ifsc_code) && $supplierInfo->sup_ifsc_code != '') ? $supplierInfo->sup_ifsc_code : 'null',
				'BeneBankName' => (isset($supplierInfo->sup_bank_name) && $supplierInfo->sup_bank_name != '') ? $supplierInfo->sup_bank_name : 'null',
				'TxnReffIds' => \Session::get('legal_entity_id'),
				'ValueDate' => date('Y-m-d'),
				'TxnReffCode' => isset($data1['payment_ref']) ? $data1['payment_ref'] : 0,
				'LedgerGroup' => 'Bank Accounts',
				'LedgerAccount' => $data1['paid_through_stockist'],
				'CostCenter' => 'Z1R1D1',
				'CostCenterGroup' => 'Z1R1D1',
				'TxnToLegalID' => \Session::get('legal_entity_id'),
				'TxnToID' => \Session::get('legal_entity_id'),
				'PayType' => $data1['payment_type_stockist'],
				'PayForModule' => "Stockist Payment",
				'AutoInit' => 0,
				'CreatedBy' => \Session::get('userId'),
				'payment_from'=>$legalEntity_Id,
				'deposite_type'=>$modeofpayment,
				'state_code' =>isset($stateID->code)?($stateID->code):"TS",
               ];
                        $headers = array("cache-control: no-cache", "content-type: application/json", 'auth:E446F5E53AD8835EAA4FA63511E22');

                        //$response = Utility::sendcUrlRequest($url, $data, $headers);
                        $response = $this->h2hAxisAPIController= new h2hAxisAPIController();
                        $request = new Request();
                        $response = $this->h2hAxisAPIController->sendPaymentRequestToAxis($request, $data);

            	$p_pay_id = $response['p_pay_id'];
            	$comment = "Amount received vide ref no: ".$data['TxnReffCode']." (".$data['TransmissionDate'].")";
            	$transaction_type = 143002;

            	if($modeofpayment == 3){
            		$comment = "Amount deducted by Debit Note vide ref no: ".$data['TxnReffCode']." (".$data['TransmissionDate'].")";
            		$transaction_type = 143001;
               		$this->legalEntity->deductthecashlimit($userid,$stockistamount,$legalEntity_Id);
            	}else{
            		if($modeofpayment == 2)
            			$comment = "Amount added by Credit Note vide ref no: ".$data['TxnReffCode']." (".$data['TransmissionDate'].")";
               		$this->legalEntity->updatethecashlimit($userid,$stockistamount,$legalEntity_Id);
            	}


               	// add into history table 
				$this->legalEntity->addcashbackdata($userid,$stockistamount,$legalEntity_Id,$modeofpayment2,$p_pay_id,$transaction_type,$comment);
               	// this function is used for save voucherentry
               	$data1['pay_code'] = $response['response'];
               	if($data1['add_in_tally'] == 1)
           			$this->legalEntity->savePaymnetsInVouchertable($data1);
  		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}			
	}

// this function is used for transaction history
public function getTransactionhistory($legalEntityID,Request $request){
  	$makeFinalSql = array();			

			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }
		     // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pay_code", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ledger_account", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for phone_no
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("txn_reff_code", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for email
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Created_By", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Mode_Type", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for pincode
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("transaction_date", $filter,true);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pay_amount", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Created_At", $filter,true);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("from_date", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("to_date", $filter);
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
   		  	$userdata =  $this->legalEntity->getPaymentHistory($makeFinalSql, $orderBy, $page, $pageSize,$legalEntityID);
            if ($userdata){
               return ['results' => $userdata['result'],'totalPayments' => $userdata['resultCount']];  	
            }
		   else{
		   	return ['Status'=>"No Data Found"];
		   }
  }

    public function validateMobileno() {
        try {
            $response = [ "valid" => false ];
            $data = Input::all();
            $mobileNo = isset($data['phone_number']) ? $data['phone_number'] : '';
            if($mobileNo != '')
            {
                $isMobileAvailable = DB::table('users')->where('mobile_no','=',$mobileNo)->count();
                if ($isMobileAvailable == 0)
                    $response = [ "valid" => true ];
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return json_encode($response);
    }
    
    public function uploadDoc(Request $request){
    	$data = $request->all();
    	$legalID = $data['le_id'];
    	$documentType = isset($data['documentType']) ? $data['documentType'] : '';
		$docName = DB::select(DB::raw("select description from master_lookup where value = ".$documentType.""));
		$docName = $docName[0]->description;
		$documentType =  DB::select(DB::raw("select master_lookup_name from master_lookup where value = ".$documentType.""));
		$documentType = $documentType[0]->master_lookup_name;
		$url = "";
    	$EntityType="products";
        $type=1;
     	if ($request->hasFile('upload_file')) {
            $extension = Input::file('upload_file')->getClientOriginalExtension();
            if(!in_array($extension, array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg','jfif'))) {
                return Response::json(array('status'=>400, 'message'=>Lang::get('inward.alertExtension')));
            }
			$imageObj = $request->file('upload_file');
            $url = $this->_productRepo->uploadToS3($imageObj,$EntityType,$type);
                if($url!='') {
                    $docsArr = array(
                        'legal_entity_id'=>$legalID,
                        'doc_name'=>$docName,
                        'doc_url'=>$url,
                        'doc_type'=>$documentType,
                        'created_by'=>Session('userId'), 
                        'created_at'=>date('Y-m-d H:i:s')
                    );
                    $doc_id=$this->legalEntity->upLoadPathInToDB($docsArr);
                    Session::push('ledocs_'.$legalID, $doc_id);
                    $docTypes = $this->legalEntity->getDocumentTypes();
                    $userInfo = $this->legalEntity->getLoginUserInfo();
                    $firstname = isset($userInfo->firstname)?$userInfo->firstname:'';
                    $lastname = isset($userInfo->lastname)?$userInfo->lastname:'';
                    $createdBy = $firstname.' '.$lastname;
                    
                    $docType = (isset($docTypes[$docsArr['doc_type']]))?$docTypes[$docsArr['doc_type']]:'';
                    $docText='<tr>
                            <td><input type="hidden" name="docs[]" value="'.$doc_id.'">'.$docType.'</td>
                            <td>'.$docsArr['doc_name'].'</td>
                            <td>'.$createdBy.'</td>
                            <td>'.$docsArr['created_at'].'</td>
                            <td align="center"><a href="'.$url.'" target="_blank"><i class="fa fa-download"></i></a></td>
                            <td align="center">
                            <a class="delete le-del-doc" id="'.$doc_id.'" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
                            </td>
                        </tr>';
                    return Response::json(array('status'=>200, 'message'=>Lang::get('inward.successUploaded'),'docText'=>$docText,'doc_type' => $documentType));
                }
            }
            else {
                return Response::json(array('status'=>200, 'message'=>Lang::get('salesorders.errorInputData')));
            }
    }

    public function deleteDoc(){
      	try {
            $id = Input::get('id');
            $docArr = $this->legalEntity->getDocumentById($id);
           	$leId = isset($docArr->legal_entity_id) ? $docArr->legal_entity_id : '';
          	$filename = isset($docArr->doc_url) ? $docArr->doc_url : '';
            if(!empty($filename) && file_exists(public_path().'/'.$filename)) {
                unlink(public_path().'/'.$filename);
            }

            $this->legalEntity->deleteRecord($id);
            $sessioncheck = Session::get('ledocs_'.$leId) ;
                if(is_array($sessioncheck)) {    
                    Session::put('ledocs_'.$leId, array_diff(Session::get('ledocs_'.$leId), [$id]));
            }
            return Response::json(array('status'=>200, 'message'=>Lang::get('inward.successDelete'),'doc_type' => $docArr->doc_type));
        } catch (Exception $e) {
            return Response::json(array('status'=>200, 'message'=>'Failed'));
        }
    }
    public function legalentityDetailsView($id){
	// $userId = Session::put('getting_userInfo',$id);
   	$details = $this->legalEntity->editGridId($id);
  	$state =$this->legalEntity->getStates();
  	$country = $this->legalEntity->getCountries();
  	$view = 'view';
  	// $details[0]->legal_entity_id
  	$stockistpaymentdetails = $this->legalEntity->getPaymentDetailsFromView($id);
    $legalDoc = $this->legalEntity->legalEntityDoc($id);
  	$creditlimiteditfeature=$this->_roleRepo->checkPermissionByFeatureCode('CRTLTDETD001');
  	$creditlimiteditView=$this->_roleRepo->checkPermissionByFeatureCode('CRTLTGDTD001');
  	$updateFeature=$this->_roleRepo->checkPermissionByFeatureCode('DCFCUPDATE001');
  	// The below two lines split "keys" and "values" and it for the Orders tab 
  	if(!empty($stockistpaymentdetails)){
  		$stockistpaymentkeys = array_keys((array) $stockistpaymentdetails[0]);
  	$paymentDetails = array_combine($stockistpaymentkeys, (array) $stockistpaymentdetails[0]);
	
  	} 
  	$paymentGrid = $this->_roleRepo->checkPermissionByFeatureCode('LGRGRD001');
  	parent::Title('Edit DC/FC -  '.' '. ($details[0]->business_legal_name));
  	$paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type', [2, 3]);
	$modeofpayment = DB::table('master_lookup')->whereIn("value",[16503,16504])->get();
	$docTypes = $this->legalEntity->getDocumentTypes();


	return view('LegalEntity::legalentitydetails',[
		'details'=>$details,
		'paymentType'=>$paymentType,'modeofpayments'=>$modeofpayment,
		'state'=>$state,
		'country'=>$country,
		'userlegalentityid'=>$details[0]->legal_entity_id,
		'userid'=>$id,
		'legalDoc'=>$legalDoc,
        'le_id' =>$id,
		'paymentDetails' => isset($paymentDetails) ? $paymentDetails : 0,'creditlimiteditfeature'=>$creditlimiteditfeature,'view'=>$view,'updateFeature'=>$updateFeature,'creditlimiteditView'=>$creditlimiteditView,'paymentGrid'=>$paymentGrid,'docTypes'=>$docTypes]);
  }

  public function getCitiesByStateId(){
  	$data=Input::all();
    $stateid=$data['state_id'];
  	$getcities=$this->legalEntity->getCitiesForStates($stateid);
  	$getcities=json_decode(json_encode($getcities),true);
    $resreturn='<option value="">Select</option>';
  	 for($l=0;$l<count($getcities);$l++) {
           
               $resreturn.='<option value="'.$getcities[$l]['scc_id']. '"> '.$getcities[$l]['city_name'].'</option>';
          }
      return $resreturn;    
  }

  public function getDcFcCode(){
   
     $data=Input::all();
    
     $getdcfccode=$this->legalEntity->getCodeForDcFc($data);
     $getdcfccode=json_decode(json_encode($getdcfccode),true);
     $getdcfccode=isset($getdcfccode[0])?$getdcfccode[0]['DC/FC Code']:'';
     if($data['dcs']!=''){
	     $fcDccode= $this->legalEntity->getFcDcs($data['dcs']);
	     $fcDccode=json_decode(json_encode($fcDccode),true);
     	$getdcfccode=$fcDccode[0]['le_wh_code'].'-'.$getdcfccode;
     }
  	  $isunique = $this->getWarehouseValidator($getdcfccode);
  	  if($isunique){
  	  	return json_encode(array('status'=>200,'code'=>$getdcfccode));
  	  }else{
  	  	return json_encode(array('status'=>400,'code'=>$getdcfccode));
  	  }
  
  }

  public function getLegalentityIdforDc(){

     $data=Input::all();
    
     $getlentityid=$this->legalEntity->getLegalentityForDc($data['dcs']);

     $leid=json_decode(json_encode($getlentityid),true);

     $leid=isset($leid[0])?$leid[0]['legal_entity_id']:'';

     return $leid;
  }


  public function getCityName(){

     $data=Input::all();
    
     $cityname=$this->legalEntity->getCityName($data['city_state_id']);

     $ctyname=json_decode(json_encode($cityname),true);

     $ctyname=isset($ctyname[0])?$ctyname[0]['city_name']:'';

     return $ctyname;
  }

  public function creditLimitApproval(){


   try{
  	$data=Input::all();

  	$creditlimit=$this->legalEntity->stockistCreditLimitInsert($data);

  	echo $creditlimit;
  }catch(Exception $e) {
		  return 'Message: ' .$e->getMessage();
		}
  }
  public function creditDebitApproval(){
  	try{
  		$data=Input::all();
  		$creditlimit=$this->legalEntity->stockistCreditDebitInsert($data);
  		echo $creditlimit;
  	}catch(Exception $e) {
		return 'Message: ' .$e->getMessage();
	}
  }
  public function getCreditDebitDetails($user_id) {
        try {
            $userdata = DB::table('credit_debit_note')
                        ->where('cdID',$user_id)
                        ->select('approval_status','amount','mode_of_deposit',DB::raw('IF(approval_status=1,getMastLookupValue(57199),getMastLookupValue(approval_status)) as approval_status_name'),'business_legal_name')->first();
            if($userdata->mode_of_deposit == 16503)
            	$userdata->mode_of_deposit = 'Credit Note';
            else
            	$userdata->mode_of_deposit = 'Debit Note';
            return $userdata;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
  public function creditDetails($Id) {
        try {
            $creditdebitDetails = $this->getCreditDebitDetails($Id);
            $business_legal_name = DB::table('legal_entities')->where('legal_entity_id',$creditdebitDetails->business_legal_name)->select('business_legal_name')->get();
            $creditdebitDetails->business_legal_name = $business_legal_name[0]->business_legal_name;
            $status = isset($creditdebitDetails->approval_status)?$creditdebitDetails->approval_status:'';
            $approval_flow_func = new CommonApprovalFlowFunctionModel();
            if($status=='' || $status==0){
                $status=57208;
            }
            $module = 'Credit or Debit Note';
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails($module, $status, \Session::get('userId'));
            $approvalOptions = array();
            $approvalVal = array();
            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                foreach ($res_approval_flow_func["data"] as $options) {
                    $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep']] = $options['condition'];
                }
            }
            $approvalVal = array('current_status' => $status,
                'approval_unique_id' => $Id,
                'approval_module' => $module,
                'table_name' => 'credit_debit_note',
                'unique_column' => 'business_legal_name',
                'approvalurl' => '/legalentity/approvalSubmit',
            );
            return View('LegalEntity::creditdebitnote')
                    ->with('creditdebitDetails', $creditdebitDetails)
                    ->with('approvalOptions', $approvalOptions)
                    ->with('approvalVal', $approvalVal);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function approvalSubmit() {
        try {
            $data = input::get();
            $approval_unique_id = $data['approval_unique_id'];
            $approval_status = $data['approval_status'];
            $approval_module = $data['approval_module'];
            $current_status = $data['current_status'];
            $approval_comment = $data['approval_comment'];
            $table = $data['table_name'];
            $unique_column = $data['unique_column'];
            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            $status = explode(',',$approval_status);
            $nextStatus = $status[0];
            $is_final = DB::table('appr_workflow_status_details')->where('awf_status_to_go_id',$nextStatus)->select('is_final')->first();
            $is_final = $is_final->is_final;
            if($is_final == 1){
	            $main = DB::table('credit_debit_note')->where('cdID',$data['approval_unique_id'])->get();
	            $main = json_decode(json_encode($main),1);
	            $main = $main[0];
	            $id = DB::table('users')->select('legal_entity_id')->where('user_id',$main['business_legal_name'])->first();
	            $main['legalentity_id'] =$main['business_legal_name'];
	            $main['payment_amount_stockist'] = $main['amount'];
	            $main['mode_payment_type'] = $main['mode_of_deposit'];
	            $main['payment_ref'] = isset($main['ref_no'])?$main['ref_no']:"";
	            $main['payment_type_stockist'] = isset($main['transaction_type'])?$main['transaction_type']:"";
	            $main['paid_through_stockist'] = '';
	            $req = new Request();
	            $savestockist = $this->saveStockistDetails($req,$main);
            }
            //else{
                 // $this->_poModel->updateStatusAWF($table,$unique_column,$approval_unique_id, $approval_status);
                 DB::table('credit_debit_note')->where('cdID',$approval_unique_id)
                 		->update(['approval_status'=>$nextStatus]);
            //}
            $approval_flow_func->storeWorkFlowHistory($approval_module, $approval_unique_id, $current_status, $nextStatus, $approval_comment, \Session::get('userId'));
            $response = array('status'=>200,'message'=>'Success');
            return json_encode($response);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
  public function generateCostcenter(Request $request){
    	try{
    	$data = $request->input();
    	$result = $this->legalEntity->generatingCostCenter($data);
    	return $result;
    }catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}
  }


  public function approvedCreditLimit(){
  	try{
  		//this function is in no longer use,hadn't deleted this function as we might use this function in future
  		/*$data=Input::all();
  		$userid=$data['userid'];
  		$leid=$data['leid'];
  		$getcreditdetails=$this->legalEntity->getApprovedCreditLimit($userid,$leid);
  		$getcreditdetails=json_decode(json_encode($getcreditdetails),true);*/
  		$getcreditdetails=0;//initialised to 0 since requirement has changed  we are not showing total creditlimit 
       return $getcreditdetails;
  	}catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}
  }
  public function deletePayment($pay_id) {
        try {
            $msg=$this->legalEntity->deletePayment($pay_id);
            if($msg){
            	$success_msg='Payment deleted successfully';
              return json_encode(['status'=>'200','message'=>$success_msg]);
            }else{
            	 $success_msg='Payment record cannot be deleted';
            	return json_encode(['status'=>'200','message'=>$success_msg]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
  public function getStockistLedger($legalentityId,Request $request){
  			$makeFinalSql = array();			

			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }

		     // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("transaction_date", $filter,true);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		   	$fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_at", $filter,true);
		   	$fieldQuery = str_replace("created_at", "eth.created_at", $fieldQuery);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("comment", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for phone_no
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("reference_no", $filter);
		    $fieldQuery = str_replace('reference_no', '(CASE WHEN order_id IS NOT NULL THEN gds_orders.`order_code` 
         				WHEN order_id IS NULL THEN pd.`pay_code` END)', $fieldQuery);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    // make sql for email
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("dr_amount", $filter);
		    $fieldQuery = str_replace('dr_amount', "(CASE WHEN transaction_type=143001 THEN cash_back_amount ELSE '' END)", $fieldQuery);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("cr_amount", $filter);
		    $fieldQuery = str_replace('cr_amount', "(CASE WHEN transaction_type=143002 THEN cash_back_amount ELSE '' END)", $fieldQuery);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("balance_amount", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("from_date", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("to_date", $filter);
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
   		  	$resultQuery =  $this->legalEntity->getPaymentLedger($makeFinalSql, $orderBy, $page, $pageSize,$legalentityId);
            if ($resultQuery){
               return $resultQuery;  	
            }
		   else{
		   	return ['Status'=>"No Data Found"];
		   }
  }
  public function exportData(Request $request){
  	$filterData = $request->all();
	$fdate = (isset($filterData['from_date_ledger']) && !empty($filterData['from_date_ledger'])) ? $filterData['from_date_ledger'] : date('Y-m-d');
	$fdate = str_replace('/', '-', $fdate);
	$fdate = date('Y-m-d', strtotime($fdate));
	$tdate = (isset($filterData['to_date_ledger']) && !empty($filterData['to_date_ledger'])) ? $filterData['to_date_ledger'] : date('Y-m-d');
	$tdate = str_replace('/', '-', $tdate);
	$tdate = date('Y-m-d', strtotime($tdate));
  	$legalentityID = isset($filterData['legalentity_id_ledger']) ? $filterData['legalentity_id_ledger'] :0;																
  	$result = json_decode(json_encode($this->legalEntity->exportDataDownload($legalentityID,$fdate,$tdate)),true);
  	Excel::create('Payment Ledger - '. date('Y-m-d'),function($excel) use($result) {
            $excel->sheet('Payment Ledger', function($sheet) use($result) {          
            $sheet->fromArray($result);
            });      
        })->export('xls');
  }

  	public function updateBalanceAmount($legal_entity_id,$update=0){
  		$input = Input::all();
  		$from_date = isset($input['from_date']) ? $input['from_date'] : "";
  		$to_date = isset($input['to_date']) ? $input['to_date'] : "";
  		$add_query = "";
  		if($from_date != "")
  			$add_query = "and created_at>='$from_date'";

  		if($to_date != "")
  			$to_date = "and created_at<='$to_date'";

  		if($from_date != "" && $to_date != "")
  			$add_query = "and created_at between '$from_date' and '$to_date'";
        $data = DB::select(DB::raw("SELECT * FROM ecash_transaction_history WHERE legal_entity_id IN ($legal_entity_id) $add_query AND is_deleted=0"));
        $cashback = 0;
        $balance_amount = 0;
        $table_data = "<table><tr><th>S no<th/> <th>Created Date<th/> <th>Trans. Date<th/>   <th>Type<th/> <th>Amount<th/> <th>Balance<th/></tr>";
        foreach ($data as $key => $value) {
            # code...
            $cashback = $value->cash_back_amount;
            if($value->transaction_type == 143001){
                $balance_amount = $balance_amount - $cashback ;
                $text = "<span style='color:red'>Debit</span>";
            }else{
                $balance_amount =   $balance_amount + $cashback;
                $text = "<span style='color:green'>Credit</span>";
            }
            if($update == 1){
        		$data = DB::table('ecash_transaction_history')
                    ->where('ecash_transaction_id', $value->ecash_transaction_id)
                    ->update(['balance_amount' => $balance_amount]);
            }
        	$table_data .= "<tr><td>$key<td/> 
        					<td>$value->created_at<td/> 
        					<td>$value->transaction_date<td/> 
        					<td>$text</td> 
        					<td></td>
        					<td align='right'>".round($value->cash_back_amount,2)."<td/> 
        					<td align='right'>".round($balance_amount,2)."<td/> </tr>";
            //print_r("S No ".$key."  ".$text ." = ".$value->cash_back_amount .",Balance = ".$balance_amount."</br>");
        }
        $table_data .= "</tbody></table";
        print_r($table_data);die;
    }

    public function getCreditHistroy($legalentityId,Request $request){
  			$makeFinalSql = array();			

		    $filter = $request->input('$filter');

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("From_Date", $filter,true);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("To_Date", $filter,true);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("description", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $filter=str_replace('Requested_Amount', 'Rqstd_Amount', $filter);
		   	$fieldQuery = $this->objCommonGrid->makeIGridToSQL("Rqstd_Amount", $filter);
		   	$fieldQuery =str_replace('Rqstd_Amount', 'Requested_Amount', $fieldQuery);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("STATUS", $filter);
		    $fieldQuery = str_replace("STATUS", "CONVERT(STATUS USING utf8)", $fieldQuery);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("from_date", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		   	$fieldQuery = $this->objCommonGrid->makeIGridToSQL("to_date", $filter);
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
   		  	$resultQuery =  $this->legalEntity->getCreditLimitHistory($makeFinalSql, $orderBy, $page, $pageSize,$legalentityId);
            if ($resultQuery){
               return $resultQuery;  	
            }
		   else{
		   	return ['Status'=>"No Data Found"];
		   }
  }
  public function rmvCreditLimit($user_ecash_details_id){
  	$data = DB::table('user_ecash_credit_details')->where('user_ecash_details_id',$user_ecash_details_id)->get();
    $data = json_decode(json_encode($data),1);
    $data = $data[0];
  	$update = DB::table('user_ecash_creditlimit')
  				->where('user_ecash_id',$data['user_ecash_id'])
  				->where('le_id',$data['le_id'])
  				->where('user_id',$data['user_id'])
  				->decrement('creditlimit',$data['amount_requested_to_approve']);
  	$status = DB::table('user_ecash_credit_details')
  				->where('user_ecash_id',$data['user_ecash_id'])
  				->where('le_id',$data['le_id'])
  				->where('user_id',$data['user_id'])
  				->where('user_ecash_details_id',$data['user_ecash_details_id'])
  				->update(['updated_status' => 1]);
  	return 1;
  }
  public function editCreditLimit($user_ecash_details_id){
  	$dates = DB::table('user_ecash_credit_details')->where('user_ecash_details_id',$user_ecash_details_id)->get();
  	$dates = json_decode(json_encode($dates),1);
  	return $dates[0];
  }
   public function updateCredit(){
   	$data = Input::all();
  	$dates = DB::table('user_ecash_credit_details')
  				->where('user_ecash_details_id',$data['user_ecash_details_id'])
  				->update(['To_Date' => $data['to_date']]);
  	return 1;
  }
  public function getEmailValidator() {
    try {
        $response = [ "valid" => false ];
        $data = Input::all();
        $emailID = isset($data['email']) ? $data['email'] : '';
        if($emailID != '')
        {
            $isMobileAvailable = DB::table('users')->where('email_id','=',$emailID)->count();
            if ($isMobileAvailable == 0)
                $response = [ "valid" => true ];
        }
    } catch (\ErrorException $ex) {
        \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
    }
    return json_encode($response);
 }
   public function getWarehouseValidator($code) {
        try {
            $response = 0;
            if($code != '')
            {  
                $leCode =  DB::table('legalentity_warehouses')
                			->select(DB::raw('count(*) as le_code'))
                			->where('le_wh_code','=',$code)->count();
                if ($leCode==0)
                    $response =  1;
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return json_encode($response);
    }
	
	public function checkGstStateCode(Request $request) {
		$status = (\Utility::check_gst_state_code($request->gstin_number)) ? true : false;
		return json_encode(array('valid' => $status));
	} 
}

	

