<?php

namespace App\Modules\Grn\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Log;
//use App\Lib\Queue;
use App\Modules\WarehouseConfig\Models\WarehouseConfigApi;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\MongoRepo;
use App\Central\Repositories\ProductRepo;
use App\Modules\Orders\Models\Inventory;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\Roles\Models\Role;
use App\Modules\Indent\Models\LegalEntity;

class Grn extends Model {



    /*
	 * getGrnMaxId() method is used to get Max Grn id
	 * @param Null
	 * @return id
	 */
    public function getGrnMaxId() {
        try {
            $maxid = DB::table('grn')->max('grn_id');
            return $maxid;
        } catch (Exception $e) {
            
        }
    }
    /*
	 * getSupplierId() method is used to get order name with value
	 * @param Null
	 * @return Array
	 */
    public function getSuppliers(){
        try{

            $legalentityId = Session::get('legal_entity_id');
            $purchaseOrder = new PurchaseOrder();
            $legal_entity_type_id = $purchaseOrder->getLegalEntityTypeId($legalentityId);
            $this->_roleModel = new Role();
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $suppliers = DB::table('legal_entities')->select('legal_entity_id','business_legal_name')
                   ->where('legal_entity_type_id',1002)
                   ->where('is_approved',1)
                   ->where('parent_id',$legalentityId)
                   ->get()->all();
            $supMergeArray = array();
            $dc_fc_mapping_data = $purchaseOrder->getDCFCData($legalentityId,array('legal_entity_id','business_legal_name'));
            if(count($dc_fc_mapping_data))
                $supMergeArray = array_merge($suppliers, $dc_fc_mapping_data);

            if($legal_entity_type_id == 1001){
                $legal_entity_type_id = [1014,1016];
            
                $fc_dc_legal_entities = DB::table('dc_fc_mapping')->select(DB::raw("GROUP_CONCAT(DISTINCT CONCAT(dc_le_id,',',fc_le_id) ) AS dc_le_id"))
                            ->whereIn('dc_fc_mapping.dc_le_wh_id', explode(',',$dc_acess_list))
                            ->orWhereIn('dc_fc_mapping.fc_le_wh_id', explode(',',$dc_acess_list))
                            ->first();
                $fc_dc_legal_entities = isset($fc_dc_legal_entities->dc_le_id) ? $fc_dc_legal_entities->dc_le_id : "";
                $dcfcList = array();
                if($fc_dc_legal_entities != ""){
                    $dcfcList = DB::table('legal_entities')->select('legal_entity_id','business_legal_name')
                            ->whereIn( 'legal_entities.legal_entity_id',explode(',',$fc_dc_legal_entities))
                            ->whereIn( 'legal_entities.legal_entity_type_id',$legal_entity_type_id)
                            ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name'])->all();
                    if(count($dcfcList))
                        $supMergeArray = array_merge($supMergeArray, $dcfcList);
                }
            }

            $suppliers = json_decode(json_encode($supMergeArray),true);
            return $suppliers;
        } catch (Exception $ex) {

        }
        
    }
    /*
     * getSuppliersByWarehouseId() method is used to get list of Suppliers by warehouse id
     * @param $le_wh_id
     * @return Array
     */
    public function getWarehouseBySupplierId($sup_id) {
        try {
            $fieldArr = array('lewh.lp_wh_name', 'lewh.le_wh_id');
            $query = DB::table('legalentity_warehouses as lewh')->select($fieldArr);
            $query->leftJoin('product_tot as lewhmap', 'lewhmap.le_wh_id', '=', 'lewh.le_wh_id');
            $query->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'lewhmap.supplier_id');
            $query->where('lewhmap.supplier_id', $sup_id);
            $query->where('lewh.dc_type', 118001);
            $query->where('lewh.status', 1);
            $query->groupBy('lewhmap.le_wh_id');
            $warehouses = $query->get()->all();
            return $warehouses;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /*
     * getSuppliersByWarehouseId() method is used to get list of Suppliers by warehouse id
     * @param $le_wh_id
     * @return Array
     */
    public function getProductInfoBySku($sku) {
        try {
            $fieldArr = array('products.product_id','products.product_title','products.upc','products.pack_size','products.mrp','tot.dlp','tot.base_price');
            $query = DB::table('products')->select($fieldArr);
            $query->leftJoin('product_tot as tot', 'tot.product_id', '=', 'products.product_id');
            $query->where('products.sku', $sku);
            $product = $query->first();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getProductPackInfo($product_id) {
        try {
            $fieldArr = array('products.product_id','products.product_title','products.seller_sku','products.upc','pack.pack_id','pack.level','pack.no_of_eaches','pack.pack_sku_code','lookup.master_lookup_name as packname');
            $query = DB::table('products')->select($fieldArr);
            $query->leftJoin('product_pack_config as pack', 'pack.product_id', '=', 'products.product_id');
            $query->leftJoin('master_lookup as lookup', 'pack.level', '=', 'lookup.value');
            $query->where('products.product_id', $product_id);
            $query->orderBy('lookup.sort_order', 'desc');
            $query->orderBy('pack.no_of_eaches', 'asc');
            $product = $query->get()->all();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

	public function getGrn($filter=array(), $count=0) {
		$fieldArr = array('grn.*', 'legal.business_legal_name', 'users.firstname',
			'users.lastname', 'currency.symbol_left as symbol');
		
		$query = DB::table('grn')->select($fieldArr);
		$query->join('legal_entities as legal', 'legal.legal_entity_id', '=', 'grn.legal_entity_id');
		$query->join('users', 'users.user_id', '=', 'grn.created_by');
		$query->join('currency', 'currency.currency_id', '=', 'grn.currency_id');
		$query->groupBy('grn.grn_id');

		if(isset($filter['grn_id']) && !empty($filter['grn_id'])) {
			$query->where('grn.grn_id', $filter['grn_id']);
		}

		if(isset($filter['fdate']) && !empty($filter['fdate'])) {
			$query->where('grn.created_at', '>=', $filter['fdate'].' 00:00:00');
		}
	
		if(isset($filter['tdate']) && !empty($filter['tdate'])) {
			$query->where('grn.created_at', '<=', $filter['tdate'].' 23:59:59');
		}
		#echo $query->toSql();
		if($count) {
			return $query->count();	
		}
		else {
			return $query->get()->all();	
		}
	}

	public function getGrnDetailById($grnId) {
		try{
			$fieldArr = array('grn.*', 'product.*', 
							'legal.business_legal_name', 
							'legal.address1',
							'legal.address2',
							'legal.city',
							'legal.pincode',
							'wh.lp_wh_name', 
							'wh.address1 as dc_address1', 
							'wh.address2 as dc_address2',
							'countries.name as country_name',
							'zone.name as state_name',
							'users.firstname',
							'users.lastname',
							'legalUser.mobile_no as legalMobile',
							'legalUser.email_id as legalEmail',
							'currency.symbol_left as symbol'
							);
			
			$query = DB::table('grn')->select($fieldArr);
			$query->join('grn_products as product', 'grn.grn_id', '=', 'product.grn_id');
			$query->join('legal_entities as legal', 'legal.legal_entity_id', '=', 'grn.legal_entity_id');
			$query->join('legalentity_warehouses as wh', 'wh.le_wh_id', '=', 'grn.le_wh_id');
			$query->join('users', 'users.user_id', '=', 'grn.created_by');
			$query->join('users as legalUser', 'legalUser.legal_entity_id', '=', 'grn.legal_entity_id');
			$query->join('currency', 'currency.currency_id', '=', 'grn.currency_id');
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
			$query->where('grn.grn_id', $grnId);
			#echo $query->toSql();
			return $query->get()->all();
		}
		catch(Exception $e) {

		}		
	}
    public function validateGRN($purchaseOrderNo, $productId, $receivedQty)    
    {
        try
        {
            $response = 0;
            if($purchaseOrderNo > 0 && $productId && $receivedQty > 0)
            {
                DB::enableQueryLog();
                $result = DB::table('po_products')
                        ->leftJoin('inward', 'inward.po_no', '=', 'po_products.po_id')
//                        ->leftJoin('inward_products', 'inward_products.product_id', '=', 'po_products.product_id')
                        ->leftJoin('inward_products', function($join)
                        {
                           $join->on('inward_products.product_id', '=', 'po_products.product_id');
                           $join->on('inward.inward_id', '=', 'inward_products.inward_id');
                        })
                        ->where(['po_products.po_id' => $purchaseOrderNo, 'po_products.product_id' => $productId])
                        ->select(DB::raw('((po_products.no_of_eaches * po_products.qty) - inward_products.received_qty) AS result'))
                        ->groupBy('inward_products.product_id')
                        ->first();
                if(!empty($result))
                {
                    $response = property_exists($result, 'result') ? $result->result : 0;
                }else{
                    $response = 1;
                }
            }
            return $response;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
            return 0;
        }
    }
        /*
     * saveIndent() method is used to Save Indent details
     * @param $indentArr
     * @return $indent_id
     */

    public function grnSave($grnArr) {
        try {
            $grn_id = DB::table('inward')->insertGetId($grnArr);
            return $grn_id;
        } catch (Exception $ex) {
            
        }
    }
    public function saveGrnProducts($grnProducts) {
        try {
            $grnProductId = DB::table('inward_products')->insert($grnProducts);
            return $grnProductId;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }

    public function grnUpdate($grnArr, $grnId) {
        try {
            DB::table('inward')
                    ->where('inward_id', $grnId)
                    ->update($grnArr);
            return true;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
            return false;
        }
    }
    public function updateGrnProducts($grnProducts, $grnId, $productId) {
        try {
            DB::table('inward_products')
                    ->where(['inward_id' => $grnId, 'product_id' => $productId])
                    ->update($grnProducts);
            return true;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
           return false;
        }
    }
    public function saveGrnProductDetails($grnProductsDetails) {
        try {
            $inward_prd_det_id = DB::table('inward_product_details')->insert($grnProductsDetails);
            return $inward_prd_det_id;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    public function saveStockInward($inwardId)
    {
        try
        {
            $stockInwardId = 0;
            if($inwardId > 0)
            {
                $productList = [];
                $inwardDeails = DB::table('inward')
                        ->leftJoin('inward_products', 'inward_products.inward_id', '=', 'inward.inward_id')
                        ->select('inward.le_wh_id', 'inward.inward_code', 'inward.po_no', 'inward_products.product_id', 'inward_products.received_qty',
                            'inward_products.free_qty', 'inward_products.damage_qty', 'inward_products.missing_qty',
                            'inward_products.excess_qty', 'inward_products.quarantine_stock',
                            'inward_products.orderd_qty'
                            )
                        ->where(['inward_products.inward_id' => $inwardId])
                        ->get()->all();
                if(!empty($inwardDeails))
                {
                    // Adding Stock Inward to Queue
                    // Initalize Queue and Array for Data                    
                    $data = array();
                    $ref_type = "Grn";
                    $i=0;

                    $productInfo = [];
                    $inwardCode = 0;
                    $leWhId = 0;
                    foreach($inwardDeails as $inwardProducts)
                    {
                        $productId = $inwardProducts->product_id;
                        Log::info(json_encode($inwardProducts));
                        $productList[] = $productId;
                        $checkStockInward = DB::table('stock_inward')
                                ->where(['reference_no' => $inwardId, 'product_id' => $productId])
                                ->select('stock_inward_id')
                                ->first();
                        $inwardCode = $inwardProducts->inward_code;
                        $leWhId = $inwardProducts->le_wh_id;
                        if(empty($checkStockInward))
                        {
                            $stockInward = array(
                                'le_wh_id' => $inwardProducts->le_wh_id,
                                'product_id' => $inwardProducts->product_id,
                                'good_qty' => ($inwardProducts->received_qty - 
                                        ($inwardProducts->damage_qty + $inwardProducts->missing_qty + $inwardProducts->quarantine_stock)),
                                'free_qty' => $inwardProducts->free_qty,
                                'dnd_qty' => $inwardProducts->missing_qty,
                                'dit_qty' => $inwardProducts->damage_qty,  //damage in transit
                                'quarantine_qty' => $inwardProducts->quarantine_stock,
                                'po_no' => $inwardProducts->po_no,
                                'reference_no' => $inwardId,
                                'inward_date' => date('Y-m-d H:i:s'),
                                'status' => '',
                                'created_by' => \Session::get('userId'),
                            );
                            $stockInwardId = DB::table('stock_inward')->insert($stockInward);
                            $productData['product_id'] = $inwardProducts->product_id;
                            $productData['soh'] = ($inwardProducts->received_qty - 
                                        ($inwardProducts->damage_qty + $inwardProducts->missing_qty + $inwardProducts->quarantine_stock));
                            $productData['free_qty'] = $inwardProducts->free_qty;
                            $productData['quarantine_qty'] = $inwardProducts->quarantine_stock;
                            $productData['dit_qty'] = $inwardProducts->damage_qty;  //damage in transit
                            $productData['dnd_qty'] = $inwardProducts->missing_qty;
                            
                            $productInfo[] = $productData;
                        }
                    }
                    if(!empty($productInfo))
                    {
                        DB::enableQueryLog();
                        $productNameList = [];
                        if(!empty($productList))
                        {
                            //$productNameList = DB::selectFromWriteConnection(DB::raw("CALL getMinEsuProductsList('".implode(',' ,$productList)."', ".$leWhId.")"));    
                            //Log::info("productNameList".json_encode($productNameList));
                        }
                        //Log::info('GRN pushnotification');
                        //Log::info(DB::getQueryLog());
                        $inventoryModel = new Inventory();
                        $response = $inventoryModel->inventoryStockInward($productInfo, $leWhId, $inwardCode, 1);
                        if($response)
                        {
                            //$this->stockInwardPushNotification($productList,$productNameList,$leWhId);
                        }else{
                            Log::info('Error from inventory model inventoryStockInward for inward '.$inwardCode);
                        }
                    }
                    return $stockInwardId;
                }
            }
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function saveStockInwardNew($inward_code,$warehouse,$stockInward,$productInfo,$stock_transfer=0,$stock_transfer_dc=0,$po_id=0)
    {
        try
        {

            $stockInwardId = DB::table('stock_inward')->insert($stockInward);
            if(!empty($productInfo))
            {
                $productNameList = [];
                if(!empty($productList))
                {
                    //$productNameList = DB::selectFromWriteConnection(DB::raw("CALL getMinEsuProductsList('".implode(',' ,$productList)."', ".$leWhId.")"));    
                    //Log::info("productNameList".json_encode($productNameList));
                }
                $inventoryModel = new Inventory();
                
                $response = $inventoryModel->inventoryStockInward($productInfo, $warehouse, $inward_code, 1);
                Log::info("stock_transfer".$stock_transfer);
                if($stock_transfer == 1){
                    $inventoryModel->inventoryStockOutward($productInfo, $stock_transfer_dc, 1, $po_id, 3);
                }
                if($response)
                {
                    //$this->stockInwardPushNotification($productList,$productNameList,$leWhId);
                }else{
                    Log::info('Error from inventory model inventoryStockInward for inward '.$inwardCode);
                }
            }
            return $stockInwardId;
            
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function stockInwardPushNotification($productList,$productNameList,$le_wh_id=0) {
        try {
            $productNames = '';
            $pushMessageNumbers = [];
            if (!empty($productList)) {
                if (!empty($productNameList)) {
                    $productNamesList = [];
                    foreach ($productNameList as $productName) {
                        $esu = property_exists($productName, 'esu') ? $productName->esu : 0;
                        $inv = property_exists($productName, 'inv') ? $productName->inv : 0;
                        if ($esu > $inv) {
                            $productNamesList[] = property_exists($productName, 'product_title') ? $productName->product_title : '';
                        }
                    }
                    if (!empty($productNamesList)) {
                        $productNames = implode(',', $productNamesList);
                    }
                }
            }
            if ($productNames != '') {
                $this->_LegalEntity = new LegalEntity();
                $whInfo = $this->_LegalEntity->getWarehouseById($le_wh_id);
                $le_wh_name = "";
                if(count($whInfo) > 0 && isset($whInfo->display_name)){
                    $le_wh_name = "at ".$whInfo->display_name;
                }
                $message = "New stock arrived ".$le_wh_name." for the products - " . $productNames;
                //$roleRepo = new RoleRepo();
                //$roleNames = ['Field Force Manager', 'Field Force Associate'];
                
                $notificationObj= new NotificationsModel();
                $userIdData= $notificationObj->getUsersByCode('GRN002');
                //$insertData = [];
                //foreach ($roleNames as $roleName) {
                    //$usersInfo = $roleRepo->getUsersByRole($roleName);
                    //if (!empty($usersInfo)) {
                        $_roleModel = new Role();
                        foreach ($userIdData as $userId) {
                            $userDetails = $this->userInfoById($userId);
                            $Json = json_decode($_roleModel->getFilterData(6,$userId), 1);
                            $filters = json_decode($Json['sbu'], 1);
                            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
                            $dc_acess_list = explode(',', $dc_acess_list);
                            if(count($userDetails)>0 && in_array($le_wh_id, $dc_acess_list)){
                                $pushMessageNumbers[] = $userDetails->mobile_no;
                                $tempData['reference_id'] = 0;
                                $tempData['requested_by'] = 0;
                                $tempData['request_type'] = 'push';
                                $tempData['number'] = $userDetails->mobile_no;
                                $tempData['message'] = $message;
                                $tempData['response'] = '';
                                date_default_timezone_set('Asia/Kolkata');
                                $tempData['created_on'] = date('Y-m-d H:i:s');
                                //$insertData[] = $tempData;
                            }
                        }
                    //}
                //}
                $RegId = DB::table('device_details')
                                ->join('users', 'device_details.user_id', '=', 'users.user_id')
                                ->select('registration_id', 'platform_id')
                                ->whereIn('users.mobile_no', $pushMessageNumbers)->get()->all();
                $tokenDetails = json_decode((json_encode($RegId)), true);
                if (!empty($tokenDetails)) {
                    $productRepo = new ProductRepo();
                    $productRepo->pushNotifications($message, $tokenDetails, 'NewStock', 'Ebutor', implode(',', $productList));
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function userInfoById($user_id) {
        try {
            $fieldArr = [
                    'user_id',
                    'email_id',
                    'mobile_no',
                    'firstname',
                    'lastname'
                ];
            $query = DB::table('users')->select($fieldArr);
                    $query->where('users.is_active', 1);
                    $query->where('users.user_id', $user_id);
            $result = $query->first();
            return $result;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    public function saveInputTax($inputTax) {
        try {
            $inputTaxId = DB::table('input_tax')->insert($inputTax);
            return $inputTaxId;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    public function updateInputTax($inputTax, $inwardId, $productId) {
        try {
            DB::table('input_tax')
                    ->where(['inward_id' => $inwardId, 'product_id' => $productId])
                    ->update($inputTax);
            return;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    public function getSkus($data)
    {
        try
        {
            $response = [];
            $term = $data['term'];
            $legal_entity_id = \Session::get('legal_entity_id');
            $supplier_id = $data['supplier_id'];
            $warehouse_id = $data['warehouse_id'];
                $products = DB::table('products')
                        ->where('tot.supplier_id',$supplier_id)
                        ->where('tot.le_wh_id',$warehouse_id)
                        ->where(function ($query) use($term) {
                            $query->orWhere('products.sku','like', '%'.$term.'%')
                                  ->orWhere('products.product_title','like', '%'.$term.'%')
                                  ->orWhere('products.upc','like', '%'.$term.'%');
                                  //->orWhere('content.product_name','like', '%'.$term.'%');
                        })
                        ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                        ->leftJoin('product_content as content','products.product_id','=','content.product_id')
                        ->leftJoin('product_tot as tot','products.product_id','=','tot.product_id')
                        ->select('products.product_id','products.product_title','products.upc','products.sku','products.pack_size','products.seller_sku','products.mrp','brands.brand_id','brands.brand_name')
                        ->get()->all();
                        
            $prodAry = array();
            if(count($products)>0){
                foreach($products as $product){
                    $brand = $product->brand_name;
                    $product_name = $product->product_title.' ( '.$brand.' )';
                    $product_id = $product->product_id;
                    $product_title = $product->product_title;
                    $upc = $product->upc;
                    $mrp = ($product->mrp!='')?$product->mrp:0; 
                    $product_info = $this->getProductPackInfo($product_id);
                    $packUOM = '<option value="">Select Pack UOM</option>';
                    foreach($product_info as $product){
                        if($product->level != 16001)
                        {
                            $packUOM .='<option value="'.$product->level.'" data-noofeach="'.$product->no_of_eaches.'">'.$product->packname.' ('.$product->no_of_eaches.' Eaches)</option>';
                        }else{
                            $packUOM .='<option value="'.$product->level.'" data-noofeach="'.$product->no_of_eaches.'">'.$product->packname.'</option>';
                        }
                        
                    }            
                    $prod_arr = array("label" => $product_name, "product_id" => $product_id, "product_title" => $product_title, "brand" => $brand, "upc" => $upc,'mrp'=>'Rs. '.$mrp,'packoum'=>$packUOM);
                    array_push($prodAry, $prod_arr);
                }
            }else{
                $prod_arr = array("label" => 'No Result Found','value'=>'');
                array_push($prodAry, $prod_arr);
            }
            echo json_encode($prodAry);die;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }
    public function getProductInfoById($product_id,$le_wh_id,$sup_id) {
        try {
            $fieldArr = array('products.product_id','products.product_title as product_name', DB::raw('0 as qty'), DB::raw('tot.dlp as unit_price'), DB::raw('1 as is_tax_included'), 'products.sku', 'products.seller_sku','products.upc','products.pack_size','products.mrp','products.kvi','tot.dlp','tot.base_price','inventory.mbq','currency.symbol_right as symbol');
            $query = DB::table('products')->select($fieldArr);
            $query->leftJoin('product_tot as tot', function($join)
                    {
                        $join->on('products.product_id','=','tot.product_id');
                        //$join->on('tot.supplier_id','=','po.legal_entity_id');
                        //$join->on('tot.le_wh_id','=','po.le_wh_id');
                    });
            $query->leftJoin('inventory', function($join)
             {
                $join->on('products.product_id','=','inventory.product_id');
                $join->on('tot.le_wh_id','=','inventory.le_wh_id');
             });
            
            $query->leftJoin('currency','tot.currency_id','=','currency.currency_id');
            $query->where('products.product_id', $product_id);
            $query->where('tot.le_wh_id', $le_wh_id);
            $query->where('tot.supplier_id', $sup_id);
            $product = $query->first();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function poList() {
        try {

            $legalentityId = Session::get('legal_entity_id');
            $purchaseOrder = new PurchaseOrder();
            $legal_entity_type_id = $purchaseOrder->getLegalEntityTypeId($legalentityId);
            $this->_roleModel = new Role();
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $suppliers = DB::table('legal_entities')->select('legal_entity_id')
                   ->where('legal_entity_type_id',1002)
                    ->where(['is_approved' => 1])
                   ->where('parent_id',$legalentityId)
                   ->get()->all();
            $dc_fc_mapping_data = $purchaseOrder->getDCFCData($legalentityId,array('legal_entity_id'));
            $supMergeArray = array();
            if(count($dc_fc_mapping_data)){
                $dc_fc_mapping_data = array_column(json_decode(json_encode($dc_fc_mapping_data),true), "legal_entity_id");
                $supMergeArray = array_merge($suppliers, $dc_fc_mapping_data);
                if(count($supMergeArray) == 0){
                    $supMergeArray = $dc_fc_mapping_data;
                }
            }
            else
                $supMergeArray = $suppliers;

            $fieldArr = array('po.po_id','po.po_code');
            $query = DB::table('po')->select($fieldArr);
            if($legal_entity_type_id == 1001){
                /*$legal_entity_type_id = [1014,1016];
            
                $fc_dc_legal_entities = DB::table('dc_fc_mapping')->select(DB::raw("GROUP_CONCAT(DISTINCT CONCAT(dc_le_id,',',fc_le_id) ) AS dc_le_id"))
                            ->whereIn('dc_fc_mapping.dc_le_wh_id', explode(',',$dc_acess_list))
                            ->orWhereIn('dc_fc_mapping.fc_le_wh_id', explode(',',$dc_acess_list))
                            ->first();
                $fc_dc_legal_entities = isset($fc_dc_legal_entities->dc_le_id) ? $fc_dc_legal_entities->dc_le_id : "";
                $dcfcList = array();
                if($fc_dc_legal_entities != ""){
                    $dcfcList = DB::table('legal_entities')->select('legal_entity_id')
                            ->whereIn( 'legal_entities.legal_entity_id',explode(',',$fc_dc_legal_entities))
                            ->whereIn( 'legal_entities.legal_entity_type_id',$legal_entity_type_id)
                            ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name']);
                    if(count($dcfcList))
                    $supMergeArray = array_merge($supMergeArray, $dcfcList);
                */
            }else{
                $query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
            }
            // getting global supplier id for stockists
            $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get()->all();
            $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
            array_push($supMergeArray, $globalSupperLierId);
            // added legal entity id 2 beacause to get PO from DC to Ebutor
            array_push($supMergeArray, 2);
            $suppliers=  json_decode(json_encode($supMergeArray),true);
            
            $query->where(['po.is_closed' => 0]);
            //$query->whereIn('po.approval_status', [57031, 57032, 57033, 1]);
            $query->whereIn('po.approval_status', [57107,57119,57120, 1]);
            
            $query->whereIn('po.po_status', array(87001, 87005));
            $query->orderBy('po.po_id', 'desc');
            $poList = $query->get();
            return $poList;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getGrnQtyByPOId($poId) {
        try {
            $fieldArr = array(DB::raw('SUM(received_qty) AS tot_received'));
            $query = DB::table('inward')->select($fieldArr);
            $query->join('inward_products as inwrdprd','inwrdprd.inward_id','=','inward.inward_id');
            $query->where('inward.po_no', $poId);
            $grnQty = $query->first();
            return $grnQty;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getGrnQtyByPOProductId($poId,$productId) {
        try {
            $fieldArr = array('orderd_qty',DB::raw('(poprd.qty*poprd.no_of_eaches) AS po_qty'),
                DB::raw('SUM(inwrdprd.received_qty) AS tot_received'),
                DB::raw('SUM(inwrdprd.free_qty) AS tot_free_received'));
            $query = DB::table('inward')->select($fieldArr);
            $query->join('inward_products as inwrdprd','inwrdprd.inward_id','=','inward.inward_id');
            $query->leftJoin('po_products as poprd', function($join)
             {
                $join->on('poprd.po_id','=','inward.po_no');
                $join->on('poprd.product_id','=','inwrdprd.product_id');
             });
            $query->where('inward.po_no', $poId);
            $query->where('inwrdprd.product_id', $productId);
            $grnQty = $query->first();
            return $grnQty;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPOQtyByProductId($poId,$productId) {
        try {
            $fieldArr = array('po_id',DB::raw('(poprd.qty*poprd.no_of_eaches) AS po_qty'));
            $query = DB::table('po_products as poprd')->select($fieldArr);
            $query->where('poprd.po_id', $poId);
            $query->where('poprd.product_id', $productId);
            $query->useWritePdo();
            $grnQty = $query->first();
            return $grnQty;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getGRNQtyByProductId($poId,$productId) {
        try {
           $fieldArr = array('orderd_qty',DB::raw('SUM(inwrdprd.received_qty) AS tot_received'));
            $query = DB::table('inward')->select($fieldArr);
            $query->join('inward_products as inwrdprd','inwrdprd.inward_id','=','inward.inward_id');
            $query->where('inward.po_no', $poId);
            $query->where('inwrdprd.product_id', $productId);
            $query->useWritePdo();
            $grnQty = $query->first();
            return $grnQty;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPOQtyById($poId) {
        try {
            $fieldArr = array(DB::raw('SUM(poprd.qty*no_of_eaches) AS totpo_qty'));
            $query = DB::table('po')->select($fieldArr);
            $query->join('po_products as poprd','poprd.po_id','=','po.po_id');
            $query->where('po.po_id', $poId);
            $poQty = $query->first();
            return $poQty;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getProductTaxClass($product_id,$wh_state_code=4033,$seller_state_code=4033) {
        try {
            $url=env('APP_TAXAPI');
            $data['product_id'] = (int)$product_id;
            $data['buyer_state_id'] = (int)$wh_state_code;
            $data['seller_state_id'] = (int)$seller_state_code;
            $taxData = $this->sendRequest($url,$data);
            return $taxData;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function sendRequest($url,$data) {
        try {
            //Log::info(json_encode($data).'URL='.$url);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                //CURLOPT_PORT => "3100",
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                //CURLOPT_MAXREDIRS => 10,
                //CURLOPT_TIMEOUT => 30,
                //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            //Log::info('TAX Cass Response:'.$response);
            if ($err) {
            
            } else {
                $response = json_decode($response, true);
                if ($response) {
                    if ($response['Status'] != 200) {
                        return 'No data from API';
                    } else {
                        return $response['ResponseBody'];
                    }
                } else {

                }
                return $response;
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getProductShelfLife($product_id) {
        try {
            $fields = array('products.shelf_life', 'products.shelf_life_uom','lookup.master_lookup_name');
            $query = DB::table('products')->select($fields);
            $query->leftJoin('master_lookup as lookup','lookup.value','=','products.shelf_life_uom');
            $query->where('products.product_id',$product_id);
            $shelflife = $query->first();
            return $shelflife;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getProductPackStatus() {
        try {
            $fields = array('lookup.value','lookup.master_lookup_name');
            $query = DB::table('master_lookup as lookup')->select($fields);
            $query->where('lookup.mas_cat_id',91);
            $packStatus = $query->get()->all();
            return $packStatus;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getProductPackUOMInfo($productId,$uom) {
        try {
            $fields = array('lookup.value','lookup.master_lookup_name as uomName','pack.no_of_eaches');
            $query = DB::table('product_pack_config as pack');
            $query->leftJoin('master_lookup as lookup','pack.level','=','lookup.value');
            $query->select($fields);
            $query->where('pack.product_id',$productId);
            $query->where('pack.level',$uom);
            $packStatus = $query->first();
            return $packStatus;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPOSupplierProductList($poId){
        $legal_entity_id = \Session::get('legal_entity_id');
        if($poId > 0)
            {            
                $legal_entity_type_id = [1002,1014,1016];
                $supplierList = DB::table('legal_entities')
                        ->join('po', 'po.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['po.po_id' => $poId,'legal_entities.is_approved' => 1])
                        ->whereIn( 'legal_entities.legal_entity_type_id',$legal_entity_type_id)
                        ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name'])->all();
                
                $warehouseList = DB::table('legalentity_warehouses')
                        ->join('po', 'po.le_wh_id', '=', 'legalentity_warehouses.le_wh_id')
                        ->where(['po.po_id' => $poId])
                        ->get(['legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.le_wh_id'])->all();
                DB::enableQueryLog();
                $products = DB::table('po_products as poprod')
                        ->where(['poprod.po_id' => $poId])
                        ->leftJoin('products','products.product_id','=','poprod.product_id')
                        ->leftJoin('po','po.po_id','=','poprod.po_id')
                        ->leftJoin('inward','inward.po_no','=','po.po_id')
                        ->leftJoin('inward_products', function($join)
                        {
                            $join->on('inward_products.inward_id','=','inward.inward_id');
                            $join->on('poprod.product_id','=','inward_products.product_id');
                        })
                        ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                        ->leftJoin('product_tot as tot', function($join)
                        {
                            $join->on('products.product_id','=','tot.product_id');
                            $join->on('tot.supplier_id','=','po.legal_entity_id');
                            $join->on('tot.le_wh_id','=','po.le_wh_id');
                        })                        
                        ->leftJoin('inventory', function($join)
                         {
                            $join->on('products.product_id','=','inventory.product_id');
                            $join->on('po.le_wh_id','=','inventory.le_wh_id');
                         })
                        ->leftJoin('currency','tot.currency_id','=','currency.currency_id')
                        ->select('poprod.product_id', 'po.approval_status', 'products.product_title as product_name', 'poprod.qty', 'poprod.is_tax_included', 'poprod.no_of_eaches', DB::raw('sum(inward_products.received_qty) as received_qty'),
                                'poprod.tax_per', 'poprod.tax_name', 'poprod.uom', 'products.sku', 'products.seller_sku', 'products.mrp', 'products.kvi',
                                'products.upc', 'poprod.unit_price','brands.brand_id','brands.brand_name','inventory.mbq','inventory.soh',DB::raw('(poprod.no_of_eaches * poprod.qty) AS actual_po_quantity'),
                                'inventory.atp','inventory.order_qty','products.pack_size','tot.dlp','tot.base_price',
                                'currency.symbol_right as symbol',DB::raw('(
                                                        CASE
                                                          WHEN 
                                                            `poprod`.`parent_id`=0                                                          
                                                          THEN `poprod`.`product_id` 
                                                          ELSE `poprod`.`parent_id` 
                                                        END
                                                            ) AS `product_parent_id`'),
                                'poprod.apply_discount', 'poprod.discount_type', 'poprod.discount',                                
                                DB::raw('(
                                        CASE
                                          WHEN inward_products.received_qty IS NOT NULL                                     
                                          THEN (poprod.no_of_eaches * poprod.qty) - inward_products.received_qty
                                          ELSE (poprod.no_of_eaches * poprod.qty)
                                        END
                                            ) AS po_quantity')
                                )
                                 ->orderBy('product_parent_id', 'asc')
                                 ->groupBy('poprod.product_id')
                        ->having('po_quantity', '>', 0)         
                        ->get()->all();
                         //\Log::info(DB::getQueryLog());
            }else{
                $supplierList = DB::table('legal_entities')
                        ->where(['legal_entities.legal_entity_type_id' => 1002, 'legal_entities.is_approved' => 1, 'parent_id'=>$legal_entity_id])
                        ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name'])->all();
                
                $warehouseList = DB::table('legalentity_warehouses')
                        ->where(['legalentity_warehouses.legal_entity_id' => $legal_entity_id])
                        ->get(['legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.le_wh_id'])->all();                
                $products = array();
                
            }
            foreach($products as $key=>$product){
                
                $qtyCheck=$this->getGrnQtyByPOProductId($poId,$product->product_id);
                if(($qtyCheck->tot_received>=$qtyCheck->po_qty) && ($qtyCheck->tot_received!='')){
                    unset($products[$key]);
                }else{
                    if(isset($qtyCheck->tot_received) && $qtyCheck->tot_received!='' && $qtyCheck->orderd_qty!='' && $qtyCheck->orderd_qty>$qtyCheck->tot_received){
                        $product->qty=($qtyCheck->orderd_qty-$qtyCheck->tot_received);
                    }
                }
            }
            $data=array('supplierList'=>$supplierList,'warehouseList'=>$warehouseList,'products'=>$products);
            return $data;
    }
    
    public function getPODiscountDetails($poId)
    {
        try
        {
            $poData = [];
            if($poId > 0)
            {
                $poDetails = DB::table('po')
                        ->where(['po_id' => $poId, 'apply_discount_on_bill' => 1])
                        ->select('apply_discount_on_bill', 'discount_type', 'discount','discount_before_tax')
                        ->first();
                if(!empty($poDetails))
                {
                    $poData = json_decode(json_encode($poDetails), true);
                }
            }
            return $poData;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function allowDocuments($supplierId)
    {
        try
        {
            $allowDocuments = 1;
            $ignoreList = ['CnC', 'SWD'];
            if($supplierId > 0)
            {
                $allowDocumentId = DB::table('master_lookup as lookup')
                        ->leftJoin('suppliers', 'suppliers.supplier_type', '=', 'lookup.value')
                        ->where('suppliers.legal_entity_id', $supplierId)
                        ->select('lookup.master_lookup_name')
                        ->first();
                if(!empty($allowDocumentId))
                {
                    $supplierType = property_exists($allowDocumentId, 'master_lookup_name') ? $allowDocumentId->master_lookup_name : '';
                    if(in_array($supplierType, $ignoreList))
                    {
                        $allowDocuments = 0;
                    }
                }
                return $allowDocuments;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getInvalidGRNDocs() {
        try{
            $fields = array('inward_docs.inward_doc_id','inward_docs.doc_url');
            $query = DB::table('inward_docs')->select($fields);
            $query->where('inward_docs.inward_id',0)
                    ->orderBy('inward_doc_id','desc');
            return $query->get()->all();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function deleteInvalidGRNDocs() {
        $docArr = $this->getInvalidGRNDocs();
        if(is_array($docArr) && count($docArr) > 0) {
            foreach ($docArr as $doc) {
                if(isset($doc->inward_doc_id) && $doc->inward_doc_id!=''){
                    $filename = public_path().$doc->doc_url;
                    if(file_exists($filename)){
                        unlink($filename);
                    }
                    $delete = DB::table('inward_docs')->where('inward_doc_id', $doc->inward_doc_id)->delete();
                }
            }
        }
    }

    public function getGrnDetails($grnId) {
        try
        {
            $result = [];
            if($grnId > 0)
            {
                $result = DB::table('inward')
                        ->leftJoin('inward_products', 'inward_products.inward_id', '=' , 'inward.inward_id')
                        ->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=' , 'inward.legal_entity_id')
                        ->leftJoin('po', 'po.po_id', '=', 'inward.po_no')
                        ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'inward.le_wh_id')
                        ->where(['inward.inward_id' => $grnId])
                        ->select('legal_entities.business_legal_name', 'po.po_code',
                                'inward.*', 'legalentity_warehouses.lp_wh_name', DB::raw('SUM(inward_products.tax_amount) as total_tax_amount')
                                , DB::raw('SUM(inward_products.received_qty) as total_received_qty'))
                        ->first();
            }
            return $result;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getGrnProductDetails($grnId) {
        try
        {
            $result = [];
            if($grnId > 0)
            {
                /* $result = DB::table('inward_products')
                        ->leftJoin('inward', 'inward.inward_id', '=', 'inward_products.inward_id')
                        ->leftJoin('po', 'po.po_id', '=', 'inward.po_no')
                        ->leftJoin('po_products',function($join)
                            {
                               $join->on('po_products.po_id', '=', 'inward.po_no');
                               $join->on('po_products.product_id', '=', 'inward_products.product_id');
                            })
                        ->leftJoin('products', 'products.product_id', '=', 'inward_products.product_id')
                        ->leftJoin('master_lookup', 'master_lookup.value', '=', 'po_products.uom')
                        ->where('inward.inward_id', $grnId)
                        ->select('products.mrp', 'products.kvi', 'products.sku', 'products.product_title', 'po_products.tax_per', 'po_products.tax_name',
                                'po_products.qty', 'po_products.uom', 'master_lookup.master_lookup_name as uomName', 
                                'po_products.no_of_eaches', DB::raw('(po_products.no_of_eaches * po_products.qty) AS actual_po_quantity'),
                                'po_products.free_qty',
                                'po_products.free_uom',
                                'po_products.free_eaches',
                                'po_products.hsn_code as hsn',
                                'po_products.tax_data as po_tax_data',
                                'inward_products.*')
                        ->groupBy('inward_products.product_id')
                        ->get();
                */
                $result = DB::selectFromWriteConnection(DB::raw("select `products`.`mrp`, `products`.`kvi`, `products`.`sku`, `products`.`product_title`, `po_products`.`tax_per`, `po_products`.`tax_name`, `po_products`.`qty`, `po_products`.`uom`, `master_lookup`.`master_lookup_name` as `uomName`, `po_products`.`no_of_eaches`, (po_products.no_of_eaches * po_products.qty) AS actual_po_quantity, `po_products`.`free_qty`, `po_products`.`free_uom`, `po_products`.`free_eaches`, `po_products`.`hsn_code` as `hsn`, `po_products`.`tax_data` as `po_tax_data`,po_products.`apply_discount`,
                    po_products.`discount_type`,po_products.`discount`, `inward_products`.* from `inward_products` left join `inward` on `inward`.`inward_id` = `inward_products`.`inward_id` left join `po` on `po`.`po_id` = `inward`.`po_no` left join `po_products` on `po_products`.`po_id` = `inward`.`po_no` and `po_products`.`product_id` = `inward_products`.`product_id` left join `products` on `products`.`product_id` = `inward_products`.`product_id` left join `master_lookup` on `master_lookup`.`value` = `po_products`.`uom` where `inward`.`inward_id` = $grnId group by `inward_products`.`product_id`"));
            }
            return $result;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getGrnDocDetails($grnId) {
        try
        {
            $result = [];
            if($grnId > 0)
            {
                $result = DB::table('inward_docs')
                        ->leftJoin('master_lookup', 'master_lookup.value', '=', 'inward_docs.doc_ref_type')
                        ->where('inward_id', $grnId)
                        ->select('doc_ref_no', DB::raw('master_lookup.master_lookup_name AS doc_ref_type'),
                                'doc_url', DB::raw('GetUserName(inward_docs.created_by, 2) as created_by'), 
                                'inward_doc_id')
                        ->get()->all();
            }
            return $result;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function checkGRNCreated($poId, $grnProductsData = []) {
        try {
            $check = true;
            if (isset($grnProductsData) && is_array($grnProductsData)) {
                foreach ($grnProductsData as $product) {
                    $productId = $product['product_id'];
                    $grn_received = $product['received_qty'];
                    $podata = $this->getPOQtyByProductId($poId,$productId);
                    $grndata = $this->getGRNQtyByProductId($poId,$productId);
                    $po_qty = isset($podata->po_qty)?$podata->po_qty:0;
                    $tot_received = isset($grndata->tot_received)?$grndata->tot_received:0;
                    $remaining_qty = ($po_qty - $tot_received);
                    if ($grn_received > $remaining_qty) {
                       $check = false;
                    }
                }
            } else {
                $check = false;
            }
            return $check;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function updateElpData($inwardId,$userId)
    {
        try
        {
            if($inwardId > 0)
            {
                $elpDetails = DB::table('inward_products')
                        ->leftJoin('inward', 'inward.inward_id', '=', 'inward_products.inward_id')
                        ->leftJoin('po', 'po.po_id', '=', 'inward.po_no')
                        ->where('inward.inward_id', $inwardId)
                        ->select('po.po_id', 'inward_products.product_id', 'po.le_wh_id', 
                                DB::raw('po.legal_entity_id as supplier_id'), 
                                DB::raw('inward_products.cur_elp AS elp'),
                                DB::raw('NOW() AS effective_date'))
                        ->having('elp', '>', 0)
                        ->get()->all();
                $purchaseOrder = new PurchaseOrder();
                if(!empty($elpDetails))
                {
                    $childPOexist = $purchaseOrder->checkChildPoExist($elpDetails[0]->po_id);
                    $wh_data = $purchaseOrder->getLEWHById($elpDetails[0]->le_wh_id);
                    $wh_legal_entity_id = $wh_data->legal_entity_id;
                    $wh_state_id = $wh_data->state_id;
                    $checkDCFC = $purchaseOrder->getLegalEntityTypeId($wh_legal_entity_id);

                    $dc_le_wh_id = 0;
                    if($checkDCFC == 1014){
                        //FC
                        $dc_le_wh_id = $purchaseOrder->getDCFCData($wh_legal_entity_id,['dc_le_wh_id']);
                        $dc_le_wh_id = isset($dc_le_wh_id[0]->dc_le_wh_id) ? $dc_le_wh_id[0]->dc_le_wh_id : 0;

                    }else if($checkDCFC == 1016){
                        // DC
                        $supplier_id = $elpDetails[0]->supplier_id;
                        $check_supplier = DB::table("legalentity_warehouses")
                        ->select('le_wh_id')
                        ->where('legal_entity_id',$supplier_id)
                        ->first();
                        if(count($check_supplier) > 0){
                            $dc_le_wh_id = $check_supplier->le_wh_id;
                        }else{
                            // $dc_le_wh_id = DB::table("legalentity_warehouses")
                            //     ->select('le_wh_id')
                            //     ->where('legal_entity_id',2)
                            //     ->where('state',$wh_state_id)
                            //     ->where('dc_type',118001)
                            //     ->where('is_apob',1)
                            //     ->first();
                            $dc_le_wh_id = $purchaseOrder->getApobData($wh_legal_entity_id);
                            $dc_le_wh_id = isset($dc_le_wh_id->le_wh_id) ? $dc_le_wh_id->le_wh_id : 0;
                        }
                    }
                    foreach($elpDetails as $elpData)
                    {
                        $elpData = (array)$elpData;
//                        $elpData['effective_date'] = date('Y-d-m');
                        $elpData['created_by'] = $userId;
                        $productId = isset($elpData['product_id']) ? $elpData['product_id'] : 0;
                        //$inwardId = isset($elpData['inward_id']) ? $elpData['inward_id'] : 0;
                        if($productId > 0)
                        {
        //                     $discountTotalValue = 0;
        //                     $discountOnTotal = DB::table('inward')
        //                         ->where(['inward.inward_id' => $inwardId, 'inward.discount_on_bill_options' => 0])
								// ->where('inward.discount_on_total', '>', 0)
        //                         ->select(DB::raw('sum(inward.discount_on_total) as discount'))
        //                         ->first();	
        //                     if(!empty($discountOnTotal))
        //                     {
        //                         $discountTotalValue = property_exists($discountOnTotal, 'discount') ? $discountOnTotal->discount : 0;
        //                     }
        //                     $totalInwardValue = 0;
        //                     $totalInwardDetail = DB::table('inward_products')
        //                         ->where(['inward_products.inward_id' => $inwardId])
        //                         ->select(DB::raw('SUM(inward_products.row_total) as row_totals'))
        //                         ->first();
        //                     if(!empty($totalInwardDetail))
        //                     {
        //                         $totalInwardValue = property_exists($totalInwardDetail, 'row_totals') ? $totalInwardDetail->row_totals : 0;
        //                     }
        //                     $elp = $this->calculateElp($inwardId, $productId, $discountTotalValue, $totalInwardValue);
                            $elp = DB::table('po')->leftJoin('po_products as pp','po.po_id','=','pp.po_id')
                                ->select('pp.cur_elp')->where(
                                        [
                                            'po.po_id'=>$elpDetails[0]->po_id,
                                            'pp.product_id'=>$productId
                                        ])->first();
                            $elpData['elp'] = $elp->cur_elp;
                            if($childPOexist > 0)
                            {   
                                $elp = DB::table('po')->leftJoin('po_products as pp','po.po_id','=','pp.po_id')
                                ->select('pp.cur_elp')->where(
                                            [
                                                'po.parent_id'=>$elpDetails[0]->po_id,
                                                'pp.product_id'=>$productId
                                            ])->first();
                                $elpData['elp'] = $elp->cur_elp;  
                            }

                            // actual elp 
                            $elpData['actual_elp'] = $elpData['elp'];
                            if($dc_le_wh_id != 0){
                                $actual_elp = DB::table('purchase_price_history')->select('actual_elp')->where('le_wh_id',$dc_le_wh_id)->where('product_id',$productId)->orderBy('created_at','desc')->first();
                                if(isset($actual_elp->actual_elp) && $actual_elp->actual_elp !="")
                                    $elpData['actual_elp'] = $actual_elp->actual_elp;
                            }
                            //Log::info($elpData);
                            $elpDataArr[] = $elpData;
                            //Log::info(DB::getQueryLog());
                        }
                    }
                    DB::table('purchase_price_history')->insert($elpDataArr);

                }
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function initiatePutAway($inwardId) {
        try {
            if ($inwardId > 0) {
                $failed = 1;
                $status = 'HOLD';
                $statusId = 12803;
                $statusData = DB::table('master_lookup')
                            ->where('mas_cat_id', 128)
                            ->where(DB::raw('LOWER(master_lookup_name)'),strtolower($status))
                            ->first(['value']);
                if(!empty($statusData))
                {
                    $statusId = $statusData->value;
                }
                $putAwayIncId = $this->insertPutAway($statusId, 'GRN', $inwardId);
                //Log::info('putAwayIncId');
                //Log::info($putAwayIncId);
                if($putAwayIncId > 0)
                {
                    $whConfigObj = new WarehouseConfigApi();
                    $response = $whConfigObj->putawayBinAllocation($putAwayIncId);
                    //Log::info('binAllocation Response ');
                    //Log::info($response);
                    $allocationData = json_decode($response, true);
                    $status = isset($allocationData['status']) ? $allocationData['status'] : 0;
                    if($status == 'failed')
                    {
                        $this->sendPutAwayFailedMail($putAwayIncId, $allocationData);
                    }
                }else{
                    Log::info('unable to get putaway id');
                }
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function insertPutAway($putAwayStatusId, $putAwayType, $putAwayId)
    {
        try
        {
            $putAwayIncId = 0;
            if($putAwayId > 0)
            {
                $insertData['putaway_source'] = $putAwayType;
                $insertData['source_id'] = $putAwayId;
                $insertData['putaway_status'] = $putAwayStatusId;
                //Log::info('Putaway table entry data');
                //Log::info($insertData);
                $putAwayIncId = DB::table('putaway_list')->insertGetId($insertData);
            }
            return $putAwayIncId;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function sendPutAwayFailedMail($putAwayId, $allocationData)
    {
        try
        {
            if($putAwayId > 0)
            {
                $grnIdData = DB::table('putaway_list')
                        ->where(['putaway_id' => $putAwayId])
                        ->select('source_id', 'putaway_source')
                        ->first();
                $sourceId = 0;
                if(!empty($grnIdData))
                {
                    $sourceId = property_exists($grnIdData, 'source_id') ? $grnIdData->source_id : 0;
                }
                if($sourceId > 0)
                {
                    $inwardDetails = DB::table('inward')
                            ->where(['inward_id' => $sourceId])
                            ->pluck('inward_code');
                    $inwardCode = 0;
                    $failProducts = '';
                    if(!empty($inwardDetails) && isset($inwardDetails[0]))
                    {
                        $inwardCode = $inwardDetails[0];
                    }
                    if(!empty($allocationData))
                    {
                        $productInfo = isset($allocationData['data']) ? $allocationData['data'] : [];
                        if(!empty($productInfo))
                        {
                            foreach($productInfo as $productData)
                            {
                                $failProducts = $failProducts . ((isset($productData['product_title'])) ? $productData['product_title'] : '')."<br />";
                            }
                        }
                    }
                    if($failProducts != '')
                    {
                    $fail_message = '';
                    $comment = '<br/>Please check bin reservation or capacity for below products<br/><br/>';
                    $fail_message .= '<br/><strong>GRN - '.$inwardCode.'</strong><br/><br/>';
                    $fail_message .= $failProducts;
                    $comment = $comment . $fail_message;
                    $body = array('template' => 'emails.po', 'attachment' => '', 'name' => 'Hello All', 'comment' => $comment);
                    //$roleRepo = new RoleRepo();
                    //$userEmailArr = $roleRepo->getUsersByRole(['DC Manager']);
                    $notificationObj= new NotificationsModel();
                    $userIdData= $notificationObj->getUsersByCode('GRN003');
                    $userIdData=json_decode(json_encode($userIdData));
                    $purchaseOrder = new PurchaseOrder();
                    $userEmailArr = $purchaseOrder->getUserEmailByIds($userIdData);
                    $toEmails = array();
                    if (is_array($userEmailArr) && count($userEmailArr) > 0) {
                        foreach ($userEmailArr as $userData) {
                            if(isset($userData['email_id']))
                                $toEmails[] = $userData['email_id'];
                        }
                    }
                    $instance = env('MAIL_ENV');
                    $subject = $instance . 'Bin allocation reminder';
                    //Log::info($body);
                    \Utility::sendEmail($toEmails, $subject, $body);
                    }else{
                        Log::info('Product info empty for inward '.$inwardCode);
                    }
                }
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getPoApprovalStatusByPoId($poId)
    {
        try
        {
            $approvalStatus = 0;
            if($poId > 0)
            {
                $approvalStatusData = DB::table('po')
                        ->where('po_id', $poId)
                        ->pluck('approval_status');
                if(!empty($approvalStatusData) && isset($approvalStatusData[0]))
                {
                    $approvalStatus = $approvalStatusData[0];
                }
            }
            return $approvalStatus;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function createSubPo($poId, $createdBy)
    {
        try
        {
//            Log::info(__METHOD__);
            if($poId > 0)
            {
                $poDetails = DB::table('po')
                        ->leftJoin('inward', 'inward.po_no', '=', 'po.po_id')                        
                        ->where('po.po_id', $poId)
                        ->select(DB::raw('po.*'), 
                                DB::raw('IF((po.`apply_discount_on_bill` = 1 AND po.`discount_type` = 0), (po.`discount` - inward.`discount_on_total`), po.`discount`) AS final_discount'))
                        ->first();
                //Log::info('poDetails');
                $poDetails = (array)$poDetails;
                //Log::info($poDetails);
                if(!empty($poDetails))
                {                    
                    if(isset($poDetails['po_id']))
                    {
                        unset($poDetails['po_id']);
                    }
                    if(isset($poDetails['updated_at']))
                    {
                        unset($poDetails['updated_at']);
                    }
                    if(isset($poDetails['created_at']))
                    {
                        unset($poDetails['created_at']);
                    }

                    // if(isset($poDetails['final_discount'])) commented because it 'final_discount' exist in above query
                    // {
                        $poDetails['discount'] = $poDetails['final_discount'];
                        unset($poDetails['final_discount']);
                    // }

                    if(isset($poDetails['po_so_order_code']))
                    {
                        unset($poDetails['po_so_order_code']);
                    }
                    if(isset($poDetails['po_so_status']))
                    {
                        unset($poDetails['po_so_status']);
                    }                    
                    $currentPoCode = $poDetails['po_code'];
                    $poCodeDetails = explode('_', $currentPoCode);
                    $poCode = isset($poCodeDetails[0]) ? $poCodeDetails[0] : $currentPoCode;
                    $codeCount = DB::table('inward')
                            ->leftJoin('po', 'po.po_id', '=', 'inward.po_no')
                            ->where('po_code', 'like', '%'.$poCode.'%')
                            ->count();
                    $poDetails['po_code'] = $poCode . '_'.$codeCount;
                    $poDetails['po_status'] = 87001;
                    $poDetails['approval_status'] = 57031;
                    $poDetails['parent_id'] = $poId;
                    $poDetails['logistic_associate_id'] = NULL;
                    $poDetails['is_closed'] = 0;
                    $poDetails['platform'] = 5001;
                    $poDetails['po_date'] = date('Y-m-d H:i:s');
                    $poDetails['created_by'] = $createdBy;
                    $poDetails['updated_by'] = $createdBy;
                    //Log::info('before insert poDetails');
                    //Log::info($poDetails);
                    $newPoId = DB::table('po')->insertGetId($poDetails);
//                    $productCollection = DB::table('po_products')
//                            ->leftJoin('inward', 'inward.po_no', '=', 'po_products.po_id')
//                            ->leftJoin('inward_products', function($join)
//                            {
//                               $join->on('inward_products.product_id', '=', 'po_products.product_id');
//                               $join->on('inward.inward_id', '=', 'inward_products.inward_id');
//                            })
//                            ->where('po_products.po_id', $poId)
//                            ->select('po_products.*', DB::raw('(IF(inward_products.`received_qty`, (SUM(po_products.qty * po_products.no_of_eaches) - inward_products.`received_qty`), SUM(po_products.qty * po_products.no_of_eaches))) as diff'))
//                            ->groupBy('po_products.product_id')
////                            ->having('diff', '>', 0)
//                            ->get();
                    $productCollection = DB::select("CALL getSubPoProductDetails(".$poId.")");
//                    Log::info('productCollection');
//                    Log::info($productCollection);
                    $newPoProductData = [];
                    if(!empty($productCollection))
                    {
                        foreach($productCollection as $productInfo)
                        {
                            $tempProductInfo = [];
                            $tempProductInfo = (array)$productInfo;
                            $diffCount = isset($tempProductInfo['diff']) ? $tempProductInfo['diff'] : 0;
                            if($diffCount > 0)
                            {   
                                if(isset($tempProductInfo['po_product_id']))
                                {
                                    unset($tempProductInfo['po_product_id']);
                                }
                                if(isset($tempProductInfo['diff']))
                                {
                                    unset($tempProductInfo['diff']);
                                }
                                if(isset($tempProductInfo['created_at']))
                                {
                                    unset($tempProductInfo['created_at']);
                                }
                                $tempProductInfo['po_id'] = $newPoId;
                                $no_of_eaches = $tempProductInfo['no_of_eaches'];
                                $qty = $tempProductInfo['qty'];
                                if(($no_of_eaches * $qty) <= $diffCount)
                                {
                                    $diffResult = ($diffCount/($no_of_eaches));
                                    if(is_float($diffResult))
                                    {
                                        $no_of_eaches = 1;
                                        $tempProductInfo['no_of_eaches'] = 1;
                                        $tempProductInfo['uom'] = 16001;
                                    }else{
                                        $diffCount = $diffResult;
                                    }
                                }else{
                                    $no_of_eaches = 1;
                                    $tempProductInfo['no_of_eaches'] = 1;
                                    $tempProductInfo['uom'] = 16001;
                                }
                                $unit_price = $tempProductInfo['unit_price'];
                                $tax_per = $tempProductInfo['tax_per'];
                                $is_tax_included = $tempProductInfo['is_tax_included'];
                                $tempProductInfo['qty'] = $diffCount;
                                $subTotal = (($diffCount * $no_of_eaches) * $unit_price);                                
                                $tempProductInfo['price'] = ($no_of_eaches * $unit_price);
                                $taxAmount = 0;
                                if($is_tax_included)
                                {
                                    $basePrice = ($subTotal/(100+$tax_per)*100);
                                    $taxAmount = ($subTotal - $basePrice);
                                }else{
                                    $taxAmount = (($subTotal * $tax_per)/100);
                                }
                                $tempProductInfo['tax_amt'] = $taxAmount;
                                $newPoProductData[] = $tempProductInfo;
                            }
                        }
                        //Log::info('before insert newPoProductData');
                        //Log::info($newPoProductData);
                        if(!empty($newPoProductData))
                        {
                            DB::table('po_products')->insert($newPoProductData);
                        }
                    }
                }                
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function assetProductDetails($inwardId) {
        try
        {
            $productList = DB::table('inward_products')
                    ->leftJoin('inward', 'inward.inward_id', '=', 'inward_products.inward_id')
                    ->leftJoin('products', 'products.product_id', '=', 'inward_products.product_id')
                    ->where('products.product_type_id', 130001)
                    ->where('inward_products.inward_id', $inwardId)
                    ->select('products.product_id','products.business_unit_id','products.asset_category', DB::raw('inward_products.received_qty as qty'), 'inward_products.created_by',
                            'inward.invoice_no', 'inward.invoice_date')
                    ->get()->all();
            //Log::info($productList);
            if(!empty($productList))
            {
                $assetProductList = json_decode(json_encode($productList), true);
                //Log::info($assetProductList);
                app('App\Modules\Assets\Controllers\assetsController')->saveAssetDetails($assetProductList);
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

public function calculateElp($inwardId, $productId, $discountTotalValue, $totalInwardValue)
    {
        try
        {
            $elp = 0;
            $productDetails = DB::table('inward')
                ->leftJoin('inward_products', 'inward_products.inward_id', '=', 'inward.inward_id')
                ->where(['inward.inward_id' => $inwardId, 'inward_products.product_id' => $productId])
                ->select('inward_products.product_id', 'inward_products.row_total', DB::raw('(inward_products.good_qty - inward_products.free_qty) as good_qty'))
                ->first();
            if(!empty($productDetails))
            {
                $rowTotal = property_exists($productDetails, 'row_total') ? $productDetails->row_total : 0;
                $goodQty = property_exists($productDetails, 'good_qty') ? $productDetails->good_qty : 0;
                if($discountTotalValue > 0)
                {
                    $contribution = ($rowTotal/$totalInwardValue);
                    $finalRowDiscount = ($contribution * $discountTotalValue);
                    $finalRowTotal = ($rowTotal - $finalRowDiscount);
                }else{
                    $finalRowTotal = $rowTotal;
                }
                $elp = ($finalRowTotal/$goodQty);
            }   
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $elp;
    }
    
    public function updateTaxValues($inwardId)
    {
        try
        {
            if($inwardId > 0)
            {
                DB::enableQueryLog();
                $grnProductsList = DB::table('inward_products')
                            ->leftJoin('inward', 'inward.inward_id', '=', 'inward_products.inward_id')
                            ->leftJoin('po_products', function($join)
                            {
                                $join->on('po_products.product_id', '=', 'inward_products.product_id');
                                $join->on('inward.po_no', '=', 'po_products.po_id');
                            })                            
                            ->where(['inward_products.inward_id' => $inwardId])
                            ->select('inward_products.inward_prd_id', 'po_products.hsn_code', 'po_products.tax_data', 'inward_products.tax_amount')
                            ->groupBy('inward_products.product_id')
                            ->get()->all();
                if(!empty($grnProductsList))
                {
                    foreach($grnProductsList as $productDetails)
                    {
                        $inputData = [];
                        $taxInfo = [];
                        $taxDetails = [];
                        $inward_prd_id = property_exists($productDetails, 'inward_prd_id') ? $productDetails->inward_prd_id : 0;
                        $hsnCode = property_exists($productDetails, 'hsn_code') ? $productDetails->hsn_code : '';
                        $taxData = property_exists($productDetails, 'tax_data') ? $productDetails->tax_data : '';
                        $taxAmount = property_exists($productDetails, 'tax_amount') ? $productDetails->tax_amount : 0.00;
                        $inputData['hsn_code'] = $hsnCode;
                        $taxDetails = json_decode($taxData, true);
                        if(!empty($taxDetails))
                        {
                            $taxInfo = isset($taxDetails[0]) ? $taxDetails[0] : [];
                            $CGST = isset($taxInfo['CGST']) ? $taxInfo['CGST'] : 0;
                            $IGST = isset($taxInfo['IGST']) ? $taxInfo['IGST'] : 0;
                            $SGST = isset($taxInfo['SGST']) ? $taxInfo['SGST'] : 0;
                            $UTGST = isset($taxInfo['UTGST']) ? $taxInfo['UTGST'] : 0;
                            
                            $CGST = ($CGST/100) * $taxAmount;
                            $IGST = ($IGST/100) * $taxAmount;
                            $SGST = ($SGST/100) * $taxAmount;
                            $UTGST = ($UTGST/100) * $taxAmount;
                            
                            $taxInfo['CGST_VALUE'] = $CGST;
                            $taxInfo['IGST_VALUE'] = $IGST;
                            $taxInfo['SGST_VALUE'] = $SGST;
                            $taxInfo['UTGST_VALUE'] = $UTGST;
                        }
                        $taxInfo[] = $taxInfo; 
                        $inputData['tax_data'] = json_encode($taxInfo);
                        if(!empty($inputData))
                        {
                            DB::table('inward_products')
                            ->where(['inward_prd_id' => $inward_prd_id])
                            ->update($inputData);    
                        }
                    }
                }            
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
	
	public function getDeliveryGtin($grnId)
	{
		try{
			$gtinId = 0;
			if($grnId > 0)
			{
				$gstinDetails = DB::table('legal_entities')
					->leftJoin('inward', 'inward.legal_entity_id', '=', 'legal_entities.legal_entity_id')
					->leftJoin('legal_entities AS le', 'le.legal_entity_id', '=', 'legal_entities.parent_id')
					->where('inward.inward_id', $grnId)
					->first(['le.gstin']);
				if(!empty($gstinDetails))
				{
					$gtinId = property_exists($gstinDetails, 'gstin') ? trim($gstinDetails->gstin, " ") : 0;
				}
			}
			return $gtinId;
		} catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }	
	}

    public function getBillingAddress($leWhId) {
        try {
            $billingInfo = [];
            if ($leWhId > 0) {
            
         //    $billingInfo = DB::table('legal_entities')
									// ->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'legal_entities.parent_id')
									// ->leftJoin('countries', 'countries.country_id', '=', 'le.country')
									// ->where('legal_entities.legal_entity_id', $leWhId)
									// ->select('le.business_legal_name', 'le.address1', 'le.address2', DB::raw('countries.name as country_name'),
									// DB::raw('getStateNameById(le.state_id) AS state'), DB::raw('getStateCodeById(le.state_id) AS state_code'), 'le.gstin')
									// ->first();
                  
                $billingInfo = DB::table('legalentity_warehouses as lwh')
                        ->leftJoin('countries', 'countries.country_id', '=', 'lwh.country')
                        ->where('lwh.le_wh_id', $leWhId)
                        ->select(DB::raw('lwh.lp_wh_name as business_legal_name'), 'lwh.address1', 'lwh.address2', DB::raw('countries.name as country_name'), DB::raw('getStateNameById(lwh.state) AS state'), DB::raw('getStateCodeById(lwh.state) AS state_code'), DB::raw('lwh.tin_number as gstin'),'lwh.legal_entity_id')
                        ->first();
            }
            return $billingInfo;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    /*
     * getMasterLookupByCatId() method is used to get lookup data with value
     * @param Null
     * @return Array
     */
     
    public function getMasterLookupByCatId($catName = 'Order Status') {
        
        $fieldArr = array('lookup.master_lookup_name as name', 'lookup.value', 'lookup.description');
        $query = DB::table('master_lookup as lookup')->select($fieldArr);
        $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','lookup.mas_cat_id');
        $query->where('master_lookup_categories.mas_cat_name', $catName);
        $allLookupArr = $query->get()->all();
        
        $lookupArr = array();
        if(is_array($allLookupArr)) {
			foreach($allLookupArr as $data){
				$lookupArr[$data->value] = $data;
			}
        }
        return $lookupArr;
    }

    public function checkPOType($po_id){
        
        $checkPOType = DB::table('po')
                        ->select('po_so_order_code')
                        ->where(['po_id'=>$po_id,'po_so_status'=>1])
                        ->first();
        return $checkPOType;
    }
    public function getPOInfo($po_id){
        
        $checkPOInfo = DB::table('po')
                        ->select('*')
                        ->where(['po_id'=>$po_id])
                        ->first();
        return $checkPOInfo;
    }

    public function checkPOSOInvoiceStatus($gds_order_id){
        
        $checkPOInvoice = DB::table('gds_invoice_grid')
                        ->select('gds_order_id')
                        ->where(['gds_order_id'=>$gds_order_id])
                        ->first();
        return isset($checkPOInvoice->gds_order_id)?$checkPOInvoice->gds_order_id: array();
    }

    public function checkPODeliverStatus($order_code){
        
        $checkPOType = DB::table('gds_orders')
                        ->select('gds_order_id')
                        ->where(['order_status_id'=>17021,'order_code'=>$order_code])
                        ->first();
        return isset($checkPOType->gds_order_id)?$checkPOType->gds_order_id: "";
    }

    public function collectionRemittanceMapping($array){
        
        $remittanceID = DB::table('collection_remittance_history')
                        ->insertGetId($array);
        return $remittanceID;
    }

    public function remittanceMapping($array){
        
        $remittanceMappingID = DB::table('remittance_mapping')
                        ->insertGetId($array);
        return $remittanceMappingID;
    }

    public function SaveReferenceNo($data){

        $saverefe=DB::table('inward_docs')
                  ->where('inward_doc_id','=',$data['rid'])
                  ->update(['doc_ref_no'=>$data['reference_value']]); 

          if($saverefe){
            return true;
          }else{
            return false;
          }        
    }

    public function collectionDetailsById($order_id){
        
        $collections = DB::selectFromWriteConnection(DB::raw("SELECT * FROM collections c WHERE c.`gds_order_id` = $order_id LIMIT 1"));
        return $collections[0];
    }

    public function getUserByLegalEntityId($legal_entity_id){
        $token = DB::table('users')->select('password_token','user_id')->where(['legal_entity_id'=>$legal_entity_id,'is_active'=>1,'is_parent'=>0])->get()->all();
        return isset($token[0]->password_token)?$token[0]:array();
    }

    public function getOrderByOrderId($order_id){
        
        $orderData = DB::table('gds_orders')
                        ->select('le_wh_id','hub_id')
                        ->where(['gds_order_id'=>$order_id])
                        ->first();
        return isset($orderData->hub_id)?$orderData: "";
    }

    public function getProductInfo($product_id){
        $productData = DB::table('products')
                        ->select('*')
                        ->where(['product_id'=>$product_id])
                        ->first();
        return isset($productData->product_id)?$productData: "";
    }


    public function getHubIdByDcId($dc_id){
        $hub = DB::table('dc_hub_mapping')
                        ->select('hub_id')
                        ->where(['dc_id'=>$dc_id])
                        ->first();
        return isset($hub->hub_id)?$hub->hub_id: 0;
    }

    public function checkStockInward($inwardId,$productId){
        $checkStockInward = DB::table('stock_inward')
                                ->where(['reference_no' => $inwardId, 'product_id' => $productId])
                                ->select('stock_inward_id')
                                ->first();
        return $checkStockInward;
    }
    public function getMfgDate($poId,$product_id){
        $po_so_code = DB::table('po')->where('po_id',$poId)->pluck('po_so_order_code')->all();
        $code = isset($po_so_code[0])?$po_so_code[0]:'';
        if (empty($code)) {
            $date=date("Y-m-d");
        }else{
            $gdsOrders = DB::table('gds_orders as go')
                            ->select('gob.main_batch_id')
                            ->join('gds_orders_batch as gob','gob.gds_order_id','=','go.gds_order_id')
                            ->where('order_code',$code)
                            ->where('gob.product_id',$product_id)
                            ->first();
            if (isset($gdsOrders->main_batch_id)) {          
                $inwdPrdDetails =DB::table('inward_product_details as inwpd')
                                        ->join('inward_products as inwp','inwp.inward_prd_id','=','inwpd.inward_prd_id')
                                        ->where('inwp.inward_id',$gdsOrders->main_batch_id)
                                        ->where('inwp.product_id',$product_id)
                                        ->pluck('inwpd.mfg_date')->all();
                $date =isset($inwdPrdDetails[0])?$inwdPrdDetails[0]:date('Y-m-d'); 
            }else{
                $date = date("Y-m-d");
            }
        }
        return $date;
    }
}
