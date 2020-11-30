<?php
namespace App\Modules\bfilDashboard\Controllers;

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
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Assets\Controllers\commonIgridController;
use App\Modules\BusinessUnit\Models\businessUnitDashboardModel;
use App\Modules\Roles\Models\Role;
use App\Modules\Inventory\Models\Inventory;
use Excel;


class bfilDashboardController extends BaseController {

    public function __construct() {

        $this->reports = new ReportsRepo();
        $this->roleAccess = new RoleRepo();
        $this->dashboard = new DashboardController();
        $this->objCommonGrid = new commonIgridController();
        $this->objBusinessDashboardModel = new businessUnitDashboardModel();
        $this->_inventory = new Inventory();
       //added middleware to get userid within constructor(4/11/2019)
        $this->middleware(function ($request, $next) {
            $this->userId = Session::get('userId');
            // All the code related to the session will come here
            return $next($request);
        });  
        // By Default its Zero!
        // Hub Id for CNC

        parent::Title('Ebutor - Sales Dashboard');
    }

    /**
    * The GET method for the Index Dashboard Grid
    */
    public function index() {
            // Code to Check weather the User has CNC Page Access or not
            $dashboard_data=Input::all();
            $checkBFILAccess = $this->roleAccess->checkPermissionByFeatureCode('BFIL001');
            if(!$checkBFILAccess)
                return Redirect::to('/');


            parent::Title('Ebutor - Sales Dashboard');

            // By default it will load for the Current Date
            $fromDate = date('Y-m-d');
            $toDate = $this->dashboard->tomorrow->format('Y-m-d');
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
            $salesoptios=DB::table('master_lookup')->select('description','master_lookup_name')->whereIn('mas_cat_id',[181])->where('is_active',1)->get();
            $salesoptios=json_decode(json_encode($salesoptios),1);
            $primary_secondary_sales=2;
            /*$legalentitytype=DB::table('legal_entities as le')->join('legalentity_warehouses as lw','le.legal_entity_id','=','lw.legal_entity_id')->select(DB::raw('group_concat(le.legal_entity_type_id) as legal_entity_type_id'))->where('lw.bu_id',$bu_id)->first();
            $legalentitytype=isset($legalentitytype->legal_entity_type_id)?$legalentitytype->legal_entity_type_id:'';
            $legalentitytype=explode(',', $legalentitytype);
            $legalentitytype=array_filter($legalentitytype);
            if(count($legalentitytype)==0){
                $primary_secondary_sales=2;//load secondary data by default
            }elseif(in_array(1014, $legalentitytype) && !in_array(1001, $legalentitytype) && !in_array(1016, $legalentitytype)){
                $primary_secondary_sales=2;//load secondary sales by default
                unset($salesoptios[0]);
                unset($salesoptios[1]);
            }elseif(!empty(array_intersect([1016,1014], $legalentitytype)) && !in_array(1001, $legalentitytype) && in_array(1016, $legalentitytype)){
                
                    $primary_secondary_sales=2;//load secondary sales by default since dc can have intermediate sales and secondary sales
                    unset($salesoptios[0]);
            }elseif(in_array(1001, $legalentitytype) && !in_array(1014, $legalentitytype) &&
                ! in_array(1016, $legalentitytype)){
                $primary_secondary_sales=1; //since it is apob only primary sales data is loaded
                unset($salesoptios[1]);
                unset($salesoptios[2]);
            }*/
            $primarysalesAccess = $this->roleAccess->checkPermissionByFeatureCode('PRSR001');

            $result = $this->getStockistDashboard_web($this->userId,$primary_secondary_sales,$fromDate,$toDate,$bu_id);
            return view('bfilDashboard::index')
                ->with(["last_updated"=>date('Y-m-d h:i a'), "BFILData"=>$result,"brands"=>$brandObj,"manufacturer"=>$manufObj,"product_grp"=>$product_grp,"buid"=>$bu_id,'primary_secondary_sales'=>$primary_secondary_sales,'primarysalesaccess'=>$primarysalesAccess,'salesoption'=>$salesoptios]);

    }

