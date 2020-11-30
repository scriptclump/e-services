<?php
/*
FileName : pricingDashboadController
Author   : eButor
Description :
CreatedDate :14/Aug/2016
*/
//defining namespace
namespace App\Modules\Pricing\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;
use App\Modules\Pricing\Models\pricingDashboardModel;
use App\Modules\Pricing\Controllers\uploadPriceSlabFiles;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use Input;
use Log;
use Session;
use DB;
use Redirect;
use Notifications;
use UserActivity;


class pricingDashboadController extends BaseController{

	private $objCommonGrid = '';
    private $objPricing = '';

	//calling model 
    public function __construct() {

    	// get common controller reff
        $this->_roleRepo = new RoleRepo();
        $this->objCommonGrid = new commonIgridController();
        $this->objPricing = new pricingDashboardModel();

        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                parent::Title('Ebutor - Pricing');
                $access = $this->_roleRepo->checkPermissionByFeatureCode('SCH001');
                if (!$access && Session::get('legal_entity_id')!=0) {
                    Redirect::to('/')->send();
                    die();
                }
                return $next($request);
            });

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

	// pricing DashBoard / Index Controller
	public function pricingDashboard(){

		try{
			$breadCrumbs = array('Home' => url('/'),'Pricing' => '#', 'Dashboard' => '#');
			parent::Breadcrumbs($breadCrumbs);

			//$getProductDetails = $this->objPricing->getProductDetails();
			$getManufactureDetails = $this->objPricing->getManufactureDetails();
			$getBrandDetails = $this->objPricing->getBrandDetails();

            $getCategoryDetails = $this->objPricing->getCategoryDetails();
            $getStateDetails = $this->objPricing->getStateDetails();
            $getCustomerGroup = $this->objPricing->getCustomerGroup();
            
            $beneficiaryName = $this->objPricing->getBenificiaryName();
            $wareHouse = $this->objPricing->getWarehouses();
            $product_stars = $this->objPricing->getProductStars();
            // new requirement from satish and naresh
            $dcs = $this->objPricing->getAllDCS();

            $addPriceAccess=1;
            $uploadPriceAccess=1;
            if(Session::get('legal_entity_id')!=0){
                $addPriceAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRC002');
                $uploadPriceAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRC005');
            }

			return view('Pricing::pricingDashboard', ['getManufactureDetails'=>$getManufactureDetails, 'getBrandDetails'=>$getBrandDetails, 'getCategoryDetails'=>$getCategoryDetails, 'getStateDetails'=>$getStateDetails, 'getCustomerGroup'=>$getCustomerGroup, 'addPriceAccess'=>$addPriceAccess, 'uploadPriceAccess'=>$uploadPriceAccess,'beneficiaryName' => $beneficiaryName ,'wareHouses' => $wareHouse,'product_stars'=>$product_stars,'dcs'=>$dcs]); //'getProductDetails'=>$getProductDetails,
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	//filterization for required data
    public function pricingData(Request $request){
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }

        // make sql for Product name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("effective_date", $filter, true);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // make sql for Product name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("product_title", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for Product name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("sku", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for state name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("StateName", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for customertype
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("CustomerTypeName", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for Price
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("price", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

         // make sql for Ptr
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ptr", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // make sql for brand name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("BrandName", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for manf name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ManufacName", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for category name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Category", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // make sql for ELP 
        // $fieldQuery = $this->objCommonGrid->makeIGridToSQL("elp", $filter);
        // if($fieldQuery!=''){
        //     $makeFinalSql[] = $fieldQuery;
        // }
        // make sql for ELP 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("cpEnabled", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for ELP 
        // $fieldQuery = $this->objCommonGrid->makeIGridToSQL("DI", $filter);
        // if($fieldQuery!=''){
        //     $makeFinalSql[] = $fieldQuery;
        // }
        // // make sql for ELP 
        // $fieldQuery = $this->objCommonGrid->makeIGridToSQL("MI", $filter);
        // if($fieldQuery!=''){
        //     $makeFinalSql[] = $fieldQuery;
        // }
        // // make sql for ELP 
        // $fieldQuery = $this->objCommonGrid->makeIGridToSQL("CI", $filter);
        // if($fieldQuery!=''){
        //     $makeFinalSql[] = $fieldQuery;
        // }


         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("dc_id", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

         $fieldQuery = $this->objCommonGrid->makeIGridToSQL("DCName", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }


        // Process data for Extra Search
        if( $request->input('productname')!='' ){
            $makeFinalSql[] = "product_id = " . $request->input('productname');
        }
        if( $request->input('manufac')!='' ){
            $makeFinalSql[] = "manufacturer_id = " . $request->input('manufac');
        }
        if( $request->input('brand')!='' ){
            $makeFinalSql[] = "brand_id = " . $request->input('brand');
        }

        // Arrange Grid Sort here
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

        // Check for the Data Edit Access and Delete Access
        $editAccess=1;
        $deleteAccess=1;
        if(Session::get('legal_entity_id')!=0){
            $editAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRC003');
            $deleteAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRC004');
        }

        return $this->objPricing->viewPricingDetailsData($makeFinalSql, $orderBy, $page, $pageSize, $editAccess,$deleteAccess);

    }

    public function getlist(){   
        $term = Input::get('term');
        $manufacID = Input::get('manufac');
        $brandID = Input::get('brand');
        $product_arr = $this->objPricing->productForSearch($term, $manufacID, $brandID);
        echo json_encode($product_arr);
    }

    // Get all Brands as per Manufacture
    public function getBrandsAsManufac($manufac){
        return  $this->objPricing->getBrandsAsManufacId($manufac);
    }

    public function getProductAsBrand($brand_id){
        return  $this->objPricing->getProductsAsPerBrand($brand_id) ;
    }

    public function getProductbyID($myId){
        return  $this->objPricing->getProductDatabyID($myId);
    }
    public function deletePriceDetails(Request $request){

        $deleteData = $request->input('deleteData');
        // Get SKU 
        $data=json_decode(json_encode( $this->objPricing->getSkuforNotification($deleteData)), true);
        $productSKU = $data['sku'];
        $this->objPricing->deletePricingData($deleteData);
        Notifications::addNotification(['note_code' =>'PRS004', 'note_params' => ['SKU' => $productSKU]]);
    }

    public function addEditSlabData(Request $request){
        DB::beginTransaction();
        try {
            $product_tax_flag = $request->input("product_tax_flag");
            if($product_tax_flag==0){
                $displayMSG = trans('pricing.UI_PRICE_TAX_NOT_FOUND');
                return $displayMSG;
                die();
            }

            $product_id = $request->input("add_prd_id");
            $productName = $request->input("product_name");
            //$state_id = $request->input("add_state");

            if($request->input("add_dc") > 1){
                $dc_id = $request->input("add_dc");
            }else{
                $dc_id = $request->input("hidden_add_dc");
            }

            $customer_type = $request->input("add_custgroup");
            $price = $request->input("selling_price");
            $ptr = $request->input("price_ptr");
            $originalDate = $request->input("date"); 
            $originalDate = str_replace('/', '-', $originalDate);

            $created_by = Session::get('userId'); 
            $add_edit_flag = $request->input("add_edit_flag");
            $availableDate = date("Y-m-d", strtotime($originalDate) );  

            // store the product price ID for edit
            $product_price_id = $request->input("product_price_id");

            // Get product information for Notificaion
            $productSKU = $this->objPricing->getProductDatabyID($product_id);
            if(is_array($productSKU)){
                $productSKU = isset($productSKU[0]) ? $productSKU[0]->sku : "";
            }     

            // get the state by dc id
             $state_id = $this->objPricing->getstateIdByDC($dc_id);



            // add that date into this array (that will save your data)
            $slab_data = array(
                'product_id'        => $product_id,
                'state_id'          => isset($state_id) ? $state_id : 4033,
                'customer_type'     => $customer_type,
                'price'             => $price,
                'ptr'               => $ptr,
                'effective_date'    => $availableDate,
                'created_by'        => $created_by,
                'created_at'        => date('Y-m-d'),
                'legal_entity_id'   => Session::get('legal_entity_id'),
                'dc_id'             => $dc_id
            );

            $responseMSG = $this->objPricing->addEditProductPrice($slab_data, $product_price_id, $productName);

            $displayMSG = "";
            $responseData = $responseMSG;
            $responseMSG = $responseMSG['status'];
            $oldCustType = isset($responseData['oldCustType'])?$responseData['oldCustType']:0;
            $new_price = isset($responseData['new_price'])?$responseData['new_price']:0;
            if($responseMSG==1){
                $displayMSG = trans('pricing.UI_PRICE_UPDATE_MSG');
            }elseif($responseMSG==2){
                $displayMSG = trans('pricing.UI_PRICE_ADD_MSG');
            }elseif($responseMSG==3){
                $displayMSG = trans('pricing.UI_PRICE_ADD_DUPLICATE_REC_MSG');
            }elseif($responseMSG==10){
                $displayMSG = "Your are trying to change the date which is currently active, So update aborted!";
            }else{
              $displayMSG = "No response found!";
            }

            if($product_price_id==0)
            {
                $response = Notifications::addNotification(['note_code' =>'PRS002', 'note_params' => ['SKU' => $productSKU]]); 
            }elseif($responseMSG!=3){
                $response = Notifications::addNotification(['note_code' => 'PRS003', 'note_params' => ['SKU' => $productSKU]]);            
            }

            $pricObj = new uploadPriceSlabFiles();
            if($responseMSG == 1 || $responseMSG == 2 ){
                $appKeyData = env('DB_DATABASE');
                $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type.'_le_wh_id_'.$dc_id;
                $cache_array = array("product_id"=>$product_id,"le_wh_id"=>$dc_id,"customer_type"=>$customer_type);
                $pricObj->clearCache($keyString,1,$cache_array);
            }
            // inserting data into price change table
            if($responseMSG == 1 || $responseMSG == 2){
                $inv = $pricObj->checkInventory($product_id,$dc_id);
                if($responseData['old_price'] != "" && $responseData['old_price'] != $price && $customer_type == $oldCustType && $new_price != 0 && $availableDate >= date('Y-m-d')){
                    $price_change_data = array(
                            'stock'             => $inv,
                            'product_id'        => $product_id,
                            'customer_type'     => $customer_type,
                            'old_price'         => $responseData['old_price'],
                            'new_price'         => $price,
                            'price_difference'  => $responseData['old_price'] - $price,
                            'effective_date'    => $availableDate,
                            'created_by'        => $created_by,
                            'le_wh_id'          => $dc_id
                        );
                    $pricObj->insertPriceChanges($price_change_data);
                }
            }
            DB::commit();
            return $displayMSG;
        }catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "rollback";
        }
    }

    public function getUpdateData($priceid){

        $priceData = $this->objPricing->getPriceByID($priceid);
        $cashBack = $this->objPricing->getCashback($priceid);

        // echo "<pre/>";print_r($cashBack);exit;

        $detailsHTMLPart = "";
        foreach ($cashBack as $value) {

            $is_percent = "";
            if($value->cbk_type == 1){
                $is_percent = '%';
            }else{
                $is_percent = '&#x20B9;';
            }
            $benificiary_type="";
            $state_id = "";
            $customer_type = "";
            $product_star = "";
            $wh = "";
            if($value->benificiary_type == ""){
                $benificiary_type = "All"; 
            }else{
                $benificiary_type = $value->Benificiary; 
            }
            if($value->state_id == ""){
                $state_id = "All"; 
            }else{
                $state_id =$value->StateName ; 
            }
            if($value->customer_type == ""){
                $customer_type = "All"; 
            }else{
                $customer_type = $value->CustomerType; 
            }
            if($value->product_star == ""){
                $product_star = "All"; 
            }else{
                $product_star = $value->ProductStart; 
            }
            if($value->wh_id == ""){
                $wh= "All"; 
            }else{
               $wh= $value->WareHouse ; 
            }


            $detailsHTMLPart .= '<tr class="gradeX odd">
                <td id="description" class="prom-font-size">'.$value->cbk_label.'</td>
                <td id="benificiary" class="prom-font-size">'.$benificiary_type.'</td>
                <td id="product_star_TD" class="prom-font-size">'.$product_star.'</td>
                <td id="start_date" class="prom-font-size">'.$value->StartDate.'</td>
                <td id="end_date" class="prom-font-size">'.$value->endDate.'</td>
                <td id="state" class="prom-font-size">'.$state_id.'</td>
                <td id="cust_group" class="prom-font-size">'.$customer_type.'</td>
                <td id="offer_value" class="prom-font-size">'.$value->cbk_value.'</td>
                <td id="quantity" class="prom-font-size">'.$value->range_to.'</td>
                <td id="warehouse" class="prom-font-size">'.$wh.'</td>
                <td id="cbk_type" class="prom-font-size">'.$is_percent.'</td>
                <td><a class="btn btn-icon-only default delcondition" onclick="DeleteCashBack('.$value->cbk_id.',this)"><i class="fa fa-trash-o"></i></a></td>
                </tr>';
        }


        $AllPricedata = array(
            'Maindata'=>$priceData,
            'Cashback'=>$detailsHTMLPart
            );

        return $AllPricedata;
    }

    public function getRightSideInfo($product_id, $dcid){

        // get the state id by dcid

        $stateid = $this->objPricing->getstateIdByDC($dcid);

        // call fro same state
        $getTax = $this->getTaxByState($product_id, $stateid, $stateid);
        // get VAT
        $taxDataVAT = "";
        $taxCGST=0;
        $taxSGST=0;
        if( $getTax['Status']=='200' ){
            foreach($getTax['ResponseBody'] as $data){
                if($data['Tax Type']=='GST'){
                    $taxDataVAT = $data['Tax Percentage'];
                }
                
                $taxCGST = $data['CGST'];
                $taxSGST = $data['SGST'];
                
            }
        }

        // call fro same state
        // $stateidBeller = 0;
        // $getOtherState = $this->objPricing->getStateForTAX($stateid); 
        // if(isset($getOtherState->zone_id)){
        //     $stateidBeller = $getOtherState->zone_id;
        // }else{
        //     $stateidBeller = $stateid;
        // }
        $getTaxCST = $this->getTaxByState($product_id, $stateid, $stateid);
        // get CST
        $taxDataCST = "";
        $taxDataUTST = "";
        if( $getTaxCST['Status']=='200' ){
            foreach($getTaxCST['ResponseBody'] as $data){
                if($data['Tax Type']=='IGST'){
                    $taxDataCST = $data['Tax Percentage'];
                }
                if($data['Tax Type']=='UTGST'){
                    $taxDataUTST = $data['Tax Percentage'];
                }
            }
        }
        $taxData = $taxDataVAT . "==!!==" . $taxDataCST . "==!!==" . $taxCGST . "==!!==" . $taxSGST . "==!!==" .$taxDataUTST;

        if($taxDataVAT=="" && $taxDataCST=="" && $getTax['ResponseBody'] == 'Tax Mapping for Product Not Found' && $getTaxCST['ResponseBody'] == 'Tax Mapping for Product Not Found'){
            $taxData="no tax";
        }

        return $taxData;
    }

    public function getTaxByState($product_id, $stateidSeller, $stateidBeller){

        $url = env('APP_TAXAPI');
        $callType = "POST";
        $postData = array(
                    'product_id' => $product_id, 
                    'seller_state_id' => $stateidSeller,
                    'buyer_state_id' => $stateidBeller
                );

        $postData = json_encode($postData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'api_key: testkey',
            'api_secret: testsecret',
            'Content-Type: application/json'
        ));
        if ($callType == "POST") {
            curl_setopt($ch, CURLOPT_POST, count($postData));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        $output = curl_exec($ch);
        curl_close($ch);

        $outputs = json_decode($output, true);

        return $outputs;

    }

    public function saveCashBackData(Request $request){


        $Cashback = $request->input();
        $cashback_data = array(
            'cbk_ref_id'                => $Cashback['cashback_ref_id'],
            'cbk_source_type'           => 2,
            'cbk_label'                 => $Cashback['cashback_text'],
            'state_id'                  => intval($Cashback['cashback_state']),
            'product_id'                => $Cashback['cashback_product_id'],
            'customer_type'             => intval($Cashback['cashback_custgroup']),
            'wh_id'                     => intval($Cashback['cashback_warehouse']),
            'benificiary_type'          => intval($Cashback['cashback_for']),
            'product_star'              => intval($Cashback['cashback_product_star']),
            'start_date'                => $Cashback['cashback_start_date'],
            'end_date'                  => $Cashback['cashback_end_date'],
            'range_to'                  => $Cashback['cashback_quantity'],
            'cbk_type'                  => isset($Cashback['is_percent']) ? 1 : 0,
            'created_by'                => Session::get('userId'),
            'cbk_value'                 => $Cashback['offer_value'],
            'cbk_status'                => 1
        );

        $refId = $this->objPricing->saveCashBackDataIntoTable($cashback_data);
        if($cashback_data['cbk_type'] == 1 ){
            $cashback_data['cbk_type']  = '%';

        }else{
            $cashback_data['cbk_type']  = '&#x20B9;';
        }
        
        if($refId == 0){
            $cashback_data['cashBackrefId'] = "none";
            $cashback_data['cashbackResponse'] = "Cashback combination already found!";
        }else{
            $cashback_data['cashBackrefId'] = $refId;
            $cashback_data['cashbackResponse'] = "Cashback added successfully!";
        }
        return $cashback_data;



    }
    public function deleteCashBackData(Request $request){

        $reid = $request->input('refId');
        $refId = $this->objPricing->deleteCashBackDataById($reid);

        return $refId;
    }
}