<?php

namespace App\Modules\Assets\Controllers;
use App\Http\Controllers\BaseController;
use App\Modules\Assets\Models\assetsModel; 
use App\Modules\Assets\Models\assetsImplodeModel;  
use App\Modules\Assets\Models\assetsHistoryModel;
use Log;
use App\Modules\Assets\Controllers\commonIgridController;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\ProductRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Response;
use Redirect;
use Session;
use Input;
use Excel;
use Carbon\Carbon;
use Notifications;
use DB;

class assetsController extends BaseController {

	public function __construct(){
		$this->_roleRepo = new RoleRepo();
		$this->objAsset = new assetsModel();

		$this->objAssetImplode = new assetsImplodeModel();

		$this->objCommonGrid = new commonIgridController();
		$this->objAssetsHistory = new assetsHistoryModel();
		$this->_productRepo = new ProductRepo();

	}

	public function assetDashboard(){
		try{
		$breadCrumbs = array('HOME' => url('/'),'ASSETS DASHBOARD' => '#', 'DASHBOARD' => '#');
		parent::Breadcrumbs($breadCrumbs);

		if (!Session::has('userId')) {
                Redirect::to('/login')->send();
        }

        $access = $this->_roleRepo->checkPermissionByFeatureCode('AST001');
        if (!$access && Session::get('legal_entity_id')!=0) {
            Redirect::to('/')->send();
            die();
        }
		
		$getManufactureDetails = $this->objAsset->getManufactureDetails();
		$getCategoryDetails = $this->objAsset->getCategoryDetails();
		$getAssetCategoryDetails=$this->objAsset->getAssetCategoryDetails();
		$allocationNames = $this->objAsset->getNamesFromUsersTable();
		$businessUnit = $this->objAsset->getBusinessData();
		$assetClassification =$this->objAsset->getAssetClassification();
		
		$assetTotalCost = $this->objAsset->getAssetTotalCost();
		$value=$assetTotalCost[0]->Total;
		$TotalValue=round($value,2);

		$addAssetsAccess=1;
		$importAssetsAccess=1;

		$env = env('APP_ENV_URL');
 
        if(Session::get('legal_entity_id')!=0){
            $addAssetsAccess = $this->_roleRepo->checkPermissionByFeatureCode('AST002'); 
            $importAssetsAccess = $this->_roleRepo->checkPermissionByFeatureCode('AST006');
        }
        return view('Assets::assets',['getManufactureDetails'=>$getManufactureDetails,'getCategoryDetails'=>$getCategoryDetails,'allocationNames'=>$allocationNames,'businessUnit'=>$businessUnit,'assetClassification'=>$assetClassification,'addAssetsAccess'=>$addAssetsAccess,'assetcategory'=>$getAssetCategoryDetails,'importAssetsAccess'=>$importAssetsAccess,'assetTotalCost' =>$TotalValue,'env'=>$env]);

		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function assetDashboardData(Request $request){
		try{
			$makeFinalSql = array();


			
			$filter = $request->input('%24filter');
		    if( $filter=='' ){
		        $filter = $request->input('$filter');
		    }

		    // make sql for collection code
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("AssetDetails", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for collection code
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("AssetCatMrp", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for collection code
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TotalAsset", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for collection code
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TotalAllocated", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for TotalRepaired
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TotalRepaired", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for TotalAvailable
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TotalAvail", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for TotalWarranty
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TotalWarranty", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		    // make sql for TotalOutOfWarranty
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TotalOutOfWarranty", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for TotalOutOfWarranty
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("AssetCategory", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }

		     // make sql for movable
		    $fieldQuery = $this->objCommonGrid->makeIGridToSQL("is_movable", $filter);
		    if($fieldQuery!=''){
		        $makeFinalSql[] = $fieldQuery;
		    }


		    // Check for the Data Add Access,Edit Access And View Access Using RBAC
		    $addAccess=1;
		    $editAccess=1;
		    $viewAccess=1;
		    if(Session::get('legal_entity_id')!=0){
		        $addAccess = $this->_roleRepo->checkPermissionByFeatureCode('AST004');
		        $editAccess = $this->_roleRepo->checkPermissionByFeatureCode('AST003');
		        $viewAccess = $this->_roleRepo->checkPermissionByFeatureCode('AST005');
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

			$assetdata =  json_decode(json_encode($this->objAsset->DetailsAsPerAsset($makeFinalSql, $orderBy, $page, $pageSize )),true);


		 	$assetDetailsData = array();
			$loopCounter=0;
			$AssetMrp=0;
			$noOfAssets=0;
			$noOfAvailable=0;

			foreach ($assetdata as $value) {

				$actionButton= '<a href= "/editproduct/'.$value['product_id'].'" target="_blank"><i class="fa fa-pencil"></i></a>';

				$AssetMrp  +=  $value['TotalAssetValue'];
				$noOfAssets += $value['TotalAsset'];
				$noOfAvailable += $value['TotalAvail'];


				$assetDetailsData[$loopCounter] = array(
					"product_id"				=> 	$value['product_id'],
					"AssetDetails"				=> 	$value['AssetDetails'],
					"TotalAsset"				=> 	$value['TotalAsset'],
					"TotalAllocated"			=> 	$value['TotalAllocated'],
					"TotalRepaired"				=> 	$value['TotalRepaired'],
					"TotalAvail"			=> 	$value['TotalAvail'],
					"is_movable"					=>  $value['is_movable'],
					"TotalWarranty"				=> 	$value['TotalWarranty'],
					"TotalOutOfWarranty"		=> 	$value['TotalOutOfWarranty'],					
					"AssetCategory"				=>	$value['AssetCategory'],
					"AssetMRP"					=>  $AssetMrp,
					"AssetCatMrp"				=> 	$value['AssetCatMrp'],
					"CustomAction"				=> 	$actionButton,
					"asset_details"				=> 	json_decode(json_encode( $this->objAsset->totalAssetDetails($value['product_id']) ) )
				);

				
				$loopCounter++;				
				
			}	
			
			return json_encode($assetDetailsData);
			
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function updateAssetData(Request $request){
		
		try{
			$updatedata = $request->input();

			
			$assetid=$updatedata['asset_id'];

			$checkcount = $this->objAsset->countWithAssetData($assetid);

			$updateId=$this->objAsset->updateAssetInformationData($updatedata,$assetid);
			$product_id_update=$updatedata['product_id_update'];

			$assetUpdateInProducts=$this->objAsset->UpdateAssetCategoryInProducts($product_id_update,$updatedata['asset_category_id']);



			return "Asset Updated Succesfully";
		}
		catch (\ErrorException $ex) {
				Log::error($ex->getMessage());
				Log::error($ex->getTraceAsString());
				Redirect::to('/')->send();
			}
	}

	public function getDetailsFromAssets($id){

		return $this->objAsset->getDetailsFromAssetsTable($id);
	}

	public function getUserListDetails(){
		try{
			$term = Input::get('term');
			$user_name = $this->objAsset->getUserDetails($term);
			echo json_encode($user_name);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function getAssetHistoryDetails($id){

		try{

			$history=$this->objAsset->getAssetHistoryData($id);

			$assetname="";
			$srno="";
			$image="";

			$historyHTML="";
			$loopCounter = 1;

			foreach($history as $data){

				$assetname= $data->producttitle . "( ". $data->company_asset_code ." )";
				$srno = $data->serial_number;
				$image = $data->image;
				$historyDate=date("d-m-Y",strtotime($data->fromdate));

				$historyHTML .= '
		                    <tr>
		                        <td id="hist_date">'.$data->allocation_name.'</td> 
		                        <td id = "reffNo">'.$data->allocation_status.'</td> 
		                        <td id="prev_status">'.$historyDate.'</td> 
		                    </tr>';
				$loopCounter++;
			}

			$returnDataArray = array(
				'historyHTML'	=> $historyHTML,
				'assetname'		=>	$assetname,
				'srno'			=> $srno,
				'profImage'		=> $image
			);
			
			return $returnDataArray;
		
		}catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function getBrandsAsManufac($manufac){
		return  $this->objAsset->getBrandsAsManufacId($manufac);
	}

	public function saveDataIntoProductsTable(Request $request){
		try{
			$product_data = $request->input();
			// Product Image code
			$photo = $request->file('proof_image');
			$EntityType="products";
			$type=1;
			$url="";
			if(is_object($photo)){
				$url=$this->_productRepo->uploadToS3($photo,$EntityType,$type);
			}
			
			$sku=$this->_productRepo->generateSKUcode();
			$product_id = $this->objAsset->saveAssetProductIntoTable($product_data,$url,$sku);

			$this->objAsset->saveIntoPackCinfigTable($product_id);

			//return "Product Saved Succesfuly! SKU Code : '".$sku."'. <a href='".env("APP_ENV_URL") . "productpreview/" . $product_id . "' target='_blank'>Please See the Product Here</a>";
			return "Product Saved Succesfuly! SKU Code : " . $sku;  
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}    	
	}	

	public function getInwardProductwithId($id){
		try{

			//get the productid with help of  assetid from assets
			$productid=$this->objAsset->getProductId($id);

			$count=$this->objAsset->getInwardDetails($productid);
			$data=$this->objAsset->getDetailsFromAssetsTable($id);

			$returnDataArray = array(
				'productCount'	=> $count,
				'productData'	=>	$data
			);

			return $returnDataArray;
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function saveAllocateData(Request $request){

		try{	

			$allocationdata = $request->input();
			$allocatedid=$allocationdata['asset_user_id'];
			$assetid=$allocationdata['hidden_asset_id'];

			$checkcount = $this->objAssetsHistory->countWithAssetHistoryData($allocationdata['hidden_asset_id']);

			if($checkcount==0){
				$this->objAssetsHistory->saveIntoHistoryTable($allocationdata);

				$this->objAsset->updateAllocateid($allocatedid,$assetid,$allocationdata['select_part'],$allocationdata);

				return "Allocation Added Succesfully";

			}else{

				//update only fromdate in history table
				$this->objAssetsHistory->updateAssetInformationHistoryTable($allocationdata);
				//save the data into history table
				$this->objAssetsHistory->saveIntoHistoryTable($allocationdata);
				//update the allocated to in main table
				$this->objAsset->updateAllocateid($allocatedid,$assetid,$allocationdata['select_part'],$allocationdata);
				return "Allocation Updated Succesfully";
			}
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}



	public function downloadExcelWithAssets(Request $request){
		try{

			$exp_manufac = $request->input("exp_manufac");
	        $exp_brand = $request->input("exp_brand");
	        $exp_category = $request->input("exp_category");
	        $exp_asset_name = $request->input("exp_asset_name");

	        $selectionQueryInner = array();

	        if($exp_manufac!='' && $exp_manufac!='all'){
	            $selectionQueryInner[] = "prd.manufacturer_id = '".$exp_manufac."'";
	        }
	        if($exp_brand!='' && $exp_brand!='all'){
	            $selectionQueryInner[] = "prd.brand_id = '".$exp_brand."'";
	        }

	        if($exp_category!='' && $exp_category!='all'){
	            $selectionQueryInner[] = "prd.category_id = '".$exp_category."'";
	        }
	        if($exp_asset_name!='' && $exp_asset_name!='all'){
	            $selectionQueryInner[] = "prd.product_id = '".$exp_asset_name."'";
	        }
			$headers = array('S NO','MANUFACTURER NAME','BRAND','PRODUCT CATEGORY','ASSET NAME','ASSET CODE','SERIAL NUMBER','PURCHASE DATE','WARRANTY STATUS','ASSET STATUS','ALLOCATED TO','DEPRECIATION AGE','RESIDUAL VALUE','ASSET CATEGORY','IS_MOVABLE','PRODUCT VALUE');
			 $headers_second_page = array('ASSET CATEGORY');

			$exceldata = json_decode($this->objAsset->getDataAsPerQueryForAsset($selectionQueryInner));
			$AssetCategoryData=json_decode($this->objAsset->getAssetCategoryData(),true);
			

			 $AssetCategoryCounter = 0;
            $exceldata_second = array();
            foreach($AssetCategoryData as $val){

            	$exceldata_second[$AssetCategoryCounter]['asset_category']=isset($AssetCategoryData[$AssetCategoryCounter])?$AssetCategoryData[$AssetCategoryCounter]['master_lookup_name']:'';
            	$AssetCategoryCounter++;
            	
			
            }

			$mytime = Carbon::now();

			Excel::create('Asset Template Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $exceldata,$headers_second_page, $exceldata_second) 
	        {

	            $excel->sheet("Assets", function($sheet) use($headers, $exceldata)
	            {
	                $sheet->loadView('Assets::downloadAssetTemplate', array('headers' => $headers, 'data' => $exceldata)); 
	            });
	             $excel->sheet("AssetsCategory", function($sheet) use($headers_second_page, $exceldata_second)
	            {
	                $sheet->loadView('Assets::AssetCategoryTemplate', array('headers' => $headers_second_page, 'data' => $exceldata_second)); 
	            });

	        })->export('xlsx');

		}catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function downloadExcelWithDepreciation(Request $request){
		try{

			$category = $request->input("dep_category");
	        $assetid = $request->input("dep_asset_name");

	        $selectionQuery = array();

	        if($category!='' && $category!='all'){
	            $selectionQuery[] = " category_id = '".$category."'";
	        }
	        if($assetid!='' && $assetid!='all'){
	            $selectionQuery[] = " product_id = '".$assetid."'";
	        }

			$headers = array('ASSET NAME','PURCHASE DATE','MRP','DEP AGE','RESIDUAL VALUE','DEPRECIATION(%)','YEAR ENDING ON','YEARS','TOTAL DAYS','OPENING ASSET VALUE','DEPRECIATION','ASSET CLOSING VALUE');

			$exceldata = json_decode($this->objAsset->getDataAsPerQueryForDepreciationCalculation($selectionQuery));

			$mytime = Carbon::now();

			Excel::create('Depreciation Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $exceldata) 
	        {

	            $excel->sheet("Assets", function($sheet) use($headers, $exceldata)
	            {
	                $sheet->loadView('Assets::downloadDepreciationData', array('headers' => $headers, 'data' => $exceldata)); 
	            });
	        })->export('xlsx');

	    }catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function loadManufactureData(){
		return  $this->objAsset->getManufactureDetails();
	}

	public function loadCategories(){
		return  $this->objAsset->getCategoryDetails();
	}

	public function readExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get()->all();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Redirect::to('/')->send();
        }
    }


    // This method should called by the GRN TEAM
	// Once the GRN is done for the Product
	// @ProductID : ID of the product
	// @qry : number of quantity they are doing GRN of
	public function saveAssetDetails($productInfo){
		try{

			foreach($productInfo as $productData)
			{
				$qty = isset($productData['qty']) ? $productData['qty'] : 0;
				$product_id = isset($productData['product_id']) ? $productData['product_id'] : 0;
				$createdByID = isset($productData['created_by']) ? $productData['created_by'] : 0;
				$purchase_date = isset($productData['invoice_date']) ? $productData['invoice_date'] : "";
				$invoice_no = isset($productData['invoice_no']) ? $productData['invoice_no'] : 0;

				//sending values from import excel
				$businessunitname = isset($productData['business_unit_id']) ? $productData['business_unit_id'] : 0;
				
				$warentyEndDate =isset($productData['warranty_end_date']) ? $productData['warranty_end_date'] : 0;
				$WarrantyYear =isset($productData['WarrantyYear']) ? $productData['WarrantyYear'] : 0;
				$WarrantyMonts =isset($productData['WarrantyMonts']) ? $productData['WarrantyMonts'] : 0;

				$depresiationDate =isset($productData['depresiation_date']) ? $productData['depresiation_date'] : 0;
				$depresiation_month =isset($productData['depresiation_month']) ? $productData['depresiation_month'] : 0;
				$depresiation_per_month =isset($productData['depresiation_per_month']) ? $productData['depresiation_per_month'] : 0;
				$asset_category_id=isset($productData['asset_category'])?$productData['asset_category']:0;

				$isManualImport = isset($productData['is_manual_import']) ? $productData['is_manual_import'] : 0;

				// Added few column which is needed for import but those not send by Sandeep Team
				for ( $i = 1; $i <= $qty; $i++){

		            $qty_data = array(
		                'product_id'       				=>  	$product_id,
		                'purchase_date'					=>		$purchase_date,
		                'invoice_number'				=>		$invoice_no,
		                'is_working'					=>		"Yes",
		                'business_unit'					=>		$businessunitname,

		                'warranty_end_date'				=>		$warentyEndDate,
		                'warranty_year'					=>		$WarrantyYear,
		                'warranty_month'				=>		$WarrantyMonts,

		                'created_by'					=>		$createdByID,
		                'is_manual_import'				=>		$isManualImport,
		  				'depresiation_date'				=>		$depresiationDate,
		                'depresiation_per_month'		=>		$depresiation_per_month,
		                'depresiation_month'			=>		$depresiation_month,
		                'asset_category'				=>		$asset_category_id,
		                'asset_status'					=>      1,	
		            );	

		        	$this->objAsset->saveQtyWiseProducts($qty_data);
		        }	
			}
			
	       	return 1;
       	}
       	catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function importAssetFromExcel(Request $request){
		try{
			
			$file_data   	=  Input::file('asset_data');
			$file_extension	 = $file_data->getClientOriginalExtension();
			$msg = "";

			if( $file_extension != 'xlsx'){

				$msg .= "Invalid file type" . PHP_EOL;
				$file = fopen($file_path, "w");
	            fwrite($file, $msg);
	            fclose($file);

	            return 'Invalid file type';

	        }else{

				if (Input::hasFile('asset_data')) {
				    $path                           = Input::file('asset_data')->getRealPath();
				    $data                           = $this->readExcel($path);
				    $file_data                      = Input::file('asset_data');
				    $result                         = json_decode($data['prod_data'], true);
				    $headers                        = json_decode($data['cat_data'], true);
				    $headers1                       = array('SrNo','Manufacture','Brand','Supplier Name','Product Category','Business Unit','Asset Name','Description','Qty','Value','Taxes and others','Total Asset value','Date of purchase Asset','Warranty(Year)','Depreciation Age(Year)','Asset Category','is_movableyn');
				    $recordDiff                     = array_diff($headers,$headers1);
				    	
				    	

					foreach($result as $key => $data){

						

						// Checking for the duplicate Product
						$productCount = $this->objAssetImplode->checkProductNameInDB($data['asset_name']);

						$manufactureid = $this->objAssetImplode->getTheIdWithManufactureName($data['manufacture']);
						$brandid = $this->objAssetImplode->getTheIdWithBrandName($data['brand']);
						$categoryid = $this->objAssetImplode->getTheIdWithCategoryName($data['product_category']);
						$Businessid = $this->objAssetImplode->getTheIdWithBusinessName($data['business_unit']);
						$AssetCategoryId=$this->objAssetImplode->getIdByCategoryName($data['asset_category']);

						$sku=$this->_productRepo->generateSKUcode();

						$msg .= " Sr No . " . $data['srno'] . " - " . $data['asset_name'] . " : ";

						$assetNotInsertedFlag = 0;
						// Product MSG
						if( $productCount>0 ){
							$msg .= " Asset by same name exist, can not insert ||";
							$assetNotInsertedFlag=1;
						}

						// Checking for Quantiry FLD
						if( (int)$data['qty'] <= 0 ){
							$msg .= " Quantiry is not valid ||";
							$assetNotInsertedFlag=1;
						}

						// Checking for Quantiry FLD
						if( (int)$data['value'] <= 0 ){
							$msg .= " Asset value is not valid ||";
							$assetNotInsertedFlag=1;
						}

						// Warrenty Year
						if( (int)$data['warrantyyear'] <= 0 ){
							$msg .= " Asset Warranty Year is not valid ||";
							$assetNotInsertedFlag=1;
						}

						// Despriciation Age
						if( (int)$data['depreciation_ageyear'] <= 0 ){
							$msg .= " Asset Depriciation Age is not valid ||";
							$assetNotInsertedFlag=1;
						}

						if( $manufactureid == "0" ){
							$msg .= " Manufacture not matched ||";
							$assetNotInsertedFlag=1;
						}

						if(trim($data['asset_name']) == ""){
							$msg .= " Asset Name Is Empty ||";
							$assetNotInsertedFlag=1;
						}


						if( $brandid == "0" ){
							$msg .= " Brand not matched ||";
							$assetNotInsertedFlag=1;
						}

						if( $categoryid == "0" ){
							$msg .= " Product Category not matched ||";
							$assetNotInsertedFlag=1;
						}

						if( $Businessid == "0" ){
							$msg .= " BusinessID not matched ||";
							$assetNotInsertedFlag=1;
						}
						if($AssetCategoryId == "0"){
							$msg.="Asset category not matched ||";
							$assetNotInsertedFlag=1;
						}

						if( $assetNotInsertedFlag==1 ){
							$msg .= " Asset not Inserted"  . PHP_EOL ;
						}else{

							// Assigning asset type for Moveable = 1 || non-moveable=0	
							$asset_type=0;
							$asset_type = strtolower($data['is_movableyn'])=='y' ? 1 : 0;

							// Save data into Product Table
				   			$import_asset_data = array(
							                'legal_entity_id'			=>2,
							                'brand_id'					=>$brandid,
							                'manufacturer_id'			=>$manufactureid,
							                'product_title'				=>$data['asset_name'],
							                'sku'						=>$sku,
							                'product_type_id'			=>130001,
							                'category_id'				=>$categoryid,
							                'business_unit_id'			=>$Businessid,
							                'mrp'						=>$data['value'],
							                'asset_type'				=>$asset_type,
							                'asset_category'			=>$AssetCategoryId,							   
							                'created_by' 				=> Session::get('userId')
							            );

				   			//save the data into products table
				   			$productID = $this->objAssetImplode->saveIntoProductsTable($import_asset_data);

				   			if( $productID > 1){
								$msg .= " Asset Created Succesfully." . PHP_EOL;
							}

				   			$purchasedate = $data['date_of_purchase_asset'];

				   			if(!isset($purchasedate['date'])){
				   				$purchasedate = date('Y-m-d');
				   			}else{
				   				$purchasedate = date('Y-m-d', strtotime($purchasedate['date']));
				   			}

				   			// Calculate Warenty year
				   			$warentyYear = date('Y-m-d', strtotime('+ '.(int)$data['warrantyyear'].' year', strtotime($purchasedate)));
				   			// Depriciation year
				   			$depreciationYear = date('Y-m-d', strtotime('+ '.(int)$data['depreciation_ageyear'].' year', strtotime($purchasedate)));

				   			// Preparing the Asset Array as per the common function built
							$productData = array();
				   			$productData[0]['product_id'] 						= $productID;
							$productData[0]['business_unit'] 					= $data['business_unit'];
							$productData[0]['invoice_date'] 					= date('Y-m-d', strtotime($purchasedate));
							$productData[0]['qty'] 								= isset($data['qty']) ? $data['qty'] : 0;
							$productData[0]['created_by'] 						= Session::get('userId');
							$productData[0]['invoice_date'] 					= isset($purchasedate) ? $purchasedate : date('Y-m-d');
							$productData[0]['invoice_no'] 						= "";
							$productData[0]['warranty_end_date'] 				= $warentyYear;
							$productData[0]['depresiation_date'] 				= $depreciationYear;
							$productData[0]['WarrantyYear'] 					= $data['warrantyyear'];
							$productData[0]['WarrantyMonts'] 					= 0;
							$productData[0]['depresiation_month'] 				= $data['depreciation_ageyear'];
							$productData[0]['is_manual_import'] 				= 1;
							$productData[0]['depresiation_per_month'] 			= (int)$data['total_asset_value'] / (int)$data['depreciation_ageyear'];
							$productData[0]['asset_status']						= 1;
							$productData[0]['asset_category']					= $AssetCategoryId;


							// call the common function
							$this->saveAssetDetails($productData);
						}
					}	
				}

				$timestamp = md5(microtime(true));
	            $txtFileName = 'Asset-Impost-Status-' . $timestamp . '.txt';
	            
	           

	            $file_path = 'download' . DIRECTORY_SEPARATOR . 'pricing_log' . DIRECTORY_SEPARATOR . $txtFileName;

	            $file = fopen($file_path, "w");
	            fwrite($file, $msg);
	            fclose($file);

	            $ImportUrl 		= $this->_productRepo->uploadToS3($file_path,'inventory',2);	           
				return "Assets Saved Succesfully Please check the file for Details!".'<a href='.$ImportUrl.' target="_blank"> View Details </a>';  
			} 
		}catch(\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}  
	}
}	