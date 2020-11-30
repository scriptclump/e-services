<?php
namespace App\Modules\MFCcompany\Controllers;
use App\Http\Controllers\BaseController;
use App\Modules\MFCcompany\Models\MFCModel;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\Assets\Controllers\commonIgridController;
use Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use session;
use Input;
use DB;

class MFCCompanyController extends BaseController {

    private $microfinance;
	
    public function __construct(){

	 $this->microfinance = new MFCModel();
	 $this->objCommonGrid = new commonIgridController();
	}
	
	public function index(){
		try{
			$state =$this->microfinance->getStates();
			return view('MFCcompany::microfinance')->with('state',$state);
		}
		catch(Exception $e) {
		  return 'Message: ' .$e->getMessage();
		}
	}

	public function companyGridData(Request $request){
		try{
			
			$legalid = Session::get('legal_entity_id');
		    $makeFinalSql = array();			
			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }
		    // make sql for firstname
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("fullname", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("lastname", $filter);
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
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("address1", $filter);
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
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pincode", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for state_id
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("state_id", $filter);
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
			$legaldata =  $this->microfinance->GetAllCompanydata($makeFinalSql, $orderBy, $page, $pageSize,$legalid);
			$result = json_decode(json_encode($legaldata), true);
			return $legaldata;
		}catch(Exception $e) {
		  return 'Message: ' .$e->getMessage();
	  }
  }

    public function editGridDetails($id){
    	try{  
      	$details = $this->microfinance->editGridId($id);
    	$result = json_decode(json_encode($details), true);
    	return  isset($result[0]) ? $result[0] : [];
    }catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}
   }

    public function updateUsersInfo(Request $request){
    	try{
    	// $userdataid = Session::get('getting_userInfo');
     	$data = $request->input();
    	$updateintoalltables = $this->microfinance->upDateUsersInfo($data);
    	return $updateintoalltables;
    }catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}
  }

    public function updateCustomerInfo(Request $request){
    	try{
    	$data = $request->input();
    	$updateUser = $this->microfinance->registeredData($data);
    	return $updateUser;
    }catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}
  }

  public function companyDetailsInGrid($id){
    try{
   	$details = $this->microfinance->editGridId($id);
   	$id = json_decode(json_encode($details), true);   	

    if(isset($id[0]['user_id'])){
    	$userId = $id[0]['user_id'];
    	Session::put('getting_userId',$userId);
    }
   	
  	$state =$this->microfinance->getStates();
  	$country = $this->microfinance->getCountries();
	return view('MFCcompany::companydetails',['details'=>$details,'state'=>$state]);
  }catch(Exception $e) {
	  return 'Message: ' .$e->getMessage();
	}

 }

  public function getUsersList(Request $request){
        $getUser_id = session::get('getting_userId');
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
		    // $fieldQuery = $this->objCommonGrid->makeIGridToSQL("otp", $filter);
		    // if($fieldQuery!=''){
		    //     $makeFinalSql[] = $fieldQuery;
		    // }
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
   		  $userdata =  $this->microfinance->GetAllUsersData($makeFinalSql, $orderBy, $page, $pageSize,$getUser_id);
		 return json_encode(array('results'=>$userdata)); 
  } 
  public function CreatingUsers(Request $request){

   	    $data = $request->all();
  	
   		$result =  $this->microfinance->saveUsersData($data);
   		if($result ==1){
   			$status=1;
   			$message ="Successfully Created";
   		}else{
   			$status=0;

   			$message = "Somethink Went Wrong";
   		}
    
    $returnarray = array("status"=>$status,
					"message"=>$message);

    return $returnarray;
  }

  public function getUsersData($id){
  	
    $usersData = $this->microfinance->getUsersDataGrid($id);

    return $usersData;
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
}
	

