<?php
//business dashboard 
namespace App\Modules\BusinessUnit\Controllers;
use App\Http\Controllers\BaseController;
use App\Modules\BusinessUnit\Models\businessUnitDashboardModel;
use App\Central\Repositories\RoleRepo;
use Log;
use Illuminate\Http\Request;
use Response;
use Redirect;
use Session;
use Input;
use App\Modules\Roles\Models\Role;
use Notifications;
use DB;
use \App\Modules\Users\Models\Users;

class businessUnitDashboardController extends BaseController {

    public $businessUnit = '<option value="0">Please Select  ....</option>';
    private $deleteBusinessUnitAccess=1;
    private $updateBusinessUnitAccess=1;

	public function __construct(){

		$this->objBusinessDashboardModel = new businessUnitDashboardModel();
        $this->_roleRepo = new RoleRepo();
        $this->middleware(function ($request, $next) {

            $access = $this->_roleRepo->checkPermissionByFeatureCode('BUS001');

            // Get button lable access
            if(Session::get('legal_entity_id')!=0){
                $this->deleteBusinessUnitAccess = $this->_roleRepo->checkPermissionByFeatureCode('BU0003');
            }
            if(Session::get('legal_entity_id')!=0){
                $this->updateBusinessUnitAccess = $this->_roleRepo->checkPermissionByFeatureCode('BU0002');
            }

            if (!$access && Session::get('legal_entity_id')!=0) {
                Redirect::to('/')->send();
                die();
            }
            return $next($request);

        });
	}

