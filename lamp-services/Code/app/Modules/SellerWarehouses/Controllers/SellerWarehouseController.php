<?php

namespace App\Modules\SellerWarehouses\Controllers;

use Session;
use Excel;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Log;
use URL;
use Redirect;
use Hash;
use Carbon\Carbon;
use App\Modules\Lp\Controllers\LogisticPartnersController;
use \App\Modules\SellerWarehouses\Models\SellerWarehouses;
use App\Modules\Lp\Models\LogisticsPartner;
use App\Modules\Lp\Models\lpWarehouses;
use DB;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\RoleRepo;
use stdClass;
use Config;
use Response;
use App\Modules\Roles\Models\Role;


class SellerWarehouseController extends BaseController {

    public function __construct() {  
     $this->swModel = new SellerWarehouses(); 
        try
        {
            $this->middleware(function ($request, $next) {                
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                return $next($request);
            }); 
            parent::Title('Warehouse Configuration - Ebutor');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [index redirects to sellerwarehouse landing page]
     * @return [view] [Redirects to warehouse view]
     */
    public function index() {
        try {
            parent::Title('Warehouse Configuration - Ebutor');
            $breadCrumbs = array('Dashboard' => url('/'), 'Warehouses' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $user_id = Session::get('userId');
            return view('SellerWarehouses::list');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [editCustom To edit warehouse ]
     * @param  [int] $id [warehouse id]
     * @return [view]     [Redirects to edit warehouse]
     */
    public function editCustom($id){

      try{
        $se_wh = new SellerWarehouses();
        $legal_entity_id = Session::get('legal_entity_id');
        $data = DB::table('legalentity_warehouses')->where('le_wh_id',$id)->first();
        // echo "<pre>";print_R($data);die;
        $name = property_exists($data, 'lp_wh_name') ? $data->lp_wh_name : '';
        $virtualdata=DB::table('legal_entities')->where('legal_entity_id',$data->legal_entity_id)->first();        
        //$is_virtual=isset($virtualdata->is_virtual)?($virtualdata->is_virtual==null?0:$virtualdata->is_virtual):0;
        $is_virtual=isset($data->is_apob)?($data->is_apob==null?0:$data->is_apob):0;
        $isFc=0;
        $fcDCDetails=[];
        $fcDc=[];
        if($name != '')
        {
            parent::Title('Edit Warehouse ('.$name.') - Ebutor');
        }else{
            parent::Title('Edit Warehouse - Ebutor');
        }
        $breadCrumbs = array('Dashboard' => url('/'), 'Warehouses' => '/warehouse', 'Edit Warehouse' => '/warehouse/editCustom/'.$id);
        parent::Breadcrumbs($breadCrumbs);
        $states = DB::table('zone')->select('zone.zone_id as state_id','zone.name as state')->where('country_id',99)->orderByRaw("FIELD(name,'Telangana') DESC")->get()->all();
        $country = DB::table('countries')->where('country_id',99)->select('country_id','name as country')->get()->all();
        $tinDoc = DB::table('legal_entity_docs')->where(['legal_entity_id' => $legal_entity_id, 'reference_no' => $id, 'doc_type' => "wh_tin"])->select('doc_id','doc_url','doc_name')->first();
        $apobDoc = DB::table('legal_entity_docs')->where(['legal_entity_id' => $legal_entity_id, 'reference_no' => $id, 'doc_type' => "wh_apob"])->select('doc_id','doc_url','doc_name')->first();
        
        $roleRepo = new RoleRepo();
        //$rmIds = $roleRepo->getUsersByRole(['Field Force Associate', 'Sales Agent']);
        $rmIds = $roleRepo->getUsersByRoleCode(['SSLO', 'SSLA']);
        $getWarehouseType= $se_wh->getMasterLookUpData('118','DC Type');
        $getDcDetails = DB::table('legalentity_warehouses')
                ->where(['status' => 1, 'dc_type' => 118001])
                ->select('le_wh_id', 'lp_wh_name')
                ->get()->all();
//        $dcId = DB::table('dc_hub_mapping')
//                ->where(['hub_id' => $id])
//                ->pluck('dc_id')->all();
        $getCurrentHubData = DB::table('legalentity_warehouses')
                ->where(['status' => 1, 'dc_type' => 118002, 'le_wh_id' => $id])
                ->select('le_wh_id', 'lp_wh_name')
                ->get()->all();
        $userId = Session::get('userId');
        $role=new Role();
        $dcList = $role->getWarehouseData($userId, 6);
        $dc=json_decode($dcList,true);
        if(isset($dc) && count($dc)!=0 && $dc!=null && array_key_exists(118002,$dc)){
            $dc=explode(',',$dc[118002]);
            $getHubDetails = DB::table('legalentity_warehouses')
                ->where(['status' => 1, 'dc_type' => 118002])
                ->whereIn('le_wh_id',$dc)
                ->select('le_wh_id', 'lp_wh_name')
                ->get()->all();
        }else{
            $getHubDetails=[];
        }  
        $se_wh_mas = new SellerWarehouses();
        $priceGroup = $se_wh_mas->getPriceGroup();
        $priceGroup_id = $se_wh_mas->getPriceGroupEdit($id); 
        $priceGroup_id = isset($priceGroup_id->stockist_price_group_id) ? $priceGroup_id->stockist_price_group_id : 0;
        $getFcDetails=DB::table('legal_entities as l')
            ->leftJoin('legalentity_warehouses as lw', 'l.legal_entity_id', '=', 'lw.legal_entity_id')
            ->select('l.legal_entity_id','l.business_legal_name','lw.le_wh_id')
            ->where('l.legal_entity_type_id','=',1014)
            ->where('lw.dc_type',118001)
            ->get()->all();
       
        $getHubData = DB::table('dc_hub_mapping')
                ->where(['dc_id' => $id])
                ->pluck('hub_id')->all();
        $getFcData=DB::table('dc_fc_mapping')
                    ->where(['dc_le_wh_id'=>$id])
                    ->pluck('fc_le_wh_id')->all();
        $checkFcDc=DB::table('legal_entities')->where(["legal_entity_id"=>$data->legal_entity_id,"legal_entity_type_id"=>1014])->get()->all();
        if(count($checkFcDc)>0 || $is_virtual==1){

            $fcDCDetails=DB::table('dc_fc_mapping')->where('dc_le_wh_id','=',$id)->pluck('fc_le_wh_id')->all();
            $fcDc=DB::table('legalentity_warehouses as lw')
                ->leftJoin('legal_entities as l', 'l.legal_entity_id', '=', 'lw.legal_entity_id')->groupBy('l.legal_entity_id')->where('dc_type','=',118001)->where('l.legal_entity_type_id','=',1016)->where('l.is_virtual','=',0)->get()->all();
            if(count($checkFcDc)>0){
                $isFc=1;
                $fcDCDetails=DB::table('dc_fc_mapping')->where('fc_le_wh_id','=',$id)->pluck('dc_le_wh_id')->all();

            } 

        }
        $spokes = DB::table('spokes')
                ->where(['le_wh_id' => $id])
                ->select('spoke_id', 'spoke_name')
                ->get()->all();
        $updateDCInfo = $roleRepo->checkPermissionByFeatureCode('DC004');
        $updateDCDocInfo = $roleRepo->checkPermissionByFeatureCode('DC005');
        $dcImportPincodes = $roleRepo->checkPermissionByFeatureCode('DC006');
        $dcUpdatePincodes = $roleRepo->checkPermissionByFeatureCode('DC007');
        $dcAddPJP = $roleRepo->checkPermissionByFeatureCode('DC008');
        $dcEditPJP = $roleRepo->checkPermissionByFeatureCode('DC009');
        $dcDeletePJP = $roleRepo->checkPermissionByFeatureCode('DC010');
        $dcDeletePincodes = $roleRepo->checkPermissionByFeatureCode('DC011');
        $dcAddSpoke = $roleRepo->checkPermissionByFeatureCode('DC013');
        $globalaccess=$roleRepo->checkPermissionByFeatureCode('GLB0001');
        if($globalaccess==1){
            $updateDCInfo=1;
            $updateDCDocInfo =1;
            $dcImportPincodes =1;
            $dcUpdatePincodes =1;
            $dcAddPJP = 1;
            $dcEditPJP = 1;
            $dcDeletePJP = 1;
            $dcDeletePincodes = 1;
            $dcAddSpoke = 1;
        }
        $pincode_locations = DB::table('wh_serviceables as ws')->leftJoin('cities_pincodes as cp','cp.pincode','=','ws.pincode')->groupBy('ws.pincode')->where(['ws.le_wh_id' => $id])->select('ws.pincode','cp.state','cp.city','ws.le_wh_id','ws.legal_entity_id','wh_serviceables_id')->get()->all();
        foreach ($pincode_locations as $key => $value) {
            if($dcDeletePincodes)
            {
                $actions = '<span style="padding-left:20px;" ><a id ="deletePin" href="javascript:void(0)" onclick="deletePin(' . $value->wh_serviceables_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                $value->actions = $actions;
            }            
        }
        $pincode_locations = json_encode($pincode_locations);

		$currUrl = URL::current(); 
        $urlArray = explode('/',$currUrl);
        if(isset($urlArray[0]) && $urlArray[0]=='https'){
        $mapurl = "https://maps.googleapis.com/maps/api/js?key=".env('GOOGLE_MAP_URL_KEY')."&libraries=places";
        }
        else{
        $mapurl = "http://maps.googleapis.com/maps/api/js?key=".env('GOOGLE_MAP_URL_KEY')."&libraries=places";    
        }
        $slot_data = $se_wh->getMasterLookupForTimeSlot();
        $days = $se_wh_mas->getDays();
        return view('SellerWarehouses::editCustom')->with(['states' => $states, 
            'countries' => $country,
            'data'=>$data,
            'id' => $id,
            'tinDoc' => $tinDoc, 
            'apobDoc' => $apobDoc,
            'pincode_locations' => $pincode_locations ,
            'le_wh_id'=>$id, 
            'rm_ids' => $rmIds,
            'spokes' => $spokes,
            'warehouse_type'=>$getWarehouseType,
            'updateDCInfo' => $updateDCInfo,
            'updateDCDocInfo' => $updateDCDocInfo,
            'dcImportPincodes' => $dcImportPincodes,
            'dcUpdatePincodes' => $dcUpdatePincodes,
            'dcAddPJP' => $dcAddPJP,
            'dcEditPJP' => $dcEditPJP,
            'dcDeletePJP' => $dcDeletePJP,
            'getHubData' => (array)$getHubData,
            'getCurrentHubData' => $getCurrentHubData,
            'getDcDetails' => $getDcDetails,
            'getHubDetails' => $getHubDetails,
            'dcAddSpoke' => $dcAddSpoke,
			'mapurl'=>$mapurl,
            'getFcData' => $getFcData,
            'getFcDetails'=> $getFcDetails,
            'isFc'=>$isFc,
            'fcDCDetails'=>$fcDCDetails,
            'fcDc'=>$fcDc,
            'is_virtual'=>$is_virtual,
            'priceGroup'=>$priceGroup,
            'priceGroup_id'=>$priceGroup_id,
//            'dcId' => $dcId
            'is_apob'=>$data->is_apob,
            'credit_limit_check'=>$data->credit_limit_check,
            'is_billing'=>$data->is_billing,
            'slot' => $slot_data,
            'days'=>$days,
            'is_disabled' =>$data->is_disabled,
            'send_ff_otp'=>$data->send_ff_otp,
            'is_binusing'=>$data->is_binusing,
            'wh_pdp'=>$data->wh_pdp,
            'wh_pdp_slot'=>$data->wh_pdp_slot,
                ]);

      }
      catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getSavedPincodes get list of pincode under a warehouse]
     * @param  [int] $le_wh_id        [warehouse id]
     * @param  [int] $legal_entity_id [legal entity id]
     * @return [array]                  [list of pincodes]
     */
    public function getSavedPincodes($le_wh_id,$legal_entity_id){
        try {
            $pincodes = DB::table('wh_serviceables')->where(['legal_entity_id' => $legal_entity_id, 'le_wh_id' => $le_wh_id])->select('pincode')->get()->all();
            return $pincodes;

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getPincodeAreas get areas list under beat & pincode]
     * @param  [int] $pincode [pincode]
     * @param  [int] $beatId  [beat id]
     * @return [array]        [areas list]
     */
    public function getPincodeAreas($pincode, $beatId){
        try {
            $selectedAreas = DB::table('pincode_area')
                    ->where('pjp_pincode_area_id', $beatId)
                    ->pluck('area_id')->all();
            $areas = DB::table('cities_pincodes')
                    ->where('pincode',$pincode)
                    ->whereNotIn('city_id', $selectedAreas)
                    ->select('officename as area','city_id')->get()->all();
            return $areas;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [savePJP To add beat]
     * @param  [int] $le_wh_id [warehouse id]
     * @param  [int] $legal_id [legal entity id]
     * @return [array]           [Array with inserted id, status & message]
     */
    public function savePJP($le_wh_id,$legal_id){
        try {
            $data = Input::all();
            $status = 0;
            $message = "Unable to save Beat";
            $days = implode(',',$data['week']);
            $pjp_area_id = DB::table('pjp_pincode_area')->insertGetId([
                'pjp_name' => $data['pjp_name'],
                'days' => $days,
                'rm_id' => $data['rm_id'],
                'le_wh_id' => $le_wh_id,
                'spoke_id' => $data['spoke'],
                'default_pincode' => $data['pincode'],
                'created_by' => $legal_id,
                'created_at' => date('Y-m-d H:i:s'),
                'pdp' => $data['pdp']
                //'pdp_slot'=> $data['pdp_slot']
                ]);
           
            DB::table('pincode_beats')
                    ->insert([
                    'beat_id' => $pjp_area_id,
                    'pincode' => $data['pincode'],
                    'created_at' => date('Y-m-d H:i:s')
                    //'updated_at' => date('Y-m-d H:i:s')
                ]);
            $status = 1;

            $message = "Beat saved successfully";
           return json_encode([
            'status' => $status,
            'pjp_pincode_area_id' => $pjp_area_id,
            'message' => $message
            ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString()); 
        }
    }
    /**
     * [savePJPArea To add pincode,area and beat combination]
     * @return [array] [With status qand message]
     */
    public function savePJPArea(){
        try {
            $data = Input::all();
            $status = 0;
            $message = "Unable to Save Beat Area";
            $legal_id = Session::get('legal_entity_id');            
            $pjp_area_id = $data['pjp_pincode_area_id'];
            if (!empty($pjp_area_id)) {
                 foreach ($data['pin_area'] as $key => $area) {
                $pincode_area_id = DB::table('pincode_area')->insertGetId([
                                    'pjp_pincode_area_id' => $pjp_area_id,
                                    'pincode' => $data['pincode_value'],
                                    'area_id' => $area,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => $legal_id,
                                    ]);
                }
                $status = 1;
                $message = "Beat Area saved successfully";
            }
            return json_encode(['status' => $status,
                'message' => $message
                ]);
        } catch (\ErrorException $e) {
            Log::info($e->getTraceAsString());
            Log::info($e->getMessage());
        }
    }
    /**
     * [deletePJPArea To delete pincode,area and beat combination]
     * @param  [int] $pjp_pincode_area_id [beat id]
     * @return [array][with status & message]
     */
    public function deletePJPArea($pjp_pincode_area_id){
        try {
            $status = 0;
            $message = "Unable to delete Beat Area";
            //DB::table('pincode_area')->where('pjp_pincode_area_id', $pjp_pincode_area_id)->delete();
            DB::table('pincode_area')->where('pincode_area_id', $pjp_pincode_area_id)->delete();
            $status = 1;
            $message = "Delete Beat Area Successfully";
            return json_encode(['status' => $status,
               'message' => $message ]);
        } catch (\ErrorException $e) {
            Log::info($e->getTraceAsString());
            Log::info($e->getMessage());   
        }
    }
    /**
     * [getPJPs To get beats list]
     * @return [array] [records list]
     */
    public function getPJPs(){
        try {
            $data = Input::all();
            $path = isset($data['path']) ? $data['path'] : '';
            $spokeId = 0;
            if ($path != '') {
                if (strpos($path, '/') !== false) {
                    $path2 = explode('/', $path);
                    if(is_array($path2))
                    {
                        $path = isset($path2[1]) ? $path2[1] : $path;
                    }
                }
                $temp = explode(':', $path);
                $spokeId = isset($temp[1]) ? $temp[1] : 0;
            }
            $data =  DB::table('pjp_pincode_area as ppa')
                            ->leftJoin('users', 'users.user_id', '=', 'ppa.rm_id')
                            ->leftJoin('pincode_area as pa','ppa.pjp_pincode_area_id','=','pa.pjp_pincode_area_id')
//                            ->where('ppa.le_wh_id',$le_wh_id)
                            ->where('ppa.spoke_id',$spokeId)
                            ->select('ppa.days','ppa.pjp_name','ppa.pjp_pincode_area_id',
                                    DB::raw('count(pa.area_id) as total_areas'), 
                                    DB::raw('concat(users.firstname, " ", users.lastname) as rm_name'))
                            ->groupBy('ppa.pjp_pincode_area_id')
                            ->get()->all();
            $actions = '';
            $roleRepo = new RoleRepo();
            $dcAddPJP = $roleRepo->checkPermissionByFeatureCode('DC008');
            $dcEditPJP = $roleRepo->checkPermissionByFeatureCode('DC009');
            $dcDeletePJP = $roleRepo->checkPermissionByFeatureCode('DC010');
            $dcMovePJP = $roleRepo->checkPermissionByFeatureCode('DC012');
            $globalaccess=$roleRepo->checkPermissionByFeatureCode('GLB0001');
            if($globalaccess == 1){
                $dcAddPJP=1;
                $dcEditPJP=1;
                $dcDeletePJP=1;
                $dcMovePJP=1;
            }

            foreach ($data as $key => $value) {
                $pincode_area = DB::table('pincode_area as pa')
                                ->leftJoin('cities_pincodes as cp','cp.city_id','=','pa.area_id')
                                ->select('pa.pincode','pa.pincode_area_id',DB::raw('GROUP_CONCAT(cp.officename) as areas'))
                                ->where('pa.pjp_pincode_area_id',$value->pjp_pincode_area_id)
                                ->groupBy('pa.pincode')
                                ->get()->all();
                $actions = '';
                //if($dcAddPJP)
                //{
                //$actions = '<span style="padding-left:10px;" ><a data-href="/warehouse/addPJPArea/'. $value->pjp_pincode_area_id.'" data-toggle="modal" id="addPJPArea" data-target="#basicvalCodeModal2"><i class="fa fa-plus"></i></a></span>';
                //}
                if($dcEditPJP)
                {
                    $actions = $actions.'<span style="padding-left:10px;" ><a href="javascript:void(0);" onclick="editPJP(' . $value->pjp_pincode_area_id . ')" data-target="#basicvalCodeModal4"><i class="fa fa-pencil"></i></a></span>';
                }
                if($dcDeletePJP)
                {
                    $actions = $actions . '<span style="padding-left:10px;" ><a id ="deletePJP" href="javascript:void(0)" onclick="deletePJP(' . $value->pjp_pincode_area_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                }
//                if($dcMovePJP)
//                {
//                    $actions = $actions . '<span style="padding-left:10px;" ><a id ="movePJP" href="javascript:void(0)" onclick="movePJP(' . $value->pjp_pincode_area_id . ')" data-target="#basicvalCodeModal3"><i class="fa fa-exchange"></i></a></span>';
//                }
                $value->actions = $actions; 
//                foreach ($pincode_area as $key1 => $area) {
//                    $actions = '';
//                     $actions = '<span style="padding-left:10px;" ><a id ="deletePJPArea" href="javascript:void(0)" onclick="deletePJPArea(' . $value->pjp_pincode_area_id . ')"><i class="fa fa-trash-o"></i></a></span>';
//                    $area->actions = $actions;
//                }
//              $value->pincode_area = $pincode_area;
              
            }

            if($data){
                return json_encode(array('Records' => $data));
            }
            else{
                echo '{"Records":[],"TotalRecordsCount":0}';
                exit;  
            }
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [editPJP To get beat details to edit]
     * @param  [int] $id [beat id]
     * @return [view]    [redirects to edit beat]
     */
    public function editPJP($id){
        try {
            $data = DB::table('pjp_pincode_area')
                    ->where('pjp_pincode_area_id',$id)
                    ->select('days','pjp_name','le_wh_id', 'rm_id', 'spoke_id','default_pincode','pdp','pdp_slot')
                    ->first();
            $se_wh = new SellerWarehouses();
            $legal_entity_id = Session::get('legal_entity_id');
            $data->days = explode(',', $data->days);
            $data->pjp_pincode_area_id = $id;
            $data->legal_entity_id = $legal_entity_id;
            $roleRepo = new RoleRepo();
            //$rmIds = $roleRepo->getUsersByRole(['Field Force Associate', 'Sales Agent']);
            $rmIds = $roleRepo->getUsersByRoleCode(['SSLO', 'SSLA']);
            $hubId = property_exists($data, 'le_wh_id') ? $data->le_wh_id : 0;
            $spokes = DB::table('spokes')
                ->where(['le_wh_id' => $hubId])
                ->select('spoke_id', 'spoke_name')
                ->get()->all();
            $slot_data = $se_wh->getMasterLookupForTimeSlot();

            return view('SellerWarehouses::editPJP')->with(['data' => $data, 
                'rm_ids' => $rmIds,
                'spokes' => $spokes,
                'slot' => $slot_data]);
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [updatePJP To update beat]
     * @return [array] [With status and message]
     */
    public function updatePJP(){
        try {
            $data = Input::all();
            $status = 0;
            $message = "Unable to update Beat";
            $days = implode(',',$data['week']);
            DB::table('pjp_pincode_area')
            ->where('pjp_pincode_area_id',$data['pjp_pincode_area_id'])
            ->update([
                'pjp_name' => $data['pjp_name'],
                'rm_id' => $data['rm_id'],
                'spoke_id' => $data['spoke_id'],
                'default_pincode' => $data['pincode'],
                'days' => $days,
                'updated_at' => date('Y-m-d H:i:s'),
                'pdp' =>$data['pdp']
               // 'pdp_slot'=>$data['pdp_slot']
            ]);


            $checkdata = DB::table("pincode_beats")->where("beat_id",$data['pjp_pincode_area_id'])->where("pincode",$data['pincode'])->first();

            if(!count($checkdata)){
                DB::table('pincode_beats')
                    ->insert([
                    'beat_id' => $data['pjp_pincode_area_id'],
                    'pincode' => $data['pincode'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            }
            $status = 1;
            $message = "Successfully updated Beat";
            // update retailer flat table
            $rminfo = DB::table('pjp_pincode_area')
                        ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
                        ->where('pjp_pincode_area.pjp_pincode_area_id', $data['pjp_pincode_area_id'])
                        ->select('pjp_pincode_area.pjp_name', DB::raw('GetUserName(pjp_pincode_area.rm_id, 2) AS beat_rm_name'),
                        'spokes.le_wh_id AS hub_id')
                        ->first();
            if(!empty($rminfo))
            {
                $updateFields['beat_rm_name'] = property_exists($rminfo, 'beat_rm_name') ? $rminfo->beat_rm_name : '';
                $updateFields['hub_id'] = property_exists($rminfo, 'hub_id') ? $rminfo->hub_id : 0;
                $updateCustomerFields['hub_id'] = property_exists($rminfo, 'hub_id') ? $rminfo->hub_id : 0;
                $updateFields['beat'] = property_exists($rminfo, 'pjp_name') ? $rminfo->pjp_name : '';
            }               
            $updateFields['spoke_id'] = $data['spoke_id'];
            $updateCustomerFields['spoke_id'] = $data['spoke_id'];
            if(!empty($updateFields))
            {
                DB::table('retailer_flat')
                        ->where('beat_id', $data['pjp_pincode_area_id'])
                        ->update($updateFields);
            }
            if($updateCustomerFields)
            {
                DB::table('customers')
                        ->where('beat_id', $data['pjp_pincode_area_id'])
                        ->update($updateCustomerFields);
            }
            return json_encode(['status' => $status,
                'message' => $message
                ]);
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [getChildPJPs To get beats information under a warehouse and to whom they were assigned ]
     * @return [array] [beats list]
     */
    public function getChildPJPs(){
        try {
            $data = Input::all();
             $path = isset($data['path']) ? $data['path'] : '';
            if ($path != '') {
                $temp = explode(':', $path);
                $le_wh_id = isset($temp[1]) ? $temp[1] : 0;
                if ($le_wh_id != 0) {
                    $data = DB::table('pjp_pincode_area as ppa')
                            ->leftJoin('users', 'users.user_id', '=', 'ppa.rm_id')
                            ->leftJoin('pincode_area as pa','ppa.pjp_pincode_area_id','=','pa.pjp_pincode_area_id')
                            ->where('ppa.le_wh_id',$le_wh_id)
                            ->select('ppa.days','ppa.pjp_name','ppa.pjp_pincode_area_id',DB::raw('count(pa.area_id) as total_areas'), DB::raw('concat(users.firstname, " ", users.lastname) as rm_name'))
                            ->groupBy('ppa.pjp_pincode_area_id')
                            ->get()->all();

                }
            return $data;
            }
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [getChildPJPAreas To get areas list configuration]
     * @return [array] [Areas list]
     */
    public function getChildPJPAreas(){
        try {
            $data = Input::all();
            $path = isset($data['path']) ? $data['path'] : '';
             if ($path != '') {
                if (strpos($path, '/') !== false) {
                    $path2 = explode('/', $path);
//                    echo "<pre>";print_R($path2);
                    if(is_array($path2))
                    {
                        $path = isset($path2[2]) ? $path2[2] : (isset($path2[1]) ? $path2[1] : $path);
                    }
                }
//                print_R($path);
//                $temp = explode('/', $path);
                $id = explode(':', $path);
                $pjp_pincode_area_id = isset($id[1]) ? $id[1] : 0;
            }
            if($pjp_pincode_area_id != 0){
                $pincode_area = DB::table('pincode_area as pa')
                                ->leftJoin('cities_pincodes as cp','cp.city_id','=','pa.area_id')
                                ->select('pa.pjp_pincode_area_id', 'pa.pincode','pa.pincode_area_id',DB::raw('GROUP_CONCAT(cp.officename) as areas'),DB::raw('count(pa.area_id) as count_areas'))
                                ->where('pa.pjp_pincode_area_id',$pjp_pincode_area_id)
                                ->groupBy('pa.pincode')
                                ->get()->all();
                if(!empty($pincode_area))
                {
                    $roleRepo = new RoleRepo();
                    $dcDeleteArea =$roleRepo->checkPermissionByFeatureCode('DC010');
                    $globalaccess=$roleRepo->checkPermissionByFeatureCode('GLB0001');
                    if($globalaccess == 1){
                        $dcDeleteArea=1;
                    }
                    $actions = '';
                    foreach($pincode_area as $area)
                    {
                        $actions = '';
                        if($dcDeleteArea)
                        {
                            $actions = $actions . '<span style="padding-left:10px;" ><a id ="deletePJPArea" href="javascript:void(0)" onclick="deletePJPArea('. $area->pincode_area_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                        }
                        $area->actions = $actions;
                    }
                }
            }
            return $pincode_area;
        }catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [addPJPArea To add  pjp area]
     * @param [string] $name [name fo area]
     */
    public function addPJPArea($name)
    {
        try { 
            return Response::json(['pjp_pincode_area_id' => $name]);
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    /**
     * [deletePJP To delete beat]
     * @param  [int] $pjp_pincode_area_id [beat id]
     * @return [array]                    [with status and message]
     */
    public function deletePJP($pjp_pincode_area_id){
        try {
             $status = 0;
             $message = "Unable to delete Beat";
            $areas = DB::table('pincode_area')->where('pjp_pincode_area_id',$pjp_pincode_area_id)->get()->all();
            if(!empty($areas)){
            DB::table('pincode_area')->where('pjp_pincode_area_id',$pjp_pincode_area_id)->delete();
            }
            DB::table('pjp_pincode_area')
                        ->where('pjp_pincode_area_id','=',$pjp_pincode_area_id)
                        ->delete();
            $status = 1;
            $message = "Successfully deleted Beat";
            return json_encode([
                'status' => $status,
                'message' => $message
                ]);
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString);
        }
    }

    /**
     * [importExcel To import pincodes into wh_serviceables]
     * @return [array] [pincodes list]
     */
    public function importExcel(){
        try {
            $data = Input::all();
            //echo "<pre>"; print_r($data); die();
            ini_set('max_execution_time', 1200);
              if  (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $filePins =  Excel::load( Input::file('import_file') )
                      ->ignoreEmpty()
                      ->get()->all();
                
            }
            $pins = DB::table('wh_serviceables')
                        ->where(['le_wh_id'=>$data['le_wh_id'], 'legal_entity_id' => $data['legal_entity_id']])
                        ->get(['pincode'])->all();
            
            $temp = [];
            $message = '';
            foreach ($pins as $key => $pin) {
                # code...
                $temp[] =  $pin->pincode;
            }
            $i = 0;
            $pincode_cities = [];
            $pincode_messages = [];
            $j = 0;
            $k = 0;
            $pincode_delete = [];
            foreach ($filePins as $key => $value) {
                $validator = Validator::make(
                    ['pincode' => isset($value['pincodes']) ? $value['pincodes'] : '',
                      'delete' => isset($value['delete']) ? $value['delete'] : '',
                    ], 
                    ['pincode' => 'required|numeric|digits:6|',
                    'delete' => 'required|in:Yes,No'
                    ]);

                if ($validator->fails()) {
                    $messages = $validator->messages();
                    $messageArr = json_decode($messages);
                    //$errorMsg = '';
                    
                        $pincode_messages[$j]['pincode'] = $value['pincodes'];
                        $pincode_messages[$j]['message'] = '';
                    if (isset($messageArr->pincode[0])){
                        $pincode_messages[$j]['message'] = 'Pincode ' . $value['pincodes'] .' '. $messageArr->pincode[0];
                    }
                    if(isset($messageArr->delete[0])){
                        $pincode_messages[$j]['message'] = $pincode_messages[$j]['message'] . ' Pincode ' . $value['pincodes'] .' '. $messageArr->delete[0];
                    }
                }else{
                if(in_array($value['pincodes'], $temp) == 1 && $value['delete'] == 'No'){
                         $pincode_messages[$j]['pincode'] = $value['pincodes'];
                        $pincode_messages[$j]['message'] = 'Pincode ' . $value['pincodes'] . ' already exists. ' ;
                }
                else if(in_array($value['pincodes'], $temp) != 1 && $value['delete'] == 'No'){
                     $id = DB::table('wh_serviceables')
                                ->insertGetId([
                                'legal_entity_id' => $data['legal_entity_id'],
                                'pincode' => $value['pincodes'],
                                'le_wh_id' => $data['le_wh_id'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => $data['le_wh_id']
                                ]);
                        if($id){
                        $pincode_messages[$j]['pincode'] = $value['pincodes'];
                        $pincode_messages[$j]['message'] = 'Pincode '. $value['pincodes'].' added successfully ' ;
                            $pincode_cities[$i] = DB::table('cities_pincodes')->where('pincode',$value['pincodes'])->select('state','city','pincode')->first();
                            $pincode_cities[$i]->wh_serviceables_id = $id;
                            $i++;
                        }
                }
                else if(in_array($value['pincodes'], $temp) == 1 && $value['delete'] == "Yes"){
                    $id = DB::table('wh_serviceables')
                            ->where(['pincode'=> $value['pincodes'], 'le_wh_id'=>$data['le_wh_id'], 'legal_entity_id' => $data['legal_entity_id']])
                            ->select('wh_serviceables_id')->first();
                    //echo "<pre>"; print_r($id); die();

                    if(!empty($id)){
                        DB::table('wh_serviceables')->where('wh_serviceables_id',$id->wh_serviceables_id)->delete();
                        $pincode_messages[$j]['pincode'] = $value['pincodes'];
                        $pincode_messages[$j]['message'] =  "Pincode " . $value['pincodes'] . ' deleted. ';
                        $pincode_delete[$k]['pincode'] = $value['pincodes'];
                        $k++;
                    }
                }else{
                    $pincode_messages[$j]['pincode'] = $value['pincodes'];
                    $pincode_messages[$j]['message'] =  'Pincode '.$value['pincodes'] .' doesnot exist to delete. ';
                }
                }
            $j++;
            }
            return json_encode([
                    'message' => $pincode_messages,
                    'pincode_cities' => $pincode_cities,
                    'pincode_delete' => $pincode_delete
                ]);
        }  catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [exportPin To export pincode data]
     * @param  [int] $id   [warehouse id]
     * @param  [int] $smpl [it is a flag whether to download sample data/original data]
     */
    public function exportPin($id,$smpl){
        try {
            $legal_entity_id = Session::get('legal_entity_id');
            $pincode = [];
            $pins = DB::table('wh_serviceables')->where(['legal_entity_id' => $legal_entity_id, 'le_wh_id' => $id])->select('pincode')->get()->all();
            $pin_code = json_encode($pins,true);
            $pincode = json_decode($pin_code,true);
            
            $pincodeArr[] = array('Pincodes','Delete');
             if($smpl == 0){
                $pincodeArr[]=array('pincode'=>'500039','Delete'=>'No');
            }
            else{
                foreach ($pins as $key => $value) {
                    # code...
                    $pincodeArr[]=array('pincode'=>$value->pincode,'Delete'=>'No');
                }
            }

            $file_name = 'pincodes';
            //echo "<pre>"; print_r($pincode); die();
            $result = Excel::create($file_name, function($excel) use($pincodeArr) {
                    $excel->sheet('Sheet1', function($sheet) use($pincodeArr) {
                        $sheet->fromArray($pincodeArr, null, 'A1', false, false);
                    });                   
                })->export('csv');
            exit();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }  
    }
    /**
     * [downloadPinSample To download pincode list sample]
     */
    public function downloadPinSample(){
        try{
            $pincodeArr[] = array('Pincodes');
            $pincodeArr[]=array('pincode'=>'500039');
            Excel::create("pincode_sample", function($excel) use ($pincodeArr) {
            $excel->sheet('Sheet 1', function($sheet) use ($pincodeArr) {
                $sheet->fromArray($pincodeArr, null, 'A1', false, false);
            });
            })->export('csv');
            exit();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }  
    }
    /**
     * [importPinSample Sample excel sheet template]
     * @return [array] [pincode cities]
     */
    public function importPinSample(){
        try {
            $data = Input::all();
            ini_set('max_execution_time', 1200);
              if  (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $filePins =  Excel::load( Input::file('import_file') )
                      ->ignoreEmpty()
                      ->get()->all();
                
            }
           $pins = DB::table('wh_serviceables')
                        ->where(['le_wh_id'=>$data['le_wh_id'], 'legal_entity_id' => Session::get('legal_entity_id')])
                        ->get(['pincode'])->all();
            
            $temp = [];
            $message = '';
            if(!empty($pins)){
                foreach ($pins as $key => $pin) {
                    # code...
                        $temp[] =  $pin->pincode;
                    }
            }
            $i = 0;
            $j = 0;
            $pincode_messages = [];
            $pincode_cities = [];
            foreach ($filePins as $key => $value) {

                 $validator = Validator::make(
                    ['pincode' => $value['pincodes']
                    ], 
                    ['pincode' => 'required|numeric|digits:6|',
                    ]);

                if ($validator->fails()) {
                    $messages = $validator->messages();
                    $messageArr = json_decode($messages);
                    //$errorMsg = '';
                    if (isset($messageArr->pincode[0]))
                        $pincode_messages[$j]['pincode'] = $value['pincodes'];
                        $pincode_messages[$j]['message'] = 'Pincode ' . $value['pincodes'] .' '. $messageArr->pincode[0];
                }else{


                 if(in_array($value['pincodes'], $temp) == 1){
                   $pincode_messages[$j]['pincode'] = $value['pincodes'];
                   $pincode_messages[$j]['message'] = 'Pincode ' . $value['pincodes'] . ' already exists. ' ;
                }
                else{
                        $id = DB::table('wh_serviceables')
                                ->insertGetId([
                                'legal_entity_id' => Session::get('legal_entity_id'),
                                'pincode' => $value['pincodes'],
                                'le_wh_id' => $data['le_wh_id'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => $data['le_wh_id']
                                ]);
                        if($id){
                            $pincode_messages[$j]['pincode'] = $value['pincodes'];
                            $pincode_messages[$j]['message'] = 'Pincode '. $value['pincodes'].' added successfully ' ;
                            $pincode_cities[$i] = DB::table('cities_pincodes')->where('pincode',$value['pincodes'])->select('state','city','pincode')->first();
                            $pincode_cities[$i]->wh_serviceables_id = $id;
                            $i++;
                        }
                }
            }
            $j++;
        }
        //echo "<pre>"; print_r($pincode_messages); die();
        return json_encode([
            'message' => $pincode_messages,
            'pincode_cities' => $pincode_cities,

        ]);
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }  
    }
    /**
     * [editWarehouse To edit warehouse]
     * @param  [int] $id [warehouse id]
     * @return [view]     [redirects to view]
     */
    public function editWarehouse($id){
       try {
           $data = DB::table('legalentity_warehouses')->where('le_wh_id',$id)->first(); 
           $tinProof = DB::table('legal_entity_docs')->where(['legal_entity_id'=>$data->legal_entity_id, 'reference_no' => $id])->where('doc_url','like','%/LegalWarehouses/%')->where('doc_type','like','%tin%')->select('doc_id','doc_name','doc_url')->first();
           $apobProof = DB::table('legal_entity_docs')->where(['legal_entity_id'=>$data->legal_entity_id, 'reference_no' => $id])->where('doc_url','like','%/LegalWarehouses/%')->where('doc_type','like','%apob%')->select('doc_id','doc_name','doc_url')->first();
           return view('SellerWarehouses::editWarehouse')->with(['tinProof' => $tinProof, 'apobProof' => $apobProof, 'data'=>$data,'id' => $id]);
       } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [savePinLocations To save pincodes]
     * @return [array] [With status & message]
     */
    public function savePinLocations(){
        try {
        $data = Input::all();
        $pins = json_decode($data['pincode_locations']);
        //$legal_entity_id = Session::get('legal_entity_id');
//        $pincodes = $data['pincodes'];
        $le=DB::table('legalentity_warehouses')->where('le_wh_id',$data['le_wh_id'])->pluck(DB::raw('legal_entity_id'))->all();
        $legal_entity_id =$le[0];
        $message = "Unable to save location";
        $status = false;
        $id = 0;
        foreach ($pins as $key => $value) {
            if(!isset($value->wh_serviceables_id)){
                     $id = DB::table('wh_serviceables')
                    ->insertGetId([
                    'legal_entity_id' => $legal_entity_id,
                    'pincode' => $value->pincode,
                    'le_wh_id' => $data['le_wh_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $data['le_wh_id']
                    ]);
            }else{
                $id = 1;
            }
        }
        if($id){
            $message = "Saved successfully";
            $status = true;
        }
        return json_encode([
            'message' => $message,
            'status' => $status
            ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
     
    /**
     * [addCustom To add custom warehouse]
     * @return  [Redirects to add custom warehouse view]
     */
    public function addCustom(){
        try {
            $roleRepo = new RoleRepo();
            $se_wh = new SellerWarehouses();
            $dcAddBeat = $roleRepo->checkPermissionByFeatureCode('DC008');
            $dcAddSpoke = $roleRepo->checkPermissionByFeatureCode('DC013');
            $globalaccess=$roleRepo->checkPermissionByFeatureCode('GLB0001');
            if($globalaccess==1){
                $dcAddBeat=1;
                $dcAddSpoke=1;
            }

            parent::Title('Warehouses');
            $breadCrumbs = array('Dashboard' => url('/'), 'Warehouses' => '/warehouse', 'Add Warehouse' => '/warehouse/addCustom');
            parent::Breadcrumbs($breadCrumbs);  
            $legal_entity_id = Session::get('legal_entity_id');
            $getWarehouseType= $se_wh->getMasterLookUpData('118','DC Type');
            $states = DB::table('zone')->select('zone.zone_id as state_id','zone.name as state')->where('country_id',99)->orderByRaw("FIELD(name,'Telangana') DESC")->get()->all();
             $country = DB::table('countries')->where('country_id',99)->select('country_id','name as country')->get()->all();             
             //$rmIds = $roleRepo->getUsersByRole(['Field Force Associate', 'Sales Agent']);
             $rmIds = $roleRepo->getUsersByRoleCode(['SSLO', 'SSLA']);
            $userId = Session::get('userId');
            $role=new Role();
            $dcList = $role->getWarehouseData($userId, 6);
            $se_wh_mas = new SellerWarehouses();
            $priceGroup = $se_wh_mas->getPriceGroup();
            $dc=json_decode($dcList,true);        
            $dc=explode(',',$dc[118002]);
             $getHubDetails = DB::table('legalentity_warehouses')
                ->where(['status' => 1, 'dc_type' => 118002])
                ->whereIn('le_wh_id',$dc)
                ->select('le_wh_id', 'lp_wh_name')
                ->get()->all();        

             return view('SellerWarehouses::addCustomWarehouse')
                     ->with(['states' => $states, 
                         'countries' => $country, 
                         'legal_entity_id' => $legal_entity_id,
                         'warehouse_type'=>$getWarehouseType, 
                         'rm_ids' => $rmIds, 
                         'getHubDetails' => $getHubDetails,
                         'dcAddBeat' => $dcAddBeat,
                         'dcAddSpoke' => $dcAddSpoke,
                         'priceGroup'=>$priceGroup]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex;
        }
    }
    /**
     * [saveDocs To save documents like tin,apob related docs
     * @return [array][with status & message]
     */
    public function saveDocs(){
        try {
            $data = Input::all();
            $legal_entity_id = Session::get('legal_entity_id');
            $se_wh = new SellerWarehouses();
            $response = $se_wh->saveDocs($data,$legal_entity_id);
            DB::table('legalentity_warehouses')
                        ->where('le_wh_id',$data['le_wh_id'])
                        ->update([
                            'tin_number' => $data['tin_number']
                            ]);
            
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [updateDocs  To update documents like tin,apob related docs]
     * @return [array] [With status and message]
     */
    public function updateDocs(){
        try {
            $data = Input::all();
            $response = '';
            $status = 0;
            $message = "Unable to update";
            $legal_entity_id = Session::get('legal_entity_id');
            $se_wh = new SellerWarehouses();
            if(isset($data['tin_files']) || isset($data['apob_files'])){
            $response = $se_wh->updateDocs($data,$legal_entity_id);
            }
            DB::table('legalentity_warehouses')
                        ->where('le_wh_id',$data['le_wh_id'])
                        ->update([
                            'tin_number' => $data['tin_number']
                            ]);
            $message = "Updated Successfully.";
            $status = 1;
            if(empty($response)){
                $response =  json_encode([
                'status' => $status,
                'message' => $message
                ]);
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [logisticPartners To create a warehouse]
     * @return [view] [Redirects to create warehouse view]
     */
    public function logisticPartners(){
        try {
            $breadCrumbs = array('Dashboard' => url('/'), 'Warehouses' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $user_id = Session::get('userId');
            $legal_id = DB::table('users')->where('user_id',$user_id)->select('legal_entity_id')->first();
            $legal_id = $legal_id->legal_entity_id ? $legal_id->legal_entity_id : '';
            $lp = new LogisticPartnersController();
            $lps = $lp->getLogisticPartners();
            foreach ($lps as $key => $value) {
                # code...
                $value->lp_logo = '/uploads/logistic_partner/' . $value->lp_logo;
            }
            return view('SellerWarehouses::createWarehouse')->with(['lps'=>$lps,'legal_id'=>$legal_id]);

            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }      
    }
    /**
     * [getPinLocations To get state and city of a pincode]
     * @param  [int] $pin [pincode]
     * @return [array]    [array contains pincode info like state and city]
     */
    public function getPinLocations($pin){
        try {
            $location = [];
            $locations = DB::table('cities_pincodes')->where('pincode',$pin)->select('city','state','pincode')->first();
            if(empty($locations)){
                $location['city'] = "NA";
                $location['state'] = "NA";
                $location['pincode'] = $pin;
                $locations = (object) $location;
            }
            return json_encode($locations);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getWarehouses To get warehouses list]
     * @param  [int] $id [logisticPartner id]
     * @return [array]     [list of warehouses]
     */
    public function getWarehouses($id){
        try {
            $legal_id = Session::get('legal_entity_id');
            $lp = new logisticPartnersController();
            $warehouses = $lp->getLpWarehouses($id,$legal_id);
            return $warehouses;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }  
    }

    /**
     * [saveWarehouse To save warehouse]
     * @return [array] [Array contains new warehouse id, status and message]
     */
    public function saveWarehouse(){
    try {
        $warehouses_data = Input::all();
        $i = 0;
        $loop =  count($warehouses_data['wh_id']);
        $legal_id = $warehouses_data['legal_id'];
        unset($warehouses_data['legal_id']);
        unset($warehouses_data['_token']);
        $complete_warehousedata = array();
        foreach ($warehouses_data as  $whvalue) {
            if($i<$loop){
                $complete_warehousedata[$i]['wh_id'] = $warehouses_data['wh_id'][$i];
                $complete_warehousedata[$i]['tin_number'] = $warehouses_data['tin_number'][$i];
                $complete_warehousedata[$i]['tinProof'] = $warehouses_data['tinProof'][$i];
                $complete_warehousedata[$i]['apobProof'] = $warehouses_data['apobProof'][$i];
            }
            $i++;
        }
        $sellerWarehouse = new SellerWarehouses();
        $result = $sellerWarehouse->saveWarehouse($complete_warehousedata,$legal_id);
        return $result;
     }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }  
    }
    /**
     * [getLogisticsPartners To get logistics partners list]
     * @return [array] [logistics partners list]
     */
    public function getLogisticsPartners() {
        try {
            $legalentitys = new SellerWarehouses();
            $legalentityData = $legalentitys->getLogisticsPartners();
            if($legalentityData){
                return json_encode(array('Records' => $legalentityData));
            }
            else{
                echo '{"Records":[],"TotalRecordsCount":0}';
                exit;  
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getLpWarehoues To get lp warehouses]
     * @return [array] [lp warehouses list]
     */
    public function getLpWarehoues() {
        try {
            $input = Input::all();
            $lpwarehouse = new SellerWarehouses();
            $data = $lpwarehouse->getLpWarehoues($input);
            return json_encode(array('Records' => $data));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [saveCustomWarehouse To save a warehouse]
     * @return [array] [Array contains status,message and warehouse id]
     */
    public function saveCustomWarehouse(){
        try {
            $data = Input::all();
            $legal_entity_id = Session::get('legal_entity_id');
            $lpwarehouse = new SellerWarehouses();
            $response = $lpwarehouse->saveCustomWarehouse($data,$legal_entity_id);
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [updateCustomWarehouse To update custom warehouse]
     * @param  [int] $id [Warehouse id]
     * @return [array]   [warehouse information]
     */
    public function updateCustomWarehouse($id){
        try {
            $data = Input::all();
            $lpwarehouse = new SellerWarehouses();
            $response = $lpwarehouse->updateCustomWarehouse($data,$id);
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [updateWarehouse To update a warehouse]
     * @param  [int] $id [warehouse id]
     * @return [array]     [With status and message]
     */
    public function updateWarehouse($id){
        try {
            $data = Input::all();
            $lpwarehouse = new SellerWarehouses();
            $response = $lpwarehouse->updateWarehouse($data,$id);
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getLegalentitys To get legal entities]
     * @return [array] [Legal entity list]
     */
    public function getLegalentitys() {
        $legalentitys = new SellerWarehouses();
        $legalentityData = $legalentitys->getLegalentitys();
        echo json_encode(array('Records' => $legalentityData));
    }
    /**
     * [deleteLpWharehouses To delete warehouse]
     * @param  [int] $le_wh_id [le_wh_id]
     * @return [view]           [Redirects ti warehouse view]
     */
    public function deleteLpWharehouses($le_wh_id) {
       
        $lpwarehouse = new SellerWarehouses();
        $warehouseConfig = DB::table('warehouse_config')->where('le_wh_id',$le_wh_id)->pluck(DB::raw('count(wh_loc_id)'))->all();
        $count = (isset($warehouseConfig[0]))?$warehouseConfig[0]:0;
        //$deleteLpwh = $lpwarehouse->deleteLpWharehouses($le_wh_id);
        if($count>1){
        return Redirect::to('/warehouse')->withFlashMessage("Warehouse can't be deleted as it is configured in WMS.");
        }
        else
        {
            $wh_conf_del = DB::table('warehouse_config')->where('le_wh_id',$le_wh_id)->delete();
            $wh_del = DB::table('legalentity_warehouses')->where('le_wh_id',$le_wh_id)->delete();
            
            return Redirect::to('/warehouse')->withFlashMessage("Warehouse deleted successfully!");
        }
    }
    /**
     * [deletePin To delete pincode]
     * @param  [int] $id [warehouse serviceables id]
     * @return [array]     [status,message and deleted data]
     */
    public function deletePin($id) {
       try {
            $status = false;
            $message = 'Unable to delete Pin';
            $delData = DB::table('wh_serviceables')->where('wh_serviceables_id',$id)->first();
            $lpwarehouse = new SellerWarehouses();
            $deletePin = $lpwarehouse->deletePin($id);
            if($deletePin){
                $status = true;
                $message = 'Pincode deleted successfully';
            }
            return json_encode([
                'status' => $status,
                'message' => $message,
                'delData' => $delData
                ]);
       } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        
    }
    /**
     * [checkUnique To check whether the warehouse with same name exists]
     * @return [array] [Returns whether the name is valid or not]
     */
    public function checkUnique(){
        try{
        $data = Input::all();
        $legal_entity_id = Session::get('legal_entity_id');
        $wh_name = isset($data['wh_name']) ? $data['wh_name'] : '';
        $result = false;
        $id = DB::table('legalentity_warehouses')->where(['lp_wh_name'=>$wh_name,'legal_entity_id'=>$legal_entity_id,'lp_wh_id' => null])->pluck('le_wh_id')->all();
        $id = isset($id[0]) ? $id[0] : 0;
        if(isset($data['le_wh_id'])){
            if($id == $data['le_wh_id']){
                return json_encode(array('valid' => true));
            }
        }
        if($id == 0){
            return json_encode(array('valid' => true));
        }
        else{
            return json_encode(array('valid' => false));
        }

        return json_encode(array('valid' => $result));
        }
     catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [checkUniquePJP To check whether the beat with same name exists]
     * @return [array] [Returns whether the name is valid or not]
     */
    public function checkUniquePJP(){
        try {
            $data = Input::all();
            $pjp_name = isset($data['pjp_name']) ? $data['pjp_name'] : '';
            $result = false;
            $id = DB::table('pjp_pincode_area')
            ->where(['pjp_name'=>$pjp_name,'le_wh_id'=>$data['le_wh_id'],'spoke_id'=>$data['spoke'] ])
            ->pluck('pjp_pincode_area_id')->all();
            $id = isset($id[0]) ? $id[0] : 0;
            if(isset($data['pjp_pincode_area_id']) && $id==$data['pjp_pincode_area_id']){
                return json_encode(array('valid' => true));
            }
            if($id == 0){
                return json_encode(array('valid' => true));
            }
            else{
                return json_encode(array('valid' => false));
            }

            return json_encode(array('valid' => $result));
         }    
          catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [checkUniquePJP To check whether the area with same name exists]
     * @return [array] [Returns whether the area is valid or not]
     */
    public function checkUniquePJPArea(){
        try {
            $data = Input::all();
            $areas = isset($data['pin_area']) ? $data['pin_area'] : [];
            $result = false;
            $area = [];
            foreach ($areas as $key => $value) {
                # code...
                 $id = DB::table('pjp_pincode_area as ppa')
                        ->leftJoin('pincode_area as pa','ppa.pjp_pincode_area_id','=','pa.pjp_pincode_area_id')
                        ->where('ppa.le_wh_id',$data['le_wh_id'])
                        ->where('pa.area_id',$value)
                        ->pluck('pa.pincode_area_id')->all();
                if(isset($id[0]))
                $area['id'][$key] = $id[0];
            }
            if(empty($area)){
                return json_encode(array('valid' => true));
            }
            else{
                return json_encode(array('valid' => false));
            }

            return json_encode(array('valid' => $result)); 
           

        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [mapArea To map area to pincode and city]
     * @return [array] [Return array with status and message]
     */
    public function mapArea(){
        try {
            $data = Input::all();
            $state_name = DB::table('zone')->where('zone_id',$data['area_state'])->select('name')->first();
            $id = DB::table('cities_pincodes')->insertGetId([
                'pincode' => $data['pincode_value1'],
                'city' => $data['area_city'],
                'officename' => $data['area_name'],
                'state' => $state_name->name,
                'country_id' => 99
                ]);
            if(empty($id)){
                return json_encode([
                    'status' => 0,
                    'message' => 'Unable to map area']);
            }
            else{
                return json_encode([
                    'status' => 1,
                    'message' => 'Area mapped successfully.'      
                    ]);
            }
        } catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
	/**
     * [checkHubPins check whether the pincode exists in given warehouse]
     * @param  [array] $data [wh id, pincode]
     * @return [string]       [lp_wh_name]
     */
    public function checkHubPins($data)
    {
        $data = explode('_', $data);
        $le_wh_id = isset($data[1])?$data[1]:'';
        $pincode = isset($data[0])?$data[0]:'';
        
        $dcType = DB::table('legalentity_warehouses')->where('le_wh_id',$le_wh_id)->pluck('dc_type')->all();
       
        //print_r($dcType);
        //added 118001 condition for dc
        
        if($dcType[0] != '118002' && $dcType[0]!='118001')
        {
            return 1;
        }
        else
        {
            $data = DB::table('legalentity_warehouses')->whereIN('dc_type',['118001','118002'])->pluck('le_wh_id')->all();
            $pinExists = DB::table('wh_serviceables')->whereIn('le_wh_id',$data)->where('pincode',$pincode)->pluck('le_wh_id')->all();            
            if(empty($pinExists))
            {
                return 1;
            }
            else
            {
                $dataWh = DB::table('legalentity_warehouses')->where('le_wh_id',$pinExists[0])->pluck('lp_wh_name')->all();
                return $dataWh[0];
            }
        }       
    }
    /**
     * [bussinessUnitsData To get bu list]
     * @return [array] [Bu list]
     */
    public function bussinessUnitsData()
    {
        $se_wh = new SellerWarehouses();
        $response=$se_wh->getBussinessUnitData();
        return $response;
    }	
    /**
     * [addSpoke To ass spoke]
     */
    public function addSpoke()
    {
        try
        {
            $response = 0;
            $data = Input::all();
            if(!empty($data))
            {
                $hubId = isset($data['hub_id']) ? $data['hub_id'] : 0; 
                $spokeName = isset($data['spoke_name']) ? trim($data['spoke_name']) : ''; 
                if($spokeName != '' && $hubId > 0)
                {
                    $insertData['le_wh_id'] = $hubId;
                    $insertData['spoke_name'] = $spokeName;
                    $response = DB::table('spokes')->insertGetId($insertData);
                }
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return 0;
        }
    }	
    /**
     * [getAllSpokes To get all spokes]
     * @return [array] [Spokes list]
     */
    public function getAllSpokes() {
        $data = Input::all();
        $path = isset($data['path']) ? $data['path'] : '';
        $le_wh_id = 0;
        if ($path != '') {
            $temp = explode(':', $path);
            $le_wh_id = isset($temp[1]) ? $temp[1] : 0;
        }
        return $this->getSpokes($le_wh_id);
    }
    /**
     * [getSpokes To get spokes]
     * @param  [int] $le_wh_id [warehouse id]
     * @return [array]           [Spokes list]
     */
    public function getSpokes($le_wh_id) {
        try {
            $data = DB::table('spokes')
                    ->where('le_wh_id', $le_wh_id)
                    ->select('spoke_id', 'spoke_name')
                    ->get()->all();

            $actions = '';
            $roleRepo = new RoleRepo();
            $dcAddPJP = $roleRepo->checkPermissionByFeatureCode('DC008');
            $dcEditSpoke = $roleRepo->checkPermissionByFeatureCode('DC014');
//            $dcDeletePJP = $roleRepo->checkPermissionByFeatureCode('DC010');
            $dcMovePJP = $roleRepo->checkPermissionByFeatureCode('DC012');
            $globalaccess=$roleRepo->checkPermissionByFeatureCode('GLB0001');
            if($globalaccess==1){
                $dcAddPJP=1;
                $dcEditSpoke=1;
                $dcMovePJP=1;
            }
            foreach ($data as $key => $value) {
                $actions = $actions . '';
                if ($dcAddPJP) {
                    $actions = '<span style="padding-left:10px;" ><a data-toggle="modal" id="addPJP" onclick="addPJP(' . $value->spoke_id . ')"><i class="fa fa-plus"></i></a></span>';
                }
                if ($dcEditSpoke) {
                    $actions = $actions . '<span style="padding-left:10px;" ><a href="javascript:void(0);" onclick="editSpoke(' . $value->spoke_id . ')" ><i class="fa fa-pencil"></i></a></span>';
                }
//                if ($dcDeletePJP) {
//                    $actions = $actions . '<span style="padding-left:10px;" ><a id ="deletePJP" href="javascript:void(0)" onclick="deletePJP(' . $value->pjp_pincode_area_id . ')"><i class="fa fa-trash-o"></i></a></span>';
//                }
                if ($dcMovePJP) {
                    $actions = $actions . '<span style="padding-left:10px;" ><a id ="movePJP" href="javascript:void(0)" onclick="movePJP(' . $value->spoke_id . ')"><i class="fa fa-exchange"></i></a></span>';
                }
                $value->actions = $actions;
            }
            if ($data) {
                return json_encode(array('Records' => $data));
            } else {
                echo '{"Records":[],"TotalRecordsCount":0}';
                exit;
            }
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [editSpoke To edit spokes list]
     * @param  [int] $spokeId [Spoke id]
     * @return [array]          [Spoke information]
     */
    public function editSpoke($spokeId)
    {
        try
        {
            $response = [];
            if($spokeId > 0)
            {
                $response = DB::table('spokes')
//                        ->leftJoin('dc_hub_mapping', 'dc_hub_mapping.hub_id', '=', 'spokes.le_wh_id')
                        ->where('spokes.spoke_id', $spokeId)
//                        ->select('spokes.spoke_name', 'spokes.spoke_id', 'dc_hub_mapping.hub_id', 'dc_hub_mapping.dc_id')
                        ->select('spokes.spoke_name', 'spokes.spoke_id', 'spokes.le_wh_id as hub_id')
                        ->first();
            }
            echo json_encode((array)$response);
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [moveSpoke To move spoke from one to other hub]
     * @return [int] [1]
     */
    public function moveSpoke()
    {
        try
        {
            $response = 0;
            $data = Input::all();
            if(!empty($data))
            {
                $hubId = isset($data['hub_id']) ? $data['hub_id'] : 0; 
                $spokeId = isset($data['spoke_id']) ? $data['spoke_id'] : 0; 
                if($spokeId > 0 && $hubId > 0)
                {
                    $updateData['le_wh_id'] = $hubId;
                    $response = DB::table('spokes')
                            ->where('spoke_id', $spokeId)
                            ->update($updateData);
                    if($response)
                    {
                        $response = 1;
                    }
                }
            }
            return $response;
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [checkUniqueSpoke check whether the spoke exists in given warehouse]
     * @return [array]       [returns valid/not valid]
     */
    public function checkUniqueSpoke()
    {
        try
        {
            $status = false;
            $data = Input::all();
            if(!empty($data))
            {
//                Log::info($data);
                $leWhId = isset($data['le_wh_id']) ? $data['le_wh_id'] : 0;
                $spokeId = isset($data['spoke_id']) ? $data['spoke_id'] : 0;
                $spokeName = isset($data['spoke_name']) ? $data['spoke_name'] : ''; 
                if($spokeName != '' && $leWhId > 0)
                {
//                    $existingSpokePicodes = DB::table('spokes')
//                            ->leftJoin('pjp_pincode_area', 'pjp_pincode_area.spoke_id', '=', 'spokes.spoke_id')
//                            ->leftJoin('pincode_area', 'pincode_area.pjp_pincode_area_id', '=', 'pjp_pincode_area.pjp_pincode_area_id')
//                            ->where('spokes.spoke_id', $spokeId)
//                            ->groupBy('pincode_area.pincode')
//                            ->pluck('pincode_area.pincode')->all();
//                    
//                    $newHubServiceables = DB::table('wh_serviceables')
//                            ->where('le_wh_id', $leWhId)
//                            ->orderBy('pincode', 'asc')
//                            ->pluck('pincode')->all();
//                    Log::info('existingSpokePicodes');
//                    Log::info($existingSpokePicodes);
//                    Log::info('newHubServiceables');
//                    Log::info($newHubServiceables);
//                    if(count(array_intersect($existingSpokePicodes, $newHubServiceables)) == count($existingSpokePicodes))
//                    {
                        DB::enableQueryLog();
                        $response = DB::table('spokes')
                                ->where(['spoke_name' => $spokeName, 'le_wh_id' => $leWhId])
                                ->where('spoke_id', '!=', $spokeId)
                                ->first(['spoke_id']);
                       // Log::info(DB::getQueryLog());
                        if(empty($response))
                        {
                           $status = true;
                        }
//                    }
                }
            }
            return json_encode(array('valid' => $status));
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [updateSpoke To update spoke name]
     * @return [array] [With status and message]
     */
    public function updateSpoke()
    {
        try
        {
            $message = 'Unable to update Spoke';
            $status = 0;
            $data = Input::all();
            if(!empty($data))
            {
                $spokeId = isset($data['spoke_id']) ? $data['spoke_id'] : 0;
                $spokeName = isset($data['spoke_name']) ? $data['spoke_name'] : ''; 
                if($spokeName != '' && $spokeId > 0)
                {
                    DB::table('spokes')
                            ->where('spoke_id', $spokeId)
                            ->update(['spoke_name' => $spokeName]);
                    $status = 1;
                    $message = "Updated Successfully";
                }
            }
            return json_encode(['status' => $status, 'message' => $message]);
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [exportSpokes To export spokes list]
     * @param  [int] $hubId [hub id]
     */
    public function exportSpokes($hubId)
    {
        try
        {
            $fileName = 'Beats_'.$hubId;
            if($hubId > 0)
            {
                $sellerWarehouse = new SellerWarehouses();
                $hubCollection = $sellerWarehouse->getBeatsInfo($hubId);
                $hubCollection = json_decode(json_encode($hubCollection), true);
//                echo "<pre>";print_R($hubCollection);die;
                Excel::create($fileName, function($excel) use ($hubCollection) {
                    $excel->setTitle('Beats List');
                    $excel->sheet('Beats List', function($sheet) use ($hubCollection) {
                        $sheet->fromArray($hubCollection, null, 'A1', false, true);
                    });
                })->download('xls');
//                })->store('xls', storage_path('excel/exports'));
            }
            return storage_path('excel/exports/').$fileName.'.xls';
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [getAllSpokesBeats Get spokes and beats under  a warehouse]
     * @param  [int] $le_wh_id [warehouse id]
     * @return [array]           [spokes and beats list]
     */
    public function getAllSpokesBeats($le_wh_id = null) {
        try {
            $data = Input::all();
            $path = isset($data['path']) ? $data['path'] : '';
            $spokeId = 0;
            if ($path != '') {
                if (strpos($path, ':') !== false) {
                    $path2 = explode(':', $path);
                    if (is_array($path2)) {
                        $path = isset($path2[1]) ? $path2[1] : $path;
                    }
                }
                $le_wh_id = $path;
            }
            DB::enableQueryLog();
//            $data = DB::table('pjp_pincode_area as ppa')
            $data = DB::table('spokes')
                    ->leftJoin('pjp_pincode_area as ppa', 'ppa.spoke_id', '=', 'spokes.spoke_id')
//                    ->leftJoin('users', 'users.user_id', '=', 'ppa.rm_id')
//                    ->leftJoin('pincode_area as pa', 'ppa.pjp_pincode_area_id', '=', 'pa.pjp_pincode_area_id')
//                    ->where('ppa.le_wh_id',$le_wh_id)
                    ->where('spokes.le_wh_id', $le_wh_id)
//                    ->where('ppa.spoke_id', $spokeId)
                    ->select('spokes.spoke_id', 'spokes.spoke_name', 'ppa.days', 'ppa.pjp_name', 'ppa.pjp_pincode_area_id', 
                            DB::raw('GetUserName (`ppa`.`rm_id`, 2) as rm_name'))
//                    ->groupBy('ppa.pjp_pincode_area_id')
//                    ->orderBy('spokes.spoke_id')
                    ->get()->all();
            //Log::info(DB::getQueryLog());
            $actions = '';
            $roleRepo = new RoleRepo();
            $dcAddPJP = $roleRepo->checkPermissionByFeatureCode('DC008');
            $dcEditPJP = $roleRepo->checkPermissionByFeatureCode('DC009');
            $dcDeletePJP = $roleRepo->checkPermissionByFeatureCode('DC010');
            $dcMovePJP = $roleRepo->checkPermissionByFeatureCode('DC012');
            $globalaccess=$roleRepo->checkPermissionByFeatureCode('GLB0001');
            if($globalaccess==1){
                $dcAddPJP=1;
                $dcEditPJP=1;
                $dcDeletePJP=1;
                $dcMovePJP=1;
            }
            foreach ($data as $key => $value) {
                $actions = '';
                if($value->pjp_name != '')
                {
                    if ($dcEditPJP) {
                        $actions = $actions . '<span style="padding-left:10px;" ><a href="javascript:void(0);" onclick="editPJP(' . $value->pjp_pincode_area_id . ')" data-target="#basicvalCodeModal4"><i class="fa fa-pencil"></i></a></span>';
                    }
                    if ($dcDeletePJP) {
                        $actions = $actions . '<span style="padding-left:10px;" ><a id ="deletePJP" href="javascript:void(0)" onclick="deletePJP(' . $value->pjp_pincode_area_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                    }
                }
                $value->actions = $actions;
                $value->spoke_name = '<a href="javascript:void(0);" onclick="editSpoke('.$value->spoke_id.')" >'.$value->spoke_name.'</a>';
            }

            if ($data) {
                return json_encode(array('Records' => $data));
            } else {
                echo '{"Records":[],"TotalRecordsCount":0}';
                exit;
            }
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [gethubValidate To validate selected hub before mapping  whether  it is mapped to ther warehouse ]
     * @param  [int] $id [hub id]
     * @return [array]     [With status & message]
     */
    public function gethubValidate($id){
        $data=Input::all();
        $lpwarehouse = new SellerWarehouses();
        $response=$lpwarehouse->getselectedhubValidates($id,$data['hubs']);
        return $response;
    }
    /**
     * [getFcValidate To validate selected Fc before mapping  whether  it is mapped to ther warehouse ]
     * @param  [int] $id [warehouse id]
     * @return [array]     [With status & message]
     */
    public function getFcValidate($id){
        $data=Input::all();
        $lpwarehouse=new SellerWarehouses();
        $response=$lpwarehouse->getFcValidate($id,$data['fcs'],$data['le_wh_id']);
        return $response;

    }

    /**
     * [index redirects to GST Address landing page]
     * @return [view] [Redirects to GSt Address view]
     */

    public function gstAddress() {
        try {
            $roleRepo = new RoleRepo();
            $access_check=$roleRepo->checkPermissionByFeatureCode('GSTAD001');
            if(!$access_check){
                return redirect()->to('/');
            }
            parent::Title(' Ebutor - GST Address');
            $breadCrumbs = array('Home' => url('/'), 'Logistics' =>'','GST Address' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $user_id = Session::get('userId');
            return view('SellerWarehouses::gst_list');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

     /**
     * [listGstAddress To get GST Address list]
     * @return [array] [GST Address list]
     */

    public function listGstAddress() {
        try {
            $results =  $this->swModel->gstAddressList();
            if($results){
                return json_encode(array('Records' => $results));
            }
            else{
                echo '{"Records":[],"TotalRecordsCount":0}';
                exit;  
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    /**
     * [addCustom To add GST Address]
     * @return  [Redirects to add GSt Address view]
     */
    public function addGst(){
        try {
            $roleRepo = new RoleRepo();
            $access_check=$roleRepo->checkPermissionByFeatureCode('GSTAD002');

            if(!$access_check){

                return redirect()->to('/');
            }

            parent::Title('Add GST Address');
            $breadCrumbs = array('Home' => url('/'), 'Logistics' =>'', 'Add GST Address' => '/warehouse/gstaddress', 'Add GST Address' => '/warehouse/addGst');
            parent::Breadcrumbs($breadCrumbs); 
            $states = DB::table('zone')->select('zone.zone_id as state_id','zone.name as state')->where('country_id',99)->orderByRaw("FIELD(name,'Telangana') DESC")->get();
             $country = DB::table('countries')->where('country_id',99)->select('country_id','name as country')->get();             
            $userId = Session::get('userId');
            return view('SellerWarehouses::addGstAddress')
                   ->with(['states' => $states, 
                         'countries' => $country, 
                         ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex;
        }
    }

    /**
     * [saveGStAddress To save a GST Address
     * @return [array] [Array contains status,message and Gst id]
     */
    public function saveGStAddress(){
        try {
            $data = Input::all();
            $gstaddress = new SellerWarehouses();
            $response = $gstaddress->saveGStAddress($data);
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [checkState To check whether the State same name exists]
     * @return [array] [Returns whether the name is valid or not]
     */
    public function checkState(){
        try{
        $data = Input::all();
        $gst_state = isset($data['gst_state']) ? $data['gst_state'] : '';
        $bil_id = isset($data['billing_id']) ? $data['billing_id'] : '';
        $status = false;
        $id = DB::table('legal_entity_gst_addresses')->where(['state'=> $gst_state])->pluck('billing_id');
        $id = isset($id[0]) ? $id[0] : 0;

        if(isset($data['billing_id'])){
            if($id == $data['billing_id']){
                return json_encode(array('valid' => true));
            } 
        }
        if($id == 0){
            return json_encode(array('valid' => true));
        }else{
            return json_encode(array('valid' => false));
        }
        return json_encode(array('valid' => $status));
        }
     catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
        /**
     * [editCustom To  edit GstAddress ]
     * @param  [int] $id [GstAddress id]
     * @return [view]     [Redirects to edit editGstAddress]
     */
    public function editGstAddress($id){
      try{
        $roleRepo = new RoleRepo();
        $access_check=$roleRepo->checkPermissionByFeatureCode('GSTAD003');
            if(!$access_check){
                return redirect()->to('/');
            }
        $gst_edit_list=DB::table('legal_entity_gst_addresses')->where('billing_id',$id)->first();
        parent::Title('Edit GST Address');
        $breadCrumbs = array('Home' => url('/'),'Logistics' =>'', 'GST Address' => '/warehouse/gstaddress', 'Edit GST Address' => '#');
        parent::Breadcrumbs($breadCrumbs);
        $states = DB::table('zone')->select('zone.zone_id as state_id','zone.name as state')->where('country_id',99)->orderByRaw("FIELD(name,'Telangana') DESC")->get();
        $country = DB::table('countries')->where('country_id',99)->select('country_id','name as country')->get();

        $currUrl = URL::current(); 
        $urlArray = explode('/',$currUrl);
        if(isset($urlArray[0]) && $urlArray[0]=='https'){
        $mapurl = "https://maps.googleapis.com/maps/api/js?key=".env('GOOGLE_MAP_URL_KEY')."&libraries=places";
        }
        else{
        $mapurl = "http://maps.googleapis.com/maps/api/js?key=".env('GOOGLE_MAP_URL_KEY')."&libraries=places";    
        }
        return view('SellerWarehouses::editGstAddress')->with(['states' => $states, 
            'countries' => $country,
            'billing_id' => $id,'gst_edit_list' =>$gst_edit_list
                ]);

      }
      catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [deleteLpWharehouses To delete GST ADDRESS]
     * @param  [int] $le_wh_id [le_wh_id]
     * @return [view]           [Redirects ti GST Address view]
     */
    public function deleteGstAddress($id) {
        $roleRepo = new RoleRepo();
        $access_check=$roleRepo->checkPermissionByFeatureCode('GSTAD004');
            if(!$access_check){
                return redirect()->to('/');
            }
        $gstdel = DB::table('legal_entity_gst_addresses')->where('billing_id',$id)->delete();
            
        return Redirect::to('/warehouse/gstaddress')->withFlashMessage("GST Address deleted successfully!");
    }
        /**
     * [updateGstAddress To update custom GST Address]
     * @param  [int] $id [billing id]
     * @return [array]   [GST Address information]
     */
    public function updateGstAddress($id){
        try {
            $data = Input::all();
            $gstaddress = new SellerWarehouses();
            $response = $gstaddress->updateGstAddress($data,$id);
            return $response;
        } catch (\ErrorException $ex) {
            return json_encode(['status'=>false,'message'=>'Failed to Update Records']);
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

        /**
     * [checkState To check whether the GSTIN exists]
     * @return [array] [Returns whether the gstin is valid or not]
     */
    public function checkGstin(){
        try{
          
            $data = Input::all();
            $gstin = isset($data['tin_number']) ? $data['tin_number'] : '';
            $bil_id = isset($data['billing_id']) ? $data['billing_id'] : '';
            $status = false;

            if(!\Utility::check_gst_state_code($gstin))
            {
                return json_encode(array('valid' => false));
            } else {
                return json_encode(array('valid' => true));
            }

            $id = DB::table('legal_entity_gst_addresses')->where('gstin',$gstin)->pluck('billing_id');
            $id = isset($id[0]) ? $id[0] : 0;

            if(isset($data['billing_id'])){
                if($id == $data['billing_id']){
                    return json_encode(array('valid' => true));
                } 
            }
            if($id == 0){
                return json_encode(array('valid' => true));
            }else{
                return json_encode(array('valid' => false));
            }
            return json_encode(array('valid' => $status));
        }

     catch (\ErrorException $ex) {
            $response['message'] = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
?>