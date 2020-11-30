<?php

namespace App\Modules\DeviceDetails\Controllers;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;
use App\Modules\DeviceDetails\Models\DeviceDetailsModel;
use Session;
use View;
use Illuminate\Support\Facades\Input;
use Log;
use Redirect;
use DB;
use Response;
use Illuminate\Support\Facades\Cache;

class DeviceDetailsController extends BaseController
{
    protected $deviceObj;
    protected $roleAccess;
    private $objdevice;
    private $objCommonGrid = '';


    public function __construct(RoleRepo $roleAccess, DeviceDetailsModel $deviceObj){
        try{
            $this->objCommonGrid = new commonIgridController();
            parent::__construct();
              $this->deviceObj = $deviceObj;
            $this->roleAccess = $roleAccess;

             $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                return $next($request);

            }); 
          
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }


    public function index()
    {
        try
        {
            parent::Breadcrumbs(array('Home' => '/', 'Administration' => '#', trans('device_dtls.heading.index_page_title') => '#'));

         $warehouse=$this->deviceObj->GetWarehouses();
         
         $beat=$this->deviceObj->GetBeats();

         $hubs=$this->deviceObj->GetHubs(); 

         //echo "<pre/>";print_r($beat);exit;  
        return View('DeviceDetails::index',['wareHouses' => json_decode(json_encode($warehouse)),'beat' => json_decode(json_encode($beat)),'hubs' => json_decode(json_encode($hubs))]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function devicedetailslist(Request $request)
    {
        
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }

        if(count($request->input('$fillter')) > 0) {
            $filter .= ' and'.$request->input('$fillter');
            }
        
         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("firstname", $filter, false);
        
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("b_name", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("mobile_no", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("device_id", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("registration_id", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        //now writing for ajax results for same function

         /*$fieldQuery = $this->objCommonGrid->makeIGridToSQL("hub_id", $filter, false);

         $hudexplode=explode("=",$fieldQuery);
         //print_r($hudexplode);
        if($hudexplode[1]!='' && $hudexplode[1]!=0){
            $makeFinalSql[] = $fieldQuery;
        }*/

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("pjp_pincode_area_id", $filter, false);
        $beatexplode=explode("=",$fieldQuery);
        if($beatexplode[1]!='' && $beatexplode[1]!=0){
            $makeFinalSql[] = $fieldQuery;
        }

        $orderBy = $request->input('%24orderby');
        if($orderBy==''){
            $orderBy = $request->input('$orderby');
        }

        $page="";
        $pageSize="";
        if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
            $page = $request->input('page');
            $pageSize = $request->input('pageSize');
        }

            $content = $this->deviceObj->getDeviceList($makeFinalSql, $orderBy, $page, $pageSize);
            //echo "<pre/>";print_r($content);exit;
            return $content;
            
    }

    public function DeviceWarehouse(Request $request){

       try
        {
         $filter = Input::get("warehouseid");
        
        
         $hubsreturn='<option value="">Select Hub</option>';
         $ajaxwarehousehubs=$this->deviceObj->getAjaxHubsList($filter);

         //print_r($ajaxwarehousehubs);
    
           foreach ($ajaxwarehousehubs as $hubs) {
               
              

                $hubsreturn.='<option value="'.$hubs["le_wh_id"].'">'.$hubs["lp_wh_name"].'</option>';
           }

       return $hubsreturn;
     } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function DeviceHubs(Request $request){

       try
        {
         $filter = Input::get("hubsid");
        
        
         $hubsreturn='<option value=" ">Select Beats</option>';
         $ajaxwarehousebeats=$this->deviceObj->getAjaxBeatsList($filter);

           foreach ($ajaxwarehousebeats as $beats) {
               
              

                $hubsreturn.='<option value="'.$beats["pjp_pincode_area_id"].'">'.$beats["pjp_name"].'</option>';
           }

           //print_r($hubsreturn);

       return $hubsreturn;
     } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
