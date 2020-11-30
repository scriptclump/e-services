<?php

namespace App\Modules\Caching\Controllers;

use DB;
use Log;
use View;
use Session;
use Request;
use Redirect;
use Response; 
use App\Lib\Queue;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use App\Modules\Caching\Models\CachingModel;
use Utility; 
use App\Modules\Users\Models\Users;

define('CACHE_TIME', 60);

class CachingController extends BaseController{

    protected $appKeyData;

	public function __construct(CachingModel $cachingObj, RoleRepo $roleAccess){
        try
        {
            $this->cachingObj = $cachingObj;
            $this->appKeyData = env('DB_DATABASE');
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('caching.caching.caching_caption'));

            
        
            $this->middleware(function ($request, $next) use($roleAccess){
                if(!Session::has('userId'))
                    return Redirect::to('/');
		$accessPermission = false;
	        $accessPermission = $roleAccess->checkPermissionByFeatureCode('CASHFLUSH');
	    
               if(!$accessPermission)
	        return Redirect::to('/');

                return $next($request);
            });

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return trans('caching.message.in_exception');
        }


	}

	public function index()
    {
        try
        {
            parent::Breadcrumbs(array('Home' => '/', 'Administration' => '#', 'Caching' => '/cache'));

            $rolerepo = new RoleRepo();
            $checkAccess = $rolerepo->checkPermissionByFeatureCode('CACHE001');
            if(!$checkAccess)
            {
                return redirect()->to('/');
            }

            $productsInfo = $this->cachingObj->getProductInfo();

            $productsInfoArr = array();
            if(isset($productsInfo))
                foreach($productsInfo as $product)
                    $productsInfoArr[$product->product_id] = $product->product_title." (".$product->product_sku.')';

            $customerTypeInfo = $this->cachingObj->getCustomerType();

            $customerTypeInfoArr = array();
            if(isset($customerTypeInfo))
                foreach($customerTypeInfo as $customer)
                    $customerTypeInfoArr[$customer->value] = $customer->master_lookup_name;

            $dcInfo = $this->cachingObj->getDcInfo();

            $dcInfoArr = array();
            if(isset($dcInfo))
                foreach($dcInfo as $dc)
                    $dcInfoArr[$dc->le_wh_id] = $dc->lp_wh_name;

            $beatInfo = $this->cachingObj->getBeatInfo();

            $beatInfoArr = array();
            if(isset($beatInfo))
                foreach($beatInfo as $beat)
                {
                    if(isset($beat->spoke_name))
                        $beatInfoArr[$beat->beat_id] = $beat->beat_name ." (".$beat->spoke_name.")";
                    else
                        $beatInfoArr[$beat->beat_id] = $beat->beat_name;
                }

            $brandsInfo = $this->cachingObj->getBrandInfo();

            $brandsInfoArr = array();
            if(isset($brandsInfo))
                foreach($brandsInfo as $brand)
                    $brandsInfoArr[$brand->brand_id] = $brand->brand_name;

            $categoryInfo = $this->cachingObj->getCategoryInfo();

            $categoryInfoArr = array();
            if(isset($categoryInfo))
                foreach($categoryInfo as $category)
                    $categoryInfoArr[$category->category_id] = $category->cat_name;

            $manufacturerInfo = $this->cachingObj->getManufacturerInfo();
            $manufacturerInfoArr = array();
            if(isset($manufacturerInfo))
                foreach($manufacturerInfo as $manufacturer)
                    $manufacturerInfoArr[$manufacturer->legal_entity_id] = $manufacturer->business_legal_name;

            // Dynamic Cache
            $dynamicKeysList = $this->cachingObj->getDynamicKeysList();

            $segmentList = $this->cachingObj->getSegments();


            return view('Caching::index')
                    ->with('dcInfo',$dcInfoArr)
                    ->with('beatInfo',$beatInfoArr)
                    ->with('brandsInfo',$brandsInfoArr)
                    ->with('categoryInfo',$categoryInfoArr)
                    ->with('productsInfo',$productsInfoArr)
                    ->with('dynamicKeysInfo',$dynamicKeysList)
                    ->with('customerTypeInfo',$customerTypeInfoArr)
                    ->with('manufacturerInfo',$manufacturerInfoArr)
                    ->with('segmentinfo',$segmentList);

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        	return trans('caching.message.in_exception');
        }
    }

    public function flushProductsSlab($product_id = null,$customer_type_id = null,$dc_id = null){
       
        $undeleteCount = -1;
        $deleteCount = -1;
        $typeDeleteCount = -1;
        $typeUndeleteCount = -1;
        $status = false;
        $message = null;
        $user_id = Session::get('userId');
        $environment = env('APP_ENV');
        if($customer_type_id == "all")
            $customerTypes = $this->cachingObj->getCustomerType();
        else
            $customerTypes = $this->cachingObj->getCustomerType($customer_type_id);
     
        if(!empty($product_id ==='all') || !empty ($dc_id ==='all') || !empty($customer_type_id ==='all')){

                if($product_id == "all" && $dc_id == "all" && $customer_type_id === 'all'){
                    $queue = new Queue();
                    $args = array("ConsoleClass" => 'flushAllCache', 'arguments' => array($customer_type_id,$this->appKeyData,$dc_id,$user_id));
                    Log::info('Before Queue 1');
                    log::info($args);
                   $queue->enqueue('default', 'ResqueJobRiver', $args);
                    
                }elseif ($product_id === "all"  && $dc_id === "all"){
                    
                    $queue = new Queue();
                    $args = array("ConsoleClass" => 'flushAllCacheDcProducts', 'arguments' => array($customer_type_id,$this->appKeyData,$dc_id,$user_id));
                    Log::info('Before Queue 7v');
                    log::info($args);
                    $queue->enqueue('default', 'ResqueJobRiver', $args);

                }elseif($product_id === "all" && $customer_type_id == "all"){
                    $queue = new Queue();
                    $args = array("ConsoleClass" => 'flushAllCacheCustomerProducts', 'arguments' => array($customer_type_id,$this->appKeyData,$dc_id,$user_id));
                    Log::info('Before Queue 3');
                    log::info($args);
                    $queue->enqueue('default', 'ResqueJobRiver', $args);

                }elseif($product_id == "all" ){
                    $queue = new Queue();
                    $args = array("ConsoleClass" => 'command:flushAllCacheProducts', 'arguments' => array($customer_type_id,$this->appKeyData,$dc_id,$user_id));
                    Log::info('Before Queue 4');
                    log::info($args);
                    $queue->enqueue('default', 'ResqueJobRiver', $args);
                   
                } elseif($dc_id == "all" && $customer_type_id == "all"){
                    $queue = new Queue();
                    $args = array("ConsoleClass" => 'flushAllDcCustomerCache', 'arguments' => array($customer_type_id,$product_id,$dc_id,$user_id));
                    Log::info('Before DC Queue 2');
                    log::info($args);
                    $queue->enqueue('default', 'ResqueJobRiver', $args);
                    
                    
                }elseif($customer_type_id == "all"){
                    log::info('all customerTypes flush');
                    $subject="Alert - Cache Flush has been completed successfully";
                    $userInfo=DB::table('users')->select('email_id','firstname','lastname')->where('user_id',$user_id)->first();
                    $toMail =$userInfo->email_id ;
                    $fname = $userInfo->firstname;
                    $lname = $userInfo->lastname;
                    DB::unprepared("call ProdSlabFlatRefreshByProductIdByCust($product_id,$dc_id,NULL)");
                    $body = array('template'=>'emails.cacheFlushMail', 'attachment'=>'','fname' =>$fname,'name'=>"Tech Support - " . $environment,'lname'=>$lname);
                    Utility::sendEmail($toMail, $subject, $body);
                    log::info('done');
                             
                }elseif($dc_id === "all"){
                    
                    $queue = new Queue();
                    $args = array("ConsoleClass" => 'flushAllDcCache', 'arguments' => array($customer_type_id,$product_id,$dc_id,$user_id));
                    log::info($args);
                    Log::info('Before  DC Queue1');
                    $queue->enqueue('default', 'ResqueJobRiver', $args);

                }
                
            return Response::json(array('status' => 200, 'message' => trans('caching.messages.all_products_flushing')));
            
        }else{
           
            log::info("single dc/pro/cust");
            DB::unprepared("call ProdSlabFlatRefreshByProductIdByCust($product_id,$dc_id,$customer_type_id)");
            
            foreach ($customerTypes as $customerType){

                $keyString = $this->appKeyData.'_product_slab_'.$product_id.'_customer_type_'.$customerType->value.'_le_wh_id_'.$dc_id;
                log::info($keyString);
                $response = null;
                $status = false;
                
                $response = Cache::get($keyString);
                if($response!=null)
                    $status = $this->flushSingleProduct($product_id,$customerType->value,$dc_id,$response);

                if($status)
                    $typeDeleteCount++;             // If the product Type has been flushed.
                else
                    $typeUndeleteCount++;           // If the product Type has not been flushed.
            }
            return Response::json(array('status' => 200, 'message' => trans('caching.messages.selected_productslab_flushed')));
              
        }
      
    }

    public function flushSingleProduct($product_id = null,$customer_type_id = null,$dc_id = null,$response = null) {
        $status = false;

        if(($dc_id == "all") and !empty($response)){

            // The Below Code is to Delete all the Dc`s Cache for a Single Product...
            // Cache::flush($response);
            
            $keyString = $this->appKeyData.'_product_slab_'.$product_id.'_customer_type_'.$customer_type_id.'_le_wh_id_'.$dc_id;

            Cache::forget($keyString);
            $status = true;
            
            return $status;
        }

        if(($dc_id != null) and !empty($response)){ 

            $unSetCount=0;
            $response = json_decode($response,true);
            foreach ($response as $key => $value) {
                if($dc_id == $key)
                {
                    unset($response[$key]);
                    $unSetCount++;
                    // Here, we are removing the data, which is not required tobe flushed.
                }
            }
            if($unSetCount>0 and empty($response))  $status = true;
        }

        if($status == true or !empty($response)){

            $keyString = $this->appKeyData.'_product_slab_'.$product_id.'_customer_type_'.$customer_type_id.'_le_wh_id_'.$dc_id;
            
            Cache::put($keyString, json_encode($response), CACHE_TIME);
            $status = true;
        }

        return $status;
    }

    public function viewProductsSlab($product_id = null,$customer_type_id = null,$dc_id = null)
    {
        $content = null;

        if($customer_type_id == "all")
            $customerTypes = $this->cachingObj->getCustomerType();
        else{
            $customerTypes = $this->cachingObj->getCustomerType($customer_type_id);
        }

        if($product_id == "all")
        {
            $productIds = $this->cachingObj->getProductInfo();
            if(!empty($productIds))
            {
                foreach($productIds as $product)
                {
                    // The Below Loop Runs Only Once, If the Customer type is not "all"
                    foreach($customerTypes as $customerType)
                    {
                        $keyString = $this->appKeyData.'_product_slab_'.$product->product_id.'_customer_type_'.$customerType->value.'_le_wh_id_'.$dc_id;
                        //Log::info($keyString);
                        $response = null;
                        $response = Cache::get($keyString);
                        // Log::info($response);
                        if(!empty($response))
                            $content.= $this->viewSingleProduct($product->product_title,$product->product_id,$customerType->value,$customerType->master_lookup_name,$dc_id,$response);
                    }
                }
            }
        }
        else if($product_id != "all")
        {
            foreach($customerTypes as $customer_type_id){
                $keyString = $this->appKeyData.'_product_slab_'.$product_id.'_customer_type_'.$customer_type_id->value.'_le_wh_id_'.$dc_id;
                //log::info($keyString);
                $response = null;
                $response = Cache::get($keyString);
                //log::info($response);
                $content.= $this->viewSingleProduct(null,$product_id,$customer_type_id->value,$customer_type_id->master_lookup_name,$dc_id,$response);
            }
        }

        if(empty($content))
            $content = trans('caching.messages.empty_data_with_filter');
        else
            $content = "<small><p>".trans('caching.messages.data_with_filter')."</p></small>".$content;


        return ["status" => true,"table" => $content];
    }

    public function viewSingleProduct($product_name = null,$product_id,$customer_type_id,$customer_type_name,$dc_id,$response)
    {
        $response = json_decode($response,true);
        if(($dc_id != null) and ($dc_id != "all") and !empty($response))
        {
            foreach ($response as $key => $value) {
                if($dc_id != $key)
                {
                    unset($response[$key]);
                    // Here, we are removing the data, which is not required to be viewed.
                }
            }
        }

        $data = Input::all();

        // The below Code is to Show Dc Name
        if($dc_id == "all")
            $dc_name = "All";
        else
            $dc_name = isset($data["dc_name"])?$data["dc_name"]:'';

        // The below Code works only when ther is a single product to view...
        if($product_name == null)
            $product_name = isset($data["product_name"])?$data["product_name"]:'';

        // The below Code works only when ther is a single Customer Type to view...
        if($customer_type_name == null)
            $customer_type_name = isset($data["customer_type_name"])?$data["customer_type_name"]:'';

        // The below Code is to Create Table to Display Cache Content
        // Log::info("Last Response: ");
        $table = null;
        if(!empty($response))
        {
            $thead = '
            <div><b>'.trans('caching.grid.product_name').': </b>'.$product_name.'<b>&nbsp;&nbsp;&nbsp;'.trans('caching.grid.customer_type').': </b>'.$customer_type_name.'
            <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover table-scrolling">
             <thead><tr>
                <th>'.trans('caching.grid.dc_name').'</th>
                <th>'.trans('caching.grid.pack_size').'</th>
                <th>'.trans('caching.grid.unit_price').'</th>
                <th>'.trans('caching.grid.margin').'</th>
                <th>'.trans('caching.grid.star').'</th>
                <th></th>
             </tr></thead><tbody>';
                    
            $td = null;
        // Log::info("Type of resp");
            if(is_array($response) || is_object($response))
            foreach ($response as $key => $value) {
                $dcInfo = $this->cachingObj->getDcInfo($key);
                $dcName = isset($dcInfo[0]->dc_name)?$dcInfo[0]->dc_name:trans('caching.caching_form_fields.no_dc');

                foreach ($response[$key] as $subkey => $value) {

                    $cfc = isset($response[$key][$subkey]["cfc"])?$response[$key][$subkey]["cfc"]:null;
                    $esu = isset($response[$key][$subkey]["esu"])?$response[$key][$subkey]["esu"]:null;
                    $ptr = isset($response[$key][$subkey]["ptr"])?$response[$key][$subkey]["ptr"]:null;
                    $star = isset($response[$key][$subkey]["star"])?$response[$key][$subkey]["star"]:null;
                    $stock = isset($response[$key][$subkey]["stock"])?$response[$key][$subkey]["stock"]:null;
                    $margin = isset($response[$key][$subkey]["margin"])?$response[$key][$subkey]["margin"]:null;
                    $is_slab = isset($response[$key][$subkey]["is_slab"])?$response[$key][$subkey]["is_slab"]:null;
                    $state_id = isset($response[$key][$subkey]["state_id"])?$response[$key][$subkey]["state_id"]:null;
                    $is_markup = isset($response[$key][$subkey]["is_markup"])?$response[$key][$subkey]["is_markup"]:null;
                    $pack_size = isset($response[$key][$subkey]["pack_size"])?$response[$key][$subkey]["pack_size"]:null;
                    $pack_level = isset($response[$key][$subkey]["pack_level"])?$response[$key][$subkey]["pack_level"]:null;
                    $unit_price = isset($response[$key][$subkey]["unit_price"])?$response[$key][$subkey]["unit_price"]:null;
                    $product_id = isset($response[$key][$subkey]["product_id"])?$response[$key][$subkey]["product_id"]:null;
                    $freebee_mpq = isset($response[$key][$subkey]["freebee_mpq"])?$response[$key][$subkey]["freebee_mpq"]:null;
                    $freebee_qty = isset($response[$key][$subkey]["freebee_qty"])?$response[$key][$subkey]["freebee_qty"]:null;
                    $blocked_qty = isset($response[$key][$subkey]["blocked_qty"])?$response[$key][$subkey]["blocked_qty"]:null;
                    $prmt_det_id = isset($response[$key][$subkey]["prmt_det_id"])?$response[$key][$subkey]["prmt_det_id"]:null;
                    $start_range = isset($response[$key][$subkey]["start_range"])?$response[$key][$subkey]["start_range"]:null;
                    $dealer_price = isset($response[$key][$subkey]["dealer_price"])?$response[$key][$subkey]["dealer_price"]:null;
                    $freebee_desc = isset($response[$key][$subkey]["freebee_desc"])?$response[$key][$subkey]["freebee_desc"]:null;
                    $customer_type = isset($response[$key][$subkey]["customer_type"])?$response[$key][$subkey]["customer_type"]:null;
                    $freebee_prd_id = isset($response[$key][$subkey]["freebee_prd_id"])?$response[$key][$subkey]["freebee_prd_id"]:null;
                    $product_slab_id = isset($response[$key][$subkey]["product_slab_id"])?$response[$key][$subkey]["product_slab_id"]:null;
                    $product_price_id = isset($response[$key][$subkey]["product_price_id"])?$response[$key][$subkey]["product_price_id"]:null;
                    $cashback_details = isset($response[$key][$subkey]["cashback_details"])?$response[$key][$subkey]["cashback_details"]:null;

                    $td.= '<tr onclick="showAdditionalContent(this)">';
                    $td.= "<td width='30%'>".$dcName."</td>";
                    $td.= "<td width='10%'>".$pack_size."</td>";
                    $td.= "<td width='10%'>".$unit_price."</td>";
                    $td.= "<td width='10%'>".$margin."</td>";
                    $td.= "<td width='10%'>".$star."</td>";
                    $td.= '<td class="hidden" width="30%">
                            <div style="overflow:scroll;  height:150px;">
                            <b>'.trans('caching.grid.esu').'</b>: '.$esu.',
                            <br><b>'.trans('caching.grid.cfc').'</b>: '.$cfc.'.
                            <br><b>'.trans('caching.grid.ptr').'</b>: '.$ptr.',
                            <br><b>'.trans('caching.grid.stock').'</b>: '.$stock.',
                            <br><b>'.trans('caching.grid.is_slab').'</b>: '.$is_slab.',
                            <br><b>'.trans('caching.grid.state_id').'</b>: '.$state_id.',
                            <br><b>'.trans('caching.grid.is_markup').'</b>: '.$is_markup.',
                            <br><b>'.trans('caching.grid.product_id').'</b>: '.$product_id.',
                            <br><b>'.trans('caching.grid.pack_level').'</b>: '.$pack_level.'.
                            <br><b>'.trans('caching.grid.blocked_qty').'</b>: '.$blocked_qty.',
                            <br><b>'.trans('caching.grid.prmt_det_id').'</b>: '.$prmt_det_id.',
                            <br><b>'.trans('caching.grid.freebee_mpq').'</b>: '.$freebee_mpq.',
                            <br><b>'.trans('caching.grid.freebee_qty').'</b>: '.$freebee_qty.',
                            <br><b>'.trans('caching.grid.start_range').'</b>: '.$start_range.',
                            <br><b>'.trans('caching.grid.dealer_price').'</b>: '.$dealer_price.',
                            <br><b>'.trans('caching.grid.freebee_desc').'</b>: '.$freebee_desc.',
                            <br><b>'.trans('caching.grid.customer_type').'</b>: '.$customer_type.',
                            <br><b>'.trans('caching.grid.freebee_prd_id').'</b>: '.$freebee_prd_id.',
                            <br><b>'.trans('caching.grid.product_slab_id').'</b>: '.$product_slab_id.'.
                            <br><b>'.trans('caching.grid.product_price_id').'</b>: '.$product_price_id.',
                            <br><b>'.trans('caching.grid.cashback_details').'</b>: '.$cashback_details.'.
                            </div>
                          </td>';
                    $td.= "</tr>";
                }
            }

            $table = $thead.' '.$td.' </tbody></table></div></div>';

        }
        
        return $table;
    }

    public function viewCacheItemsList($item_id = null, $beat_id = 0, $item_type = null)
    {
        if($item_type == "brands")
            $cacheKeyString = $this->appKeyData . '_getbrands';
        else if($item_type == "category")
            $cacheKeyString = $this->appKeyData . '_getcategories';
        else if($item_type == "manufacturer")
            $cacheKeyString = $this->appKeyData . '_getmanufacturers';
        else if($item_type == "retailer")
            $cacheKeyString = $this->appKeyData . '_getretailers';

        $tbody = $this->viewAllCacheItems($item_id,$beat_id,$cacheKeyString,$item_type);
        if($tbody == null)
            return ["status" => true, "table" => trans('caching.messages.empty_data_with_filter')];
        
        // if($productsInfo == null)
        //     return ["status" => true, "table" => "There is nothing to Show with the selected filters"];

        // The Head must be written once only
        $thead = '
        <div class="row">
            <div class="col-lg-12">
                Search for <input type="radio" name="search_filter" id="products_output_id" value="products" checked="true" name="products">'.(($item_type=="retailer") ? 'Company' : 'Product').'s, 
                <input type="radio" name="search_filter" id="items_output_id" value="items">'.ucfirst($item_type).'. 
            </div>
        </div>
        <div class="row">
        <div class="col-lg-12">
            <input type="text" class="form-control" id="myProductInput" onkeyup="searchProducts()" placeholder="Search for '.(($item_type=="retailer") ? 'company' : 'product').'s.." title="Type in a product name">
            <input type="text" class="form-control" id="myItemInput" onkeyup="searchItems()" placeholder="Search for '.ucfirst($item_type).'.." title="Type in a '.$item_type.' name" style="display:none">
        </div>
        </div>
            <table class="table table-striped table-bordered table-hover" id="myTable">
             <thead><tr>
                <th>S.No</th>
                <th>Beat Name</th>
                <th>'.ucfirst($item_type).' Name</th>
                <th>'.(($item_type=="retailer") ? 'Company' : 'Product').' Name</th>
             </tr></thead><tbody>';
        
        $table = $thead.$tbody."</tbody></table>";

        return ["status" => true,"table" => $table];
    }

    public function viewAllCacheItems($item_id = null, $beat_id = null, $cacheKeyString = null, $item_type= null)
    {
        if(isset($beat_id) and isset($item_id) and isset($cacheKeyString) and isset($item_type))
        {
            $cacheItemsList = Cache::get($cacheKeyString);

            # We are Storing retailer cache data in json objects.(as there is no other way)
            if($item_type == "retailer")
                $cacheItemsList = json_decode(json_encode($cacheItemsList),true);
            else
                $cacheItemsList = json_decode($cacheItemsList,true);
            // dd($cacheItemsList);

            if(!isset($cacheItemsList) and ($cacheItemsList == ''))  return null;
            $productsList = null;
            $beatNames = array();
            $itemNames = array();
            $i=0;
            $sno=1;
            $tbody = null;
            if(($beat_id == "all") and ($item_id == "all"))
            {
                $rno=1;
                if(isset($cacheItemsList))
                foreach ($cacheItemsList as $key => $value)
                {
                    if(isset($cacheItemsList[$key]))
                    {
                        $beatInfo = $this->cachingObj->getBeatInfo($key);
                        if(isset($beatInfo[0]->beat_name))
                            $beatName = $beatInfo[0]->beat_name ." (".$beatInfo[0]->spoke_name.")";
                        else
                            $beatName = trans('caching.caching_form_fields.no_beat');
                        if($item_type == "retailer")
                        {
                            if(isset($cacheItemsList[$key]['data']))
                            foreach ($cacheItemsList[$key]['data'] as $key1)
                            {
                                $company = (isset($key1["company"]))?$key1["company"]:'';
                                $address2 = (isset($key1["address2"]))?$key1["address2"]:'';
                                $address_1 = (isset($key1["address_1"]))?$key1["address_1"]:'';
                                $firstname = (isset($key1["firstname"]))?$key1["firstname"]:'';
                                $business_type_id = (isset($key1["business_type_id"]))?$key1["business_type_id"]:0;
                                $telephone = (isset($key1["telephone"]))?$key1["telephone"]:'';

                                $business_type_name = $this->cachingObj->getSegmentNameById($business_type_id);
                                $business_type_name = isset($business_type_name->master_lookup_name)?$business_type_name->master_lookup_name:'';
                                /*$latitude = (isset($key1["latitude"]))?$key1["latitude"]:'';
                                $longitude = (isset($key1["longitude"]))?$key1["longitude"]:'';
                                $legal_entity_id = (isset($key1["legal_entity_id"]))?$key1["legal_entity_id"]:'';
                                $beat_id = (isset($key1["beat_id"]))?$key1["beat_id"]:'';
                                $beatname = (isset($key1["beatname"]))?$key1["beatname"]:'';
                                $check_in = (isset($key1["check_in"]))?$key1["check_in"]:'';
                                $customer_id = (isset($key1["customer_id"]))?$key1["customer_id"]:'';
                                $customer_token = (isset($key1["customer_token"]))?$key1["customer_token"]:'';
                                $No_of_shutters = (isset($key1["No_of_shutters"]))?$key1["No_of_shutters"]:'';
                                $volume_class = (isset($key1["volume_class"]))?$key1["volume_class"]:'';
                                $master_manf = (isset($key1["master_manf"]))?$key1["master_manf"]:'';
                                $buyer_type = (isset($key1["buyer_type"]))?$key1["buyer_type"]:'';
                                $popup = (isset($key1["popup"]))?$key1["popup"]:'';*/

                                $tbody.=
                                "<tr>
                                    <td>".$rno."</td>
                                    <td>".$beatName."</td>
                                    <td>".$firstname." (".$telephone.")</td>
                                    <td><strong>".$business_type_name."</strong> - ".$company." <small>(".$address_1." ".$address2.")</small></td>
                                </tr>";

                                $rno++;
                            }
                        }
                        else
                        foreach ($cacheItemsList[$key] as $key1 => $value)
                        {
                            $ids = isset($cacheItemsList[$key][$key1][0]["product_id"]) ? $cacheItemsList[$key][$key1][0]["product_id"] : null;
                            if($ids != null)
                            {
                                $ids = array_map('intval', explode(',',$ids));
                                $itemName = $this->getItemName($item_type,$key1);

                                foreach ($ids as $k => $v) {
                                    $productInfo = $this->cachingObj->getProductInfo($v);
                                    $productName = isset($productInfo[0]->product_title)?$productInfo[0]->product_title:null;

                                    $tbody.=
                                    "<tr>
                                        <td>".$sno."</td>
                                        <td>".$beatName."</td>
                                        <td>".$itemName."</td>
                                        <td>".$productName."</td>
                                    </tr>";

                                    $sno++;
                                }
                            }
                        }
                    }
                }
            }
            else if(($beat_id == "all") and ($item_id != "all"))
            {
                // To view all the beats only for single item

                // To know the name of the item
                $itemName = $this->getItemName($item_type,$item_id);
                if($item_type == "retailer")
                {
                    if(isset($cacheItemsList))
                    foreach ($cacheItemsList as $beat_id => $value)
                    {
                        $rno=1;
                        // dd($cacheItemsList[$beat_id]['data']);
                        if(isset($cacheItemsList[$beat_id]['data']))
                        foreach ($cacheItemsList[$beat_id]['data'] as $key1)
                        {
                            $customer_id = (isset($key1["customer_id"]))?$key1["customer_id"]:'';
                            if($customer_id == $item_id)
                            {
                                $company = (isset($key1["company"]))?$key1["company"]:'';
                                $address2 = (isset($key1["address2"]))?$key1["address2"]:'';
                                $address_1 = (isset($key1["address_1"]))?$key1["address_1"]:'';
                                $firstname = (isset($key1["firstname"]))?$key1["firstname"]:'';
                                $business_type_id = (isset($key1["business_type_id"]))?$key1["business_type_id"]:0;
                                $telephone = (isset($key1["telephone"]))?$key1["telephone"]:'';
                                $beatname = (isset($key1["beatname"]))?$key1["beatname"]:'';

                                $business_type_name = $this->cachingObj->getSegmentNameById($business_type_id);
                                $business_type_name = isset($business_type_name->master_lookup_name)?$business_type_name->master_lookup_name:'';

                                $tbody.=
                                "<tr>
                                    <td>".$rno."</td>
                                    <td>".$beatname."</td>
                                    <td>".$firstname." (".$telephone.")</td>
                                    <td><strong>".$business_type_name."</strong> - ".$company." <small>(".$address_1." ".$address2.")</small></td>
                                </tr>";
                                $rno++;
                                #The reason to break is, ther is no need to search more.
                                break;
                            }
                        }
                    }
                }
                else
                foreach ($cacheItemsList as $key => $value) 
                {
                    $ids = isset($cacheItemsList[$key][$item_id][0]["product_id"])?$cacheItemsList[$key][$item_id][0]["product_id"]:null;
                    if($ids != null)
                    {
                        $ids = array_map('intval', explode(',',$ids));

                        // To know the name of the beat
                        $beatInfo = $this->cachingObj->getBeatInfo($key);
                        if(isset($beatInfo[0]->beat_name))
                            $beatName = $beatInfo[0]->beat_name ." (".$beatInfo[0]->spoke_name.")";
                        else
                            $beatName = "Un Mapped Beat";
                        foreach ($ids as $k => $v) {
                            $productInfo = $this->cachingObj->getProductInfo($v);
                            $productName = isset($productInfo[0]->product_title)?$productInfo[0]->product_title:null;

                            $tbody.="<tr><td>".$sno."</td><td>".$beatName."</td><td>".$itemName."</td><td>".$productName."</td></tr>";
                            $sno++;
                        }
                    }
                }
            }
            else if(($beat_id != "all") and ($item_id == "all"))
            {
                // To view all the Items in Single beat only
                $beatInfo = $this->cachingObj->getBeatInfo($beat_id);
                if(isset($beatInfo[0]->beat_name))
                    $beatName = $beatInfo[0]->beat_name ." (".$beatInfo[0]->spoke_name.")";
                else
                    $beatName = trans('caching.caching_form_fields.no_beat');

                if(isset($cacheItemsList[$beat_id]))
                {
                    if($item_type == "retailer")
                    {
                        if(isset($cacheItemsList[$beat_id]['data']))
                        {
                            $rno=1;
                            foreach ($cacheItemsList[$beat_id]['data'] as $key1)
                            {
                                $customer_id = (isset($key1["customer_id"]))?$key1["customer_id"]:'';
                                $company = (isset($key1["company"]))?$key1["company"]:'';
                                $address2 = (isset($key1["address2"]))?$key1["address2"]:'';
                                $address_1 = (isset($key1["address_1"]))?$key1["address_1"]:'';
                                $firstname = (isset($key1["firstname"]))?$key1["firstname"]:'';
                                $business_type_id = (isset($key1["business_type_id"]))?$key1["business_type_id"]:0;
                                $telephone = (isset($key1["telephone"]))?$key1["telephone"]:'';
                                $beatname = (isset($key1["beatname"]))?$key1["beatname"]:'';

                                $business_type_name = $this->cachingObj->getSegmentNameById($business_type_id);
                                $business_type_name = isset($business_type_name->master_lookup_name)?$business_type_name->master_lookup_name:'';

                                $tbody.=
                                "<tr>
                                    <td>".$rno."</td>
                                    <td>".$beatname."</td>
                                    <td>".$firstname." (".$telephone.")</td>
                                    <td><strong>".$business_type_name."</strong> - ".$company." <small>(".$address_1." ".$address2.")</small></td>
                                </tr>";
                                $rno++;
                            }
                        }
                    }
                    else
                    {
                        # Below thing for Manufacturers, Categories and Brands
                        foreach ($cacheItemsList[$beat_id] as $key => $value) {
                            $ids = isset($cacheItemsList[$beat_id][$key][0]["product_id"])?$cacheItemsList[$beat_id][$key][0]["product_id"]:null;
                            if($ids != null)
                            {
                                // To know the name of the Item Id
                                $itemName = $this->getItemName($item_type,$key);

                                $ids = array_map('intval', explode(',',$ids));
                                foreach ($ids as $k => $v) {
                                    $productInfo = $this->cachingObj->getProductInfo($v);
                                    $productName = isset($productInfo[0]->product_title)?$productInfo[0]->product_title:null;

                                    $tbody.="<tr><td>".$sno."</td><td>".$beatName."</td><td>".$itemName."</td><td>".$productName."</td></tr>";
                                    $sno++;
                                }

                            }
                        }
                    }
                }
            }
            else if(($beat_id != "all") and ($item_id != "all"))
            {
                // To view single, product on single beat
                $itemName = $this->getItemName($item_type,$item_id);
                $beatInfo = $this->cachingObj->getBeatInfo($beat_id);
                if(isset($beatInfo[0]->beat_name))
                    $beatName = $beatInfo[0]->beat_name ." (".$beatInfo[0]->spoke_name.")";
                else
                    $beatName = trans('caching.caching_form_fields.no_beat');
                // dd($cacheItemsList);
                if($item_type == "retailer")
                {
                    if(isset($cacheItemsList[$beat_id]['data']))
                    {
                        $rno=1;
                        foreach ($cacheItemsList[$beat_id]['data'] as $key1)
                        {
                            $customer_id = (isset($key1["customer_id"]))?$key1["customer_id"]:'';
                            if($customer_id == $item_id)
                            {
                                $company = (isset($key1["company"]))?$key1["company"]:'';
                                $address2 = (isset($key1["address2"]))?$key1["address2"]:'';
                                $address_1 = (isset($key1["address_1"]))?$key1["address_1"]:'';
                                $firstname = (isset($key1["firstname"]))?$key1["firstname"]:'';
                                $business_type_id = (isset($key1["business_type_id"]))?$key1["business_type_id"]:0;
                                $telephone = (isset($key1["telephone"]))?$key1["telephone"]:'';
                                $beatname = (isset($key1["beatname"]))?$key1["beatname"]:'';

                                $business_type_name = $this->cachingObj->getSegmentNameById($business_type_id);
                                $business_type_name = isset($business_type_name->master_lookup_name)?$business_type_name->master_lookup_name:'';

                                $tbody.=
                                "<tr>
                                    <td>".$rno."</td>
                                    <td>".$beatname."</td>
                                    <td>".$firstname." (".$telephone.")</td>
                                    <td><strong>".$business_type_name."</strong> - ".$company." <small>(".$address_1." ".$address2.")</small></td>
                                </tr>";
                                #The reason to break is, ther is no need to search more.
                                break;
                            }
                        }
                    }
                }

                $ids = isset($cacheItemsList[$beat_id][$item_id][0]["product_id"]) ? $cacheItemsList[$beat_id][$item_id][0]["product_id"] : null;

                if($ids != null)
                {
                    $ids = array_map('intval', explode(',',$ids));
                    foreach ($ids as $k => $v) {
                        $productInfo = $this->cachingObj->getProductInfo($v);
                        $productName = isset($productInfo[0]->product_title)?$productInfo[0]->product_title:null;

                        $tbody.="<tr><td>".$sno."</td><td>".$beatName."</td><td>".$itemName."</td><td>".$productName."</td></tr>";
                        $sno++;
                    }
                }}
            // $productsList = rtrim($productsList,',');
            // $productsInfo = $this->cachingObj->getProductInfo($productsList);
            // $tbody = null;
            // $sno = 1;

            // if(isset($productsInfo))
            //     foreach($productsInfo as $product) {
            //         $tbody.="<tr><td>".$sno."</td><td>".$itemNames[$sno-1]."</td><td>".$product->product_title."</td></tr>";
            //         $sno++;
            //     }
            return $tbody;
        }
        else
            return null;

    }

    // The main reason of this method is to flush the Cache of the Dashboard...
    public function flushCacheDashboard($dashboard_id = null, $day_id = null, $ff_user_id = null)
    {
        try
        {
        
        # Completely Removed the Old Code and 
        # Stored Cache in Tags
        # Flushing the Cache Based on Tags
        
        Cache::tags("dncDashboard_"+Session::get('legal_entity_id'))->flush();
        Cache::tags("cncDashboard")->flush();

        return ["message" => trans('caching.messages.dashboard_success')];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return ["message" => trans('caching.messages.dashboard_failed')];
        }
    }

    # load Records on users Request for UI
    public function getAjaxData($search_type='')
    {
        $data = '';
        $input = Input::all();
        $search_term = (isset($input['q']))?$input['q']:-1;
        if(($search_type == "retailer") and (($search_term != '') or ($search_term != -1)))
        {
            $response = $this->cachingObj->getRetailerInfo($search_term);
            foreach ($response as $user) {
                $user->retailer_name = $user->retailer_name .' ('. $user->retailer_number .')';
            }
            $data = json_encode($response);
            $data = substr_replace($data, '{"retailer_id":"all","retailer_name":"All"},', 1, 0);
        }

        // var_dump(gettype(var));
        return $data;
    }

    // This function is used to get name according to the particular id
    public function getItemName($item_type,$item_id){

        if($item_type == "brands")
            $itemInfo = $this->cachingObj->getBrandInfo($item_id);
        else if($item_type == "category")
            $itemInfo = $this->cachingObj->getCategoryInfo($item_id);
        else if($item_type == "manufacturer")
            $itemInfo = $this->cachingObj->getManufacturerInfo($item_id);
        else if($item_type == "retailer")
            $itemInfo = $this->cachingObj->getRetailerInfo($item_id);
        else if($item_type == "beat")
            $itemInfo = $this->cachingObj->getBeatInfo($item_id);
        else
            return null;

        return isset($itemInfo[0]->item_name) ? $itemInfo[0]->item_name : null;
    }

    public function flushCacheItemsList($dc_id = null,$customer_id = null,$item_type = null,$segment_id=null)
    { 
       
        if($item_type == "brands")
            $cacheKeyString = '_getbrands_';
        else if($item_type == "category")
            $cacheKeyString =  '_getcategories_';
        else if($item_type == "manufacturer")
            $cacheKeyString = '_getmanufacturers_';
        $redis = Cache::getRedis();

        if($segment_id && $segment_id!='no'){
            //log::info('if');
            if($segment_id == 'all' && $dc_id=="all"){
                $keyString="categories_".'*'."_le_wh_id_".'*';
                $keys = $redis->keys("laravel:*".$keyString."*");
                //log::info($keys);
                foreach ($keys as $key) {
                    $redis->del($key);
                }
            }else if($segment_id == 'all' && $dc_id!="all"){
                $keyString="categories_".'*'."_le_wh_id_".$dc_id;
                $keys = $redis->keys("laravel:*".$keyString."*");
                //log::info($keys);
                foreach ($keys as $key) {
                    $redis->del($key);
                }
            }else if($segment_id != 'all' && $dc_id=="all"){
                $keyString="categories_".$segment_id."_le_wh_id_".'*';
                $keys = $redis->keys("laravel:*".$keyString."*");
                //log::info($keys);
                foreach ($keys as $key) {
                    $redis->del($key);
                }
            }else if($segment_id != 'all' && $dc_id!="all"){
                $keyString="categories_".$segment_id."_le_wh_id_".$dc_id;
                $keys = $redis->keys("laravel:*".$keyString."*");
                //log::info($keys);
                foreach ($keys as $key) {
                    $redis->del($key);
                }
            }
        }

       if($dc_id == 'all' && $customer_id !='all'){
            $keyString =  env('DB_DATABASE').'_'.$customer_id.'_le_wh_id_'.'*'.$cacheKeyString;
            $keys = $redis->keys("laravel:*".$keyString."*");
            //log::info($keys);
            foreach ($keys as $key) {
                $redis->del($key);
            }
       }else if($dc_id != 'all' && $customer_id =='all'){
            $keyString =  env('DB_DATABASE').'_'.'*'.'_le_wh_id_'.$dc_id.$cacheKeyString;
            $keys = $redis->keys("laravel:*".$keyString."*");
            //log::info($keys);
            foreach ($keys as $key) {
                $redis->del($key);
            }
       }else if($dc_id == 'all' && $customer_id =='all'){
            $keyString =  env('DB_DATABASE').'_'.'*'.'_le_wh_id_'.'*'.$cacheKeyString;
            $keys = $redis->keys("laravel:*".$keyString."*");
            //log::info($keys);
            foreach ($keys as $key) {
                $redis->del($key);
            }
       }else{
            $keyString =  env('DB_DATABASE').'_'.$customer_id.'_le_wh_id_'.$dc_id.$cacheKeyString;
            $keys = $redis->keys("laravel:*".$keyString."*");
            //log::info($keys);
            foreach ($keys as $key) {
                $redis->del($key);
            }
       }
          

        return ["status" => true, "message" => "flushed successfully"];

    }

    /**
    * Method to Flush Laravel Cache Based on Pattern
    * @param $patter [patern string]
    */
    public function flushDynamicCacheData($pattern)
    {
        $count = 0;

        // Pattern Validation
        if($pattern == "")
            return ["message"=>"Pattern is required to Flush. Please Select a Pattern"];

        $redis = Cache::getRedis();
        if($pattern == "all"){
            $dynamicKeysList = $this->cachingObj->getDynamicKeysList();
            if(isset($dynamicKeysList) and !empty($dynamicKeysList))
                foreach ($dynamicKeysList as $key) {
                    
                    $keys = $redis->keys("laravel:*".$key->pattern."*");
                    foreach ($keys as $key) {
                        $redis->del($key);
                        $count++;
                    }

                }
        }else{

            $keys = $redis->keys("laravel:*".$pattern."*");
            foreach ($keys as $key) {
                $redis->del($key);
                $count++;
            }

        }
        
        if(!$count)
            return ["message"=>"There is no key to Flush based on the pattern \"".$pattern."\""];
        else
            return ["message"=>$count." key(s) had been Flushed!"];
    }
}
