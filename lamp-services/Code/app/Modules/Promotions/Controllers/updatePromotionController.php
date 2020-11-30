<?php
/*
FileName : UpdatePromotionController
Author   : eButor
Description :
CreatedDate : 23/August/2016
*/
//defining namespace
namespace App\Modules\Promotions\Controllers;
//loading namespaces
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Modules\Promotions\Models\AddpromotionModel;
use App\Modules\Promotions\Models\promotionDetailsDashboardModel;
use App\Modules\Promotions\Models\updatePromotionModel;
use App\Modules\Promotions\Models\promotionDayTimeModel;
use App\Modules\Promotions\Models\AddPromotionBundleQuantity;
use App\Modules\Promotions\Models\slabDetailsModel;
use App\Modules\Promotions\Models\cashBackModel;
use App\Modules\Promotions\Models\freeQtyModel;
use App\Modules\Cpmanager\Models\MasterLookupModel;
use App\Modules\Promotions\Models\tradeDiscountModel;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use App\Modules\Roles\Models\Role;
use Input;
use DB;
use Session;
use Log;
use Notifications;
use Redirect;
use DateTime;

class updatePromotionController extends BaseController{
    private $add_promotion_request = '';
    private $objPromotionDayTime = '';
    private $objSlabDetails = '';
    private $update_promotion_details = '';
    private $objPromotionBundleForQuantity = '';
    private $objCashBack = '';

