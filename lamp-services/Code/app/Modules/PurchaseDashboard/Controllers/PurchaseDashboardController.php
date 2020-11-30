<?php
namespace App\Modules\PurchaseDashboard\Controllers;

use DB;
use Log;
use View;
use Cache;
use Input;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\ReportsRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\Assets\Controllers\commonIgridController;
use App\Modules\Roles\Models\Role;
use App\Modules\Inventory\Models\Inventory;
use Excel;

class PurchaseDashboardController extends BaseController {
    public function __construct() {
 // Logged In User Legal Entity Id
        //added middleware to get userid within constructor(4/11/2019)
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            $this->userId = Session::get('userId');
            $this->legal_entity_id = Session::get('legal_entity_id');
         
            // All the code related to the session will come here
            return $next($request);
        });
        $this->reports = new ReportsRepo();
        $this->roleAccess = new RoleRepo();
        $this->objCommonGrid = new commonIgridController();

        // Date Objects of Tommorow and Date
        $this->tomorrow = new \DateTime('tomorrow');
        $this->yesterday = new \DateTime('yesterday');
        $this->legal_entity_id =   isset($this->legal_entity_id)?$this->legal_entity_id:0;
        // Cache Tag Name + le id
        define("CACHE_TAG","PurchaseDashboard"+$this->legal_entity_id);
        // The Default Cache Time to set for 15 Minutes
        define("CACHE_TIME", 15);
        parent::Title('Ebutor - Purchase Dashboard');

    }

    /**
    * The GET method for the Index Dashboard Grid
    */
    public function index() {
            // Code to Check weather the User has Purchase Dashboard Page Access or not
            $dashboardAccess = $this->roleAccess->checkPermissionByFeatureCode('PCD001');
            if(!$dashboardAccess){
            return Redirect::to('/');
            }
            $fromDate = date('Y-m-d');
            $toDate = $this->tomorrow->format('Y-m-d');
            $roleObj = new Role();
            $brandObj = json_decode($roleObj->getFilterData(7,$this->userId), 1); 
            $manufObj = json_decode($roleObj->getFilterData(11,$this->userId), 1);
            $product_grp=$roleObj->getProductGroups();
            $product_grp=json_decode(json_encode($product_grp),true);
            $getaccessbuids=$this->getBuidsByUserId($this->userId);
            $getaccessbuids=explode(',', $getaccessbuids);
            $getaccessbuids=min($getaccessbuids);
            if($getaccessbuids==0){
                $buid=DB::table('business_units')
                      ->select('bu_id')
                      ->where('parent_bu_id',$getaccessbuids)
                      ->first();
                $bu_id=isset($buid->bu_id)?$buid->bu_id:1;
            }else{
                $bu_id=$getaccessbuids;
            }
            $supplier = DB::table('legal_entities as l')->select('l.legal_entity_id','l.business_legal_name')->join('suppliers as s','s.legal_entity_id','=','l.legal_entity_id')->where('l.legal_entity_type_id', 1002)->where('s.is_active' , 1)->get();
            $supplier=json_decode(json_encode($supplier),1);
            $salesoptios=DB::table('master_lookup')->select('value','master_lookup_name')->whereIn('mas_cat_id',[181])->where('is_active',1)->get();
            $salesoptios=json_decode(json_encode($salesoptios),1);
            $primary_secondary_sales=1;
            $primarysalesAccess = $this->roleAccess->checkPermissionByFeatureCode('PRSR001');
            $result = $this->getDashboardData($this->userId,$primary_secondary_sales,$fromDate,$toDate,$bu_id);
            $last_updated = isset($result['last_updated'])?$result['last_updated']:date('Y-m-d h:i a');
            unset($result['last_updated']);
            $dcselect=$this->roleAccess->checkPermissionByFeatureCode('DNCDC001');
            $tabAccess['poOrderDetails'] = $this->roleAccess->checkPermissionByFeatureCode('POTAB01');
            $tabAccess['grnDetails'] = $this->roleAccess->checkPermissionByFeatureCode('GRNTAB01');
            $tabAccess['inventoryDetails'] = $this->roleAccess->checkPermissionByFeatureCode('INVTAB01');
                return 
                    View('PurchaseDashboard::index')->with([
                        'PCDData' => $result,
                        'last_updated' => $last_updated,
                        'tab_access' => $tabAccess,
                        'dcdashboard'    =>$dcselect,
                        "brands"=>$brandObj,
                        "manufacturer"=>$manufObj,
                        "product_grp"=>$product_grp,
                        "buid"=>$bu_id,
                        'primary_secondary_sales'=>$primary_secondary_sales,
                        'primarysalesaccess'=>$primarysalesAccess,
                        'salesoption'=>$salesoptios,
                        'supplier'=>$supplier
                    ]);
           

    }

    /**
    * The POST method for the Index Dashboard Grid
    */
    public function getIndexData(){
        try {
            $data = \Input::all();            
            $datesArr = $this->getDateRange($data);
            $fromDate = $datesArr["fromDate"];
            $toDate = $datesArr["toDate"];
            //$flag=2;
            //bu_id
            if(isset($data['buid']) && !empty($data['buid'])) { 
                $buid=$data['buid'];
            } else {
            $getaccessbuids=$this->getBuidsByUserId($this->userId);
            $getaccessbuids=explode(',', $getaccessbuids);
            $getaccessbuids=min($getaccessbuids);
              if($getaccessbuids==0){
                $buid=DB::table('business_units')
                      ->select('bu_id')
                      ->where('parent_bu_id',$getaccessbuids)
                      ->first();
                $buid=isset($buid->bu_id)?$buid->bu_id:1;
                }else{
                    $buid=$getaccessbuids;
                }
            }
             //category_id
            if(isset($data['categoryid']) && !empty($data['categoryid'])) { 
                $categoryid=$data['categoryid'];
            }else{
                $categoryid='NULL';
            }

            //brand_id
            if(isset($data['brandid']) && !empty($data['brandid'])) { 
                $brandid=$data['brandid'];
            }else{
                $brandid='NULL';
            }

            //manufacture_id
            if(isset($data['manufid']) && !empty($data['manufid'])) { 
                $manufid=$data['manufid'];
            }else{
                $manufid='NULL';
            }

            //productgrpid
            if(isset($data['productgrpid']) && !empty($data['productgrpid'])) { 
                $productgrpid=$data['productgrpid'];
            }else{
                $productgrpid='NULL';
            }

            //primary_secondary_sales
            if(isset($data['primary_secondary_sales']) && !empty($data['primary_secondary_sales'])) { 
                $primary_secondary_sales=$data['primary_secondary_sales'];
            }else{
                $primary_secondary_sales=1;
            }
            $primarysalesenable=0;
            $result["purchasedashboard"] = $this->getPurchaseDashboard_web($this->userId,$primary_secondary_sales,$fromDate,$toDate,$buid,$brandid,$manufid,$productgrpid,$categoryid);
            
            if(!empty($data))
            {
                return ['last_updated' => date('Y-m-d h:i a'),'PCDData' => $result ,'primary_secondary_sales'=>$primary_secondary_sales,'primarysalesenable'=>$primarysalesenable];
            }          
        } catch (Exception $e) {
            return "Sorry! Something went wrong. Please contact the Admin";
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
        }
    }


   public function getPurchaseDashboard_web($userId = 0,$primary_secondary_sales=1, $fromDate, $toDate,$buid,$brandid='NULL',$manufid='NULL',$product_grp='NULL',$categoryid='NULL') {
     //Fetching  value from cache based on key 
        $data = Cache::tags("PurchaseDashboard")->get('Purchase_dashboard_report_'.$primary_secondary_sales.'_'.$fromDate.'_'.$toDate.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$product_grp.'_'.$categoryid,false);
       
            if(empty($data))
            {
                //  getPurchaseReturnDashboard_New Stockist_Dashboard
                $response = DB::select(DB::raw("CALL  getPurchaseReturnDashboard_New(NULL,'".$fromDate."','".$toDate."',".$primary_secondary_sales.",".$brandid.",".$manufid.",".$product_grp.",".$buid.",".$categoryid.")"));
                $result = json_decode(json_encode($response),true);
                $data = json_decode($result[0]['Purchase_Dashboard'],true);
              // $data = json_decode($result[0]['Stockist_Dashboard'],true);
            //setting value into the cache 
            Cache::tags("PurchaseDashboard")->put('Purchase_dashboard_report_'.$primary_secondary_sales.'_'.$fromDate.'_'.$toDate.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$product_grp.'_'.$categoryid,$data,CACHE_TIME);
            Cache::tags("PurchaseDashboard")->put('Purchase_dashboard_report_'.$primary_secondary_sales.'_'.$fromDate.'_'.$toDate.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$product_grp.'_'.$categoryid.'last_updated',date('Y-m-d h:i:s a'),CACHE_TIME);
        }

        return json_decode(json_encode($data));

   }

   
   public function getDashboardData($userId = 0,$primary_secondary_sales=1, $fromDate, $toDate,$buid,$brandid='NULL',$manufid='NULL',$product_grp='NULL',$categoryid='NULL')
    {
        $result = $this->getPurchaseDashboard_web($userId,$primary_secondary_sales,$fromDate,$toDate,$buid,$brandid,$manufid,$product_grp,$categoryid );    
        return $result;
    }

    //used for date filter to get toand from date range
    public function getDateRange($inputDate='')
    {
      
        $this->tomorrow = new \DateTime('tomorrow');
        $this->yesterday = new \DateTime('yesterday');
        $fromDate = date('Y-m-d');
      //  $toDate = $this->tomorrow->format('Y-m-d');
        $toDate = date('Y-m-d');
        try {
            $switchOp = isset($inputDate['filter_date'])?strtolower($inputDate['filter_date']):"";
            switch($switchOp)
            {
                case 'wtd':
                    $currentWeekSunday = strtotime("last sunday");
                    $sunday = date('w', $currentWeekSunday)==date('w') ? $currentWeekSunday + 7*86400 : $currentWeekSunday;
                    $lastSunday = date("Y-m-d",$sunday);
                    $fromDate = $lastSunday;
                    break;
                case 'mtd':
                    $fromDate = date('Y-m-01');
                    break;
                case 'ytd': case 'quarter':
                    $fromDate = date('Y-01-01');
                    break;
                case 'today':
                    $fromDate = date('Y-m-d');
                    $toDate = date('Y-m-d');
                 //   $toDate = $this->tomorrow->format('Y-m-d');
                    break;
                case 'yesterday':
                    $toDate = $fromDate = $this->yesterday->format('Y-m-d');
                    break;
                case 'last_month':
                    $fromDate = date("Y-m-1", strtotime("last month"));
                    $toDate = date("Y-m-t", strtotime("last month"));
                    break;
                case 'custom': default:
                    // Converting the Date format from "dd/mm/yyyy" -to- "yyyy-mm-dd";
                    if(isset($inputDate['fromDate']) and !empty($inputDate['fromDate'])){
                        $fromDateSubArr = explode('/', $inputDate['fromDate']);
                        $newFromDate = $fromDateSubArr[2]."-".$fromDateSubArr[1]."-".$fromDateSubArr[0];
                    }else
                        $newFromDate = date('Y-m-d');
                    
                    if(isset($inputDate['toDate']) and !empty($inputDate['toDate'])){
                        $toDateSubArr = explode('/', $inputDate['toDate']);
                        $newToDate = $toDateSubArr[2]."-".$toDateSubArr[1]."-".$toDateSubArr[0];
                    }else
                        $newToDate = $this->tomorrow->format("Y-m-d");

                    $fromDate = $newFromDate;
                    $toDate = $newToDate;
                    break;
            }
            // If the toDate is todate, then changing it to tomorrow
            // if($toDate == date("Y-m-d"))
            //     $toDate = $this->tomorrow->format("Y-m-d");
            // Log::info("fromDate ".$fromDate);
            // Log::info("toDate ".$toDate);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return ["fromDate" => $fromDate, "toDate" => $toDate];
        }
        return ["fromDate" => $fromDate, "toDate" => $toDate];
    }

    public function getPOOrderDetailsDashboard()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        $wh_id = isset($data["buid"])?$data["buid"]:0;
       // echo $wh_id;exit;
        //$legal_entity_id = isset($data["legal_entity_id"])?$data["legal_entity_id"]:$this->legal_entity_id;

        $data = Cache::tags(CACHE_TAG)->get('POOrderDetailsDashboard_'.$wh_id.'_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            // Updating Proc to Hide CnC Hub Data in Main Dashboard [@satish]
            // $query = "CALL getLegalEntitiesExportData(?,?)";
            // $query = "CALL getDnCLegalEntitiesExportData(?,?)";
            $query = "CALL getPOOrderDetails_web(".$wh_id.",'".$fromDate."','".$toDate."')";
            $data = DB::selectFromWriteConnection(DB::raw($query));
            Cache::tags(CACHE_TAG)->put('POOrderDetailsDashboard_'.$wh_id.'_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }

        return $data;
    }

    public function getGRNDetailsDashboard()
    {
        $data = Input::all();
        $datesArr = $this->getDateRange($data);
        $fromDate = $datesArr["fromDate"];
        $toDate = $datesArr["toDate"];
        $wh_id = isset($data["buid"])?$data["buid"]:0;
        //$legal_entity_id = isset($data["legal_entity_id"])?$data["legal_entity_id"]:$this->legal_entity_id;

        $data = Cache::tags(CACHE_TAG)->get('GRNDetailsDashboard_'.$wh_id.'_'.$fromDate.'_'.$toDate,false);
        if(empty($data)){

            // Updating Proc to Hide CnC Hub Data in Main Dashboard [@satish]
            // $query = "CALL getLegalEntitiesExportData(?,?)";
            // $query = "CALL getDnCLegalEntitiesExportData(?,?)";
            $query = "CALL getGRNDetails_web(".$wh_id.",'".$fromDate."','".$toDate."')";
            $data = DB::selectFromWriteConnection(DB::raw($query));
            Cache::tags(CACHE_TAG)->put('GRNDetailsDashboard_'.$wh_id.'_'.$fromDate.'_'.$toDate, $data, CACHE_TIME);

        }

        return $data;
    }

    public function getSaleDetails(Request $request,$userId = 0,$flag=1)
    {
        try{
            $data = Input::all();
            $datesArr = $this->getDateRange($data);
            $fromDate = $datesArr["fromDate"];
            $toDate = $datesArr["toDate"];
            //business unit 
            if(isset($data['buid']) && !empty($data['buid'])) { 
                $buid=$data['buid'];
            } else {
            $getaccessbuids=$this->getBuidsByUserId($this->userId);
            $getaccessbuids=explode(',', $getaccessbuids);
            $getaccessbuids=min($getaccessbuids);
              if($getaccessbuids==0){
                $buid=DB::table('business_units')
                      ->select('bu_id')
                      ->where('parent_bu_id',$getaccessbuids)
                      ->first();
                $buid=isset($buid->bu_id)?$buid->bu_id:1;
                }else{
                    $buid=$getaccessbuids;
                }
            }
            $makeFinalSql = array();            
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }

            // make sql for FC name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Stockist_Name", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for  state name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("State", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
             // make sql for city name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("City", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for Total invoice amount
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Total_Invoiced", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for Total_Return
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Total_Returned", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            //Date filter 
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Date", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            //Dc name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Parent", $filter);
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

            if($orderBy!=''){
                $orderBy = ' ORDER BY ' . $orderBy;
            }

            $sqlWhrCls = '';
            $countLoop = 0;
            foreach ($makeFinalSql as $value) {
                if( $countLoop==0 ){
                    $sqlWhrCls .= ' AND ' . $value;
                }elseif(count($makeFinalSql)==$countLoop ){
                    $sqlWhrCls .= $value;
                }else{
                    $sqlWhrCls .= ' AND ' .$value;
                }
                $countLoop++;
            }
            
            // $whdata = DB::select("call getBuHierarchy_proc($buid,@le_wh_ids)");
            // $whdata =DB::select(DB::raw('select @le_wh_ids as wh_list'));
            // if(!empty($whdata[0]->wh_list)){

            //    $dc_acess_list=$whdata[0]->wh_list;

            // }else{

            //    $dc_acess_list='NULL';

            // }
            $response = DB::select(DB::raw("CALL getPOGridData(1 ,'".$fromDate."','".$toDate."','".$buid."')"));
         /// echo "CALL getPOGridData(1 ,'".$fromDate."','".$toDate."','".$buid."')";
            $result = json_decode(json_encode($response),true);
            return array("data" => $result);
            
        }catch(Exception $e) {
            $data=array();
            return array("data" => $data);
      }
    }

    public function getInventoryDetailsDashboard()
    {
        $data = Input::all();
        
        $wh_id = isset($data["buid"])?$data["buid"]:0;
        $legal_entity_id = isset($data["legal_entity_id"])?$data["legal_entity_id"]:0;

       

        $data = Cache::tags(CACHE_TAG)->get('InventoryDetailsDashboard_'.$wh_id.'_'.$legal_entity_id,false);
        if(empty($data)){
            $query = "CALL getDynamicInventoryDashboard_web(".$wh_id.",".$legal_entity_id.")";
            $data = DB::selectFromWriteConnection(DB::raw($query));
            Cache::tags(CACHE_TAG)->put('InventoryDetailsDashboard_'.$wh_id.'_'.$legal_entity_id, $data, CACHE_TIME);

        }

        return $data;
    }

    public function getBuidsByUserId($userid){
        try{
            $buids=DB::table('user_permssion')
                       ->select(DB::raw("GROUP_CONCAT(object_id) as object_id"))
                       ->where('user_id',$userid)
                       ->where('permission_level_id',6)
                       ->get();
             $buids=isset($buids[0]->object_id)?$buids[0]->object_id:1;
             return $buids;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getBrands(){

        try{
            $data=Input::all();
            $manufid=$data['manufid'];
            $result=DB::table('brands')
                        ->select('brands.brand_name', 'brands.brand_id')
                        ->where('mfg_id',$manufid)
                        ->orderBy('brands.brand_name','asc')
                        ->get();
            $result = json_decode(json_encode($result), True);
                $resreturn='<option value="">Select Brands</option>';
            

                foreach ($result as $result) {
                $resreturn.='<option value="'.$result['brand_id']. '"> '.$result['brand_name'].'</option>';
                } 

            return Array('status'=>200,'message'=>'success','res'=>$resreturn);          
        }catch(Exception $e) {
            return 'Message: ' .$e->getMessage();
        }

    }

    public function getProductGroupByBrand(){
        try{
            $data=Input::all();
            $brandid=$data['brandid'];
            $result=DB::table('product_groups as pg')
                            ->select('pg.product_grp_ref_id','pg.product_grp_name')
                            ->join('products as p','pg.product_grp_ref_id','=','p.product_group_id')
                            ->join('brands as b','b.brand_id','=','p.brand_id')
                            ->where('p.brand_id',$brandid)
                            ->groupBy('pg.product_grp_ref_id')
                            ->orderBy('pg.product_grp_name','asc')
                        ->get();
            $result = json_decode(json_encode($result), True);
                $resreturn='<option value="">Select Product Group</option>';
            

                foreach ($result as $result) {
                $resreturn.='<option value="'.$result['product_grp_ref_id']. '"> '.$result['product_grp_name'].'</option>';
                } 

            return Array('status'=>200,'message'=>'success','res'=>$resreturn);          
        }catch(Exception $e) {
            return 'Message: ' .$e->getMessage();
        }    


    }

    public function getBuUnit(){
     return $this->_inventory->businessTreeData();
    }

     /*
     * download po details() method is used to Download All sales details
     * @param NULL
     * @return Excell
     */ 

    public function getExportPoDetails(Request $request){
        try {
            $checkexportfeature=$this->roleAccess->checkPermissionByFeatureCode('SSDEXB001');
            if ($checkexportfeature==0)
            {
                return Redirect::to('/');
            }    
            $filterData = $request->input();
            $datesArr = $this->getDateRange($filterData);
            $from_date = (isset($datesArr['fromDate']) && !empty($datesArr['fromDate'])) ? $datesArr['fromDate'] : date('Y-m').'-0';
            $to_date = (isset($datesArr['toDate']) && !empty($datesArr['toDate'])) ? $datesArr['toDate'] : date('Y-m-d');
            $file_name = 'sales_details_' .date('Y-m-d-H-i-s').'.xlsx';
            $bu_id  = $filterData['exportbuId'];
            $flag = $filterData['exportflag'];
            // $whdata = DB::select("call getBuHierarchy_proc( $bu_id,@le_wh_ids)");
            // $whdata =DB::select(DB::raw('select @le_wh_ids as wh_list'));
            // if(!empty($whdata[0]->wh_list)){

            //    $dc_acess_list=$whdata[0]->wh_list;

            // }else{

            //    $dc_acess_list='NULL';

            // }
            $response = DB::select(DB::raw("CALL getPOGridData(2 ,'".$from_date."','".$to_date."','".$bu_id."')"));
           /// echo "CALL getPOGridData(2,'".$from_date."','".$to_date."','".$bu_id."')";
                $query=json_decode(json_encode($response),true);
                $result = Excel::create($file_name, function($excel) use($query) {
                    $excel->sheet('Sheet1', function($sheet) use($query) {
                        $sheet->fromArray($query ,null, 'A1', true , true);
                    });
                })->export('xlsx');
                exit;           
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
        
    }
    

    
/*
* Function Name: getSupplierList
* Description: getSupplierList function is used to get all the supplier list based on user
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 Sep 2016
* Modified Date & Reason:
*/
public function getSupplierList()
{
//db::enablequerylog();
$supplier = DB::table('legal_entities as le')
         ->select(DB::raw("le.business_legal_name AS supplier_name))
            le.address1,le.address2,
            u.mobile_no AS telephone,
          le.business_type_id,le.city,le.pincode,le.legal_entity_id AS supplier_id"))
         ->join('users as u','le.legal_entity_id','=','u.legal_entity_id')
       ->where("le.legal_entity_type_id", "=",1002)
       ->where("le.is_approved", 1);
            $result=$supplier  
                ->get()->all();
            return $result;

    }
  
}   