	public function saveEditBusinessTreeData(Request $request){
        $displayMSG = 'No Response!';
        try{
    		$businessdata = $request->input();
    		$businessId = $request->input("add_business_id");

    		$responseMessage = $this->objBusinessDashboardModel->saveBusinessData($businessdata,$businessId);

            if($responseMessage==1){
                $displayMSG = trans('business_unit_eng.UI_BUSINESS_ADD_MSG');
            }elseif($responseMessage==2){
            	$displayMSG = trans('business_unit_eng.UI_BUSINESS_UPDATE_MSG');
            }elseif ($responseMessage==3) {
                $displayMSG = "Business Name already exist!";
            } else{
                $displayMSG = trans('business_unit_eng.UI_NO_RESPONSE_MSG');
            }

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

        return $displayMSG;

	}

	 public function getUpdateBusinessData($updateBusinessID){
        return  $this->objBusinessDashboardModel->getBusinessID($updateBusinessID);
    }

    public function loadBusinessUnitData(){
        $parentData = $this->objBusinessDashboardModel->getParentDetails();
        return $parentData;
    }

    public function getBusinessUnitList($updateBusinessID){
       $this->getChildBusinessLevels(0, 1, $updateBusinessID);
       return $this->businessUnit;
    }


    public function getChildBusinessLevels($bu_id,$level, $updateBusinessID){
        try{
            $buData =DB::table('business_units')
                    ->where('business_units.parent_bu_id', "=", $bu_id)
                    ->where('business_units.bu_id', "!=", $updateBusinessID)
                    ->where('is_active','1')
                    ->get()->all(); 
            
           if (!empty($buData)) 
            {
                foreach($buData as  $buRow)
                { 
                    $css_class='';
                    switch ($level) {
                        case 1:
                            $css_class='parent_cat';
                            break;
                        case 2:
                            $css_class='sub_cat';
                            break;
                        case 3:
                            $css_class='prod_class';
                            break;

                        default:
                            $css_class='prod_class_'.$level;
                            break;
                    }

                    $this->businessUnit.= '<option value="'.$buRow->bu_id.'" class="'.$css_class.'" > '.$buRow->bu_name.'</option>';
                    $this->getChildBusinessLevels($buRow->bu_id, $level+1, $updateBusinessID);
                }
            }
            return $this->businessUnit;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function businessUnitDashboard(){

    	try{
			$breadCrumbs = array('Home' => url('/'),'Business' => '#', 'Dashboard' => '#');
			parent::Breadcrumbs($breadCrumbs);

			$parentData = $this->objBusinessDashboardModel->getParentDetails();
            $users = new Users();
            $businessUnitsData=$users->getBusinesUnitData();
            //echo "<pre/>";print_r($parentData);
            Notifications::addNotification(['note_code' =>'BU001']);
            $addBusinessUnitAccess=1;
            if(Session::get('legal_entity_id')!=0){
                $addBusinessUnitAccess = $this->_roleRepo->checkPermissionByFeatureCode('BU0001');
            }
			return view('BusinessUnit::businessUnitDashboard',['parentData' => $parentData,'addBusinessUnitAccess' => $addBusinessUnitAccess,'businessUnitsData'=>$businessUnitsData ]);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function deleteBusinessTreeData(Request $request){

		$deleteData = $request->input('deleteData');
        Notifications::addNotification(['note_code' =>'BU003']);
        $this->objBusinessDashboardModel->deleteBusinessTreeData($deleteData);

	}

	public function businessTreeData(){
        try{
    		$allBusinessUnits = $this->objBusinessDashboardModel->allBusinessUnits();
            $allBusinessUnits = json_decode($allBusinessUnits,true);

    		$finalArr = array();
            $parentWiseArr = array();
            
            
            foreach($allBusinessUnits as $key=>$businessData){
            	if($businessData['parent_bu_id'] == 0){
    	            $parentWiseArr[$businessData['bu_id']]['bu_id']      		= $businessData['bu_id'];
    	            $parentWiseArr[$businessData['bu_id']]['bu_name']         	= $businessData['bu_name'];
    	            $parentWiseArr[$businessData['bu_id']]['description']       = $businessData['description'];
                    $parentWiseArr[$businessData['bu_id']]['parent_name']       = $businessData['parent_name'];
    	            $parentWiseArr[$businessData['bu_id']]['is_active']        	= ($businessData['is_active']==1)?'Active':'Inactive';
                    $parentWiseArr[$businessData['bu_id']]['cost_center']       = $businessData['cost_center'];
                    $parentWiseArr[$businessData['bu_id']]['tally_company_name']       = $businessData['tally_company_name'];
                    $parentWiseArr[$businessData['bu_id']]['sales_ledger_name']       = $businessData['sales_ledger_name'];
                    $parentWiseArr[$businessData['bu_id']]['actions']           = "";

                    if($this->updateBusinessUnitAccess == 1){
                        $parentWiseArr[$businessData['bu_id']]['actions'] = '<a data-type="edit" data-id="' . $businessData['bu_id'] . '" data-toggle="modal"  onclick="editBusinessData('.$businessData['bu_id'].');"><i class="fa fa-pencil"></i></a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
                    }
                    
                    unset($allBusinessUnits[$key]);
                    $child = $this->getNextBusinessChild($businessData['bu_id'], $allBusinessUnits);

                    if(!empty($child)){
                        $parentWiseArr[$businessData['bu_id']]['businessChild'] = $child;
                    }else{
                        if($this->deleteBusinessUnitAccess == 1){
                            $parentWiseArr[$businessData['bu_id']]['actions'] .= '<a data-type="edit" data-id="' . $businessData['bu_id'] . '"  onclick="deleteBusinessData('.$businessData['bu_id'].');" ><i  class="fa fa-trash-o" ></i></a>';
                        }
                    }
                }
            }

            foreach($parentWiseArr as $value){
                    $finalArr[] = $value;
            }
            echo json_encode($finalArr);
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

	}

	public function getNextBusinessChild($catId, $businessArr){
        try{
            $collectChild = array();

            $temp = array();
            if(!empty($businessArr)){
                foreach($businessArr as $key=>$value){
                    if($value['parent_bu_id']==$catId){
                        unset($temp);
                        $temp['bu_id']              = $value['bu_id'];
                        $temp['bu_name']            = $value['bu_name'];
                        $temp['description']        = $value['description'];
                        $temp['parent_name']        = $value['parent_name'];
                        $temp['is_active']          = ($value['is_active']==1)?'Active':'Inactive';
                        $temp['cost_center']        = $value['cost_center'];
                        $temp['tally_company_name'] = $value['tally_company_name'];
                        $temp['sales_ledger_name'] = $value['sales_ledger_name'];
                        $temp['actions']            = "";

                        if($this->updateBusinessUnitAccess == 1){
                            $temp['actions'] = '<a data-type="edit" data-id="' . $value['bu_id'] . '" ><span  style="padding-left:1px;" onclick="editBusinessData('.$value['bu_id'].');"><i class="fa fa-pencil"></i></span></a>';
                        }

                        unset($businessArr[$key]);

                        $child = $this->getNextBusinessChild($value['bu_id'], $businessArr);
                        if(!empty($child)){
                            $temp['businessChild'] = $child;
                        }else{
                            if($this->deleteBusinessUnitAccess == 1){
                                $temp['actions'] .= '<a data-type="edit" data-id="' . $value['bu_id'] . '" data-toggle="modal" ><span  style="padding-left:25px;"><i  class="fa fa-trash-o" onclick="deleteBusinessData('.$value['bu_id'].');"></i></span></a>';
                            }
                        }
                        $collectChild[] = $temp; 
                    }
                } 
            }
            else{
                return $collectChild;
            }
            return $collectChild;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateSalesLedger(Request $request){
        $request = $request->all();
            if(!empty($request)){
                $data = json_decode($request['data'],true);
                $hub_id = $data['le_wh_id'];
                $name = $data['Name'];
                $flag = $data['flag'];          
                $result = $this->objBusinessDashboardModel->updateBusinessName($hub_id,$name,$flag);            
                    if($result){
                        $Message = "Tally Name Updated Successfully";
                        if($flag == 2){
                            $Message = "Sale Ledger Name Updated Successfully";
                        }
                        $response = array('status'=>200, 'message'=>$Message);
                        return Response::json($response);
                    }else{
                        return $response = array('status'=>500, 'message'=>"Something Went Wrong");
                    }
        }else{
            return $response = array('status'=>200, 'message'=>"Data is empty");
        }
    }
 }
    
