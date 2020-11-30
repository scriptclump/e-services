<?php

namespace App\Modules\CustomerFeedback\Controllers;
use App\Http\Controllers\BaseController;
use App\Modules\CustomerFeedback\Models\customersMov;
use App\Modules\Assets\Controllers\commonIgridController;
use App\Modules\Orders\Models\OrderModel;
use App\Central\Repositories\RoleRepo;
use App\Modules\Roles\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use View;
use Log;
Class customersMovController extends BaseController{
		protected $_roleModel;
		protected $_orderModel;
	public function __construct(){
		$this->objCommonGrid = new commonIgridController();
		$this->_roleModel = new Role();
		$this->customersMovObj = new customersMov();
		$this->_orderModel = new OrderModel();
	}
	public function index(){
	try{
		$customerDetails = $this->customersMovObj->customerType();
		// $dcDetails = $this->customersMovObj->dcTypesLegalentityWarehouses();
		    $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $allDc = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
            $filter_options['dc_data'] = $allDc;
		$stateCode = $this->customersMovObj->stateNames();
	 return View::make('CustomerFeedback::ecashIndex')
	            ->with(['customerDetails'=>$customerDetails,'filter_options'=>$filter_options,'stateCode'=>$stateCode]);
	}
	catch(Exception $e) {
	return 'Message: ' .$e->getMessage();
	 }
	}
	public function saveEcashCreditlimitUser(Request $request){
		$data =$request->all();
        $result = $this->customersMovObj->addDCToCustomers($data);
        if($result==0) {
        	return 2;
        }
        return $result;
	}
	public function ecashCreditlimitGrid(Request $request){
		    $userId = Session::get('userId');
		    $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $allDc = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
            $filter_options['dc_data'] = $allDc;
          	$dcIDS = json_decode(json_encode($allDc),true);
          	$keys = array_column($dcIDS, "le_wh_id");
          	$leIDS = implode(",", $keys);
	        $makeFinalSql = array();			
			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }

		    // make sql for customers name
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("master_lookup_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		     // make sql for dc name
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("lp_wh_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		     // make sql for state name

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("creditlimit", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("mov_ordercount", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("self_order_mov", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("state_name", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("minimum_order_value", $filter);
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
      return $data = $this->customersMovObj->ecashCreditlimitQuery($makeFinalSql, $orderBy, $page, $pageSize,$userId,$leIDS);
	}
	public function editecashCreditLimit($id){
		$data = $this->customersMovObj->editecashCreditLimit($id);
		return json_encode($data);
	}
	public function updateEcashLimit(Request $request){
	   $data = $request->all();
       $ecashId = $this->customersMovObj->ecashCreditLimitID($data);
       if($ecashId==0) {
    	 return 2;
        }
       return $ecashId;
	}
}



