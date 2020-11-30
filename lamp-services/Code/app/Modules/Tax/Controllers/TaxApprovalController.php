<?php

namespace App\Modules\Tax\Controllers;

use View;
use Illuminate\Support\Facades\Session;
use Validator;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use Redirect;
use Title;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Modules\Tax\Models\TaxClass;
use App\Modules\Tax\Models\ClassTaxMap;
use App\Modules\Tax\Models\MasterLookUp;
use App\Modules\Tax\Models\Product;
use App\Modules\Tax\Models\Zone;
use App\Modules\Tax\Models\Country;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Notifications;
use UserActivity;
use Carbon\Carbon;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use DB;
class TaxApprovalController extends BaseController {

    public function __construct() {
        $this->_taxmap = new ClassTaxMap();
        $this->_approvalFlowMethod= new CommonApprovalFlowFunctionModel();
        $this->_product = new Product();
        $this->_masterlookup = new MasterLookUp();
        $this->_zone = new Zone();
        try {
            $this->middleware(function ($request, $next) {

                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                parent::Title('Tax Approval');
                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction($mappingId) {

        $mapId=$this->_taxmap->getMapIdForParentID($mappingId);
        $productDetails=[];
        $productApprovalOptions=[];
        $i=0;
        foreach($mapId as $parentArray){
            $details = $this->_taxmap->getProductTaxDetails($parentArray['map_id']);
            
            //if mapping ID was deleted showing the reject message
            if (empty($details)) {
                return View::make('Tax::taxApproval')->with(["rejected" => "This ticket has been rejected!"]);
            }
            $approvalStatusDetails = $this->_approvalFlowMethod->getApprovalFlowDetails('TAX', $details["mappingDetails"][0]["status"], Session::get('userId'));
            if (isset($approvalStatusDetails["data"])) {
                foreach ($approvalStatusDetails["data"] as $eachData) {
                    $approvalOptions[$eachData["nextStatusId"] . "," . $eachData["isFinalStep"] . "," . $eachData["condition"]] = $eachData["condition"];
                }
            } else {
                $approvalOptions = array();
            }
            $productDetails["productDetails"][$i]=$details['productDetails'][0];
            $productDetails["taxDetails"][$i]=$details["taxDetails"];
            $productDetails["mappingDetails"][$i]=$details['mappingDetails'][0]['status'];
            $productApprovalOptions[$i]=$approvalOptions;
    $productDetails['hsn_data']=isset($details["hsn_data"][0]) ? $details["hsn_data"][0] : [];
            $i++;

        }
        $j=0;
        foreach ($productDetails['taxDetails'] as $value) {
            $tax[$j++]=$value[0];
        }


        return View::make('Tax::taxApproval')->with(['productDetails' => $productDetails["productDetails"], "taxDetails" => $tax, "currentStatus" => $productDetails["mappingDetails"], "approvalOptions" => $approvalOptions, "hsn_data" => $productDetails["hsn_data"]]);
    }

    public function updateAction(Request $request) {
        $request_input = $request->input();
        $explode_nextstatus = explode(",", $request_input["next_status"]);
        $id="select map_id from tax_class_product_map where parent_id=". $request_input["mapping_id"] ." order by map_id desc";
        $id=DB::select(DB::raw($id));
        $id=json_decode(json_encode($id),true);
        $flag=true;
        foreach ($id as $value) {
            if($flag){
                $approval_comment = $this->_taxmap->checkStatus($value['map_id'], $request_input["next_status"], "approval");
                if($approval_comment=='same-effective-date-exists'){
                    $flag=false;
                }
            }
            
        }

        if($flag){
            foreach ($id as $value) {
                $approval_comment = $this->_taxmap->updateStatusAWF($value['map_id'], $request_input["next_status"], "approval");
            }
            if($approval_comment != "same-effective-date-exists"){
                $this->_approvalFlowMethod->storeWorkFlowHistory('TAX', $request_input["mapping_id"], $request_input["current_status"], $request_input["next_status"], $request_input["approval_comment"], Session::get('userId'));
            }
            //if the ticket is rejected, delete the tax mapping from mapping table
            if($explode_nextstatus[2] == "Rejected")
            {
                $mapping_details = $this->_taxmap->where('parent_id',$request_input["mapping_id"])->first();
                $mapping_details = json_decode(json_encode($mapping_details));
                $product_details = $this->_product->find($mapping_details->product_id);
                $this->_taxmap->deleteTaxClassMap($request_input["mapping_id"],'parent_id');
                $oldvalues = "";
                $newvalues = array("SKU" => $product_details->sku, "TAXCLASSCODE" => $product_details->tax_class_id, "PRODUCT_ID" => $product_details->product_id);
                $uniqueId  = array("Product_id" => $product_details->product_id);
                $mongodb = UserActivity::userActivityLog("TAXMAPPING", $newvalues, "TAXMAPPING was deleted from the Table tax_class_product_map, due to rejection in approval process", $oldvalues, $uniqueId);
                $approval_comment = "fine";
            }
        }

        return $approval_comment;
    }
    
    public function approvalDashboard($productId, $stateId) {
        $result["productDetails"] = json_decode(json_encode($this->_product->getProductDetailsofTaxClassCode($productId)), true)[0];
        $result["taxDetails"] = json_decode(json_encode($this->_taxmap->getAlltaxClassRulesbasedonProductID($productId, $stateId)), true);
        $result["productDetails"]["state_name"] = json_decode(json_encode($this->_zone->getStateName($stateId)), true)[0];
        
        foreach ($result["taxDetails"] as $eachKey => $eachValue) {
            if($eachValue["status"] != '1'){
                $result["taxDetails"][$eachKey]["status"] = $this->_masterlookup->getMasterLookupName($eachValue["status"])[$eachValue["status"]];
                $actionCode = json_decode(json_encode($this->_taxmap->getActionByStatus($eachValue["status"])), true);
                if($actionCode["message"] === "User does not have role"){
                    $result["taxDetails"][$eachKey]["action"] = "--";
                } else {
                    $result["taxDetails"][$eachKey]["action"] = "<a href='/tax/taxapproval/".$eachValue['map_id']."' class='btn btn-primary' target='_blank'>Approve</a>";
                }
            } else {
                $result["taxDetails"][$eachKey]["status"] = "Tax Approved";
                $result["taxDetails"][$eachKey]["action"] = "--";
            }
        }
        
        return View::make('Tax::taxApprovalDashboard')->with(['details' => $result]);
    }

    public function getApprovalWorkFlowStatusforTax()
    {
       $res_approval_flow_func             = $this->_approvalFlowMethod->getApprovalFlowDetails('TAX', 'drafted', \Session::get('userId'));
       return $res_approval_flow_func['status'];
    }
}