<?php
namespace App\Modules\Product\Controllers;
/*
* ProductApiController is used to manage Products related api
* @author    Ebutor <info@ebutor.com>
* @copyright ebutor@2018
* @package   Products API
* @version:  v1.0
*/
use Illuminate\Support\Facades\Input;
use Session;
use Response;
use URL;
use Config;
use DB;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use App\models\Mongo\MongoApiLogsModel;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Central\Repositories\ProductRepo;
use App\Modules\Product\Models\ProductInfo;
use App\Modules\Product\Models\ProductAttributes;
use App\Modules\Pricing\Models\pricingDashboardModel;
use App\Modules\Cpmanager\Models\SearchModel;

class ProductApiController extends BaseController {
    public function __construct(){
      $this->token = new CategoryModel();
    }

    public function createProductAPI()
    {
        try {
            $data = Input::all();
            $data = json_decode($data['data'], true);
            $token = $data['token'];
        } catch (\Exception $e) {
            return json_encode(['status' => "failed", 'message' =>"Invalid Params"]);
        }

        $tokenResult = $this->token->checkCustomerToken($token);
        if($tokenResult <= 0)
            return json_encode(['status' => "session", 'message' =>"Invalid Customer Token"]);

        if(!isset($data['flag']))
            return ['status' => "failed", 'message' =>"Invalid Flag Paramater"];

        //flag represents CRUD Operation
        // 1 -> To Create a New Product
        // 2 -> To Get Details of the Existing Single Product
        // 3 -> To Update Details of the Existing Single Product
        // 4 -> To Delete an Existing Single Product
        if($data['flag'] == 1)
            return $this->addProductApi($data); 
        else if($data['flag'] == 2)
            return $this->viewProductApi($data); 
        else if($data['flag'] == 3)
            return $this->updateProductApi($data); 
        else if($data['flag'] == 4)
            return $this->deleteProductApi($data);
        else
            return ['status' => "failed", 'message' =>"Invalid Flag ID"];
    }

