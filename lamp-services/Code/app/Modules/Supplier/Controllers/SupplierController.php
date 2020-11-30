<?php

namespace App\Modules\Supplier\Controllers;
use App\Modules\Roles\Models\Role;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\CustomerRepo;
use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use File;
use Illuminate\Support\Facades\Mail;
use Redirect;
use App\Modules\Product\Controllers\ProductController;
use App\Modules\Supplier\Models\ProductModel;
use Response;
use App\Modules\Supplier\Models\ServiceProvider;
use App\Modules\Supplier\Models\HrProvider;
use App\Modules\Supplier\Models\Space;
use App\Modules\Supplier\Models\SpaceProvider;
use App\Modules\Supplier\Models\VehicleService;
use App\Modules\Supplier\Models\Vehicle;
use App\Modules\Supplier\Models\SuppliertermsModel;
use Illuminate\Contracts\Filesystem\Filesystem;
use App\Modules\Supplier\Models\PurchasePriceHistory;
use App\Modules\Supplier\Models\BrandModel;
use App\Modules\Supplier\Models\VwManagesuppliesModel;
use App\Modules\Supplier\Models\VwManageHrProvidersModel;
use App\Modules\Supplier\Models\VwManageServiceModel;
use App\Modules\Supplier\Models\VwManageVehicleModel;
use App\Modules\Supplier\Models\VwManageVehProvidersModel;
use App\Modules\Supplier\Models\VwManageSpaceModel;
use App\Modules\Supplier\Models\VwManageSpaceProModel;
use App\Modules\Supplier\Models\VwBrandwiseDetailsModel;
use App\Modules\Supplier\Models\VwProductsMarginModel;
use App\Modules\Supplier\Models\countries;
use App\Modules\Supplier\Models\ZoneModel;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Modules\Supplier\Models\Legalentities;
use App\Modules\Supplier\Models\Suppliers;
use App\Modules\Supplier\Models\Users;
use App\Modules\Supplier\Models\Userroles;
use App\Modules\Supplier\Models\SupplierModel;
use App\Modules\Supplier\Models\Documentsmaster;
use App\Modules\Supplier\Models\Legalentitydocs;
use App\Modules\Supplier\Models\SupplierWarehouseModel;
use App\models\MasterLookup\MasterLookup;
use App\Modules\Supplier\Models\SupplierwhMappingModel;
use App\Modules\Product\Models\ProductContent;
use App\Modules\Product\Models\ProductSlabRates;
use App\Modules\Product\Models\ProductMedia;
use App\Modules\Product\Models\ProductInventory;
use App\Modules\Product\Models\ProductPolicies;
use App\Modules\Product\Models\ProductAttributes;
use App\Modules\Product\Models\ProductRelations;
use App\Modules\Product\Models\ProductTOT;
use App\Central\Repositories\ProductRepo;
use App\Modules\Supplier\Models\CategoriesModel;
use App\Modules\Supplier\Models\TotModel;
use Utility;
use App\Modules\Orders\Controllers\OrdersGridController;
use \App\Modules\Reports\Controllers\ReportsController;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use PDO;

class SupplierController extends BaseController {

