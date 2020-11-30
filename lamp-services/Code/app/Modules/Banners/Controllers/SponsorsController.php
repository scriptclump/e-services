<?php
namespace App\Modules\Banners\Controllers;

use DB;
use Log;
use View;
use Cache;
use Input;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;
use App\Central\Repositories\RoleRepo;
use \App\Modules\Banners\Models\Sponsor;
use App\Central\Repositories\ProductRepo;
use App\Modules\Roles\Models\Role;
use \App\Modules\Banners\Models\Banner;
use Excel;

class SponsorsController extends BaseController {

     private $objCommonGrid = '';

    public function __construct() {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                     Redirect::to('/login')->send();
            }
            $this->roleAccess = new RoleRepo();
            $this->sponsor = new Sponsor();
            $this->objCommonGrid = new commonIgridController();
            $this->roleObj = new Role();
            $this->banners = new Banner();

            $sponsorindexaccess = $this->roleAccess->checkPermissionByFeatureCode('SPR001');

                 if(!$sponsorindexaccess){
                    echo "You don't have access,Please Contact Admin";die();
                 }
             return $next($request);
        });
    }

    public function index() {
        try {
            $breadCrumbs = array('Home' => url('/'),'Sponsors' => 'sponsors','Dashboard'=>'#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('banners.heading.index_page_sponsor'));


            $addbnnerpermission=$this->roleAccess->checkPermissionByFeatureCode('ADDSPR001');
            $excelsponsorpermission=$this->roleAccess->checkPermissionByFeatureCode('EXLSPR001');
            $bannertype=$this->banners->GetBannerType();
            $Json=json_decode($this->roleObj->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);      
            $warehouse=$this->roleObj->GetWareHouses($filters);
            $warehouse = json_decode(json_encode($warehouse), True);
            $Jsonhubs=json_decode($this->roleObj->getFilterData(6), 1);
            $filtershubs = json_decode($Jsonhubs['sbu'], 1);
            $hubs=$this->banners->GetHubsByaccesslevel($filtershubs);
            $hubs = json_decode(json_encode($hubs), True);
            $beats=$this->banners->GetBeats(); 
         return View('Banners::sponsors',['addprms' => json_decode(json_encode($addbnnerpermission)),'exlsprprms' => json_decode(json_encode($excelsponsorpermission)),'bnrtype'=>json_decode(json_encode($bannertype)),'dcs' => json_decode(json_encode($warehouse)),'hubs' =>json_decode(json_encode($hubs)),'beats'=> json_decode(json_encode($beats))]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }

    public function sponsorslist(Request $request){

        $makeFinalSql = array();
        $filter = $request->input('$filter');
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("sponsor_name", $filter, false);
        
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("lname", $filter, false);
        $fieldQuery =str_replace('lname', 'IFNULL(getLeWhName(le_wh_id),"All")', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("hname", $filter, false);
        $fieldQuery =str_replace('hname', 'IFNULL(getLeWhName(hub_id),"All")', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $filter =str_replace("frequency", "frqncy", $filter);
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("frqncy", $filter, false);
        $fieldQuery =str_replace('frqncy', 'frequency', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("click_cost", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("impression_cost", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("from_date", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("to_date", $filter, false);
        if($fieldQuery!=''){
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
            $editdeletepermission=$this->roleAccess->checkPermissionByFeatureCode('ETDLTSPR001');

            $content = $this->sponsor->getsponsorList($makeFinalSql, $orderBy, $page, $pageSize,$editdeletepermission);
            return $content;
    }
  

    public function editsponsor($id){

        $breadCrumbs = array('Home' => url('/'),'Sponsors' => 'sponsors','Edit Sponsors' => '#');
            parent::Breadcrumbs($breadCrumbs);

            $banneredit=$this->sponsor->EditSponsor($id);

             parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('banners.heading.index_page_sponsor')." - ".$banneredit[0]['sponsor_name']);
            /*$warehouse=$this->sponsor->GetWareHouses(); 
            $hubs=$this->sponsor->GetHubs();
            $beats=$this->sponsor->GetBeats();*/

                $Json=json_decode($this->roleObj->getFilterData(6), 1);
                $filters = json_decode($Json['sbu'], 1);            
                $warehouse=$this->roleObj->GetWareHouses($filters);
                $warehouse = json_decode(json_encode($warehouse), True);
                $Jsonhubs=json_decode($this->roleObj->getFilterData(6), 1);
                $filtershubs = json_decode($Jsonhubs['sbu'], 1);
                //$hubs=$this->banners->GetHubs();
                $hubs=$this->roleObj->GetHubs($filtershubs);
                $hubs = json_decode(json_encode($hubs), True);
                $beats=$this->sponsor->GetBeats();
                $type=$this->sponsor->GetType();
                $bannertype=$this->sponsor->GetBannerType();
                $addoredit='Edit Sponsor';
        
            return view::make('Banners::addbanner',['editdata'=>$banneredit,'dcs' => json_decode(json_encode($warehouse)),'hubs' =>json_decode(json_encode($hubs)),'beats'=> json_decode(json_encode($beats)),'type'=>json_decode(json_encode($type)),'bnrtype'=>json_decode(json_encode($bannertype)),'addoredit'=>json_decode(json_encode($addoredit))]);

    }

    public function DeleteSponsor(Request $request){

             $deleteData = $request->input('deleteData');
        
             $deleterecord=$this->sponsor->DeleteSponsorModel($deleteData);
         

            if($deleterecord!='')
            {
             $success=Session::flash('alert-success',$deleterecord);
             }
             return $success;
    }

    public function getHubs(Request $request){

       try
        {
            $filter = Input::get("warehouseid");
            $datahubid=Input::get("hdnhubid");
            $hubsreturn='<option value="">Select Hub</option>';
            $ajaxwarehousehubs=$this->banners->getAjaxHubsList($filter);

           foreach ($ajaxwarehousehubs as $hubs) {

            $selected = ($datahubid==$hubs["le_wh_id"]) ?"selected":"";
               
            $hubsreturn.='<option value="'.$hubs["le_wh_id"].'" '.$selected.'>'.$hubs["lp_wh_name"].'</option>';
           }
       return $hubsreturn;
     } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function getBeats(Request $request){

       try
        {
         $filter = Input::get("hubid");
         $hdnbeatid=Input::get('hdnbeatid');
        
         $beatsreturn='<option value=" ">Select Beats</option>';
         $ajaxwarehousebeats=$this->banners->getAjaxBeatsList($filter);

           foreach ($ajaxwarehousebeats as $beats) {
               
            $selected = ($hdnbeatid==$beats["pjp_pincode_area_id"]) ?"selected":""; 

            $beatsreturn.='<option value="'.$beats["pjp_pincode_area_id"].'" '.$selected.'>'.$beats["pjp_name"].'</option>';
           }
       return $beatsreturn;
     } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function bannerType(Request $request){


        $data=Input::all();

                $resreturn='<option value="">Select</option>';
                $bannerajaxlist=$this->banners->BannerAjaxList($data);

        if($data['bannertype']==16703)
        {

            foreach ($bannerajaxlist as $product) 
            {
                $selected = ($data["listitem"]==$product["product_id"]) ?"selected":"";
                $resreturn.='<option value="'.$product["product_id"]. '" '.$selected.' > '.$product["product_title"].'</option>';
            }
        }elseif($data['bannertype']==16704){

             foreach ($bannerajaxlist as $cat) {
              
                $selected = ($data["listitem"]==$cat["category_id"]) ?"selected":"";
                $resreturn.='<option value="'.$cat["category_id"].'" '.$selected.'>'.$cat["cat_name"].'</option>';
           }
       }elseif($data['bannertype']==16701){

        $bannerajaxlist = json_decode($roleObj->getFilterData(9), 1);

             foreach ($bannerajaxlist['brand'] as $man =>$value) {
              
                $selected = ($data["listitem"]==$man) ?"selected":"";
                $resreturn.='<option value="'.$man.'" '.$selected.'>'.$value.'</option>';
           }
       }elseif($data['bannertype']==16702){

        $bannerajaxlist = json_decode($roleObj->getFilterData(7), 1);

             foreach ($bannerajaxlist['brand'] as $man => $value) {
               
              $selected = ($data["listitem"]==$man) ?"selected":"";

              $resreturn.='<option value="'.$man.'" '.$selected.'>'.$value.'</option>';
           }
       }


       return $resreturn;


        }

        public function sponsor(Request $request) {
    try {

              $breadCrumbs = array('Home' => url('/'),'Sponsors' => '/sponsors');
              parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('banners.heading.index_page_title'));
               parent::Breadcrumbs($breadCrumbs);
                $Json=json_decode($this->roleObj->getFilterData(6), 1);
                $filters = json_decode($Json['sbu'], 1);            
                $warehouse=$this->roleObj->GetWareHouses($filters);
                $warehouse = json_decode(json_encode($warehouse), True);
                $Jsonhubs=json_decode($this->roleObj->getFilterData(6), 1);
                $filtershubs = json_decode($Jsonhubs['sbu'], 1);
                $hubs=$this->roleObj->GetHubs($filtershubs);
                $hubs = json_decode(json_encode($hubs), True);
                $beats=$this->banners->GetBeats(); 
                $type=$this->banners->GetType();
                $bannertype=$this->banners->GetBannerType();             
                $addoredit='Add Sponsors';
                return  View('Banners::addbanner',['dcs' => json_decode(json_encode($warehouse)),'hubs' =>json_decode(json_encode($hubs)),'beats'=> json_decode(json_encode($beats)),'type'=>json_decode(json_encode($type)),'bnrtype'=>json_decode(json_encode($bannertype)),'addoredit'=>json_decode(json_encode($addoredit))]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }


    public function createsponsorsExport(){
         try{
            $flag ='';
            $filterData = Input::get();
            //$listids=implode(',',$filterData['banner_list']);
             if(!empty($filterData['banner_list'])){

                if(in_array('NULL', $filterData['banner_list'])){
                    $listids='NULL';
                }else{
                $listids=implode(',',$filterData['banner_list']);
                $listids=trim($listids,',');
                $listids="'".$listids."'";
               }
            
            }else{
              $listids='NULL';  
            }
            $listids=trim($listids,',');
            if(!empty($filterData['warehouse'])){
                if(in_array(0, $filterData['warehouse'])){
                    $warehouse='NULL';
                }else{
                $warehouse=implode(',',$filterData['warehouse']);
                $warehouse=trim($warehouse,',');
                $warehouse="'".$warehouse."'";
               }
            //$warehouse=implode(',',$filterData['warehouse']);
            }else{
              $warehouse='NULL';  
            }
             if(!empty($filterData['hubs'])){
                 if(in_array(0, $filterData['hubs'])){
                    $hubs='NULL';
                }else{
                $hubs=implode(',',$filterData['hubs']);
                $hubs=trim($hubs,',');
                $hubs="'".$hubs."'";
               }
            //$hubs=implode(',',$filterData['hubs']);
            }else{
              $hubs='NULL';  
            }
            
             if(!empty($filterData['beats'])){
            //$beats=implode(',',$filterData['beats']);
                 if(in_array(0, $filterData['beats'])){
                    $beats='NULL';
                }else{
                 $beats=implode(',',$filterData['beats']);
                 $beats=trim($beats,',');
                $beats="'".$beats."'";
               }
            }else{
              $beats='NULL';  
            }
            
            $fdate = (isset($filterData['fromdate']) && !empty($filterData['fromdate'])) ? $filterData['fromdate'] : date('Y-m').'-01';
            $fdate = str_replace('/', '-', $fdate);
            $fromDate=  date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['todate']) && !empty($filterData['todate'])) ? $filterData['todate'] : date('Y-m').'-01';
            $date = str_replace('/', '-', $tdate);
            $TDate=  date('Y-m-d', strtotime($date)); 
            $flag=$filterData['select_flags'];
            $details = json_decode(json_encode($this->sponsor->getreportsData_forsponsors($fromDate,$TDate,$listids,$warehouse,$hubs,$beats,$flag)), true);
            Excel::create('Sponsor Reports - '. date('Y-m-d'),function($excel) use($details) {
                $excel->sheet('Sponsor Reports', function($sheet) use($details) {          
                $sheet->fromArray($details);
                });      
            })->export('csv');

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('banners.errorInputData')));
        }
    } 

    public function blockSponsor(){
      
       try{
        $data=Input::all();
        $sponsorid=$data['sponsorId'];
        $sts=$data['status'];

        $bnrsts=$this->sponsor->changeSponsorSts($sponsorid,$sts);

        return $bnrsts;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }

    }