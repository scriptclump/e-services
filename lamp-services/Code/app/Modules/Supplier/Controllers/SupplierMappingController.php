<?php

namespace App\Modules\Supplier\Controllers;
use App\Modules\Roles\Models\Role;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use App\Modules\Supplier\Models\SupplierModel;
use App\Modules\Supplier\Controllers\commonIgridController;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Supplier\Models\Suppliers;

use Session;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Log;
use DB;
use Redirect;
use Response;
class SupplierMappingController extends BaseController {

    public $atp_peyiod;
	
    public function __construct() {
        try {
             $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                return $next($request);
            });
            //For Raback
            $this->_roleRepo = new RoleRepo();
            $this->_supplierModel = new SupplierModel();
            
        
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


               
    public function supplierMapIndex(){
        $rolesObj= new Role();
        $supplierMapAccess = $this->_roleRepo->checkPermissionByFeatureCode('SUPMP01');   
        $inventory= new Inventory();
        $DataFilter= $rolesObj->getFilterData(11, Session::get('userId'));
        $options = json_decode(json_encode($inventory->filterOptions()), true);
        $options['suppler_list'] = $this->_supplierModel->getSuppliersForMapping();
        return View::make('Supplier::suppliermapping',["supplierMapAccess"=>$supplierMapAccess,'filter_options'=>$options]);
        
    }

    public function supplierMappingGrid(Request $request) {
        if (!Session::has('userId')) {
            return Redirect::to('/');
        }
        
        $supplierMapAccess = $this->_roleRepo->checkPermissionByFeatureCode('SUPMP01');   
        if(!$supplierMapAccess)
            return json_encode(array('results'=>[], 'TotalRecordsCount'=>0));

        $this->objCommonGrid = new commonIgridController();
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("legal_entity_name", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("le_wh_name", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("manf_name", $filter);
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

        return $this->_supplierModel->supplierMapGrid($makeFinalSql, $orderBy, $page, $pageSize);
    }


    public function addNewMapping(Request $request){
        $_POST = $request->input();
        $suppliermapping = array("legal_entity_id"=>$_POST['supp_name'],
            "manf_id"=>$_POST['manf_name'],
            "le_wh_id"=>$_POST['dc_name'],
            "status"=>1);

        if($_POST['supp_name'] =="" || $_POST['dc_name'] == "" || $_POST['manf_name'] == ""){
            return array("status"=>0,
            "message"=>"Select all inputs!",
            "data"=>[]);
        }

        $count = $this->_supplierModel->checkSupMapping($suppliermapping);
        if($count == 0){
            // making same brand and manufature status inactive
            $updateData = array("manf_id"=>$_POST['manf_name'],
            "le_wh_id"=>$_POST['dc_name']);
            $this->_supplierModel->updateMappingDB(array("status"=>0),$updateData);
            $supplier_id = $this->_supplierModel->addNewMappingDB($suppliermapping);
        }
        $status = 1;
        $message = "";
        if(isset($supplier_id) && $supplier_id > 0){
            $message = "Supplier mapped successfully";
        }else if(isset($supplier_id) && $supplier_id == 0){
            $message = "Unable to map supplier!";
        }else if($count > 0){
            $message = "Mapping already exist!";
            $status = 1;
        }

        $returnArray  = array("status"=>$status,
            "message"=>$message,
            "data"=>[]);
        return $returnArray;
    }

    public function updateSuppMaping(Request $request){
        $_POST = $request->input();
        $suppliermapping = array("legal_entity_id"=>$_POST['supp_name'],
            "manf_id"=>$_POST['manf_name'],
            "le_wh_id"=>$_POST['dc_name']);

        $count = $this->_supplierModel->checkSupMapping($suppliermapping);
        if($count == 1){
            return array("status"=>0,
            "message"=>"Mapping already exist!",
            "data"=>[]);
        }
        if($_POST['supp_name'] =="" || $_POST['dc_name'] == "" || $_POST['manf_name'] == ""){
            return array("status"=>0,
            "message"=>"Select all inputs!",
            "data"=>[]);
        }
        
        $map_id = $_POST['map_id'];
        $wheredata = array("map_id"=>$map_id);
        $supplier_id = $this->_supplierModel->updateMappingDB($suppliermapping,$wheredata);
        $status = 1;
        $message = "";
        if($supplier_id){
            $message = "Updated successfully";
        }else{
            $message = "Unable to update supplier!";
        }

        $returnArray  = array("status"=>$status,
            "message"=>$message,
            "data"=>[]);
        return $returnArray;

    }

    public function deleteSupplierMapping($map_id){
        $map_id = $this->_supplierModel->deleteSupplierMappingDB($map_id);
        $status = 1;
        $message = "";
        if($map_id){
            $message = "Deleted successfully";
        }else{
            $message = "Unable to delete supplier!";
        }

        $returnArray  = array("status"=>$status,
            "message"=>$message,
            "data"=>[]);
        return $returnArray;
    }

    public function getSupplierMapping($map_id){
        $map_data = $this->_supplierModel->getSupplierMappingDB($map_id);
        $map_data = $map_data[0];
        $data['manf_name'] = $map_data->manf_id;
        $data['supp_name'] = $map_data->legal_entity_id;
        $data['le_wh_name'] = $map_data->le_wh_id;
        return $data;
    }

    public function changeMapStatus($map_id,$status){
        
        if($status == 'true'){
            $map_status = 1;
            $mappingdata = $this->_supplierModel->getSupplierMappingDB($map_id);
            // making status inactive for all remaining same warehouse and same brand
            $updateData = array("le_wh_id"=>$mappingdata[0]->le_wh_id,
                "manf_id"=>$mappingdata[0]->manf_id);
            $this->_supplierModel->updateMappingDB(array("status"=>0),$updateData);
        }else{
            $map_status = 0;
        }

        $data = array("status"=>$map_status);
        $wheredata = array("map_id"=>$map_id);
        $map_id = $this->_supplierModel->updateMappingDB($data,$wheredata);
        $status = 1;
        $message = "";
        if($map_id){
            $message = "Updated successfully";
        }else{
            $message = "Unable to delete supplier!";
        }

        $returnArray  = array("status"=>$status,
            "message"=>$message,
            "data"=>[]);
        return $returnArray;   
    }
}