    /**
    * The POST method for the Index Dashboard Grid
    */
    public function getIndexData(){
        try {
            $data = \Input::all();            
            $datesArr = $this->dashboard->getDateRange($data);
            $fromDate = $datesArr["fromDate"];
            $toDate = $datesArr["toDate"];
            //$flag=2;
            if(isset($data['buid']) && !empty($data['buid'])) { 
                $buid=$data['buid'];
            }else{

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
            
            if(isset($data['categoryid']) && !empty($data['categoryid'])) { 
                $categoryid=$data['categoryid'];
            }else{
                $categoryid='NULL';
            }

            if(isset($data['brandid']) && !empty($data['brandid'])) { 
                $brandid=$data['brandid'];
            }else{
                $brandid='NULL';
            }

            if(isset($data['manufid']) && !empty($data['manufid'])) { 
                $manufid=$data['manufid'];
            }else{
                $manufid='NULL';
            }

            if(isset($data['productgrpid']) && !empty($data['productgrpid'])) { 
                $productgrpid=$data['productgrpid'];
            }else{
                $productgrpid='NULL';
            }
            if(isset($data['primary_secondary_sales']) && !empty($data['primary_secondary_sales'])) { 
                $primary_secondary_sales=$data['primary_secondary_sales'];
            }else{
                $primary_secondary_sales=2;
            }
            
            /*$legalentitytype=DB::table('legal_entities as le')->join('legalentity_warehouses as lw','le.legal_entity_id','=','lw.legal_entity_id')->select(DB::raw('group_concat(le.legal_entity_type_id) as legal_entity_type_id'))->where('lw.bu_id',$buid)->first();
            $legalentitytype=isset($legalentitytype->legal_entity_type_id)?$legalentitytype->legal_entity_type_id:'';
            $legalentitytype=explode(',', $legalentitytype);
            $legalentitytype=array_filter($legalentitytype);
            if(count($legalentitytype)==0){
                $primarysalesenable=0;//this indicates all primary,intermediate and secondary sales are to be displayed in options(generally all these three options or shown when user selects corporate,zones or states)
            }elseif(in_array(1014, $legalentitytype) && !in_array(1001, $legalentitytype) && !in_array(1016, $legalentitytype)){
                $primarysalesenable=3;//this indicates only secondary sales to be enabled when end user selects only FC's
                $primary_secondary_sales=2;
            }elseif(!empty(array_intersect([1016,1014], $legalentitytype)) && !in_array(1001, $legalentitytype) && in_array(1016, $legalentitytype)){
                $primarysalesenable=2;//generally this represent intermediate sales,when end user selects DC's we provide end user with two select options(intermediate and secondary sales) since there are chances of deliveries from dc to retailer(secondary sales)
                if($primary_secondary_sales==1){
                    $primary_secondary_sales=2;
                }
            }elseif(in_array(1001, $legalentitytype) && !in_array(1014, $legalentitytype) &&
                ! in_array(1016, $legalentitytype)){
                $primarysalesenable=1;//apob sales only primary option is enabled
                $primary_secondary_sales=1; 
            }else{
                $primarysalesenable=0;
            }*/
            $primarysalesenable=0;
            $result["dashboard"] = $this->getStockistDashboard_web($this->userId,$primary_secondary_sales,$fromDate,$toDate,$buid,$brandid,$manufid,$productgrpid,$categoryid);
            
            if(!empty($data))
            {
                return ['last_updated' => date('Y-m-d h:i a'),'BFILData' => $result,'primary_secondary_sales'=>$primary_secondary_sales,'primarysalesenable'=>$primarysalesenable];
            }          
        
        } catch (Exception $e) {
             $result=array();
            return ['last_updated' => date('Y-m-d h:i a'),'BFILData' => $result,'primary_secondary_sales'=>2,'primarysalesenable'=>1];
            //return "Sorry! Something went wrong. Please contact the Admin";
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
        }
    }
    
    /**
    * The Central Function for the Dashboard Data.
    * It acts like a Model for the Dashboard Procedure..,
    */
    public function getStockistDashboard_web($userId = 0,$primary_secondary_sales=1, $fromDate, $toDate,$buid,$brandid='NULL',$manufid='NULL',$product_grp='NULL',$categoryid='NULL'){
        // Code to Check weather the User has TGM Access or not
        $data = Cache::tags("StockistDashboard")->get('Stockist_dashboard_report_'.$primary_secondary_sales.'_'.$fromDate.'_'.$toDate.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$product_grp.'_'.$categoryid,false);                    
        if(empty($data))
        {
            //db::enableQueryLog();
            $response = DB::select(DB::raw("CALL getStockistDashboardByBU_web(NULL,'".$fromDate."','".$toDate."',".$primary_secondary_sales.",".$brandid.",".$manufid.",".$product_grp.",".$buid.",".$categoryid.")"));
           // echo "CALL getStockistDashboardByBU_web(NULL,'".$fromDate."','".$toDate."',".$primary_secondary_sales.",".$brandid.",".$manufid.",".$product_grp.",".$buid.",".$categoryid.")";
            $result = json_decode(json_encode($response),true);
            $data = json_decode($result[0]['Stockist_Dashboard'],true);


            Cache::tags("StockistDashboard")->put('Stockist_dashboard_report_'.$primary_secondary_sales.'_'.$fromDate.'_'.$toDate.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$product_grp.'_'.$categoryid,$data,CACHE_TIME);
            Cache::tags("StockistDashboard")->put('Stockist_dashboard_report_'.$primary_secondary_sales.'_'.$fromDate.'_'.$toDate.'_'.$buid.'_'.$brandid.'_'.$manufid.'_'.$product_grp.'_'.$categoryid.'_'.'last_updated',date('Y-m-d h:i:s a'),CACHE_TIME);
        }

        return json_decode(json_encode($data));
    }
    


    public function getStockistSales(Request $request,$userId = 0,$flag=1)
    {
        try{
            $data = Input::all();
            $datesArr = $this->dashboard->getDateRange($data);
            $fromDate = $datesArr["fromDate"];
            $toDate = $datesArr["toDate"];
            $sales_type=isset($data['sales_type'])?$data['sales_type']:2;
            if(isset($data['buid']) && !empty($data['buid'])) { 
                $buid=$data['buid'];
            }else{

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

            // make sql for firstname
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Stockist_Name", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for cbusiness_legal_name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Stockist_Code", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
             // make sql for contact name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Total_Orders", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for pan_number
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TBV", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for phone_no
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Invoiced", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for email
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Total_Invoiced", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for pincode
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Returns", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for state_id
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Total_Returned", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for city
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Cancel", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for gstin
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Total_Cancelled", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Order_Date", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
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

            $data = $data = Cache::tags("StockistDashboard_sales")->get('Stockist_dashboard_report_sales'.$userId.'_'.$sales_type.'_'.$fromDate.'_'.$toDate.'_'.$buid,false);
        if(empty($data)){

             $roleObj = new Role();
             $Json = json_decode($roleObj->getFilterData(6,$this->userId), 1);
             $filters = json_decode($Json['sbu'], 1);            
             $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
             $data=DB::statement("SET SESSION group_concat_max_len = 100000");
             $whdata = DB::select("call getBuHierarchy_proc($buid,@le_wh_ids)");
             $whdata =DB::select(DB::raw('select @le_wh_ids as wh_list'));
             if(!empty($whdata[0]->wh_list)){

                $dc_acess_list=$whdata[0]->wh_list;

             }else{

                $dc_acess_list='NULL';

             }
             
        $orderdate = " where Order_Date between '".$fromDate."' and '".$toDate."' and le_wh_id in (".$dc_acess_list.")";
       
       if($sales_type==2){
          $query = 
               "SELECT Parent,Stockist_Name,
                SUM(Total_Orders) AS 'Total_Orders',
                SUM(TBV) AS 'TBV', 
                SUM(Invoiced) AS 'Invoiced', 
                SUM(Total_Invoiced) AS 'Total_Invoiced', 
                SUM(RETURNS) AS 'Returns',
                SUM(Total_Returned) AS 'Total_Returned', 
                SUM(Cancel) AS 'Cancel', 
                SUM(Total_Cancelled) AS 'Total_Cancelled', 
                SUM(Delivered) AS 'Delivered', 
                SUM(Total_Delivered) AS 'Total_Delivered', 
                SUM(Pending_GRN) AS 'Pending_GRN', 
                SUM(Pending_GRN_Value) AS Pending_GRN_Value, 
                SUM(Collected)AS Collected, 
                SUM(Outstanding) AS Outstanding, SUM(Opening_Stock) as Opening_Stock,STATUS,SUM(Cashback_Orders) AS Cashback_Orders,State AS State,City AS City
                FROM vw_stockist_retailer_orders".$orderdate." GROUP BY le_wh_id";
            }elseif($sales_type==1){
              $query = 
               "SELECT dc_name as Parent,
                SUM(ord_cnt) AS 'Total_Orders',
                SUM(ord_val) AS 'TBV', 
                SUM(inv_cnt) AS 'Invoiced', 
                SUM(inv_val) AS 'Total_Invoiced', 
                SUM(ret_cnt) AS 'Returns',
                SUM(ret_val) AS 'Total_Returned', 
                SUM(cancl_cnt) AS 'Cancel', 
                SUM(cancl_val) AS 'Total_Cancelled', 
                SUM(del_cnt) AS 'Delivered', 
                SUM(del_val) AS 'Total_Delivered',  
                SUM(collec_val)AS Collected,State AS State,City AS City 
                FROM vw_primary_orders".$orderdate." GROUP BY le_wh_id";  

            }elseif($sales_type==3){
                $query = 
                       "SELECT dc_name as Parent,
                        SUM(ord_cnt) AS 'Total_Orders',
                        SUM(ord_val) AS 'TBV', 
                        SUM(inv_cnt) AS 'Invoiced', 
                        SUM(inv_val) AS 'Total_Invoiced', 
                        SUM(ret_cnt) AS 'Returns',
                        SUM(ret_val) AS 'Total_Returned', 
                        SUM(cancl_cnt) AS 'Cancel', 
                        SUM(cancl_val) AS 'Total_Cancelled', 
                        SUM(del_cnt) AS 'Delivered', 
                        SUM(del_val) AS 'Total_Delivered',  
                        SUM(collec_val)AS Collected,state AS State,city AS City 
                        FROM vw_intermediate_orders".$orderdate." GROUP BY le_wh_id";
            }

            $data = DB::select($query);

            Cache::tags("StockistDashboard_sales")->put('Stockist_dashboard_report_sales'.$userId.'_'.$sales_type.'_'.$fromDate.'_'.$toDate.'_'.$buid,$data,CACHE_TIME);
            Cache::tags("StockistDashboard_sales")->put('Stockist_dashboard_report_sales'.$userId.'_'.$sales_type.'_'.$fromDate.'_'.$toDate.'_'.$buid.'last_updated',date('Y-m-d h:i:s a'),CACHE_TIME);
            }            
        return array("data" => $data);
        }catch(Exception $e) {
            $data=array();
            return array("data" => $data);
          //return 'Message: ' .$e->getMessage();
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

    /*
     * download sales details() method is used to Download All sales details
     * @param NULL
     * @return Excell
     */ 

    public function getExportSalesDetails(Request $request){
        try {
            $checkexportfeature=$this->roleAccess->checkPermissionByFeatureCode('SSDEXB001');
            if ($checkexportfeature==0)
            {
                return Redirect::to('/');
            }    
            $filterData = $request->input();
            $datesArr = $this->dashboard->getDateRange($filterData);
            $from_date = (isset($datesArr['fromDate']) && !empty($datesArr['fromDate'])) ? $datesArr['fromDate'] : date('Y-m').'-0';
            $to_date = (isset($datesArr['toDate']) && !empty($datesArr['toDate'])) ? $datesArr['toDate'] : date('Y-m-d');
            $file_name = 'sales_details_' .date('Y-m-d-H-i-s').'.csv';
            $bu_id  = $filterData['exportbuId'];
            $flag = $filterData['exportflag'];
            $query = DB::select(DB::raw("CALL getStockistGridExport (".$bu_id.",'".$from_date."','".$to_date."',".$flag.")"));
          
                $query=json_decode(json_encode($query),1);
                $result = Excel::create($file_name, function($excel) use($query) {
                    $excel->sheet('Sheet1', function($sheet) use($query) {
                        $sheet->fromArray($query);
                    });
                })->export('xlsx');
                exit;
           
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
        
    }
    
}
