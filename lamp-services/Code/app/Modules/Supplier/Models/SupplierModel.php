<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Modules\Supplier\Models\Legalentities;
use App\Modules\Supplier\Models\Suppliers;
use App\Modules\Supplier\Models\ServiceProvider;
use App\Modules\Supplier\Models\HrProvider;
use App\Modules\Supplier\Models\VehicleService;
use App\Modules\Supplier\Models\Vehicle;
use App\Modules\Supplier\Models\Users;
use App\Modules\Supplier\Models\Userroles;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\RoleRepo;
use Mail;
use DB;
use Log;
use App\Modules\Supplier\Models\PurchasePriceHistory;
use App\Modules\Cpmanager\Models\AttendanceModel;
use Illuminate\Support\Facades\Config;
use App\Modules\Roles\Models\Role;
use Excel;
use Session;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;

class SupplierModel extends Model
{
    public $timestamps = true;
    protected $fillable = ['supplier_id','legal_entity_id','erp_code','est_year','sup_add1','sup_add2','sup_country','sup_state','sup_city','sup_pincode','sup_account_name','sup_bank_name','sup_account_no','sup_account_type','sup_ifsc_code','sup_branch_name','sup_micr_code','sup_currency_code','sup_currency_code'];
	protected $table = "suppliers";
	protected $primaryKey = 'supplier_id';


public function createdBy($userId,$legalEntityId,$supplierId)
{
			$supplier = new SupplierModel();
			$legalEntity = new Legalentities();
	$current_time = Carbon::now()->toDateTimeString();
	    $status = $supplier->where ("supplier_id",$supplierId)
                      ->update(['created_by'=>$userId,'created_at'=>$current_time]);
        $Legalstatus = $legalEntity->where ("legal_entity_id",$legalEntityId)
                      ->update(['created_by'=>$userId,'created_at'=>$current_time]);
}

public function updatedBy($userId,$legalEntityId)
{
			$supplier = new SupplierModel();
			$legalEntity = new Legalentities();
	$current_time = Carbon::now()->toDateTimeString();
	    $status = $supplier->where ("legal_entity_id",$legalEntityId)
                      ->update(['updated_by'=>$userId,'updated_at'=>$current_time]);
        $Legalstatus = $legalEntity->where ("legal_entity_id",$legalEntityId)
                      ->update(['updated_by'=>$userId,'updated_at'=>$current_time]);
}

	public function approvedBy($userId,$legalEntityId,$supplierId)
	{
				$supplier = new SupplierModel();
				$legalEntity = new Legalentities();
		$current_time = Carbon::now()->toDateTimeString();
			$status = $supplier->where ("supplier_id",$supplierId)
						  ->update(['approved_by'=>$userId,'approved_at'=>$current_time]);
			$Legalstatus = $legalEntity->where ("legal_entity_id",$legalEntityId)
						  ->update(['approved_by'=>$userId,'approved_at'=>$current_time]);
	}

    public function sendEmailReminder($userId, $supplierId,$requestComments)
    {
	$supplier = new SupplierModel();
        $userModel = new Users();
        $legalEntities = new Legalentities();
        $srmUser = $supplier->select('sup_rm','legal_entity_id')->where("supplier_id",$supplierId)->get()->all();
        $srmUserId = $srmUser[0]->sup_rm;
        $legalId = $srmUser[0]->legal_entity_id;
        $supplierName = $legalEntities->select('business_legal_name')->where("legal_entity_id",$legalId)->get()->all();
                
        $fmName = $this->getUserNameById($userId);
        $fmEmail = "ravinder.majoju@ebutor.com";//$this->getUserEmailById($userId);       
        $srmName = $this->getUserNameById($srmUserId);        
        $srmEmail = "ravinder.majoju@ebutor.com";//$this->getUserEmailById($srmUserId);        
        $sender = "ravinder.majoju@ebutor.com";
        $name = "Ebutor";             
        /*
        Mail::send('emails.reminder', ['user' => $user], function ($m) use ($user) {
            $m->from('ravinder.majoju@ebutor.com', 'Ebutor');
            $m->to($user->email, $user->name)->subject('Your Reminder!');
        });
        */
        $copyTo = ['toName'=>$srmName,'toEmail'=>$srmEmail,'ccName'=>$fmName,'ccEmail'=>$fmEmail,'fromName'=>$name,'fromEmail'=>$sender,'suppliername'=>$supplierName[0]->business_legal_name];
         Mail::send('emails.supplierrej',['srmname'=>$srmName,'suppliername'=>$supplierName[0]->business_legal_name,'comments'=>$requestComments], function ($message) use ($copyTo) {            
             $message->from($copyTo['fromEmail'], $copyTo['fromName']);
            $message->to($copyTo['toEmail'], $copyTo['toName']);
            $message->cc($copyTo['ccEmail'], $copyTo['ccName']);
            $message->subject('supplier "'.$copyTo['suppliername'].'" rejected');
            });
    }
    public function sendEmail($fmName, $fmEmail, $name, $template, $subject, $attach_file) {
        try {
            $copyTo = ['toName' => $fmName, 'toEmail' => $fmEmail, 'ccName' => 'raju', 'ccEmail' => 'raju.aavudoddi@ebutor.com','subject'=>$subject,'attach_file'=>$attach_file];
            Mail::send('emails.' . $template, ['srmname' => $fmName, 'comments' => ''], function ($message) use ($copyTo) {
                //$message->from($copyTo['ccEmail'], 'Ebutor');
                $message->to($copyTo['toEmail'], $copyTo['toName']);
                $message->cc($copyTo['ccEmail'], $copyTo['ccName']);
                $message->subject($copyTo['subject']);
                if ($copyTo['attach_file'] != '') {
                    $message->attach($copyTo['attach_file']);
                }
            });
        } catch (Exception $e) {
            
        }
    }
    public function getStates() {
        $states_data = DB::table('zone')
                ->select('name as state_name', 'zone_id as id')
                ->where('country_id', 99)
				->orderByRaw("FIELD(name,'Telangana') DESC")->get()->all();
        return $states_data;
    }

    public function getWarehouseBylegalentity($legal_entity_id) {
        $LegalentitywarehousesModel = new LegalentitywarehousesModel();        
        $legalentity_warehouses = $LegalentitywarehousesModel->where(['legal_entity_id'=>$legal_entity_id,'dc_type'=>NULL])->orwhere('dc_type','118001')->get()->all();
        return $legalentity_warehouses;
    }

    public function getCountries() {
        $countries_data = DB::table('countries')
                ->select('name as country_name', 'country_id as id')
                ->get()->all();
        return $countries_data;
    }

    public function getCurrency() {
        $currency_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as currency_name', 'master_lookup.master_lookup_id as id')
                ->where('master_lookup_categories.mas_cat_id', '=', '46')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Currency')
                ->get()->all();
        return $currency_data;
    }

    public function getCompanytype($cat_id) {
        $company_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as company_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', $cat_id)
                //->where('master_lookup_categories.mas_cat_name', '=', 'Company Types')
                ->get()->all();
        return $company_data;
    }

