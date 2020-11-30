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
use \App\Modules\Banners\Models\Banner;
use App\Central\Repositories\ProductRepo;
use App\Modules\Roles\Models\Role;
use Excel;

class BannersController extends BaseController {

     private $objCommonGrid = '';

    public function __construct() {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                     Redirect::to('/login')->send();
            }
            $this->roleAccess = new RoleRepo();
            $this->banners = new Banner();
            $this->objCommonGrid = new commonIgridController();
            $this->_productRepo = new ProductRepo();
            $this->roleObj = new Role(); 
            $this->_userId = Session::get('userId');

            $bannerindexaccess = $this->roleAccess->checkPermissionByFeatureCode('BAN001');

                 if(!$bannerindexaccess){
                    echo "You don't have access,Please Contact Admin";die();
                 }
             return $next($request);
        });
    }

    public function index() {
        try {
            $breadCrumbs = array('Home' => url('/'),'Banners/Pop-ups' => '#', 'Dashboard' => '#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('banners.heading.index_page_title'));
              
            $addbnnerpermission=$this->roleAccess->checkPermissionByFeatureCode('ADDBNR001');
            $excelbannerpermission=$this->roleAccess->checkPermissionByFeatureCode('EXLBNR001');
            $excelpopuppermission=$this->roleAccess->checkPermissionByFeatureCode('EXLPOP001');
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
         return View('Banners::bannersindex',['addprms' => json_decode(json_encode($addbnnerpermission)),'exlbnrper' => json_decode(json_encode($excelbannerpermission)),'exlpopper' => json_decode(json_encode($excelpopuppermission)),'bnrtype'=>json_decode(json_encode($bannertype)),'dcs' => json_decode(json_encode($warehouse)),'hubs' =>json_decode(json_encode($hubs)),'beats'=> json_decode(json_encode($beats))]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }

    public function bannerlist(Request $request){

        $makeFinalSql = array();
        $filter = $request->input('$filter');
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("banner_name", $filter, false);
        
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuerycase = " CASE when display_type=16601 then 'Banner' else 'Popup' END "; 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("display_type", $filter, false);
        //print_r($fieldQuery);exit;
        $fieldQuery =str_replace('display_type', $fieldQuerycase, $fieldQuery);
        //print_r($fieldQuery);exit;
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
            $editdeletepermission=$this->roleAccess->checkPermissionByFeatureCode('ETDLT001');

            $content = $this->banners->getbannerList($makeFinalSql, $orderBy, $page, $pageSize,$editdeletepermission);
            
            return $content;
    }

    public function banner(Request $request) {
    try {

              $breadCrumbs = array('Home' => url('/'),'Banners/Pop-ups' => 'banners');
              parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('banners.heading.index_page_title'));
               parent::Breadcrumbs($breadCrumbs);
 
                //$warehouse=$this->banners->GetWareHouses();
                $Json=json_decode($this->roleObj->getFilterData(6), 1);
                $filters = json_decode($Json['sbu'], 1);            
                $warehouse=$this->roleObj->GetWareHouses($filters);
                $warehouse = json_decode(json_encode($warehouse), True);
                $Jsonhubs=json_decode($this->roleObj->getFilterData(6), 1);
                $filtershubs = json_decode($Jsonhubs['sbu'], 1);
                //$hubs=$this->banners->GetHubs();
                $hubs=$this->roleObj->GetHubs($filtershubs);
                $hubs = json_decode(json_encode($hubs), True);
                $beats=$this->banners->GetBeats(); 
                $type=$this->banners->GetType();
                $bannertype=$this->banners->GetBannerType();             
                $addoredit='Add Banners';
                return  View('Banners::addbanner',['dcs' => json_decode(json_encode($warehouse)),'hubs' =>json_decode(json_encode($hubs)),'beats'=> json_decode(json_encode($beats)),'type'=>json_decode(json_encode($type)),'bnrtype'=>json_decode(json_encode($bannertype)),'addoredit'=>json_decode(json_encode($addoredit))]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    } 

    public function addbanners(Request $request){
   
        $data=Input::all();
        $bannersinsert='';

        $bannerupdate='';

        $url="";

        if($data['type']!=16603 && $data['bannerurl_edited']==''){

        $photo =$request->file('bannerimage');
        $EntityType="products";
        $type=1;
        
        if(is_object($photo)){
             $url=$this->_productRepo->uploadToS3($photo,$EntityType,$type);
        }
      }else{
             $url=$data['bannerurl_edited'];
      }

        if(empty(trim($data['banner_id']))){
   
             $bannersinsert=$this->banners->SaveBanners($data,$url); 

         }elseif(!empty(trim($data['banner_id']))){
            
             $bannerupdate=$this->banners->UpdateBanner($data,$url);
         }

         if($data['type']==16601 || $data['type']==16602){

         if($bannersinsert!=''){

             $success=Session::flash('alert-success',$bannersinsert);
             return redirect('/banners')->with('status', $bannersinsert);
              
         }elseif($bannerupdate!=''){
            
            $success=Session::flash('alert-success',$bannerupdate);
            return redirect('/banners')->with('status', $bannerupdate);
         }
     }elseif($data['type']==16603){

         if($bannersinsert!=''){

             $success=Session::flash('alert-success',$bannersinsert);
             return redirect('/sponsors')->with('status', $bannersinsert);
              
         }elseif($bannerupdate!=''){
            
            $success=Session::flash('alert-success',$bannerupdate);
            return redirect('/sponsors')->with('status', $bannerupdate);
         }
     }



    }  

    public function editbanner($id){

         $breadCrumbs = array('Home' => url('/'),'Banners' => '/banners','Edit Banners'=>'#');
            parent::Breadcrumbs($breadCrumbs);

           
            $banneredit=$this->banners->EditBanners($id);
            /*$warehouse=$this->banners->GetWareHouses(); 
            $hubs=$this->banners->GetHubs();
            $beats=$this->banners->GetBeats();*/
                $Json=json_decode($this->roleObj->getFilterData(6), 1);
                $filters = json_decode($Json['sbu'], 1);            
                $warehouse=$this->roleObj->GetWareHouses($filters);
                $warehouse = json_decode(json_encode($warehouse), True);
                $Jsonhubs=json_decode($this->roleObj->getFilterData(6), 1);
                $filtershubs = json_decode($Jsonhubs['sbu'], 1);
                //$hubs=$this->banners->GetHubs();
                $hubs=$this->roleObj->GetHubs($filtershubs);
                $hubs = json_decode(json_encode($hubs), True);
                $beats=$this->banners->GetBeats();
                $type=$this->banners->GetType();
                $bannertype=$this->banners->GetBannerType(); 
                $addoredit='Edit Banner';

            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('banners.heading.index_page_title')." -" . $banneredit[0]['banner_name']);            
        
            return view::make('Banners::addbanner',['editdata'=>$banneredit,'dcs' => json_decode(json_encode($warehouse)),'hubs' =>json_decode(json_encode($hubs)),'beats'=> json_decode(json_encode($beats)),'type'=>json_decode(json_encode($type)),'bnrtype'=>json_decode(json_encode($bannertype)),'addoredit'=>json_decode(json_encode($addoredit))]);

    }

    public function DeleteBanner(Request $request){

             $deleteData = $request->input('deleteData');
        
             $deleterecord=$this->banners->DeleteModelBanner($deleteData);
         

            if($deleterecord!='')
            {
             $success=Session::flash('alert-success',$deleterecord);
             }
    }

    public function getHubs(Request $request){

       try
        {
            $filter = Input::get("warehouseid");
            $datahubid=Input::get("hdnhubid");
            $hubsreturn='<option value="0">All</option>';
            //$ajaxwarehousehubs=$this->banners->getAjaxHubsList($filter);
            $ajaxwarehousehubs=json_decode($this->roleObj->getFilterData(6), 1);
            $filtershubs = json_decode($ajaxwarehousehubs['sbu'], 1);
            $ajaxwarehousehubs=$this->roleObj->GetHubs($filtershubs,$filter);

            $ajaxwarehousehubs=json_decode(json_encode($ajaxwarehousehubs),true);

           foreach ($ajaxwarehousehubs as $hubs) {

            $selected = ($datahubid==$hubs["le_wh_id"]) ?"selected":"";
               
            $hubsreturn.='<option class="'.$hubs['dc_id'].'" value="'.$hubs["le_wh_id"].'" '.$selected.'>'.$hubs["lp_wh_name"].'</option>';
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
         if($filter==null){
          $filter = Input::get('hdnbeatid');
         }
         $hdnbeatid=Input::get('hdnbeatid');
         $wid=Input::get('warehouseid');

         /*$dchubmap=$this->banners->GetDcHubMappings($wid,$filter);
        
        if($dchubmap)
        {*/
             $beatsreturn='<option value="0">All</option>';
             $ajaxwarehousebeats=$this->banners->getAjaxBeatsList($filter);

           foreach ($ajaxwarehousebeats as $beats) 
           {
               
            $selected = ($hdnbeatid==$beats["pjp_pincode_area_id"]) ?"selected":""; 

            $beatsreturn.='<option value="'.$beats["pjp_pincode_area_id"].'" '.$selected.'>'.$beats["pjp_name"].'</option>';
           }
        /* }else{
           $beatsreturn=0;
         }*/
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
 
             if(isset($data['select2all']) && $data['select2all']=='alloptions'){
                $resreturn='<option value=NULL>All</options>';
             }
                           

        if($data['bannertype']==16703)
        {
         // $bannerajaxlist = json_decode($this->roleObj->getFilterData(9), 1);
          $currentUserId = $this->_userId;
          $legalEntityId = $this->roleObj->getLegalEntityId($currentUserId);
          $bannerajaxlist=$this->getProductsByUser($currentUserId,9, $legalEntityId);
          //print_r($products);exit;
          //$bannerajaxlist = json_decode($products, 1);
            foreach ($bannerajaxlist as $product=>$productname) 
            {
                $selected = ($data["listitem"]==$product) ?"selected":"";
                $resreturn.='<option value="'.$product. '" '.$selected.' > '.$productname.'</option>';
            }
        }elseif($data['bannertype']==16704){

         $bannerajaxlist = json_decode($this->roleObj->getFilterData(8), 1);

             foreach ($bannerajaxlist['category'] as $cat=>$value) {

                $getcatname=$this->banners->getCategoryName($value);
                $getcatname=json_decode(json_encode($getcatname),true);
                
                $selected = ($data["listitem"]==$value) ?"selected":"";
                $resreturn.='<option value="'.$value.'" '.$selected.'>'.$getcatname[0]['cat_name'].'</option>';
           }
       }elseif($data['bannertype']==16701){

        $bannerajaxlist = json_decode($this->roleObj->getFilterData(11), 1);
             foreach ($bannerajaxlist['manufacturer'] as $man => $value) {
              
                $selected = ($data["listitem"]==$man) ?"selected":"";
                $resreturn.='<option value="'.$man.'" '.$selected.'>'.$value.'</option>';
           }
       }elseif($data['bannertype']==16702){

        $Json = json_decode($this->roleObj->getFilterData(7), 1);

             foreach ($Json['brand'] as $man => $value) {
               
              $selected = ($data["listitem"]==$man) ?"selected":"";

              $resreturn.='<option value="'.$man.'" '.$selected.'>'.$value.'</option>';
           }
       }


       return $resreturn;


        }

        public function hubbeatsmap(){
          $data = Input::all();
          $beatid=$data['beatid'];
          $hubid=$data['hubid'];
          $beatsreturn=1;

           $maphubbeat=$this->banners->GetHubBeatMappings($hubid,$beatid);
    
         if($maphubbeat==false){
           $beatsreturn=0;
         }
         return $beatsreturn;
        }

        public function imageUpload(){

            $data=Input::all();
            $bannerid=Input::get('bannerid');//$data['bannerid'];
            
             $url='';
             
             $photo =Input::file('bannerimg');//basename($data['bannerimg']);
             $EntityType="products";
             $type=1;


             //echo $var=is_object($photo)?'Yes':'NO';exit;
        
                if(is_object($photo)){
                     $url=$this->_productRepo->uploadToS3($photo,$EntityType,$type);
                }

             $imgupdate=$this->banners->bannerImgUpdate($url,$bannerid);
             
             if($imgupdate){
                return $url;
             }   
        }

        public function createpopupExport(){
         try{
            $flag ='';
            $filterData = Input::get();
            //$listids=implode(',',$filterData['banner_list']);
            if(!empty($filterData['banner_list'])){

                if(in_array('NULL', $filterData['banner_list'])){
                    $listids='NULL';
                }else{
                $listids=implode(',',$filterData['banner_list']);
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
           // $hubs=implode(',',$filterData['hubs']);
            }else{
              $hubs='NULL';  
            }
            
             if(!empty($filterData['beats'])){
                 if(in_array(0, $filterData['beats'])){
                    $beats='NULL';
                }else{
                 $beats=implode(',',$filterData['beats']);
                 $beats=trim($beats,',');
                 $beats="'".$beats."'";
               }
            //$beats=implode(',',$filterData['beats']);
            }else{
              $beats='NULL';  
            }
            
            $fdate = (isset($filterData['fsdate']) && !empty($filterData['fsdate'])) ? $filterData['fsdate'] : date('Y-m').'-01';
            $fdate = str_replace('/', '-', $fdate);
            $fromDate=  date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tsdate']) && !empty($filterData['tsdate'])) ? $filterData['tsdate'] : date('Y-m').'-01';
            $date = str_replace('/', '-', $tdate);
            $TDate=  date('Y-m-d', strtotime($date)); 
            $flag=$filterData['select_flags'];
            $details = json_decode(json_encode($this->banners->getreportsData_forpopups($fromDate,$TDate,$listids,$warehouse,$hubs,$beats,$flag)), true);
            Excel::create('Popup Reports - '. date('Y-m-d'),function($excel) use($details) {
                $excel->sheet('Popup Reports', function($sheet) use($details) {          
                $sheet->fromArray($details);
                });      
            })->export('csv');

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('banners.errorInputData')));
        }
    }


       public function createbannersExport(){
         try{
            $flag ='';
            $filterData = Input::get();
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
            
            if(!empty($filterData['warehouse'])){

                if(in_array(0, $filterData['warehouse'])){
                    $warehouse='NULL';
                }else{
                $warehouse=implode(',',$filterData['warehouse']);
                $warehouse=trim($warehouse,',');
                $warehouse="'".$warehouse."'";
               }
            
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
            
            }else{
              $hubs='NULL';  
            }
            
             if(!empty($filterData['beats'])){

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
            $details = json_decode(json_encode($this->banners->getreportsData_forbanners($fromDate,$TDate,$listids,$warehouse,$hubs,$beats,$flag)), true);
            Excel::create('Banner Reports - '. date('Y-m-d'),function($excel) use($details) {
                $excel->sheet('Banner Reports', function($sheet) use($details) {          
                $sheet->fromArray($details);
                });      
            })->export('csv');

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('banners.errorInputData')));
        }
    }

    function blockBanner(){

        try{

        $data=Input::all();
        $bannerid=$data['bannerId'];
        $sts=$data['status'];
        $bnrsts=$this->banners->changeBannerSts($bannerid,$sts);

        return $bnrsts;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }

    function checkPopupStatus(){
        try{
            $data=Input::all();
            $dcs=$data['dcs'];
            $type=$data['type'];
            $sts=$data['sts'];

            $checkactivepopups=$this->banners->checkActivePopupsByDC($type,$dcs,$sts);

            return $checkactivepopups;
        }catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }


    public function getProductsByUser($userId, $permissionLevelId, $legalEntityId)
    {
        try
        {
            $response = [];
            if($userId > 0 && $permissionLevelId > 0)
            {
                $allCategoryPermission = $this->roleObj->getUserPermission($userId, 8);
                if($allCategoryPermission)
                {
                    $response = DB::table('products')
                        ->where(['products.legal_entity_id' => $legalEntityId])
                        ->where('cp_enabled','=',1)
                        ->pluck('products.product_title','products.product_id')->all();
                }else{
                    $response = DB::table('products')
                        ->join('user_permssion', 'user_permssion.object_id', '=', 'products.category_id')
                        ->where(['user_permssion.user_id' => $userId, 
                            'user_permssion.permission_level_id' => 8,
                            'products.legal_entity_id' => $legalEntityId])
                        ->where('cp_enabled','=',1)
                        ->groupBy('products.product_id')
                        ->pluck('products.product_title','products.product_id')->all();
                }
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getItemsbyType(){

        $data=Input::all();

        $type=$data['type'];
        $bannertype=$this->banners->GetBannerType(); 
          $bannertype = json_decode(json_encode($bannertype), True);
           $resreturn='<option>Select</option>';

        if($type==16601 || $type==16602){

          
          for($l=0;$l<count($bannertype);$l++) {
               $resreturn.='<option value="'.$bannertype[$l]['value']. '"> '.$bannertype[$l]['master_lookup_name'].'</option>';
          }
        }else{
           for($l=0;$l<count($bannertype);$l++) {
            if($bannertype[$l]['value']!='16701' && $bannertype[$l]['value']!='16704'){
               $resreturn.='<option value="'.$bannertype[$l]['value']. '"> '.$bannertype[$l]['master_lookup_name'].'</option>';
            }
          }
        }
        return $resreturn;
    }
    }