    // To Add the Product API - Zincle
    public function addProductApi($data)
    {
        try {
            $userId = $data['user_id'];
            $legalEntityId = $data['legal_entity_id'];  // Legal Entity Id
            // Images URL`s
            $defaultImageUrl = "https://s3.ap-south-1.amazonaws.com/ebutormedia/products/168+products/harisharan/no-image_zpseqbcsx2n.jpg";
            $productImageUrl = isset($data['product_image_url'])?$data['product_image_url']:$defaultImageUrl;
            $thumbnailImageUrl = isset($data['thumbnail_image_url'])?$data['thumbnail_image_url']:$defaultImageUrl;
            // Product Title
            $productTitle = isset($data['product_title'])?$data['product_title']:"";
            // Category, Brand and Manufacturer Ids
            $categoryId = isset($data['category_id'])?$data['category_id']:"";
            $brandId = isset($data['brand_id'])?$data['brand_id']:-1;
            $manufacturerId = NULL;
            if($brandId != -1){
                $manufacturerData = DB::table('brands')->where('brand_id',$brandId)->first();
                $manufacturerId = isset($manufacturerData->mfg_id)?$manufacturerData->mfg_id:"";
            }
            // MRP
            $mrp = isset($data['mrp'])?$data['mrp']:0;
            // Selling Units
            $sellingUnits = isset($data['selling_units'])?$data['selling_units']:0;
            // Offer Pack - [Regular for now]
            $offerPack = isset($data['offer_pack'])?$data['offer_pack']:"";
            // Is Sellable and Cp Enable - Boolean Values
            $isSellable = isset($data['is_sellable'])?intval($data['is_sellable']):0;
            $cpEnabled = isset($data['cp_enabled'])?intval($data['cp_enabled']):0;
            // KVI - Known Valued Items
            // Q1 = 69001 for Fast Moving (Parent Products)
            // Q9 = 69010 for Freebie Products
            $kvi = 69001;
            $sku = (new ProductRepo)->generateSKUcode();

        } catch (\Exception $e) {
            // Log::info("Adding Product Message ".$e->getMessage());
            return json_encode(["status" => "failed","message" => "Invalid Field Values"]);
        }

        // HSN Validation
        try {
            $hsnCode = $data['hsn_code'];
        } catch (\Exception $e) {
            return json_encode(["status" => "failed","message" => "HSN Code Required"]);
        }

        // Transaction Begin`s here Bro
        DB::beginTransaction();
        try {
        // Inserting the minimal details of the Products
        $productInsertId =
            DB::table('products')
                ->insertGetId([
                    "legal_entity_id" => $legalEntityId,
                    "category_id" => $categoryId,
                    "manufacturer_id" => $manufacturerId,
                    "brand_id" => $brandId,
                    "primary_image" => $productImageUrl,
                    "thumbnail_image" => $thumbnailImageUrl,
                    "product_title" => $productTitle,
                    "mrp" => $mrp,
                    "esu" => $sellingUnits,
                    "is_sellable" => $isSellable,
                    "cp_enabled" => $cpEnabled,
                    "kvi" => $kvi,
                    "sku" => $sku,
                    "product_type_id" => 130002,
                    "is_active" => 1,
                    "status" => 1,
                    "created_by" => $userId,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed", "message" => "Unable to Add Product", "full_message" => $e->getMessage()]);
        }
        // End Added Product Details to Products Table

        // Update the Product Group Id
        $status = $this->getProductGroupId($productInsertId,$categoryId,$userId);
        try {
            if(!$status)
                $raisExceptionHere;
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed", "message" => "Unable to set Product Attributes for the selected Category", "full_message" => $e->getMessage()]);    
        }
        // End Product Group Id

        // Get Legal Entity Warehouse ID, based on Legal Entity Id,
        try {
            $leWarehouseId = $this->getLeWhIdByLegalEntityId($legalEntityId);
            $stateId = $this->getStateIdByLeWareHouseId($leWarehouseId);
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed","message" => "Invalid Legal WareHouse Id", "full_message" => $e->getMessage()]);
        }
        // End Legal Entity Warehouse ID, based on Legal Entity Id,

        // Product Freebie Configuration Begin
        try {
            if(isset($data['freebie_details'])){
                $freebieInsertId =
                DB::table('products')
                    ->insertGetId([
                        "legal_entity_id" => $legalEntityId,
                        "product_title" => $data['freebie_details']['main_product_title'],
                        "kvi" => 69010,
                        "created_by" => $userId,
                    ]);

                // $productInsertId => Parent  Product Id
                // $freebieInsertId => Freebie Product Id
                $data['freebie_details']['main_product_id'] = $productInsertId;
                $this->configFreebieDetails("add",$data['freebie_details'],$freebieInsertId,$leWarehouseId,$stateId,$userId);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed", "message" => "Unable to Add Freebie Details", "full_message" => $e->getMessage()]);
        }
        // End Product Freebie Configuration

        // Checking Tax Percentage and retrieveing Tax Class Map Id
        try {
            $taxPer = isset($data['tax_percentage'])?intval($data['tax_percentage']):-1;
            if($taxPer != -1){
                // Hard Coded GST & State Id and Status
                // 4033 -> Telangana State
                $taxDetails = DB::table('tax_classes')
                    ->where([
                        ["tax_class_type","=","GST"],
                        ["state_id","=",$stateId],
                        ["status","=","Active"],
                        ["tax_percentage","=",$taxPer]
                    ])->first();
                $taxClassId = isset($taxDetails->tax_class_id)?$taxDetails->tax_class_id:-1;

                // If the tax is not valid or not found,
                // then we wont allow the user to create the product
                if($taxClassId == -1)
                    $taxClassId = $noTaxClassFound;
                
                $insertTaxValues =
                    DB::table('tax_class_product_map')
                    ->insert([
                        'product_id' => $productInsertId,
                        'tax_class_id' => $taxClassId,
                        'hsn_code' => $hsnCode,
                        'status' => 1,
                        'date_start' => date('Y-m-d'),
                    ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed","message" => "Invalid Tax Percentage", "full_message" => $e->getMessage()]);
        }
        // End of Tax Percentage

        // Update HSN Status to 1
        try {
            DB::table('HSN_Master')->where('ITC_HSCodes',$hsnCode)->update(['is_active' => 1]);
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed","message" => "Invalid HSN Code", "full_message" => $e->getMessage()]);
        }
        // End Update HSN Status

        // Begin Product Prices
        try {
            $ptr = $data["ptr"];
            $userName = $this->getUserNameByUserId($userId);            
            // Call to function for product prices and its details
            (new pricingDashboardModel)->addEditProductPrice([
                'product_id' => $productInsertId,
                'state_id' => $stateId,
                'customer_type' => 3014,
                'price' => $mrp,
                'ptr' => $ptr,
                'legal_entity_id' => $legalEntityId,
                'effective_date' => date('Y-m-d'),
                'created_by' => $userId,
                'created_at' => date('Y-m-d'),
                'dc_id' => $leWarehouseId,
            ],0,'',$userName);
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed","message" => "Invalid PTR and Pricing", "full_message" => $e->getMessage()]);
        }
        // End Product Prices


        // Added SOH and ATP values to Inventory Table
        try {
            $soh = isset($data['soh'])?intval($data['soh']):0;
            $atp = isset($data['atp'])?intval($data['atp']):10000;
            $inventoryStatus =
                DB::table('inventory')
                    ->insert([
                        'le_wh_id' => $leWarehouseId,
                        'product_id' => $productInsertId,
                        'soh' => $soh,
                        'atp' => $atp,
                    ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed","message" => "Unable to Add Inventory", "full_message" => $e->getMessage()]);
        }
        // End of Inventory Addition for the New Product

        // Insertion of Pack Configuration
        try{
            $productPacks = isset($data['product_packs'])?$data['product_packs']:-1;
            if($productPacks != -1){
                $productPackStatus = $this->addProductPacks($productPacks,$productInsertId,$stateId,$userId);
                if($productPackStatus == 0) throw new Exception("Unable to Add Product Pack Configuration", 1);
            }   
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed","message" => "Unable to Add Product Pack Configuration", "full_message" => $e->getMessage()]);
        }
        // End of Pack Configuraion

        // Finally
        DB::commit();

        return ["status"=>"success","message" => "Congrats! ".$productTitle." Product Added","product_id" => $productInsertId];
    }

    // To Pull the Data of a Single Product - Zincle
    // old Name: getSingleProductDetails
    public function viewProductApi($data)
    {
        try {
            $productId = $data['product_id'];
            $legalEntityId = $data['legal_entity_id'];
            $leWhId = $this->getLeWhIdByLegalEntityId($legalEntityId);
        } catch (\Exception $e) {
            return ['status' => "failed", 'message' => "Invalid Paramters", "full_message" => $e->getMessage()];
        }

        $productBasicDetail = 
            DB::table('products')
                ->select(
                    "category_id",
                    "brand_id",
                    "primary_image AS product_image_url",
                    "product_title",
                    "mrp",
                    "esu AS selling_units",
                    "is_sellable",
                    "cp_enabled",
                    "kvi",
                    "product_type_id"
                )
                ->leftjoin('inventory','inventory.product_id','=','products.product_id')
                ->where([
                    ['products.product_id','=',$productId],
                    ['inventory.le_wh_id','=',$leWhId]])
                ->first();

        $finalProductsArray = [];   // An empty Array
        array_push($finalProductsArray, json_decode(json_encode($productBasicDetail,1)));

        // Empty Product Check
        if(empty($productBasicDetail))
            return ["status"=>"failed", "message" => "Invalid ProductID or LegalEntityId", "data" => null]; 

        // Need to get Freebie Configuration
        // if the product has a freebie
        $freebieDetail =
            DB::table('freebee_conf')
                ->select("freebee_desc AS description")
                ->where([['main_prd_id','=',$productId],['le_wh_id','=',$leWhId]])
                ->limit(1)
                ->get()->all();
        if(!empty($freebieDetail))
            $finalProductsArray[0]->freebie_details = $freebieDetail;
        
        // Begin Product Packs
        $productPacks = DB::table('product_pack_config AS pack')
            ->selectRaw("
                pack.level,
                pack.no_of_eaches AS quantity,
                CAST(slab.price AS DECIMAL(18,2)) AS price,
                CAST(slab.margin AS DECIMAL(18,2)) AS margin
            ")
            ->leftJoin('products_slab_rates AS slab',function($join){
                $join->on('slab.product_id','=','pack.product_id')
                ->on('slab.pack_type','=','pack.level');
            })
            ->where('pack.product_id',$productId)
            ->groupby('pack.level')
            ->get()->all();
        if(!empty($productPacks)){
            $finalProductsArray[0]->product_packs = $productPacks;
        }
        // End Product Packs

        // Begin Tax Class & HSN
        $taxDetails = 
            DB::table('tax_class_product_map AS tax_map')
                ->leftJoin('tax_classes','tax_classes.tax_class_id','=','tax_map.tax_class_id')
                ->where([
                    ['tax_map.product_id','=',$productId],
                    ['tax_classes.tax_class_type','=','GST']
                ])
                ->first();
        $finalProductsArray[0]->hsn_code = $taxDetails->hsn_code;
        $finalProductsArray[0]->tax_class = $taxDetails->tax_percentage;
        // End Tax Class & HSN

        // Product Price and PTR
        $productPrice = 
            DB::table('product_prices')
                ->where([
                    ['product_id','=',$productId],
                    ['legal_entity_id','=',$legalEntityId]
                ])
                ->first();
        $finalProductsArray[0]->ptr = $productPrice->ptr;
        // End Product Price and PTR

        return ["status"=>"success", "message" => "Product Edit", "data" => $finalProductsArray];
    }


    // To Update the Data of a Single Product - Zincle
    public function updateProductApi($data)
    {
        DB::beginTransaction();
        try {
            $productId = intval($data['product_id']);
            $legalEntityId = intval($data['legal_entity_id']);
            $userId = $data['user_id'];
            $leWarehouseId = $this->getLeWhIdByLegalEntityId($legalEntityId);
            
            // Updating the Products
            $query = "
                UPDATE products
                LEFT JOIN inventory ON inventory.product_id = products.product_id
                SET 
                    primary_image = '".$data['product_image_url']."',
                    thumbnail_image = '".$data['thumbnail_image_url']."',
                    product_title = '".$data['product_title']."',
                    mrp = ".$data['mrp'].",
                    esu = ".$data['selling_units'].",
                    is_sellable = ".$data['is_sellable'].",
                    cp_enabled = ".$data['cp_enabled']."
                WHERE
                    products.product_id = ".$productId."
                    AND products.legal_entity_id = ".$legalEntityId."
                    AND inventory.le_wh_id = ".$leWarehouseId;
            $updateStatus = DB::STATEMENT($query);
                
            // Done Updating Products
        } catch (\Exception $e) {
            DB::rollBack();
            return ["status"=>"failed", "message" => "Unable to Update Product Details", "full_message" => $e->getMessage()];
        }

        // Get Legal Entity Warehouse ID, based on Legal Entity Id,
        try {
            $stateId = $this->getStateIdByLeWareHouseId($leWarehouseId);
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed","message" => "Invalid Legal WareHouse Id or State Id", "full_message" => $e->getMessage()]);
        }
        // End Legal Entity Warehouse ID, based on Legal Entity Id,

        // Closing PTR in Update API //
        // PTR updation can be done through WEB pricing module only //

        // Updation of Pack Configuration
        try{
            $productPacks = isset($data['product_packs'])?$data['product_packs']:-1;
            if($productPacks != -1){
                $productPackStatus = $this->addProductPacks($productPacks,$productId,$stateId,$userId,1);
                if($productPackStatus == 0) throw new \Exception("Unable to Update Product Packs", 1);
            }   
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(["status" => "failed","message" => "Unable to Update Product Pack Configuration", "full_message" => $e->getMessage()]);
        }
        // End of Pack Configuraion

        // Finall Commit
        DB::commit();
        return ["status"=>"success", "message" => "Product Successfully Updated", "data" => $updateStatus];
    }

    // TODO: Delete Product
    public function deleteProductApi($data)
    {
        # code...
    }

    // Add Product Packs
    public function addProductPacks($productPacks,$productId,$stateId,$userId,$isEdit = false)
    {
        try {

        // Code to check the uniqueness of the Product Level
        // If there is a duplicate product pack, then we will raise an exception 
        $packLevelList = [];
        foreach ($productPacks as $pack) {
            if(empty($packLevelList) or !in_array($pack["level"], $packLevelList))
                array_push($packLevelList, $pack["level"]);
            else
                return 0;
        }

        // Before Inserting Product Packs, we need to delete them
        // Only when Editing the Product Packs
        if($isEdit){
            DB::table('product_pack_config')->where('product_id',$productId)->delete();
            DB::table('products_slab_rates')->where('product_id',$productId)->delete();
        }

        foreach ($productPacks as $pack) {
            // Inserting into Product Pack Configuration here
            $packsInsert =
                DB::table('product_pack_config')
                ->insert([
                    'product_id' => $productId,
                    'level' => $pack["level"],
                    'no_of_eaches' => $pack["quantity"],
                    'is_sellable' => 1,
                    'effective_date' => date('Y-m-d'),
                    'created_by' => $userId,
                ]);
            // Inserting Pack Price in Slab Rates
            $slabInsert =
                DB::table('products_slab_rates')
                ->insert([
                    'price' => $pack["price"],
                    'product_id' => $productId,
                    'margin' => $pack["margin"],
                    'state_id' => $stateId,
                    'customer_type' => 3014,
                    'start_date' => date("Y-m-d"),
                    'end_date' => date('Y-m-d', strtotime("+3 months", strtotime(date('Y-m-d')))),
                    'pack_type' => $pack["level"],
                    'created_by' => $userId,
                ]);
        }
        } catch (\Exception $e) {
            return 0;
        }
        return 1;    
    }

    public function configFreebieDetails($freebieStatus,$freebieDetails,$productId,$leWarehouseId,$stateId,$userId)
    {
        // $productId => is the current freebie product ID
        if($freebieStatus == "add"){
            return
            DB::table('freebee_conf')->insertGetId([
                "main_prd_id" => $freebieDetails['main_product_id'],
                "freebee_desc" => $freebieDetails['description'],
                "mpq" => $freebieDetails['mpq'],
                "qty" => $freebieDetails['quantity'],
                "free_prd_id" => $productId,
                "le_wh_id" => $leWarehouseId,
                "start_date" => date("Y-m-d"),
                "end_date" => (date("Y")+1)."-".(date("m-d")),
                "state_id" => $stateId,
                "created_by" => $userId,
            ]);
        }else if($freebieStatus == "edit"){
            // Currently We haven`t started this
        }else if($freebieStatus == "delete"){
            return
            DB::table('freebee_conf')->where([
                ["free_prd_id",'=',$productId],
                ["main_prd_id",'=',$freebieDetails['main_product_id']],
            ])->delete();
        }
    }

    // Method to Get Username Based on UserID
    public function getUserNameByUserId($userId)
    {
        $userName = DB::table('users')->where('user_id',$userId)->selectRaw('concat(firstname," ",lastname) AS name')->first();
        return isset($userName->name)?$userName->name:"";
    }

    public function getLeWhIdByLegalEntityId($legalEntityId)
    {
        $leWarehouseId = DB::table('legalentity_warehouses')
            ->where([['legal_entity_id','=',$legalEntityId],['dc_type','=',118001]])
            ->first();
        // Raises No Legal WareHouse Id Exception, if not found
        $leWarehouseId = isset($leWarehouseId->le_wh_id)?$leWarehouseId->le_wh_id:$noLegalWareHouseId;
        return $leWarehouseId;
    }

    // API to get all the Cp Enabled and Is Sellable Parent Products
    public function getAllParentProductsList()
    {
        try {
            $data = Input::all();
            $data = json_decode($data['data'], true);

            $token = $data['token'];
            $legalEntityId = $data['legal_entity_id'];
        } catch (\Exception $e) {
            return json_encode(['status' => "failed", 'message' =>"Invalid Paramters", "full_message" => $e->getMessage()]);
        }

        $tokenResult = $this->token->checkCustomerToken($token);
        if($tokenResult <= 0)
            return json_encode(['status' => "session", 'message' =>"Invalid Customer Token"]);

        try {
            $productsList = $this->getProductsByLegalEntityId($legalEntityId);
            $productsList = $productsList->get()->all();
            return json_encode(['status'=>"success",'list'=>$productsList]);
        } catch (\Exception $e) {
            return json_encode(['status'=>"failed",'message'=>$e->getMessage()]);
        }
    }

    public function getProductsByLegalEntityId($legalEntityId)
    {
        $productsList = DB::table('products')
            ->select('product_id','product_title','is_sellable','cp_enabled','is_parent','thumbnail_image','mrp','esu')
            ->where([
                ['legal_entity_id','=',$legalEntityId],
                ['is_parent','=',1],
                ['is_active','=',1],
                ['status','=',1],
                ['kvi','<>',69010]
            ])
            ->orderBy('product_id','desc');
        // Returns Eloquent Object, not the data, you need 
        // to use get() to generate data for the above query
        return $productsList;
    }

    public function getPackLevelList()
    {
      try {
        $data = Input::all();
        $data = json_decode($data['data'], true);

        $token = $data['token'];
        $legalEntityId = $data['legal_entity_id'];
      } catch (\Exception $e) {
        return json_encode(['status' => "failed", 'message' =>"Invalid Paramters", "full_message" => $e->getMessage()]);
      }

      $tokenResult = $this->token->checkCustomerToken($token);
      if($tokenResult <= 0)
        return json_encode(['status' => "session", 'message' =>"Invalid Customer Token"]);

      try {
        $packList = DB::table('master_lookup')
          ->select("master_lookup_name AS pack_level","value AS pack_level_id")
          ->where([["mas_cat_id","=",16],["is_active","=",1]])
          ->orderBy("sort_order")->get()->all();
        return json_encode(['status' => "success", 'message' => "Pack Values", "list" => $packList]);
      } catch (\Exception $e) {
        return json_encode(['status' => "failed", 'message' => "Database Error", "full_message" => $e->getMessage()]);
      }
    }

    public function getCategoriesAndBrands()
    {
      try {
        $data = Input::all();
        $data = json_decode($data['data'], true);

        $token = $data['customer_token'];
        $segmentId = $data['segment_id'];
        $legalEntityId = $data['legal_entity_id'];
        $leWhId = $this->getLeWhIdByLegalEntityId($legalEntityId);
        $offset = isset($data['offset'])?intval($data['offset']):0;
        $offsetLimit = isset($data['offset_limit'])?intval($data['offset_limit']):20;
      } catch (\Exception $e) {
        return json_encode(['status' => "failed", 'message' => "Invalid Paramters", "full_message" => $e->getMessage()]);
      }

      $tokenResult = $this->token->checkCustomerToken($token);
      if($tokenResult <= 0)
        return json_encode(['status' => "session", 'message' => "Invalid Customer Token"]);

      try {
        $brandsData = DB::SELECT("call getCPBrands_all('".$leWhId."',$segmentId,$offsetLimit,$offset,'0')");
        $categoriesData = DB::SELECT("call getCPCategories_all('".$leWhId."',$segmentId,$offsetLimit,$offset)");
        return json_encode(['status' => "success", 'message' => "Brands and Categories Data", 'brands' => $brandsData, 'categories' => $categoriesData]);
      } catch (\Exception $e) {
        return json_encode(['status' => "failed", 'message' => "Database Error", "full_message" => $e->getMessage()]);
      }
    }

    /* In this api, we get all the products irrespective of cp_enabled and is_sellable */
    public function getAllProductsList()
    {
      try {
        
        $data = Input::all();
        $data = json_decode($data['data'], true);

        $token = $data['token'];
        //$legalEntityId = $data['legal_entity_id'];
        $le_wh_id = $data['le_wh_id'];

        } catch (\Exception $e) {
            return ['status' => "failed", 'message' => "Invalid Paramters", "full_message" => $e->getMessage()];
        }

        $tokenResult = $this->token->checkCustomerToken($token);
        if($tokenResult <= 0)
            return ['status' => "session", 'message' => "Invalid Customer Token"];

        $getCount = (isset($data['get_count']) and $data['get_count'] == 1)?1:0;
        $limit = isset($data['limit'])?intval($data['limit']):20;
        $offset = isset($data['offset'])?intval($data['offset']):0;

        try {

            // Database call to Products List
            $productsList = $this->getProductsByDCId($le_wh_id);

            // I dont know, y this count is working when I keep this here, and y not when its below.
            // Sometimes its better to die :(
            $count = 0;
            if($getCount == 1){
                $count = $productsList->count();
            }
            // Additional Conditions are written below
            if($limit != 0)
                $productsList = $productsList->limit($limit)->offset($offset);
            $productsList = $productsList->get()->all();

        } catch (\Exception $e) {
            return ['status' => "failed", 'message' => "Database Error", 'full_message' => $e->getMessage()];
        }
        return ['status' => "success", 'message' => "Products List", 'list' => ['count' => $count, 'products' => $productsList]];
    }

    public function validateHSNCode()
    {
        try {
            $data = Input::all();
            $data = json_decode($data['data'], true);

            $token = $data['token'];
            $hsnCode = $data['HSN_code'];

        } catch (\Exception $e) {
            return ['status' => "failed", 'message' => "Invalid Paramters", "full_message" => $e->getMessage()];
        }

        $tokenResult = $this->token->checkCustomerToken($token);
        if($tokenResult <= 0)
            return ['status' => "session", 'message' => "Invalid Customer Token"];

        try {
            $count = DB::table('HSN_Master')->where('ITC_HSCodes',$data['HSN_code'])->count();
            $isUnique = ($count==0)?false:true;
            return ['status' => "success", "message" => "HSN Uniqueness", 'result' => $isUnique];
        } catch (\Exception $e) {
            return ['status' => "failed", "message" => "Database Error", 'full_message' => $e->getMessage()];
        }
    }

    public function getProductGroupId($pid,$categoryId,$userId)
    {
        try {
            $productAttributeSet = (new ProductInfo)->getEditProductAttributeData($pid,$categoryId);
            if($productAttributeSet == "false")
                return false;
            
            $attMainArray = [];
            foreach ($productAttributeSet as $productAttrSet) {
                $attArray = [];
                $attArray['product_id'] = $pid;
                $attArray['attribute_id'] = $productAttrSet->attribute_id;
                $attArray['created_by'] = $userId;
                array_push($attMainArray, $attArray); 
                // Inserting product attributes fields here
            }
            ProductAttributes::insert($attMainArray);
            $productSql = 
                DB::table('products')
                    ->join('product_attributes','product_attributes.product_id','=','products.product_id')
                    ->where('products.product_id','=',$pid)
                    ->wherein('attribute_id',array(1,2))
                    ->select('product_title','brand_id','category_id','manufacturer_id','product_group_id','product_title','attribute_id','value')
                    ->get()->all();

            $productSql = json_decode(json_encode($productSql), true);
            $brand_id = $productSql[0]['brand_id'];
            $cat_id = $productSql[0]['category_id'];
            $manf_id = $productSql[0]['manufacturer_id'];
            $pack_type_value = $productSql[0]['value'];
            $pack_type = $productSql[1]['value'];
            $attributeStatus = 
                DB::table('product_attributes')
                    ->join('products','product_attributes.product_id','=','products.product_id')
                    ->where('brand_id',$brand_id)
                    ->where('category_id',$cat_id)
                    ->where('manufacturer_id',$manf_id)
                    ->wherein('attribute_id', [1,2])
                    ->wherein('value',[$pack_type_value,$pack_type])
                    ->select('product_group_id')
                    ->groupby('product_attributes.product_id')
                    ->havingRaw('COUNT(DISTINCT(product_attributes.value)) = 2')
                    ->first();

            $attributeStatus = json_decode(json_encode($attributeStatus), true);
            if($attributeStatus['product_group_id'] == 0)
            {
                $title = $productSql[0]['product_title'];
                $conditonRs=0;
                if(strstr($title, 'mrp', true))
                {
                    $title = strstr($title, 'mrp', true);
                    $conditonRs = 1;
                }
                else if(strstr($title, 'MRP', true))
                {
                    $title = strstr($title, 'MRP', true);
                    $conditonRs = 1;
                }
                $getGrpId = 
                    DB::table('product_groups')
                        ->insertGetId(['product_grp_name'=>$title,"created_by"=>$userId]);
                $rss = 
                    DB::table('product_groups')
                        ->where('product_grp_id',$getGrpId)
                        ->update(['product_grp_ref_id'=> $getGrpId.'000','updated_by'=>$userId]);

                DB::table('products')
                ->where('product_id', $pid)
                ->update(['product_group_id' => $getGrpId.'000']);

            }
            else if($attributeStatus['product_group_id']!=0)
            {
                DB::table('products')
                ->where('product_id', $pid)
                ->update(['product_group_id' => $attributeStatus['product_group_id']]);
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    

    public function  searchProducts()
    {
        try {
            $data = Input::all();
            $data = json_decode($data['data'], true);

            $query = strtolower($data['query']);
            $limit = isset($data['limit'])?$data['limit']:10;
            $token = $data['token'];
            $legalEntityId = $data['legal_entity_id'];
            $leWhId = $this->getLeWhIdByLegalEntityId($legalEntityId);

            if(empty($query))   $emptySearchQuery;
        } catch (\Exception $e) {
            return ['status' => "failed", 'message' => "Invalid Paramters", "full_message" => $e->getMessage()];
        }

        $tokenResult = $this->token->checkCustomerToken($token);
        if($tokenResult <= 0)
            return ['status' => "session", 'message' => "Invalid Customer Token"];

        try {
            // Query to Get Brands
            $brands = $this->searchBrandsByQuery($query,$limit,$legalEntityId);
            // Query to Get Categories
            $categories = $this->searchCategoriesByQuery($query,$limit);
            // Query to Get Products
            $products = $this->searchProductsByQuery($query,$limit,$leWhId);
            // return $products;

            $list = array_merge($brands,$categories,$products);

            return ["status"=>"success","message" => "Search Result","data" => ["data" => $list]];
        } catch (\Exception $e) {
            return ["status"=>"failed","message" => "Invalid Product Search","full_message" => $e->getMessage()];
        }
    }

    public function searchProductsByQuery($query,$limit,$leWhId)
    {
        return
            DB::table('products')
                ->select('products.product_id','product_title AS name')
                ->leftjoin('inventory','inventory.product_id','=','products.product_id')
                ->join('product_cpenabled_dcfcwise as pcdfw','pcdfw.product_id','=','products.product_id')
                ->where([
                    ['products.product_title','like','%'.$query.'%'],
                    ['products.is_parent','=',1],
                    ['products.is_active','=',1],
                    ['products.status','=',1],
                    ['kvi','<>',69010],
                    ['inventory.le_wh_id','=',$leWhId]])
                ->where('pcdfw.cp_enabled', '=', 1)
                ->where('pcdfw.is_sellable', '=', 1)
                ->whereRaw('FIND_IN_SET(pcdfw.le_wh_id,' . $le_wh_id . ')')
                ->orderBy('products.product_id','desc')
                ->limit($limit)
                ->get()->all();
    }

    public function searchBrandsByQuery($query,$limit,$legalEntityId)
    {
        return 
            DB::table('brands')
                ->selectRaw("DISTINCT brand_id,brand_name AS name")
                ->where([
                    ['brand_name','like','%'.$query.'%'],
                    ['legal_entity_id','=',$legalEntityId],])
                ->limit($limit)
                ->get()->all();
    }

    public function searchCategoriesByQuery($query,$limit)
    {
        return
            DB::table('categories')
                ->selectRaw("distinct category_id AS category_id,cat_name AS name")
                ->where([
                    ['cat_name','like','%'.$query.'%'],
                    ['is_active','=',1],])
                ->limit($limit)
                ->get()->all();
    }

    public function getStateIdByLeWareHouseId($leWarehouseId)
    {
        $stateId = 
            DB::table("legalentity_warehouses")
            ->where('le_wh_id',$leWarehouseId)
            ->first();

        return $stateId->state;
    }
    public function getSelectedProduct()
    {
      try {
        
        $data = Input::all();
        $data = json_decode($data['data'], true);
        $token = $data['token'];
        //$legalEntityId = $data['legal_entity_id'];
        $le_wh_id = $data['le_wh_id'];
        $product_id = $data['product_id'];

        } catch (\Exception $e) {
            return ['status' => "failed", 'message' => "Invalid Paramters", "full_message" => $e->getMessage()];
        }

        $tokenResult = $this->token->checkCustomerToken($token);
        if($tokenResult <= 0)
            return ['status' => "session", 'message' => "Invalid Customer Token"];

        try {

            // Database call to Products List
            $selectedProduct = $this->getProductsByProductId($le_wh_id,$product_id);

           
            $selectedProduct = $selectedProduct->get()->all();
            $selectedProduct = isset($selectedProduct[0])?$selectedProduct[0]:[];

        } catch (\Exception $e) {
            return ['status' => "failed", 'message' => "Database Error", 'full_message' => $e->getMessage()];
        }
        return ['status' => "success", 'message' => "Your Selected Product","data"=>$selectedProduct];
    }

    public function getProductsByProductId($le_wh_id=0,$product_id)
    {
        $selectedProduct = DB::table('products as pro')
            ->join('inventory as inv','pro.product_id','=','inv.product_id')
            ->join('product_cpenabled_dcfcwise as pcdfw','pcdfw.product_id','=','pro.product_id')
            ->select('pro.product_id','product_title','pcdfw.is_sellable','pcdfw.cp_enabled','is_parent','thumbnail_image','mrp','pcdfw.esu',DB::raw("IFNULL(getProductEsp_wh(pro.product_id,".$le_wh_id."),0) as esp"),DB::raw("IFNULL(GetCPInventoryByProductId(pro.product_id,".$le_wh_id."),0) as inventory"))
            
                ->where('pcdfw.cp_enabled', '=', 1)
                ->where('pcdfw.is_sellable', '=', 1)
                ->whereRaw('FIND_IN_SET(pcdfw.le_wh_id,' . $le_wh_id . ')')
            ->where([
                //['pro.legal_entity_id','=',$legalEntityId],
                ['pro.product_id', '=',$product_id],
                //['pro.is_parent','=',1],
                //['pro.is_active','=',1],
                //['pro.status','=',1],
                ['pro.product_type_id','=', 130002],
                ['kvi','<>',69010],
                ['inv.le_wh_id', $le_wh_id]
            ])->whereRaw("GetCPInventoryByProductId(pro.product_id,".$le_wh_id.") > 0")
            ->orderBy('pro.product_id','desc');
        // Returns Eloquent Object, not the data, you need 
        // to use get() to generate data for the above query
        return $selectedProduct;
    }
    public function searchAllProducts()
    {
        try {
            $data = Input::all();
            $data = json_decode($data['data'], true);
            $query = strtolower($data['query']);
            $limit = isset($data['limit'])?$data['limit']:10;
            $token = $data['token'];
            //$legalEntityId = $data['legal_entity_id'];
            $leWhId = $data['leWhId'];
            
            if(empty($query))   $emptySearchQuery;
        } catch (\Exception $e) {
            return ['status' => "failed", 'message' => "Invalid Paramters", "full_message" => $e->getMessage()];
        }

        $tokenResult = $this->token->checkCustomerToken($token);
        if($tokenResult <= 0)
            return ['status' => "session", 'message' => "Invalid Customer Token"];

        try {
            // Query to Get Brands
            $brands = $this->searchAllBrandsByQuery($query,$limit,$leWhId);
           
            // Query to Get Only Products
            $products = $this->searchAllProductsByQuery($query,$limit,$leWhId);

            // Query to Get Categories
            $categories = $this->searchAllCategoriesByQuery($query,$limit,$leWhId);

            $productslist = array_merge($brands,$categories,$products);

            return ["status"=>"success","message" => "Search Result","data" => ["data" => $productslist]];
        } catch (\Exception $e) {
            return ["status"=>"failed","message" => "Invalid Product Search","full_message" => $e->getMessage()];
        }
    }
    public function getProductsByDCId($le_wh_id=0)
    {
        $productsList = DB::table('products as pro')
                        ->join('inventory as inv','pro.product_id','=','inv.product_id')
                        ->join('product_cpenabled_dcfcwise as pcdfw','pcdfw.product_id','=','pro.product_id')
            ->select('pro.product_id','product_title','pcdfw.is_sellable','pcdfw.cp_enabled','is_parent','thumbnail_image','mrp','pcdfw.esu',DB::raw("IFNULL(getProductEsp_wh(pro.product_id,".$le_wh_id."),0) as esp"),DB::raw("IFNULL(GetCPInventoryByProductId(pro.product_id,".$le_wh_id."),0) as inventory"))
            ->where('pcdfw.cp_enabled', '=', 1)
            ->where('pcdfw.is_sellable', '=', 1)
            ->whereRaw('FIND_IN_SET(pcdfw.le_wh_id,' . $le_wh_id . ')')
            ->where([
                //['pro.legal_entity_id','=',$legalEntityId],
               // ['pro.is_parent','=',1],
                //['pro.is_active','=',1],
                //['pro.status','=',1],
                ['pro.product_type_id','=', 130002],
                ['kvi','<>',69010],
                ['inv.le_wh_id', $le_wh_id]
            ])->whereRaw("GetCPInventoryByProductId(pro.product_id,".$le_wh_id.") > 0")
            ->orderBy('pro.product_id','desc');
        
        // Returns Eloquent Object, not the data, you need 
        // to use get() to generate data for the above query
        return $productsList;
    }
    public function searchAllProductsByQuery($query,$limit,$leWhId)
    {
        return
            DB::table('products')
                ->select('products.product_id','product_title AS name')
                ->leftjoin('inventory','inventory.product_id','=','products.product_id')
                ->where([
                    ['products.product_title','like','%'.$query.'%'],
                    //['products.is_parent','=',1],
                    //['products.is_active','=',1],
                    //['products.status','=',1],
                    ['products.product_type_id','=', 130002],
                    ['kvi','<>',69010],
                    ['inventory.le_wh_id','=',$leWhId]])
                ->orderBy('products.product_id','desc')
                ->limit($limit)
                ->get()->all();
    }

    public function searchAllBrandsByQuery($query,$limit,$leWhId)
    {

        $result = DB::select("SELECT 
              b.`brand_id`,b.`brand_name` AS name 
            FROM
              inventory i 
              INNER JOIN products p 
                ON i.`product_id`=p.`product_id` 
              INNER JOIN brands b 
                ON b.`brand_id` = p.`brand_id` 
            WHERE i.`le_wh_id` = $leWhId 
              AND b.`brand_name` like '%$query%' GROUP BY p.`brand_id` limit ".$limit);
        
        return $result;
            
    }

    public function searchAllCategoriesByQuery($query,$limit,$leWhId)
    {

        $result = DB::select("SELECT 
            c.`category_id`,c.`cat_name` AS name 
            FROM
              inventory i 
              INNER JOIN products p  
                ON i.`product_id` = p.`product_id` 
              INNER JOIN categories c  
                ON c.`category_id` = p.`category_id` 
            WHERE i.`le_wh_id` = $leWhId 
              AND c.`cat_name` like '%$query%' GROUP BY p.`category_id` limit ".$limit);
        
        return $result;
        
            
    }
     

}
