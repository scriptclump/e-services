<?php
//business dashboard 
namespace App\Modules\FieldForce\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\FieldForce\Models\fieldForceDashboardModel;
use App\Modules\FieldForce\Controllers\commonIgridController;
use App\Central\Repositories\RoleRepo;
use Log;
use Illuminate\Http\Request;
use Response;
use Redirect;
use Session;
use Input;
use App\Modules\Roles\Models\Role;
use UserActivity;
use Notifications;
use DB;

class fieldForceDashboardController extends BaseController {
    private $objCommonGrid = '';

    public function __construct() {
        $this->_roleRepo = new RoleRepo();
        $this->fieldforce_dashboard = new fieldForceDashboardModel();
        $this->objCommonGrid = new commonIgridController();
    }

    public function fieldForceDashboard(){
    	try{
			$breadCrumbs = array('Home' => url('/'),'FieldForce' => '#', 'Dashboard' => '#');
			parent::Breadcrumbs($breadCrumbs);
            $access = $this->_roleRepo->checkPermissionByFeatureCode('FFT001');
            if (!$access) {
                Redirect::to('/')->send();
                die();
            }
        
            $loadmascatdata = $this->fieldforce_dashboard->getLoadMascatData();
            $showfieldforcedetails=$this->fieldforce_dashboard->fieldForceDetails();
            $addFFtarget = $this->_roleRepo->checkPermissionByFeatureCode('FFT004');
            $deleteFFtarget = $this->_roleRepo->checkPermissionByFeatureCode('FFT003');
			return view('FieldForce::fieldForceDashboard',['loadmascatdata'=>$loadmascatdata,'fieldforcedetails'=>$showfieldforcedetails,'addFlag'=>$addFFtarget,'deleteFFtarget'=>$deleteFFtarget]);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

    public function loadMascatData(){
        return $this->fieldforce_dashboard->loadmascatdata();
    }

    public function deleteFieldForce(Request $request){
        $deleteData = $request->input('deleteData');
        $this->fieldforce_dashboard->deleteFieldForceData($deleteData);

    }

    public function saveFieldForcedata(Request $request){

        $fieldforcedata = $request->input();
        $responseMessage = $this->fieldforce_dashboard->saveFieldForceData($fieldforcedata);
        $displayMSG = "";

        if($responseMessage==1){
            $displayMSG = "Target Name Already Exist";
        }else{
            $displayMSG = "Target Name Added Successfully";
        }
        return $displayMSG;
    }

    public function loadFieldForceData(Request $request){
        $data = $request->input('selectedData');
        $userid = $request->input('hiddenid');
        return $this->fieldforce_dashboard->loadFieldForceTarget($data,$userid);

    }

    public function getFieldForceData($ffid){
        //$deleteFFtarget = $this->_roleRepo->checkPermissionByFeatureCode('FFT003');
        return $this->fieldforce_dashboard->getfieldforceDetails($ffid);
    }

    public function getUserDetailsWithId($ffid){
        return $this->fieldforce_dashboard->getUserDetailsWithId($ffid);
    }

    public function showFieldForceDashboardDetails(Request $request){
        
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }        

         // make sql for Field force name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("FFFullName", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // make sql for current beat
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("BeatName", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // make sql for current beat
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("mobile_no", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

         // make sql for current beat
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("RMName", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // Process data for Status Filter
        $statusFilter = '';
        if($request->input('filterStatusType')!='all'){
            $statusFilter = $request->input('filterStatusType')!='' ? "status='".$request->input('filterStatusType')."'" : '';
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

        return $this->fieldforce_dashboard->showfieldforceDetails($makeFinalSql, $statusFilter, $orderBy, $page, $pageSize);
    }

 }
    
