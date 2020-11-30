<?php
/*
FileName :AddpromotionController
Author   :eButor
Description :
CreatedDate :23/August/2016
*/
//defining namespace
namespace App\Modules\Promotions\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\Promotions\Controllers\commonIgridController;
use Illuminate\Support\Facades\Validator;
//use App\Modules\Promotions\Models\PromotionModel;
use App\Modules\Promotions\Models\AddpromotionModel;
use App\Modules\Promotions\Models\promotionDayTimeModel;
use App\Modules\Promotions\Models\slabDetailsModel;
use App\Modules\Promotions\Models\promotionDetailsDashboardModel;
use App\Modules\Promotions\Models\cashBackModel;
use App\Modules\Promotions\Models\freeQtyModel;
use App\Modules\Promotions\Models\AddPromotionBundleQuantity;
use App\Modules\Cpmanager\Models\MasterLookupModel;
use App\Modules\Promotions\Models\tradeDiscountModel;
use App\Modules\Roles\Models\Role;
use App\Modules\Product\Models\ProductModel;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use Input;
use DB;
use Session;
use Log;
use Redirect;
use Notifications;
use DateTime;

class AddpromotionController extends BaseController{
    
    private $add_promotion_request = '';
    private $objPromotionDayTime = '';
    private $objSlabDetails = '';
    private $promotion_dashboard = '';