    //calling model 
    public function __construct() {
        $this->add_promotion_request = new AddpromotionModel();
        $this->objPromotionDayTime = new promotionDayTimeModel();
        $this->objSlabDetails = new slabDetailsModel();
        $this->_roleRepo = new RoleRepo();
        $this->update_promotion_details = new updatePromotionModel();
        $this->promotion_dashboard = new promotionDetailsDashboardModel();
        $this->objPromotionBundleForQuantity = new AddPromotionBundleQuantity();
        $this->objCashBack = new cashBackModel();       
        $this->_roleModel = new Role();
        $this->freeQtyModel = new freeQtyModel();
        $this->master_lookup = new MasterLookupModel();
        $this->tradeDiscount = new tradeDiscountModel();

        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                parent::Title('Update Promotion');
                $access = $this->_roleRepo->checkPermissionByFeatureCode('PRDS004');

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
     * [editNewPromotion show the view page for update]
     * @param  [int] $updateId [promotion id]
     * @return [view]           [redirects to edit promotion page]
     */
    public function editNewPromotion($updateId){
        try{
            $breadCrumbs = array('Home' => url('/'),'Promotions' => '/promotions/viewpromotiondetails','Promotions Update' => '#');
            parent::Breadcrumbs($breadCrumbs);
             $editAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS004');
            if (!$editAccess) {
                Redirect::to('/')->send();
                die();
            }
            //load promotion details data
            $getpromotionData = $this->update_promotion_details->getpromotiondetailsData($updateId);

            if($getpromotionData == ""){
                Redirect::to('/promotions')->send();
            }

            // load template data
            $templateNames = $this->add_promotion_request->getpromotionData();          

            $getpromotionFreeProducts = $this->update_promotion_details->getFreeProductByID();

            $getSlabItemId=$this->promotion_dashboard->getProductId($updateId);

            $getPackData = array();
            
            //show applied items (product or categeory or brand)
            if( $getpromotionData->prmt_tmpl_Id=="1" ){
                $getProductAndCategory = $this->update_promotion_details->getAppliedItem( $getpromotionData->prmt_offer_on, $getpromotionData->applied_ids, 1 );
                $getPackData = $this->promotion_dashboard->getPackdataForProduct($getSlabItemId);
            }elseif($getpromotionData->prmt_tmpl_Id=="2"){
                $getProductAndCategory = $this->update_promotion_details->getAppliedItemForBundle( $updateId );
            }elseif($getpromotionData->prmt_tmpl_Id=="3"){
                $getProductAndCategory = $this->update_promotion_details->getAppliedItem( $getpromotionData->prmt_offer_on, $getpromotionData->applied_ids, 3 );
            }else{
               $getProductAndCategory="0";
            }
            $getFreeSampleData=array();
            $getPackLevelData=array();
            $getTradeData = array();
            $masterLookupForTrade=array();
            $pack_type=array();
            $equivalentData=array();
            if($getpromotionData->prmt_tmpl_Id=="6"){
                $getFreeSampleData=$this->update_promotion_details->getFreeSampleDataFromChild($updateId);
                $getPackLevelData = $this->promotion_dashboard->getData($getFreeSampleData->product_id);
            }
            if($getpromotionData->prmt_tmpl_Id=="7"){
                $getTradeData=$this->update_promotion_details->getTradeCashbackDataFromChild($updateId);
                $masterLookupForTrade = $this->master_lookup->getMasterLookupValues(172);
                $pack_type = $this->master_lookup->getMasterLookupValues(16);
                $equivalentData = $this->promotion_dashboard->getTradeDataItems($getTradeData->object_type);
            }
            $stateNames = $this->promotion_dashboard->getStateDetailsDropdown();
            //get promotion slab data
            $getSlabData = array();
            if($getpromotionData->is_slab==1){
                $getSlabData = $this->objSlabDetails->getPrmtSlabData($getpromotionData->prmt_det_id);
            }
            //getcustomer  group details
            $get_customer = $this->promotion_dashboard->getCustomerGroupDropdown();
            //getDate and Time
            $getDateAndTime = $this->objPromotionDayTime->getDateAndTime($updateId);
            //get manufacture details
            $getmanfr = $this->promotion_dashboard->getManufactureDetailsDropdown($getpromotionData->prmt_manufacturers);
            //get brand details
            $getbrand = $this->promotion_dashboard->getBrandDetailsDropdown($getpromotionData->prmt_brands);
            $getproduct = $this->promotion_dashboard->getProductStar();
            $getOfferType = $this->promotion_dashboard->getOrderType(); 
            $warehouse_id = $this->promotion_dashboard->getWareHouseId();
            //get cashback data 
            $cashbackdata = $this->promotion_dashboard->getcashBackdataFromTable($updateId);
            $getProductForBill = $this->promotion_dashboard->getProductForBill();
            $selectDayFlag = 'block';
            if( isset($getDateAndTime[0]) && strtolower($getDateAndTime[0]->day_name) !='all'){
                $selectDayFlag='none';
            }

            $wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
            $wh_list = (json_decode($wh_list['sbu'],true));
            $wh_list=$this->add_promotion_request->getWarehouseData($wh_list['118001']);
            $products=$this->add_promotion_request->getProductData();
            $getpromotionData->warehouse=explode(',', $getpromotionData->warehouse);
            $product_groups = $this->promotion_dashboard->productGroupData();
            $categorie_groups = $this->promotion_dashboard->categorieGroupData();
            $manufacture_groups = $this->promotion_dashboard->manufactureGroupData();
            return view('Promotions::updatePromotionDetails',['getpromotionData' => $getpromotionData,'templateNames' => $templateNames,
                'stateData' => $stateNames,'get_customer' =>$get_customer,'getDateAndTime'=>$getDateAndTime,'getProductAndCategory'=>$getProductAndCategory,
                'getSlabData'=>$getSlabData,'getpromotionFreeProducts'=>$getpromotionFreeProducts, 'selectDayFlag'=>$selectDayFlag,'mandata'=>$getmanfr,'branddata'=>$getbrand,'product'=>$getproduct,'order'=>$getOfferType,'warehouse'=>$warehouse_id,'packdata'=>$getPackData,'cashbackdata'=>$cashbackdata,'productBill'=>$getProductForBill,'warehouse_detail'=>$wh_list,'products'=>$products,'getfreesample'=>$getFreeSampleData,'getPackLevelData'=>$getPackLevelData,'trademasterlookup'=>$masterLookupForTrade,'packs'=>$pack_type,'tradeData'=>$getTradeData,'equivalentData'=>$equivalentData,'product_groups'=>$product_groups,'categorie_groups'=>$categorie_groups,'manufacture_groups'=>$manufacture_groups]);

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
    /**
     * [updatenewpromotion Updates promotion]
     * @param  Request $request [Promotion information]
     * @return [view]           [Redirects to promotion view]
     */
    public function updatenewpromotion(Request $request){     

       
        try{
            DB::beginTransaction();
            $updatePromotionData = $request->input();
          
            //print_r($updatePromotionData);exit;
            $editAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS004');

            if (!$editAccess) {
                Redirect::to('/')->send();
                die();
            }

            $created_by = Session::get('userId');
            $entity_id = Session::get('legal_entity_id'); 
            $updatedate = $this->update_promotion_details->updateNewPromotionData($updatePromotionData,$entity_id);  


            /*if($updatePromotionData['select_offer_tmpl'] == '5'){
                $CashBackdata = $this->objCashBack->updateCashBackData($updatePromotionData,$created_by);
            }*/

            //delete the time details before update
            $deleteResult = $this->objPromotionDayTime->deleteDayTimeDetails($updatedate);
            // if user doesnt select all days checkbox and selects individual day 
            if( !isset($updatePromotionData['all_days']) && isset($updatePromotionData['days']) ){
                $loopCounter = 0;
                foreach ($updatePromotionData['days'] as $value) {
                    
                    $date_time_data = array(
                        'prmt_det_id'       =>  $updatedate,
                        'day_name'          =>  $value,
                        'day_time_from'     =>  $updatePromotionData['select_day'][$loopCounter],
                        'day_time_to'       =>  $updatePromotionData['select_day_to'][$loopCounter]
                    );
                    $loopCounter++;

                    // create a module called promotionDayTimeModel (elequoent model)
                    $this->objPromotionDayTime->insertDayTimeDetails($date_time_data);
                }
            }

            // insert into promotion bundle product data
            if( isset($updatePromotionData['item_id']) && $updatePromotionData['select_offer_tmpl']==2 ){

                //delete bundle qty from table 
                $deleteqty = $this->objPromotionBundleForQuantity->deleteBundleQty($updatedate);

                $loopCounter = 0;
                foreach ($updatePromotionData['item_id'] as $key => $value) {
                    
                    $bundle_data = array(
                        'prmt_det_id'       =>  $updatedate,
                        'applied_ids'       =>  $value,
                        'product_qty'       =>  $updatePromotionData['product_qty'][$loopCounter],
                    );
                    
                    $loopCounter++;

                    // create a module called promotionDayTimeModel (elequoent model)
                    $this->objPromotionBundleForQuantity->insertBundleQuantity($bundle_data);
                }
            } 
            if($updatePromotionData['select_offer_tmpl']==4){
                // Do not have subtable value so not doing any activity now
            }
            // if user doesnt select any value(Default value) and the user selected all days checkbox 

            if(isset($updatePromotionData['all_days']) || !isset($updatePromotionData['days']) ){
                $date_time_data = array(
                    'prmt_det_id'       =>  $updatedate,
                    'day_name'          =>  'all',
                    'day_time_from'     =>  $updatePromotionData['select_all_from'],
                    'day_time_to'       =>  $updatePromotionData['select_all_to']
                );

                // create a module called promotionDayTimeModel (elequoent model)
                $this->objPromotionDayTime->insertDayTimeDetails($date_time_data);
            }

            // for cashback data update 

            // delete from cashback table and insert into cashback table
            if( isset($updatePromotionData['Benificiary_table_update']) && $updatePromotionData['select_offer_tmpl']==5 ){

                //delete cashback data from table 
                $deletecashback = $this->objCashBack->deleteCashBackDetails($updatedate);

                $loopCounter = 0;
               // print_r($updatePromotionData);exit;
                foreach ($updatePromotionData['Benificiary_table_update'] as $key => $value) {

                    
                    $cashback_data = array(
                        'cbk_ref_id'             =>  $updatedate,
                        'state_id'               =>  $updatePromotionData['state_table_update'][$loopCounter],
                        'customer_type'          =>  $updatePromotionData['customer_group_table_update'][$loopCounter],
                        'wh_id'                  =>  implode( ',', $updatePromotionData['warehouse_details']),
                        'benificiary_type'       =>  $updatePromotionData['Benificiary_table_update'][$loopCounter],
                        'product_star'           =>  $updatePromotionData['ProductStar_table_update'][$loopCounter],
                        'start_date'             =>  new DateTime($updatePromotionData['start_date']),
                        'end_date'               =>  $updatePromotionData['end_date']." 23:59:59",
                        'range_from'             =>  $updatePromotionData['cash_back_from_table_update'][$loopCounter],
                        'range_to'               =>  $updatePromotionData['cash_back_to_table_update'][$loopCounter],
                        'cbk_type'               =>  $updatePromotionData['offon_percent_table_update'][$loopCounter],
                        'cbk_value'              =>  $updatePromotionData['discount_offer_on_bill_table_update'][$loopCounter],
                        'cbk_source_type'        =>  1,
                        'cbk_label'              =>  $updatePromotionData['description_table_update'][$loopCounter],
                        'cbk_status'             =>  isset($updatePromotionData['promotion_status']) ? 1:0, 
                        'manufacturer_id'        =>  $updatePromotionData['offertypemanf_table_update'][$loopCounter],
                        'brand_id'               =>  $updatePromotionData['offertypbrand_table_update'][$loopCounter],
                        'created_by'             =>  $created_by,
                        'updated_by'             =>  Session::get('userId'),
                        'product_value'          =>   $updatePromotionData['product_value_to_update_table'][$loopCounter],
                        'cap_limit'              =>   $updatePromotionData['cap_limit_to_update_table'][$loopCounter],
                        'is_self'              =>     $updatePromotionData['order_type_to_update_table'][$loopCounter],
                        'excl_brand_id'          =>   $updatePromotionData['offertypexclbrand_table_update'][$loopCounter],
                        'excl_prod_group_id'     =>  $updatePromotionData['update_excl_prdgrp'][$loopCounter],
                        'excl_category_id'       =>  $updatePromotionData['update_excl_category'][$loopCounter],
                        'excl_manf_id'           =>  $updatePromotionData['update_excl_manf'][$loopCounter],
                        'product_group_id'       =>  $updatePromotionData['prdgrp_tbl'][$loopCounter]
                    );
                    
                    $loopCounter++;
                    // save data into cashback table
                    $this->objCashBack->saveNewcashBackData($cashback_data);
                }
            }
            if($updatePromotionData['select_offer_tmpl']==6){
                 $freeqty_data=array(
                'ref_id'                 =>  $updatedate,
                'description'            => $updatePromotionData['update_freeqty_description'],
                'product_id'             => $updatePromotionData['update_freeqty_product_id'],
                'product_qty'            => $updatePromotionData['update_product_quantity'],            
                'range_from'             =>  $updatePromotionData['update_freeqty_from'],
                'range_to'               =>  $updatePromotionData['update_freeqty_to'],
                'start_date'             =>  $updatePromotionData['start_date'],
                'end_date'               =>  $updatePromotionData['end_date'],
                'is_sample'              =>  isset($updatePromotionData['update_is_sample']) ? 1:0,
                'wh_id'                  =>  $updatePromotionData['update_wareHouseId'][0],
                'state_id'               =>  $updatePromotionData['state'][0],
                'customer_type'          =>  $updatePromotionData['customer_group'][0],
                'is_active'              =>  isset($updatePromotionData['promotion_status']) ? 1:0,
                'created_by'             =>  $created_by,
                'pack_level'             =>  $updatePromotionData['update_freeqty_pack'],
                'updated_by'             =>  Session::get('userId'),
                );
                $this->freeQtyModel->updateFreeQtyData($freeqty_data);
            }
            //print_r($updatePromotionData);exit;
            if($updatePromotionData['select_offer_tmpl']==7){
                  $trade_data = array(
                    'ref_id' => $updatedate,
                    'trade_name' => $updatePromotionData['promotion_name'],
                    'object_type' => $updatePromotionData['update_trade_type'],
                    'object_ids' => implode( ',', $updatePromotionData['update_promotion_on']),
                    'warehouse_ids' => implode(',', $updatePromotionData['warehouse_details']),
                    'state_ids' => implode(',',$updatePromotionData['state']),
                    'cust_types' =>implode(',',$updatePromotionData['customer_group']),
                    'pack_type' => implode(',',$updatePromotionData['update_pack_type']),
                    'from_range' => $updatePromotionData['update_trade_from_range'],
                    'to_range' => $updatePromotionData['update_trade_to_range'],
                    'disc_value' => $updatePromotionData['update_tradeoffer_on_bill'],
                    'is_percent' => isset($updatePromotionData['update_trade_percent_cashback']) ? 1:0,
                    'cap_limit' => ($updatePromotionData['update_trade_to_range']*$updatePromotionData['update_tradeoffer_on_bill'])/100,
                    'from_date' => $updatePromotionData['start_date'],
                    'to_date' => $updatePromotionData['end_date'],
                    'is_active' => isset($updatePromotionData['promotion_status']) ? 1:0,
                    'is_self' => $updatePromotionData['update_tradeoffer_type'],
                    'created_by' => $created_by
                );
                $this->tradeDiscount->updateTradeDiscData($trade_data);
            } 

            //delete the slabrates before update
            $deleteResult = $this->objSlabDetails->deleteSlabDetails($updatedate);

            // insert the slab data if available
            if(isset($updatePromotionData['value_two']) && is_array($updatePromotionData['value_two']) && $updatePromotionData['select_offer_tmpl']==1){

                // check if the product ID selected
                if( isset($updatePromotionData['item_id']) && is_array($updatePromotionData['item_id']) && $updatePromotionData['select_offer_tmpl']==1 ){
                    $productIDS = $updatePromotionData['item_id'];
                    // Product loop
                    foreach ($productIDS as $prdid) {

                        // inner loop to store the slab
                        $loopCounter=0;
                        foreach ($updatePromotionData['value_two'] as $key => $value) {

                            // Check here for the existing Slab, if the slab already there then do not save 
                            $chckSlabExist = $this->objSlabDetails->checkSlabExist($updatedate, $value,$updatePromotionData['pack_number_update'][$loopCounter],$updatePromotionData['pack_value_update'][$loopCounter]);
                            if($chckSlabExist==0){

                                    $slab_data = array(
                                    'prmt_det_id'       =>  $updatedate,
                                    'end_range'         =>  $value,
                                    'price'             =>  $updatePromotionData['offer_value'][$loopCounter],
                                    'product_id'        =>  $prdid,
                                    "state_id"          =>  isset($updatePromotionData['state'][0]) ?  $updatePromotionData['state'][0] : 0,
                                    "customer_type"     =>  isset($updatePromotionData['customer_group'][0]) ?  $updatePromotionData['customer_group'][0] : 0,
                                    "start_date"        =>  $updatePromotionData['start_date'],
                                    "end_date"          =>  $updatePromotionData['end_date'],
                                    "prmt_lock_qty"     =>  $updatePromotionData['prmt_lock_qty'],
                                    "prmt_det_status"   =>  isset($updatePromotionData['promotion_status']) ? $updatePromotionData['promotion_status'] : 0,
                                    "updated_by"        =>  Session::get('userId'),
                                    "updated_at"        =>  date("Y-m-d H:i:s"),
                                    'product_star_slab' =>  $updatePromotionData['product_star_color_table'][$loopCounter],
                                    'pack_type'         =>  $updatePromotionData['pack_number_update'][$loopCounter],
                                    'esu'               =>  $updatePromotionData['pack_value_update'][$loopCounter],
                                
                                    'created_by'        =>  Session::get('userId'),
                                    'wh_id'             =>  $updatePromotionData['warehouse_details']
                                );
                                // insert slab rates into database
                                $this->objSlabDetails->insertSlabDetails($slab_data);
                            }
                            $loopCounter++;
                            
                        }
                    }
                }
            }
            
            Notifications::addNotification(['note_code' =>'PRM004']);
            DB::commit(); 
            return redirect('/promotions/viewpromotiondetails');
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            DB::rollback();
            Redirect::to('/')->send();
        }
    } 

}