    public $atp_peyiod;
	public $inventory_data;
	public $legalentity_warehouses;
	public $kvi;
	public $uom_data;
	public $moq_data;
	public $tax_types;
	public $suppliers_dcrealtionship;
	public $getpayment_days;
	public $suppliers_rank;
	public $suppliers_types;
	public $rtv_scope;
	public $negotiation_data;
	public $rtvloc_data;
	public $states_data;
	public $countries_data;
	public $currency_data;
	public $company_data;
	public $account_data;
	public $rm_data;
	public $returns_location_types;
	public $brands_data;
	public $categories;
	public $inventory_types;
	public $margin_types;
    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                $legal_entity_id = Session::get('legal_entity_id'); 
                $loggedin_user_id = Session::get('userId');
                $leWhObj = $this->_supplierModel->getWarehouseBylegalentity($legal_entity_id);
                $this->legalentity_warehouses = json_decode(json_encode($leWhObj), true);
                $this->rm_data = $this->_supplierModel->getRm(null);
                return $next($request);
            });

            //For Raback
            $this->_roleRepo = new RoleRepo();
            $this->_supplierModel = new SupplierModel();
            $this->_orders = new OrdersGridController();
            $this->reports = new ReportsController;
            
        if(isset($_SERVER["REQUEST_URI"]))
        {
        $referer = $_SERVER["REQUEST_URI"];
        $urlArray = explode('/',$referer);
        $is_provider  = isset($urlArray[1])?$urlArray[1]:'';
        }
        switch($is_provider)
        {
            case 'vehicleproviders':  $vendor = 'Vehicle Provider';break;
            case 'humanresource': $vendor = 'Human Resource Provider'; break;
            case 'vehicle': $vendor = 'Vehicle'; break;
            case 'serviceproviders': $vendor = 'Service Provider'; break;
            case 'space': $vendor = 'Space'; break;
            case 'spaceprovider': $vendor = 'Space Provider'; break;
            default: $vendor = 'Supplier'; break;
        } 
        parent::Title('Manage '.$vendor.' - Ebutor');

            $this->tax_types = $this->_supplierModel->gettaxtypes();
            $this->suppliers_dcrealtionship = $this->_supplierModel->getSuppliersDCrealtionship();
            $this->moq_data = $this->_supplierModel->getWeightUoM();
            $this->uom_data = $this->_supplierModel->getTatUom();
            $this->kvi = $this->_supplierModel->getkvi();
            $this->inventory_data = $this->_supplierModel->getInventorymode();
            $this->atp_peyiod = $this->_supplierModel->getatppyriod();
            $this->getpayment_days = $this->_supplierModel->getPaymentDays();
            $this->suppliers_rank = $this->_supplierModel->getSuppliersRank(); //print_r(suppliers_rank);
            $this->suppliers_types = $this->_supplierModel->getSupplierstype();//print_r($this->suppliers_types);
            $this->rtv_scope = $this->_supplierModel->getRtvScope();
            $this->negotiation_data = $this->_supplierModel->getNegotiation();
            $this->rtvloc_data = $this->_supplierModel->getRtvlocation();
            $this->states_data = $this->_supplierModel->getStates(); //print_r($this->states_data);
            $this->countries_data = $this->_supplierModel->getCountries();
            $this->currency_data = $this->_supplierModel->getCurrency();
            //$this->company_data = $this->_supplierModel->getCompanytype(); //print_r($this->company_data);    die;    
            $this->account_data = $this->_supplierModel->getAccounttype();
            
            $this->returns_location_types = $this->_supplierModel->returnsLocationType();
            $this->brands_data = $this->_supplierModel->returnLegalentityAllBrands();
            $this->categories = $this->_supplierModel->categoriesList();
            $this->inventory_types = $this->_supplierModel->inventoryType();
            $this->margin_types = $this->_supplierModel->marginType();
            //$this->_atp_peyiod = $this->_supplierModel->getatppyriod();
            //Please fill the grid filed name along with db table field name example 'gridid' => 'table.fieldname'

            $this->grid_field_db_match = array(
                'ProductID' => 'products.product_id',
                //'BrandName' => 'brands.brand_name',
                'Category' => 'categories.cat_name',
                'ProductName' => 'products.product_title',
                'upc' => 'products.upc',
                'MRP' => 'product_tot.mrp',
                'MSP' => 'product_tot.msp',
                'Bestprice' => 'product_tot.base_price',
                'VAT' => 'product_tot.vat',
                'CST' => 'product_tot.cst',
                'EBP' => 'product_tot.cst',
                'RBP' => 'product_tot.cst',
                'CBP' => 'product_tot.cst',
                'InventoryMode' => 'product_tot.inventory_mode',
                'TotStatus' => 'product_tot.is_active',
                'Status' => 'products.is_active',
                //'sku' => 'products.sku',
                'BrandID' => 'brands.brand_id',
                'Description'         => 'brands.description',
                'Authorized'          => 'brands.is_authorized',
                'Trademark'           => 'brands.is_trademark',
                'BrandName'           => 'brands.brand_name',
                'le_code'             => 'le_code',
                //'ProductName'         =>'p.product_title',
                'ProductName'         => 'pt.product_name',
                'ELP'                 => 'pt.dlp',                                
                'Bestprice'           => 'pt.base_price',                
                'TaxType'             => 'pt.tax_type',                
                'Tax'                 => 'pt.tax',                
                'PTR'                 => 'pt.rlp',                
                'ATP'                 => 'pt.atp',
                //'sku'                 => 'p.sku'                
            );      
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }



               
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $rolesObj= new Role();
        if (!Session::has('userId')) {
            return Redirect::to('/');
        }
        
        if(isset($_SERVER["REQUEST_URI"]))
        {
        $referer = $_SERVER["REQUEST_URI"];
        $urlArray = explode('/',$referer);
        $is_provider  = isset($urlArray[1])?$urlArray[1]:'';
        }
        switch($is_provider)
        {
            case 'vehicleproviders':  $vendor = 'Vehicle Provider'; $cat_id = 47; $businessTypeId = array('47001'); break;
            case 'humanresource': $vendor = 'Human Resource Provider'; $cat_id = 47; $businessTypeId = array('47001'); break;
            case 'vehicle': $vendor = 'Vehicle'; $cat_id = 133; $businessTypeId = array('133002'); break;
            case 'serviceproviders': $vendor = 'Service Provider'; $cat_id = 134; $businessTypeId = array('134001','134002','134003'); break;
            case 'space': $vendor = 'Space'; $cat_id = 135; $businessTypeId = array('135001','135002'); break;
            case 'spaceprovider': $vendor = 'Space Provider'; $cat_id = 47; $businessTypeId = array('47001'); break;
            default: $vendor = 'Supplier'; $cat_id = 47; $businessTypeId = array('47001'); break;
        }        
        $breadCrumbs = array('Home' => url('/'),   $vendor => url('/') . '/'.$is_provider, 'Add' => '#');
        parent::Breadcrumbs($breadCrumbs);
        parent::Title('Create '.$vendor);
        Session::forget('legalentity_id');
        Session::forget('supplier_id');
        Session::forget('add_continue');
        $countries = countries::all();
        $countries = json_decode(json_encode($countries), true);        
        $DataFilter= $rolesObj->getFilterData(11, Session::get('userId'));
        $manufacturerList = json_decode($DataFilter,true);
        $manufacturerList = $manufacturerList['manufacturer'];
		
        $states = ZoneModel::all();
        $states = json_decode(json_encode($states), true);

        $legal_entity_id = Session::get('legal_entity_id');        
        $brands = BrandModel::where('legal_entity_id', $legal_entity_id)->get()->all();        
        $cat = DB::Table('categories')->get()->all();
        $parent_Cat = DB::table('categories')->where('categories.parent_id', '!=', 0)->where('is_product_class', '=', 0)->get()->all();
        $product_class = DB::table('categories')->where('categories.parent_id', '!=', 0)->where('is_product_class', '=', 1)->get()->all();
        $suppliers_data = (object) array();
        $logo = (object) array();        
        //$docTypes = Documentsmaster::where(['business_type_id'=>'47001','country'=>'99'])->pluck('doc_no');
        $docTypes = Documentsmaster::whereIn('business_type_id',$businessTypeId)->pluck('doc_no');
        $veh_lbh_uom = DB::table('master_lookup')->where('mas_cat_id',12)->select('value','master_lookup_name as name')->get()->all();   
        $veh_weight_uom = DB::table('master_lookup')->where('mas_cat_id',86)->select('value','master_lookup_name as name')->get()->all();
        $body_type = DB::table('master_lookup')->where('mas_cat_id',159)->select('value','master_lookup_name as name')->get()->all();
        $vehicle_type = DB::table('master_lookup')->where('mas_cat_id',156)->select('value','master_lookup_name as name')->first();
        $vehicle_type = json_decode(json_encode($vehicle_type), true);
        $vehicle_models = DB::table('master_lookup')->where('mas_cat_id',171)->select('value','master_lookup_name as name')->get()->all(); 
        $driver_contact = DB::table('users as u')->select('ur.user_roles_id as user_roles_id','u.user_id as user_id',DB::raw('CONCAT(u.firstname," ",u.lastname) as "fullname"'))->join('user_roles as ur', 'ur.user_id','=', 'u.user_id')->where('ur.role_id',77)->get()->all(); 
        $veh_provider = DB::table('legal_entities')
                ->leftJoin('vehicle_provider','vehicle_provider.legal_entity_id','=','legal_entities.legal_entity_id')->where('legal_entity_type_id',1009)->where('is_active',1)->select('legal_entities.legal_entity_id as value','business_legal_name as name')->get()->all();

        $space_provider = DB::table('legal_entities')
                ->leftJoin('space_provider','space_provider.legal_entity_id','=','legal_entities.legal_entity_id')->where('legal_entity_type_id',1012)->where('is_active',1)->select('legal_entities.legal_entity_id as value','business_legal_name as name')->get()->all();
        $supplierModel = new SupplierModel();
        $hub_list = $supplierModel->getHubs();    
        $company_data = $supplierModel->getCompanytype($cat_id);
        $space_data = array();
        return View::make('Supplier::suppliers', ['logo' => $logo, 'supplier_data' => $suppliers_data, 'category_list' => $this->categories, 
            'brands' => $brands, 'margin_types_list' => $this->margin_types, 'returns_location_types_list' => $this->returns_location_types, 
            'inventory_types_list' => $this->inventory_types, 'states_data' => $this->states_data, 'states' => $states, 'countries_data' => $this->countries_data, 
            'countries' => $countries, 'rm_data' => $this->rm_data, 'currency_data' => $this->currency_data, 'brands_data' => $this->brands_data, 
            'company_data' => $company_data, 'account_data' => $this->account_data, 'cat' => $cat, 'parent_id' => $parent_Cat, 
            'product_class' => $product_class, 'legalentity_warehouses' => $this->legalentity_warehouses, 'inventory_data' => $this->inventory_data, 
            'suppliers_types' => $this->suppliers_types, 'uom_data' => $this->uom_data, 'rtv_scope' => $this->rtv_scope, 'negotiation_data' => $this->negotiation_data, 
            'rtvloc_data' => $this->rtvloc_data, 'getpayment_days' => $this->getpayment_days, 'suppliers_rank' => $this->suppliers_rank, 
            'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 'kvi' => $this->kvi, 'moq_data' => $this->moq_data, 'tax_types' => $this->tax_types, 
            'atp_peyiod' => $this->atp_peyiod, 'manufacturerList' => $manufacturerList,'docTypes'=>$docTypes,'docsArr'=>array(),'po_days'=>'','invoice_days'=>'','negotiation'=>'',
            'is_provider'=>$is_provider,'vendor'=>$vendor,'veh_lbh_uom'=>$veh_lbh_uom,'veh_weight_uom'=>$veh_weight_uom,'body_type'=>$body_type,'vehicle_type'=>$vehicle_type,'vehicle_models'=>$vehicle_models,'veh_provider'=>$veh_provider,'hub_list'=>$hub_list,'driver_contact'=>$driver_contact,'responseSource'=>"add",
            'space_provider'=>$space_provider,'space_data'=>$space_data]);
    }

    public function gridCountArray(){
        $count_array = array();        
        $legal_entity_id = Session::get('legal_entity_id');
        $rback = new Role;
        $sm = new VwManagesuppliesModel();
        $vehiclePr = new VwManageVehProvidersModel();
        $rmUserList = $rback->getFilterData(5);
        $rmUserListObj = json_decode($rmUserList);
        $rnUserListArr = [];
        if($rmUserListObj->supplier)
        {
            $rnUserListArr = $rmUserListObj->supplier;
        }

        if ($legal_entity_id == 0) {
            $query = $sm::select(DB::raw('count(*) as supplier_count'));
            $provider = $vehiclePr::select(DB::raw('count(*) as vehicleprovider'));
        } else {
            $query = $sm::select(DB::raw('count(*) as supplier_count'))
                ->where('parent_le_entity_id', $legal_entity_id)
                ->whereIn('rel_manager_id',$rnUserListArr);

            $providerQuery = $vehiclePr::select(DB::raw('count(*) as vehicleprovider'))
                ->where('parent_le_entity_id', $legal_entity_id)
                ->whereIn('rel_manager_id',$rnUserListArr);
        }
        $role = new Role();
        $sbuData = json_decode($role->getFilterData(6), 1);
        $sbu = isset($sbuData['sbu']) ? $sbuData['sbu'] : 0;
        $hubsData = json_decode($sbu, 1);
        $hubs = (isset($hubsData['118002'])) ? $hubsData['118002'] : '';
        $hubs = explode(',',$hubs);

        $vehiclequery = DB::table('vehicle')
                ->leftjoin('legal_entities as le_veh','le_veh.legal_entity_id','=','vehicle.legal_entity_id')
                ->where('le_veh.legal_entity_type_id',1008)
                ->whereIn('vehicle.hub_id',$hubs)
                ->select(DB::raw('count(*) as count'));
        $query = $query->get()->all();
        $vehiclequery = $vehiclequery->get()->all();
        $providerQuery = $providerQuery->get()->all();
        $query = json_decode(json_encode($query), true);
        $vehiclequery = json_decode(json_encode($vehiclequery), true);
        $providerQuery = json_decode(json_encode($providerQuery), true);
          array_push($count_array,array("supplier"=>$query[0]));
          array_push($count_array, array("vehicle"=>$vehiclequery[0]['count']));
          array_push($count_array, array("providerQuery"=>$providerQuery[0]));
          return $count_array;
    }
    public function suppliersList() {
        if (!Session::has('userId')) {
            return Redirect::to('/');
        }
		Session::forget('add_continue');
        $brands = BrandModel::all();
        $brands = json_decode(json_encode($brands), true);

        $breadCrumbs = array('Home' => url('/'),   'Suppliers' => '#');
        parent::Breadcrumbs($breadCrumbs);
        $addSuppliers = $this->_roleRepo->checkPermissionByFeatureCode('SUP002');
        $vendorexport = $this->_roleRepo->checkPermissionByFeatureCode('EVP01');
	$suppliersGridFilters = $this->_roleRepo->checkPermissionByFeatureCode('SLF001'); 
        $status = 'suppliers';
        $leCounts = DB::table('legal_entities')->select('legal_entity_type_id',DB::RAW('COUNT(legal_entity_type_id) AS COUNT'))->groupBy('legal_entity_type_id')->get()->all();
        $counts = $this->gridCountArray();
    $title = 'Manage Suppliers';
    $gridTableId = 'supplier_list_grid';
    $buttonName = 'Add New Supplier';
    $addUrl = 'suppliers/add';
     $suppliers = DB::select(DB::raw("select user_name,legal_entity_id,le_code from vw_managesupplies"));
     $suppliers = json_decode(json_encode($suppliers),1);

    return View::make('Supplier::supplierslist', ['suppliers' => $suppliers,'atp_peyiod' => $this->atp_peyiod,'inventory_data'  => $this->inventory_data,
        'legalentity_warehouses' => $this->legalentity_warehouses,'kvi' => $this->kvi, 'add_suppliers' => $addSuppliers,'vendorexport' => $vendorexport,
        'uom_data' => $this->uom_data, 'moq_data' => $this->moq_data, 'tax_types' => $this->tax_types, 'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 
        'category_list' => $this->categories, 'brands' => $brands, 'brands_data' => $this->brands_data, 'returns_location_types_list' => $this->returns_location_types,'status'=>$status,'le_counts'=>$leCounts,'page_title'=>$title,'grid_table_id'=>$gridTableId,'button_name'=>$buttonName,'add_url'=>$addUrl, 'suppliers_grid_filters' => $suppliersGridFilters,'counts'=>$counts]);

    }
    public function downloadVendorExcel() {
        try{
            $mytime = Carbon::now();
            $filterData = Input::get();
            $id = $filterData['supplier_id'];
            $supplier = DB::table('legal_entities')->where('legal_entity_id',$id)->select('business_legal_name','le_code')->get()->all();
            $supplier_id = $filterData['supplier_id'];
            $start_date = (isset($filterData['start_date']) && !empty($filterData['start_date'])) ? $filterData['start_date'] : date('Y-m').'-01';
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = (isset($filterData['end_date']) && !empty($filterData['end_date'])) ? $filterData['end_date'] : date('Y-m-d');
            $end_date = date('Y-m-d', strtotime($end_date));
            $params = [$supplier_id,"'".$start_date."'","'".$end_date."'"];
            $details =$this->CallRaw('get_supplier_payment_details',$params);
            $details = json_decode(json_encode($details),1);
            $exceldata_first['supplier_name'] = $supplier[0]->business_legal_name;
            $exceldata_first['supplier_code'] = $supplier[0]->le_code;
            $exceldata_first['start_date'] = $start_date;
            $exceldata_first['end_date'] = $end_date;
            Excel::create('Vendor Payments Sheet-'.$mytime->toDateTimeString(), function($excel) use($exceldata_first, $details) 
            {
                $excel->sheet("Paynents Data", function($sheet) use($exceldata_first, $details)
                {
                    $sheet->setWidth(array('A'=>15,'B' => 20,'C' => 15,'D' => 20,'E' => 20,'F' => 20,'G' => 18,'H' => 18,'I' => 12,'J' => 12));
                    $sheet->loadView('Supplier::vendorPaymentsTemplate', array('exceldata_first' => $exceldata_first,'opening_balance'=>isset($details[0][0]['opening_balance'])?$details[0][0]['opening_balance']:0,'details' => isset($details[1])?$details[1]:[]));
                });
            })->export('xlsx');
        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    // public function odersTabGetBuUnit(){
    //     return $this->_Inventory->businessTreeData();
    // }
    public function editAction($Supplier_id,Request $request) {
        if (!Session::has('userId')) {
            return Redirect::to('/');
        }
        if(isset($_SERVER["REQUEST_URI"]))
        {
        $referer = $_SERVER["REQUEST_URI"];
        $urlArray = explode('/',$referer);
        $is_provider  = isset($urlArray[1])?$urlArray[1]:'';
        }
        switch($is_provider)
        {
            case 'vehicleproviders':  $vendor = 'Vehicle Provider'; $table = 'vehicle_provider'; $cat_id = 47;break;
            case 'humanresource': $vendor = 'Human Resource Provider'; $table = 'hr_provider'; $cat_id = 47; break;
            case 'vehicle': $vendor = 'Vehicle';  $table = 'vehicle'; $cat_id = 133; 
            $vehicle_id = $Supplier_id;
            $sup = DB::table('vehicle')->where('vehicle_id',$vehicle_id)->select('legal_entity_id')->first();
            $Supplier_id = $sup->legal_entity_id;
            break;
            case 'serviceproviders': $vendor = 'Service Provider';  $table = 'service_provider'; $cat_id = 134; break;
            case 'space': $vendor = 'Space';  $table = 'space'; $cat_id = 135; break;
            case 'spaceprovider': $vendor = 'Space Provider';  $table = 'space_provider'; $cat_id = 47; break;
            default: $vendor = 'Supplier'; $table = 'suppliers'; $cat_id = 47; break;
        } 
        $supllier_terms = null;
        $rolesObj= new Role();
        $approveCode = 'SUP004';
        $rejectCode = 'SUP005';
		$urlArray = explode('/', $request->fullUrl());
        $actionName = ucfirst($urlArray[4]);
        $breadCrumbs = array('Home' => url('/'),   $vendor => url('/') . '/'.$is_provider, $actionName => '#');
        parent::Breadcrumbs($breadCrumbs);
        Session::forget('warehouse_id');
        //Session::set('vehicle_id', $vehicle_id);
        //$Suppliers = new Suppliers();
        $Legalentities = new Legalentities();
        $roleRepo = new RoleRepo();
        $userId = Session::get('userId');
        $approveAccess = $roleRepo->checkActionAccess($userId, $approveCode);
        $rejectAccess = $roleRepo->checkActionAccess($userId, $rejectCode);
        $DataFilter= $rolesObj->getFilterData(11, Session::get('userId'));
        $manufacturerList = json_decode($DataFilter,true);
        $manufacturerList = $manufacturerList['manufacturer'];
        
        $countries = countries::all();
        $countries = json_decode(json_encode($countries), true);
        $states = ZoneModel::all();
        $states = json_decode(json_encode($states), true);
		$checkNewUser = DB::select(DB::raw('select COUNT(user_id) AS count  from users where users.legal_entity_id = '.$Supplier_id.''));
		$legalType = DB::select(DB::raw('select legal_entity_type_id from legal_entities where legal_entity_id = '.$Supplier_id.''));
        //$vehicle_id = DB::select(DB::raw('select vehicle_id from vehicle where legal_entity_id = '.$Supplier_id.''));
        $leftjoin = 'sp.legal_entity_id';
        $leTypeId = isset($legalType[0]->legal_entity_type_id)?$legalType[0]->legal_entity_type_id:0;
        if($checkNewUser[0]->count == 0 && $table == "vehicle"){
            $leftjoin = 'sp.driver_le_id';
        }

        $leftjoinCheck = ($leTypeId==1013)?'sp.driver_le_id':'sp.legal_entity_id';
        if($checkNewUser[0]->count == 0 || $table != "vehicle"){
            $leftjoinCheck = 'sp.legal_entity_id';
        }
        //get suppllier wise data and set legal entity in sessions
        $table = $table.' as sp';
        $supllier_data = DB::table($table)
                        ->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'sp.legal_entity_id')
                        ->leftJoin('users', 'users.legal_entity_id', '=', $leftjoin)
                        ->where($leftjoinCheck, $Supplier_id)->first();
        $supllier_data->driver_contact = isset($supllier_data->user_id)?$supllier_data->user_id:0;
        $le_type = DB::table('legal_entities')->where('legal_entity_id',$Supplier_id)->pluck('legal_entity_type_id');
        $le_type_id = (isset($le_type[0]))?$le_type[0]:0;

        $vehicle_data = DB::table('vehicle as sp')->where($leftjoinCheck,$Supplier_id)->select('veh_provider','body_type','vehicle_type','reg_no','driver_le_id','reg_exp_date','length',
                'breadth','height','veh_lbh_uom','license_no','license_exp_date','veh_weight','veh_weight_uom','insurance_no','insurance_exp_date','hub_id',
                'fit_exp_date','poll_exp_date','safty_exp_date','batch_number')->first();

       if($is_provider == "vehicle"){
        $driver_le_id = $vehicle_data->driver_le_id;
        $driver_data = DB::table('drivers')->where('legal_entity_id',$driver_le_id)->select('driving_license_no','license_exp_date')->first();
        $driver_le_data = DB::table('legal_entities')->where('legal_entity_id',$driver_le_id)->select('address1','address2','city','state_id','country','pincode')->first();
        //echo '<pre/>';print_r($driver_le_data);die;
        if(isset($driver_le_data) && count($driver_le_data)>0){
            $supllier_data->address1 = $driver_le_data->address1;
            $supllier_data->address2 = $driver_le_data->address2;
            $supllier_data->city = $driver_le_data->city;
            $supllier_data->state_id = $driver_le_data->state_id;
            $supllier_data->country = $driver_le_data->country;
            $supllier_data->pincode = $driver_le_data->pincode;
        }
    }
      

        if(!empty($driver_data) && empty($vehicle_data->license_no)){
            $vehicle_data->license_no = $driver_data->driving_license_no;
           // echo "hello"; print_r($vehicle_data_license);die();
            $vehicle_data->license_exp_date = $driver_data->license_exp_date;
        }
        $space_data = DB::table('space')->where('legal_entity_id',$Supplier_id)->select('space_provider','hub_id','area')->first();

		if($actionName != 'Approval')				
                parent::Title('Edit '.$vendor.' - '.$supllier_data->business_legal_name);
		else
		parent::Title($vendor.' Approval- '.$supllier_data->business_legal_name);	
        
        $businessTypeId = $supllier_data->business_type_id;
        $countryId = $supllier_data->country;        
        $docTypes = Documentsmaster::where(['business_type_id'=>$businessTypeId,'country'=>$countryId])->pluck('doc_no');

        $supllier_terms = DB::table('aggrement_terms')
                        ->where(['legal_entity_id'=>$Supplier_id,'le_type_id'=>$le_type_id])->first();
 
        Session::forget('recent_supplier_edit');	
        Session::put('recent_supplier_edit',$supllier_data->legal_entity_id);
        //$docsArr = Legalentitydocs::where('legal_entity_id', $Supplier_id)->get()->all();
		$docsArr = DB::table('legal_entity_docs')->select('doc_type','reference_no',DB::Raw('GetUserName(created_by,2) as created_by'),'doc_url','doc_id')->where('legal_entity_id', $Supplier_id)->get()->all();
        $base_path = 'uploads/Suppliers_Docs/';

        Session::put('legalentity_id', $supllier_data->legal_entity_id);
        Session::put('supplier_id', $Supplier_id);
        $sesLi = session('legalentity_id');
        $sp = session('supplier_id');
        $legal_entity_id = Session::get('legal_entity_id');        
        $brands = BrandModel::where('legal_entity_id', $legal_entity_id)->get()->all();
        $brands = json_decode(json_encode($brands), true);        
        $supplierModel = new SupplierModel();
        $userId = Session::get('userId');

        $cat = DB::Table('categories')->get()->all();
        $parent_Cat = DB::table('categories')->where('categories.parent_id', '!=', 0)->where('is_product_class', '=', 0)->get()->all();
        $product_class = DB::table('categories')->where('categories.parent_id', '!=', 0)->where('is_product_class', '=', 1)->get()->all();
        $upload_product_template  = 1;// $this->_roleRepo->checkPermissionByFeatureCode('SUP012');
        $add_product              = 1;//$this->_roleRepo->checkPermissionByFeatureCode('SUP013');
		
        $history = $this->_supplierModel->getApprovalHistory('Supplier',$Supplier_id);
		if($supllier_terms && $supllier_terms->po_days)
        $poDays = $supllier_terms->po_days;
		else
		$poDays = null;
	
		if($supllier_terms && $supllier_terms->invoice_days)
		$invoiceDays = $supllier_terms->invoice_days;
		else
			$invoiceDays = null;
		
		if($supllier_terms && $supllier_terms->negotiation)
		$negotiation = $supllier_terms->negotiation;
		else
			$negotiation = null;
                
        $veh_lbh_uom = DB::table('master_lookup')->where('mas_cat_id',12)->select('value','master_lookup_name as name')->get()->all();   
        $veh_weight_uom = DB::table('master_lookup')->where('mas_cat_id',86)->select('value','master_lookup_name as name')->get()->all(); 
        $body_type= DB::table('master_lookup')->where('mas_cat_id',159)->select('value','master_lookup_name as name')->get()->all(); 
        $vehicle_type = DB::table('master_lookup')->where('mas_cat_id',156)->select('value','master_lookup_name as name')->first();
        $vehicle_type = json_decode(json_encode($vehicle_type), true);
        $vehicle_models= DB::table('master_lookup')->where('mas_cat_id',171)->select('value','master_lookup_name as name')->get()->all(); 
        $driver_contact = DB::table('users as u')->select('ur.user_roles_id as user_roles_id','u.user_id as user_id',DB::raw('CONCAT(u.firstname," ",u.lastname) as "fullname"'))->join('user_roles as ur', 'ur.user_id','=', 'u.user_id')->where('ur.role_id',77)->get()->all();
        $veh_provider = DB::table('legal_entities')
                ->leftJoin('vehicle_provider','vehicle_provider.legal_entity_id','=','legal_entities.legal_entity_id')->where('legal_entity_type_id',1009)->where('is_active',1)->select('legal_entities.legal_entity_id as value','business_legal_name as name')->get()->all();
        $space_provider = DB::table('legal_entities')
                ->leftJoin('space_provider','space_provider.legal_entity_id','=','legal_entities.legal_entity_id')->where('legal_entity_type_id',1012)->where('is_active',1)->select('legal_entities.legal_entity_id as value','business_legal_name as name')->get()->all();
        $company_data =   $supplierModel->getCompanytype($cat_id); 
        $hub_list =  $supplierModel->getHubs();          
        $erp_code = DB::table('legal_entities')->where('legal_entity_id', $Supplier_id)->pluck('le_code');
        $supplierDataArray = ['manufacturerList' => $manufacturerList, 'upload_product_template' => $upload_product_template, 
            'add_product' => $add_product, 'atp_peyiod' => $this->atp_peyiod,'moq_data' => $this->moq_data, 'kvi' => $this->kvi, 'rtvloc_data' => $this->rtvloc_data, 
            'negotiation_data' => $this->negotiation_data, 'rtv_scope' => $this->rtv_scope, 'uom_data' => $this->uom_data, 'supllier_terms' => $supllier_terms, 
            'suppliers_types' => $this->suppliers_types, 'category_list' => $this->categories, 'brands' => $brands, 'margin_types_list' => $this->margin_types, 
            'returns_location_types_list' => $this->returns_location_types, 'inventory_types_list' => $this->inventory_types, 'states_data' => $this->states_data, 
            'countries_data' => $this->countries_data, 'states' => $states, 'countries' => $countries, 'brands_data' => $this->brands_data, 'rm_data' => $this->rm_data, 
            'currency_data' => $this->currency_data, 'company_data' => $company_data, 'account_data' => $this->account_data, 'supplier_data' => $supllier_data, 
            'cat' => $cat, 'parent_id' => $parent_Cat,'product_class' => $product_class, 'legalentity_warehouses' => $this->legalentity_warehouses, 'inventory_data' => $this->inventory_data, 
            'getpayment_days' => $this->getpayment_days, 'suppliers_rank' => $this->suppliers_rank, 'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 
            'tax_types' => $this->tax_types,'history'=>$history,'docsArr'=>$docsArr,'docTypes'=>$docTypes,'po_days'=>$poDays,
            'invoice_days'=>$invoiceDays,'negotiation'=>$negotiation,'erp_code'=>$erp_code[0],'vendor'=>$vendor,'vehicle_data'=>$vehicle_data,'veh_lbh_uom'=>$veh_lbh_uom,
            'veh_weight_uom'=>$veh_weight_uom,'body_type'=>$body_type,'vehicle_type'=>$vehicle_type,'vehicle_models'=>$vehicle_models,'veh_provider'=>$veh_provider,'hub_list'=>$hub_list,'space_data'=>$space_data,'driver_contact'=>$driver_contact,'responseSource'=>"edit",'space_provider'=>$space_provider];
			
		if($actionName == 'Approval')
		{
			$supplierDataArray['approval'] = 1;
						
					
						
		}
        $supplierDataArray['leId']=$Supplier_id;
        return View::make('Supplier::suppliers', $supplierDataArray);
    }

    function approvalAction($Supplier_id) {
        $rolesObj= new Role();
        $approveCode = 'SUP004';
        $rejectCode = 'SUP005';
        $breadCrumbs = array('Home' => url('/'), 'Account' => url('/'), 'Suppliers' => url('/') . '/suppliers', 'Edit' => '#');
        parent::Breadcrumbs($breadCrumbs);
		$supllier_terms = null;
        Session::forget('warehouse_id');
        $Suppliers = new Suppliers();
        $Legalentities = new Legalentities();
        $roleRepo = new RoleRepo();
        $userId = Session::get('userId');
        $approveAccess = $roleRepo->checkActionAccess($userId, $approveCode);
        $rejectAccess = $roleRepo->checkActionAccess($userId, $rejectCode);

        $countries = countries::all();
        $countries = json_decode(json_encode($countries), true);

        $states = ZoneModel::all();
        $states = json_decode(json_encode($states), true);
        $suppliers_types = $this->getSupplierstype();
        $uom_data = $this->getTatUom();
        $negotiation_data = $this->getNegotiation();
        $rtv_scope = $this->getRtvScope();
        $rtvloc_data = $this->getRtvlocation();
        $getpayment_days = $this->getPaymentDays();
        $suppliers_rank = $this->getSuppliersRank();
        $suppliers_dcrealtionship = $this->getSuppliersDCrealtionship();
        $kvi = $this->getkvi();
        $moq_data = $this->getWeightUoM();
        $tax_types = $this->gettaxtypes();
        $atp_peyiod = $this->getatppyriod();
        
        $DataFilter= $rolesObj->getFilterData(11, Session::get('userId'));
        $manufacturerList = json_decode($DataFilter,true);
        $manufacturerList = $manufacturerList['manufacturer'];


        //get suppllier wise data and set legal entity in sessions

        $supllier_data = DB::table('suppliers as sp')
                        ->leftjoin('legal_entities as le', 'le.legal_entity_id', '=', 'sp.legal_entity_id')
                        ->leftjoin('users', 'users.legal_entity_id', '=', 'sp.legal_entity_id')
                        ->where('sp.legal_entity_id', $Supplier_id)->first();
		$businessTypeId = $supllier_data->business_type_id;
$countryId = $supllier_data->country; 		
        //Session::forget('supplier_no');	
        //Session::put('supplier_no',$supllier_data->supplier_id);							
        $supplier_docs = DB::table('suppliers as sp')
                        ->join('legal_entity_docs as ld', 'ld.legal_entity_id', '=', 'sp.legal_entity_id')
                        ->where('sp.legal_entity_id', $Supplier_id)->get()->all();
        $pan_det = array('ref_no' => '', 'img' => '');
        $cin_det = array('ref_no' => '', 'img' => '');
        $tinvat_det = array('ref_no' => '', 'img' => '');
        $cst_det = array('ref_no' => '', 'img' => '');
        $cheque_det = array('ref_no' => '', 'img' => '');
        $mou_det = array('ref_no' => '', 'img' => '');
        $base_path = 'uploads/Suppliers_Docs/';
        $supllier_terms = DB::table('supplier_terms')->where('legal_entity_id', $supllier_data->legal_entity_id)->first();
        if (!empty($supplier_docs)) {
            foreach ($supplier_docs as $value) {
                if ($value->doc_type == "PAN") {
                    $pan_det['ref_no'] = $value->reference_no;
                    if (File::exists(public_path($base_path . $value->doc_url))) {
                        $pan_det['img'] = $value->doc_url;
                    } else {
                        $pan_det['img'] = "notfound.png";
                    }
                }
                if ($value->doc_type == "CIN") {
                    $cin_det['ref_no'] = $value->reference_no;
                    if (File::exists(public_path($base_path . $value->doc_url))) {
                        $cin_det['img'] = $value->doc_url;
                    } else {
                        $cin_det['img'] = "notfound.png";
                    }
                }
                if ($value->doc_type == "TINVAT") {
                    $tinvat_det['ref_no'] = $value->reference_no;
                    if (File::exists(public_path($base_path . $value->doc_url))) {
                        $tinvat_det['img'] = $value->doc_url;
                    } else {
                        $tinvat_det['img'] = "notfound.png";
                    }
                }
                if ($value->doc_type == "CST") {
                    $cst_det['ref_no'] = $value->reference_no;
                    if (File::exists(public_path($base_path . $value->doc_url))) {
                        $cst_det['img'] = $value->doc_url;
                    } else {
                        $cst_det['img'] = "notfound.png";
                    }
                }
                if ($value->doc_type == "CHEQUE") {
                    $cheque_det['ref_no'] = $value->reference_no;
                    if (File::exists(public_path($base_path . $value->doc_url))) {
                        $cheque_det['img'] = $value->doc_url;
                    } else {
                        $cheque_det['img'] = "notfound.png";
                    }
                }
                if ($value->doc_type == "MOU") {
                    $mou_det['ref_no'] = $value->reference_no;
                    if (File::exists(public_path($base_path . $value->doc_url))) {
                        $mou_det['img'] = $value->doc_url;
                    } else {
                        $mou_det['img'] = "notfound.png";
                    }
                }
            }
        }
        Session::put('legalentity_id', $supllier_data->legal_entity_id);
        Session::put('supplier_id', $Supplier_id);
        $sesLi = session('legalentity_id');
        $sp = session('supplier_id');

        $states_data = $this->getStates();
        $countries_data = $this->getCountries();
        $rm_data = $this->getRm();
        $currency_data = $this->getCurrency();
        $company_data = $this->getCompanytype();
        $account_data = $this->getAccounttype();
        $brands_data = $this->returnLegalentityAllBrands();


        $returns_location_types = $this->returnsLocationType();
        $categories = $this->categoriesList();
        $inventory_types = $this->inventoryType();
        $margin_types = $this->marginType();

        $brands = BrandModel::all();
        $brands = json_decode(json_encode($brands), true);

        $supplierModel = new SupplierModel();
        $userId = Session::get('userId');
        //$supplierModel->updatedBy($userId, $supllier_data->legal_entity_id, $supllier_data->supplier_id);
		
		if($supllier_terms && $supllier_terms->po_days)
        $poDays = $supllier_terms->po_days;
		else
		$poDays = null;
	
		if($supllier_terms && $supllier_terms->invoice_days)
		$invoiceDays = $supllier_terms->invoice_days;
		else
			$invoiceDays = null;
		
		if($supllier_terms && $supllier_terms->negotiation)
		$negotiation = $supllier_terms->negotiation;
		else
			$negotiation = null;

        $cat = DB::Table('categories')->get()->all();
        $parent_Cat = DB::table('categories')->where('categories.parent_id', '!=', 0)->where('is_product_class', '=', 0)->get()->all();
        $product_class = DB::table('categories')->where('categories.parent_id', '!=', 0)->where('is_product_class', '=', 1)->get()->all();
		$docsArr = Legalentitydocs::where('legal_entity_id', $supllier_data->supplier_id)->get()->all();
		$productObj = new ProductRepo();
		$history = $productObj->getApprovalHistory('suppliers','legal_entity_id',$Supplier_id);
		$docTypes = Documentsmaster::where(['business_type_id'=>$businessTypeId,'country'=>$countryId])->pluck('doc_no');
        return View::make('Supplier::suppliers', ['history'=>$history,'manufacturerList' => $manufacturerList, 'atp_peyiod' => $atp_peyiod,'tax_types' => $tax_types, 'moq_data' => $moq_data, 'kvi' => $kvi, 'suppliers_dcrealtionship' => $suppliers_dcrealtionship, 'getpayment_days' => $getpayment_days, 'rtvloc_data' => $rtvloc_data, 'rtv_scope' => $rtv_scope, 'negotiation_data' => $negotiation_data, 'uom_data' => $uom_data, 'suppliers_types' => $suppliers_types, 'category_list' => $categories, 'brands' => $brands, 'margin_types_list' => $margin_types, 'returns_location_types_list' => $returns_location_types, 'inventory_types_list' => $inventory_types, 'states_data' => $states_data, 'countries_data' => $countries_data, 'states' => $states, 'countries' => $countries, 'brands_data' => $brands_data, 'rm_data' => $rm_data, 'currency_data' => $currency_data, 'company_data' => $company_data, 'account_data' => $account_data, 'supplier_data' => $supllier_data, 'pan_det' => $pan_det, 'cin_det' => $cin_det, 'tinvat_det' => $tinvat_det, 'cst_det' => $cst_det, 'cheque_det' => $cheque_det, 'mou_det' => $mou_det, 'approve_access' => $approveAccess, 'reject_access' => $rejectAccess, 'cat' => $cat, 'parent_id' => $parent_Cat, 'product_class' => $product_class,'docsArr'=>$docsArr, 'approval' => '1', 'suppliers_rank' => $suppliers_rank,'docTypes'=>$docTypes,'po_days'=>$poDays,'invoice_days'=>$invoiceDays,'negotiation'=>$negotiation]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
       
        DB::enableQueryLog();
        $seller_id = Session::get('userId');
        $legalentitySessionId = Session::get('legal_entity_id');
        //$supplierEditSessionId = session('supplier_id');
        //$vehicle_id = session::get('vehicle_id');
		$referrer = $request->headers->get('referer');
		$params = explode('/',$referrer);
		$editSupplierId = array_pop($params);
        if($editSupplierId == 'add')
			$editSupplierId = Null;
        $is_provider = array_pop($params);
        
        switch($is_provider)
        {
            case 'vehicleproviders':  $vendor = 'saveVehServiceTable'; $leType = '1009'; $roleName = 'Vehicle Provider';break;
            case 'humanresource': $vendor = 'saveHrProviderTable';  $leType = '1010'; $roleName = 'HR Provider';break;
            case 'vehicle': $vendor = 'saveVehicleTable';  $leType = '1008'; $roleName = 'DRVR';break;
            case 'serviceproviders': $vendor = 'saveServiceProviderTable';  $leType = '1007'; $roleName = 'Service Provider'; break;
            case 'space': $vendor = 'saveSpaceTable';  $leType = '1011'; $roleName = 'Space Owner'; break;
            case 'spaceprovider': $vendor = 'saveSpaceProviderTable';  $leType = '1012'; $roleName ='Space Provider'; break;
            default: $vendor = NULL;  $leType = NULL;  $roleName = 'Supplier'; break;
        }
    
    	$this->_supplierModel->saveSupplier($request,$seller_id,$legalentitySessionId,$editSupplierId,0,$vendor,$leType,$roleName);

    }

    public function supplierdocs(Request $request) {
        if (!Session::has('supplier_id')) {
            echo "No Supplier";
        } else {            
            
        try{
            $postData = Input::all();
            $docText = '';
            $supplierId = (Session::has('supplier_id')) ? Session::get('supplier_id') : 0;
            $documentType = isset($postData['documentType']) ? $postData['documentType'] : '';            
            $ref_no = isset($postData['ref_no']) ? $postData['ref_no'] : '';            
            if ($request->hasFile('upload_file')) {
                $extension = Input::file('upload_file')->getClientOriginalExtension();
                if(!in_array($extension, array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'))) {
                    return Response::json(array('status'=>400, 'message'=>'Invalid extension'));
                }
                $doc_id_exists = 0;
                $destinationPath = public_path().'/uploads/Suppliers_Docs';
				$imageObj = $request->file('upload_file');
                $fileName = Input::file('upload_file')->getClientOriginalName();
				$productObj = new ProductRepo();
				$url = $productObj->uploadToS3($imageObj,'supplier',1);
			
			}	
			else
			{
				$fileName = NULL;
				$url = NULL;
			}
	
            $doc_id_exists = Legalentitydocs::select('doc_id')->where(['legal_entity_id'=>$supplierId,'doc_type'=>$documentType])->first();                  
			if($doc_id_exists && $doc_id_exists->doc_id)
			{
			 Legalentitydocs::where('doc_id',$doc_id_exists->doc_id)
			 ->update(['legal_entity_id'=>$supplierId,
				'reference_no'=>$ref_no, 
				'doc_name'=>$fileName, 
				'doc_type'=>$documentType,
				'doc_url'=>$url, 
				'updated_by'=>Session('userId'), 
				'updated_at'=>date('Y-m-d H:i:s')
			 ]);			 
			 if($documentType == 'GSTIN')
			 {
				$this->saveGstin($supplierId,$ref_no); 
			 }					 
			 $docsArr = Legalentitydocs::where('legal_entity_id', $supplierId)->get()->all();
			 $model = new SupplierModel;
			 foreach($docsArr as $doc)                                            
			 {
				if($doc->doc_url)
				{
					$docText .='<tr>
				<td>'.$doc->doc_type.'</td>
				<td>'.$doc->reference_no.'</td>
				<td>'.$model->getUserNameById($doc->created_by).'</td>
				<td align="center"><a href="'.$doc->doc_url.'" target="_blank"><i class="fa fa-download"></i></a></td>
				<td align="center">
					<a class="delete grn-del-doc" id="'.$doc->doc_id.'" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
				</td>
				</tr>';
				}
				else
				{
					$docText .='<tr>
				<td>'.$doc->doc_type.'</td>
				<td>'.$doc->reference_no.'</td>
				<td>'.$model->getUserNameById($doc->created_by).'</td>
				<td align="center"></td>
				<td align="center">
					<a class="delete grn-del-doc" id="'.$doc->doc_id.'" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
				</td>
				</tr>';
				}									
			 }
			return Response::json(array('status'=>200, 'message'=>'Successfully uploaded.','docText'=>$docText,'refresh'=>1,'count'=>count($docsArr)));
			}
			else
			{    
			$docsArr = array(
				'legal_entity_id'=>$supplierId,
				'reference_no'=>$ref_no, 
				'doc_name'=>$fileName, 
				'doc_type'=>$documentType,
				'doc_url'=>$url, 
				'created_by'=>Session('userId'), 
				'created_at'=>date('Y-m-d H:i:s')
			);
			$supplierModel = new SupplierModel();
			$createdBy = $supplierModel->getUserNameById($docsArr['created_by']);
			$savedObj = Legalentitydocs::create($docsArr); 
			 if($documentType == 'GSTIN')
			 {
				$this->saveGstin($supplierId,$ref_no); 
			 }	
			$supplier_doc_id=$savedObj->doc_id;
			
			if(isset($docsArr['doc_url']) && $docsArr['doc_url']!= null)
			{
			$docText='<tr>
					<td><input type="hidden" name="docs[]" value="'.$supplier_doc_id.'">'.$documentType.'</td>
					<td>'.$docsArr['reference_no'].'</td>
					<td>'.$createdBy.'</td>
					<td align="center"><a href="'.$docsArr['doc_url'].'" target="_blank"><i class="fa fa-download"></i></a></td>
					<td align="center">
					<a class="delete grn-del-doc" id="'.$supplier_doc_id.'" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
					</td>
				</tr>';	
			}
			else
			{
				$docText='<tr>
					<td><input type="hidden" name="docs[]" value="'.$supplier_doc_id.'">'.$documentType.'</td>
					<td>'.$docsArr['reference_no'].'</td>
					<td>'.$createdBy.'</td>
					<td align="center"></td>
					<td align="center">
					<a class="delete grn-del-doc" id="'.$supplier_doc_id.'" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
					</td>
				</tr>';
			}			
			return Response::json(array('status'=>200, 'message'=>'Successfully uploaded.','docText'=>$docText,'count'=>count($docsArr)));
			}            
        }
        catch(Exception $e) {
            return Response::json(array('status'=>400, 'message'=>'Failed to upload'));
        }
        }
    }

    public function deleteDoc($docId)
    {
        if($docId != null)
        {
        $url = DB::table('legal_entity_docs')->where('doc_id',$docId)->pluck('doc_url');  
        $objectUrl = isset($url[0])?$url[0]:null;
        $productObj = new ProductRepo();
		$result = $productObj->deleteFromS3($objectUrl);
        Legalentitydocs::destroy($docId);
        }
    }
    
    public function getDocumentTypeName($documentType)
    {
        return Documentsmaster::select('doc_no')->where(['doc_master_id'=>$documentType+1])->first()->doc_no;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show() {
        //            
        $finalData = SupplierDetailsModel::selectRaw('le.business_name as business_name, le.legal_name as legal_name')
                ->Join('legal_entities as le', 'le.legal_entity_id', '=', 'supplier_details.legal_entity_id')
                ->Join('supplier_details as sd', 'sd.supplier_id', '=', 'le.parent_id')
                ->get()->all();
        return json_encode($finalData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try
        {
            $Legalentity_Id = $request->legalentity_id;
            $vendor = Legalentities::where('legal_entity_id', $Legalentity_Id)->pluck('legal_entity_type_id');
            $vendorType = (isset($vendor[0])) ? $vendor[0] : '1002';
            $gridToRefresh = '#supplier_list_grid';
            $status = 1;
            $message = 'Successfully deleted';
            if ($vendorType == '1002')
            {
                $brandsCount = DB::table('vw_managesupplies')->where('legal_entity_id', $Legalentity_Id)->pluck('brands');
                if ($brandsCount[0] > 0)
                {
                    $message = 'Please delete / unmap brands associated with this Supplier';
                }
                else
                {
                    $po = DB::table('po')->where('legal_entity_id', $Legalentity_Id)->pluck('po_id');
                    if (count($po))
                    {
                        $message = 'Please check POs associated with this Supplier';
                    }
                    SupplierModel::where('legal_entity_id', $Legalentity_Id)->delete();
                    DB::table('supplier_terms')->where('legal_entity_id', $Legalentity_Id)->delete();
                    $gridToRefresh = '#supplier_list_grid';
                }
            }
            else
            {
                switch ($vendorType)
                {
                    case '1007':
                        $provider = new ServiceProvider();
                        $gridToRefresh = '#ser_pro_list_grid';
                        break;
                    case '1008':
                        $provider = new Vehicle();
                        $gridToRefresh = '#veh_list_grid';
                        break;
                    case '1009':
                        $veh_exists = Vehicle::where('veh_provider', $Legalentity_Id)->count();
                        $message = ($veh_exists > 0) ? 'Please un-map vehicles' : 'Successfully deleted';
                        $provider = new VehicleService();
                        $gridToRefresh = '#veh_pro_list_grid';
                        break;
                    case '1010':
                        $provider = new HrProvider();
                        $gridToRefresh = '#hr_list_grid';
                        break;
                    case '1011':
                        $provider = new Space();
                        $gridToRefresh = '#space_list_grid';
                        break;
                    case '1012':
                        $space_exists = Space::where('space_provider', $Legalentity_Id)->count();
                        $message = ($space_exists > 0) ? 'Please un-map Space' : 'Successfully deleted';
                        $provider = new SpaceProvider();
                        $gridToRefresh = '#space_pro_list_grid';
                        break;
                }
            }
            
            if ($message == 'Successfully deleted')
            {
                 
                if($gridToRefresh != '#veh_list_grid'){
                    $provider::where('legal_entity_id', $Legalentity_Id)->delete();
                        Users::where('legal_entity_id', $Legalentity_Id)->delete();
                        Legalentitydocs::where('legal_entity_id', $Legalentity_Id)->delete();
                        DB::table('aggrement_terms')->where(['legal_entity_id'=>$Legalentity_Id])->delete();
                        Legalentities::where('legal_entity_id', $Legalentity_Id)->delete(); 
                } else{
                    $Veh_Id = $request->vehicle_id;
                    $vehicletype=DB::select(DB::raw("select vehicle_type from vehicle where vehicle_id=$Veh_Id"));
                    if($vehicletype[0]->vehicle_type != 156002){
                        $provider::where('legal_entity_id', $Legalentity_Id)->delete();
                            Users::where('legal_entity_id', $Legalentity_Id)->delete();
                            Legalentitydocs::where('legal_entity_id', $Legalentity_Id)->delete();
                            DB::table('aggrement_terms')->where(['legal_entity_id'=>$Legalentity_Id])->delete();
                            Legalentities::where('legal_entity_id', $Legalentity_Id)->delete();  
                    } else{
                        $provider::where('vehicle_id', $Veh_Id)->delete();
                    }                    
                }
            }
            $leCounts = DB::table('legal_entities')->select('legal_entity_type_id',DB::RAW('COUNT(legal_entity_type_id) AS COUNT'))->where('legal_entity_type_id',$vendorType)->get()->all();
            $count = (isset($leCounts[0]))?$leCounts[0]:0;        
            $response = array('status' => $status, 'grid_id' => $gridToRefresh, 'message' => $message,'count'=>$count->COUNT);        
            return $response;
        
        }
        catch (\ErrorException $ex)
        {

            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getSuppliers(Request $request) {
        try {
            //$this->grid_field_db_match = array('Supplier' => 'user_name', 'Contact' => 'contact', 'SRM' => 'rel_manager', 'Brands' => 'Brands', 'Products' => 'Products', 'Warehouses' => 'warehouses', 'Documents' => 'Documents',
            //    'Created_By' => 'created_by', 'Created_On' => 'created_at', 'Approved_By' => 'approvedby', 'Approved_On' => 'Approvedon', 'Status' => 'status', 'le_code' => 'le_code',);
            $this->grid_field_db_match_supp = array(
                'user_name'=>'vw_managesupplies.user_name',
                'Contact'=>'vw_managesupplies.contact',
                'SRM'=>'vw_managesupplies.rel_manager',
                'Brands'=>'vw_managesupplies.Brands',
                'Products'=>'vw_managesupplies.Products',
                'Documents_count'=>'vw_managesupplies.Documents',
                'Created_By'=>'vw_managesupplies.created_by',
                'Created_On'=>'vw_managesupplies.created_at',
                'Approved_By'=>'vw_managesupplies.approvedby',
                'Approved_On'=>'vw_managesupplies.Approvedon',
                'le_code'=>'vw_managesupplies.le_code',
                'Status'=>'vw_managesupplies.status',
                'is_active'=>'vw_managesupplies.is_active',
                'gst_no'  => 'legal_entities.gstin',
                'state_name' => DB::raw('getStateNameById(legal_entities.state_id)'));
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;

            $sm = new VwManagesuppliesModel();


            $legalEntityIdArray = array();

            $loggedInuserId = Session::get('userId');
            //$legal_entity_id = DB::table('users')->select('legal_entity_id')->where('user_id', $loggedInuserId)->get()->all();
            $legal_entity_id = Session::get('legal_entity_id');
            $rback = new Role;
            $rmUserList = $rback->getFilterData(5);
            $rmUserListObj = json_decode($rmUserList);
            if($rmUserListObj->supplier)
            {
                $rnUserListArr = $rmUserListObj->supplier;
            }

            $gloabl_access = $this->_roleRepo->checkPermissionByFeatureCode('GLB0001');
            
            if ($gloabl_access == 1) {

                $query = $sm::select([
                    'vw_managesupplies.legal_entity_id as SupplierID',
                    'vw_managesupplies.user_name',
                    'vw_managesupplies.le_code as le_code',
                    'vw_managesupplies.contact as Contact',
                    'vw_managesupplies.rel_manager as SRM',
                    'vw_managesupplies.Brands as Brands',
                    'vw_managesupplies.Products as Products',
                    'vw_managesupplies.warehouses as Warehouses',
                    'vw_managesupplies.Documents as Documents',
                    'vw_managesupplies.created_by as Created_By',
                    'vw_managesupplies.created_at as Created_On',
                    'vw_managesupplies.approvedby as Approved_By',
                    'vw_managesupplies.Approvedon as Approved_On',
                    'vw_managesupplies.status as Status',
                    'vw_managesupplies.is_active',
                    'legal_entities.gstin as gst_no',
                    DB::raw('getStateNameById(legal_entities.state_id) as state_name')
                ]);

                $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'vw_managesupplies.legal_entity_id');
                $query->orderBy('Created_On','desc');
            } else {

                $query = $sm::select([
                    'vw_managesupplies.legal_entity_id as SupplierID',
                    'vw_managesupplies.user_name',
                    'vw_managesupplies.le_code as le_code',
                    'vw_managesupplies.contact as Contact',
                    'vw_managesupplies.rel_manager as SRM',
                    'vw_managesupplies.Brands as Brands',
                    'vw_managesupplies.Products as Products',
                    'vw_managesupplies.warehouses as Warehouses',
                    'vw_managesupplies.Documents as Documents',
                    'vw_managesupplies.created_by as Created_By',
                    'vw_managesupplies.created_at as Created_On',
                    'vw_managesupplies.approvedby as Approved_By',
                    'vw_managesupplies.Approvedon as Approved_On',
                    'vw_managesupplies.status as Status',
                    'vw_managesupplies.is_active',
                    'legal_entities.gstin as gst_no',
                    DB::raw('getStateNameById(legal_entities.state_id) as state_name')
                ]);

                $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'vw_managesupplies.legal_entity_id');

                $brands=DB::table('user_permssion')
                           ->where(['permission_level_id' => 7, 'user_id' => $loggedInuserId])
                         ->pluck('object_id')->all();
                // if(!in_array(0, $brands)){
                //     $query->whereIn('Brands',$brands);
                // }
                $manufacturer=DB::table('user_permssion')
                       ->where(['permission_level_id' => 11, 'user_id' => $loggedInuserId])
                     ->pluck('object_id')->all();
                $getMappedSuppliers=$this->getMappedSuppliersForManufacturer($manufacturer);
                if(count($getMappedSuppliers)>0){
                    $query->whereIn('legal_entity_id',$getMappedSuppliers);
                }
                $query->orderBy('Created_On','desc');
            }

            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_supp[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_supp[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter')) {           //checking for filtering

                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                        if ($filter_query_substr == 'startsw') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = $filter_value_array[1] . '%';


                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }


                        if ($filter_query_substr == 'endswit') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = '%' . $filter_value_array[1];


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }




                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :

                                $filter_operator = '=';

                                break;

                            case 'ne':

                                $filter_operator = '!=';

                                break;

                            case 'gt' :

                                $filter_operator = '>';

                                break;

                            case 'lt' :

                                $filter_operator = '<';

                                break;

                            case 'ge' :

                                $filter_operator = '>=';

                                break;

                            case 'le' :

                                $filter_operator = '<=';

                                break;
                        }


                        if (isset($this->grid_field_db_match_supp[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_supp[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
//            \DB::enableQueryLog();
            $row_count = count($query->groupBy('SupplierID')->get()->all());
//            \Log::info(\DB::getQueryLog());
            $query->skip($skip)->take($pageSize);

            $Manage_Suppliers = $query->groupBy('SupplierID')->get()->all();

            foreach ($Manage_Suppliers as $k => $list) {
                $totDocs = 6; //$docsArray[$businesstypeIdValue][$countryId];					
                $Manage_Suppliers[$k]['Documents_count'] = $Manage_Suppliers[$k]['Documents'].'/'.$totDocs;

                if ($Manage_Suppliers[$k]['is_active']  == '1') {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox" checked>'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }                
                if ($Manage_Suppliers[$k]['Created_On'] != '') {
                $Manage_Suppliers[$k]['Created_On'] = date('d-m-Y', strtotime($Manage_Suppliers[$k]['Created_On']));
                }
                
                if ($Manage_Suppliers[$k]['Approved_On'] != '') {
                $Manage_Suppliers[$k]['Approved_On'] = date('d-m-Y', strtotime($Manage_Suppliers[$k]['Approved_On']));
                }
                if ($Manage_Suppliers[$k]['SupplierLogo'] != '') {
                    if (strstr($Manage_Suppliers[$k]['SupplierLogo'], 'http')) {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    } else {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/Suppliers_Docs/" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    }
                } else {
                    $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                }
               
                 $edit_supplier    = $this->_roleRepo->checkPermissionByFeatureCode('SUP003');
                 $delete_supplier  = $this->_roleRepo->checkPermissionByFeatureCode('SUP004');
                 $approve_supplier = $this->_roleRepo->checkPermissionByFeatureCode('SUP005');
                 
                 if($approve_supplier == 1) {
                    $Manage_Suppliers[$k]['Action']  = '<a data-toggle="modal" href="suppliers/approval/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp;&nbsp;';                    
                 }
                 if($edit_supplier == 1) {
                    $Manage_Suppliers[$k]['Action'] .= '<a data-toggle="modal" href="suppliers/edit/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';
                 }
                if($delete_supplier ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a class="deleteSupplier" href="' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-trash-o"></i> </a>';
                }
                $addPaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO0011');
                if($addPaymentFeature ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a href="#addPaymentModel" class="addLePayment" data-leId ="' . $Manage_Suppliers[$k]['SupplierID'] . '" data-toggle="modal"><i class="fa fa-inr"></i></a>';
                }
            }

            echo json_encode(array('Records' => $Manage_Suppliers, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getBrandsFromView(Request $request) {
        try {
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;
            
            $loggedInuserId = Session::get('userId');
            $path = explode(':', $request->input('path'));
            $SupplierID = $path[1];
            $Brand_Model_Obj = new VwBrandwiseDetailsModel();
            $query = $Brand_Model_Obj::select('brand_id as BrandID', 'brand_logo as BrandLogo', 'brand_name as BrandName',
                    'IS Trademarked', 'Authorised as Authorised', 'Products as Products', 'legal_entity_id', 'WithImages as With_Images',
                    'WithoutImages as Without_Images', 'withInventory as With_Inventory', 'WithoutInventory as Without_Inventory',
                    'approved as Approved', 'pending as Pending')->where('legal_entity_id',$SupplierID)->groupBy('brand_id');
            $brands=DB::table('user_permssion')
                       ->where(['permission_level_id' => 7, 'user_id' => $loggedInuserId])
                     ->pluck('object_id')->all();
            if(!in_array(0, $brands)){
                $query->whereIn('brand_id',$brands);
            }
           
            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';
                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }
                if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }
            if ($request->input('$filter')) {           //checking for filtering
                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'
                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);
                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                        if ($filter_query_substr == 'startsw') {
                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = $filter_value_array[1] . '%';
                            foreach ($this->grid_field_db_match as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'endswit') {
                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = '%' . $filter_value_array[1];
                            foreach ($this->grid_field_db_match as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }
                            foreach ($this->grid_field_db_match as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }
                            foreach ($this->grid_field_db_match as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {
                        switch ($filter_query_operator) {
                            case 'eq' :
                                $filter_operator = '=';
                                break;
                            case 'ne':
                                $filter_operator = '!=';
                                break;
                            case 'gt' :
                                $filter_operator = '>';
                                break;
                            case 'lt' :
                                $filter_operator = '<';
                                break;
                            case 'ge' :
                                $filter_operator = '>=';
                                break;
                            case 'le' :
                                $filter_operator = '<=';
                                break;
                        }
                        if (isset($this->grid_field_db_match[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
            
            $row_count = count($query->get()->all());
            $query->skip($skip)->take($pageSize);
            $Manage_Brands = $query->get()->all();

            foreach ($Manage_Brands as $k => $list) {

                if ($list['IS Trademarked'] == '1') {
                    $Manage_Brands[$k]['Trademarked'] = '<i class="fa fa-check"></i>';
                } else {
                    $Manage_Brands[$k]['Trademarked'] = '<i class="fa fa-times"></i>';
                }

                if ($list['Authorised'] == '1') {
                    $Manage_Brands[$k]['is_authorised'] = '<i class="fa fa-check"></i>';
                } else {
                    $Manage_Brands[$k]['is_authorised'] = '<i class="fa fa-times"></i>';
                }
                $Manage_Brands[$k]['Action'] = '<a data-toggle="modal" class="editBrand" href="' . $Manage_Brands[$k]['BrandID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="deleteBrand" href="' . $Manage_Brands[$k]['BrandID'] . '"> <i class="fa fa-trash-o"></i> </a>';

                if ($Manage_Brands[$k]['BrandLogo'] != '') {
                    if (strstr($Manage_Brands[$k]['BrandLogo'], 'http')) {
                        $Manage_Brands[$k]['BrandLogo'] = "<img src='" . $Manage_Brands[$k]['BrandLogo'] . "' height='33' width='100' />";
                    } else {
                        $Manage_Brands[$k]['BrandLogo'] = "<img src='/uploads/brand_logos/" . $Manage_Brands[$k]['BrandLogo'] . "' height='33' width='100' />";
                    }
                } else {
                    $Manage_Brands[$k]['BrandLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                }
            }
            echo json_encode(array('Records' => $Manage_Brands, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

   public function getProductsFromView(Request $request) {
        try {   
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;  
            
            $path = explode('/', $request->input('path'));
            $suppl_path_temp = explode(':', $path[0]);
            $path_temp = explode(':', $path[1]);            
            $BrandID = $path_temp[1];
            $Legalentity_ID = $suppl_path_temp[1];            
            $Product_Model_Obj = new VwProductsMarginModel();
            $query = $Product_Model_Obj::select(['vw_productsmargin.product_id as Product_ID', 'vw_productsmargin.image as ProductLogo',
                'vw_productsmargin.category_name as Category', 'vw_productsmargin.prod_price_id as prod_price_id', 
                'vw_productsmargin.product_name as Product_Name', 'vw_productsmargin.mrp as MRP', 'vw_productsmargin.base_price as BasePrice', 
                'vw_productsmargin.EBP as EBP', 'vw_productsmargin.RBP as RBP', 'vw_productsmargin.CBP as CBP',
                'master_lookup.master_lookup_name as Inventory_Mode', 'vw_productsmargin.Schemes', 'vw_productsmargin.status as Status', 
                'vw_productsmargin.MBQ as MPQ', 'prt.tax as Tax', 'prt.dlp as Elp', 'prt.distributor_margin as Emargin', 
                'prt.rlp as Ptr', 'prt.retailer_margin as RetailerMargin', 'prt.inventory_mode as Inv', 'prt.atp as Atp', 
                'prt.effective_date as effectiveDate', 'prt.subscribe as Subscribe', DB::Raw('getLeWhName(prt.le_wh_id) as whname')]);
            $query->join('master_lookup','master_lookup.value', '=', 'vw_productsmargin.inventorymode');
            $query->join('product_tot as prt','prt.prod_price_id','=','vw_productsmargin.prod_price_id');
            //$query->join('legalentity_warehouses as lew','lew.le_wh_id','=','vw_productsmargin.le_wh_id');
            
            $query->where('brand_id', '=', $BrandID);
            $query->where('legal_entity_id', '=', $Legalentity_ID);
            
            
            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';
                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }
                if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }
            if ($request->input('$filter')) {           //checking for filtering
                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'
                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);
                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                        if ($filter_query_substr == 'startsw') {
                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = $filter_value_array[1] . '%';
                            foreach ($this->grid_field_db_match as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'endswit') {
                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = '%' . $filter_value_array[1];
                            foreach ($this->grid_field_db_match as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }
                            foreach ($this->grid_field_db_match as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }
                            foreach ($this->grid_field_db_match as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {
                        switch ($filter_query_operator) {
                            case 'eq' :
                                $filter_operator = '=';
                                break;
                            case 'ne':
                                $filter_operator = '!=';
                                break;
                            case 'gt' :
                                $filter_operator = '>';
                                break;
                            case 'lt' :
                                $filter_operator = '<';
                                break;
                            case 'ge' :
                                $filter_operator = '>=';
                                break;
                            case 'le' :
                                $filter_operator = '<=';
                                break;
                        }
                        if (isset($this->grid_field_db_match[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
            
            $row_count = count($query->get()->all());
            $query->skip($skip)->take($pageSize);
            $Manage_Products = $query->get()->all();
            
            $currency = DB::table('currency')->where('code','INR')->select('currency_id','symbol_left')->first();
            foreach ($Manage_Products as $k => $list) {
                $edit_product  = $this->_roleRepo->checkPermissionByFeatureCode('SUP007');
                $dlete_product = $this->_roleRepo->checkPermissionByFeatureCode('SUP008');
                
                $Manage_Products[$k]['Currency'] = $currency->symbol_left;
                if ($Manage_Products[$k]['ProductLogo'] != '') {
                    if (strstr($Manage_Products[$k]['ProductLogo'], 'http')) {
                        $Manage_Products[$k]['ProductLogo'] = "<img src='" . $Manage_Products[$k]['ProductLogo'] . "' height='45' width='45' />";
                    } else {
                        $Manage_Products[$k]['ProductLogo'] = "<img src='/uploads/products/" . $Manage_Products[$k]['ProductLogo'] . "' height='45' width='45' />";
                    }
                } else {
                    $Manage_Products[$k]['ProductLogo'] = "<img src='/uploads/products/notfound.png' height='45' width='45' />";
                }
                $Manage_Products[$k]['Action'] = '<a data-toggle="modal" class="set_price" href="' . $list->prod_price_id . '"> <i class="fa fa-inr"></i></a>';
                if($list->Subscribe == 1 ) {
                    $Manage_Products[$k]['Subscribe'] = '<span style="display:block" class="ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
                } else {
                    $Manage_Products[$k]['Subscribe'] = '<span style="display:block;color:red;" class="ui-igcheckbox-small-off ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
                }
                
            }
            echo json_encode(array('Records' => $Manage_Products, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    /**
     * get product info from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProducts(Request $request, $Supplier_Id) {

        //start

        $this->grid_field_db_match_tot = array(
            'ProductID' => 'products.product_id',
            'BrandName' => 'brands.brand_name',
            'Category' => 'products.category_id',
            //'ProductName' => 'p.product_title',
            'ProductName' => 'pt.product_name',
            'WarehouseName' => 'pt.le_wh_id',
            'upc' => 'products.upc',
            'MRP' => 'p.mrp',
            'MSP' => 'product_tot.msp',
            'Bestprice' => 'pt.base_price',
            'TaxType' => 'pt.tax_type',
            'Tax' => 'pt.tax',
            'EbutorMargin'=> 'distributor_margin',
            'ELP' => 'pt.dlp',
            'PTR' => 'pt.rlp',
            'ATP' => 'pt.atp',
            'MRP' => 'pt.mrp',
            'sku' => 'p.sku',
            'seller_sku' => 'p.seller_sku',
            'subscribe' => 'pt.subscribe',
            'InventoryMode' => 'pt.inventory_mode',
            'EffectiveDate' => 'pt.effective_date'
        );

        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        $pm = new ProductModel();
	
            $query = DB::table('product_tot as pt')
                    ->join('products as p','p.product_id', '=', 'pt.product_id')
                    ->leftjoin('brands','brands.brand_id', '=', 'p.brand_id')
                    ->select('pt.product_id as ProductID', DB::raw('getLeWhName(pt.le_wh_id) as WarehouseName'),
                            DB::raw('getBrandName(p.brand_id) AS BrandName'), 'pt.product_name as ProductName', 'pt.dlp as ELP',
                            'pt.base_price as Bestprice', DB::raw('getTaxTypeName(pt.tax_type) as TaxType'),
                            'pt.tax as Tax', 'pt.prod_price_id', 'pt.rlp as PTR','pt.distributor_margin as EbutorMargin',
                            DB::raw('getMastLookupValue(pt.inventory_mode) AS InventoryMode'), 'pt.atp AS ATP','p.mrp as MRP',
                            'pt.effective_date as EffectiveDate','p.sku as sku', 'p.seller_sku as seller_sku',
			    DB::raw('getMastLookupValue(pt.atp_period) AS ATPPeriod'),'pt.subscribe as subscribe',
                            //DB::raw( 'case when pt.is_markup=1 then (p.mrp-pt.rlp)*100/p.mrp else (p.mrp-pt.rlp)*100/pt.rlp end as margin' ),
                            DB::raw('pt.dlp-pt.base_price as Tax_Amt')
                            )->where('pt.supplier_id',$Supplier_Id);
		

            $currency = DB::table('currency')->where('code','INR')->select('currency_id','symbol_left')->first();

			
            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_tot[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_tot[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter')) {           //checking for filtering
                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);

                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }

                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {

                        if ($filter_query_substr == 'startsw') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = $filter_value_array[1] . '%';


                            foreach ($this->grid_field_db_match_tot as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_tot[$key], 'like', $filter_value);
                                }
                            }
                        }


                        if ($filter_query_substr == 'endswit') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = '%' . $filter_value_array[1];


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_tot as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_tot[$key], 'like', $filter_value);
                                }
                            }
                        }




                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_tot as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_tot[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_tot as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_tot[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :

                                $filter_operator = '=';

                                break;

                            case 'ne':

                                $filter_operator = '!=';

                                break;

                            case 'gt' :

                                $filter_operator = '>';

                                break;

                            case 'lt' :

                                $filter_operator = '<';

                                break;

                            case 'ge' :

                                $filter_operator = '>=';

                                break;

                            case 'le' :

                                $filter_operator = '<=';

                                break;
                        }


                        if (isset($this->grid_field_db_match_tot[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_tot[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
			

        $row_count = count($query->get()->all());


        $query->skip($skip)->take($pageSize);
        $products_list = $query->get()->all();
        $edit_tot = $this->_roleRepo->checkPermissionByFeatureCode('SUP010');
        $delete_tot = $this->_roleRepo->checkPermissionByFeatureCode('SUP011');
        $set_price = $this->_roleRepo->checkPermissionByFeatureCode('TOT002');
        
        foreach ($products_list as $k => $list) {
            $actions = '';
            if ($edit_tot == 1) {
                $actions  = '<a data-toggle="modal" class="editProduct" href="' . $list->prod_price_id . '" data-type="edit" id="edit"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';            
            }
            if($set_price == 1) {
            $actions .= '<a data-toggle="modal" class="set_price"  href="' . $list->prod_price_id . '"> <i class="fa fa-inr" style="color:#3598dc !important"></i></a>&nbsp;&nbsp;';
            }
            if ($delete_tot == 1) { 
                $actions .='<a class="deleteProduct" href="' . $list->prod_price_id . '"> <i class="fa fa-trash-o"></i> </a>&nbsp;&nbsp;';  
            }
            //$products_list[$k]->subscribe = '<span style="display:block" class="ui-icon ui-icon-check ui-igcheckbox-small-on"></span>'; 
            if($list->subscribe != 1 ) {
             $products_list[$k]->subscribe = 'No'; 
            }
            else
            {
                $products_list[$k]->subscribe = 'Yes';
            }
            $products_list[$k]->action = $actions;
        }        
        echo json_encode(array('Records' => $products_list, 'TotalRecordsCount' => $row_count));
    }


    public function childProdutList22($Wh_id) {
        $legalentity_id = Session()->get('legalentity_id')->all();
        try {
            $path = explode(':', Input::get('path'));
            $parent_id = $path[1];
            $child_data = DB::table('product_relations as pr')
                    ->leftjoin('vw_cp_products as vw', 'vw.product_id', '=', 'pr.product_id')
                    ->join('product_tot as pt', 'pt.product_id', '=', 'pr.product_id')
                    ->where('pr.parent_id', $parent_id)
                    ->where('pt.supplier_id', $legalentity_id)
                    ->where('pt.le_wh_id', $Wh_id)
                    ->select('vw.product_id', 'vw.primary_image', 'vw.product_title', 'vw.mrp', 'vw.variant_value1', 'vw.variant_value2', 'vw.variant_value3', 'pt.is_active')
                    ->get()->all();
            $parent_data = array();
            $parent_data = DB::table('vw_cp_products as vw')
                            ->join('product_tot as pt', 'pt.product_id', '=', 'vw.product_id')
                            ->where('vw.product_id', $parent_id)
                            ->where('pt.supplier_id', $legalentity_id)
                            ->where('pt.le_wh_id', $Wh_id)
                            ->select('vw.product_id', 'vw.primary_image', 'vw.product_title', 'vw.mrp', 'vw.variant_value1', 'vw.variant_value2', 'vw.variant_value3', 'pt.is_active')->get()->all();
            $product_viw_data = array_merge($parent_data, $child_data);
            $i = 0;
            foreach ($product_viw_data as $product_data) {
                if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $product_data->primary_image)) {
                    $product_image = "<img width='100' height='33'  src='" . $product_data->primary_image . "'/>";
                    $product_viw_data[$i]->primary_image = $product_image;
                } else {
                    $baseurl = url('uploads/manufacturer_logos');
                    $product_image = "<img width='100' height='33'  src='" . $baseurl . '/' . $product_data->primary_image . "'/>";
                    $product_viw_data[$i]->primary_image = $product_image;
                }
                if($product_data->is_active == 1)
                {
                $actions = '<label class="switch "><input class="switch-input enableDisableProduct enable" data_attr_productid=' . $product_data->product_id . ' type="checkbox" checked>'
                        . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }
                else
                {
                 $actions = '<label class="switch "><input class="switch-input enableDisableProduct enable" data_attr_productid=' . $product_data->product_id . ' type="checkbox">'
                        . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';   
                }
                
                $currency = DB::table('currency')->where('code', 'INR')->select('currency_id', 'symbol_left')->first();
                $product_viw_data[$i]->currency = $currency->symbol_left;
                $product_viw_data[$i]->actions = $actions;

                $i++;
            }
            if ($product_viw_data) {
                return json_encode(["productData" => $product_viw_data]);
            } else {
                return json_encode(["productData" => []]);
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function enableDisableProduct(Request $request) {
        $Type = $request->input('type');  // enable or diable
        $ProductID = $request->input('ProductId');

        $legalentity_id = Session()->get('legalentity_id')->all();



        $Is_Enable = 0;

        if ($Type == 'enable') {
            $Is_Enable = 1;
        }

        $Query = ProductTOT::where('supplier_id', '=', $legalentity_id);

        $Product_Count = $Query->where('product_id', '=', $ProductID)->count();

        if ($Product_Count == 0) {


            $Tot_Array = array(
                'supplier_id' => $legalentity_id,
                'product_id' => $ProductID,
                'is_active' => $Is_Enable,
				'created_by'=>Session::get('userId')
            );

            ProductTOT::insert($Tot_Array);
        } else {

            $Query = ProductTOT::where('supplier_id', '=', $legalentity_id);
            $Query->where('product_id', '=', $ProductID)->update(array('is_active' => $Is_Enable));
        }
    }

	

    /**
     * get brands info from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBrands(Request $request, $Legal_Entity_Id) {

        //start

        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        $bm = new BrandModel();

        $query = $bm::select(['brands.brand_id as BrandID', 'brands.brand_name as BrandName', 'brands.description as Description', DB::raw("count(products.product_id) as Products"), 'brands.is_authorized as Authorized', 'brands.is_trademark as Trademark']);

        $query->leftjoin('products', 'products.brand_id', '=', 'brands.brand_id');

        $query->where('brands.legal_entity_id', '=', $Legal_Entity_Id);

        if ($request->input('$orderby')) {    //checking for sorting


            $order = explode(' ', $request->input('$orderby'));


            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc

            $order_by_type = 'desc';

            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }


            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field

                $order_by = $this->grid_field_db_match[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }

		
		


        $query->groupBy('brands.brand_id');

        $row_count = count($query->get()->all());

//            echo $page*$pageSize;exit;
        $query->skip($skip)->take($pageSize);
        $brands_list = $query->get()->all();


        foreach ($brands_list as $k => $list) {
            $brands_list[$k]['Action'] = '<a data-toggle="modal" class="editBrand" href="' . $brands_list[$k]['BrandID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="deleteBrand" href="' . $brands_list[$k]['BrandID'] . '"> <i class="fa fa-trash-o"></i> </a>';
        }

        echo json_encode(array('Records' => $brands_list, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);

        //end
    }

    public function totSave(Request $request) {
        $supplier_id = Session()->get('supplier_id');
        $legalentity_id = Session()->get('legalentity_id');
        $sellerLegalEntityId = Session::get('legal_entity_id');
        $productMethod = new ProductController();

        $Brand_Id = $request->brand;        
        $prod_id = $request->get('product_name');
        $wh_id = $request->get('spd_whid');
        $TotModel = new TotModel();
        $ProductModel = new ProductModel();

        $where = ['supplier_id' => $legalentity_id, 'product_id' => $prod_id, 'le_wh_id'=>$wh_id];

        $whexists_tot = $TotModel->where($where)->first();
		$grnd_days = '';
		if($request->get('grn_days'))
        $grnd_days = implode(',',$request->get('grn_days'));
	
       //$base_price = $request->get('dlp') / (100+$request->get('tax'))*100;
	   $base_price = $request->get('dlp') / (100+$request->get('tax'))*100;
	   
		if($request->get('spd_whid'))
		{
		 $totWhId = $request->get('spd_whid'); 	
		}
		else
		{
	    $totWhId = $TotModel->where('prod_price_id',$request->get('product_tot_id'))->pluck('le_wh_id')->first();
		}
		
		if($request->get('product_name'))
		{
		 $prod_id = $request->get('product_name'); 	
		}
		else if($request->get('edit_form_product_id'))
		{
	    $prod_id = $request->get('edit_form_product_id');
		}	
		
		$date = date('Y-m-d', strtotime($request->get('efet_dat')));
	   	$setPriceArray =  array('product_id'=>$prod_id,'supplier_id'=>$supplier_id,'le_wh_id'=>$totWhId,'elp'=>$request->get('dlp'),'created_by'=> Session::get('userId'),'effective_date'=>$date);
		$supplierModel = new SupplierModel();
	   
        $Tot_Array = array(
            'product_id' => $prod_id,
            'le_wh_id' => $request->get('spd_whid'),
            'product_name' => $request->get('product_title'),
            'supplier_sku_code' => $request->get('supplier_sku_code'),
            'dlp' => $request->get('dlp'),
            'distributor_margin' => $request->get('distributor_margin'),
            'rlp' => $request->get('rlp'),
            'supplier_dc_relationship' => $request->get('supplier_dc_relationship'),
            'grn_freshness_percentage' => $request->get('grn_freshness_percentage'),
            'tax_type' => $request->get('tax_type'),  
            'tax' => $request->get('tax'),
            'moq' => $request->get('moq'),
            'moq_uom' => $request->get('moq_uom'),  
            'delivery_terms' => $request->get('delivery_terms'),            
            'delivery_tat_uom' => $request->get('delivery_tat_uom'),
            'grn_days' => $grnd_days,            
            'rtv_allowed' => $request->get('rtv_allowed'),
            'inventory_mode' => $request->get('inventory_mode'),
            'atp' => $request->get('atp'),
            'atp_period' => $request->get('atp_period'),
            'kvi' => $request->get('kvi'),
            'is_preferred_supplier' => $request->get('is_preferred_supplier'),
            'effective_date' => $request->get('efet_dat'),
            'base_price' => $base_price,
            'created_by' => $sellerLegalEntityId,
            'supplier_id' => $legalentity_id,
			'subscribe' => 1
        );
        
        $Tot_update = array(
            //'le_wh_id' => $request->get('spd_whid'),
            'product_name' => $request->get('product_title'),
            'supplier_sku_code' => $request->get('supplier_sku_code'),
            'dlp' => $request->get('dlp'),
            'distributor_margin' => $request->get('distributor_margin'),
            'rlp' => $request->get('rlp'),
            'supplier_dc_relationship' => $request->get('supplier_dc_relationship'),
            'grn_freshness_percentage' => $request->get('grn_freshness_percentage'),
            'tax_type' => $request->get('tax_type'),  
            'tax' => $request->get('tax'),
            'moq' => $request->get('moq'),
            'moq_uom' => $request->get('moq_uom'),  
            'delivery_terms' => $request->get('delivery_terms'),            
            'delivery_tat_uom' => $request->get('delivery_tat_uom'),
            'grn_days' => $grnd_days,            
            'rtv_allowed' => $request->get('rtv_allowed'),
            'inventory_mode' => $request->get('inventory_mode'),
            'atp' => $request->get('atp'),
            'atp_period' => $request->get('atp_period'),
            'kvi' => $request->get('kvi'),
            'is_preferred_supplier' => $request->get('is_preferred_supplier'),
            'effective_date' => $request->get('efet_dat'),
            'base_price' => $base_price,
            'updated_by' => $sellerLegalEntityId
        );

        if ($request->edit_form_product_id == '' && !isset($whexists_tot)) {  //if edit id not exist insert
            DB::table('product_tot')->insert($Tot_Array);
            //echo "Inserted";
			$supplierModel->saveTotPrice($setPriceArray);
        } 
        if(isset($whexists_tot) && count($whexists_tot) > 0 && $request->edit_form_product_id == '')
        {
         DB::table('product_tot')->where(array('product_id' => $whexists_tot->product_id, 'supplier_id' => $legalentity_id))->update($Tot_update);
         //echo "Updated";
        }
        if($request->edit_form_product_id != '') 
            {
            $Product_Id = $request->edit_form_product_id;
            $Product_tot = $request->product_tot_id;
            DB::table('product_tot')->where(array('product_id' => $Product_Id, 'supplier_id' => $legalentity_id, 'prod_price_id' =>$Product_tot))->update($Tot_update);            

			$supplierModel->saveTotPrice($setPriceArray);
        }
		return $legalentity_id;
    }

    /**
     * desc : Deletes products
     * @return success
     */
    public function deleteProductAction($prod_price_id) {        
        $productMethod = new ProductTOT();
        $productMethod->where('prod_price_id', $prod_price_id)->delete();
    }

    public function editProductAction($Product_price_ID, $Supp_ID) {        
        if($Supp_ID > 0)
        {
         $legalentity_id = $Supp_ID;
        }
        else
        {
        $legalentity_id = Session()->get('legalentity_id');
        }
    
        $product_tot = ProductTOT::find($Product_price_ID);
        $Product_ID = $product_tot->product_id;
        $Product_Model_Obj = new ProductModel();

        $query = $Product_Model_Obj::select(['products.product_id', 'brands.brand_name', 'brands.brand_id', 'categories.category_id', 'categories.cat_name', 'product_tot.product_name', 'product_content.short_description', 'products.product_title', 'products.seller_sku', 'product_tot.mrp', 'product_tot.msp', 'product_tot.rlp', 'product_tot.dlp', 'product_tot.cbp', 'product_tot.credit_days', 'product_tot.return_location_type', 'product_tot.delivery_terms', 'product_tot.is_return_accepted', 'product_tot.base_price', 'products.upc',
                    'product_tot.is_markup', 'product_tot.inventory_mode', 'products.is_active', 'product_tot.supplier_sku_code', 'product_tot.dlp', 'product_tot.distributor_margin', 'product_tot.rlp', 'product_tot.supplier_dc_relationship', 'product_tot.grn_freshness_percentage', 'product_tot.tax_type', 'product_tot.tax', 'product_tot.moq', 'product_tot.moq_uom', 'product_tot.delivery_terms', 'product_tot.delivery_tat_uom', 'product_tot.grn_days', 'product_tot.rtv_allowed', 'product_tot.atp', 'product_tot.atp_period', 'product_tot.kvi', 'product_tot.is_preferred_supplier', 'product_tot.effective_date', 'product_tot.le_wh_id', 'product_tot.prod_price_id']);

        $query->leftjoin('brands', 'brands.brand_id', '=', 'products.brand_id');
        $query->leftjoin('categories', 'categories.category_id', '=', 'products.category_id');
        $query->leftjoin('product_tot', 'product_tot.product_id', '=', 'products.product_id');
        $query->leftjoin('product_content', 'product_content.product_id', '=', 'products.product_id');

        $query->where('products.product_id', '=', $Product_ID);
        $query->where('product_tot.prod_price_id', '=', $Product_price_ID);

        $result = $query->get()->all();

        return $result;
    }



    public function readExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 3;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function importTOTExcel() {
//        try {
        ini_set('max_execution_time', 1200);
        $message = array();
        $msg = '';
        $mail_msg = '';
        $report_table = '';
        $status = 'failed';
        $replace_values = array('NA', 'N/A');
        $productObj = new ProductRepo();

		$required_data = array('product_id' => 'required', 'category'=>'', 'brand_id' => '', 'product_title' => '', 'supplier_product_code' => '', 'product_name' => 'required', 'elp' => '','actual_elp' => 'required', 'ebutor_margin' => '', 'ptr' => '', 'supplier_dc_relationship' => '', 'grn_freshness_percentage' => '', 'tax_type' => '', 'tax' => '', 'moq' => '', 'moq_uom' => '', 'delivery_tat' => '', 'delivery_tat_uom' => '', 'grn_days' => '', 'rtv_allowed' => '', 'inventory_mode' => '', 'atp' => '', 'atp_period' => '',
		'kvi' => '', 'is_preferred_supplier' => '', 'effective_datemm_dd_yyyy' => '');

        if (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $data = $this->_supplierModel->readExcel($path);
            $data = json_decode(json_encode($data), 1);
            if (isset($data['prod_data']) && count($data['prod_data']) > 0) {
                $cat_data = $data['cat_data'];
                $cat_data = array_values($cat_data);
                $prod_data = $data['prod_data'];
				$catgory_data = (isset($cat_data[0])) ? $cat_data[0] : '';
                $category = explode('-', $catgory_data);
                $warehouse_data = (isset($cat_data[1])) ? $cat_data[1] : '';
                $warehouse = explode('-', $warehouse_data);
				//$legalentity_id = Session()->get('legalentity_id');
				$legalentity_id = (Session::get('legalentity_id'))?Session::get('legalentity_id'): Session::get('add_continue');
                $current_supplier_id = Session()->get('supplier_id');
                $category_id = (isset($category[1])) ? $category[1] : '';
                $warehouse_id = (isset($warehouse[1])) ? $warehouse[1] : '';
                $check_data = array('yes' => 1, 'no' => 0, 'y' => 1, 'n' => 0);
                $is_margin_arr = array('markup' => 1, 'markdown' => 0);
                if ($legalentity_id != '') {
                    $pr_scount = 0;
                    $pr_fcount = 0;
                    foreach ($prod_data as $product) {
						$product_name = (isset($product['product_name'])) ? $product['product_name'] : '';

						$required_check_msg = array();
						
						foreach($required_data as $required_data_key=>$required) {
							
							if($required=='required')
							{
								if(!isset($product[$required_data_key]) || $product[$required_data_key] == '')
								{
									$required_check_msg[]=$required_data_key;
								}									
							}
							
						}

						if (count($required_check_msg) == 0) {
                            $brand = $product['brand'];
                            $supplier_sku = $product['supplier_product_code'];

							
								$productMethod = new ProductController();
                                $Brand_Query = DB::table('brands')->where('brand_name', '=', $brand);

                                $tax_type = $product['tax_type'];
								$moq_uom = $product['moq_uom'];
								$delivery_tat_uom = $product['delivery_tat_uom'];
								$kvi = $product['kvi'];
								$atp_period = $product['atp_period'];
								
                                $supplier_dc_rel = $product['supplier_dc_relationship'];
                                $supplier_dc_rel = MasterLookup::where(array('description'=>$supplier_dc_rel,'mas_cat_id'=>100))->pluck('value');
                                $supplier_dc_rel = (isset($supplier_dc_rel[0])) ? $supplier_dc_rel[0] : '';
                                $moq_uom = MasterLookup::where('master_lookup_name',$moq_uom)->pluck('value');
                                $moq_uom = (isset($moq_uom[0]))?$moq_uom[0]:'';


                                $delivery_tat_uom = MasterLookup::where('master_lookup_name',$delivery_tat_uom)->pluck('value');
                                $delivery_tat_uom = (isset($delivery_tat_uom[0]))?$delivery_tat_uom[0]:'';

                                $atp_period = MasterLookup::where('description',$atp_period)->pluck('value');
                                $atp_period = (isset($atp_period[0]))?$atp_period[0]:'';

                                $tax_type = MasterLookup::where('master_lookup_name', $tax_type)->pluck('value');
                                $tax_type = (isset($tax_type[0])) ? $tax_type[0] : '';

                                $kvi = MasterLookup::where('master_lookup_name', $kvi)->pluck('value');
                                $kvi = (isset($kvi[0])) ? $kvi[0] : '';



                                $inventory_mode = $product['inventory_mode'];
                                $inventory_mode = MasterLookup::where('description', $inventory_mode)->pluck('value');
                                $inventory_mode = (isset($inventory_mode[0])) ? $inventory_mode[0] : '';

                                /*                                        $upc_type_arr = MasterLookup::where('description', $product['product_typeupcean'])->pluck('value');
                                  $upc_type = (isset($upc_type_arr[0])) ? $upc_type_arr[0] : ''; */
                                //echo '=-===-=R'.$weight_uom.'===A'.$upc_type.'====J'.$return_location_type;
                                if ($inventory_mode != '') { //&& $lbh_uom != ''
                                    //$skuid = $productObj->generateSKUcode();

									$base_price = $product['elp'] / (100+$product['tax'])*100;

									
                                    $Product_Id = $product['product_id'];

                                    if ($Product_Id != '') {
                                  
									if($product['rtv_allowed'])
									{
										if(ucfirst($product['rtv_allowed']) == 'Yes')
										{$rtv_allowed=1;}
										else
										{$rtv_allowed=0;}
									}else
                                        {$rtv_allowed=0;}										
									
									if($product['is_preferred_supplier'])
									{
										if(ucfirst($product['is_preferred_supplier']) == 'Yes')
										{$is_preferred_supplier=1;}
										else
										{$is_preferred_supplier=0;}
									}else
                                        {$is_preferred_supplier=0;}
									    if(empty($product['atp'])){
                                            $product['atp']=0;
                                        }
                                        //echo 'Prod_id => '.$Product_Id.', Supp_id => '.$legalentity_id.' le_wh_id => '.$warehouse_id;

                                       // $effective_date = (isset($product['effective_datemm_dd_yyyy']['date'])) ? $product['effective_datemm_dd_yyyy']['date'] : $product['effective_datemm_dd_yyyy'];
                                        $effective_date=date('Y-m-d');

                                             $findproductin_products=DB::table('products')
                                             ->where('product_id',$product['product_id'])
                                             ->get()->all();

                                            if(count($findproductin_products)>0){

                                        $totexist = ProductTOT::where(array('product_id' => $Product_Id, 'supplier_id' => $legalentity_id,'le_wh_id' => $warehouse_id))->first();

                                        if (count($totexist) == 0) {
                                           /*ProductTOT::where(array('product_id' => $Product_Id, 'supplier_id' => $legalentity_id, 'le_wh_id'=>$warehouse_id))->update($Tot_Array); 


                                        } else {*/
                                           $Tot_Array = array(
                                            'product_id' => $product['product_id'],    
                                            'product_name' => $product['product_name'],
                                            'supplier_id'  =>$legalentity_id,
                                            'supplier_sku_code' => $product['supplier_product_code'],
                                            'distributor_margin' => $product['ebutor_margin'],
                                            'rlp' => $product['ptr'],
                                            'supplier_dc_relationship'=>$supplier_dc_rel,
                                            'grn_freshness_percentage'=>$product['grn_freshness_percentage'],
                                            'tax_type' => $tax_type,
                                            'tax' => $product['tax'],
                                            'base_price' => $base_price,
                                            'moq' => $product['moq'],
                                            'moq_uom' => $moq_uom,
                                            'delivery_terms' => $product['delivery_tat'],
                                            'delivery_tat_uom' => $delivery_tat_uom,
                                            'grn_days' => $product['grn_days'],
                                            'rtv_allowed' => $rtv_allowed,
                                            'inventory_mode' => $inventory_mode,
                                            'atp' => $product['atp'],
                                            'atp_period' => $atp_period,
                                            'kvi' => $kvi,
                                            'is_preferred_supplier' => $is_preferred_supplier,
                                            'le_wh_id' => $warehouse_id,
                                            'effective_date' => date('Y-m-d'),
                                            'updated_by' => Session::get('userId'),
                                            'subscribe'=>1,
                                            'is_active'=>1
                                        );
                                        $tot_elp = $product['actual_elp'];
                                        $Purchase_History = DB::table('product_tot')->insert($Tot_Array);
                                        $message[]=$product['product_name'].' product subscribed';
                                    }else{
                                        $totdata = $totexist->toArray();
                                        $tot_elp = (isset($totdata['dlp']) && $totdata['dlp']!='')?$totdata['dlp']:$product['actual_elp'];
                                    }
                                    if($product['elp']!='')
                                        $tot_elp = $product['elp'];
                                    
                                    $Purchase_Price_History = array('product_id'=>$Product_Id,
                                                                    'supplier_id'=>$legalentity_id,
                                                                    'le_wh_id' => $warehouse_id,
                                                                //'prod_price_id'=>$totexist->prod_price_id,
                                                                    'effective_date'=>date('Y-m-d'),
                                                                    'elp'=>$tot_elp,
                                                                    'actual_elp'=>$product['actual_elp'],
                                                                    'created_by'=>Session::get('userId')
                                                                    );
                                    //print_r($Purchase_Price_History);die;
                                    $Purchase_History = DB::table('purchase_price_history')->insert($Purchase_Price_History);
                                }else{
                                    $message[]=$product['product_name'].' product details not found';
                                }
                                        /*                                                ProductSlabRates::where('product_id', $Product_Id)->delete();
                                          $slab = array();
                                          for ($sl = 1; $sl <= 4; $sl++) {
                                          $sla_start = 'slab' . $sl . '_start_qty';
                                          $sla_end = 'slab' . $sl . '_end_qty';
                                          $sla_price = 'slab' . $sl . '_selling_price';
                                          $product[$sla_start] = str_replace($replace_values, '', $product[$sla_start]);
                                          if ($product[$sla_start] != '' && (int) ($product[$sla_price]) > 0) {
                                          $slab[] = array(
                                          'product_id' => $Product_Id,
                                          'start_range' => $product[$sla_start],
                                          'end_range' => $product[$sla_end],
                                          'price' => $product[$sla_price]
                                          );
                                          }
                                          }
                                          if (count($slab) > 0) {
                                          $slabs = ProductSlabRates::insert($slab);
                                          } else {
                                          Log::info('rollback if no slab rates');
                                          } */
                                        $pr_scount++;
                                    } else {
                                        $message[] = 'Product Id missing. Unable to create product ' . $product_name;
                                        $mail_msg.= 'Product Id missing. Unable to create product ' . $product_name . PHP_EOL;
                                        $pr_fcount++;
                                    }
                                } else {
                                    $message[] = 'Master Lookup data not exist in DB for ' . $product_name;
                                    $mail_msg.= 'Master Lookup data not exist in DB for ' . $product_name . PHP_EOL;
                                    $pr_fcount++;
                                }
                        } else {
							$message[] = 'All mandatory fields need to be filled for '.$product_name;
                            $mail_msg.= 'All mandatory fields need to be filled for '.$product_name . PHP_EOL;
                            $pr_fcount++;
                        }
                    }
                    $msg = $pr_scount . ' Products Created/Updated Successfully and ' . $pr_fcount . ' Products failed to Create/Update';
                    $status = 'success';

                    $file_path = public_path() . '/download/product_import_report.txt';
                    $supplierObj = new SupplierModel();
                    if (file_exists($file_path)) {
                        $file = fopen($file_path, "w");
                        fwrite($file, $mail_msg);
                        fclose($file);
                        $userId = Session::get('userId');
                        $fmName = $supplierObj->getUserNameById($userId);
                        $fmEmail = $supplierObj->getUserEmailById($userId);
                        $name = "Ebutor";
                        $template = 'product_import_report';
                        $subject = 'Product Import Report';
                        $supplierObj->sendEmail($fmName, $fmEmail, $name, $template, $subject, $file_path);
                    }
                } else {
                    $msg = 'Legal Entity/Supplier/Category ID should not be empty';
                }
            } else {
                $msg = 'No Data available';
            }
        } else {
            $msg = 'Please upload file';
        }
        /* } catch (\ErrorException $ex) {
          Log::error($ex->getMessage());
          } */
        $messg = json_encode(array('status' => $status, 'message' => $msg, 'status_messages' => $message));
//        Log::info($messg);
        return $messg;
    }

    public function getCatList() {
        $categories = DB::table('categories')
                ->select('category_id', 'cat_name', 'description', 'parent_id', 'is_active', 'is_product_class')
                ->get()->all();
        $options = '';
        if (!empty($categories)) {
            foreach ($categories as $childCategory) {
                if (property_exists($childCategory, 'childs')) {
                    if ($childCategory->is_product_class)
                        $options .= '<option value="' . $childCategory->category_id . '">  ---' . $childCategory->cat_name . '</option>';
                    else
                        $options .= '<option value="' . $childCategory->category_id . '" disabled="true">' . $childCategory->cat_name . '</option>';
                    foreach ($childCategory->childs as $childChildCategory) {
                        if (property_exists($childChildCategory, 'childs')) {

                            if ($childChildCategory->is_product_class)
                                $options .= '<option value="' . $childChildCategory->category_id . '">   --' . $childChildCategory->cat_name . '</option>';
                            else
                                $options .= '<option value="' . $childChildCategory->category_id . '" disabled="true">   --' . $childChildCategory->cat_name . '</option>';

                            foreach ($childChildCategory->childs as $childChildChildCategory) {

                                if ($childChildChildCategory->is_product_class)
                                    $options .= '<option value="' . $childChildChildCategory->category_id . '">     ----' . $childChildChildCategory->cat_name . '</option>';
                                else
                                    $options .= '<option value="' . $childChildChildCategory->category_id . '" disabled="true">     ----' . $childChildChildCategory->cat_name . '</option>';
                            }
                        } else {

                            if ($childChildCategory->is_product_class)
                                $options .= '<option value="' . $childChildCategory->category_id . '">   --' . $childChildCategory->cat_name . '</option>';
                            else
                                $options .= '<option value="' . $childChildCategory->category_id . '" disabled="true">   --' . $childChildCategory->cat_name . '</option>';
                        }
                    }
                } else {
                    if (!empty($childCategory) && $childCategory->is_product_class) {

                        $options .= '<option value="' . $childCategory->category_id . '">   --' . $childCategory->cat_name . '</option>';
                    } else {
                        $options .= '<option value="' . $childCategory->category_id . '" disabled="true">' . $childCategory->cat_name . '</option>';
                    }
                }
            }
        }
        return $options;
    }

    public function downloadTOTExcel() {
        $category_id = Input::get('category');
        $warehouse_id = Input::get('warehouse_id');
        //$not_to_modify = 'Manifacture ID -' . $manufacture_id . ',Category ID -' . $category_id;
        $input_manufacturer_id = Input::get('manufacturer_id');
        $cat_data = array();
        $supplier_id = Session::get('supplier_id');
        $cat_data[] = 'Category ID -' . $category_id;
        $cat_data[] = 'Warehouse ID -' . $warehouse_id;
        //$legalentity_id = Session()->get('legalentity_id');
        $legalentity_id = (Session::get('legalentity_id'))?Session::get('legalentity_id'): Session::get('add_continue');
        $legal_entity_id = Session()->get('legal_entity_id');
        $pim_data = array(
            'Product Id', 'Category', 'Brand', 'Product Title', 'Supplier Product Code', 'Product Name', 'ELP','Actual ELP', 'Ebutor Margin(%)', 'PTR','Supplier DC Relationship','GRN Freshness Percentage', 'Tax Type', 'Tax %', 'MOQ', 'MOQ UOM', 'Delivery TAT', 'Delivery TAT UOM', 'GRN Days', 'RTV Allowed', 'Inventory Mode', 'ATP', 'ATP Period',
            'KVI', 'Is Preferred Supplier', 'Effective Date(MM-DD-YYYY)');

		$required_data = array('product_id' => 'required', 'category'=>'', 'brand_id' => '', 'product_title' => '', 'supplier_product_code' => '', 'product_name' => 'required', 'elp' => '','actual_elp' => 'required', 'ebutor_margin' => '', 'ptr' => '', 'supplier_dc_relationship' => '', 'grn_freshness_percentage' => '', 'tax_type' => '', 'tax' => '', 'moq' => '', 'moq_uom' => '', 'delivery_tat' => '', 'delivery_tat_uom' => '', 'grn_days' => '', 'rtv_allowed' => '', 'inventory_mode' => '', 'atp' => '', 'atp_period' => '',
		'kvi' => '', 'is_preferred_supplier' => '', 'effective_datemm_dd_yyyy' => ''); 




		$data['cat_data'] = array(
            $cat_data,
			$required_data,
            $pim_data
        );
        $lookup_options = array();
        $lookupObj = new MasterLookup();
        $brandObj = new BrandModel();
        $inventory_modes = $lookupObj->getInventoryModes();
        $margin_types = $lookupObj->getMaginTypes();
        $returns_location_type = $lookupObj->getReturnLocationTypes();
        $Length_UOM = $lookupObj->getLengthUOM();
        $Capacity_UOM = $lookupObj->getCapacityUOM();
        $taxTypes = $lookupObj->getTaxType();
        $atpPeriod = $lookupObj->getAtpPeriod();
        $PackSize_UOM = $lookupObj->getPackSizeUOM();

        $Supplier_DC_Relationship_Lookup = $lookupObj->getSupplierDCRel();
        $MOQ_UOM = $lookupObj->getEachesLookup();
        $Delivery_TAT_UOM = $lookupObj->getShelfLife();
        $KVI_Lookup = $lookupObj->getKVI();
		$Supplier_Rank_Lookup = $lookupObj->getSuppliersRank();
		$GRN_Days_Lookup = $lookupObj->getGrnDays();


		$legalEntityIdArray=array();
		$child_legal_entity_id = DB::table('legal_entities')->select('legal_entity_id')->where(['parent_id'=> $legal_entity_id,'legal_entity_type_id'=>'1006'])->get()->all();
		foreach ($child_legal_entity_id as $val)
		{
			$legalEntityIdArray[] = $val->legal_entity_id;
		}
		
		
		$Brands = $brandObj->getBrandsBySupplierId($legalEntityIdArray);


        $headings = array('Supplier DC Relationship', 'Tax Type',  'MOQ UOM', 'Delivery TAT UOM','GRN Days','Inventory Mode', 'ATP Period','KVI');
        $array_count = array(
			'Supplier_DC_Relationship_Lookup' => count($Supplier_DC_Relationship_Lookup),
            'TaxTypes' => count($taxTypes),
            'MOQ_UOM' => count($MOQ_UOM),
            'Delivery_TAT_UOM' => count($Delivery_TAT_UOM),
            'GRN_Days' => count($GRN_Days_Lookup),
            'Inventory_mode' => count($inventory_modes),
            'ATPPeriod' => count($atpPeriod),
            'KVI_Lookup' => count($KVI_Lookup),
        );
        $sort = arsort($array_count);
        $return_accept = array('Yes', 'No');
        $data['options'][] = $headings;
        for ($i = 1; $i <= max($array_count); $i++) {
            $data['options'][$i][] = (isset($Supplier_DC_Relationship_Lookup[$i - 1])) ? $Supplier_DC_Relationship_Lookup[$i - 1]->name : '';
            $data['options'][$i][] = (isset($taxTypes[$i - 1])) ? $taxTypes[$i - 1]->name : '';
            $data['options'][$i][] = (isset($MOQ_UOM[$i - 1])) ? $MOQ_UOM[$i - 1]->name : '';
            $data['options'][$i][] = (isset($Delivery_TAT_UOM[$i - 1])) ? $Delivery_TAT_UOM[$i - 1]->name : '';
            $data['options'][$i][] = (isset($GRN_Days_Lookup[$i - 1])) ? $GRN_Days_Lookup[$i - 1] : '';
            $data['options'][$i][] = isset($inventory_modes[$i - 1]) ? $inventory_modes[$i - 1]->name : '';
            $data['options'][$i][] = (isset($atpPeriod[$i - 1])) ? $atpPeriod[$i - 1]->name : '';
            $data['options'][$i][] = (isset($KVI_Lookup[$i - 1])) ? $KVI_Lookup[$i - 1]->name : '';
        }

		$PackSize_UOM_Values = array();
		
        foreach ($PackSize_UOM as $Temp) {
            $PackSize_UOM_Values[$Temp->value] = $Temp->name;
        }

		$Capacity_UOM_Values = array();

        foreach ($Capacity_UOM as $Temp) {
            $Capacity_UOM_Values[$Temp->value] = $Temp->name;
        }

        $Tax_Type_Values = array();

        foreach ($taxTypes as $Temp) {
            $Tax_Type_Values[$Temp->value] = $Temp->name;
        }

        $Atp_Period_Values = array();

        foreach ($atpPeriod as $Temp) {
            $Atp_Period_Values[$Temp->value] = $Temp->name;
        }

        $Inventory_Modes_Values = array();

        foreach ($inventory_modes as $Temp) {
            $Inventory_Modes_Values[$Temp->value] = $Temp->name;
        }

        $Margin_Types_Values = array();

        foreach ($margin_types as $Temp) {
            $Margin_Types_Values[$Temp->value] = $Temp->name;
        }

        $Returns_Location_Type_Values = array();

        foreach ($returns_location_type as $Temp) {
            $Returns_Location_Type_Values[$Temp->value] = $Temp->name;
        }

        $Length_UOM_Values = array();

        foreach ($Length_UOM as $Temp) {
            $Length_UOM_Values[$Temp->value] = $Temp->name;
        }

        $Brands_Values = array();
        foreach ($Brands as $Temp) {
            $Brands_Values[$Temp->brand_id] = $Temp->brand_name;
        }



        $Supplier_DC_Relationship_Values = array();

        foreach ($Supplier_DC_Relationship_Lookup as $Temp) {
            $Supplier_DC_Relationship_Values[$Temp->value] = $Temp->name;
        }
		
        $MOQ_UOM_Values = array();

        foreach ($MOQ_UOM as $Temp) {
            $MOQ_UOM_Values[$Temp->value] = $Temp->name;
        }

        $Delivery_TAT_Values = array();
        foreach ($Delivery_TAT_UOM as $Temp) {
            $Delivery_TAT_Values[$Temp->value] = $Temp->name;
        }
		
        $KVI_Values = array();
        foreach ($KVI_Lookup as $Temp) {
            $KVI_Lookup[$Temp->value] = $Temp->name;
        }
		

		
		
        if (Input::get('with_data') != '') {
            $check_data = array('no', 'yes');
            $is_margin_arr = array('Markdown', 'Markup');
            $products_query = DB::table('product_tot')->select('products.product_id', 'brands.brand_id','categories.cat_name', 'dlp','actual_elp', 'rlp', 'supplier_sku_code', 'brands.brand_name', 'product_tot.product_name', 'products.product_title', 'product_tot.supplier_sku_code', 'products.upc_type as product_code_type', 'products.upc as product_code', 'products.mrp', 'product_tot.dlp as elp', 'rlp as ptr', 'distributor_margin', 'tax', 'tax_type', 'inventory_mode', 'atp', 'moq', 'tax', 'moq_uom', 'atp', 'atp_period', 'delivery_tat_uom', 'return_location_type', 'delivery_terms as delivery_tat','grn_days','rtv_allowed','product_tot.kvi','is_preferred_supplier','effective_date','supplier_dc_relationship','grn_freshness_percentage');

            $products_query->join('products', function($join) use($input_manufacturer_id) {
                $join->on('products.product_id', '=', 'product_tot.product_id');
				if($input_manufacturer_id!='' && $input_manufacturer_id!=0) {
                $join->on('products.manufacturer_id', '=', DB::raw($input_manufacturer_id));
				}
            });

            $products_query->leftjoin('brands', 'brands.brand_id', '=', 'products.brand_id');

			$products_query->leftjoin('categories', 'categories.category_id', '=', 'products.category_id');

            $products_query->leftjoin('product_content', 'product_content.product_id', '=', 'products.product_id');

            if($category_id!='' && $category_id!=0)
			{
				$products_query->where('products.category_id', $category_id);
			}

			$products_query->where('product_tot.supplier_id', $legalentity_id)
                    ->where('product_tot.subscribe', '1')->where('product_tot.le_wh_id',$warehouse_id);



            $products = $products_query->get()->all();

			
            $product_data = json_decode(json_encode($products), True);


            foreach ($product_data as $product) {
                $product_id = $product['product_id'];
                $prod_slabs = ProductSlabRates::select('start_range', 'end_range', 'price')
                        ->where('product_id', $product_id)
                        ->get()->all();

                $Brand_Id = $product['brand_id'];


                /*$w_uom = MasterLookup::select('description')
                        ->where('value', $product['weight_uom'])
                        ->first();*/
                if($product['return_location_type']!='')
				{
					$ret_loc_type = MasterLookup::select('description')
							->where('value', $product['return_location_type'])
							->first();
				}
                $product_parent = ProductRelations::where('product_id',$product_id)->first();
				$parent_id = '';
                if(count($product_parent)>0 && isset($product_parent->parent_id)){
                    $parent_id = $product_parent->product_id;
                }

				$is_preferred_supplier = ($product['is_preferred_supplier']=='0') ? 'No' : 'Yes';
				$rtv_allowed = ($product['rtv_allowed']=='0') ? 'No' : 'Yes';
				
				
//$Supplier_DC_Relationship_Values, $MOQ_UOM_Values, $Delivery_TAT_Values, $Supplier_Rank_Values
                $prod = array(
                    'product_id' => $product['product_id'],
                    'category' => $product['cat_name'],
                    'brand_id' => $product['brand_name'],
                    'product_title' => $product['product_title'],
                    'supplier_product_code' => $product['supplier_sku_code'],
                    'product_name' => $product['product_name'],
                    'elp' => $product['dlp'],
                    'actual_elp' => $product['actual_elp'],
                    'ebutor_margin' => $product['distributor_margin'],
                    'ptr' => $product['rlp'],
                    'supplier_dc_relationship' => (isset($Supplier_DC_Relationship_Values[$product['supplier_dc_relationship']])) ? $Supplier_DC_Relationship_Values[$product['supplier_dc_relationship']] : '',
					'grn_freshness_percentage' => $product['grn_freshness_percentage'],
                    'tax_type' => (isset($Tax_Type_Values[$product['tax_type']])) ? $Tax_Type_Values[$product['tax_type']] : '',
                    'tax' => $product['tax'],
                    'moq' => $product['moq'],
                    'moq_uom' => (isset($MOQ_UOM_Values[$product['moq_uom']])) ? $MOQ_UOM_Values[$product['moq_uom']] : '',
                    'delivery_tat' => $product['delivery_tat'],
                    'delivery_tat_uom' => (isset($Delivery_TAT_Values[$product['delivery_tat_uom']])) ? $Delivery_TAT_Values[$product['delivery_tat_uom']] : '',
                    'grn_days' => $product['grn_days'],
                    'rtv_allowed' => $rtv_allowed,
                    'inventory_mode' => (isset($Inventory_Modes_Values[$product['inventory_mode']])) ? $Inventory_Modes_Values[$product['inventory_mode']] : '',
                    'atp' => $product['atp'],
                    'atp_period' => (isset($Atp_Period_Values[$product['atp_period']])) ? $Atp_Period_Values[$product['atp_period']] : '',
                    'kvi' => (isset($KVI_Lookup[$product['kvi']])) ? $KVI_Lookup[$product['kvi']] : '',
                    'is_preferred_supplier' => $is_preferred_supplier,
					'effective_date' => $product['effective_date']
                );


                /* 				foreach ($prod_slabs as $key => $slabs) {
                  $prod['start_range' . $key] = $slabs->start_range;
                  $prod['end_range' . $key] = $slabs->end_range;
                  $prod['price' . $key] = $slabs->price;
                  } */
                $data['cat_data'][] = $prod;
            }
        }
        $file_name = 'TOT Template_' . $category_id;
        $result = Excel::create($file_name, function($excel) use($data) {
                    $excel->sheet('Sheet1', function($sheet) use($data) {
                        $sheet->fromArray($data['cat_data'], null, 'A1', false, false);
                        $sheet->protectCells('A1', 'password');
                        $sheet->protectCells('B1', 'password');
                    });
                    // Our second sheet
                    $excel->sheet('Sheet2', function($sheet) use($data) {
                        $sheet->fromArray($data['options'], null, 'A1', false, false);
                    });
                    // Set sheets
                })->export('xlsx');
        exit;
    }

    function googlepincode(Request $resquest) {
        $pincode = $resquest->pincode;
       $pincode = $resquest->pincode;
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$pincode}&key=AIzaSyDxnWRzkxLyWEVsaNA1DkCQOw_4nbwc8to";
        $callType = "GET";
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

        return $output;
    }

    public function warehuniq(Request $request, $legalentity_id) {
        $warehouse = new SupplierWarehouseModel();
        $whexists = new Suppliers();
        $whId = Session('warehouse_id');
        $warehouse->legal_entity_id = $legalentity_id;
        $where = ['sp_wh_name' => $request->input('wh_name'), 'legal_entity_id' => $legalentity_id];
        $whexists = $warehouse->where($where)->first();
        //echo $request->wh_name; print_r($whexists->legal_entity_id); die;
        if (isset($whId) && isset($whexists) && $whexists->legal_entity_id && $whexists->legal_entity_id == $legalentity_id && $whId != $whexists->sp_wh_id) {
            echo "false";
            exit;
        }
        if (!isset($whId) && isset($whexists) && $whexists->legal_entity_id == $legalentity_id) {
            echo "false";
            exit;
        } else {
            echo "true";
        }
    }

    public function productsbybrand(Request $request) {
        $brand = $request->brand;

        $products = DB::table('products')
                ->select('products.product_id', 'products.product_title')
                ->where('products.brand_id', '=', $brand)
                ->orderBy('products.product_title', 'asc')
                ->get()->all();

        //$products = ProductModel::where('brand_id',$brand)
        //->orderBy('product_title','desc')
        //->get();
        $dropdown = '<option value="">Select Product</option>';
        foreach ($products as $product) {
            $dropdown .= '<option value="' . $product->product_id . '">' . $product->product_title . '</option>';
        }
        return $dropdown;
    }

    public function categoriesbyproducts(Request $request) {
        $product_id = $request->product_id;
        $products = new ProductModel();
        $category_id = $products->select('category_id')->where('product_id', $product_id)->first();
        $categories = CategoriesModel::where('category_id', $category_id->category_id)
                ->orderBy('cat_name', 'desc')
                ->get()->all();
        $dropdown = '';
        //$dropdown='<option value="">Select Category</option>';
        foreach ($categories as $category) {
            $dropdown .= '<option value="' . $category->category_id . '">' . $category->cat_name . '</option>';
        }
        return $dropdown;
    }

    public function productotdetails(Request $request) {
        $product_id = $request->product_id;
        $brand = $request->brand;
        $category = $request->category;

        $Product_Model_Obj = new ProductModel();
        $query = $Product_Model_Obj::select(['products.product_id', 'brands.brand_name', 'categories.category_id', 'product_tot.product_name', 'product_content.short_description', 'products.product_title', 'products.seller_sku', 'product_tot.mrp', 'product_tot.msp', 'product_tot.rlp', 'product_tot.dlp', 'product_tot.cbp', 'product_tot.credit_days', 'product_tot.return_location_type', 'product_tot.delivery_terms', 'product_tot.is_return_accepted', 'product_tot.base_price', 'products.upc',
                    'product_tot.is_markup', 'product_tot.inventory_mode', 'products.is_active']);

        $query->leftjoin('brands', 'brands.brand_id', '=', 'products.brand_id');
        $query->leftjoin('categories', 'categories.category_id', '=', 'products.category_id');
        $query->leftjoin('product_tot', 'product_tot.product_id', '=', 'products.product_id');
        $query->leftjoin('product_content', 'product_content.product_id', '=', 'products.product_id');

        $query->where('products.product_id', '=', $product_id);

        $productdetails = $query->get()->all();

        return $productdetails;
    }      

    public function getProductsOnBrand(Request $request, $Brand_Id, $Wh_id) {
        $legalentity_id =(Session()->get('legalentity_id'))?Session()->get('legalentity_id'):Session()->get('add_continue');
        try {
            $fields =  array(
                'business_legal_name' => 'le.business_legal_name',
                'group_repo'          => 'pr.product_title',
                'brand_name'           => 'brands.brand_name',
                'ProductName'         => 'p.product_title',
                'ELP'                 => 'pt.dlp',                
                'Bestprice'           => 'pt.base_price',                
                'TaxType'             => 'pt.tax_type',                
                'Tax'                 => 'pt.tax',                
                'PTR'                 => 'pt.rlp',                
                'ATP'                 => 'pt.atp',                
                'cat_name'            => 'ct.cat_name',
                'sku'                 => 'p.sku'  
                    //'Count' => 'product_relations.count'
            );

            $query = DB::table('products as p')
                    ->leftjoin('product_tot as pt', function($join) use($Wh_id, $legalentity_id) {
                        $join->on('p.product_id', '=', 'pt.product_id');
                        $join->on('pt.le_wh_id', '=', DB::raw($Wh_id));
                        $join->on('pt.supplier_id', '=', DB::raw($legalentity_id));
                    })
                    ->leftJoin('brands', 'brands.brand_id', '=', 'p.brand_id')
                    ->where('p.manufacturer_id', $Brand_Id)
                    ->select('p.product_id as ProductID', 'p.sku', DB::raw('getLeWhName('.$Wh_id.')'), 'brands.brand_name AS brand_name',
                            DB::raw('getCategoryName(p.category_id) as Category'), 'p.product_title as ProductName', 'pt.dlp as ELP',
                            'pt.base_price as Bestprice', 'pt.tax_type as TaxType', 'pt.tax as Tax', 'pt.rlp as PTR',
                            DB::raw('getMastLookupValue(pt.inventory_mode) AS InventoryMode'), 'pt.atp AS ATP',
                            DB::raw('getMastLookupValue(pt.atp_period) AS ATPPeriod'),
                            DB::raw('getprdSubscriptionStaus(' . $legalentity_id . ', p.product_id, ' . $Wh_id . ') AS Subc_status'));

            $product_lists_array = $this->_supplierModel->getGridData($request,$fields,$query);        
            $product_lists = (isset($product_lists_array[0]))?$product_lists_array[0]:array();
            $i=0;
            foreach ($product_lists as $product_list) {

                if ($product_list->Subc_status == 1) {
                    $action = '<label class="switch "><input class="switch-input enableDisableProducttot enable" data_attr_productid=' . $product_list->ProductID . ' type="checkbox" checked>'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $action = '<label class="switch "><input class="switch-input enableDisableProducttot enable" data_attr_productid=' . $product_list->ProductID . ' type="checkbox">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }
                $product_lists[$i]->actions = $action;
                $i++;
            }

            $product_lists = json_decode(json_encode($product_lists), true);
            foreach ($product_lists as $key => $value) {
                $tax_type_name = DB::table('master_lookup')->where('mas_cat_id', 9)->where('value', $value['TaxType'])->pluck('master_lookup_name')->all();
                $product_lists[$key]['TaxType'] = (!empty($tax_type_name)) ? $tax_type_name[0] : '';
            }
            $resCount = (isset($product_lists_array[1]))?$product_lists_array[1]:array();
            return json_encode(array('Records' => $product_lists, 'TotalRecordsCount' => $resCount));
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }	
	
    public function getinventorymodes() {
        $inventory_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '45')
                ->where('master_lookup_categories.mas_cat_name', '=', 'invetory_mode')
                ->get()->all();

        $dropdown = '<option value="">Select Inventory Mode</option>';
        foreach ($inventory_data as $inventory_data) {
            $dropdown .= '<option value="' . $inventory_data->value . '">' . $inventory_data->account_type . '</option>';
        }
        return $dropdown;
    }

	public function downloadTemplate($type)
	{
                //$data = array(array('Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>''));
                $data2 = array(array('WarehouseName(Required)'=>'', 'ContactName(Required)'=>'','Address(Required)'=>'', 'PhoneNumber(Required)'=>'', 'Email(Required)'=>'', 'Country(Required)'=>'', 'State(Required)'=>'', 'City(Required)'=>'', 'Pincode(Required)'=>'', 'Longitude(Required)'=>'', 'Latitude(Required)'=>''));
		
                $sheetName = "warehouse_import_".Carbon::now();
		return Excel::create($sheetName, function($excel) use ($data2) {
		
			$excel->sheet('Warehouse_import', function($sheet) use ($data2)
	        {
	           // $sheet->fromArray($data);
                    $sheet->fromArray($data2);
				
	        });
		})->download($type);
	}
        
        public function importExcel($legalentity_id) {
         $save = array();   
        if (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $ext = Input::file('import_file')->getClientOriginalExtension();
            if ($ext != "xls") {
                echo "Invalid Format!";
                exit;
            } else {
                
                $headerRowNumber = 1;
                Config::set('excel.import.startRow', $headerRowNumber);
                $states = $this->getStates();
                $countries = $this->getCountries();
                $data = Excel::load($path, function($reader) {                       
                        })->get();
                $insertedCount = 0;
                
                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {

                        $warehouse = new SupplierWarehouseModel();
                        $whexists = new SupplierWarehouseModel();
                        $warehouse->legal_entity_id = $legalentity_id;
                        $where = ['sp_wh_name'=>$value->warehousenamerequired,'legal_entity_id'=>$legalentity_id];
                        $whexists = $warehouse->where($where)->first();
                        //echo $value->warehousenamerequired; print_r($whexists->sp_wh_id); die;
                        $error = '';
                        if(preg_match('/^[1-9][0-9]{5}$/', $value->pincoderequired) == 0)
                        {
                          $error = "Pincode Invalid";  
                        }
                        if(preg_match('/^[a-zA-Z ]+$/', $value->contactnamerequired) == 0)
                        {
                          $error = "ContactName Invalid";  
                        }
                        if(preg_match('/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/', $value->emailrequired) == 0)
                        {
                           $error =  "Email Invalid";
                        }
                        if(preg_match('/^[1-9][0-9]{9,11}$/', $value->phonenumberrequired) == 0)
                        {
                             $error =  "Phone Number Invalid";
                        }
                        if(preg_match('/^[1-9][0-9]{5}$/', $value->pincoderequired) == 0)
                        {
                            $error =  "Pincode Invalid";
                        }
                        if(preg_match('/^[a-zA-Z ]*\z/', $value->cityrequired) == 0)
                        {
                            $error =  "City Invalid";
                        }
                        if(preg_match('/^[a-zA-Z ]*\z/', $value->staterequired) == 0)
                        {
                           $error =  "State Invalid"; 
                        }
                        if(preg_match('/^[a-zA-Z ]*\z/', $value->countryrequired) == 0)
                        {
                            $error =  "Country Invalid";
                        }
                        if($value->latituderequired != '' && (preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}/', $value->latituderequired) == 0))
                        {
                            $error =  "Latitude Invalid";
                        }
                        if($value->longituderequired != '' && (preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}/', $value->longituderequired) == 0))
                        {
                            $error =  "Longitude Invalid";
                        }
                        if(isset($whexists) && $whexists->sp_wh_id)
                        {
                           $error =  "Warehouse Name Should Be Unique";
                        }
                        if((isset($whexists) && $whexists->sp_wh_id) || ($value->warehousenamerequired=='') || ($value->contactnamerequired=='') || ($value->addressrequired=='') || ($value->phonenumberrequired=='') || ($value->emailrequired=='') || ($value->countryrequired=='') || ($value->staterequired=='') || ($value->cityrequired=='') || ($value->pincoderequired=='') || (preg_match('/^[a-zA-Z ]+$/', $value->contactnamerequired)==0) || (preg_match('/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/', $value->emailrequired)==0) || (preg_match('/^[1-9][0-9]{9,11}$/', $value->phonenumberrequired)==0) || (preg_match('/^[1-9][0-9]{5}$/', $value->pincoderequired)==0) || (preg_match('/^[a-zA-Z ]*\z/', $value->cityrequired)==0) || (preg_match('/^[a-zA-Z ]*\z/', $value->staterequired)==0) || (preg_match('/^[a-zA-Z ]*\z/', $value->countryrequired)==0) || (preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}/', $value->longituderequired)==0 && $value->longituderequired != '') || (preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}/', $value->latituderequired)==0 && $value->latituderequired != '') )
                        {
                            continue;                            
                        }
                        $warehouse->sp_wh_name = $value->warehousenamerequired;
                        $warehouse->contact_name = $value->contactnamerequired;
                        $warehouse->address1 = $value->addressrequired;
                        $warehouse->phone_no = $value->phonenumberrequired;
                        $warehouse->email = $value->emailrequired;
                        
                        $countries = countries::all();
                        $countries = json_decode(json_encode($countries), true);
                        
                        
                        $states = ZoneModel::all();
                        $states = json_decode(json_encode($states), true);
                        
                        foreach($countries as $country)
                        {
                            if(str_replace(' ', '',strtolower($country['name'])) == str_replace(' ', '',strtolower($value->countryrequired)))
                            {
                                $warehouse->country = $country['country_id'];
                            }
                        }
                        foreach($states as $state)
                        {
                            if(str_replace(' ', '',strtolower($state['name'])) == str_replace(' ', '',strtolower($value->staterequired)))
                            {
                                $warehouse->state = $state['zone_id'];
                            }
                        }                        
                        
                        $warehouse->city = $value->cityrequired;
                        $warehouse->pincode = $value->pincoderequired;
                        $warehouse->longitude = $value->longituderequired;
                        $warehouse->latitude = $value->latituderequired;
                        //print_r($warehouse); die;
                        $save[] = $warehouse->save();
                        $insertedCount++;
                    }
                    if (!$save) {
                        echo "Uploading warehouses is unsuccessful. Please check xls"."(".$error.")";
                    } else {
                        echo "Sucessfully inserted " . $insertedCount . " Record(s)";
                    }
                }
            }
        } else {
            echo "Please Select File";
            exit;
        }
    }

     public function dcInventory ($supplier_id) {   
        try { 
        $dc_data = DB::table('supplier_le_wh_mapping as sp')                
                   ->leftjoin('products as pr', 'sp.product_id','=','pr.product_id')
                   ->leftjoin('legalentity_warehouses as le', 'le.le_wh_id', '=', 'sp.le_wh_id')
                   ->where('sp.supplier_id', $supplier_id)
                   ->select('pr.product_id', 'pr.product_title', 'le.lp_wh_name', 'sp.atp')
                   ->get()->all();
        return json_encode(['Records'=>$dc_data]);
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        } 
        
    }
    
    public function agreementTerms(Request $request)
    {
        $user_id = Session::get('userId');
        $addContinueId = Session::get('add_continue');
        if (isset($_SERVER["HTTP_REFERER"]))
        {
            $referer = $_SERVER["HTTP_REFERER"];
            $urlArray = explode('/', $referer);
            $is_provider = isset($urlArray[3]) ? $urlArray[3] : '';
            $is_edit_id = (isset($urlArray[5])) ? $urlArray[5] : 0;
        }
        $final_id = ($is_edit_id == 0) ? $addContinueId : $is_edit_id;
       
        $le_type = DB::table('legal_entities')->where('legal_entity_id', $final_id)->pluck('legal_entity_type_id');
        $le_type_id = (isset($le_type[0])) ? $le_type[0] : 0;
        Log::info("Suppler terms update hit");
        if (!$final_id)
        {
            echo "No Supplier";
        }
        else
        {
            $SuppliertermsModel = new SuppliertermsModel();
            $is_exist = $SuppliertermsModel::where(['legal_entity_id' => $final_id])->select('terms_id')->first();
            $is_exist = isset($is_exist->terms_id)? $is_exist->terms_id : 0;
            if ($is_exist)
            {
                $SuppliertermsModel = $SuppliertermsModel::find($is_exist);
            }
            else
            {
                $SuppliertermsModel->legal_entity_id = $final_id;
            }
            
            if ($is_provider == 'suppliers')
            {
                Log::info("Suppler terms update in function");
                $SuppliertermsModel->vendor_reg_charges = $request->vendorreg_charges;
                $SuppliertermsModel->sku_reg_charges = $request->skureg_charges;
                $SuppliertermsModel->dc_link_charges = $request->dclinking_charges;
                $SuppliertermsModel->b2b_channel_support_as = $request->btbchannel_supportassistance;
                $SuppliertermsModel->ecp_visibility_ass = $request->ecp_visibilityassistance;
                $SuppliertermsModel->po_days = $request->po_days;
                $SuppliertermsModel->delivery_tat = $request->delivery_tat;
                $SuppliertermsModel->delivery_tat_uom = $request->delivery_tatuom;
                $SuppliertermsModel->invoice_days = $request->invoice_days;
                $SuppliertermsModel->delivery_frequency = $request->delivery_frequency;
                if($SuppliertermsModel->credit_period != $request->credit_period){
                    $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                    $comment = "Credit Period Changed </br>";
                    $comment .= "Credit Period : ";
                    if($SuppliertermsModel->credit_period != "")
                        $comment .= $SuppliertermsModel->credit_period." to ";
                    $comment .= $request->credit_period;
                    $current_time = Carbon::now()->toDateTimeString();
                    Suppliers::where("legal_entity_id",$final_id)
                      ->update(['updated_by'=>$user_id,'updated_at'=>$current_time,'status'=>57009,'is_approved'=>0]);
                    $approvalFlowObj->storeWorkFlowHistory('Supplier', $final_id, 57008, 57009, $comment, $user_id);
                }
                $SuppliertermsModel->credit_period = $request->credit_period;
                $SuppliertermsModel->payment_days = $request->payment_days;
                $SuppliertermsModel->negotiation = $request->negotiation;
                $SuppliertermsModel->rtv = $request->rtv;
                $SuppliertermsModel->rtv_timeline = $request->rtv_timeline;
                $SuppliertermsModel->rtv_scope = $request->rtv_scope;
                $SuppliertermsModel->rtv_location = $request->rtv_location;
                $SuppliertermsModel->start_date = $request->start_date;
                $SuppliertermsModel->end_date = $request->end_date;
                $SuppliertermsModel->created_by = $user_id;
            }
            else
            {
                $SuppliertermsModel->start_date = $request->start_date;
                $SuppliertermsModel->end_date = $request->end_date;
                $SuppliertermsModel->created_by = $user_id;
            }
			$SuppliertermsModel->le_type_id = $le_type_id;
            $SuppliertermsModel->save();
            Log::info("Suppler terms updated");
            echo "true";
        }
    }

    public function suppuniq(Request $request) {
        $org_name = $request->organization_name;
        $referer = $_SERVER["HTTP_REFERER"];
        $urlArray = explode('/',$referer);
        $is_edit  = array_pop($urlArray);
        if($is_edit == 'add')
        {
        $leCounts = DB::table('legal_entities')->select(DB::RAW('COUNT(legal_entity_id) AS COUNT'))->where('business_legal_name',$org_name)->get()->all();
        $count = (isset($leCounts[0]))?$leCounts[0]:0;        
                    
           if($count->COUNT > 0)
           {
             echo "false";  
           }
           else
           {
               echo "true";
           }
        }
        else
        {
            echo "true";
        }        
    }
    public function uniqueEmail(Request $request) {
        
        
        $org_email = $request->org_email;
        $referer = $_SERVER["HTTP_REFERER"];
        $urlArray = explode('/',$referer);
        $is_edit  = array_pop($urlArray);
        if($is_edit == 'add')
        {
        $leCounts = DB::table('users')->select(DB::RAW('COUNT(user_id) AS COUNT'))->where('email_id',$org_email)->get()->all();
        $count = (isset($leCounts[0]))?$leCounts[0]:0;        
                    
           if($count->COUNT > 0)
           {
             echo "false";  
           }
           else
           {
               echo "true";
           }
        }
        else
        {
            echo "true";
        }
    }

    public function prdwhmapping(Request $request) {
        $ProductTOT = new ProductTOT();
        $Type = $request->input('flag');  // enable or diable
        $ProductID = $request->input('ProductId');
        $user_id = Session::get('userId');

        $legalentity_id = Session()->get('legalentity_id');
        $supplier_id = Session()->get('supplier_id');
        $DcId = $request->input('DcId');
        
        
        $wh_prd_parent = $ProductTOT::where('supplier_id', $supplier_id)
                                                 ->where('product_id', $ProductID) 
                                                 ->where('le_wh_id', $DcId) 
                                                 ->first();
        if(empty($wh_prd_parent) && count($wh_prd_parent) == 0)
        {
        $Tot_Array = array(
                'le_wh_id' => $DcId,
                'product_id' => $ProductID,
                'supplier_id' => $legalentity_id,
	        'is_active'=>$Type,
                'created_by'=>$user_id            
            );
            ProductTOT::insert($Tot_Array);
           echo "inserted";
        }
        else {
            $Qery = ProductTOT::where('supplier_id', '=', $legalentity_id);
            $Qery->where('product_id', '=', $ProductID)->update(array('is_active' => $Type));
            echo "updated";
        }
        
        $child_data = DB::table('product_relations as pr')
                    ->leftjoin('vw_cp_products as vw', 'vw.product_id', '=', 'pr.product_id')
                    ->where('pr.parent_id', $ProductID)
                    ->select('vw.product_id')
                    ->get()->all();
        foreach($child_data as $chlid_product)
        {
        $wh_prd = $ProductTOT::where('supplier_id', $supplier_id)
                                                 ->where('product_id', $chlid_product->product_id) 
                                                 ->where('le_wh_id', $DcId) 
                                                 ->first();
        if(empty($wh_prd) && count($wh_prd) == 0)
        {
        $Tot_Array = array(
                'le_wh_id' => $DcId,
                'product_id' => $chlid_product->product_id,
                'supplier_id' => $legalentity_id,
	        'is_active'=>$Type,
                'created_by'=>$user_id            
            );
            ProductTOT::insert($Tot_Array);
           echo "inserted";
        }
        else {
            $Query = ProductTOT::where('supplier_id', '=', $legalentity_id);
            $Query->where('product_id', '=', $chlid_product->product_id)->update(array('is_active' => $Type));
            echo "updated";
        }        

        }
    }
	
	public function totmapping(Request $request) {
        $ProductTOT = new ProductTOT();
        $Type = $request->input('flag');  // enable or diable
        $ProductID = $request->input('ProductId');
        $user_id = Session::get('userId');

        $legalentity_id = (Session::get('legalentity_id'))?Session::get('legalentity_id'): Session::get('add_continue');
		
		$referer = $_SERVER["HTTP_REFERER"];
        $urlArray = explode('/',$referer);
		$urlLastParam = array_pop($urlArray);
		
		if(trim($urlLastParam) == 'add')
		{
			$supplier_id = Session()->get('supplier_id');
		}
        else
		{
			$supplier_id = $request->input('supplier_id');  
		}
              
        $DcId = $request->input('DcId');


        $wh_prd_parent = $ProductTOT::where('supplier_id', $supplier_id)
                ->where('product_id', $ProductID)
                ->where('le_wh_id', $DcId)
                ->first();
        if (empty($wh_prd_parent) && count($wh_prd_parent) == 0) {
            
			
			$WhInfo = DB::table('legalentity_warehouses')->where('le_wh_id',$DcId)->pluck('state');
			
			$stateidSeller = ($WhInfo[0]) ? $WhInfo[0] : 0;
			
			$SupInfo = DB::table('suppliers')->where('legal_entity_id',$supplier_id)->pluck('sup_state');
			$stateidBeller = ($SupInfo[0]) ? $SupInfo[0] : 0;
			
			$getTax = $this->getTaxByState($ProductID, $stateidSeller, $stateidBeller);

			
			$ProductModel = new ProductModel();			
			
			$ProductInfo	=	$ProductModel::where('product_id', $ProductID)->pluck('product_title');
			
			$Product_Title	=	$ProductInfo[0];
			
			$Tot_Array = array(
                'le_wh_id' => $DcId,
                'product_id' => $ProductID,
                'product_name' => $Product_Title,
                'supplier_id' => $supplier_id,
                'subscribe' => $Type,
                'created_by' => $user_id,
				'supplier_dc_relationship' => 100001,
				'grn_freshness_percentage' => 90,
				'moq'=> 1,
				'moq_uom'	=>	16004,
				'delivery_terms' => 1,
				'delivery_tat_uom'=>71002,
				'grn_days'=>'MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY',
				'inventory_mode'=>45001,
				'atp'=>0,
				'atp_period'=>80002,
				'kvi'=>69002,
				'effective_date'=>date('Y-m-d')

            );


			if( $getTax['Status']=='200' ){

				$taxData = $getTax['ResponseBody'];

				$Tot_Array['tax'] = $taxData[0]['Tax Percentage'];
				
				$taxInfo = DB::table('master_lookup')->where(array('mas_cat_id'=>9,'master_lookup_name'=>$taxData[0]['Tax Type']))->pluck('value');
				
				$taxType = ($taxInfo[0]) ? $taxInfo[0] : 9003;

				$Tot_Array['tax_type'] = $taxType;
				
			}

            
			$totIncrId = ProductTOT::insertGetId($Tot_Array);
		
		/*if($Type == 1)
		{
			$PurchasePriceHistory = new PurchasePriceHistory();
			$PurchasePriceHistory->product_id = $ProductID;
			$PurchasePriceHistory->supplier_id = $supplier_id;
			$PurchasePriceHistory->le_wh_id = $DcId;
			$PurchasePriceHistory->prod_price_id = $totIncrId;
			$PurchasePriceHistory->created_by = Session::get('userId');
			$PurchasePriceHistory->save();
		}*/
            echo "inserted";
        } else {
            $Qery = ProductTOT::where('supplier_id', '=', $legalentity_id);
            $Qery->where('supplier_id', $supplier_id)
                ->where('product_id', $ProductID)
                ->where('le_wh_id', $DcId)->update(array('subscribe' => $Type));
            echo "updated";
        }
    }

    private function getTaxByState($product_id, $stateidSeller, $stateidBeller){

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
    public function editSetPrice($product_price_id) {
        try {            
            $data = DB::table('product_tot as tot')->select('tot.le_wh_id as wh_id','tot.supplier_id as supp_id',
                    'tot.product_id as prd_id','le.business_legal_name as supp_name','prod.product_title as prod_title',
                    'prod.mrp','prod.esu')
                    ->join('products as prod','prod.product_id','=','tot.product_id')
                    ->join('legal_entities as le','le.legal_entity_id','=','tot.supplier_id')
                    ->where('prod_price_id',$product_price_id)->get()->all();
            return json_encode($data);        
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function saveSetPrice(Request $request) {
        try {
            $date = date('Y-m-d', strtotime($request->get('set_price_date')));            
            $PurchasePriceHistory = new PurchasePriceHistory();
			$PurchasePriceHistory->product_id = $request->get('set_price_productId');
			$PurchasePriceHistory->supplier_id = $request->get('set_price_supId');
			$PurchasePriceHistory->le_wh_id = $request->get('set_price_whId');			
			$PurchasePriceHistory->elp = $request->get('set_price_elp');			
			$PurchasePriceHistory->effective_date = $date;                        
			$PurchasePriceHistory->created_by = Session::get('userId');
                        $PurchasePriceHistory->save();                
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
	
	public function getPurhaseHistory(Request $request)
    {

            $path = explode(':', $request->input('path'));
            $ProductPriceId = $path[1];
            
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;
		
		$tot = DB::table('product_tot')->where('prod_price_id',$ProductPriceId)->select('le_wh_id','product_id','supplier_id')->first();
		
		$leWhId = $tot->le_wh_id;
		$productId = $tot->product_id;
		$supplierId = $tot->supplier_id;
		
        $purHistory = new PurchasePriceHistory();

        $query = $purHistory::select(['pur_price_id as PurPriceId','elp as ELP','effective_date as EffectiveDate','created_at as CreatedAt']);
        $query->where(['le_wh_id'=>$leWhId,'product_id'=>$productId,'supplier_id'=>$supplierId]);

		
        if ($request->input('$orderby')) {    //checking for sorting


            $order = explode(' ', $request->input('$orderby'));


            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc

            $order_by_type = 'desc';

            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }


            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field

                $order_by = $this->grid_field_db_match[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }


        if ($request->input('$filter')) {           //checking for filtering

            $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


            foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                $filter = explode(' ', $post_filter_query_sub);

                $filter_query_field = $filter[0];
                $filter_query_operator = $filter[1];
                $filter_query_value = $filter[2];

                $filter_query_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                    if ($filter_query_substr == 'startsw') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = $filter_value_array[1] . '%';


                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }


                    if ($filter_query_substr == 'endswit') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = '%' . $filter_value_array[1];


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }




                    if ($filter_query_substr == 'tolower') {

                        $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = $filter_value_array[1];

                        if ($filter_query_operator == 'eq') {
                            $like = '=';
                        } else {
                            $like = '!=';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }

                    if ($filter_query_substr == 'indexof') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_value_array[1] . '%';

                        if ($filter_query_operator == 'ge') {
                            $like = 'like';
                        } else {
                            $like = 'not like';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }
                } else {

                    switch ($filter_query_operator) {
                        case 'eq' :

                            $filter_operator = '=';

                            break;

                        case 'ne':

                            $filter_operator = '!=';

                            break;

                        case 'gt' :

                            $filter_operator = '>';

                            break;

                        case 'lt' :

                            $filter_operator = '<';

                            break;

                        case 'ge' :

                            $filter_operator = '>=';

                            break;

                        case 'le' :

                            $filter_operator = '<=';

                            break;
                    }


                    if (isset($this->grid_field_db_match[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }

                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }

        $query->orderBy('purchase_price_history.created_at','desc');

        $row_count = count($query->get());

        //$query->skip($skip)->take($pageSize);
        $pur_history_list = $query->get()->all();




        echo json_encode(array('Records' => $pur_history_list, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);

        //end
    }
    public function saveTotMapping($ProductID,$supplier_id,$DcId,$Type,$user_id)
    {
        $ProductTOT = new ProductTOT();
        $wh_prd_parent = $ProductTOT::where('supplier_id', $supplier_id)
                ->where('product_id', $ProductID)
                ->where('le_wh_id', $DcId)
                ->first();
        if (empty($wh_prd_parent) && count($wh_prd_parent) == 0) {
            
            
            $WhInfo = DB::table('legalentity_warehouses')->where('le_wh_id',$DcId)->pluck('state');
            
            $stateidSeller = ($WhInfo[0]) ? $WhInfo[0] : 0;
            
            $SupInfo = DB::table('suppliers')->where('legal_entity_id',$supplier_id)->pluck('sup_state');
            $stateidBeller = ($SupInfo[0]) ? $SupInfo[0] : 0;
            
            $getTax = $this->getTaxByState($ProductID, $stateidSeller, $stateidBeller);

            
            $ProductModel = new ProductModel();         
            
            $ProductInfo    =   $ProductModel::where('product_id', $ProductID)->pluck('product_title');
            
            $Product_Title  =   $ProductInfo[0];
            
            $Tot_Array = array(
                'le_wh_id' => $DcId,
                'product_id' => $ProductID,
                'product_name' => $Product_Title,
                'supplier_id' => $supplier_id,
                'subscribe' => $Type,
                'created_by' => $user_id,
                'supplier_dc_relationship' => 100001,
                'grn_freshness_percentage' => 90,
                'moq'=> 1,
                'moq_uom'   =>  16004,
                'delivery_terms' => 1,
                'delivery_tat_uom'=>71002,
                'grn_days'=>'MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY',
                'inventory_mode'=>45001,
                'atp'=>0,
                'atp_period'=>80002,
                'kvi'=>69002,
                'effective_date'=>date('Y-m-d')

            );


            if( $getTax['Status']=='200' ){

                $taxData = $getTax['ResponseBody'];

                $Tot_Array['tax'] = $taxData[0]['Tax Percentage'];
                
                $taxInfo = DB::table('master_lookup')->where(array('mas_cat_id'=>9,'master_lookup_name'=>$taxData[0]['Tax Type']))->pluck('value');
                
                $taxType = ($taxInfo[0]) ? $taxInfo[0] : 9003;

                $Tot_Array['tax_type'] = $taxType;
                
            }

            
            $totIncrId = ProductTOT::insertGetId($Tot_Array);
        
        /*if($Type == 1)
        {
            $PurchasePriceHistory = new PurchasePriceHistory();
            $PurchasePriceHistory->product_id = $ProductID;
            $PurchasePriceHistory->supplier_id = $supplier_id;
            $PurchasePriceHistory->le_wh_id = $DcId;
            $PurchasePriceHistory->prod_price_id = $totIncrId;
            $PurchasePriceHistory->created_by = Session::get('userId');
            $PurchasePriceHistory->save();
        }*/
            return "inserted";
        } else {
            $Qery = ProductTOT::where('supplier_id', '=', $supplier_id);
            $Qery->where('product_id', '=', $ProductID)->update(array('subscribe' => $Type));
            return "updated";
        }
    }
    public function checkProvider(Request $request){
        $supplier_id = $request->input('supplier_id');  
         $flag = $request->input('flag'); 
         if($flag == 0)
         {
             $act  = 'De-Activate';
         }else
         {
             $act  = 'Activate';
         }
        $le_type = DB::table('legal_entities')->where('legal_entity_id',$supplier_id)->pluck('legal_entity_type_id');
        if($le_type[0] == 1009)
        {
            $vendor =  'Are you sure, you want to '.$act.' Vehicle Provider & Associated Vehicles.';
        }
        else if($le_type[0] == 1012)
        {
            $vendor = 'Are you sure, you want to '.$act.' Space Provider & Associated Space.';
        }
        else
        {
            $vendor = "Are you sure, you want to ".$act." ?";
        }
        $result = array();
        $result['status'] = 1;
        $result['vendor'] = $vendor;
        return $result;        
    }
    
    public function setActive(Request $request) {
        $supplier_id = $request->input('supplier_id');  
        $flag = $request->input('flag'); 
        $le_type = DB::table('legal_entities')->where('legal_entity_id',$supplier_id)->pluck('legal_entity_type_id');
        $table = 'suppliers';
        $vendor = 'Supplier';
        $le_type_id = (isset($le_type[0]))?$le_type[0]:0;
        $assoctable = '';
        $assoColumn = '';
        switch($le_type_id)
        {
            case '1008': $table = 'vehicle'; $vendor = 'Vehicle';break;
            case '1009': $table = 'vehicle_provider'; $vendor = 'Vehicle Provider';  $assoctable = 'vehicle'; $assoColumn = 'veh_provider';break;
            case '1007': $table = 'service_provider'; $vendor = 'Service Provider'; break;
            case '1010': $table = 'hr_provider'; $vendor = 'Human Resource Provider'; break;
            case '1011': $table = 'space'; $vendor = 'Space'; break;
            case '1012': $table = 'space_provider'; $vendor = 'Space Provider'; $assoctable = 'space'; $assoColumn='space_provider';break;
            default : $table = 'suppliers'; $vendor = 'Supplier'; break;
        }
        
        $WhInfo = DB::table($table)->where('legal_entity_id',$supplier_id)->update(['is_active'=>$flag]);
        if($assoctable !='')
        {
            DB::table($assoctable)->where($assoColumn,$supplier_id)->update(['is_active'=>$flag]);
        }
        $result = array();
        $result['status'] = 1;
        $result['vendor'] = $vendor;
        return $result;
    }

    public function serviceProviderList() {
    if (!Session::has('userId')) {
        return Redirect::to('/');
    }
            Session::forget('add_continue');
    $brands = BrandModel::all();
    $brands = json_decode(json_encode($brands), true);

    $breadCrumbs = array('Home' => url('/'),   'Suppliers' => '#');
    parent::Breadcrumbs($breadCrumbs);
    $addSuppliers = $this->_roleRepo->checkPermissionByFeatureCode('SUP002');
    $status = 'serviceprovider';
    $leCounts = DB::table('legal_entities')->select('legal_entity_type_id',DB::RAW('COUNT(legal_entity_type_id) AS COUNT'))->groupBy('legal_entity_type_id')->get()->all();
    $counts = $this->gridCountArray();
    $title = 'Manage Service Providers';
    $gridTableId = 'ser_pro_list_grid';
    $buttonName = 'Add New Service Provider';
    $addUrl = 'serviceproviders/add';
    return View::make('Supplier::supplierslist', ['suppliers'=>'','atp_peyiod' => $this->atp_peyiod,'inventory_data'  => $this->inventory_data,
        'legalentity_warehouses' => $this->legalentity_warehouses,'kvi' => $this->kvi, 'add_suppliers' => $addSuppliers,'vendorexport' => 0,
        'uom_data' => $this->uom_data, 'moq_data' => $this->moq_data, 'tax_types' => $this->tax_types, 'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 
        'category_list' => $this->categories, 'brands' => $brands, 'brands_data' => $this->brands_data, 'returns_location_types_list' => $this->returns_location_types,
        'status'=>$status,'le_counts'=>$leCounts,'page_title'=>$title,'grid_table_id'=>$gridTableId,'button_name'=>$buttonName,'add_url'=>$addUrl,'counts'=>$counts]);
    }
    
    public function vehiclesList() {
    if (!Session::has('userId')) {
        return Redirect::to('/');
    }
            Session::forget('add_continue');
    $brands = BrandModel::all();
    $brands = json_decode(json_encode($brands), true);

    $breadCrumbs = array('Home' => url('/'),   'Suppliers' => '#');
    parent::Breadcrumbs($breadCrumbs);
    $addSuppliers = $this->_roleRepo->checkPermissionByFeatureCode('SUP002');
    $status = 'vehicles';
    $leCounts = DB::table('legal_entities')->select('legal_entity_type_id',DB::RAW('COUNT(legal_entity_type_id) AS COUNT'))->groupBy('legal_entity_type_id')->get()->all();
    $counts = $this->gridCountArray();
    $title = 'Manage Vehicles';
    $gridTableId = 'veh_list_grid';
    $buttonName = 'Add New Vehicle';
    $addUrl = 'vehicle/add';
    return View::make('Supplier::supplierslist', ['suppliers'=> '','atp_peyiod' => $this->atp_peyiod,'inventory_data'  => $this->inventory_data,
        'legalentity_warehouses' => $this->legalentity_warehouses,'kvi' => $this->kvi, 'add_suppliers' => $addSuppliers,'vendorexport'=>0, 
        'uom_data' => $this->uom_data, 'moq_data' => $this->moq_data, 'tax_types' => $this->tax_types, 'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 
        'category_list' => $this->categories, 'brands' => $brands, 'brands_data' => $this->brands_data, 'returns_location_types_list' => $this->returns_location_types,
        'status'=>$status,'le_counts'=>$leCounts,'page_title'=>$title,'grid_table_id'=>$gridTableId,'button_name'=>$buttonName,'add_url'=>$addUrl,'counts'=>$counts]);    
    }
    
    public function vehicleProvidersList() {
    if (!Session::has('userId')) {
        return Redirect::to('/');
    }
            Session::forget('add_continue');
    $brands = BrandModel::all();
    $brands = json_decode(json_encode($brands), true);

    $breadCrumbs = array('Home' => url('/'),   'Vehicle Provider' => '#');
    parent::Breadcrumbs($breadCrumbs);
    $addSuppliers = $this->_roleRepo->checkPermissionByFeatureCode('SUP002');
    $status = 'vehiclelist';
    $leCounts = DB::table('legal_entities')->select('legal_entity_type_id',DB::RAW('COUNT(legal_entity_type_id) AS COUNT'))->groupBy('legal_entity_type_id')->get()->all();
    $counts = $this->gridCountArray();
    $title = 'Manage Vehicles Providers';
    $gridTableId = 'veh_pro_list_grid';
    $buttonName = 'Add New Vehicle Provider';
    $addUrl = 'vehicleproviders/add';
    return View::make('Supplier::supplierslist', ['suppliers'=>'','atp_peyiod' => $this->atp_peyiod,'inventory_data'  => $this->inventory_data,
        'legalentity_warehouses' => $this->legalentity_warehouses,'kvi' => $this->kvi, 'add_suppliers' => $addSuppliers,'vendorexport'=>0,
        'uom_data' => $this->uom_data, 'moq_data' => $this->moq_data, 'tax_types' => $this->tax_types, 'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 
        'category_list' => $this->categories, 'brands' => $brands, 'brands_data' => $this->brands_data, 'returns_location_types_list' => $this->returns_location_types,
        'status'=>$status,'le_counts'=>$leCounts,'page_title'=>$title,'grid_table_id'=>$gridTableId,'button_name'=>$buttonName,'add_url'=>$addUrl,'counts'=>$counts]);
    }
    
    public function spaceList() {
    if (!Session::has('userId')) {
        return Redirect::to('/');
    }
            Session::forget('add_continue');
    $brands = BrandModel::all();
    $brands = json_decode(json_encode($brands), true);

    $breadCrumbs = array('Home' => url('/'),   'Space' => '#');
    parent::Breadcrumbs($breadCrumbs);
    $addSuppliers = $this->_roleRepo->checkPermissionByFeatureCode('SUP002');
    $status = 'space';
    $leCounts = DB::table('legal_entities')->select('legal_entity_type_id',DB::RAW('COUNT(legal_entity_type_id) AS COUNT'))->groupBy('legal_entity_type_id')->get()->all();
    $counts = $this->gridCountArray();
    $title = 'Manage Space';
    $gridTableId = 'space_list_grid';
    $buttonName = 'Add New Space';
    $addUrl = 'space/add';
    return View::make('Supplier::supplierslist', ['suppliers'=>'', 'atp_peyiod' => $this->atp_peyiod,'inventory_data'  => $this->inventory_data,
        'legalentity_warehouses' => $this->legalentity_warehouses,'kvi' => $this->kvi, 'add_suppliers' => $addSuppliers,'vendorexport' => 0,
        'uom_data' => $this->uom_data, 'moq_data' => $this->moq_data, 'tax_types' => $this->tax_types, 'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 
        'category_list' => $this->categories, 'brands' => $brands, 'brands_data' => $this->brands_data, 'returns_location_types_list' => $this->returns_location_types,
        'status'=>$status,'le_counts'=>$leCounts,'page_title'=>$title,'grid_table_id'=>$gridTableId,'button_name'=>$buttonName,'add_url'=>$addUrl,'counts'=>$counts]);
    }  
    public function spaceProvidersList() {
    if (!Session::has('userId')) {
        return Redirect::to('/');
    }
            Session::forget('add_continue');
    $brands = BrandModel::all();
    $brands = json_decode(json_encode($brands), true);

    $breadCrumbs = array('Home' => url('/'),   'Space Provider' => '#');
    parent::Breadcrumbs($breadCrumbs);
    $addSuppliers = $this->_roleRepo->checkPermissionByFeatureCode('SUP002');
    $status = 'spaceprovider';
    $leCounts = DB::table('legal_entities')->select('legal_entity_type_id',DB::RAW('COUNT(legal_entity_type_id) AS COUNT'))->groupBy('legal_entity_type_id')->get()->all();
    $counts = $this->gridCountArray(); 
    $title = 'Manage Space Providers';
    $gridTableId = 'space_pro_list_grid';
    $buttonName = 'Add New Space Provider';
    $addUrl = 'spaceprovider/add';
    return View::make('Supplier::supplierslist', ['suppliers'=>'','atp_peyiod' => $this->atp_peyiod,'inventory_data'  => $this->inventory_data,
        'legalentity_warehouses' => $this->legalentity_warehouses,'kvi' => $this->kvi, 'add_suppliers' => $addSuppliers,'vendorexport' => 0,
        'uom_data' => $this->uom_data, 'moq_data' => $this->moq_data, 'tax_types' => $this->tax_types, 'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 
        'category_list' => $this->categories, 'brands' => $brands, 'brands_data' => $this->brands_data, 'returns_location_types_list' => $this->returns_location_types,
        'status'=>$status,'le_counts'=>$leCounts,'page_title'=>$title,'grid_table_id'=>$gridTableId,'button_name'=>$buttonName,'add_url'=>$addUrl,'counts'=>$counts]);
    }  
    public function manpowerProvidersList() {
    if (!Session::has('userId')) {
        return Redirect::to('/');
    }
            Session::forget('add_continue');
    $brands = BrandModel::all();
    $brands = json_decode(json_encode($brands), true);

    $breadCrumbs = array('Home' => url('/'),   'Human Resource Provider' => '#');
    parent::Breadcrumbs($breadCrumbs);
    $addSuppliers = $this->_roleRepo->checkPermissionByFeatureCode('SUP002');
    $status = 'manpower';
    $leCounts = DB::table('legal_entities')->select('legal_entity_type_id',DB::RAW('COUNT(legal_entity_type_id) AS COUNT'))->groupBy('legal_entity_type_id')->get()->all();
    $counts = $this->gridCountArray();
    $title = 'Manage Human Resource Providers';
    $gridTableId = 'hr_list_grid';
    $buttonName = 'Add New Human Resource Provider';
    $addUrl = 'humanresource/add';
    return View::make('Supplier::supplierslist', ['suppliers'=>'','atp_peyiod' => $this->atp_peyiod,'inventory_data'  => $this->inventory_data,
        'legalentity_warehouses' => $this->legalentity_warehouses,'kvi' => $this->kvi, 'add_suppliers' => $addSuppliers,'vendorexport' => 0,
        'uom_data' => $this->uom_data, 'moq_data' => $this->moq_data, 'tax_types' => $this->tax_types, 'suppliers_dcrealtionship' => $this->suppliers_dcrealtionship, 
        'category_list' => $this->categories, 'brands' => $brands, 'brands_data' => $this->brands_data, 'returns_location_types_list' => $this->returns_location_types,
        'status'=>$status,'le_counts'=>$leCounts,'page_title'=>$title,'grid_table_id'=>$gridTableId,'button_name'=>$buttonName,'add_url'=>$addUrl,'counts'=>$counts]);
    }  
    
    
    public function getHrProviders(Request $request) {
        try {
           $this->grid_field_db_match_supp = array('user_name'=>'user_name',
                'Contact'=>'contact',
                'SRM'=>'rel_manager',
                'Documents_count'=>'Documents',
                'Created_By'=>'created_by',
                'Created_On'=>'created_at',
                'Approved_By'=>'approvedby',
                'Approved_On'=>'Approvedon',
                'le_code'=>'le_code',
                'Status'=>'status',
                'is_active'=>'is_active');
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;

            $sm = new VwManageHrProvidersModel();
            $legalEntityIdArray = array();
            $loggedInuserId = Session::get('userId');
            $legal_entity_id = Session::get('legal_entity_id');
            $rback = new Role;
            $rmUserList = $rback->getFilterData(5);
            $rmUserListObj = json_decode($rmUserList);
            if($rmUserListObj->supplier)
            {
                $rnUserListArr = $rmUserListObj->supplier;
            }

            if ($legal_entity_id == 0) {
                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM', 'Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active']);
            } else {

                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM','Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active'])
                ->where('parent_le_entity_id', $legal_entity_id)
                ->whereIn('rel_manager_id',$rnUserListArr)
                        ->orderBy('created_at','desc');
            }

            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_supp[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_supp[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter')) {           //checking for filtering

                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                        if ($filter_query_substr == 'startsw') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = $filter_value_array[1] . '%';


                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }


                        if ($filter_query_substr == 'endswit') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = '%' . $filter_value_array[1];


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :

                                $filter_operator = '=';

                                break;

                            case 'ne':

                                $filter_operator = '!=';

                                break;

                            case 'gt' :

                                $filter_operator = '>';

                                break;

                            case 'lt' :

                                $filter_operator = '<';

                                break;

                            case 'ge' :

                                $filter_operator = '>=';

                                break;

                            case 'le' :

                                $filter_operator = '<=';

                                break;
                        }


                        if (isset($this->grid_field_db_match_supp[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_supp[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
//            \DB::enableQueryLog();
            $row_count = count($query->get()->all());
//            \Log::info(\DB::getQueryLog());
            $query->skip($skip)->take($pageSize);

            $Manage_Suppliers = $query->get()->all();

            foreach ($Manage_Suppliers as $k => $list) {
                $totDocs = 6; //$docsArray[$businesstypeIdValue][$countryId];					
                $Manage_Suppliers[$k]['Documents_count'] = $Manage_Suppliers[$k]['Documents'];

                if ($Manage_Suppliers[$k]['is_active']  == '1') {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox" checked>'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }                
                if ($Manage_Suppliers[$k]['Created_On'] != '') {
                $Manage_Suppliers[$k]['Created_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Created_On']));
                }
                
                if ($Manage_Suppliers[$k]['Approved_On'] != '') {
                $Manage_Suppliers[$k]['Approved_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Approved_On']));
                }
                if ($Manage_Suppliers[$k]['SupplierLogo'] != '') {
                    if (strstr($Manage_Suppliers[$k]['SupplierLogo'], 'http')) {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    } else {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/Suppliers_Docs/" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    }
                } else {
                    $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                }
               
                 $edit_supplier    = $this->_roleRepo->checkPermissionByFeatureCode('SUP003');
                 $delete_supplier  = $this->_roleRepo->checkPermissionByFeatureCode('SUP004');
                 $approve_supplier = $this->_roleRepo->checkPermissionByFeatureCode('SUP005');
                 
                 if($approve_supplier == 1) {
                    $Manage_Suppliers[$k]['Action']  = '<a data-toggle="modal" href="humanresource/approval/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp;&nbsp;';                    
                 }
                 if($edit_supplier == 1) {
                    $Manage_Suppliers[$k]['Action'] .= '<a data-toggle="modal" href="humanresource/edit/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';
                 }
                if($delete_supplier ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a class="deleteSupplier" href="' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-trash-o"></i> </a>';
                }
            }

            echo json_encode(array('Records' => $Manage_Suppliers, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getVehProviders(Request $request) {
        try {
           $this->grid_field_db_match_supp = array('user_name'=>'user_name',
                'Contact'=>'contact',
                'SRM'=>'rel_manager',
                'Documents_count'=>'Documents',
                'Created_By'=>'created_by',
                'Created_On'=>'created_at',
                'Approved_By'=>'approvedby',
                'Approved_On'=>'Approvedon',
                'le_code'=>'le_code',
                'Status'=>'status',
                'is_active'=>'is_active');
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;

            $sm = new VwManageVehProvidersModel();
            $legalEntityIdArray = array();
            $loggedInuserId = Session::get('userId');
            $legal_entity_id = Session::get('legal_entity_id');
            $rback = new Role;
            $rmUserList = $rback->getFilterData(5);
            $rmUserListObj = json_decode($rmUserList);
            if($rmUserListObj->supplier)
            {
                $rnUserListArr = $rmUserListObj->supplier;
            }
			$rmUsers = Session::get('rm_users');
			$dataArr = array();
			if($rmUsers)
			{
				foreach($rmUsers as $userData)
				{
					$id = (isset($userData->id))?$userData->id:'';
					$nam = (isset($userData->username))?$userData->username:'';
					$dataArr[$id] = $nam;
				}
			}
            if ($legal_entity_id == 0) {
                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM', 'Documents as Documents','rel_manager_id',
                            'created_by as Created_By', 'created_at as Created_On', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active']);
            } else {

                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM','Documents as Documents','rel_manager_id',
                            'created_by as Created_By', 'created_at as Created_On', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active'])
                ->where('parent_le_entity_id', $legal_entity_id)
                ->whereIn('rel_manager_id',$rnUserListArr)
                        ->orderBy('created_at','desc');
            }

            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_supp[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_supp[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter')) {           //checking for filtering

                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                        if ($filter_query_substr == 'startsw') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = $filter_value_array[1] . '%';


                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }


                        if ($filter_query_substr == 'endswit') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = '%' . $filter_value_array[1];


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :

                                $filter_operator = '=';

                                break;

                            case 'ne':

                                $filter_operator = '!=';

                                break;

                            case 'gt' :

                                $filter_operator = '>';

                                break;

                            case 'lt' :

                                $filter_operator = '<';

                                break;

                            case 'ge' :

                                $filter_operator = '>=';

                                break;

                            case 'le' :

                                $filter_operator = '<=';

                                break;
                        }


                        if (isset($this->grid_field_db_match_supp[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_supp[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
//            \DB::enableQueryLog();
            $row_count = count($query->get());
//            \Log::info(\DB::getQueryLog());
            $query->skip($skip)->take($pageSize);

            $Manage_Suppliers = $query->get()->all();

            foreach ($Manage_Suppliers as $k => $list) {
                $totDocs = 6; //$docsArray[$businesstypeIdValue][$countryId];					
                $Manage_Suppliers[$k]['Documents_count'] = $Manage_Suppliers[$k]['Documents'];

                if ($Manage_Suppliers[$k]['is_active']  == '1') {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox" checked>'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }                
                if ($Manage_Suppliers[$k]['Created_On'] != '') {
                $Manage_Suppliers[$k]['Created_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Created_On']));
                }
                if ($Manage_Suppliers[$k]['rel_manager_id'] != '') {
                    if(isset($dataArr[$Manage_Suppliers[$k]['rel_manager_id']]))
                    {
                        $Manage_Suppliers[$k]['SRM'] = $dataArr[$Manage_Suppliers[$k]['rel_manager_id']];
                    }
                }               
                if ($Manage_Suppliers[$k]['Approved_On'] != '') {
                $Manage_Suppliers[$k]['Approved_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Approved_On']));
                }
                if ($Manage_Suppliers[$k]['SupplierLogo'] != '') {
                    if (strstr($Manage_Suppliers[$k]['SupplierLogo'], 'http')) {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    } else {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/Suppliers_Docs/" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    }
                } else {
                    $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                }
               
                 $edit_supplier    = $this->_roleRepo->checkPermissionByFeatureCode('SUP003');
                 $delete_supplier  = $this->_roleRepo->checkPermissionByFeatureCode('SUP004');
                 $approve_supplier = $this->_roleRepo->checkPermissionByFeatureCode('SUP005');
                 
                 if($approve_supplier == 1) {
                    $Manage_Suppliers[$k]['Action']  = '<a data-toggle="modal" href="vehicleproviders/approval/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp;&nbsp;';                    
                 }
                 if($edit_supplier == 1) {
                    $Manage_Suppliers[$k]['Action'] .= '<a data-toggle="modal" href="vehicleproviders/edit/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';
                 }
                if($delete_supplier ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a class="deleteSupplier" href="' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-trash-o"></i> </a>';
                }
                $addPaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO0011');
                if($addPaymentFeature ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a href="#addPaymentModel" class="addLePayment" data-leId ="' . $Manage_Suppliers[$k]['SupplierID'] . '" data-toggle="modal"><i class="fa fa-inr"></i></a>';
                }
            }

            echo json_encode(array('Records' => $Manage_Suppliers, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }    
    public function getVehiclesList(Request $request) {
        try {
            $this->_roleRepo = new RoleRepo();
           $this->grid_field_db_match_supp = array('user_name'=>'user_name',
                'Contact'=>'contact',
                'SRM'=>'rel_manager',
                'reg_no'=>'reg_no',
                'vehicle_model'=>'user_name',
                'Documents_count'=>'Documents',
                'Created_By'=>'created_by',
                'Created_On'=>'created_at',
                'Approved_By'=>'approvedby',
                'Approved_On'=>'Approvedon',
                'le_code'=>'le_code',
                'Status'=>'status',
                'veh_provider'=>'veh_provider',
                'is_active'=>'is_active');
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;

            $sm = new VwManageVehicleModel();
            $legalEntityIdArray = array();
            $loggedInuserId = Session::get('userId');
            $legal_entity_id = Session::get('legal_entity_id');
            $vehicle_id = Session::get('vehicle_id');
            $rback = new Role;
            $rmUserList = $rback->getFilterData(5);
            $rmUserListObj = json_decode($rmUserList);
            if($rmUserListObj->supplier)
            {
                $rnUserListArr = $rmUserListObj->supplier;
            }
			$rmUsers = Session::get('rm_users');
			$dataArr = array();
			if($rmUsers)
			{
				foreach($rmUsers as $userData)
				{
					$id = (isset($userData->id))?$userData->id:'';
					$nam = (isset($userData->username))?$userData->username:'';
					$dataArr[$id] = $nam;
				}
			}
            $role = new Role();
            $sbuData = json_decode($role->getFilterData(6), 1);
            $sbu = isset($sbuData['sbu']) ? $sbuData['sbu'] : 0;
            $hubsData = json_decode($sbu, 1);
            $hubs = (isset($hubsData['118002'])) ? $hubsData['118002'] : '';
            $hubs = explode(',',$hubs);
            $query = DB::table('vehicle')
                        ->leftjoin('legal_entities as le_veh','le_veh.legal_entity_id','=','vehicle.legal_entity_id')
                        ->where('le_veh.legal_entity_type_id',1008)
                        ->whereIn('vehicle.hub_id',$hubs)
                        ->select('vehicle.vehicle_id AS vehicle_id',
                                'le_veh.legal_entity_id AS SupplierID' ,
                                'le_veh.parent_id AS parent_le_entity_id',
                                DB::raw('getMastLookupValue(vehicle.vehicle_model)  AS user_name'),
                                DB::raw('getMastLookupValue(vehicle.vehicle_type) AS vehicletype'),
                                DB::raw('getMastLookupValue(vehicle.status) AS Status'),
                                'le_veh.le_code AS le_code',
                                DB::raw('getLeWhName(vehicle.hub_id) AS Warehouse'),
                                'vehicle.hub_id',
                                'vehicle.reg_no AS reg_no',
                                DB::raw('getBusinessLegalName(vehicle.veh_provider)  AS veh_provider'),
                                'le_veh.rel_manager AS rel_manager_id',
                                DB::raw('GetUserName(le_veh.rel_manager,1)  AS SRM'),
                                DB::raw('GetUserName(vehicle.created_by,-(1))  AS Created_By'),
                                'vehicle.created_at AS Created_On',
                                'vehicle.is_active AS is_active')
                        ->orderBy('vehicle.created_at','desc');
            // if ($legal_entity_id > 0) {
            //     $query->where('le_veh.parent_id', $legal_entity_id)
            //      ->whereIn('rel_manager_id',$rnUserListArr)
            //      ->orderBy('vehicle.created_at','desc');
            // }
            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_supp[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_supp[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter')) {           //checking for filtering
                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'

                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual


                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }

                            // foreach ($this->grid_field_db_match_supp as $key => $value) {
                                $field = substr($filter_query_field, 16, 4);
                                if($field == 'user' || $field == 'vehi'|| $field == 'Stat'){
                                    if($field == 'user')
                                        $column_key = 'vehicle_model';
                                    else if($field == 'vehi')
                                        $column_key = 'vehicle_type';
                                    else
                                        $column_key = 'status';
                                    $query->where(DB::raw('getMastLookupValue(vehicle.'.$column_key.')'),$like, $filter_value);
                                }
                                else if($field == 'Ware'){
                                    $query->where(DB::raw('getLeWhName(vehicle.'.'hub_id'.')'),$like, $filter_value);
                                }
                               else if($field == 'veh_'){
                                    $query->where(DB::raw('getBusinessLegalName(vehicle.'.'veh_provider'.')'),$like, $filter_value);
                                }
                                else if($field == 'Crea'){
                                        $query->where(DB::raw('GetUserName(vehicle.'.'created_by'.','.'-1'.')'),$like, $filter_value);
                                }
                                else if($field == 'SRM)'){
                                        $query->where(DB::raw('GetUserName(le_veh.'.'rel_manager'.','.'1'.')'),$like, $filter_value);
                                }
                                else{
                                    $query->where('vehicle.reg_no', $like, $filter_value);
                                }
                            //}
                        }
                    }
                }
            }
//            \DB::enableQueryLog();
            $row_count = count($query->get());
//            \Log::info(\DB::getQueryLog());
            $query->skip($skip)->take($pageSize);

            $Manage_Suppliers = $query->get()->all();
            $Manage_Suppliers = json_decode(json_encode($Manage_Suppliers),1);
            foreach ($Manage_Suppliers as $k => $list) {
                //$totDocs = 6; //$docsArray[$businesstypeIdValue][$countryId];
                //$Manage_Suppliers[$k]['Documents_count'] = $Manage_Suppliers[$k]['Documents'];

                if ($Manage_Suppliers[$k]['is_active']  == '1') {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox" checked>'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }                

                if ($Manage_Suppliers[$k]['rel_manager_id'] != '') {
                    if(isset($dataArr[$Manage_Suppliers[$k]['rel_manager_id']]))
                    {
                        $Manage_Suppliers[$k]['SRM'] = $dataArr[$Manage_Suppliers[$k]['rel_manager_id']];
                    }
                }
                if ($Manage_Suppliers[$k]['Created_On'] != '') {
                $Manage_Suppliers[$k]['Created_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Created_On']));
                }
                
                // if ($Manage_Suppliers[$k]['Approved_On'] != '') {
                // $Manage_Suppliers[$k]['Approved_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Approved_On']));
                // }
                // if ($Manage_Suppliers[$k]['SupplierLogo'] != '') {
                //     if (strstr($Manage_Suppliers[$k]['SupplierLogo'], 'http')) {
                //         $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                //     } else {
                //         $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/Suppliers_Docs/" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                //     }
                // } else {
                //     $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                // }
               
                 $edit_supplier    = $this->_roleRepo->checkPermissionByFeatureCode('SUP003');
                 $delete_supplier  = $this->_roleRepo->checkPermissionByFeatureCode('SUP004');
                 $approve_supplier = $this->_roleRepo->checkPermissionByFeatureCode('SUP005');
                 
                 if($approve_supplier == 1) {
                    $Manage_Suppliers[$k]['Action']  = '<a data-toggle="modal" href="vehicle/approval/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp;&nbsp;';                    
                 }
                 if($edit_supplier == 1) {
                    $Manage_Suppliers[$k]['Action'] .= '<a data-toggle="modal" href="vehicle/edit/' . $Manage_Suppliers[$k]['vehicle_id'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';
                 }
                if($delete_supplier ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a class="deleteSupplier" vehid="' . $Manage_Suppliers[$k]['vehicle_id'] . '" href="' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-trash-o"></i> </a>';
                }
            }

            echo json_encode(array('Records' => $Manage_Suppliers, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }    
    
    public function getServiceProvider(Request $request) {
        try {
           $this->grid_field_db_match_supp = array('user_name'=>'user_name',
                'Contact'=>'contact',
                'SRM'=>'rel_manager',
                'Documents_count'=>'Documents',
                'Created_By'=>'created_by',
                'Created_On'=>'created_at',
                'Approved_By'=>'approvedby',
                'Approved_On'=>'Approvedon',
                'le_code'=>'le_code',
                'Status'=>'status',
                'is_active'=>'is_active');
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;

            $sm = new VwManageServiceModel();
            $legalEntityIdArray = array();
            $loggedInuserId = Session::get('userId');
            $legal_entity_id = Session::get('legal_entity_id');
            $rback = new Role;
            $rmUserList = $rback->getFilterData(5);
            $rmUserListObj = json_decode($rmUserList);
            if($rmUserListObj->supplier)
            {
                $rnUserListArr = $rmUserListObj->supplier;
            }

            if ($legal_entity_id == 0) {
                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM', 'Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active']);
            } else {

                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM','Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active'])
                ->where('parent_le_entity_id', $legal_entity_id)
                ->whereIn('rel_manager_id',$rnUserListArr)
                        ->orderBy('created_at','desc');
            }

            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_supp[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_supp[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter')) {           //checking for filtering

                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                        if ($filter_query_substr == 'startsw') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = $filter_value_array[1] . '%';


                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }


                        if ($filter_query_substr == 'endswit') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = '%' . $filter_value_array[1];


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :

                                $filter_operator = '=';

                                break;

                            case 'ne':

                                $filter_operator = '!=';

                                break;

                            case 'gt' :

                                $filter_operator = '>';

                                break;

                            case 'lt' :

                                $filter_operator = '<';

                                break;

                            case 'ge' :

                                $filter_operator = '>=';

                                break;

                            case 'le' :

                                $filter_operator = '<=';

                                break;
                        }


                        if (isset($this->grid_field_db_match_supp[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_supp[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
//            \DB::enableQueryLog();
            $row_count = count($query->get());
//            \Log::info(\DB::getQueryLog());
            $query->skip($skip)->take($pageSize);

            $Manage_Suppliers = $query->get()->all();

            foreach ($Manage_Suppliers as $k => $list) {
                $totDocs = 6; //$docsArray[$businesstypeIdValue][$countryId];					
                $Manage_Suppliers[$k]['Documents_count'] = $Manage_Suppliers[$k]['Documents'];

                if ($Manage_Suppliers[$k]['is_active']  == '1') {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox" checked>'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }                
                if ($Manage_Suppliers[$k]['Created_On'] != '') {
                $Manage_Suppliers[$k]['Created_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Created_On']));
                }
                
                if ($Manage_Suppliers[$k]['Approved_On'] != '') {
                $Manage_Suppliers[$k]['Approved_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Approved_On']));
                }
                if ($Manage_Suppliers[$k]['SupplierLogo'] != '') {
                    if (strstr($Manage_Suppliers[$k]['SupplierLogo'], 'http')) {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    } else {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/Suppliers_Docs/" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    }
                } else {
                    $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                }
               
                 $edit_supplier    = $this->_roleRepo->checkPermissionByFeatureCode('SUP003');
                 $delete_supplier  = $this->_roleRepo->checkPermissionByFeatureCode('SUP004');
                 $approve_supplier = $this->_roleRepo->checkPermissionByFeatureCode('SUP005');
                 
                 if($approve_supplier == 1) {
                    $Manage_Suppliers[$k]['Action']  = '<a data-toggle="modal" href="serviceproviders/approval/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp;&nbsp;';                    
                 }
                 if($edit_supplier == 1) {
                    $Manage_Suppliers[$k]['Action'] .= '<a data-toggle="modal" href="serviceproviders/edit/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';
                 }
                if($delete_supplier ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a class="deleteSupplier" href="' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-trash-o"></i> </a>';
                }
            }

            echo json_encode(array('Records' => $Manage_Suppliers, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getSpace(Request $request) {
        try {
           $this->grid_field_db_match_supp = array('user_name'=>'user_name',
                'Contact'=>'contact',
                'SRM'=>'rel_manager',
                'Documents_count'=>'Documents',
                'Created_By'=>'created_by',
                'Created_On'=>'created_at',
                'Approved_By'=>'approvedby',
                'Approved_On'=>'Approvedon',
                'le_code'=>'le_code',
                'Status'=>'status',
                'space_provider'=>'space_provider',
                'is_active'=>'is_active');
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;

            $sm = new VwManageSpaceModel();
            $legalEntityIdArray = array();
            $loggedInuserId = Session::get('userId');
            $legal_entity_id = Session::get('legal_entity_id');
            $rback = new Role;
            $rmUserList = $rback->getFilterData(5);
            $rmUserListObj = json_decode($rmUserList);
            if($rmUserListObj->supplier)
            {
                $rnUserListArr = $rmUserListObj->supplier;
            }

            if ($legal_entity_id == 0) {
                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM', 'Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On', 'space_provider','approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active']);
            } else {

                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM','Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On','space_provider', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active'])
                ->where('parent_le_entity_id', $legal_entity_id)
                ->whereIn('rel_manager_id',$rnUserListArr)
                        ->orderBy('created_at','desc');
            }

            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_supp[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_supp[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter')) {           //checking for filtering

                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                        if ($filter_query_substr == 'startsw') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = $filter_value_array[1] . '%';


                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }


                        if ($filter_query_substr == 'endswit') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = '%' . $filter_value_array[1];


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :

                                $filter_operator = '=';

                                break;

                            case 'ne':

                                $filter_operator = '!=';

                                break;

                            case 'gt' :

                                $filter_operator = '>';

                                break;

                            case 'lt' :

                                $filter_operator = '<';

                                break;

                            case 'ge' :

                                $filter_operator = '>=';

                                break;

                            case 'le' :

                                $filter_operator = '<=';

                                break;
                        }


                        if (isset($this->grid_field_db_match_supp[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_supp[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
//            \DB::enableQueryLog();
            $row_count = count($query->get());
//            \Log::info(\DB::getQueryLog());
            $query->skip($skip)->take($pageSize);

            $Manage_Suppliers = $query->get()->all();

            foreach ($Manage_Suppliers as $k => $list) {
                $totDocs = 6; //$docsArray[$businesstypeIdValue][$countryId];					
                //$Manage_Suppliers[$k]['Documents_count'] = $Manage_Suppliers[$k]['Documents'] . '/' . $totDocs;
                $Manage_Suppliers[$k]['Documents_count'] = $Manage_Suppliers[$k]['Documents'];

                if ($Manage_Suppliers[$k]['is_active']  == '1') {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox" checked>'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }                
                if ($Manage_Suppliers[$k]['Created_On'] != '') {
                $Manage_Suppliers[$k]['Created_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Created_On']));
                }
                
                if ($Manage_Suppliers[$k]['Approved_On'] != '') {
                $Manage_Suppliers[$k]['Approved_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Approved_On']));
                }
                if ($Manage_Suppliers[$k]['SupplierLogo'] != '') {
                    if (strstr($Manage_Suppliers[$k]['SupplierLogo'], 'http')) {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    } else {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/Suppliers_Docs/" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    }
                } else {
                    $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                }
               
                 $edit_supplier    = $this->_roleRepo->checkPermissionByFeatureCode('SUP003');
                 $delete_supplier  = $this->_roleRepo->checkPermissionByFeatureCode('SUP004');
                 $approve_supplier = $this->_roleRepo->checkPermissionByFeatureCode('SUP005');
                 
                 if($approve_supplier == 1) {
                    $Manage_Suppliers[$k]['Action']  = '<a data-toggle="modal" href="space/approval/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp;&nbsp;';                    
                 }
                 if($edit_supplier == 1) {
                    $Manage_Suppliers[$k]['Action'] .= '<a data-toggle="modal" href="space/edit/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';
                 }
                if($delete_supplier ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a class="deleteSupplier" href="' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-trash-o"></i> </a>';
                }
            }

            echo json_encode(array('Records' => $Manage_Suppliers, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getSpaceProvider(Request $request) {
        try {
           $this->grid_field_db_match_supp = array('user_name'=>'user_name',
                'Contact'=>'contact',
                'SRM'=>'rel_manager',
                'Documents_count'=>'Documents',
                'Created_By'=>'created_by',
                'Created_On'=>'created_at',
                'Approved_By'=>'approvedby',
                'Approved_On'=>'Approvedon',
                'le_code'=>'le_code',
                'Status'=>'status',
                'is_active'=>'is_active');
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax call
            $skip = $page * $pageSize;

            $sm = new VwManageSpaceProModel();
            $legalEntityIdArray = array();
            $loggedInuserId = Session::get('userId');
            $legal_entity_id = Session::get('legal_entity_id');
            $rback = new Role;
            $rmUserList = $rback->getFilterData(5);
            $rmUserListObj = json_decode($rmUserList);
            if($rmUserListObj->supplier)
            {
                $rnUserListArr = $rmUserListObj->supplier;
            }

            if ($legal_entity_id == 0) {
                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM', 'Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active']);
            } else {

                $query = $sm::select(['legal_entity_id as SupplierID', 'user_name', 'le_code as le_code', 'contact as Contact', 'rel_manager as SRM','Documents as Documents',
                            'created_by as Created_By', 'created_at as Created_On', 'approvedby as Approved_By', 'Approvedon as Approved_On', 'status as Status','is_active'])
                ->where('parent_le_entity_id', $legal_entity_id)
                ->whereIn('rel_manager_id',$rnUserListArr)
                        ->orderBy('created_at','desc');
            }

            if ($request->input('$orderby')) {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));

                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc

                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_supp[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_supp[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }


            if ($request->input('$filter')) {           //checking for filtering

                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


                foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    } else {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                        if ($filter_query_substr == 'startsw') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = $filter_value_array[1] . '%';


                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }


                        if ($filter_query_substr == 'endswit') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                            $filter_value = '%' . $filter_value_array[1];


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'tolower') {

                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = $filter_value_array[1];

                            if ($filter_query_operator == 'eq') {
                                $like = '=';
                            } else {
                                $like = '!=';
                            }

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }

                        if ($filter_query_substr == 'indexof') {

                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = 'like';
                            } else {
                                $like = 'not like';
                            }


                            //substr(strpos($info, '-', strpos($info, '-')+1)

                            foreach ($this->grid_field_db_match_supp as $key => $value) {

                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_supp[$key], $like, $filter_value);
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :

                                $filter_operator = '=';

                                break;

                            case 'ne':

                                $filter_operator = '!=';

                                break;

                            case 'gt' :

                                $filter_operator = '>';

                                break;

                            case 'lt' :

                                $filter_operator = '<';

                                break;

                            case 'ge' :

                                $filter_operator = '>=';

                                break;

                            case 'le' :

                                $filter_operator = '<=';

                                break;
                        }


                        if (isset($this->grid_field_db_match_supp[$filter_query_field])) { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_supp[$filter_query_field];
                        }

                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
//            \DB::enableQueryLog();
            $row_count = count($query->get());
//            \Log::info(\DB::getQueryLog());
            $query->skip($skip)->take($pageSize);

            $Manage_Suppliers = $query->get()->all();

            foreach ($Manage_Suppliers as $k => $list) {
                $totDocs = 6; //$docsArray[$businesstypeIdValue][$countryId];					
                $Manage_Suppliers[$k]['Documents_count'] = $Manage_Suppliers[$k]['Documents'];

                if ($Manage_Suppliers[$k]['is_active']  == '1') {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox" checked>'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                } else {
                    $Manage_Suppliers[$k]['is_active'] = '<label class="switch "><input id ='.$Manage_Suppliers[$k]['SupplierID'].' class="switch-input enableDisableProducttot enable" data_attr_productid=' . $Manage_Suppliers[$k]['is_active'] . ' type="checkbox">'
                            . '<span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                }                
                if ($Manage_Suppliers[$k]['Created_On'] != '') {
                $Manage_Suppliers[$k]['Created_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Created_On']));
                }
                
                if ($Manage_Suppliers[$k]['Approved_On'] != '') {
                $Manage_Suppliers[$k]['Approved_On'] = date('d-m-Y h:i A', strtotime($Manage_Suppliers[$k]['Approved_On']));
                }
                if ($Manage_Suppliers[$k]['SupplierLogo'] != '') {
                    if (strstr($Manage_Suppliers[$k]['SupplierLogo'], 'http')) {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    } else {
                        $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/Suppliers_Docs/" . $Manage_Suppliers[$k]['SupplierLogo'] . "' height='33' width='100' />";
                    }
                } else {
                    $Manage_Suppliers[$k]['SupplierLogo'] = "<img src='/uploads/brand_logos/notfound.png' height='33' width='100' />";
                }
               
                 $edit_supplier    = $this->_roleRepo->checkPermissionByFeatureCode('SUP003');
                 $delete_supplier  = $this->_roleRepo->checkPermissionByFeatureCode('SUP004');
                 $approve_supplier = $this->_roleRepo->checkPermissionByFeatureCode('SUP005');
                 
                 if($approve_supplier == 1) {
                    $Manage_Suppliers[$k]['Action']  = '<a data-toggle="modal" href="spaceprovider/approval/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-thumbs-o-up"></i> </a>&nbsp;&nbsp;';                    
                 }
                 if($edit_supplier == 1) {
                    $Manage_Suppliers[$k]['Action'] .= '<a data-toggle="modal" href="spaceprovider/edit/' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;';
                 }
                if($delete_supplier ==1 ) {
                    $Manage_Suppliers[$k]['Action'] .= '<a class="deleteSupplier" href="' . $Manage_Suppliers[$k]['SupplierID'] . '"> <i class="fa fa-trash-o"></i> </a>';
                }
            }

            echo json_encode(array('Records' => $Manage_Suppliers, 'TotalRecordsCount' => $row_count), JSON_NUMERIC_CHECK);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }    
    public function vehiclesAdditional(Request $request)
    {
        $vendor = Session::get('supplier_id');
        
        if(!$vendor)
        {
            if(isset($_SERVER["HTTP_REFERER"]))
            {
            $referer = $_SERVER["HTTP_REFERER"];
            $urlArray = explode('/',$referer);
            $is_provider  = isset($urlArray[5])?$urlArray[5]:'';
            }
        }
        else
        {
            $is_provider  = $vendor;
        } 
        $data = $request->all();
            $data['reg_exp_date'] = date('Y-m-d',strtotime($data['reg_exp_date']));
            $data['insurance_exp_date'] = date('Y-m-d',strtotime($data['insurance_exp_date']));
            $data['fit_exp_date'] = date('Y-m-d',strtotime($data['fit_exp_date']));
            $data['poll_exp_date'] = date('Y-m-d',strtotime($data['poll_exp_date']));
            $data['safty_exp_date'] = date('Y-m-d',strtotime($data['poll_exp_date']));
        unset($data['_token']);

        $res = DB::table('vehicle')
            ->where('legal_entity_id', $is_provider)
            ->update($data);
        echo  json_encode(array('status' => 'true', 'legalentity_id' => $is_provider, 'result'=>$res));
    }
    public function spaceAdditional(Request $request)
    {
        $vendor = Session::get('supplier_id');
        if(isset($_SERVER["HTTP_REFERER"]))
        {
            $referer = $_SERVER["HTTP_REFERER"];
            $urlArray = explode('/',$referer);
        }
        if(!$vendor)
        {
            $is_provider  = isset($urlArray[5])?$urlArray[5]:'';            
        }
        else
        {
            $is_provider  = $vendor;
        } 
        $data = $request->all();
        unset($data['_token']);

        $res = DB::table('space')
            ->where('legal_entity_id', $is_provider)
            ->update($data);
        echo  json_encode(array('status' => 'true', 'legalentity_id' => $is_provider, 'result'=>$res));
    }
    
    public function uniqueRegistation(Request $request) {                
        $org_email = $request->reg_no;
        $referer = $_SERVER["HTTP_REFERER"];
        $urlArray = explode('/',$referer);
        //$is_edit  = array_pop($urlArray);
        $veh_id=isset($request->vehicle_id)?$request->vehicle_id:'';
        
        $leCounts = DB::table('vehicle')->select(DB::RAW('COUNT(vehicle_id) AS COUNT'))->where('reg_no',$org_email)->whereNotIn("vehicle_type",[156002]);
        if(!empty($veh_id)){
            $leCounts =$leCounts->whereNotIn('vehicle_id',[$veh_id]);
        }   
        $leCounts =$leCounts->get();
        $count = (isset($leCounts[0]))?$leCounts[0]:0;        
                    
           if($count->COUNT > 0)
           {
             echo "false";  
           }
           else
           {
               echo "true";
           }
       
    }
    
        public function uniqueInsurance(Request $request) {
        
        
        $org_email = $request->insurance_no;
        $referer = $_SERVER["HTTP_REFERER"];
        $urlArray = explode('/',$referer);
        $is_edit  = array_pop($urlArray);
        if($is_edit == 'add')
        {
        $leCounts = DB::table('vehicle')->select(DB::RAW('COUNT(vehicle_id) AS COUNT'))->where('insurance_no',$org_email)->get()->all();
        $count = (isset($leCounts[0]))?$leCounts[0]:0;        
                    
           if($count->COUNT > 0)
           {
             echo "false";  
           }
           else
           {
               echo "true";
           }
        }
        else
        {
            echo "true";
        }
    }
        public function uniqueLicense(Request $request) {
        $referer = $_SERVER["HTTP_REFERER"];
        $urlArray = explode('/',$referer);
        $is_edit  = array_pop($urlArray);
        if($is_edit == 'add')
        {
            $leCounts = DB::table('drivers')->where('driving_license_no',$request->license_no)->count();
                    
            if($leCounts > 0)
                return "false";
            return "true";
        }
        return "true";
    }

    public function getHubList()
    {
        $role = new Role();
        $sbuData = json_decode($role->getFilterData(6), 1);
        $sbu = isset($sbuData['sbu']) ? $sbuData['sbu'] : 0;
        $hubsData = json_decode($sbu, 1);
        
        $wh = (isset($hubsData['118001'])) ? $hubsData['118001'] : '';
        $hb = (isset($hubsData['118002'])) ? $hubsData['118002'] : '';
        
        $whNames = DB::table('legalentity_warehouses')->where('lp_wh_name','!=',null)->pluck('le_wh_id','lp_wh_name')->all();
        $whKeyNames = array_flip($whNames);
        
            $warehouseArray = explode(',', $wh);
            $hubsArray = explode(',', $hb);
            $mapping = DB::table('dc_hub_mapping')->select('dc_id', 'hub_id')->get()->all();
            $finalArray = array();
            foreach ($mapping as $dat)
            {
                if(in_array($dat->dc_id,$warehouseArray) && in_array($dat->hub_id,$hubsArray))
                    $mapData[$dat->dc_id][] = $dat->hub_id;
            }           
           $finalArray[] = '<option value="" class="" >  Plaese select Hub </option>';
           if(empty($mapData))
           {
               return $finalArray;
           }            

            foreach($mapData as $key=>$whid)
            {
                $finalArray[] = $this->getHubsArray($whKeyNames,$key,'parent_cat');
                $finalArray[] = $this->getHubsArray($whKeyNames,$whid,'sub_cat');
            }
            $finalArray2 = array();
            foreach($finalArray as $key=>$whid)
            {
                if(is_array($whid))
                {
                    foreach($whid as $v)
                        $finalArray2[] = $v;
                }
                else
                {
                $finalArray2[] =$whid;
                }
            }
            
            
        return $finalArray2;
    }

    public function getHubsArray($whKeyNames,$hubsData,$flag)
    {
        
        if (is_array($hubsData))
        {
            $subArray = array();
            foreach ($hubsData as $units)
            {                
                    $subArray[] = $this->getHubsArray($whKeyNames,$units,$flag);
            } 
            return $subArray;
        }
        else
        {
            $name = (isset($whKeyNames[$hubsData]))?$whKeyNames[$hubsData]:'';
            return $hubArray = '<option value="' . $hubsData . '" class="'.$flag.'" > ' . $name. '</option>';
        }    
    }
	
	public function saveGstin($legal_entity_Id,$ref_no)
	{
		try{
			DB::table('legal_entities')->where('legal_entity_Id',$legal_entity_Id)->update(['gstin'=>$ref_no]);
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}		
	}  
    public function gstZoneCode(){
        try{    
            $zone = DB::table('zone')
                    ->where('gst_state_code',"!=","null")
                    ->where('gst_state_code','!=','00')
                    ->select('gst_state_code')
                    ->get()->all();
            $zone = json_decode(json_encode($zone),true);
            $zone1=array();
            foreach ($zone as $key)
            {
                $code = $key['gst_state_code'];
                $len = strlen($code);
                if($len==1)
                {
                    $code = '0'.$code;
                }
                $zone1[] = $code;
            }
            return $zone1;
        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }   
    }
    public function getSupplierDetails($supplierId)
    {
        $query= DB::select(DB::raw("SELECT * FROM user_roles ur INNER JOIN users u ON u.`user_id`=ur.`user_id` INNER JOIN legal_entities l ON l.`legal_entity_id`=u.`legal_entity_id` LEFT JOIN drivers d ON d.`legal_entity_id`=l.`legal_entity_id` WHERE ur.`user_id` = '$supplierId'"));
        return $query;
    }

    public function getBannersList(){
        try{
            $data=Input::all();
            $displaytype=$data['item'];
            $navigatorobjects=$data['payment_for'];
            $getlist=DB::table('banner')->select('banner_id','banner_name')->where('navigator_objects',$displaytype)->where('display_type',$navigatorobjects)->get()->all();
            $getlist = json_decode(json_encode($getlist), True);
            $resreturn='<option value="">Select</option>';
            for($l=0;$l<count($getlist);$l++) {
               $resreturn.='<option value="'.$getlist[$l]['banner_id']. '"> '.$getlist[$l]['banner_name'].'</option>';
          }
          return $resreturn;
        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }
    }

    public function checkGstStateCode($gst_no) {
        try
        {            
            $status = (!\Utility::check_gst_state_code($gst_no)) ? false : true;
            return json_encode(array('status' => $status));
        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }
    }


    public function getImpressionClicksbyBannerId(){
        try{
            $data=Input::all();
            $bannerId=$data['banner_id'];
            $cost_center=$data['cost_center'];
            $payment_for=$data['payment_for'];
            $result=array();

            $buid=1;

            $leWhId = DB::select(DB::raw("call getAllBuHierarchyByID($buid)"));
            $leWhId=isset($leWhId[0]->le_wh_ids) ? explode(',',$leWhId[0]->le_wh_ids.',0') : 0;
            $payid='';
            if(isset($data['pay_id']) && !empty($data['pay_id'])){
                $payid=' and bph.pay_id!='.$data['pay_id'];
            }
            $result['click_info']=DB::table('banner as b')->join('sponsor_history_details as shd','b.banner_id','=','shd.config_mapping_id')->leftjoin('brand_payment_histroy as bph','bph.config_mapping_id','=','shd.config_mapping_id')->select(DB::raw('COUNT(distinct(shd.history_id)) - IFNULL((SELECT SUM(bph.`clicks`) FROM brand_payment_histroy bph 
                WHERE bph.config_mapping_id = shd.`config_mapping_id`'.$payid.'),0
                ) AS clicks,
                b.click_cost,
                (COUNT(distinct(shd.history_id)) - IFNULL((SELECT SUM(bph.`clicks`) FROM brand_payment_histroy bph 
                WHERE bph.config_mapping_id = shd.`config_mapping_id`'.$payid.'),0
                )) * b.click_cost AS clickamt'))->where('b.banner_id',$bannerId)->where('shd.action_type',16802)->whereIn('shd.le_wh_id',$leWhId)->where('config_object_type',$payment_for)->get()->all();
            $result['impression_info']=DB::table('banner as b')->join('sponsor_history_details as shd','b.banner_id','=','shd.config_mapping_id')->leftjoin('brand_payment_histroy as bph','bph.config_mapping_id','=','shd.config_mapping_id')->select(DB::raw('COUNT(distinct(shd.history_id)) - IFNULL((SELECT SUM(bph.`impressions`) FROM brand_payment_histroy bph 
                WHERE bph.config_mapping_id = shd.`config_mapping_id`'.$payid.'),0
                ) AS impressions,
                b.impression_cost AS impressions_cost,
                (COUNT(distinct(shd.history_id)) - IFNULL((SELECT SUM(bph.`impressions`) FROM brand_payment_histroy bph 
                WHERE bph.config_mapping_id = shd.`config_mapping_id`'.$payid.'),0
                )) * b.impression_cost AS impressionsamt'))->where('b.banner_id',$bannerId)->where('shd.action_type',16801)->whereIn('shd.le_wh_id',$leWhId)->where('config_object_type',$payment_for)->get()->all();
            if(!empty($result['click_info'][0]->clickamt) && $result['click_info'][0]->clickamt!=null){
                $sumclick=$result['click_info'][0]->clickamt;
            }else{
                $sumclick=0;
            }
            if(!empty($result['impression_info'][0]->impressionsamt) && $result['impression_info'][0]->impressionsamt!=null){
                $sumimpression=$result['impression_info'][0]->impressionsamt;
            }else{
                $sumimpression=0;
            }
            $result['total_amt']=$sumclick+$sumimpression;

            return json_encode($result);

        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }
    }
    public static function CallRaw($procName, $parameters = null, $isExecute = false)
    {
        $syntax = '';
        $syntax = $parameters[0].','.$parameters[1].','.$parameters[2];
        $syntax = 'CALL ' . $procName . '(' . $syntax . ');';
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare($syntax,[\PDO::ATTR_CURSOR=>\PDO::CURSOR_SCROLL]);
        for ($i = 0; $i < count($parameters); $i++) {
            $stmt->bindValue((1 + $i), $parameters[$i]);
        }
        $exec = $stmt->execute();
        if (!$exec) return $pdo->errorInfo();
        if ($isExecute) return $exec;
        $results = [];
        do {
            try {
                $results[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                log::info($results);
            } catch (\Exception $ex) {
                log::info($ex);

            }
        } while ($stmt->nextRowset());
        if (1 === count($results)) return $results[0];
        return $results;
    }

    function getMappedSuppliersForManufacturer($manufacturer){

        $getmappedSuppliers=DB::table('supplier_brand_mapping')
                            ->select(DB::raw('GROUP_CONCAT(supplier_id) as supplier'));
        // if(!in_array(0, $manufacturer)){
        array_push($manufacturer, 0);
          $getmappedSuppliers=$getmappedSuppliers->whereIn('manufacturer_id',$manufacturer);
        // }
        $getmappedSuppliers=$getmappedSuppliers->first();
        $getmappedSuppliers=explode(',', $getmappedSuppliers->supplier);
        return $getmappedSuppliers;
    }
}