	//calling model
    public function __construct() {

        $this->objCommonGrid = new commonIgridController();
        $this->add_promotion_request = new AddpromotionModel();
        $this->objPromotionDayTime = new promotionDayTimeModel();
        $this->objSlabDetails = new slabDetailsModel();
        $this->objPromotionBundleForQuantity = new AddPromotionBundleQuantity();
        $this->promotion_dashboard = new promotionDetailsDashboardModel();
        $this->_roleRepo = new RoleRepo();
        $this->objCashBack = new cashBackModel();        
        $this->_roleModel = new Role();
        $this->productModel = new ProductModel();
        $this->freeQtyModel = new freeQtyModel();
        $this->master_lookup = new MasterLookupModel();
        $this->tradeDiscount = new tradeDiscountModel();
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                parent::Title('Add New Promotion');
                $access = $this->_roleRepo->checkPermissionByFeatureCode('PRDS003');

                if (!$access && Session::get('legal_entity_id')!=0) {
                    Redirect::to('/promotions/viewpromotiondetails')->send();
                    die();
                }
                return $next($request);
            });

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
    /**
     * [addnewPromotion To add promotion ]
     * @return [view] [returns add promotion view ]
     */
	public function addnewPromotion(){

        try{

            $addAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS003');
            if (!$addAccess) {
                Redirect::to('/')->send();
                die();
            }

            $breadCrumbs = array('Home' => url('/'),'Promotions' => '/promotions/viewpromotiondetails','Add New Promotions' => '#');
            parent::Breadcrumbs($breadCrumbs);
            
            $getpromotionData = $this->add_promotion_request->getpromotionData();
            $getstate = $this->promotion_dashboard->getStateDetailsDropdown();
            $manufactures = $this->promotion_dashboard->getManufactureDetailsDropdown();
            $branddata= $this->promotion_dashboard->getBrandDetailsDropdown();
            $customer_group = $this->promotion_dashboard->getCustomerGroupDropdown();
            $select_product = $this->add_promotion_request->getSelectFreeProduct();
            
            // Needed for cash back
            $product_star = $this->promotion_dashboard->getProductStar();

            $order_type = $this->promotion_dashboard->getOrderType();
            $warehouse_id = $this->promotion_dashboard->getWareHouseId();
            $wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
            $wh_list = (json_decode($wh_list['sbu'],true));
            $wh_list=$this->add_promotion_request->getWarehouseData($wh_list['118001']);
            //for freeqty
            $products=$this->add_promotion_request->getProductData();
            $masterLookupForTrade = $this->master_lookup->getMasterLookupValues(172);
            $pack_type = $this->master_lookup->getMasterLookupValues(16);
            $product_groups = $this->promotion_dashboard->productGroupData();
            $categorie_groups = $this->promotion_dashboard->categorieGroupData();
            $manufacture_groups = $this->promotion_dashboard->manufactureGroupData();
            return view('Promotions::addPromotionDetails',['getpromotionData' => $getpromotionData,'manufac' => $manufactures,'brand'=>$branddata, 'getstate' => $getstate, 'customer_group' => $customer_group,'product'=>$product_star,'order'=>$order_type,'warehouse'=>$warehouse_id,'select_product' =>$select_product,'warehouse_detail'=>$wh_list,'products'=>$products,'trademasterlookup'=>$masterLookupForTrade,'packs'=>$pack_type,'product_groups'=>$product_groups,'categorie_groups'=>$categorie_groups,'manufacture_groups'=>$manufacture_groups]);
        
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
    /**
     * [productGridDetails shows products with offer type]
     * @param  Request $request [ig grid i/p]
     */
    public function productGridDetails(Request $request){
        try{    
            $makeFinalSql = array();
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            } 

            // make sql for prmt_tmpl_name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("list_details", $filter);
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

            // check for ProductID not IN
            $inputData = $request->input();
            $notindata = isset($inputData['notindata']) ? $inputData['notindata'] : "";

            // Check for the calltype
            $calltype = isset($inputData['calltype']) ? $inputData['calltype'] : "";


            return $this->add_promotion_request->getProductDetailsWithOffertype($makeFinalSql,$page,$pageSize,$orderBy,$notindata,$calltype);
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        } 

    }
    /**
     * [savenewpromotion-saves promotion]
     * @param  Request $request [promotion data like state,customer_type,warehouse, products, brands]
     * @return [view]           [redirects to promotions grid view]
     */
    public function savenewpromotion(Request $request){
            
        try{
            DB::beginTransaction();
        	$newPromotionData = $request->input();
          
            $addAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS003');

            if (!$addAccess) {
                Redirect::to('/')->send();
                die();
            }

            $entity_id = Session::get('legal_entity_id');
            $created_by = Session::get('userId');
            $main_tbl_id = $this->add_promotion_request->saveNewPromotionData($newPromotionData,$entity_id,$created_by); 
            // if user doesnt select all days checkbox and selects individual day  
            if( !isset($newPromotionData['all_days']) && isset($newPromotionData['days']) ){
                $loopCounter = 0;
                foreach ($newPromotionData['days'] as $value) {
                    
                    $date_time_data = array(
                        'prmt_det_id'       =>  $main_tbl_id,
                        'day_name'          =>  $value,
                        'day_time_from'     =>  $newPromotionData['select_day'][$loopCounter],
                        'day_time_to'       =>  $newPromotionData['select_day_to'][$loopCounter]
                    );
                    $loopCounter++;

                    // create a module called promotionDayTimeModel (elequoent model)
                    $this->objPromotionDayTime->insertDayTimeDetails($date_time_data);
                }
            }

            // if user doesnt select any value(Default value) and the user selected all days checkbox
            if( isset($newPromotionData['all_days']) || !isset($newPromotionData['days'])){
                $date_time_data = array(
                    'prmt_det_id'       =>  $main_tbl_id,
                    'day_name'          =>  'all',
                    'day_time_from'     =>  $newPromotionData['select_all_from'],
                    'day_time_to'       =>  $newPromotionData['select_all_to']
                );

                // create a module called promotionDayTimeModel (elequoent model)
                $this->objPromotionDayTime->insertDayTimeDetails($date_time_data);
            }

            // insert into promotion bundle product data
            if( isset($newPromotionData['item_id']) && $newPromotionData['select_offer_tmpl']==2 ){
                $loopCounter = 0;
                foreach ($newPromotionData['item_id'] as $key => $value) {
                    
                    $bundle_data = array(
                        'prmt_det_id'       =>  $main_tbl_id,
                        'applied_ids'       =>  $value,
                        'product_qty'       =>  $newPromotionData['product_qty'][$loopCounter],
                    );
                    
                    $loopCounter++;

                    // create a module called promotionDayTimeModel (elequoent model)
                    $this->objPromotionBundleForQuantity->insertBundleQuantity($bundle_data);
                }
            }

            // insert the slab data if available
            if(isset($newPromotionData['value_two']) && is_array($newPromotionData['value_two']) && $newPromotionData['select_offer_tmpl']==1 ){

                // check if the product ID selected
                if( isset($newPromotionData['item_id']) && is_array($newPromotionData['item_id']) && $newPromotionData['select_offer_tmpl']==1 ){
                    $productIDS = $newPromotionData['item_id'];
                    // Product loop
                    foreach ($productIDS as $prdid) {

                        // inner loop to store the slab
                        $loopCounter=0;
                        foreach ($newPromotionData['value_two'] as $key => $value) {
                            
                            $slab_data = array(
                                'prmt_det_id'       =>  $main_tbl_id,
                                'end_range'         =>  $value,
                                'price'             =>  $newPromotionData['offer_value'][$loopCounter],
                                'product_id'        =>  $prdid,
                                "state_id"          =>  isset($newPromotionData['state'][0]) ?  $newPromotionData['state'][0] : 0,
                                "customer_type"     =>  isset($newPromotionData['customer_group'][0]) ?  $newPromotionData['customer_group'][0] : 0,
                                "start_date"        =>  $newPromotionData['start_date'],
                                "end_date"          =>  $newPromotionData['end_date'],
                                "prmt_lock_qty"     =>  $newPromotionData['prmt_lock_qty'],
                                'prmt_det_status'   =>  isset($newPromotionData['promotion_status']) ? $newPromotionData['promotion_status'] : 0,
                                'product_star_slab' =>  $newPromotionData['product_star_color_table'][$loopCounter],
                                'pack_type'         =>  $newPromotionData['pack_number_table'][$loopCounter],
                                'esu'               =>  $newPromotionData['pack_value_table'][$loopCounter],
                                'created_by'        =>  Session::get('userId'),
                                'wh_id'             => $newPromotionData['warehouse_details']
                            );

                            $loopCounter++;

                            // insert slab rates into database
                            $this->objSlabDetails->insertSlabDetails($slab_data);
                        }
                    }
                }
            } 

            // insert promotional information in CB table
            if( isset($newPromotionData['state_table']) && $newPromotionData['select_offer_tmpl']==5 ){
                
                $loopCounter = 0;
                foreach ($newPromotionData['state_table'] as $key => $value) {
                    
                    
                    $cashback_data = array(
                        'cbk_ref_id'             =>  $main_tbl_id,
                        'state_id'               =>  $newPromotionData['state_table'][$loopCounter],
                        'customer_type'          =>  $newPromotionData['customer_group_table'][$loopCounter],
                        'wh_id'                  =>  $newPromotionData['wareHouseId_table'][$loopCounter],
                        'benificiary_type'       =>  $newPromotionData['Benificiary_table'][$loopCounter],
                        'product_star'           =>  $newPromotionData['ProductStar_table'][$loopCounter],
                        'start_date'             =>  new DateTime($newPromotionData['start_date']),
                        'end_date'               =>  $newPromotionData['end_date']." 23:59:59",
                        'range_from'             =>  $newPromotionData['cash_back_from_table'][$loopCounter],
                        'range_to'               =>  $newPromotionData['cash_back_to_table'][$loopCounter],
                        'cbk_type'               =>  $newPromotionData['offon_percent_table'][$loopCounter],
                        'cbk_value'              =>  $newPromotionData['discount_offer_on_bill_table'][$loopCounter],
                        'cbk_source_type'        =>  1,
                        'cbk_label'              =>  $newPromotionData['cashback_description_table'][$loopCounter],
                        'cbk_status'             =>   isset($newPromotionData['promotion_status']) ? 1:0,
                        'manufacturer_id'        =>   $newPromotionData['offertypemanf_table'][$loopCounter],
                        'brand_id'               =>   $newPromotionData['offertypbrand_table'][$loopCounter],
                        'created_by'             =>   $created_by,
                        'product_value'          =>   $newPromotionData['table_product_value'][$loopCounter],
                        'cap_limit'              =>   $newPromotionData['table_cap_limit'][$loopCounter],
                        'is_self'                =>   $newPromotionData['table_order_type'][$loopCounter],
                        'excl_brand_id'          =>   $newPromotionData['excludebrand_table'][$loopCounter],
                        'product_group_id'       =>   $newPromotionData['prd_grp_table'][$loopCounter],
                        'excl_prod_group_id'     =>   $newPromotionData['table_excl_product'][$loopCounter],
                        'excl_category_id'       =>   $newPromotionData['table_excl_category'][$loopCounter],
                        'excl_manf_id'           =>   $newPromotionData['table_excl_manf'][$loopCounter]
                    );
                    
                   
                    
                    $loopCounter++;

                    // create a module called promotionDayTimeModel (elequoent model)
                    $this->objCashBack->saveNewcashBackData($cashback_data);

                    
                }
               
            }
            if($newPromotionData['select_offer_tmpl'] == 6){
                $freeqty_data=array(
                'ref_id'                 =>  $main_tbl_id,
                'description'            => $newPromotionData['freeqty_description'],
                'product_id'             => $newPromotionData['freeqty_product_id'],
                'product_qty'            => $newPromotionData['product_quantity'],            
                'range_from'             =>  $newPromotionData['freeqty_from'],
                'range_to'               =>  $newPromotionData['freeqty_to'],
                'start_date'             =>  $newPromotionData['start_date'],
                'end_date'               =>  $newPromotionData['end_date'],
                'is_sample'              =>  isset($newPromotionData['is_sample']) ? 1:0,
                'wh_id'                  =>  $newPromotionData['free_wareHouseId'][0],
                'state_id'               =>  $newPromotionData['state'][0],
                'customer_type'          =>  $newPromotionData['customer_group'][0],
                'is_active'              =>  isset($newPromotionData['promotion_status']) ? 1:0,
                'pack_level'             =>  $newPromotionData['freeqty_pack'],
                'created_by'             =>  $created_by,
                );

                $this->freeQtyModel->saveFreeQtyData($freeqty_data);
            }
            //print_r($newPromotionData);exit;
            if($newPromotionData['select_offer_tmpl'] == 7){
                $trade_data = array(
                    'ref_id' => $main_tbl_id,
                    'trade_name' => $newPromotionData['promotion_name'],
                    'object_type' => $newPromotionData['trade_type'],
                    'object_ids' => implode( ',', $newPromotionData['promotion_on']),
                    'warehouse_ids' => implode(',', $newPromotionData['warehouse_details']),
                    'state_ids' => implode(',',$newPromotionData['state']),
                    'cust_types' =>implode(',',$newPromotionData['customer_group']),
                    'pack_type' => implode(',',$newPromotionData['pack_type']),
                    'from_range' => $newPromotionData['trade_from_range'],
                    'to_range' => $newPromotionData['trade_to_range'],
                    'disc_value' => $newPromotionData['tradeoffer_on_bill'],
                    'is_percent' => isset($newPromotionData['trade_percent_cashback']) ? 1:0,
                    'cap_limit' => ($newPromotionData['trade_to_range']*$newPromotionData['tradeoffer_on_bill'])/100,
                    'from_date' => $newPromotionData['start_date'],
                    'to_date' => $newPromotionData['end_date'],
                    'is_active' => isset($newPromotionData['promotion_status']) ? 1:0,
                    'is_self' => $newPromotionData['tradeoffer_type'],
                    'created_by' => $created_by
                );
                $this->tradeDiscount->saveTradeDiscData($trade_data);
            }
            
            // Notifications::addNotification(['note_code' =>'PRM001']);
            DB::commit();
            return redirect('/promotions/viewpromotiondetails');
            
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            DB::rollback();
            Redirect::to('/')->send();
        }
    }

    // Get all the Manufacturer for AJAX Dropdown
    public function getManufac(){
        $getmanu = $this->promotion_dashboard->getManufactureDetailsDropdown();
        $getmanuf = json_decode( json_encode($getmanu), true );       
        return $getmanuf;
    }
    /**
     * [getPackData Get pack information for a single product]
     * @param  [int] $id [product id]
     * @return [Array]     [Pack information of product]
     */
    public function getPackData($id){
        
        $getpackdata    = $this->promotion_dashboard->getPackdataForProduct($id);
        return $getpackdata;
        
       
    }   
    /**
     * [getproductPackData Get pack information for a single product]
     * @param  [int] $id [product_id]
     * @return [Array]     [Pack information of product]
     */
    public function getproductPackData($id){        
        $getpack   = $this->promotion_dashboard->getData($id);
        return $getpack;        
    }
    /**
     * [getBrandsAsManufac Get brands under a manufacturer]
     * @param  Request $request [manufacturer id]
     * @return [array]           [returns brand data with brand id & name]
     */
    public function getBrandsAsManufac(Request $request){
        $input=$request->input();
        $manfData=explode(',',$input['data']);
        $brandData = $this->add_promotion_request->getBrandsData($manfData);
        $items = "";
        foreach ($brandData as $key => $value) {
            $items .= '<option value="'.$value->brand_id.'" >'.$value->brand_name.'</option>';
        }
        return array('status'=>true,'message'=>true,'data'=>$items);
    }
    /**
     * [getTradeItems get trade discount option like brand/manufacturer/product star/sku]
     * @param  [int] $id [type of trade discount]
     * @return [array]     [trade option like brand/manufacturer/product star/sku with their id & name]
     */
    public function getTradeItems($id){
        $data = $this->promotion_dashboard->getTradeDataItems($id);
        $items = "";
        foreach ($data as $key => $value) {
            $items .= '<option value="'.$value->id.'" >'.$value->i_name.'</option>';
        }
        return array('status'=>true,'message'=>true,'data'=>$items);
    }
    
}