    public function getInventorymode() {
        $inventory_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '45')
                ->where('master_lookup_categories.mas_cat_name', '=', 'invetory_mode')
                ->get()->all();
        return $inventory_data;
    }
    
    public function getTatUom() {
        $uom_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '71')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Shelf Life UOM')
                ->get()->all();
        return $uom_data;
    }
    
    public function getRtvScope() {
        $rtv_scope = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '92')
                ->where('master_lookup_categories.mas_cat_name', '=', 'RTV Scopes')
                ->get()->all();
        return $rtv_scope;
    }
    
    public function getNegotiation() {
        $negotiation_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '93')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Supplier Negotiation')
                ->get()->all();
        return $negotiation_data;
    }
    
    public function getRtvlocation() {
        $rtvloc_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '94')
                ->where('master_lookup_categories.mas_cat_name', '=', 'RTV Location')
                ->get()->all();
        return $rtvloc_data;
    }
    
    public function getSupplierstype() {
        $suppliers_type = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '89')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Supplier Types')
                ->get()->all();
        return $suppliers_type;
    }
    
    public function getSuppliersRank() {
        $suppliers_rank = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '99')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Supplier Rank')
                ->get()->all();
        return $suppliers_rank;
    }
    
    public function getSuppliersDCrealtionship() {
        $suppliers_dcrealtionship = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '100')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Supplier DC relationship')
                ->get()->all();
        return $suppliers_dcrealtionship;
    }

    public function getLegalentityTypeID($type = '') {
        $supplierType = DB::table('master_lookup')
                ->join('master_lookup_categories', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name', 'master_lookup.master_lookup_id as id', 'master_lookup.value')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Customer Types')
                ->where('master_lookup.master_lookup_name', '=', $type)
                ->first();
        return $supplierType;
    }

    public function getAccounttype() {
        $account_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id')
                ->where('master_lookup_categories.mas_cat_id', '=', '31')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Account_Type')
                ->get()->all();
        return $account_data;
    }
    
    public function getWeightUoM() {
        $moq_data = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '16')
                ->get()->all();
        return $moq_data;
    }
    
    public function getPaymentDays() {
        $payment_days = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id')
                ->where('master_lookup_categories.mas_cat_id', '=', '98')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Payment Days')
                ->get()->all();
        return $payment_days;
    }
    
    public function getkvi() {
        $kvi = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '69')
                ->where('master_lookup_categories.mas_cat_name', '=', 'KVI')
                ->get()->all();
        return $kvi;
    }
    
    public function gettaxtypes() {
        $tax_types = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '9')
                ->where('master_lookup_categories.mas_cat_name', '=', 'Tax Types')
                ->get()->all();
        return $tax_types;
    }

    public function getatppyriod() {
        $atp_peyiod = DB::table('master_lookup_categories')
                ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                ->select('master_lookup.master_lookup_name as account_type', 'master_lookup.master_lookup_id as id', 'master_lookup.value as value')
                ->where('master_lookup_categories.mas_cat_id', '=', '80')
                ->where('master_lookup_categories.mas_cat_name', '=', 'ATP Period')
                ->get()->all();
        return $atp_peyiod;
    }	
	
    public function getRm($userId)
    {
        $userHubs = array();
        $usersWithHubs = array();
        if (isset($_SERVER["REQUEST_URI"]))
        {
            $referer = $_SERVER["REQUEST_URI"];
            $urlArray = explode('/', $referer);
            $is_provider = isset($urlArray[1]) ? $urlArray[1] : '';
        }

        if ($userId == null)
        {
            $userId = Session::get('userId');
        }
        $rback = new Role;
        $rmUserList = $rback->getFilterData(5, $userId);
        $rmUserListObj = json_decode($rmUserList);
        if ($rmUserListObj->supplier)
        {
            $usersList = $rmUserListObj->supplier;
        }
        $roleRepo = new RoleRepo();
        $rm_data=$roleRepo->getUsersByFeatureCode('VRM002');
        /*$rm_data = DB::table('users')
          ->select(DB::raw('CONCAT(users.firstname," ",users.lastname) as username'), 'users.user_id as id')
          ->whereIn('users.user_id', $usersList)
          ->whereIn('users.is_active', 1)
          ->get();*/
        if ($is_provider == 'vehicle' || $is_provider == 'vehicleproviders')
        {   
            $rm_data=$roleRepo->getUsersByFeatureCode('VRM001');
            /* 
             * $whnames = DB::table('legalentity_warehouses')->where('dc_type',118002)->pluck('lp_wh_name','le_wh_id');
            foreach ($usersList as $uid)
            {                
                $sbuData = json_decode($rback->getFilterData(6,$uid), 1);
                $sbu = isset($sbuData['sbu']) ? $sbuData['sbu'] : 0;
                $hubsData = json_decode($sbu, 1);
                $hb = (isset($hubsData['118002'])) ? $hubsData['118002'] : '';
                if($hb !='')
                {
                $userHubs[$uid] = $hb;
                }
            }
            $roleId = DB::table('roles')->where('name','Field Force Manager')->pluck('role_id');
            $roleUsers = DB::table('user_roles')->where('role_id',$roleId)->pluck('user_id');
            $rm_data2 = json_decode(json_encode($rm_data),1);
             
            $rm_data =array();
            
            foreach($rmUsers as $dat)
            { 
                /*if(in_array($dat['id'],$roleUsers))
                {
                    $hubVehUserList = (isset($userHubs[$dat['id']]))? $userHubs[$dat['id']]:'';
                    $hubVehUser = explode(',',$hubVehUserList);
                    if(isset($hubVehUser[0]) && $hubVehUser[0])
                    {
                       $hub_complete_name = $whnames[$hubVehUser[0]];
                       $hub_complete_array = explode(' ',$hub_complete_name); 
                       $hub_code = (isset($hub_complete_array[0]))?$hub_complete_array[0]:'';
                       $hub_name_value = (isset($hub_complete_array[1]))?$hub_complete_array[1]:'';
                       $rm_data[] = (object)array('username'=>$hub_name_value.'-'.$dat['username'].' ('.$hub_code.')','id'=>$dat['id']);
                       Session::set('rm_users',$rm_data);
                    }

                }
                $key = array_search($dat->user_id, array_column($rm_data, 'id'));
                if($key>=0){
                    $rm_data[] = (object)array('username'=>$dat->name,'id'=>$dat->user_id);
                }
            }*/
        }
        Session::set('rm_users',$rm_data);
        return $rm_data;
    }
	
	function returnsLocationType() {
        $returns_location_types = DB::table('master_lookup_categories')->select('master_lookup.master_lookup_name as location_name', 'master_lookup.value as location_value')
                        ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                        ->where('mas_cat_name', 'returns_location_type')->get()->all();
        return $returns_location_types;
    }

    function returnAllBrands() {
        $bm = new BrandModel();

        return $bm::select(['brands.brand_id', 'brands.brand_name'])->get()->all();
    }

    function returnLegalentityAllBrands() {
        $Brand_Model_Obj = new BrandModel();
        $legalEntityIdArray = array();
        $legal_entity_id = Session::get('legal_entity_id');
        $child_legal_entity_id = DB::table('legal_entities')->select('legal_entity_id')->where(['parent_id' => $legal_entity_id, 'legal_entity_type_id' => '1006'])->get()->all();
        foreach ($child_legal_entity_id as $val) {
            $legalEntityIdArray[] = $val->legal_entity_id;
        }
        if ($legal_entity_id == 0) {
            $query = $Brand_Model_Obj::select(['brands.brand_id', 'brands.brand_name']);
        } else {
            $query = $Brand_Model_Obj::select(['brands.brand_id', 'brands.brand_name'])->whereIn('legal_entity_id', $legalEntityIdArray);
        }
        return $query->get()->all();
    }

    function marginType() {
        $margin_types = DB::table('master_lookup_categories')->select('master_lookup.master_lookup_name as margin_type_name', 'master_lookup.value as margin_type_value')
                        ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                        ->where('mas_cat_name', 'margin_types')->get()->all();
        return $margin_types;
    }

    function inventoryType() {
        $inventory_types = DB::table('master_lookup_categories')->select('master_lookup.master_lookup_name as inventory_name', 'master_lookup.value as inventory_value')
                        ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                        ->where('mas_cat_name', 'invetory_mode')->get()->all();
        return $inventory_types;
    }

    function categoriesList() {
        $categories = DB::table('categories')->where('is_active', 1)->get()->all();
        return $categories;
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
                        
                    })->get()->all();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
	
    public function getUserNameById($id)
    {
        $userModel = new Users();
        $srmName = $userModel->select(DB::raw('CONCAT(firstname," ",lastname) as name'))
                   ->where("user_id",$id)->get()->all(); 
        if(isset($srmName[0]))
        {
            $name = $srmName[0]->name;
        }
        else
        {
            $name="";
        }
        return $name;
    }
    public function getUserEmailById($id)
    {
        $userModel = new Users();
        $srmName = $userModel->select('email_id')
                   ->where("user_id",$id)->get()->all(); 
        if(isset($srmName[0]))
        {
            $emailId = $srmName[0]->email_id;
        }
        else
        {
            $emailId="";
        }
        return $emailId;        
    }
	
	public function saveTotPrice($priceArray)
	{
		$PurchasePriceHistory = new PurchasePriceHistory();
		$PurchasePriceHistory->product_id = $priceArray['product_id'];
		$PurchasePriceHistory->supplier_id = $priceArray['supplier_id'];
		$PurchasePriceHistory->elp = $priceArray['elp'];
		$PurchasePriceHistory->le_wh_id = $priceArray['le_wh_id'];
		$PurchasePriceHistory->created_by = $priceArray['created_by'];
		$PurchasePriceHistory->effective_date = $priceArray['effective_date'];
		$PurchasePriceHistory->save();
	}
  	
	public function saveSupplier($request,$seller_id,$legalentitySessionId,$editSupplierId,$is_app=0,$methodName = NULL,$leType=NULL,$roleName=NULL)
	{
		$Legalentities = new Legalentities();
        $state_id = $request->org_billingaddress_state;
        $Users = new Users();
        $Userroles = new Userroles();
        $supplierModel = new SupplierModel();
        $custRepoObj = new CustomerRepo();
        $addContinueId = Session::get('add_continue');
                
        Session::forget('add_continue');
        if($addContinueId && $is_app != 1)
                    $editSupplierId = '';//$addContinueId;
                    $erp_code = $custRepoObj->getRefCode('SP',$state_id);
        // log::info('supp erp code=>');
        if(isset($_SERVER["HTTP_REFERER"]))
                {
                    $referer = $_SERVER["HTTP_REFERER"];
                    $urlArray = explode('/',$referer);
                    $is_provider  = isset($urlArray[3])?$urlArray[3]:'';
                }
        if (null !== $seller_id) {
            
            if (isset($editSupplierId) && !empty($editSupplierId) && isset($legalentitySessionId) && !empty($legalentitySessionId)) {
                // Adding Vehicle No and Registration Number
                if($request->org_email == ""){
                    $email = $request->org_mobile . '@yopmail.com';
                } else{
                    $email = $request->org_email;
                }
                $vehicle_id = isset($request->vehicle_id)?$request->vehicle_id:0;
                $userArray = array("firstname" => $request->org_firstname, "lastname" => $request->org_lastname, "email_id" => $email, "mobile_no" => $request->org_mobile, "landline_no" => $request->org_landline, "landline_ext" => $request->org_extnumber);
                $vendorArray = array("sup_rank" => $request->supplier_rank, "erp_code" => $request->reference_erp_code, "est_year" => $request->date_estb, "sup_add1" => $request->org_billingaddress_address1, "sup_add2" => $request->org_billingaddress_address2, "sup_country" => $request->org_billingaddress_country, "sup_state" => $request->org_billingaddress_state, "sup_city" => $request->org_billingaddress_city, "sup_pincode" => $request->org_billingaddress_pincode, "sup_account_name" => $request->org_bank_acname, "sup_bank_name" => $request->org_bank_name, "sup_account_no" => $request->org_bank_acno, "sup_account_type" => $request->org_bank_actype, "sup_ifsc_code" => $request->org_bank_ifsc, "sup_branch_name" => $request->org_bank_branch, "sup_micr_code" => $request->org_micr_code, "sup_currency_code" => $request->org_curr_code,"updated_by"=>$seller_id);
                if($is_provider == "suppliers"){
                    $pendingPayments = $this->getVendorPaymentRequestsById($editSupplierId,[57203,57204,57218,57222]);
                    if(count($pendingPayments)>0){
                        echo  json_encode(array('status' => 'false', 'message'=>'Please close payment requests initiated to update supplier'));die();
                    }

                    $supplierBankDBArray = Suppliers::where("legal_entity_id", $editSupplierId)->select("sup_account_name","sup_bank_name","sup_account_no","sup_account_type","sup_ifsc_code","sup_branch_name","sup_micr_code","sup_currency_code")->get();
                    $supplierBankDBArray = json_decode(json_encode($supplierBankDBArray),1);
                    // if(count($supplierBankDBArray) > 0){
                        $supplierBankArray = array("sup_account_name" => $request->org_bank_acname, "sup_bank_name" => $request->org_bank_name, "sup_account_no" => $request->org_bank_acno, "sup_account_type" => $request->org_bank_actype, "sup_ifsc_code" => $request->org_bank_ifsc, "sup_branch_name" => $request->org_bank_branch, "sup_micr_code" => $request->org_micr_code, "sup_currency_code" => $request->org_curr_code);
                        $supplierBankDBArray = $supplierBankDBArray[0];
                        $diff = array_diff_assoc($supplierBankDBArray, $supplierBankArray);
                        if(count($diff) > 0){
                            $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                            $userId = \Session::get('userId');
                            //$res_approval_flow_func = $approvalFlowObj->getApprovalFlowDetails('Supplier', 'drafted', $userId);
                            //if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                                // $current_status_id = $res_approval_flow_func["currentStatusId"];
                                // $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                                $comment = "Bank Details Changed </br>";
                                foreach ($diff as $key => $value) {
                                    # code...
                                    $text = ucwords(str_replace("_", " ", $key));
                                    $comment .= $text . ": ";
                                    $comment .= isset($supplierBankDBArray[$key]) ? $supplierBankDBArray[$key] : "";
                                    if(isset($supplierBankDBArray[$key]) && trim($supplierBankDBArray[$key] !="" ))
                                        $comment .= " to ";
                                    $comment .= $text .isset($supplierBankArray[$key]) ? $supplierBankArray[$key] : "";
                                    $comment .= "</br>";

                                }
                                $current_time = Carbon::now()->toDateTimeString();
                                Suppliers::where("legal_entity_id", $editSupplierId)
                                  ->update(['updated_by'=>$userId,'updated_at'=>$current_time,'status'=>57009,'is_approved'=>0]);
                                $approvalFlowObj->storeWorkFlowHistory('Supplier', $editSupplierId, 57008, 57009, $comment, $userId);
                            //}
                        }
                    // }
                    
                }     
                $supplierArray = array("sup_rank" => $request->supplier_rank ,"supplier_type" => $request->supplier_type, "erp_code" => $request->reference_erp_code, "est_year" => $request->date_estb, "sup_add1" => $request->org_billingaddress_address1, "sup_add2" => $request->org_billingaddress_address2, "sup_country" => $request->org_billingaddress_country, "sup_state" => $request->org_billingaddress_state, "sup_city" => $request->org_billingaddress_city, "sup_pincode" => $request->org_billingaddress_pincode, "sup_account_name" => $request->org_bank_acname, "sup_bank_name" => $request->org_bank_name, "sup_account_no" => $request->org_bank_acno, "sup_account_type" => $request->org_bank_actype, "sup_ifsc_code" => $request->org_bank_ifsc, "sup_branch_name" => $request->org_bank_branch, "sup_micr_code" => $request->org_micr_code, "sup_currency_code" => $request->org_curr_code,"updated_by"=>$seller_id);
                $erp_code = DB::table('legal_entities')->where('legal_entity_id', $editSupplierId)->pluck('le_code');
                $erp_code = (is_array($erp_code) && isset($erp_code[0]))?$erp_code[0]:$erp_code;
                

                switch($is_provider)
                {
                    case 'vehicleproviders': DB::table('vehicle_provider')->where("legal_entity_id", $editSupplierId)->update($vendorArray); break;
                    case 'humanresource': DB::table('hr_provider')->where("legal_entity_id", $editSupplierId)->update($vendorArray); break;
                    case 'vehicle': 

                        $checkdriver_legal_id = DB::table('vehicle')->where('vehicle_id', $vehicle_id)->pluck('driver_le_id');
                        if (isset($request->reg_no) && !empty($request->reg_no) && $request->reg_no != "undefined"){
                        $reg_no= $request->reg_no;
                        $checkregno = DB::table('vehicle')->select(DB::RAW('COUNT(vehicle_id) AS count'))->where('reg_no',$reg_no)->whereNotIn("vehicle_type",[156002])->whereNotIn('vehicle_id',[$vehicle_id])->get()->all();

                          if(($checkregno[0]->count)>0){
                            echo  json_encode(array('status' => '500', 'reg_no'=>'true'));die();
                          }
                        }

                        if (isset($checkdriver_legal_id[0]) && ($checkdriver_legal_id[0]==''|| $checkdriver_legal_id[0]==0)){
                            $veh_le_id = $this->getMasterLookupValue(78012);
                            if($editSupplierId!=$veh_le_id){
                                $leglarr = ['le_code' => $erp_code,'parent_id' => $editSupplierId,'legal_entity_type_id' => 1013];
                                $DriverLegalentityId = DB::table('legal_entities')->insertGetId($leglarr);
                                $checkuser_id = DB::table('users')->where('legal_entity_id', $editSupplierId)->select('user_id')->first();

                                $driverrole = array("legal_entity_id"=>$DriverLegalentityId,
                                             "user_id"=>$checkuser_id->user_id,
                                             "driving_license_no"=>$request->license_no,
                                             "license_exp_date"=>$request->license_exp_date);
                                $driver_insert = DB::table('drivers')->insert($driverrole);
                                Users::where("legal_entity_id", $editSupplierId)->update(['legal_entity_id'=>$DriverLegalentityId]);
                                DB::table('drivers')->where('legal_entity_id', $DriverLegalentityId)->update(['driving_license_no' => $request->license_no, 'license_exp_date' => $request->license_exp_date]);
                            }else{
                                $DriverLegalentityId = 33848;
                            }
                        }else{
                            $DriverLegalentityId = isset($checkdriver_legal_id[0])?$checkdriver_legal_id[0]:0;
                        }
                        $vendorArray['reg_no']=$request->reg_no;
                        $vendorArray['vehicle_model']=$request->vehicle_model;
                        $vendorArray['driver_le_id']=$DriverLegalentityId;
                        DB::table('vehicle')->where("vehicle_id", $vehicle_id)->update($vendorArray);
                        
                        if(isset($request->reg_no) and !empty($request->reg_no))
                            $organization_name = $request->vehicle_name.' '.$request->reg_no;
                        else
                            $organization_name = $request->organization_name;

                        $ownerlegalEntityArray = array("business_legal_name" => $organization_name, "business_type_id" => $request->organization_type, 
                            "website_url" => $request->org_site, "address1" => $request->org_billingaddress_address1, "address2" => $request->org_billingaddress_address2, 
                            "country" => $request->org_billingaddress_country,"state_id" => $request->org_billingaddress_state, 
                            "city" => $request->org_billingaddress_city, "pincode" => $request->org_billingaddress_pincode, "rel_manager" => $request->org_rm,
                            "updated_by"=>$seller_id);
                        
                        Legalentities::where("legal_entity_id", $editSupplierId)
                                        ->update($ownerlegalEntityArray);
                        
                        $driver_legal_id = DB::table('vehicle')->where('vehicle_id', $vehicle_id)->pluck('driver_le_id');                        
                        if (isset($driver_legal_id[0]))
                            $editSupplierId = $driver_legal_id[0];
                            DB::table('drivers')->where('legal_entity_id', $driver_legal_id[0])->update(['driving_license_no' => $request->license_no, 'license_exp_date' => $request->license_exp_date]);
                        break;
                    case 'serviceproviders': DB::table('service_provider')->where("legal_entity_id", $editSupplierId)->update($vendorArray); break;
                    case 'space': DB::table('space')->where("legal_entity_id", $editSupplierId)->update($vendorArray); break;
                    case 'spaceprovider': DB::table('space_provider')->where("legal_entity_id", $editSupplierId)->update($vendorArray); break;
                    default: Suppliers::where("legal_entity_id", $editSupplierId)->update($supplierArray); break;
                }

                Users::where("legal_entity_id", $editSupplierId)
                        ->update($userArray);

                $legalEntityArray = array("business_legal_name" => $request->organization_name, "business_type_id" => $request->organization_type, 
                    "website_url" => $request->org_site, "address1" => $request->org_address1, "address2" => $request->org_address2, 
                    "country" => $request->org_country, "state_id" => $request->org_state, "city" => $request->org_city, "pincode" => $request->org_pincode, 
                    "rel_manager" => $request->org_rm,"updated_by"=>$seller_id);

                Legalentities::where("legal_entity_id", $editSupplierId)
                        ->update($legalEntityArray);
                // log::info('supp erp code edit=>');       
                //             log::info($erp_code);

                if ($is_app) {
                    return json_encode(array('status' => 'true', 'legalentity_id' => $legalentitySessionId, 'supplier_id' => $editSupplierId, 'erp_code' => $erp_code));
                } else {
                    echo json_encode(array('status' => 'true', 'legalentity_id' => $legalentitySessionId, 'supplier_id' => $editSupplierId, 'erp_code' => $erp_code));
                }
            } else {
                DB::beginTransaction();
                //echo "string";die;
                //$legal_entity_id = Session::get('legal_entity_id');
                try {
                    if (isset($request->reg_no) && !empty($request->reg_no) && $request->reg_no != "undefined"){
                        $reg_no= $request->reg_no;
                        $checkregno = DB::table('vehicle')->select(DB::RAW('COUNT(vehicle_id) AS count'))->where('reg_no',$reg_no)->whereNotIn("vehicle_type",[156002])->get()->all();

                          if(($checkregno[0]->count)>0){
                            echo  json_encode(array('status' => '500', 'reg_no'=>'true'));die();
                          }
                    }

                    // Adding Vehicle No and Registration Number
                    if (isset($request->reg_no) && !empty($request->reg_no) && $request->reg_no != "undefined"){
                        $business_legal_name=$request->vehicle_name . ' ' . $request->reg_no;
                        $Legalentities->business_legal_name = $business_legal_name;
                        $checkbusinesslegalname = DB::table('legal_entities')->select(DB::RAW('COUNT(legal_entity_id) AS count'))->where('business_legal_name',$business_legal_name)->get()->all();

                          if(($checkbusinesslegalname[0]->count)>0){
                            echo  json_encode(array('status' => '400', 'businesslegalnameexists'=>'true'));die();
                          }
                    }
                    else
                        $Legalentities->business_legal_name = $request->organization_name;

                    $Legalentities->business_type_id = $request->organization_type;
                    $Legalentities->website_url = $request->org_site;
                    $Legalentities->le_code = $erp_code;
                     // Added Parent Legal Id -> As we deal with multiple companies from now
                    $Legalentities->parent_le_id = $legalentitySessionId;
                    //$Legalentities->logo = $files;
                    if($leType==1008){
                        $Legalentities->address1 = $request->org_billingaddress_address1;
                        $Legalentities->address2 = $request->org_billingaddress_address2;
                        $Legalentities->country = $request->org_billingaddress_country;
                        $Legalentities->state_id = $request->org_billingaddress_state;
                        $Legalentities->city = $request->org_billingaddress_city;
                        $Legalentities->pincode = $request->org_billingaddress_pincode;
                    } else {
                        $Legalentities->address1 = $request->org_address1;
                        $Legalentities->address2 = $request->org_address2;
                        $Legalentities->country = $request->org_country;
                        $Legalentities->state_id = $request->org_state;
                        $Legalentities->city = $request->org_city;
                        $Legalentities->pincode = $request->org_pincode;
                    }

                    $Legalentities->parent_id = $legalentitySessionId;
                    if ($leType) {
                        $Legalentities->legal_entity_type_id = $leType;
                    } else {
                        $Legalentities->legal_entity_type_id = 1002;
                    }
                    $Legalentities->rel_manager = $request->org_rm;
                    $Legalentities->created_by = $seller_id;
                // echo "here i'm";print_r(json_decode(json_encode($Legalentities),1));die(); 
                    $legal[] = $Legalentities->save();
                    $LegalentityId = $Legalentities->legal_entity_id;
                    $OnwerLegalentityId = $LegalentityId;                    
                    //echo 'check userr exist ===' . $request->driver_contact_old;    
                    $drcontactno = isset($request->driver_contact_old)?$request->driver_contact_old:'';
                    if ($is_provider=='vehicle' || $drcontactno == '') {
                        $OwnerLegalentities = new Legalentities();

                        //inerting owner address into table
                        $OwnerLegalentities->business_legal_name = $business_legal_name;
                        $OwnerLegalentities->business_type_id = $request->organization_type;
                        $OwnerLegalentities->website_url = $request->org_site;
                        $OwnerLegalentities->le_code = $erp_code;
                        //$Legalentities->logo = $files;
                        
                        $OwnerLegalentities->address1 = $request->org_address1;
                        $OwnerLegalentities->address2 = $request->org_address2;
                        $OwnerLegalentities->country = $request->org_country;
                        $OwnerLegalentities->state_id = $request->org_state;
                        $OwnerLegalentities->city = $request->org_city;
                        $OwnerLegalentities->pincode = $request->org_pincode;
                         // Added Parent Legal Id -> As we deal with multiple companies from now
                        $OwnerLegalentities->parent_le_id = $legalentitySessionId;
                        $OwnerLegalentities->parent_id = $LegalentityId;
                        
                        $OwnerLegalentities->legal_entity_type_id = 1013;
                        $OwnerLegalentities->rel_manager = $request->org_rm;
                        $OwnerLegalentities->created_by = $seller_id;                        
                        $legal[] = $OwnerLegalentities->save();
                        $DriverLegalentityId = $OwnerLegalentities->legal_entity_id;
                        // print_r($OnwerLegalentityId);
                    } else {
                        $driver_legal_id = DB::table('users')->where('user_id', $request->driver_contact_old)->pluck('legal_entity_id');
                        $DriverLegalentityId = (isset($driver_legal_id[0]))?$driver_legal_id[0]:0;
                    }
                    Session::set('add_continue', $LegalentityId);
                } catch (ValidationException $e) {
                    DB::rollback();
                }
                //die;
                try {
                    if ($request->org_email == "") {
                        $email = $request->org_mobile . '@yopmail.com';
                    } else {
                        $email = $request->org_email;
                    }
                    if ($is_provider!='vehicle' || $drcontactno == '') {
                        $Users->firstname = $request->org_firstname;
                        $Users->lastname = $request->org_lastname;
                        $Users->email_id = $email;
                        $Users->mobile_no = $request->org_mobile;
                        $Users->landline_no = $request->org_landline;
                        $Users->landline_ext = $request->org_extnumber;
                        $Users->legal_entity_id = ($is_provider=='vehicle')?$DriverLegalentityId:$LegalentityId;
                        $Users->password = md5($request->org_firstname);
                        $user[] = $Users->save();
                        $Userid = $Users->user_id;
                    } else {
                        $Userid = $request->driver_contact_old;
                        $user[] = array();
                    }
                    
                } catch (ValidationException $e) {
                    DB::rollback();
                }

                try {
                    //insert into user roles tbl
                    if ($request->driver_contact_old == '') {
                        $roleIds = DB::table('roles')->where('short_code', $roleName)->pluck('role_id');
                        $roleId = (isset($roleIds[0])) ? $roleIds[0] : 4;
                        $Userroles->role_id = $roleId;
                        $Userroles->user_id = $Userid;
                        $userole[] = $Userroles->save();
                    } else {
                        $userole[] = array();
                    }
                } catch (ValidationException $e) {
                    DB::rollback();
                }
                $temp_id = $LegalentityId;
                try {
                    //insert into suppliers tbale
                    //$Suppliers->erp_code = $erp_code;
                    if ($methodName == NULL) {
                        Log::info('NULL method');
                        $Supplier_id = $supp = $returnValue = $this->saveSupplierTable($request, $seller_id, $LegalentityId);
                        $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                        $comment = "New Supplier Created";
                        $userId = Session::get('userId');
                        $approvalFlowObj->storeWorkFlowHistory('Supplier', $LegalentityId, 57008, 57009, $comment, $userId);

                    } else {
                        Log::info('vendor method');
                        $request->license_exp_date = date('Y-m-d', strtotime($request->license_exp_date));
                        $Supplier_id = $supp = $returnValue = $this->$methodName($request, $seller_id, $LegalentityId);
                    }
                } catch (ValidationException $e) {
                    DB::rollback();
                }
                DB::commit();
                if($leType == 1008)
                {
                    if($request->driver_contact_old != ''){
                        $Ulegal_entity_id = DB::select(DB::raw("select legal_entity_id from users where users.user_id = '$request->driver_contact_old'"));
                        $Ulegal_entity_id = $Ulegal_entity_id[0]->legal_entity_id;
                    }else{
                        $driverrole = array("legal_entity_id"=>$DriverLegalentityId,
                                         "user_id"=>$Userid,
                                         "driving_license_no"=>$request->license_no,
                                         "license_exp_date"=>$request->license_exp_date);
                        $driver_insert = DB::table('drivers')->insert($driverrole);
                        $Ulegal_entity_id = $DriverLegalentityId;
                    }                
                    $res = DB::table('vehicle')
                        ->where('legal_entity_id', $LegalentityId)
                        ->update(['reg_no'=>$request->reg_no,'driver_le_id'=>$Ulegal_entity_id]);
                }
                if (!$legal || !$user || !$userole || !$supp) {
                    //Log::info($legal);
                    //Log::info($user);
                    //Log::info($userole);
                    //Log::info($supp);
                    
                    
                    echo "false";
                }
                if ($legal && $user && $userole && $supp) {    
                    Session::forget('recent_legalentity_id');
                    Session::put('recent_legalentity_id', $LegalentityId);
                    Session::put('supplier_id', $LegalentityId);
                    if ($is_app) {
                        //die('in app');
                        return json_encode(array('status' => 'true', 'legalentity_id' => $LegalentityId, 'supplier_id' => $Supplier_id, 'erp_code' => $erp_code));
                    } else {
                        //die('in web');
                        echo json_encode(array('status' => 'true', 'legalentity_id' => $LegalentityId, 'supplier_id' => $Supplier_id, 'erp_code' => $erp_code));
                    }
                }
            }   
        } else {
            //Log::info('user id not found');
            echo "false";
        }//return redirect('suppliers');        
    }
        
        public function saveSupplierTable($request,$seller_id,$LegalentityId)
        {
            $Suppliers = new Suppliers();
            //echo "<pre>";print_r($request); die;
            $Suppliers->legal_entity_id = $LegalentityId;
            $Suppliers->supplier_type = $request->supplier_type;
            $Suppliers->est_year = $request->date_estb;
            $Suppliers->sup_add1 = $request->org_billingaddress_address1;
            $Suppliers->sup_add2 = $request->org_billingaddress_address2;
            $Suppliers->sup_country = $request->org_billingaddress_country;
            $Suppliers->sup_state = $request->org_billingaddress_state;
            $Suppliers->sup_city = $request->org_billingaddress_city;
            $Suppliers->sup_pincode = $request->org_billingaddress_pincode;
            $Suppliers->sup_account_name = $request->org_bank_acname;
            $Suppliers->sup_bank_name = $request->org_bank_name;
            $Suppliers->sup_account_no = $request->org_bank_acno;
            $Suppliers->sup_account_type = $request->org_bank_actype;
            $Suppliers->sup_ifsc_code = $request->org_bank_ifsc;
            $Suppliers->sup_branch_name = $request->org_bank_branch;
            $Suppliers->sup_micr_code = $request->org_micr_code;
            $Suppliers->sup_currency_code = $request->org_curr_code;
            $Suppliers->sup_rm = $request->org_rm;
            $Suppliers->sup_rank = $request->supplier_rank;
            $Suppliers->created_by = $seller_id;
            $Suppliers->save();
            return $Suppliers->supplier_id;
        }
        public function saveServiceProviderTable($request,$seller_id,$LegalentityId)
        {
            $serviceProvider = new ServiceProvider();
            //echo "<pre>";print_r($request); die;
            $serviceProvider->legal_entity_id = $LegalentityId;
            //$serviceProvider->supplier_type = $request->supplier_type;
            $serviceProvider->est_year = $request->date_estb;
            $serviceProvider->sup_add1 = $request->org_billingaddress_address1;
            $serviceProvider->sup_add2 = $request->org_billingaddress_address2;
            $serviceProvider->sup_country = $request->org_billingaddress_country;
            $serviceProvider->sup_state = $request->org_billingaddress_state;
            $serviceProvider->sup_city = $request->org_billingaddress_city;
            $serviceProvider->sup_pincode = $request->org_billingaddress_pincode;
            $serviceProvider->sup_account_name = $request->org_bank_acname;
            $serviceProvider->sup_bank_name = $request->org_bank_name;
            $serviceProvider->sup_account_no = $request->org_bank_acno;
            $serviceProvider->sup_account_type = $request->org_bank_actype;
            $serviceProvider->sup_ifsc_code = $request->org_bank_ifsc;
            $serviceProvider->sup_branch_name = $request->org_bank_branch;
            $serviceProvider->sup_micr_code = $request->org_micr_code;
            $serviceProvider->sup_currency_code = $request->org_curr_code;
            $serviceProvider->sup_rm = $request->org_rm;
            //$serviceProvider->sup_rank = $request->supplier_rank;
            $serviceProvider->created_by = $seller_id;
            $serviceProvider->save();
            return $serviceProvider->service_pro_id;
        }	
        public function saveVehicleTable($request,$seller_id,$LegalentityId)
        {
            $serviceProvider = new Vehicle();
            log::info($request);
            //echo "<pre>helloo";print_r($request); die;
            $serviceProvider->legal_entity_id = $LegalentityId;
           // $serviceProvider->vehicle_type = $request->supplier_type;
            $serviceProvider->vehicle_model = $request->vehicle_model;
            $serviceProvider->est_year = $request->date_estb;
            $serviceProvider->reg_no = $request->reg_no;
            $serviceProvider->sup_add1 = $request->org_billingaddress_address1;
            $serviceProvider->sup_add2 = $request->org_billingaddress_address2;
            $serviceProvider->sup_country = $request->org_billingaddress_country;
            $serviceProvider->sup_state = $request->org_billingaddress_state;
            $serviceProvider->sup_city = $request->org_billingaddress_city;
            $serviceProvider->sup_pincode = $request->org_billingaddress_pincode;
            $serviceProvider->sup_account_name = $request->org_bank_acname;
            $serviceProvider->sup_bank_name = $request->org_bank_name;
            $serviceProvider->sup_account_no = $request->org_bank_acno;
            $serviceProvider->sup_account_type = $request->org_bank_actype;
            $serviceProvider->sup_ifsc_code = $request->org_bank_ifsc;
            $serviceProvider->sup_branch_name = $request->org_bank_branch;
            $serviceProvider->sup_micr_code = $request->org_micr_code;
            $serviceProvider->sup_currency_code = $request->org_curr_code;
            $serviceProvider->sup_rm = $request->org_rm;
            //$serviceProvider->sup_rank = $request->supplier_rank;
            $serviceProvider->created_by = $seller_id;
            $serviceProvider->save();
            return $serviceProvider->vehicle_id;
        }	
        public function saveVehServiceTable($request,$seller_id,$LegalentityId)
        {
            $serviceProvider = new VehicleService();
            //echo "<pre>";print_r($request); die;
            $serviceProvider->legal_entity_id = $LegalentityId;
            //$serviceProvider->supplier_type = $request->supplier_type;
            $serviceProvider->est_year = $request->date_estb;
            $serviceProvider->sup_add1 = $request->org_billingaddress_address1;
            $serviceProvider->sup_add2 = $request->org_billingaddress_address2;
            $serviceProvider->sup_country = $request->org_billingaddress_country;
            $serviceProvider->sup_state = $request->org_billingaddress_state;
            $serviceProvider->sup_city = $request->org_billingaddress_city;
            $serviceProvider->sup_pincode = $request->org_billingaddress_pincode;
            $serviceProvider->sup_account_name = $request->org_bank_acname;
            $serviceProvider->sup_bank_name = $request->org_bank_name;
            $serviceProvider->sup_account_no = $request->org_bank_acno;
            $serviceProvider->sup_account_type = $request->org_bank_actype;
            $serviceProvider->sup_ifsc_code = $request->org_bank_ifsc;
            $serviceProvider->sup_branch_name = $request->org_bank_branch;
            $serviceProvider->sup_micr_code = $request->org_micr_code;
            $serviceProvider->sup_currency_code = $request->org_curr_code;
            $serviceProvider->sup_rm = $request->org_rm;
            //$serviceProvider->sup_rank = $request->supplier_rank;
            $serviceProvider->created_by = $seller_id;
            $serviceProvider->save();
            return $serviceProvider->vehicle_pro_id;
        }	
        public function saveHrProviderTable($request,$seller_id,$LegalentityId)
        {
            $serviceProvider = new HrProvider();
            //echo "<pre>";print_r($request); die;
            $serviceProvider->legal_entity_id = $LegalentityId;
            //$serviceProvider->supplier_type = $request->supplier_type;
            $serviceProvider->est_year = $request->date_estb;
            $serviceProvider->sup_add1 = $request->org_billingaddress_address1;
            $serviceProvider->sup_add2 = $request->org_billingaddress_address2;
            $serviceProvider->sup_country = $request->org_billingaddress_country;
            $serviceProvider->sup_state = $request->org_billingaddress_state;
            $serviceProvider->sup_city = $request->org_billingaddress_city;
            $serviceProvider->sup_pincode = $request->org_billingaddress_pincode;
            $serviceProvider->sup_account_name = $request->org_bank_acname;
            $serviceProvider->sup_bank_name = $request->org_bank_name;
            $serviceProvider->sup_account_no = $request->org_bank_acno;
            $serviceProvider->sup_account_type = $request->org_bank_actype;
            $serviceProvider->sup_ifsc_code = $request->org_bank_ifsc;
            $serviceProvider->sup_branch_name = $request->org_bank_branch;
            $serviceProvider->sup_micr_code = $request->org_micr_code;
            $serviceProvider->sup_currency_code = $request->org_curr_code;
            $serviceProvider->sup_rm = $request->org_rm;
            //$serviceProvider->sup_rank = $request->supplier_rank;
            $serviceProvider->created_by = $seller_id;
            $serviceProvider->save();
            return $serviceProvider->hr_pro_id;
        }	
        public function saveSpaceTable($request,$seller_id,$LegalentityId)
        {
            $serviceProvider = new Space();
            //echo "<pre>";print_r($request); die;
            $serviceProvider->legal_entity_id = $LegalentityId;
            //$serviceProvider->supplier_type = $request->supplier_type;
            $serviceProvider->est_year = $request->date_estb;
            $serviceProvider->sup_add1 = $request->org_billingaddress_address1;
            $serviceProvider->sup_add2 = $request->org_billingaddress_address2;
            $serviceProvider->sup_country = $request->org_billingaddress_country;
            $serviceProvider->sup_state = $request->org_billingaddress_state;
            $serviceProvider->sup_city = $request->org_billingaddress_city;
            $serviceProvider->sup_pincode = $request->org_billingaddress_pincode;
            $serviceProvider->sup_account_name = $request->org_bank_acname;
            $serviceProvider->sup_bank_name = $request->org_bank_name;
            $serviceProvider->sup_account_no = $request->org_bank_acno;
            $serviceProvider->sup_account_type = $request->org_bank_actype;
            $serviceProvider->sup_ifsc_code = $request->org_bank_ifsc;
            $serviceProvider->sup_branch_name = $request->org_bank_branch;
            $serviceProvider->sup_micr_code = $request->org_micr_code;
            $serviceProvider->sup_currency_code = $request->org_curr_code;
            $serviceProvider->sup_rm = $request->org_rm;
            //$serviceProvider->sup_rank = $request->supplier_rank;
            $serviceProvider->created_by = $seller_id;
            $serviceProvider->save();
            return $serviceProvider->space_id;
        }	

        public function saveSpaceProviderTable($request,$seller_id,$LegalentityId)
        {
            $serviceProvider = new SpaceProvider();
            //echo "<pre>";print_r($request); die;
            $serviceProvider->legal_entity_id = $LegalentityId;
            //$serviceProvider->supplier_type = $request->supplier_type;
            $serviceProvider->est_year = $request->date_estb;
            $serviceProvider->sup_add1 = $request->org_billingaddress_address1;
            $serviceProvider->sup_add2 = $request->org_billingaddress_address2;
            $serviceProvider->sup_country = $request->org_billingaddress_country;
            $serviceProvider->sup_state = $request->org_billingaddress_state;
            $serviceProvider->sup_city = $request->org_billingaddress_city;
            $serviceProvider->sup_pincode = $request->org_billingaddress_pincode;
            $serviceProvider->sup_account_name = $request->org_bank_acname;
            $serviceProvider->sup_bank_name = $request->org_bank_name;
            $serviceProvider->sup_account_no = $request->org_bank_acno;
            $serviceProvider->sup_account_type = $request->org_bank_actype;
            $serviceProvider->sup_ifsc_code = $request->org_bank_ifsc;
            $serviceProvider->sup_branch_name = $request->org_bank_branch;
            $serviceProvider->sup_micr_code = $request->org_micr_code;
            $serviceProvider->sup_currency_code = $request->org_curr_code;
            $serviceProvider->sup_rm = $request->org_rm;
            //$serviceProvider->sup_rank = $request->supplier_rank;
            $serviceProvider->created_by = $seller_id;
            $serviceProvider->save();
            return $serviceProvider->space_pro_id;
        }	

        
    public function getHubs()
    {
        $hublist = '<option value="">Please Select Hub ...</option> ';
        $hublistObj = DB::table('legalentity_warehouses')->where('dc_type',118002)->select('le_wh_id','lp_wh_name')->get()->all();
        return $hublistObj;
    }    
    
    public function getHubsList()
    {
        return  $this->getChildHubsList(0,118001);
    }
    public function getChildHubsList($bid,$level)
    {
         $legal_entity_id = Session::get('legal_entity_id');
        $bussinesUnits = DB::table('business_units')
                ->select('bu_id','bu_name','parent_bu_id')
                ->where('is_active','1')
                ->where('legal_entity_id','=',$legal_entity_id)
                ->where('parent_bu_id','=',$bid)
                ->get()->all();  
        
        if (!empty($bussinesUnits)) 
        {
            foreach($bussinesUnits as  $units)
            { 
                $css_class='';
                switch ($level) {
                    case 1:
                        $css_class='parent_cat';
                        break;
                    case 2:
                        $css_class='sub_cat';
                        break;
                }
                $this->bussinessUnitList.= '<option value="'.$units->bu_id.'" class="'.$css_class.'" > '.$units->bu_name.'</option>';
                $this->getChildHubsList($units->bu_id,$level+1);
            }
        }
        return $this->bussinessUnitList;
    }
    
    public function getGridData($request,$fields,$query) {
        //$this->_roleRepo = new RoleRepo();
        $this->grid_field_db_match = $fields;
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;
        
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
        $row_count = count($query->get());
        $query->skip($skip)->take($pageSize);
        $GridData = $query->get()->all();
        return array($GridData,$row_count);
        //return  json_encode(array('Records' => $GridData, 'TotalRecordsCount' => $row_count));
    }    


    public function supplierMapGrid($makeFinalSql, $orderBy, $page, $pageSize){

      if($orderBy!=''){
        $orderBy = ' ORDER BY ' . $orderBy;
      }

      $sqlWhrCls = '';
      $countLoop = 0;
      
      foreach ($makeFinalSql as $value) {
        if( $countLoop==0 ){
          $sqlWhrCls .= ' WHERE ' . $value;
        }elseif( count($makeFinalSql)==$countLoop ){
          $sqlWhrCls .= $value;
        }else{
          $sqlWhrCls .= ' AND ' .$value;
        }
        $countLoop++;
      }

        $sqlQuery = "SELECT * FROM (SELECT *,getManfName(manf_id) AS manf_name,getBusinessLegalName(legal_entity_id) AS legal_entity_name,getLeWhName(le_wh_id) AS le_wh_name,
        CONCAT ('<center><label class=\"switch\"><input class=\"switch-input change_sup_active_status\"  type=\"checkbox\" ',IF(supplier_wh_mapping.`status` = 1, 'checked=\"true\"', 'check=\"false\"'),' name=',map_id,' id=',map_id,' value=',map_id,'><span class=\"switch-label\" data-on=\"Yes\"  data-off=\"No\"></span><span class=\"switch-handle\"></span></label></center>') AS status_type,
        CONCAT('<center>
        <code>                            
        <a href=\"javascript:void(0)\" onclick=\"getSupplierMap(',map_id,')\">
        <i class=\"fa fa-edit\"></i>
        </a> &nbsp&nbsp
        <a href=\"javascript:void(0)\" onclick=\"deletesuppliermapping(',map_id,')\">
        <i class=\"fa fa-trash\"></i>
        </a>
        </code>
        </center>') AS actions FROM `supplier_wh_mapping`) AS innertbl ".$sqlWhrCls;
      $pageLimit = '';
      if($page!='' && $pageSize!=''){
        $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
      }
      $allRecallData = DB::selectFromWriteConnection(DB::raw($sqlQuery . $pageLimit));
      $TotalRecordsCount = count($allRecallData);
      return json_encode(array('results'=>$allRecallData, 'TotalRecordsCount'=>(int)($TotalRecordsCount)));
    }

    public function addNewMappingDB($data){
        $supplier_id = DB::table('supplier_wh_mapping')->insertGetId($data);
        return ($supplier_id>0)?$supplier_id:0;
    }

    public function updateMappingDB($data,$where_data){
        $map_id = DB::table('supplier_wh_mapping')->where($where_data)->update($data);
        return ($map_id>0)?$map_id:0;
    }

    public function deleteSupplierMappingDB($map_id){
        $map_id = DB::table('supplier_wh_mapping')->where('map_id','=',$map_id)->delete();
        return $map_id;
    }

    public function getSupplierMappingDB($map_id){
        $map_data = DB::table('supplier_wh_mapping')->where('map_id','=',$map_id)->get()->all();
        return $map_data;
    }

    public function checkSupMapping($data){
        $count = DB::table('supplier_wh_mapping')->where($data)->count();
        return $count;
    }

    public function getSuppliersForMapping(){

        $legal_entity_id = Session::get("legal_entity_id");
        $supplierList = DB::table('legal_entities')
                        ->join('suppliers', 'suppliers.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['legal_entities.legal_entity_type_id' => 1002, 'suppliers.is_active' => 1, 'legal_entities.is_approved' => 1, 'parent_id'=>$legal_entity_id])
                        ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name'])->all();
        return $supplierList;
    }
    public function getMasterLookupValue($value)
    {
        // To get Temp Vehicle Legal Id and Provider Id
        $query = '
            SELECT value,description
            FROM master_lookup
            WHERE value IN (?)';
        
        $result = DB::SELECT($query,[$value]);

        if($result[0]->value == $value)
            return $result[0]->description;
        return '';
    }

    public function getApprovalHistory($module,$id) {
        try
        {
            $approvalFlowObj = new CommonApprovalFlowFunctionModel();
            $totalhistory=$approvalFlowObj->getApprovalHistoryFromCommentsTable($id,$module);
            if(count($totalhistory)>0){
                $history=json_decode($totalhistory[0]->comments,1);

            }else{
                $history=$approvalFlowObj->getApprovalHistory($module,$id);
                $history=array_reverse($history);
            }
            return json_decode(json_encode($history),true);
        }catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /**
     * Get the PO count whose payment is initiated
     * @return [type] [description]
     */
    public function getVendorPaymentRequestsById($supplier_id,$status){
        $fieldArr = array(
            DB::raw('vpr.id,vpr.po_id')
        );
        $query = DB::table('vendor_payment_request as vpr')->select($fieldArr);
        $query->leftJoin('po','po.po_id','=','vpr.po_id');
        $query->where('po.legal_entity_id', $supplier_id);
        $query->whereIn('vpr.approval_status', $status);
       // $query->groupBy('po.po_id');
        return $po = $query->get()->all();
    }
}
