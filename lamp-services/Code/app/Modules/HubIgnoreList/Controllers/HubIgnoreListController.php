<?php

namespace App\Modules\HubIgnoreList\Controllers;

use DB;
use Log;
use View;
use Session;
use Request;
use Redirect;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use App\Modules\HubIgnoreList\Models\HubIgnoreListModel;


class HubIgnoreListController extends BaseController{

	public function __construct(HubIgnoreListModel $hubIgnoreListObj, RoleRepo $roleAccess){
        try{
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('hubignorelist.hubignorelist_index.ignorelist_title'));
            // parent::__construct();
            
            $this->hubIgnoreListObj = $hubIgnoreListObj;
            $this->roleAccess = $roleAccess;
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                // All the code related to the session will come here
                return $next($request);
            });  
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }

	public function index()
	{
        try{

            parent::Breadcrumbs(array('Home' => '/', 'Administration' => '#', 'Ignore List' => '/ignorelist'));

            $checkAddPermission = false;
            $checkAddPermission = $this->roleAccess->checkPermissionByFeatureCode('ADDHUIG01');

            if($checkAddPermission)
            {
                $brandsInfo = $this->hubIgnoreListObj->getBrandInfo();

                $brandsInfoArr = array();
                if(isset($brandsInfo))
                    foreach($brandsInfo as $brand)
                        $brandsInfoArr[$brand->brand_id] = $brand->brand_name;

                $hubInfo = $this->hubIgnoreListObj->getHubInfo();

                $hubInfoArr = array();
                if(isset($hubInfo))
                    foreach($hubInfo as $hub)
                        $hubInfoArr[$hub->le_wh_id] = $hub->lp_wh_name;

                $dcInfo = $this->hubIgnoreListObj->getDcInfo();

                $dcInfoArr = array();
                if(isset($dcInfo))
                    foreach($dcInfo as $dc)
                        $dcInfoArr[$dc->le_wh_id] = $dc->lp_wh_name;

                $manufacturerInfo = $this->hubIgnoreListObj->getManufacturerInfo();

                $manufacturerInfoArr = array();
                if(isset($manufacturerInfo))
                    foreach($manufacturerInfo as $manufacturer)
                        $manufacturerInfoArr[$manufacturer->legal_entity_id] = $manufacturer->business_legal_name;

                $productsInfo = $this->hubIgnoreListObj->getProductInfo();

                $productsInfoArr = array();
                if(isset($productsInfo))
                    foreach($productsInfo as $product)
                        $productsInfoArr[$product->product_id] = $product->product_title;


                $beatInfo = $this->hubIgnoreListObj->getBeatInfo();

                $beatInfoArr = array();
                if(isset($beatInfo))
                    foreach($beatInfo as $beat)
                    {
                        if(isset($beat->spoke_name))
                            $beatInfoArr[$beat->beat_id] = $beat->beat_name ."(".$beat->spoke_name.")";
                        else
                            $beatInfoArr[$beat->beat_id] = $beat->beat_name;
                    }

                $spokeInfo = $this->hubIgnoreListObj->getSpokeInfo();

                $spokeInfoArr = array();
                if(isset($spokeInfo))
                    foreach($spokeInfo as $spoke)
                        $spokeInfoArr[$spoke->spoke_id] = $spoke->spoke_name;

        		return View::make('HubIgnoreList::index')
                            ->with("addPermission",true)
                            ->with("dcInfo",$dcInfoArr)
                            ->with("hubInfo",$hubInfoArr)
                            ->with("beatInfo",$beatInfoArr)
                            ->with("spokeInfo",$spokeInfoArr)
                            ->with("brandsInfo",$brandsInfoArr)
                            ->with("productInfo",$productsInfoArr)
                            ->with("manufacturerInfo",$manufacturerInfoArr);
            }
            else
                return View::make('HubIgnoreList::index')
                            ->with("addPermission",false);

        } catch (\ErrorException $ex) {
            
            echo "Sorry. The HubIgnoreList is failed to Load. Please Contact the Admin and Check the Log";
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
	}

    public function addNewHubIgnoreList()
    {
        $data = Input::all();

        // Log::info("DAta before data");       
        // Log::info($data);       
        $message="Adding of Data has been failed. Please Try Again";
        
        // Server Side Validation is done below....
        $refStatus = false;
        if(empty($data["ignoreRefType"]))
            $message = "Please Select a Brand or Manufacturer or Product to Ignore";
        else if(!is_array($data[$data["ignoreRefType"]."_id"]))
            $message = "Please Select a ".ucfirst($data["ignoreRefType"]);
        else
            $refStatus = true;

        $scopeStatus = false;
        if(empty($data["ignoreScopeType"]))
            $message = "Please Select a Dc or Hub or Beat or Scope to Ignore";
        else if(!is_array($data[$data["ignoreScopeType"]."_id"]))
            $message = "Please Select a ".ucfirst($data["ignoreScopeType"]);
        else
            $scopeStatus = true;

        // If all the valid Fields are valid. Then the insertion Starts...
        if($refStatus and $scopeStatus)
        {
            $successCount=0;
            $failIdsList = array();
            if(isset($data[$data["ignoreRefType"]."_id"]))
            foreach ($data[$data["ignoreRefType"]."_id"] as $ref_id) {
                if($ref_id != '' and isset($data[$data["ignoreScopeType"]."_id"]))
                    foreach ($data[$data["ignoreScopeType"]."_id"] as $scope_id) {
                        if($scope_id != ''){
                            $isDuplicate = false;
                            $status = false;
                            $isDuplicate = $this->hubIgnoreListObj->checkHubIgnore($ref_id,$data["ignoreRefType"],$scope_id,$data["ignoreScopeType"]);
                            if(!$isDuplicate)
                            {
                                $status = $this->hubIgnoreListObj->insertNewHubIgnore($ref_id,$data["ignoreRefType"],$scope_id,$data["ignoreScopeType"]);
                                if($status)
                                    $successCount++;
                            }
                            if($isDuplicate or !$status){
                                array_push($failIdsList, intval($ref_id));
                            }
                        }
                    }
            }
            $message = null;
            if($successCount)
                $message.= $successCount." ".ucfirst($data["ignoreRefType"])."(s) for ".ucfirst($data["ignoreScopeType"])."(s) had been <b>Successfully Added</b>.<br>";
            if(is_array($failIdsList) and !empty($failIdsList))
            {
                $itemsList = $this->hubIgnoreListObj->getItemsList($failIdsList,$data["ignoreRefType"]);
                if($itemsList != null)
                    $message.= implode(', ', $itemsList);
                $message.= " Item(s) had not been Added. <br><b>Reason:</b> Duplicate Data or Invalid Data. Please Try Again";
            }
        }
        return ["message" => $message];
    }

    public function viewHubIgnoreList()
    {
        try{
            $results = $this->hubIgnoreListObj->getHubIgnoreList();

            $deletePermission = false;
            $deletePermission = $this->roleAccess->checkPermissionByFeatureCode("DELHUIG02");

            foreach ($results["result"] as $record) {

                if($record->scope_type == "DC")
                {
                    $record->scope_type = "Dc";
                    $record->scope_name = $this->hubIgnoreListObj->getLeWhName($record->scope_id);
                }
                else if($record->scope_type == "HUB")
                {
                    $record->scope_type = "Hub";
                    $record->scope_name = $this->hubIgnoreListObj->getLeWhName($record->scope_id);
                }
                else if($record->scope_type == "SPOKE")
                {
                    $record->scope_type = "Spoke";
                    $record->scope_name = $this->hubIgnoreListObj->getSpokeName($record->scope_id);
                }
                else if($record->scope_type == "BEAT")
                {
                    $record->scope_type = "Beat";
                    $record->scope_name = $this->hubIgnoreListObj->getBeatName($record->scope_id);
                }

                if($record->ref_type == "brands")
                {
                    $record->ref_type = "Brand";
                    $record->ref_name = $this->hubIgnoreListObj->getBrandName($record->ref_id);
                }
                else if($record->ref_type == "manufacturers")
                {
                    $record->ref_type = "Manufacturer";
                    $record->ref_name = $this->hubIgnoreListObj->getManufacturerName($record->ref_id);
                }
                else if($record->ref_type == "beats")
                {
                    $record->ref_type = "Beat";
                    $record->ref_name = $this->hubIgnoreListObj->getBeatName($record->ref_id);
                }
                $record->action='';
                if($deletePermission)
                    $record->action.= '<span style="padding-left:20px;" ><a href="javascript:void(0);" onclick="deleteHubIgnoreListById('.$record->hbm_id.')"><i class="fa fa-trash-o"></i></span>';
            }

            return ["Records" => $results["result"], "TotRecKey" => $results["count"]];

        } catch (\ErrorException $ex) {
            echo "Sorry. The HubIgnoreList Grid is failed to Load. Please Contact the Admin or Check the Log";
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function deleteHubIgnoreList($id = null)
    {
        if($id != null)
        {
            $deleteHubIgnoreList = false;
            $deleteHubIgnoreList = $this->hubIgnoreListObj->deleteHubIgnoreListById($id);

            if($deleteHubIgnoreList)
                return ["status" => 1];
        }
        else
            return "Sorry, Something Went Wrong. Please Try Again";
    }
}
